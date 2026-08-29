<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class WPST_Widget_Expert_Profile extends WPST_Elementor_Widget_Base {
 public function get_name(){ return 'wpsoft-expert-profile'; }
 public function get_title(){ return 'WPSoft · Expert Profile 2.0'; }
 public function get_icon(){ return 'eicon-person'; }
 public function get_keywords(){ return array('expert','team','profile','doctor','consultant','person','wpsoft'); }
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'Uzman'));
  $this->wpst_signature_preset_control();
  $this->add_control('image',array('label'=>'Fotoğraf','type'=>\Elementor\Controls_Manager::MEDIA));
  $this->add_control('name',array('label'=>'Ad Soyad','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Dr. Deniz Arslan','dynamic'=>array('active'=>true)));
  $this->add_control('role',array('label'=>'Ünvan','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Uzman Danışman','dynamic'=>array('active'=>true)));
  $this->add_control('bio',array('label'=>'Biyografi','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'Uzmanlık, deneyim ve yaklaşımı kısa ve güven veren bir anlatımla öne çıkarın.','dynamic'=>array('active'=>true)));
  $this->add_control('meta',array('label'=>'Kısa Bilgiler','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>"15+ Yıl Deneyim\n120+ Proje\nİstanbul"));
  $this->link_controls('button','Profil Butonu');
  $this->add_control('layout',array('label'=>'Yerleşim','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'split','options'=>array('split'=>'Split','card'=>'Card','editorial'=>'Editorial','compact'=>'Compact'),'prefix_class'=>'wpst-expert-layout-'));
  $this->end_controls_section();

  $this->start_controls_section('quality_style',array('label'=>'Profil Kartı','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('surface_bg',array('label'=>'Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-expert-profile'=>'--wpst-expert-surface:{{VALUE}};')));
  $this->add_control('border',array('label'=>'Kenarlık','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-expert-profile'=>'--wpst-expert-border:{{VALUE}};')));
  $this->add_responsive_control('quality_gap',array('label'=>'Aralık','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>60)),'default'=>array('size'=>28),'selectors'=>array('{{WRAPPER}} .wpst-expert-profile'=>'--wpst-expert-gap:{{SIZE}}px;')));
  $this->add_responsive_control('quality_radius',array('label'=>'Köşe','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>60)),'default'=>array('size'=>24),'selectors'=>array('{{WRAPPER}} .wpst-expert-profile'=>'--wpst-expert-radius:{{SIZE}}px;')));
  $this->add_responsive_control('quality_padding',array('label'=>'İç Boşluk','type'=>\Elementor\Controls_Manager::DIMENSIONS,'size_units'=>array('px'),'selectors'=>array('{{WRAPPER}} .wpst-expert-profile'=>'padding:{{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;')));
  $this->add_control('image_ratio',array('label'=>'Fotoğraf Oranı','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'portrait','options'=>array('portrait'=>'Portre','square'=>'Kare','wide'=>'Yatay'),'prefix_class'=>'wpst-expert-image-'));
  $this->end_controls_section();

  $this->start_controls_section('content_style',array('label'=>'İçerik Biçimi','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('role_color',array('label'=>'Ünvan Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-expert-profile'=>'--wpst-expert-role:{{VALUE}};')));
  $this->add_control('name_color',array('label'=>'İsim Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-expert-profile'=>'--wpst-expert-name:{{VALUE}};')));
  $this->add_control('bio_color',array('label'=>'Biyografi Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-expert-profile'=>'--wpst-expert-bio:{{VALUE}};')));
  $this->add_control('meta_bg',array('label'=>'Bilgi Etiketi Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-expert-profile'=>'--wpst-expert-meta-bg:{{VALUE}};')));
  $this->add_control('meta_color',array('label'=>'Bilgi Etiketi Yazı','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-expert-profile'=>'--wpst-expert-meta:{{VALUE}};')));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'name_typography','label'=>'İsim Tipografi','selector'=>'{{WRAPPER}} .wpst-expert-copy h3'));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'bio_typography','label'=>'Biyografi Tipografi','selector'=>'{{WRAPPER}} .wpst-expert-copy p'));
  $this->end_controls_section();
  $this->standard_responsive_controls();
 }
 protected function render(){
  $s=$this->get_settings_for_display();
  echo '<article class="wpst-expert-profile">';
  echo '<div class="wpst-expert-media">';
  if(!empty($s['image']['url'])) echo '<img src="'.esc_url($s['image']['url']).'" alt="'.esc_attr($s['name']).'" loading="lazy" decoding="async">';
  else echo '<div class="wpst-expert-placeholder">'.(class_exists('WPST_Icon_Library')?WPST_Icon_Library::svg('user',array('size'=>42)):'').'</div>';
  echo '</div><div class="wpst-expert-copy"><span>'.esc_html($s['role']).'</span><h3>'.esc_html($s['name']).'</h3><p>'.esc_html($s['bio']).'</p><ul>';
  foreach(preg_split('/\r\n|\r|\n/',(string)$s['meta']) as $m){ if(trim($m)!=='') echo '<li>'.esc_html(trim($m)).'</li>'; }
  if(!empty($s['button_text'])) echo '</ul><a class="wpst-ew-button"'.$this->render_link_attrs($s['button_url']).'>'.esc_html($s['button_text']).'</a>';
  else echo '</ul>';
  echo '</div></article>';
 }
}
