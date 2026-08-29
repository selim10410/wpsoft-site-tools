<?php
if(!defined('ABSPATH'))exit;
class WPST_Widget_Hero_Medical extends WPST_Elementor_Widget_Base{
 public function get_name(){return'wpsoft-hero-medical';} public function get_title(){return'WPSoft Hero · Medical 2.0';} public function get_icon(){return'eicon-plus-square';} public function get_keywords(){return array('hero','medical','clinic','doctor','health','wpsoft');}
 protected function register_controls(){ $this->start_controls_section('content',array('label'=>'İçerik')); $this->add_control('eyebrow',array('label'=>'Etiket','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'SAĞLIKTA GÜVEN','dynamic'=>array('active'=>true))); $this->add_control('title',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'Uzmanlık ve güveni modern bir deneyimle sunun','dynamic'=>array('active'=>true))); $this->add_control('text',array('label'=>'Açıklama','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'Klinik, diş ve sağlık markaları için güven odaklı modern hero.','dynamic'=>array('active'=>true))); $this->add_control('image',array('label'=>'Görsel','type'=>\Elementor\Controls_Manager::MEDIA)); $r=new \Elementor\Repeater(); $r->add_control('text',array('label'=>'Güven Maddesi','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Uzman Hekim')); $this->add_control('trust_items',array('label'=>'Güven Maddeleri','type'=>\Elementor\Controls_Manager::REPEATER,'fields'=>$r->get_controls(),'default'=>array(array('text'=>'Uzman Hekim'),array('text'=>'Hızlı Randevu'),array('text'=>'Güvenli Süreç')),'title_field'=>'{{{ text }}}')); $this->add_control('trust_icon',array('label'=>'Madde İkonu','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'✓')); $this->link_controls('button','Randevu Butonu'); $this->add_responsive_control('hero_min_height',array('label'=>'Hero Min. Yükseklik','type'=>\Elementor\Controls_Manager::SLIDER,'size_units'=>array('px','vh'),'range'=>array('px'=>array('min'=>280,'max'=>1100),'vh'=>array('min'=>30,'max'=>100)),'selectors'=>array('{{WRAPPER}} .wpst-ew-hero-medical'=>'min-height:{{SIZE}}{{UNIT}};')));
  $this->add_responsive_control('hero_gap',array('label'=>'Kolon Aralığı','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>100)),'selectors'=>array('{{WRAPPER}} .wpst-ew-hero-medical'=>'gap:{{SIZE}}px;')));
  $this->add_responsive_control('hero_media_height',array('label'=>'Görsel Alanı Yüksekliği','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>180,'max'=>800)),'selectors'=>array('{{WRAPPER}} .wpst-hm-media'=>'--wpst-media-height:{{SIZE}}px;')));
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
  $this->start_controls_section('medical_style',array('label'=>'Medical Görünümü','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('hero_bg',array('label'=>'Hero Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-hero-medical'=>'--wpst-hm-bg:{{VALUE}};')));
  $this->add_control('border',array('label'=>'Kenarlık','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-hero-medical'=>'--wpst-hm-border:{{VALUE}};')));
  $this->add_control('eyebrow_color',array('label'=>'Etiket Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-hero-medical'=>'--wpst-hm-eyebrow:{{VALUE}};')));
  $this->add_control('title_color',array('label'=>'Başlık Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-hero-medical'=>'--wpst-hm-title:{{VALUE}};')));
  $this->add_control('text_color',array('label'=>'Metin Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-hero-medical'=>'--wpst-hm-text:{{VALUE}};')));
  $this->add_control('trust_bg',array('label'=>'Güven Etiketi Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-hero-medical'=>'--wpst-hm-trust-bg:{{VALUE}};')));
  $this->add_control('trust_color',array('label'=>'Güven Etiketi Yazı','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-hero-medical'=>'--wpst-hm-trust-color:{{VALUE}};')));
  $this->add_control('accent_bg',array('label'=>'Görsel + İşareti','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-hero-medical'=>'--wpst-hm-accent:{{VALUE}};')));
  $this->add_responsive_control('hero_radius',array('label'=>'Hero Radius','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>60)),'default'=>array('size'=>30),'selectors'=>array('{{WRAPPER}} .wpst-ew-hero-medical'=>'--wpst-hm-radius:{{SIZE}}px;')));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'hero_title_typography','label'=>'Başlık Tipografi','selector'=>'{{WRAPPER}} .wpst-hm-copy h1'));
  $this->end_controls_section();
  $this->standard_responsive_controls(); }
 protected function render(){ $s=$this->get_settings_for_display(); echo'<section class="wpst-ew-hero-medical"><div class="wpst-hm-copy"><small>'.esc_html($s['eyebrow']).'</small><h1>'.wp_kses_post($s['title']).'</h1><p>'.esc_html($s['text']).'</p>'; if($s['button_text'])echo'<a'.$this->render_link_attrs($s['button_url']).'>'.esc_html($s['button_text']).' <span class="wpst-cta-arrow" aria-hidden="true"></span></a>'; echo'<div class="wpst-hm-trust">'; foreach((array)$s['trust_items'] as $trust) echo'<span>'.esc_html($s['trust_icon']).' '.esc_html($trust['text']).'</span>'; echo'</div></div><div class="wpst-hm-media">'.(!empty($s['image']['url'])?'<img src="'.esc_url($s['image']['url']).'" alt="">':'').'<i>+</i></div></section>'; }
}