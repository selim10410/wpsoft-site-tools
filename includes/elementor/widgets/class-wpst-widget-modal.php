<?php
if(!defined('ABSPATH'))exit;
class WPST_Widget_Modal extends WPST_Elementor_Widget_Base{
 public function get_name(){return'wpsoft-modal';}
 public function get_title(){return'WPSoft · Modal / Popup';}
 public function get_icon(){return'eicon-lightbox';}
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'Modal'));
  $this->add_control('trigger_text',array('label'=>'Tetikleyici Buton','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Detayları Aç'));
  $this->add_control('eyebrow',array('label'=>'Etiket','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'WPSOFT MODAL'));
  $this->add_control('title',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Modern popup içeriği'));
  $this->add_control('text',array('label'=>'Açıklama','type'=>\Elementor\Controls_Manager::WYSIWYG,'default'=>'Kampanya, form, duyuru veya özel içerik için kullanabilirsiniz.'));
  $this->add_control('close_text',array('label'=>'Kapat Yazısı','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Kapat'));
  $this->add_control('size',array('label'=>'Boyut','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'md','options'=>array('sm'=>'Küçük','md'=>'Orta','lg'=>'Büyük')));
  $this->end_controls_section();
  $this->start_controls_section('style',array('label'=>'Modal Biçimi','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('accent',array('label'=>'Vurgu Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-modal-widget'=>'--modal-accent:{{VALUE}};')));
  $this->add_responsive_control('modal_width',array('label'=>'Modal Genişliği','type'=>\Elementor\Controls_Manager::SLIDER,'size_units'=>array('px','vw'),'range'=>array('px'=>array('min'=>280,'max'=>1100),'vw'=>array('min'=>40,'max'=>96)),'selectors'=>array('{{WRAPPER}} .wpst-modal-dialog'=>'width:{{SIZE}}{{UNIT}};max-width:calc(100vw - 28px);')));
  $this->add_responsive_control('modal_padding',array('label'=>'Modal İç Boşluk','type'=>\Elementor\Controls_Manager::DIMENSIONS,'size_units'=>array('px'),'selectors'=>array('{{WRAPPER}} .wpst-modal-dialog'=>'padding:{{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;')));
  $this->add_responsive_control('modal_radius',array('label'=>'Modal Köşe','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>50)),'selectors'=>array('{{WRAPPER}} .wpst-modal-dialog'=>'border-radius:{{SIZE}}px;')));
  $this->add_responsive_control('trigger_width',array('label'=>'Tetikleyici Buton Genişliği','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'auto','tablet_default'=>'auto','mobile_default'=>'100%','options'=>array('auto'=>'Otomatik','100%'=>'Tam Genişlik'),'selectors'=>array('{{WRAPPER}} .wpst-modal-trigger'=>'width:{{VALUE}};')));
  $this->add_responsive_control('content_max_height',array('label'=>'İçerik Maks. Yüksekliği','type'=>\Elementor\Controls_Manager::SLIDER,'size_units'=>array('vh','px'),'range'=>array('vh'=>array('min'=>40,'max'=>90),'px'=>array('min'=>240,'max'=>900)),'selectors'=>array('{{WRAPPER}} .wpst-modal-dialog'=>'max-height:{{SIZE}}{{UNIT}};overflow:auto;')));

  $this->end_controls_section();$this->standard_responsive_controls();
 }
 protected function render(){ $s=$this->get_settings_for_display();$id='wpst-modal-'.$this->get_id();$title_id=$id.'-title';echo'<div class="wpst-modal-widget"><button type="button" class="wpst-modal-trigger" data-modal-open="'.esc_attr($id).'" aria-controls="'.esc_attr($id).'" aria-haspopup="dialog">'.esc_html($s['trigger_text']).'</button><div class="wpst-modal-overlay" id="'.esc_attr($id).'" aria-hidden="true"><div class="wpst-modal-dialog size-'.esc_attr($s['size']).'" role="dialog" aria-modal="true" aria-labelledby="'.esc_attr($title_id).'"><button type="button" class="wpst-modal-close" data-modal-close aria-label="'.esc_attr($s['close_text']).'">×</button><small>'.esc_html($s['eyebrow']).'</small><h3 id="'.esc_attr($title_id).'">'.esc_html($s['title']).'</h3><div class="wpst-modal-content">'.wp_kses_post($s['text']).'</div><button type="button" class="wpst-modal-footer-close" data-modal-close>'.esc_html($s['close_text']).'</button></div></div></div>'; }
}