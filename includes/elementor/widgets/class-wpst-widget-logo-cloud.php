<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class WPST_Widget_Logo_Cloud extends WPST_Elementor_Widget_Base {
 public function get_name(){return 'wpsoft-logo-cloud';} public function get_title(){return 'WPSoft Logo Cloud 2.0';} public function get_icon(){return 'eicon-logo';}
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'Logolar'));
  $this->wpst_signature_preset_control();
  $this->add_control('title',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Bize güvenen markalar'));
  $this->add_control('gallery',array('label'=>'Logo Görselleri','type'=>\Elementor\Controls_Manager::GALLERY));
  $this->add_control('text_logos',array('label'=>'Metin Logoları','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'NOVA\nARC\nPIXEL\nATLAS\nNEXA\nFORM','description'=>'Logo görseli seçilmezse her satır bir marka adı olarak gösterilir.'));
  $this->add_control('layout_variant',array('label'=>'Logo Yerleşimi','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'cloud','options'=>array('cloud'=>'Cloud','strip'=>'Strip','boxed'=>'Boxed','editorial'=>'Editorial'),'prefix_class'=>'wpst-logo-cloud-layout-'));
  $this->add_responsive_control('columns',array('label'=>'Kolon','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'6','tablet_default'=>'3','mobile_default'=>'2','options'=>array('2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6'),'selectors'=>array('{{WRAPPER}} .wpst-ew-logo-cloud>div'=>'grid-template-columns:repeat({{VALUE}},minmax(0,1fr))!important;')));
  $this->add_control('grayscale',array('label'=>'Gri Ton','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes','prefix_class'=>'wpst-logo-cloud-gray-'));
  $this->end_controls_section();
  $this->start_controls_section('style',array('label'=>'Logo Biçimi','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('title_color',array('label'=>'Başlık Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-logo-cloud>p'=>'color:{{VALUE}};')));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'title_typography','label'=>'Başlık Tipografisi','selector'=>'{{WRAPPER}} .wpst-ew-logo-cloud>p'));
  $this->add_responsive_control('logo_height',array('label'=>'Logo Yüksekliği','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>18,'max'=>120)),'default'=>array('size'=>40),'selectors'=>array('{{WRAPPER}} .wpst-ew-logo-cloud img'=>'max-height:{{SIZE}}px;width:auto;')));
  $this->add_responsive_control('cell_padding',array('label'=>'Hücre İç Boşluk','type'=>\Elementor\Controls_Manager::DIMENSIONS,'size_units'=>array('px'),'selectors'=>array('{{WRAPPER}} .wpst-ew-logo-cloud figure'=>'padding:{{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;')));
  $this->add_responsive_control('gap',array('label'=>'Aralık','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>70)),'selectors'=>array('{{WRAPPER}} .wpst-ew-logo-cloud>div'=>'gap:{{SIZE}}px;')));
  $this->add_control('cell_bg',array('label'=>'Hücre Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-logo-cloud figure'=>'background:{{VALUE}};')));
  $this->add_control('cell_border',array('label'=>'Hücre Kenarlık','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-logo-cloud figure'=>'border-color:{{VALUE}};border-style:solid;border-width:1px;')));
  $this->add_control('logo_opacity',array('label'=>'Logo Opaklığı','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>10,'max'=>100)),'default'=>array('size'=>72),'selectors'=>array('{{WRAPPER}} .wpst-ew-logo-cloud figure'=>'opacity:calc({{SIZE}} / 100);')));
  $this->add_control('hover_full',array('label'=>'Hover’da Tam Görünüm','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes','prefix_class'=>'wpst-logo-hover-full-'));
  $this->add_responsive_control('cell_radius',array('label'=>'Hücre Köşe','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>50)),'selectors'=>array('{{WRAPPER}} .wpst-ew-logo-cloud figure'=>'border-radius:{{SIZE}}px;')));
  $this->end_controls_section();
  $this->standard_responsive_controls();
 }
 protected function render(){ $s=$this->get_settings_for_display(); echo '<section class="wpst-ew-logo-cloud"><p>'.esc_html($s['title']).'</p><div>'; if(!empty($s['gallery'])){ foreach($s['gallery'] as $img){ echo '<figure><img src="'.esc_url($img['url']).'" alt="" loading="lazy"></figure>'; } } else { foreach(preg_split('/\r\n|\r|\n/',(string)$s['text_logos']) as $x){ if(trim($x)!=='') echo '<figure><span>'.esc_html(trim($x)).'</span></figure>'; } } echo '</div></section>'; }
}