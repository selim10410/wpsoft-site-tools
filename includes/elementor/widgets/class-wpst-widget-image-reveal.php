<?php
if(!defined('ABSPATH'))exit;
class WPST_Widget_Image_Reveal extends WPST_Elementor_Widget_Base{
 public function get_name(){return'wpsoft-image-reveal';}
 public function get_title(){return'WPSoft · Image Reveal 2.0';}
 public function get_icon(){return'eicon-image-rollover';}
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'Görsel'));
  $this->wpst_signature_preset_control();
  $this->add_control('image',array('label'=>'Görsel','type'=>\Elementor\Controls_Manager::MEDIA));
  $this->add_control('caption',array('label'=>'Alt Yazı','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Selected Project · 2026'));
  $this->add_control('direction',array('label'=>'Reveal Yönü','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'left','options'=>array('left'=>'Soldan','right'=>'Sağdan','up'=>'Aşağıdan','center'=>'Merkezden')));
  $this->add_control('trigger',array('label'=>'Reveal Tetikleme','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'view','options'=>array('view'=>'Görünür Olunca','hover'=>'Hover'),'prefix_class'=>'wpst-image-reveal-trigger-'));
  $this->add_control('show_caption',array('label'=>'Alt Yazıyı Göster','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes'));
  $this->end_controls_section();
  $this->start_controls_section('style',array('label'=>'Reveal Biçimi','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('overlay',array('label'=>'Reveal Rengi','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#2563eb','selectors'=>array('{{WRAPPER}} .wpst-image-reveal'=>'--reveal-color:{{VALUE}};')));
  $this->add_responsive_control('height',array('label'=>'Yükseklik','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>180,'max'=>900)),'selectors'=>array('{{WRAPPER}} .wpst-image-reveal-media'=>'height:{{SIZE}}px;')));
  $this->add_responsive_control('radius',array('label'=>'Köşe','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>60)),'selectors'=>array('{{WRAPPER}} .wpst-image-reveal-media'=>'border-radius:{{SIZE}}px;')));
  $this->add_control('fit',array('label'=>'Görsel Yerleşimi','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'cover','options'=>array('cover'=>'Cover','contain'=>'Contain'),'selectors'=>array('{{WRAPPER}} .wpst-image-reveal-media img'=>'object-fit:{{VALUE}};')));
  $this->add_control('caption_color',array('label'=>'Alt Yazı Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-image-reveal figcaption'=>'color:{{VALUE}};')));
  $this->end_controls_section();
  $this->standard_responsive_controls();
 }
 protected function render(){ $s=$this->get_settings_for_display();$img=!empty($s['image']['url'])?$s['image']['url']:'';echo'<figure class="wpst-image-reveal reveal-'.esc_attr($s['direction']).'"><div class="wpst-image-reveal-media">'.($img?'<img src="'.esc_url($img).'" alt="" loading="lazy">':'<div class="wpst-image-reveal-placeholder">Görsel</div>').'<i></i></div>';if('yes'===$s['show_caption'])echo'<figcaption>'.esc_html($s['caption']).'</figcaption>';echo'</figure>'; }
}