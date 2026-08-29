<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WPSoft Site Tools Updater
 *
 * Stage 1 client-side updater. It integrates WPSoft Site Tools with the native
 * WordPress plugin updater and is ready for the WPSoft License Server update
 * endpoint:
 *
 * POST {license_server}/wp-json/wpsoft-license/v1/update
 *
 * Expected JSON shape (server stage):
 * {
 *   "success": true,
 *   "version": "3.2.90",
 *   "name": "WPSoft Site Tools",
 *   "package": "https://signed-download-url...",
 *   "homepage": "https://...",
 *   "requires": "6.2",
 *   "tested": "6.8",
 *   "requires_php": "7.4",
 *   "last_updated": "2026-08-17 20:00:00",
 *   "changelog": "<h4>3.2.90</h4><ul>...</ul>"
 * }
 */
final class WPST_Updater {

    const PRODUCT       = 'wpsoft-site-tools';
    const CACHE_KEY     = 'wpst_update_remote_info';
    const CACHE_TTL     = 21600; // 6 hours.
    const UPDATE_ROUTE  = 'wp-json/wpsoft-license/v1/update';
    const PAGE_SLUG     = 'wpsoft-updates';

    public static function init() {
        add_filter( 'pre_set_site_transient_update_plugins', array( __CLASS__, 'inject_update' ), 30 );
        add_filter( 'plugins_api', array( __CLASS__, 'plugins_api' ), 30, 3 );
        add_filter( 'plugin_row_meta', array( __CLASS__, 'row_meta' ), 30, 2 );

        add_action( 'admin_menu', array( __CLASS__, 'menu' ), 35 );
        add_action( 'admin_post_wpst_check_updates', array( __CLASS__, 'manual_check' ) );
        add_action( 'admin_notices', array( __CLASS__, 'license_update_notice' ) );

        // Clear the old remote response after this plugin itself is upgraded.
        add_action( 'upgrader_process_complete', array( __CLASS__, 'after_upgrade' ), 10, 2 );
    }

    public static function plugin_file() {
        return plugin_basename( WPST_FILE );
    }

    private static function license_state() {
        $state = get_option( 'wpst_license_state', array() );
        return is_array( $state ) ? $state : array();
    }

    private static function license_active() {
        if ( class_exists( 'WPST_License' ) ) {
            return WPST_License::is_active();
        }
        $state = self::license_state();
        return in_array( sanitize_key( $state['status'] ?? '' ), array( 'active', 'grace' ), true );
    }

    private static function license_server() {
        $state = self::license_state();

        if ( defined( 'WPST_LICENSE_SERVER_URL' ) && WPST_LICENSE_SERVER_URL ) {
            $server = (string) WPST_LICENSE_SERVER_URL;
        } elseif ( ! empty( $state['server_url'] ) ) {
            $server = (string) $state['server_url'];
        } else {
            $server = (string) apply_filters( 'wpst_license_server_url', '' );
        }

        $server = untrailingslashit( esc_url_raw( trim( $server ) ) );
        $server = preg_replace( '#/wp-json/wpsoft-license/v1(?:/(?:activate|check|deactivate|update))?$#i', '', $server );
        return untrailingslashit( $server );
    }

    private static function license_key() {
        $state = self::license_state();
        return strtoupper( trim( sanitize_text_field( $state['license_key'] ?? '' ) ) );
    }

    private static function domain() {
        $host = wp_parse_url( home_url( '/' ), PHP_URL_HOST );
        if ( ! $host ) return '';
        return preg_replace( '/^www\./', '', strtolower( $host ) );
    }

    private static function valid_server( $server ) {
        if ( ! $server ) return false;
        $parts = wp_parse_url( $server );
        if ( empty( $parts['scheme'] ) || empty( $parts['host'] ) ) return false;
        if ( 'https' === strtolower( $parts['scheme'] ) ) return true;
        return in_array( strtolower( $parts['host'] ), array( 'localhost', '127.0.0.1', '::1' ), true );
    }

    private static function endpoint() {
        $server = self::license_server();
        if ( ! self::valid_server( $server ) ) return '';
        return trailingslashit( $server ) . self::UPDATE_ROUTE;
    }

    public static function clear_cache() {
        delete_site_transient( self::CACHE_KEY );
    }

    /**
     * Fetch and normalize the update response.
     * When the server is not ready yet the current plugin continues normally.
     */
    public static function remote_info( $force = false ) {
        if ( ! $force ) {
            $cached = get_site_transient( self::CACHE_KEY );
            if ( is_array( $cached ) ) return $cached;
        }

        $empty = array(
            'success'       => false,
            'version'       => '',
            'name'          => 'WPSoft Site Tools',
            'package'       => '',
            'homepage'      => '',
            'requires'      => '',
            'tested'        => '',
            'requires_php'  => '',
            'last_updated'  => '',
            'changelog'     => '',
            'message'       => '',
            'error_code'    => '',
            'checked_at'    => time(),
        );

        if ( ! self::license_active() ) {
            $empty['message'] = 'Güncellemeler için aktif WPSoft lisansı gerekiyor.';
            $empty['error_code'] = 'license_inactive';
            set_site_transient( self::CACHE_KEY, $empty, HOUR_IN_SECONDS );
            return $empty;
        }

        $endpoint = self::endpoint();
        $key      = self::license_key();

        if ( ! $endpoint || ! $key ) {
            $empty['message'] = 'WPSoft güncelleme sunucusu veya lisans anahtarı yapılandırılmamış.';
            $empty['error_code'] = 'not_configured';
            set_site_transient( self::CACHE_KEY, $empty, HOUR_IN_SECONDS );
            return $empty;
        }

        $response = wp_remote_post( $endpoint, array(
            'timeout'     => 15,
            'redirection' => 2,
            'sslverify'   => true,
            'headers'     => array(
                'Accept'     => 'application/json',
                'User-Agent' => 'WPSoft-Site-Tools-Updater/' . WPST_VERSION . '; ' . home_url( '/' ),
            ),
            'body'        => array(
                'license_key'      => $key,
                'domain'           => self::domain(),
                'product'          => self::PRODUCT,
                'version'          => WPST_VERSION,
                'wordpress'        => get_bloginfo( 'version' ),
                'php'              => PHP_VERSION,
                'locale'           => determine_locale(),
                'site_url'         => home_url( '/' ),
                'update_channel'   => 'stable',
            ),
        ) );

        if ( is_wp_error( $response ) ) {
            $empty['message'] = $response->get_error_message();
            $empty['error_code'] = 'network';
            set_site_transient( self::CACHE_KEY, $empty, 30 * MINUTE_IN_SECONDS );
            return $empty;
        }

        $code = (int) wp_remote_retrieve_response_code( $response );
        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body, true );

        if ( ! is_array( $data ) ) {
            $empty['message'] = 'Güncelleme sunucusu geçerli JSON yanıtı döndürmedi.';
            $empty['error_code'] = 'invalid_response';
            set_site_transient( self::CACHE_KEY, $empty, 30 * MINUTE_IN_SECONDS );
            return $empty;
        }

        $info = array(
            'success'       => ! empty( $data['success'] ),
            'version'       => sanitize_text_field( $data['version'] ?? $data['new_version'] ?? '' ),
            'name'          => sanitize_text_field( $data['name'] ?? 'WPSoft Site Tools' ),
            'package'       => esc_url_raw( $data['package'] ?? $data['download_url'] ?? '' ),
            'homepage'      => esc_url_raw( $data['homepage'] ?? '' ),
            'requires'      => sanitize_text_field( $data['requires'] ?? '' ),
            'tested'        => sanitize_text_field( $data['tested'] ?? '' ),
            'requires_php'  => sanitize_text_field( $data['requires_php'] ?? '' ),
            'last_updated'  => sanitize_text_field( $data['last_updated'] ?? '' ),
            'changelog'     => wp_kses_post( $data['changelog'] ?? '' ),
            'message'       => sanitize_text_field( $data['message'] ?? '' ),
            'error_code'    => sanitize_key( $data['error_code'] ?? '' ),
            'checked_at'    => time(),
            'http_code'     => $code,
        );

        // A server may expose version/changelog but withhold package for invalid licenses.
        if ( ! $info['success'] && ! $info['message'] ) {
            $info['message'] = 'Güncelleme sunucusu isteği reddetti.';
        }

        set_site_transient( self::CACHE_KEY, $info, self::CACHE_TTL );
        return $info;
    }

    public static function update_available( $info = null ) {
        if ( null === $info ) $info = self::remote_info();
        if ( empty( $info['version'] ) ) return false;
        return version_compare( $info['version'], WPST_VERSION, '>' );
    }

    private static function update_object( $info ) {
        $obj = new stdClass();
        $obj->id            = 'wpsoft-site-tools';
        $obj->slug          = 'wpsoft-site-tools';
        $obj->plugin        = self::plugin_file();
        $obj->new_version   = $info['version'];
        $obj->url           = $info['homepage'];
        $obj->package       = $info['package'];
        $obj->tested        = $info['tested'];
        $obj->requires      = $info['requires'];
        $obj->requires_php  = $info['requires_php'];
        $obj->icons         = array();
        $obj->banners       = array();
        $obj->banners_rtl   = array();
        return $obj;
    }

    public static function inject_update( $transient ) {
        if ( ! is_object( $transient ) ) $transient = new stdClass();
        if ( empty( $transient->response ) || ! is_array( $transient->response ) ) {
            $transient->response = array();
        }
        if ( empty( $transient->no_update ) || ! is_array( $transient->no_update ) ) {
            $transient->no_update = array();
        }

        $info = self::remote_info();
        $file = self::plugin_file();

        if ( self::update_available( $info ) ) {
            $obj = self::update_object( $info );

            // Never offer an installable package unless license server issued a package URL.
            // This prevents WordPress from attempting an invalid download.
            if ( self::license_active() && ! empty( $info['package'] ) ) {
                $transient->response[ $file ] = $obj;
                unset( $transient->no_update[ $file ] );
            } else {
                $obj->package = '';
                $transient->no_update[ $file ] = $obj;
            }
        } else {
            $obj = self::update_object( array_merge( $info, array(
                'version' => WPST_VERSION,
                'package' => '',
            ) ) );
            $transient->no_update[ $file ] = $obj;
            unset( $transient->response[ $file ] );
        }

        return $transient;
    }

    public static function plugins_api( $result, $action, $args ) {
        if ( 'plugin_information' !== $action || empty( $args->slug ) || 'wpsoft-site-tools' !== $args->slug ) {
            return $result;
        }

        $info = self::remote_info();

        $obj = new stdClass();
        $obj->name          = $info['name'] ?: 'WPSoft Site Tools';
        $obj->slug          = 'wpsoft-site-tools';
        $obj->version       = $info['version'] ?: WPST_VERSION;
        $obj->author        = '<a href="#">WPSoft</a>';
        $obj->homepage      = $info['homepage'];
        $obj->requires      = $info['requires'];
        $obj->tested        = $info['tested'];
        $obj->requires_php  = $info['requires_php'];
        $obj->last_updated  = $info['last_updated'];
        $obj->download_link = self::license_active() ? $info['package'] : '';
        $obj->sections      = array(
            'description' => '<p>WPSoft Site Tools için özel güncelleme paketi.</p>',
            'changelog'   => $info['changelog'] ?: '<p>Changelog bilgisi güncelleme sunucusundan alınacaktır.</p>',
        );
        return $obj;
    }

    public static function row_meta( $links, $file ) {
        if ( self::plugin_file() !== $file ) return $links;

        $links[] = '<a href="' . esc_url( admin_url( 'admin.php?page=' . self::PAGE_SLUG ) ) . '">Güncellemeler</a>';
        return $links;
    }

    public static function menu() {
        add_submenu_page(
            'wpsoft-site-tools',
            'WPSoft Güncellemeler',
            'Güncellemeler',
            'manage_options',
            self::PAGE_SLUG,
            array( __CLASS__, 'page' )
        );
    }

    public static function manual_check() {
        if ( ! current_user_can( 'update_plugins' ) ) {
            wp_die( 'Bu işlem için yetkiniz yok.' );
        }
        check_admin_referer( 'wpst_check_updates' );

        self::clear_cache();
        delete_site_transient( 'update_plugins' );
        wp_update_plugins();
        $info = self::remote_info( true );

        $args = array( 'wpst_checked' => 1 );
        if ( ! empty( $info['error_code'] ) ) {
            $args['wpst_update_error'] = rawurlencode( $info['error_code'] );
        }
        wp_safe_redirect( add_query_arg( $args, admin_url( 'admin.php?page=' . self::PAGE_SLUG ) ) );
        exit;
    }

    public static function after_upgrade( $upgrader, $options ) {
        if ( empty( $options['type'] ) || 'plugin' !== $options['type'] ) return;
        $plugins = isset( $options['plugins'] ) ? (array) $options['plugins'] : array();
        if ( in_array( self::plugin_file(), $plugins, true ) || ( ! empty( $options['plugin'] ) && self::plugin_file() === $options['plugin'] ) ) {
            self::clear_cache();
            delete_site_transient( 'update_plugins' );
        }
    }

    public static function license_update_notice() {
        if ( ! current_user_can( 'update_plugins' ) || self::license_active() ) return;
        if ( empty( $_GET['page'] ) || self::PAGE_SLUG !== sanitize_key( wp_unslash( $_GET['page'] ) ) ) return;

        echo '<div class="notice notice-warning"><p><strong>WPSoft Güncellemeleri:</strong> Paket indirmek için aktif lisans gerekiyor.</p></div>';
    }

    private static function status_label( $info ) {
        if ( ! self::license_active() ) return array( 'Lisans gerekli', 'warning' );
        if ( ! empty( $info['error_code'] ) ) return array( 'Sunucu bekleniyor', 'warning' );
        if ( self::update_available( $info ) ) return array( 'Yeni sürüm mevcut', 'update' );
        return array( 'Güncel', 'ok' );
    }

    public static function page() {
        if ( ! current_user_can( 'update_plugins' ) ) return;

        $info   = self::remote_info();
        $status = self::status_label( $info );
        $server = self::license_server();
        $last   = ! empty( $info['checked_at'] ) ? wp_date( 'd.m.Y H:i', (int) $info['checked_at'] ) : '—';
        $newer  = self::update_available( $info );

        ?>
        <div class="wrap wpst-updates-page">
            <style>
                .wpst-updates-page{max-width:1050px}
                .wpst-update-hero{margin-top:18px;padding:28px;border:1px solid #e2e8f0;border-radius:18px;background:#fff;box-shadow:0 8px 24px rgba(15,23,42,.05)}
                .wpst-update-head{display:flex;justify-content:space-between;gap:20px;align-items:flex-start;flex-wrap:wrap}
                .wpst-update-kicker{margin:0 0 6px;color:#64748b;font-weight:700;font-size:11px;letter-spacing:.08em;text-transform:uppercase}
                .wpst-update-title{margin:0;font-size:28px;line-height:1.15}
                .wpst-update-sub{margin:8px 0 0;color:#64748b}
                .wpst-update-status{display:inline-flex;align-items:center;gap:8px;padding:8px 12px;border-radius:999px;background:#ecfdf5;color:#047857;font-weight:700}
                .wpst-update-status.warning{background:#fffbeb;color:#b45309}.wpst-update-status.update{background:#eff6ff;color:#1d4ed8}
                .wpst-update-dot{width:8px;height:8px;border-radius:999px;background:currentColor}
                .wpst-update-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px;margin-top:24px}
                .wpst-update-card{padding:16px;border:1px solid #e2e8f0;border-radius:14px;background:#f8fafc}
                .wpst-update-card small{display:block;color:#64748b;margin-bottom:6px}.wpst-update-card strong{font-size:15px;word-break:break-word}
                .wpst-update-actions{display:flex;gap:10px;align-items:center;flex-wrap:wrap;margin-top:22px}
                .wpst-update-changelog{margin-top:18px;padding:22px;border:1px solid #e2e8f0;border-radius:16px;background:#fff}
                .wpst-update-changelog h2{margin-top:0}
                .wpst-update-message{margin-top:16px;padding:12px 14px;border-radius:12px;background:#f8fafc;color:#475569}
                @media(max-width:782px){.wpst-update-grid{grid-template-columns:1fr 1fr}}
                @media(max-width:520px){.wpst-update-grid{grid-template-columns:1fr}}
            </style>

            <h1>WPSoft Güncellemeler</h1>

            <div class="wpst-update-hero">
                <div class="wpst-update-head">
                    <div>
                        <p class="wpst-update-kicker">WPSoft Site Tools</p>
                        <h2 class="wpst-update-title">Eklenti Güncelleme Merkezi</h2>
                        <p class="wpst-update-sub">WordPress’in doğal eklenti güncelleme altyapısıyla çalışır.</p>
                    </div>
                    <span class="wpst-update-status <?php echo esc_attr( $status[1] ); ?>"><i class="wpst-update-dot"></i><?php echo esc_html( $status[0] ); ?></span>
                </div>

                <div class="wpst-update-grid">
                    <div class="wpst-update-card"><small>Mevcut sürüm</small><strong><?php echo esc_html( WPST_VERSION ); ?></strong></div>
                    <div class="wpst-update-card"><small>Sunucudaki sürüm</small><strong><?php echo esc_html( $info['version'] ?: '—' ); ?></strong></div>
                    <div class="wpst-update-card"><small>Son kontrol</small><strong><?php echo esc_html( $last ); ?></strong></div>
                    <div class="wpst-update-card"><small>Lisans</small><strong><?php echo self::license_active() ? 'Aktif' : 'Pasif'; ?></strong></div>
                </div>

                <?php if ( ! empty( $info['message'] ) ) : ?>
                    <div class="wpst-update-message"><?php echo esc_html( $info['message'] ); ?></div>
                <?php endif; ?>

                <div class="wpst-update-actions">
                    <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                        <input type="hidden" name="action" value="wpst_check_updates">
                        <?php wp_nonce_field( 'wpst_check_updates' ); ?>
                        <button class="button button-primary" type="submit">Güncellemeleri Kontrol Et</button>
                    </form>

                    <?php if ( $newer && self::license_active() && ! empty( $info['package'] ) ) : ?>
                        <a class="button" href="<?php echo esc_url( admin_url( 'plugins.php' ) ); ?>">Eklentiler Ekranında Güncelle</a>
                    <?php endif; ?>

                    <a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=wpsoft-activation' ) ); ?>">Aktivasyon</a>
                </div>
            </div>

            <div class="wpst-update-changelog">
                <h2>Sürüm Notları</h2>
                <?php
                if ( ! empty( $info['changelog'] ) ) {
                    echo wp_kses_post( $info['changelog'] );
                } else {
                    echo '<p>Update Server bağlandığında yeni sürüm notları burada görüntülenecek.</p>';
                }
                ?>
            </div>

            <div class="wpst-update-changelog">
                <h2>Bağlantı Durumu</h2>
                <p><strong>Update endpoint:</strong> <?php echo $server ? '<code>' . esc_html( trailingslashit( $server ) . self::UPDATE_ROUTE ) . '</code>' : 'Henüz yapılandırılmadı.'; ?></p>
                <p>Bir sonraki aşamada lisans sunucusuna <code>/update</code> endpoint’i ve güvenli ZIP dağıtımı eklenecek.</p>
            </div>
        </div>
        <?php
    }
}
