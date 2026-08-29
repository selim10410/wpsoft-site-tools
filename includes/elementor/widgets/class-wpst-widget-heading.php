<?php
if(!defined('ABSPATH'))exit;
class WPST_Widget_Heading extends WPST_Elementor_Widget_Base{
 public function get_name(){return'wpsoft-heading';}
 public function get_title(){return'WPSoft · Heading 2.0';}
 public function get_icon(){return'eicon-heading';}
 public function get_keywords(){return array('heading','title','eyebrow','editorial','wpsoft');}
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'İçerik'));
  $this->add_control('eyebrow',array('label'=>'Üst Etiket','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'WPSOFT'));
  $this->add_control('title',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'Modern ve güçlü bir başlık'));
  $this->add_control('description',array('label'=>'Açıklama','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'Kısa ve anlaşılır açıklamanızı buraya yazın.'));
  $this->add_control('style_preset',array('label'=>'Stil','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'clean','options'=>array(
   'clean'=>'Clean','editorial'=>'Editorial','display'=>'Display','compact'=>'Compact'
  ),'prefix_class'=>'wpst-heading-style-'));
  $this->add_responsive_control('align',array('label'=>'Hizalama','type'=>\Elementor\Controls_Manager::CHOOSE,'options'=>array(
   'left'=>array('title'=>'Sol','icon'=>'eicon-text-align-left'),
   'center'=>array('title'=>'Orta','icon'=>'eicon-text-align-center'),
   'right'=>array('title'=>'Sağ','icon'=>'eicon-text-align-right')
  ),'default'=>'left','selectors'=>array('{{WRAPPER}} .wpst-ew-heading'=>'text-align:{{VALUE}};align-items:{{VALUE}};')));
  $this->add_responsive_control('max_width',array('label'=>'İçerik Genişliği','type'=>\Elementor\Controls_Manager::SLIDER,'size_units'=>array('px','%'),'range'=>array('px'=>array('min'=>280,'max'=>1200),'%'=>array('min'=>30,'max'=>100)),'default'=>array('unit'=>'px','size'=>760),'selectors'=>array('{{WRAPPER}} .wpst-ew-heading'=>'max-width:{{SIZE}}{{UNIT}};')));
  $this->end_controls_section();

  $this->start_controls_section('style_text',array('label'=>'Tipografi & Renk','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('accent',array('label'=>'Vurgu','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#2563eb','selectors'=>array('{{WRAPPER}} .wpst-ew-eyebrow'=>'color:{{VALUE}};')));
  $this->add_control('title_color',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#0f172a','selectors'=>array('{{WRAPPER}} .wpst-ew-title'=>'color:{{VALUE}};')));
  $this->add_control('desc_color',array('label'=>'Açıklama','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#64748b','selectors'=>array('{{WRAPPER}} .wpst-ew-desc'=>'color:{{VALUE}};')));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'title_typography','label'=>'Başlık Tipografisi','selector'=>'{{WRAPPER}} .wpst-ew-title'));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'desc_typography','label'=>'Açıklama Tipografisi','selector'=>'{{WRAPPER}} .wpst-ew-desc'));
  $this->add_responsive_control('title_gap',array('label'=>'Başlık Alt Boşluğu','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>60)),'default'=>array('size'=>16),'selectors'=>array('{{WRAPPER}} .wpst-ew-title'=>'margin-bottom:{{SIZE}}px;')));
  $this->end_controls_section();

  $this->start_controls_section('style_eyebrow',array('label'=>'Üst Etiket','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('eyebrow_mode',array('label'=>'Görünüm','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'plain','options'=>array('plain'=>'Plain','pill'=>'Pill','line'=>'Line'),'prefix_class'=>'wpst-eyebrow-mode-'));
  $this->add_control('eyebrow_bg',array('label'=>'Etiket Arka Planı','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-eyebrow'=>'--wpst-eyebrow-bg:{{VALUE}};')));
  $this->add_responsive_control('eyebrow_gap',array('label'=>'Başlığa Uzaklık','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>50)),'default'=>array('size'=>12),'selectors'=>array('{{WRAPPER}} .wpst-ew-eyebrow'=>'margin-bottom:{{SIZE}}px;')));
  $this->end_controls_section();
  $this->standard_responsive_controls();
 }
 protected function render(){
  $s=$this->get_settings_for_display();
  echo'<div class="wpst-ew-heading">';
  if(''!==trim((string)$s['eyebrow']))echo'<div class="wpst-ew-eyebrow">'.esc_html($s['eyebrow']).'</div>';
  echo'<h2 class="wpst-ew-title">'.wp_kses_post($s['title']).'</h2>';
  if(''!==trim((string)$s['description']))echo'<div class="wpst-ew-desc">'.wp_kses_post($s['description']).'</div>';
  echo'</div>';
 }
}
