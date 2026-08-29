<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class WPST_Widget_Quote extends WPST_Elementor_Widget_Base {
 public function get_name(){return 'wpsoft-quote';} public function get_title(){return 'WPSoft Büyük Alıntı 2.0';} public function get_icon(){return 'eicon-blockquote';}
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'İçerik'));
  $this->wpst_signature_preset_control();
  $this->add_control('quote',array('label'=>'Alıntı','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'İyi bir web sitesi yalnızca güzel görünmez; markanın değerini hissettirir.'));
  $this->add_control('name',array('label'=>'İsim','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'WPSoft'));
  $this->add_control('role',array('label'=>'Alt Bilgi','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Dijital Çözümler'));
  $this->add_control('image',array('label'=>'Avatar','type'=>\Elementor\Controls_Manager::MEDIA));
  $this->add_control('layout',array('label'=>'Yerleşim','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'statement','options'=>array('statement'=>'Statement','card'=>'Card','editorial'=>'Editorial','profile'=>'Profile'),'prefix_class'=>'wpst-quote-layout-'));
  $this->add_control('quote_mark',array('label'=>'Alıntı İşareti','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes'));
  $this->end_controls_section();
  $this->start_controls_section('quote_style',array('label'=>'Alıntı Biçimi','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('accent',array('label'=>'Vurgu','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#315cf5','selectors'=>array('{{WRAPPER}} .wpst-ew-big-quote'=>'--wpst-quote-accent:{{VALUE}};')));
  $this->add_control('bg',array('label'=>'Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-big-quote'=>'background:{{VALUE}};')));
  $this->add_control('quote_color',array('label'=>'Alıntı Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-big-quote p'=>'color:{{VALUE}};')));
  $this->add_control('name_color',array('label'=>'İsim Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-big-quote footer strong'=>'color:{{VALUE}};')));
  $this->add_control('meta_color',array('label'=>'Alt Bilgi Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-big-quote footer small'=>'color:{{VALUE}};')));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'quote_typography','label'=>'Alıntı Tipografisi','selector'=>'{{WRAPPER}} .wpst-ew-big-quote p'));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'name_typography','label'=>'İsim Tipografisi','selector'=>'{{WRAPPER}} .wpst-ew-big-quote footer strong'));
  $this->add_responsive_control('quote_size',array('label'=>'Alıntı Boyutu','type'=>\Elementor\Controls_Manager::SLIDER,'size_units'=>array('px','vw'),'range'=>array('px'=>array('min'=>18,'max'=>100),'vw'=>array('min'=>2,'max'=>9,'step'=>.1)),'selectors'=>array('{{WRAPPER}} .wpst-ew-big-quote p'=>'font-size:{{SIZE}}{{UNIT}};')));
  $this->add_responsive_control('avatar_size',array('label'=>'Avatar Boyutu','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>32,'max'=>120)),'selectors'=>array('{{WRAPPER}} .wpst-ew-big-quote footer img'=>'width:{{SIZE}}px;height:{{SIZE}}px;')));
  $this->add_responsive_control('padding',array('label'=>'İç Boşluk','type'=>\Elementor\Controls_Manager::DIMENSIONS,'size_units'=>array('px'),'selectors'=>array('{{WRAPPER}} .wpst-ew-big-quote'=>'padding:{{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;')));
  $this->add_responsive_control('radius',array('label'=>'Köşe','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>60)),'selectors'=>array('{{WRAPPER}} .wpst-ew-big-quote'=>'border-radius:{{SIZE}}px;')));
  $this->add_control('border_color',array('label'=>'Kenarlık Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-big-quote'=>'border-color:{{VALUE}};')));
  $this->end_controls_section();
  $this->standard_responsive_controls();
 }
 protected function render(){ $s=$this->get_settings_for_display(); echo '<blockquote class="wpst-ew-big-quote">'; if('yes'===$s['quote_mark'])echo'<span class="wpst-quote-mark">“</span>'; echo'<p>'.esc_html($s['quote']).'</p><footer>'; if(!empty($s['image']['url']))echo'<img src="'.esc_url($s['image']['url']).'" alt="'.esc_attr($s['name']).'" loading="lazy"><div>'; echo'<strong>'.esc_html($s['name']).'</strong><small>'.esc_html($s['role']).'</small>'; if(!empty($s['image']['url']))echo'</div>'; echo'</footer></blockquote>'; }
}