(function(){
    'use strict';
    if (window.__wpstPreviewLauncherBooted) return;
    window.__wpstPreviewLauncherBooted = true;

    var observer = null;
    var timer = null;

    function openLibrary(e){
        if(e){ e.preventDefault(); e.stopPropagation(); }
        try { window.parent.postMessage({type:'wpst-open-library',tab:'widgets'}, '*'); } catch(err){}
        return false;
    }

    function createButton(){
        var b=document.createElement('button');
        b.type='button';
        b.className='wpst-preview-launcher is-compact';
        b.setAttribute('data-wpst-launcher','1');
        b.title='WPSoft Şablonlar';
        b.setAttribute('aria-label','WPSoft Şablonlar');
        b.innerHTML='<span aria-hidden="true">W</span><em>WPSoft Şablonlar</em>';
        b.addEventListener('mousedown',function(e){e.preventDefault();e.stopPropagation();},true);
        b.addEventListener('click',openLibrary,true);
        return b;
    }

    function visible(el){
        if(!el) return false;
        var cs=getComputedStyle(el);
        return cs.display!=='none' && cs.visibility!=='hidden' && cs.opacity!=='0';
    }

    function unique(arr){
        return arr.filter(function(v,i,a){return v && a.indexOf(v)===i;});
    }

    function findEmptyRoots(){
        var roots=[];
        var selectors=[
            '.elementor-add-new-section',
            '.elementor-empty-view',
            '.elementor-add-section-inner',
            '.e-con.e-con-empty',
            '[data-element_type="container"].e-con-empty'
        ];
        selectors.forEach(function(sel){
            try{ document.querySelectorAll(sel).forEach(function(n){ if(visible(n)) roots.push(n); }); }catch(e){}
        });

        // Current Elementor UI fallback: find the smallest node containing the drag-widget hint.
        if(!roots.length){
            try{
                var matches=[];
                document.querySelectorAll('div,section').forEach(function(n){
                    if(!visible(n)) return;
                    var t=String(n.textContent||'').replace(/\s+/g,' ').trim().toLowerCase();
                    if(!t || t.length>90) return;
                    if(t.indexOf('widget')===-1) return;
                    if(t.indexOf('sürük')===-1 && t.indexOf('drag')===-1) return;
                    matches.push(n);
                });
                matches.sort(function(a,b){ return a.querySelectorAll('*').length-b.querySelectorAll('*').length; });
                if(matches[0]){
                    var r=matches[0].closest('.elementor-empty-view,.elementor-add-new-section,.e-con,[data-element_type="container"]') || matches[0].parentElement;
                    if(r) roots.push(r);
                }
            }catch(e){}
        }
        return unique(roots);
    }

    function findNativeToolbar(root){
        if(!root) return null;
        // Prefer the wrapper that contains Elementor's template/AI controls.
        var folder=root.querySelector('.eicon-folder,[class*="eicon-folder"],.elementor-add-template-button,button[aria-label*="template" i],button[title*="template" i],button[aria-label*="şablon" i],button[title*="şablon" i]');
        if(folder){
            var target=folder.closest('button,a') || folder;
            return {host:target.parentElement||root,anchor:target};
        }
        var buttons=Array.prototype.slice.call(root.querySelectorAll('button,a')).filter(visible);
        if(buttons.length>=2){
            // Elementor's round controls are siblings; use their shared parent.
            var anchor=buttons[buttons.length-1];
            return {host:anchor.parentElement||root,anchor:anchor};
        }
        return null;
    }

    function cleanDuplicates(){
        var all=document.querySelectorAll('.wpst-preview-launcher,.wpst-template-launcher,.wpst-inline-launcher');
        // Remove launchers created by legacy injector systems. Current launcher will be re-added below.
        all.forEach(function(n){
            if(n.classList.contains('wpst-preview-launcher')) return;
            n.remove();
        });
        // Strictly keep max one preview launcher per native control host.
        var byHost=[];
        document.querySelectorAll('.wpst-preview-launcher').forEach(function(n){
            var h=n.parentElement;
            var key=byHost.indexOf(h);
            if(key!==-1) n.remove(); else byHost.push(h);
        });
    }

    function inject(){
        cleanDuplicates();
        var roots=findEmptyRoots();
        roots.forEach(function(root){
            var info=findNativeToolbar(root);
            if(!info || !info.host) return;
            if(info.host.querySelector(':scope > .wpst-preview-launcher')) return;
            var b=createButton();
            if(info.anchor && info.anchor.nextSibling) info.host.insertBefore(b,info.anchor.nextSibling);
            else info.host.appendChild(b);
        });
    }

    function schedule(){
        clearTimeout(timer);
        timer=setTimeout(function(){ requestAnimationFrame(inject); },120);
    }

    function boot(){
        inject();
        observer=new MutationObserver(schedule);
        observer.observe(document.documentElement,{subtree:true,childList:true});
    }
    if(document.readyState==='loading') document.addEventListener('DOMContentLoaded',boot,{once:true});
    else boot();
})();
