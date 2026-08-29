<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class WPST_Widget_Icon_List extends WPST_Elementor_Widget_Base {
 public function get_name(){return 'wpsoft-icon-list';}
 public function get_title(){return 'WPSoft Simge Listesi';}
 public function get_icon(){return 'eicon-editor-list-ul';}
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'Liste'));
  $r=new \Elementor\Repeater();
  $r->add_control('wpst_icon',array('label'=>'WPSoft Icon','type'=>\Elementor\Controls_Manager::SELECT2,'options'=>class_exists('WPST_Icon_Library')?WPST_Icon_Library::options():array(),'default'=>'check-circle','label_block'=>true));$r->add_control('icon',array('label'=>'Elementor Icon (Eski)','type'=>\Elementor\Controls_Manager::ICONS,'default'=>array('value'=>'fas fa-check','library'=>'fa-solid')));
  $r->add_control('title',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Hızlı Teslimat'));
  $r->add_control('text',array('label'=>'Açıklama','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'Kısa açıklama metni.'));
  $this->add_control('items',array('label'=>'Öğeler','type'=>\Elementor\Controls_Manager::REPEATER,'fields'=>$r->get_controls(),'default'=>array(
   array('title'=>'Modern Tasarım','text'=>'Güncel arayüz yaklaşımı.'),
   array('title'=>'Mobil Uyumlu','text'=>'Tüm cihazlarda güçlü deneyim.'),
   array('title'=>'Hızlı Altyapı','text'=>'Performans odaklı yapı.')
  ),'title_field'=>'{{{ title }}}'));
  $this->wpst_signature_preset_control();
  $this->add_control('layout',array('label'=>'Liste Düzeni','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'cards','options'=>array('cards'=>'Cards','minimal'=>'Minimal','rows'=>'Rows','compact'=>'Compact'),'prefix_class'=>'wpst-icon-list-layout-'));
  $this->add_responsive_control('columns',array('label'=>'Kolon','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'1','tablet_default'=>'1','mobile_default'=>'1','options'=>array('1'=>'1','2'=>'2','3'=>'3'),'selectors'=>array('{{WRAPPER}} .wpst-ew-icon-list'=>'grid-template-columns:repeat({{VALUE}},minmax(0,1fr))!important;')));
  $this->end_controls_section();
  $this->start_controls_section('icon_list_style',array('label'=>'Liste Biçimi','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('icon_bg',array('label'=>'Icon Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-icon-list-icon'=>'background:{{VALUE}};')));
  $this->add_control('icon_color',array('label'=>'Icon Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-icon-list-icon'=>'color:{{VALUE}};')));
  $this->add_responsive_control('gap',array('label'=>'Öğe Aralığı','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>50)),'selectors'=>array('{{WRAPPER}} .wpst-ew-icon-list'=>'gap:{{SIZE}}px;')));
  $this->add_responsive_control('padding',array('label'=>'Öğe İç Boşluk','type'=>\Elementor\Controls_Manager::DIMENSIONS,'size_units'=>array('px'),'selectors'=>array('{{WRAPPER}} .wpst-ew-icon-list article'=>'padding:{{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;')));
  $this->add_responsive_control('radius',array('label'=>'Öğe Köşe','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>50)),'selectors'=>array('{{WRAPPER}} .wpst-ew-icon-list article'=>'border-radius:{{SIZE}}px;')));
  $this->end_controls_section();
 
        $this->standard_responsive_controls();
    }
 protected function render(){ $s=$this->get_settings_for_display(); echo '<div class="wpst-ew-icon-list">'; foreach((array)$s['items'] as $i){ echo '<article><div class="wpst-ew-icon-list-icon">'; if(!empty($i['wpst_icon'])&&class_exists('WPST_Icon_Library'))WPST_Icon_Library::render($i['wpst_icon']);else \Elementor\Icons_Manager::render_icon($i['icon'],array('aria-hidden'=>'true')); echo '</div><div><h3>'.esc_html($i['title']).'</h3><p>'.esc_html($i['text']).'</p></div></article>'; } echo '</div>'; }
}