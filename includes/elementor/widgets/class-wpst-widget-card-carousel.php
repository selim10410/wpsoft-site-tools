<?php
if(!defined('ABSPATH'))exit;
class WPST_Widget_Card_Carousel extends WPST_Elementor_Widget_Base{
 public function get_name(){return'wpsoft-card-carousel';}
 public function get_title(){return'WPSoft · Card Carousel 2.0';}
 public function get_icon(){return'eicon-slider-album';}
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'Kartlar'));
  $rep=new \Elementor\Repeater();
  $rep->add_control('image',array('label'=>'Görsel','type'=>\Elementor\Controls_Manager::MEDIA));
  $rep->add_control('title',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Modern çözüm'));
  $rep->add_control('text',array('label'=>'Açıklama','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'Kısa ve anlaşılır açıklama.'));
  $rep->add_control('wpst_icon',array('label'=>'WPSoft Icon','type'=>\Elementor\Controls_Manager::SELECT2,'options'=>class_exists('WPST_Icon_Library')?WPST_Icon_Library::options():array(),'default'=>'sparkles','label_block'=>true));
  $this->add_control('items',array('label'=>'Kartlar','type'=>\Elementor\Controls_Manager::REPEATER,'fields'=>$rep->get_controls(),'default'=>array(array('title'=>'Strateji','text'=>'Doğru başlangıç için net plan.'),array('title'=>'Tasarım','text'=>'Modern ve kullanılabilir deneyim.'),array('title'=>'Geliştirme','text'=>'Hızlı ve sürdürülebilir altyapı.')),'title_field'=>'{{{ title }}}'));
  $this->add_control('placeholder_text',array('label'=>'Görsel Yoksa','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'WPSoft'));
  $this->add_responsive_control('visible',array('label'=>'Görünen Kart','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'3','tablet_default'=>'2','mobile_default'=>'1','options'=>array('1'=>'1','2'=>'2','3'=>'3','4'=>'4')));
  $this->add_responsive_control('gap',array('label'=>'Kart Aralığı','type'=>\Elementor\Controls_Manager::SLIDER,'size_units'=>array('px'),'range'=>array('px'=>array('min'=>0,'max'=>60)),'default'=>array('unit'=>'px','size'=>18),'selectors'=>array('{{WRAPPER}} .wpst-ew-card-carousel'=>'--wpst-carousel-gap:{{SIZE}}{{UNIT}};','{{WRAPPER}} .wpst-ew-card-carousel-track'=>'gap:{{SIZE}}{{UNIT}};')));
  $this->add_control('card_style',array('label'=>'Kart Stili','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'elevated','options'=>array('elevated'=>'Elevated','soft'=>'Soft','editorial'=>'Editorial','dark'=>'Dark'),'prefix_class'=>'wpst-card-carousel-style-'));
  $this->add_control('peek',array('label'=>'Sonraki Kartı Göster','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes'));
  $this->add_responsive_control('peek_width',array(
   'label'=>'Peek Genişliği','type'=>\Elementor\Controls_Manager::SLIDER,
   'range'=>array('px'=>array('min'=>20,'max'=>180)),
   'default'=>array('size'=>100,'unit'=>'px'),'tablet_default'=>array('size'=>64,'unit'=>'px'),'mobile_default'=>array('size'=>42,'unit'=>'px'),
   'selectors'=>array('{{WRAPPER}} .wpst-ew-card-carousel'=>'--wpst-carousel-peek:{{SIZE}}px;')
  ));
  $this->add_control('touch_swipe',array('label'=>'Dokunmatik Kaydırma','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes'));
  $this->add_control('mouse_drag',array('label'=>'Mouse ile Sürükle','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes'));
  $this->add_control('show_progress',array('label'=>'İlerleme Çizgisi','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes'));
  $this->end_controls_section();

  $this->start_controls_section('style',array('label'=>'Kart Stili','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_responsive_control('radius',array('label'=>'Köşe Yuvarlaklığı','type'=>\Elementor\Controls_Manager::SLIDER,'size_units'=>array('px'),'range'=>array('px'=>array('min'=>0,'max'=>48)),'default'=>array('unit'=>'px','size'=>20),'selectors'=>array('{{WRAPPER}} .wpst-ew-card-carousel article'=>'border-radius:{{SIZE}}{{UNIT}};')));
  $this->add_responsive_control('media_height',array('label'=>'Görsel Yüksekliği','type'=>\Elementor\Controls_Manager::SLIDER,'size_units'=>array('px'),'range'=>array('px'=>array('min'=>120,'max'=>520)),'default'=>array('unit'=>'px','size'=>220),'selectors'=>array('{{WRAPPER}} .wpst-ew-card-media'=>'height:{{SIZE}}{{UNIT}};')));
  $this->end_controls_section();
  $this->standard_responsive_controls();
 }
 protected function render(){
  $s=$this->get_settings_for_display();
  $desktop=!empty($s['visible'])?(int)$s['visible']:3;
  $tablet=!empty($s['visible_tablet'])?(int)$s['visible_tablet']:min(2,$desktop);
  $mobile=!empty($s['visible_mobile'])?(int)$s['visible_mobile']:1;
  echo'<div class="wpst-ew-card-carousel" data-visible="'.absint($desktop).'" data-visible-tablet="'.absint($tablet).'" data-visible-mobile="'.absint($mobile).'" data-peek="'.('yes'===($s['peek']??'yes')?'yes':'no').'" data-touch-swipe="'.('yes'===($s['touch_swipe']??'yes')?'yes':'no').'" data-mouse-drag="'.('yes'===($s['mouse_drag']??'yes')?'yes':'no').'" role="region" aria-roledescription="carousel" aria-label="Kart carousel"><div class="wpst-ew-card-carousel-track">';
  foreach((array)$s['items'] as $i){
   $url=!empty($i['image']['url'])?$i['image']['url']:'';
   echo'<article><div class="wpst-ew-card-media">';
   if($url)echo'<img src="'.esc_url($url).'" alt="" loading="lazy" decoding="async">';
   else echo'<span>'.esc_html($s['placeholder_text']).'</span>';
   echo'</div><div class="wpst-ew-card-copy"><i class="wpst-card-carousel-icon">'.(class_exists('WPST_Icon_Library')?WPST_Icon_Library::svg(!empty($i['wpst_icon'])?$i['wpst_icon']:'sparkles',array('size'=>18)):'').'</i><h3>'.esc_html($i['title']).'</h3><p>'.esc_html($i['text']).'</p></div></article>';
  }
  echo'</div><div class="wpst-ew-card-nav"><button class="wpst-ew-card-prev" type="button" aria-label="Önceki kart">'.(class_exists('WPST_Icon_Library')?WPST_Icon_Library::svg('arrow-left',array('size'=>16)):'‹').'</button><button class="wpst-ew-card-next" type="button" aria-label="Sonraki kart">'.(class_exists('WPST_Icon_Library')?WPST_Icon_Library::svg('arrow-right',array('size'=>16)):'›').'</button></div>'; if('yes'===($s['show_progress']??'yes'))echo'<div class="wpst-carousel-progress" aria-hidden="true"><span></span></div>'; echo'</div>';
 }
}
