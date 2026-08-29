<?php
if(!defined('ABSPATH'))exit;
class WPST_Widget_Hero_SaaS extends WPST_Elementor_Widget_Base{
 public function get_name(){return'wpsoft-hero-saas';} public function get_title(){return'WPSoft Hero · SaaS 2.0';} public function get_icon(){return'eicon-code';}
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'İçerik'));
  $this->wpst_signature_preset_control();
  $this->add_control('badge',array('label'=>'Badge','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Yeni · v3.1'));
  $this->add_control('title',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'İş akışınızı daha hızlı ve akıllı hale getirin'));
  $this->add_control('text',array('label'=>'Açıklama','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'SaaS, yazılım ve teknoloji şirketleri için dashboard görselli premium hero.'));
  $this->add_control('image',array('label'=>'Dashboard Görseli','type'=>\Elementor\Controls_Manager::MEDIA));
  $this->link_controls('primary','Başla Butonu'); $this->link_controls('secondary','Demo Butonu');
  $this->add_control('layout',array('label'=>'Kompozisyon','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'split','options'=>array('split'=>'Split','centered'=>'Centered','dashboard-first'=>'Dashboard First','compact'=>'Compact'),'prefix_class'=>'wpst-hero-saas-layout-'));
  $this->add_control('show_glow',array('label'=>'Glow Efekti','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes'));
  $this->add_control('show_floaters',array('label'=>'Dashboard Dekorları','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes'));
  $this->end_controls_section();
  $this->start_controls_section('saas_style',array('label'=>'Hero Biçimi','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('bg',array('label'=>'Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-hero-saas'=>'background:{{VALUE}}!important;')));
  $this->add_control('accent',array('label'=>'Vurgu Rengi','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#635bff','selectors'=>array('{{WRAPPER}} .wpst-ew-hero-saas'=>'--wpst-saas-accent:{{VALUE}};', '{{WRAPPER}} .wpst-hs-badge'=>'color:{{VALUE}}!important;')));
  $this->add_responsive_control('min_height',array('label'=>'Minimum Yükseklik','type'=>\Elementor\Controls_Manager::SLIDER,'size_units'=>array('px','vh'),'range'=>array('px'=>array('min'=>320,'max'=>1100),'vh'=>array('min'=>40,'max'=>100)),'selectors'=>array('{{WRAPPER}} .wpst-ew-hero-saas'=>'min-height:{{SIZE}}{{UNIT}};')));
  $this->add_responsive_control('dashboard_radius',array('label'=>'Dashboard Köşe','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>60)),'selectors'=>array('{{WRAPPER}} .wpst-hs-dashboard'=>'border-radius:{{SIZE}}px;')));
  $this->add_responsive_control('dashboard_width',array('label'=>'Dashboard Genişliği','type'=>\Elementor\Controls_Manager::SLIDER,'size_units'=>array('%','px'),'range'=>array('%'=>array('min'=>30,'max'=>100),'px'=>array('min'=>280,'max'=>1100)),'selectors'=>array('{{WRAPPER}} .wpst-hs-dashboard'=>'width:{{SIZE}}{{UNIT}};')));
  $this->add_responsive_control('dashboard_height',array('label'=>'Dashboard Görsel Yüksekliği','type'=>\Elementor\Controls_Manager::SLIDER,'size_units'=>array('px'),'range'=>array('px'=>array('min'=>180,'max'=>900)),'selectors'=>array('{{WRAPPER}} .wpst-hs-dashboard'=>'--wpst-media-height:{{SIZE}}px;')));
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

  $this->end_controls_section();
  $this->hero_button_style_controls();
        $this->standard_responsive_controls();
 }
 protected function render(){ $s=$this->get_settings_for_display(); echo'<section class="wpst-ew-hero-saas">';if('yes'===$s['show_glow'])echo'<div class="wpst-hs-glow"></div>';echo'<div class="wpst-hs-copy"><span class="wpst-hs-badge">'.esc_html($s['badge']).'</span><h1>'.wp_kses_post($s['title']).'</h1><p>'.esc_html($s['text']).'</p><div class="wpst-hs-actions">'; if($s['primary_text'])echo'<a class="is-primary"'.$this->render_link_attrs($s['primary_url']).'>'.esc_html($s['primary_text']).' <span class="wpst-cta-arrow" aria-hidden="true"></span></a>'; if($s['secondary_text'])echo'<a class="is-secondary"'.$this->render_link_attrs($s['secondary_url']).'><span class="wpst-play-icon" aria-hidden="true"></span>'.esc_html($s['secondary_text']).'</a>'; echo'</div></div><div class="wpst-hs-dashboard">'.(!empty($s['image']['url'])?'<img src="'.esc_url($s['image']['url']).'" alt="" loading="lazy">':'');if('yes'===$s['show_floaters'])echo'<i class="one"></i><i class="two"></i>';echo'</div></section>'; }
}