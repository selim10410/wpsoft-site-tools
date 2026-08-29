<?php
if(!defined('ABSPATH'))exit;
class WPST_Widget_Tabs_Modern extends WPST_Elementor_Widget_Base{
 public function get_name(){return'wpsoft-tabs-modern';}
 public function get_title(){return'WPSoft · Tabs 2.0';}
 public function get_icon(){return'eicon-tabs';}
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'Sekmeler'));
  $this->wpst_signature_preset_control();
  $r=new \Elementor\Repeater();
  $r->add_control('wpst_icon',array('label'=>'WPSoft Icon','type'=>\Elementor\Controls_Manager::SELECT2,'options'=>class_exists('WPST_Icon_Library')?WPST_Icon_Library::options():array(),'default'=>'sparkles','label_block'=>true));
  $r->add_control('title',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Web Tasarım'));
  $r->add_control('text',array('label'=>'İçerik','type'=>\Elementor\Controls_Manager::WYSIWYG,'default'=>'Modern ve hızlı çözümler.'));
  $this->add_control('items',array(
   'label'=>'Sekmeler','type'=>\Elementor\Controls_Manager::REPEATER,'fields'=>$r->get_controls(),
   'default'=>array(
    array('wpst_icon'=>'monitor','title'=>'Web Tasarım','text'=>'Modern kurumsal web çözümleri.'),
    array('wpst_icon'=>'cart','title'=>'E-Ticaret','text'=>'Satış odaklı altyapı.'),
    array('wpst_icon'=>'chart','title'=>'SEO','text'=>'Organik görünürlük.')
   ),
   'title_field'=>'{{{ title }}}'
  ));
  $this->add_control('style_preset',array('label'=>'Stil','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'pill','options'=>array('pill'=>'Pill','line'=>'Line','cards'=>'Cards','vertical'=>'Vertical'),'prefix_class'=>'wpst-tabs-style-'));
  $this->add_control('layout_variant',array(
   'label'=>'Sekme Kompozisyonu','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'classic',
   'options'=>array('classic'=>'Classic','sidebar'=>'Sidebar','segmented'=>'Segmented','editorial'=>'Editorial','compact'=>'Compact'),
   'prefix_class'=>'wpst-tabs-layout-'
  ));
  $this->end_controls_section();

  $this->start_controls_section('style',array('label'=>'Biçim','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('accent',array('label'=>'Vurgu','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#2563eb','selectors'=>array('{{WRAPPER}} .wpst-ew-tabs'=>'--tabs-accent:{{VALUE}};')));
  $this->add_control('nav_bg',array('label'=>'Navigasyon Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-tabs'=>'--tabs-nav-bg:{{VALUE}};')));
  $this->add_control('panel_bg',array('label'=>'İçerik Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-tabs'=>'--tabs-panel-bg:{{VALUE}};')));
  $this->add_responsive_control('radius',array('label'=>'Köşe','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>40)),'default'=>array('size'=>16),'selectors'=>array('{{WRAPPER}} .wpst-ew-tabs-nav,{{WRAPPER}} .wpst-ew-tabs-panels article'=>'border-radius:{{SIZE}}px;')));
  $this->end_controls_section();
  $this->standard_responsive_controls();
 }
 protected function render(){
  $s=$this->get_settings_for_display();
  $uid='wpst-tabs-'.$this->get_id();
  echo'<div class="wpst-ew-tabs" data-wpst-tabs><div class="wpst-ew-tabs-nav" role="tablist">';
  $n=0;
  foreach((array)$s['items'] as $i){
   $tab=$uid.'-tab-'.$n;$panel=$uid.'-panel-'.$n;
   echo'<button id="'.esc_attr($tab).'" type="button" role="tab" aria-controls="'.esc_attr($panel).'" aria-selected="'.($n===0?'true':'false').'" tabindex="'.($n===0?'0':'-1').'" class="'.($n===0?'is-active':'').'" data-index="'.$n.'"><i>'.(class_exists('WPST_Icon_Library')?WPST_Icon_Library::svg($i['wpst_icon'],array('size'=>15)):'').'</i><span>'.esc_html($i['title']).'</span></button>';
   $n++;
  }
  echo'</div><div class="wpst-ew-tabs-panels">';
  $n=0;
  foreach((array)$s['items'] as $i){
   $tab=$uid.'-tab-'.$n;$panel=$uid.'-panel-'.$n;
   echo'<article id="'.esc_attr($panel).'" role="tabpanel" aria-labelledby="'.esc_attr($tab).'" class="'.($n===0?'is-active':'').'" data-index="'.$n.'" '.($n===0?'':'hidden').'><h3>'.esc_html($i['title']).'</h3><div>'.wp_kses_post($i['text']).'</div></article>';
   $n++;
  }
  echo'</div></div>';
 }
}
