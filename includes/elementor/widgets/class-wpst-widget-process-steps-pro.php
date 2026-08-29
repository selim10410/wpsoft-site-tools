<?php
if(!defined('ABSPATH'))exit;
class WPST_Widget_Process_Steps_Pro extends WPST_Elementor_Widget_Base{
 public function get_name(){return'wpsoft-process-steps-pro';} public function get_title(){return'WPSoft Process Steps Pro';} public function get_icon(){return'eicon-flow';}
 protected function register_controls(){ $this->start_controls_section('content',array('label'=>'Süreç'));
  $this->wpst_signature_preset_control(); $r=new \Elementor\Repeater(); $r->add_control('step',array('label'=>'Adım','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'01')); $r->add_control('title',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Analiz')); $r->add_control('text',array('label'=>'Metin','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'İhtiyaçları belirliyoruz.')); $this->add_control('items',array('label'=>'Adımlar','type'=>\Elementor\Controls_Manager::REPEATER,'fields'=>$r->get_controls(),'default'=>array(array('step'=>'01','title'=>'Analiz','text'=>'İhtiyaçları belirliyoruz.'),array('step'=>'02','title'=>'Planlama','text'=>'Yol haritası oluşturuyoruz.'),array('step'=>'03','title'=>'Uygulama','text'=>'Projeyi hayata geçiriyoruz.'),array('step'=>'04','title'=>'Destek','text'=>'Sürekliliği sağlıyoruz.')),'title_field'=>'{{{ step }}} · {{{ title }}}')); 
  $this->add_control('layout_variant',array(
   'label'=>'Süreç Yerleşimi','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'columns',
   'options'=>array('columns'=>'Columns','timeline'=>'Horizontal Timeline','cards'=>'Cards','compact'=>'Compact','editorial'=>'Editorial'),
   'prefix_class'=>'wpst-process-layout-'
  ));
  $this->add_control('show_connector',array('label'=>'Bağlantı Çizgisi','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes','prefix_class'=>'wpst-process-connector-'));
  $this->end_controls_section();
  $this->start_controls_section('process_style',array('label'=>'Süreç Biçimi','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('accent',array('label'=>'Vurgu Rengi','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#315cf5','selectors'=>array('{{WRAPPER}} .wpst-ew-process-steps-pro'=>'--wpst-process-accent:{{VALUE}};')));
  $this->add_control('line',array('label'=>'Çizgi Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-process-steps-pro'=>'--wpst-process-line:{{VALUE}};')));
  $this->add_control('card_bg',array('label'=>'Kart Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-process-steps-pro article'=>'background:{{VALUE}};')));
  $this->add_responsive_control('gap',array('label'=>'Adım Aralığı','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>70)),'selectors'=>array('{{WRAPPER}} .wpst-ew-process-steps-pro'=>'gap:{{SIZE}}px;')));
  $this->add_responsive_control('padding',array('label'=>'Kart İç Boşluk','type'=>\Elementor\Controls_Manager::DIMENSIONS,'size_units'=>array('px'),'selectors'=>array('{{WRAPPER}} .wpst-ew-process-steps-pro article'=>'padding:{{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;')));
  $this->add_responsive_control('radius',array('label'=>'Kart Köşe','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>50)),'selectors'=>array('{{WRAPPER}} .wpst-ew-process-steps-pro article'=>'border-radius:{{SIZE}}px;')));
  $this->end_controls_section(); $this->standard_responsive_controls(); }
 protected function render(){ $s=$this->get_settings_for_display(); echo'<div class="wpst-ew-process-steps-pro">'; foreach((array)$s['items'] as $i)echo'<article><b>'.esc_html($i['step']).'</b><div><h3>'.esc_html($i['title']).'</h3><p>'.esc_html($i['text']).'</p></div></article>'; echo'</div>'; }
}