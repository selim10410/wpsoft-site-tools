<?php
if(!defined('ABSPATH'))exit;
class WPST_Widget_Image_Text extends WPST_Elementor_Widget_Base{
 public function get_name(){return'wpsoft-image-text';}
 public function get_title(){return'WPSoft · Image + Text 2.0';}
 public function get_icon(){return'eicon-image-box';}
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'İçerik'));
  $this->wpst_signature_preset_control();
  $this->add_control('eyebrow',array('label'=>'Üst Etiket','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'HAKKIMIZDA'));
  $this->add_control('title',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'İşletmenizi dijitalde güçlü bir yapıyla temsil edin'));
  $this->add_control('description',array('label'=>'Açıklama','type'=>\Elementor\Controls_Manager::WYSIWYG,'default'=>'Modern tasarım, güçlü içerik ve kullanıcı deneyimini tek bir yapıda birleştirin.'));
  $this->add_control('image',array('label'=>'Görsel','type'=>\Elementor\Controls_Manager::MEDIA));
  $this->add_control('placeholder_text',array('label'=>'Görsel Yoksa Yazı','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'WPSoft'));
  $this->link_controls('button','Buton');
  $this->add_control('button_icon',array('label'=>'Buton Icon','type'=>\Elementor\Controls_Manager::SELECT2,'options'=>class_exists('WPST_Icon_Library')?WPST_Icon_Library::options():array(),'default'=>'arrow-right','label_block'=>true));
  $this->add_control('reverse',array('label'=>'Görsel Sağda','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>''));
  $this->add_control('layout_style',array(
   'label'=>'Stil',
   'type'=>\Elementor\Controls_Manager::SELECT,
   'default'=>'clean',
   'options'=>array(
    'clean'=>'Clean Split',
    'overlap'=>'Overlap Card',
    'editorial'=>'Editorial',
    'soft'=>'Soft Surface',
    'full-media'=>'Full Media',
    'frame'=>'Framed',
    'compact'=>'Compact',
    'magazine'=>'Magazine'
   ),
   'prefix_class'=>'wpst-image-text-style-'
  ));
  $this->end_controls_section();

  $this->start_controls_section('style_media',array('label'=>'Görsel','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_responsive_control('radius',array('label'=>'Görsel Köşesi','type'=>\Elementor\Controls_Manager::SLIDER,'default'=>array('size'=>28),'range'=>array('px'=>array('min'=>0,'max'=>60)),'selectors'=>array('{{WRAPPER}} .wpst-ew-image-text-media'=>'border-radius:{{SIZE}}px;')));
  $this->add_responsive_control('min_height',array('label'=>'Görsel Min. Yükseklik','type'=>\Elementor\Controls_Manager::SLIDER,'size_units'=>array('px'),'range'=>array('px'=>array('min'=>220,'max'=>800)),'default'=>array('size'=>420,'unit'=>'px'),'selectors'=>array('{{WRAPPER}} .wpst-ew-image-text-media'=>'--wpst-media-height:{{SIZE}}{{UNIT}};')));
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

  $this->start_controls_section('style_text',array('label'=>'Metin','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('accent',array('label'=>'Vurgu','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#2563eb','selectors'=>array('{{WRAPPER}} .wpst-ew-image-text'=>'--it-accent:{{VALUE}};')));
  $this->add_control('title_color',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#0f172a','selectors'=>array('{{WRAPPER}} .wpst-ew-image-text'=>'--it-title:{{VALUE}};')));
  $this->add_control('text_color',array('label'=>'Metin','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#64748b','selectors'=>array('{{WRAPPER}} .wpst-ew-image-text'=>'--it-text:{{VALUE}};')));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'title_typography','selector'=>'{{WRAPPER}} .wpst-ew-image-text-copy h2'));
  $this->end_controls_section();
  $this->standard_responsive_controls();
 }
 protected function render(){
  $s=$this->get_settings_for_display();
  $cl=!empty($s['reverse'])?' is-reverse':'';
  $img=!empty($s['image']['url'])?'<img src="'.esc_url($s['image']['url']).'" alt="">':'<div class="wpst-ew-media-placeholder">'.esc_html($s['placeholder_text']).'</div>';
  echo'<section class="wpst-ew-image-text'.$cl.'"><div class="wpst-ew-image-text-media">'.$img.'</div><div class="wpst-ew-image-text-copy"><small>'.esc_html($s['eyebrow']).'</small><h2>'.wp_kses_post($s['title']).'</h2><div class="wpst-ew-rich">'.wp_kses_post($s['description']).'</div>';
  if(!empty($s['button_text']))echo'<a'.$this->render_link_attrs($s['button_url']).'><span>'.esc_html($s['button_text']).'</span><i>'.(class_exists('WPST_Icon_Library')?WPST_Icon_Library::svg($s['button_icon'],array('size'=>15)):'→').'</i></a>';
  echo'</div></section>';
 }
}
