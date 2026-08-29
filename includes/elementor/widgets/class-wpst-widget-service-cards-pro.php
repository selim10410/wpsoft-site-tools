<?php
if(!defined('ABSPATH'))exit;
class WPST_Widget_Service_Cards_Pro extends WPST_Elementor_Widget_Base{
 public function get_name(){return'wpsoft-service-cards-pro';}
 public function get_title(){return'WPSoft · Services Grid Pro 3.0';}
 public function get_icon(){return'eicon-info-box';}
 public function get_keywords(){return array('services','service','hizmet','grid','cards','icon','image');}

 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'Hizmetler'));
  $this->wpst_signature_preset_control();

  $r=new \Elementor\Repeater();
  $r->add_control('wpst_icon',array(
   'label'=>'WPSoft Icon','type'=>\Elementor\Controls_Manager::SELECT2,
   'options'=>class_exists('WPST_Icon_Library')?WPST_Icon_Library::options():array(),
   'default'=>'sparkles','label_block'=>true
  ));
  $r->add_control('icon',array('label'=>'Eski Simge Metni','type'=>\Elementor\Controls_Manager::TEXT,'default'=>''));
  $r->add_control('image',array('label'=>'Görsel','type'=>\Elementor\Controls_Manager::MEDIA));
  $r->add_control('tag',array('label'=>'No / Etiket','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'01'));
  $r->add_control('badge',array('label'=>'Badge','type'=>\Elementor\Controls_Manager::TEXT,'default'=>''));
  $r->add_control('title',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Hizmet'));
  $r->add_control('text',array('label'=>'Açıklama','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'Hizmetinizi kısa ve net biçimde açıklayın.'));
  $r->add_control('url',array('label'=>'Bağlantı','type'=>\Elementor\Controls_Manager::URL,'default'=>array('url'=>'#')));
  $this->add_control('items',array(
   'label'=>'Hizmet Kartları','type'=>\Elementor\Controls_Manager::REPEATER,'fields'=>$r->get_controls(),
   'default'=>array(
    array('wpst_icon'=>'target','title'=>'Strateji','text'=>'Hedef, konumlandırma ve doğru yol haritası.','tag'=>'01','badge'=>'Planlama','url'=>array('url'=>'#')),
    array('wpst_icon'=>'palette','title'=>'UI / UX Tasarım','text'=>'Modern, erişilebilir ve dönüşüm odaklı deneyimler.','tag'=>'02','badge'=>'Tasarım','url'=>array('url'=>'#')),
    array('wpst_icon'=>'code','title'=>'Geliştirme','text'=>'Hızlı, ölçeklenebilir ve yönetilebilir altyapı.','tag'=>'03','badge'=>'Teknoloji','url'=>array('url'=>'#')),
    array('wpst_icon'=>'chart','title'=>'Büyüme','text'=>'SEO, performans ve ölçülebilir büyüme sistemi.','tag'=>'04','badge'=>'Growth','url'=>array('url'=>'#'))
   ),
   'title_field'=>'{{{ title }}}'
  ));

  $this->add_control('action_text',array('label'=>'Aksiyon Yazısı','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Hizmeti İncele'));
  $this->add_control('action_icon',array(
   'label'=>'Aksiyon Icon','type'=>\Elementor\Controls_Manager::SELECT2,
   'options'=>class_exists('WPST_Icon_Library')?WPST_Icon_Library::options():array(),
   'default'=>'arrow-up-right','label_block'=>true
  ));
  $this->add_control('action_align',array(
   'label'=>'Aksiyon Hizası',
   'type'=>\Elementor\Controls_Manager::CHOOSE,
   'options'=>array(
    'flex-start'=>array('title'=>'Sol','icon'=>'eicon-h-align-left'),
    'center'=>array('title'=>'Orta','icon'=>'eicon-h-align-center'),
    'flex-end'=>array('title'=>'Sağ','icon'=>'eicon-h-align-right')
   ),
   'default'=>'center',
   'selectors'=>array(
    '{{WRAPPER}} .wpst-service-action'=>'justify-content:{{VALUE}}!important;text-align:center;width:100%;'
   )
  ));
  $this->add_control('action_style',array(
   'label'=>'Aksiyon Stili',
   'type'=>\Elementor\Controls_Manager::SELECT,
   'default'=>'line',
   'options'=>array(
    'line'=>'Minimal Çizgi',
    'soft'=>'Soft',
    'pill'=>'Pill',
    'plain'=>'Sade'
   ),
   'prefix_class'=>'wpst-service-action-style-'
  ));
  $this->add_control('show_icons',array('label'=>'Iconları Göster','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes','prefix_class'=>'wpst-service-icons-'));
  $this->add_control('show_numbers',array('label'=>'No / Etiket Göster','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes'));
  $this->add_control('show_badges',array('label'=>'Badge Göster','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes'));
  $this->add_control('show_images',array('label'=>'Görselleri Göster','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes'));
  $this->end_controls_section();

  $this->start_controls_section('layout_section',array('label'=>'Grid & Kompozisyon'));
  $this->add_control('layout_variant',array(
   'label'=>'Yerleşim','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'modern',
   'options'=>array(
    'modern'=>'Modern Grid',
    'image'=>'Image Cards',
    'editorial'=>'Editorial Index',
    'bento'=>'Bento Services',
    'minimal'=>'Minimal Lines',
    'dark'=>'Dark Expertise',
    'horizontal'=>'Legacy · Horizontal Rows',
    'editorial-old'=>'Legacy · Editorial',
    'icon-top'=>'Legacy · Icon Top',
    'compact'=>'Legacy · Compact List'
   ),
   'prefix_class'=>'wpst-service-layout-'
  ));
  $this->add_control('card_style',array(
   'label'=>'Kart Yüzeyi','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'elevated',
   'options'=>array('flat'=>'Flat','elevated'=>'Elevated','soft'=>'Soft','glass'=>'Glass','outline'=>'Outline','minimal'=>'Legacy · Minimal','dark'=>'Legacy · Dark'),
   'prefix_class'=>'wpst-service-style-'
  ));
  $this->add_responsive_control('columns',array(
   'label'=>'Kolon','type'=>\Elementor\Controls_Manager::SELECT,
   'default'=>'3','tablet_default'=>'2','mobile_default'=>'1',
   'options'=>array('1'=>'1','2'=>'2','3'=>'3','4'=>'4'),
   'selectors'=>array('{{WRAPPER}} .wpst-ew-service-cards-pro'=>'grid-template-columns:repeat({{VALUE}},minmax(0,1fr))!important;')
  ));
  $this->add_responsive_control('gap',array(
   'label'=>'Kart Aralığı','type'=>\Elementor\Controls_Manager::SLIDER,'size_units'=>array('px'),
   'range'=>array('px'=>array('min'=>0,'max'=>70)),'default'=>array('unit'=>'px','size'=>20),
   'selectors'=>array('{{WRAPPER}} .wpst-ew-service-cards-pro'=>'gap:{{SIZE}}{{UNIT}};')
  ));
  $this->add_control('equal_height',array('label'=>'Eşit Kart Yüksekliği','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes','prefix_class'=>'wpst-service-equal-'));
  $this->end_controls_section();

  $this->start_controls_section('style_card',array('label'=>'Kart Biçimi','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('card_bg',array('label'=>'Kart Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-service-card'=>'--svc-bg:{{VALUE}};')));
  $this->add_control('card_border',array('label'=>'Kenarlık','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-service-card'=>'--svc-border:{{VALUE}};')));
  $this->add_control('accent',array('label'=>'Vurgu Rengi','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#315cf5','selectors'=>array('{{WRAPPER}} .wpst-service-card'=>'--svc-accent:{{VALUE}};')));
  $this->add_control('title_color',array('label'=>'Başlık Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-service-card h3'=>'color:{{VALUE}}!important;')));
  $this->add_control('text_color',array('label'=>'Metin Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-service-card p'=>'color:{{VALUE}}!important;')));
  $this->add_responsive_control('card_padding',array(
   'label'=>'İç Boşluk','type'=>\Elementor\Controls_Manager::DIMENSIONS,'size_units'=>array('px'),
   'selectors'=>array('{{WRAPPER}} .wpst-service-card-content'=>'padding:{{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;')
  ));
  $this->add_responsive_control('card_radius',array(
   'label'=>'Köşe','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>60)),
   'default'=>array('size'=>24),'selectors'=>array('{{WRAPPER}} .wpst-service-card'=>'border-radius:{{SIZE}}px;')
  ));
  $this->add_control('hover_effect',array(
   'label'=>'Hover','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'lift',
   'options'=>array('none'=>'Yok','lift'=>'Lift','zoom'=>'Media Zoom','border'=>'Border','glow'=>'Glow'),
   'prefix_class'=>'wpst-service-hover-'
  ));
  $this->end_controls_section();

  $this->start_controls_section('style_media',array('label'=>'Icon & Görsel','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_responsive_control('icon_box',array(
   'label'=>'Icon Alanı','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>32,'max'=>100)),
   'default'=>array('size'=>52),'selectors'=>array('{{WRAPPER}} .wpst-service-icon'=>'width:{{SIZE}}px;height:{{SIZE}}px;')
  ));
  $this->add_responsive_control('icon_size',array(
   'label'=>'Icon Boyutu','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>12,'max'=>50)),
   'default'=>array('size'=>22),'selectors'=>array('{{WRAPPER}} .wpst-service-icon svg'=>'width:{{SIZE}}px;height:{{SIZE}}px;')
  ));
  $this->add_control('icon_bg',array('label'=>'Icon Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-service-icon'=>'background:{{VALUE}}!important;')));
  $this->add_control('icon_color',array('label'=>'Icon Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-service-icon'=>'color:{{VALUE}}!important;')));
  $this->add_responsive_control('image_height',array(
   'label'=>'Görsel Yüksekliği','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>120,'max'=>520)),
   'default'=>array('size'=>220,'unit'=>'px'),'selectors'=>array('{{WRAPPER}} .wpst-service-media'=>'--wpst-media-height:{{SIZE}}px;')
  ));
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

  $this->standard_responsive_controls();
 }

 protected function render(){
  $s=$this->get_settings_for_display();
  $icons_enabled = !array_key_exists('show_icons',$s) || 'yes'===($s['show_icons']??'');
  echo'<div class="wpst-ew-service-cards-pro '.($icons_enabled?'has-icons':'no-icons').'" data-icons="'.($icons_enabled?'yes':'no').'">';
  foreach((array)$s['items'] as $item){
   $url=!empty($item['url']['url'])?$item['url']['url']:'#';
   $target=!empty($item['url']['is_external'])?' target="_blank"':'';
   $nofollow=!empty($item['url']['nofollow'])?' rel="nofollow"':'';
   echo'<a class="wpst-service-card" href="'.esc_url($url).'"'.$target.$nofollow.'>';

   $image=!empty($item['image']['url'])?$item['image']['url']:'';
   if('yes'===$s['show_images'] && $image){
    echo'<span class="wpst-service-media"><img src="'.esc_url($image).'" alt="'.esc_attr($item['title']).'" loading="lazy" decoding="async"></span>';
   }

   echo'<span class="wpst-service-card-content">';
   $show_number=('yes'===($s['show_numbers']??'yes') && !empty($item['tag']));
   $show_badge=('yes'===($s['show_badges']??'yes') && !empty($item['badge']));
   if($show_number || $show_badge){
    echo'<span class="wpst-service-card-top">';
    if($show_number)echo'<small>'.esc_html($item['tag']).'</small>';
    if($show_badge)echo'<em>'.esc_html($item['badge']).'</em>';
    echo'</span>';
   }

   if($icons_enabled){
    echo'<i class="wpst-service-icon">';
    if(!empty($item['wpst_icon'])&&class_exists('WPST_Icon_Library'))WPST_Icon_Library::render($item['wpst_icon']);
    elseif(!empty($item['icon']))echo esc_html($item['icon']);
    echo'</i>';
   }

   echo'<h3>'.esc_html($item['title']).'</h3>';
   if(!empty($item['text']))echo'<p>'.esc_html($item['text']).'</p>';

   if(''!==trim((string)$s['action_text'])){
    echo'<span class="wpst-service-action">'.esc_html($s['action_text']);
    if($icons_enabled){
      echo'<i class="wpst-service-action-icon">'.(class_exists('WPST_Icon_Library')?WPST_Icon_Library::svg($s['action_icon'],array('size'=>15)):'→').'</i>';
    }
    echo'</span>';
   }
   echo'</span></a>';
  }
  echo'</div>';
 }
}
