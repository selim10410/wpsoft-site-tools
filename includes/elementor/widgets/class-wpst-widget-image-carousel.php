<?php
if(!defined('ABSPATH'))exit;
class WPST_Widget_Image_Carousel extends WPST_Elementor_Widget_Base{
 public function get_name(){return'wpsoft-image-carousel';}
 public function get_title(){return'WPSoft · Image Carousel 2.0';}
 public function get_icon(){return'eicon-slider-push';}
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'Görseller'));
  $this->add_control('gallery',array('label'=>'Galeri','type'=>\Elementor\Controls_Manager::GALLERY,'default'=>array()));
  $this->add_responsive_control('visible',array(
   'label'=>'Görünen Görsel','type'=>\Elementor\Controls_Manager::SELECT,
   'default'=>'3','tablet_default'=>'2','mobile_default'=>'1',
   'options'=>array('1'=>'1','2'=>'2','3'=>'3','4'=>'4')
  ));
  $this->add_responsive_control('gap',array(
   'label'=>'Görsel Aralığı','type'=>\Elementor\Controls_Manager::SLIDER,
   'size_units'=>array('px'),'range'=>array('px'=>array('min'=>0,'max'=>60)),
   'default'=>array('unit'=>'px','size'=>18),
   'selectors'=>array('{{WRAPPER}} .wpst-ew-image-carousel'=>'--wpst-carousel-gap:{{SIZE}}{{UNIT}};','{{WRAPPER}} .wpst-ew-carousel-track'=>'gap:{{SIZE}}{{UNIT}};')
  ));
  $this->add_control('aspect',array(
   'label'=>'Görsel Oranı','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'4-3',
   'options'=>array('auto'=>'Otomatik','1-1'=>'1:1','4-3'=>'4:3','3-2'=>'3:2','16-9'=>'16:9')
  ));
  $this->add_control('fit',array(
   'label'=>'Görsel Yerleşimi','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'cover',
   'options'=>array('cover'=>'Kırp / Cover','contain'=>'Tam Göster / Contain')
  ));
  $this->add_control('card_style',array(
   'label'=>'Carousel Stili','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'modern',
   'options'=>array('modern'=>'Modern','editorial'=>'Editorial','soft'=>'Soft','borderless'=>'Borderless'),
   'prefix_class'=>'wpst-image-carousel-style-'
  ));
  $this->add_control('peek',array('label'=>'Sonraki Görseli Göster','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes'));
  $this->add_responsive_control('peek_width',array(
   'label'=>'Peek Genişliği','type'=>\Elementor\Controls_Manager::SLIDER,
   'range'=>array('px'=>array('min'=>20,'max'=>180)),
   'default'=>array('size'=>90,'unit'=>'px'),'tablet_default'=>array('size'=>60,'unit'=>'px'),'mobile_default'=>array('size'=>38,'unit'=>'px'),
   'selectors'=>array('{{WRAPPER}} .wpst-ew-image-carousel'=>'--wpst-carousel-peek:{{SIZE}}px;')
  ));
  $this->add_control('touch_swipe',array('label'=>'Dokunmatik Kaydırma','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes'));
  $this->add_control('mouse_drag',array('label'=>'Mouse ile Sürükle','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes'));
  $this->add_control('show_progress',array('label'=>'İlerleme Çizgisi','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes'));
  $this->end_controls_section();

  $this->start_controls_section('style',array('label'=>'Kart Stili','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_responsive_control('radius',array(
   'label'=>'Köşe Yuvarlaklığı','type'=>\Elementor\Controls_Manager::SLIDER,
   'size_units'=>array('px'),'range'=>array('px'=>array('min'=>0,'max'=>48)),
   'default'=>array('unit'=>'px','size'=>20),
   'selectors'=>array('{{WRAPPER}} .wpst-ew-image-carousel figure'=>'border-radius:{{SIZE}}{{UNIT}};')
  ));
  $this->add_control('shadow',array(
   'label'=>'Yumuşak Gölge','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes',
   'prefix_class'=>'wpst-carousel-shadow-'
  ));
  $this->end_controls_section();
  $this->standard_responsive_controls();
 }
 protected function render(){
  $s=$this->get_settings_for_display();
  $desktop=!empty($s['visible'])?(int)$s['visible']:3;
  $tablet=!empty($s['visible_tablet'])?(int)$s['visible_tablet']:min(2,$desktop);
  $mobile=!empty($s['visible_mobile'])?(int)$s['visible_mobile']:1;
  $aspect=in_array($s['aspect'],array('auto','1-1','4-3','3-2','16-9'),true)?$s['aspect']:'4-3';
  $fit='contain'===$s['fit']?'contain':'cover';
  echo'<div class="wpst-ew-image-carousel ratio-'.esc_attr($aspect).'" data-visible="'.absint($desktop).'" data-visible-tablet="'.absint($tablet).'" data-visible-mobile="'.absint($mobile).'" data-fit="'.esc_attr($fit).'" data-peek="'.('yes'===($s['peek']??'yes')?'yes':'no').'" data-touch-swipe="'.('yes'===($s['touch_swipe']??'yes')?'yes':'no').'" data-mouse-drag="'.('yes'===($s['mouse_drag']??'yes')?'yes':'no').'" role="region" aria-roledescription="carousel" aria-label="Görsel carousel">';
  echo'<div class="wpst-ew-carousel-track">';
  if(!empty($s['gallery'])){
   foreach($s['gallery'] as $img){
    $url=!empty($img['url'])?$img['url']:'';
    $id=!empty($img['id'])?absint($img['id']):0;
    $alt=$id?get_post_meta($id,'_wp_attachment_image_alt',true):'';
    echo'<figure><img src="'.esc_url($url).'" alt="'.esc_attr($alt).'" loading="lazy" decoding="async"></figure>';
   }
  }else{
   for($i=1;$i<=5;$i++)echo'<figure class="wpst-ew-carousel-placeholder"><span>WPSoft '.$i.'</span></figure>';
  }
  echo'</div><div class="wpst-ew-carousel-nav"><button class="wpst-ew-carousel-prev" type="button" aria-label="Önceki görsel">'.(class_exists('WPST_Icon_Library')?WPST_Icon_Library::svg('arrow-left',array('size'=>16)):'‹').'</button><button class="wpst-ew-carousel-next" type="button" aria-label="Sonraki görsel">'.(class_exists('WPST_Icon_Library')?WPST_Icon_Library::svg('arrow-right',array('size'=>16)):'›').'</button></div>'; if('yes'===($s['show_progress']??'yes'))echo'<div class="wpst-carousel-progress" aria-hidden="true"><span></span></div>'; echo'</div>';
 }
}
