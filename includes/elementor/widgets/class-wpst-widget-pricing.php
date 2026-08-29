<?php
if(!defined('ABSPATH'))exit;
class WPST_Widget_Pricing extends WPST_Elementor_Widget_Base{
 public function get_name(){return'wpsoft-pricing';}
 public function get_title(){return'WPSoft · Pricing 2.0';}
 public function get_icon(){return'eicon-price-table';}
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'Paket'));
  $this->wpst_signature_preset_control();
  $this->add_control('badge',array('label'=>'Etiket','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Popüler'));
  $this->add_control('title',array('label'=>'Paket Adı','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Profesyonel'));
  $this->add_control('price',array('label'=>'Fiyat','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'₺9.900'));
  $this->add_control('period',array('label'=>'Dönem','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'proje'));
  $this->add_control('description',array('label'=>'Kısa Açıklama','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'Büyüyen işletmeler için güçlü ve dengeli paket.'));
  $this->add_control('features',array('label'=>'Özellikler','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>"Modern tasarım\nMobil uyumluluk\nSEO altyapısı\nHız optimizasyonu"));
  $this->add_control('feature_icon',array('label'=>'Özellik Icon','type'=>\Elementor\Controls_Manager::SELECT2,'options'=>class_exists('WPST_Icon_Library')?WPST_Icon_Library::options():array(),'default'=>'check','label_block'=>true));
  $this->link_controls('button','Buton');
  $this->add_control('button_icon',array('label'=>'Buton Icon','type'=>\Elementor\Controls_Manager::SELECT2,'options'=>class_exists('WPST_Icon_Library')?WPST_Icon_Library::options():array(),'default'=>'arrow-right','label_block'=>true));
  $this->add_control('style_preset',array('label'=>'Kart Stili','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'elevated','options'=>array('minimal'=>'Minimal','elevated'=>'Elevated','soft'=>'Soft','dark'=>'Dark'),'prefix_class'=>'wpst-pricing-style-'));
  $this->add_control('layout_variant',array(
   'label'=>'Fiyat Kompozisyonu','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'classic',
   'options'=>array('classic'=>'Classic Card','horizontal'=>'Horizontal','editorial'=>'Editorial','compact'=>'Compact','statement'=>'Price Statement'),
   'prefix_class'=>'wpst-pricing-layout-'
  ));
  $this->add_control('featured',array('label'=>'Öne Çıkan Plan','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'','prefix_class'=>'wpst-pricing-featured-'));
  $this->end_controls_section();

  $this->start_controls_section('style_card',array('label'=>'Kart','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('accent',array('label'=>'Vurgu','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#2563eb','selectors'=>array('{{WRAPPER}} .wpst-ew-pricing'=>'--pricing-accent:{{VALUE}};')));
  $this->add_control('card_bg',array('label'=>'Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-pricing'=>'--pricing-bg:{{VALUE}};')));
  $this->add_control('border',array('label'=>'Border','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-pricing'=>'--pricing-border:{{VALUE}};')));
  $this->add_responsive_control('padding',array('label'=>'İç Boşluk','type'=>\Elementor\Controls_Manager::DIMENSIONS,'size_units'=>array('px'),'selectors'=>array('{{WRAPPER}} .wpst-ew-pricing'=>'padding:{{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;')));
  $this->add_responsive_control('radius',array('label'=>'Köşe','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>60)),'default'=>array('size'=>24),'selectors'=>array('{{WRAPPER}} .wpst-ew-pricing'=>'border-radius:{{SIZE}}px;')));
  $this->end_controls_section();
  $this->standard_responsive_controls();
 }
 protected function render(){
  $s=$this->get_settings_for_display();
  $features=preg_split('/\r\n|\r|\n/',(string)$s['features']);
  echo'<article class="wpst-ew-pricing">';
  if(trim((string)$s['badge'])!=='')echo'<span class="wpst-ew-pricing-badge">'.esc_html($s['badge']).'</span>';
  echo'<h3>'.esc_html($s['title']).'</h3><p class="wpst-ew-pricing-desc">'.esc_html($s['description']).'</p><div class="wpst-ew-price">'.esc_html($s['price']).'<small>/ '.esc_html($s['period']).'</small></div><ul>';
  foreach($features as $f){if(trim($f)!=='')echo'<li><span>'.(class_exists('WPST_Icon_Library')?WPST_Icon_Library::svg($s['feature_icon'],array('size'=>13)):'✓').'</span>'.esc_html(trim($f)).'</li>';}
  echo'</ul><a'.$this->render_link_attrs($s['button_url']).'><span>'.esc_html($s['button_text']).'</span><i>'.(class_exists('WPST_Icon_Library')?WPST_Icon_Library::svg($s['button_icon'],array('size'=>15)):'→').'</i></a></article>';
 }
}
