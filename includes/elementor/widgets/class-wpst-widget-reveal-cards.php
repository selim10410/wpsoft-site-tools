<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class WPST_Widget_Reveal_Cards extends WPST_Elementor_Widget_Base {
 public function get_name(){return 'wpsoft-reveal-cards';}
 public function get_title(){return 'WPSoft Animasyonlu Kartlar 2.0';}
 public function get_icon(){return 'eicon-flip-box';}
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'Kartlar'));
  $this->wpst_signature_preset_control();
  $r=new \Elementor\Repeater();
  $r->add_control('media_type',array(
   'label'=>'Görsel Türü',
   'type'=>\Elementor\Controls_Manager::SELECT,
   'default'=>'icon',
   'options'=>array(
    'icon'=>'WPSoft Icon',
    'image'=>'PNG / JPG / WebP',
    'svg'=>'SVG Dosyası'
   )
  ));
  $r->add_control('wpst_icon',array(
   'label'=>'WPSoft Icon',
   'type'=>\Elementor\Controls_Manager::SELECT2,
   'options'=>class_exists('WPST_Icon_Library')?WPST_Icon_Library::options():array(),
   'default'=>'sparkles',
   'condition'=>array('media_type'=>'icon')
  ));
  $r->add_control('media_image',array(
   'label'=>'Görsel Yükle',
   'type'=>\Elementor\Controls_Manager::MEDIA,
   'media_types'=>array('image'),
   'condition'=>array('media_type'=>'image')
  ));
  $r->add_control('media_svg',array(
   'label'=>'SVG Yükle',
   'type'=>\Elementor\Controls_Manager::MEDIA,
   'media_types'=>array('image'),
   'description'=>'Medya kütüphanesinden SVG dosyanızı seçin.',
   'condition'=>array('media_type'=>'svg')
  ));
  $r->add_control('media_size',array(
   'label'=>'Görsel / İkon Boyutu',
   'type'=>\Elementor\Controls_Manager::SLIDER,
   'size_units'=>array('px'),
   'range'=>array('px'=>array('min'=>16,'max'=>160,'step'=>1)),
   'default'=>array('size'=>42,'unit'=>'px')
  ));
  $r->add_control('title',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Modern Tasarım'));
  $r->add_control('text',array('label'=>'Açıklama','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'Hover ve görünürlük animasyonlu modern kart.'));
  $this->add_control('items',array('label'=>'Kartlar','type'=>\Elementor\Controls_Manager::REPEATER,'fields'=>$r->get_controls(),'default'=>array(array('title'=>'Modern Tasarım','text'=>'Güncel ve sade arayüz.'),array('title'=>'Yüksek Performans','text'=>'Hız odaklı yapı.'),array('title'=>'Esnek Altyapı','text'=>'Kolay geliştirilebilir sistem.')),'title_field'=>'{{{ title }}}'));
  $this->add_control('layout',array('label'=>'Yerleşim','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'grid','options'=>array('grid'=>'Grid','staggered'=>'Staggered','rows'=>'Rows','editorial'=>'Editorial'),'prefix_class'=>'wpst-reveal-cards-layout-'));
  $this->add_responsive_control('columns',array('label'=>'Kolon','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'3','tablet_default'=>'2','mobile_default'=>'1','options'=>array('1'=>'1','2'=>'2','3'=>'3','4'=>'4'),'selectors'=>array('{{WRAPPER}} .wpst-ew-reveal-cards'=>'grid-template-columns:repeat({{VALUE}},minmax(0,1fr))!important;')));
  $this->end_controls_section();
  $this->start_controls_section('style',array('label'=>'Biçim','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('bg',array(
   'label'=>'Kart Arka Plan',
   'type'=>\Elementor\Controls_Manager::COLOR,
   'default'=>'#ffffff',
   'selectors'=>array(
    '{{WRAPPER}} .wpst-ew-reveal-cards'=>'--wpst-reveal-card-bg:{{VALUE}};'
   )
  ));
  $this->add_control('accent',array('label'=>'Vurgu','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#2563eb','selectors'=>array('{{WRAPPER}} .wpst-ew-reveal-cards article:before,{{WRAPPER}} .wpst-reveal-card-icon'=>'background:{{VALUE}}')));
  $this->add_responsive_control('gap',array('label'=>'Kart Aralığı','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>60)),'selectors'=>array('{{WRAPPER}} .wpst-ew-reveal-cards'=>'gap:{{SIZE}}px;')));
  $this->add_responsive_control('padding',array('label'=>'Kart İç Boşluk','type'=>\Elementor\Controls_Manager::DIMENSIONS,'size_units'=>array('px'),'selectors'=>array('{{WRAPPER}} .wpst-ew-reveal-cards article'=>'padding:{{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;')));
  $this->add_responsive_control('radius',array('label'=>'Kart Köşe','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>60)),'selectors'=>array('{{WRAPPER}} .wpst-ew-reveal-cards article'=>'border-radius:{{SIZE}}px;')));
  $this->end_controls_section();
  $this->standard_responsive_controls();
 }
 protected function render(){
  $s=$this->get_settings_for_display();
  echo '<div class="wpst-ew-reveal-cards">';

  foreach((array)$s['items'] as $i){
   $type=isset($i['media_type'])?sanitize_key($i['media_type']):'icon';
   $size=42;
   if(isset($i['media_size']['size']) && is_numeric($i['media_size']['size'])){
    $size=max(16,min(160,(float)$i['media_size']['size']));
   }

   echo '<article class="wpst-animate-on-view">';
   echo '<div class="wpst-reveal-card-media" style="--wpst-reveal-media-size:'.esc_attr($size).'px;">';

   if('image'===$type && !empty($i['media_image']['url'])){
    echo '<img class="wpst-reveal-card-image" src="'.esc_url($i['media_image']['url']).'" alt="'.esc_attr($i['title']??'').'" loading="lazy" decoding="async">';
   }elseif('svg'===$type && !empty($i['media_svg']['url'])){
    echo '<img class="wpst-reveal-card-svg" src="'.esc_url($i['media_svg']['url']).'" alt="" aria-hidden="true">';
   }elseif(!empty($i['wpst_icon']) && class_exists('WPST_Icon_Library')){
    echo '<div class="wpst-reveal-card-icon">'.WPST_Icon_Library::svg($i['wpst_icon'],array('size'=>$size)).'</div>';
   }

   echo '</div>';
   echo '<h3>'.esc_html($i['title']).'</h3><p>'.esc_html($i['text']).'</p>';
   echo '</article>';
  }

  echo '</div>';
 }
}