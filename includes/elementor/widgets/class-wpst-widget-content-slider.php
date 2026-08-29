<?php
if(!defined('ABSPATH'))exit;
class WPST_Widget_Content_Slider extends WPST_Elementor_Widget_Base{
 public function get_name(){return'wpsoft-content-slider';}
 public function get_title(){return'WPSoft · Content Slider 2.0';}
 public function get_icon(){return'eicon-slider-push';} public function get_keywords(){return array('content','slider','hero','slides','carousel','wpsoft');}
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'Slides')); $this->wpst_signature_preset_control();
  $r=new \Elementor\Repeater();
  $r->add_control('eyebrow',array('label'=>'Etiket','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'01 / FEATURE'));
  $r->add_control('title',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Modern içerik slaytı'));
  $r->add_control('text',array('label'=>'Açıklama','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'Metin, görsel ve CTA alanlarını birlikte kullanın.'));
  $r->add_control('image',array('label'=>'Görsel','type'=>\Elementor\Controls_Manager::MEDIA));
  $r->add_control('button',array('label'=>'Buton','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'İncele'));
  $r->add_control('url',array('label'=>'Bağlantı','type'=>\Elementor\Controls_Manager::URL,'default'=>array('url'=>'#')));
  $r->add_control('button_icon',array('label'=>'Buton Icon','type'=>\Elementor\Controls_Manager::SELECT2,'options'=>class_exists('WPST_Icon_Library')?WPST_Icon_Library::options():array(),'default'=>'arrow-up-right','label_block'=>true));
  $this->add_control('items',array('label'=>'Slaytlar','type'=>\Elementor\Controls_Manager::REPEATER,'fields'=>$r->get_controls(),'default'=>array(array('eyebrow'=>'01 / STRATEGY','title'=>'Strateji ile başlayın','text'=>'Doğru yapı ve mesajla güçlü başlangıç.'),array('eyebrow'=>'02 / DESIGN','title'=>'Deneyimi tasarlayın','text'=>'Modern UI ve güçlü görsel hiyerarşi.'),array('eyebrow'=>'03 / GROWTH','title'=>'Sonuçları büyütün','text'=>'Dönüşüm odaklı optimizasyon.')),'title_field'=>'{{{ title }}}'));
  $this->add_control('autoplay',array('label'=>'Otomatik Oynat','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes'));
  $this->add_control('speed',array('label'=>'Geçiş Süresi','type'=>\Elementor\Controls_Manager::NUMBER,'default'=>4500,'min'=>1500,'max'=>15000,'step'=>500));
  $this->add_control('pause_hover',array('label'=>'Hover’da Duraklat','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes','condition'=>array('autoplay'=>'yes')));
  $this->add_control('touch_swipe',array('label'=>'Dokunmatik Kaydırma','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes'));
  $this->add_control('mouse_drag',array('label'=>'Mouse ile Sürükle','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes'));
  $this->add_control('show_progress',array('label'=>'İlerleme Çizgisi','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes'));
  $this->add_control('style_preset',array('label'=>'Slider Stili','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'split','options'=>array('split'=>'Split','editorial'=>'Editorial','dark'=>'Dark','soft'=>'Soft'),'prefix_class'=>'wpst-content-slider-style-'));
  $this->add_responsive_control('slide_gap',array('label'=>'İçerik / Görsel Aralığı','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>100)),'selectors'=>array('{{WRAPPER}} .wpst-content-slide'=>'gap:{{SIZE}}px;')));
  $this->add_responsive_control('media_height',array('label'=>'Görsel Yüksekliği','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>180,'max'=>700)),'selectors'=>array('{{WRAPPER}} .wpst-content-slide-media'=>'min-height:{{SIZE}}px;height:{{SIZE}}px;')));

  $this->end_controls_section();
  $this->start_controls_section('slider_shell_style',array('label'=>'Slider Biçimi','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('surface',array('label'=>'Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-content-slider'=>'--wpst-cs-surface:{{VALUE}};')));
  $this->add_control('border',array('label'=>'Kenarlık','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-content-slider'=>'--wpst-cs-border:{{VALUE}};')));
  $this->add_responsive_control('radius',array('label'=>'Slider Radius','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>60)),'default'=>array('size'=>28),'selectors'=>array('{{WRAPPER}} .wpst-content-slider'=>'--wpst-cs-radius:{{SIZE}}px;')));
  $this->add_responsive_control('copy_padding',array('label'=>'Metin Alanı İç Boşluk','type'=>\Elementor\Controls_Manager::DIMENSIONS,'size_units'=>array('px'),'selectors'=>array('{{WRAPPER}} .wpst-content-slide-copy'=>'padding:{{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;')));
  $this->add_control('nav_bg',array('label'=>'Navigasyon Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-content-slider'=>'--wpst-cs-nav-bg:{{VALUE}};')));
  $this->add_control('nav_color',array('label'=>'Navigasyon Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-content-slider'=>'--wpst-cs-nav-color:{{VALUE}};')));
  $this->end_controls_section();

  $this->start_controls_section('slider_type_style',array('label'=>'İçerik Biçimi','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('eyebrow_color',array('label'=>'Etiket Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-content-slider'=>'--wpst-cs-eyebrow:{{VALUE}};')));
  $this->add_control('title_color',array('label'=>'Başlık Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-content-slider'=>'--wpst-cs-title:{{VALUE}};')));
  $this->add_control('text_color',array('label'=>'Metin Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-content-slider'=>'--wpst-cs-text:{{VALUE}};')));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'slide_title_typography','label'=>'Başlık Tipografi','selector'=>'{{WRAPPER}} .wpst-content-slide-copy h3'));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'slide_text_typography','label'=>'Metin Tipografi','selector'=>'{{WRAPPER}} .wpst-content-slide-copy p'));
  $this->end_controls_section();
  $this->standard_responsive_controls();
 }
 protected function render(){
  $s=$this->get_settings_for_display();
  echo'<div class="wpst-content-slider" data-autoplay="'.esc_attr($s['autoplay']??'').'" data-speed="'.absint($s['speed']??4500).'" data-pause-hover="'.esc_attr($s['pause_hover']??'yes').'" data-touch-swipe="'.esc_attr($s['touch_swipe']??'yes').'" data-mouse-drag="'.('yes'===($s['mouse_drag']??'yes')?'yes':'no').'">';
  echo'<div class="wpst-content-slider-track">';
  foreach((array)$s['items'] as $i=>$item){
   $link=!empty($item['url'])&&is_array($item['url'])?$item['url']:array();
   $u=!empty($link['url'])?$link['url']:'#';
   $target=!empty($link['is_external'])?' target="_blank"':'';
   $rels=array(); if(!empty($link['nofollow']))$rels[]='nofollow'; if(!empty($link['is_external']))$rels[]='noopener';
   $rel=$rels?' rel="'.esc_attr(implode(' ',$rels)).'"':'';
   $img=!empty($item['image']['url'])?$item['image']['url']:'';
   $icon=!empty($item['button_icon'])?$item['button_icon']:'arrow-up-right';
   echo'<article class="wpst-content-slide'.($i===0?' is-active':'').'" aria-hidden="'.($i===0?'false':'true').'">';
   echo'<div class="wpst-content-slide-copy"><small>'.esc_html($item['eyebrow']).'</small><h3>'.esc_html($item['title']).'</h3><p>'.esc_html($item['text']).'</p>';
   echo'<a href="'.esc_url($u).'"'.$target.$rel.'>'.esc_html($item['button']).'<span class="wpst-native-arrow">'.(class_exists('WPST_Icon_Library')?WPST_Icon_Library::svg($icon,array('size'=>14)):'↗').'</span></a></div>';
   echo'<div class="wpst-content-slide-media">'.($img?'<img src="'.esc_url($img).'" alt="" loading="'.($i===0?'eager':'lazy').'" decoding="async">':'<span>WPSoft</span>').'</div></article>';
  }
  echo'</div><div class="wpst-content-slider-nav"><button type="button" data-slider-prev aria-label="Önceki slayt">'.(class_exists('WPST_Icon_Library')?WPST_Icon_Library::svg('arrow-left',array('size'=>16)):'‹').'</button><div class="wpst-content-slider-dots"></div><button type="button" data-slider-next aria-label="Sonraki slayt">'.(class_exists('WPST_Icon_Library')?WPST_Icon_Library::svg('arrow-right',array('size'=>16)):'›').'</button></div>';
  if('yes'===($s['show_progress']??'yes'))echo'<div class="wpst-carousel-progress wpst-content-slider-progress" aria-hidden="true"><span></span></div>';
  echo'</div>';
 }

}