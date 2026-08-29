<?php
if(!defined('ABSPATH'))exit;

class WPST_Widget_Reviews_Carousel extends WPST_Elementor_Widget_Base{
 public function get_name(){return'wpsoft-reviews-carousel';}
 public function get_title(){return'WPSoft · Reviews Carousel';}
 public function get_icon(){return'eicon-review';}
 public function get_keywords(){return array('review','reviews','yorum','müşteri','carousel','slider','rating','google');}

 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'Yorumlar'));
  $this->wpst_signature_preset_control();

  $r=new \Elementor\Repeater();
  $r->add_control('name',array('label'=>'İsim','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Ayşe Yılmaz'));
  $r->add_control('role',array('label'=>'Görev / Şirket','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Müşteri'));
  $r->add_control('source',array('label'=>'Kaynak','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Google Review'));
  $r->add_control('text',array('label'=>'Yorum','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'Hizmet kalitesi, iletişim ve süreç yönetimi beklentimizin üzerindeydi.'));
  $r->add_control('rating',array(
   'label'=>'Puan','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'5',
   'options'=>array('5'=>'5.0','4.5'=>'4.5','4'=>'4.0','3.5'=>'3.5','3'=>'3.0','2.5'=>'2.5','2'=>'2.0','1'=>'1.0')
  ));
  $r->add_control('image',array('label'=>'Avatar','type'=>\Elementor\Controls_Manager::MEDIA));
  $r->add_control('verified',array('label'=>'Doğrulandı Rozeti','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes'));

  $this->add_control('items',array(
   'label'=>'Yorumlar','type'=>\Elementor\Controls_Manager::REPEATER,'fields'=>$r->get_controls(),
   'default'=>array(
    array('name'=>'Ayşe Yılmaz','role'=>'Genel Müdür','source'=>'Google Review','text'=>'Tasarım süreci çok düzenli ilerledi. Sonuç modern, hızlı ve kullanımı gerçekten kolay oldu.','rating'=>'5','verified'=>'yes'),
    array('name'=>'Mert Kaya','role'=>'Kurucu','source'=>'Müşteri Yorumu','text'=>'İletişim güçlüydü ve tüm detaylar zamanında tamamlandı. Beklentimizin üzerinde bir çalışma çıktı.','rating'=>'5','verified'=>'yes'),
    array('name'=>'Selin Ak','role'=>'Pazarlama Müdürü','source'=>'Google Review','text'=>'Mobil ve masaüstü görünüm çok başarılı. Özellikle kullanıcı deneyimi tarafındaki iyileştirmeler fark yarattı.','rating'=>'4.5','verified'=>'yes'),
    array('name'=>'Emre Demir','role'=>'İşletme Sahibi','source'=>'Müşteri Yorumu','text'=>'Hızlı destek, modern tasarım ve sorunsuz teslim. Uzun vadede çalışmak isteyeceğimiz bir ekip.','rating'=>'5','verified'=>'yes')
   ),
   'title_field'=>'{{{ name }}}'
  ));
  $this->end_controls_section();

  $this->start_controls_section('carousel',array('label'=>'Carousel'));
  $this->add_responsive_control('visible',array(
   'label'=>'Görünen Kart','type'=>\Elementor\Controls_Manager::SELECT,
   'default'=>'3','tablet_default'=>'2','mobile_default'=>'1',
   'options'=>array('1'=>'1','2'=>'2','3'=>'3','4'=>'4')
  ));
  $this->add_responsive_control('gap',array(
   'label'=>'Kart Aralığı','type'=>\Elementor\Controls_Manager::SLIDER,
   'size_units'=>array('px'),'range'=>array('px'=>array('min'=>0,'max'=>60)),
   'default'=>array('size'=>20,'unit'=>'px'),
   'selectors'=>array('{{WRAPPER}} .wpst-reviews-carousel'=>'--wpst-reviews-gap:{{SIZE}}px;')
  ));
  $this->add_control('autoplay',array('label'=>'Otomatik Oynat','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes'));
  $this->add_control('speed',array('label'=>'Geçiş Süresi','type'=>\Elementor\Controls_Manager::NUMBER,'min'=>1500,'max'=>15000,'step'=>500,'default'=>5000,'condition'=>array('autoplay'=>'yes')));
  $this->add_control('pause_hover',array('label'=>'Hover’da Duraklat','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes','condition'=>array('autoplay'=>'yes')));
  $this->add_control('touch_swipe',array('label'=>'Dokunmatik Kaydırma','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes'));
  $this->add_control('mouse_drag',array('label'=>'Mouse ile Sürükle','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes'));
  $this->add_control('show_arrows',array('label'=>'Okları Göster','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes'));
  $this->add_control('show_dots',array('label'=>'Noktaları Göster','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes'));
  $this->end_controls_section();

  $this->start_controls_section('style_card',array('label'=>'Kart Biçimi','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('style',array(
   'label'=>'Kart Stili','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'modern',
   'options'=>array('modern'=>'Modern','minimal'=>'Minimal','soft'=>'Soft','dark'=>'Dark','glass'=>'Glass'),
   'prefix_class'=>'wpst-reviews-carousel-style-'
  ));
  $this->add_control('card_bg',array('label'=>'Kart Arka Planı','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-review-carousel-card'=>'background:{{VALUE}}!important;')));
  $this->add_control('text_color',array('label'=>'Yorum Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-review-carousel-card blockquote'=>'color:{{VALUE}}!important;')));
  $this->add_control('name_color',array('label'=>'İsim Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-review-carousel-card strong'=>'color:{{VALUE}}!important;')));
  $this->add_control('muted_color',array('label'=>'Alt Metin Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-review-carousel-card small, {{WRAPPER}} .wpst-review-source'=>'color:{{VALUE}}!important;')));
  $this->add_control('star_color',array('label'=>'Yıldız Rengi','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#f59e0b','selectors'=>array('{{WRAPPER}} .wpst-review-carousel-stars'=>'color:{{VALUE}}!important;')));
  $this->add_responsive_control('radius',array('label'=>'Kart Köşesi','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>50)),'default'=>array('size'=>22),'selectors'=>array('{{WRAPPER}} .wpst-review-carousel-card'=>'border-radius:{{SIZE}}px;')));
  $this->add_responsive_control('padding',array('label'=>'Kart İç Boşluğu','type'=>\Elementor\Controls_Manager::DIMENSIONS,'size_units'=>array('px'),'selectors'=>array('{{WRAPPER}} .wpst-review-carousel-card'=>'padding:{{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;')));
  $this->end_controls_section();

  $this->start_controls_section('style_controls',array('label'=>'Kontroller','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('arrow_bg',array('label'=>'Ok Arka Planı','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#ffffff','selectors'=>array('{{WRAPPER}} .wpst-reviews-carousel'=>'--wpst-review-arrow-bg:{{VALUE}};')));
  $this->add_control('arrow_color',array('label'=>'Ok Rengi','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#0f172a','selectors'=>array('{{WRAPPER}} .wpst-reviews-carousel'=>'--wpst-review-arrow-color:{{VALUE}};')));
  $this->add_control('arrow_hover_bg',array('label'=>'Ok Hover Arka Planı','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#0f172a','selectors'=>array('{{WRAPPER}} .wpst-reviews-carousel'=>'--wpst-review-arrow-hover-bg:{{VALUE}};')));
  $this->add_control('arrow_hover_color',array('label'=>'Ok Hover Rengi','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#ffffff','selectors'=>array('{{WRAPPER}} .wpst-reviews-carousel'=>'--wpst-review-arrow-hover-color:{{VALUE}};')));
  $this->add_control('dot_color',array('label'=>'Nokta Rengi','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#cbd5e1','selectors'=>array('{{WRAPPER}} .wpst-reviews-carousel'=>'--wpst-review-dot:{{VALUE}};')));
  $this->add_control('dot_active',array('label'=>'Aktif Nokta Rengi','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#2563eb','selectors'=>array('{{WRAPPER}} .wpst-reviews-carousel'=>'--wpst-review-dot-active:{{VALUE}};')));
  $this->end_controls_section();

  $this->standard_responsive_controls();
 }

 protected function render(){
  $s=$this->get_settings_for_display();
  $items=(array)($s['items']??array());
  if(!$items)return;

  $desktop=max(1,min(4,absint($s['visible']??3)));
  $tablet=max(1,min(4,absint($s['visible_tablet']??2)));
  $mobile=max(1,min(4,absint($s['visible_mobile']??1)));

  echo'<section class="wpst-reviews-carousel" tabindex="0"'
      .' data-visible-desktop="'.esc_attr($desktop).'"'
      .' data-visible-tablet="'.esc_attr($tablet).'"'
      .' data-visible-mobile="'.esc_attr($mobile).'"'
      .' data-autoplay="'.esc_attr($s['autoplay']??'').'"'
      .' data-speed="'.absint($s['speed']??5000).'"'
      .' data-pause-hover="'.esc_attr($s['pause_hover']??'').'"'
      .' data-touch-swipe="'.esc_attr($s['touch_swipe']??'yes').'"'
      .' data-mouse-drag="'.esc_attr($s['mouse_drag']??'yes').'">';

  echo'<div class="wpst-reviews-carousel-viewport"><div class="wpst-reviews-carousel-track">';
  foreach($items as $i){
   $rating=max(1,min(5,(float)($i['rating']??5)));
   $full=(int)floor($rating);
   $half=($rating-$full)>=.5;
   $name=trim((string)($i['name']??''));
   echo'<article class="wpst-review-carousel-card">';
   echo'<div class="wpst-review-carousel-top"><div class="wpst-review-carousel-stars" aria-label="'.esc_attr(number_format($rating,1).' / 5').'">';
   for($x=1;$x<=5;$x++){
    if($x<=$full) echo'<span class="is-full">★</span>';
    elseif($half && $x===$full+1) echo'<span class="is-half">★</span>';
    else echo'<span class="is-empty">★</span>';
   }
   echo'</div>';
   if(!empty($i['source']))echo'<span class="wpst-review-source">'.esc_html($i['source']).'</span>';
   echo'</div>';
   echo'<blockquote>'.esc_html($i['text']??'').'</blockquote>';
   echo'<footer>';
   if(!empty($i['image']['url'])){
    echo'<img src="'.esc_url($i['image']['url']).'" alt="'.esc_attr($name).'" loading="lazy" decoding="async">';
   }else{
    $initial=$name!==''?(function_exists('mb_substr')?mb_substr($name,0,1):substr($name,0,1)):'?';
    echo'<span class="wpst-review-carousel-avatar">'.esc_html($initial).'</span>';
   }
   echo'<div class="wpst-review-carousel-person"><span><strong>'.esc_html($name).'</strong>';
   if('yes'===($i['verified']??''))echo'<i class="wpst-review-verified" title="Doğrulandı" aria-label="Doğrulandı">✓</i>';
   echo'</span>';
   if(!empty($i['role']))echo'<small>'.esc_html($i['role']).'</small>';
   echo'</div></footer></article>';
  }
  echo'</div></div>';

  if('yes'===($s['show_arrows']??'yes')){
   echo'<div class="wpst-reviews-carousel-controls">';
   echo'<button type="button" class="wpst-reviews-prev" aria-label="Önceki yorum">'.(class_exists('WPST_Icon_Library')?WPST_Icon_Library::svg('arrow-left',array('size'=>17)):'←').'</button>';
   echo'<button type="button" class="wpst-reviews-next" aria-label="Sonraki yorum">'.(class_exists('WPST_Icon_Library')?WPST_Icon_Library::svg('arrow-right',array('size'=>17)):'→').'</button>';
   echo'</div>';
  }
  if('yes'===($s['show_dots']??'yes'))echo'<div class="wpst-reviews-carousel-dots" aria-label="Yorum sayfaları"></div>';
  echo'</section>';
 }
}
