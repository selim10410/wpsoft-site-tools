<?php
if(!defined('ABSPATH'))exit;
class WPST_Widget_Mega_Promo extends WPST_Elementor_Widget_Base{
 public function get_name(){return'wpsoft-mega-promo';}
 public function get_title(){return'WPSoft Mega · Promo 2.0';}
 public function get_icon(){return'eicon-call-to-action';}
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'Promo'));
  $this->wpst_signature_preset_control();
  $this->add_control('eyebrow',array('label'=>'Etiket','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'ÖNE ÇIKAN'));
  $this->add_control('title',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Yeni nesil dijital çözümler'));
  $this->add_control('text',array('label'=>'Açıklama','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'Öne çıkan hizmet, ürün veya kampanyayı burada vurgulayın.'));
  $this->add_control('image',array('label'=>'Görsel','type'=>\Elementor\Controls_Manager::MEDIA));
  $this->link_controls('button','Buton');
  $this->add_control('layout',array('label'=>'Yerleşim','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'overlay','options'=>array('overlay'=>'Overlay','card'=>'Card','split'=>'Split','minimal'=>'Minimal'),'prefix_class'=>'wpst-mega-promo-layout-'));
  $this->add_control('image_position',array('label'=>'Görsel Odak','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'center center','options'=>array('center center'=>'Orta','center top'=>'Üst','center bottom'=>'Alt','left center'=>'Sol','right center'=>'Sağ'),'selectors'=>array('{{WRAPPER}} .wpst-ew-mega-promo img'=>'object-position:{{VALUE}};')));
  $this->end_controls_section();
  $this->start_controls_section('promo_style',array('label'=>'Promo Biçimi','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('overlay',array('label'=>'Overlay','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'rgba(15,23,42,.55)','selectors'=>array('{{WRAPPER}} .wpst-mega-promo-shade'=>'background:{{VALUE}};')));
  $this->add_control('accent',array('label'=>'Vurgu','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-mega-promo-copy small'=>'color:{{VALUE}};')));
  $this->add_responsive_control('min_height',array('label'=>'Minimum Yükseklik','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>180,'max'=>700)),'selectors'=>array('{{WRAPPER}} .wpst-ew-mega-promo'=>'min-height:{{SIZE}}px;')));
  $this->add_responsive_control('radius',array('label'=>'Köşe','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>50)),'selectors'=>array('{{WRAPPER}} .wpst-ew-mega-promo'=>'border-radius:{{SIZE}}px;')));
  $this->add_responsive_control('padding',array('label'=>'İçerik İç Boşluk','type'=>\Elementor\Controls_Manager::DIMENSIONS,'size_units'=>array('px'),'selectors'=>array('{{WRAPPER}} .wpst-mega-promo-copy'=>'padding:{{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;')));
  $this->end_controls_section();
  $this->standard_responsive_controls();
 }
 protected function render(){ $s=$this->get_settings_for_display();echo'<aside class="wpst-ew-mega-promo">';if(!empty($s['image']['url']))echo'<img src="'.esc_url($s['image']['url']).'" alt="" loading="lazy">';echo'<div class="wpst-mega-promo-shade"></div><div class="wpst-mega-promo-copy"><small>'.esc_html($s['eyebrow']).'</small><h3>'.esc_html($s['title']).'</h3><p>'.esc_html($s['text']).'</p>';if($s['button_text'])echo'<a'.$this->render_link_attrs($s['button_url']).'>'.esc_html($s['button_text']).' <span class="wpst-native-arrow">'.(class_exists('WPST_Icon_Library')?WPST_Icon_Library::svg('arrow-right',array('size'=>15)):'→').'</span></a>';echo'</div></aside>'; }
}