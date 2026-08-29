<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class WPST_Widget_Breadcrumb extends WPST_Elementor_Widget_Base {
 public function get_name(){return 'wpsoft-breadcrumb';} public function get_title(){return 'WPSoft Breadcrumb / İç Başlık 2.0';} public function get_icon(){return 'eicon-navigation-horizontal';}
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'İçerik'));
  $this->wpst_signature_preset_control();
  $this->add_control('title',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Hizmetlerimiz'));
  $this->add_control('home',array('label'=>'Ana Sayfa Metni','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Ana Sayfa'));
  $this->add_control('show_current',array('label'=>'Mevcut Sayfayı Göster','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes'));
  $this->add_control('separator_icon',array('label'=>'Ayırıcı Icon','type'=>\Elementor\Controls_Manager::SELECT2,'options'=>class_exists('WPST_Icon_Library')?WPST_Icon_Library::options():array(),'default'=>'chevron-right','label_block'=>true));
  $this->add_control('layout',array('label'=>'Yerleşim','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'left','options'=>array('left'=>'Sol','center'=>'Orta','compact'=>'Compact','hero'=>'Hero'),'prefix_class'=>'wpst-breadcrumb-layout-'));
  $this->end_controls_section();
  $this->start_controls_section('style',array('label'=>'Breadcrumb Biçimi','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('bg',array('label'=>'Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#f8fafc','selectors'=>array('{{WRAPPER}} .wpst-ew-breadcrumb'=>'background:{{VALUE}}')));
  $this->add_control('link_color',array('label'=>'Link Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-breadcrumb nav a'=>'color:{{VALUE}}')));
  $this->add_control('current_color',array('label'=>'Mevcut Sayfa Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-breadcrumb nav strong'=>'color:{{VALUE}}')));
  $this->add_control('separator_color',array('label'=>'Ayırıcı Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-breadcrumb-sep'=>'color:{{VALUE}}')));
  $this->add_responsive_control('padding',array('label'=>'İç Boşluk','type'=>\Elementor\Controls_Manager::DIMENSIONS,'size_units'=>array('px'),'selectors'=>array('{{WRAPPER}} .wpst-ew-breadcrumb'=>'padding:{{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;')));
  $this->add_responsive_control('content_width',array('label'=>'İçerik Maks. Genişlik','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>320,'max'=>1600)),'selectors'=>array('{{WRAPPER}} .wpst-ew-breadcrumb>div'=>'max-width:{{SIZE}}px;margin-inline:auto;')));
  $this->end_controls_section();
  $this->standard_responsive_controls();
 }
 protected function render(){ $s=$this->get_settings_for_display(); echo '<section class="wpst-ew-breadcrumb"><div><h1>'.esc_html($s['title']).'</h1><nav aria-label="Breadcrumb"><a href="'.esc_url(home_url('/')).'">'.esc_html($s['home']).'</a>'; if('yes'===$s['show_current'])echo'<span class="wpst-breadcrumb-sep">'.(class_exists('WPST_Icon_Library')?WPST_Icon_Library::svg($s['separator_icon'],array('size'=>14)):'›').'</span><strong aria-current="page">'.esc_html($s['title']).'</strong>'; echo'</nav></div></section>'; }
}