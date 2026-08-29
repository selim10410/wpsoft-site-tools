<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class WPST_Widget_Icon_Grid extends WPST_Elementor_Widget_Base {
 public function get_name(){return 'wpsoft-icon-grid';}
 public function get_title(){return 'WPSoft Simge Grid 2.0';}
 public function get_icon(){return 'eicon-apps';}
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'Grid'));
  $this->wpst_signature_preset_control();
  $r=new \Elementor\Repeater();
  $r->add_control('wpst_icon',array('label'=>'WPSoft Icon','type'=>\Elementor\Controls_Manager::SELECT2,'options'=>class_exists('WPST_Icon_Library')?WPST_Icon_Library::options():array(),'default'=>'bolt','label_block'=>true));
  $r->add_control('icon',array('label'=>'Elementor Icon (Eski)','type'=>\Elementor\Controls_Manager::ICONS,'default'=>array('value'=>'fas fa-bolt','library'=>'fa-solid')));
  $r->add_control('title',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Özellik'));
  $r->add_control('text',array('label'=>'Açıklama','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'Kısa açıklama.'));
  $this->add_control('items',array('label'=>'Öğeler','type'=>\Elementor\Controls_Manager::REPEATER,'fields'=>$r->get_controls(),'default'=>array(array('title'=>'Hızlı','text'=>'Yüksek performans.'),array('title'=>'Güvenli','text'=>'Güncel altyapı.'),array('title'=>'Modern','text'=>'Yeni nesil tasarım.'),array('title'=>'Esnek','text'=>'Kolay geliştirilebilir.')),'title_field'=>'{{{ title }}}'));
  $this->add_control('layout',array('label'=>'Yerleşim','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'cards','options'=>array('cards'=>'Cards','minimal'=>'Minimal','horizontal'=>'Horizontal','numbered'=>'Numbered'),'prefix_class'=>'wpst-icon-grid-layout-'));
  $this->add_responsive_control('columns',array('label'=>'Kolon','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'4','tablet_default'=>'2','mobile_default'=>'1','options'=>array('1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6'),'selectors'=>array('{{WRAPPER}} .wpst-ew-icon-grid'=>'grid-template-columns:repeat({{VALUE}},minmax(0,1fr))!important;')));
  $this->end_controls_section();
  $this->start_controls_section('style',array('label'=>'Grid Biçimi','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('card_bg',array(
   'label'=>'Kart Arka Plan',
   'type'=>\Elementor\Controls_Manager::COLOR,
   'selectors'=>array(
    '{{WRAPPER}} .wpst-ew-icon-grid'=>'--wpst-icon-grid-card-bg:{{VALUE}};'
   )
  ));
  $this->add_control('icon_bg',array('label'=>'Icon Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-icon-grid-icon'=>'background:{{VALUE}};')));
  $this->add_control('icon_color',array('label'=>'Icon Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-icon-grid-icon'=>'color:{{VALUE}};')));
  $this->add_responsive_control('icon_size',array('label'=>'Icon Boyutu','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>14,'max'=>70)),'selectors'=>array('{{WRAPPER}} .wpst-ew-icon-grid-icon svg'=>'width:{{SIZE}}px;height:{{SIZE}}px;')));
  $this->add_responsive_control('gap',array('label'=>'Kart Aralığı','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>60)),'selectors'=>array('{{WRAPPER}} .wpst-ew-icon-grid'=>'gap:{{SIZE}}px;')));
  $this->add_responsive_control('padding',array('label'=>'Kart İç Boşluk','type'=>\Elementor\Controls_Manager::DIMENSIONS,'size_units'=>array('px'),'selectors'=>array('{{WRAPPER}} .wpst-ew-icon-grid article'=>'padding:{{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;')));
  $this->add_responsive_control('radius',array('label'=>'Kart Köşe','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>60)),'selectors'=>array('{{WRAPPER}} .wpst-ew-icon-grid article'=>'border-radius:{{SIZE}}px;')));
  $this->end_controls_section();
  $this->standard_responsive_controls();
 }
 protected function render(){ $s=$this->get_settings_for_display(); echo '<div class="wpst-ew-icon-grid">'; foreach((array)$s['items'] as $idx=>$i){ echo '<article data-index="'.esc_attr($idx+1).'"><div class="wpst-ew-icon-grid-icon">'; if(!empty($i['wpst_icon'])&&class_exists('WPST_Icon_Library'))WPST_Icon_Library::render($i['wpst_icon']);else \Elementor\Icons_Manager::render_icon($i['icon'],array('aria-hidden'=>'true')); echo '</div><h3>'.esc_html($i['title']).'</h3><p>'.esc_html($i['text']).'</p></article>'; } echo '</div>'; }
}