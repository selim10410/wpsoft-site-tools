<?php
if(!defined('ABSPATH'))exit;
class WPST_Widget_Progress_Pro extends WPST_Elementor_Widget_Base{
 public function get_name(){return'wpsoft-progress-pro';}public function get_title(){return'WPSoft · Progress Pro 2.0';}public function get_icon(){return'eicon-skill-bar';}
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'İlerleme'));
  $this->wpst_signature_preset_control();
  $r=new \Elementor\Repeater();
  $r->add_control('title',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Web Tasarım'));
  $r->add_control('value',array('label'=>'Yüzde','type'=>\Elementor\Controls_Manager::NUMBER,'min'=>0,'max'=>100,'default'=>90));
  $r->add_control('note',array('label'=>'Kısa Not','type'=>\Elementor\Controls_Manager::TEXT,'default'=>''));
  $this->add_control('items',array('label'=>'İlerlemeler','type'=>\Elementor\Controls_Manager::REPEATER,'fields'=>$r->get_controls(),'default'=>array(array('title'=>'Web Tasarım','value'=>94),array('title'=>'UI / UX','value'=>88),array('title'=>'Performans','value'=>91)),'title_field'=>'{{{ title }}}'));
  $this->add_control('layout',array('label'=>'Yerleşim','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'bar','options'=>array('bar'=>'Bar','thin'=>'Thin','cards'=>'Cards','minimal'=>'Minimal'),'prefix_class'=>'wpst-progress-layout-'));
  $this->add_control('show_value',array('label'=>'Yüzdeyi Göster','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes'));
  $this->end_controls_section();
  $this->start_controls_section('progress_style',array('label'=>'Progress Biçimi','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('track',array('label'=>'Track Rengi','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#e8edf3','selectors'=>array('{{WRAPPER}} .wpst-progress-row>i'=>'background:{{VALUE}};')));
  $this->add_control('fill',array('label'=>'Dolgu Rengi','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#315cf5','selectors'=>array('{{WRAPPER}} .wpst-progress-row>i>b'=>'background:{{VALUE}};')));
  $this->add_responsive_control('bar_height',array('label'=>'Bar Yüksekliği','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>2,'max'=>30)),'default'=>array('size'=>8),'selectors'=>array('{{WRAPPER}} .wpst-progress-row>i'=>'height:{{SIZE}}px;')));
  $this->add_responsive_control('row_gap',array('label'=>'Satır Aralığı','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>4,'max'=>50)),'default'=>array('size'=>18),'selectors'=>array('{{WRAPPER}} .wpst-progress-pro'=>'gap:{{SIZE}}px;')));
  $this->end_controls_section();
  $this->standard_responsive_controls();
 }
 protected function render(){$s=$this->get_settings_for_display();echo'<div class="wpst-progress-pro">';foreach((array)$s['items'] as $it){$v=max(0,min(100,(int)$it['value']));echo'<div class="wpst-progress-row"><div><strong>'.esc_html($it['title']).'</strong>';if('yes'===$s['show_value'])echo'<span>'.$v.'%</span>';echo'</div>';if(!empty($it['note']))echo'<small>'.esc_html($it['note']).'</small>';echo'<i aria-hidden="true"><b style="width:'.$v.'%"></b></i></div>';}echo'</div>';}
}