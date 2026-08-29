<?php
if ( ! defined( 'ABSPATH' ) ) exit;

final class WPST_Header_Footer_Templates {

    public static function init() {
        add_action( 'admin_menu', array( __CLASS__, 'menu' ), 22 );
        add_action( 'admin_post_wpst_apply_hf_template', array( __CLASS__, 'apply' ) );
        add_action( 'admin_post_wpst_create_elementor_hf_template', array( __CLASS__, 'create_elementor_template' ) );
        add_action( 'wp_ajax_wpst_hf_library', array( __CLASS__, 'ajax_library' ) );
        add_action( 'wp_ajax_wpst_create_hf_template_ajax', array( __CLASS__, 'ajax_create_elementor_template' ) );
        add_action( 'admin_post_wpst_delete_my_template', array( __CLASS__, 'delete_my_template' ) );
        add_action( 'admin_post_wpst_duplicate_my_template', array( __CLASS__, 'duplicate_my_template' ) );
        add_action( 'admin_post_wpst_toggle_template_favorite', array( __CLASS__, 'toggle_template_favorite' ) );
        add_action( 'admin_post_wpst_export_my_template', array( __CLASS__, 'export_my_template' ) );
        add_action( 'admin_post_wpst_save_template_conditions', array( __CLASS__, 'save_template_conditions' ) );
        add_action( 'admin_init', array( __CLASS__, 'migrate_header_navigation_widgets' ), 25 );
    }


    public static function editor_assets() {
        wp_enqueue_style( 'wpst-hf-library-css', WPST_URL . 'assets/css/wpst-hf-library.css', array(), WPST_VERSION );
        wp_enqueue_script(
            'wpst-hf-library-tabs',
            WPST_URL . 'assets/js/wpst-hf-library-tabs.js',
            array( 'jquery' ),
            WPST_VERSION,
            true
        );
    }

    /**
     * WPSoft Template Library için tek kaynak header verisi.
     * Header/Footer Şablonları ile Elementor içindeki WPSoft Template Library
     * aynı gerçek Elementor verisini kullanır.
     */
    public static function header_library_items() {
        $items = array();
        foreach ( self::headers() as $key => $item ) {
            $premium_keys = array('floating','glass','pill','executive-glass','studio-minimal','saas-command','luxury-center','industry-pro','hotel-reserve');
            $items[] = array(
                'key'           => 'header-' . sanitize_key( $key ),
                'source_key'    => sanitize_key( $key ),
                'title'         => isset( $item['title'] ) ? $item['title'] : $key,
                'desc'          => isset( $item['desc'] ) ? $item['desc'] : '',
                'preview_image' => WPST_URL . 'assets/images/header-footer/' . ( isset( $item['preview'] ) ? $item['preview'] : '' ),
                'data'          => self::elementor_hf_data( 'header', $key, $item ),
                'category'      => 'Header',
                'sector'        => isset($item['sector']) ? $item['sector'] : 'Genel',
                'template_role' => 'header',
                'is_new'        => in_array($key,$premium_keys,true),
                'is_popular'    => in_array($key,array('corporate','floating','glass','pill','executive-glass'),true),
                'premium'       => in_array($key,$premium_keys,true),
                'quality'       => in_array($key,$premium_keys,true) ? 'Signature' : '',
            );
        }
        return $items;
    }


    /**
     * WPSoft Template Library için tek kaynak footer verisi.
     * Böylece Header/Footer Şablonları ile Elementor Template Library aynı koleksiyonu kullanır.
     */
    public static function footer_library_items() {
        $items = array();
        foreach ( self::footers() as $key => $item ) {
            $items[] = array(
                'key'           => 'footer-' . sanitize_key( $key ),
                'source_key'    => sanitize_key( $key ),
                'title'         => isset( $item['title'] ) ? $item['title'] : $key,
                'desc'          => isset( $item['desc'] ) ? $item['desc'] : '',
                'preview_image' => WPST_URL . 'assets/images/header-footer/' . ( isset( $item['preview'] ) ? $item['preview'] : '' ),
                'data'          => self::elementor_hf_data( 'footer', $key, $item ),
                'category'      => 'Footer',
                'sector'        => self::footer_sector( $key ),
                'template_role' => 'footer',
                'is_new'        => true,
                'is_popular'    => in_array($key,array('modern-saas','agency-bento','hotel-luxury','industry-modern','finance-trust','dark-gradient'),true),
                'premium'       => !in_array($key,array('corporate','dark','cta','minimal','dark-minimal','newsletter','centered'),true),
                'quality'       => !in_array($key,array('corporate','dark','cta','minimal','dark-minimal','newsletter','centered'),true) ? 'Signature' : '',
            );
        }
        return $items;
    }

    public static function ajax_library() {
        if ( ! current_user_can( 'edit_posts' ) ) {
            wp_send_json_error( array( 'message' => 'Yetkiniz yok.' ), 403 );
        }

        $headers = array();
        $footers = array();

        foreach ( self::headers() as $key => $item ) {
            $headers[] = array(
                'key' => $key,
                'title' => $item['title'],
                'desc' => $item['desc'],
                'preview_image' => WPST_URL . 'assets/images/header-footer/' . $item['preview'],
                'type' => 'header',
                'data' => self::elementor_hf_data( 'header', $key, $item ),
            );
        }

        foreach ( self::footers() as $key => $item ) {
            $footers[] = array(
                'key' => $key,
                'title' => $item['title'],
                'desc' => $item['desc'],
                'preview_image' => WPST_URL . 'assets/images/header-footer/' . $item['preview'],
                'type' => 'footer',
                'data' => self::elementor_hf_data( 'footer', $key, $item ),
            );
        }

        wp_send_json_success( array(
            'headers' => $headers,
            'footers' => $footers,
        ) );
    }

    public static function ajax_create_elementor_template() {
        if ( ! current_user_can( 'edit_posts' ) ) {
            wp_send_json_error( array( 'message' => 'Bu işlem için yetkiniz yok.' ), 403 );
        }

        check_ajax_referer( 'wpst_create_hf_template_ajax', 'nonce' );

        $type = isset( $_POST['type'] ) ? sanitize_key( wp_unslash( $_POST['type'] ) ) : '';
        $key  = isset( $_POST['template'] ) ? sanitize_key( wp_unslash( $_POST['template'] ) ) : '';

        if ( ! in_array( $type, array( 'header', 'footer' ), true ) ) {
            wp_send_json_error( array( 'message' => 'Geçersiz şablon türü.' ), 400 );
        }

        $all = ( 'footer' === $type ) ? self::footers() : self::headers();
        if ( empty( $all[ $key ] ) ) {
            wp_send_json_error( array( 'message' => 'Şablon bulunamadı.' ), 404 );
        }

        if ( ! did_action( 'elementor/loaded' ) || ! class_exists( '\Elementor\Plugin' ) ) {
            wp_send_json_error( array( 'message' => 'Elementor etkin olmalıdır.' ), 400 );
        }

        $item = $all[ $key ];

        $post_id = wp_insert_post( array(
            'post_title'  => 'WPSoft - ' . $item['title'],
            'post_type'   => 'elementor_library',
            'post_status' => 'publish',
        ), true );

        if ( is_wp_error( $post_id ) ) {
            wp_send_json_error( array( 'message' => $post_id->get_error_message() ), 500 );
        }

        if ( ! $post_id ) {
            wp_send_json_error( array( 'message' => 'Elementor şablonu oluşturulamadı.' ), 500 );
        }

        update_post_meta( $post_id, '_elementor_edit_mode', 'builder' );
        update_post_meta( $post_id, '_elementor_template_type', $type );
        update_post_meta( $post_id, '_wpst_hf_template', '1' );
        update_post_meta( $post_id, '_wpst_hf_type', $type );
        update_post_meta( $post_id, '_wpst_hf_key', $key );
        update_post_meta( $post_id, '_elementor_version', defined( 'ELEMENTOR_VERSION' ) ? ELEMENTOR_VERSION : '3.0.0' );

        $data = self::elementor_hf_data( $type, $key, $item );
        update_post_meta( $post_id, '_elementor_data', wp_slash( wp_json_encode( $data ) ) );

        // Elementor library taxonomy/type compatibility where available.
        if ( taxonomy_exists( 'elementor_library_type' ) ) {
            wp_set_object_terms( $post_id, $type, 'elementor_library_type', false );
        }

        wp_send_json_success( array(
            'post_id'  => $post_id,
            'edit_url' => admin_url( 'post.php?post=' . $post_id . '&action=elementor' ),
            'message'  => $item['title'] . ' oluşturuldu.',
        ) );
    }

    public static function headers() {
        return array(
            'corporate'=>array('title'=>'Kurumsal Header','desc'=>'Logo + menü + CTA.','preview'=>'header-corporate.svg','sections'=>3,'blocks'=>array(array('type'=>'logo','section'=>1),array('type'=>'menu','section'=>2),array('type'=>'button','section'=>3,'text'=>'Teklif Al','url'=>'#iletisim')),'settings'=>array('sticky'=>'yes','bg'=>'#ffffff','text'=>'#0f172a','button_bg'=>'#2563eb','radius'=>12)),
            'minimal'=>array('title'=>'Minimal Header','desc'=>'Sade logo + menü yapısı.','preview'=>'header-minimal.svg','sections'=>2,'blocks'=>array(array('type'=>'logo','section'=>1),array('type'=>'menu','section'=>2)),'settings'=>array('sticky'=>'','bg'=>'#ffffff','text'=>'#0f172a','radius'=>10)),
            'topbar'=>array('title'=>'Top Bar Header','desc'=>'Üst bilgi satırı + ana navigasyon.','preview'=>'header-topbar.svg','sections'=>3,'blocks'=>array(array('type'=>'html','section'=>1,'html'=>'<span>☎ 0212 000 00 00 &nbsp; • &nbsp; info@firma.com</span>'),array('type'=>'logo','section'=>1),array('type'=>'menu','section'=>2),array('type'=>'button','section'=>3,'text'=>'Bize Ulaşın','url'=>'#iletisim')),'settings'=>array('sticky'=>'yes','bg'=>'#ffffff','text'=>'#0f172a','button_bg'=>'#0f766e','radius'=>12)),
            'dark'=>array('title'=>'Koyu Header','desc'=>'Ajans ve teknoloji siteleri için koyu tasarım.','preview'=>'header-dark.svg','sections'=>3,'blocks'=>array(array('type'=>'logo','section'=>1),array('type'=>'menu','section'=>2),array('type'=>'button','section'=>3,'text'=>'Projeyi Başlat','url'=>'#iletisim')),'settings'=>array('sticky'=>'yes','bg'=>'#0f172a','text'=>'#ffffff','button_bg'=>'#7c3aed','radius'=>12)),
            'social'=>array('title'=>'Sosyal Header','desc'=>'Logo + menü + sosyal ikonlar.','preview'=>'header-social.svg','sections'=>3,'blocks'=>array(array('type'=>'logo','section'=>1),array('type'=>'menu','section'=>2),array('type'=>'social','section'=>3)),'settings'=>array('sticky'=>'','bg'=>'#ffffff','text'=>'#0f172a','button_bg'=>'#be123c','radius'=>12)),
            'transparent'=>array('title'=>'Şeffaf Hero Header','desc'=>'Hero üzerinde koyu/şeffaf hissiyat.','preview'=>'header-transparent.svg','sections'=>3,'blocks'=>array(array('type'=>'logo','section'=>1),array('type'=>'menu','section'=>2),array('type'=>'button','section'=>3,'text'=>'İletişim','url'=>'#iletisim')),'settings'=>array('sticky'=>'scroll','bg'=>'#0f172a','text'=>'#ffffff','button_bg'=>'#2563eb','radius'=>999)),

            'floating'=>array('title'=>'Floating Header','desc'=>'Sayfanın üstünde yüzen, yuvarlatılmış modern header.','preview'=>'header-floating.svg','sections'=>3,'blocks'=>array(array('type'=>'logo','section'=>1),array('type'=>'menu','section'=>2),array('type'=>'button','section'=>3,'text'=>'Teklif Al','url'=>'#iletisim')),'settings'=>array('sticky'=>'yes','bg'=>'#ffffff','text'=>'#0f172a','button_bg'=>'#2563eb','radius'=>999,'layout_class'=>'floating','shadow'=>'yes')),
            'glass'=>array('title'=>'Glass Header','desc'=>'Glassmorphism görünüm; ajans ve teknoloji siteleri için.','preview'=>'header-glass.svg','sections'=>3,'blocks'=>array(array('type'=>'logo','section'=>1),array('type'=>'menu','section'=>2),array('type'=>'button','section'=>3,'text'=>'Başlayalım','url'=>'#iletisim')),'settings'=>array('sticky'=>'yes','bg'=>'rgba(15,23,42,.78)','text'=>'#ffffff','button_bg'=>'#7c3aed','radius'=>22,'layout_class'=>'glass','shadow'=>'yes')),
            'centered'=>array('title'=>'Centered Logo Header','desc'=>'Ortalanmış logo, iki yana dağıtılmış menü hissiyatı.','preview'=>'header-centered.svg','sections'=>3,'blocks'=>array(array('type'=>'menu','section'=>1),array('type'=>'logo','section'=>2),array('type'=>'button','section'=>3,'text'=>'İletişim','url'=>'#iletisim')),'settings'=>array('sticky'=>'','bg'=>'#ffffff','text'=>'#0f172a','button_bg'=>'#0f766e','radius'=>14,'layout_class'=>'centered')),
            'announcement'=>array('title'=>'Announcement Header','desc'=>'Üst duyuru bandı + ana header. Kampanya siteleri için.','preview'=>'header-announcement.svg','sections'=>3,'blocks'=>array(array('type'=>'html','section'=>1,'html'=>'<div class="wpst-announcement">Yeni sezon fırsatları başladı — Detayları inceleyin</div>'),array('type'=>'logo','section'=>1),array('type'=>'menu','section'=>2),array('type'=>'button','section'=>3,'text'=>'Teklif Al','url'=>'#iletisim')),'settings'=>array('sticky'=>'scroll','bg'=>'#ffffff','text'=>'#0f172a','button_bg'=>'#ea580c','radius'=>14,'layout_class'=>'announcement')),
            'split'=>array('title'=>'Split Navigation Header','desc'=>'Logo ortada, navigasyon iki yana ayrılmış modern görünüm.','preview'=>'header-split.svg','sections'=>3,'blocks'=>array(array('type'=>'menu','section'=>1),array('type'=>'logo','section'=>2),array('type'=>'social','section'=>3)),'settings'=>array('sticky'=>'','bg'=>'#ffffff','text'=>'#0f172a','button_bg'=>'#111827','radius'=>16,'layout_class'=>'split')),
            'pill'=>array('title'=>'Pill Navigation Header','desc'=>'Menü ve CTA için pill yapısı kullanan modern SaaS header.','preview'=>'header-pill.svg','sections'=>3,'blocks'=>array(array('type'=>'logo','section'=>1),array('type'=>'menu','section'=>2),array('type'=>'button','section'=>3,'text'=>'Demo İste','url'=>'#iletisim')),'settings'=>array('sticky'=>'yes','bg'=>'#ffffff','text'=>'#0f172a','button_bg'=>'#2563eb','radius'=>999,'layout_class'=>'pill','shadow'=>'yes')),
            'executive-glass'=>array('title'=>'Executive Glass Header','desc'=>'Floating glass yüzey, kompakt navigasyon ve premium CTA ile Signature kurumsal header.','preview'=>'header-executive-glass.svg','sector'=>'Kurumsal','sections'=>3,'blocks'=>array(array('type'=>'logo','section'=>1),array('type'=>'menu','section'=>2),array('type'=>'button','section'=>3,'text'=>'Projeyi Başlat','url'=>'#iletisim')),'settings'=>array('sticky'=>'yes','bg'=>'rgba(255,255,255,.88)','text'=>'#0f172a','button_bg'=>'#111827','radius'=>999,'layout_class'=>'executive-glass','shadow'=>'yes')),
            'studio-minimal'=>array('title'=>'Studio Minimal Header','desc'=>'Yaratıcı stüdyo ve portföy siteleri için geniş boşluklu, tipografi odaklı Signature header.','preview'=>'header-studio-minimal.svg','sector'=>'Ajans','sections'=>3,'blocks'=>array(array('type'=>'logo','section'=>1),array('type'=>'menu','section'=>2),array('type'=>'button','section'=>3,'text'=>'Bize Yazın','url'=>'#iletisim')),'settings'=>array('sticky'=>'','bg'=>'#fffdf8','text'=>'#18181b','button_bg'=>'#18181b','radius'=>999,'layout_class'=>'studio-minimal')),
            'saas-command'=>array('title'=>'SaaS Command Header','desc'=>'Ürün navigasyonu, modern pill CTA ve koyu kontrastla teknoloji odaklı Signature header.','preview'=>'header-saas-command.svg','sector'=>'Yazılım','sections'=>3,'blocks'=>array(array('type'=>'logo','section'=>1),array('type'=>'menu','section'=>2),array('type'=>'button','section'=>3,'text'=>'Ücretsiz Başla','url'=>'#')),'settings'=>array('sticky'=>'yes','bg'=>'#0b1020','text'=>'#f8fafc','button_bg'=>'#7c3aed','radius'=>999,'layout_class'=>'saas-command','shadow'=>'yes')),
            'luxury-center'=>array('title'=>'Luxury Center Header','desc'=>'Otel, hukuk ve premium markalar için ortalanmış marka ve zarif navigasyon.','preview'=>'header-luxury-center.svg','sector'=>'Premium','sections'=>3,'blocks'=>array(array('type'=>'menu','section'=>1),array('type'=>'logo','section'=>2),array('type'=>'button','section'=>3,'text'=>'Rezervasyon','url'=>'#rezervasyon')),'settings'=>array('sticky'=>'scroll','bg'=>'#fffdf8','text'=>'#292524','button_bg'=>'#a16207','radius'=>4,'layout_class'=>'luxury-center')),
            'industry-pro'=>array('title'=>'Industry Pro Header','desc'=>'Makina ve sanayi siteleri için güçlü teknik navigasyon ve servis CTA.','preview'=>'header-industry-pro.svg','sector'=>'Sanayi','sections'=>3,'blocks'=>array(array('type'=>'logo','section'=>1),array('type'=>'menu','section'=>2),array('type'=>'button','section'=>3,'text'=>'Teknik Teklif','url'=>'#iletisim')),'settings'=>array('sticky'=>'yes','bg'=>'#101820','text'=>'#f8fafc','button_bg'=>'#efb321','radius'=>8,'layout_class'=>'industry-pro','shadow'=>'yes')),
            'hotel-reserve'=>array('title'=>'Hotel Reserve Header','desc'=>'Otel siteleri için rezervasyon odaklı zarif header ve premium CTA.','preview'=>'header-hotel-reserve.svg','sector'=>'Otel','sections'=>3,'blocks'=>array(array('type'=>'logo','section'=>1),array('type'=>'menu','section'=>2),array('type'=>'button','section'=>3,'text'=>'Rezervasyon Yap','url'=>'#rezervasyon')),'settings'=>array('sticky'=>'yes','bg'=>'#13251f','text'=>'#f2eadf','button_bg'=>'#c8a96b','radius'=>999,'layout_class'=>'hotel-reserve','shadow'=>'yes'))
        );
    }

    public static function footers() {
        return array(
            'modern-saas'=>array('title'=>'SaaS Gradient Footer','desc'=>'Essentials yaklaşımından esinlenen; büyük CTA, ürün linkleri, newsletter ve modern koyu gradient düzen.','preview'=>'footer-modern-saas.svg','sections'=>4,'blocks'=>array(array('type'=>'logo','section'=>1),array('type'=>'text','section'=>1,'text'=>'Ürününüzü daha hızlı büyüten modern dijital deneyimler.'),array('type'=>'menu','section'=>2),array('type'=>'html','section'=>3,'html'=>'<strong>Ürün</strong><br>Özellikler<br>Entegrasyonlar<br>Fiyatlandırma'),array('type'=>'social','section'=>4),array('type'=>'copyright','section'=>1)),'settings'=>array('bg'=>'#0b1020','text'=>'#e8eefc','button_bg'=>'#7c3aed','layout_class'=>'modern-saas')),
            'agency-bento'=>array('title'=>'Agency Bento Footer','desc'=>'Ajans ve kreatif stüdyolar için büyük mesaj, bento link kartları ve sosyal alan.','preview'=>'footer-agency-bento.svg','sections'=>3,'blocks'=>array(array('type'=>'html','section'=>1,'html'=>'<strong style="font-size:28px">Bir sonraki güçlü fikri birlikte üretelim.</strong>'),array('type'=>'menu','section'=>2),array('type'=>'button','section'=>3,'text'=>'Projeyi Konuşalım','url'=>'#iletisim'),array('type'=>'social','section'=>3),array('type'=>'copyright','section'=>1)),'settings'=>array('bg'=>'#f6f4ff','text'=>'#161326','button_bg'=>'#6d5dfc','layout_class'=>'agency-bento')),
            'corporate-pro'=>array('title'=>'Corporate Pro Footer','desc'=>'Kurumsal siteler için marka, çözüm kolonları, iletişim ve güvenli alt bar.','preview'=>'footer-corporate-pro.svg','sections'=>4,'blocks'=>array(array('type'=>'logo','section'=>1),array('type'=>'text','section'=>1,'text'=>'Sürdürülebilir büyüme için teknoloji ve danışmanlık çözümleri.'),array('type'=>'menu','section'=>2),array('type'=>'html','section'=>3,'html'=>'<strong>Çözümler</strong><br>Kurumsal Web<br>E-Ticaret<br>SEO & Performans'),array('type'=>'html','section'=>4,'html'=>'<strong>İletişim</strong><br>info@firma.com<br>+90 212 000 00 00'),array('type'=>'copyright','section'=>1)),'settings'=>array('bg'=>'#ffffff','text'=>'#1e293b','button_bg'=>'#0f4cdd','layout_class'=>'corporate-pro')),
            'commerce-premium'=>array('title'=>'Commerce Premium Footer','desc'=>'E-ticaret siteleri için newsletter, kategori linkleri, destek alanı ve ödeme güven şeridi.','preview'=>'footer-commerce-premium.svg','sections'=>4,'blocks'=>array(array('type'=>'logo','section'=>1),array('type'=>'menu','section'=>2),array('type'=>'html','section'=>3,'html'=>'<strong>Müşteri</strong><br>Sipariş Takibi<br>İade & Değişim<br>SSS'),array('type'=>'html','section'=>4,'html'=>'<strong>Destek</strong><br>Hafta içi 09:00–18:00<br>destek@magaza.com'),array('type'=>'copyright','section'=>1)),'settings'=>array('bg'=>'#111111','text'=>'#f5f5f5','button_bg'=>'#f97316','layout_class'=>'commerce-premium')),
            'hotel-luxury'=>array('title'=>'Luxury Hotel Footer','desc'=>'Otel ve turizm siteleri için rezervasyon CTA, adres, sosyal ve zarif serif tipografi hissi.','preview'=>'footer-hotel-luxury.svg','sections'=>3,'blocks'=>array(array('type'=>'logo','section'=>1),array('type'=>'html','section'=>1,'html'=>'<strong>Unutulmaz bir konaklama deneyimi.</strong><br>Sahil Yolu No: 1, Antalya'),array('type'=>'menu','section'=>2),array('type'=>'button','section'=>3,'text'=>'Rezervasyon Yap','url'=>'#rezervasyon'),array('type'=>'social','section'=>3),array('type'=>'copyright','section'=>1)),'settings'=>array('bg'=>'#13251f','text'=>'#f2eadf','button_bg'=>'#c8a96b','layout_class'=>'hotel-luxury')),
            'industry-modern'=>array('title'=>'Industry Modern Footer','desc'=>'Makina ve sanayi firmaları için teknik linkler, servis CTA ve güçlü koyu tasarım.','preview'=>'footer-industry-modern.svg','sections'=>4,'blocks'=>array(array('type'=>'logo','section'=>1),array('type'=>'text','section'=>1,'text'=>'Endüstriyel üretimde güvenilir teknoloji ve servis çözümleri.'),array('type'=>'html','section'=>2,'html'=>'<strong>Ürünler</strong><br>CNC Tezgahları<br>Taşlama<br>Erozyon'),array('type'=>'html','section'=>3,'html'=>'<strong>Destek</strong><br>Teknik Servis<br>Yedek Parça<br>Dokümanlar'),array('type'=>'button','section'=>4,'text'=>'Servis Talebi','url'=>'#servis'),array('type'=>'copyright','section'=>1)),'settings'=>array('bg'=>'#101820','text'=>'#e7edf2','button_bg'=>'#efb321','layout_class'=>'industry-modern')),
            'medical-clean'=>array('title'=>'Medical Clean Footer','desc'=>'Sağlık ve klinik siteleri için temiz, güven veren iletişim ve randevu odaklı footer.','preview'=>'footer-medical-clean.svg','sections'=>4,'blocks'=>array(array('type'=>'logo','section'=>1),array('type'=>'text','section'=>1,'text'=>'Sağlığınız için güvenilir, erişilebilir ve modern hizmet.'),array('type'=>'menu','section'=>2),array('type'=>'html','section'=>3,'html'=>'<strong>Çalışma Saatleri</strong><br>Pzt–Cmt 09:00–19:00<br>Pazar Kapalı'),array('type'=>'button','section'=>4,'text'=>'Randevu Al','url'=>'#randevu'),array('type'=>'copyright','section'=>1)),'settings'=>array('bg'=>'#f4fbfa','text'=>'#183b3b','button_bg'=>'#0f9f8f','layout_class'=>'medical-clean')),
            'restaurant-editorial'=>array('title'=>'Restaurant Editorial Footer','desc'=>'Restoran ve kafe siteleri için rezervasyon, çalışma saatleri ve editorial düzen.','preview'=>'footer-restaurant-editorial.svg','sections'=>3,'blocks'=>array(array('type'=>'logo','section'=>1),array('type'=>'html','section'=>1,'html'=>'<strong>Lezzet, atmosfer ve iyi anılar.</strong>'),array('type'=>'html','section'=>2,'html'=>'<strong>Saatler</strong><br>Her gün 10:00–00:00<br><br><strong>Adres</strong><br>Merkez, İstanbul'),array('type'=>'button','section'=>3,'text'=>'Masa Ayırt','url'=>'#rezervasyon'),array('type'=>'social','section'=>3),array('type'=>'copyright','section'=>1)),'settings'=>array('bg'=>'#261c18','text'=>'#f7efe6','button_bg'=>'#d68b5d','layout_class'=>'restaurant-editorial')),
            'portfolio-creative'=>array('title'=>'Portfolio Creative Footer','desc'=>'Freelancer ve portföy siteleri için dev tipografi, minimal bağlantılar ve sosyal CTA.','preview'=>'footer-portfolio-creative.svg','sections'=>2,'blocks'=>array(array('type'=>'html','section'=>1,'html'=>'<strong style="font-size:32px">BİRLİKTE HARİKA BİR ŞEY ÜRETELİM.</strong>'),array('type'=>'button','section'=>2,'text'=>'Merhaba De','url'=>'mailto:hello@example.com'),array('type'=>'social','section'=>2),array('type'=>'copyright','section'=>1)),'settings'=>array('bg'=>'#f5ff68','text'=>'#111111','button_bg'=>'#111111','layout_class'=>'portfolio-creative')),
            'minimal-line'=>array('title'=>'Minimal Line Footer','desc'=>'Çok sade siteler için tek çizgili marka, navigasyon ve telif düzeni.','preview'=>'footer-minimal-line.svg','sections'=>3,'blocks'=>array(array('type'=>'logo','section'=>1),array('type'=>'menu','section'=>2),array('type'=>'social','section'=>3),array('type'=>'copyright','section'=>1)),'settings'=>array('bg'=>'#ffffff','text'=>'#18181b','button_bg'=>'#18181b','layout_class'=>'minimal-line')),
            'newsletter-glass'=>array('title'=>'Newsletter Glass Footer','desc'=>'Glass kart içinde büyük newsletter CTA ve altta kompakt marka/link düzeni.','preview'=>'footer-newsletter-glass.svg','sections'=>3,'blocks'=>array(array('type'=>'html','section'=>1,'html'=>'<strong style="font-size:26px">Gelişmeleri doğrudan gelen kutunuza alın.</strong>'),array('type'=>'button','section'=>3,'text'=>'Bültene Katıl','url'=>'#bulten'),array('type'=>'logo','section'=>1),array('type'=>'menu','section'=>2),array('type'=>'copyright','section'=>1)),'settings'=>array('bg'=>'#eaf2ff','text'=>'#13213a','button_bg'=>'#315efb','layout_class'=>'newsletter-glass')),
            'finance-trust'=>array('title'=>'Finance Trust Footer','desc'=>'Finans, danışmanlık ve fintech siteleri için güven, mevzuat linkleri ve premium koyu lacivert düzen.','preview'=>'footer-finance-trust.svg','sections'=>4,'blocks'=>array(array('type'=>'logo','section'=>1),array('type'=>'text','section'=>1,'text'=>'Finansal kararlarınız için şeffaf, güvenilir ve sürdürülebilir çözümler.'),array('type'=>'menu','section'=>2),array('type'=>'html','section'=>3,'html'=>'<strong>Kurumsal</strong><br>Yatırımcı İlişkileri<br>Raporlar<br>Gizlilik'),array('type'=>'button','section'=>4,'text'=>'Danışmanlık Al','url'=>'#iletisim'),array('type'=>'copyright','section'=>1)),'settings'=>array('bg'=>'#071a2b','text'=>'#eef5f7','button_bg'=>'#c8a55a','layout_class'=>'finance-trust')),
            'construction-bold'=>array('title'=>'Construction Bold Footer','desc'=>'İnşaat, mimarlık ve taahhüt firmaları için proje, yetkinlik ve teklif odaklı güçlü footer.','preview'=>'footer-construction-bold.svg','sections'=>4,'blocks'=>array(array('type'=>'logo','section'=>1),array('type'=>'text','section'=>1,'text'=>'Güvenli, nitelikli ve sürdürülebilir yapılar inşa ediyoruz.'),array('type'=>'html','section'=>2,'html'=>'<strong>Projeler</strong><br>Konut<br>Ticari<br>Endüstriyel'),array('type'=>'html','section'=>3,'html'=>'<strong>Kurumsal</strong><br>Hakkımızda<br>Kalite Politikası<br>Belgeler'),array('type'=>'button','section'=>4,'text'=>'Teklif İste','url'=>'#teklif'),array('type'=>'copyright','section'=>1)),'settings'=>array('bg'=>'#171717','text'=>'#f5f5f4','button_bg'=>'#f59e0b','layout_class'=>'construction-bold')),
            'photography-canvas'=>array('title'=>'Photography Canvas Footer','desc'=>'Fotoğrafçı ve yaratıcı stüdyolar için büyük mesaj, sosyal odak ve sade portföy kapanışı.','preview'=>'footer-photography-canvas.svg','sections'=>2,'blocks'=>array(array('type'=>'html','section'=>1,'html'=>'<strong style="font-size:32px">HİKÂYENİZİ BİRLİKTE KADRAJA ALALIM.</strong>'),array('type'=>'button','section'=>2,'text'=>'Çekim Planla','url'=>'#iletisim'),array('type'=>'social','section'=>2),array('type'=>'copyright','section'=>1)),'settings'=>array('bg'=>'#f2eee8','text'=>'#151515','button_bg'=>'#151515','layout_class'=>'photography-canvas')),
            'blog-editorial'=>array('title'=>'Blog Editorial Footer','desc'=>'Blog, dergi ve içerik siteleri için editorial tipografi, kategori linkleri ve güçlü bülten alanı.','preview'=>'footer-blog-editorial.svg','sections'=>4,'blocks'=>array(array('type'=>'logo','section'=>1),array('type'=>'menu','section'=>2),array('type'=>'html','section'=>3,'html'=>'<strong>Kategoriler</strong><br>Gündem<br>Rehber<br>İlham'),array('type'=>'social','section'=>4),array('type'=>'copyright','section'=>1)),'settings'=>array('bg'=>'#fbf8f2','text'=>'#2b2926','button_bg'=>'#b64b35','layout_class'=>'blog-editorial')),
            'app-gradient'=>array('title'=>'App Launch Footer','desc'=>'Mobil uygulama ve startup landing page siteleri için gradient CTA, mağaza linkleri ve bülten alanı.','preview'=>'footer-app-gradient.svg','sections'=>4,'blocks'=>array(array('type'=>'logo','section'=>1),array('type'=>'text','section'=>1,'text'=>'Uygulamayı keşfedin, işlerinizi daha hızlı ve kolay yönetin.'),array('type'=>'menu','section'=>2),array('type'=>'html','section'=>3,'html'=>'<strong>İndir</strong><br>App Store<br>Google Play<br>Web Uygulaması'),array('type'=>'social','section'=>4),array('type'=>'copyright','section'=>1)),'settings'=>array('bg'=>'#0c1024','text'=>'#f3f5ff','button_bg'=>'#7c5cff','layout_class'=>'app-gradient')),
            'seo-growth'=>array('title'=>'SEO Growth Footer','desc'=>'SEO, performans ve dijital pazarlama ajansları için lead odaklı modern growth footer.','preview'=>'footer-seo-growth.svg','sections'=>4,'blocks'=>array(array('type'=>'logo','section'=>1),array('type'=>'text','section'=>1,'text'=>'Aramada görünürlüğünüzü ve dijital büyümenizi birlikte hızlandıralım.'),array('type'=>'menu','section'=>2),array('type'=>'html','section'=>3,'html'=>'<strong>Kaynaklar</strong><br>SEO Rehberi<br>Vaka Analizleri<br>Blog'),array('type'=>'button','section'=>4,'text'=>'Ücretsiz Analiz','url'=>'#analiz'),array('type'=>'copyright','section'=>1)),'settings'=>array('bg'=>'#f4f0ff','text'=>'#211a35','button_bg'=>'#6d42e8','layout_class'=>'seo-growth')),
            'legal-elegant'=>array('title'=>'Legal Elegant Footer','desc'=>'Hukuk bürosu ve danışmanlık firmaları için sade, ciddi ve premium serif hissiyatlı footer.','preview'=>'footer-legal-elegant.svg','sections'=>4,'blocks'=>array(array('type'=>'logo','section'=>1),array('type'=>'text','section'=>1,'text'=>'Hukuki süreçlerde güvenilir, şeffaf ve çözüm odaklı danışmanlık.'),array('type'=>'menu','section'=>2),array('type'=>'html','section'=>3,'html'=>'<strong>Çalışma Alanları</strong><br>Ticaret Hukuku<br>İş Hukuku<br>Uyuşmazlık'),array('type'=>'button','section'=>4,'text'=>'Görüşme Talep Et','url'=>'#iletisim'),array('type'=>'copyright','section'=>1)),'settings'=>array('bg'=>'#f7f4ee','text'=>'#2d2924','button_bg'=>'#5b4636','layout_class'=>'legal-elegant')),
            'education-friendly'=>array('title'=>'Education Friendly Footer','desc'=>'Okul, kurs ve eğitim platformları için programlar, öğrenci bağlantıları ve başvuru CTA içeren sıcak footer.','preview'=>'footer-education-friendly.svg','sections'=>4,'blocks'=>array(array('type'=>'logo','section'=>1),array('type'=>'text','section'=>1,'text'=>'Öğrenmeyi erişilebilir, ilham verici ve geleceğe dönük hale getiriyoruz.'),array('type'=>'menu','section'=>2),array('type'=>'html','section'=>3,'html'=>'<strong>Öğrenci</strong><br>Programlar<br>Takvim<br>Sık Sorulanlar'),array('type'=>'button','section'=>4,'text'=>'Başvuru Yap','url'=>'#basvuru'),array('type'=>'copyright','section'=>1)),'settings'=>array('bg'=>'#eef7ff','text'=>'#17324d','button_bg'=>'#2878d0','layout_class'=>'education-friendly')),
            'realestate-luxury'=>array('title'=>'Real Estate Luxury Footer','desc'=>'Gayrimenkul, villa ve emlak projeleri için premium portföy, iletişim ve randevu odaklı footer.','preview'=>'footer-realestate-luxury.svg','sections'=>4,'blocks'=>array(array('type'=>'logo','section'=>1),array('type'=>'text','section'=>1,'text'=>'Seçkin yaşam alanlarını doğru yatırım fırsatlarıyla buluşturuyoruz.'),array('type'=>'menu','section'=>2),array('type'=>'html','section'=>3,'html'=>'<strong>Portföy</strong><br>Satılık<br>Kiralık<br>Yeni Projeler'),array('type'=>'button','section'=>4,'text'=>'Portföyü Gör','url'=>'#portfoy'),array('type'=>'copyright','section'=>1)),'settings'=>array('bg'=>'#102620','text'=>'#f1eee7','button_bg'=>'#c5a46d','layout_class'=>'realestate-luxury')),
                        'boxed-cloud'=>array('title'=>'Boxed Cloud Footer','desc'=>'Sayfa kenarlarından ayrılan, 30px oval köşeli açık renk premium boxed footer.','preview'=>'footer-boxed-cloud.svg','sections'=>4,'blocks'=>array(array('type'=>'logo','section'=>1),array('type'=>'text','section'=>1,'text'=>'Dijital ürünler için sade, hızlı ve güven veren deneyimler.'),array('type'=>'menu','section'=>2),array('type'=>'html','section'=>3,'html'=>'<strong>Çözümler</strong><br>Web Tasarım<br>Ürün Tasarımı<br>Geliştirme'),array('type'=>'button','section'=>4,'text'=>'Projeyi Başlat','url'=>'#iletisim'),array('type'=>'copyright','section'=>1)),'settings'=>array('bg'=>'#edf4ff','text'=>'#15233c','button_bg'=>'#315efb','layout_class'=>'boxed-cloud','boxed'=>'yes','radius'=>30,'max_width'=>1280)),
            'boxed-midnight'=>array('title'=>'Boxed Midnight Footer','desc'=>'Koyu lacivert yüzey, oval dış çerçeve, büyük CTA ve kompakt link kolonları.','preview'=>'footer-boxed-midnight.svg','sections'=>4,'blocks'=>array(array('type'=>'logo','section'=>1),array('type'=>'text','section'=>1,'text'=>'Yeni nesil ürünler için strateji, tasarım ve teknoloji.'),array('type'=>'menu','section'=>2),array('type'=>'html','section'=>3,'html'=>'<strong>Platform</strong><br>Ürünler<br>Çözümler<br>Kaynaklar'),array('type'=>'social','section'=>4),array('type'=>'copyright','section'=>1)),'settings'=>array('bg'=>'#091426','text'=>'#eef4ff','button_bg'=>'#7c6cff','layout_class'=>'boxed-midnight','boxed'=>'yes','radius'=>34,'max_width'=>1240)),
            'boxed-editorial'=>array('title'=>'Boxed Editorial Footer','desc'=>'Krem yüzeyli, asimetrik editorial tipografi ve geniş oval köşeli boxed footer.','preview'=>'footer-boxed-editorial.svg','sections'=>3,'blocks'=>array(array('type'=>'html','section'=>1,'html'=>'<strong>İyi fikirlerin iyi bir kapanışı hak ettiğine inanıyoruz.</strong>'),array('type'=>'menu','section'=>2),array('type'=>'button','section'=>3,'text'=>'Birlikte Çalışalım','url'=>'#iletisim'),array('type'=>'social','section'=>3),array('type'=>'copyright','section'=>1)),'settings'=>array('bg'=>'#f4efe6','text'=>'#28231e','button_bg'=>'#9a5d3f','layout_class'=>'boxed-editorial','boxed'=>'yes','radius'=>38,'max_width'=>1180)),
            'boxed-sage'=>array('title'=>'Boxed Sage Footer','desc'=>'Doğal yeşil tonlar, newsletter odaklı yapı ve yumuşak 32px köşeli boxed footer.','preview'=>'footer-boxed-sage.svg','sections'=>3,'blocks'=>array(array('type'=>'logo','section'=>1),array('type'=>'text','section'=>1,'text'=>'Daha sakin, daha sürdürülebilir ve daha anlaşılır dijital deneyimler.'),array('type'=>'menu','section'=>2),array('type'=>'button','section'=>3,'text'=>'Bültene Katıl','url'=>'#bulten'),array('type'=>'copyright','section'=>1)),'settings'=>array('bg'=>'#eaf2e8','text'=>'#203126','button_bg'=>'#426a4b','layout_class'=>'boxed-sage','boxed'=>'yes','radius'=>32,'max_width'=>1220)),
'dark-gradient'=>array('title'=>'Dark Gradient Footer','desc'=>'Teknoloji, yapay zekâ ve startup siteleri için gradient yüzeyli premium koyu footer.','preview'=>'footer-dark-gradient.svg','sections'=>4,'blocks'=>array(array('type'=>'logo','section'=>1),array('type'=>'text','section'=>1,'text'=>'Yeni nesil dijital ürünler için ölçeklenebilir altyapı.'),array('type'=>'menu','section'=>2),array('type'=>'html','section'=>3,'html'=>'<strong>Kaynaklar</strong><br>Dokümantasyon<br>Blog<br>Changelog'),array('type'=>'social','section'=>4),array('type'=>'copyright','section'=>1)),'settings'=>array('bg'=>'#080b18','text'=>'#e9ecff','button_bg'=>'#7557ff','layout_class'=>'dark-gradient')),
            'corporate'=>array('title'=>'Kurumsal Footer','desc'=>'Logo, menü, iletişim ve sosyal alanlar.','preview'=>'footer-corporate.svg','sections'=>4,'blocks'=>array(array('type'=>'logo','section'=>1),array('type'=>'text','section'=>1,'text'=>'Modern dijital çözümlerle işletmenizi büyütün.'),array('type'=>'menu','section'=>2),array('type'=>'html','section'=>3,'html'=>'<strong>İletişim</strong><br>info@firma.com<br>0212 000 00 00'),array('type'=>'social','section'=>4),array('type'=>'copyright','section'=>1)),'settings'=>array('bg'=>'#ffffff','text'=>'#334155')),
            'dark'=>array('title'=>'Koyu Footer','desc'=>'Ajans ve teknoloji siteleri için koyu footer.','preview'=>'footer-dark.svg','sections'=>4,'blocks'=>array(array('type'=>'logo','section'=>1),array('type'=>'text','section'=>1,'text'=>'Dijitalde güçlü bir marka deneyimi oluşturun.'),array('type'=>'menu','section'=>2),array('type'=>'html','section'=>3,'html'=>'<strong>İletişim</strong><br>info@firma.com<br>0212 000 00 00'),array('type'=>'social','section'=>4),array('type'=>'copyright','section'=>1)),'settings'=>array('bg'=>'#0f172a','text'=>'#e2e8f0')),
            'cta'=>array('title'=>'CTA Footer','desc'=>'Üst teklif çağrısı + sade footer.','preview'=>'footer-cta.svg','sections'=>3,'blocks'=>array(array('type'=>'html','section'=>1,'html'=>'<strong style="font-size:22px">Yeni projeniz için hazır mısınız?</strong>'),array('type'=>'button','section'=>3,'text'=>'Teklif Al','url'=>'#iletisim'),array('type'=>'logo','section'=>1),array('type'=>'menu','section'=>2),array('type'=>'copyright','section'=>1)),'settings'=>array('bg'=>'#ffffff','text'=>'#0f172a','button_bg'=>'#2563eb')),
            'minimal'=>array('title'=>'Minimal Footer','desc'=>'Logo + kısa metin + sosyal alan.','preview'=>'footer-minimal.svg','sections'=>2,'blocks'=>array(array('type'=>'logo','section'=>1),array('type'=>'text','section'=>1,'text'=>'Modern web çözümleri.'),array('type'=>'social','section'=>2),array('type'=>'copyright','section'=>1)),'settings'=>array('bg'=>'#ffffff','text'=>'#475569')),
            'dark-minimal'=>array('title'=>'Koyu Minimal Footer','desc'=>'Temiz, koyu ve sade footer.','preview'=>'footer-dark-minimal.svg','sections'=>2,'blocks'=>array(array('type'=>'logo','section'=>1),array('type'=>'social','section'=>2),array('type'=>'copyright','section'=>1)),'settings'=>array('bg'=>'#0f172a','text'=>'#cbd5e1')),

            'newsletter'=>array('title'=>'Newsletter Footer','desc'=>'Üstte e-posta kayıt CTA, altta sade footer.','preview'=>'footer-newsletter.svg','sections'=>3,'blocks'=>array(array('type'=>'html','section'=>1,'html'=>'<div class="wpst-newsletter"><strong>Yeni içerikleri kaçırmayın</strong><span>E-posta bültenimize katılın.</span></div>'),array('type'=>'button','section'=>3,'text'=>'Abone Ol','url'=>'#'),array('type'=>'logo','section'=>1),array('type'=>'menu','section'=>2),array('type'=>'copyright','section'=>1)),'settings'=>array('bg'=>'#ffffff','text'=>'#0f172a','button_bg'=>'#2563eb','layout_class'=>'newsletter')),
            'mega'=>array('title'=>'Mega Footer','desc'=>'Kurumsal ve büyük siteler için geniş 4 kolonlu footer.','preview'=>'footer-mega.svg','sections'=>4,'blocks'=>array(array('type'=>'logo','section'=>1),array('type'=>'text','section'=>1,'text'=>'Markanızı dijitalde güçlü biçimde temsil eden çözümler.'),array('type'=>'menu','section'=>2),array('type'=>'html','section'=>3,'html'=>'<strong>Hizmetler</strong><br>Web Tasarım<br>E-Ticaret<br>SEO<br>Bakım & Destek'),array('type'=>'html','section'=>4,'html'=>'<strong>İletişim</strong><br>info@firma.com<br>0212 000 00 00'),array('type'=>'social','section'=>4),array('type'=>'copyright','section'=>1)),'settings'=>array('bg'=>'#0b1220','text'=>'#e2e8f0','layout_class'=>'mega')),
            'split'=>array('title'=>'Split Footer','desc'=>'Sol tarafta marka mesajı, sağda menü ve CTA.','preview'=>'footer-split.svg','sections'=>3,'blocks'=>array(array('type'=>'logo','section'=>1),array('type'=>'html','section'=>1,'html'=>'<strong style="font-size:24px">Dijitalde daha güçlü bir marka oluşturun.</strong>'),array('type'=>'menu','section'=>2),array('type'=>'button','section'=>3,'text'=>'Bize Ulaşın','url'=>'#iletisim'),array('type'=>'copyright','section'=>1)),'settings'=>array('bg'=>'#ffffff','text'=>'#0f172a','button_bg'=>'#0f766e','layout_class'=>'split')),
            'centered'=>array('title'=>'Centered Minimal Footer','desc'=>'Ortalanmış logo, kısa metin ve sosyal ikonlar.','preview'=>'footer-centered.svg','sections'=>1,'blocks'=>array(array('type'=>'logo','section'=>1),array('type'=>'text','section'=>1,'text'=>'Modern, hızlı ve kullanıcı odaklı dijital çözümler.'),array('type'=>'social','section'=>1),array('type'=>'copyright','section'=>1)),'settings'=>array('bg'=>'#ffffff','text'=>'#475569','layout_class'=>'centered'))
        );
    }

    public static function menu() {
        add_submenu_page(
            'wpsoft-site-tools',
            'Şablonlarım',
            'Şablonlarım',
            'manage_options',
            'wpsoft-my-templates',
            array( __CLASS__, 'my_templates_page' )
        );
    }


    public static function my_templates_page() {
        if ( ! current_user_can( 'manage_options' ) ) return;

        $view = isset($_GET['view']) ? sanitize_key(wp_unslash($_GET['view'])) : 'list';
        $type_filter = isset($_GET['type']) ? sanitize_key(wp_unslash($_GET['type'])) : 'all';
        $favorite_only = !empty($_GET['favorite']);
        $search_term = isset($_GET['s']) ? sanitize_text_field(wp_unslash($_GET['s'])) : '';

        if ( 'new' === $view ) {
            self::new_template_page();
            return;
        }
        if ( 'conditions' === $view ) {
            self::conditions_page();
            return;
        }

        $items = get_posts( array(
            'post_type'      => 'elementor_library',
            'post_status'    => array( 'publish', 'draft', 'private' ),
            'posts_per_page' => -1,
            'orderby'        => 'modified',
            'order'          => 'DESC',
            'meta_query'     => array(
                'relation' => 'OR',
                array( 'key' => '_wpst_hf_template', 'value' => '1' ),
                array( 'key' => '_wpst_menu_template', 'value' => '1' ),
                array( 'key' => '_wpst_blog_library_template', 'value' => '1' ),
            ),
        ) );

        if($favorite_only){
            $items=array_values(array_filter($items,function($p){return '1'===get_post_meta($p->ID,'_wpst_template_favorite',true);}));
        }
        if($search_term){
            $items=array_values(array_filter($items,function($p)use($search_term){return false!==stripos($p->post_title,$search_term);}));
        }

        $new_url = admin_url('admin.php?page=wpsoft-my-templates&view=new');
        echo '<div class="wrap wpst-my-templates">';
        echo '<div class="wpst-mt-head"><div><span class="wpst-mt-kicker">WPSOFT SITE TOOLS</span><h1>Şablonlarım</h1><p>Şablonlarınızı arayın, favorileyin, kopyalayın, dışa aktarın ve gösterim koşullarını yönetin.</p></div><a class="button button-primary button-hero" href="'.esc_url($new_url).'">+ Yeni Ekle</a></div>';
        echo '<div class="wpst-mt-tools"><form method="get"><input type="hidden" name="page" value="wpsoft-my-templates"><input type="search" name="s" value="'.esc_attr($search_term).'" placeholder="Şablonlarda ara..."><button class="button">Ara</button></form><a class="button '.($favorite_only?'button-primary':'').'" href="'.esc_url(admin_url('admin.php?page=wpsoft-my-templates&favorite='.($favorite_only?'0':'1'))).'">★ Favoriler</a></div>';

        if(isset($_GET['deleted'])) echo '<div class="notice notice-success is-dismissible"><p>Şablon silindi.</p></div>';

        $counts=array('all'=>0,'header'=>0,'footer'=>0,'mega'=>0,'blog_archive'=>0,'blog_single'=>0);
        foreach($items as $it){
            if('1'===get_post_meta($it->ID,'_wpst_blog_library_template',true)){
                $bt=get_post_meta($it->ID,'_wpst_blog_template_type',true);
                $t=('single'===$bt)?'blog_single':'blog_archive';
            }else{
                $t=('1'===get_post_meta($it->ID,'_wpst_menu_template',true))?'mega':get_post_meta($it->ID,'_wpst_hf_type',true);
            }
            if(!isset($counts[$t])) continue;
            $counts['all']++;$counts[$t]++;
        }

        echo '<div class="wpst-mt-tabs">';
        foreach(array('all'=>'Tümü','header'=>'Header','footer'=>'Footer','mega'=>'Mega Menü','blog_archive'=>'Blog Arşiv','blog_single'=>'Tek Yazı') as $k=>$label){
            $url=admin_url('admin.php?page=wpsoft-my-templates&type='.$k);
            echo '<a class="'.($type_filter===$k?'is-active':'').'" href="'.esc_url($url).'">'.esc_html($label).'<span>'.absint($counts[$k]).'</span></a>';
        }
        echo '</div>';

        $shown=0;
        echo '<div class="wpst-mt-grid">';
        foreach ( $items as $post ) {
            $is_mega = '1' === get_post_meta($post->ID,'_wpst_menu_template',true);
            $is_blog = '1' === get_post_meta($post->ID,'_wpst_blog_library_template',true);
            if($is_blog){
                $blog_type=get_post_meta($post->ID,'_wpst_blog_template_type',true);
                $type=('single'===$blog_type)?'blog_single':'blog_archive';
            }else{
                $type=$is_mega?'mega':get_post_meta($post->ID,'_wpst_hf_type',true);
            }
            if(!in_array($type,array('header','footer','mega','blog_archive','blog_single'),true)) continue;
            if('all'!==$type_filter && $type_filter!==$type) continue;
            $shown++;

            $preview='';
            if($is_blog){
                $blog_preview=get_post_meta($post->ID,'_wpst_blog_template_preview',true);
                if($blog_preview) $preview=WPST_URL.'assets/images/templates/'.$blog_preview;
            }elseif(!$is_mega){
                $key=get_post_meta($post->ID,'_wpst_hf_key',true);
                $source=('footer'===$type)?self::footers():self::headers();
                if($key && isset($source[$key]['preview'])) $preview=WPST_URL.'assets/images/header-footer/'.$source[$key]['preview'];
            }
            if($is_mega) $preview=WPST_URL.'assets/images/demo/corporate.svg';

            $edit=admin_url('post.php?post='.$post->ID.'&action=elementor');
            $delete=wp_nonce_url(admin_url('admin-post.php?action=wpst_delete_my_template&template_id='.$post->ID),'wpst_delete_my_template_'.$post->ID);
            $duplicate=wp_nonce_url(admin_url('admin-post.php?action=wpst_duplicate_my_template&template_id='.$post->ID),'wpst_duplicate_my_template_'.$post->ID);
            $favorite=wp_nonce_url(admin_url('admin-post.php?action=wpst_toggle_template_favorite&template_id='.$post->ID),'wpst_toggle_template_favorite_'.$post->ID);
            $export=wp_nonce_url(admin_url('admin-post.php?action=wpst_export_my_template&template_id='.$post->ID),'wpst_export_my_template_'.$post->ID);
            $conditions=admin_url('admin.php?page=wpsoft-my-templates&view=conditions&template_id='.$post->ID);
            $is_favorite='1'===get_post_meta($post->ID,'_wpst_template_favorite',true);
            $rules=get_post_meta($post->ID,'_wpst_display_conditions',true);
            $rule_count=is_array($rules)?count($rules):0;
            $label=$type==='mega'?'MEGA MENÜ':($type==='blog_archive'?'BLOG ARŞİV':($type==='blog_single'?'TEK YAZI':strtoupper($type)));

            echo '<article class="wpst-mt-card">';
            echo '<div class="wpst-mt-preview '.esc_attr('is-'.$type).'">';
            if($preview) echo '<img src="'.esc_url($preview).'" alt="">';
            else echo '<div class="wpst-mt-fallback">W</div>';
            echo '<span>'.esc_html($label).'</span></div>';
            echo '<div class="wpst-mt-body"><div class="wpst-mt-titleline"><h2>'.esc_html($post->post_title).'</h2><a class="wpst-mt-star '.($is_favorite?'is-active':'').'" href="'.esc_url($favorite).'" title="Favori">★</a></div><p>Son düzenleme: '.esc_html(get_the_modified_date('d.m.Y H:i',$post)).' · ID #'.absint($post->ID).'</p><div class="wpst-mt-meta"><span>'.($rule_count?absint($rule_count).' gösterim koşulu':'Koşul yok').'</span><span>'.esc_html(ucfirst($post->post_status)).'</span></div>';
            echo '<div class="wpst-mt-actions"><a class="button button-primary" href="'.esc_url($edit).'">Elementor ile Düzenle</a><a class="button" href="'.esc_url($conditions).'">Gösterim Koşulları</a><a class="button" href="'.esc_url($duplicate).'">Kopyala</a><a class="button" href="'.esc_url($export).'">Dışa Aktar</a><a class="button wpst-mt-delete" onclick="return confirm(\'Bu şablon silinsin mi?\')" href="'.esc_url($delete).'">Sil</a></div>';
            echo '</div></article>';
        }
        if(!$shown){
            echo '<div class="wpst-mt-empty"><strong>Bu kategoride henüz şablon yok.</strong><p>“Yeni Ekle” ile Header, Footer, Mega Menü, Blog Arşiv veya Tek Yazı şablonu oluşturabilirsiniz.</p><a class="button button-primary" href="'.esc_url($new_url).'">Yeni Şablon Oluştur</a></div>';
        }
        echo '</div></div>';
    }

    public static function duplicate_my_template(){
        if(!current_user_can('manage_options')) wp_die('Yetkiniz yok.');
        $id=isset($_GET['template_id'])?absint($_GET['template_id']):0;
        check_admin_referer('wpst_duplicate_my_template_'.$id);
        $post=get_post($id);
        if(!$post || 'elementor_library'!==$post->post_type) wp_die('Şablon bulunamadı.');
        $new=wp_insert_post(array('post_title'=>$post->post_title.' - Kopya','post_type'=>'elementor_library','post_status'=>'publish'),true);
        if(is_wp_error($new)) wp_die($new->get_error_message());
        foreach(get_post_meta($id) as $key=>$values){
            foreach($values as $value) add_post_meta($new,$key,maybe_unserialize($value));
        }
        delete_post_meta($new,'_wpst_template_favorite');
        wp_safe_redirect(admin_url('admin.php?page=wpsoft-my-templates'));exit;
    }

    public static function toggle_template_favorite(){
        if(!current_user_can('manage_options')) wp_die('Yetkiniz yok.');
        $id=isset($_GET['template_id'])?absint($_GET['template_id']):0;
        check_admin_referer('wpst_toggle_template_favorite_'.$id);
        update_post_meta($id,'_wpst_template_favorite','1'===get_post_meta($id,'_wpst_template_favorite',true)?'0':'1');
        wp_safe_redirect(wp_get_referer()?wp_get_referer():admin_url('admin.php?page=wpsoft-my-templates'));exit;
    }

    public static function export_my_template(){
        if(!current_user_can('manage_options')) wp_die('Yetkiniz yok.');
        $id=isset($_GET['template_id'])?absint($_GET['template_id']):0;
        check_admin_referer('wpst_export_my_template_'.$id);
        $post=get_post($id);
        if(!$post || 'elementor_library'!==$post->post_type) wp_die('Şablon bulunamadı.');
        $payload=array('format'=>'wpsoft-template','version'=>WPST_VERSION,'title'=>$post->post_title,'post_status'=>$post->post_status,'meta'=>array());
        foreach(get_post_meta($id) as $key=>$values){
            if('_edit_lock'===$key||'_edit_last'===$key) continue;
            $payload['meta'][$key]=array_map('maybe_unserialize',$values);
        }
        nocache_headers();
        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename="wpsoft-template-'.$id.'.json"');
        echo wp_json_encode($payload,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);exit;
    }

    /**
     * Existing WPSoft Header templates created before Navigation 2.0 may
     * contain Elementor's legacy "nav-menu" widget. Convert only WPSoft
     * managed Header templates, once per migration version.
     */
    public static function migrate_header_navigation_widgets(){
        if('1'===get_option('wpst_migration_header_widgets_13815','0')) return;
        if(!current_user_can('manage_options')) return;

        $ids=get_posts(array(
            'post_type'=>'elementor_library',
            'post_status'=>array('publish','draft','private'),
            'posts_per_page'=>-1,
            'fields'=>'ids',
            'no_found_rows'=>true,
            'meta_query'=>array(
                'relation'=>'AND',
                array('key'=>'_wpst_hf_template','value'=>'1'),
                array('key'=>'_wpst_hf_type','value'=>'header')
            )
        ));

        foreach((array)$ids as $id){
            $raw=get_post_meta($id,'_elementor_data',true);
            if(!$raw) continue;
            $data=json_decode($raw,true);
            if(!is_array($data)) continue;

            $changed=false;
            $data=self::replace_legacy_navigation_widgets($data,$changed);
            if($changed){
                update_post_meta($id,'_elementor_data',wp_slash(wp_json_encode($data)));
                // Elementor regenerates CSS after document data changes.
                delete_post_meta($id,'_elementor_css');
            }
        }

        update_option('wpst_migration_header_widgets_13815','1',false);
    }

    private static function replace_legacy_navigation_widgets($nodes,&$changed){
        if(!is_array($nodes)) return $nodes;

        foreach($nodes as $index=>$node){
            if(!is_array($node)) continue;

            if(isset($node['elType'],$node['widgetType']) && 'widget'===$node['elType'] && 'site-logo'===$node['widgetType']){
                $legacy_settings=isset($node['settings'])&&is_array($node['settings'])?$node['settings']:array();
                $legacy_width=isset($legacy_settings['width'])&&is_array($legacy_settings['width'])?$legacy_settings['width']:array('unit'=>'px','size'=>148);
                $node['widgetType']='wpsoft-site-logo';
                $node['settings']=array(
                    'source'=>'site',
                    'link_home'=>'yes',
                    'fallback_title'=>'yes',
                    'logo_width'=>$legacy_width,
                    'logo_width_tablet'=>array('unit'=>'px','size'=>132),
                    'logo_width_mobile'=>array('unit'=>'px','size'=>116),
                    'max_height'=>array('unit'=>'px','size'=>54),
                    'max_height_tablet'=>array('unit'=>'px','size'=>48),
                    'max_height_mobile'=>array('unit'=>'px','size'=>44),
                    'align'=>'flex-start'
                );
                $changed=true;
            }

            if(isset($node['elType'],$node['widgetType']) && 'widget'===$node['elType'] && 'nav-menu'===$node['widgetType']){
                $legacy_settings=isset($node['settings'])&&is_array($node['settings'])?$node['settings']:array();

                $node['widgetType']='wpsoft-navigation';
                $node['settings']=array(
                    'menu_id'=>'0',
                    'fallback'=>'first',
                    'submenu_indicator'=>'yes',
                    'aria_label'=>'Ana navigasyon',
                    'mobile_behavior'=>'inherit',
                    'align'=>'center',
                    'menu_preset'=>'modern',
                    'active_style'=>'pill',
                    'hover_motion'=>'lift',
                    'dropdown_shadow'=>'soft'
                );

                // Preserve a selected WordPress menu when the legacy widget stored one.
                foreach(array('menu','menu_id','nav_menu') as $legacy_key){
                    if(!empty($legacy_settings[$legacy_key])){
                        $node['settings']['menu_id']=(string)absint($legacy_settings[$legacy_key]);
                        break;
                    }
                }

                $changed=true;
            }

            if(!empty($node['elements']) && is_array($node['elements'])){
                $node['elements']=self::replace_legacy_navigation_widgets($node['elements'],$changed);
            }

            $nodes[$index]=$node;
        }

        return $nodes;
    }

    public static function save_template_conditions(){
        $id=isset($_POST['template_id'])?absint($_POST['template_id']):0;
        if(!$id) wp_die('Geçersiz şablon.');
        $post=get_post($id);
        if(!$post || 'elementor_library'!==$post->post_type) wp_die('Şablon bulunamadı.');
        if(!current_user_can('manage_options') && !current_user_can('edit_post',$id)) wp_die('Yetkiniz yok.');
        check_admin_referer('wpst_save_template_conditions_'.$id);

        $raw=isset($_POST['wpst_conditions'])?wp_unslash($_POST['wpst_conditions']):array();
        if(!is_array($raw)) $raw=array();

        // Removed rows can leave sparse/duplicated browser indexes. Rebuild a clean list.
        $normalized=array();
        foreach($raw as $rule){
            if(!is_array($rule) || empty($rule['type'])) continue;
            $normalized[]=array(
                'mode'=>isset($rule['mode'])?$rule['mode']:'include',
                'type'=>$rule['type'],
                'value'=>isset($rule['value'])?$rule['value']:'',
                'group'=>isset($rule['group'])?$rule['group']:1,
                'operator'=>isset($rule['operator'])?$rule['operator']:'or'
            );
        }

        $rules=class_exists('WPST_Display_Conditions')?WPST_Display_Conditions::sanitize($normalized):$normalized;
        update_post_meta($id,'_wpst_display_conditions',$rules);

        $priority=isset($_POST['wpst_condition_priority'])?max(1,min(999,absint($_POST['wpst_condition_priority']))):10;
        update_post_meta($id,'_wpst_condition_priority',$priority);

        wp_safe_redirect(add_query_arg(array(
            'page'=>'wpsoft-my-templates',
            'view'=>'conditions',
            'template_id'=>$id,
            'saved'=>'1',
            'rules'=>count($rules)
        ),admin_url('admin.php')));
        exit;
    }

    private static function conditions_page(){
        $id=isset($_GET['template_id'])?absint($_GET['template_id']):0;
        $post=get_post($id);
        if(!$post||'elementor_library'!==$post->post_type){echo '<div class="wrap"><h1>Şablon bulunamadı.</h1></div>';return;}
        $rules=get_post_meta($id,'_wpst_display_conditions',true);if(!is_array($rules))$rules=array();
        $rules=class_exists('WPST_Display_Conditions')?WPST_Display_Conditions::sanitize($rules):$rules;
        $opts=class_exists('WPST_Display_Conditions')?WPST_Display_Conditions::labels():array();
        $priority=(int)get_post_meta($id,'_wpst_condition_priority',true);if(!$priority)$priority=10;
        $pages=get_pages(array(
            'post_status'=>array('publish','draft','private'),
            'sort_column'=>'menu_order,post_title',
            'sort_order'=>'ASC'
        ));
        echo '<div class="wrap wpst-my-templates"><div class="wpst-mt-head"><div><a class="wpst-mt-back" href="'.esc_url(admin_url('admin.php?page=wpsoft-my-templates')).'">← Şablonlarım</a><h1>Display Conditions 2.0</h1><p>'.esc_html($post->post_title).' şablonunun nerede görüntüleneceğini veya hariç tutulacağını belirleyin.</p></div></div>';
        if(isset($_GET['saved'])){
            $saved_count=isset($_GET['rules'])?absint($_GET['rules']):count($rules);
            echo '<div class="notice notice-success is-dismissible"><p><strong>Gösterim koşulları kaydedildi.</strong> Aktif koşul sayısı: '.absint($saved_count).'</p></div>';
        }
        echo '<form id="wpst-condition-form" method="post" action="'.esc_url(admin_url('admin-post.php')).'" class="wpst-condition-editor"><input type="hidden" name="action" value="wpst_save_template_conditions"><input type="hidden" name="template_id" value="'.absint($id).'">';
        wp_nonce_field('wpst_save_template_conditions_'.$id);
        echo '<div class="wpst-condition-builder"><div class="wpst-condition-help"><strong>Include / Exclude mantığı</strong><p>Include şablonun kullanılacağı alanı tanımlar. Exclude eşleşirse Include kurallarından bağımsız olarak şablonu kapatır.</p></div>';
        echo '<div class="wpst-condition-add"><select id="wpst-condition-mode"><option value="include">Include</option><option value="exclude">Exclude</option></select><select id="wpst-condition-type">';
        foreach($opts as $k=>$v)echo '<option value="'.esc_attr($k).'">'.esc_html($v).'</option>';
        echo '</select>';
        echo '<input id="wpst-condition-value" type="text" placeholder="ID / içerik türü (gerekirse)">';
        echo '<div class="wpst-page-picker" id="wpst-page-picker" style="display:none">';
        echo '<button type="button" class="wpst-page-picker-trigger" id="wpst-page-picker-trigger"><span>Sayfa seç…</span><b>⌄</b></button>';
        echo '<div class="wpst-page-picker-menu" id="wpst-page-picker-menu">';
        echo '<div class="wpst-page-picker-search"><input type="search" id="wpst-page-picker-search" placeholder="Sayfa ara…"></div>';
        echo '<div class="wpst-page-picker-options">';
        foreach($pages as $page){
            $title=get_the_title($page)?:'(Başlıksız)';
            $status=('publish'===$page->post_status)?'':ucfirst($page->post_status);
            echo '<label class="wpst-page-picker-option" data-search="'.esc_attr(strtolower($title)).'">';
            echo '<input type="checkbox" value="'.absint($page->ID).'" data-title="'.esc_attr($title).'">';
            echo '<span><strong>'.esc_html($title).'</strong><small>#'.absint($page->ID).($status?' · '.esc_html($status):'').'</small></span>';
            echo '</label>';
        }
        echo '</div></div></div>';
        echo '<button type="button" class="button button-primary" id="wpst-condition-add">+ Koşul Ekle</button></div>';
        echo '<div id="wpst-page-picker-selected" class="wpst-page-picker-selected"></div>';
        echo '<div id="wpst-condition-list">';
        foreach($rules as $i=>$r){
            $mode=isset($r['mode'])&&'exclude'===$r['mode']?'exclude':'include';
            $value=isset($r['value'])?(string)$r['value']:'';
            $display=$value;
            if('page'===($r['type']??'')){
                $names=array();
                foreach(array_filter(array_map('absint',explode(',',$value))) as $page_id){
                    $title=get_the_title($page_id);
                    if($title)$names[]=$title;
                }
                if($names)$display=implode(', ',$names);
            }
            echo '<div class="wpst-condition-rule is-'.esc_attr($mode).'"><b class="wpst-condition-mode">'.esc_html(strtoupper($mode)).'</b><strong>'.esc_html($opts[$r['type']]??$r['type']).'</strong><span>'.esc_html($display).'</span><input type="hidden" name="wpst_conditions['.$i.'][mode]" value="'.esc_attr($mode).'"><input type="hidden" name="wpst_conditions['.$i.'][type]" value="'.esc_attr($r['type']).'"><input type="hidden" name="wpst_conditions['.$i.'][value]" value="'.esc_attr($value).'"><button type="button" class="button-link-delete">Kaldır</button></div>';
        }
        echo '</div><div class="wpst-condition-footer"><label><strong>Şablon Önceliği</strong><input type="number" name="wpst_condition_priority" min="1" max="999" value="'.absint($priority).'"><small>Düşük sayı daha yüksek önceliktir. Varsayılan: 10</small></label><div><strong>Koşul mantığı</strong><p>Konum Include kuralları OR, kullanıcı/cihaz Include kuralları AND çalışır. Herhangi bir Exclude eşleşmesi şablonu kapatır.</p></div></div></div><p><button type="submit" name="wpst_conditions_submit" value="1" class="button button-primary button-hero" id="wpst-condition-save">Koşulları Kaydet</button></p></form></div>';
        ?>
        <style>
        .wpst-condition-add{align-items:stretch}
        .wpst-page-picker{position:relative;min-width:280px}
        .wpst-page-picker-trigger{width:100%;height:40px;display:flex;align-items:center;justify-content:space-between;gap:12px;padding:0 12px;border:1px solid #8c8f94;border-radius:3px;background:#fff;color:#1d2327;cursor:pointer;text-align:left}
        .wpst-page-picker-trigger:hover,.wpst-page-picker.is-open .wpst-page-picker-trigger{border-color:#2271b1;box-shadow:0 0 0 1px #2271b1}
        .wpst-page-picker-trigger b{font-size:15px;transition:transform .18s ease}
        .wpst-page-picker.is-open .wpst-page-picker-trigger b{transform:rotate(180deg)}
        .wpst-page-picker-menu{display:none;position:absolute;z-index:99999;top:calc(100% + 5px);left:0;width:min(420px,90vw);max-height:360px;overflow:hidden;border:1px solid #c3c4c7;border-radius:9px;background:#fff;box-shadow:0 18px 44px rgba(15,23,42,.16)}
        .wpst-page-picker.is-open .wpst-page-picker-menu{display:block}
        .wpst-page-picker-search{padding:8px;border-bottom:1px solid #e2e8f0;background:#f8fafc}
        .wpst-page-picker-search input{width:100%;margin:0}
        .wpst-page-picker-options{max-height:300px;overflow:auto;padding:5px}
        .wpst-page-picker-option{display:flex;align-items:flex-start;gap:9px;padding:8px;border-radius:7px;cursor:pointer}
        .wpst-page-picker-option:hover{background:#f1f5f9}
        .wpst-page-picker-option input{margin-top:3px}
        .wpst-page-picker-option span{display:flex;flex-direction:column;min-width:0}
        .wpst-page-picker-option strong{font-size:12px;color:#1d2327}
        .wpst-page-picker-option small{font-size:10px;color:#646970;margin-top:2px}
        .wpst-page-picker-selected{display:flex;flex-wrap:wrap;gap:5px;margin:7px 0 0}
        .wpst-page-picker-chip{display:inline-flex;align-items:center;gap:5px;padding:4px 7px;border-radius:999px;background:#eef2ff;color:#3730a3;font-size:11px}
        .wpst-page-picker-chip button{border:0;background:transparent;color:inherit;padding:0;cursor:pointer;line-height:1}
        @media(max-width:782px){
          .wpst-condition-add{display:grid!important;grid-template-columns:1fr!important}
          .wpst-page-picker{min-width:0;width:100%}
          .wpst-page-picker-menu{width:100%}
        }
        </style>
        <script>
        document.addEventListener("DOMContentLoaded",function(){
          const list=document.getElementById("wpst-condition-list");
          const add=document.getElementById("wpst-condition-add");
          const mode=document.getElementById("wpst-condition-mode");
          const type=document.getElementById("wpst-condition-type");
          const value=document.getElementById("wpst-condition-value");
          const picker=document.getElementById("wpst-page-picker");
          const trigger=document.getElementById("wpst-page-picker-trigger");
          const menu=document.getElementById("wpst-page-picker-menu");
          const search=document.getElementById("wpst-page-picker-search");
          const selected=document.getElementById("wpst-page-picker-selected");
          if(!list||!add||!mode||!type||!value||!picker||!trigger||!menu)return;

          const esc=s=>String(s).replace(/[&<>"]/g,m=>({"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;"}[m]||m));

          function checkedPages(){
            return [...menu.querySelectorAll('input[type="checkbox"]:checked')];
          }
          function updatePicker(){
            const items=checkedPages();
            trigger.querySelector("span").textContent=items.length ? items.length+" sayfa seçildi" : "Sayfa seç…";
            selected.innerHTML=items.map(cb=>'<span class="wpst-page-picker-chip">'+esc(cb.dataset.title)+'<button type="button" data-id="'+cb.value+'" aria-label="Kaldır">×</button></span>').join("");
          }
          function syncTypeUI(){
            const isPage=type.value==="page";
            picker.style.display=isPage?"block":"none";
            value.style.display=isPage?"none":"block";
            if(isPage)value.value="";
            else picker.classList.remove("is-open");
          }
          type.addEventListener("change",syncTypeUI);
          syncTypeUI();

          trigger.addEventListener("click",function(e){
            e.stopPropagation();
            picker.classList.toggle("is-open");
            if(picker.classList.contains("is-open")&&search)setTimeout(()=>search.focus(),0);
          });
          menu.addEventListener("change",function(e){
            if(e.target.matches('input[type="checkbox"]'))updatePicker();
          });
          selected.addEventListener("click",function(e){
            const b=e.target.closest("button[data-id]");
            if(!b)return;
            const cb=menu.querySelector('input[type="checkbox"][value="'+b.dataset.id+'"]');
            if(cb){cb.checked=false;updatePicker();}
          });
          if(search){
            search.addEventListener("input",function(){
              const q=this.value.trim().toLocaleLowerCase("tr");
              menu.querySelectorAll(".wpst-page-picker-option").forEach(row=>{
                const text=(row.dataset.search||"").toLocaleLowerCase("tr");
                row.style.display=!q||text.includes(q)?"flex":"none";
              });
            });
          }
          document.addEventListener("click",function(e){
            if(!picker.contains(e.target))picker.classList.remove("is-open");
          });

          add.addEventListener("click",function(){
            let raw="",display="";
            if(type.value==="page"){
              const items=checkedPages();
              if(!items.length){alert("En az bir sayfa seçmelisin.");return;}
              raw=items.map(cb=>cb.value).join(",");
              display=items.map(cb=>cb.dataset.title).join(", ");
            }else{
              raw=value.value.trim();
              display=raw;
            }

            const i=list.children.length,row=document.createElement("div");
            row.className="wpst-condition-rule is-"+mode.value;
            row.innerHTML=
              "<b class=wpst-condition-mode>"+mode.value.toUpperCase()+"</b>"+
              "<strong>"+esc(type.options[type.selectedIndex].text)+"</strong>"+
              "<span>"+esc(display)+"</span>"+
              '<input type="hidden" name="wpst_conditions['+i+'][mode]" value="'+esc(mode.value)+'">'+
              '<input type="hidden" name="wpst_conditions['+i+'][type]" value="'+esc(type.value)+'">'+
              '<input type="hidden" name="wpst_conditions['+i+'][value]" value="'+esc(raw)+'">'+
              '<button type="button" class="button-link-delete">Kaldır</button>';
            list.appendChild(row);

            value.value="";
            if(type.value==="page"){
              menu.querySelectorAll('input[type="checkbox"]').forEach(cb=>cb.checked=false);
              if(search)search.value="";
              menu.querySelectorAll(".wpst-page-picker-option").forEach(row=>row.style.display="flex");
              updatePicker();
              picker.classList.remove("is-open");
            }
          });

          list.addEventListener("click",function(e){
            if(e.target.classList.contains("button-link-delete"))e.target.closest(".wpst-condition-rule").remove();
          });

          const form=document.getElementById("wpst-condition-form");
          const save=document.getElementById("wpst-condition-save");
          if(form){
            form.addEventListener("submit",function(e){
              // If pages are selected but user forgot '+ Koşul Ekle', convert them now.
              if(type.value==="page" && checkedPages().length){
                const currentSelected=checkedPages().map(cb=>cb.value).sort().join(",");
                const already=[...list.querySelectorAll('.wpst-condition-rule')].some(row=>{
                  const t=row.querySelector('input[name$="[type]"]');
                  const v=row.querySelector('input[name$="[value]"]');
                  return t&&v&&t.value==="page"&&v.value.split(",").sort().join(",")===currentSelected;
                });
                if(!already){
                  e.preventDefault();
                  add.click();
                  setTimeout(()=>form.requestSubmit(save),0);
                  return;
                }
              }

              // Re-index every row so PHP receives a clean wpst_conditions array.
              [...list.querySelectorAll(".wpst-condition-rule")].forEach((row,index)=>{
                row.querySelectorAll('input[type="hidden"]').forEach(input=>{
                  input.name=input.name.replace(/wpst_conditions\[[^\]]+\]/,'wpst_conditions['+index+']');
                });
              });

              if(save){
                save.disabled=true;
                save.textContent="Kaydediliyor…";
              }
            });
          }
        });
        </script>
        <?php
    }

    private static function new_template_page() {
        $back=admin_url('admin.php?page=wpsoft-my-templates');
        echo '<div class="wrap wpst-my-templates">';
        echo '<div class="wpst-mt-head"><div><a class="wpst-mt-back" href="'.esc_url($back).'">← Şablonlarım</a><h1>Yeni Şablon Ekle</h1><p>Önce şablon türünü seçin. Header, Footer, Mega Menü, Blog Arşiv veya Tek Yazı şablonunu hazır tasarımdan oluşturup Elementor ile düzenleyebilirsiniz.</p></div></div>';

        echo '<div class="wpst-mt-type-grid">';
        echo '<a href="#wpst-new-header" class="wpst-mt-type-card"><i>H</i><div><strong>Header</strong><span>Logo, menü, CTA ve mobil navigasyon için hazır üst alanlar.</span></div><b>→</b></a>';
        echo '<a href="#wpst-new-footer" class="wpst-mt-type-card"><i>F</i><div><strong>Footer</strong><span>Kurumsal, modern ve sektör odaklı alt alan tasarımları.</span></div><b>→</b></a>';
        echo '<a href="#wpst-new-mega" class="wpst-mt-type-card"><i>M</i><div><strong>Mega Menü</strong><span>Geniş navigasyon, ikonlu linkler, promo kartları ve sektör presetleri.</span></div><b>→</b></a>';
        echo '<a href="#wpst-new-blog-archive" class="wpst-mt-type-card"><i>A</i><div><strong>Blog Arşiv</strong><span>Blog sayfasında yayınlanan yazıları grid, magazine veya liste şeklinde gösterin.</span></div><b>→</b></a>';
        echo '<a href="#wpst-new-blog-single" class="wpst-mt-type-card"><i>S</i><div><strong>Tek Yazı</strong><span>Blog yazısına tıklandığında açılan detay sayfasının dinamik Elementor tasarımı.</span></div><b>→</b></a>';
        echo '<a href="#wpst-new-blog-context" class="wpst-mt-type-card"><i>D</i><div><strong>Dinamik Arşivler</strong><span>Kategori, etiket, yazar ve arama sonuçları için hazır Elementor şablonları.</span></div><b>→</b></a>';
        echo '</div>';

        self::new_hf_section('header',self::headers(),'wpst-new-header','Header Şablonları');
        self::new_hf_section('footer',self::footers(),'wpst-new-footer','Footer Şablonları');

        echo '<section id="wpst-new-mega" class="wpst-mt-new-section"><div class="wpst-mt-section-head"><div><span>MEGA MENÜ</span><h2>Mega Menü Şablonları</h2><p>Hazır sektör düzenlerinden başlayın veya boş bir Mega Menü oluşturun.</p></div>';
        if(class_exists('WPST_Mega_Menu')) echo '<a class="button" href="'.esc_url(wp_nonce_url(admin_url('admin-post.php?action=wpst_create_menu_template'),'wpst_create_menu_template')).'">+ Boş Mega Menü</a>';
        echo '</div><div class="wpst-mt-library-grid">';
        if(class_exists('WPST_Mega_Menu')){
            foreach(WPST_Mega_Menu::presets() as $key=>$item){
                $url=wp_nonce_url(admin_url('admin-post.php?action=wpst_create_mega_preset&preset='.rawurlencode($key)),'wpst_create_mega_preset_'.$key);
                echo '<article class="wpst-mt-library-card"><div class="wpst-mt-mega-preview '.esc_attr($item['class']).'"><span></span><span></span><span></span><b></b></div><div><span class="wpst-mt-badge">MEGA MENÜ</span><h3>'.esc_html($item['title']).'</h3><p>'.esc_html($item['description']).'</p><a class="button button-primary" href="'.esc_url($url).'">Şablonu Oluştur</a></div></article>';
            }
        }
        echo '</div></section>';

        self::new_blog_section('archive','wpst-new-blog-archive','Blog Arşiv Şablonları');
        self::new_blog_section('single','wpst-new-blog-single','Tek Yazı Şablonları');
        echo '<div id="wpst-new-blog-context"></div>';
        self::new_blog_section('category','wpst-new-category','Kategori Arşiv Şablonları');
        self::new_blog_section('tag','wpst-new-tag','Etiket Arşiv Şablonları');
        self::new_blog_section('author','wpst-new-author','Yazar Arşiv Şablonları');
        self::new_blog_section('search','wpst-new-search','Arama Sonuçları Şablonları');

        echo '</div>';
    }

    private static function new_blog_section($type,$id,$title){
        if(!class_exists('WPST_Template_Library')) return;
        $items=('single'===$type)?WPST_Template_Library::blog_single_presets():WPST_Template_Library::blog_archive_presets();
        $labels=array('single'=>'TEK YAZI','archive'=>'BLOG ARŞİV','category'=>'KATEGORİ','tag'=>'ETİKET','author'=>'YAZAR','search'=>'ARAMA');
        $label=isset($labels[$type])?$labels[$type]:'BLOG ARŞİV';
        $desc=('single'===$type)?'Yazı detay sayfası için dinamik Elementor şablonu oluşturun.':(('archive'===$type)?'Blog listeleme sayfası için yayınlanmış yazıları otomatik çeken Elementor şablonu oluşturun.':'Aktif WordPress sorgusunu kullanan dinamik arşiv Elementor şablonu oluşturun.');
        echo '<section id="'.esc_attr($id).'" class="wpst-mt-new-section"><div class="wpst-mt-section-head"><div><span>'.esc_html($label).'</span><h2>'.esc_html($title).'</h2><p>'.esc_html($desc).'</p></div></div><div class="wpst-mt-library-grid">';
        foreach($items as $key=>$item){
            $url=wp_nonce_url(admin_url('admin-post.php?action=wpst_create_blog_library_template&type='.$type.'&style='.$key),'wpst_create_blog_library_'.$type.'_'.$key);
            echo '<article class="wpst-mt-library-card"><div class="wpst-mt-lib-img"><img src="'.esc_url(WPST_URL.'assets/images/templates/'.$item['preview']).'" alt=""></div><div><span class="wpst-mt-badge">'.esc_html($label).'</span><h3>'.esc_html($item['title']).'</h3><p>'.esc_html($item['desc']).'</p><a class="button button-primary" href="'.esc_url($url).'">Şablonu Oluştur</a></div></article>';
        }
        echo '</div></section>';
    }

    private static function footer_sector($key){
        $map=array(
            'modern-saas'=>'SaaS','dark-gradient'=>'SaaS','app-gradient'=>'App','agency-bento'=>'Ajans','seo-growth'=>'SEO & Marketing',
            'corporate-pro'=>'Kurumsal','corporate'=>'Kurumsal','construction-bold'=>'İnşaat','industry-modern'=>'Makina & Sanayi',
            'hotel-luxury'=>'Otel','restaurant-editorial'=>'Restoran','medical-clean'=>'Sağlık','commerce-premium'=>'E-Ticaret',
            'finance-trust'=>'Finans','legal-elegant'=>'Hukuk','education-friendly'=>'Eğitim','realestate-luxury'=>'Gayrimenkul',
            'photography-canvas'=>'Fotoğrafçı','portfolio-creative'=>'Portföy','blog-editorial'=>'Blog & Editorial',
            'newsletter-glass'=>'Newsletter','newsletter'=>'Newsletter','minimal-line'=>'Minimal','minimal'=>'Minimal','centered'=>'Minimal',
            'dark'=>'Genel','dark-minimal'=>'Minimal','cta'=>'Genel','mega'=>'Kurumsal','split'=>'Genel',
            'boxed-cloud'=>'Boxed / Rounded','boxed-midnight'=>'Boxed / Rounded','boxed-editorial'=>'Boxed / Rounded','boxed-sage'=>'Boxed / Rounded'
        );
        return isset($map[$key])?$map[$key]:'Genel';
    }

    private static function new_hf_section($type,$items,$id,$title) {
        echo '<section id="'.esc_attr($id).'" class="wpst-mt-new-section"><div class="wpst-mt-section-head"><div><span>'.esc_html(strtoupper($type)).'</span><h2>'.esc_html($title).'</h2><p>Hazır tasarımı Elementor şablonu olarak oluşturup hemen düzenlemeye başlayın.</p></div></div>';
        if('footer'===$type){
            $sectors=array();foreach($items as $key=>$item){$sectors[self::footer_sector($key)]=true;}
            echo '<div class="wpst-footer-sector-filter" data-target="'.esc_attr($id).'"><button type="button" class="is-active" data-sector="all">Tümü <b>'.count($items).'</b></button>';
            foreach(array_keys($sectors) as $sector){echo '<button type="button" data-sector="'.esc_attr(sanitize_title($sector)).'">'.esc_html($sector).'</button>';}
            echo '</div>';
        }
        echo '<div class="wpst-mt-library-grid">';
        foreach($items as $key=>$item){
            $url=wp_nonce_url(admin_url('admin-post.php?action=wpst_create_elementor_hf_template&type='.$type.'&template='.$key),'wpst_create_hf_'.$type.'_'.$key);
            $sector=('footer'===$type)?self::footer_sector($key):'';
            echo '<article class="wpst-mt-library-card"'.('footer'===$type?' data-sector="'.esc_attr(sanitize_title($sector)).'"':'').'><div class="wpst-mt-lib-img"><img src="'.esc_url(WPST_URL.'assets/images/header-footer/'.$item['preview']).'" alt=""></div><div><span class="wpst-mt-badge">'.esc_html(strtoupper($type)).'</span>'.('footer'===$type?'<span class="wpst-mt-sector-badge">'.esc_html($sector).'</span>':'').'<h3>'.esc_html($item['title']).'</h3><p>'.esc_html($item['desc']).'</p><a class="button button-primary" href="'.esc_url($url).'">Şablonu Oluştur</a></div></article>';
        }
        echo '</div></section>';
        if('footer'===$type){echo '<script>document.addEventListener("DOMContentLoaded",function(){document.querySelectorAll("#'.esc_js($id).' .wpst-footer-sector-filter button").forEach(function(b){b.addEventListener("click",function(){var s=b.getAttribute("data-sector");document.querySelectorAll("#'.esc_js($id).' .wpst-footer-sector-filter button").forEach(function(x){x.classList.toggle("is-active",x===b)});document.querySelectorAll("#'.esc_js($id).' .wpst-mt-library-card[data-sector]").forEach(function(c){c.style.display=(s==="all"||c.getAttribute("data-sector")===s)?"":"none";});});});});</script>';}
    }

    public static function delete_my_template() {
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Yetkiniz yok.' );
        $id = isset($_GET['template_id']) ? absint($_GET['template_id']) : 0;
        check_admin_referer( 'wpst_delete_my_template_' . $id );
        $is_hf = $id && '1' === get_post_meta($id,'_wpst_hf_template',true);
        $is_mega = $id && '1' === get_post_meta($id,'_wpst_menu_template',true);
        $is_blog = $id && '1' === get_post_meta($id,'_wpst_blog_library_template',true);
        if ( ! $is_hf && ! $is_mega && ! $is_blog ) {
            wp_die( 'WPSoft şablonu bulunamadı.' );
        }
        wp_trash_post( $id );
        wp_safe_redirect( admin_url('admin.php?page=wpsoft-my-templates&deleted=1') );
        exit;
    }

    public static function page() {
        if ( ! current_user_can( 'manage_options' ) ) return;
        $headers = self::headers(); $footers = self::footers();
        echo '<div class="wrap"><h1>Header & Footer Şablonları</h1><p>Hazır tasarımlardan birini WPSoft hızlı tasarım sistemine uygulayın veya Elementor şablonu olarak oluşturun.</p>';
        self::tabs('header', $headers);
        self::tabs('footer', $footers);
        echo '</div>';
    }

    private static function tabs($type,$items) {
        echo '<h2 style="margin-top:28px">'.($type==='header'?'Header Şablonları':'Footer Şablonları').'</h2>';
        echo '<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(290px,1fr));gap:18px;max-width:1400px">';
        foreach($items as $key=>$t){
            $apply = wp_nonce_url(admin_url('admin-post.php?action=wpst_apply_hf_template&type='.$type.'&template='.$key),'wpst_apply_hf_'.$type.'_'.$key);
            $elementor = wp_nonce_url(admin_url('admin-post.php?action=wpst_create_elementor_hf_template&type='.$type.'&template='.$key),'wpst_create_hf_'.$type.'_'.$key);
            echo '<article style="background:#fff;border:1px solid #dcdcde;border-radius:16px;padding:12px;box-shadow:0 8px 28px rgba(0,0,0,.04)">';
            echo '<img src="'.esc_url(WPST_URL.'assets/images/header-footer/'.$t['preview']).'" alt="" style="width:100%;height:180px;object-fit:cover;border-radius:12px;display:block">';
            echo '<div style="padding:10px 8px"><h3 style="font-size:17px;margin:3px 0 6px">'.esc_html($t['title']).'</h3><p style="min-height:42px">'.esc_html($t['desc']).'</p>';
            echo '<div style="display:flex;gap:8px;flex-wrap:wrap"><a class="button button-primary" href="'.esc_url($apply).'">Hızlı Tasarıma Uygula</a><a class="button" href="'.esc_url($elementor).'">Elementor Şablonu Oluştur</a></div></div></article>';
        }
        echo '</div>';
    }

    private static function get_current_options() {
        $defaults = array();
        if ( class_exists('WPST_Options') && method_exists('WPST_Options','get') ) {
            $got = WPST_Options::get();
            if ( is_array($got) ) $defaults = $got;
        }
        if ( empty($defaults) ) {
            $raw = get_option('wpst_options', array());
            if ( is_array($raw) ) $defaults = $raw;
        }
        return $defaults;
    }

    public static function apply() {
        if(!current_user_can('manage_options')) wp_die('Yetkiniz yok.');
        $type = sanitize_key($_GET['type'] ?? 'header');
        $key = sanitize_key($_GET['template'] ?? '');
        check_admin_referer('wpst_apply_hf_'.$type.'_'.$key);
        $all = $type==='footer' ? self::footers() : self::headers();
        if(empty($all[$key])) wp_die('Şablon bulunamadı.');
        $t = $all[$key];

        $opts = self::get_current_options();
        $prefix = $type === 'footer' ? 'footer_' : 'header_';
        $opts[$prefix.'enabled'] = 'yes';
        $opts[$prefix.'mode'] = 'quick';
        $opts[$prefix.'sections'] = (string)$t['sections'];
        $opts[$prefix.'blocks'] = $t['blocks'];

        foreach((array)$t['settings'] as $k=>$v){
            $opts[$prefix.$k] = $v;
        }
        update_option('wpst_options',$opts);

        wp_safe_redirect(admin_url('admin.php?page=wpsoft-site-tools&wpst_template_applied='.$type));
        exit;
    }

    public static function create_elementor_template() {
        if(!current_user_can('manage_options')) wp_die('Yetkiniz yok.');
        $type = sanitize_key($_GET['type'] ?? 'header');
        $key = sanitize_key($_GET['template'] ?? '');
        check_admin_referer('wpst_create_hf_'.$type.'_'.$key);
        $all = $type==='footer' ? self::footers() : self::headers();
        if(empty($all[$key])) wp_die('Şablon bulunamadı.');
        if(!did_action('elementor/loaded') || !class_exists('\\Elementor\\Plugin')) wp_die('Elementor etkin olmalıdır.');

        $t = $all[$key];
        $post_id = wp_insert_post(array(
            'post_title' => 'WPSoft - '.$t['title'],
            'post_type' => 'elementor_library',
            'post_status' => 'publish'
        ));
        if(is_wp_error($post_id) || !$post_id) wp_die('Şablon oluşturulamadı.');

        update_post_meta($post_id,'_elementor_edit_mode','builder');
        update_post_meta($post_id,'_elementor_template_type',$type);
        update_post_meta($post_id,'_wpst_hf_template','1');
        update_post_meta($post_id,'_wpst_hf_type',$type);
        update_post_meta($post_id,'_wpst_hf_key',$key);
        update_post_meta($post_id,'_elementor_version',defined('ELEMENTOR_VERSION')?ELEMENTOR_VERSION:'3.0.0');

        $data = self::elementor_hf_data($type,$key,$t);
        update_post_meta($post_id,'_elementor_data',wp_slash(wp_json_encode($data)));

        wp_safe_redirect(admin_url('post.php?post='.$post_id.'&action=elementor'));
        exit;
    }

    private static function uid(){ return substr(md5(uniqid('',true)),0,8); }
    private static function el($widget,$settings=array()){ return array('id'=>self::uid(),'elType'=>'widget','widgetType'=>$widget,'settings'=>$settings,'elements'=>array()); }
    private static function cont($elements,$settings=array()){ return array('id'=>self::uid(),'elType'=>'container','settings'=>array_merge(array('content_width'=>'boxed','width'=>array('unit'=>'px','size'=>1200,'sizes'=>array()),'padding'=>array('unit'=>'px','top'=>'18','right'=>'24','bottom'=>'18','left'=>'24','isLinked'=>false)),$settings),'elements'=>$elements,'isInner'=>false); }

    private static function elementor_hf_data($type,$key,$t){
        if($type==='header'){
            $settings=isset($t['settings'])&&is_array($t['settings'])?$t['settings']:array();
            $bg=isset($settings['bg'])?$settings['bg']:'#ffffff';
            $text=isset($settings['text'])?$settings['text']:'#0f172a';
            $accent=isset($settings['button_bg'])?$settings['button_bg']:'#2563eb';
            $radius=isset($settings['radius'])?absint($settings['radius']):12;
            $class='wpst-header-template wpst-header-'.sanitize_html_class($key);

            $cta='Teklif Al';
            if('dark'===$key)$cta='Projeyi Başlat';
            elseif('pill'===$key)$cta='Demo İste';
            elseif('executive-glass'===$key)$cta='Projeyi Başlat';
            elseif('studio-minimal'===$key)$cta='Bize Yazın';
            elseif('saas-command'===$key)$cta='Ücretsiz Başla';
            elseif('luxury-center'===$key)$cta='Rezervasyon';
            elseif('industry-pro'===$key)$cta='Teknik Teklif';
            elseif('hotel-reserve'===$key)$cta='Rezervasyon Yap';

            $logo_align=in_array($key,array('centered','luxury-center'),true)?'center':'flex-start';
            $logo=self::el('wpsoft-site-logo',array(
                'source'=>'site',
                'link_home'=>'yes',
                'fallback_title'=>'yes',
                'logo_width'=>array('unit'=>'px','size'=>148),
                'logo_width_tablet'=>array('unit'=>'px','size'=>132),
                'logo_width_mobile'=>array('unit'=>'px','size'=>116),
                'max_height'=>array('unit'=>'px','size'=>54),
                'max_height_tablet'=>array('unit'=>'px','size'=>48),
                'max_height_mobile'=>array('unit'=>'px','size'=>44),
                'align'=>$logo_align,
                'title_color'=>$text
            ));
            $nav_preset='modern';
            $active_style='pill';
            $hover_motion='lift';
            if(in_array($key,array('studio-minimal','centered'),true)){
                $nav_preset='minimal';
                $active_style='underline';
                $hover_motion='none';
            }elseif(in_array($key,array('floating','executive-glass'),true)){
                $nav_preset='floating';
                $active_style='pill';
            }elseif(in_array($key,array('glass','luxury-center'),true)){
                $nav_preset='glass';
                $active_style='soft';
            }elseif(in_array($key,array('industry-pro'),true)){
                $nav_preset='clean';
                $active_style='outline';
            }

            $menu=self::el('wpsoft-navigation',array(
                'menu_id'=>'0',
                'fallback'=>'first',
                'submenu_indicator'=>'yes',
                'aria_label'=>'Ana navigasyon',
                'mobile_behavior'=>'inherit',
                'align'=>'center',
                'menu_preset'=>$nav_preset,
                'active_style'=>$active_style,
                'hover_motion'=>$hover_motion,
                'text_color'=>$text,
                'hover_color'=>$accent,
                'active_color'=>$accent,
                'dropdown_bg'=>'#ffffff',
                'dropdown_color'=>'#0f172a',
                'dropdown_hover_bg'=>'#f8fafc',
                'dropdown_active_color'=>$accent,
                'dropdown_shadow'=>'soft',
                'mobile_cta_enabled'=>'yes',
                'mobile_cta_text'=>$cta,
                'mobile_cta_url'=>array('url'=>in_array($key,array('luxury-center','hotel-reserve'),true)?'#rezervasyon':'#iletisim'),
                'mobile_cta_bg'=>$accent,
                'mobile_cta_color'=>'#ffffff'
            ));
            $button=self::el('wpsoft-advanced-button',array(
                'text'=>$cta,
                'url'=>array('url'=>in_array($key,array('luxury-center','hotel-reserve'),true)?'#rezervasyon':'#iletisim'),
                'style_preset'=>'solid',
                'bg'=>$accent,
                'color'=>'#ffffff',
                'border_color'=>$accent,
                'hover_bg'=>$text,
                'hover_color'=>'#ffffff',
                'radius'=>array('unit'=>'px','size'=>12),
                '_css_classes'=>'wpst-header-desktop-cta'
            ));

            $main_settings=array(
                '_css_classes'=>$class,
                'content_width'=>'boxed',
                'boxed_width'=>array('unit'=>'px','size'=>1280,'sizes'=>array()),
                'flex_direction'=>'row',
                'flex_justify_content'=>'space-between',
                'flex_align_items'=>'center',
                'background_background'=>'classic',
                'background_color'=>$bg,
                'gap'=>array('unit'=>'px','size'=>24,'sizes'=>array()),
                'padding'=>array('unit'=>'px','top'=>'6','right'=>'22','bottom'=>'6','left'=>'22','isLinked'=>false),
                'border_radius'=>array('unit'=>'px','top'=>(string)$radius,'right'=>(string)$radius,'bottom'=>(string)$radius,'left'=>(string)$radius,'isLinked'=>true)
            );

            if(in_array($key,array('floating','executive-glass'),true)){
                $main_settings['margin']=array('unit'=>'px','top'=>'16','right'=>'20','bottom'=>'0','left'=>'20','isLinked'=>false);
                $main_settings['padding']=array('unit'=>'px','top'=>'11','right'=>'14','bottom'=>'11','left'=>'18','isLinked'=>false);
                $main_settings['border_border']='solid';
                $main_settings['border_width']=array('unit'=>'px','top'=>'1','right'=>'1','bottom'=>'1','left'=>'1','isLinked'=>true);
                $main_settings['border_color']=('executive-glass'===$key)?'rgba(255,255,255,.72)':'#e5e7eb';
                $main_settings['box_shadow_box_shadow_type']='yes';
                $main_settings['box_shadow_box_shadow']=array('horizontal'=>0,'vertical'=>12,'blur'=>34,'spread'=>-16,'color'=>'rgba(15,23,42,.26)');
            }
            if(in_array($key,array('glass','executive-glass'),true)){
                $main_settings['_css_classes'].=' is-glass';
            }
            if(in_array($key,array('centered','luxury-center'),true)){
                return array(self::cont(array(
                    self::cont(array($menu),array('content_width'=>'full','width'=>array('unit'=>'%','size'=>38,'sizes'=>array()),'padding'=>array('unit'=>'px','top'=>'0','right'=>'0','bottom'=>'0','left'=>'0','isLinked'=>true))),
                    self::cont(array($logo),array('content_width'=>'full','width'=>array('unit'=>'%','size'=>24,'sizes'=>array()),'flex_align_items'=>'center','padding'=>array('unit'=>'px','top'=>'0','right'=>'0','bottom'=>'0','left'=>'0','isLinked'=>true))),
                    self::cont(array($button),array('content_width'=>'full','width'=>array('unit'=>'%','size'=>38,'sizes'=>array()),'flex_align_items'=>'flex-end','padding'=>array('unit'=>'px','top'=>'0','right'=>'0','bottom'=>'0','left'=>'0','isLinked'=>true)))
                ),$main_settings));
            }
            if('announcement'===$key || 'topbar'===$key){
                $notice=('announcement'===$key)?'Yeni sezon fırsatları başladı · Detayları inceleyin':'Ücretsiz danışmanlık · +90 212 000 00 00 · '.get_option('admin_email');
                return array(
                    self::cont(array(self::el('wpsoft-heading',array('eyebrow'=>$notice,'title'=>'','description'=>''))),array(
                        '_css_classes'=>$class.' wpst-header-notice','content_width'=>'full','background_background'=>'classic',
                        'background_color'=>$accent,'padding'=>array('unit'=>'px','top'=>'7','right'=>'18','bottom'=>'7','left'=>'18','isLinked'=>false)
                    )),
                    self::cont(array($logo,$menu,$button),$main_settings)
                );
            }
            if('split'===$key){
                return array(self::cont(array($menu,$logo,self::el('wpsoft-footer-social',array('title'=>''))),$main_settings));
            }
            if('social'===$key){
                return array(self::cont(array($logo,$menu,self::el('wpsoft-footer-social',array('title'=>''))),$main_settings));
            }

            return array(self::cont(array($logo,$menu,$button),$main_settings));
        }
        $dark_keys = array('dark','dark-minimal','mega','modern-saas','commerce-premium','hotel-luxury','industry-modern','restaurant-editorial','dark-gradient','finance-trust','construction-bold','app-gradient','realestate-luxury','boxed-midnight');
        $bg = in_array($key,$dark_keys,true) ? (isset($t['settings']['bg'])?$t['settings']['bg']:'#0f172a') : (isset($t['settings']['bg'])?$t['settings']['bg']:'#ffffff');
        $brand_text = 'Modern dijital deneyimler ve sürdürülebilir büyüme için güçlü çözümler.';
        $cta_text = 'Bize Ulaşın';
        $newsletter = false;
        $accent = isset($t['settings']['button_bg']) ? $t['settings']['button_bg'] : '#2563eb';
        $template_class = 'wpst-footer-template wpst-footer-' . sanitize_html_class($key);
        if ('industry-modern'===$key){$brand_text='Endüstriyel üretimde güvenilir teknoloji, satış ve servis çözümleri.';$cta_text='Servis Talebi';}
        elseif ('hotel-luxury'===$key){$brand_text='Konfor, zarafet ve unutulmaz bir konaklama deneyimi.';$cta_text='Rezervasyon Yap';}
        elseif ('medical-clean'===$key){$brand_text='Sağlığınız için güvenilir, erişilebilir ve modern hizmet.';$cta_text='Randevu Al';}
        elseif ('restaurant-editorial'===$key){$brand_text='Lezzet, atmosfer ve iyi anılar için sizi bekliyoruz.';$cta_text='Masa Ayırt';}
        elseif ('commerce-premium'===$key){$brand_text='Yeni ürünler, avantajlar ve güvenli alışveriş deneyimi.';$newsletter=true;$cta_text='Alışverişe Başla';}
        elseif (in_array($key,array('modern-saas','newsletter-glass','newsletter','boxed-sage'),true)){$newsletter=true;$cta_text=('boxed-sage'===$key?'Bültene Katıl':'Demo İste');}
        elseif ('agency-bento'===$key){$brand_text='Cesur fikirleri güçlü dijital deneyimlere dönüştüren yaratıcı stüdyo.';$cta_text='Projeyi Konuşalım';}
        elseif ('portfolio-creative'===$key){$brand_text='Bağımsız tasarım, yaratıcı teknoloji ve dikkat çekici dijital işler.';$cta_text='Merhaba De';}
        elseif ('finance-trust'===$key){$brand_text='Finansal kararlarınız için şeffaf, güvenilir ve sürdürülebilir çözümler.';$cta_text='Danışmanlık Al';}
        elseif ('construction-bold'===$key){$brand_text='Güvenli, nitelikli ve sürdürülebilir yapılar inşa ediyoruz.';$cta_text='Teklif İste';}
        elseif ('photography-canvas'===$key){$brand_text='Hikâyenizi zamansız karelere dönüştüren yaratıcı fotoğraf deneyimleri.';$cta_text='Çekim Planla';}
        elseif ('blog-editorial'===$key){$brand_text='Güncel fikirler, derinlemesine rehberler ve ilham veren hikâyeler.';$newsletter=true;$cta_text='Bültene Katıl';}
        elseif ('app-gradient'===$key){$brand_text='Uygulamayı keşfedin, işlerinizi daha hızlı ve kolay yönetin.';$newsletter=true;$cta_text='Uygulamayı İndir';}
        elseif ('seo-growth'===$key){$brand_text='Aramada görünürlüğünüzü ve dijital büyümenizi birlikte hızlandıralım.';$cta_text='Ücretsiz Analiz';}
        elseif ('legal-elegant'===$key){$brand_text='Hukuki süreçlerde güvenilir, şeffaf ve çözüm odaklı danışmanlık.';$cta_text='Görüşme Talep Et';}
        elseif ('education-friendly'===$key){$brand_text='Öğrenmeyi erişilebilir, ilham verici ve geleceğe dönük hale getiriyoruz.';$cta_text='Başvuru Yap';}
        elseif ('realestate-luxury'===$key){$brand_text='Seçkin yaşam alanlarını doğru yatırım fırsatlarıyla buluşturuyoruz.';$cta_text='Portföyü Gör';}

        $links_company=array(
            array('text'=>'Hakkımızda','url'=>array('url'=>'#hakkimizda')),
            array('text'=>'Hizmetler','url'=>array('url'=>'#hizmetler')),
            array('text'=>'Projeler','url'=>array('url'=>'#projeler')),
            array('text'=>'İletişim','url'=>array('url'=>'#iletisim')),
        );
        $links_resources=array(
            array('text'=>'Blog','url'=>array('url'=>'#blog')),
            array('text'=>'SSS','url'=>array('url'=>'#sss')),
            array('text'=>'Destek','url'=>array('url'=>'#destek')),
            array('text'=>'Gizlilik','url'=>array('url'=>'#gizlilik')),
        );
        if ('finance-trust'===$key){
            $links_company=array(array('text'=>'Hizmetler','url'=>array('url'=>'#hizmetler')),array('text'=>'Kurumsal','url'=>array('url'=>'#kurumsal')),array('text'=>'Raporlar','url'=>array('url'=>'#raporlar')),array('text'=>'İletişim','url'=>array('url'=>'#iletisim')));
            $links_resources=array(array('text'=>'Yatırımcı İlişkileri','url'=>array('url'=>'#yatirimci')),array('text'=>'Aydınlatma Metni','url'=>array('url'=>'#kvkk')),array('text'=>'Gizlilik','url'=>array('url'=>'#gizlilik')),array('text'=>'Yasal Uyarı','url'=>array('url'=>'#yasal')));
        } elseif ('construction-bold'===$key){
            $links_company=array(array('text'=>'Projeler','url'=>array('url'=>'#projeler')),array('text'=>'Taahhüt','url'=>array('url'=>'#taahhut')),array('text'=>'Mimarlık','url'=>array('url'=>'#mimarlik')),array('text'=>'Kurumsal','url'=>array('url'=>'#kurumsal')));
            $links_resources=array(array('text'=>'Kalite Politikası','url'=>array('url'=>'#kalite')),array('text'=>'Belgeler','url'=>array('url'=>'#belgeler')),array('text'=>'İş Güvenliği','url'=>array('url'=>'#isg')),array('text'=>'Teklif','url'=>array('url'=>'#teklif')));
        } elseif ('blog-editorial'===$key){
            $links_company=array(array('text'=>'Gündem','url'=>array('url'=>'#gundem')),array('text'=>'Rehber','url'=>array('url'=>'#rehber')),array('text'=>'İlham','url'=>array('url'=>'#ilham')),array('text'=>'Yazarlar','url'=>array('url'=>'#yazarlar')));
            $links_resources=array(array('text'=>'Hakkımızda','url'=>array('url'=>'#hakkimizda')),array('text'=>'Bülten','url'=>array('url'=>'#bulten')),array('text'=>'Arşiv','url'=>array('url'=>'#arsiv')),array('text'=>'İletişim','url'=>array('url'=>'#iletisim')));
        } elseif ('app-gradient'===$key){
            $links_company=array(array('text'=>'Özellikler','url'=>array('url'=>'#ozellikler')),array('text'=>'Fiyatlandırma','url'=>array('url'=>'#fiyat')),array('text'=>'Entegrasyonlar','url'=>array('url'=>'#entegrasyonlar')),array('text'=>'Güncellemeler','url'=>array('url'=>'#changelog')));
            $links_resources=array(array('text'=>'App Store','url'=>array('url'=>'#appstore')),array('text'=>'Google Play','url'=>array('url'=>'#playstore')),array('text'=>'Destek','url'=>array('url'=>'#destek')),array('text'=>'Gizlilik','url'=>array('url'=>'#gizlilik')));
        } elseif ('seo-growth'===$key){
            $links_company=array(array('text'=>'SEO','url'=>array('url'=>'#seo')),array('text'=>'İçerik','url'=>array('url'=>'#icerik')),array('text'=>'Performans','url'=>array('url'=>'#performans')),array('text'=>'Vaka Analizleri','url'=>array('url'=>'#vakalar')));
            $links_resources=array(array('text'=>'SEO Rehberi','url'=>array('url'=>'#rehber')),array('text'=>'Blog','url'=>array('url'=>'#blog')),array('text'=>'Araçlar','url'=>array('url'=>'#araclar')),array('text'=>'Ücretsiz Analiz','url'=>array('url'=>'#analiz')));
        } elseif ('legal-elegant'===$key){
            $links_company=array(array('text'=>'Ticaret Hukuku','url'=>array('url'=>'#ticaret')),array('text'=>'İş Hukuku','url'=>array('url'=>'#is')),array('text'=>'Uyuşmazlık','url'=>array('url'=>'#uyusmazlik')),array('text'=>'Danışmanlık','url'=>array('url'=>'#danismanlik')));
            $links_resources=array(array('text'=>'Ekibimiz','url'=>array('url'=>'#ekip')),array('text'=>'Yayınlar','url'=>array('url'=>'#yayinlar')),array('text'=>'KVKK','url'=>array('url'=>'#kvkk')),array('text'=>'İletişim','url'=>array('url'=>'#iletisim')));
        } elseif ('education-friendly'===$key){
            $links_company=array(array('text'=>'Programlar','url'=>array('url'=>'#programlar')),array('text'=>'Eğitmenler','url'=>array('url'=>'#egitmenler')),array('text'=>'Takvim','url'=>array('url'=>'#takvim')),array('text'=>'Başvuru','url'=>array('url'=>'#basvuru')));
            $links_resources=array(array('text'=>'Öğrenci Portalı','url'=>array('url'=>'#portal')),array('text'=>'SSS','url'=>array('url'=>'#sss')),array('text'=>'Duyurular','url'=>array('url'=>'#duyurular')),array('text'=>'İletişim','url'=>array('url'=>'#iletisim')));
        } elseif ('realestate-luxury'===$key){
            $links_company=array(array('text'=>'Satılık','url'=>array('url'=>'#satilik')),array('text'=>'Kiralık','url'=>array('url'=>'#kiralik')),array('text'=>'Yeni Projeler','url'=>array('url'=>'#projeler')),array('text'=>'Yatırım','url'=>array('url'=>'#yatirim')));
            $links_resources=array(array('text'=>'Danışmanlar','url'=>array('url'=>'#danismanlar')),array('text'=>'Bölgeler','url'=>array('url'=>'#bolgeler')),array('text'=>'Blog','url'=>array('url'=>'#blog')),array('text'=>'Randevu','url'=>array('url'=>'#randevu')));
        }
        $brand=self::el('wpsoft-footer-brand',array('brand'=>get_bloginfo('name'),'text'=>$brand_text,'phone'=>'+90 212 000 00 00','email'=>get_option('admin_email')));
        $links1=self::el('wpsoft-footer-links',array('title'=>'Keşfedin','items'=>$links_company));
        $links2=self::el('wpsoft-footer-links',array('title'=>'Kaynaklar','items'=>$links_resources));
        $social=self::el('wpsoft-footer-social',array('title'=>'Sosyal Medya'));
        $rows=array();
        // Premium footerlar için büyük üst CTA. Minimal/centered yapılar sade tutulur.
        $cta_keys = array('modern-saas','agency-bento','corporate-pro','commerce-premium','hotel-luxury','industry-modern','medical-clean','restaurant-editorial','newsletter-glass','dark-gradient','finance-trust','construction-bold','blog-editorial','app-gradient','seo-growth','legal-elegant','education-friendly','realestate-luxury','boxed-cloud','boxed-midnight','boxed-editorial');
        if ( in_array($key,$cta_keys,true) ) {
            $cta_eyebrow = 'BİRLİKTE ÇALIŞALIM';
            $cta_title = 'Bir sonraki projenizi birlikte büyütelim.';
            if ('hotel-luxury'===$key) { $cta_eyebrow='DENEYİMİ PLANLAYIN'; $cta_title='Bir sonraki konaklamanız burada başlasın.'; }
            elseif ('restaurant-editorial'===$key) { $cta_eyebrow='MASANIZ HAZIR'; $cta_title='İyi yemek, iyi atmosfer, iyi anılar.'; }
            elseif ('industry-modern'===$key || 'construction-bold'===$key) { $cta_eyebrow='PROJENİZİ KONUŞALIM'; $cta_title='Güçlü projeler doğru çözüm ortağıyla başlar.'; }
            elseif ('commerce-premium'===$key) { $cta_eyebrow='YENİ KOLEKSİYON'; $cta_title='Seçili ürünleri ve yeni fırsatları keşfedin.'; }
            elseif ('medical-clean'===$key) { $cta_eyebrow='SAĞLIĞINIZ İÇİN'; $cta_title='Doğru bilgi ve doğru uzmanla başlayın.'; }
            elseif ('finance-trust'===$key) { $cta_eyebrow='GÜVENLE İLERLEYİN'; $cta_title='Finansal hedeflerinizi daha net planlayın.'; }
            elseif ('legal-elegant'===$key) { $cta_eyebrow='HUKUKİ DESTEK'; $cta_title='Sürecinizi güvenle değerlendirelim.'; }
            elseif ('education-friendly'===$key) { $cta_eyebrow='GELECEĞE BAŞLAYIN'; $cta_title='Doğru programla yeni bir adım atın.'; }
            elseif ('realestate-luxury'===$key) { $cta_eyebrow='SEÇKİN PORTFÖY'; $cta_title='Aradığınız yaşam alanını birlikte bulalım.'; }
            elseif ('app-gradient'===$key || 'modern-saas'===$key) { $cta_eyebrow='DAHA HIZLI BÜYÜYÜN'; $cta_title='Daha sade, daha hızlı, daha güçlü bir deneyim.'; }
            $rows[]=self::cont(array(
                self::el('wpsoft-heading',array('eyebrow'=>$cta_eyebrow,'title'=>$cta_title,'description'=>$brand_text)),
                self::el('wpsoft-advanced-button',array('text'=>$cta_text,'url'=>array('url'=>'#iletisim')))
            ),array('_css_classes'=>$template_class.' wpst-footer-premium-cta wpst-footer-row-cta','flex_direction'=>'row','flex_justify_content'=>'space-between','flex_align_items'=>'flex-end','background_background'=>'classic','background_color'=>$bg,'gap'=>array('unit'=>'px','size'=>30,'sizes'=>array()),'padding'=>array('unit'=>'px','top'=>'82','right'=>'24','bottom'=>'50','left'=>'24','isLinked'=>false)));
        }
        if($newsletter){
            $rows[]=self::cont(array(self::el('wpsoft-footer-newsletter',array('eyebrow'=>'BÜLTEN','title'=>'Güncel kalın','text'=>'Yeni içerikler, ürünler ve duyurular doğrudan gelen kutunuza gelsin.','button'=>'Katıl'))),array('_css_classes'=>$template_class.' wpst-footer-row-newsletter','background_background'=>'classic','background_color'=>$bg,'padding'=>array('unit'=>'px','top'=>'54','right'=>'24','bottom'=>'26','left'=>'24','isLinked'=>false)));
        }
        if(in_array($key,array('portfolio-creative','photography-canvas'),true)){
            $rows[]=self::cont(array(self::el('wpsoft-heading',array('eyebrow'=>('photography-canvas'===$key?'BOOK A SHOOT':'LET’S WORK TOGETHER'),'title'=>'Birlikte harika bir şey üretelim.','description'=>$brand_text)),self::el('wpsoft-advanced-button',array('text'=>$cta_text,'url'=>array('url'=>'mailto:'.get_option('admin_email'))))),array('_css_classes'=>$template_class,'flex_direction'=>'row','flex_justify_content'=>'space-between','flex_align_items'=>'center','background_background'=>'classic','background_color'=>$bg,'padding'=>array('unit'=>'px','top'=>'70','right'=>'24','bottom'=>'70','left'=>'24','isLinked'=>false)));
            $rows[]=self::cont(array($social),array('_css_classes'=>$template_class,'background_background'=>'classic','background_color'=>$bg,'padding'=>array('unit'=>'px','top'=>'20','right'=>'24','bottom'=>'28','left'=>'24','isLinked'=>false)));
            return $rows;
        }
        if('minimal'===$key){
            $rows[]=self::cont(array(
                self::cont(array($brand),array('content_width'=>'full','width'=>array('unit'=>'%','size'=>58,'sizes'=>array()))),
                self::cont(array($social),array('content_width'=>'full','width'=>array('unit'=>'%','size'=>42,'sizes'=>array()),'flex_align_items'=>'flex-end'))
            ),array('_css_classes'=>$template_class.' wpst-footer-row-main wpst-footer-layout-minimal','flex_direction'=>'row','flex_justify_content'=>'space-between','flex_align_items'=>'center','background_background'=>'classic','background_color'=>$bg,'padding'=>array('unit'=>'px','top'=>'42','right'=>'24','bottom'=>'30','left'=>'24','isLinked'=>false)));
            $rows[]=self::cont(array(self::el('wpsoft-heading',array('eyebrow'=>'© '.date('Y').' '.get_bloginfo('name'),'title'=>'','description'=>'Tüm hakları saklıdır.'))),array('_css_classes'=>$template_class.' wpst-footer-row-bottom','background_background'=>'classic','background_color'=>$bg,'border_border'=>'solid','border_width'=>array('unit'=>'px','top'=>'1','right'=>'0','bottom'=>'0','left'=>'0','isLinked'=>false),'border_color'=>'#e5e7eb','padding'=>array('unit'=>'px','top'=>'18','right'=>'24','bottom'=>'18','left'=>'24','isLinked'=>false)));
            return $rows;
        }
        if('dark-minimal'===$key){
            $rows[]=self::cont(array($brand,$social,self::el('wpsoft-heading',array('eyebrow'=>'','title'=>'','description'=>'© '.date('Y').' · '.get_bloginfo('name')))),array('_css_classes'=>$template_class.' wpst-footer-row-main wpst-footer-layout-dark-minimal','flex_direction'=>'column','flex_align_items'=>'center','background_background'=>'classic','background_color'=>$bg,'gap'=>array('unit'=>'px','size'=>24,'sizes'=>array()),'padding'=>array('unit'=>'px','top'=>'56','right'=>'24','bottom'=>'42','left'=>'24','isLinked'=>false)));
            return $rows;
        }
        if('split'===$key){
            $rows[]=self::cont(array(
                self::cont(array($brand,self::el('wpsoft-advanced-button',array('text'=>'Birlikte Çalışalım','url'=>array('url'=>'#iletisim')))),array('content_width'=>'full','width'=>array('unit'=>'%','size'=>56,'sizes'=>array()))),
                self::cont(array($links1,$social),array('content_width'=>'full','width'=>array('unit'=>'%','size'=>44,'sizes'=>array()),'flex_direction'=>'row','gap'=>array('unit'=>'px','size'=>28,'sizes'=>array())))
            ),array('_css_classes'=>$template_class.' wpst-footer-row-main wpst-footer-layout-split','flex_direction'=>'row','background_background'=>'classic','background_color'=>$bg,'gap'=>array('unit'=>'px','size'=>42,'sizes'=>array()),'padding'=>array('unit'=>'px','top'=>'72','right'=>'24','bottom'=>'62','left'=>'24','isLinked'=>false)));
            $rows[]=self::cont(array(self::el('wpsoft-heading',array('eyebrow'=>'© '.date('Y').' '.get_bloginfo('name'),'title'=>'','description'=>'Gizlilik · Kullanım Koşulları'))),array('_css_classes'=>$template_class.' wpst-footer-row-bottom','background_background'=>'classic','background_color'=>$bg,'padding'=>array('unit'=>'px','top'=>'20','right'=>'24','bottom'=>'22','left'=>'24','isLinked'=>false)));
            return $rows;
        }
        if('mega'===$key){
            $rows[]=self::cont(array($brand),array('_css_classes'=>$template_class.' wpst-footer-row-hero wpst-footer-layout-mega','background_background'=>'classic','background_color'=>$bg,'padding'=>array('unit'=>'px','top'=>'74','right'=>'24','bottom'=>'30','left'=>'24','isLinked'=>false)));
            $rows[]=self::cont(array($links1,$links2,$social),array('_css_classes'=>$template_class.' wpst-footer-row-main','flex_direction'=>'row','flex_justify_content'=>'space-between','background_background'=>'classic','background_color'=>$bg,'gap'=>array('unit'=>'px','size'=>42,'sizes'=>array()),'padding'=>array('unit'=>'px','top'=>'30','right'=>'24','bottom'=>'52','left'=>'24','isLinked'=>false)));
            $rows[]=self::cont(array(self::el('wpsoft-heading',array('eyebrow'=>'© '.date('Y').' '.get_bloginfo('name'),'title'=>'','description'=>'Kurumsal · Gizlilik · Destek'))),array('_css_classes'=>$template_class.' wpst-footer-row-bottom','background_background'=>'classic','background_color'=>$bg,'border_border'=>'solid','border_width'=>array('unit'=>'px','top'=>'1','right'=>'0','bottom'=>'0','left'=>'0','isLinked'=>false),'border_color'=>'rgba(255,255,255,.12)','padding'=>array('unit'=>'px','top'=>'20','right'=>'24','bottom'=>'20','left'=>'24','isLinked'=>false)));
            return $rows;
        }
        if('minimal-line'===$key || 'centered'===$key){
            $rows[]=self::cont(array($brand,$social),array('_css_classes'=>$template_class.' wpst-footer-row-bottom','flex_direction'=>'row','flex_justify_content'=>'space-between','flex_align_items'=>'center','background_background'=>'classic','background_color'=>$bg,'border_border'=>'solid','border_width'=>array('unit'=>'px','top'=>'1','right'=>'0','bottom'=>'0','left'=>'0','isLinked'=>false),'border_color'=>'#e5e7eb','padding'=>array('unit'=>'px','top'=>'34','right'=>'24','bottom'=>'34','left'=>'24','isLinked'=>false)));
            return $rows;
        }
        $main_elements=array($brand,$links1,$links2,$social);
        $rows[]=self::cont($main_elements,array('_css_classes'=>$template_class.' wpst-footer-row-main','flex_direction'=>'row','flex_justify_content'=>'space-between','flex_align_items'=>'flex-start','background_background'=>'classic','background_color'=>$bg,'gap'=>array('unit'=>'px','size'=>34,'sizes'=>array()),'padding'=>array('unit'=>'px','top'=>'70','right'=>'24','bottom'=>'64','left'=>'24','isLinked'=>false)));
        $rows[]=self::cont(array(self::el('wpsoft-heading',array('eyebrow'=>'© '.date('Y').' '.get_bloginfo('name'),'title'=>'','description'=>'Tüm hakları saklıdır. Gizlilik · Çerezler · Kullanım Koşulları')),self::el('wpsoft-advanced-button',array('text'=>$cta_text,'url'=>array('url'=>'#iletisim')))),array('_css_classes'=>$template_class.' wpst-footer-row-bottom','flex_direction'=>'row','flex_justify_content'=>'space-between','flex_align_items'=>'center','background_background'=>'classic','background_color'=>$bg,'border_border'=>'solid','border_width'=>array('unit'=>'px','top'=>'1','right'=>'0','bottom'=>'0','left'=>'0','isLinked'=>false),'border_color'=>in_array($key,$dark_keys,true)?'rgba(255,255,255,.12)':'#e5e7eb','padding'=>array('unit'=>'px','top'=>'24','right'=>'24','bottom'=>'24','left'=>'24','isLinked'=>false)));

        /*
         * True boxed footer shell.
         * Previous implementation reduced individual row widths only. Elementor
         * still painted each row's background across the document, so the footer
         * looked full-width. For boxed presets all footer rows now live inside a
         * single rounded outer container; child rows are transparent.
         */
        $boxed_footer_keys=array('boxed-cloud','boxed-midnight','boxed-editorial','boxed-sage');
        if(in_array($key,$boxed_footer_keys,true)){
            $boxed_radius=isset($settings['radius'])?max(20,absint($settings['radius'])):32;
            $boxed_max=isset($settings['max_width'])?max(960,absint($settings['max_width'])):1240;

            foreach($rows as &$boxed_row){
                if(!isset($boxed_row['settings']) || !is_array($boxed_row['settings'])) continue;
                $boxed_row['settings']['content_width']='full';
                $boxed_row['settings']['width']=array('unit'=>'%','size'=>100,'sizes'=>array());
                $boxed_row['settings']['background_background']='classic';
                $boxed_row['settings']['background_color']='transparent';
                $boxed_row['settings']['border_radius']=array('unit'=>'px','top'=>'0','right'=>'0','bottom'=>'0','left'=>'0','isLinked'=>true);
            }
            unset($boxed_row);

            $shell_settings=array(
                '_css_classes'=>'wpst-footer-boxed-shell wpst-footer-boxed-shell-'.sanitize_html_class($key),
                'content_width'=>'full',
                'width'=>array('unit'=>'%','size'=>100,'sizes'=>array()),
                'background_background'=>'classic',
                'background_color'=>$bg,
                'border_radius'=>array('unit'=>'px','top'=>(string)$boxed_radius,'right'=>(string)$boxed_radius,'bottom'=>(string)$boxed_radius,'left'=>(string)$boxed_radius,'isLinked'=>true),
                'overflow'=>'hidden',
                'padding'=>array('unit'=>'px','top'=>'0','right'=>'0','bottom'=>'0','left'=>'0','isLinked'=>true),
                'margin'=>array('unit'=>'px','top'=>'0','right'=>'0','bottom'=>'0','left'=>'0','isLinked'=>true),
                'box_shadow_box_shadow_type'=>'yes',
                'box_shadow_box_shadow'=>array('horizontal'=>0,'vertical'=>22,'blur'=>64,'spread'=>-28,'color'=>in_array($key,$dark_keys,true)?'rgba(2,8,23,.34)':'rgba(15,23,42,.14)')
            );

            $shell=self::cont($rows,$shell_settings);

            /*
             * Elementor stretches top-level containers to the full document
             * width. Therefore the boxed shell must NOT be the top-level
             * Elementor container. Keep a transparent full-width outer canvas
             * and center the actual rounded shell as its child.
             */
            $outer_settings=array(
                '_css_classes'=>'wpst-footer-boxed-outer wpst-footer-boxed-outer-'.sanitize_html_class($key),
                'content_width'=>'full',
                'width'=>array('unit'=>'%','size'=>100,'sizes'=>array()),
                'flex_direction'=>'column',
                'flex_align_items'=>'center',
                'background_background'=>'classic',
                'background_color'=>'transparent',
                'padding'=>array('unit'=>'px','top'=>'28','right'=>'32','bottom'=>'28','left'=>'32','isLinked'=>false),
                'padding_tablet'=>array('unit'=>'px','top'=>'20','right'=>'16','bottom'=>'20','left'=>'16','isLinked'=>false),
                'padding_mobile'=>array('unit'=>'px','top'=>'12','right'=>'10','bottom'=>'12','left'=>'10','isLinked'=>false),
                'margin'=>array('unit'=>'px','top'=>'0','right'=>'0','bottom'=>'0','left'=>'0','isLinked'=>true)
            );

            return array(self::cont(array($shell),$outer_settings));
        }

        return $rows;
    }
}
