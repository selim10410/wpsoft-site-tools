<?php
if(!defined('ABSPATH'))exit;
class WPST_Widget_Flip_Box extends WPST_Elementor_Widget_Base{
 public function get_name(){return'wpsoft-flip-box';} public function get_title(){return'WPSoft Flip Box';} public function get_icon(){return'eicon-flip-box';}
 protected function register_controls(){
  $this->start_controls_section('front',array('label'=>'Ön Yüz'));
  $this->wpst_signature_preset_control();
  $this->add_control('front_title',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Marka Stratejisi'));
  $this->add_control('front_text',array('label'=>'Açıklama','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'Kartın üzerine gelin.'));
  $this->add_control('image',array('label'=>'Görsel','type'=>\Elementor\Controls_Manager::MEDIA));
  $this->add_control('front_badge',array('label'=>'Ön Etiket','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'01'));
  $this->end_controls_section();
  $this->start_controls_section('back',array('label'=>'Arka Yüz'));
  $this->add_control('back_title',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Daha Fazlasını Keşfedin'));
  $this->add_control('back_text',array('label'=>'Açıklama','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'Strateji, tasarım ve teknoloji tek yaklaşımda.'));
  $this->link_controls('button','Buton');
  $this->add_control('flip_direction',array('label'=>'Dönüş Yönü','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'horizontal','options'=>array('horizontal'=>'Yatay','vertical'=>'Dikey','fade'=>'Fade'),'prefix_class'=>'wpst-flip-direction-'));
  $this->end_controls_section();
  $this->start_controls_section('flip_style',array('label'=>'Flip Box Biçimi','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('front_overlay',array('label'=>'Ön Overlay','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'rgba(2,6,23,.48)','selectors'=>array('{{WRAPPER}} .wpst-flip-front:after'=>'background:{{VALUE}};')));
  $this->add_control('back_bg',array('label'=>'Arka Yüz','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#111827','selectors'=>array('{{WRAPPER}} .wpst-flip-back'=>'background:{{VALUE}};')));
  $this->add_responsive_control('height',array('label'=>'Kart Yüksekliği','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>220,'max'=>700)),'selectors'=>array('{{WRAPPER}} .wpst-ew-flip-box'=>'min-height:{{SIZE}}px;')));
  $this->add_responsive_control('radius',array('label'=>'Köşe','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>60)),'selectors'=>array('{{WRAPPER}} .wpst-ew-flip-box,{{WRAPPER}} .wpst-flip-front,{{WRAPPER}} .wpst-flip-back'=>'border-radius:{{SIZE}}px;')));
  $this->add_responsive_control('padding',array('label'=>'İç Boşluk','type'=>\Elementor\Controls_Manager::DIMENSIONS,'size_units'=>array('px'),'selectors'=>array('{{WRAPPER}} .wpst-flip-front>div,{{WRAPPER}} .wpst-flip-back'=>'padding:{{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;')));
  $this->end_controls_section();
  $this->standard_responsive_controls();
 }
 protected function render(){ $s=$this->get_settings_for_display(); echo'<div class="wpst-ew-flip-box"><div class="wpst-flip-inner"><div class="wpst-flip-front">'.(!empty($s['image']['url'])?'<img src="'.esc_url($s['image']['url']).'" alt="">':'').'<div><span class="wpst-flip-badge">'.esc_html($s['front_badge']).'</span><h3>'.esc_html($s['front_title']).'</h3><p>'.esc_html($s['front_text']).'</p></div></div><div class="wpst-flip-back"><h3>'.esc_html($s['back_title']).'</h3><p>'.esc_html($s['back_text']).'</p>'; if($s['button_text'])echo'<a'.$this->render_link_attrs($s['button_url']).'>'.esc_html($s['button_text']).'<span class="wpst-native-arrow">'.(class_exists('WPST_Icon_Library')?WPST_Icon_Library::svg('arrow-right',array('size'=>15)):'→').'</span></a>'; echo'</div></div></div>'; }
}