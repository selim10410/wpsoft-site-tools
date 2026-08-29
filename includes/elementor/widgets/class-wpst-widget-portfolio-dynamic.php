<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Portfolio-only dynamic widgets.
 *
 * These widgets intentionally do not inherit the Blog dynamic widget context.
 * Elementor's preview iframe can therefore resolve a wpst_portfolio item
 * directly without falling back to a normal blog post.
 */
abstract class WPST_Portfolio_Dynamic_Widget_Base extends WPST_Elementor_Widget_Base {
    protected function portfolio_id() {
        $id = get_queried_object_id();
        if ( $id && 'wpst_portfolio' === get_post_type( $id ) ) return (int) $id;

        $id = get_the_ID();
        if ( $id && 'wpst_portfolio' === get_post_type( $id ) ) return (int) $id;

        if ( isset( $_GET['elementor-preview'] ) ) {
            $preview_id = absint( $_GET['elementor-preview'] );
            if ( $preview_id && 'wpst_portfolio' === get_post_type( $preview_id ) ) return $preview_id;
        }

        if ( isset( $_GET['post'] ) ) {
            $post_id = absint( $_GET['post'] );
            if ( $post_id && 'wpst_portfolio' === get_post_type( $post_id ) ) return $post_id;
        }

        return 0;
    }

    public function get_categories(){ return array( 'wpsoft-dynamic', 'wpsoft' ); }
}

class WPST_Widget_Portfolio_Title extends WPST_Portfolio_Dynamic_Widget_Base {
    public function get_name(){ return 'wpsoft-portfolio-title'; }
    public function get_title(){ return 'WPSoft · Portföy Başlığı'; }
    public function get_icon(){ return 'eicon-post-title'; }

    protected function register_controls(){
        $this->start_controls_section( 'content', array( 'label' => 'Başlık' ) );
        $this->add_control( 'preview_title', array(
            'label' => 'Editör Önizleme Başlığı',
            'type' => \Elementor\Controls_Manager::TEXTAREA,
            'default' => 'Örnek Portföy Projesi',
        ) );
        $this->add_control( 'tag', array(
            'label' => 'HTML Etiketi',
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'h1',
            'options' => array( 'h1'=>'H1', 'h2'=>'H2', 'h3'=>'H3', 'div'=>'DIV' ),
        ) );
        $this->add_responsive_control( 'align', array(
            'label' => 'Hizalama',
            'type' => \Elementor\Controls_Manager::CHOOSE,
            'default' => 'left',
            'options' => array(
                'left' => array( 'title'=>'Sol', 'icon'=>'eicon-text-align-left' ),
                'center' => array( 'title'=>'Orta', 'icon'=>'eicon-text-align-center' ),
                'right' => array( 'title'=>'Sağ', 'icon'=>'eicon-text-align-right' ),
            ),
            'selectors' => array( '{{WRAPPER}} .wpst-portfolio-dynamic-title' => 'text-align:{{VALUE}};' ),
        ) );
        $this->end_controls_section();
        $this->standard_responsive_controls();
    }

    protected function render(){
        $s = $this->get_settings_for_display();
        $id = $this->portfolio_id();
        $title = $id ? get_the_title( $id ) : $s['preview_title'];
        $tag = in_array( $s['tag'], array( 'h1','h2','h3','div' ), true ) ? $s['tag'] : 'h1';
        echo '<' . $tag . ' class="wpst-portfolio-dynamic-title">' . esc_html( $title ) . '</' . $tag . '>';
    }
}

class WPST_Widget_Portfolio_Excerpt extends WPST_Portfolio_Dynamic_Widget_Base {
    public function get_name(){ return 'wpsoft-portfolio-excerpt'; }
    public function get_title(){ return 'WPSoft · Portföy Özeti'; }
    public function get_icon(){ return 'eicon-text'; }

    protected function register_controls(){
        $this->start_controls_section( 'content', array( 'label' => 'Özet' ) );
        $this->add_control( 'preview_excerpt', array(
            'label' => 'Editör Önizleme Özeti',
            'type' => \Elementor\Controls_Manager::TEXTAREA,
            'default' => 'Projenin amacı, yaklaşımı ve ortaya çıkan sonucu kısa ve etkili şekilde anlatın.',
        ) );
        $this->end_controls_section();
        $this->standard_responsive_controls();
    }

    protected function render(){
        $s = $this->get_settings_for_display();
        $id = $this->portfolio_id();
        $text = $id ? get_the_excerpt( $id ) : $s['preview_excerpt'];
        echo '<div class="wpst-portfolio-dynamic-excerpt">' . esc_html( $text ) . '</div>';
    }
}

class WPST_Widget_Portfolio_Image extends WPST_Portfolio_Dynamic_Widget_Base {
    public function get_name(){ return 'wpsoft-portfolio-image'; }
    public function get_title(){ return 'WPSoft · Portföy Görseli'; }
    public function get_icon(){ return 'eicon-featured-image'; }

    protected function register_controls(){
        $this->start_controls_section( 'content', array( 'label' => 'Görsel' ) );
        $this->add_control( 'size', array(
            'label' => 'Görsel Boyutu',
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'full',
            'options' => array( 'medium'=>'Orta', 'large'=>'Büyük', 'full'=>'Tam Boyut' ),
        ) );
        $this->add_control( 'placeholder_text', array(
            'label' => 'Editör Önizleme Yazısı',
            'type' => \Elementor\Controls_Manager::TEXT,
            'default' => 'Proje Görseli',
        ) );
        $this->end_controls_section();

        $this->start_controls_section( 'media_style', array(
            'label' => 'Görsel Biçimi',
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ) );
        $this->add_responsive_control( 'height', array(
            'label' => 'Yükseklik',
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => array( 'px','vh' ),
            'range' => array(
                'px' => array( 'min'=>180, 'max'=>1000 ),
                'vh' => array( 'min'=>20, 'max'=>90 ),
            ),
            'selectors' => array( '{{WRAPPER}} .wpst-portfolio-dynamic-image img' => 'height:{{SIZE}}{{UNIT}};' ),
        ) );
        $this->add_responsive_control( 'radius', array(
            'label' => 'Köşe',
            'type' => \Elementor\Controls_Manager::SLIDER,
            'range' => array( 'px' => array( 'min'=>0, 'max'=>60 ) ),
            'default' => array( 'size'=>24, 'unit'=>'px' ),
            'selectors' => array(
                '{{WRAPPER}} .wpst-portfolio-dynamic-image img,{{WRAPPER}} .wpst-portfolio-dynamic-image-placeholder' => 'border-radius:{{SIZE}}px;',
            ),
        ) );
        $this->end_controls_section();
        $this->standard_responsive_controls();
    }

    protected function render(){
        $s = $this->get_settings_for_display();
        $id = $this->portfolio_id();
        echo '<figure class="wpst-portfolio-dynamic-image">';
        if ( $id && has_post_thumbnail( $id ) ) {
            echo get_the_post_thumbnail( $id, $s['size'], array( 'loading'=>'eager', 'decoding'=>'async' ) );
        } else {
            echo '<div class="wpst-portfolio-dynamic-image-placeholder"><span>' . esc_html( $s['placeholder_text'] ) . '</span></div>';
        }
        echo '</figure>';
    }
}

class WPST_Widget_Portfolio_Terms extends WPST_Portfolio_Dynamic_Widget_Base {
    public function get_name(){ return 'wpsoft-portfolio-terms'; }
    public function get_title(){ return 'WPSoft · Portföy Kategorileri'; }
    public function get_icon(){ return 'eicon-tags'; }

    protected function register_controls(){
        $this->start_controls_section( 'content', array( 'label' => 'Kategoriler' ) );
        $this->add_control( 'preview_terms', array(
            'label' => 'Editör Önizleme Kategorileri',
            'type' => \Elementor\Controls_Manager::TEXT,
            'default' => 'Kurumsal, Web Tasarım',
        ) );
        $this->end_controls_section();
        $this->standard_responsive_controls();
    }

    protected function render(){
        $s = $this->get_settings_for_display();
        $id = $this->portfolio_id();
        $terms = $id && taxonomy_exists( 'wpst_portfolio_cat' )
            ? wp_get_post_terms( $id, 'wpst_portfolio_cat' )
            : array();

        echo '<div class="wpst-portfolio-dynamic-terms">';
        if ( $terms && ! is_wp_error( $terms ) ) {
            foreach ( $terms as $term ) {
                $link = get_term_link( $term );
                if ( is_wp_error( $link ) ) echo '<span>' . esc_html( $term->name ) . '</span>';
                else echo '<a href="' . esc_url( $link ) . '">' . esc_html( $term->name ) . '</a>';
            }
        } else {
            foreach ( array_filter( array_map( 'trim', explode( ',', $s['preview_terms'] ) ) ) as $term ) {
                echo '<span>' . esc_html( $term ) . '</span>';
            }
        }
        echo '</div>';
    }
}
