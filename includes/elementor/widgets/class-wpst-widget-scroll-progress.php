<?php
if(!defined('ABSPATH'))exit;
class WPST_Widget_Scroll_Progress extends WPST_Elementor_Widget_Base{
 public function get_name(){return'wpsoft-scroll-progress';}
 public function get_title(){return'WPSoft · Scroll Progress 3.0';}
 public function get_icon(){return'eicon-progress-tracker';}
 public function get_keywords(){return array('scroll','progress','reading','indicator','bar','wpsoft');}
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'Ayarlar'));
  $this->add_control('position',array('label'=>'Konum','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'top','options'=>array('top'=>'Üst','bottom'=>'Alt'),'prefix_class'=>'wpst-scroll-progress-pos-'));
  $this->add_control('style_variant',array('label'=>'Görünüm','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'line','options'=>array('line'=>'Line','pill'=>'Pill','glass'=>'Glass','gradient'=>'Gradient'),'prefix_class'=>'wpst-scroll-progress-style-'));
  $this->add_control('hide_mobile',array('label'=>'Mobilde Gizle','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'','prefix_class'=>'wpst-scroll-progress-hide-mobile-'));
  $this->add_control('show_percentage',array('label'=>'Yüzde Göstergesi','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'','prefix_class'=>'wpst-scroll-progress-percent-'));
  $this->end_controls_section();

  $this->start_controls_section('style',array('label'=>'Biçim','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('color',array('label'=>'Dolgu Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-scroll-progress'=>'--wpst-progress-color:{{VALUE}};')));
  $this->add_control('color_2',array('label'=>'Gradient İkinci Renk','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-scroll-progress'=>'--wpst-progress-color-2:{{VALUE}};'),'condition'=>array('style_variant'=>'gradient')));
  $this->add_control('track',array('label'=>'Track Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-scroll-progress'=>'--wpst-progress-track:{{VALUE}};')));
  $this->add_responsive_control('height',array('label'=>'Yükseklik','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>2,'max'=>16)),'default'=>array('size'=>4),'selectors'=>array('{{WRAPPER}} .wpst-ew-scroll-progress'=>'height:{{SIZE}}px;')));
  $this->add_responsive_control('inset',array('label'=>'Kenar Boşluğu','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>40)),'default'=>array('size'=>0),'selectors'=>array('{{WRAPPER}} .wpst-ew-scroll-progress'=>'--wpst-progress-inset:{{SIZE}}px;')));
  $this->add_control('zindex',array('label'=>'Z-Index','type'=>\Elementor\Controls_Manager::NUMBER,'default'=>9999,'min'=>1,'max'=>99999,'selectors'=>array('{{WRAPPER}} .wpst-ew-scroll-progress'=>'z-index:{{VALUE}};')));
  $this->end_controls_section();
  $this->standard_responsive_controls();
 }
 protected function render(){
  echo'<div class="wpst-ew-scroll-progress" role="progressbar" aria-label="Sayfa kaydırma ilerlemesi" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><span></span><b aria-hidden="true">0%</b></div>';
 }
}
