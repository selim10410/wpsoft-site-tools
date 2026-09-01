<?php
if(!defined('ABSPATH'))exit;

class WPST_Widget_Navigation extends WPST_Elementor_Widget_Base{
 public function get_name(){return'wpsoft-navigation';}
 public function get_title(){return'WPSoft · Navigation';}
 public function get_icon(){return'eicon-nav-menu';}
 public function get_categories(){return array('wpsoft-navigation');}
 public function get_keywords(){return array('menu','navigation','nav','header','mega menu');}

 private function menu_options(){
  $out=array('0'=>'Menü Seç');
  foreach(wp_get_nav_menus() as $menu)$out[(string)$menu->term_id]=$menu->name;
  return $out;
 }

 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'Navigasyon'));
  $this->add_control('menu_id',array(
   'label'=>'WordPress Menüsü',
   'type'=>\Elementor\Controls_Manager::SELECT,
   'options'=>$this->menu_options(),
   'default'=>'0',
   'description'=>'Görünüm → Menüler bölümünde oluşturduğun menüyü seç.'
  ));
  $this->add_control('fallback',array(
   'label'=>'Menü Seçilmediyse',
   'type'=>\Elementor\Controls_Manager::SELECT,
   'default'=>'none',
   'options'=>array('none'=>'Hiçbir Şey Gösterme','first'=>'İlk Menüyü Kullan','pages'=>'Sayfaları Listele')
  ));
  $this->add_control('submenu_indicator',array(
   'label'=>'Alt Menü Oku',
   'type'=>\Elementor\Controls_Manager::SWITCHER,
   'return_value'=>'yes','default'=>'yes',
   'prefix_class'=>'wpst-nav-submenu-indicator-'
  ));
  $this->add_control('aria_label',array(
   'label'=>'Navigasyon Etiketi',
   'type'=>\Elementor\Controls_Manager::TEXT,
   'default'=>'Ana navigasyon',
   'description'=>'Erişilebilirlik için menünün aria-label değeridir.'
  ));

  $this->add_control('mobile_behavior',array(
   'label'=>'Mobil Davranış',
   'type'=>\Elementor\Controls_Manager::SELECT,
   'default'=>'hamburger',
   'options'=>array(
    'hamburger'=>'Hamburger Menü',
    'inherit'=>'Header Mobil Menüyü Kullansın',
    'wrap'=>'Satıra Geç',
    'scroll'=>'Yatay Kaydır'
   ),
   'prefix_class'=>'wpst-nav-mobile-'
  ));
  $this->add_control('mobile_breakpoint',array(
   'label'=>'Mobil Menü Breakpoint','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'767',
   'options'=>array('767'=>'Mobil · 767px','1024'=>'Tablet + Mobil · 1024px'),
   'prefix_class'=>'wpst-nav-breakpoint-'
  ));
  $this->add_control('mobile_panel_side',array(
   'label'=>'Mobil Menü Yönü','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'right',
   'options'=>array('right'=>'Sağdan Aç','left'=>'Soldan Aç'),
   'prefix_class'=>'wpst-nav-drawer-'
  ));
  $this->add_control('mobile_overlay',array(
   'label'=>'Mobil Overlay','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes',
   'prefix_class'=>'wpst-nav-overlay-'
  ));
  $this->add_control('mobile_design_source',array(
   'label'=>'Mobil Menü Tasarımı','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'global',
   'options'=>array('global'=>'Global Tasarımı Kullan','custom'=>'Özel Tasarım'),
   'description'=>'Global seçim, WPSoft → Global Tasarım → Mobil Menü presetini kullanır.'
  ));

  $this->add_control('mobile_cta_enabled',array(
   'label'=>'Mobil CTA Butonu',
   'type'=>\Elementor\Controls_Manager::SWITCHER,
   'return_value'=>'yes',
   'default'=>'yes'
  ));
  $this->add_control('mobile_cta_text',array(
   'label'=>'Mobil CTA Yazısı',
   'type'=>\Elementor\Controls_Manager::TEXT,
   'default'=>'Teklif Al',
   'condition'=>array('mobile_cta_enabled'=>'yes')
  ));
  $this->add_control('mobile_cta_url',array(
   'label'=>'Mobil CTA Linki',
   'type'=>\Elementor\Controls_Manager::URL,
   'default'=>array('url'=>'#iletisim'),
   'condition'=>array('mobile_cta_enabled'=>'yes')
  ));
  $this->end_controls_section();

  $this->start_controls_section('mobile_footer_content',array('label'=>'Mobil Menü Alt Alanı'));
  $this->add_control('mobile_logo',array('label'=>'Mobil Logo','type'=>\Elementor\Controls_Manager::MEDIA,'description'=>'Boş bırakılırsa Header Builder veya site logosu kullanılır.'));
  $this->add_responsive_control('mobile_logo_width',array('label'=>'Mobil Logo Genişliği','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>40,'max'=>240)),'default'=>array('unit'=>'px','size'=>150),'selectors'=>array('{{WRAPPER}} .wps-mobile-drawer .wpst-nav-mobile-brand img'=>'width:{{SIZE}}{{UNIT}};max-width:{{SIZE}}{{UNIT}};')));
  $this->add_responsive_control('mobile_logo_height',array('label'=>'Mobil Logo Maks. Yüksekliği','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>24,'max'=>100)),'default'=>array('unit'=>'px','size'=>54),'selectors'=>array('{{WRAPPER}} .wps-mobile-drawer .wpst-nav-mobile-brand img'=>'max-height:{{SIZE}}{{UNIT}};')));
  $this->add_control('mobile_search_show',array('label'=>'Aramayı Göster','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>''));
  $this->add_control('mobile_cta_subtitle',array('label'=>'CTA Alt Metin','type'=>\Elementor\Controls_Manager::TEXT,'condition'=>array('mobile_cta_enabled'=>'yes')));
  $this->add_control('mobile_cta_phone',array('label'=>'CTA Telefon','type'=>\Elementor\Controls_Manager::TEXT,'condition'=>array('mobile_cta_enabled'=>'yes')));
  $this->add_control('mobile_cta_icon',array('label'=>'CTA İkon','type'=>\Elementor\Controls_Manager::ICONS,'condition'=>array('mobile_cta_enabled'=>'yes')));
  $this->add_control('mobile_social_show',array('label'=>'Sosyal Medyayı Göster','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>''));
  foreach(array('facebook'=>'Facebook','instagram'=>'Instagram','linkedin'=>'LinkedIn','youtube'=>'YouTube') as $network=>$label)$this->add_control('mobile_social_'.$network,array('label'=>$label.' URL','type'=>\Elementor\Controls_Manager::URL,'condition'=>array('mobile_social_show'=>'yes')));
  $this->end_controls_section();

  $this->start_controls_section('layout',array('label'=>'Yerleşim','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_responsive_control('align',array(
   'label'=>'Hizalama','type'=>\Elementor\Controls_Manager::CHOOSE,'default'=>'center',
   'options'=>array(
    'flex-start'=>array('title'=>'Sol','icon'=>'eicon-h-align-left'),
    'center'=>array('title'=>'Orta','icon'=>'eicon-h-align-center'),
    'flex-end'=>array('title'=>'Sağ','icon'=>'eicon-h-align-right')
   ),
   'selectors'=>array('{{WRAPPER}} .wpst-navigation .wpst-navigation-menu'=>'justify-content:{{VALUE}};')
  ));
  $this->add_responsive_control('item_gap',array(
   'label'=>'Menü Aralığı','type'=>\Elementor\Controls_Manager::SLIDER,
   'range'=>array('px'=>array('min'=>0,'max'=>60)),
   'default'=>array('size'=>8,'unit'=>'px'),
   'selectors'=>array('{{WRAPPER}} .wpst-navigation'=>'--wpst-nav-gap:{{SIZE}}px;')
  ));
  $this->add_responsive_control('item_padding_x',array(
   'label'=>'Link Yatay Boşluk','type'=>\Elementor\Controls_Manager::SLIDER,
   'range'=>array('px'=>array('min'=>0,'max'=>32)),
   'default'=>array('size'=>12,'unit'=>'px'),
   'selectors'=>array('{{WRAPPER}} .wpst-navigation'=>'--wpst-nav-pad-x:{{SIZE}}px;')
  ));
  $this->add_responsive_control('item_height',array(
   'label'=>'Link Yüksekliği','type'=>\Elementor\Controls_Manager::SLIDER,
   'range'=>array('px'=>array('min'=>32,'max'=>70)),
   'default'=>array('size'=>44,'unit'=>'px'),
   'selectors'=>array('{{WRAPPER}} .wpst-navigation'=>'--wpst-nav-height:{{SIZE}}px;')
  ));
  $this->end_controls_section();

  $this->start_controls_section('style_nav',array('label'=>'Menü Biçimi','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('text_color',array('label'=>'Menü Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-navigation'=>'--wpst-nav-color:{{VALUE}};')));
  $this->add_control('hover_color',array('label'=>'Hover Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-navigation'=>'--wpst-nav-hover:{{VALUE}};')));
  $this->add_control('active_color',array('label'=>'Aktif Menü Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-navigation'=>'--wpst-nav-active:{{VALUE}};')));
  $this->add_control('hover_bg',array('label'=>'Hover Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-navigation'=>'--wpst-nav-hover-bg:{{VALUE}};')));
  $this->add_control('active_bg',array('label'=>'Aktif Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-navigation'=>'--wpst-nav-active-bg:{{VALUE}};')));
  $this->add_control('menu_preset',array(
   'label'=>'Menü Görünümü',
   'type'=>\Elementor\Controls_Manager::SELECT,
   'default'=>'modern',
   'options'=>array(
    'modern'=>'Modern',
    'minimal'=>'Minimal',
    'floating'=>'Floating',
    'glass'=>'Glass',
    'clean'=>'Clean'
   ),
   'prefix_class'=>'wpst-navigation-preset-'
  ));

  $this->add_control('active_style',array(
   'label'=>'Aktif Menü Stili','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'pill',
   'options'=>array(
    'none'=>'Yok',
    'pill'=>'Pill',
    'underline'=>'Alt Çizgi',
    'soft'=>'Soft Gölge',
    'outline'=>'Outline'
   ),
   'prefix_class'=>'wpst-navigation-active-'
  ));

  $this->add_control('hover_motion',array(
   'label'=>'Hover Animasyonu',
   'type'=>\Elementor\Controls_Manager::SELECT,
   'default'=>'lift',
   'options'=>array(
    'none'=>'Yok',
    'lift'=>'Hafif Yüksel',
    'slide'=>'Hafif Kay',
    'scale'=>'Scale'
   ),
   'prefix_class'=>'wpst-navigation-hover-'
  ));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array(
   'name'=>'nav_typography','selector'=>'{{WRAPPER}} .wpst-navigation .wpst-navigation-menu>li>a'
  ));
  $this->add_responsive_control('radius',array(
   'label'=>'Link Köşesi','type'=>\Elementor\Controls_Manager::SLIDER,
   'range'=>array('px'=>array('min'=>0,'max'=>30)),
   'default'=>array('size'=>10),
   'selectors'=>array('{{WRAPPER}} .wpst-navigation'=>'--wpst-nav-radius:{{SIZE}}px;')
  ));
  $this->add_control('item_border_color',array(
   'label'=>'Link Kenarlık Rengi',
   'type'=>\Elementor\Controls_Manager::COLOR,
   'selectors'=>array('{{WRAPPER}} .wpst-navigation'=>'--wpst-nav-border:{{VALUE}};')
  ));
  $this->add_responsive_control('underline_width',array(
   'label'=>'Aktif Çizgi Kalınlığı',
   'type'=>\Elementor\Controls_Manager::SLIDER,
   'range'=>array('px'=>array('min'=>1,'max'=>6)),
   'default'=>array('size'=>2,'unit'=>'px'),
   'condition'=>array('active_style'=>'underline'),
   'selectors'=>array('{{WRAPPER}} .wpst-navigation'=>'--wpst-nav-underline:{{SIZE}}px;')
  ));
  $this->end_controls_section();

  $this->start_controls_section('style_dropdown',array('label'=>'Dropdown','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('dropdown_bg',array('label'=>'Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#ffffff','selectors'=>array('{{WRAPPER}} .wpst-navigation'=>'--wpst-nav-dropdown-bg:{{VALUE}};')));
  $this->add_control('dropdown_color',array('label'=>'Yazı Rengi','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#0f172a','selectors'=>array('{{WRAPPER}} .wpst-navigation'=>'--wpst-nav-dropdown-color:{{VALUE}};')));
  $this->add_control('dropdown_hover_bg',array('label'=>'Hover Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#f8fafc','selectors'=>array('{{WRAPPER}} .wpst-navigation'=>'--wpst-nav-dropdown-hover:{{VALUE}};')));
  $this->add_control('dropdown_border',array('label'=>'Kenarlık Rengi','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'rgba(148,163,184,.18)','selectors'=>array('{{WRAPPER}} .wpst-navigation'=>'--wpst-nav-dropdown-border:{{VALUE}};')));
  $this->add_control('dropdown_active_color',array('label'=>'Aktif Alt Menü Rengi','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#2563eb','selectors'=>array('{{WRAPPER}} .wpst-navigation'=>'--wpst-nav-dropdown-active:{{VALUE}};')));
  $this->add_responsive_control('dropdown_width',array('label'=>'Dropdown Genişliği','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>160,'max'=>420)),'default'=>array('size'=>240),'selectors'=>array('{{WRAPPER}} .wpst-navigation'=>'--wpst-nav-dropdown-width:{{SIZE}}px;')));
  $this->add_responsive_control('dropdown_radius',array('label'=>'Dropdown Köşesi','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>30)),'default'=>array('size'=>14),'selectors'=>array('{{WRAPPER}} .wpst-navigation'=>'--wpst-nav-dropdown-radius:{{SIZE}}px;')));
  $this->add_responsive_control('dropdown_padding',array('label'=>'Dropdown İç Boşluk','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>24)),'default'=>array('size'=>8),'selectors'=>array('{{WRAPPER}} .wpst-navigation'=>'--wpst-nav-dropdown-pad:{{SIZE}}px;')));
  $this->add_control('dropdown_shadow',array(
   'label'=>'Dropdown Gölgesi',
   'type'=>\Elementor\Controls_Manager::SELECT,
   'default'=>'soft',
   'options'=>array('none'=>'Yok','soft'=>'Soft','medium'=>'Orta','strong'=>'Belirgin'),
   'prefix_class'=>'wpst-navigation-dropdown-shadow-'
  ));
  $this->end_controls_section();

  $this->start_controls_section('style_mobile',array('label'=>'Mobil Menü','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('hamburger_style',array(
   'label'=>'Hamburger Görünümü',
   'type'=>\Elementor\Controls_Manager::SELECT,
   'default'=>'soft',
   'options'=>array(
    'soft'=>'Soft',
    'minimal'=>'Minimal',
    'floating'=>'Floating',
    'dark'=>'Siyah',
    'accent'=>'Vurgu'
   ),
   'prefix_class'=>'wpst-hamburger-style-'
  ));
  $this->add_control('hamburger_animation',array(
   'label'=>'Hamburger Animasyonu',
   'type'=>\Elementor\Controls_Manager::SELECT,
   'default'=>'morph',
   'options'=>array('morph'=>'Morph · X','rotate'=>'Dönüş · X','simple'=>'Sade · X'),
   'prefix_class'=>'wpst-hamburger-animation-'
  ));
  $this->add_responsive_control('hamburger_size',array(
   'label'=>'Hamburger Buton Boyutu',
   'type'=>\Elementor\Controls_Manager::SLIDER,
   'range'=>array('px'=>array('min'=>38,'max'=>58,'step'=>1)),
   'default'=>array('size'=>46,'unit'=>'px'),
   'selectors'=>array('{{WRAPPER}} .wpst-navigation'=>'--wpst-nav-toggle-size:{{SIZE}}px;')
  ));
  $this->add_responsive_control('hamburger_radius',array(
   'label'=>'Hamburger Köşesi',
   'type'=>\Elementor\Controls_Manager::SLIDER,
   'range'=>array('px'=>array('min'=>0,'max'=>30,'step'=>1)),
   'default'=>array('size'=>14,'unit'=>'px'),
   'selectors'=>array('{{WRAPPER}} .wpst-navigation'=>'--wpst-nav-toggle-radius:{{SIZE}}px;')
  ));
  $this->add_control('hamburger_color',array('label'=>'Hamburger Rengi','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#0f172a','selectors'=>array('{{WRAPPER}} .wpst-nav-toggle'=>'--wpst-nav-toggle-color:{{VALUE}};')));
  $this->add_control('hamburger_bg',array('label'=>'Hamburger Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'rgba(148,163,184,.10)','selectors'=>array('{{WRAPPER}} .wpst-nav-toggle'=>'--wpst-nav-toggle-bg:{{VALUE}};')));
  $this->add_control('mobile_heading_panel',array('label'=>'Panel','type'=>\Elementor\Controls_Manager::HEADING,'separator'=>'before'));
  $this->add_control('mobile_panel_bg',array('label'=>'Panel Arka Planı','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-navigation'=>'--wps-mobile-menu-bg:{{VALUE}};')));
  $this->add_control('mobile_heading_items',array('label'=>'Menü Öğeleri','type'=>\Elementor\Controls_Manager::HEADING,'separator'=>'before'));
  $this->add_control('mobile_text_color',array('label'=>'Mobil Menü Yazı Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wps-mobile-drawer'=>'--wps-mobile-menu-text:{{VALUE}};')));
  $this->add_control('mobile_hover_text_color',array('label'=>'Mobil Hover Yazı Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wps-mobile-drawer'=>'--wps-mobile-menu-hover-text:{{VALUE}};')));
  $this->add_control('mobile_submenu_text_color',array('label'=>'Alt Menü Yazı Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wps-mobile-drawer'=>'--wps-mobile-menu-submenu-text:{{VALUE}};')));
  $this->add_control('mobile_hover_bg',array('label'=>'Mobil Hover Arka Planı','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-navigation'=>'--wpst-nav-mobile-hover:{{VALUE}};')));
  $this->add_control('mobile_overlay_color',array('label'=>'Overlay Rengi','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'rgba(15,23,42,.46)','selectors'=>array('{{WRAPPER}} .wpst-navigation'=>'--wpst-nav-overlay-bg:{{VALUE}};')));
  $this->add_control('mobile_cta_bg',array('label'=>'CTA Arka Planı','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#2563eb','selectors'=>array('{{WRAPPER}} .wpst-navigation'=>'--wpst-nav-mobile-cta-bg:{{VALUE}};')));
  $this->add_control('mobile_cta_color',array('label'=>'CTA Yazı Rengi','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#ffffff','selectors'=>array('{{WRAPPER}} .wpst-navigation'=>'--wpst-nav-mobile-cta-color:{{VALUE}};')));
  $this->add_control('mobile_cta_hover_bg',array('label'=>'CTA Hover Arka Planı','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#1d4ed8','selectors'=>array('{{WRAPPER}} .wpst-navigation'=>'--wpst-nav-mobile-cta-hover-bg:{{VALUE}};')));
  $this->add_responsive_control('mobile_panel_width',array('label'=>'Panel Genişliği','type'=>\Elementor\Controls_Manager::SLIDER,'size_units'=>array('%','px'),'range'=>array('%'=>array('min'=>80,'max'=>96),'px'=>array('min'=>240,'max'=>480)),'default'=>array('unit'=>'%','size'=>90),'selectors'=>array('{{WRAPPER}} .wpst-navigation'=>'--wpst-nav-mobile-width:{{SIZE}}{{UNIT}};')));
  $this->add_responsive_control('mobile_panel_max_width',array('label'=>'Panel Maksimum Genişliği','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>300,'max'=>520)),'default'=>array('unit'=>'px','size'=>410),'selectors'=>array('{{WRAPPER}} .wpst-navigation'=>'--wps-mobile-menu-max-width:{{SIZE}}px;')));
  $this->add_control('mobile_heading_active',array('label'=>'Aktif Öğe','type'=>\Elementor\Controls_Manager::HEADING,'separator'=>'before'));
  $this->add_control('mobile_active_bg',array('label'=>'Aktif Öğe Arka Planı','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-navigation'=>'--wps-mobile-menu-active-bg:{{VALUE}};')));
  $this->add_control('mobile_active_color',array('label'=>'Aktif Yazı Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-navigation'=>'--wps-mobile-menu-active-color:{{VALUE}};')));
  $this->add_control('mobile_chevron_color',array('label'=>'Chevron Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-navigation'=>'--wps-mobile-menu-chevron:{{VALUE}};')));
  $this->add_control('mobile_close_bg',array('label'=>'Kapatma Butonu Arka Planı','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-navigation'=>'--wps-mobile-menu-close-bg:{{VALUE}};')));
  $this->add_control('mobile_close_color',array('label'=>'Kapatma Butonu Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-navigation'=>'--wps-mobile-menu-close-color:{{VALUE}};')));
  $this->add_control('mobile_surface_color',array('label'=>'Kart Yüzeyi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-navigation'=>'--wps-mobile-nav-surface:{{VALUE}};')));
  $this->add_control('mobile_muted_color',array('label'=>'İkincil Yazı / Chevron','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-navigation'=>'--wps-mobile-nav-muted:{{VALUE}};')));
  $this->add_control('mobile_search_style',array('label'=>'Arama Alanı','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-navigation'=>'--wps-mobile-nav-search-bg:{{VALUE}};')));
  $this->add_control('mobile_heading_icons',array('label'=>'İkonlar','type'=>\Elementor\Controls_Manager::HEADING,'separator'=>'before'));
  $this->add_responsive_control('mobile_icon_box_size',array('label'=>'İkon Kutusu Boyutu','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>36,'max'=>58)),'default'=>array('unit'=>'px','size'=>46),'selectors'=>array('{{WRAPPER}} .wpst-navigation'=>'--wps-mobile-nav-icon-box:{{SIZE}}px;')));
  $this->add_control('mobile_icon_color',array('label'=>'İkon Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wps-mobile-drawer'=>'--wps-mobile-icon-color:{{VALUE}};')));
  $this->add_control('mobile_icon_active_color',array('label'=>'Aktif İkon Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wps-mobile-drawer'=>'--wps-mobile-icon-active-color:{{VALUE}};')));
  $this->add_control('mobile_icon_bg',array('label'=>'İkon Arka Planı','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wps-mobile-drawer'=>'--wps-mobile-icon-bg:{{VALUE}};')));
  $this->add_control('mobile_icon_active_bg',array('label'=>'Aktif İkon Arka Planı','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wps-mobile-drawer'=>'--wps-mobile-icon-active-bg:{{VALUE}};')));
  $this->add_responsive_control('mobile_icon_size',array('label'=>'İkon Boyutu','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>14,'max'=>30)),'default'=>array('unit'=>'px','size'=>20),'selectors'=>array('{{WRAPPER}} .wps-mobile-drawer'=>'--wps-mobile-icon-size:{{SIZE}}px;')));
  $this->add_responsive_control('mobile_icon_radius',array('label'=>'İkon Kutusu Radius','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>28)),'default'=>array('unit'=>'px','size'=>13),'selectors'=>array('{{WRAPPER}} .wps-mobile-drawer'=>'--wps-mobile-icon-radius:{{SIZE}}px;')));
  $this->add_control('mobile_heading_cta',array('label'=>'CTA','type'=>\Elementor\Controls_Manager::HEADING,'separator'=>'before'));
  $this->add_control('mobile_cta_title_color',array('label'=>'CTA Başlık Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wps-mobile-drawer'=>'--wps-mobile-cta-title:{{VALUE}};')));
  $this->add_control('mobile_cta_subtitle_color',array('label'=>'CTA Alt Metin Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wps-mobile-drawer'=>'--wps-mobile-cta-subtitle:{{VALUE}};')));
  $this->add_control('mobile_cta_icon_color',array('label'=>'CTA İkon Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wps-mobile-drawer'=>'--wps-mobile-cta-icon-color:{{VALUE}};')));
  $this->add_control('mobile_cta_icon_bg',array('label'=>'CTA İkon Arka Planı','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wps-mobile-drawer'=>'--wps-mobile-cta-icon-bg:{{VALUE}};')));
  $this->add_control('mobile_heading_social',array('label'=>'Sosyal Medya','type'=>\Elementor\Controls_Manager::HEADING,'separator'=>'before'));
  $this->add_control('mobile_social_color',array('label'=>'Sosyal İkon Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wps-mobile-drawer'=>'--wps-mobile-social-color:{{VALUE}};')));
  $this->add_control('mobile_social_hover_color',array('label'=>'Sosyal Hover Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wps-mobile-drawer'=>'--wps-mobile-social-hover-color:{{VALUE}};')));
  $this->add_control('mobile_social_bg',array('label'=>'Sosyal Buton Arka Planı','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wps-mobile-drawer'=>'--wps-mobile-social-bg:{{VALUE}};')));
  $this->add_control('mobile_social_hover_bg',array('label'=>'Sosyal Hover Arka Planı','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wps-mobile-drawer'=>'--wps-mobile-social-hover-bg:{{VALUE}};')));
  $this->add_control('mobile_social_border',array('label'=>'Sosyal Border Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wps-mobile-drawer'=>'--wps-mobile-social-border:{{VALUE}};')));
  $this->add_responsive_control('mobile_social_size',array('label'=>'Sosyal Buton Boyutu','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>36,'max'=>56)),'default'=>array('unit'=>'px','size'=>44),'selectors'=>array('{{WRAPPER}} .wps-mobile-drawer'=>'--wps-mobile-social-size:{{SIZE}}px;')));
  $this->add_responsive_control('mobile_social_icon_size',array('label'=>'Sosyal İkon Boyutu','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>14,'max'=>28)),'default'=>array('unit'=>'px','size'=>20),'selectors'=>array('{{WRAPPER}} .wps-mobile-drawer'=>'--wps-mobile-social-icon-size:{{SIZE}}px;')));
  $this->add_responsive_control('mobile_social_gap',array('label'=>'Sosyal Buton Aralığı','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>24)),'default'=>array('unit'=>'px','size'=>10),'selectors'=>array('{{WRAPPER}} .wps-mobile-drawer'=>'--wps-mobile-social-gap:{{SIZE}}px;')));
  $this->add_responsive_control('mobile_social_radius',array('label'=>'Sosyal Buton Radius','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>28)),'default'=>array('unit'=>'px','size'=>13),'selectors'=>array('{{WRAPPER}} .wps-mobile-drawer'=>'--wps-mobile-social-radius:{{SIZE}}px;')));
  $this->add_responsive_control('mobile_item_height',array('label'=>'Mobil Öğe Yüksekliği','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>48,'max'=>76)),'default'=>array('unit'=>'px','size'=>58),'selectors'=>array('{{WRAPPER}} .wpst-navigation'=>'--wps-mobile-menu-item-height:{{SIZE}}px;')));
  $this->add_responsive_control('mobile_item_gap',array('label'=>'Mobil Öğe Aralığı','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>16)),'default'=>array('unit'=>'px','size'=>6),'selectors'=>array('{{WRAPPER}} .wpst-navigation'=>'--wps-mobile-menu-gap:{{SIZE}}px;')));
  $this->add_responsive_control('mobile_item_radius',array('label'=>'Aktif Öğe Köşesi','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>28)),'default'=>array('unit'=>'px','size'=>16),'selectors'=>array('{{WRAPPER}} .wpst-navigation'=>'--wps-mobile-menu-radius:{{SIZE}}px;')));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'mobile_typography','label'=>'Mobil Menü Tipografi','selector'=>'{{WRAPPER}} .wpst-nav-mobile-panel .wpst-navigation-menu a'));
  $this->end_controls_section();

  $this->standard_responsive_controls();
 }

 protected function render(){
  $s=$this->get_settings_for_display();
  $global=get_option('wpst_settings',array());
  if(!is_array($global))$global=array();
  /*
   * Elementor Header source isolation:
   * Never synthesize mobile menu/CTA data from implicit defaults. Older Header
   * templates may not contain these keys at all; treating the control defaults as
   * saved data made a mobile Elementor Header look like Live Builder (first menu +
   * "Teklif Al"). Only an explicitly saved fallback/CTA may be rendered.
   */
  $data=$this->get_data();
  $raw=(isset($data['settings']) && is_array($data['settings'])) ? $data['settings'] : array();

  $menu_id=absint($s['menu_id']??0);
  $fallback=array_key_exists('fallback',$raw) ? sanitize_key((string)($s['fallback']??'none')) : 'none';
  if(!$menu_id && 'first'===$fallback){
   $menus=wp_get_nav_menus();
   if(!empty($menus))$menu_id=absint($menus[0]->term_id);
  }

  $aria_label=trim((string)($s['aria_label']??'Ana navigasyon'));
  if(''===$aria_label)$aria_label='Ana navigasyon';
  $uid='wpst-nav-'.$this->get_id();

  // Keep the mobile drawer appearance tied to this Elementor widget's saved
  // settings. This avoids header/global active colors leaking into the drawer
  // when the same template is used as the mobile header.
  $nav_style=array();
  $color_vars=array(
   '--wps-mobile-menu-bg'=>($s['mobile_panel_bg']??''),
   '--wpst-nav-mobile-color'=>($s['mobile_text_color']??''),
   '--wpst-nav-mobile-hover'=>($s['mobile_hover_bg']??''),
   '--wpst-nav-overlay-bg'=>($s['mobile_overlay_color']??''),
   '--wpst-nav-mobile-cta-bg'=>($s['mobile_cta_bg']??''),
   '--wpst-nav-mobile-cta-color'=>($s['mobile_cta_color']??''),
   '--wpst-nav-mobile-cta-hover-bg'=>($s['mobile_cta_hover_bg']??''),
   '--wps-mobile-menu-text'=>($s['mobile_text_color']??''),
   '--wps-mobile-menu-hover-text'=>($s['mobile_hover_text_color']??''),
   '--wps-mobile-menu-submenu-text'=>($s['mobile_submenu_text_color']??''),
   '--wps-mobile-menu-active-color'=>($s['mobile_active_color']??''),
   '--wps-mobile-menu-active-bg'=>($s['mobile_active_bg']??''),
   '--wps-mobile-icon-color'=>($s['mobile_icon_color']??''),
   '--wps-mobile-icon-active-color'=>($s['mobile_icon_active_color']??''),
   '--wps-mobile-icon-bg'=>($s['mobile_icon_bg']??''),
   '--wps-mobile-icon-active-bg'=>($s['mobile_icon_active_bg']??''),
   '--wps-mobile-cta-title'=>($s['mobile_cta_title_color']??''),
   '--wps-mobile-cta-subtitle'=>($s['mobile_cta_subtitle_color']??''),
   '--wps-mobile-social-color'=>($s['mobile_social_color']??''),
   '--wps-mobile-social-hover-color'=>($s['mobile_social_hover_color']??''),
   '--wps-mobile-social-bg'=>($s['mobile_social_bg']??''),
  );
  foreach($color_vars as $var=>$value){
   if(is_string($value) && ''!==trim($value))$nav_style[]=$var.':'.sanitize_hex_color($value);
  }
  // rgba()/CSS variable values are valid Elementor color values too; preserve
  // them if sanitize_hex_color() cannot represent the saved value.
  foreach($color_vars as $var=>$value){
   if(!is_string($value) || ''===trim($value))continue;
   $found=false;
   foreach($nav_style as $decl){if(0===strpos($decl,$var.':')){$found=true;break;}}
   if(!$found)$nav_style[]=$var.':'.wp_strip_all_tags($value);
  }
  if(!empty($s['mobile_panel_width']['size'])){
   $unit=!empty($s['mobile_panel_width']['unit'])?$s['mobile_panel_width']['unit']:'px';
   $nav_style[]='--wpst-nav-mobile-width:'.floatval($s['mobile_panel_width']['size']).preg_replace('/[^a-z%]/i','',$unit);
  }
  foreach(array('mobile_logo_width'=>'--wps-mobile-logo-width','mobile_logo_height'=>'--wps-mobile-logo-height','mobile_icon_size'=>'--wps-mobile-icon-size','mobile_icon_box_size'=>'--wps-mobile-nav-icon-box','mobile_social_size'=>'--wps-mobile-social-size','mobile_social_icon_size'=>'--wps-mobile-social-icon-size','mobile_social_gap'=>'--wps-mobile-social-gap','mobile_social_radius'=>'--wps-mobile-social-radius') as $key=>$var){if(!empty($s[$key]['size'])){$unit=!empty($s[$key]['unit'])?$s[$key]['unit']:'px';$nav_style[]=$var.':'.floatval($s[$key]['size']).preg_replace('/[^a-z%]/i','',$unit);}}
  $mobile_design=$this->resolve_mobile_menu_preset($s,$raw,$global);
  $design_source=$mobile_design['source'];
  $preset=$mobile_design['preset'];
  $style_attr=!empty($nav_style)&&'custom'===$design_source?' style="'.esc_attr(implode(';',$nav_style)).'"':'';
  $panel_tokens=array();
  if('global'===$design_source){
   $global_token_map=array('global_mobile_panel_background'=>'--wps-mobile-menu-bg','global_mobile_item_background'=>'--wps-mobile-nav-surface','global_mobile_text_color'=>'--wps-mobile-menu-text','global_mobile_active_background'=>'--wps-mobile-menu-active-bg','global_mobile_cta_background'=>'--wpst-nav-mobile-cta-bg','global_mobile_icon_background'=>'--wps-mobile-icon-bg','global_mobile_logo_position'=>'--wps-mobile-logo-align');
   foreach($global_token_map as $key=>$var)if(isset($global[$key])&&''!==trim((string)$global[$key]))$panel_tokens[]=$var.':'.wp_strip_all_tags($global[$key]);
   foreach(array('global_mobile_panel_padding'=>'--wps-mobile-panel-padding','global_mobile_item_radius'=>'--wps-mobile-menu-radius','global_mobile_item_height'=>'--wps-mobile-menu-item-height','global_mobile_item_gap'=>'--wps-mobile-menu-gap','global_mobile_icon_box_size'=>'--wps-mobile-nav-icon-box','global_mobile_cta_radius'=>'--wps-mobile-cta-radius','global_mobile_text_size'=>'--wps-mobile-text-size') as $key=>$var)if(isset($global[$key])&&''!==(string)$global[$key])$panel_tokens[]=$var.':'.absint($global[$key]).'px';
  }
  $panel_style=$panel_tokens?' style="'.esc_attr(implode(';',$panel_tokens)).'"':'';

  echo'<nav class="wpst-navigation wpst-mobile-design--'.esc_attr($design_source).' wpst-mobile-preset--'.esc_attr($preset).'"'.$style_attr.' aria-label="'.esc_attr($aria_label).'" data-wpst-nav data-wpst-mobile-design="'.esc_attr($design_source).'" data-wpst-mobile-preset="'.esc_attr($preset).'" data-home-url="'.esc_url(home_url('/')).'" data-wpst-menu-id="'.absint($menu_id).'" data-wpst-nav-fallback="'.esc_attr($fallback).'">';
  echo'<div class="wpst-nav-desktop-host">';$this->render_menu_markup($menu_id,$fallback,false);echo'</div>';
  echo'<button type="button" class="wpst-nav-toggle" aria-expanded="false" aria-controls="'.esc_attr($uid).'"><span class="wpst-nav-toggle-bars"><i></i><i></i><i></i></span><span class="screen-reader-text">Menüyü aç</span></button>';
  echo'<div class="wpst-nav-overlay" aria-hidden="true"></div>';
  echo'<div class="wpst-nav-mobile-panel wps-mobile-drawer wpst-mobile-design--'.esc_attr($design_source).' wpst-mobile-preset--'.esc_attr($preset).'" data-wpst-mobile-preset="'.esc_attr($preset).'" data-wpst-preset-source="'.esc_attr($design_source).'"'.$panel_style.' id="'.esc_attr($uid).'" role="dialog" aria-modal="true" aria-label="'.esc_attr($aria_label).'" aria-hidden="true" tabindex="-1">';
  echo'<div class="wpst-nav-mobile-head"><div class="wpst-nav-mobile-brand wps-mobile-drawer__branding">';
  $widget_logo_id=!empty($s['mobile_logo']['id'])?absint($s['mobile_logo']['id']):0;
  $widget_logo_url=!empty($s['mobile_logo']['url'])?esc_url($s['mobile_logo']['url']):'';
  $has_widget_logo=$widget_logo_id||$widget_logo_url;
  $logo_id=$widget_logo_id?:(!$has_widget_logo&&!empty($global['header_logo_id'])?absint($global['header_logo_id']):0);
  $logo_html=$logo_id?wp_get_attachment_image($logo_id,'full',false,array('class'=>'wpst-site-logo-image','alt'=>get_bloginfo('name'))):'';
  if(!$logo_html&&$widget_logo_url)$logo_html='<img class="wpst-site-logo-image" src="'.esc_url($widget_logo_url).'" alt="'.esc_attr(get_bloginfo('name')).'">';
  if($logo_html){echo'<a href="'.esc_url(home_url('/')).'" class="wpst-nav-drawer-logo" data-widget-logo="'.($has_widget_logo?'1':'0').'">'.$logo_html.'</a>';}
  elseif(has_custom_logo()){the_custom_logo();}else{echo'<a href="'.esc_url(home_url('/')).'" class="wpst-nav-site-title">'.esc_html(get_bloginfo('name')).'</a>';}
  echo'</div><button type="button" class="wpst-nav-close" aria-label="Menüyü kapat"><span aria-hidden="true"></span></button></div>';
  $search_show=array_key_exists('mobile_search_show',$raw)?'yes'===($s['mobile_search_show']??''):!empty($global['header_mobile_search']);
  if($search_show)echo'<div class="wps-mobile-drawer__search"><span aria-hidden="true"></span><input type="search" class="wps-mobile-drawer__search-input" aria-label="Menüde ara" placeholder="'.esc_attr__('Menüde ara…','wpsoft-site-tools').'" autocomplete="off"></div>';
  echo'<div class="wpst-nav-menu-host wps-mobile-drawer__menu">';$this->render_menu_markup($menu_id,$fallback,true);echo'</div>';
  $cta=$this->resolve_mobile_cta($s,$raw,$global);
  if($cta['enabled']){
    $target=$cta['is_external']?' target="_blank"':'';
    $rel=$cta['nofollow']?' rel="nofollow noopener"':($cta['is_external']?' rel="noopener"':'');
    echo'<div class="wpst-nav-mobile-footer"><a class="wpst-nav-mobile-cta wps-mobile-drawer__cta" href="'.esc_url($cta['url']).'"'.$target.$rel.'><span class="wps-mobile-drawer__cta-icon" aria-hidden="true">'.$cta['icon'].'</span><span class="wps-mobile-drawer__cta-copy"><strong>'.esc_html($cta['text']).'</strong>';
    if($cta['subtitle'])echo'<small>'.esc_html($cta['subtitle']).'</small>';
    echo'</span><i aria-hidden="true">→</i></a></div>';
  }
  $social_show=array_key_exists('mobile_social_show',$raw)?'yes'===($s['mobile_social_show']??''):!empty($global['header_mobile_social_enabled']);
  $socials=array();foreach(array('facebook','instagram','linkedin','youtube') as $network){$widget_url=$s['mobile_social_'.$network]['url']??'';$url=$widget_url?:($global['header_mobile_social_'.$network]??'');if($url)$socials[$network]=$url;}
  if($social_show&&$socials){echo'<div class="wps-mobile-drawer__socials" aria-label="'.esc_attr__('Sosyal medya','wpsoft-site-tools').'">';foreach($socials as $network=>$url)echo'<a class="is-'.esc_attr($network).'" href="'.esc_url($url).'" target="_blank" rel="noopener noreferrer" aria-label="'.esc_attr(ucfirst($network)).'">'.$this->social_icon($network).'</a>';echo'</div>';}
  echo'</div></nav>';
 }

 private function resolve_mobile_menu_preset($settings,$raw,$global){
  $allowed=array('corporate-modern','minimal-light','luxury','creative-gradient','e-commerce','hotel-tourism','professional-dark','classic-clean');
  $source=isset($raw['mobile_design_source']) && 'custom'===($settings['mobile_design_source']??'global')?'custom':'global';
  $preset='corporate-modern';
  if('global'===$source && !empty($global['global_mobile_menu_preset']) && in_array($global['global_mobile_menu_preset'],$allowed,true))$preset=$global['global_mobile_menu_preset'];
  return array('source'=>$source,'preset'=>$preset);
 }

 private function render_menu_markup($menu_id,$fallback,$mobile){
  if($menu_id){wp_nav_menu(array('menu'=>$menu_id,'container'=>false,'menu_class'=>'wpst-navigation-menu','fallback_cb'=>false,'depth'=>4,'wpst_navigation'=>'1','wpst_mobile_drawer'=>$mobile));return;}
  if('pages'===$fallback){echo'<ul class="wpst-navigation-menu">';wp_list_pages(array('title_li'=>'','depth'=>2));echo'</ul>';return;}
  if(!$mobile&&\Elementor\Plugin::$instance->editor->is_edit_mode())echo'<div class="wpst-navigation-empty">Navigasyon için bir WordPress menüsü seçin.</div>';
 }

 private function resolve_mobile_cta($settings,$raw,$global){
  $explicit=array_key_exists('mobile_cta_enabled',$raw) || $this->is_enabled($settings['mobile_cta_enabled']??false);
  $use_widget=$explicit && $this->is_enabled($settings['mobile_cta_enabled']??false);
  $use_global=!$explicit && $this->is_enabled($global['header_mobile_cta_enabled']??false);
  $link=$use_widget && is_array($settings['mobile_cta_url']??null)?$settings['mobile_cta_url']:array('url'=>(string)($global['header_mobile_cta_url']??''));
  if($use_widget&&empty($link['url']))$link['url']='#iletisim';
  $text=trim((string)($use_widget?($settings['mobile_cta_text']??''):($use_global?($global['header_mobile_cta_text']??''):'')));
  $icon='';
  if($use_widget&&!empty($settings['mobile_cta_icon']['value'])){ob_start();\Elementor\Icons_Manager::render_icon($settings['mobile_cta_icon'],array('aria-hidden'=>'true'));$icon=ob_get_clean();}
  return array(
   'enabled'=>($use_widget||$use_global)&&''!==$text,
   'text'=>$text,
   'subtitle'=>(string)($use_widget?($settings['mobile_cta_subtitle']??''):($global['header_mobile_contact_title']??'')),
   'url'=>(string)($link['url']??''),
   'is_external'=>$this->is_enabled($link['is_external']??false),
   'nofollow'=>$this->is_enabled($link['nofollow']??false),
   'icon'=>$icon,
  );
 }

 private function is_enabled($value){
  if(true===$value||1===$value||'1'===$value)return true;
  return is_string($value)&&in_array(strtolower(trim($value)),array('yes','true','on'),true);
 }

 private function social_icon($network){
  $paths=array('facebook'=>'<path d="M14 8h3V4h-3c-3 0-5 2-5 5v3H6v4h3v8h4v-8h3l1-4h-4V9c0-.7.3-1 1-1Z"/>','instagram'=>'<rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1"/>','linkedin'=>'<path d="M6 9v12M6 5v.01M10 21V9h4v2c1-3 7-3 7 3v7M3 9h6"/>','youtube'=>'<path d="M22 12s0-5-1-6-4-1-9-1-8 0-9 1-1 6-1 6 0 5 1 6 4 1 9 1 8 0 9-1 1-6 1-6Z"/><path d="m10 9 5 3-5 3Z"/>');
  return isset($paths[$network])?'<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">'.$paths[$network].'</svg>':'';
 }
}
