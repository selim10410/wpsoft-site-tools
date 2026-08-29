<?php
if(!defined('ABSPATH'))exit;
class WPST_Widget_SVG_Shape extends WPST_Elementor_Widget_Base{
 public function get_name(){return'wpsoft-svg-shape';}
 public function get_title(){return'WPSoft SVG Shape 2.0';}
 public function get_icon(){return'eicon-shape';}
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'SVG'));
  $this->add_control('shape',array('label'=>'WPSoft SVG','type'=>\Elementor\Controls_Manager::SELECT2,'options'=>class_exists('WPST_SVG_Library')?WPST_SVG_Library::options():array(),'default'=>'blob-soft','label_block'=>true));
  $this->add_control('position_mode',array('label'=>'Konumlandırma','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'inline','options'=>array('inline'=>'Normal Akış','absolute'=>'Absolute Dekor'),'prefix_class'=>'wpst-svg-position-'));
  $this->add_control('animate',array('label'=>'Yumuşak Hareket','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'','prefix_class'=>'wpst-svg-animate-'));
  $this->end_controls_section();
  $this->start_controls_section('style',array('label'=>'Stil','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('color',array('label'=>'Renk','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#6366f1','selectors'=>array('{{WRAPPER}} .wpst-svg-shape'=>'color:{{VALUE}};')));
  $this->add_responsive_control('width',array('label'=>'Genişlik','type'=>\Elementor\Controls_Manager::SLIDER,'size_units'=>array('px','%','vw'),'range'=>array('px'=>array('min'=>40,'max'=>1000),'%'=>array('min'=>5,'max'=>100),'vw'=>array('min'=>5,'max'=>100)),'default'=>array('unit'=>'px','size'=>260),'selectors'=>array('{{WRAPPER}} .wpst-svg-shape svg'=>'width:{{SIZE}}{{UNIT}};')));
  $this->add_responsive_control('offset_x',array('label'=>'X Offset','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>-500,'max'=>500)),'selectors'=>array('{{WRAPPER}} .wpst-svg-shape'=>'--wpst-svg-x:{{SIZE}}px;')));
  $this->add_responsive_control('offset_y',array('label'=>'Y Offset','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>-500,'max'=>500)),'selectors'=>array('{{WRAPPER}} .wpst-svg-shape'=>'--wpst-svg-y:{{SIZE}}px;')));
  $this->add_control('opacity',array('label'=>'Opaklık','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array(''=>array('min'=>0,'max'=>1,'step'=>.05)),'default'=>array('size'=>1),'selectors'=>array('{{WRAPPER}} .wpst-svg-shape'=>'opacity:{{SIZE}};')));
  $this->add_control('rotate',array('label'=>'Döndür','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>-180,'max'=>180)),'default'=>array('size'=>0),'selectors'=>array('{{WRAPPER}} .wpst-svg-shape svg'=>'transform:rotate({{SIZE}}deg);')));
  $this->end_controls_section();
  $this->standard_responsive_controls();
 }
 protected function render(){
  $s=$this->get_settings_for_display();echo'<div class="wpst-svg-shape">';
  if(class_exists('WPST_SVG_Library'))echo WPST_SVG_Library::inline($s['shape'],array('class'=>'wpst-svg-shape-art'));
  echo'</div>';
 }
}