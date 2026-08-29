<?php
if(!defined('ABSPATH'))exit;
class WPST_Widget_Stats_Grid extends WPST_Elementor_Widget_Base{
 public function get_name(){return'wpsoft-stats-grid';}
 public function get_title(){return'WPSoft · Stats Grid 2.0';}
 public function get_icon(){return'eicon-counter';}
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'İstatistikler'));
  $this->wpst_signature_preset_control();
  $r=new \Elementor\Repeater();
  $r->add_control('wpst_icon',array('label'=>'WPSoft Icon','type'=>\Elementor\Controls_Manager::SELECT2,'options'=>class_exists('WPST_Icon_Library')?WPST_Icon_Library::options():array(),'default'=>'chart','label_block'=>true));
  $r->add_control('number',array('label'=>'Değer','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'250+'));
  $r->add_control('label',array('label'=>'Açıklama','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Tamamlanan Proje'));
  $this->add_control('items',array('label'=>'İstatistikler','type'=>\Elementor\Controls_Manager::REPEATER,'fields'=>$r->get_controls(),'default'=>array(
   array('wpst_icon'=>'briefcase','number'=>'250+','label'=>'Tamamlanan Proje'),
   array('wpst_icon'=>'heart','number'=>'98%','label'=>'Müşteri Memnuniyeti'),
   array('wpst_icon'=>'award','number'=>'10+','label'=>'Yıl Deneyim'),
   array('wpst_icon'=>'headphones','number'=>'7/24','label'=>'Destek')
  ),'title_field'=>'{{{ number }}}'));
  $this->add_control('style_preset',array('label'=>'Stil','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'clean','options'=>array('clean'=>'Clean','cards'=>'Cards','soft'=>'Soft','dark'=>'Dark'),'prefix_class'=>'wpst-stats-style-'));
  $this->add_control('layout_variant',array(
   'label'=>'Metrik Yerleşimi','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'grid',
   'options'=>array('grid'=>'Grid','strip'=>'Strip','editorial'=>'Editorial','compact'=>'Compact','hero'=>'Hero Metrics'),
   'prefix_class'=>'wpst-stats-layout-'
  ));
  $this->add_responsive_control('columns',array('label'=>'Kolon','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'4','tablet_default'=>'2','mobile_default'=>'1','options'=>array('1'=>'1','2'=>'2','3'=>'3','4'=>'4'),'selectors'=>array('{{WRAPPER}} .wpst-ew-stats-grid'=>'grid-template-columns:repeat({{VALUE}},minmax(0,1fr))!important;')));
  $this->end_controls_section();

  $this->start_controls_section('style',array('label'=>'Stil','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('accent',array('label'=>'Vurgu','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#2563eb','selectors'=>array('{{WRAPPER}} .wpst-ew-stats-grid'=>'--stats-accent:{{VALUE}};')));
  $this->add_control('number_color',array('label'=>'Sayı','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-stats-grid'=>'--stats-number:{{VALUE}};')));
  $this->add_control('label_color',array('label'=>'Açıklama','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-stats-grid'=>'--stats-text:{{VALUE}};')));
  $this->add_responsive_control('radius',array('label'=>'Kart Köşe','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>50)),'default'=>array('size'=>20),'selectors'=>array('{{WRAPPER}} .wpst-ew-stats-grid article'=>'border-radius:{{SIZE}}px;')));
  $this->end_controls_section();
  $this->standard_responsive_controls();
 }
 protected function render(){
  $s=$this->get_settings_for_display();
  echo'<div class="wpst-ew-stats-grid">';
  foreach((array)$s['items'] as $i)echo'<article><i>'.(class_exists('WPST_Icon_Library')?WPST_Icon_Library::svg($i['wpst_icon'],array('size'=>18)):'').'</i><strong>'.esc_html($i['number']).'</strong><span>'.esc_html($i['label']).'</span></article>';
  echo'</div>';
 }
}
