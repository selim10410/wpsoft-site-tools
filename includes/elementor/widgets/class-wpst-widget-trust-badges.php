<?php
if(!defined('ABSPATH'))exit;
class WPST_Widget_Trust_Badges extends WPST_Elementor_Widget_Base{
 public function get_name(){return'wpsoft-trust-badges';}
 public function get_title(){return'WPSoft Trust Badges';}
 public function get_icon(){return'eicon-shield';}
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'Güven Rozetleri'));
  $this->wpst_signature_preset_control();
  $r=new \Elementor\Repeater();
  $r->add_control('wpst_icon',array('label'=>'WPSoft Icon','type'=>\Elementor\Controls_Manager::SELECT2,'options'=>class_exists('WPST_Icon_Library')?WPST_Icon_Library::options():array(),'default'=>'check-circle','label_block'=>true)); $r->add_control('icon',array('label'=>'Eski Simge / Değer','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'✓'));
  $r->add_control('title',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Güvenli'));
  $r->add_control('text',array('label'=>'Açıklama','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Güvenilir hizmet'));
  $this->add_control('items',array(
   'label'=>'Rozetler','type'=>\Elementor\Controls_Manager::REPEATER,'fields'=>$r->get_controls(),
   'default'=>array(
    array('icon'=>'✓','title'=>'Güvenli','text'=>'Güvenilir hizmet'),
    array('icon'=>'★','title'=>'Deneyimli','text'=>'Uzman ekip'),
    array('icon'=>'24','title'=>'Destek','text'=>'Hızlı iletişim'),
    array('icon'=>'↗','title'=>'Sonuç','text'=>'Ölçülebilir değer')
   ),
   'title_field'=>'{{{ title }}}'
  ));
  $this->end_controls_section();

  $this->start_controls_section('badge_style',array('label'=>'Rozet Stili','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('icon_color',array('label'=>'Simge Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-trust-badges article>i'=>'color:{{VALUE}}')));
  $this->add_control('title_color',array('label'=>'Başlık Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-trust-badges b'=>'color:{{VALUE}}')));
  $this->add_control('text_color',array('label'=>'Açıklama Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-trust-badges span'=>'color:{{VALUE}}')));
  
  $this->add_control('layout_variant',array(
   'label'=>'Güven Rozeti Yerleşimi','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'grid',
   'options'=>array('grid'=>'Grid','strip'=>'Strip','cards'=>'Cards','compact'=>'Compact'),
   'prefix_class'=>'wpst-trust-badges-layout-'
  ));
  $this->add_responsive_control('columns',array('label'=>'Kolon','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'4','tablet_default'=>'2','mobile_default'=>'2','options'=>array('1'=>'1','2'=>'2','3'=>'3','4'=>'4'),'selectors'=>array('{{WRAPPER}} .wpst-ew-trust-badges'=>'grid-template-columns:repeat({{VALUE}},minmax(0,1fr))!important;')));
  $this->add_responsive_control('gap',array('label'=>'Rozet Aralığı','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>50)),'selectors'=>array('{{WRAPPER}} .wpst-ew-trust-badges'=>'gap:{{SIZE}}px;')));

  $this->end_controls_section();
  $this->standard_responsive_controls();
 }
 protected function render(){
  $s=$this->get_settings_for_display();
  echo '<div class="wpst-ew-trust-badges">';
  foreach((array)$s['items'] as $item){
   echo '<article><i class="wpst-trust-icon">'.((!empty($item['wpst_icon'])&&class_exists('WPST_Icon_Library'))?WPST_Icon_Library::svg($item['wpst_icon'],array('size'=>20)):esc_html($item['icon'])).'</i><div><b>'.esc_html($item['title']).'</b><span>'.esc_html($item['text']).'</span></div></article>';
  }
  echo '</div>';
 }
}
