<?php
if(!defined('ABSPATH'))exit;
class WPST_Widget_Mouse_Follow_Card extends WPST_Elementor_Widget_Base{
 public function get_name(){return'wpsoft-mouse-follow-card';}
 public function get_title(){return'WPSoft Mouse Follow Kart 2.0';}
 public function get_icon(){return'eicon-hotspot';}
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'İçerik'));
  $this->wpst_signature_preset_control();
  $this->add_control('title',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'Etkileşimli modern kart'));
  $this->add_control('text',array('label'=>'Açıklama','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'Mouse hareketine göre ışık ve perspektif efekti.'));
  $this->add_control('footer_text',array('label'=>'Alt Etiket','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'WPSoft Experience'));
  $this->add_control('layout',array('label'=>'Kart Düzeni','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'dark','options'=>array('dark'=>'Dark','light'=>'Light','glass'=>'Glass','minimal'=>'Minimal'),'prefix_class'=>'wpst-mouse-card-layout-'));
  $this->add_control('follow_strength',array('label'=>'Mouse Takip Gücü','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>2,'max'=>30)),'default'=>array('size'=>12),'selectors'=>array('{{WRAPPER}} .wpst-ew-mouse-card'=>'--wpst-follow-strength:{{SIZE}};')));
  $this->add_control('glow_size',array('label'=>'Işık Boyutu','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>80,'max'=>600)),'default'=>array('size'=>280),'selectors'=>array('{{WRAPPER}} .wpst-ew-mouse-glow'=>'width:{{SIZE}}px;height:{{SIZE}}px;')));
  $this->end_controls_section();
  $this->start_controls_section('style',array('label'=>'Biçim','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('bg',array('label'=>'Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#0f172a','selectors'=>array('{{WRAPPER}} .wpst-ew-mouse-card'=>'background:{{VALUE}}')));
  $this->add_control('glow',array('label'=>'Işık Rengi','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'rgba(99,102,241,.35)','selectors'=>array('{{WRAPPER}} .wpst-ew-mouse-glow'=>'background:radial-gradient(circle,{{VALUE}},transparent 68%);')));
  $this->add_responsive_control('min_height',array('label'=>'Minimum Yükseklik','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>220,'max'=>700)),'selectors'=>array('{{WRAPPER}} .wpst-ew-mouse-card'=>'min-height:{{SIZE}}px;')));
  $this->add_responsive_control('radius',array('label'=>'Köşe','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>60)),'selectors'=>array('{{WRAPPER}} .wpst-ew-mouse-card'=>'border-radius:{{SIZE}}px;')));
  $this->end_controls_section();
  $this->standard_responsive_controls();
 }
 protected function render(){ $s=$this->get_settings_for_display();echo'<article class="wpst-ew-mouse-card"><div class="wpst-ew-mouse-glow"></div><h3>'.wp_kses_post($s['title']).'</h3><p>'.esc_html($s['text']).'</p><span>'.esc_html($s['footer_text']).'</span></article>'; }
}