<?php
/**
 * WPSoft Builder Core
 *
 * Central registry and shared contracts for Builder, Elementor widgets and
 * Theme Builder locations. This keeps future Header Builder 2.0 / Footer
 * Builder 2.0 additions compatible with existing saved layouts.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

final class WPST_Builder_Core {
    const SCHEMA_VERSION = '2.2.0';

    public static function init() {
        add_action( 'init', array( __CLASS__, 'maybe_upgrade_schema' ), 2 );
        add_filter( 'wpst_builder_elements', array( __CLASS__, 'filter_elements' ), 5, 2 );
    }

    public static function maybe_upgrade_schema() {
        $stored = (string) get_option( 'wpst_builder_schema_version', '' );
        if ( self::SCHEMA_VERSION === $stored ) return;

        // Do not rewrite current layouts here. v3.1.x layouts remain valid and
        // Header Builder 2.0 can migrate them non-destructively when enabled.
        update_option( 'wpst_builder_schema_version', self::SCHEMA_VERSION, false );
    }

    public static function breakpoints() {
        return apply_filters( 'wpst_builder_breakpoints', array(
            'desktop' => array( 'label' => 'Masaüstü', 'max' => null ),
            'tablet'  => array( 'label' => 'Tablet',    'max' => 1024 ),
            'mobile'  => array( 'label' => 'Mobil',     'max' => 767 ),
        ) );
    }

    public static function locations() {
        return apply_filters( 'wpst_builder_locations', array(
            'header'       => 'Header',
            'footer'       => 'Footer',
            'single'       => 'Single',
            'archive'      => 'Archive',
            'mega_menu'    => 'Mega Menü',
            '404'          => '404',
            'search'       => 'Arama',
            'woocommerce'  => 'WooCommerce',
        ) );
    }

    public static function element_registry( $type = 'header' ) {
        $elements = array(
            'logo'    => array( 'label' => 'Logo',          'group' => 'brand',      'icon' => 'format-image' ),
            'menu'    => array( 'label' => 'Menü',          'group' => 'navigation', 'icon' => 'menu' ),
            'button'  => array( 'label' => 'Buton',         'group' => 'action',     'icon' => 'button' ),
            'search'  => array( 'label' => 'Arama',         'group' => 'action',     'icon' => 'search' ),
            'account' => array( 'label' => 'Hesap',         'group' => 'action',     'icon' => 'admin-users' ),
            'cart'    => array( 'label' => 'Sepet',         'group' => 'action',     'icon' => 'cart' ),
            'text'    => array( 'label' => 'Metin',         'group' => 'content',    'icon' => 'editor-textcolor' ),
            'html'    => array( 'label' => 'HTML',          'group' => 'content',    'icon' => 'editor-code' ),
            'social'  => array( 'label' => 'Sosyal Medya',  'group' => 'social',     'icon' => 'share' ),
            'spacer'  => array( 'label' => 'Esnek Boşluk',  'group' => 'layout',     'icon' => 'leftright' ),
        );

        if ( 'footer' === $type ) {
            $elements['copyright'] = array( 'label' => 'Telif Yazısı', 'group' => 'content', 'icon' => 'editor-quote' );
        }

        /**
         * Filter element registry before it reaches the visual builder.
         * Extensions can add a new builder element without editing core files.
         */
        return apply_filters( 'wpst_builder_element_registry', $elements, $type );
    }

    public static function filter_elements( $elements, $type = 'header' ) {
        return is_array( $elements ) && $elements ? $elements : self::element_registry( $type );
    }

    public static function element_labels( $type = 'header' ) {
        $labels = array();
        foreach ( self::element_registry( $type ) as $key => $config ) {
            $labels[ $key ] = isset( $config['label'] ) ? $config['label'] : $key;
        }
        return $labels;
    }

    public static function widget_categories() {
        return apply_filters( 'wpst_builder_widget_categories', array(
            'wpsoft-creative' => array(
                'wpsoft-hero-slider','wpsoft-video-hero','wpsoft-hero-split-modern','wpsoft-hero-bento','wpsoft-hero-saas','wpsoft-hero-spotlight',
                'wpsoft-hero-industry','wpsoft-hero-hospitality','wpsoft-hero-medical','wpsoft-hero-commerce','wpsoft-gradient-heading','wpsoft-animated-heading',
                'wpsoft-marquee-text','wpsoft-reveal-cards','wpsoft-hover-reveal','wpsoft-mouse-follow-card','wpsoft-icon-orbit','wpsoft-scroll-reveal-text',
                'wpsoft-morphing-cta','wpsoft-glass-card','wpsoft-image-reveal'
            ),
            'wpsoft-content' => array(
                'wpsoft-heading','wpsoft-icon-box','wpsoft-faq','wpsoft-quote','wpsoft-feature-list','wpsoft-icon-list',
                'wpsoft-icon-grid','wpsoft-icon-steps','wpsoft-number-cards','wpsoft-info-strip','wpsoft-tabs-modern','wpsoft-advanced-accordion','wpsoft-timeline-modern',
                'wpsoft-process-steps-pro','wpsoft-feature-mosaic','wpsoft-advanced-button','wpsoft-content-slider','wpsoft-advanced-table','wpsoft-story-cards','wpsoft-link-grid','wpsoft-expert-profile','wpsoft-image-box-pro'
            ),
            'wpsoft-marketing' => array(
                'wpsoft-cta','wpsoft-pricing','wpsoft-price-list','wpsoft-animated-counter','wpsoft-countdown-modern','wpsoft-testimonial-slider','wpsoft-reviews-pro','wpsoft-promo-banner','wpsoft-form-shell',
                'wpsoft-trust-badges','wpsoft-logo-cloud','wpsoft-logo-marquee','wpsoft-logo-grid-pro','wpsoft-wpforms','wpsoft-contact-cards','wpsoft-modal'
            ),
            'wpsoft-business' => array(
                'wpsoft-stats-grid','wpsoft-badge-grid','wpsoft-team-carousel-pro','wpsoft-service-cards-pro','wpsoft-service-carousel-pro','wpsoft-product-showcase','wpsoft-location-map',
                'wpsoft-booking-strip','wpsoft-progress-pro','wpsoft-footer-brand','wpsoft-footer-links','wpsoft-footer-newsletter','wpsoft-footer-social'
            ),
            'wpsoft-media' => array(
                'wpsoft-image-text','wpsoft-gallery-zoom-pro','wpsoft-image-carousel','wpsoft-before-after','wpsoft-card-carousel','wpsoft-parallax-image',
                'wpsoft-image-cascade','wpsoft-image-hotspots','wpsoft-video-popup-pro','wpsoft-fancy-box','wpsoft-flip-box','wpsoft-image-reveal'
            ),
            'wpsoft-navigation' => array(
                'wpsoft-site-logo','wpsoft-navigation','wpsoft-breadcrumb','wpsoft-mega-links','wpsoft-mega-promo','wpsoft-mega-banner','wpsoft-mega-quicknav','wpsoft-scroll-progress','wpsoft-link-grid','wpsoft-content-finder'
            ),
            'wpsoft-portfolio' => array(
                'wpsoft-portfolio','wpsoft-portfolio-title','wpsoft-portfolio-excerpt','wpsoft-portfolio-image','wpsoft-portfolio-terms'
            ),
            'wpsoft-dynamic' => array(
                'wpsoft-blog-posts','wpsoft-post-title','wpsoft-post-content','wpsoft-post-image','wpsoft-post-meta','wpsoft-post-excerpt','wpsoft-post-author',
                'wpsoft-post-terms','wpsoft-post-navigation','wpsoft-post-share','wpsoft-post-comments'
            ),
        ) );
    }

    public static function widget_category_for( $name ) {
        foreach ( self::widget_categories() as $category => $names ) {
            if ( in_array( $name, $names, true ) ) return $category;
        }
        return 'wpsoft';
    }

    public static function widget_framework_contract() {
        return apply_filters( 'wpst_widget_framework_contract', array(
            'version' => '2.1.0',
            'tabs' => array( 'content' => 'İçerik', 'style' => 'Stil', 'advanced' => 'Gelişmiş' ),
            'responsive' => array( 'desktop', 'tablet', 'mobile' ),
            'responsive_controls' => array( 'typography', 'spacing', 'width', 'alignment', 'media', 'visibility', 'motion' ),
            'design_sources' => array( 'global', 'local' ),
            'tokens' => array( 'colors', 'typography', 'spacing', 'radius', 'shadow', 'surface', 'forms', 'buttons' ),
        ) );
    }

    public static function capabilities() {
        return apply_filters( 'wpst_builder_capabilities', array(
            'responsive_controls' => true,
            'global_design'       => true,
            'display_conditions'  => true,
            'theme_builder'       => false,
            'template_manager'    => true,
            'template_library'    => true,
            'mega_menu'           => true,
            'header_rows_v2'      => true,
            'widget_framework_v2'  => true,
            'signature_presets'   => true,
            'signature_presets_stage2' => true,
            'controls_ux_v3'      => true,
            'responsive_controls_v2' => true,
        ) );
    }
}
