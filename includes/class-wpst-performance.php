<?php
if(!defined('ABSPATH'))exit;

final class WPST_Performance {
    const OPTION='wpst_performance_settings';
    const CACHE_PREFIX='wpst_asset_map_';

    public static function init(){
        add_action('wp_enqueue_scripts',array(__CLASS__,'optimize'),99);
        add_filter('script_loader_tag',array(__CLASS__,'defer_scripts'),10,3);
        add_filter('wp_get_attachment_image_attributes',array(__CLASS__,'image_attributes'),20,3);
        add_action('admin_menu',array(__CLASS__,'menu'),35);
        add_action('admin_post_wpst_save_performance',array(__CLASS__,'save'));
        add_action('admin_post_wpst_clear_asset_cache',array(__CLASS__,'clear_cache'));
        add_action('save_post',array(__CLASS__,'purge_post_cache'),20,1);
    }

    private static function defaults(){
        return array(
            'smart_assets'=>1,
            'motion_on_demand'=>1,
            'defer_scripts'=>1,
            'lazy_media'=>1,
            'editor_always_full'=>1,
        );
    }

    public static function settings(){
        return wp_parse_args((array)get_option(self::OPTION,array()),self::defaults());
    }

    public static function menu(){
        add_submenu_page(
            'wpsoft-site-tools',
            'Performance & Asset Manager',
            'Performance',
            'manage_options',
            'wpsoft-performance',
            array(__CLASS__,'page')
        );
    }

    private static function template_ids(){
        $settings=(array)get_option('wpst_settings',array());
        $ids=array();
        foreach(array(
            'header_template','footer_template','blog_single_template','blog_archive_template',
            'theme_search_template','theme_category_template','theme_tag_template',
            'theme_author_template','theme_404_template'
        ) as $key){
            if(!empty($settings[$key]))$ids[]=absint($settings[$key]);
        }
        return array_values(array_unique(array_filter($ids)));
    }

    private static function current_ids(){
        $ids=array();
        $id=get_queried_object_id();
        if($id)$ids[]=$id;
        foreach(self::template_ids() as $tid)$ids[]=$tid;
        return array_values(array_unique(array_filter($ids)));
    }

    private static function data_for_post($post_id){
        $post_id=absint($post_id);
        if(!$post_id)return '';
        $post=get_post($post_id);
        $modified=$post?$post->post_modified_gmt:'';
        $cache_key=self::CACHE_PREFIX.$post_id.'_'.md5((string)$modified);
        $cached=get_transient($cache_key);
        if(false!==$cached)return (string)$cached;

        $data=get_post_meta($post_id,'_elementor_data',true);
        if(!is_string($data))$data='';
        $content=$post && is_string($post->post_content)?$post->post_content:'';
        $blob=$data."\n".$content;
        set_transient($cache_key,$blob,DAY_IN_SECONDS);
        return $blob;
    }

    private static function combined_data(){
        static $blob=null;
        if(null!==$blob)return $blob;
        $parts=array();
        foreach(self::current_ids() as $id)$parts[]=self::data_for_post($id);
        $blob=implode("\n",$parts);
        return $blob;
    }

    private static function editor_context(){
        if(is_admin())return true;
        if(isset($_GET['elementor-preview']))return true; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        return false;
    }

    public static function page_uses_wpsoft(){
        if(self::editor_context())return true;

        $blob=self::combined_data();
        if(false!==strpos($blob,'wpsoft-') || false!==strpos($blob,'wpst_'))return true;

        $settings=(array)get_option('wpst_settings',array());
        if(!empty($settings['header_enabled']) || !empty($settings['footer_enabled']))return true;
        if(is_singular('post') && !empty($settings['blog_single_enabled']))return true;
        if(is_page() && !empty($settings['blog_archive_enabled']) && absint($settings['blog_page_id'])===get_queried_object_id())return true;
        if((is_search()&&!empty($settings['theme_search_enabled'])) ||
           (is_category()&&!empty($settings['theme_category_enabled'])) ||
           (is_tag()&&!empty($settings['theme_tag_enabled'])) ||
           (is_author()&&!empty($settings['theme_author_enabled'])) ||
           (is_404()&&!empty($settings['theme_404_enabled'])))return true;
        return false;
    }

    public static function page_needs_motion(){
        if(self::editor_context())return true;
        $blob=self::combined_data();
        if($blob==='')return false;

        $needles=array(
            'wpst_entry_motion','wpst_hover_motion','wpst_stagger_children','wpst_parallax_enabled','wpst_mouse_follow_enabled',
            'wpsoft-animated-heading','wpsoft-animated-counter','wpsoft-marquee-text',
            'wpsoft-reveal-cards','wpsoft-parallax-image','wpsoft-icon-orbit',
            'wpsoft-scroll-progress','wpsoft-mouse-follow-card','wpsoft-scroll-reveal-text'
        );
        foreach($needles as $needle){
            if(false!==strpos($blob,$needle))return true;
        }
        return false;
    }

    public static function optimize(){
        $settings=self::settings();
        if(!self::page_uses_wpsoft())return;

        // Motion is a functional widget layer, not merely a performance optimization.
        // Therefore it must still load when Smart Assets is disabled.
        $load_motion=empty($settings['motion_on_demand']) || self::page_needs_motion();
        if($load_motion){
            wp_enqueue_style('wpst-motion-engine',WPST_URL.'assets/css/motion-engine.css',array(),WPST_VERSION);
            wp_enqueue_script('wpst-motion-engine',WPST_URL.'assets/js/motion-engine.js',array(),WPST_VERSION,true);
            if(function_exists('wp_script_add_data'))wp_script_add_data('wpst-motion-engine','strategy','defer');
        }
    }

    public static function defer_scripts($tag,$handle,$src){
        $settings=self::settings();
        if(empty($settings['defer_scripts']))return $tag;

        $defer=array('wpst-motion-engine','wpst-mega-menu','wpst-elementor-widgets');
        if(in_array($handle,$defer,true) && false===strpos($tag,' defer')){
            return str_replace(' src=',' defer src=',$tag);
        }
        return $tag;
    }

    public static function image_attributes($attr,$attachment,$size){
        $settings=self::settings();
        if(empty($settings['lazy_media']) || is_admin())return $attr;
        if(empty($attr['loading']))$attr['loading']='lazy';
        if(empty($attr['decoding']))$attr['decoding']='async';
        return $attr;
    }

    public static function purge_post_cache($post_id){
        $post_id=absint($post_id);
        if(!$post_id)return;
        global $wpdb;
        $like='_transient_'.self::CACHE_PREFIX.$post_id.'_%';
        $timeout_like='_transient_timeout_'.self::CACHE_PREFIX.$post_id.'_%';
        $wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",$like,$timeout_like)); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
    }

    public static function clear_cache(){
        if(!current_user_can('manage_options'))wp_die('Yetkiniz yok.');
        check_admin_referer('wpst_clear_asset_cache');
        global $wpdb;
        $like='_transient_'.self::CACHE_PREFIX.'%';
        $timeout='_transient_timeout_'.self::CACHE_PREFIX.'%';
        $wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",$like,$timeout)); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
        wp_safe_redirect(admin_url('admin.php?page=wpsoft-performance&cache_cleared=1'));
        exit;
    }

    public static function save(){
        if(!current_user_can('manage_options'))wp_die('Yetkiniz yok.');
        check_admin_referer('wpst_save_performance');
        $in=isset($_POST['wpst_performance'])?(array)wp_unslash($_POST['wpst_performance']):array();
        $clean=array();
        foreach(array_keys(self::defaults()) as $key)$clean[$key]=!empty($in[$key])?1:0;
        update_option(self::OPTION,$clean,false);
        wp_safe_redirect(admin_url('admin.php?page=wpsoft-performance&updated=1'));
        exit;
    }

    private static function asset_rows(){
        $assets=array(
            'Global Design CSS'=>'assets/css/global-design.css',
            'Widgets · Foundation CSS'=>'assets/css/widgets/wpst-widgets-foundation.css',
            'Widgets · Framework CSS'=>'assets/css/widgets/wpst-widgets-framework.css',
            'Widgets · Media & Motion CSS'=>'assets/css/widgets/wpst-widgets-media-motion.css',
            'Widgets · Signature CSS'=>'assets/css/widgets/wpst-widgets-signature.css',
            'Widgets · UI CSS'=>'assets/css/widgets/wpst-widgets-ui.css',
            'Widgets · Interactive CSS'=>'assets/css/widgets/wpst-widgets-interactive.css',
            'Header/Footer · Foundation'=>'assets/css/frontend/wpst-01-foundation.css',
            'Header/Footer · Header/Mobile'=>'assets/css/frontend/wpst-02-header-mobile.css',
            'Header/Footer · Builder Rows'=>'assets/css/frontend/wpst-03-builder-rows.css',
            'Header/Footer · Footer'=>'assets/css/frontend/wpst-04-footer-builder.css',
            'Header/Footer · Modern Compat'=>'assets/css/frontend/wpst-05-modern-compat.css',
            'Header · Canonical State'=>'assets/css/frontend/wpst-06-header-canonical.css',
            'Header/Footer + Media JS'=>'assets/js/frontend.js',
            'Elementor Interactive JS'=>'assets/js/elementor-widgets.js',
            'Motion Engine CSS'=>'assets/css/motion-engine.css',
            'Motion Engine JS'=>'assets/js/motion-engine.js',
            'Mega Menu CSS'=>'assets/css/mega-menu.css',
            'Mega Menu JS'=>'assets/js/mega-menu.js',
        );
        $rows=array();
        foreach($assets as $label=>$rel){
            $path=WPST_PATH.$rel;
            $bytes=file_exists($path)?filesize($path):0;
            $rows[]=array('label'=>$label,'rel'=>$rel,'bytes'=>$bytes,'exists'=>file_exists($path));
        }
        return $rows;
    }

    private static function human($bytes){
        if($bytes>=1048576)return number_format_i18n($bytes/1048576,2).' MB';
        if($bytes>=1024)return number_format_i18n($bytes/1024,1).' KB';
        return absint($bytes).' B';
    }

    public static function page(){
        if(!current_user_can('manage_options'))return;
        $s=self::settings();
        $rows=self::asset_rows();
        $total=0;foreach($rows as $r)$total+=$r['bytes'];
        $smart=!empty($s['smart_assets']);
        ?>
        <div class="wrap wpst-performance-page">
            <h1>Performance & Asset Manager</h1>
            <p class="description">WPSoft büyüdükçe frontend’de yalnız ihtiyaç duyulan davranışların yüklenmesini kontrol eder. Güvenli varsayılanlar açık gelir.</p>
            <?php if(isset($_GET['updated'])): ?><div class="notice notice-success is-dismissible"><p>Performans ayarları kaydedildi.</p></div><?php endif; // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
            <?php if(isset($_GET['cache_cleared'])): ?><div class="notice notice-success is-dismissible"><p>Asset tarama önbelleği temizlendi.</p></div><?php endif; // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>

            <div class="wpst-perf-hero">
                <div><small>ASSET MANAGER 2.0</small><strong><?php echo $smart?'Akıllı yükleme aktif':'Akıllı yükleme kapalı'; ?></strong><span>Toplam WPSoft frontend asset boyutu: <?php echo esc_html(self::human($total)); ?></span></div>
                <b><?php echo $smart?'SMART':'FULL'; ?></b>
            </div>

            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" class="wpst-perf-grid">
                <?php wp_nonce_field('wpst_save_performance'); ?>
                <input type="hidden" name="action" value="wpst_save_performance">
                <section class="wpst-perf-card">
                    <h2>Akıllı Asset Yükleme</h2>
                    <label><input type="checkbox" name="wpst_performance[smart_assets]" value="1" <?php checked($s['smart_assets']); ?>> <span><b>Smart Assets</b><em>WPSoft kullanılmayan sayfalarda ek performans motorlarını yükleme.</em></span></label>
                    <label><input type="checkbox" name="wpst_performance[motion_on_demand]" value="1" <?php checked($s['motion_on_demand']); ?>> <span><b>Motion on Demand</b><em>Animasyon/parallax yoksa Motion Engine CSS/JS yüklenmez.</em></span></label>
                    <label><input type="checkbox" name="wpst_performance[defer_scripts]" value="1" <?php checked($s['defer_scripts']); ?>> <span><b>JS Defer</b><em>WPSoft motion, mega menu ve interactive scriptlerini render-blocking olmaktan çıkarır.</em></span></label>
                    <label><input type="checkbox" name="wpst_performance[lazy_media]" value="1" <?php checked($s['lazy_media']); ?>> <span><b>Lazy Media</b><em>WordPress attachment görsellerine güvenli lazy-load + async decode ekler.</em></span></label>
                    <label><input type="checkbox" name="wpst_performance[editor_always_full]" value="1" <?php checked($s['editor_always_full']); ?>> <span><b>Editörde Tam Asset</b><em>Elementor editöründe eksik önizleme riskini önlemek için güvenli tam çalışma modu.</em></span></label>
                    <?php submit_button('Performans Ayarlarını Kaydet','primary','submit',false); ?>
                </section>
                <section class="wpst-perf-card">
                    <h2>Asset Envanteri</h2>
                    <div class="wpst-perf-assets">
                        <?php foreach($rows as $row): ?>
                            <div><span><i class="<?php echo $row['exists']?'is-ok':'is-bad'; ?>"></i><?php echo esc_html($row['label']); ?><small><?php echo esc_html($row['rel']); ?></small></span><b><?php echo esc_html(self::human($row['bytes'])); ?></b></div>
                        <?php endforeach; ?>
                    </div>
                </section>
            </form>

            <div class="wpst-perf-footer">
                <div><strong>Asset Detection Cache</strong><span>Elementor verisi tekrar tekrar taranmasın diye kısa süreli harita önbelleği kullanılır. Sayfa kaydedildiğinde ilgili cache otomatik temizlenir.</span></div>
                <a class="button" href="<?php echo esc_url(wp_nonce_url(admin_url('admin-post.php?action=wpst_clear_asset_cache'),'wpst_clear_asset_cache')); ?>">Asset Cache Temizle</a>
            </div>
        </div>
        <style>
        .wpst-performance-page{max-width:1180px}.wpst-perf-hero{margin:20px 0;display:flex;align-items:center;justify-content:space-between;padding:28px 30px;border-radius:18px;background:linear-gradient(135deg,#0f172a,#1e293b);color:#fff;box-shadow:0 18px 50px rgba(15,23,42,.14)}.wpst-perf-hero div{display:grid;gap:5px}.wpst-perf-hero small{font-size:10px;font-weight:900;letter-spacing:.14em;color:#93c5fd}.wpst-perf-hero strong{font-size:26px;letter-spacing:-.03em}.wpst-perf-hero span{color:#cbd5e1}.wpst-perf-hero>b{padding:9px 12px;border-radius:999px;background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.12);font-size:11px;letter-spacing:.08em}.wpst-perf-grid{display:grid;grid-template-columns:1fr 1fr;gap:18px}.wpst-perf-card,.wpst-perf-footer{padding:22px;border:1px solid #e2e8f0;border-radius:16px;background:#fff;box-shadow:0 6px 24px rgba(15,23,42,.035)}.wpst-perf-card h2{margin-top:0}.wpst-perf-card>label{display:flex;gap:11px;padding:13px 0;border-bottom:1px solid #eef2f7}.wpst-perf-card>label span{display:grid;gap:3px}.wpst-perf-card>label b{font-size:13px;color:#0f172a}.wpst-perf-card>label em{font-size:11px;font-style:normal;color:#64748b}.wpst-perf-assets>div{display:flex;justify-content:space-between;gap:15px;padding:11px 0;border-bottom:1px solid #eef2f7}.wpst-perf-assets span{display:grid;grid-template-columns:auto 1fr;column-gap:8px;align-items:center}.wpst-perf-assets span small{grid-column:2;color:#94a3b8;margin-top:2px}.wpst-perf-assets i{width:7px;height:7px;border-radius:50%;background:#22c55e}.wpst-perf-assets i.is-bad{background:#ef4444}.wpst-perf-assets>b{font-size:11px;color:#475569}.wpst-perf-footer{display:flex;align-items:center;justify-content:space-between;gap:20px;margin-top:18px}.wpst-perf-footer div{display:grid;gap:4px}.wpst-perf-footer span{font-size:11px;color:#64748b}@media(max-width:850px){.wpst-perf-grid{grid-template-columns:1fr}.wpst-perf-footer{align-items:flex-start;flex-direction:column}}
        </style>
        <?php
    }
}
