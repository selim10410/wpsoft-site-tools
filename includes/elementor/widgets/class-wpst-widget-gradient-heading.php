<?php
if(!defined('ABSPATH'))exit;
class WPST_Widget_Gradient_Heading extends WPST_Elementor_Widget_Base{
 public function get_name(){return'wpsoft-gradient-heading';}
 public function get_title(){return'WPSoft Gradient Başlık';}
 public function get_icon(){return'eicon-heading';}
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'İçerik'));
  $this->add_control('eyebrow',array('label'=>'Üst Etiket','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Yeni Nesil Tasarım'));
  $this->add_control('title',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'Markanızı daha güçlü ve modern gösterin'));
  $this->add_control('text',array('label'=>'Açıklama','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'Gradient tipografi ve güçlü boşluk kullanımıyla dikkat çekici bölüm başlığı.'));
  $this->wpst_signature_preset_control();
  $this->add_control('layout',array('label'=>'Yerleşim','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'left','options'=>array('left'=>'Sol','center'=>'Orta','split'=>'Split','display'=>'Display'),'prefix_class'=>'wpst-gradient-heading-layout-'));
  $this->add_control('highlight_mode',array('label'=>'Gradient Kullanımı','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'full','options'=>array('full'=>'Tüm Başlık','first'=>'İlk Satır','accent'=>'Vurgu Katmanı'),'prefix_class'=>'wpst-gradient-heading-mode-'));
  $this->end_controls_section();
  $this->start_controls_section('style',array('label'=>'Biçim','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('c1',array('label'=>'Gradient Başlangıç','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#2563eb'));
  $this->add_control('c2',array('label'=>'Gradient Bitiş','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#7c3aed'));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'title_typography','selector'=>'{{WRAPPER}} .wpst-ew-gradient-heading h2'));
  $this->add_control('eyebrow_color',array('label'=>'Üst Etiket Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-gradient-heading small'=>'color:{{VALUE}};')));
  $this->add_control('text_color',array('label'=>'Açıklama Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-gradient-heading p'=>'color:{{VALUE}};')));
  $this->add_responsive_control('max_width',array('label'=>'Maks. Genişlik','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>320,'max'=>1400)),'selectors'=>array('{{WRAPPER}} .wpst-ew-gradient-heading'=>'max-width:{{SIZE}}px;')));
  $this->add_responsive_control('spacing',array('label'=>'Başlık Alt Boşluk','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>60)),'selectors'=>array('{{WRAPPER}} .wpst-ew-gradient-heading h2'=>'margin-bottom:{{SIZE}}px;')));
  $this->end_controls_section();
 
        $this->standard_responsive_controls();
    }
 protected function render(){ $s=$this->get_settings_for_display(); $c1=$s['c1']?:'#2563eb';$c2=$s['c2']?:'#7c3aed'; echo'<section class="wpst-ew-gradient-heading"><small>'.esc_html($s['eyebrow']).'</small><h2 style="--wpst-g1:'.esc_attr($c1).';--wpst-g2:'.esc_attr($c2).'">'.wp_kses_post($s['title']).'</h2><p>'.esc_html($s['text']).'</p></section>'; }
}