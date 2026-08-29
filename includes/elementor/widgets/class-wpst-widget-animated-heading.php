<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class WPST_Widget_Animated_Heading extends WPST_Elementor_Widget_Base {
 public function get_name(){return 'wpsoft-animated-heading';}
 public function get_title(){return 'WPSoft Animasyonlu Başlık 2.0';}
 public function get_icon(){return 'eicon-animated-headline';}

 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'İçerik'));
  $this->add_control('before',array('label'=>'Başlangıç','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'İşletmeniz için'));
  $this->add_control('words',array('label'=>'Dönen Kelimeler','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>"modern\nhızlı\ngüçlü"));
  $this->add_control('after',array('label'=>'Bitiş','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'web çözümleri'));
  $this->add_control('html_tag',array(
   'label'=>'HTML Etiketi','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'h2',
   'options'=>array('h1'=>'H1','h2'=>'H2','h3'=>'H3','h4'=>'H4','div'=>'DIV','p'=>'P')
  ));
  $this->add_control('animation_type',array(
   'label'=>'Animasyon Türü','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'slide',
   'options'=>array(
    'slide'=>'Slide',
    'fade'=>'Fade',
    'scale'=>'Scale',
    'blur'=>'Blur',
    'flip'=>'Flip',
    'typing'=>'Typing'
   ),
   'prefix_class'=>'wpst-animated-type-'
  ));
  $this->add_control('speed',array(
   'label'=>'Kelime Değişim Süresi (ms)','type'=>\Elementor\Controls_Manager::NUMBER,
   'default'=>2200,'min'=>700,'max'=>10000,'step'=>100
  ));
  $this->add_control('transition_speed',array(
   'label'=>'Geçiş Hızı (ms)','type'=>\Elementor\Controls_Manager::NUMBER,
   'default'=>320,'min'=>120,'max'=>1200,'step'=>20
  ));
  $this->add_control('pause_hover',array(
   'label'=>'Hover Üzerinde Duraklat','type'=>\Elementor\Controls_Manager::SWITCHER,
   'return_value'=>'yes','default'=>'yes'
  ));
  $this->add_control('reduce_mobile_motion',array(
   'label'=>'Mobilde Animasyonu Sadeleştir','type'=>\Elementor\Controls_Manager::SWITCHER,
   'return_value'=>'yes','default'=>'yes'
  ));
  $this->end_controls_section();

  $this->start_controls_section('layout',array('label'=>'Düzen'));
  $this->add_responsive_control('align',array(
   'label'=>'Hizalama','type'=>\Elementor\Controls_Manager::CHOOSE,
   'options'=>array(
    'left'=>array('title'=>'Sol','icon'=>'eicon-text-align-left'),
    'center'=>array('title'=>'Orta','icon'=>'eicon-text-align-center'),
    'right'=>array('title'=>'Sağ','icon'=>'eicon-text-align-right')
   ),
   'default'=>'left','tablet_default'=>'left','mobile_default'=>'left',
   'selectors'=>array('{{WRAPPER}} .wpst-ew-animated-heading'=>'text-align:{{VALUE}};')
  ));
  $this->add_responsive_control('max_width',array(
   'label'=>'Maksimum Genişlik','type'=>\Elementor\Controls_Manager::SLIDER,
   'range'=>array('px'=>array('min'=>280,'max'=>1600)),
   'selectors'=>array('{{WRAPPER}}'=>'--wpst-ah-max-width:{{SIZE}}px;')
  ));
  $this->add_responsive_control('word_min_width',array(
   'label'=>'Animasyon Kelime Min. Genişliği','type'=>\Elementor\Controls_Manager::SLIDER,
   'range'=>array('px'=>array('min'=>0,'max'=>700)),
   'selectors'=>array('{{WRAPPER}}'=>'--wpst-ah-word-min:{{SIZE}}px;')
  ));
  $this->add_control('word_display',array(
   'label'=>'Kelime Akışı','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'inline',
   'options'=>array('inline'=>'Satır İçinde','block'=>'Yeni Satırda'),
   'prefix_class'=>'wpst-animated-word-display-'
  ));
  $this->end_controls_section();

  $this->start_controls_section('style',array('label'=>'Biçim · Başlık','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->wpst_signature_preset_control('animated_heading_preset');

  $this->add_control('color',array(
   'label'=>'Metin Rengi','type'=>\Elementor\Controls_Manager::COLOR,
   'selectors'=>array('{{WRAPPER}}'=>'--wpst-ah-color:{{VALUE}};')
  ));
  $this->add_control('accent',array(
   'label'=>'Animasyon Rengi','type'=>\Elementor\Controls_Manager::COLOR,
   'selectors'=>array('{{WRAPPER}}'=>'--wpst-ah-accent:{{VALUE}};')
  ));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array(
   'name'=>'typography','selector'=>'{{WRAPPER}} .wpst-ew-animated-heading'
  ));
  $this->add_responsive_control('font_size',array(
   'label'=>'Başlık Boyutu','type'=>\Elementor\Controls_Manager::SLIDER,
   'size_units'=>array('px','vw'),
   'range'=>array('px'=>array('min'=>20,'max'=>160),'vw'=>array('min'=>2,'max'=>14,'step'=>.1)),
   'selectors'=>array('{{WRAPPER}}'=>'--wpst-ah-size:{{SIZE}}{{UNIT}};')
  ));
  $this->add_responsive_control('line_height',array(
   'label'=>'Satır Yüksekliği','type'=>\Elementor\Controls_Manager::SLIDER,
   'size_units'=>array('em'),
   'range'=>array('em'=>array('min'=>.75,'max'=>1.8,'step'=>.05)),
   'selectors'=>array('{{WRAPPER}}'=>'--wpst-ah-line:{{SIZE}}{{UNIT}};')
  ));
  $this->add_responsive_control('letter_spacing',array(
   'label'=>'Harf Aralığı','type'=>\Elementor\Controls_Manager::SLIDER,
   'size_units'=>array('px','em'),
   'range'=>array('px'=>array('min'=>-6,'max'=>20,'step'=>.1),'em'=>array('min'=>-.12,'max'=>.5,'step'=>.01)),
   'selectors'=>array('{{WRAPPER}}'=>'--wpst-ah-letter:{{SIZE}}{{UNIT}};')
  ));
  $this->end_controls_section();

  $this->start_controls_section('word_style',array('label'=>'Biçim · Animasyonlu Kelime','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('highlight_style',array(
   'label'=>'Vurgu Stili','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'underline',
   'options'=>array(
    'none'=>'Yok',
    'underline'=>'Alt Çizgi',
    'marker'=>'Marker',
    'pill'=>'Pill',
    'gradient'=>'Gradient Yazı'
   ),
   'prefix_class'=>'wpst-animated-highlight-'
  ));
  $this->add_control('highlight_bg',array(
   'label'=>'Vurgu Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,
   'selectors'=>array('{{WRAPPER}}'=>'--wpst-ah-highlight-bg:{{VALUE}};')
  ));
  $this->add_control('underline_thickness',array(
   'label'=>'Alt Çizgi Kalınlığı','type'=>\Elementor\Controls_Manager::SLIDER,
   'range'=>array('px'=>array('min'=>1,'max'=>16)),
   'default'=>array('size'=>5),
   'selectors'=>array('{{WRAPPER}}'=>'--wpst-ah-underline:{{SIZE}}px;'),
   'condition'=>array('highlight_style'=>'underline')
  ));
  $this->add_responsive_control('word_padding',array(
   'label'=>'Kelime İç Boşluğu','type'=>\Elementor\Controls_Manager::DIMENSIONS,
   'size_units'=>array('px'),
   'selectors'=>array('{{WRAPPER}} .wpst-ew-animated-word'=>'padding:{{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;')
  ));
  $this->add_responsive_control('word_radius',array(
   'label'=>'Kelime Köşesi','type'=>\Elementor\Controls_Manager::SLIDER,
   'range'=>array('px'=>array('min'=>0,'max'=>999)),
   'selectors'=>array('{{WRAPPER}}'=>'--wpst-ah-word-radius:{{SIZE}}px;')
  ));
  $this->end_controls_section();

  $this->standard_responsive_controls();
 }

 protected function render(){
  $s=$this->get_settings_for_display();
  $words=array_values(array_filter(array_map('trim',preg_split('/\r\n|\r|\n/',(string)($s['words']??'')))));
  if(!$words)$words=array('');

  $tag=in_array(($s['html_tag']??'h2'),array('h1','h2','h3','h4','div','p'),true)?$s['html_tag']:'h2';

  echo '<'.$tag.' class="wpst-ew-animated-heading"';
  echo ' data-speed="'.esc_attr((int)($s['speed']??2200)).'"';
  echo ' data-transition="'.esc_attr((int)($s['transition_speed']??320)).'"';
  echo ' data-animation="'.esc_attr($s['animation_type']??'slide').'"';
  echo ' data-pause-hover="'.('yes'===($s['pause_hover']??'yes')?'1':'0').'"';
  echo ' data-reduce-mobile="'.('yes'===($s['reduce_mobile_motion']??'yes')?'1':'0').'"';
  echo ' data-words="'.esc_attr(wp_json_encode($words)).'">';

  if(!empty($s['before'])) echo '<span class="wpst-ah-before">'.esc_html($s['before']).'</span> ';
  echo '<strong class="wpst-ew-animated-word" aria-live="polite">'.esc_html($words[0]).'</strong>';
  if(!empty($s['after'])) echo ' <span class="wpst-ah-after">'.esc_html($s['after']).'</span>';

  echo '</'.$tag.'>';
 }
}
