<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class WPST_Widget_Image_Box_Pro extends WPST_Elementor_Widget_Base {

    public function get_name(){ return 'wpsoft-image-box-pro'; }
    public function get_title(){ return 'WPSoft · Image Box Pro'; }
    public function get_icon(){ return 'eicon-image-box'; }

    protected function register_controls(){

        $this->start_controls_section('content',array('label'=>'İçerik'));
        $this->wpst_signature_preset_control();

        $this->add_control('image',array(
            'label'=>'Görsel',
            'type'=>\Elementor\Controls_Manager::MEDIA,
            'default'=>array('url'=>\Elementor\Utils::get_placeholder_image_src())
        ));

        $this->add_control('eyebrow',array(
            'label'=>'Üst Etiket',
            'type'=>\Elementor\Controls_Manager::TEXT,
            'default'=>'WPSOFT'
        ));

        $this->add_control('title',array(
            'label'=>'Başlık',
            'type'=>\Elementor\Controls_Manager::TEXT,
            'default'=>'Modern görsel kutusu',
            'label_block'=>true
        ));

        $this->add_control('description',array(
            'label'=>'Açıklama',
            'type'=>\Elementor\Controls_Manager::TEXTAREA,
            'default'=>'Görsel, metin ve aksiyonu tek bir modern bileşende birleştirin.'
        ));

        $this->add_control('badge',array(
            'label'=>'Badge',
            'type'=>\Elementor\Controls_Manager::TEXT,
            'default'=>'Yeni'
        ));

        $this->add_control('wpst_icon',array(
            'label'=>'Aksiyon Icon',
            'type'=>\Elementor\Controls_Manager::SELECT2,
            'options'=>class_exists('WPST_Icon_Library') ? WPST_Icon_Library::options() : array(),
            'default'=>'arrow-up-right',
            'label_block'=>true
        ));

        $this->link_controls('button','Bağlantı');

        $this->add_control('layout',array(
            'label'=>'Görünüm',
            'type'=>\Elementor\Controls_Manager::SELECT,
            'default'=>'below',
            'options'=>array(
                'below'=>'Görsel Üstte / İçerik Altta',
                'overlay'=>'Görsel Üstü Overlay',
                'side'=>'Yatay Görsel Kutusu',
                'minimal'=>'Minimal',
                'poster'=>'Poster / Editorial',
                'floating'=>'Floating Content',
                'full-image'=>'Tam Görsel'
            ),
            'prefix_class'=>'wpst-image-box-layout-'
        ));

        $this->add_control('content_align',array(
            'label'=>'İçerik Hizalama',
            'type'=>\Elementor\Controls_Manager::CHOOSE,
            'default'=>'left',
            'options'=>array(
                'left'=>array('title'=>'Sol','icon'=>'eicon-text-align-left'),
                'center'=>array('title'=>'Orta','icon'=>'eicon-text-align-center'),
                'right'=>array('title'=>'Sağ','icon'=>'eicon-text-align-right')
            ),
            'selectors'=>array('{{WRAPPER}} .wpst-image-box-copy'=>'text-align:{{VALUE}};')
        ));

        $this->add_control('show_description',array(
            'label'=>'Açıklamayı Göster',
            'type'=>\Elementor\Controls_Manager::SWITCHER,
            'return_value'=>'yes',
            'default'=>'yes'
        ));

        $this->add_control('show_badge',array(
            'label'=>'Badge Göster',
            'type'=>\Elementor\Controls_Manager::SWITCHER,
            'return_value'=>'yes',
            'default'=>'yes'
        ));

        $this->add_control('show_button',array(
            'label'=>'Butonu Göster',
            'type'=>\Elementor\Controls_Manager::SWITCHER,
            'return_value'=>'yes',
            'default'=>'yes'
        ));

        $this->add_control('full_image_content',array(
            'label'=>'Tam Görselde İçerik',
            'type'=>\Elementor\Controls_Manager::SELECT,
            'default'=>'overlay',
            'options'=>array(
                'overlay'=>'Görsel Üstünde',
                'hover'=>'Sadece Hover’da',
                'none'=>'Sadece Görsel'
            ),
            'prefix_class'=>'wpst-image-box-full-content-',
            'condition'=>array('layout'=>'full-image')
        ));

        $this->add_control('full_image_vertical',array(
            'label'=>'İçerik Dikey Konumu',
            'type'=>\Elementor\Controls_Manager::SELECT,
            'default'=>'bottom',
            'options'=>array(
                'top'=>'Üst',
                'center'=>'Orta',
                'bottom'=>'Alt'
            ),
            'prefix_class'=>'wpst-image-box-full-v-',
            'condition'=>array('layout'=>'full-image')
        ));

        $this->end_controls_section();


        $this->start_controls_section('media',array(
            'label'=>'Görsel',
            'tab'=>\Elementor\Controls_Manager::TAB_STYLE
        ));

        $this->add_control('image_ratio',array(
            'label'=>'Görsel Oranı',
            'type'=>\Elementor\Controls_Manager::SELECT,
            'default'=>'4-3',
            'options'=>array(
                'auto'=>'Otomatik',
                '1-1'=>'1:1',
                '4-3'=>'4:3',
                '3-2'=>'3:2',
                '16-9'=>'16:9',
                '4-5'=>'4:5',
                '3-4'=>'3:4'
            ),
            'prefix_class'=>'wpst-image-box-ratio-'
        ));

        $this->add_responsive_control('full_image_height',array(
            'label'=>'Tam Görsel Yüksekliği',
            'type'=>\Elementor\Controls_Manager::SLIDER,
            'size_units'=>array('px','vh'),
            'range'=>array(
                'px'=>array('min'=>220,'max'=>900),
                'vh'=>array('min'=>30,'max'=>90)
            ),
            'default'=>array('unit'=>'px','size'=>420),
            'selectors'=>array('{{WRAPPER}} .wpst-image-box-pro'=>'--wpst-image-box-full-height:{{SIZE}}{{UNIT}};'),
            'condition'=>array('layout'=>'full-image')
        ));

        $this->add_control('object_fit',array(
            'label'=>'Görsel Yerleşimi',
            'type'=>\Elementor\Controls_Manager::SELECT,
            'default'=>'cover',
            'options'=>array('cover'=>'Cover','contain'=>'Contain'),
            'selectors'=>array('{{WRAPPER}} .wpst-image-box-media img'=>'object-fit:{{VALUE}};')
        ));

        $this->add_control('object_position',array(
            'label'=>'Görsel Odak',
            'type'=>\Elementor\Controls_Manager::SELECT,
            'default'=>'center center',
            'options'=>array(
                'center center'=>'Orta',
                'center top'=>'Üst',
                'center bottom'=>'Alt',
                'left center'=>'Sol',
                'right center'=>'Sağ'
            ),
            'selectors'=>array('{{WRAPPER}} .wpst-image-box-media img'=>'object-position:{{VALUE}};')
        ));

        $this->add_control('hover_effect',array(
            'label'=>'Hover Efekti',
            'type'=>\Elementor\Controls_Manager::SELECT,
            'default'=>'zoom',
            'options'=>array(
                'none'=>'Yok',
                'zoom'=>'Zoom',
                'zoom-out'=>'Zoom Out',
                'lift'=>'Lift',
                'tilt'=>'Tilt',
                'reveal'=>'Reveal'
            ),
            'prefix_class'=>'wpst-image-box-hover-'
        ));

        $this->add_control('overlay_color',array(
            'label'=>'Overlay Rengi',
            'type'=>\Elementor\Controls_Manager::COLOR,
            'default'=>'rgba(15,23,42,.55)',
            'selectors'=>array('{{WRAPPER}} .wpst-image-box-overlay'=>'background:{{VALUE}}!important;')
        ));

        $this->add_responsive_control('media_radius',array(
            'label'=>'Görsel Köşe',
            'type'=>\Elementor\Controls_Manager::SLIDER,
            'range'=>array('px'=>array('min'=>0,'max'=>80)),
            'selectors'=>array('{{WRAPPER}} .wpst-image-box-media'=>'border-radius:{{SIZE}}px;')
        ));

        $this->end_controls_section();


        $this->start_controls_section('box_style',array(
            'label'=>'Kutu',
            'tab'=>\Elementor\Controls_Manager::TAB_STYLE
        ));

        $this->add_control('box_bg',array(
            'label'=>'Kutu Arka Plan',
            'type'=>\Elementor\Controls_Manager::COLOR,
            'selectors'=>array('{{WRAPPER}} .wpst-image-box-pro'=>'background:{{VALUE}}!important;')
        ));

        $this->add_control('border_color',array(
            'label'=>'Kenarlık Rengi',
            'type'=>\Elementor\Controls_Manager::COLOR,
            'selectors'=>array('{{WRAPPER}} .wpst-image-box-pro'=>'border-color:{{VALUE}}!important;')
        ));

        $this->add_responsive_control('box_radius',array(
            'label'=>'Kutu Köşe',
            'type'=>\Elementor\Controls_Manager::SLIDER,
            'range'=>array('px'=>array('min'=>0,'max'=>80)),
            'selectors'=>array('{{WRAPPER}} .wpst-image-box-pro'=>'border-radius:{{SIZE}}px;')
        ));

        $this->add_responsive_control('content_padding',array(
            'label'=>'İçerik İç Boşluk',
            'type'=>\Elementor\Controls_Manager::DIMENSIONS,
            'size_units'=>array('px'),
            'selectors'=>array('{{WRAPPER}} .wpst-image-box-copy'=>'padding:{{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;')
        ));

        $this->add_control('shadow_style',array(
            'label'=>'Gölge',
            'type'=>\Elementor\Controls_Manager::SELECT,
            'default'=>'soft',
            'options'=>array(
                'none'=>'Yok',
                'soft'=>'Soft',
                'medium'=>'Medium',
                'strong'=>'Strong'
            ),
            'prefix_class'=>'wpst-image-box-shadow-'
        ));

        $this->end_controls_section();


        $this->start_controls_section('content_style',array(
            'label'=>'Metin & Aksiyon',
            'tab'=>\Elementor\Controls_Manager::TAB_STYLE
        ));

        $this->add_control('eyebrow_color',array(
            'label'=>'Etiket Rengi',
            'type'=>\Elementor\Controls_Manager::COLOR,
            'selectors'=>array('{{WRAPPER}} .wpst-image-box-copy>small'=>'color:{{VALUE}}!important;')
        ));

        $this->add_control('title_color',array(
            'label'=>'Başlık Rengi',
            'type'=>\Elementor\Controls_Manager::COLOR,
            'selectors'=>array('{{WRAPPER}} .wpst-image-box-copy h3'=>'color:{{VALUE}}!important;')
        ));

        $this->add_control('text_color',array(
            'label'=>'Açıklama Rengi',
            'type'=>\Elementor\Controls_Manager::COLOR,
            'selectors'=>array('{{WRAPPER}} .wpst-image-box-copy p'=>'color:{{VALUE}}!important;')
        ));

        $this->add_control('badge_bg',array(
            'label'=>'Badge Arka Plan',
            'type'=>\Elementor\Controls_Manager::COLOR,
            'selectors'=>array('{{WRAPPER}} .wpst-image-box-badge'=>'background:{{VALUE}}!important;')
        ));

        $this->add_control('badge_color',array(
            'label'=>'Badge Yazı',
            'type'=>\Elementor\Controls_Manager::COLOR,
            'selectors'=>array('{{WRAPPER}} .wpst-image-box-badge'=>'color:{{VALUE}}!important;')
        ));

        $this->add_responsive_control('title_size',array(
            'label'=>'Başlık Boyutu',
            'type'=>\Elementor\Controls_Manager::SLIDER,
            'size_units'=>array('px','vw'),
            'range'=>array(
                'px'=>array('min'=>14,'max'=>72),
                'vw'=>array('min'=>1,'max'=>6,'step'=>.1)
            ),
            'selectors'=>array('{{WRAPPER}} .wpst-image-box-copy h3'=>'font-size:{{SIZE}}{{UNIT}};')
        ));

        $this->end_controls_section();

        $this->standard_responsive_controls();
    }

    protected function render(){
        $s=$this->get_settings_for_display();
        $url=!empty($s['button_url']['url']) ? $s['button_url']['url'] : '';
        $tag=$url ? 'a' : 'article';

        echo '<'.$tag.' class="wpst-image-box-pro"';
        if($url) echo $this->render_link_attrs($s['button_url']);
        echo '>';

        echo '<div class="wpst-image-box-media">';
        if(!empty($s['image']['url'])){
            echo '<img src="'.esc_url($s['image']['url']).'" alt="'.esc_attr($s['title']).'" loading="lazy">';
        }
        echo '<div class="wpst-image-box-overlay"></div>';

        if('yes'===$s['show_badge'] && trim((string)$s['badge'])!==''){
            echo '<span class="wpst-image-box-badge">'.esc_html($s['badge']).'</span>';
        }
        echo '</div>';

        echo '<div class="wpst-image-box-copy">';
        if(trim((string)$s['eyebrow'])!=='') echo '<small>'.esc_html($s['eyebrow']).'</small>';
        echo '<h3>'.esc_html($s['title']).'</h3>';
        if('yes'===$s['show_description'] && trim((string)$s['description'])!=='') echo '<p>'.esc_html($s['description']).'</p>';

        if('yes'===$s['show_button'] && trim((string)$s['button_text'])!==''){
            echo '<span class="wpst-image-box-action"><b>'.esc_html($s['button_text']).'</b><i>';
            if(class_exists('WPST_Icon_Library')) echo WPST_Icon_Library::svg($s['wpst_icon'],array('size'=>16));
            echo '</i></span>';
        }

        echo '</div></'.$tag.'>';
    }
}
