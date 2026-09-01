<?php
if(!defined('ABSPATH'))exit;

class WPST_Widget_Image_Slider extends WPST_Elementor_Widget_Base{
 public function get_name(){return'wpsoft-image-slider';}
 public function get_title(){return'WPSoft · Görsel Slider';}
 public function get_icon(){return'eicon-slider-push';}
 public function get_categories(){return array('wpsoft-media','wpsoft');}
 public function get_keywords(){return array('image','slider','gallery','carousel','görsel','slayt');}

 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'Görseller'));
  $this->wpst_signature_preset_control();

  $r=new \Elementor\Repeater();
  $r->add_control('image',array(
   'label'=>'Görsel',
   'type'=>\Elementor\Controls_Manager::MEDIA,
   'default'=>array()
  ));
  $r->add_control('link',array(
   'label'=>'Görsel Linki',
   'type'=>\Elementor\Controls_Manager::URL,
   'placeholder'=>'https://',
   'options'=>array('url','is_external','nofollow','custom_attributes'),
   'default'=>array('url'=>'')
  ));
  $r->add_control('alt',array(
   'label'=>'Alt Metin',
   'type'=>\Elementor\Controls_Manager::TEXT,
   'default'=>''
  ));
  $this->add_control('linked_images',array(
   'label'=>'Görseller',
   'type'=>\Elementor\Controls_Manager::REPEATER,
   'fields'=>$r->get_controls(),
   'title_field'=>'{{{ alt || "Görsel" }}}',
   'default'=>array(),
   'description'=>'Slider görsellerini buradan ekleyin. Her görsele isteğe bağlı ayrı bağlantı verebilirsiniz.'
  ));

  $this->add_control('autoplay',array(
   'label'=>'Otomatik Oynat',
   'type'=>\Elementor\Controls_Manager::SWITCHER,
   'return_value'=>'yes',
   'default'=>'yes'
  ));

  $this->add_control('delay',array(
   'label'=>'Geçiş Süresi (ms)',
   'type'=>\Elementor\Controls_Manager::NUMBER,
   'default'=>4500,
   'min'=>1500,
   'max'=>15000,
   'step'=>500,
   'condition'=>array('autoplay'=>'yes')
  ));

  $this->add_control('pause_hover',array(
   'label'=>'Hover’da Duraklat',
   'type'=>\Elementor\Controls_Manager::SWITCHER,
   'return_value'=>'yes',
   'default'=>'yes',
   'condition'=>array('autoplay'=>'yes')
  ));

  $this->add_control('touch_swipe',array(
   'label'=>'Dokunmatik Kaydırma',
   'type'=>\Elementor\Controls_Manager::SWITCHER,
   'return_value'=>'yes',
   'default'=>'yes',
   'description'=>'Mobil, tablet ve dokunmatik ekranlarda parmakla sağa/sola kaydırmayı etkinleştirir.'
  ));

  $this->add_control('mouse_drag',array(
   'label'=>'Masaüstünde Mouse ile Sürükle',
   'type'=>\Elementor\Controls_Manager::SWITCHER,
   'return_value'=>'yes',
   'default'=>'yes',
   'description'=>'Masaüstünde görseli tutup sağa/sola sürükleyerek slayt değiştirmeyi etkinleştirir.'
  ));

  $this->add_control('show_arrows',array(
   'label'=>'Okları Göster',
   'type'=>\Elementor\Controls_Manager::SWITCHER,
   'return_value'=>'yes',
   'default'=>'yes'
  ));

  $this->add_control('show_dots',array(
   'label'=>'Noktaları Göster',
   'type'=>\Elementor\Controls_Manager::SWITCHER,
   'return_value'=>'yes',
   'default'=>'yes'
  ));

  $this->add_control('show_progress',array(
   'label'=>'İlerleme Çizgisi',
   'type'=>\Elementor\Controls_Manager::SWITCHER,
   'return_value'=>'yes',
   'default'=>''
  ));

  $this->end_controls_section();

  $this->start_controls_section('style_media',array(
   'label'=>'Görsel',
   'tab'=>\Elementor\Controls_Manager::TAB_STYLE
  ));

  $this->add_responsive_control('height',array(
   'label'=>'Görsel Yüksekliği',
   'type'=>\Elementor\Controls_Manager::SLIDER,
   'size_units'=>array('px','vh'),
   'range'=>array(
    'px'=>array('min'=>180,'max'=>1100),
    'vh'=>array('min'=>20,'max'=>100)
   ),
   'default'=>array('unit'=>'px','size'=>520),
   'tablet_default'=>array('unit'=>'px','size'=>420),
   'mobile_default'=>array('unit'=>'px','size'=>240),
   'selectors'=>array(
    '{{WRAPPER}} .wpst-ew-image-slider'=>'--wpst-image-slider-height:{{SIZE}}{{UNIT}};'
   )
  ));

  $this->add_control('fit',array(
   'label'=>'Görsel Yerleşimi',
   'type'=>\Elementor\Controls_Manager::SELECT,
   'default'=>'cover',
   'options'=>array(
    'cover'=>'Cover / Kırp',
    'contain'=>'Contain / Tam Göster'
   ),
   'selectors'=>array(
    '{{WRAPPER}} .wpst-ew-image-slider img'=>'object-fit:{{VALUE}};'
   )
  ));

  $this->add_responsive_control('image_position',array(
   'label'=>'Görsel Konumu',
   'type'=>\Elementor\Controls_Manager::SELECT,
   'default'=>'center center',
   'tablet_default'=>'center center',
   'mobile_default'=>'center center',
   'options'=>array(
    'left center'=>'Sol',
    'center center'=>'Orta',
    'right center'=>'Sağ',
    'center top'=>'Üst',
    'center bottom'=>'Alt'
   ),
   'selectors'=>array(
    '{{WRAPPER}} .wpst-ew-image-slider img'=>'object-position:{{VALUE}};'
   )
  ));

  $this->add_responsive_control('radius',array(
   'label'=>'Köşe Yuvarlaklığı',
   'type'=>\Elementor\Controls_Manager::SLIDER,
   'range'=>array('px'=>array('min'=>0,'max'=>60)),
   'default'=>array('unit'=>'px','size'=>22),
   'selectors'=>array(
    '{{WRAPPER}} .wpst-ew-image-slider'=>'border-radius:{{SIZE}}px;'
   )
  ));

  $this->end_controls_section();

  $this->start_controls_section('style_controls',array(
   'label'=>'Slider Kontrolleri',
   'tab'=>\Elementor\Controls_Manager::TAB_STYLE
  ));

  $this->add_control('arrow_style',array(
   'label'=>'Ok Buton Stili',
   'type'=>\Elementor\Controls_Manager::SELECT,
   'default'=>'glass',
   'options'=>array(
    'glass'=>'Glass',
    'solid'=>'Dolu',
    'outline'=>'Çerçeveli',
    'minimal'=>'Minimal',
    'square'=>'Kare'
   ),
   'prefix_class'=>'wpst-image-slider-arrow-',
   'condition'=>array('show_arrows'=>'yes')
  ));

  $this->add_responsive_control('arrow_size',array(
   'label'=>'Ok Buton Boyutu',
   'type'=>\Elementor\Controls_Manager::SLIDER,
   'range'=>array('px'=>array('min'=>30,'max'=>72)),
   'default'=>array('unit'=>'px','size'=>46),
   'tablet_default'=>array('unit'=>'px','size'=>44),
   'mobile_default'=>array('unit'=>'px','size'=>40),
   'selectors'=>array(
    '{{WRAPPER}} .wpst-image-slider-prev, {{WRAPPER}} .wpst-image-slider-next'=>'width:{{SIZE}}px;height:{{SIZE}}px;'
   ),
   'condition'=>array('show_arrows'=>'yes')
  ));

  $this->add_responsive_control('arrow_icon_size',array(
   'label'=>'Ok İkon Boyutu',
   'type'=>\Elementor\Controls_Manager::SLIDER,
   'range'=>array('px'=>array('min'=>10,'max'=>32)),
   'default'=>array('unit'=>'px','size'=>17),
   'tablet_default'=>array('unit'=>'px','size'=>16),
   'mobile_default'=>array('unit'=>'px','size'=>15),
   'selectors'=>array(
    '{{WRAPPER}} .wpst-image-slider-prev svg, {{WRAPPER}} .wpst-image-slider-next svg'=>'width:{{SIZE}}px;height:{{SIZE}}px;'
   ),
   'condition'=>array('show_arrows'=>'yes')
  ));

  $this->add_responsive_control('arrow_side_offset',array(
   'label'=>'Ok Kenar Mesafesi',
   'type'=>\Elementor\Controls_Manager::SLIDER,
   'range'=>array('px'=>array('min'=>4,'max'=>80)),
   'default'=>array('unit'=>'px','size'=>18),
   'tablet_default'=>array('unit'=>'px','size'=>14),
   'mobile_default'=>array('unit'=>'px','size'=>10),
   'selectors'=>array(
    '{{WRAPPER}} .wpst-image-slider-prev'=>'left:{{SIZE}}px;',
    '{{WRAPPER}} .wpst-image-slider-next'=>'right:{{SIZE}}px;'
   ),
   'condition'=>array('show_arrows'=>'yes')
  ));

  $this->add_control('arrow_color',array(
   'label'=>'Ok Rengi',
   'type'=>\Elementor\Controls_Manager::COLOR,
   'default'=>'#ffffff',
   'selectors'=>array(
    '{{WRAPPER}} .wpst-ew-image-slider'=>'--wpst-image-slider-arrow-color:{{VALUE}}!important;'
   ),
   'condition'=>array('show_arrows'=>'yes')
  ));

  $this->add_control('arrow_bg',array(
   'label'=>'Ok Arka Planı',
   'type'=>\Elementor\Controls_Manager::COLOR,
   'default'=>'rgba(15,23,42,.34)',
   'selectors'=>array(
    '{{WRAPPER}} .wpst-ew-image-slider'=>'--wpst-image-slider-arrow-bg:{{VALUE}};'
   ),
   'condition'=>array('show_arrows'=>'yes')
  ));

  $this->add_control('arrow_border_color',array(
   'label'=>'Ok Border Rengi',
   'type'=>\Elementor\Controls_Manager::COLOR,
   'default'=>'rgba(255,255,255,.40)',
   'selectors'=>array(
    '{{WRAPPER}} .wpst-ew-image-slider'=>'--wpst-image-slider-arrow-border:{{VALUE}};'
   ),
   'condition'=>array('show_arrows'=>'yes')
  ));

  $this->add_control('arrow_hover_color',array(
   'label'=>'Ok Hover Rengi',
   'type'=>\Elementor\Controls_Manager::COLOR,
   'default'=>'#ffffff',
   'selectors'=>array(
    '{{WRAPPER}} .wpst-ew-image-slider'=>'--wpst-image-slider-arrow-hover-color:{{VALUE}}!important;'
   ),
   'condition'=>array('show_arrows'=>'yes')
  ));

  $this->add_control('arrow_hover_bg',array(
   'label'=>'Ok Hover Arka Planı',
   'type'=>\Elementor\Controls_Manager::COLOR,
   'default'=>'rgba(15,23,42,.62)',
   'selectors'=>array(
    '{{WRAPPER}} .wpst-ew-image-slider'=>'--wpst-image-slider-arrow-hover-bg:{{VALUE}};'
   ),
   'condition'=>array('show_arrows'=>'yes')
  ));

  $this->add_control('dots_style',array(
   'label'=>'Nokta Stili',
   'type'=>\Elementor\Controls_Manager::SELECT,
   'default'=>'pill',
   'options'=>array(
    'dot'=>'Nokta',
    'pill'=>'Aktif Pill',
    'line'=>'Çizgi',
    'boxed'=>'Kutulu'
   ),
   'prefix_class'=>'wpst-image-slider-dots-',
   'condition'=>array('show_dots'=>'yes')
  ));

  $this->add_responsive_control('dot_size',array(
   'label'=>'Nokta Boyutu',
   'type'=>\Elementor\Controls_Manager::SLIDER,
   'range'=>array('px'=>array('min'=>4,'max'=>18)),
   'default'=>array('unit'=>'px','size'=>6),
   'tablet_default'=>array('unit'=>'px','size'=>6),
   'mobile_default'=>array('unit'=>'px','size'=>5),
   'selectors'=>array(
    '{{WRAPPER}} .wpst-image-slider-dots'=>'--wpst-image-slider-dot-size:{{SIZE}}px;'
   ),
   'condition'=>array('show_dots'=>'yes')
  ));

  $this->add_responsive_control('dot_gap',array(
   'label'=>'Nokta Aralığı',
   'type'=>\Elementor\Controls_Manager::SLIDER,
   'range'=>array('px'=>array('min'=>2,'max'=>20)),
   'default'=>array('unit'=>'px','size'=>5),
   'tablet_default'=>array('unit'=>'px','size'=>5),
   'mobile_default'=>array('unit'=>'px','size'=>4),
   'selectors'=>array(
    '{{WRAPPER}} .wpst-image-slider-dots'=>'gap:{{SIZE}}px;'
   ),
   'condition'=>array('show_dots'=>'yes')
  ));

  $this->add_responsive_control('dots_bottom',array(
   'label'=>'Noktaların Alt Mesafesi',
   'type'=>\Elementor\Controls_Manager::SLIDER,
   'range'=>array('px'=>array('min'=>6,'max'=>80)),
   'default'=>array('unit'=>'px','size'=>18),
   'tablet_default'=>array('unit'=>'px','size'=>16),
   'mobile_default'=>array('unit'=>'px','size'=>14),
   'selectors'=>array(
    '{{WRAPPER}} .wpst-image-slider-dots'=>'bottom:{{SIZE}}px;'
   ),
   'condition'=>array('show_dots'=>'yes')
  ));

  $this->add_control('dot_color',array(
   'label'=>'Nokta Rengi',
   'type'=>\Elementor\Controls_Manager::COLOR,
   'default'=>'rgba(255,255,255,.50)',
   'selectors'=>array(
    '{{WRAPPER}} .wpst-ew-image-slider'=>'--wpst-image-slider-dot-color:{{VALUE}}!important;'
   ),
   'condition'=>array('show_dots'=>'yes')
  ));

  $this->add_control('dot_active_color',array(
   'label'=>'Aktif Nokta Rengi',
   'type'=>\Elementor\Controls_Manager::COLOR,
   'default'=>'#ffffff',
   'selectors'=>array(
    '{{WRAPPER}} .wpst-ew-image-slider'=>'--wpst-image-slider-dot-active:{{VALUE}};'
   ),
   'condition'=>array('show_dots'=>'yes')
  ));

  $this->add_control('dots_bg',array(
   'label'=>'Nokta Alanı Arka Planı',
   'type'=>\Elementor\Controls_Manager::COLOR,
   'default'=>'rgba(15,23,42,.26)',
   'selectors'=>array(
    '{{WRAPPER}} .wpst-ew-image-slider'=>'--wpst-image-slider-dots-bg:{{VALUE}};'
   ),
   'condition'=>array('show_dots'=>'yes')
  ));

  $this->end_controls_section();

  $this->standard_responsive_controls();
 }

 protected function render(){
  $s=$this->get_settings_for_display();
  $linked=!empty($s['linked_images'])?(array)$s['linked_images']:array();
  if(!$linked)return;

  echo'<div class="wpst-ew-image-slider"'
   .' data-autoplay="'.esc_attr($s['autoplay']??'').'"'
   .' data-delay="'.absint($s['delay']??4500).'"'
   .' data-pause-hover="'.esc_attr($s['pause_hover']??'yes').'"'
   .' data-touch-swipe="'.esc_attr($s['touch_swipe']??'yes').'"'
   .' data-mouse-drag="'.esc_attr($s['mouse_drag']??'yes').'"'
   .' tabindex="0" role="region" aria-roledescription="carousel" aria-label="Görsel slider">';

  echo'<div class="wpst-image-slider-track">';
  $n=0;

   foreach($linked as $item){
    $img=!empty($item['image'])?(array)$item['image']:array();
    $url=!empty($img['url'])?$img['url']:'';
    if(!$url)continue;

    $alt=!empty($item['alt'])?$item['alt']:'';
    if(!$alt && !empty($img['id']))$alt=get_post_meta((int)$img['id'],'_wp_attachment_image_alt',true);

    $link=!empty($item['link'])?(array)$item['link']:array();
    $href=!empty($link['url'])?$link['url']:'';
    echo'<figure class="wpst-image-slider-slide'.($n===0?' is-active':'').'" aria-hidden="'.($n===0?'false':'true').'">';
    if($href)echo'<a class="wpst-image-slider-link"'.$this->render_link_attrs($link).' aria-label="'.esc_attr($alt?:'Görsel bağlantısı').'">';
    if(!empty($img['id'])){
     echo wp_get_attachment_image((int)$img['id'],'full',false,array('alt'=>$alt,'loading'=>($n===0?'eager':'lazy'),'decoding'=>'async'));
    }else{
     echo'<img src="'.esc_url($url).'" alt="'.esc_attr($alt).'" loading="'.($n===0?'eager':'lazy').'" decoding="async">';
    }
    if($href)echo'</a>';
    echo'</figure>';
    $n++;
   }

  echo'</div>';

  if('yes'===($s['show_arrows']??'yes') && $n>1){
   echo'<button type="button" class="wpst-image-slider-prev" aria-label="Önceki görsel">'.(class_exists('WPST_Icon_Library')?WPST_Icon_Library::svg('arrow-left',array('size'=>17)):'←').'</button>';
   echo'<button type="button" class="wpst-image-slider-next" aria-label="Sonraki görsel">'.(class_exists('WPST_Icon_Library')?WPST_Icon_Library::svg('arrow-right',array('size'=>17)):'→').'</button>';
  }

  if('yes'===($s['show_dots']??'yes') && $n>1)echo'<div class="wpst-image-slider-dots"></div>';
  if('yes'===($s['show_progress']??'') && $n>1)echo'<div class="wpst-image-slider-progress" aria-hidden="true"><span></span></div>';

  echo'</div>';
 }
}
