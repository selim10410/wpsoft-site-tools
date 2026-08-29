<?php
if(!defined('ABSPATH'))exit;
class WPST_Widget_Logo_Grid_Pro extends WPST_Elementor_Widget_Base{
 public function get_name(){return'wpsoft-logo-grid-pro';}public function get_title(){return'WPSoft · Logo Grid Pro 2.0';}public function get_icon(){return'eicon-gallery-grid';}
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'Logolar'));
  $this->wpst_signature_preset_control();
  $r=new \Elementor\Repeater();
  $r->add_control('image',array('label'=>'Logo','type'=>\Elementor\Controls_Manager::MEDIA));
  $r->add_control('name',array('label'=>'Marka','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Partner'));
  $r->add_control('url',array('label'=>'Bağlantı','type'=>\Elementor\Controls_Manager::URL));
  $this->add_control('items',array('label'=>'Logolar','type'=>\Elementor\Controls_Manager::REPEATER,'fields'=>$r->get_controls(),'default'=>array(array('name'=>'Partner 01'),array('name'=>'Partner 02'),array('name'=>'Partner 03'),array('name'=>'Partner 04')),'title_field'=>'{{{ name }}}'));
  $this->add_control('layout_variant',array('label'=>'Logo Grid Yerleşimi','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'grid','options'=>array('grid'=>'Grid','compact'=>'Compact','framed'=>'Framed','editorial'=>'Editorial'),'prefix_class'=>'wpst-logo-grid-layout-'));
  $this->add_responsive_control('columns',array('label'=>'Kolon','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'4','tablet_default'=>'3','mobile_default'=>'2','options'=>array('2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6'),'selectors'=>array('{{WRAPPER}} .wpst-logo-grid-pro'=>'grid-template-columns:repeat({{VALUE}},minmax(0,1fr))!important;')));
  $this->add_control('grayscale',array('label'=>'Gri Ton','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'','prefix_class'=>'wpst-logo-grid-gray-'));
  $this->add_control('hover_color',array('label'=>'Hover’da Renk','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes','prefix_class'=>'wpst-logo-grid-hover-color-'));
  $this->end_controls_section();
  $this->start_controls_section('logo_style',array('label'=>'Logo Biçimi','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_responsive_control('logo_height',array('label'=>'Logo Yüksekliği','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>18,'max'=>140)),'default'=>array('size'=>44),'selectors'=>array('{{WRAPPER}} .wpst-logo-grid-pro img'=>'max-height:{{SIZE}}px;width:auto;')));
  $this->add_responsive_control('gap',array('label'=>'Grid Aralığı','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>60)),'selectors'=>array('{{WRAPPER}} .wpst-logo-grid-pro'=>'gap:{{SIZE}}px;')));
  $this->add_control('cell_bg',array('label'=>'Hücre Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-logo-grid-pro>div'=>'background:{{VALUE}};')));
  $this->add_responsive_control('cell_padding',array('label'=>'Hücre İç Boşluk','type'=>\Elementor\Controls_Manager::DIMENSIONS,'size_units'=>array('px'),'selectors'=>array('{{WRAPPER}} .wpst-logo-grid-pro>div'=>'padding:{{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;')));
  $this->add_responsive_control('cell_radius',array('label'=>'Hücre Köşe','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>50)),'selectors'=>array('{{WRAPPER}} .wpst-logo-grid-pro>div'=>'border-radius:{{SIZE}}px;')));
  $this->end_controls_section();
  $this->standard_responsive_controls();
 }
 protected function render(){$s=$this->get_settings_for_display();echo'<div class="wpst-logo-grid-pro">';foreach((array)$s['items'] as $it){$url=!empty($it['url']['url'])?esc_url($it['url']['url']):'';echo'<div>';if($url)echo'<a href="'.$url.'">';if(!empty($it['image']['url']))echo'<img src="'.esc_url($it['image']['url']).'" alt="'.esc_attr($it['name']).'" loading="lazy">';else echo'<strong>'.esc_html($it['name']).'</strong>';if($url)echo'</a>';echo'</div>';}echo'</div>';}
}