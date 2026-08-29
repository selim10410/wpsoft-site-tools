<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class WPST_Widget_Floating_Icons extends WPST_Elementor_Widget_Base {
 public function get_name(){return 'wpsoft-floating-icons';}
 public function get_title(){return 'WPSoft Floating Simge Kartları';}
 public function get_icon(){return 'eicon-icon-box';}
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'Kartlar'));
  $this->wpst_signature_preset_control();
  $r=new \Elementor\Repeater();
  $r->add_control('wpst_icon',array('label'=>'WPSoft Icon','type'=>\Elementor\Controls_Manager::SELECT2,'options'=>class_exists('WPST_Icon_Library')?WPST_Icon_Library::options():array(),'default'=>'star','label_block'=>true));$r->add_control('icon',array('label'=>'Elementor Icon (Eski)','type'=>\Elementor\Controls_Manager::ICONS,'default'=>array('value'=>'fas fa-star','library'=>'fa-solid')));
  $r->add_control('title',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Premium'));
  $r->add_control('text',array('label'=>'Alt Metin','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Modern deneyim'));
  $this->add_control('items',array('label'=>'Kartlar','type'=>\Elementor\Controls_Manager::REPEATER,'fields'=>$r->get_controls(),'default'=>array(
   array('title'=>'Premium','text'=>'Modern deneyim'),
   array('title'=>'Hızlı','text'=>'Yüksek performans'),
   array('title'=>'Güvenli','text'=>'Sağlam altyapı')
  ),'title_field'=>'{{{ title }}}'));
  $this->end_controls_section();
 
        
  $this->start_controls_section('quality_style',array('label'=>'Görünüm','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('layout_variant',array('label'=>'Yerleşim','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'floating','options'=>array('floating'=>'Floating','grid'=>'Grid','compact'=>'Compact'),'prefix_class'=>'wpst-floating-icons-layout-'));
  $this->add_responsive_control('columns',array('label'=>'Kolon','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'3','tablet_default'=>'2','mobile_default'=>'1','options'=>array('1'=>'1','2'=>'2','3'=>'3','4'=>'4'),'selectors'=>array('{{WRAPPER}} .wpst-ew-floating-icons'=>'grid-template-columns:repeat({{VALUE}},minmax(0,1fr));')));
  $this->add_control('card_bg',array('label'=>'Kart Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-floating-icons article'=>'background:{{VALUE}};')));
  $this->add_control('icon_color',array('label'=>'Icon Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-floating-icons article>div'=>'color:{{VALUE}};')));
  $this->add_control('icon_bg',array('label'=>'Icon Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-floating-icons article>div'=>'background:{{VALUE}};')));
  $this->add_responsive_control('icon_size',array('label'=>'Icon Alanı','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>28,'max'=>80)),'default'=>array('size'=>46),'selectors'=>array('{{WRAPPER}} .wpst-ew-floating-icons article>div'=>'width:{{SIZE}}px;height:{{SIZE}}px;')));
  $this->add_control('title_color',array('label'=>'Başlık Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-floating-icons strong'=>'color:{{VALUE}};')));
  $this->add_control('text_color',array('label'=>'Metin Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-floating-icons span'=>'color:{{VALUE}};')));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'title_typography','label'=>'Başlık Tipografisi','selector'=>'{{WRAPPER}} .wpst-ew-floating-icons strong'));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'text_typography','label'=>'Metin Tipografisi','selector'=>'{{WRAPPER}} .wpst-ew-floating-icons span'));
  $this->add_responsive_control('gap',array('label'=>'Kart Aralığı','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>60)),'selectors'=>array('{{WRAPPER}} .wpst-ew-floating-icons'=>'gap:{{SIZE}}px;')));
  $this->add_responsive_control('radius',array('label'=>'Köşe','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>50)),'selectors'=>array('{{WRAPPER}} .wpst-ew-floating-icons article'=>'border-radius:{{SIZE}}px;')));
  $this->add_responsive_control('card_padding',array('label'=>'Kart İç Boşluk','type'=>\Elementor\Controls_Manager::DIMENSIONS,'size_units'=>array('px'),'selectors'=>array('{{WRAPPER}} .wpst-ew-floating-icons article'=>'padding:{{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;')));
  $this->add_control('border_color',array('label'=>'Kenarlık Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-floating-icons article'=>'border-color:{{VALUE}};')));
  $this->add_control('hover_lift',array('label'=>'Hover Yükselme','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes','prefix_class'=>'wpst-floating-hover-'));
  $this->end_controls_section();
        $this->standard_responsive_controls();
    }
 protected function render(){ $s=$this->get_settings_for_display(); echo '<div class="wpst-ew-floating-icons">'; foreach((array)$s['items'] as $i){ echo '<article><div>'; if(!empty($i['wpst_icon'])&&class_exists('WPST_Icon_Library'))WPST_Icon_Library::render($i['wpst_icon']);else \Elementor\Icons_Manager::render_icon($i['icon'],array('aria-hidden'=>'true')); echo '</div><strong>'.esc_html($i['title']).'</strong><span>'.esc_html($i['text']).'</span></article>'; } echo '</div>'; }
}