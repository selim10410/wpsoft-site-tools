<?php
if(!defined('ABSPATH'))exit;
class WPST_Widget_Service_Carousel_Pro extends WPST_Elementor_Widget_Base{
 public function get_name(){return'wpsoft-service-carousel-pro';}
 public function get_title(){return'WPSoft · Services Carousel Pro';}
 public function get_icon(){return'eicon-slider-push';}
 public function get_keywords(){return array('services','service','hizmet','carousel','slider','cards');}

 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'Hizmetler'));
  $this->wpst_signature_preset_control();

  $r=new \Elementor\Repeater();
  $r->add_control('wpst_icon',array(
   'label'=>'WPSoft Icon','type'=>\Elementor\Controls_Manager::SELECT2,
   'options'=>class_exists('WPST_Icon_Library')?WPST_Icon_Library::options():array(),
   'default'=>'sparkles','label_block'=>true
  ));
  $r->add_control('image',array('label'=>'Görsel','type'=>\Elementor\Controls_Manager::MEDIA));
  $r->add_control('tag',array('label'=>'No / Etiket','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'01'));
  $r->add_control('category',array('label'=>'Kategori','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Creative'));
  $r->add_control('title',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Hizmet'));
  $r->add_control('text',array('label'=>'Açıklama','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'Hizmet detayını kısa biçimde açıklayın.'));
  $r->add_control('url',array('label'=>'Bağlantı','type'=>\Elementor\Controls_Manager::URL,'default'=>array('url'=>'#')));
  $this->add_control('items',array(
   'label'=>'Carousel Kartları','type'=>\Elementor\Controls_Manager::REPEATER,'fields'=>$r->get_controls(),
   'default'=>array(
    array('wpst_icon'=>'target','tag'=>'01','category'=>'Strategy','title'=>'Dijital Strateji','text'=>'Markanız için doğru konumlandırma ve büyüme yol haritası.','url'=>array('url'=>'#')),
    array('wpst_icon'=>'palette','tag'=>'02','category'=>'Design','title'=>'UI / UX Tasarım','text'=>'Kullanıcı odaklı, modern ve güçlü dijital deneyimler.','url'=>array('url'=>'#')),
    array('wpst_icon'=>'code','tag'=>'03','category'=>'Technology','title'=>'Web Development','text'=>'Performans, ölçeklenebilirlik ve kolay yönetim bir arada.','url'=>array('url'=>'#')),
    array('wpst_icon'=>'chart','tag'=>'04','category'=>'Growth','title'=>'SEO & Growth','text'=>'Organik görünürlük ve ölçülebilir performans altyapısı.','url'=>array('url'=>'#')),
    array('wpst_icon'=>'headphones','tag'=>'05','category'=>'Support','title'=>'Bakım & Destek','text'=>'Sürdürülebilir teknik destek ve sürekli iyileştirme.','url'=>array('url'=>'#'))
   ),
   'title_field'=>'{{{ title }}}'
  ));
  $this->add_control('action_text',array('label'=>'Aksiyon Yazısı','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Detayı Gör'));
  $this->add_control('show_icons',array('label'=>'Iconları Göster','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes','prefix_class'=>'wpst-service-carousel-icons-'));
  $this->end_controls_section();

  $this->start_controls_section('carousel',array('label'=>'Carousel Ayarları'));
  $this->add_control('style_preset',array(
   'label'=>'Carousel Stili','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'media',
   'options'=>array('media'=>'Media Cards','icon'=>'Icon Cards','dark'=>'Dark Editorial','numbered'=>'Large Number','minimal'=>'Minimal'),
   'prefix_class'=>'wpst-service-carousel-style-'
  ));
  $this->add_responsive_control('visible',array(
   'label'=>'Görünen Kart','type'=>\Elementor\Controls_Manager::SELECT,
   'default'=>'3','tablet_default'=>'2','mobile_default'=>'1',
   'options'=>array('1'=>'1','2'=>'2','3'=>'3','4'=>'4')
  ));
  $this->add_responsive_control('gap',array(
   'label'=>'Kart Aralığı','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>60)),
   'default'=>array('size'=>20,'unit'=>'px'),
   'selectors'=>array('{{WRAPPER}} .wpst-ew-service-carousel'=>'--wpst-service-carousel-gap:{{SIZE}}px;','{{WRAPPER}} .wpst-ew-service-carousel-track'=>'gap:{{SIZE}}px;')
  ));
  $this->add_control('show_arrows',array('label'=>'Okları Göster','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes'));
  $this->add_control('touch_swipe',array('label'=>'Dokunmatik Kaydırma','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes'));
  $this->add_control('mouse_drag',array('label'=>'Mouse ile Sürükle','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes'));
  $this->add_control('drag_cursor',array('label'=>'Grab İmleci','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes','condition'=>array('mouse_drag'=>'yes'),'prefix_class'=>'wpst-service-carousel-grab-'));
  $this->add_control('peek',array('label'=>'Sonraki Kartı Göster','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes','prefix_class'=>'wpst-service-carousel-peek-'));
  $this->add_responsive_control('peek_width',array(
   'label'=>'Sonraki Kart Görünümü','type'=>\Elementor\Controls_Manager::SLIDER,
   'range'=>array('px'=>array('min'=>20,'max'=>180)),
   'default'=>array('size'=>120,'unit'=>'px'),'tablet_default'=>array('size'=>70,'unit'=>'px'),'mobile_default'=>array('size'=>46,'unit'=>'px'),
   'selectors'=>array('{{WRAPPER}} .wpst-ew-service-carousel'=>'--wpst-service-peek:{{SIZE}}px;')
  ));

  $this->add_control('show_drag_hint',array('label'=>'Kaydırma İpucunu Göster','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes','prefix_class'=>'wpst-service-carousel-hint-'));
  $this->add_control('drag_hint_text',array('label'=>'Mouse İpucu Yazısı','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Tut & Sürükle','condition'=>array('show_drag_hint'=>'yes')));
  $this->add_control('touch_hint_text',array('label'=>'Mobil İpucu Yazısı','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Kaydır','condition'=>array('show_drag_hint'=>'yes')));
  $this->add_control('show_progress',array('label'=>'İlerleme Çizgisi','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes'));
  $this->add_control('edge_hint',array('label'=>'Kenar Kaydırma İpucu','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes','prefix_class'=>'wpst-service-carousel-edge-'));

  $this->end_controls_section();

  $this->start_controls_section('style',array('label'=>'Kart Biçimi','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('card_bg',array('label'=>'Kart Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-service-carousel-card'=>'--svc-c-bg:{{VALUE}};')));
  $this->add_control('accent',array('label'=>'Vurgu','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#315cf5','selectors'=>array('{{WRAPPER}} .wpst-service-carousel-card'=>'--svc-c-accent:{{VALUE}};')));
  $this->add_control('title_color',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-service-carousel-card h3'=>'color:{{VALUE}}!important;')));
  $this->add_control('text_color',array('label'=>'Metin','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-service-carousel-card p'=>'color:{{VALUE}}!important;')));
  $this->add_responsive_control('radius',array(
   'label'=>'Kart Köşe','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>60)),
   'default'=>array('size'=>24),'selectors'=>array('{{WRAPPER}} .wpst-service-carousel-card'=>'border-radius:{{SIZE}}px;')
  ));
  $this->add_responsive_control('card_height',array(
   'label'=>'Minimum Kart Yüksekliği','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>240,'max'=>650)),
   'selectors'=>array('{{WRAPPER}} .wpst-service-carousel-card'=>'min-height:{{SIZE}}px;')
  ));
  $this->add_responsive_control('padding',array(
   'label'=>'İç Boşluk','type'=>\Elementor\Controls_Manager::DIMENSIONS,'size_units'=>array('px'),
   'selectors'=>array('{{WRAPPER}} .wpst-service-carousel-content'=>'padding:{{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;')
  ));
  $this->end_controls_section();

  $this->standard_responsive_controls();
 }

 protected function render(){
  $s=$this->get_settings_for_display();
  $visible=!empty($s['visible'])?(int)$s['visible']:3;
  $vt=!empty($s['visible_tablet'])?(int)$s['visible_tablet']:2;
  $vm=!empty($s['visible_mobile'])?(int)$s['visible_mobile']:1;

  echo'<div class="wpst-ew-card-carousel wpst-ew-service-carousel" data-visible="'.absint($visible).'" data-visible-tablet="'.absint($vt).'" data-visible-mobile="'.absint($vm).'" data-touch-swipe="'.('yes'===($s['touch_swipe']??'yes')?'yes':'no').'" data-mouse-drag="'.('yes'===($s['mouse_drag']??'yes')?'yes':'no').'" data-drag-hint="'.esc_attr($s['drag_hint_text']??'Tut & Sürükle').'" data-touch-hint="'.esc_attr($s['touch_hint_text']??'Kaydır').'">';
  if('yes'===($s['show_drag_hint']??'yes')){
   echo'<div class="wpst-service-carousel-drag-hint" aria-hidden="true"><span class="wpst-service-drag-desktop">'.esc_html($s['drag_hint_text']??'Tut & Sürükle').'</span><span class="wpst-service-drag-touch">'.esc_html($s['touch_hint_text']??'Kaydır').'</span><i>'.(class_exists('WPST_Icon_Library')?WPST_Icon_Library::svg('arrow-left',array('size'=>13)):'←').'</i><b></b><i>'.(class_exists('WPST_Icon_Library')?WPST_Icon_Library::svg('arrow-right',array('size'=>13)):'→').'</i></div>';
  }
  echo'<div class="wpst-service-carousel-viewport">';
  if('yes'===$s['show_arrows']){
   echo'<button type="button" class="wpst-ew-card-prev wpst-service-carousel-side wpst-service-carousel-side-prev" aria-label="Önceki">'.(class_exists('WPST_Icon_Library')?WPST_Icon_Library::svg('arrow-left',array('size'=>18)):'←').'</button>';
   echo'<button type="button" class="wpst-ew-card-next wpst-service-carousel-side wpst-service-carousel-side-next" aria-label="Sonraki">'.(class_exists('WPST_Icon_Library')?WPST_Icon_Library::svg('arrow-right',array('size'=>18)):'→').'</button>';
  }
  echo'<div class="wpst-ew-card-carousel-track wpst-ew-service-carousel-track">';

  foreach((array)$s['items'] as $item){
   $url=!empty($item['url']['url'])?$item['url']['url']:'#';
   $image=!empty($item['image']['url'])?$item['image']['url']:'';
   echo'<a class="wpst-service-carousel-card" href="'.esc_url($url).'">';
   if($image)echo'<span class="wpst-service-carousel-media"><img src="'.esc_url($image).'" alt="'.esc_attr($item['title']).'" loading="lazy" decoding="async"></span>';
   echo'<span class="wpst-service-carousel-content">';
   echo'<span class="wpst-service-carousel-meta"><small>'.esc_html($item['tag']).'</small><em>'.esc_html($item['category']).'</em></span>';
   if('yes'===($s['show_icons']??'yes')){
    echo'<i class="wpst-service-carousel-icon">';
    if(!empty($item['wpst_icon'])&&class_exists('WPST_Icon_Library'))WPST_Icon_Library::render($item['wpst_icon']);
    echo'</i>';
   }
   echo'<h3>'.esc_html($item['title']).'</h3>';
   if(!empty($item['text']))echo'<p>'.esc_html($item['text']).'</p>';
   echo'<span class="wpst-service-carousel-action">'.esc_html($s['action_text']).'<i>'.(class_exists('WPST_Icon_Library')?WPST_Icon_Library::svg('arrow-up-right',array('size'=>15)):'↗').'</i></span>';
   echo'</span></a>';
  }
  echo'</div></div>';

  if('yes'===($s['show_progress']??'yes')){
   echo'<div class="wpst-service-carousel-progress" aria-hidden="true"><span></span></div>';
  }
  echo'</div>';
 }
}
