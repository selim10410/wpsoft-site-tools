<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class WPST_Widget_Promo_Banner extends WPST_Elementor_Widget_Base {
 public function get_name(){ return 'wpsoft-promo-banner'; }
 public function get_title(){ return 'WPSoft · Promo Banner'; }
 public function get_icon(){ return 'eicon-call-to-action'; }
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'Banner'));
  $this->wpst_signature_preset_control();
  $this->add_control('eyebrow',array('label'=>'Etiket','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'YENİ'));
  $this->add_control('title',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Yeni sezon şimdi yayında'));
  $this->add_control('text',array('label'=>'Açıklama','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'Öne çıkan kampanya, ürün veya hizmeti güçlü bir görsel alanda sunun.'));
  $this->add_control('image',array('label'=>'Görsel','type'=>\Elementor\Controls_Manager::MEDIA));
  $this->link_controls('button','Buton');
  $this->add_control('layout',array('label'=>'Yerleşim','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'split','options'=>array('split'=>'Split','overlay'=>'Overlay','minimal'=>'Minimal','wide'=>'Wide Banner','editorial'=>'Editorial'),'prefix_class'=>'wpst-promo-layout-'));
  $this->add_control('media_position',array('label'=>'Görsel Konumu','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'right','options'=>array('right'=>'Sağ','left'=>'Sol'),'prefix_class'=>'wpst-promo-media-'));
  $this->add_control('show_eyebrow',array('label'=>'Etiketi Göster','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes'));
  $this->end_controls_section();
  $this->start_controls_section('promo_style',array('label'=>'Banner Biçimi','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('bg',array('label'=>'Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-promo-banner'=>'background:{{VALUE}};')));
  $this->add_control('overlay',array('label'=>'Overlay','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-promo-banner:after'=>'background:{{VALUE}};')));
  $this->add_responsive_control('height',array('label'=>'Minimum Yükseklik','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>180,'max'=>700)),'selectors'=>array('{{WRAPPER}} .wpst-promo-banner'=>'min-height:{{SIZE}}px;')));
  $this->add_responsive_control('radius',array('label'=>'Köşe','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>60)),'selectors'=>array('{{WRAPPER}} .wpst-promo-banner'=>'border-radius:{{SIZE}}px;')));
  $this->add_responsive_control('padding',array('label'=>'İçerik İç Boşluk','type'=>\Elementor\Controls_Manager::DIMENSIONS,'size_units'=>array('px'),'selectors'=>array('{{WRAPPER}} .wpst-promo-copy'=>'padding:{{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;')));
  $this->end_controls_section();
  $this->standard_responsive_controls();
 }
 protected function render(){
  $s=$this->get_settings_for_display();
  echo '<section class="wpst-promo-banner">';
  if(!empty($s['image']['url'])) echo '<div class="wpst-promo-media"><img src="'.esc_url($s['image']['url']).'" alt="" loading="lazy"></div>';
  echo '<div class="wpst-promo-copy">'; if('yes'===$s['show_eyebrow'])echo'<span>'.esc_html($s['eyebrow']).'</span>'; echo'<h3>'.esc_html($s['title']).'</h3><p>'.esc_html($s['text']).'</p><a'.$this->render_link_attrs($s['button_url']).'>'.esc_html($s['button_text']).'</a></div></section>';
 }
}