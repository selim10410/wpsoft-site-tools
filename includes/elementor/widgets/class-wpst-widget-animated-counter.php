<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class WPST_Widget_Animated_Counter extends WPST_Elementor_Widget_Base {
 public function get_name(){return 'wpsoft-animated-counter';}
 public function get_title(){return 'WPSoft Animasyonlu Sayaç';}
 public function get_icon(){return 'eicon-counter-circle';}
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'İçerik'));
  $this->wpst_signature_preset_control();
  $this->add_control('number',array('label'=>'Hedef Değer','type'=>\Elementor\Controls_Manager::NUMBER,'default'=>250));
  $this->add_control('prefix',array('label'=>'Ön Ek','type'=>\Elementor\Controls_Manager::TEXT,'default'=>''));
  $this->add_control('suffix',array('label'=>'Son Ek','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'+'));
  $this->add_control('label',array('label'=>'Açıklama','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Tamamlanan Proje'));
  $this->add_control('duration',array('label'=>'Animasyon Süresi (ms)','type'=>\Elementor\Controls_Manager::NUMBER,'default'=>1600,'min'=>300,'step'=>100));
  $this->end_controls_section();
  $this->start_controls_section('style',array('label'=>'Biçim','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('number_color',array('label'=>'Sayı Rengi','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#0f172a','selectors'=>array('{{WRAPPER}} .wpst-ew-animated-counter strong'=>'color:{{VALUE}}')));
  $this->add_control('label_color',array('label'=>'Açıklama Rengi','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#64748b','selectors'=>array('{{WRAPPER}} .wpst-ew-animated-counter span'=>'color:{{VALUE}}')));
  
  $this->add_control('layout_variant',array(
   'label'=>'Sayaç Yerleşimi','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'stacked',
   'options'=>array('stacked'=>'Stacked','inline'=>'Inline','boxed'=>'Boxed','statement'=>'Statement'),
   'prefix_class'=>'wpst-counter-layout-'
  ));
  $this->add_responsive_control('number_size',array('label'=>'Sayı Boyutu','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>24,'max'=>140)),'selectors'=>array('{{WRAPPER}} .wpst-ew-animated-counter strong'=>'font-size:{{SIZE}}px;')));
  $this->add_responsive_control('label_size',array('label'=>'Açıklama Boyutu','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>10,'max'=>40)),'selectors'=>array('{{WRAPPER}} .wpst-ew-animated-counter span'=>'font-size:{{SIZE}}px;')));
  $this->add_responsive_control('gap',array('label'=>'Sayı / Açıklama Aralığı','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>40)),'selectors'=>array('{{WRAPPER}} .wpst-ew-animated-counter'=>'gap:{{SIZE}}px;')));
  $this->add_responsive_control('align',array('label'=>'Hizalama','type'=>\Elementor\Controls_Manager::CHOOSE,'options'=>array('left'=>array('title'=>'Sol','icon'=>'eicon-text-align-left'),'center'=>array('title'=>'Orta','icon'=>'eicon-text-align-center'),'right'=>array('title'=>'Sağ','icon'=>'eicon-text-align-right')),'selectors'=>array('{{WRAPPER}} .wpst-ew-animated-counter'=>'text-align:{{VALUE}};align-items:{{VALUE}};')));

  $this->end_controls_section();
 
        $this->standard_responsive_controls();
    }
 protected function render(){ $s=$this->get_settings_for_display(); echo '<div class="wpst-ew-animated-counter" data-target="'.esc_attr((float)$s['number']).'" data-duration="'.esc_attr((int)$s['duration']).'" data-prefix="'.esc_attr($s['prefix']).'" data-suffix="'.esc_attr($s['suffix']).'"><strong>'.esc_html($s['prefix']).'0'.esc_html($s['suffix']).'</strong><span>'.esc_html($s['label']).'</span></div>'; }
}