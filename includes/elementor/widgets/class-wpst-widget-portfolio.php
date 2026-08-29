<?php
if(!defined('ABSPATH'))exit;

class WPST_Widget_Portfolio extends WPST_Elementor_Widget_Base{
 public function get_name(){return'wpsoft-portfolio';}
 public function get_title(){return'WPSoft · Portfolio 2.0';}
 public function get_icon(){return'eicon-gallery-masonry';}
 public function get_keywords(){return array('portfolio','project','gallery','case study','work','showcase','wpsoft');}
 public function get_script_depends(){return array('wpst-portfolio-filter');}

 public function is_dynamic_content(): bool{return true;}

 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'Projeler'));
  $this->wpst_signature_preset_control();

  $this->add_control('data_source',array(
   'label'=>'Veri Kaynağı',
   'type'=>\Elementor\Controls_Manager::SELECT,
   'default'=>'portfolio',
   'options'=>array(
    'portfolio'=>'WPSoft Portföyler',
    'manual'=>'Manuel Projeler'
   ),
   'description'=>'WPSoft Portföyler seçildiğinde içerikler bağımsız Portföyler menüsünden otomatik çekilir.'
  ));

  $portfolio_categories = class_exists('WPST_Portfolio_Manager')
   ? WPST_Portfolio_Manager::category_options()
   : array();

  // Use a real value for "all". Older widgets may still contain an empty
  // category value; render() treats both empty and "all" identically.
  unset($portfolio_categories['']);
  $portfolio_categories = array('all'=>'Tüm Kategoriler') + $portfolio_categories;

  $this->add_control('portfolio_category',array(
   'label'=>'Portföy Kategorisi',
   'type'=>\Elementor\Controls_Manager::SELECT2,
   'options'=>$portfolio_categories,
   'default'=>'all',
   'condition'=>array('data_source'=>'portfolio'),
   'description'=>'Kategori seçilmez veya Tüm Kategoriler seçilirse yayınlanmış tüm portföyler gösterilir.'
  ));

  $this->add_control('portfolio_count',array(
   'label'=>'Gösterilecek Proje',
   'type'=>\Elementor\Controls_Manager::NUMBER,
   'default'=>6,
   'min'=>1,
   'max'=>48,
   'condition'=>array('data_source'=>'portfolio')
  ));

  $this->add_control('show_filters',array(
   'label'=>'Kategori Filtresini Göster',
   'type'=>\Elementor\Controls_Manager::SWITCHER,
   'return_value'=>'yes',
   'default'=>'yes',
   'condition'=>array('data_source'=>'portfolio')
  ));

  $this->add_control('filter_all_label',array(
   'label'=>'Tümü Buton Yazısı',
   'type'=>\Elementor\Controls_Manager::TEXT,
   'default'=>'Tüm Projeler',
   'condition'=>array('data_source'=>'portfolio','show_filters'=>'yes')
  ));

  $this->add_control('enable_load_more',array(
   'label'=>'Devamını Göster',
   'type'=>\Elementor\Controls_Manager::SWITCHER,
   'return_value'=>'yes',
   'default'=>'yes',
   'condition'=>array('data_source'=>'portfolio')
  ));

  $this->add_control('load_more_label',array(
   'label'=>'Devamını Göster Yazısı',
   'type'=>\Elementor\Controls_Manager::TEXT,
   'default'=>'Devamını Göster',
   'condition'=>array('data_source'=>'portfolio','enable_load_more'=>'yes')
  ));

  $this->add_control('portfolio_orderby',array(
   'label'=>'Sıralama',
   'type'=>\Elementor\Controls_Manager::SELECT,
   'default'=>'date',
   'options'=>array(
    'date'=>'Eklenme Tarihi',
    'menu_order'=>'Özel Sıralama',
    'title'=>'Başlık',
    'modified'=>'Son Güncelleme'
   ),
   'condition'=>array('data_source'=>'portfolio')
  ));

  $this->add_control('portfolio_order',array(
   'label'=>'Sıra Yönü',
   'type'=>\Elementor\Controls_Manager::SELECT,
   'default'=>'DESC',
   'options'=>array('DESC'=>'Azalan','ASC'=>'Artan'),
   'condition'=>array('data_source'=>'portfolio')
  ));

  $r=new \Elementor\Repeater();
  $r->add_control('image',array(
   'label'=>'Görsel',
   'type'=>\Elementor\Controls_Manager::MEDIA
  ));
  $r->add_control('category',array(
   'label'=>'Kategori',
   'type'=>\Elementor\Controls_Manager::TEXT,
   'default'=>'Web Tasarım',
   'dynamic'=>array('active'=>true)
  ));
  $r->add_control('title',array(
   'label'=>'Başlık',
   'type'=>\Elementor\Controls_Manager::TEXT,
   'default'=>'Kurumsal Web Projesi',
   'dynamic'=>array('active'=>true)
  ));
  $r->add_control('url',array(
   'label'=>'Bağlantı',
   'type'=>\Elementor\Controls_Manager::URL,
   'show_external'=>true,
   'dynamic'=>array('active'=>true)
  ));

  $this->add_control('items',array(
   'label'=>'Manuel Projeler',
   'type'=>\Elementor\Controls_Manager::REPEATER,
   'fields'=>$r->get_controls(),
   'condition'=>array('data_source'=>'manual'),
   'default'=>array(
    array('category'=>'Kurumsal','title'=>'Dijital Dönüşüm Projesi'),
    array('category'=>'E-Ticaret','title'=>'Online Satış Platformu'),
    array('category'=>'Web Tasarım','title'=>'Modern Marka Sitesi')
   ),
   'title_field'=>'{{{ title }}}'
  ));

  $this->add_control('placeholder_text',array(
   'label'=>'Görsel Yoksa Yazı',
   'type'=>\Elementor\Controls_Manager::TEXT,
   'default'=>'Proje Görseli'
  ));

  $this->add_control('layout_style',array(
   'label'=>'Layout',
   'type'=>\Elementor\Controls_Manager::SELECT,
   'default'=>'grid',
   'options'=>array(
    'grid'=>'Modern Grid',
    'editorial'=>'Editorial Feature',
    'masonry'=>'Masonry Rhythm',
    'cinematic'=>'Cinematic Cases',
    'index'=>'Project Index',
    'tiles'=>'Asymmetric Tiles'
   ),
   'prefix_class'=>'wpst-portfolio-style-'
  ));

  $this->add_control('action_icon',array(
   'label'=>'Hover Icon',
   'type'=>\Elementor\Controls_Manager::SELECT2,
   'options'=>class_exists('WPST_Icon_Library')?WPST_Icon_Library::options():array(),
   'default'=>'arrow-up-right',
   'label_block'=>true
  ));

  $this->add_responsive_control('columns',array(
   'label'=>'Kolon',
   'type'=>\Elementor\Controls_Manager::SELECT,
   'default'=>'3',
   'tablet_default'=>'2',
   'mobile_default'=>'1',
   'options'=>array('1'=>'1','2'=>'2','3'=>'3','4'=>'4'),
   'selectors'=>array(
    '{{WRAPPER}} .wpst-ew-portfolio'=>'--wpst-portfolio-cols:{{VALUE}};'
   ),
   'condition'=>array('layout_style!'=>array('cinematic','index','tiles'))
  ));

  $this->add_responsive_control('grid_gap',array(
   'label'=>'Grid / Kart Aralığı',
   'type'=>\Elementor\Controls_Manager::SLIDER,
   'size_units'=>array('px'),
   'range'=>array('px'=>array('min'=>0,'max'=>80)),
   'default'=>array('size'=>20,'unit'=>'px'),
   'tablet_default'=>array('size'=>18,'unit'=>'px'),
   'mobile_default'=>array('size'=>16,'unit'=>'px'),
   'selectors'=>array(
    '{{WRAPPER}} .wpst-ew-portfolio'=>'--wpst-portfolio-gap:{{SIZE}}{{UNIT}};'
   )
  ));

  $this->end_controls_section();

  $this->start_controls_section('media_style',array(
   'label'=>'Görsel',
   'tab'=>\Elementor\Controls_Manager::TAB_STYLE
  ));

  $this->add_responsive_control('image_height',array(
   'label'=>'Görsel Yüksekliği',
   'type'=>\Elementor\Controls_Manager::SLIDER,
   'size_units'=>array('px','vh'),
   'range'=>array(
    'px'=>array('min'=>120,'max'=>900),
    'vh'=>array('min'=>20,'max'=>90)
   ),
   'default'=>array('size'=>300,'unit'=>'px'),
   'tablet_default'=>array('size'=>270,'unit'=>'px'),
   'mobile_default'=>array('size'=>240,'unit'=>'px'),
   'selectors'=>array(
    '{{WRAPPER}} .wpst-ew-portfolio'=>'--wpst-portfolio-media-height:{{SIZE}}{{UNIT}};'
   ),
   'condition'=>array('layout_style!'=>'index')
  ));

  $this->add_control('image_ratio',array(
   'label'=>'Görsel Oranı',
   'type'=>\Elementor\Controls_Manager::SELECT,
   'default'=>'auto',
   'options'=>array(
    'auto'=>'Yüksekliği Kullan',
    '1-1'=>'1:1 Kare',
    '4-3'=>'4:3',
    '3-2'=>'3:2',
    '16-9'=>'16:9',
    '4-5'=>'4:5 Portre'
   ),
   'prefix_class'=>'wpst-portfolio-ratio-',
   'condition'=>array('layout_style!'=>'index'),
   'description'=>'“Yüksekliği Kullan” seçildiğinde üstteki responsive Görsel Yüksekliği değeri uygulanır.'
  ));

  $this->add_control('image_fit',array(
   'label'=>'Görsel Oturtma',
   'type'=>\Elementor\Controls_Manager::SELECT,
   'default'=>'cover',
   'options'=>array(
    'cover'=>'Cover',
    'contain'=>'Contain'
   ),
   'selectors'=>array(
    '{{WRAPPER}} .wpst-ew-project-media img'=>'object-fit:{{VALUE}};'
   )
  ));

  $this->add_responsive_control('radius',array(
   'label'=>'Görsel Köşe',
   'type'=>\Elementor\Controls_Manager::SLIDER,
   'range'=>array('px'=>array('min'=>0,'max'=>60)),
   'default'=>array('size'=>20),
   'selectors'=>array(
    '{{WRAPPER}} .wpst-ew-portfolio'=>'--wpst-portfolio-media-radius:{{SIZE}}px;'
   )
  ));

  $this->add_control('media_bg',array(
   'label'=>'Görsel Alanı Arka Plan',
   'type'=>\Elementor\Controls_Manager::COLOR,
   'selectors'=>array(
    '{{WRAPPER}} .wpst-ew-portfolio'=>'--wpst-portfolio-media-bg:{{VALUE}};'
   )
  ));

  $this->end_controls_section();

  $this->start_controls_section('card_style',array(
   'label'=>'Kart & Grid',
   'tab'=>\Elementor\Controls_Manager::TAB_STYLE
  ));

  $this->add_control('card_preset',array(
   'label'=>'Kart Görünümü',
   'type'=>\Elementor\Controls_Manager::SELECT,
   'default'=>'clean',
   'options'=>array(
    'clean'=>'Clean',
    'card'=>'Modern Card',
    'soft'=>'Soft Surface',
    'outline'=>'Outline',
    'minimal'=>'Minimal'
   ),
   'prefix_class'=>'wpst-portfolio-card-'
  ));

  $this->add_responsive_control('card_padding',array(
   'label'=>'Kart İç Boşluk',
   'type'=>\Elementor\Controls_Manager::DIMENSIONS,
   'size_units'=>array('px'),
   'default'=>array('top'=>0,'right'=>0,'bottom'=>0,'left'=>0,'unit'=>'px','isLinked'=>true),
   'selectors'=>array(
    '{{WRAPPER}} .wpst-ew-portfolio'=>'--wpst-portfolio-card-pt:{{TOP}}{{UNIT}};--wpst-portfolio-card-pr:{{RIGHT}}{{UNIT}};--wpst-portfolio-card-pb:{{BOTTOM}}{{UNIT}};--wpst-portfolio-card-pl:{{LEFT}}{{UNIT}};'
   ),
   'condition'=>array('layout_style!'=>array('cinematic','index'))
  ));

  $this->add_responsive_control('content_padding',array(
   'label'=>'Metin İç Boşluk',
   'type'=>\Elementor\Controls_Manager::DIMENSIONS,
   'size_units'=>array('px'),
   'default'=>array('top'=>16,'right'=>4,'bottom'=>4,'left'=>4,'unit'=>'px','isLinked'=>false),
   'selectors'=>array(
    '{{WRAPPER}} .wpst-ew-portfolio'=>'--wpst-portfolio-copy-pt:{{TOP}}{{UNIT}};--wpst-portfolio-copy-pr:{{RIGHT}}{{UNIT}};--wpst-portfolio-copy-pb:{{BOTTOM}}{{UNIT}};--wpst-portfolio-copy-pl:{{LEFT}}{{UNIT}};'
   ),
   'condition'=>array('layout_style!'=>array('cinematic','index'))
  ));

  $this->add_control('card_bg',array(
   'label'=>'Kart Arka Plan',
   'type'=>\Elementor\Controls_Manager::COLOR,
   'selectors'=>array(
    '{{WRAPPER}} .wpst-ew-portfolio'=>'--wpst-portfolio-card-bg:{{VALUE}};'
   )
  ));

  $this->add_control('card_border',array(
   'label'=>'Kart Kenarlık',
   'type'=>\Elementor\Controls_Manager::COLOR,
   'selectors'=>array(
    '{{WRAPPER}} .wpst-ew-portfolio'=>'--wpst-portfolio-card-border:{{VALUE}};'
   )
  ));

  $this->add_responsive_control('card_radius',array(
   'label'=>'Kart Köşe',
   'type'=>\Elementor\Controls_Manager::SLIDER,
   'range'=>array('px'=>array('min'=>0,'max'=>60)),
   'default'=>array('size'=>22),
   'selectors'=>array(
    '{{WRAPPER}} .wpst-ew-portfolio'=>'--wpst-portfolio-card-radius:{{SIZE}}px;'
   ),
   'condition'=>array('layout_style!'=>'index')
  ));

  $this->end_controls_section();

  $this->start_controls_section('content_style',array(
   'label'=>'İçerik',
   'tab'=>\Elementor\Controls_Manager::TAB_STYLE
  ));

  $this->add_control('accent',array(
   'label'=>'Kategori / Vurgu',
   'type'=>\Elementor\Controls_Manager::COLOR,
   'selectors'=>array(
    '{{WRAPPER}} .wpst-ew-portfolio'=>'--portfolio-accent:{{VALUE}};'
   )
  ));

  $this->add_control('hover_effect',array(
   'label'=>'Hover',
   'type'=>\Elementor\Controls_Manager::SELECT,
   'default'=>'zoom',
   'options'=>array(
    'none'=>'Yok',
    'zoom'=>'Görsel Zoom',
    'lift'=>'Kart Yükselme',
    'overlay'=>'Overlay',
    'border'=>'Border Accent'
   ),
   'prefix_class'=>'wpst-portfolio-hover-'
  ));

  $this->end_controls_section();

  $this->standard_responsive_controls();
 }

 protected function is_elementor_edit_context(){
  if(is_admin()) return true;
  if(isset($_GET['elementor-preview'])) return true; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
  if(class_exists('\Elementor\Plugin')){
   try{
    $plugin=\Elementor\Plugin::instance();
    if(isset($plugin->editor) && is_object($plugin->editor) && method_exists($plugin->editor,'is_edit_mode') && $plugin->editor->is_edit_mode()) return true;
    if(isset($plugin->preview) && is_object($plugin->preview) && method_exists($plugin->preview,'is_preview_mode') && $plugin->preview->is_preview_mode()) return true;
   }catch(\Throwable $e){}
  }
  return false;
 }

 protected function portfolio_query_items($settings){
  if(!class_exists('WPST_Portfolio_Manager')) return array();

  $post_type=WPST_Portfolio_Manager::POST_TYPE;
  if(!post_type_exists($post_type)) return array();

  $orderby=isset($settings['portfolio_orderby'])?$settings['portfolio_orderby']:'date';
  if(!in_array($orderby,array('date','menu_order','title','modified'),true)) $orderby='date';

  $order=(isset($settings['portfolio_order'])&&'ASC'===$settings['portfolio_order'])?'ASC':'DESC';
  $visible_limit=max(1,min(48,absint($settings['portfolio_count']??6)));
  $needs_full_set=('yes'===($settings['show_filters']??'yes') || 'yes'===($settings['enable_load_more']??'yes'));
  $limit=$needs_full_set ? -1 : $visible_limit;

  $category_raw=isset($settings['portfolio_category'])?$settings['portfolio_category']:'all';
  $category_id=is_numeric($category_raw)?absint($category_raw):0;

  $editor_context=$this->is_elementor_edit_context();
  $statuses=array('publish');

  // Elementor must be able to preview a portfolio card before the portfolio
  // item itself is published. Public frontend still remains publish-only.
  if($editor_context && current_user_can('edit_posts')){
   $statuses=array('publish','draft','pending','private','future');
  }

  $args=array(
   'post_type'=>$post_type,
   'post_status'=>$statuses,
   'posts_per_page'=>$limit,
   'orderby'=>$orderby,
   'order'=>$order,
   'fields'=>'ids',
   'no_found_rows'=>true,
   'ignore_sticky_posts'=>true,
   'suppress_filters'=>true,
  );

  // No category / "all" means exactly ALL portfolio items allowed by status.
  if($category_id>0){
   $args['tax_query']=array(array(
    'taxonomy'=>WPST_Portfolio_Manager::TAXONOMY,
    'field'=>'term_id',
    'terms'=>array($category_id),
    'include_children'=>true,
    'operator'=>'IN'
   ));
  }

  $post_ids=get_posts($args);
  $items=array();

  foreach(array_map('absint',(array)$post_ids) as $portfolio_id){
   if(!$portfolio_id) continue;

   $portfolio_post=get_post($portfolio_id);
   if(!$portfolio_post || $post_type!==$portfolio_post->post_type) continue;
   if(!$editor_context && 'publish'!==$portfolio_post->post_status) continue;

   $terms=get_the_terms($portfolio_id,WPST_Portfolio_Manager::TAXONOMY);
   $category='';
   $category_slugs=array();
   if(!is_wp_error($terms)&&!empty($terms)){
    $category=$terms[0]->name;
    foreach($terms as $term){
     $category_slugs[]=sanitize_title($term->slug);
    }
   }

   $url=get_post_meta($portfolio_id,WPST_Portfolio_Manager::META_URL,true);
   if(!$url) $url=get_permalink($portfolio_id);

   $thumb_id=get_post_thumbnail_id($portfolio_id);
   $thumb_url=$thumb_id?wp_get_attachment_image_url($thumb_id,'full'):'';

   $items[]=array(
    'image'=>array(
     'id'=>$thumb_id,
     'url'=>$thumb_url?:''
    ),
    'category'=>$category,
    '_category_slugs'=>$category_slugs,
    'title'=>get_the_title($portfolio_id),
    'url'=>array('url'=>$url),
    '_status'=>$portfolio_post->post_status
   );
  }

  return $items;
 }

 protected function render(){
  $s=$this->get_settings_for_display();

  $source=isset($s['data_source']) && in_array($s['data_source'],array('portfolio','manual'),true)
   ? $s['data_source']
   : 'portfolio';

  $items=('portfolio'===$source)
   ? $this->portfolio_query_items($s)
   : (array)($s['items']??array());

  $initial_count=max(1,min(48,absint($s['portfolio_count']??6)));
  $show_filters=('portfolio'===$source && 'yes'===($s['show_filters']??'yes'));
  $enable_load_more=('portfolio'===$source && 'yes'===($s['enable_load_more']??'yes'));

  $filter_terms=array();
  if($show_filters && class_exists('WPST_Portfolio_Manager')){
   $term_args=array(
    'taxonomy'=>WPST_Portfolio_Manager::TAXONOMY,
    'hide_empty'=>true
   );
   $selected=isset($s['portfolio_category'])?$s['portfolio_category']:'all';
   if(is_numeric($selected) && absint($selected)>0){
    $term_args['include']=array(absint($selected));
   }
   $filter_terms=get_terms($term_args);
   if(is_wp_error($filter_terms))$filter_terms=array();
  }

  $uid='wpst-portfolio-'.$this->get_id();
  echo'<div id="'.esc_attr($uid).'" class="wpst-portfolio-shell" data-initial-count="'.esc_attr($initial_count).'">';

  if($show_filters && !empty($filter_terms)){
   echo'<div class="wpst-portfolio-filters" role="group" aria-label="Portföy kategorileri">';
   echo'<button type="button" class="wpst-portfolio-filter is-active" data-filter="all" aria-pressed="true">'.esc_html($s['filter_all_label']??'Tüm Projeler').'</button>';
   foreach($filter_terms as $term){
    echo'<button type="button" class="wpst-portfolio-filter" data-filter="'.esc_attr(sanitize_title($term->slug)).'" aria-pressed="false">'.esc_html($term->name).'</button>';
   }
   echo'</div>';
  }

  echo'<div class="wpst-ew-portfolio">';

  if(empty($items) && 'portfolio'===$source && $this->is_elementor_edit_context()){
   $counts=wp_count_posts(WPST_Portfolio_Manager::POST_TYPE);
   $publish=isset($counts->publish)?absint($counts->publish):0;
   $draft=isset($counts->draft)?absint($counts->draft):0;
   echo'<div class="wpst-portfolio-empty-editor" style="grid-column:1/-1;padding:22px;border:1px dashed #cbd5e1;border-radius:14px;background:#f8fafc;color:#475569">';
   echo'<strong>WPSoft Portföy verisi bulunamadı.</strong><br>';
   echo'<span style="font-size:12px">Yayınlanan: '.esc_html($publish).' · Taslak: '.esc_html($draft).' · Kategori: '.esc_html(isset($s['portfolio_category'])?$s['portfolio_category']:'all').'</span>';
   echo'</div>';
  }

  $index=0;
  foreach($items as $i){
   $index++;
   $link=is_array($i['url']??null)?$i['url']:array();
   $url=!empty($link['url'])?$link['url']:'#';
   $attrs=' href="'.esc_url($url).'"';

   if(!empty($link['is_external'])) $attrs.=' target="_blank"';

   $rels=array();
   if(!empty($link['nofollow'])) $rels[]='nofollow';
   if(!empty($link['is_external'])) $rels[]='noopener';
   if($rels) $attrs.=' rel="'.esc_attr(implode(' ',$rels)).'"';

   $slugs=array();
   if(!empty($i['_category_slugs']) && is_array($i['_category_slugs'])){
    $slugs=array_map('sanitize_title',$i['_category_slugs']);
   }elseif(!empty($i['category'])){
    $slugs=array(sanitize_title($i['category']));
   }
   $hidden=($enable_load_more && $index>$initial_count)?' is-wpst-portfolio-hidden':'';

   echo'<a class="wpst-portfolio-item'.$hidden.'" data-categories="'.esc_attr(implode(' ',$slugs)).'" data-portfolio-index="'.esc_attr($index).'"'.$attrs.'><div class="wpst-ew-project-media">';

   if(!empty($i['image']['url'])){
    echo'<img src="'.esc_url($i['image']['url']).'" alt="'.esc_attr($i['title']??'').'" loading="lazy" decoding="async">';
   }else{
    echo'<span>'.esc_html($s['placeholder_text']).'</span>';
   }

   echo'<i class="wpst-project-action" aria-hidden="true">'.(class_exists('WPST_Icon_Library')?WPST_Icon_Library::svg($s['action_icon'],array('size'=>17)):'↗').'</i></div>';
   echo'<div class="wpst-portfolio-copy">';

   if(!empty($i['category'])) echo'<small>'.esc_html($i['category']).'</small>';
   echo'<h3>'.esc_html($i['title']).'</h3></div></a>';
  }

  echo'</div>';

  if($enable_load_more && count($items)>$initial_count){
   echo'<div class="wpst-portfolio-more-wrap"><button type="button" class="wpst-portfolio-more">'.esc_html($s['load_more_label']??'Devamını Göster').'<span aria-hidden="true">↓</span></button></div>';
  }

  echo'</div>';
 }
}
