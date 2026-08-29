<?php
if(!defined('ABSPATH'))exit;
class WPST_Widget_Footer_Social extends WPST_Elementor_Widget_Base{
 public function get_name(){return'wpsoft-footer-social';}
 public function get_title(){return'WPSoft Footer · Social 2.0';}
 public function get_icon(){return'eicon-social-icons';}
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'Sosyal Bağlantılar'));
  $this->add_control('title',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Bizi Takip Edin'));
  $this->add_control('show_title',array('label'=>'Başlığı Göster','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes'));
  $r=new \Elementor\Repeater();
  $r->add_control('platform',array(
   'label'=>'Platform',
   'type'=>\Elementor\Controls_Manager::SELECT,
   'default'=>'instagram',
   'options'=>array(
    'instagram'=>'Instagram',
    'facebook'=>'Facebook',
    'x'=>'X / Twitter',
    'linkedin'=>'LinkedIn',
    'youtube'=>'YouTube',
    'tiktok'=>'TikTok',
    'whatsapp'=>'WhatsApp',
    'telegram'=>'Telegram',
    'pinterest'=>'Pinterest',
    'github'=>'GitHub',
    'dribbble'=>'Dribbble',
    'behance'=>'Behance',
    'discord'=>'Discord',
    'custom'=>'Özel'
   )
  ));
  $r->add_control('label',array(
   'label'=>'Özel Etiket',
   'type'=>\Elementor\Controls_Manager::TEXT,
   'default'=>'Sosyal Medya',
   'condition'=>array('platform'=>'custom')
  ));
  $r->add_control('wpst_icon',array(
   'label'=>'Özel WPSoft Icon',
   'type'=>\Elementor\Controls_Manager::SELECT2,
   'options'=>class_exists('WPST_Icon_Library')?WPST_Icon_Library::options():array(),
   'default'=>'globe',
   'label_block'=>true,
   'condition'=>array('platform'=>'custom')
  ));
  $r->add_control('custom_color',array(
   'label'=>'Özel Platform Rengi',
   'type'=>\Elementor\Controls_Manager::COLOR,
   'default'=>'#2563eb',
   'condition'=>array('platform'=>'custom')
  ));
  $r->add_control('url',array('label'=>'Link','type'=>\Elementor\Controls_Manager::URL,'default'=>array('url'=>'#')));
  $this->add_control('items',array('label'=>'Sosyal Ağlar','type'=>\Elementor\Controls_Manager::REPEATER,'fields'=>$r->get_controls(),'default'=>array(
   array('platform'=>'instagram','url'=>array('url'=>'#')),
   array('platform'=>'linkedin','url'=>array('url'=>'#')),
   array('platform'=>'youtube','url'=>array('url'=>'#'))
  ),'title_field'=>'{{{ label }}}'));
  $this->add_control('style_preset',array('label'=>'Stil','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'icon-label','options'=>array('icon-label'=>'Icon + Label','icons'=>'Sadece Icon','pills'=>'Pills','minimal'=>'Minimal'),'prefix_class'=>'wpst-footer-social-style-'));
  $this->add_responsive_control('layout_direction',array(
   'label'=>'Yerleşim',
   'type'=>\Elementor\Controls_Manager::SELECT,
   'default'=>'row',
   'tablet_default'=>'row',
   'mobile_default'=>'row',
   'options'=>array(
    'row'=>'Yatay',
    'column'=>'Dikey'
   ),
   'selectors'=>array(
    '{{WRAPPER}} .wpst-ew-footer-social>div'=>'flex-direction:{{VALUE}};'
   )
  ));
  $this->add_responsive_control('wrap',array(
   'label'=>'Satıra Geçiş',
   'type'=>\Elementor\Controls_Manager::SELECT,
   'default'=>'wrap',
   'tablet_default'=>'wrap',
   'mobile_default'=>'wrap',
   'options'=>array(
    'wrap'=>'Satıra Geçebilir',
    'nowrap'=>'Tek Satır'
   ),
   'selectors'=>array(
    '{{WRAPPER}} .wpst-ew-footer-social>div'=>'flex-wrap:{{VALUE}};'
   ),
   'condition'=>array('layout_direction'=>'row')
  ));
  $this->add_control('icon_shape',array(
   'label'=>'İkon Şekli',
   'type'=>\Elementor\Controls_Manager::SELECT,
   'default'=>'rounded',
   'options'=>array(
    'none'=>'Yok',
    'circle'=>'Daire',
    'rounded'=>'Yuvarlatılmış Kare',
    'square'=>'Kare'
   ),
   'prefix_class'=>'wpst-footer-social-shape-'
  ));
  $this->add_control('hover_style',array(
   'label'=>'Hover Efekti',
   'type'=>\Elementor\Controls_Manager::SELECT,
   'default'=>'lift',
   'options'=>array(
    'none'=>'Yok',
    'lift'=>'Yukarı Kalk',
    'fill'=>'Dolgu',
    'scale'=>'Büyüt',
    'outline'=>'Outline'
   ),
   'prefix_class'=>'wpst-footer-social-hover-'
  ));
  $this->add_control('brand_colors',array(
   'label'=>'Platform Renklerini Kullan',
   'type'=>\Elementor\Controls_Manager::SWITCHER,
   'return_value'=>'yes',
   'default'=>'yes',
   'prefix_class'=>'wpst-footer-social-brand-'
  ));
  $this->add_responsive_control('align',array(
   'label'=>'Hizalama',
   'type'=>\Elementor\Controls_Manager::CHOOSE,
   'default'=>'flex-start',
   'options'=>array(
    'flex-start'=>array('title'=>'Sol','icon'=>'eicon-h-align-left'),
    'center'=>array('title'=>'Orta','icon'=>'eicon-h-align-center'),
    'flex-end'=>array('title'=>'Sağ','icon'=>'eicon-h-align-right')
   ),
   'selectors'=>array('{{WRAPPER}} .wpst-ew-footer-social>div'=>'justify-content:{{VALUE}};')
  ));
  $this->add_responsive_control('gap',array('label'=>'Sosyal Link Aralığı','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>40)),'selectors'=>array('{{WRAPPER}} .wpst-ew-footer-social>div'=>'gap:{{SIZE}}px;')));
  $this->add_responsive_control('icon_box',array('label'=>'Icon Alanı','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>30,'max'=>72)),'selectors'=>array('{{WRAPPER}} .wpst-ew-footer-social a>b'=>'width:{{SIZE}}px;height:{{SIZE}}px;')));
  $this->add_responsive_control('link_min_height',array('label'=>'Link Yüksekliği','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>36,'max'=>72)),'selectors'=>array('{{WRAPPER}} .wpst-ew-footer-social a'=>'min-height:{{SIZE}}px;')));

  $this->end_controls_section();

  $this->start_controls_section('style_title',array(
   'label'=>'Başlık Biçimi',
   'tab'=>\Elementor\Controls_Manager::TAB_STYLE,
   'condition'=>array('show_title'=>'yes')
  ));
  $this->add_control('title_color',array(
   'label'=>'Başlık Rengi',
   'type'=>\Elementor\Controls_Manager::COLOR,
   'selectors'=>array('{{WRAPPER}} .wpst-ew-footer-social h4'=>'color:{{VALUE}}!important;')
  ));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array(
   'name'=>'footer_social_title_typography',
   'selector'=>'{{WRAPPER}} .wpst-ew-footer-social h4'
  ));
  $this->add_responsive_control('title_spacing',array(
   'label'=>'Başlık Alt Boşluk',
   'type'=>\Elementor\Controls_Manager::SLIDER,
   'range'=>array('px'=>array('min'=>0,'max'=>50)),
   'selectors'=>array('{{WRAPPER}} .wpst-ew-footer-social h4'=>'margin-bottom:{{SIZE}}px!important;')
  ));
  $this->end_controls_section();

  $this->start_controls_section('style_social',array(
   'label'=>'Sosyal Link Biçimi',
   'tab'=>\Elementor\Controls_Manager::TAB_STYLE
  ));

  $this->add_control('link_color',array(
   'label'=>'Metin / İkon Rengi',
   'type'=>\Elementor\Controls_Manager::COLOR,
   'selectors'=>array('{{WRAPPER}} .wpst-ew-footer-social'=>'--wpst-footer-social-color:{{VALUE}}!important;')
  ));
  $this->add_control('hover_color',array(
   'label'=>'Hover Rengi',
   'type'=>\Elementor\Controls_Manager::COLOR,
   'selectors'=>array('{{WRAPPER}} .wpst-ew-footer-social'=>'--wpst-footer-social-hover:{{VALUE}};')
  ));
  $this->add_control('icon_bg',array(
   'label'=>'İkon Arka Planı',
   'type'=>\Elementor\Controls_Manager::COLOR,
   'selectors'=>array('{{WRAPPER}} .wpst-ew-footer-social'=>'--wpst-footer-social-icon-bg:{{VALUE}};')
  ));
  $this->add_control('icon_border',array(
   'label'=>'İkon Border Rengi',
   'type'=>\Elementor\Controls_Manager::COLOR,
   'selectors'=>array('{{WRAPPER}} .wpst-ew-footer-social'=>'--wpst-footer-social-border:{{VALUE}};')
  ));
  $this->add_control('link_bg',array(
   'label'=>'Link Arka Planı',
   'type'=>\Elementor\Controls_Manager::COLOR,
   'selectors'=>array('{{WRAPPER}} .wpst-ew-footer-social'=>'--wpst-footer-social-link-bg:{{VALUE}};')
  ));
  $this->add_control('hover_bg',array(
   'label'=>'Hover Arka Planı',
   'type'=>\Elementor\Controls_Manager::COLOR,
   'selectors'=>array('{{WRAPPER}} .wpst-ew-footer-social'=>'--wpst-footer-social-hover-bg:{{VALUE}};')
  ));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array(
   'name'=>'footer_social_typography',
   'selector'=>'{{WRAPPER}} .wpst-ew-footer-social a>span'
  ));
  $this->add_responsive_control('link_padding_x',array(
   'label'=>'Yatay İç Boşluk',
   'type'=>\Elementor\Controls_Manager::SLIDER,
   'range'=>array('px'=>array('min'=>0,'max'=>28)),
   'selectors'=>array('{{WRAPPER}} .wpst-ew-footer-social'=>'--wpst-footer-social-pad-x:{{SIZE}}px;')
  ));
  $this->add_responsive_control('link_radius',array(
   'label'=>'Link Köşesi',
   'type'=>\Elementor\Controls_Manager::SLIDER,
   'range'=>array('px'=>array('min'=>0,'max'=>40)),
   'selectors'=>array('{{WRAPPER}} .wpst-ew-footer-social'=>'--wpst-footer-social-radius:{{SIZE}}px;')
  ));
  $this->add_responsive_control('icon_size',array(
   'label'=>'İkon Boyutu',
   'type'=>\Elementor\Controls_Manager::SLIDER,
   'range'=>array('px'=>array('min'=>10,'max'=>32)),
   'default'=>array('unit'=>'px','size'=>15),
   'selectors'=>array(
    '{{WRAPPER}} .wpst-ew-footer-social a>b svg'=>'width:{{SIZE}}px;height:{{SIZE}}px;'
   )
  ));
  $this->add_responsive_control('label_size',array(
   'label'=>'Metin Boyutu',
   'type'=>\Elementor\Controls_Manager::SLIDER,
   'range'=>array('px'=>array('min'=>10,'max'=>22)),
   'selectors'=>array(
    '{{WRAPPER}} .wpst-ew-footer-social a>span'=>'font-size:{{SIZE}}px;'
   )
  ));

  $this->end_controls_section();
  $this->standard_responsive_controls();
 }
 private function platform_data($platform){
  $map=array(
   'instagram'=>array('label'=>'Instagram','color'=>'#E4405F','svg'=>'<svg viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><rect x="3.5" y="3.5" width="17" height="17" rx="5"/><circle cx="12" cy="12" r="4.1"/><circle cx="17.4" cy="6.8" r=".85" fill="currentColor" stroke="none"/></svg>'),
   'facebook'=>array('label'=>'Facebook','color'=>'#1877F2','svg'=>'<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M13.7 21v-8h2.7l.4-3h-3.1V8.1c0-.9.3-1.5 1.6-1.5H17V4a23 23 0 0 0-2.4-.1c-2.4 0-4.1 1.5-4.1 4.2V10H8v3h2.5v8h3.2Z"/></svg>'),
   'x'=>array('label'=>'X','color'=>'#000000','svg'=>'<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4.6 4h4.2l3.8 5.1L17 4h2l-5.5 6.5L20 20h-4.2l-4.3-5.8L6.5 20h-2l6-7.2L4.6 4Zm3.1 1.5 9 13h1.6l-9-13H7.7Z"/></svg>'),
   'linkedin'=>array('label'=>'LinkedIn','color'=>'#0A66C2','svg'=>'<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6.3 8.2H3.2V21h3.1V8.2ZM4.7 3A1.8 1.8 0 1 0 4.7 6.6 1.8 1.8 0 0 0 4.7 3ZM21 13.7c0-3.9-2.1-5.7-4.9-5.7-2.2 0-3.3 1.2-3.8 2.1V8.2H9.2V21h3.1v-6.3c0-1.7.3-3.3 2.4-3.3 2 0 2.1 1.9 2.1 3.4V21H20l1-7.3Z"/></svg>'),
   'youtube'=>array('label'=>'YouTube','color'=>'#FF0000','svg'=>'<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21.6 7.1a3 3 0 0 0-2.1-2.1C17.7 4.5 12 4.5 12 4.5S6.3 4.5 4.5 5a3 3 0 0 0-2.1 2.1C2 9 2 12 2 12s0 3 .4 4.9A3 3 0 0 0 4.5 19c1.8.5 7.5.5 7.5.5s5.7 0 7.5-.5a3 3 0 0 0 2.1-2.1C22 15 22 12 22 12s0-3-.4-4.9ZM10 15.2V8.8l5.5 3.2-5.5 3.2Z"/></svg>'),
   'tiktok'=>array('label'=>'TikTok','color'=>'#000000','svg'=>'<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14.7 3h3a5.2 5.2 0 0 0 3.3 3.3v3a8.2 8.2 0 0 1-3.3-1v6.2a6.2 6.2 0 1 1-5.4-6.1v3.1a3.2 3.2 0 1 0 2.4 3.1V3Z"/></svg>'),
   'whatsapp'=>array('label'=>'WhatsApp','color'=>'#25D366','svg'=>'<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3a8.8 8.8 0 0 0-7.5 13.4L3.2 21l4.7-1.2A8.9 8.9 0 1 0 12 3Zm4.9 12.4c-.2.6-1.3 1.2-1.8 1.3-.5.1-1.1.2-1.8-.1-.4-.1-.9-.3-1.5-.6-2.7-1.2-4.5-4-4.7-4.2-.1-.2-1.1-1.5-1.1-2.9 0-1.4.7-2.1 1-2.4.3-.3.6-.4.9-.4h.6c.2 0 .4 0 .6.5l.8 2c.1.2.1.4 0 .6l-.3.5-.5.5c-.2.2-.3.4-.1.7.2.3.7 1.2 1.6 1.9 1.1 1 2.1 1.3 2.4 1.5.3.1.5.1.7-.1l.9-1.1c.2-.3.4-.2.7-.1l1.9.9c.3.1.5.2.6.4.1.1.1.6-.1 1.1Z"/></svg>'),
   'telegram'=>array('label'=>'Telegram','color'=>'#229ED9','svg'=>'<svg viewBox="0 0 24 24" aria-hidden="true"><path d="m21 4-3 16-5-4-3 3 .5-4.7L18 7.2 8.8 13 4 11.4 21 4Z"/></svg>'),
   'pinterest'=>array('label'=>'Pinterest','color'=>'#E60023','svg'=>'<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3a9 9 0 0 0-3.3 17.4c-.1-1.4 0-3 .3-4.3l1.2-5s-.3-.7-.3-1.8c0-1.7 1-3 2.3-3 1.1 0 1.6.8 1.6 1.8 0 1.1-.7 2.7-1 4.2-.3 1.3.6 2.3 1.9 2.3 2.3 0 4-2.4 4-5.8 0-3-2.2-5.2-5.3-5.2-3.6 0-5.7 2.7-5.7 5.5 0 1.1.4 2.3.9 2.9.1.1.1.2.1.4l-.4 1.5c-.1.5-.5.6-.9.4-1.7-.8-2.7-3.1-2.7-5 0-4.1 3-7.9 8.9-7.9 4.7 0 8.3 3.3 8.3 7.8 0 4.6-2.9 8.4-7 8.4-1.4 0-2.7-.7-3.1-1.6l-.9 3.3c-.3 1.3-1.2 2.8-1.8 3.8A9 9 0 1 0 12 3Z"/></svg>'),
   'github'=>array('label'=>'GitHub','color'=>'#181717','svg'=>'<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3a9 9 0 0 0-2.8 17.6v-2.2c-2.4.5-2.9-1-2.9-1-.4-1-.9-1.3-.9-1.3-.8-.5 0-.5 0-.5.9.1 1.3.9 1.3.9.8 1.3 2 1 2.5.7.1-.6.3-1 .6-1.2-1.9-.2-3.9-1-3.9-4 0-.9.3-1.6.9-2.2-.1-.2-.4-1 .1-2.2 0 0 .7-.2 2.3.8a8 8 0 0 1 4.2-.6c.7 0 1.4.2 2.1.5 1.6-1 2.3-.8 2.3-.8.5 1.2.2 2 .1 2.2.6.6.9 1.3.9 2.2 0 3.1-2 3.8-3.9 4 .3.3.6.8.6 1.5v3.2A9 9 0 0 0 12 3Z"/></svg>'),
   'dribbble'=>array('label'=>'Dribbble','color'=>'#EA4C89','svg'=>'<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="8.5"/><path d="M6.3 7.3c3.7 1.4 7.5 1.2 11-.6M5.1 12.2c5.6-.1 9.9 1.5 12.7 4.8M9 4.8c2.4 3 4.5 7 5.8 13.7"/></svg>'),
   'behance'=>array('label'=>'Behance','color'=>'#1769FF','svg'=>'<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5h6.2c3 0 4.8 1.3 4.8 3.8 0 1.5-.8 2.6-2 3.1 1.8.5 2.8 1.9 2.8 3.8 0 3-2.4 4.3-5.2 4.3H4V5Zm3 5.8h2.8c1.2 0 2.1-.5 2.1-1.8 0-1.4-1-1.7-2.2-1.7H7v3.5Zm0 6.8h3.2c1.4 0 2.5-.5 2.5-2 0-1.5-1-2.1-2.4-2.1H7v4.1ZM17 7h5v1.7h-5V7Zm5.7 8.5h-5.8c.1 1.7.9 2.5 2.3 2.5 1 0 1.9-.6 2-1.2h1.4c-.5 2.2-1.8 3.1-3.5 3.1-2.4 0-3.9-1.8-3.9-4.5 0-2.6 1.6-4.5 3.9-4.5 2.6 0 3.8 2.4 3.6 4.6Zm-5.8-1.5h4c-.2-1.2-.7-2-1.9-2-1.5 0-2 1.2-2.1 2Z"/></svg>'),
   'discord'=>array('label'=>'Discord','color'=>'#5865F2','svg'=>'<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M18.5 5.3A15 15 0 0 0 14.8 4l-.5 1.1a13.5 13.5 0 0 0-4.6 0L9.2 4a15 15 0 0 0-3.7 1.3C3.2 8.8 2.6 12.2 3 15.6a15 15 0 0 0 4.5 2.3l1.1-1.5a9 9 0 0 1-1.7-.9l.4-.3c3.3 1.5 6.9 1.5 10.2 0l.4.3c-.6.4-1.1.7-1.7.9l1.1 1.5a15 15 0 0 0 4.5-2.3c.5-4-.8-7.3-3.3-10.3ZM9.4 14.4c-1 0-1.8-1-1.8-2.2S8.4 10 9.4 10s1.8 1 1.8 2.2-.8 2.2-1.8 2.2Zm5.2 0c-1 0-1.8-1-1.8-2.2s.8-2.2 1.8-2.2 1.8 1 1.8 2.2-.8 2.2-1.8 2.2Z"/></svg>')
  );
  return isset($map[$platform])?$map[$platform]:array('label'=>'Sosyal Medya','color'=>'#2563eb','svg'=>'');
 }

 protected function render(){
  $s=$this->get_settings_for_display();
  $use_brand='yes'===($s['brand_colors']??'yes');

  echo'<div class="wpst-ew-footer-social">';
  if('yes'===($s['show_title']??'yes') && ''!==trim((string)($s['title']??'')))echo'<h4>'.esc_html($s['title']).'</h4>';
  echo'<div>';

  foreach((array)$s['items'] as $i){
   $platform=sanitize_key($i['platform']??'custom');
   $data=$this->platform_data($platform);
   $label='custom'===$platform ? trim((string)($i['label']??'Sosyal Medya')) : $data['label'];
   if(''===$label)$label='Sosyal Medya';

   $brand_color='custom'===$platform && !empty($i['custom_color']) ? $i['custom_color'] : $data['color'];
   $url=!empty($i['url']['url'])?$i['url']['url']:'#';
   $target=!empty($i['url']['is_external'])?' target="_blank"':'';
   $rel=!empty($i['url']['nofollow'])?' rel="nofollow noopener"':(!empty($i['url']['is_external'])?' rel="noopener"':'');
   $style=$use_brand?' style="--wpst-social-brand:'.esc_attr($brand_color).';"':'';

   echo'<a class="wpst-social-link wpst-social-'.esc_attr($platform).'" href="'.esc_url($url).'"'.$target.$rel.$style.' aria-label="'.esc_attr($label).'" data-platform="'.esc_attr($platform).'">';
   echo'<b class="wpst-social-icon">';

   if('custom'===$platform){
    echo class_exists('WPST_Icon_Library')?WPST_Icon_Library::svg(!empty($i['wpst_icon'])?$i['wpst_icon']:'globe',array('size'=>15)):'';
   }else{
    echo $data['svg']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
   }

   echo'</b><span>'.esc_html($label).'</span></a>';
  }

  echo'</div></div>';
 }
}
