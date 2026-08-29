<?php
if(!defined('ABSPATH'))exit;
class WPST_Widget_Image_Hotspots extends WPST_Elementor_Widget_Base{
 public function get_name(){return'wpsoft-image-hotspots';}
 public function get_title(){return'WPSoft · Image Hotspots Pro';}
 public function get_icon(){return'eicon-image-hotspot';}
 public function get_categories(){return array('wpsoft-media','wpsoft');}
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'Görsel & Noktalar'));
  $this->add_control('image',array('label'=>'Görsel','type'=>\Elementor\Controls_Manager::MEDIA));
  $r=new \Elementor\Repeater();
  $r->add_control('title',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Özellik'));
  $r->add_control('text',array('label'=>'Açıklama','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'Detay bilgisi'));
  $r->add_control('link_text',array('label'=>'Link Metni','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Detayı Gör')); $r->add_control('link',array('label'=>'Bağlantı','type'=>\Elementor\Controls_Manager::URL,'show_external'=>true));
  $r->add_control('x',array('label'=>'X %','type'=>\Elementor\Controls_Manager::NUMBER,'default'=>50,'min'=>0,'max'=>100));
  $r->add_control('y',array('label'=>'Y %','type'=>\Elementor\Controls_Manager::NUMBER,'default'=>50,'min'=>0,'max'=>100));
  $this->add_control('items',array('label'=>'Hotspotlar','type'=>\Elementor\Controls_Manager::REPEATER,'fields'=>$r->get_controls(),'default'=>array(array('title'=>'Premium Malzeme','text'=>'Öne çıkan özellik','x'=>30,'y'=>38),array('title'=>'Modern Detay','text'=>'İkinci açıklama','x'=>72,'y'=>62)),'title_field'=>'{{{ title }}}'));
  $this->add_control('interaction',array('label'=>'Açılma Davranışı','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'hover','options'=>array('hover'=>'Hover / Focus','click'=>'Tıklama')));
  $this->add_control('pulse',array('label'=>'Pulse Efekti','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes'));
  $this->add_control('tooltip_style',array('label'=>'Tooltip Stili','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'dark','options'=>array('dark'=>'Dark','light'=>'Light','glass'=>'Glass')));
  $this->end_controls_section();

  $this->start_controls_section('style',array('label'=>'Hotspot Stili','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('hotspot_bg',array('label'=>'Nokta Rengi','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#ffffff','selectors'=>array('{{WRAPPER}} .wpst-ew-image-hotspots'=>'--wpst-hotspot-bg:{{VALUE}}')));
  $this->add_control('hotspot_color',array('label'=>'İkon Rengi','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#0f172a','selectors'=>array('{{WRAPPER}} .wpst-ew-image-hotspots'=>'--wpst-hotspot-color:{{VALUE}}')));
  $this->add_control('radius',array('label'=>'Görsel Köşesi','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>50)),'default'=>array('size'=>20),'selectors'=>array('{{WRAPPER}} .wpst-ew-image-hotspots'=>'border-radius:{{SIZE}}px')));
  $this->add_responsive_control('image_height',array('label'=>'Görsel Yüksekliği','type'=>\Elementor\Controls_Manager::SLIDER,'size_units'=>array('px','vh'),'range'=>array('px'=>array('min'=>220,'max'=>900),'vh'=>array('min'=>25,'max'=>90)),'selectors'=>array('{{WRAPPER}} .wpst-ew-image-hotspots'=>'height:{{SIZE}}{{UNIT}};','{{WRAPPER}} .wpst-ew-image-hotspots>img'=>'height:100%;object-fit:cover;')));
  $this->add_responsive_control('hotspot_size',array('label'=>'Hotspot Boyutu','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>34,'max'=>72)),'selectors'=>array('{{WRAPPER}} .wpst-hotspot'=>'width:{{SIZE}}px;height:{{SIZE}}px;')));
  $this->add_responsive_control('tooltip_width',array('label'=>'Tooltip Genişliği','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>160,'max'=>420)),'selectors'=>array('{{WRAPPER}} .wpst-hotspot>b'=>'width:{{SIZE}}px;max-width:calc(100vw - 32px);')));
  $this->add_responsive_control('radius_responsive',array('label'=>'Responsive Görsel Köşesi','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>50)),'selectors'=>array('{{WRAPPER}} .wpst-ew-image-hotspots'=>'border-radius:{{SIZE}}px;')));

  $this->end_controls_section();
  $this->standard_responsive_controls();
 }
 protected function render(){
  $s=$this->get_settings_for_display();
  $interaction=('click'===$s['interaction'])?'click':'hover';
  $tooltip=in_array($s['tooltip_style'],array('dark','light','glass'),true)?$s['tooltip_style']:'dark';
  echo'<div class="wpst-ew-image-hotspots tooltip-'.esc_attr($tooltip).('yes'===$s['pulse']?' has-pulse':'').'" data-hotspot-mode="'.esc_attr($interaction).'">';
  echo!empty($s['image']['url'])?'<img src="'.esc_url($s['image']['url']).'" alt="">':'<div class="wpst-media-placeholder">Görsel seçin</div>';
  foreach((array)$s['items'] as $i){
   $link_data=is_array($i['link']??null)?$i['link']:array();
   $link=!empty($link_data['url'])?$link_data['url']:'';
   $link_text=isset($i['link_text'])&&''!==trim((string)$i['link_text'])?$i['link_text']:'Detayı Gör';
   $target=!empty($link_data['is_external'])?' target="_blank"':'';
   $rels=array();if(!empty($link_data['nofollow']))$rels[]='nofollow';if(!empty($link_data['is_external']))$rels[]='noopener';
   $rel=$rels?' rel="'.esc_attr(implode(' ',$rels)).'"':'';
   echo'<button class="wpst-hotspot" type="button" style="left:'.esc_attr($i['x']).'%;top:'.esc_attr($i['y']).'%" aria-expanded="false"><span>+</span><b><strong>'.esc_html($i['title']).'</strong><em>'.esc_html($i['text']).'</em>'.($link?'<a href="'.esc_url($link).'"'.$target.$rel.'>'.esc_html($link_text).' →</a>':'').'</b></button>';
  }
  echo'</div>';
 }
}
