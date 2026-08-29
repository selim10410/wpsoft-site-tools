<?php
if(!defined('ABSPATH'))exit;
class WPST_Widget_Icon_Orbit extends WPST_Elementor_Widget_Base{
 public function get_name(){return'wpsoft-icon-orbit';}
 public function get_title(){return'WPSoft Simge Orbit 2.0';}
 public function get_icon(){return'eicon-globe';}
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'İçerik'));
  $this->wpst_signature_preset_control();
  $this->add_control('center',array('label'=>'Merkez Metni','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'WPSoft'));
  $r=new \Elementor\Repeater();
  $r->add_control('wpst_icon',array('label'=>'WPSoft Icon','type'=>\Elementor\Controls_Manager::SELECT2,'options'=>class_exists('WPST_Icon_Library')?WPST_Icon_Library::options():array(),'default'=>'sparkles','label_block'=>true));
  $r->add_control('icon',array('label'=>'Elementor Icon (Eski)','type'=>\Elementor\Controls_Manager::ICONS,'default'=>array('value'=>'fas fa-bolt','library'=>'fa-solid')));
  $r->add_control('title',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Hızlı'));
  $this->add_control('items',array('label'=>'Simgeler','type'=>\Elementor\Controls_Manager::REPEATER,'fields'=>$r->get_controls(),'default'=>array(array('title'=>'Hızlı'),array('title'=>'Güvenli'),array('title'=>'Modern'),array('title'=>'Esnek'),array('title'=>'Mobil'),array('title'=>'SEO')),'title_field'=>'{{{ title }}}'));
  $this->add_control('layout',array('label'=>'Orbit Düzeni','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'circle','options'=>array('circle'=>'Circle','ellipse'=>'Ellipse','compact'=>'Compact'),'prefix_class'=>'wpst-orbit-layout-'));
  $this->add_control('rotation',array('label'=>'Otomatik Dönüş','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes','prefix_class'=>'wpst-orbit-rotate-'));
  $this->end_controls_section();
  $this->start_controls_section('style',array('label'=>'Orbit Biçimi','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_responsive_control('size',array('label'=>'Orbit Boyutu','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>240,'max'=>900)),'default'=>array('size'=>520),'selectors'=>array('{{WRAPPER}} .wpst-ew-icon-orbit'=>'--wpst-orbit-size:{{SIZE}}px;')));
  $this->add_responsive_control('icon_size',array('label'=>'Icon Boyutu','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>12,'max'=>60)),'selectors'=>array('{{WRAPPER}} .wpst-ew-orbit-item svg'=>'width:{{SIZE}}px;height:{{SIZE}}px;')));
  $this->add_control('accent',array('label'=>'Vurgu','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#6366f1','selectors'=>array('{{WRAPPER}} .wpst-ew-icon-orbit'=>'--wpst-orbit-accent:{{VALUE}};')));
  $this->add_control('center_bg',array('label'=>'Merkez Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-orbit-center'=>'background:{{VALUE}};')));
  $this->end_controls_section();
  $this->standard_responsive_controls();
 }
 protected function render(){ $s=$this->get_settings_for_display();echo'<div class="wpst-ew-icon-orbit"><div class="wpst-ew-orbit-center">'.esc_html($s['center']).'</div><div class="wpst-ew-orbit-ring">';$n=count((array)$s['items']);$i=0;foreach((array)$s['items'] as $it){$angle=$n?($i*360/$n):0;echo'<div class="wpst-ew-orbit-item" style="--wpst-angle:'.esc_attr($angle).'deg"><div>'; if(!empty($it['wpst_icon'])&&class_exists('WPST_Icon_Library'))WPST_Icon_Library::render($it['wpst_icon']);else \Elementor\Icons_Manager::render_icon($it['icon'],array('aria-hidden'=>'true'));echo'<span>'.esc_html($it['title']).'</span></div></div>';$i++;}echo'</div></div>'; }
}