<?php
if ( ! defined( 'ABSPATH' ) ) exit;

final class WPST_Elementor {
    private static $initialized = false;

    public static function init() {
        if ( self::$initialized ) return;
        self::$initialized = true;
        add_action( 'elementor/elements/categories_registered', array( __CLASS__, 'category' ) );
        add_action( 'elementor/widgets/register', array( __CLASS__, 'widgets' ) );
        add_action( 'elementor/frontend/after_enqueue_styles', array( __CLASS__, 'styles' ) );
        add_action( 'elementor/frontend/after_enqueue_scripts', array( __CLASS__, 'scripts' ) );
        add_action( 'elementor/preview/enqueue_scripts', array( __CLASS__, 'scripts' ), 20 );
        add_action( 'elementor/editor/after_enqueue_styles', array( __CLASS__, 'styles' ) );
        add_action( 'elementor/editor/after_enqueue_scripts', array( __CLASS__, 'editor_library_scripts' ) );
        add_action( 'elementor/editor/after_enqueue_styles', array( __CLASS__, 'editor_library_styles' ) );
        // Elementor preview iframe: load the launcher inside the actual canvas.
        add_action( 'elementor/preview/enqueue_scripts', array( __CLASS__, 'preview_launcher_scripts' ) );
        add_action( 'elementor/preview/enqueue_styles', array( __CLASS__, 'preview_launcher_styles' ) );
        // Fallback for Elementor versions where preview hooks are not fired consistently.
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'preview_fallback_assets' ), 99 );
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'frontend_scripts_fallback' ), 20 );
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'editor_fallback_assets' ), 99 );
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'force_editor_bootstrap' ), 999 );
        add_action( 'admin_footer', array( __CLASS__, 'editor_inline_fallback' ), 999 );
        add_action( 'admin_init', array( __CLASS__, 'refresh_widget_cache' ), 30 );
    }
    public static function refresh_widget_cache() {
        $cache_version = get_option( 'wpst_elementor_widget_cache_version', '' );
        if ( WPST_VERSION === $cache_version ) return;

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

        update_option( 'wpst_elementor_widget_cache_version', WPST_VERSION, false );
    }

    private static function library_enabled() {
        if ( class_exists('WPST_License') && ! WPST_License::is_active() ) return false;
        $settings = get_option( 'wpst_settings', array() );
        return ! isset( $settings['elementor_library_enabled'] ) || ! empty( $settings['elementor_library_enabled'] );
    }

    public static function category( $elements_manager ) {
        $categories = array(
            'wpsoft-creative'   => array('title'=>'WPSoft · Creative','icon'=>'fa fa-magic'),
            'wpsoft-content'    => array('title'=>'WPSoft · Content','icon'=>'fa fa-font'),
            'wpsoft-marketing'  => array('title'=>'WPSoft · Marketing','icon'=>'fa fa-bullhorn'),
            'wpsoft-business'   => array('title'=>'WPSoft · Business','icon'=>'fa fa-briefcase'),
            'wpsoft-media'      => array('title'=>'WPSoft · Media','icon'=>'fa fa-image'),
            'wpsoft-navigation' => array('title'=>'WPSoft · Navigation','icon'=>'fa fa-bars'),
            'wpsoft-dynamic'    => array('title'=>'WPSoft · Dynamic','icon'=>'fa fa-database'),
            'wpsoft-portfolio'  => array('title'=>'WPSoft · Portfolio','icon'=>'fa fa-briefcase'),
            'wpsoft'            => array('title'=>'WPSoft · Diğer','icon'=>'fa fa-plug'),
        );
        foreach($categories as $slug=>$args) $elements_manager->add_category($slug,$args);
    }
    public static function widgets( $widgets_manager ) {
        $files = array(
            'class-wpst-widget-base.php', 'class-wpst-widget-site-logo.php', 'class-wpst-widget-navigation.php', 'class-wpst-widget-native-icon.php', 'class-wpst-widget-svg-shape.php', 'class-wpst-widget-blog.php', 'class-wpst-widget-blog-posts.php', 'class-wpst-widget-heading.php', 'class-wpst-widget-icon-box.php',
            'class-wpst-widget-cta.php', 'class-wpst-widget-faq.php', 'class-wpst-widget-image-text.php', 'class-wpst-widget-pricing.php',
            'class-wpst-widget-portfolio.php', 'class-wpst-widget-portfolio-dynamic.php', 'class-wpst-widget-gallery-zoom-pro.php', 'class-wpst-widget-video-gallery-pro.php', 'class-wpst-widget-video-background-pro.php', 'class-wpst-widget-media-card-pro.php', 'class-wpst-widget-breadcrumb.php',
            'class-wpst-widget-feature-list.php',
            'class-wpst-widget-contact-cards.php',
            'class-wpst-widget-wpforms.php',
            'class-wpst-widget-stats-grid.php',
            'class-wpst-widget-badge-grid.php',
            'class-wpst-widget-quote.php',
            'class-wpst-widget-logo-cloud.php',
            'class-wpst-widget-icon-list.php',
            'class-wpst-widget-icon-grid.php',
            'class-wpst-widget-icon-steps.php',
            'class-wpst-widget-floating-icons.php',
            'class-wpst-widget-number-cards.php',
            'class-wpst-widget-info-strip.php',
            'class-wpst-widget-hero-slider.php','class-wpst-widget-image-carousel.php','class-wpst-widget-image-slider.php','class-wpst-widget-testimonial-slider.php','class-wpst-widget-logo-marquee.php','class-wpst-widget-tabs-modern.php','class-wpst-widget-before-after.php','class-wpst-widget-video-hero.php','class-wpst-widget-card-carousel.php',
            'class-wpst-widget-animated-heading.php',
            'class-wpst-widget-animated-counter.php',
            'class-wpst-widget-marquee-text.php',
            'class-wpst-widget-reveal-cards.php',
            'class-wpst-widget-gradient-heading.php',
            'class-wpst-widget-parallax-image.php',
            'class-wpst-widget-hover-reveal.php',
            'class-wpst-widget-icon-orbit.php',
            'class-wpst-widget-scroll-progress.php',
            'class-wpst-widget-mouse-follow-card.php','class-wpst-widget-hero-split-modern.php','class-wpst-widget-hero-bento.php','class-wpst-widget-hero-saas.php','class-wpst-widget-hero-spotlight.php','class-wpst-widget-image-cascade.php','class-wpst-widget-image-hotspots.php','class-wpst-widget-fancy-box.php','class-wpst-widget-flip-box.php','class-wpst-widget-morphing-cta.php','class-wpst-widget-scroll-reveal-text.php','class-wpst-widget-timeline-modern.php','class-wpst-widget-countdown-modern.php','class-wpst-widget-footer-brand.php','class-wpst-widget-footer-links.php','class-wpst-widget-footer-newsletter.php','class-wpst-widget-footer-social.php','class-wpst-widget-hero-industry.php','class-wpst-widget-hero-hospitality.php','class-wpst-widget-hero-medical.php','class-wpst-widget-hero-commerce.php','class-wpst-widget-service-cards-pro.php','class-wpst-widget-service-carousel-pro.php','class-wpst-widget-process-steps-pro.php','class-wpst-widget-feature-mosaic.php','class-wpst-widget-product-showcase.php','class-wpst-widget-booking-strip.php','class-wpst-widget-trust-badges.php','class-wpst-widget-mega-links.php','class-wpst-widget-mega-promo.php','class-wpst-widget-mega-banner.php','class-wpst-widget-mega-quicknav.php','class-wpst-widget-advanced-accordion.php','class-wpst-widget-team-carousel-pro.php','class-wpst-widget-video-popup-pro.php','class-wpst-widget-logo-grid-pro.php','class-wpst-widget-progress-pro.php','class-wpst-widget-advanced-button.php','class-wpst-widget-glass-card.php','class-wpst-widget-image-reveal.php','class-wpst-widget-modal.php','class-wpst-widget-content-slider.php','class-wpst-widget-advanced-table.php','class-wpst-widget-location-map.php','class-wpst-widget-reviews-pro.php','class-wpst-widget-reviews-carousel.php','class-wpst-widget-story-cards.php','class-wpst-widget-link-grid.php','class-wpst-widget-price-list.php','class-wpst-widget-image-box-pro.php','class-wpst-widget-content-finder.php','class-wpst-widget-expert-profile.php','class-wpst-widget-promo-banner.php','class-wpst-widget-form-shell.php'
        );
        foreach ( $files as $file ) require_once WPST_PATH . 'includes/elementor/widgets/' . $file;
        $classes = array(
            'WPST_Widget_Blog_Posts','WPST_Widget_Post_Reading_Progress','WPST_Widget_Related_Posts','WPST_Widget_Archive_Title','WPST_Widget_Archive_Description','WPST_Widget_Archive_Author','WPST_Widget_Post_Title','WPST_Widget_Post_Content','WPST_Widget_Post_Image','WPST_Widget_Post_Meta','WPST_Widget_Post_Excerpt','WPST_Widget_Post_Author','WPST_Widget_Post_Terms','WPST_Widget_Post_Navigation','WPST_Widget_Post_Share','WPST_Widget_Post_Comments',
            'WPST_Widget_Heading','WPST_Widget_Site_Logo','WPST_Widget_Navigation','WPST_Widget_Native_Icon','WPST_Widget_SVG_Shape',
            'WPST_Widget_Icon_Box','WPST_Widget_CTA','WPST_Widget_FAQ',
            'WPST_Widget_Image_Text','WPST_Widget_Pricing','WPST_Widget_Portfolio','WPST_Widget_Portfolio_Title','WPST_Widget_Portfolio_Excerpt','WPST_Widget_Portfolio_Image','WPST_Widget_Portfolio_Terms','WPST_Widget_Gallery_Zoom_Pro','WPST_Widget_Video_Gallery_Pro','WPST_Widget_Video_Background_Pro','WPST_Widget_Media_Card_Pro','WPST_Widget_Breadcrumb',
            'WPST_Widget_Feature_List',
            'WPST_Widget_Contact_Cards','WPST_Widget_WPForms',
            'WPST_Widget_Stats_Grid',
            'WPST_Widget_Badge_Grid',
            'WPST_Widget_Quote',
            'WPST_Widget_Logo_Cloud',
            'WPST_Widget_Icon_List',
            'WPST_Widget_Icon_Grid',
            'WPST_Widget_Icon_Steps',
            'WPST_Widget_Floating_Icons',
            'WPST_Widget_Number_Cards',
            'WPST_Widget_Info_Strip',
            'WPST_Widget_Hero_Slider','WPST_Widget_Image_Carousel','WPST_Widget_Image_Slider','WPST_Widget_Testimonial_Slider','WPST_Widget_Logo_Marquee','WPST_Widget_Tabs_Modern','WPST_Widget_Before_After','WPST_Widget_Video_Hero','WPST_Widget_Card_Carousel',
            'WPST_Widget_Animated_Heading',
            'WPST_Widget_Animated_Counter',
            'WPST_Widget_Marquee_Text',
            'WPST_Widget_Reveal_Cards',
            'WPST_Widget_Gradient_Heading',
            'WPST_Widget_Parallax_Image',
            'WPST_Widget_Hover_Reveal',
            'WPST_Widget_Icon_Orbit',
            'WPST_Widget_Scroll_Progress',
            'WPST_Widget_Mouse_Follow_Card','WPST_Widget_Hero_Split_Modern','WPST_Widget_Hero_Bento','WPST_Widget_Hero_SaaS','WPST_Widget_Hero_Spotlight','WPST_Widget_Image_Cascade','WPST_Widget_Image_Hotspots','WPST_Widget_Fancy_Box','WPST_Widget_Flip_Box','WPST_Widget_Morphing_CTA','WPST_Widget_Scroll_Reveal_Text','WPST_Widget_Timeline_Modern','WPST_Widget_Countdown_Modern','WPST_Widget_Footer_Brand','WPST_Widget_Footer_Links','WPST_Widget_Footer_Newsletter','WPST_Widget_Footer_Social','WPST_Widget_Hero_Industry','WPST_Widget_Hero_Hospitality','WPST_Widget_Hero_Medical','WPST_Widget_Hero_Commerce','WPST_Widget_Service_Cards_Pro','WPST_Widget_Service_Carousel_Pro','WPST_Widget_Process_Steps_Pro','WPST_Widget_Feature_Mosaic','WPST_Widget_Product_Showcase','WPST_Widget_Booking_Strip','WPST_Widget_Trust_Badges','WPST_Widget_Mega_Links','WPST_Widget_Mega_Promo','WPST_Widget_Mega_Banner','WPST_Widget_Mega_Quicknav','WPST_Widget_Advanced_Accordion','WPST_Widget_Team_Carousel_Pro','WPST_Widget_Video_Popup_Pro','WPST_Widget_Logo_Grid_Pro','WPST_Widget_Progress_Pro','WPST_Widget_Advanced_Button','WPST_Widget_Glass_Card','WPST_Widget_Image_Reveal','WPST_Widget_Modal','WPST_Widget_Content_Slider','WPST_Widget_Advanced_Table','WPST_Widget_Location_Map','WPST_Widget_Reviews_Pro','WPST_Widget_Reviews_Carousel','WPST_Widget_Story_Cards','WPST_Widget_Link_Grid','WPST_Widget_Price_List','WPST_Widget_Image_Box_Pro','WPST_Widget_Content_Finder','WPST_Widget_Expert_Profile','WPST_Widget_Promo_Banner','WPST_Widget_Form_Shell'
        );
        foreach ( $classes as $class ) if ( class_exists( $class ) ) $widgets_manager->register( new $class() );
    }

    public static function editor_library_styles() {
        if ( ! self::library_enabled() ) return;
        wp_enqueue_style( 'wpst-elementor-editor-library', WPST_URL . 'assets/css/elementor-editor-library.css', array(), WPST_VERSION );
    }
    public static function editor_library_scripts() {
        if ( ! self::library_enabled() ) return;
        wp_enqueue_script( 'wpst-elementor-editor-library', WPST_URL . 'assets/js/elementor-editor-library.js', array( 'jquery' ), WPST_VERSION, true );
        $payload = class_exists( 'WPST_Template_Library' ) ? WPST_Template_Library::editor_payload() : array( 'widgets'=>array(), 'sections'=>array(), 'pages'=>array(), 'headers'=>array(), 'footers'=>array(), 'mega_menus'=>array() );
        // Template Library 2.0 can optionally adapt bundled template palette/typography
        // to the site's current WPSoft Design System while inserting.
        $ds = wp_parse_args( get_option( 'wpst_settings', array() ), array(
            'global_primary'=>'#2563eb','global_secondary'=>'#7c3aed','global_heading'=>'#0f172a','global_text'=>'#334155',
            'global_muted'=>'#64748b','global_surface'=>'#ffffff','global_soft'=>'#f8fafc','global_border'=>'#e2e8f0','global_accent'=>'#0ea5e9',
            'global_body_font'=>'system','global_heading_font'=>'system','global_custom_body_font'=>'','global_custom_heading_font'=>''
        ) );
        $payload['design_system'] = array(
            'primary'=>sanitize_hex_color($ds['global_primary']) ?: '#2563eb',
            'secondary'=>sanitize_hex_color($ds['global_secondary']) ?: '#7c3aed',
            'heading'=>sanitize_hex_color($ds['global_heading']) ?: '#0f172a',
            'text'=>sanitize_hex_color($ds['global_text']) ?: '#334155',
            'muted'=>sanitize_hex_color($ds['global_muted']) ?: '#64748b',
            'surface'=>sanitize_hex_color($ds['global_surface']) ?: '#ffffff',
            'soft'=>sanitize_hex_color($ds['global_soft']) ?: '#f8fafc',
            'border'=>sanitize_hex_color($ds['global_border']) ?: '#e2e8f0',
            'accent'=>sanitize_hex_color($ds['global_accent']) ?: '#0ea5e9',
            'body_font'=>sanitize_text_field(self::design_font_name($ds['global_body_font'],$ds['global_custom_body_font'])),
            'heading_font'=>sanitize_text_field(self::design_font_name($ds['global_heading_font'],$ds['global_custom_heading_font']))
        );
        wp_localize_script( 'wpst-elementor-editor-library', 'WPSTEditorLibrary', $payload );
    }
    public static function preview_launcher_scripts() {
        if ( ! self::library_enabled() ) return;
        wp_enqueue_script( 'wpst-elementor-preview-launcher', WPST_URL . 'assets/js/elementor-preview-launcher.js', array(), WPST_VERSION, true );
    }
    public static function preview_launcher_styles() {
        if ( ! self::library_enabled() ) return;
        wp_enqueue_style( 'wpst-elementor-preview-launcher', WPST_URL . 'assets/css/elementor-preview-launcher.css', array(), WPST_VERSION );
    }
    public static function preview_fallback_assets() {
        if ( ! self::library_enabled() ) return;
        $is_preview = isset( $_GET['elementor-preview'] ) || isset( $_GET['elementor_library'] );
        if ( ! $is_preview ) return;
        self::preview_launcher_styles();
        self::preview_launcher_scripts();
    }

    public static function editor_fallback_assets( $hook = '' ) {
        if ( ! is_admin() ) return;
        $action = isset( $_GET['action'] ) ? sanitize_key( wp_unslash( $_GET['action'] ) ) : '';
        if ( 'elementor' !== $action ) return;
        self::editor_library_styles();
        self::editor_library_scripts();
    }

    public static function preview_inline_launcher() { return; }


    public static function force_editor_bootstrap() {
        if ( ! is_admin() || ! self::library_enabled() ) return;
        $action = isset( $_GET['action'] ) ? sanitize_key( wp_unslash( $_GET['action'] ) ) : '';
        if ( 'elementor' !== $action ) return;

        // Load the editor library even if a specific Elementor editor enqueue hook changes.
        self::editor_library_styles();
        self::editor_library_scripts();
    }

    public static function editor_inline_fallback() {
        if ( ! is_admin() || ! self::library_enabled() ) return;
        $action = isset( $_GET['action'] ) ? sanitize_key( wp_unslash( $_GET['action'] ) ) : '';
        if ( 'elementor' !== $action ) return;
        $payload = class_exists( 'WPST_Template_Library' ) ? WPST_Template_Library::editor_payload() : array( 'widgets'=>array(), 'sections'=>array(), 'pages'=>array(), 'headers'=>array(), 'footers'=>array(), 'mega_menus'=>array() );
        ?>
        <script>window.WPSTEditorLibrary = window.WPSTEditorLibrary || <?php echo wp_json_encode( $payload ); ?>;</script>
        <script>
        (function(){
          if(window.__wpstBridgeReady)return;window.__wpstBridgeReady=1;
          window.addEventListener('message',function(ev){var d=ev&&ev.data||{};if(d.type!=='wpst-open-library')return;if(typeof window.WPSTOpenLibrary==='function'){window.WPSTOpenLibrary(d.tab||'widgets');return;}var s=document.getElementById('wpst-editor-fallback-js');if(!s){s=document.createElement('script');s.id='wpst-editor-fallback-js';s.src=<?php echo wp_json_encode( WPST_URL . 'assets/js/elementor-editor-library.js?ver=' . WPST_VERSION ); ?>;document.body.appendChild(s);var l=document.getElementById('wpst-editor-fallback-css');if(!l){l=document.createElement('link');l.id='wpst-editor-fallback-css';l.rel='stylesheet';l.href=<?php echo wp_json_encode( WPST_URL . 'assets/css/elementor-editor-library.css?ver=' . WPST_VERSION ); ?>;document.head.appendChild(l);}setTimeout(function(){if(typeof window.WPSTOpenLibrary==='function')window.WPSTOpenLibrary(d.tab||'widgets');},500);}});
        })();
        </script>
        <?php
    }
    private static function is_editor_context() {
        if ( is_admin() ) return true;
        if ( isset($_GET['elementor-preview']) || isset($_GET['elementor_library']) ) return true;
        return false;
    }

    private static function scanned_post_ids() {
        $ids = array();

        $current = get_queried_object_id();
        if ( $current ) $ids[] = absint($current);

        $settings = get_option('wpst_settings',array());
        foreach ( array('header_template','mobile_header_template','footer_template','mobile_footer_template') as $key ) {
            if ( ! empty($settings[$key]) ) $ids[] = absint($settings[$key]);
        }

        // Conditional Header/Footer templates are runtime candidates too.
        // Include them in the widget asset scan so a matched template never renders unstyled.
        $conditional_hf = get_posts(array(
            'post_type'=>'elementor_library',
            'post_status'=>'publish',
            'posts_per_page'=>-1,
            'fields'=>'ids',
            'no_found_rows'=>true,
            'meta_query'=>array(
                'relation'=>'AND',
                array('key'=>'_wpst_hf_type','compare'=>'EXISTS'),
                array('key'=>'_wpst_display_conditions','compare'=>'EXISTS')
            )
        ));
        foreach((array)$conditional_hf as $conditional_id){
            $rules=get_post_meta($conditional_id,'_wpst_display_conditions',true);
            if(is_array($rules) && !empty($rules)) $ids[]=absint($conditional_id);
        }

        $mega = get_option('wpst_mega_menu',array());
        if ( is_array($mega) ) {
            foreach ( $mega as $cfg ) {
                if ( is_array($cfg) && !empty($cfg['enabled']) && !empty($cfg['template_id']) ) {
                    $ids[] = absint($cfg['template_id']);
                }
            }
        }

        return array_values(array_unique(array_filter($ids)));
    }

    private static function combined_elementor_data() {
        static $data = null;
        if ( null !== $data ) return $data;

        $chunks = array();
        foreach ( self::scanned_post_ids() as $post_id ) {
            $raw = get_post_meta($post_id,'_elementor_data',true);
            if ( is_string($raw) && $raw !== '' ) $chunks[] = $raw;
        }

        $data = implode("\n",$chunks);
        return $data;
    }

    private static function has_wpsoft_widgets() {
        if ( self::is_editor_context() ) return true;
        return false !== strpos(self::combined_elementor_data(),'"widgetType":"wpsoft-');
    }

    private static function design_font_name($key,$custom='') {
        if ('custom'===$key && $custom) return $custom;
        $map=array('system'=>'','inter'=>'Inter','manrope'=>'Manrope','dmsans'=>'DM Sans','plusjakarta'=>'Plus Jakarta Sans','outfit'=>'Outfit','sora'=>'Sora','spacegrotesk'=>'Space Grotesk','urbanist'=>'Urbanist','figtree'=>'Figtree','worksans'=>'Work Sans','nunitosans'=>'Nunito Sans','sourcesans3'=>'Source Sans 3','poppins'=>'Poppins','montserrat'=>'Montserrat','roboto'=>'Roboto','opensans'=>'Open Sans','lato'=>'Lato','playfair'=>'Playfair Display','cormorant'=>'Cormorant Garamond');
        return isset($map[$key])?$map[$key]:'';
    }

    private static function has_interactive_widgets() {
        if ( self::is_editor_context() ) return true;

        $data = self::combined_elementor_data();
        if ( $data === '' ) return false;

        $interactive = array(
            'wpsoft-hero-slider','wpsoft-image-carousel','wpsoft-testimonial-slider',
            'wpsoft-logo-marquee','wpsoft-tabs-modern','wpsoft-before-after',
            'wpsoft-video-hero','wpsoft-card-carousel','wpsoft-animated-heading',
            'wpsoft-animated-counter','wpsoft-marquee-text','wpsoft-reveal-cards',
            'wpsoft-parallax-image','wpsoft-icon-orbit','wpsoft-scroll-progress',
            'wpsoft-mouse-follow-card','wpsoft-hero-spotlight','wpsoft-scroll-reveal-text','wpsoft-countdown-modern',
            'wpsoft-gallery-zoom-pro','wpsoft-video-gallery-pro','wpsoft-video-popup-pro','wpsoft-media-card-pro','wpsoft-image-hotspots','wpsoft-service-carousel-pro','wpsoft-navigation',
            'wpsoft-content-slider','wpsoft-image-reveal','wpsoft-modal','wpsoft-reviews-carousel','wpsoft-advanced-button',
            'wpsoft-image-slider','wpsoft-team-carousel-pro','wpsoft-faq','wpsoft-advanced-accordion'
        );

        foreach ( $interactive as $widget ) {
            if ( false !== strpos($data,'"widgetType":"'.$widget.'"') ) return true;
        }
        return false;
    }

    public static function frontend_scripts_fallback() {
        if ( is_admin() || ! self::has_interactive_widgets() ) return;
        self::scripts();
    }

    public static function styles() {
        if ( ! self::has_wpsoft_widgets() ) return;

        // Do not enqueue only the static defaults here. The saved Global Design
        // values live in an inline :root block generated by WPST_Plugin.
        // Without this call Elementor editor/template previews can fall back to
        // global-design.css defaults and appear to ignore the color system.
        if ( class_exists( 'WPST_Plugin' ) ) {
            WPST_Plugin::instance()->enqueue_global_design_assets();
        } else {
            wp_enqueue_style( 'wpst-global-design', WPST_URL . 'assets/css/global-design.css', array(), WPST_VERSION );
        }

        // Widget CSS is intentionally split by responsibility. Dependency chaining preserves
        // the exact historical cascade order while making future per-widget loading possible.
        $modules = array(
            'wpst-elementor'          => array( 'assets/css/widgets/wpst-widgets-foundation.css', array( 'wpst-global-design' ) ),
            'wpst-widget-framework'   => array( 'assets/css/widgets/wpst-widgets-framework.css', array( 'wpst-elementor' ) ),
            'wpst-widget-media'       => array( 'assets/css/widgets/wpst-widgets-media-motion.css', array( 'wpst-widget-framework' ) ),
            'wpst-widget-signature'   => array( 'assets/css/widgets/wpst-widgets-signature.css', array( 'wpst-widget-media' ) ),
            'wpst-widget-ui'          => array( 'assets/css/widgets/wpst-widgets-ui.css', array( 'wpst-widget-signature' ) ),
            'wpst-widget-interactive' => array( 'assets/css/widgets/wpst-widgets-interactive.css', array( 'wpst-widget-ui' ) ),
        );
        foreach ( $modules as $handle => $asset ) {
            wp_enqueue_style( $handle, WPST_URL . $asset[0], $asset[1], WPST_VERSION );
        }
    }

    public static function scripts() {
        // Register Portfolio 2.0 interaction separately so Elementor's
        // get_script_depends() can enqueue it only when the widget exists.
        wp_register_script(
            'wpst-portfolio-filter',
            WPST_URL . 'assets/js/portfolio-filter.js',
            array(),
            WPST_VERSION,
            true
        );
        if(function_exists('wp_script_add_data')) wp_script_add_data('wpst-portfolio-filter','strategy','defer');

        if ( ! self::has_interactive_widgets() ) return;

        wp_enqueue_script(
            'wpst-elementor-widgets',
            WPST_URL . 'assets/js/elementor-widgets.js',
            array(),
            WPST_VERSION,
            true
        );
        if(function_exists('wp_script_add_data')) wp_script_add_data('wpst-elementor-widgets','strategy','defer');
    }
}
