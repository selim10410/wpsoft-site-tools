<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class WPST_Widget_Video_Gallery_Pro extends WPST_Elementor_Widget_Base {
    public function get_name(){ return 'wpsoft-video-gallery-pro'; }
    public function get_title(){ return 'WPSoft · Video Gallery 2.0'; }
    public function get_keywords(){ return array('video','gallery','youtube','vimeo','media','lightbox','galeri'); }
    public function get_icon(){ return 'eicon-video-playlist'; }
    public function get_categories(){ return array('wpsoft-media','wpsoft'); }
    public function show_in_panel(){ return true; }

    protected function register_controls(){
        $this->start_controls_section('content',array('label'=>'Videolar'));
  $this->wpst_signature_preset_control();
        $rep=new \Elementor\Repeater();
        $rep->add_control('title',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Proje Videosu'));
        $rep->add_control('video_url',array('label'=>'Video URL','type'=>\Elementor\Controls_Manager::URL,'placeholder'=>'https://youtube.com/watch?v=...'));
        $rep->add_control('poster',array('label'=>'Kapak Görseli','type'=>\Elementor\Controls_Manager::MEDIA));
        $rep->add_control('label',array('label'=>'Küçük Etiket','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Video'));
        $rep->add_control('duration',array('label'=>'Süre','type'=>\Elementor\Controls_Manager::TEXT,'placeholder'=>'02:34','default'=>''));
        $this->add_control('items',array(
            'label'=>'Video Listesi','type'=>\Elementor\Controls_Manager::REPEATER,
            'fields'=>$rep->get_controls(),'title_field'=>'{{{ title }}}',
            'default'=>array(
                array('title'=>'Tanıtım Videosu','label'=>'Showreel','duration'=>'01:48'),
                array('title'=>'Proje Hikayesi','label'=>'Case Study','duration'=>'03:12'),
                array('title'=>'Perde Arkası','label'=>'Behind the Scenes','duration'=>'02:26')
            )
        ));
        $this->add_control('layout',array(
            'label'=>'Düzen','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'grid',
            'options'=>array('grid'=>'Grid','featured'=>'İlk Video Büyük','carousel'=>'Carousel','editorial'=>'Editorial','filmstrip'=>'Filmstrip','stack'=>'Story Stack')
        ));
        $this->add_responsive_control('columns',array(
            'label'=>'Sütun','type'=>\Elementor\Controls_Manager::SELECT,
            'default'=>'3','tablet_default'=>'2','mobile_default'=>'1',
            'options'=>array('1'=>'1','2'=>'2','3'=>'3','4'=>'4'),
            'selectors'=>array('{{WRAPPER}} .wpst-video-gallery-pro'=>'--wpst-video-cols:{{VALUE}}')
        ));
        $this->add_responsive_control('gap',array(
            'label'=>'Aralık','type'=>\Elementor\Controls_Manager::SLIDER,
            'range'=>array('px'=>array('min'=>0,'max'=>60)),
            'default'=>array('size'=>18,'unit'=>'px'),
            'selectors'=>array('{{WRAPPER}} .wpst-video-gallery-pro'=>'--wpst-video-gap:{{SIZE}}{{UNIT}}')
        ));
        $this->add_control('aspect',array(
            'label'=>'Video Oranı','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'16-9',
            'options'=>array('1-1'=>'1:1','4-3'=>'4:3','16-9'=>'16:9','21-9'=>'21:9')
        ));
        $this->add_control('open_mode',array(
            'label'=>'Video Açılışı','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'lightbox',
            'options'=>array('lightbox'=>'Lightbox','new_tab'=>'Yeni Sekme')
        ));
        $this->add_control('youtube_auto_poster',array(
            'label'=>'YouTube Kapak Görselini Otomatik Al',
            'type'=>\Elementor\Controls_Manager::SWITCHER,
            'return_value'=>'yes',
            'default'=>'yes',
            'description'=>'Manuel kapak seçilmemişse YouTube videosunun küçük resmi otomatik kullanılır.'
        ));
        $this->add_control('info_position',array(
            'label'=>'Başlık Konumu',
            'type'=>\Elementor\Controls_Manager::SELECT,
            'default'=>'overlay',
            'options'=>array(
                'overlay'=>'Görsel Üzerinde',
                'below'=>'Görsel Altında',
                'floating'=>'Floating Panel'
            ),
            'prefix_class'=>'wpst-video-info-'
        ));
        $this->add_control('play_style',array(
            'label'=>'Play Butonu',
            'type'=>\Elementor\Controls_Manager::SELECT,
            'default'=>'glass',
            'options'=>array(
                'glass'=>'Glass',
                'solid'=>'Solid',
                'minimal'=>'Minimal',
                'outline'=>'Outline'
            ),
            'prefix_class'=>'wpst-video-play-style-'
        ));
        $this->add_control('show_index',array(
            'label'=>'Video Numarasını Göster',
            'type'=>\Elementor\Controls_Manager::SWITCHER,
            'return_value'=>'yes',
            'default'=>'yes'
        ));
        $this->add_control('show_label',array(
            'label'=>'Etiketi Göster',
            'type'=>\Elementor\Controls_Manager::SWITCHER,
            'return_value'=>'yes',
            'default'=>'yes'
        ));
        $this->add_control('show_duration',array(
            'label'=>'Süreyi Göster',
            'type'=>\Elementor\Controls_Manager::SWITCHER,
            'return_value'=>'yes',
            'default'=>'yes'
        ));
        $this->add_control('hover_effect',array(
            'label'=>'Kart Hover',
            'type'=>\Elementor\Controls_Manager::SELECT,
            'default'=>'cinematic',
            'options'=>array(
                'cinematic'=>'Cinematic Zoom',
                'lift'=>'Lift',
                'soft'=>'Soft',
                'none'=>'Yok'
            ),
            'prefix_class'=>'wpst-video-hover-'
        ));
        $this->add_control('style_preset',array(
            'label'=>'Galeri Stili','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'cinematic',
            'options'=>array(
                'cinematic'=>'Cinematic',
                'studio'=>'Studio',
                'glass'=>'Glass',
                'clean'=>'Clean',
                'editorial'=>'Editorial',
                'dark'=>'Dark'
            ),
            'prefix_class'=>'wpst-video-gallery-style-'
        ));
        $this->end_controls_section();

        $this->start_controls_section('style',array('label'=>'Kart Stili','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
        $this->add_control('radius',array(
            'label'=>'Köşe','type'=>\Elementor\Controls_Manager::SLIDER,
            'range'=>array('px'=>array('min'=>0,'max'=>50)),'default'=>array('size'=>20),
            'selectors'=>array('{{WRAPPER}} .wpst-video-card'=>'border-radius:{{SIZE}}px')
        ));
        $this->add_control('play_bg',array(
            'label'=>'Play Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#ffffff',
            'selectors'=>array('{{WRAPPER}} .wpst-video-play'=>'background:{{VALUE}}')
        ));
        $this->add_control('play_color',array(
            'label'=>'Play Rengi','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#111827',
            'selectors'=>array('{{WRAPPER}} .wpst-video-play'=>'color:{{VALUE}}')
        ));
        $this->add_responsive_control('play_size',array(
            'label'=>'Play Buton Boyutu',
            'type'=>\Elementor\Controls_Manager::SLIDER,
            'range'=>array('px'=>array('min'=>36,'max'=>92)),
            'default'=>array('size'=>58,'unit'=>'px'),
            'selectors'=>array('{{WRAPPER}} .wpst-video-play'=>'width:{{SIZE}}{{UNIT}};height:{{SIZE}}{{UNIT}};')
        ));
        $this->add_control('card_bg',array(
            'label'=>'Kart Arka Plan',
            'type'=>\Elementor\Controls_Manager::COLOR,
            'selectors'=>array('{{WRAPPER}} .wpst-video-card'=>'background:{{VALUE}}!important;')
        ));
        $this->add_control('title_color',array(
            'label'=>'Başlık Rengi',
            'type'=>\Elementor\Controls_Manager::COLOR,
            'selectors'=>array('{{WRAPPER}} .wpst-video-info strong'=>'color:{{VALUE}}!important;')
        ));
        $this->add_control('label_color',array(
            'label'=>'Etiket Rengi',
            'type'=>\Elementor\Controls_Manager::COLOR,
            'selectors'=>array('{{WRAPPER}} .wpst-video-info small'=>'color:{{VALUE}}!important;')
        ));
        $this->add_control('scrim_color',array(
            'label'=>'Overlay Rengi',
            'type'=>\Elementor\Controls_Manager::COLOR,
            'default'=>'rgba(2,6,23,.72)',
            'selectors'=>array('{{WRAPPER}} .wpst-video-scrim'=>'--wpst-video-scrim:{{VALUE}};')
        ));
        $this->add_responsive_control('info_padding',array(
            'label'=>'İçerik İç Boşluk',
            'type'=>\Elementor\Controls_Manager::DIMENSIONS,
            'size_units'=>array('px'),
            'selectors'=>array('{{WRAPPER}} .wpst-video-info'=>'padding:{{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;')
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

    private function youtube_poster_url($url){
        $id=$this->youtube_id($url);
        return $id ? 'https://i.ytimg.com/vi/'.rawurlencode($id).'/maxresdefault.jpg' : '';
    }

    private function youtube_poster_fallback_url($url){
        $id=$this->youtube_id($url);
        return $id ? 'https://i.ytimg.com/vi/'.rawurlencode($id).'/hqdefault.jpg' : '';
    }

    private function embed_url($url){
        $url=(string)$url;
        $youtube_id=$this->youtube_id($url);
        if($youtube_id)
            return 'https://www.youtube.com/embed/'.rawurlencode($youtube_id).'?autoplay=1&rel=0';
        if(preg_match('~vimeo\.com/(?:video/)?(\d+)~i',$url,$m))
            return 'https://player.vimeo.com/video/'.rawurlencode($m[1]).'?autoplay=1';
        return esc_url_raw($url);
    }

    protected function render(){
        $s=$this->get_settings_for_display();
        $items=!empty($s['items'])&&is_array($s['items'])?$s['items']:array();
        $layout=in_array($s['layout'],array('grid','featured','carousel','editorial','filmstrip','stack'),true)?$s['layout']:'grid';
        $aspect=in_array($s['aspect'],array('1-1','4-3','16-9','21-9'),true)?$s['aspect']:'16-9';
        echo '<div class="wpst-video-gallery-pro is-'.esc_attr($layout).' ratio-'.esc_attr($aspect).'" data-wpst-video-gallery>';
        if('carousel'===$layout) echo '<div class="wpst-video-gallery-controls"><button type="button" data-video-prev aria-label="Önceki">'.(class_exists('WPST_Icon_Library')?WPST_Icon_Library::svg('arrow-left',array('size'=>16)):'‹').'</button><button type="button" data-video-next aria-label="Sonraki">'.(class_exists('WPST_Icon_Library')?WPST_Icon_Library::svg('arrow-right',array('size'=>16)):'›').'</button></div>';
        foreach($items as $i=>$item){
            $title=isset($item['title'])?$item['title']:'';
            $label=isset($item['label'])?$item['label']:'';
            $duration=isset($item['duration'])?$item['duration']:'';
            $url=!empty($item['video_url']['url'])?$item['video_url']['url']:'';
            $poster=!empty($item['poster']['url'])?$item['poster']['url']:'';
            $poster_fallback='';
            if(!$poster && 'yes'===($s['youtube_auto_poster']??'yes')){
                $poster=$this->youtube_poster_url($url);
                $poster_fallback=$this->youtube_poster_fallback_url($url);
            }
            $embed=$this->embed_url($url);
            $tag=('new_tab'===$s['open_mode'])?'a':'button';
            $attrs=('a'===$tag)?' href="'.esc_url($url).'" target="_blank" rel="noopener"':' type="button" data-wpst-video-open data-video="'.esc_url($embed).'"';
            echo '<'.$tag.' class="wpst-video-card"'.$attrs.' data-video-index="'.esc_attr($i+1).'">';
            echo '<span class="wpst-video-poster">';
            if($poster){
                echo '<img src="'.esc_url($poster).'" alt="'.esc_attr($title).'" loading="lazy" decoding="async"'.($poster_fallback?' data-wpst-video-fallback="'.esc_url($poster_fallback).'"':'').'>';
            } else {
                echo '<span class="wpst-video-placeholder"><i></i><b></b></span>';
            }
            echo '<span class="wpst-video-scrim"></span>';
            if('yes'===($s['show_index']??'yes')) echo '<span class="wpst-video-index">'.esc_html(str_pad((string)($i+1),2,'0',STR_PAD_LEFT)).'</span>';
            if('yes'===($s['show_duration']??'yes') && $duration) echo '<span class="wpst-video-duration">'.esc_html($duration).'</span>';
            echo '<span class="wpst-video-play" aria-hidden="true">'.(class_exists('WPST_Icon_Library')?WPST_Icon_Library::svg('play',array('size'=>22)):'▶').'</span>';
            echo '</span>';
            echo '<span class="wpst-video-info">';
            echo '<span class="wpst-video-meta">';
            if('yes'===($s['show_label']??'yes') && $label) echo '<small>'.esc_html($label).'</small>';
            echo '<i>'.(class_exists('WPST_Icon_Library')?WPST_Icon_Library::svg('arrow-up-right',array('size'=>14)):'↗').'</i>';
            echo '</span>';
            if($title) echo '<strong>'.esc_html($title).'</strong>';
            echo '</span></'.$tag.'>';
        }
        echo '</div>';
    }
}
