<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class WPST_Widget_Location_Map extends WPST_Elementor_Widget_Base {
 public function get_name(){ return 'wpsoft-location-map'; }
 public function get_title(){ return 'WPSoft · Location Map 2.0'; }
 public function get_icon(){ return 'eicon-google-maps'; }
 public function get_keywords(){ return array('map','location','address','directions','office','wpsoft'); }
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'Konum'));
  $this->wpst_signature_preset_control();
  $this->add_control('eyebrow',array('label'=>'Etiket','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'BİZİ ZİYARET EDİN','dynamic'=>array('active'=>true)));
  $this->add_control('title',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Merkez ofisimiz','dynamic'=>array('active'=>true)));
  $this->add_control('address',array('label'=>'Adres','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'İstanbul, Türkiye','dynamic'=>array('active'=>true)));
  $this->add_control('map_url',array('label'=>'Harita Embed URL','type'=>\Elementor\Controls_Manager::URL,'placeholder'=>'https://www.openstreetmap.org/export/embed.html?...','description'=>'OpenStreetMap veya izin verilen bir harita embed URL kullanın.'));
  $this->add_control('directions_url',array('label'=>'Yol Tarifi URL','type'=>\Elementor\Controls_Manager::URL,'placeholder'=>'https://maps.google.com/...','show_external'=>true));
  $this->add_control('button_text',array('label'=>'Buton','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Yol Tarifi Al','dynamic'=>array('active'=>true)));
  $this->add_control('layout',array('label'=>'Yerleşim','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'split','options'=>array('split'=>'Split','overlay'=>'Overlay Card','full'=>'Full Map'),'prefix_class'=>'wpst-map-layout-'));
  $this->end_controls_section();

  $this->start_controls_section('style_layout',array('label'=>'Harita & Yerleşim','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_responsive_control('height',array('label'=>'Harita Yüksekliği','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>240,'max'=>900)),'default'=>array('size'=>480),'tablet_default'=>array('size'=>420),'mobile_default'=>array('size'=>320),'selectors'=>array('{{WRAPPER}} .wpst-location-map-media'=>'min-height:{{SIZE}}px;')));
  $this->add_responsive_control('radius',array('label'=>'Köşe','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>50)),'default'=>array('size'=>24),'selectors'=>array('{{WRAPPER}} .wpst-location-map'=>'--wpst-map-radius:{{SIZE}}px;')));
  $this->add_responsive_control('copy_padding',array('label'=>'İçerik Boşluğu','type'=>\Elementor\Controls_Manager::DIMENSIONS,'size_units'=>array('px'),'selectors'=>array('{{WRAPPER}} .wpst-location-map-copy'=>'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};')));
  $this->add_control('map_filter',array('label'=>'Harita Tonu','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'normal','options'=>array('normal'=>'Normal','soft'=>'Soft','gray'=>'Gri','contrast'=>'Kontrast'),'prefix_class'=>'wpst-map-filter-'));
  $this->end_controls_section();

  $this->start_controls_section('style_copy',array('label'=>'İçerik Biçimi','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('copy_bg',array('label'=>'İçerik Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-location-map'=>'--wpst-map-copy-bg:{{VALUE}};')));
  $this->add_control('border',array('label'=>'Kenarlık','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-location-map'=>'--wpst-map-border:{{VALUE}};')));
  $this->add_control('eyebrow_color',array('label'=>'Etiket Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-location-map'=>'--wpst-map-eyebrow:{{VALUE}};')));
  $this->add_control('title_color',array('label'=>'Başlık Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-location-map'=>'--wpst-map-title:{{VALUE}};')));
  $this->add_control('text_color',array('label'=>'Adres Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-location-map'=>'--wpst-map-text:{{VALUE}};')));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'title_typography','label'=>'Başlık Tipografi','selector'=>'{{WRAPPER}} .wpst-location-map-copy h3'));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'address_typography','label'=>'Adres Tipografi','selector'=>'{{WRAPPER}} .wpst-location-map-copy p'));
  $this->end_controls_section();
  $this->standard_responsive_controls();
 }
 protected function render(){
  $s=$this->get_settings_for_display();
  $map=!empty($s['map_url']['url'])?esc_url($s['map_url']['url']):'';
  $link=is_array($s['directions_url']??null)?$s['directions_url']:array();
  $dir=!empty($link['url'])?esc_url($link['url']):'';
  $target=!empty($link['is_external'])?' target="_blank"':'';
  $rels=array(); if(!empty($link['nofollow']))$rels[]='nofollow'; if(!empty($link['is_external']))$rels[]='noopener';
  $rel=$rels?' rel="'.esc_attr(implode(' ',$rels)).'"':'';
  echo '<section class="wpst-location-map">';
  echo '<div class="wpst-location-map-copy"><span>'.esc_html($s['eyebrow']).'</span><h3>'.esc_html($s['title']).'</h3><p>'.nl2br(esc_html($s['address'])).'</p>';
  if($dir&&trim((string)$s['button_text'])!=='') echo '<a class="wpst-ew-button" href="'.$dir.'"'.$target.$rel.'><i aria-hidden="true">'.(class_exists('WPST_Icon_Library')?WPST_Icon_Library::svg('map-pin',array('size'=>16)):'').'</i>'.esc_html($s['button_text']).'</a>';
  echo '</div><div class="wpst-location-map-media">';
  if($map) echo '<iframe src="'.$map.'" loading="lazy" title="'.esc_attr($s['title']).'" referrerpolicy="no-referrer-when-downgrade"></iframe>';
  else echo '<div class="wpst-location-map-placeholder">'.(class_exists('WPST_Icon_Library')?WPST_Icon_Library::svg('map-pin',array('size'=>34)):'').'<strong>Harita URL ekleyin</strong><small>OpenStreetMap embed URL ile çalışır.</small></div>';
  echo '</div></section>';
 }
}
