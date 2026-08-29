<?php
if(!defined('ABSPATH'))exit;

final class WPST_Diagnostics {

    public static function init(){
        add_action('admin_menu',array(__CLASS__,'menu'),25);
        add_action('admin_enqueue_scripts',array(__CLASS__,'assets'));
        add_action('admin_post_wpst_export_diagnostics',array(__CLASS__,'export'));
    }

    public static function menu(){
        add_submenu_page(
            'wpsoft-site-tools',
            'WPSoft Sistem Durumu',
            'Sistem Durumu',
            'manage_options',
            'wpsoft-system-status',
            array(__CLASS__,'page')
        );
    }

    public static function assets($hook){
        if('wpsoft-site-tools_page_wpsoft-system-status'!==$hook)return;
        wp_enqueue_style('wpst-diagnostics',WPST_URL.'assets/css/diagnostics.css',array(),WPST_VERSION);
    }

    private static function bytes($value){
        $value=trim((string)$value);
        if(''===$value)return 0;
        $last=strtolower(substr($value,-1));
        $num=(float)$value;
        if('g'===$last)$num*=1024;
        if('m'===$last)$num*=1024;
        if('k'===$last)$num*=1024;
        return (int)round($num*1024);
    }

    private static function human_bytes($bytes){
        $bytes=(int)$bytes;
        if($bytes>=1073741824)return round($bytes/1073741824,1).' GB';
        if($bytes>=1048576)return round($bytes/1048576,1).' MB';
        if($bytes>=1024)return round($bytes/1024,1).' KB';
        return $bytes.' B';
    }

    private static function environment(){
        global $wp_version;
        $theme=wp_get_theme();
        $parent=$theme->parent();
        $memory=defined('WP_MEMORY_LIMIT')?WP_MEMORY_LIMIT:ini_get('memory_limit');
        $max_memory=defined('WP_MAX_MEMORY_LIMIT')?WP_MAX_MEMORY_LIMIT:$memory;
        return array(
            'WordPress'=>$wp_version,
            'PHP'=>PHP_VERSION,
            'WPSoft Site Tools'=>WPST_VERSION,
            'WPSoft Lisans'=>class_exists('WPST_License')?(WPST_License::is_active()?(WPST_License::is_grace()?'Grace Period':'Aktif'):'Salt Okunur'):'—',
            'Elementor'=>defined('ELEMENTOR_VERSION')?ELEMENTOR_VERSION:'Etkin değil',
            'Tema'=>$theme->get('Name').' '.$theme->get('Version'),
            'Parent Tema'=>$parent?$parent->get('Name').' '.$parent->get('Version'):'—',
            'Tema Profili'=>class_exists('WPST_Theme_Compatibility')?ucfirst(WPST_Theme_Compatibility::detect()):'Generic',
            'WP Memory'=>$memory,
            'WP Max Memory'=>$max_memory,
            'PHP Memory'=>ini_get('memory_limit'),
            'PHP Max Input Vars'=>ini_get('max_input_vars'),
            'PHP Max Execution'=>ini_get('max_execution_time').' sn',
            'Upload Max'=>ini_get('upload_max_filesize'),
            'Debug'=>defined('WP_DEBUG')&&WP_DEBUG?'Açık':'Kapalı',
            'Multisite'=>is_multisite()?'Evet':'Hayır'
        );
    }

    private static function registered_widgets(){
        $out=array();
        if(!did_action('elementor/loaded')||!class_exists('\Elementor\Plugin'))return $out;
        try{
            $manager=\Elementor\Plugin::instance()->widgets_manager;
            if(!$manager||!method_exists($manager,'get_widget_types'))return $out;
            foreach((array)$manager->get_widget_types() as $widget){
                if(!is_object($widget)||!method_exists($widget,'get_name'))continue;
                $name=$widget->get_name();
                if(0===strpos($name,'wpsoft-'))$out[$name]=method_exists($widget,'get_title')?$widget->get_title():$name;
            }
        }catch(\Throwable $e){}
        return $out;
    }

    private static function library_payload(){
        if(!class_exists('WPST_Template_Library')||!method_exists('WPST_Template_Library','editor_payload'))return array();
        try{return (array)WPST_Template_Library::editor_payload();}catch(\Throwable $e){return array();}
    }

    private static function recursive_widget_refs($data,&$refs){
        if(!is_array($data))return;
        if(!empty($data['widgetType']))$refs[]=(string)$data['widgetType'];
        foreach($data as $value)if(is_array($value))self::recursive_widget_refs($value,$refs);
    }

    private static function url_to_path($url){
        if(!$url)return '';
        $url=(string)$url;
        $base=trailingslashit(WPST_URL);
        if(0===strpos($url,$base)){
            $rel=ltrim(substr($url,strlen($base)),'/');
            return WPST_PATH.$rel;
        }
        return '';
    }

    private static function add(&$checks,$status,$title,$message,$group='Genel',$meta=array()){
        $checks[]=array(
            'status'=>$status,
            'title'=>$title,
            'message'=>$message,
            'group'=>$group,
            'meta'=>$meta
        );
    }

    public static function run(){
        $checks=array();
        $settings=get_option('wpst_settings',array());

        // Core files.
        $files=array(
            'Ana eklenti dosyası'=>'wpsoft-site-tools.php',
            'Plugin çekirdeği'=>'includes/class-wpst-plugin.php',
            'Elementor entegrasyonu'=>'includes/elementor/class-wpst-elementor.php',
            'Template Library'=>'includes/class-wpst-template-library.php',
            'Header/Footer'=>'includes/class-wpst-header-footer-templates.php',
            'Mega Menü'=>'includes/class-wpst-mega-menu.php',
            'Şablon Yöneticisi'=>'includes/class-wpst-template-manager.php',
            'Performance'=>'includes/class-wpst-performance.php',
            'Tema Uyumluluğu'=>'includes/class-wpst-theme-compatibility.php'
        );
        foreach($files as $label=>$rel){
            self::add($checks,file_exists(WPST_PATH.$rel)?'pass':'error',$label,file_exists(WPST_PATH.$rel)?'Dosya mevcut.':'Dosya bulunamadı: '.$rel,'Dosya Bütünlüğü');
        }

        // PHP/WP environment.
        self::add($checks,version_compare(PHP_VERSION,'7.4','>=')?'pass':'error','PHP sürümü','PHP '.PHP_VERSION.' kullanılıyor.','Sunucu');
        $mem=self::bytes(defined('WP_MEMORY_LIMIT')?WP_MEMORY_LIMIT:ini_get('memory_limit'));
        self::add($checks,$mem>=134217728?'pass':'warn','WordPress bellek limiti','Mevcut limit: '.self::human_bytes($mem).'. Elementor için 128 MB+ önerilir.','Sunucu');

        // Elementor state.
        if(did_action('elementor/loaded')&&defined('ELEMENTOR_VERSION')){
            self::add($checks,'pass','Elementor','Elementor '.ELEMENTOR_VERSION.' algılandı.','Elementor');
        }else{
            self::add($checks,'error','Elementor','Elementor etkin değil veya yüklenemedi. WPSoft Elementor widgetları çalışmaz.','Elementor');
        }

        $registered=self::registered_widgets();
        self::add($checks,count($registered)?'pass':'warn','WPSoft Elementor widgetları',count($registered).' WPSoft widgetı Elementor’a kayıtlı.','Elementor',array('count'=>count($registered)));

        // PHP widget classes/files audit.
        $widget_files=glob(WPST_PATH.'includes/elementor/widgets/*.php');
        $class_count=0;$missing_class_files=array();
        foreach((array)$widget_files as $file){
            if('class-wpst-widget-base.php'===basename($file))continue;
            $contents=@file_get_contents($file);
            if(false===$contents)continue;
            if(preg_match_all('/class\s+(WPST_Widget_[A-Za-z0-9_]+)\s+extends/',$contents,$matches)){
                $class_count+=count($matches[1]);
            }else{
                $missing_class_files[]=basename($file);
            }
        }
        self::add($checks,$missing_class_files?'warn':'pass','Widget dosya/class kontrolü',$class_count.' widget class tanımı bulundu.'.($missing_class_files?' Class bulunamayan dosya: '.implode(', ',$missing_class_files):''),'Widget Bütünlüğü');

        // Template library audit.
        $payload=self::library_payload();
        $library_widgets=isset($payload['widgets'])&&is_array($payload['widgets'])?$payload['widgets']:array();
        self::add($checks,$library_widgets?'pass':'warn','WPSoft Şablonlar widget kütüphanesi',count($library_widgets).' widget kartı kütüphanede bulundu.','Şablon Kütüphanesi',array('count'=>count($library_widgets)));

        $library_names=array();
        $preview_missing=array();
        foreach($library_widgets as $item){
            if(!empty($item['data']['widgetType']))$library_names[$item['data']['widgetType']]=true;
            if(!empty($item['preview_image'])){
                $path=self::url_to_path($item['preview_image']);
                if($path&&!file_exists($path))$preview_missing[]=$item['key'].' → '.basename($path);
            }
        }
        $missing_library=array();
        foreach($registered as $name=>$title)if(!isset($library_names[$name]))$missing_library[]=$name;
        self::add($checks,$missing_library?'error':'pass','Elementor ↔ WPSoft Şablonlar senkronizasyonu',$missing_library?'Kütüphanede eksik widgetlar: '.implode(', ',$missing_library):'Elementor’a kayıtlı WPSoft widgetları kütüphaneyle senkron.','Widget Bütünlüğü',array('missing'=>$missing_library));

        self::add($checks,$preview_missing?'warn':'pass','Widget önizleme görselleri',$preview_missing?'Eksik preview: '.implode(', ',$preview_missing):'Kontrol edilen widget preview dosyaları mevcut.','Şablon Kütüphanesi');

        // All library widget references against Elementor registry.
        $refs=array();
        foreach(array('widgets','sections','pages','headers','footers','mega_menus') as $kind){
            if(!empty($payload[$kind]))self::recursive_widget_refs($payload[$kind],$refs);
        }
        $refs=array_values(array_unique(array_filter($refs)));
        $broken_refs=array();
        if($registered){
            foreach($refs as $ref){
                if(0===strpos($ref,'wpsoft-')&&!isset($registered[$ref]))$broken_refs[]=$ref;
            }
        }
        self::add($checks,$broken_refs?'error':'pass','Şablon widget referansları',$broken_refs?'Elementor’da bulunmayan widget referansları: '.implode(', ',$broken_refs):count($refs).' benzersiz widget referansı kontrol edildi; bozuk WPSoft referansı bulunmadı.','Şablon Bütünlüğü');

        // Elementor library template IDs.
        $template_fields=array(
            'header_template'=>'Header masaüstü',
            'mobile_header_template'=>'Header mobil',
            'footer_template'=>'Footer masaüstü',
            'mobile_footer_template'=>'Footer mobil',
            'blog_archive_template'=>'Blog arşiv',
            'blog_single_template'=>'Tek yazı',
            'theme_404_template'=>'404',
            'theme_search_template'=>'Arama',
            'theme_category_template'=>'Kategori',
            'theme_tag_template'=>'Etiket',
            'theme_author_template'=>'Yazar'
        );
        foreach($template_fields as $key=>$label){
            $id=!empty($settings[$key])?absint($settings[$key]):0;
            if(!$id)continue;
            $post=get_post($id);
            self::add($checks,($post&&'elementor_library'===$post->post_type)?'pass':'error',$label.' şablon ID',($post&&'elementor_library'===$post->post_type)?'#'.$id.' geçerli Elementor şablonu.':'#'.$id.' bulunamadı veya Elementor şablonu değil.','Aktif Yapı');
        }

        // Blog page.
        if(!empty($settings['blog_archive_enabled'])){
            $page_id=!empty($settings['blog_page_id'])?absint($settings['blog_page_id']):0;
            $page=$page_id?get_post($page_id):null;
            self::add($checks,($page&&'page'===$page->post_type)?'pass':'error','Blog sayfası',($page&&'page'===$page->post_type)?'#'.$page_id.' '.get_the_title($page_id):'Blog arşivi aktif ancak geçerli Blog sayfası seçilmemiş.','Aktif Yapı');
        }

        // Header/Footer live layout JSON.
        foreach(array('header','footer') as $type){
            $raw=isset($settings[$type.'_layout'])?$settings[$type.'_layout']:'';
            if(is_string($raw)){
                $decoded=json_decode($raw,true);
                $ok=(JSON_ERROR_NONE===json_last_error()&&is_array($decoded));
            }else{
                $ok=is_array($raw);
            }
            self::add($checks,$ok?'pass':'error',ucfirst($type).' canlı builder verisi',$ok?'Layout verisi okunabiliyor.':'Layout JSON bozuk veya okunamıyor.','Builder');
        }

        // Frontend assets.
        $assets=array(
            'Frontend · Foundation CSS'=>'assets/css/frontend/wpst-01-foundation.css',
            'Frontend · Header/Mobile CSS'=>'assets/css/frontend/wpst-02-header-mobile.css',
            'Frontend · Builder Rows CSS'=>'assets/css/frontend/wpst-03-builder-rows.css',
            'Frontend · Footer Builder CSS'=>'assets/css/frontend/wpst-04-footer-builder.css',
            'Frontend · Modern Compat CSS'=>'assets/css/frontend/wpst-05-modern-compat.css',
            'Widgets Foundation CSS'=>'assets/css/widgets/wpst-widgets-foundation.css',
            'Widgets Framework CSS'=>'assets/css/widgets/wpst-widgets-framework.css',
            'Widgets Media/Motion CSS'=>'assets/css/widgets/wpst-widgets-media-motion.css',
            'Widgets Signature CSS'=>'assets/css/widgets/wpst-widgets-signature.css',
            'Widgets UI CSS'=>'assets/css/widgets/wpst-widgets-ui.css',
            'Widgets Interactive CSS'=>'assets/css/widgets/wpst-widgets-interactive.css',
            'Frontend JS'=>'assets/js/frontend.js',
            'Admin CSS'=>'assets/css/admin.css',
            'Admin JS'=>'assets/js/admin.js',
            'Motion CSS'=>'assets/css/motion-engine.css',
            'Motion JS'=>'assets/js/motion-engine.js',
            'Mega Menu CSS'=>'assets/css/mega-menu.css',
            'Mega Menu JS'=>'assets/js/mega-menu.js'
        );
        foreach($assets as $label=>$rel){
            self::add($checks,file_exists(WPST_PATH.$rel)?'pass':'error',$label,file_exists(WPST_PATH.$rel)?'Asset mevcut.':'Eksik asset: '.$rel,'Asset Bütünlüğü');
        }

        // Widget responsive quality.
        $responsive_widget_files = glob( WPST_PATH . 'includes/elementor/widgets/class-wpst-widget-*.php' );
        $responsive_total = 0;
        $responsive_standard = 0;
        foreach ( (array) $responsive_widget_files as $widget_file ) {
            if ( basename( $widget_file ) === 'class-wpst-widget-base.php' ) continue;
            $responsive_total++;
            $source = @file_get_contents( $widget_file );
            if ( false !== $source && false !== strpos( $source, 'standard_responsive_controls()' ) ) $responsive_standard++;
        }
        self::add(
            $checks,
            ( $responsive_total > 0 && $responsive_standard === $responsive_total ) ? 'pass' : 'warn',
            'Widget Responsive Standardı',
            $responsive_standard . '/' . $responsive_total . ' widget ortak responsive kontrol sistemine bağlı.',
            'Widget Quality'
        );
        self::add(
            $checks,
            file_exists( WPST_PATH . 'assets/css/widgets/wpst-widgets-interactive.css' ) ? 'pass' : 'error',
            'Responsive QA CSS',
            'Masaüstü / tablet / mobil güvenlik katmanı mevcut.',
            'Widget Quality'
        );

        // Motion System 1.0.
        $motion_css = WPST_PATH . 'assets/css/motion-engine.css';
        $motion_js  = WPST_PATH . 'assets/js/motion-engine.js';
        $motion_base = WPST_PATH . 'includes/elementor/widgets/class-wpst-widget-base.php';
        $motion_base_source = file_exists($motion_base) ? (string) @file_get_contents($motion_base) : '';
        self::add(
            $checks,
            ( file_exists($motion_css) && file_exists($motion_js) ) ? 'pass' : 'error',
            'Motion System 1.0 · Engine',
            ( file_exists($motion_css) && file_exists($motion_js) ) ? 'Motion CSS ve JS motoru mevcut.' : 'Motion motor assetlerinden biri eksik.',
            'Widget Quality'
        );
        self::add(
            $checks,
            ( false !== strpos($motion_base_source,'wpst_entry_motion') &&
              false !== strpos($motion_base_source,'wpst_stagger_children') &&
              false !== strpos($motion_base_source,'wpst_motion_disable_mobile') ) ? 'pass' : 'warn',
            'Motion System 1.0 · Kontroller',
            'Giriş, stagger ve cihaz bazlı motion kontrolleri denetlendi.',
            'Widget Quality'
        );

        // Counts.
        $templates=wp_count_posts('elementor_library');
        $pages=wp_count_posts('page');
        $posts=wp_count_posts('post');

        $summary=array('pass'=>0,'warn'=>0,'error'=>0);
        foreach($checks as $c){
            if(isset($summary[$c['status']]))$summary[$c['status']]++;
        }

        return array(
            'environment'=>self::environment(),
            'checks'=>$checks,
            'summary'=>$summary,
            'counts'=>array(
                'registered_widgets'=>count($registered),
                'library_widgets'=>count($library_widgets),
                'elementor_templates'=>$templates?(int)$templates->publish:0,
                'pages'=>$pages?(int)$pages->publish:0,
                'posts'=>$posts?(int)$posts->publish:0
            )
        );
    }

    public static function export(){
        if(!current_user_can('manage_options'))wp_die('Yetkiniz yok.');
        check_admin_referer('wpst_export_diagnostics');
        $data=self::run();
        $safe=array(
            'generated_at'=>current_time('mysql'),
            'environment'=>$data['environment'],
            'summary'=>$data['summary'],
            'counts'=>$data['counts'],
            'checks'=>$data['checks']
        );
        nocache_headers();
        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename="wpsoft-system-status-'.gmdate('Ymd-His').'.json"');
        echo wp_json_encode($safe,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
        exit;
    }

    private static function status_label($status){
        if('pass'===$status)return 'Başarılı';
        if('warn'===$status)return 'Uyarı';
        return 'Hata';
    }

    public static function page(){
        if(!current_user_can('manage_options'))return;
        $data=self::run();
        $s=$data['summary'];
        $total=$s['pass']+$s['warn']+$s['error'];
        $score=$total?round(($s['pass']+($s['warn']*.5))/$total*100):100;
        $export=wp_nonce_url(admin_url('admin-post.php?action=wpst_export_diagnostics'),'wpst_export_diagnostics');
        ?>
        <div class="wrap wpst-diagnostics">
            <div class="wpst-diag-hero">
                <div>
                    <span class="wpst-diag-kicker">WPSOFT SYSTEM HEALTH</span>
                    <h1>Sistem Durumu & Tanılama Merkezi</h1>
                    <p>WPSoft Site Tools, Elementor, şablonlar, widgetlar, builder verileri ve temel asset bütünlüğünü tek ekranda kontrol edin.</p>
                </div>
                <div class="wpst-diag-actions">
                    <a class="button" href="<?php echo esc_url(admin_url('admin.php?page=wpsoft-system-status&refresh='.time())); ?>">Kontrolleri Yenile</a>
                    <a class="button button-primary" href="<?php echo esc_url($export); ?>">Raporu JSON İndir</a>
                </div>
            </div>

            <div class="wpst-health-overview">
                <div class="wpst-health-score is-<?php echo $s['error']?'error':($s['warn']?'warn':'pass'); ?>">
                    <div class="wpst-health-ring"><strong><?php echo absint($score); ?></strong><span>/100</span></div>
                    <div><small>GENEL SAĞLIK</small><h2><?php echo $s['error']?'Müdahale Gerekiyor':($s['warn']?'İyi · Uyarılar Var':'Sistem Sağlıklı'); ?></h2><p><?php echo absint($total); ?> otomatik kontrol çalıştırıldı.</p></div>
                </div>
                <div class="wpst-health-stat pass"><span>✓</span><div><strong><?php echo absint($s['pass']); ?></strong><small>Başarılı</small></div></div>
                <div class="wpst-health-stat warn"><span>!</span><div><strong><?php echo absint($s['warn']); ?></strong><small>Uyarı</small></div></div>
                <div class="wpst-health-stat error"><span>×</span><div><strong><?php echo absint($s['error']); ?></strong><small>Hata</small></div></div>
            </div>

            <div class="wpst-diag-counts">
                <div><small>WPSOFT WIDGET</small><strong><?php echo absint($data['counts']['registered_widgets']); ?></strong></div>
                <div><small>KÜTÜPHANE WIDGET</small><strong><?php echo absint($data['counts']['library_widgets']); ?></strong></div>
                <div><small>ELEMENTOR ŞABLON</small><strong><?php echo absint($data['counts']['elementor_templates']); ?></strong></div>
                <div><small>SAYFA</small><strong><?php echo absint($data['counts']['pages']); ?></strong></div>
                <div><small>BLOG YAZISI</small><strong><?php echo absint($data['counts']['posts']); ?></strong></div>
            </div>

            <div class="wpst-diag-layout">
                <main>
                    <section class="wpst-diag-card">
                        <div class="wpst-diag-card-head"><div><span>INTEGRITY CHECKER</span><h2>Otomatik Sağlık Kontrolü</h2></div><small><?php echo absint($total); ?> kontrol</small></div>
                        <?php
                        $groups=array();
                        foreach($data['checks'] as $check)$groups[$check['group']][]=$check;
                        foreach($groups as $group=>$checks):
                        ?>
                            <details class="wpst-check-group" <?php echo array_filter($checks,function($c){return 'error'===$c['status'];})?'open':''; ?>>
                                <summary><strong><?php echo esc_html($group); ?></strong><span><?php echo count($checks); ?> kontrol</span></summary>
                                <div class="wpst-check-list">
                                <?php foreach($checks as $check): ?>
                                    <article class="wpst-health-row is-<?php echo esc_attr($check['status']); ?>">
                                        <i><?php echo 'pass'===$check['status']?'✓':('warn'===$check['status']?'!':'×'); ?></i>
                                        <div><strong><?php echo esc_html($check['title']); ?></strong><p><?php echo esc_html($check['message']); ?></p></div>
                                        <span><?php echo esc_html(self::status_label($check['status'])); ?></span>
                                    </article>
                                <?php endforeach; ?>
                                </div>
                            </details>
                        <?php endforeach; ?>
                    </section>
                </main>

                <aside>
                    <section class="wpst-diag-card">
                        <div class="wpst-diag-card-head"><div><span>ENVIRONMENT</span><h2>Sistem Bilgileri</h2></div></div>
                        <div class="wpst-env-list">
                            <?php foreach($data['environment'] as $key=>$value): ?>
                                <div><span><?php echo esc_html($key); ?></span><strong><?php echo esc_html($value); ?></strong></div>
                            <?php endforeach; ?>
                        </div>
                    </section>

                    <section class="wpst-diag-card wpst-diag-help">
                        <div class="wpst-diag-card-head"><div><span>QUICK ACTIONS</span><h2>Hızlı İşlemler</h2></div></div>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=wpsoft-site-tools')); ?>"><span>Site Tools</span><b>→</b></a>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=wpsoft-my-templates')); ?>"><span>Şablonlarım</span><b>→</b></a>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=wpsoft-my-templates&view=new')); ?>"><span>Yeni Şablon</span><b>→</b></a>
                        <a href="<?php echo esc_url(admin_url('edit.php?post_type=elementor_library')); ?>"><span>Elementor Şablonları</span><b>→</b></a>
                    </section>
                </aside>
            </div>
        </div>
        <?php
    }
}
