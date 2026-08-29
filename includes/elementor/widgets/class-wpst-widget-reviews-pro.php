<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class WPST_Widget_Reviews_Pro extends WPST_Elementor_Widget_Base {
 public function get_name(){ return 'wpsoft-reviews-pro'; }
 public function get_title(){ return 'WPSoft · Reviews Pro 2.0'; }
 public function get_icon(){ return 'eicon-review'; }
 public function get_keywords(){ return array('reviews','testimonial','rating','google','cards','wpsoft'); }
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'Yorumlar'));
  $this->wpst_signature_preset_control();
  $r=new \Elementor\Repeater();
  $r->add_control('name',array('label'=>'İsim','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Ayşe Yılmaz','dynamic'=>array('active'=>true)));
  $r->add_control('role',array('label'=>'Rol / Kaynak','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Google Review','dynamic'=>array('active'=>true)));
  $r->add_control('text',array('label'=>'Yorum','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'Süreç çok düzenliydi ve sonuç beklentimizin üzerindeydi.','dynamic'=>array('active'=>true)));
  $r->add_control('rating',array('label'=>'Puan','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'5','options'=>array('5'=>'5','4'=>'4','3'=>'3','2'=>'2','1'=>'1')));
  $r->add_control('image',array('label'=>'Avatar','type'=>\Elementor\Controls_Manager::MEDIA));
  $this->add_control('items',array('label'=>'Yorumlar','type'=>\Elementor\Controls_Manager::REPEATER,'fields'=>$r->get_controls(),'default'=>array(
   array('name'=>'Ayşe Yılmaz','role'=>'Google Review','text'=>'Süreç çok düzenliydi ve sonuç beklentimizin üzerindeydi.','rating'=>'5'),
   array('name'=>'Mert Kaya','role'=>'Müşteri','text'=>'İletişim, hız ve detaylara verilen önem gerçekten güçlü.','rating'=>'5'),
   array('name'=>'Selin Ak','role'=>'Proje Ortağı','text'=>'Modern bir çözüm ve sorunsuz teslim süreci.','rating'=>'5')
  ),'title_field'=>'{{{ name }}}'));
  $this->add_control('layout',array('label'=>'Yerleşim','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'cards','options'=>array('cards'=>'Modern Cards','featured'=>'Featured Review','compact'=>'Compact','wall'=>'Review Wall'),'prefix_class'=>'wpst-reviews-layout-'));
  $this->add_responsive_control('columns',array('label'=>'Kolon','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'3','tablet_default'=>'2','mobile_default'=>'1','options'=>array('1'=>'1','2'=>'2','3'=>'3','4'=>'4'),'selectors'=>array('{{WRAPPER}} .wpst-reviews-pro'=>'--wpst-review-cols:{{VALUE}};')));
  $this->add_control('quote_mark',array('label'=>'Alıntı İşareti','type'=>\Elementor\Controls_Manager::SWITCHER,'label_on'=>'Göster','label_off'=>'Gizle','return_value'=>'yes','default'=>'yes','prefix_class'=>'wpst-review-quote-'));
  $this->end_controls_section();

  $this->start_controls_section('style_card',array('label'=>'Kart Biçimi','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_responsive_control('gap',array('label'=>'Kart Aralığı','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>60)),'default'=>array('size'=>18),'selectors'=>array('{{WRAPPER}} .wpst-reviews-pro'=>'--wpst-review-gap:{{SIZE}}px;')));
  $this->add_responsive_control('card_padding',array('label'=>'Kart İç Boşluk','type'=>\Elementor\Controls_Manager::DIMENSIONS,'size_units'=>array('px'),'default'=>array('top'=>26,'right'=>26,'bottom'=>26,'left'=>26,'unit'=>'px'),'selectors'=>array('{{WRAPPER}} .wpst-reviews-pro article'=>'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};')));
  $this->add_responsive_control('radius',array('label'=>'Kart Radius','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>50)),'default'=>array('size'=>22),'selectors'=>array('{{WRAPPER}} .wpst-reviews-pro'=>'--wpst-review-radius:{{SIZE}}px;')));
  $this->add_control('surface',array('label'=>'Kart Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-reviews-pro'=>'--wpst-review-surface:{{VALUE}};')));
  $this->add_control('border',array('label'=>'Kenarlık','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-reviews-pro'=>'--wpst-review-border:{{VALUE}};')));
  $this->add_control('hover_border',array('label'=>'Hover Kenarlık','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-reviews-pro'=>'--wpst-review-hover-border:{{VALUE}};')));
  $this->end_controls_section();

  $this->start_controls_section('style_review',array('label'=>'Yorum Biçimi','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('stars_color',array('label'=>'Yıldız Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-reviews-pro'=>'--wpst-review-stars:{{VALUE}};')));
  $this->add_control('quote_color',array('label'=>'Yorum Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-reviews-pro'=>'--wpst-review-quote:{{VALUE}};')));
  $this->add_control('name_color',array('label'=>'İsim Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-reviews-pro'=>'--wpst-review-name:{{VALUE}};')));
  $this->add_control('role_color',array('label'=>'Rol / Kaynak Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-reviews-pro'=>'--wpst-review-role:{{VALUE}};')));
  $this->add_control('avatar_bg',array('label'=>'Avatar Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-reviews-pro'=>'--wpst-review-avatar-bg:{{VALUE}};')));
  $this->add_control('avatar_color',array('label'=>'Avatar Yazı Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-reviews-pro'=>'--wpst-review-avatar-color:{{VALUE}};')));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'quote_typography','label'=>'Yorum Tipografi','selector'=>'{{WRAPPER}} .wpst-reviews-pro blockquote'));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'name_typography','label'=>'İsim Tipografi','selector'=>'{{WRAPPER}} .wpst-reviews-pro footer strong'));
  $this->end_controls_section();
  $this->standard_responsive_controls();
 }
 protected function render(){
  $s=$this->get_settings_for_display();
  echo '<div class="wpst-reviews-pro">';
  foreach((array)$s['items'] as $i){
   $rating=max(1,min(5,absint($i['rating'])));
   echo '<article>';
   if('yes'===($s['quote_mark']??'')) echo '<span class="wpst-review-quote-mark" aria-hidden="true">“</span>';
   echo '<div class="wpst-review-stars" aria-label="'.esc_attr($rating.' / 5').'">';
   for($x=0;$x<$rating;$x++) echo class_exists('WPST_Icon_Library')?WPST_Icon_Library::svg('star',array('size'=>15)):'★';
   echo '</div><blockquote>'.esc_html($i['text']).'</blockquote><footer>';
   if(!empty($i['image']['url'])) echo '<img src="'.esc_url($i['image']['url']).'" alt="'.esc_attr($i['name']).'" loading="lazy" decoding="async">';
   else echo '<span class="wpst-review-avatar" aria-hidden="true">'.esc_html(function_exists('mb_substr')?mb_substr($i['name'],0,1):substr($i['name'],0,1)).'</span>';
   echo '<div><strong>'.esc_html($i['name']).'</strong><small>'.esc_html($i['role']).'</small></div></footer></article>';
  }
  echo '</div>';
 }
}
