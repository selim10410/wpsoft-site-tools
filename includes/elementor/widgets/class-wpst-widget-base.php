<?php
if ( ! defined( 'ABSPATH' ) ) exit;
abstract class WPST_Elementor_Widget_Base extends \Elementor\Widget_Base {
    public function get_categories() {
        if ( class_exists( 'WPST_Builder_Core' ) ) {
            return array( WPST_Builder_Core::widget_category_for( $this->get_name() ) );
        }
        return array( 'wpsoft' );
    }
    public function get_style_depends() { return array( 'wpst-elementor' ); }
    protected function link_controls( $prefix = 'button', $label = 'Buton' ) {
        $this->add_control( $prefix . '_text', array( 'label' => $label . ' Metni', 'type' => \Elementor\Controls_Manager::TEXT, 'default' => 'Detaylı İncele' ) );
        $this->add_control( $prefix . '_url', array( 'label' => $label . ' Bağlantısı', 'type' => \Elementor\Controls_Manager::URL, 'placeholder' => 'https://', 'default' => array( 'url' => '#' ) ) );
    }
    /**
     * Shared Hero controls.
     * Keeps all WPSoft Hero widgets consistent without changing their content/save flow.
     */
    protected function hero_button_style_controls() {
        $this->start_controls_section( 'wpst_hero_controls', array(
            'label' => 'Hero · İçerik & Butonlar',
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ) );

        $hero_copy = '{{WRAPPER}} .wpst-hsm-copy, {{WRAPPER}} .wpst-hb-main, {{WRAPPER}} .wpst-hs-copy, {{WRAPPER}} .wpst-hsp-inner, {{WRAPPER}} .wpst-hi-copy, {{WRAPPER}} .wpst-hh-copy, {{WRAPPER}} .wpst-hm-copy, {{WRAPPER}} .wpst-hc-copy, {{WRAPPER}} .wpst-ew-hero-slide-inner';
        $hero_actions = '{{WRAPPER}} .wpst-hsm-actions, {{WRAPPER}} .wpst-hs-actions';
        $hero_direct_buttons = '{{WRAPPER}} .wpst-hb-main > a, {{WRAPPER}} .wpst-hi-copy > a, {{WRAPPER}} .wpst-hh-copy > a, {{WRAPPER}} .wpst-hm-copy > a, {{WRAPPER}} .wpst-hc-copy > a, {{WRAPPER}} .wpst-hsp-inner > a';

        $this->add_responsive_control( 'wpst_hero_content_align', array(
            'label' => 'İçerik Hizası',
            'type'  => \Elementor\Controls_Manager::CHOOSE,
            'options' => array(
                'start'  => array( 'title'=>'Sol', 'icon'=>'eicon-text-align-left' ),
                'center' => array( 'title'=>'Tam Orta', 'icon'=>'eicon-text-align-center' ),
                'end'    => array( 'title'=>'Sağ', 'icon'=>'eicon-text-align-right' ),
            ),
            'default' => '',
            'prefix_class' => 'wpst-hero-content-align-',
            'selectors' => array(
                '{{WRAPPER}}' => '--wpst-hero-content-align:{{VALUE}};',
            ),
            'description' => 'Metin, butonlar ve içerik bloğu birlikte hizalanır.',
        ) );

        $this->add_responsive_control( 'wpst_hero_vertical_align', array(
            'label' => 'Dikey İçerik Konumu',
            'type'  => \Elementor\Controls_Manager::CHOOSE,
            'options' => array(
                'start'  => array( 'title'=>'Üst', 'icon'=>'eicon-v-align-top' ),
                'center' => array( 'title'=>'Orta', 'icon'=>'eicon-v-align-middle' ),
                'end'    => array( 'title'=>'Alt', 'icon'=>'eicon-v-align-bottom' ),
            ),
            'default' => 'center',
            'tablet_default' => 'center',
            'mobile_default' => 'center',
            'selectors' => array(
                '{{WRAPPER}}' => '--wpst-hero-vertical-align:{{VALUE}};',
            ),
        ) );

        $this->add_control( 'wpst_hero_button_style', array(
            'label' => 'Buton Stili',
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'default',
            'options' => array(
                'default' => 'Widget Varsayılanı',
                'solid' => 'Solid',
                'outline' => 'Outline',
                'soft' => 'Soft',
                'glass' => 'Glass',
                'dark' => 'Dark',
                'minimal' => 'Minimal',
            ),
            'prefix_class' => 'wpst-hero-btn-style-',
        ) );

        $this->add_control( 'wpst_hero_button_radius', array(
            'label' => 'Buton Köşesi',
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'default',
            'options' => array(
                'default' => 'Widget Varsayılanı',
                'square' => 'Köşeli',
                'soft' => 'Soft',
                'rounded' => 'Yuvarlak',
                'pill' => 'Pill',
            ),
            'prefix_class' => 'wpst-hero-btn-radius-',
        ) );

        $this->add_control( 'wpst_hero_button_shadow', array(
            'label' => 'Buton Gölgesi',
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'none',
            'options' => array(
                'none' => 'Yok · Önerilen',
                'soft' => 'Soft',
                'medium' => 'Medium',
            ),
            'prefix_class' => 'wpst-hero-btn-shadow-',
        ) );

        $this->add_responsive_control( 'wpst_hero_button_height', array(
            'label' => 'Buton Yüksekliği',
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => array('px'),
            'range' => array('px'=>array('min'=>38,'max'=>72)),
            'selectors' => array(
                '{{WRAPPER}}' => '--wpst-hero-btn-height:{{SIZE}}px;',
            ),
        ) );

        $this->add_responsive_control( 'wpst_hero_button_gap', array(
            'label' => 'Butonlar Arası Boşluk',
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => array('px'),
            'range' => array('px'=>array('min'=>0,'max'=>40)),
            'selectors' => array(
                '{{WRAPPER}}' => '--wpst-hero-btn-gap:{{SIZE}}px;',
            ),
        ) );

        $this->add_control( 'wpst_hero_button_bg', array(
            'label' => 'Buton Arka Plan',
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => array('{{WRAPPER}}'=>'--wpst-hero-btn-bg:{{VALUE}};'),
            'condition' => array('wpst_hero_button_style!'=>'default'),
        ) );
        $this->add_control( 'wpst_hero_button_text', array(
            'label' => 'Buton Yazı Rengi',
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => array('{{WRAPPER}}'=>'--wpst-hero-btn-color:{{VALUE}};'),
            'condition' => array('wpst_hero_button_style!'=>'default'),
        ) );
        $this->add_control( 'wpst_hero_button_hover_bg', array(
            'label' => 'Hover Arka Plan',
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => array('{{WRAPPER}}'=>'--wpst-hero-btn-hover-bg:{{VALUE}};'),
            'condition' => array('wpst_hero_button_style!'=>'default'),
        ) );
        $this->add_control( 'wpst_hero_button_hover_text', array(
            'label' => 'Hover Yazı Rengi',
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => array('{{WRAPPER}}'=>'--wpst-hero-btn-hover-color:{{VALUE}};'),
            'condition' => array('wpst_hero_button_style!'=>'default'),
        ) );

        $this->end_controls_section();
    }

    protected function typography_section( $selector, $title = 'Tipografi' ) {
        $this->start_controls_section( 'style_typography_' . md5($selector), array( 'label' => $title, 'tab' => \Elementor\Controls_Manager::TAB_STYLE ) );
        $this->add_group_control( \Elementor\Group_Control_Typography::get_type(), array( 'name' => 'typo_' . md5($selector), 'selector' => '{{WRAPPER}} ' . $selector ) );
        $this->end_controls_section();
    }
    /**
     * Standard WPSoft "Biçim" controls helper.
     * New widgets should use this so all controls follow the same UI language.
     */
    protected function standard_style_controls( $selector, $options = array() ) {
        $id = 'wpst_standard_' . substr( md5( $selector ), 0, 8 );
        $this->start_controls_section( $id, array(
            'label' => 'Biçim',
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ) );

        $this->add_control( $id . '_background', array(
            'label' => 'Arka Plan',
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => array( '{{WRAPPER}} ' . $selector => 'background:{{VALUE}}' ),
        ) );

        $this->add_control( $id . '_text', array(
            'label' => 'Metin Rengi',
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => array( '{{WRAPPER}} ' . $selector => 'color:{{VALUE}}' ),
        ) );

        $this->add_control( $id . '_radius', array(
            'label' => 'Köşe Yuvarlaklığı',
            'type' => \Elementor\Controls_Manager::SLIDER,
            'range' => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
            'selectors' => array( '{{WRAPPER}} ' . $selector => 'border-radius:{{SIZE}}{{UNIT}}' ),
        ) );

        $this->add_responsive_control( $id . '_padding', array(
            'label' => 'İç Boşluk',
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => array( 'px', '%' ),
            'selectors' => array( '{{WRAPPER}} ' . $selector => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}' ),
        ) );

        $this->end_controls_section();
    }

    /**
     * Shared controls appended to every WPSoft Elementor widget.
     * Keeps the editor structure consistent: İçerik → WPSoft Biçim → WPSoft Gelişmiş.
     */

    public function show_in_panel() {
        return ! class_exists('WPST_License') || WPST_License::is_active();
    }

    public function wpst_widget_tier() {
        $name = method_exists($this,'get_name') ? $this->get_name() : '';
        if(preg_match('/(?:-pro|-modern|-zoom|-reveal|-carousel|-spotlight|-mosaic|-orbit|-cascade|-morphing|-bento|-split|-saas|-medical|-hospitality|-industry|-commerce)$/',$name)) return 'signature';
        return 'modern';
    }

    protected function wpst_global_design_controls() {
        $this->start_controls_section( 'wpst_global_design', array(
            'label' => '01 · Global Tasarım',
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ) );
        $this->add_control( 'wpst_use_global_design', array(
            'label' => 'Global Tasarımı Kullan',
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => 'yes',
            'prefix_class' => 'wpst-global-design-',
        ) );
        $this->add_control( 'wpst_global_design_note', array(
            'type' => \Elementor\Controls_Manager::RAW_HTML,
            'raw' => '<div class="wpst-elementor-global-note"><strong>Global Design System</strong><br>Buradaki tokenlar yalnızca “Global Tasarımı Kullan” açıkken uygulanır. 02 · Lokal Stil & Tipografi alanındaki dolu değerler her zaman Global Design > BUTTONS değerlerinin üzerine yazılır.</div>',
            'content_classes' => 'wpst-control-note',
            'condition' => array( 'wpst_use_global_design' => 'yes' ),
        ) );

        $this->add_control( 'wpst_global_surface_token', array(
            'label' => 'Widget Yüzeyi',
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'inherit',
            'options' => array(
                'inherit' => 'Widget Varsayılanı',
                'surface' => 'Ana Surface',
                'surface-alt' => 'Alternatif Surface',
                'soft' => 'Soft Surface',
                'dark' => 'Koyu Surface',
                'primary-soft' => 'Primary Soft',
            ),
            'prefix_class' => 'wpst-surface-token-',
            'condition' => array( 'wpst_use_global_design' => 'yes' ),
            'description' => 'Global Design > Renk Sistemi yüzeylerinden birini widget ana yüzeyine uygular.',
        ) );
        $this->add_control( 'wpst_radius_token', array(
            'label' => 'Radius Tokenı',
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'inherit',
            'options' => array(
                'inherit' => 'Widget Varsayılanı',
                'sm' => 'Small',
                'md' => 'Medium',
                'lg' => 'Large',
                'xl' => 'X Large',
                'card' => 'Card',
            ),
            'prefix_class' => 'wpst-radius-token-',
            'condition' => array( 'wpst_use_global_design' => 'yes' ),
        ) );
        $this->add_control( 'wpst_shadow_token', array(
            'label' => 'Gölge Tokenı',
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'inherit',
            'options' => array(
                'inherit' => 'Widget Varsayılanı',
                'none' => 'Yok',
                'sm' => 'Soft',
                'md' => 'Medium',
                'lg' => 'Strong',
                'global' => 'Global',
            ),
            'prefix_class' => 'wpst-shadow-token-',
            'condition' => array( 'wpst_use_global_design' => 'yes' ),
        ) );

        $this->add_control( 'wpst_spacing_token', array(
            'label' => 'İç Boşluk Ritmi',
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'inherit',
            'options' => array(
                'inherit' => 'Widget Varsayılanı',
                'compact' => 'Compact',
                'balanced' => 'Balanced',
                'spacious' => 'Spacious',
            ),
            'prefix_class' => 'wpst-spacing-token-',
            'condition' => array( 'wpst_use_global_design' => 'yes' ),
            'description' => 'Widget ana yüzeyinin padding ve içerik ritmini Global Design spacing ölçeğine bağlar.',
        ) );

        if ( $this->wpst_has_control_capability( 'button' ) ) {
            $this->add_control( 'wpst_global_button_token', array(
                'label' => 'Buton Tokenı',
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'inherit',
                'options' => array(
                    'inherit' => 'Widget Varsayılanı',
                    'primary' => 'Primary',
                    'secondary' => 'Secondary',
                    'dark' => 'Dark',
                    'outline' => 'Outline',
                ),
                'prefix_class' => 'wpst-button-token-',
                'condition' => array( 'wpst_use_global_design' => 'yes' ),
                'description' => 'Widgetın ana aksiyon butonlarını Global Design buton sistemiyle eşler.',
            ) );
        }

        $this->add_control( 'wpst_signature_ui', array(
            'label' => 'Signature UI',
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'balanced',
            'options' => array(
                'off' => 'Kapalı / Widget Varsayılanı',
                'compact' => 'Compact',
                'balanced' => 'Balanced · Önerilen',
                'spacious' => 'Spacious',
                'editorial' => 'Editorial',
            ),
            'prefix_class' => 'wpst-signature-ui-',
            'description' => 'Kart aralığı, tipografi ritmi, buton ölçüsü ve dokunmatik hedefleri aynı tasarım diline bağlar.',
            'condition' => array( 'wpst_use_global_design' => 'yes' ),
        ) );

        $this->add_control( 'wpst_mobile_touch_mode', array(
            'label' => 'Mobil Dokunmatik Optimizasyon',
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => 'yes',
            'prefix_class' => 'wpst-touch-ui-',
            'condition' => array( 'wpst_use_global_design' => 'yes' ),
        ) );

        $this->add_control( 'wpst_mobile_cta_full', array(
            'label' => 'Mobilde Ana Aksiyon Tam Genişlik',
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => '',
            'prefix_class' => 'wpst-mobile-cta-full-',
            'condition' => array( 'wpst_mobile_touch_mode' => 'yes' ),
        ) );
        $this->end_controls_section();
    }

    /**
     * Shared selectors are intentionally scoped to visible content/actions.
     * They must never style carousel navigation, tabs, disclosure toggles,
     * lightbox controls or other interaction chrome.
     */
    protected function wpst_action_selector() {
        return implode( ', ', array(
            '{{WRAPPER}} a.wpst-ew-button',
            '{{WRAPPER}} a.wpst-q-button',
            '{{WRAPPER}} .wpst-button',
            '{{WRAPPER}} .wpst-adv-button',
            '{{WRAPPER}} [class*="cta"] a',
            '{{WRAPPER}} a[class*="button"]',
            '{{WRAPPER}} button[class*="button"]',
            '{{WRAPPER}} .wpst-ew-hero-slide a',
            '{{WRAPPER}} .wpst-content-slide-copy a',
            '{{WRAPPER}} .wpst-video-bg-copy > a',
            '{{WRAPPER}} .wpst-ew-video-content > a',
            '{{WRAPPER}} .wpst-media-card-copy > a',
            '{{WRAPPER}} .wpst-booking-strip a',
            '{{WRAPPER}} .wpst-hsm-actions a',
            '{{WRAPPER}} .wpst-hb-main > a',
            '{{WRAPPER}} .wpst-hs-actions a',
            '{{WRAPPER}} .wpst-hsp-inner > a',
            '{{WRAPPER}} .wpst-hi-copy > a',
            '{{WRAPPER}} .wpst-hh-copy > a',
            '{{WRAPPER}} .wpst-hm-copy > a',
            '{{WRAPPER}} .wpst-hc-copy > a',
            '{{WRAPPER}} .wpst-ew-footer-newsletter button[type="submit"]',
            '{{WRAPPER}} .wpforms-submit'
        ) );
    }

    protected function wpst_action_hover_selector() {
        return str_replace(
            array( ', ', '{{WRAPPER}} ' ),
            array( ':hover, ', '{{WRAPPER}} ' ),
            $this->wpst_action_selector()
        ) . ':hover';
    }

    protected function wpst_media_selector() {
        return implode( ', ', array(
            '{{WRAPPER}} .wpst-ew-image-text img',
            '{{WRAPPER}} .wpst-ew-hero-slide img',
            '{{WRAPPER}} .wpst-content-slide-media img',
            '{{WRAPPER}} .wpst-ew-card-media img',
            '{{WRAPPER}} .wpst-video-poster img',
            '{{WRAPPER}} .wpst-gallery-zoom-item img',
            '{{WRAPPER}} .wpst-portfolio-item img',
            '{{WRAPPER}} .wpst-product-showcase img',
            '{{WRAPPER}} .wpst-image-cascade img',
            '{{WRAPPER}} .wpst-parallax-image img'
        ) );
    }


    /**
     * Deep Controls 4.0
     * Widgets declare only the control groups that are meaningful for their UI.
     * This keeps the shared design system without flooding Elementor's panel.
     */
    protected function wpst_control_capabilities() {
        $name = method_exists( $this, 'get_name' ) ? (string) $this->get_name() : '';
        $caps = array(
            'surface'    => true,
            'typography' => true,
            'button'     => true,
            'media'      => true,
            'layout'     => true,
            'motion'     => true,
        );

        if ( preg_match( '/(?:gallery|image|video|logo|media|portfolio|product|team|expert|banner|hero)/', $name ) ) {
            $caps['media'] = true;
        }
        if ( preg_match( '/(?:scroll-progress|svg-shape|marquee-text|logo-marquee|logo-cloud|logo-grid|map|table|divider|spacer)/', $name ) ) {
            $caps['button'] = false;
        }
        if ( preg_match( '/(?:scroll-progress|svg-shape|image-cascade|image-reveal|parallax-image|logo-marquee|logo-cloud|logo-grid|gallery|map)/', $name ) ) {
            $caps['typography'] = false;
        }
        if ( preg_match( '/(?:quote|accordion|tabs|table|progress|counter|countdown|breadcrumb|info-strip|badge-grid|icon-grid|story|reviews|testimonial|pricing|service|blog|post|finder|form|footer|mega|cta|button)/', $name ) ) {
            $caps['media'] = false;
        }
        if ( preg_match( '/(?:scroll-progress|svg-shape)/', $name ) ) {
            $caps['surface'] = false;
            $caps['layout'] = false;
        }
        return $caps;
    }

    protected function wpst_has_control_capability( $capability ) {
        $caps = $this->wpst_control_capabilities();
        return ! empty( $caps[ $capability ] );
    }

    protected function wpst_signature_preset_control( $control_id = 'wpst_signature_preset' ) {
        $this->add_control( $control_id, array(
            'label' => 'Hızlı Tasarım Preseti',
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'custom',
            'options' => array(
                'custom'    => 'Özel / Mevcut Ayarlar',
                'global'    => 'Global Tasarımı Takip Et',
                'signature' => 'WPSoft Signature',
                'corporate' => 'Corporate Clean',
                'editorial' => 'Editorial',
                'soft'      => 'Soft Modern',
                'dark'      => 'Dark Premium',
                'minimal'   => 'Minimal',
            ),
            'prefix_class' => 'wpst-preset-',
            'description' => 'Global Tasarımı Takip Et seçilirse Global Tasarım > Widget Hızlı Tasarım ayarı kullanılır. Diğer presetler spacing, radius, yüzey, gölge ve bileşen ritmini otomatik düzenler; özel layout ayarlarını ezmez.',
        ) );
    }

    protected function standard_responsive_controls() {
        $this->wpst_global_design_controls();

        $this->start_controls_section( 'wpst_standard_style', array(
            'label' => '02 · Lokal Stil & Tipografi',
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ) );

        $this->add_control( 'wpst_use_global_note', array(
            'type' => \Elementor\Controls_Manager::RAW_HTML,
            'raw'  => '<div class="wpst-elementor-global-note"><strong>Lokal Override Alanı</strong><br>Bu bölümde boş bıraktığınız değerler widget varsayılanı / Global Design değerini korur. Dolu değerler yalnızca bu widgetı değiştirir.</div>',
            'content_classes' => 'wpst-control-note',
        ) );

        $this->add_control( 'wpst_design_mode', array(
            'label' => 'Tasarım Kaynağı',
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'global',
            'options' => array(
                'global' => 'Global Tasarım + Lokal Override',
                'local' => 'Lokal Biçim Öncelikli',
            ),
            'prefix_class' => 'wpst-design-source-',
            'description' => 'Global seçiliyken boş bırakılan Biçim değerleri Design System tokenlarından gelir. Lokal değerler her zaman üzerine yazabilir.',
        ) );

        if ( $this->wpst_has_control_capability( 'surface' ) ) {
        $this->add_control( 'wpst_box_background', array(
            'label' => 'Arka Plan',
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => array(
                '{{WRAPPER}} > .elementor-widget-container' => 'background:{{VALUE}};',
                '{{WRAPPER}}'=>'--wpst-local-surface:{{VALUE}};'),
        ) );


        $this->add_control( 'wpst_surface_token', array(
            'label' => 'Lokal Yüzey Override',
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => '',
            'options' => array(
                '' => 'Widget Varsayılanı',
                'surface' => 'Surface',
                'surface-alt' => 'Alternatif Surface',
                'surface-dark' => 'Koyu Surface',
                'primary-soft' => 'Primary Soft',
            ),
            'prefix_class' => 'wpst-surface-',
            'description' => 'Bu alan widget özelinde yüzey tokenını değiştirir. Üstteki Global Tasarım yüzeyinden sonra uygulanır; Lokal Arka Plan rengi girerseniz en son o değer geçerli olur.',
        ) );

        $this->add_control( 'wpst_box_border_color', array(
            'label' => 'Kenarlık Rengi',
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => array(
                '{{WRAPPER}} > .elementor-widget-container' => 'border-color:{{VALUE}};',
                '{{WRAPPER}}'=>'--wpst-local-border:{{VALUE}};'),
        ) );

        $this->add_group_control( \Elementor\Group_Control_Border::get_type(), array(
            'name' => 'wpst_box_border',
            'selector' => '{{WRAPPER}} > .elementor-widget-container',
        ) );

        $this->add_group_control( \Elementor\Group_Control_Box_Shadow::get_type(), array(
            'name' => 'wpst_box_shadow',
            'selector' => '{{WRAPPER}} > .elementor-widget-container',
        ) );

        $this->add_control( 'wpst_general_text_color', array(
            'label' => 'Genel Metin Rengi',
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => array(
                '{{WRAPPER}} > .elementor-widget-container' => 'color:{{VALUE}};',
                '{{WRAPPER}}'=>'--wpst-local-text:{{VALUE}};'),
        ) );

        $this->add_control( 'wpst_heading_color', array(
            'label' => 'Başlık Rengi',
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => array(
                '{{WRAPPER}} h1,{{WRAPPER}} h2,{{WRAPPER}} h3,{{WRAPPER}} h4,{{WRAPPER}} h5,{{WRAPPER}} h6' => 'color:{{VALUE}};',
                '{{WRAPPER}}'=>'--wpst-local-heading:{{VALUE}};'),
        ) );

        $this->add_control( 'wpst_link_color', array(
            'label' => 'Bağlantı Rengi',
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => array(
                '{{WRAPPER}} a' => 'color:{{VALUE}};',
                '{{WRAPPER}}'=>'--wpst-local-link:{{VALUE}};'),
        ) );

        }

        if ( $this->wpst_has_control_capability( 'typography' ) ) {
        $this->add_control( 'wpst_typography_heading', array(
            'label' => 'Başlık Tipografisi',
            'type' => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ) );
        $this->add_group_control( \Elementor\Group_Control_Typography::get_type(), array(
            'name' => 'wpst_heading_typography',
            'selector' => '{{WRAPPER}} h1, {{WRAPPER}} h2, {{WRAPPER}} h3, {{WRAPPER}} h4, {{WRAPPER}} h5, {{WRAPPER}} h6',
        ) );
        $this->add_responsive_control( 'wpst_heading_font_size', array(
            'label' => 'Başlık Boyutu',
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => array('px','em','rem','vw'),
            'range' => array(
                'px'=>array('min'=>8,'max'=>160),
                'em'=>array('min'=>.5,'max'=>10,'step'=>.1),
                'rem'=>array('min'=>.5,'max'=>10,'step'=>.1),
                'vw'=>array('min'=>1,'max'=>15,'step'=>.1),
            ),
            'selectors' => array('{{WRAPPER}} h1, {{WRAPPER}} h2, {{WRAPPER}} h3, {{WRAPPER}} h4, {{WRAPPER}} h5, {{WRAPPER}} h6'=>'font-size:{{SIZE}}{{UNIT}};'),
        ) );
        $this->add_responsive_control( 'wpst_heading_line_height', array(
            'label' => 'Başlık Satır Yüksekliği',
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units'=>array('em','px'),
            'range'=>array('em'=>array('min'=>.7,'max'=>2.5,'step'=>.05),'px'=>array('min'=>10,'max'=>180)),
            'selectors'=>array('{{WRAPPER}} h1, {{WRAPPER}} h2, {{WRAPPER}} h3, {{WRAPPER}} h4, {{WRAPPER}} h5, {{WRAPPER}} h6'=>'line-height:{{SIZE}}{{UNIT}};'),
        ) );
        $this->add_responsive_control( 'wpst_heading_letter_spacing', array(
            'label'=>'Başlık Harf Aralığı',
            'type'=>\Elementor\Controls_Manager::SLIDER,
            'size_units'=>array('px','em'),
            'range'=>array('px'=>array('min'=>-5,'max'=>20,'step'=>.1),'em'=>array('min'=>-.2,'max'=>1,'step'=>.01)),
            'selectors'=>array('{{WRAPPER}} h1, {{WRAPPER}} h2, {{WRAPPER}} h3, {{WRAPPER}} h4, {{WRAPPER}} h5, {{WRAPPER}} h6'=>'letter-spacing:{{SIZE}}{{UNIT}};'),
        ) );
        $this->add_responsive_control( 'wpst_heading_align', array(
            'label'=>'Başlık Hizalama',
            'type'=>\Elementor\Controls_Manager::CHOOSE,
            'options'=>array(
                'left'=>array('title'=>'Sol','icon'=>'eicon-text-align-left'),
                'center'=>array('title'=>'Orta','icon'=>'eicon-text-align-center'),
                'right'=>array('title'=>'Sağ','icon'=>'eicon-text-align-right'),
            ),
            'selectors'=>array('{{WRAPPER}} h1, {{WRAPPER}} h2, {{WRAPPER}} h3, {{WRAPPER}} h4, {{WRAPPER}} h5, {{WRAPPER}} h6'=>'text-align:{{VALUE}};'),
        ) );

        $this->add_control( 'wpst_typography_body', array(
            'label'=>'Metin Tipografisi','type'=>\Elementor\Controls_Manager::HEADING,'separator'=>'before',
        ) );
        $this->add_group_control( \Elementor\Group_Control_Typography::get_type(), array(
            'name'=>'wpst_body_typography',
            'selector'=>'{{WRAPPER}} p, {{WRAPPER}} blockquote, {{WRAPPER}} .wpst-ew-rich, {{WRAPPER}} .wpst-post-excerpt, {{WRAPPER}} .wpst-post-content',
        ) );
        $this->add_responsive_control( 'wpst_body_font_size', array(
            'label'=>'Metin Boyutu','type'=>\Elementor\Controls_Manager::SLIDER,'size_units'=>array('px','em','rem'),
            'range'=>array('px'=>array('min'=>8,'max'=>72),'em'=>array('min'=>.5,'max'=>5,'step'=>.1),'rem'=>array('min'=>.5,'max'=>5,'step'=>.1)),
            'selectors'=>array('{{WRAPPER}} p, {{WRAPPER}} blockquote, {{WRAPPER}} .wpst-ew-rich, {{WRAPPER}} .wpst-post-excerpt, {{WRAPPER}} .wpst-post-content'=>'font-size:{{SIZE}}{{UNIT}};'),
        ) );
        $this->add_responsive_control( 'wpst_body_line_height', array(
            'label'=>'Metin Satır Yüksekliği','type'=>\Elementor\Controls_Manager::SLIDER,'size_units'=>array('em','px'),
            'range'=>array('em'=>array('min'=>.8,'max'=>3,'step'=>.05),'px'=>array('min'=>10,'max'=>120)),
            'selectors'=>array('{{WRAPPER}} p, {{WRAPPER}} blockquote, {{WRAPPER}} .wpst-ew-rich, {{WRAPPER}} .wpst-post-excerpt, {{WRAPPER}} .wpst-post-content'=>'line-height:{{SIZE}}{{UNIT}};'),
        ) );
        $this->add_responsive_control( 'wpst_body_letter_spacing', array(
            'label'=>'Metin Harf Aralığı','type'=>\Elementor\Controls_Manager::SLIDER,'size_units'=>array('px','em'),
            'range'=>array('px'=>array('min'=>-4,'max'=>16,'step'=>.1),'em'=>array('min'=>-.15,'max'=>.8,'step'=>.01)),
            'selectors'=>array('{{WRAPPER}} p, {{WRAPPER}} blockquote, {{WRAPPER}} .wpst-ew-rich, {{WRAPPER}} .wpst-post-excerpt, {{WRAPPER}} .wpst-post-content'=>'letter-spacing:{{SIZE}}{{UNIT}};'),
        ) );
        $this->add_control( 'wpst_body_color', array(
            'label'=>'Metin Rengi','type'=>\Elementor\Controls_Manager::COLOR,
            'selectors'=>array('{{WRAPPER}} p, {{WRAPPER}} blockquote, {{WRAPPER}} .wpst-ew-rich, {{WRAPPER}} .wpst-post-excerpt, {{WRAPPER}} .wpst-post-content'=>'color:{{VALUE}};',
                '{{WRAPPER}}'=>'--wpst-local-body:{{VALUE}};'),
        ) );
        $this->add_responsive_control( 'wpst_body_align', array(
            'label'=>'Metin Hizalama','type'=>\Elementor\Controls_Manager::CHOOSE,
            'options'=>array(
                'left'=>array('title'=>'Sol','icon'=>'eicon-text-align-left'),
                'center'=>array('title'=>'Orta','icon'=>'eicon-text-align-center'),
                'right'=>array('title'=>'Sağ','icon'=>'eicon-text-align-right'),
                'justify'=>array('title'=>'İki Yana','icon'=>'eicon-text-align-justify'),
            ),
            'selectors'=>array('{{WRAPPER}} p, {{WRAPPER}} blockquote, {{WRAPPER}} .wpst-ew-rich, {{WRAPPER}} .wpst-post-excerpt, {{WRAPPER}} .wpst-post-content'=>'text-align:{{VALUE}};'),
        ) );

        $this->add_control( 'wpst_typography_small', array(
            'label'=>'Etiket / Küçük Metin','type'=>\Elementor\Controls_Manager::HEADING,'separator'=>'before',
        ) );
        $this->add_group_control( \Elementor\Group_Control_Typography::get_type(), array(
            'name'=>'wpst_small_typography',
            'selector'=>'{{WRAPPER}} small, {{WRAPPER}} .wpst-ew-eyebrow, {{WRAPPER}} [class*="badge"], {{WRAPPER}} [class*="eyebrow"]',
        ) );
        $this->add_responsive_control( 'wpst_small_font_size', array(
            'label'=>'Etiket Boyutu','type'=>\Elementor\Controls_Manager::SLIDER,'size_units'=>array('px','em','rem'),
            'range'=>array('px'=>array('min'=>6,'max'=>40),'em'=>array('min'=>.4,'max'=>3,'step'=>.1),'rem'=>array('min'=>.4,'max'=>3,'step'=>.1)),
            'selectors'=>array('{{WRAPPER}} small, {{WRAPPER}} .wpst-ew-eyebrow, {{WRAPPER}} [class*="badge"], {{WRAPPER}} [class*="eyebrow"]'=>'font-size:{{SIZE}}{{UNIT}};'),
        ) );

        $this->add_responsive_control( 'wpst_small_line_height', array(
            'label'=>'Etiket Satır Yüksekliği','type'=>\Elementor\Controls_Manager::SLIDER,'size_units'=>array('em','px'),
            'range'=>array('em'=>array('min'=>.7,'max'=>3,'step'=>.05),'px'=>array('min'=>8,'max'=>80)),
            'selectors'=>array('{{WRAPPER}} small, {{WRAPPER}} .wpst-ew-eyebrow, {{WRAPPER}} [class*="badge"], {{WRAPPER}} [class*="eyebrow"]'=>'line-height:{{SIZE}}{{UNIT}};'),
        ) );
        $this->add_responsive_control( 'wpst_small_letter_spacing', array(
            'label'=>'Etiket Harf Aralığı','type'=>\Elementor\Controls_Manager::SLIDER,'size_units'=>array('px','em'),
            'range'=>array('px'=>array('min'=>-4,'max'=>16,'step'=>.1),'em'=>array('min'=>-.15,'max'=>.8,'step'=>.01)),
            'selectors'=>array('{{WRAPPER}} small, {{WRAPPER}} .wpst-ew-eyebrow, {{WRAPPER}} [class*="badge"], {{WRAPPER}} [class*="eyebrow"]'=>'letter-spacing:{{SIZE}}{{UNIT}};'),
        ) );
        }

        if ( $this->wpst_has_control_capability( 'button' ) ) {
        $this->add_control( 'wpst_button_heading', array(
            'label'=>'Buton / Link Biçimi','type'=>\Elementor\Controls_Manager::HEADING,'separator'=>'before',
        ) );
        $this->add_group_control( \Elementor\Group_Control_Typography::get_type(), array(
            'name'=>'wpst_button_typography','selector'=>$this->wpst_action_selector(),
        ) );
        $this->add_responsive_control( 'wpst_button_font_size', array(
            'label'=>'Buton Yazı Boyutu','type'=>\Elementor\Controls_Manager::SLIDER,'size_units'=>array('px','em','rem'),
            'range'=>array('px'=>array('min'=>8,'max'=>48),'em'=>array('min'=>.5,'max'=>3,'step'=>.1),'rem'=>array('min'=>.5,'max'=>3,'step'=>.1)),
            'selectors'=>array(
                $this->wpst_action_selector()=>'font-size:{{SIZE}}{{UNIT}}!important;',
                '{{WRAPPER}}'=>'--wpst-local-button-size:{{SIZE}}{{UNIT}};'
            ),
        ) );
        $this->add_responsive_control( 'wpst_button_line_height', array(
            'label'=>'Buton Satır Yüksekliği','type'=>\Elementor\Controls_Manager::SLIDER,'size_units'=>array('em','px'),
            'range'=>array('em'=>array('min'=>.7,'max'=>2.5,'step'=>.05),'px'=>array('min'=>8,'max'=>80)),
            'selectors'=>array(
                $this->wpst_action_selector()=>'line-height:{{SIZE}}{{UNIT}}!important;',
                '{{WRAPPER}}'=>'--wpst-local-button-line:{{SIZE}}{{UNIT}};'
            ),
        ) );
        $this->add_responsive_control( 'wpst_button_letter_spacing', array(
            'label'=>'Buton Harf Aralığı','type'=>\Elementor\Controls_Manager::SLIDER,'size_units'=>array('px','em'),
            'range'=>array('px'=>array('min'=>-4,'max'=>16,'step'=>.1),'em'=>array('min'=>-.15,'max'=>.8,'step'=>.01)),
            'selectors'=>array(
                $this->wpst_action_selector()=>'letter-spacing:{{SIZE}}{{UNIT}}!important;',
                '{{WRAPPER}}'=>'--wpst-local-button-letter:{{SIZE}}{{UNIT}};'
            ),
        ) );
        $this->add_control( 'wpst_button_text_color', array(
            'label'=>'Buton Yazı Rengi','type'=>\Elementor\Controls_Manager::COLOR,
            'selectors'=>array(
                $this->wpst_action_selector()=>'color:{{VALUE}}!important;',
                '{{WRAPPER}}'=>'--wpst-local-button-color:{{VALUE}};'
            ),
        ) );
        $this->add_control( 'wpst_button_background', array(
            'label'=>'Buton Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,
            'selectors'=>array(
                $this->wpst_action_selector()=>'background:{{VALUE}}!important;',
                '{{WRAPPER}}'=>'--wpst-local-button-bg:{{VALUE}};'
            ),
        ) );
        $this->add_control( 'wpst_button_hover_text_color', array(
            'label'=>'Buton Hover Yazı','type'=>\Elementor\Controls_Manager::COLOR,
            'selectors'=>array(
                $this->wpst_action_hover_selector()=>'color:{{VALUE}}!important;',
                '{{WRAPPER}}'=>'--wpst-local-button-hover-color:{{VALUE}};'
            ),
        ) );
        $this->add_control( 'wpst_button_hover_background', array(
            'label'=>'Buton Hover Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,
            'selectors'=>array(
                $this->wpst_action_hover_selector()=>'background-color:{{VALUE}}!important;',
                '{{WRAPPER}}'=>'--wpst-local-button-hover-bg:{{VALUE}};'
            ),
        ) );
        $this->add_responsive_control( 'wpst_button_padding', array(
            'label'=>'Buton İç Boşluk','type'=>\Elementor\Controls_Manager::DIMENSIONS,'size_units'=>array('px','em','rem'),
            'selectors'=>array(
                $this->wpst_action_selector()=>'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}!important;',
                '{{WRAPPER}}'=>'--wpst-local-button-pad-top:{{TOP}}{{UNIT}};--wpst-local-button-pad-right:{{RIGHT}}{{UNIT}};--wpst-local-button-pad-bottom:{{BOTTOM}}{{UNIT}};--wpst-local-button-pad-left:{{LEFT}}{{UNIT}};'
            ),
        ) );
        $this->add_responsive_control( 'wpst_button_radius', array(
            'label'=>'Buton Köşe','type'=>\Elementor\Controls_Manager::SLIDER,'size_units'=>array('px','%'),
            'range'=>array('px'=>array('min'=>0,'max'=>80),'%'=>array('min'=>0,'max'=>50)),
            'selectors'=>array(
                $this->wpst_action_selector()=>'border-radius:{{SIZE}}{{UNIT}}!important;',
                '{{WRAPPER}}'=>'--wpst-local-button-radius:{{SIZE}}{{UNIT}};'
            ),
        ) );

        }

        if ( $this->wpst_has_control_capability( 'media' ) ) {
        $this->add_control( 'wpst_media_heading', array(
            'label'=>'Görsel Biçimi','type'=>\Elementor\Controls_Manager::HEADING,'separator'=>'before',
        ) );
        $this->add_responsive_control( 'wpst_image_width', array(
            'label'=>'Görsel Genişliği','type'=>\Elementor\Controls_Manager::SLIDER,'size_units'=>array('px','%','vw'),
            'range'=>array('px'=>array('min'=>20,'max'=>1800),'%'=>array('min'=>5,'max'=>100),'vw'=>array('min'=>5,'max'=>100)),
            'selectors'=>array($this->wpst_media_selector()=>'width:{{SIZE}}{{UNIT}};max-width:100%;'),
        ) );
        $this->add_responsive_control( 'wpst_image_height', array(
            'label'=>'Görsel Yüksekliği','type'=>\Elementor\Controls_Manager::SLIDER,'size_units'=>array('px','vh'),
            'range'=>array('px'=>array('min'=>60,'max'=>1200),'vh'=>array('min'=>10,'max'=>100)),
            'selectors'=>array($this->wpst_media_selector()=>'height:{{SIZE}}{{UNIT}};'),
        ) );
        $this->add_control( 'wpst_image_fit', array(
            'label'=>'Görsel Oturtma','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'',
            'options'=>array(''=>'Widget Varsayılanı','cover'=>'Cover','contain'=>'Contain','fill'=>'Fill','none'=>'None'),
            'selectors'=>array($this->wpst_media_selector()=>'object-fit:{{VALUE}};'),
        ) );
        $this->add_responsive_control( 'wpst_image_radius', array(
            'label'=>'Görsel Köşe','type'=>\Elementor\Controls_Manager::SLIDER,'size_units'=>array('px','%'),
            'range'=>array('px'=>array('min'=>0,'max'=>120),'%'=>array('min'=>0,'max'=>50)),
            'selectors'=>array($this->wpst_media_selector()=>'border-radius:{{SIZE}}{{UNIT}};'),
        ) );
        $this->add_control( 'wpst_image_opacity', array(
            'label'=>'Görsel Opaklığı','type'=>\Elementor\Controls_Manager::SLIDER,
            'range'=>array(''=>array('min'=>0,'max'=>1,'step'=>.01)),
            'selectors'=>array($this->wpst_media_selector()=>'opacity:{{SIZE}};'),
        ) );

        }

        $this->add_responsive_control( 'wpst_box_radius', array(
            'label' => 'Köşe Yuvarlaklığı',
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => array( 'px', '%' ),
            'range' => array(
                'px' => array( 'min' => 0, 'max' => 80 ),
                '%'  => array( 'min' => 0, 'max' => 50 ),
            ),
            'selectors' => array(
                '{{WRAPPER}} > .elementor-widget-container' => 'border-radius:{{SIZE}}{{UNIT}};',
            ),
        ) );

        $this->add_responsive_control( 'wpst_box_padding', array(
            'label' => 'İç Boşluk',
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => array( 'px', '%', 'em' ),
            'selectors' => array(
                '{{WRAPPER}} > .elementor-widget-container' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ),
        ) );


        $this->add_responsive_control( 'wpst_box_min_height', array(
            'label' => 'Minimum Yükseklik',
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => array( 'px', 'vh' ),
            'range' => array(
                'px' => array( 'min' => 0, 'max' => 1200 ),
                'vh' => array( 'min' => 0, 'max' => 100 ),
            ),
            'selectors' => array(
                '{{WRAPPER}} > .elementor-widget-container' => 'min-height:{{SIZE}}{{UNIT}};',
            ),
        ) );

        $this->add_control( 'wpst_box_overflow', array(
            'label' => 'Taşma',
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => '',
            'options' => array(
                '' => 'Widget Varsayılanı',
                'visible' => 'Görünür',
                'hidden' => 'Gizle',
                'clip' => 'Clip',
            ),
            'selectors' => array(
                '{{WRAPPER}} > .elementor-widget-container' => 'overflow:{{VALUE}};',
            ),
        ) );

        $this->add_responsive_control( 'wpst_content_align', array(
            'label' => 'İçerik Hizalama',
            'type' => \Elementor\Controls_Manager::CHOOSE,
            'options' => array(
                'left' => array( 'title' => 'Sol', 'icon' => 'eicon-text-align-left' ),
                'center' => array( 'title' => 'Orta', 'icon' => 'eicon-text-align-center' ),
                'right' => array( 'title' => 'Sağ', 'icon' => 'eicon-text-align-right' ),
            ),
            'selectors' => array(
                '{{WRAPPER}} > .elementor-widget-container' => 'text-align:{{VALUE}};',
                '{{WRAPPER}}' => '--wpst-content-align:{{VALUE}};',
            ),
        ) );

        $this->add_control( 'wpst_hover_lift', array(
            'label' => 'Hover Yükselme',
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => '',
            'prefix_class' => 'wpst-hover-lift-',
        ) );

        $this->end_controls_section();

        $this->start_controls_section( 'wpst_standard_advanced', array(
            'label' => 'Gelişmiş · Responsive & Motion',
            'tab'   => \Elementor\Controls_Manager::TAB_ADVANCED,
        ) );

        $this->add_responsive_control( 'wpst_width', array(
            'label' => 'Genişlik',
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => array( 'px', '%', 'vw' ),
            'range' => array(
                'px' => array( 'min' => 40, 'max' => 1800 ),
                '%' => array( 'min' => 5, 'max' => 100 ),
                'vw' => array( 'min' => 5, 'max' => 100 ),
            ),
            'selectors' => array(
                '{{WRAPPER}} > .elementor-widget-container' => 'width:{{SIZE}}{{UNIT}};',
            ),
        ) );
        $this->add_responsive_control( 'wpst_max_width', array(
            'label' => 'Maksimum Genişlik',
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => array( 'px', '%', 'vw' ),
            'range' => array(
                'px' => array( 'min' => 200, 'max' => 1800 ),
                '%' => array( 'min' => 10, 'max' => 100 ),
                'vw' => array( 'min' => 10, 'max' => 100 ),
            ),
            'selectors' => array(
                '{{WRAPPER}} > .elementor-widget-container' => 'max-width:{{SIZE}}{{UNIT}};margin-left:auto;margin-right:auto;',
            ),
        ) );

        $this->add_responsive_control( 'wpst_outer_margin', array(
            'label' => 'Dış Boşluk',
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => array( 'px', '%', 'em' ),
            'selectors' => array(
                '{{WRAPPER}}' => 'margin:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ),
        ) );

        $this->add_responsive_control( 'wpst_auto_widget_gap', array(
            'label' => 'Widgetlar Arası Otomatik Boşluk',
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => array( 'px', 'rem' ),
            'range' => array(
                'px' => array( 'min' => 0, 'max' => 80 ),
                'rem' => array( 'min' => 0, 'max' => 5, 'step' => .1 ),
            ),
            'default' => array( 'unit' => 'px', 'size' => 18 ),
            'tablet_default' => array( 'unit' => 'px', 'size' => 16 ),
            'mobile_default' => array( 'unit' => 'px', 'size' => 14 ),
            'selectors' => array(
                '{{WRAPPER}}' => '--wpst-widget-stack-gap:{{SIZE}}{{UNIT}};',
            ),
            'description' => 'Aynı Elementor alanında arka arkaya eklenen WPSoft widgetların birbirine yapışmasını önler. 0 yaparsanız kapatılır.',
        ) );

        $this->add_responsive_control( 'wpst_widget_zindex', array(
            'label' => 'Z-Index',
            'type' => \Elementor\Controls_Manager::NUMBER,
            'min' => -10,
            'max' => 9999,
            'selectors' => array(
                '{{WRAPPER}}' => 'z-index:{{VALUE}};',
            ),
        ) );

        $this->add_control( 'wpst_responsive_v2_heading', array(
            'label' => 'Responsive Layout 2.0',
            'type' => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ) );

        $this->add_control( 'wpst_responsive_preset', array(
            'label' => 'Responsive Tipografi Preseti',
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'inherit',
            'options' => array(
                'inherit' => 'Mevcut / Global',
                'balanced' => 'Balanced',
                'compact' => 'Compact',
                'editorial' => 'Editorial',
                'fluid' => 'Fluid',
            ),
            'prefix_class' => 'wpst-responsive-preset-',
            'description' => 'Özel cihaz tipografi değerleri girilmemişse dengeli responsive ölçek sağlar.',
        ) );

        $this->add_responsive_control( 'wpst_responsive_order', array(
            'label' => 'Sıralama',
            'type' => \Elementor\Controls_Manager::NUMBER,
            'min' => -20,
            'max' => 20,
            'selectors' => array(
                '{{WRAPPER}}' => 'order:{{VALUE}};',
            ),
            'description' => 'Aynı flex/grid alanındaki widget sırasını cihaz bazında değiştirir.',
        ) );

        $this->add_responsive_control( 'wpst_responsive_align_self', array(
            'label' => 'Kendi Hizası',
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => '',
            'options' => array(
                '' => 'Varsayılan',
                'auto' => 'Auto',
                'flex-start' => 'Başlangıç',
                'center' => 'Orta',
                'flex-end' => 'Bitiş',
                'stretch' => 'Uzat',
            ),
            'selectors' => array(
                '{{WRAPPER}}' => 'align-self:{{VALUE}};',
            ),
        ) );

        $this->add_responsive_control( 'wpst_responsive_inner_gap', array(
            'label' => 'İç Öğeler Aralığı',
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => array( 'px', 'rem' ),
            'range' => array(
                'px' => array( 'min' => 0, 'max' => 120 ),
                'rem' => array( 'min' => 0, 'max' => 8, 'step' => .1 ),
            ),
            'selectors' => array(
                '{{WRAPPER}}' => '--wpst-responsive-gap:{{SIZE}}{{UNIT}};',
            ),
        ) );

        $this->add_responsive_control( 'wpst_responsive_action_width', array(
            'label' => 'Buton / Aksiyon Genişliği',
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => '',
            'options' => array(
                '' => 'Widget Varsayılanı',
                'auto' => 'Otomatik',
                '100%' => 'Tam Genişlik',
            ),
            'selectors' => array(
                '{{WRAPPER}} a.wpst-button, {{WRAPPER}} .wpst-button, {{WRAPPER}} .wpst-adv-button, {{WRAPPER}} .wpst-cta a, {{WRAPPER}} .wpst-booking-strip a, {{WRAPPER}} .wpst-ew-hero-slide a' => 'width:{{VALUE}};',
            ),
        ) );

        $this->add_responsive_control( 'wpst_responsive_media_position', array(
            'label' => 'Görsel Odak Noktası',
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => '',
            'options' => array(
                '' => 'Varsayılan',
                'center center' => 'Orta',
                'center top' => 'Üst Orta',
                'center bottom' => 'Alt Orta',
                'left center' => 'Sol',
                'right center' => 'Sağ',
            ),
            'selectors' => array(
                '{{WRAPPER}} img, {{WRAPPER}} video' => 'object-position:{{VALUE}};',
            ),
        ) );

        $this->add_responsive_control( 'wpst_responsive_overflow', array(
            'label' => 'Cihaz Taşma Davranışı',
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => '',
            'options' => array(
                '' => 'Varsayılan',
                'visible' => 'Görünür',
                'hidden' => 'Gizle',
                'clip' => 'Clip',
                'auto' => 'Otomatik Kaydır',
            ),
            'selectors' => array(
                '{{WRAPPER}} > .elementor-widget-container' => 'overflow:{{VALUE}};',
            ),
        ) );

        $this->add_control( 'wpst_stack_tablet', array(
            'label' => 'Tablette Kartları Tek Kolon',
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => '',
            'prefix_class' => 'wpst-stack-tablet-',
            'description' => 'WPSoft grid/kart yapılarında tablet görünümünde güvenli tek kolon fallback uygular.',
        ) );

        $this->add_control( 'wpst_stack_mobile', array(
            'label' => 'Mobilde Kartları Tek Kolon',
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => '',
            'prefix_class' => 'wpst-stack-mobile-',
            'description' => 'WPSoft grid/kart yapılarında mobil görünümde güvenli tek kolon fallback uygular.',
        ) );

        $this->add_control( 'wpst_text_wrap_mode', array(
            'label' => 'Başlık Satır Kırılımı',
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'inherit',
            'options' => array(
                'inherit' => 'Varsayılan',
                'balance' => 'Dengeli',
                'pretty' => 'Doğal / Pretty',
            ),
            'prefix_class' => 'wpst-text-wrap-',
        ) );

        $this->add_control( 'wpst_visibility_heading', array(
            'label' => 'Cihaz Görünürlüğü',
            'type' => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ) );
        $this->add_control( 'wpst_hide_desktop', array(
            'label' => 'Masaüstünde Gizle',
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => '',
            'prefix_class' => 'wpst-hide-desktop-',
        ) );
        $this->add_control( 'wpst_hide_tablet', array(
            'label' => 'Tablette Gizle',
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => '',
            'prefix_class' => 'wpst-hide-tablet-',
        ) );
        $this->add_control( 'wpst_hide_mobile', array(
            'label' => 'Mobilde Gizle',
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => '',
            'prefix_class' => 'wpst-hide-mobile-',
        ) );
        $this->add_control( 'wpst_motion_mode', array(
            'label' => 'Hareket',
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'global',
            'options' => array(
                'global' => 'Global Ayarı Kullan',
                'none' => 'Kapalı',
                'soft' => 'Yumuşak',
                'normal' => 'Normal',
                'dynamic' => 'Dinamik',
            ),
            'prefix_class' => 'wpst-motion-',
        ) );

        $this->add_control( 'wpst_entry_motion', array(
            'label' => 'Giriş Animasyonu',
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'none',
            'options' => array(
                'none' => 'Yok',
                'fade' => 'Fade',
                'fade-up' => 'Fade Up',
                'fade-down' => 'Fade Down',
                'fade-left' => 'Fade Left',
                'fade-right' => 'Fade Right',
                'scale' => 'Scale In',
                'zoom' => 'Zoom In',
                'blur' => 'Blur Reveal',
                'rotate-soft' => 'Soft Rotate',
                'clip-up' => 'Clip Up',
                'reveal-left' => 'Reveal Left',
                'reveal-right' => 'Reveal Right',
                'flip-soft' => 'Soft Flip',
            ),
            'prefix_class' => 'wpst-entry-',
        ) );

        $this->add_control( 'wpst_entry_delay', array(
            'label' => 'Animasyon Gecikmesi',
            'type' => \Elementor\Controls_Manager::NUMBER,
            'min' => 0, 'max' => 3000, 'step' => 50, 'default' => 0,
            'selectors' => array(
                '{{WRAPPER}}' => '--wpst-motion-delay:{{VALUE}}ms;',
            ),
            'condition' => array('wpst_entry_motion!' => 'none'),
        ) );

        $this->add_control( 'wpst_entry_duration', array(
            'label' => 'Animasyon Süresi',
            'type' => \Elementor\Controls_Manager::NUMBER,
            'min' => 200, 'max' => 3000, 'step' => 50, 'default' => 700,
            'selectors' => array(
                '{{WRAPPER}}' => '--wpst-motion-duration:{{VALUE}}ms;',
            ),
            'condition' => array('wpst_entry_motion!' => 'none'),
        ) );

        $this->add_control( 'wpst_entry_distance', array(
            'label' => 'Hareket Mesafesi',
            'type' => \Elementor\Controls_Manager::NUMBER,
            'min' => 4, 'max' => 140, 'step' => 2, 'default' => 28,
            'selectors' => array(
                '{{WRAPPER}}' => '--wpst-motion-distance:{{VALUE}}px;',
            ),
            'condition' => array('wpst_entry_motion!' => 'none'),
        ) );

        $this->add_control( 'wpst_entry_easing', array(
            'label' => 'Animasyon Eğrisi',
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'smooth',
            'options' => array(
                'smooth' => 'Smooth',
                'ease' => 'Ease',
                'snappy' => 'Snappy',
                'spring' => 'Soft Spring',
                'linear' => 'Linear',
            ),
            'prefix_class' => 'wpst-ease-',
            'condition' => array('wpst_entry_motion!' => 'none'),
        ) );

        $this->add_control( 'wpst_entry_repeat', array(
            'label' => 'Her Görünüşte Tekrarla',
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => '',
            'prefix_class' => 'wpst-motion-repeat-',
            'condition' => array('wpst_entry_motion!' => 'none'),
        ) );

        $this->add_control( 'wpst_entry_threshold', array(
            'label' => 'Tetikleme Eşiği %',
            'type' => \Elementor\Controls_Manager::NUMBER,
            'min' => 0, 'max' => 80, 'step' => 5, 'default' => 15,
            'selectors' => array(
                '{{WRAPPER}}' => '--wpst-motion-threshold:{{VALUE}};',
            ),
            'condition' => array('wpst_entry_motion!' => 'none'),
        ) );

        $this->add_control( 'wpst_motion_device_heading', array(
            'label' => 'Cihaz Kontrolü',
            'type' => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
            'condition' => array('wpst_entry_motion!' => 'none'),
        ) );

        $this->add_control( 'wpst_motion_disable_desktop', array(
            'label' => 'Masaüstünde Animasyonu Kapat',
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => '',
            'prefix_class' => 'wpst-motion-disable-desktop-',
            'condition' => array('wpst_entry_motion!' => 'none'),
        ) );

        $this->add_control( 'wpst_motion_disable_tablet', array(
            'label' => 'Tablette Animasyonu Kapat',
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => '',
            'prefix_class' => 'wpst-motion-disable-tablet-',
            'condition' => array('wpst_entry_motion!' => 'none'),
        ) );

        $this->add_control( 'wpst_motion_disable_mobile', array(
            'label' => 'Mobilde Animasyonu Kapat',
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => '',
            'prefix_class' => 'wpst-motion-disable-mobile-',
            'condition' => array('wpst_entry_motion!' => 'none'),
        ) );

        $this->add_control( 'wpst_hover_motion', array(
            'label' => 'Hover Animasyonu',
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'none',
            'options' => array(
                'none' => 'Yok',
                'lift' => 'Yüksel',
                'scale' => 'Hafif Büyüt',
                'tilt' => 'Soft Tilt',
                'glow' => 'Soft Glow',
                'press' => 'Basılma Hissi',
            ),
            'prefix_class' => 'wpst-hover-motion-',
        ) );

        $this->add_control( 'wpst_stagger_children', array(
            'label' => 'İç Öğeleri Sırayla Göster',
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => '',
            'prefix_class' => 'wpst-stagger-',
            'condition' => array('wpst_entry_motion!' => 'none'),
        ) );

        $this->add_control( 'wpst_stagger_delay', array(
            'label' => 'Öğe Aralığı (ms)',
            'type' => \Elementor\Controls_Manager::NUMBER,
            'min' => 30, 'max' => 500, 'step' => 10, 'default' => 90,
            'selectors' => array(
                '{{WRAPPER}}' => '--wpst-stagger-delay:{{VALUE}}ms;',
            ),
            'condition' => array('wpst_stagger_children' => 'yes'),
        ) );

        $this->add_control( 'wpst_stagger_style', array(
            'label' => 'Stagger Efekti',
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'fade-up',
            'options' => array(
                'fade' => 'Fade',
                'fade-up' => 'Fade Up',
                'scale' => 'Scale',
                'blur' => 'Blur',
            ),
            'prefix_class' => 'wpst-stagger-style-',
            'condition' => array('wpst_stagger_children' => 'yes'),
        ) );

        $this->add_control( 'wpst_motion_advanced_note', array(
            'type' => \Elementor\Controls_Manager::RAW_HTML,
            'raw' => '<strong>WPSoft Motion System 1.0</strong><br>Giriş animasyonu viewport içinde tetiklenir. Repeat açıksa ekrandan çıkınca sıfırlanır. Stagger, widget içindeki kart/öğeleri sırayla gösterir. Kullanıcının Reduce Motion tercihi her zaman önceliklidir.',
            'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
            'condition' => array('wpst_entry_motion!' => 'none'),
        ) );

        $this->add_control( 'wpst_parallax_enabled', array(
            'label' => 'Hafif Parallax',
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => '',
            'prefix_class' => 'wpst-parallax-',
        ) );

        $this->add_control( 'wpst_mouse_follow_enabled', array(
            'label' => 'Mouse Follow / Tilt',
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => '',
            'prefix_class' => 'wpst-mouse-follow-',
        ) );

        $this->add_control( 'wpst_layout_presets_heading', array(
            'label' => 'Layout Presetleri',
            'type' => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ) );

        $this->add_control( 'wpst_layout_density', array(
            'label' => 'İçerik Yoğunluğu',
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'default',
            'options' => array(
                'default' => 'Widget Varsayılanı',
                'compact' => 'Compact',
                'comfortable' => 'Comfortable',
                'airy' => 'Airy',
            ),
            'prefix_class' => 'wpst-density-',
        ) );

        $this->add_control( 'wpst_content_width_preset', array(
            'label' => 'İçerik Genişlik Preseti',
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'default',
            'options' => array(
                'default' => 'Widget Varsayılanı',
                'reading' => 'Reading · 760px',
                'narrow' => 'Narrow · 920px',
                'standard' => 'Standard · 1180px',
                'wide' => 'Wide · 1360px',
                'full' => 'Full Width',
            ),
            'prefix_class' => 'wpst-content-width-',
        ) );

        $this->add_control( 'wpst_radius_preset_local', array(
            'label' => 'Köşe Preseti',
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'default',
            'options' => array(
                'default' => 'Widget Varsayılanı',
                'square' => 'Square',
                'soft' => 'Soft · 12px',
                'modern' => 'Modern · 20px',
                'round' => 'Round · 30px',
            ),
            'prefix_class' => 'wpst-radius-preset-',
        ) );

        $this->add_control( 'wpst_surface_depth', array(
            'label' => 'Yüzey Derinliği',
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'default',
            'options' => array(
                'default' => 'Widget Varsayılanı',
                'flat' => 'Flat',
                'soft' => 'Soft Shadow',
                'elevated' => 'Elevated',
            ),
            'prefix_class' => 'wpst-depth-',
        ) );

        $this->add_responsive_control( 'wpst_component_gap', array(
            'label' => 'Bileşen İç Aralığı',
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => array( 'px', 'rem' ),
            'range' => array(
                'px' => array( 'min' => 0, 'max' => 120 ),
                'rem' => array( 'min' => 0, 'max' => 8, 'step' => .1 ),
            ),
            'selectors' => array(
                '{{WRAPPER}}' => '--wpst-component-gap:{{SIZE}}{{UNIT}};',
            ),
        ) );

        $this->add_responsive_control( 'wpst_mobile_content_align', array(
            'label' => 'Cihaz İçerik Hizası',
            'type' => \Elementor\Controls_Manager::CHOOSE,
            'options' => array(
                'left' => array( 'title' => 'Sol', 'icon' => 'eicon-text-align-left' ),
                'center' => array( 'title' => 'Orta', 'icon' => 'eicon-text-align-center' ),
                'right' => array( 'title' => 'Sağ', 'icon' => 'eicon-text-align-right' ),
            ),
            'selectors' => array(
                '{{WRAPPER}} > .elementor-widget-container' => 'text-align:{{VALUE}};',
            ),
        ) );

        $this->add_control( 'wpst_framework_note', array(
            'type' => \Elementor\Controls_Manager::RAW_HTML,
            'raw'  => '<strong>Widget Framework 2.0 + Motion 3.0 + Responsive 2.0</strong><br>Renk, tipografi, boşluk ve cihaz ayarları ortak WPSoft kontrol sözleşmesini kullanır. Widgetın kendi özel kontrolleri varsa lokal değerler önceliklidir.',
            'content_classes' => 'wpst-control-note',
        ) );

        $this->add_control( 'wpst_mobile_note', array(
            'type' => \Elementor\Controls_Manager::RAW_HTML,
            'raw'  => '<strong>Responsive:</strong> Cihaz simgesinden Masaüstü / Tablet / Mobil değerlerini ayrı belirleyebilirsiniz. Responsive Layout 2.0 ile sıralama, hizalama, iç aralık, aksiyon genişliği, medya odağı ve taşma davranışı da cihaz bazında yönetilir.',
            'content_classes' => 'wpst-control-note',
        ) );

        $this->end_controls_section();

        $this->start_controls_section( 'wpst_quality_responsive', array(
            'label' => '03 · Responsive & Etkileşim',
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ) );

        $this->add_control( 'wpst_quality_motion', array(
            'label' => 'Hareket Tercihi',
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'auto',
            'options' => array(
                'auto' => 'Sistem / Widget Varsayılanı',
                'subtle' => 'Hafif',
                'off' => 'Animasyonu Azalt',
            ),
            'prefix_class' => 'wpst-motion-pref-',
        ) );

        $this->add_control( 'wpst_quality_touch_targets', array(
            'label' => 'Mobil Dokunma Alanlarını Koru',
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => 'yes',
            'prefix_class' => 'wpst-safe-touch-',
            'description' => 'Slider okları, sekmeler ve küçük kontroller mobilde en az 42px kullanılabilir alanı korur.',
        ) );

        $this->add_control( 'wpst_quality_overflow', array(
            'label' => 'Mobil Taşma Koruması',
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => 'yes',
            'prefix_class' => 'wpst-safe-overflow-',
        ) );


    }

    protected function render_link_attrs( $link ) {
        $url = ! empty( $link['url'] ) ? $link['url'] : '#';
        $attrs = ' href="' . esc_url( $url ) . '"';
        if ( ! empty( $link['is_external'] ) ) $attrs .= ' target="_blank"';
        if ( ! empty( $link['nofollow'] ) ) $attrs .= ' rel="nofollow"';
        return $attrs;
    }

    protected function wpst_icon_control($id='wpst_icon',$label='WPSoft Icon',$default='sparkles',$extra=array()){
        $args=array(
            'label'=>$label,
            'type'=>\Elementor\Controls_Manager::SELECT2,
            'options'=>class_exists('WPST_Icon_Library')?WPST_Icon_Library::options():array(),
            'default'=>$default,
            'label_block'=>true,
        );
        $this->add_control($id,array_merge($args,$extra));
    }

    protected function wpst_shape_control($id='wpst_shape',$label='WPSoft SVG',$default='blob-soft',$extra=array()){
        $args=array(
            'label'=>$label,
            'type'=>\Elementor\Controls_Manager::SELECT2,
            'options'=>class_exists('WPST_SVG_Library')?WPST_SVG_Library::options():array(),
            'default'=>$default,
            'label_block'=>true,
        );
        $this->add_control($id,array_merge($args,$extra));
    }

    protected function render_wpst_icon($slug,$args=array()){
        if(class_exists('WPST_Icon_Library'))WPST_Icon_Library::render($slug,$args);
    }

}
