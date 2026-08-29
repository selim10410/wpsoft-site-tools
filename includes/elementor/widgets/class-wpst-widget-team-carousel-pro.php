<?php
if(!defined('ABSPATH'))exit;
class WPST_Widget_Team_Carousel_Pro extends WPST_Elementor_Widget_Base{
 public function get_name(){return'wpsoft-team-carousel-pro';}public function get_title(){return'WPSoft · Team Carousel Pro 2.0';}public function get_icon(){return'eicon-person';} public function get_keywords(){return array('team','carousel','staff','people','profiles','wpsoft');}
 protected function register_controls(){$this->start_controls_section('content',array('label'=>'Ekip'));
  $this->wpst_signature_preset_control();$r=new \Elementor\Repeater();$r->add_control('image',array('label'=>'Fotoğraf','type'=>\Elementor\Controls_Manager::MEDIA));$r->add_control('name',array('label'=>'Ad','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Ekip Üyesi'));$r->add_control('role',array('label'=>'Pozisyon','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Uzman'));$this->add_control('items',array('type'=>\Elementor\Controls_Manager::REPEATER,'fields'=>$r->get_controls(),'default'=>array(array('name'=>'Ayşe Yılmaz','role'=>'Creative Director'),array('name'=>'Mert Kaya','role'=>'Project Lead'),array('name'=>'Deniz Akın','role'=>'Designer')),'title_field'=>'{{{ name }}}'));
  $this->add_responsive_control('columns',array('label'=>'Kolon','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'4','tablet_default'=>'2','mobile_default'=>'1','options'=>array('1'=>'1','2'=>'2','3'=>'3','4'=>'4'),'selectors'=>array('{{WRAPPER}} .wpst-team-pro'=>'--wpst-team-cols:{{VALUE}};')));
  $this->add_control('layout_variant',array(
   'label'=>'Ekip Yerleşimi','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'cards',
   'options'=>array('cards'=>'Portrait Cards','editorial'=>'Editorial','compact'=>'Compact Profiles','spotlight'=>'Spotlight','strip'=>'Horizontal Strip'),
   'prefix_class'=>'wpst-team-layout-'
  ));
  $this->add_responsive_control('gap',array('label'=>'Kart Aralığı','type'=>\Elementor\Controls_Manager::SLIDER,'size_units'=>array('px'),'range'=>array('px'=>array('min'=>0,'max'=>60)),'default'=>array('unit'=>'px','size'=>18),'selectors'=>array('{{WRAPPER}} .wpst-ew-team-carousel'=>'--wpst-team-gap:{{SIZE}}{{UNIT}};','{{WRAPPER}} .wpst-team-pro'=>'gap:{{SIZE}}{{UNIT}};')));
  $this->add_control('carousel_mode',array('label'=>'Carousel Modu','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes'));
  $this->add_control('peek',array('label'=>'Sonraki Profili Göster','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes','condition'=>array('carousel_mode'=>'yes')));
  $this->add_responsive_control('peek_width',array(
   'label'=>'Peek Genişliği','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>20,'max'=>180)),
   'default'=>array('size'=>92,'unit'=>'px'),'tablet_default'=>array('size'=>58,'unit'=>'px'),'mobile_default'=>array('size'=>38,'unit'=>'px'),
   'selectors'=>array('{{WRAPPER}} .wpst-ew-team-carousel'=>'--wpst-carousel-peek:{{SIZE}}px;'),
   'condition'=>array('carousel_mode'=>'yes')
  ));
  $this->add_control('touch_swipe',array('label'=>'Dokunmatik Kaydırma','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes','condition'=>array('carousel_mode'=>'yes')));
  $this->add_control('mouse_drag',array('label'=>'Mouse ile Sürükle','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes','condition'=>array('carousel_mode'=>'yes')));
  $this->add_control('show_arrows',array('label'=>'Okları Göster','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes','condition'=>array('carousel_mode'=>'yes')));
  $this->add_control('show_progress',array('label'=>'İlerleme Çizgisi','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes','condition'=>array('carousel_mode'=>'yes')));
$this->end_controls_section();
  $this->start_controls_section('team_card_style',array('label'=>'Kart Biçimi','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('surface',array('label'=>'Kart Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-team-pro'=>'--wpst-team-surface:{{VALUE}};')));
  $this->add_control('border',array('label'=>'Kenarlık','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-team-pro'=>'--wpst-team-border:{{VALUE}};')));
  $this->add_control('hover_border',array('label'=>'Hover Kenarlık','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-team-pro'=>'--wpst-team-hover-border:{{VALUE}};')));
  $this->add_responsive_control('card_radius',array('label'=>'Kart Radius','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>50)),'default'=>array('size'=>22),'selectors'=>array('{{WRAPPER}} .wpst-team-pro'=>'--wpst-team-radius:{{SIZE}}px;')));
  $this->add_responsive_control('image_height',array('label'=>'Fotoğraf Yüksekliği','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>180,'max'=>620)),'default'=>array('size'=>360),'tablet_default'=>array('size'=>320),'mobile_default'=>array('size'=>300),'selectors'=>array('{{WRAPPER}} .wpst-team-pro'=>'--wpst-team-image-h:{{SIZE}}px;')));
  $this->add_control('hover_motion',array('label'=>'Hover Hareketi','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'lift','options'=>array('none'=>'Yok','lift'=>'Yüksel','image'=>'Görsel Zoom'),'prefix_class'=>'wpst-team-motion-'));
  $this->end_controls_section();

  $this->start_controls_section('team_type_style',array('label'=>'İçerik Biçimi','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('name_color',array('label'=>'İsim Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-team-pro'=>'--wpst-team-name:{{VALUE}};')));
  $this->add_control('role_color',array('label'=>'Pozisyon Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-team-pro'=>'--wpst-team-role:{{VALUE}};')));
  $this->add_control('nav_color',array('label'=>'Ok Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-team-carousel'=>'--wpst-team-nav-color:{{VALUE}};')));
  $this->add_control('nav_bg',array('label'=>'Ok Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-team-carousel'=>'--wpst-team-nav-bg:{{VALUE}};')));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'name_typography','label'=>'İsim Tipografi','selector'=>'{{WRAPPER}} .wpst-team-pro h3'));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'role_typography','label'=>'Pozisyon Tipografi','selector'=>'{{WRAPPER}} .wpst-team-pro span'));
  $this->end_controls_section();
  $this->standard_responsive_controls();
    }
 protected function render(){
  $s=$this->get_settings_for_display();
  $desktop=!empty($s['columns'])?(int)$s['columns']:4;
  $tablet=!empty($s['columns_tablet'])?(int)$s['columns_tablet']:2;
  $mobile=!empty($s['columns_mobile'])?(int)$s['columns_mobile']:1;
  $carousel='yes'===($s['carousel_mode']??'yes');
  if($carousel){
   echo'<div class="wpst-ew-team-carousel" data-visible="'.absint($desktop).'" data-visible-tablet="'.absint($tablet).'" data-visible-mobile="'.absint($mobile).'" data-peek="'.('yes'===($s['peek']??'yes')?'yes':'no').'" data-touch-swipe="'.('yes'===($s['touch_swipe']??'yes')?'yes':'no').'" data-mouse-drag="'.('yes'===($s['mouse_drag']??'yes')?'yes':'no').'" role="region" aria-roledescription="carousel" aria-label="Ekip carousel">';
  }
  echo'<div class="wpst-team-pro">';
  foreach((array)$s['items'] as $it){
   echo'<article>';
   if(!empty($it['image']['url']))echo'<img src="'.esc_url($it['image']['url']).'" alt="'.esc_attr($it['name']).'" loading="lazy" decoding="async">';
   else echo'<div class="wpst-team-ph"></div>';
   echo'<h3>'.esc_html($it['name']).'</h3><span>'.esc_html($it['role']).'</span></article>';
  }
  echo'</div>';
  if($carousel){
   if('yes'===($s['show_arrows']??'yes'))echo'<div class="wpst-team-carousel-nav"><button type="button" class="wpst-team-carousel-prev" aria-label="Önceki ekip üyesi">'.(class_exists('WPST_Icon_Library')?WPST_Icon_Library::svg('arrow-left',array('size'=>16)):'←').'</button><button type="button" class="wpst-team-carousel-next" aria-label="Sonraki ekip üyesi">'.(class_exists('WPST_Icon_Library')?WPST_Icon_Library::svg('arrow-right',array('size'=>16)):'→').'</button></div>';
   if('yes'===($s['show_progress']??'yes'))echo'<div class="wpst-carousel-progress" aria-hidden="true"><span></span></div>';
   echo'</div>';
  }
 }
}