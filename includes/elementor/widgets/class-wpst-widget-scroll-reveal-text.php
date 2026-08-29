<?php
if(!defined('ABSPATH'))exit;
class WPST_Widget_Scroll_Reveal_Text extends WPST_Elementor_Widget_Base{
 public function get_name(){return'wpsoft-scroll-reveal-text';} public function get_title(){return'WPSoft Scroll Reveal Text 2.0';} public function get_icon(){return'eicon-animation-text';}
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'Metin'));
  $this->wpst_signature_preset_control();
  $this->add_control('eyebrow',array('label'=>'Etiket','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'OUR APPROACH'));
  $this->add_control('text',array('label'=>'Büyük Metin','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'Fikirleri, insanların hatırladığı dijital deneyimlere dönüştürüyoruz.'));
  $this->add_control('reveal_mode',array('label'=>'Reveal Modu','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'words','options'=>array('words'=>'Kelime Kelime','line'=>'Satır','fade'=>'Fade'),'prefix_class'=>'wpst-reveal-text-mode-'));
  $this->add_control('align',array('label'=>'Hizalama','type'=>\Elementor\Controls_Manager::CHOOSE,'default'=>'left','options'=>array('left'=>array('title'=>'Sol','icon'=>'eicon-text-align-left'),'center'=>array('title'=>'Orta','icon'=>'eicon-text-align-center'),'right'=>array('title'=>'Sağ','icon'=>'eicon-text-align-right')),'selectors'=>array('{{WRAPPER}} .wpst-ew-scroll-reveal-text'=>'text-align:{{VALUE}};')));
  $this->end_controls_section();
  $this->start_controls_section('style',array('label'=>'Biçim','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('active_color',array('label'=>'Aktif Metin','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#0f172a','selectors'=>array('{{WRAPPER}} .wpst-ew-scroll-reveal-text'=>'--wpst-reveal-active:{{VALUE}};')));
  $this->add_control('muted_color',array('label'=>'Bekleyen Metin','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#cbd5e1','selectors'=>array('{{WRAPPER}} .wpst-ew-scroll-reveal-text'=>'--wpst-reveal-muted:{{VALUE}};')));
  $this->add_responsive_control('font_size',array('label'=>'Metin Boyutu','type'=>\Elementor\Controls_Manager::SLIDER,'size_units'=>array('px','vw'),'range'=>array('px'=>array('min'=>22,'max'=>110),'vw'=>array('min'=>2,'max'=>9,'step'=>.1)),'selectors'=>array('{{WRAPPER}} .wpst-ew-scroll-reveal-text p'=>'font-size:{{SIZE}}{{UNIT}};')));
  $this->add_responsive_control('max_width',array('label'=>'Maks. Genişlik','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>320,'max'=>1400)),'selectors'=>array('{{WRAPPER}} .wpst-ew-scroll-reveal-text'=>'max-width:{{SIZE}}px;')));
  $this->end_controls_section();
  $this->standard_responsive_controls();
 }
 protected function render(){ $s=$this->get_settings_for_display(); $words=preg_split('/\s+/u',trim($s['text'])); echo'<div class="wpst-ew-scroll-reveal-text"><small>'.esc_html($s['eyebrow']).'</small><p>'; foreach($words as $i=>$w)echo'<span style="--i:'.absint($i).'">'.esc_html($w).' </span>'; echo'</p></div>'; }
}