<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class WPST_Widget_Marquee_Text extends WPST_Elementor_Widget_Base {
 public function get_name(){return 'wpsoft-marquee-text';}
 public function get_title(){return 'WPSoft Kayan Yazı 2.0';}
 public function get_icon(){return 'eicon-animation-text';}
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'İçerik'));
  $this->wpst_signature_preset_control();
  $this->add_control('text',array('label'=>'Metin','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'WEB TASARIM • E-TİCARET • SEO • DİJİTAL ÇÖZÜMLER'));
  $this->add_control('speed',array('label'=>'Hız (sn)','type'=>\Elementor\Controls_Manager::NUMBER,'default'=>20,'min'=>6,'max'=>80));
  $this->add_control('direction',array('label'=>'Yön','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'left','options'=>array('left'=>'Sola','right'=>'Sağa'),'prefix_class'=>'wpst-marquee-text-dir-'));
  $this->add_control('pause_hover',array('label'=>'Hover’da Duraklat','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes','prefix_class'=>'wpst-marquee-text-pause-'));
  $this->add_control('separator',array('label'=>'Ayırıcı','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'•'));
  $this->end_controls_section();
  $this->start_controls_section('style',array('label'=>'Biçim','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('bg',array('label'=>'Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#0f172a','selectors'=>array('{{WRAPPER}} .wpst-ew-marquee-text'=>'background:{{VALUE}}')));
  $this->add_control('color',array('label'=>'Metin Rengi','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#ffffff','selectors'=>array('{{WRAPPER}} .wpst-ew-marquee-text'=>'color:{{VALUE}}')));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'text_typography','label'=>'Metin Tipografisi','selector'=>'{{WRAPPER}} .wpst-ew-marquee-text'));
  $this->add_control('separator_color',array('label'=>'Ayırıcı Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-marquee-text'=>'--wpst-marquee-separator:{{VALUE}};')));
  $this->add_responsive_control('font_size',array('label'=>'Yazı Boyutu','type'=>\Elementor\Controls_Manager::SLIDER,'size_units'=>array('px','vw'),'range'=>array('px'=>array('min'=>12,'max'=>100),'vw'=>array('min'=>1,'max'=>8,'step'=>.1)),'selectors'=>array('{{WRAPPER}} .wpst-ew-marquee-text'=>'font-size:{{SIZE}}{{UNIT}};')));
  $this->add_responsive_control('padding_y',array('label'=>'Dikey İç Boşluk','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>60)),'selectors'=>array('{{WRAPPER}} .wpst-ew-marquee-text'=>'padding-block:{{SIZE}}px;')));
  $this->add_responsive_control('gap',array('label'=>'Tekrar Aralığı','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>10,'max'=>160)),'selectors'=>array('{{WRAPPER}} .wpst-ew-marquee-text>div'=>'gap:{{SIZE}}px;')));
  $this->add_responsive_control('radius',array('label'=>'Köşe','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>60)),'selectors'=>array('{{WRAPPER}} .wpst-ew-marquee-text'=>'border-radius:{{SIZE}}px;')));
  $this->add_control('border_color',array('label'=>'Kenarlık Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-marquee-text'=>'border-color:{{VALUE}};border-style:solid;border-width:1px;')));
  $this->end_controls_section();
  $this->standard_responsive_controls();
 }
 protected function render(){ $s=$this->get_settings_for_display();$content=trim((string)$s['text']);if(trim((string)$s['separator'])!=='')$content.=' '.trim((string)$s['separator']);echo '<div class="wpst-ew-marquee-text" style="--wpst-marquee-text-speed:'.esc_attr((int)$s['speed']).'s"><div><span>'.esc_html($content).'</span><span>'.esc_html($content).'</span></div></div>'; }
}