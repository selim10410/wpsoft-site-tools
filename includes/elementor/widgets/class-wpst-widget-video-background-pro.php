<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class WPST_Widget_Video_Background_Pro extends WPST_Elementor_Widget_Base {

    public function get_name(){ return 'wpsoft-video-background-pro'; }
    public function get_title(){ return 'WPSoft · Video Background Pro'; }
    public function get_icon(){ return 'eicon-video-camera'; }
    public function get_keywords(){ return array('video','background','youtube','hero','media','arka plan'); }
    public function get_categories(){ return array('wpsoft-media','wpsoft-creative','wpsoft'); }

    protected function register_controls(){

        $this->start_controls_section('video_content',array('label'=>'Video Arka Plan'));
        $this->wpst_signature_preset_control();

        $this->add_control('source_type',array(
            'label'=>'Video Kaynağı',
            'type'=>\Elementor\Controls_Manager::SELECT,
            'default'=>'self',
            'options'=>array(
                'self'=>'MP4 / WebM',
                'youtube'=>'YouTube'
            )
        ));

        $this->add_control('mp4_url',array(
            'label'=>'MP4 Video',
            'type'=>\Elementor\Controls_Manager::URL,
            'placeholder'=>'https://site.com/video.mp4',
            'condition'=>array('source_type'=>'self')
        ));

        $this->add_control('webm_url',array(
            'label'=>'WebM Video',
            'type'=>\Elementor\Controls_Manager::URL,
            'placeholder'=>'https://site.com/video.webm',
            'condition'=>array('source_type'=>'self')
        ));

        $this->add_control('youtube_url',array(
            'label'=>'YouTube URL',
            'type'=>\Elementor\Controls_Manager::URL,
            'placeholder'=>'https://youtube.com/watch?v=...',
            'condition'=>array('source_type'=>'youtube')
        ));

        $this->add_control('poster',array(
            'label'=>'Poster / Mobil Görsel',
            'type'=>\Elementor\Controls_Manager::MEDIA,
            'description'=>'Video yüklenirken ve mobilde video kapalıysa gösterilir.'
        ));

        $this->add_control('autoplay',array(
            'label'=>'Otomatik Oynat',
            'type'=>\Elementor\Controls_Manager::SWITCHER,
            'return_value'=>'yes',
            'default'=>'yes'
        ));

        $this->add_control('loop',array(
            'label'=>'Döngü',
            'type'=>\Elementor\Controls_Manager::SWITCHER,
            'return_value'=>'yes',
            'default'=>'yes'
        ));

        $this->add_control('muted',array(
            'label'=>'Sessiz',
            'type'=>\Elementor\Controls_Manager::SWITCHER,
            'return_value'=>'yes',
            'default'=>'yes',
            'description'=>'Tarayıcıların otomatik oynatma desteği için sessiz kullanım önerilir.'
        ));

        $this->add_control('mobile_video',array(
            'label'=>'Mobilde Video Oynat',
            'type'=>\Elementor\Controls_Manager::SWITCHER,
            'return_value'=>'yes',
            'default'=>'',
            'description'=>'Kapalıysa mobilde poster görseli kullanılır.'
        ));

        $this->add_control('pause_offscreen',array(
            'label'=>'Ekran Dışında Videoyu Duraklat',
            'type'=>\Elementor\Controls_Manager::SWITCHER,
            'return_value'=>'yes',
            'default'=>'yes'
        ));

        $this->end_controls_section();


        $this->start_controls_section('content',array('label'=>'İçerik Katmanı'));

        $this->add_control('show_content',array(
            'label'=>'İçerik Göster',
            'type'=>\Elementor\Controls_Manager::SWITCHER,
            'return_value'=>'yes',
            'default'=>'yes'
        ));

        $this->add_control('eyebrow',array(
            'label'=>'Üst Etiket',
            'type'=>\Elementor\Controls_Manager::TEXT,
            'default'=>'WPSOFT EXPERIENCE',
            'condition'=>array('show_content'=>'yes')
        ));

        $this->add_control('title',array(
            'label'=>'Başlık',
            'type'=>\Elementor\Controls_Manager::TEXTAREA,
            'default'=>'Hareketli ve güçlü bir dijital deneyim',
            'condition'=>array('show_content'=>'yes')
        ));

        $this->add_control('description',array(
            'label'=>'Açıklama',
            'type'=>\Elementor\Controls_Manager::TEXTAREA,
            'default'=>'Arka plan videosunu içerik, CTA veya görsel atmosfer alanı olarak kullanın.',
            'condition'=>array('show_content'=>'yes')
        ));

        $this->link_controls('button','Buton');

        $this->add_control('content_position',array(
            'label'=>'İçerik Konumu',
            'type'=>\Elementor\Controls_Manager::SELECT,
            'default'=>'center-left',
            'options'=>array(
                'top-left'=>'Üst Sol',
                'top-center'=>'Üst Orta',
                'center-left'=>'Orta Sol',
                'center'=>'Orta',
                'center-right'=>'Orta Sağ',
                'bottom-left'=>'Alt Sol',
                'bottom-center'=>'Alt Orta'
            ),
            'prefix_class'=>'wpst-video-bg-pos-',
            'condition'=>array('show_content'=>'yes')
        ));

        $this->add_control('content_style',array(
            'label'=>'İçerik Yüzeyi',
            'type'=>\Elementor\Controls_Manager::SELECT,
            'default'=>'none',
            'options'=>array(
                'none'=>'Yüzeysiz',
                'glass'=>'Glass Panel',
                'dark'=>'Dark Panel',
                'light'=>'Light Panel'
            ),
            'prefix_class'=>'wpst-video-bg-content-',
            'condition'=>array('show_content'=>'yes')
        ));

        $this->end_controls_section();


        $this->start_controls_section('layout_style',array(
            'label'=>'Alan & Video',
            'tab'=>\Elementor\Controls_Manager::TAB_STYLE
        ));

        $this->add_responsive_control('height',array(
            'label'=>'Minimum Yükseklik',
            'type'=>\Elementor\Controls_Manager::SLIDER,
            'size_units'=>array('px','vh'),
            'range'=>array(
                'px'=>array('min'=>220,'max'=>1100),
                'vh'=>array('min'=>30,'max'=>100)
            ),
            'default'=>array('unit'=>'px','size'=>620),
            'tablet_default'=>array('unit'=>'px','size'=>520),
            'mobile_default'=>array('unit'=>'px','size'=>440),
            'selectors'=>array('{{WRAPPER}} .wpst-video-background-pro'=>'min-height:{{SIZE}}{{UNIT}};')
        ));

        $this->add_control('object_fit',array(
            'label'=>'Video Yerleşimi',
            'type'=>\Elementor\Controls_Manager::SELECT,
            'default'=>'cover',
            'options'=>array('cover'=>'Cover','contain'=>'Contain'),
            'selectors'=>array('{{WRAPPER}} .wpst-video-bg-media video, {{WRAPPER}} .wpst-video-bg-poster'=>'object-fit:{{VALUE}};')
        ));

        $this->add_control('object_position',array(
            'label'=>'Video Odak',
            'type'=>\Elementor\Controls_Manager::SELECT,
            'default'=>'center center',
            'options'=>array(
                'center center'=>'Orta',
                'center top'=>'Üst',
                'center bottom'=>'Alt',
                'left center'=>'Sol',
                'right center'=>'Sağ'
            ),
            'selectors'=>array('{{WRAPPER}} .wpst-video-bg-media video, {{WRAPPER}} .wpst-video-bg-poster'=>'object-position:{{VALUE}};')
        ));

        $this->add_responsive_control('radius',array(
            'label'=>'Köşe',
            'type'=>\Elementor\Controls_Manager::SLIDER,
            'range'=>array('px'=>array('min'=>0,'max'=>80)),
            'selectors'=>array('{{WRAPPER}} .wpst-video-background-pro'=>'border-radius:{{SIZE}}px;')
        ));

        $this->add_control('video_scale',array(
            'label'=>'Video Yakınlaştırma',
            'type'=>\Elementor\Controls_Manager::SLIDER,
            'range'=>array(''=>array('min'=>1,'max'=>1.4,'step'=>.01)),
            'default'=>array('size'=>1.02),
            'selectors'=>array('{{WRAPPER}} .wpst-video-bg-media'=>'--wpst-video-bg-scale:{{SIZE}};')
        ));

        $this->end_controls_section();


        $this->start_controls_section('overlay_style',array(
            'label'=>'Overlay',
            'tab'=>\Elementor\Controls_Manager::TAB_STYLE
        ));

        $this->add_control('overlay_type',array(
            'label'=>'Overlay Türü',
            'type'=>\Elementor\Controls_Manager::SELECT,
            'default'=>'gradient',
            'options'=>array(
                'none'=>'Yok',
                'solid'=>'Tek Renk',
                'gradient'=>'Gradient',
                'vignette'=>'Vignette'
            ),
            'prefix_class'=>'wpst-video-bg-overlay-'
        ));

        $this->add_control('overlay_color',array(
            'label'=>'Overlay Rengi',
            'type'=>\Elementor\Controls_Manager::COLOR,
            'default'=>'rgba(2,6,23,.56)',
            'selectors'=>array('{{WRAPPER}} .wpst-video-bg-overlay'=>'--wpst-video-bg-overlay:{{VALUE}};')
        ));

        $this->add_control('overlay_secondary',array(
            'label'=>'Gradient İkinci Renk',
            'type'=>\Elementor\Controls_Manager::COLOR,
            'default'=>'rgba(2,6,23,.10)',
            'selectors'=>array('{{WRAPPER}} .wpst-video-bg-overlay'=>'--wpst-video-bg-overlay-2:{{VALUE}};'),
            'condition'=>array('overlay_type'=>'gradient')
        ));

        $this->end_controls_section();


        $this->start_controls_section('content_style_controls',array(
            'label'=>'İçerik Biçimi',
            'tab'=>\Elementor\Controls_Manager::TAB_STYLE,
            'condition'=>array('show_content'=>'yes')
        ));

        $this->add_control('title_color',array(
            'label'=>'Başlık Rengi',
            'type'=>\Elementor\Controls_Manager::COLOR,
            'default'=>'#ffffff',
            'selectors'=>array('{{WRAPPER}} .wpst-video-bg-copy h2'=>'color:{{VALUE}}!important;')
        ));

        $this->add_control('text_color',array(
            'label'=>'Açıklama Rengi',
            'type'=>\Elementor\Controls_Manager::COLOR,
            'default'=>'rgba(255,255,255,.78)',
            'selectors'=>array('{{WRAPPER}} .wpst-video-bg-copy p'=>'color:{{VALUE}}!important;')
        ));

        $this->add_control('eyebrow_color',array(
            'label'=>'Etiket Rengi',
            'type'=>\Elementor\Controls_Manager::COLOR,
            'default'=>'#bfdbfe',
            'selectors'=>array('{{WRAPPER}} .wpst-video-bg-copy small'=>'color:{{VALUE}}!important;')
        ));

        $this->add_responsive_control('content_align',array(
            'label'=>'İçerik Hizalama',
            'type'=>\Elementor\Controls_Manager::CHOOSE,
            'options'=>array(
                'flex-start'=>array('title'=>'Sol','icon'=>'eicon-text-align-left'),
                'center'=>array('title'=>'Orta','icon'=>'eicon-text-align-center'),
                'flex-end'=>array('title'=>'Sağ','icon'=>'eicon-text-align-right')
            ),
            'default'=>'',
            'prefix_class'=>'wpst-video-copy-align-',
            'selectors'=>array(
                '{{WRAPPER}} .wpst-video-bg-copy'=>'--wpst-video-copy-align:{{VALUE}};'
            ),
            'description'=>'Başlık, açıklama, etiket ve butonu aynı eksende birlikte hizalar.'
        ));

        $this->add_responsive_control('content_width',array(
            'label'=>'İçerik Genişliği',
            'type'=>\Elementor\Controls_Manager::SLIDER,
            'range'=>array('px'=>array('min'=>260,'max'=>1000)),
            'default'=>array('size'=>700,'unit'=>'px'),
            'selectors'=>array('{{WRAPPER}} .wpst-video-bg-copy'=>'max-width:{{SIZE}}{{UNIT}};')
        ));

        $this->add_responsive_control('content_padding',array(
            'label'=>'İçerik İç Boşluk',
            'type'=>\Elementor\Controls_Manager::DIMENSIONS,
            'size_units'=>array('px'),
            'selectors'=>array('{{WRAPPER}} .wpst-video-bg-copy'=>'padding:{{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;')
        ));

        $this->end_controls_section();

        $this->standard_responsive_controls();
    }

    private function youtube_id($url){
        $url=(string)$url;
        if(preg_match('~(?:youtu\.be/|youtube(?:-nocookie)?\.com/(?:watch\?(?:.*&)?v=|shorts/|embed/|live/))([A-Za-z0-9_-]{6,})~i',$url,$m)){
            return $m[1];
        }
        return '';
    }

    private function youtube_embed($url,$autoplay=true,$loop=true,$muted=true){
        $id=$this->youtube_id($url);
        if(!$id) return '';
        $params=array(
            'autoplay'=>$autoplay?'1':'0',
            'mute'=>$muted?'1':'0',
            'controls'=>'0',
            'showinfo'=>'0',
            'rel'=>'0',
            'modestbranding'=>'1',
            'playsinline'=>'1',
            'iv_load_policy'=>'3',
            'disablekb'=>'1'
        );
        if($loop){
            $params['loop']='1';
            $params['playlist']=$id;
        }
        return 'https://www.youtube.com/embed/'.rawurlencode($id).'?'.http_build_query($params);
    }

    protected function render(){
        $s=$this->get_settings_for_display();

        $source=in_array($s['source_type'],array('self','youtube'),true)?$s['source_type']:'self';
        $poster=!empty($s['poster']['url'])?$s['poster']['url']:'';
        $autoplay='yes'===$s['autoplay'];
        $loop='yes'===$s['loop'];
        $muted='yes'===$s['muted'];
        $mobile='yes'===$s['mobile_video'];
        $pause='yes'===$s['pause_offscreen'];

        echo '<section class="wpst-video-background-pro" data-wpst-video-background data-mobile-video="'.($mobile?'1':'0').'" data-pause-offscreen="'.($pause?'1':'0').'">';
        echo '<div class="wpst-video-bg-media">';

        if($poster){
            echo '<img class="wpst-video-bg-poster" src="'.esc_url($poster).'" alt="" loading="lazy" decoding="async">';
        }

        if('self'===$source){
            $mp4=!empty($s['mp4_url']['url'])?$s['mp4_url']['url']:'';
            $webm=!empty($s['webm_url']['url'])?$s['webm_url']['url']:'';
            if($mp4||$webm){
                echo '<video '.($autoplay?'autoplay ':'').($muted?'muted ':'').($loop?'loop ':'').'playsinline preload="metadata"'.($poster?' poster="'.esc_url($poster).'"':'').'>';
                if($webm) echo '<source src="'.esc_url($webm).'" type="video/webm">';
                if($mp4) echo '<source src="'.esc_url($mp4).'" type="video/mp4">';
                echo '</video>';
            }
        }else{
            $yt=!empty($s['youtube_url']['url'])?$s['youtube_url']['url']:'';
            $embed=$this->youtube_embed($yt,$autoplay,$loop,$muted);
            if($embed){
                echo '<iframe class="wpst-video-bg-youtube" src="'.esc_url($embed).'" title="Background video" frameborder="0" allow="autoplay; encrypted-media; picture-in-picture" tabindex="-1" aria-hidden="true"></iframe>';
            }
        }

        echo '</div><div class="wpst-video-bg-overlay"></div>';

        if('yes'===$s['show_content']){
            echo '<div class="wpst-video-bg-copy">';
            if(trim((string)$s['eyebrow'])!=='') echo '<small>'.esc_html($s['eyebrow']).'</small>';
            if(trim((string)$s['title'])!=='') echo '<h2>'.wp_kses_post($s['title']).'</h2>';
            if(trim((string)$s['description'])!=='') echo '<p>'.esc_html($s['description']).'</p>';
            if(!empty($s['button_text'])){
                echo '<a'.$this->render_link_attrs($s['button_url']).'>'.esc_html($s['button_text']).'<span class="wpst-native-arrow">'.(class_exists('WPST_Icon_Library')?WPST_Icon_Library::svg('arrow-up-right',array('size'=>15)):'↗').'</span></a>';
            }
            echo '</div>';
        }

        echo '</section>';
    }
}
