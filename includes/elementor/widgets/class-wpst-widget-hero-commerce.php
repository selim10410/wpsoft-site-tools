<?php
if(!defined('ABSPATH'))exit;
class WPST_Widget_Hero_Commerce extends WPST_Elementor_Widget_Base{
 public function get_name(){return'wpsoft-hero-commerce';} public function get_title(){return'WPSoft Hero · Commerce';} public function get_icon(){return'eicon-cart';}
 protected function register_controls(){ $this->start_controls_section('content',array('label'=>'İçerik')); $this->add_control('badge',array('label'=>'Badge','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'YENİ SEZON')); $this->add_control('title',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'Yeni koleksiyonu keşfedin')); $this->add_control('text',array('label'=>'Açıklama','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'E-ticaret ve ürün odaklı siteler için modern kampanya hero.')); $this->add_control('image',array('label'=>'Ürün Görseli','type'=>\Elementor\Controls_Manager::MEDIA)); $this->link_controls('button','Buton'); $this->add_control('discount',array('label'=>'Kampanya','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'%30 İndirim'));
  $this->wpst_signature_preset_control();
  $this->add_control('layout',array('label'=>'Hero Düzeni','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'split','options'=>array('split'=>'Split','editorial'=>'Editorial','product-focus'=>'Product Focus','center'=>'Centered'),'prefix_class'=>'wpst-commerce-layout-'));
  $this->add_control('show_discount',array('label'=>'Kampanya Rozeti','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes'));
  $this->end_controls_section();
  $this->start_controls_section('commerce_style',array('label'=>'Commerce Biçimi','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('bg',array('label'=>'Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-hero-commerce'=>'background:{{VALUE}}!important;')));
  $this->add_control('accent',array('label'=>'Vurgu Rengi','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#7c3aed','selectors'=>array('{{WRAPPER}} .wpst-ew-hero-commerce'=>'--wpst-commerce-accent:{{VALUE}};', '{{WRAPPER}} .wpst-hc-media>b'=>'background:{{VALUE}}!important;')));
  $this->add_responsive_control('min_height',array('label'=>'Minimum Yükseklik','type'=>\Elementor\Controls_Manager::SLIDER,'size_units'=>array('px','vh'),'range'=>array('px'=>array('min'=>360,'max'=>1000),'vh'=>array('min'=>40,'max'=>100)),'selectors'=>array('{{WRAPPER}} .wpst-ew-hero-commerce'=>'min-height:{{SIZE}}{{UNIT}};')));
  $this->add_responsive_control('media_width',array('label'=>'Görsel Genişliği','type'=>\Elementor\Controls_Manager::SLIDER,'size_units'=>array('%','px'),'range'=>array('%'=>array('min'=>30,'max'=>70),'px'=>array('min'=>280,'max'=>900)),'selectors'=>array('{{WRAPPER}} .wpst-hc-media'=>'width:{{SIZE}}{{UNIT}};')));
  $this->add_responsive_control('media_height',array('label'=>'Görsel Alanı Yüksekliği','type'=>\Elementor\Controls_Manager::SLIDER,'size_units'=>array('px'),'range'=>array('px'=>array('min'=>180,'max'=>900)),'selectors'=>array('{{WRAPPER}} .wpst-hc-media'=>'--wpst-media-height:{{SIZE}}px;')));
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

  $this->add_responsive_control('radius',array('label'=>'Görsel Köşe','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>70)),'selectors'=>array('{{WRAPPER}} .wpst-hc-media'=>'border-radius:{{SIZE}}px;')));
  $this->end_controls_section();
  $this->hero_button_style_controls();
        $this->standard_responsive_controls(); }
 protected function render(){ $s=$this->get_settings_for_display(); echo'<section class="wpst-ew-hero-commerce"><div class="wpst-hc-copy"><span>'.esc_html($s['badge']).'</span><h1>'.wp_kses_post($s['title']).'</h1><p>'.esc_html($s['text']).'</p>'; if($s['button_text'])echo'<a'.$this->render_link_attrs($s['button_url']).'>'.esc_html($s['button_text']).' <span class="wpst-cta-arrow" aria-hidden="true"></span></a>'; echo'</div><div class="wpst-hc-media">'.(!empty($s['image']['url'])?'<img src="'.esc_url($s['image']['url']).'" alt="">':'').'<b>'.esc_html($s['discount']).'</b></div></section>'; }
}