<?php
if ( ! defined( 'ABSPATH' ) ) exit;

final class WPST_Mega_Menu {

    const OPTION = 'wpst_mega_menu';
    const TEMPLATE_META = '_wpst_menu_template';

    public static function init() {
        add_action( 'admin_menu', array( __CLASS__, 'menu' ), 24 );
        add_action( 'admin_post_wpst_save_mega_menu', array( __CLASS__, 'save' ) );
        add_action( 'admin_post_wpst_create_menu_template', array( __CLASS__, 'create_template' ) );
        add_action( 'admin_post_wpst_delete_menu_template', array( __CLASS__, 'delete_template' ) );
        add_action( 'admin_post_wpst_create_mega_preset', array( __CLASS__, 'create_preset' ) );

        add_filter( 'nav_menu_css_class', array( __CLASS__, 'menu_classes' ), 20, 4 );
        add_filter( 'nav_menu_link_attributes', array( __CLASS__, 'link_attrs' ), 20, 4 );
        add_filter( 'walker_nav_menu_start_el', array( __CLASS__, 'render_elementor_template' ), 20, 4 );

        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'assets' ), 30 );
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_assets' ) );
        add_filter( 'nav_menu_item_title', array( __CLASS__, 'decorate_menu_item_title' ), 20, 4 );
        add_filter( 'walker_nav_menu_start_el', array( __CLASS__, 'decorate_menu_item_output' ), 15, 4 );

        // Native Appearance > Menus integration.
        add_action( 'wp_nav_menu_item_custom_fields', array( __CLASS__, 'menu_item_fields' ), 20, 5 );
        add_action( 'wp_update_nav_menu_item', array( __CLASS__, 'save_menu_item_fields' ), 20, 3 );
    }

    public static function menu() {
        add_submenu_page(
            'wpsoft-site-tools',
            'Mega Menü',
            'Mega Menü',
            'manage_options',
            'wpsoft-mega-menu',
            array( __CLASS__, 'page' )
        );
    }

    private static function settings() {
        $s = get_option( self::OPTION, array() );
        return is_array( $s ) ? $s : array();
    }

    private static function item_config( $item_id ) {
        $all = self::settings();
        $id = absint( $item_id );
        $defaults = array(
            'enabled' => 0,
            'mode' => 'columns',
            'cols' => 3,
            'width' => 'wide',
            'template_id' => 0,
            'promo_title' => '',
            'promo_text' => '',
            'show_column_titles' => 1,
            'panel_bg' => '#ffffff',
            'panel_radius' => 22,
            'panel_shadow' => 'soft',
            'item_style' => 'cards',
            'density' => 'comfortable',
            'promo_button_text' => '',
            'promo_button_url' => '',
            'mobile_accordion' => 1,
            'dynamic_source' => 'none',
            'dynamic_count' => 4,
            'dynamic_category' => 0,
            'dynamic_images' => 1,
            'panel_sticky' => 0,
            'layout_variant' => 'cards',
            'panel_align' => 'center',
            'open_animation' => 'slide',
        );
        return isset( $all[$id] ) && is_array($all[$id])
            ? array_merge( $defaults, $all[$id] )
            : $defaults;
    }


    private static function item_meta( $item_id ) {
        $id = absint($item_id);
        return array(
            'badge' => sanitize_text_field( get_post_meta($id,'_wpst_mega_badge',true) ),
            'badge_color' => sanitize_hex_color( get_post_meta($id,'_wpst_mega_badge_color',true) ) ?: '#2563eb',
            'icon' => sanitize_text_field( get_post_meta($id,'_wpst_mega_icon',true) ),
            'image_id' => absint( get_post_meta($id,'_wpst_mega_image_id',true) ),
            'column_title' => get_post_meta($id,'_wpst_mega_column_title',true) === '1' ? 1 : 0,
            'description' => sanitize_text_field( get_post_meta($id,'_wpst_mega_description',true) ),
        );
    }

    private static function icon_svg( $icon ) {
        if ( class_exists('WPST_Icon_Library') && WPST_Icon_Library::exists($icon) ) {
            return WPST_Icon_Library::svg($icon,array('size'=>22,'class'=>'wpst-menu-icon-svg'));
        }
        $map = array(
            'star' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="m12 2.5 2.9 5.9 6.5.9-4.7 4.6 1.1 6.5-5.8-3.1-5.8 3.1 1.1-6.5-4.7-4.6 6.5-.9L12 2.5Z"/></svg>',
            'bolt' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M13 2 5 13h6l-1 9 8-12h-6l1-8Z"/></svg>',
            'grid' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 4h6v6H4V4Zm10 0h6v6h-6V4ZM4 14h6v6H4v-6Zm10 0h6v6h-6v-6Z"/></svg>',
            'briefcase' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 5V3h6v2h5a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h5Zm2 0h2V5h-2Zm9 6H4v7h16v-7Z"/></svg>',
            'cart' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 4h2l2.2 10.2A2 2 0 0 0 9.1 16H18a2 2 0 0 0 1.9-1.4L22 7H7M10 21a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Zm8 0a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Z"/></svg>',
            'location' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 22s7-6.1 7-13A7 7 0 1 0 5 9c0 6.9 7 13 7 13Zm0-10a3 3 0 1 1 0-6 3 3 0 0 1 0 6Z"/></svg>',
            'heart' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 21 3.6 12.8A5.5 5.5 0 0 1 11.4 5l.6.7.6-.7a5.5 5.5 0 0 1 7.8 7.8L12 21Z"/></svg>',
            'code' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="m8.5 6-6 6 6 6 1.5-1.5L5.5 12 10 7.5 8.5 6Zm7 0L14 7.5l4.5 4.5-4.5 4.5 1.5 1.5 6-6-6-6Z"/></svg>',
            'support' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3a8 8 0 0 0-8 8v5a3 3 0 0 0 3 3h2v-7H6v-1a6 6 0 1 1 12 0v1h-3v7h2a2.9 2.9 0 0 0 1-.2A4 4 0 0 1 14 22h-2v-2h2a2 2 0 0 0 2-1h-1v-7h3v-1a6 6 0 0 0-6-6Z"/></svg>',
            'chart' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 20V10h4v10H4Zm6 0V4h4v16h-4Zm6 0v-7h4v7h-4Z"/></svg>',
        );
        return isset($map[$icon]) ? $map[$icon] : '';
    }

    private static function menu_templates() {
        return get_posts( array(
            'post_type' => 'elementor_library',
            'post_status' => array( 'publish', 'draft', 'private' ),
            'posts_per_page' => -1,
            'orderby' => 'modified',
            'order' => 'DESC',
            'meta_query' => array(
                array(
                    'key' => self::TEMPLATE_META,
                    'value' => '1',
                ),
            ),
        ) );
    }

    public static function page() {
        if ( ! current_user_can( 'manage_options' ) ) return;

        $menus = wp_get_nav_menus();
        $templates = self::menu_templates();
        $settings = self::settings();

        echo '<div class="wrap wpst-mega-admin">';
        echo '<div class="wpst-mega-admin-hero">';
        echo '<div><span class="wpst-mega-kicker">WPSoft Site Tools</span><h1>Mega Menü Builder</h1><p>WordPress menünüzü modern bir navigasyon deneyimine dönüştürün. Hızlı kolon düzenini kullanın veya Mega Links / Mega Promo widget’larıyla Elementor içinde tamamen özgür tasarlayın.</p></div>';
        echo '<div class="wpst-mega-admin-actions"><a class="button button-primary button-hero" href="'.esc_url(admin_url('admin.php?page=wpsoft-my-templates&view=new#wpst-new-mega')).'">Mega Menü Şablonları</a><a class="button button-hero" href="'.esc_url(admin_url('nav-menus.php')).'">WordPress Menülerini Aç</a></div>';
        echo '</div>';

        echo '<div class="wpst-mega-info-grid">';
        echo '<div><strong>1</strong><span>Menü öğesini seç</span><small>Üst seviye menü öğesini Mega Menü olarak etkinleştir.</small></div>';
        echo '<div><strong>2</strong><span>Görünümü belirle</span><small>Kolonlu hızlı yapı veya Elementor Menü Şablonu seç.</small></div>';
        echo '<div><strong>3</strong><span>Kaydet ve kullan</span><small>Header’daki normal WordPress Menü bloğu otomatik kullanır.</small></div>';
        echo '</div>';

        if ( isset($_GET['updated']) ) {
            echo '<div class="notice notice-success is-dismissible"><p>Mega Menü ayarları kaydedildi.</p></div>';
        }

        echo '<div class="wpst-mega-builder-note"><strong>Şablonlar artık Şablonlarım bölümünde.</strong><span>Bu ekran yalnızca WordPress menü öğelerine Mega Menü atamak ve davranışını ayarlamak için kullanılır.</span><a class="button" href="'.esc_url(admin_url('admin.php?page=wpsoft-my-templates')).'">Şablonlarım</a></div>';

        if ( ! $menus ) {
            echo '<div class="notice notice-warning"><p>Önce Görünüm → Menüler bölümünden bir WordPress menüsü oluşturmalısın.</p></div></div>';
            return;
        }

        echo '<form method="post" action="'.esc_url(admin_url('admin-post.php')).'">';
        wp_nonce_field('wpst_save_mega_menu');
        echo '<input type="hidden" name="action" value="wpst_save_mega_menu">';

        foreach ( $menus as $menu ) {
            $items = wp_get_nav_menu_items( $menu->term_id );
            if ( ! $items ) continue;

            echo '<section class="wpst-mega-menu-card">';
            echo '<div class="wpst-mega-section-head"><div><h2>'.esc_html($menu->name).'</h2><p>Bu menüdeki üst seviye öğeler.</p></div><a class="button" href="'.esc_url(admin_url('nav-menus.php?action=edit&menu='.$menu->term_id)).'">Menüyü Düzenle</a></div>';
            echo '<div class="wpst-mega-live-preview" data-menu-preview="'.(int)$menu->term_id.'"><div class="wpst-mega-preview-browser"><div class="wpst-mega-preview-header"><span class="wpst-mega-preview-logo">WPSOFT</span><nav><span>Anasayfa</span><span class="is-active">Mega Menü</span><span>İletişim</span></nav></div><div class="wpst-mega-preview-panel"><div class="wpst-mega-preview-cols"><div><b>Hizmetler</b><i>Web Tasarım</i><i>E-Ticaret</i><i>SEO</i></div><div><b>Çözümler</b><i>Kurumsal</i><i>Bakım</i><i>Destek</i></div><div><b>Kaynaklar</b><i>Blog</i><i>Projeler</i><i>İletişim</i></div></div><div class="wpst-mega-preview-card"><small>ÖNE ÇIKAN</small><strong>Modern Mega Menü</strong><span>Görsel, ikon ve badge kullanabilirsiniz.</span></div></div></div></div>';

            foreach ( $items as $item ) {
                if ( (int)$item->menu_item_parent !== 0 ) continue;

                $id = (int)$item->ID;
                $cfg = self::item_config($id);

                echo '<article class="wpst-mega-row" data-item="'.$id.'">';
                echo '<div class="wpst-mega-row-main">';
                echo '<label class="wpst-switch"><input type="checkbox" name="mega['.$id.'][enabled]" value="1" '.checked(!empty($cfg['enabled']),true,false).'><span></span></label>';
                echo '<div><strong>'.esc_html($item->title).'</strong><small>'.esc_html($item->url).'</small></div>';
                echo '<span class="wpst-mega-status '.(!empty($cfg['enabled'])?'is-on':'').'">'.(!empty($cfg['enabled'])?'Mega Menü Açık':'Normal Menü').'</span>';
                echo '</div>';

                echo '<div class="wpst-mega-row-options">';
                echo '<label><span>Menü Türü</span><select name="mega['.$id.'][mode]" class="wpst-mega-mode">';
                echo '<option value="columns" '.selected($cfg['mode'],'columns',false).'>Kolonlu Mega Menü</option>';
                echo '<option value="dynamic" '.selected($cfg['mode'],'dynamic',false).'>Dinamik İçerik Mega Menü</option>';
                echo '<option value="elementor" '.selected($cfg['mode'],'elementor',false).'>Elementor Menü Şablonu</option>';
                echo '</select></label>';

                echo '<div class="wpst-mode-columns">';
                echo '<label><span>Kolon</span><select name="mega['.$id.'][cols]">';
                foreach(array(2,3,4,5,6) as $c) echo '<option value="'.$c.'" '.selected((int)$cfg['cols'],$c,false).'>'.$c.' Kolon</option>';
                echo '</select></label>';
                echo '</div>';

                echo '<label><span>Dropdown Genişliği</span><select name="mega['.$id.'][width]">';
                foreach(array('menu'=>'Kompakt','wide'=>'Container','full'=>'Tam Genişlik') as $k=>$v) echo '<option value="'.esc_attr($k).'" '.selected($cfg['width'],$k,false).'>'.esc_html($v).'</option>';
                echo '</select></label>';
                echo '<label><span>Panel Hizası</span><select name="mega['.$id.'][panel_align]"><option value="left" '.selected($cfg['panel_align'],'left',false).'>Menüye Sol Hizalı</option><option value="center" '.selected($cfg['panel_align'],'center',false).'>Ortalanmış</option><option value="right" '.selected($cfg['panel_align'],'right',false).'>Menüye Sağ Hizalı</option></select></label>';
                echo '<label><span>Açılış Animasyonu</span><select name="mega['.$id.'][open_animation]"><option value="fade" '.selected($cfg['open_animation'],'fade',false).'>Fade</option><option value="slide" '.selected($cfg['open_animation'],'slide',false).'>Slide</option><option value="scale" '.selected($cfg['open_animation'],'scale',false).'>Scale</option><option value="none" '.selected($cfg['open_animation'],'none',false).'>Animasyon Yok</option></select></label>';
                echo '<label><span>Link Görünümü</span><select name="mega['.$id.'][item_style]"><option value="cards" '.selected($cfg['item_style'],'cards',false).'>Modern Kart</option><option value="list" '.selected($cfg['item_style'],'list',false).'>Temiz Liste</option><option value="compact" '.selected($cfg['item_style'],'compact',false).'>Kompakt</option></select></label>';
                echo '<label><span>Yoğunluk</span><select name="mega['.$id.'][density]"><option value="comfortable" '.selected($cfg['density'],'comfortable',false).'>Rahat</option><option value="compact" '.selected($cfg['density'],'compact',false).'>Sıkı</option></select></label>';
                echo '<label><span>Panel Arka Plan</span><input type="color" class="wpst-mega-panel-bg" name="mega['.$id.'][panel_bg]" value="'.esc_attr($cfg['panel_bg']).'"></label>';
                echo '<label><span>Köşe</span><input type="number" min="0" max="50" class="wpst-mega-panel-radius" name="mega['.$id.'][panel_radius]" value="'.absint($cfg['panel_radius']).'"></label>';
                echo '<label><span>Gölge</span><select name="mega['.$id.'][panel_shadow]" class="wpst-mega-panel-shadow"><option value="none" '.selected($cfg['panel_shadow'],'none',false).'>Yok</option><option value="soft" '.selected($cfg['panel_shadow'],'soft',false).'>Yumuşak</option><option value="medium" '.selected($cfg['panel_shadow'],'medium',false).'>Orta</option><option value="strong" '.selected($cfg['panel_shadow'],'strong',false).'>Güçlü</option></select></label>';
                echo '<label class="wpst-mega-check"><input type="checkbox" name="mega['.$id.'][mobile_accordion]" value="1" '.checked(!empty($cfg['mobile_accordion']),true,false).'> <span>Mobil alt menüler accordion</span></label>';

                echo '<div class="wpst-mode-dynamic">';
                echo '<label><span>Dinamik Kaynak</span><select name="mega['.$id.'][dynamic_source]"><option value="none" '.selected($cfg['dynamic_source'],'none',false).'>Yok</option><option value="posts" '.selected($cfg['dynamic_source'],'posts',false).'>Son Blog Yazıları</option><option value="categories" '.selected($cfg['dynamic_source'],'categories',false).'>Blog Kategorileri</option><option value="products" '.selected($cfg['dynamic_source'],'products',false).'>WooCommerce Ürünleri</option></select></label>';
                echo '<label><span>Gösterilecek Adet</span><input type="number" min="1" max="12" name="mega['.$id.'][dynamic_count]" value="'.absint($cfg['dynamic_count']).'"></label>';
                echo '<label><span>Kategori ID</span><input type="number" min="0" name="mega['.$id.'][dynamic_category]" value="'.absint($cfg['dynamic_category']).'" placeholder="0 = tümü"></label>';
                echo '<label><span>Dinamik Görünüm</span><select name="mega['.$id.'][layout_variant]"><option value="cards" '.selected($cfg['layout_variant'],'cards',false).'>Kartlar</option><option value="list" '.selected($cfg['layout_variant'],'list',false).'>Liste</option><option value="tabs" '.selected($cfg['layout_variant'],'tabs',false).'>Sekmeli Görünüm</option></select></label>';
                echo '<label class="wpst-mega-check"><input type="checkbox" name="mega['.$id.'][dynamic_images]" value="1" '.checked(!empty($cfg['dynamic_images']),true,false).'> <span>Görselleri göster</span></label>';
                echo '<label class="wpst-mega-check"><input type="checkbox" name="mega['.$id.'][panel_sticky]" value="1" '.checked(!empty($cfg['panel_sticky']),true,false).'> <span>Mega panel viewport içinde sabit kalsın</span></label>';
                echo '</div>';

                echo '<div class="wpst-mode-elementor">';
                echo '<label><span>Elementor Şablonu</span><select name="mega['.$id.'][template_id]">';
                echo '<option value="0">Şablon seç</option>';
                foreach($templates as $tpl) echo '<option value="'.(int)$tpl->ID.'" '.selected((int)$cfg['template_id'],(int)$tpl->ID,false).'>'.esc_html($tpl->post_title).'</option>';
                echo '</select></label>';
                if ( !empty($cfg['template_id']) ) {
                    echo '<a class="button" href="'.esc_url(admin_url('post.php?post='.(int)$cfg['template_id'].'&action=elementor')).'">Elementor ile Düzenle</a>';
                } else {
                    echo '<a class="button" href="'.esc_url(self::create_template_url()).'">Yeni Şablon</a>';
                }
                echo '</div>';

                echo '<div class="wpst-mode-columns wpst-mega-extra">';
                echo '<label><span>Tanıtım Başlığı</span><input type="text" name="mega['.$id.'][promo_title]" value="'.esc_attr($cfg['promo_title']).'" placeholder="Örn. Yeni Ürünler"></label>';
                echo '<label><span>Tanıtım Metni</span><input type="text" name="mega['.$id.'][promo_text]" value="'.esc_attr($cfg['promo_text']).'" placeholder="Kısa açıklama"></label>';
                echo '<label><span>Tanıtım Butonu</span><input type="text" name="mega['.$id.'][promo_button_text]" value="'.esc_attr($cfg['promo_button_text']).'" placeholder="Örn. Detaylı İncele"></label>';
                echo '<label><span>Tanıtım Linki</span><input type="url" name="mega['.$id.'][promo_button_url]" value="'.esc_attr($cfg['promo_button_url']).'" placeholder="https:// veya #"></label>';
                echo '</div>';

                echo '</div>';
                echo '</article>';
            }

            echo '</section>';
        }

        submit_button('Mega Menü Ayarlarını Kaydet', 'primary large');
        echo '</form></div>';
    }

    public static function presets() {
        return array(
            'corporate' => array(
                'title' => 'Kurumsal',
                'description' => 'Hizmetler, şirket sayfaları ve güçlü CTA alanı.',
                'class' => 'is-corporate',
            ),
            'services' => array(
                'title' => 'Hizmetler',
                'description' => 'İkonlu hizmet kartları ve kısa açıklamalar.',
                'class' => 'is-services',
            ),
            'shop' => array(
                'title' => 'E-Ticaret',
                'description' => 'Kategori odaklı, kampanya alanlı geniş menü.',
                'class' => 'is-shop',
            ),
            'creative' => array(
                'title' => 'Creative',
                'description' => 'Büyük tipografi, bağlantılar ve görsel CTA düzeni.',
                'class' => 'is-creative',
            ),
            'minimal' => array(
                'title' => 'Minimal',
                'description' => 'Temiz, sade ve hızlı 3 kolon bağlantı yapısı.',
                'class' => 'is-minimal',
            ),
            'software' => array(
                'title' => 'Software / SaaS',
                'description' => 'Ürün, çözümler, kaynaklar ve öne çıkan kart.',
                'class' => 'is-software',
            ),
            'industry' => array(
                'title' => 'Sanayi / Makina',
                'description' => 'Teknik kategoriler, servis bağlantıları ve öne çıkan çözüm kartı.',
                'class' => 'is-industry',
            ),
            'hospitality' => array(
                'title' => 'Otel / Turizm',
                'description' => 'Konaklama, deneyimler ve rezervasyon CTA alanı.',
                'class' => 'is-hospitality',
            ),
        );
    }

    private static function eid() {
        return substr(md5(uniqid('',true)),0,8);
    }

    private static function widget($type,$settings=array()) {
        return array(
            'id'=>self::eid(),
            'elType'=>'widget',
            'widgetType'=>$type,
            'settings'=>$settings,
            'elements'=>array(),
        );
    }

    private static function container($elements=array(),$settings=array()) {
        return array(
            'id'=>self::eid(),
            'elType'=>'container',
            'settings'=>$settings,
            'elements'=>$elements,
            'isInner'=>false,
        );
    }

    private static function preset_data($key) {
        $base = array(
            'content_width'=>'boxed',
            'width'=>array('unit'=>'px','size'=>1200,'sizes'=>array()),
            'padding'=>array('unit'=>'px','top'=>'16','right'=>'16','bottom'=>'16','left'=>'16','isLinked'=>true),
            'gap'=>array('unit'=>'px','size'=>14,'sizes'=>array()),
            'flex_direction'=>'column',
            'background_background'=>'classic',
            'background_color'=>'#ffffff',
            'border_radius'=>array('unit'=>'px','top'=>'18','right'=>'18','bottom'=>'18','left'=>'18','isLinked'=>true)
        );

        $row=function($els,$widths=array()) {
            $count=count($els);$cols=array();
            foreach($els as $i=>$el){
                $w=isset($widths[$i])?$widths[$i]:(100/$count);
                $cols[]=self::container(array($el),array(
                    'content_width'=>'full',
                    'width'=>array('unit'=>'%','size'=>$w,'sizes'=>array()),
                    'padding'=>array('unit'=>'px','top'=>'0','right'=>'6','bottom'=>'0','left'=>'6','isLinked'=>false)
                ));
            }
            return self::container($cols,array('content_width'=>'full','flex_direction'=>'row','align_items'=>'stretch','gap'=>array('unit'=>'px','size'=>6,'sizes'=>array()),'padding'=>array('unit'=>'px','top'=>'0','right'=>'0','bottom'=>'0','left'=>'0','isLinked'=>true)));
        };

        $links=function($title,$items,$style='cards',$columns='2'){
            return self::widget('wpsoft-mega-links',array('title'=>$title,'items'=>$items,'style'=>$style,'columns'=>$columns));
        };
        $item=function($icon,$title,$text,$badge=''){
            return array('icon'=>$icon,'title'=>$title,'text'=>$text,'badge'=>$badge,'url'=>array('url'=>'#'));
        };
        $banner=function($eyebrow,$title,$text,$img,$btn='İncele'){
            return self::widget('wpsoft-mega-banner',array(
                'eyebrow'=>$eyebrow,'title'=>$title,'text'=>$text,
                'image'=>array('url'=>WPST_URL.'assets/images/demo/'.$img),
                'button_text'=>$btn,'button_url'=>array('url'=>'#')
            ));
        };
        $quick=function($items){
            return self::widget('wpsoft-mega-quicknav',array('items'=>$items));
        };

        if('corporate'===$key){
            return array(self::container(array(
                $quick(array(
                    array('icon'=>'◈','title'=>'Hizmetler','url'=>array('url'=>'#')),
                    array('icon'=>'★','title'=>'Projeler','url'=>array('url'=>'#')),
                    array('icon'=>'◎','title'=>'Hakkımızda','url'=>array('url'=>'#')),
                    array('icon'=>'→','title'=>'İletişim','url'=>array('url'=>'#'))
                )),
                $row(array(
                    $links('Dijital Çözümler',array(
                        $item('◈','Kurumsal Web','Markanıza özel modern web deneyimi.','Popüler'),
                        $item('▦','E-Ticaret','Satış ve dönüşüm odaklı mağazalar.'),
                        $item('↗','SEO & Büyüme','Organik görünürlük ve performans.'),
                        $item('⚙','Bakım & Destek','Sürekli teknik destek.')
                    )),
                    $links('Şirket',array(
                        $item('◎','Hakkımızda','Ekibimizi ve yaklaşımımızı tanıyın.'),
                        $item('★','Projeler','Seçili çalışmalarımız.'),
                        $item('▣','Kariyer','Ekibimize katılın.','Yeni'),
                        $item('→','İletişim','Yeni projenizi konuşalım.')
                    ),'list','1'),
                    $banner('FEATURED','Yeni projenizi birlikte planlayalım','Kısa bir görüşmeyle ihtiyacınıza uygun yapıyı belirleyelim.','corporate.svg','Teklif Al')
                ),array(42,24,34))
            ),$base));
        }

        if('services'===$key){
            return array(self::container(array(
                $quick(array(
                    array('icon'=>'◈','title'=>'Web','url'=>array('url'=>'#')),
                    array('icon'=>'▦','title'=>'E-Ticaret','url'=>array('url'=>'#')),
                    array('icon'=>'↗','title'=>'SEO','url'=>array('url'=>'#')),
                    array('icon'=>'⚙','title'=>'Destek','url'=>array('url'=>'#'))
                )),
                $row(array(
                    $links('Hizmetler',array(
                        $item('◈','Web Tasarım','Kurumsal ve premium web projeleri.'),
                        $item('▦','E-Ticaret','WooCommerce ve özel mağazalar.'),
                        $item('↗','SEO','Teknik ve içerik optimizasyonu.'),
                        $item('⚙','Bakım','Güncelleme ve destek.'),
                        $item('★','Dijital Reklam','Dönüşüm odaklı kampanyalar.'),
                        $item('◎','İçerik','Markanıza uygun içerik.')
                    ),'cards','3'),
                    $banner('HİZMETLER','Tek noktadan dijital çözüm','İşletmenizin ihtiyaçlarına göre modüler hizmet yapısı.','agency.svg','Tüm Hizmetler')
                ),array(68,32))
            ),$base));
        }

        if('shop'===$key){
            return array(self::container(array(
                $quick(array(
                    array('icon'=>'✦','title'=>'Yeni Gelenler','url'=>array('url'=>'#')),
                    array('icon'=>'★','title'=>'Çok Satanlar','url'=>array('url'=>'#')),
                    array('icon'=>'%','title'=>'Kampanyalar','url'=>array('url'=>'#')),
                    array('icon'=>'♡','title'=>'Favoriler','url'=>array('url'=>'#'))
                )),
                $row(array(
                    $links('Alışveriş',array(
                        $item('▦','Yeni Gelenler','Yeni sezon ürünleri.','Yeni'),
                        $item('★','Çok Satanlar','Müşteri favorileri.'),
                        $item('♡','Kampanyalar','Seçili ürünlerde fırsatlar.','%30'),
                        $item('→','Tüm Ürünler','Tüm koleksiyonu keşfedin.')
                    ),'cards','2'),
                    $links('Yardım',array(
                        $item('◎','Sipariş Takibi','Sipariş durumunu kontrol edin.'),
                        $item('⚙','Destek','Müşteri hizmetlerine ulaşın.'),
                        $item('↗','İade & Değişim','Kolay iade süreci.')
                    ),'compact','1'),
                    $banner('YENİ SEZON','Yeni koleksiyon yayında','Yeni sezon ürünlerini ve avantajları keşfedin.','shop.svg','Alışverişe Başla')
                ),array(43,22,35))
            ),$base));
        }

        if('creative'===$key){
            return array(self::container(array(
                $quick(array(
                    array('icon'=>'✦','title'=>'Branding','url'=>array('url'=>'#')),
                    array('icon'=>'◈','title'=>'Web','url'=>array('url'=>'#')),
                    array('icon'=>'↗','title'=>'Motion','url'=>array('url'=>'#')),
                    array('icon'=>'◎','title'=>'Cases','url'=>array('url'=>'#'))
                )),
                $row(array(
                    $links('Creative Studio',array(
                        $item('✦','Brand Strategy','Marka yönü ve konumlandırma.'),
                        $item('◈','Web Experience','Yaratıcı dijital deneyimler.'),
                        $item('↗','Motion & Interaction','Etkileşimli tasarım.'),
                        $item('◎','Campaign','Lansman ve kampanya.')
                    ),'cards','2'),
                    $banner('SELECTED WORK','Fark edilen işler üretin','Cesur fikirleri modern ve akılda kalıcı deneyimlere dönüştürün.','agency.svg','Projeyi Gör')
                ),array(60,40))
            ),$base));
        }

        if('software'===$key){
            return array(self::container(array(
                $quick(array(
                    array('icon'=>'</>','title'=>'Platform','url'=>array('url'=>'#')),
                    array('icon'=>'▦','title'=>'Entegrasyon','url'=>array('url'=>'#')),
                    array('icon'=>'↗','title'=>'Otomasyon','url'=>array('url'=>'#')),
                    array('icon'=>'◎','title'=>'Analitik','url'=>array('url'=>'#'))
                )),
                $row(array(
                    $links('Ürün',array(
                        $item('</>','Platform','Tüm özellikleri keşfedin.','v3'),
                        $item('▦','Entegrasyonlar','Kullandığınız araçlara bağlanın.'),
                        $item('↗','Otomasyon','Tekrarlayan işleri hızlandırın.'),
                        $item('◎','Analitik','Verilerinizi anlamlandırın.')
                    ),'cards','2'),
                    $links('Kaynaklar',array(
                        $item('□','Dokümantasyon','Kurulum ve kullanım rehberi.'),
                        $item('⚙','Destek Merkezi','Teknik yardım alın.'),
                        $item('★','Changelog','Yeni özellikleri takip edin.')
                    ),'compact','1'),
                    $banner('PRODUCT UPDATE','Yeni nesil çalışma alanı','Hızlı, esnek ve ölçeklenebilir ürün deneyimi.','saas.svg','Demo İzle')
                ),array(44,22,34))
            ),$base));
        }

        if('industry'===$key){
            return array(self::container(array(
                $quick(array(
                    array('icon'=>'⚙','title'=>'CNC','url'=>array('url'=>'#')),
                    array('icon'=>'◈','title'=>'Erozyon','url'=>array('url'=>'#')),
                    array('icon'=>'▦','title'=>'Taşlama','url'=>array('url'=>'#')),
                    array('icon'=>'↗','title'=>'Servis','url'=>array('url'=>'#'))
                )),
                $row(array(
                    $links('Makina Grupları',array(
                        $item('⚙','CNC Tezgâhları','Hassas üretim çözümleri.'),
                        $item('◈','Erozyon Sistemleri','EDM ve tel erozyon.'),
                        $item('▦','Taşlama','Yüzey ve silindirik taşlama.'),
                        $item('↗','İkinci El','Kontrollü makinalar.','Stok')
                    ),'cards','2'),
                    $links('Teknik Destek',array(
                        $item('◎','Teknik Servis','Bakım ve onarım.'),
                        $item('□','Yedek Parça','Parça ve sarf.'),
                        $item('→','Teklif İste','Size özel teklif.')
                    ),'compact','1'),
                    $banner('ENDÜSTRİ','Üretim gücünüzü artırın','Makina, servis ve mühendislik çözümleri.','industry.svg','Teknik Bilgi Al')
                ),array(45,21,34))
            ),$base));
        }

        if('hospitality'===$key){
            return array(self::container(array(
                $quick(array(
                    array('icon'=>'✦','title'=>'Odalar','url'=>array('url'=>'#')),
                    array('icon'=>'◎','title'=>'Wellness','url'=>array('url'=>'#')),
                    array('icon'=>'★','title'=>'Gastronomi','url'=>array('url'=>'#')),
                    array('icon'=>'→','title'=>'Rezervasyon','url'=>array('url'=>'#'))
                )),
                $row(array(
                    $links('Deneyimler',array(
                        $item('✦','Odalar & Süitler','Konforlu konaklama.'),
                        $item('◎','Spa & Wellness','Dinlenme ve yenilenme.'),
                        $item('★','Gastronomi','Restoran ve bar deneyimi.'),
                        $item('↗','Aktiviteler','Bölgeyi keşfedin.')
                    ),'cards','2'),
                    $links('Bilgi',array(
                        $item('□','Galeri','Otel deneyimini görün.'),
                        $item('◎','Konum','Ulaşım bilgileri.'),
                        $item('→','İletişim','Rezervasyon ekibi.')
                    ),'compact','1'),
                    $banner('BOOK DIRECT','Özel konaklama deneyimi','Doğrudan rezervasyon avantajlarını keşfedin.','hotel.svg','Müsaitliği Gör')
                ),array(45,21,34))
            ),$base));
        }

        return array(self::container(array(
            $quick(array(
                array('icon'=>'→','title'=>'Hakkımızda','url'=>array('url'=>'#')),
                array('icon'=>'◈','title'=>'Hizmetler','url'=>array('url'=>'#')),
                array('icon'=>'★','title'=>'Projeler','url'=>array('url'=>'#')),
                array('icon'=>'◎','title'=>'İletişim','url'=>array('url'=>'#'))
            )),
            $row(array(
                $links('Hızlı Bağlantılar',array(
                    $item('→','Hakkımızda','Markamızı tanıyın.'),
                    $item('◈','Hizmetler','Çözümleri keşfedin.'),
                    $item('★','Projeler','Referansları inceleyin.'),
                    $item('◎','İletişim','Bize ulaşın.')
                ),'list','2'),
                $banner('WPSOFT','Modern Mega Menü','Elementor ile kolayca özelleştirin.','corporate.svg','İncele')
            ),array(64,36))
        ),$base));
    }

    public static function create_preset() {
        if ( ! current_user_can( 'edit_posts' ) ) wp_die('Yetkiniz yok.');
        $key = isset($_GET['preset']) ? sanitize_key(wp_unslash($_GET['preset'])) : '';
        $presets = self::presets();
        if(!isset($presets[$key])) wp_die('Şablon bulunamadı.');
        check_admin_referer('wpst_create_mega_preset_'.$key);

        if ( ! did_action('elementor/loaded') || ! class_exists('\\Elementor\\Plugin') ) {
            wp_die('Elementor etkin olmalıdır.');
        }

        $id = wp_insert_post(array(
            'post_title' => 'WPSoft Mega Menü - '.$presets[$key]['title'],
            'post_type' => 'elementor_library',
            'post_status' => 'publish',
        ), true);

        if(is_wp_error($id)) wp_die(esc_html($id->get_error_message()));

        update_post_meta($id,'_elementor_edit_mode','builder');
        update_post_meta($id,'_elementor_template_type','section');
        update_post_meta($id,'_elementor_version',defined('ELEMENTOR_VERSION')?ELEMENTOR_VERSION:'3.0.0');
        update_post_meta($id,self::TEMPLATE_META,'1');
        update_post_meta($id,'_elementor_data',wp_slash(wp_json_encode(self::preset_data($key))));

        wp_safe_redirect(admin_url('post.php?post='.$id.'&action=elementor'));
        exit;
    }

    private static function create_template_url() {
        return wp_nonce_url(
            admin_url('admin-post.php?action=wpst_create_menu_template'),
            'wpst_create_menu_template'
        );
    }

    public static function create_template() {
        if ( ! current_user_can( 'edit_posts' ) ) wp_die('Yetkiniz yok.');
        check_admin_referer('wpst_create_menu_template');

        if ( ! did_action('elementor/loaded') || ! class_exists('\\Elementor\\Plugin') ) {
            wp_die('Elementor etkin olmalıdır.');
        }

        $id = wp_insert_post(array(
            'post_title' => 'WPSoft Mega Menü ' . current_time('d.m.Y H:i'),
            'post_type' => 'elementor_library',
            'post_status' => 'publish',
        ), true);

        if ( is_wp_error($id) ) wp_die( esc_html($id->get_error_message()) );

        update_post_meta($id,'_elementor_edit_mode','builder');
        update_post_meta($id,'_elementor_template_type','section');
        update_post_meta($id,'_elementor_version',defined('ELEMENTOR_VERSION')?ELEMENTOR_VERSION:'3.0.0');
        update_post_meta($id,self::TEMPLATE_META,'1');

        // Starter content: a container with heading + icon grid.
        // Starter content: menu-focused widgets, ready to edit.
        $data = array(
            self::container(array(
                self::widget('wpsoft-mega-links',array(
                    'title'=>'Hızlı Bağlantılar',
                    'style'=>'cards',
                    'columns'=>'2',
                    'items'=>array(
                        array('icon'=>'◈','title'=>'Hizmetler','text'=>'Çözümlerimizi keşfedin.','badge'=>'','url'=>array('url'=>'#')),
                        array('icon'=>'★','title'=>'Projeler','text'=>'Seçili işlerimizi görün.','badge'=>'','url'=>array('url'=>'#')),
                        array('icon'=>'◎','title'=>'Hakkımızda','text'=>'Bizi daha yakından tanıyın.','badge'=>'','url'=>array('url'=>'#')),
                        array('icon'=>'→','title'=>'İletişim','text'=>'Yeni projenizi konuşalım.','badge'=>'','url'=>array('url'=>'#'))
                    )
                )),
                self::widget('wpsoft-mega-promo',array(
                    'eyebrow'=>'ÖNE ÇIKAN',
                    'title'=>'Mega menünüz hazır',
                    'text'=>'Bağlantıları, açıklamaları ve görsel alanı Elementor içinden kolayca değiştirin.',
                    'image'=>array('url'=>WPST_URL.'assets/images/demo/corporate.svg'),
                    'button_text'=>'Detaylı İncele','button_url'=>array('url'=>'#')
                ))
            ),array(
                'content_width'=>'boxed',
                'width'=>array('unit'=>'px','size'=>1180,'sizes'=>array()),
                'padding'=>array('unit'=>'px','top'=>'18','right'=>'18','bottom'=>'18','left'=>'18','isLinked'=>true),
                'gap'=>array('unit'=>'px','size'=>16,'sizes'=>array()),
                'flex_direction'=>'row',
                'align_items'=>'stretch'
            )),
        );
        update_post_meta($id,'_elementor_data',wp_slash(wp_json_encode($data)));

        wp_safe_redirect(admin_url('post.php?post='.$id.'&action=elementor'));
        exit;
    }

    public static function delete_template() {
        if ( ! current_user_can( 'manage_options' ) ) wp_die('Yetkiniz yok.');
        $id = isset($_GET['template_id']) ? absint($_GET['template_id']) : 0;
        check_admin_referer('wpst_delete_menu_template_'.$id);
        if(!$id || '1' !== get_post_meta($id,self::TEMPLATE_META,true)) wp_die('Şablon bulunamadı.');
        wp_trash_post($id);
        wp_safe_redirect(admin_url('admin.php?page=wpsoft-my-templates&deleted=1'));
        exit;
    }

    public static function save() {
        if ( ! current_user_can( 'manage_options' ) ) wp_die('Yetkiniz yok.');
        check_admin_referer('wpst_save_mega_menu');

        $raw = isset($_POST['mega']) && is_array($_POST['mega']) ? wp_unslash($_POST['mega']) : array();
        $out = array();

        foreach($raw as $id=>$cfg){
            $id = absint($id);
            if(!$id || !is_array($cfg)) continue;

            $mode = isset($cfg['mode']) ? sanitize_key($cfg['mode']) : 'columns';
            if(!in_array($mode,array('columns','dynamic','elementor'),true)) $mode='columns';

            $cols = isset($cfg['cols']) ? absint($cfg['cols']) : 3;
            if(!in_array($cols,array(2,3,4,5,6),true)) $cols=3;

            $width = isset($cfg['width']) ? sanitize_key($cfg['width']) : 'wide';
            if(!in_array($width,array('menu','wide','full'),true)) $width='wide';

            $template_id = isset($cfg['template_id']) ? absint($cfg['template_id']) : 0;
            if($template_id && '1' !== get_post_meta($template_id,self::TEMPLATE_META,true)) {
                $template_id = 0;
            }

            $out[$id] = array(
                'enabled' => !empty($cfg['enabled']) ? 1 : 0,
                'mode' => $mode,
                'cols' => $cols,
                'width' => $width,
                'template_id' => $template_id,
                'promo_title' => isset($cfg['promo_title']) ? sanitize_text_field($cfg['promo_title']) : '',
                'promo_text' => isset($cfg['promo_text']) ? sanitize_text_field($cfg['promo_text']) : '',
                'show_column_titles' => 1,
                'panel_bg' => isset($cfg['panel_bg']) && sanitize_hex_color($cfg['panel_bg']) ? sanitize_hex_color($cfg['panel_bg']) : '#ffffff',
                'panel_radius' => isset($cfg['panel_radius']) ? max(0,min(50,absint($cfg['panel_radius']))) : 22,
                'panel_shadow' => isset($cfg['panel_shadow']) && in_array($cfg['panel_shadow'],array('none','soft','medium','strong'),true) ? $cfg['panel_shadow'] : 'soft',
                'item_style' => isset($cfg['item_style']) && in_array($cfg['item_style'],array('cards','list','compact'),true) ? $cfg['item_style'] : 'cards',
                'density' => isset($cfg['density']) && in_array($cfg['density'],array('comfortable','compact'),true) ? $cfg['density'] : 'comfortable',
                'promo_button_text' => isset($cfg['promo_button_text']) ? sanitize_text_field($cfg['promo_button_text']) : '',
                'promo_button_url' => isset($cfg['promo_button_url']) ? esc_url_raw($cfg['promo_button_url']) : '',
                'mobile_accordion' => !empty($cfg['mobile_accordion']) ? 1 : 0,
                'dynamic_source' => isset($cfg['dynamic_source']) && in_array($cfg['dynamic_source'],array('none','posts','categories','products'),true) ? $cfg['dynamic_source'] : 'none',
                'dynamic_count' => isset($cfg['dynamic_count']) ? max(1,min(12,absint($cfg['dynamic_count']))) : 4,
                'dynamic_category' => isset($cfg['dynamic_category']) ? absint($cfg['dynamic_category']) : 0,
                'dynamic_images' => !empty($cfg['dynamic_images']) ? 1 : 0,
                'panel_sticky' => !empty($cfg['panel_sticky']) ? 1 : 0,
                'layout_variant' => isset($cfg['layout_variant']) && in_array($cfg['layout_variant'],array('cards','list','tabs'),true) ? $cfg['layout_variant'] : 'cards',
                'panel_align' => isset($cfg['panel_align']) && in_array($cfg['panel_align'],array('left','center','right'),true) ? $cfg['panel_align'] : 'center',
                'open_animation' => isset($cfg['open_animation']) && in_array($cfg['open_animation'],array('fade','slide','scale','none'),true) ? $cfg['open_animation'] : 'slide',
            );
        }

        update_option(self::OPTION,$out);
        wp_safe_redirect(admin_url('admin.php?page=wpsoft-mega-menu&updated=1'));
        exit;
    }

    public static function menu_classes($classes,$item,$args,$depth) {
        if ( 0 !== (int)$depth ) return $classes;

        $cfg = self::item_config((int)$item->ID);
        if ( empty($cfg['enabled']) ) return $classes;

        $classes[]='wpst-mega-enabled';
        $classes[]='wpst-mega-mode-'.sanitize_html_class($cfg['mode']);
        $classes[]='wpst-mega-cols-'.(int)$cfg['cols'];
        $classes[]='wpst-mega-width-'.sanitize_html_class($cfg['width']);
        $classes[]='wpst-mega-shadow-'.sanitize_html_class($cfg['panel_shadow']);
        $classes[]='wpst-mega-items-'.sanitize_html_class($cfg['item_style']);
        $classes[]='wpst-mega-density-'.sanitize_html_class($cfg['density']);
        if ( ! empty($cfg['mobile_accordion']) ) $classes[]='wpst-mega-mobile-accordion';
        if ( ! empty($cfg['panel_sticky']) ) $classes[]='wpst-mega-panel-sticky';
        $classes[]='wpst-mega-layout-'.sanitize_html_class($cfg['layout_variant']);
        $classes[]='wpst-mega-align-'.sanitize_html_class($cfg['panel_align']);
        $classes[]='wpst-mega-animation-'.sanitize_html_class($cfg['open_animation']);

        return $classes;
    }

    public static function link_attrs($atts,$item,$args,$depth) {
        if ( !empty($args->wpst_mobile_drawer) ) $atts['class']=trim((isset($atts['class'])?$atts['class'].' ':'').'wps-mobile-drawer__item');
        if ( 0 !== (int)$depth ) return $atts;

        $cfg = self::item_config((int)$item->ID);
        if ( empty($cfg['enabled']) ) return $atts;

        $atts['data-wpst-mega']='1';
        $atts['aria-haspopup']='true';
        $atts['data-wpst-panel-bg']=sanitize_hex_color($cfg['panel_bg']) ?: '#ffffff';
        $atts['data-wpst-panel-radius']=absint($cfg['panel_radius']);
        $atts['data-wpst-layout']=sanitize_key($cfg['layout_variant']);
        $atts['data-wpst-sticky-panel']=!empty($cfg['panel_sticky'])?'1':'0';
        $atts['data-wpst-panel-align']=sanitize_key($cfg['panel_align']);
        $atts['data-wpst-open-animation']=sanitize_key($cfg['open_animation']);

        if('columns'===$cfg['mode'] && !empty($cfg['promo_title'])){
            $atts['data-wpst-promo-title']=$cfg['promo_title'];
            $atts['data-wpst-promo-text']=$cfg['promo_text'];
            $atts['data-wpst-promo-button']=$cfg['promo_button_text'];
            $atts['data-wpst-promo-url']=$cfg['promo_button_url'];
        }

        return $atts;
    }

    private static function render_dynamic_panel($cfg){
        $source=$cfg['dynamic_source'];
        $count=max(1,min(12,absint($cfg['dynamic_count'])));
        $show_images=!empty($cfg['dynamic_images']);
        $html='<div class="wpst-mega-dynamic-panel is-'.esc_attr($cfg['layout_variant']).'">';

        if('categories'===$source){
            $cats=get_categories(array('hide_empty'=>true,'number'=>$count,'orderby'=>'count','order'=>'DESC'));
            foreach($cats as $cat){
                $html.='<a class="wpst-mega-dynamic-card" href="'.esc_url(get_category_link($cat)).'"><span class="wpst-mega-dynamic-icon">#</span><div><strong>'.esc_html($cat->name).'</strong><small>'.absint($cat->count).' yazı</small></div></a>';
            }
        }elseif('products'===$source && post_type_exists('product')){
            $q=new \WP_Query(array('post_type'=>'product','post_status'=>'publish','posts_per_page'=>$count,'no_found_rows'=>true));
            while($q->have_posts()){ $q->the_post();
                $html.='<a class="wpst-mega-dynamic-card" href="'.esc_url(get_permalink()).'">';
                if($show_images && has_post_thumbnail()) $html.='<span class="wpst-mega-dynamic-image">'.get_the_post_thumbnail(get_the_ID(),'thumbnail').'</span>';
                $html.='<div><strong>'.esc_html(get_the_title()).'</strong>';
                if(function_exists('wc_get_product')){$product=wc_get_product(get_the_ID());if($product)$html.='<small>'.$product->get_price_html().'</small>';}
                $html.='</div></a>';
            }
            wp_reset_postdata();
        }else{
            $args=array('post_type'=>'post','post_status'=>'publish','posts_per_page'=>$count,'no_found_rows'=>true);
            if(!empty($cfg['dynamic_category']))$args['cat']=absint($cfg['dynamic_category']);
            $q=new \WP_Query($args);
            while($q->have_posts()){ $q->the_post();
                $html.='<a class="wpst-mega-dynamic-card" href="'.esc_url(get_permalink()).'">';
                if($show_images && has_post_thumbnail())$html.='<span class="wpst-mega-dynamic-image">'.get_the_post_thumbnail(get_the_ID(),'thumbnail').'</span>';
                $html.='<div><small>'.esc_html(get_the_date()).'</small><strong>'.esc_html(get_the_title()).'</strong></div></a>';
            }
            wp_reset_postdata();
        }
        $html.='</div>';
        return $html;
    }

    public static function render_elementor_template($item_output,$item,$depth,$args) {
        if ( 0 !== (int)$depth ) return $item_output;

        $cfg = self::item_config((int)$item->ID);
        if ( empty($cfg['enabled']) ) return $item_output;

        if ( 'dynamic' === $cfg['mode'] ) {
            return $item_output . self::render_dynamic_panel($cfg);
        }

        if ( 'elementor' !== $cfg['mode'] || empty($cfg['template_id']) ) return $item_output;

        if ( ! class_exists('\\Elementor\\Plugin') ) return $item_output;

        $content = \Elementor\Plugin::$instance->frontend->get_builder_content_for_display(
            (int)$cfg['template_id'],
            true
        );

        if ( ! $content ) return $item_output;

        // Added right after top-level anchor. JS/CSS treats this as the dropdown.
        $item_output .= '<div class="wpst-elementor-mega-panel" data-wpst-template="'.(int)$cfg['template_id'].'">'.$content.'</div>';

        return $item_output;
    }

    public static function admin_assets($hook) {
        $admin_page = !empty($_GET['page']) ? sanitize_key(wp_unslash($_GET['page'])) : '';
        $is_builder = 'wpsoft-mega-menu' === $admin_page;
        $is_templates = 'wpsoft-my-templates' === $admin_page;
        $is_nav = 'nav-menus.php' === $hook;
        if ( ! $is_builder && ! $is_templates && ! $is_nav ) return;
        wp_enqueue_media();
        wp_enqueue_style('wpst-mega-menu-admin',WPST_URL.'assets/css/mega-menu-admin.css',array(),WPST_VERSION);
        wp_enqueue_script('wpst-mega-menu-admin',WPST_URL.'assets/js/mega-menu-admin.js',array('jquery'),WPST_VERSION,true);
        if($is_nav && class_exists('WPST_Icon_Library')){
            $svgs=array();foreach(WPST_Icon_Library::options() as $slug=>$label)$svgs[$slug]=WPST_Icon_Library::svg($slug,array('size'=>22,'class'=>'wpst-menu-icon-svg'));
            wp_localize_script('wpst-mega-menu-admin','wpstMenuIcons',array('svgs'=>$svgs));
        }
    }

    private static function has_enabled_mega_menu() {
        static $has = null;
        if ( null !== $has ) return $has;

        $settings = self::settings();
        $has = false;
        foreach ( $settings as $cfg ) {
            if ( is_array($cfg) && ! empty($cfg['enabled']) ) {
                $has = true;
                break;
            }
        }
        return $has;
    }

    public static function assets() {
        if ( is_admin() || ! self::has_enabled_mega_menu() ) return;

        wp_enqueue_style('wpst-mega-menu',WPST_URL.'assets/css/mega-menu.css',array(),WPST_VERSION);
        wp_enqueue_script('wpst-mega-menu',WPST_URL.'assets/js/mega-menu.js',array(),WPST_VERSION,true);
    }

    // Appearance > Menus integration: compact, advanced options without forcing the WPSoft page.
    public static function menu_item_fields($item_id,$menu_item,$depth,$args,$current_object_id) {
        if ( (int)$depth !== 0 ) {
            self::render_native_icon_field($item_id);
            return;
        }

        $cfg = self::item_config($item_id);
        $templates = self::menu_templates();

        echo '<div class="wpst-native-mega-fields" style="padding:10px 12px;background:#f8fafc;border-radius:8px;margin:8px 0">';
        echo '<p class="description description-wide"><label><input type="checkbox" name="wpst_mega_native['.(int)$item_id.'][enabled]" value="1" '.checked(!empty($cfg['enabled']),true,false).'> <strong>WPSoft Mega Menü</strong></label></p>';
        echo '<p class="description description-wide"><label>Tür<br><select name="wpst_mega_native['.(int)$item_id.'][mode]" style="width:100%"><option value="columns" '.selected($cfg['mode'],'columns',false).'>Kolonlu Mega Menü</option><option value="elementor" '.selected($cfg['mode'],'elementor',false).'>Elementor Menü Şablonu</option></select></label></p>';
        echo '<p class="description description-thin"><label>Kolon<br><select name="wpst_mega_native['.(int)$item_id.'][cols]"><option value="2" '.selected((int)$cfg['cols'],2,false).'>2</option><option value="3" '.selected((int)$cfg['cols'],3,false).'>3</option><option value="4" '.selected((int)$cfg['cols'],4,false).'>4</option><option value="5" '.selected((int)$cfg['cols'],5,false).'>5</option><option value="6" '.selected((int)$cfg['cols'],6,false).'>6</option></select></label></p>';
        echo '<p class="description description-thin"><label>Genişlik<br><select name="wpst_mega_native['.(int)$item_id.'][width]"><option value="menu" '.selected($cfg['width'],'menu',false).'>Kompakt</option><option value="wide" '.selected($cfg['width'],'wide',false).'>Container</option><option value="full" '.selected($cfg['width'],'full',false).'>Tam</option></select></label></p>';
        echo '<p class="description description-thin"><label>Panel Hizası<br><select name="wpst_mega_native['.(int)$item_id.'][panel_align]"><option value="left" '.selected($cfg['panel_align'],'left',false).'>Sol</option><option value="center" '.selected($cfg['panel_align'],'center',false).'>Orta</option><option value="right" '.selected($cfg['panel_align'],'right',false).'>Sağ</option></select></label></p>';
        echo '<p class="description description-thin"><label>Animasyon<br><select name="wpst_mega_native['.(int)$item_id.'][open_animation]"><option value="fade" '.selected($cfg['open_animation'],'fade',false).'>Fade</option><option value="slide" '.selected($cfg['open_animation'],'slide',false).'>Slide</option><option value="scale" '.selected($cfg['open_animation'],'scale',false).'>Scale</option><option value="none" '.selected($cfg['open_animation'],'none',false).'>Yok</option></select></label></p>';
        echo '<p class="description description-thin"><label>Link Stili<br><select name="wpst_mega_native['.(int)$item_id.'][item_style]"><option value="cards" '.selected($cfg['item_style'],'cards',false).'>Kart</option><option value="list" '.selected($cfg['item_style'],'list',false).'>Liste</option><option value="compact" '.selected($cfg['item_style'],'compact',false).'>Kompakt</option></select></label></p>';
        echo '<p class="description description-thin"><label>Yoğunluk<br><select name="wpst_mega_native['.(int)$item_id.'][density]"><option value="comfortable" '.selected($cfg['density'],'comfortable',false).'>Rahat</option><option value="compact" '.selected($cfg['density'],'compact',false).'>Sıkı</option></select></label></p>';

        echo '<p class="description description-wide"><label>Elementor Şablonu<br><select name="wpst_mega_native['.(int)$item_id.'][template_id]" style="width:100%"><option value="0">Şablon seç</option>';
        foreach($templates as $tpl) echo '<option value="'.(int)$tpl->ID.'" '.selected((int)$cfg['template_id'],(int)$tpl->ID,false).'>'.esc_html($tpl->post_title).'</option>';
        echo '</select></label></p>';

        $meta = self::item_meta($item_id);
        echo '<hr style="margin:10px 0;border:0;border-top:1px solid #e2e8f0">';
        echo '<p class="description description-wide"><strong>WPSoft Menü Görünümü</strong></p>';
        echo '<p class="description description-thin"><label>Badge<br><input type="text" name="wpst_mega_item_meta['.(int)$item_id.'][badge]" value="'.esc_attr($meta['badge']).'" placeholder="Yeni"></label></p>';
        echo '<p class="description description-thin"><label>Badge Rengi<br><input type="color" name="wpst_mega_item_meta['.(int)$item_id.'][badge_color]" value="'.esc_attr($meta['badge_color']).'"></label></p>';
        self::render_native_icon_field($item_id,$meta['icon'],false);
        echo '<p class="description description-wide"><label>Kısa Açıklama<br><input type="text" style="width:100%" name="wpst_mega_item_meta['.(int)$item_id.'][description]" value="'.esc_attr($meta['description']).'" placeholder="Kısa açıklama"></label></p>';
        echo '<p class="description description-wide"><label><input type="checkbox" name="wpst_mega_item_meta['.(int)$item_id.'][column_title]" value="1" '.checked(!empty($meta['column_title']),true,false).'> Bu öğeyi kolon başlığı gibi göster</label></p>';

        $image = $meta['image_id'] ? wp_get_attachment_image_url($meta['image_id'],'thumbnail') : '';
        echo '<div class="wpst-menu-image-field" data-item-id="'.(int)$item_id.'">';
        echo '<input type="hidden" class="wpst-menu-image-id" name="wpst_mega_item_meta['.(int)$item_id.'][image_id]" value="'.absint($meta['image_id']).'">';
        echo '<div class="wpst-menu-image-preview">'.($image?'<img src="'.esc_url($image).'" alt="">':'<span>Görsel yok</span>').'</div>';
        echo '<button type="button" class="button wpst-menu-image-select">Görsel Seç</button> ';
        echo '<button type="button" class="button-link-delete wpst-menu-image-remove">Kaldır</button>';
        echo '</div>';
        echo '</div>';
    }

    public static function save_menu_item_fields($menu_id,$menu_item_db_id,$args) {
        if ( ! current_user_can('edit_theme_options') ) return;
        if ( empty($_POST['wpst_menu_icon_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['wpst_menu_icon_nonce'])),'wpst_menu_icon') ) return;
        $id = absint($menu_item_db_id);
        $meta_all = isset($_POST['wpst_mega_item_meta']) && is_array($_POST['wpst_mega_item_meta']) ? wp_unslash($_POST['wpst_mega_item_meta']) : array();
        if(isset($meta_all[$id]) && is_array($meta_all[$id])){
            $icon=isset($meta_all[$id]['icon'])?sanitize_key($meta_all[$id]['icon']):'';
            if($icon && !self::icon_svg($icon))$icon='';
            if($icon)update_post_meta($id,'_wpst_mega_icon',$icon);else delete_post_meta($id,'_wpst_mega_icon');
        }
        if ( empty($_POST['wpst_mega_native']) || !is_array($_POST['wpst_mega_native']) ) return;

        $all = self::settings();

        $raw_all = wp_unslash($_POST['wpst_mega_native']);
        if ( !isset($raw_all[$id]) || !is_array($raw_all[$id]) ) return;

        $cfg = $raw_all[$id];
        $old = self::item_config($id);

        $mode = isset($cfg['mode']) ? sanitize_key($cfg['mode']) : 'columns';
        if(!in_array($mode,array('columns','elementor'),true))$mode='columns';

        $cols = isset($cfg['cols']) ? absint($cfg['cols']) : 3;
        if(!in_array($cols,array(2,3,4,5,6),true))$cols=3;

        $width = isset($cfg['width']) ? sanitize_key($cfg['width']) : 'wide';
        if(!in_array($width,array('menu','wide','full'),true))$width='wide';

        $template_id = isset($cfg['template_id']) ? absint($cfg['template_id']) : 0;

        $all[$id]=array_merge($old,array(
            'enabled'=>!empty($cfg['enabled'])?1:0,
            'mode'=>$mode,
            'cols'=>$cols,
            'width'=>$width,
            'item_style'=>isset($cfg['item_style']) && in_array($cfg['item_style'],array('cards','list','compact'),true)?$cfg['item_style']:'cards',
            'density'=>isset($cfg['density']) && in_array($cfg['density'],array('comfortable','compact'),true)?$cfg['density']:'comfortable',
            'template_id'=>$template_id,
            'panel_align'=>isset($cfg['panel_align']) && in_array($cfg['panel_align'],array('left','center','right'),true)?$cfg['panel_align']:'center',
            'open_animation'=>isset($cfg['open_animation']) && in_array($cfg['open_animation'],array('fade','slide','scale','none'),true)?$cfg['open_animation']:'slide',
        ));

        update_option(self::OPTION,$all);

        if ( isset($_POST['wpst_mega_item_meta']) && is_array($_POST['wpst_mega_item_meta']) ) {
            if ( isset($meta_all[$id]) && is_array($meta_all[$id]) ) {
                $meta = $meta_all[$id];
                update_post_meta($id,'_wpst_mega_badge',isset($meta['badge'])?sanitize_text_field($meta['badge']):'');
                $badge_color = isset($meta['badge_color']) ? sanitize_hex_color($meta['badge_color']) : '';
                update_post_meta($id,'_wpst_mega_badge_color',$badge_color ?: '#2563eb');
                // Icon is saved above independently, including submenu items.
                update_post_meta($id,'_wpst_mega_image_id',isset($meta['image_id'])?absint($meta['image_id']):0);
                update_post_meta($id,'_wpst_mega_column_title',!empty($meta['column_title'])?'1':'0');
                update_post_meta($id,'_wpst_mega_description',isset($meta['description'])?sanitize_text_field($meta['description']):'');
            }
        }
    }

    private static function render_native_icon_field($item_id,$selected='',$wrap=true){
        if(''===$selected)$selected=sanitize_key(get_post_meta(absint($item_id),'_wpst_mega_icon',true));
        $icons=class_exists('WPST_Icon_Library')?WPST_Icon_Library::options():array('star'=>'Yıldız','briefcase'=>'Çanta','file'=>'Dosya');
        if($selected && !isset($icons[$selected]))$icons[$selected]=ucwords(str_replace(array('-','_'),' ',$selected));
        if($wrap)echo'<div class="wpst-native-mega-fields wpst-native-icon-only">';
        echo'<input type="hidden" name="wpst_menu_icon_nonce" value="'.esc_attr(wp_create_nonce('wpst_menu_icon')).'">';
        echo'<div class="wpst-menu-icon-field" data-item-id="'.absint($item_id).'">';
        echo'<p class="description description-wide"><strong>WPSoft Menü İkonu</strong><br><span>Bu ikon WPSoft Navigation mobil menüsünde ve desteklenen WPSoft menülerinde kullanılır.</span></p>';
        echo'<div class="wpst-menu-icon-picker"><span class="wpst-menu-icon-preview">'.($selected?self::icon_svg($selected):'<span class="dashicons dashicons-minus"></span>').'</span><select class="wpst-menu-icon-select" name="wpst_mega_item_meta['.absint($item_id).'][icon]"><option value="">İkon yok</option>';
        foreach($icons as $slug=>$label)echo'<option value="'.esc_attr($slug).'" '.selected($selected,$slug,false).'>'.esc_html($label).'</option>';
        echo'</select><button type="button" class="button wpst-menu-icon-remove">İkonu Kaldır</button></div></div>';
        if($wrap)echo'</div>';
    }

    public static function decorate_menu_item_title($title,$item,$args,$depth) {
        $meta = self::item_meta($item->ID);
        $prefix = '';
        $is_mobile = ! empty($args->wpst_mobile_drawer);
        $is_mega = self::is_mega_item_context($item);
        $icon = ($is_mobile || $is_mega) ? $meta['icon'] : '';
        if(!$icon && $is_mobile)$icon=self::default_menu_icon($item);
        if ( $icon ) {
            $svg = self::icon_svg($icon);
            if($svg){
                $icon_markup = '<span class="wpst-menu-item-icon">'.$svg.'</span>';
                $prefix .= $is_mobile ? '<span class="wps-mobile-drawer__icon">'.$icon_markup.'</span>' : $icon_markup;
            }
        }
        $suffix = '';
        if ( $meta['badge'] ) {
            $suffix .= '<span class="wpst-menu-badge" style="--wpst-badge-color:'.esc_attr($meta['badge_color']).'">'.esc_html($meta['badge']).'</span>';
        }
        return $prefix . '<span class="wpst-menu-item-label">'.$title.'</span>' . $suffix;
    }

    private static function is_mega_item_context($item) {
        $seen = array();
        $current = $item;
        while ( $current && ! empty($current->ID) && ! isset($seen[(int)$current->ID]) ) {
            $seen[(int)$current->ID] = true;
            $cfg = self::item_config((int)$current->ID);
            if ( ! empty($cfg['enabled']) ) return true;
            $parent_id = ! empty($current->menu_item_parent) ? absint($current->menu_item_parent) : 0;
            if ( ! $parent_id ) break;
            $current = wp_setup_nav_menu_item(get_post($parent_id));
        }
        return false;
    }

    private static function default_menu_icon($item){
        $home=untrailingslashit(home_url('/'));$url=isset($item->url)?untrailingslashit((string)$item->url):'';
        if($url===$home)return'home';
        if(absint($item->object_id)===absint(get_option('page_for_posts')))return'article';
        $slug='';if(!empty($item->object_id)){$post=get_post(absint($item->object_id));if($post)$slug=(string)$post->post_name;}
        if(!$slug&&$url){$path=(string)wp_parse_url($url,PHP_URL_PATH);$slug=basename(untrailingslashit($path));}
        $slug=sanitize_title(remove_accents($slug));
        if(in_array($slug,array('hakkimizda','about'),true))return'user';
        if(in_array($slug,array('hizmetlerimiz','hizmetler','services'),true))return'briefcase';
        if(in_array($slug,array('iletisim','contact'),true))return'mail';
        if('blog'===$slug)return'article';
        return'file';
    }

    public static function decorate_menu_item_output($item_output,$item,$depth,$args) {
        $meta = self::item_meta($item->ID);

        if ( ! empty($meta['column_title']) ) {
            $item_output = '<div class="wpst-mega-column-title">'.$item_output.'</div>';
        }

        if ( $meta['description'] ) {
            $item_output .= '<span class="wpst-menu-item-description">'.esc_html($meta['description']).'</span>';
        }

        if ( $meta['image_id'] ) {
            $url = wp_get_attachment_image_url($meta['image_id'],'medium');
            if($url) $item_output .= '<span class="wpst-menu-item-image"><img src="'.esc_url($url).'" alt="" loading="lazy"></span>';
        }

        return $item_output;
    }
}
