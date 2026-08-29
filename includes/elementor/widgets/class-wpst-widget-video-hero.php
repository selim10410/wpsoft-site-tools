<?php
if(!defined('ABSPATH'))exit;
class WPST_Widget_Video_Hero extends WPST_Elementor_Widget_Base{
 public function get_name(){return'wpsoft-video-hero';}
 public function get_title(){return'WPSoft · Video Hero Pro';}
 public function get_icon(){return'eicon-video-camera';}
 public function get_categories(){return array('wpsoft-media','wpsoft-creative','wpsoft');}
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'İçerik'));
  $this->add_control('eyebrow',array('label'=>'Üst Etiket','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Yeni Nesil Deneyim'));
  $this->add_control('title',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'Markanızı hareketli bir deneyimle öne çıkarın'));
  $this->add_control('description',array('label'=>'Açıklama','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'Video arka planlı modern hero alanı.'));
  $this->add_control('video',array('label'=>'MP4 / WebM Video URL','type'=>\Elementor\Controls_Manager::URL));
  $this->add_control('poster',array('label'=>'Poster Görsel','type'=>\Elementor\Controls_Manager::MEDIA));
  $this->add_control('autoplay',array('label'=>'Otomatik Oynat','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes'));
  $this->add_control('loop',array('label'=>'Döngü','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes'));
  $this->add_control('show_controls',array('label'=>'Video Kontrolleri','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>''));
  $this->add_control('mobile_video',array('label'=>'Mobilde Video Oynat','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>''));
  $this->link_controls('button','Buton');
  $this->end_controls_section();

  $this->start_controls_section('style',array('label'=>'Hero Stili','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_responsive_control('height',array('label'=>'Minimum Yükseklik','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>320,'max'=>1000)),'default'=>array('size'=>680,'unit'=>'px'),'selectors'=>array('{{WRAPPER}} .wpst-ew-video-hero'=>'min-height:{{SIZE}}{{UNIT}}')));
  $this->add_control('overlay_color',array('label'=>'Overlay Rengi','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'rgba(2,6,23,.52)','selectors'=>array('{{WRAPPER}} .wpst-ew-video-overlay'=>'background:{{VALUE}}')));
  $this->add_control('content_width',array('label'=>'İçerik Genişliği','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>320,'max'=>900)),'default'=>array('size'=>720,'unit'=>'px'),'selectors'=>array('{{WRAPPER}} .wpst-ew-video-content'=>'max-width:{{SIZE}}{{UNIT}}')));
  $this->add_control('content_align',array('label'=>'İçerik Hizası','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'left','options'=>array('left'=>'Sol','center'=>'Orta'),'prefix_class'=>'wpst-video-align-'));
  $this->end_controls_section();
  $this->standard_responsive_controls();
 }
 protected function render(){
  $s=$this->get_settings_for_display();
  $video=!empty($s['video']['url'])?$s['video']['url']:'';
  $poster=!empty($s['poster']['url'])?$s['poster']['url']:'';
  echo'<section class="wpst-ew-video-hero" data-mobile-video="'.('yes'===$s['mobile_video']?'1':'0').'">';
  if($video){
   echo'<video '.('yes'===$s['autoplay']?'autoplay ':'').'muted '.('yes'===$s['loop']?'loop ':'').'playsinline '.('yes'===$s['show_controls']?'controls ':'').($poster?'poster="'.esc_url($poster).'" ':'').'><source src="'.esc_url($video).'"></video>';
  }elseif($poster) echo'<img class="wpst-ew-video-poster" src="'.esc_url($poster).'" alt="">';
  echo'<div class="wpst-ew-video-overlay"></div><div class="wpst-ew-video-content">';
  if($s['eyebrow'])echo'<small>'.esc_html($s['eyebrow']).'</small>';
  if($s['title'])echo'<h2>'.wp_kses_post($s['title']).'</h2>';
  if($s['description'])echo'<p>'.esc_html($s['description']).'</p>';
  if(!empty($s['button_text']))echo'<a'.$this->render_link_attrs($s['button_url']).'>'.esc_html($s['button_text']).'</a>';
  echo'</div></section>';
 }
}
