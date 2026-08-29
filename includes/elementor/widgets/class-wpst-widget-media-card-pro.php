<?php
if(!defined('ABSPATH'))exit;
class WPST_Widget_Media_Card_Pro extends WPST_Elementor_Widget_Base{
 public function get_name(){return'wpsoft-media-card-pro';}
 public function get_title(){return'WPSoft · Media Card Pro';}
 public function get_icon(){return'eicon-image-box';}
 public function get_categories(){return array('wpsoft-media','wpsoft-content','wpsoft');}
 public function get_keywords(){return array('media','card','image','video','project','case study','hover');}
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'İçerik'));
  $this->add_control('media_type',array('label'=>'Medya Türü','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'image','options'=>array('image'=>'Görsel','video'=>'Video')));
  $this->add_control('image',array('label'=>'Görsel / Video Kapağı','type'=>\Elementor\Controls_Manager::MEDIA));
  $this->add_control('video_url',array('label'=>'Video URL','type'=>\Elementor\Controls_Manager::URL,'condition'=>array('media_type'=>'video')));
  $this->add_control('eyebrow',array('label'=>'Etiket','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Selected Work'));
  $this->add_control('title',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Modern proje sunumu'));
  $this->add_control('description',array('label'=>'Açıklama','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'Görsel veya video ile güçlü bir içerik kartı oluşturun.'));
  $this->add_control('link_text',array('label'=>'Link Metni','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Projeyi İncele'));
  $this->add_control('link',array('label'=>'Bağlantı','type'=>\Elementor\Controls_Manager::URL));
  $this->add_control('video_lightbox',array('label'=>'Videoyu Lightbox Aç','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes','condition'=>array('media_type'=>'video')));
  $this->end_controls_section();

  $this->start_controls_section('style',array('label'=>'Kart Stili','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('preset',array('label'=>'Preset','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'editorial','options'=>array('editorial'=>'Editorial','glass'=>'Glass','dark'=>'Dark','minimal'=>'Minimal'),'prefix_class'=>'wpst-media-card-'));
  $this->add_responsive_control('media_height',array('label'=>'Medya Yüksekliği','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>180,'max'=>800)),'default'=>array('size'=>420,'unit'=>'px'),'selectors'=>array('{{WRAPPER}} .wpst-media-card-media'=>'--wpst-media-height:{{SIZE}}{{UNIT}};')));
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

  $this->add_control('radius',array('label'=>'Köşe','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>50)),'default'=>array('size'=>22),'selectors'=>array('{{WRAPPER}} .wpst-media-card-pro'=>'border-radius:{{SIZE}}px')));
  $this->end_controls_section();
  $this->standard_responsive_controls();
 }
 private function embed_url($url){
  if(preg_match('~youtu(?:\.be/|be\.com/(?:watch\?v=|shorts/|embed/))([^?&/]+)~i',$url,$m))return'https://www.youtube.com/embed/'.rawurlencode($m[1]).'?autoplay=1&rel=0';
  if(preg_match('~vimeo\.com/(?:video/)?(\d+)~i',$url,$m))return'https://player.vimeo.com/video/'.rawurlencode($m[1]).'?autoplay=1';
  return esc_url_raw($url);
 }
 protected function render(){
  $s=$this->get_settings_for_display();$img=!empty($s['image']['url'])?$s['image']['url']:'';
  $is_video='video'===$s['media_type'];$video=!empty($s['video_url']['url'])?$s['video_url']['url']:'';
  echo'<article class="wpst-media-card-pro">';
  if($is_video&&'yes'===$s['video_lightbox']){
   echo'<button type="button" class="wpst-media-card-media" data-wpst-video-open data-video="'.esc_url($this->embed_url($video)).'">';
  }else{
   $url=!empty($s['link']['url'])?$s['link']['url']:'#';
   echo'<a class="wpst-media-card-media" href="'.esc_url($url).'">';
  }
  if($img)echo'<img src="'.esc_url($img).'" alt="" loading="lazy">';else echo'<span class="wpst-media-placeholder">Media</span>';
  echo'<span class="wpst-media-card-overlay"></span>';
  if($is_video)echo'<span class="wpst-media-card-play" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M9 7.5 17 12l-8 4.5z"/></svg></span>';
  echo($is_video&&'yes'===$s['video_lightbox'])?'</button>':'</a>';
  echo'<div class="wpst-media-card-copy">';
  if($s['eyebrow'])echo'<small>'.esc_html($s['eyebrow']).'</small>';
  if($s['title'])echo'<h3>'.esc_html($s['title']).'</h3>';
  if($s['description'])echo'<p>'.esc_html($s['description']).'</p>';
  if(!$is_video||'yes'!=$s['video_lightbox']){
   if(!empty($s['link']['url'])&&$s['link_text'])echo'<a href="'.esc_url($s['link']['url']).'">'.esc_html($s['link_text']).' <span class="wpst-cta-arrow is-diagonal" aria-hidden="true"></span></a>';
  }
  echo'</div></article>';
 }
}
