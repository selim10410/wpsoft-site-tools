<?php
if(!defined('ABSPATH'))exit;
class WPST_Widget_Mega_Quicknav extends WPST_Elementor_Widget_Base{
 public function get_name(){return'wpsoft-mega-quicknav';}
 public function get_title(){return'WPSoft Mega · Quick Nav 2.0';}
 public function get_icon(){return'eicon-menu-bar';}
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'Hızlı Navigasyon'));
  $this->wpst_signature_preset_control();
  $r=new \Elementor\Repeater();
  $r->add_control('wpst_icon',array('label'=>'WPSoft Icon','type'=>\Elementor\Controls_Manager::SELECT2,'options'=>class_exists('WPST_Icon_Library')?WPST_Icon_Library::options():array(),'default'=>'sparkles'));
  $r->add_control('icon',array('label'=>'Eski Simge','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'✦'));
  $r->add_control('title',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Yeni'));
  $r->add_control('text',array('label'=>'Kısa Açıklama','type'=>\Elementor\Controls_Manager::TEXT,'default'=>''));
  $r->add_control('url',array('label'=>'Bağlantı','type'=>\Elementor\Controls_Manager::URL,'default'=>array('url'=>'#')));
  $this->add_control('items',array('label'=>'Öğeler','type'=>\Elementor\Controls_Manager::REPEATER,'fields'=>$r->get_controls(),'default'=>array(
    array('wpst_icon'=>'sparkles','icon'=>'✦','title'=>'Yeni','text'=>'Yeni içerikler','url'=>array('url'=>'#')),
    array('wpst_icon'=>'star','icon'=>'★','title'=>'Popüler','text'=>'En çok ziyaret edilenler','url'=>array('url'=>'#')),
    array('wpst_icon'=>'arrow-up-right','icon'=>'↗','title'=>'Kampanyalar','text'=>'Güncel fırsatlar','url'=>array('url'=>'#')),
    array('wpst_icon'=>'message','icon'=>'◎','title'=>'Destek','text'=>'Yardım alın','url'=>array('url'=>'#'))
  ),'title_field'=>'{{{ title }}}'));
  $this->add_control('layout',array('label'=>'Yerleşim','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'tiles','options'=>array('tiles'=>'Tiles','rows'=>'Rows','compact'=>'Compact','minimal'=>'Minimal'),'prefix_class'=>'wpst-mega-quicknav-layout-'));
  $this->add_responsive_control('columns',array('label'=>'Kolon','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'4','tablet_default'=>'2','mobile_default'=>'1','options'=>array('1'=>'1','2'=>'2','3'=>'3','4'=>'4'),'selectors'=>array('{{WRAPPER}} .wpst-ew-mega-quicknav'=>'grid-template-columns:repeat({{VALUE}},minmax(0,1fr))!important;')));
  $this->end_controls_section();
  $this->start_controls_section('nav_style',array('label'=>'Navigasyon Biçimi','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('card_bg',array('label'=>'Öğe Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-mega-quicknav>a'=>'background:{{VALUE}};')));
  $this->add_control('icon_bg',array('label'=>'Icon Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-mega-quicknav>a>i'=>'background:{{VALUE}};')));
  $this->add_control('icon_color',array('label'=>'Icon Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-mega-quicknav>a>i'=>'color:{{VALUE}};')));
  $this->add_responsive_control('gap',array('label'=>'Öğe Aralığı','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>50)),'selectors'=>array('{{WRAPPER}} .wpst-ew-mega-quicknav'=>'gap:{{SIZE}}px;')));
  $this->add_responsive_control('radius',array('label'=>'Köşe','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>50)),'selectors'=>array('{{WRAPPER}} .wpst-ew-mega-quicknav>a'=>'border-radius:{{SIZE}}px;')));
  $this->end_controls_section();
  $this->standard_responsive_controls();
 }
 protected function render(){
  $s=$this->get_settings_for_display(); echo'<nav class="wpst-ew-mega-quicknav">';
  foreach((array)$s['items'] as $i){$url=!empty($i['url']['url'])?$i['url']['url']:'#';echo'<a href="'.esc_url($url).'"><i>';if(!empty($i['wpst_icon'])&&class_exists('WPST_Icon_Library'))WPST_Icon_Library::render($i['wpst_icon']);else echo esc_html($i['icon']);echo'</i><span><strong>'.esc_html($i['title']).'</strong>';if(!empty($i['text']))echo'<small>'.esc_html($i['text']).'</small>';echo'</span></a>';} echo'</nav>';
 }
}