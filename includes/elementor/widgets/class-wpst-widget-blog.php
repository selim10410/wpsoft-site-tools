<?php
if ( ! defined( 'ABSPATH' ) ) exit;

abstract class WPST_Blog_Widget_Base extends WPST_Elementor_Widget_Base {
    protected function preview_post(){
        $id=get_the_ID();
        if($id && 'post'===get_post_type($id)) return $id;
        $posts=get_posts(array('post_type'=>'post','post_status'=>'publish','posts_per_page'=>1));
        return $posts ? (int)$posts[0]->ID : 0;
    }
    protected function post_id(){ return $this->preview_post(); }
}

class WPST_Widget_Post_Title extends WPST_Blog_Widget_Base {
    public function get_name(){return 'wpsoft-post-title';}
    public function get_title(){return 'WPSoft · İçerik Başlığı';}
    public function get_icon(){return 'eicon-post-title';}
    protected function register_controls(){
        $this->start_controls_section('content',array('label'=>'Başlık'));
        $this->add_control('preview_title',array('label'=>'Editör Önizleme Başlığı','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'Örnek Blog Yazısı Başlığı','description'=>'Gerçek yazıda WordPress başlığı otomatik gelir.')); $this->add_control('tag',array('label'=>'HTML Etiketi','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'h1','options'=>array('h1'=>'H1','h2'=>'H2','h3'=>'H3','div'=>'DIV')));
        $this->add_control('align',array('label'=>'Hizalama','type'=>\Elementor\Controls_Manager::CHOOSE,'default'=>'left','options'=>array('left'=>array('title'=>'Sol','icon'=>'eicon-text-align-left'),'center'=>array('title'=>'Orta','icon'=>'eicon-text-align-center'),'right'=>array('title'=>'Sağ','icon'=>'eicon-text-align-right')),'selectors'=>array('{{WRAPPER}} .wpst-post-title'=>'text-align:{{VALUE}}')));
        $this->end_controls_section();
        $this->start_controls_section('style',array('label'=>'Stil','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
        $this->add_control('color',array('label'=>'Renk','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-post-title'=>'color:{{VALUE}}')));
        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'typography','selector'=>'{{WRAPPER}} .wpst-post-title'));
        $this->end_controls_section();
    
        $this->standard_responsive_controls();
    }
    protected function render(){ $s=$this->get_settings_for_display();$id=$this->post_id();$title=$id?get_the_title($id):$s['preview_title'];$tag=in_array($s['tag'],array('h1','h2','h3','div'),true)?$s['tag']:'h1';echo '<'.$tag.' class="wpst-post-title">'.esc_html($title).'</'.$tag.'>'; }
}

class WPST_Widget_Post_Content extends WPST_Blog_Widget_Base {
    public function get_name(){return 'wpsoft-post-content';}
    public function get_title(){return 'WPSoft · İçerik Content';}
    public function get_icon(){return 'eicon-post-content';}
    protected function register_controls(){
        $this->start_controls_section('content_options',array('label'=>'İçerik Ayarları'));
        $this->add_control('content_mode',array(
            'label'=>'İçerik Modu',
            'type'=>\Elementor\Controls_Manager::SELECT,
            'default'=>'body',
            'options'=>array(
                'body'=>'Gövde İçeriği (Önerilen)',
                'full'=>'WordPress İçeriğinin Tamamı',
            ),
            'description'=>'Gövde İçeriği; şablondaki ayrı Başlık ve Content Görsel widgetlarıyla çakışabilecek tekrarları temizler. Yazı içindeki H2-H6 başlıklar ve gerçek içerik görselleri korunur.',
        ));
        $this->add_control('remove_duplicate_title',array(
            'label'=>'Tekrarlanan H1 Başlığını Kaldır',
            'type'=>\Elementor\Controls_Manager::SWITCHER,
            'return_value'=>'yes',
            'default'=>'yes',
            'condition'=>array('content_mode'=>'body'),
        ));
        $this->add_control('remove_duplicate_featured',array(
            'label'=>'Tekrarlanan Öne Çıkan Görseli Kaldır',
            'type'=>\Elementor\Controls_Manager::SWITCHER,
            'return_value'=>'yes',
            'default'=>'yes',
            'condition'=>array('content_mode'=>'body'),
        ));
        $this->add_control('remove_first_image',array(
            'label'=>'İlk İçerik Görselini de Kaldır',
            'type'=>\Elementor\Controls_Manager::SWITCHER,
            'return_value'=>'yes',
            'default'=>'',
            'condition'=>array('content_mode'=>'body'),
            'description'=>'Sadece eski yazılarda öne çıkan görsel içerik alanına manuel eklenmişse kullanın. Varsayılan kapalıdır.',
        ));
        $this->end_controls_section();
        $this->start_controls_section('preview',array('label'=>'Editör Önizleme'));
        $this->add_control('preview_content',array('label'=>'Önizleme İçeriği','type'=>\Elementor\Controls_Manager::WYSIWYG,'default'=>'<p>Blog yazısının ana gövde içeriği burada dinamik olarak görüntülenir.</p><h2>İçerik alt başlığı</h2><p>Başlık ve öne çıkan görsel için ayrı WPSoft widgetları kullanabilirsiniz.</p>'));
        $this->end_controls_section();
        $this->start_controls_section('style',array('label'=>'İçerik Stili','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
        $this->add_control('color',array('label'=>'Metin Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-post-content'=>'color:{{VALUE}}')));
        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'typography','selector'=>'{{WRAPPER}} .wpst-post-content'));
        $this->add_responsive_control('max_width',array('label'=>'Okuma Genişliği','type'=>\Elementor\Controls_Manager::SLIDER,'size_units'=>array('px'),'range'=>array('px'=>array('min'=>480,'max'=>1200)),'default'=>array('size'=>820,'unit'=>'px'),'selectors'=>array('{{WRAPPER}} .wpst-post-content'=>'max-width:{{SIZE}}{{UNIT}}')));
        $this->end_controls_section();
    
        $this->standard_responsive_controls();
    }
    private function normalize_text($text){
        $text=wp_strip_all_tags(html_entity_decode((string)$text,ENT_QUOTES,get_bloginfo('charset')));
        $text=preg_replace('/\s+/u',' ',trim($text));
        return function_exists('mb_strtolower')?mb_strtolower($text,'UTF-8'):strtolower($text);
    }
    private function remove_duplicate_h1($content,$post_id){
        $title=$this->normalize_text(get_the_title($post_id));
        if(''===$title) return $content;
        return preg_replace_callback('/<h1\b[^>]*>.*?<\/h1>/is',function($m)use($title){
            return $this->normalize_text($m[0])===$title?'':$m[0];
        },$content,1);
    }
    private function remove_featured_image_duplicate($content,$post_id){
        $thumb_id=get_post_thumbnail_id($post_id);
        if(!$thumb_id) return $content;
        $urls=array_filter(array(
            wp_get_attachment_image_url($thumb_id,'full'),
            wp_get_attachment_image_url($thumb_id,'large'),
            wp_get_attachment_image_url($thumb_id,'medium_large'),
            wp_get_attachment_image_url($thumb_id,'medium'),
        ));
        if(!$urls) return $content;
        $matched=false;
        $pattern='/<(figure|div)\b[^>]*>.*?<img\b[^>]*>.*?<\/\1>|<img\b[^>]*>/is';
        return preg_replace_callback($pattern,function($m)use($urls,&$matched){
            if($matched) return $m[0];
            foreach($urls as $url){
                $path=wp_parse_url($url,PHP_URL_PATH);
                $base=$path?basename($path):'';
                if(($url && false!==strpos($m[0],$url)) || ($base && false!==strpos($m[0],$base))){
                    $matched=true;
                    return '';
                }
            }
            return $m[0];
        },$content);
    }
    private function remove_first_content_image($content){
        return preg_replace('/<(figure|div)\b[^>]*>.*?<img\b[^>]*>.*?<\/\1>|<img\b[^>]*>/is','',$content,1);
    }
    protected function render(){
        $s=$this->get_settings_for_display();
        $id=$this->post_id();
        if($id){
            $post=get_post($id);
            $content=$post?$post->post_content:'';
            $content=apply_filters('the_content',$content);
            if('body'===($s['content_mode']??'body')){
                if('yes'===($s['remove_duplicate_title']??'yes')) $content=$this->remove_duplicate_h1($content,$id);
                if('yes'===($s['remove_duplicate_featured']??'yes')) $content=$this->remove_featured_image_duplicate($content,$id);
                if('yes'===($s['remove_first_image']??'')) $content=$this->remove_first_content_image($content);
            }
        } else {
            $content=wp_kses_post($s['preview_content']??'');
        }
        echo '<article class="wpst-post-content">'.$content.'</article>';
    }
}

class WPST_Widget_Post_Image extends WPST_Blog_Widget_Base {
    public function get_name(){return 'wpsoft-post-image';}
    public function get_title(){return 'WPSoft · Content Görsel';}
    public function get_icon(){return 'eicon-featured-image';}
    protected function register_controls(){
        $this->start_controls_section('content',array('label'=>'Görsel'));
        $this->add_control('size',array('label'=>'Görsel Boyutu','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'large','options'=>array('medium'=>'Orta','large'=>'Büyük','full'=>'Tam Boyut')));
        $this->add_control('caption',array('label'=>'Açıklamayı Göster','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'')); $this->add_control('placeholder_text',array('label'=>'Editör Önizleme Yazısı','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Öne Çıkan Görsel'));
        $this->end_controls_section();
        $this->start_controls_section('style',array('label'=>'Stil','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
        $this->add_responsive_control('height',array('label'=>'Yükseklik','type'=>\Elementor\Controls_Manager::SLIDER,'size_units'=>array('px'),'range'=>array('px'=>array('min'=>180,'max'=>900)),'selectors'=>array('{{WRAPPER}} .wpst-post-image img'=>'height:{{SIZE}}{{UNIT}}')));
        $this->add_control('radius',array('label'=>'Köşe','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>60)),'default'=>array('size'=>22),'selectors'=>array('{{WRAPPER}} .wpst-post-image img'=>'border-radius:{{SIZE}}px')));
        $this->end_controls_section();
    
        $this->standard_responsive_controls();
    }
    protected function render(){
        $s=$this->get_settings_for_display();$id=$this->post_id();
        echo '<figure class="wpst-post-image">';
        if($id && has_post_thumbnail($id)) echo get_the_post_thumbnail($id,$s['size'],array('loading'=>'eager'));
        else echo '<div class="wpst-post-image-placeholder"><span>'.esc_html($s['placeholder_text']).'</span></div>';
        if('yes'===$s['caption'] && $id){$cap=get_the_post_thumbnail_caption($id);if($cap)echo '<figcaption>'.esc_html($cap).'</figcaption>';}
        echo '</figure>';
    }
}

class WPST_Widget_Post_Meta extends WPST_Blog_Widget_Base {
    public function get_name(){return 'wpsoft-post-meta';}
    public function get_title(){return 'WPSoft · İçerik Bilgileri';}
    public function get_icon(){return 'eicon-post-info';}
    protected function register_controls(){
        $this->start_controls_section('content',array('label'=>'Bilgiler')); $this->add_control('preview_author',array('label'=>'Önizleme Yazar','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'WPSoft Editör')); $this->add_control('preview_category',array('label'=>'Önizleme Kategori','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Blog')); $this->add_control('reading_suffix',array('label'=>'Okuma Süresi Son Eki','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'dk okuma')); $this->add_control('comments_suffix',array('label'=>'Yorum Son Eki','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'yorum'));
        foreach(array('author'=>'Yazar','date'=>'Tarih','category'=>'Kategori','reading'=>'Okuma Süresi','comments'=>'Yorum') as $k=>$v)
            $this->add_control('show_'.$k,array('label'=>$v,'type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes'));
        $this->end_controls_section();
    
        $this->standard_responsive_controls();
    }
    protected function render(){
        $s=$this->get_settings_for_display();$id=$this->post_id();$parts=array();
        if('yes'===$s['show_author'])$parts[]='<span class="wpst-post-meta-author">'.esc_html($id?get_the_author_meta('display_name',get_post_field('post_author',$id)) : $s['preview_author']).'</span>';
        if('yes'===$s['show_date'])$parts[]='<span>'.esc_html($id?get_the_date('', $id):wp_date(get_option('date_format'))).'</span>';
        if('yes'===$s['show_category']){$cats=$id?get_the_category($id):array();$parts[]='<span>'.esc_html($cats?$cats[0]->name:$s['preview_category']).'</span>';}
        if('yes'===$s['show_reading']){$words=$id?str_word_count(wp_strip_all_tags(get_post_field('post_content',$id))):600;$parts[]='<span>'.max(1,(int)ceil($words/220)) .' '.esc_html($s['reading_suffix']).'</span>';}
        if('yes'===$s['show_comments'])$parts[]='<span>'.($id?(int)get_comments_number($id):0) .' '.esc_html($s['comments_suffix']).'</span>';
        echo '<div class="wpst-post-meta">'.implode('<i aria-hidden="true"></i>',$parts).'</div>';
    }
}

class WPST_Widget_Post_Excerpt extends WPST_Blog_Widget_Base {
    public function get_name(){return 'wpsoft-post-excerpt';}
    public function get_title(){return 'WPSoft · İçerik Özeti';}
    public function get_icon(){return 'eicon-text';}
    protected function register_controls(){
        $this->start_controls_section('content',array('label'=>'Özet'));
        $this->add_control('preview_excerpt',array('label'=>'Editör Önizleme Özeti','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'Yazının kısa özeti burada görüntülenir.','description'=>'Gerçek yazıda WordPress özeti otomatik gelir.'));
        $this->end_controls_section();
        $this->standard_responsive_controls();
    }
    protected function render(){ $s=$this->get_settings_for_display();$id=$this->post_id();$text=$id?get_the_excerpt($id):$s['preview_excerpt'];echo '<div class="wpst-post-excerpt">'.esc_html($text).'</div>'; }
}

class WPST_Widget_Post_Author extends WPST_Blog_Widget_Base {
    public function get_name(){return 'wpsoft-post-author';}
    public function get_title(){return 'WPSoft · Yazar Kutusu';}
    public function get_icon(){return 'eicon-person';}
    protected function register_controls(){
        $this->start_controls_section('content',array('label'=>'Yazar Kutusu'));
        $this->add_control('label',array('label'=>'Üst Etiket','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'YAZAR'));
        $this->add_control('preview_name',array('label'=>'Editör Önizleme Adı','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'WPSoft Editör'));
        $this->add_control('preview_bio',array('label'=>'Editör Önizleme Açıklaması','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'Yazar hakkında kısa açıklama.'));
        $this->end_controls_section();
        $this->standard_responsive_controls();
    }
    protected function render(){
        $s=$this->get_settings_for_display();$id=$this->post_id();$uid=$id?(int)get_post_field('post_author',$id):0;$name=$uid?get_the_author_meta('display_name',$uid):$s['preview_name'];$bio=$uid?get_the_author_meta('description',$uid):$s['preview_bio'];
        echo '<div class="wpst-post-author">'.get_avatar($uid?:0,72).'<div><small>'.esc_html($s['label']).'</small><strong>'.esc_html($name).'</strong><p>'.esc_html($bio).'</p></div></div>';
    }
}

class WPST_Widget_Post_Terms extends WPST_Blog_Widget_Base {
    public function get_name(){return 'wpsoft-post-terms';}
    public function get_title(){return 'WPSoft · Kategori & Etiketler';}
    public function get_icon(){return 'eicon-tags';}
    protected function register_controls(){
        $this->start_controls_section('content',array('label'=>'Kategori & Etiketler'));
        $this->add_control('preview_terms',array('label'=>'Editör Önizleme Etiketleri','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Blog, İçerik','description'=>'Gerçek yazıda kategori ve etiketler otomatik gelir.'));
        $this->end_controls_section();
        $this->standard_responsive_controls();
    }
    protected function render(){
        $s=$this->get_settings_for_display();$id=$this->post_id();$terms=$id?wp_get_post_terms($id,array('category','post_tag')):array();
        echo '<div class="wpst-post-terms">';
        if($terms && !is_wp_error($terms)){foreach($terms as $t)echo '<a href="'.esc_url(get_term_link($t)).'">'.esc_html($t->name).'</a>';}
        else foreach(array_filter(array_map('trim',explode(',',$s['preview_terms']))) as $term)echo '<span>'.esc_html($term).'</span>';
        echo '</div>';
    }
}

class WPST_Widget_Post_Navigation extends WPST_Blog_Widget_Base {
    public function get_name(){return 'wpsoft-post-navigation';}
    public function get_title(){return 'WPSoft · Önceki / Sonraki';}
    public function get_icon(){return 'eicon-post-navigation';}
    protected function register_controls(){
        $this->start_controls_section('content',array('label'=>'Navigasyon'));
        $this->add_control('prev_label',array('label'=>'Önceki Etiketi','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'← ÖNCEKİ YAZI'));
        $this->add_control('next_label',array('label'=>'Sonraki Etiketi','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'SONRAKİ YAZI →'));
        $this->add_control('preview_prev',array('label'=>'Önizleme Önceki Başlık','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Önceki içerik'));
        $this->add_control('preview_next',array('label'=>'Önizleme Sonraki Başlık','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Sonraki içerik'));
        $this->end_controls_section();
        $this->standard_responsive_controls();
    }
    protected function render(){
        $s=$this->get_settings_for_display();
        echo '<nav class="wpst-post-navigation">';
        previous_post_link('<div class="wpst-post-nav-prev"><small>'.esc_html($s['prev_label']).'</small><strong>%link</strong></div>','%title');
        next_post_link('<div class="wpst-post-nav-next"><small>'.esc_html($s['next_label']).'</small><strong>%link</strong></div>','%title');
        if(!is_singular('post')) echo '<div><small>'.esc_html($s['prev_label']).'</small><strong>'.esc_html($s['preview_prev']).'</strong></div><div><small>'.esc_html($s['next_label']).'</small><strong>'.esc_html($s['preview_next']).'</strong></div>';
        echo '</nav>';
    }
}

class WPST_Widget_Post_Share extends WPST_Blog_Widget_Base {
    public function get_name(){return 'wpsoft-post-share';}
    public function get_title(){return 'WPSoft · İçerik Paylaş';}
    public function get_icon(){return 'eicon-share';}
    protected function register_controls(){
        $this->start_controls_section('content',array('label'=>'Paylaşım'));
        $this->add_control('label',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Paylaş'));
        $this->add_control('facebook_text',array('label'=>'Facebook Yazısı','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Facebook'));
        $this->add_control('x_text',array('label'=>'X Yazısı','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'X'));
        $this->add_control('linkedin_text',array('label'=>'LinkedIn Yazısı','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'LinkedIn'));
        $this->end_controls_section();
        $this->standard_responsive_controls();
    }
    protected function render(){
        $s=$this->get_settings_for_display();$id=$this->post_id();$url=$id?get_permalink($id):home_url('/');$title=$id?get_the_title($id):get_bloginfo('name');
        echo '<div class="wpst-post-share"><span>'.esc_html($s['label']).'</span><a target="_blank" rel="noopener" href="'.esc_url('https://www.facebook.com/sharer/sharer.php?u='.rawurlencode($url)).'">'.esc_html($s['facebook_text']).'</a><a target="_blank" rel="noopener" href="'.esc_url('https://twitter.com/intent/tweet?url='.rawurlencode($url).'&text='.rawurlencode($title)).'">'.esc_html($s['x_text']).'</a><a target="_blank" rel="noopener" href="'.esc_url('https://www.linkedin.com/sharing/share-offsite/?url='.rawurlencode($url)).'">'.esc_html($s['linkedin_text']).'</a></div>';
    }
}

class WPST_Widget_Post_Comments extends WPST_Blog_Widget_Base {
    public function get_name(){return 'wpsoft-post-comments';}
    public function get_title(){return 'WPSoft · Yorumlar';}
    public function get_icon(){return 'eicon-comments';}
    protected function register_controls(){
        $this->start_controls_section('content',array('label'=>'Yorumlar'));
        $this->add_control('preview_title',array('label'=>'Editör Önizleme Başlığı','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Yorumlar'));
        $this->add_control('preview_text',array('label'=>'Editör Önizleme Açıklaması','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'Blog yazısı yorumları bu alanda görüntülenir.'));
        $this->end_controls_section();
        $this->standard_responsive_controls();
    }
    protected function render(){ $s=$this->get_settings_for_display();echo '<div class="wpst-post-comments">'; if(is_singular('post') && (comments_open()||get_comments_number())) comments_template(); else echo '<div class="wpst-comments-preview"><strong>'.esc_html($s['preview_title']).'</strong><p>'.esc_html($s['preview_text']).'</p></div>'; echo '</div>'; }
}


class WPST_Widget_Post_Reading_Progress extends WPST_Blog_Widget_Base {
    public function get_name(){return 'wpsoft-post-reading-progress';}
    public function get_title(){return 'WPSoft · Okuma İlerlemesi';}
    public function get_icon(){return 'eicon-skill-bar';}
    protected function register_controls(){
        $this->start_controls_section('content',array('label'=>'Okuma İlerlemesi'));
        $this->add_control('position',array('label'=>'Konum','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'top','options'=>array('top'=>'Ekranın Üstü','bottom'=>'Ekranın Altı','inline'=>'Widget İçinde')));
        $this->add_control('show_percent',array('label'=>'Yüzdeyi Göster','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>''));
        $this->add_control('aria_label',array('label'=>'Erişilebilirlik Metni','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Okuma ilerlemesi'));
        $this->end_controls_section();
        $this->start_controls_section('style',array('label'=>'Stil','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
        $this->add_control('track_color',array('label'=>'Zemin Rengi','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'rgba(148,163,184,.22)','selectors'=>array('{{WRAPPER}} .wpst-reading-progress'=>'--wpst-progress-track:{{VALUE}}')));
        $this->add_control('bar_color',array('label'=>'İlerleme Rengi','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#2563eb','selectors'=>array('{{WRAPPER}} .wpst-reading-progress'=>'--wpst-progress-color:{{VALUE}}')));
        $this->add_control('height',array('label'=>'Kalınlık','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>2,'max'=>14)),'default'=>array('size'=>4),'selectors'=>array('{{WRAPPER}} .wpst-reading-progress'=>'--wpst-progress-height:{{SIZE}}px')));
        $this->end_controls_section();
        $this->standard_responsive_controls();
    }
    protected function render(){
        $s=$this->get_settings_for_display();
        $position=in_array($s['position'],array('top','bottom','inline'),true)?$s['position']:'top';
        echo '<div class="wpst-reading-progress is-'.esc_attr($position).'" data-wpst-reading-progress data-show-percent="'.esc_attr('yes'===$s['show_percent']?'1':'0').'" role="progressbar" aria-label="'.esc_attr($s['aria_label']).'" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><span class="wpst-reading-progress-bar"></span><b class="wpst-reading-progress-percent">0%</b></div>';
    }
}

class WPST_Widget_Related_Posts extends WPST_Blog_Widget_Base {
    public function get_name(){return 'wpsoft-related-posts';}
    public function get_title(){return 'WPSoft · İlgili Yazılar';}
    public function get_icon(){return 'eicon-posts-grid';}
    protected function register_controls(){
        $this->start_controls_section('content',array('label'=>'İlgili Yazılar'));
        $this->add_control('title',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'İlgili Yazılar'));
        $this->add_control('count',array('label'=>'Yazı Sayısı','type'=>\Elementor\Controls_Manager::NUMBER,'min'=>1,'max'=>12,'default'=>3));
        $this->add_control('match_by',array('label'=>'Eşleştirme','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'category','options'=>array('category'=>'Aynı Kategori','tag'=>'Aynı Etiket','category_tag'=>'Kategori + Etiket','recent'=>'Son Yazılar')));
        $this->add_responsive_control('columns',array('label'=>'Kolon','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'3','tablet_default'=>'2','mobile_default'=>'1','options'=>array('1'=>'1','2'=>'2','3'=>'3','4'=>'4'),'selectors'=>array('{{WRAPPER}} .wpst-related-posts-grid'=>'--wpst-related-cols:{{VALUE}}')));
        $this->add_control('show_image',array('label'=>'Görsel','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes'));
        $this->add_control('show_date',array('label'=>'Tarih','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes'));
        $this->add_control('show_excerpt',array('label'=>'Özet','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>''));
        $this->add_control('excerpt_length',array('label'=>'Özet Kelime Sayısı','type'=>\Elementor\Controls_Manager::NUMBER,'min'=>5,'max'=>60,'default'=>18,'condition'=>array('show_excerpt'=>'yes')));
        $this->add_control('button_text',array('label'=>'Buton Metni','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Yazıyı Oku'));
        $this->add_control('empty_text',array('label'=>'İçerik Bulunamazsa','type'=>\Elementor\Controls_Manager::TEXT,'default'=>''));
        $this->end_controls_section();
        $this->start_controls_section('style',array('label'=>'Kart Stili','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
        $this->add_control('card_bg',array('label'=>'Kart Arka Planı','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-related-post-card'=>'background:{{VALUE}}')));
        $this->add_control('radius',array('label'=>'Köşe','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>40)),'default'=>array('size'=>18),'selectors'=>array('{{WRAPPER}} .wpst-related-post-card'=>'border-radius:{{SIZE}}px','{{WRAPPER}} .wpst-related-post-thumb img'=>'border-radius:calc({{SIZE}}px - 5px)')));
        $this->end_controls_section();
        $this->standard_responsive_controls();
    }
    protected function render(){
        $s=$this->get_settings_for_display();$id=$this->post_id();
        $args=array('post_type'=>'post','post_status'=>'publish','posts_per_page'=>max(1,(int)$s['count']),'ignore_sticky_posts'=>true);
        if($id) $args['post__not_in']=array($id);
        if($id && in_array($s['match_by'],array('category','category_tag'),true)){
            $cats=wp_get_post_categories($id); if($cats)$args['category__in']=$cats;
        }
        if($id && in_array($s['match_by'],array('tag','category_tag'),true)){
            $tags=wp_get_post_tags($id,array('fields'=>'ids')); if($tags)$args['tag__in']=$tags;
        }
        $q=new \WP_Query($args);
        echo '<section class="wpst-related-posts"><div class="wpst-related-posts-head"><h2>'.esc_html($s['title']).'</h2></div><div class="wpst-related-posts-grid">';
        if($q->have_posts()){
            while($q->have_posts()){ $q->the_post();
                echo '<article class="wpst-related-post-card">';
                if('yes'===$s['show_image']){echo '<a class="wpst-related-post-thumb" href="'.esc_url(get_permalink()).'">'; if(has_post_thumbnail())the_post_thumbnail('medium_large',array('loading'=>'lazy')); else echo '<span></span>'; echo '</a>';}
                echo '<div class="wpst-related-post-body">';
                if('yes'===$s['show_date'])echo '<small>'.esc_html(get_the_date()).'</small>';
                echo '<h3><a href="'.esc_url(get_permalink()).'">'.esc_html(get_the_title()).'</a></h3>';
                if('yes'===$s['show_excerpt'])echo '<p>'.esc_html(wp_trim_words(get_the_excerpt(),(int)$s['excerpt_length'],'…')).'</p>';
                if($s['button_text'])echo '<a class="wpst-related-post-link" href="'.esc_url(get_permalink()).'">'.esc_html($s['button_text']).' <span>→</span></a>';
                echo '</div></article>';
            }
        } elseif($s['empty_text']) echo '<div class="wpst-related-empty">'.esc_html($s['empty_text']).'</div>';
        echo '</div></section>';
        wp_reset_postdata();
    }
}


abstract class WPST_Archive_Widget_Base extends WPST_Elementor_Widget_Base {
    protected function archive_context(){
        if(is_category()) return 'category';
        if(is_tag()) return 'tag';
        if(is_author()) return 'author';
        if(is_search()) return 'search';
        if(is_date()) return 'date';
        if(is_home()) return 'blog';
        return 'preview';
    }
}

class WPST_Widget_Archive_Title extends WPST_Archive_Widget_Base {
    public function get_name(){return 'wpsoft-archive-title';}
    public function get_title(){return 'WPSoft · Arşiv Başlığı';}
    public function get_icon(){return 'eicon-archive-title';}
    protected function register_controls(){
        $this->start_controls_section('content',array('label'=>'Arşiv Başlığı'));
        $this->add_control('preview_title',array('label'=>'Editör Önizleme Başlığı','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Blog Arşivi'));
        $this->add_control('search_prefix',array('label'=>'Arama Ön Eki','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Arama:'));
        $this->add_control('tag',array('label'=>'HTML Etiketi','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'h1','options'=>array('h1'=>'H1','h2'=>'H2','h3'=>'H3','div'=>'DIV')));
        $this->end_controls_section();
        $this->start_controls_section('style',array('label'=>'Stil','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
        $this->add_control('color',array('label'=>'Renk','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-archive-title'=>'color:{{VALUE}}')));
        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'typography','selector'=>'{{WRAPPER}} .wpst-archive-title'));
        $this->end_controls_section();
        $this->standard_responsive_controls();
    }
    protected function render(){
        $s=$this->get_settings_for_display();
        if(is_search()) $title=trim($s['search_prefix'].' '.get_search_query());
        elseif(is_archive()) $title=get_the_archive_title();
        elseif(is_home()) $title=get_the_title((int)get_option('page_for_posts')) ?: get_bloginfo('name');
        else $title=$s['preview_title'];
        $tag=in_array($s['tag'],array('h1','h2','h3','div'),true)?$s['tag']:'h1';
        echo '<'.$tag.' class="wpst-archive-title">'.esc_html(wp_strip_all_tags($title)).'</'.$tag.'>';
    }
}

class WPST_Widget_Archive_Description extends WPST_Archive_Widget_Base {
    public function get_name(){return 'wpsoft-archive-description';}
    public function get_title(){return 'WPSoft · Arşiv Açıklaması';}
    public function get_icon(){return 'eicon-text-area';}
    protected function register_controls(){
        $this->start_controls_section('content',array('label'=>'Arşiv Açıklaması'));
        $this->add_control('preview_text',array('label'=>'Editör Önizleme Açıklaması','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'Kategori, etiket veya yazar arşivinin açıklaması burada dinamik görünür.'));
        $this->add_control('fallback_text',array('label'=>'Açıklama Yoksa','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>''));
        $this->end_controls_section();
        $this->standard_responsive_controls();
    }
    protected function render(){
        $s=$this->get_settings_for_display();$text='';
        if(is_category()||is_tag()) $text=term_description();
        elseif(is_author()) $text=get_the_author_meta('description',get_queried_object_id());
        if(!$text) $text=(is_archive()||is_search()||is_home())?$s['fallback_text']:$s['preview_text'];
        if($text) echo '<div class="wpst-archive-description">'.wp_kses_post(wpautop($text)).'</div>';
    }
}

class WPST_Widget_Archive_Author extends WPST_Archive_Widget_Base {
    public function get_name(){return 'wpsoft-archive-author';}
    public function get_title(){return 'WPSoft · Arşiv Yazar Kartı';}
    public function get_icon(){return 'eicon-person';}
    protected function register_controls(){
        $this->start_controls_section('content',array('label'=>'Yazar Kartı'));
        $this->add_control('label',array('label'=>'Üst Etiket','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'YAZAR'));
        $this->add_control('avatar_size',array('label'=>'Avatar Boyutu','type'=>\Elementor\Controls_Manager::NUMBER,'min'=>32,'max'=>160,'default'=>80));
        $this->add_control('preview_name',array('label'=>'Önizleme Adı','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'WPSoft Editör'));
        $this->add_control('preview_bio',array('label'=>'Önizleme Açıklaması','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'Yazar biyografisi burada görüntülenir.'));
        $this->end_controls_section();
        $this->standard_responsive_controls();
    }
    protected function render(){
        $s=$this->get_settings_for_display();$uid=is_author()?get_queried_object_id():0;
        $name=$uid?get_the_author_meta('display_name',$uid):$s['preview_name'];
        $bio=$uid?get_the_author_meta('description',$uid):$s['preview_bio'];
        echo '<div class="wpst-archive-author">'.get_avatar($uid?:0,(int)$s['avatar_size']).'<div><small>'.esc_html($s['label']).'</small><strong>'.esc_html($name).'</strong>';
        if($bio) echo '<p>'.esc_html($bio).'</p>';
        echo '</div></div>';
    }
}
