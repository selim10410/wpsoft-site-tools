<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class WPST_Widget_Hero_Slider extends WPST_Elementor_Widget_Base {

    public function get_name(){ return 'wpsoft-hero-slider'; }
    public function get_title(){ return 'WPSoft · Hero Slider 2.0'; }
    public function get_icon(){ return 'eicon-slider-push'; }

    protected function register_controls(){

        $this->start_controls_section('content',array('label'=>'İçerik'));
        $r = new \Elementor\Repeater();
        $r->add_control('eyebrow',array('label'=>'Üst Etiket','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'WPSoft'));
        $r->add_control('title',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'Modern dijital deneyimler oluşturun'));
        $r->add_control('text',array('label'=>'Açıklama','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'Markanız için güçlü ve modern bir sunum.'));
        $r->add_control('image',array('label'=>'Arka Plan Görseli','type'=>\Elementor\Controls_Manager::MEDIA));
        $r->add_control('button',array('label'=>'Buton Metni','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Detaylı İncele'));
        $r->add_control('url',array('label'=>'Buton Bağlantısı','type'=>\Elementor\Controls_Manager::URL));
        $r->add_control('button_icon',array('label'=>'Buton Icon','type'=>\Elementor\Controls_Manager::SELECT2,'options'=>class_exists('WPST_Icon_Library')?WPST_Icon_Library::options():array(),'default'=>'arrow-right','label_block'=>true));

        $this->add_control('items',array(
            'label'=>'Slaytlar',
            'type'=>\Elementor\Controls_Manager::REPEATER,
            'fields'=>$r->get_controls(),
            'default'=>array(
                array('eyebrow'=>'Dijital Çözümler','title'=>'İşletmenizi dijitalde daha güçlü hale getirin','text'=>'Modern tasarım ve güçlü içerik yapısı.','button'=>'Teklif Al'),
                array('eyebrow'=>'Yeni Nesil','title'=>'Hızlı, modern ve mobil uyumlu web deneyimi','text'=>'Her cihazda güçlü kullanıcı deneyimi.','button'=>'Hizmetleri İncele'),
                array('eyebrow'=>'WPSoft','title'=>'Markanıza özel modern web altyapısı','text'=>'İş hedeflerinize uygun esnek çözümler.','button'=>'Bize Ulaşın')
            ),
            'title_field'=>'{{{ title }}}'
        ));

        $this->add_control('autoplay',array(
            'label'=>'Otomatik Oynat',
            'type'=>\Elementor\Controls_Manager::SWITCHER,
            'default'=>'yes',
            'return_value'=>'yes'
        ));

        $this->add_control('delay',array(
            'label'=>'Geçiş Süresi (ms)',
            'type'=>\Elementor\Controls_Manager::NUMBER,
            'default'=>5000,
            'min'=>1000,
            'step'=>500
        ));

        $this->add_control('pause_hover',array(
            'label'=>'Hover’da Duraklat',
            'type'=>\Elementor\Controls_Manager::SWITCHER,
            'default'=>'yes',
            'return_value'=>'yes'
        ));

        $this->add_control('show_arrows',array(
            'label'=>'Okları Göster',
            'type'=>\Elementor\Controls_Manager::SWITCHER,
            'default'=>'yes',
            'return_value'=>'yes'
        ));

        $this->add_control('show_dots',array(
            'label'=>'Noktaları Göster',
            'type'=>\Elementor\Controls_Manager::SWITCHER,
            'default'=>'yes',
            'return_value'=>'yes'
        ));

        $this->add_control('show_progress',array(
            'label'=>'İlerleme Çizgisi',
            'type'=>\Elementor\Controls_Manager::SWITCHER,
            'default'=>'yes',
            'return_value'=>'yes'
        ));

        $this->add_control('show_counter',array(
            'label'=>'Slayt Sayacı',
            'type'=>\Elementor\Controls_Manager::SWITCHER,
            'default'=>'yes',
            'return_value'=>'yes'
        ));

        $this->add_control('transition_style',array(
            'label'=>'Geçiş Efekti',
            'type'=>\Elementor\Controls_Manager::SELECT,
            'default'=>'fade',
            'options'=>array(
                'fade'=>'Fade',
                'slide'=>'Slide',
                'zoom'=>'Soft Zoom'
            ),
            'prefix_class'=>'wpst-hero-slider-transition-'
        ));

        $this->add_control('touch_swipe',array(
            'label'=>'Dokunmatik Kaydırma',
            'type'=>\Elementor\Controls_Manager::SWITCHER,
            'default'=>'yes',
            'return_value'=>'yes'
        ));

        $this->add_control('mouse_drag',array(
            'label'=>'Mouse ile Sürükle',
            'type'=>\Elementor\Controls_Manager::SWITCHER,
            'default'=>'yes',
            'return_value'=>'yes'
        ));

        $this->add_control('mobile_controls',array(
            'label'=>'Mobil Kontrol Yerleşimi',
            'type'=>\Elementor\Controls_Manager::SELECT,
            'default'=>'bottom',
            'options'=>array(
                'bottom'=>'Alt Satır',
                'sides'=>'Görsel Yanları'
            ),
            'prefix_class'=>'wpst-hero-slider-mobile-controls-'
        ));
        $this->add_control('slider_style',array(
            'label'=>'Slider Stili',
            'type'=>\Elementor\Controls_Manager::SELECT,
            'default'=>'cinematic',
            'options'=>array('cinematic'=>'Cinematic','editorial'=>'Editorial','minimal'=>'Minimal','glass'=>'Glass'),
            'prefix_class'=>'wpst-hero-slider-style-'
        ));

        $this->end_controls_section();

        $this->start_controls_section('style_box',array(
            'label'=>'Biçim',
            'tab'=>\Elementor\Controls_Manager::TAB_STYLE
        ));

        $this->add_responsive_control('height',array(
            'label'=>'Yükseklik',
            'type'=>\Elementor\Controls_Manager::SLIDER,
            'range'=>array('px'=>array('min'=>360,'max'=>900)),
            'default'=>array('size'=>620),
            'selectors'=>array('{{WRAPPER}} .wpst-ew-hero-slider'=>'min-height:{{SIZE}}{{UNIT}}')
        ));

        $this->add_control('radius',array(
            'label'=>'Köşe Yuvarlaklığı',
            'type'=>\Elementor\Controls_Manager::SLIDER,
            'range'=>array('px'=>array('min'=>0,'max'=>60)),
            'default'=>array('size'=>28),
            'selectors'=>array('{{WRAPPER}} .wpst-ew-hero-slider'=>'border-radius:{{SIZE}}{{UNIT}}')
        ));

        $this->add_control('overlay',array(
            'label'=>'Overlay Rengi',
            'type'=>\Elementor\Controls_Manager::COLOR,
            'default'=>'rgba(2,6,23,.72)',
            'selectors'=>array('{{WRAPPER}} .wpst-ew-hero-overlay'=>'background:linear-gradient(90deg,{{VALUE}},rgba(2,6,23,.20))!important')
        ));

        $this->add_control('title_color',array(
            'label'=>'Başlık Rengi',
            'type'=>\Elementor\Controls_Manager::COLOR,
            'default'=>'#ffffff',
            'selectors'=>array('{{WRAPPER}} .wpst-ew-hero-slide h2'=>'color:{{VALUE}}!important')
        ));

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            array(
                'name'=>'title_typography',
                'label'=>'Başlık Tipografi',
                'selector'=>'{{WRAPPER}} .wpst-ew-hero-slide h2'
            )
        );

        $this->add_control('text_color',array(
            'label'=>'Açıklama Rengi',
            'type'=>\Elementor\Controls_Manager::COLOR,
            'default'=>'#dbeafe',
            'selectors'=>array('{{WRAPPER}} .wpst-ew-hero-slide p'=>'color:{{VALUE}}!important')
        ));

        $this->add_control('button_bg',array(
            'label'=>'Buton Arka Plan',
            'type'=>\Elementor\Controls_Manager::COLOR,
            'default'=>'#2563eb',
            'selectors'=>array('{{WRAPPER}}'=>'--wpst-hero-btn-bg:{{VALUE}};', '{{WRAPPER}} .wpst-ew-hero-slide a'=>'background:{{VALUE}}!important')
        ));

        $this->add_control('button_color',array(
            'label'=>'Buton Yazı',
            'type'=>\Elementor\Controls_Manager::COLOR,
            'default'=>'#ffffff',
            'selectors'=>array('{{WRAPPER}}'=>'--wpst-hero-btn-color:{{VALUE}}!important;', '{{WRAPPER}} .wpst-ew-hero-slide a'=>'color:{{VALUE}}!important')
        ));

        $this->add_responsive_control('content_width',array(
            'label'=>'İçerik Genişliği',
            'type'=>\Elementor\Controls_Manager::SLIDER,
            'range'=>array('px'=>array('min'=>360,'max'=>1000)),
            'default'=>array('size'=>760),
            'selectors'=>array('{{WRAPPER}} .wpst-ew-hero-slide-inner'=>'max-width:{{SIZE}}{{UNIT}}')
        ));

        $this->add_responsive_control('padding',array(
            'label'=>'İç Boşluk',
            'type'=>\Elementor\Controls_Manager::DIMENSIONS,
            'size_units'=>array('px','%'),
            'default'=>array('top'=>110,'right'=>70,'bottom'=>110,'left'=>70,'unit'=>'px','isLinked'=>false),
            'selectors'=>array('{{WRAPPER}} .wpst-ew-hero-slide-inner'=>'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}')
        ));

        
        $this->end_controls_section();

        $this->start_controls_section('style_controls',array(
            'label'=>'Kontroller',
            'tab'=>\Elementor\Controls_Manager::TAB_STYLE
        ));

        $this->add_control('arrow_bg',array(
            'label'=>'Ok Arka Plan',
            'type'=>\Elementor\Controls_Manager::COLOR,
            'default'=>'rgba(255,255,255,.10)',
            'selectors'=>array(
                '{{WRAPPER}} .wpst-ew-hero-slider'=>'--wpst-slider-control-bg:{{VALUE}}'
            )
        ));

        $this->add_control('arrow_hover_bg',array(
            'label'=>'Ok Hover Arka Plan',
            'type'=>\Elementor\Controls_Manager::COLOR,
            'default'=>'rgba(255,255,255,.18)',
            'selectors'=>array(
                '{{WRAPPER}} .wpst-ew-hero-slider'=>'--wpst-slider-control-hover:{{VALUE}}'
            )
        ));

        $this->add_control('arrow_color',array(
            'label'=>'Ok Rengi',
            'type'=>\Elementor\Controls_Manager::COLOR,
            'default'=>'#ffffff',
            'selectors'=>array(
                '{{WRAPPER}} .wpst-ew-hero-slider'=>'--wpst-slider-arrow-color:{{VALUE}}!important;--wpst-control-color:{{VALUE}}!important;',
                '{{WRAPPER}} .wpst-ew-slider-prev, {{WRAPPER}} .wpst-ew-slider-next'=>'color:{{VALUE}}!important;'
            )
        ));

        $this->add_control('arrow_hover_color',array(
            'label'=>'Ok Hover Rengi',
            'type'=>\Elementor\Controls_Manager::COLOR,
            'default'=>'#ffffff',
            'selectors'=>array(
                '{{WRAPPER}} .wpst-ew-hero-slider'=>'--wpst-slider-arrow-hover-color:{{VALUE}}!important;',
                '{{WRAPPER}} .wpst-ew-slider-prev:hover, {{WRAPPER}} .wpst-ew-slider-next:hover'=>'color:{{VALUE}}!important;'
            )
        ));

        $this->add_control('dot_color',array(
            'label'=>'Nokta Rengi',
            'type'=>\Elementor\Controls_Manager::COLOR,
            'default'=>'rgba(255,255,255,.36)',
            'selectors'=>array(
                '{{WRAPPER}} .wpst-ew-hero-slider'=>'--wpst-slider-dot:{{VALUE}}'
            )
        ));

        $this->add_control('dot_active_color',array(
            'label'=>'Aktif Nokta Rengi',
            'type'=>\Elementor\Controls_Manager::COLOR,
            'default'=>'#ffffff',
            'selectors'=>array(
                '{{WRAPPER}} .wpst-ew-hero-slider'=>'--wpst-slider-dot-active:{{VALUE}}'
            )
        ));

        $this->add_control('button_radius',array(
            'label'=>'Buton Köşe',
            'type'=>\Elementor\Controls_Manager::SLIDER,
            'default'=>array('size'=>14),
            'range'=>array('px'=>array('min'=>0,'max'=>40)),
            'selectors'=>array(
                '{{WRAPPER}} .wpst-ew-hero-slide a'=>'border-radius:{{SIZE}}{{UNIT}}'
            )
        ));

        $this->add_responsive_control('arrow_size',array(
            'label'=>'Ok Boyutu',
            'type'=>\Elementor\Controls_Manager::SLIDER,
            'default'=>array('size'=>44),
            'range'=>array('px'=>array('min'=>34,'max'=>64)),
            'selectors'=>array(
                '{{WRAPPER}} .wpst-ew-slider-prev, {{WRAPPER}} .wpst-ew-slider-next'=>'width:{{SIZE}}{{UNIT}}!important;height:{{SIZE}}{{UNIT}}!important'
            )
        ));

        $this->end_controls_section();


    
        $this->hero_button_style_controls();
        $this->standard_responsive_controls();
    }

    protected function render(){
        $s = $this->get_settings_for_display();
        $delay = ! empty($s['delay']) ? absint($s['delay']) : 5000;

        echo '<section class="wpst-ew-hero-slider"'
            .' data-autoplay="'.esc_attr($s['autoplay']).'"'
            .' data-delay="'.esc_attr($delay).'"'
            .' data-pause-hover="'.esc_attr($s['pause_hover']).'"'
            .' data-touch-swipe="'.esc_attr($s['touch_swipe']??'yes').'"'
            .' data-mouse-drag="'.esc_attr($s['mouse_drag']??'yes').'">';

        echo '<div class="wpst-ew-slider-track">';

        $n = 0;
        foreach((array)$s['items'] as $i){
            $img = !empty($i['image']['url'])
                ? ' style="background-image:url(\''.esc_url($i['image']['url']).'\')"'
                : '';

            echo '<article class="wpst-ew-hero-slide'.($n===0?' is-active':'').'"'.$img.'>';
            echo '<div class="wpst-ew-hero-overlay"></div>';
            echo '<div class="wpst-ew-hero-slide-inner">';
            echo '<small>'.esc_html($i['eyebrow']).'</small>';
            echo '<h2>'.wp_kses_post($i['title']).'</h2>';
            echo '<p>'.esc_html($i['text']).'</p>';
            $link = !empty($i['url']) && is_array($i['url']) ? $i['url'] : array();
            $href = !empty($link['url']) ? $link['url'] : '#';
            $target = !empty($link['is_external']) ? ' target="_blank"' : '';
            $rels = array();
            if ( ! empty($link['nofollow']) ) $rels[] = 'nofollow';
            if ( ! empty($link['is_external']) ) $rels[] = 'noopener';
            $rel = $rels ? ' rel="'.esc_attr(implode(' ',$rels)).'"' : '';
            echo '<a href="'.esc_url($href).'"'.$target.$rel.'><span>'.esc_html($i['button']).'</span><i class="wpst-slider-action-icon">'.(class_exists('WPST_Icon_Library')?WPST_Icon_Library::svg(!empty($i['button_icon'])?$i['button_icon']:'arrow-right',array('size'=>15)):'→').'</i></a>';
            echo '</div></article>';
            $n++;
        }

        echo '</div>';

        if ( 'yes' === $s['show_arrows'] ) {
            echo '<button class="wpst-ew-slider-prev wpst-slider-nav-button" type="button" aria-label="Önceki">'.(class_exists('WPST_Icon_Library')?WPST_Icon_Library::svg('arrow-left',array('size'=>17)):'‹').'</button>';
            echo '<button class="wpst-ew-slider-next wpst-slider-nav-button" type="button" aria-label="Sonraki">'.(class_exists('WPST_Icon_Library')?WPST_Icon_Library::svg('arrow-right',array('size'=>17)):'›').'</button>';
        }

        if ( 'yes' === $s['show_dots'] ) {
            echo '<div class="wpst-ew-slider-dots"></div>';
        }

        if ( 'yes' === ($s['show_counter']??'yes') ) {
            echo '<div class="wpst-ew-slider-counter" aria-live="polite"><span>01</span><em>/</em><b>'.str_pad((string)max(1,$n),2,'0',STR_PAD_LEFT).'</b></div>';
        }

        if ( 'yes' === ($s['show_progress']??'yes') ) {
            echo '<div class="wpst-ew-slider-progress" aria-hidden="true"><span></span></div>';
        }

        echo '</section>';
    }
}
