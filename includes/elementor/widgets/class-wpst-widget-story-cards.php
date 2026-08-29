<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class WPST_Widget_Story_Cards extends WPST_Elementor_Widget_Base {
 public function get_name(){ return 'wpsoft-story-cards'; }
 public function get_title(){ return 'WPSoft · Story Cards 2.0'; }
 public function get_icon(){ return 'eicon-posts-ticker'; }
 public function get_keywords(){ return array('story','cards','process','steps','editorial','wpsoft'); }
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'Hikâyeler'));
  $this->wpst_signature_preset_control();
  $r=new \Elementor\Repeater();
  $r->add_control('eyebrow',array('label'=>'Etiket','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'01','dynamic'=>array('active'=>true)));
  $r->add_control('title',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Keşif','dynamic'=>array('active'=>true)));
  $r->add_control('text',array('label'=>'Açıklama','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'İhtiyacı ve hedefi doğru çerçeveliyoruz.','dynamic'=>array('active'=>true)));
  $r->add_control('image',array('label'=>'Görsel','type'=>\Elementor\Controls_Manager::MEDIA));
  $r->add_control('url',array('label'=>'Bağlantı','type'=>\Elementor\Controls_Manager::URL,'show_external'=>true,'dynamic'=>array('active'=>true)));
  $this->add_control('items',array('label'=>'Kartlar','type'=>\Elementor\Controls_Manager::REPEATER,'fields'=>$r->get_controls(),'default'=>array(
   array('eyebrow'=>'01','title'=>'Keşif','text'=>'İhtiyacı ve hedefi doğru çerçeveliyoruz.'),
   array('eyebrow'=>'02','title'=>'Tasarım','text'=>'Mesajı güçlü bir görsel sisteme dönüştürüyoruz.'),
   array('eyebrow'=>'03','title'=>'Büyüme','text'=>'Ölçülebilir ve sürdürülebilir sonuçlara odaklanıyoruz.')
  ),'title_field'=>'{{{ title }}}'));
  $this->add_control('layout',array('label'=>'Yerleşim','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'editorial','options'=>array('editorial'=>'Editorial','horizontal'=>'Horizontal Story','visual'=>'Visual Cards','steps'=>'Numbered Steps'),'prefix_class'=>'wpst-story-layout-'));
  $this->add_responsive_control('columns',array('label'=>'Kolon','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'3','tablet_default'=>'2','mobile_default'=>'1','options'=>array('1'=>'1','2'=>'2','3'=>'3'),'selectors'=>array('{{WRAPPER}} .wpst-story-cards'=>'grid-template-columns:repeat({{VALUE}},minmax(0,1fr))!important;')));
  $this->add_responsive_control('gap',array('label'=>'Kart Aralığı','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>60)),'default'=>array('size'=>18),'selectors'=>array('{{WRAPPER}} .wpst-story-cards'=>'--wpst-story-gap:{{SIZE}}px;')));
  $this->add_responsive_control('media_height',array('label'=>'Görsel Yüksekliği','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>120,'max'=>500)),'default'=>array('size'=>240),'selectors'=>array('{{WRAPPER}} .wpst-story-media'=>'--wpst-media-height:{{SIZE}}px;')));
  $this->add_responsive_control('wpst_media_position',array('label'=>'Görsel Yatay Konum','type'=>\Elementor\Controls_Manager::CHOOSE,'options'=>array('left'=>array('title'=>'Sol','icon'=>'eicon-h-align-left'),'center'=>array('title'=>'Orta','icon'=>'eicon-h-align-center'),'right'=>array('title'=>'Sağ','icon'=>'eicon-h-align-right'),'custom'=>array('title'=>'Özel','icon'=>'eicon-settings')),'default'=>'center','tablet_default'=>'center','mobile_default'=>'center','toggle'=>false,'selectors'=>array('{{WRAPPER}}'=>'--wpst-media-pos-x:{{VALUE}};')));
  $this->add_responsive_control('wpst_media_position_x',array('label'=>'Özel X Konumu','type'=>\Elementor\Controls_Manager::SLIDER,'size_units'=>array('%'),'range'=>array('%'=>array('min'=>0,'max'=>100)),'default'=>array('size'=>50,'unit'=>'%'),'condition'=>array('wpst_media_position'=>'custom'),'selectors'=>array('{{WRAPPER}}'=>'--wpst-media-custom-x:{{SIZE}}%;')));
  $this->add_responsive_control('wpst_media_position_y',array('label'=>'Özel Y Konumu','type'=>\Elementor\Controls_Manager::SLIDER,'size_units'=>array('%'),'range'=>array('%'=>array('min'=>0,'max'=>100)),'default'=>array('size'=>50,'unit'=>'%'),'condition'=>array('wpst_media_position'=>'custom'),'selectors'=>array('{{WRAPPER}}'=>'--wpst-media-pos-y:{{SIZE}}%;')));
  $this->end_controls_section();

  $this->start_controls_section('style_card',array('label'=>'Kart Biçimi','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('surface',array('label'=>'Kart Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-story-cards'=>'--wpst-story-surface:{{VALUE}};')));
  $this->add_control('border',array('label'=>'Kenarlık','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-story-cards'=>'--wpst-story-border:{{VALUE}};')));
  $this->add_control('hover_border',array('label'=>'Hover Kenarlık','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-story-cards'=>'--wpst-story-hover-border:{{VALUE}};')));
  $this->add_responsive_control('radius',array('label'=>'Kart Radius','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>50)),'default'=>array('size'=>22),'selectors'=>array('{{WRAPPER}} .wpst-story-cards'=>'--wpst-story-radius:{{SIZE}}px;')));
  $this->add_responsive_control('padding',array('label'=>'İçerik Boşluğu','type'=>\Elementor\Controls_Manager::DIMENSIONS,'size_units'=>array('px'),'default'=>array('top'=>24,'right'=>24,'bottom'=>24,'left'=>24,'unit'=>'px'),'selectors'=>array('{{WRAPPER}} .wpst-story-copy'=>'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};')));
  $this->add_control('hover_motion',array('label'=>'Hover Hareketi','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'lift','options'=>array('none'=>'Yok','lift'=>'Yüksel','scale'=>'Büyüt'),'prefix_class'=>'wpst-story-motion-'));
  $this->end_controls_section();

  $this->start_controls_section('style_type',array('label'=>'İçerik Biçimi','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('eyebrow_color',array('label'=>'Etiket Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-story-cards'=>'--wpst-story-eyebrow:{{VALUE}};')));
  $this->add_control('title_color',array('label'=>'Başlık Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-story-cards'=>'--wpst-story-title:{{VALUE}};')));
  $this->add_control('text_color',array('label'=>'Açıklama Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-story-cards'=>'--wpst-story-text:{{VALUE}};')));
  $this->add_control('arrow_color',array('label'=>'Ok Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-story-cards'=>'--wpst-story-arrow:{{VALUE}};')));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'title_typography','label'=>'Başlık Tipografi','selector'=>'{{WRAPPER}} .wpst-story-copy h3'));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'text_typography','label'=>'Açıklama Tipografi','selector'=>'{{WRAPPER}} .wpst-story-copy p'));
  $this->end_controls_section();
  $this->standard_responsive_controls();
 }
 protected function render(){
  $s=$this->get_settings_for_display();
  echo '<div class="wpst-story-cards">';
  foreach((array)$s['items'] as $i){
   $link=is_array($i['url']??null)?$i['url']:array();
   $url=!empty($link['url'])?esc_url($link['url']):'';
   $tag=$url?'a':'article'; $attr='';
   if($url){$attr=' href="'.$url.'"';if(!empty($link['is_external']))$attr.=' target="_blank"';$rels=array();if(!empty($link['nofollow']))$rels[]='nofollow';if(!empty($link['is_external']))$rels[]='noopener';if($rels)$attr.=' rel="'.esc_attr(implode(' ',$rels)).'"';}
   echo '<'.$tag.$attr.' class="wpst-story-card">';
   if(!empty($i['image']['url'])) echo '<div class="wpst-story-media"><img src="'.esc_url($i['image']['url']).'" alt="" loading="lazy" decoding="async"></div>';
   echo '<div class="wpst-story-copy"><span>'.esc_html($i['eyebrow']).'</span><h3>'.esc_html($i['title']).'</h3><p>'.esc_html($i['text']).'</p>';
   if($url) echo '<i aria-hidden="true">'.(class_exists('WPST_Icon_Library')?WPST_Icon_Library::svg('arrow-up-right',array('size'=>16)):'↗').'</i>';
   echo '</div></'.$tag.'>';
  }
  echo '</div>';
 }
}
