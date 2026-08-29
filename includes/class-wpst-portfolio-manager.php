<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WPSoft Portfolio Manager
 *
 * IMPORTANT:
 * This manager is intentionally isolated from WPSoft Site Tools admin menus.
 * It registers its own top-level "Portföyler" menu so Header/Footer,
 * Global Design, Templates and all existing WPSoft settings remain untouched.
 */
final class WPST_Portfolio_Manager {
    const POST_TYPE = 'wpst_portfolio';
    const TAXONOMY  = 'wpst_portfolio_cat';
    const META_URL  = '_wpst_portfolio_url';

    public static function init() {
        add_action( 'init', array( __CLASS__, 'register_content_types' ), 5 );
        add_action( 'init', array( __CLASS__, 'maybe_refresh_rewrite_rules' ), 99 );
        add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_boxes' ) );
        add_action( 'save_post_' . self::POST_TYPE, array( __CLASS__, 'save_meta' ) );
        add_filter( 'manage_' . self::POST_TYPE . '_posts_columns', array( __CLASS__, 'columns' ) );
        add_action( 'manage_' . self::POST_TYPE . '_posts_custom_column', array( __CLASS__, 'column_content' ), 10, 2 );
        add_filter( 'post_row_actions', array( __CLASS__, 'row_actions' ), 10, 2 );
        add_filter( 'enter_title_here', array( __CLASS__, 'title_placeholder' ), 10, 2 );

        // Register Portfolio in Elementor's supported post types without
        // filtering the option on every editor/AJAX request.
        add_action( 'elementor/loaded', array( __CLASS__, 'ensure_elementor_support' ), 5 );
        add_action( 'admin_init', array( __CLASS__, 'ensure_elementor_support' ), 1 );
        add_action( 'admin_init', array( __CLASS__, 'migrate_portfolio_elementor_context' ), 20 );
        add_action( 'admin_init', array( __CLASS__, 'refresh_portfolio_widget_cache_once' ), 30 );

        // Keep public Elementor output fresh after either side changes:
        // the Elementor document containing Portfolio 2.0 or a central portfolio item.
        add_action( 'elementor/document/after_save', array( __CLASS__, 'after_elementor_document_save' ), 20, 2 );
        add_action( 'save_post_' . self::POST_TYPE, array( __CLASS__, 'invalidate_portfolio_widget_documents' ), 30, 1 );
        add_action( 'set_object_terms', array( __CLASS__, 'maybe_invalidate_after_portfolio_terms' ), 30, 6 );
        add_action( 'updated_post_meta', array( __CLASS__, 'maybe_invalidate_after_portfolio_meta' ), 30, 4 );
        add_action( 'added_post_meta', array( __CLASS__, 'maybe_invalidate_after_portfolio_meta' ), 30, 4 );
        add_action( 'deleted_post_meta', array( __CLASS__, 'maybe_invalidate_after_portfolio_meta' ), 30, 4 );
        add_action( 'template_redirect', array( __CLASS__, 'bypass_stale_frontend_element_cache' ), 1 );

    }

    public static function bypass_stale_frontend_element_cache() {
        if ( is_admin() || isset( $_GET['elementor-preview'] ) ) return; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

        $post_id = get_queried_object_id();
        if ( ! $post_id ) return;

        $raw = get_post_meta( $post_id, '_elementor_data', true );
        if ( ! is_string( $raw ) || false === strpos( $raw, 'wpsoft-portfolio' ) ) return;

        /*
         * Portfolio 2.0 is dynamic. A document-level Elementor element cache can
         * still contain HTML generated before the widget switched to the
         * central portfolio source. Remove only this document's element cache
         * immediately before Elementor renders the public page.
         */
        delete_post_meta( $post_id, '_elementor_element_cache' );
        clean_post_cache( $post_id );
    }

    private static function portfolio_widget_document_ids() {
        global $wpdb;

        $like = '%' . $wpdb->esc_like( '"widgetType":"wpsoft-portfolio"' ) . '%';
        $ids = $wpdb->get_col(
            $wpdb->prepare(
                "SELECT DISTINCT post_id
                 FROM {$wpdb->postmeta}
                 WHERE meta_key = '_elementor_data'
                   AND meta_value LIKE %s",
                $like
            )
        ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery

        // Older Elementor JSON encoders/spaces may not match the compact token.
        if ( empty( $ids ) ) {
            $fallback = '%' . $wpdb->esc_like( 'wpsoft-portfolio' ) . '%';
            $ids = $wpdb->get_col(
                $wpdb->prepare(
                    "SELECT DISTINCT post_id
                     FROM {$wpdb->postmeta}
                     WHERE meta_key = '_elementor_data'
                       AND meta_value LIKE %s",
                    $fallback
                )
            ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
        }

        return array_values( array_unique( array_filter( array_map( 'absint', (array) $ids ) ) ) );
    }

    private static function clear_elementor_document_cache( $post_id ) {
        $post_id = absint( $post_id );
        if ( ! $post_id ) return;

        delete_post_meta( $post_id, '_elementor_element_cache' );
        delete_post_meta( $post_id, '_elementor_css' );

        // Elementor also keeps document/element objects in runtime cache.
        clean_post_cache( $post_id );

        if ( did_action( 'elementor/loaded' ) && class_exists( '\Elementor\Plugin' ) ) {
            try {
                $plugin = \Elementor\Plugin::instance();

                if ( isset( $plugin->files_manager ) && is_object( $plugin->files_manager ) && method_exists( $plugin->files_manager, 'clear_cache' ) ) {
                    $plugin->files_manager->clear_cache();
                }

                if ( isset( $plugin->documents ) && is_object( $plugin->documents ) && method_exists( $plugin->documents, 'get' ) ) {
                    $document = $plugin->documents->get( $post_id, false );
                    if ( $document && method_exists( $document, 'get_elements_data' ) ) {
                        // Touching the document after metadata cleanup ensures a
                        // subsequent frontend request rebuilds its element tree.
                        $document->get_elements_data();
                    }
                }
            } catch ( \Throwable $e ) {
                // A cache refresh must never interrupt the save request.
            }
        }
    }

    public static function after_elementor_document_save( $document, $data = array() ) {
        if ( ! is_object( $document ) || ! method_exists( $document, 'get_main_id' ) ) return;

        $post_id = absint( $document->get_main_id() );
        if ( ! $post_id ) return;

        $raw = get_post_meta( $post_id, '_elementor_data', true );
        if ( ! is_string( $raw ) || false === strpos( $raw, 'wpsoft-portfolio' ) ) return;

        self::clear_elementor_document_cache( $post_id );
    }

    public static function invalidate_portfolio_widget_documents( $portfolio_id = 0 ) {
        $portfolio_id = absint( $portfolio_id );
        if ( $portfolio_id && self::POST_TYPE !== get_post_type( $portfolio_id ) ) return;

        foreach ( self::portfolio_widget_document_ids() as $post_id ) {
            self::clear_elementor_document_cache( $post_id );
        }
    }

    public static function maybe_invalidate_after_portfolio_terms( $object_id, $terms, $tt_ids, $taxonomy, $append, $old_tt_ids ) {
        if ( self::TAXONOMY !== $taxonomy || self::POST_TYPE !== get_post_type( $object_id ) ) return;
        self::invalidate_portfolio_widget_documents( $object_id );
    }

    public static function maybe_invalidate_after_portfolio_meta( $meta_id, $object_id, $meta_key, $meta_value ) {
        if ( self::POST_TYPE !== get_post_type( $object_id ) ) return;

        // Only metadata that changes a Portfolio 2.0 card needs to invalidate
        // every page that lists central portfolios.
        $watched = array(
            self::META_URL,
            '_thumbnail_id',
            '_wp_attachment_metadata',
        );
        if ( ! in_array( $meta_key, $watched, true ) ) return;

        self::invalidate_portfolio_widget_documents( $object_id );
    }

    public static function refresh_portfolio_widget_cache_once() {
        /*
         * Elementor can keep previously rendered widget HTML in
         * _elementor_element_cache. The editor bypasses that stale HTML while
         * the public page can continue serving an old empty Portfolio widget.
         * Clear only documents that actually contain WPSoft Portfolio 2.0.
         */
        $schema = 'portfolio-widget-front-cache-v1';
        if ( get_option( 'wpst_portfolio_widget_cache_schema' ) === $schema ) return;

        global $wpdb;
        $like = '%' . $wpdb->esc_like( 'wpsoft-portfolio' ) . '%';
        $ids = $wpdb->get_col(
            $wpdb->prepare(
                "SELECT DISTINCT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_elementor_data' AND meta_value LIKE %s",
                $like
            )
        ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery

        foreach ( (array) $ids as $post_id ) {
            $post_id = absint( $post_id );
            if ( ! $post_id ) continue;
            delete_post_meta( $post_id, '_elementor_element_cache' );
            delete_post_meta( $post_id, '_elementor_css' );
        }

        if ( did_action( 'elementor/loaded' ) && class_exists( '\Elementor\Plugin' ) ) {
            try {
                $plugin = \Elementor\Plugin::instance();
                if ( isset( $plugin->files_manager ) && is_object( $plugin->files_manager ) && method_exists( $plugin->files_manager, 'clear_cache' ) ) {
                    $plugin->files_manager->clear_cache();
                }
            } catch ( \Throwable $e ) {
                // Cache refresh must never break wp-admin.
            }
        }

        update_option( 'wpst_portfolio_widget_cache_schema', $schema, false );
    }

    public static function register_content_types() {
        register_post_type( self::POST_TYPE, array(
            'labels' => array(
                'name'                  => 'Portföyler',
                'singular_name'         => 'Portföy',
                'menu_name'             => 'Portföyler',
                'name_admin_bar'        => 'Portföy',
                'add_new'               => 'Yeni Ekle',
                'add_new_item'          => 'Yeni Portföy Ekle',
                'new_item'              => 'Yeni Portföy',
                'edit_item'             => 'Portföyü Düzenle',
                'view_item'             => 'Portföyü Görüntüle',
                'all_items'             => 'Tüm Portföyler',
                'search_items'          => 'Portföylerde Ara',
                'not_found'             => 'Portföy bulunamadı.',
                'not_found_in_trash'    => 'Çöp kutusunda portföy bulunamadı.',
                'featured_image'        => 'Proje Görseli',
                'set_featured_image'    => 'Proje görseli seç',
                'remove_featured_image' => 'Proje görselini kaldır',
                'use_featured_image'    => 'Proje görseli olarak kullan',
            ),
            'public'             => true,
            'publicly_queryable' => true,
            'query_var'          => true,
            'exclude_from_search'=> false,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'show_in_admin_bar'  => true,
            'show_in_rest'       => true,
            'has_archive'        => false,
            'rewrite'            => array( 'slug' => 'portfolio', 'with_front' => false ),
            'supports'           => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes' ),
            'menu_icon'          => 'dashicons-portfolio',
            'menu_position'      => 59,
            'capability_type'    => 'post',
            'map_meta_cap'       => true,
        ) );

        register_taxonomy( self::TAXONOMY, array( self::POST_TYPE ), array(
            'labels' => array(
                'name'              => 'Portföy Kategorileri',
                'singular_name'     => 'Portföy Kategorisi',
                'search_items'      => 'Kategorilerde Ara',
                'all_items'         => 'Tüm Kategoriler',
                'parent_item'       => 'Üst Kategori',
                'parent_item_colon' => 'Üst Kategori:',
                'edit_item'         => 'Kategoriyi Düzenle',
                'update_item'       => 'Kategoriyi Güncelle',
                'add_new_item'      => 'Yeni Kategori Ekle',
                'new_item_name'     => 'Yeni Kategori Adı',
                'menu_name'         => 'Kategoriler',
            ),
            'public'            => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'show_in_rest'      => true,
            'hierarchical'      => true,
            'rewrite'           => array( 'slug' => 'portfolio-kategori', 'with_front' => false ),
        ) );
    }



    public static function maybe_refresh_rewrite_rules() {
        /*
         * Updating a plugin ZIP does not run register_activation_hook().
         * Therefore a newly introduced custom post type can remain absent
         * from WordPress rewrite rules and both "Görüntüle" and Elementor's
         * preview iframe return 404. Refresh once per portfolio schema.
         */
        $schema = 'portfolio-rewrite-v2';
        if ( get_option( 'wpst_portfolio_rewrite_schema' ) === $schema ) return;

        flush_rewrite_rules( false );
        update_option( 'wpst_portfolio_rewrite_schema', $schema, false );
    }

    public static function ensure_elementor_support() {
        $types = get_option( 'elementor_cpt_support', array( 'post', 'page' ) );
        if ( ! is_array( $types ) ) $types = array( 'post', 'page' );

        if ( ! in_array( self::POST_TYPE, $types, true ) ) {
            $types[] = self::POST_TYPE;
            update_option( 'elementor_cpt_support', array_values( array_unique( $types ) ), false );
        }
    }


    public static function migrate_portfolio_elementor_context() {
        if ( ! current_user_can( 'edit_posts' ) ) return;

        $ids = get_posts( array(
            'post_type'      => self::POST_TYPE,
            'post_status'    => array( 'publish','draft','private','pending' ),
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'no_found_rows'  => true,
            'meta_query'     => array(
                array( 'key' => '_elementor_data', 'compare' => 'EXISTS' ),
            ),
        ) );

        foreach ( (array) $ids as $post_id ) {
            $document_fix = get_post_meta( $post_id, '_wpst_portfolio_elementor_document_fix_2', true );
            if ( '1' !== $document_fix ) {
                update_post_meta( $post_id, '_elementor_edit_mode', 'builder' );
                update_post_meta( $post_id, '_elementor_template_type', 'wp-post' );
                if ( ! get_post_meta( $post_id, '_elementor_page_settings', true ) ) {
                    update_post_meta( $post_id, '_elementor_page_settings', array( 'hide_title' => 'yes' ) );
                }
                delete_post_meta( $post_id, '_elementor_css' );
                update_post_meta( $post_id, '_wpst_portfolio_elementor_document_fix_2', '1' );
            }

            if ( '1' === get_post_meta( $post_id, '_wpst_portfolio_elementor_context_fix_1', true ) ) continue;

            $raw = get_post_meta( $post_id, '_elementor_data', true );
            if ( ! $raw ) {
                update_post_meta( $post_id, '_wpst_portfolio_elementor_context_fix_1', '1' );
                continue;
            }

            $data = json_decode( wp_unslash( $raw ), true );
            if ( ! is_array( $data ) ) {
                update_post_meta( $post_id, '_wpst_portfolio_elementor_context_fix_1', '1' );
                continue;
            }

            $changed = false;
            $data = self::replace_legacy_portfolio_dynamic_widgets( $data, $changed );

            if ( $changed ) {
                update_post_meta( $post_id, '_elementor_data', wp_slash( wp_json_encode( $data ) ) );
                delete_post_meta( $post_id, '_elementor_css' );
            }

            update_post_meta( $post_id, '_wpst_portfolio_elementor_context_fix_1', '1' );
        }
    }

    private static function replace_legacy_portfolio_dynamic_widgets( $nodes, &$changed ) {
        if ( ! is_array( $nodes ) ) return $nodes;

        $map = array(
            'wpsoft-post-title'   => 'wpsoft-portfolio-title',
            'wpsoft-post-excerpt' => 'wpsoft-portfolio-excerpt',
            'wpsoft-post-image'   => 'wpsoft-portfolio-image',
            'wpsoft-post-terms'   => 'wpsoft-portfolio-terms',
        );

        foreach ( $nodes as $index => $node ) {
            if ( ! is_array( $node ) ) continue;

            if ( isset( $node['elType'], $node['widgetType'] ) && 'widget' === $node['elType'] ) {
                if ( isset( $map[ $node['widgetType'] ] ) ) {
                    $node['widgetType'] = $map[ $node['widgetType'] ];
                    $changed = true;
                } elseif ( 'wpsoft-post-share' === $node['widgetType'] ) {
                    $node['widgetType'] = 'wpsoft-heading';
                    $node['settings'] = array(
                        'eyebrow' => 'PROJE',
                        'title' => 'Proje detayını kendi içeriğinizle tamamlayın',
                        'description' => 'Bu alan Elementor üzerinden düzenlenebilir.',
                        'wpst_heading_color' => '#0f172a',
                        'wpst_body_color' => '#64748b',
                    );
                    $changed = true;
                }
            }

            if ( ! empty( $node['elements'] ) && is_array( $node['elements'] ) ) {
                $node['elements'] = self::replace_legacy_portfolio_dynamic_widgets( $node['elements'], $changed );
            }

            $nodes[ $index ] = $node;
        }

        return $nodes;
    }

    public static function row_actions( $actions, $post ) {
        if ( ! $post || self::POST_TYPE !== $post->post_type || ! current_user_can( 'edit_post', $post->ID ) ) return $actions;
        $actions['wpst_elementor'] = '<a href="' . esc_url( admin_url( 'post.php?post=' . $post->ID . '&action=elementor' ) ) . '">Elementor ile Düzenle</a>';
        return $actions;
    }

    public static function template_menu() {
        add_submenu_page(
            'edit.php?post_type=' . self::POST_TYPE,
            'Hazır Portföy Şablonları',
            'Hazır Şablonlar',
            'edit_posts',
            'wpst-portfolio-templates',
            array( __CLASS__, 'template_page' )
        );
    }

    public static function templates() {
        return array(
            'editorial' => array(
                'title' => 'Editorial Case',
                'desc'  => 'Büyük başlık, kategori, güçlü kapak görseli ve rahat içerik akışı.',
                'tone'  => 'Light · Editorial',
            ),
            'split' => array(
                'title' => 'Split Showcase',
                'desc'  => 'Başlık ve proje özetini görselle yan yana sunan modern case-study düzeni.',
                'tone'  => 'Light · Split',
            ),
            'dark' => array(
                'title' => 'Dark Studio',
                'desc'  => 'Ajans, mimari ve yaratıcı işler için koyu premium proje detay sayfası.',
                'tone'  => 'Dark · Studio',
            ),
            'minimal' => array(
                'title' => 'Minimal Case',
                'desc'  => 'İçerik ve görseli öne çıkaran sade, hızlı ve kurumsal proje düzeni.',
                'tone'  => 'Minimal · Clean',
            ),
        );
    }

    private static function uid() {
        return substr( md5( uniqid( '', true ) ), 0, 8 );
    }

    private static function el( $widget, $settings = array() ) {
        /*
         * Every widget inserted by a Portfolio ready template starts from the
         * same contract as WPSoft Template Library:
         * Hızlı Tasarım Preseti = Global Tasarımı Takip Et.
         * Template-specific local values may still override individual fields.
         */
        $settings = array_merge( array(
            'wpst_signature_preset' => 'global',
            'wpst_use_global_design' => 'yes',
            'wpst_design_mode'       => 'global',
        ), (array) $settings );

        return array(
            'id'         => self::uid(),
            'elType'     => 'widget',
            'widgetType' => $widget,
            'settings'   => $settings,
            'elements'   => array(),
        );
    }

    private static function cont( $elements, $settings = array() ) {
        return array(
            'id'       => self::uid(),
            'elType'   => 'container',
            'settings' => array_merge( array(
                'content_width' => 'boxed',
                'boxed_width'   => array( 'unit' => 'px', 'size' => 1240, 'sizes' => array() ),
                'padding'       => array( 'unit' => 'px', 'top' => '64', 'right' => '28', 'bottom' => '64', 'left' => '28', 'isLinked' => false ),
            ), $settings ),
            'elements' => $elements,
            'isInner'  => false,
        );
    }

    public static function template_data( $key ) {
        /*
         * Portfolio Template Library 2.0
         * Templates are real WPSoft Elementor compositions. They intentionally
         * avoid depending on portfolio-only dynamic widgets so clicking a
         * library card can always be inserted by Elementor's normal element API.
         */
        $hero = self::el( 'wpsoft-heading', array(
            'eyebrow' => 'SELECTED PROJECT',
            'title' => 'Markayı dijitalde yeniden konumlandıran güçlü bir proje',
            'description' => 'Strateji, tasarım ve geliştirme süreçlerini tek bir hikâyede birleştiren örnek portföy sunumu.',
            'align' => 'left',
            'wpst_heading_color' => '#0f172a',
            'wpst_body_color' => '#64748b',
            'wpst_heading_font_size' => array( 'size' => 58, 'unit' => 'px' ),
            'wpst_body_font_size' => array( 'size' => 18, 'unit' => 'px' ),
        ) );
        $intro = self::el( 'wpsoft-image-text', array(
            'eyebrow' => 'PROJE HAKKINDA',
            'title' => 'İhtiyaçtan deneyime uzanan yaratıcı çözüm',
            'description' => 'Projenin hedeflerini, tasarım kararlarını ve uygulama sürecini burada anlatabilirsiniz. Bu içerik Elementor üzerinden tamamen düzenlenebilir.',
            'button_text' => 'Projeyi İncele',
            'button_url' => array( 'url' => '#' ),
        ) );
        $stats = self::el( 'wpsoft-stats-grid', array(
            'layout_variant' => 'strip',
            'style_preset' => 'soft',
            'columns' => '3',
        ) );
        $gallery = self::el( 'wpsoft-gallery-zoom-pro', array(
            'layout' => 'grid',
            'columns' => '3',
            'lightbox' => 'yes',
        ) );
        $quote = self::el( 'wpsoft-quote', array() );
        $reviews = self::el( 'wpsoft-reviews-pro', array(
            'layout' => 'wall',
            'columns' => '3',
        ) );
        $cta = self::el( 'wpsoft-cta', array(
            'title' => 'Benzer bir proje planlıyor musunuz?',
            'description' => 'İhtiyacınızı konuşalım ve doğru çözümü birlikte planlayalım.',
            'button_text' => 'Projeyi Konuşalım',
            'button_url' => array( 'url' => '#iletisim' ),
            'wpst_button_background' => '#2563eb',
            'wpst_button_text_color' => '#ffffff',
        ) );

        if ( 'split' === $key ) {
            $left = self::cont( array( $hero ), array(
                'content_width' => 'full',
                'width' => array( 'unit' => '%', 'size' => 46, 'sizes' => array() ),
                'width_mobile' => array( 'unit' => '%', 'size' => 100, 'sizes' => array() ),
                'padding' => array( 'unit' => 'px', 'top' => '54', 'right' => '38', 'bottom' => '54', 'left' => '0', 'isLinked' => false ),
            ) );
            $right = self::cont( array( self::el( 'wpsoft-image-text', array(
                'eyebrow' => 'CASE STUDY',
                'title' => 'Görsel odaklı proje vitrini',
                'description' => 'Ana proje görselinizi ve kısa proje bilgisini bu blokta sunun.',
                'button_text' => 'Detaylar',
                'button_url' => array( 'url' => '#' ),
            ) ) ), array(
                'content_width' => 'full',
                'width' => array( 'unit' => '%', 'size' => 54, 'sizes' => array() ),
                'width_mobile' => array( 'unit' => '%', 'size' => 100, 'sizes' => array() ),
            ) );
            return array(
                self::cont( array( $left, $right ), array(
                    'flex_direction' => 'row',
                    'flex_direction_mobile' => 'column',
                    'gap' => array( 'unit' => 'px', 'size' => 24, 'row' => 24, 'column' => 24 ),
                    'padding' => array( 'unit' => 'px', 'top' => '54', 'right' => '28', 'bottom' => '54', 'left' => '28', 'isLinked' => false ),
                ) ),
                self::cont( array( $stats ), array( 'background_background'=>'classic','background_color'=>'#f8fafc' ) ),
                self::cont( array( $gallery ), array( 'padding'=>array('unit'=>'px','top'=>'76','right'=>'28','bottom'=>'76','left'=>'28','isLinked'=>false) ) ),
                self::cont( array( $quote, $cta ), array( 'boxed_width'=>array('unit'=>'px','size'=>980,'sizes'=>array()) ) ),
            );
        }

        if ( 'dark' === $key ) {
            $darkhero = self::el( 'wpsoft-heading', array(
                'eyebrow' => 'CREATIVE STUDIO',
                'title' => 'Cesur fikir. Güçlü görsel dil. Ölçülebilir sonuç.',
                'description' => 'Ajans, mimari, tasarım ve yaratıcı projeler için koyu premium portföy hikâyesi.',
                'wpst_heading_color' => '#ffffff',
                'wpst_body_color' => '#cbd5e1',
                'wpst_heading_font_size' => array( 'size' => 68, 'unit' => 'px' ),
            ) );
            return array(
                self::cont( array( $darkhero ), array(
                    'background_background'=>'classic','background_color'=>'#07111f',
                    'padding'=>array('unit'=>'px','top'=>'104','right'=>'28','bottom'=>'104','left'=>'28','isLinked'=>false)
                ) ),
                self::cont( array( $intro ), array(
                    'background_background'=>'classic','background_color'=>'#0f172a',
                    'padding'=>array('unit'=>'px','top'=>'72','right'=>'28','bottom'=>'72','left'=>'28','isLinked'=>false)
                ) ),
                self::cont( array( $gallery ), array(
                    'background_background'=>'classic','background_color'=>'#07111f',
                    'padding'=>array('unit'=>'px','top'=>'72','right'=>'28','bottom'=>'72','left'=>'28','isLinked'=>false)
                ) ),
                self::cont( array( $stats, $reviews ), array( 'boxed_width'=>array('unit'=>'px','size'=>1120,'sizes'=>array()) ) ),
                self::cont( array( $cta ), array( 'background_background'=>'classic','background_color'=>'#f8fafc' ) ),
            );
        }

        if ( 'minimal' === $key ) {
            return array(
                self::cont( array( $hero ), array(
                    'boxed_width'=>array('unit'=>'px','size'=>900,'sizes'=>array()),
                    'padding'=>array('unit'=>'px','top'=>'86','right'=>'24','bottom'=>'54','left'=>'24','isLinked'=>false)
                ) ),
                self::cont( array( $intro ), array(
                    'boxed_width'=>array('unit'=>'px','size'=>1040,'sizes'=>array()),
                    'padding'=>array('unit'=>'px','top'=>'32','right'=>'24','bottom'=>'54','left'=>'24','isLinked'=>false)
                ) ),
                self::cont( array( $gallery ), array(
                    'boxed_width'=>array('unit'=>'px','size'=>1120,'sizes'=>array()),
                    'padding'=>array('unit'=>'px','top'=>'24','right'=>'24','bottom'=>'64','left'=>'24','isLinked'=>false)
                ) ),
                self::cont( array( $stats, $cta ), array(
                    'boxed_width'=>array('unit'=>'px','size'=>900,'sizes'=>array()),
                    'padding'=>array('unit'=>'px','top'=>'36','right'=>'24','bottom'=>'80','left'=>'24','isLinked'=>false)
                ) ),
            );
        }

        // Editorial Case
        return array(
            self::cont( array( $hero ), array(
                'boxed_width'=>array('unit'=>'px','size'=>1060,'sizes'=>array()),
                'padding'=>array('unit'=>'px','top'=>'88','right'=>'28','bottom'=>'56','left'=>'28','isLinked'=>false)
            ) ),
            self::cont( array( $intro ), array(
                'boxed_width'=>array('unit'=>'px','size'=>1180,'sizes'=>array()),
                'background_background'=>'classic','background_color'=>'#f8fafc',
                'padding'=>array('unit'=>'px','top'=>'70','right'=>'28','bottom'=>'70','left'=>'28','isLinked'=>false)
            ) ),
            self::cont( array( $stats ), array(
                'boxed_width'=>array('unit'=>'px','size'=>1100,'sizes'=>array()),
                'padding'=>array('unit'=>'px','top'=>'58','right'=>'28','bottom'=>'58','left'=>'28','isLinked'=>false)
            ) ),
            self::cont( array( $gallery ), array(
                'boxed_width'=>array('unit'=>'px','size'=>1240,'sizes'=>array()),
                'padding'=>array('unit'=>'px','top'=>'46','right'=>'28','bottom'=>'72','left'=>'28','isLinked'=>false)
            ) ),
            self::cont( array( $quote, $reviews ), array(
                'boxed_width'=>array('unit'=>'px','size'=>1040,'sizes'=>array()),
                'padding'=>array('unit'=>'px','top'=>'62','right'=>'28','bottom'=>'62','left'=>'28','isLinked'=>false)
            ) ),
            self::cont( array( $cta ), array(
                'background_background'=>'classic','background_color'=>'#f8fafc',
                'padding'=>array('unit'=>'px','top'=>'64','right'=>'28','bottom'=>'84','left'=>'28','isLinked'=>false)
            ) ),
        );
    }

    private static function write_elementor_template( $post_id, $key ) {
        $templates = self::templates();
        if ( ! isset( $templates[ $key ] ) ) $key = 'editorial';

        update_post_meta( $post_id, '_elementor_edit_mode', 'builder' );
        update_post_meta( $post_id, '_elementor_template_type', 'wp-post' );
        update_post_meta( $post_id, '_elementor_version', defined( 'ELEMENTOR_VERSION' ) ? ELEMENTOR_VERSION : '3.0.0' );
        update_post_meta( $post_id, '_elementor_page_settings', array( 'hide_title' => 'yes' ) );
        update_post_meta( $post_id, '_elementor_data', wp_slash( wp_json_encode( self::template_data( $key ) ) ) );
        update_post_meta( $post_id, '_wpst_portfolio_template', sanitize_key( $key ) );
        delete_post_meta( $post_id, '_elementor_css' );
    }

    public static function template_page() {
        if ( ! current_user_can( 'edit_posts' ) ) wp_die( 'Yetkiniz yok.' );

        echo '<div class="wrap"><h1>Hazır Portföy Şablonları</h1>';
        echo '<p style="max-width:760px;color:#64748b">Bir tasarım seçtiğinizde yeni bir Portföy kaydı oluşturulur ve Elementor ile düzenleme ekranı açılır. Başlık, proje görseli, kategori ve içerik daha sonra projeye özel olarak değiştirilebilir.</p>';
        echo '<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:18px;max-width:1180px;margin-top:24px">';

        foreach ( self::templates() as $key => $template ) {
            $url = wp_nonce_url(
                admin_url( 'admin-post.php?action=wpst_create_portfolio_from_template&template=' . rawurlencode( $key ) ),
                'wpst_create_portfolio_' . $key
            );
            echo '<article style="background:#fff;border:1px solid #dcdcde;border-radius:14px;padding:22px;box-shadow:0 8px 28px rgba(15,23,42,.05)">';
            echo '<span style="display:inline-block;padding:5px 9px;border-radius:999px;background:#f1f5f9;color:#475569;font-size:11px;font-weight:700">' . esc_html( $template['tone'] ) . '</span>';
            echo '<h2 style="margin:14px 0 8px">' . esc_html( $template['title'] ) . '</h2>';
            echo '<p style="min-height:54px;color:#64748b">' . esc_html( $template['desc'] ) . '</p>';
            echo '<a class="button button-primary" href="' . esc_url( $url ) . '">Bu Şablonla Portföy Oluştur</a>';
            echo '</article>';
        }

        echo '</div></div>';
    }

    public static function template_meta_box( $post ) {
        if ( ! $post || ! $post->ID ) return;

        $current = get_post_meta( $post->ID, '_wpst_portfolio_template', true );
        $has_elementor = (bool) get_post_meta( $post->ID, '_elementor_data', true );

        echo '<p style="margin-top:0;color:#64748b">Hazır tasarımı uygula, sonra Elementor ile istediğiniz gibi düzenleyin.</p>';
        if ( $has_elementor ) {
            echo '<p><strong>Mevcut Elementor düzeni var.</strong><br><span style="color:#b45309">Başka şablon uygularsanız yalnız Elementor sayfa düzeni değiştirilir.</span></p>';
        }

        foreach ( self::templates() as $key => $template ) {
            $url = wp_nonce_url(
                admin_url( 'admin-post.php?action=wpst_apply_portfolio_template&post_id=' . $post->ID . '&template=' . rawurlencode( $key ) ),
                'wpst_apply_portfolio_' . $post->ID . '_' . $key
            );
            echo '<p style="margin:0 0 9px"><a class="button" style="width:100%;text-align:left' . ( $current === $key ? ';border-color:#2271b1;color:#2271b1;font-weight:700' : '' ) . '" onclick="return confirm(\'Bu şablon mevcut Elementor düzeninin üzerine uygulansın mı?\')" href="' . esc_url( $url ) . '">' . esc_html( $template['title'] ) . '</a></p>';
        }

        echo '<hr><p><a class="button button-primary" style="width:100%;text-align:center" href="' . esc_url( admin_url( 'post.php?post=' . $post->ID . '&action=elementor' ) ) . '">Elementor ile Düzenle</a></p>';
    }

    public static function create_from_template() {
        if ( ! current_user_can( 'edit_posts' ) ) wp_die( 'Yetkiniz yok.' );

        $key = isset( $_GET['template'] ) ? sanitize_key( wp_unslash( $_GET['template'] ) ) : 'editorial';
        check_admin_referer( 'wpst_create_portfolio_' . $key );

        $templates = self::templates();
        if ( ! isset( $templates[ $key ] ) ) wp_die( 'Şablon bulunamadı.' );

        $post_id = wp_insert_post( array(
            'post_type'   => self::POST_TYPE,
            'post_status' => 'draft',
            'post_title'  => 'Yeni Portföy · ' . $templates[ $key ]['title'],
        ) );

        if ( is_wp_error( $post_id ) || ! $post_id ) wp_die( 'Portföy oluşturulamadı.' );

        self::write_elementor_template( $post_id, $key );

        wp_safe_redirect( admin_url( 'post.php?post=' . $post_id . '&action=elementor' ) );
        exit;
    }

    public static function apply_template() {
        $post_id = isset( $_GET['post_id'] ) ? absint( $_GET['post_id'] ) : 0;
        $key = isset( $_GET['template'] ) ? sanitize_key( wp_unslash( $_GET['template'] ) ) : '';

        if ( ! $post_id || ! current_user_can( 'edit_post', $post_id ) ) wp_die( 'Yetkiniz yok.' );
        if ( self::POST_TYPE !== get_post_type( $post_id ) ) wp_die( 'Geçersiz portföy.' );

        check_admin_referer( 'wpst_apply_portfolio_' . $post_id . '_' . $key );

        if ( ! isset( self::templates()[ $key ] ) ) wp_die( 'Şablon bulunamadı.' );

        self::write_elementor_template( $post_id, $key );

        wp_safe_redirect( admin_url( 'post.php?post=' . $post_id . '&action=elementor' ) );
        exit;
    }

    public static function activate() {
        self::register_content_types();
        flush_rewrite_rules();
    }

    public static function title_placeholder( $title, $post ) {
        if ( $post && self::POST_TYPE === $post->post_type ) return 'Proje başlığını yazın';
        return $title;
    }

    public static function add_meta_boxes() {
        add_meta_box(
            'wpst_portfolio_details',
            'WPSoft · Proje Bilgileri',
            array( __CLASS__, 'meta_box' ),
            self::POST_TYPE,
            'normal',
            'high'
        );
    }

    public static function meta_box( $post ) {
        wp_nonce_field( 'wpst_portfolio_save', 'wpst_portfolio_nonce' );
        $url = get_post_meta( $post->ID, self::META_URL, true );
        ?>
        <p style="color:#64748b">
            Portfolio 2.0 widgetı bu projeyi otomatik olarak çekebilir.
            Başlık, açıklama, proje görseli ve kategoriyi standart WordPress alanlarından yönetin.
        </p>
        <p>
            <label for="wpst_portfolio_url"><strong>Proje / Detay Bağlantısı</strong></label>
            <input type="url" class="widefat" id="wpst_portfolio_url" name="wpst_portfolio_url"
                   value="<?php echo esc_attr( $url ); ?>"
                   placeholder="https://ornek.com/proje">
            <span class="description">Boş bırakılırsa projenin kendi WordPress detay sayfası kullanılır.</span>
        </p>
        <?php
    }

    public static function save_meta( $post_id ) {
        if ( ! isset( $_POST['wpst_portfolio_nonce'] ) ) return;
        $nonce = sanitize_text_field( wp_unslash( $_POST['wpst_portfolio_nonce'] ) );
        if ( ! wp_verify_nonce( $nonce, 'wpst_portfolio_save' ) ) return;
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
        if ( ! current_user_can( 'edit_post', $post_id ) ) return;

        $url = isset( $_POST['wpst_portfolio_url'] )
            ? esc_url_raw( wp_unslash( $_POST['wpst_portfolio_url'] ) )
            : '';

        if ( $url ) update_post_meta( $post_id, self::META_URL, $url );
        else delete_post_meta( $post_id, self::META_URL );
    }

    public static function columns( $columns ) {
        $result = array();
        foreach ( $columns as $key => $label ) {
            if ( 'title' === $key ) $result['wpst_thumb'] = 'Görsel';
            $result[$key] = $label;
            if ( 'title' === $key ) $result['wpst_project_url'] = 'Proje Linki';
        }
        return $result;
    }

    public static function column_content( $column, $post_id ) {
        if ( 'wpst_thumb' === $column ) {
            if ( has_post_thumbnail( $post_id ) ) {
                echo get_the_post_thumbnail(
                    $post_id,
                    array( 64, 48 ),
                    array( 'style' => 'width:64px;height:48px;object-fit:cover;border-radius:8px' )
                );
            } else {
                echo '<span style="color:#94a3b8">—</span>';
            }
        }

        if ( 'wpst_project_url' === $column ) {
            $url = get_post_meta( $post_id, self::META_URL, true );
            if ( $url ) {
                echo '<a href="' . esc_url( $url ) . '" target="_blank" rel="noopener">Aç ↗</a>';
            } else {
                echo '<span style="color:#94a3b8">Detay sayfası</span>';
            }
        }
    }

    public static function category_options() {
        $options = array( '' => 'Tüm Kategoriler' );
        $terms = get_terms( array(
            'taxonomy'   => self::TAXONOMY,
            'hide_empty' => false,
        ) );

        if ( ! is_wp_error( $terms ) ) {
            foreach ( $terms as $term ) {
                $options[ (string) $term->term_id ] = $term->name;
            }
        }
        return $options;
    }
}
