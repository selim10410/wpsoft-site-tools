<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class WPST_Widget_Content_Finder extends WPST_Elementor_Widget_Base {
 public function get_name(){ return 'wpsoft-content-finder'; }
 public function get_title(){ return 'WPSoft · Content Finder'; }
 public function get_icon(){ return 'eicon-search'; }
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'Arama'));
  $this->wpst_signature_preset_control();
  $this->add_control('title',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Ne arıyorsunuz?'));
  $this->add_control('placeholder',array('label'=>'Placeholder','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Hizmet, yazı veya kaynak ara…'));
  $this->add_control('button_text',array('label'=>'Buton','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Ara'));
  $this->add_control('scope',array('label'=>'Arama Kapsamı','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'site','options'=>array('site'=>'Tüm Site','post'=>'Yazılar','page'=>'Sayfalar')));
  $this->add_control('layout',array('label'=>'Yerleşim','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'hero','options'=>array('hero'=>'Hero Search','inline'=>'Inline','compact'=>'Compact','card'=>'Card'),'prefix_class'=>'wpst-finder-layout-'));
  $this->add_control('button_icon',array('label'=>'Arama İkonu','type'=>\Elementor\Controls_Manager::SELECT2,'options'=>class_exists('WPST_Icon_Library')?WPST_Icon_Library::options():array(),'default'=>'search','label_block'=>true));
  $this->add_control('show_title',array('label'=>'Başlığı Göster','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes'));
  $this->add_control('new_tab',array('label'=>'Sonuçları Yeni Sekmede Aç','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>''));
  $this->end_controls_section();
  $this->start_controls_section('finder_style',array('label'=>'Arama Alanı','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('field_bg',array('label'=>'Alan Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-content-finder-row'=>'background:{{VALUE}};')));
  $this->add_control('field_border',array('label'=>'Kenarlık Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-content-finder-row'=>'border-color:{{VALUE}};')));
  $this->add_control('button_bg',array('label'=>'Buton Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-content-finder-row button'=>'background:{{VALUE}};')));
  $this->add_control('button_color',array('label'=>'Buton Yazı','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-content-finder-row button'=>'color:{{VALUE}};')));
  $this->add_responsive_control('field_height',array('label'=>'Alan Minimum Yükseklik','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>44,'max'=>110)),'selectors'=>array('{{WRAPPER}} .wpst-content-finder-row'=>'min-height:{{SIZE}}px;')));
  $this->add_responsive_control('field_radius',array('label'=>'Alan Köşe','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>60)),'selectors'=>array('{{WRAPPER}} .wpst-content-finder-row'=>'border-radius:{{SIZE}}px;')));
  $this->end_controls_section();
  $this->standard_responsive_controls();
 }
 protected function render(){
  $s=$this->get_settings_for_display();
  echo '<form class="wpst-content-finder" role="search" method="get" action="'.esc_url(home_url('/')).'"'.(('yes'===$s['new_tab'])?' target="_blank"':'').'>';
  if('yes'===$s['show_title']&&trim((string)$s['title'])!=='') echo '<label for="wpst-finder-'.$this->get_id().'">'.esc_html($s['title']).'</label>';
  echo '<div class="wpst-content-finder-row"><i>'.(class_exists('WPST_Icon_Library')?WPST_Icon_Library::svg($s['button_icon'],array('size'=>18)):'').'</i><input id="wpst-finder-'.$this->get_id().'" type="search" name="s" placeholder="'.esc_attr($s['placeholder']).'">';
  if('site'!==$s['scope']) echo '<input type="hidden" name="post_type" value="'.esc_attr($s['scope']).'">';
  echo '<button type="submit">'.esc_html($s['button_text']).'</button></div></form>';
 }
}