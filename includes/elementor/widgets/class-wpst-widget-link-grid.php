<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class WPST_Widget_Link_Grid extends WPST_Elementor_Widget_Base {
 public function get_name(){ return 'wpsoft-link-grid'; }
 public function get_title(){ return 'WPSoft · Link Grid 2.0'; }
 public function get_icon(){ return 'eicon-link'; }
 public function get_keywords(){ return array('link','grid','quick links','cards','navigation','wpsoft'); }
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'Bağlantılar'));
  $this->wpst_signature_preset_control();
  $r=new \Elementor\Repeater();

  $r->add_control('icon_source',array(
   'label'=>'İkon Kaynağı',
   'type'=>\Elementor\Controls_Manager::SELECT,
   'default'=>'wpsoft',
   'options'=>array(
    'wpsoft'=>'WPSoft Icon Library',
    'elementor'=>'Elementor Icon',
    'svg_upload'=>'SVG Dosyası Yükle',
    'svg_code'=>'SVG Kodu'
   )
  ));

  $r->add_control('wpst_icon',array(
   'label'=>'WPSoft Icon',
   'type'=>\Elementor\Controls_Manager::SELECT2,
   'options'=>class_exists('WPST_Icon_Library')?WPST_Icon_Library::options():array(),
   'default'=>'arrow-up-right',
   'label_block'=>true,
   'condition'=>array('icon_source'=>'wpsoft')
  ));

  $r->add_control('elementor_icon',array(
   'label'=>'Elementor Icon',
   'type'=>\Elementor\Controls_Manager::ICONS,
   'default'=>array('value'=>'fas fa-arrow-up-right','library'=>'fa-solid'),
   'condition'=>array('icon_source'=>'elementor')
  ));

  $r->add_control('svg_upload',array(
   'label'=>'SVG Dosyası',
   'type'=>\Elementor\Controls_Manager::MEDIA,
   'media_types'=>array('image'),
   'description'=>'Medya kütüphanesinden bir SVG dosyası seçin.',
   'condition'=>array('icon_source'=>'svg_upload')
  ));

  $r->add_control('svg_code',array(
   'label'=>'SVG Kodu',
   'type'=>\Elementor\Controls_Manager::TEXTAREA,
   'rows'=>8,
   'placeholder'=>'<svg viewBox="0 0 24 24">...</svg>',
   'description'=>'Harici SVG kodunu buraya yapıştırabilirsiniz.',
   'condition'=>array('icon_source'=>'svg_code')
  ));

  $r->add_control('icon_size',array(
   'label'=>'İkon Boyutu',
   'type'=>\Elementor\Controls_Manager::SLIDER,
   'size_units'=>array('px'),
   'range'=>array('px'=>array('min'=>8,'max'=>120,'step'=>1)),
   'default'=>array('size'=>20,'unit'=>'px')
  ));

  $r->add_control('title',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Hizmetler','dynamic'=>array('active'=>true)));
  $r->add_control('text',array('label'=>'Açıklama','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'Neler yaptığımızı keşfedin.','dynamic'=>array('active'=>true)));
  $r->add_control('url',array('label'=>'Bağlantı','type'=>\Elementor\Controls_Manager::URL,'default'=>array('url'=>'#'),'show_external'=>true,'dynamic'=>array('active'=>true)));
  $this->add_control('items',array('label'=>'Linkler','type'=>\Elementor\Controls_Manager::REPEATER,'fields'=>$r->get_controls(),'default'=>array(
   array('icon_source'=>'wpsoft','wpst_icon'=>'briefcase','icon_size'=>array('size'=>20,'unit'=>'px'),'title'=>'Hizmetler','text'=>'Neler yaptığımızı keşfedin.','url'=>array('url'=>'#')),
   array('icon_source'=>'wpsoft','wpst_icon'=>'layers','icon_size'=>array('size'=>20,'unit'=>'px'),'title'=>'Projeler','text'=>'Seçilmiş çalışmalarımızı görün.','url'=>array('url'=>'#')),
   array('icon_source'=>'wpsoft','wpst_icon'=>'message','icon_size'=>array('size'=>20,'unit'=>'px'),'title'=>'İletişim','text'=>'Yeni projenizi konuşalım.','url'=>array('url'=>'#'))
  ),'title_field'=>'{{{ title }}}'));
  $this->add_control('layout',array('label'=>'Yerleşim','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'cards','options'=>array('cards'=>'Modern Cards','rows'=>'Yatay Rows','minimal'=>'Minimal','tiles'=>'Color Tiles'),'prefix_class'=>'wpst-link-grid-layout-'));
  $this->add_responsive_control('columns',array('label'=>'Kolon','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'3','tablet_default'=>'2','mobile_default'=>'1','options'=>array('1'=>'1','2'=>'2','3'=>'3','4'=>'4'),'selectors'=>array('{{WRAPPER}} .wpst-link-grid'=>'--wpst-link-cols:{{VALUE}};')));
  $this->end_controls_section();

  $this->start_controls_section('style_cards',array('label'=>'Kart Biçimi','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_responsive_control('gap',array('label'=>'Kart Aralığı','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>60)),'default'=>array('size'=>16),'tablet_default'=>array('size'=>14),'mobile_default'=>array('size'=>12),'selectors'=>array('{{WRAPPER}} .wpst-link-grid'=>'--wpst-link-gap:{{SIZE}}px;')));
  $this->add_responsive_control('card_padding',array('label'=>'Kart İç Boşluk','type'=>\Elementor\Controls_Manager::DIMENSIONS,'size_units'=>array('px'),'default'=>array('top'=>22,'right'=>22,'bottom'=>22,'left'=>22,'unit'=>'px'),'selectors'=>array('{{WRAPPER}} .wpst-link-grid>a'=>'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};')));
  $this->add_responsive_control('card_radius',array('label'=>'Kart Radius','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>50)),'default'=>array('size'=>20),'selectors'=>array('{{WRAPPER}} .wpst-link-grid'=>'--wpst-link-radius:{{SIZE}}px;')));
  $this->add_control('surface',array('label'=>'Kart Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-link-grid'=>'--wpst-link-surface:{{VALUE}};')));
  $this->add_control('border_color',array('label'=>'Kenarlık','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-link-grid'=>'--wpst-link-border:{{VALUE}};')));
  $this->add_control('hover_surface',array('label'=>'Hover Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-link-grid'=>'--wpst-link-hover-surface:{{VALUE}};')));
  $this->add_control('hover_motion',array('label'=>'Hover Hareketi','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'lift','options'=>array('none'=>'Yok','lift'=>'Hafif Yüksel','slide'=>'Sağa Akış'),'prefix_class'=>'wpst-link-motion-'));
  $this->end_controls_section();

  $this->start_controls_section('style_content',array('label'=>'İçerik Biçimi','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('title_color',array('label'=>'Başlık Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-link-grid'=>'--wpst-link-title:{{VALUE}};')));
  $this->add_control('text_color',array('label'=>'Açıklama Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-link-grid'=>'--wpst-link-text:{{VALUE}};')));
  $this->add_control('icon_color',array('label'=>'İkon Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-link-grid'=>'--wpst-link-icon:{{VALUE}};')));
  $this->add_control('icon_bg',array('label'=>'İkon Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-link-grid'=>'--wpst-link-icon-bg:{{VALUE}};')));
  $this->add_control('arrow_color',array('label'=>'Ok Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-link-grid'=>'--wpst-link-arrow:{{VALUE}};')));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'title_typography','label'=>'Başlık Tipografi','selector'=>'{{WRAPPER}} .wpst-link-grid strong'));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'text_typography','label'=>'Açıklama Tipografi','selector'=>'{{WRAPPER}} .wpst-link-grid small'));
  $this->end_controls_section();
  $this->standard_responsive_controls();
 }
 private function allowed_svg_html(){
  return array(
   'svg'=>array(
    'xmlns'=>true,'viewbox'=>true,'viewBox'=>true,'width'=>true,'height'=>true,
    'fill'=>true,'stroke'=>true,'stroke-width'=>true,'stroke-linecap'=>true,
    'stroke-linejoin'=>true,'class'=>true,'role'=>true,'aria-hidden'=>true,
    'focusable'=>true,'preserveAspectRatio'=>true
   ),
   'g'=>array('fill'=>true,'stroke'=>true,'stroke-width'=>true,'transform'=>true,'opacity'=>true),
   'path'=>array('d'=>true,'fill'=>true,'stroke'=>true,'stroke-width'=>true,'stroke-linecap'=>true,'stroke-linejoin'=>true,'opacity'=>true,'transform'=>true),
   'circle'=>array('cx'=>true,'cy'=>true,'r'=>true,'fill'=>true,'stroke'=>true,'stroke-width'=>true,'opacity'=>true),
   'rect'=>array('x'=>true,'y'=>true,'width'=>true,'height'=>true,'rx'=>true,'ry'=>true,'fill'=>true,'stroke'=>true,'stroke-width'=>true,'opacity'=>true,'transform'=>true),
   'line'=>array('x1'=>true,'y1'=>true,'x2'=>true,'y2'=>true,'stroke'=>true,'stroke-width'=>true,'stroke-linecap'=>true),
   'polyline'=>array('points'=>true,'fill'=>true,'stroke'=>true,'stroke-width'=>true,'stroke-linecap'=>true,'stroke-linejoin'=>true),
   'polygon'=>array('points'=>true,'fill'=>true,'stroke'=>true,'stroke-width'=>true,'stroke-linejoin'=>true),
   'ellipse'=>array('cx'=>true,'cy'=>true,'rx'=>true,'ry'=>true,'fill'=>true,'stroke'=>true,'stroke-width'=>true),
   'defs'=>array(),
   'lineargradient'=>array('id'=>true,'x1'=>true,'y1'=>true,'x2'=>true,'y2'=>true,'gradientUnits'=>true),
   'linearGradient'=>array('id'=>true,'x1'=>true,'y1'=>true,'x2'=>true,'y2'=>true,'gradientUnits'=>true),
   'radialgradient'=>array('id'=>true,'cx'=>true,'cy'=>true,'r'=>true,'gradientUnits'=>true),
   'radialGradient'=>array('id'=>true,'cx'=>true,'cy'=>true,'r'=>true,'gradientUnits'=>true),
   'stop'=>array('offset'=>true,'stop-color'=>true,'stop-opacity'=>true)
  );
 }

 private function render_item_icon($item){
  $source=isset($item['icon_source'])?sanitize_key($item['icon_source']):'wpsoft';
  $size=20;
  if(isset($item['icon_size']['size']) && is_numeric($item['icon_size']['size'])){
   $size=max(8,min(120,(float)$item['icon_size']['size']));
  }
  $style='--wpst-link-item-icon-size:'.esc_attr($size).'px;';

  echo '<span class="wpst-link-icon" style="'.$style.'">';

  if('elementor'===$source && !empty($item['elementor_icon']) && class_exists('\\Elementor\\Icons_Manager')){
   \Elementor\Icons_Manager::render_icon($item['elementor_icon'],array('aria-hidden'=>'true'));
  }elseif('svg_upload'===$source && !empty($item['svg_upload']['url'])){
   $svg_url=esc_url($item['svg_upload']['url']);
   echo '<img class="wpst-link-custom-svg" src="'.$svg_url.'" alt="" aria-hidden="true">';
  }elseif('svg_code'===$source && !empty($item['svg_code'])){
   echo '<span class="wpst-link-inline-svg" aria-hidden="true">'.wp_kses($item['svg_code'],$this->allowed_svg_html()).'</span>';
  }else{
   $icon=isset($item['wpst_icon'])?$item['wpst_icon']:'arrow-up-right';
   if(class_exists('WPST_Icon_Library')) echo WPST_Icon_Library::svg($icon,array('size'=>$size));
  }

  echo '</span>';
 }

 protected function render(){
  $s=$this->get_settings_for_display();
  echo '<nav class="wpst-link-grid" aria-label="Hızlı bağlantılar">';
  foreach((array)$s['items'] as $i){
   $link=is_array($i['url']??null)?$i['url']:array('url'=>'#');
   $url=!empty($link['url'])?esc_url($link['url']):'#';
   $target=!empty($link['is_external'])?' target="_blank"':'';
   $rels=array();
   if(!empty($link['nofollow']))$rels[]='nofollow';
   if(!empty($link['is_external']))$rels[]='noopener';
   $rel=$rels?' rel="'.esc_attr(implode(' ',$rels)).'"':'';
   echo '<a href="'.$url.'"'.$target.$rel.'>';
   $this->render_item_icon($i);
   echo '<div><strong>'.esc_html($i['title']).'</strong><small>'.esc_html($i['text']).'</small></div><i class="wpst-link-arrow" aria-hidden="true">'.(class_exists('WPST_Icon_Library')?WPST_Icon_Library::svg('arrow-up-right',array('size'=>15)):'↗').'</i></a>';
  }
  echo '</nav>';
 }
}
