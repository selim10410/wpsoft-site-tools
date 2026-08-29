<?php
if(!defined('ABSPATH'))exit;
class WPST_Widget_Image_Cascade extends WPST_Elementor_Widget_Base{
 public function get_name(){return'wpsoft-image-cascade';} public function get_title(){return'WPSoft Image Cascade 2.0';} public function get_icon(){return'eicon-gallery-justified';}
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'Görseller'));
  $this->wpst_signature_preset_control();
  foreach(array('image_one'=>'Görsel 1','image_two'=>'Görsel 2','image_three'=>'Görsel 3') as $k=>$l)$this->add_control($k,array('label'=>$l,'type'=>\Elementor\Controls_Manager::MEDIA));
  $this->add_control('title',array('label'=>'Alt Başlık','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Görsellerle güçlü hikâye anlatımı'));
  $this->add_control('layout',array('label'=>'Kompozisyon','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'cascade','options'=>array('cascade'=>'Cascade','editorial'=>'Editorial','stack'=>'Soft Stack','cards'=>'Floating Cards'),'prefix_class'=>'wpst-cascade-layout-'));
  $this->add_control('show_caption',array('label'=>'Alt Başlığı Göster','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes'));
  $this->end_controls_section();
  $this->start_controls_section('cascade_style',array('label'=>'Cascade Biçimi','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_responsive_control('media_height',array('label'=>'Kompozisyon Yüksekliği','type'=>\Elementor\Controls_Manager::SLIDER,'size_units'=>array('px','vh'),'range'=>array('px'=>array('min'=>220,'max'=>900),'vh'=>array('min'=>30,'max'=>90)),'default'=>array('size'=>560,'unit'=>'px'),'selectors'=>array('{{WRAPPER}} .wpst-ew-image-cascade'=>'--wpst-cascade-height:{{SIZE}}{{UNIT}};')));
  $this->add_responsive_control('image_gap',array('label'=>'Görsel Aralığı','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>80)),'default'=>array('size'=>18),'selectors'=>array('{{WRAPPER}} .wpst-ew-image-cascade'=>'--wpst-cascade-gap:{{SIZE}}px;')));
  $this->add_responsive_control('image_radius',array('label'=>'Görsel Köşe','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>80)),'default'=>array('size'=>22),'selectors'=>array('{{WRAPPER}} .wpst-ew-image-cascade img'=>'border-radius:{{SIZE}}px;')));
  $this->add_control('image_shadow',array('label'=>'Görsel Gölgesi','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'soft','options'=>array('none'=>'Yok','soft'=>'Soft','medium'=>'Medium','strong'=>'Strong'),'prefix_class'=>'wpst-cascade-shadow-'));
  $this->add_control('caption_color',array('label'=>'Alt Başlık Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-image-cascade figcaption'=>'color:{{VALUE}};')));
  $this->end_controls_section();
  $this->standard_responsive_controls();
 }
 protected function render(){ $s=$this->get_settings_for_display(); echo'<figure class="wpst-ew-image-cascade">'; foreach(array('image_one','image_two','image_three') as $i=>$k)if(!empty($s[$k]['url']))echo'<img class="img-'.($i+1).'" src="'.esc_url($s[$k]['url']).'" alt="" loading="lazy">'; if('yes'===$s['show_caption']&&trim((string)$s['title'])!=='')echo'<figcaption>'.esc_html($s['title']).'</figcaption>';echo'</figure>'; }
}