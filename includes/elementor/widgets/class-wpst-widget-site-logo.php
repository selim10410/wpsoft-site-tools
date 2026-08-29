<?php
if(!defined('ABSPATH'))exit;

class WPST_Widget_Site_Logo extends WPST_Elementor_Widget_Base{
 public function get_name(){return'wpsoft-site-logo';}
 public function get_title(){return'WPSoft · Site Logo';}
 public function get_icon(){return'eicon-site-logo';}
 public function get_categories(){return array('wpsoft-navigation');}
 public function get_keywords(){return array('logo','site logo','header','brand','marka');}

 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'Logo'));
  $this->add_control('source',array(
   'label'=>'Logo Kaynağı','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'site',
   'options'=>array('site'=>'WordPress Site Logosu','custom'=>'Özel Logo')
  ));
  $this->add_control('custom_logo',array(
   'label'=>'Özel Logo','type'=>\Elementor\Controls_Manager::MEDIA,
   'condition'=>array('source'=>'custom')
  ));
  $this->add_control('link_home',array(
   'label'=>'Anasayfaya Bağla','type'=>\Elementor\Controls_Manager::SWITCHER,
   'return_value'=>'yes','default'=>'yes'
  ));
  $this->add_control('fallback_title',array(
   'label'=>'Logo Yoksa Site Adını Göster','type'=>\Elementor\Controls_Manager::SWITCHER,
   'return_value'=>'yes','default'=>'yes'
  ));
  $this->end_controls_section();

  $this->start_controls_section('style',array('label'=>'Biçim','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_responsive_control('logo_width',array(
   'label'=>'Logo Genişliği','type'=>\Elementor\Controls_Manager::SLIDER,
   'range'=>array('px'=>array('min'=>30,'max'=>360)),
   'default'=>array('unit'=>'px','size'=>148),
   'tablet_default'=>array('unit'=>'px','size'=>132),
   'mobile_default'=>array('unit'=>'px','size'=>116),
   'selectors'=>array('{{WRAPPER}} .wpst-site-logo img'=>'width:{{SIZE}}{{UNIT}};')
  ));
  $this->add_responsive_control('max_height',array(
   'label'=>'Maks. Logo Yüksekliği','type'=>\Elementor\Controls_Manager::SLIDER,
   'range'=>array('px'=>array('min'=>20,'max'=>160)),
   'default'=>array('unit'=>'px','size'=>54),
   'tablet_default'=>array('unit'=>'px','size'=>48),
   'mobile_default'=>array('unit'=>'px','size'=>44),
   'selectors'=>array('{{WRAPPER}} .wpst-site-logo img'=>'max-height:{{SIZE}}{{UNIT}};')
  ));
  $this->add_responsive_control('align',array(
   'label'=>'Hizalama','type'=>\Elementor\Controls_Manager::CHOOSE,'default'=>'left',
   'options'=>array(
    'flex-start'=>array('title'=>'Sol','icon'=>'eicon-h-align-left'),
    'center'=>array('title'=>'Orta','icon'=>'eicon-h-align-center'),
    'flex-end'=>array('title'=>'Sağ','icon'=>'eicon-h-align-right')
   ),
   'selectors'=>array('{{WRAPPER}} .wpst-site-logo'=>'justify-content:{{VALUE}};')
  ));
  $this->add_control('title_color',array(
   'label'=>'Site Adı Rengi','type'=>\Elementor\Controls_Manager::COLOR,
   'selectors'=>array('{{WRAPPER}} .wpst-site-logo-title'=>'color:{{VALUE}};')
  ));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array(
   'name'=>'title_typography','selector'=>'{{WRAPPER}} .wpst-site-logo-title'
  ));
  $this->end_controls_section();

  $this->standard_responsive_controls();
 }

 protected function render(){
  $s=$this->get_settings_for_display();
  $image_id=0;
  $image_url='';

  if('custom'===($s['source']??'site') && !empty($s['custom_logo']['url'])){
   $image_url=esc_url($s['custom_logo']['url']);
   $image_id=!empty($s['custom_logo']['id'])?absint($s['custom_logo']['id']):0;
  }else{
   $image_id=(int)get_theme_mod('custom_logo');
   if($image_id){
    $src=wp_get_attachment_image_src($image_id,'full');
    if($src)$image_url=$src[0];
   }
  }

  $home=home_url('/');
  $tag=('yes'===($s['link_home']??'yes'))?'a':'div';
  $attrs=('a'===$tag)?' href="'.esc_url($home).'"':'';
  echo'<'.$tag.' class="wpst-site-logo"'.$attrs.'>';

  if($image_url){
   if($image_id){
    echo wp_get_attachment_image($image_id,'full',false,array(
     'class'=>'wpst-site-logo-image',
     'alt'=>get_bloginfo('name')
    ));
   }else{
    echo'<img class="wpst-site-logo-image" src="'.esc_url($image_url).'" alt="'.esc_attr(get_bloginfo('name')).'">';
   }
  }elseif('yes'===($s['fallback_title']??'yes')){
   echo'<span class="wpst-site-logo-title">'.esc_html(get_bloginfo('name')).'</span>';
  }

  echo'</'.$tag.'>';
 }
}
