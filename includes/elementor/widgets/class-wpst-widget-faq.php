<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class WPST_Widget_FAQ extends WPST_Elementor_Widget_Base {
 public function get_name(){return 'wpsoft-faq';}
 public function get_title(){return 'WPSoft · FAQ 2.0';}
 public function get_icon(){return 'eicon-accordion';}
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'Sorular'));
  $this->wpst_signature_preset_control();
  $rep=new \Elementor\Repeater();
  $rep->add_control('question',array('label'=>'Soru','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Bu hizmet nasıl çalışır?'));
  $rep->add_control('answer',array('label'=>'Cevap','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'İhtiyaçlarınıza göre planlama yaparak projeyi adım adım hayata geçiriyoruz.'));
  $this->add_control('items',array(
   'label'=>'SSS','type'=>\Elementor\Controls_Manager::REPEATER,'fields'=>$rep->get_controls(),
   'default'=>array(
    array('question'=>'Projeye nasıl başlıyoruz?','answer'=>'İhtiyaç analizi ile başlayıp tasarım ve geliştirme aşamasına geçiyoruz.'),
    array('question'=>'Mobil uyumlu mu?','answer'=>'Evet, tüm bileşenler responsive olarak hazırlanmıştır.'),
    array('question'=>'Sonradan düzenleyebilir miyim?','answer'=>'Evet, içerik ve tasarım ayarlarını Elementor üzerinden yönetebilirsiniz.')
   ),
   'title_field'=>'{{{ question }}}'
  ));
  $this->add_control('first_open',array('label'=>'İlk Soru Açık','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes'));
  $this->add_control('toggle_icon',array('label'=>'Aç/Kapat Icon','type'=>\Elementor\Controls_Manager::SELECT2,'options'=>class_exists('WPST_Icon_Library')?WPST_Icon_Library::options():array(),'default'=>'plus','label_block'=>true));
  $this->add_control('style_preset',array('label'=>'Stil','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'clean','options'=>array('clean'=>'Clean','cards'=>'Cards','soft'=>'Soft','dark'=>'Dark'),'prefix_class'=>'wpst-faq-style-'));
  $this->add_control('single_open',array('label'=>'Tek Soru Açık Kalsın','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>''));
  $this->end_controls_section();

  $this->start_controls_section('style',array('label'=>'Biçim','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('accent',array('label'=>'Vurgu','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#2563eb','selectors'=>array('{{WRAPPER}} .wpst-ew-faq'=>'--faq-accent:{{VALUE}};')));
  $this->add_control('title_color',array('label'=>'Soru Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-faq'=>'--faq-title:{{VALUE}};')));
  $this->add_control('text_color',array('label'=>'Cevap Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-faq'=>'--faq-text:{{VALUE}};')));
  $this->add_control('item_bg',array('label'=>'Öğe Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-faq details'=>'background:{{VALUE}};')));
  $this->add_control('border_color',array('label'=>'Kenarlık Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-faq details'=>'border-color:{{VALUE}};')));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'question_typography','label'=>'Soru Tipografisi','selector'=>'{{WRAPPER}} .wpst-ew-faq summary'));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'answer_typography','label'=>'Cevap Tipografisi','selector'=>'{{WRAPPER}} .wpst-faq-answer'));
  $this->add_responsive_control('gap',array('label'=>'Soru Aralığı','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>40)),'default'=>array('size'=>10),'selectors'=>array('{{WRAPPER}} .wpst-ew-faq'=>'gap:{{SIZE}}px;')));
  $this->add_responsive_control('icon_size',array('label'=>'Aç/Kapat Icon Alanı','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>28,'max'=>60)),'selectors'=>array('{{WRAPPER}} .wpst-faq-toggle'=>'width:{{SIZE}}px;height:{{SIZE}}px;')));
  $this->add_responsive_control('radius',array('label'=>'Köşe','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>40)),'default'=>array('size'=>16),'selectors'=>array('{{WRAPPER}} .wpst-ew-faq details'=>'border-radius:{{SIZE}}px;')));
  $this->add_responsive_control('question_padding',array('label'=>'Soru İç Boşluk','type'=>\Elementor\Controls_Manager::DIMENSIONS,'size_units'=>array('px'),'selectors'=>array('{{WRAPPER}} .wpst-ew-faq summary'=>'padding:{{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px!important;')));
  $this->end_controls_section();
  $this->standard_responsive_controls();
 }
 protected function render(){
  $s=$this->get_settings_for_display();
  echo '<div class="wpst-ew-faq" data-single-open="'.esc_attr('yes'===($s['single_open']??'')?'yes':'no').'">';
  foreach((array)$s['items'] as $i=>$item){
   $open=($i===0 && 'yes'===$s['first_open']);
   echo '<details'.($open?' open':'').'><summary><span>'.esc_html($item['question']).'</span><i class="wpst-faq-toggle">'.(class_exists('WPST_Icon_Library')?WPST_Icon_Library::svg($s['toggle_icon'],array('size'=>15)):'+').'</i></summary><div class="wpst-faq-answer">'.wp_kses_post($item['answer']).'</div></details>';
  }
  echo '</div>';
 }
}
