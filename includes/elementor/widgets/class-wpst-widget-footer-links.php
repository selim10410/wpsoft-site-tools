<?php
if(!defined('ABSPATH'))exit;
class WPST_Widget_Footer_Links extends WPST_Elementor_Widget_Base{
 public function get_name(){return'wpsoft-footer-links';}
 public function get_title(){return'WPSoft Footer · Links 2.0';}
 public function get_icon(){return'eicon-editor-list-ul';}
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'Bağlantılar'));
  $this->add_control('title',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Hızlı Bağlantılar'));
  $this->add_control('show_title',array(
   'label'=>'Başlığı Göster',
   'type'=>\Elementor\Controls_Manager::SWITCHER,
   'return_value'=>'yes',
   'default'=>'yes'
  ));
  $r=new \Elementor\Repeater();
  $r->add_control('text',array('label'=>'Metin','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Hakkımızda'));
  $r->add_control('url',array('label'=>'Link','type'=>\Elementor\Controls_Manager::URL,'default'=>array('url'=>'#')));
  $r->add_control('badge',array('label'=>'Badge','type'=>\Elementor\Controls_Manager::TEXT,'default'=>''));
  $r->add_control('wpst_icon',array('label'=>'WPSoft Icon','type'=>\Elementor\Controls_Manager::SELECT2,'options'=>class_exists('WPST_Icon_Library')?WPST_Icon_Library::options():array(),'default'=>'arrow-up-right','label_block'=>true));
  $this->add_control('items',array('label'=>'Linkler','type'=>\Elementor\Controls_Manager::REPEATER,'fields'=>$r->get_controls(),'default'=>array(
   array('text'=>'Hakkımızda','url'=>array('url'=>'#'),'wpst_icon'=>'arrow-up-right'),
   array('text'=>'Hizmetler','url'=>array('url'=>'#'),'wpst_icon'=>'arrow-up-right'),
   array('text'=>'Projeler','url'=>array('url'=>'#'),'wpst_icon'=>'arrow-up-right'),
   array('text'=>'İletişim','url'=>array('url'=>'#'),'wpst_icon'=>'arrow-up-right')
  ),'title_field'=>'{{{ text }}}'));
  $this->add_control('style_preset',array('label'=>'Stil','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'clean','options'=>array('clean'=>'Clean','line'=>'Line','soft'=>'Soft','compact'=>'Compact'),'prefix_class'=>'wpst-footer-links-style-'));
  $this->add_responsive_control('layout_direction',array(
   'label'=>'Link Yerleşimi',
   'type'=>\Elementor\Controls_Manager::SELECT,
   'default'=>'grid',
   'tablet_default'=>'grid',
   'mobile_default'=>'grid',
   'options'=>array(
    'grid'=>'Dikey',
    'flex'=>'Yatay'
   ),
   'selectors'=>array(
    '{{WRAPPER}} .wpst-ew-footer-links ul'=>'display:{{VALUE}};'
   )
  ));
  $this->add_responsive_control('horizontal_wrap',array(
   'label'=>'Yatayda Satıra Geçiş',
   'type'=>\Elementor\Controls_Manager::SELECT,
   'default'=>'wrap',
   'tablet_default'=>'wrap',
   'mobile_default'=>'wrap',
   'options'=>array(
    'wrap'=>'Satıra Geçebilir',
    'nowrap'=>'Tek Satır'
   ),
   'selectors'=>array(
    '{{WRAPPER}} .wpst-ew-footer-links ul'=>'flex-wrap:{{VALUE}};'
   ),
   'condition'=>array('layout_direction'=>'flex')
  ));
  $this->add_responsive_control('columns',array(
   'label'=>'Kolon',
   'type'=>\Elementor\Controls_Manager::SELECT,
   'default'=>'1','tablet_default'=>'1','mobile_default'=>'1',
   'options'=>array('1'=>'1','2'=>'2'),
   'selectors'=>array('{{WRAPPER}} .wpst-ew-footer-links ul'=>'grid-template-columns:repeat({{VALUE}},minmax(0,1fr));'),
   'condition'=>array('layout_direction'=>'grid')
  ));
  $this->add_responsive_control('gap',array('label'=>'Link Aralığı','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>30)),'selectors'=>array('{{WRAPPER}} .wpst-ew-footer-links ul'=>'gap:{{SIZE}}px;')));
  $this->add_responsive_control('link_min_height',array('label'=>'Link Yüksekliği','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>34,'max'=>68)),'selectors'=>array('{{WRAPPER}} .wpst-ew-footer-links a'=>'min-height:{{SIZE}}px;')));
  $this->add_responsive_control('align',array(
   'label'=>'Hizalama',
   'type'=>\Elementor\Controls_Manager::CHOOSE,
   'options'=>array(
    'flex-start'=>array('title'=>'Sol','icon'=>'eicon-text-align-left'),
    'center'=>array('title'=>'Orta','icon'=>'eicon-text-align-center'),
    'flex-end'=>array('title'=>'Sağ','icon'=>'eicon-text-align-right')
   ),
   'default'=>'flex-start',
   'selectors'=>array(
    '{{WRAPPER}} .wpst-ew-footer-links ul'=>'justify-content:{{VALUE}};'
   )
  ));

  $this->add_control('show_icons',array(
   'label'=>'İkonları Göster',
   'type'=>\Elementor\Controls_Manager::SWITCHER,
   'return_value'=>'yes',
   'default'=>'yes',
   'prefix_class'=>'wpst-footer-links-icons-'
  ));

  $this->add_control('icon_position',array(
   'label'=>'İkon Konumu',
   'type'=>\Elementor\Controls_Manager::SELECT,
   'default'=>'right',
   'options'=>array(
    'left'=>'Solda',
    'right'=>'Sağda'
   ),
   'prefix_class'=>'wpst-footer-links-icon-',
   'condition'=>array('show_icons'=>'yes')
  ));

  $this->add_control('hover_style',array(
   'label'=>'Hover Efekti',
   'type'=>\Elementor\Controls_Manager::SELECT,
   'default'=>'slide',
   'options'=>array(
    'none'=>'Yok',
    'slide'=>'Hafif Kaydır',
    'underline'=>'Alt Çizgi',
    'background'=>'Arka Plan',
    'pill'=>'Pill'
   ),
   'prefix_class'=>'wpst-footer-links-hover-'
  ));

  $this->add_control('separator_style',array(
   'label'=>'Ayırıcı',
   'type'=>\Elementor\Controls_Manager::SELECT,
   'default'=>'none',
   'options'=>array(
    'none'=>'Yok',
    'line'=>'Çizgi',
    'dot'=>'Nokta'
   ),
   'prefix_class'=>'wpst-footer-links-separator-'
  ));

  $this->add_control('active_style',array(
   'label'=>'Aktif Link Stili',
   'type'=>\Elementor\Controls_Manager::SELECT,
   'default'=>'accent',
   'options'=>array(
    'none'=>'Yok',
    'accent'=>'Vurgu Rengi',
    'underline'=>'Alt Çizgi',
    'pill'=>'Pill'
   ),
   'prefix_class'=>'wpst-footer-links-active-'
  ));

  $this->add_control('active_match_mode',array(
   'label'=>'Aktif Link Algılama',
   'type'=>\Elementor\Controls_Manager::SELECT,
   'default'=>'exact',
   'options'=>array(
    'exact'=>'Tam Eşleşme',
    'parent'=>'Üst Yol / Alt Sayfalar'
   ),
   'description'=>'Üst Yol seçilirse /hizmetler linki /hizmetler/web-tasarim gibi alt sayfalarda da aktif kalır.'
  ));

  $this->add_control('open_external_new_tab',array(
   'label'=>'Harici Linkleri Yeni Sekmede Aç',
   'type'=>\Elementor\Controls_Manager::SWITCHER,
   'return_value'=>'yes',
   'default'=>''
  ));

  $this->add_control('icon_visibility',array(
   'label'=>'İkon Görünümü',
   'type'=>\Elementor\Controls_Manager::SELECT,
   'default'=>'hover',
   'options'=>array(
    'always'=>'Her Zaman',
    'hover'=>'Hover’da',
    'subtle'=>'Her Zaman · Soluk'
   ),
   'prefix_class'=>'wpst-footer-links-icon-visibility-',
   'condition'=>array('show_icons'=>'yes')
  ));

  $this->end_controls_section();

  $this->start_controls_section('style_title',array(
   'label'=>'Başlık Biçimi',
   'tab'=>\Elementor\Controls_Manager::TAB_STYLE,
   'condition'=>array('show_title'=>'yes')
  ));

  $this->add_control('title_color',array(
   'label'=>'Başlık Rengi',
   'type'=>\Elementor\Controls_Manager::COLOR,
   'selectors'=>array('{{WRAPPER}} .wpst-ew-footer-links h4'=>'color:{{VALUE}}!important;')
  ));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array(
   'name'=>'footer_links_title_typography',
   'selector'=>'{{WRAPPER}} .wpst-ew-footer-links h4'
  ));
  $this->add_responsive_control('title_spacing',array(
   'label'=>'Başlık Alt Boşluk',
   'type'=>\Elementor\Controls_Manager::SLIDER,
   'range'=>array('px'=>array('min'=>0,'max'=>50)),
   'selectors'=>array('{{WRAPPER}} .wpst-ew-footer-links h4'=>'margin-bottom:{{SIZE}}px!important;')
  ));
  $this->end_controls_section();

  $this->start_controls_section('style_links',array(
   'label'=>'Link Biçimi',
   'tab'=>\Elementor\Controls_Manager::TAB_STYLE
  ));

  $this->add_control('link_color',array(
   'label'=>'Link Rengi',
   'type'=>\Elementor\Controls_Manager::COLOR,
   'selectors'=>array('{{WRAPPER}} .wpst-ew-footer-links'=>'--wpst-footer-link-color:{{VALUE}}!important;')
  ));

  $this->add_control('hover_color',array(
   'label'=>'Hover Rengi',
   'type'=>\Elementor\Controls_Manager::COLOR,
   'selectors'=>array('{{WRAPPER}} .wpst-ew-footer-links'=>'--wpst-footer-link-hover:{{VALUE}};')
  ));

  $this->add_control('active_color',array(
   'label'=>'Aktif Link Rengi',
   'type'=>\Elementor\Controls_Manager::COLOR,
   'selectors'=>array('{{WRAPPER}} .wpst-ew-footer-links'=>'--wpst-footer-link-active:{{VALUE}};')
  ));

  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array(
   'name'=>'footer_links_typography',
   'selector'=>'{{WRAPPER}} .wpst-ew-footer-links a'
  ));

  $this->add_control('link_bg',array(
   'label'=>'Link Arka Planı',
   'type'=>\Elementor\Controls_Manager::COLOR,
   'selectors'=>array('{{WRAPPER}} .wpst-ew-footer-links'=>'--wpst-footer-link-bg:{{VALUE}};')
  ));

  $this->add_control('link_hover_bg',array(
   'label'=>'Hover Arka Planı',
   'type'=>\Elementor\Controls_Manager::COLOR,
   'selectors'=>array('{{WRAPPER}} .wpst-ew-footer-links'=>'--wpst-footer-link-hover-bg:{{VALUE}};')
  ));

  $this->add_control('separator_color',array(
   'label'=>'Ayırıcı Rengi',
   'type'=>\Elementor\Controls_Manager::COLOR,
   'selectors'=>array('{{WRAPPER}} .wpst-ew-footer-links'=>'--wpst-footer-link-separator:{{VALUE}};')
  ));

  $this->add_control('badge_bg',array(
   'label'=>'Badge Arka Planı',
   'type'=>\Elementor\Controls_Manager::COLOR,
   'selectors'=>array('{{WRAPPER}} .wpst-ew-footer-links'=>'--wpst-footer-badge-bg:{{VALUE}};')
  ));

  $this->add_control('badge_color',array(
   'label'=>'Badge Yazı Rengi',
   'type'=>\Elementor\Controls_Manager::COLOR,
   'selectors'=>array('{{WRAPPER}} .wpst-ew-footer-links'=>'--wpst-footer-badge-color:{{VALUE}};')
  ));

  $this->add_responsive_control('icon_size',array(
   'label'=>'İkon Boyutu',
   'type'=>\Elementor\Controls_Manager::SLIDER,
   'range'=>array('px'=>array('min'=>10,'max'=>28)),
   'default'=>array('unit'=>'px','size'=>13),
   'selectors'=>array('{{WRAPPER}} .wpst-ew-footer-links a i'=>'--wpst-footer-link-icon-size:{{SIZE}}px;')
  ));

  $this->add_responsive_control('link_padding_x',array(
   'label'=>'Yatay İç Boşluk',
   'type'=>\Elementor\Controls_Manager::SLIDER,
   'range'=>array('px'=>array('min'=>0,'max'=>28)),
   'selectors'=>array('{{WRAPPER}} .wpst-ew-footer-links a'=>'--wpst-footer-link-pad-x:{{SIZE}}px;')
  ));

  $this->add_responsive_control('link_padding_y',array(
   'label'=>'Dikey İç Boşluk',
   'type'=>\Elementor\Controls_Manager::SLIDER,
   'range'=>array('px'=>array('min'=>0,'max'=>24)),
   'selectors'=>array('{{WRAPPER}} .wpst-ew-footer-links a'=>'--wpst-footer-link-pad-y:{{SIZE}}px;')
  ));

  $this->add_responsive_control('link_radius',array(
   'label'=>'Link Köşesi',
   'type'=>\Elementor\Controls_Manager::SLIDER,
   'range'=>array('px'=>array('min'=>0,'max'=>40)),
   'selectors'=>array('{{WRAPPER}} .wpst-ew-footer-links a'=>'--wpst-footer-link-radius:{{SIZE}}px;')
  ));

  $this->add_responsive_control('icon_gap',array(
   'label'=>'İkon / Metin Aralığı',
   'type'=>\Elementor\Controls_Manager::SLIDER,
   'range'=>array('px'=>array('min'=>0,'max'=>24)),
   'selectors'=>array('{{WRAPPER}} .wpst-ew-footer-links a'=>'--wpst-footer-link-icon-gap:{{SIZE}}px;')
  ));

  $this->end_controls_section();
  $this->standard_responsive_controls();
 }
 protected function render(){
  $s=$this->get_settings_for_display();
  $show_icons='yes'===($s['show_icons']??'yes');
  $show_title='yes'===($s['show_title']??'yes') && ''!==trim((string)($s['title']??''));

  echo'<nav class="wpst-ew-footer-links'.($show_icons?' has-icons':' no-icons').'" data-icons="'.($show_icons?'yes':'no').'">';
  if($show_title)echo'<h4>'.esc_html($s['title']).'</h4>';
  echo'<ul>';

  $request_path='/'.ltrim((string)($GLOBALS['wp']->request??''),'/');
  $request_path=untrailingslashit($request_path);
  if(''===$request_path)$request_path='/';

  $match_mode=$s['active_match_mode']??'exact';
  $site_host=strtolower((string)wp_parse_url(home_url(),PHP_URL_HOST));

  foreach((array)$s['items'] as $index=>$item){
   $url_data=is_array($item['url']??null)?$item['url']:array();
   $url=!empty($url_data['url'])?$url_data['url']:'#';

   $link_path=(string)wp_parse_url($url,PHP_URL_PATH);
   if(''===$link_path && 0===strpos($url,'/'))$link_path=$url;
   $link_path='/'.ltrim($link_path,'/');
   $link_path=untrailingslashit($link_path);
   if(''===$link_path)$link_path='/';

   $link_host=strtolower((string)wp_parse_url($url,PHP_URL_HOST));
   $is_internal=empty($link_host) || $link_host===$site_host;

   $is_current=false;
   if('#'!==$url && $is_internal){
    if('parent'===$match_mode){
     if('/'===$link_path){
      $is_current='/'===$request_path;
     }else{
      $is_current=($request_path===$link_path || 0===strpos($request_path,$link_path.'/'));
     }
    }else{
     $is_current=($request_path===$link_path);
    }
   }

   $li_classes=$is_current?' class="is-current"':'';

   $attrs=array(
    'href'=>esc_url($url),
   );

   // Per-link Elementor URL settings have first priority.
   if(!empty($url_data['is_external']))$attrs['target']='_blank';
   if(!empty($url_data['nofollow']))$attrs['rel']='nofollow';

   // Legacy global switch remains as fallback for external URLs.
   if(empty($attrs['target']) && 'yes'===($s['open_external_new_tab']??'') && preg_match('~^https?://~i',$url) && !$is_internal){
    $attrs['target']='_blank';
   }

   if(!empty($attrs['target']) && '_blank'===$attrs['target']){
    $rel=isset($attrs['rel'])?$attrs['rel']:'';
    if(false===strpos($rel,'noopener'))$rel=trim($rel.' noopener');
    if(false===strpos($rel,'noreferrer'))$rel=trim($rel.' noreferrer');
    $attrs['rel']=$rel;
   }

   if($is_current)$attrs['aria-current']='page';

   // Elementor URL Custom Attributes support (key|value, comma separated).
   if(!empty($url_data['custom_attributes']) && is_string($url_data['custom_attributes'])){
    $customs=array_filter(array_map('trim',explode(',',$url_data['custom_attributes'])));
    foreach($customs as $custom){
     $pair=array_map('trim',explode('|',$custom,2));
     if(empty($pair[0]))continue;
     $key=sanitize_key($pair[0]);
     if(''===$key || in_array($key,array('href'),true))continue;
     $attrs[$key]=isset($pair[1])?$pair[1]:'';
    }
   }

   $attr_html='';
   foreach($attrs as $key=>$value){
    if(''===(string)$key)continue;
    $attr_html.=' '.esc_attr($key).'="'.esc_attr($value).'"';
   }

   echo'<li'.$li_classes.'><a'.$attr_html.'>';
   if($show_icons && 'left'===($s['icon_position']??'right'))echo'<i>'.(class_exists('WPST_Icon_Library')?WPST_Icon_Library::svg(!empty($item['wpst_icon'])?$item['wpst_icon']:'arrow-up-right',array('size'=>13)):'↗').'</i>';
   echo'<span>'.esc_html($item['text']??'').'</span>';
   if(!empty($item['badge']))echo'<small>'.esc_html($item['badge']).'</small>';
   if($show_icons && 'right'===($s['icon_position']??'right'))echo'<i>'.(class_exists('WPST_Icon_Library')?WPST_Icon_Library::svg(!empty($item['wpst_icon'])?$item['wpst_icon']:'arrow-up-right',array('size'=>13)):'↗').'</i>';
   echo'</a></li>';
  }

  echo'</ul></nav>';
 }
}
