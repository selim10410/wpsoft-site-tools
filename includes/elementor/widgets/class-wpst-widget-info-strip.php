<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class WPST_Widget_Info_Strip extends WPST_Elementor_Widget_Base {
 public function get_name(){return 'wpsoft-info-strip';}
 public function get_title(){return 'WPSoft Bilgi Şeridi 2.0';}
 public function get_icon(){return 'eicon-info-circle-o';}
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'İçerik'));
  $this->wpst_signature_preset_control();
  $this->add_control('wpst_icon',array('label'=>'WPSoft Icon','type'=>\Elementor\Controls_Manager::SELECT2,'options'=>class_exists('WPST_Icon_Library')?WPST_Icon_Library::options():array(),'default'=>'message','label_block'=>true));
  $this->add_control('icon',array('label'=>'Elementor Icon (Eski)','type'=>\Elementor\Controls_Manager::ICONS,'default'=>array('value'=>'fas fa-bullhorn','library'=>'fa-solid')));
  $this->add_control('title',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Yeni kampanya yayında'));
  $this->add_control('text',array('label'=>'Açıklama','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Detayları hemen inceleyin.'));
  $this->link_controls('button','Buton');
  $this->add_control('layout',array('label'=>'Yerleşim','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'inline','options'=>array('inline'=>'Inline','compact'=>'Compact','center'=>'Centered','pill'=>'Pill'),'prefix_class'=>'wpst-info-strip-layout-'));
  $this->add_control('show_icon',array('label'=>'Icon Göster','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes'));
  $this->end_controls_section();
  $this->start_controls_section('strip_style',array('label'=>'Şerit Biçimi','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('bg',array('label'=>'Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-info-strip'=>'background:{{VALUE}};')));
  $this->add_control('border_color',array('label'=>'Kenarlık','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-info-strip'=>'border-color:{{VALUE}};')));
  $this->add_control('icon_bg',array('label'=>'Icon Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-info-strip-icon'=>'background:{{VALUE}};')));
  $this->add_control('icon_color',array('label'=>'Icon Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-info-strip-icon'=>'color:{{VALUE}};')));
  $this->add_responsive_control('padding',array('label'=>'İç Boşluk','type'=>\Elementor\Controls_Manager::DIMENSIONS,'size_units'=>array('px'),'selectors'=>array('{{WRAPPER}} .wpst-ew-info-strip'=>'padding:{{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;')));
  $this->add_responsive_control('radius',array('label'=>'Köşe','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>999)),'selectors'=>array('{{WRAPPER}} .wpst-ew-info-strip'=>'border-radius:{{SIZE}}px;')));
  $this->end_controls_section();
  $this->standard_responsive_controls();
 }
 protected function render(){ $s=$this->get_settings_for_display(); echo '<div class="wpst-ew-info-strip">'; if('yes'===$s['show_icon']){echo'<div class="wpst-ew-info-strip-icon">'; if(!empty($s['wpst_icon'])&&class_exists('WPST_Icon_Library'))WPST_Icon_Library::render($s['wpst_icon']);else \Elementor\Icons_Manager::render_icon($s['icon'],array('aria-hidden'=>'true')); echo '</div>';} echo'<div class="wpst-ew-info-strip-copy"><strong>'.esc_html($s['title']).'</strong><span>'.esc_html($s['text']).'</span></div><a'.$this->render_link_attrs($s['button_url']).'>'.esc_html($s['button_text']).'</a></div>'; }
}