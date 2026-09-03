(function($){
    'use strict';

    var cfg = window.WPSTEditorLibrary || {};
    ['widgets','sections','pages','headers','footers','mega_menus'].forEach(function(k){
        if(!Array.isArray(cfg[k])) cfg[k]=[];
    });
    var modal = null;
    var currentTab = 'widgets';
    var busy = false;
    var previewObserver = null;
    var observedDoc = null;
    var lastFocusedElement = null;
    var lastPreviewTrigger = null;

    function esc(s){ return $('<div>').text(s == null ? '' : String(s)).html(); }

    function editorReady(){
        return window.elementor && window.$e && typeof $e.run === 'function';
    }

    function getPreviewIframe(){
        return document.getElementById('elementor-preview-iframe') ||
            document.querySelector('iframe[name="elementor-preview-iframe"]') ||
            document.querySelector('.elementor-preview-iframe iframe') ||
            document.querySelector('#elementor-preview iframe');
    }

    function previewDocument(){
        try {
            var iframe = getPreviewIframe();
            if (iframe && iframe.contentDocument) return iframe.contentDocument;
            if (window.elementor && elementor.$preview && elementor.$preview.length && elementor.$preview[0].contentDocument) {
                return elementor.$preview[0].contentDocument;
            }
        } catch(e){}
        return null;
    }

    function ensurePreviewStyles(doc){
        if(!doc || doc.getElementById('wpst-preview-inline-style')) return;
        var style = doc.createElement('style');
        style.id = 'wpst-preview-inline-style';
        style.textContent = [
            '.wpst-template-launcher{display:inline-flex!important;align-items:center!important;justify-content:center!important;width:40px!important;height:40px!important;min-width:40px!important;padding:0!important;margin:0 4px!important;border:0!important;border-radius:50%!important;background:#2563eb!important;color:#fff!important;cursor:pointer!important;box-shadow:0 5px 14px rgba(37,99,235,.25)!important;vertical-align:middle!important;position:relative!important;z-index:999999!important;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif!important;line-height:1!important}',
            '.wpst-template-launcher:hover{background:#1d4ed8!important;transform:translateY(-1px)!important}',
            '.wpst-template-launcher .wpst-launcher-mark{font-size:15px!important;font-weight:900!important;line-height:1!important;color:#fff!important}',
            '.wpst-template-launcher .wpst-launcher-text{display:none!important}',
            '.wpst-template-launcher-wrap{display:inline-flex!important;align-items:center!important;justify-content:center!important}',
            '.elementor-add-new-section .wpst-template-launcher,.elementor-empty-view .wpst-template-launcher,.e-con-empty .wpst-template-launcher{pointer-events:auto!important}'
        ].join('');
        (doc.head || doc.documentElement).appendChild(style);
    }

    function launcherElement(doc){
        var b = doc.createElement('button');
        b.type = 'button';
        b.className = 'wpst-template-launcher';
        b.title = 'WPSoft Şablonlar';
        b.setAttribute('aria-label','WPSoft Şablonlar');
        b.innerHTML = '<span class="wpst-launcher-mark">W</span><span class="wpst-launcher-text">WPSoft Şablonlar</span>';
        b.addEventListener('mousedown', function(e){ e.preventDefault(); e.stopPropagation(); }, true);
        b.addEventListener('click', function(e){
            e.preventDefault();
            e.stopPropagation();
            openModal('all');
            return false;
        }, true);
        return b;
    }

    function isVisible(el){
        if(!el || !el.ownerDocument) return false;
        var win = el.ownerDocument.defaultView;
        if(!win) return true;
        var css = win.getComputedStyle(el);
        return css.display !== 'none' && css.visibility !== 'hidden';
    }

    function findAddAreas(doc){
        var areas = [];
        var selectors = [
            '.elementor-add-new-section',
            '.elementor-add-section',
            '.elementor-empty-view',
            '.elementor-add-section-inner',
            '.e-con-empty',
            '.elementor-empty-view__buttons',
            '[class*="elementor-add-section"]'
        ];
        selectors.forEach(function(sel){
            try {
                doc.querySelectorAll(sel).forEach(function(node){
                    if(areas.indexOf(node) === -1 && isVisible(node)) areas.push(node);
                });
            } catch(e){}
        });

        // Newer Elementor builds can change class names. Detect the visible empty-state text as a last resort.
        try {
            doc.querySelectorAll('div,section').forEach(function(node){
                if(!isVisible(node)) return;
                var txt = String(node.textContent || '').replace(/\s+/g,' ').trim().toLowerCase();
                if(!txt || txt.length > 90) return;
                var looksEmpty = (txt.indexOf('widget') !== -1 && (txt.indexOf('sürük') !== -1 || txt.indexOf('drag') !== -1));
                if(!looksEmpty) return;
                var area = node.closest ? (node.closest('.elementor-element, .e-con, [data-element_type], .elementor-empty-view') || node.parentElement) : node.parentElement;
                if(area && areas.indexOf(area) === -1) areas.push(area);
            });
        } catch(e){}
        return areas;
    }

    function findNativeButtons(area){
        var selectors = [
            '.elementor-add-section-area-button',
            '.elementor-add-template-button',
            'button[title*="template" i]',
            'button[aria-label*="template" i]',
            'button[title*="şablon" i]',
            'button[aria-label*="şablon" i]',
            '.eicon-folder',
            '[class*="eicon-folder"]',
            '[class*="add-template"]'
        ];
        var out = [];
        selectors.forEach(function(sel){
            try {
                area.querySelectorAll(sel).forEach(function(node){
                    var target = node.tagName === 'BUTTON' ? node : (node.closest ? (node.closest('button') || node.closest('div')) : node);
                    if(target && out.indexOf(target) === -1) out.push(target);
                });
            } catch(e){}
        });
        return out;
    }

    function injectIntoArea(doc, area){
        if(!area || area.querySelector('.wpst-template-launcher')) return;
        var nativeButtons = findNativeButtons(area);
        var launcher = launcherElement(doc);

        if(nativeButtons.length){
            var anchor = nativeButtons[nativeButtons.length - 1];
            var host = anchor.parentElement || area;
            if(host && !host.querySelector('.wpst-template-launcher')){
                if(anchor.nextSibling) host.insertBefore(launcher, anchor.nextSibling);
                else host.appendChild(launcher);
                return;
            }
        }

        // Elementor'un yeni Container boş görünümünde butonlar ortak bir araç satırında olmayabilir.
        var hostFallback = area.querySelector('.elementor-empty-view__buttons, .elementor-first-add, .elementor-add-section-inner, [class*="buttons"]') || area;
        if(hostFallback && !hostFallback.querySelector('.wpst-template-launcher')) hostFallback.appendChild(launcher);
    }

    function injectEditorToolbarButton(){
        if(document.getElementById('wpst-editor-toolbar-launcher')) return;
        var candidates = [
            document.querySelector('#elementor-panel-header'),
            document.querySelector('.MuiToolbar-root'),
            document.querySelector('[class*=top-bar]'),
            document.querySelector('[class*=editor-top-bar]')
        ];
        var host = null;
        for(var i=0;i<candidates.length;i++){ if(candidates[i]){ host=candidates[i]; break; } }
        if(!host) return;
        var b=document.createElement('button');
        b.id='wpst-editor-toolbar-launcher'; b.type='button'; b.title='WPSoft Şablonlar';
        b.textContent='W';
        b.style.cssText='margin:6px;width:32px;height:32px;border:0;border-radius:8px;background:#2563eb;color:#fff;font-weight:800;cursor:pointer;z-index:99999';
        b.addEventListener('click',function(e){e.preventDefault();e.stopPropagation();openModal('all');});
        host.appendChild(b);
    }

    function injectLaunchers(){
        var doc = previewDocument();
        if(!doc || !doc.body) return;
        ensurePreviewStyles(doc);
        findAddAreas(doc).forEach(function(area){ injectIntoArea(doc, area); });
        watchPreviewDocument(doc);
    }

    function watchPreviewDocument(doc){
        if(!doc || observedDoc === doc) return;
        if(previewObserver){ try{ previewObserver.disconnect(); }catch(e){} }
        observedDoc = doc;
        previewObserver = new MutationObserver(function(){
            window.requestAnimationFrame(injectLaunchers);
        });
        try { previewObserver.observe(doc.body || doc.documentElement, {childList:true, subtree:true}); } catch(e){}
    }

    function kindLabel(kind){
        return {
            all:'Tüm Türler',
            widgets:'Widgetler',
            sections:'Bölümler',
            pages:'Sayfalar',
            portfolio:'Portföy',
            blog:'Blog',
            blog_archive:'Blog Arşiv',
            single_blog:'Tek Yazı',
            woocommerce:'WooCommerce',
            popups:'Popup',
            headers:'Header',
            footers:'Footer',
            mega_menus:'Mega Menü'
        }[kind] || 'Widgetler';
    }

    function cloneWithVirtualKind(item,virtualKind,sourceKind){
        var copy=$.extend({},item);
        copy.kind=virtualKind;
        copy.source_kind=sourceKind;
        return copy;
    }

    function isBlogArchiveItem(item){
        return String(item.template_role||'') === 'blog_archive';
    }

    function isSingleBlogItem(item){
        return String(item.template_role||'') === 'single_post';
    }

    function isPortfolioItem(item){
        return String(item.template_role||'') === 'portfolio_single';
    }

    function isWooItem(item){
        var text=[item.key,item.title,item.category,item.sector,item.desc].join(' ').toLocaleLowerCase('tr-TR');
        return text.indexOf('woocommerce')!==-1 || text.indexOf('e-ticaret')!==-1 || text.indexOf('eticaret')!==-1 || text.indexOf('ürün')!==-1 || text.indexOf('urun')!==-1 || text.indexOf('shop')!==-1 || text.indexOf('commerce')!==-1;
    }

    function isPopupItem(item){
        var text=[item.key,item.title,item.category,item.desc].join(' ').toLocaleLowerCase('tr-TR');
        return text.indexOf('popup')!==-1 || text.indexOf('modal')!==-1;
    }

    function listForKind(kind){
        if(kind === 'pages'){
            return (cfg.pages || []).filter(function(item){return !isBlogArchiveItem(item) && !isSingleBlogItem(item) && !isPortfolioItem(item);});
        }
        if(kind === 'portfolio'){
            return (cfg.pages || []).filter(isPortfolioItem).map(function(item){return cloneWithVirtualKind(item,'portfolio','pages');});
        }
        if(kind === 'blog_archive'){
            return (cfg.pages || []).filter(isBlogArchiveItem).map(function(item){return cloneWithVirtualKind(item,'blog_archive','pages');});
        }
        if(kind === 'single_blog'){
            return (cfg.pages || []).filter(isSingleBlogItem).map(function(item){return cloneWithVirtualKind(item,'single_blog','pages');});
        }
        if(kind === 'blog'){
            return listForKind('single_blog').concat(listForKind('blog_archive'));
        }
        if(kind === 'woocommerce'){
            var out=[];
            (cfg.widgets || []).filter(isWooItem).forEach(function(item){out.push(cloneWithVirtualKind(item,'woocommerce','widgets'));});
            (cfg.sections || []).filter(isWooItem).forEach(function(item){out.push(cloneWithVirtualKind(item,'woocommerce','sections'));});
            (cfg.pages || []).filter(function(item){return isWooItem(item) && !isBlogArchiveItem(item) && !isSingleBlogItem(item) && !isPortfolioItem(item);}).forEach(function(item){out.push(cloneWithVirtualKind(item,'woocommerce','pages'));});
            return out;
        }
        if(kind === 'popups'){
            return (cfg.widgets || []).filter(isPopupItem).map(function(item){return cloneWithVirtualKind(item,'popups','widgets');});
        }
        if(kind === 'sections') return cfg.sections || [];
        if(kind === 'mega_menus') return cfg.mega_menus || [];
        if(kind === 'headers') return cfg.headers || [];
        if(kind === 'footers') return cfg.footers || [];
        if(kind === 'widgets') return cfg.widgets || [];
        var all=[];
        ['widgets','sections','pages','portfolio','blog_archive','single_blog','headers','footers','mega_menus'].forEach(function(k){
            listForKind(k).forEach(function(item){
                var copy=$.extend({},item);
                copy.kind=copy.kind||k;
                all.push(copy);
            });
        });
        return all;
    }

    function favIds(){
        try{return JSON.parse(localStorage.getItem('wpst_template_favorites')||'[]');}catch(e){return [];}
    }
    function favId(item,kind){return (kind||item.kind||'item')+':'+(item.key||'');}
    function isFav(item,kind){return favIds().indexOf(favId(item,kind))!==-1;}
    function toggleFav(item,kind){
        var ids=favIds(),id=favId(item,kind),i=ids.indexOf(id);
        if(i===-1)ids.push(id);else ids.splice(i,1);
        localStorage.setItem('wpst_template_favorites',JSON.stringify(ids));
    }

    function recentIds(){
        try{return JSON.parse(localStorage.getItem('wpst_template_recents')||'[]');}catch(e){return [];}
    }
    function markRecent(item,kind){
        var id=favId(item,kind),ids=recentIds().filter(function(x){return x!==id;});
        ids.unshift(id); localStorage.setItem('wpst_template_recents',JSON.stringify(ids.slice(0,12)));
    }
    function isRecent(item,kind){return recentIds().indexOf(favId(item,kind))!==-1;}
    function adaptEnabled(){
        if(!modal)return true;
        return modal.find('.wpst-library-adapt-design').is(':checked');
    }
    function adaptTemplateData(data){
        if(!adaptEnabled() || !cfg.design_system)return cloneModel(data);
        var ds=cfg.design_system||{};
        var palette={
            '#2563eb':ds.primary,'#1d4ed8':ds.primary,'#7c3aed':ds.secondary,'#0f172a':ds.heading,
            '#111827':ds.heading,'#334155':ds.text,'#64748b':ds.muted,'#ffffff':ds.surface,
            '#f8fafc':ds.soft,'#e2e8f0':ds.border,'#0ea5e9':ds.accent
        };
        function walk(v,key){
            if(Array.isArray(v))return v.map(function(x){return walk(x,key);});
            if(v && typeof v==='object'){
                var o={};Object.keys(v).forEach(function(k){o[k]=walk(v[k],k);});return o;
            }
            if(typeof v==='string'){
                var low=v.toLowerCase();
                if(palette[low])return palette[low]||v;
                if((key||'').toLowerCase().indexOf('font_family')!==-1){
                    var isHeading=/(title|heading|headline)/i.test(key||'');
                    var font=isHeading?ds.heading_font:ds.body_font;
                    if(font && font!=='system')return font;
                }
            }
            return v;
        }
        return walk(cloneModel(data),'');
    }

    function qualityScore(item){
        var score=0;
        if(item.quality==='Signature')score+=50;
        else if(item.quality==='Modern')score+=32;
        else if(item.quality==='Legacy')score-=60;
        else score+=12;
        if(item.preview_image)score+=14;
        if(item.desc && String(item.desc).length>=45)score+=8;
        if(item.category)score+=5;
        if(item.sector)score+=4;
        if(item.style)score+=4;
        if(item.is_popular)score+=8;
        if(item.is_new)score+=5;
        if(item.signature_ui)score+=6;
        return score;
    }
    function qualityLabel(item){
        var s=qualityScore(item);
        if(s>=72)return 'Premium';
        if(s>=50)return 'Seçkin';
        return 'Modern';
    }

    function buildCards(type){
        var list=listForKind(type).filter(function(item){return !item.validation || item.validation.valid!==false;});
        if(!list.length) return '<div class="wpst-library-empty">Henüz şablon bulunmuyor.</div>';
        return list.map(function(item){
            var kind=item.kind || (type==='all'?'widgets':type);
            var badges='';
            if(item.quality==='Signature')badges+='<span class="wpst-lib-badge is-signature">Signature</span>';
            else if(item.quality==='Modern')badges+='<span class="wpst-lib-badge is-modern">Modern</span>';
            else if(item.premium)badges+='<span class="wpst-lib-badge is-premium">Premium</span>';
            if(item.is_new)badges+='<span class="wpst-lib-badge is-new">Yeni</span>';
            else if(item.is_popular)badges+='<span class="wpst-lib-badge is-popular">Popüler</span>';
            if(item.is_featured)badges+='<span class="wpst-lib-badge is-featured">Öne Çıkan</span>';
            var qScore=qualityScore(item),qLabel=qualityLabel(item);
            if(item.quality!=='Signature')badges+='<span class="wpst-lib-badge is-curated">'+esc(qLabel)+'</span>';

            var industries=Array.isArray(item.industries)?item.industries:[item.sector||''];
            var styles=Array.isArray(item.styles)?item.styles:[item.style||''];
            var tags=Array.isArray(item.tags)?item.tags:[];
            var widgets=Array.isArray(item.widgets)?item.widgets:[];
            var audit=item.audit||{},auditScore=Number(audit.score||0),similarity=audit.similarity||{};
            return '<article class="wpst-library-card" data-key="'+esc(item.key)+'" data-title="'+esc(item.title||'')+'" data-kind="'+esc(kind)+'" data-source-kind="'+esc(item.source_kind||kind)+'" data-category="'+esc(item.category||'')+'" data-sector="'+esc(industries.join('|'))+'" data-style="'+esc(styles.join('|'))+'" data-tags="'+esc(tags.join('|'))+'" data-widgets="'+esc(widgets.join('|'))+'" data-new="'+(item.is_new?'1':'0')+'" data-featured="'+(item.is_featured?'1':'0')+'" data-popular="'+(item.is_popular?'1':'0')+'" data-favorite="'+(isFav(item,kind)?'1':'0')+'" data-recent="'+(isRecent(item,kind)?'1':'0')+'" data-premium="'+(item.premium?'1':'0')+'" data-quality="'+esc(item.quality||'Standard')+'" data-quality-score="'+qScore+'" data-audit-score="'+auditScore+'" data-similarity="'+esc(similarity.level||'')+'" data-signature-ui="'+esc(item.signature_ui||'')+'">' +
                '<div class="wpst-library-preview '+esc(item.preview_class || '')+'">' +
                    (item.preview_image ? '<img class="wpst-library-preview-img" loading="lazy" decoding="async" src="'+esc(item.preview_image)+'" alt="'+esc((item.title||'WPSoft')+' önizlemesi')+'"><div class="wpst-library-preview-fallback" aria-hidden="true">WPSoft</div>' : '<div class="wpst-library-preview-ui">' + (item.preview_html || '<span>WPSoft</span>') + '</div>') +
                    '<div class="wpst-lib-badges">'+badges+'</div>'+
                    '<button type="button" class="wpst-library-favorite '+(isFav(item,kind)?'is-active':'')+'" aria-label="Favori">♡</button>'+

                '</div>' +
                '<div class="wpst-library-card-body">' +
                    '<div class="wpst-library-card-copy"><div class="wpst-library-card-meta"><span>'+esc(kind==='pages'?(item.category||'Sayfa'):kindLabel(kind))+'</span>'+(industries.filter(Boolean).length?'<b>'+esc(industries.filter(Boolean)[0])+'</b>':'')+(kind==='pages'&&styles.filter(Boolean).length?'<b>'+esc(styles.filter(Boolean)[0])+'</b>':'')+'</div><h3>'+esc(item.title)+'</h3><p>'+esc(item.desc || '')+'</p><div class="wpst-library-card-quality"><span>'+esc(kind==='pages'&&auditScore?(auditScore+'/100 · '+(audit.grade||'')):qLabel)+'</span><i></i></div></div>' +
                    '<div class="wpst-library-card-actions"><button type="button" class="wpst-library-preview-large wpst-library-preview-secondary">Önizle</button><button type="button" class="wpst-library-insert">Ekle</button></div>' +
                '</div>' +
            '</article>';
        }).join('');
    }

    function modalHtml(){
        return '<div class="wpst-library-overlay" aria-hidden="true">' +
            '<div class="wpst-library-modal" role="dialog" aria-modal="true" aria-label="WPSoft Şablonlar">' +
                '<header class="wpst-library-head">' +
                    '<div class="wpst-library-brand"><span class="wpst-library-logo">W</span><div><strong>WPSoft Template Library</strong><small>Seçilmiş premium sayfalar ve bölümler</small></div></div>' +
                    '<button type="button" class="wpst-library-close" aria-label="Kapat">×</button>' +
                '</header>' +
                '<nav class="wpst-library-tabs">' +
                    '<button type="button" data-tab="all" class="is-active">Tümü</button>' +
                    '<button type="button" data-tab="pages">Sayfalar</button>' +
                    '<button type="button" data-tab="portfolio">Portföy</button>' +
                    '<button type="button" data-tab="sections">Bölümler</button>' +
                    '<button type="button" data-tab="headers">Header</button>' +
                    '<button type="button" data-tab="footers">Footer</button>' +
                    '<button type="button" data-tab="blog">Blog</button>' +
                    '<button type="button" data-tab="mega_menus">Mega Menü</button>' +
                '</nav>' +
                '<section class="wpst-library-pages-intro" aria-labelledby="wpst-pages-title"><div><h2 id="wpst-pages-title">WPSoft Sayfa Şablonları</h2><p>Profesyonel sayfaları tek tıkla Elementor\'a ekleyin.</p></div><span class="wpst-library-pages-count"></span></section>'+
                '<div class="wpst-library-toolbar wpst-library-toolbar-simple">' +
                    '<div class="wpst-library-searchbox"><span>⌕</span><input type="search" class="wpst-library-search" placeholder="Şablonlarda ara..." aria-label="Şablonlarda ara"></div>'+
                    '<select class="wpst-library-sort" aria-label="Sıralama"><option value="recommended">Kaliteye Göre</option><option value="new">En Yeni</option><option value="popular">Popüler</option><option value="az">A–Z</option></select>'+
                    '<button type="button" class="wpst-library-advanced-toggle">Filtreler <span>⌄</span></button>'+
                    '<span class="wpst-library-result-count"></span>'+
                '</div>' +
                '<div class="wpst-library-advanced-filters">' +
                    '<select class="wpst-library-category"><option value="">Tüm Kategoriler</option></select>'+
                    '<select class="wpst-library-style"><option value="">Tüm Stiller</option></select>'+
                    '<select class="wpst-library-sector"><option value="">Tüm Sektörler</option></select>'+
                    '<select class="wpst-library-quality"><option value="recommended">Seçilmiş Koleksiyon</option><option value="Signature">Signature</option><option value="Modern">Modern</option><option value="">Tüm kaliteler</option></select>'+
                    '<select class="wpst-library-status"><option value="">Tüm durumlar</option><option value="new">Yeni</option><option value="popular">Popüler</option><option value="favorites">Favoriler</option><option value="recent">Son Kullanılanlar</option><option value="free">Ücretsiz</option><option value="premium">Premium</option></select>'+
                    '<label class="wpst-library-adapt-wrap"><input type="checkbox" class="wpst-library-adapt-design" checked> <span>Global tasarıma uyarla</span></label>'+
                '</div>' +
                '<div class="wpst-library-category-chips"></div>'+
                '<div class="wpst-library-quickfilters">'+
                    '<button type="button" data-quick="favorites">♡ Favoriler</button>'+
                    '<button type="button" data-quick="recent">Son Kullanılanlar</button>'+
                    '<button type="button" data-quick="new">Yeni</button>'+
                    '<button type="button" data-quick-reset="1" class="is-reset">Filtreleri Temizle</button>'+
                '</div>'+
                '<main class="wpst-library-content"><div class="wpst-library-grid"></div></main>' +
                '<div class="wpst-library-loading"><span class="wpst-spinner"></span><strong>Sayfaya ekleniyor…</strong></div>' +
            '</div>' +
        '</div>';
    }

    function categoryOptions(){
        var seen={};
        listForKind('all').forEach(function(x){if(x.category)seen[x.category]=1;});
        return Object.keys(seen).sort();
    }
    function sectorOptions(){
        var seen={};
        listForKind('all').forEach(function(x){if(x.sector)seen[x.sector]=1;});
        return Object.keys(seen).sort();
    }

    function overviewKinds(){
        return [
            ['pages','Sayfalar'],
            ['portfolio','Portföy'],
            ['sections','Bölümler'],
            ['headers','Header'],
            ['footers','Footer'],
            ['blog','Blog'],
            ['mega_menus','Mega Menü']
        ];
    }

    function renderKindOverview(){
        if(!modal)return;
        var box=modal.find('.wpst-library-kind-overview');
        box.html(overviewKinds().map(function(row){
            var kind=row[0],label=row[1],count=listForKind(kind).length;
            return '<button type="button" data-overview-kind="'+esc(kind)+'" class="'+(currentTab===kind?'is-active':'')+'"><span>'+esc(label)+'</span><b>'+count+'</b></button>';
        }).join(''));
    }
    function refreshFilters(kind, resetValues){
        if(!modal)return;
        kind = kind || currentTab || 'all';

        var source = listForKind(kind);
        var categories={}, sectors={}, styles={};
        source.forEach(function(x){
            if(x.category)categories[x.category]=1;
            (Array.isArray(x.industries)?x.industries:[x.sector]).forEach(function(v){if(v)sectors[v]=1;});
            (Array.isArray(x.styles)?x.styles:[x.style]).forEach(function(v){if(v)styles[v]=1;});
        });

        var cat=modal.find('.wpst-library-category');
        var sty=modal.find('.wpst-library-style');
        var sec=modal.find('.wpst-library-sector');

        cat.html('<option value="">Tüm Kategoriler</option>'+Object.keys(categories).sort().map(function(x){
            return '<option value="'+esc(x)+'">'+esc(x)+'</option>';
        }).join(''));

        sty.html('<option value="">Tüm Stiller</option>'+Object.keys(styles).sort().map(function(x){
            return '<option value="'+esc(x)+'">'+esc(x)+'</option>';
        }).join(''));

        sec.html('<option value="">Tüm Sektörler</option>'+Object.keys(sectors).sort().map(function(x){
            return '<option value="'+esc(x)+'">'+esc(x)+'</option>';
        }).join(''));

        // Section library uses visual category chips for faster discovery.
        var chips=modal.find('.wpst-library-category-chips');
        if(kind==='sections' || kind==='pages' || kind==='portfolio' || kind==='blog' || kind==='single_blog' || kind==='blog_archive' || kind==='woocommerce'){
            chips.html('<button type="button" class="is-active" data-category-chip="">Tümü</button>'+Object.keys(categories).sort().map(function(x){
                return '<button type="button" data-category-chip="'+esc(x)+'">'+esc(x)+'</button>';
            }).join('')).show();
        }else{
            chips.empty().hide();
        }

        // Sector filtering only makes sense when the current type actually has sector data.
        sty.toggle((kind==='sections' || kind==='pages') && Object.keys(styles).length>0);
        sec.toggle(Object.keys(sectors).length>0);

        if(resetValues){
            cat.val('');
            sty.val('');
            sec.val('');
            modal.find('.wpst-library-status').val('');
            modal.find('.wpst-library-quality').val('recommended');
        }
    }

    function ensureModal(){
        if(modal) return modal;
        $('body').append(modalHtml());
        modal = $('.wpst-library-overlay').last();
        modal.on('click', function(e){ if(e.target === this) closeModal(); });
        modal.find('.wpst-library-close').on('click', closeModal);
        modal.on('click','[data-category-chip]',function(){
            var value=$(this).attr('data-category-chip')||'';
            modal.find('[data-category-chip]').removeClass('is-active');
            $(this).addClass('is-active');
            modal.find('.wpst-library-category').val(value);
            filterCards();
        });
        modal.on('change','.wpst-library-category',function(){
            var value=$(this).val()||'';
            modal.find('[data-category-chip]').removeClass('is-active').filter(function(){return ($(this).attr('data-category-chip')||'')===value;}).addClass('is-active');
        });
        modal.find('.wpst-library-tabs button').on('click', function(){ switchTab($(this).data('tab')); });
        modal.on('click','.wpst-library-tabs button,[data-category-chip]',function(){
            if(window.innerWidth<783 && this.scrollIntoView) this.scrollIntoView({behavior:'smooth',block:'nearest',inline:'center'});
        });

        modal.on('change','.wpst-library-sort',function(){sortCards();});

        modal.on('click','.wpst-library-advanced-toggle',function(){
            var open=modal.toggleClass('is-advanced-open').hasClass('is-advanced-open');
            $(this).toggleClass('is-active',open).find('span').text(open?'⌃':'⌄');
        });

        modal.find('.wpst-library-search,.wpst-library-category,.wpst-library-style,.wpst-library-sector,.wpst-library-quality,.wpst-library-status').on('input change', filterCards);
        modal.on('click','[data-quick]',function(){
            var value=$(this).data('quick')||'';
            modal.find('.wpst-library-status').val(value).trigger('change');
            modal.find('.wpst-library-quickfilters [data-quick]').removeClass('is-active');
            $(this).addClass('is-active');
        });
        modal.on('click','[data-quick-kind]',function(){
            var kind=$(this).data('quick-kind')||'all';
            switchTab(kind);
            modal.find('.wpst-library-quickfilters [data-quick-kind]').removeClass('is-active');
            $(this).addClass('is-active');
        });
        modal.on('click','[data-overview-kind]',function(){
            switchTab($(this).data('overview-kind')||'all');
        });
        modal.on('click','[data-quick-reset]',function(){
            modal.find('.wpst-library-search').val('');
            modal.find('.wpst-library-category,.wpst-library-style,.wpst-library-sector,.wpst-library-status').val('');
            modal.find('.wpst-library-quality').val('recommended');
            modal.find('.wpst-library-sort').val('recommended');
            modal.find('.wpst-library-quickfilters button').removeClass('is-active');
            modal.removeClass('is-advanced-open');
            modal.find('.wpst-library-advanced-toggle').removeClass('is-active').find('span').text('⌄');
            switchTab(currentTab);
        });
        modal.on('click', '.wpst-library-insert', function(){
            var $card = $(this).closest('.wpst-library-card');
            insertItem($card.data('kind'), $card.data('key'), $(this));
        });
        modal.on('error','.wpst-library-preview-img',function(){
            $(this).addClass('is-broken').attr('aria-hidden','true').siblings('.wpst-library-preview-fallback').addClass('is-visible');
        });
        modal.on('click','.wpst-library-favorite',function(e){
            e.preventDefault();e.stopPropagation();
            var card=$(this).closest('.wpst-library-card'),kind=card.data('kind'),key=card.data('key');
            var item=findPayload(kind,key);
            if(item){toggleFav(item,kind);switchTab(currentTab);}
        });
        modal.on('click','.wpst-library-preview-large',function(){
            lastPreviewTrigger=this;
            var card=$(this).closest('.wpst-library-card'),kind=card.data('kind'),key=card.data('key'),item=findPayload(kind,key);
            if(!item)return;
            var img=item.preview_image||'';
            $('body').append('<div class="wpst-library-lightbox" data-kind="'+esc(kind)+'" data-key="'+esc(key)+'"><button class="wpst-library-lightbox-close">×</button><div class="wpst-library-lightbox-inner">'+
                '<div class="wpst-library-lightbox-toolbar"><div><strong>Tam Önizleme</strong><small>'+esc(item.quality||'Modern')+(item.signature_ui?' · UI '+esc(item.signature_ui):'')+'</small></div><div class="wpst-library-device-switch"><button type="button" class="is-active" data-preview-device="desktop">Masaüstü</button><button type="button" data-preview-device="tablet">Tablet</button><button type="button" data-preview-device="mobile">Mobil</button></div><button type="button" class="wpst-library-fullscreen-toggle" aria-label="Tam ekran">⛶</button></div>'+
                '<div class="wpst-library-preview-device is-desktop">'+(img?'<img src="'+esc(img)+'" alt="">':'<div class="wpst-library-lightbox-fallback">WPSoft</div>')+'</div>'+
                '<div class="wpst-library-lightbox-info"><span>'+esc(kindLabel(kind))+(item.category?' · '+esc(item.category):'')+'</span><h2>'+esc(item.title)+'</h2><p>'+esc(item.desc||'')+'</p><div class="wpst-library-lightbox-actions"><button class="button wpst-library-lightbox-favorite">'+(isFav(item,kind)?'♥ Favorilerde':'♡ Favoriye Ekle')+'</button><button class="button button-primary wpst-library-lightbox-insert">Bu Şablonu Ekle</button></div></div></div></div>');
            $('.wpst-library-lightbox').last().find('.wpst-library-lightbox-close').trigger('focus');
        });
        $(document).on('click','.wpst-library-lightbox-close',function(){$('.wpst-library-lightbox').remove();if(lastPreviewTrigger&&lastPreviewTrigger.focus)lastPreviewTrigger.focus();});
        $(document).on('click','.wpst-library-fullscreen-toggle',function(){
            var lb=$(this).closest('.wpst-library-lightbox');
            lb.toggleClass('is-fullscreen');
            $(this).text(lb.hasClass('is-fullscreen')?'↙':'⛶').attr('aria-label',lb.hasClass('is-fullscreen')?'Tam ekrandan çık':'Tam ekran');
        });

        $(document).on('click','[data-preview-device]',function(){
            var btn=$(this),box=btn.closest('.wpst-library-lightbox');
            box.find('[data-preview-device]').removeClass('is-active');btn.addClass('is-active');
            box.find('.wpst-library-preview-device').removeClass('is-desktop is-tablet is-mobile').addClass('is-'+btn.data('preview-device'));
        });

        $(document).on('keydown.wpstLibraryPreview',function(e){
            var lb=$('.wpst-library-lightbox').last();
            if(!lb.length)return;
            if(e.key==='Escape'){lb.remove();if(lastPreviewTrigger&&lastPreviewTrigger.focus)lastPreviewTrigger.focus();return;}
            if(e.key==='f' || e.key==='F'){lb.find('.wpst-library-fullscreen-toggle').trigger('click');return;}
            if(e.key==='ArrowLeft' || e.key==='ArrowRight'){
                var btns=lb.find('[data-preview-device]'),active=btns.index(btns.filter('.is-active'));
                var next=e.key==='ArrowRight'?Math.min(btns.length-1,active+1):Math.max(0,active-1);
                btns.eq(next).trigger('click');
            }
        });
        $(document).on('click','.wpst-library-lightbox-favorite',function(){
            var lb=$(this).closest('.wpst-library-lightbox'),kind=lb.data('kind'),key=lb.data('key'),item=findPayload(kind,key);
            if(!item)return;toggleFav(item,kind);
            $(this).text(isFav(item,kind)?'♥ Favorilerde':'♡ Favoriye Ekle');
            if(modal){var card=modal.find('.wpst-library-card[data-kind="'+kind+'"][data-key="'+key+'"]');card.attr('data-favorite',isFav(item,kind)?'1':'0');card.find('.wpst-library-favorite').toggleClass('is-active',isFav(item,kind));filterCards();}
        });
        $(document).on('click','.wpst-library-lightbox-insert',function(){
            var lb=$(this).closest('.wpst-library-lightbox');
            insertItem(lb.data('kind'),lb.data('key'));lb.remove();
        });
        $(document).on('keydown.wpstLibrary', function(e){ if(e.key === 'Escape'){ $('.wpst-library-lightbox').remove(); if(modal && modal.hasClass('is-open')) closeModal(); } });
        $(document).on('keydown.wpstLibraryTrap',function(e){
            if(e.key!=='Tab')return;
            var scope=$('.wpst-library-lightbox').last();
            if(!scope.length && modal && modal.hasClass('is-open'))scope=modal.find('.wpst-library-modal');
            if(!scope.length)return;
            var focusable=scope.find('button:not([disabled]),input:not([disabled]),select:not([disabled]),[href],[tabindex]:not([tabindex="-1"])').filter(':visible');
            if(!focusable.length)return;
            var first=focusable.get(0),last=focusable.get(focusable.length-1);
            if(e.shiftKey && document.activeElement===first){e.preventDefault();last.focus();}
            else if(!e.shiftKey && document.activeElement===last){e.preventDefault();first.focus();}
        });
        refreshFilters('all', true);
        return modal;
    }

    function switchTab(tab){
        currentTab = ['all','pages','portfolio','sections','headers','footers','blog','single_blog','blog_archive','woocommerce','popups','mega_menus','widgets'].indexOf(tab)!==-1 ? tab : 'all';
        var $m = ensureModal();

        $m.find('.wpst-library-tabs button')
            .removeClass('is-active')
            .filter('[data-tab="'+currentTab+'"]')
            .addClass('is-active');

        // Important: old category/sector selections must not hide a newly selected type.
        refreshFilters(currentTab, true);

        var cards = buildCards(currentTab);
        $m.find('.wpst-library-grid').html(cards);

        filterCards();

        // Diagnostic class is useful for UI styling and guarantees a direct Footer state.
        $m.attr('data-active-library-kind',currentTab);
        $m.find('.wpst-library-pages-count').text(listForKind('pages').length+' şablon');
        renderKindOverview();
    }

    function sortCards(){
        if(!modal)return;
        var mode=modal.find('.wpst-library-sort').val()||'recommended';
        var grid=modal.find('.wpst-library-grid').get(0);
        if(!grid)return;
        var cards=Array.prototype.slice.call(grid.querySelectorAll('.wpst-library-card'));
        cards.sort(function(a,b){
            if(mode==='az')return String(a.dataset.title||'').localeCompare(String(b.dataset.title||''),'tr');
            if(mode==='new')return Number(b.dataset.new||0)-Number(a.dataset.new||0) || Number(b.dataset.popular||0)-Number(a.dataset.popular||0);
            if(mode==='popular')return Number(b.dataset.popular||0)-Number(a.dataset.popular||0) || Number(b.dataset.new||0)-Number(a.dataset.new||0);
            var aq=Number(a.dataset.qualityScore||0);
            var bq=Number(b.dataset.qualityScore||0);
            return bq-aq || Number(b.dataset.popular||0)-Number(a.dataset.popular||0) || Number(b.dataset.new||0)-Number(a.dataset.new||0);
        });
        cards.forEach(function(card){grid.appendChild(card);});
    }

    function filterCards(){
        if(!modal)return;
        var q=String(modal.find('.wpst-library-search').val()||'').toLocaleLowerCase('tr-TR').trim();
        var cat=modal.find('.wpst-library-category').val()||'';
        var style=modal.find('.wpst-library-style').val()||'';
        var sector=modal.find('.wpst-library-sector').val()||'';
        var quality=modal.find('.wpst-library-quality').val()||'';
        var status=modal.find('.wpst-library-status').val()||'';
        var visible=0;
        modal.find('.wpst-library-card').each(function(){
            var card=$(this);
            var searchText=[
                card.text(),
                card.attr('data-category')||'',
                card.attr('data-sector')||'',
                card.attr('data-style')||'',
                card.attr('data-tags')||'',
                card.attr('data-widgets')||'',
                card.attr('data-kind')||'',
                card.attr('data-key')||''
            ].join(' ').toLocaleLowerCase('tr-TR');
            var ok=!q || searchText.indexOf(q)!==-1;
            if(cat && card.attr('data-category')!==cat)ok=false;
            if(style && ('|'+(card.attr('data-style')||'')+'|').indexOf('|'+style+'|')===-1)ok=false;
            if(sector && ('|'+(card.attr('data-sector')||'')+'|').indexOf('|'+sector+'|')===-1)ok=false;
            if(quality==='recommended' && (card.attr('data-quality')==='Legacy' || Number(card.attr('data-quality-score')||0)<28))ok=false;
            else if(quality && quality!=='recommended' && card.attr('data-quality')!==quality)ok=false;
            if(status==='new' && card.attr('data-new')!=='1')ok=false;
            if(status==='popular' && card.attr('data-popular')!=='1')ok=false;
            if(status==='favorites' && card.attr('data-favorite')!=='1')ok=false;
            if(status==='recent' && card.attr('data-recent')!=='1')ok=false;
            if(status==='free' && card.attr('data-premium')==='1')ok=false;
            if(status==='premium' && card.attr('data-premium')!=='1')ok=false;
            card.toggle(ok);if(ok)visible++;
        });
        sortCards();
        var total=modal.find('.wpst-library-card').length;
        modal.find('.wpst-library-result-count').text(currentTab==='pages'?(visible+' şablon'):(visible+' / '+total+' sonuç'));
        modal.find('.wpst-library-content').toggleClass('has-no-results',visible===0);
        if(visible===0 && !modal.find('.wpst-library-no-results').length){
            modal.find('.wpst-library-grid').after('<div class="wpst-library-no-results" role="status"><strong>Bu filtrelere uygun şablon bulunamadı.</strong><span>Arama kelimesini veya filtreleri değiştirin.</span><button type="button" data-quick-reset="1">Filtreleri Temizle</button></div>');
        }
        modal.find('.wpst-library-no-results').toggle(visible===0);
    }

    function openModal(tab){
        lastFocusedElement=document.activeElement;
        var $m = ensureModal();
        switchTab(tab || 'all');
        $m.addClass('is-open').attr('aria-hidden','false');
        setTimeout(function(){ $m.find('.wpst-library-search').trigger('focus'); }, 80);
    }
    function closeModal(){ if(!modal || busy) return; modal.removeClass('is-open').attr('aria-hidden','true'); if(lastFocusedElement&&lastFocusedElement.focus)lastFocusedElement.focus(); }
    function closeModalForce(){ if(modal) modal.removeClass('is-open').attr('aria-hidden','true'); }

    function cloneModel(model){ return JSON.parse(JSON.stringify(model || {})); }

    async function createRecursive(model, parentContainer, at){
        var clean = cloneModel(model);
        var children = Array.isArray(clean.elements) ? clean.elements : [];
        delete clean.elements;
        delete clean.id;
        if(!clean.elType) clean.elType = clean.widgetType ? 'widget' : 'container';

        var result = await $e.run('document/elements/create', {
            model: clean,
            container: parentContainer,
            options: typeof at === 'number' ? { at: at } : {}
        });
        var createdId = result && (result.id || (result.model && result.model.id));
        var childContainer = createdId && window.elementor && typeof elementor.getContainer === 'function' ? elementor.getContainer(createdId) : null;
        if(children.length && childContainer){
            for(var i=0;i<children.length;i++) await createRecursive(children[i], childContainer, i);
        }
        return result;
    }

    function findPayload(kind,key){
        var list=listForKind(kind);
        for(var i=0;i<list.length;i++) if(list[i].key === key) return list[i];
        return null;
    }

    async function insertItem(kind,key,trigger){
        if(busy) return;
        if(!editorReady()){
            alert('Elementor editörü hazır değil. Sayfayı yenileyip tekrar deneyin.');
            return;
        }
        var item = findPayload(kind,key);
        if(!item || !item.data) return;
        var insertData = adaptTemplateData(item.data);
        busy = true;
        modal.addClass('is-busy');
        var button=trigger&&trigger.length?trigger:null;
        if(button)button.prop('disabled',true).attr('aria-busy','true').data('label',button.text()).text('Ekleniyor...');
        try{
            var root = typeof elementor.getPreviewContainer === 'function' ? elementor.getPreviewContainer() : null;
            if(!root && elementor.getContainer) root = elementor.getContainer('document');
            if(!root) throw new Error('Elementor kök konteyneri bulunamadı.');

            var actualKind = item.source_kind || kind;

            // Portfolio library cards are full Elementor compositions. When editing
            // a portfolio post, replace an empty document with the selected design;
            // otherwise append it like every other WPSoft template.
            if(kind === 'portfolio' && window.elementor && elementor.config && elementor.config.document){
                var docType = String((elementor.config.document.type || elementor.config.document.post_type || '')).toLowerCase();
                var isPortfolioDocument = docType.indexOf('wpst_portfolio') !== -1 || docType.indexOf('portfolio') !== -1;
                if(isPortfolioDocument && root && root.children && root.children.length === 0){
                    // normal insertion below is enough; this branch documents the
                    // intended portfolio editing context without a custom endpoint.
                }
            }

            if(actualKind === 'widgets'){
                var shell = {
                    elType: 'container',
                    settings: { content_width:'boxed', width:{unit:'px',size:1200,sizes:[]}, padding:{unit:'px',top:'24',right:'24',bottom:'24',left:'24',isLinked:true} },
                    elements: [ insertData ]
                };
                await createRecursive(shell, root);
            } else {
                var rows = Array.isArray(insertData) ? insertData : [insertData];
                for(var r=0;r<rows.length;r++) await createRecursive(rows[r], root);
            }
            markRecent(item,kind);
            closeModalForce();
            if(window.elementor && elementor.notifications && elementor.notifications.showToast){
                elementor.notifications.showToast({message:'WPSoft şablonu sayfaya eklendi.'});
            }
        }catch(err){
            console.error('WPSoft template insert error',err);
            alert('Şablon eklenirken hata oluştu. Elementor sürümünüzle uyumluluğu kontrol edin.');
        }finally{
            busy = false;
            if(modal) modal.removeClass('is-busy');
            if(button)button.prop('disabled',false).removeAttr('aria-busy').text(button.data('label')||'Ekle');
        }
    }

    function bindIframeLoad(){
        var iframe = getPreviewIframe();
        if(!iframe || iframe.dataset.wpstBound === '1') return;
        iframe.dataset.wpstBound = '1';
        iframe.addEventListener('load', function(){
            observedDoc = null;
            if(previewObserver){ try{previewObserver.disconnect();}catch(e){} previewObserver = null; }
            setTimeout(injectLaunchers,100);
            setTimeout(injectLaunchers,600);
        });
    }

    function boot(){
        ensureModal();
        // Preview launcher is injected only by elementor-preview-launcher.js.
        // Keeping a single injector prevents duplicate W buttons in Elementor.
    }


    window.addEventListener('message', function(event){
        var data = event && event.data ? event.data : {};
        if(data && data.type === 'wpst-open-library') openModal(data.tab || 'widgets');
    });

    window.WPSTOpenLibrary = openModal;
    $(window).on('elementor:init', function(){ setTimeout(boot,200); });
    $(function(){ setTimeout(boot,500); });
})(jQuery);


/* WPSoft generic Elementor-data insertion bridge */
window.WPSTInsertElementorData = window.WPSTInsertElementorData || function(elements){
    try{
        var list = Array.isArray(elements) ? elements : [elements];
        if(!window.elementor || !list.length) return false;

        // Preferred Elementor command API.
        if(elementor.commands && typeof elementor.commands.run === 'function'){
            list.forEach(function(model){
                elementor.commands.run('document/elements/create', {
                    model: model,
                    options: { at: 0 }
                });
            });
            return true;
        }

        // Fallback used by older Elementor editors.
        if(elementor.getPreviewView && elementor.getPreviewView().addChildModel){
            list.forEach(function(model){
                elementor.getPreviewView().addChildModel(model);
            });
            return true;
        }
    }catch(e){
        if(window.console) console.error('WPSoft insert error', e);
    }
    return false;
};
