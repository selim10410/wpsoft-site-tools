<?php
if(!defined('ABSPATH'))exit;
class WPST_Widget_Feature_Mosaic extends WPST_Elementor_Widget_Base{
 public function get_name(){return'wpsoft-feature-mosaic';}
 public function get_title(){return'WPSoft · Feature Mosaic 2.0';}
 public function get_icon(){return'eicon-inner-section';}
 public function get_keywords(){return array('feature','mosaic','grid','cards','image','wpsoft');}
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'İçerik'));
  $this->wpst_signature_preset_control();
  $this->add_control('title',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Tek sistemde güçlü özellikler','dynamic'=>array('active'=>true)));
  $this->add_control('image',array('label'=>'Görsel','type'=>\Elementor\Controls_Manager::MEDIA));
  $r=new \Elementor\Repeater();
  $r->add_control('title',array('label'=>'Kart Başlığı','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Hızlı','dynamic'=>array('active'=>true)));
  $r->add_control('text',array('label'=>'Kart Metni','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'Yüksek performans.','dynamic'=>array('active'=>true)));
  $r->add_control('wpst_icon',array('label'=>'WPSoft Icon','type'=>\Elementor\Controls_Manager::SELECT2,'options'=>class_exists('WPST_Icon_Library')?WPST_Icon_Library::options():array(),'default'=>'arrow-up-right','label_block'=>true));
  $r->add_control('icon',array('label'=>'Eski İşaret','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'↗'));
  $this->add_control('items',array('label'=>'Özellik Kartları','type'=>\Elementor\Controls_Manager::REPEATER,'fields'=>$r->get_controls(),'default'=>array(array('wpst_icon'=>'bolt','title'=>'Hızlı','text'=>'Yüksek performans.','icon'=>'↗'),array('wpst_icon'=>'sliders','title'=>'Esnek','text'=>'Kolay özelleştirme.','icon'=>'↗'),array('wpst_icon'=>'shield','title'=>'Güvenli','text'=>'Sağlam altyapı.','icon'=>'↗')),'title_field'=>'{{{ title }}}'));
  $this->add_control('layout_variant',array('label'=>'Mosaic Yerleşimi','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'feature','options'=>array('feature'=>'Feature Lead','balanced'=>'Balanced Grid','editorial'=>'Editorial','compact'=>'Compact','media-left'=>'Media Left'),'prefix_class'=>'wpst-mosaic-layout-'));
  $this->add_responsive_control('cards_columns',array('label'=>'Kart Kolonu','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'2','tablet_default'=>'2','mobile_default'=>'1','options'=>array('1'=>'1','2'=>'2','3'=>'3'),'selectors'=>array('{{WRAPPER}} .wpst-fm-cards'=>'grid-template-columns:repeat({{VALUE}},minmax(0,1fr))!important;')));
  $this->add_responsive_control('mosaic_gap',array('label'=>'Mosaic Aralığı','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>70)),'default'=>array('size'=>18),'selectors'=>array('{{WRAPPER}} .wpst-ew-feature-mosaic'=>'--wpst-fm-gap:{{SIZE}}px;')));
  $this->add_responsive_control('media_height',array('label'=>'Ana Görsel Yüksekliği','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>180,'max'=>720)),'default'=>array('size'=>420),'tablet_default'=>array('size'=>360),'mobile_default'=>array('size'=>260),'selectors'=>array('{{WRAPPER}} .wpst-fm-main'=>'--wpst-fm-media-height:{{SIZE}}px;')));
  $this->end_controls_section();

  $this->start_controls_section('style_shell',array('label'=>'Mosaic Biçimi','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('surface',array('label'=>'Ana Yüzey','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-feature-mosaic'=>'--wpst-fm-surface:{{VALUE}};')));
  $this->add_control('card_surface',array('label'=>'Kart Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-feature-mosaic'=>'--wpst-fm-card:{{VALUE}};')));
  $this->add_control('border',array('label'=>'Kenarlık','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-feature-mosaic'=>'--wpst-fm-border:{{VALUE}};')));
  $this->add_control('hover_border',array('label'=>'Kart Hover Kenarlık','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-feature-mosaic'=>'--wpst-fm-hover-border:{{VALUE}};')));
  $this->add_responsive_control('radius',array('label'=>'Genel Radius','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>50)),'default'=>array('size'=>24),'selectors'=>array('{{WRAPPER}} .wpst-ew-feature-mosaic'=>'--wpst-fm-radius:{{SIZE}}px;')));
  $this->add_responsive_control('card_padding',array('label'=>'Kart İç Boşluk','type'=>\Elementor\Controls_Manager::DIMENSIONS,'size_units'=>array('px'),'default'=>array('top'=>22,'right'=>22,'bottom'=>22,'left'=>22,'unit'=>'px'),'selectors'=>array('{{WRAPPER}} .wpst-fm-cards article'=>'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};')));
  $this->end_controls_section();

  $this->start_controls_section('style_type',array('label'=>'İçerik Biçimi','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('title_color',array('label'=>'Ana Başlık Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-feature-mosaic'=>'--wpst-fm-title:{{VALUE}};')));
  $this->add_control('card_title_color',array('label'=>'Kart Başlık Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-feature-mosaic'=>'--wpst-fm-card-title:{{VALUE}};')));
  $this->add_control('text_color',array('label'=>'Kart Metin Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-feature-mosaic'=>'--wpst-fm-text:{{VALUE}};')));
  $this->add_control('icon_color',array('label'=>'İkon Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-feature-mosaic'=>'--wpst-fm-icon:{{VALUE}};')));
  $this->add_control('icon_bg',array('label'=>'İkon Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-feature-mosaic'=>'--wpst-fm-icon-bg:{{VALUE}};')));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'main_title_typography','label'=>'Ana Başlık Tipografi','selector'=>'{{WRAPPER}} .wpst-fm-main h2'));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'card_title_typography','label'=>'Kart Başlık Tipografi','selector'=>'{{WRAPPER}} .wpst-fm-cards h3'));
  $this->end_controls_section();
  $this->standard_responsive_controls();
 }
 protected function render(){
  $s=$this->get_settings_for_display();
  echo'<section class="wpst-ew-feature-mosaic"><div class="wpst-fm-main"><h2>'.esc_html($s['title']).'</h2>';
  if(!empty($s['image']['url']))echo'<img src="'.esc_url($s['image']['url']).'" alt="" loading="lazy" decoding="async">';
  echo'</div><div class="wpst-fm-cards">';
  foreach((array)$s['items'] as $item){
   echo'<article><span class="wpst-feature-mosaic-icon">';
   echo(!empty($item['wpst_icon'])&&class_exists('WPST_Icon_Library'))?WPST_Icon_Library::svg($item['wpst_icon'],array('size'=>18)):esc_html($item['icon']);
   echo'</span><h3>'.esc_html($item['title']).'</h3><p>'.esc_html($item['text']).'</p></article>';
  }
  echo'</div></section>';
 }
}
