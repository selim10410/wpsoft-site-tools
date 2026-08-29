<?php
if ( ! defined( 'ABSPATH' ) ) exit;
final class WPST_Template_Library {
    public static function init(){
        add_action('admin_post_wpst_create_demo_page',array(__CLASS__,'create_page'));
        add_action('admin_post_wpst_create_blog_library_template',array(__CLASS__,'create_blog_library_template'));
    }

    private static function templates(){ return array(
        'contact-wpforms-modern'=>array('title'=>'İletişim · WPForms Modern','desc'=>'Modern split hero, iletişim kartları, WPForms form alanı, SSS ve CTA ile hazır iletişim sayfası.','accent'=>'#2563eb','preview'=>'contact.svg','sector'=>'İletişim','premium'=>1),
        'contact-wpforms-bento'=>array('title'=>'İletişim · Bento Form','desc'=>'Bento bilgi kartları, WPForms formu ve ofis/çalışma bilgileriyle modern iletişim sayfası.','accent'=>'#7c3aed','preview'=>'contact.svg','sector'=>'İletişim','premium'=>1),
        'contact-wpforms-dark'=>array('title'=>'İletişim · Dark Premium','desc'=>'Koyu premium hero, WPForms form kartı, güven alanları ve güçlü CTA ile modern iletişim sayfası.','accent'=>'#0ea5e9','preview'=>'contact.svg','sector'=>'İletişim','premium'=>1),
        'machinery-premium'=>array('title'=>'Makina / CNC · Technical Signature','desc'=>'Makine, CNC ve endüstriyel ürün firmaları için teknik ürün ve servis odaklı premium sayfa.','accent'=>'#0ea5e9','preview'=>'machinery-premium.svg','sector'=>'Makina','premium'=>1),
        'ecommerce-premium'=>array('title'=>'E-Ticaret · Commerce Signature','desc'=>'Kategori, ürün vitrinleri, güven unsurları ve kampanya alanları için premium mağaza ana sayfası.','accent'=>'#16a34a','preview'=>'ecommerce-premium.svg','sector'=>'E-Ticaret','premium'=>1),
        'architecture-premium'=>array('title'=>'Mimarlık · Architecture Signature','desc'=>'Projeler, yaklaşım, sayısal başarılar ve referanslarla premium mimarlık sayfası.','accent'=>'#a16207','preview'=>'architecture-premium.svg','sector'=>'İnşaat','premium'=>1),
        'consulting-premium'=>array('title'=>'Danışmanlık · Business Signature','desc'=>'Strateji, uzmanlık alanları, vaka çalışmaları, ekip ve randevu CTA akışına sahip premium danışmanlık sayfası.','accent'=>'#334155','preview'=>'consulting-premium.svg','sector'=>'Danışmanlık','premium'=>1),
        'realestate-premium'=>array('title'=>'Emlak · Property Signature','desc'=>'Öne çıkan portföy, lokasyonlar, danışmanlar ve iletişim akışıyla premium gayrimenkul ana sayfası.','accent'=>'#1d4ed8','preview'=>'realestate-premium.svg','sector'=>'Emlak','premium'=>1),
        'education-premium'=>array('title'=>'Eğitim · Academy Signature','desc'=>'Programlar, eğitmenler, başarı metrikleri ve kayıt CTA alanlarıyla modern eğitim ana sayfası.','accent'=>'#7c3aed','preview'=>'education-premium.svg','sector'=>'Eğitim','premium'=>1),
        'law-premium'=>array('title'=>'Hukuk · Legal Signature','desc'=>'Uzmanlık alanları, avukat profilleri, güven unsurları ve danışma CTA alanlarıyla premium hukuk sayfası.','accent'=>'#1e3a8a','preview'=>'law-premium.svg','sector'=>'Hukuk','premium'=>1),
        'restaurant-signature'=>array('title'=>'Restoran · Editorial Dining','desc'=>'Video/görsel hero, menü hikâyesi, şef alanı, zoom galeri ve rezervasyon CTA ile editorial restoran tasarımı.','accent'=>'#be123c','preview'=>'restaurant-premium.svg','sector'=>'Restoran','premium'=>1),
); }

    public static function page(){
        if(!current_user_can('manage_options'))return; $templates=self::templates();
        echo '<div class="wrap"><h1>WPSoft Elementor Şablonları</h1><p>Hazır sayfaları buradan oluşturabilir veya Elementor içindeki <strong>W / WPSoft Şablonlar</strong> butonundan mevcut sayfaya ekleyebilirsiniz.</p><div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:18px;max-width:1300px;margin-top:24px">';
        foreach($templates as $key=>$tpl){
            $url=wp_nonce_url(admin_url('admin-post.php?action=wpst_create_demo_page&template='.$key),'wpst_create_demo_'.$key);
            $img=WPST_URL.'assets/images/templates/'.$tpl['preview'];
            echo '<div style="background:#fff;border:1px solid #dcdcde;border-radius:16px;padding:12px;box-shadow:0 8px 30px rgba(0,0,0,.04)"><img src="'.esc_url($img).'" alt="" style="width:100%;height:170px;object-fit:cover;border-radius:12px;display:block"><div style="padding:10px 8px 8px"><h2 style="font-size:17px;margin:5px 0 7px">'.esc_html($tpl['title']).'</h2><p style="min-height:48px">'.esc_html($tpl['desc']).'</p><a class="button button-primary" href="'.esc_url($url).'">Yeni Sayfa Oluştur</a></div></div>';
        } echo '</div></div>';
    }

    public static function create_page(){
        if(!current_user_can('manage_options'))wp_die('Yetkiniz yok.');
        $key=sanitize_key($_GET['template']??''); check_admin_referer('wpst_create_demo_'.$key); $templates=self::templates();
        if(empty($templates[$key]))wp_die('Şablon bulunamadı.'); if(!did_action('elementor/loaded')||!class_exists('\\Elementor\\Plugin'))wp_die('Elementor etkin olmalıdır.');
        $page_id=wp_insert_post(array('post_title'=>'WPSoft - '.$templates[$key]['title'],'post_type'=>'page','post_status'=>'draft')); if(is_wp_error($page_id)||!$page_id)wp_die('Sayfa oluşturulamadı.');
        $data=self::elementor_data($key,$templates[$key]); update_post_meta($page_id,'_elementor_edit_mode','builder'); update_post_meta($page_id,'_elementor_template_type','wp-page'); update_post_meta($page_id,'_elementor_version',defined('ELEMENTOR_VERSION')?ELEMENTOR_VERSION:'3.0.0'); update_post_meta($page_id,'_elementor_data',wp_slash(wp_json_encode($data)));
        wp_safe_redirect(admin_url('post.php?post='.$page_id.'&action=elementor')); exit;
    }

    private static function uid(){ return substr(md5(uniqid('',true)),0,8); }
    private static function element($widget,$settings=array()){
        /*
         * v3.3.15 · Template Library Global Design contract
         * All newly inserted WPSoft template widgets follow Global Design by default.
         * Explicit template/widget values remain authoritative when intentionally set.
         */
        $global_defaults=array(
            'wpst_signature_preset'=>'global',
            'wpst_use_global_design'=>'yes',
            'wpst_design_mode'=>'global',
        );
        $settings=array_merge($global_defaults,(array)$settings);
        return array(
            'id'=>self::uid(),
            'elType'=>'widget',
            'widgetType'=>$widget,
            'settings'=>$settings,
            'elements'=>array()
        );
    }
    private static function container($elements=array(),$settings=array()){ return array('id'=>self::uid(),'elType'=>'container','settings'=>array_merge(array('content_width'=>'boxed','width'=>array('unit'=>'px','size'=>1200,'sizes'=>array()),'padding'=>array('unit'=>'px','top'=>'48','right'=>'24','bottom'=>'48','left'=>'24','isLinked'=>false)),$settings),'elements'=>$elements,'isInner'=>false); }
    private static function footer_row($elements=array(),$settings=array(),$widths=array()){
        $count=max(1,count($elements));
        if(!$widths){
            if($count===4)$widths=array(32,18,18,32);
            elseif($count===3)$widths=array(34,22,44);
            elseif($count===2)$widths=array(55,45);
            else $widths=array(100);
        }
        $cols=array();
        foreach($elements as $i=>$element){
            $w=isset($widths[$i])?(float)$widths[$i]:(100/$count);
            $cols[]=array(
                'id'=>self::uid(),
                'elType'=>'container',
                'settings'=>array(
                    'content_width'=>'full',
                    'width'=>array('unit'=>'%','size'=>$w,'sizes'=>array()),
                    'width_tablet'=>array('unit'=>'%','size'=>($count>=3?50:100),'sizes'=>array()),
                    'width_mobile'=>array('unit'=>'%','size'=>100,'sizes'=>array()),
                    'flex_direction'=>'column',
                    'gap'=>array('unit'=>'px','size'=>14,'row'=>14,'column'=>14),
                    'padding'=>array('unit'=>'px','top'=>'0','right'=>'10','bottom'=>'0','left'=>'10','isLinked'=>false)
                ),
                'elements'=>array($element),
                'isInner'=>true
            );
        }
        return self::container($cols,array_merge(array(
            'flex_direction'=>'row',
            'flex_wrap'=>'wrap',
            'align_items'=>'flex-start',
            'gap'=>array('unit'=>'px','size'=>0,'row'=>28,'column'=>0)
        ),$settings));
    }
    private static function sample($name='abstract-blue.svg'){ return WPST_URL.'assets/images/samples/'.$name; }
    private static function preview($name){ return WPST_URL.'assets/images/templates/'.$name; }

    private static function demo($name='generic.svg'){
        return WPST_URL.'assets/images/demo/'.sanitize_file_name($name);
    }

    private static function demo_v2($name){
        return WPST_URL.'assets/images/demo-v2/'.sanitize_file_name($name);
    }

    private static function demo_for($key){
        $map=array(
            'corporate'=>'corporate.svg','corporate-premium'=>'corporate.svg',
            'agency'=>'agency.svg','agency-premium'=>'agency.svg',
            'industrial'=>'industry.svg','industrial-premium'=>'industry.svg',
            'construction'=>'architecture.svg','architecture-premium'=>'architecture.svg',
            'hotel'=>'hotel.svg','hotel-premium'=>'hotel.svg',
            'restaurant'=>'restaurant.svg','restaurant-premium'=>'restaurant.svg',
            'realestate'=>'architecture.svg',
            'health'=>'health.svg','clinic-premium'=>'health.svg',
            'automotive'=>'machinery.svg','machinery-premium'=>'machinery.svg',
            'software'=>'saas.svg','saas-premium'=>'saas.svg',
            'law'=>'corporate-alt.svg','education'=>'corporate-alt.svg',
            'beauty'=>'generic.svg','dentist'=>'health.svg','veterinary'=>'health.svg',
            'logistics'=>'industry.svg','energy'=>'industry.svg','finance'=>'corporate-alt.svg',
            'event'=>'agency.svg','personal'=>'agency.svg','gym'=>'generic.svg',
            'security'=>'corporate-alt.svg','cleaning'=>'corporate-alt.svg',
            'travel'=>'travel.svg','furniture'=>'shop.svg','printing'=>'agency.svg',
            'ecommerce-premium'=>'shop.svg'
        );
        return self::demo(isset($map[$key])?$map[$key]:'generic.svg');
    }



    private static function header_templates(){
        return array(
            array('key'=>'header-modern','title'=>'Header · Modern','desc'=>'Logo, merkez menü ve sağ CTA düzeni.','data'=>self::header_footer_data('header-modern')),
            array('key'=>'header-minimal','title'=>'Header · Minimal','desc'=>'Temiz ve sade navigasyon yapısı.','data'=>self::header_footer_data('header-minimal')),
            array('key'=>'header-business','title'=>'Header · Business','desc'=>'Kurumsal siteler için güçlü CTA düzeni.','data'=>self::header_footer_data('header-business')),
            array('key'=>'header-centered','title'=>'Header · Centered','desc'=>'Ortalanmış marka ve menü yaklaşımı.','data'=>self::header_footer_data('header-centered'))
        );
    }
    private static function footer_templates(){
        $base = WPST_URL.'assets/images/footer-templates/';
        return array(
            array('key'=>'footer-modern-split','title'=>'Footer · Modern Split','desc'=>'Marka, hizmet linkleri, newsletter ve sosyal alanlarla dengeli modern split footer.','preview_image'=>$base.'footer-modern-split.svg','data'=>self::header_footer_data('footer-modern-split')),
            array('key'=>'footer-corporate-grid','title'=>'Footer · Corporate Grid','desc'=>'4 kolon kurumsal navigasyon, logo, sosyal ve güven katmanı.','preview_image'=>$base.'footer-corporate-grid.svg','data'=>self::header_footer_data('footer-corporate-grid')),
            array('key'=>'footer-dark-cta','title'=>'Footer · Dark CTA','desc'=>'Morphing CTA, koyu premium gövde, linkler ve newsletter alanı.','preview_image'=>$base.'footer-dark-cta.svg','data'=>self::header_footer_data('footer-dark-cta')),
            array('key'=>'footer-minimal-center','title'=>'Footer · Minimal Center','desc'=>'Minimal tipografi, sosyal pill bağlantıları ve sade navigasyon.','preview_image'=>$base.'footer-minimal-center.svg','data'=>self::header_footer_data('footer-minimal-center')),
            array('key'=>'footer-saas','title'=>'Footer · SaaS Product','desc'=>'Ürün, kaynaklar, newsletter ve sosyal alanlarla SaaS odaklı footer.','preview_image'=>$base.'footer-saas.svg','data'=>self::header_footer_data('footer-saas')),
            array('key'=>'footer-contact-rich','title'=>'Footer · Contact Rich','desc'=>'İletişim kartları, sosyal bağlantılar, marka ve newsletter ile zengin footer.','preview_image'=>$base.'footer-contact.svg','data'=>self::header_footer_data('footer-contact-rich')),
            array('key'=>'footer-agency','title'=>'Footer · Creative Agency','desc'=>'Scroll reveal tipografi, morphing CTA ve yaratıcı agency footer.','preview_image'=>$base.'footer-agency.svg','data'=>self::header_footer_data('footer-agency')),
            array('key'=>'footer-shop','title'=>'Footer · E-Ticaret','desc'=>'Kampanya newsletter, mağaza linkleri ve müşteri hizmetleri odaklı footer.','preview_image'=>$base.'footer-shop.svg','data'=>self::header_footer_data('footer-shop')),

            array('key'=>'footer-mega-premium','title'=>'Footer · Mega Premium','desc'=>'Büyük CTA, 4 kolon bilgi mimarisi, newsletter ve sosyal alanlarla premium mega footer.','preview_image'=>$base.'footer-mega-premium.svg','data'=>self::header_footer_data('footer-mega-premium')),
            array('key'=>'footer-newsletter-pro','title'=>'Footer · Newsletter Pro','desc'=>'Büyük newsletter bölümü, marka, link kolonları ve sosyal alanlarla dönüşüm odaklı footer.','preview_image'=>$base.'footer-newsletter-pro.svg','data'=>self::header_footer_data('footer-newsletter-pro')),
            array('key'=>'footer-corporate-light','title'=>'Footer · Corporate Light','desc'=>'Açık renk kurumsal footer; çoklu kolon, iletişim ve minimal alt navigasyon.','preview_image'=>$base.'footer-corporate-light.svg','data'=>self::header_footer_data('footer-corporate-light')),
            array('key'=>'footer-shop-pro','title'=>'Footer · Shop Pro','desc'=>'E-ticaret için kampanya alanı, kategori linkleri, müşteri hizmetleri ve sosyal bloklar.','preview_image'=>$base.'footer-shop-pro.svg','data'=>self::header_footer_data('footer-shop-pro'))
        );
    }

    private static function header_footer_data($type){
        if(strpos($type,'header-')===0){
            $items=array(
                self::element('wpsoft-heading',array(
                    'eyebrow'=>'WPSOFT',
                    'title'=>'',
                    'description'=>'',
                    'wpst_heading_font_size'=>array('size'=>22,'unit'=>'px')
                )),
                self::element('wpsoft-navigation',array(
                    'menu_id'=>'0',
                    'fallback'=>'first',
                    'align'=>'center',
                    'active_style'=>'pill'
                ))
            );
            if('header-business'===$type || 'header-modern'===$type){
                $items[]=self::element('wpsoft-advanced-button',array('text'=>'Teklif Al','button_url'=>array('url'=>'#')));
            }
            return array(self::container($items,array(
                'padding'=>array('unit'=>'px','top'=>'16','right'=>'20','bottom'=>'16','left'=>'20','isLinked'=>false)
            )));
        }

        $dark=array(
            'background_background'=>'classic',
            'background_color'=>'#0b1120',
            'padding'=>array('unit'=>'px','top'=>'52','right'=>'34','bottom'=>'34','left'=>'34','isLinked'=>false)
        );
        $light=array(
            'background_background'=>'classic',
            'background_color'=>'#f8fafc',
            'padding'=>array('unit'=>'px','top'=>'52','right'=>'34','bottom'=>'34','left'=>'34','isLinked'=>false)
        );

        if($type==='footer-modern-split'){
            return array(
                self::footer_row(array(
                    self::element('wpsoft-footer-brand',array(
                        'brand'=>'WPSoft',
                        'text'=>'Modern web tasarım, e-ticaret, SEO ve özel dijital çözümler.',
                        'phone'=>'+90 212 000 00 00','email'=>'info@example.com'
                    )),
                    self::element('wpsoft-footer-links',array(
                        'title'=>'Hizmetler',
                        'items'=>array(
                            array('text'=>'Kurumsal Web','url'=>array('url'=>'#')),
                            array('text'=>'E-Ticaret','url'=>array('url'=>'#')),
                            array('text'=>'SEO','url'=>array('url'=>'#')),
                            array('text'=>'Bakım & Destek','url'=>array('url'=>'#'))
                        )
                    )),
                    self::element('wpsoft-footer-newsletter')
                ),array_merge($dark,array('gap'=>array('unit'=>'px','size'=>0,'row'=>30,'column'=>0)))),
                self::footer_row(array(
                    self::element('wpsoft-footer-social'),
                    self::element('wpsoft-info-strip')
                ),array('background_background'=>'classic','background_color'=>'#070b14','padding'=>array('unit'=>'px','top'=>'18','right'=>'34','bottom'=>'18','left'=>'34','isLinked'=>false)))
            );
        }

        if($type==='footer-corporate-grid'){
            return array(
                self::footer_row(array(
                    self::element('wpsoft-footer-brand',array('brand'=>'Şirket Adı','text'=>'Güvenilir kurumsal çözüm ortağınız.')),
                    self::element('wpsoft-footer-links',array('title'=>'Kurumsal','items'=>array(
                        array('text'=>'Hakkımızda','url'=>array('url'=>'#')),
                        array('text'=>'Yönetim','url'=>array('url'=>'#')),
                        array('text'=>'Kariyer','badge'=>'Yeni','url'=>array('url'=>'#')),
                        array('text'=>'İletişim','url'=>array('url'=>'#'))
                    ))),
                    self::element('wpsoft-footer-links',array('title'=>'Çözümler','items'=>array(
                        array('text'=>'Danışmanlık','url'=>array('url'=>'#')),
                        array('text'=>'Teknik Destek','url'=>array('url'=>'#')),
                        array('text'=>'Projeler','url'=>array('url'=>'#')),
                        array('text'=>'Servis','url'=>array('url'=>'#'))
                    ))),
                    self::element('wpsoft-footer-links',array('title'=>'Kaynaklar','items'=>array(
                        array('text'=>'Blog','url'=>array('url'=>'#')),
                        array('text'=>'SSS','url'=>array('url'=>'#')),
                        array('text'=>'Belgeler','url'=>array('url'=>'#')),
                        array('text'=>'Gizlilik','url'=>array('url'=>'#'))
                    )))
                ),array_merge($dark,array('gap'=>array('unit'=>'px','size'=>0,'row'=>30,'column'=>0)))),
                self::footer_row(array(
                    self::element('wpsoft-logo-marquee'),
                    self::element('wpsoft-footer-social')
                ),array('background_background'=>'classic','background_color'=>'#0f172a','padding'=>array('unit'=>'px','top'=>'20','right'=>'34','bottom'=>'20','left'=>'34','isLinked'=>false)))
            );
        }

        if($type==='footer-dark-cta'){
            return array(
                self::footer_row(array(
                    self::element('wpsoft-morphing-cta',array(
                        'eyebrow'=>'NEXT PROJECT',
                        'title'=>'Yeni projenizi birlikte hayata geçirelim',
                        'text'=>'Kısa bir görüşmeyle ihtiyacınızı netleştirelim.',
                        'button_text'=>'Başlayalım','button_url'=>array('url'=>'#iletisim')
                    ))
                ),array('background_background'=>'classic','background_color'=>'#020617','padding'=>array('unit'=>'px','top'=>'32','right'=>'34','bottom'=>'16','left'=>'34','isLinked'=>false))),
                self::footer_row(array(
                    self::element('wpsoft-footer-brand'),
                    self::element('wpsoft-footer-links',array('title'=>'Keşfet')),
                    self::element('wpsoft-footer-newsletter')
                ),array('background_background'=>'classic','background_color'=>'#020617','padding'=>array('unit'=>'px','top'=>'34','right'=>'34','bottom'=>'36','left'=>'34','isLinked'=>false)))
            );
        }

        if($type==='footer-minimal-center'){
            return array(
                self::footer_row(array(
                    self::element('wpsoft-gradient-heading',array(
                        'eyebrow'=>'WPSOFT',
                        'title'=>'Sade. Hızlı. Etkili.',
                        'text'=>'Markanızın ihtiyaç duyduğu modern dijital deneyim.'
                    )),
                    self::element('wpsoft-footer-social',array('title'=>'Sosyalde Buluşalım')),
                    self::element('wpsoft-footer-links',array(
                        'title'=>'',
                        'items'=>array(
                            array('text'=>'Hakkımızda','url'=>array('url'=>'#')),
                            array('text'=>'Projeler','url'=>array('url'=>'#')),
                            array('text'=>'Blog','url'=>array('url'=>'#')),
                            array('text'=>'İletişim','url'=>array('url'=>'#'))
                        )
                    ))
                ),array('background_background'=>'classic','background_color'=>'#18181b','padding'=>array('unit'=>'px','top'=>'64','right'=>'34','bottom'=>'44','left'=>'34','isLinked'=>false)))
            );
        }

        if($type==='footer-saas'){
            return array(
                self::footer_row(array(
                    self::element('wpsoft-footer-brand',array(
                        'brand'=>'Productly',
                        'text'=>'Takımların daha hızlı çalışmasını sağlayan yeni nesil platform.'
                    )),
                    self::element('wpsoft-footer-links',array('title'=>'Ürün','items'=>array(
                        array('text'=>'Özellikler','url'=>array('url'=>'#')),
                        array('text'=>'Entegrasyonlar','url'=>array('url'=>'#')),
                        array('text'=>'Fiyatlandırma','url'=>array('url'=>'#')),
                        array('text'=>'Changelog','badge'=>'v3','url'=>array('url'=>'#'))
                    ))),
                    self::element('wpsoft-footer-links',array('title'=>'Kaynaklar')),
                    self::element('wpsoft-footer-newsletter',array(
                        'eyebrow'=>'UPDATES',
                        'title'=>'Ürün güncellemelerini alın',
                        'text'=>'Yeni özellikler ve ürün notları doğrudan e-postanıza.'
                    ))
                ),array('background_background'=>'classic','background_color'=>'#070b17','padding'=>array('unit'=>'px','top'=>'54','right'=>'34','bottom'=>'36','left'=>'34','isLinked'=>false))),
                self::footer_row(array(
                    self::element('wpsoft-footer-social'),
                    self::element('wpsoft-info-strip')
                ),array('background_background'=>'classic','background_color'=>'#0d1322','padding'=>array('unit'=>'px','top'=>'18','right'=>'34','bottom'=>'18','left'=>'34','isLinked'=>false)))
            );
        }

        if($type==='footer-contact-rich'){
            return array(
                self::footer_row(array(
                    self::element('wpsoft-heading',array(
                        'eyebrow'=>'BİZE ULAŞIN',
                        'title'=>'Yeni projenizi konuşalım',
                        'description'=>'Telefon, e-posta veya sosyal medya üzerinden bize ulaşabilirsiniz.'
                    )),
                    self::element('wpsoft-contact-cards'),
                    self::element('wpsoft-footer-social')
                ),array('background_background'=>'classic','background_color'=>'#0c4a6e','padding'=>array('unit'=>'px','top'=>'50','right'=>'34','bottom'=>'34','left'=>'34','isLinked'=>false))),
                self::footer_row(array(
                    self::element('wpsoft-footer-brand',array('brand'=>'WPSoft','text'=>'İstanbul · Türkiye')),
                    self::element('wpsoft-footer-links',array('title'=>'Hızlı Menü')),
                    self::element('wpsoft-footer-newsletter',array('title'=>'İletişimde Kalın'))
                ),array('background_background'=>'classic','background_color'=>'#083b58','padding'=>array('unit'=>'px','top'=>'32','right'=>'34','bottom'=>'36','left'=>'34','isLinked'=>false)))
            );
        }

        if($type==='footer-agency'){
            return array(
                self::footer_row(array(
                    self::element('wpsoft-scroll-reveal-text',array(
                        'eyebrow'=>'LET’S CREATE',
                        'text'=>'Bir sonraki güçlü dijital deneyimi birlikte oluşturalım.'
                    )),
                    self::element('wpsoft-morphing-cta',array(
                        'eyebrow'=>'START A PROJECT','title'=>'Fikrinizi anlatın',
                        'text'=>'Markanızı fark edilir hale getirelim.',
                        'button_text'=>'Mesaj Gönder','button_url'=>array('url'=>'#iletisim')
                    ))
                ),array('background_background'=>'classic','background_color'=>'#2e1065','padding'=>array('unit'=>'px','top'=>'54','right'=>'34','bottom'=>'32','left'=>'34','isLinked'=>false))),
                self::footer_row(array(
                    self::element('wpsoft-footer-brand',array('brand'=>'Creative Studio')),
                    self::element('wpsoft-footer-links',array('title'=>'Studio')),
                    self::element('wpsoft-footer-social')
                ),array('background_background'=>'classic','background_color'=>'#220c4a','padding'=>array('unit'=>'px','top'=>'30','right'=>'34','bottom'=>'36','left'=>'34','isLinked'=>false)))
            );
        }

        if($type==='footer-shop'){
            return array(
                self::footer_row(array(
                    self::element('wpsoft-footer-newsletter',array(
                        'eyebrow'=>'KAMPANYALAR',
                        'title'=>'Yeni ürün ve fırsatları kaçırmayın',
                        'text'=>'Kampanya ve yeni koleksiyonları ilk siz öğrenin.'
                    )),
                    self::element('wpsoft-info-strip')
                ),array('background_background'=>'classic','background_color'=>'#ecfdf5','padding'=>array('unit'=>'px','top'=>'36','right'=>'34','bottom'=>'30','left'=>'34','isLinked'=>false))),
                self::footer_row(array(
                    self::element('wpsoft-footer-brand',array('brand'=>'Store','text'=>'Güvenli ve hızlı alışveriş deneyimi.')),
                    self::element('wpsoft-footer-links',array('title'=>'Mağaza','items'=>array(
                        array('text'=>'Yeni Gelenler','badge'=>'Yeni','url'=>array('url'=>'#')),
                        array('text'=>'Çok Satanlar','url'=>array('url'=>'#')),
                        array('text'=>'Kampanyalar','url'=>array('url'=>'#')),
                        array('text'=>'Tüm Ürünler','url'=>array('url'=>'#'))
                    ))),
                    self::element('wpsoft-footer-links',array('title'=>'Müşteri Hizmetleri','items'=>array(
                        array('text'=>'Sipariş Takibi','url'=>array('url'=>'#')),
                        array('text'=>'Teslimat','url'=>array('url'=>'#')),
                        array('text'=>'İade & Değişim','url'=>array('url'=>'#')),
                        array('text'=>'Yardım','url'=>array('url'=>'#'))
                    ))),
                    self::element('wpsoft-footer-social')
                ),array('background_background'=>'classic','background_color'=>'#052e16','padding'=>array('unit'=>'px','top'=>'44','right'=>'34','bottom'=>'36','left'=>'34','isLinked'=>false)),array(30,22,22,26))
            );
        }

        if($type==='footer-mega-premium'){
            return array(
                self::footer_row(array(
                    self::element('wpsoft-morphing-cta',array(
                        'eyebrow'=>'LET’S BUILD',
                        'title'=>'Bir sonraki büyük projenizi birlikte tasarlayalım',
                        'text'=>'Strateji, tasarım ve teknolojiyi tek noktada birleştirin.',
                        'button_text'=>'Projeyi Başlat','button_url'=>array('url'=>'#iletisim')
                    ))
                ),array(
                    'background_background'=>'classic','background_color'=>'#020617',
                    'padding'=>array('unit'=>'px','top'=>'28','right'=>'34','bottom'=>'12','left'=>'34','isLinked'=>false)
                )),
                self::footer_row(array(
                    self::element('wpsoft-footer-brand',array(
                        'brand'=>'WPSoft',
                        'text'=>'Kurumsal web, e-ticaret, yazılım ve dijital büyüme çözümleri.',
                        'phone'=>'+90 212 000 00 00','email'=>'hello@example.com'
                    )),
                    self::element('wpsoft-footer-links',array(
                        'title'=>'Hizmetler',
                        'items'=>array(
                            array('text'=>'Web Tasarım','url'=>array('url'=>'#')),
                            array('text'=>'E-Ticaret','url'=>array('url'=>'#')),
                            array('text'=>'SEO','url'=>array('url'=>'#')),
                            array('text'=>'Özel Yazılım','badge'=>'Pro','url'=>array('url'=>'#'))
                        )
                    )),
                    self::element('wpsoft-footer-links',array(
                        'title'=>'Şirket',
                        'items'=>array(
                            array('text'=>'Hakkımızda','url'=>array('url'=>'#')),
                            array('text'=>'Projeler','url'=>array('url'=>'#')),
                            array('text'=>'Kariyer','url'=>array('url'=>'#')),
                            array('text'=>'İletişim','url'=>array('url'=>'#'))
                        )
                    )),
                    self::element('wpsoft-footer-newsletter',array(
                        'eyebrow'=>'NEWSLETTER',
                        'title'=>'Yeni fikirler doğrudan e-postanıza',
                        'text'=>'Dijital trendler, yeni projeler ve ürün güncellemeleri.'
                    ))
                ),array(
                    'background_background'=>'classic','background_color'=>'#020617',
                    'padding'=>array('unit'=>'px','top'=>'42','right'=>'34','bottom'=>'28','left'=>'34','isLinked'=>false)
                ),array(30,18,18,34)),
                self::footer_row(array(
                    self::element('wpsoft-footer-social',array('title'=>'Bizi Takip Edin')),
                    self::element('wpsoft-info-strip')
                ),array(
                    'background_background'=>'classic','background_color'=>'#070b14',
                    'padding'=>array('unit'=>'px','top'=>'16','right'=>'34','bottom'=>'16','left'=>'34','isLinked'=>false)
                ),array(35,65))
            );
        }

        if($type==='footer-newsletter-pro'){
            return array(
                self::footer_row(array(
                    self::element('wpsoft-footer-newsletter',array(
                        'eyebrow'=>'BÜLTEN',
                        'title'=>'Her ay yeni fikirler ve faydalı içerikler',
                        'text'=>'Sadece önemli güncellemeler. Gereksiz e-posta yok.',
                        'button'=>'Kaydol'
                    )),
                    self::element('wpsoft-footer-social',array('title'=>'Sosyalde Buluşalım'))
                ),array(
                    'background_background'=>'classic','background_color'=>'#0f172a',
                    'padding'=>array('unit'=>'px','top'=>'44','right'=>'34','bottom'=>'32','left'=>'34','isLinked'=>false)
                ),array(68,32)),
                self::footer_row(array(
                    self::element('wpsoft-footer-brand',array(
                        'brand'=>'WPSoft','text'=>'Dijital dünyada güçlü markalar için modern çözümler.'
                    )),
                    self::element('wpsoft-footer-links',array('title'=>'Hizmetler')),
                    self::element('wpsoft-footer-links',array('title'=>'Şirket')),
                    self::element('wpsoft-footer-links',array('title'=>'Destek'))
                ),array(
                    'background_background'=>'classic','background_color'=>'#111827',
                    'padding'=>array('unit'=>'px','top'=>'34','right'=>'34','bottom'=>'34','left'=>'34','isLinked'=>false)
                ),array(34,22,22,22))
            );
        }

        if($type==='footer-corporate-light'){
            return array(
                self::footer_row(array(
                    self::element('wpsoft-footer-brand',array(
                        'brand'=>'Kurumsal Şirket',
                        'text'=>'Sektörünüzde güvenilir, sürdürülebilir ve yenilikçi çözümler.',
                        'phone'=>'+90 212 000 00 00','email'=>'info@example.com'
                    )),
                    self::element('wpsoft-footer-links',array('title'=>'Kurumsal')),
                    self::element('wpsoft-footer-links',array('title'=>'Hizmetler')),
                    self::element('wpsoft-footer-links',array('title'=>'Kaynaklar'))
                ),array(
                    'background_background'=>'classic','background_color'=>'#f8fafc',
                    'padding'=>array('unit'=>'px','top'=>'52','right'=>'34','bottom'=>'34','left'=>'34','isLinked'=>false)
                ),array(34,22,22,22)),
                self::footer_row(array(
                    self::element('wpsoft-footer-social',array('title'=>'Bizi Takip Edin')),
                    self::element('wpsoft-info-strip')
                ),array(
                    'background_background'=>'classic','background_color'=>'#eef2f7',
                    'padding'=>array('unit'=>'px','top'=>'16','right'=>'34','bottom'=>'16','left'=>'34','isLinked'=>false)
                ),array(35,65))
            );
        }

        if($type==='footer-shop-pro'){
            return array(
                self::footer_row(array(
                    self::element('wpsoft-heading',array(
                        'eyebrow'=>'ÖZEL FIRSATLAR',
                        'title'=>'Yeni sezonu ilk siz keşfedin',
                        'description'=>'Yeni ürünler, özel kampanyalar ve üyelik fırsatları.'
                    )),
                    self::element('wpsoft-footer-newsletter',array(
                        'eyebrow'=>'KAMPANYALAR',
                        'title'=>'Avantajları e-postanıza alın',
                        'text'=>'Yeni ürün ve indirimlerden ilk siz haberdar olun.'
                    ))
                ),array(
                    'background_background'=>'classic','background_color'=>'#ecfdf5',
                    'padding'=>array('unit'=>'px','top'=>'34','right'=>'34','bottom'=>'30','left'=>'34','isLinked'=>false)
                ),array(48,52)),
                self::footer_row(array(
                    self::element('wpsoft-footer-brand',array(
                        'brand'=>'Store','text'=>'Güvenli, hızlı ve modern alışveriş deneyimi.'
                    )),
                    self::element('wpsoft-footer-links',array(
                        'title'=>'Mağaza',
                        'items'=>array(
                            array('text'=>'Yeni Gelenler','badge'=>'Yeni','url'=>array('url'=>'#')),
                            array('text'=>'Çok Satanlar','url'=>array('url'=>'#')),
                            array('text'=>'Kampanyalar','url'=>array('url'=>'#')),
                            array('text'=>'Tüm Ürünler','url'=>array('url'=>'#'))
                        )
                    )),
                    self::element('wpsoft-footer-links',array(
                        'title'=>'Yardım',
                        'items'=>array(
                            array('text'=>'Sipariş Takibi','url'=>array('url'=>'#')),
                            array('text'=>'Teslimat','url'=>array('url'=>'#')),
                            array('text'=>'İade & Değişim','url'=>array('url'=>'#')),
                            array('text'=>'SSS','url'=>array('url'=>'#'))
                        )
                    )),
                    self::element('wpsoft-footer-social',array('title'=>'Bizi Takip Edin'))
                ),array(
                    'background_background'=>'classic','background_color'=>'#052e16',
                    'padding'=>array('unit'=>'px','top'=>'42','right'=>'34','bottom'=>'34','left'=>'34','isLinked'=>false)
                ),array(30,22,22,26))
            );
        }

        return array(
            self::footer_row(array(
                self::element('wpsoft-footer-brand'),
                self::element('wpsoft-footer-links'),
                self::element('wpsoft-footer-newsletter')
            ),$dark)
        );
    }

    private static function mega_menu_templates(){
        $base = WPST_URL.'assets/images/mega-menu/';
        return array(
            array('key'=>'mega-corporate','title'=>'Mega Menü · Kurumsal','desc'=>'Editoryal giriş, iki dar link kolonu ve alt iletişim şeridiyle kurumsal bilgi mimarisi.','preview_image'=>$base.'mega-corporate.svg','data'=>self::mega_menu_data('corporate')),
            array('key'=>'mega-services','title'=>'Mega Menü · Hizmetler','desc'=>'Görselsiz, yoğun icon directory ve servis matrisi; çok sayıda hizmet için hızlı tarama.','preview_image'=>$base.'mega-services.svg','data'=>self::mega_menu_data('services')),
            array('key'=>'mega-shop','title'=>'Mega Menü · E-Ticaret','desc'=>'Sol kategori indeksi, ortada büyük kampanya kartı, sağda müşteri hizmetleri rayı.','preview_image'=>$base.'mega-shop.svg','data'=>self::mega_menu_data('shop')),
            array('key'=>'mega-travel','title'=>'Mega Menü · Turizm','desc'=>'Önce büyük destinasyon görseli, altında kompakt rota kartlarıyla görsel odaklı turizm menüsü.','preview_image'=>$base.'mega-travel.svg','data'=>self::mega_menu_data('travel')),
            array('key'=>'mega-food','title'=>'Mega Menü · Restoran','desc'=>'Fiyat/menü listesi, görsel story kartları ve ayrı rezervasyon şeridiyle restoran menüsü.','preview_image'=>$base.'mega-food.svg','data'=>self::mega_menu_data('food')),
            array('key'=>'mega-software','title'=>'Mega Menü · Software / SaaS','desc'=>'Arama alanı, ürün kartları, kaynak indeksi ve product-update paneliyle SaaS kaynak merkezi.','preview_image'=>$base.'mega-software.svg','data'=>self::mega_menu_data('software')),
            array('key'=>'mega-creative','title'=>'Mega Menü · Creative','desc'=>'Büyük selected-work canvas ve dar studio index ile asimetrik yaratıcı portfolio mega menüsü.','preview_image'=>$base.'mega-creative.svg','data'=>self::mega_menu_data('creative')),
            array('key'=>'mega-minimal','title'=>'Mega Menü · Minimal','desc'=>'Görselsiz, üç tipografik kolon ve sıfır kart yüzeyiyle ultra minimal kurumsal indeks.','preview_image'=>$base.'mega-minimal.svg','data'=>self::mega_menu_data('minimal'))
        );
    }

    private static function mega_menu_data($type){
        /*
         * Mega Menu Compositions 2.0
         * Every family has a different information architecture.
         * Do not normalize these back into the same three-column composition.
         */
        $panel=function($bg='#ffffff',$radius=18,$padding=16,$width=1200){
            return array(
                'content_width'=>'boxed',
                'width'=>array('unit'=>'px','size'=>$width,'sizes'=>array()),
                'padding'=>array('unit'=>'px','top'=>$padding,'right'=>$padding,'bottom'=>$padding,'left'=>$padding,'isLinked'=>true),
                'gap'=>array('unit'=>'px','size'=>14,'sizes'=>array()),
                'flex_direction'=>'column',
                'background_background'=>'classic',
                'background_color'=>$bg,
                'border_radius'=>array('unit'=>'px','top'=>$radius,'right'=>$radius,'bottom'=>$radius,'left'=>$radius,'isLinked'=>true)
            );
        };

        $row=function($els,$widths=array(),$gap=10,$align='stretch'){
            $count=max(1,count($els)); $cols=array();
            foreach($els as $i=>$el){
                $w=isset($widths[$i])?$widths[$i]:(100/$count);
                $cols[]=self::container(array($el),array(
                    'content_width'=>'full',
                    'width'=>array('unit'=>'%','size'=>$w,'sizes'=>array()),
                    'width_tablet'=>array('unit'=>'%','size'=>($count>=3?50:100),'sizes'=>array()),
                    'width_mobile'=>array('unit'=>'%','size'=>100,'sizes'=>array()),
                    'padding'=>array('unit'=>'px','top'=>'0','right'=>'4','bottom'=>'0','left'=>'4','isLinked'=>false)
                ));
            }
            return self::container($cols,array(
                'content_width'=>'full','flex_direction'=>'row','flex_wrap'=>'wrap','align_items'=>$align,
                'gap'=>array('unit'=>'px','size'=>$gap,'sizes'=>array()),
                'padding'=>array('unit'=>'px','top'=>'0','right'=>'0','bottom'=>'0','left'=>'0','isLinked'=>true)
            ));
        };

        $item=function($icon,$title,$text,$badge=''){
            return array('wpst_icon'=>$icon,'icon'=>'','title'=>$title,'text'=>$text,'badge'=>$badge,'url'=>array('url'=>'#'));
        };
        $quick=function($items,$layout='tiles',$columns='4'){
            return self::element('wpsoft-mega-quicknav',array('items'=>$items,'layout'=>$layout,'columns'=>$columns));
        };
        $links=function($title,$items,$style='cards',$columns='2'){
            return self::element('wpsoft-mega-links',array('title'=>$title,'items'=>$items,'style'=>$style,'columns'=>$columns));
        };
        $banner=function($eyebrow,$title,$text,$img,$button,$layout='overlay'){
            return self::element('wpsoft-mega-banner',array(
                'eyebrow'=>$eyebrow,'title'=>$title,'text'=>$text,'layout'=>$layout,
                'image'=>array('url'=>self::demo($img)),
                'button_text'=>$button,'button_url'=>array('url'=>'#')
            ));
        };
        $promo=function($eyebrow,$title,$text,$img,$button,$layout='overlay'){
            return self::element('wpsoft-mega-promo',array(
                'eyebrow'=>$eyebrow,'title'=>$title,'text'=>$text,'layout'=>$layout,
                'image'=>array('url'=>self::demo($img)),
                'button_text'=>$button,'button_url'=>array('url'=>'#')
            ));
        };

        /* 1 · Corporate Editorial
         * Left editorial statement + two narrow link columns + bottom utility strip.
         */
        if('corporate'===$type){
            $content=$row(array(
                self::element('wpsoft-heading',array(
                    'eyebrow'=>'COMPANY','title'=>'Kurumsal yapımızı keşfedin',
                    'description'=>'Hakkımızda, çalışma kültürümüz, projelerimiz ve iletişim noktaları.'
                )),
                $links('Şirket',array(
                    $item('building','Hakkımızda','Yaklaşımımızı ve hikâyemizi tanıyın.'),
                    $item('users','Ekibimiz','Uzman kadromuzla tanışın.'),
                    $item('briefcase','Kariyer','Açık pozisyonları inceleyin.','Yeni')
                ),'list','1'),
                $links('Keşfet',array(
                    $item('layers','Projeler','Seçili çalışmalarımızı görün.'),
                    $item('file-text','Blog','Güncel içeriklerimizi okuyun.'),
                    $item('message','İletişim','Yeni projenizi konuşalım.')
                ),'list','1')
            ),array(46,27,27),14,'flex-start');

            $utility=$quick(array(
                array('wpst_icon'=>'phone','title'=>'Bizi Arayın','text'=>'+90 212 000 00 00','url'=>array('url'=>'#')),
                array('wpst_icon'=>'mail','title'=>'E-Posta','text'=>'info@example.com','url'=>array('url'=>'#')),
                array('wpst_icon'=>'map-pin','title'=>'Ofis','text'=>'İstanbul, Türkiye','url'=>array('url'=>'#'))
            ),'rows','3');

            return array(self::container(array($content,$utility),array_merge($panel('#ffffff',18,22,1180),array('_css_classes'=>'wpst-mega-composition wpst-mega-corporate'))));
        }

        /* 2 · Services Matrix
         * Dense icon directory. No large image/promo.
         */
        if('services'===$type){
            $intro=self::element('wpsoft-info-strip',array(
                'wpst_icon'=>'sparkles','title'=>'Tüm dijital hizmetler tek çatı altında',
                'text'=>'Stratejiden geliştirmeye kadar uçtan uca destek.',
                'button_text'=>'Tüm Hizmetler','button_url'=>array('url'=>'#'),'layout'=>'inline'
            ));
            $directory=self::element('wpsoft-link-grid',array(
                'layout'=>'tiles','columns'=>'4',
                'items'=>array(
                    array('wpst_icon'=>'monitor','title'=>'Web Tasarım','text'=>'Kurumsal ve premium web projeleri.','url'=>array('url'=>'#')),
                    array('wpst_icon'=>'shopping-bag','title'=>'E-Ticaret','text'=>'Dönüşüm odaklı mağaza deneyimleri.','url'=>array('url'=>'#')),
                    array('wpst_icon'=>'search','title'=>'SEO','text'=>'Teknik ve içerik optimizasyonu.','url'=>array('url'=>'#')),
                    array('wpst_icon'=>'bar-chart','title'=>'Dijital Reklam','text'=>'Ölçülebilir kampanya yönetimi.','url'=>array('url'=>'#')),
                    array('wpst_icon'=>'palette','title'=>'UI / UX','text'=>'Kullanıcı deneyimi ve arayüz tasarımı.','url'=>array('url'=>'#')),
                    array('wpst_icon'=>'settings','title'=>'Bakım','text'=>'Sürekli bakım ve teknik destek.','url'=>array('url'=>'#')),
                    array('wpst_icon'=>'code','title'=>'Özel Yazılım','text'=>'İhtiyaca özel WordPress çözümleri.','url'=>array('url'=>'#')),
                    array('wpst_icon'=>'message','title'=>'Danışmanlık','text'=>'Projeniz için doğru yol haritası.','url'=>array('url'=>'#'))
                )
            ));
            return array(self::container(array($intro,$directory),array_merge($panel('#f8fafc',18,18,1240),array('_css_classes'=>'wpst-mega-composition wpst-mega-services'))));
        }

        /* 3 · Shop Product Showcase
         * Category list + central campaign card + customer-service rail.
         */
        if('shop'===$type){
            $categories=$links('Kategoriler',array(
                $item('sparkles','Yeni Gelenler','Yeni sezon seçkisi.','Yeni'),
                $item('star','Çok Satanlar','Müşteri favorileri.'),
                $item('tag','Kampanyalar','Güncel fırsatlar.','%30'),
                $item('grid','Tüm Ürünler','Koleksiyonun tamamı.')
            ),'compact','1');

            $campaign=$promo('NEW SEASON','Yeni koleksiyon yayında','Yeni sezon ürünleri ve özel avantajlar.','shop.svg','Koleksiyonu Gör','overlay');

            $support=$quick(array(
                array('wpst_icon'=>'package','title'=>'Sipariş Takibi','text'=>'Sipariş durumunu görüntüleyin.','url'=>array('url'=>'#')),
                array('wpst_icon'=>'refresh-cw','title'=>'İade & Değişim','text'=>'Kolay iade süreci.','url'=>array('url'=>'#')),
                array('wpst_icon'=>'headphones','title'=>'Destek','text'=>'Müşteri hizmetleri.','url'=>array('url'=>'#'))
            ),'rows','1');

            return array(self::container(array(
                $row(array($categories,$campaign,$support),array(24,50,26),12)
            ),array_merge($panel('#ffffff',20,16,1260),array('_css_classes'=>'wpst-mega-composition wpst-mega-shop'))));
        }

        /* 4 · Travel Visual Destinations
         * Full visual banner first, compact destination shortcuts underneath.
         */
        if('travel'===$type){
            $hero=$banner('DISCOVER','Yeni rotalara açılın','Seçili destinasyonları ve unutulmaz deneyimleri keşfedin.','travel.svg','Turları Gör','overlay');
            $destinations=$quick(array(
                array('wpst_icon'=>'map-pin','title'=>'İstanbul','text'=>'Kültür & şehir','url'=>array('url'=>'#')),
                array('wpst_icon'=>'sun','title'=>'Akdeniz','text'=>'Deniz & doğa','url'=>array('url'=>'#')),
                array('wpst_icon'=>'compass','title'=>'Kapadokya','text'=>'Macera & keşif','url'=>array('url'=>'#')),
                array('wpst_icon'=>'heart','title'=>'Balayı','text'=>'Özel deneyimler','url'=>array('url'=>'#'))
            ),'compact','4');
            return array(self::container(array($hero,$destinations),array_merge($panel('#ffffff',22,14,1220),array('_css_classes'=>'wpst-mega-composition wpst-mega-travel'))));
        }

        /* 5 · Restaurant Menu Board
         * Menu-price content instead of ordinary link cards.
         */
        if('food'===$type){
            $menu=self::element('wpsoft-price-list',array(
                'layout'=>'compact',
                'items'=>array(
                    array('title'=>'Başlangıçlar','description'=>'Mevsimsel başlangıç seçkisi.','price'=>'→','badge'=>''),
                    array('title'=>'Ana Yemekler','description'=>'Şefin imza tabakları.','price'=>'→','badge'=>'Öne Çıkan'),
                    array('title'=>'Tatlılar','description'=>'Günün tatlı seçkisi.','price'=>'→','badge'=>''),
                    array('title'=>'İçecekler','description'=>'Kokteyl ve içecekler.','price'=>'→','badge'=>'')
                )
            ));
            $story=self::element('wpsoft-story-cards',array('layout'=>'visual'));
            $reserve=self::element('wpsoft-cta',array(
                'layout_style'=>'inline','title'=>'Masanızı ayırtın',
                'description'=>'Rezervasyon için hızlıca devam edin.',
                'button_text'=>'Rezervasyon','button_url'=>array('url'=>'#')
            ));
            return array(self::container(array(
                $row(array($menu,$story),array(40,60),14),
                $reserve
            ),array_merge($panel('#fbfaf7',20,18,1220),array('_css_classes'=>'wpst-mega-composition wpst-mega-food'))));
        }

        /* 6 · SaaS Resource Hub
         * Search-led experience + product links + resources.
         */
        if('software'===$type){
            $search=self::element('wpsoft-content-finder',array(
                'layout'=>'compact','title'=>'Kaynaklarda ara',
                'placeholder'=>'Dokümantasyon, özellik veya entegrasyon ara…',
                'button_text'=>'Ara'
            ));
            $product=$links('Ürün',array(
                $item('layers','Platform','Tüm özellikleri keşfedin.','v3'),
                $item('plug','Entegrasyonlar','Araçlarınıza bağlanın.'),
                $item('zap','Otomasyon','İş akışlarını hızlandırın.'),
                $item('bar-chart','Analitik','Verilerinizi anlamlandırın.')
            ),'cards','2');
            $resources=$links('Kaynaklar',array(
                $item('book-open','Dokümantasyon','Kurulum ve kullanım rehberi.'),
                $item('life-buoy','Destek Merkezi','Teknik yardım alın.'),
                $item('clock','Changelog','Yeni özellikleri takip edin.'),
                $item('code','API','Geliştirici kaynakları.')
            ),'list','1');
            $update=$promo('PRODUCT UPDATE','Yeni çalışma alanını keşfedin','Daha hızlı, esnek ve ölçeklenebilir.','saas.svg','Demo İzle','minimal');
            return array(self::container(array(
                $search,
                $row(array($product,$resources,$update),array(44,26,30),12)
            ),array_merge($panel('#f8fafc',18,18,1240),array('_css_classes'=>'wpst-mega-composition wpst-mega-software'))));
        }

        /* 7 · Creative Portfolio Canvas
         * Large project media + narrow studio index. Asymmetric by design.
         */
        if('creative'===$type){
            $selected=$banner('SELECTED WORK','Cesur fikirler, güçlü deneyimler','Öne çıkan yaratıcı projeyi doğrudan mega menüde sergileyin.','agency.svg','Case Study','overlay');
            $studio=$links('Studio',array(
                $item('sparkles','Brand Strategy','Marka yönü ve konumlandırma.'),
                $item('monitor','Web Experience','Yaratıcı dijital deneyimler.'),
                $item('play','Motion','Hareket ve etkileşim tasarımı.'),
                $item('camera','Campaign','Lansman ve kampanya üretimi.')
            ),'list','1');
            return array(self::container(array(
                $row(array($selected,$studio),array(68,32),16)
            ),array_merge($panel('#0b1020',22,14,1260),array('_css_classes'=>'wpst-mega-composition wpst-mega-creative'))));
        }

        /* 8 · Minimal Corporate Index
         * Zero cards, no image, plain type hierarchy.
         */
        $about=$links('Kurumsal',array(
            $item('arrow-up-right','Hakkımızda','Markamızı tanıyın.'),
            $item('users','Ekip','İnsanlarımızla tanışın.'),
            $item('briefcase','Kariyer','Açık pozisyonlar.')
        ),'list','1');
        $solutions=$links('Çözümler',array(
            $item('monitor','Web','Kurumsal web çözümleri.'),
            $item('shopping-bag','E-Ticaret','Online satış altyapıları.'),
            $item('search','SEO','Arama görünürlüğü.')
        ),'list','1');
        $resources=$links('Kaynaklar',array(
            $item('file-text','Blog','İçerikler ve içgörüler.'),
            $item('help-circle','SSS','Sık sorulan sorular.'),
            $item('message','İletişim','Bize ulaşın.')
        ),'list','1');
        return array(self::container(array(
            $row(array($about,$solutions,$resources),array(33.33,33.33,33.34),20,'flex-start')
        ),array_merge($panel('#ffffff',8,28,1120),array('_css_classes'=>'wpst-mega-composition wpst-mega-minimal'))));
    }

    private static function sync_registered_wpsoft_widgets($items){
        if(!did_action('elementor/loaded') || !class_exists('\Elementor\Plugin')) return $items;

        $existing=array();
        foreach($items as $item){
            if(!empty($item['data']['widgetType'])) $existing[$item['data']['widgetType']]=true;
        }

        try{
            $manager=\Elementor\Plugin::instance()->widgets_manager;
            if(!$manager || !method_exists($manager,'get_widget_types')) return $items;
            $types=$manager->get_widget_types();

            foreach($types as $widget){
                if(!is_object($widget) || !method_exists($widget,'get_name')) continue;
                $name=$widget->get_name();
                if(0!==strpos($name,'wpsoft-') || isset($existing[$name])) continue;

                $categories=method_exists($widget,'get_categories') ? (array)$widget->get_categories() : array();
                if($categories && !in_array('wpsoft',$categories,true)) continue;

                $title=method_exists($widget,'get_title') ? wp_strip_all_tags($widget->get_title()) : $name;
                $title=preg_replace('/^WPSoft\s*[·\-:]?\s*/iu','',$title);
                if(!$title) $title=$name;

                $key=preg_replace('/^wpsoft-/','',$name);
                $preview='corporate.svg';
                if(false!==strpos($name,'hero')) $preview='software.svg';
                elseif(false!==strpos($name,'gallery') || false!==strpos($name,'image')) $preview='restaurant.svg';
                elseif(false!==strpos($name,'team') || false!==strpos($name,'testimonial')) $preview='agency.svg';
                elseif(false!==strpos($name,'footer')) $preview='corporate.svg';
                elseif(false!==strpos($name,'mega')) $preview='corporate-premium.svg';
                elseif(false!==strpos($name,'blog') || false!==strpos($name,'post-')) $preview='blog-editorial.svg';

                $items[]=self::widget_item(
                    sanitize_key($key),
                    $title,
                    'Elementor içindeki WPSoft widgetı. İçerik ve biçim ayarları Elementor üzerinden tamamen düzenlenebilir.',
                    $name,
                    array(),
                    $preview
                );
                $existing[$name]=true;
            }
        }catch(\Throwable $e){
            // Library synchronization must never block the Elementor editor.
        }

        return $items;
    }

    private static function signature_ui_preset_for_item($item){
        $style=strtolower((string)(isset($item['style'])?$item['style']:''));
        $key=strtolower((string)(isset($item['key'])?$item['key']:''));
        $text=$style.' '.$key.' '.strtolower((string)(isset($item['title'])?$item['title']:''));
        if(false!==strpos($text,'editorial') || false!==strpos($text,'luxury')) return 'editorial';
        if(false!==strpos($text,'minimal') || false!==strpos($text,'compact')) return 'compact';
        if(false!==strpos($text,'spacious') || false!==strpos($text,'hospitality')) return 'spacious';
        return 'balanced';
    }

    private static function apply_signature_ui_node($node,$preset='balanced',$depth=0){
        if(!is_array($node)) return $node;
        $el_type=isset($node['elType'])?(string)$node['elType']:'';
        if(!isset($node['settings']) || !is_array($node['settings'])) $node['settings']=array();
        $settings=$node['settings'];

        if('widget'===$el_type){
            $widget=isset($node['widgetType'])?(string)$node['widgetType']:'';
            if(0===strpos($widget,'wpsoft-')){
                if(!isset($settings['wpst_use_global_design'])) $settings['wpst_use_global_design']='yes';
                if(!isset($settings['wpst_signature_ui'])) $settings['wpst_signature_ui']=$preset;
                if(!isset($settings['wpst_mobile_touch_mode'])) $settings['wpst_mobile_touch_mode']='yes';
                if(!isset($settings['wpst_text_wrap_mode'])) $settings['wpst_text_wrap_mode']='balance';

                $stack_widgets=array(
                    'wpsoft-service-cards-pro','wpsoft-stats-grid','wpsoft-icon-grid','wpsoft-badge-grid',
                    'wpsoft-number-cards','wpsoft-contact-cards','wpsoft-pricing','wpsoft-logo-grid-pro',
                    'wpsoft-team-carousel-pro','wpsoft-blog-posts','wpsoft-process-steps-pro'
                );
                if(in_array($widget,$stack_widgets,true) && !isset($settings['wpst_stack_mobile'])) $settings['wpst_stack_mobile']='yes';

                $cta_widgets=array('wpsoft-cta','wpsoft-morphing-cta','wpsoft-booking-strip');
                if(in_array($widget,$cta_widgets,true) && !isset($settings['wpst_mobile_cta_full'])) $settings['wpst_mobile_cta_full']='yes';
            }
        }elseif('container'===$el_type){
            $classes=isset($settings['_css_classes'])?trim((string)$settings['_css_classes']):'';
            $class_list=preg_split('/\s+/',trim($classes))?:array();

            if(0===$depth && !in_array('wpst-signature-section',$class_list,true)){
                $class_list[]='wpst-signature-section';
                $class_list[]='wpst-signature-section-'.$preset;
                if(!isset($settings['padding_tablet'])){
                    $settings['padding_tablet']=array('unit'=>'px','top'=>'48','right'=>'22','bottom'=>'48','left'=>'22','isLinked'=>false);
                }
                if(!isset($settings['padding_mobile'])){
                    $settings['padding_mobile']=array('unit'=>'px','top'=>'34','right'=>'18','bottom'=>'34','left'=>'18','isLinked'=>false);
                }
                if(!isset($settings['boxed_width']) && isset($settings['content_width']) && 'boxed'===$settings['content_width']){
                    $settings['boxed_width']=array('unit'=>'px','size'=>1240,'sizes'=>array());
                }
            }

            if(isset($settings['flex_direction']) && 'row'===$settings['flex_direction'] && !isset($settings['flex_direction_mobile'])){
                $settings['flex_direction_mobile']='column';
            }
            if(!isset($settings['gap_mobile']) && isset($settings['gap'])){
                $settings['gap_mobile']=array('unit'=>'px','size'=>18,'row'=>18,'column'=>18);
            }
            if($class_list) $settings['_css_classes']=trim(implode(' ',array_unique(array_filter($class_list))));
        }

        $node['settings']=$settings;
        if(isset($node['elements']) && is_array($node['elements'])){
            foreach($node['elements'] as $i=>$child){
                $node['elements'][$i]=self::apply_signature_ui_node($child,$preset,$depth+1);
            }
        }
        return $node;
    }

    private static function apply_signature_ui_data($data,$preset='balanced'){
        if(!is_array($data)) return $data;
        $out=array();
        foreach($data as $i=>$node) $out[$i]=self::apply_signature_ui_node($node,$preset,0);
        return $out;
    }

    private static function section_family_for_item($item){
        $key=strtolower((string)($item['key']??''));
        $title=strtolower((string)($item['title']??''));
        $hay=$key.' '.$title;
        if(false!==strpos($hay,'hero')) return 'hero';
        if(false!==strpos($hay,'about') || false!==strpos($hay,'hakk')) return 'about';
        if(false!==strpos($hay,'service') || false!==strpos($hay,'hizmet')) return 'services';
        if(false!==strpos($hay,'project') || false!==strpos($hay,'portfolio') || false!==strpos($hay,'case')) return 'projects';
        if(false!==strpos($hay,'testimonial') || false!==strpos($hay,'yorum') || false!==strpos($hay,'proof')) return 'proof';
        if(false!==strpos($hay,'pricing') || false!==strpos($hay,'fiyat')) return 'pricing';
        if(false!==strpos($hay,'faq') || false!==strpos($hay,'sss')) return 'faq';
        if(false!==strpos($hay,'contact') || false!==strpos($hay,'iletişim')) return 'contact';
        if(false!==strpos($hay,'blog') || false!==strpos($hay,'news')) return 'blog';
        if(false!==strpos($hay,'cta')) return 'cta';
        if(false!==strpos($hay,'process') || false!==strpos($hay,'timeline') || false!==strpos($hay,'süreç')) return 'process';
        if(false!==strpos($hay,'logo') || false!==strpos($hay,'brand') || false!==strpos($hay,'referans')) return 'logos';
        if(false!==strpos($hay,'stat') || false!==strpos($hay,'counter') || false!==strpos($hay,'metric')) return 'stats';
        if(false!==strpos($hay,'gallery') || false!==strpos($hay,'video') || false!==strpos($hay,'media')) return 'media';
        if(false!==strpos($hay,'team') || false!==strpos($hay,'ekip')) return 'team';
        if(false!==strpos($hay,'feature') || false!==strpos($hay,'özellik')) return 'features';
        if(false!==strpos($hay,'sector')) return 'sector';
        return 'general';
    }

    private static function apply_section_quality_data($data,$item){
        if(!is_array($data)) return $data;
        $family=self::section_family_for_item($item);
        $style=strtolower((string)($item['style']??''));
        foreach($data as $i=>$node){
            if(!is_array($node) || (($node['elType']??'')!=='container')) continue;
            if(!isset($node['settings']) || !is_array($node['settings'])) $node['settings']=array();
            $s=$node['settings'];
            $classes=array_filter(preg_split('/\s+/',trim((string)($s['_css_classes']??'')))?:array());
            $classes[]='wpst-premium-section';
            $classes[]='wpst-section-family-'.$family;
            if($style)$classes[]='wpst-section-style-'.sanitize_html_class($style);

            if(!isset($s['content_width']))$s['content_width']='boxed';
            if('boxed'===$s['content_width']&&!isset($s['boxed_width'])){
                $s['boxed_width']=array('unit'=>'px','size'=>1280,'sizes'=>array());
            }

            // Preserve deliberate custom desktop spacing; supply quality fallbacks where missing.
            if(!isset($s['padding'])){
                $pad=in_array($family,array('hero','cta'),true)?56:76;
                $s['padding']=array('unit'=>'px','top'=>(string)$pad,'right'=>'28','bottom'=>(string)$pad,'left'=>'28','isLinked'=>false);
            }
            if(!isset($s['padding_tablet'])){
                $pad=in_array($family,array('hero','cta'),true)?44:56;
                $s['padding_tablet']=array('unit'=>'px','top'=>(string)$pad,'right'=>'22','bottom'=>(string)$pad,'left'=>'22','isLinked'=>false);
            }
            if(!isset($s['padding_mobile'])){
                $pad=in_array($family,array('hero','cta'),true)?32:40;
                $s['padding_mobile']=array('unit'=>'px','top'=>(string)$pad,'right'=>'18','bottom'=>(string)$pad,'left'=>'18','isLinked'=>false);
            }
            if(!isset($s['gap_mobile'])){
                $s['gap_mobile']=array('unit'=>'px','size'=>18,'row'=>18,'column'=>18);
            }
            if(isset($s['flex_direction'])&&'row'===$s['flex_direction']&&!isset($s['flex_direction_mobile'])){
                $s['flex_direction_mobile']='column';
            }
            $s['_css_classes']=trim(implode(' ',array_unique($classes)));
            $node['settings']=$s;
            $data[$i]=$node;
        }
        return $data;
    }

    private static function section_quality_tour_v1($sections){
        if(!is_array($sections)) return array();

        $category_map=array(
            'Projeler'=>'Portföy',
            'Yorumlar & Güven'=>'Yorumlar',
            'Sosyal Kanıt'=>'Yorumlar',
            'Logo Carousel'=>'Logo / Markalar',
            'Sektör Setleri'=>'Sektörel',
            'Diğer'=>'Genel'
        );
        $valid_categories=array(
            'Hero','Hakkımızda','Hizmetler','Özellikler','Portföy','Süreç','İstatistik',
            'Yorumlar','Ekip','Fiyatlandırma','SSS','Blog','Video / Galeri','CTA',
            'İletişim','Logo / Markalar','Rezervasyon','Ürünler','Sektörel','Genel'
        );

        foreach($sections as $idx=>$item){
            if(!is_array($item)) continue;
            $category=(string)($item['category']??'Genel');
            if(isset($category_map[$category])) $category=$category_map[$category];

            $family=self::section_family_for_item($item);
            $family_category=array(
                'hero'=>'Hero','about'=>'Hakkımızda','services'=>'Hizmetler','projects'=>'Portföy',
                'proof'=>'Yorumlar','pricing'=>'Fiyatlandırma','faq'=>'SSS','contact'=>'İletişim',
                'blog'=>'Blog','cta'=>'CTA','process'=>'Süreç','logos'=>'Logo / Markalar',
                'stats'=>'İstatistik','media'=>'Video / Galeri','team'=>'Ekip','features'=>'Özellikler',
                'sector'=>'Sektörel'
            );
            if(!in_array($category,$valid_categories,true) && isset($family_category[$family])){
                $category=$family_category[$family];
            }
            if(!in_array($category,$valid_categories,true)) $category='Genel';

            $item['category']=$category;
            $item['collection']=$item['collection']??($category.' Koleksiyonu');
            $item['experience']=$item['experience']??'Section';
            $item['responsive_ready']=true;
            $item['global_design_ready']=true;
            $item['local_style_ready']=true;
            $item['section_quality']='Premium Section';
            if(empty($item['quality']) || in_array($item['quality'],array('Legacy','Standard'),true)){
                $item['quality']='Modern';
            }
            $tags=array($category,(string)($item['style']??''),(string)($item['sector']??''),'Responsive','Global Design');
            $item['quality_tags']=array_values(array_unique(array_filter($tags)));

            // Same widget can legitimately power several sections; make the intended
            // visual composition explicit so variants are discoverable instead of
            // looking like accidental duplicates in the library.
            $key=strtolower((string)($item['key']??''));
            $title=strtolower((string)($item['title']??''));
            $variant='standard';
            foreach(array(
                'bento'=>'bento','editorial'=>'editorial','cinematic'=>'cinematic',
                'horizontal'=>'horizontal','compact'=>'compact','minimal'=>'minimal',
                'collage'=>'collage','filmstrip'=>'filmstrip','carousel'=>'carousel',
                'featured'=>'featured','spotlight'=>'spotlight','tiles'=>'tiles',
                'index'=>'index','grid'=>'grid','cards'=>'cards','panel'=>'panel',
                'statement'=>'statement','banner'=>'banner','journal'=>'journal'
            ) as $token=>$name){
                if(false!==strpos($key,$token) || false!==strpos($title,$token)){
                    $variant=$name; break;
                }
            }
            $item['visual_variant']=$item['visual_variant']??$variant;
            $item['design_family']=$item['design_family']??($category.' · '.ucfirst($item['visual_variant']));
            if(in_array($category,array('Hizmetler','Portföy','Video / Galeri'),true)){
                $item['composition_quality']='Curated 2.0';
            }
            if(in_array($category,array('Hakkımızda','Özellikler','Süreç','İstatistik'),true)){
                $item['composition_quality']='Curated 3.0';
                $item['content_hierarchy']='Heading + Primary Composition';
            }
            $sections[$idx]=$item;
        }
        return $sections;
    }

    private static function page_family_for_item($item){
        $key=strtolower((string)($item['key']??''));
        $role=strtolower((string)($item['template_role']??''));
        $sector=strtolower((string)($item['sector']??''));
        if('single_post'===$role || 0===strpos($key,'single-')) return 'single';
        if('blog_archive'===$role || 0===strpos($key,'blog-')) return 'archive';
        if(false!==strpos($key,'contact') || false!==strpos($sector,'iletişim')) return 'contact';
        if(false!==strpos($key,'service')) return 'service';
        if(false!==strpos($key,'agency')) return 'creative';
        if(false!==strpos($key,'hotel') || false!==strpos($key,'restaurant')) return 'hospitality';
        if(false!==strpos($key,'industrial') || false!==strpos($key,'machinery')) return 'industry';
        if(false!==strpos($key,'saas') || false!==strpos($key,'ecommerce')) return 'product';
        if(false!==strpos($key,'clinic')) return 'medical';
        return 'corporate';
    }

    private static function apply_page_quality_data($data,$item){
        if(!is_array($data)) return $data;
        $family=self::page_family_for_item($item);
        $count=count($data);

        foreach($data as $i=>$node){
            if(!is_array($node) || (($node['elType']??'')!=='container')) continue;
            if(!isset($node['settings']) || !is_array($node['settings'])) $node['settings']=array();
            $s=$node['settings'];

            $classes=trim((string)($s['_css_classes']??''));
            $class_list=array_filter(preg_split('/\s+/',trim($classes))?:array());
            $class_list[]='wpst-page-section';
            $class_list[]='wpst-page-family-'.$family;

            if(0===$i) $class_list[]='wpst-page-hero-section';
            elseif($i===$count-1) $class_list[]='wpst-page-final-section';
            else $class_list[]='wpst-page-content-section';

            if($i>0 && $i<$count-1){
                $class_list[]=(0===$i%2)?'wpst-page-section-even':'wpst-page-section-odd';
            }

            // Do not overwrite intentional desktop values, but add modern safe fallbacks.
            if(!isset($s['content_width'])) $s['content_width']='boxed';
            if('boxed'===$s['content_width'] && !isset($s['boxed_width'])){
                // Large desktop friendly baseline. Frontend CSS can still grow
                // WPSoft page sections fluidly on ultrawide displays.
                $s['boxed_width']=array('unit'=>'px','size'=>1440,'sizes'=>array());
            }

            if(!isset($s['padding'])){
                $desktop=(0===$i)?42:(($i===$count-1)?48:78);
                $s['padding']=array('unit'=>'px','top'=>(string)$desktop,'right'=>'28','bottom'=>(string)$desktop,'left'=>'28','isLinked'=>false);
            }
            if(!isset($s['padding_tablet'])){
                $tablet=(0===$i)?32:(($i===$count-1)?42:58);
                $s['padding_tablet']=array('unit'=>'px','top'=>(string)$tablet,'right'=>'22','bottom'=>(string)$tablet,'left'=>'22','isLinked'=>false);
            }
            if(!isset($s['padding_mobile'])){
                $mobile=(0===$i)?22:(($i===$count-1)?32:42);
                $s['padding_mobile']=array('unit'=>'px','top'=>(string)$mobile,'right'=>'18','bottom'=>(string)$mobile,'left'=>'18','isLinked'=>false);
            }

            if(!isset($s['gap'])){
                $s['gap']=array('unit'=>'px','size'=>28,'row'=>28,'column'=>28);
            }
            if(!isset($s['gap_mobile'])){
                $s['gap_mobile']=array('unit'=>'px','size'=>18,'row'=>18,'column'=>18);
            }

            // A row at page level should stack naturally on phones.
            if(isset($s['flex_direction']) && 'row'===$s['flex_direction'] && !isset($s['flex_direction_mobile'])){
                $s['flex_direction_mobile']='column';
            }

            $s['_css_classes']=trim(implode(' ',array_unique($class_list)));
            $node['settings']=$s;
            $data[$i]=$node;
        }
        return $data;
    }

    private static function library_meta($items,$kind){
        $popular_keys = array(
            'corporate','agency','software','hotel','restaurant','realestate',
            'mega-corporate','mega-services','mega-shop','header-modern','footer-modern-split','footer-corporate-grid','footer-dark-cta'
        );

        $new_keys = array(
            'dentist','veterinary','gym','security','travel','furniture',
            'mega-travel','mega-food','mega-software','header-business','footer-saas','footer-contact-rich','footer-agency',
            'hero-industry','hero-hospitality','hero-medical','hero-commerce',
            'service-cards-pro','process-steps-pro','feature-mosaic','product-showcase','booking-strip','trust-badges',
            'mega-links','mega-promo','mega-banner','mega-quicknav',
            'gallery-zoom-pro','advanced-accordion','team-carousel-pro','video-popup-pro','logo-grid-pro','progress-pro',
            'sec-hero-bento-v2','sec-hero-split-v2','sec-hero-hotel-v2','sec-hero-industry-v2','sec-hero-saas-v2',
            'sec-hero-medical-v2','sec-hero-commerce-v2','sec-hero-spotlight-v2','sec-hero-slider-v2','sec-hero-video-v2','sec-hero-architecture-v2',
            'hotel-signature','industrial-signature','saas-signature','clinic-signature','restaurant-signature','agency-signature',
            'contact-wpforms-modern','contact-wpforms-bento','contact-wpforms-dark','sec-contact-hero-split','sec-contact-info-bento','sec-contact-wpforms-card','sec-contact-form-dark','sec-contact-office','sec-contact-faq-cta','wpforms','sec-hero-kinetic-v3','sec-about-editorial-v3','sec-about-stats-v3','sec-services-bento-v3','sec-features-dark-v3','sec-projects-showcase-v3','sec-products-split-v3','sec-gallery-zoom-v3','sec-testimonials-premium-v3','sec-team-editorial-v3','sec-pricing-dark-v3','sec-faq-split-v3','sec-cta-gradient-v3','sec-logos-proof-v3','sec-stats-metrics-v3','sec-process-timeline-v3','sec-hero-glass-v4','sec-hero-product-v4','sec-hero-editorial-v4','sec-about-values-v4','sec-about-cascade-v4','sec-about-trust-v4','sec-services-hover-v4','sec-services-carousel-v4','sec-services-process-v4','sec-features-tabs-v4','sec-features-orbit-v4','sec-features-cards-v4','sec-projects-cascade-v4','sec-projects-hover-v4','sec-projects-reveal-v4','sec-products-carousel-v4','sec-products-hotspot-v4','sec-products-launch-v4','sec-gallery-cascade-v4','sec-gallery-carousel-v4','sec-gallery-beforeafter-v4','sec-testimonials-dark-v4','sec-testimonials-quote-v4','sec-testimonials-cards-v4','sec-team-glass-v4','sec-team-values-v4','sec-team-cta-v4','sec-pricing-toggle-v4','sec-pricing-trust-v4','sec-pricing-darkcta-v4','sec-faq-accordion-v4','sec-faq-contact-v4','sec-faq-dark-v4','sec-contact-glass-v4','sec-contact-modal-v4','sec-contact-mapstory-v4','sec-cta-glass-v4','sec-cta-modal-v4','sec-cta-darkbutton-v4','sec-logos-marquee-v4','sec-logos-grid-v4','sec-logos-cloud-v4','sec-stats-dark-v4','sec-stats-progress-v4','sec-stats-numbercards-v4','sec-process-icons-v4','sec-process-timeline-dark-v4','sec-process-accordion-v4'
        );

        foreach($items as &$item){
            $key = isset($item['key']) ? sanitize_key($item['key']) : '';

            if(!isset($item['category'])){
                if('widgets'===$kind){
                    $title = strtolower($item['title'] ?? '');
                    if(strpos($title,'hero')!==false || strpos($title,'slider')!==false || strpos($title,'kaydır')!==false) $item['category']='Hero & Slider';
                    elseif(strpos($title,'mega')!==false) $item['category']='Mega Menü';
                    elseif(strpos($title,'footer')!==false) $item['category']='Footer';
                    elseif(strpos($title,'service')!==false || strpos($title,'hizmet')!==false) $item['category']='Hizmet & Kart';
                    elseif(strpos($title,'process')!==false || strpos($title,'step')!==false || strpos($title,'süreç')!==false) $item['category']='Süreç';
                    elseif(strpos($title,'product')!==false || strpos($title,'ürün')!==false || strpos($title,'commerce')!==false) $item['category']='E-Ticaret';
                    elseif(strpos($title,'booking')!==false || strpos($title,'rezerv')!==false) $item['category']='Rezervasyon';
                    elseif(strpos($title,'trust')!==false || strpos($title,'testimonial')!==false || strpos($title,'yorum')!==false) $item['category']='Sosyal Kanıt';
                    elseif(strpos($title,'animasyon')!==false || strpos($title,'orbit')!==false || strpos($title,'mouse')!==false || strpos($title,'parallax')!==false || strpos($title,'reveal')!==false || strpos($title,'morph')!==false) $item['category']='Animasyon';
                    elseif(strpos($title,'simge')!==false || strpos($title,'ikon')!==false || strpos($title,'icon')!==false) $item['category']='İkon & Bilgi';
                    elseif(strpos($title,'feature')!==false || strpos($title,'özellik')!==false || strpos($title,'mosaic')!==false) $item['category']='Özellikler';
                    else $item['category']='Genel';
                } elseif('pages'===$kind){
                    if(!empty($item['template_role']) && 'blog_archive'===$item['template_role']) $item['category']='Blog Arşiv';
                    elseif(!empty($item['template_role']) && 'single_post'===$item['template_role']) $item['category']='Tek Yazı';
                    else $item['category']='Sektör Sayfası';
                } elseif('mega_menus'===$kind){
                    $item['category']='Mega Menü';
                } elseif('headers'===$kind){
                    $item['category']='Header';
                } elseif('footers'===$kind){
                    $item['category']='Footer';
                } elseif('sections'===$kind){
                    $title = strtolower($item['title'] ?? '');
                    $haystack = strtolower($key.' '.$title);
                    if(strpos($haystack,'hero')!==false) $item['category']='Hero';
                    elseif(strpos($haystack,'about')!==false || strpos($haystack,'hakk')!==false) $item['category']='Hakkımızda';
                    elseif(strpos($haystack,'service')!==false || strpos($haystack,'hizmet')!==false) $item['category']='Hizmetler';
                    elseif(strpos($haystack,'feature')!==false || strpos($haystack,'özellik')!==false || strpos($haystack,'mosaic')!==false) $item['category']='Özellikler';
                    elseif(strpos($haystack,'project')!==false || strpos($haystack,'portfolio')!==false || strpos($haystack,'proje')!==false) $item['category']='Portföy';
                    elseif(strpos($haystack,'product')!==false || strpos($haystack,'ürün')!==false) $item['category']='Ürünler';
                    elseif(strpos($haystack,'gallery')!==false || strpos($haystack,'galeri')!==false || strpos($haystack,'cascade')!==false) $item['category']='Galeri';
                    elseif(strpos($haystack,'testimonial')!==false || strpos($haystack,'yorum')!==false || strpos($haystack,'trust')!==false) $item['category']='Yorumlar';
                    elseif(strpos($haystack,'team')!==false || strpos($haystack,'ekip')!==false) $item['category']='Ekip';
                    elseif(strpos($haystack,'pricing')!==false || strpos($haystack,'paket')!==false || strpos($haystack,'fiyat')!==false) $item['category']='Fiyatlandırma';
                    elseif(strpos($haystack,'faq')!==false || strpos($haystack,'sss')!==false) $item['category']='SSS';
                    elseif(strpos($haystack,'contact')!==false || strpos($haystack,'iletişim')!==false || strpos($haystack,'iletisim')!==false) $item['category']='İletişim';
                    elseif(strpos($haystack,'cta')!==false) $item['category']='CTA';
                    elseif(strpos($haystack,'blog')!==false || strpos($haystack,'post')!==false || strpos($haystack,'news')!==false || strpos($haystack,'haber')!==false) $item['category']='Blog';
                    elseif(strpos($haystack,'logo')!==false || strpos($haystack,'referans')!==false) $item['category']='Logo Carousel';
                    elseif(strpos($haystack,'stat')!==false || strpos($haystack,'counter')!==false || strpos($haystack,'istatistik')!==false) $item['category']='İstatistik';
                    elseif(strpos($haystack,'process')!==false || strpos($haystack,'süreç')!==false || strpos($haystack,'timeline')!==false) $item['category']='Süreç';
                    elseif(strpos($haystack,'booking')!==false || strpos($haystack,'rezerv')!==false) $item['category']='Rezervasyon';
                    else $item['category']='Diğer';
                } else {
                    $item['category']='Bölüm';
                }
            }

            if('sections'===$kind && empty($item['style'])){
                $style_haystack = strtolower($key.' '.($item['title'] ?? '').' '.($item['desc'] ?? ''));
                if(strpos($style_haystack,'glass')!==false) $item['style']='Glass';
                elseif(strpos($style_haystack,'bento')!==false || strpos($style_haystack,'mosaic')!==false) $item['style']='Bento';
                elseif(strpos($style_haystack,'dark')!==false || strpos($style_haystack,'night')!==false) $item['style']='Dark';
                elseif(strpos($style_haystack,'editorial')!==false || strpos($style_haystack,'magazine')!==false) $item['style']='Editorial';
                elseif(strpos($style_haystack,'minimal')!==false || strpos($style_haystack,'clean')!==false) $item['style']='Minimal';
                elseif(strpos($style_haystack,'creative')!==false || strpos($style_haystack,'kinetic')!==false || strpos($style_haystack,'cascade')!==false || strpos($style_haystack,'reveal')!==false || strpos($style_haystack,'orbit')!==false) $item['style']='Creative';
                else $item['style']='Modern';
            }

            if(!isset($item['sector'])){
                $map = array(
                    'corporate'=>'Kurumsal','agency'=>'Ajans','industrial'=>'Sanayi','construction'=>'İnşaat',
                    'hotel'=>'Otel','restaurant'=>'Restoran','realestate'=>'Emlak','health'=>'Sağlık',
                    'automotive'=>'Otomotiv','software'=>'Yazılım','law'=>'Hukuk','education'=>'Eğitim',
                    'beauty'=>'Güzellik','logistics'=>'Lojistik','energy'=>'Enerji','finance'=>'Finans',
                    'event'=>'Etkinlik','personal'=>'Kişisel','dentist'=>'Diş Kliniği','veterinary'=>'Veteriner',
                    'gym'=>'Fitness','security'=>'Güvenlik','cleaning'=>'Temizlik','travel'=>'Turizm',
                    'furniture'=>'Mobilya','printing'=>'Matbaa',
                    'corporate-premium'=>'Kurumsal','industrial-premium'=>'Sanayi','machinery-premium'=>'Makina',
                    'ecommerce-premium'=>'E-Ticaret','saas-premium'=>'Yazılım','hotel-premium'=>'Otel',
                    'clinic-premium'=>'Sağlık','architecture-premium'=>'İnşaat','restaurant-premium'=>'Restoran','agency-premium'=>'Ajans',
                    'hotel-signature'=>'Otel','industrial-signature'=>'Sanayi','saas-signature'=>'Yazılım',
                    'clinic-signature'=>'Sağlık','restaurant-signature'=>'Restoran','agency-signature'=>'Ajans',
                    'contact-wpforms-modern'=>'İletişim','contact-wpforms-bento'=>'İletişim','contact-wpforms-dark'=>'İletişim'
                );
                $item['sector'] = isset($map[$key]) ? $map[$key] : '';
            }

            // Template quality tier for cleaner discovery.
            if(empty($item['quality'])){
                if(strpos($key,'-v6')!==false || strpos($key,'-v5')!==false) $item['quality']='Signature';
                elseif(strpos($key,'-v4')!==false || strpos($key,'-v3')!==false) $item['quality']='Modern';
                elseif(strpos($key,'-v2')!==false) $item['quality']='Standard';
                elseif('sections'===$kind && strpos($key,'sec-')===0) $item['quality']='Legacy';
                elseif('pages'===$kind) $item['quality']='Modern';
                else $item['quality']='Standard';
            }

            $is_premium = strpos($key,'-premium') !== false || strpos($key,'-signature') !== false;
            if($is_premium){
                if('pages'===$kind) $item['category'] = 'Premium Sayfa';
                $item['is_popular'] = true;
                $item['is_new'] = true;
                $item['premium'] = true;
                if(empty($item['quality']) || 'Legacy'===$item['quality'] || 'Standard'===$item['quality']) $item['quality'] = 'Signature';
            } else {
                $item['is_popular'] = in_array($key,$popular_keys,true);
                $item['is_new'] = in_array($key,$new_keys,true);
            }
            if(in_array($kind,array('sections','pages'),true) && !empty($item['data']) && is_array($item['data'])){
                $signature_preset=self::signature_ui_preset_for_item($item);
                $item['data']=self::apply_signature_ui_data($item['data'],$signature_preset);
                $item['signature_ui']=$signature_preset;
            }
            if('pages'===$kind && !empty($item['data']) && is_array($item['data'])){
                $item['data']=self::apply_page_quality_data($item['data'],$item);
                $item['page_family']=self::page_family_for_item($item);
                if(empty($item['quality']) || in_array($item['quality'],array('Legacy','Standard'),true)) $item['quality']='Modern';
                $item['page_quality']='Premium Layout';
            }
            if('sections'===$kind && !empty($item['data']) && is_array($item['data'])){
                $item['data']=self::apply_section_quality_data($item['data'],$item);
                $item['section_family']=self::section_family_for_item($item);
                if(empty($item['quality'])) $item['quality']='Signature';
                $item['section_quality']='Premium Section';
            }
            $item['kind'] = $kind;
        }
        unset($item);
        return $items;
    }

    public static function header_library_payload(){
        if ( class_exists( 'WPST_Header_Footer_Templates' ) && method_exists( 'WPST_Header_Footer_Templates', 'header_library_items' ) ) {
            return self::library_meta( WPST_Header_Footer_Templates::header_library_items(), 'headers' );
        }
        return self::library_meta(self::header_templates(),'headers');
    }

    public static function footer_library_payload(){
        // Footer koleksiyonunun tek kaynağı Header/Footer Templates sınıfıdır.
        // Bu sayede yeni footerlar Elementor WPSoft Template Library içinde de otomatik görünür.
        if ( class_exists( 'WPST_Header_Footer_Templates' ) && method_exists( 'WPST_Header_Footer_Templates', 'footer_library_items' ) ) {
            return self::library_meta( WPST_Header_Footer_Templates::footer_library_items(), 'footers' );
        }
        return self::library_meta(self::footer_templates(),'footers');
    }

    public static function editor_payload(){
        $templates=self::templates();
        $widgets=array(
            self::widget_item('heading','Modern Başlık','Üst etiket, başlık ve açıklama.','wpsoft-heading',array('eyebrow'=>'Hizmetlerimiz','title'=>'İşletmeniz için modern çözümler','description'=>'İhtiyacınıza uygun profesyonel çözümler sunuyoruz.'),'service.svg'),
            self::widget_item('image-text','Görsel + Metin','Hakkımızda ve tanıtım alanları için iki kolonlu modern blok.','wpsoft-image-text',array('eyebrow'=>'Hakkımızda','title'=>'İşletmenizi modern bir deneyimle öne çıkarın','description'=>'Güçlü içerik ve modern tasarımı bir araya getirin.','image'=>array('url'=>self::demo('corporate.svg')),'button_text'=>'Hakkımızda','button_url'=>array('url'=>'#')),'agency.svg'),
            self::widget_item('pricing','Paket / Fiyat','Hizmet veya üyelik paketleri için fiyat kartı.','wpsoft-pricing',array('button_text'=>'Başlayalım','button_url'=>array('url'=>'#iletisim')),'software.svg'),
            self::widget_item('portfolio','Portföy / Projeler','Projeleri görselli modern grid yapısında gösterin.','wpsoft-portfolio',array(),'realestate.svg'),
            self::widget_item('gallery-zoom-pro','Zoom Galeri Pro','Zoom/lightbox, masonry, featured grid ve responsive kolon destekli modern görsel galerisi.','wpsoft-gallery-zoom-pro',array('layout'=>'grid','columns'=>'3','lightbox'=>'yes'),'restaurant.svg'),
            self::widget_item('breadcrumb','İç Sayfa Hero / Breadcrumb','İç sayfalarda başlık ve breadcrumb alanı.','wpsoft-breadcrumb',array('title'=>'Hizmetlerimiz'),'service.svg'),
            self::widget_item('iconbox','İkon Kutusu','Özellik veya hizmet avantajı anlatımı.','wpsoft-icon-box',array('title'=>'Hızlı ve Modern','description'=>'Performans odaklı modern kullanıcı deneyimi.'),'software.svg'),
            self::widget_item('cta','CTA Alanı','Teklif veya iletişim çağrısı için koyu alan.','wpsoft-cta',array('title'=>'Yeni projeniz için hazır mısınız?','description'=>'İhtiyacınızı konuşalım ve doğru çözümü birlikte belirleyelim.','button_text'=>'Bize Ulaşın','button_url'=>array('url'=>'#iletisim'),'bg'=>'#0f172a'),'contact.svg'),
            self::widget_item('faq','SSS / Accordion','Sık sorulan sorular bölümü.','wpsoft-faq',array(),'contact.svg'),
            self::widget_item('feature-list','Özellik Listesi','Avantajları iki kolonlu modern yapıda anlatın.','wpsoft-feature-list',array(),'corporate.svg'),
            self::widget_item('contact-cards','İletişim Kartları','Telefon, e-posta ve çalışma saatleri kartları.','wpsoft-contact-cards',array(),'contact.svg'),
            self::widget_item('wpforms','WPForms Form','WPForms formlarını seçip WPSoft tasarım sistemiyle sayfaya yerleştirin.','wpsoft-wpforms',array('empty_title'=>'İletişim Formunuzu Seçin','empty_text'=>'WPForms form ID seçildiğinde gerçek form burada görüntülenir.'),'contact.svg'),
            self::widget_item('stats-grid','İstatistik Grid','4 kolonlu modern istatistik alanı.','wpsoft-stats-grid',array(),'industrial.svg'),
            self::widget_item('badge-grid','Rozet / Avantaj Grid','Özellik ve avantaj kartları.','wpsoft-badge-grid',array(),'software.svg'),
            self::widget_item('quote','Büyük Alıntı','Marka mesajı veya kurucu sözü alanı.','wpsoft-quote',array(),'agency.svg'),
            self::widget_item('logo-cloud','Logo Cloud','Referans ve iş ortakları için logo bulutu.','wpsoft-logo-cloud',array(),'corporate.svg'),
            self::widget_item('icon-list','Simge Listesi','Simge, başlık ve açıklamalı modern liste.','wpsoft-icon-list',array(),'corporate.svg'),
            self::widget_item('icon-grid','Simge Grid','4 kolonlu modern simge kartları.','wpsoft-icon-grid',array(),'software.svg'),
            self::widget_item('icon-steps','Simge Süreç','Simge destekli süreç adımları.','wpsoft-icon-steps',array(),'construction.svg'),
            self::widget_item('floating-icons','Floating Simge Kartları','Pill formunda yüzen simge kartları.','wpsoft-floating-icons',array(),'agency.svg'),
            self::widget_item('number-cards','Numaralı Kartlar','3 adımlı numaralı modern kartlar.','wpsoft-number-cards',array(),'industrial.svg'),
            self::widget_item('info-strip','Bilgi Şeridi','Duyuru, kampanya ve bilgi şeridi.','wpsoft-info-strip',array('button_text'=>'İncele','button_url'=>array('url'=>'#')),'contact.svg'),
            self::widget_item('hero-slider','Hero Slider','Çoklu slayt ve otomatik oynatma.','wpsoft-hero-slider',array(
                'items'=>array(
                    array('eyebrow'=>'Dijital Çözümler','title'=>'Markanızı dijitalde daha güçlü hale getirin','text'=>'Modern tasarım ve demo görsellerle hazır slider.','button'=>'Teklif Al','url'=>array('url'=>'#iletisim'),'image'=>array('url'=>self::demo('corporate.svg'))),
                    array('eyebrow'=>'Premium Tasarım','title'=>'Elementor ile kolayca özelleştirin','text'=>'İçeriği ve görselleri müşterinize göre değiştirin.','button'=>'İncele','url'=>array('url'=>'#'),'image'=>array('url'=>self::demo('agency.svg'))),
                    array('eyebrow'=>'WPSoft','title'=>'Hızlı, modern ve mobil uyumlu','text'=>'Hazır gelen demo içeriğiyle daha hızlı başlayın.','button'=>'Başlayalım','url'=>array('url'=>'#'),'image'=>array('url'=>self::demo('saas.svg')))
                )
            ),'corporate.svg'),
            self::widget_item('image-carousel','Görsel Kaydırıcı','Modern görsel carousel.','wpsoft-image-carousel',array(
                'gallery'=>array(
                    array('url'=>self::demo('corporate.svg'),'id'=>0),
                    array('url'=>self::demo('industry.svg'),'id'=>0),
                    array('url'=>self::demo('hotel.svg'),'id'=>0),
                    array('url'=>self::demo('restaurant.svg'),'id'=>0)
                )
            ),'restaurant.svg'),
            self::widget_item('testimonial-slider','Yorum Slider','Kaydırmalı müşteri yorumları.','wpsoft-testimonial-slider',array(),'hotel.svg'),
            self::widget_item('logo-marquee','Logo Marquee','Sürekli kayan referans logoları.','wpsoft-logo-marquee',array(),'corporate.svg'),
            self::widget_item('tabs-modern','Modern Sekmeler','Sekmeli içerik alanı.','wpsoft-tabs-modern',array(),'service.svg'),
            self::widget_item('before-after','Öncesi / Sonrası','Karşılaştırmalı görsel alanı.','wpsoft-before-after',array(
                'before'=>array('url'=>self::demo('before.svg'),'id'=>0),
                'after'=>array('url'=>self::demo('after.svg'),'id'=>0)
            ),'beauty.svg'),
            self::widget_item('video-hero','Video Hero','Video veya poster arka planlı hero.','wpsoft-video-hero',array(
                'eyebrow'=>'WPSoft Studio',
                'title'=>'Güçlü bir ilk izlenim oluşturun',
                'description'=>'Video yüklemeden de yerel demo poster görseliyle hazır görünür.',
                'poster'=>array('url'=>self::demo('agency.svg'),'id'=>0),
                'button_text'=>'Bize Ulaşın','button_url'=>array('url'=>'#iletisim')
            ),'agency.svg'),
            self::widget_item('card-carousel','Kart Kaydırıcı','Hizmet/proje kart carousel.','wpsoft-card-carousel',array(),'software.svg'),
            self::widget_item('animated-heading','Animasyonlu Başlık','Dönen kelimelerle modern hero/başlık alanı.','wpsoft-animated-heading',array(),'agency.svg'),
            self::widget_item('animated-counter','Animasyonlu Sayaç','Görünür olduğunda sayarak yükselen istatistik.','wpsoft-animated-counter',array(),'industrial.svg'),
            self::widget_item('marquee-text','Kayan Yazı','Marka mesajı veya hizmetleri kayan şerit halinde gösterir.','wpsoft-marquee-text',array(),'software.svg'),
            self::widget_item('reveal-cards','Animasyonlu Kartlar','Scroll görünürlük ve hover animasyonlu kartlar.','wpsoft-reveal-cards',array(),'corporate.svg'),
            self::widget_item('gradient-heading','Gradient Başlık','Essentials tarzı büyük gradient tipografi.','wpsoft-gradient-heading',array(),'agency.svg'),
            self::widget_item('parallax-image','Parallax Görsel','Scroll ile hareket eden modern görsel alanı.','wpsoft-parallax-image',array(
                'image'=>array('url'=>self::demo('architecture.svg'),'id'=>0),
                'title'=>'Modern mimari deneyim',
                'text'=>'Demo görselle hazır gelen parallax alanı.'
            ),'construction.svg'),
            self::widget_item('hover-reveal','Hover Reveal','Hover ile içeriği açılan görsel kartlar.','wpsoft-hover-reveal',array(),'realestate.svg'),
            self::widget_item('icon-orbit','Simge Orbit','Merkez etrafında dönen animasyonlu simgeler.','wpsoft-icon-orbit',array(),'software.svg'),
            self::widget_item('scroll-progress','Scroll Progress','Sayfa kaydırma ilerleme çubuğu.','wpsoft-scroll-progress',array(),'service.svg'),
            self::widget_item('mouse-follow','Mouse Follow Kart','Mouse takipli ışık ve perspektif kartı.','wpsoft-mouse-follow-card',array(),'software.svg'),
            self::widget_item('hero-split-modern','Hero · Split Modern','Büyük tipografi, görsel ve floating metric içeren modern hero.','wpsoft-hero-split-modern',array('image'=>array('url'=>self::demo('corporate.svg')),'primary_text'=>'Teklif Al','primary_url'=>array('url'=>'#iletisim'),'secondary_text'=>'Projeler','secondary_url'=>array('url'=>'#')),'corporate-premium.svg'),
            self::widget_item('hero-bento','Hero · Bento Grid','Modüler bento kartlarıyla modern hero.','wpsoft-hero-bento',array('image'=>array('url'=>self::demo('agency.svg')),'button_text'=>'Projeyi Başlat','button_url'=>array('url'=>'#iletisim')),'agency-premium.svg'),
            self::widget_item('hero-saas','Hero · SaaS Dashboard','Dashboard görseli, glow efektleri ve çift CTA.','wpsoft-hero-saas',array('image'=>array('url'=>self::demo('saas.svg')),'primary_text'=>'Ücretsiz Başla','primary_url'=>array('url'=>'#'),'secondary_text'=>'Demo İzle','secondary_url'=>array('url'=>'#')),'saas-premium.svg'),
            self::widget_item('hero-spotlight','Hero · Spotlight','Mouse spotlight ve büyük tipografili koyu premium hero.','wpsoft-hero-spotlight',array('button_text'=>'Projeleri Keşfet','button_url'=>array('url'=>'#')),'agency-premium.svg'),
            self::widget_item('image-cascade','Image Cascade','Katmanlı modern görsel kompozisyonu.','wpsoft-image-cascade',array('image_one'=>array('url'=>self::demo('architecture.svg')),'image_two'=>array('url'=>self::demo('agency.svg')),'image_three'=>array('url'=>self::demo('corporate.svg'))),'architecture-premium.svg'),
            self::widget_item('image-hotspots','Image Hotspots','Etkileşimli görsel noktaları.','wpsoft-image-hotspots',array('image'=>array('url'=>self::demo('shop.svg'))),'furniture.svg'),
            self::widget_item('fancy-box','Fancy Box','Görsel, hover ve güçlü tipografi içeren premium kart.','wpsoft-fancy-box',array('image'=>array('url'=>self::demo('agency.svg')),'button_text'=>'İncele','button_url'=>array('url'=>'#')),'agency.svg'),
            self::widget_item('flip-box','Flip Box','Hover ile dönen iki yüzlü interaktif kart.','wpsoft-flip-box',array('image'=>array('url'=>self::demo('corporate.svg')),'button_text'=>'Detaylı İncele','button_url'=>array('url'=>'#')),'corporate.svg'),
            self::widget_item('morphing-cta','Morphing CTA','Şekil değiştiren modern CTA.','wpsoft-morphing-cta',array('button_text'=>'Başlayalım','button_url'=>array('url'=>'#iletisim')),'agency.svg'),
            self::widget_item('scroll-reveal-text','Scroll Reveal Text','Scroll sırasında kelime kelime ortaya çıkan tipografi.','wpsoft-scroll-reveal-text',array(),'agency.svg'),
            self::widget_item('timeline-modern','Timeline Modern','Tarih ve kilometre taşı gösterimi.','wpsoft-timeline-modern',array(),'industrial.svg'),
            self::widget_item('countdown-modern','Countdown Modern','Kampanya, açılış veya etkinlik geri sayımı.','wpsoft-countdown-modern',array(),'event.svg'),
            self::widget_item('footer-brand','Footer · Marka','Logo, açıklama, telefon ve e-posta için modern footer marka bloğu.','wpsoft-footer-brand',array(),'corporate.svg'),
            self::widget_item('footer-links','Footer · Link Kolonu','Modern link kolonu, badge ve hover ok desteği.','wpsoft-footer-links',array(),'corporate.svg'),
            self::widget_item('footer-newsletter','Footer · Newsletter','E-posta kayıt alanı ve modern CTA yapısı.','wpsoft-footer-newsletter',array(),'saas-premium.svg'),
            self::widget_item('footer-social','Footer · Sosyal','Pill tarzı modern sosyal medya bağlantıları.','wpsoft-footer-social',array(),'agency.svg'),
            self::widget_item('hero-industry','Hero · Industry','Sanayi, üretim ve makina için teknik metrikli hero.','wpsoft-hero-industry',array('image'=>array('url'=>self::demo('industry.svg')),'button_text'=>'Teknik Bilgi Al','button_url'=>array('url'=>'#iletisim')),'industrial-premium.svg'),
            self::widget_item('hero-hospitality','Hero · Hospitality','Otel, resort ve turizm için rezervasyon odaklı hero.','wpsoft-hero-hospitality',array('image'=>array('url'=>self::demo('hotel.svg')),'button_text'=>'Rezervasyon Yap','button_url'=>array('url'=>'#iletisim')),'hotel-premium.svg'),
            self::widget_item('hero-medical','Hero · Medical','Sağlık ve klinik için güven odaklı hero.','wpsoft-hero-medical',array('image'=>array('url'=>self::demo('health.svg')),'button_text'=>'Randevu Al','button_url'=>array('url'=>'#iletisim')),'clinic-premium.svg'),
            self::widget_item('hero-commerce','Hero · Commerce','E-ticaret ve ürün siteleri için kampanya hero.','wpsoft-hero-commerce',array('image'=>array('url'=>self::demo('shop.svg')),'button_text'=>'Alışverişe Başla','button_url'=>array('url'=>'#')),'ecommerce-premium.svg'),
            self::widget_item('service-cards-pro','Service Cards Pro','Modern hizmet kartları.','wpsoft-service-cards-pro',array(),'corporate.svg'),
            self::widget_item('process-steps-pro','Process Steps Pro','Modern süreç adımları.','wpsoft-process-steps-pro',array(),'corporate.svg'),
            self::widget_item('feature-mosaic','Feature Mosaic','Büyük görsel ve özellik kartları.','wpsoft-feature-mosaic',array('image'=>array('url'=>self::demo('saas.svg'))),'saas-premium.svg'),
            self::widget_item('product-showcase','Product Showcase','Ürün ve koleksiyon kartları.','wpsoft-product-showcase',array('items'=>array(array('image'=>array('url'=>self::demo('shop.svg')),'title'=>'Premium Ürün','meta'=>'Yeni','price'=>'₺1.250'),array('image'=>array('url'=>self::demo('corporate.svg')),'title'=>'Modern Seri','meta'=>'Popüler','price'=>'₺980'),array('image'=>array('url'=>self::demo('agency.svg')),'title'=>'Özel Seçim','meta'=>'Sınırlı','price'=>'₺1.490'))),'ecommerce-premium.svg'),
            self::widget_item('booking-strip','Booking Strip','Otel ve restoran için rezervasyon şeridi.','wpsoft-booking-strip',array('button_text'=>'Müsaitliği Kontrol Et','button_url'=>array('url'=>'#iletisim')),'hotel-premium.svg'),
            self::widget_item('trust-badges','Trust Badges','Güven ve uzmanlık rozetleri.','wpsoft-trust-badges',array(),'clinic-premium.svg'),
            self::widget_item('mega-links','Mega Menü · Links','Mega menü için ikonlu, açıklamalı ve badge destekli bağlantı grubu.','wpsoft-mega-links',array(),'corporate.svg'),
            self::widget_item('mega-promo','Mega Menü · Promo','Mega menü içinde görselli öne çıkan CTA kartı.','wpsoft-mega-promo',array('image'=>array('url'=>self::demo('corporate.svg')),'button_text'=>'İncele','button_url'=>array('url'=>'#')),'corporate-premium.svg'),
            self::widget_item('mega-banner','Mega Menü · Media Banner','Görsel veya video bağlantılı, CTA içeren geniş mega menü bannerı.','wpsoft-mega-banner',array('image'=>array('url'=>self::demo('agency.svg')),'button_text'=>'İncele','button_url'=>array('url'=>'#')),'agency-premium.svg'),
            self::widget_item('mega-quicknav','Mega Menü · Quick Nav','Mega menü üstünde hızlı kategori ve kısa yol pill bağlantıları.','wpsoft-mega-quicknav',array(),'corporate.svg'),
            self::widget_item('blog-posts','Blog Yazıları','Yayınlanan yazıları 10, 20 veya tümü şeklinde grid, magazine veya liste görünümünde gösterir.','wpsoft-blog-posts',array('posts_per_page'=>10,'layout_style'=>'cards','columns'=>'3','pagination'=>'yes'),'blog-editorial.svg'),
            self::widget_item('advanced-accordion','Advanced Accordion','Modern SSS, hizmet detayları ve açılır içerik alanları için gelişmiş accordion.','wpsoft-advanced-accordion',array(),'contact.svg'),
            self::widget_item('team-carousel-pro','Team Carousel Pro','Ekip üyelerini modern görsel kartlarla sunan gelişmiş ekip widgetı.','wpsoft-team-carousel-pro',array(),'agency-premium.svg'),
            self::widget_item('video-popup-pro','Video Popup Pro','Kapak görselli video CTA ve popup deneyimi için modern video widgetı.','wpsoft-video-popup-pro',array(),'agency.svg'),
            self::widget_item('logo-grid-pro','Logo Grid Pro','Müşteri, partner ve referans logoları için modern responsive grid.','wpsoft-logo-grid-pro',array(),'corporate.svg'),
            self::widget_item('progress-pro','Progress Pro','Yetkinlik, kapasite ve performans değerleri için modern progress göstergeleri.','wpsoft-progress-pro',array(),'software.svg')
        );

        $sections=array();

        $sec_wrap=function($els,$bg='#ffffff',$pad=56){
            return array(self::container($els,array(
                'background_background'=>'classic',
                'background_color'=>$bg,
                'padding'=>array('unit'=>'px','top'=>(string)$pad,'right'=>'24','bottom'=>(string)$pad,'left'=>'24','isLinked'=>false)
            )));
        };

        // Hero Library 2.0 — sektöre göre farklı kompozisyonlar ve gerçek demo görselleri.

        // Contact Library 2.0 — WPForms uyumlu modern iletişim bölümleri.
        $sections[]=array(
            'key'=>'sec-contact-office','title'=>'İletişim · Ofis & Konum',
            'quality'=>'Modern','is_new'=>true,'desc'=>'Ofis, adres, çalışma saatleri ve ulaşım bilgileri için modern bölüm.',
            'category'=>'İletişim',
            'preview_image'=>WPST_URL.'assets/images/section-templates/contact-office.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-image-text',array(
                    'eyebrow'=>'OFİSİMİZ','title'=>'Bizi ziyaret etmek ister misiniz?',
                    'description'=>'Adres, çalışma saatleri ve ziyaret bilgilerinizi bu alanda paylaşın.',
                    'image'=>array('url'=>self::demo_v2('corporate-signature.svg')),
                    'button_text'=>'Yol Tarifi Al','button_url'=>array('url'=>'#')
                )),
                self::element('wpsoft-info-strip')
            ),'#f0fdf4',48)
        );

        $sections[]=array(
            'key'=>'sec-contact-faq-cta','title'=>'İletişim · SSS + CTA',
            'quality'=>'Modern','is_new'=>true,'desc'=>'Form öncesi veya sonrası kullanılabilecek SSS ve iletişim CTA bölümü.',
            'category'=>'SSS',
            'preview_image'=>WPST_URL.'assets/images/section-templates/contact-wpforms-split.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-heading',array(
                    'eyebrow'=>'MERAK ETTİKLERİNİZ','title'=>'İletişim öncesi sık sorulan sorular',
                    'description'=>'Süreç, teklif ve geri dönüş ile ilgili temel soruları burada yanıtlayın.'
                )),
                self::element('wpsoft-faq'),
                self::element('wpsoft-cta',array(
                    'title'=>'Sorunuz hâlâ mı var?',
                    'description'=>'Bize doğrudan ulaşın; size yardımcı olalım.',
                    'button_text'=>'İletişime Geç','button_url'=>array('url'=>'#iletisim-formu'),'bg'=>'#0f172a'
                ))
            ),'#ffffff',54)
        );


        // Ready Sections 2.0 — modern, birbirinden bağımsız bloklar.

        // Ready Sections 3.0 — 48 new professional blocks.

        // Ready Sections 3.0 — curated modern collection built on existing WPSoft widgets.
        $sections[]=array(
            'key'=>'sec-hero-bento-launch-v5','title'=>'Hero · Bento Launch','desc'=>'Ürün, SaaS ve ajans siteleri için bento kartlar ve güçlü CTA içeren modern hero.',
            'category'=>'Hero','quality'=>'Modern','style'=>'Bento','sector'=>'Genel','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/hero-bento-v2.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-gradient-heading',array('eyebrow'=>'BUILD / LAUNCH / GROW','title'=>'Markanızı daha güçlü bir dijital deneyime dönüştürün','text'=>'Büyük tipografi, net CTA ve modüler içerik yapısıyla modern bir başlangıç.')),
                self::element('wpsoft-feature-mosaic',array('title'=>'Tek alanda güçlü özellikler','image'=>array('url'=>self::demo_v2('saas-signature.svg')))),
                self::element('wpsoft-advanced-button',array('text'=>'Projeyi Başlat','url'=>array('url'=>'#iletisim'),'icon'=>'↗','effect'=>'lift'))
            ),'#f8fafc',58)
        );

        $sections[]=array(
            'key'=>'sec-services-glass-stack-v5','title'=>'Hizmetler · Glass Stack','desc'=>'Cam efektli modern servis kartları ve güven alanını bir araya getiren premium bölüm.',
            'category'=>'Hizmetler','quality'=>'Modern','style'=>'Glass','sector'=>'Kurumsal','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-services-hover-v4.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-gradient-heading',array('eyebrow'=>'SERVICES','title'=>'İhtiyacınıza göre şekillenen uçtan uca çözümler','text'=>'Strateji, tasarım ve teknik uygulamayı tek süreçte birleştirin.')),
                self::element('wpsoft-service-cards-pro',array('layout_variant'=>'horizontal','card_style'=>'soft')),
                self::element('wpsoft-trust-badges')
            ),'#0f172a',58)
        );

        $sections[]=array(
            'key'=>'sec-cta-bento-conversion-v5','title'=>'CTA · Bento Conversion','desc'=>'Teklif ve iletişim dönüşümü için bento düzenli güçlü kapanış bölümü.',
            'category'=>'CTA','quality'=>'Modern','style'=>'Bento','sector'=>'Genel','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/cta-gradient-v3.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-morphing-cta',array('eyebrow'=>'NEXT STEP','title'=>'Projenizi birlikte planlayalım','text'=>'İhtiyacınızı anlatın, size uygun yol haritasını birlikte oluşturalım.','button_text'=>'Teklif Al','button_url'=>array('url'=>'#iletisim'))),
                self::element('wpsoft-info-strip')
            ),'#eef2ff',46)
        );

        $sections[]=array(
            'key'=>'sec-testimonials-editorial-proof-v5','title'=>'Yorumlar · Editorial Proof','desc'=>'Büyük alıntılar, referans logoları ve sosyal kanıt odaklı editoryal yorum bölümü.',
            'category'=>'Yorumlar & Güven','quality'=>'Modern','style'=>'Editorial','sector'=>'Kurumsal','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-testimonials-quote-v4.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-heading',array('eyebrow'=>'CLIENT STORIES','title'=>'Sonuçlarımızı müşterilerimiz anlatsın','description'=>'Gerçek deneyimleri güçlü bir sosyal kanıt alanına dönüştürün.')),
                self::element('wpsoft-testimonial-slider',array('layout_variant'=>'profile','style_preset'=>'light')),
                self::element('wpsoft-logo-grid-pro')
            ),'#fff7ed',52)
        );

        $sections[]=array(
            'key'=>'sec-portfolio-dark-cases-v5','title'=>'Projeler · Dark Case Studies','desc'=>'Seçili işleri koyu premium zeminde portföy ve case-study hissiyle sunar.',
            'category'=>'Projeler','quality'=>'Modern','style'=>'Dark','sector'=>'Ajans','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-projects-hover-v4.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-gradient-heading',array('wpst_heading_color'=>'#f8fafc','wpst_body_color'=>'#cbd5e1','eyebrow'=>'SELECTED WORK','title'=>'Detaylarıyla öne çıkan işler','text'=>'Görsel odaklı portföy ve güçlü proje anlatımı.')),
                self::element('wpsoft-portfolio',array('layout_style'=>'cinematic','columns'=>'1')),
                self::element('wpsoft-advanced-button',array('text'=>'Tüm Projeler','url'=>array('url'=>'#projeler'),'icon'=>'↗','effect'=>'lift'))
            ),'#07111f',58)
        );

        

        


        // Sector Collections 1.0 — curated starter stacks for fast industry-specific page building.
        $sections[]=array(
            'key'=>'sec-sector-corporate-signature-v6','title'=>'Kurumsal · Signature Stack','desc'=>'Kurumsal firmalar için güçlü ilk mesaj, hizmet özeti, güven kanıtı ve teklif CTA akışı.',
            'category'=>'Sektör Setleri','quality'=>'Signature','style'=>'Modern','sector'=>'Kurumsal','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/templates/corporate-premium.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-hero-split-modern',array('eyebrow'=>'CORPORATE','title'=>'Güven veren dijital deneyim, ölçülebilir sonuçlar','text'=>'Uzmanlığınızı, hizmetlerinizi ve kurumsal güveninizi tek akışta anlatın.','image'=>array('url'=>self::demo_v2('corporate-signature.svg')),'primary_text'=>'Teklif Al','primary_url'=>array('url'=>'#iletisim'),'secondary_text'=>'Hizmetler','secondary_url'=>array('url'=>'#hizmetler'))),
                self::element('wpsoft-service-cards-pro',array('layout_variant'=>'modern','card_style'=>'soft','columns'=>'3','gap'=>array('size'=>18),'card_radius'=>array('size'=>20),'hover_effect'=>'lift')),
                self::element('wpsoft-trust-badges'),
                self::element('wpsoft-morphing-cta',array('eyebrow'=>'LET’S TALK','title'=>'İhtiyacınızı birlikte planlayalım','text'=>'Kısa bir görüşmeyle doğru çözüm yolunu netleştirelim.','button_text'=>'İletişime Geç','button_url'=>array('url'=>'#iletisim')))
            ),'#f8fafc',58)
        );

        $sections[]=array(
            'key'=>'sec-sector-agency-signature-v6','title'=>'Ajans · Creative Signature Stack','desc'=>'Ajans ve yaratıcı stüdyolar için portföy, motion, sosyal kanıt ve proje CTA akışı.',
            'category'=>'Sektör Setleri','quality'=>'Signature','style'=>'Creative','sector'=>'Ajans','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/templates/agency-premium.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-hero-spotlight',array('eyebrow'=>'CREATIVE STUDIO','title'=>'Fikirleri dikkat çeken dijital deneyimlere dönüştürüyoruz','text'=>'Strateji, tasarım ve teknolojiyi tek yaratıcı sistemde birleştirin.','button_text'=>'Projeyi Başlat','button_url'=>array('url'=>'#iletisim'))),
                self::element('wpsoft-marquee-text'),
                self::element('wpsoft-hover-reveal'),
                self::element('wpsoft-portfolio'),
                self::element('wpsoft-testimonial-slider')
            ),'#07111f',60)
        );

        $sections[]=array(
            'key'=>'sec-sector-industry-signature-v6','title'=>'Makina / Sanayi · Technical Stack','desc'=>'Makina, CNC ve üretim firmaları için teknik kapasite, ürün vitrini, güven ve servis CTA akışı.',
            'category'=>'Sektör Setleri','quality'=>'Signature','style'=>'Dark','sector'=>'Sanayi','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/templates/industrial-premium.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-hero-industry',array('wpst_heading_color'=>'#ffffff','wpst_body_color'=>'#94a3b8','wpst_button_background'=>'#f59e0b','wpst_button_text_color'=>'#111827','hero_radius'=>array('size'=>30),'eyebrow'=>'INDUSTRIAL CAPABILITY','title'=>'Üretim gücünüzü teknik verilerle kanıtlayın','text'=>'Makina parkuru, kapasite, kalite ve servis gücünü B2B odaklı bir deneyimle sunun.','image'=>array('url'=>self::demo_v2('industry-signature.svg')),'button_text'=>'Teknik Teklif Al','button_url'=>array('url'=>'#iletisim'))),
                self::element('wpsoft-feature-mosaic',array('title'=>'Teknik altyapı ve üretim kabiliyeti','image'=>array('url'=>self::demo('industry.svg')))),
                self::element('wpsoft-product-showcase'),
                self::element('wpsoft-stats-grid'),
                self::element('wpsoft-morphing-cta',array('eyebrow'=>'TECHNICAL SUPPORT','title'=>'Projeniz için doğru çözümü birlikte belirleyelim','text'=>'Teknik ekibimizle ihtiyaç, kapasite ve uygulama detaylarını değerlendirin.','button_text'=>'Servis / Teklif Talebi','button_url'=>array('url'=>'#iletisim')))
            ),'#07111f',60)
        );

        $sections[]=array(
            'key'=>'sec-sector-hotel-signature-v6','title'=>'Otel · Hospitality Signature Stack','desc'=>'Otel ve resort siteleri için deneyim odaklı hero, olanaklar, galeri, yorumlar ve rezervasyon CTA akışı.',
            'category'=>'Sektör Setleri','quality'=>'Signature','style'=>'Editorial','sector'=>'Otel','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/templates/hotel-premium.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-hero-hospitality',array('wpst_heading_color'=>'#2b2118','wpst_body_color'=>'#6b5d50','wpst_button_background'=>'#8b5e3c','wpst_button_text_color'=>'#ffffff','eyebrow'=>'WELCOME','title'=>'Konaklamadan fazlasını sunan bir deneyim','text'=>'Odalar, olanaklar ve destinasyon hikâyesini güçlü bir ilk ekranda anlatın.','image'=>array('url'=>self::demo_v2('hotel-signature.svg')),'button_text'=>'Rezervasyon Yap','button_url'=>array('url'=>'#rezervasyon'))),
                self::element('wpsoft-icon-grid'),
                self::element('wpsoft-gallery-zoom-pro',array('layout'=>'editorial','hover_effect'=>'soft','lightbox'=>'yes','lightbox_style'=>'clean','gap'=>array('size'=>14),'radius'=>array('size'=>18))),
                self::element('wpsoft-testimonial-slider',array('style_preset'=>'editorial','layout_variant'=>'profile','touch_swipe'=>'yes','mouse_drag'=>'yes','radius'=>array('size'=>20))),
                self::element('wpsoft-booking-strip',array('quality_gap'=>array('size'=>14),'quality_radius'=>array('size'=>18),'quality_padding'=>array('top'=>18,'right'=>18,'bottom'=>18,'left'=>18,'unit'=>'px')))
            ),'#f7f3ec',58)
        );

        $sections[]=array(
            'key'=>'sec-sector-health-signature-v6','title'=>'Sağlık · Trust Signature Stack','desc'=>'Klinik ve sağlık merkezleri için güven, uzmanlık, ekip, sonuç ve randevu odaklı hazır akış.',
            'category'=>'Sektör Setleri','quality'=>'Signature','style'=>'Minimal','sector'=>'Sağlık','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/templates/clinic-premium.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-hero-medical',array('wpst_heading_color'=>'#0f172a','wpst_body_color'=>'#475569','wpst_button_background'=>'#0f766e','wpst_button_text_color'=>'#ffffff','hero_radius'=>array('size'=>30),'eyebrow'=>'TRUSTED CARE','title'=>'Uzmanlık, güven ve modern hasta deneyimi','text'=>'Tedavi alanlarını, uzman kadroyu ve randevu sürecini açık ve güven veren biçimde sunun.','image'=>array('url'=>self::demo_v2('clinic-signature.svg')),'button_text'=>'Randevu Al','button_url'=>array('url'=>'#randevu'))),
                self::element('wpsoft-service-cards-pro',array('layout_variant'=>'modern','card_style'=>'soft','columns'=>'3','gap'=>array('size'=>18),'card_radius'=>array('size'=>20),'hover_effect'=>'lift')),
                self::element('wpsoft-team-carousel-pro',array('layout_variant'=>'compact','image_height'=>array('size'=>320),'card_radius'=>array('size'=>20),'hover_motion'=>'lift')),
                self::element('wpsoft-before-after'),
                self::element('wpsoft-trust-badges'),
                self::element('wpsoft-cta',array('wpst_button_background'=>'#0f766e','wpst_button_text_color'=>'#ffffff','wpst_button_hover_background'=>'#115e59','wpst_button_hover_text_color'=>'#ffffff','title'=>'Uzman ekibimizle görüşün','text'=>'Size uygun randevu zamanını birlikte planlayalım.','button_text'=>'Randevu Talebi','button_url'=>array('url'=>'#randevu')))
            ),'#f0fdfa',58)
        );

        $sections[]=array(
            'key'=>'sec-sector-ecommerce-signature-v6','title'=>'E‑Ticaret · Commerce Signature Stack','desc'=>'Ürün odaklı mağazalar için kampanya hero, ürün vitrini, güven kanıtı ve satış CTA akışı.',
            'category'=>'Sektör Setleri','quality'=>'Signature','style'=>'Bento','sector'=>'E-Ticaret','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/templates/ecommerce-premium.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-hero-commerce',array('eyebrow'=>'NEW COLLECTION','title'=>'Ürünlerinizi daha güçlü bir alışveriş deneyimiyle sunun','text'=>'Kampanya, kategori ve öne çıkan ürünleri dönüşüm odaklı bir başlangıçta birleştirin.','image'=>array('url'=>self::demo_v2('commerce-signature.svg')),'button_text'=>'Alışverişe Başla','button_url'=>array('url'=>'#urunler'))),
                self::element('wpsoft-product-showcase',array('quality_gap'=>array('size'=>16),'quality_radius'=>array('size'=>20),'quality_padding'=>array('top'=>18,'right'=>18,'bottom'=>18,'left'=>18,'unit'=>'px'))),
                self::element('wpsoft-badge-grid'),
                self::element('wpsoft-testimonial-slider'),
                self::element('wpsoft-morphing-cta',array('eyebrow'=>'LIMITED OFFER','title'=>'En çok tercih edilenleri keşfedin','text'=>'Öne çıkan koleksiyonu ve avantajları tek tıkla inceleyin.','button_text'=>'Ürünleri Gör','button_url'=>array('url'=>'#urunler')))
            ),'#fff7ed',58)
        );

        $sections[]=array(
            'key'=>'sec-sector-saas-signature-v6','title'=>'SaaS · Product Signature Stack','desc'=>'SaaS ve yazılım ürünleri için ürün hero, özellikler, entegrasyonlar, metrikler ve conversion CTA akışı.',
            'category'=>'Sektör Setleri','quality'=>'Signature','style'=>'Glass','sector'=>'SaaS','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/templates/saas-premium.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-hero-saas',array('wpst_heading_color'=>'#111827','wpst_body_color'=>'#4b5563','wpst_button_background'=>'#4f46e5','wpst_button_text_color'=>'#ffffff','eyebrow'=>'PRODUCT PLATFORM','title'=>'Daha hızlı çalışan ekipler için tek ürün deneyimi','text'=>'Temel değer önerisini, ürün kabiliyetlerini ve dönüşüm CTA’sını aynı akışta sunun.','image'=>array('url'=>self::demo_v2('saas-signature.svg')),'button_text'=>'Ücretsiz Başla','button_url'=>array('url'=>'#basla'))),
                self::element('wpsoft-tabs-modern'),
                self::element('wpsoft-logo-marquee'),
                self::element('wpsoft-stats-grid',array('style_preset'=>'soft','layout_variant'=>'strip','columns'=>'4','radius'=>array('size'=>18))),
                self::element('wpsoft-pricing',array('style_preset'=>'soft','layout_variant'=>'compact','radius'=>array('size'=>20))),
                self::element('wpsoft-morphing-cta',array('eyebrow'=>'START NOW','title'=>'Ürünü bugün deneyin','text'=>'Kurulum süresini kısaltın ve ekibinizi aynı sistemde buluşturun.','button_text'=>'Hemen Başla','button_url'=>array('url'=>'#basla')))
            ),'#eef2ff',58)
        );


        /*
         * Template Library 3.0 · Signature Sections
         * Daha güçlü tipografi, asimetrik yerleşim ve Global Design tokenlarıyla
         * WPSoft'un premium section koleksiyonu.
         */
        $signature_heading=function($eyebrow,$title,$description=''){
            return self::element('wpsoft-heading',array(
                'eyebrow'=>$eyebrow,'title'=>$title,'description'=>$description
            ));
        };
        $signature_pair=function($left,$right,$bg='#ffffff',$left_width=48,$pad=68){
            return array(self::container(array(
                self::container(array($left),array(
                    'content_width'=>'full',
                    'width'=>array('unit'=>'%','size'=>$left_width,'sizes'=>array()),
                    'width_tablet'=>array('unit'=>'%','size'=>100,'sizes'=>array()),
                    'width_mobile'=>array('unit'=>'%','size'=>100,'sizes'=>array()),
                    'padding'=>array('unit'=>'px','top'=>'0','right'=>'22','bottom'=>'0','left'=>'0','isLinked'=>false)
                )),
                self::container(array($right),array(
                    'content_width'=>'full',
                    'width'=>array('unit'=>'%','size'=>(100-$left_width),'sizes'=>array()),
                    'width_tablet'=>array('unit'=>'%','size'=>100,'sizes'=>array()),
                    'width_mobile'=>array('unit'=>'%','size'=>100,'sizes'=>array()),
                    'padding'=>array('unit'=>'px','top'=>'0','right'=>'0','bottom'=>'0','left'=>'22','isLinked'=>false)
                ))
            ),array(
                'content_width'=>'boxed','boxed_width'=>array('unit'=>'px','size'=>1240,'sizes'=>array()),
                'flex_direction'=>'row','flex_wrap'=>'wrap','align_items'=>'center',
                'background_background'=>'classic','background_color'=>$bg,
                'padding'=>array('unit'=>'px','top'=>(string)$pad,'right'=>'28','bottom'=>(string)$pad,'left'=>'28','isLinked'=>false),
                'border_radius'=>array('unit'=>'px','top'=>'28','right'=>'28','bottom'=>'28','left'=>'28','isLinked'=>true)
            )));
        };

        $sections[]=array(
            'key'=>'sec-hero-executive-premium','title'=>'Hero · Executive Signature',
            'desc'=>'Büyük tipografi, split görsel, güven metrikleri ve çift CTA içeren premium kurumsal hero.',
            'category'=>'Hero','quality'=>'Signature',
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-hero-executive-premium.svg',
            'style'=>'Minimal','sector'=>'Kurumsal','premium'=>1,
            'data'=>$sec_wrap(array(
                self::element('wpsoft-hero-split-modern',array('composition'=>'showcase',
                    'eyebrow'=>'STRATEGY · DESIGN · TECHNOLOGY',
                    'title'=>'Dijitalde daha güçlü, daha net bir marka deneyimi',
                    'text'=>'Modern tipografi, güçlü içerik hiyerarşisi ve dönüşüm odaklı kullanıcı deneyimini tek yapıda birleştirin.',
                    'image'=>array('url'=>self::demo_v2('corporate-signature.svg')),
                    'primary_text'=>'Projeyi Başlat','primary_url'=>array('url'=>'#iletisim'),
                    'secondary_text'=>'Çalışmalarımız','secondary_url'=>array('url'=>'#projeler')
                )),
                self::element('wpsoft-trust-badges')
            ),'#f8fafc',24)
        );

        $sections[]=array(
            'key'=>'sec-hero-editorial-luxe-premium','title'=>'Hero · Editorial Luxe',
            'desc'=>'Serif karakter, geniş beyaz alan ve editorial görsel diliyle premium marka hero bölümü.',
            'category'=>'Hero','quality'=>'Signature',
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-hero-editorial-luxe-premium.svg',
            'style'=>'Editorial','sector'=>'Premium','premium'=>1,
            'data'=>$sec_wrap(array(
                self::element('wpsoft-gradient-heading',array(
                    'eyebrow'=>'SELECTED EXPERIENCE',
                    'title'=>'Detayların markayı tanımladığı bir dijital deneyim',
                    'text'=>'Minimal arayüz, güçlü hikâye ve editoryal görsellerle zamansız bir sunum oluşturun.'
                )),
                self::element('wpsoft-image-reveal',array(
                    'image'=>array('url'=>self::demo('architecture.svg')),
                    'caption'=>'Selected Story · 2026','direction'=>'center'
                ))
            ),'#fffdf8',64)
        );

        $sections[]=array(
            'key'=>'sec-about-story-premium','title'=>'Hakkımızda · Brand Story',
            'desc'=>'Marka hikâyesi, katmanlı görsel ve güçlü metin hiyerarşisiyle premium hakkımızda bölümü.',
            'category'=>'Hakkımızda','quality'=>'Signature',
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-about-story-premium.svg',
            'style'=>'Editorial','sector'=>'Kurumsal','premium'=>1,
            'data'=>$signature_pair(
                self::element('wpsoft-image-cascade',array(
                    'image_one'=>array('url'=>self::demo('corporate.svg')),
                    'image_two'=>array('url'=>self::demo('agency.svg')),
                    'image_three'=>array('url'=>self::demo('architecture.svg'))
                )),
                self::element('wpsoft-image-text',array(
                    'eyebrow'=>'OUR STORY','title'=>'Yılların deneyimini yeni nesil bakış açısıyla birleştiriyoruz',
                    'description'=>'Sadece hizmet üretmiyor; strateji, tasarım ve teknoloji arasında sürdürülebilir bir sistem kuruyoruz.',
                    'button_text'=>'Hikâyemizi Keşfet','button_url'=>array('url'=>'#')
                )),
                '#ffffff',52,76
            )
        );

        $sections[]=array(
            'key'=>'sec-about-metrics-premium','title'=>'Hakkımızda · Metrics & Values',
            'desc'=>'Büyük sayı metrikleri, değerler ve güven alanlarını tek premium bölümde birleştirir.',
            'category'=>'Hakkımızda','quality'=>'Signature',
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-about-metrics-premium.svg',
            'style'=>'Modern','sector'=>'Kurumsal','premium'=>1,
            'data'=>$sec_wrap(array(
                $signature_heading('WHY WPSOFT','Sonuç üreten net bir çalışma sistemi','Deneyim, süreç ve ölçülebilir sonuçları aynı anlatı içinde sunun.'),
                self::element('wpsoft-number-cards'),
                self::element('wpsoft-stats-grid')
            ),'#eff6ff',66)
        );

        $sections[]=array(
            'key'=>'sec-services-dark-premium','title'=>'Hizmetler · Dark Expertise',
            'desc'=>'Koyu premium yüzey, uzmanlık kartları ve süreç alanıyla teknoloji/ajans hizmet bölümü.',
            'category'=>'Hizmetler','quality'=>'Signature',
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-services-dark-premium.svg',
            'style'=>'Dark','sector'=>'Yazılım','premium'=>1,
            'data'=>$sec_wrap(array(
                self::element('wpsoft-gradient-heading',array(
                    'eyebrow'=>'EXPERTISE','title'=>'Karmaşık ihtiyaçları sade ve ölçeklenebilir çözümlere dönüştürüyoruz',
                    'text'=>'Strateji, UX, geliştirme ve optimizasyon tek akışta.'
                )),
                self::element('wpsoft-reveal-cards'),
                self::element('wpsoft-process-steps-pro')
            ),'#07111f',70)
        );

        

        $sections[]=array(
            'key'=>'sec-blog-featured-premium','title'=>'Blog · Featured Insight',
            'desc'=>'Editoryal başlık ve dinamik yazı listesiyle modern kurumsal blog/insight bölümü.',
            'category'=>'Blog','quality'=>'Signature',
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-blog-featured-premium.svg',
            'style'=>'Editorial','sector'=>'Blog','premium'=>1,
            'data'=>$sec_wrap(array(
                $signature_heading('INSIGHTS','Fikirler, rehberler ve sektörden güncel notlar','Uzmanlığınızı düzenli içerikle görünür hale getirin.'),
                self::element('wpsoft-blog-posts',array('layout_style'=>'editorial-feed','columns'=>'3','posts_per_page'=>6))
            ),'#f8fafc',70)
        );

        $sections[]=array(
            'key'=>'sec-blog-magazine-premium','title'=>'Blog · Magazine Grid',
            'desc'=>'Magazine hissi veren dinamik blog grid ve güçlü editoryal başlık düzeni.',
            'category'=>'Blog','quality'=>'Signature',
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-blog-magazine-premium.svg',
            'style'=>'Editorial','sector'=>'Blog','premium'=>1,
            'data'=>$sec_wrap(array(
                self::element('wpsoft-gradient-heading',array(
                    'eyebrow'=>'JOURNAL','title'=>'Okumaya değer yeni hikâyeler',
                    'text'=>'Haber, rehber, vaka analizi ve ilham veren içerikleri modern bir vitrinde sunun.'
                )),
                self::element('wpsoft-blog-posts',array('layout_style'=>'visual-journal','columns'=>'2','posts_per_page'=>4))
            ),'#ffffff',72)
        );

        $sections[]=array(
            'key'=>'sec-proof-signature-premium','title'=>'Sosyal Kanıt · Signature Proof',
            'desc'=>'Referans logoları, müşteri yorumları ve istatistikleri tek premium güven bölümünde birleştirir.',
            'category'=>'Yorumlar & Güven','quality'=>'Signature',
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-proof-signature-premium.svg',
            'style'=>'Modern','sector'=>'Kurumsal','premium'=>1,
            'data'=>$sec_wrap(array(
                $signature_heading('TRUSTED BY','Güven, sonuçlarla görünür olur','Referanslarınızı, müşteri deneyimini ve önemli metrikleri tek alanda gösterin.'),
                self::element('wpsoft-logo-marquee'),
                self::element('wpsoft-testimonial-slider',array('layout_variant'=>'stage','style_preset'=>'light')),
                self::element('wpsoft-stats-grid')
            ),'#eff6ff',62)
        );

        


        /*
         * Template Library 3.3 · Essentials-inspired modern composition system.
         * Bu koleksiyon birebir tema kopyası değildir; ferah spacing, güçlü tipografi,
         * layered cards, glass surfaces, asymmetric grids ve touch-first responsive yapı
         * gibi modern prensipleri WPSoft tasarım diline uygular.
         */
        $ess_section=function($elements,$bg='#ffffff',$pad=84,$class='wpst-ess-section'){
            return self::container($elements,array(
                '_css_classes'=>$class,
                'content_width'=>'boxed',
                'boxed_width'=>array('unit'=>'px','size'=>1280,'sizes'=>array()),
                'background_background'=>'classic',
                'background_color'=>$bg,
                'padding'=>array('unit'=>'px','top'=>(string)$pad,'right'=>'28','bottom'=>(string)$pad,'left'=>'28','isLinked'=>false),
                'padding_tablet'=>array('unit'=>'px','top'=>'58','right'=>'22','bottom'=>'58','left'=>'22','isLinked'=>false),
                'padding_mobile'=>array('unit'=>'px','top'=>'38','right'=>'18','bottom'=>'38','left'=>'18','isLinked'=>false)
            ));
        };

        $ess_split=function($left,$right,$bg='#ffffff',$widths=array(48,52),$class='wpst-ess-split'){
            $cols=array();
            foreach(array($left,$right) as $i=>$el){
                $cols[]=array(
                    'id'=>self::uid(),'elType'=>'container','isInner'=>true,
                    'settings'=>array(
                        '_css_classes'=>'wpst-ess-split-col',
                        'content_width'=>'full',
                        'width'=>array('unit'=>'%','size'=>$widths[$i],'sizes'=>array()),
                        'width_tablet'=>array('unit'=>'%','size'=>50,'sizes'=>array()),
                        'width_mobile'=>array('unit'=>'%','size'=>100,'sizes'=>array()),
                        'flex_direction'=>'column',
                        'gap'=>array('unit'=>'px','size'=>22,'row'=>22,'column'=>22),
                        'padding'=>array('unit'=>'px','top'=>'0','right'=>'14','bottom'=>'0','left'=>'14','isLinked'=>false)
                    ),
                    'elements'=>array($el)
                );
            }
            return self::container($cols,array(
                '_css_classes'=>$class,
                'content_width'=>'boxed','boxed_width'=>array('unit'=>'px','size'=>1280,'sizes'=>array()),
                'flex_direction'=>'row','flex_wrap'=>'wrap','align_items'=>'center',
                'background_background'=>'classic','background_color'=>$bg,
                'gap'=>array('unit'=>'px','size'=>0,'row'=>28,'column'=>0),
                'padding'=>array('unit'=>'px','top'=>'84','right'=>'14','bottom'=>'84','left'=>'14','isLinked'=>false),
                'padding_tablet'=>array('unit'=>'px','top'=>'58','right'=>'12','bottom'=>'58','left'=>'12','isLinked'=>false),
                'padding_mobile'=>array('unit'=>'px','top'=>'38','right'=>'8','bottom'=>'38','left'=>'8','isLinked'=>false)
            ));
        };

        

        $sections[]=array(
            'key'=>'sec-about-asymmetric-signature','title'=>'Hakkımızda · Asymmetric Story',
            'desc'=>'Asimetrik görsel yerleşim, güçlü editorial metin ve metriklerle modern marka hikayesi.',
            'category'=>'Hakkımızda',
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-about-asymmetric-signature.svg',
            'style'=>'Editorial','sector'=>'Kurumsal','premium'=>1,'quality'=>'Signature',
            'data'=>$ess_split(
                self::element('wpsoft-image-cascade',array('layout'=>'editorial','media_height'=>array('size'=>560),'image_gap'=>array('size'=>18),'image_radius'=>array('size'=>22),'image_shadow'=>'soft',
                    'image_one'=>array('url'=>self::demo('corporate.svg')),
                    'image_two'=>array('url'=>self::demo('agency.svg')),
                    'image_three'=>array('url'=>self::demo('architecture.svg'))
                )),
                self::container(array(
                    self::element('wpsoft-heading',array(
                        'eyebrow'=>'OUR STORY','title'=>'İyi tasarım, doğru hiyerarşiyle başlar',
                        'description'=>'Strateji, içerik ve kullanıcı deneyimini tek sistem içinde birleştiriyoruz.'
                    )),
                    self::element('wpsoft-number-cards',array('quality_gap'=>array('size'=>14),'quality_radius'=>array('size'=>18))),
                    self::element('wpsoft-advanced-button',array('text'=>'Hikâyemizi Keşfet','url'=>array('url'=>'#')))
                ),array('content_width'=>'full')),
                '#ffffff',array(54,46),'wpst-ess-split wpst-ess-about-asym'
            )
        );

        $sections[]=array(
            'key'=>'sec-services-floating-signature','title'=>'Hizmetler · Floating Cards',
            'desc'=>'Yumuşak gölgeli floating kartlar, geniş başlık ve modern hizmet akışı.',
            'category'=>'Hizmetler',
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-services-floating-signature.svg',
            'style'=>'Modern','sector'=>'Ajans','premium'=>1,'quality'=>'Signature',
            'data'=>$ess_section(array(
                self::element('wpsoft-heading',array('wpst_heading_color'=>'#0f172a','wpst_body_color'=>'#64748b','wpst_small_font_size'=>array('size'=>11,'unit'=>'px'),'wpst_heading_line_height'=>array('size'=>1.05,'unit'=>'em'),
                    'eyebrow'=>'WHAT WE DO','title'=>'İhtiyaca göre şekillenen yaratıcı ve teknik çözümler',
                    'description'=>'Her hizmet, net fayda ve güçlü kullanıcı deneyimi etrafında tasarlanır.'
                )),
                self::element('wpsoft-service-cards-pro',array('layout_variant'=>'editorial','card_style'=>'minimal','gap'=>array('size'=>18),'card_radius'=>array('size'=>22),'hover_effect'=>'lift')),
                self::element('wpsoft-process-steps-pro')
            ),'#f7f8fc',82,'wpst-ess-section wpst-ess-services-floating')
        );

        $sections[]=array(
            'key'=>'sec-projects-showcase-signature','title'=>'Projeler · Immersive Showcase',
            'desc'=>'Büyük görseller, staggered kompozisyon ve modern hover/reveal hareketleriyle proje vitrini.',
            'category'=>'Projeler',
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-projects-showcase-signature.svg',
            'style'=>'Creative','sector'=>'Ajans','premium'=>1,'quality'=>'Signature',
            'data'=>$ess_section(array(
                self::element('wpsoft-heading',array(
                    'eyebrow'=>'SELECTED WORK','title'=>'Son projelerden seçilmiş çalışmalar',
                    'description'=>'Görsel hikâyeyi daha büyük ve daha cesur bir kompozisyonla sunun.'
                )),
                self::element('wpsoft-hover-reveal'),
                self::element('wpsoft-media-card-pro')
            ),'#0b1020',86,'wpst-ess-section wpst-ess-projects-dark')
        );

        

        $sections[]=array(
            'key'=>'sec-pricing-clean-signature','title'=>'Fiyatlandırma · Clean Plans',
            'desc'=>'Temiz fiyat kartları, güçlü featured plan ve mobilde dokunmatik seçim odaklı yapı.',
            'category'=>'Fiyatlandırma',
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-pricing-clean-signature.svg',
            'style'=>'Minimal','sector'=>'Yazılım','premium'=>1,'quality'=>'Signature',
            'data'=>$ess_section(array(
                self::element('wpsoft-heading',array('wpst_heading_color'=>'#0f172a','wpst_body_color'=>'#64748b','wpst_small_font_size'=>array('size'=>11,'unit'=>'px'),
                    'eyebrow'=>'PRICING','title'=>'İhtiyacınıza uygun sade planlar',
                    'description'=>'Karar vermeyi kolaylaştıran net ve karşılaştırılabilir fiyatlandırma.'
                )),
                self::element('wpsoft-pricing',array('layout_variant'=>'statement','style_preset'=>'minimal','radius'=>array('size'=>22),'padding'=>array('top'=>30,'right'=>30,'bottom'=>30,'left'=>30,'unit'=>'px'))),
                self::element('wpsoft-trust-badges')
            ),'#ffffff',78,'wpst-ess-section wpst-ess-pricing-clean')
        );

        $sections[]=array(
            'key'=>'sec-faq-split-signature','title'=>'SSS · Split Support',
            'desc'=>'Büyük yardımcı başlık ve sade accordion ile modern destek/SSS bölümü.',
            'category'=>'SSS',
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-faq-split-signature.svg',
            'style'=>'Minimal','sector'=>'Kurumsal','premium'=>1,'quality'=>'Signature',
            'data'=>$ess_split(
                self::element('wpsoft-heading',array('wpst_heading_color'=>'#0f172a','wpst_body_color'=>'#64748b','wpst_heading_line_height'=>array('size'=>1.08,'unit'=>'em'),
                    'eyebrow'=>'FAQ','title'=>'Merak ettiklerinizi hızlıca yanıtlayalım',
                    'description'=>'Sık sorulan soruları sade ve anlaşılır bir yapı içinde sunun.'
                )),
                self::element('wpsoft-faq',array('style_preset'=>'clean','radius'=>array('size'=>16),'first_open'=>'yes')),
                '#f8fafc',array(38,62),'wpst-ess-split wpst-ess-faq-split'
            )
        );

        $sections[]=array(
            'key'=>'sec-contact-layered-signature','title'=>'İletişim · Layered Contact',
            'desc'=>'İletişim kartları, form, floating info ve güçlü CTA ile premium kapanış bölümü.',
            'category'=>'İletişim',
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-contact-layered-signature.svg',
            'style'=>'Modern','sector'=>'İletişim','premium'=>1,'quality'=>'Signature',
            'data'=>$ess_split(
                self::container(array(
                    self::element('wpsoft-heading',array('wpst_heading_color'=>'#0f172a','wpst_body_color'=>'#64748b','wpst_small_font_size'=>array('size'=>11,'unit'=>'px'),
                        'eyebrow'=>'LET’S TALK','title'=>'Bir sonraki projenizi birlikte konuşalım',
                        'description'=>'Kısa bir mesaj bırakın; ihtiyacınızı birlikte netleştirelim.'
                    )),
                    self::element('wpsoft-contact-cards',array('style_preset'=>'clean','columns'=>'2','radius'=>array('size'=>18)))
                ),array('content_width'=>'full')),
                self::element('wpsoft-wpforms',array(
                    'empty_title'=>'Formunuzu Seçin',
                    'empty_text'=>'WPForms formu bu modern kart içinde görüntülenir.','shell_style'=>'card','shell_radius'=>array('size'=>22),'input_radius'=>array('size'=>12)
                )),
                '#ffffff',array(44,56),'wpst-ess-split wpst-ess-contact-layered'
            )
        );


        // Inspiration Collection 2026 — WoodMart / Essentials / Salient patterns,
        // rebuilt as original WPSoft sections using existing WPSoft Elementor widgets.
        $sections[]=array(
            'key'=>'sec-hero-commerce-editorial-v7','title'=>'Hero · Commerce Editorial',
            'desc'=>'Kategori odaklı mağaza ve ürün siteleri için geniş görsel, kısa mesaj ve güçlü CTA kompozisyonu.',
            'category'=>'Hero','style'=>'Editorial','sector'=>'E-Ticaret','quality'=>'Signature','is_new'=>true,'is_popular'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/hero-commerce-editorial-v7.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-hero-slider',array()),
                self::element('wpsoft-logo-cloud',array())
            ),'#f6f3ee',42)
        );

        $sections[]=array(
            'key'=>'sec-hero-creative-parallax-v7','title'=>'Hero · Creative Parallax',
            'desc'=>'Ajans, portföy ve yaratıcı markalar için büyük tipografi, görsel hareket ve aşağı akış hissi.',
            'category'=>'Hero','style'=>'Creative','sector'=>'Ajans','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/hero-creative-parallax-v7.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-animated-heading',array()),
                self::element('wpsoft-image-text',array(
                    'eyebrow'=>'CREATIVE STUDIO','title'=>'Fikirleri güçlü dijital deneyimlere dönüştürüyoruz',
                    'description'=>'Büyük tipografi ve görsel anlatımla ilk ekranda güçlü bir marka etkisi oluşturun.',
                    'image'=>array('url'=>self::demo_v2('agency-signature.svg')),
                    'button_text'=>'Projeleri Keşfet','button_url'=>array('url'=>'#projeler')
                ))
            ),'#f8fafc',48)
        );

        $sections[]=array(
            'key'=>'sec-hero-minimal-saas-v7','title'=>'Hero · Minimal SaaS',
            'desc'=>'SaaS ve teknoloji ürünleri için sade başlık, ürün anlatımı, güven logoları ve net dönüşüm akışı.',
            'category'=>'Hero','style'=>'Minimal','sector'=>'Teknoloji','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/hero-minimal-saas-v7.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-heading',array(
                    'eyebrow'=>'SMARTER WORKFLOW','title'=>'Daha az karmaşa, daha hızlı sonuç',
                    'description'=>'Ürününüzün temel değerini sade bir ilk ekran ve güçlü sosyal kanıt ile anlatın.'
                )),
                self::element('wpsoft-image-text',array(
                    'title'=>'Tek ekranda tüm iş akışınız','description'=>'Ürün ekran görüntüsü veya arayüz görselinizi burada öne çıkarın.',
                    'image'=>array('url'=>self::demo_v2('saas-signature.svg')),
                    'button_text'=>'Ücretsiz Başla','button_url'=>array('url'=>'#basla')
                )),
                self::element('wpsoft-logo-cloud',array())
            ),'#ffffff',52)
        );

        $sections[]=array(
            'key'=>'sec-about-overlap-story-v7','title'=>'Hakkımızda · Overlap Story',
            'desc'=>'Kurumsal hikâyeyi görsel, metin ve sayısal güven unsurlarıyla katmanlı şekilde anlatır.',
            'category'=>'Hakkımızda','style'=>'Overlap','sector'=>'Kurumsal','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/about-overlap-story-v7.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-image-text',array(
                    'eyebrow'=>'OUR STORY','title'=>'Deneyim ile yeniliği aynı noktada buluşturuyoruz',
                    'description'=>'Marka hikâyenizi, yaklaşımınızı ve fark yaratan yönlerinizi güçlü bir görsel eşliğinde anlatın.',
                    'image'=>array('url'=>self::demo_v2('corporate-signature.svg')),
                    'button_text'=>'Bizi Tanıyın','button_url'=>array('url'=>'#'),'layout_style'=>'overlap','radius'=>array('size'=>26),'min_height'=>array('size'=>520)
                )),
                self::element('wpsoft-stats-grid',array('style_preset'=>'soft','layout_variant'=>'cards','columns'=>'3','radius'=>array('size'=>18)))
            ),'#f8fafc',62)
        );

        $sections[]=array(
            'key'=>'sec-features-icon-editorial-v7','title'=>'Özellikler · Icon Editorial',
            'desc'=>'Ürün ve hizmet avantajlarını temiz ikon kartları ve güçlü başlık hiyerarşisiyle sunar.',
            'category'=>'Özellikler','style'=>'Clean','sector'=>'Genel','quality'=>'Modern','is_new'=>true,'premium'=>0,
            'preview_image'=>WPST_URL.'assets/images/section-templates/features-icon-editorial-v7.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-heading',array(
                    'eyebrow'=>'WHY US','title'=>'Karmaşık özellikleri kolay anlaşılır hale getirin',
                    'description'=>'İkon, kısa başlık ve açıklamalarla değer önerilerinizi hızlıca taranabilir yapın.'
                )),
                self::element('wpsoft-icon-grid',array())
            ),'#ffffff',56)
        );

        $sections[]=array(
            'key'=>'sec-features-numbered-process-v7','title'=>'Özellikler · Numbered Process',
            'desc'=>'Adımları veya hizmet sürecini numaralı kartlarla anlatan modern ve okunaklı süreç bölümü.',
            'category'=>'Süreç','style'=>'Numbered','sector'=>'Genel','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/features-numbered-process-v7.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-heading',array(
                    'eyebrow'=>'HOW IT WORKS','title'=>'Basit, şeffaf ve ölçülebilir bir süreç',
                    'description'=>'Ziyaretçiye sonraki adımı net biçimde gösteren dört aşamalı akış.'
                )),
                self::element('wpsoft-number-cards',array())
            ),'#f1f5f9',58)
        );

        $sections[]=array(
            'key'=>'sec-categories-visual-grid-v7','title'=>'Kategoriler · Visual Grid',
            'desc'=>'Ürün, hizmet veya içerik kategorilerini büyük görsel kartlarla keşfedilebilir hale getirir.',
            'category'=>'Kategoriler','style'=>'Visual Grid','sector'=>'Genel','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/categories-visual-grid-v7.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-heading',array(
                    'eyebrow'=>'EXPLORE','title'=>'İhtiyacınıza göre keşfedin',
                    'description'=>'Öne çıkan kategorileri görsel ağırlıklı bir vitrin düzeninde sunun.'
                )),
                self::element('wpsoft-reveal-cards',array())
            ),'#ffffff',54)
        );

        $sections[]=array(
            'key'=>'sec-showcase-masonry-gallery-v7','title'=>'Showcase · Masonry Gallery',
            'desc'=>'Projeler, ürünler ve mekan fotoğrafları için zoom destekli modern galeri vitrini.',
            'category'=>'Galeri','style'=>'Masonry','sector'=>'Genel','quality'=>'Signature','is_new'=>true,'is_popular'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/showcase-masonry-gallery-v7.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-heading',array(
                    'eyebrow'=>'SHOWCASE','title'=>'Detayların konuştuğu bir görsel vitrin',
                    'description'=>'Yüksek kaliteli görselleri sade ve güçlü bir galeri akışında sergileyin.'
                )),
                self::element('wpsoft-gallery-zoom-pro',array())
            ),'#0b1220',52)
        );

        $sections[]=array(
            'key'=>'sec-showcase-video-story-v7','title'=>'Showcase · Video Story',
            'desc'=>'Marka filmi, mekan, ürün veya proje videolarını hikâye odaklı bir bölümde öne çıkarır.',
            'category'=>'Video','style'=>'Cinematic','sector'=>'Genel','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/showcase-video-story-v7.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-heading',array(
                    'eyebrow'=>'WATCH THE STORY','title'=>'Hikâyenizi hareketli içerikle anlatın',
                    'description'=>'Video içeriğini güçlü tipografi ve sade bir anlatımla merkeze alın.'
                )),
                self::element('wpsoft-video-gallery-pro',array())
            ),'#070b12',56)
        );

        $sections[]=array(
            'key'=>'sec-projects-case-study-split-v7','title'=>'Projeler · Case Study Split',
            'desc'=>'Portföy çalışmalarını proje anlatımı ve görsel vitrin yaklaşımıyla daha premium gösterir.',
            'category'=>'Projeler','style'=>'Case Study','sector'=>'Ajans','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/projects-case-study-split-v7.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-heading',array(
                    'eyebrow'=>'SELECTED WORK','title'=>'Sonuç üreten seçili çalışmalar',
                    'description'=>'Projeleri yalnızca görsel olarak değil, değer ve sonuç odaklı sunun.'
                )),
                self::element('wpsoft-portfolio',array('layout_style'=>'index',)),
                self::element('wpsoft-cta',array(
                    'title'=>'Bir sonraki proje sizin olabilir','description'=>'İhtiyacınızı konuşalım ve doğru çözümü birlikte planlayalım.',
                    'button_text'=>'Projeyi Başlat','button_url'=>array('url'=>'#iletisim')
                ))
            ),'#f8fafc',58)
        );

        $sections[]=array(
            'key'=>'sec-testimonials-featured-quote-v7','title'=>'Yorumlar · Featured Quote',
            'desc'=>'Tek bir güçlü müşteri yorumunu büyük tipografi ve güven logolarıyla öne çıkaran sosyal kanıt bölümü.',
            'category'=>'Yorumlar & Güven','style'=>'Editorial','sector'=>'Kurumsal','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/testimonials-featured-quote-v7.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-quote',array()),
                self::element('wpsoft-logo-cloud',array())
            ),'#fff7ed',50)
        );

        $sections[]=array(
            'key'=>'sec-trust-logo-metrics-v7','title'=>'Güven · Logo + Metrics',
            'desc'=>'Referans logolarını ve önemli başarı sayılarını aynı bölümde birleştiren kompakt güven alanı.',
            'category'=>'Yorumlar & Güven','style'=>'Minimal','sector'=>'Kurumsal','quality'=>'Modern','is_new'=>true,'premium'=>0,
            'preview_image'=>WPST_URL.'assets/images/section-templates/trust-logo-metrics-v7.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-logo-cloud',array()),
                self::element('wpsoft-stats-grid',array())
            ),'#ffffff',42)
        );

        $sections[]=array(
            'key'=>'sec-pricing-comparison-clean-v7','title'=>'Fiyatlandırma · Comparison Clean',
            'desc'=>'Paketleri sade kart yapısı, kısa avantaj listeleri ve SSS desteğiyle karşılaştırılabilir sunar.',
            'category'=>'Fiyatlandırma','style'=>'Clean','sector'=>'SaaS','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/pricing-comparison-clean-v7.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-heading',array(
                    'eyebrow'=>'PRICING','title'=>'İhtiyacınıza uygun planı seçin',
                    'description'=>'Paket farklarını açık ve hızlı karşılaştırılabilir şekilde gösterin.'
                )),
                self::element('wpsoft-pricing',array()),
                self::element('wpsoft-faq',array())
            ),'#f8fafc',58)
        );

        

        

        $sections[]=array(
            'key'=>'sec-cta-fullwidth-statement-v7','title'=>'CTA · Fullwidth Statement',
            'desc'=>'Sayfa kapanışlarında kullanılmak üzere büyük başlık, kısa açıklama ve tek aksiyonlu güçlü CTA.',
            'category'=>'CTA','style'=>'Statement','sector'=>'Genel','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/cta-fullwidth-statement-v7.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-cta',array('layout_style'=>'editorial',
                    'title'=>'Bir sonraki güçlü işi birlikte oluşturalım',
                    'description'=>'İhtiyacınızı anlatın, doğru dijital deneyimi birlikte planlayalım.',
                    'button_text'=>'Projeyi Başlat','button_url'=>array('url'=>'#iletisim'),'bg'=>'#0f172a'
                ))
            ),'#0f172a',44)
        );

        $sections[]=array(
            'key'=>'sec-blog-featured-magazine-v7','title'=>'Blog · Featured Magazine',
            'desc'=>'İçerik siteleri ve kurumsal bloglar için öne çıkan yazı + kart akışı hissi veren modern bölüm.',
            'category'=>'Blog','style'=>'Magazine','sector'=>'Blog','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/blog-featured-magazine-v7.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-heading',array(
                    'eyebrow'=>'LATEST STORIES','title'=>'Öne çıkan içerikler ve güncel yazılar',
                    'description'=>'İçeriklerinizi daha güçlü bir yayın deneyimiyle sunun.'
                )),
                self::element('wpsoft-blog-posts',array('layout_style'=>'compact-news','posts_per_page'=>7,'columns'=>3,'pagination'=>'none'))
            ),'#ffffff',52)
        );

        $sections[]=array(
            'key'=>'sec-timeline-company-v7','title'=>'Süreç · Company Timeline',
            'desc'=>'Şirket geçmişi, proje kilometre taşları veya aşamalı hizmet süreçleri için dikey zaman çizgisi.',
            'category'=>'Süreç','style'=>'Timeline','sector'=>'Kurumsal','quality'=>'Modern','is_new'=>true,'premium'=>0,
            'preview_image'=>WPST_URL.'assets/images/section-templates/timeline-company-v7.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-heading',array(
                    'eyebrow'=>'OUR JOURNEY','title'=>'Dünden bugüne gelişim yolculuğumuz',
                    'description'=>'Önemli kilometre taşlarını kronolojik ve kolay taranabilir bir akışta gösterin.'
                )),
                self::element('wpsoft-icon-steps',array())
            ),'#f8fafc',56)
        );

        $sections[]=array(
            'key'=>'sec-marquee-brand-statement-v7','title'=>'Metin · Brand Marquee',
            'desc'=>'Ajans ve yaratıcı siteler için büyük hareketli marka mesajını sade içerik alanıyla birleştirir.',
            'category'=>'Metin','style'=>'Marquee','sector'=>'Ajans','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/marquee-brand-statement-v7.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-marquee-text',array()),
                self::element('wpsoft-heading',array(
                    'eyebrow'=>'DESIGN / BUILD / GROW','title'=>'Markanızın karakterini sayfanın ritmine taşıyın',
                    'description'=>'Hareketli tipografiyi içerik hiyerarşisiyle dengeli kullanın.'
                ))
            ),'#ffffff',38)
        );

        

        $sections[]=array(
            'key'=>'sec-services-bento-grid-v7','title'=>'Hizmetler · Bento Grid',
            'desc'=>'Hizmetleri eşit kartlar yerine farklı vurgu seviyelerine sahip modern bento akışıyla sunar.',
            'category'=>'Hizmetler','style'=>'Bento','sector'=>'Ajans','quality'=>'Signature','is_new'=>true,'is_popular'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/services-bento-grid-v7.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-heading',array(
                    'eyebrow'=>'CAPABILITIES','title'=>'Birbirini tamamlayan uzmanlık alanları',
                    'description'=>'Ana hizmetleri güçlü, ikincil hizmetleri daha kompakt bir görsel hiyerarşiyle gösterin.'
                )),
                self::element('wpsoft-reveal-cards',array()),
                self::element('wpsoft-icon-grid',array())
            ),'#eef2ff',54)
        );



        /*
         * Template Library 4.0 · Curated Signature Collection
         * WoodMart'ın modüler section yaklaşımı ve Essentials'ın büyük tipografi /
         * whitespace / bento kompozisyon dilinden ilham alan özgün WPSoft tasarımları.
         * Kopya layout değildir; WPSoft widget sistemiyle yeniden yorumlanmıştır.
         */

        $sections[]=array(
            'key'=>'sec-hero-product-stage-v8','title'=>'Hero · Product Stage',
            'desc'=>'Büyük tipografi, geniş ürün/görsel sahnesi, floating metrik ve çift CTA ile yüksek etkili modern hero.',
            'category'=>'Hero','style'=>'Editorial Split','sector'=>'Genel','quality'=>'Signature','is_new'=>true,'is_popular'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-hero-product-stage-v8.svg',
            'data'=>$signature_pair(
                self::element('wpsoft-hero-split-modern',array('composition'=>'offset',
                    'eyebrow'=>'NEW GENERATION','title'=>'Daha az kalabalık, daha güçlü bir ilk izlenim',
                    'text'=>'Büyük başlık, net değer önerisi ve odaklı CTA ile premium açılış deneyimi oluşturun.',
                    'image'=>array('url'=>self::demo_v2('corporate-signature.svg')),
                    'float_icon'=>'chart','float_value'=>'+42%','float_text'=>'Daha güçlü etkileşim',
                    'primary_text'=>'Projeyi Başlat','primary_url'=>array('url'=>'#iletisim'),
                    'secondary_text'=>'Çalışmaları Gör','secondary_url'=>array('url'=>'#projeler'),
                    'layout_style'=>'editorial','media_ratio'=>'portrait'
                )),
                self::element('wpsoft-image-cascade',array(
                    'image_one'=>array('url'=>self::demo_v2('agency-signature.svg')),
                    'image_two'=>array('url'=>self::demo_v2('corporate-signature.svg')),
                    'image_three'=>array('url'=>self::demo_v2('architecture-signature.svg'))
                )),
                '#f7f7f5',54,74
            )
        );

        $sections[]=array(
            'key'=>'sec-about-editorial-cascade-v8','title'=>'Hakkımızda · Editorial Cascade',
            'desc'=>'Editoryal marka hikâyesini katmanlı görseller, büyük tipografi ve güven metrikleriyle sunan asimetrik bölüm.',
            'category'=>'Hakkımızda','style'=>'Editorial','sector'=>'Kurumsal','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-about-editorial-cascade-v8.svg',
            'data'=>$signature_pair(
                self::container(array(
                    $signature_heading('OUR STORY','Markanızı yalnızca anlatmayın, hissettirin.','Essentials benzeri güçlü whitespace ve editoryal ritimle marka hikâyesini daha değerli gösterin.'),
                    self::element('wpsoft-stats-grid',array('style_preset'=>'clean'))
                ),array()),
                self::element('wpsoft-image-cascade',array('layout'=>'editorial','media_height'=>array('size'=>560),'image_gap'=>array('size'=>18),'image_radius'=>array('size'=>22),'image_shadow'=>'soft',
                    'image_one'=>array('url'=>self::demo_v2('architecture-signature.svg')),
                    'image_two'=>array('url'=>self::demo_v2('agency-signature.svg')),
                    'image_three'=>array('url'=>self::demo_v2('corporate-signature.svg'))
                )),
                '#ffffff',46,76
            )
        );

        $sections[]=array(
            'key'=>'sec-features-bento-signature-v8','title'=>'Özellikler · Bento Signature',
            'desc'=>'Bir büyük odak kartı ve destekleyici özellik bloklarıyla WoodMart/Essentials çizgisinde modüler bento anlatımı.',
            'category'=>'Özellikler','style'=>'Bento','sector'=>'SaaS','quality'=>'Signature','is_new'=>true,'is_popular'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-features-bento-signature-v8.svg',
            'data'=>$sec_wrap(array(
                $signature_heading('WHY WPSOFT','Özellikleri listelemek yerine görsel bir hikâyeye dönüştürün.','Ana faydayı büyük, ikincil faydaları daha kompakt kartlarla hiyerarşik biçimde gösterin.'),
                self::element('wpsoft-feature-mosaic',array(
                    'title'=>'Tek sistemde daha güçlü dijital deneyim',
                    'image'=>array('url'=>self::demo_v2('saas-signature.svg'))
                )),
                self::element('wpsoft-icon-grid',array())
            ),'#f3f5f8',62)
        );

        $sections[]=array(
            'key'=>'sec-categories-commerce-cards-v8','title'=>'Kategoriler · Commerce Cards',
            'desc'=>'WoodMart mağaza demolarındaki güçlü kategori keşfini, büyük görsel kartlar ve minimal metinle yeniden yorumlayan kategori alanı.',
            'category'=>'Kategoriler','style'=>'Commerce','sector'=>'E-Ticaret','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-categories-commerce-cards-v8.svg',
            'data'=>$sec_wrap(array(
                $signature_heading('SHOP BY CATEGORY','Koleksiyonları görselle keşfedin','Ürün kategorilerini sade, büyük ve tıklanabilir görsel kartlarla öne çıkarın.'),
                self::element('wpsoft-product-showcase',array(
                    'items'=>array(
                        array('image'=>array('url'=>self::demo_v2('commerce-signature.svg')),'title'=>'Yeni Sezon','meta'=>'24 ürün','price'=>''),
                        array('image'=>array('url'=>self::demo_v2('corporate-signature.svg')),'title'=>'En Çok Tercih Edilenler','meta'=>'18 ürün','price'=>''),
                        array('image'=>array('url'=>self::demo_v2('agency-signature.svg')),'title'=>'Editör Seçimi','meta'=>'12 ürün','price'=>'')
                    ),
                    'action_text'=>'Koleksiyonu Gör'
                ))
            ),'#ffffff',58)
        );

        $sections[]=array(
            'key'=>'sec-gallery-editorial-wall-v8','title'=>'Galeri · Editorial Wall',
            'desc'=>'Farklı görsel oranları ve geniş boşluklarla oluşturulan modern editorial showcase duvarı.',
            'category'=>'Galeri','style'=>'Editorial Gallery','sector'=>'Ajans','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-gallery-editorial-wall-v8.svg',
            'data'=>$sec_wrap(array(
                $signature_heading('SELECTED WORK','İşi anlatan en güçlü şey bazen görselin kendisidir.','Görselleri sıkışık grid yerine daha nefes alan premium bir kompozisyonla sunun.'),
                self::element('wpsoft-gallery-zoom-pro',array(
                    'layout'=>'masonry','columns'=>'3','lightbox'=>'yes','style_preset'=>'modern','hover_effect'=>'zoom'
                ))
            ),'#f8f7f4',64)
        );

        $sections[]=array(
            'key'=>'sec-video-spotlight-v8','title'=>'Video · Spotlight Story',
            'desc'=>'Büyük video posterini güçlü başlık, kısa hikâye ve aksiyonla birleştiren sinematik marka bölümü.',
            'category'=>'Video','style'=>'Cinematic','sector'=>'Genel','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-video-spotlight-v8.svg',
            'data'=>$signature_pair(
                self::container(array(
                    $signature_heading('WATCH THE STORY','Markanızın arkasındaki fikri 60 saniyede anlatın.','Ürün, proje veya marka hikâyesi için geniş video alanı ve kısa açıklama.'),
                    self::element('wpsoft-quote',array())
                ),array()),
                self::element('wpsoft-video-popup-pro',array(
                    'image'=>array('url'=>self::demo_v2('agency-signature.svg')),
                    'url'=>'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                    'title'=>'Marka Hikâyemizi İzleyin','subtitle'=>'60 saniye','open_mode'=>'lightbox','height'=>520,'radius'=>28
                )),
                '#0d1117',44,72
            )
        );

        $sections[]=array(
            'key'=>'sec-pricing-highlight-v8','title'=>'Fiyatlandırma · Highlight Plan',
            'desc'=>'Bir paketi güçlü biçimde öne çıkaran, sade tipografi ve geniş spacing kullanan premium fiyatlandırma alanı.',
            'category'=>'Fiyatlandırma','style'=>'Clean Premium','sector'=>'SaaS','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-pricing-highlight-v8.svg',
            'data'=>$sec_wrap(array(
                $signature_heading('SIMPLE PRICING','Karar vermeyi kolaylaştıran net paketler','Gereksiz detay yerine fayda, fiyat ve aksiyonu öne çıkaran sade yapı.'),
                self::container(array(
                    self::element('wpsoft-pricing',array('badge'=>'Başlangıç','title'=>'Starter','price'=>'₺4.900','period'=>'proje','description'=>'Temel ihtiyaçlar için sade başlangıç.','style_preset'=>'minimal','button_text'=>'Başla','button_url'=>array('url'=>'#iletisim'))),
                    self::element('wpsoft-pricing',array('badge'=>'En Popüler','title'=>'Professional','price'=>'₺9.900','period'=>'proje','description'=>'Büyüyen markalar için dengeli ve güçlü paket.','style_preset'=>'elevated','featured'=>'yes','button_text'=>'Bu Paketi Seç','button_url'=>array('url'=>'#iletisim'))),
                    self::element('wpsoft-pricing',array('badge'=>'Kurumsal','title'=>'Business','price'=>'Teklif','period'=>'özel','description'=>'Özel ihtiyaçlar ve kapsamlı projeler.','style_preset'=>'soft','button_text'=>'Teklif Al','button_url'=>array('url'=>'#iletisim')))
                ),array('flex_direction'=>'row','flex_wrap'=>'wrap','gap'=>array('unit'=>'px','size'=>18,'column'=>18,'row'=>18)))
            ),'#ffffff',66)
        );

        $sections[]=array(
            'key'=>'sec-faq-support-sidebar-v8','title'=>'SSS · Support Sidebar',
            'desc'=>'Solda kısa destek/iletişim mesajı, sağda geniş accordion ile daha kullanılabilir modern SSS kompozisyonu.',
            'category'=>'SSS','style'=>'Split Support','sector'=>'Genel','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-faq-support-sidebar-v8.svg',
            'data'=>$signature_pair(
                self::container(array(
                    $signature_heading('NEED HELP?','Aklınızdaki sorulara hızlı yanıtlar.','Aradığınız cevabı bulamazsanız ekibimizle doğrudan iletişime geçebilirsiniz.'),
                    self::element('wpsoft-contact-cards',array(
                        'columns'=>'1',
                        'items'=>array(
                            array('wpst_icon'=>'phone','label'=>'Destek','value'=>'+90 212 000 00 00','url'=>array('url'=>'tel:+902120000000')),
                            array('wpst_icon'=>'mail','label'=>'E-posta','value'=>'info@firma.com','url'=>array('url'=>'mailto:info@firma.com'))
                        )
                    ))
                ),array()),
                self::element('wpsoft-faq',array('style_preset'=>'clean','first_open'=>'yes')),
                '#f6f7f9',38,62
            )
        );

        $sections[]=array(
            'key'=>'sec-contact-editorial-form-v8','title'=>'İletişim · Editorial Form',
            'desc'=>'Büyük iletişim başlığı, minimal bilgi kartları ve geniş form alanını aynı premium kompozisyonda birleştirir.',
            'category'=>'İletişim','style'=>'Editorial Form','sector'=>'Genel','quality'=>'Signature','is_new'=>true,'is_popular'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-contact-editorial-form-v8.svg',
            'data'=>$signature_pair(
                self::container(array(
                    $signature_heading('LET’S TALK','Bir sonraki projenizi birlikte şekillendirelim.','Kısa bir mesaj bırakın; ihtiyaçlarınızı anlayıp en doğru yol haritasını oluşturalım.'),
                    self::element('wpsoft-contact-cards',array('columns'=>'1','style_preset'=>'soft'))
                ),array()),
                self::element('wpsoft-wpforms',array(
                    'empty_title'=>'İletişim Formunuzu Seçin',
                    'empty_text'=>'WPForms formunuzu seçtiğinizde bu alan gerçek formu gösterecek.',
                    'shell_style'=>'card'
                )),
                '#ffffff',43,72
            )
        );

        $sections[]=array(
            'key'=>'sec-product-story-split-v8','title'=>'Ürün · Story Split',
            'desc'=>'Ürün faydasını büyük görsel, editorial metin ve özellik listesiyle anlatan premium ürün tanıtım bölümü.',
            'category'=>'Ürün Tanıtımı','style'=>'Story Split','sector'=>'E-Ticaret','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-product-story-split-v8.svg',
            'data'=>$signature_pair(
                self::element('wpsoft-image-text',array(
                    'eyebrow'=>'DESIGNED FOR EVERYDAY','title'=>'Detayları daha iyi bir deneyim için tasarlandı',
                    'description'=>'Ürünün yalnızca özelliklerini değil, kullanım değerini ve neden farklı olduğunu anlatın.',
                    'image'=>array('url'=>self::demo_v2('commerce-signature.svg')),
                    'button_text'=>'Ürünü İncele','button_url'=>array('url'=>'#urun'),
                    'layout_style'=>'editorial'
                )),
                self::container(array(
                    self::element('wpsoft-feature-list',array()),
                    self::element('wpsoft-trust-badges',array())
                ),array()),
                '#f4f1eb',52,68
            )
        );

        $sections[]=array(
            'key'=>'sec-testimonials-card-wall-v8','title'=>'Yorumlar · Card Wall',
            'desc'=>'Büyük featured yorum ve destekleyici güven öğeleriyle daha doğal sosyal kanıt kompozisyonu.',
            'category'=>'Yorumlar & Güven','style'=>'Editorial Cards','sector'=>'Genel','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-testimonials-card-wall-v8.svg',
            'data'=>$sec_wrap(array(
                $signature_heading('LOVED BY CLIENTS','Güven, en güçlü satış argümanıdır.','Müşteri deneyimlerini büyük ve okunabilir biçimde öne çıkarın.'),
                self::element('wpsoft-testimonial-slider',array('layout_variant'=>'card','style_preset'=>'light')),
                self::element('wpsoft-logo-marquee',array())
            ),'#f8fafc',64)
        );

        $sections[]=array(
            'key'=>'sec-cta-floating-banner-v8','title'=>'CTA · Floating Banner',
            'desc'=>'Sayfanın sonunda güçlü kontrast, büyük tipografi ve tek odaklı aksiyon sunan modern kapanış alanı.',
            'category'=>'CTA','style'=>'Floating','sector'=>'Genel','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-cta-floating-banner-v8.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-morphing-cta',array(
                    'eyebrow'=>'READY WHEN YOU ARE',
                    'title'=>'Bir sonraki büyük fikri birlikte hayata geçirelim.',
                    'text'=>'İhtiyacınızı paylaşın, size özel yol haritasını birlikte oluşturalım.',
                    'button_text'=>'Projeyi Başlat','button_url'=>array('url'=>'#iletisim')
                ))
            ),'#111827',48)
        );


        
        $sections[]=array(
            'key'=>'sec-widget-flip-box-v1',
            'title'=>'Widget Lab · Flip Box · Interactive Card',
            'desc'=>'Flip Box · Interactive Card widgetını tek başına, bağımsız bir bölüm olarak kullanabileceğiniz modern başlangıç şablonu.',
            'category'=>'Widget Lab','style'=>'Independent','sector'=>'Genel','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-widget-flip-box-v1.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-heading',array('eyebrow'=>'WIDGET LAB','title'=>'Flip Box · Interactive Card','description'=>'Bu bölüm bağımsızdır; sayfanızda istediğiniz konuma ekleyip widget ayarlarını özgürce özelleştirebilirsiniz.')),
                self::element('wpsoft-flip-box')
            ),'#f7f8fb',54)
        );

        $sections[]=array(
            'key'=>'sec-widget-parallax-image-v1',
            'title'=>'Widget Lab · Parallax Image · Visual Story',
            'desc'=>'Parallax Image · Visual Story widgetını tek başına, bağımsız bir bölüm olarak kullanabileceğiniz modern başlangıç şablonu.',
            'category'=>'Widget Lab','style'=>'Independent','sector'=>'Genel','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-widget-parallax-image-v1.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-heading',array('eyebrow'=>'WIDGET LAB','title'=>'Parallax Image · Visual Story','description'=>'Bu bölüm bağımsızdır; sayfanızda istediğiniz konuma ekleyip widget ayarlarını özgürce özelleştirebilirsiniz.')),
                self::element('wpsoft-parallax-image')
            ),'#eef2ff',54)
        );

        $sections[]=array(
            'key'=>'sec-widget-icon-box-v1',
            'title'=>'Widget Lab · Icon Box · Feature Card',
            'desc'=>'Icon Box · Feature Card widgetını tek başına, bağımsız bir bölüm olarak kullanabileceğiniz modern başlangıç şablonu.',
            'category'=>'Widget Lab','style'=>'Independent','sector'=>'Genel','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-widget-icon-box-v1.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-heading',array('eyebrow'=>'WIDGET LAB','title'=>'Icon Box · Feature Card','description'=>'Bu bölüm bağımsızdır; sayfanızda istediğiniz konuma ekleyip widget ayarlarını özgürce özelleştirebilirsiniz.')),
                self::element('wpsoft-icon-box')
            ),'#f8fafc',54)
        );

        $sections[]=array(
            'key'=>'sec-widget-mega-promo-v1',
            'title'=>'Widget Lab · Mega Promo · Campaign Card',
            'desc'=>'Mega Promo · Campaign Card widgetını tek başına, bağımsız bir bölüm olarak kullanabileceğiniz modern başlangıç şablonu.',
            'category'=>'Widget Lab','style'=>'Independent','sector'=>'Genel','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-widget-mega-promo-v1.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-heading',array('eyebrow'=>'WIDGET LAB','title'=>'Mega Promo · Campaign Card','description'=>'Bu bölüm bağımsızdır; sayfanızda istediğiniz konuma ekleyip widget ayarlarını özgürce özelleştirebilirsiniz.')),
                self::element('wpsoft-mega-promo')
            ),'#ffffff',54)
        );

        $sections[]=array(
            'key'=>'sec-widget-svg-shape-v1',
            'title'=>'Widget Lab · SVG Shape · Decorative Layer',
            'desc'=>'SVG Shape · Decorative Layer widgetını tek başına, bağımsız bir bölüm olarak kullanabileceğiniz modern başlangıç şablonu.',
            'category'=>'Widget Lab','style'=>'Independent','sector'=>'Genel','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-widget-svg-shape-v1.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-heading',array('eyebrow'=>'WIDGET LAB','title'=>'SVG Shape · Decorative Layer','description'=>'Bu bölüm bağımsızdır; sayfanızda istediğiniz konuma ekleyip widget ayarlarını özgürce özelleştirebilirsiniz.')),
                self::element('wpsoft-svg-shape')
            ),'#f7f8fb',54)
        );

        $sections[]=array(
            'key'=>'sec-widget-content-slider-v1',
            'title'=>'Widget Lab · Content Slider · Story Slides',
            'desc'=>'Content Slider · Story Slides widgetını tek başına, bağımsız bir bölüm olarak kullanabileceğiniz modern başlangıç şablonu.',
            'category'=>'Widget Lab','style'=>'Independent','sector'=>'Genel','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-widget-content-slider-v1.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-heading',array('eyebrow'=>'WIDGET LAB','title'=>'Content Slider · Story Slides','description'=>'Bu bölüm bağımsızdır; sayfanızda istediğiniz konuma ekleyip widget ayarlarını özgürce özelleştirebilirsiniz.')),
                self::element('wpsoft-content-slider',array('radius'=>array('size'=>28)))
            ),'#eef2ff',54)
        );

        $sections[]=array(
            'key'=>'sec-widget-timeline-modern-v1',
            'title'=>'Widget Lab · Timeline · Company Story',
            'desc'=>'Timeline · Company Story widgetını tek başına, bağımsız bir bölüm olarak kullanabileceğiniz modern başlangıç şablonu.',
            'category'=>'Widget Lab','style'=>'Independent','sector'=>'Genel','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-widget-timeline-modern-v1.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-heading',array('eyebrow'=>'WIDGET LAB','title'=>'Timeline · Company Story','description'=>'Bu bölüm bağımsızdır; sayfanızda istediğiniz konuma ekleyip widget ayarlarını özgürce özelleştirebilirsiniz.')),
                self::element('wpsoft-timeline-modern')
            ),'#f8fafc',54)
        );

        $sections[]=array(
            'key'=>'sec-widget-countdown-modern-v1',
            'title'=>'Widget Lab · Countdown · Campaign Timer',
            'desc'=>'Countdown · Campaign Timer widgetını tek başına, bağımsız bir bölüm olarak kullanabileceğiniz modern başlangıç şablonu.',
            'category'=>'Widget Lab','style'=>'Independent','sector'=>'Genel','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-widget-countdown-modern-v1.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-heading',array('eyebrow'=>'WIDGET LAB','title'=>'Countdown · Campaign Timer','description'=>'Bu bölüm bağımsızdır; sayfanızda istediğiniz konuma ekleyip widget ayarlarını özgürce özelleştirebilirsiniz.')),
                self::element('wpsoft-countdown-modern')
            ),'#ffffff',54)
        );

        $sections[]=array(
            'key'=>'sec-widget-fancy-box-v1',
            'title'=>'Widget Lab · Fancy Box · Highlight Card',
            'desc'=>'Fancy Box · Highlight Card widgetını tek başına, bağımsız bir bölüm olarak kullanabileceğiniz modern başlangıç şablonu.',
            'category'=>'Widget Lab','style'=>'Independent','sector'=>'Genel','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-widget-fancy-box-v1.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-heading',array('eyebrow'=>'WIDGET LAB','title'=>'Fancy Box · Highlight Card','description'=>'Bu bölüm bağımsızdır; sayfanızda istediğiniz konuma ekleyip widget ayarlarını özgürce özelleştirebilirsiniz.')),
                self::element('wpsoft-fancy-box')
            ),'#f7f8fb',54)
        );

        $sections[]=array(
            'key'=>'sec-widget-advanced-accordion-v1',
            'title'=>'Widget Lab · Accordion · Content Stack',
            'desc'=>'Accordion · Content Stack widgetını tek başına, bağımsız bir bölüm olarak kullanabileceğiniz modern başlangıç şablonu.',
            'category'=>'Widget Lab','style'=>'Independent','sector'=>'Genel','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-widget-advanced-accordion-v1.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-heading',array('eyebrow'=>'WIDGET LAB','title'=>'Accordion · Content Stack','description'=>'Bu bölüm bağımsızdır; sayfanızda istediğiniz konuma ekleyip widget ayarlarını özgürce özelleştirebilirsiniz.')),
                self::element('wpsoft-advanced-accordion')
            ),'#eef2ff',54)
        );

        $sections[]=array(
            'key'=>'sec-widget-modal-v1',
            'title'=>'Widget Lab · Modal · Conversion Popup',
            'desc'=>'Modal · Conversion Popup widgetını tek başına, bağımsız bir bölüm olarak kullanabileceğiniz modern başlangıç şablonu.',
            'category'=>'Widget Lab','style'=>'Independent','sector'=>'Genel','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-widget-modal-v1.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-heading',array('eyebrow'=>'WIDGET LAB','title'=>'Modal · Conversion Popup','description'=>'Bu bölüm bağımsızdır; sayfanızda istediğiniz konuma ekleyip widget ayarlarını özgürce özelleştirebilirsiniz.')),
                self::element('wpsoft-modal')
            ),'#f8fafc',54)
        );

        $sections[]=array(
            'key'=>'sec-widget-glass-card-v1',
            'title'=>'Widget Lab · Glass Card · Premium Surface',
            'desc'=>'Glass Card · Premium Surface widgetını tek başına, bağımsız bir bölüm olarak kullanabileceğiniz modern başlangıç şablonu.',
            'category'=>'Widget Lab','style'=>'Independent','sector'=>'Genel','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-widget-glass-card-v1.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-heading',array('eyebrow'=>'WIDGET LAB','title'=>'Glass Card · Premium Surface','description'=>'Bu bölüm bağımsızdır; sayfanızda istediğiniz konuma ekleyip widget ayarlarını özgürce özelleştirebilirsiniz.')),
                self::element('wpsoft-glass-card')
            ),'#ffffff',54)
        );

        $sections[]=array(
            'key'=>'sec-widget-floating-icons-v1',
            'title'=>'Widget Lab · Floating Icons · Feature Cloud',
            'desc'=>'Floating Icons · Feature Cloud widgetını tek başına, bağımsız bir bölüm olarak kullanabileceğiniz modern başlangıç şablonu.',
            'category'=>'Widget Lab','style'=>'Independent','sector'=>'Genel','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-widget-floating-icons-v1.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-heading',array('eyebrow'=>'WIDGET LAB','title'=>'Floating Icons · Feature Cloud','description'=>'Bu bölüm bağımsızdır; sayfanızda istediğiniz konuma ekleyip widget ayarlarını özgürce özelleştirebilirsiniz.')),
                self::element('wpsoft-floating-icons')
            ),'#f7f8fb',54)
        );

        $sections[]=array(
            'key'=>'sec-widget-breadcrumb-v1',
            'title'=>'Widget Lab · Breadcrumb · Inner Hero',
            'desc'=>'Breadcrumb · Inner Hero widgetını tek başına, bağımsız bir bölüm olarak kullanabileceğiniz modern başlangıç şablonu.',
            'category'=>'Widget Lab','style'=>'Independent','sector'=>'Genel','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-widget-breadcrumb-v1.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-heading',array('eyebrow'=>'WIDGET LAB','title'=>'Breadcrumb · Inner Hero','description'=>'Bu bölüm bağımsızdır; sayfanızda istediğiniz konuma ekleyip widget ayarlarını özgürce özelleştirebilirsiniz.')),
                self::element('wpsoft-breadcrumb')
            ),'#eef2ff',54)
        );

        $sections[]=array(
            'key'=>'sec-widget-scroll-progress-v1',
            'title'=>'Widget Lab · Scroll Progress · Reading UX',
            'desc'=>'Scroll Progress · Reading UX widgetını tek başına, bağımsız bir bölüm olarak kullanabileceğiniz modern başlangıç şablonu.',
            'category'=>'Widget Lab','style'=>'Independent','sector'=>'Genel','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-widget-scroll-progress-v1.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-heading',array('eyebrow'=>'WIDGET LAB','title'=>'Scroll Progress · Reading UX','description'=>'Bu bölüm bağımsızdır; sayfanızda istediğiniz konuma ekleyip widget ayarlarını özgürce özelleştirebilirsiniz.')),
                self::element('wpsoft-scroll-progress')
            ),'#f8fafc',54)
        );

        $sections[]=array(
            'key'=>'sec-widget-native-icon-v1',
            'title'=>'Widget Lab · Native Icon · Icon Showcase',
            'desc'=>'Native Icon · Icon Showcase widgetını tek başına, bağımsız bir bölüm olarak kullanabileceğiniz modern başlangıç şablonu.',
            'category'=>'Widget Lab','style'=>'Independent','sector'=>'Genel','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-widget-native-icon-v1.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-heading',array('eyebrow'=>'WIDGET LAB','title'=>'Native Icon · Icon Showcase','description'=>'Bu bölüm bağımsızdır; sayfanızda istediğiniz konuma ekleyip widget ayarlarını özgürce özelleştirebilirsiniz.')),
                self::element('wpsoft-native-icon')
            ),'#ffffff',54)
        );

        
        /*
         * Template Library 4.2 · Signature Variant Collection
         * Widget Design Variants 3.0 kullanılarak gerçek kompozisyon farklarıyla üretildi.
         */
        $sections[]=array(
            'key'=>'sec-hero-statement-v9','title'=>'Hero · Statement Minimal',
            'desc'=>'Görsel kalabalığı kaldırıp büyük tipografi, kısa değer önerisi ve tek odaklı aksiyonla premium minimal hero.',
            'category'=>'Hero','style'=>'Statement','sector'=>'Kurumsal','quality'=>'Signature','is_new'=>true,'is_popular'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-hero-statement-v9.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-hero-split-modern',array(
                    'composition'=>'minimal','layout_style'=>'split',
                    'eyebrow'=>'LESS, BUT BETTER','title'=>'Daha net mesaj. Daha güçlü ilk izlenim.',
                    'text'=>'Büyük tipografi ve kontrollü boşlukla kullanıcının odağını tek bir değer önerisine taşıyın.',
                    'primary_text'=>'Projeyi Başlat','primary_url'=>array('url'=>'#iletisim'),
                    'secondary_text'=>'Yaklaşımımız','secondary_url'=>array('url'=>'#yaklasim')
                ))
            ),'#f8fafc',34)
        );

        $sections[]=array(
            'key'=>'sec-services-editorial-index-v9','title'=>'Hizmetler · Editorial Index',
            'desc'=>'Hizmetleri klasik eşit kartlar yerine numaralı, büyük başlıklı ve editoryal ritimli bir servis indeksi olarak sunar.',
            'category'=>'Hizmetler','style'=>'Editorial Index','sector'=>'Ajans','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-services-editorial-index-v9.svg',
            'data'=>$sec_wrap(array(
                $signature_heading('CAPABILITIES','Uzmanlık alanlarımızı daha net keşfedin','Her hizmeti aynı ağırlıkta göstermek yerine güçlü bir editoryal hiyerarşi oluşturun.'),
                self::element('wpsoft-service-cards-pro',array(
                    'layout_variant'=>'editorial','card_style'=>'minimal','columns'=>'3','action_text'=>'Detay'
                ))
            ),'#ffffff',68)
        );

        $sections[]=array(
            'key'=>'sec-portfolio-cinematic-v9','title'=>'Projeler · Cinematic Cases',
            'desc'=>'Projeleri büyük görsel yüzeyler, koyu overlay ve güçlü proje başlıklarıyla sinematik case-study akışında sunar.',
            'category'=>'Projeler','style'=>'Cinematic','sector'=>'Ajans','quality'=>'Signature','is_new'=>true,'is_popular'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-portfolio-cinematic-v9.svg',
            'data'=>$sec_wrap(array(
                $signature_heading('SELECTED WORK','Detaylarıyla hatırlanan projeler','Seçilmiş işleri büyük görsel alanlarla güçlü bir portfolio deneyimine dönüştürün.'),
                self::element('wpsoft-portfolio',array(
                    'layout_style'=>'cinematic','columns'=>'1','hover_effect'=>'zoom'
                ))
            ),'#0b1020',64)
        );

        $sections[]=array(
            'key'=>'sec-testimonial-stage-v9','title'=>'Yorumlar · Stage Quote',
            'desc'=>'Tek müşteri yorumunu çok büyük tipografi ve sahne kompozisyonuyla öne çıkaran güçlü sosyal kanıt bölümü.',
            'category'=>'Yorumlar & Güven','style'=>'Stage','sector'=>'Genel','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-testimonial-stage-v9.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-testimonial-slider',array(
                    'layout_variant'=>'stage','style_preset'=>'dark'
                )),
                self::element('wpsoft-logo-marquee',array('layout_variant'=>'pill'))
            ),'#111827',58)
        );

        $sections[]=array(
            'key'=>'sec-pricing-statement-v9','title'=>'Fiyatlandırma · Price Statement',
            'desc'=>'Tek paketin fiyatını ve değer önerisini büyük ölçekte sunan, landing page odaklı premium fiyat bölümü.',
            'category'=>'Fiyatlandırma','style'=>'Statement','sector'=>'SaaS','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-pricing-statement-v9.svg',
            'data'=>$sec_wrap(array(
                $signature_heading('ONE CLEAR PLAN','Karar vermeyi kolaylaştıran sade fiyatlandırma','Karmaşık karşılaştırmalar yerine tek güçlü teklif ve net faydalar.'),
                self::element('wpsoft-pricing',array(
                    'layout_variant'=>'statement','style_preset'=>'minimal',
                    'badge'=>'En Çok Tercih Edilen','title'=>'Professional','price'=>'₺9.900','period'=>'proje'
                )),
                self::element('wpsoft-trust-badges',array('layout_variant'=>'strip'))
            ),'#ffffff',70)
        );

        $sections[]=array(
            'key'=>'sec-blog-visual-journal-v9','title'=>'Blog · Visual Journal',
            'desc'=>'Büyük görseller, değişken kart yüksekliği ve nefes alan tipografiyle modern yayın/journal deneyimi.',
            'category'=>'Blog','style'=>'Visual Journal','sector'=>'Blog','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-blog-visual-journal-v9.svg',
            'data'=>$sec_wrap(array(
                $signature_heading('JOURNAL','Daha güçlü bir editoryal yayın deneyimi','Makale ve hikâyeleri sıradan kart gridinden çıkarıp görsel bir yayına dönüştürün.'),
                self::element('wpsoft-blog-posts',array(
                    'layout_style'=>'visual-journal','columns'=>'2','posts_per_page'=>6,'featured_first'=>'yes'
                ))
            ),'#f8f7f4',68)
        );

        $sections[]=array(
            'key'=>'sec-cta-floating-card-v9','title'=>'CTA · Floating Premium Card',
            'desc'=>'Sayfa kapanışında içerikten ayrışan, yüksek kontrastlı ve yükseltilmiş premium CTA kartı.',
            'category'=>'CTA','style'=>'Floating Card','sector'=>'Genel','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-cta-floating-card-v9.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-cta',array(
                    'layout_style'=>'floating','surface_style'=>'dark',
                    'title'=>'Bir sonraki güçlü adımı birlikte atalım',
                    'description'=>'İhtiyacınızı paylaşın, size özel dijital deneyimi birlikte planlayalım.',
                    'button_text'=>'Projeyi Başlat','button_url'=>array('url'=>'#iletisim')
                ))
            ),'#eef2f7',52)
        );


        /*
         * Reference Expansion · Essentials / WoodMart gap pass
         * Bağımsız, kullanım odaklı yeni widget bölümleri.
         */
        $sections[]=array(
            'key'=>'sec-table-comparison-v10','title'=>'Karşılaştırma · Premium Table',
            'desc'=>'Paket, hizmet veya özellik karşılaştırmaları için mobil kart moduna dönüşebilen modern tablo bölümü.',
            'category'=>'Karşılaştırma','style'=>'Comparison','sector'=>'SaaS / Kurumsal','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-table-comparison-v10.svg',
            'data'=>$sec_wrap(array(
                $signature_heading('COMPARE','Doğru seçimi daha kolay yapın','Özellikleri sade, okunabilir ve mobil uyumlu bir tabloda karşılaştırın.'),
                self::element('wpsoft-advanced-table',array('layout'=>'comparison','caption'=>'Plan Karşılaştırması','highlight_column'=>3))
            ),'#ffffff',68)
        );
        $sections[]=array(
            'key'=>'sec-table-editorial-v10','title'=>'Karşılaştırma · Editorial Matrix',
            'desc'=>'Daha sade ve yayın hissinde hizmet kapsamı / teknik özellik matrisi.',
            'category'=>'Karşılaştırma','style'=>'Editorial','sector'=>'Genel','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-table-editorial-v10.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-advanced-table',array('layout'=>'minimal','caption'=>'Hizmet Kapsamı','mobile_mode'=>'stack'))
            ),'#faf9f6',58)
        );

        $sections[]=array(
            'key'=>'sec-map-contact-split-v10','title'=>'İletişim · Location Split',
            'desc'=>'Adres bilgisi ile canlı haritayı dengeli split düzende birleştiren modern iletişim bölümü.',
            'category'=>'İletişim','style'=>'Map Split','sector'=>'Yerel İşletme','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-map-contact-split-v10.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-location-map',array('layout'=>'split','eyebrow'=>'BİZİ ZİYARET EDİN','title'=>'Size yakınız','address'=>'İstanbul, Türkiye','radius'=>array('size'=>24),'height'=>array('size'=>460),'map_filter'=>'soft'))
            ),'#f7f8fb',52)
        );
        $sections[]=array(
            'key'=>'sec-map-overlay-v10','title'=>'İletişim · Map Overlay',
            'desc'=>'Harita üzerinde yükseltilmiş adres kartı kullanan premium konum bölümü.',
            'category'=>'İletişim','style'=>'Map Overlay','sector'=>'Otel / Restoran','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-map-overlay-v10.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-location-map',array('layout'=>'overlay','title'=>'Bizi ziyaret edin','address'=>'Merkez lokasyon · İstanbul','radius'=>array('size'=>28),'height'=>array('size'=>500),'map_filter'=>'soft'))
            ),'#ffffff',36)
        );

        $sections[]=array(
            'key'=>'sec-reviews-featured-v10','title'=>'Yorumlar · Featured Reviews',
            'desc'=>'Bir güçlü müşteri yorumunu öne çıkarıp diğerlerini destekleyici kartlarla sunan sosyal kanıt bölümü.',
            'category'=>'Yorumlar & Güven','style'=>'Featured Reviews','sector'=>'Genel','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-reviews-featured-v10.svg',
            'data'=>$sec_wrap(array(
                $signature_heading('REAL FEEDBACK','Müşterilerimizin deneyimi','Karar vermeyi kolaylaştıran gerçek ve okunabilir sosyal kanıt.'),
                self::element('wpsoft-reviews-pro',array('layout'=>'featured','columns'=>'3','gap'=>array('size'=>18),'card_padding'=>array('top'=>28,'right'=>28,'bottom'=>28,'left'=>28,'unit'=>'px'),'radius'=>array('size'=>22),'quote_mark'=>'yes'))
            ),'#ffffff',68)
        );
        $sections[]=array(
            'key'=>'sec-reviews-wall-v10','title'=>'Yorumlar · Review Wall',
            'desc'=>'Farklı yükseklik hissiyle daha doğal ve dinamik müşteri yorum duvarı.',
            'category'=>'Yorumlar & Güven','style'=>'Review Wall','sector'=>'E-Ticaret / Hizmet','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-reviews-wall-v10.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-reviews-pro',array('layout'=>'wall','columns'=>'3','gap'=>array('size'=>18),'card_padding'=>array('top'=>24,'right'=>24,'bottom'=>24,'left'=>24,'unit'=>'px'),'radius'=>array('size'=>20),'quote_mark'=>'yes'))
            ),'#f8fafc',56)
        );

        $sections[]=array(
            'key'=>'sec-story-editorial-v10','title'=>'Hikâye · Editorial Journey',
            'desc'=>'Süreç, marka hikâyesi veya vaka adımlarını editoryal kartlarla anlatan bağımsız bölüm.',
            'category'=>'Hikâye','style'=>'Editorial','sector'=>'Ajans / Kurumsal','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-story-editorial-v10.svg',
            'data'=>$sec_wrap(array(
                $signature_heading('OUR JOURNEY','Fikirden sonuca uzanan hikâye','Her aşama kendi amacı ve anlatımıyla bağımsız bir deneyim oluşturur.'),
                self::element('wpsoft-story-cards',array('layout'=>'editorial'))
            ),'#f8f7f4',68)
        );
        $sections[]=array(
            'key'=>'sec-story-horizontal-v10','title'=>'Hikâye · Horizontal Stories',
            'desc'=>'Görselli uzun anlatılar ve vaka çalışmaları için yatay story kartları.',
            'category'=>'Hikâye','style'=>'Horizontal','sector'=>'Portfolyo','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-story-horizontal-v10.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-story-cards',array('layout'=>'horizontal','columns'=>'2','gap'=>array('size'=>20),'media_height'=>array('size'=>260),'hover_motion'=>'lift'))
            ),'#ffffff',58)
        );

        $sections[]=array(
            'key'=>'sec-links-resource-grid-v10','title'=>'Bağlantılar · Resource Grid',
            'desc'=>'Hizmet, proje, doküman veya önemli sayfalara modern hızlı erişim kartları.',
            'category'=>'Bağlantılar','style'=>'Resource Grid','sector'=>'Genel','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-links-resource-grid-v10.svg',
            'data'=>$sec_wrap(array(
                $signature_heading('EXPLORE','Aradığınız alana hızlı ulaşın','Önemli içerikleri sade bir navigasyon katmanında bir araya getirin.'),
                self::element('wpsoft-link-grid',array('layout'=>'cards','columns'=>'3'))
            ),'#ffffff',62)
        );
        $sections[]=array(
            'key'=>'sec-links-minimal-index-v10','title'=>'Bağlantılar · Minimal Index',
            'desc'=>'Kurumsal site alt sayfaları ve kaynaklar için sade link indeksi.',
            'category'=>'Bağlantılar','style'=>'Minimal Index','sector'=>'Kurumsal','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-links-minimal-index-v10.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-link-grid',array('layout'=>'minimal','columns'=>'1'))
            ),'#fafafa',52)
        );

        $sections[]=array(
            'key'=>'sec-price-menu-editorial-v10','title'=>'Fiyat / Menü · Editorial List',
            'desc'=>'Restoran menüsü, hizmet tarifesi veya kısa fiyat listelerini premium editoryal düzende sunar.',
            'category'=>'Fiyatlandırma','style'=>'Menu Editorial','sector'=>'Restoran / Hizmet','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-price-menu-editorial-v10.svg',
            'data'=>$sec_wrap(array(
                $signature_heading('MENU','Özenle seçilmiş seçenekler','Kısa açıklamalar ve net fiyatlarla okunabilir premium liste.'),
                self::element('wpsoft-price-list',array('layout'=>'editorial','quality_radius'=>array('size'=>18),'quality_gap'=>array('size'=>12)))
            ),'#fbfaf7',64)
        );
        $sections[]=array(
            'key'=>'sec-price-menu-cards-v10','title'=>'Fiyat / Menü · Service Cards',
            'desc'=>'Hizmet ve menü kalemlerini kart yapısında fiyatlarıyla birlikte gösterir.',
            'category'=>'Fiyatlandırma','style'=>'Price Cards','sector'=>'Hizmet','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-price-menu-cards-v10.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-price-list',array('layout'=>'cards','quality_radius'=>array('size'=>20),'quality_gap'=>array('size'=>14)))
            ),'#f7f8fb',56)
        );


        /*
         * Reference Expansion 2 · Utility / Promotion / Expert / Forms
         */
        $sections[]=array(
            'key'=>'sec-finder-resource-v11','title'=>'Arama · Resource Finder',
            'desc'=>'İçerik, hizmet ve kaynak aramaları için büyük tipografili modern arama bölümü.',
            'category'=>'Arama','style'=>'Hero Finder','sector'=>'Blog / Kurumsal','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-finder-resource-v11.svg',
            'data'=>$sec_wrap(array(
                $signature_heading('FIND IT FAST','Aradığınız içeriğe daha hızlı ulaşın','Kullanıcıyı menüler arasında dolaştırmadan doğrudan arama deneyimi sunun.'),
                self::element('wpsoft-content-finder',array('layout'=>'hero','title'=>'Ne arıyorsunuz?'))
            ),'#f8fafc',64)
        );
        $sections[]=array(
            'key'=>'sec-finder-compact-v11','title'=>'Arama · Compact Finder',
            'desc'=>'Dokümantasyon, yardım merkezi ve blog üst alanı için kompakt arama.',
            'category'=>'Arama','style'=>'Compact','sector'=>'SaaS / Yardım','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-finder-compact-v11.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-content-finder',array('layout'=>'compact','placeholder'=>'Yardım makalesi ara…'))
            ),'#ffffff',42)
        );

        $sections[]=array(
            'key'=>'sec-expert-editorial-v11','title'=>'Uzman · Editorial Profile',
            'desc'=>'Uzman, doktor, avukat veya danışman profilini büyük tipografi ve biyografiyle öne çıkarır.',
            'category'=>'Ekip','style'=>'Editorial Expert','sector'=>'Profesyonel Hizmet','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-expert-editorial-v11.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-expert-profile',array('layout'=>'editorial'))
            ),'#fbfaf7',62)
        );
        $sections[]=array(
            'key'=>'sec-expert-card-v11','title'=>'Uzman · Profile Card',
            'desc'=>'Kompakt uzman veya ekip üyesi sunumu için modern profil kartı.',
            'category'=>'Ekip','style'=>'Profile Card','sector'=>'Klinik / Hukuk','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-expert-card-v11.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-expert-profile',array('layout'=>'card'))
            ),'#ffffff',54)
        );

        $sections[]=array(
            'key'=>'sec-promo-overlay-v11','title'=>'Promo · Visual Overlay',
            'desc'=>'Kampanya veya yeni ürün lansmanı için görsel üstü güçlü promosyon bannerı.',
            'category'=>'CTA','style'=>'Promo Overlay','sector'=>'E-Ticaret / Kampanya','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-promo-overlay-v11.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-promo-banner',array('layout'=>'overlay','title'=>'Yeni koleksiyon şimdi yayında'))
            ),'#ffffff',42)
        );
        $sections[]=array(
            'key'=>'sec-promo-minimal-v11','title'=>'Promo · Minimal Launch',
            'desc'=>'Daha sakin landing page ve SaaS kampanyaları için minimal promo alanı.',
            'category'=>'CTA','style'=>'Promo Minimal','sector'=>'SaaS / Kurumsal','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-promo-minimal-v11.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-promo-banner',array('layout'=>'minimal','eyebrow'=>'NEW RELEASE','title'=>'Daha sade. Daha hızlı. Daha güçlü.'))
            ),'#f8fafc',50)
        );

        $sections[]=array(
            'key'=>'sec-form-split-v11','title'=>'Form · Split Contact',
            'desc'=>'Metin ve formu dengeli iki sütunda sunan modern form shell bölümü.',
            'category'=>'İletişim','style'=>'Form Split','sector'=>'Genel','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-form-split-v11.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-form-shell',array('layout'=>'split','form_position'=>'right','gap'=>array('size'=>32),'radius'=>array('size'=>26)))
            ),'#f8fafc',60)
        );
        $sections[]=array(
            'key'=>'sec-form-dark-v11','title'=>'Form · Dark Conversion',
            'desc'=>'Koyu yüzey üzerinde güçlü başlık ve form ile dönüşüm odaklı iletişim bölümü.',
            'category'=>'İletişim','style'=>'Dark Form','sector'=>'Ajans / SaaS','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-form-dark-v11.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-form-shell',array('layout'=>'dark','title'=>'Birlikte çalışmaya hazır mısınız?','form_position'=>'right','gap'=>array('size'=>32),'radius'=>array('size'=>26)))
            ),'#0b1020',58)
        );


        /*
         * Template Library Depth Pass · v3.2.77
         * Existing widgets and Design Variants are recombined into deeper category collections.
         */

        /* FEATURES / ÖZELLİKLER */
        $sections[]=array(
            'key'=>'sec-features-balanced-v12','title'=>'Özellikler · Balanced Mosaic',
            'desc'=>'Ürün veya hizmet avantajlarını dengeli iki sütunlu mosaic yapıda sunar.',
            'category'=>'Özellikler','style'=>'Balanced Mosaic','sector'=>'SaaS / Kurumsal','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-features-balanced-v12.svg',
            'data'=>$sec_wrap(array(
                $signature_heading('WHY IT WORKS','Öne çıkan özellikler','En güçlü faydaları eşit görsel ağırlıkla anlatın.'),
                self::element('wpsoft-feature-mosaic',array('layout_variant'=>'balanced'))
            ),'#ffffff',68)
        );
        $sections[]=array(
            'key'=>'sec-features-editorial-v12','title'=>'Özellikler · Editorial Feature',
            'desc'=>'Büyük başlık ve daha güçlü içerik oranıyla editoryal feature bölümü.',
            'category'=>'Özellikler','style'=>'Editorial','sector'=>'Ajans','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-features-editorial-v12.svg',
            'data'=>$sec_wrap(array(
                $signature_heading('FEATURE STORY','Özellikleri bir ürün hikâyesi gibi anlatın','Büyük medya alanı ve destekleyici kartlarla ana faydayı önce, detayları sonra gösterin.'),
                self::element('wpsoft-feature-mosaic',array(
                    'layout_variant'=>'editorial',
                    'title'=>'Tek deneyimde daha güçlü özellikler',
                    'image'=>array('url'=>self::demo_v2('saas-signature.svg')),
                    'cards_columns'=>'2',
                    'mosaic_gap'=>array('size'=>20),
                    'media_height'=>array('size'=>440),
                    'radius'=>array('size'=>28)
                ))
            ),'#fbfaf7',68)
        );
        $sections[]=array(
            'key'=>'sec-features-icon-grid-v12','title'=>'Özellikler · Icon Grid',
            'desc'=>'Kısa faydalar ve yetenekler için yoğun ama okunabilir icon grid.',
            'category'=>'Özellikler','style'=>'Icon Grid','sector'=>'Genel','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-features-icon-grid-v12.svg',
            'data'=>$sec_wrap(array(
                $signature_heading('CAPABILITIES','Daha fazlasını tek bakışta gösterin','Kısa açıklamalarla geniş özellik setlerini düzenli sunun.'),
                self::element('wpsoft-icon-grid')
            ),'#f8fafc',62)
        );
        $sections[]=array(
            'key'=>'sec-features-list-v12','title'=>'Özellikler · Clean Feature List',
            'desc'=>'Karmaşık özellikleri sade liste düzeninde anlatan kurumsal bölüm.',
            'category'=>'Özellikler','style'=>'Feature List','sector'=>'Kurumsal','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-features-list-v12.svg',
            'data'=>$sec_wrap(array(
                $signature_heading('CORE FEATURES','İhtiyacınız olan özellikler, sade bir akışta','Uzun özellik listelerini daha okunabilir başlık ve açıklama hiyerarşisiyle sunun.'),
                self::element('wpsoft-feature-list')
            ),'#ffffff',62)
        );
        $sections[]=array(
            'key'=>'sec-features-numbered-v12','title'=>'Özellikler · Numbered Benefits',
            'desc'=>'Faydaları büyük numaralarla önceliklendirerek anlatır.',
            'category'=>'Özellikler','style'=>'Numbered','sector'=>'Danışmanlık','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-features-numbered-v12.svg',
            'data'=>$sec_wrap(array(
                $signature_heading('BENEFITS','Neden bizi seçiyorlar','Karar vermeyi kolaylaştıran ana faydaları sırayla öne çıkarın.'),
                self::element('wpsoft-number-cards')
            ),'#f7f8fb',62)
        );
        $sections[]=array(
            'key'=>'sec-features-tabs-v12','title'=>'Özellikler · Segmented Tabs',
            'desc'=>'Farklı özellik kümelerini sekmeli ve modern segmented navigasyonla gösterir.',
            'category'=>'Özellikler','style'=>'Tabs','sector'=>'SaaS','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-features-tabs-v12.svg',
            'data'=>$sec_wrap(array(
                $signature_heading('EXPLORE','Detayları kategorilere ayırın','Yoğun özellik setlerini kullanıcıyı yormadan sekmeler halinde keşfedilebilir sunun.'),
                self::element('wpsoft-tabs-modern',array('layout_variant'=>'segmented','style_preset'=>'pill'))
            ),'#ffffff',62)
        );


        /* Quality Tour 4 · Curated Metrics + Process */
        $sections[]=array(
            'key'=>'sec-stats-editorial-impact-v20',
            'title'=>'İstatistik · Editorial Impact',
            'desc'=>'Büyük metrikleri güçlü başlık hiyerarşisi ve editoryal sayaç düzeniyle sunar.',
            'category'=>'İstatistik',
            'style'=>'Editorial Metrics',
            'sector'=>'Kurumsal / Ajans',
            'quality'=>'Signature',
            'is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-stats-editorial-impact-v20.svg',
            'data'=>$sec_wrap(array(
                $signature_heading('IMPACT','Rakamlarla görünür sonuçlar','Başarı metriklerini yalnız sayı olarak değil, marka güvenini destekleyen bir anlatı olarak gösterin.'),
                self::element('wpsoft-stats-grid',array(
                    'layout_variant'=>'editorial',
                    'style_preset'=>'clean',
                    'columns'=>'4',
                    'items'=>array(
                        array('wpst_icon'=>'briefcase','number'=>'250+','label'=>'Tamamlanan Proje'),
                        array('wpst_icon'=>'heart','number'=>'98%','label'=>'Müşteri Memnuniyeti'),
                        array('wpst_icon'=>'award','number'=>'10+','label'=>'Yıl Deneyim'),
                        array('wpst_icon'=>'zap','number'=>'40%','label'=>'Daha Hızlı Teslim')
                    )
                ))
            ),'#ffffff',66)
        );

        $sections[]=array(
            'key'=>'sec-stats-compact-strip-v20',
            'title'=>'İstatistik · Compact Strip',
            'desc'=>'Dar alanlarda sonuç, güven ve performans metriklerini kompakt yatay şerit olarak gösterir.',
            'category'=>'İstatistik',
            'style'=>'Compact Strip',
            'sector'=>'SaaS / Kurumsal',
            'quality'=>'Signature',
            'is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-stats-compact-strip-v20.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-stats-grid',array(
                    'layout_variant'=>'strip',
                    'style_preset'=>'soft',
                    'columns'=>'4',
                    'radius'=>array('size'=>16),
                    'items'=>array(
                        array('wpst_icon'=>'users','number'=>'120+','label'=>'Aktif Marka'),
                        array('wpst_icon'=>'chart','number'=>'3.2x','label'=>'Ortalama Dönüşüm'),
                        array('wpst_icon'=>'clock','number'=>'24/7','label'=>'Kesintisiz Deneyim'),
                        array('wpst_icon'=>'shield','number'=>'99.9%','label'=>'Süreklilik')
                    )
                ))
            ),'#f8fafc',38)
        );

        $sections[]=array(
            'key'=>'sec-process-compact-delivery-v20',
            'title'=>'Süreç · Compact Delivery',
            'desc'=>'Dört temel aşamayı daha kompakt ve hızlı taranabilir süreç kartlarıyla gösterir.',
            'category'=>'Süreç',
            'style'=>'Compact Process',
            'sector'=>'Kurumsal / Hizmet',
            'quality'=>'Signature',
            'is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-process-compact-delivery-v20.svg',
            'data'=>$sec_wrap(array(
                $signature_heading('PROCESS','Dört adımda net teslim süreci','Karmaşık proje akışını kullanıcıya kolay anlaşılır dört temel aşamayla anlatın.'),
                self::element('wpsoft-process-steps-pro',array(
                    'layout_variant'=>'compact',
                    'show_connector'=>'yes',
                    'items'=>array(
                        array('step'=>'01','title'=>'Analiz','text'=>'Hedef ve kapsam belirlenir.'),
                        array('step'=>'02','title'=>'Plan','text'=>'İçerik ve tasarım sistemi kurulur.'),
                        array('step'=>'03','title'=>'Üretim','text'=>'Uygulama ve kontroller tamamlanır.'),
                        array('step'=>'04','title'=>'Teslim','text'=>'Yayın ve optimizasyon yapılır.')
                    )
                ))
            ),'#f8fafc',58)
        );

        /* SERVICES */
        $sections[]=array(
            'key'=>'sec-services-horizontal-v12','title'=>'Hizmetler · Horizontal Services',
            'desc'=>'Hizmetleri satır bazlı, daha kurumsal ve karşılaştırılabilir bir düzende sunar.',
            'category'=>'Hizmetler','style'=>'Horizontal','sector'=>'Kurumsal','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-services-horizontal-v12.svg',
            'data'=>$sec_wrap(array(
                $signature_heading('SERVICES','İhtiyacınıza göre şekillenen hizmetler','Her hizmeti daha fazla açıklama alanıyla sunun.'),
                self::element('wpsoft-service-cards-pro',array('layout_variant'=>'horizontal','card_style'=>'minimal','columns'=>'1','gap'=>array('size'=>16),'card_radius'=>array('size'=>18),'card_padding'=>array('top'=>24,'right'=>24,'bottom'=>24,'left'=>24,'unit'=>'px'),'hover_effect'=>'lift'))
            ),'#ffffff',66)
        );
        $sections[]=array(
            'key'=>'sec-services-compact-v12','title'=>'Hizmetler · Compact Services',
            'desc'=>'Çok sayıda hizmeti kısa ve kompakt kartlarla sunmak için.',
            'category'=>'Hizmetler','style'=>'Compact','sector'=>'Ajans / Hizmet','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-services-compact-v12.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-service-cards-pro',array('layout_variant'=>'compact','card_style'=>'soft','columns'=>'3','gap'=>array('size'=>14),'card_radius'=>array('size'=>18),'hover_effect'=>'border'))
            ),'#f8fafc',54)
        );
        $sections[]=array(
            'key'=>'sec-services-icon-top-v12','title'=>'Hizmetler · Icon Top',
            'desc'=>'İkon merkezli, simetrik ve modern servis kartları.',
            'category'=>'Hizmetler','style'=>'Icon Top','sector'=>'Teknoloji','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-services-icon-top-v12.svg',
            'data'=>$sec_wrap(array(
                $signature_heading('WHAT WE DO','Çözümlerimizi keşfedin','İkon odaklı kartlarla hizmetlerinizi daha hızlı anlaşılır hale getirin.'),
                self::element('wpsoft-service-cards-pro',array('layout_variant'=>'icon-top','card_radius'=>array('size'=>22),'gap'=>array('size'=>18),'hover_effect'=>'lift','card_style'=>'elevated'))
            ),'#ffffff',60)
        );

        /* PROJECTS / PORTFOLIO */
        $sections[]=array(
            'key'=>'sec-projects-index-v12','title'=>'Projeler · Project Index',
            'desc'=>'Projeleri görsel kart yerine tipografik ve profesyonel proje indeksi olarak sunar.',
            'category'=>'Projeler','style'=>'Project Index','sector'=>'Mimarlık / Ajans','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-projects-index-v12.svg',
            'data'=>$sec_wrap(array(
                $signature_heading('SELECTED WORK','Seçilmiş projeler','Daha editoryal ve sade bir proje listesi.'),
                self::element('wpsoft-portfolio',array('layout_style'=>'index','columns'=>'1','radius'=>array('size'=>16),'hover_effect'=>'overlay'))
            ),'#ffffff',64)
        );
        $sections[]=array(
            'key'=>'sec-projects-tiles-v12','title'=>'Projeler · Asymmetric Tiles',
            'desc'=>'Farklı ölçülerde proje kartlarıyla daha dinamik portfolio kompozisyonu.',
            'category'=>'Projeler','style'=>'Asymmetric Tiles','sector'=>'Creative','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-projects-tiles-v12.svg',
            'data'=>$sec_wrap(array(
                $signature_heading('PROJECTS','Seçilmiş çalışmalar','Asimetrik görsel ritimle öne çıkan projelerden bir seçki.'),
                self::element('wpsoft-portfolio',array('layout_style'=>'tiles','columns'=>'3','radius'=>array('size'=>22),'hover_effect'=>'zoom'))
            ),'#f8fafc',58)
        );
        $sections[]=array(
            'key'=>'sec-projects-cinematic-v12','title'=>'Projeler · Dark Cinematic',
            'desc'=>'Büyük görsel alanlar ve koyu yüzeyle premium case-study hissi.',
            'category'=>'Projeler','style'=>'Cinematic Dark','sector'=>'Ajans','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-projects-cinematic-v12.svg',
            'data'=>$sec_wrap(array(
                $signature_heading('CASE STUDIES','Etkisi yüksek projeler','Koyu sinematik yüzeyde güçlü görseller ve seçilmiş proje hikâyeleri.'),
                self::element('wpsoft-portfolio',array('wpst_heading_color'=>'#f8fafc','wpst_body_color'=>'#cbd5e1','wpst_link_color'=>'#93c5fd','layout_style'=>'cinematic','columns'=>'2','radius'=>array('size'=>24),'hover_effect'=>'overlay'))
            ),'#0b1020',52)
        );
        $sections[]=array(
            'key'=>'sec-projects-gallery-v12','title'=>'Projeler · Portfolio Gallery',
            'desc'=>'Portfolyo ve galeri widgetlarını aynı bölümde birleştiren görsel showcase.',
            'category'=>'Projeler','style'=>'Gallery Showcase','sector'=>'Fotoğraf / Tasarım','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-projects-gallery-v12.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-gallery-zoom-pro',array('layout'=>'collage','hover_effect'=>'lift','lightbox'=>'yes','lightbox_style'=>'glass','gap'=>array('size'=>14),'radius'=>array('size'=>20)))
            ),'#ffffff',54)
        );

        /* BLOG */
        $sections[]=array(
            'key'=>'sec-blog-editorial-feed-v12','title'=>'Blog · Editorial Feed',
            'desc'=>'Yazıları büyük başlık ve yatay görsel akışıyla yayın formatında listeler.',
            'category'=>'Blog','style'=>'Editorial Feed','sector'=>'Medya','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-blog-editorial-feed-v12.svg',
            'data'=>$sec_wrap(array(
                $signature_heading('INSIGHTS','Son içerikler','Düşünce liderliği ve editoryal yayınlar için güçlü liste görünümü.'),
                self::element('wpsoft-blog-posts',array('layout_style'=>'editorial-feed','card_preset'=>'editorial','posts_per_page'=>4,'columns'=>'2','radius'=>array('size'=>20),'hover_effect'=>'border'))
            ),'#ffffff',64)
        );
        $sections[]=array(
            'key'=>'sec-blog-compact-news-v12','title'=>'Blog · Compact News',
            'desc'=>'Haber, duyuru veya sık güncellenen içerikler için kompakt liste.',
            'category'=>'Blog','style'=>'Compact News','sector'=>'Kurumsal / Haber','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-blog-compact-news-v12.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-blog-posts',array('layout_style'=>'compact-news','card_preset'=>'borderless','posts_per_page'=>6,'columns'=>'1','radius'=>array('size'=>14),'hover_effect'=>'border'))
            ),'#f8fafc',52)
        );
        $sections[]=array(
            'key'=>'sec-blog-journal-v12','title'=>'Blog · Visual Journal',
            'desc'=>'Büyük görseller ve değişken kart ritmiyle modern journal bölümü.',
            'category'=>'Blog','style'=>'Visual Journal','sector'=>'Lifestyle / Medya','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-blog-journal-v12.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-blog-posts',array('layout_style'=>'visual-journal','card_preset'=>'editorial','posts_per_page'=>6,'columns'=>'3','radius'=>array('size'=>22),'hover_effect'=>'zoom'))
            ),'#fbfaf7',60)
        );
        $sections[]=array(
            'key'=>'sec-blog-search-v12','title'=>'Blog · Search + Articles',
            'desc'=>'Arama alanı ve makale listesini tek yayın bölümünde birleştirir.',
            'category'=>'Blog','style'=>'Search + Feed','sector'=>'Bilgi Merkezi','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-blog-search-v12.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-content-finder',array('layout'=>'compact','placeholder'=>'Makalelerde ara…')),
                self::element('wpsoft-blog-posts',array('layout_style'=>'minimal','card_preset'=>'borderless','posts_per_page'=>5,'columns'=>'1','radius'=>array('size'=>14),'hover_effect'=>'border'))
            ),'#ffffff',58)
        );

        /* FAQ */
        $sections[]=array(
            'key'=>'sec-faq-numbered-v12','title'=>'SSS · Numbered Accordion',
            'desc'=>'Sık sorulan soruları numaralı ve daha editoryal accordion yapısıyla sunar.',
            'category'=>'SSS','style'=>'Numbered','sector'=>'Genel','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-faq-numbered-v12.svg',
            'data'=>$sec_wrap(array(
                $signature_heading('FAQ','Merak edilenler','Karar öncesi en sık sorulan sorular.'),
                self::element('wpsoft-advanced-accordion',array('layout_variant'=>'numbered','style_preset'=>'clean','radius'=>array('size'=>16)))
            ),'#ffffff',62)
        );
        $sections[]=array(
            'key'=>'sec-faq-panel-v12','title'=>'SSS · Soft Panel',
            'desc'=>'Yumuşak panel yüzeyinde modern soru-cevap bölümü.',
            'category'=>'SSS','style'=>'Panel','sector'=>'SaaS','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-faq-panel-v12.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-advanced-accordion',array('layout_variant'=>'panel','style_preset'=>'soft','radius'=>array('size'=>18)))
            ),'#f8fafc',54)
        );
        $sections[]=array(
            'key'=>'sec-faq-minimal-v12','title'=>'SSS · Minimal Divided',
            'desc'=>'Daha sakin sayfalar için çizgili minimal soru-cevap listesi.',
            'category'=>'SSS','style'=>'Minimal','sector'=>'Kurumsal','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-faq-minimal-v12.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-advanced-accordion',array('layout_variant'=>'divided','style_preset'=>'clean','radius'=>array('size'=>0)))
            ),'#ffffff',50)
        );
        $sections[]=array(
            'key'=>'sec-faq-contact-v12','title'=>'SSS · FAQ + Contact',
            'desc'=>'Soru-cevap ile iletişim CTA alanını tek bölümde birleştirir.',
            'category'=>'SSS','style'=>'FAQ Contact','sector'=>'Hizmet','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-faq-contact-v12.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-advanced-accordion',array('layout_variant'=>'stack','style_preset'=>'soft','radius'=>array('size'=>18))),
                self::element('wpsoft-contact-cards')
            ),'#f7f8fb',60)
        );
        $sections[]=array(
            'key'=>'sec-faq-tabs-v12','title'=>'SSS · Topic Tabs',
            'desc'=>'Farklı soru kategorilerini tabs ve accordion kombinasyonuyla sunar.',
            'category'=>'SSS','style'=>'Topic Tabs','sector'=>'Destek','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-faq-tabs-v12.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-tabs-modern',array('layout_variant'=>'sidebar')),
                self::element('wpsoft-advanced-accordion',array('layout_variant'=>'minimal','style_preset'=>'clean','radius'=>array('size'=>12)))
            ),'#ffffff',58)
        );

        /* TEAM */
        $sections[]=array(
            'key'=>'sec-team-editorial-v12','title'=>'Ekip · Editorial Team',
            'desc'=>'Daha büyük fotoğraf ve biyografi oranıyla editoryal ekip sunumu.',
            'category'=>'Ekip','style'=>'Editorial Team','sector'=>'Ajans / Stüdyo','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-team-editorial-v12.svg',
            'data'=>$sec_wrap(array(
                $signature_heading('TEAM','İşi yapan insanlarla tanışın','Uzmanlık ve kişiliği birlikte gösteren ekip sunumu.'),
                self::element('wpsoft-team-carousel-pro',array('layout_variant'=>'editorial'))
            ),'#fbfaf7',64)
        );
        $sections[]=array(
            'key'=>'sec-team-compact-v12','title'=>'Ekip · Compact Profiles',
            'desc'=>'Çok kişili ekipler için küçük avatar ve kısa profil listesi.',
            'category'=>'Ekip','style'=>'Compact','sector'=>'Kurumsal','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-team-compact-v12.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-team-carousel-pro',array('layout_variant'=>'compact','image_height'=>array('size'=>320),'card_radius'=>array('size'=>20),'hover_motion'=>'lift'))
            ),'#ffffff',52)
        );
        $sections[]=array(
            'key'=>'sec-team-spotlight-v12','title'=>'Ekip · Leadership Spotlight',
            'desc'=>'İlk ekip üyesini lider profili olarak öne çıkaran güçlü düzen.',
            'category'=>'Ekip','style'=>'Spotlight','sector'=>'Kurumsal','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-team-spotlight-v12.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-team-carousel-pro',array('layout_variant'=>'spotlight'))
            ),'#f8fafc',56)
        );
        $sections[]=array(
            'key'=>'sec-team-expert-v12','title'=>'Ekip · Expert + Team',
            'desc'=>'Öne çıkan uzman profili ile ekip listesini tek bölümde birleştirir.',
            'category'=>'Ekip','style'=>'Expert Team','sector'=>'Klinik / Danışmanlık','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-team-expert-v12.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-expert-profile',array('layout'=>'compact')),
                self::element('wpsoft-team-carousel-pro',array('layout_variant'=>'strip','image_height'=>array('size'=>300),'card_radius'=>array('size'=>20),'hover_motion'=>'image'))
            ),'#ffffff',62)
        );

        /* GALLERY */
        $sections[]=array(
            'key'=>'sec-gallery-editorial-v12','title'=>'Galeri · Editorial Grid',
            'desc'=>'İlk görseli öne çıkaran güçlü editoryal galeri.',
            'category'=>'Galeri','style'=>'Editorial Grid','sector'=>'Otel / Mimarlık','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-gallery-editorial-v12.svg',
            'data'=>$sec_wrap(array(
                $signature_heading('GALLERY','Editoryal seçki','Öne çıkan görselle başlayan dengeli ve temiz bir galeri kompozisyonu.'),
                self::element('wpsoft-gallery-zoom-pro',array('layout'=>'editorial','columns'=>'3','aspect_ratio'=>'4-3','hover_effect'=>'soft','lightbox'=>'yes','lightbox_style'=>'clean','gap'=>array('size'=>16),'radius'=>array('size'=>18)))
            ),'#ffffff',54)
        );
        $sections[]=array(
            'key'=>'sec-gallery-filmstrip-v12','title'=>'Galeri · Filmstrip',
            'desc'=>'Yatay kaydırmalı, mobilde doğal swipe hissi veren galeri.',
            'category'=>'Galeri','style'=>'Filmstrip','sector'=>'Portfolyo','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-gallery-filmstrip-v12.svg',
            'data'=>$sec_wrap(array(
                $signature_heading('VISUAL STORY','Yatay görsel hikâye','Swipe odaklı filmstrip akışı; portföy ve etkinlik görselleri için.'),
                self::element('wpsoft-gallery-zoom-pro',array('wpst_heading_color'=>'#f8fafc','wpst_body_color'=>'#cbd5e1','layout'=>'filmstrip','aspect_ratio'=>'3-2','hover_effect'=>'zoom','lightbox'=>'yes','lightbox_style'=>'dark','gap'=>array('size'=>14),'radius'=>array('size'=>18)))
            ),'#0b1020',48)
        );
        $sections[]=array(
            'key'=>'sec-gallery-collage-v12','title'=>'Galeri · Modern Collage',
            'desc'=>'Asimetrik hücrelerle daha dinamik görsel kolaj.',
            'category'=>'Galeri','style'=>'Collage','sector'=>'Creative','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-gallery-collage-v12.svg',
            'data'=>$sec_wrap(array(
                $signature_heading('COLLECTION','Modern kolaj','Farklı oranlardaki görselleri daha dinamik bir kompozisyonda birleştirin.'),
                self::element('wpsoft-gallery-zoom-pro',array('layout'=>'collage','hover_effect'=>'lift','lightbox'=>'yes','lightbox_style'=>'glass','gap'=>array('size'=>14),'radius'=>array('size'=>20)))
            ),'#f8fafc',52)
        );
        $sections[]=array(
            'key'=>'sec-gallery-video-v12','title'=>'Galeri · Photo + Video',
            'desc'=>'Fotoğraf ve video içeriklerini aynı medya showcase bölümünde birleştirir.',
            'category'=>'Galeri','style'=>'Mixed Media','sector'=>'Otel / Etkinlik','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-gallery-video-v12.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-gallery-zoom-pro',array('layout'=>'featured','aspect_ratio'=>'16-9','hover_effect'=>'soft','lightbox'=>'yes','lightbox_style'=>'dark','gap'=>array('size'=>16),'radius'=>array('size'=>20))),
                self::element('wpsoft-video-popup-pro')
            ),'#ffffff',58)
        );


        /*
         * Template Library Depth Pass 2 · v3.2.78
         * Remaining core categories are normalized to 8+ curated variants.
         */

        /* TEAM */
        $sections[]=array(
            'key'=>'sec-team-strip-v13','title'=>'Ekip · Horizontal Strip',
            'desc'=>'Yatay kaydırmalı ekip profilleriyle özellikle mobilde doğal swipe deneyimi.',
            'category'=>'Ekip','style'=>'Horizontal Strip','sector'=>'Ajans / Startup','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-team-strip-v13.svg',
            'data'=>$sec_wrap(array(
                $signature_heading('PEOPLE','Ekibimiz','Farklı uzmanlıkları tek akışta keşfedin.'),
                self::element('wpsoft-team-carousel-pro',array('layout_variant'=>'strip','image_height'=>array('size'=>300),'card_radius'=>array('size'=>20),'hover_motion'=>'image'))
            ),'#ffffff',58)
        );
        $sections[]=array(
            'key'=>'sec-team-leadership-v13','title'=>'Ekip · Leadership + Experts',
            'desc'=>'Lider profili ile uzman ekip kartlarını bir araya getiren kurumsal bölüm.',
            'category'=>'Ekip','style'=>'Leadership','sector'=>'Kurumsal / Danışmanlık','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-team-leadership-v13.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-expert-profile',array('layout'=>'split','image_ratio'=>'portrait')),
                self::element('wpsoft-team-carousel-pro',array('layout_variant'=>'compact','image_height'=>array('size'=>320),'card_radius'=>array('size'=>20),'hover_motion'=>'lift'))
            ),'#f8fafc',64)
        );

        /* GALLERY */
        $sections[]=array(
            'key'=>'sec-gallery-featured-v13','title'=>'Galeri · Featured Story',
            'desc'=>'Bir ana görseli öne çıkarıp destekleyici karelerle güçlü görsel hikâye oluşturur.',
            'category'=>'Galeri','style'=>'Featured Story','sector'=>'Otel / Mimarlık','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-gallery-featured-v13.svg',
            'data'=>$sec_wrap(array(
                $signature_heading('GALLERY','Mekânı ve detayları keşfedin','Ana görseli güçlü bir hikâye başlangıcı olarak kullanın.'),
                self::element('wpsoft-gallery-zoom-pro',array('layout'=>'featured'))
            ),'#ffffff',58)
        );
        $sections[]=array(
            'key'=>'sec-gallery-carousel-v13','title'=>'Galeri · Clean Carousel',
            'desc'=>'Temiz ve kontrollü yatay galeri; ürün, mekân ve proje sunumları için.',
            'category'=>'Galeri','style'=>'Carousel','sector'=>'Genel','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-gallery-carousel-v13.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-gallery-zoom-pro',array('layout'=>'carousel'))
            ),'#f8fafc',50)
        );

        /* CTA */
        $sections[]=array(
            'key'=>'sec-cta-inline-v13','title'=>'CTA · Inline Minimal',
            'desc'=>'İçerik akışını bozmadan tek satırlık güçlü aksiyon alanı.',
            'category'=>'CTA','style'=>'Inline Minimal','sector'=>'Kurumsal','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-cta-inline-v13.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-cta',array('layout_style'=>'inline','surface_style'=>'minimal','title'=>'Bir sonraki adımı konuşalım','radius'=>array('size'=>18)))
            ),'#ffffff',42)
        );
        $sections[]=array(
            'key'=>'sec-cta-banner-v13','title'=>'CTA · Full Banner',
            'desc'=>'Landing page kapanışlarında yüksek görünürlüklü tam genişlik aksiyon bölümü.',
            'category'=>'CTA','style'=>'Full Banner','sector'=>'SaaS / Ajans','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-cta-banner-v13.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-cta',array('wpst_button_background'=>'#2563eb','wpst_button_text_color'=>'#ffffff','wpst_button_hover_background'=>'#1d4ed8','wpst_button_hover_text_color'=>'#ffffff','wpst_button_radius'=>array('size'=>14,'unit'=>'px'),'layout_style'=>'banner','surface_style'=>'dark','title'=>'Hazırsanız başlayalım','description'=>'Fikrinizi güçlü bir dijital deneyime dönüştürelim.','radius'=>array('size'=>24)))
            ),'#0f172a',52)
        );

        /* PRICING */
        $sections[]=array(
            'key'=>'sec-pricing-editorial-v13','title'=>'Fiyatlandırma · Editorial Plan',
            'desc'=>'Fiyatı ve faydaları büyük tipografiyle sade editoryal düzende sunar.',
            'category'=>'Fiyatlandırma','style'=>'Editorial Plan','sector'=>'Danışmanlık / SaaS','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-pricing-editorial-v13.svg',
            'data'=>$sec_wrap(array(
                $signature_heading('PRICING','Net ve anlaşılır fiyatlandırma','Karmaşık tablolar yerine güçlü bir teklif sunun.'),
                self::element('wpsoft-pricing',array('layout_variant'=>'editorial','style_preset'=>'minimal','radius'=>array('size'=>22),'padding'=>array('top'=>28,'right'=>28,'bottom'=>28,'left'=>28,'unit'=>'px')))
            ),'#fbfaf7',62)
        );
        $sections[]=array(
            'key'=>'sec-pricing-compact-v13','title'=>'Fiyatlandırma · Compact Plans',
            'desc'=>'Birden fazla küçük planı daha sıkı ve kompakt kartlarla sunar.',
            'category'=>'Fiyatlandırma','style'=>'Compact Plans','sector'=>'SaaS','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-pricing-compact-v13.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-pricing',array('layout_variant'=>'compact','style_preset'=>'soft','radius'=>array('size'=>20)))
            ),'#f8fafc',50)
        );

        /* CONTACT */
        $sections[]=array(
            'key'=>'sec-contact-expert-map-v13','title'=>'İletişim · Expert + Map',
            'desc'=>'Uzman profili, konum ve iletişim akışını tek premium bölümde birleştirir.',
            'category'=>'İletişim','style'=>'Expert Map','sector'=>'Klinik / Danışmanlık','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-contact-expert-map-v13.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-expert-profile',array('layout'=>'compact')),
                self::element('wpsoft-location-map',array('layout'=>'split'))
            ),'#ffffff',60)
        );


        /*
         * Image Box Pro · v3.2.83
         */
        $sections[]=array(
            'key'=>'sec-image-box-overlay-v14','title'=>'Görsel Kutusu · Overlay Cards',
            'desc'=>'WoodMart benzeri görsel kutu mantığını modern overlay kartlarla sunan bölüm.',
            'category'=>'Özellikler','style'=>'Image Box Overlay','sector'=>'Genel','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-image-box-overlay-v14.svg',
            'data'=>$sec_wrap(array(
                $signature_heading('EXPLORE','Görsel odaklı içerik kutuları','Kategori, hizmet veya koleksiyonları güçlü görsellerle öne çıkarın.'),
                self::container(array(
                    self::element('wpsoft-image-box-pro',array('layout'=>'overlay','title'=>'Web Tasarım','description'=>'Modern ve dönüşüm odaklı web deneyimleri.','image_ratio'=>'4-5','badge'=>'Popüler')),
                    self::element('wpsoft-image-box-pro',array('layout'=>'overlay','title'=>'E-Ticaret','description'=>'Satış odaklı mağaza deneyimleri.','image_ratio'=>'4-5','badge'=>'Yeni')),
                    self::element('wpsoft-image-box-pro',array('layout'=>'overlay','title'=>'SEO & Büyüme','description'=>'Organik görünürlük ve performans.','image_ratio'=>'4-5','badge'=>''))
                ),array('content_width'=>'full','flex_direction'=>'row','flex_wrap'=>'wrap','gap'=>array('unit'=>'px','size'=>18)))
            ),'#ffffff',64)
        );

        $sections[]=array(
            'key'=>'sec-image-box-editorial-v14','title'=>'Görsel Kutusu · Editorial Poster',
            'desc'=>'Büyük tipografi ve uzun görsel oranıyla editoryal poster tarzı görsel kutular.',
            'category'=>'Projeler','style'=>'Image Box Poster','sector'=>'Ajans / Mimarlık','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-image-box-editorial-v14.svg',
            'data'=>$sec_wrap(array(
                self::container(array(
                    self::element('wpsoft-image-box-pro',array('layout'=>'poster','eyebrow'=>'PROJECT 01','title'=>'Selected Work','description'=>'Görsel hikâye anlatımı için büyük poster kompozisyonu.','image_ratio'=>'3-4','badge'=>'')),
                    self::element('wpsoft-image-box-pro',array('layout'=>'poster','eyebrow'=>'PROJECT 02','title'=>'Brand Experience','description'=>'Editoryal proje ve koleksiyon sunumu.','image_ratio'=>'3-4','badge'=>'Featured'))
                ),array('content_width'=>'full','flex_direction'=>'row','flex_wrap'=>'wrap','gap'=>array('unit'=>'px','size'=>20)))
            ),'#fbfaf7',56)
        );

        $sections[]=array(
            'key'=>'sec-image-box-side-v14','title'=>'Görsel Kutusu · Horizontal Services',
            'desc'=>'Görsel ve içeriği yatay düzende birleştiren servis/kategori kutuları.',
            'category'=>'Hizmetler','style'=>'Image Box Side','sector'=>'Kurumsal','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-image-box-side-v14.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-image-box-pro',array('layout'=>'side','title'=>'Kurumsal Web Çözümleri','description'=>'Markanızı güçlü bir dijital deneyime dönüştüren uçtan uca çözüm.','badge'=>'01')),
                self::element('wpsoft-image-box-pro',array('layout'=>'side','title'=>'E-Ticaret Sistemleri','description'=>'Ürünlerinizi daha etkili sunan modern mağaza deneyimleri.','badge'=>'02'))
            ),'#f8fafc',58)
        );

        $sections[]=array(
            'key'=>'sec-image-box-floating-v14','title'=>'Görsel Kutusu · Floating Content',
            'desc'=>'Görsel üzerine cam yüzeyli içerik kartı bindiren premium image box bölümü.',
            'category'=>'Özellikler','style'=>'Image Box Floating','sector'=>'Otel / Lifestyle','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-image-box-floating-v14.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-image-box-pro',array('layout'=>'floating','eyebrow'=>'DISCOVER','title'=>'Yeni deneyimleri keşfedin','description'=>'Görsel alan üzerinde yüzen içerik paneli.','image_ratio'=>'16-9','badge'=>'Signature'))
            ),'#ffffff',54)
        );


        $sections[]=array(
            'key'=>'sec-image-box-full-image-v15','title'=>'Görsel Kutusu · Tam Görsel',
            'desc'=>'Kutunun tamamını dolduran görseller; içerik overlay, hover veya yalnız görsel olarak kullanılabilir.',
            'category'=>'Özellikler','style'=>'Image Box Full Image','sector'=>'Genel','quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-image-box-full-image-v15.svg',
            'data'=>$sec_wrap(array(
                self::container(array(
                    self::element('wpsoft-image-box-pro',array('layout'=>'full-image','title'=>'Modern Yaşam','description'=>'Tam görsel yüzey üzerinde sade içerik.','badge'=>'Yeni','full_image_content'=>'overlay','full_image_vertical'=>'bottom')),
                    self::element('wpsoft-image-box-pro',array('layout'=>'full-image','title'=>'Yeni Koleksiyon','description'=>'Hover ile açılan içerik katmanı.','badge'=>'','full_image_content'=>'hover','full_image_vertical'=>'center')),
                    self::element('wpsoft-image-box-pro',array('layout'=>'full-image','title'=>'','description'=>'','badge'=>'','full_image_content'=>'none'))
                ),array('content_width'=>'full','flex_direction'=>'row','flex_wrap'=>'wrap','gap'=>array('unit'=>'px','size'=>18)))
            ),'#ffffff',58)
        );


        $sections[]=array(
            'key'=>'sec-video-background-cinematic-v16',
            'title'=>'Video Background · Cinematic Hero',
            'desc'=>'MP4/WebM veya YouTube video arka planı, gradient overlay ve içerik katmanıyla sinematik hero bölümü.',
            'category'=>'Hero',
            'style'=>'Video Background',
            'sector'=>'Ajans / Kurumsal / Creative',
            'quality'=>'Signature',
            'is_new'=>true,
            'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-video-background-cinematic-v16.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-video-background-pro',array(
                    'source_type'=>'self',
                    'show_content'=>'yes',
                    'eyebrow'=>'CINEMATIC EXPERIENCE',
                    'title'=>'Markanızı hareketli bir deneyime dönüştürün',
                    'description'=>'Video arka plan, güçlü tipografi ve modern CTA yapısını tek alanda kullanın.',
                    'content_position'=>'center-left',
                    'content_style'=>'none',
                    'overlay_type'=>'gradient'
                ))
            ),'#0f172a',24)
        );

        $sections[]=array(
            'key'=>'sec-video-background-glass-v16',
            'title'=>'Video Background · Glass Content',
            'desc'=>'Video üzerinde cam yüzeyli içerik paneli kullanan premium video background bölümü.',
            'category'=>'Hero',
            'style'=>'Video Background Glass',
            'sector'=>'SaaS / Creative',
            'quality'=>'Signature',
            'is_new'=>true,
            'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-video-background-glass-v16.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-video-background-pro',array(
                    'source_type'=>'self',
                    'show_content'=>'yes',
                    'eyebrow'=>'NEW EXPERIENCE',
                    'title'=>'Görsel hikâyeyi hareketle güçlendirin',
                    'description'=>'Glass panel, poster fallback ve responsive video davranışıyla.',
                    'content_position'=>'bottom-left',
                    'content_style'=>'glass',
                    'overlay_type'=>'vignette'
                ))
            ),'#111827',24)
        );


        /* ======================================================
         * v3.2.92 · Quality Round · New independent compositions
         * ====================================================== */
        $sections[]=array(
            'key'=>'sec-process-editorial-v17',
            'title'=>'Süreç · Editorial Journey',
            'desc'=>'Büyük adım numaraları, güçlü tipografi ve editoryal akış kullanan premium süreç bölümü.',
            'category'=>'Süreç',
            'style'=>'Editorial Process',
            'sector'=>'Ajans / Kurumsal',
            'quality'=>'Signature',
            'is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-process-editorial-v17.svg',
            'data'=>$sec_wrap(array(
                $signature_heading('HOW WE WORK','Fikirden yayına net bir süreç','Her adımı görünür, anlaşılır ve modern bir akışta sunun.'),
                self::element('wpsoft-process-steps-pro',array(
                    'layout_variant'=>'editorial',
                    'show_connector'=>'yes',
                    'items'=>array(
                        array('step'=>'01','title'=>'Keşif','text'=>'İhtiyaç, hedef ve kullanıcı beklentilerini belirliyoruz.'),
                        array('step'=>'02','title'=>'Sistem','text'=>'Bilgi mimarisi ve tasarım yönünü oluşturuyoruz.'),
                        array('step'=>'03','title'=>'Üretim','text'=>'Arayüzü ve teknik altyapıyı geliştiriyoruz.'),
                        array('step'=>'04','title'=>'Yayın','text'=>'Test, optimizasyon ve teslim sürecini tamamlıyoruz.')
                    )
                ))
            ),'#ffffff',62)
        );

        $sections[]=array(
            'key'=>'sec-timeline-milestones-v17',
            'title'=>'Timeline · Milestones',
            'desc'=>'Yıl ve kilometre taşlarını büyük tipografiyle gösteren modern marka hikâyesi.',
            'category'=>'Hakkımızda',
            'style'=>'Milestone Timeline',
            'sector'=>'Kurumsal / Startup',
            'quality'=>'Signature',
            'is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-timeline-milestones-v17.svg',
            'data'=>$sec_wrap(array(
                $signature_heading('OUR STORY','Büyümeyi kilometre taşlarıyla anlatın','Marka geçmişini sade ama güçlü bir görsel ritimle sunun.'),
                self::element('wpsoft-timeline-modern',array(
                    'layout_variant'=>'milestones',
                    'show_line'=>'yes',
                    'items'=>array(
                        array('year'=>'2022','title'=>'Başlangıç','text'=>'İlk ürün ve projeler yayına alındı.'),
                        array('year'=>'2024','title'=>'Büyüme','text'=>'Yeni sektörler ve daha geniş hizmet kapsamı.'),
                        array('year'=>'2026','title'=>'Yeni Nesil','text'=>'Daha güçlü ürün altyapısı ve tasarım sistemi.')
                    )
                ))
            ),'#f8fafc',60)
        );

        $sections[]=array(
            'key'=>'sec-commerce-product-focus-v17',
            'title'=>'Commerce Hero · Product Focus',
            'desc'=>'Ürün görselini ana odak haline getiren modern e-ticaret hero kompozisyonu.',
            'category'=>'Ürün Tanıtımı',
            'style'=>'Commerce Product Focus',
            'sector'=>'E-Ticaret',
            'quality'=>'Signature',
            'is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-commerce-product-focus-v17.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-hero-commerce',array(
                    'layout'=>'product-focus',
                    'badge'=>'NEW DROP',
                    'title'=>'Yeni sezonun öne çıkan parçası',
                    'text'=>'Ürün odaklı sayfalar için geniş medya alanı ve net CTA.',
                    'discount'=>'%25',
                    'show_discount'=>'yes',
                    'button_text'=>'Koleksiyonu Gör',
                    'button_url'=>array('url'=>'#')
                ))
            ),'#f7f5f2',18)
        );

        $sections[]=array(
            'key'=>'sec-gradient-display-v17',
            'title'=>'Başlık · Gradient Display',
            'desc'=>'Büyük ölçekli gradient tipografiyle bölüm geçişi veya manifesto alanı.',
            'category'=>'Metin',
            'style'=>'Gradient Display',
            'sector'=>'Creative / SaaS',
            'quality'=>'Signature',
            'is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-gradient-display-v17.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-gradient-heading',array(
                    'layout'=>'display',
                    'highlight_mode'=>'full',
                    'eyebrow'=>'NEXT GENERATION',
                    'title'=>'Daha hızlı. Daha güçlü. Daha akılda kalıcı.',
                    'text'=>'Yeni nesil dijital ürünler için güçlü bir tipografik geçiş alanı.'
                ))
            ),'#0b1020',72)
        );

        $sections[]=array(
            'key'=>'sec-flip-services-v17',
            'title'=>'Hizmetler · Flip Showcase',
            'desc'=>'Ön yüzde görsel, arka yüzde detay ve CTA sunan interaktif hizmet kartları.',
            'category'=>'Hizmetler',
            'style'=>'Flip Services',
            'sector'=>'Ajans / Creative',
            'quality'=>'Signature',
            'is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-flip-services-v17.svg',
            'data'=>$sec_wrap(array(
                $signature_heading('SERVICES','Kartın arkasında daha fazla detay','Hizmetleri interaktif ve daha keşfedilebilir bir yapıda sunun.'),
                self::container(array(
                    self::element('wpsoft-flip-box',array('front_title'=>'Web Experience','front_text'=>'Modern dijital deneyimler.','front_badge'=>'01','back_title'=>'Web Experience','back_text'=>'Strateji, UI/UX ve geliştirmeyi tek akışta birleştirin.','button_text'=>'Detay','button_url'=>array('url'=>'#'))),
                    self::element('wpsoft-flip-box',array('front_title'=>'E-Commerce','front_text'=>'Dönüşüm odaklı mağazalar.','front_badge'=>'02','back_title'=>'E-Commerce','back_text'=>'Ürün sunumu, kullanıcı akışı ve satış performansı birlikte ele alınır.','button_text'=>'Detay','button_url'=>array('url'=>'#'))),
                    self::element('wpsoft-flip-box',array('front_title'=>'Growth','front_text'=>'SEO ve performans.','front_badge'=>'03','back_title'=>'Growth','back_text'=>'Teknik SEO, içerik yapısı ve ölçülebilir büyüme altyapısı.','button_text'=>'Detay','button_url'=>array('url'=>'#')))
                ),array('content_width'=>'full','flex_direction'=>'row','flex_wrap'=>'wrap','gap'=>array('unit'=>'px','size'=>18)))
            ),'#ffffff',62)
        );

        $sections[]=array(
            'key'=>'sec-icon-list-trust-v17',
            'title'=>'Özellikler · Trust List',
            'desc'=>'Minimal ikon listesiyle avantaj, güven unsuru veya ürün faydalarını gösteren bölüm.',
            'category'=>'Özellikler',
            'style'=>'Trust Icon List',
            'sector'=>'Genel',
            'quality'=>'Signature',
            'is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-icon-list-trust-v17.svg',
            'data'=>$sec_wrap(array(
                self::container(array(
                    $signature_heading('WHY WPSOFT','Detaylarda fark yaratan altyapı','Kullanıcıya en önemli avantajları hızlıca aktarın.'),
                    self::element('wpsoft-icon-list',array(
                        'layout'=>'rows',
                        'columns'=>'1',
                        'items'=>array(
                            array('wpst_icon'=>'zap','title'=>'Yüksek Performans','text'=>'Hız ve Core Web Vitals odaklı yapı.'),
                            array('wpst_icon'=>'smartphone','title'=>'Responsive Sistem','text'=>'Masaüstü, tablet ve mobilde kontrollü deneyim.'),
                            array('wpst_icon'=>'shield','title'=>'Sürdürülebilir Altyapı','text'=>'Geliştirilebilir, yönetilebilir ve güncel mimari.')
                        )
                    ))
                ),array('content_width'=>'full','flex_direction'=>'row','flex_wrap'=>'wrap','gap'=>array('unit'=>'px','size'=>36)))
            ),'#f8fafc',64)
        );


        /* v3.2.93 · Quality Round 6 */
        $sections[]=array(
            'key'=>'sec-hospitality-editorial-v18',
            'title'=>'Hospitality · Editorial Escape',
            'desc'=>'Büyük tipografi, tam görsel ve premium puan kartıyla otel/resort hero bölümü.',
            'category'=>'Hero','style'=>'Hospitality Editorial','sector'=>'Otel / Turizm',
            'quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-hospitality-editorial-v18.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-hero-hospitality',array(
                    'layout_variant'=>'editorial',
                    'eyebrow'=>'ESCAPE THE ORDINARY',
                    'title'=>'Sessiz lüks, doğal ritim ve unutulmaz bir konaklama',
                    'text'=>'Otel ve resort markaları için güçlü, görsel odaklı editoryal hero.',
                    'button_text'=>'Konaklamayı Keşfet',
                    'button_url'=>array('url'=>'#')
                ))
            ),'#0f172a',18)
        );

        $sections[]=array(
            'key'=>'sec-booking-premium-v18',
            'title'=>'Rezervasyon · Premium Booking Strip',
            'desc'=>'Check-in, check-out ve misafir alanlarını tek premium rezervasyon şeridinde sunar.',
            'category'=>'İletişim','style'=>'Booking Strip','sector'=>'Otel / Turizm',
            'quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-booking-premium-v18.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-booking-strip',array(
                    'checkin'=>'18 Ağustos','checkout'=>'21 Ağustos','guests'=>'2 Yetişkin',
                    'button_text'=>'Müsaitliği Kontrol Et','button_url'=>array('url'=>'#')
                ))
            ),'#f8fafc',46)
        );

        $sections[]=array(
            'key'=>'sec-expert-editorial-v18',
            'title'=>'Uzman · Editorial Profile',
            'desc'=>'Büyük portre, uzmanlık bilgileri ve CTA ile premium uzman/doktor profil bölümü.',
            'category'=>'Ekip','style'=>'Expert Editorial','sector'=>'Sağlık / Danışmanlık',
            'quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-expert-editorial-v18.svg',
            'data'=>$sec_wrap(array(
                self::element('wpsoft-expert-profile',array(
                    'layout'=>'editorial',
                    'name'=>'Dr. Deniz Arslan','role'=>'Uzman Danışman',
                    'bio'=>'Deneyim, güven ve uzmanlığı sade bir editoryal kompozisyonla öne çıkarın.',
                    'meta'=>"15+ Yıl Deneyim\n120+ Proje\nİstanbul",
                    'button_text'=>'Profili İncele','button_url'=>array('url'=>'#')
                ))
            ),'#ffffff',58)
        );

        $sections[]=array(
            'key'=>'sec-hover-reveal-editorial-v18',
            'title'=>'Projeler · Editorial Hover Reveal',
            'desc'=>'Bir büyük ve iki destekleyici görsel kartla asimetrik proje/hizmet showcase.',
            'category'=>'Projeler','style'=>'Hover Reveal Editorial','sector'=>'Ajans / Mimarlık',
            'quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-hover-reveal-editorial-v18.svg',
            'data'=>$sec_wrap(array(
                $signature_heading('SELECTED WORK','Seçili işleri daha etkileyici sunun','Asimetrik görsel kompozisyon ve hover detaylarıyla.'),
                self::element('wpsoft-hover-reveal',array(
                    'layout_variant'=>'editorial',
                    'items'=>array(
                        array('title'=>'Hospitality Experience','text'=>'Marka ve dijital deneyim tasarımı.'),
                        array('title'=>'Commerce System','text'=>'Dönüşüm odaklı e-ticaret deneyimi.'),
                        array('title'=>'Corporate Platform','text'=>'Kurumsal bilgi mimarisi ve içerik sistemi.')
                    )
                ))
            ),'#ffffff',62)
        );

        $sections[]=array(
            'key'=>'sec-price-list-modern-v18',
            'title'=>'Menü · Modern Price List',
            'desc'=>'Restoran, servis veya paket fiyatlarını sade ve premium bir liste düzeninde sunar.',
            'category'=>'Fiyatlandırma','style'=>'Modern Price List','sector'=>'Restoran / Hizmet',
            'quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-price-list-modern-v18.svg',
            'data'=>$sec_wrap(array(
                $signature_heading('MENU','Seçili tatlar ve deneyimler','Temiz hiyerarşi ve güçlü fiyat görünümü.'),
                self::element('wpsoft-price-list',array(
                    'layout'=>'editorial',
                    'items'=>array(
                        array('title'=>'Signature Menü','description'=>'Şefin mevsimsel seçkisi.','price'=>'₺950','badge'=>'Önerilen'),
                        array('title'=>'Classic Menü','description'=>'Sevilen klasiklerden dengeli bir seçki.','price'=>'₺720','badge'=>''),
                        array('title'=>'Tasting Menü','description'=>'Çok aşamalı özel deneyim.','price'=>'₺1.250','badge'=>'Yeni')
                    )
                ))
            ),'#fbfaf7',60)
        );

        $sections[]=array(
            'key'=>'sec-product-showcase-premium-v18',
            'title'=>'Ürünler · Premium Showcase',
            'desc'=>'Ürün görseli, etiket, fiyat ve aksiyonu modern kartlarda gösteren ürün bölümü.',
            'category'=>'Ürün Tanıtımı','style'=>'Premium Product Showcase','sector'=>'E-Ticaret',
            'quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-product-showcase-premium-v18.svg',
            'data'=>$sec_wrap(array(
                $signature_heading('CURATED','Öne çıkan ürün seçkisi','Ürünleri sade ve premium kartlarla sergileyin.'),
                self::element('wpsoft-product-showcase',array(
                    'action_text'=>'Ürünü İncele',
                    'items'=>array(
                        array('title'=>'Studio Chair','meta'=>'Yeni','price'=>'₺12.900'),
                        array('title'=>'Mono Lamp','meta'=>'Popüler','price'=>'₺6.450'),
                        array('title'=>'Form Table','meta'=>'Sınırlı','price'=>'₺18.750')
                    )
                ))
            ),'#f8fafc',62)
        );


        /* ======================================================
         * v3.2.97 · Services System 3.0
         * ====================================================== */
        $sections[]=array(
            'key'=>'sec-services-modern-grid-v19',
            'title'=>'Hizmetler · Modern Grid 3.0',
            'desc'=>'Icon, badge, açıklama ve aksiyon içeren modern servis kartları.',
            'category'=>'Hizmetler',
            'style'=>'Modern Grid',
            'sector'=>'Ajans / Kurumsal / SaaS',
            'quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-services-modern-grid-v19.svg',
            'data'=>$sec_wrap(array(
                $signature_heading('CAPABILITIES','İşinizi ileri taşıyan uzmanlık alanları','Stratejiden teknolojiye tüm hizmetleri tek, tutarlı sistemde sunun.'),
                self::element('wpsoft-service-cards-pro',array(
                    'layout_variant'=>'modern','card_style'=>'elevated','columns'=>'3',
                    'show_numbers'=>'yes','show_badges'=>'yes','action_text'=>'Hizmeti İncele',
                    'items'=>array(
                        array('wpst_icon'=>'target','tag'=>'01','badge'=>'Strategy','title'=>'Dijital Strateji','text'=>'Hedef, konumlandırma ve büyüme yol haritası.','url'=>array('url'=>'#')),
                        array('wpst_icon'=>'palette','tag'=>'02','badge'=>'Design','title'=>'UI / UX Tasarım','text'=>'Kullanıcı odaklı ve modern dijital deneyimler.','url'=>array('url'=>'#')),
                        array('wpst_icon'=>'code','tag'=>'03','badge'=>'Technology','title'=>'Web Development','text'=>'Performanslı ve ölçeklenebilir ürün altyapısı.','url'=>array('url'=>'#')),
                        array('wpst_icon'=>'chart','tag'=>'04','badge'=>'Growth','title'=>'SEO & Growth','text'=>'Organik görünürlük ve ölçülebilir büyüme.','url'=>array('url'=>'#')),
                        array('wpst_icon'=>'layers','tag'=>'05','badge'=>'Commerce','title'=>'E-Ticaret','text'=>'Ürün, içerik ve satış akışlarını optimize edin.','url'=>array('url'=>'#')),
                        array('wpst_icon'=>'help-circle','tag'=>'06','badge'=>'Support','title'=>'Bakım & Destek','text'=>'Sürdürülebilir teknik destek ve iyileştirme.','url'=>array('url'=>'#'))
                    )
                ))
            ),'#ffffff',64)
        );

        $sections[]=array(
            'key'=>'sec-services-image-grid-v19',
            'title'=>'Hizmetler · Image Cards',
            'desc'=>'Görsel, icon ve güçlü içerik hiyerarşisi kullanan premium hizmet kartları.',
            'category'=>'Hizmetler',
            'style'=>'Image Services',
            'sector'=>'Creative / Mimarlık / Ajans',
            'quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-services-image-grid-v19.svg',
            'data'=>$sec_wrap(array(
                $signature_heading('WHAT WE DO','Hizmetlerinizi görsel hikâyelerle anlatın','Her hizmet kendi görsel kimliği ve net aksiyonuyla öne çıksın.'),
                self::element('wpsoft-service-cards-pro',array(
                    'layout_variant'=>'image','card_style'=>'flat','columns'=>'2','show_images'=>'yes',
                    'items'=>array(
                        array('wpst_icon'=>'target','tag'=>'01','badge'=>'Strategy','title'=>'Strateji & Konumlandırma','text'=>'Marka, kullanıcı ve pazar verisini tek yol haritasında birleştirin.','image'=>array('url'=>WPST_URL.'assets/images/services/service-strategy.svg'),'url'=>array('url'=>'#')),
                        array('wpst_icon'=>'palette','tag'=>'02','badge'=>'Design','title'=>'Dijital Tasarım','text'=>'Güçlü görsel dil ve yüksek kullanılabilirlik sağlayan arayüzler.','image'=>array('url'=>WPST_URL.'assets/images/services/service-design.svg'),'url'=>array('url'=>'#')),
                        array('wpst_icon'=>'code','tag'=>'03','badge'=>'Technology','title'=>'Ürün Geliştirme','text'=>'Hızlı ve sürdürülebilir web ürünleri geliştirin.','image'=>array('url'=>WPST_URL.'assets/images/services/service-tech.svg'),'url'=>array('url'=>'#')),
                        array('wpst_icon'=>'chart','tag'=>'04','badge'=>'Growth','title'=>'Büyüme & Performans','text'=>'SEO, hız ve dönüşüm metriklerini birlikte yönetin.','image'=>array('url'=>WPST_URL.'assets/images/services/service-growth.svg'),'url'=>array('url'=>'#'))
                    )
                ))
            ),'#f8fafc',64)
        );

        $sections[]=array(
            'key'=>'sec-services-bento-3-v19',
            'title'=>'Hizmetler · Bento Services 3.0',
            'desc'=>'Farklı kart boyutlarıyla güçlü görsel ritim oluşturan bento hizmet kompozisyonu.',
            'category'=>'Hizmetler',
            'style'=>'Bento',
            'sector'=>'SaaS / Creative',
            'quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-services-bento-3-v19.svg',
            'data'=>$sec_wrap(array(
                $signature_heading('EXPERTISE','Birbirini tamamlayan uzmanlık alanları','Tekrarlayan kart diziliminden uzak, daha dinamik bir servis vitrini.'),
                self::element('wpsoft-service-cards-pro',array(
                    'layout_variant'=>'bento','card_style'=>'soft','columns'=>'3',
                    'items'=>array(
                        array('wpst_icon'=>'sparkles','tag'=>'01','badge'=>'Core','title'=>'Brand Experience','text'=>'Marka stratejisi ve dijital deneyimi tek çatı altında kurgulayın.','url'=>array('url'=>'#')),
                        array('wpst_icon'=>'palette','tag'=>'02','badge'=>'UI/UX','title'=>'Product Design','text'=>'Karmaşık ürünleri sade kullanıcı akışlarına dönüştürün.','url'=>array('url'=>'#')),
                        array('wpst_icon'=>'code','tag'=>'03','badge'=>'Dev','title'=>'Development','text'=>'Performanslı frontend ve WordPress altyapısı.','url'=>array('url'=>'#')),
                        array('wpst_icon'=>'chart','tag'=>'04','badge'=>'Growth','title'=>'Growth Systems','text'=>'SEO, içerik ve performansı sürekli gelişen bir sisteme bağlayın.','url'=>array('url'=>'#'))
                    )
                ))
            ),'#ffffff',64)
        );

        $sections[]=array(
            'key'=>'sec-services-editorial-3-v19',
            'title'=>'Hizmetler · Editorial Index 3.0',
            'desc'=>'Büyük başlıklar ve yatay hizmet satırlarıyla modern editoryal servis listesi.',
            'category'=>'Hizmetler',
            'style'=>'Editorial Index',
            'sector'=>'Ajans / Tasarım Stüdyosu',
            'quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-services-editorial-3-v19.svg',
            'data'=>$sec_wrap(array(
                $signature_heading('SERVICES INDEX','Net, büyük ve doğrudan','Hizmetlerinizi kartlardan ziyade editoryal bir indeks gibi sunun.'),
                self::element('wpsoft-service-cards-pro',array(
                    'layout_variant'=>'editorial','card_style'=>'flat','columns'=>'1','show_badges'=>'no',
                    'items'=>array(
                        array('wpst_icon'=>'target','tag'=>'01','title'=>'Strategy & Research','text'=>'Ürün, pazar ve kullanıcı araştırmalarını stratejik kararlara dönüştürün.','url'=>array('url'=>'#')),
                        array('wpst_icon'=>'palette','tag'=>'02','title'=>'Design Systems','text'=>'Tutarlı UI, component ve marka deneyimi sistemleri oluşturun.','url'=>array('url'=>'#')),
                        array('wpst_icon'=>'code','tag'=>'03','title'=>'Development','text'=>'Modern, hızlı ve yönetilebilir web deneyimleri geliştirin.','url'=>array('url'=>'#')),
                        array('wpst_icon'=>'chart','tag'=>'04','title'=>'Growth & SEO','text'=>'Teknik performansı görünürlük ve dönüşümle birleştirin.','url'=>array('url'=>'#'))
                    )
                ))
            ),'#fbfbfc',58)
        );

        $sections[]=array(
            'key'=>'sec-services-carousel-media-v19',
            'title'=>'Hizmetler · Media Carousel',
            'desc'=>'Görselli servis kartlarını kaydırılabilir premium carousel yapısında sunar.',
            'category'=>'Hizmetler',
            'style'=>'Services Carousel',
            'sector'=>'Ajans / Creative',
            'quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-services-carousel-media-v19.svg',
            'data'=>$sec_wrap(array(
                $signature_heading('OUR SERVICES','Kaydırarak daha fazlasını keşfedin','Geniş hizmet portföylerini modern carousel ile daha kompakt sunun.'),
                self::element('wpsoft-service-carousel-pro',array(
                    'style_preset'=>'media','visible'=>'3','visible_tablet'=>'2','visible_mobile'=>'1','show_arrows'=>'yes',
                    'items'=>array(
                        array('wpst_icon'=>'target','tag'=>'01','category'=>'Strategy','title'=>'Digital Strategy','text'=>'Marka ve dijital büyüme için net yol haritası.','image'=>array('url'=>WPST_URL.'assets/images/services/service-strategy.svg'),'url'=>array('url'=>'#')),
                        array('wpst_icon'=>'palette','tag'=>'02','category'=>'Design','title'=>'UI / UX Design','text'=>'Modern ve dönüşüm odaklı kullanıcı deneyimleri.','image'=>array('url'=>WPST_URL.'assets/images/services/service-design.svg'),'url'=>array('url'=>'#')),
                        array('wpst_icon'=>'code','tag'=>'03','category'=>'Technology','title'=>'Development','text'=>'Hızlı, sürdürülebilir ve ölçeklenebilir altyapı.','image'=>array('url'=>WPST_URL.'assets/images/services/service-tech.svg'),'url'=>array('url'=>'#')),
                        array('wpst_icon'=>'chart','tag'=>'04','category'=>'Growth','title'=>'SEO & Growth','text'=>'Organik görünürlük ve ölçülebilir performans.','image'=>array('url'=>WPST_URL.'assets/images/services/service-growth.svg'),'url'=>array('url'=>'#'))
                    )
                ))
            ),'#ffffff',64)
        );

        $sections[]=array(
            'key'=>'sec-services-carousel-dark-v19',
            'title'=>'Hizmetler · Dark Expertise Carousel',
            'desc'=>'Koyu premium kartlar ve güçlü tipografi kullanan servis carousel bölümü.',
            'category'=>'Hizmetler',
            'style'=>'Dark Carousel',
            'sector'=>'Teknoloji / Ajans',
            'quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-services-carousel-dark-v19.svg',
            'data'=>$sec_wrap(array(
                $signature_heading('EXPERTISE','Teknoloji ve tasarımın kesişiminde','Uzmanlıkları premium koyu yüzeylerle öne çıkarın.'),
                self::element('wpsoft-service-carousel-pro',array(
                    'style_preset'=>'dark','visible'=>'3','visible_tablet'=>'2','visible_mobile'=>'1','show_arrows'=>'yes',
                    'items'=>array(
                        array('wpst_icon'=>'layers','tag'=>'01','category'=>'Systems','title'=>'Digital Platforms','text'=>'Kurumsal içerik ve ürün sistemlerini tek altyapıda birleştirin.','url'=>array('url'=>'#')),
                        array('wpst_icon'=>'code','tag'=>'02','category'=>'Engineering','title'=>'Custom Development','text'=>'İhtiyaca özel, sürdürülebilir teknik çözümler.','url'=>array('url'=>'#')),
                        array('wpst_icon'=>'sparkles','tag'=>'03','category'=>'Experience','title'=>'Interactive Experiences','text'=>'Hareket, mikro etkileşim ve güçlü kullanıcı deneyimi.','url'=>array('url'=>'#')),
                        array('wpst_icon'=>'chart','tag'=>'04','category'=>'Performance','title'=>'Optimization','text'=>'Hız, SEO ve dönüşüm optimizasyonunu birlikte yönetin.','url'=>array('url'=>'#'))
                    )
                ))
            ),'#080d18',64)
        );

        $sections[]=array(
            'key'=>'sec-services-carousel-numbered-v19',
            'title'=>'Hizmetler · Numbered Carousel',
            'desc'=>'Büyük numaralar ve minimal içerikle editorial carousel kompozisyonu.',
            'category'=>'Hizmetler',
            'style'=>'Numbered Carousel',
            'sector'=>'Creative / Studio',
            'quality'=>'Signature','is_new'=>true,'premium'=>1,
            'preview_image'=>WPST_URL.'assets/images/section-templates/sec-services-carousel-numbered-v19.svg',
            'data'=>$sec_wrap(array(
                $signature_heading('HOW WE HELP','Dört temel uzmanlık. Tek güçlü sistem.','Minimal ve tipografi odaklı carousel görünümü.'),
                self::element('wpsoft-service-carousel-pro',array(
                    'style_preset'=>'numbered','visible'=>'3','visible_tablet'=>'2','visible_mobile'=>'1','show_arrows'=>'yes',
                    'items'=>array(
                        array('wpst_icon'=>'target','tag'=>'01','category'=>'Discover','title'=>'Araştırma & Strateji','text'=>'Sorunu doğru tanımlayın ve net bir yön oluşturun.','url'=>array('url'=>'#')),
                        array('wpst_icon'=>'palette','tag'=>'02','category'=>'Design','title'=>'Deneyim Tasarımı','text'=>'Fikirleri sezgisel dijital ürünlere dönüştürün.','url'=>array('url'=>'#')),
                        array('wpst_icon'=>'code','tag'=>'03','category'=>'Build','title'=>'Geliştirme','text'=>'Tasarımları performanslı ve güvenilir ürünlere taşıyın.','url'=>array('url'=>'#')),
                        array('wpst_icon'=>'chart','tag'=>'04','category'=>'Grow','title'=>'Optimizasyon','text'=>'Yayın sonrası verilerle sürekli geliştirin.','url'=>array('url'=>'#'))
                    )
                ))
            ),'#f8fafc',64)
        );

$pages=array();

        /* ======================================================
         * v3.3.17 · Independent Inner Pages · Phase 2
         * ====================================================== */

        $pages[]=array(
            'key'=>'page-references-proof-v1','title'=>'Referanslar · Trust & Results',
            'desc'=>'Müşteri logoları, yorumlar, başarı metrikleri ve vaka özetiyle bağımsız referanslar sayfası.',
            'preview_image'=>self::preview('page-references-proof-v1.svg'),
            'category'=>'İç Sayfalar','sector'=>'Kurumsal / Ajans','premium'=>1,'quality'=>'Signature',
            'page_quality'=>'Independent Inner Page + Motion','collection'=>'Proof Inner Pages','experience'=>'References',
            'responsive_ready'=>true,'is_new'=>true,
            'data'=>array(
                self::container(array(self::element('wpsoft-gradient-heading',array('layout'=>'display','eyebrow'=>'REFERANSLAR','title'=>'Birlikte ürettiğimiz sonuçlar kendini anlatır.','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>820,'wpst_entry_distance'=>28,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>8))),array('_css_classes'=>'wpst-page-section wpst-page-hero','padding'=>array('unit'=>'px','top'=>'82','right'=>'24','bottom'=>'58','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-logo-grid-pro',array('wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>660,'wpst_entry_distance'=>18,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>85,'wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-proof','background_background'=>'classic','background_color'=>'#f8fafc','padding'=>array('unit'=>'px','top'=>'62','right'=>'24','bottom'=>'62','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-reviews-pro',array('layout'=>'wall','columns'=>'3','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>660,'wpst_entry_distance'=>18,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>85,'wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-proof','padding'=>array('unit'=>'px','top'=>'72','right'=>'24','bottom'=>'72','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-stats-grid',array('layout_variant'=>'hero','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>660,'wpst_entry_distance'=>18,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>85,'wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-proof','background_background'=>'classic','background_color'=>'#0f172a','padding'=>array('unit'=>'px','top'=>'58','right'=>'24','bottom'=>'58','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-content-slider',array('wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>700,'wpst_entry_distance'=>20,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-content','padding'=>array('unit'=>'px','top'=>'68','right'=>'24','bottom'=>'68','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-cta',array('layout_style'=>'floating','title'=>'Sıradaki başarı hikâyesi sizin olabilir.','wpst_entry_motion'=>'scale','wpst_entry_duration'=>720,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>15))),array('_css_classes'=>'wpst-page-section wpst-page-contact','background_background'=>'classic','background_color'=>'#f8fafc','padding'=>array('unit'=>'px','top'=>'54','right'=>'24','bottom'=>'80','left'=>'24','isLinked'=>false)))
            )
        );

        $pages[]=array(
            'key'=>'page-service-detail-v1','title'=>'Hizmet Detay · Focused Service',
            'desc'=>'Tek bir hizmeti problem, çözüm, süreç, kapsam, SSS ve CTA ile anlatan bağımsız hizmet detay sayfası.',
            'preview_image'=>self::preview('page-service-detail-v1.svg'),
            'category'=>'İç Sayfalar','sector'=>'Hizmet / Ajans','premium'=>1,'quality'=>'Signature',
            'page_quality'=>'Independent Inner Page + Motion','collection'=>'Service Inner Pages','experience'=>'Service Detail',
            'responsive_ready'=>true,'is_new'=>true,
            'data'=>array(
                self::container(array(self::element('wpsoft-breadcrumb',array('wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>700,'wpst_entry_distance'=>20,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12)),self::element('wpsoft-hero-split-modern',array('composition'=>'minimal','eyebrow'=>'HİZMET','title'=>'İşinizi büyüten net ve ölçülebilir bir hizmet yaklaşımı.','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>820,'wpst_entry_distance'=>28,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>8))),array('_css_classes'=>'wpst-page-section wpst-page-hero','background_background'=>'classic','background_color'=>'#f8fafc','padding'=>array('unit'=>'px','top'=>'42','right'=>'24','bottom'=>'64','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-image-text',array('wpst_entry_motion'=>'reveal-left','wpst_entry_duration'=>840,'wpst_entry_distance'=>24,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-content','padding'=>array('unit'=>'px','top'=>'72','right'=>'24','bottom'=>'72','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-feature-list',array('wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>660,'wpst_entry_distance'=>18,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>85,'wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-content','background_background'=>'classic','background_color'=>'#f8fafc','padding'=>array('unit'=>'px','top'=>'64','right'=>'24','bottom'=>'64','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-process-steps-pro',array('layout_variant'=>'timeline','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>660,'wpst_entry_distance'=>18,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>85,'wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-content','padding'=>array('unit'=>'px','top'=>'70','right'=>'24','bottom'=>'70','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-advanced-accordion',array('layout_variant'=>'panel','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>700,'wpst_entry_distance'=>20,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-content','background_background'=>'classic','background_color'=>'#f8fafc','padding'=>array('unit'=>'px','top'=>'64','right'=>'24','bottom'=>'64','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-cta',array('layout_style'=>'banner','title'=>'Bu hizmeti projenize uyarlayalım.','wpst_entry_motion'=>'scale','wpst_entry_duration'=>720,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>15))),array('_css_classes'=>'wpst-page-section wpst-page-contact','padding'=>array('unit'=>'px','top'=>'50','right'=>'24','bottom'=>'78','left'=>'24','isLinked'=>false)))
            )
        );

        $pages[]=array(
            'key'=>'page-project-detail-v1','title'=>'Proje Detay · Case Study',
            'desc'=>'Proje özeti, süreç, görsel anlatım, sonuçlar ve müşteri yorumu içeren bağımsız vaka/proje detay sayfası.',
            'preview_image'=>self::preview('page-project-detail-v1.svg'),
            'category'=>'İç Sayfalar','sector'=>'Ajans / Creative','premium'=>1,'quality'=>'Signature',
            'page_quality'=>'Independent Inner Page + Motion','collection'=>'Creative Inner Pages','experience'=>'Project Detail',
            'responsive_ready'=>true,'is_new'=>true,
            'data'=>array(
                self::container(array(self::element('wpsoft-breadcrumb',array('wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>700,'wpst_entry_distance'=>20,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12)),self::element('wpsoft-post-title',array('wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>820,'wpst_entry_distance'=>28,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>8))),array('_css_classes'=>'wpst-page-section wpst-page-hero','padding'=>array('unit'=>'px','top'=>'44','right'=>'24','bottom'=>'56','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-image-reveal',array('wpst_entry_motion'=>'clip-up','wpst_entry_duration'=>900,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-showcase','background_background'=>'classic','background_color'=>'#111827','padding'=>array('unit'=>'px','top'=>'58','right'=>'24','bottom'=>'58','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-image-text',array('wpst_entry_motion'=>'reveal-left','wpst_entry_duration'=>840,'wpst_entry_distance'=>24,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-content','padding'=>array('unit'=>'px','top'=>'72','right'=>'24','bottom'=>'72','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-before-after',array('wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>700,'wpst_entry_distance'=>20,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-showcase','background_background'=>'classic','background_color'=>'#f8fafc','padding'=>array('unit'=>'px','top'=>'66','right'=>'24','bottom'=>'66','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-stats-grid',array('layout_variant'=>'hero','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>660,'wpst_entry_distance'=>18,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>85,'wpst_entry_threshold'=>12)),self::element('wpsoft-quote',array('wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>700,'wpst_entry_distance'=>20,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-proof','padding'=>array('unit'=>'px','top'=>'68','right'=>'24','bottom'=>'68','left'=>'24','isLinked'=>false),'gap'=>array('unit'=>'px','size'=>28))),
                self::container(array(self::element('wpsoft-morphing-cta',array('eyebrow'=>'NEXT PROJECT','title'=>'Yeni bir proje hikâyesi yazalım.','wpst_entry_motion'=>'scale','wpst_entry_duration'=>720,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>15))),array('_css_classes'=>'wpst-page-section wpst-page-contact','padding'=>array('unit'=>'px','top'=>'52','right'=>'24','bottom'=>'80','left'=>'24','isLinked'=>false)))
            )
        );

        $pages[]=array(
            'key'=>'page-blog-archive-modern-v1','title'=>'Blog · Editorial Archive',
            'desc'=>'Öne çıkan içerikler, editorial feed, kategori keşfi ve newsletter ile bağımsız blog listeleme sayfası.',
            'preview_image'=>self::preview('page-blog-archive-modern-v1.svg'),
            'category'=>'İç Sayfalar','sector'=>'Blog / Medya','premium'=>1,'quality'=>'Signature',
            'page_quality'=>'Independent Inner Page + Motion','collection'=>'Editorial Inner Pages','experience'=>'Blog Archive',
            'responsive_ready'=>true,'is_new'=>true,
            'data'=>array(
                self::container(array(self::element('wpsoft-gradient-heading',array('layout'=>'display','eyebrow'=>'JOURNAL','title'=>'Fikirler, rehberler ve güncel notlar.','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>820,'wpst_entry_distance'=>28,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>8))),array('_css_classes'=>'wpst-page-section wpst-page-hero','background_background'=>'classic','background_color'=>'#0b1020','padding'=>array('unit'=>'px','top'=>'84','right'=>'24','bottom'=>'72','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-content-finder',array('layout'=>'compact','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>700,'wpst_entry_distance'=>20,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-content','padding'=>array('unit'=>'px','top'=>'42','right'=>'24','bottom'=>'46','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-blog-posts',array('layout_style'=>'editorial-feed','posts_per_page'=>9,'wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>660,'wpst_entry_distance'=>18,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>85,'wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-content','padding'=>array('unit'=>'px','top'=>'66','right'=>'24','bottom'=>'70','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-link-grid',array('layout'=>'tiles','columns'=>'3','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>660,'wpst_entry_distance'=>18,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>85,'wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-content','background_background'=>'classic','background_color'=>'#f8fafc','padding'=>array('unit'=>'px','top'=>'62','right'=>'24','bottom'=>'62','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-footer-newsletter',array('wpst_entry_motion'=>'scale','wpst_entry_duration'=>720,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>15))),array('_css_classes'=>'wpst-page-section wpst-page-contact','padding'=>array('unit'=>'px','top'=>'58','right'=>'24','bottom'=>'82','left'=>'24','isLinked'=>false)))
            )
        );

        $pages[]=array(
            'key'=>'page-blog-detail-editorial-v1','title'=>'Blog Detay · Editorial Article',
            'desc'=>'Başlık, öne çıkan görsel, içerik blokları, alıntı, ilgili yazılar ve newsletter ile bağımsız blog detay kompozisyonu.',
            'preview_image'=>self::preview('page-blog-detail-editorial-v1.svg'),
            'category'=>'İç Sayfalar','sector'=>'Blog / Medya','premium'=>1,'quality'=>'Signature',
            'page_quality'=>'Independent Inner Page + Motion','collection'=>'Editorial Inner Pages','experience'=>'Blog Detail',
            'responsive_ready'=>true,'is_new'=>true,
            'data'=>array(
                self::container(array(self::element('wpsoft-breadcrumb',array('wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>700,'wpst_entry_distance'=>20,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12)),self::element('wpsoft-post-title',array('wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>820,'wpst_entry_distance'=>28,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>8))),array('_css_classes'=>'wpst-page-section wpst-page-hero','padding'=>array('unit'=>'px','top'=>'44','right'=>'24','bottom'=>'54','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-parallax-image',array('wpst_entry_motion'=>'reveal-left','wpst_entry_duration'=>840,'wpst_entry_distance'=>24,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-showcase','padding'=>array('unit'=>'px','top'=>'30','right'=>'24','bottom'=>'50','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-image-text',array('wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>700,'wpst_entry_distance'=>20,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12)),self::element('wpsoft-quote',array('wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>700,'wpst_entry_distance'=>20,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-content','padding'=>array('unit'=>'px','top'=>'64','right'=>'24','bottom'=>'64','left'=>'24','isLinked'=>false),'gap'=>array('unit'=>'px','size'=>30))),
                self::container(array(self::element('wpsoft-blog-posts',array('layout_style'=>'compact-news','posts_per_page'=>3,'wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>660,'wpst_entry_distance'=>18,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>85,'wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-content','background_background'=>'classic','background_color'=>'#f8fafc','padding'=>array('unit'=>'px','top'=>'64','right'=>'24','bottom'=>'64','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-footer-newsletter',array('wpst_entry_motion'=>'scale','wpst_entry_duration'=>720,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>15))),array('_css_classes'=>'wpst-page-section wpst-page-contact','padding'=>array('unit'=>'px','top'=>'56','right'=>'24','bottom'=>'80','left'=>'24','isLinked'=>false)))
            )
        );

        $pages[]=array(
            'key'=>'page-privacy-legal-v1','title'=>'Gizlilik & KVKK · Legal',
            'desc'=>'Gizlilik, KVKK, çerez veya kullanım şartları içerikleri için sade ve okunaklı bağımsız yasal sayfa.',
            'preview_image'=>self::preview('page-privacy-legal-v1.svg'),
            'category'=>'İç Sayfalar','sector'=>'Genel','premium'=>1,'quality'=>'Signature',
            'page_quality'=>'Independent Inner Page + Motion','collection'=>'Utility Inner Pages','experience'=>'Legal',
            'responsive_ready'=>true,'is_new'=>true,
            'data'=>array(
                self::container(array(self::element('wpsoft-breadcrumb',array('wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>700,'wpst_entry_distance'=>20,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12)),self::element('wpsoft-gradient-heading',array('layout'=>'display','eyebrow'=>'YASAL','title'=>'Gizlilik ve Kişisel Veriler Politikası','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>820,'wpst_entry_distance'=>28,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>8))),array('_css_classes'=>'wpst-page-section wpst-page-hero','background_background'=>'classic','background_color'=>'#f8fafc','padding'=>array('unit'=>'px','top'=>'58','right'=>'24','bottom'=>'58','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-advanced-accordion',array('layout_variant'=>'minimal','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>700,'wpst_entry_distance'=>20,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-content','padding'=>array('unit'=>'px','top'=>'66','right'=>'24','bottom'=>'66','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-quote',array('wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>700,'wpst_entry_distance'=>20,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-content','background_background'=>'classic','background_color'=>'#f8fafc','padding'=>array('unit'=>'px','top'=>'54','right'=>'24','bottom'=>'54','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-contact-cards',array('wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>660,'wpst_entry_distance'=>18,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>85,'wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-contact','padding'=>array('unit'=>'px','top'=>'58','right'=>'24','bottom'=>'76','left'=>'24','isLinked'=>false)))
            )
        );

        $pages[]=array(
            'key'=>'page-404-modern-v1','title'=>'404 · Smart Recovery',
            'desc'=>'Kayıp sayfalarda kullanıcıyı arama, hızlı bağlantılar ve ana CTA ile yönlendiren bağımsız 404 sayfası.',
            'preview_image'=>self::preview('page-404-modern-v1.svg'),
            'category'=>'İç Sayfalar','sector'=>'Genel','premium'=>1,'quality'=>'Signature',
            'page_quality'=>'Independent Inner Page + Motion','collection'=>'Utility Inner Pages','experience'=>'404',
            'responsive_ready'=>true,'is_new'=>true,
            'data'=>array(
                self::container(array(self::element('wpsoft-animated-heading',array('text'=>'404','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>820,'wpst_entry_distance'=>28,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>8)),self::element('wpsoft-gradient-heading',array('layout'=>'display','eyebrow'=>'SAYFA BULUNAMADI','title'=>'Aradığınız sayfa burada görünmüyor.','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>700,'wpst_entry_distance'=>20,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-hero','background_background'=>'classic','background_color'=>'#0b1020','padding'=>array('unit'=>'px','top'=>'96','right'=>'24','bottom'=>'72','left'=>'24','isLinked'=>false),'gap'=>array('unit'=>'px','size'=>16))),
                self::container(array(self::element('wpsoft-content-finder',array('layout'=>'compact','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>700,'wpst_entry_distance'=>20,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-content','padding'=>array('unit'=>'px','top'=>'50','right'=>'24','bottom'=>'44','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-link-grid',array('layout'=>'tiles','columns'=>'3','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>660,'wpst_entry_distance'=>18,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>85,'wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-content','background_background'=>'classic','background_color'=>'#f8fafc','padding'=>array('unit'=>'px','top'=>'58','right'=>'24','bottom'=>'64','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-advanced-button',array('text'=>'Ana Sayfaya Dön','button_url'=>array('url'=>'/'),'wpst_entry_motion'=>'scale','wpst_entry_duration'=>720,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>15))),array('_css_classes'=>'wpst-page-section wpst-page-contact','padding'=>array('unit'=>'px','top'=>'42','right'=>'24','bottom'=>'80','left'=>'24','isLinked'=>false)))
            )
        );

        $pages[]=array(
            'key'=>'page-coming-soon-v1','title'=>'Yakında · Launch Page',
            'desc'=>'Yeni site, ürün veya kampanya yayını için geri sayım, kısa mesaj, sosyal bağlantılar ve newsletter odaklı bağımsız launch sayfası.',
            'preview_image'=>self::preview('page-coming-soon-v1.svg'),
            'category'=>'İç Sayfalar','sector'=>'Genel','premium'=>1,'quality'=>'Signature',
            'page_quality'=>'Independent Inner Page + Motion','collection'=>'Utility Inner Pages','experience'=>'Coming Soon',
            'responsive_ready'=>true,'is_new'=>true,
            'data'=>array(
                self::container(array(self::element('wpsoft-gradient-heading',array('layout'=>'display','eyebrow'=>'ÇOK YAKINDA','title'=>'Yeni deneyimimizi hazırlıyoruz.','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>820,'wpst_entry_distance'=>28,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>8)),self::element('wpsoft-countdown-modern',array('wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>700,'wpst_entry_distance'=>20,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12)),self::element('wpsoft-footer-newsletter',array('wpst_entry_motion'=>'scale','wpst_entry_duration'=>720,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>15)),self::element('wpsoft-footer-social',array('wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>700,'wpst_entry_distance'=>20,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-hero','background_background'=>'classic','background_color'=>'#0b1020','padding'=>array('unit'=>'px','top'=>'96','right'=>'24','bottom'=>'96','left'=>'24','isLinked'=>false),'gap'=>array('unit'=>'px','size'=>30)))
            )
        );


        /* ======================================================
         * v3.3.16 · Independent Inner Page Templates
         * Each page has its own layout language and composition.
         * ====================================================== */

        $pages[]=array(
            'key'=>'page-about-signature-v1',
            'title'=>'Hakkımızda · Brand Story',
            'desc'=>'Kurumsal hikâye, değerler, kilometre taşları, ekip ve güven öğeleriyle bağımsız Hakkımızda sayfası.',
            'preview_image'=>self::preview('page-about-signature-v1.svg'),
            'category'=>'İç Sayfalar','sector'=>'Kurumsal','premium'=>1,'quality'=>'Signature',
            'page_quality'=>'Independent Inner Page + Motion','collection'=>'Corporate Inner Pages','experience'=>'About',
            'responsive_ready'=>true,'is_new'=>true,
            'data'=>array(
                self::container(array(
                    self::element('wpsoft-hero-split-modern',array('composition'=>'minimal','eyebrow'=>'HAKKIMIZDA','title'=>'İşimizi yalnızca yapmakla kalmıyor, anlamlı hale getiriyoruz.','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>820,'wpst_entry_distance'=>28,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>8))
                ),array('_css_classes'=>'wpst-page-section wpst-page-hero','background_background'=>'classic','background_color'=>'#fbfaf7','padding'=>array('unit'=>'px','top'=>'64','right'=>'24','bottom'=>'72','left'=>'24','isLinked'=>false))),
                self::container(array(
                    self::element('wpsoft-story-cards',array('layout'=>'editorial','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>660,'wpst_entry_distance'=>18,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>85,'wpst_entry_threshold'=>12))
                ),array('_css_classes'=>'wpst-page-section wpst-page-content','padding'=>array('unit'=>'px','top'=>'76','right'=>'24','bottom'=>'76','left'=>'24','isLinked'=>false))),
                self::container(array(
                    self::element('wpsoft-number-cards',array('wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>660,'wpst_entry_distance'=>18,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>85,'wpst_entry_threshold'=>12))
                ),array('_css_classes'=>'wpst-page-section wpst-page-content','background_background'=>'classic','background_color'=>'#f8fafc','padding'=>array('unit'=>'px','top'=>'68','right'=>'24','bottom'=>'68','left'=>'24','isLinked'=>false))),
                self::container(array(
                    self::element('wpsoft-process-steps-pro',array('layout_variant'=>'editorial','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>660,'wpst_entry_distance'=>18,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>85,'wpst_entry_threshold'=>12))
                ),array('_css_classes'=>'wpst-page-section wpst-page-content','padding'=>array('unit'=>'px','top'=>'72','right'=>'24','bottom'=>'72','left'=>'24','isLinked'=>false))),
                self::container(array(
                    self::element('wpsoft-team-carousel-pro',array('layout_variant'=>'editorial','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>700,'wpst_entry_distance'=>20,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12))
                ),array('_css_classes'=>'wpst-page-section wpst-page-content','background_background'=>'classic','background_color'=>'#f8fafc','padding'=>array('unit'=>'px','top'=>'72','right'=>'24','bottom'=>'72','left'=>'24','isLinked'=>false))),
                self::container(array(
                    self::element('wpsoft-logo-cloud',array('wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>660,'wpst_entry_distance'=>18,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>85,'wpst_entry_threshold'=>12))
                ),array('_css_classes'=>'wpst-page-section wpst-page-proof','padding'=>array('unit'=>'px','top'=>'54','right'=>'24','bottom'=>'62','left'=>'24','isLinked'=>false))),
                self::container(array(
                    self::element('wpsoft-cta',array('layout_style'=>'floating','title'=>'Birlikte değer üretelim','description'=>'Markanız için doğru dijital yaklaşımı birlikte oluşturalım.','wpst_entry_motion'=>'scale','wpst_entry_duration'=>720,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>15))
                ),array('_css_classes'=>'wpst-page-section wpst-page-contact','padding'=>array('unit'=>'px','top'=>'54','right'=>'24','bottom'=>'80','left'=>'24','isLinked'=>false)))
            )
        );

        $pages[]=array(
            'key'=>'page-services-modern-v1',
            'title'=>'Hizmetler · Expertise Index',
            'desc'=>'Hizmetlerin editoryal index, carousel, süreç, karşılaştırma ve CTA akışıyla sunulduğu bağımsız Hizmetler sayfası.',
            'preview_image'=>self::preview('page-services-modern-v1.svg'),
            'category'=>'İç Sayfalar','sector'=>'Ajans / Kurumsal','premium'=>1,'quality'=>'Signature',
            'page_quality'=>'Independent Inner Page + Motion','collection'=>'Service Inner Pages','experience'=>'Services',
            'responsive_ready'=>true,'is_new'=>true,
            'data'=>array(
                self::container(array(
                    self::element('wpsoft-gradient-heading',array('layout'=>'display','eyebrow'=>'HİZMETLER','title'=>'Uzmanlık alanlarımızı iş hedeflerinize göre birleştiriyoruz.','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>820,'wpst_entry_distance'=>28,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>8))
                ),array('_css_classes'=>'wpst-page-section wpst-page-hero','padding'=>array('unit'=>'px','top'=>'84','right'=>'24','bottom'=>'64','left'=>'24','isLinked'=>false))),
                self::container(array(
                    self::element('wpsoft-service-cards-pro',array('layout_variant'=>'editorial','card_style'=>'flat','columns'=>'1','show_badges'=>'no','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>660,'wpst_entry_distance'=>18,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>85,'wpst_entry_threshold'=>12))
                ),array('_css_classes'=>'wpst-page-section wpst-page-services','background_background'=>'classic','background_color'=>'#f8fafc','padding'=>array('unit'=>'px','top'=>'68','right'=>'24','bottom'=>'68','left'=>'24','isLinked'=>false))),
                self::container(array(
                    self::element('wpsoft-service-carousel-pro',array('style_preset'=>'media','visible'=>'3','visible_tablet'=>'2','visible_mobile'=>'1','peek'=>'yes','show_progress'=>'yes','show_drag_hint'=>'yes','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>700,'wpst_entry_distance'=>20,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12))
                ),array('_css_classes'=>'wpst-page-section wpst-page-services','padding'=>array('unit'=>'px','top'=>'72','right'=>'24','bottom'=>'76','left'=>'24','isLinked'=>false))),
                self::container(array(
                    self::element('wpsoft-process-steps-pro',array('layout_variant'=>'timeline','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>660,'wpst_entry_distance'=>18,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>85,'wpst_entry_threshold'=>12))
                ),array('_css_classes'=>'wpst-page-section wpst-page-content','background_background'=>'classic','background_color'=>'#0f172a','padding'=>array('unit'=>'px','top'=>'74','right'=>'24','bottom'=>'74','left'=>'24','isLinked'=>false))),
                self::container(array(
                    self::element('wpsoft-advanced-table',array('layout'=>'comparison','caption'=>'Hizmet Kapsamları','highlight_column'=>3,'wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>700,'wpst_entry_distance'=>20,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12))
                ),array('_css_classes'=>'wpst-page-section wpst-page-content','padding'=>array('unit'=>'px','top'=>'72','right'=>'24','bottom'=>'72','left'=>'24','isLinked'=>false))),
                self::container(array(
                    self::element('wpsoft-advanced-accordion',array('layout_variant'=>'panel','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>700,'wpst_entry_distance'=>20,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12))
                ),array('_css_classes'=>'wpst-page-section wpst-page-content','background_background'=>'classic','background_color'=>'#f8fafc','padding'=>array('unit'=>'px','top'=>'68','right'=>'24','bottom'=>'68','left'=>'24','isLinked'=>false))),
                self::container(array(
                    self::element('wpsoft-cta',array('layout_style'=>'banner','title'=>'Hangi hizmetin size uygun olduğunu birlikte belirleyelim','wpst_entry_motion'=>'scale','wpst_entry_duration'=>720,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>15))
                ),array('_css_classes'=>'wpst-page-section wpst-page-contact','padding'=>array('unit'=>'px','top'=>'52','right'=>'24','bottom'=>'78','left'=>'24','isLinked'=>false)))
            )
        );

        $pages[]=array(
            'key'=>'page-contact-modern-v1',
            'title'=>'İletişim · Contact Hub',
            'desc'=>'İletişim kartları, form, harita, çalışma saatleri ve sık sorulan sorularla bağımsız modern iletişim sayfası.',
            'preview_image'=>self::preview('page-contact-modern-v1.svg'),
            'category'=>'İç Sayfalar','sector'=>'Genel','premium'=>1,'quality'=>'Signature',
            'page_quality'=>'Independent Inner Page + Motion','collection'=>'Contact Inner Pages','experience'=>'Contact',
            'responsive_ready'=>true,'is_new'=>true,
            'data'=>array(
                self::container(array(
                    self::element('wpsoft-hero-split-modern',array('composition'=>'minimal','eyebrow'=>'İLETİŞİM','title'=>'Projenizi konuşmak için buradayız.','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>820,'wpst_entry_distance'=>28,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>8))
                ),array('_css_classes'=>'wpst-page-section wpst-page-hero','background_background'=>'classic','background_color'=>'#eef2ff','padding'=>array('unit'=>'px','top'=>'68','right'=>'24','bottom'=>'68','left'=>'24','isLinked'=>false))),
                self::container(array(
                    self::element('wpsoft-contact-cards',array('wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>660,'wpst_entry_distance'=>18,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>85,'wpst_entry_threshold'=>12))
                ),array('_css_classes'=>'wpst-page-section wpst-page-contact','padding'=>array('unit'=>'px','top'=>'58','right'=>'24','bottom'=>'62','left'=>'24','isLinked'=>false))),
                self::container(array(
                    self::element('wpsoft-form-shell',array('layout'=>'split','title'=>'Bize yazın','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>700,'wpst_entry_distance'=>20,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12))
                ),array('_css_classes'=>'wpst-page-section wpst-page-contact','background_background'=>'classic','background_color'=>'#f8fafc','padding'=>array('unit'=>'px','top'=>'70','right'=>'24','bottom'=>'70','left'=>'24','isLinked'=>false))),
                self::container(array(
                    self::element('wpsoft-location-map',array('layout'=>'split','wpst_entry_motion'=>'reveal-left','wpst_entry_duration'=>840,'wpst_entry_distance'=>24,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12))
                ),array('_css_classes'=>'wpst-page-section wpst-page-contact','padding'=>array('unit'=>'px','top'=>'64','right'=>'24','bottom'=>'64','left'=>'24','isLinked'=>false))),
                self::container(array(
                    self::element('wpsoft-info-strip',array('wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>660,'wpst_entry_distance'=>18,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>85,'wpst_entry_threshold'=>12))
                ),array('_css_classes'=>'wpst-page-section wpst-page-proof','background_background'=>'classic','background_color'=>'#0f172a','padding'=>array('unit'=>'px','top'=>'44','right'=>'24','bottom'=>'44','left'=>'24','isLinked'=>false))),
                self::container(array(
                    self::element('wpsoft-advanced-accordion',array('layout_variant'=>'minimal','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>700,'wpst_entry_distance'=>20,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12))
                ),array('_css_classes'=>'wpst-page-section wpst-page-content','padding'=>array('unit'=>'px','top'=>'66','right'=>'24','bottom'=>'78','left'=>'24','isLinked'=>false)))
            )
        );

        $pages[]=array(
            'key'=>'page-team-editorial-v1',
            'title'=>'Ekibimiz · People & Culture',
            'desc'=>'Ekip profilleri, kültür, değerler, istatistikler ve kariyer CTA alanlarıyla bağımsız ekip sayfası.',
            'preview_image'=>self::preview('page-team-editorial-v1.svg'),
            'category'=>'İç Sayfalar','sector'=>'Kurumsal / Ajans','premium'=>1,'quality'=>'Signature',
            'page_quality'=>'Independent Inner Page + Motion','collection'=>'Corporate Inner Pages','experience'=>'Team',
            'responsive_ready'=>true,'is_new'=>true,
            'data'=>array(
                self::container(array(
                    self::element('wpsoft-gradient-heading',array('layout'=>'display','eyebrow'=>'EKİBİMİZ','title'=>'İyi işler, iyi ekiplerle ortaya çıkar.','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>820,'wpst_entry_distance'=>28,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>8))
                ),array('_css_classes'=>'wpst-page-section wpst-page-hero','padding'=>array('unit'=>'px','top'=>'84','right'=>'24','bottom'=>'64','left'=>'24','isLinked'=>false))),
                self::container(array(
                    self::element('wpsoft-team-carousel-pro',array('layout_variant'=>'editorial','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>700,'wpst_entry_distance'=>20,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12))
                ),array('_css_classes'=>'wpst-page-section wpst-page-content','background_background'=>'classic','background_color'=>'#f8fafc','padding'=>array('unit'=>'px','top'=>'70','right'=>'24','bottom'=>'70','left'=>'24','isLinked'=>false))),
                self::container(array(
                    self::element('wpsoft-story-cards',array('layout'=>'horizontal','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>660,'wpst_entry_distance'=>18,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>85,'wpst_entry_threshold'=>12))
                ),array('_css_classes'=>'wpst-page-section wpst-page-content','padding'=>array('unit'=>'px','top'=>'74','right'=>'24','bottom'=>'74','left'=>'24','isLinked'=>false))),
                self::container(array(
                    self::element('wpsoft-stats-grid',array('layout_variant'=>'hero','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>660,'wpst_entry_distance'=>18,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>85,'wpst_entry_threshold'=>12))
                ),array('_css_classes'=>'wpst-page-section wpst-page-proof','background_background'=>'classic','background_color'=>'#0f172a','padding'=>array('unit'=>'px','top'=>'62','right'=>'24','bottom'=>'62','left'=>'24','isLinked'=>false))),
                self::container(array(
                    self::element('wpsoft-quote',array('wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>700,'wpst_entry_distance'=>20,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12))
                ),array('_css_classes'=>'wpst-page-section wpst-page-content','padding'=>array('unit'=>'px','top'=>'68','right'=>'24','bottom'=>'68','left'=>'24','isLinked'=>false))),
                self::container(array(
                    self::element('wpsoft-cta',array('layout_style'=>'floating','title'=>'Ekibimize katılmak ister misiniz?','description'=>'Açık pozisyonlarımızı inceleyin ve birlikte üretelim.','wpst_entry_motion'=>'scale','wpst_entry_duration'=>720,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>15))
                ),array('_css_classes'=>'wpst-page-section wpst-page-contact','background_background'=>'classic','background_color'=>'#f8fafc','padding'=>array('unit'=>'px','top'=>'58','right'=>'24','bottom'=>'80','left'=>'24','isLinked'=>false)))
            )
        );

        $pages[]=array(
            'key'=>'page-portfolio-cinematic-v1',
            'title'=>'Projeler · Cinematic Portfolio',
            'desc'=>'Öne çıkan projeler, kategoriler, before/after ve müşteri kanıtlarıyla bağımsız portfolyo/projeler sayfası.',
            'preview_image'=>self::preview('page-portfolio-cinematic-v1.svg'),
            'category'=>'İç Sayfalar','sector'=>'Ajans / Mimarlık / Creative','premium'=>1,'quality'=>'Signature',
            'page_quality'=>'Independent Inner Page + Motion','collection'=>'Creative Inner Pages','experience'=>'Portfolio',
            'responsive_ready'=>true,'is_new'=>true,
            'data'=>array(
                self::container(array(
                    self::element('wpsoft-hero-slider',array('wpst_entry_motion'=>'fade','wpst_entry_duration'=>950,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>5))
                ),array('_css_classes'=>'wpst-page-section wpst-page-hero','padding'=>array('unit'=>'px','top'=>'26','right'=>'24','bottom'=>'32','left'=>'24','isLinked'=>false))),
                self::container(array(
                    self::element('wpsoft-portfolio',array('layout_style'=>'cinematic','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>660,'wpst_entry_distance'=>18,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>85,'wpst_entry_threshold'=>12))
                ),array('_css_classes'=>'wpst-page-section wpst-page-showcase','padding'=>array('unit'=>'px','top'=>'74','right'=>'24','bottom'=>'74','left'=>'24','isLinked'=>false))),
                self::container(array(
                    self::element('wpsoft-image-reveal',array('wpst_entry_motion'=>'clip-up','wpst_entry_duration'=>900,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12))
                ),array('_css_classes'=>'wpst-page-section wpst-page-showcase','background_background'=>'classic','background_color'=>'#111827','padding'=>array('unit'=>'px','top'=>'64','right'=>'24','bottom'=>'64','left'=>'24','isLinked'=>false))),
                self::container(array(
                    self::element('wpsoft-before-after',array('wpst_entry_motion'=>'reveal-left','wpst_entry_duration'=>840,'wpst_entry_distance'=>24,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12))
                ),array('_css_classes'=>'wpst-page-section wpst-page-showcase','padding'=>array('unit'=>'px','top'=>'68','right'=>'24','bottom'=>'68','left'=>'24','isLinked'=>false))),
                self::container(array(
                    self::element('wpsoft-reviews-pro',array('layout'=>'wall','columns'=>'3','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>660,'wpst_entry_distance'=>18,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>85,'wpst_entry_threshold'=>12))
                ),array('_css_classes'=>'wpst-page-section wpst-page-proof','background_background'=>'classic','background_color'=>'#f8fafc','padding'=>array('unit'=>'px','top'=>'70','right'=>'24','bottom'=>'70','left'=>'24','isLinked'=>false))),
                self::container(array(
                    self::element('wpsoft-morphing-cta',array('eyebrow'=>'NEXT PROJECT','title'=>'Bir sonraki projeyi birlikte oluşturalım','wpst_entry_motion'=>'scale','wpst_entry_duration'=>720,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>15))
                ),array('_css_classes'=>'wpst-page-section wpst-page-contact','padding'=>array('unit'=>'px','top'=>'54','right'=>'24','bottom'=>'80','left'=>'24','isLinked'=>false)))
            )
        );

        $pages[]=array(
            'key'=>'page-faq-support-v1',
            'title'=>'SSS · Help & Answers',
            'desc'=>'Arama, kategori bağlantıları, kapsamlı accordion ve destek CTA alanlarıyla bağımsız SSS/destek sayfası.',
            'preview_image'=>self::preview('page-faq-support-v1.svg'),
            'category'=>'İç Sayfalar','sector'=>'SaaS / Kurumsal','premium'=>1,'quality'=>'Signature',
            'page_quality'=>'Independent Inner Page + Motion','collection'=>'Support Inner Pages','experience'=>'FAQ',
            'responsive_ready'=>true,'is_new'=>true,
            'data'=>array(
                self::container(array(
                    self::element('wpsoft-content-finder',array('layout'=>'hero','title'=>'Size nasıl yardımcı olabiliriz?','placeholder'=>'Sorunuzu veya konuyu arayın…','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>820,'wpst_entry_distance'=>28,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>8))
                ),array('_css_classes'=>'wpst-page-section wpst-page-hero','background_background'=>'classic','background_color'=>'#eef2ff','padding'=>array('unit'=>'px','top'=>'82','right'=>'24','bottom'=>'82','left'=>'24','isLinked'=>false))),
                self::container(array(
                    self::element('wpsoft-link-grid',array('layout'=>'tiles','columns'=>'3','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>660,'wpst_entry_distance'=>18,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>85,'wpst_entry_threshold'=>12))
                ),array('_css_classes'=>'wpst-page-section wpst-page-content','padding'=>array('unit'=>'px','top'=>'62','right'=>'24','bottom'=>'62','left'=>'24','isLinked'=>false))),
                self::container(array(
                    self::element('wpsoft-advanced-accordion',array('layout_variant'=>'panel','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>700,'wpst_entry_distance'=>20,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12))
                ),array('_css_classes'=>'wpst-page-section wpst-page-content','background_background'=>'classic','background_color'=>'#f8fafc','padding'=>array('unit'=>'px','top'=>'70','right'=>'24','bottom'=>'70','left'=>'24','isLinked'=>false))),
                self::container(array(
                    self::element('wpsoft-contact-cards',array('wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>660,'wpst_entry_distance'=>18,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>85,'wpst_entry_threshold'=>12))
                ),array('_css_classes'=>'wpst-page-section wpst-page-contact','padding'=>array('unit'=>'px','top'=>'62','right'=>'24','bottom'=>'62','left'=>'24','isLinked'=>false))),
                self::container(array(
                    self::element('wpsoft-promo-banner',array('layout'=>'minimal','title'=>'Aradığınız cevabı bulamadınız mı?','text'=>'Destek ekibimiz size yardımcı olmaya hazır.','wpst_entry_motion'=>'scale','wpst_entry_duration'=>720,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>15))
                ),array('_css_classes'=>'wpst-page-section wpst-page-contact','background_background'=>'classic','background_color'=>'#f8fafc','padding'=>array('unit'=>'px','top'=>'54','right'=>'24','bottom'=>'78','left'=>'24','isLinked'=>false)))
            )
        );

        $pages[]=array(
            'key'=>'page-pricing-plans-v1',
            'title'=>'Fiyatlandırma · Plans & Comparison',
            'desc'=>'Paketler, özellik karşılaştırması, güven göstergeleri ve SSS ile bağımsız fiyatlandırma sayfası.',
            'preview_image'=>self::preview('page-pricing-plans-v1.svg'),
            'category'=>'İç Sayfalar','sector'=>'SaaS / Hizmet','premium'=>1,'quality'=>'Signature',
            'page_quality'=>'Independent Inner Page + Motion','collection'=>'Conversion Inner Pages','experience'=>'Pricing',
            'responsive_ready'=>true,'is_new'=>true,
            'data'=>array(
                self::container(array(
                    self::element('wpsoft-gradient-heading',array('layout'=>'display','eyebrow'=>'FİYATLANDIRMA','title'=>'İhtiyacınıza uygun planı seçin.','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>820,'wpst_entry_distance'=>28,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>8))
                ),array('_css_classes'=>'wpst-page-section wpst-page-hero','padding'=>array('unit'=>'px','top'=>'82','right'=>'24','bottom'=>'60','left'=>'24','isLinked'=>false))),
                self::container(array(
                    self::element('wpsoft-pricing',array('layout_variant'=>'statement','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>660,'wpst_entry_distance'=>18,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>85,'wpst_entry_threshold'=>12))
                ),array('_css_classes'=>'wpst-page-section wpst-page-content','background_background'=>'classic','background_color'=>'#f8fafc','padding'=>array('unit'=>'px','top'=>'70','right'=>'24','bottom'=>'70','left'=>'24','isLinked'=>false))),
                self::container(array(
                    self::element('wpsoft-advanced-table',array('layout'=>'comparison','caption'=>'Paket Karşılaştırması','highlight_column'=>3,'wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>700,'wpst_entry_distance'=>20,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12))
                ),array('_css_classes'=>'wpst-page-section wpst-page-content','padding'=>array('unit'=>'px','top'=>'68','right'=>'24','bottom'=>'68','left'=>'24','isLinked'=>false))),
                self::container(array(
                    self::element('wpsoft-trust-badges',array('wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>660,'wpst_entry_distance'=>18,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>85,'wpst_entry_threshold'=>12))
                ),array('_css_classes'=>'wpst-page-section wpst-page-proof','background_background'=>'classic','background_color'=>'#0f172a','padding'=>array('unit'=>'px','top'=>'52','right'=>'24','bottom'=>'52','left'=>'24','isLinked'=>false))),
                self::container(array(
                    self::element('wpsoft-advanced-accordion',array('layout_variant'=>'minimal','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>700,'wpst_entry_distance'=>20,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12))
                ),array('_css_classes'=>'wpst-page-section wpst-page-content','padding'=>array('unit'=>'px','top'=>'66','right'=>'24','bottom'=>'70','left'=>'24','isLinked'=>false))),
                self::container(array(
                    self::element('wpsoft-cta',array('layout_style'=>'floating','title'=>'Hangi planın uygun olduğundan emin değil misiniz?','wpst_entry_motion'=>'scale','wpst_entry_duration'=>720,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>15))
                ),array('_css_classes'=>'wpst-page-section wpst-page-contact','background_background'=>'classic','background_color'=>'#f8fafc','padding'=>array('unit'=>'px','top'=>'54','right'=>'24','bottom'=>'80','left'=>'24','isLinked'=>false)))
            )
        );

        $pages[]=array(
            'key'=>'page-careers-culture-v1',
            'title'=>'Kariyer · Culture & Opportunities',
            'desc'=>'Kültür, avantajlar, açık pozisyonlar, ekip hikâyeleri ve başvuru CTA alanlarıyla bağımsız kariyer sayfası.',
            'preview_image'=>self::preview('page-careers-culture-v1.svg'),
            'category'=>'İç Sayfalar','sector'=>'Kurumsal','premium'=>1,'quality'=>'Signature',
            'page_quality'=>'Independent Inner Page + Motion','collection'=>'Corporate Inner Pages','experience'=>'Careers',
            'responsive_ready'=>true,'is_new'=>true,
            'data'=>array(
                self::container(array(
                    self::element('wpsoft-hero-split-modern',array('composition'=>'minimal','eyebrow'=>'KARİYER','title'=>'Birlikte üretmek için doğru insanları arıyoruz.','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>820,'wpst_entry_distance'=>28,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>8))
                ),array('_css_classes'=>'wpst-page-section wpst-page-hero','background_background'=>'classic','background_color'=>'#f7f5f2','padding'=>array('unit'=>'px','top'=>'68','right'=>'24','bottom'=>'72','left'=>'24','isLinked'=>false))),
                self::container(array(
                    self::element('wpsoft-feature-mosaic',array('layout_variant'=>'balanced','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>660,'wpst_entry_distance'=>18,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>85,'wpst_entry_threshold'=>12))
                ),array('_css_classes'=>'wpst-page-section wpst-page-content','padding'=>array('unit'=>'px','top'=>'74','right'=>'24','bottom'=>'74','left'=>'24','isLinked'=>false))),
                self::container(array(
                    self::element('wpsoft-stats-grid',array('layout_variant'=>'hero','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>660,'wpst_entry_distance'=>18,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>85,'wpst_entry_threshold'=>12))
                ),array('_css_classes'=>'wpst-page-section wpst-page-proof','background_background'=>'classic','background_color'=>'#0f172a','padding'=>array('unit'=>'px','top'=>'58','right'=>'24','bottom'=>'58','left'=>'24','isLinked'=>false))),
                self::container(array(
                    self::element('wpsoft-story-cards',array('layout'=>'editorial','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>660,'wpst_entry_distance'=>18,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>85,'wpst_entry_threshold'=>12))
                ),array('_css_classes'=>'wpst-page-section wpst-page-content','padding'=>array('unit'=>'px','top'=>'70','right'=>'24','bottom'=>'70','left'=>'24','isLinked'=>false))),
                self::container(array(
                    self::element('wpsoft-link-grid',array('layout'=>'tiles','columns'=>'2','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>660,'wpst_entry_distance'=>18,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>85,'wpst_entry_threshold'=>12))
                ),array('_css_classes'=>'wpst-page-section wpst-page-content','background_background'=>'classic','background_color'=>'#f8fafc','padding'=>array('unit'=>'px','top'=>'68','right'=>'24','bottom'=>'68','left'=>'24','isLinked'=>false))),
                self::container(array(
                    self::element('wpsoft-form-shell',array('layout'=>'split','title'=>'Ekibimize katılın','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>700,'wpst_entry_distance'=>20,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12))
                ),array('_css_classes'=>'wpst-page-section wpst-page-contact','padding'=>array('unit'=>'px','top'=>'64','right'=>'24','bottom'=>'80','left'=>'24','isLinked'=>false)))
            )
        );


        $pages[]=array(
            'key'=>'page-digital-agency-v13','title'=>'Digital Agency · Motion Signature',
            'desc'=>'Yaratıcı ajanslar için bento hero, modern hizmet carousel, proje, ekip, sosyal kanıt ve CTA akışına sahip animasyonlu premium tam sayfa.',
            'preview_image'=>self::preview('page-digital-agency-v13.svg'),
            'category'=>'Full Pages','sector'=>'Ajans','premium'=>1,'quality'=>'Signature','page_quality'=>'Full Page Quality 3.0 + Motion','collection'=>'Creative Pages','experience'=>'Full Page','responsive_ready'=>true,'is_new'=>true,
            'data'=>array(
                self::container(array(self::element('wpsoft-hero-bento',array('hero_radius'=>array('size'=>30),'wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>850,'wpst_entry_distance'=>34,'wpst_entry_easing'=>'smooth','wpst_motion_disable_mobile'=>'','wpst_entry_threshold'=>8))),array('_css_classes'=>'wpst-page-section wpst-page-hero','padding'=>array('unit'=>'px','top'=>'36','right'=>'24','bottom'=>'42','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-service-carousel-pro',array('style_preset'=>'media','visible'=>'3','visible_tablet'=>'2','visible_mobile'=>'1','peek'=>'yes','show_arrows'=>'yes','show_progress'=>'yes','show_drag_hint'=>'yes','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>680,'wpst_entry_distance'=>20,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>90,'wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-services','background_background'=>'classic','background_color'=>'#f8fafc','padding'=>array('unit'=>'px','top'=>'76','right'=>'24','bottom'=>'82','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-portfolio',array('layout_style'=>'cinematic','wpst_entry_motion'=>'reveal-left','wpst_entry_duration'=>850,'wpst_entry_distance'=>28,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-showcase','background_background'=>'classic','background_color'=>'#0b1020','padding'=>array('unit'=>'px','top'=>'72','right'=>'24','bottom'=>'72','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-team-carousel-pro',array('layout_variant'=>'strip','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>720,'wpst_entry_distance'=>24,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-content','padding'=>array('unit'=>'px','top'=>'76','right'=>'24','bottom'=>'76','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-reviews-pro',array('layout'=>'wall','columns'=>'3','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>680,'wpst_entry_distance'=>20,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>90,'wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-proof','background_background'=>'classic','background_color'=>'#f8fafc','padding'=>array('unit'=>'px','top'=>'76','right'=>'24','bottom'=>'76','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-cta',array('layout_style'=>'banner','title'=>'Bir sonraki dijital deneyimi birlikte tasarlayalım','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>720,'wpst_entry_distance'=>24,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-contact','padding'=>array('unit'=>'px','top'=>'54','right'=>'24','bottom'=>'78','left'=>'24','isLinked'=>false)))
            )
        );

        $pages[]=array(
            'key'=>'page-saas-product-v13','title'=>'SaaS Product · Motion Modern',
            'desc'=>'SaaS ürünleri için güçlü ürün hero, özellik mosaic, tabs, metrikler, pricing, FAQ ve dönüşüm CTA akışı; Motion System hazır.',
            'preview_image'=>self::preview('page-saas-product-v13.svg'),
            'category'=>'Full Pages','sector'=>'SaaS','premium'=>1,'quality'=>'Signature','page_quality'=>'Full Page Quality 3.0 + Motion','collection'=>'Technology Pages','experience'=>'Full Page','responsive_ready'=>true,'is_new'=>true,
            'data'=>array(
                self::container(array(self::element('wpsoft-hero-saas',array('wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>850,'wpst_entry_distance'=>34,'wpst_entry_easing'=>'smooth','wpst_motion_disable_mobile'=>'','wpst_entry_threshold'=>8))),array('_css_classes'=>'wpst-page-section wpst-page-hero','padding'=>array('unit'=>'px','top'=>'38','right'=>'24','bottom'=>'44','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-feature-mosaic',array('layout_variant'=>'balanced','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>680,'wpst_entry_distance'=>20,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>90,'wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-content','padding'=>array('unit'=>'px','top'=>'78','right'=>'24','bottom'=>'78','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-tabs-modern',array('layout_variant'=>'segmented','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>720,'wpst_entry_distance'=>24,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-content','background_background'=>'classic','background_color'=>'#f8fafc','padding'=>array('unit'=>'px','top'=>'68','right'=>'24','bottom'=>'68','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-stats-grid',array('layout_variant'=>'hero','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>680,'wpst_entry_distance'=>20,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>90,'wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-proof','padding'=>array('unit'=>'px','top'=>'68','right'=>'24','bottom'=>'68','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-pricing',array('layout_variant'=>'statement','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>680,'wpst_entry_distance'=>20,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>90,'wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-content','background_background'=>'classic','background_color'=>'#f8fafc','padding'=>array('unit'=>'px','top'=>'78','right'=>'24','bottom'=>'78','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-advanced-accordion',array('layout_variant'=>'panel','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>720,'wpst_entry_distance'=>24,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-content','padding'=>array('unit'=>'px','top'=>'68','right'=>'24','bottom'=>'68','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-promo-banner',array('layout'=>'minimal','title'=>'Ürününüzü daha hızlı büyütmeye hazır mısınız?','wpst_entry_motion'=>'scale','wpst_entry_duration'=>760,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>15))),array('_css_classes'=>'wpst-page-section wpst-page-contact','padding'=>array('unit'=>'px','top'=>'52','right'=>'24','bottom'=>'78','left'=>'24','isLinked'=>false)))
            )
        );

        $pages[]=array(
            'key'=>'page-architecture-studio-v13','title'=>'Architecture Studio · Motion Editorial',
            'desc'=>'Mimarlık ve tasarım stüdyoları için editorial hero, proje index, servis yaklaşımı, story, ekip, galeri ve iletişim akışı.',
            'preview_image'=>self::preview('page-architecture-studio-v13.svg'),
            'category'=>'Full Pages','sector'=>'Mimarlık','premium'=>1,'quality'=>'Signature','page_quality'=>'Full Page Quality 3.0 + Motion','collection'=>'Creative Pages','experience'=>'Full Page','responsive_ready'=>true,'is_new'=>true,
            'data'=>array(
                self::container(array(self::element('wpsoft-hero-split-modern',array('composition'=>'minimal','wpst_entry_motion'=>'reveal-left','wpst_entry_duration'=>920,'wpst_entry_distance'=>34,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>8))),array('_css_classes'=>'wpst-page-section wpst-page-hero','background_background'=>'classic','background_color'=>'#fbfaf7','padding'=>array('unit'=>'px','top'=>'66','right'=>'24','bottom'=>'66','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-portfolio',array('layout_style'=>'index','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>680,'wpst_entry_distance'=>20,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>90,'wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-content','padding'=>array('unit'=>'px','top'=>'78','right'=>'24','bottom'=>'78','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-service-carousel-pro',array('style_preset'=>'numbered','visible'=>'3','visible_tablet'=>'2','visible_mobile'=>'1','peek'=>'yes','show_progress'=>'yes','show_drag_hint'=>'yes','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>720,'wpst_entry_distance'=>24,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-services','background_background'=>'classic','background_color'=>'#f5f3ef','padding'=>array('unit'=>'px','top'=>'70','right'=>'24','bottom'=>'76','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-story-cards',array('layout'=>'horizontal','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>680,'wpst_entry_distance'=>20,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>90,'wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-content','background_background'=>'classic','background_color'=>'#f8f7f4','padding'=>array('unit'=>'px','top'=>'74','right'=>'24','bottom'=>'74','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-team-carousel-pro',array('layout_variant'=>'editorial','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>720,'wpst_entry_distance'=>24,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-content','padding'=>array('unit'=>'px','top'=>'72','right'=>'24','bottom'=>'72','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-gallery-zoom-pro',array('layout'=>'collage','wpst_entry_motion'=>'clip-up','wpst_entry_duration'=>900,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>15))),array('_css_classes'=>'wpst-page-section wpst-page-showcase','background_background'=>'classic','background_color'=>'#111827','padding'=>array('unit'=>'px','top'=>'62','right'=>'24','bottom'=>'62','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-form-shell',array('layout'=>'minimal','title'=>'Bir sonraki yapıyı birlikte tasarlayalım','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>720,'wpst_entry_distance'=>24,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-contact','padding'=>array('unit'=>'px','top'=>'66','right'=>'24','bottom'=>'78','left'=>'24','isLinked'=>false)))
            )
        );

        $pages[]=array(
            'key'=>'page-luxury-hotel-v13','title'=>'Luxury Hotel · Motion Signature',
            'desc'=>'Otel ve resort siteleri için hospitality hero, rezervasyon, hikâye, galeri, yorum, konum ve CTA akışı; premium motion presetleriyle.',
            'preview_image'=>self::preview('page-luxury-hotel-v13.svg'),
            'category'=>'Full Pages','sector'=>'Otel','premium'=>1,'quality'=>'Signature','page_quality'=>'Full Page Quality 3.0 + Motion','collection'=>'Hospitality Pages','experience'=>'Full Page','responsive_ready'=>true,'is_new'=>true,
            'data'=>array(
                self::container(array(self::element('wpsoft-hero-hospitality',array('layout_variant'=>'editorial','wpst_entry_motion'=>'fade','wpst_entry_duration'=>1000,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>5))),array('_css_classes'=>'wpst-page-section wpst-page-hero','padding'=>array('unit'=>'px','top'=>'28','right'=>'24','bottom'=>'28','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-booking-strip',array('wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>720,'wpst_entry_distance'=>24,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-booking','padding'=>array('unit'=>'px','top'=>'30','right'=>'24','bottom'=>'38','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-story-cards',array('layout'=>'editorial','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>680,'wpst_entry_distance'=>20,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>90,'wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-content','background_background'=>'classic','background_color'=>'#fbfaf7','padding'=>array('unit'=>'px','top'=>'78','right'=>'24','bottom'=>'78','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-gallery-zoom-pro',array('layout'=>'featured','wpst_entry_motion'=>'clip-up','wpst_entry_duration'=>920,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>14))),array('_css_classes'=>'wpst-page-section wpst-page-showcase','padding'=>array('unit'=>'px','top'=>'62','right'=>'24','bottom'=>'62','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-reviews-pro',array('layout'=>'featured','columns'=>'3','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>680,'wpst_entry_distance'=>20,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>90,'wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-proof','background_background'=>'classic','background_color'=>'#f8fafc','padding'=>array('unit'=>'px','top'=>'74','right'=>'24','bottom'=>'74','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-location-map',array('layout'=>'overlay','wpst_entry_motion'=>'reveal-right','wpst_entry_duration'=>820,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>15))),array('_css_classes'=>'wpst-page-section wpst-page-contact','padding'=>array('unit'=>'px','top'=>'50','right'=>'24','bottom'=>'50','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-cta',array('layout_style'=>'floating','title'=>'Konaklamanızı planlayın','wpst_entry_motion'=>'scale','wpst_entry_duration'=>740,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>15))),array('_css_classes'=>'wpst-page-section wpst-page-contact','padding'=>array('unit'=>'px','top'=>'50','right'=>'24','bottom'=>'76','left'=>'24','isLinked'=>false)))
            )
        );

        $pages[]=array(
            'key'=>'page-expert-consultant-v11','title'=>'Expert Consultant · Motion Signature',
            'desc'=>'Danışman, doktor, avukat ve kişisel markalar için uzmanlık, hizmet, sosyal kanıt, içerik ve iletişim akışı.',
            'preview_image'=>self::preview('page-expert-consultant-v11.svg'),
            'category'=>'Full Pages','sector'=>'Uzman / Danışman','premium'=>1,'quality'=>'Signature','page_quality'=>'Full Page Quality 3.0 + Motion','collection'=>'Professional Pages','experience'=>'Full Page','responsive_ready'=>true,'is_new'=>true,
            'data'=>array(
                self::container(array(self::element('wpsoft-hero-split-modern',array('composition'=>'minimal','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>850,'wpst_entry_distance'=>34,'wpst_entry_easing'=>'smooth','wpst_motion_disable_mobile'=>'','wpst_entry_threshold'=>8))),array('_css_classes'=>'wpst-page-section wpst-page-hero','padding'=>array('unit'=>'px','top'=>'44','right'=>'24','bottom'=>'46','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-expert-profile',array('layout'=>'editorial','wpst_entry_motion'=>'reveal-left','wpst_entry_duration'=>850,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-content','background_background'=>'classic','background_color'=>'#fbfaf7','padding'=>array('unit'=>'px','top'=>'76','right'=>'24','bottom'=>'76','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-service-carousel-pro',array('style_preset'=>'numbered','visible'=>'3','visible_tablet'=>'2','visible_mobile'=>'1','peek'=>'yes','show_progress'=>'yes','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>720,'wpst_entry_distance'=>24,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-services','padding'=>array('unit'=>'px','top'=>'68','right'=>'24','bottom'=>'72','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-reviews-pro',array('layout'=>'featured','columns'=>'3','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>680,'wpst_entry_distance'=>20,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>90,'wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-proof','padding'=>array('unit'=>'px','top'=>'72','right'=>'24','bottom'=>'72','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-blog-posts',array('layout_style'=>'editorial-feed','posts_per_page'=>3,'wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>680,'wpst_entry_distance'=>20,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>90,'wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-content','background_background'=>'classic','background_color'=>'#f8fafc','padding'=>array('unit'=>'px','top'=>'72','right'=>'24','bottom'=>'72','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-form-shell',array('layout'=>'split','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>720,'wpst_entry_distance'=>24,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-contact','padding'=>array('unit'=>'px','top'=>'66','right'=>'24','bottom'=>'78','left'=>'24','isLinked'=>false)))
            )
        );

        $pages[]=array(
            'key'=>'page-help-center-v11','title'=>'Help Center · Motion Content Hub',
            'desc'=>'SaaS ve destek siteleri için arama, hızlı bağlantılar, SSS, kaynaklar ve destek CTA akışı; kontrollü motion ile.',
            'preview_image'=>self::preview('page-help-center-v11.svg'),
            'category'=>'Full Pages','sector'=>'SaaS / Support','premium'=>1,'quality'=>'Signature','page_quality'=>'Full Page Quality 3.0 + Motion','collection'=>'Content Pages','experience'=>'Full Page','responsive_ready'=>true,'is_new'=>true,
            'data'=>array(
                self::container(array(self::element('wpsoft-content-finder',array('layout'=>'hero','title'=>'Size nasıl yardımcı olabiliriz?','placeholder'=>'Yardım makalesi, özellik veya konu ara…','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>760,'wpst_entry_distance'=>20,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>5))),array('_css_classes'=>'wpst-page-section wpst-page-hero','background_background'=>'classic','background_color'=>'#eef2ff','padding'=>array('unit'=>'px','top'=>'88','right'=>'24','bottom'=>'88','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-link-grid',array('layout'=>'tiles','columns'=>'3','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>680,'wpst_entry_distance'=>20,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>90,'wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-content','padding'=>array('unit'=>'px','top'=>'68','right'=>'24','bottom'=>'68','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-advanced-accordion',array('layout_variant'=>'panel','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>720,'wpst_entry_distance'=>24,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-content','background_background'=>'classic','background_color'=>'#f8fafc','padding'=>array('unit'=>'px','top'=>'72','right'=>'24','bottom'=>'72','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-blog-posts',array('layout_style'=>'compact-news','posts_per_page'=>6,'wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>680,'wpst_entry_distance'=>20,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>90,'wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-content','padding'=>array('unit'=>'px','top'=>'72','right'=>'24','bottom'=>'72','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-promo-banner',array('layout'=>'minimal','title'=>'Cevabı bulamadınız mı?','text'=>'Destek ekibimiz size yardımcı olmaya hazır.','wpst_entry_motion'=>'scale','wpst_entry_duration'=>700,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>15))),array('_css_classes'=>'wpst-page-section wpst-page-contact','padding'=>array('unit'=>'px','top'=>'52','right'=>'24','bottom'=>'78','left'=>'24','isLinked'=>false)))
            )
        );

        $pages[]=array(
            'key'=>'page-restaurant-signature-v10','title'=>'Restaurant · Motion Signature',
            'desc'=>'Modern restoran ve bistro için güçlü hospitality hero, hikâye, menü, galeri, yorum, konum ve rezervasyon akışı.',
            'preview_image'=>self::preview('page-restaurant-signature-v10.svg'),
            'category'=>'Full Pages','sector'=>'Restoran','premium'=>1,'quality'=>'Signature','page_quality'=>'Full Page Quality 3.0 + Motion','collection'=>'Business Pages','experience'=>'Full Page','responsive_ready'=>true,'is_new'=>true,
            'data'=>array(
                self::container(array(self::element('wpsoft-hero-hospitality',array('layout_variant'=>'cinematic','wpst_entry_motion'=>'fade','wpst_entry_duration'=>1000,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>5))),array('_css_classes'=>'wpst-page-section wpst-page-hero','padding'=>array('unit'=>'px','top'=>'30','right'=>'24','bottom'=>'30','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-booking-strip',array('wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>720,'wpst_entry_distance'=>24,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-booking','padding'=>array('unit'=>'px','top'=>'28','right'=>'24','bottom'=>'36','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-story-cards',array('layout'=>'editorial','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>680,'wpst_entry_distance'=>20,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>90,'wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-content','background_background'=>'classic','background_color'=>'#fbfaf7','padding'=>array('unit'=>'px','top'=>'76','right'=>'24','bottom'=>'76','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-price-list',array('layout'=>'editorial','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>680,'wpst_entry_distance'=>20,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>90,'wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-content','padding'=>array('unit'=>'px','top'=>'74','right'=>'24','bottom'=>'74','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-gallery-zoom-pro',array('layout'=>'editorial','wpst_entry_motion'=>'clip-up','wpst_entry_duration'=>900,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>15))),array('_css_classes'=>'wpst-page-section wpst-page-showcase','background_background'=>'classic','background_color'=>'#111827','padding'=>array('unit'=>'px','top'=>'66','right'=>'24','bottom'=>'66','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-reviews-pro',array('layout'=>'featured','columns'=>'3','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>680,'wpst_entry_distance'=>20,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>90,'wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-proof','padding'=>array('unit'=>'px','top'=>'74','right'=>'24','bottom'=>'74','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-location-map',array('layout'=>'overlay','wpst_entry_motion'=>'reveal-right','wpst_entry_duration'=>820,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>15))),array('_css_classes'=>'wpst-page-section wpst-page-contact','padding'=>array('unit'=>'px','top'=>'44','right'=>'24','bottom'=>'74','left'=>'24','isLinked'=>false)))
            )
        );

        $pages[]=array(
            'key'=>'page-consulting-signature-v10','title'=>'Consulting · Motion Advisory',
            'desc'=>'Danışmanlık ve profesyonel hizmet şirketleri için hero, hizmet carousel, süreç, karşılaştırma, kanıt ve CTA sayfası.',
            'preview_image'=>self::preview('page-consulting-signature-v10.svg'),
            'category'=>'Full Pages','sector'=>'Danışmanlık','premium'=>1,'quality'=>'Signature','page_quality'=>'Full Page Quality 3.0 + Motion','collection'=>'Business Pages','experience'=>'Full Page','responsive_ready'=>true,'is_new'=>true,
            'data'=>array(
                self::container(array(self::element('wpsoft-hero-split-modern',array('composition'=>'minimal','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>850,'wpst_entry_distance'=>34,'wpst_entry_easing'=>'smooth','wpst_motion_disable_mobile'=>'','wpst_entry_threshold'=>8))),array('_css_classes'=>'wpst-page-section wpst-page-hero','padding'=>array('unit'=>'px','top'=>'42','right'=>'24','bottom'=>'44','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-service-carousel-pro',array('style_preset'=>'numbered','visible'=>'3','visible_tablet'=>'2','visible_mobile'=>'1','peek'=>'yes','show_progress'=>'yes','show_drag_hint'=>'yes','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>720,'wpst_entry_distance'=>24,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-services','padding'=>array('unit'=>'px','top'=>'66','right'=>'24','bottom'=>'72','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-process-steps-pro',array('layout_variant'=>'editorial','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>680,'wpst_entry_distance'=>20,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>90,'wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-content','background_background'=>'classic','background_color'=>'#f8fafc','padding'=>array('unit'=>'px','top'=>'76','right'=>'24','bottom'=>'76','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-advanced-table',array('layout'=>'comparison','caption'=>'Hizmet Kapsamı','highlight_column'=>3,'wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>760,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>15))),array('_css_classes'=>'wpst-page-section wpst-page-content','padding'=>array('unit'=>'px','top'=>'74','right'=>'24','bottom'=>'74','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-reviews-pro',array('layout'=>'wall','columns'=>'3','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>680,'wpst_entry_distance'=>20,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>90,'wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-proof','background_background'=>'classic','background_color'=>'#f7f8fb','padding'=>array('unit'=>'px','top'=>'74','right'=>'24','bottom'=>'74','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-cta',array('layout_style'=>'floating','title'=>'Bir sonraki kararı birlikte netleştirelim','wpst_entry_motion'=>'scale','wpst_entry_duration'=>720,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>15))),array('_css_classes'=>'wpst-page-section wpst-page-contact','padding'=>array('unit'=>'px','top'=>'60','right'=>'24','bottom'=>'78','left'=>'24','isLinked'=>false)))
            )
        );

        $pages[]=array(
            'key'=>'page-local-service-v10','title'=>'Local Service · Motion Conversion',
            'desc'=>'Klinik, servis, ofis ve yerel işletmeler için güven, modern hizmet grid, fiyat, yorum, harita ve iletişim odaklı sayfa.',
            'preview_image'=>self::preview('page-local-service-v10.svg'),
            'category'=>'Full Pages','sector'=>'Yerel İşletme','premium'=>1,'quality'=>'Signature','page_quality'=>'Full Page Quality 3.0 + Motion','collection'=>'Business Pages','experience'=>'Full Page','responsive_ready'=>true,'is_new'=>true,
            'data'=>array(
                self::container(array(self::element('wpsoft-hero-medical',array('hero_radius'=>array('size'=>30),'wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>850,'wpst_entry_distance'=>34,'wpst_entry_easing'=>'smooth','wpst_motion_disable_mobile'=>'','wpst_entry_threshold'=>8))),array('_css_classes'=>'wpst-page-section wpst-page-hero','padding'=>array('unit'=>'px','top'=>'34','right'=>'24','bottom'=>'40','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-service-cards-pro',array('layout_variant'=>'modern','card_style'=>'soft','columns'=>'3','columns_tablet'=>'2','columns_mobile'=>'2','show_images'=>'no','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>680,'wpst_entry_distance'=>20,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>90,'wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-services','padding'=>array('unit'=>'px','top'=>'68','right'=>'24','bottom'=>'72','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-price-list',array('layout'=>'cards','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>680,'wpst_entry_distance'=>20,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>90,'wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-content','background_background'=>'classic','background_color'=>'#f8fafc','padding'=>array('unit'=>'px','top'=>'68','right'=>'24','bottom'=>'68','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-reviews-pro',array('layout'=>'featured','columns'=>'3','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>680,'wpst_entry_distance'=>20,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>90,'wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-proof','padding'=>array('unit'=>'px','top'=>'74','right'=>'24','bottom'=>'74','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-location-map',array('layout'=>'split','wpst_entry_motion'=>'reveal-right','wpst_entry_duration'=>820,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>15))),array('_css_classes'=>'wpst-page-section wpst-page-contact','padding'=>array('unit'=>'px','top'=>'50','right'=>'24','bottom'=>'50','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-contact-cards',array('wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>680,'wpst_entry_distance'=>20,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>90,'wpst_entry_threshold'=>12)),self::element('wpsoft-wpforms',array('wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>720,'wpst_entry_distance'=>24,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-contact','background_background'=>'classic','background_color'=>'#f7f8fb','padding'=>array('unit'=>'px','top'=>'74','right'=>'24','bottom'=>'78','left'=>'24','isLinked'=>false)))
            )
        );

        $pages[]=array(
            'key'=>'widget-creative-studio-v1',
            'title'=>'Creative Studio · Immersive Portfolio',
            'desc'=>'Yaratıcı stüdyo ve dijital ajanslar için güçlü hero, hareketli manifesto, proje vitrini, hizmet carousel, ekip ve dönüşüm CTA akışı.',
            'preview_image'=>self::preview('widget-creative-studio-v1.svg'),
            'category'=>'Full Pages','sector'=>'Creative Studio / Ajans','premium'=>1,'quality'=>'Signature',
            'page_quality'=>'Full Page Quality 3.0 + Motion','collection'=>'Creative Pages','experience'=>'Full Page',
            'responsive_ready'=>true,'is_new'=>true,
            'data'=>array(
                self::container(array(self::element('wpsoft-hero-bento',array('hero_radius'=>array('size'=>30),'wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>860,'wpst_entry_distance'=>32,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>8))),array('_css_classes'=>'wpst-page-section wpst-page-hero','padding'=>array('unit'=>'px','top'=>'36','right'=>'24','bottom'=>'42','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-animated-heading',array('text'=>'Ideas into digital experiences','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>720,'wpst_entry_distance'=>22,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12)),self::element('wpsoft-marquee-text',array('wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>720,'wpst_entry_distance'=>22,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-content','background_background'=>'classic','background_color'=>'#0b1020','padding'=>array('unit'=>'px','top'=>'58','right'=>'24','bottom'=>'58','left'=>'24','isLinked'=>false),'gap'=>array('unit'=>'px','size'=>20))),
                self::container(array(self::element('wpsoft-hover-reveal',array('layout_variant'=>'editorial','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>680,'wpst_entry_distance'=>18,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>85,'wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-showcase','padding'=>array('unit'=>'px','top'=>'76','right'=>'24','bottom'=>'76','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-service-carousel-pro',array('style_preset'=>'numbered','visible'=>'3','visible_tablet'=>'2','visible_mobile'=>'1','peek'=>'yes','show_progress'=>'yes','show_drag_hint'=>'yes','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>720,'wpst_entry_distance'=>22,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-services','background_background'=>'classic','background_color'=>'#f8fafc','padding'=>array('unit'=>'px','top'=>'72','right'=>'24','bottom'=>'78','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-team-carousel-pro',array('layout_variant'=>'editorial','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>720,'wpst_entry_distance'=>22,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-content','padding'=>array('unit'=>'px','top'=>'72','right'=>'24','bottom'=>'72','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-morphing-cta',array('eyebrow'=>'NEXT PROJECT','title'=>'Fikrinizi güçlü bir dijital deneyime dönüştürelim','button_text'=>'Projeyi Başlat','button_url'=>array('url'=>'#iletisim'),'wpst_entry_motion'=>'scale','wpst_entry_duration'=>720,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>15))),array('_css_classes'=>'wpst-page-section wpst-page-contact','padding'=>array('unit'=>'px','top'=>'56','right'=>'24','bottom'=>'82','left'=>'24','isLinked'=>false)))
            )
        );

        $pages[]=array(
            'key'=>'widget-corporate-system-v1',
            'title'=>'Corporate Group · Executive',
            'desc'=>'Holding, kurumsal grup ve B2B şirketleri için executive hero, güven, hizmetler, süreç, metrikler, referanslar ve iletişim akışı.',
            'preview_image'=>self::preview('widget-corporate-system-v1.svg'),
            'category'=>'Full Pages','sector'=>'Kurumsal / Holding','premium'=>1,'quality'=>'Signature',
            'page_quality'=>'Full Page Quality 3.0 + Motion','collection'=>'Corporate Pages','experience'=>'Full Page',
            'responsive_ready'=>true,'is_new'=>true,
            'data'=>array(
                self::container(array(self::element('wpsoft-hero-split-modern',array('composition'=>'minimal','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>860,'wpst_entry_distance'=>32,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>8))),array('_css_classes'=>'wpst-page-section wpst-page-hero','padding'=>array('unit'=>'px','top'=>'42','right'=>'24','bottom'=>'46','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-logo-marquee',array('wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>720,'wpst_entry_distance'=>22,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-proof','padding'=>array('unit'=>'px','top'=>'30','right'=>'24','bottom'=>'40','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-service-cards-pro',array('layout_variant'=>'editorial','card_style'=>'flat','columns'=>'1','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>680,'wpst_entry_distance'=>18,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>85,'wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-services','background_background'=>'classic','background_color'=>'#f8fafc','padding'=>array('unit'=>'px','top'=>'72','right'=>'24','bottom'=>'72','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-process-steps-pro',array('layout_variant'=>'editorial','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>680,'wpst_entry_distance'=>18,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>85,'wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-content','padding'=>array('unit'=>'px','top'=>'74','right'=>'24','bottom'=>'74','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-stats-grid',array('layout_variant'=>'hero','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>680,'wpst_entry_distance'=>18,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>85,'wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-proof','background_background'=>'classic','background_color'=>'#0f172a','padding'=>array('unit'=>'px','top'=>'64','right'=>'24','bottom'=>'64','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-testimonial-slider',array('wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>720,'wpst_entry_distance'=>22,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-proof','padding'=>array('unit'=>'px','top'=>'70','right'=>'24','bottom'=>'70','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-cta',array('layout_style'=>'floating','title'=>'Bir sonraki büyüme adımını birlikte planlayalım','description'=>'Kurumsal hedeflerinize uygun dijital sistemi birlikte oluşturalım.','button_text'=>'Görüşme Planla','button_url'=>array('url'=>'#iletisim'),'wpst_entry_motion'=>'scale','wpst_entry_duration'=>720,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>15))),array('_css_classes'=>'wpst-page-section wpst-page-contact','background_background'=>'classic','background_color'=>'#f8fafc','padding'=>array('unit'=>'px','top'=>'58','right'=>'24','bottom'=>'80','left'=>'24','isLinked'=>false)))
            )
        );

        $pages[]=array(
            'key'=>'widget-commerce-conversion-v1',
            'title'=>'Modern Storefront · Conversion',
            'desc'=>'DTC marka ve modern mağazalar için commerce hero, ürün vitrini, keşif, sosyal kanıt, kampanya ve dönüşüm odaklı tam sayfa.',
            'preview_image'=>self::preview('widget-commerce-conversion-v1.svg'),
            'category'=>'Full Pages','sector'=>'E-Ticaret','premium'=>1,'quality'=>'Signature',
            'page_quality'=>'Full Page Quality 3.0 + Motion','collection'=>'Commerce Pages','experience'=>'Full Page',
            'responsive_ready'=>true,'is_new'=>true,
            'data'=>array(
                self::container(array(self::element('wpsoft-hero-commerce',array('layout'=>'product-focus','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>860,'wpst_entry_distance'=>32,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>8))),array('_css_classes'=>'wpst-page-section wpst-page-hero','background_background'=>'classic','background_color'=>'#f7f5f2','padding'=>array('unit'=>'px','top'=>'28','right'=>'24','bottom'=>'34','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-product-showcase',array('wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>680,'wpst_entry_distance'=>18,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>85,'wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-content','padding'=>array('unit'=>'px','top'=>'72','right'=>'24','bottom'=>'72','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-image-hotspots',array('wpst_entry_motion'=>'reveal-left','wpst_entry_duration'=>860,'wpst_entry_distance'=>26,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-showcase','background_background'=>'classic','background_color'=>'#f8fafc','padding'=>array('unit'=>'px','top'=>'68','right'=>'24','bottom'=>'68','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-badge-grid',array('wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>680,'wpst_entry_distance'=>18,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>85,'wpst_entry_threshold'=>12)),self::element('wpsoft-trust-badges',array('wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>680,'wpst_entry_distance'=>18,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>85,'wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-proof','padding'=>array('unit'=>'px','top'=>'62','right'=>'24','bottom'=>'62','left'=>'24','isLinked'=>false),'gap'=>array('unit'=>'px','size'=>26))),
                self::container(array(self::element('wpsoft-countdown-modern',array('wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>720,'wpst_entry_distance'=>22,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12)),self::element('wpsoft-promo-banner',array('layout'=>'editorial','title'=>'Sınırlı süreli yeni sezon avantajı','wpst_entry_motion'=>'scale','wpst_entry_duration'=>720,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>15))),array('_css_classes'=>'wpst-page-section wpst-page-content','background_background'=>'classic','background_color'=>'#111827','padding'=>array('unit'=>'px','top'=>'68','right'=>'24','bottom'=>'72','left'=>'24','isLinked'=>false),'gap'=>array('unit'=>'px','size'=>24))),
                self::container(array(self::element('wpsoft-reviews-pro',array('layout'=>'featured','columns'=>'3','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>680,'wpst_entry_distance'=>18,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>85,'wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-proof','padding'=>array('unit'=>'px','top'=>'72','right'=>'24','bottom'=>'78','left'=>'24','isLinked'=>false)))
            )
        );

        $pages[]=array(
            'key'=>'widget-hospitality-media-v1',
            'title'=>'Boutique Resort · Storytelling',
            'desc'=>'Boutique otel ve resort markaları için sinematik hero, rezervasyon, hikâye, galeri/video, yorum ve iletişim odaklı premium tam sayfa.',
            'preview_image'=>self::preview('widget-hospitality-media-v1.svg'),
            'category'=>'Full Pages','sector'=>'Otel / Resort','premium'=>1,'quality'=>'Signature',
            'page_quality'=>'Full Page Quality 3.0 + Motion','collection'=>'Hospitality Pages','experience'=>'Full Page',
            'responsive_ready'=>true,'is_new'=>true,
            'data'=>array(
                self::container(array(self::element('wpsoft-hero-hospitality',array('layout_variant'=>'cinematic','wpst_entry_motion'=>'fade','wpst_entry_duration'=>1050,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>5))),array('_css_classes'=>'wpst-page-section wpst-page-hero','padding'=>array('unit'=>'px','top'=>'26','right'=>'24','bottom'=>'26','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-booking-strip',array('wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>720,'wpst_entry_distance'=>22,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-booking','padding'=>array('unit'=>'px','top'=>'28','right'=>'24','bottom'=>'34','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-story-cards',array('layout'=>'editorial','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>680,'wpst_entry_distance'=>18,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>85,'wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-content','background_background'=>'classic','background_color'=>'#fbfaf7','padding'=>array('unit'=>'px','top'=>'76','right'=>'24','bottom'=>'76','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-gallery-zoom-pro',array('layout'=>'featured','wpst_entry_motion'=>'clip-up','wpst_entry_duration'=>920,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>14)),self::element('wpsoft-video-popup-pro',array('wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>720,'wpst_entry_distance'=>22,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-showcase','padding'=>array('unit'=>'px','top'=>'62','right'=>'24','bottom'=>'68','left'=>'24','isLinked'=>false),'gap'=>array('unit'=>'px','size'=>26))),
                self::container(array(self::element('wpsoft-reviews-pro',array('layout'=>'featured','columns'=>'3','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>680,'wpst_entry_distance'=>18,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>85,'wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-proof','background_background'=>'classic','background_color'=>'#f8fafc','padding'=>array('unit'=>'px','top'=>'72','right'=>'24','bottom'=>'72','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-form-shell',array('layout'=>'split','title'=>'Konaklamanızı planlayalım','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>720,'wpst_entry_distance'=>22,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-contact','padding'=>array('unit'=>'px','top'=>'62','right'=>'24','bottom'=>'80','left'=>'24','isLinked'=>false)))
            )
        );

        $pages[]=array(
            'key'=>'widget-saas-interactive-v1',
            'title'=>'AI SaaS · Product Experience',
            'desc'=>'AI ve SaaS ürünleri için ürün odaklı hero, etkileşimli özellikler, metrikler, kullanım senaryoları, pricing ve FAQ akışı.',
            'preview_image'=>self::preview('widget-saas-interactive-v1.svg'),
            'category'=>'Full Pages','sector'=>'SaaS / AI','premium'=>1,'quality'=>'Signature',
            'page_quality'=>'Full Page Quality 3.0 + Motion','collection'=>'Technology Pages','experience'=>'Full Page',
            'responsive_ready'=>true,'is_new'=>true,
            'data'=>array(
                self::container(array(self::element('wpsoft-hero-saas',array('wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>860,'wpst_entry_distance'=>32,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>8))),array('_css_classes'=>'wpst-page-section wpst-page-hero','padding'=>array('unit'=>'px','top'=>'36','right'=>'24','bottom'=>'44','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-feature-mosaic',array('layout_variant'=>'balanced','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>680,'wpst_entry_distance'=>18,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>85,'wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-content','padding'=>array('unit'=>'px','top'=>'76','right'=>'24','bottom'=>'76','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-tabs-modern',array('layout_variant'=>'segmented','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>720,'wpst_entry_distance'=>22,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12)),self::element('wpsoft-content-slider',array('wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>720,'wpst_entry_distance'=>22,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-content','background_background'=>'classic','background_color'=>'#f8fafc','padding'=>array('unit'=>'px','top'=>'68','right'=>'24','bottom'=>'72','left'=>'24','isLinked'=>false),'gap'=>array('unit'=>'px','size'=>28))),
                self::container(array(self::element('wpsoft-number-cards',array('wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>680,'wpst_entry_distance'=>18,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>85,'wpst_entry_threshold'=>12)),self::element('wpsoft-stats-grid',array('layout_variant'=>'hero','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>680,'wpst_entry_distance'=>18,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>85,'wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-proof','padding'=>array('unit'=>'px','top'=>'70','right'=>'24','bottom'=>'70','left'=>'24','isLinked'=>false),'gap'=>array('unit'=>'px','size'=>26))),
                self::container(array(self::element('wpsoft-pricing',array('layout_variant'=>'statement','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>680,'wpst_entry_distance'=>18,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>85,'wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-content','background_background'=>'classic','background_color'=>'#f8fafc','padding'=>array('unit'=>'px','top'=>'74','right'=>'24','bottom'=>'74','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-advanced-accordion',array('layout_variant'=>'panel','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>720,'wpst_entry_distance'=>22,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-content','padding'=>array('unit'=>'px','top'=>'66','right'=>'24','bottom'=>'70','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-cta',array('layout_style'=>'floating','title'=>'Ürününüzü bugün deneyin','wpst_entry_motion'=>'scale','wpst_entry_duration'=>720,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>15))),array('_css_classes'=>'wpst-page-section wpst-page-contact','padding'=>array('unit'=>'px','top'=>'52','right'=>'24','bottom'=>'80','left'=>'24','isLinked'=>false)))
            )
        );

        $pages[]=array(
            'key'=>'widget-industry-health-v1',
            'title'=>'Industrial Manufacturing · Precision',
            'desc'=>'Üretim, makine ve endüstriyel teknoloji firmaları için güçlü hero, hizmetler, süreç, kalite metrikleri, referanslar ve teklif akışı.',
            'preview_image'=>self::preview('widget-industry-health-v1.svg'),
            'category'=>'Full Pages','sector'=>'Sanayi / Üretim','premium'=>1,'quality'=>'Signature',
            'page_quality'=>'Full Page Quality 3.0 + Motion','collection'=>'Industry Pages','experience'=>'Full Page',
            'responsive_ready'=>true,'is_new'=>true,
            'data'=>array(
                self::container(array(self::element('wpsoft-hero-industry',array('hero_radius'=>array('size'=>30),'wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>860,'wpst_entry_distance'=>32,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>8))),array('_css_classes'=>'wpst-page-section wpst-page-hero','padding'=>array('unit'=>'px','top'=>'34','right'=>'24','bottom'=>'40','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-service-cards-pro',array('layout_variant'=>'dark','card_style'=>'dark','columns'=>'3','columns_tablet'=>'2','columns_mobile'=>'1','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>680,'wpst_entry_distance'=>18,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>85,'wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-services','background_background'=>'classic','background_color'=>'#0f172a','padding'=>array('unit'=>'px','top'=>'72','right'=>'24','bottom'=>'72','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-process-steps-pro',array('layout_variant'=>'timeline','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>680,'wpst_entry_distance'=>18,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>85,'wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-content','padding'=>array('unit'=>'px','top'=>'74','right'=>'24','bottom'=>'74','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-stats-grid',array('layout_variant'=>'hero','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>680,'wpst_entry_distance'=>18,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>85,'wpst_entry_threshold'=>12)),self::element('wpsoft-progress-pro',array('wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>720,'wpst_entry_distance'=>22,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-proof','background_background'=>'classic','background_color'=>'#f8fafc','padding'=>array('unit'=>'px','top'=>'68','right'=>'24','bottom'=>'68','left'=>'24','isLinked'=>false),'gap'=>array('unit'=>'px','size'=>28))),
                self::container(array(self::element('wpsoft-logo-grid-pro',array('wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>680,'wpst_entry_distance'=>18,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>85,'wpst_entry_threshold'=>12)),self::element('wpsoft-testimonial-slider',array('wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>720,'wpst_entry_distance'=>22,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-proof','padding'=>array('unit'=>'px','top'=>'68','right'=>'24','bottom'=>'72','left'=>'24','isLinked'=>false),'gap'=>array('unit'=>'px','size'=>28))),
                self::container(array(self::element('wpsoft-form-shell',array('layout'=>'split','title'=>'Projeniz için teknik teklif alın','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>720,'wpst_entry_distance'=>22,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-contact','background_background'=>'classic','background_color'=>'#f8fafc','padding'=>array('unit'=>'px','top'=>'62','right'=>'24','bottom'=>'80','left'=>'24','isLinked'=>false)))
            )
        );

        $pages[]=array(
            'key'=>'widget-editorial-blog-v1',
            'title'=>'Editorial Magazine · Newsroom',
            'desc'=>'Dergi, yayın ve kurumsal içerik merkezleri için editorial hero, öne çıkan içerikler, kategori akışı, video ve newsletter odaklı sayfa.',
            'preview_image'=>self::preview('widget-editorial-blog-v1.svg'),
            'category'=>'Full Pages','sector'=>'Blog / Medya','premium'=>1,'quality'=>'Signature',
            'page_quality'=>'Full Page Quality 3.0 + Motion','collection'=>'Editorial Pages','experience'=>'Full Page',
            'responsive_ready'=>true,'is_new'=>true,
            'data'=>array(
                self::container(array(self::element('wpsoft-gradient-heading',array('layout'=>'display','eyebrow'=>'THE JOURNAL','title'=>'Fikirler, insanlar ve yeni perspektifler','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>860,'wpst_entry_distance'=>32,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>8))),array('_css_classes'=>'wpst-page-section wpst-page-hero','background_background'=>'classic','background_color'=>'#0b1020','padding'=>array('unit'=>'px','top'=>'82','right'=>'24','bottom'=>'82','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-blog-posts',array('layout_style'=>'editorial-feed','posts_per_page'=>5,'wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>680,'wpst_entry_distance'=>18,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>85,'wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-content','padding'=>array('unit'=>'px','top'=>'74','right'=>'24','bottom'=>'74','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-story-cards',array('layout'=>'editorial','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>680,'wpst_entry_distance'=>18,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>85,'wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-content','background_background'=>'classic','background_color'=>'#f8fafc','padding'=>array('unit'=>'px','top'=>'68','right'=>'24','bottom'=>'68','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-video-hero',array('wpst_entry_motion'=>'reveal-left','wpst_entry_duration'=>860,'wpst_entry_distance'=>26,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-showcase','padding'=>array('unit'=>'px','top'=>'62','right'=>'24','bottom'=>'68','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-quote',array('wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>720,'wpst_entry_distance'=>22,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-content','background_background'=>'classic','background_color'=>'#f3f4f6','padding'=>array('unit'=>'px','top'=>'64','right'=>'24','bottom'=>'64','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-footer-newsletter',array('wpst_entry_motion'=>'scale','wpst_entry_duration'=>720,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>15))),array('_css_classes'=>'wpst-page-section wpst-page-contact','padding'=>array('unit'=>'px','top'=>'60','right'=>'24','bottom'=>'82','left'=>'24','isLinked'=>false)))
            )
        );

        $pages[]=array(
            'key'=>'widget-ui-navigation-footer-v1',
            'title'=>'Technology Platform · Ecosystem',
            'desc'=>'Teknoloji platformları için ürün hero, çözüm navigasyonu, özellikler, entegrasyonlar, güven ve newsletter CTA akışı.',
            'preview_image'=>self::preview('widget-ui-navigation-footer-v1.svg'),
            'category'=>'Full Pages','sector'=>'Teknoloji / Platform','premium'=>1,'quality'=>'Signature',
            'page_quality'=>'Full Page Quality 3.0 + Motion','collection'=>'Technology Pages','experience'=>'Full Page',
            'responsive_ready'=>true,'is_new'=>true,
            'data'=>array(
                self::container(array(self::element('wpsoft-hero-saas',array('wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>860,'wpst_entry_distance'=>32,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>8))),array('_css_classes'=>'wpst-page-section wpst-page-hero','padding'=>array('unit'=>'px','top'=>'36','right'=>'24','bottom'=>'44','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-mega-quicknav',array('wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>680,'wpst_entry_distance'=>18,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>85,'wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-content','padding'=>array('unit'=>'px','top'=>'46','right'=>'24','bottom'=>'54','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-feature-mosaic',array('layout_variant'=>'balanced','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>680,'wpst_entry_distance'=>18,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>85,'wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-content','background_background'=>'classic','background_color'=>'#f8fafc','padding'=>array('unit'=>'px','top'=>'72','right'=>'24','bottom'=>'72','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-logo-cloud',array('wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>680,'wpst_entry_distance'=>18,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>85,'wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-proof','padding'=>array('unit'=>'px','top'=>'56','right'=>'24','bottom'=>'62','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-link-grid',array('layout'=>'tiles','columns'=>'3','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>680,'wpst_entry_distance'=>18,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>85,'wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-content','background_background'=>'classic','background_color'=>'#f8fafc','padding'=>array('unit'=>'px','top'=>'68','right'=>'24','bottom'=>'68','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-footer-newsletter',array('wpst_entry_motion'=>'scale','wpst_entry_duration'=>720,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>15))),array('_css_classes'=>'wpst-page-section wpst-page-contact','padding'=>array('unit'=>'px','top'=>'60','right'=>'24','bottom'=>'80','left'=>'24','isLinked'=>false)))
            )
        );

        $pages[]=array(
            'key'=>'widget-showcase-experiments-v1',
            'title'=>'Visual Portfolio · Interactive',
            'desc'=>'Fotoğrafçı, sanat yönetmeni ve yaratıcı portfolyolar için güçlü slider, reveal, before/after, hotspot, ekip ve iletişim akışı.',
            'preview_image'=>self::preview('widget-showcase-experiments-v1.svg'),
            'category'=>'Full Pages','sector'=>'Portfolyo / Creative','premium'=>1,'quality'=>'Signature',
            'page_quality'=>'Full Page Quality 3.0 + Motion','collection'=>'Creative Pages','experience'=>'Full Page',
            'responsive_ready'=>true,'is_new'=>true,
            'data'=>array(
                self::container(array(self::element('wpsoft-hero-slider',array('wpst_entry_motion'=>'fade','wpst_entry_duration'=>950,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>5))),array('_css_classes'=>'wpst-page-section wpst-page-hero','padding'=>array('unit'=>'px','top'=>'26','right'=>'24','bottom'=>'32','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-image-reveal',array('wpst_entry_motion'=>'clip-up','wpst_entry_duration'=>920,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-showcase','padding'=>array('unit'=>'px','top'=>'68','right'=>'24','bottom'=>'68','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-before-after',array('wpst_entry_motion'=>'reveal-left','wpst_entry_duration'=>860,'wpst_entry_distance'=>26,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-showcase','background_background'=>'classic','background_color'=>'#f8fafc','padding'=>array('unit'=>'px','top'=>'68','right'=>'24','bottom'=>'68','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-image-hotspots',array('wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>720,'wpst_entry_distance'=>22,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-content','padding'=>array('unit'=>'px','top'=>'66','right'=>'24','bottom'=>'66','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-team-carousel-pro',array('layout_variant'=>'editorial','wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>720,'wpst_entry_distance'=>22,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>12)),self::element('wpsoft-logo-cloud',array('wpst_entry_motion'=>'fade-up','wpst_entry_duration'=>680,'wpst_entry_distance'=>18,'wpst_entry_easing'=>'smooth','wpst_stagger_children'=>'yes','wpst_stagger_style'=>'fade-up','wpst_stagger_delay'=>85,'wpst_entry_threshold'=>12))),array('_css_classes'=>'wpst-page-section wpst-page-proof','background_background'=>'classic','background_color'=>'#f8fafc','padding'=>array('unit'=>'px','top'=>'68','right'=>'24','bottom'=>'72','left'=>'24','isLinked'=>false),'gap'=>array('unit'=>'px','size'=>28))),
                self::container(array(self::element('wpsoft-cta',array('layout_style'=>'floating','title'=>'Yeni bir görsel hikâye oluşturalım','wpst_entry_motion'=>'scale','wpst_entry_duration'=>720,'wpst_entry_easing'=>'smooth','wpst_entry_threshold'=>15))),array('_css_classes'=>'wpst-page-section wpst-page-contact','padding'=>array('unit'=>'px','top'=>'54','right'=>'24','bottom'=>'80','left'=>'24','isLinked'=>false)))
            )
        );

        $pages[]=array(
            'key'=>'studio-editorial-signature-v8','title'=>'Studio · Editorial Signature 2026',
            'desc'=>'Büyük hero, editorial hikâye, bento özellikler, immersive projeler, sosyal kanıt ve güçlü kapanış CTA’sıyla modern ajans/kurumsal sayfa.',
            'preview_image'=>self::preview('studio-editorial-signature-v8.svg'),
            'category'=>'Signature Sayfa','sector'=>'Ajans','premium'=>1,'quality'=>'Signature',
            'page_quality'=>'Curated 2026','collection'=>'Editorial Collection','is_new'=>true,'is_popular'=>true,
            'data'=>array_merge(
                $signature_pair(
                    self::element('wpsoft-hero-split-modern',array(
                        'eyebrow'=>'CREATIVE BUSINESS','title'=>'Fikirleri güçlü dijital deneyimlere dönüştürüyoruz',
                        'text'=>'Daha büyük tipografi, daha az görsel gürültü ve net aksiyonlarla premium bir marka deneyimi.',
                        'image'=>array('url'=>self::demo_v2('agency-signature.svg')),
                        'primary_text'=>'Projeyi Başlat','primary_url'=>array('url'=>'#iletisim'),
                        'secondary_text'=>'Çalışmalar','secondary_url'=>array('url'=>'#projeler'),
                        'layout_style'=>'editorial'
                    )),
                    self::element('wpsoft-image-cascade',array(
                        'image_one'=>array('url'=>self::demo_v2('agency-signature.svg')),
                        'image_two'=>array('url'=>self::demo_v2('architecture-signature.svg')),
                        'image_three'=>array('url'=>self::demo_v2('corporate-signature.svg'))
                    )),
                    '#f7f7f5',54,76
                ),
                $sec_wrap(array(
                    self::element('wpsoft-heading',array('eyebrow'=>'CAPABILITIES','title'=>'Birbirini tamamlayan uzmanlık alanları','description'=>'Hizmetleri bento hiyerarşisiyle daha kolay keşfedilebilir hale getirin.')),
                    self::element('wpsoft-feature-mosaic',array('title'=>'Tek ekip, bütünsel deneyim','image'=>array('url'=>self::demo_v2('saas-signature.svg')))),
                    self::element('wpsoft-icon-grid',array())
                ),'#ffffff',66),
                $sec_wrap(array(
                    self::element('wpsoft-heading',array('eyebrow'=>'SELECTED WORK','title'=>'Sonuç üreten projeler','description'=>'Görsel odaklı proje anlatımı ve güçlü case-study ritmi.')),
                    self::element('wpsoft-portfolio',array('layout_style'=>'editorial','hover_effect'=>'zoom','columns'=>'3'))
                ),'#101318',68),
                $sec_wrap(array(
                    self::element('wpsoft-testimonial-slider',array('style_preset'=>'light')),
                    self::element('wpsoft-logo-marquee',array())
                ),'#f5f7fa',62),
                $signature_pair(
                    self::container(array(
                        self::element('wpsoft-heading',array('eyebrow'=>'LET’S TALK','title'=>'Yeni projenizi birlikte şekillendirelim','description'=>'Kısa bir mesaj bırakın; ihtiyaçlarınızı anlayıp doğru yol haritasını oluşturalım.')),
                        self::element('wpsoft-contact-cards',array('columns'=>'1','style_preset'=>'soft'))
                    ),array()),
                    self::element('wpsoft-wpforms',array('empty_title'=>'İletişim Formunuzu Seçin','empty_text'=>'WPForms formu seçildiğinde gerçek form burada görüntülenir.','shell_style'=>'card')),
                    '#ffffff',43,72
                )
            )
        );

        $pages[]=array(
            'key'=>'commerce-editorial-signature-v8','title'=>'Commerce · Editorial Shop 2026',
            'desc'=>'WoodMart’ın ürün keşfi yaklaşımından esinlenen kategori kartları, ürün hikâyesi, güven alanları ve kampanya CTA akışına sahip modern mağaza sayfası.',
            'preview_image'=>self::preview('commerce-editorial-signature-v8.svg'),
            'category'=>'Signature Sayfa','sector'=>'E-Ticaret','premium'=>1,'quality'=>'Signature',
            'page_quality'=>'Curated 2026','collection'=>'Commerce Collection','is_new'=>true,'is_popular'=>true,
            'data'=>array_merge(
                $sec_wrap(array(
                    self::element('wpsoft-hero-commerce',array(
                        'image'=>array('url'=>self::demo_v2('commerce-signature.svg')),
                        'button_text'=>'Koleksiyonu Keşfet','button_url'=>array('url'=>'#koleksiyon')
                    ))
                ),'#f4f1eb',36),
                $sec_wrap(array(
                    self::element('wpsoft-heading',array('eyebrow'=>'SHOP BY CATEGORY','title'=>'İhtiyacınıza göre keşfedin','description'=>'Görsel kategori kartlarıyla ürün keşfini hızlandırın.')),
                    self::element('wpsoft-product-showcase',array(
                        'items'=>array(
                            array('image'=>array('url'=>self::demo_v2('commerce-signature.svg')),'title'=>'Yeni Sezon','meta'=>'24 ürün','price'=>''),
                            array('image'=>array('url'=>self::demo_v2('corporate-signature.svg')),'title'=>'Çok Satanlar','meta'=>'18 ürün','price'=>''),
                            array('image'=>array('url'=>self::demo_v2('agency-signature.svg')),'title'=>'Editör Seçimi','meta'=>'12 ürün','price'=>'')
                        ),
                        'action_text'=>'Koleksiyonu Gör'
                    ))
                ),'#ffffff',64),
                $signature_pair(
                    self::element('wpsoft-image-text',array(
                        'eyebrow'=>'DESIGNED FOR LIFE','title'=>'Detaylarda daha iyi bir kullanım deneyimi',
                        'description'=>'Ürün özelliklerini yaşam tarzı ve kullanım faydalarıyla birlikte anlatın.',
                        'image'=>array('url'=>self::demo_v2('commerce-signature.svg')),
                        'button_text'=>'Ürünü İncele','button_url'=>array('url'=>'#urun'),
                        'layout_style'=>'editorial'
                    )),
                    self::container(array(
                        self::element('wpsoft-feature-list',array()),
                        self::element('wpsoft-trust-badges',array())
                    ),array()),
                    '#f5f2ec',52,70
                ),
                $sec_wrap(array(
                    self::element('wpsoft-testimonial-slider',array('style_preset'=>'light')),
                    self::element('wpsoft-logo-marquee',array())
                ),'#ffffff',58),
                $sec_wrap(array(
                    self::element('wpsoft-morphing-cta',array(
                        'eyebrow'=>'LIMITED EDIT','title'=>'Yeni koleksiyonu keşfedin','text'=>'Seçilmiş ürünleri ve yeni sezon fırsatlarını inceleyin.',
                        'button_text'=>'Alışverişe Başla','button_url'=>array('url'=>'#magaza')
                    ))
                ),'#111827',48)
            )
        );

        $pages[]=array(
            'key'=>'essentials-corporate-signature','title'=>'Kurumsal · Modern Signature',
            'desc'=>'Layered hero, asymmetric about, floating services, proof, pricing, FAQ ve modern contact akışı.',
            'preview_image'=>self::preview('essentials-corporate-signature.svg'),
            'category'=>'Signature Sayfa','sector'=>'Kurumsal','premium'=>1,'quality'=>'Signature','page_quality'=>'Premium Layout','collection'=>'Modern Collection','is_new'=>true,'is_popular'=>true,
            'data'=>array(
                self::container(array(self::element('wpsoft-hero-split-modern',array(
                    'eyebrow'=>'MODERN BUSINESS','title'=>'Daha güçlü bir dijital ilk izlenim',
                    'text'=>'Modern tipografi, katmanlı görseller ve net dönüşüm akışıyla markanızı öne çıkarın.',
                    'image'=>array('url'=>self::demo_v2('corporate-signature.svg')),
                    'primary_text'=>'Projeyi Başlat','primary_url'=>array('url'=>'#iletisim'),
                    'secondary_text'=>'Hizmetleri İncele','secondary_url'=>array('url'=>'#hizmetler')
                ))),array('_css_classes'=>'wpst-ess-section wpst-page-section wpst-page-hero wpst-ess-hero-layered','background_background'=>'classic','background_color'=>'#f6f7fb','padding'=>array('unit'=>'px','top'=>'34','right'=>'24','bottom'=>'34','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-logo-marquee')),array('_css_classes'=>'wpst-ess-section wpst-page-section wpst-page-proof wpst-ess-proof-strip','padding'=>array('unit'=>'px','top'=>'26','right'=>'24','bottom'=>'26','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-image-cascade'),self::element('wpsoft-image-text',array(
                    'eyebrow'=>'ABOUT','title'=>'Sadelik ve sistem, iyi deneyimin temelidir',
                    'description'=>'Strateji, içerik ve tasarımı aynı akış içinde çözüyoruz.'
                ))),array('_css_classes'=>'wpst-ess-section wpst-page-section wpst-page-split wpst-ess-about-asym','flex_direction'=>'row','flex_wrap'=>'wrap','gap'=>array('unit'=>'px','size'=>32,'row'=>32,'column'=>32),'padding'=>array('unit'=>'px','top'=>'84','right'=>'24','bottom'=>'84','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-heading',array('eyebrow'=>'SERVICES','title'=>'Daha iyi sonuç için doğru yapı','description'=>'Strateji, tasarım ve teknoloji hizmetlerini tek bir sistemde sunun.')),self::element('wpsoft-service-cards-pro')),array('_css_classes'=>'wpst-ess-section wpst-page-section wpst-page-content wpst-ess-services-floating','background_background'=>'classic','background_color'=>'#f7f8fc','padding'=>array('unit'=>'px','top'=>'84','right'=>'24','bottom'=>'84','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-hover-reveal')),array('_css_classes'=>'wpst-ess-section wpst-page-section wpst-page-showcase wpst-ess-projects-dark','background_background'=>'classic','background_color'=>'#0b1020','padding'=>array('unit'=>'px','top'=>'86','right'=>'24','bottom'=>'86','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-testimonial-slider'),self::element('wpsoft-logo-marquee')),array('_css_classes'=>'wpst-ess-section wpst-page-section wpst-page-proof wpst-ess-glass-proof','background_background'=>'classic','background_color'=>'#eef2ff','padding'=>array('unit'=>'px','top'=>'72','right'=>'24','bottom'=>'72','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-pricing')),array('_css_classes'=>'wpst-ess-section wpst-page-section wpst-page-content wpst-ess-pricing-clean','padding'=>array('unit'=>'px','top'=>'78','right'=>'24','bottom'=>'78','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-faq')),array('_css_classes'=>'wpst-ess-section wpst-page-section wpst-page-content wpst-ess-faq-split','background_background'=>'classic','background_color'=>'#f8fafc','padding'=>array('unit'=>'px','top'=>'72','right'=>'24','bottom'=>'72','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-contact-cards'),self::element('wpsoft-wpforms')),array('_css_classes'=>'wpst-ess-section wpst-page-section wpst-page-contact wpst-ess-contact-layered','flex_direction'=>'row','flex_wrap'=>'wrap','gap'=>array('unit'=>'px','size'=>28,'row'=>28,'column'=>28),'padding'=>array('unit'=>'px','top'=>'82','right'=>'24','bottom'=>'82','left'=>'24','isLinked'=>false)))
            )
        );

        foreach($templates as $key=>$tpl){
            $is_contact=0===strpos($key,'contact-');
            $pages[]=array(
                'key'=>$key,
                'title'=>$tpl['title'],
                'desc'=>$tpl['desc'],
                'preview_image'=>self::preview($tpl['preview']),
                'data'=>self::elementor_data($key,$tpl),
                'category'=>$is_contact?'İletişim Sayfası':'Sektör Sayfası',
                'sector'=>isset($tpl['sector'])?$tpl['sector']:($is_contact?'İletişim':'Genel'),
                'quality'=>'Signature',
                'page_quality'=>'Premium Layout',
                'collection'=>$is_contact?'Contact Collection':'Sector Collection',
                'is_popular'=>in_array($key,array('contact-wpforms-modern','ecommerce-premium','architecture-premium','restaurant-signature'),true)
            );
        }


        // Template Library 3.1 · Signature full-page experiences.
        $signature_pages=array(
            array(
                'key'=>'corporate-signature-v2','title'=>'Kurumsal · Executive Signature',
                'desc'=>'Executive hero, güven katmanı, hizmetler, metrikler, süreç, referanslar ve güçlü kapanış CTA ile premium kurumsal sayfa.',
                'sector'=>'Kurumsal','preview'=>'corporate-signature-v2.svg',
                'data'=>array(
                    self::container(array(self::element('wpsoft-hero-split-modern',array(
                        'eyebrow'=>'EXECUTIVE DIGITAL EXPERIENCE','title'=>'Güven veren güçlü bir marka deneyimi oluşturun',
                        'text'=>'Kurumsal uzmanlığınızı modern tipografi, güçlü sosyal kanıt ve net aksiyonlarla sunun.',
                        'image'=>array('url'=>self::demo_v2('corporate-signature.svg')),
                        'primary_text'=>'Projeyi Başlat','primary_url'=>array('url'=>'#iletisim'),
                        'secondary_text'=>'Hizmetleri Keşfet','secondary_url'=>array('url'=>'#hizmetler')
                    ))),array('background_background'=>'classic','background_color'=>'#f8fafc','padding'=>array('unit'=>'px','top'=>'26','right'=>'24','bottom'=>'26','left'=>'24','isLinked'=>false))),
                    self::container(array(self::element('wpsoft-logo-marquee')),array('padding'=>array('unit'=>'px','top'=>'26','right'=>'24','bottom'=>'26','left'=>'24','isLinked'=>false))),
                    self::container(array(self::element('wpsoft-heading',array('eyebrow'=>'EXPERTISE','title'=>'Stratejiden uygulamaya tek yapı','description'=>'İş hedeflerinize göre şekillenen bütünleşik dijital çözümler.')),self::element('wpsoft-service-cards-pro')),array('background_background'=>'classic','background_color'=>'#ffffff','padding'=>array('unit'=>'px','top'=>'72','right'=>'24','bottom'=>'72','left'=>'24','isLinked'=>false))),
                    self::container(array(self::element('wpsoft-image-text',array('eyebrow'=>'WHY US','title'=>'Net süreç, ölçülebilir sonuç','description'=>'Planlama, tasarım, geliştirme ve optimizasyonu sürdürülebilir bir sistem içinde yürütüyoruz.','image'=>array('url'=>self::demo('corporate.svg')))),self::element('wpsoft-stats-grid')),array('background_background'=>'classic','background_color'=>'#eff6ff','padding'=>array('unit'=>'px','top'=>'68','right'=>'24','bottom'=>'68','left'=>'24','isLinked'=>false))),
                    self::container(array(self::element('wpsoft-process-steps-pro')),array('padding'=>array('unit'=>'px','top'=>'66','right'=>'24','bottom'=>'66','left'=>'24','isLinked'=>false))),
                    self::container(array(self::element('wpsoft-testimonial-slider'),self::element('wpsoft-logo-grid-pro')),array('background_background'=>'classic','background_color'=>'#f8fafc','padding'=>array('unit'=>'px','top'=>'64','right'=>'24','bottom'=>'64','left'=>'24','isLinked'=>false))),
                    self::container(array(self::element('wpsoft-morphing-cta',array('eyebrow'=>'NEXT STEP','title'=>'Bir sonraki güçlü adımı birlikte planlayalım','button_text'=>'Bize Ulaşın','button_url'=>array('url'=>'#iletisim')))),array('background_background'=>'classic','background_color'=>'#07111f','padding'=>array('unit'=>'px','top'=>'54','right'=>'24','bottom'=>'54','left'=>'24','isLinked'=>false)))
                )
            ),
            array(
                'key'=>'agency-signature-v2','title'=>'Ajans · Creative Signature',
                'desc'=>'Kinetik hero, marquee, cascade görseller, yaratıcı servis vitrini, proje reveal ve modern CTA ile ajans sayfası.',
                'sector'=>'Ajans','preview'=>'agency-signature-v2.svg',
                'data'=>array(
                    self::container(array(self::element('wpsoft-hero-bento',array('hero_radius'=>array('size'=>30),'eyebrow'=>'INDEPENDENT CREATIVE STUDIO','title'=>'Fikirleri dikkat çeken dijital deneyimlere dönüştürüyoruz','text'=>'Strateji, tasarım, motion ve teknolojiyi tek yaratıcı sistemde birleştiriyoruz.','image'=>array('url'=>self::demo_v2('agency-signature.svg')),'button_text'=>'Projeyi Başlat','button_url'=>array('url'=>'#iletisim')))),array('background_background'=>'classic','background_color'=>'#f5f3ff','padding'=>array('unit'=>'px','top'=>'24','right'=>'24','bottom'=>'24','left'=>'24','isLinked'=>false))),
                    self::container(array(self::element('wpsoft-marquee-text')),array('background_background'=>'classic','background_color'=>'#07111f','padding'=>array('unit'=>'px','top'=>'22','right'=>'0','bottom'=>'22','left'=>'0','isLinked'=>false))),
                    self::container(array(self::element('wpsoft-scroll-reveal-text',array('eyebrow'=>'OUR POINT OF VIEW','text'=>'İyi tasarım sadece görünmez; markayı daha net, daha güçlü ve daha hatırlanabilir hale getirir.'))),array('padding'=>array('unit'=>'px','top'=>'78','right'=>'24','bottom'=>'78','left'=>'24','isLinked'=>false))),
                    self::container(array(self::element('wpsoft-image-cascade',array('image_one'=>array('url'=>self::demo('agency.svg')),'image_two'=>array('url'=>self::demo('architecture.svg')),'image_three'=>array('url'=>self::demo('corporate.svg')))),self::element('wpsoft-service-cards-pro')),array('background_background'=>'classic','background_color'=>'#faf5ff','padding'=>array('unit'=>'px','top'=>'70','right'=>'24','bottom'=>'70','left'=>'24','isLinked'=>false))),
                    self::container(array(self::element('wpsoft-hover-reveal')),array('background_background'=>'classic','background_color'=>'#07111f','padding'=>array('unit'=>'px','top'=>'68','right'=>'24','bottom'=>'68','left'=>'24','isLinked'=>false))),
                    self::container(array(self::element('wpsoft-testimonial-slider'),self::element('wpsoft-animated-counter')),array('padding'=>array('unit'=>'px','top'=>'64','right'=>'24','bottom'=>'64','left'=>'24','isLinked'=>false))),
                    self::container(array(self::element('wpsoft-morphing-cta',array('eyebrow'=>'NEXT PROJECT','title'=>'Birlikte fark edilen bir şey üretelim','button_text'=>'Başlayalım','button_url'=>array('url'=>'#iletisim')))),array('background_background'=>'classic','background_color'=>'#7c3aed','padding'=>array('unit'=>'px','top'=>'56','right'=>'24','bottom'=>'56','left'=>'24','isLinked'=>false)))
                )
            ),
            array(
                'key'=>'industrial-signature-v2','title'=>'Sanayi · Technical Signature',
                'desc'=>'Teknik hero, üretim kabiliyetleri, servis alanları, metrikler, süreç ve teklif CTA ile güçlü B2B sanayi sayfası.',
                'sector'=>'Sanayi','preview'=>'industrial-signature-v2.svg',
                'data'=>array(
                    self::container(array(self::element('wpsoft-hero-industry',array('hero_radius'=>array('size'=>30),'eyebrow'=>'ENGINEERING · PRODUCTION · SERVICE','title'=>'Üretim gücünüzü dijitalde doğru anlatın','text'=>'Makina parkuru, teknik kabiliyetler ve servis gücünü güven veren bir yapı içinde sunun.','image'=>array('url'=>self::demo_v2('industrial-signature.svg')),'button_text'=>'Teknik Teklif Al','button_url'=>array('url'=>'#iletisim')))),array('background_background'=>'classic','background_color'=>'#07111f','padding'=>array('unit'=>'px','top'=>'20','right'=>'20','bottom'=>'20','left'=>'20','isLinked'=>false))),
                    self::container(array(self::element('wpsoft-trust-badges')),array('padding'=>array('unit'=>'px','top'=>'26','right'=>'24','bottom'=>'26','left'=>'24','isLinked'=>false))),
                    self::container(array(self::element('wpsoft-feature-mosaic',array('title'=>'Teknik altyapı ve üretim kabiliyeti','image'=>array('url'=>self::demo('industry.svg'))))),array('background_background'=>'classic','background_color'=>'#101820','padding'=>array('unit'=>'px','top'=>'66','right'=>'24','bottom'=>'66','left'=>'24','isLinked'=>false))),
                    self::container(array(self::element('wpsoft-service-cards-pro'),self::element('wpsoft-number-cards')),array('background_background'=>'classic','background_color'=>'#f8fafc','padding'=>array('unit'=>'px','top'=>'68','right'=>'24','bottom'=>'68','left'=>'24','isLinked'=>false))),
                    self::container(array(self::element('wpsoft-stats-grid'),self::element('wpsoft-progress-pro')),array('padding'=>array('unit'=>'px','top'=>'62','right'=>'24','bottom'=>'62','left'=>'24','isLinked'=>false))),
                    self::container(array(self::element('wpsoft-process-steps-pro')),array('background_background'=>'classic','background_color'=>'#f8fafc','padding'=>array('unit'=>'px','top'=>'64','right'=>'24','bottom'=>'64','left'=>'24','isLinked'=>false))),
                    self::container(array(self::element('wpsoft-cta',array('title'=>'Teknik ihtiyacınızı uzman ekibimizle değerlendirelim','description'=>'Makina, üretim, servis veya yedek parça ihtiyacınız için bize ulaşın.','button_text'=>'Teknik Görüşme','button_url'=>array('url'=>'#iletisim'),'bg'=>'#101820'))),array('padding'=>array('unit'=>'px','top'=>'32','right'=>'24','bottom'=>'32','left'=>'24','isLinked'=>false)))
                )
            ),
            array(
                'key'=>'hotel-signature-v2','title'=>'Otel · Hospitality Signature',
                'desc'=>'Zarif hero, rezervasyon şeridi, deneyim galerisi, hizmetler, yorumlar ve direkt rezervasyon CTA ile premium otel sayfası.',
                'sector'=>'Otel','preview'=>'hotel-signature-v2.svg',
                'data'=>array(
                    self::container(array(self::element('wpsoft-hero-hospitality',array('eyebrow'=>'A DIFFERENT WAY TO STAY','title'=>'Konforun ötesinde hatırlanan bir deneyim','text'=>'Mekânın atmosferini, hizmet kalitesini ve doğrudan rezervasyon avantajını güçlü bir hikâyeyle sunun.','image'=>array('url'=>self::demo_v2('hotel-signature.svg')),'button_text'=>'Rezervasyon Yap','button_url'=>array('url'=>'#rezervasyon')))),array('background_background'=>'classic','background_color'=>'#13251f','padding'=>array('unit'=>'px','top'=>'18','right'=>'18','bottom'=>'18','left'=>'18','isLinked'=>false))),
                    self::container(array(self::element('wpsoft-booking-strip',array('button_text'=>'Müsaitliği Kontrol Et','button_url'=>array('url'=>'#rezervasyon')))),array('padding'=>array('unit'=>'px','top'=>'20','right'=>'24','bottom'=>'20','left'=>'24','isLinked'=>false))),
                    self::container(array(self::element('wpsoft-image-carousel',array('gallery'=>array(array('url'=>self::demo('hotel.svg'),'id'=>0),array('url'=>self::demo('travel.svg'),'id'=>0),array('url'=>self::demo('restaurant.svg'),'id'=>0))))),array('background_background'=>'classic','background_color'=>'#fffdf8','padding'=>array('unit'=>'px','top'=>'66','right'=>'24','bottom'=>'66','left'=>'24','isLinked'=>false))),
                    self::container(array(self::element('wpsoft-heading',array('eyebrow'=>'THE EXPERIENCE','title'=>'Her detay daha iyi bir konaklama için','description'=>'Oda, gastronomi, spa ve destinasyon deneyimini tek sayfada anlatın.')),self::element('wpsoft-service-cards-pro')),array('padding'=>array('unit'=>'px','top'=>'68','right'=>'24','bottom'=>'68','left'=>'24','isLinked'=>false))),
                    self::container(array(self::element('wpsoft-image-text',array('eyebrow'=>'LOCAL STORY','title'=>'Bulunduğunuz yeri deneyimin bir parçası haline getirin','image'=>array('url'=>self::demo('travel.svg'))))),array('background_background'=>'classic','background_color'=>'#f0fdfa','padding'=>array('unit'=>'px','top'=>'66','right'=>'24','bottom'=>'66','left'=>'24','isLinked'=>false))),
                    self::container(array(self::element('wpsoft-testimonial-slider')),array('padding'=>array('unit'=>'px','top'=>'62','right'=>'24','bottom'=>'62','left'=>'24','isLinked'=>false))),
                    self::container(array(self::element('wpsoft-morphing-cta',array('eyebrow'=>'BOOK DIRECT','title'=>'Bir sonraki konaklamanız burada başlasın','button_text'=>'Rezervasyon Yap','button_url'=>array('url'=>'#rezervasyon')))),array('background_background'=>'classic','background_color'=>'#13251f','padding'=>array('unit'=>'px','top'=>'56','right'=>'24','bottom'=>'56','left'=>'24','isLinked'=>false)))
                )
            ),
            array(
                'key'=>'clinic-signature-v2','title'=>'Klinik · Trust Signature',
                'desc'=>'Güven odaklı sağlık hero, uzmanlık kartları, doktor/ekip, süreç, yorumlar ve randevu CTA ile premium klinik sayfası.',
                'sector'=>'Sağlık','preview'=>'clinic-signature-v2.svg',
                'data'=>array(
                    self::container(array(self::element('wpsoft-hero-medical',array('hero_radius'=>array('size'=>30),'eyebrow'=>'TRUST · CARE · EXPERTISE','title'=>'Sağlıkta güven veren modern bir dijital deneyim','text'=>'Uzmanlık alanlarını, ekibi ve hasta deneyimini sade ve güvenilir biçimde sunun.','image'=>array('url'=>self::demo_v2('clinic-signature.svg')),'button_text'=>'Randevu Al','button_url'=>array('url'=>'#randevu')))),array('background_background'=>'classic','background_color'=>'#f0fdfa','padding'=>array('unit'=>'px','top'=>'24','right'=>'24','bottom'=>'24','left'=>'24','isLinked'=>false))),
                    self::container(array(self::element('wpsoft-trust-badges')),array('padding'=>array('unit'=>'px','top'=>'26','right'=>'24','bottom'=>'26','left'=>'24','isLinked'=>false))),
                    self::container(array(self::element('wpsoft-heading',array('eyebrow'=>'UZMANLIKLAR','title'=>'İhtiyacınıza uygun uzmanlık alanları','description'=>'Hizmetleri anlaşılır ve güven veren kartlarla sunun.')),self::element('wpsoft-icon-grid')),array('padding'=>array('unit'=>'px','top'=>'66','right'=>'24','bottom'=>'66','left'=>'24','isLinked'=>false))),
                    self::container(array(self::element('wpsoft-image-text',array('eyebrow'=>'OUR APPROACH','title'=>'Her hasta için kişiselleştirilmiş yaklaşım','description'=>'Tanıdan takibe kadar açık iletişim ve uzmanlık odaklı süreç.','image'=>array('url'=>self::demo('health.svg')))),self::element('wpsoft-number-cards')),array('background_background'=>'classic','background_color'=>'#f8fafc','padding'=>array('unit'=>'px','top'=>'66','right'=>'24','bottom'=>'66','left'=>'24','isLinked'=>false))),
                    self::container(array(self::element('wpsoft-team-carousel-pro')),array('padding'=>array('unit'=>'px','top'=>'62','right'=>'24','bottom'=>'62','left'=>'24','isLinked'=>false))),
                    self::container(array(self::element('wpsoft-testimonial-slider'),self::element('wpsoft-faq')),array('background_background'=>'classic','background_color'=>'#f0fdfa','padding'=>array('unit'=>'px','top'=>'62','right'=>'24','bottom'=>'62','left'=>'24','isLinked'=>false))),
                    self::container(array(self::element('wpsoft-cta',array('title'=>'Sağlığınız için ilk adımı bugün atın','description'=>'Randevu ve bilgi talepleriniz için ekibimizle iletişime geçin.','button_text'=>'Randevu Oluştur','button_url'=>array('url'=>'#randevu'),'bg'=>'#0f766e'))),array('padding'=>array('unit'=>'px','top'=>'30','right'=>'24','bottom'=>'30','left'=>'24','isLinked'=>false)))
                )
            ),
            array(
                'key'=>'saas-signature-v2','title'=>'SaaS · Product Signature',
                'desc'=>'Dashboard hero, logo proof, özellik vitrini, süreç, fiyatlandırma, yorumlar ve trial CTA ile modern SaaS landing page.',
                'sector'=>'Yazılım','preview'=>'saas-signature-v2.svg',
                'data'=>array(
                    self::container(array(self::element('wpsoft-hero-saas',array('badge'=>'BUILT FOR MODERN TEAMS','title'=>'Daha hızlı çalışın, daha net büyüyün','text'=>'Ürünün değerini ilk ekranda anlatan dashboard odaklı modern SaaS deneyimi.','image'=>array('url'=>self::demo_v2('saas-signature.svg')),'primary_text'=>'Ücretsiz Başla','primary_url'=>array('url'=>'#'),'secondary_text'=>'Demo İzle','secondary_url'=>array('url'=>'#demo')))),array('background_background'=>'classic','background_color'=>'#f5f3ff','padding'=>array('unit'=>'px','top'=>'22','right'=>'22','bottom'=>'22','left'=>'22','isLinked'=>false))),
                    self::container(array(self::element('wpsoft-logo-marquee')),array('padding'=>array('unit'=>'px','top'=>'24','right'=>'24','bottom'=>'24','left'=>'24','isLinked'=>false))),
                    self::container(array(self::element('wpsoft-feature-mosaic',array('title'=>'Ekibinizin ihtiyaç duyduğu her şey tek akışta','image'=>array('url'=>self::demo('saas.svg'))))),array('padding'=>array('unit'=>'px','top'=>'68','right'=>'24','bottom'=>'68','left'=>'24','isLinked'=>false))),
                    self::container(array(self::element('wpsoft-tabs-modern'),self::element('wpsoft-reveal-cards')),array('background_background'=>'classic','background_color'=>'#f8fafc','padding'=>array('unit'=>'px','top'=>'66','right'=>'24','bottom'=>'66','left'=>'24','isLinked'=>false))),
                    self::container(array(self::element('wpsoft-pricing'),self::element('wpsoft-trust-badges')),array('padding'=>array('unit'=>'px','top'=>'66','right'=>'24','bottom'=>'66','left'=>'24','isLinked'=>false))),
                    self::container(array(self::element('wpsoft-testimonial-slider'),self::element('wpsoft-faq')),array('background_background'=>'classic','background_color'=>'#f5f3ff','padding'=>array('unit'=>'px','top'=>'62','right'=>'24','bottom'=>'62','left'=>'24','isLinked'=>false))),
                    self::container(array(self::element('wpsoft-morphing-cta',array('eyebrow'=>'START TODAY','title'=>'Ekibiniz için daha sade bir çalışma alanı','button_text'=>'Ücretsiz Başla','button_url'=>array('url'=>'#')))),array('background_background'=>'classic','background_color'=>'#0b1020','padding'=>array('unit'=>'px','top'=>'56','right'=>'24','bottom'=>'56','left'=>'24','isLinked'=>false)))
                )
            )
        );
        foreach($signature_pages as $sp){
            $pages[]=array(
                'key'=>$sp['key'],'title'=>$sp['title'],'desc'=>$sp['desc'],
                'preview_image'=>self::preview($sp['preview']),'data'=>$sp['data'],
                'category'=>'Signature Sayfa','sector'=>$sp['sector'],'premium'=>1,'quality'=>'Signature',
                'page_quality'=>'Premium Layout','collection'=>'Signature v2',
                'is_new'=>true,'is_popular'=>true
            );
        }

        // Blog / Single Post page examples: shown directly in WPSoft Şablonlar > Sayfa Şablonları.
        // These are dynamic templates; after insertion they pull data from the current WordPress post.
        $blog_pages=array(
            array(
                'key'=>'blog-editorial',
                'title'=>'Blog Arşiv · Editorial Clean',
                'desc'=>'10 yazılık modern kart grid, sayfalama ve kategori bilgileriyle blog listeleme sayfası.',
                'preview'=>'blog-editorial.svg',
                'data'=>self::blog_page_data('editorial')
            ),
            array(
                'key'=>'blog-magazine',
                'title'=>'Blog Arşiv · Modern Magazine',
                'desc'=>'12 yazılık görsel odaklı magazine blog listeleme sayfası.',
                'preview'=>'blog-magazine.svg',
                'data'=>self::blog_page_data('magazine')
            ),
            array(
                'key'=>'blog-minimal',
                'title'=>'Blog Arşiv · Minimal Reading',
                'desc'=>'20 yazıya kadar sade tek kolon blog arşiv/liste görünümü.',
                'preview'=>'blog-minimal.svg',
                'data'=>self::blog_page_data('minimal')
            ),
            array(
                'key'=>'blog-creative',
                'title'=>'Blog Arşiv · Creative Story',
                'desc'=>'Ajans ve kişisel markalar için 2 kolon yaratıcı blog listeleme sayfası.',
                'preview'=>'blog-creative.svg',
                'data'=>self::blog_page_data('creative')
            ),
            array(
                'key'=>'blog-corporate',
                'title'=>'Blog Arşiv · Corporate Insight',
                'desc'=>'Kurumsal haber ve makaleler için 3 kolon blog arşiv sayfası.',
                'preview'=>'blog-corporate.svg',
                'data'=>self::blog_page_data('corporate')
            ),
            array(
                'key'=>'blog-tech',
                'title'=>'Blog Arşiv · Tech / SaaS',
                'desc'=>'Teknoloji ve SaaS içerikleri için 12 yazılık modern blog listeleme sayfası.',
                'preview'=>'blog-tech.svg',
                'data'=>self::blog_page_data('tech')
            )
        );
        foreach($blog_pages as $bp){
            $pages[]=array('key'=>$bp['key'],'title'=>$bp['title'],'desc'=>$bp['desc'],'preview_image'=>self::preview($bp['preview']),'data'=>$bp['data'],'category'=>'Blog Arşiv','sector'=>'Blog','template_role'=>'blog_archive','quality'=>'Modern','page_quality'=>'Premium Layout','collection'=>'Blog Archive','is_new'=>true);
        }

        $single_blog_pages=array(
            array(
                'key'=>'single-editorial',
                'title'=>'Tek Yazı · Editorial Clean',
                'desc'=>'Klasik blog detay yapısı: meta, başlık, özet, öne çıkan görsel, içerik, paylaşım, yazar, ilgili yazılar ve yorumlar.',
                'preview'=>'single-editorial.svg',
                'style'=>'editorial'
            ),
            array(
                'key'=>'single-magazine',
                'title'=>'Tek Yazı · Modern Magazine',
                'desc'=>'Kategori etiketi, büyük başlık ve güçlü görsel hiyerarşisiyle dergi tipi tek yazı detay şablonu.',
                'preview'=>'single-magazine.svg',
                'style'=>'magazine'
            ),
            array(
                'key'=>'single-minimal',
                'title'=>'Tek Yazı · Minimal Reading',
                'desc'=>'Dar içerik genişliği ve yüksek okunabilirlik odaklı sade tek yazı detay şablonu.',
                'preview'=>'single-minimal.svg',
                'style'=>'minimal'
            ),
            array(
                'key'=>'single-feature',
                'title'=>'Tek Yazı · Feature Story',
                'desc'=>'Okuma ilerlemesi ve ilgili yazılarla uzun içerik, rehber ve editoryal hikâyeler için detay şablonu.',
                'preview'=>'single-feature.svg',
                'style'=>'feature'
            ),
            array(
                'key'=>'single-dark',
                'title'=>'Tek Yazı · Dark Insight',
                'desc'=>'Teknoloji, SaaS ve içerik siteleri için koyu tonlu premium tek yazı detay şablonu.',
                'preview'=>'single-dark.svg',
                'style'=>'dark'
            )
        );
        foreach($single_blog_pages as $sp){
            $pages[]=array(
                'key'=>$sp['key'],
                'title'=>$sp['title'],
                'desc'=>$sp['desc'],
                'preview_image'=>self::preview($sp['preview']),
                'data'=>self::single_blog_page_data($sp['style']),
                'category'=>'Tek Yazı',
                'sector'=>'Blog',
                'template_role'=>'single_post',
                'quality'=>in_array($sp['style'],array('feature','dark','magazine'),true)?'Signature':'Modern',
                'page_quality'=>'Premium Layout',
                'collection'=>'Single Post',
                'is_new'=>in_array($sp['style'],array('feature','dark'),true)
            );
        }

        /*
         * Portfolio Detail Templates
         * These are intentionally exposed only inside WPSoft Template Library.
         * Portfolio admin remains a clean content/category manager.
         */
        if(class_exists('WPST_Portfolio_Manager') && method_exists('WPST_Portfolio_Manager','templates') && method_exists('WPST_Portfolio_Manager','template_data')){
            $portfolio_previews=array(
                'editorial'=>'page-project-detail-v1.svg',
                'split'=>'page-project-detail-v1.svg',
                'dark'=>'page-portfolio-cinematic-v1.svg',
                'minimal'=>'single-minimal.svg'
            );
            foreach(WPST_Portfolio_Manager::templates() as $portfolio_key=>$portfolio_tpl){
                $pages[]=array(
                    'key'=>'portfolio-detail-'.$portfolio_key,
                    'title'=>'Portföy Detay · '.$portfolio_tpl['title'],
                    'desc'=>$portfolio_tpl['desc'],
                    'preview_image'=>self::preview($portfolio_previews[$portfolio_key] ?? 'page-project-detail-v1.svg'),
                    'data'=>WPST_Portfolio_Manager::template_data($portfolio_key),
                    'category'=>'Portföy Detay',
                    'sector'=>'Portföy',
                    'template_role'=>'portfolio_single',
                    'quality'=>'Signature',
                    'page_quality'=>'Portfolio Detail Layout',
                    'collection'=>'Portfolio Detail',
                    'experience'=>'Portfolio Single',
                    'responsive_ready'=>true,
                    'is_new'=>true,
                    'premium'=>1
                );
            }
        }

        // Bölümler Kalite Turu 1: mevcut section verisini silmeden
        // kategori, responsive ve Global Design standardını normalize eder.
        $sections=self::section_quality_tour_v1($sections);

        // Quality Tour 2: legacy category labels are normalized after every
        // dynamically appended section has joined the collection.
        $section_category_aliases=array(
            'Projeler'=>'Portföy',
            'Galeri'=>'Video / Galeri',
            'Video'=>'Video / Galeri',
            'Metin'=>'Genel',
            'Kategoriler'=>'Özellikler',
            'Ürün Tanıtımı'=>'Ürünler',
            'Yorumlar & Güven'=>'Yorumlar',
            'Sosyal Kanıt'=>'Yorumlar',
            'Sektör Setleri'=>'Sektörel',
            'Widget Lab'=>'Genel'
        );
        foreach($sections as &$wpst_section_item){
            $wpst_cat=(string)($wpst_section_item['category']??'Genel');
            if(isset($section_category_aliases[$wpst_cat])){
                $wpst_section_item['category']=$section_category_aliases[$wpst_cat];
            }
        }
        unset($wpst_section_item);

        $widgets=self::sync_registered_wpsoft_widgets($widgets);

        return array(
            'widgets'=>self::library_meta($widgets,'widgets'),
            'sections'=>self::library_meta($sections,'sections'),
            'pages'=>self::library_meta($pages,'pages'),
            'headers'=>self::header_library_payload(),
            'footers'=>self::footer_library_payload(),
            'mega_menus'=>self::library_meta(self::mega_menu_templates(),'mega_menus'),
            'version'=>WPST_VERSION
        );
    }

    public static function blog_archive_presets(){
        return array(
            'editorial'=>array('title'=>'Editorial Clean','desc'=>'10 yazılık modern kart grid ve sayfalama.','preview'=>'blog-editorial.svg'),
            'magazine'=>array('title'=>'Modern Magazine','desc'=>'12 yazılık görsel odaklı magazine düzeni.','preview'=>'blog-magazine.svg'),
            'minimal'=>array('title'=>'Minimal Reading','desc'=>'20 yazılık sade tek kolon blog arşivi.','preview'=>'blog-minimal.svg'),
            'creative'=>array('title'=>'Creative Story','desc'=>'Ajans ve kişisel markalar için 2 kolon yaratıcı blog.','preview'=>'blog-creative.svg'),
            'corporate'=>array('title'=>'Corporate Insight','desc'=>'Kurumsal haber ve makaleler için 3 kolon arşiv.','preview'=>'blog-corporate.svg'),
            'tech'=>array('title'=>'Tech / SaaS','desc'=>'Teknoloji ve SaaS için modern magazine blog arşivi.','preview'=>'blog-tech.svg')
        );
    }

    public static function blog_single_presets(){
        return array(
            'editorial'=>array('title'=>'Editorial Clean','desc'=>'Başlık, meta, görsel, içerik, yazar ve yorum alanları.','preview'=>'single-editorial.svg'),
            'magazine'=>array('title'=>'Modern Magazine','desc'=>'Kategori, büyük başlık ve güçlü görsel hiyerarşisi.','preview'=>'single-magazine.svg'),
            'minimal'=>array('title'=>'Minimal Reading','desc'=>'Sade ve yüksek okunabilirlikli tek yazı tasarımı.','preview'=>'single-minimal.svg'),
            'feature'=>array('title'=>'Feature Story','desc'=>'Okuma ilerlemesi ve ilgili yazılarla güçlü editoryal detay sayfası.','preview'=>'single-feature.svg'),
            'dark'=>array('title'=>'Dark Insight','desc'=>'Teknoloji ve içerik siteleri için koyu premium tek yazı düzeni.','preview'=>'single-dark.svg')
        );
    }

    public static function create_blog_library_template(){
        if(!current_user_can('manage_options')) wp_die('Yetkiniz yok.');
        $type=isset($_GET['type'])?sanitize_key(wp_unslash($_GET['type'])):'archive';
        $style=isset($_GET['style'])?sanitize_key(wp_unslash($_GET['style'])):'editorial';
        if(!in_array($type,array('archive','single','category','tag','author','search'),true)) wp_die('Geçersiz şablon türü.');
        $presets=('single'===$type)?self::blog_single_presets():self::blog_archive_presets();
        if(empty($presets[$style])) wp_die('Blog şablonu bulunamadı.');
        check_admin_referer('wpst_create_blog_library_'.$type.'_'.$style);
        if(!did_action('elementor/loaded')||!class_exists('\\Elementor\\Plugin')) wp_die('Elementor etkin olmalıdır.');

        $item=$presets[$style];
        $post_id=wp_insert_post(array(
            'post_title'=>'WPSoft - '.self::blog_template_type_label($type).' - '.$item['title'],
            'post_type'=>'elementor_library',
            'post_status'=>'publish'
        ),true);
        if(is_wp_error($post_id)||!$post_id) wp_die('Şablon oluşturulamadı.');

        update_post_meta($post_id,'_elementor_edit_mode','builder');
        update_post_meta($post_id,'_elementor_template_type',('single'===$type?'single':'archive'));
        update_post_meta($post_id,'_wpst_blog_archive_context',$type);
        update_post_meta($post_id,'_elementor_version',defined('ELEMENTOR_VERSION')?ELEMENTOR_VERSION:'3.0.0');
        update_post_meta($post_id,'_wpst_blog_library_template','1');
        update_post_meta($post_id,'_wpst_blog_template_type',$type);
        update_post_meta($post_id,'_wpst_blog_template_style',$style);
        update_post_meta($post_id,'_wpst_blog_template_preview',$item['preview']);

        $data=('single'===$type)?self::single_blog_page_data($style):(('archive'===$type)?self::blog_page_data($style):self::archive_context_page_data($type,$style));
        update_post_meta($post_id,'_elementor_data',wp_slash(wp_json_encode($data)));

        wp_safe_redirect(admin_url('post.php?post='.$post_id.'&action=elementor'));
        exit;
    }

    private static function single_blog_page_data($style='editorial'){
        $progress=self::element('wpsoft-post-reading-progress',array('position'=>'top','show_percent'=>''));
        $title_left=self::element('wpsoft-post-title',array('tag'=>'h1','align'=>'left'));
        $title_center=self::element('wpsoft-post-title',array('tag'=>'h1','align'=>'center'));
        $meta=self::element('wpsoft-post-meta');
        $terms=self::element('wpsoft-post-terms');
        $excerpt=self::element('wpsoft-post-excerpt');
        $image=self::element('wpsoft-post-image',array('size'=>'full'));
        $content=self::element('wpsoft-post-content');
        $share=self::element('wpsoft-post-share');
        $author=self::element('wpsoft-post-author');
        $related=self::element('wpsoft-related-posts',array('title'=>'İlgili Yazılar','count'=>3,'match_by'=>'category','columns'=>'3','show_image'=>'yes','show_date'=>'yes'));
        $nav=self::element('wpsoft-post-navigation');
        $comments=self::element('wpsoft-post-comments');

        if('magazine'===$style){
            return array(
                self::container(array($terms,$meta),array(
                    'boxed_width'=>array('size'=>1180,'unit'=>'px'),
                    'padding'=>array('unit'=>'px','top'=>'56','right'=>'24','bottom'=>'18','left'=>'24','isLinked'=>false)
                )),
                self::container(array(
                    self::container(array($title_left,$excerpt),array(
                        'content_width'=>'full','width'=>array('unit'=>'%','size'=>48,'sizes'=>array()),
                        'padding'=>array('unit'=>'px','top'=>'12','right'=>'34','bottom'=>'34','left'=>'0','isLinked'=>false)
                    )),
                    self::container(array($image),array(
                        'content_width'=>'full','width'=>array('unit'=>'%','size'=>52,'sizes'=>array()),
                        'padding'=>array('unit'=>'px','top'=>'0','right'=>'0','bottom'=>'0','left'=>'0','isLinked'=>false),
                        'border_radius'=>array('unit'=>'px','top'=>'26','right'=>'26','bottom'=>'26','left'=>'26','isLinked'=>true),
                        'overflow'=>'hidden'
                    ))
                ),array(
                    'content_width'=>'boxed','boxed_width'=>array('size'=>1180,'unit'=>'px'),
                    'flex_direction'=>'row','align_items'=>'center','gap'=>array('unit'=>'px','size'=>28,'column'=>28,'row'=>28),
                    'padding'=>array('unit'=>'px','top'=>'10','right'=>'24','bottom'=>'58','left'=>'24','isLinked'=>false)
                )),
                self::container(array($content,$share,$author,$related,$nav,$comments),array(
                    'content_width'=>'boxed','boxed_width'=>array('size'=>860,'unit'=>'px'),
                    'padding'=>array('unit'=>'px','top'=>'40','right'=>'24','bottom'=>'92','left'=>'24','isLinked'=>false)
                ))
            );
        }

        if('minimal'===$style){
            return array(
                self::container(array($title_center,$meta),array(
                    'content_width'=>'boxed','boxed_width'=>array('size'=>760,'unit'=>'px'),
                    'padding'=>array('unit'=>'px','top'=>'96','right'=>'24','bottom'=>'36','left'=>'24','isLinked'=>false)
                )),
                self::container(array($image),array(
                    'content_width'=>'boxed','boxed_width'=>array('size'=>980,'unit'=>'px'),
                    'padding'=>array('unit'=>'px','top'=>'0','right'=>'24','bottom'=>'54','left'=>'24','isLinked'=>false)
                )),
                self::container(array($content,$terms,$author,$nav,$comments),array(
                    'content_width'=>'boxed','boxed_width'=>array('size'=>720,'unit'=>'px'),
                    'padding'=>array('unit'=>'px','top'=>'14','right'=>'24','bottom'=>'110','left'=>'24','isLinked'=>false)
                ))
            );
        }

        if('feature'===$style){
            return array(
                self::container(array($progress),array(
                    'content_width'=>'full','padding'=>array('unit'=>'px','top'=>'0','right'=>'0','bottom'=>'0','left'=>'0','isLinked'=>false)
                )),
                self::container(array($terms,$title_left,$excerpt,$meta),array(
                    'content_width'=>'boxed','boxed_width'=>array('size'=>1040,'unit'=>'px'),
                    'padding'=>array('unit'=>'px','top'=>'82','right'=>'24','bottom'=>'42','left'=>'24','isLinked'=>false)
                )),
                self::container(array($image),array(
                    'content_width'=>'full','padding'=>array('unit'=>'px','top'=>'0','right'=>'0','bottom'=>'0','left'=>'0','isLinked'=>false),
                    'overflow'=>'hidden'
                )),
                self::container(array($content,$share,$author),array(
                    'content_width'=>'boxed','boxed_width'=>array('size'=>800,'unit'=>'px'),
                    'padding'=>array('unit'=>'px','top'=>'64','right'=>'24','bottom'=>'54','left'=>'24','isLinked'=>false)
                )),
                self::container(array($related,$nav,$comments),array(
                    'content_width'=>'boxed','boxed_width'=>array('size'=>1120,'unit'=>'px'),
                    'background_background'=>'classic','background_color'=>'#f8fafc',
                    'padding'=>array('unit'=>'px','top'=>'56','right'=>'32','bottom'=>'86','left'=>'32','isLinked'=>false),
                    'border_radius'=>array('unit'=>'px','top'=>'32','right'=>'32','bottom'=>'32','left'=>'32','isLinked'=>true)
                ))
            );
        }

        if('dark'===$style){
            $dark=array('background_background'=>'classic','background_color'=>'#07111f');
            return array(
                self::container(array($progress),array_merge($dark,array(
                    'content_width'=>'full','padding'=>array('unit'=>'px','top'=>'0','right'=>'0','bottom'=>'0','left'=>'0','isLinked'=>false)
                ))),
                self::container(array($terms,$title_center,$meta,$excerpt),array_merge($dark,array(
                    'content_width'=>'boxed','boxed_width'=>array('size'=>980,'unit'=>'px'),
                    'padding'=>array('unit'=>'px','top'=>'88','right'=>'24','bottom'=>'46','left'=>'24','isLinked'=>false)
                ))),
                self::container(array($image),array_merge($dark,array(
                    'content_width'=>'boxed','boxed_width'=>array('size'=>1160,'unit'=>'px'),
                    'padding'=>array('unit'=>'px','top'=>'0','right'=>'24','bottom'=>'56','left'=>'24','isLinked'=>false),
                    'border_radius'=>array('unit'=>'px','top'=>'30','right'=>'30','bottom'=>'30','left'=>'30','isLinked'=>true),
                    'overflow'=>'hidden'
                ))),
                self::container(array($content,$terms,$share,$author),array_merge($dark,array(
                    'content_width'=>'boxed','boxed_width'=>array('size'=>800,'unit'=>'px'),
                    'padding'=>array('unit'=>'px','top'=>'34','right'=>'24','bottom'=>'54','left'=>'24','isLinked'=>false)
                ))),
                self::container(array($related,$nav,$comments),array(
                    'content_width'=>'boxed','boxed_width'=>array('size'=>1100,'unit'=>'px'),
                    'background_background'=>'classic','background_color'=>'#0d1b2a',
                    'padding'=>array('unit'=>'px','top'=>'54','right'=>'32','bottom'=>'88','left'=>'32','isLinked'=>false),
                    'border_radius'=>array('unit'=>'px','top'=>'28','right'=>'28','bottom'=>'28','left'=>'28','isLinked'=>true)
                ))
            );
        }

        /* Editorial Clean */
        return array(
            self::container(array($meta,$title_left,$excerpt),array(
                'content_width'=>'boxed','boxed_width'=>array('size'=>1020,'unit'=>'px'),
                'padding'=>array('unit'=>'px','top'=>'82','right'=>'24','bottom'=>'34','left'=>'24','isLinked'=>false)
            )),
            self::container(array($image),array(
                'content_width'=>'boxed','boxed_width'=>array('size'=>1120,'unit'=>'px'),
                'padding'=>array('unit'=>'px','top'=>'0','right'=>'24','bottom'=>'46','left'=>'24','isLinked'=>false)
            )),
            self::container(array($content,$terms,$share,$author,$related,$nav,$comments),array(
                'content_width'=>'boxed','boxed_width'=>array('size'=>820,'unit'=>'px'),
                'padding'=>array('unit'=>'px','top'=>'28','right'=>'24','bottom'=>'92','left'=>'24','isLinked'=>false)
            ))
        );
    }

    private static function blog_template_type_label($type){
        $labels=array('archive'=>'Blog Arşiv','single'=>'Tek Yazı','category'=>'Kategori Arşivi','tag'=>'Etiket Arşivi','author'=>'Yazar Arşivi','search'=>'Arama Sonuçları');
        return isset($labels[$type])?$labels[$type]:'Blog Arşiv';
    }

    private static function archive_context_page_data($context='category',$style='editorial'){
        $labels=array(
            'category'=>array('eyebrow'=>'KATEGORİ','preview'=>'Kategori Başlığı'),
            'tag'=>array('eyebrow'=>'ETİKET','preview'=>'Etiket Başlığı'),
            'author'=>array('eyebrow'=>'YAZAR','preview'=>'Yazar Yazıları'),
            'search'=>array('eyebrow'=>'ARAMA','preview'=>'Arama Sonuçları')
        );
        $cfg=isset($labels[$context])?$labels[$context]:$labels['category'];
        $hero=array(
            self::element('wpsoft-heading',array('eyebrow'=>$cfg['eyebrow'],'title'=>'','description'=>'')),
            self::element('wpsoft-archive-title',array('preview_title'=>$cfg['preview'],'tag'=>'h1')),
            self::element('wpsoft-archive-description',array('fallback_text'=>''))
        );
        if('author'===$context) $hero[]=self::element('wpsoft-archive-author');
        $layout=in_array($style,array('magazine','tech'),true)?'magazine':('minimal'===$style?'minimal':'cards');
        $cols=('minimal'===$style)?'1':('creative'===$style?'2':'3');
        $bg=('tech'===$style)?'#07111f':(('creative'===$style)?'#f5f3ff':'#ffffff');
        return array(
            self::container($hero,array('background_background'=>'classic','background_color'=>$bg,'padding'=>array('unit'=>'px','top'=>'72','right'=>'24','bottom'=>'28','left'=>'24','isLinked'=>false))),
            self::container(array(self::element('wpsoft-blog-posts',array('use_current_query'=>'yes','posts_per_page'=>12,'layout_style'=>$layout,'columns'=>$cols,'show_image'=>'yes','show_category'=>'yes','show_date'=>'yes','show_author'=>'yes','show_excerpt'=>'yes','pagination_type'=>'numbers'))),array('background_background'=>'classic','background_color'=>$bg,'padding'=>array('unit'=>'px','top'=>'20','right'=>'24','bottom'=>'80','left'=>'24','isLinked'=>false)))
        );
    }

    private static function blog_page_data($style='editorial'){
        $heading=array(
            'editorial'=>array('eyebrow'=>'BLOG','title'=>'Fikirler, rehberler ve güncel içerikler','description'=>'Yayınlanmış tüm yazıları modern ve okunabilir bir blog sayfasında keşfedin.'),
            'magazine'=>array('eyebrow'=>'MAGAZINE','title'=>'Gündem, içgörü ve öne çıkan yazılar','description'=>'Görsel odaklı modern dergi düzeni.'),
            'minimal'=>array('eyebrow'=>'JOURNAL','title'=>'Blog','description'=>'Sade, temiz ve içerik odaklı yazı listesi.'),
            'creative'=>array('eyebrow'=>'STORIES & IDEAS','title'=>'Düşünceler, projeler ve yaratıcı hikâyeler','description'=>'Ajanslar ve kişisel markalar için editorial blog görünümü.'),
            'corporate'=>array('eyebrow'=>'INSIGHTS','title'=>'Sektörel İçgörüler','description'=>'Kurumsal haberler, analizler ve uzman görüşleri.'),
            'tech'=>array('eyebrow'=>'TECH NOTES','title'=>'Ürün, teknoloji ve geliştirme notları','description'=>'Teknoloji ve SaaS içerikleri için kompakt bilgi akışı.')
        );
        $h=isset($heading[$style])?$heading[$style]:$heading['editorial'];

        $layout='cards'; $cols='3'; $count=10; $bg='#ffffff';
        if('magazine'===$style){$layout='magazine';$cols='3';$count=12;$bg='#f8fafc';}
        if('minimal'===$style){$layout='minimal';$cols='1';$count=20;$bg='#ffffff';}
        if('creative'===$style){$layout='cards';$cols='2';$count=10;$bg='#f5f3ff';}
        if('corporate'===$style){$layout='cards';$cols='3';$count=9;$bg='#eff6ff';}
        if('tech'===$style){$layout='magazine';$cols='3';$count=12;$bg='#07111f';}

        return array(
            self::container(array(
                self::element('wpsoft-heading',array(
                    'eyebrow'=>$h['eyebrow'],
                    'title'=>$h['title'],
                    'description'=>$h['description']
                ))
            ),array(
                'background_background'=>'classic','background_color'=>$bg,
                'padding'=>array('unit'=>'px','top'=>'72','right'=>'24','bottom'=>'28','left'=>'24','isLinked'=>false)
            )),
            self::container(array(
                self::element('wpsoft-blog-posts',array(
                    'posts_per_page'=>$count,
                    'layout_style'=>$layout,
                    'columns'=>$cols,
                    'show_image'=>'yes',
                    'show_category'=>'yes',
                    'show_date'=>'yes',
                    'show_author'=>'yes',
                    'show_excerpt'=>'yes',
                    'excerpt_length'=>22,
                    'pagination'=>'yes',
                    'button_text'=>'Yazıyı Oku'
                ))
            ),array(
                'background_background'=>'classic','background_color'=>$bg,
                'padding'=>array('unit'=>'px','top'=>'20','right'=>'24','bottom'=>'80','left'=>'24','isLinked'=>false)
            ))
        );
    }


    private static function widget_item($key,$title,$desc,$widget,$settings,$preview){
        return array('key'=>$key,'title'=>$title,'desc'=>$desc,'preview_image'=>self::preview($preview),'data'=>self::element($widget,$settings));
    }

    private static function services_section($accent){
        $items=array(
            array('icon'=>array('value'=>'fas fa-laptop-code','library'=>'fa-solid'),'title'=>'Web Tasarım','text'=>'Modern, hızlı ve kullanıcı odaklı profesyonel çözüm.','tag'=>'01'),
            array('icon'=>array('value'=>'fas fa-shopping-cart','library'=>'fa-solid'),'title'=>'E-Ticaret','text'=>'Satış ve dönüşüm odaklı modern mağaza deneyimi.','tag'=>'02'),
            array('icon'=>array('value'=>'fas fa-chart-line','library'=>'fa-solid'),'title'=>'SEO & Performans','text'=>'Hız, görünürlük ve sürdürülebilir büyüme odaklı optimizasyon.','tag'=>'03')
        );
        return array(
            self::container(array(self::element('wpsoft-heading',array(
                'eyebrow'=>'Hizmetlerimiz','title'=>'İşletmeniz için modern çözümler',
                'description'=>'Dijital ihtiyaçlarınızı tek noktadan karşılayan profesyonel hizmetler.','accent'=>$accent
            )))),
            self::container(array(self::element('wpsoft-service-cards-pro',array('items'=>$items,'action_text'=>'İncele')))
        ));
    }


    private static function ready_page_blocks($key,$accent,$sample){
        $blocks=array();

        $blocks[]=self::container(array(
            self::element('wpsoft-info-strip',array(
                'items'=>array(
                    array('title'=>'Mobil Uyumlu','text'=>'Tüm ekranlara uyumlu modern yapı'),
                    array('title'=>'Hızlı Altyapı','text'=>'Performans odaklı sayfa düzeni'),
                    array('title'=>'Kolay Düzenleme','text'=>'Elementor üzerinden tamamen özelleştirilebilir')
                )
            ))
        ),array('padding'=>array('unit'=>'px','top'=>'20','right'=>'24','bottom'=>'20','left'=>'24','isLinked'=>false)));

        if(in_array($key,array('corporate-premium','industrial-premium','machinery-premium','architecture-premium'),true)){
            $blocks[]=self::container(array(
                self::element('wpsoft-heading',array('eyebrow'=>'NEDEN BİZ','title'=>'Güven, uzmanlık ve sürdürülebilir kalite','description'=>'İhtiyaca özel çözümler, güçlü teknik altyapı ve satış sonrası destek yaklaşımı.')),
                self::element('wpsoft-number-cards')
            ));
        }

        if(in_array($key,array('ecommerce-premium','saas-premium'),true)){
            $blocks[]=self::container(array(
                self::element('wpsoft-heading',array('eyebrow'=>'AVANTAJLAR','title'=>'Daha hızlı karar, daha iyi kullanıcı deneyimi','description'=>'Dönüşüm odaklı içerik akışı ve güçlü ürün sunumu.')),
                self::element('wpsoft-feature-list')
            ));
        }

        if(in_array($key,array('hotel-premium','restaurant-premium'),true)){
            $blocks[]=self::container(array(
                self::element('wpsoft-heading',array('eyebrow'=>'DENEYİM','title'=>'Her detay misafir deneyimi için tasarlandı','description'=>'Mekân, hizmet ve deneyimi güçlü görsellerle anlatan hazır bölüm.')),
                self::element('wpsoft-image-carousel',array('gallery'=>array(array('url'=>self::demo_for($key),'id'=>0),array('url'=>self::demo('corporate.svg'),'id'=>0),array('url'=>self::demo('agency.svg'),'id'=>0),array('url'=>self::demo('hotel.svg'),'id'=>0))))
            ));
        }

        if($key==='clinic-premium'){
            $blocks[]=self::container(array(
                self::element('wpsoft-heading',array('eyebrow'=>'UZMANLIKLAR','title'=>'İhtiyacınıza uygun uzmanlık alanları','description'=>'Hizmetlerinizi anlaşılır ve güven veren kartlarla sunun.')),
                self::element('wpsoft-icon-grid')
            ));
        }

        if($key==='agency-premium'){
            $blocks[]=self::container(array(
                self::element('wpsoft-heading',array('eyebrow'=>'ÇALIŞMA ŞEKLİMİZ','title'=>'Fikirden yayına net ve yaratıcı süreç','description'=>'Strateji, tasarım, üretim ve optimizasyon adımları.')),
                self::element('wpsoft-icon-steps')
            ));
        }

        return $blocks;
    }

    private static function page_v3114_data($key,$tpl,$sample,$title,$cta){
        $accent=isset($tpl['accent'])?$tpl['accent']:'#2563eb';

        $section=function($widget,$settings=array(),$bg='#ffffff',$pad=56){
            return self::container(
                array(self::element($widget,$settings)),
                array(
                    'background_background'=>'classic',
                    'background_color'=>$bg,
                    'padding'=>array('unit'=>'px','top'=>(string)$pad,'right'=>'24','bottom'=>(string)$pad,'left'=>'24','isLinked'=>false)
                )
            );
        };

        $pair=function($left,$right,$bg='#ffffff',$widths=array(50,50),$pad=56){
            $cols=array();
            foreach(array($left,$right) as $i=>$el){
                $cols[]=array(
                    'id'=>self::uid(),'elType'=>'container','isInner'=>true,
                    'settings'=>array(
                        'content_width'=>'full',
                        'width'=>array('unit'=>'%','size'=>$widths[$i],'sizes'=>array()),
                        'width_tablet'=>array('unit'=>'%','size'=>50,'sizes'=>array()),
                        'width_mobile'=>array('unit'=>'%','size'=>100,'sizes'=>array()),
                        'padding'=>array('unit'=>'px','top'=>'0','right'=>'10','bottom'=>'0','left'=>'10','isLinked'=>false)
                    ),
                    'elements'=>array($el)
                );
            }
            return self::container($cols,array(
                'background_background'=>'classic','background_color'=>$bg,
                'flex_direction'=>'row','flex_wrap'=>'wrap','align_items'=>'stretch',
                'padding'=>array('unit'=>'px','top'=>(string)$pad,'right'=>'14','bottom'=>(string)$pad,'left'=>'14','isLinked'=>false)
            ));
        };

        $hero=function($type,$eyebrow,$headline,$text,$button='Teklif Al') use($sample,$accent){
            if('industry'===$type)return self::element('wpsoft-hero-industry',array('hero_radius'=>array('size'=>30),'eyebrow'=>$eyebrow,'title'=>$headline,'text'=>$text,'image'=>array('url'=>$sample),'button_text'=>$button,'button_url'=>array('url'=>'#iletisim')));
            if('hospitality'===$type)return self::element('wpsoft-hero-hospitality',array('eyebrow'=>$eyebrow,'title'=>$headline,'text'=>$text,'image'=>array('url'=>$sample),'button_text'=>$button,'button_url'=>array('url'=>'#iletisim')));
            if('medical'===$type)return self::element('wpsoft-hero-medical',array('hero_radius'=>array('size'=>30),'eyebrow'=>$eyebrow,'title'=>$headline,'text'=>$text,'image'=>array('url'=>$sample),'button_text'=>$button,'button_url'=>array('url'=>'#iletisim')));
            if('commerce'===$type)return self::element('wpsoft-hero-commerce',array('badge'=>$eyebrow,'title'=>$headline,'text'=>$text,'image'=>array('url'=>$sample),'button_text'=>$button,'button_url'=>array('url'=>'#')));
            if('saas'===$type)return self::element('wpsoft-hero-saas',array('badge'=>$eyebrow,'title'=>$headline,'text'=>$text,'image'=>array('url'=>$sample),'primary_text'=>$button,'primary_url'=>array('url'=>'#iletisim'),'secondary_text'=>'Demo / Detay','secondary_url'=>array('url'=>'#')));
            if('bento'===$type)return self::element('wpsoft-hero-bento',array('hero_radius'=>array('size'=>30),'eyebrow'=>$eyebrow,'title'=>$headline,'text'=>$text,'image'=>array('url'=>$sample),'button_text'=>$button,'button_url'=>array('url'=>'#iletisim')));
            if('spotlight'===$type)return self::element('wpsoft-hero-spotlight',array('eyebrow'=>$eyebrow,'title'=>$headline,'text'=>$text,'button_text'=>$button,'button_url'=>array('url'=>'#iletisim')));
            return self::element('wpsoft-hero-split-modern',array('eyebrow'=>$eyebrow,'title'=>$headline,'text'=>$text,'image'=>array('url'=>$sample),'primary_text'=>$button,'primary_url'=>array('url'=>'#iletisim'),'secondary_text'=>'Daha Fazla','secondary_url'=>array('url'=>'#')));
        };

        $dark='#07111f'; $soft='#f8fafc'; $warm='#fff7ed'; $mint='#f0fdfa'; $violet='#f5f3ff'; $rose='#fff1f2'; $blue='#eff6ff'; $lime='#f7fee7';

        switch($key){

        case 'corporate':
            return array(
                $section('wpsoft-hero-split-modern',array('eyebrow'=>'KURUMSAL','title'=>$title,'text'=>'Güven, uzmanlık ve net değer önerisini ilk ekranda sunan kurumsal deneyim.','image'=>array('url'=>$sample),'primary_text'=>'Teklif Al','primary_url'=>array('url'=>'#iletisim'),'secondary_text'=>'Hizmetler','secondary_url'=>array('url'=>'#hizmetler')),$blue,28),
                $section('wpsoft-logo-marquee',array(),$soft,30),
                $section('wpsoft-service-cards-pro',array(),$soft,60),
                $pair(self::element('wpsoft-image-text',array('eyebrow'=>'YAKLAŞIM','title'=>'Strateji, tasarım ve teknoloji tek yapıda','image'=>array('url'=>$sample))),self::element('wpsoft-stats-grid'),$blue,array(58,42),64),
                $section('wpsoft-process-steps-pro',array(),$soft,58),
                $section('wpsoft-testimonial-slider',array(),$blue,60),
                $section('wpsoft-faq',array(),$soft,56),
                $section('wpsoft-cta',array('title'=>'Yeni projenizi konuşalım','button_text'=>'Bize Ulaşın','button_url'=>array('url'=>'#iletisim'),'bg'=>'#0f172a'),$soft,34)
            );

        case 'agency':
            return array(
                $section('wpsoft-hero-bento',array('eyebrow'=>'CREATIVE STUDIO','title'=>$title,'text'=>'Cesur fikirleri güçlü dijital deneyimlere dönüştüren yaratıcı ana sayfa.','image'=>array('url'=>$sample),'button_text'=>'Projeyi Başlat','button_url'=>array('url'=>'#iletisim')),$violet,24),
                $section('wpsoft-marquee-text',array(),$dark,24),
                $section('wpsoft-scroll-reveal-text',array('eyebrow'=>'OUR POINT OF VIEW','text'=>'Strateji tasarım motion ve teknolojiyi tek deneyimde birleştiriyoruz.'),$soft,64),
                $section('wpsoft-image-cascade',array('image_one'=>array('url'=>$sample),'image_two'=>array('url'=>self::demo('corporate.svg')),'image_three'=>array('url'=>self::demo('agency.svg'))),$violet,58),
                $section('wpsoft-service-cards-pro',array(),$soft,60),
                $section('wpsoft-hover-reveal',array(),$dark,58),
                $pair(self::element('wpsoft-animated-counter'),self::element('wpsoft-testimonial-slider'),$violet,array(40,60),58),
                $section('wpsoft-morphing-cta',array('eyebrow'=>'NEXT PROJECT','title'=>'Birlikte dikkat çeken bir şey üretelim','button_text'=>'Başlayalım','button_url'=>array('url'=>'#iletisim')),$soft,54)
            );

        case 'service':
            return array(
                $section('wpsoft-hero-split-modern',array('eyebrow'=>'HİZMET','title'=>$title,'text'=>'Tek hizmeti net fayda, süreç ve güçlü sosyal kanıtla anlatan dönüşüm odaklı sayfa.','image'=>array('url'=>$sample),'primary_text'=>'Teklif Al','primary_url'=>array('url'=>'#iletisim'),'secondary_text'=>'Nasıl Çalışır?','secondary_url'=>array('url'=>'#surec')),$mint,26),
                $section('wpsoft-trust-badges',array(),$soft,30),
                $section('wpsoft-feature-mosaic',array('title'=>'Hizmetin sağladığı temel faydalar','image'=>array('url'=>$sample)),$mint,58),
                $section('wpsoft-process-steps-pro',array(),$soft,56),
                $pair(self::element('wpsoft-pricing'),self::element('wpsoft-testimonial-slider'),$mint,array(44,56),58),
                $section('wpsoft-faq',array(),$soft,50),
                $section('wpsoft-cta',array('title'=>'İhtiyacınızı birlikte netleştirelim','button_text'=>'Teklif İste','button_url'=>array('url'=>'#iletisim'),'bg'=>'#0f766e'),$mint,30)
            );

        case 'contact':
            return array(
                $section('wpsoft-gradient-heading',array('eyebrow'=>'İLETİŞİM','title'=>$title,'text'=>'Hızlı iletişim, teklif ve destek talepleri için sade ve güven veren sayfa.'),$dark,48),
                $section('wpsoft-contact-cards',array(),$soft,54),
                $pair(self::element('wpsoft-image-text',array('eyebrow'=>'BİZE ULAŞIN','title'=>'Doğru kanaldan hızlıca iletişim kurun','image'=>array('url'=>$sample))),self::element('wpsoft-faq'),$blue,array(45,55),58),
                $section('wpsoft-trust-badges',array(),$soft,34),
                $section('wpsoft-cta',array('title'=>'Projeniz hakkında kısa bir mesaj bırakın','button_text'=>'İletişime Geç','button_url'=>array('url'=>'#iletisim'),'bg'=>'#059669'),$soft,34)
            );

        case 'industrial':
            return array(
                $section('wpsoft-hero-industry',array('eyebrow'=>'ENDÜSTRİYEL GÜÇ','title'=>$title,'text'=>'Üretim kabiliyeti, makina parkuru ve teknik uzmanlığı güvenle sunun.','image'=>array('url'=>$sample),'button_text'=>'Teknik Teklif Al','button_url'=>array('url'=>'#iletisim')),$dark,20),
                $section('wpsoft-trust-badges',array(),$soft,28),
                $section('wpsoft-feature-mosaic',array('title'=>'Üretim altyapısı ve teknik kabiliyetler','image'=>array('url'=>$sample)),$dark,58),
                $section('wpsoft-service-cards-pro',array(),$soft,58),
                $pair(self::element('wpsoft-stats-grid'),self::element('wpsoft-tabs-modern'),$blue,array(38,62),58),
                $section('wpsoft-process-steps-pro',array(),$soft,56),
                $section('wpsoft-logo-marquee',array(),$blue,30),
                $section('wpsoft-cta',array('title'=>'Üretim ihtiyacınız için teknik ekibimizle görüşün','button_text'=>'Teknik Bilgi Al','button_url'=>array('url'=>'#iletisim'),'bg'=>'#0b1120'),$soft,32)
            );

        case 'construction':
            return array(
                $section('wpsoft-hero-split-modern',array('eyebrow'=>'MİMARLIK & İNŞAAT','title'=>$title,'text'=>'Projeleri görsel kalite, süreç şeffaflığı ve güven unsurlarıyla sunun.','image'=>array('url'=>$sample),'primary_text'=>'Projeleri Gör','primary_url'=>array('url'=>'#projeler'),'secondary_text'=>'Teklif Al','secondary_url'=>array('url'=>'#iletisim')),$warm,24),
                $section('wpsoft-image-cascade',array('image_one'=>array('url'=>$sample),'image_two'=>array('url'=>self::demo('architecture.svg')),'image_three'=>array('url'=>self::demo('corporate.svg'))),$soft,58),
                $section('wpsoft-portfolio',array(),$warm,60),
                $pair(self::element('wpsoft-number-cards'),self::element('wpsoft-process-steps-pro'),$soft,array(38,62),58),
                $section('wpsoft-image-text',array('eyebrow'=>'YAKLAŞIM','title'=>'Malzeme, işlev ve estetik arasında denge','image'=>array('url'=>$sample)),$warm,58),
                $section('wpsoft-testimonial-slider',array(),$soft,54),
                $section('wpsoft-cta',array('title'=>'Yeni projenizi birlikte planlayalım','button_text'=>'Proje Talebi','button_url'=>array('url'=>'#iletisim'),'bg'=>'#7c4a03'),$warm,34)
            );

        case 'hotel':
            return array(
                $section('wpsoft-hero-hospitality',array('eyebrow'=>'STAY DIFFERENT','title'=>$title,'text'=>'Konaklama deneyimini görsel hikâye ve doğrudan rezervasyon akışıyla sunun.','image'=>array('url'=>$sample),'button_text'=>'Rezervasyon Yap','button_url'=>array('url'=>'#rezervasyon')),$mint,18),
                $section('wpsoft-booking-strip',array('button_text'=>'Müsaitliği Kontrol Et','button_url'=>array('url'=>'#rezervasyon')),$soft,22),
                $section('wpsoft-image-carousel',array('gallery'=>array(array('url'=>$sample,'id'=>0),array('url'=>self::demo('hotel.svg'),'id'=>0),array('url'=>self::demo('travel.svg'),'id'=>0))),$mint,56),
                $section('wpsoft-service-cards-pro',array(),$soft,58),
                $pair(self::element('wpsoft-image-text',array('eyebrow'=>'DENEYİM','title'=>'Konforun ötesinde unutulmaz anlar','image'=>array('url'=>$sample))),self::element('wpsoft-trust-badges'),$mint,array(62,38),58),
                $section('wpsoft-testimonial-slider',array(),$soft,52),
                $section('wpsoft-cta',array('title'=>'Doğrudan rezervasyon avantajlarını keşfedin','button_text'=>'Rezervasyon','button_url'=>array('url'=>'#rezervasyon'),'bg'=>'#0f766e'),$mint,30)
            );

        case 'restaurant':
            return array(
                $section('wpsoft-hero-hospitality',array('eyebrow'=>'TASTE THE STORY','title'=>$title,'text'=>'Mekân, menü ve şef hikâyesini rezervasyona bağlayan gastronomi deneyimi.','image'=>array('url'=>$sample),'button_text'=>'Masa Ayırt','button_url'=>array('url'=>'#rezervasyon')),$rose,18),
                $section('wpsoft-booking-strip',array('checkin'=>'Bugün','checkout'=>'20:00','guests'=>'2 Kişi','button_text'=>'Masa Ayırt','button_url'=>array('url'=>'#rezervasyon')),$soft,20),
                $section('wpsoft-image-cascade',array('image_one'=>array('url'=>$sample),'image_two'=>array('url'=>self::demo('restaurant.svg')),'image_three'=>array('url'=>self::demo('agency.svg'))),$rose,58),
                $section('wpsoft-tabs-modern',array(),$soft,56),
                $section('wpsoft-service-cards-pro',array(),$rose,56),
                $pair(self::element('wpsoft-image-text',array('eyebrow'=>'ŞEFİN HİKÂYESİ','title'=>'Mevsimsel ürünler, özgün yorumlar','image'=>array('url'=>$sample))),self::element('wpsoft-testimonial-slider'),$soft,array(52,48),58),
                $section('wpsoft-cta',array('title'=>'Bu akşam için masanızı ayırtın','button_text'=>'Rezervasyon','button_url'=>array('url'=>'#rezervasyon'),'bg'=>'#881337'),$rose,30)
            );

        case 'realestate':
            return array(
                $section('wpsoft-hero-split-modern',array('eyebrow'=>'PROPERTY','title'=>$title,'text'=>'Portföyleri güçlü görsel, güven ve hızlı aksiyonlarla öne çıkarın.','image'=>array('url'=>$sample),'primary_text'=>'Portföyü Gör','primary_url'=>array('url'=>'#portfoy'),'secondary_text'=>'Danışmana Ulaş','secondary_url'=>array('url'=>'#iletisim')),$blue,24),
                $section('wpsoft-trust-badges',array(),$soft,28),
                $section('wpsoft-portfolio',array(),$blue,58),
                $section('wpsoft-number-cards',array(),$soft,48),
                $pair(self::element('wpsoft-image-text',array('eyebrow'=>'BÖLGE UZMANLIĞI','title'=>'Doğru yatırım için yerel içgörü','image'=>array('url'=>$sample))),self::element('wpsoft-contact-cards'),$blue,array(58,42),58),
                $section('wpsoft-testimonial-slider',array(),$soft,52),
                $section('wpsoft-cta',array('title'=>'Aradığınız gayrimenkulü birlikte bulalım','button_text'=>'Danışmana Ulaş','button_url'=>array('url'=>'#iletisim'),'bg'=>'#1e3a8a'),$blue,30)
            );

        case 'health':
            return array(
                $section('wpsoft-hero-medical',array('eyebrow'=>'SAĞLIKTA GÜVEN','title'=>$title,'text'=>'Uzmanlık, hasta güveni ve kolay randevu akışını tek deneyimde birleştirin.','image'=>array('url'=>$sample),'button_text'=>'Randevu Al','button_url'=>array('url'=>'#iletisim')),$mint,20),
                $section('wpsoft-trust-badges',array(),$soft,28),
                $section('wpsoft-service-cards-pro',array(),$mint,54),
                $pair(self::element('wpsoft-image-text',array('eyebrow'=>'UZMANLIK','title'=>'Hasta odaklı modern yaklaşım','image'=>array('url'=>$sample))),self::element('wpsoft-before-after'),$soft,array(54,46),58),
                $section('wpsoft-process-steps-pro',array(),$mint,54),
                $section('wpsoft-testimonial-slider',array(),$soft,52),
                $pair(self::element('wpsoft-contact-cards'),self::element('wpsoft-faq'),$mint,array(45,55),54)
            );

        case 'automotive':
            return array(
                $section('wpsoft-hero-industry',array('eyebrow'=>'AUTOMOTIVE','title'=>$title,'text'=>'Servis, bakım ve teknik uzmanlığı güçlü koyu görsel sistemle sunun.','image'=>array('url'=>$sample),'button_text'=>'Servis Talebi','button_url'=>array('url'=>'#iletisim')),$dark,18),
                $section('wpsoft-info-strip',array(),$soft,20),
                $section('wpsoft-service-cards-pro',array(),$dark,54),
                $pair(self::element('wpsoft-feature-mosaic',array('title'=>'Servis altyapısı ve teknik kapasite','image'=>array('url'=>$sample))),self::element('wpsoft-stats-grid'),$soft,array(64,36),58),
                $section('wpsoft-process-steps-pro',array(),$dark,54),
                $section('wpsoft-testimonial-slider',array(),$soft,50),
                $section('wpsoft-cta',array('title'=>'Aracınız için hızlı servis planlayın','button_text'=>'Randevu Oluştur','button_url'=>array('url'=>'#iletisim'),'bg'=>'#111827'),$soft,30)
            );

        case 'software':
            return array(
                $section('wpsoft-hero-saas',array('badge'=>'PRODUCT 3.0','title'=>$title,'text'=>'Ürünü net fayda, güçlü demo ve güven unsurlarıyla anlatan SaaS deneyimi.','image'=>array('url'=>$sample),'primary_text'=>'Ücretsiz Başla','primary_url'=>array('url'=>'#iletisim'),'secondary_text'=>'Demo İzle','secondary_url'=>array('url'=>'#')),$dark,18),
                $section('wpsoft-logo-marquee',array(),$soft,26),
                $section('wpsoft-feature-mosaic',array('title'=>'Tek platformda daha hızlı iş akışı','image'=>array('url'=>$sample)),$violet,58),
                $section('wpsoft-tabs-modern',array(),$soft,54),
                $pair(self::element('wpsoft-icon-orbit'),self::element('wpsoft-animated-counter'),$violet,array(58,42),58),
                $section('wpsoft-pricing',array(),$soft,56),
                $section('wpsoft-testimonial-slider',array(),$violet,52),
                $section('wpsoft-cta',array('title'=>'Dakikalar içinde başlayın','button_text'=>'Ücretsiz Başla','button_url'=>array('url'=>'#iletisim'),'bg'=>'#4c1d95'),$soft,30)
            );

        case 'law':
            return array(
                $section('wpsoft-hero-split-modern',array('eyebrow'=>'HUKUK & DANIŞMANLIK','title'=>$title,'text'=>'Uzmanlık, gizlilik ve güven duygusunu önceliklendiren profesyonel deneyim.','image'=>array('url'=>$sample),'primary_text'=>'Görüşme Talebi','primary_url'=>array('url'=>'#iletisim'),'secondary_text'=>'Uzmanlıklar','secondary_url'=>array('url'=>'#')),$soft,26),
                $section('wpsoft-trust-badges',array(),$blue,28),
                $section('wpsoft-service-cards-pro',array(),$soft,54),
                $pair(self::element('wpsoft-image-text',array('eyebrow'=>'YAKLAŞIM','title'=>'Hukuki süreçlerde açık ve stratejik iletişim','image'=>array('url'=>$sample))),self::element('wpsoft-number-cards'),$blue,array(58,42),58),
                $section('wpsoft-team-carousel-pro',array(),$soft,56),
                $section('wpsoft-faq',array(),$blue,50),
                $section('wpsoft-cta',array('title'=>'Dosyanızı uzman ekiple değerlendirin','button_text'=>'Görüşme Planla','button_url'=>array('url'=>'#iletisim'),'bg'=>'#172554'),$soft,30)
            );

        case 'education':
            return array(
                $section('wpsoft-hero-bento',array('eyebrow'=>'LEARN & GROW','title'=>$title,'text'=>'Programları, eğitmenleri ve öğrenme sonuçlarını dinamik bir yapıda sunun.','image'=>array('url'=>$sample),'button_text'=>'Programları İncele','button_url'=>array('url'=>'#')),$violet,24),
                $section('wpsoft-logo-marquee',array(),$soft,24),
                $section('wpsoft-card-carousel',array(),$violet,54),
                $section('wpsoft-feature-mosaic',array('title'=>'Öğrenme yolculuğunu kolaylaştıran yapı','image'=>array('url'=>$sample)),$soft,58),
                $section('wpsoft-team-carousel-pro',array(),$violet,54),
                $section('wpsoft-process-steps-pro',array(),$soft,54),
                $section('wpsoft-testimonial-slider',array(),$violet,50),
                $section('wpsoft-cta',array('title'=>'Yeni döneme bugün başlayın','button_text'=>'Kayıt Ol','button_url'=>array('url'=>'#iletisim'),'bg'=>'#6d28d9'),$soft,30)
            );

        case 'beauty':
            return array(
                $section('wpsoft-hero-medical',array('eyebrow'=>'BEAUTY STUDIO','title'=>$title,'text'=>'Estetik, bakım ve premium hizmetleri yumuşak görsel dille sunun.','image'=>array('url'=>$sample),'button_text'=>'Randevu Al','button_url'=>array('url'=>'#iletisim')),$rose,20),
                $section('wpsoft-service-cards-pro',array(),$soft,52),
                $section('wpsoft-before-after',array(),$rose,56),
                $pair(self::element('wpsoft-image-cascade',array('image_one'=>array('url'=>$sample),'image_two'=>array('url'=>self::demo('health.svg')),'image_three'=>array('url'=>self::demo('agency.svg')))),self::element('wpsoft-trust-badges'),$soft,array(62,38),58),
                $section('wpsoft-testimonial-slider',array(),$rose,50),
                $section('wpsoft-contact-cards',array(),$soft,48),
                $section('wpsoft-cta',array('title'=>'Kendiniz için zaman ayırın','button_text'=>'Randevu Oluştur','button_url'=>array('url'=>'#iletisim'),'bg'=>'#9d174d'),$rose,30)
            );

        case 'logistics':
            return array(
                $section('wpsoft-hero-industry',array('eyebrow'=>'GLOBAL LOGISTICS','title'=>$title,'text'=>'Hatlar, operasyon gücü ve takip kabiliyetini kurumsal güvenle sunun.','image'=>array('url'=>$sample),'button_text'=>'Teklif Al','button_url'=>array('url'=>'#iletisim')),$dark,18),
                $section('wpsoft-stats-grid',array(),$soft,34),
                $section('wpsoft-service-cards-pro',array(),$blue,54),
                $pair(self::element('wpsoft-image-text',array('eyebrow'=>'OPERASYON','title'=>'Uçtan uca planlanan taşıma süreçleri','image'=>array('url'=>$sample))),self::element('wpsoft-process-steps-pro'),$soft,array(48,52),58),
                $section('wpsoft-logo-marquee',array(),$blue,26),
                $section('wpsoft-testimonial-slider',array(),$soft,48),
                $section('wpsoft-cta',array('title'=>'Gönderiniz için hızlı teklif alın','button_text'=>'Navlun Teklifi','button_url'=>array('url'=>'#iletisim'),'bg'=>'#0f172a'),$blue,30)
            );

        case 'energy':
            return array(
                $section('wpsoft-hero-split-modern',array('eyebrow'=>'CLEAN ENERGY','title'=>$title,'text'=>'Sürdürülebilirlik, teknik veri ve yatırım geri dönüşünü net biçimde anlatın.','image'=>array('url'=>$sample),'primary_text'=>'Keşif Talebi','primary_url'=>array('url'=>'#iletisim'),'secondary_text'=>'Çözümler','secondary_url'=>array('url'=>'#')),$lime,24),
                $section('wpsoft-trust-badges',array(),$soft,28),
                $section('wpsoft-feature-mosaic',array('title'=>'Enerji verimliliğini görünür hale getirin','image'=>array('url'=>$sample)),$lime,58),
                $pair(self::element('wpsoft-animated-counter'),self::element('wpsoft-service-cards-pro'),$soft,array(35,65),58),
                $section('wpsoft-process-steps-pro',array(),$lime,54),
                $section('wpsoft-logo-marquee',array(),$soft,24),
                $section('wpsoft-cta',array('title'=>'Enerji dönüşümünüzü birlikte planlayalım','button_text'=>'Ücretsiz Keşif','button_url'=>array('url'=>'#iletisim'),'bg'=>'#14532d'),$lime,30)
            );

        case 'finance':
            return array(
                $section('wpsoft-hero-split-modern',array('eyebrow'=>'FINANCE & ADVISORY','title'=>$title,'text'=>'Rakamları sadeleştiren, güven veren ve aksiyona yönlendiren finansal deneyim.','image'=>array('url'=>$sample),'primary_text'=>'Danışmanlık Al','primary_url'=>array('url'=>'#iletisim'),'secondary_text'=>'Hizmetler','secondary_url'=>array('url'=>'#')),$mint,24),
                $section('wpsoft-trust-badges',array(),$soft,26),
                $pair(self::element('wpsoft-number-cards'),self::element('wpsoft-service-cards-pro'),$mint,array(35,65),56),
                $section('wpsoft-process-steps-pro',array(),$soft,52),
                $section('wpsoft-testimonial-slider',array(),$mint,50),
                $pair(self::element('wpsoft-faq'),self::element('wpsoft-contact-cards'),$soft,array(58,42),54),
                $section('wpsoft-cta',array('title'=>'Finansal süreçlerinizi birlikte sadeleştirelim','button_text'=>'Görüşme Talebi','button_url'=>array('url'=>'#iletisim'),'bg'=>'#115e59'),$mint,30)
            );

        case 'event':
            return array(
                $section('wpsoft-hero-spotlight',array('eyebrow'=>'EVENT EXPERIENCE','title'=>$title,'text'=>'Etkinliği daha başlamadan heyecan yaratan güçlü görsel deneyim.','button_text'=>'Tarihleri Gör','button_url'=>array('url'=>'#')),$dark,18),
                $section('wpsoft-marquee-text',array(),$violet,20),
                $section('wpsoft-image-cascade',array('image_one'=>array('url'=>$sample),'image_two'=>array('url'=>self::demo('agency.svg')),'image_three'=>array('url'=>self::demo('restaurant.svg'))),$dark,56),
                $section('wpsoft-card-carousel',array(),$violet,52),
                $pair(self::element('wpsoft-animated-counter'),self::element('wpsoft-testimonial-slider'),$dark,array(36,64),56),
                $section('wpsoft-cta',array('title'=>'Bir sonraki etkinliği birlikte tasarlayalım','button_text'=>'Teklif Al','button_url'=>array('url'=>'#iletisim'),'bg'=>'#581c87'),$violet,30)
            );

        case 'personal':
            return array(
                $section('wpsoft-hero-split-modern',array('eyebrow'=>'PERSONAL BRAND','title'=>$title,'text'=>'Uzmanlığınızı, yaklaşımınızı ve güveninizi sade ama güçlü biçimde anlatın.','image'=>array('url'=>$sample),'primary_text'=>'Görüşme Planla','primary_url'=>array('url'=>'#iletisim'),'secondary_text'=>'Hakkımda','secondary_url'=>array('url'=>'#')),$soft,24),
                $section('wpsoft-scroll-reveal-text',array('eyebrow'=>'MY APPROACH','text'=>'Deneyimi içgörüye içgörüyü uygulanabilir sonuçlara dönüştürüyorum.'),$dark,58),
                $pair(self::element('wpsoft-image-text',array('eyebrow'=>'HAKKIMDA','title'=>'Deneyim, odak ve sonuç','image'=>array('url'=>$sample))),self::element('wpsoft-number-cards'),$soft,array(60,40),56),
                $section('wpsoft-service-cards-pro',array(),$blue,54),
                $section('wpsoft-testimonial-slider',array(),$soft,50),
                $section('wpsoft-morphing-cta',array('eyebrow'=>'LET’S TALK','title'=>'Birlikte çalışmayı konuşalım','button_text'=>'Görüşme Planla','button_url'=>array('url'=>'#iletisim')),$blue,48)
            );

        case 'dentist':
            return array(
                $section('wpsoft-hero-medical',array('eyebrow'=>'DENTAL CARE','title'=>$title,'text'=>'Tedavi alanları, uzmanlık ve hasta konforunu güven veren bir yapıda sunun.','image'=>array('url'=>$sample),'button_text'=>'Randevu Al','button_url'=>array('url'=>'#iletisim')),$blue,20),
                $section('wpsoft-trust-badges',array(),$soft,26),
                $section('wpsoft-service-cards-pro',array(),$blue,52),
                $section('wpsoft-before-after',array(),$soft,56),
                $section('wpsoft-process-steps-pro',array(),$blue,52),
                $pair(self::element('wpsoft-testimonial-slider'),self::element('wpsoft-contact-cards'),$soft,array(58,42),54),
                $section('wpsoft-faq',array(),$blue,48)
            );

        case 'veterinary':
            return array(
                $section('wpsoft-hero-medical',array('eyebrow'=>'PET HEALTH','title'=>$title,'text'=>'Sıcak, güven veren ve kolay randevu odaklı veteriner kliniği deneyimi.','image'=>array('url'=>$sample),'button_text'=>'Randevu Al','button_url'=>array('url'=>'#iletisim')),$lime,20),
                $section('wpsoft-service-cards-pro',array(),$soft,52),
                $section('wpsoft-trust-badges',array(),$lime,30),
                $pair(self::element('wpsoft-image-text',array('eyebrow'=>'SEVGİYLE BAKIM','title'=>'Her dost için kişiselleştirilmiş yaklaşım','image'=>array('url'=>$sample))),self::element('wpsoft-process-steps-pro'),$soft,array(52,48),56),
                $section('wpsoft-testimonial-slider',array(),$lime,48),
                $section('wpsoft-contact-cards',array(),$soft,48),
                $section('wpsoft-cta',array('title'=>'Dostunuz için hızlı randevu oluşturun','button_text'=>'Randevu','button_url'=>array('url'=>'#iletisim'),'bg'=>'#166534'),$lime,28)
            );

        case 'gym':
            return array(
                $section('wpsoft-hero-spotlight',array('eyebrow'=>'TRAIN HARDER','title'=>$title,'text'=>'Enerji, topluluk ve sonuç odaklı güçlü fitness deneyimi.','button_text'=>'Üyeliği Başlat','button_url'=>array('url'=>'#iletisim')),$dark,18),
                $section('wpsoft-animated-counter',array(),$rose,32),
                $section('wpsoft-service-cards-pro',array(),$dark,52),
                $section('wpsoft-image-carousel',array('gallery'=>array(array('url'=>$sample,'id'=>0),array('url'=>self::demo('generic.svg'),'id'=>0),array('url'=>self::demo('agency.svg'),'id'=>0))),$rose,52),
                $pair(self::element('wpsoft-pricing'),self::element('wpsoft-testimonial-slider'),$dark,array(45,55),54),
                $section('wpsoft-cta',array('title'=>'Bugün başla, farkı hisset','button_text'=>'Üye Ol','button_url'=>array('url'=>'#iletisim'),'bg'=>'#7f1d1d'),$rose,28)
            );

        case 'security':
            return array(
                $section('wpsoft-hero-saas',array('badge'=>'SMART SECURITY','title'=>$title,'text'=>'Kamera, alarm ve erişim sistemlerini teknoloji odaklı güven diliyle sunun.','image'=>array('url'=>$sample),'primary_text'=>'Keşif Talebi','primary_url'=>array('url'=>'#iletisim'),'secondary_text'=>'Sistemleri İncele','secondary_url'=>array('url'=>'#')),$dark,18),
                $section('wpsoft-trust-badges',array(),$soft,28),
                $section('wpsoft-feature-mosaic',array('title'=>'Tek merkezden akıllı güvenlik','image'=>array('url'=>$sample)),$blue,56),
                $section('wpsoft-service-cards-pro',array(),$soft,52),
                $section('wpsoft-process-steps-pro',array(),$dark,52),
                $section('wpsoft-testimonial-slider',array(),$blue,48),
                $section('wpsoft-cta',array('title'=>'Mekânınız için güvenlik keşfi planlayın','button_text'=>'Keşif Talebi','button_url'=>array('url'=>'#iletisim'),'bg'=>'#172554'),$soft,28)
            );

        case 'cleaning':
            return array(
                $section('wpsoft-hero-split-modern',array('eyebrow'=>'PROFESSIONAL CLEANING','title'=>$title,'text'=>'Hızlı teklif, hizmet kapsamı ve güven unsurlarına odaklanan sade servis sayfası.','image'=>array('url'=>$sample),'primary_text'=>'Hızlı Teklif','primary_url'=>array('url'=>'#iletisim'),'secondary_text'=>'Hizmetler','secondary_url'=>array('url'=>'#')),$blue,24),
                $section('wpsoft-trust-badges',array(),$soft,26),
                $section('wpsoft-service-cards-pro',array(),$blue,52),
                $pair(self::element('wpsoft-before-after'),self::element('wpsoft-process-steps-pro'),$soft,array(48,52),54),
                $section('wpsoft-testimonial-slider',array(),$blue,46),
                $section('wpsoft-contact-cards',array(),$soft,46),
                $section('wpsoft-cta',array('title'=>'İhtiyacınıza uygun temizlik planı oluşturalım','button_text'=>'Teklif Al','button_url'=>array('url'=>'#iletisim'),'bg'=>'#0369a1'),$blue,28)
            );

        case 'travel':
            return array(
                $section('wpsoft-hero-hospitality',array('eyebrow'=>'EXPLORE MORE','title'=>$title,'text'=>'Destinasyonları ve turları keşif duygusuyla sunan deneyim odaklı seyahat sayfası.','image'=>array('url'=>$sample),'button_text'=>'Turları Keşfet','button_url'=>array('url'=>'#')),$blue,18),
                $section('wpsoft-booking-strip',array('checkin'=>'Tarih Seç','checkout'=>'Dönüş','guests'=>'2 Misafir','button_text'=>'Tur Ara','button_url'=>array('url'=>'#')),$soft,20),
                $section('wpsoft-card-carousel',array(),$blue,52),
                $section('wpsoft-image-cascade',array('image_one'=>array('url'=>$sample),'image_two'=>array('url'=>self::demo('travel.svg')),'image_three'=>array('url'=>self::demo('hotel.svg'))),$soft,56),
                $section('wpsoft-service-cards-pro',array(),$blue,50),
                $section('wpsoft-testimonial-slider',array(),$soft,48),
                $section('wpsoft-cta',array('title'=>'Yeni rotanızı birlikte seçelim','button_text'=>'Tur Danışmanı','button_url'=>array('url'=>'#iletisim'),'bg'=>'#075985'),$blue,28)
            );

        case 'furniture':
            return array(
                $section('wpsoft-hero-commerce',array('badge'=>'NEW COLLECTION','title'=>$title,'text'=>'Mobilya ve dekorasyonu editorial ürün vitriniyle sunan premium mağaza deneyimi.','image'=>array('url'=>$sample),'button_text'=>'Koleksiyonu Gör','button_url'=>array('url'=>'#'),'discount'=>'Yeni'),$warm,18),
                $section('wpsoft-product-showcase',array(),$soft,52),
                $section('wpsoft-image-cascade',array('image_one'=>array('url'=>$sample),'image_two'=>array('url'=>self::demo('shop.svg')),'image_three'=>array('url'=>self::demo('architecture.svg'))),$warm,56),
                $pair(self::element('wpsoft-feature-mosaic',array('title'=>'Malzeme, işçilik ve zamansız tasarım','image'=>array('url'=>$sample))),self::element('wpsoft-trust-badges'),$soft,array(65,35),56),
                $section('wpsoft-testimonial-slider',array(),$warm,46),
                $section('wpsoft-cta',array('title'=>'Mekânınıza uygun koleksiyonu keşfedin','button_text'=>'Showroom','button_url'=>array('url'=>'#iletisim'),'bg'=>'#78350f'),$soft,28)
            );

        case 'printing':
            return array(
                $section('wpsoft-hero-bento',array('eyebrow'=>'PRINT & SIGN','title'=>$title,'text'=>'Matbaa, tabela ve üretim kabiliyetlerini renkli, hızlı ve proje odaklı sunun.','image'=>array('url'=>$sample),'button_text'=>'Fiyat Al','button_url'=>array('url'=>'#iletisim')),$violet,22),
                $section('wpsoft-service-cards-pro',array(),$soft,50),
                $section('wpsoft-portfolio',array(),$violet,54),
                $pair(self::element('wpsoft-feature-mosaic',array('title'=>'Baskıdan uygulamaya tek üretim akışı','image'=>array('url'=>$sample))),self::element('wpsoft-stats-grid'),$soft,array(62,38),54),
                $section('wpsoft-process-steps-pro',array(),$violet,50),
                $section('wpsoft-cta',array('title'=>'Dosyanızı gönderin, hızlı fiyatlandıralım','button_text'=>'Fiyat Teklifi','button_url'=>array('url'=>'#iletisim'),'bg'=>'#581c87'),$soft,28)
            );

        /* Premium pages: intentionally more editorial and richer than standard variants. */
        case 'corporate-premium':
            return array(
                $section('wpsoft-hero-split-modern',array('eyebrow'=>'CORPORATE 2026','title'=>'Güven veren güçlü bir dijital marka deneyimi','text'=>'Büyük tipografi, kanıt ve net aksiyon hiyerarşisiyle premium kurumsal ana sayfa.','image'=>array('url'=>$sample),'primary_text'=>'Projeyi Konuşalım','primary_url'=>array('url'=>'#iletisim'),'secondary_text'=>'Case Studies','secondary_url'=>array('url'=>'#')),$blue,22),
                $section('wpsoft-logo-marquee',array(),$soft,22),
                $pair(self::element('wpsoft-scroll-reveal-text',array('eyebrow'=>'WHY US','text'=>'Stratejiyi tasarımla tasarımı teknolojiyle birleştiriyoruz.')),self::element('wpsoft-number-cards'),$dark,array(64,36),62),
                $section('wpsoft-service-cards-pro',array(),$soft,58),
                $section('wpsoft-image-cascade',array('image_one'=>array('url'=>$sample),'image_two'=>array('url'=>self::demo('corporate-alt.svg')),'image_three'=>array('url'=>self::demo('agency.svg'))),$blue,60),
                $section('wpsoft-process-steps-pro',array(),$soft,54),
                $pair(self::element('wpsoft-testimonial-slider'),self::element('wpsoft-faq'),$blue,array(55,45),58),
                $section('wpsoft-morphing-cta',array('eyebrow'=>'NEXT MOVE','title'=>'Bir sonraki büyüme adımınızı konuşalım','button_text'=>'Başlayalım','button_url'=>array('url'=>'#iletisim')),$soft,50)
            );

        case 'industrial-premium':
            return array(
                $section('wpsoft-hero-industry',array('eyebrow'=>'INDUSTRIAL PREMIUM','title'=>'Üretim gücünüzü teknik kanıtlarla öne çıkarın','text'=>'Makina parkuru, kapasite, kalite ve mühendislik yetkinliğini premium B2B deneyimle sunun.','image'=>array('url'=>$sample),'button_text'=>'Teknik Görüşme','button_url'=>array('url'=>'#iletisim')),$dark,16),
                $section('wpsoft-stats-grid',array(),$soft,30),
                $section('wpsoft-feature-mosaic',array('title'=>'Kapasite ve mühendislik altyapısı','image'=>array('url'=>$sample)),$dark,58),
                $pair(self::element('wpsoft-tabs-modern'),self::element('wpsoft-trust-badges'),$soft,array(64,36),58),
                $section('wpsoft-card-carousel',array(),$blue,52),
                $section('wpsoft-process-steps-pro',array(),$soft,52),
                $section('wpsoft-logo-marquee',array(),$blue,24),
                $section('wpsoft-cta',array('title'=>'Üretim projenizi teknik ekiple değerlendirin','button_text'=>'Teknik Teklif','button_url'=>array('url'=>'#iletisim'),'bg'=>'#020617'),$soft,28)
            );

        case 'machinery-premium':
            return array(
                $section('wpsoft-hero-industry',array('eyebrow'=>'CNC & MACHINERY','title'=>'Makina çözümlerini ürün odaklı premium deneyimle sunun','text'=>'Teknik özellik, uygulama alanı ve servis gücünü satış yolculuğuna dönüştürün.','image'=>array('url'=>$sample),'button_text'=>'Makina Teklifi','button_url'=>array('url'=>'#iletisim')),$dark,16),
                $section('wpsoft-product-showcase',array(),$soft,54),
                $pair(self::element('wpsoft-image-hotspots',array('image'=>array('url'=>$sample))),self::element('wpsoft-feature-list'),$blue,array(60,40),58),
                $section('wpsoft-service-cards-pro',array(),$soft,52),
                $section('wpsoft-process-steps-pro',array(),$dark,52),
                $section('wpsoft-trust-badges',array(),$soft,28),
                $section('wpsoft-cta',array('title'=>'Doğru makinayı birlikte belirleyelim','button_text'=>'Satış Ekibine Ulaş','button_url'=>array('url'=>'#iletisim'),'bg'=>'#0c4a6e'),$blue,28)
            );

        case 'ecommerce-premium':
            return array(
                $section('wpsoft-hero-commerce',array('badge'=>'SHOP PREMIUM','title'=>'Dönüşüm odaklı modern alışveriş deneyimi','text'=>'Koleksiyon, kampanya ve güven unsurlarını editorial mağaza düzeninde birleştirin.','image'=>array('url'=>$sample),'button_text'=>'Alışverişe Başla','button_url'=>array('url'=>'#'),'discount'=>'%30'),$lime,16),
                $section('wpsoft-info-strip',array(),$soft,18),
                $section('wpsoft-product-showcase',array(),$lime,54),
                $section('wpsoft-hover-reveal',array(),$soft,54),
                $pair(self::element('wpsoft-image-hotspots',array('image'=>array('url'=>$sample))),self::element('wpsoft-trust-badges'),$lime,array(66,34),56),
                $section('wpsoft-card-carousel',array(),$soft,52),
                $section('wpsoft-testimonial-slider',array(),$lime,46),
                $section('wpsoft-morphing-cta',array('eyebrow'=>'NEW DROP','title'=>'Yeni koleksiyonu keşfedin','button_text'=>'Şimdi İncele','button_url'=>array('url'=>'#')),$soft,46)
            );

        case 'saas-premium':
            return array(
                $section('wpsoft-hero-saas',array('badge'=>'SAAS PREMIUM','title'=>'İş akışınızı daha hızlı ve akıllı hale getirin','text'=>'Dashboard, sosyal kanıt ve ürün hikâyesini premium teknoloji deneyiminde birleştirin.','image'=>array('url'=>$sample),'primary_text'=>'Ücretsiz Başla','primary_url'=>array('url'=>'#'),'secondary_text'=>'Canlı Demo','secondary_url'=>array('url'=>'#')),$dark,16),
                $section('wpsoft-logo-marquee',array(),$soft,22),
                $section('wpsoft-feature-mosaic',array('title'=>'Ürünün değerini tek bakışta anlatın','image'=>array('url'=>$sample)),$violet,56),
                $pair(self::element('wpsoft-icon-orbit'),self::element('wpsoft-mouse-follow-card',array('title'=>'AI destekli çalışma alanı','text'=>'Hızlı ve akıllı ürün deneyimi.')),$dark,array(55,45),58),
                $section('wpsoft-tabs-modern',array(),$soft,52),
                $section('wpsoft-pricing',array(),$violet,54),
                $section('wpsoft-testimonial-slider',array(),$soft,48),
                $section('wpsoft-cta',array('title'=>'Takımınızı bugün hızlandırın','button_text'=>'Ücretsiz Başla','button_url'=>array('url'=>'#'),'bg'=>'#4c1d95'),$violet,28)
            );

        case 'hotel-premium':
            return array(
                $section('wpsoft-hero-hospitality',array('eyebrow'=>'RESORT COLLECTION','title'=>'Konforun ötesinde premium konaklama deneyimi','text'=>'Büyük görsel hikâye, rezervasyon ve deneyim keşfini tek akışta birleştirin.','image'=>array('url'=>$sample),'button_text'=>'Rezervasyon Yap','button_url'=>array('url'=>'#rezervasyon')),$mint,14),
                $section('wpsoft-booking-strip',array('button_text'=>'Müsaitliği Kontrol Et','button_url'=>array('url'=>'#rezervasyon')),$soft,18),
                $section('wpsoft-image-cascade',array('image_one'=>array('url'=>$sample),'image_two'=>array('url'=>self::demo('hotel.svg')),'image_three'=>array('url'=>self::demo('travel.svg'))),$mint,56),
                $section('wpsoft-service-cards-pro',array(),$soft,52),
                $pair(self::element('wpsoft-image-text',array('eyebrow'=>'SIGNATURE STAY','title'=>'Her detayı düşünülmüş deneyim','image'=>array('url'=>$sample))),self::element('wpsoft-testimonial-slider'),$mint,array(58,42),56),
                $section('wpsoft-image-carousel',array('gallery'=>array(array('url'=>$sample,'id'=>0),array('url'=>self::demo('hotel.svg'),'id'=>0),array('url'=>self::demo('travel.svg'),'id'=>0))),$soft,50),
                $section('wpsoft-morphing-cta',array('eyebrow'=>'BOOK DIRECT','title'=>'Doğrudan rezervasyon avantajları','button_text'=>'Rezervasyon','button_url'=>array('url'=>'#rezervasyon')),$mint,46)
            );

        case 'clinic-premium':
            return array(
                $section('wpsoft-hero-medical',array('eyebrow'=>'CLINIC PREMIUM','title'=>'Uzmanlığı güven veren premium sağlık deneyimine dönüştürün','text'=>'Uzmanlık, kanıt, süreç ve randevuyu sakin ve profesyonel bir arayüzle sunun.','image'=>array('url'=>$sample),'button_text'=>'Randevu Al','button_url'=>array('url'=>'#iletisim')),$mint,16),
                $section('wpsoft-trust-badges',array(),$soft,26),
                $pair(self::element('wpsoft-service-cards-pro'),self::element('wpsoft-before-after'),$mint,array(55,45),56),
                $section('wpsoft-process-steps-pro',array(),$soft,52),
                $section('wpsoft-team-carousel-pro',array(),$mint,52),
                $section('wpsoft-testimonial-slider',array(),$soft,46),
                $pair(self::element('wpsoft-contact-cards'),self::element('wpsoft-faq'),$mint,array(45,55),52)
            );

        case 'architecture-premium':
            return array(
                $section('wpsoft-hero-spotlight',array('eyebrow'=>'ARCHITECTURE 2026','title'=>'Mekânları zamansız deneyimlere dönüştürüyoruz','text'=>'Büyük tipografi, proje görselleri ve editorial boşluklarla premium mimarlık portföyü.','button_text'=>'Projeleri Gör','button_url'=>array('url'=>'#projeler')),$dark,16),
                $section('wpsoft-scroll-reveal-text',array('eyebrow'=>'DESIGN PHILOSOPHY','text'=>'Işık malzeme işlev ve bağlam arasında dengeli mekânlar tasarlıyoruz.'),$soft,62),
                $section('wpsoft-image-cascade',array('image_one'=>array('url'=>$sample),'image_two'=>array('url'=>self::demo('architecture.svg')),'image_three'=>array('url'=>self::demo('corporate.svg'))),$dark,56),
                $section('wpsoft-portfolio',array(),$soft,56),
                $pair(self::element('wpsoft-number-cards'),self::element('wpsoft-process-steps-pro'),$warm,array(36,64),54),
                $section('wpsoft-testimonial-slider',array(),$soft,46),
                $section('wpsoft-morphing-cta',array('eyebrow'=>'NEW PROJECT','title'=>'Bir sonraki mekânı birlikte tasarlayalım','button_text'=>'Proje Görüşmesi','button_url'=>array('url'=>'#iletisim')),$warm,46)
            );

        case 'restaurant-premium':
            return array(
                $section('wpsoft-hero-hospitality',array('eyebrow'=>'FINE DINING','title'=>'Lezzeti unutulmaz bir deneyime dönüştürün','text'=>'Şef hikâyesi, atmosfer ve rezervasyonu premium gastronomi deneyiminde birleştirin.','image'=>array('url'=>$sample),'button_text'=>'Masa Ayırt','button_url'=>array('url'=>'#rezervasyon')),$rose,14),
                $section('wpsoft-booking-strip',array('checkin'=>'Bugün','checkout'=>'20:30','guests'=>'2 Kişi','button_text'=>'Masa Ayırt','button_url'=>array('url'=>'#rezervasyon')),$soft,18),
                $section('wpsoft-image-cascade',array('image_one'=>array('url'=>$sample),'image_two'=>array('url'=>self::demo('restaurant.svg')),'image_three'=>array('url'=>self::demo('agency.svg'))),$dark,56),
                $section('wpsoft-tabs-modern',array(),$rose,50),
                $pair(self::element('wpsoft-image-text',array('eyebrow'=>'CHEF STORY','title'=>'Mevsimsel ürünler, özgün teknikler','image'=>array('url'=>$sample))),self::element('wpsoft-testimonial-slider'),$soft,array(58,42),54),
                $section('wpsoft-marquee-text',array(),$dark,22),
                $section('wpsoft-morphing-cta',array('eyebrow'=>'TONIGHT','title'=>'Bu akşam için masanızı ayırtın','button_text'=>'Rezervasyon','button_url'=>array('url'=>'#rezervasyon')),$rose,44)
            );

        case 'agency-premium':
            return array(
                $section('wpsoft-hero-bento',array('eyebrow'=>'CREATIVE PREMIUM','title'=>'Markaları fark edilen dijital deneyimlere dönüştürüyoruz','text'=>'Bento grid, motion ve editorial tipografiyle bağımsız creative agency deneyimi.','image'=>array('url'=>$sample),'button_text'=>'Projeyi Başlat','button_url'=>array('url'=>'#iletisim')),$violet,18),
                $section('wpsoft-marquee-text',array(),$dark,20),
                $section('wpsoft-scroll-reveal-text',array('eyebrow'=>'WE CREATE','text'=>'Strateji branding web motion ve teknolojiyi tek yaratıcı sistemde buluşturuyoruz.'),$soft,62),
                $section('wpsoft-hover-reveal',array(),$dark,54),
                $section('wpsoft-reveal-cards',array(),$violet,52),
                $pair(self::element('wpsoft-animated-counter'),self::element('wpsoft-testimonial-slider'),$soft,array(38,62),54),
                $section('wpsoft-morphing-cta',array('eyebrow'=>'MAKE IT REAL','title'=>'Sıradaki güçlü işi birlikte üretelim','button_text'=>'Brief Gönder','button_url'=>array('url'=>'#iletisim')),$violet,44)
            );
        }

        return null;
    }

    private static function sector_v317_data($key,$tpl,$sample,$title,$cta){
        $out=array();

        $hero_split=function($eyebrow,$button='Teklif Al') use($title,$sample){
            return self::element('wpsoft-hero-split-modern',array(
                'eyebrow'=>$eyebrow,'title'=>$title,'text'=>'Sektörünüze özel modern, mobil uyumlu ve düzenlemeye hazır sayfa yapısı.',
                'image'=>array('url'=>$sample),'primary_text'=>$button,'primary_url'=>array('url'=>'#iletisim'),
                'secondary_text'=>'Daha Fazla','secondary_url'=>array('url'=>'#')
            ));
        };

        if(in_array($key,array('industrial','automotive','logistics','energy','industrial-premium','machinery-premium'),true)){
            $out[]=self::container(array(self::element('wpsoft-hero-industry',array('hero_radius'=>array('size'=>30),'title'=>$title,'image'=>array('url'=>$sample),'button_text'=>'Teknik Bilgi Al','button_url'=>array('url'=>'#iletisim')))));
            $out[]=self::container(array(self::element('wpsoft-trust-badges')));
            $out[]=self::container(array(self::element('wpsoft-service-cards-pro')));
            $out[]=self::container(array(self::element('wpsoft-feature-mosaic',array('title'=>'Üretim kabiliyetleri ve teknik güç','image'=>array('url'=>$sample)))));
            $out[]=self::container(array(self::element('wpsoft-process-steps-pro')));
            $out[]=self::container(array(self::element('wpsoft-testimonial-slider')));
            $out[]=self::container(array($cta));
            return $out;
        }

        if(in_array($key,array('hotel','travel','restaurant','hotel-premium','restaurant-premium'),true)){
            $out[]=self::container(array(self::element('wpsoft-hero-hospitality',array('title'=>$title,'image'=>array('url'=>$sample),'button_text'=>($key==='restaurant'||$key==='restaurant-premium'?'Rezervasyon':'Müsaitliği Gör'),'button_url'=>array('url'=>'#iletisim')))));
            $out[]=self::container(array(self::element('wpsoft-booking-strip',array('button_text'=>($key==='restaurant'||$key==='restaurant-premium'?'Masa Ayırt':'Müsaitliği Kontrol Et'),'button_url'=>array('url'=>'#iletisim')))));
            $out[]=self::container(array(self::element('wpsoft-service-cards-pro')));
            $out[]=self::container(array(self::element('wpsoft-image-carousel',array('gallery'=>array(
                array('url'=>$sample,'id'=>0),array('url'=>self::demo('hotel.svg'),'id'=>0),array('url'=>self::demo('restaurant.svg'),'id'=>0),array('url'=>self::demo('travel.svg'),'id'=>0)
            )))));
            $out[]=self::container(array(self::element('wpsoft-trust-badges')));
            $out[]=self::container(array(self::element('wpsoft-testimonial-slider')));
            $out[]=self::container(array($cta));
            return $out;
        }

        if(in_array($key,array('health','dentist','veterinary','beauty','clinic-premium'),true)){
            $out[]=self::container(array(self::element('wpsoft-hero-medical',array('hero_radius'=>array('size'=>30),'title'=>$title,'image'=>array('url'=>$sample),'button_text'=>'Randevu Al','button_url'=>array('url'=>'#iletisim')))));
            $out[]=self::container(array(self::element('wpsoft-trust-badges')));
            $out[]=self::container(array(self::element('wpsoft-service-cards-pro')));
            $out[]=self::container(array(self::element('wpsoft-before-after')));
            $out[]=self::container(array(self::element('wpsoft-process-steps-pro')));
            $out[]=self::container(array(self::element('wpsoft-contact-cards')));
            $out[]=self::container(array(self::element('wpsoft-testimonial-slider')));
            $out[]=self::container(array($cta));
            return $out;
        }

        if(in_array($key,array('furniture','ecommerce-premium'),true)){
            $out[]=self::container(array(self::element('wpsoft-hero-commerce',array('title'=>$title,'image'=>array('url'=>$sample),'button_text'=>'Ürünleri İncele','button_url'=>array('url'=>'#'),'discount'=>'%30 İndirim'))));
            $out[]=self::container(array(self::element('wpsoft-product-showcase',array('items'=>array(
                array('image'=>array('url'=>self::demo('shop.svg')),'title'=>'Yeni Koleksiyon','meta'=>'Yeni','price'=>'₺1.250'),
                array('image'=>array('url'=>self::demo('corporate.svg')),'title'=>'Çok Satan','meta'=>'Popüler','price'=>'₺980'),
                array('image'=>array('url'=>self::demo('agency.svg')),'title'=>'Özel Seri','meta'=>'Premium','price'=>'₺1.490')
            )))));
            $out[]=self::container(array(self::element('wpsoft-trust-badges')));
            $out[]=self::container(array(self::element('wpsoft-service-cards-pro')));
            $out[]=self::container(array(self::element('wpsoft-testimonial-slider')));
            $out[]=self::container(array($cta));
            return $out;
        }

        if(in_array($key,array('software','security','saas-premium'),true)){
            $out[]=self::container(array(self::element('wpsoft-hero-saas',array('badge'=>'YENİ NESİL','title'=>$title,'text'=>'Ürün ve teknoloji odaklı dönüşüm sağlayan modern SaaS deneyimi.','image'=>array('url'=>$sample),'primary_text'=>'Başlayın','primary_url'=>array('url'=>'#iletisim'),'secondary_text'=>'Demo İzle','secondary_url'=>array('url'=>'#')))));
            $out[]=self::container(array(self::element('wpsoft-logo-marquee')));
            $out[]=self::container(array(self::element('wpsoft-feature-mosaic',array('title'=>'Ürününüzün güçlü özelliklerini öne çıkarın','image'=>array('url'=>$sample)))));
            $out[]=self::container(array(self::element('wpsoft-service-cards-pro')));
            $out[]=self::container(array(self::element('wpsoft-process-steps-pro')));
            $out[]=self::container(array(self::element('wpsoft-testimonial-slider')));
            $out[]=self::container(array($cta));
            return $out;
        }

        if(in_array($key,array('construction','realestate','architecture-premium'),true)){
            $out[]=self::container(array($hero_split('PROJE & TASARIM','Projeleri Gör')));
            $out[]=self::container(array(self::element('wpsoft-image-cascade',array('image_one'=>array('url'=>$sample),'image_two'=>array('url'=>self::demo('architecture.svg')),'image_three'=>array('url'=>self::demo('agency.svg'))))));
            $out[]=self::container(array(self::element('wpsoft-service-cards-pro')));
            $out[]=self::container(array(self::element('wpsoft-portfolio')));
            $out[]=self::container(array(self::element('wpsoft-process-steps-pro')));
            $out[]=self::container(array(self::element('wpsoft-testimonial-slider')));
            $out[]=self::container(array($cta));
            return $out;
        }

        if(in_array($key,array('agency','event','personal','agency-premium'),true)){
            $out[]=self::container(array(self::element('wpsoft-hero-bento',array('hero_radius'=>array('size'=>30),'eyebrow'=>'CREATIVE EXPERIENCE','title'=>$title,'text'=>'Cesur fikirler ve modern dijital deneyimler.','image'=>array('url'=>$sample),'button_text'=>'Projeyi Başlat','button_url'=>array('url'=>'#iletisim')))));
            $out[]=self::container(array(self::element('wpsoft-scroll-reveal-text')));
            $out[]=self::container(array(self::element('wpsoft-service-cards-pro')));
            $out[]=self::container(array(self::element('wpsoft-image-cascade',array('image_one'=>array('url'=>$sample),'image_two'=>array('url'=>self::demo('agency.svg')),'image_three'=>array('url'=>self::demo('corporate.svg'))))));
            $out[]=self::container(array(self::element('wpsoft-process-steps-pro')));
            $out[]=self::container(array(self::element('wpsoft-testimonial-slider')));
            $out[]=self::container(array($cta));
            return $out;
        }

        if(in_array($key,array('corporate','service','finance','law','education','corporate-premium'),true)){
            $out[]=self::container(array($hero_split('KURUMSAL ÇÖZÜMLER')));
            $out[]=self::container(array(self::element('wpsoft-trust-badges')));
            $out[]=self::container(array(self::element('wpsoft-service-cards-pro')));
            $out[]=self::container(array(self::element('wpsoft-feature-mosaic',array('title'=>'Güçlü bir kurumsal deneyim','image'=>array('url'=>$sample)))));
            $out[]=self::container(array(self::element('wpsoft-process-steps-pro')));
            $out[]=self::container(array(self::element('wpsoft-testimonial-slider')));
            $out[]=self::container(array(self::element('wpsoft-faq')));
            $out[]=self::container(array($cta));
            return $out;
        }

        return null;
    }

    private static function elementor_data($key,$tpl){
        $titles=array(
        'corporate'=>'İşletmenizi dijitalde daha güçlü hale getirin','agency'=>'Fikirleri güçlü dijital deneyimlere dönüştürüyoruz','service'=>'Hizmetinizi güçlü bir sunumla öne çıkarın','contact'=>'Yeni projenizi birlikte planlayalım','industrial'=>'Üretiminizi güçlü bir dijital vitrinle öne çıkarın','construction'=>'Projelerinizi modern ve güven veren bir sunumla anlatın','hotel'=>'Konuklarınıza daha rezervasyon öncesinde güçlü bir deneyim sunun','restaurant'=>'Lezzetinizi modern bir dijital deneyimle buluşturun','realestate'=>'Doğru gayrimenkulü doğru sunumla buluşturun','health'=>'Güven veren modern bir sağlık deneyimi oluşturun','automotive'=>'Otomotiv hizmetlerinizi güçlü bir dijital vitrine taşıyın','software'=>'Ürününüzü sade, hızlı ve ikna edici biçimde anlatın','beauty'=>'Markanızı premium bir dijital deneyimle öne çıkarın','dentist'=>'Kliniğinizi güven veren modern bir deneyimle tanıtın','gym'=>'Enerjiyi güçlü bir web deneyimine dönüştürün','security'=>'Güvenlik çözümlerinizi teknoloji odaklı anlatın','travel'=>'Yeni rotaları etkileyici bir dijital deneyimle keşfettirin','furniture'=>'Tasarım anlayışınızı premium bir dijital vitrine taşıyın');
        $sample=self::demo_for($key);
        $title=$titles[$key]??'İşletmenizi dijitalde daha güçlü hale getirin';
        $hero=self::element('wpsoft-hero-split-modern',array('eyebrow'=>'WPSoft Tasarım','title'=>$title,'text'=>'Tüm alanları Elementor üzerinden değiştirebilirsiniz.','primary_text'=>'Teklif Al','primary_url'=>array('url'=>'#iletisim'),'accent'=>$tpl['accent'],'image'=>array('url'=>$sample)));
        $slider=self::element('wpsoft-hero-slider',array('items'=>array(
                    array('eyebrow'=>'WPSoft Premium','title'=>$title,'text'=>'Sektörünüze uygun demo görselle hazır başlangıç.','button'=>'Teklif Al','url'=>array('url'=>'#iletisim'),'image'=>array('url'=>$sample)),
                    array('eyebrow'=>'Modern Tasarım','title'=>'Mobil uyumlu ve kolay düzenlenebilir','text'=>'Elementor üzerinden tüm alanları değiştirebilirsiniz.','button'=>'İncele','url'=>array('url'=>'#'),'image'=>array('url'=>self::demo('agency.svg')))
                )));
        $video=self::element('wpsoft-video-hero',array('title'=>$title,'poster'=>array('url'=>$sample),'button_text'=>'Bize Ulaşın','button_url'=>array('url'=>'#iletisim')));
        $about=self::element('wpsoft-image-text',array('eyebrow'=>'Hakkımızda','title'=>'Güçlü bir marka deneyimi için doğru altyapı','description'=>'Modern tasarım ve mobil uyumlu yapı.','image'=>array('url'=>$sample),'button_text'=>'Daha Fazla','button_url'=>array('url'=>'#')));
        $cta=self::element('wpsoft-cta',array('title'=>'Yeni projeniz için hazır mısınız?','description'=>'İhtiyacınızı konuşalım.','button_text'=>'Bize Ulaşın','button_url'=>array('url'=>'#iletisim'),'bg'=>'#0f172a'));
        $out=array();

        // Signature sector pages: aynı şablonun farklı metni değil, farklı bilgi mimarisi.
        if($key==='hotel-signature'){
            $img=self::demo_v2('hotel-signature.svg');
            return array(
                self::container(array(self::element('wpsoft-hero-hospitality',array('eyebrow'=>'SIGNATURE RESORT','title'=>'Konaklamayı unutulmaz bir deneyime dönüştürün','text'=>'Görsel atmosfer, rezervasyon CTA ve premium konaklama hikâyesi.','image'=>array('url'=>$img),'button_text'=>'Rezervasyon Yap','button_url'=>array('url'=>'#rezervasyon')))),array('padding'=>array('unit'=>'px','top'=>'18','right'=>'18','bottom'=>'18','left'=>'18','isLinked'=>true))),
                self::container(array(self::element('wpsoft-info-strip'))),
                self::container(array(self::element('wpsoft-image-carousel',array('gallery'=>array(array('url'=>$img,'id'=>0),array('url'=>self::demo_v2('travel-signature.svg'),'id'=>0),array('url'=>self::demo('hotel.svg'),'id'=>0)))))),
                self::container(array(self::element('wpsoft-icon-grid'),self::element('wpsoft-number-cards'))),
                self::container(array(self::element('wpsoft-image-text',array('eyebrow'=>'DENEYİM','title'=>'Her detay konfor ve sakinlik için tasarlandı','image'=>array('url'=>$img))))),
                self::container(array(self::element('wpsoft-testimonial-slider'))),
                self::container(array(self::element('wpsoft-cta',array('title'=>'Konaklamanızı planlayın','description'=>'Tarihlerinizi seçin ve size özel deneyimi keşfedin.','button_text'=>'Rezervasyon','button_url'=>array('url'=>'#rezervasyon'),'bg'=>'#0f3d38'))))
            );
        }
        if($key==='industrial-signature'){
            $img=self::demo_v2('industry-signature.svg');
            return array(
                self::container(array(self::element('wpsoft-hero-industry',array('hero_radius'=>array('size'=>30),'eyebrow'=>'TECHNICAL CAPABILITY','title'=>'Üretim kabiliyetinizi teknik verilerle anlatın','text'=>'Makina parkuru, üretim kapasitesi ve servis gücü için B2B odaklı yapı.','image'=>array('url'=>$img),'button_text'=>'Teknik Teklif Al','button_url'=>array('url'=>'#iletisim')))),array('background_background'=>'classic','background_color'=>'#07111f')),
                self::container(array(self::element('wpsoft-stats-grid'))),
                self::container(array(self::element('wpsoft-feature-mosaic',array('title'=>'Üretim altyapısı ve proses kabiliyetleri','image'=>array('url'=>$img))))),
                self::container(array(self::element('wpsoft-tabs-modern'))),
                self::container(array(self::element('wpsoft-card-carousel'))),
                self::container(array(self::element('wpsoft-trust-badges'),self::element('wpsoft-logo-marquee'))),
                self::container(array(self::element('wpsoft-cta',array('title'=>'Teknik dosyanızı birlikte değerlendirelim','description'=>'Uygun üretim yöntemi ve termin için mühendislik ekibimizle görüşün.','button_text'=>'Teknik Görüşme','button_url'=>array('url'=>'#iletisim'),'bg'=>'#111827'))))
            );
        }
        if($key==='saas-signature'){
            $img=self::demo_v2('saas-signature.svg');
            return array(
                self::container(array(self::element('wpsoft-hero-saas',array('badge'=>'PRODUCT 4.0','title'=>'İş akışınızı tek platformda sadeleştirin','text'=>'Dashboard, entegrasyon ve otomasyon değerini ilk ekranda gösterin.','image'=>array('url'=>$img),'primary_text'=>'Ücretsiz Başla','primary_url'=>array('url'=>'#'),'secondary_text'=>'Demo İzle','secondary_url'=>array('url'=>'#demo')))),array('background_background'=>'classic','background_color'=>'#07111f')),
                self::container(array(self::element('wpsoft-logo-marquee'))),
                self::container(array(self::element('wpsoft-icon-orbit'))),
                self::container(array(self::element('wpsoft-tabs-modern'))),
                self::container(array(self::element('wpsoft-feature-mosaic',array('title'=>'Tek panel, daha az operasyon yükü','image'=>array('url'=>$img))))),
                self::container(array(self::element('wpsoft-animated-counter'),self::element('wpsoft-progress-pro'))),
                self::container(array(self::element('wpsoft-testimonial-slider'),self::element('wpsoft-cta',array('title'=>'Ekibiniz için daha akıllı bir başlangıç','button_text'=>'Hemen Başla','button_url'=>array('url'=>'#'),'bg'=>'#4c1d95'))))
            );
        }
        if($key==='clinic-signature'){
            $img=self::demo_v2('clinic-signature.svg');
            return array(
                self::container(array(self::element('wpsoft-hero-medical',array('hero_radius'=>array('size'=>30),'eyebrow'=>'UZMANLIK & GÜVEN','title'=>'Modern sağlık hizmetinde güven veren deneyim','text'=>'Uzman ekip, teknoloji ve kolay randevu akışını sade biçimde sunun.','image'=>array('url'=>$img),'button_text'=>'Randevu Al','button_url'=>array('url'=>'#randevu')))),array('background_background'=>'classic','background_color'=>'#ecfeff')),
                self::container(array(self::element('wpsoft-trust-badges'))),
                self::container(array(self::element('wpsoft-icon-grid'))),
                self::container(array(self::element('wpsoft-team-carousel-pro'))),
                self::container(array(self::element('wpsoft-before-after'))),
                self::container(array(self::element('wpsoft-number-cards'),self::element('wpsoft-testimonial-slider'))),
                self::container(array(self::element('wpsoft-contact-cards'),self::element('wpsoft-faq')))
            );
        }
        if($key==='restaurant-signature'){
            $img=self::demo_v2('restaurant-signature.svg');
            return array(
                self::container(array(self::element('wpsoft-video-hero',array('eyebrow'=>'EDITORIAL DINING','title'=>'Lezzeti unutulmaz bir atmosfere dönüştürün','poster'=>array('url'=>$img),'button_text'=>'Rezervasyon Yap','button_url'=>array('url'=>'#rezervasyon')))),array('background_background'=>'classic','background_color'=>'#140b0d')),
                self::container(array(self::element('wpsoft-marquee-text'))),
                self::container(array(self::element('wpsoft-image-text',array('eyebrow'=>'ŞEFİN HİKÂYESİ','title'=>'Mevsimsel ürünler, özgün yorumlar','image'=>array('url'=>$img))))),
                self::container(array(self::element('wpsoft-tabs-modern'))),
                self::container(array(self::element('wpsoft-gallery-zoom-pro',array('gallery'=>array(array('url'=>$img,'id'=>0),array('url'=>self::demo('restaurant.svg'),'id'=>0),array('url'=>self::demo_v2('hotel-signature.svg'),'id'=>0)),'layout'=>'featured','lightbox'=>'yes')))),
                self::container(array(self::element('wpsoft-testimonial-slider'))),
                self::container(array(self::element('wpsoft-cta',array('title'=>'Bu akşam masanızı ayırtın','description'=>'Özel deneyim için rezervasyonunuzu tamamlayın.','button_text'=>'Rezervasyon','button_url'=>array('url'=>'#rezervasyon'),'bg'=>'#3f0d18'))))
            );
        }
        if($key==='agency-signature'){
            $img=self::demo_v2('agency-signature.svg');
            return array(
                self::container(array(self::element('wpsoft-hero-spotlight',array('eyebrow'=>'INDEPENDENT CREATIVE STUDIO','title'=>'Cesur fikirler.<br>Hatırlanan dijital deneyimler.','text'=>'Strateji, tasarım ve motion aynı yaratıcı sistemde.','button_text'=>'Birlikte Çalışalım','button_url'=>array('url'=>'#iletisim')))),array('background_background'=>'classic','background_color'=>'#050816')),
                self::container(array(self::element('wpsoft-marquee-text'))),
                self::container(array(self::element('wpsoft-hover-reveal'))),
                self::container(array(self::element('wpsoft-reveal-cards'))),
                self::container(array(self::element('wpsoft-gallery-zoom-pro',array('gallery'=>array(array('url'=>$img,'id'=>0),array('url'=>self::demo('agency.svg'),'id'=>0),array('url'=>self::demo_v2('corporate-signature.svg'),'id'=>0)),'layout'=>'masonry','lightbox'=>'yes')))),
                self::container(array(self::element('wpsoft-animated-counter'))),
                self::container(array(self::element('wpsoft-morphing-cta',array('eyebrow'=>'NEXT PROJECT','title'=>'Bir sonraki güçlü işi birlikte üretelim','text'=>'Kısa bir brief ile başlayın.','button_text'=>'Brief Gönder','button_url'=>array('url'=>'#iletisim')))))
            );
        }

        $page_v3114=self::page_v3114_data($key,$tpl,$sample,$title,$cta);
        if(null!==$page_v3114)return $page_v3114;
        $sector_v317=self::sector_v317_data($key,$tpl,$sample,$title,$cta);
        if(null!==$sector_v317)return $sector_v317;

        // Premium 2026 templates: each sector gets a deliberately different composition.
        if(strpos($key,'-premium')!==false){
            $premium_sample=self::demo_for($key);

            if($key==='corporate-premium'){
                $out[]=self::container(array(self::element('wpsoft-hero-split-modern',array('eyebrow'=>'Kurumsal Premium','title'=>'Güven veren güçlü bir dijital marka deneyimi','text'=>'Kurumsal kimliğinizi modern tasarım ve net mesajlarla öne çıkarın.','primary_text'=>'Projeyi Konuşalım','primary_url'=>array('url'=>'#iletisim'),'accent'=>$tpl['accent'],'image'=>array('url'=>$premium_sample)))));
                $out[]=self::container(array(self::element('wpsoft-logo-marquee')));
                $out[]=self::container(array(self::element('wpsoft-icon-grid'),self::element('wpsoft-number-cards')));
                $out[]=self::container(array(self::element('wpsoft-image-text',array('eyebrow'=>'Yaklaşımımız','title'=>'Strateji, tasarım ve teknoloji aynı sistemde','image'=>array('url'=>$premium_sample)))));
                $out[]=self::container(array(self::element('wpsoft-testimonial-slider')));
                $out[]=self::container(array(self::element('wpsoft-faq'),$cta));
                foreach(self::ready_page_blocks($key,$tpl['accent'],$premium_sample) as $ready_block) $out[]=$ready_block;
                return $out;
            }

            if($key==='industrial-premium' || $key==='machinery-premium'){
                $out[]=self::container(array(self::element('wpsoft-video-hero',array('eyebrow'=>'Endüstriyel Güç','title'=>'Üretim kabiliyetinizi dijitalde güçlü biçimde anlatın','description'=>'Teknik uzmanlık, makine parkuru ve servis gücü tek sayfada.','poster'=>array('url'=>$premium_sample),'button_text'=>'Teknik Bilgi Al','button_url'=>array('url'=>'#iletisim')))));
                $out[]=self::container(array(self::element('wpsoft-stats-grid')));
                $out[]=self::container(array(self::element('wpsoft-tabs-modern')));
                $out[]=self::container(array(self::element('wpsoft-card-carousel')));
                $out[]=self::container(array(self::element('wpsoft-image-text',array('eyebrow'=>'Üretim','title'=>'Hassasiyet ve sürdürülebilir performans','image'=>array('url'=>$premium_sample)))));
                $out[]=self::container(array(self::element('wpsoft-logo-marquee'),$cta));
                foreach(self::ready_page_blocks($key,$tpl['accent'],$premium_sample) as $ready_block) $out[]=$ready_block;
                return $out;
            }

            if($key==='ecommerce-premium'){
                $out[]=self::container(array(self::element('wpsoft-hero-slider',array('items'=>array(
                    array('eyebrow'=>'WPSoft Premium','title'=>$title,'text'=>'Sektörünüze uygun demo görselle hazır başlangıç.','button'=>'Teklif Al','url'=>array('url'=>'#iletisim'),'image'=>array('url'=>$sample)),
                    array('eyebrow'=>'Modern Tasarım','title'=>'Mobil uyumlu ve kolay düzenlenebilir','text'=>'Elementor üzerinden tüm alanları değiştirebilirsiniz.','button'=>'İncele','url'=>array('url'=>'#'),'image'=>array('url'=>self::demo('agency.svg')))
                )))));
                $out[]=self::container(array(self::element('wpsoft-hover-reveal')));
                $out[]=self::container(array(self::element('wpsoft-card-carousel')));
                $out[]=self::container(array(self::element('wpsoft-info-strip')));
                $out[]=self::container(array(self::element('wpsoft-testimonial-slider')));
                $out[]=self::container(array($cta));
                foreach(self::ready_page_blocks($key,$tpl['accent'],$premium_sample) as $ready_block) $out[]=$ready_block;
                return $out;
            }

            if($key==='saas-premium'){
                $out[]=self::container(array(self::element('wpsoft-gradient-heading',array('eyebrow'=>'Yeni Nesil Platform','title'=>'İş akışınızı daha hızlı ve akıllı hale getirin','text'=>'Modern SaaS ürünleri için dönüşüm odaklı premium yapı.'))));
                $out[]=self::container(array(self::element('wpsoft-logo-marquee')));
                $out[]=self::container(array(self::element('wpsoft-icon-orbit')));
                $out[]=self::container(array(self::element('wpsoft-tabs-modern')));
                $out[]=self::container(array(self::element('wpsoft-animated-counter'),self::element('wpsoft-mouse-follow-card')));
                $out[]=self::container(array(self::element('wpsoft-testimonial-slider'),$cta));
                foreach(self::ready_page_blocks($key,$tpl['accent'],$premium_sample) as $ready_block) $out[]=$ready_block;
                return $out;
            }

            if($key==='hotel-premium'){
                $out[]=self::container(array(self::element('wpsoft-hero-slider',array('items'=>array(
                    array('eyebrow'=>'WPSoft Premium','title'=>$title,'text'=>'Sektörünüze uygun demo görselle hazır başlangıç.','button'=>'Teklif Al','url'=>array('url'=>'#iletisim'),'image'=>array('url'=>$sample)),
                    array('eyebrow'=>'Modern Tasarım','title'=>'Mobil uyumlu ve kolay düzenlenebilir','text'=>'Elementor üzerinden tüm alanları değiştirebilirsiniz.','button'=>'İncele','url'=>array('url'=>'#'),'image'=>array('url'=>self::demo('agency.svg')))
                )))));
                $out[]=self::container(array(self::element('wpsoft-image-carousel',array('gallery'=>array(array('url'=>self::demo_for($key),'id'=>0),array('url'=>self::demo('corporate.svg'),'id'=>0),array('url'=>self::demo('agency.svg'),'id'=>0),array('url'=>self::demo('hotel.svg'),'id'=>0))))));
                $out[]=self::container(array(self::element('wpsoft-image-text',array('eyebrow'=>'Deneyim','title'=>'Konforun ötesinde unutulmaz bir konaklama','image'=>array('url'=>$premium_sample)))));
                $out[]=self::container(array(self::element('wpsoft-icon-grid')));
                $out[]=self::container(array(self::element('wpsoft-testimonial-slider')));
                $out[]=self::container(array($cta));
                foreach(self::ready_page_blocks($key,$tpl['accent'],$premium_sample) as $ready_block) $out[]=$ready_block;
                return $out;
            }

            if($key==='clinic-premium'){
                $out[]=self::container(array(self::element('wpsoft-hero-split-modern',array('eyebrow'=>'Sağlık & Güven','title'=>'Modern sağlık hizmetlerinde güven veren deneyim','text'=>'Uzmanlıkları ve hasta deneyimini sade, profesyonel bir yapıyla sunun.','primary_text'=>'Randevu Al','primary_url'=>array('url'=>'#iletisim'),'accent'=>$tpl['accent'],'image'=>array('url'=>$premium_sample)))));
                $out[]=self::container(array(self::element('wpsoft-icon-grid')));
                $out[]=self::container(array(self::element('wpsoft-before-after')));
                $out[]=self::container(array(self::element('wpsoft-number-cards')));
                $out[]=self::container(array(self::element('wpsoft-testimonial-slider')));
                $out[]=self::container(array(self::element('wpsoft-contact-cards'),self::element('wpsoft-faq')));
                foreach(self::ready_page_blocks($key,$tpl['accent'],$premium_sample) as $ready_block) $out[]=$ready_block;
                return $out;
            }

            if($key==='architecture-premium'){
                $out[]=self::container(array(self::element('wpsoft-video-hero',array('eyebrow'=>'Architecture','title'=>'Mekânları zamansız deneyimlere dönüştürüyoruz','poster'=>array('url'=>$premium_sample),'button_text'=>'Projeleri Gör','button_url'=>array('url'=>'#projeler')))));
                $out[]=self::container(array(self::element('wpsoft-portfolio')));
                $out[]=self::container(array(self::element('wpsoft-number-cards')));
                $out[]=self::container(array(self::element('wpsoft-image-text',array('eyebrow'=>'Tasarım Yaklaşımı','title'=>'Detay, malzeme ve işlev arasında denge','image'=>array('url'=>$premium_sample)))));
                $out[]=self::container(array(self::element('wpsoft-testimonial-slider'),$cta));
                foreach(self::ready_page_blocks($key,$tpl['accent'],$premium_sample) as $ready_block) $out[]=$ready_block;
                return $out;
            }

            if($key==='restaurant-premium'){
                $out[]=self::container(array(self::element('wpsoft-video-hero',array('eyebrow'=>'Fine Dining','title'=>'Lezzeti unutulmaz bir deneyime dönüştürün','poster'=>array('url'=>$premium_sample),'button_text'=>'Rezervasyon','button_url'=>array('url'=>'#rezervasyon')))));
                $out[]=self::container(array(self::element('wpsoft-image-carousel',array('gallery'=>array(array('url'=>self::demo_for($key),'id'=>0),array('url'=>self::demo('corporate.svg'),'id'=>0),array('url'=>self::demo('agency.svg'),'id'=>0),array('url'=>self::demo('hotel.svg'),'id'=>0))))));
                $out[]=self::container(array(self::element('wpsoft-tabs-modern')));
                $out[]=self::container(array(self::element('wpsoft-image-text',array('eyebrow'=>'Şefin Hikâyesi','title'=>'Mevsimsel ürünler, özgün yorumlar','image'=>array('url'=>$premium_sample)))));
                $out[]=self::container(array(self::element('wpsoft-testimonial-slider'),$cta));
                foreach(self::ready_page_blocks($key,$tpl['accent'],$premium_sample) as $ready_block) $out[]=$ready_block;
                return $out;
            }

            // agency-premium
            $out[]=self::container(array(self::element('wpsoft-gradient-heading',array('eyebrow'=>'Creative Agency','title'=>'Markaları fark edilen dijital deneyimlere dönüştürüyoruz','text'=>'Strateji, tasarım ve motion odaklı premium ajans deneyimi.'))));
            $out[]=self::container(array(self::element('wpsoft-marquee-text')));
            $out[]=self::container(array(self::element('wpsoft-hover-reveal')));
            $out[]=self::container(array(self::element('wpsoft-reveal-cards')));
            $out[]=self::container(array(self::element('wpsoft-animated-counter')));
            $out[]=self::container(array(self::element('wpsoft-testimonial-slider'),$cta));
            return $out;
        }


        if($key==='contact-wpforms-modern'){
            return array(
                self::container(array(self::element('wpsoft-hero-split-modern',array(
                    'eyebrow'=>'İLETİŞİM','title'=>'Yeni projenizi birlikte konuşalım',
                    'text'=>'İhtiyacınızı anlatın; size uygun çözüm ve yol haritasıyla geri dönelim.',
                    'primary_text'=>'Mesaj Gönder','primary_url'=>array('url'=>'#iletisim-formu'),
                    'secondary_text'=>'Telefon','secondary_url'=>array('url'=>'tel:+900000000000'),
                    'float_value'=>'24s','float_text'=>'Ortalama geri dönüş'
                ))),array('background_background'=>'classic','background_color'=>'#f8fafc','padding'=>array('unit'=>'px','top'=>'22','right'=>'22','bottom'=>'22','left'=>'22','isLinked'=>true))),
                self::container(array(self::element('wpsoft-contact-cards'))),
                self::container(array(
                    self::element('wpsoft-heading',array('eyebrow'=>'MESAJ GÖNDERİN','title'=>'Size nasıl yardımcı olabiliriz?','description'=>'WPForms formunuzu aşağıdaki alana bağlayın.')),
                    self::element('wpsoft-wpforms',array('empty_title'=>'WPForms formunu seçin','empty_text'=>'Bu şablonu Elementor ile düzenleyip WPForms widgetından form ID seçin.'))
                ),array('background_background'=>'classic','background_color'=>'#ffffff','padding'=>array('unit'=>'px','top'=>'58','right'=>'24','bottom'=>'58','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-faq'))),
                self::container(array(self::element('wpsoft-cta',array('title'=>'Doğrudan görüşmek ister misiniz?','description'=>'Telefon veya e-posta üzerinden bize ulaşabilirsiniz.','button_text'=>'Bizi Arayın','button_url'=>array('url'=>'tel:+900000000000'),'bg'=>'#0f172a'))))
            );
        }

        if($key==='contact-wpforms-bento'){
            return array(
                self::container(array(self::element('wpsoft-gradient-heading',array('eyebrow'=>'CONTACT','title'=>'Size nasıl yardımcı olabiliriz?','text'=>'Satış, proje ve destek talepleriniz için doğru kanala hızlıca ulaşın.'))),array('background_background'=>'classic','background_color'=>'#f5f3ff')),
                self::container(array(self::element('wpsoft-contact-cards'),self::element('wpsoft-info-strip'))),
                self::container(array(
                    self::element('wpsoft-image-text',array('eyebrow'=>'BİRLİKTE ÇALIŞALIM','title'=>'İhtiyacınızı anlatın, gerisini birlikte planlayalım','description'=>'Formu doldurun; talebinizi değerlendirip size en uygun çözümle dönüş yapalım.','image'=>array('url'=>self::demo_v2('agency-signature.svg')))),
                    self::element('wpsoft-wpforms',array('empty_title'=>'WPForms seçimi gerekli','empty_text'=>'Form ID seçildiğinde iletişim formunuz burada görüntülenecek.'))
                )),
                self::container(array(self::element('wpsoft-testimonial-slider'))),
                self::container(array(self::element('wpsoft-faq')))
            );
        }

        if($key==='contact-wpforms-dark'){
            return array(
                self::container(array(self::element('wpsoft-hero-spotlight',array('eyebrow'=>'LET’S TALK','title'=>'Bir sonraki güçlü projeyi birlikte başlatalım','text'=>'Kısa bir mesaj bırakın. Projenizi, hedefinizi ve ihtiyacınızı birlikte değerlendirelim.','button_text'=>'Formu Doldur','button_url'=>array('url'=>'#iletisim-formu')))),array('background_background'=>'classic','background_color'=>'#050816')),
                self::container(array(self::element('wpsoft-contact-cards')),array('background_background'=>'classic','background_color'=>'#07111f')),
                self::container(array(
                    self::element('wpsoft-gradient-heading',array('eyebrow'=>'MESSAGE','title'=>'Projenizi anlatın','text'=>'WPForms formunuzu bu premium koyu alana bağlayın.')),
                    self::element('wpsoft-wpforms',array('empty_title'=>'WPForms formunu seçin','empty_text'=>'Elementor içindeki WPSoft · WPForms widgetından form ID belirleyin.'))
                ),array('background_background'=>'classic','background_color'=>'#07111f','padding'=>array('unit'=>'px','top'=>'64','right'=>'24','bottom'=>'64','left'=>'24','isLinked'=>false))),
                self::container(array(self::element('wpsoft-logo-marquee'),self::element('wpsoft-testimonial-slider')),array('background_background'=>'classic','background_color'=>'#f8fafc')),
                self::container(array(self::element('wpsoft-cta',array('title'=>'Daha hızlı iletişim için bizi arayın','description'=>'Çalışma saatleri içinde doğrudan görüşebilirsiniz.','button_text'=>'Telefon','button_url'=>array('url'=>'tel:+900000000000'),'bg'=>'#0f172a'))))
            );
        }

        if(in_array($key,array('agency','restaurant','travel','gym','event'),true))$out[]=self::container(array($video));
        elseif(in_array($key,array('hotel','realestate','automotive','furniture','beauty'),true))$out[]=self::container(array($slider));
        else $out[]=self::container(array($hero));
        if($key==='contact'){ $out[]=self::container(array(self::element('wpsoft-contact-cards')));$out[]=self::container(array(self::element('wpsoft-faq')));$out[]=self::container(array($cta));return$out;}
        if(in_array($key,array('software','security'),true)){ $out[]=self::container(array(self::element('wpsoft-logo-marquee')));$out[]=self::container(array(self::element('wpsoft-icon-grid')));$out[]=self::container(array(self::element('wpsoft-tabs-modern')));$out[]=self::container(array(self::element('wpsoft-card-carousel')));$out[]=self::container(array(self::element('wpsoft-testimonial-slider')));$out[]=self::container(array($cta));return$out;}
        if(in_array($key,array('beauty','dentist','health'),true)){ $out[]=self::container(array($about));$out[]=self::container(array(self::element('wpsoft-before-after')));$out[]=self::container(array(self::element('wpsoft-icon-list')));$out[]=self::container(array(self::element('wpsoft-testimonial-slider')));$out[]=self::container(array(self::element('wpsoft-contact-cards')));$out[]=self::container(array($cta));return$out;}
        if(in_array($key,array('hotel','restaurant','travel'),true)){ $out[]=self::container(array(self::element('wpsoft-image-carousel',array('gallery'=>array(array('url'=>self::demo_for($key),'id'=>0),array('url'=>self::demo('corporate.svg'),'id'=>0),array('url'=>self::demo('agency.svg'),'id'=>0),array('url'=>self::demo('hotel.svg'),'id'=>0))))));$out[]=self::container(array(self::element('wpsoft-tabs-modern')));$out[]=self::container(array(self::element('wpsoft-testimonial-slider')));$out[]=self::container(array($cta));return$out;}
        if(in_array($key,array('construction','realestate','furniture'),true)){ $out[]=self::container(array($about));$out[]=self::container(array(self::element('wpsoft-portfolio')));$out[]=self::container(array(self::element('wpsoft-number-cards')));$out[]=self::container(array(self::element('wpsoft-testimonial-slider')));$out[]=self::container(array($cta));return$out;}
        $out[]=self::container(array($about));foreach(self::services_section($tpl['accent']) as $sec)$out[]=$sec;$out[]=self::container(array(self::element('wpsoft-feature-list')));$out[]=self::container(array(self::element('wpsoft-stats-grid')));$out[]=self::container(array(self::element('wpsoft-testimonial-slider')));$out[]=self::container(array(self::element('wpsoft-faq')));$out[]=self::container(array($cta));return$out;
    }

    /**
     * Header/Footer templates exposed inside WPSoft Şablonlar.
     * Uses the same source as the Header/Footer Templates admin screen,
     * so both libraries always stay synchronized.
     */
    public static function hf_library_items() {
        $items = array( 'headers' => array(), 'footers' => array() );
        if ( ! class_exists( 'WPST_Header_Footer_Templates' ) ) {
            return $items;
        }

        foreach ( WPST_Header_Footer_Templates::headers() as $key => $item ) {
            $items['headers'][] = array(
                'key' => $key,
                'title' => $item['title'],
                'desc' => $item['desc'],
                'preview_image' => WPST_URL . 'assets/images/header-footer/' . $item['preview'],
                'type' => 'header',
                'apply_url' => wp_nonce_url(
                    admin_url( 'admin-post.php?action=wpst_create_elementor_hf_template&type=header&template=' . $key ),
                    'wpst_create_hf_header_' . $key
                ),
            );
        }

        foreach ( WPST_Header_Footer_Templates::footers() as $key => $item ) {
            $items['footers'][] = array(
                'key' => $key,
                'title' => $item['title'],
                'desc' => $item['desc'],
                'preview_image' => WPST_URL . 'assets/images/header-footer/' . $item['preview'],
                'type' => 'footer',
                'apply_url' => wp_nonce_url(
                    admin_url( 'admin-post.php?action=wpst_create_elementor_hf_template&type=footer&template=' . $key ),
                    'wpst_create_hf_footer_' . $key
                ),
            );
        }
        return $items;
    }

}