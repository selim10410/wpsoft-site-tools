<?php
if(!defined('ABSPATH'))exit;
class WPST_Widget_Testimonial_Slider extends WPST_Elementor_Widget_Base{
 public function get_name(){return'wpsoft-testimonial-slider';}
 public function get_title(){return'WPSoft · Testimonials 2.0';}
 public function get_icon(){return'eicon-testimonial-carousel';}
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'Yorumlar'));
  $this->wpst_signature_preset_control();
  $r=new \Elementor\Repeater();
  $r->add_control('quote',array('label'=>'Yorum','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'Süreç hızlı ve profesyonel ilerledi.'));
  $r->add_control('name',array('label'=>'Ad Soyad','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Müşteri Adı'));
  $r->add_control('role',array('label'=>'Ünvan','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Firma'));
  $r->add_control('avatar',array('label'=>'Avatar','type'=>\Elementor\Controls_Manager::MEDIA));
  $this->add_control('items',array('label'=>'Yorumlar','type'=>\Elementor\Controls_Manager::REPEATER,'fields'=>$r->get_controls(),'default'=>array(
   array('quote'=>'Yeni web sitemiz hem daha modern hem daha hızlı oldu.','name'=>'Zeynep Kaya','role'=>'Pazarlama Müdürü'),
   array('quote'=>'İletişim ve proje yönetimi oldukça başarılıydı.','name'=>'Mert Aydın','role'=>'Genel Müdür'),
   array('quote'=>'İhtiyacımızı doğru anlayıp güçlü bir sonuç çıkardılar.','name'=>'Selin Aras','role'=>'Kurucu')
  ),'title_field'=>'{{{ name }}}'));
  $this->add_control('quote_icon',array('label'=>'Quote Icon','type'=>\Elementor\Controls_Manager::SELECT2,'options'=>class_exists('WPST_Icon_Library')?WPST_Icon_Library::options():array(),'default'=>'quote','label_block'=>true));
  $this->add_control('style_preset',array('label'=>'Stil','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'dark','options'=>array('dark'=>'Dark','light'=>'Light','editorial'=>'Editorial','glass'=>'Glass'),'prefix_class'=>'wpst-testimonial-style-'));
  $this->add_control('layout_variant',array(
   'label'=>'Yorum Kompozisyonu','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'statement',
   'options'=>array('statement'=>'Statement','card'=>'Card','profile'=>'Profile Focus','compact'=>'Compact Quote','stage'=>'Stage'),
   'prefix_class'=>'wpst-testimonial-layout-'
  ));
  $this->add_control('autoplay',array('label'=>'Otomatik Oynat','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>''));
  $this->add_control('speed',array('label'=>'Otomatik Geçiş Süresi','type'=>\Elementor\Controls_Manager::NUMBER,'default'=>5000,'min'=>1800,'max'=>15000,'step'=>500,'condition'=>array('autoplay'=>'yes')));
  $this->add_control('pause_hover',array('label'=>'Hover’da Duraklat','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes','condition'=>array('autoplay'=>'yes')));
  $this->add_control('touch_swipe',array('label'=>'Dokunmatik Kaydırma','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes'));
  $this->add_control('mouse_drag',array('label'=>'Mouse ile Sürükle','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>''));
  $this->add_control('show_dots',array('label'=>'Noktaları Göster','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes'));
  $this->add_control('show_progress',array('label'=>'İlerleme Çizgisi','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>''));
  $this->end_controls_section();

  $this->start_controls_section('style',array('label'=>'Biçim','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('bg',array('label'=>'Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-testimonial-slider'=>'--testimonial-bg:{{VALUE}};')));
  $this->add_control('text',array('label'=>'Yorum Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-testimonial-slider'=>'--testimonial-text:{{VALUE}};')));
  $this->add_control('accent',array('label'=>'Vurgu','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#f59e0b','selectors'=>array('{{WRAPPER}} .wpst-ew-testimonial-slider'=>'--testimonial-accent:{{VALUE}};')));
  $this->add_responsive_control('radius',array('label'=>'Köşe','type'=>\Elementor\Controls_Manager::SLIDER,'default'=>array('size'=>26),'range'=>array('px'=>array('min'=>0,'max'=>50)),'selectors'=>array('{{WRAPPER}} .wpst-ew-testimonial-slider'=>'border-radius:{{SIZE}}px;')));
  $this->end_controls_section();
  $this->standard_responsive_controls();
 }
 protected function render(){
  $s=$this->get_settings_for_display();
  echo'<section class="wpst-ew-testimonial-slider" tabindex="0" data-autoplay="'.esc_attr($s['autoplay']??'').'" data-speed="'.absint($s['speed']??5000).'" data-pause-hover="'.esc_attr($s['pause_hover']??'yes').'" data-touch-swipe="'.esc_attr($s['touch_swipe']??'yes').'" data-mouse-drag="'.esc_attr($s['mouse_drag']??'').'"><div class="wpst-ew-testimonial-track">';
  $n=0;
  foreach((array)$s['items'] as $i){
   echo'<article class="'.($n===0?'is-active':'').'"><div class="wpst-testimonial-top"><i>'.(class_exists('WPST_Icon_Library')?WPST_Icon_Library::svg($s['quote_icon'],array('size'=>28)):'“').'</i><div class="wpst-ew-stars">★★★★★</div></div><blockquote>'.esc_html($i['quote']).'</blockquote><footer>';
   if(!empty($i['avatar']['url']))echo'<img src="'.esc_url($i['avatar']['url']).'" alt="'.esc_attr($i['name']??'').'" loading="lazy" decoding="async">';
   echo'<div><strong>'.esc_html($i['name']).'</strong><span>'.esc_html($i['role']).'</span></div></footer></article>';
   $n++;
  }
  echo'</div><div class="wpst-ew-testimonial-controls"><button type="button" class="wpst-ew-testimonial-prev" aria-label="Önceki yorum">'.(class_exists('WPST_Icon_Library')?WPST_Icon_Library::svg('arrow-left',array('size'=>16)):'←').'</button>'; if('yes'===($s['show_dots']??'yes'))echo'<div class="wpst-testimonial-dots" aria-label="Yorum seçimi"></div>'; echo'<button type="button" class="wpst-ew-testimonial-next" aria-label="Sonraki yorum">'.(class_exists('WPST_Icon_Library')?WPST_Icon_Library::svg('arrow-right',array('size'=>16)):'→').'</button></div>'; if('yes'===($s['show_progress']??''))echo'<div class="wpst-carousel-progress wpst-testimonial-progress" aria-hidden="true"><span></span></div>'; echo'</section>';
 }
}
