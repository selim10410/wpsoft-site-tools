<?php
if(!defined('ABSPATH'))exit;
class WPST_Widget_Timeline_Modern extends WPST_Elementor_Widget_Base{
 public function get_name(){return'wpsoft-timeline-modern';} public function get_title(){return'WPSoft Timeline Modern';} public function get_icon(){return'eicon-time-line';}
 protected function register_controls(){ $this->start_controls_section('content',array('label'=>'Timeline'));
  $this->wpst_signature_preset_control(); $r=new \Elementor\Repeater(); $r->add_control('year',array('label'=>'Yıl','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'2026')); $r->add_control('title',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Yeni dönem')); $r->add_control('text',array('label'=>'Açıklama','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'Önemli gelişme veya kilometre taşı.')); $this->add_control('items',array('label'=>'Adımlar','type'=>\Elementor\Controls_Manager::REPEATER,'fields'=>$r->get_controls(),'default'=>array(array('year'=>'2023','title'=>'Başlangıç','text'=>'İlk projelerimizi yayına aldık.'),array('year'=>'2025','title'=>'Büyüme','text'=>'Yeni hizmet ve sektörlere genişledik.'),array('year'=>'2026','title'=>'Yeni Nesil','text'=>'Daha modern deneyimler geliştiriyoruz.')),'title_field'=>'{{{ year }}} · {{{ title }}}')); 
  $this->add_control('layout_variant',array(
   'label'=>'Timeline Yerleşimi','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'vertical',
   'options'=>array('vertical'=>'Vertical','alternating'=>'Alternating','compact'=>'Compact','cards'=>'Cards','milestones'=>'Milestones'),
   'prefix_class'=>'wpst-timeline-layout-'
  ));
  $this->add_control('show_line',array('label'=>'Timeline Çizgisi','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes','prefix_class'=>'wpst-timeline-line-'));
  $this->end_controls_section();
  $this->start_controls_section('timeline_style',array('label'=>'Timeline Biçimi','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('accent',array('label'=>'Vurgu Rengi','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#315cf5','selectors'=>array('{{WRAPPER}} .wpst-ew-timeline-modern'=>'--wpst-timeline-accent:{{VALUE}};')));
  $this->add_control('line_color',array('label'=>'Çizgi Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-timeline-modern'=>'--wpst-timeline-line:{{VALUE}};')));
  $this->add_control('card_bg',array('label'=>'Kart Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-timeline-modern article>div'=>'background:{{VALUE}};')));
  $this->add_responsive_control('gap',array('label'=>'Adım Aralığı','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>8,'max'=>70)),'selectors'=>array('{{WRAPPER}} .wpst-ew-timeline-modern'=>'gap:{{SIZE}}px;')));
  $this->add_responsive_control('radius',array('label'=>'Kart Köşe','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>50)),'selectors'=>array('{{WRAPPER}} .wpst-ew-timeline-modern article>div'=>'border-radius:{{SIZE}}px;')));
  $this->end_controls_section(); $this->standard_responsive_controls(); }
 protected function render(){ $s=$this->get_settings_for_display(); echo'<div class="wpst-ew-timeline-modern">'; foreach((array)$s['items'] as $i)echo'<article><span>'.esc_html($i['year']).'</span><i></i><div><h3>'.esc_html($i['title']).'</h3><p>'.esc_html($i['text']).'</p></div></article>'; echo'</div>'; }
}