<?php
if(!defined('ABSPATH'))exit;

class WPST_Widget_Product_Showcase extends WPST_Elementor_Widget_Base{
 public function get_name(){return'wpsoft-product-showcase';}
 public function get_title(){return'WPSoft Product Showcase 2.0';}
 public function get_icon(){return'eicon-products';}

 protected function register_controls(){
  /* -------------------- CONTENT -------------------- */
  $this->start_controls_section('content',array('label'=>'İçerik · Ürünler'));

  $this->add_control('layout',array(
   'label'=>'Yerleşim',
   'type'=>\Elementor\Controls_Manager::SELECT,
   'default'=>'cards',
   'options'=>array(
    'cards'=>'Modern Kartlar',
    'minimal'=>'Minimal',
    'editorial'=>'Editorial',
    'compact'=>'Compact'
   ),
   'prefix_class'=>'wpst-product-layout-'
  ));

  $r=new \Elementor\Repeater();
  $r->add_control('image',array('label'=>'Ürün Görseli','type'=>\Elementor\Controls_Manager::MEDIA));
  $r->add_control('title',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Premium Ürün'));
  $r->add_control('meta',array('label'=>'Rozet / Etiket','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Yeni'));
  $r->add_control('description',array('label'=>'Kısa Açıklama','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'Modern ürün koleksiyonundan seçilmiş premium ürün.'));
  $r->add_control('price',array('label'=>'Fiyat','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'₺1.250'));
  $r->add_control('old_price',array('label'=>'Eski Fiyat','type'=>\Elementor\Controls_Manager::TEXT,'default'=>''));
  $r->add_control('url',array('label'=>'Ürün Bağlantısı','type'=>\Elementor\Controls_Manager::URL,'placeholder'=>'https://'));

  $this->add_control('items',array(
   'label'=>'Ürünler',
   'type'=>\Elementor\Controls_Manager::REPEATER,
   'fields'=>$r->get_controls(),
   'default'=>array(
    array('title'=>'Premium Ürün','meta'=>'Yeni','description'=>'Modern koleksiyonun öne çıkan seçimi.','price'=>'₺1.250'),
    array('title'=>'Modern Seri','meta'=>'Popüler','description'=>'Günlük kullanım için sade ve güçlü tasarım.','price'=>'₺980'),
    array('title'=>'Özel Seçim','meta'=>'Sınırlı','description'=>'Sınırlı üretim premium seri.','price'=>'₺1.490')
   ),
   'title_field'=>'{{{ title }}}'
  ));

  $this->add_control('action_text',array('label'=>'Aksiyon Yazısı','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Ürünü İncele'));
  $this->add_control('show_description',array('label'=>'Açıklamayı Göster','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes'));
  $this->add_control('show_badge',array('label'=>'Rozeti Göster','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes'));
  $this->add_control('show_action',array('label'=>'Aksiyonu Göster','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes'));

  $this->end_controls_section();

  /* -------------------- LAYOUT -------------------- */
  $this->start_controls_section('layout_controls',array('label'=>'Düzen'));

  $this->add_responsive_control('columns',array(
   'label'=>'Kolon',
   'type'=>\Elementor\Controls_Manager::SELECT,
   'default'=>'3','tablet_default'=>'2','mobile_default'=>'1',
   'options'=>array('1'=>'1','2'=>'2','3'=>'3','4'=>'4'),
   'selectors'=>array('{{WRAPPER}} .wpst-ew-product-showcase'=>'grid-template-columns:repeat({{VALUE}},minmax(0,1fr));')
  ));

  $this->add_responsive_control('gap',array(
   'label'=>'Kart Aralığı',
   'type'=>\Elementor\Controls_Manager::SLIDER,
   'range'=>array('px'=>array('min'=>0,'max'=>64)),
   'default'=>array('size'=>20),
   'selectors'=>array('{{WRAPPER}}'=>'--wpst-product-gap:{{SIZE}}px;')
  ));

  $this->add_responsive_control('image_height',array(
   'label'=>'Görsel Yüksekliği',
   'type'=>\Elementor\Controls_Manager::SLIDER,
   'size_units'=>array('px'),
   'range'=>array('px'=>array('min'=>120,'max'=>720)),
   'default'=>array('size'=>300,'unit'=>'px'),
   'tablet_default'=>array('size'=>260,'unit'=>'px'),
   'mobile_default'=>array('size'=>240,'unit'=>'px'),
   'selectors'=>array('{{WRAPPER}}'=>'--wpst-product-image-h:{{SIZE}}px;')
  ));

  $this->add_control('image_fit',array(
   'label'=>'Görsel Oturtma',
   'type'=>\Elementor\Controls_Manager::SELECT,
   'default'=>'cover',
   'options'=>array('cover'=>'Kapla','contain'=>'Sığdır'),
   'selectors'=>array('{{WRAPPER}} .wpst-product-media img'=>'object-fit:{{VALUE}};')
  ));

  $this->add_control('hover_effect',array(
   'label'=>'Hover Efekti',
   'type'=>\Elementor\Controls_Manager::SELECT,
   'default'=>'lift',
   'options'=>array(
    'none'=>'Yok',
    'lift'=>'Yukarı Kalk',
    'zoom'=>'Görsel Zoom',
    'border'=>'Border Vurgusu'
   ),
   'prefix_class'=>'wpst-product-hover-'
  ));

  $this->end_controls_section();

  /* -------------------- CARD STYLE -------------------- */
  $this->start_controls_section('card_style',array('label'=>'Biçim · Kart','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->wpst_signature_preset_control('product_preset');

  $this->add_control('surface_bg',array(
   'label'=>'Kart Arka Plan',
   'type'=>\Elementor\Controls_Manager::COLOR,
   'selectors'=>array('{{WRAPPER}}'=>'--wpst-product-bg:{{VALUE}};')
  ));
  $this->add_control('border_color',array(
   'label'=>'Border Rengi',
   'type'=>\Elementor\Controls_Manager::COLOR,
   'selectors'=>array('{{WRAPPER}}'=>'--wpst-product-border:{{VALUE}};')
  ));
  $this->add_control('hover_border_color',array(
   'label'=>'Hover Border',
   'type'=>\Elementor\Controls_Manager::COLOR,
   'selectors'=>array('{{WRAPPER}}'=>'--wpst-product-hover-border:{{VALUE}};')
  ));
  $this->add_responsive_control('card_radius',array(
   'label'=>'Kart Köşesi',
   'type'=>\Elementor\Controls_Manager::SLIDER,
   'range'=>array('px'=>array('min'=>0,'max'=>60)),
   'default'=>array('size'=>24),
   'selectors'=>array('{{WRAPPER}}'=>'--wpst-product-radius:{{SIZE}}px;')
  ));
  $this->add_responsive_control('card_padding',array(
   'label'=>'Kart İç Boşluk',
   'type'=>\Elementor\Controls_Manager::DIMENSIONS,
   'size_units'=>array('px'),
   'selectors'=>array('{{WRAPPER}} .wpst-product-content'=>'padding:{{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;')
  ));
  $this->add_control('card_shadow',array(
   'label'=>'Kart Gölgesi',
   'type'=>\Elementor\Controls_Manager::SELECT,
   'default'=>'soft',
   'options'=>array('none'=>'Yok','soft'=>'Soft','medium'=>'Medium'),
   'prefix_class'=>'wpst-product-shadow-'
  ));
  $this->end_controls_section();

  /* -------------------- MEDIA -------------------- */
  $this->start_controls_section('media_style',array('label'=>'Biçim · Görsel','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_responsive_control('image_radius',array(
   'label'=>'Görsel Köşesi',
   'type'=>\Elementor\Controls_Manager::SLIDER,
   'range'=>array('px'=>array('min'=>0,'max'=>50)),
   'default'=>array('size'=>18),
   'selectors'=>array('{{WRAPPER}}'=>'--wpst-product-image-radius:{{SIZE}}px;')
  ));
  $this->add_control('media_bg',array(
   'label'=>'Görsel Alanı Arka Plan',
   'type'=>\Elementor\Controls_Manager::COLOR,
   'selectors'=>array('{{WRAPPER}}'=>'--wpst-product-media-bg:{{VALUE}};')
  ));
  $this->end_controls_section();

  /* -------------------- TYPOGRAPHY -------------------- */
  $this->start_controls_section('text_style',array('label'=>'Biçim · Yazılar','tab'=>\Elementor\Controls_Manager::TAB_STYLE));

  $this->add_control('title_color',array('label'=>'Başlık Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}}'=>'--wpst-product-title:{{VALUE}};')));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'title_typography','label'=>'Başlık Tipografi','selector'=>'{{WRAPPER}} .wpst-product-title'));

  $this->add_control('desc_color',array('label'=>'Açıklama Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}}'=>'--wpst-product-desc:{{VALUE}};')));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'desc_typography','label'=>'Açıklama Tipografi','selector'=>'{{WRAPPER}} .wpst-product-desc'));

  $this->add_control('price_color',array('label'=>'Fiyat Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}}'=>'--wpst-product-price:{{VALUE}};')));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'price_typography','label'=>'Fiyat Tipografi','selector'=>'{{WRAPPER}} .wpst-product-price'));

  $this->add_control('badge_bg',array('label'=>'Rozet Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}}'=>'--wpst-product-badge-bg:{{VALUE}};')));
  $this->add_control('badge_color',array('label'=>'Rozet Yazı Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}}'=>'--wpst-product-badge-color:{{VALUE}};')));

  $this->end_controls_section();

  /* -------------------- ACTION -------------------- */
  $this->start_controls_section('action_style',array('label'=>'Biçim · Aksiyon','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('action_color',array('label'=>'Aksiyon Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}}'=>'--wpst-product-action:{{VALUE}};')));
  $this->add_control('action_hover_color',array('label'=>'Hover Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}}'=>'--wpst-product-action-hover:{{VALUE}};')));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'action_typography','label'=>'Aksiyon Tipografi','selector'=>'{{WRAPPER}} .wpst-product-action'));
  $this->end_controls_section();

  $this->standard_responsive_controls();
 }

 protected function render(){
  $s=$this->get_settings_for_display();
  $items=!empty($s['items'])&&is_array($s['items'])?$s['items']:array();

  echo '<div class="wpst-ew-product-showcase" data-layout="'.esc_attr($s['layout']??'cards').'">';

  foreach($items as $index=>$i){
   $title=isset($i['title'])?$i['title']:'';
   $url=!empty($i['url']['url'])?$i['url']['url']:'#';
   $target=!empty($i['url']['is_external'])?' target="_blank"':'';
   $nofollow=!empty($i['url']['nofollow'])?' rel="nofollow"':'';

   echo '<article class="wpst-product-card">';

   if(!empty($i['image']['url'])){
    echo '<a class="wpst-product-media" href="'.esc_url($url).'"'.$target.$nofollow.'>';
    echo '<img src="'.esc_url($i['image']['url']).'" alt="'.esc_attr($title).'" loading="lazy" decoding="async">';
    echo '</a>';
   }else{
    echo '<div class="wpst-product-media is-placeholder" aria-hidden="true"><span>'.esc_html__('Görsel','wpsoft-site-tools').'</span></div>';
   }

   echo '<div class="wpst-product-content">';

   if('yes'===($s['show_badge']??'yes') && !empty($i['meta'])){
    echo '<small class="wpst-product-badge">'.esc_html($i['meta']).'</small>';
   }

   echo '<h3 class="wpst-product-title"><a href="'.esc_url($url).'"'.$target.$nofollow.'>'.esc_html($title).'</a></h3>';

   if('yes'===($s['show_description']??'yes') && !empty($i['description'])){
    echo '<p class="wpst-product-desc">'.esc_html($i['description']).'</p>';
   }

   echo '<div class="wpst-product-bottom">';
   echo '<div class="wpst-product-prices">';
   if(!empty($i['price'])) echo '<b class="wpst-product-price">'.esc_html($i['price']).'</b>';
   if(!empty($i['old_price'])) echo '<del class="wpst-product-old-price">'.esc_html($i['old_price']).'</del>';
   echo '</div>';

   if('yes'===($s['show_action']??'yes')){
    echo '<a class="wpst-product-action" href="'.esc_url($url).'"'.$target.$nofollow.'><span>'.esc_html($s['action_text']).'</span><i class="wpst-native-arrow">'.(class_exists('WPST_Icon_Library')?WPST_Icon_Library::svg('arrow-right',array('size'=>15)):'→').'</i></a>';
   }
   echo '</div>';

   echo '</div></article>';
  }

  echo '</div>';
 }
}
