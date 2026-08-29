<?php
if ( ! defined( 'ABSPATH' ) ) exit;

final class WPST_Blog_Templates {
    public static function init(){
        add_action('admin_post_wpst_create_blog_template',array(__CLASS__,'create_template'));
        add_filter('the_content',array(__CLASS__,'replace_archive_content'),998);
        add_filter('the_content',array(__CLASS__,'replace_single_content'),999);
        add_filter('body_class',array(__CLASS__,'body_class'));
        add_action('wp_head',array(__CLASS__,'single_title_compat_css'),99);
    }
    public static function templates(){
        return array(
            'editorial'=>array('title'=>'Editorial Clean','desc'=>'Geniş başlık, meta, büyük görsel ve rahat okuma alanı.'),
            'magazine'=>array('title'=>'Modern Magazine','desc'=>'Kategori vurgusu, güçlü başlık ve sosyal paylaşım odaklı.'),
            'minimal'=>array('title'=>'Minimal Reading','desc'=>'İçeriği öne çıkaran sade ve yüksek okunabilirlikli yapı.'),
            'feature'=>array('title'=>'Feature Story','desc'=>'Okuma ilerlemesi ve ilgili yazılarla güçlü editoryal detay.'),
            'dark'=>array('title'=>'Dark Insight','desc'=>'Teknoloji ve kurumsal içerikler için koyu premium detay düzeni.')
        );
    }
    private static function el($type,$settings=array()){return array('id'=>substr(md5(uniqid('',true)),0,7),'elType'=>'widget','widgetType'=>$type,'settings'=>$settings,'elements'=>array());}
    private static function container($elements,$settings=array()){return array('id'=>substr(md5(uniqid('',true)),0,7),'elType'=>'container','settings'=>$settings,'elements'=>$elements,'isInner'=>false);}
    public static function data($key='editorial'){
        $progress=self::el('wpsoft-post-reading-progress',array('position'=>'top','show_percent'=>''));
        $title_left=self::el('wpsoft-post-title',array('tag'=>'h1','align'=>'left'));
        $title_center=self::el('wpsoft-post-title',array('tag'=>'h1','align'=>'center'));
        $meta=self::el('wpsoft-post-meta');
        $terms=self::el('wpsoft-post-terms');
        $excerpt=self::el('wpsoft-post-excerpt');
        $image=self::el('wpsoft-post-image',array('size'=>'full'));
        $content=self::el('wpsoft-post-content');
        $share=self::el('wpsoft-post-share');
        $author=self::el('wpsoft-post-author');
        $related=self::el('wpsoft-related-posts',array('title'=>'İlgili Yazılar','count'=>3,'match_by'=>'category','columns'=>'3','show_image'=>'yes','show_date'=>'yes'));
        $nav=self::el('wpsoft-post-navigation');
        $comments=self::el('wpsoft-post-comments');

        if('magazine'===$key){
            return array(
                self::container(array($terms,$meta),array('content_width'=>'boxed','boxed_width'=>array('size'=>1180,'unit'=>'px'),'padding'=>array('unit'=>'px','top'=>'56','right'=>'24','bottom'=>'18','left'=>'24','isLinked'=>false))),
                self::container(array(
                    self::container(array($title_left,$excerpt),array('content_width'=>'full','width'=>array('unit'=>'%','size'=>48,'sizes'=>array()),'padding'=>array('unit'=>'px','top'=>'12','right'=>'34','bottom'=>'34','left'=>'0','isLinked'=>false))),
                    self::container(array($image),array('content_width'=>'full','width'=>array('unit'=>'%','size'=>52,'sizes'=>array()),'padding'=>array('unit'=>'px','top'=>'0','right'=>'0','bottom'=>'0','left'=>'0','isLinked'=>false),'overflow'=>'hidden'))
                ),array('content_width'=>'boxed','boxed_width'=>array('size'=>1180,'unit'=>'px'),'flex_direction'=>'row','align_items'=>'center','gap'=>array('unit'=>'px','size'=>28,'column'=>28,'row'=>28),'padding'=>array('unit'=>'px','top'=>'10','right'=>'24','bottom'=>'58','left'=>'24','isLinked'=>false))),
                self::container(array($content,$share,$author,$related,$nav,$comments),array('content_width'=>'boxed','boxed_width'=>array('size'=>860,'unit'=>'px'),'padding'=>array('unit'=>'px','top'=>'40','right'=>'24','bottom'=>'92','left'=>'24','isLinked'=>false)))
            );
        }
        if('minimal'===$key){
            return array(
                self::container(array($title_center,$meta),array('content_width'=>'boxed','boxed_width'=>array('size'=>760,'unit'=>'px'),'padding'=>array('unit'=>'px','top'=>'96','right'=>'24','bottom'=>'36','left'=>'24','isLinked'=>false))),
                self::container(array($image),array('content_width'=>'boxed','boxed_width'=>array('size'=>980,'unit'=>'px'),'padding'=>array('unit'=>'px','top'=>'0','right'=>'24','bottom'=>'54','left'=>'24','isLinked'=>false))),
                self::container(array($content,$terms,$author,$nav,$comments),array('content_width'=>'boxed','boxed_width'=>array('size'=>720,'unit'=>'px'),'padding'=>array('unit'=>'px','top'=>'14','right'=>'24','bottom'=>'110','left'=>'24','isLinked'=>false)))
            );
        }
        if('feature'===$key){
            return array(
                self::container(array($progress),array('content_width'=>'full','padding'=>array('unit'=>'px','top'=>'0','right'=>'0','bottom'=>'0','left'=>'0','isLinked'=>false))),
                self::container(array($terms,$title_left,$excerpt,$meta),array('content_width'=>'boxed','boxed_width'=>array('size'=>1040,'unit'=>'px'),'padding'=>array('unit'=>'px','top'=>'82','right'=>'24','bottom'=>'42','left'=>'24','isLinked'=>false))),
                self::container(array($image),array('content_width'=>'full','padding'=>array('unit'=>'px','top'=>'0','right'=>'0','bottom'=>'0','left'=>'0','isLinked'=>false),'overflow'=>'hidden')),
                self::container(array($content,$share,$author),array('content_width'=>'boxed','boxed_width'=>array('size'=>800,'unit'=>'px'),'padding'=>array('unit'=>'px','top'=>'64','right'=>'24','bottom'=>'54','left'=>'24','isLinked'=>false))),
                self::container(array($related,$nav,$comments),array('content_width'=>'boxed','boxed_width'=>array('size'=>1120,'unit'=>'px'),'background_background'=>'classic','background_color'=>'#f8fafc','padding'=>array('unit'=>'px','top'=>'56','right'=>'32','bottom'=>'86','left'=>'32','isLinked'=>false)))
            );
        }
        if('dark'===$key){
            $dark=array('background_background'=>'classic','background_color'=>'#07111f');
            return array(
                self::container(array($progress),array_merge($dark,array('content_width'=>'full','padding'=>array('unit'=>'px','top'=>'0','right'=>'0','bottom'=>'0','left'=>'0','isLinked'=>false)))),
                self::container(array($terms,$title_center,$meta,$excerpt),array_merge($dark,array('content_width'=>'boxed','boxed_width'=>array('size'=>980,'unit'=>'px'),'padding'=>array('unit'=>'px','top'=>'88','right'=>'24','bottom'=>'46','left'=>'24','isLinked'=>false)))),
                self::container(array($image),array_merge($dark,array('content_width'=>'boxed','boxed_width'=>array('size'=>1160,'unit'=>'px'),'padding'=>array('unit'=>'px','top'=>'0','right'=>'24','bottom'=>'56','left'=>'24','isLinked'=>false),'overflow'=>'hidden'))),
                self::container(array($content,$terms,$share,$author),array_merge($dark,array('content_width'=>'boxed','boxed_width'=>array('size'=>800,'unit'=>'px'),'padding'=>array('unit'=>'px','top'=>'34','right'=>'24','bottom'=>'54','left'=>'24','isLinked'=>false)))),
                self::container(array($related,$nav,$comments),array('content_width'=>'boxed','boxed_width'=>array('size'=>1100,'unit'=>'px'),'background_background'=>'classic','background_color'=>'#0d1b2a','padding'=>array('unit'=>'px','top'=>'54','right'=>'32','bottom'=>'88','left'=>'32','isLinked'=>false)))
            );
        }
        return array(
            self::container(array($meta,$title_left,$excerpt),array('content_width'=>'boxed','boxed_width'=>array('size'=>1020,'unit'=>'px'),'padding'=>array('unit'=>'px','top'=>'82','right'=>'24','bottom'=>'34','left'=>'24','isLinked'=>false))),
            self::container(array($image),array('content_width'=>'boxed','boxed_width'=>array('size'=>1120,'unit'=>'px'),'padding'=>array('unit'=>'px','top'=>'0','right'=>'24','bottom'=>'46','left'=>'24','isLinked'=>false))),
            self::container(array($content,$terms,$share,$author,$related,$nav,$comments),array('content_width'=>'boxed','boxed_width'=>array('size'=>820,'unit'=>'px'),'padding'=>array('unit'=>'px','top'=>'28','right'=>'24','bottom'=>'92','left'=>'24','isLinked'=>false)))
        );
    }

    public static function create_template(){
        if(!current_user_can('edit_posts'))wp_die('Yetkiniz yok.');
        check_admin_referer('wpst_create_blog_template');
        $key=isset($_GET['template'])?sanitize_key(wp_unslash($_GET['template'])):'editorial';
        $all=self::templates();if(empty($all[$key]))$key='editorial';
        $post_id=wp_insert_post(array('post_title'=>'WPSoft Blog - '.$all[$key]['title'],'post_type'=>'elementor_library','post_status'=>'publish'),true);
        if(is_wp_error($post_id))wp_die(esc_html($post_id->get_error_message()));
        update_post_meta($post_id,'_elementor_edit_mode','builder');
        update_post_meta($post_id,'_elementor_template_type','section');
        update_post_meta($post_id,'_wpst_blog_template','1');
        update_post_meta($post_id,'_elementor_version',defined('ELEMENTOR_VERSION')?ELEMENTOR_VERSION:'3.0.0');
        update_post_meta($post_id,'_elementor_data',wp_slash(wp_json_encode(self::data($key))));
        wp_safe_redirect(admin_url('post.php?post='.$post_id.'&action=elementor'));exit;
    }
    public static function replace_archive_content($content){
        if(is_admin() || !is_page() || !in_the_loop() || !is_main_query()) return $content;
        $s=wp_parse_args(get_option('wpst_settings',array()),array(
            'blog_archive_enabled'=>0,
            'blog_archive_template'=>0,
            'blog_page_id'=>0
        ));
        $page_id=absint($s['blog_page_id']);
        $template_id=absint($s['blog_archive_template']);
        if($template_id && class_exists('WPST_Display_Conditions') && !WPST_Display_Conditions::match_template($template_id)) return $content;
        if(empty($s['blog_archive_enabled']) || !$page_id || !$template_id || get_queried_object_id()!==$page_id) return $content;
        if(!class_exists('\\Elementor\\Plugin')) return $content;

        static $rendering_archive=false;
        if($rendering_archive) return $content;
        $rendering_archive=true;
        $html=\Elementor\Plugin::instance()->frontend->get_builder_content_for_display($template_id,true);
        $rendering_archive=false;

        return $html ? '<div class="wpst-blog-archive-output">'.$html.'</div>' : $content;
    }

    public static function replace_single_content($content){
        if(is_admin()||!is_singular('post')||!in_the_loop()||!is_main_query())return $content;
        $s=wp_parse_args(get_option('wpst_settings',array()),array(
            'blog_single_enabled'=>0,
            'blog_single_template'=>0
        ));
        $id=absint($s['blog_single_template']);
        if($id && class_exists('WPST_Display_Conditions') && !WPST_Display_Conditions::match_template($id)) return $content;
        if(empty($s['blog_single_enabled'])||!$id||!class_exists('\Elementor\Plugin'))return $content;
        static $rendering=false;if($rendering)return $content;$rendering=true;
        $html=\Elementor\Plugin::instance()->frontend->get_builder_content_for_display($id,true);
        $rendering=false;
        return $html?'<div class="wpst-blog-single-output">'.$html.'</div>':$content;
    }

    /**
     * Tek Yazı Builder aktifken aktif temanın içerikten önce bastığı varsayılan
     * yazı başlığını/header alanını gizler. WPSoft'un kendi dinamik başlık widgetı
     * (.wpst-post-title) bu kurallardan özellikle hariç tutulur.
     */
    public static function single_title_compat_css(){
        if(is_admin() || !is_singular('post')) return;
        $s=get_option('wpst_settings',array());
        if(empty($s['blog_single_enabled']) || empty($s['blog_single_template'])) return;
        echo '<style id="wpst-single-title-compat">
body.single-post.wpst-blog-single-template-active .site-main>article>.entry-header,
body.single-post.wpst-blog-single-template-active main.site-main>article>.entry-header,
body.single-post.wpst-blog-single-template-active #primary>article>.entry-header,
body.single-post.wpst-blog-single-template-active #main>article>.entry-header{display:none!important;}
body.single-post.wpst-blog-single-template-active h1.entry-title:not(.wpst-post-title),
body.single-post.wpst-blog-single-template-active h1.post-title:not(.wpst-post-title),
body.single-post.wpst-blog-single-template-active h1.page-title:not(.wpst-post-title),
body.single-post.wpst-blog-single-template-active .single-post-title:not(.wpst-post-title),
body.single-post.wpst-blog-single-template-active .wp-block-post-title:not(.wpst-post-title),
body.single-post.wpst-blog-single-template-active .post-header>.post-title:not(.wpst-post-title),
body.single-post.wpst-blog-single-template-active .post-header>.entry-title:not(.wpst-post-title){display:none!important;}
body.single-post.wpst-blog-single-template-active .wpst-blog-single-output .wpst-post-title{display:block!important;}
</style>';
    }

    public static function body_class($classes){
        $s=get_option('wpst_settings',array());
        if(is_singular('post')&&!empty($s['blog_single_enabled'])&&!empty($s['blog_single_template'])&&(!class_exists('WPST_Display_Conditions')||WPST_Display_Conditions::match_template(absint($s['blog_single_template']))))$classes[]='wpst-blog-single-template-active';
        if(is_page()&&!empty($s['blog_archive_enabled'])&&!empty($s['blog_archive_template'])&&!empty($s['blog_page_id'])&&get_queried_object_id()===absint($s['blog_page_id']))$classes[]='wpst-blog-archive-template-active';
        return $classes;
    }
}
