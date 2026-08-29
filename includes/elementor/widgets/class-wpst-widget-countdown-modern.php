<?php
if(!defined('ABSPATH'))exit;
class WPST_Widget_Countdown_Modern extends WPST_Elementor_Widget_Base{
 public function get_name(){return'wpsoft-countdown-modern';}
 public function get_title(){return'WPSoft · Countdown Modern 2.0';}
 public function get_icon(){return'eicon-countdown';}
 public function get_keywords(){return array('countdown','timer','launch','event','coming soon','wpsoft');}
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'Sayaç'));
  $this->wpst_signature_preset_control();
  $this->add_control('eyebrow',array('label'=>'Etiket','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'YAKINDA','dynamic'=>array('active'=>true)));
  $this->add_control('title',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Yeni deneyim için geri sayım','dynamic'=>array('active'=>true)));
  $this->add_control('date',array('label'=>'Bitiş Tarihi','type'=>\Elementor\Controls_Manager::DATE_TIME,'default'=>gmdate('Y-m-d H:i',strtotime('+30 days'))));
  $this->add_control('day_label',array('label'=>'Gün Yazısı','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Gün'));
  $this->add_control('hour_label',array('label'=>'Saat Yazısı','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Saat'));
  $this->add_control('minute_label',array('label'=>'Dakika Yazısı','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Dakika'));
  $this->add_control('second_label',array('label'=>'Saniye Yazısı','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Saniye'));
  $this->add_control('layout',array('label'=>'Görünüm','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'cards','options'=>array('cards'=>'Modern Cards','minimal'=>'Minimal','glass'=>'Glass','dark'=>'Dark'),'prefix_class'=>'wpst-countdown-layout-'));
  $this->add_responsive_control('columns',array('label'=>'Sayaç Kolonu','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'4','tablet_default'=>'4','mobile_default'=>'2','options'=>array('1'=>'1','2'=>'2','4'=>'4'),'selectors'=>array('{{WRAPPER}} .wpst-countdown-cells'=>'grid-template-columns:repeat({{VALUE}},minmax(0,1fr))!important;')));
  $this->add_responsive_control('gap',array('label'=>'Sayaç Aralığı','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>50)),'default'=>array('size'=>12),'selectors'=>array('{{WRAPPER}} .wpst-countdown-cells'=>'--wpst-countdown-gap:{{SIZE}}px;')));
  $this->end_controls_section();

  $this->start_controls_section('style_shell',array('label'=>'Sayaç Biçimi','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('surface',array('label'=>'Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-countdown-modern'=>'--wpst-countdown-surface:{{VALUE}};')));
  $this->add_control('border',array('label'=>'Kenarlık','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-countdown-modern'=>'--wpst-countdown-border:{{VALUE}};')));
  $this->add_responsive_control('radius',array('label'=>'Köşe','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>60)),'default'=>array('size'=>26),'selectors'=>array('{{WRAPPER}} .wpst-ew-countdown-modern'=>'--wpst-countdown-radius:{{SIZE}}px;')));
  $this->add_responsive_control('padding',array('label'=>'İç Boşluk','type'=>\Elementor\Controls_Manager::DIMENSIONS,'size_units'=>array('px'),'selectors'=>array('{{WRAPPER}} .wpst-ew-countdown-modern'=>'padding:{{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;')));
  $this->add_control('cell_surface',array('label'=>'Hücre Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-countdown-modern'=>'--wpst-countdown-cell:{{VALUE}};')));
  $this->add_control('cell_border',array('label'=>'Hücre Kenarlık','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-countdown-modern'=>'--wpst-countdown-cell-border:{{VALUE}};')));
  $this->add_responsive_control('cell_radius',array('label'=>'Hücre Köşe','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>40)),'default'=>array('size'=>18),'selectors'=>array('{{WRAPPER}} .wpst-ew-countdown-modern'=>'--wpst-countdown-cell-radius:{{SIZE}}px;')));
  $this->end_controls_section();

  $this->start_controls_section('style_type',array('label'=>'Metin & Sayılar','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('eyebrow_color',array('label'=>'Etiket Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-countdown-modern'=>'--wpst-countdown-eyebrow:{{VALUE}};')));
  $this->add_control('title_color',array('label'=>'Başlık Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-countdown-modern'=>'--wpst-countdown-title:{{VALUE}};')));
  $this->add_control('number_color',array('label'=>'Sayı Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-countdown-modern'=>'--wpst-countdown-number:{{VALUE}};')));
  $this->add_control('label_color',array('label'=>'Birim Yazı Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-countdown-modern'=>'--wpst-countdown-label:{{VALUE}};')));
  $this->add_responsive_control('number_size',array('label'=>'Sayı Boyutu','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>20,'max'=>100)),'default'=>array('size'=>48),'tablet_default'=>array('size'=>42),'mobile_default'=>array('size'=>36),'selectors'=>array('{{WRAPPER}} .wpst-countdown-cells strong'=>'font-size:{{SIZE}}px;')));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'title_typography','label'=>'Başlık Tipografi','selector'=>'{{WRAPPER}} .wpst-ew-countdown-modern h3'));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'label_typography','label'=>'Birim Tipografi','selector'=>'{{WRAPPER}} .wpst-countdown-cells span'));
  $this->end_controls_section();
  $this->standard_responsive_controls();
 }
 protected function render(){
  $s=$this->get_settings_for_display();
  echo'<div class="wpst-ew-countdown-modern" data-date="'.esc_attr($s['date']).'"><small>'.esc_html($s['eyebrow']).'</small><h3>'.esc_html($s['title']).'</h3><div class="wpst-countdown-cells"><b><strong data-d>00</strong><span>'.esc_html($s['day_label']).'</span></b><b><strong data-h>00</strong><span>'.esc_html($s['hour_label']).'</span></b><b><strong data-m>00</strong><span>'.esc_html($s['minute_label']).'</span></b><b><strong data-s>00</strong><span>'.esc_html($s['second_label']).'</span></b></div></div>';
 }
}
