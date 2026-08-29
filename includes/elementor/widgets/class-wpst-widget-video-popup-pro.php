<?php
if(!defined('ABSPATH'))exit;
class WPST_Widget_Video_Popup_Pro extends WPST_Elementor_Widget_Base{
 public function get_name(){return'wpsoft-video-popup-pro';}
 public function get_title(){return'WPSoft · Video Popup Pro 2.0';}
 public function get_icon(){return'eicon-play-o';}
 public function get_categories(){return array('wpsoft-media','wpsoft');}
 public function get_keywords(){return array('video','popup','lightbox','youtube','vimeo','media','wpsoft');}
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'Video'));
  $this->wpst_signature_preset_control();
  $this->add_control('image',array('label'=>'Kapak Görseli','type'=>\Elementor\Controls_Manager::MEDIA));
  $this->add_control('url',array('label'=>'Video URL','type'=>\Elementor\Controls_Manager::URL,'placeholder'=>'https://youtube.com/...','show_external'=>true,'dynamic'=>array('active'=>true)));
  $this->add_control('title',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Hikâyemizi İzleyin','dynamic'=>array('active'=>true)));
  $this->add_control('subtitle',array('label'=>'Küçük Metin','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Play video','dynamic'=>array('active'=>true)));
  $this->add_control('open_mode',array('label'=>'Açılış','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'lightbox','options'=>array('lightbox'=>'Lightbox','new_tab'=>'Yeni Sekme')));
  $this->add_control('play_style',array('label'=>'Play Butonu','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'glass','options'=>array('solid'=>'Solid','glass'=>'Glass','outline'=>'Outline'),'prefix_class'=>'wpst-video-play-style-'));
  $this->add_control('content_position',array('label'=>'İçerik Konumu','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'bottom-left','options'=>array('bottom-left'=>'Sol Alt','bottom-center'=>'Orta Alt','center'=>'Tam Orta'),'prefix_class'=>'wpst-video-copy-pos-'));
  $this->end_controls_section();

  $this->start_controls_section('style_media',array('label'=>'Görsel & Yüzey','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_responsive_control('height',array('label'=>'Yükseklik','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>220,'max'=>900)),'default'=>array('size'=>420,'unit'=>'px'),'tablet_default'=>array('size'=>380,'unit'=>'px'),'mobile_default'=>array('size'=>300,'unit'=>'px'),'selectors'=>array('{{WRAPPER}} .wpst-video-popup-pro'=>'min-height:{{SIZE}}{{UNIT}};')));
  $this->add_responsive_control('radius',array('label'=>'Köşe','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>60)),'default'=>array('size'=>22),'selectors'=>array('{{WRAPPER}} .wpst-video-popup-pro'=>'--wpst-video-radius:{{SIZE}}px;')));
  $this->add_control('overlay',array('label'=>'Overlay','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-video-popup-pro'=>'--wpst-video-overlay:{{VALUE}};')));
  $this->add_control('border',array('label'=>'Kenarlık','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-video-popup-pro'=>'--wpst-video-border:{{VALUE}};')));
  $this->add_control('image_filter',array('label'=>'Görsel Tonu','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'normal','options'=>array('normal'=>'Normal','cinematic'=>'Cinematic','soft'=>'Soft','mono'=>'Monochrome'),'prefix_class'=>'wpst-video-filter-'));
  $this->end_controls_section();

  $this->start_controls_section('style_content',array('label'=>'İçerik Biçimi','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('title_color',array('label'=>'Başlık Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-video-popup-pro'=>'--wpst-video-title:{{VALUE}};')));
  $this->add_control('subtitle_color',array('label'=>'Küçük Metin Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-video-popup-pro'=>'--wpst-video-subtitle:{{VALUE}};')));
  $this->add_control('play_color',array('label'=>'Play İkon Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-video-popup-pro'=>'--wpst-video-play:{{VALUE}};')));
  $this->add_control('play_bg',array('label'=>'Play Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-video-popup-pro'=>'--wpst-video-play-bg:{{VALUE}};')));
  $this->add_responsive_control('play_size',array('label'=>'Play Kutu Boyutu','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>44,'max'=>110)),'default'=>array('size'=>68),'selectors'=>array('{{WRAPPER}} .wpst-video-popup-pro'=>'--wpst-video-play-size:{{SIZE}}px;')));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'title_typography','label'=>'Başlık Tipografi','selector'=>'{{WRAPPER}} .wpst-video-popup-copy strong'));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'subtitle_typography','label'=>'Küçük Metin Tipografi','selector'=>'{{WRAPPER}} .wpst-video-popup-copy small'));
  $this->end_controls_section();
  $this->standard_responsive_controls();
 }
 private function embed_url($url){
  if(preg_match('~youtu(?:\.be/|be\.com/(?:watch\?v=|shorts/|embed/))([^?&/]+)~i',$url,$m))return'https://www.youtube.com/embed/'.rawurlencode($m[1]).'?autoplay=1&rel=0';
  if(preg_match('~vimeo\.com/(?:video/)?(\d+)~i',$url,$m))return'https://player.vimeo.com/video/'.rawurlencode($m[1]).'?autoplay=1';
  return esc_url_raw($url);
 }
 protected function render(){
  $s=$this->get_settings_for_display();$link=is_array($s['url']??null)?$s['url']:array();
  $u=!empty($link['url'])?$link['url']:'';
  $style=!empty($s['image']['url'])?' style="background-image:url('.esc_url($s['image']['url']).')"':'';
  if('new_tab'===$s['open_mode']){
    $target=!empty($link['is_external'])?' target="_blank"':' target="_blank"';
    $rels=array('noopener');if(!empty($link['nofollow']))$rels[]='nofollow';
    echo'<a class="wpst-video-popup-pro" href="'.esc_url($u).'"'.$target.' rel="'.esc_attr(implode(' ',$rels)).'"'.$style.'>';
  }else{
    echo'<button type="button" class="wpst-video-popup-pro" data-wpst-video-open data-video="'.esc_url($this->embed_url($u)).'" aria-label="'.esc_attr($s['title']).'"'.$style.'>';
  }
  echo'<span class="wpst-video-popup-scrim"></span><span class="wpst-video-play" aria-hidden="true">'.(class_exists('WPST_Icon_Library')?WPST_Icon_Library::svg('play',array('size'=>24)):'▶').'</span><span class="wpst-video-popup-copy"><small>'.esc_html($s['subtitle']).'</small><strong>'.esc_html($s['title']).'</strong></span>';
  echo('new_tab'===$s['open_mode'])?'</a>':'</button>';
 }
}
