<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class WPST_Widget_Number_Cards extends WPST_Elementor_Widget_Base {
 public function get_name(){return 'wpsoft-number-cards';}
 public function get_title(){return 'WPSoft Numaralı Kartlar';}
 public function get_icon(){return 'eicon-number-field';}
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'Kartlar'));
  $this->wpst_signature_preset_control();
  $r=new \Elementor\Repeater();
  $r->add_control('title',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Planlama'));
  $r->add_control('text',array('label'=>'Açıklama','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'Kısa açıklama.'));
  $this->add_control('items',array('label'=>'Kartlar','type'=>\Elementor\Controls_Manager::REPEATER,'fields'=>$r->get_controls(),'default'=>array(
   array('title'=>'Planlama','text'=>'Doğru stratejiyi belirliyoruz.'),
   array('title'=>'Uygulama','text'=>'Projeyi geliştiriyoruz.'),
   array('title'=>'Büyüme','text'=>'Sonuçları optimize ediyoruz.')
  ),'title_field'=>'{{{ title }}}'));
  $this->add_control('layout',array('label'=>'Yerleşim','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'grid','options'=>array('grid'=>'Grid','minimal'=>'Minimal','accent'=>'Accent'),'prefix_class'=>'wpst-number-cards-layout-'));
  $this->add_responsive_control('columns',array('label'=>'Kolon','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'3','tablet_default'=>'2','mobile_default'=>'1','options'=>array('1'=>'1','2'=>'2','3'=>'3','4'=>'4'),'selectors'=>array('{{WRAPPER}} .wpst-ew-number-cards'=>'grid-template-columns:repeat({{VALUE}},minmax(0,1fr))!important;')));
  $this->add_control('number_prefix',array('label'=>'Numara Ön Eki','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'')); $this->add_control('number_suffix',array('label'=>'Numara Son Eki','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'')); $this->add_control('number_pad',array('label'=>'Numara Basamak Sayısı','type'=>\Elementor\Controls_Manager::NUMBER,'min'=>1,'max'=>4,'default'=>2)); $this->end_controls_section();
 
        
  $this->start_controls_section('quality_style',array('label'=>'Numaralı Kart Biçimi','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('surface_bg',array('label'=>'Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-number-cards article'=>'background:{{VALUE}};')));
  $this->add_control('number_color',array('label'=>'Numara Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-number-cards article>span'=>'color:{{VALUE}};')));
  $this->add_control('title_color',array('label'=>'Başlık Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-number-cards h3'=>'color:{{VALUE}};')));
  $this->add_control('text_color',array('label'=>'Metin Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-number-cards p'=>'color:{{VALUE}};')));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'number_typography','label'=>'Numara Tipografisi','selector'=>'{{WRAPPER}} .wpst-ew-number-cards article>span'));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'title_typography','label'=>'Başlık Tipografisi','selector'=>'{{WRAPPER}} .wpst-ew-number-cards h3'));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'text_typography','label'=>'Metin Tipografisi','selector'=>'{{WRAPPER}} .wpst-ew-number-cards p'));
  $this->add_responsive_control('quality_gap',array('label'=>'Aralık','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>60)),'selectors'=>array('{{WRAPPER}}'=>'--wpst-quality-gap:{{SIZE}}px;')));
  $this->add_responsive_control('quality_radius',array('label'=>'Köşe','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>60)),'selectors'=>array('{{WRAPPER}} .wpst-ew-number-cards article'=>'border-radius:{{SIZE}}px;')));
  $this->add_responsive_control('quality_padding',array('label'=>'İç Boşluk','type'=>\Elementor\Controls_Manager::DIMENSIONS,'size_units'=>array('px'),'selectors'=>array('{{WRAPPER}} .wpst-ew-number-cards article'=>'padding:{{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;')));
  $this->add_control('border_color',array('label'=>'Kenarlık Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-number-cards article'=>'border-color:{{VALUE}};')));
  $this->add_control('hover_lift',array('label'=>'Hover Yükselme','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes','prefix_class'=>'wpst-number-hover-'));
  $this->end_controls_section();
        $this->standard_responsive_controls();
    }
 protected function render(){ $s=$this->get_settings_for_display(); echo '<div class="wpst-ew-number-cards">'; $n=1; foreach((array)$s['items'] as $i){ echo '<article><span>'.esc_html($s['number_prefix']).esc_html(str_pad((string)$n,max(1,(int)$s['number_pad']),'0',STR_PAD_LEFT)).esc_html($s['number_suffix']).'</span><h3>'.esc_html($i['title']).'</h3><p>'.esc_html($i['text']).'</p></article>'; $n++; } echo '</div>'; }
}