<?php
if(!defined('ABSPATH'))exit;
class WPST_Widget_Hero_Industry extends WPST_Elementor_Widget_Base{
 public function get_name(){return'wpsoft-hero-industry';} public function get_title(){return'WPSoft Hero · Industry 2.0';} public function get_icon(){return'eicon-tools';} public function get_keywords(){return array('hero','industry','manufacturing','factory','metrics','wpsoft');}
 protected function register_controls(){ $this->start_controls_section('content',array('label'=>'İçerik')); $this->add_control('eyebrow',array('label'=>'Etiket','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'ENDÜSTRİYEL ÇÖZÜMLER')); $this->add_control('title',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'Üretim gücünüzü dijitalde güçlü biçimde anlatın')); $this->add_control('text',array('label'=>'Açıklama','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'Makine parkuru, üretim kabiliyeti ve teknik uzmanlığı tek bakışta sunan hero.')); $this->add_control('image',array('label'=>'Görsel','type'=>\Elementor\Controls_Manager::MEDIA)); $r=new \Elementor\Repeater(); $r->add_control('value',array('label'=>'Değer','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'25+')); $r->add_control('label',array('label'=>'Açıklama','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Yıllık Deneyim')); $this->add_control('metrics',array('label'=>'Metrikler','type'=>\Elementor\Controls_Manager::REPEATER,'fields'=>$r->get_controls(),'default'=>array(array('value'=>'25+','label'=>'Yıllık Deneyim'),array('value'=>'1200+','label'=>'Proje'),array('value'=>'98%','label'=>'Memnuniyet')),'title_field'=>'{{{ value }}} · {{{ label }}}')); $this->add_control('media_badge',array('label'=>'Görsel Üstü Teknik Etiket','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'ISO · CNC · PRODUCTION')); $this->link_controls('button','Ana Buton'); $this->add_responsive_control('hero_min_height',array('label'=>'Hero Min. Yükseklik','type'=>\Elementor\Controls_Manager::SLIDER,'size_units'=>array('px','vh'),'range'=>array('px'=>array('min'=>280,'max'=>1100),'vh'=>array('min'=>30,'max'=>100)),'selectors'=>array('{{WRAPPER}} .wpst-ew-hero-industry'=>'min-height:{{SIZE}}{{UNIT}};')));
  $this->add_responsive_control('hero_gap',array('label'=>'Kolon Aralığı','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>100)),'selectors'=>array('{{WRAPPER}} .wpst-ew-hero-industry'=>'gap:{{SIZE}}px;')));
  $this->add_responsive_control('hero_media_height',array('label'=>'Görsel Alanı Yüksekliği','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>180,'max'=>800)),'selectors'=>array('{{WRAPPER}} .wpst-hi-media'=>'--wpst-media-height:{{SIZE}}px;')));
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
  $this->start_controls_section('industry_style',array('label'=>'Industry Görünümü','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('hero_bg',array('label'=>'Hero Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-hero-industry'=>'--wpst-hi-bg:{{VALUE}};')));
  $this->add_control('border',array('label'=>'Kenarlık','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-hero-industry'=>'--wpst-hi-border:{{VALUE}};')));
  $this->add_control('eyebrow_color',array('label'=>'Etiket Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-hero-industry'=>'--wpst-hi-eyebrow:{{VALUE}};')));
  $this->add_control('title_color',array('label'=>'Başlık Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-hero-industry'=>'--wpst-hi-title:{{VALUE}};')));
  $this->add_control('text_color',array('label'=>'Metin Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-hero-industry'=>'--wpst-hi-text:{{VALUE}};')));
  $this->add_control('metric_value_color',array('label'=>'Metrik Değer Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-hero-industry'=>'--wpst-hi-metric-value:{{VALUE}};')));
  $this->add_control('metric_label_color',array('label'=>'Metrik Yazı Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-hero-industry'=>'--wpst-hi-metric-label:{{VALUE}};')));
  $this->add_control('metric_border',array('label'=>'Metrik Çizgi Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-hero-industry'=>'--wpst-hi-metric-border:{{VALUE}};')));
  $this->add_control('badge_bg',array('label'=>'Teknik Etiket Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-hero-industry'=>'--wpst-hi-badge-bg:{{VALUE}};')));
  $this->add_control('badge_color',array('label'=>'Teknik Etiket Yazı','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-hero-industry'=>'--wpst-hi-badge-color:{{VALUE}};')));
  $this->add_responsive_control('hero_radius',array('label'=>'Hero Radius','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>60)),'default'=>array('size'=>30),'selectors'=>array('{{WRAPPER}} .wpst-ew-hero-industry'=>'--wpst-hi-radius:{{SIZE}}px;')));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'hero_title_typography','label'=>'Başlık Tipografi','selector'=>'{{WRAPPER}} .wpst-hi-copy h1'));
  $this->end_controls_section();
  $this->standard_responsive_controls(); }
 protected function render(){ $s=$this->get_settings_for_display(); echo'<section class="wpst-ew-hero-industry"><div class="wpst-hi-copy"><small>'.esc_html($s['eyebrow']).'</small><h1>'.wp_kses_post($s['title']).'</h1><p>'.esc_html($s['text']).'</p>'; if($s['button_text'])echo'<a'.$this->render_link_attrs($s['button_url']).'>'.esc_html($s['button_text']).' <span class="wpst-cta-arrow" aria-hidden="true"></span></a>'; echo'<div class="wpst-hi-metrics">'; foreach((array)$s['metrics'] as $metric) echo'<div><b>'.esc_html($metric['value']).'</b><span>'.esc_html($metric['label']).'</span></div>'; echo'</div></div><div class="wpst-hi-media">'.(!empty($s['image']['url'])?'<img src="'.esc_url($s['image']['url']).'" alt="">':'').'<span>'.esc_html($s['media_badge']).'</span></div></section>'; }
}