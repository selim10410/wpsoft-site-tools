<?php
if(!defined('ABSPATH'))exit;
class WPST_Widget_Icon_Box extends WPST_Elementor_Widget_Base{
 public function get_name(){return'wpsoft-icon-box';}
 public function get_title(){return'WPSoft · Icon Box 2.0';}
 public function get_icon(){return'eicon-icon-box';}
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'İçerik'));
  $this->add_control('wpst_icon',array('label'=>'WPSoft Icon','type'=>\Elementor\Controls_Manager::SELECT2,'options'=>class_exists('WPST_Icon_Library')?WPST_Icon_Library::options():array(),'default'=>'bolt','label_block'=>true));
  $this->add_control('icon',array('label'=>'Elementor Icon (Eski İçerik)','type'=>\Elementor\Controls_Manager::ICONS,'default'=>array('value'=>'fas fa-bolt','library'=>'fa-solid')));
  $this->add_control('title',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Hızlı Altyapı'));
  $this->add_control('description',array('label'=>'Açıklama','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'Performans odaklı ve modern yapı.'));
  $this->add_control('layout',array('label'=>'Yerleşim','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'horizontal','options'=>array('horizontal'=>'Yatay','vertical'=>'Dikey'),'prefix_class'=>'wpst-iconbox-layout-'));
  $this->add_control('card_style',array('label'=>'Kart Stili','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'clean','options'=>array('clean'=>'Clean','card'=>'Card','soft'=>'Soft','glass'=>'Glass'),'prefix_class'=>'wpst-iconbox-style-'));
  $this->end_controls_section();

  $this->start_controls_section('style_card',array('label'=>'Kart','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('card_bg',array('label'=>'Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-iconbox'=>'--ib-bg:{{VALUE}};')));
  $this->add_control('card_border',array('label'=>'Border','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-iconbox'=>'--ib-border:{{VALUE}};')));
  $this->add_responsive_control('card_padding',array('label'=>'İç Boşluk','type'=>\Elementor\Controls_Manager::DIMENSIONS,'size_units'=>array('px'),'selectors'=>array('{{WRAPPER}} .wpst-ew-iconbox'=>'padding:{{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;')));
  $this->add_responsive_control('card_radius',array('label'=>'Köşe','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>60)),'default'=>array('size'=>20),'selectors'=>array('{{WRAPPER}} .wpst-ew-iconbox'=>'border-radius:{{SIZE}}px;')));
  $this->add_control('hover_lift',array('label'=>'Hover Lift','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes','prefix_class'=>'wpst-iconbox-hover-'));
  $this->end_controls_section();

  $this->start_controls_section('style_icon',array('label'=>'Icon','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('icon_color',array('label'=>'Icon Rengi','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#2563eb','selectors'=>array('{{WRAPPER}} .wpst-ew-icon'=>'color:{{VALUE}};')));
  $this->add_control('icon_bg',array('label'=>'Icon Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#eff6ff','selectors'=>array('{{WRAPPER}} .wpst-ew-icon'=>'background:{{VALUE}};')));
  $this->add_responsive_control('icon_box_size',array('label'=>'Kutu Boyutu','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>30,'max'=>120)),'default'=>array('size'=>52),'selectors'=>array('{{WRAPPER}} .wpst-ew-icon'=>'width:{{SIZE}}px;height:{{SIZE}}px;flex-basis:{{SIZE}}px;')));
  $this->add_responsive_control('icon_size',array('label'=>'Icon Boyutu','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>12,'max'=>64)),'default'=>array('size'=>22),'selectors'=>array('{{WRAPPER}} .wpst-ew-icon svg'=>'width:{{SIZE}}px;height:{{SIZE}}px;')));
  $this->add_responsive_control('icon_radius',array('label'=>'Icon Köşe','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>60)),'default'=>array('size'=>15),'selectors'=>array('{{WRAPPER}} .wpst-ew-icon'=>'border-radius:{{SIZE}}px;')));
  $this->end_controls_section();

  $this->start_controls_section('style_text',array('label'=>'Metin','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('title_color',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#0f172a','selectors'=>array('{{WRAPPER}} .wpst-ew-iconbox h3'=>'color:{{VALUE}};')));
  $this->add_control('text_color',array('label'=>'Açıklama','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#64748b','selectors'=>array('{{WRAPPER}} .wpst-ew-iconbox p'=>'color:{{VALUE}};')));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'title_typography','selector'=>'{{WRAPPER}} .wpst-ew-iconbox h3'));
  $this->end_controls_section();
  $this->standard_responsive_controls();
 }
 protected function render(){
  $s=$this->get_settings_for_display();
  echo'<div class="wpst-ew-iconbox"><div class="wpst-ew-icon">';
  if(!empty($s['wpst_icon'])&&class_exists('WPST_Icon_Library'))WPST_Icon_Library::render($s['wpst_icon']);else \Elementor\Icons_Manager::render_icon($s['icon'],array('aria-hidden'=>'true'));
  echo'</div><div class="wpst-iconbox-copy"><h3>'.esc_html($s['title']).'</h3><p>'.esc_html($s['description']).'</p></div></div>';
 }
}
