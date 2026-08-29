<?php
if(!defined('ABSPATH'))exit;
class WPST_Widget_Morphing_CTA extends WPST_Elementor_Widget_Base{
 public function get_name(){return'wpsoft-morphing-cta';} public function get_title(){return'WPSoft Morphing CTA 2.0';} public function get_icon(){return'eicon-button';}
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'İçerik'));
  $this->wpst_signature_preset_control();
  $this->add_control('eyebrow',array('label'=>'Etiket','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'NEXT PROJECT'));
  $this->add_control('title',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Birlikte çalışalım'));
  $this->add_control('text',array('label'=>'Açıklama','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'Hover sırasında şekil değiştiren modern CTA.'));
  $this->link_controls('button','Buton');
  $this->add_control('layout',array('label'=>'Kompozisyon','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'split','options'=>array('split'=>'Split','center'=>'Centered','compact'=>'Compact','statement'=>'Statement'),'prefix_class'=>'wpst-morphing-cta-layout-'));
  $this->add_control('morph_style',array('label'=>'Morph Stili','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'blob','options'=>array('blob'=>'Blob','circle'=>'Circle','pill'=>'Pill','none'=>'Kapalı'),'prefix_class'=>'wpst-morphing-cta-style-'));
  $this->end_controls_section();
  $this->start_controls_section('style',array('label'=>'CTA Biçimi','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('bg',array('label'=>'Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-morphing-cta'=>'background:{{VALUE}};')));
  $this->add_control('accent',array('label'=>'Vurgu','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#315cf5','selectors'=>array('{{WRAPPER}} .wpst-ew-morphing-cta'=>'--wpst-morph-accent:{{VALUE}};')));
  $this->add_responsive_control('padding',array('label'=>'İç Boşluk','type'=>\Elementor\Controls_Manager::DIMENSIONS,'size_units'=>array('px'),'selectors'=>array('{{WRAPPER}} .wpst-ew-morphing-cta'=>'padding:{{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;')));
  $this->add_responsive_control('radius',array('label'=>'Köşe','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>80)),'selectors'=>array('{{WRAPPER}} .wpst-ew-morphing-cta'=>'border-radius:{{SIZE}}px;')));
  $this->end_controls_section();
  $this->standard_responsive_controls();
 }
 protected function render(){ $s=$this->get_settings_for_display(); echo'<div class="wpst-ew-morphing-cta"><div><small>'.esc_html($s['eyebrow']).'</small><h3>'.esc_html($s['title']).'</h3><p>'.esc_html($s['text']).'</p></div>'; if($s['button_text'])echo'<a'.$this->render_link_attrs($s['button_url']).'><span>'.esc_html($s['button_text']).'</span><i class="wpst-native-arrow">'.(class_exists('WPST_Icon_Library')?WPST_Icon_Library::svg('arrow-up-right',array('size'=>14)):'↗').'</i></a>'; echo'</div>'; }
}