<?php
if(!defined('ABSPATH'))exit;
class WPST_Widget_Before_After extends WPST_Elementor_Widget_Base{
 public function get_name(){return'wpsoft-before-after';}
 public function get_title(){return'WPSoft · Before / After Pro';}
 public function get_icon(){return'eicon-image-before-after';}
 public function get_categories(){return array('wpsoft-media','wpsoft');}
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'Görseller'));
  $this->add_control('before',array('label'=>'Öncesi','type'=>\Elementor\Controls_Manager::MEDIA));
  $this->add_control('after',array('label'=>'Sonrası','type'=>\Elementor\Controls_Manager::MEDIA));
  $this->add_control('before_label',array('label'=>'Öncesi Etiketi','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Öncesi'));
  $this->add_control('after_label',array('label'=>'Sonrası Etiketi','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Sonrası'));
  $this->add_control('start_position',array('label'=>'Başlangıç Pozisyonu %','type'=>\Elementor\Controls_Manager::NUMBER,'default'=>50,'min'=>10,'max'=>90));
  $this->add_control('show_labels',array('label'=>'Etiketleri Göster','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes'));
  $this->add_control('handle_style',array('label'=>'Kontrol Stili','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'circle','options'=>array('circle'=>'Yuvarlak','pill'=>'Pill','minimal'=>'Minimal')));
  $this->end_controls_section();

  $this->start_controls_section('style',array('label'=>'Görünüm','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_responsive_control('height',array('label'=>'Yükseklik','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>220,'max'=>900)),'default'=>array('size'=>480,'unit'=>'px'),'selectors'=>array('{{WRAPPER}} .wpst-ew-before-after'=>'height:{{SIZE}}{{UNIT}}')));
  $this->add_control('radius',array('label'=>'Köşe','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>50)),'default'=>array('size'=>20),'selectors'=>array('{{WRAPPER}} .wpst-ew-before-after'=>'border-radius:{{SIZE}}px')));
  $this->add_control('handle_color',array('label'=>'Kontrol Rengi','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#ffffff','selectors'=>array('{{WRAPPER}} .wpst-ew-before-after'=>'--wpst-ba-handle:{{VALUE}}')));
  $this->add_control('line_color',array('label'=>'Ayırıcı Rengi','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'rgba(255,255,255,.9)','selectors'=>array('{{WRAPPER}} .wpst-ew-before-after'=>'--wpst-ba-line:{{VALUE}}')));
  $this->end_controls_section();
  $this->standard_responsive_controls();
 }
 protected function render(){
  $s=$this->get_settings_for_display();
  $b=!empty($s['before']['url'])?$s['before']['url']:'';
  $a=!empty($s['after']['url'])?$s['after']['url']:'';
  $pos=max(10,min(90,(int)$s['start_position']));
  $style=in_array($s['handle_style'],array('circle','pill','minimal'),true)?$s['handle_style']:'circle';
  echo'<div class="wpst-ew-before-after handle-'.esc_attr($style).'" data-wpst-before-after style="--wpst-ba-pos:'.$pos.'%">';
  echo'<div class="wpst-ew-ba-before">'.($b?'<img src="'.esc_url($b).'" alt="">':'<div class="wpst-ew-ba-placeholder">'.esc_html($s['before_label']).'</div>').'</div>';
  echo'<div class="wpst-ew-ba-after">'.($a?'<img src="'.esc_url($a).'" alt="">':'<div class="wpst-ew-ba-placeholder">'.esc_html($s['after_label']).'</div>').'</div>';
  if('yes'===$s['show_labels']){
   echo'<span class="wpst-ba-label is-before">'.esc_html($s['before_label']).'</span><span class="wpst-ba-label is-after">'.esc_html($s['after_label']).'</span>';
  }
  echo'<span class="wpst-ba-divider" aria-hidden="true"><i>↔</i></span>';
  echo'<input type="range" min="10" max="90" value="'.$pos.'" aria-label="'.esc_attr($s['before_label'].' / '.$s['after_label']).'"></div>';
 }
}
