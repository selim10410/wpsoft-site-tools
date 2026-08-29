<?php
if(!defined('ABSPATH'))exit;
class WPST_Widget_Footer_Brand extends WPST_Elementor_Widget_Base{
 public function get_name(){return'wpsoft-footer-brand';}
 public function get_title(){return'WPSoft Footer · Brand 2.0';}
 public function get_icon(){return'eicon-site-logo';}
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'Marka'));
  $this->add_control('logo',array('label'=>'Logo','type'=>\Elementor\Controls_Manager::MEDIA));
  $this->add_control('brand',array('label'=>'Marka Adı','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'WPSoft'));
  $this->add_control('text',array('label'=>'Açıklama','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'Modern web tasarım, e-ticaret ve dijital çözüm ortağınız.'));
  $this->add_control('phone',array('label'=>'Telefon 1','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'+90 212 000 00 00'));
  $this->add_control('phone_label',array('label'=>'Telefon 1 Etiketi','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Telefon'));
  $this->add_control('phone_2',array('label'=>'Telefon 2','type'=>\Elementor\Controls_Manager::TEXT,'default'=>''));
  $this->add_control('phone_2_label',array('label'=>'Telefon 2 Etiketi','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Mobil','condition'=>array('show_phone_2'=>'yes')));
  $this->add_control('show_phone_2',array(
   'label'=>'İkinci Telefonu Göster',
   'type'=>\Elementor\Controls_Manager::SWITCHER,
   'return_value'=>'yes',
   'default'=>''
  ));
  $this->add_control('email',array('label'=>'E-posta','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'info@example.com'));
  $this->add_control('email_label',array('label'=>'E-posta Etiketi','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'E-posta'));
  $this->add_control('show_contact_labels',array(
   'label'=>'İletişim Etiketlerini Göster',
   'type'=>\Elementor\Controls_Manager::SWITCHER,
   'return_value'=>'yes',
   'default'=>'yes',
   'prefix_class'=>'wpst-footer-brand-labels-'
  ));

  $this->add_control('style_preset',array(
   'label'=>'Stil',
   'type'=>\Elementor\Controls_Manager::SELECT,
   'default'=>'modern',
   'options'=>array(
    'modern'=>'Modern Signature',
    'contact-cards'=>'Contact Cards',
    'minimal-pro'=>'Minimal Pro',
    'glass'=>'Glass',
    'clean'=>'Clean',
    'compact'=>'Compact',
    'soft'=>'Soft',
    'dark'=>'Dark'
   ),
   'prefix_class'=>'wpst-footer-brand-style-'
  ));
  $this->add_control('show_description',array(
   'label'=>'Açıklamayı Göster',
   'type'=>\Elementor\Controls_Manager::SWITCHER,
   'return_value'=>'yes',
   'default'=>'yes'
  ));
  $this->add_control('show_phone',array(
   'label'=>'Telefonu Göster',
   'type'=>\Elementor\Controls_Manager::SWITCHER,
   'return_value'=>'yes',
   'default'=>'yes'
  ));
  $this->add_control('show_email',array(
   'label'=>'E-postayı Göster',
   'type'=>\Elementor\Controls_Manager::SWITCHER,
   'return_value'=>'yes',
   'default'=>'yes'
  ));
  $this->add_control('show_contact_icons',array(
   'label'=>'İletişim İkonlarını Göster',
   'type'=>\Elementor\Controls_Manager::SWITCHER,
   'return_value'=>'yes',
   'default'=>'yes',
   'prefix_class'=>'wpst-footer-brand-icons-'
  ));
  $this->add_control('phone_icon',array(
   'label'=>'Telefon 1 İkonu',
   'type'=>\Elementor\Controls_Manager::SELECT2,
   'options'=>class_exists('WPST_Icon_Library')?WPST_Icon_Library::options():array(),
   'default'=>'phone',
   'label_block'=>true,
   'condition'=>array('show_contact_icons'=>'yes')
  ));
  $this->add_control('phone_2_icon',array(
   'label'=>'Telefon 2 İkonu',
   'type'=>\Elementor\Controls_Manager::SELECT2,
   'options'=>class_exists('WPST_Icon_Library')?WPST_Icon_Library::options():array(),
   'default'=>'smartphone',
   'label_block'=>true,
   'condition'=>array('show_contact_icons'=>'yes','show_phone_2'=>'yes')
  ));
  $this->add_control('email_icon',array(
   'label'=>'E-posta İkonu',
   'type'=>\Elementor\Controls_Manager::SELECT2,
   'options'=>class_exists('WPST_Icon_Library')?WPST_Icon_Library::options():array(),
   'default'=>'mail',
   'label_block'=>true,
   'condition'=>array('show_contact_icons'=>'yes')
  ));

  $this->add_responsive_control('contact_direction',array(
   'label'=>'İletişim Düzeni',
   'type'=>\Elementor\Controls_Manager::SELECT,
   'default'=>'column',
   'tablet_default'=>'column',
   'mobile_default'=>'column',
   'options'=>array(
    'column'=>'Dikey',
    'row'=>'Yatay'
   ),
   'selectors'=>array(
    '{{WRAPPER}} .wpst-ew-footer-brand'=>'--wpst-footer-brand-contact-direction:{{VALUE}};'
   )
  ));
  $this->add_responsive_control('align',array(
   'label'=>'Hizalama',
   'type'=>\Elementor\Controls_Manager::CHOOSE,
   'default'=>'left',
   'options'=>array(
    'left'=>array('title'=>'Sol','icon'=>'eicon-text-align-left'),
    'center'=>array('title'=>'Orta','icon'=>'eicon-text-align-center'),
    'right'=>array('title'=>'Sağ','icon'=>'eicon-text-align-right')
   ),
   'selectors'=>array(
    '{{WRAPPER}} .wpst-ew-footer-brand'=>'text-align:{{VALUE}};'
   )
  ));
  $this->end_controls_section();

  $this->start_controls_section('style',array('label'=>'Biçim','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('accent',array('label'=>'Vurgu','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#2563eb','selectors'=>array('{{WRAPPER}} .wpst-ew-footer-brand'=>'--footer-brand-accent:{{VALUE}};')));
  $this->add_responsive_control('logo_width',array('label'=>'Logo Genişliği','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>40,'max'=>260)),'default'=>array('size'=>140),'selectors'=>array('{{WRAPPER}} .wpst-footer-brand-logo'=>'width:{{SIZE}}px;')));
  $this->add_responsive_control('description_width',array(
   'label'=>'Açıklama Maks. Genişliği',
   'type'=>\Elementor\Controls_Manager::SLIDER,
   'range'=>array('px'=>array('min'=>180,'max'=>700)),
   'default'=>array('unit'=>'px','size'=>440),
   'selectors'=>array('{{WRAPPER}} .wpst-ew-footer-brand p'=>'max-width:{{SIZE}}px;')
  ));
  $this->add_responsive_control('contact_gap',array(
   'label'=>'İletişim Aralığı',
   'type'=>\Elementor\Controls_Manager::SLIDER,
   'range'=>array('px'=>array('min'=>0,'max'=>40)),
   'default'=>array('unit'=>'px','size'=>10),
   'selectors'=>array('{{WRAPPER}} .wpst-ew-footer-brand'=>'--wpst-footer-brand-contact-gap:{{SIZE}}px;')
  ));
  $this->add_responsive_control('contact_card_gap',array(
   'label'=>'İkon / Metin Aralığı',
   'type'=>\Elementor\Controls_Manager::SLIDER,
   'range'=>array('px'=>array('min'=>4,'max'=>28)),
   'default'=>array('unit'=>'px','size'=>12),
   'selectors'=>array('{{WRAPPER}} .wpst-ew-footer-brand'=>'--wpst-footer-brand-item-gap:{{SIZE}}px;')
  ));
  $this->add_responsive_control('contact_radius',array(
   'label'=>'İletişim Kart Köşesi',
   'type'=>\Elementor\Controls_Manager::SLIDER,
   'range'=>array('px'=>array('min'=>0,'max'=>36)),
   'default'=>array('unit'=>'px','size'=>14),
   'selectors'=>array('{{WRAPPER}} .wpst-ew-footer-brand'=>'--wpst-footer-brand-contact-radius:{{SIZE}}px;')
  ));
  $this->add_responsive_control('icon_box_size',array(
   'label'=>'İkon Alanı Boyutu',
   'type'=>\Elementor\Controls_Manager::SLIDER,
   'range'=>array('px'=>array('min'=>24,'max'=>64)),
   'default'=>array('unit'=>'px','size'=>38),
   'selectors'=>array(
    '{{WRAPPER}} .wpst-ew-footer-brand'=>'--wpst-footer-brand-icon-box:{{SIZE}}px;'
   ),
   'condition'=>array('show_contact_icons'=>'yes')
  ));
  $this->add_control('contact_color',array(
   'label'=>'İletişim Metin Rengi',
   'type'=>\Elementor\Controls_Manager::COLOR,
   'selectors'=>array('{{WRAPPER}} .wpst-footer-brand-contact'=>'--wpst-footer-brand-contact-color:{{VALUE}}!important;')
  ));
  $this->add_control('contact_hover_color',array(
   'label'=>'İletişim Hover Rengi',
   'type'=>\Elementor\Controls_Manager::COLOR,
   'selectors'=>array('{{WRAPPER}} .wpst-footer-brand-contact'=>'--wpst-footer-brand-contact-hover:{{VALUE}};')
  ));

  $this->add_control('brand_name_color',array(
   'label'=>'Marka Adı Rengi',
   'type'=>\Elementor\Controls_Manager::COLOR,
   'selectors'=>array('{{WRAPPER}} .wpst-footer-brand-name'=>'color:{{VALUE}}!important;')
  ));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array(
   'name'=>'footer_brand_name_typography',
   'selector'=>'{{WRAPPER}} .wpst-footer-brand-name'
  ));

  $this->add_control('description_color',array(
   'label'=>'Açıklama Rengi',
   'type'=>\Elementor\Controls_Manager::COLOR,
   'selectors'=>array('{{WRAPPER}} .wpst-ew-footer-brand p'=>'color:{{VALUE}}!important;opacity:1;')
  ));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array(
   'name'=>'footer_brand_description_typography',
   'selector'=>'{{WRAPPER}} .wpst-ew-footer-brand p'
  ));

  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array(
   'name'=>'footer_brand_contact_typography',
   'selector'=>'{{WRAPPER}} .wpst-footer-contact-text'
  ));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array(
   'name'=>'footer_brand_contact_label_typography',
   'selector'=>'{{WRAPPER}} .wpst-footer-contact-label'
  ));
  $this->add_control('contact_label_color',array(
   'label'=>'İletişim Etiket Rengi',
   'type'=>\Elementor\Controls_Manager::COLOR,
   'selectors'=>array('{{WRAPPER}} .wpst-footer-contact-label'=>'color:{{VALUE}}!important;')
  ));
  $this->add_control('contact_card_bg',array(
   'label'=>'İletişim Kart Arka Planı',
   'type'=>\Elementor\Controls_Manager::COLOR,
   'selectors'=>array('{{WRAPPER}} .wpst-footer-contact-link'=>'--wpst-footer-brand-contact-bg:{{VALUE}};')
  ));
  $this->add_control('contact_card_border',array(
   'label'=>'İletişim Kart Kenarlığı',
   'type'=>\Elementor\Controls_Manager::COLOR,
   'selectors'=>array('{{WRAPPER}} .wpst-footer-contact-link'=>'--wpst-footer-brand-contact-border:{{VALUE}};')
  ));
  $this->add_responsive_control('contact_padding_y',array(
   'label'=>'İletişim Dikey İç Boşluk',
   'type'=>\Elementor\Controls_Manager::SLIDER,
   'range'=>array('px'=>array('min'=>4,'max'=>28)),
   'default'=>array('unit'=>'px','size'=>10),
   'selectors'=>array('{{WRAPPER}} .wpst-ew-footer-brand'=>'--wpst-footer-brand-contact-py:{{SIZE}}px;')
  ));
  $this->add_responsive_control('contact_padding_x',array(
   'label'=>'İletişim Yatay İç Boşluk',
   'type'=>\Elementor\Controls_Manager::SLIDER,
   'range'=>array('px'=>array('min'=>0,'max'=>32)),
   'default'=>array('unit'=>'px','size'=>12),
   'selectors'=>array('{{WRAPPER}} .wpst-ew-footer-brand'=>'--wpst-footer-brand-contact-px:{{SIZE}}px;')
  ));



  $this->add_control('contact_icon_color',array(
   'label'=>'İletişim İkon Rengi',
   'type'=>\Elementor\Controls_Manager::COLOR,
   'selectors'=>array('{{WRAPPER}} .wpst-ew-footer-brand'=>'--wpst-footer-brand-icon-color:{{VALUE}};')
  ));
  $this->add_control('contact_icon_bg',array(
   'label'=>'İletişim İkon Arka Planı',
   'type'=>\Elementor\Controls_Manager::COLOR,
   'selectors'=>array('{{WRAPPER}} .wpst-ew-footer-brand'=>'--wpst-footer-brand-icon-bg:{{VALUE}};')
  ));
  $this->add_responsive_control('content_gap',array(
   'label'=>'İçerik Dikey Aralığı',
   'type'=>\Elementor\Controls_Manager::SLIDER,
   'range'=>array('px'=>array('min'=>0,'max'=>40)),
   'selectors'=>array('{{WRAPPER}} .wpst-ew-footer-brand'=>'--wpst-footer-brand-gap:{{SIZE}}px;')
  ));
  $this->end_controls_section();
  $this->standard_responsive_controls();
 }
 private function render_contact_icon($icon,$fallback){
  if(class_exists('WPST_Icon_Library')){
   echo WPST_Icon_Library::svg($icon?:$fallback,array('size'=>16));
  }else{
   echo esc_html($fallback==='mail'?'✉':'☎');
  }
 }

 private function phone_href($phone){
  return preg_replace('/[^0-9+]/','',(string)$phone);
 }

 protected function render(){
  $s=$this->get_settings_for_display();

  echo'<div class="wpst-ew-footer-brand">';

  echo'<div class="wpst-footer-brand-head">';
  if(!empty($s['logo']['url'])){
   echo'<img class="wpst-footer-brand-logo" src="'.esc_url($s['logo']['url']).'" alt="'.esc_attr($s['brand']).'">';
  }elseif(!empty($s['brand'])){
   echo'<strong class="wpst-footer-brand-name">'.esc_html($s['brand']).'</strong>';
  }

  if('yes'===($s['show_description']??'yes') && !empty($s['text'])){
   echo'<p>'.esc_html($s['text']).'</p>';
  }
  echo'</div>';

  $show_phone='yes'===($s['show_phone']??'yes') && !empty($s['phone']);
  $show_phone_2='yes'===($s['show_phone_2']??'') && !empty($s['phone_2']);
  $show_email='yes'===($s['show_email']??'yes') && !empty($s['email']);
  $show_icons='yes'===($s['show_contact_icons']??'yes');
  $show_labels='yes'===($s['show_contact_labels']??'yes');

  if($show_phone || $show_phone_2 || $show_email){
   echo'<div class="wpst-footer-brand-contact">';

   if($show_phone){
    echo'<a class="wpst-footer-contact-link is-phone" href="tel:'.esc_attr($this->phone_href($s['phone'])).'">';
    if($show_icons){
     echo'<i class="wpst-footer-contact-icon">';
     $this->render_contact_icon($s['phone_icon']??'phone','phone');
     echo'</i>';
    }
    echo'<span class="wpst-footer-contact-copy">';
    if($show_labels && !empty($s['phone_label']))echo'<small class="wpst-footer-contact-label">'.esc_html($s['phone_label']).'</small>';
    echo'<strong class="wpst-footer-contact-text">'.esc_html($s['phone']).'</strong>';
    echo'</span></a>';
   }

   if($show_phone_2){
    echo'<a class="wpst-footer-contact-link is-phone is-phone-secondary" href="tel:'.esc_attr($this->phone_href($s['phone_2'])).'">';
    if($show_icons){
     echo'<i class="wpst-footer-contact-icon">';
     $this->render_contact_icon($s['phone_2_icon']??'smartphone','phone');
     echo'</i>';
    }
    echo'<span class="wpst-footer-contact-copy">';
    if($show_labels && !empty($s['phone_2_label']))echo'<small class="wpst-footer-contact-label">'.esc_html($s['phone_2_label']).'</small>';
    echo'<strong class="wpst-footer-contact-text">'.esc_html($s['phone_2']).'</strong>';
    echo'</span></a>';
   }

   if($show_email){
    echo'<a class="wpst-footer-contact-link is-email" href="mailto:'.esc_attr($s['email']).'">';
    if($show_icons){
     echo'<i class="wpst-footer-contact-icon">';
     $this->render_contact_icon($s['email_icon']??'mail','mail');
     echo'</i>';
    }
    echo'<span class="wpst-footer-contact-copy">';
    if($show_labels && !empty($s['email_label']))echo'<small class="wpst-footer-contact-label">'.esc_html($s['email_label']).'</small>';
    echo'<strong class="wpst-footer-contact-text">'.esc_html($s['email']).'</strong>';
    echo'</span></a>';
   }

   echo'</div>';
  }

  echo'</div>';
 }
}
