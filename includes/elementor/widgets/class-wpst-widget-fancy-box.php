<?php
if(!defined('ABSPATH'))exit;
class WPST_Widget_Fancy_Box extends WPST_Elementor_Widget_Base{
 public function get_name(){return'wpsoft-fancy-box';} public function get_title(){return'WPSoft Fancy Box 2.0';} public function get_icon(){return'eicon-call-to-action';}
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'İçerik'));
  $this->wpst_signature_preset_control();
  $this->add_control('number',array('label'=>'Numara / Etiket','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'01'));
  $this->add_control('title',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Web Tasarım'));
  $this->add_control('text',array('label'=>'Açıklama','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'Güçlü görsel dil ve dengeli boşluklarla premium içerik kutusu.'));
  $this->add_control('image',array('label'=>'Görsel','type'=>\Elementor\Controls_Manager::MEDIA));
  $this->link_controls('button','Bağlantı');
  $this->add_control('layout',array('label'=>'Yerleşim','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'overlay','options'=>array('overlay'=>'Overlay','split'=>'Split','minimal'=>'Minimal','hover-card'=>'Hover Card'),'prefix_class'=>'wpst-fancy-layout-'));
  $this->add_control('show_number',array('label'=>'Numarayı Göster','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes'));
  $this->end_controls_section();
  $this->start_controls_section('fancy_style',array('label'=>'Fancy Box Biçimi','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('overlay',array('label'=>'Overlay Rengi','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'rgba(15,23,42,.55)','selectors'=>array('{{WRAPPER}} .wpst-fb-overlay'=>'background:{{VALUE}};')));
  $this->add_control('accent',array('label'=>'Vurgu Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-fancy-box>span'=>'color:{{VALUE}};')));
  $this->add_responsive_control('height',array('label'=>'Minimum Yükseklik','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>220,'max'=>800)),'selectors'=>array('{{WRAPPER}} .wpst-ew-fancy-box'=>'min-height:{{SIZE}}px;')));
  $this->add_responsive_control('radius',array('label'=>'Köşe','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>60)),'selectors'=>array('{{WRAPPER}} .wpst-ew-fancy-box'=>'border-radius:{{SIZE}}px;')));
  $this->add_responsive_control('padding',array('label'=>'İç Boşluk','type'=>\Elementor\Controls_Manager::DIMENSIONS,'size_units'=>array('px'),'selectors'=>array('{{WRAPPER}} .wpst-ew-fancy-box>div:last-child'=>'padding:{{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;')));
  $this->end_controls_section();
  $this->standard_responsive_controls();
 }
 protected function render(){ $s=$this->get_settings_for_display(); echo'<article class="wpst-ew-fancy-box">'.(!empty($s['image']['url'])?'<img src="'.esc_url($s['image']['url']).'" alt="" loading="lazy">':'').'<div class="wpst-fb-overlay"></div>';if('yes'===$s['show_number'])echo'<span>'.esc_html($s['number']).'</span>';echo'<div><h3>'.esc_html($s['title']).'</h3><p>'.esc_html($s['text']).'</p>'; if($s['button_text'])echo'<a'.$this->render_link_attrs($s['button_url']).'>'.esc_html($s['button_text']).'<span class="wpst-cta-arrow is-diagonal" aria-hidden="true"></span></a>'; echo'</div></article>'; }
}