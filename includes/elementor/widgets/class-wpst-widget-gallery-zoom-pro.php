<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class WPST_Widget_Gallery_Zoom_Pro extends WPST_Elementor_Widget_Base {
    public function get_name(){ return 'wpsoft-gallery-zoom-pro'; }
    public function get_title(){ return 'WPSoft · Gallery Zoom 2.0'; }
    public function get_keywords(){ return array('gallery','galeri','zoom','lightbox','image','görsel','photo','fotoğraf','masonry','wpsoft'); }
    public function get_icon(){ return 'eicon-gallery-grid'; }
    public function get_categories(){ return array('wpsoft'); }
    public function show_in_panel(){ return true; }

    protected function register_controls(){
        $this->start_controls_section('content',array('label'=>'Galeri'));
  $this->wpst_signature_preset_control();
        $this->add_control('gallery',array(
            'label'=>'Görseller',
            'type'=>\Elementor\Controls_Manager::GALLERY
        ));
        $this->add_control('layout',array(
            'label'=>'Düzen',
            'type'=>\Elementor\Controls_Manager::SELECT,
            'default'=>'grid',
            'options'=>array(
                'grid'=>'Modern Grid',
                'masonry'=>'Masonry',
                'featured'=>'İlk Görsel Büyük',
                'carousel'=>'Yatay Carousel',
                'editorial'=>'Editorial Grid',
                'filmstrip'=>'Filmstrip',
                'collage'=>'Modern Collage',
            )
        ));
        $this->add_responsive_control('columns',array(
            'label'=>'Sütun',
            'type'=>\Elementor\Controls_Manager::SELECT,
            'default'=>'3',
            'tablet_default'=>'2',
            'mobile_default'=>'1',
            'options'=>array('1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5'),
            'selectors'=>array('{{WRAPPER}} .wpst-gallery-zoom-pro'=>'--wpst-gallery-cols:{{VALUE}}')
        ));
        $this->add_control('aspect_ratio',array(
            'label'=>'Görsel Oranı',
            'type'=>\Elementor\Controls_Manager::SELECT,
            'default'=>'4-3',
            'options'=>array('auto'=>'Otomatik','1-1'=>'1:1 Kare','4-3'=>'4:3','3-2'=>'3:2','16-9'=>'16:9','21-9'=>'21:9')
        ));
        $this->add_control('image_fit',array(
            'label'=>'Görsel Yerleşimi',
            'type'=>\Elementor\Controls_Manager::SELECT,
            'default'=>'cover',
            'options'=>array('cover'=>'Kapla','contain'=>'Sığdır')
        ));
        $this->add_control('hover_effect',array(
            'label'=>'Hover Efekti',
            'type'=>\Elementor\Controls_Manager::SELECT,
            'default'=>'zoom',
            'options'=>array('none'=>'Yok','zoom'=>'Zoom','lift'=>'Yükselme','soft'=>'Soft Fade','grayscale'=>'Grayscale → Renk')
        ));
        $this->add_control('carousel_arrows',array(
            'label'=>'Carousel Okları',
            'type'=>\Elementor\Controls_Manager::SWITCHER,
            'return_value'=>'yes','default'=>'yes',
            'condition'=>array('layout'=>'carousel')
        ));
        $this->add_control('carousel_snap',array(
            'label'=>'Snap Kaydırma',
            'type'=>\Elementor\Controls_Manager::SWITCHER,
            'return_value'=>'yes','default'=>'yes',
            'condition'=>array('layout'=>'carousel')
        ));
        $this->add_responsive_control('gap',array(
            'label'=>'Görsel Aralığı',
            'type'=>\Elementor\Controls_Manager::SLIDER,
            'size_units'=>array('px'),
            'range'=>array('px'=>array('min'=>0,'max'=>80)),
            'default'=>array('size'=>14,'unit'=>'px'),
            'tablet_default'=>array('size'=>12,'unit'=>'px'),
            'mobile_default'=>array('size'=>10,'unit'=>'px'),
            'selectors'=>array(
                '{{WRAPPER}} .wpst-gallery-zoom-pro'=>'--wpst-gallery-gap:{{SIZE}}{{UNIT}};gap:{{SIZE}}{{UNIT}} !important;'
            )
        ));
        $this->add_control('lightbox',array(
            'label'=>'Zoom / Lightbox',
            'type'=>\Elementor\Controls_Manager::SWITCHER,
            'return_value'=>'yes',
            'default'=>'yes'
        ));
        $this->add_control('lightbox_style',array(
            'label'=>'Lightbox Stili',
            'type'=>\Elementor\Controls_Manager::SELECT,
            'default'=>'dark',
            'options'=>array('dark'=>'Dark Cinema','glass'=>'Glass','clean'=>'Clean Light'),
            'condition'=>array('lightbox'=>'yes')
        ));
        $this->add_control('show_caption',array(
            'label'=>'Görsel Başlığını Göster',
            'type'=>\Elementor\Controls_Manager::SWITCHER,
            'return_value'=>'yes',
            'default'=>''
        ));
        $this->add_control('zoom_label',array(
            'label'=>'Zoom Erişilebilirlik Metni',
            'type'=>\Elementor\Controls_Manager::TEXT,
            'default'=>'Görseli büyüt'
        ));
        $this->add_control('placeholder_text',array(
            'label'=>'Boş Galeri Görsel Metni',
            'type'=>\Elementor\Controls_Manager::TEXT,
            'default'=>'Görsel'
        ));
        $this->add_control('placeholder_count',array(
            'label'=>'Boş Galeri Örnek Sayısı',
            'type'=>\Elementor\Controls_Manager::NUMBER,
            'min'=>1,'max'=>12,'default'=>6
        ));
        $this->end_controls_section();

        $this->start_controls_section('image_style',array('label'=>'Görsel Stili','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
        $this->add_responsive_control('height',array(
            'label'=>'Grid Görsel Yüksekliği',
            'type'=>\Elementor\Controls_Manager::SLIDER,
            'size_units'=>array('px'),
            'range'=>array('px'=>array('min'=>140,'max'=>800)),
            'default'=>array('size'=>320,'unit'=>'px'),
            'selectors'=>array('{{WRAPPER}} .wpst-gallery-zoom-pro:not(.is-masonry) .wpst-gallery-zoom-item img'=>'height:{{SIZE}}{{UNIT}}')
        ));
        $this->add_control('radius',array(
            'label'=>'Köşe Yuvarlaklığı',
            'type'=>\Elementor\Controls_Manager::SLIDER,
            'range'=>array('px'=>array('min'=>0,'max'=>60)),
            'default'=>array('size'=>18),
            'selectors'=>array('{{WRAPPER}} .wpst-gallery-zoom-item'=>'border-radius:{{SIZE}}px')
        ));
        $this->add_control('overlay_color',array(
            'label'=>'Hover Overlay',
            'type'=>\Elementor\Controls_Manager::COLOR,
            'default'=>'rgba(15,23,42,.28)',
            'selectors'=>array('{{WRAPPER}} .wpst-gallery-zoom-overlay'=>'background:{{VALUE}}')
        ));
        $this->add_control('zoom_icon_bg',array(
            'label'=>'Zoom Buton Arka Plan',
            'type'=>\Elementor\Controls_Manager::COLOR,
            'default'=>'#ffffff',
            'selectors'=>array('{{WRAPPER}} .wpst-gallery-zoom-icon'=>'background:{{VALUE}}')
        ));
        $this->add_control('zoom_icon_color',array(
            'label'=>'Zoom İkon Rengi',
            'type'=>\Elementor\Controls_Manager::COLOR,
            'default'=>'#0f172a',
            'selectors'=>array('{{WRAPPER}} .wpst-gallery-zoom-icon'=>'color:{{VALUE}}')
        ));
        $this->end_controls_section();

        $this->standard_responsive_controls();
    }

    protected function render(){
        $s=$this->get_settings_for_display();
        $gallery=(array)$s['gallery'];
        $layout=in_array($s['layout'],array('grid','masonry','featured','carousel','editorial','filmstrip','collage'),true)?$s['layout']:'grid';

        $ratio=in_array($s['aspect_ratio'],array('auto','1-1','4-3','3-2','16-9','21-9'),true)?$s['aspect_ratio']:'4-3';
        $fit=('contain'===$s['image_fit'])?'contain':'cover';
        $hover=in_array($s['hover_effect'],array('none','zoom','lift','soft','grayscale'),true)?$s['hover_effect']:'zoom';
        $lb_style=in_array($s['lightbox_style'],array('dark','glass','clean'),true)?$s['lightbox_style']:'dark';
        echo '<div class="wpst-gallery-zoom-pro is-'.esc_attr($layout).' hover-'.esc_attr($hover).' ratio-'.esc_attr($ratio).'" data-wpst-gallery data-lightbox-style="'.esc_attr($lb_style).'" data-fit="'.esc_attr($fit).'"'.(('carousel'===$layout&&'yes'===$s['carousel_snap'])?' data-snap="1"':'').'>';
        if('carousel'===$layout && 'yes'===$s['carousel_arrows']){
            echo '<div class="wpst-gallery-carousel-controls"><button type="button" data-gallery-prev aria-label="Önceki">'.(class_exists('WPST_Icon_Library')?WPST_Icon_Library::svg('arrow-left',array('size'=>16)):'‹').'</button><button type="button" data-gallery-next aria-label="Sonraki">'.(class_exists('WPST_Icon_Library')?WPST_Icon_Library::svg('arrow-right',array('size'=>16)):'›').'</button></div>';
        }
        if(empty($gallery)){
            for($i=0;$i<max(1,(int)$s['placeholder_count']);$i++){
                echo '<div class="wpst-gallery-zoom-item is-placeholder"><span>'.esc_html($s['placeholder_text']).' '.($i+1).'</span></div>';
            }
        }else{
            foreach($gallery as $index=>$img){
                $url=!empty($img['url'])?$img['url']:'';
                $id=!empty($img['id'])?absint($img['id']):0;
                $caption=$id?wp_get_attachment_caption($id):'';
                $alt=$id?get_post_meta($id,'_wp_attachment_image_alt',true):'';
                $tag=('yes'===$s['lightbox'])?'button':'div';
                echo '<'.$tag.' class="wpst-gallery-zoom-item" '.(('yes'===$s['lightbox'])?'type="button" data-wpst-gallery-open data-full="'.esc_url($url).'" data-alt="'.esc_attr($alt).'" data-caption="'.esc_attr($caption).'" aria-label="'.esc_attr($s['zoom_label']).'"':'').'>';
                echo '<img src="'.esc_url($url).'" alt="'.esc_attr($alt).'" loading="lazy">';
                echo '<span class="wpst-gallery-zoom-overlay"></span>';
                if('yes'===$s['lightbox']){
                    echo '<span class="wpst-gallery-zoom-icon" aria-hidden="true">'.(class_exists('WPST_Icon_Library')?WPST_Icon_Library::svg('search',array('size'=>18)):'＋').'</span>';
                }
                if('yes'===$s['show_caption'] && $caption){
                    echo '<span class="wpst-gallery-zoom-caption">'.esc_html($caption).'</span>';
                }
                echo '</'.$tag.'>';
            }
        }
        echo '</div>';
    }
}
