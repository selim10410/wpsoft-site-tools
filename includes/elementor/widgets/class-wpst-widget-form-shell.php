<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class WPST_Widget_Form_Shell extends WPST_Elementor_Widget_Base {
 public function get_name(){ return 'wpsoft-form-shell'; }
 public function get_title(){ return 'WPSoft · Form Shell 2.0'; }
 public function get_icon(){ return 'eicon-form-horizontal'; }
 public function get_keywords(){ return array('form','contact','wpforms','cf7','shell','lead','wpsoft'); }
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'Form Sunumu'));
  $this->wpst_signature_preset_control();
  $this->add_control('eyebrow',array('label'=>'Etiket','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'İLETİŞİM','dynamic'=>array('active'=>true)));
  $this->add_control('title',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Projenizi konuşalım','dynamic'=>array('active'=>true)));
  $this->add_control('text',array('label'=>'Açıklama','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'Kısa form açıklaması ve beklenti yönetimi için modern bir sunum alanı.','dynamic'=>array('active'=>true)));
  $this->add_control('shortcode',array('label'=>'Form Shortcode','type'=>\Elementor\Controls_Manager::TEXT,'placeholder'=>'[wpforms id="123"]'));
  $this->add_control('layout',array('label'=>'Yerleşim','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'split','options'=>array('split'=>'Split','card'=>'Card','minimal'=>'Minimal','dark'=>'Dark Panel'),'prefix_class'=>'wpst-form-shell-layout-'));
  $this->end_controls_section();

  $this->start_controls_section('quality_style',array('label'=>'Form Shell Biçimi','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('form_position',array('label'=>'Form Konumu','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'right','options'=>array('right'=>'Sağ','left'=>'Sol'),'prefix_class'=>'wpst-form-shell-position-'));
  $this->add_control('bg',array('label'=>'Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-form-shell'=>'--wpst-form-shell-bg:{{VALUE}};')));
  $this->add_control('form_bg',array('label'=>'Form Alanı','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-form-shell'=>'--wpst-form-panel-bg:{{VALUE}};')));
  $this->add_control('border',array('label'=>'Kenarlık','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-form-shell'=>'--wpst-form-shell-border:{{VALUE}};')));
  $this->add_responsive_control('gap',array('label'=>'Kolon Aralığı','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>80)),'default'=>array('size'=>28),'selectors'=>array('{{WRAPPER}} .wpst-form-shell'=>'--wpst-form-shell-gap:{{SIZE}}px;')));
  $this->add_responsive_control('radius',array('label'=>'Köşe','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>60)),'default'=>array('size'=>24),'selectors'=>array('{{WRAPPER}} .wpst-form-shell'=>'--wpst-form-shell-radius:{{SIZE}}px;')));
  $this->add_responsive_control('padding',array('label'=>'Dış İç Boşluk','type'=>\Elementor\Controls_Manager::DIMENSIONS,'size_units'=>array('px'),'selectors'=>array('{{WRAPPER}} .wpst-form-shell'=>'padding:{{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;')));
  $this->add_responsive_control('form_padding',array('label'=>'Form Alanı İç Boşluk','type'=>\Elementor\Controls_Manager::DIMENSIONS,'size_units'=>array('px'),'selectors'=>array('{{WRAPPER}} .wpst-form-shell-form'=>'padding:{{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;')));
  $this->end_controls_section();

  $this->start_controls_section('content_style',array('label'=>'Metin Biçimi','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('eyebrow_color',array('label'=>'Etiket Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-form-shell'=>'--wpst-form-eyebrow:{{VALUE}};')));
  $this->add_control('title_color',array('label'=>'Başlık Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-form-shell'=>'--wpst-form-title:{{VALUE}};')));
  $this->add_control('text_color',array('label'=>'Açıklama Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-form-shell'=>'--wpst-form-text:{{VALUE}};')));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'title_typography','label'=>'Başlık Tipografi','selector'=>'{{WRAPPER}} .wpst-form-shell-copy h3'));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'text_typography','label'=>'Açıklama Tipografi','selector'=>'{{WRAPPER}} .wpst-form-shell-copy p'));
  $this->end_controls_section();

  $this->start_controls_section('form_style',array('label'=>'Form Alanları','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('input_bg',array('label'=>'Input Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-form-shell'=>'--wpst-form-input-bg:{{VALUE}};')));
  $this->add_control('input_text',array('label'=>'Input Yazı','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-form-shell'=>'--wpst-form-input-text:{{VALUE}};')));
  $this->add_control('input_border',array('label'=>'Input Kenarlık','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-form-shell'=>'--wpst-form-input-border:{{VALUE}};')));
  $this->add_control('input_focus',array('label'=>'Input Focus','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-form-shell'=>'--wpst-form-input-focus:{{VALUE}};')));
  $this->add_responsive_control('input_radius',array('label'=>'Input Radius','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>40)),'selectors'=>array('{{WRAPPER}} .wpst-form-shell'=>'--wpst-form-input-radius:{{SIZE}}px;')));
  $this->end_controls_section();
  $this->standard_responsive_controls();
 }
 protected function render(){
  $s=$this->get_settings_for_display();
  echo '<section class="wpst-form-shell"><div class="wpst-form-shell-copy"><span>'.esc_html($s['eyebrow']).'</span><h3>'.esc_html($s['title']).'</h3><p>'.esc_html($s['text']).'</p></div><div class="wpst-form-shell-form">';
  if(trim((string)$s['shortcode'])!=='') echo do_shortcode(wp_kses_post($s['shortcode']));
  else echo '<div class="wpst-form-shell-placeholder"><strong>Form shortcode ekleyin</strong><small>WPForms, Contact Form 7 veya shortcode destekleyen form eklentileriyle kullanabilirsiniz.</small></div>';
  echo '</div></section>';
 }
}
