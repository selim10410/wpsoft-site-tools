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
  $this->add_control('mobile_panel_bg',array('label'=>'Panel Arka Planı','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#ffffff','selectors'=>array('{{WRAPPER}} .wpst-navigation'=>'--wpst-nav-mobile-bg:{{VALUE}};')));
  $this->add_control('mobile_text_color',array('label'=>'Mobil Menü Rengi','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#0f172a','selectors'=>array('{{WRAPPER}} .wpst-navigation'=>'--wpst-nav-mobile-color:{{VALUE}};')));
  $this->add_control('mobile_hover_bg',array('label'=>'Mobil Hover Arka Planı','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#f8fafc','selectors'=>array('{{WRAPPER}} .wpst-navigation'=>'--wpst-nav-mobile-hover:{{VALUE}};')));
  $this->add_control('mobile_overlay_color',array('label'=>'Overlay Rengi','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'rgba(15,23,42,.46)','selectors'=>array('{{WRAPPER}} .wpst-navigation'=>'--wpst-nav-overlay-bg:{{VALUE}};')));
  $this->add_control('mobile_cta_bg',array('label'=>'CTA Arka Planı','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#2563eb','selectors'=>array('{{WRAPPER}} .wpst-navigation'=>'--wpst-nav-mobile-cta-bg:{{VALUE}};')));
  $this->add_control('mobile_cta_color',array('label'=>'CTA Yazı Rengi','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#ffffff','selectors'=>array('{{WRAPPER}} .wpst-navigation'=>'--wpst-nav-mobile-cta-color:{{VALUE}};')));
  $this->add_control('mobile_cta_hover_bg',array('label'=>'CTA Hover Arka Planı','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#1d4ed8','selectors'=>array('{{WRAPPER}} .wpst-navigation'=>'--wpst-nav-mobile-cta-hover-bg:{{VALUE}};')));
  $this->add_responsive_control('mobile_panel_width',array('label'=>'Panel Genişliği','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>240,'max'=>480)),'default'=>array('unit'=>'px','size'=>340),'selectors'=>array('{{WRAPPER}} .wpst-navigation'=>'--wpst-nav-mobile-width:{{SIZE}}px;')));
  $this->end_controls_section();

  $this->standard_responsive_controls();
 }

 protected function render(){
  $s=$this->get_settings_for_display();
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
   '--wpst-nav-mobile-bg'=>($s['mobile_panel_bg']??''),
   '--wpst-nav-mobile-color'=>($s['mobile_text_color']??''),
   '--wpst-nav-mobile-hover'=>($s['mobile_hover_bg']??''),
   '--wpst-nav-overlay-bg'=>($s['mobile_overlay_color']??''),
   '--wpst-nav-mobile-cta-bg'=>($s['mobile_cta_bg']??''),
   '--wpst-nav-mobile-cta-color'=>($s['mobile_cta_color']??''),
   '--wpst-nav-mobile-cta-hover-bg'=>($s['mobile_cta_hover_bg']??''),
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
  $style_attr=!empty($nav_style)?' style="'.esc_attr(implode(';',$nav_style)).'"':'';

  echo'<nav class="wpst-navigation"'.$style_attr.' aria-label="'.esc_attr($aria_label).'" data-wpst-nav data-wpst-menu-id="'.absint($menu_id).'" data-wpst-nav-fallback="'.esc_attr($fallback).'">';
  echo'<button type="button" class="wpst-nav-toggle" aria-expanded="false" aria-controls="'.esc_attr($uid).'"><span class="wpst-nav-toggle-bars"><i></i><i></i><i></i></span><span class="screen-reader-text">Menüyü aç</span></button>';
  echo'<div class="wpst-nav-overlay" aria-hidden="true"></div>';
  echo'<div class="wpst-nav-mobile-panel" id="'.esc_attr($uid).'">';
  echo'<div class="wpst-nav-mobile-head"><button type="button" class="wpst-nav-close" aria-label="Menüyü kapat">×</button></div>';
  echo'<div class="wpst-nav-menu-host">';

  if($menu_id){
   wp_nav_menu(array('menu'=>$menu_id,'container'=>false,'menu_class'=>'wpst-navigation-menu','fallback_cb'=>false,'depth'=>4));
  }elseif('pages'===$fallback){
   echo'<ul class="wpst-navigation-menu">'; wp_list_pages(array('title_li'=>'','depth'=>2)); echo'</ul>';
  }elseif(\Elementor\Plugin::$instance->editor->is_edit_mode()){
   echo'<div class="wpst-navigation-empty">Navigasyon için bir WordPress menüsü seçin.</div>';
  }

  echo'</div>';
  if(array_key_exists('mobile_cta_enabled',$raw) && 'yes'===($s['mobile_cta_enabled']??'') && !empty($s['mobile_cta_text'])){
    $cta_url=!empty($s['mobile_cta_url']['url'])?$s['mobile_cta_url']['url']:'#iletisim';
    $target=!empty($s['mobile_cta_url']['is_external'])?' target="_blank"':'';
    $rel=!empty($s['mobile_cta_url']['nofollow'])?' rel="nofollow noopener"':(!empty($s['mobile_cta_url']['is_external'])?' rel="noopener"':'');
    echo'<div class="wpst-nav-mobile-footer"><a class="wpst-nav-mobile-cta" href="'.esc_url($cta_url).'"'.$target.$rel.'><span>'.esc_html($s['mobile_cta_text']).'</span><i>→</i></a></div>';
  }
  echo'</div></nav>';
 }
}
