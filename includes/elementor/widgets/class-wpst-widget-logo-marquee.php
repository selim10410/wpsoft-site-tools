<?php
if(!defined('ABSPATH'))exit;
class WPST_Widget_Logo_Marquee extends WPST_Elementor_Widget_Base{
 public function get_name(){return'wpsoft-logo-marquee';} public function get_title(){return'WPSoft Logo Marquee';} public function get_icon(){return'eicon-carousel';}
 protected function register_controls(){ $this->start_controls_section('content',array('label'=>'Logolar'));
  $this->wpst_signature_preset_control();$this->add_control('gallery',array('label'=>'Logo Görselleri','type'=>\Elementor\Controls_Manager::GALLERY));$this->add_control('text_logos',array('label'=>'Metin Logoları','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'NOVA\nARC\nPIXEL\nATLAS\nNEXA\nFORM','description'=>'Görsel logo seçilmezse her satır kayan bir marka olarak kullanılır.'));
  $this->add_control('layout_variant',array(
   'label'=>'Marquee Yerleşimi','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'continuous',
   'options'=>array('continuous'=>'Continuous','pill'=>'Pill Logos','boxed'=>'Boxed Logos'),
   'prefix_class'=>'wpst-logo-marquee-layout-'
  ));
  $this->add_control('direction',array('label'=>'Akış Yönü','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'left','options'=>array('left'=>'Sola','right'=>'Sağa'),'prefix_class'=>'wpst-logo-marquee-dir-'));
  $this->add_control('speed',array('label'=>'Akış Hızı','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>8,'max'=>80)),'default'=>array('size'=>28),'selectors'=>array('{{WRAPPER}} .wpst-ew-logo-marquee-track'=>'--wpst-marquee-duration:{{SIZE}}s;')));
  $this->add_control('pause_hover',array('label'=>'Hover’da Duraklat','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes','prefix_class'=>'wpst-logo-marquee-pause-'));
  $this->add_responsive_control('logo_height',array('label'=>'Logo Yüksekliği','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>18,'max'=>140)),'default'=>array('size'=>42),'selectors'=>array('{{WRAPPER}} .wpst-ew-logo-marquee img'=>'height:{{SIZE}}px;width:auto;')));
  $this->add_responsive_control('item_gap',array('label'=>'Logo Aralığı','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>8,'max'=>100)),'default'=>array('size'=>30),'selectors'=>array('{{WRAPPER}} .wpst-ew-logo-marquee-track'=>'gap:{{SIZE}}px;')));
  $this->end_controls_section();
  $this->start_controls_section('logo_style',array('label'=>'Logo Biçimi','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('logo_opacity',array('label'=>'Logo Opaklığı','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array(''=>array('min'=>0.1,'max'=>1,'step'=>.05)),'default'=>array('size'=>.72),'selectors'=>array('{{WRAPPER}} .wpst-ew-logo-marquee figure'=>'opacity:{{SIZE}};')));
  $this->add_control('item_bg',array('label'=>'Öğe Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-ew-logo-marquee figure'=>'background:{{VALUE}};')));
  $this->add_responsive_control('item_radius',array('label'=>'Öğe Köşe','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>60)),'selectors'=>array('{{WRAPPER}} .wpst-ew-logo-marquee figure'=>'border-radius:{{SIZE}}px;')));
  $this->end_controls_section();
        $this->standard_responsive_controls();
    }
 protected function render(){ $s=$this->get_settings_for_display();$items=array();if(!empty($s['gallery']))$items=$s['gallery'];else foreach(preg_split('/\r\n|\r|\n/',(string)$s['text_logos']) as $x){if(trim($x)!=='')$items[]=array('label'=>trim($x));}echo'<div class="wpst-ew-logo-marquee"><div class="wpst-ew-logo-marquee-track">';for($r=0;$r<2;$r++)foreach($items as $i){echo'<figure>';if(!empty($i['url']))echo'<img src="'.esc_url($i['url']).'" alt="">';else echo'<span>'.esc_html($i['label']).'</span>';echo'</figure>';}echo'</div></div>'; }
}