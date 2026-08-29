<?php
if(!defined('ABSPATH'))exit;

class WPST_Widget_Booking_Strip extends WPST_Elementor_Widget_Base{
 public function get_name(){return'wpsoft-booking-strip';}
 public function get_title(){return'WPSoft Booking Strip 2.0';}
 public function get_icon(){return'eicon-calendar';}

 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'İçerik · Rezervasyon'));
  $this->add_control('checkin',array('label'=>'Giriş Değeri','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Bugün'));
  $this->add_control('checkout',array('label'=>'Çıkış Değeri','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Yarın'));
  $this->add_control('guests',array('label'=>'Misafir Değeri','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'2 Misafir'));
  $this->add_control('checkin_label',array('label'=>'Giriş Etiketi','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'CHECK-IN'));
  $this->add_control('checkout_label',array('label'=>'Çıkış Etiketi','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'CHECK-OUT'));
  $this->add_control('guests_label',array('label'=>'Misafir Etiketi','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'MİSAFİR'));
  $this->add_control('checkin_icon',array('label'=>'Giriş İkonu','type'=>\Elementor\Controls_Manager::SELECT2,'options'=>class_exists('WPST_Icon_Library')?WPST_Icon_Library::options():array(),'default'=>'calendar'));
  $this->add_control('checkout_icon',array('label'=>'Çıkış İkonu','type'=>\Elementor\Controls_Manager::SELECT2,'options'=>class_exists('WPST_Icon_Library')?WPST_Icon_Library::options():array(),'default'=>'calendar'));
  $this->add_control('guests_icon',array('label'=>'Misafir İkonu','type'=>\Elementor\Controls_Manager::SELECT2,'options'=>class_exists('WPST_Icon_Library')?WPST_Icon_Library::options():array(),'default'=>'users'));
  $this->add_control('show_icons',array('label'=>'İkonları Göster','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes'));
  $this->link_controls('button','Buton');
  $this->end_controls_section();

  $this->start_controls_section('layout',array('label'=>'Düzen'));
  $this->add_control('layout_variant',array(
   'label'=>'Yerleşim',
   'type'=>\Elementor\Controls_Manager::SELECT,
   'default'=>'horizontal',
   'options'=>array(
    'horizontal'=>'Yatay',
    'cards'=>'Kartlı',
    'compact'=>'Compact',
    'dark'=>'Dark Bar'
   ),
   'prefix_class'=>'wpst-booking-layout-'
  ));
  $this->add_responsive_control('gap',array(
   'label'=>'Alan Aralığı',
   'type'=>\Elementor\Controls_Manager::SLIDER,
   'range'=>array('px'=>array('min'=>0,'max'=>48)),
   'default'=>array('size'=>8),
   'selectors'=>array('{{WRAPPER}}'=>'--wpst-booking-gap:{{SIZE}}px;')
  ));
  $this->add_responsive_control('min_height',array(
   'label'=>'Minimum Yükseklik',
   'type'=>\Elementor\Controls_Manager::SLIDER,
   'range'=>array('px'=>array('min'=>52,'max'=>140)),
   'default'=>array('size'=>72),
   'selectors'=>array('{{WRAPPER}}'=>'--wpst-booking-h:{{SIZE}}px;')
  ));
  $this->add_control('stack_tablet',array(
   'label'=>'Tablette Alt Alta',
   'type'=>\Elementor\Controls_Manager::SWITCHER,
   'return_value'=>'yes',
   'default'=>''
  ));
  $this->end_controls_section();

  $this->start_controls_section('surface',array('label'=>'Biçim · Yüzey','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->wpst_signature_preset_control('booking_preset');
  $this->add_control('surface_bg',array('label'=>'Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}}'=>'--wpst-booking-bg:{{VALUE}};')));
  $this->add_control('surface_border',array('label'=>'Border Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}}'=>'--wpst-booking-border:{{VALUE}};')));
  $this->add_responsive_control('radius',array(
   'label'=>'Köşe',
   'type'=>\Elementor\Controls_Manager::SLIDER,
   'range'=>array('px'=>array('min'=>0,'max'=>60)),
   'default'=>array('size'=>22),
   'selectors'=>array('{{WRAPPER}}'=>'--wpst-booking-radius:{{SIZE}}px;')
  ));
  $this->add_responsive_control('padding',array(
   'label'=>'Dış İç Boşluk',
   'type'=>\Elementor\Controls_Manager::DIMENSIONS,
   'size_units'=>array('px'),
   'selectors'=>array('{{WRAPPER}} .wpst-ew-booking-strip'=>'padding:{{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;')
  ));
  $this->add_control('shadow',array(
   'label'=>'Gölge',
   'type'=>\Elementor\Controls_Manager::SELECT,
   'default'=>'soft',
   'options'=>array('none'=>'Yok','soft'=>'Soft','medium'=>'Medium'),
   'prefix_class'=>'wpst-booking-shadow-'
  ));
  $this->end_controls_section();

  $this->start_controls_section('field_style',array('label'=>'Biçim · Alanlar','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('field_bg',array('label'=>'Alan Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}}'=>'--wpst-booking-field-bg:{{VALUE}};')));
  $this->add_control('field_border',array('label'=>'Alan Border','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}}'=>'--wpst-booking-field-border:{{VALUE}};')));
  $this->add_responsive_control('field_radius',array(
   'label'=>'Alan Köşesi',
   'type'=>\Elementor\Controls_Manager::SLIDER,
   'range'=>array('px'=>array('min'=>0,'max'=>40)),
   'default'=>array('size'=>16),
   'selectors'=>array('{{WRAPPER}}'=>'--wpst-booking-field-radius:{{SIZE}}px;')
  ));
  $this->add_responsive_control('field_padding',array(
   'label'=>'Alan İç Boşluk',
   'type'=>\Elementor\Controls_Manager::DIMENSIONS,
   'size_units'=>array('px'),
   'selectors'=>array('{{WRAPPER}} .wpst-booking-field'=>'padding:{{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;')
  ));
  $this->add_control('icon_bg',array('label'=>'İkon Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}}'=>'--wpst-booking-icon-bg:{{VALUE}};')));
  $this->add_control('icon_color',array('label'=>'İkon Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}}'=>'--wpst-booking-icon-color:{{VALUE}};')));
  $this->add_responsive_control('icon_size',array(
   'label'=>'İkon Boyutu',
   'type'=>\Elementor\Controls_Manager::SLIDER,
   'range'=>array('px'=>array('min'=>14,'max'=>40)),
   'default'=>array('size'=>18),
   'selectors'=>array('{{WRAPPER}}'=>'--wpst-booking-icon-size:{{SIZE}}px;')
  ));
  $this->end_controls_section();

  $this->start_controls_section('text_style',array('label'=>'Biçim · Yazılar','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('label_color',array('label'=>'Etiket Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}}'=>'--wpst-booking-label:{{VALUE}};')));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'label_typography','label'=>'Etiket Tipografi','selector'=>'{{WRAPPER}} .wpst-booking-field small'));
  $this->add_control('value_color',array('label'=>'Değer Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}}'=>'--wpst-booking-value:{{VALUE}};')));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'value_typography','label'=>'Değer Tipografi','selector'=>'{{WRAPPER}} .wpst-booking-field b'));
  $this->end_controls_section();

  $this->start_controls_section('button_style',array('label'=>'Biçim · Buton','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('button_bg',array('label'=>'Buton Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}}'=>'--wpst-booking-btn-bg:{{VALUE}};')));
  $this->add_control('button_color',array('label'=>'Buton Yazı Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}}'=>'--wpst-booking-btn-color:{{VALUE}};')));
  $this->add_control('button_hover_bg',array('label'=>'Hover Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}}'=>'--wpst-booking-btn-hover-bg:{{VALUE}};')));
  $this->add_control('button_hover_color',array('label'=>'Hover Yazı Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}}'=>'--wpst-booking-btn-hover-color:{{VALUE}};')));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'button_typography','label'=>'Buton Tipografi','selector'=>'{{WRAPPER}} .wpst-booking-button'));
  $this->add_responsive_control('button_radius',array(
   'label'=>'Buton Köşesi',
   'type'=>\Elementor\Controls_Manager::SLIDER,
   'range'=>array('px'=>array('min'=>0,'max'=>40)),
   'default'=>array('size'=>16),
   'selectors'=>array('{{WRAPPER}}'=>'--wpst-booking-btn-radius:{{SIZE}}px;')
  ));
  $this->end_controls_section();

  $this->standard_responsive_controls();
 }

 private function field_html($icon,$label,$value,$show_icons){
  $html='<div class="wpst-booking-field">';
  if($show_icons && !empty($icon) && class_exists('WPST_Icon_Library')){
   $html.='<span class="wpst-booking-icon">'.WPST_Icon_Library::svg($icon,array('size'=>18)).'</span>';
  }
  $html.='<span class="wpst-booking-copy"><small>'.esc_html($label).'</small><b>'.esc_html($value).'</b></span></div>';
  return $html;
 }

 protected function render(){
  $s=$this->get_settings_for_display();
  $show_icons='yes'===($s['show_icons']??'yes');

  echo'<div class="wpst-ew-booking-strip">';
  echo $this->field_html($s['checkin_icon']??'calendar',$s['checkin_label']??'',$s['checkin']??'',$show_icons);
  echo $this->field_html($s['checkout_icon']??'calendar',$s['checkout_label']??'',$s['checkout']??'',$show_icons);
  echo $this->field_html($s['guests_icon']??'users',$s['guests_label']??'',$s['guests']??'',$show_icons);

  if(!empty($s['button_text'])){
   echo'<a class="wpst-booking-button"'.$this->render_link_attrs($s['button_url']).'>';
   echo'<span>'.esc_html($s['button_text']).'</span>';
   echo'<i class="wpst-native-arrow">'.(class_exists('WPST_Icon_Library')?WPST_Icon_Library::svg('arrow-right',array('size'=>15)):'→').'</i>';
   echo'</a>';
  }
  echo'</div>';
 }
}
