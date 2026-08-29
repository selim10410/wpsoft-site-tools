<?php
if(!defined('ABSPATH'))exit;
class WPST_Widget_Mega_Links extends WPST_Elementor_Widget_Base{
 public function get_name(){return'wpsoft-mega-links';}
 public function get_title(){return'WPSoft Mega · Links';}
 public function get_icon(){return'eicon-menu-card';}
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'Mega Linkler'));
  $this->wpst_signature_preset_control();
  $this->add_control('title',array('label'=>'Grup Başlığı','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Hizmetler'));
  $this->add_control('columns',array('label'=>'Kolon','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'2','options'=>array('1'=>'1','2'=>'2','3'=>'3')));
  $this->add_control('style',array('label'=>'Görünüm','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'cards','options'=>array('cards'=>'Kart','list'=>'Liste','compact'=>'Kompakt','editorial'=>'Editorial','icon-list'=>'Icon List')));
  $r=new \Elementor\Repeater();
  $r->add_control('wpst_icon',array('label'=>'WPSoft Icon','type'=>\Elementor\Controls_Manager::SELECT2,'options'=>class_exists('WPST_Icon_Library')?WPST_Icon_Library::options():array(),'default'=>'arrow-up-right','label_block'=>true)); $r->add_control('icon',array('label'=>'Eski Simge','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'↗'));
  $r->add_control('title',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Web Tasarım'));
  $r->add_control('text',array('label'=>'Kısa Açıklama','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Modern ve mobil uyumlu çözümler.'));
  $r->add_control('badge',array('label'=>'Badge','type'=>\Elementor\Controls_Manager::TEXT,'default'=>''));
  $r->add_control('url',array('label'=>'Bağlantı','type'=>\Elementor\Controls_Manager::URL,'default'=>array('url'=>'#')));
  $this->add_control('items',array('label'=>'Bağlantılar','type'=>\Elementor\Controls_Manager::REPEATER,'fields'=>$r->get_controls(),'default'=>array(
    array('icon'=>'◈','title'=>'Web Tasarım','text'=>'Kurumsal ve modern web projeleri.','badge'=>'Popüler','url'=>array('url'=>'#')),
    array('icon'=>'▦','title'=>'E-Ticaret','text'=>'Satış odaklı mağaza deneyimleri.','badge'=>'','url'=>array('url'=>'#')),
    array('icon'=>'↗','title'=>'SEO & Büyüme','text'=>'Organik görünürlük ve performans.','badge'=>'','url'=>array('url'=>'#')),
    array('icon'=>'⚙','title'=>'Bakım & Destek','text'=>'Sürekli teknik destek ve bakım.','badge'=>'','url'=>array('url'=>'#'))
  ),'title_field'=>'{{{ title }}}'));
  $this->add_control('show_descriptions',array('label'=>'Açıklamaları Göster','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes','prefix_class'=>'wpst-mega-links-desc-'));
  $this->add_control('show_badges',array('label'=>'Badge Göster','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes','prefix_class'=>'wpst-mega-links-badge-'));
  $this->end_controls_section();
  $this->start_controls_section('mega_links_style',array('label'=>'Mega Link Biçimi','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('accent',array('label'=>'Icon Vurgu','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-mega-links a>i'=>'color:{{VALUE}};')));
  $this->add_control('icon_bg',array('label'=>'Icon Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-mega-links a>i'=>'background:{{VALUE}};')));
  $this->add_control('link_bg',array('label'=>'Link Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-mega-links a'=>'background:{{VALUE}};')));
  $this->add_responsive_control('item_gap',array('label'=>'Öğe Aralığı','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>30)),'selectors'=>array('{{WRAPPER}} .wpst-mega-links-grid'=>'gap:{{SIZE}}px;')));
  $this->add_responsive_control('item_radius',array('label'=>'Öğe Köşe','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>30)),'selectors'=>array('{{WRAPPER}} .wpst-ew-mega-links a'=>'border-radius:{{SIZE}}px;')));
  $this->end_controls_section();
  $this->standard_responsive_controls();
 }
 protected function render(){
  $s=$this->get_settings_for_display();
  echo'<nav class="wpst-ew-mega-links is-'.esc_attr($s['style']).' cols-'.absint($s['columns']).'">';
  if($s['title'])echo'<h4>'.esc_html($s['title']).'</h4>';
  echo'<div class="wpst-mega-links-grid">';
  foreach((array)$s['items'] as $item){
    $url=!empty($item['url']['url'])?$item['url']['url']:'#';
    echo'<a href="'.esc_url($url).'"><i>';
    if(!empty($item['wpst_icon'])&&class_exists('WPST_Icon_Library'))WPST_Icon_Library::render($item['wpst_icon']);
    else echo esc_html($item['icon']);
    echo'</i><span><strong>'.esc_html($item['title']).'</strong>';
    if('yes'===$s['show_descriptions']&&!empty($item['text']))echo'<em>'.esc_html($item['text']).'</em>';
    echo'</span>';
    if('yes'===$s['show_badges']&&!empty($item['badge']))echo'<small>'.esc_html($item['badge']).'</small>';
    echo'<b class="wpst-native-arrow">'.(class_exists('WPST_Icon_Library')?WPST_Icon_Library::svg('arrow-right',array('size'=>14)):'→').'</b></a>';
  }
  echo'</div></nav>';
 }
}