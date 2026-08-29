<?php
if(!defined('ABSPATH'))exit;
class WPST_Widget_CTA extends WPST_Elementor_Widget_Base{
 public function get_name(){return'wpsoft-cta';}
 public function get_title(){return'WPSoft · CTA 2.0';}
 public function get_icon(){return'eicon-call-to-action';}
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'İçerik'));
  $this->wpst_signature_preset_control();
  $this->add_control('title',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'Projenizi birlikte hayata geçirelim.'));
  $this->add_control('description',array('label'=>'Açıklama','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'İhtiyacınızı anlatın, size en uygun çözümü hazırlayalım.'));
  $this->link_controls('button','Buton');
  $this->add_control('wpst_button_icon',array('label'=>'WPSoft Buton Icon','type'=>\Elementor\Controls_Manager::SELECT2,'options'=>class_exists('WPST_Icon_Library')?WPST_Icon_Library::options():array(),'default'=>'arrow-right','label_block'=>true));
  $this->add_control('button_icon',array('label'=>'Eski Buton Sembolü','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'→','description'=>'Eski içerikler için fallback.'));
  $this->add_control('layout_style',array(
   'label'=>'Yerleşim',
   'type'=>\Elementor\Controls_Manager::SELECT,
   'default'=>'split',
   'options'=>array(
    'split'=>'Split',
    'center'=>'Centered',
    'compact'=>'Compact',
    'banner'=>'Full Banner',
    'editorial'=>'Editorial Statement',
    'floating'=>'Floating Card',
    'inline'=>'Inline Minimal'
   ),
   'prefix_class'=>'wpst-cta-layout-'
  ));
  $this->add_control('surface_style',array('label'=>'Yüzey','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'dark','options'=>array('dark'=>'Dark','light'=>'Light','gradient'=>'Gradient','glass'=>'Glass'),'prefix_class'=>'wpst-cta-surface-'));
  $this->add_control('show_shape',array('label'=>'Dekoratif SVG','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes'));
  $this->add_control('shape',array('label'=>'SVG Shape','type'=>\Elementor\Controls_Manager::SELECT2,'options'=>class_exists('WPST_SVG_Library')?WPST_SVG_Library::options():array(),'default'=>'rings','label_block'=>true,'condition'=>array('show_shape'=>'yes')));
  $this->end_controls_section();

  $this->start_controls_section('style_surface',array('label'=>'Yüzey','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('bg',array('label'=>'Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#0f172a','selectors'=>array('{{WRAPPER}} .wpst-ew-cta'=>'--cta-bg:{{VALUE}};')));
  $this->add_control('title_color',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#ffffff','selectors'=>array('{{WRAPPER}} .wpst-ew-cta'=>'--cta-title:{{VALUE}};')));
  $this->add_control('desc_color',array('label'=>'Açıklama','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#cbd5e1','selectors'=>array('{{WRAPPER}} .wpst-ew-cta'=>'--cta-text:{{VALUE}};')));
  $this->add_control('shape_color',array('label'=>'SVG Rengi','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#ffffff','selectors'=>array('{{WRAPPER}} .wpst-cta-shape'=>'color:{{VALUE}};')));
  $this->add_responsive_control('padding',array('label'=>'İç Boşluk','type'=>\Elementor\Controls_Manager::DIMENSIONS,'size_units'=>array('px'),'selectors'=>array('{{WRAPPER}} .wpst-ew-cta'=>'padding:{{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;')));
  $this->add_responsive_control('radius',array('label'=>'Köşe','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>70)),'default'=>array('size'=>28),'selectors'=>array('{{WRAPPER}} .wpst-ew-cta'=>'border-radius:{{SIZE}}px;')));
  $this->end_controls_section();

  $this->start_controls_section('style_button',array('label'=>'Buton','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('button_bg',array('label'=>'Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#ffffff','selectors'=>array('{{WRAPPER}} .wpst-ew-button'=>'--cta-btn-bg:{{VALUE}};')));
  $this->add_control('button_color',array('label'=>'Yazı','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#0f172a','selectors'=>array('{{WRAPPER}} .wpst-ew-button'=>'--cta-btn-color:{{VALUE}};')));
  $this->add_control('button_hover',array('label'=>'Hover Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#2563eb','selectors'=>array('{{WRAPPER}} .wpst-ew-button'=>'--cta-btn-hover:{{VALUE}};')));
  $this->add_responsive_control('button_radius',array('label'=>'Buton Köşe','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>50)),'default'=>array('size'=>14),'selectors'=>array('{{WRAPPER}} .wpst-ew-button'=>'border-radius:{{SIZE}}px;')));
  $this->end_controls_section();
  $this->standard_responsive_controls();
 }
 protected function render(){
  $s=$this->get_settings_for_display();
  echo'<section class="wpst-ew-cta">';
  if('yes'===$s['show_shape']&&class_exists('WPST_SVG_Library'))echo'<div class="wpst-cta-shape">'.WPST_SVG_Library::inline($s['shape'],array('class'=>'wpst-cta-shape-svg')).'</div>';
  echo'<div class="wpst-cta-copy"><h2>'.wp_kses_post($s['title']).'</h2><p>'.wp_kses_post($s['description']).'</p></div>';
  echo'<a class="wpst-ew-button"'.$this->render_link_attrs($s['button_url']).'><span>'.esc_html($s['button_text']).'</span><span class="wpst-button-native-icon">'.((!empty($s['wpst_button_icon'])&&class_exists('WPST_Icon_Library'))?WPST_Icon_Library::svg($s['wpst_button_icon'],array('size'=>16)):esc_html($s['button_icon'])).'</span></a></section>';
 }
}
