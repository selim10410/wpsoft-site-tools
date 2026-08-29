<?php
if(!defined('ABSPATH'))exit;
class WPST_Widget_Advanced_Accordion extends WPST_Elementor_Widget_Base{
 public function get_name(){return'wpsoft-advanced-accordion';}
 public function get_title(){return'WPSoft · Advanced Accordion 2.0';}
 public function get_icon(){return'eicon-accordion';}
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'Accordion'));
  $this->wpst_signature_preset_control();
  $r=new \Elementor\Repeater();
  $r->add_control('wpst_icon',array('label'=>'WPSoft Icon','type'=>\Elementor\Controls_Manager::SELECT2,'options'=>class_exists('WPST_Icon_Library')?WPST_Icon_Library::options():array(),'default'=>'help-circle','label_block'=>true));
  $r->add_control('title',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Sık Sorulan Soru'));
  $r->add_control('content',array('label'=>'İçerik','type'=>\Elementor\Controls_Manager::WYSIWYG,'default'=>'Detaylı açıklama alanı.'));
  $this->add_control('items',array(
   'label'=>'Öğeler','type'=>\Elementor\Controls_Manager::REPEATER,'fields'=>$r->get_controls(),
   'default'=>array(
    array('wpst_icon'=>'briefcase','title'=>'Hizmet kapsamı nedir?','content'=>'Hizmet detaylarını burada açıklayın.'),
    array('wpst_icon'=>'layers','title'=>'Nasıl çalışır?','content'=>'Süreci kısa ve anlaşılır biçimde anlatın.')
   ),
   'title_field'=>'{{{ title }}}'
  ));
  $this->add_control('first_open',array('label'=>'İlk Öğe Açık','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes'));
  $this->add_control('toggle_icon',array('label'=>'Aç/Kapat Icon','type'=>\Elementor\Controls_Manager::SELECT2,'options'=>class_exists('WPST_Icon_Library')?WPST_Icon_Library::options():array(),'default'=>'chevron-down','label_block'=>true));
  $this->add_control('style_preset',array('label'=>'Stil','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'cards','options'=>array('clean'=>'Clean','cards'=>'Cards','soft'=>'Soft','dark'=>'Dark'),'prefix_class'=>'wpst-accordion-style-'));
  $this->add_control('layout_variant',array(
   'label'=>'Accordion Kompozisyonu','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'stack',
   'options'=>array('stack'=>'Stack','divided'=>'Divided','numbered'=>'Numbered','minimal'=>'Minimal','panel'=>'Panel'),
   'prefix_class'=>'wpst-accordion-layout-'
  ));
  $this->end_controls_section();

  $this->start_controls_section('style',array('label'=>'Biçim','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('accent',array('label'=>'Vurgu','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#2563eb','selectors'=>array('{{WRAPPER}} .wpst-adv-accordion'=>'--acc-accent:{{VALUE}};')));
  $this->add_control('title_color',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-adv-accordion'=>'--acc-title:{{VALUE}};')));
  $this->add_control('text_color',array('label'=>'Metin','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-adv-accordion'=>'--acc-text:{{VALUE}};')));
  $this->add_responsive_control('radius',array('label'=>'Köşe','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>40)),'default'=>array('size'=>16),'selectors'=>array('{{WRAPPER}} .wpst-adv-acc-item'=>'border-radius:{{SIZE}}px;')));
  $this->end_controls_section();
  $this->standard_responsive_controls();
 }
 protected function render(){
  $s=$this->get_settings_for_display();
  echo'<div class="wpst-adv-accordion">';
  foreach((array)$s['items'] as $i=>$it){
   $open=$i===0&&'yes'===$s['first_open'];
   echo'<details class="wpst-adv-acc-item" '.($open?'open':'').'><summary><span class="wpst-adv-acc-title"><i>'.(class_exists('WPST_Icon_Library')?WPST_Icon_Library::svg($it['wpst_icon'],array('size'=>16)):'').'</i><b>'.esc_html($it['title']).'</b></span><i class="wpst-acc-toggle">'.(class_exists('WPST_Icon_Library')?WPST_Icon_Library::svg($s['toggle_icon'],array('size'=>15)):'⌄').'</i></summary><div class="wpst-adv-acc-content">'.wp_kses_post($it['content']).'</div></details>';
  }
  echo'</div>';
 }
}
