<?php
if(!defined('ABSPATH'))exit;

final class WPST_Theme_Compatibility {

    public static function init(){
        add_filter('body_class',array(__CLASS__,'body_classes'));
        add_action('wp_enqueue_scripts',array(__CLASS__,'inline_compat_css'),120);
    }

    public static function detect(){
        $theme=wp_get_theme();
        $slug=strtolower($theme->get_stylesheet());
        $parent=$theme->parent() ? strtolower($theme->parent()->get_stylesheet()) : '';

        $map=array(
            'woodmart'=>'woodmart',
            'hello-elementor'=>'hello',
            'astra'=>'astra',
            'kadence'=>'kadence',
            'generatepress'=>'generatepress',
            'salient'=>'salient'
        );

        foreach($map as $needle=>$name){
            if(false!==strpos($slug,$needle) || false!==strpos($parent,$needle)) return $name;
        }
        return 'generic';
    }

    public static function body_classes($classes){
        $classes[]='wpst-theme-'.sanitize_html_class(self::detect());
        $settings=get_option('wpst_settings',array());
        if(!empty($settings['header_enabled']))$classes[]='wpst-custom-header-active';
        if(!empty($settings['footer_enabled']))$classes[]='wpst-custom-footer-active';
        return $classes;
    }

    public static function selectors(){
        return array(
            'woodmart'=>array(
                'header'=>array('.whb-header','.whb-clone','.main-header'),
                'footer'=>array('.footer-container','footer.footer-container')
            ),
            'hello'=>array(
                'header'=>array('#site-header','.site-header'),
                'footer'=>array('#site-footer','.site-footer')
            ),
            'astra'=>array(
                'header'=>array('#masthead','.site-header','.ast-primary-header-bar'),
                'footer'=>array('#colophon','.site-footer')
            ),
            'kadence'=>array(
                'header'=>array('#masthead','.site-header-wrap'),
                'footer'=>array('#colophon','.site-footer-wrap')
            ),
            'generatepress'=>array(
                'header'=>array('#masthead','.site-header'),
                'footer'=>array('.site-info','.site-footer')
            ),
            'salient'=>array(
                'header'=>array('#header-outer','#header-space'),
                'footer'=>array('#footer-outer')
            ),
            'generic'=>array(
                'header'=>array('#masthead','.site-header','header.site-header'),
                'footer'=>array('#colophon','.site-footer','footer.site-footer')
            )
        );
    }

    public static function current_selectors($type){
        $all=self::selectors();
        $theme=self::detect();
        return isset($all[$theme][$type])?$all[$theme][$type]:$all['generic'][$type];
    }

    public static function inline_compat_css(){
        $settings=get_option('wpst_settings',array());
        $css='';
        $theme=self::detect();

        if(!empty($settings['header_enabled']) && !empty($settings['hide_theme_header'])){
            foreach(self::current_selectors('header') as $sel){
                $css.='body.wpst-theme-'.$theme.' '.$sel.'{display:none!important;}';
            }
        }
        if(!empty($settings['footer_enabled']) && !empty($settings['hide_theme_footer'])){
            foreach(self::current_selectors('footer') as $sel){
                $css.='body.wpst-theme-'.$theme.' '.$sel.'{display:none!important;}';
            }
        }

        // Known collision fixes
        if('woodmart'===$theme)$css.='.wpsoft-site-header{position:relative;z-index:99999}.wpst-mobile-drawer{z-index:100001}';
        if('salient'===$theme)$css.='body.wpst-custom-header-active #header-space{height:0!important}.wpsoft-site-header{z-index:10000}';
        if('astra'===$theme)$css.='.wpsoft-site-header .wpst-q-menu{margin:0}.wpsoft-site-header .wpst-q-menu li{margin:0}';
        if('hello'===$theme)$css.='.wpsoft-site-header,.wpsoft-site-footer{width:100%}';

        if($css){
            wp_register_style('wpst-theme-compat','',array(),WPST_VERSION);
            wp_enqueue_style('wpst-theme-compat');
            wp_add_inline_style('wpst-theme-compat',$css);
        }
    }
}
