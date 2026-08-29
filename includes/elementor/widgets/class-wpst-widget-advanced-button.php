<?php
if(!defined('ABSPATH'))exit;
class WPST_Widget_Advanced_Button extends WPST_Elementor_Widget_Base{
 public function get_name(){return'wpsoft-advanced-button';}
 public function get_title(){return'WPSoft · Advanced Button 3.0';}
 public function get_icon(){return'eicon-button';}
 public function get_keywords(){return array('button','magnetic','icon','cta','wpsoft','primary','outline','gradient','glass');}
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'İçerik'));
  $this->add_control('text',array('label'=>'Buton Metni','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Projeyi Başlat','dynamic'=>array('active'=>true)));
  $this->add_control('url',array('label'=>'Bağlantı','type'=>\Elementor\Controls_Manager::URL,'default'=>array('url'=>'#'),'dynamic'=>array('active'=>true),'placeholder'=>'https://...','show_external'=>true));
  $this->add_control('button_type',array(
   'label'=>'Hazır Buton Türü',
   'type'=>\Elementor\Controls_Manager::SELECT,
   'default'=>'custom',
   'options'=>array(
    'custom'=>'Özel / Mevcut Ayarlar',
    'primary'=>'Primary · Marka Rengi',
    'secondary'=>'Violet · Mor',
    'emerald'=>'Emerald · Yeşil',
    'sunset'=>'Sunset · Turuncu',
    'dark'=>'Dark · Koyu',
    'light'=>'Light · Açık',
    'outline'=>'Outline · Modern',
    'soft'=>'Soft · Yumuşak',
    'gradient'=>'Gradient · Premium',
    'glass'=>'Glass · Cam',
   ),
   'prefix_class'=>'wpst-button-type-',
   'description'=>'Hazır tür seçildiğinde arka plan, yazı, border, hover ve gölge birlikte uygulanır. Elementor Lokal Stil renkleri girilirse seçilen hazır türün ilgili rengini yalnız bu butonda override eder.'
  ));
  $this->add_control('wpst_icon',array('label'=>'WPSoft Icon','type'=>\Elementor\Controls_Manager::SELECT2,'options'=>class_exists('WPST_Icon_Library')?WPST_Icon_Library::options():array(),'default'=>'arrow-up-right','label_block'=>true));
  $this->add_control('icon',array('label'=>'Eski İkon / İşaret','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'↗'));
  $this->add_control('icon_position',array('label'=>'İkon Konumu','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'right','options'=>array('left'=>'Sol','right'=>'Sağ')));
  $this->add_control('size_preset',array('label'=>'Boyut','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'md','options'=>array('sm'=>'Small','md'=>'Medium','lg'=>'Large'),'prefix_class'=>'wpst-button-size-'));
  $this->add_control('style_preset',array('label'=>'Yapı Stili','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'solid','options'=>array('solid'=>'Solid','outline'=>'Outline','soft'=>'Soft','ghost'=>'Ghost','gradient'=>'Gradient','glass'=>'Glass'),'prefix_class'=>'wpst-button-style-','description'=>'Hazır Buton Türü = Özel olduğunda buton kabuğunu belirler.'));
  $this->add_control('effect',array('label'=>'Hover Efekti','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'lift','options'=>array('none'=>'Yok','slide'=>'Slide Fill','lift'=>'Lift','glow'=>'Glow','magnetic'=>'Magnetic')));
  $this->add_control('full_width',array('label'=>'Tam Genişlik','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','prefix_class'=>'wpst-button-full-'));
  $this->add_responsive_control('align',array('label'=>'Hizalama','type'=>\Elementor\Controls_Manager::CHOOSE,'options'=>array('left'=>array('title'=>'Sol','icon'=>'eicon-text-align-left'),'center'=>array('title'=>'Orta','icon'=>'eicon-text-align-center'),'right'=>array('title'=>'Sağ','icon'=>'eicon-text-align-right')),'default'=>'left','selectors'=>array('{{WRAPPER}} .elementor-widget-container'=>'text-align:{{VALUE}};')));
  $this->add_control('aria_label',array('label'=>'Erişilebilirlik Etiketi','type'=>\Elementor\Controls_Manager::TEXT,'placeholder'=>'Boşsa buton metni kullanılır','separator'=>'before'));
  $this->end_controls_section();

  $this->start_controls_section('style_button',array('label'=>'Buton','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('preset_note',array('type'=>\Elementor\Controls_Manager::RAW_HTML,'raw'=>'<strong>Renk kaynağı:</strong> Önce Global Design > BUTTONS kullanılır. Aşağıdaki Lokal Stil renklerinden birini girerseniz yalnız bu widget için global değer override edilir.','content_classes'=>'elementor-panel-alert elementor-panel-alert-info','condition'=>array('button_type!'=>'custom')));
  $this->start_controls_tabs('button_color_tabs');
  $this->start_controls_tab('button_normal_tab',array('label'=>'Normal'));
  $this->add_control('bg',array('label'=>'Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'','selectors'=>array('{{WRAPPER}} .wpst-adv-button'=>'--ab-bg:{{VALUE}};background-color:{{VALUE}};','{{WRAPPER}}'=>'--wpst-local-button-bg:{{VALUE}};')));
  $this->add_control('color',array('label'=>'Yazı Rengi','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'','selectors'=>array('{{WRAPPER}} .wpst-adv-button'=>'--ab-color:{{VALUE}};color:{{VALUE}}!important;','{{WRAPPER}}'=>'--wpst-local-button-color:{{VALUE}};')));
  $this->add_control('border_color',array('label'=>'Border Rengi','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'','selectors'=>array('{{WRAPPER}} .wpst-adv-button'=>'--ab-border:{{VALUE}};border-color:{{VALUE}};')));
  $this->add_control('icon_color',array('label'=>'İkon Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-adv-button'=>'--ab-icon-color:{{VALUE}};')));
  $this->end_controls_tab();
  $this->start_controls_tab('button_hover_tab',array('label'=>'Hover'));
  $this->add_control('hover_bg',array('label'=>'Hover Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'','selectors'=>array('{{WRAPPER}} .wpst-adv-button'=>'--ab-hover:{{VALUE}};','{{WRAPPER}}'=>'--wpst-local-button-hover-bg:{{VALUE}};')));
  $this->add_control('hover_color',array('label'=>'Hover Yazı','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'','selectors'=>array('{{WRAPPER}} .wpst-adv-button'=>'--ab-hover-color:{{VALUE}};','{{WRAPPER}} .wpst-adv-button:hover'=>'color:{{VALUE}}!important;','{{WRAPPER}}'=>'--wpst-local-button-hover-color:{{VALUE}};')));
  $this->add_control('hover_border_color',array('label'=>'Hover Border','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-adv-button'=>'--ab-hover-border:{{VALUE}};')));
  $this->add_control('hover_icon_color',array('label'=>'Hover İkon','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-adv-button'=>'--ab-hover-icon-color:{{VALUE}};')));
  $this->end_controls_tab();
  $this->end_controls_tabs();

  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'button_typography','selector'=>'{{WRAPPER}} .wpst-adv-button','separator'=>'before'));
  $this->add_responsive_control('padding',array('label'=>'İç Boşluk','type'=>\Elementor\Controls_Manager::DIMENSIONS,'size_units'=>array('px','em','rem'),'selectors'=>array('{{WRAPPER}} .wpst-adv-button'=>'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};')));
  $this->add_responsive_control('radius',array('label'=>'Köşe','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>60)),'default'=>array('size'=>14),'selectors'=>array('{{WRAPPER}} .wpst-adv-button'=>'border-radius:{{SIZE}}px;')));
  $this->add_responsive_control('border_width',array('label'=>'Border Kalınlığı','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>8)),'default'=>array('size'=>1),'selectors'=>array('{{WRAPPER}} .wpst-adv-button'=>'border-width:{{SIZE}}px;')));
  $this->add_responsive_control('icon_size',array('label'=>'Icon Boyutu','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>10,'max'=>40)),'default'=>array('size'=>16),'selectors'=>array('{{WRAPPER}} .wpst-adv-button-icon svg'=>'width:{{SIZE}}px;height:{{SIZE}}px;','{{WRAPPER}} .wpst-adv-button-icon'=>'font-size:{{SIZE}}px;')));
  $this->add_responsive_control('icon_gap',array('label'=>'Icon Aralığı','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>30)),'default'=>array('size'=>8),'selectors'=>array('{{WRAPPER}} .wpst-adv-button'=>'gap:{{SIZE}}px;')));
  $this->add_group_control(\Elementor\Group_Control_Box_Shadow::get_type(),array('name'=>'button_shadow','label'=>'Normal Gölge','selector'=>'{{WRAPPER}} .wpst-adv-button'));
  $this->add_group_control(\Elementor\Group_Control_Box_Shadow::get_type(),array('name'=>'button_hover_shadow','label'=>'Hover Gölge','selector'=>'{{WRAPPER}} .wpst-adv-button:hover'));
  $this->end_controls_section();
  $this->standard_responsive_controls();
 }
 protected function render(){
  $s=$this->get_settings_for_display();
  $link=!empty($s['url'])&&is_array($s['url'])?$s['url']:array();
  if(empty($link['url']))$link['url']='#';
  $i='<span class="wpst-adv-button-icon" aria-hidden="true">';
  if(!empty($s['wpst_icon'])&&class_exists('WPST_Icon_Library'))$i.=WPST_Icon_Library::svg($s['wpst_icon'],array('size'=>16));else $i.=esc_html($s['icon']);
  $i.='</span>';
  $this->add_render_attribute('button','class',array('wpst-adv-button','effect-'.sanitize_html_class($s['effect']),'icon-'.sanitize_html_class($s['icon_position'])));
  $this->add_render_attribute('button','data-effect',esc_attr($s['effect']));
  $this->add_link_attributes('button',$link);
  $aria=!empty($s['aria_label'])?$s['aria_label']:$s['text'];
  if($aria!=='')$this->add_render_attribute('button','aria-label',esc_attr(wp_strip_all_tags($aria)));
  echo'<a '.$this->get_render_attribute_string('button').'>'.($s['icon_position']==='left'?$i:'').'<span class="wpst-adv-button-label">'.esc_html($s['text']).'</span>'.($s['icon_position']==='right'?$i:'').'</a>';
 }
}
