<?php
if(!defined('ABSPATH'))exit;
class WPST_Widget_Mega_Banner extends WPST_Elementor_Widget_Base{
 public function get_name(){return'wpsoft-mega-banner';}
 public function get_title(){return'WPSoft Mega · Media Banner 2.0';}
 public function get_icon(){return'eicon-video-playlist';}
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'Banner'));
  $this->wpst_signature_preset_control();
  $this->add_control('eyebrow',array('label'=>'Etiket','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'FEATURED'));
  $this->add_control('title',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Yeni nesil dijital deneyim'));
  $this->add_control('text',array('label'=>'Açıklama','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'Öne çıkan içerik veya kampanya için görsel banner.'));
  $this->add_control('image',array('label'=>'Görsel','type'=>\Elementor\Controls_Manager::MEDIA));
  $this->add_control('video_url',array('label'=>'Video URL','type'=>\Elementor\Controls_Manager::URL,'placeholder'=>'https://...'));
  $this->link_controls('button','Buton');
  $this->add_control('layout',array('label'=>'Yerleşim','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'overlay','options'=>array('overlay'=>'Overlay','split'=>'Split','card'=>'Card','minimal'=>'Minimal'),'prefix_class'=>'wpst-mega-banner-layout-'));
  $this->add_control('media_ratio',array('label'=>'Medya Oranı','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'16-9','options'=>array('16-9'=>'16:9','4-3'=>'4:3','3-2'=>'3:2','1-1'=>'1:1'),'prefix_class'=>'wpst-mega-banner-ratio-'));
  $this->end_controls_section();
  $this->start_controls_section('style',array('label'=>'Banner Biçimi','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('overlay',array('label'=>'Overlay','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'rgba(15,23,42,.48)','selectors'=>array('{{WRAPPER}} .wpst-mb-shade'=>'background:{{VALUE}};')));
  $this->add_control('accent',array('label'=>'Vurgu','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-mb-copy small,{{WRAPPER}} .wpst-mb-play'=>'color:{{VALUE}};')));
  $this->add_responsive_control('min_height',array('label'=>'Minimum Yükseklik','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>220,'max'=>800)),'selectors'=>array('{{WRAPPER}} .wpst-ew-mega-banner'=>'min-height:{{SIZE}}px;')));
  $this->add_responsive_control('radius',array('label'=>'Köşe','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>60)),'selectors'=>array('{{WRAPPER}} .wpst-ew-mega-banner'=>'border-radius:{{SIZE}}px;')));
  $this->add_control('image_position',array('label'=>'Görsel Odak','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'center center','options'=>array('center center'=>'Orta','center top'=>'Üst','center bottom'=>'Alt','left center'=>'Sol','right center'=>'Sağ'),'selectors'=>array('{{WRAPPER}} .wpst-ew-mega-banner>img'=>'object-position:{{VALUE}};')));
  $this->end_controls_section();
  $this->standard_responsive_controls();
 }
 protected function render(){
  $s=$this->get_settings_for_display();$video=!empty($s['video_url']['url'])?$s['video_url']['url']:'';
  echo'<aside class="wpst-ew-mega-banner">';
  if(!empty($s['image']['url']))echo'<img src="'.esc_url($s['image']['url']).'" alt="" loading="lazy">';
  echo'<div class="wpst-mb-shade"></div>';
  if($video)echo'<a class="wpst-mb-play" href="'.esc_url($video).'" target="_blank" rel="noopener">'.(class_exists('WPST_Icon_Library')?WPST_Icon_Library::svg('play',array('size'=>20)):'▶').'</a>';
  echo'<div class="wpst-mb-copy"><small>'.esc_html($s['eyebrow']).'</small><h3>'.esc_html($s['title']).'</h3><p>'.esc_html($s['text']).'</p>';
  if($s['button_text'])echo'<a class="wpst-mb-cta"'.$this->render_link_attrs($s['button_url']).'>'.esc_html($s['button_text']).'<span class="wpst-native-arrow">'.(class_exists('WPST_Icon_Library')?WPST_Icon_Library::svg('arrow-right',array('size'=>15)):'→').'</span></a>';
  echo'</div></aside>';
 }
}