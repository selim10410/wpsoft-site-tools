<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class WPST_Widget_Contact_Cards extends WPST_Elementor_Widget_Base {
 public function get_name(){return 'wpsoft-contact-cards';}
 public function get_title(){return 'WPSoft · Contact Cards 2.0';}
 public function get_icon(){return 'eicon-info-box';}
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'Kartlar'));
  $this->wpst_signature_preset_control();
  $r=new \Elementor\Repeater();
  $r->add_control('wpst_icon',array('label'=>'WPSoft Icon','type'=>\Elementor\Controls_Manager::SELECT2,'options'=>class_exists('WPST_Icon_Library')?WPST_Icon_Library::options():array(),'default'=>'phone','label_block'=>true));
  $r->add_control('label',array('label'=>'Etiket','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Telefon'));
  $r->add_control('value',array('label'=>'Bilgi','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'+90 212 000 00 00'));
  $r->add_control('url',array('label'=>'Bağlantı','type'=>\Elementor\Controls_Manager::URL));
  $this->add_control('items',array(
   'label'=>'İletişim Bilgileri','type'=>\Elementor\Controls_Manager::REPEATER,'fields'=>$r->get_controls(),
   'default'=>array(
    array('wpst_icon'=>'phone','label'=>'Telefon','value'=>'+90 212 000 00 00'),
    array('wpst_icon'=>'mail','label'=>'E-posta','value'=>'info@firma.com'),
    array('wpst_icon'=>'clock','label'=>'Çalışma Saatleri','value'=>'Pzt - Cum / 09:00 - 18:00')
   ),
   'title_field'=>'{{{ label }}}'
  ));
  $this->add_control('style_preset',array('label'=>'Kart Stili','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'clean','options'=>array('clean'=>'Clean','soft'=>'Soft','bordered'=>'Bordered','dark'=>'Dark'),'prefix_class'=>'wpst-contact-cards-style-'));
  $this->add_responsive_control('columns',array('label'=>'Kolon','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'3','tablet_default'=>'2','mobile_default'=>'1','options'=>array('1'=>'1','2'=>'2','3'=>'3','4'=>'4'),'selectors'=>array('{{WRAPPER}} .wpst-ew-contact-cards'=>'grid-template-columns:repeat({{VALUE}},minmax(0,1fr))!important;')));
  $this->add_responsive_control('gap',array('label'=>'Kart Aralığı','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>60)),'default'=>array('size'=>14),'selectors'=>array('{{WRAPPER}} .wpst-ew-contact-cards'=>'gap:{{SIZE}}px;')));
  $this->end_controls_section();

  $this->start_controls_section('style',array('label'=>'Biçim','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('accent',array('label'=>'Vurgu','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#2563eb','selectors'=>array('{{WRAPPER}} .wpst-ew-contact-cards'=>'--contact-accent:{{VALUE}};')));
  $this->add_control('card_bg',array('label'=>'Kart Arka Planı','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-contact-cards'=>'--contact-bg:{{VALUE}};')));
  $this->add_control('label_color',array('label'=>'Etiket Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-contact-cards small'=>'color:{{VALUE}};')));
  $this->add_control('value_color',array('label'=>'Bilgi Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-contact-cards :is(strong,a span)'=>'color:{{VALUE}};')));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'label_typography','label'=>'Etiket Tipografisi','selector'=>'{{WRAPPER}} .wpst-ew-contact-cards small'));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'value_typography','label'=>'Bilgi Tipografisi','selector'=>'{{WRAPPER}} .wpst-ew-contact-cards :is(strong,a span)'));
  $this->add_responsive_control('icon_size',array('label'=>'Icon Alanı','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>30,'max'=>72)),'default'=>array('size'=>44),'selectors'=>array('{{WRAPPER}} .wpst-contact-native-icon'=>'width:{{SIZE}}px;height:{{SIZE}}px;')));
  $this->add_responsive_control('radius',array('label'=>'Köşe','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>50)),'default'=>array('size'=>18),'selectors'=>array('{{WRAPPER}} .wpst-ew-contact-cards article'=>'border-radius:{{SIZE}}px;')));
  $this->add_responsive_control('padding',array('label'=>'Kart İç Boşluk','type'=>\Elementor\Controls_Manager::DIMENSIONS,'size_units'=>array('px'),'selectors'=>array('{{WRAPPER}} .wpst-ew-contact-cards article'=>'padding:{{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;')));
  $this->add_control('border_color',array('label'=>'Kenarlık Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-contact-cards article'=>'border-color:{{VALUE}};')));
  $this->add_control('hover_lift',array('label'=>'Hover Yükselme','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes','prefix_class'=>'wpst-contact-hover-'));
  $this->end_controls_section();
  $this->standard_responsive_controls();
 }
 protected function render(){
  $s=$this->get_settings_for_display();
  echo '<div class="wpst-ew-contact-cards">';
  foreach((array)$s['items'] as $i){
   $url=!empty($i['url']['url'])?$i['url']['url']:'';
   echo '<article><i class="wpst-contact-native-icon">'.(class_exists('WPST_Icon_Library')?WPST_Icon_Library::svg(!empty($i['wpst_icon'])?$i['wpst_icon']:'info',array('size'=>20)):'').'</i><small>'.esc_html($i['label']).'</small>';
   if($url){
    echo '<a href="'.esc_url($url).'"><span>'.esc_html($i['value']).'</span><b>'.(class_exists('WPST_Icon_Library')?WPST_Icon_Library::svg('arrow-up-right',array('size'=>14)):'↗').'</b></a>';
   }else{
    echo '<strong>'.esc_html($i['value']).'</strong>';
   }
   echo '</article>';
  }
  echo '</div>';
 }
}
