<?php
if(!defined('ABSPATH'))exit;
class WPST_Widget_Glass_Card extends WPST_Elementor_Widget_Base{
 public function get_name(){return'wpsoft-glass-card';}
 public function get_title(){return'WPSoft · Glass Card 2.0';}
 public function get_icon(){return'eicon-call-to-action';}
 public function get_keywords(){return array('glass','card','blur','cta','modern','wpsoft');}
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'İçerik'));
  $this->wpst_signature_preset_control();
  $this->add_control('eyebrow',array('label'=>'Etiket','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'PREMIUM','dynamic'=>array('active'=>true)));
  $this->add_control('title',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Modern glass deneyimi','dynamic'=>array('active'=>true)));
  $this->add_control('text',array('label'=>'Açıklama','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'Blur, transparan yüzey ve güçlü tipografi ile modern içerik kartı.','dynamic'=>array('active'=>true)));
  $this->add_control('icon',array('label'=>'Eski İkon / İşaret','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'✦','description'=>'Eski kartlarla uyumluluk için korunur. WPSoft Icon seçilirse bu alan kullanılmaz.'));
  $this->add_control('wpst_icon',array('label'=>'WPSoft Icon','type'=>\Elementor\Controls_Manager::SELECT2,'options'=>class_exists('WPST_Icon_Library')?array(''=>'Eski işareti kullan')+WPST_Icon_Library::options():array(),'default'=>''));
  $this->link_controls('button','Buton');
  $this->add_control('align',array('label'=>'İçerik Hizası','type'=>\Elementor\Controls_Manager::CHOOSE,'options'=>array('left'=>array('title'=>'Sol','icon'=>'eicon-text-align-left'),'center'=>array('title'=>'Orta','icon'=>'eicon-text-align-center'),'right'=>array('title'=>'Sağ','icon'=>'eicon-text-align-right')),'default'=>'left','prefix_class'=>'wpst-glass-align-'));
  $this->end_controls_section();

  $this->start_controls_section('glass',array('label'=>'Glass Biçimi','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('surface',array('label'=>'Yüzey','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-glass-card'=>'--wpst-glass-surface:{{VALUE}};')));
  $this->add_responsive_control('blur',array('label'=>'Blur','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>50)),'default'=>array('size'=>18),'selectors'=>array('{{WRAPPER}} .wpst-glass-card'=>'--wpst-glass-blur:{{SIZE}}px;')));
  $this->add_control('border',array('label'=>'Kenarlık','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-glass-card'=>'--wpst-glass-border:{{VALUE}};')));
  $this->add_responsive_control('radius',array('label'=>'Kart Radius','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>60)),'default'=>array('size'=>26),'selectors'=>array('{{WRAPPER}} .wpst-glass-card'=>'--wpst-glass-radius:{{SIZE}}px;')));
  $this->add_responsive_control('padding',array('label'=>'İç Boşluk','type'=>\Elementor\Controls_Manager::DIMENSIONS,'size_units'=>array('px'),'default'=>array('top'=>32,'right'=>32,'bottom'=>32,'left'=>32,'unit'=>'px'),'selectors'=>array('{{WRAPPER}} .wpst-glass-card'=>'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};')));
  $this->add_responsive_control('min_height',array('label'=>'Minimum Yükseklik','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>180,'max'=>700)),'selectors'=>array('{{WRAPPER}} .wpst-glass-card'=>'min-height:{{SIZE}}px;')));
  $this->add_control('shadow',array('label'=>'Gölge','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'soft','options'=>array('none'=>'Yok','soft'=>'Soft','medium'=>'Medium','strong'=>'Strong'),'prefix_class'=>'wpst-glass-shadow-'));
  $this->end_controls_section();

  $this->start_controls_section('content_style',array('label'=>'İçerik Biçimi','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('eyebrow_color',array('label'=>'Etiket Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-glass-card'=>'--wpst-glass-eyebrow:{{VALUE}};')));
  $this->add_control('title_color',array('label'=>'Başlık Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-glass-card'=>'--wpst-glass-title:{{VALUE}};')));
  $this->add_control('text_color',array('label'=>'Metin Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-glass-card'=>'--wpst-glass-text:{{VALUE}};')));
  $this->add_control('icon_color',array('label'=>'İkon Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-glass-card'=>'--wpst-glass-icon:{{VALUE}};')));
  $this->add_control('icon_bg',array('label'=>'İkon Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-glass-card'=>'--wpst-glass-icon-bg:{{VALUE}};')));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'title_typography','label'=>'Başlık Tipografi','selector'=>'{{WRAPPER}} .wpst-glass-card h3'));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'text_typography','label'=>'Metin Tipografi','selector'=>'{{WRAPPER}} .wpst-glass-card p'));
  $this->end_controls_section();
  $this->standard_responsive_controls();
 }
 protected function render(){
  $s=$this->get_settings_for_display();
  $link=is_array($s['button_url']??null)?$s['button_url']:array('url'=>'#');
  $u=!empty($link['url'])?esc_url($link['url']):'#';
  $target=!empty($link['is_external'])?' target="_blank"':'';
  $rels=array();if(!empty($link['nofollow']))$rels[]='nofollow';if(!empty($link['is_external']))$rels[]='noopener';
  $rel=$rels?' rel="'.esc_attr(implode(' ',$rels)).'"':'';
  echo'<article class="wpst-glass-card"><span class="wpst-glass-icon">';
  if(!empty($s['wpst_icon'])&&class_exists('WPST_Icon_Library')) echo WPST_Icon_Library::svg($s['wpst_icon'],array('size'=>22));
  else echo esc_html($s['icon']);
  echo'</span><small>'.esc_html($s['eyebrow']).'</small><h3>'.esc_html($s['title']).'</h3><p>'.esc_html($s['text']).'</p>';
  if(!empty($s['button_text'])) echo'<a class="wpst-ew-button" href="'.$u.'"'.$target.$rel.'>'.esc_html($s['button_text']).' <b class="wpst-native-arrow" aria-hidden="true">'.(class_exists('WPST_Icon_Library')?WPST_Icon_Library::svg('arrow-up-right',array('size'=>14)):'↗').'</b></a>';
  echo'</article>';
 }
}
