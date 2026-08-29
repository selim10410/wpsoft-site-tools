<?php
if(!defined('ABSPATH'))exit;

class WPST_Widget_Hover_Reveal extends WPST_Elementor_Widget_Base{
 public function get_name(){return'wpsoft-hover-reveal';}
 public function get_title(){return'WPSoft Hover Reveal 2.0';}
 public function get_icon(){return'eicon-flip-box';}

 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'İçerik · Kartlar'));
  $r=new \Elementor\Repeater();
  $r->add_control('image',array('label'=>'Görsel','type'=>\Elementor\Controls_Manager::MEDIA));
  $r->add_control('eyebrow',array('label'=>'Üst Etiket','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Hizmet'));
  $r->add_control('title',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Web Tasarım'));
  $r->add_control('text',array('label'=>'Açıklama','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'Modern ve güçlü kullanıcı deneyimi.'));
  $r->add_control('action_text',array('label'=>'Aksiyon Yazısı','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'İncele'));
  $r->add_control('url',array('label'=>'Bağlantı','type'=>\Elementor\Controls_Manager::URL,'placeholder'=>'https://'));
  $this->add_control('items',array(
   'label'=>'Kartlar','type'=>\Elementor\Controls_Manager::REPEATER,'fields'=>$r->get_controls(),
   'default'=>array(
    array('eyebrow'=>'Dijital','title'=>'Web Tasarım','text'=>'Modern kurumsal deneyim.','action_text'=>'İncele'),
    array('eyebrow'=>'Satış','title'=>'E-Ticaret','text'=>'Satış odaklı yapı.','action_text'=>'İncele'),
    array('eyebrow'=>'Büyüme','title'=>'SEO','text'=>'Organik büyüme altyapısı.','action_text'=>'İncele')
   ),
   'title_field'=>'{{{ title }}}'
  ));
  $this->add_control('placeholder_text',array('label'=>'Görsel Yoksa Yazı','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'WPSoft'));
  $this->add_control('show_eyebrow',array('label'=>'Üst Etiketi Göster','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes'));
  $this->add_control('show_description',array('label'=>'Açıklamayı Göster','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes'));
  $this->add_control('show_action',array('label'=>'Aksiyonu Göster','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes'));
  $this->end_controls_section();

  $this->start_controls_section('layout_section',array('label'=>'Düzen'));
  $this->add_control('layout_variant',array(
   'label'=>'Yerleşim','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'cards',
   'options'=>array('cards'=>'Modern Kartlar','editorial'=>'Editorial','full'=>'Full Width','mosaic'=>'Mosaic'),
   'prefix_class'=>'wpst-hover-reveal-layout-'
  ));
  $this->add_responsive_control('columns',array(
   'label'=>'Kolon','type'=>\Elementor\Controls_Manager::SELECT,
   'default'=>'3','tablet_default'=>'2','mobile_default'=>'1',
   'options'=>array('1'=>'1','2'=>'2','3'=>'3','4'=>'4'),
   'selectors'=>array('{{WRAPPER}} .wpst-ew-hover-reveal'=>'grid-template-columns:repeat({{VALUE}},minmax(0,1fr));'),
   'condition'=>array('layout_variant'=>'cards')
  ));
  $this->add_responsive_control('height',array(
   'label'=>'Kart Yüksekliği','type'=>\Elementor\Controls_Manager::SLIDER,
   'range'=>array('px'=>array('min'=>220,'max'=>760)),
   'default'=>array('size'=>420),'tablet_default'=>array('size'=>380),'mobile_default'=>array('size'=>340),
   'selectors'=>array('{{WRAPPER}}'=>'--wpst-hover-card-h:{{SIZE}}px;')
  ));
  $this->add_responsive_control('gap',array(
   'label'=>'Kart Aralığı','type'=>\Elementor\Controls_Manager::SLIDER,
   'range'=>array('px'=>array('min'=>0,'max'=>60)),
   'default'=>array('size'=>18),
   'selectors'=>array('{{WRAPPER}}'=>'--wpst-hover-gap:{{SIZE}}px;')
  ));
  $this->add_control('content_position',array(
   'label'=>'İçerik Konumu','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'bottom',
   'options'=>array('bottom'=>'Alt','center'=>'Orta','top'=>'Üst'),
   'prefix_class'=>'wpst-hover-content-'
  ));
  $this->add_control('reveal_effect',array(
   'label'=>'Reveal Efekti','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'rise',
   'options'=>array('rise'=>'Yukarı Açıl','fade'=>'Fade','zoom'=>'Zoom','always'=>'Her Zaman Açık'),
   'prefix_class'=>'wpst-hover-effect-'
  ));
  $this->end_controls_section();

  $this->start_controls_section('card_style',array('label'=>'Biçim · Kart','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->wpst_signature_preset_control('hover_reveal_preset');
  $this->add_responsive_control('radius',array(
   'label'=>'Köşe','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>60)),
   'default'=>array('size'=>26),'selectors'=>array('{{WRAPPER}}'=>'--wpst-hover-radius:{{SIZE}}px;')
  ));
  $this->add_control('overlay',array(
   'label'=>'Overlay Rengi','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'rgba(2,6,23,.72)',
   'selectors'=>array('{{WRAPPER}}'=>'--wpst-hover-overlay:{{VALUE}};')
  ));
  $this->add_control('overlay_hover',array(
   'label'=>'Hover Overlay','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'rgba(2,6,23,.48)',
   'selectors'=>array('{{WRAPPER}}'=>'--wpst-hover-overlay-active:{{VALUE}};')
  ));
  $this->add_control('border_color',array('label'=>'Border Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}}'=>'--wpst-hover-border:{{VALUE}};')));
  $this->add_control('shadow',array(
   'label'=>'Gölge','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'soft',
   'options'=>array('none'=>'Yok','soft'=>'Soft','medium'=>'Medium'),'prefix_class'=>'wpst-hover-shadow-'
  ));
  $this->end_controls_section();

  $this->start_controls_section('image_style',array('label'=>'Biçim · Görsel','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('image_fit',array(
   'label'=>'Görsel Oturtma','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'cover',
   'options'=>array('cover'=>'Kapla','contain'=>'Sığdır'),
   'selectors'=>array('{{WRAPPER}} .wpst-hover-media img'=>'object-fit:{{VALUE}};')
  ));
  $this->add_control('image_position',array(
   'label'=>'Görsel Konumu','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'center',
   'options'=>array('center'=>'Orta','top'=>'Üst','bottom'=>'Alt','left'=>'Sol','right'=>'Sağ'),
   'selectors'=>array('{{WRAPPER}} .wpst-hover-media img'=>'object-position:{{VALUE}};')
  ));
  $this->end_controls_section();

  $this->start_controls_section('text_style',array('label'=>'Biçim · Yazılar','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('eyebrow_color',array('label'=>'Üst Etiket Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}}'=>'--wpst-hover-eyebrow:{{VALUE}};')));
  $this->add_control('title_color',array('label'=>'Başlık Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}}'=>'--wpst-hover-title:{{VALUE}};')));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'title_typography','label'=>'Başlık Tipografi','selector'=>'{{WRAPPER}} .wpst-hover-title'));
  $this->add_control('text_color',array('label'=>'Açıklama Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}}'=>'--wpst-hover-text:{{VALUE}};')));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'text_typography','label'=>'Açıklama Tipografi','selector'=>'{{WRAPPER}} .wpst-hover-text'));
  $this->add_control('action_color',array('label'=>'Aksiyon Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}}'=>'--wpst-hover-action:{{VALUE}};')));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'action_typography','label'=>'Aksiyon Tipografi','selector'=>'{{WRAPPER}} .wpst-hover-action'));
  $this->add_responsive_control('content_padding',array(
   'label'=>'İçerik Boşluğu','type'=>\Elementor\Controls_Manager::DIMENSIONS,'size_units'=>array('px'),
   'selectors'=>array('{{WRAPPER}} .wpst-ew-hover-layer'=>'padding:{{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;')
  ));
  $this->end_controls_section();

  $this->standard_responsive_controls();
 }

 protected function render(){
  $s=$this->get_settings_for_display();
  echo'<div class="wpst-ew-hover-reveal">';
  foreach((array)($s['items']??array()) as $i){
   $title=$i['title']??'';
   $url=!empty($i['url']['url'])?$i['url']['url']:'#';
   $target=!empty($i['url']['is_external'])?' target="_blank"':'';
   $nofollow=!empty($i['url']['nofollow'])?' rel="nofollow"':'';

   echo'<article class="wpst-hover-card">';
   echo'<div class="wpst-hover-media">';
   if(!empty($i['image']['url'])) echo'<img src="'.esc_url($i['image']['url']).'" alt="'.esc_attr($title).'" loading="lazy" decoding="async">';
   else echo'<div class="wpst-ew-hover-placeholder">'.esc_html($s['placeholder_text']??'WPSoft').'</div>';
   echo'</div>';

   echo'<a class="wpst-ew-hover-layer" href="'.esc_url($url).'"'.$target.$nofollow.'>';
   echo'<div class="wpst-hover-copy">';
   if('yes'===($s['show_eyebrow']??'yes')&&!empty($i['eyebrow'])) echo'<small class="wpst-hover-eyebrow">'.esc_html($i['eyebrow']).'</small>';
   echo'<h3 class="wpst-hover-title">'.esc_html($title).'</h3>';
   if('yes'===($s['show_description']??'yes')&&!empty($i['text'])) echo'<p class="wpst-hover-text">'.esc_html($i['text']).'</p>';
   if('yes'===($s['show_action']??'yes')){
    echo'<span class="wpst-hover-action"><b>'.esc_html($i['action_text']??'İncele').'</b><i class="wpst-native-arrow">'.(class_exists('WPST_Icon_Library')?WPST_Icon_Library::svg('arrow-right',array('size'=>16)):'→').'</i></span>';
   }
   echo'</div></a></article>';
  }
  echo'</div>';
 }
}
