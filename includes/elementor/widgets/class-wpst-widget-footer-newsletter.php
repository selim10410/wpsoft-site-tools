<?php
if(!defined('ABSPATH'))exit;
class WPST_Widget_Footer_Newsletter extends WPST_Elementor_Widget_Base{
 public function get_name(){return'wpsoft-footer-newsletter';}
 public function get_title(){return'WPSoft Footer · Newsletter 2.0';}
 public function get_icon(){return'eicon-mail';}
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'Newsletter'));
  $this->add_control('eyebrow',array('label'=>'Etiket','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'BÜLTEN'));
  $this->add_control('title',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Yeniliklerden haberdar olun'));
  $this->add_control('text',array('label'=>'Açıklama','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'Yeni projeler, içerikler ve güncellemeler için e-posta listenize katılın.'));
  $this->add_control('placeholder',array('label'=>'Placeholder','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'E-posta adresiniz'));
  $this->add_control('button',array('label'=>'Buton','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Katıl'));
  $this->add_control('note',array('label'=>'Alt Not','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Spam yok. İstediğiniz zaman ayrılabilirsiniz.'));
  $this->add_control('show_eyebrow',array('label'=>'Etiketi Göster','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes'));
  $this->add_control('show_text',array('label'=>'Açıklamayı Göster','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes'));
  $this->add_control('show_note',array('label'=>'Alt Notu Göster','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes'));
  $this->add_control('button_icon',array('label'=>'Buton Icon','type'=>\Elementor\Controls_Manager::SELECT2,'options'=>class_exists('WPST_Icon_Library')?WPST_Icon_Library::options():array(),'default'=>'arrow-right','label_block'=>true));
  $this->add_control('style_preset',array('label'=>'Stil','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'clean','options'=>array('clean'=>'Clean','soft'=>'Soft','inline'=>'Inline','dark'=>'Dark'),'prefix_class'=>'wpst-footer-newsletter-style-'));
  $this->end_controls_section();

  $this->start_controls_section('style',array('label'=>'Biçim','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('accent',array('label'=>'Vurgu','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#2563eb','selectors'=>array('{{WRAPPER}} .wpst-ew-footer-newsletter'=>'--newsletter-accent:{{VALUE}};')));
  $this->add_responsive_control('form_direction',array('label'=>'Form Düzeni','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'row','tablet_default'=>'row','mobile_default'=>'column','options'=>array('row'=>'Yan Yana','column'=>'Alt Alta'),'selectors'=>array('{{WRAPPER}} .wpst-ew-footer-newsletter form'=>'flex-direction:{{VALUE}};')));
  $this->add_responsive_control('form_gap',array('label'=>'Form Aralığı','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>30)),'selectors'=>array('{{WRAPPER}} .wpst-ew-footer-newsletter form'=>'gap:{{SIZE}}px;')));
  $this->add_responsive_control('button_width',array('label'=>'Buton Genişliği','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'auto','tablet_default'=>'auto','mobile_default'=>'100%','options'=>array('auto'=>'Otomatik','100%'=>'Tam Genişlik'),'selectors'=>array('{{WRAPPER}} .wpst-ew-footer-newsletter form>button'=>'width:{{VALUE}};')));
  $this->add_responsive_control('field_min_height',array('label'=>'Alan Yüksekliği','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>42,'max'=>72)),'selectors'=>array('{{WRAPPER}} .wpst-footer-newsletter-field','{{WRAPPER}} .wpst-ew-footer-newsletter form>button'=>'min-height:{{SIZE}}px;')));
  $this->add_responsive_control('content_max_width',array('label'=>'İçerik Maks. Genişliği','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>280,'max'=>900)),'selectors'=>array('{{WRAPPER}} .wpst-ew-footer-newsletter'=>'max-width:{{SIZE}}px;')));

  $this->add_control('title_color',array('label'=>'Başlık Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-footer-newsletter h3'=>'color:{{VALUE}}!important;')));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'footer_newsletter_title_typography','selector'=>'{{WRAPPER}} .wpst-ew-footer-newsletter h3'));
  $this->add_control('text_color',array('label'=>'Açıklama Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-footer-newsletter>p'=>'color:{{VALUE}}!important;opacity:1;')));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'footer_newsletter_text_typography','selector'=>'{{WRAPPER}} .wpst-ew-footer-newsletter>p'));
  $this->add_control('eyebrow_color',array('label'=>'Etiket Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-footer-newsletter>small'=>'color:{{VALUE}}!important;')));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'footer_newsletter_eyebrow_typography','selector'=>'{{WRAPPER}} .wpst-ew-footer-newsletter>small'));
  $this->add_control('note_color',array('label'=>'Alt Not Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-footer-newsletter>em'=>'color:{{VALUE}}!important;opacity:1;')));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'footer_newsletter_note_typography','selector'=>'{{WRAPPER}} .wpst-ew-footer-newsletter>em'));

  $this->add_control('field_bg',array('label'=>'Alan Arka Planı','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-footer-newsletter'=>'--wpst-newsletter-field-bg:{{VALUE}};')));
  $this->add_control('field_border',array('label'=>'Alan Border Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-footer-newsletter'=>'--wpst-newsletter-field-border:{{VALUE}};')));
  $this->add_control('field_text',array('label'=>'Alan Yazı Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-footer-newsletter'=>'--wpst-newsletter-field-color:{{VALUE}};')));
  $this->add_control('button_bg',array('label'=>'Buton Arka Planı','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-footer-newsletter'=>'--wpst-newsletter-button-bg:{{VALUE}};')));
  $this->add_control('button_color',array('label'=>'Buton Yazı Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-footer-newsletter'=>'--wpst-newsletter-button-color:{{VALUE}};')));
  $this->add_control('button_hover_bg',array('label'=>'Buton Hover Arka Planı','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-footer-newsletter'=>'--wpst-newsletter-button-hover-bg:{{VALUE}};')));
  $this->add_control('button_hover_color',array('label'=>'Buton Hover Yazı Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-footer-newsletter'=>'--wpst-newsletter-button-hover-color:{{VALUE}};')));
  $this->add_responsive_control('field_radius',array('label'=>'Form Köşesi','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>40)),'selectors'=>array('{{WRAPPER}} .wpst-ew-footer-newsletter'=>'--wpst-newsletter-radius:{{SIZE}}px;')));

  $this->end_controls_section();
  $this->standard_responsive_controls();
 }
 protected function render(){
  $s=$this->get_settings_for_display();
  echo'<div class="wpst-ew-footer-newsletter">';
  if('yes'===($s['show_eyebrow']??'yes') && ''!==trim((string)($s['eyebrow']??'')))echo'<small>'.esc_html($s['eyebrow']).'</small>';
  if(''!==trim((string)($s['title']??'')))echo'<h3>'.esc_html($s['title']).'</h3>';
  if('yes'===($s['show_text']??'yes') && ''!==trim((string)($s['text']??'')))echo'<p>'.esc_html($s['text']).'</p>';
  echo'<form onsubmit="return false" aria-label="Newsletter formu"><div class="wpst-footer-newsletter-field"><i>'.(class_exists('WPST_Icon_Library')?WPST_Icon_Library::svg('mail',array('size'=>14)):'✉').'</i><input type="email" aria-label="E-posta" placeholder="'.esc_attr($s['placeholder']).'"></div><button type="submit"><span>'.esc_html($s['button']).'</span><i>'.(class_exists('WPST_Icon_Library')?WPST_Icon_Library::svg($s['button_icon'],array('size'=>14)):'→').'</i></button></form>';
  if('yes'===($s['show_note']??'yes') && ''!==trim((string)($s['note']??'')))echo'<em>'.esc_html($s['note']).'</em>';
  echo'</div>';
 }
}
