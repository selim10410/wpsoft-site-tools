<?php
if(!defined('ABSPATH'))exit;
class WPST_Widget_Parallax_Image extends WPST_Elementor_Widget_Base{
 public function get_name(){return'wpsoft-parallax-image';}
 public function get_title(){return'WPSoft Parallax Görsel 2.0';}
 public function get_icon(){return'eicon-image-rollover';}
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'İçerik'));
  $this->add_control('image',array('label'=>'Görsel','type'=>\Elementor\Controls_Manager::MEDIA));
  $this->add_control('eyebrow',array('label'=>'Üst Etiket','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'KEŞFET'));
  $this->add_control('title',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Modern deneyim'));
  $this->add_control('text',array('label'=>'Açıklama','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'Scroll hareketine tepki veren modern görsel alanı.'));
  $this->add_control('placeholder_text',array('label'=>'Görsel Yoksa Yazı','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'WPSoft'));
  $this->add_control('show_caption',array('label'=>'İçeriği Göster','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes'));
  $this->add_control('strength',array('label'=>'Parallax Gücü','type'=>\Elementor\Controls_Manager::SLIDER,'default'=>array('size'=>28),'range'=>array('px'=>array('min'=>0,'max'=>100))));
  $this->add_control('disable_mobile',array('label'=>'Mobilde Parallax Kapat','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes'));
  $this->end_controls_section();

  $this->start_controls_section('layout',array('label'=>'Düzen'));
  $this->add_control('layout_variant',array('label'=>'Görünüm','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'overlay','options'=>array('overlay'=>'Overlay','glass'=>'Glass Card','editorial'=>'Editorial','minimal'=>'Minimal'),'prefix_class'=>'wpst-parallax-layout-'));
  $this->add_control('caption_position',array('label'=>'İçerik Konumu','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'bottom-left','options'=>array('bottom-left'=>'Sol Alt','bottom-right'=>'Sağ Alt','center'=>'Orta','top-left'=>'Sol Üst'),'prefix_class'=>'wpst-parallax-caption-'));
  $this->add_responsive_control('height',array('label'=>'Görsel Yüksekliği','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>240,'max'=>900)),'default'=>array('size'=>520),'tablet_default'=>array('size'=>440),'mobile_default'=>array('size'=>360),'selectors'=>array('{{WRAPPER}}'=>'--wpst-parallax-height:{{SIZE}}px;')));
  $this->add_control('image_fit',array('label'=>'Görsel Oturtma','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'cover','options'=>array('cover'=>'Kapla','contain'=>'Sığdır'),'selectors'=>array('{{WRAPPER}} .wpst-ew-parallax-image>img'=>'object-fit:{{VALUE}};')));
  $this->add_control('image_position',array('label'=>'Görsel Konumu','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'center','options'=>array('center'=>'Orta','top'=>'Üst','bottom'=>'Alt','left'=>'Sol','right'=>'Sağ'),'selectors'=>array('{{WRAPPER}} .wpst-ew-parallax-image>img'=>'object-position:{{VALUE}};')));
  $this->end_controls_section();

  $this->start_controls_section('surface',array('label'=>'Biçim · Görsel Alanı','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->wpst_signature_preset_control('parallax_preset');
  $this->add_control('surface_bg',array('label'=>'Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}}'=>'--wpst-parallax-bg:{{VALUE}};')));
  $this->add_control('overlay',array('label'=>'Overlay Rengi','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'rgba(2,6,23,.48)','selectors'=>array('{{WRAPPER}}'=>'--wpst-parallax-overlay:{{VALUE}};')));
  $this->add_control('border_color',array('label'=>'Border Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}}'=>'--wpst-parallax-border:{{VALUE}};')));
  $this->add_responsive_control('radius',array('label'=>'Köşe','type'=>\Elementor\Controls_Manager::SLIDER,'default'=>array('size'=>28),'range'=>array('px'=>array('min'=>0,'max'=>70)),'selectors'=>array('{{WRAPPER}}'=>'--wpst-parallax-radius:{{SIZE}}px;')));
  $this->add_control('shadow',array('label'=>'Gölge','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'soft','options'=>array('none'=>'Yok','soft'=>'Soft','medium'=>'Medium'),'prefix_class'=>'wpst-parallax-shadow-'));
  $this->end_controls_section();

  $this->start_controls_section('caption_style',array('label'=>'Biçim · İçerik','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('caption_bg',array('label'=>'İçerik Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}}'=>'--wpst-parallax-caption-bg:{{VALUE}};')));
  $this->add_control('eyebrow_color',array('label'=>'Etiket Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}}'=>'--wpst-parallax-eyebrow:{{VALUE}};')));
  $this->add_control('title_color',array('label'=>'Başlık Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}}'=>'--wpst-parallax-title:{{VALUE}};')));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'title_typography','label'=>'Başlık Tipografi','selector'=>'{{WRAPPER}} .wpst-ew-parallax-image h3'));
  $this->add_control('text_color',array('label'=>'Açıklama Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}}'=>'--wpst-parallax-text:{{VALUE}};')));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'text_typography','label'=>'Açıklama Tipografi','selector'=>'{{WRAPPER}} .wpst-ew-parallax-image p'));
  $this->add_responsive_control('caption_width',array('label'=>'İçerik Genişliği','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>220,'max'=>800)),'selectors'=>array('{{WRAPPER}}'=>'--wpst-parallax-caption-width:{{SIZE}}px;')));
  $this->add_responsive_control('caption_padding',array('label'=>'İçerik Boşluğu','type'=>\Elementor\Controls_Manager::DIMENSIONS,'size_units'=>array('px'),'selectors'=>array('{{WRAPPER}} .wpst-ew-parallax-image figcaption'=>'padding:{{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;')));
  $this->end_controls_section();
  $this->standard_responsive_controls();
 }
 protected function render(){
  $s=$this->get_settings_for_display();
  $mobile='yes'===($s['disable_mobile']??'yes')?'1':'0';
  echo'<figure class="wpst-ew-parallax-image" data-strength="'.esc_attr((int)($s['strength']['size']??28)).'" data-disable-mobile="'.$mobile.'">';
  if(!empty($s['image']['url']))echo'<img src="'.esc_url($s['image']['url']).'" alt="'.esc_attr($s['title']??'').'" loading="lazy" decoding="async">';
  else echo'<div class="wpst-ew-parallax-placeholder">'.esc_html($s['placeholder_text']??'WPSoft').'</div>';
  echo'<span class="wpst-parallax-shade" aria-hidden="true"></span>';
  if('yes'===($s['show_caption']??'yes')){
   echo'<figcaption>';
   if(!empty($s['eyebrow']))echo'<small>'.esc_html($s['eyebrow']).'</small>';
   echo'<h3>'.esc_html($s['title']??'').'</h3><p>'.esc_html($s['text']??'').'</p></figcaption>';
  }
  echo'</figure>';
 }
}
