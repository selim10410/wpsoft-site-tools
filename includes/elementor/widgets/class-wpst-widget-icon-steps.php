<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class WPST_Widget_Icon_Steps extends WPST_Elementor_Widget_Base {
 public function get_name(){return 'wpsoft-icon-steps';}
 public function get_title(){return 'WPSoft Simge Süreç';}
 public function get_icon(){return 'eicon-time-line';}
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'Süreç'));
  $r=new \Elementor\Repeater();
  $r->add_control('wpst_icon',array('label'=>'WPSoft Icon','type'=>\Elementor\Controls_Manager::SELECT2,'options'=>class_exists('WPST_Icon_Library')?WPST_Icon_Library::options():array(),'default'=>'search','label_block'=>true));$r->add_control('icon',array('label'=>'Elementor Icon (Eski)','type'=>\Elementor\Controls_Manager::ICONS,'default'=>array('value'=>'fas fa-search','library'=>'fa-solid')));
  $r->add_control('title',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Analiz'));
  $r->add_control('text',array('label'=>'Açıklama','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'İhtiyacı belirliyoruz.'));
  $this->add_control('items',array('label'=>'Adımlar','type'=>\Elementor\Controls_Manager::REPEATER,'fields'=>$r->get_controls(),'default'=>array(
   array('title'=>'Analiz','text'=>'İhtiyaçları belirliyoruz.'),
   array('title'=>'Tasarım','text'=>'Modern arayüzü oluşturuyoruz.'),
   array('title'=>'Yayın','text'=>'Projeyi yayına alıyoruz.')
  ),'title_field'=>'{{{ title }}}'));
  $this->wpst_signature_preset_control();
  $this->add_control('layout',array('label'=>'Süreç Düzeni','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'cards','options'=>array('cards'=>'Cards','line'=>'Connected Line','minimal'=>'Minimal','editorial'=>'Editorial'),'prefix_class'=>'wpst-icon-steps-layout-'));
  $this->add_control('show_numbers',array('label'=>'Adım Numaraları','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes'));
  $this->end_controls_section();
  $this->start_controls_section('steps_style',array('label'=>'Süreç Biçimi','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('accent',array('label'=>'Vurgu Rengi','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#315cf5','selectors'=>array('{{WRAPPER}} .wpst-ew-icon-steps'=>'--wpst-step-accent:{{VALUE}};')));
  $this->add_control('line_color',array('label'=>'Bağlantı Çizgisi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-icon-steps'=>'--wpst-step-line:{{VALUE}};')));
  $this->add_responsive_control('gap',array('label'=>'Adım Aralığı','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>8,'max'=>70)),'selectors'=>array('{{WRAPPER}} .wpst-ew-icon-steps'=>'gap:{{SIZE}}px;')));
  $this->add_responsive_control('icon_size',array('label'=>'Icon Alanı','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>36,'max'=>100)),'selectors'=>array('{{WRAPPER}} .wpst-ew-icon-steps-icon'=>'width:{{SIZE}}px;height:{{SIZE}}px;')));
  $this->add_responsive_control('radius',array('label'=>'Kart Köşe','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>50)),'selectors'=>array('{{WRAPPER}} .wpst-ew-icon-steps article'=>'border-radius:{{SIZE}}px;')));
  $this->end_controls_section();
 
        $this->standard_responsive_controls();
    }
 protected function render(){ $s=$this->get_settings_for_display(); echo '<div class="wpst-ew-icon-steps">'; $n=1; foreach((array)$s['items'] as $i){ echo '<article>'; if('yes'===$s['show_numbers'])echo'<span class="wpst-ew-step-no">'.str_pad((string)$n,2,'0',STR_PAD_LEFT).'</span>'; echo'<div class="wpst-ew-icon-steps-icon">'; if(!empty($i['wpst_icon'])&&class_exists('WPST_Icon_Library'))WPST_Icon_Library::render($i['wpst_icon']);else \Elementor\Icons_Manager::render_icon($i['icon'],array('aria-hidden'=>'true')); echo '</div><h3>'.esc_html($i['title']).'</h3><p>'.esc_html($i['text']).'</p></article>'; $n++; } echo '</div>'; }
}