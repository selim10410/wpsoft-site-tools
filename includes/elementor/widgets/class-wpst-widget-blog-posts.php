<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class WPST_Widget_Blog_Posts extends WPST_Elementor_Widget_Base {
    public function get_name(){ return 'wpsoft-blog-posts'; }
    public function get_title(){ return 'WPSoft · Blog Yazıları'; }
    public function get_icon(){ return 'eicon-posts-grid'; }

    protected function register_controls(){
        $this->start_controls_section('query',array('label'=>'Yazı Sorgusu'));
  $this->wpst_signature_preset_control();
        $this->add_control('use_current_query',array(
            'label'=>'Aktif Arşiv Sorgusunu Kullan',
            'type'=>\Elementor\Controls_Manager::SWITCHER,
            'return_value'=>'yes','default'=>'',
            'description'=>"Kategori, etiket, yazar ve arama şablonlarında WordPress'in mevcut sorgusunu otomatik kullanır."
        ));
        $this->add_control('posts_per_page',array(
            'label'=>'Gösterilecek Yazı Sayısı',
            'type'=>\Elementor\Controls_Manager::NUMBER,
            'min'=>-1,'max'=>100,'step'=>1,'default'=>10,
            'description'=>'-1 girerseniz tüm yayınlanmış yazılar gösterilir.'
        ));
        $cats=array(''=>'Tüm Kategoriler'); foreach(get_categories(array('hide_empty'=>false)) as $cat)$cats[$cat->term_id]=$cat->name;
        $this->add_control('category',array('label'=>'Kategori','type'=>\Elementor\Controls_Manager::SELECT,'options'=>$cats,'default'=>''));
        $this->add_control('show_filter',array('label'=>'Kategori Filtreleri','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>''));
        $this->add_control('show_search',array('label'=>'Blog Arama','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>''));
        $this->add_control('featured_first',array('label'=>'İlk Yazıyı Öne Çıkar','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>''));

        $this->add_control('orderby',array(
            'label'=>'Sıralama',
            'type'=>\Elementor\Controls_Manager::SELECT,
            'default'=>'date',
            'options'=>array('date'=>'Tarih','title'=>'Başlık','modified'=>'Güncellenme','comment_count'=>'Yorum Sayısı','rand'=>'Rastgele')
        ));
        $this->add_control('order',array(
            'label'=>'Yön',
            'type'=>\Elementor\Controls_Manager::SELECT,
            'default'=>'DESC',
            'options'=>array('DESC'=>'Yeni → Eski','ASC'=>'Eski → Yeni')
        ));
        $this->add_control('ignore_sticky',array(
            'label'=>'Sabit Yazıları Normal Sırala',
            'type'=>\Elementor\Controls_Manager::SWITCHER,
            'return_value'=>'yes','default'=>'yes'
        ));
        $this->end_controls_section();

        $this->start_controls_section('layout',array('label'=>'Görünüm'));
        $this->add_control('layout_style',array(
            'label'=>'Stil',
            'type'=>\Elementor\Controls_Manager::SELECT,
            'default'=>'cards',
            'options'=>array(
                'cards'=>'Kart Grid',
                'magazine'=>'Magazine',
                'minimal'=>'Minimal Liste',
                'masonry'=>'Masonry',
                'featured'=>'Featured + Grid',
                'editorial-feed'=>'Editorial Feed',
                'compact-news'=>'Compact News',
                'visual-journal'=>'Visual Journal'
            )
        ));
        $this->add_control('card_preset',array(
            'label'=>'Kart Görünümü',
            'type'=>\Elementor\Controls_Manager::SELECT,
            'default'=>'modern',
            'options'=>array('modern'=>'Modern','editorial'=>'Editorial','soft'=>'Soft','borderless'=>'Borderless'),
            'prefix_class'=>'wpst-blog-card-style-'
        ));
        $this->add_responsive_control('columns',array(
            'label'=>'Kolon',
            'type'=>\Elementor\Controls_Manager::SELECT,
            'default'=>'3',
            'tablet_default'=>'2',
            'mobile_default'=>'1',
            'options'=>array('1'=>'1','2'=>'2','3'=>'3','4'=>'4'),
            'selectors'=>array('{{WRAPPER}} .wpst-blog-grid'=>'--wpst-blog-cols:{{VALUE}}')
        ));
        foreach(array('image'=>'Görsel','category'=>'Kategori','date'=>'Tarih','author'=>'Yazar','excerpt'=>'Özet') as $k=>$v){
            $this->add_control('show_'.$k,array('label'=>$v,'type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes'));
        }
        $this->add_control('excerpt_length',array(
            'label'=>'Özet Kelime Sayısı','type'=>\Elementor\Controls_Manager::NUMBER,'min'=>5,'max'=>80,'default'=>22,
            'condition'=>array('show_excerpt'=>'yes')
        ));
        $this->add_control('pagination_type',array('label'=>'Sayfalama Tipi','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'numbers','options'=>array('none'=>'Yok','numbers'=>'Numaralı','loadmore'=>'Daha Fazla Yükle')));

        $this->add_control('button_text',array('label'=>'Buton Yazısı','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Yazıyı Oku'));
        $this->add_control('search_placeholder',array('label'=>'Arama Placeholder','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Yazılarda ara...'));
        $this->add_control('search_button_text',array('label'=>'Arama Butonu','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Ara'));
        $this->add_control('all_filter_text',array('label'=>'Tümü Filtre Yazısı','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Tümü'));
        $this->add_control('loadmore_text',array('label'=>'Daha Fazla Butonu','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Daha Fazla Yükle'));
        $this->add_control('wpst_readmore_icon',array('label'=>'Yazıyı Oku WPSoft Icon','type'=>\Elementor\Controls_Manager::SELECT2,'options'=>class_exists('WPST_Icon_Library')?WPST_Icon_Library::options():array(),'default'=>'arrow-right','label_block'=>true));
        $this->add_control('readmore_icon',array('label'=>'Eski Yazıyı Oku Sembolü','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'→'));
        $this->add_control('wpst_loadmore_icon',array('label'=>'Daha Fazla WPSoft Icon','type'=>\Elementor\Controls_Manager::SELECT2,'options'=>class_exists('WPST_Icon_Library')?WPST_Icon_Library::options():array(),'default'=>'arrow-down','label_block'=>true));
        $this->add_control('loadmore_icon',array('label'=>'Eski Daha Fazla Sembolü','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'↓'));
        $this->add_control('pagination_prev_text',array('label'=>'Önceki Sayfa Sembolü / Metni','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'←'));
        $this->add_control('pagination_next_text',array('label'=>'Sonraki Sayfa Sembolü / Metni','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'→'));
        $this->add_control('pagination_aria_label',array('label'=>'Sayfalama Erişilebilirlik Metni','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Blog sayfalama'));
        $this->add_control('empty_title',array('label'=>'Boş Durum Başlığı','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Henüz yayınlanmış yazı yok.'));
        $this->add_control('empty_text',array('label'=>'Boş Durum Açıklaması','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'Yeni içerikler yayınlandığında burada otomatik görünecek.'));
        $this->end_controls_section();

        $this->start_controls_section('style',array('label'=>'Kart Stili','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
        $this->add_control('card_bg',array('label'=>'Kart Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-blog-card'=>'background:{{VALUE}}')));
        $this->add_control('title_color',array('label'=>'Başlık Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-blog-card h3 a'=>'color:{{VALUE}}')));
        $this->add_control('text_color',array('label'=>'Metin Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-blog-excerpt'=>'color:{{VALUE}}')));
        $this->add_control('radius',array('label'=>'Köşe','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>40)),'default'=>array('size'=>18),'selectors'=>array('{{WRAPPER}} .wpst-blog-card'=>'border-radius:{{SIZE}}px','{{WRAPPER}} .wpst-blog-thumb img'=>'border-radius:calc({{SIZE}}px - 5px)')));
        $this->add_control('hover_effect',array('label'=>'Hover','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'lift','options'=>array('none'=>'Yok','lift'=>'Lift','zoom'=>'Zoom','border'=>'Border'),'prefix_class'=>'wpst-blog-hover-'));
        $this->end_controls_section();
    
        $this->standard_responsive_controls();
    }

    protected function render(){
        $s=$this->get_settings_for_display();
        $paged=max(1,(int)get_query_var('paged'),(int)get_query_var('page'));
        $args=array(
            'post_type'=>'post',
            'post_status'=>'publish',
            'posts_per_page'=>(int)$s['posts_per_page'],
            'paged'=>$paged,
            'orderby'=>sanitize_key($s['orderby']),
            'order'=>('ASC'===$s['order']?'ASC':'DESC'),
            'ignore_sticky_posts'=>('yes'===$s['ignore_sticky'])
        );
        $use_current=('yes'===($s['use_current_query']??'')) && (is_archive() || is_search() || is_home());
        if($use_current){
            if(is_category()) $args['cat']=get_queried_object_id();
            elseif(is_tag()) $args['tag_id']=get_queried_object_id();
            elseif(is_author()) $args['author']=get_queried_object_id();
            elseif(is_search()) $args['s']=get_search_query();
            elseif(is_date()){
                $args['year']=(int)get_query_var('year');
                if(get_query_var('monthnum')) $args['monthnum']=(int)get_query_var('monthnum');
                if(get_query_var('day')) $args['day']=(int)get_query_var('day');
            }
        } else {
            $active_cat=!empty($_GET['cat'])?absint($_GET['cat']):absint($s['category']);
            if($active_cat) $args['cat']=$active_cat;
            if(!empty($_GET['s'])) $args['s']=sanitize_text_field(wp_unslash($_GET['s']));
        }
        $q=new \WP_Query($args);

        echo '<div class="wpst-blog-listing is-'.esc_attr($s['layout_style']).'">';
        if('yes'===$s['show_search'] || 'yes'===$s['show_filter']){
            echo '<div class="wpst-blog-toolbar">';
            if('yes'===$s['show_filter']){echo '<div class="wpst-blog-filters"><a class="'.(empty($s['category'])?'is-active':'').'" href="'.esc_url(remove_query_arg('cat')).'">'.esc_html($s['all_filter_text']).'</a>';foreach(get_categories(array('hide_empty'=>true)) as $cat)echo '<a class="'.((int)$s['category']===$cat->term_id?'is-active':'').'" href="'.esc_url(add_query_arg('cat',$cat->term_id)).'">'.esc_html($cat->name).'</a>';echo '</div>';}
            if('yes'===$s['show_search'])echo '<form class="wpst-blog-search" method="get"><input type="search" name="s" value="'.esc_attr(get_search_query()).'" placeholder="'.esc_attr($s['search_placeholder']).'"><button type="submit">'.esc_html($s['search_button_text']).'</button></form>';
            echo '</div>';
        }
        echo '<div class="wpst-blog-grid">';
        if($q->have_posts()){
            while($q->have_posts()){ $q->the_post();
                $cats=get_the_category();
                echo '<article class="wpst-blog-card '.(('yes'===$s['featured_first'] && 0===$q->current_post)?'is-featured':'').'">';
                if('yes'===$s['show_image']){
                    echo '<a class="wpst-blog-thumb" href="'.esc_url(get_permalink()).'">';
                    if(has_post_thumbnail()) the_post_thumbnail('large',array('loading'=>'lazy'));
                    else echo '<span class="wpst-blog-thumb-placeholder"></span>';
                    echo '</a>';
                }
                echo '<div class="wpst-blog-card-body">';
                if('yes'===$s['show_category'] && $cats) echo '<a class="wpst-blog-category" href="'.esc_url(get_category_link($cats[0]->term_id)).'">'.esc_html($cats[0]->name).'</a>';
                echo '<h3><a href="'.esc_url(get_permalink()).'">'.esc_html(get_the_title()).'</a></h3>';
                if('yes'===$s['show_date'] || 'yes'===$s['show_author']){
                    echo '<div class="wpst-blog-meta">';
                    if('yes'===$s['show_date']) echo '<span>'.esc_html(get_the_date()).'</span>';
                    if('yes'===$s['show_author']) echo '<span>'.esc_html(get_the_author()).'</span>';
                    echo '</div>';
                }
                if('yes'===$s['show_excerpt']){
                    echo '<div class="wpst-blog-excerpt">'.esc_html(wp_trim_words(get_the_excerpt(),(int)$s['excerpt_length'],'…')).'</div>';
                }
                if($s['button_text']) echo '<a class="wpst-blog-readmore" href="'.esc_url(get_permalink()).'"><span>'.esc_html($s['button_text']).'</span><i>'.((!empty($s['wpst_readmore_icon'])&&class_exists('WPST_Icon_Library'))?WPST_Icon_Library::svg($s['wpst_readmore_icon'],array('size'=>14)):esc_html($s['readmore_icon'])).'</i></a>';
                echo '</div></article>';
            }
        } else {
            echo '<div class="wpst-blog-empty"><strong>'.esc_html($s['empty_title']).'</strong><span>'.esc_html($s['empty_text']).'</span></div>';
        }
        echo '</div>';

        if('numbers'===$s['pagination_type'] && $q->max_num_pages>1){
            $links=paginate_links(array(
                'current'=>$paged,'total'=>$q->max_num_pages,'type'=>'array',
                'prev_text'=>esc_html($s['pagination_prev_text']),'next_text'=>esc_html($s['pagination_next_text'])
            ));
            if($links){
                echo '<nav class="wpst-blog-pagination" aria-label="'.esc_attr($s['pagination_aria_label']).'">';
                foreach($links as $link) echo $link;
                echo '</nav>';
            }
        }
        if('loadmore'===$s['pagination_type'] && $q->max_num_pages>$paged){
            echo '<div class="wpst-blog-loadmore"><a href="'.esc_url(get_pagenum_link($paged+1)).'"><span>'.esc_html($s['loadmore_text']).'</span><i>'.((!empty($s['wpst_loadmore_icon'])&&class_exists('WPST_Icon_Library'))?WPST_Icon_Library::svg($s['wpst_loadmore_icon'],array('size'=>14)):esc_html($s['loadmore_icon'])).'</i></a></div>';
        }
        echo '</div>';
        wp_reset_postdata();
    }
}
