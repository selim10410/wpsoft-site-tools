<?php
if(!defined('ABSPATH'))exit;
class WPST_Widget_Hero_Split_Modern extends WPST_Elementor_Widget_Base{
 public function get_name(){return'wpsoft-hero-split-modern';}
 public function get_title(){return'WPSoft Hero · Split 2.0';}
 public function get_icon(){return'eicon-banner';}
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'İçerik'));
  $this->wpst_signature_preset_control();
  $this->add_control('eyebrow',array('label'=>'Üst Etiket','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'WPSOFT STUDIO'));
  $this->add_control('title',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'Dijitalde güçlü bir ilk izlenim oluşturun'));
  $this->add_control('text',array('label'=>'Açıklama','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'Modern tipografi, güçlü görsel ve dönüşüm odaklı CTA ile premium hero alanı.'));
  $this->add_control('image',array('label'=>'Görsel','type'=>\Elementor\Controls_Manager::MEDIA));
  $this->add_control('float_icon',array('label'=>'Floating Icon','type'=>\Elementor\Controls_Manager::SELECT2,'options'=>class_exists('WPST_Icon_Library')?WPST_Icon_Library::options():array(),'default'=>'chart','label_block'=>true));
  $this->add_control('float_value',array('label'=>'Floating Değer','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'+48%'));
  $this->add_control('float_text',array('label'=>'Floating Açıklama','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Daha güçlü etkileşim'));
  $this->link_controls('primary','Ana Buton');
  $this->link_controls('secondary','İkinci Buton');
  $this->add_control('primary_icon',array('label'=>'Ana Buton Icon','type'=>\Elementor\Controls_Manager::SELECT2,'options'=>class_exists('WPST_Icon_Library')?WPST_Icon_Library::options():array(),'default'=>'arrow-right','label_block'=>true));
  $this->add_control('layout_style',array('label'=>'Hero Stili','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'split','options'=>array('split'=>'Split','editorial'=>'Editorial','soft'=>'Soft','dark'=>'Dark'),'prefix_class'=>'wpst-hero2-style-'));
  $this->add_control('composition',array(
   'label'=>'Kompozisyon',
   'type'=>\Elementor\Controls_Manager::SELECT,
   'default'=>'classic',
   'options'=>array(
    'classic'=>'Classic Split',
    'stacked'=>'Stacked Editorial',
    'showcase'=>'Media Showcase',
    'minimal'=>'Minimal Statement',
    'offset'=>'Offset Studio'
   ),
   'prefix_class'=>'wpst-hero-composition-'
  ));
  $this->add_control('media_ratio',array('label'=>'Görsel Oranı','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'portrait','options'=>array('portrait'=>'Portrait','square'=>'Square','wide'=>'Wide'),'prefix_class'=>'wpst-hero2-media-'));
  $this->end_controls_section();

  $this->start_controls_section('style_surface',array('label'=>'Yüzey','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('bg',array('label'=>'Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-hero-split-modern'=>'--hero2-bg:{{VALUE}};background:{{VALUE}}!important;')));
  $this->add_control('accent',array('label'=>'Vurgu','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#2563eb','selectors'=>array('{{WRAPPER}} .wpst-ew-hero-split-modern'=>'--hero2-accent:{{VALUE}};', '{{WRAPPER}} .wpst-hsm-copy small'=>'color:{{VALUE}}!important;', '{{WRAPPER}} .wpst-hsm-float>i'=>'color:{{VALUE}}!important;')));
  $this->add_control('title_color',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-hero-split-modern'=>'--hero2-title:{{VALUE}};', '{{WRAPPER}} .wpst-hsm-copy h1'=>'color:{{VALUE}}!important;')));
  $this->add_control('text_color',array('label'=>'Metin','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-hero-split-modern'=>'--hero2-text:{{VALUE}};', '{{WRAPPER}} .wpst-hsm-copy p'=>'color:{{VALUE}}!important;')));
  $this->add_responsive_control('padding',array('label'=>'İç Boşluk','type'=>\Elementor\Controls_Manager::DIMENSIONS,'size_units'=>array('px'),'selectors'=>array('{{WRAPPER}} .wpst-ew-hero-split-modern'=>'padding:{{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;')));
  $this->add_responsive_control('radius',array('label'=>'Köşe','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>70)),'default'=>array('size'=>30),'selectors'=>array('{{WRAPPER}} .wpst-ew-hero-split-modern'=>'border-radius:{{SIZE}}px;')));
  $this->end_controls_section();

  $this->start_controls_section('style_media',array('label'=>'Görsel','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_responsive_control('media_radius',array('label'=>'Görsel Köşe','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>60)),'default'=>array('size'=>24),'selectors'=>array('{{WRAPPER}} .wpst-hsm-media'=>'border-radius:{{SIZE}}px;')));
  $this->add_responsive_control('media_height',array('label'=>'Görsel Alanı Yüksekliği','type'=>\Elementor\Controls_Manager::SLIDER,'size_units'=>array('px'),'range'=>array('px'=>array('min'=>180,'max'=>900)),'selectors'=>array('{{WRAPPER}} .wpst-hsm-media'=>'--wpst-media-height:{{SIZE}}px;')));
  $this->add_responsive_control('wpst_media_position',array(
   'label'=>'Görsel Yatay Konum',
   'type'=>\Elementor\Controls_Manager::CHOOSE,
   'options'=>array(
    'left'=>array('title'=>'Sol','icon'=>'eicon-h-align-left'),
    'center'=>array('title'=>'Orta','icon'=>'eicon-h-align-center'),
    'right'=>array('title'=>'Sağ','icon'=>'eicon-h-align-right'),
    'custom'=>array('title'=>'Özel','icon'=>'eicon-settings')
   ),
   'default'=>'center',
   'tablet_default'=>'center',
   'mobile_default'=>'center',
   'toggle'=>false,
   'selectors'=>array(
    '{{WRAPPER}}'=>'--wpst-media-pos-x:{{VALUE}};'
   )
  ));
  $this->add_responsive_control('wpst_media_position_x',array(
   'label'=>'Özel X Konumu',
   'type'=>\Elementor\Controls_Manager::SLIDER,
   'size_units'=>array('%'),
   'range'=>array('%'=>array('min'=>0,'max'=>100)),
   'default'=>array('size'=>50,'unit'=>'%'),
   'tablet_default'=>array('size'=>50,'unit'=>'%'),
   'mobile_default'=>array('size'=>50,'unit'=>'%'),
   'condition'=>array('wpst_media_position'=>'custom'),
   'selectors'=>array('{{WRAPPER}}'=>'--wpst-media-custom-x:{{SIZE}}%;')
  ));
  $this->add_responsive_control('wpst_media_position_y',array(
   'label'=>'Özel Y Konumu',
   'type'=>\Elementor\Controls_Manager::SLIDER,
   'size_units'=>array('%'),
   'range'=>array('%'=>array('min'=>0,'max'=>100)),
   'default'=>array('size'=>50,'unit'=>'%'),
   'tablet_default'=>array('size'=>50,'unit'=>'%'),
   'mobile_default'=>array('size'=>50,'unit'=>'%'),
   'condition'=>array('wpst_media_position'=>'custom'),
   'selectors'=>array('{{WRAPPER}}'=>'--wpst-media-pos-y:{{SIZE}}%;')
  ));

  $this->add_control('media_shadow',array('label'=>'Gölge','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'soft','options'=>array('none'=>'Yok','soft'=>'Soft','deep'=>'Deep'),'prefix_class'=>'wpst-hero2-shadow-'));
  $this->end_controls_section();
  $this->hero_button_style_controls();
        $this->standard_responsive_controls();
 }
 protected function render(){
  $s=$this->get_settings_for_display();
  echo'<section class="wpst-ew-hero-split-modern"><div class="wpst-hsm-copy"><small>'.esc_html($s['eyebrow']).'</small><h1>'.wp_kses_post($s['title']).'</h1><p>'.esc_html($s['text']).'</p><div class="wpst-hsm-actions">';
  if(!empty($s['primary_text']))echo'<a class="is-primary"'.$this->render_link_attrs($s['primary_url']).'><span>'.esc_html($s['primary_text']).'</span><i>'.(class_exists('WPST_Icon_Library')?WPST_Icon_Library::svg($s['primary_icon'],array('size'=>16)):'→').'</i></a>';
  if(!empty($s['secondary_text']))echo'<a class="is-secondary"'.$this->render_link_attrs($s['secondary_url']).'>'.esc_html($s['secondary_text']).'</a>';
  echo'</div></div><div class="wpst-hsm-media">';
  if(!empty($s['image']['url']))echo'<img src="'.esc_url($s['image']['url']).'" alt="">';
  else echo'<div class="wpst-hsm-placeholder"></div>';
  echo'<div class="wpst-hsm-float"><i>'.(class_exists('WPST_Icon_Library')?WPST_Icon_Library::svg($s['float_icon'],array('size'=>18)):'').'</i><div><b>'.esc_html($s['float_value']).'</b><span>'.esc_html($s['float_text']).'</span></div></div></div></section>';
 }
}
