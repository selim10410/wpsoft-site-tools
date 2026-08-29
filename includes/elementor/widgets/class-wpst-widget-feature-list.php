<?php
if(!defined('ABSPATH'))exit;
class WPST_Widget_Feature_List extends WPST_Elementor_Widget_Base{
 public function get_name(){return'wpsoft-feature-list';}
 public function get_title(){return'WPSoft · Feature List 2.0';}
 public function get_icon(){return'eicon-check-circle';}
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'İçerik'));
  $this->add_control('eyebrow',array('label'=>'Üst Etiket','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'NEDEN BİZ?'));
  $this->add_control('title',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'İşinizi büyütmek için doğru dijital altyapı'));
  $r=new \Elementor\Repeater();
  $r->add_control('wpst_icon',array('label'=>'WPSoft Icon','type'=>\Elementor\Controls_Manager::SELECT2,'options'=>class_exists('WPST_Icon_Library')?WPST_Icon_Library::options():array(),'default'=>'check','label_block'=>true));
  $r->add_control('title',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Modern Tasarım'));
  $r->add_control('text',array('label'=>'Açıklama','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'Markanıza uygun modern ve kullanıcı odaklı arayüz.'));
  $this->add_control('items',array('label'=>'Özellikler','type'=>\Elementor\Controls_Manager::REPEATER,'fields'=>$r->get_controls(),'default'=>array(
   array('wpst_icon'=>'palette','title'=>'Modern Tasarım','text'=>'Markanıza uygun modern arayüz.'),
   array('wpst_icon'=>'smartphone','title'=>'Mobil Uyumlu','text'=>'Tüm cihazlarda kusursuz deneyim.'),
   array('wpst_icon'=>'bolt','title'=>'Hız Odaklı','text'=>'Performans ve kullanıcı deneyimi öncelikli yapı.')
  ),'title_field'=>'{{{ title }}}'));
  $this->add_control('layout',array('label'=>'Yerleşim','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'split','options'=>array('split'=>'Split','stack'=>'Stack'),'prefix_class'=>'wpst-feature-layout-'));
  $this->add_control('item_style',array('label'=>'Öğe Stili','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'card','options'=>array('card'=>'Card','line'=>'Line','soft'=>'Soft'),'prefix_class'=>'wpst-feature-style-'));
  $this->end_controls_section();

  $this->start_controls_section('style_items',array('label'=>'Özellik Kartları','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('item_bg',array('label'=>'Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-feature-items article'=>'--fl-bg:{{VALUE}};')));
  $this->add_control('item_border',array('label'=>'Border','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-feature-items article'=>'--fl-border:{{VALUE}};')));
  $this->add_control('accent',array('label'=>'Icon Vurgu','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#2563eb','selectors'=>array('{{WRAPPER}} .wpst-ew-feature-items article'=>'--fl-accent:{{VALUE}};')));
  $this->add_responsive_control('item_padding',array('label'=>'İç Boşluk','type'=>\Elementor\Controls_Manager::DIMENSIONS,'size_units'=>array('px'),'selectors'=>array('{{WRAPPER}} .wpst-ew-feature-items article'=>'padding:{{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;')));
  $this->add_responsive_control('item_radius',array('label'=>'Köşe','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>50)),'default'=>array('size'=>18),'selectors'=>array('{{WRAPPER}} .wpst-ew-feature-items article'=>'border-radius:{{SIZE}}px;')));
  $this->end_controls_section();

  $this->start_controls_section('style_heading',array('label'=>'Başlık Alanı','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('eyebrow_color',array('label'=>'Üst Etiket','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#2563eb','selectors'=>array('{{WRAPPER}} .wpst-ew-feature-head small'=>'color:{{VALUE}};')));
  $this->add_control('heading_color',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#0f172a','selectors'=>array('{{WRAPPER}} .wpst-ew-feature-head h2'=>'color:{{VALUE}};')));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'heading_typography','selector'=>'{{WRAPPER}} .wpst-ew-feature-head h2'));
  $this->end_controls_section();
  $this->standard_responsive_controls();
 }
 protected function render(){
  $s=$this->get_settings_for_display();
  echo'<section class="wpst-ew-feature-list"><div class="wpst-ew-feature-head"><small>'.esc_html($s['eyebrow']).'</small><h2>'.wp_kses_post($s['title']).'</h2></div><div class="wpst-ew-feature-items">';
  foreach((array)$s['items'] as $i){
   echo'<article><span class="wpst-feature-check">';
   if(!empty($i['wpst_icon'])&&class_exists('WPST_Icon_Library'))WPST_Icon_Library::render($i['wpst_icon']);else echo'✓';
   echo'</span><div><h3>'.esc_html($i['title']).'</h3><p>'.esc_html($i['text']).'</p></div></article>';
  }
  echo'</div></section>';
 }
}
