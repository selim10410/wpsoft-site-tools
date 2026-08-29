<?php
if(!defined('ABSPATH'))exit;

final class WPST_License {
    const OPTION='wpst_license_state';
    const CRON_HOOK='wpst_license_cron_check';
    const PRODUCT='wpsoft-site-tools';
    const GRACE_DAYS=7;
    const CHECK_INTERVAL=300; // 5 dakika

    public static function init(){
        add_action('admin_menu',array(__CLASS__,'menu'),30);
        add_action('admin_post_wpst_activate_license',array(__CLASS__,'activate'));
        add_action('admin_post_wpst_deactivate_license',array(__CLASS__,'deactivate'));
        add_action('admin_post_wpst_check_license',array(__CLASS__,'manual_check'));
        add_action('admin_notices',array(__CLASS__,'notice'));
        add_action('admin_enqueue_scripts',array(__CLASS__,'admin_assets'));
        add_action('admin_init',array(__CLASS__,'guard_admin_actions'),1);
        add_action('admin_init',array(__CLASS__,'maybe_background_check'),40);
        add_action(self::CRON_HOOK,array(__CLASS__,'cron_check'));
        add_action('init',array(__CLASS__,'ensure_cron'),30);

        foreach(self::protected_admin_post_actions() as $action){
            add_action('admin_post_'.$action,array(__CLASS__,'block_write'),0);
        }
        foreach(self::protected_ajax_actions() as $action){
            add_action('wp_ajax_'.$action,array(__CLASS__,'block_ajax_write'),0);
        }

        add_action('wp_ajax_elementor_save_builder',array(__CLASS__,'guard_elementor_save'),0);
    }

    public static function ensure_cron(){
        if(!wp_next_scheduled(self::CRON_HOOK)){
            wp_schedule_event(time()+300,'twicedaily',self::CRON_HOOK);
        }
    }

    private static function state(){
        $state=get_option(self::OPTION,array());
        return is_array($state)?$state:array();
    }

    private static function save_state($state){
        update_option(self::OPTION,$state,false);
    }

    private static function now(){
        return current_time('timestamp',true);
    }

    private static function mysql_utc($timestamp=null){
        if(null===$timestamp)$timestamp=self::now();
        return gmdate('Y-m-d H:i:s',(int)$timestamp);
    }

    private static function configured_server(){
        $state=self::state();
        if(defined('WPST_LICENSE_SERVER_URL') && WPST_LICENSE_SERVER_URL){
            return self::normalize_server_url(WPST_LICENSE_SERVER_URL);
        }
        if(!empty($state['server_url']))return self::normalize_server_url($state['server_url']);
        return self::normalize_server_url((string)apply_filters('wpst_license_server_url',''));
    }

    private static function normalize_server_url($url){
        $url=trim((string)$url);
        if(!$url)return '';
        $url=untrailingslashit($url);
        $url=preg_replace('#/wp-json/wpsoft-license/v1(?:/(?:activate|check|deactivate))?$#i','',$url);
        return untrailingslashit(esc_url_raw($url));
    }

    private static function valid_server_url($url){
        $url=self::normalize_server_url($url);
        if(!$url)return false;
        $parts=wp_parse_url($url);
        if(empty($parts['scheme'])||empty($parts['host']))return false;
        if('https'===$parts['scheme'])return true;
        $host=strtolower($parts['host']);
        return in_array($host,array('localhost','127.0.0.1','::1'),true);
    }

    private static function endpoint($server,$action){
        return trailingslashit(self::normalize_server_url($server)).'wp-json/wpsoft-license/v1/'.sanitize_key($action);
    }

    private static function domain(){
        $host=wp_parse_url(home_url('/'),PHP_URL_HOST);
        if(!$host)return '';
        return preg_replace('/^www\./','',strtolower($host));
    }

    private static function api_request($action,$server,$key){
        $server=self::normalize_server_url($server);
        $key=strtoupper(trim((string)$key));

        if(!self::valid_server_url($server)){
            return new WP_Error('invalid_server','Geçerli bir HTTPS lisans sunucusu URL’si girin.');
        }
        if(!$key){
            return new WP_Error('missing_key','Lisans anahtarı boş.');
        }

        $url=self::endpoint($server,$action);
        $response=wp_remote_post($url,array(
            'timeout'=>15,
            'redirection'=>2,
            'sslverify'=>true,
            'headers'=>array(
                'Accept'=>'application/json',
                'User-Agent'=>'WPSoft-Site-Tools/'.WPST_VERSION.'; '.home_url('/'),
            ),
            'body'=>array(
                'license_key'=>$key,
                'domain'=>self::domain(),
                'product'=>self::PRODUCT,
                'version'=>WPST_VERSION,
            ),
        ));

        if(is_wp_error($response))return $response;

        $code=(int)wp_remote_retrieve_response_code($response);
        $body=wp_remote_retrieve_body($response);
        $data=json_decode($body,true);

        if(!is_array($data)){
            return new WP_Error('invalid_response','Lisans sunucusu geçerli JSON yanıtı döndürmedi.',array('http_code'=>$code));
        }

        $data['_http_code']=$code;
        return $data;
    }

    private static function grace_timestamp(){
        return self::now()+(DAY_IN_SECONDS*self::GRACE_DAYS);
    }

    public static function status(){
        $state=self::state();
        $status=isset($state['status'])?sanitize_key($state['status']):'inactive';

        if(in_array($status,array('active','grace'),true)){
            $grace_until=isset($state['grace_until'])?(int)$state['grace_until']:0;
            if($grace_until && self::now()>$grace_until){
                return 'inactive';
            }
            return $status;
        }
        return $status ?: 'inactive';
    }

    public static function is_active(){
        return in_array(self::status(),array('active','grace'),true);
    }

    public static function is_grace(){
        return 'grace'===self::status();
    }

    public static function can_edit(){
        return self::is_active() && current_user_can('manage_options');
    }

    public static function require_active($ajax=false){
        if(self::can_edit())return true;
        $message='WPSoft Site Tools salt okunur modda. Bu işlem için aktif lisans gereklidir.';
        if($ajax)wp_send_json_error(array('message'=>$message,'code'=>'wpst_license_required'),403);
        wp_die(
            '<h1>WPSoft Aktivasyonu Gerekli</h1><p>'.esc_html($message).'</p><p><a class="button button-primary" href="'.esc_url(admin_url('admin.php?page=wpsoft-activation')).'">Aktivasyon Sayfasına Git</a></p>',
            'WPSoft Aktivasyonu Gerekli',
            array('response'=>403,'back_link'=>true)
        );
    }

    public static function menu(){
        $hook=add_submenu_page(
            'wpsoft-site-tools',
            'WPSoft Aktivasyon',
            'Aktivasyon',
            'manage_options',
            'wpsoft-activation',
            array(__CLASS__,'page')
        );
        if($hook)$GLOBALS['wpst_activation_page_hook']=$hook;
    }

    public static function activate(){
        if(!current_user_can('manage_options'))wp_die('Yetkiniz yok.');
        check_admin_referer('wpst_activate_license');

        $server=isset($_POST['server_url'])?self::normalize_server_url(wp_unslash($_POST['server_url'])):'';
        $key=isset($_POST['license_key'])?strtoupper(trim(sanitize_text_field(wp_unslash($_POST['license_key'])))):'';

        if(!self::valid_server_url($server)){
            self::activation_redirect(array('license_error'=>'invalid_server'));
        }

        $result=self::api_request('activate',$server,$key);
        if(is_wp_error($result)){
            self::activation_redirect(array('license_error'=>'network','license_message'=>$result->get_error_message()));
        }

        if(empty($result['success']) || 'active'!==sanitize_key($result['status']??'')){
            self::activation_redirect(array(
                'license_error'=>sanitize_key($result['status']??'invalid'),
                'license_message'=>sanitize_text_field($result['message']??'Aktivasyon başarısız.')
            ));
        }

        $now=self::now();
        self::save_state(array(
            'status'=>'active',
            'remote_status'=>'active',
            'server_url'=>$server,
            'license_key'=>$key,
            'license_id'=>absint($result['license_id']??0),
            'domain'=>self::domain(),
            'domain_limit'=>absint($result['domain_limit']??1),
            'expires_at'=>sanitize_text_field($result['expires_at']??''),
            'activated_at'=>self::mysql_utc($now),
            'last_check'=>$now,
            'last_success'=>$now,
            'grace_until'=>self::grace_timestamp(),
            'last_message'=>sanitize_text_field($result['message']??'Lisans aktif.'),
        ));

        self::activation_redirect(array('activated'=>1));
    }

    public static function deactivate(){
        if(!current_user_can('manage_options'))wp_die('Yetkiniz yok.');
        check_admin_referer('wpst_deactivate_license');

        $state=self::state();
        $server=!empty($state['server_url'])?$state['server_url']:'';
        $key=!empty($state['license_key'])?$state['license_key']:'';

        if($server && $key){
            $result=self::api_request('deactivate',$server,$key);
            if(is_wp_error($result)){
                self::activation_redirect(array('license_error'=>'deactivate_network','license_message'=>$result->get_error_message()));
            }
            if(empty($result['success'])){
                self::activation_redirect(array(
                    'license_error'=>'deactivate_failed',
                    'license_message'=>sanitize_text_field($result['message']??'Deaktivasyon başarısız.')
                ));
            }
        }

        self::save_state(array(
            'status'=>'inactive',
            'remote_status'=>'inactive',
            'server_url'=>$server,
            'deactivated_at'=>self::mysql_utc(),
            'last_message'=>'Domain aktivasyonu kaldırıldı.'
        ));

        self::activation_redirect(array('deactivated'=>1));
    }

    public static function manual_check(){
        if(!current_user_can('manage_options'))wp_die('Yetkiniz yok.');
        check_admin_referer('wpst_check_license');

        $result=self::remote_check(true);
        if(is_wp_error($result)){
            self::activation_redirect(array('checked'=>'network','license_message'=>$result->get_error_message()));
        }

        self::activation_redirect(array('checked'=>sanitize_key($result['status']??'done')));
    }

    private static function activation_redirect($args=array()){
        $url=add_query_arg($args,admin_url('admin.php?page=wpsoft-activation'));
        wp_safe_redirect($url);
        exit;
    }

    public static function remote_check($manual=false){
        $state=self::state();
        $server=!empty($state['server_url'])?$state['server_url']:self::configured_server();
        $key=!empty($state['license_key'])?$state['license_key']:'';

        if(!$server || !$key){
            return new WP_Error('not_configured','Lisans sunucusu veya lisans anahtarı yapılandırılmamış.');
        }

        $now=self::now();
        $state['last_check']=$now;
        $result=self::api_request('check',$server,$key);

        if(is_wp_error($result)){
            $last_success=isset($state['last_success'])?(int)$state['last_success']:0;
            $grace_until=isset($state['grace_until'])?(int)$state['grace_until']:0;

            if($last_success && $grace_until && $now<=$grace_until){
                $state['status']='grace';
                $state['remote_status']='unreachable';
                $state['last_message']='Lisans sunucusuna ulaşılamadı. Grace period kullanılıyor: '.$result->get_error_message();
                self::save_state($state);
                return array('success'=>true,'status'=>'grace','message'=>$state['last_message']);
            }

            $state['status']='inactive';
            $state['remote_status']='unreachable';
            $state['last_message']='Lisans sunucusuna ulaşılamadı ve grace period sona erdi.';
            self::save_state($state);
            return $result;
        }

        $remote_status=sanitize_key($result['status']??'invalid');
        $success=!empty($result['success']) && 'active'===$remote_status;

        if($success){
            $state['status']='active';
            $state['remote_status']='active';
            $state['last_success']=$now;
            $state['grace_until']=self::grace_timestamp();
            $state['license_id']=absint($result['license_id']??($state['license_id']??0));
            $state['domain_limit']=absint($result['domain_limit']??($state['domain_limit']??1));
            $state['expires_at']=sanitize_text_field($result['expires_at']??($state['expires_at']??''));
            $state['last_message']=sanitize_text_field($result['message']??'Lisans aktif.');
            self::save_state($state);
            return $result;
        }

        // Sunucu açıkça lisansı reddediyorsa grace uygulanmaz.
        // invalid_key; serverdan lisans kaydı silindiğinde de bu bloğa düşer.
        $state['status']=$remote_status ?: 'inactive';
        $state['remote_status']=$remote_status ?: 'inactive';
        $state['last_message']=sanitize_text_field($result['message']??'Lisans doğrulanamadı.');
        $state['grace_until']=0;
        $state['last_rejected_at']=$now;
        self::save_state($state);
        return $result;
    }

    public static function cron_check(){
        $state=self::state();
        if(empty($state['license_key'])||empty($state['server_url']))return;
        self::remote_check(false);
    }

    public static function maybe_background_check(){
        if(!current_user_can('manage_options'))return;

        $state=self::state();
        if(empty($state['license_key'])||empty($state['server_url']))return;

        $last=isset($state['last_check'])?(int)$state['last_check']:0;
        $age=$last ? (self::now()-$last) : PHP_INT_MAX;

        // WPSoft yönetim ekranlarında lisans daha hızlı doğrulanır.
        // Böylece server tarafında silinen/askıya alınan lisans dakikalarca aktif görünmez.
        $is_wpsoft_admin=false;
        if(is_admin()){
            $page=isset($_GET['page'])?sanitize_key(wp_unslash($_GET['page'])):'';
            $post_type=isset($_GET['post_type'])?sanitize_key(wp_unslash($_GET['post_type'])):'';

            if(0===strpos($page,'wpsoft-') || 'elementor_library'===$post_type){
                $is_wpsoft_admin=true;
            }

            if(function_exists('get_current_screen')){
                $screen=get_current_screen();
                if($screen){
                    $id=(string)$screen->id;
                    if(false!==strpos($id,'wpsoft') || false!==strpos($id,'elementor_library')){
                        $is_wpsoft_admin=true;
                    }
                }
            }
        }

        // WPSoft ekranlarında 30 saniye, diğer admin ekranlarında 5 dakika cache.
        $interval=$is_wpsoft_admin ? 30 : self::CHECK_INTERVAL;
        if($age<$interval)return;

        self::remote_check(false);
    }

    public static function protected_admin_post_actions(){
        return array(
            'wpst_save_mega_menu',
            'wpst_create_menu_template',
            'wpst_delete_menu_template',
            'wpst_create_mega_preset',
            'wpst_apply_hf_template',
            'wpst_create_elementor_hf_template',
            'wpst_delete_my_template',
            'wpst_duplicate_my_template',
            'wpst_toggle_template_favorite',
            'wpst_save_template_conditions',
            'wpst_create_blog_template',
            'wpst_create_demo_page',
            'wpst_create_blog_library_template'
        );
    }

    public static function protected_ajax_actions(){
        return array('wpst_create_hf_template_ajax');
    }

    public static function block_write(){self::require_active(false);}
    public static function block_ajax_write(){self::require_active(true);}

    public static function guard_admin_actions(){
        if(self::is_active())return;
        if(isset($_POST['option_page']) && 'wpst_settings_group'===sanitize_key(wp_unslash($_POST['option_page']))){
            self::require_active(false);
        }
    }

    private static function request_contains_wpsoft(){
        $raw='';
        foreach(array('actions','data','editor_post_id','post_id') as $key){
            if(isset($_POST[$key])){
                $value=wp_unslash($_POST[$key]);
                $raw.=is_array($value)?wp_json_encode($value):(string)$value;
            }
        }
        return false!==stripos($raw,'wpsoft-');
    }

    public static function guard_elementor_save(){
        if(self::is_active())return;
        if(self::request_contains_wpsoft()){
            wp_send_json_error(array(
                'message'=>'Bu sayfa/şablon WPSoft widgetı içeriyor. Lisans aktif değilken WPSoft içeren Elementor belgeleri salt okunur.',
                'code'=>'wpst_license_required'
            ),403);
        }
    }

    public static function admin_assets($hook){
        $screen=function_exists('get_current_screen')?get_current_screen():null;
        $activation_hook=isset($GLOBALS['wpst_activation_page_hook'])?(string)$GLOBALS['wpst_activation_page_hook']:'';
        $is_activation=$activation_hook && $hook===$activation_hook;
        $is_wpsoft=$is_activation || ($screen && (false!==strpos((string)$screen->id,'wpsoft') || false!==strpos((string)$screen->id,'elementor_library')));
        if(!$is_wpsoft)return;

        wp_enqueue_style('wpst-license',WPST_URL.'assets/css/license.css',array(),WPST_VERSION);

        if(!self::is_active()){
            wp_enqueue_script('wpst-license-readonly',WPST_URL.'assets/js/license-readonly.js',array(),WPST_VERSION,true);
            wp_localize_script('wpst-license-readonly','WPST_LICENSE_STATE',array(
                'active'=>false,
                'activationUrl'=>admin_url('admin.php?page=wpsoft-activation'),
                'message'=>'Aktif lisans gerekli · Salt Okunur Mod'
            ));
        }
    }

    public static function notice(){
        $screen=function_exists('get_current_screen')?get_current_screen():null;
        if(!$screen)return;
        $id=(string)$screen->id;
        if(false===strpos($id,'wpsoft') && false===strpos($id,'elementor_library'))return;
        if(false!==strpos($id,'wpsoft-activation'))return;

        if(self::is_grace()){
            $state=self::state();
            $until=!empty($state['grace_until'])?wp_date('d.m.Y H:i',(int)$state['grace_until']):'—';
            echo '<div class="notice notice-warning"><p><strong>WPSoft Lisans Sunucusuna Ulaşılamıyor.</strong> Lisans daha önce doğrulandığı için özellikler grace period boyunca açık. Son tarih: '.esc_html($until).'. <a href="'.esc_url(admin_url('admin.php?page=wpsoft-activation')).'">Lisans Durumu</a></p></div>';
            return;
        }

        if(self::is_active())return;

        echo '<div class="notice notice-warning wpst-license-notice"><p><strong>WPSoft Site Tools · Salt Okunur Mod</strong> Mevcut tasarım frontend’de çalışmaya devam eder. Yeni widget, şablon veya WPSoft ayarı eklemek/değiştirmek/silmek için aktivasyon gereklidir. <a href="'.esc_url(admin_url('admin.php?page=wpsoft-activation')).'">Aktivasyonu Aç</a></p></div>';
    }

    private static function masked_key($key){
        $key=(string)$key;
        if(strlen($key)<10)return '••••••••';
        return substr($key,0,11).'••••••••'.substr($key,-4);
    }

    private static function friendly_status($status){
        $map=array(
            'active'=>'Aktif',
            'grace'=>'Grace Period',
            'inactive'=>'Salt Okunur',
            'expired'=>'Süresi Dolmuş',
            'suspended'=>'Askıda',
            'cancelled'=>'İptal',
            'domain_not_activated'=>'Domain Aktif Değil',
            'invalid_key'=>'Geçersiz Anahtar',
            'product_mismatch'=>'Ürün Uyuşmuyor',
            'domain_limit'=>'Domain Limiti',
            'unreachable'=>'Sunucuya Ulaşılamıyor',
        );
        return $map[$status]??ucfirst($status);
    }

    public static function page(){
        if(!current_user_can('manage_options'))return;

        // Aktivasyon ekranı açıldığında server durumunu güncel tut.
        // Son kontrol 15 saniyeden eskiyse hemen yeniden doğrula.
        $pre_state=self::state();
        if(!empty($pre_state['license_key']) && !empty($pre_state['server_url'])){
            $pre_last=isset($pre_state['last_check'])?(int)$pre_state['last_check']:0;
            if(!$pre_last || (self::now()-$pre_last)>15){
                self::remote_check(false);
            }
        }

        $state=self::state();
        $active=self::is_active();
        $status=self::status();
        $server=!empty($state['server_url'])?$state['server_url']:self::configured_server();
        $key=!empty($state['license_key'])?$state['license_key']:'';
        $last_check=!empty($state['last_check'])?wp_date('d.m.Y H:i',(int)$state['last_check']):'—';
        $last_success=!empty($state['last_success'])?wp_date('d.m.Y H:i',(int)$state['last_success']):'—';
        $grace_until=!empty($state['grace_until'])?wp_date('d.m.Y H:i',(int)$state['grace_until']):'—';
        ?>
        <div class="wrap wpst-license-page">
            <div class="wpst-license-hero">
                <div>
                    <span class="wpst-license-kicker">WPSOFT ACTIVATION · REMOTE LICENSE</span>
                    <h1>WPSoft Site Tools Aktivasyon</h1>
                    <p>Lisans sunucusu üzerinden domain doğrulaması yapılır. Lisans olmadan mevcut site çalışır; WPSoft düzenleme araçları salt okunur moda geçer.</p>
                </div>
                <div class="wpst-license-pill <?php echo $active?'is-active':'is-inactive'; ?>">
                    <i></i><?php echo esc_html(self::friendly_status($status)); ?>
                </div>
            </div>

            <?php if(isset($_GET['activated'])): ?><div class="notice notice-success inline"><p>WPSoft Site Tools lisansı sunucu üzerinden doğrulandı ve etkinleştirildi.</p></div><?php endif; ?>
            <?php if(isset($_GET['deactivated'])): ?><div class="notice notice-warning inline"><p>Domain aktivasyonu lisans sunucusundan kaldırıldı. Eklenti salt okunur moda geçti.</p></div><?php endif; ?>
            <?php if(isset($_GET['checked'])): ?><div class="notice notice-info inline"><p>Lisans kontrolü tamamlandı: <?php echo esc_html(self::friendly_status(sanitize_key(wp_unslash($_GET['checked'])))); ?>.</p></div><?php endif; ?>
            <?php if(isset($_GET['license_error'])): ?>
                <div class="notice notice-error inline"><p><strong>Lisans işlemi başarısız.</strong> <?php echo esc_html(isset($_GET['license_message'])?sanitize_text_field(wp_unslash($_GET['license_message'])):self::friendly_status(sanitize_key(wp_unslash($_GET['license_error'])))); ?></p></div>
            <?php endif; ?>

            <div class="wpst-license-grid">
                <section class="wpst-license-card">
                    <div class="wpst-license-card-head"><span>REMOTE ACTIVATION</span><h2><?php echo $active?'Lisans Durumu':'Lisansı Etkinleştir'; ?></h2></div>

                    <?php if(!$active): ?>
                        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                            <input type="hidden" name="action" value="wpst_activate_license">
                            <?php wp_nonce_field('wpst_activate_license'); ?>
                            <label>Lisans Sunucusu URL
                                <input type="url" name="server_url" value="<?php echo esc_attr($server); ?>" placeholder="https://license.ornek.com" required>
                                <small>WPSoft License Server eklentisinin kurulu olduğu WordPress sitesinin ana adresi.</small>
                            </label>
                            <label>Lisans Anahtarı
                                <input type="text" name="license_key" autocomplete="off" placeholder="WPSOFT-XXXX-XXXX-XXXX-XXXX" required>
                            </label>
                            <button class="button button-primary button-hero">Sunucudan Aktive Et</button>
                        </form>
                        <div class="wpst-license-server-note">
                            <strong>Aşama 3 aktif</strong>
                            <p>Yerel geliştirme anahtarı kaldırıldı. Aktivasyon artık WPSoft License Server API üzerinden doğrulanır.</p>
                        </div>
                    <?php else: ?>
                        <div class="wpst-license-active-box">
                            <span><?php echo self::is_grace()?'!':'✓'; ?></span>
                            <div>
                                <strong><?php echo self::is_grace()?'Grace Period Aktif':'Tüm WPSoft özellikleri açık'; ?></strong>
                                <p><?php echo esc_html(!empty($state['last_message'])?$state['last_message']:'Lisans aktif.'); ?></p>
                            </div>
                        </div>

                        <dl class="wpst-license-info">
                            <div><dt>Durum</dt><dd><?php echo esc_html(self::friendly_status($status)); ?></dd></div>
                            <div><dt>Lisans</dt><dd><?php echo esc_html(self::masked_key($key)); ?></dd></div>
                            <div><dt>Domain</dt><dd><?php echo esc_html(self::domain()); ?></dd></div>
                            <div><dt>Sunucu</dt><dd><?php echo esc_html($server); ?></dd></div>
                            <div><dt>Domain Limiti</dt><dd><?php echo absint($state['domain_limit']??1); ?></dd></div>
                            <div><dt>Lisans Bitişi</dt><dd><?php echo esc_html(!empty($state['expires_at'])?$state['expires_at']:'Süresiz'); ?></dd></div>
                            <div><dt>Son Kontrol</dt><dd><?php echo esc_html($last_check); ?></dd></div>
                            <div><dt>Son Başarılı Kontrol</dt><dd><?php echo esc_html($last_success); ?></dd></div>
                            <div><dt>Grace Sonu</dt><dd><?php echo esc_html($grace_until); ?></dd></div>
                        </dl>

                        <div class="wpst-license-actions-row">
                            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                                <input type="hidden" name="action" value="wpst_check_license">
                                <?php wp_nonce_field('wpst_check_license'); ?>
                                <button class="button button-primary">Şimdi Kontrol Et</button>
                            </form>
                            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" onsubmit="return confirm('Domain aktivasyonu lisans sunucusundan kaldırılsın mı? Site çalışmaya devam eder fakat WPSoft düzenleme araçları kilitlenir.');">
                                <input type="hidden" name="action" value="wpst_deactivate_license">
                                <?php wp_nonce_field('wpst_deactivate_license'); ?>
                                <button class="button">Aktivasyonu Kaldır</button>
                            </form>
                        </div>
                    <?php endif; ?>
                </section>

                <section class="wpst-license-card">
                    <div class="wpst-license-card-head"><span>LICENSE POLICY</span><h2>Çalışma Mantığı</h2></div>
                    <div class="wpst-license-policy">
                        <article class="is-ok"><i>✓</i><div><strong>Frontend hiçbir zaman lisans yüzünden kapanmaz</strong><p>Mevcut Header, Footer, Mega Menü, Blog ve WPSoft widgetları ziyaretçilere gösterilmeye devam eder.</p></div></article>
                        <article class="is-ok"><i>✓</i><div><strong>Hızlı otomatik kontrol</strong><p>WPSoft yönetim ekranlarında yaklaşık 30 saniyelik cache ile, diğer admin ekranlarında 5 dakikada bir lisans sunucusu yeniden doğrulanır.</p></div></article>
                        <article class="is-ok"><i>7</i><div><strong>7 günlük Grace Period</strong><p>Daha önce doğrulanmış lisanslarda sunucu geçici olarak erişilemiyorsa düzenleme özellikleri 7 gün açık kalır.</p></div></article>
                        <article class="is-lock"><i>⌁</i><div><strong>Sunucu lisansı açıkça reddederse kilitlenir</strong><p>Expired, suspended, cancelled, invalid key veya domain uyumsuzluğunda grace uygulanmadan salt okunur moda geçilir.</p></div></article>
                        <article class="is-lock"><i>⌁</i><div><strong>Lisans yokken yazma işlemleri engellenir</strong><p>Widget ekleme, şablon oluşturma/silme, Header/Footer ve diğer WPSoft ayar değişiklikleri sunucu tarafında korunur.</p></div></article>
                    </div>
                </section>
            </div>
        </div>
        <?php
    }
}
