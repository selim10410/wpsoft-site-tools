<?php
if(!defined('ABSPATH'))exit;
class WPST_Widget_Native_Icon extends WPST_Elementor_Widget_Base{
 public function get_name(){return'wpsoft-native-icon';}
 public function get_title(){return'WPSoft · Native Icon 2.0';}
 public function get_icon(){return'eicon-star';}
 public function get_keywords(){return array('icon','svg','symbol','badge','wpsoft');}
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'Icon'));
  $this->wpst_signature_preset_control();
  $this->add_control('icon',array('label'=>'WPSoft Icon','type'=>\Elementor\Controls_Manager::SELECT2,'options'=>class_exists('WPST_Icon_Library')?WPST_Icon_Library::options():array(),'default'=>'sparkles','label_block'=>true));
  $this->add_control('link',array('label'=>'Bağlantı','type'=>\Elementor\Controls_Manager::URL,'placeholder'=>'https://','show_external'=>true,'dynamic'=>array('active'=>true)));
  $this->add_control('aria_label',array('label'=>'Erişilebilirlik Etiketi','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'','placeholder'=>'Örn. Instagram'));
  $this->add_control('hover_motion',array('label'=>'Hover Hareketi','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'lift','options'=>array('none'=>'Yok','lift'=>'Yüksel','scale'=>'Büyüt','rotate'=>'Döndür'),'prefix_class'=>'wpst-native-motion-'));
  $this->end_controls_section();

  $this->start_controls_section('style',array('label'=>'Icon Biçimi','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('color',array('label'=>'Renk','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-native-icon'=>'--wpst-native-color:{{VALUE}};')));
  $this->add_control('hover_color',array('label'=>'Hover Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-native-icon'=>'--wpst-native-hover-color:{{VALUE}};')));
  $this->add_control('bg',array('label'=>'Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-native-icon'=>'--wpst-native-bg:{{VALUE}};')));
  $this->add_control('hover_bg',array('label'=>'Hover Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-native-icon'=>'--wpst-native-hover-bg:{{VALUE}};')));
  $this->add_control('border',array('label'=>'Kenarlık','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-native-icon'=>'--wpst-native-border:{{VALUE}};')));
  $this->add_responsive_control('size',array('label'=>'Icon Boyutu','type'=>\Elementor\Controls_Manager::SLIDER,'size_units'=>array('px'),'range'=>array('px'=>array('min'=>12,'max'=>160)),'default'=>array('unit'=>'px','size'=>34),'selectors'=>array('{{WRAPPER}} .wpst-native-icon svg'=>'width:{{SIZE}}{{UNIT}};height:{{SIZE}}{{UNIT}};')));
  $this->add_responsive_control('box',array('label'=>'Kutu Boyutu','type'=>\Elementor\Controls_Manager::SLIDER,'size_units'=>array('px'),'range'=>array('px'=>array('min'=>24,'max'=>220)),'default'=>array('unit'=>'px','size'=>64),'selectors'=>array('{{WRAPPER}} .wpst-native-icon'=>'width:{{SIZE}}{{UNIT}};height:{{SIZE}}{{UNIT}};')));
  $this->add_responsive_control('radius',array('label'=>'Köşe','type'=>\Elementor\Controls_Manager::SLIDER,'size_units'=>array('px','%'),'range'=>array('px'=>array('min'=>0,'max'=>100),'%'=>array('min'=>0,'max'=>50)),'default'=>array('unit'=>'px','size'=>18),'selectors'=>array('{{WRAPPER}} .wpst-native-icon'=>'border-radius:{{SIZE}}{{UNIT}};')));
  $this->add_control('shadow',array('label'=>'Gölge','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'none','options'=>array('none'=>'Yok','soft'=>'Soft','medium'=>'Medium'),'prefix_class'=>'wpst-native-shadow-'));
  $this->end_controls_section();
  $this->standard_responsive_controls();
 }
 protected function render(){
  $s=$this->get_settings_for_display();
  $tag=!empty($s['link']['url'])?'a':'div';
  $attrs='';
  if('a'===$tag)$attrs=$this->render_link_attrs($s['link']);
  if(!empty($s['aria_label']))$attrs.=' aria-label="'.esc_attr($s['aria_label']).'"';
  elseif('a'===$tag)$attrs.=' aria-label="'.esc_attr(str_replace(array('-','_'),' ',$s['icon'])).'"';
  echo'<'.$tag.' class="wpst-native-icon"'.$attrs.'>';
  if(class_exists('WPST_Icon_Library'))WPST_Icon_Library::render($s['icon']);
  echo'</'.$tag.'>';
 }
}
