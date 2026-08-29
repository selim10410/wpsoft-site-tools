<?php
if(!defined('ABSPATH'))exit;
class WPST_Widget_Hero_Bento extends WPST_Elementor_Widget_Base{
 public function get_name(){return'wpsoft-hero-bento';} public function get_title(){return'WPSoft Hero · Bento 2.0';} public function get_icon(){return'eicon-gallery-grid';} public function get_keywords(){return array('hero','bento','grid','stats','modern','wpsoft');}
 protected function register_controls(){ $this->start_controls_section('content',array('label'=>'İçerik')); $this->add_control('eyebrow',array('label'=>'Etiket','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'MODERN EXPERIENCE','dynamic'=>array('active'=>true))); $this->add_control('title',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'Tek ekranda güçlü hikâye, görsel ve metrikler','dynamic'=>array('active'=>true))); $this->add_control('text',array('label'=>'Açıklama','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'Bento grid yaklaşımıyla modern ve modüler hero tasarımı.','dynamic'=>array('active'=>true))); $this->add_control('image',array('label'=>'Ana Görsel','type'=>\Elementor\Controls_Manager::MEDIA)); $this->add_control('stat_value',array('label'=>'Metrik Değeri','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'250+')); $this->add_control('stat_label',array('label'=>'Metrik Yazısı','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Proje')); $this->add_control('note_icon',array('label'=>'Not İkonu','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'✦')); $this->add_control('note_text',array('label'=>'Not Yazısı','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'Modern\nDesign System')); $this->link_controls('button','Buton'); $this->add_responsive_control('hero_min_height',array('label'=>'Hero Min. Yükseklik','type'=>\Elementor\Controls_Manager::SLIDER,'size_units'=>array('px','vh'),'range'=>array('px'=>array('min'=>280,'max'=>1100),'vh'=>array('min'=>30,'max'=>100)),'selectors'=>array('{{WRAPPER}} .wpst-ew-hero-bento'=>'min-height:{{SIZE}}{{UNIT}};')));
  $this->add_responsive_control('hero_gap',array('label'=>'Kolon Aralığı','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>100)),'selectors'=>array('{{WRAPPER}} .wpst-ew-hero-bento'=>'gap:{{SIZE}}px;')));
  $this->add_responsive_control('hero_media_height',array('label'=>'Görsel Alanı Yüksekliği','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>180,'max'=>800)),'selectors'=>array('{{WRAPPER}} .wpst-hb-image'=>'--wpst-media-height:{{SIZE}}px;')));
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

  $this->end_controls_section(); $this->hero_button_style_controls();
  $this->start_controls_section('bento_style',array('label'=>'Bento Görünümü','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('hero_bg',array('label'=>'Hero Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-hero-bento'=>'--wpst-hb-bg:{{VALUE}};')));
  $this->add_control('main_surface',array('label'=>'İçerik Kartı','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-hero-bento'=>'--wpst-hb-main-bg:{{VALUE}};')));
  $this->add_control('border',array('label'=>'Kenarlık','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-hero-bento'=>'--wpst-hb-border:{{VALUE}};')));
  $this->add_control('eyebrow_color',array('label'=>'Etiket Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-hero-bento'=>'--wpst-hb-eyebrow:{{VALUE}};')));
  $this->add_control('title_color',array('label'=>'Başlık Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-hero-bento'=>'--wpst-hb-title:{{VALUE}};')));
  $this->add_control('text_color',array('label'=>'Metin Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-hero-bento'=>'--wpst-hb-text:{{VALUE}};')));
  $this->add_control('stat_bg',array('label'=>'Metrik Kartı','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-hero-bento'=>'--wpst-hb-stat-bg:{{VALUE}};')));
  $this->add_control('note_bg',array('label'=>'Not Kartı','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-hero-bento'=>'--wpst-hb-note-bg:{{VALUE}};')));
  $this->add_responsive_control('hero_radius',array('label'=>'Hero Radius','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>60)),'default'=>array('size'=>30),'selectors'=>array('{{WRAPPER}} .wpst-ew-hero-bento'=>'--wpst-hb-radius:{{SIZE}}px;')));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'hero_title_typography','label'=>'Başlık Tipografi','selector'=>'{{WRAPPER}} .wpst-hb-main h1'));
  $this->end_controls_section();
  $this->standard_responsive_controls(); }
 protected function render(){ $s=$this->get_settings_for_display(); echo'<section class="wpst-ew-hero-bento"><div class="wpst-hb-main"><small>'.esc_html($s['eyebrow']).'</small><h1>'.wp_kses_post($s['title']).'</h1><p>'.esc_html($s['text']).'</p>'; if($s['button_text'])echo'<a'.$this->render_link_attrs($s['button_url']).'>'.esc_html($s['button_text']).' <span class="wpst-cta-arrow" aria-hidden="true"></span></a>'; echo'</div><div class="wpst-hb-image">'.(!empty($s['image']['url'])?'<img src="'.esc_url($s['image']['url']).'" alt="">':'').'</div><div class="wpst-hb-stat"><b>'.esc_html($s['stat_value']).'</b><span>'.esc_html($s['stat_label']).'</span></div><div class="wpst-hb-note"><span>'.esc_html($s['note_icon']).'</span><b>'.nl2br(esc_html($s['note_text'])).'</b></div></section>'; }
}