<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class WPST_Plugin {
    private static $instance = null;
    private $header_rendered = false;
    private $footer_rendered = false;

    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'admin_menu', array( $this, 'register_admin_menu' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        add_action( 'admin_notices', array( $this, 'elementor_notice' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_assets' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'frontend_assets' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'register_elementor_assets' ), 1 );
        add_action( 'wp_body_open', array( $this, 'render_header' ), 5 );
        add_action( 'wp_footer', array( $this, 'render_footer' ), 5 );
        add_action( 'wp_footer', array( $this, 'render_scroll_top' ), 30 );
        add_filter( 'body_class', array( $this, 'body_classes' ) );
        add_shortcode( 'wpsoft_header', array( $this, 'header_shortcode' ) );
        add_shortcode( 'wpsoft_footer', array( $this, 'footer_shortcode' ) );
        add_filter( 'wp_nav_menu_objects', array( $this, 'filter_menu_objects' ), 10, 2 );
    }

    public function register_admin_menu() {
        add_menu_page( 'WPSoft Site Tools', 'WPSoft Site Tools', 'manage_options', 'wpsoft-site-tools', array( $this, 'settings_page' ), 'dashicons-layout', 58 );
    }

    public function register_settings() {
        register_setting( 'wpst_settings_group', 'wpst_settings', array(
            'type' => 'array',
            'sanitize_callback' => array( $this, 'sanitize_settings' ),
            'default' => array(),
        ) );
    }

    public function sanitize_settings( $input ) {
        if ( class_exists('WPST_License') && ! WPST_License::can_edit() ) {
            return get_option( 'wpst_settings', array() );
        }
        $clean = array();
        foreach ( array( 'header', 'footer' ) as $type ) {
            $clean[ $type . '_enabled' ] = ! empty( $input[ $type . '_enabled' ] ) ? 1 : 0;
            $clean[ 'hide_theme_' . $type ] = ! empty( $input[ 'hide_theme_' . $type ] ) ? 1 : 0;
            $clean[ $type . '_template' ] = isset( $input[ $type . '_template' ] ) ? absint( $input[ $type . '_template' ] ) : 0;
            $clean[ $type . '_logo_id' ] = isset( $input[ $type . '_logo_id' ] ) ? absint( $input[ $type . '_logo_id' ] ) : 0;
            $clean[ $type . '_logo_width' ] = isset( $input[ $type . '_logo_width' ] ) ? max( 0, min( 800, absint( $input[ $type . '_logo_width' ] ) ) ) : 0;
            $clean[ $type . '_logo_height' ] = isset( $input[ $type . '_logo_height' ] ) ? max( 0, min( 300, absint( $input[ $type . '_logo_height' ] ) ) ) : 0;
            $clean[ 'mobile_' . $type . '_template' ] = isset( $input[ 'mobile_' . $type . '_template' ] ) ? absint( $input[ 'mobile_' . $type . '_template' ] ) : 0;
            $clean[ $type . '_mode' ] = ( isset( $input[ $type . '_mode' ] ) && 'elementor' === $input[ $type . '_mode' ] ) ? 'elementor' : 'builder';
            if ( 'header' === $type ) {
                $clean['header_builder_version'] = isset($input['header_builder_version']) && 2 === absint($input['header_builder_version']) ? 2 : 1;

                // Hybrid Header device sources must persist independently.
                foreach ( array('desktop','mobile') as $device ) {
                    $source_key = 'header_' . $device . '_source';
                    $source = isset($input[$source_key]) ? sanitize_key($input[$source_key]) : 'inherit';
                    $clean[$source_key] = in_array($source,array('inherit','builder','elementor'),true) ? $source : 'inherit';
                }
            }
            $clean[ $type . '_sections' ] = isset( $input[ $type . '_sections' ] ) ? max( 1, min( 4, absint( $input[ $type . '_sections' ] ) ) ) : 3;
            $clean[ $type . '_layout' ] = $this->sanitize_layout( isset( $input[ $type . '_layout' ] ) ? $input[ $type . '_layout' ] : '[]' );
            $clean[ $type . '_background' ] = isset( $input[ $type . '_background' ] ) ? sanitize_hex_color( $input[ $type . '_background' ] ) : '#ffffff';
            $clean[ $type . '_text_color' ] = isset( $input[ $type . '_text_color' ] ) ? sanitize_hex_color( $input[ $type . '_text_color' ] ) : '#111827';
            $clean[ $type . '_container' ] = isset( $input[ $type . '_container' ] ) ? max( 960, min( 1600, absint( $input[ $type . '_container' ] ) ) ) : 1200;
            $clean[ $type . '_padding' ] = isset( $input[ $type . '_padding' ] ) ? max( 0, min( 100, absint( $input[ $type . '_padding' ] ) ) ) : 18;
            $clean[ 'custom_' . $type . '_selectors' ] = isset( $input[ 'custom_' . $type . '_selectors' ] ) ? sanitize_textarea_field( $input[ 'custom_' . $type . '_selectors' ] ) : '';
        }
        $clean['header_sticky'] = ! empty( $input['header_sticky'] ) ? 1 : 0;
        $clean['header_sticky_mode'] = ( isset( $input['header_sticky_mode'] ) && 'scroll' === $input['header_sticky_mode'] ) ? 'scroll' : 'always';
        $clean['header_sticky_top'] = ! empty( $input['header_sticky_top'] ) ? 1 : 0;
        $clean['header_sticky_main'] = ! empty( $input['header_sticky_main'] ) ? 1 : 0;
        $clean['header_sticky_bottom'] = ! empty( $input['header_sticky_bottom'] ) ? 1 : 0;
        if ( empty($clean['header_sticky_top']) && empty($clean['header_sticky_main']) && empty($clean['header_sticky_bottom']) ) $clean['header_sticky_main'] = 1;
        $clean['header_shrink'] = ! empty( $input['header_shrink'] ) ? 1 : 0;
        $clean['header_shadow'] = ! empty( $input['header_shadow'] ) ? 1 : 0; // legacy
        $shadow_style = isset($input['header_shadow_style']) ? sanitize_key($input['header_shadow_style']) : '';
        if ( ! in_array($shadow_style,array('normal','soft','medium','strong'),true) ) {
            if ( !empty($input['header_shadow']) ) $shadow_style = 'soft';
            elseif ( isset($input['header_boxed_shadow']) && in_array($input['header_boxed_shadow'],array('soft','medium','strong'),true) ) $shadow_style = sanitize_key($input['header_boxed_shadow']);
            else $shadow_style = 'normal';
        }
        $clean['header_shadow_style'] = $shadow_style;
        $clean['header_mobile_breakpoint'] = isset( $input['header_mobile_breakpoint'] ) ? max( 480, min( 1200, absint( $input['header_mobile_breakpoint'] ) ) ) : 768;
        $clean['header_scrolled_logo_id'] = isset( $input['header_scrolled_logo_id'] ) ? absint( $input['header_scrolled_logo_id'] ) : 0;
        $clean['header_scrolled_logo_width'] = isset($input['header_scrolled_logo_width']) ? max(0,min(800,absint($input['header_scrolled_logo_width']))) : 0;
        $clean['header_scrolled_logo_height'] = isset($input['header_scrolled_logo_height']) ? max(0,min(300,absint($input['header_scrolled_logo_height']))) : 44;
        $clean['header_glass_style'] = isset($input['header_glass_style']) && in_array($input['header_glass_style'], array('off','soft','strong','dark'), true) ? $input['header_glass_style'] : 'off';
        $clean['header_row_top_enabled'] = ! empty($input['header_row_top_enabled']) ? 1 : 0;
        $clean['header_row_bottom_enabled'] = ! empty($input['header_row_bottom_enabled']) ? 1 : 0;
        foreach ( array( 'top', 'main', 'bottom' ) as $row ) {
            $prefix = 'header_row_' . $row . '_';
            $clean[ $prefix . 'height_desktop' ] = isset( $input[ $prefix . 'height_desktop' ] ) ? max( 24, min( 160, absint( $input[ $prefix . 'height_desktop' ] ) ) ) : ( 'main' === $row ? 78 : 38 );
            $clean[ $prefix . 'height_tablet' ]  = isset( $input[ $prefix . 'height_tablet' ] ) ? max( 24, min( 140, absint( $input[ $prefix . 'height_tablet' ] ) ) ) : ( 'main' === $row ? 70 : 36 );
            $clean[ $prefix . 'height_mobile' ]  = isset( $input[ $prefix . 'height_mobile' ] ) ? max( 24, min( 120, absint( $input[ $prefix . 'height_mobile' ] ) ) ) : ( 'main' === $row ? 64 : 34 );
            $clean[ $prefix . 'height_scrolled' ] = isset( $input[ $prefix . 'height_scrolled' ] ) ? max( 24, min( 140, absint( $input[ $prefix . 'height_scrolled' ] ) ) ) : ( 'main' === $row ? 62 : 32 );
            $bg = isset( $input[ $prefix . 'background' ] ) ? sanitize_hex_color( $input[ $prefix . 'background' ] ) : '';
            $text = isset( $input[ $prefix . 'text_color' ] ) ? sanitize_hex_color( $input[ $prefix . 'text_color' ] ) : '';
            $border = isset( $input[ $prefix . 'border_color' ] ) ? sanitize_hex_color( $input[ $prefix . 'border_color' ] ) : '';
            $clean[ $prefix . 'background' ] = $bg ? $bg : ( 'main' === $row ? '#ffffff' : '#f8fafc' );
            $clean[ $prefix . 'text_color' ] = $text ? $text : '#111827';
            $clean[ $prefix . 'border_color' ] = $border ? $border : '#e5e7eb';
            $clean[ $prefix . 'border_width' ] = isset( $input[ $prefix . 'border_width' ] ) ? max( 0, min( 4, absint( $input[ $prefix . 'border_width' ] ) ) ) : ( 'bottom' === $row ? 0 : 1 );
            $clean[ $prefix . 'container' ] = isset( $input[ $prefix . 'container' ] ) ? max( 720, min( 1920, absint( $input[ $prefix . 'container' ] ) ) ) : 1200;
            $clean[ $prefix . 'full_width' ] = ! empty( $input[ $prefix . 'full_width' ] ) ? 1 : 0;
            foreach ( array( 'desktop', 'tablet', 'mobile' ) as $device ) {
                $clean[ $prefix . 'show_' . $device ] = ! empty( $input[ $prefix . 'show_' . $device ] ) ? 1 : 0;
            }
        }
        $clean['button_background'] = isset( $input['button_background'] ) ? sanitize_hex_color( $input['button_background'] ) : '#2563eb';
        $clean['button_radius'] = isset( $input['button_radius'] ) ? max( 0, min( 50, absint( $input['button_radius'] ) ) ) : 10;
        $clean['elementor_library_enabled'] = ! empty( $input['elementor_library_enabled'] ) ? 1 : 0;
        $clean['blog_archive_enabled'] = ! empty( $input['blog_archive_enabled'] ) ? 1 : 0;
        $clean['blog_archive_template'] = isset($input['blog_archive_template']) ? absint($input['blog_archive_template']) : 0;
        $clean['blog_page_id'] = isset($input['blog_page_id']) ? absint($input['blog_page_id']) : 0;
        $clean['blog_single_enabled'] = ! empty( $input['blog_single_enabled'] ) ? 1 : 0;
        $clean['blog_single_template'] = isset($input['blog_single_template']) ? absint($input['blog_single_template']) : 0;
        foreach(array('404','search','category','tag','author') as $tb){
            $clean['theme_'.$tb.'_enabled'] = !empty($input['theme_'.$tb.'_enabled']) ? 1 : 0;
            $clean['theme_'.$tb.'_template'] = isset($input['theme_'.$tb.'_template']) ? absint($input['theme_'.$tb.'_template']) : 0;
        }

        // Global Design System
        $global_colors = array(
            'global_primary'   => '#2563eb',
            'global_secondary' => '#7c3aed',
            'global_heading'   => '#0f172a',
            'global_text'      => '#334155',
            'global_muted'     => '#64748b',
            'global_surface'   => '#ffffff',
            'global_page_bg'   => '#ffffff',
            'global_soft'      => '#f8fafc',
            'global_border'    => '#e2e8f0',
            'global_accent'    => '#0ea5e9',
            'global_success'   => '#16a34a',
            'global_warning'   => '#f59e0b',
            'global_danger'    => '#dc2626',
            'global_button_bg' => '#2563eb',
            'global_button_text' => '#ffffff',
            'global_button_hover_bg' => '#1d4ed8',
            'global_button_hover_text' => '#ffffff',
            'global_secondary_button_bg' => '#ffffff',
            'global_secondary_button_text' => '#0f172a',
            'global_secondary_button_border' => '#cbd5e1',
            'global_secondary_button_hover_bg' => '#f8fafc',
            'global_secondary_button_hover_text' => '#0f172a',
            'global_link' => '#2563eb',
            'global_link_hover' => '#1d4ed8',
            'global_input_bg' => '#ffffff',
            'global_input_text' => '#0f172a',
            'global_input_border' => '#cbd5e1',
            'global_input_focus' => '#2563eb',
            'global_surface_alt' => '#f8fafc',
            'global_surface_dark' => '#0f172a',
        );
        foreach ( $global_colors as $key => $fallback ) {
            $value = isset( $input[$key] ) ? sanitize_hex_color( $input[$key] ) : '';
            $clean[$key] = $value ? $value : $fallback;
        }
        $clean['global_apply_page_bg'] = ! empty( $input['global_apply_page_bg'] ) ? 1 : 0;

        $clean['global_container'] = isset( $input['global_container'] ) ? max( 960, min( 1600, absint( $input['global_container'] ) ) ) : 1200;
        $clean['global_container_narrow'] = isset( $input['global_container_narrow'] ) ? max( 640, min( 1100, absint( $input['global_container_narrow'] ) ) ) : 860;
        $clean['global_container_wide'] = isset( $input['global_container_wide'] ) ? max( 1200, min( 1920, absint( $input['global_container_wide'] ) ) ) : 1440;
        $clean['global_section_space'] = isset( $input['global_section_space'] ) ? max( 20, min( 180, absint( $input['global_section_space'] ) ) ) : 80;
        $clean['global_section_space_tablet'] = isset( $input['global_section_space_tablet'] ) ? max( 16, min( 140, absint( $input['global_section_space_tablet'] ) ) ) : 60;
        $clean['global_section_space_mobile'] = isset( $input['global_section_space_mobile'] ) ? max( 12, min( 100, absint( $input['global_section_space_mobile'] ) ) ) : 40;
        $clean['global_gap'] = isset( $input['global_gap'] ) ? max( 8, min( 80, absint( $input['global_gap'] ) ) ) : 24;
        foreach ( array( 'xs'=>8, 'sm'=>12, 'md'=>20, 'lg'=>32, 'xl'=>48, 'xxl'=>72 ) as $space_key => $space_default ) {
            $field = 'global_space_' . $space_key;
            $clean[$field] = isset( $input[$field] ) ? max( 0, min( 160, absint( $input[$field] ) ) ) : $space_default;
        }
        $clean['global_radius_sm'] = isset( $input['global_radius_sm'] ) ? max( 0, min( 32, absint( $input['global_radius_sm'] ) ) ) : 8;
        $clean['global_radius_md'] = isset( $input['global_radius_md'] ) ? max( 0, min( 48, absint( $input['global_radius_md'] ) ) ) : 14;
        $clean['global_radius_lg'] = isset( $input['global_radius_lg'] ) ? max( 0, min( 64, absint( $input['global_radius_lg'] ) ) ) : 20;
        $clean['global_radius_xl'] = isset( $input['global_radius_xl'] ) ? max( 0, min( 96, absint( $input['global_radius_xl'] ) ) ) : 30;
        $clean['global_card_radius'] = isset( $input['global_card_radius'] ) ? max( 0, min( 60, absint( $input['global_card_radius'] ) ) ) : 20;
        $clean['global_button_radius'] = isset( $input['global_button_radius'] ) ? max( 0, min( 50, absint( $input['global_button_radius'] ) ) ) : 12;
        $clean['global_button_height'] = isset( $input['global_button_height'] ) ? max( 36, min( 72, absint( $input['global_button_height'] ) ) ) : 48;
        $clean['global_shadow'] = isset( $input['global_shadow'] ) && in_array( $input['global_shadow'], array( 'none', 'soft', 'medium', 'strong' ), true ) ? $input['global_shadow'] : 'soft';
        $clean['global_motion'] = isset( $input['global_motion'] ) && in_array( $input['global_motion'], array( 'off', 'soft', 'normal', 'dynamic' ), true ) ? $input['global_motion'] : 'normal';
        $preset_choices = array('custom','modern','minimal','corporate','creative','luxury','dark');
        $clean['global_preset'] = isset($input['global_preset']) && in_array($input['global_preset'],$preset_choices,true) ? $input['global_preset'] : 'modern';
        $widget_quick_choices = array('auto','signature','corporate','editorial','soft','dark','minimal');
        $clean['global_widget_quick_preset'] = isset($input['global_widget_quick_preset']) && in_array($input['global_widget_quick_preset'],$widget_quick_choices,true) ? $input['global_widget_quick_preset'] : 'auto';
        $mobile_menu_presets=array('corporate-modern','minimal-light','luxury','creative-gradient','e-commerce','hotel-tourism','professional-dark','classic-clean');
        $clean['global_mobile_menu_preset']=isset($input['global_mobile_menu_preset']) && in_array($input['global_mobile_menu_preset'],$mobile_menu_presets,true) ? $input['global_mobile_menu_preset'] : 'corporate-modern';
        foreach(array('global_mobile_panel_background','global_mobile_item_background','global_mobile_text_color','global_mobile_active_background','global_mobile_cta_background','global_mobile_icon_background') as $key)$clean[$key]=isset($input[$key])?sanitize_text_field($input[$key]):'';
        foreach(array('global_mobile_panel_padding','global_mobile_item_radius','global_mobile_item_height','global_mobile_item_gap','global_mobile_icon_box_size','global_mobile_cta_radius','global_mobile_text_size') as $key)$clean[$key]=isset($input[$key]) && ''!==$input[$key]?max(0,min(120,absint($input[$key]))):'';
        $clean['global_mobile_logo_position']=isset($input['global_mobile_logo_position']) && in_array($input['global_mobile_logo_position'],array('','flex-start','center','flex-end'),true)?$input['global_mobile_logo_position']:'';
        $widget_button_choices = array('auto','primary','secondary','emerald','sunset','dark','light','outline','soft','gradient','glass');
        $legacy_widget_button_map = array('modern'=>'primary','pill'=>'primary','minimal'=>'light');
        $widget_button_value = isset($input['global_widget_button_style']) ? $input['global_widget_button_style'] : 'auto';
        if ( isset($legacy_widget_button_map[$widget_button_value]) ) $widget_button_value = $legacy_widget_button_map[$widget_button_value];
        $clean['global_widget_button_style'] = in_array($widget_button_value,$widget_button_choices,true) ? $widget_button_value : 'auto';
        $clean['global_content_width_mode'] = isset($input['global_content_width_mode']) && in_array($input['global_content_width_mode'],array('narrow','standard','wide'),true) ? $input['global_content_width_mode'] : 'standard';
        $clean['global_base_font_size_tablet'] = isset($input['global_base_font_size_tablet']) ? max(12,min(22,absint($input['global_base_font_size_tablet']))) : 16;
        $clean['global_base_font_size_mobile'] = isset($input['global_base_font_size_mobile']) ? max(12,min(22,absint($input['global_base_font_size_mobile']))) : 15;
        $clean['global_button_padding_x'] = isset($input['global_button_padding_x']) ? max(8,min(64,absint($input['global_button_padding_x']))) : 24;
        foreach(array('global_secondary_button_hover_bg'=>'#f8fafc','global_secondary_button_hover_text'=>'#0f172a') as $key=>$fallback){ $value=isset($input[$key])?sanitize_hex_color($input[$key]):''; $clean[$key]=$value?:$fallback; }
        foreach(array('global_link'=>'#2563eb','global_link_hover'=>'#1d4ed8','global_input_bg'=>'#ffffff','global_input_text'=>'#0f172a','global_input_border'=>'#cbd5e1','global_input_focus'=>'#2563eb','global_surface_alt'=>'#f8fafc','global_surface_dark'=>'#0f172a') as $key=>$fallback){ $value=isset($input[$key])?sanitize_hex_color($input[$key]):''; $clean[$key]=$value?:$fallback; }
        $font_choices=array('system','inter','manrope','dmsans','plusjakarta','outfit','sora','spacegrotesk','urbanist','figtree','worksans','nunitosans','sourcesans3','poppins','montserrat','roboto','opensans','lato','playfair','cormorant','custom');
        $clean['global_body_font']=isset($input['global_body_font']) && in_array($input['global_body_font'],$font_choices,true) ? $input['global_body_font'] : 'system';
        $clean['global_heading_font']=isset($input['global_heading_font']) && in_array($input['global_heading_font'],$font_choices,true) ? $input['global_heading_font'] : 'system';
        $clean['global_custom_body_font']=isset($input['global_custom_body_font']) ? sanitize_text_field($input['global_custom_body_font']) : '';
        $clean['global_custom_heading_font']=isset($input['global_custom_heading_font']) ? sanitize_text_field($input['global_custom_heading_font']) : '';
        $clean['global_google_fonts']=isset($input['global_google_fonts']) ? (!empty($input['global_google_fonts']) ? 1 : 0) : 1;
        $clean['global_link_underline']=!empty($input['global_link_underline'])?1:0;
        $clean['global_link_hover_underline']=!empty($input['global_link_hover_underline'])?1:0;
        $clean['global_input_height']=isset($input['global_input_height']) ? max(36,min(72,absint($input['global_input_height']))) : 48;
        $clean['global_input_radius']=isset($input['global_input_radius']) ? max(0,min(40,absint($input['global_input_radius']))) : 10;
        $clean['global_input_border_width']=isset($input['global_input_border_width']) ? max(0,min(4,absint($input['global_input_border_width']))) : 1;

        foreach ( array( 'header_transparent','header_transparent_home_only','header_transparent_overlay','header_scroll_solid','header_blur','header_scroll_shadow','header_topbar_enabled','header_announcement_enabled','header_mobile_overlay' ) as $key ) $clean[$key] = ! empty( $input[$key] ) ? 1 : 0;
        $clean['header_blur_amount'] = isset($input['header_blur_amount']) ? max(0,min(40,absint($input['header_blur_amount']))) : 16;
        $clean['header_scroll_threshold'] = isset($input['header_scroll_threshold']) ? max(0,min(500,absint($input['header_scroll_threshold']))) : 60;
        foreach ( array('header_topbar_text','header_topbar_link_text','header_announcement_text','header_announcement_link_text','header_mobile_close_text') as $key ) $clean[$key] = isset($input[$key]) ? sanitize_text_field($input[$key]) : '';
        foreach ( array('header_topbar_link_url','header_announcement_link_url') as $key ) $clean[$key] = isset($input[$key]) ? esc_url_raw($input[$key]) : '';
        $clean['header_mobile_drawer_side'] = isset($input['header_mobile_drawer_side']) && 'left' === $input['header_mobile_drawer_side'] ? 'left' : 'right';
        foreach(array('header_hide_on_scroll','header_search_enabled','header_account_enabled','header_cart_enabled','header_mobile_bottom_nav') as $key) $clean[$key]=!empty($input[$key])?1:0;
        $clean['header_hide_scroll_delta']=isset($input['header_hide_scroll_delta'])?max(2,min(80,absint($input['header_hide_scroll_delta']))):12;
        $clean['header_search_placeholder']=isset($input['header_search_placeholder'])?sanitize_text_field($input['header_search_placeholder']):'Sitede ara...';
        $clean['header_account_url']=isset($input['header_account_url'])?esc_url_raw($input['header_account_url']):'';
        $clean['header_cart_url']=isset($input['header_cart_url'])?esc_url_raw($input['header_cart_url']):'';

        // Header Builder 3.0
        $header_presets=array('custom','minimal','centered','transparent','floating-boxed','corporate','saas','luxury','ecommerce','hotel','creative');
        $clean['header_preset']=isset($input['header_preset']) && in_array($input['header_preset'],$header_presets,true) ? $input['header_preset'] : 'custom';
        $clean['header_layout_style']=isset($input['header_layout_style']) && in_array($input['header_layout_style'],array('normal','boxed'),true) ? $input['header_layout_style'] : 'normal';
        $clean['header_boxed_width']=isset($input['header_boxed_width']) ? max(720,min(1920,absint($input['header_boxed_width']))) : 1260;
        $clean['header_boxed_top']=isset($input['header_boxed_top']) ? max(0,min(80,absint($input['header_boxed_top']))) : 16;
        $clean['header_boxed_side']=isset($input['header_boxed_side']) ? max(0,min(80,absint($input['header_boxed_side']))) : 24;
        $clean['header_boxed_radius']=isset($input['header_boxed_radius']) ? max(0,min(60,absint($input['header_boxed_radius']))) : 14;
        $clean['header_boxed_background']=isset($input['header_boxed_background']) ? sanitize_text_field($input['header_boxed_background']) : '#ffffff';
        $clean['header_boxed_border_color']=isset($input['header_boxed_border_color']) ? sanitize_text_field($input['header_boxed_border_color']) : 'rgba(15,23,42,.08)';
        $clean['header_boxed_border_width']=isset($input['header_boxed_border_width']) ? max(0,min(4,absint($input['header_boxed_border_width']))) : 1;
        $shadow_input = isset($clean['header_shadow_style']) ? $clean['header_shadow_style'] : 'normal';
        $clean['header_boxed_shadow'] = $shadow_input; // compatibility mirror
        $clean['header_boxed_mobile']=!empty($input['header_boxed_mobile']) ? 1 : 0;
        $clean['header_desktop_height']=isset($input['header_desktop_height'])?max(56,min(130,absint($input['header_desktop_height']))):78;
        $clean['header_scrolled_height']=isset($input['header_scrolled_height'])?max(48,min(110,absint($input['header_scrolled_height']))):64;
        $clean['header_menu_gap']=isset($input['header_menu_gap'])?max(8,min(64,absint($input['header_menu_gap']))):28;
        $clean['header_menu_hover']=isset($input['header_menu_hover']) && in_array($input['header_menu_hover'],array('none','pill','fade','slide','shadow'),true)?$input['header_menu_hover']:'none';
        $clean['header_menu_active']=isset($input['header_menu_active']) && in_array($input['header_menu_active'],array('none','pill','fade','shadow','border'),true)?$input['header_menu_active']:'shadow';
        $clean['header_menu_active_shadow']=isset($input['header_menu_active_shadow']) && in_array($input['header_menu_active_shadow'],array('soft','medium','strong'),true)?$input['header_menu_active_shadow']:'soft';
        $clean['header_mobile_drawer_width']=isset($input['header_mobile_drawer_width'])?max(280,min(460,absint($input['header_mobile_drawer_width']))):390;
        $mobile_presets=array('classic','centered','compact','cta','commerce');
        $clean['header_mobile_preset']=isset($input['header_mobile_preset']) && in_array($input['header_mobile_preset'],$mobile_presets,true) ? $input['header_mobile_preset'] : 'classic';
        $clean['header_mobile_logo_position']=isset($input['header_mobile_logo_position']) && in_array($input['header_mobile_logo_position'],array('left','center'),true) ? $input['header_mobile_logo_position'] : 'left';
        $clean['header_mobile_logo_width']=isset($input['header_mobile_logo_width'])?max(40,min(320,absint($input['header_mobile_logo_width']))):160;
        $clean['header_mobile_logo_height']=isset($input['header_mobile_logo_height'])?max(20,min(120,absint($input['header_mobile_logo_height']))):44;
        $clean['header_mobile_logo_scroll_width']=isset($input['header_mobile_logo_scroll_width'])?max(40,min(320,absint($input['header_mobile_logo_scroll_width']))):150;
        $clean['header_mobile_logo_scroll_height']=isset($input['header_mobile_logo_scroll_height'])?max(20,min(120,absint($input['header_mobile_logo_scroll_height']))):40;
        foreach(array('header_mobile_search','header_mobile_account','header_mobile_cart','header_mobile_cta_enabled') as $key)$clean[$key]=!empty($input[$key])?1:0;
        $clean['header_mobile_cta_text']=isset($input['header_mobile_cta_text'])?sanitize_text_field($input['header_mobile_cta_text']):'Teklif Al';
        $clean['header_mobile_cta_url']=isset($input['header_mobile_cta_url'])?esc_url_raw($input['header_mobile_cta_url']):'#iletisim';
        foreach(array('header_scrolled_background'=>'#ffffff','header_scrolled_text_color'=>'#111827','header_transparent_text_color'=>'#ffffff','header_announcement_background'=>'#2563eb','header_announcement_text_color'=>'#ffffff') as $key=>$fallback){
            $value=isset($input[$key])?sanitize_hex_color($input[$key]):'';
            $clean[$key]=$value?:$fallback;
        }
        foreach(array('header_announcement_dismissible','header_mobile_contact_enabled') as $key)$clean[$key]=!empty($input[$key])?1:0;
        $clean['header_mobile_contact_title']=isset($input['header_mobile_contact_title'])?sanitize_text_field($input['header_mobile_contact_title']):'Hızlı İletişim';
        $clean['header_mobile_phone']=isset($input['header_mobile_phone'])?sanitize_text_field($input['header_mobile_phone']):'';
        $clean['header_mobile_email']=isset($input['header_mobile_email'])?sanitize_email($input['header_mobile_email']):'';
        $clean['header_mobile_drawer_style']=isset($input['header_mobile_drawer_style']) && in_array($input['header_mobile_drawer_style'],array('clean','soft','dark','glass'),true)?$input['header_mobile_drawer_style']:'clean';
        foreach(array('header_mobile_drawer_logo','header_mobile_social_enabled') as $key)$clean[$key]=!empty($input[$key])?1:0;
        foreach(array('header_mobile_social_instagram','header_mobile_social_facebook','header_mobile_social_youtube','header_mobile_social_linkedin') as $key)$clean[$key]=isset($input[$key])?esc_url_raw($input[$key]):'';


        $clean['scroll_top_enabled'] = !empty($input['scroll_top_enabled']) ? 1 : 0;
        $clean['scroll_top_mobile'] = !empty($input['scroll_top_mobile']) ? 1 : 0;
        $clean['scroll_top_threshold'] = isset($input['scroll_top_threshold']) ? max(100,min(2000,absint($input['scroll_top_threshold']))) : 320;
        $clean['scroll_top_position'] = (isset($input['scroll_top_position']) && 'left'===$input['scroll_top_position']) ? 'left' : 'right';
        $clean['scroll_top_style'] = isset($input['scroll_top_style']) && in_array($input['scroll_top_style'],array('soft','solid','outline','black'),true) ? $input['scroll_top_style'] : 'soft';

        foreach(array('footer_cta_enabled','footer_bottom_enabled','footer_divider','footer_reveal') as $key){
            $clean[$key] = !empty($input[$key]) ? 1 : 0;
        }
        $clean['footer_cta_title'] = isset($input['footer_cta_title']) ? sanitize_text_field($input['footer_cta_title']) : '';
        $clean['footer_cta_text'] = isset($input['footer_cta_text']) ? sanitize_text_field($input['footer_cta_text']) : '';
        $clean['footer_cta_button_text'] = isset($input['footer_cta_button_text']) ? sanitize_text_field($input['footer_cta_button_text']) : '';
        $clean['footer_cta_button_url'] = isset($input['footer_cta_button_url']) ? esc_url_raw($input['footer_cta_button_url']) : '';
        $clean['footer_bottom_text'] = isset($input['footer_bottom_text']) ? sanitize_text_field($input['footer_bottom_text']) : '© {year} {site}. Tüm hakları saklıdır.';
        $clean['footer_bottom_menu'] = isset($input['footer_bottom_menu']) ? absint($input['footer_bottom_menu']) : 0;
        $clean['footer_mobile_columns'] = isset($input['footer_mobile_columns']) ? max(1,min(2,absint($input['footer_mobile_columns']))) : 1;
        $clean['footer_mobile_align'] = isset($input['footer_mobile_align']) && 'center' === $input['footer_mobile_align'] ? 'center' : 'left';
        $clean['footer_reveal_offset'] = isset($input['footer_reveal_offset']) ? max(0,min(240,absint($input['footer_reveal_offset']))) : 80;

        $footer_presets = array('custom','corporate','minimal','dark','glass','shop');
        $clean['footer_preset'] = isset($input['footer_preset']) && in_array($input['footer_preset'],$footer_presets,true) ? $input['footer_preset'] : 'custom';
        foreach(array('footer_newsletter_enabled','footer_contact_enabled','footer_payments_enabled','footer_mobile_accordion') as $key){
            $clean[$key] = !empty($input[$key]) ? 1 : 0;
        }
        $clean['footer_newsletter_title'] = isset($input['footer_newsletter_title']) ? sanitize_text_field($input['footer_newsletter_title']) : 'Güncel kalın';
        $clean['footer_newsletter_text'] = isset($input['footer_newsletter_text']) ? sanitize_text_field($input['footer_newsletter_text']) : 'Yeni içerik ve duyuruları e-posta ile alın.';
        $clean['footer_newsletter_placeholder'] = isset($input['footer_newsletter_placeholder']) ? sanitize_text_field($input['footer_newsletter_placeholder']) : 'E-posta adresiniz';
        $clean['footer_newsletter_button'] = isset($input['footer_newsletter_button']) ? sanitize_text_field($input['footer_newsletter_button']) : 'Abone Ol';
        $clean['footer_newsletter_action'] = isset($input['footer_newsletter_action']) ? esc_url_raw($input['footer_newsletter_action']) : '';
        foreach(array('footer_phone','footer_address','footer_hours') as $key){
            $clean[$key] = isset($input[$key]) ? sanitize_text_field($input[$key]) : '';
        }
        $clean['footer_email'] = isset($input['footer_email']) ? sanitize_email($input['footer_email']) : '';
        $clean['footer_payment_text'] = isset($input['footer_payment_text']) ? sanitize_text_field($input['footer_payment_text']) : 'Güvenli ödeme';
        foreach(array('visa','mastercard','amex','paypal','iyzico') as $brand){
            $clean['footer_payment_'.$brand] = !empty($input['footer_payment_'.$brand]) ? 1 : 0;
        }
        foreach(array('left','center','right') as $zone){
            $clean['footer_mobile_title_'.$zone] = isset($input['footer_mobile_title_'.$zone]) ? sanitize_text_field($input['footer_mobile_title_'.$zone]) : ucfirst($zone);
        }

        // Footer Builder 2.0 - row based layout (Top / Main / Bottom, Left / Center / Right)
        $clean['footer_builder_version'] = 2;
        foreach ( array( 'top', 'main', 'bottom' ) as $row_key ) {
            $rp = 'footer_row_' . $row_key . '_';
            $clean[$rp.'height_desktop'] = isset($input[$rp.'height_desktop']) ? max(24,min(220,absint($input[$rp.'height_desktop']))) : ('main'===$row_key?190:72);
            $clean[$rp.'height_tablet'] = isset($input[$rp.'height_tablet']) ? max(24,min(220,absint($input[$rp.'height_tablet']))) : ('main'===$row_key?170:68);
            $clean[$rp.'height_mobile'] = isset($input[$rp.'height_mobile']) ? max(24,min(260,absint($input[$rp.'height_mobile']))) : ('main'===$row_key?150:64);
            foreach ( array('background'=>'#111827','text_color'=>'#ffffff','border_color'=>'#243047') as $suffix=>$fallback ) {
                $value = isset($input[$rp.$suffix]) ? sanitize_hex_color($input[$rp.$suffix]) : '';
                $clean[$rp.$suffix] = $value ?: $fallback;
            }
            $clean[$rp.'border_width'] = isset($input[$rp.'border_width']) ? max(0,min(4,absint($input[$rp.'border_width']))) : 0;
            $clean[$rp.'container'] = isset($input[$rp.'container']) ? max(720,min(1920,absint($input[$rp.'container']))) : 1200;
            $clean[$rp.'full_width'] = !empty($input[$rp.'full_width']) ? 1 : 0;
            foreach(array('desktop','tablet','mobile') as $device) $clean[$rp.'show_'.$device] = !empty($input[$rp.'show_'.$device]) ? 1 : 0;
        }

        // Global Typography
        $font_choices = array('system','inter','manrope','dmsans','plusjakarta','outfit','sora','spacegrotesk','urbanist','figtree','worksans','nunitosans','sourcesans3','poppins','montserrat','roboto','opensans','lato','playfair','cormorant','custom');

        $clean['global_body_font'] = isset($input['global_body_font']) && in_array($input['global_body_font'],$font_choices,true)
            ? $input['global_body_font'] : 'system';

        $clean['global_heading_font'] = isset($input['global_heading_font']) && in_array($input['global_heading_font'],$font_choices,true)
            ? $input['global_heading_font'] : 'system';

        $clean['global_base_font_size'] = isset($input['global_base_font_size'])
            ? max(13,min(22,absint($input['global_base_font_size']))) : 16;

        $clean['global_body_line_height'] = isset($input['global_body_line_height'])
            ? max(1.2,min(2.2,floatval($input['global_body_line_height']))) : 1.65;

        $clean['global_heading_weight'] = isset($input['global_heading_weight'])
            ? max(400,min(900,absint($input['global_heading_weight']))) : 800;

        $clean['global_heading_line_height'] = isset($input['global_heading_line_height'])
            ? max(.9,min(1.6,floatval($input['global_heading_line_height']))) : 1.10;

        $clean['global_heading_letter_spacing'] = isset($input['global_heading_letter_spacing'])
            ? max(-0.08,min(.12,floatval($input['global_heading_letter_spacing']))) : -0.02;

        $clean['global_h1_size'] = isset($input['global_h1_size'])
            ? max(30,min(96,absint($input['global_h1_size']))) : 56;

        $clean['global_h2_size'] = isset($input['global_h2_size'])
            ? max(24,min(76,absint($input['global_h2_size']))) : 42;

        $clean['global_h3_size'] = isset($input['global_h3_size'])
            ? max(20,min(56,absint($input['global_h3_size']))) : 30;

        $clean['global_h4_size'] = isset($input['global_h4_size'])
            ? max(16,min(42,absint($input['global_h4_size']))) : 22;

        $clean['global_h5_size'] = isset($input['global_h5_size'])
            ? max(14,min(34,absint($input['global_h5_size']))) : 18;

        $clean['global_h6_size'] = isset($input['global_h6_size'])
            ? max(12,min(28,absint($input['global_h6_size']))) : 16;

        foreach(array(
            'global_h1_tablet'=>46,'global_h2_tablet'=>36,'global_h3_tablet'=>27,'global_h4_tablet'=>21,'global_h5_tablet'=>18,'global_h6_tablet'=>16,
            'global_h1_mobile'=>36,'global_h2_mobile'=>30,'global_h3_mobile'=>24,'global_h4_mobile'=>20,'global_h5_mobile'=>17,'global_h6_mobile'=>15
        ) as $key=>$fallback){
            $clean[$key]=isset($input[$key]) ? max(12,min(88,absint($input[$key]))) : $fallback;
        }

        $clean['global_button_weight'] = isset($input['global_button_weight'])
            ? max(400,min(900,absint($input['global_button_weight']))) : 800;

        $clean['global_button_letter_spacing'] = isset($input['global_button_letter_spacing'])
            ? max(-0.04,min(.16,floatval($input['global_button_letter_spacing']))) : 0;

        $clean['global_button_text_transform'] = isset($input['global_button_text_transform'])
            && in_array($input['global_button_text_transform'],array('none','uppercase','capitalize'),true)
            ? $input['global_button_text_transform'] : 'none';

        return $clean;
    }

    private function sanitize_layout( $raw ) {
        $items = json_decode( wp_unslash( (string) $raw ), true );
        if ( ! is_array( $items ) ) {
            return '[]';
        }
        $allowed = array( 'logo', 'menu', 'button', 'search', 'account', 'cart', 'text', 'html', 'social', 'spacer', 'copyright' );
        $clean = array();
        foreach ( array_slice( $items, 0, 20 ) as $item ) {
            if ( empty( $item['type'] ) || ! in_array( $item['type'], $allowed, true ) ) {
                continue;
            }
            $entry = array( 'type' => $item['type'] );
            $entry['section'] = isset( $item['section'] ) ? max( 1, min( 9, absint( $item['section'] ) ) ) : 1;
            if ( isset( $item['text'] ) ) $entry['text'] = sanitize_text_field( $item['text'] );
            if ( isset( $item['html'] ) ) $entry['html'] = wp_kses_post( $item['html'] );
            if ( isset( $item['url'] ) ) $entry['url'] = esc_url_raw( $item['url'] );
            if ( isset( $item['menu'] ) ) $entry['menu'] = absint( $item['menu'] );
            if ( isset( $item['align'] ) ) $entry['align'] = in_array( $item['align'], array( 'left', 'center', 'right' ), true ) ? $item['align'] : 'left';
            if ( isset( $item['width'] ) ) $entry['width'] = max( 20, min( 500, absint( $item['width'] ) ) );
            if ( isset( $item['hide_mobile'] ) ) $entry['hide_mobile'] = ! empty( $item['hide_mobile'] ) ? 1 : 0;
            if ( isset( $item['class'] ) ) $entry['class'] = sanitize_html_class( $item['class'] );
            if ( 'button' === $item['type'] ) {
                foreach ( array( 'button_bg', 'button_text_color', 'button_hover_bg', 'button_hover_text_color' ) as $color_key ) {
                    if ( isset( $item[ $color_key ] ) && '' !== trim( (string) $item[ $color_key ] ) ) {
                        $value = sanitize_hex_color( $item[ $color_key ] );
                        if ( $value ) $entry[ $color_key ] = $value;
                    }
                }
            }
            foreach ( array( 'instagram', 'facebook', 'linkedin', 'x' ) as $social ) { if ( isset( $item[ $social ] ) ) $entry[ $social ] = esc_url_raw( $item[ $social ] ); }
            $clean[] = $entry;
        }
        return wp_json_encode( $clean );
    }

    public function admin_assets( $hook ) {
        if ( 'toplevel_page_wpsoft-site-tools' !== $hook ) return;
        wp_enqueue_media();
        wp_enqueue_style( 'wpst-admin', WPST_URL . 'assets/css/admin.css', array(), WPST_VERSION );
        wp_enqueue_script( 'wpst-admin', WPST_URL . 'assets/js/admin.js', array( 'jquery' ), WPST_VERSION, true );
        $settings = $this->get_settings();
        wp_localize_script( 'wpst-admin', 'WPST_DATA', array(
            'menus' => $this->get_menus_for_js(),
            'siteName' => get_bloginfo( 'name' ),
            'logos' => array(
                'header' => $this->attachment_url( $settings['header_logo_id'] ),
                'footer' => $this->attachment_url( $settings['footer_logo_id'] ),
            ),
        ) );
    }

    private function attachment_url( $id ) {
        $url = $id ? wp_get_attachment_image_url( absint( $id ), 'full' ) : '';
        return $url ? $url : '';
    }

    private function get_menus_for_js() {
        $data = array();
        foreach ( wp_get_nav_menus() as $menu ) $data[] = array( 'id' => (int) $menu->term_id, 'name' => $menu->name );
        return $data;
    }

    public function body_classes( $classes ) {
        $settings = $this->get_settings();
        if ( ! empty( $settings['header_enabled'] ) ) $classes[] = 'wpst-theme-header-hidden';
        if ( ! empty( $settings['footer_enabled'] ) ) $classes[] = 'wpst-theme-footer-hidden';
        if ( ! empty( $settings['header_transparent'] ) && ( empty( $settings['header_transparent_home_only'] ) || is_front_page() ) ) $classes[] = 'wpst-header-transparent';
        if ( ! empty( $settings['header_mobile_overlay'] ) ) $classes[] = 'wpst-mobile-overlay-enabled';
        if ( ! empty( $settings['global_content_width_mode'] ) ) $classes[] = 'wpst-content-' . sanitize_html_class( $settings['global_content_width_mode'] );
        if ( ! empty( $settings['global_apply_page_bg'] ) ) $classes[] = 'wpst-global-page-bg-enabled';

        $widget_quick = isset($settings['global_widget_quick_preset']) ? $settings['global_widget_quick_preset'] : 'auto';
        if ( 'auto' === $widget_quick ) {
            $preset_map = array(
                'modern'    => 'signature',
                'minimal'   => 'minimal',
                'corporate' => 'corporate',
                'creative'  => 'soft',
                'luxury'    => 'editorial',
                'dark'      => 'dark',
            );
            $global_preset = isset($settings['global_preset']) ? $settings['global_preset'] : 'modern';
            $widget_quick = isset($preset_map[$global_preset]) ? $preset_map[$global_preset] : 'signature';
        }
        $classes[] = 'wpst-widget-quick-' . sanitize_html_class($widget_quick);
        $widget_button = isset($settings['global_widget_button_style']) ? $settings['global_widget_button_style'] : 'auto';
        $legacy_widget_button_map = array('modern'=>'primary','pill'=>'primary','minimal'=>'light');
        if ( isset($legacy_widget_button_map[$widget_button]) ) $widget_button = $legacy_widget_button_map[$widget_button];
        $classes[] = 'wpst-widget-button-' . sanitize_html_class($widget_button);
        return $classes;
    }

    public function register_elementor_assets() {
        wp_register_style( 'wpst-elementor', WPST_URL . 'assets/css/widgets/wpst-widgets-foundation.css', array( 'wpst-global-design' ), WPST_VERSION );
    }

    /**
     * Enqueue the Global Design stylesheet together with the SAVED token values.
     * Elementor can enqueue widget CSS outside the normal frontend_assets() path
     * (editor/template preview), so the inline :root token block must be available
     * through one shared entry point.
     */
    public function enqueue_global_design_assets() {
        $settings = $this->get_settings();
        wp_enqueue_style( 'wpst-global-design', WPST_URL . 'assets/css/global-design.css', array(), WPST_VERSION );
        $global_css = $this->global_design_css( $settings );
        if ( $global_css ) wp_add_inline_style( 'wpst-global-design', $global_css );
    }

    public function frontend_assets() {
        $settings = $this->get_settings();
        $this->enqueue_global_fonts( $settings );

        // Global Design System is independent from Header/Footer.
        // Use the same token emitter on frontend, Elementor preview and editor.
        $this->enqueue_global_design_assets();

        // Header/Footer specific assets are still conditional.
        if ( empty( $settings['header_enabled'] ) && empty( $settings['footer_enabled'] ) && empty($settings['scroll_top_enabled']) ) return;

        // CSS Architecture 3.0: the historical Header/Footer cascade is split into
        // ordered modules. Dependency chaining keeps the exact previous CSS order.
        $frontend_modules = array(
            'wpst-frontend'        => array( 'assets/css/frontend/wpst-01-foundation.css', array( 'wpst-global-design' ) ),
            'wpst-frontend-mobile' => array( 'assets/css/frontend/wpst-02-header-mobile.css', array( 'wpst-frontend' ) ),
            'wpst-frontend-rows'   => array( 'assets/css/frontend/wpst-03-builder-rows.css', array( 'wpst-frontend-mobile' ) ),
            'wpst-frontend-footer' => array( 'assets/css/frontend/wpst-04-footer-builder.css', array( 'wpst-frontend-rows' ) ),
            'wpst-frontend-compat'  => array( 'assets/css/frontend/wpst-05-modern-compat.css', array( 'wpst-frontend-footer' ) ),
            'wpst-frontend-header'  => array( 'assets/css/frontend/wpst-06-header-canonical.css', array( 'wpst-frontend-compat' ) ),
        );
        foreach ( $frontend_modules as $handle => $asset ) {
            wp_enqueue_style( $handle, WPST_URL . $asset[0], $asset[1], WPST_VERSION );
        }

        // frontend.js currently handles sticky/mobile header behavior; Footer-only pages do not need it.
        if ( ! empty( $settings['header_enabled'] ) || ( ! empty($settings['footer_enabled']) && ! empty($settings['footer_reveal']) ) || ! empty($settings['scroll_top_enabled']) ) {
            wp_enqueue_script( 'wpst-frontend', WPST_URL . 'assets/js/frontend.js', array(), WPST_VERSION, true );
        }
        $css = '';
        foreach ( array( 'header', 'footer' ) as $type ) {
            if ( ! empty( $settings[ $type . '_enabled' ] ) ) {
                $selectors = $this->selector_list( $settings[ 'custom_' . $type . '_selectors' ], 'header' === $type ? $this->default_header_selectors() : $this->default_footer_selectors() );
                if ( $selectors ) $css .= $selectors . '{display:none!important;}';
            }
            $css .= '.wpsoft-site-' . $type . '{--wpst-bg:' . esc_attr( $settings[ $type . '_background' ] ) . ';--wpst-color:' . esc_attr( $settings[ $type . '_text_color' ] ) . ';--wpst-width:' . absint( $settings[ $type . '_container' ] ) . 'px;--wpst-pad:' . absint( $settings[ $type . '_padding' ] ) . 'px;--wpst-button-bg:' . esc_attr( $settings['button_background'] ) . ';--wpst-button-radius:' . absint( $settings['button_radius'] ) . 'px;--wpst-logo-width:' . absint( $settings[ $type . '_logo_width' ] ) . 'px;--wpst-logo-height:' . absint( $settings[ $type . '_logo_height' ] ) . 'px;}';
            $logo_width = absint( $settings[ $type . '_logo_width' ] );
            $logo_height = absint( $settings[ $type . '_logo_height' ] );
            if ( $logo_width || $logo_height ) {
                $css .= '.wpsoft-site-' . $type . ' .wpst-q-logo img{';
                if ( $logo_width ) $css .= 'width:' . $logo_width . 'px!important;max-width:none!important;';
                if ( $logo_height ) $css .= 'height:' . $logo_height . 'px!important;max-height:none!important;';
                if ( ! $logo_width ) $css .= 'width:auto!important;';
                if ( ! $logo_height ) $css .= 'height:auto!important;';
                $css .= 'object-fit:contain;}';
            }
        }
        /* v3.3.18.21.12: Transparent / Sticky / Scroll presentation moved to
         * wpst-06-header-canonical.css. Header markup exposes state data attributes
         * and CSS variables, so frontend state styling no longer forks into inline CSS.
         */
        $css .= '.wpsoft-site-header{--wpst-scroll-threshold:' . absint($settings['header_scroll_threshold']) . 'px;}';
        $css .= '.wpst-mobile-drawer{--wpst-drawer-direction:' . ( 'left' === $settings['header_mobile_drawer_side'] ? '-100%' : '100%' ) . ';}';
        wp_add_inline_style( 'wpst-frontend', $css );
    }

    private function font_stack($key, $custom = '') {
        $fonts = array(
            'system' => '-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Helvetica,Arial,sans-serif',
            'inter' => 'Inter,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif',
            'manrope' => 'Manrope,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif',
            'dmsans' => '"DM Sans",-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif',
            'plusjakarta' => '"Plus Jakarta Sans",-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif',
            'outfit' => 'Outfit,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif',
            'sora' => 'Sora,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif',
            'spacegrotesk' => '"Space Grotesk",-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif',
            'urbanist' => 'Urbanist,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif',
            'figtree' => 'Figtree,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif',
            'worksans' => '"Work Sans",-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif',
            'nunitosans' => '"Nunito Sans",-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif',
            'sourcesans3' => '"Source Sans 3",-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif',
            'poppins' => 'Poppins,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif',
            'montserrat' => 'Montserrat,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif',
            'roboto' => 'Roboto,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif',
            'opensans' => '"Open Sans",-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif',
            'lato' => 'Lato,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif',
            'playfair' => '"Playfair Display",Georgia,serif',
            'cormorant' => '"Cormorant Garamond",Georgia,serif',
        );
        if ( 'custom' === $key && $custom ) return '"' . str_replace('"','', $custom) . '",-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif';
        return isset($fonts[$key]) ? $fonts[$key] : $fonts['system'];
    }

    private function google_font_family($key) {
        $map = array(
            'inter'=>'Inter','manrope'=>'Manrope','dmsans'=>'DM Sans','plusjakarta'=>'Plus Jakarta Sans','outfit'=>'Outfit','sora'=>'Sora','spacegrotesk'=>'Space Grotesk','urbanist'=>'Urbanist','figtree'=>'Figtree','worksans'=>'Work Sans','nunitosans'=>'Nunito Sans','sourcesans3'=>'Source Sans 3','poppins'=>'Poppins','montserrat'=>'Montserrat','roboto'=>'Roboto','opensans'=>'Open Sans','lato'=>'Lato','playfair'=>'Playfair Display','cormorant'=>'Cormorant Garamond'
        );
        return isset($map[$key]) ? $map[$key] : '';
    }

    private function enqueue_global_fonts($settings) {
        if ( empty($settings['global_google_fonts']) ) return;
        $families = array();
        foreach ( array('global_body_font','global_heading_font') as $field ) {
            $key = isset($settings[$field]) ? $settings[$field] : 'system';
            $family = $this->google_font_family($key);
            if ($family) $families[$family] = true;
        }
        // Footer Template Library premium typography personalities.
        // Tek bir birleşik Google Fonts isteğiyle modern footer presetlerinin gerçek fontlarını yükler.
        foreach ( array('DM Sans','Plus Jakarta Sans','Sora','Space Grotesk','Manrope','Cormorant Garamond') as $family ) {
            $families[$family] = true;
        }
        if ( empty($families) ) return;
        $parts = array();
        foreach ( array_keys($families) as $family ) {
            $parts[] = 'family=' . str_replace('%20','+',rawurlencode($family)) . ':wght@300;400;500;600;700;800;900';
        }
        $url = 'https://fonts.googleapis.com/css2?' . implode('&',$parts) . '&display=swap';
        wp_enqueue_style('wpst-google-fonts',$url,array(),null);
    }

    private function global_design_css( $settings ) {
        $shadow_map = array(
            'none'   => 'none',
            'soft'   => '0 10px 32px rgba(15,23,42,.07)',
            'medium' => '0 16px 42px rgba(15,23,42,.11)',
            'strong' => '0 22px 60px rgba(15,23,42,.16)',
        );
        $motion_map = array(
            'off'     => '0s',
            'soft'    => '.18s',
            'normal'  => '.28s',
            'dynamic' => '.42s',
        );

        $shadow = isset( $shadow_map[$settings['global_shadow']] ) ? $shadow_map[$settings['global_shadow']] : $shadow_map['soft'];
        $motion = isset( $motion_map[$settings['global_motion']] ) ? $motion_map[$settings['global_motion']] : $motion_map['normal'];

        return ':root{' .
            '--wpst-primary:' . esc_attr( $settings['global_primary'] ) . ';' .
            '--wpst-secondary:' . esc_attr( $settings['global_secondary'] ) . ';' .
            '--wpst-heading:' . esc_attr( $settings['global_heading'] ) . ';' .
            '--wpst-text:' . esc_attr( $settings['global_text'] ) . ';' .
            '--wpst-muted:' . esc_attr( $settings['global_muted'] ) . ';' .
            '--wpst-surface:' . esc_attr( $settings['global_surface'] ) . ';' .
            '--wpst-page-bg:' . esc_attr( $settings['global_page_bg'] ) . ';' .
            '--wpst-soft:' . esc_attr( $settings['global_soft'] ) . ';' .
            '--wpst-border:' . esc_attr( $settings['global_border'] ) . ';' .
            '--wpst-accent:' . esc_attr( $settings['global_accent'] ) . ';' .
            '--wpst-success:' . esc_attr( $settings['global_success'] ) . ';' .
            '--wpst-warning:' . esc_attr( $settings['global_warning'] ) . ';' .
            '--wpst-danger:' . esc_attr( $settings['global_danger'] ) . ';' .
            '--wpst-container:' . absint( $settings['global_container'] ) . 'px;' .
            '--wpst-container-narrow:' . absint( $settings['global_container_narrow'] ) . 'px;' .
            '--wpst-container-wide:' . absint( $settings['global_container_wide'] ) . 'px;' .
            '--wpst-section-space:' . absint( $settings['global_section_space'] ) . 'px;' .
            '--wpst-section-space-tablet:' . absint( $settings['global_section_space_tablet'] ) . 'px;' .
            '--wpst-section-space-mobile:' . absint( $settings['global_section_space_mobile'] ) . 'px;' .
            '--wpst-gap:' . absint( $settings['global_gap'] ) . 'px;' .
            '--wpst-space-xs:' . absint( $settings['global_space_xs'] ) . 'px;' .
            '--wpst-space-sm:' . absint( $settings['global_space_sm'] ) . 'px;' .
            '--wpst-space-md:' . absint( $settings['global_space_md'] ) . 'px;' .
            '--wpst-space-lg:' . absint( $settings['global_space_lg'] ) . 'px;' .
            '--wpst-space-xl:' . absint( $settings['global_space_xl'] ) . 'px;' .
            '--wpst-space-xxl:' . absint( $settings['global_space_xxl'] ) . 'px;' .
            '--wpst-radius-sm:' . absint( $settings['global_radius_sm'] ) . 'px;' .
            '--wpst-radius-md:' . absint( $settings['global_radius_md'] ) . 'px;' .
            '--wpst-radius-lg:' . absint( $settings['global_radius_lg'] ) . 'px;' .
            '--wpst-radius-xl:' . absint( $settings['global_radius_xl'] ) . 'px;' .
            '--wpst-card-radius:' . absint( $settings['global_card_radius'] ) . 'px;' .
            '--wpst-button-radius-global:' . absint( $settings['global_button_radius'] ) . 'px;' .
            '--wpst-button-height:' . absint( $settings['global_button_height'] ) . 'px;' .
            '--wpst-shadow:' . $shadow . ';' .
            '--wpst-shadow-sm:0 4px 16px rgba(15,23,42,.06);' .
            '--wpst-shadow-md:0 14px 38px rgba(15,23,42,.10);' .
            '--wpst-shadow-lg:0 24px 70px rgba(15,23,42,.15);' .
            '--wpst-motion:' . $motion . ';' .
            '--wpst-font-body:' . esc_attr( $this->font_stack($settings['global_body_font'],$settings['global_custom_body_font']) ) . ';' .
            '--wpst-font-heading:' . esc_attr( $this->font_stack($settings['global_heading_font'],$settings['global_custom_heading_font']) ) . ';' .
            '--wpst-font-size:' . absint($settings['global_base_font_size']) . 'px;' .
            '--wpst-font-size-tablet:' . absint($settings['global_base_font_size_tablet']) . 'px;' .
            '--wpst-font-size-mobile:' . absint($settings['global_base_font_size_mobile']) . 'px;' .
            '--wpst-body-line:' . esc_attr($settings['global_body_line_height']) . ';' .
            '--wpst-heading-weight:' . absint($settings['global_heading_weight']) . ';' .
            '--wpst-heading-line:' . esc_attr($settings['global_heading_line_height']) . ';' .
            '--wpst-heading-letter:' . esc_attr($settings['global_heading_letter_spacing']) . 'em;' .
            '--wpst-h1:' . absint($settings['global_h1_size']) . 'px;' .
            '--wpst-h2:' . absint($settings['global_h2_size']) . 'px;' .
            '--wpst-h3:' . absint($settings['global_h3_size']) . 'px;' .
            '--wpst-h4:' . absint($settings['global_h4_size']) . 'px;' .
            '--wpst-h5:' . absint($settings['global_h5_size']) . 'px;' .
            '--wpst-h6:' . absint($settings['global_h6_size']) . 'px;' .
            '--wpst-h1-tablet:' . absint($settings['global_h1_tablet']) . 'px;' .
            '--wpst-h2-tablet:' . absint($settings['global_h2_tablet']) . 'px;' .
            '--wpst-h3-tablet:' . absint($settings['global_h3_tablet']) . 'px;' .
            '--wpst-h4-tablet:' . absint($settings['global_h4_tablet']) . 'px;' .
            '--wpst-h5-tablet:' . absint($settings['global_h5_tablet']) . 'px;' .
            '--wpst-h6-tablet:' . absint($settings['global_h6_tablet']) . 'px;' .
            '--wpst-h1-mobile:' . absint($settings['global_h1_mobile']) . 'px;' .
            '--wpst-h2-mobile:' . absint($settings['global_h2_mobile']) . 'px;' .
            '--wpst-h3-mobile:' . absint($settings['global_h3_mobile']) . 'px;' .
            '--wpst-h4-mobile:' . absint($settings['global_h4_mobile']) . 'px;' .
            '--wpst-h5-mobile:' . absint($settings['global_h5_mobile']) . 'px;' .
            '--wpst-h6-mobile:' . absint($settings['global_h6_mobile']) . 'px;' .
            '--wpst-button-weight:' . absint($settings['global_button_weight']) . ';' .
            '--wpst-button-letter:' . esc_attr($settings['global_button_letter_spacing']) . 'em;' .
            '--wpst-button-transform:' . esc_attr($settings['global_button_text_transform']) . ';' .
            '--wpst-button-bg:' . esc_attr($settings['global_button_bg']) . ';' .
            '--wpst-button-text:' . esc_attr($settings['global_button_text']) . ';' .
            '--wpst-button-hover-bg:' . esc_attr($settings['global_button_hover_bg']) . ';' .
            '--wpst-button-hover-text:' . esc_attr($settings['global_button_hover_text']) . ';' .
            '--wpst-button-secondary-bg:' . esc_attr($settings['global_secondary_button_bg']) . ';' .
            '--wpst-button-secondary-text:' . esc_attr($settings['global_secondary_button_text']) . ';' .
            '--wpst-button-secondary-border:' . esc_attr($settings['global_secondary_button_border']) . ';' .
            '--wpst-button-secondary-hover-bg:' . esc_attr($settings['global_secondary_button_hover_bg']) . ';' .
            '--wpst-button-secondary-hover-text:' . esc_attr($settings['global_secondary_button_hover_text']) . ';' .
            '--wpst-button-padding-x:' . absint($settings['global_button_padding_x']) . 'px;' .
            '--wpst-link:' . esc_attr($settings['global_link']) . ';' .
            '--wpst-link-hover:' . esc_attr($settings['global_link_hover']) . ';' .
            '--wpst-link-decoration:' . ( ! empty($settings['global_link_underline']) ? 'underline' : 'none' ) . ';' .
            '--wpst-link-hover-decoration:' . ( ! empty($settings['global_link_hover_underline']) ? 'underline' : 'none' ) . ';' .
            '--wpst-input-bg:' . esc_attr($settings['global_input_bg']) . ';' .
            '--wpst-input-text:' . esc_attr($settings['global_input_text']) . ';' .
            '--wpst-input-border:' . esc_attr($settings['global_input_border']) . ';' .
            '--wpst-input-focus:' . esc_attr($settings['global_input_focus']) . ';' .
            '--wpst-input-height:' . absint($settings['global_input_height']) . 'px;' .
            '--wpst-input-radius:' . absint($settings['global_input_radius']) . 'px;' .
            '--wpst-input-border-width:' . absint($settings['global_input_border_width']) . 'px;' .
            '--wpst-surface-alt:' . esc_attr($settings['global_surface_alt']) . ';' .
            '--wpst-surface-dark:' . esc_attr($settings['global_surface_dark']) . ';' .
        '}';
    }

    private function selector_list( $custom, $defaults ) {
        $raw = trim( (string) $custom );
        if ( '' === $raw ) return implode( ',', $defaults );
        return implode( ',', array_filter( array_map( 'trim', preg_split( '/[\r\n,]+/', $raw ) ) ) );
    }

    private function default_header_selectors() { return array( '#header-outer', '#header-space', 'header#top', '#masthead', '#site-header', 'header#site-header', '.site-header', '.whb-header', '.elementor-location-header:not(.wpsoft-site-header .elementor-location-header)' ); }
    private function default_footer_selectors() { return array( '#footer-outer', '#copyright', 'footer#footer-outer', '#colophon', '#site-footer', 'footer#site-footer', '.site-footer', '.footer-container', '.elementor-location-footer:not(.wpsoft-site-footer .elementor-location-footer)' ); }

    public function elementor_notice() {
        if ( ! current_user_can( 'manage_options' ) || did_action( 'elementor/loaded' ) ) return;
        echo '<div class="notice notice-info"><p><strong>WPSoft Site Tools:</strong> Hızlı Tasarım Elementor olmadan çalışır. Elementor şablon modu için Elementor etkin olmalıdır.</p></div>';
    }

    public function settings_page() {
        if ( ! current_user_can( 'manage_options' ) ) return;
        $settings = $this->get_settings();
        $templates = $this->get_elementor_templates();
        ?>
        <div class="wrap wpst-wrap">
            <div class="wpst-topbar">
                <div class="wpst-brand"><strong>WPSoft Site Tools</strong><span>v<?php echo esc_html( WPST_VERSION ); ?></span></div>
                <div class="wpst-top-actions"><a class="button" href="<?php echo esc_url( home_url('/') ); ?>" target="_blank" rel="noopener">Önizlemeyi Aç ↗</a><button type="submit" form="wpst-settings-form" class="button button-primary">Kaydet</button></div>
            </div>
            <div class="wpst-integration-card">
                <div class="wpst-integration-icon">E</div>
                <div class="wpst-integration-copy"><strong>Elementor Entegrasyonu</strong><span><?php echo did_action( 'elementor/loaded' ) ? 'Elementor algılandı. WPSoft Şablonlar editör içinde otomatik yüklenir.' : 'Elementor şu anda algılanmadı.'; ?></span></div>
                <div class="wpst-integration-status <?php echo did_action( 'elementor/loaded' ) ? 'is-ok' : 'is-warn'; ?>"><?php echo did_action( 'elementor/loaded' ) ? 'Bağlı' : 'Kontrol Et'; ?></div>
            </div>
            <?php
            $wpst_template_count = wp_count_posts('elementor_library');
            $wpst_template_total = $wpst_template_count ? (int)$wpst_template_count->publish : 0;
            $wpst_post_count = wp_count_posts('post');
            $wpst_post_total = $wpst_post_count ? (int)$wpst_post_count->publish : 0;
            ?>
            <div class="wpst-overview">
                <div class="wpst-overview-head">
                    <div>
                        <span class="wpst-overview-kicker">SITE CONTROL CENTER</span>
                        <h2>Site Tools Genel Bakış</h2>
                        <p>Aktif yapıların durumunu kontrol edin ve sık kullanılan alanlara hızlıca ulaşın.</p>
                    </div>
                    <div class="wpst-compat-notice">
                    <strong>Tema Uyumluluğu</strong>
                    <span><?php echo class_exists('WPST_Theme_Compatibility') ? esc_html(ucfirst(WPST_Theme_Compatibility::detect())) : 'Generic'; ?> profili aktif. Header/Footer çakışma seçicileri otomatik uygulanır.</span>
                </div>
                <div class="wpst-overview-actions">
                        <a class="button button-primary" href="<?php echo esc_url(admin_url('admin.php?page=wpsoft-my-templates')); ?>">WPSoft Şablonlar</a>
                        <a class="button" href="<?php echo esc_url(admin_url('nav-menus.php')); ?>">Menüler</a>
                        <a class="button" href="<?php echo esc_url(admin_url('admin.php?page=wpsoft-system-status')); ?>">Sistem Durumu</a>
                        <a class="button" href="<?php echo esc_url(admin_url('admin.php?page=wpsoft-activation')); ?>">Aktivasyon</a>
                    </div>
                </div>

                <div class="wpst-status-grid">
                    <button type="button" class="wpst-status-card" data-open-tab="header">
                        <i class="<?php echo !empty($settings['header_enabled'])?'is-on':''; ?>">H</i>
                        <span><small>HEADER</small><strong><?php echo !empty($settings['header_enabled'])?'Aktif':'Pasif'; ?></strong><em>Üst alan ayarları</em></span>
                        <b>→</b>
                    </button>
                    <button type="button" class="wpst-status-card" data-open-tab="footer">
                        <i class="<?php echo !empty($settings['footer_enabled'])?'is-on':''; ?>">F</i>
                        <span><small>FOOTER</small><strong><?php echo !empty($settings['footer_enabled'])?'Aktif':'Pasif'; ?></strong><em>Alt alan ayarları</em></span>
                        <b>→</b>
                    </button>
                    <button type="button" class="wpst-status-card" data-open-tab="blog">
                        <i class="<?php echo (!empty($settings['blog_archive_enabled'])||!empty($settings['blog_single_enabled']))?'is-on':''; ?>">B</i>
                        <span><small>BLOG</small><strong><?php echo (!empty($settings['blog_archive_enabled'])||!empty($settings['blog_single_enabled']))?'Yapılandırıldı':'Kurulum Bekliyor'; ?></strong><em>Arşiv & tek yazı</em></span>
                        <b>→</b>
                    </button>
                    <a class="wpst-status-card" href="<?php echo esc_url(admin_url('edit.php?post_type=elementor_library')); ?>">
                        <i class="is-on">T</i>
                        <span><small>ELEMENTOR</small><strong><?php echo absint($wpst_template_total); ?> Şablon</strong><em>Şablon kütüphanesi</em></span>
                        <b>→</b>
                    </a>
                </div>

                <div class="wpst-quick-grid">
                    <a href="<?php echo esc_url(admin_url('admin.php?page=wpsoft-my-templates&view=new')); ?>"><i>＋</i><span><strong>Yeni Şablon</strong><small>Header, Footer, Mega Menü veya Blog</small></span></a>
                    <a href="<?php echo esc_url(admin_url('edit.php')); ?>"><i>✎</i><span><strong>Blog Yazıları</strong><small><?php echo absint($wpst_post_total); ?> yayınlanmış yazı</small></span></a>
                    <a href="<?php echo esc_url(admin_url('edit.php?post_type=page')); ?>"><i>□</i><span><strong>Sayfalar</strong><small>Site sayfalarını yönetin</small></span></a>
                    <a href="<?php echo esc_url(admin_url('admin.php?page=wpsoft-my-templates')); ?>"><i>◇</i><span><strong>Şablonlarım</strong><small>Header, Footer, Blog ve koşullu şablonlar</small></span></a>
                </div>
            </div>
            <form method="post" action="options.php" id="wpst-settings-form">
                <?php settings_fields( 'wpst_settings_group' ); ?>
                <input type="hidden" name="wpst_settings[elementor_library_enabled]" value="0">
                <label class="wpst-elementor-toggle"><input type="checkbox" name="wpst_settings[elementor_library_enabled]" value="1" <?php checked( ! empty( $settings['elementor_library_enabled'] ) ); ?>><span></span><b>Elementor içinde WPSoft Şablonlar aktif</b></label>
                <div class="wpst-tabs">
                    <button type="button" class="wpst-tab is-active" data-tab="header"><i>H</i><span><b>Header</b><small>Üst alan & navigasyon</small></span></button>
                    <button type="button" class="wpst-tab" data-tab="footer"><i>F</i><span><b>Footer</b><small>Alt alan & CTA</small></span></button>
                    <button type="button" class="wpst-tab" data-tab="blog"><i>B</i><span><b>Blog</b><small>Arşiv & tek yazı</small></span></button>
                    <button type="button" class="wpst-tab" data-tab="global"><i>G</i><span><b>Global Tasarım</b><small>Renk & ölçüler</small></span></button>
                </div>
                <?php $this->builder_panel( 'header', 'Header', $settings, $templates ); ?>
                <?php $this->builder_panel( 'footer', 'Footer', $settings, $templates ); ?>
                <?php $this->blog_panel( $settings, $templates ); ?>
                <?php $this->global_design_panel( $settings ); ?>
                <div class="wpst-savebar"><div><strong>Değişiklikleri kaydet</strong><span>Header, Footer, Blog ve Global Tasarım ayarları kaydedilir. Şablon atamaları Şablonlarım ve Display Conditions üzerinden yönetilir.</span></div><?php submit_button( 'Ayarları Kaydet', 'primary', 'submit', false ); ?></div>
            </form>
        </div>
        <?php
    }

    private function blog_panel( $settings, $templates ) {
        $single_presets = class_exists('WPST_Blog_Templates') ? WPST_Blog_Templates::templates() : array();
        ?>
        <section class="wpst-panel" data-panel="blog">
            <div class="wpst-toolbar">
                <div>
                    <h2>Blog Builder</h2>
                    <p>Tek yazı, blog sayfası ve kategori / etiket / yazar arşivlerini tek merkezden yönetin.</p>
                </div>
            </div>

            <div class="wpst-blog-dual-grid">
                <article class="wpst-blog-builder-box">
                    <div class="wpst-blog-box-head">
                        <div>
                            <span class="wpst-blog-kicker">ARCHIVE / LISTING</span>
                            <h3>Blog Arşiv Şablonu</h3>
                            <p>Menüye eklediğiniz Blog sayfasında yayınlanmış yazıları listeler.</p>
                        </div>
                        <label class="wpst-switch">
                            <input type="checkbox" name="wpst_settings[blog_archive_enabled]" value="1" <?php checked(!empty($settings['blog_archive_enabled'])); ?>>
                            <span></span><b>Aktif</b>
                        </label>
                    </div>

                    <label>Arşiv Elementor Şablonu
                        <select name="wpst_settings[blog_archive_template]">
                            <?php $this->template_options($templates,$settings['blog_archive_template']); ?>
                        </select>
                    </label>
                    <label>Blog Sayfası
                        <?php
                        wp_dropdown_pages(array(
                            'name'=>'wpst_settings[blog_page_id]',
                            'selected'=>absint($settings['blog_page_id']),
                            'show_option_none'=>'— Blog sayfasını seçin —',
                            'option_none_value'=>'0',
                            'post_status'=>array('publish','draft','private')
                        ));
                        ?>
                    </label>
                    <?php if(!empty($settings['blog_page_id'])):
                        $blog_page_id=absint($settings['blog_page_id']);
                        $blog_page_url=get_permalink($blog_page_id);
                    ?>
                    <div class="wpst-blog-page-actions">
                        <a class="button" href="<?php echo esc_url(get_edit_post_link($blog_page_id)); ?>">Sayfayı Düzenle</a>
                        <?php if($blog_page_url): ?><a class="button" target="_blank" rel="noopener" href="<?php echo esc_url($blog_page_url); ?>">Blog Sayfasını Gör</a><?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <div class="wpst-blog-archive-help">
                        <strong>Önerilen kullanım</strong>
                        <span>WPSoft Şablonlar → Blog Arşiv şablonlarından birini oluşturun veya normal bir Elementor sayfasına “WPSoft · Blog Yazıları” widgetını ekleyin.</span>
                    </div>

                    <a class="button button-primary" href="<?php echo esc_url(admin_url('admin.php?page=wpsoft-my-templates')); ?>">WPSoft Şablonlar'ı Aç</a>
                </article>

                <article class="wpst-blog-builder-box">
                    <div class="wpst-blog-box-head">
                        <div>
                            <span class="wpst-blog-kicker">SINGLE POST</span>
                            <h3>Tek Yazı Şablonu</h3>
                            <p>Her blog yazısının detay sayfasında dinamik olarak uygulanır.</p>
                        </div>
                        <label class="wpst-switch">
                            <input type="checkbox" name="wpst_settings[blog_single_enabled]" value="1" <?php checked(!empty($settings['blog_single_enabled'])); ?>>
                            <span></span><b>Aktif</b>
                        </label>
                    </div>

                    <label>Tek Yazı Elementor Şablonu
                        <select name="wpst_settings[blog_single_template]">
                            <?php $this->template_options($templates,$settings['blog_single_template']); ?>
                        </select>
                    </label>

                    <div class="wpst-blog-presets">
                        <strong>Hazır Tek Yazı başlangıçları</strong>
                        <?php foreach($single_presets as $key=>$item): ?>
                            <div class="wpst-blog-preset">
                                <div>
                                    <b><?php echo esc_html($item['title']); ?></b>
                                    <span><?php echo esc_html($item['desc']); ?></span>
                                </div>
                                <a class="button" href="<?php echo esc_url(wp_nonce_url(admin_url('admin-post.php?action=wpst_create_blog_template&template='.$key),'wpst_create_blog_template')); ?>">Elementor'da Oluştur</a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </article>
            </div>

            <div class="wpst-blog-widget-note">
                <strong>Arşiv Widgetı</strong>
                <span>WPSoft · Blog Yazıları — 10 / 20 / 30 / tüm yazılar, kategori, aktif arşiv sorgusu, grid, magazine, liste, kolon ve sayfalama.</span>
            </div>

            <div class="wpst-blog-widget-note">
                <strong>Tek Yazı Dinamik Widgetları</strong>
                <span>Arşiv Başlığı · Arşiv Açıklaması · Arşiv Yazar Kartı · İçerik Başlığı · İçerik Content · Content Görsel · İçerik Bilgileri · İçerik Özeti · Yazar Kutusu · Kategori & Etiketler · İçerik Paylaş · Okuma İlerlemesi · İlgili Yazılar · Önceki/Sonraki · Yorumlar</span>
            </div>
        </section>
        <?php
    }


    private function global_design_panel( $settings ) {
        ?>
        <section class="wpst-panel wpst-global-system wpst-global-v2" data-panel="global">
            <div class="wpst-global-head">
                <div><span class="wpst-builder-kicker">DESIGN SYSTEM 2.0</span><h2>Global Tasarım Sistemi</h2><p>Renk, tipografi, ölçü, radius, gölge ve buton dilini tek merkezden yönetin.</p></div>
                <span class="wpst-global-badge">Site Geneli</span>
            </div>

            <div class="wpst-preset-panel">
                <div class="wpst-preset-copy"><span>STYLE PRESETS</span><strong>Hazır Tasarım Dili</strong><p>Bir preset seçin; renk, radius, tipografi ve efekt değerleri otomatik doldurulsun. Sonrasında istediğiniz alanı değiştirebilirsiniz.</p></div>
                <div class="wpst-preset-grid">
                    <?php
                    $presets=array(
                        'modern'=>array('Modern','Dengeli · Soft UI','#2563eb','#7c3aed'),
                        'minimal'=>array('Minimal','Temiz · Keskin','#111827','#64748b'),
                        'corporate'=>array('Corporate','Güven · Kurumsal','#1d4ed8','#0f766e'),
                        'creative'=>array('Creative','Canlı · Cesur','#7c3aed','#db2777'),
                        'luxury'=>array('Luxury','Premium · Zarif','#a16207','#292524'),
                        'dark'=>array('Dark','Koyu · Teknoloji','#38bdf8','#8b5cf6')
                    );
                    foreach($presets as $key=>$preset):
                    ?>
                    <button type="button" class="wpst-design-preset <?php echo $settings['global_preset']===$key?'is-active':''; ?>" data-design-preset="<?php echo esc_attr($key); ?>">
                        <i style="--p1:<?php echo esc_attr($preset[2]); ?>;--p2:<?php echo esc_attr($preset[3]); ?>"><b></b><b></b></i>
                        <span><strong><?php echo esc_html($preset[0]); ?></strong><small><?php echo esc_html($preset[1]); ?></small></span>
                    </button>
                    <?php endforeach; ?>
                </div>
                <input type="hidden" name="wpst_settings[global_preset]" value="<?php echo esc_attr($settings['global_preset']); ?>" data-global-preset-input>
                <?php
                $mobile_menu_presets=array(
                    'corporate-modern'=>array('01 Kurumsal Modern','Koyu mavi kartlar ve güçlü aktif durum.'),
                    'minimal-light'=>array('02 Minimal Light','Beyaz, hafif çizgili ve yüksek okunabilirlik.'),
                    'luxury'=>array('03 Luxury','Geniş boşluk, zarif tipografi ve premium CTA.'),
                    'creative-gradient'=>array('04 Creative Gradient','Gradient vurgu ve ölçülü soft glow.'),
                    'e-commerce'=>array('05 E-Commerce','Kompakt kategori akışı ve güçlü alt menüler.'),
                    'hotel-tourism'=>array('06 Hotel & Tourism','Belirgin marka, rahat akış ve rezervasyon CTA.'),
                    'professional-dark'=>array('07 Professional Dark','Neutral koyu, ince çizgili teknoloji karakteri.'),
                    'classic-clean'=>array('08 Classic Clean','Sektör bağımsız, sade ve açık navigasyon.'),
                );
                ?>
                <section class="wpst-mobile-menu-presets" aria-labelledby="wpst-mobile-menu-presets-title">
                    <div class="wpst-global-card-head"><div><span class="wpst-card-kicker">MOBİL MENÜ TASARIMLARI</span><h3 id="wpst-mobile-menu-presets-title">Mobil Menü <small>› Hazır Tasarımlar</small></h3><p>Navigation widget “Global Tasarımı Kullan” modundayken seçilen tasarım uygulanır.</p></div></div>
                    <div class="wpst-mobile-preset-grid">
                    <?php foreach($mobile_menu_presets as $key=>$preset): ?>
                        <label class="wpst-mobile-preset-card <?php echo $settings['global_mobile_menu_preset']===$key?'is-active':''; ?>" data-mobile-preset-card="<?php echo esc_attr($key); ?>">
                            <input type="radio" name="wpst_settings[global_mobile_menu_preset]" value="<?php echo esc_attr($key); ?>" <?php checked($settings['global_mobile_menu_preset'],$key); ?>>
                            <span class="wpst-mobile-preset-mini wpst-mobile-preset-mini--<?php echo esc_attr($key); ?>"><i></i><b></b><b></b><em></em></span>
                            <span><strong><?php echo esc_html($preset[0]); ?></strong><small><?php echo esc_html($preset[1]); ?></small></span>
                            <mark>Aktif</mark>
                        </label>
                    <?php endforeach; ?>
                    </div>
                    <details class="wpst-mobile-preset-custom"><summary>Mobil Menü › Özel Değerler <small>Boş alanlar seçili preset değerini kullanır.</small></summary><div class="wpst-mobile-preset-custom-grid">
                        <?php foreach(array('global_mobile_panel_background'=>'Panel arka planı','global_mobile_item_background'=>'Öğe arka planı','global_mobile_text_color'=>'Metin rengi','global_mobile_active_background'=>'Aktif arka plan','global_mobile_cta_background'=>'CTA arka planı','global_mobile_icon_background'=>'İkon arka planı') as $key=>$label): ?><label><span><?php echo esc_html($label); ?></span><input type="text" name="wpst_settings[<?php echo esc_attr($key); ?>]" value="<?php echo esc_attr($settings[$key]); ?>" placeholder="#hex, rgba() veya gradient"></label><?php endforeach; ?>
                        <?php foreach(array('global_mobile_panel_padding'=>'Panel padding','global_mobile_item_radius'=>'Öğe radius','global_mobile_item_height'=>'Öğe yüksekliği','global_mobile_item_gap'=>'Öğe aralığı','global_mobile_icon_box_size'=>'İkon kutusu','global_mobile_cta_radius'=>'CTA radius','global_mobile_text_size'=>'Metin boyutu') as $key=>$label): ?><label><span><?php echo esc_html($label); ?></span><input type="number" min="0" max="120" name="wpst_settings[<?php echo esc_attr($key); ?>]" value="<?php echo esc_attr($settings[$key]); ?>" placeholder="Preset"><small>px</small></label><?php endforeach; ?>
                        <label><span>Logo konumu</span><select name="wpst_settings[global_mobile_logo_position]"><option value="" <?php selected($settings['global_mobile_logo_position'],''); ?>>Preset</option><option value="flex-start" <?php selected($settings['global_mobile_logo_position'],'flex-start'); ?>>Sol</option><option value="center" <?php selected($settings['global_mobile_logo_position'],'center'); ?>>Orta</option><option value="flex-end" <?php selected($settings['global_mobile_logo_position'],'flex-end'); ?>>Sağ</option></select></label>
                    </div></details>
                </section>
                <div class="wpst-global-widget-preset">
                    <div class="wpst-global-widget-preset-copy"><strong>Widget Hızlı Tasarım</strong><small>Kart, yüzey, boşluk ve aksiyon butonlarını ortak bir tasarım dilinde tutar. Widgetta “Global Tasarımı Takip Et” seçildiğinde uygulanır.</small></div>
                    <div class="wpst-global-widget-preset-controls">
                        <label><span>Widget Karakteri</span><select name="wpst_settings[global_widget_quick_preset]" data-design-field="global_widget_quick_preset">
                            <option value="auto" <?php selected($settings['global_widget_quick_preset'],'auto'); ?>>Otomatik · Global Presete Göre</option>
                            <option value="signature" <?php selected($settings['global_widget_quick_preset'],'signature'); ?>>WPSoft Signature</option>
                            <option value="corporate" <?php selected($settings['global_widget_quick_preset'],'corporate'); ?>>Corporate Clean</option>
                            <option value="editorial" <?php selected($settings['global_widget_quick_preset'],'editorial'); ?>>Editorial</option>
                            <option value="soft" <?php selected($settings['global_widget_quick_preset'],'soft'); ?>>Soft Modern</option>
                            <option value="dark" <?php selected($settings['global_widget_quick_preset'],'dark'); ?>>Dark Premium</option>
                            <option value="minimal" <?php selected($settings['global_widget_quick_preset'],'minimal'); ?>>Minimal</option>
                        </select></label>
                        <label><span>Buton Karakteri</span><select name="wpst_settings[global_widget_button_style]" data-design-field="global_widget_button_style">
                            <?php $wpst_button_character = isset($settings['global_widget_button_style']) ? $settings['global_widget_button_style'] : 'auto'; $wpst_button_character = array('modern'=>'primary','pill'=>'primary','minimal'=>'light')[$wpst_button_character] ?? $wpst_button_character; ?>
                            <option value="auto" <?php selected($wpst_button_character,'auto'); ?>>Otomatik · Widget Karakterine Göre</option>
                            <option value="primary" <?php selected($wpst_button_character,'primary'); ?>>Primary · Marka Rengi</option>
                            <option value="secondary" <?php selected($wpst_button_character,'secondary'); ?>>Violet · Mor</option>
                            <option value="emerald" <?php selected($wpst_button_character,'emerald'); ?>>Emerald · Yeşil</option>
                            <option value="sunset" <?php selected($wpst_button_character,'sunset'); ?>>Sunset · Turuncu</option>
                            <option value="dark" <?php selected($wpst_button_character,'dark'); ?>>Dark · Koyu</option>
                            <option value="light" <?php selected($wpst_button_character,'light'); ?>>Light · Açık</option>
                            <option value="outline" <?php selected($wpst_button_character,'outline'); ?>>Outline · Modern</option>
                            <option value="soft" <?php selected($wpst_button_character,'soft'); ?>>Soft · Yumuşak</option>
                            <option value="gradient" <?php selected($wpst_button_character,'gradient'); ?>>Gradient · Premium</option>
                            <option value="glass" <?php selected($wpst_button_character,'glass'); ?>>Glass · Cam</option>
                        </select></label>
                    </div>
                </div>
            </div>

            <div class="wpst-global-layout">
                <div class="wpst-global-main">
                    <div class="wpst-global-card">
                        <div class="wpst-global-card-head"><div><span class="wpst-card-kicker">TOKENS</span><h3>Renk Sistemi</h3><p>Marka, yüzey ve durum renklerini tek token sistemi altında yönetin.</p></div></div>
                        <div class="wpst-color-grid wpst-color-grid-v2">
                            <?php
                            $colors=array(
                                'global_primary'=>'Ana Renk','global_secondary'=>'İkincil','global_accent'=>'Vurgu',
                                'global_heading'=>'Başlık','global_text'=>'Metin','global_muted'=>'Pasif Metin',
                                'global_page_bg'=>'Sayfa Arka Planı','global_surface'=>'Kart / Yüzey','global_soft'=>'Soft Arka Plan','global_border'=>'Kenarlık',
                                'global_success'=>'Başarılı','global_warning'=>'Uyarı','global_danger'=>'Hata'
                            );
                            foreach($colors as $key=>$label):
                            ?>
                            <label><span><?php echo esc_html($label); ?></span><div class="wpst-global-color"><input type="color" name="wpst_settings[<?php echo esc_attr($key); ?>]" value="<?php echo esc_attr($settings[$key]); ?>" data-design-field="<?php echo esc_attr($key); ?>"><code><?php echo esc_html($settings[$key]); ?></code></div></label>
                            <?php endforeach; ?>
                        </div>
                        <div class="wpst-global-page-bg-row">
                            <label class="wpst-check"><input type="checkbox" name="wpst_settings[global_apply_page_bg]" value="1" <?php checked(!empty($settings['global_apply_page_bg'])); ?> data-design-field="global_apply_page_bg"><span>Sayfa arka planını site içerik alanına uygula</span></label>
                            <small>Açık olduğunda “Sayfa Arka Planı” rengi body, tema içerik kabuğu ve Elementor sayfa yüzeyine uygulanır. Elementor'da özel arka plan verilen bölüm/container'lar korunur; Header ve Footer etkilenmez.</small>
                        </div>
                    </div>

                    <div class="wpst-global-card">
                        <div class="wpst-global-card-head"><div><span class="wpst-card-kicker">LAYOUT</span><h3>Container & Spacing</h3><p>Dar içerik, standart sayfa ve geniş vitrin alanları için ortak ölçüler.</p></div></div>
                        <div class="wpst-global-fields is-three">
                            <label><span>Dar Container</span><input type="number" min="640" max="1100" name="wpst_settings[global_container_narrow]" value="<?php echo absint($settings['global_container_narrow']); ?>" data-design-field="global_container_narrow"><small>px</small></label>
                            <label><span>Standart Container</span><input type="number" min="960" max="1600" name="wpst_settings[global_container]" value="<?php echo absint($settings['global_container']); ?>" data-design-field="global_container"><small>px</small></label>
                            <label><span>Geniş Container</span><input type="number" min="1200" max="1920" name="wpst_settings[global_container_wide]" value="<?php echo absint($settings['global_container_wide']); ?>" data-design-field="global_container_wide"><small>px</small></label>
                        </div>
                        <div class="wpst-global-fields">
                            <label><span>Varsayılan İçerik Genişliği</span><select name="wpst_settings[global_content_width_mode]" data-design-field="global_content_width_mode"><option value="narrow" <?php selected($settings['global_content_width_mode'],'narrow'); ?>>Dar</option><option value="standard" <?php selected($settings['global_content_width_mode'],'standard'); ?>>Standart</option><option value="wide" <?php selected($settings['global_content_width_mode'],'wide'); ?>>Geniş</option></select></label>
                            <label><span>Grid Gap</span><input type="number" min="8" max="80" name="wpst_settings[global_gap]" value="<?php echo absint($settings['global_gap']); ?>" data-design-field="global_gap"><small>px</small></label>
                            <label><span>Section · Desktop</span><input type="number" min="20" max="180" name="wpst_settings[global_section_space]" value="<?php echo absint($settings['global_section_space']); ?>" data-design-field="global_section_space"><small>px</small></label>
                            <label><span>Section · Tablet</span><input type="number" min="16" max="140" name="wpst_settings[global_section_space_tablet]" value="<?php echo absint($settings['global_section_space_tablet']); ?>" data-design-field="global_section_space_tablet"><small>px</small></label>
                            <label><span>Section · Mobil</span><input type="number" min="12" max="100" name="wpst_settings[global_section_space_mobile]" value="<?php echo absint($settings['global_section_space_mobile']); ?>" data-design-field="global_section_space_mobile"><small>px</small></label>
                        </div>
                        <div class="wpst-token-subhead"><strong>Spacing Scale</strong><small>Widget ve şablonlarda ortak boşluk adımları.</small></div>
                        <div class="wpst-global-fields is-three">
                            <?php foreach(array('global_space_xs'=>'XS','global_space_sm'=>'SM','global_space_md'=>'MD','global_space_lg'=>'LG','global_space_xl'=>'XL','global_space_xxl'=>'2XL') as $key=>$label): ?>
                            <label><span><?php echo esc_html($label); ?></span><input type="number" min="0" max="160" name="wpst_settings[<?php echo esc_attr($key); ?>]" value="<?php echo absint($settings[$key]); ?>" data-design-field="<?php echo esc_attr($key); ?>"><small>px</small></label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="wpst-global-card">
                        <div class="wpst-global-card-head"><div><span class="wpst-card-kicker">SHAPE</span><h3>Radius & Surface</h3><p>Kart, küçük eleman, modal ve büyük yüzeyler için tutarlı köşe sistemi.</p></div></div>
                        <div class="wpst-radius-demo-grid">
                            <?php foreach(array('global_radius_sm'=>'Small','global_radius_md'=>'Medium','global_radius_lg'=>'Large','global_radius_xl'=>'X-Large') as $key=>$label): ?>
                            <label><i data-radius-preview="<?php echo esc_attr($key); ?>"></i><span><?php echo esc_html($label); ?></span><div><input type="number" min="0" max="96" name="wpst_settings[<?php echo esc_attr($key); ?>]" value="<?php echo absint($settings[$key]); ?>" data-design-field="<?php echo esc_attr($key); ?>"><small>px</small></div></label>
                            <?php endforeach; ?>
                        </div>
                        <div class="wpst-global-fields is-three">
                            <label><span>Varsayılan Kart Radius</span><input type="number" min="0" max="60" name="wpst_settings[global_card_radius]" value="<?php echo absint($settings['global_card_radius']); ?>" data-design-field="global_card_radius"><small>px</small></label>
                            <label><span>Global Gölge</span><select name="wpst_settings[global_shadow]" data-design-field="global_shadow"><option value="none" <?php selected($settings['global_shadow'],'none'); ?>>Gölgesiz</option><option value="soft" <?php selected($settings['global_shadow'],'soft'); ?>>Soft</option><option value="medium" <?php selected($settings['global_shadow'],'medium'); ?>>Medium</option><option value="strong" <?php selected($settings['global_shadow'],'strong'); ?>>Strong</option></select></label>
                            <label><span>Motion</span><select name="wpst_settings[global_motion]" data-design-field="global_motion"><option value="off" <?php selected($settings['global_motion'],'off'); ?>>Kapalı</option><option value="soft" <?php selected($settings['global_motion'],'soft'); ?>>Soft</option><option value="normal" <?php selected($settings['global_motion'],'normal'); ?>>Normal</option><option value="dynamic" <?php selected($settings['global_motion'],'dynamic'); ?>>Dynamic</option></select></label>
                        </div>
                    </div>

                    <div class="wpst-global-card">
                        <div class="wpst-global-card-head"><div><span class="wpst-card-kicker">TYPE SCALE</span><h3>Tipografi Sistemi</h3><p>H1–H6 değerlerini masaüstü, tablet ve mobil için ayrı yönetin.</p></div></div>
                        <div class="wpst-global-fields is-two">
                            <label><span>Gövde Fontu</span><select name="wpst_settings[global_body_font]" data-design-field="global_body_font"><?php foreach(array('system'=>'Sistem Fontu','inter'=>'Inter','manrope'=>'Manrope','dmsans'=>'DM Sans','plusjakarta'=>'Plus Jakarta Sans','outfit'=>'Outfit','sora'=>'Sora','spacegrotesk'=>'Space Grotesk','urbanist'=>'Urbanist','figtree'=>'Figtree','worksans'=>'Work Sans','nunitosans'=>'Nunito Sans','sourcesans3'=>'Source Sans 3','poppins'=>'Poppins','montserrat'=>'Montserrat','roboto'=>'Roboto','opensans'=>'Open Sans','lato'=>'Lato','playfair'=>'Playfair Display','cormorant'=>'Cormorant Garamond','custom'=>'Özel Font') as $k=>$v): ?><option value="<?php echo esc_attr($k); ?>" <?php selected($settings['global_body_font'],$k); ?>><?php echo esc_html($v); ?></option><?php endforeach; ?></select></label>
                            <label><span>Başlık Fontu</span><select name="wpst_settings[global_heading_font]" data-design-field="global_heading_font"><?php foreach(array('system'=>'Sistem Fontu','inter'=>'Inter','manrope'=>'Manrope','dmsans'=>'DM Sans','plusjakarta'=>'Plus Jakarta Sans','outfit'=>'Outfit','sora'=>'Sora','spacegrotesk'=>'Space Grotesk','urbanist'=>'Urbanist','figtree'=>'Figtree','worksans'=>'Work Sans','nunitosans'=>'Nunito Sans','sourcesans3'=>'Source Sans 3','poppins'=>'Poppins','montserrat'=>'Montserrat','roboto'=>'Roboto','opensans'=>'Open Sans','lato'=>'Lato','playfair'=>'Playfair Display','cormorant'=>'Cormorant Garamond','custom'=>'Özel Font') as $k=>$v): ?><option value="<?php echo esc_attr($k); ?>" <?php selected($settings['global_heading_font'],$k); ?>><?php echo esc_html($v); ?></option><?php endforeach; ?></select></label>
                            <label><span>Özel Gövde Font Adı</span><input type="text" name="wpst_settings[global_custom_body_font]" value="<?php echo esc_attr($settings['global_custom_body_font']); ?>" placeholder="Örn. MyBrand Sans"><small>Tema/eklenti tarafından yüklenen font ailesi adı</small></label>
                            <label><span>Özel Başlık Font Adı</span><input type="text" name="wpst_settings[global_custom_heading_font]" value="<?php echo esc_attr($settings['global_custom_heading_font']); ?>" placeholder="Örn. MyBrand Display"><small>Tema/eklenti tarafından yüklenen font ailesi adı</small></label>
                            <label class="wpst-font-load-toggle"><span>Modern Fontları Otomatik Yükle</span><input type="hidden" name="wpst_settings[global_google_fonts]" value="0"><input type="checkbox" name="wpst_settings[global_google_fonts]" value="1" <?php checked(!empty($settings['global_google_fonts'])); ?>><small>Seçilen hazır fontları Google Fonts üzerinden yalnız gerektiğinde yükler. Özel Font seçimlerinde yükleme yapmaz.</small></label>
                        </div>
                        <div class="wpst-global-fields">
                            <label><span>Gövde Boyutu</span><input type="number" min="13" max="22" name="wpst_settings[global_base_font_size]" value="<?php echo absint($settings['global_base_font_size']); ?>" data-design-field="global_base_font_size"><small>px</small></label>
                            <label><span>Gövde · Tablet</span><input type="number" min="12" max="22" name="wpst_settings[global_base_font_size_tablet]" value="<?php echo absint($settings['global_base_font_size_tablet']); ?>" data-design-field="global_base_font_size_tablet"><small>px</small></label>
                            <label><span>Gövde · Mobil</span><input type="number" min="12" max="22" name="wpst_settings[global_base_font_size_mobile]" value="<?php echo absint($settings['global_base_font_size_mobile']); ?>" data-design-field="global_base_font_size_mobile"><small>px</small></label>
                            <label><span>Gövde Satır</span><input type="number" min="1.2" max="2.2" step="0.05" name="wpst_settings[global_body_line_height]" value="<?php echo esc_attr($settings['global_body_line_height']); ?>" data-design-field="global_body_line_height"></label>
                            <label><span>Başlık Ağırlığı</span><input type="number" min="400" max="900" step="100" name="wpst_settings[global_heading_weight]" value="<?php echo absint($settings['global_heading_weight']); ?>" data-design-field="global_heading_weight"></label>
                            <label><span>Başlık Satır</span><input type="number" min="0.9" max="1.6" step="0.05" name="wpst_settings[global_heading_line_height]" value="<?php echo esc_attr($settings['global_heading_line_height']); ?>" data-design-field="global_heading_line_height"></label>
                            <label><span>Harf Aralığı</span><input type="number" min="-0.08" max="0.12" step="0.01" name="wpst_settings[global_heading_letter_spacing]" value="<?php echo esc_attr($settings['global_heading_letter_spacing']); ?>" data-design-field="global_heading_letter_spacing"><small>em</small></label>
                        </div>
                        <div class="wpst-type-scale-table">
                            <div class="wpst-type-scale-head"><span>Başlık</span><b>Masaüstü</b><b>Tablet</b><b>Mobil</b></div>
                            <?php
                            $type_defaults=array(
                                'h1'=>array('H1','global_h1_size','global_h1_tablet','global_h1_mobile'),
                                'h2'=>array('H2','global_h2_size','global_h2_tablet','global_h2_mobile'),
                                'h3'=>array('H3','global_h3_size','global_h3_tablet','global_h3_mobile'),
                                'h4'=>array('H4','global_h4_size','global_h4_tablet','global_h4_mobile'),
                                'h5'=>array('H5','global_h5_size','global_h5_tablet','global_h5_mobile'),
                                'h6'=>array('H6','global_h6_size','global_h6_tablet','global_h6_mobile')
                            );
                            foreach($type_defaults as $tag=>$vals):
                            ?>
                            <div class="wpst-type-scale-row"><span><i><?php echo esc_html($vals[0]); ?></i><em data-type-preview="<?php echo esc_attr($tag); ?>">Aa</em></span><?php for($x=1;$x<=3;$x++): ?><label><input type="number" min="12" max="96" name="wpst_settings[<?php echo esc_attr($vals[$x]); ?>]" value="<?php echo absint($settings[$vals[$x]]); ?>" data-design-field="<?php echo esc_attr($vals[$x]); ?>"><small>px</small></label><?php endfor; ?></div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="wpst-global-card">
                        <div class="wpst-global-card-head"><div><span class="wpst-card-kicker">BUTTONS</span><h3>Buton Sistemi</h3><p>Primary ve Secondary butonları global olarak standartlaştırın.</p></div></div>
                        <div class="wpst-button-token-grid">
                            <?php foreach(array(
                                'global_button_bg'=>'Primary Arka Plan','global_button_text'=>'Primary Yazı','global_button_hover_bg'=>'Primary Hover','global_button_hover_text'=>'Hover Yazı',
                                'global_secondary_button_bg'=>'Secondary Arka Plan','global_secondary_button_text'=>'Secondary Yazı','global_secondary_button_border'=>'Secondary Border',
                                'global_secondary_button_hover_bg'=>'Secondary Hover','global_secondary_button_hover_text'=>'Secondary Hover Yazı'
                            ) as $key=>$label): ?>
                            <label><span><?php echo esc_html($label); ?></span><div class="wpst-global-color"><input type="color" name="wpst_settings[<?php echo esc_attr($key); ?>]" value="<?php echo esc_attr($settings[$key]); ?>" data-design-field="<?php echo esc_attr($key); ?>"><code><?php echo esc_html($settings[$key]); ?></code></div></label>
                            <?php endforeach; ?>
                        </div>
                        <div class="wpst-global-fields">
                            <label><span>Buton Yüksekliği</span><input type="number" min="36" max="72" name="wpst_settings[global_button_height]" value="<?php echo absint($settings['global_button_height']); ?>" data-design-field="global_button_height"><small>px</small></label>
                            <label><span>Yatay İç Boşluk</span><input type="number" min="8" max="64" name="wpst_settings[global_button_padding_x]" value="<?php echo absint($settings['global_button_padding_x']); ?>" data-design-field="global_button_padding_x"><small>px</small></label>
                            <label><span>Buton Radius</span><input type="number" min="0" max="50" name="wpst_settings[global_button_radius]" value="<?php echo absint($settings['global_button_radius']); ?>" data-design-field="global_button_radius"><small>px</small></label>
                            <label><span>Ağırlık</span><input type="number" min="400" max="900" step="100" name="wpst_settings[global_button_weight]" value="<?php echo absint($settings['global_button_weight']); ?>" data-design-field="global_button_weight"></label>
                            <label><span>Harf Aralığı</span><input type="number" min="-0.04" max="0.16" step="0.01" name="wpst_settings[global_button_letter_spacing]" value="<?php echo esc_attr($settings['global_button_letter_spacing']); ?>" data-design-field="global_button_letter_spacing"><small>em</small></label>
                            <label><span>Metin</span><select name="wpst_settings[global_button_text_transform]" data-design-field="global_button_text_transform"><option value="none" <?php selected($settings['global_button_text_transform'],'none'); ?>>Normal</option><option value="uppercase" <?php selected($settings['global_button_text_transform'],'uppercase'); ?>>BÜYÜK HARF</option><option value="capitalize" <?php selected($settings['global_button_text_transform'],'capitalize'); ?>>Baş Harfler Büyük</option></select></label>
                        </div>
                    </div>
                </div>

                <div class="wpst-global-card wpst-global-card-wide">
                    <div class="wpst-global-card-head"><div><span>06</span><strong>Link, Form & Surface</strong></div><p>Site genelindeki bağlantılar, form alanları ve yüzey tokenları.</p></div>
                    <div class="wpst-global-fields wpst-global-fields-4">
                        <label><span>Link Rengi</span><input type="color" name="wpst_settings[global_link]" value="<?php echo esc_attr($settings['global_link']); ?>" data-design-field="global_link"></label>
                        <label><span>Link Hover</span><input type="color" name="wpst_settings[global_link_hover]" value="<?php echo esc_attr($settings['global_link_hover']); ?>" data-design-field="global_link_hover"></label>
                        <label class="wpst-check"><input type="checkbox" name="wpst_settings[global_link_underline]" value="1" <?php checked(!empty($settings['global_link_underline'])); ?>><span>Linklerde alt çizgi</span></label>
                        <label class="wpst-check"><input type="checkbox" name="wpst_settings[global_link_hover_underline]" value="1" <?php checked(!empty($settings['global_link_hover_underline'])); ?>><span>Hover'da alt çizgi</span></label>
                        <label><span>Input Arka Plan</span><input type="color" name="wpst_settings[global_input_bg]" data-design-field="global_input_bg" value="<?php echo esc_attr($settings['global_input_bg']); ?>"></label>
                        <label><span>Input Yazı</span><input type="color" name="wpst_settings[global_input_text]" data-design-field="global_input_text" value="<?php echo esc_attr($settings['global_input_text']); ?>"></label>
                        <label><span>Input Border</span><input type="color" name="wpst_settings[global_input_border]" data-design-field="global_input_border" value="<?php echo esc_attr($settings['global_input_border']); ?>"></label>
                        <label><span>Input Focus</span><input type="color" name="wpst_settings[global_input_focus]" data-design-field="global_input_focus" value="<?php echo esc_attr($settings['global_input_focus']); ?>"></label>
                        <label><span>Input Yüksekliği</span><input type="number" min="36" max="72" name="wpst_settings[global_input_height]" data-design-field="global_input_height" value="<?php echo absint($settings['global_input_height']); ?>"><small>px</small></label>
                        <label><span>Input Radius</span><input type="number" min="0" max="40" name="wpst_settings[global_input_radius]" data-design-field="global_input_radius" value="<?php echo absint($settings['global_input_radius']); ?>"><small>px</small></label>
                        <label><span>Border Kalınlığı</span><input type="number" min="0" max="4" name="wpst_settings[global_input_border_width]" data-design-field="global_input_border_width" value="<?php echo absint($settings['global_input_border_width']); ?>"><small>px</small></label>
                        <label><span>Alternatif Surface</span><input type="color" name="wpst_settings[global_surface_alt]" data-design-field="global_surface_alt" value="<?php echo esc_attr($settings['global_surface_alt']); ?>"></label>
                        <label><span>Koyu Surface</span><input type="color" name="wpst_settings[global_surface_dark]" data-design-field="global_surface_dark" value="<?php echo esc_attr($settings['global_surface_dark']); ?>"></label>
                    </div>
                </div>

                <aside class="wpst-global-preview">
                    <div class="wpst-global-preview-sticky">
                        <span class="wpst-global-preview-label">CANLI DESIGN SYSTEM</span>
                        <div class="wpst-design-preview-v2" data-design-preview>
                            <div class="wpst-design-preview-nav"><i></i><span></span><span></span><b></b></div>
                            <div class="wpst-design-preview-hero">
                                <small>WPSoft Design System 2.0</small>
                                <h3>Modern dijital deneyim</h3>
                                <p>Preset ve global token değişikliklerini kaydetmeden önce burada görün.</p>
                                <div class="wpst-design-preview-actions"><a href="#" onclick="return false">Primary</a><a class="is-secondary" href="#" onclick="return false">Secondary</a></div>
                            </div>
                            <div class="wpst-design-preview-cards"><article><i>01</i><strong>Global</strong><span>Tek tasarım dili</span></article><article><i>02</i><strong>Responsive</strong><span>3 cihaz ölçüsü</span></article></div>
                        </div>
                        <div class="wpst-global-note"><strong>Global + Lokal Mantığı</strong><p>WPSoft widgetlar bu tokenları varsayılan olarak kullanır. Elementor içindeki Biçim ayarına özel değer verdiğinizde widgetın lokal ayarı global değerin üzerine çıkar.</p></div>
                    </div>
                </aside>
            </div>
        </section>
        <?php
    }

    private function builder_panel( $type, $title, $settings, $templates ) {
        $is_header = 'header' === $type;
        $sections = max( 1, min( 4, absint( $settings[ $type . '_sections' ] ) ) );
        ?>
        <section class="wpst-panel <?php echo $is_header ? 'is-active' : ''; ?>" data-panel="<?php echo esc_attr( $type ); ?>">
            <div class="wpst-toolbar wpst-builder-toolbar">
                <div class="wpst-builder-title">
                    <span class="wpst-builder-kicker"><?php echo $is_header ? 'HEADER BUILDER' : 'FOOTER BUILDER'; ?></span>
                    <h2><?php echo esc_html( $title ); ?> Canlı Düzenleme</h2>
                    <p>Blokları soldan sürükleyin, ortadaki canlı önizlemede yerleştirin ve sağdaki tasarım ayarlarından düzenleyin.</p>
                </div>
                <div class="wpst-builder-status">
                    <span class="wpst-builder-save-state" data-wpst-save-state><i></i><b>Kaydedildi</b></span>
                    <label class="wpst-switch"><input type="checkbox" name="wpst_settings[<?php echo esc_attr( $type ); ?>_enabled]" value="1" <?php checked( ! empty( $settings[ $type . '_enabled' ] ) ); ?>><span></span><b>Aktif</b></label>
                </div>
            </div>
            <div class="wpst-mode-switch wpst-builder-mode-switch">
                <label><input type="radio" name="wpst_settings[<?php echo esc_attr( $type ); ?>_mode]" value="builder" <?php checked( $settings[ $type . '_mode' ], 'builder' ); ?>><span><i>⚡</i><b>Canlı Builder</b><small>Sürükle-bırak hızlı düzenleme</small></span></label>
                <label><input type="radio" name="wpst_settings[<?php echo esc_attr( $type ); ?>_mode]" value="elementor" <?php checked( $settings[ $type . '_mode' ], 'elementor' ); ?>><span><i>E</i><b>Elementor Şablonu</b><small>Hazır Elementor şablonu kullan</small></span></label>
            </div>
            <div class="wpst-mode-content" data-mode-content="builder">
                <?php if ( $is_header ) : ?>
                <div class="wpst-header-builder-v3bar">
                    <div class="wpst-hb3-title"><strong>Header Builder</strong><small>Satırları ve elementleri sürükleyerek header yapınızı oluşturun.</small></div>
                    <div class="wpst-hb3-device">
                        <button type="button" class="is-active" data-hb-device="desktop"><span class="dashicons dashicons-desktop"></span>Desktop</button>
                        <button type="button" data-hb-device="tablet"><span class="dashicons dashicons-tablet"></span>Tablet</button>
                        <button type="button" data-hb-device="mobile"><span class="dashicons dashicons-smartphone"></span>Mobile</button>
                    </div>
                    <div class="wpst-hb3-state">
                        <button type="button" class="is-active" data-hb-state="normal">Normal</button>
                        <button type="button" data-hb-state="transparent">Transparent</button>
                        <button type="button" data-hb-state="sticky">Sticky</button>
                    </div>
                </div>
                <div class="wpst-hb3-quickbar">
                    <div class="wpst-hb3-quickbar-label"><strong>Hızlı Başlangıç</strong><small>Hazır ayarları tek tıkla uygulayın; canlı önizleme ve frontend aynı sonucu kullanır.</small></div>
                    <div class="wpst-hb3-presets">
                        <?php $wpst_quick_preset = in_array($settings['header_preset'],array('minimal','corporate','centered','transparent','floating-boxed'),true) ? $settings['header_preset'] : 'current'; ?>
                        <button type="button" class="<?php echo $wpst_quick_preset==='current'?'is-active':''; ?>" data-hb-preset="current"><span></span>Mevcut</button>
                        <button type="button" class="<?php echo $wpst_quick_preset==='minimal'?'is-active':''; ?>" data-hb-preset="minimal"><span></span>Minimal</button>
                        <button type="button" class="<?php echo $wpst_quick_preset==='corporate'?'is-active':''; ?>" data-hb-preset="corporate"><span></span>Corporate</button>
                        <button type="button" class="<?php echo $wpst_quick_preset==='centered'?'is-active':''; ?>" data-hb-preset="centered"><span></span>Centered</button>
                        <button type="button" class="<?php echo $wpst_quick_preset==='transparent'?'is-active':''; ?>" data-hb-preset="transparent"><span></span>Transparent</button>
                        <button type="button" class="<?php echo $wpst_quick_preset==='floating-boxed'?'is-active':''; ?>" data-hb-preset="floating-boxed"><span></span>Floating Boxed</button>
                    </div>
                    <button type="button" class="wpst-hb3-focus" data-hb-focus><span class="dashicons dashicons-editor-expand"></span>Önizlemeyi Büyüt</button>
                </div>
                <div class="wpst-layout-presets wpst-header-rows-intro">
                    <input type="hidden" name="wpst_settings[header_builder_version]" value="2" data-header-builder-version data-saved-version="<?php echo absint($settings['header_builder_version']); ?>">
                    <input type="hidden" name="wpst_settings[header_sections]" value="<?php echo $sections; ?>" data-section-count-input>
                </div>
                <?php else : ?>
                <div class="wpst-layout-presets wpst-footer-rows-intro">
                    <div class="wpst-layout-preset-head"><div><strong>Footer Builder 2.0</strong><small>Üst Footer, Ana Footer ve Alt Footer satırları. Her satır Sol / Orta / Sağ alanlarından oluşur.</small></div><span>3 SATIR · 9 ALAN</span></div>
                    <div class="wpst-header-row-map wpst-footer-row-map"><span><b>Üst Footer</b><i>Sol</i><i>Orta</i><i>Sağ</i></span><span class="is-main"><b>Ana Footer</b><i>Sol</i><i>Orta</i><i>Sağ</i></span><span><b>Alt Footer</b><i>Sol</i><i>Orta</i><i>Sağ</i></span></div>
                    <input type="hidden" name="wpst_settings[footer_builder_version]" value="2" data-footer-builder-version data-saved-version="<?php echo absint($settings['footer_builder_version']); ?>">
                    <input type="hidden" name="wpst_settings[footer_sections]" value="<?php echo $sections; ?>" data-section-count-input>
                </div>
                <?php endif; ?>
                <?php if ( ! $is_header ) : ?>
                <div class="wpst-footer-builder-v3bar">
                    <div class="wpst-fb3-title">
                        <strong>Footer Builder</strong>
                        <small>Üst, ana ve alt footer satırlarını görsel olarak yönetin.</small>
                    </div>
                    <div class="wpst-fb3-device">
                        <button type="button" class="is-active" data-fb-device="desktop"><span class="dashicons dashicons-desktop"></span>Desktop</button>
                        <button type="button" data-fb-device="tablet"><span class="dashicons dashicons-tablet"></span>Tablet</button>
                        <button type="button" data-fb-device="mobile"><span class="dashicons dashicons-smartphone"></span>Mobile</button>
                    </div>
                    <button type="button" class="wpst-fb3-focus" data-fb-focus><span class="dashicons dashicons-editor-expand"></span>Önizlemeyi Büyüt</button>
                </div>
                <div class="wpst-fb3-quickbar">
                    <div class="wpst-fb3-quickbar-label"><strong>Footer Stilleri</strong><small>Mevcut ayarları bozmadan önizleme presetleri.</small></div>
                    <div class="wpst-fb3-presets">
                        <button type="button" class="is-active" data-fb-preset="current"><span></span>Mevcut</button>
                        <button type="button" data-fb-preset="corporate"><span></span>Corporate</button>
                        <button type="button" data-fb-preset="minimal"><span></span>Minimal</button>
                        <button type="button" data-fb-preset="dark"><span></span>Dark</button>
                        <button type="button" data-fb-preset="glass"><span></span>Glass</button>
                    </div>
                </div>
                <?php endif; ?>
                <div class="wpst-builder-grid">
                    <aside class="wpst-palette">
                        <div class="wpst-palette-switch">
                            <button type="button" class="is-active" data-palette-tab="structure"><span class="dashicons dashicons-screenoptions"></span>Yapı</button>
                            <button type="button" data-palette-tab="blocks"><span class="dashicons dashicons-layout"></span>Bloklar</button>
                        </div>
                        <div class="wpst-palette-pane is-active" data-palette-pane="structure">
                            <div class="wpst-palette-head"><span>YAPI</span><strong>Bölümler</strong></div>
                            <div class="wpst-section-list" data-section-list="<?php echo esc_attr( $type ); ?>"></div>
                        </div>
                        <div class="wpst-palette-pane" data-palette-pane="blocks">
                            <div class="wpst-palette-head wpst-palette-block-head"><span>ELEMENTLER</span><strong>Blok Kütüphanesi</strong></div>
                            <div class="wpst-block-search"><span class="dashicons dashicons-search"></span><input type="search" placeholder="Element ara..." data-wpst-block-search></div>
                            <?php if ( $is_header ) : ?>
                            <div class="wpst-element-groups">
                                <?php
                                $wpst_groups = array(
                                    'Temel'=>array('logo','text','html','button','spacer'),
                                    'Navigasyon'=>array('menu','search'),
                                    'E-Ticaret'=>array('cart','account'),
                                    'Sosyal'=>array('social'),
                                );
                                $wpst_blocks = $this->available_blocks($type);
                                foreach($wpst_groups as $group_label=>$group_keys):
                                ?>
                                <div class="wpst-element-group">
                                    <div class="wpst-element-group-title"><?php echo esc_html($group_label); ?><span class="dashicons dashicons-arrow-down-alt2"></span></div>
                                    <div class="wpst-block-grid">
                                    <?php foreach($group_keys as $key): if(empty($wpst_blocks[$key])) continue; ?>
                                        <button type="button" class="wpst-block-add" draggable="true" data-block="<?php echo esc_attr($key); ?>"><span class="dashicons dashicons-move"></span><?php echo esc_html($wpst_blocks[$key]); ?></button>
                                    <?php endforeach; ?>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php else : ?>
                            <?php if ( ! $is_header ) : ?>
                            <div class="wpst-footer-element-groups">
                                <?php
                                $wpst_footer_groups = array(
                                    'Marka'=>array('logo','text','html'),
                                    'Navigasyon'=>array('menu','button'),
                                    'İletişim'=>array('social'),
                                    'Yardımcı'=>array('spacer'),
                                );
                                $wpst_footer_blocks = $this->available_blocks($type);
                                foreach($wpst_footer_groups as $group_label=>$group_keys):
                                ?>
                                <div class="wpst-footer-element-group">
                                    <div class="wpst-footer-element-group-title"><?php echo esc_html($group_label); ?><span class="dashicons dashicons-arrow-down-alt2"></span></div>
                                    <div class="wpst-block-grid">
                                    <?php foreach($group_keys as $key): if(empty($wpst_footer_blocks[$key])) continue; ?>
                                        <button type="button" class="wpst-block-add" draggable="true" data-block="<?php echo esc_attr($key); ?>"><span class="dashicons dashicons-move"></span><?php echo esc_html($wpst_footer_blocks[$key]); ?></button>
                                    <?php endforeach; ?>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php else : ?>
                            <p class="wpst-palette-tip">Tıklayarak ekleyin veya önizlemedeki bölüme sürükleyin.</p>
                            <div class="wpst-block-grid"><?php foreach ( $this->available_blocks( $type ) as $key => $label ) : ?><button type="button" class="wpst-block-add" draggable="true" data-block="<?php echo esc_attr( $key ); ?>"><span class="dashicons dashicons-move"></span><?php echo esc_html( $label ); ?></button><?php endforeach; ?></div>
                            <?php endif; ?>
                            <?php endif; ?>
                        </div></aside>
                    <div class="wpst-workspace">
                        <div class="wpst-preview-label">
                            <div class="wpst-preview-title"><span>Canlı Önizleme</span><em><i></i> Canlı</em></div>
                            <div class="wpst-preview-tools">
                                <div class="wpst-device-switch">
                                    <button type="button" class="is-active" data-device="desktop"><span class="dashicons dashicons-desktop"></span> Masaüstü</button>
                                    <button type="button" data-device="tablet"><span class="dashicons dashicons-tablet"></span> Tablet</button>
                                    <button type="button" data-device="mobile"><span class="dashicons dashicons-smartphone"></span> Mobil</button>
                                </div>
                                <div class="wpst-preview-actions">
                                    <button type="button" data-wpst-undo title="Geri Al"><span class="dashicons dashicons-undo"></span></button>
                                    <button type="button" data-wpst-redo title="Yinele"><span class="dashicons dashicons-redo"></span></button>
                                    <span class="wpst-preview-separator"></span>
                                    <button type="button" data-wpst-zoom-out title="Uzaklaştır"><span class="dashicons dashicons-minus"></span></button>
                                    <span data-wpst-zoom-value>100%</span>
                                    <button type="button" data-wpst-zoom-in title="Yaklaştır"><span class="dashicons dashicons-plus-alt2"></span></button>
                                    <span class="wpst-preview-separator"></span>
                                    <button type="button" data-wpst-preview-refresh title="Önizlemeyi Yenile"><span class="dashicons dashicons-update"></span></button>
                                    <button type="button" data-wpst-panel-collapse title="Ayar Panelini Gizle/Göster"><span class="dashicons dashicons-arrow-left-alt2"></span></button>
                                    <button type="button" data-wpst-focus-mode title="Önizlemeyi Genişlet"><span class="dashicons dashicons-editor-expand"></span><b>Genişlet</b></button>
                                </div>
                            </div>
                        </div>
                        <div class="wpst-preview-stage" data-preview-type="<?php echo esc_attr( $type ); ?>">
                            <div class="wpst-canvas" data-builder="<?php echo esc_attr( $type ); ?>"></div>
                        </div>
                        <textarea hidden name="wpst_settings[<?php echo esc_attr( $type ); ?>_layout]" data-layout-input="<?php echo esc_attr( $type ); ?>"><?php echo esc_textarea( $settings[ $type . '_layout' ] ); ?></textarea>
                    </div>
                    <aside class="wpst-design">
                        <div class="wpst-inspector-head"><span>INSPECTOR</span><strong>Seçili Alan Ayarları</strong></div>
                        
                        <div class="wpst-inspector-tabs <?php echo $is_header?'wpst-hb3-inspector-tabs':''; ?>">
                            <button type="button" class="is-active" data-inspector="general"><i class="dashicons dashicons-admin-generic"></i><span><?php echo $is_header?'İçerik':'Genel'; ?></span></button>
                            <button type="button" data-inspector="section"><i class="dashicons dashicons-art"></i><span><?php echo $is_header?'Stil':'Satır'; ?></span></button>
                            <button type="button" data-inspector="element"><i class="dashicons dashicons-admin-tools"></i><span><?php echo $is_header?'Gelişmiş':'Eleman'; ?></span></button>
                            <button type="button" data-inspector-view="behavior"><i class="dashicons dashicons-controls-repeat"></i><span>Davranış</span></button>
                            <button type="button" data-inspector-view="mobile"><i class="dashicons dashicons-smartphone"></i><span>Mobil</span></button>
                        </div>
                        <div class="wpst-inspector-pane is-active" data-inspector-pane="general">
                            <?php if(!$is_header): ?>
                            <div class="wpst-fb3-selection-card">
                                <span class="dashicons dashicons-screenoptions"></span>
                                <div><strong>Footer Ayarları</strong><small>Bir satır veya element seçin; ilgili ayarlar burada görünür.</small></div>
                            </div>
                            <?php endif; ?>
                            <?php if($is_header): ?>
                            <div class="wpst-hb3-selection-card">
                                <span class="dashicons dashicons-admin-generic"></span>
                                <div><strong>Header Ayarları</strong><small>Bir satır veya element seçtiğinizde ilgili ayarlar burada öne çıkar.</small></div>
                            </div>
                            <?php endif; ?><h3>Header Ayarları</h3>
<div class="wpst-header-inspector-intro">
    <strong>Hızlı Düzen</strong>
    <small>Logo, satırlar, görünüm, menü, davranış ve mobil ayarları ayrı gruplarda yönetilir.</small>
</div>
<?php if ( $is_header ) : ?>
<details class="wpst-live-settings-details wpst-logo-responsive-settings wpst-inspector-group is-core" open>
    <summary>Logo & Responsive</summary>
    <div class="wpst-logo-mode-card is-normal">
        <div class="wpst-logo-mode-head"><strong>Normal Logo</strong><small>Sayfa ilk açıldığında kullanılan ana logo.</small></div>
        <div class="wpst-logo-control" data-logo-control="header">
            <div class="wpst-logo-preview<?php echo empty($settings['header_logo_id'])?' is-empty':''; ?>"><?php $logo_url=$this->attachment_url($settings['header_logo_id']); ?><img src="<?php echo esc_url($logo_url); ?>" alt="" <?php echo $logo_url?'':'style="display:none"'; ?>><span><?php echo $logo_url?'':'Logo seçilmedi'; ?></span></div>
            <input type="hidden" name="wpst_settings[header_logo_id]" value="<?php echo absint($settings['header_logo_id']); ?>" data-logo-id>
            <div class="wpst-logo-actions"><button type="button" class="button wpst-logo-select">Medya Seç</button><button type="button" class="button-link-delete wpst-logo-remove" <?php echo $logo_url?'':'style="display:none"'; ?>>Kaldır</button></div>
        </div>
        <div class="wpst-logo-responsive-grid">
            <label>Masaüstü Genişliği<input type="number" min="0" max="800" name="wpst_settings[header_logo_width]" value="<?php echo absint($settings['header_logo_width']); ?>"><small>px · 0 = otomatik</small></label>
            <label>Masaüstü Maks. Yükseklik<input type="number" min="0" max="300" name="wpst_settings[header_logo_height]" value="<?php echo absint($settings['header_logo_height']); ?>"><small>px · 0 = otomatik</small></label>
            <label>Mobil Genişliği<input type="number" min="40" max="320" name="wpst_settings[header_mobile_logo_width]" value="<?php echo absint($settings['header_mobile_logo_width']); ?>"><small>px</small></label>
            <label>Mobil Maks. Yükseklik<input type="number" min="20" max="120" name="wpst_settings[header_mobile_logo_height]" value="<?php echo absint($settings['header_mobile_logo_height']); ?>"><small>px</small></label>
        </div>
        <small class="wpst-logo-inherit-note">Tablet, masaüstü logo ölçülerini kullanır; mobil kırılımda mobil ölçülere geçer.</small>
    </div>
    <div class="wpst-logo-mode-card is-scroll">
        <div class="wpst-logo-mode-head"><strong>Scroll Sonrası Logo</strong><small>Sticky/scroll durumunda kullanılacak ayrı logo. Boş bırakılırsa Normal Logo kullanılır.</small></div>
        <div class="wpst-logo-control wpst-scroll-logo-control" data-logo-control="header-scrolled">
            <div class="wpst-logo-preview<?php echo empty($settings['header_scrolled_logo_id'])?' is-empty':''; ?>"><?php $scroll_logo_url=$this->attachment_url($settings['header_scrolled_logo_id']); ?><img src="<?php echo esc_url($scroll_logo_url); ?>" alt="" <?php echo $scroll_logo_url?'':'style="display:none"'; ?>><span><?php echo $scroll_logo_url?'':'Logo seçilmedi'; ?></span></div>
            <input type="hidden" name="wpst_settings[header_scrolled_logo_id]" value="<?php echo absint($settings['header_scrolled_logo_id']); ?>" data-logo-id>
            <div class="wpst-logo-actions"><button type="button" class="button wpst-logo-select">Medya Seç</button><button type="button" class="button-link-delete wpst-logo-remove" <?php echo $scroll_logo_url?'':'style="display:none"'; ?>>Kaldır</button></div>
        </div>
        <div class="wpst-logo-responsive-grid">
            <label>Masaüstü Genişliği<input type="number" min="0" max="800" name="wpst_settings[header_scrolled_logo_width]" value="<?php echo absint($settings['header_scrolled_logo_width']); ?>"><small>px · 0 = normal logodan</small></label>
            <label>Masaüstü Maks. Yükseklik<input type="number" min="0" max="300" name="wpst_settings[header_scrolled_logo_height]" value="<?php echo absint($settings['header_scrolled_logo_height']); ?>"><small>px · 0 = normal logodan</small></label>
            <label>Mobil Genişliği<input type="number" min="40" max="320" name="wpst_settings[header_mobile_logo_scroll_width]" value="<?php echo absint($settings['header_mobile_logo_scroll_width']); ?>"><small>px</small></label>
            <label>Mobil Maks. Yükseklik<input type="number" min="20" max="120" name="wpst_settings[header_mobile_logo_scroll_height]" value="<?php echo absint($settings['header_mobile_logo_scroll_height']); ?>"><small>px</small></label>
        </div>
    </div>
</details>
<?php else : ?>
<div class="wpst-logo-control" data-logo-control="footer"><span class="wpst-control-title">Logo</span><div class="wpst-logo-preview<?php echo empty($settings['footer_logo_id'])?' is-empty':''; ?>"><?php $logo_url=$this->attachment_url($settings['footer_logo_id']); ?><img src="<?php echo esc_url($logo_url); ?>" alt="" <?php echo $logo_url?'':'style="display:none"'; ?>><span><?php echo $logo_url?'':'Logo seçilmedi'; ?></span></div><input type="hidden" name="wpst_settings[footer_logo_id]" value="<?php echo absint($settings['footer_logo_id']); ?>" data-logo-id><div class="wpst-logo-actions"><button type="button" class="button wpst-logo-select">Medya Seç</button><button type="button" class="button-link-delete wpst-logo-remove" <?php echo $logo_url?'':'style="display:none"'; ?>>Kaldır</button></div><div class="wpst-logo-size-grid"><label>Logo Genişliği<input type="number" min="0" max="800" name="wpst_settings[footer_logo_width]" value="<?php echo absint($settings['footer_logo_width']); ?>"><small>px · 0 = otomatik</small></label><label>Logo Yüksekliği<input type="number" min="0" max="300" name="wpst_settings[footer_logo_height]" value="<?php echo absint($settings['footer_logo_height']); ?>"><small>px · 0 = otomatik</small></label></div></div>
<?php endif; ?>
<?php if ( $is_header ) : ?>
    <!-- Legacy header-wide surface/size values are preserved but no longer shown.
         Header Builder 2/3 uses row-level background, text, container and height controls. -->
    <input type="hidden" name="wpst_settings[header_background]" value="<?php echo esc_attr($settings['header_background']); ?>">
    <input type="hidden" name="wpst_settings[header_text_color]" value="<?php echo esc_attr($settings['header_text_color']); ?>">
    <input type="hidden" name="wpst_settings[header_container]" value="<?php echo absint($settings['header_container']); ?>">
    <input type="hidden" name="wpst_settings[header_padding]" value="<?php echo absint($settings['header_padding']); ?>">
<?php else : ?>
    <label>Arka Plan<input type="color" name="wpst_settings[footer_background]" value="<?php echo esc_attr($settings['footer_background']); ?>"></label>
    <label>Yazı Rengi<input type="color" name="wpst_settings[footer_text_color]" value="<?php echo esc_attr($settings['footer_text_color']); ?>"></label>
    <label>İçerik Genişliği<input type="number" min="960" max="1600" name="wpst_settings[footer_container]" value="<?php echo absint($settings['footer_container']); ?>"><small>px</small></label>
    <label>Üst/Alt Boşluk<input type="number" min="0" max="100" name="wpst_settings[footer_padding]" value="<?php echo absint($settings['footer_padding']); ?>"><small>px</small></label>
<?php endif; ?>
<?php if ( $is_header ) : ?>
<div class="wpst-header-row-settings wpst-inspector-group is-rows">
    <div class="wpst-live-settings-title">
        <strong>Header Satırları</strong>
        <small>Main Header ana satırdır; Top/Bottom satırlar opsiyoneldir.</small>
    </div>

    <?php
    $header_rows = array(
        'top'    => array('Top Bar','Üst yardımcı satır','optional'),
        'main'   => array('Main Header','Ana logo · menü · aksiyon satırı','primary'),
        'bottom' => array('Bottom Bar','Alt yardımcı menü / içerik satırı','optional'),
    );
    foreach ( $header_rows as $row_key => $row_meta ) :
        $rp = 'header_row_' . $row_key . '_';
        $is_primary = 'main' === $row_key;
        $row_enabled = $is_primary ? true : !empty($settings['header_row_'.$row_key.'_enabled']);
    ?>
    <details class="wpst-row-settings-card wpst-header-row-card <?php echo $is_primary?'is-primary':'is-optional'; ?>" data-header-row-card="<?php echo esc_attr($row_key); ?>" <?php echo $is_primary?'open':''; ?>>
        <summary>
            <span>
                <b><?php echo esc_html($row_meta[0]); ?></b>
                <small><?php echo esc_html($row_meta[1]); ?></small>
            </span>
            <?php if($is_primary): ?>
                <em class="wpst-row-status is-primary">Ana Satır</em>
            <?php else: ?>
                <em class="wpst-row-status <?php echo $row_enabled?'is-on':'is-off'; ?>" data-row-status><?php echo $row_enabled?'Aktif':'Kapalı'; ?></em>
            <?php endif; ?>
        </summary>

        <?php if(!$is_primary): ?>
        <label class="wpst-check wpst-row-enable">
            <input type="checkbox" name="wpst_settings[header_row_<?php echo esc_attr($row_key); ?>_enabled]" value="1" <?php checked($row_enabled); ?> data-header-row-enable="<?php echo esc_attr($row_key); ?>">
            <span><?php echo esc_html($row_meta[0]); ?> satırını kullan</span>
        </label>
        <?php endif; ?>

        <div class="wpst-header-row-options" data-row-options <?php echo (!$is_primary && !$row_enabled)?'hidden':''; ?>>
            <div class="wpst-row-section-label"><strong>Yükseklik</strong><small>Cihaza ve scroll durumuna göre bağımsız.</small></div>
            <div class="wpst-row-setting-grid is-three">
                <label>Masaüstü<input type="number" min="24" max="160" name="wpst_settings[<?php echo esc_attr($rp); ?>height_desktop]" value="<?php echo absint($settings[$rp.'height_desktop']); ?>"><small>px</small></label>
                <label>Tablet<input type="number" min="24" max="140" name="wpst_settings[<?php echo esc_attr($rp); ?>height_tablet]" value="<?php echo absint($settings[$rp.'height_tablet']); ?>"><small>px</small></label>
                <label>Mobil<input type="number" min="24" max="120" name="wpst_settings[<?php echo esc_attr($rp); ?>height_mobile]" value="<?php echo absint($settings[$rp.'height_mobile']); ?>"><small>px</small></label>
            </div>
            <div class="wpst-row-setting-grid">
                <label>Sticky / Scroll<input type="number" min="24" max="140" name="wpst_settings[<?php echo esc_attr($rp); ?>height_scrolled]" value="<?php echo absint($settings[$rp.'height_scrolled']); ?>"><small>px</small></label>
            </div>

            <div class="wpst-row-section-label"><strong>Görünüm</strong><small>Satıra özel yüzey ve ayırıcı.</small></div>
            <div class="wpst-row-setting-grid is-three">
                <label>Arka Plan<input type="color" name="wpst_settings[<?php echo esc_attr($rp); ?>background]" value="<?php echo esc_attr($settings[$rp.'background']); ?>" data-wpst-row-appearance="background"></label>
                <label>Yazı Rengi<input type="color" name="wpst_settings[<?php echo esc_attr($rp); ?>text_color]" value="<?php echo esc_attr($settings[$rp.'text_color']); ?>" data-wpst-row-appearance="text"></label>
                <label>Border Rengi<input type="color" name="wpst_settings[<?php echo esc_attr($rp); ?>border_color]" value="<?php echo esc_attr($settings[$rp.'border_color']); ?>" data-wpst-row-appearance="border"></label>
            </div>
            <div class="wpst-row-setting-grid is-three">
                <label>Border<input type="number" min="0" max="4" name="wpst_settings[<?php echo esc_attr($rp); ?>border_width]" value="<?php echo absint($settings[$rp.'border_width']); ?>"><small>px</small></label>
                <label>Container<input type="number" min="720" max="1920" name="wpst_settings[<?php echo esc_attr($rp); ?>container]" value="<?php echo absint($settings[$rp.'container']); ?>"><small>px</small></label>
                <label class="wpst-check wpst-row-fullwidth"><input type="checkbox" name="wpst_settings[<?php echo esc_attr($rp); ?>full_width]" value="1" <?php checked(!empty($settings[$rp.'full_width'])); ?>><span>Tam genişlik içerik</span></label>
            </div>

            <div class="wpst-row-visibility">
                <strong>Cihazlarda Göster</strong>
                <div>
                    <?php foreach(array('desktop'=>'Masaüstü','tablet'=>'Tablet','mobile'=>'Mobil') as $dev=>$dev_label): ?>
                    <label class="wpst-device-check"><input type="checkbox" name="wpst_settings[<?php echo esc_attr($rp); ?>show_<?php echo esc_attr($dev); ?>]" value="1" <?php checked(!empty($settings[$rp.'show_'.$dev])); ?>><span><?php echo esc_html($dev_label); ?></span></label>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </details>
    <?php endforeach; ?>
</div>
<div class="wpst-live-header-settings wpst-inspector-group is-behavior">
    <div class="wpst-live-settings-title"><strong>Header Davranışı</strong><small>Değişiklikler canlı önizlemede görünür.</small></div>
    <details class="wpst-live-settings-details wpst-header-behavior-group" open>
        <summary>Sticky & Scroll</summary>
        <label class="wpst-check"><input type="checkbox" name="wpst_settings[header_sticky]" value="1" <?php checked( ! empty( $settings['header_sticky'] ) ); ?>><span>Yapışkan menü</span></label>
        <label>Yapışkan Davranışı<select name="wpst_settings[header_sticky_mode]"><option value="always" <?php selected( $settings['header_sticky_mode'], 'always' ); ?>>Her zaman yapışkan</option><option value="scroll" <?php selected( $settings['header_sticky_mode'], 'scroll' ); ?>>Belirlenen noktadan sonra</option></select></label>
        <div class="wpst-sticky-row-picks"><span>Sticky satırlar</span><label class="wpst-check"><input type="checkbox" name="wpst_settings[header_sticky_top]" value="1" <?php checked(!empty($settings['header_sticky_top'])); ?>><span>Top Bar</span></label><label class="wpst-check"><input type="checkbox" name="wpst_settings[header_sticky_main]" value="1" <?php checked(!empty($settings['header_sticky_main'])); ?>><span>Main Header</span></label><label class="wpst-check"><input type="checkbox" name="wpst_settings[header_sticky_bottom]" value="1" <?php checked(!empty($settings['header_sticky_bottom'])); ?>><span>Bottom Bar</span></label></div>
        <label>Scroll Eşiği<input type="number" min="0" max="500" name="wpst_settings[header_scroll_threshold]" value="<?php echo absint( $settings['header_scroll_threshold'] ); ?>"><small>px</small></label>
        <label class="wpst-check"><input type="checkbox" name="wpst_settings[header_shrink]" value="1" <?php checked( ! empty( $settings['header_shrink'] ) ); ?>><span>Kaydırınca header küçülsün</span></label>
        <label class="wpst-check"><input type="checkbox" name="wpst_settings[header_hide_on_scroll]" value="1" <?php checked(!empty($settings['header_hide_on_scroll'])); ?>><span>Aşağı kaydırırken gizle, yukarı kaydırırken göster</span></label>
        <label>Gizleme Hassasiyeti<input type="number" min="2" max="80" name="wpst_settings[header_hide_scroll_delta]" value="<?php echo absint($settings['header_hide_scroll_delta']); ?>"><small>px hareket farkı</small></label>
    </details>

    <details class="wpst-live-settings-details wpst-header-glass-group">
        <summary>Transparent & Glass</summary>
        <label class="wpst-check"><input type="checkbox" name="wpst_settings[header_transparent]" value="1" <?php checked( ! empty( $settings['header_transparent'] ) ); ?>><span>Şeffaf header</span></label>
        <label class="wpst-check"><input type="checkbox" name="wpst_settings[header_transparent_overlay]" value="1" <?php checked( ! empty( $settings['header_transparent_overlay'] ) ); ?>><span>Hero / içeriğin üzerine binsin</span></label>
        <label class="wpst-check"><input type="checkbox" name="wpst_settings[header_transparent_home_only]" value="1" <?php checked( ! empty( $settings['header_transparent_home_only'] ) ); ?>><span>Sadece ana sayfada şeffaf</span></label>
        <label>Glass Görünümü<select name="wpst_settings[header_glass_style]"><option value="off" <?php selected($settings['header_glass_style'],'off'); ?>>Kapalı · Tam şeffaf</option><option value="soft" <?php selected($settings['header_glass_style'],'soft'); ?>>Soft Glass</option><option value="strong" <?php selected($settings['header_glass_style'],'strong'); ?>>Strong Glass</option><option value="dark" <?php selected($settings['header_glass_style'],'dark'); ?>>Dark Glass</option></select></label>
        <label class="wpst-check"><input type="checkbox" name="wpst_settings[header_scroll_solid]" value="1" <?php checked( ! empty( $settings['header_scroll_solid'] ) ); ?>><span>Scroll sonrası arka planı doldur</span></label>
        <label class="wpst-check"><input type="checkbox" name="wpst_settings[header_blur]" value="1" <?php checked( ! empty( $settings['header_blur'] ) ); ?>><span>Scroll sonrası blur</span></label>
        <label>Blur Şiddeti<input type="number" min="0" max="40" name="wpst_settings[header_blur_amount]" value="<?php echo absint($settings['header_blur_amount']); ?>"><small>px</small></label>
        
    </details>

    <details class="wpst-live-settings-details wpst-header-shadow-group">
        <summary>Gölge</summary>
        <label class="wpst-header-shadow-style-control">Header Gölge Stili
            <select name="wpst_settings[header_shadow_style]" data-header-shadow-style>
                <option value="normal" <?php selected(($settings['header_shadow_style']??'normal'),'normal'); ?>>Normal · Gölgesiz</option>
                <option value="soft" <?php selected(($settings['header_shadow_style']??'normal'),'soft'); ?>>Soft</option>
                <option value="medium" <?php selected(($settings['header_shadow_style']??'normal'),'medium'); ?>>Medium</option>
                <option value="strong" <?php selected(($settings['header_shadow_style']??'normal'),'strong'); ?>>Strong</option>
            </select>
            <small>Tüm header görünümlerine uygulanır.</small>
        </label>
        <label class="wpst-check"><input type="checkbox" name="wpst_settings[header_scroll_shadow]" value="1" <?php checked( ! empty( $settings['header_scroll_shadow'] ) ); ?>><span>Scroll sonrası gölgeyi kullan</span></label>
    </details>

<details class="wpst-live-settings-details wpst-floating-header-settings">
        <summary>Görünüm</summary>
        <div class="wpst-header-layout-choice">
            <label class="wpst-layout-choice <?php echo $settings['header_layout_style']==='normal'?'is-active':''; ?>">
                <input type="radio" name="wpst_settings[header_layout_style]" value="normal" <?php checked($settings['header_layout_style'],'normal'); ?> data-header-layout-choice>
                <span><i class="is-normal"></i><strong>Normal</strong><small>Tam genişlik ana header</small></span>
            </label>
            <label class="wpst-layout-choice <?php echo $settings['header_layout_style']==='boxed'?'is-active':''; ?>">
                <input type="radio" name="wpst_settings[header_layout_style]" value="boxed" <?php checked($settings['header_layout_style'],'boxed'); ?> data-header-layout-choice>
                <span><i class="is-boxed"></i><strong>Floating / Boxed</strong><small>Ortalanmış bağımsız header kutusu</small></span>
            </label>
        </div>

        <div class="wpst-boxed-only-settings" data-boxed-only <?php echo $settings['header_layout_style']==='boxed'?'':'hidden'; ?>>
            <div class="wpst-boxed-only-head"><strong>Floating / Boxed Ayarları</strong><small>Gölge, sticky ve satır ölçüleri diğer bölümlerdedir.</small></div>
            <div class="wpst-header-v3-fields wpst-boxed-header-fields">
                <label>Maks. Genişlik<input type="number" min="720" max="1920" name="wpst_settings[header_boxed_width]" value="<?php echo absint($settings['header_boxed_width']); ?>"><small>px</small></label>
                <label>Üst Boşluk<input type="number" min="0" max="80" name="wpst_settings[header_boxed_top]" value="<?php echo absint($settings['header_boxed_top']); ?>"><small>px</small></label>
                <label>Yan Boşluk<input type="number" min="0" max="80" name="wpst_settings[header_boxed_side]" value="<?php echo absint($settings['header_boxed_side']); ?>"><small>px</small></label>
                <label>Köşe Yuvarlaklığı<input type="number" min="0" max="60" name="wpst_settings[header_boxed_radius]" value="<?php echo absint($settings['header_boxed_radius']); ?>"><small>px</small></label>
                <label>Kutu Arka Planı<input type="color" name="wpst_settings[header_boxed_background]" value="<?php echo esc_attr($settings['header_boxed_background']); ?>"></label>
                <label>Kenarlık Rengi<input type="color" name="wpst_settings[header_boxed_border_color]" value="<?php echo esc_attr(preg_match('/^#[0-9a-f]{6}$/i',$settings['header_boxed_border_color'])?$settings['header_boxed_border_color']:'#e5e7eb'); ?>"></label>
                <label>Kenarlık Kalınlığı<input type="number" min="0" max="4" name="wpst_settings[header_boxed_border_width]" value="<?php echo absint($settings['header_boxed_border_width']); ?>"><small>px</small></label>
            </div>
            <label class="wpst-check wpst-boxed-mobile-check"><input type="checkbox" name="wpst_settings[header_boxed_mobile]" value="1" <?php checked(!empty($settings['header_boxed_mobile'])); ?>><span>Mobilde de Floating / Boxed kullan</span></label>
        </div>
    </details>
    <!-- v3.3.18.21.2: duplicate Header Builder 3.0 preset cards removed from Inspector.
         Keep the saved preset key for compatibility with existing sites / quick-start. -->
    <input type="hidden" name="wpst_settings[header_preset]" value="<?php echo esc_attr($settings['header_preset']); ?>" data-header-preset-input>

    <details class="wpst-live-settings-details wpst-menu-interactions">
        <summary>Menü</summary>
        <input type="hidden" name="wpst_settings[header_desktop_height]" value="<?php echo absint($settings['header_desktop_height']); ?>">
        <input type="hidden" name="wpst_settings[header_scrolled_height]" value="<?php echo absint($settings['header_scrolled_height']); ?>">
        <div class="wpst-header-v3-fields wpst-header-v3-fields-clean">
            <label>Menü Boşluğu<input type="number" min="8" max="64" name="wpst_settings[header_menu_gap]" value="<?php echo absint($settings['header_menu_gap']); ?>" data-header-v3-field="header_menu_gap"><small>px</small></label>
            <label>Hover Efekti<select name="wpst_settings[header_menu_hover]" data-header-v3-field="header_menu_hover"><option value="none" <?php selected($settings['header_menu_hover'],'none'); ?>>Yok</option><option value="pill" <?php selected($settings['header_menu_hover'],'pill'); ?>>Arka Plan</option><option value="fade" <?php selected($settings['header_menu_hover'],'fade'); ?>>Fade</option><option value="slide" <?php selected($settings['header_menu_hover'],'slide'); ?>>Hafif Hareket</option><option value="shadow" <?php selected($settings['header_menu_hover'],'shadow'); ?>>Yumuşak Gölge</option></select></label>
            <label>Aktif Öğe Efekti<select name="wpst_settings[header_menu_active]" data-header-v3-field="header_menu_active"><option value="none" <?php selected($settings['header_menu_active'],'none'); ?>>Yok</option><option value="pill" <?php selected($settings['header_menu_active'],'pill'); ?>>Arka Plan</option><option value="shadow" <?php selected($settings['header_menu_active'],'shadow'); ?>>Yumuşak Gölge</option><option value="border" <?php selected($settings['header_menu_active'],'border'); ?>>Çerçeve</option><option value="fade" <?php selected($settings['header_menu_active'],'fade'); ?>>Sade / Fade</option></select></label>
            <label>Aktif Gölge Yoğunluğu<select name="wpst_settings[header_menu_active_shadow]"><option value="soft" <?php selected(($settings['header_menu_active_shadow']??'soft'),'soft'); ?>>Soft</option><option value="medium" <?php selected(($settings['header_menu_active_shadow']??'soft'),'medium'); ?>>Medium</option><option value="strong" <?php selected(($settings['header_menu_active_shadow']??'soft'),'strong'); ?>>Strong</option></select><small>Sadece “Yumuşak Gölge” aktifken kullanılır.</small></label>
        </div>
        <small class="wpst-menu-interactions-note">Aktif menü gölgesi tek katmanlıdır; ekstra glow/parlama uygulanmaz.</small>
    </details>

    <details class="wpst-live-settings-details">
        <summary>Scroll Renkleri</summary>
        <div class="wpst-header-color-row">
            <label><span>Transparent Yazı</span><input type="color" name="wpst_settings[header_transparent_text_color]" value="<?php echo esc_attr($settings['header_transparent_text_color']); ?>" data-header-v3-field="header_transparent_text_color"></label>
            <label><span>Scroll Arka Plan</span><input type="color" name="wpst_settings[header_scrolled_background]" value="<?php echo esc_attr($settings['header_scrolled_background']); ?>" data-header-v3-field="header_scrolled_background"></label>
            <label><span>Scroll Yazı</span><input type="color" name="wpst_settings[header_scrolled_text_color]" value="<?php echo esc_attr($settings['header_scrolled_text_color']); ?>" data-header-v3-field="header_scrolled_text_color"></label>
        </div>
    </details>

    <details class="wpst-live-settings-details">
        <summary>Aksiyonlar</summary>
        <label class="wpst-check"><input type="checkbox" name="wpst_settings[header_search_enabled]" value="1" <?php checked(!empty($settings['header_search_enabled'])); ?>><span>Arama popup butonu</span></label>
        <label>Arama Placeholder<input type="text" name="wpst_settings[header_search_placeholder]" value="<?php echo esc_attr($settings['header_search_placeholder']); ?>"></label>
        <label class="wpst-check"><input type="checkbox" name="wpst_settings[header_account_enabled]" value="1" <?php checked(!empty($settings['header_account_enabled'])); ?>><span>Hesap butonu</span></label>
        <label>Hesap Linki<input type="url" name="wpst_settings[header_account_url]" value="<?php echo esc_attr($settings['header_account_url']); ?>" placeholder="Boşsa otomatik"></label>
        <label class="wpst-check"><input type="checkbox" name="wpst_settings[header_cart_enabled]" value="1" <?php checked(!empty($settings['header_cart_enabled'])); ?>><span>Sepet butonu</span></label>
        <label>Sepet Linki<input type="url" name="wpst_settings[header_cart_url]" value="<?php echo esc_attr($settings['header_cart_url']); ?>" placeholder="Boşsa WooCommerce otomatik"></label>
        <label class="wpst-check"><input type="checkbox" name="wpst_settings[header_mobile_bottom_nav]" value="1" <?php checked(!empty($settings['header_mobile_bottom_nav'])); ?>><span>Mobil alt navigasyon aksiyonları</span></label>
    </details>

    <details class="wpst-live-settings-details">
        <summary>Üst Barlar</summary>
        <label class="wpst-check"><input type="checkbox" name="wpst_settings[header_announcement_enabled]" value="1" <?php checked( ! empty( $settings['header_announcement_enabled'] ) ); ?>><span>Duyuru çubuğu</span></label>
        <label>Duyuru Metni<input type="text" name="wpst_settings[header_announcement_text]" value="<?php echo esc_attr($settings['header_announcement_text']); ?>"></label>
        <label>Duyuru Link Metni<input type="text" name="wpst_settings[header_announcement_link_text]" value="<?php echo esc_attr($settings['header_announcement_link_text']); ?>"></label>
        <label>Duyuru Linki<input type="text" name="wpst_settings[header_announcement_link_url]" value="<?php echo esc_attr($settings['header_announcement_link_url']); ?>"></label>
        <div class="wpst-header-color-row">
            <label><span>Duyuru Arka Plan</span><input type="color" name="wpst_settings[header_announcement_background]" value="<?php echo esc_attr($settings['header_announcement_background']); ?>"></label>
            <label><span>Duyuru Yazı</span><input type="color" name="wpst_settings[header_announcement_text_color]" value="<?php echo esc_attr($settings['header_announcement_text_color']); ?>"></label>
        </div>
        <label class="wpst-check"><input type="checkbox" name="wpst_settings[header_announcement_dismissible]" value="1" <?php checked(!empty($settings['header_announcement_dismissible'])); ?>><span>Kullanıcı duyuruyu kapatabilsin</span></label>

        <label class="wpst-check"><input type="checkbox" name="wpst_settings[header_topbar_enabled]" value="1" <?php checked( ! empty( $settings['header_topbar_enabled'] ) ); ?>><span>Topbar</span></label>
        <label>Topbar Metni<input type="text" name="wpst_settings[header_topbar_text]" value="<?php echo esc_attr($settings['header_topbar_text']); ?>"></label>
        <label>Topbar Link Metni<input type="text" name="wpst_settings[header_topbar_link_text]" value="<?php echo esc_attr($settings['header_topbar_link_text']); ?>"></label>
        <label>Topbar Linki<input type="text" name="wpst_settings[header_topbar_link_url]" value="<?php echo esc_attr($settings['header_topbar_link_url']); ?>"></label>
    </details>

    <details class="wpst-live-settings-details wpst-mobile-builder-v3" open>
        <summary>Mobil Header & Off-Canvas Menü</summary>
        <div class="wpst-mobile-builder-intro">
            <div><strong>Mobil Header Builder</strong><small>Header, drawer, iletişim ve sosyal alanları ayrı panellerden yönetin.</small></div>
            <button type="button" data-wpst-open-mobile-preview><span class="dashicons dashicons-smartphone"></span>Mobil Önizleme</button>
        </div>

        <div class="wpst-mobile-builder-tabs">
            <button type="button" class="is-active" data-mobile-builder-tab="header"><span class="dashicons dashicons-menu-alt"></span>Header</button>
            <button type="button" data-mobile-builder-tab="drawer"><span class="dashicons dashicons-align-right"></span>Drawer</button>
            <button type="button" data-mobile-builder-tab="contact"><span class="dashicons dashicons-phone"></span>İletişim</button>
            <button type="button" data-mobile-builder-tab="social"><span class="dashicons dashicons-share"></span>Sosyal</button>
        </div>

        <div class="wpst-mobile-builder-pane is-active" data-mobile-builder-pane="header">
            <div class="wpst-mobile-preset-cards">
                <?php foreach(array(
                    'classic'=>array('Klasik','Logo + Menü'),
                    'centered'=>array('Centered','Ortalanmış logo'),
                    'compact'=>array('Compact','Daha az yükseklik'),
                    'cta'=>array('CTA','Buton odaklı'),
                    'commerce'=>array('Commerce','Hesap + Sepet')
                ) as $preset_key=>$preset_data): ?>
                    <button type="button" data-mobile-preset="<?php echo esc_attr($preset_key); ?>" class="<?php echo $settings['header_mobile_preset']===$preset_key?'is-active':''; ?>">
                        <i></i><strong><?php echo esc_html($preset_data[0]); ?></strong><small><?php echo esc_html($preset_data[1]); ?></small>
                    </button>
                <?php endforeach; ?>
            </div>
            <label class="wpst-mobile-hidden-select">Mobil Header Preseti<select name="wpst_settings[header_mobile_preset]"><option value="classic" <?php selected($settings['header_mobile_preset'],'classic'); ?>>Klasik · Logo + Menü</option><option value="centered" <?php selected($settings['header_mobile_preset'],'centered'); ?>>Ortalanmış Logo</option><option value="compact" <?php selected($settings['header_mobile_preset'],'compact'); ?>>Kompakt</option><option value="cta" <?php selected($settings['header_mobile_preset'],'cta'); ?>>CTA Odaklı</option><option value="commerce" <?php selected($settings['header_mobile_preset'],'commerce'); ?>>E-Ticaret</option></select></label>

            <div class="wpst-mobile-fields-grid">
                <label>Logo Konumu<select name="wpst_settings[header_mobile_logo_position]"><option value="left" <?php selected($settings['header_mobile_logo_position'],'left'); ?>>Sol</option><option value="center" <?php selected($settings['header_mobile_logo_position'],'center'); ?>>Orta</option></select></label>
                <label>Mobil Kırılım<input type="number" min="480" max="1200" name="wpst_settings[header_mobile_breakpoint]" value="<?php echo absint( $settings['header_mobile_breakpoint'] ); ?>"><small>px</small></label>
            </div>

            

            <div class="wpst-mobile-builder-actions">
                <strong>Sağ Aksiyonlar</strong>
                <div class="wpst-mobile-action-grid">
                    <label class="wpst-check"><input type="checkbox" name="wpst_settings[header_mobile_search]" value="1" <?php checked(!empty($settings['header_mobile_search'])); ?>><span>Arama</span></label>
                    <label class="wpst-check"><input type="checkbox" name="wpst_settings[header_mobile_account]" value="1" <?php checked(!empty($settings['header_mobile_account'])); ?>><span>Hesap</span></label>
                    <label class="wpst-check"><input type="checkbox" name="wpst_settings[header_mobile_cart]" value="1" <?php checked(!empty($settings['header_mobile_cart'])); ?>><span>Sepet</span></label>
                    <label class="wpst-check"><input type="checkbox" name="wpst_settings[header_mobile_cta_enabled]" value="1" <?php checked(!empty($settings['header_mobile_cta_enabled'])); ?>><span>CTA</span></label>
                </div>
            </div>
            <div class="wpst-mobile-fields-grid">
                <label>CTA Metni<input type="text" name="wpst_settings[header_mobile_cta_text]" value="<?php echo esc_attr($settings['header_mobile_cta_text']); ?>"></label>
                <label>CTA Linki<input type="text" name="wpst_settings[header_mobile_cta_url]" value="<?php echo esc_attr($settings['header_mobile_cta_url']); ?>"></label>
            </div>
        </div>

        <div class="wpst-mobile-builder-pane" data-mobile-builder-pane="drawer">
            <div class="wpst-mobile-drawer-preview-card">
                <div class="wpst-mobile-drawer-preview-phone">
                    <span class="wpst-mobile-preview-notch"></span>
                    <div class="wpst-mobile-preview-head"><b>LOGO</b><i>☰</i></div>
                    <div class="wpst-mobile-preview-panel">
                        <div><strong>Menü</strong><span>×</span></div>
                        <em>Anasayfa</em><em>Hizmetler</em><em>Hakkımızda</em><em>İletişim</em>
                        <button type="button"><?php echo esc_html($settings['header_mobile_cta_text']); ?></button>
                    </div>
                </div>
                <div><strong>Off-Canvas Menü</strong><small>Açılma yönü, genişlik ve görünümü canlı önizlemede test edin.</small></div>
            </div>
            <div class="wpst-mobile-fields-grid">
                <label>Menü Açılma Yönü<select name="wpst_settings[header_mobile_drawer_side]"><option value="right" <?php selected($settings['header_mobile_drawer_side'],'right'); ?>>Sağdan</option><option value="left" <?php selected($settings['header_mobile_drawer_side'],'left'); ?>>Soldan</option></select></label>
                <label>Drawer Genişliği<input type="number" min="280" max="460" name="wpst_settings[header_mobile_drawer_width]" value="<?php echo absint($settings['header_mobile_drawer_width']); ?>"><small>px</small></label>
                <label>Drawer Tasarımı<select name="wpst_settings[header_mobile_drawer_style]"><option value="clean" <?php selected($settings['header_mobile_drawer_style'],'clean'); ?>>Clean</option><option value="soft" <?php selected($settings['header_mobile_drawer_style'],'soft'); ?>>Soft Cards</option><option value="dark" <?php selected($settings['header_mobile_drawer_style'],'dark'); ?>>Dark</option><option value="glass" <?php selected($settings['header_mobile_drawer_style'],'glass'); ?>>Glass</option></select></label>
                <label>Kapatma Metni<input type="text" name="wpst_settings[header_mobile_close_text]" value="<?php echo esc_attr($settings['header_mobile_close_text']); ?>"></label>
            </div>
            <div class="wpst-mobile-action-grid">
                <label class="wpst-check"><input type="checkbox" name="wpst_settings[header_mobile_overlay]" value="1" <?php checked( ! empty( $settings['header_mobile_overlay'] ) ); ?>><span>Arka plan karartma</span></label>
                <label class="wpst-check"><input type="checkbox" name="wpst_settings[header_mobile_drawer_logo]" value="1" <?php checked(!empty($settings['header_mobile_drawer_logo'])); ?>><span>Drawer logosu</span></label>
            </div>
            <button type="button" class="wpst-mobile-test-drawer" data-wpst-test-drawer><span class="dashicons dashicons-visibility"></span>Drawer'ı Önizle</button>
        </div>

        <div class="wpst-mobile-builder-pane" data-mobile-builder-pane="contact">
            <label class="wpst-mobile-feature-toggle"><input type="checkbox" name="wpst_settings[header_mobile_contact_enabled]" value="1" <?php checked(!empty($settings['header_mobile_contact_enabled'])); ?>><span><b>Hızlı İletişim Alanı</b><small>Mobil menünün alt kısmında telefon ve e-posta gösterir.</small></span></label>
            <label>İletişim Başlığı<input type="text" name="wpst_settings[header_mobile_contact_title]" value="<?php echo esc_attr($settings['header_mobile_contact_title']); ?>"></label>
            <div class="wpst-mobile-fields-grid">
                <label>Telefon<input type="text" name="wpst_settings[header_mobile_phone]" value="<?php echo esc_attr($settings['header_mobile_phone']); ?>" placeholder="+90 ..."></label>
                <label>E-posta<input type="email" name="wpst_settings[header_mobile_email]" value="<?php echo esc_attr($settings['header_mobile_email']); ?>" placeholder="info@example.com"></label>
            </div>
        </div>

        <div class="wpst-mobile-builder-pane" data-mobile-builder-pane="social">
            <div class="wpst-mobile-social-settings">
                <label class="wpst-mobile-feature-toggle"><input type="checkbox" name="wpst_settings[header_mobile_social_enabled]" value="1" <?php checked(!empty($settings['header_mobile_social_enabled'])); ?>><span><b>Sosyal Medya</b><small>Drawer altında sosyal medya bağlantılarını gösterir.</small></span></label>
                <label>Instagram<input type="url" name="wpst_settings[header_mobile_social_instagram]" value="<?php echo esc_attr($settings['header_mobile_social_instagram']); ?>" placeholder="https://instagram.com/..."></label>
                <label>Facebook<input type="url" name="wpst_settings[header_mobile_social_facebook]" value="<?php echo esc_attr($settings['header_mobile_social_facebook']); ?>" placeholder="https://facebook.com/..."></label>
                <label>YouTube<input type="url" name="wpst_settings[header_mobile_social_youtube]" value="<?php echo esc_attr($settings['header_mobile_social_youtube']); ?>" placeholder="https://youtube.com/..."></label>
                <label>LinkedIn<input type="url" name="wpst_settings[header_mobile_social_linkedin]" value="<?php echo esc_attr($settings['header_mobile_social_linkedin']); ?>" placeholder="https://linkedin.com/..."></label>
            </div>
        </div>
    </details>

    
</div>

<div class="wpst-live-header-settings wpst-inspector-group is-scroll-top">
    <details class="wpst-live-settings-details wpst-scroll-top-inspector">
                            <summary><span class="dashicons dashicons-arrow-up-alt2"></span> Yukarı Butonu</summary>
                            <div class="wpst-scroll-top-inspector-card">
                                <label class="wpst-check"><input type="checkbox" name="wpst_settings[scroll_top_enabled]" value="1" <?php checked(!empty($settings['scroll_top_enabled'])); ?>><span>Yukarı butonunu etkinleştir</span></label>
                                <label class="wpst-check"><input type="checkbox" name="wpst_settings[scroll_top_mobile]" value="1" <?php checked(!empty($settings['scroll_top_mobile'])); ?>><span>Mobilde göster</span></label>
                                <div class="wpst-header-v3-fields wpst-scroll-top-fields">
                                    <label>Görünme Mesafesi<input type="number" min="100" max="2000" step="20" name="wpst_settings[scroll_top_threshold]" value="<?php echo absint($settings['scroll_top_threshold']??320); ?>"><small>px</small></label>
                                    <label>Konum<select name="wpst_settings[scroll_top_position]"><option value="right" <?php selected(($settings['scroll_top_position']??'right'),'right'); ?>>Sağ Alt</option><option value="left" <?php selected(($settings['scroll_top_position']??'right'),'left'); ?>>Sol Alt</option></select></label>
                                    <label>Görünüm<select name="wpst_settings[scroll_top_style]"><option value="soft" <?php selected(($settings['scroll_top_style']??'soft'),'soft'); ?>>Soft</option><option value="solid" <?php selected(($settings['scroll_top_style']??'soft'),'solid'); ?>>Solid</option><option value="outline" <?php selected(($settings['scroll_top_style']??'soft'),'outline'); ?>>Outline</option><option value="black" <?php selected(($settings['scroll_top_style']??'soft'),'black'); ?>>Siyah</option></select></label>
                                </div>
                                <small>Sayfa belirtilen mesafe kadar aşağı kaydırıldığında görünür. Tıklanınca sayfanın en üstüne yumuşak geçiş yapar.</small>
                            </div>
                        </details>
</div>

<?php endif; ?>

<?php if ( ! $is_header ) : ?>
<div class="wpst-footer-row-settings">
    <div class="wpst-live-settings-title"><strong>Footer Satır Tasarımı</strong><small>Üst, ana ve alt footer satırlarını bağımsız yönetin.</small></div>
    <?php foreach ( array( 'top' => 'Üst Footer', 'main' => 'Ana Footer', 'bottom' => 'Alt Footer' ) as $row_key => $row_label ) : $rp = 'footer_row_' . $row_key . '_'; ?>
    <details class="wpst-row-settings-card" <?php echo 'main' === $row_key ? 'open' : ''; ?>>
        <summary><span><b><?php echo esc_html($row_label); ?></b><small>Yükseklik · renk · container · görünürlük</small></span><i>⚙</i></summary>
        <div class="wpst-row-setting-grid is-three">
            <label>Masaüstü Yükseklik<input type="number" min="24" max="220" name="wpst_settings[<?php echo esc_attr($rp); ?>height_desktop]" value="<?php echo absint($settings[$rp.'height_desktop']); ?>"><small>px</small></label>
            <label>Tablet Yükseklik<input type="number" min="24" max="220" name="wpst_settings[<?php echo esc_attr($rp); ?>height_tablet]" value="<?php echo absint($settings[$rp.'height_tablet']); ?>"><small>px</small></label>
            <label>Mobil Min. Yükseklik<input type="number" min="24" max="260" name="wpst_settings[<?php echo esc_attr($rp); ?>height_mobile]" value="<?php echo absint($settings[$rp.'height_mobile']); ?>"><small>px</small></label>
        </div>
        <div class="wpst-row-setting-grid is-three">
            <label>Arka Plan<input type="color" name="wpst_settings[<?php echo esc_attr($rp); ?>background]" value="<?php echo esc_attr($settings[$rp.'background']); ?>" data-wpst-row-appearance="background"></label>
            <label>Yazı Rengi<input type="color" name="wpst_settings[<?php echo esc_attr($rp); ?>text_color]" value="<?php echo esc_attr($settings[$rp.'text_color']); ?>" data-wpst-row-appearance="text"></label>
            <label>Border Rengi<input type="color" name="wpst_settings[<?php echo esc_attr($rp); ?>border_color]" value="<?php echo esc_attr($settings[$rp.'border_color']); ?>" data-wpst-row-appearance="border"></label>
        </div>
        <div class="wpst-row-setting-grid is-three">
            <label>Üst Border<input type="number" min="0" max="4" name="wpst_settings[<?php echo esc_attr($rp); ?>border_width]" value="<?php echo absint($settings[$rp.'border_width']); ?>"><small>px</small></label>
            <label>Container<input type="number" min="720" max="1920" name="wpst_settings[<?php echo esc_attr($rp); ?>container]" value="<?php echo absint($settings[$rp.'container']); ?>"><small>px</small></label>
            <label class="wpst-check wpst-row-fullwidth"><input type="checkbox" name="wpst_settings[<?php echo esc_attr($rp); ?>full_width]" value="1" <?php checked(!empty($settings[$rp.'full_width'])); ?>><span>Tam genişlik içerik</span></label>
        </div>
        <div class="wpst-row-visibility"><strong>Cihazlarda Göster</strong><div>
            <?php foreach(array('desktop'=>'Masaüstü','tablet'=>'Tablet','mobile'=>'Mobil') as $dev=>$dev_label): ?>
            <label class="wpst-device-check"><input type="checkbox" name="wpst_settings[<?php echo esc_attr($rp); ?>show_<?php echo esc_attr($dev); ?>]" value="1" <?php checked(!empty($settings[$rp.'show_'.$dev])); ?>><span><?php echo esc_html($dev_label); ?></span></label>
            <?php endforeach; ?>
        </div></div>
    </details>
    <?php endforeach; ?>
</div>
<div class="wpst-live-footer-settings">
    <div class="wpst-live-settings-title"><strong>Footer Davranışı</strong><small>Değişiklikler canlı önizlemede görünür.</small></div>

    <details class="wpst-live-settings-details" open>
        <summary>CTA Alanı</summary>
        <label class="wpst-check"><input type="checkbox" name="wpst_settings[footer_cta_enabled]" value="1" <?php checked(!empty($settings['footer_cta_enabled'])); ?>><span>Footer üstü CTA aktif</span></label>
        <label>Başlık<input type="text" name="wpst_settings[footer_cta_title]" value="<?php echo esc_attr($settings['footer_cta_title']); ?>"></label>
        <label>Açıklama<input type="text" name="wpst_settings[footer_cta_text]" value="<?php echo esc_attr($settings['footer_cta_text']); ?>"></label>
        <label>Buton Metni<input type="text" name="wpst_settings[footer_cta_button_text]" value="<?php echo esc_attr($settings['footer_cta_button_text']); ?>"></label>
        <label>Buton Linki<input type="text" name="wpst_settings[footer_cta_button_url]" value="<?php echo esc_attr($settings['footer_cta_button_url']); ?>"></label>
    </details>

    <details class="wpst-live-settings-details">
        <summary>Alt Footer Bar</summary>
        <label class="wpst-check"><input type="checkbox" name="wpst_settings[footer_bottom_enabled]" value="1" <?php checked(!empty($settings['footer_bottom_enabled'])); ?>><span>Alt bar aktif</span></label>
        <label>Metin<input type="text" name="wpst_settings[footer_bottom_text]" value="<?php echo esc_attr($settings['footer_bottom_text']); ?>"><small>{year} ve {site} kullanılabilir.</small></label>
        <label>Alt Bar Menüsü<select name="wpst_settings[footer_bottom_menu]"><option value="0">Menü yok</option><?php foreach($menus as $menu): ?><option value="<?php echo absint($menu->term_id); ?>" <?php selected(absint($settings['footer_bottom_menu']),absint($menu->term_id)); ?>><?php echo esc_html($menu->name); ?></option><?php endforeach; ?></select></label>
        <label class="wpst-check"><input type="checkbox" name="wpst_settings[footer_divider]" value="1" <?php checked(!empty($settings['footer_divider'])); ?>><span>Ayırıcı çizgi</span></label>
    </details>

    <details class="wpst-live-settings-details" open>
        <summary>Footer Presetleri</summary>
        <label>Hazır Görünüm<select name="wpst_settings[footer_preset]"><option value="custom" <?php selected($settings['footer_preset'],'custom'); ?>>Özel</option><option value="corporate" <?php selected($settings['footer_preset'],'corporate'); ?>>Corporate</option><option value="minimal" <?php selected($settings['footer_preset'],'minimal'); ?>>Minimal</option><option value="dark" <?php selected($settings['footer_preset'],'dark'); ?>>Dark</option><option value="glass" <?php selected($settings['footer_preset'],'glass'); ?>>Glass</option><option value="shop" <?php selected($settings['footer_preset'],'shop'); ?>>Shop</option></select></label>
    </details>

    <details class="wpst-live-settings-details">
        <summary>Newsletter</summary>
        <label class="wpst-check"><input type="checkbox" name="wpst_settings[footer_newsletter_enabled]" value="1" <?php checked(!empty($settings['footer_newsletter_enabled'])); ?>><span>Newsletter satırını göster</span></label>
        <label>Başlık<input type="text" name="wpst_settings[footer_newsletter_title]" value="<?php echo esc_attr($settings['footer_newsletter_title']); ?>"></label>
        <label>Açıklama<input type="text" name="wpst_settings[footer_newsletter_text]" value="<?php echo esc_attr($settings['footer_newsletter_text']); ?>"></label>
        <label>Input Placeholder<input type="text" name="wpst_settings[footer_newsletter_placeholder]" value="<?php echo esc_attr($settings['footer_newsletter_placeholder']); ?>"></label>
        <label>Buton<input type="text" name="wpst_settings[footer_newsletter_button]" value="<?php echo esc_attr($settings['footer_newsletter_button']); ?>"></label>
        <label>Form Action URL<input type="url" name="wpst_settings[footer_newsletter_action]" value="<?php echo esc_attr($settings['footer_newsletter_action']); ?>" placeholder="Mailchimp / Brevo / özel form URL"></label>
    </details>

    <details class="wpst-live-settings-details">
        <summary>İletişim & Çalışma Saatleri</summary>
        <label class="wpst-check"><input type="checkbox" name="wpst_settings[footer_contact_enabled]" value="1" <?php checked(!empty($settings['footer_contact_enabled'])); ?>><span>İletişim şeridini göster</span></label>
        <label>Telefon<input type="text" name="wpst_settings[footer_phone]" value="<?php echo esc_attr($settings['footer_phone']); ?>"></label>
        <label>E-posta<input type="email" name="wpst_settings[footer_email]" value="<?php echo esc_attr($settings['footer_email']); ?>"></label>
        <label>Adres<input type="text" name="wpst_settings[footer_address]" value="<?php echo esc_attr($settings['footer_address']); ?>"></label>
        <label>Çalışma Saatleri<input type="text" name="wpst_settings[footer_hours]" value="<?php echo esc_attr($settings['footer_hours']); ?>" placeholder="Pzt-Cuma 09:00-18:00"></label>
    </details>

    <details class="wpst-live-settings-details">
        <summary>Ödeme Yöntemleri</summary>
        <label class="wpst-check"><input type="checkbox" name="wpst_settings[footer_payments_enabled]" value="1" <?php checked(!empty($settings['footer_payments_enabled'])); ?>><span>Ödeme ikonlarını göster</span></label>
        <label>Kısa Metin<input type="text" name="wpst_settings[footer_payment_text]" value="<?php echo esc_attr($settings['footer_payment_text']); ?>"></label>
        <div class="wpst-row-visibility"><strong>Gösterilecekler</strong><div>
        <?php foreach(array('visa'=>'VISA','mastercard'=>'Mastercard','amex'=>'AMEX','paypal'=>'PayPal','iyzico'=>'iyzico') as $brand=>$label): ?>
          <label class="wpst-device-check"><input type="checkbox" name="wpst_settings[footer_payment_<?php echo esc_attr($brand); ?>]" value="1" <?php checked(!empty($settings['footer_payment_'.$brand])); ?>><span><?php echo esc_html($label); ?></span></label>
        <?php endforeach; ?>
        </div></div>
    </details>

    <details class="wpst-live-settings-details">
        <summary>Mobil & Reveal</summary>
        <label>Mobil Kolon<select name="wpst_settings[footer_mobile_columns]"><option value="1" <?php selected(absint($settings['footer_mobile_columns']),1); ?>>1 Kolon</option><option value="2" <?php selected(absint($settings['footer_mobile_columns']),2); ?>>2 Kolon</option></select></label>
        <label>Mobil Hizalama<select name="wpst_settings[footer_mobile_align]"><option value="left" <?php selected($settings['footer_mobile_align'],'left'); ?>>Sol</option><option value="center" <?php selected($settings['footer_mobile_align'],'center'); ?>>Orta</option></select></label>
        <label class="wpst-check"><input type="checkbox" name="wpst_settings[footer_mobile_accordion]" value="1" <?php checked(!empty($settings['footer_mobile_accordion'])); ?>><span>Mobilde kolonları accordion yap</span></label>
        <label>Sol Kolon Başlığı<input type="text" name="wpst_settings[footer_mobile_title_left]" value="<?php echo esc_attr($settings['footer_mobile_title_left']); ?>"></label>
        <label>Orta Kolon Başlığı<input type="text" name="wpst_settings[footer_mobile_title_center]" value="<?php echo esc_attr($settings['footer_mobile_title_center']); ?>"></label>
        <label>Sağ Kolon Başlığı<input type="text" name="wpst_settings[footer_mobile_title_right]" value="<?php echo esc_attr($settings['footer_mobile_title_right']); ?>"></label>
        <label class="wpst-check"><input type="checkbox" name="wpst_settings[footer_reveal]" value="1" <?php checked(!empty($settings['footer_reveal'])); ?>><span>Footer reveal efekti</span></label>
        <label>Reveal Mesafesi<input type="number" min="0" max="240" name="wpst_settings[footer_reveal_offset]" value="<?php echo absint($settings['footer_reveal_offset']); ?>"><small>px</small></label>
    </details>
</div>
<?php endif; ?></div>
                        <div class="wpst-inspector-pane" data-inspector-pane="section"><h3>Bölüm Ayarları</h3><div class="wpst-selected-section-info">Önizlemeden veya soldan bir bölüm seçin.</div></div><div class="wpst-inspector-pane" data-inspector-pane="element"><h3>Eleman Ayarları</h3><div class="wpst-element-inspector">Önizlemede bir elemana tıklayın. Ayarları burada açılır.</div></div>
                    </aside>
                </div>
            </div>
            <?php if ( $is_header ) : ?>
            <div class="wpst-header-device-sources">
                <div class="wpst-elementor-mode-head"><i>↔</i><div><strong>Cihaz Bazlı Header Kaynağı</strong><span>Live Builder ve Elementor şablonlarını masaüstü/mobil için bağımsız kullanabilirsiniz.</span></div></div>
                <div class="wpst-header-source-grid">
                    <div class="wpst-header-source-card">
                        <b>Masaüstü</b>
                        <label>Kaynak
                            <select name="wpst_settings[header_desktop_source]">
                                <option value="inherit" <?php selected($settings['header_desktop_source'],'inherit'); ?>>Mevcut Modu Kullan</option>
                                <option value="builder" <?php selected($settings['header_desktop_source'],'builder'); ?>>Live Builder</option>
                                <option value="elementor" <?php selected($settings['header_desktop_source'],'elementor'); ?>>Elementor Şablonu</option>
                            </select>
                        </label>
                        <label>Elementor Şablonu
                            <select name="wpst_settings[header_template]"><?php $this->template_options( $templates, $settings['header_template'] ); ?></select>
                        </label>
                    </div>
                    <div class="wpst-header-source-card">
                        <b>Mobil</b>
                        <label>Kaynak
                            <select name="wpst_settings[header_mobile_source]">
                                <option value="inherit" <?php selected($settings['header_mobile_source'],'inherit'); ?>>Mevcut Modu Kullan</option>
                                <option value="builder" <?php selected($settings['header_mobile_source'],'builder'); ?>>Live Builder</option>
                                <option value="elementor" <?php selected($settings['header_mobile_source'],'elementor'); ?>>Elementor Şablonu</option>
                            </select>
                        </label>
                        <label>Elementor Şablonu
                            <select name="wpst_settings[mobile_header_template]">
                                <option value="0">Masaüstü Elementor şablonunu kullan</option>
                                <?php $this->template_options( $templates, $settings['mobile_header_template'], false ); ?>
                            </select>
                        </label>
                    </div>
                </div>
                <div class="wpst-header-source-examples">
                    <span>Örnek: <b>Masaüstü = Live Builder</b> · <b>Mobil = Elementor Şablonu</b></span>
                    <span>veya <b>Masaüstü = Elementor</b> · <b>Mobil = Live Builder</b></span>
                </div>
            </div>
            <?php else : ?>
            <div class="wpst-mode-content" data-mode-content="elementor"><div class="wpst-elementor-box"><div class="wpst-elementor-mode-head"><i>E</i><div><strong>Elementor Şablonu Kullan</strong><span>Masaüstü ve mobil için ayrı şablon seçebilir veya aynı şablonu kullanabilirsiniz.</span></div></div><label>Masaüstü şablonu<select name="wpst_settings[<?php echo esc_attr( $type ); ?>_template]"><?php $this->template_options( $templates, $settings[ $type . '_template' ] ); ?></select></label><label>Mobil şablonu<select name="wpst_settings[mobile_<?php echo esc_attr( $type ); ?>_template]"><option value="0">Masaüstü şablonunu kullan</option><?php $this->template_options( $templates, $settings[ 'mobile_' . $type . '_template' ], false ); ?></select></label></div></div>
            <?php endif; ?>
            <div class="wpst-bottom-options"><label class="wpst-check"><input type="checkbox" name="wpst_settings[hide_theme_<?php echo esc_attr( $type ); ?>]" value="1" <?php checked( ! empty( $settings[ 'hide_theme_' . $type ] ) ); ?>><span>Tema <?php echo esc_html( $type ); ?> alanını gizle</span></label><details><summary>Tema Uyumluluğu · Gelişmiş</summary><textarea name="wpst_settings[custom_<?php echo esc_attr( $type ); ?>_selectors]" rows="3" placeholder=".site-<?php echo esc_attr( $type ); ?>"><?php echo esc_textarea( $settings[ 'custom_' . $type . '_selectors' ] ); ?></textarea></details></div>
        </section>
        <?php
    }

    private function available_blocks( $type ) {
        if ( class_exists( 'WPST_Builder_Core' ) ) {
            return WPST_Builder_Core::element_labels( $type );
        }
        $blocks = array( 'logo' => 'Logo', 'menu' => 'Menü', 'button' => 'Buton', 'search' => 'Arama', 'account' => 'Hesap', 'cart' => 'Sepet', 'text' => 'Metin', 'html' => 'HTML', 'social' => 'Sosyal Medya', 'spacer' => 'Esnek Boşluk' );
        if ( 'footer' === $type ) $blocks['copyright'] = 'Telif Yazısı';
        return $blocks;
    }

    private function template_options( $templates, $selected, $include_empty = true ) {
        if ( $include_empty ) echo '<option value="0">Şablon seçin</option>';
        foreach ( $templates as $template ) printf( '<option value="%1$d" %2$s>%3$s</option>', absint( $template->ID ), selected( absint( $selected ), absint( $template->ID ), false ), esc_html( $template->post_title . ' (#' . $template->ID . ')' ) );
    }

    private function get_elementor_templates() { return get_posts( array( 'post_type' => 'elementor_library', 'post_status' => 'publish', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC' ) ); }

    private function get_settings() {
        $settings = wp_parse_args( get_option( 'wpst_settings', array() ), array(
            'header_enabled'=>0,'footer_enabled'=>0,'blog_archive_enabled'=>0,'blog_archive_template'=>0,'blog_page_id'=>0,'blog_single_enabled'=>0,'blog_single_template'=>0,'theme_404_enabled'=>0,'theme_404_template'=>0,'theme_search_enabled'=>0,'theme_search_template'=>0,'theme_category_enabled'=>0,'theme_category_template'=>0,'theme_tag_enabled'=>0,'theme_tag_template'=>0,'theme_author_enabled'=>0,'theme_author_template'=>0,'hide_theme_header'=>0,'hide_theme_footer'=>0,'header_template'=>0,'footer_template'=>0,'mobile_header_template'=>0,'mobile_footer_template'=>0,'header_logo_id'=>0,'footer_logo_id'=>0,'header_logo_width'=>0,'header_logo_height'=>48,'footer_logo_width'=>0,'footer_logo_height'=>48,
            'header_mode'=>'builder','footer_mode'=>'builder','header_desktop_source'=>'inherit','header_mobile_source'=>'inherit','header_builder_version'=>1,'footer_builder_version'=>1,'header_sections'=>3,'footer_sections'=>3,'header_layout'=>'[{"type":"logo","section":1},{"type":"menu","section":2},{"type":"button","text":"Teklif Al","url":"#iletisim","section":3}]','footer_layout'=>'[{"type":"logo","section":1},{"type":"copyright","section":2},{"type":"social","section":3}]',
            'header_background'=>'#ffffff','footer_background'=>'#111827','header_text_color'=>'#111827','footer_text_color'=>'#ffffff','header_container'=>1200,'footer_container'=>1200,'header_padding'=>16,'footer_padding'=>28,'header_sticky'=>0,'header_sticky_mode'=>'always','header_shrink'=>1,'header_shadow'=>1,'header_mobile_breakpoint'=>768,'button_background'=>'#2563eb','button_radius'=>10,'custom_header_selectors'=>'','custom_footer_selectors'=>'','elementor_library_enabled'=>1,
            'global_primary'=>'#2563eb','global_secondary'=>'#7c3aed','global_heading'=>'#0f172a','global_text'=>'#334155','global_muted'=>'#64748b','global_surface'=>'#ffffff','global_page_bg'=>'#ffffff','global_apply_page_bg'=>0,'global_soft'=>'#f8fafc','global_border'=>'#e2e8f0','global_accent'=>'#0ea5e9','global_success'=>'#16a34a','global_warning'=>'#f59e0b','global_danger'=>'#dc2626','global_button_bg'=>'#2563eb','global_button_text'=>'#ffffff','global_button_hover_bg'=>'#1d4ed8','global_button_hover_text'=>'#ffffff','global_secondary_button_bg'=>'#ffffff','global_secondary_button_text'=>'#0f172a','global_secondary_button_border'=>'#cbd5e1','global_secondary_button_hover_bg'=>'#f8fafc','global_secondary_button_hover_text'=>'#0f172a','global_button_padding_x'=>24,'global_container'=>1200,'global_container_narrow'=>860,'global_container_wide'=>1440,'global_content_width_mode'=>'standard','global_section_space'=>80,'global_section_space_tablet'=>60,'global_section_space_mobile'=>40,'global_gap'=>24,'global_space_xs'=>8,'global_space_sm'=>12,'global_space_md'=>20,'global_space_lg'=>32,'global_space_xl'=>48,'global_space_xxl'=>72,'global_radius_sm'=>8,'global_radius_md'=>14,'global_radius_lg'=>20,'global_radius_xl'=>30,'global_card_radius'=>20,'global_button_radius'=>12,'global_button_height'=>48,'global_shadow'=>'soft','global_motion'=>'normal','global_preset'=>'modern','global_widget_quick_preset'=>'auto','global_widget_button_style'=>'auto','global_mobile_menu_preset'=>'corporate-modern','global_mobile_panel_background'=>'','global_mobile_item_background'=>'','global_mobile_text_color'=>'','global_mobile_active_background'=>'','global_mobile_cta_background'=>'','global_mobile_icon_background'=>'','global_mobile_panel_padding'=>'','global_mobile_item_radius'=>'','global_mobile_item_height'=>'','global_mobile_item_gap'=>'','global_mobile_icon_box_size'=>'','global_mobile_cta_radius'=>'','global_mobile_text_size'=>'','global_mobile_logo_position'=>'',
            'scroll_top_enabled'=>0,'scroll_top_mobile'=>1,'scroll_top_threshold'=>320,'scroll_top_position'=>'right','scroll_top_style'=>'soft',
            'header_transparent'=>0,'header_transparent_home_only'=>0,'header_transparent_overlay'=>1,'header_scroll_solid'=>1,'header_scroll_threshold'=>60,'header_blur'=>1,'header_blur_amount'=>16,'header_scroll_shadow'=>1,
            'header_sticky_top'=>0,'header_sticky_main'=>1,'header_sticky_bottom'=>0,
            'header_topbar_enabled'=>0,'header_topbar_text'=>'Ücretsiz danışmanlık için bizimle iletişime geçin.','header_topbar_link_text'=>'İletişim','header_topbar_link_url'=>'#',
            'header_announcement_enabled'=>0,'header_announcement_text'=>'Yeni hizmetlerimizi keşfedin.','header_announcement_link_text'=>'İncele','header_announcement_link_url'=>'#',
            'header_mobile_drawer_side'=>'right','header_mobile_overlay'=>1,'header_mobile_close_text'=>'Menüyü Kapat','header_mobile_preset'=>'classic','header_mobile_logo_position'=>'left','header_mobile_logo_width'=>160,'header_mobile_logo_height'=>44,'header_mobile_logo_scroll_width'=>150,'header_mobile_logo_scroll_height'=>40,'header_mobile_search'=>0,'header_mobile_account'=>0,'header_mobile_cart'=>0,'header_mobile_cta_enabled'=>0,'header_mobile_cta_text'=>'Teklif Al','header_mobile_cta_url'=>'#iletisim','header_hide_on_scroll'=>0,'header_hide_scroll_delta'=>12,'header_search_enabled'=>0,'header_search_placeholder'=>'Sitede ara...','header_account_enabled'=>0,'header_account_url'=>'','header_cart_enabled'=>0,'header_cart_url'=>'','header_mobile_bottom_nav'=>0,
            'header_preset'=>'custom','header_layout_style'=>'normal','header_boxed_width'=>1260,'header_boxed_top'=>16,'header_boxed_side'=>24,'header_boxed_radius'=>14,'header_boxed_background'=>'#ffffff','header_boxed_border_color'=>'rgba(15,23,42,.08)','header_boxed_border_width'=>1,'header_boxed_shadow'=>'soft','header_boxed_mobile'=>0,'header_desktop_height'=>78,'header_scrolled_height'=>64,'header_menu_gap'=>28,'header_menu_hover'=>'none','header_menu_active'=>'shadow','header_mobile_drawer_width'=>390,'header_scrolled_logo_id'=>0,'header_scrolled_logo_width'=>0,'header_scrolled_logo_height'=>44,'header_glass_style'=>'off',
            'header_row_top_enabled'=>1,'header_row_bottom_enabled'=>1,'header_row_top_height_desktop'=>38,'header_row_top_height_tablet'=>36,'header_row_top_height_mobile'=>34,'header_row_top_height_scrolled'=>32,'header_row_top_background'=>'#f8fafc','header_row_top_text_color'=>'#111827','header_row_top_border_color'=>'#e5e7eb','header_row_top_border_width'=>1,'header_row_top_container'=>1200,'header_row_top_full_width'=>0,'header_row_top_show_desktop'=>1,'header_row_top_show_tablet'=>1,'header_row_top_show_mobile'=>0,
            'header_row_main_height_desktop'=>78,'header_row_main_height_tablet'=>70,'header_row_main_height_mobile'=>64,'header_row_main_height_scrolled'=>62,'header_row_main_background'=>'#ffffff','header_row_main_text_color'=>'#111827','header_row_main_border_color'=>'#e5e7eb','header_row_main_border_width'=>1,'header_row_main_container'=>1200,'header_row_main_full_width'=>0,'header_row_main_show_desktop'=>1,'header_row_main_show_tablet'=>1,'header_row_main_show_mobile'=>1,
            'header_row_bottom_height_desktop'=>38,'header_row_bottom_height_tablet'=>36,'header_row_bottom_height_mobile'=>34,'header_row_bottom_height_scrolled'=>32,'header_row_bottom_background'=>'#f8fafc','header_row_bottom_text_color'=>'#111827','header_row_bottom_border_color'=>'#e5e7eb','header_row_bottom_border_width'=>0,'header_row_bottom_container'=>1200,'header_row_bottom_full_width'=>0,'header_row_bottom_show_desktop'=>1,'header_row_bottom_show_tablet'=>1,'header_row_bottom_show_mobile'=>0,
            'header_scrolled_background'=>'#ffffff','header_scrolled_text_color'=>'#111827','header_transparent_text_color'=>'#ffffff','header_announcement_background'=>'#2563eb','header_announcement_text_color'=>'#ffffff',
            'header_announcement_dismissible'=>0,'header_mobile_contact_enabled'=>0,'header_mobile_contact_title'=>'Hızlı İletişim','header_mobile_phone'=>'','header_mobile_email'=>'','header_mobile_drawer_style'=>'clean','header_mobile_drawer_logo'=>1,'header_mobile_social_enabled'=>0,'header_mobile_social_instagram'=>'','header_mobile_social_facebook'=>'','header_mobile_social_youtube'=>'','header_mobile_social_linkedin'=>'',
            'footer_cta_enabled'=>0,'footer_cta_title'=>'Yeni projeniz için hazır mısınız?','footer_cta_text'=>'İhtiyacınızı konuşalım, size uygun çözümü birlikte planlayalım.','footer_cta_button_text'=>'Teklif Al','footer_cta_button_url'=>'#iletisim',
            'footer_bottom_enabled'=>1,'footer_bottom_text'=>'© {year} {site}. Tüm hakları saklıdır.','footer_bottom_menu'=>0,'footer_divider'=>1,
            'footer_mobile_columns'=>1,'footer_mobile_align'=>'left','footer_reveal'=>0,'footer_reveal_offset'=>80,'footer_preset'=>'custom','footer_newsletter_enabled'=>0,'footer_newsletter_title'=>'Güncel kalın','footer_newsletter_text'=>'Yeni içerik ve duyuruları e-posta ile alın.','footer_newsletter_placeholder'=>'E-posta adresiniz','footer_newsletter_button'=>'Abone Ol','footer_newsletter_action'=>'','footer_contact_enabled'=>0,'footer_phone'=>'','footer_email'=>'','footer_address'=>'','footer_hours'=>'','footer_payments_enabled'=>0,'footer_payment_text'=>'Güvenli ödeme','footer_payment_visa'=>1,'footer_payment_mastercard'=>1,'footer_payment_amex'=>0,'footer_payment_paypal'=>0,'footer_payment_iyzico'=>1,'footer_mobile_accordion'=>0,'footer_mobile_title_left'=>'Kurumsal','footer_mobile_title_center'=>'Bağlantılar','footer_mobile_title_right'=>'İletişim',
            'footer_row_top_height_desktop'=>76,'footer_row_top_height_tablet'=>72,'footer_row_top_height_mobile'=>68,'footer_row_top_background'=>'#111827','footer_row_top_text_color'=>'#ffffff','footer_row_top_border_color'=>'#243047','footer_row_top_border_width'=>0,'footer_row_top_container'=>1200,'footer_row_top_full_width'=>0,'footer_row_top_show_desktop'=>1,'footer_row_top_show_tablet'=>1,'footer_row_top_show_mobile'=>1,
            'footer_row_main_height_desktop'=>190,'footer_row_main_height_tablet'=>170,'footer_row_main_height_mobile'=>150,'footer_row_main_background'=>'#111827','footer_row_main_text_color'=>'#ffffff','footer_row_main_border_color'=>'#243047','footer_row_main_border_width'=>0,'footer_row_main_container'=>1200,'footer_row_main_full_width'=>0,'footer_row_main_show_desktop'=>1,'footer_row_main_show_tablet'=>1,'footer_row_main_show_mobile'=>1,
            'footer_row_bottom_height_desktop'=>64,'footer_row_bottom_height_tablet'=>62,'footer_row_bottom_height_mobile'=>60,'footer_row_bottom_background'=>'#0b1220','footer_row_bottom_text_color'=>'#cbd5e1','footer_row_bottom_border_color'=>'#243047','footer_row_bottom_border_width'=>1,'footer_row_bottom_container'=>1200,'footer_row_bottom_full_width'=>0,'footer_row_bottom_show_desktop'=>1,'footer_row_bottom_show_tablet'=>1,'footer_row_bottom_show_mobile'=>1,
            'global_body_font'=>'system','global_heading_font'=>'system','global_google_fonts'=>1,'global_base_font_size'=>16,'global_base_font_size_tablet'=>16,'global_base_font_size_mobile'=>15,'global_body_line_height'=>1.65,
            'global_heading_weight'=>800,'global_heading_line_height'=>1.10,'global_heading_letter_spacing'=>-0.02,
            'global_h1_size'=>56,'global_h2_size'=>42,'global_h3_size'=>30,'global_h4_size'=>22,'global_h5_size'=>18,'global_h6_size'=>16,
            'global_h1_tablet'=>46,'global_h2_tablet'=>36,'global_h3_tablet'=>27,'global_h4_tablet'=>21,'global_h5_tablet'=>18,'global_h6_tablet'=>16,
            'global_h1_mobile'=>36,'global_h2_mobile'=>30,'global_h3_mobile'=>24,'global_h4_mobile'=>20,'global_h5_mobile'=>17,'global_h6_mobile'=>15,
            'global_button_weight'=>800,'global_button_letter_spacing'=>0,'global_button_text_transform'=>'none','global_custom_body_font'=>'','global_custom_heading_font'=>'','global_link'=>'#2563eb','global_link_hover'=>'#1d4ed8','global_link_underline'=>0,'global_link_hover_underline'=>1,'global_input_bg'=>'#ffffff','global_input_text'=>'#0f172a','global_input_border'=>'#cbd5e1','global_input_focus'=>'#2563eb','global_input_height'=>48,'global_input_radius'=>10,'global_input_border_width'=>1,'global_surface_alt'=>'#f8fafc','global_surface_dark'=>'#0f172a'
        ) );
        if ( ! is_admin() && ! wp_doing_ajax() ) {
            $settings = $this->apply_display_condition_templates( $settings );
        }
        return $settings;
    }

    /**
     * Resolve conditional Header/Footer Elementor templates for the current request.
     * Display Conditions override the configured default template only when they match.
     */
    private function apply_display_condition_templates( $settings ) {
        if ( ! class_exists( 'WPST_Display_Conditions' ) ) return $settings;

        // Elementor editor/preview should render the document being edited, not hijack it
        // with a site's conditional Header/Footer.
        if ( isset($_GET['elementor-preview']) || isset($_GET['elementor_library']) ) return $settings;

        foreach ( array('header','footer') as $type ) {
            $template_id = $this->conditional_location_template_id( $type );
            if ( ! $template_id ) continue;

            $settings[$type.'_enabled'] = 1;
            $settings[$type.'_mode'] = 'elementor';
            $settings[$type.'_template'] = $template_id;

            if ( 'header' === $type ) {
                /*
                 * Display Conditions routes the desktop/default Header template, but it
                 * must NOT destroy an explicitly configured mobile source/template.
                 *
                 * Example: Desktop = conditional Elementor, Mobile = dedicated Elementor
                 * (or Live Builder). The old code forced mobile to Elementor and then set
                 * mobile_header_template=0, so the selected mobile template was silently
                 * replaced by the conditional/desktop Header. Keep the user's mobile choice
                 * intact and let render_header_hybrid() resolve each device independently.
                 */
                $settings['header_desktop_source'] = 'elementor';
                $settings['_wpst_condition_header'] = $template_id;
            } else {
                // Footer conditions continue to represent the complete responsive footer.
                $settings['mobile_'.$type.'_template'] = 0;
            }
        }
        return $settings;
    }

    private function conditional_location_template_id( $type ) {
        static $cache = array();
        $type = ('footer' === $type) ? 'footer' : 'header';
        if ( array_key_exists($type,$cache) ) return $cache[$type];

        $ids = get_posts(array(
            'post_type'      => 'elementor_library',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'no_found_rows'  => true,
            'meta_query'     => array(
                'relation' => 'AND',
                array('key'=>'_wpst_hf_type','value'=>$type,'compare'=>'='),
                array('key'=>'_wpst_display_conditions','compare'=>'EXISTS')
            )
        ));

        $matches = array();
        foreach ( (array)$ids as $id ) {
            $rules = get_post_meta($id,'_wpst_display_conditions',true);
            // An empty conditions meta must not silently become a global override.
            if ( ! is_array($rules) || empty($rules) ) continue;
            if ( ! WPST_Display_Conditions::match_template($id) ) continue;

            $matches[] = array(
                'id'       => absint($id),
                'priority' => WPST_Display_Conditions::priority($id),
                'modified' => (int)get_post_modified_time('U',true,$id)
            );
        }

        if ( empty($matches) ) {
            $cache[$type] = 0;
            return 0;
        }

        usort($matches,function($a,$b){
            if ( $a['priority'] !== $b['priority'] ) return $a['priority'] <=> $b['priority'];
            if ( $a['modified'] !== $b['modified'] ) return $b['modified'] <=> $a['modified'];
            return $b['id'] <=> $a['id'];
        });

        $cache[$type] = absint($matches[0]['id']);
        return $cache[$type];
    }

    public function render_header() {
        if ( $this->header_rendered || is_admin() || wp_doing_ajax() ) return;
        $s = $this->get_settings();
        if ( empty( $s['header_enabled'] ) ) return;
        $this->header_rendered = true;
        echo $this->render_header_hybrid( $s );
    }

    private function header_device_source( $settings, $device ) {
        $key = 'desktop' === $device ? 'header_desktop_source' : 'header_mobile_source';
        $source = isset($settings[$key]) ? sanitize_key($settings[$key]) : 'inherit';
        if ( ! in_array($source,array('inherit','builder','elementor'),true) ) $source='inherit';

        // Explicit device choice wins.
        if ( in_array($source,array('builder','elementor'),true) ) return $source;

        // Inherit / automatic resolution.
        if ( 'desktop' === $device ) {
            if ( !empty($settings['header_template']) ) return 'elementor';
            return (isset($settings['header_mode']) && 'elementor' === $settings['header_mode']) ? 'elementor' : 'builder';
        }

        // Mobile:
        // 1) dedicated mobile Elementor template
        // 2) otherwise the selected desktop Elementor template
        // 3) otherwise legacy global mode / Live Builder
        if ( !empty($settings['mobile_header_template']) ) return 'elementor';
        if ( !empty($settings['header_template']) ) return 'elementor';

        return (isset($settings['header_mode']) && 'elementor' === $settings['header_mode']) ? 'elementor' : 'builder';
    }

    private function render_header_source( $settings, $source, $device ) {
        $copy = $settings;

        if ( 'elementor' === $source ) {
            $copy['header_mode'] = 'elementor';

            if ( 'desktop' === $device ) {
                // Desktop uses only the desktop Elementor template.
                $copy['mobile_header_template'] = 0;
                if ( empty($copy['header_template']) ) return '';
            } else {
                // Dedicated mobile template is optional.
                // When absent, reuse the selected desktop Elementor Header on mobile.
                $mobile_id = !empty($copy['mobile_header_template'])
                    ? absint($copy['mobile_header_template'])
                    : absint($copy['header_template']);

                if ( ! $mobile_id ) return '';

                // Force this render branch to output only the mobile slot.
                $copy['header_template'] = 0;
                $copy['mobile_header_template'] = $mobile_id;
            }

            $html = $this->render_elementor_location( 'header', $copy );
            // Mark the concrete device render source so frontend.js never boots
            // Live Builder's legacy mobile drawer for an Elementor-owned header.
            if ( $html ) {
                $html = preg_replace('/(<(?:div|header)\b[^>]*class=\"[^\"]*wpsoft-site-header[^\"]*\")/', '$1 data-wpst-render-source=\"elementor\" data-wpst-render-device=\"' . esc_attr($device) . '\"', $html, 1);
            }
            return $html;
        }

        $copy['header_mode'] = 'builder';
        $html = $this->render_location( 'header', $copy );
        // Builder source is explicit as well; this prevents source leakage when
        // desktop and mobile headers coexist in the hybrid DOM.
        if ( $html ) {
            $html = preg_replace('/(<(?:div|header)\b[^>]*class=\"[^\"]*wpsoft-site-header[^\"]*\")/', '$1 data-wpst-render-source=\"builder\" data-wpst-render-device=\"' . esc_attr($device) . '\"', $html, 1);
        }
        return $html;
    }

    private function render_header_hybrid( $settings ) {
        /*
         * A conditional Header is the desktop/default template only. Do not short-circuit
         * hybrid rendering here: a separately selected mobile Elementor template or mobile
         * Live Builder source must still win on its device range. When no dedicated mobile
         * choice exists, header_device_source() naturally inherits this conditional Elementor
         * template for mobile as before.
         */

        $desktop_source = $this->header_device_source($settings,'desktop');
        $mobile_source  = $this->header_device_source($settings,'mobile');
        $desktop = $this->render_header_source($settings,$desktop_source,'desktop');
        $mobile  = $this->render_header_source($settings,$mobile_source,'mobile');

        // If both device sources resolve to the same Builder configuration, render once.
        if ( 'builder' === $desktop_source && 'builder' === $mobile_source ) return $desktop;

        // If both use Elementor and there is no dedicated mobile template, one normal render is enough.
        if ( 'elementor' === $desktop_source && 'elementor' === $mobile_source && empty($settings['mobile_header_template']) ) {
            $copy=$settings;
            $copy['header_mode']='elementor';
            return $this->render_elementor_location('header',$copy);
        }

        $breakpoint = max(320,min(1440,absint($settings['header_mobile_breakpoint'])));
        if ( ! $breakpoint ) $breakpoint=768;
        $uid='wpst-hybrid-header-'.substr(md5($desktop_source.'|'.$mobile_source.'|'.$breakpoint),0,8);

        /*
         * Hybrid source visibility must follow the Header Builder breakpoint, not
         * the legacy global 767px Elementor helper classes. This is especially
         * important when Mobile = Elementor and Desktop = Live Builder (or vice
         * versa): the selected source must be the only source visible for that
         * device range.
         */
        $style='<style id="'.esc_attr($uid).'-css">'
             .'.'.esc_attr($uid).'-desktop{display:block}.'.esc_attr($uid).'-mobile{display:none}'
             .'.'.esc_attr($uid).'-desktop .wpst-mobile-template{display:none!important}'
             .'.'.esc_attr($uid).'-desktop :is(.wpst-desktop-template,.wpst-all-template){display:block!important}'
             .'.'.esc_attr($uid).'-mobile .wpst-desktop-template{display:none!important}'
             .'.'.esc_attr($uid).'-mobile :is(.wpst-mobile-template,.wpst-all-template){display:block!important}'
             .'@media(max-width:'.absint($breakpoint-1).'px){.'.esc_attr($uid).'-desktop{display:none!important}.'.esc_attr($uid).'-mobile{display:block!important}}'
             .'</style>';

        return $style
            .'<div class="'.esc_attr($uid).'-desktop wpst-hybrid-header-desktop" data-wpst-header-source="'.esc_attr($desktop_source).'">'.$desktop.'</div>'
            .'<div class="'.esc_attr($uid).'-mobile wpst-hybrid-header-mobile" data-wpst-header-source="'.esc_attr($mobile_source).'">'.$mobile.'</div>';
    }

    private function render_header_bars( $settings ) {
        $html = '';
        if ( ! empty( $settings['header_announcement_enabled'] ) ) {
            $html .= '<div class="wpst-announcement" data-wpst-announcement><div class="wpst-bar-inner"><span>' . esc_html( $settings['header_announcement_text'] ) . '</span>';
            if ( ! empty( $settings['header_announcement_link_text'] ) ) {
                $html .= '<a href="' . esc_url( $settings['header_announcement_link_url'] ) . '">' . esc_html( $settings['header_announcement_link_text'] ) . ' <span aria-hidden="true">→</span></a>';
            }
            if(!empty($settings['header_announcement_dismissible'])) $html.='<button type="button" class="wpst-announcement-close" data-wpst-announcement-close aria-label="Duyuruyu kapat">×</button>';
            $html .= '</div></div>';
        }
        if ( ! empty( $settings['header_topbar_enabled'] ) ) {
            $html .= '<div class="wpst-topbar"><div class="wpst-bar-inner"><span>' . esc_html( $settings['header_topbar_text'] ) . '</span>';
            if ( ! empty( $settings['header_topbar_link_text'] ) ) {
                $html .= '<a href="' . esc_url( $settings['header_topbar_link_url'] ) . '">' . esc_html( $settings['header_topbar_link_text'] ) . '</a>';
            }
            $html .= '</div></div>';
        }
        return $html;
    }

    public function render_scroll_top() {
        $settings = $this->get_settings();
        if ( empty($settings['scroll_top_enabled']) ) return;

        $classes = array(
            'wpst-scroll-top',
            'is-' . sanitize_html_class($settings['scroll_top_position'] ?? 'right'),
            'is-' . sanitize_html_class($settings['scroll_top_style'] ?? 'soft'),
        );
        if ( empty($settings['scroll_top_mobile']) ) $classes[] = 'is-mobile-hidden';

        echo '<button type="button" class="'.esc_attr(implode(' ',$classes)).'" data-wpst-scroll-top data-threshold="'.absint($settings['scroll_top_threshold'] ?? 320).'" aria-label="'.esc_attr__('Sayfanın en üstüne dön','wpsoft-site-tools').'" title="'.esc_attr__('Yukarı','wpsoft-site-tools').'">';
        echo '<span aria-hidden="true"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 15 6-6 6 6"/></svg></span>';
        echo '</button>';
    }

    public function render_footer() { if ( $this->footer_rendered || is_admin() || wp_doing_ajax() ) return; $s=$this->get_settings(); if(empty($s['footer_enabled']))return; $this->footer_rendered=true; echo $this->render_location('footer',$s); }

    private function footer_text_tokens($text){
        return strtr((string)$text,array('{year}'=>gmdate('Y'),'{site}'=>get_bloginfo('name')));
    }

    private function render_footer_cta($settings){
        if(empty($settings['footer_cta_enabled'])) return '';
        $html='<div class="wpst-footer-cta"><div class="wpst-footer-cta-inner"><div class="wpst-footer-cta-copy"><strong>'.esc_html($settings['footer_cta_title']).'</strong>';
        if(!empty($settings['footer_cta_text'])) $html.='<span>'.esc_html($settings['footer_cta_text']).'</span>';
        $html.='</div>';
        if(!empty($settings['footer_cta_button_text'])) $html.='<a href="'.esc_url($settings['footer_cta_button_url']).'">'.esc_html($settings['footer_cta_button_text']).' <span aria-hidden="true">→</span></a>';
        return $html.'</div></div>';
    }

    private function render_footer_extras($settings){
        $html = '';
        if ( ! empty($settings['footer_newsletter_enabled']) ) {
            $action = ! empty($settings['footer_newsletter_action']) ? $settings['footer_newsletter_action'] : '#';
            $html .= '<section class="wpst-footer-newsletter-pro"><div class="wpst-footer-extra-inner"><div class="wpst-footer-newsletter-copy"><strong>'.esc_html($settings['footer_newsletter_title']).'</strong>';
            if(!empty($settings['footer_newsletter_text'])) $html .= '<span>'.esc_html($settings['footer_newsletter_text']).'</span>';
            $html .= '</div><form class="wpst-footer-newsletter-form" method="post" action="'.esc_url($action).'"><label class="screen-reader-text" for="wpst-footer-email">E-posta</label><input id="wpst-footer-email" type="email" name="email" required placeholder="'.esc_attr($settings['footer_newsletter_placeholder']).'"><button type="submit">'.esc_html($settings['footer_newsletter_button']).'</button></form></div></section>';
        }
        if ( ! empty($settings['footer_contact_enabled']) || ! empty($settings['footer_payments_enabled']) ) {
            $html .= '<div class="wpst-footer-utility"><div class="wpst-footer-extra-inner">';
            if ( ! empty($settings['footer_contact_enabled']) ) {
                $html .= '<div class="wpst-footer-contact-mini">';
                if(!empty($settings['footer_phone'])) $html .= '<a href="tel:'.esc_attr(preg_replace('/[^0-9+]/','',$settings['footer_phone'])).'"><span>Telefon</span><b>'.esc_html($settings['footer_phone']).'</b></a>';
                if(!empty($settings['footer_email'])) $html .= '<a href="mailto:'.esc_attr($settings['footer_email']).'"><span>E-posta</span><b>'.esc_html($settings['footer_email']).'</b></a>';
                if(!empty($settings['footer_address'])) $html .= '<div><span>Adres</span><b>'.esc_html($settings['footer_address']).'</b></div>';
                if(!empty($settings['footer_hours'])) $html .= '<div><span>Çalışma Saatleri</span><b>'.esc_html($settings['footer_hours']).'</b></div>';
                $html .= '</div>';
            }
            if ( ! empty($settings['footer_payments_enabled']) ) {
                $html .= '<div class="wpst-footer-payments"><span>'.esc_html($settings['footer_payment_text']).'</span><div>';
                $labels=array('visa'=>'VISA','mastercard'=>'Mastercard','amex'=>'AMEX','paypal'=>'PayPal','iyzico'=>'iyzico');
                foreach($labels as $brand=>$label){ if(!empty($settings['footer_payment_'.$brand])) $html.='<i class="wpst-pay-'.$brand.'">'.esc_html($label).'</i>'; }
                $html .= '</div></div>';
            }
            $html .= '</div></div>';
        }
        return $html;
    }

    private function render_footer_bottom($settings){
        if(empty($settings['footer_bottom_enabled'])) return '';
        $html='<div class="wpst-footer-bottom'.(!empty($settings['footer_divider'])?' has-divider':'').'"><div class="wpst-footer-bottom-inner"><span>'.esc_html($this->footer_text_tokens($settings['footer_bottom_text'])).'</span>';
        if(!empty($settings['footer_bottom_menu'])){
            $menu=wp_nav_menu(array('menu'=>absint($settings['footer_bottom_menu']),'container'=>false,'menu_class'=>'wpst-footer-bottom-menu','fallback_cb'=>false,'echo'=>false,'depth'=>1));
            if($menu) $html.=$menu;
        }
        return $html.'</div></div>';
    }

    private function render_location( $type, $settings ) {
        if ( 'elementor' === $settings[ $type . '_mode' ] ) return $this->render_elementor_location( $type, $settings );
        $items = json_decode( $settings[ $type . '_layout' ], true );
        if ( ! is_array( $items ) || empty( $items ) ) return '';
        $tag = 'header' === $type ? 'header' : 'footer';
        $is_header_v2 = 'header' === $type && ! empty( $settings['header_builder_version'] ) && 2 === absint( $settings['header_builder_version'] );
        $is_footer_v2 = 'footer' === $type && ! empty( $settings['footer_builder_version'] ) && 2 === absint( $settings['footer_builder_version'] );
        $count = ( $is_header_v2 || $is_footer_v2 ) ? 9 : max( 1, min( 4, absint( $settings[ $type . '_sections' ] ) ) );
        $button_labels = array();
        foreach ( $items as $layout_item ) {
            if ( isset( $layout_item['type'] ) && 'button' === $layout_item['type'] ) {
                $label = trim( wp_strip_all_tags( isset( $layout_item['text'] ) ? $layout_item['text'] : '' ) );
                if ( '' !== $label ) $button_labels[] = $label;
            }
        }
        $settings['_wpst_button_labels'] = array_values( array_unique( $button_labels ) );
        $extra_data = 'header' === $type ? ' data-wpst-transparent="' . ( ! empty($settings['header_transparent']) && ( empty($settings['header_transparent_home_only']) || is_front_page() ) ? '1' : '0' ) . '" data-wpst-transparent-overlay="' . ( ! empty($settings['header_transparent_overlay']) ? '1' : '0' ) . '" data-wpst-breakpoint="' . absint( $settings['header_mobile_breakpoint'] ) . '" data-wpst-scroll-threshold="' . absint($settings['header_scroll_threshold']) . '" data-wpst-sticky="' . ( ! empty($settings['header_sticky']) ? '1' : '0' ) . '" data-wpst-sticky-mode="' . esc_attr($settings['header_sticky_mode']) . '" data-wpst-sticky-rows="' . esc_attr( implode(',', array_filter(array(!empty($settings['header_sticky_top'])?'top':'',!empty($settings['header_sticky_main'])?'main':'',!empty($settings['header_sticky_bottom'])?'bottom':''))) ) . '" data-wpst-drawer-side="' . esc_attr($settings['header_mobile_drawer_side']) . '" data-wpst-overlay="' . ( ! empty($settings['header_mobile_overlay']) ? '1' : '0' ) . '" data-wpst-close-text="' . esc_attr($settings['header_mobile_close_text']) . '" data-wpst-hide-scroll="' . (!empty($settings['header_hide_on_scroll'])?'1':'0') . '" data-wpst-hide-delta="' . absint($settings['header_hide_scroll_delta']) . '" data-wpst-search="' . (!empty($settings['header_search_enabled'])?'1':'0') . '" data-wpst-account="' . (!empty($settings['header_account_enabled'])?'1':'0') . '" data-wpst-account-url="' . esc_url(!empty($settings['header_account_url'])?$settings['header_account_url']:(is_user_logged_in()?admin_url('profile.php'):wp_login_url())) . '" data-wpst-cart="' . (!empty($settings['header_cart_enabled'])?'1':'0') . '" data-wpst-cart-url="' . esc_url(!empty($settings['header_cart_url'])?$settings['header_cart_url']:(function_exists('wc_get_cart_url')?wc_get_cart_url():'#')) . '" data-wpst-mobile-bottom="' . (!empty($settings['header_mobile_bottom_nav'])?'1':'0') . '" data-wpst-mobile-contact="' . (!empty($settings['header_mobile_contact_enabled'])?'1':'0') . '" data-wpst-mobile-contact-title="' . esc_attr($settings['header_mobile_contact_title']) . '" data-wpst-mobile-phone="' . esc_attr($settings['header_mobile_phone']) . '" data-wpst-mobile-email="' . esc_attr($settings['header_mobile_email']) . '" data-wpst-mobile-drawer-style="' . esc_attr($settings['header_mobile_drawer_style']) . '" data-wpst-mobile-drawer-logo="' . (!empty($settings['header_mobile_drawer_logo'])?'1':'0') . '" data-wpst-mobile-social="' . (!empty($settings['header_mobile_social_enabled'])?'1':'0') . '" data-wpst-mobile-instagram="' . esc_url($settings['header_mobile_social_instagram']) . '" data-wpst-mobile-facebook="' . esc_url($settings['header_mobile_social_facebook']) . '" data-wpst-mobile-youtube="' . esc_url($settings['header_mobile_social_youtube']) . '" data-wpst-mobile-linkedin="' . esc_url($settings['header_mobile_social_linkedin']) . '" data-wpst-preset="' . esc_attr($settings['header_preset']) . '" data-wpst-layout="' . esc_attr($settings['header_layout_style']) . '" data-wpst-boxed-mobile="' . (!empty($settings['header_boxed_mobile'])?'1':'0') . '" data-wpst-mobile-preset="' . esc_attr($settings['header_mobile_preset']) . '" data-wpst-mobile-logo-position="' . esc_attr($settings['header_mobile_logo_position']) . '" data-wpst-mobile-search="' . (!empty($settings['header_mobile_search'])?'1':'0') . '" data-wpst-mobile-account="' . (!empty($settings['header_mobile_account'])?'1':'0') . '" data-wpst-mobile-cart="' . (!empty($settings['header_mobile_cart'])?'1':'0') . '" data-wpst-mobile-cta="' . (!empty($settings['header_mobile_cta_enabled'])?'1':'0') . '" data-wpst-mobile-cta-text="' . esc_attr($settings['header_mobile_cta_text']) . '" data-wpst-mobile-cta-url="' . esc_url($settings['header_mobile_cta_url']) . '" data-wpst-glass="' . esc_attr($settings['header_glass_style']) . '" data-wpst-scroll-solid="' . (!empty($settings['header_scroll_solid'])?'1':'0') . '" data-wpst-state-contract="1" data-wpst-scroll-surface="' . (!empty($settings['header_scroll_solid'])?'solid':'transparent') . '" data-wpst-scroll-blur="' . (!empty($settings['header_blur'])?'1':'0') . '" data-wpst-scroll-shadow="' . (!empty($settings['header_scroll_shadow'])?'1':'0') . '" data-wpst-shrink="' . (!empty($settings['header_shrink'])?'1':'0') . '" data-wpst-shadow-style="' . esc_attr($settings['header_shadow_style']??'normal') . '" data-wpst-menu-hover="' . esc_attr($settings['header_menu_hover']) . '" data-wpst-menu-active="' . esc_attr($settings['header_menu_active']) . '" data-wpst-menu-active-shadow="' . esc_attr($settings['header_menu_active_shadow']??'soft') . '" style="--wpst-header-height:' . absint($settings['header_desktop_height']) . 'px;--wpst-header-scrolled-height:' . absint($settings['header_scrolled_height']) . 'px;--wpst-header-menu-gap:' . absint($settings['header_menu_gap']) . 'px;--wpst-header-boxed-width:' . absint($settings['header_boxed_width']) . 'px;--wpst-header-boxed-top:' . absint($settings['header_boxed_top']) . 'px;--wpst-header-boxed-side:' . absint($settings['header_boxed_side']) . 'px;--wpst-header-boxed-radius:' . absint($settings['header_boxed_radius']) . 'px;--wpst-header-boxed-bg:' . esc_attr($settings['header_boxed_background']) . ';--wpst-header-boxed-border:' . esc_attr($settings['header_boxed_border_color']) . ';--wpst-header-boxed-border-w:' . absint($settings['header_boxed_border_width']) . 'px;--wpst-header-boxed-shadow:' . esc_attr($settings['header_boxed_shadow']) . ';--wpst-header-scroll-bg:' . esc_attr($settings['header_scrolled_background']) . ';--wpst-header-scroll-color:' . esc_attr($settings['header_scrolled_text_color']) . ';--wpst-header-scroll-blur:' . absint($settings['header_blur_amount']) . 'px;--wpst-header-scroll-logo-width:' . absint($settings['header_scrolled_logo_width']) . 'px;--wpst-header-scroll-logo-height:' . absint($settings['header_scrolled_logo_height']) . 'px;--wpst-header-transparent-color:' . esc_attr($settings['header_transparent_text_color']) . ';--wpst-announcement-bg:' . esc_attr($settings['header_announcement_background']) . ';--wpst-announcement-color:' . esc_attr($settings['header_announcement_text_color']) . ';--wpst-mobile-drawer-width:' . absint($settings['header_mobile_drawer_width']) . 'px;--wpst-mobile-logo-width:' . absint($settings['header_mobile_logo_width']) . 'px;--wpst-mobile-logo-height:' . absint($settings['header_mobile_logo_height']) . 'px;--wpst-mobile-logo-scroll-width:' . absint($settings['header_mobile_logo_scroll_width']) . 'px;--wpst-mobile-logo-scroll-height:' . absint($settings['header_mobile_logo_scroll_height']) . 'px"' : ' data-wpst-footer-reveal="' . ( ! empty($settings['footer_reveal']) ? '1' : '0' ) . '" data-wpst-mobile-columns="' . absint($settings['footer_mobile_columns']) . '" data-wpst-mobile-align="' . esc_attr($settings['footer_mobile_align']) . '" data-wpst-footer-preset="' . esc_attr($settings['footer_preset']) . '" data-wpst-mobile-accordion="' . (!empty($settings['footer_mobile_accordion'])?'1':'0') . '" data-wpst-mobile-title-left="' . esc_attr($settings['footer_mobile_title_left']) . '" data-wpst-mobile-title-center="' . esc_attr($settings['footer_mobile_title_center']) . '" data-wpst-mobile-title-right="' . esc_attr($settings['footer_mobile_title_right']) . '" style="--wpst-footer-reveal-offset:' . absint($settings['footer_reveal_offset']) . 'px"';
        $html = '<' . $tag . ' class="wpsoft-site-' . esc_attr( $type ) . '" data-wpst-location="' . esc_attr( $type ) . '"' . $extra_data . '>';
        if ( 'header' === $type ) $html .= $this->render_header_bars( $settings );
        if ( 'footer' === $type ) $html .= $this->render_footer_cta( $settings );
        if ( $is_header_v2 || $is_footer_v2 ) {
            $row_defs = array( 'top' => array(1,2,3), 'main' => array(4,5,6), 'bottom' => array(7,8,9) );
            $zone_names = array( 'left', 'center', 'right' );
            $html .= '<div class="' . ( $is_header_v2 ? 'wpst-header-shell wpst-header-rows-v2' : 'wpst-footer-shell wpst-footer-rows-v2' ) . '">';
            foreach ( $row_defs as $row_key => $row_sections ) {
                if ( $is_header_v2 && 'top' === $row_key && empty($settings['header_row_top_enabled']) ) continue;
                if ( $is_header_v2 && 'bottom' === $row_key && empty($settings['header_row_bottom_enabled']) ) continue;
                $row_has_items = false;
                foreach ( $items as $probe ) { if ( in_array( isset($probe['section']) ? absint($probe['section']) : 0, $row_sections, true ) ) { $row_has_items = true; break; } }
                if ( ! $row_has_items ) continue;
                                $rp = ( $is_header_v2 ? 'header_row_' : 'footer_row_' ) . $row_key . '_';
                $row_classes = $is_header_v2 ? array( 'wpst-header-row', 'wpst-header-row-' . $row_key ) : array( 'wpst-footer-row', 'wpst-footer-row-' . $row_key );
                foreach ( array( 'desktop', 'tablet', 'mobile' ) as $device ) {
                    if ( empty( $settings[ $rp . 'show_' . $device ] ) ) $row_classes[] = 'wpst-hide-' . $device;
                }
                if ( ! empty( $settings[ $rp . 'full_width' ] ) ) $row_classes[] = 'is-full-width';
                $row_style = '--wpst-row-h-desktop:' . absint($settings[$rp.'height_desktop']) . 'px;--wpst-row-h-tablet:' . absint($settings[$rp.'height_tablet']) . 'px;--wpst-row-h-mobile:' . absint($settings[$rp.'height_mobile']) . 'px;' . ( $is_header_v2 ? '--wpst-row-h-scrolled:' . absint($settings[$rp.'height_scrolled']) . 'px;' : '' ) . '--wpst-row-bg:' . esc_attr($settings[$rp.'background']) . ';--wpst-row-color:' . esc_attr($settings[$rp.'text_color']) . ';--wpst-row-border:' . esc_attr($settings[$rp.'border_color']) . ';--wpst-row-border-w:' . absint($settings[$rp.'border_width']) . 'px;--wpst-row-container:' . absint($settings[$rp.'container']) . 'px;';
                $html .= '<div class="' . esc_attr( implode( ' ', $row_classes ) ) . '" data-wpst-row="' . esc_attr($row_key) . '" style="' . esc_attr($row_style) . '"><div class="' . ( $is_header_v2 ? 'wpst-header-row-inner' : 'wpst-footer-row-inner' ) . '">';
                foreach ( $row_sections as $idx => $section ) {
                    $html .= '<div class="wpst-q-section ' . ( $is_header_v2 ? 'wpst-header-zone wpst-header-zone-' : 'wpst-footer-zone wpst-footer-zone-' ) . esc_attr($zone_names[$idx]) . '" data-wpst-section="' . absint($section) . '">';
                    foreach ( $items as $item ) {
                        $item_section = isset( $item['section'] ) ? max( 1, min( 9, absint( $item['section'] ) ) ) : 5;
                        if ( $item_section === $section ) $html .= $this->render_block( $item, $type, $settings );
                    }
                    $html .= '</div>';
                }
                $html .= '</div></div>';
            }
            $html .= '</div>';
        } else {
            $html .= '<div class="wpst-quick-inner wpst-sections-' . $count . '">';
            for ( $section = 1; $section <= $count; $section++ ) {
                $html .= '<div class="wpst-q-section wpst-q-section-' . $section . '" data-wpst-section="' . $section . '">';
                foreach ( $items as $item ) {
                    $item_section = isset( $item['section'] ) ? max( 1, min( 4, absint( $item['section'] ) ) ) : 1;
                    if ( $item_section === $section ) $html .= $this->render_block( $item, $type, $settings );
                }
                $html .= '</div>';
            }
            $html .= '</div>';
        }
        if ( 'footer' === $type ) {
            $html .= $this->render_footer_extras( $settings );
            $html .= $this->render_footer_bottom( $settings );
        }
        if ( 'header' === $type ) {
            if(!empty($settings['header_search_enabled']) || !empty($settings['header_mobile_search'])){
                $html.='<div class="wpst-search-popup" aria-hidden="true"><button type="button" class="wpst-search-close" aria-label="Aramayı kapat">×</button><form role="search" method="get" action="'.esc_url(home_url('/')).'"><input type="search" name="s" placeholder="'.esc_attr($settings['header_search_placeholder']).'"><button type="submit">Ara</button></form></div>';
            }
            $actions='';
            if(!empty($settings['header_search_enabled']))$actions.='<button type="button" data-wpst-search-toggle aria-label="Ara">⌕</button>';
            if(!empty($settings['header_account_enabled'])){$u=!empty($settings['header_account_url'])?$settings['header_account_url']:(is_user_logged_in()?admin_url('profile.php'):wp_login_url());$actions.='<a href="'.esc_url($u).'" aria-label="Hesap">◎</a>';}
            if(!empty($settings['header_cart_enabled'])){$u=!empty($settings['header_cart_url'])?$settings['header_cart_url']:(function_exists('wc_get_cart_url')?wc_get_cart_url():'#');$actions.='<a href="'.esc_url($u).'" aria-label="Sepet">▣</a>';}
            if($actions && !empty($settings['header_mobile_bottom_nav']))$html.='<nav class="wpst-mobile-bottom-actions">'.$actions.'</nav>';
        }
        $html .= '</' . $tag . '>';
        return $html;
    }

    private function render_block( $item, $location = 'header', $settings = array() ) {
        $type = isset( $item['type'] ) ? $item['type'] : '';
        $extra_class = ! empty( $item['hide_mobile'] ) ? ' wpst-hide-mobile' : '';
        if ( ! empty( $item['class'] ) ) $extra_class .= ' ' . sanitize_html_class( $item['class'] );
        if ( 'logo' === $type ) {
            $logo_id = ! empty( $settings[ $location . '_logo_id' ] ) ? absint( $settings[ $location . '_logo_id' ] ) : 0;
            if ( $logo_id ) {
                $image = wp_get_attachment_image( $logo_id, 'full', false, array( 'class' => 'wpst-custom-logo', 'loading' => 'eager' ) );
                $logo = $image ? '<a href="' . esc_url( home_url('/') ) . '" class="wpst-logo-link" rel="home">' . $image . '</a>' : '';
            } else {
                $logo = get_custom_logo();
            }
            $normal_logo = $logo ? $logo : '<a href="' . esc_url( home_url('/') ) . '">' . esc_html( get_bloginfo('name') ) . '</a>';
            $normal_logo = '<span class="wpst-logo-normal">' . $normal_logo . '</span>';
            $scroll_logo = '';
            if ( 'header' === $location && ! empty($settings['header_scrolled_logo_id']) ) {
                $scroll_image = wp_get_attachment_image( absint($settings['header_scrolled_logo_id']), 'full', false, array( 'class'=>'wpst-custom-logo wpst-custom-logo-scroll', 'loading'=>'eager' ) );
                if ( $scroll_image ) $scroll_logo = '<span class="wpst-logo-scroll"><a href="' . esc_url(home_url('/')) . '" class="wpst-logo-link" rel="home">' . $scroll_image . '</a></span>';
            }
            return '<div class="wpst-q-logo' . esc_attr( $extra_class ) . '">' . $normal_logo . $scroll_logo . '</div>';
        }
        if ( 'menu' === $type ) {
            $menu_id = ! empty( $item['menu'] ) ? absint( $item['menu'] ) : 0;
            $args = array( 'container'=>false, 'menu_class'=>'wpst-q-menu', 'fallback_cb'=>false, 'echo'=>false, 'wpst_navigation'=>'1', 'wpst_exclude_labels'=>isset( $settings['_wpst_button_labels'] ) ? $settings['_wpst_button_labels'] : array() );
            if ( $menu_id ) $args['menu'] = $menu_id;
            $menu_html = wp_nav_menu( $args );
            if ( ! $menu_html ) {
                $pages = get_pages( array( 'sort_column' => 'menu_order,post_title' ) );
                $links = '';
                foreach ( $pages as $page ) {
                    $title = trim( get_the_title( $page ) );
                    if ( $this->label_is_excluded( $title, $args['wpst_exclude_labels'] ) ) continue;
                    $links .= '<li class="page_item page-item-' . absint( $page->ID ) . '"><a href="' . esc_url( get_permalink( $page ) ) . '">' . esc_html( $title ) . '</a></li>';
                }
                $menu_html = $links ? '<ul class="wpst-q-menu">' . $links . '</ul>' : '';
            }
            return '<nav class="wpst-q-nav' . esc_attr( $extra_class ) . '" aria-label="Site menüsü">' . $menu_html . '</nav>';
        }
        if ( 'button' === $type ) {
            $button_vars = '';
            if ( ! empty( $item['button_bg'] ) ) $button_vars .= '--wpst-header-button-bg:' . sanitize_hex_color( $item['button_bg'] ) . ';';
            if ( ! empty( $item['button_text_color'] ) ) $button_vars .= '--wpst-header-button-text:' . sanitize_hex_color( $item['button_text_color'] ) . ';';
            if ( ! empty( $item['button_hover_bg'] ) ) $button_vars .= '--wpst-header-button-hover-bg:' . sanitize_hex_color( $item['button_hover_bg'] ) . ';';
            if ( ! empty( $item['button_hover_text_color'] ) ) $button_vars .= '--wpst-header-button-hover-text:' . sanitize_hex_color( $item['button_hover_text_color'] ) . ';';
            return '<a class="wpst-q-button' . esc_attr( $extra_class ) . '"' . ( $button_vars ? ' style="' . esc_attr( $button_vars ) . '"' : '' ) . ' href="' . esc_url( ! empty($item['url']) ? $item['url'] : '#' ) . '">' . esc_html( ! empty($item['text']) ? $item['text'] : 'Buton' ) . '</a>';
        }
        if ( 'search' === $type ) return '<button type="button" class="wpst-q-action wpst-q-search'.esc_attr($extra_class).'" data-wpst-search-toggle aria-label="Ara"><svg viewBox="0 0 24 24"><path d="m20 20-4.2-4.2M18 11a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"/></svg></button>';
        if ( 'account' === $type ) {
            $url=!empty($item['url'])?$item['url']:'';
            if(!$url)$url=!empty($settings['header_account_url'])?$settings['header_account_url']:(is_user_logged_in()?admin_url('profile.php'):wp_login_url());
            return '<a class="wpst-q-action wpst-q-account'.esc_attr($extra_class).'" href="'.esc_url($url).'" aria-label="Hesabım"><svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4.5 21a7.5 7.5 0 0 1 15 0"/></svg></a>';
        }
        if ( 'cart' === $type ) {
            $url=!empty($item['url'])?$item['url']:'';
            if(!$url)$url=!empty($settings['header_cart_url'])?$settings['header_cart_url']:(function_exists('wc_get_cart_url')?wc_get_cart_url():'#');
            $count=(function_exists('WC') && WC()->cart)?WC()->cart->get_cart_contents_count():0;
            return '<a class="wpst-q-action wpst-q-cart'.esc_attr($extra_class).'" href="'.esc_url($url).'" aria-label="Sepet"><svg viewBox="0 0 24 24"><path d="M3 4h2l2 11h11l2-8H7"/><circle cx="9" cy="20" r="1.5"/><circle cx="18" cy="20" r="1.5"/></svg>'.($count?'<span>'.absint($count).'</span>':'').'</a>';
        }
        if ( 'text' === $type ) return '<div class="wpst-q-text' . esc_attr( $extra_class ) . '">' . esc_html( ! empty($item['text']) ? $item['text'] : get_bloginfo('description') ) . '</div>';
        if ( 'html' === $type ) return '<div class="wpst-q-html' . esc_attr( $extra_class ) . '">' . wp_kses_post( isset( $item['html'] ) ? $item['html'] : '' ) . '</div>';
        if ( 'social' === $type ) {
            $links = '';
            $map = array( 'instagram'=>'◎', 'facebook'=>'f', 'linkedin'=>'in', 'x'=>'X' );
            foreach ( $map as $key=>$label ) { if ( ! empty( $item[$key] ) ) $links .= '<a href="' . esc_url( $item[$key] ) . '" target="_blank" rel="noopener" aria-label="' . esc_attr( ucfirst($key) ) . '">' . esc_html($label) . '</a>'; }
            if ( '' === $links ) $links = '<span class="wpst-social-empty">Sosyal bağlantı ekleyin</span>';
            return '<div class="wpst-q-social' . esc_attr( $extra_class ) . '">' . $links . '</div>';
        }
        if ( 'spacer' === $type ) return '<span class="wpst-q-spacer' . esc_attr( $extra_class ) . '" aria-hidden="true"></span>';
        if ( 'copyright' === $type ) return '<div class="wpst-q-text' . esc_attr( $extra_class ) . '">© ' . esc_html( gmdate('Y') . ' ' . get_bloginfo('name') ) . '</div>';
        return '';
    }

    public function filter_menu_objects( $items, $args ) {
        if ( empty( $args->wpst_exclude_labels ) || ! is_array( $args->wpst_exclude_labels ) ) return $items;
        $excluded_ids = array();
        foreach ( $items as $menu_item ) {
            if ( $this->label_is_excluded( $menu_item->title, $args->wpst_exclude_labels ) ) $excluded_ids[] = (int) $menu_item->ID;
        }
        if ( empty( $excluded_ids ) ) return $items;
        $changed = true;
        while ( $changed ) {
            $changed = false;
            foreach ( $items as $menu_item ) {
                if ( in_array( (int) $menu_item->menu_item_parent, $excluded_ids, true ) && ! in_array( (int) $menu_item->ID, $excluded_ids, true ) ) { $excluded_ids[] = (int) $menu_item->ID; $changed = true; }
            }
        }
        return array_values( array_filter( $items, function( $menu_item ) use ( $excluded_ids ) { return ! in_array( (int) $menu_item->ID, $excluded_ids, true ); } ) );
    }

    private function label_is_excluded( $label, $excluded ) {
        $normalize = function( $value ) {
            $value = html_entity_decode( wp_strip_all_tags( (string) $value ), ENT_QUOTES, get_bloginfo( 'charset' ) );
            $value = preg_replace( '/\s+/u', ' ', trim( $value ) );
            return function_exists( 'mb_strtolower' ) ? mb_strtolower( $value, 'UTF-8' ) : strtolower( $value );
        };
        $needle = $normalize( $label );

        // CTA butonu header'da ayrı bir blok olarak render edildiği için aynı CTA'nın
        // WordPress menüsünde ikinci kez görünmesine izin verme. Bazı sitelerde menü
        // etiketi yanlışlıkla "Teklif Ala", "Teklif Al!" vb. kaydedilmiş olabilir.
        // Bu nedenle yalnızca birebir eşleşmeye güvenmiyoruz.
        $cta_key = preg_replace( '/[^a-z0-9çğıöşü]+/u', '', $needle );
        if ( preg_match( '/^teklifal(?:a|in|iniz)?$/u', $cta_key ) ) return true;

        foreach ( (array) $excluded as $candidate ) {
            $candidate_normalized = $normalize( $candidate );
            if ( $needle === $candidate_normalized ) return true;

            $candidate_key = preg_replace( '/[^a-z0-9çğıöşü]+/u', '', $candidate_normalized );
            if ( '' !== $candidate_key && 0 === strpos( $cta_key, $candidate_key ) && strlen( $cta_key ) <= strlen( $candidate_key ) + 4 ) return true;
        }
        return false;
    }

    private function render_elementor_location( $type, $settings ) {
        if ( ! did_action( 'elementor/loaded' ) || ! class_exists( '\\Elementor\\Plugin' ) ) return '';
        $desktop_id=absint($settings[$type.'_template']); $mobile_id=absint($settings['mobile_'.$type.'_template']); if(!$desktop_id&&!$mobile_id)return '';
        $extra = 'header' === $type ? ' data-wpst-transparent="' . ( ! empty($settings['header_transparent']) && ( empty($settings['header_transparent_home_only']) || is_front_page() ) ? '1' : '0' ) . '" data-wpst-transparent-overlay="' . ( ! empty($settings['header_transparent_overlay']) ? '1' : '0' ) . '" data-wpst-breakpoint="'.absint($settings['header_mobile_breakpoint']).'" data-wpst-scroll-threshold="'.absint($settings['header_scroll_threshold']).'" data-wpst-sticky="'.(!empty($settings['header_sticky'])?'1':'0').'" data-wpst-sticky-mode="'.esc_attr($settings['header_sticky_mode']).'" data-wpst-drawer-side="'.esc_attr($settings['header_mobile_drawer_side']).'" data-wpst-overlay="'.(!empty($settings['header_mobile_overlay'])?'1':'0').'" data-wpst-close-text="'.esc_attr($settings['header_mobile_close_text']).'" data-wpst-hide-scroll="'.(!empty($settings['header_hide_on_scroll'])?'1':'0').'" data-wpst-hide-delta="'.absint($settings['header_hide_scroll_delta']).'" data-wpst-search="'.(!empty($settings['header_search_enabled'])?'1':'0').'" data-wpst-account="'.(!empty($settings['header_account_enabled'])?'1':'0').'" data-wpst-account-url="'.esc_url(!empty($settings['header_account_url'])?$settings['header_account_url']:(is_user_logged_in()?admin_url('profile.php'):wp_login_url())).'" data-wpst-cart="'.(!empty($settings['header_cart_enabled'])?'1':'0').'" data-wpst-cart-url="'.esc_url(!empty($settings['header_cart_url'])?$settings['header_cart_url']:(function_exists('wc_get_cart_url')?wc_get_cart_url():'#')).'" data-wpst-mobile-bottom="'.(!empty($settings['header_mobile_bottom_nav'])?'1':'0').'" data-wpst-mobile-contact="'.(!empty($settings['header_mobile_contact_enabled'])?'1':'0').'" data-wpst-mobile-contact-title="'.esc_attr($settings['header_mobile_contact_title']).'" data-wpst-mobile-phone="'.esc_attr($settings['header_mobile_phone']).'" data-wpst-mobile-email="'.esc_attr($settings['header_mobile_email']).'" data-wpst-preset="'.esc_attr($settings['header_preset']).'" data-wpst-layout="'.esc_attr($settings['header_layout_style']).'" data-wpst-boxed-mobile="'.(!empty($settings['header_boxed_mobile'])?'1':'0').'" data-wpst-mobile-preset="'.esc_attr($settings['header_mobile_preset']).'" data-wpst-mobile-logo-position="'.esc_attr($settings['header_mobile_logo_position']).'" data-wpst-mobile-search="'.(!empty($settings['header_mobile_search'])?'1':'0').'" data-wpst-mobile-account="'.(!empty($settings['header_mobile_account'])?'1':'0').'" data-wpst-mobile-cart="'.(!empty($settings['header_mobile_cart'])?'1':'0').'" data-wpst-mobile-cta="'.(!empty($settings['header_mobile_cta_enabled'])?'1':'0').'" data-wpst-mobile-cta-text="'.esc_attr($settings['header_mobile_cta_text']).'" data-wpst-mobile-cta-url="'.esc_url($settings['header_mobile_cta_url']).'" data-wpst-glass="'.esc_attr($settings['header_glass_style']).'" data-wpst-scroll-solid="'.(!empty($settings['header_scroll_solid'])?'1':'0').'" data-wpst-state-contract="1" data-wpst-scroll-surface="'.(!empty($settings['header_scroll_solid'])?'solid':'transparent').'" data-wpst-scroll-blur="'.(!empty($settings['header_blur'])?'1':'0').'" data-wpst-scroll-shadow="'.(!empty($settings['header_scroll_shadow'])?'1':'0').'" data-wpst-shrink="'.(!empty($settings['header_shrink'])?'1':'0').'" data-wpst-shadow-style="'.esc_attr($settings['header_shadow_style']??'normal').'" data-wpst-menu-hover="'.esc_attr($settings['header_menu_hover']).'" data-wpst-menu-active="'.esc_attr($settings['header_menu_active']).'" data-wpst-menu-active-shadow="'.esc_attr($settings['header_menu_active_shadow']??'soft').'" style="--wpst-header-height:'.absint($settings['header_desktop_height']).'px;--wpst-header-scrolled-height:'.absint($settings['header_scrolled_height']).'px;--wpst-header-menu-gap:'.absint($settings['header_menu_gap']).'px;--wpst-header-boxed-width:'.absint($settings['header_boxed_width']).'px;--wpst-header-boxed-top:'.absint($settings['header_boxed_top']).'px;--wpst-header-boxed-side:'.absint($settings['header_boxed_side']).'px;--wpst-header-boxed-radius:'.absint($settings['header_boxed_radius']).'px;--wpst-header-boxed-bg:'.esc_attr($settings['header_boxed_background']).';--wpst-header-boxed-border:'.esc_attr($settings['header_boxed_border_color']).';--wpst-header-boxed-border-w:'.absint($settings['header_boxed_border_width']).'px;--wpst-header-boxed-shadow:'.esc_attr($settings['header_boxed_shadow']).';--wpst-header-scroll-bg:'.esc_attr($settings['header_scrolled_background']).';--wpst-header-scroll-color:'.esc_attr($settings['header_scrolled_text_color']).';--wpst-header-scroll-blur:'.absint($settings['header_blur_amount']).'px;--wpst-header-transparent-color:'.esc_attr($settings['header_transparent_text_color']).';--wpst-announcement-bg:'.esc_attr($settings['header_announcement_background']).';--wpst-announcement-color:'.esc_attr($settings['header_announcement_text_color']).';--wpst-mobile-drawer-width:'.absint($settings['header_mobile_drawer_width']).'px;--wpst-mobile-logo-width:'.absint($settings['header_mobile_logo_width']).'px;--wpst-mobile-logo-height:'.absint($settings['header_mobile_logo_height']).'px;--wpst-mobile-logo-scroll-width:'.absint($settings['header_mobile_logo_scroll_width']).'px;--wpst-mobile-logo-scroll-height:'.absint($settings['header_mobile_logo_scroll_height']).'px"' : ' data-wpst-footer-reveal="'.(!empty($settings['footer_reveal'])?'1':'0').'" data-wpst-mobile-columns="'.absint($settings['footer_mobile_columns']).'" data-wpst-mobile-align="'.esc_attr($settings['footer_mobile_align']).'" style="--wpst-footer-reveal-offset:'.absint($settings['footer_reveal_offset']).'px"';
        $location_classes='wpsoft-site-'.esc_attr($type);
        if('header'===$type){
            if(!$desktop_id && $mobile_id)$location_classes.=' wpst-elementor-mobile-only';
            elseif($desktop_id && !$mobile_id)$location_classes.=' wpst-elementor-desktop-only';
            else $location_classes.=' wpst-elementor-responsive';
        }
        $html='<div class="'.$location_classes.'" data-wpst-location="'.esc_attr($type).'" data-wpst-elementor-desktop="'.absint($desktop_id).'" data-wpst-elementor-mobile="'.absint($mobile_id).'"'.$extra.'>';
        if ( 'header' === $type ) $html .= $this->render_header_bars( $settings );
        if ( 'footer' === $type ) $html .= $this->render_footer_cta( $settings );
        if($desktop_id){$html.='<div class="'.($mobile_id?'wpst-desktop-template':'wpst-all-template').'">'.\Elementor\Plugin::instance()->frontend->get_builder_content_for_display($desktop_id,true).'</div>';}
        if($mobile_id){$html.='<div class="wpst-mobile-template">'.\Elementor\Plugin::instance()->frontend->get_builder_content_for_display($mobile_id,true).'</div>';}
        if ( 'footer' === $type ) $html .= $this->render_footer_bottom( $settings );
        return $html.'</div>';
    }

    public function header_shortcode() { return $this->render_header_hybrid( $this->get_settings() ); }
    public function footer_shortcode() { return $this->render_location( 'footer', $this->get_settings() ); }
}
