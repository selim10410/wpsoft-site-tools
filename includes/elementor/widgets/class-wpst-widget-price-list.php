<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class WPST_Widget_Price_List extends WPST_Elementor_Widget_Base {
 public function get_name(){ return 'wpsoft-price-list'; }
 public function get_title(){ return 'WPSoft · Price / Menu List'; }
 public function get_icon(){ return 'eicon-price-list'; }
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'Liste'));
  $this->wpst_signature_preset_control();
  $r=new \Elementor\Repeater();
  $r->add_control('title',array('label'=>'Ürün / Hizmet','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Signature Menü'));
  $r->add_control('description',array('label'=>'Açıklama','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'Özenle hazırlanmış özel seçenek.'));
  $r->add_control('price',array('label'=>'Fiyat','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'₺450'));
  $r->add_control('badge',array('label'=>'Etiket','type'=>\Elementor\Controls_Manager::TEXT,'default'=>''));
  $this->add_control('items',array('label'=>'Kalemler','type'=>\Elementor\Controls_Manager::REPEATER,'fields'=>$r->get_controls(),'default'=>array(
   array('title'=>'Signature Menü','description'=>'Özenle hazırlanmış özel seçenek.','price'=>'₺450','badge'=>'Şefin Seçimi'),
   array('title'=>'Classic Menü','description'=>'Dengeli ve sevilen klasikler.','price'=>'₺320'),
   array('title'=>'Seasonal Menü','description'=>'Mevsim ürünleriyle hazırlanan içerik.','price'=>'₺390')
  ),'title_field'=>'{{{ title }}}'));
  $this->add_control('layout',array('label'=>'Yerleşim','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'editorial','options'=>array('editorial'=>'Editorial','cards'=>'Cards','compact'=>'Compact','dots'=>'Dotted Menu'),'prefix_class'=>'wpst-price-list-layout-'));
  $this->end_controls_section();
  
  $this->start_controls_section('quality_style',array('label'=>'Price List Biçimi','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('surface_bg',array('label'=>'Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-price-list article'=>'background:{{VALUE}};')));
  $this->add_responsive_control('quality_gap',array('label'=>'Aralık','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>60)),'selectors'=>array('{{WRAPPER}}'=>'--wpst-quality-gap:{{SIZE}}px;')));
  $this->add_responsive_control('quality_radius',array('label'=>'Köşe','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>60)),'selectors'=>array('{{WRAPPER}} .wpst-price-list article'=>'border-radius:{{SIZE}}px;')));
  $this->add_responsive_control('quality_padding',array('label'=>'İç Boşluk','type'=>\Elementor\Controls_Manager::DIMENSIONS,'size_units'=>array('px'),'selectors'=>array('{{WRAPPER}} .wpst-price-list article'=>'padding:{{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;')));
  $this->end_controls_section();
        $this->standard_responsive_controls();
 }
 protected function render(){
  $s=$this->get_settings_for_display();
  echo '<div class="wpst-price-list">';
  foreach((array)$s['items'] as $i){
   echo '<article><div class="wpst-price-list-copy"><div><h3>'.esc_html($i['title']).'</h3>';
   if(trim((string)$i['badge'])!=='') echo '<span>'.esc_html($i['badge']).'</span>';
   echo '</div><p>'.esc_html($i['description']).'</p></div><b>'.esc_html($i['price']).'</b></article>';
  }
  echo '</div>';
 }
}