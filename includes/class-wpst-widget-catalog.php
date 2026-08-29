<?php
if(!defined('ABSPATH'))exit;

final class WPST_Widget_Catalog {
    const OPTION='wpst_widget_catalog_settings';

    public static function init(){
        add_action('admin_menu',array(__CLASS__,'menu'),36);
    }

    public static function menu(){
        add_submenu_page(
            'wpsoft-site-tools',
            'Widget Catalog',
            'Widgetler',
            'manage_options',
            'wpsoft-widget-catalog',
            array(__CLASS__,'page')
        );
    }

    private static function scan(){
        $dir=WPST_PATH.'includes/elementor/widgets/';
        $files=glob($dir.'class-wpst-widget-*.php');        $items=array();

        foreach((array)$files as $file){
            if(basename($file)==='class-wpst-widget-base.php')continue;
            $src=@file_get_contents($file);
            if(!$src)continue;
            $name='';$title='';
            if(preg_match("/function\s+get_name\(\)\s*\{\s*return\s*'([^']+)'/",$src,$m))$name=$m[1];
            if(preg_match("/function\s+get_title\(\)\s*\{\s*return\s*'([^']+)'/",$src,$m))$title=$m[1];
            if(!$name)continue;

            $tier='modern';
            if(preg_match('/(?:-pro|-modern|-zoom|-reveal|-carousel|-spotlight|-mosaic|-orbit|-cascade|-morphing|-bento|-split|-saas|-medical|-hospitality|-industry|-commerce)$/',$name))$tier='signature';

            $items[]=array(
                'name'=>$name,'title'=>$title?:$name,'tier'=>$tier,'file'=>basename($file)
            );
        }

        usort($items,function($a,$b){
            $rank=array('signature'=>0,'modern'=>1,'legacy'=>2);
            $r=($rank[$a['tier']]??9)<=>($rank[$b['tier']]??9);
            return $r?:strnatcasecmp($a['title'],$b['title']);
        });
        return $items;
    }

    private static function counts($items){
        $c=array('signature'=>0,'modern'=>0);
        foreach($items as $i){if(isset($c[$i['tier']]))$c[$i['tier']]++;}
        return $c;
    }

    public static function page(){
        if(!current_user_can('manage_options'))return;
        $settings=wp_parse_args((array)get_option(self::OPTION,array()),array('show_legacy'=>0));
        $items=self::scan();$counts=self::counts($items);
        ?>
        <div class="wrap wpst-widget-catalog">
          <div class="wpst-wc-head">
            <div><small>WPSOFT UI / UX</small><h1>Widget Catalog</h1><p>Elementor panelini modern-first tutar. Signature UI standardı; tipografi, spacing, radius, buton ve mobil dokunmatik davranışlarını ortaklaştırır. Legacy widgetlar silinmez.</p></div>
            <div class="wpst-wc-stats">
              <span><b><?php echo absint($counts['signature']); ?></b>Signature</span>
              <span><b><?php echo absint($counts['modern']); ?></b>Modern</span>
              <span><b>48px</b>Mobil Touch</span>
            </div>
          </div>
          <?php if(isset($_GET['updated'])): ?><div class="notice notice-success is-dismissible"><p>Widget kataloğu güncel.</p></div><?php endif; // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
          <div class="wpst-wc-toolbar">
            <input type="search" id="wpst-wc-search" placeholder="Widget ara…">
            <button type="button" class="button is-active" data-tier="all">Tümü</button>
            <button type="button" class="button" data-tier="signature">Signature</button>
            <button type="button" class="button" data-tier="modern">Modern</button>

          </div>
          <div class="wpst-wc-grid">
          <?php foreach($items as $item): ?>
            <article class="wpst-wc-card is-<?php echo esc_attr($item['tier']); ?>" data-tier="<?php echo esc_attr($item['tier']); ?>" data-search="<?php echo esc_attr(strtolower($item['title'].' '.$item['name'])); ?>">
              <div class="wpst-wc-icon"><?php echo 'signature'===$item['tier']?'✦':'W'; ?></div>
              <div class="wpst-wc-copy">
                <span class="wpst-wc-tier"><?php echo esc_html(ucfirst($item['tier'])); ?></span>
                <strong><?php echo esc_html($item['title']); ?></strong>
                <code><?php echo esc_html($item['name']); ?></code>
                <p><?php echo 'signature'===$item['tier']?'Yeni projelerde önerilen modern widget.':'Aktif ve desteklenen widget.'; ?></p>
              </div>
            </article>
          <?php endforeach; ?>
          </div>
        </div>
        <style>
        .wpst-widget-catalog{max-width:1220px}.wpst-wc-head{margin:18px 0;display:flex;align-items:flex-end;justify-content:space-between;gap:28px;padding:28px 30px;border-radius:20px;background:linear-gradient(135deg,#0f172a,#1e293b);color:#fff}.wpst-wc-head small{font-size:10px;font-weight:900;letter-spacing:.14em;color:#93c5fd}.wpst-wc-head h1{margin:5px 0;color:#fff;font-size:30px;letter-spacing:-.035em}.wpst-wc-head p{max-width:660px;margin:0;color:#cbd5e1}.wpst-wc-stats{display:flex;gap:8px}.wpst-wc-stats span{min-width:90px;padding:12px;border-radius:14px;background:rgba(255,255,255,.08);text-align:center;font-size:10px}.wpst-wc-stats b{display:block;font-size:22px;color:#fff}.wpst-wc-settings{display:flex;align-items:center;justify-content:space-between;gap:20px;margin:15px 0;padding:16px 18px;border:1px solid #e2e8f0;border-radius:14px;background:#fff}.wpst-wc-settings label{display:flex;align-items:flex-start;gap:10px}.wpst-wc-settings label span{display:grid;gap:3px}.wpst-wc-settings em{font-size:11px;font-style:normal;color:#64748b}.wpst-wc-toolbar{position:sticky;top:32px;z-index:5;display:flex;gap:7px;padding:10px;margin:16px 0;border:1px solid #e2e8f0;border-radius:14px;background:rgba(255,255,255,.92);backdrop-filter:blur(12px)}.wpst-wc-toolbar input{min-width:260px}.wpst-wc-toolbar .button.is-active{background:#0f172a;color:#fff;border-color:#0f172a}.wpst-wc-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:13px}.wpst-wc-card{display:flex;gap:13px;min-height:132px;padding:16px;border:1px solid #e5e7eb;border-radius:16px;background:#fff;box-shadow:0 5px 20px rgba(15,23,42,.035)}.wpst-wc-card.is-signature{border-color:#c7d2fe;background:linear-gradient(180deg,#fff,#fafaff)}.wpst-wc-icon{flex:0 0 38px;width:38px;height:38px;display:grid;place-items:center;border-radius:11px;background:#eef2ff;color:#4338ca;font-weight:900}.wpst-wc-copy{min-width:0;display:flex;flex-direction:column;align-items:flex-start}.wpst-wc-tier{font-size:8px;font-weight:900;letter-spacing:.1em;text-transform:uppercase;color:#6366f1}.wpst-wc-copy>strong{margin:3px 0 2px;font-size:13px;color:#0f172a}.wpst-wc-copy code{font-size:9px;color:#94a3b8;background:transparent;padding:0}.wpst-wc-copy p{margin:auto 0 0;font-size:10px;line-height:1.5;color:#64748b}.wpst-wc-copy p b{color:#0f172a}.wpst-wc-copy p small{display:block;color:#94a3b8}@media(max-width:1000px){.wpst-wc-grid{grid-template-columns:1fr 1fr}.wpst-wc-head{align-items:flex-start;flex-direction:column}}@media(max-width:650px){.wpst-wc-grid{grid-template-columns:1fr}.wpst-wc-toolbar{flex-wrap:wrap}.wpst-wc-toolbar input{width:100%;min-width:0}.wpst-wc-settings{align-items:flex-start;flex-direction:column}.wpst-wc-stats{width:100%;overflow-x:auto}.wpst-wc-stats span{flex:1}}
        </style>
        <script>
        document.addEventListener('DOMContentLoaded',function(){
          var q=document.getElementById('wpst-wc-search'),tier='all',cards=[].slice.call(document.querySelectorAll('.wpst-wc-card'));
          function filter(){var s=(q.value||'').toLowerCase();cards.forEach(function(c){var ok=(tier==='all'||c.dataset.tier===tier)&&(!s||(c.dataset.search||'').indexOf(s)!==-1);c.style.display=ok?'flex':'none'})}
          q.addEventListener('input',filter);
          document.querySelectorAll('[data-tier]').forEach(function(b){if(!b.classList.contains('wpst-wc-card'))b.addEventListener('click',function(){tier=b.dataset.tier;document.querySelectorAll('.wpst-wc-toolbar [data-tier]').forEach(x=>x.classList.remove('is-active'));b.classList.add('is-active');filter()})});
        });
        </script>
        <?php
    }
}
