(function(){
'use strict';
function cleanupThemeChrome(){
  const body=document.body;
  if(!body)return;
  const targets=[];
  if(body.classList.contains('wpst-theme-header-hidden')){
    targets.push('#site-header','header#site-header','.site-header','#masthead','header#top','#header-outer','.whb-header');
  }
  if(body.classList.contains('wpst-theme-footer-hidden')){
    targets.push('#site-footer','footer#site-footer','.site-footer','#colophon','#footer-outer','#copyright','.footer-container');
  }
  targets.forEach(sel=>{
    document.querySelectorAll(sel).forEach(el=>{
      if(el.closest('.wpsoft-site-header,.wpsoft-site-footer')) return;
      el.style.setProperty('display','none','important');
      el.setAttribute('aria-hidden','true');
    });
  });
}
if(document.readyState==='loading') document.addEventListener('DOMContentLoaded',cleanupThemeChrome,{once:true});
else cleanupThemeChrome();
window.addEventListener('load',cleanupThemeChrome,{once:true});
setTimeout(cleanupThemeChrome,250);
})();

(function(){
'use strict';

function initWPSTSiteHeader(header){
if(!header || header.dataset.wpstHeaderRuntimeReady==='1')return;
header.dataset.wpstHeaderRuntimeReady='1';

const announcement=header.querySelector('[data-wpst-announcement]');
if(announcement){
  const close=announcement.querySelector('[data-wpst-announcement-close]');
  const storageKey='wpst-announcement-dismissed:'+location.hostname;
  try{if(sessionStorage.getItem(storageKey)==='1')announcement.style.display='none'}catch(e){}
  if(close)close.addEventListener('click',function(){
    announcement.style.display='none';
    try{sessionStorage.setItem(storageKey,'1')}catch(e){}
    window.dispatchEvent(new Event('resize'));
  });
}

const scrollThreshold=parseInt(header.getAttribute('data-wpst-scroll-threshold')||'60',10);
const stickyEnabled=(header.getAttribute('data-wpst-sticky')||'0')==='1';
const stickyMode=header.getAttribute('data-wpst-sticky-mode')||'always';
let stickyPlaceholder=null;

if(stickyEnabled && stickyMode==='scroll'){
  stickyPlaceholder=document.createElement('div');
  stickyPlaceholder.className='wpst-sticky-placeholder';
  stickyPlaceholder.setAttribute('aria-hidden','true');
  header.parentNode.insertBefore(stickyPlaceholder,header);
}

function scrollSync(){
  const scrolled=window.scrollY>scrollThreshold;
  header.classList.toggle('is-wpst-scrolled',scrolled);

  if(stickyEnabled && stickyMode==='scroll'){
    header.classList.toggle('is-wpst-sticky-active',scrolled);
    if(stickyPlaceholder){
      stickyPlaceholder.style.height=scrolled?header.offsetHeight+'px':'0px';
    }
  }
}
scrollSync();
window.addEventListener('scroll',scrollSync,{passive:true});
window.addEventListener('resize',scrollSync,{passive:true});

/* Header Builder 2.0 uses row containers instead of the legacy .wpst-quick-inner. */
const inner=header.querySelector('.wpst-header-row-main .wpst-header-row-inner') || header.querySelector('.wpst-quick-inner');
const desktopNav=header.querySelector('.wpst-q-nav');

/*
 * WPSoft Navigation widget owns its mobile hamburger/drawer.
 * Header Builder's legacy mobile composition must never run at the same time,
 * otherwise two toggle/close controls are rendered before the first scroll.
 */
const wpstNavigationWidget=header.querySelector('.elementor-widget-wpsoft-navigation [data-wpst-nav], [data-wpst-nav]');
if(wpstNavigationWidget){
  header.classList.add('has-wpst-navigation-widget');
  header.querySelectorAll('.wpst-mobile-drawer,.wpst-mobile-toggle,.wpst-mobile-head,.wpst-mobile-overlay').forEach(el=>el.remove());
  return;
}

/*
 * Hybrid Header source isolation.
 * An Elementor-owned mobile header must never inherit/clone Live Builder's
 * .wpst-q-nav, CTA, logo or drawer data. Elementor controls its own mobile
 * navigation (WPSoft Navigation widget or any other Elementor nav widget).
 */
const renderSource=(header.getAttribute('data-wpst-render-source')||'').toLowerCase();
if(renderSource==='elementor'){
  header.classList.add('is-wpst-elementor-source');
  header.querySelectorAll('.wpst-mobile-drawer,.wpst-mobile-toggle,.wpst-mobile-head,.wpst-mobile-overlay').forEach(el=>el.remove());
  return;
}

if(!inner||!desktopNav)return;

/* Keep the logo visible on mobile regardless of Left / Center / Right placement. */
const headerLogo=header.querySelector('.wpst-q-logo');
if(headerLogo){
  const logoZone=headerLogo.closest('.wpst-header-zone');
  if(logoZone) logoZone.classList.add('wpst-mobile-logo-zone');
}

const desktopButton=header.querySelector('.wpst-q-button');
if(desktopButton){
  const buttonText=(desktopButton.textContent||'').trim().toLocaleLowerCase('tr-TR');
  desktopNav.querySelectorAll('a').forEach(a=>{
    if((a.textContent||'').trim().toLocaleLowerCase('tr-TR')===buttonText){
      const li=a.closest('li');
      if(li)li.remove();else a.remove();
    }
  });
}

/* Mobile Header Builder 2.0: independent mobile composition. */
header.querySelectorAll('.wpst-mobile-drawer,.wpst-mobile-toggle,.wpst-mobile-head').forEach(el=>el.remove());

const mobilePreset=header.getAttribute('data-wpst-mobile-preset')||'classic';
const mobileLogoPosition=header.getAttribute('data-wpst-mobile-logo-position')||(mobilePreset==='centered'?'center':'left');
const mobileHead=document.createElement('div');
mobileHead.className='wpst-mobile-head wpst-mobile-preset-'+mobilePreset+' wpst-mobile-logo-'+mobileLogoPosition;
mobileHead.setAttribute('data-wpst-mobile-head','1');

const mobileBrand=document.createElement('div');
mobileBrand.className='wpst-mobile-brand';
if(headerLogo){
  const clone=headerLogo.cloneNode(true);
  clone.classList.add('wpst-mobile-logo-clone');
  mobileBrand.appendChild(clone);
}else{
  const siteLink=document.createElement('a');
  siteLink.href='/';siteLink.textContent=document.title||'Site';siteLink.className='wpst-mobile-site-title';
  mobileBrand.appendChild(siteLink);
}

const mobileActions=document.createElement('div');
mobileActions.className='wpst-mobile-head-actions';
function mobileAction(tag,cls,label,html){
  const el=document.createElement(tag);el.className='wpst-mobile-head-action '+cls;el.setAttribute('aria-label',label);el.innerHTML=html;return el;
}
const iconSearch='<svg viewBox="0 0 24 24" focusable="false"><path d="m20.5 19.1-4.2-4.2a7.2 7.2 0 1 0-1.4 1.4l4.2 4.2 1.4-1.4ZM5.8 10.6a4.8 4.8 0 1 1 9.6 0 4.8 4.8 0 0 1-9.6 0Z"/></svg>';
const iconAccount='<svg viewBox="0 0 24 24" focusable="false"><path d="M12 12a4.5 4.5 0 1 0 0-9 4.5 4.5 0 0 0 0 9Zm0 2c-4.4 0-8 2.3-8 5.2V21h16v-1.8c0-2.9-3.6-5.2-8-5.2Z"/></svg>';
const iconCart='<svg viewBox="0 0 24 24" focusable="false"><path d="M7 4H3v2h2l2.2 9.2A2 2 0 0 0 9.1 17H18a2 2 0 0 0 1.9-1.4L22 8H8.1L7.6 6H7V4Zm2.2 6h10.1l-1.4 5H10.4l-1.2-5ZM10 19a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Z"/></svg>';
const wantsSearch=(header.getAttribute('data-wpst-mobile-search')||'0')==='1';
const wantsAccount=(header.getAttribute('data-wpst-mobile-account')||'0')==='1'||mobilePreset==='commerce';
const wantsCart=(header.getAttribute('data-wpst-mobile-cart')||'0')==='1'||mobilePreset==='commerce';
const wantsCta=(header.getAttribute('data-wpst-mobile-cta')||'0')==='1'||mobilePreset==='cta';
if(wantsSearch){const b=mobileAction('button','is-search','Ara',iconSearch);b.type='button';b.setAttribute('data-wpst-search-toggle','');mobileActions.appendChild(b);}
if(wantsAccount){const a=mobileAction('a','is-account','Hesap',iconAccount);a.href=header.getAttribute('data-wpst-account-url')||'/wp-login.php';mobileActions.appendChild(a);}
if(wantsCart){const a=mobileAction('a','is-cart','Sepet',iconCart);a.href=header.getAttribute('data-wpst-cart-url')||'/cart/';mobileActions.appendChild(a);}
if(wantsCta){const a=document.createElement('a');a.className='wpst-mobile-head-cta';a.href=header.getAttribute('data-wpst-mobile-cta-url')||'#iletisim';a.textContent=header.getAttribute('data-wpst-mobile-cta-text')||'Teklif Al';mobileActions.appendChild(a);}

const toggle=document.createElement('button');
toggle.type='button';
toggle.className='wpst-mobile-toggle';
toggle.setAttribute('aria-label','Mobil menüyü aç');
toggle.setAttribute('aria-expanded','false');
toggle.setAttribute('aria-controls','wpst-mobile-drawer');
toggle.innerHTML='<span></span><span></span><span></span>';
mobileActions.appendChild(toggle);

mobileHead.appendChild(mobileBrand);
mobileHead.appendChild(mobileActions);
inner.insertBefore(mobileHead,inner.firstChild);

const drawer=document.createElement('div');
drawer.id='wpst-mobile-drawer';
drawer.className='wpst-mobile-drawer wpst-mobile-drawer-style-'+(header.getAttribute('data-wpst-mobile-drawer-style')||'clean');
drawer.setAttribute('aria-hidden','true');
drawer.dataset.side=header.getAttribute('data-wpst-drawer-side')||'right';
const drawerOriginParent=drawer.parentNode;
const drawerOriginNext=drawer.nextSibling;

const drawerHead=document.createElement('div');
drawerHead.className='wpst-mobile-drawer-head';
const drawerTitle=document.createElement('strong');
drawerTitle.textContent=desktopNav.getAttribute('aria-label')||'Menü';
const drawerClose=document.createElement('button');
drawerClose.type='button';
drawerClose.className='wpst-mobile-close';
drawerClose.setAttribute('aria-label',header.getAttribute('data-wpst-close-text')||'Menüyü Kapat');
drawerClose.innerHTML='<span aria-hidden="true"><svg viewBox="0 0 24 24" focusable="false"><path d="M6.7 5.3 12 10.6l5.3-5.3 1.4 1.4L13.4 12l5.3 5.3-1.4 1.4L12 13.4l-5.3 5.3-1.4-1.4 5.3-5.3-5.3-5.3 1.4-1.4Z"/></svg></span>';
if((header.getAttribute('data-wpst-mobile-drawer-logo')||'1')==='1' && headerLogo){
  const drawerBrand=document.createElement('div');
  drawerBrand.className='wpst-mobile-drawer-brand';
  const brandClone=headerLogo.cloneNode(true);
  brandClone.classList.add('wpst-mobile-drawer-logo');
  drawerBrand.appendChild(brandClone);
  drawerHead.appendChild(drawerBrand);
}else{drawerHead.appendChild(drawerTitle);}
drawerHead.appendChild(drawerClose);
drawer.appendChild(drawerHead);

const overlay=document.createElement('div');
overlay.className='wpst-mobile-overlay';
overlay.setAttribute('aria-hidden','true');

const mobileMenu=document.createElement('nav');
mobileMenu.className='wpst-mobile-nav';
mobileMenu.setAttribute('aria-label','Mobil site menüsü');
const desktopMenu=desktopNav.querySelector('ul');
if(desktopMenu){
  const clonedMenu=desktopMenu.cloneNode(true);
  clonedMenu.classList.add('wpst-mobile-menu');

  // Mobile navigation is intentionally independent from desktop Mega Menu.
  // Remove Elementor mega panels, mega promo content and desktop-only UI decorations.
  clonedMenu.querySelectorAll('.wpst-elementor-mega-panel,.wpst-mega-promo,.wpst-mega-mobile-toggle').forEach(el=>el.remove());

  clonedMenu.querySelectorAll('.wpst-menu-item-icon,.wpst-menu-item-badge,.wpst-menu-item-description').forEach(el=>el.remove());

  clonedMenu.querySelectorAll('[data-wpst-mega],[data-wpst-panel-bg],[data-wpst-panel-radius],[data-wpst-promo-title],[data-wpst-promo-text],[data-wpst-promo-button],[data-wpst-promo-url]').forEach(el=>{
    ['data-wpst-mega','data-wpst-panel-bg','data-wpst-panel-radius','data-wpst-promo-title','data-wpst-promo-text','data-wpst-promo-button','data-wpst-promo-url','aria-haspopup','aria-expanded','aria-controls'].forEach(attr=>el.removeAttribute(attr));
  });

  clonedMenu.querySelectorAll('li').forEach(li=>{
    Array.from(li.classList).forEach(cls=>{
      if(cls.indexOf('wpst-mega-')===0 || cls==='is-wpst-mega-open') li.classList.remove(cls);
    });
  });

  // Column-title items become normal mobile navigation items.
  clonedMenu.querySelectorAll('.wpst-mega-column-title').forEach(li=>li.classList.remove('wpst-mega-column-title'));

  clonedMenu.querySelectorAll('li.menu-item-has-children,li.page_item_has_children').forEach((li,index)=>{
    const directLink=Array.from(li.children).find(el=>el.tagName==='A');
    const sub=Array.from(li.children).find(el=>el.tagName==='UL');
    if(!sub)return;
    const id='wpst-mobile-submenu-'+index;
    sub.id=id;
    sub.setAttribute('aria-hidden','true');
    const subToggle=document.createElement('button');
    subToggle.type='button';
    subToggle.className='wpst-mobile-submenu-toggle';
    subToggle.setAttribute('aria-expanded','false');
    subToggle.setAttribute('aria-controls',id);
    subToggle.setAttribute('aria-label',(directLink?.textContent||'Alt menü')+' alt menüsünü aç');
    subToggle.innerHTML='<span aria-hidden="true"><svg viewBox="0 0 24 24" focusable="false"><path d="m7.4 9.4 4.6 4.6 4.6-4.6L18 10.8l-6 6-6-6 1.4-1.4Z"/></svg></span>';
    if(directLink) directLink.insertAdjacentElement('afterend',subToggle);
    else li.insertBefore(subToggle,sub);
    subToggle.addEventListener('click',e=>{
      e.preventDefault();e.stopPropagation();
      const open=li.classList.toggle('is-submenu-open');
      subToggle.setAttribute('aria-expanded',open?'true':'false');
      sub.setAttribute('aria-hidden',open?'false':'true');
    });
  });

  mobileMenu.appendChild(clonedMenu);
}

drawer.appendChild(mobileMenu);

if(desktopButton){
  const mobileCta=desktopButton.cloneNode(true);
  mobileCta.classList.remove('wpst-q-button');
  mobileCta.classList.add('wpst-mobile-nav-cta');
  if(!mobileCta.querySelector('.wpst-mobile-cta-icon')){
    const ctaIcon=document.createElement('span');
    ctaIcon.className='wpst-mobile-cta-icon';
    ctaIcon.setAttribute('aria-hidden','true');
    ctaIcon.innerHTML='<svg viewBox="0 0 24 24" focusable="false"><path d="M5 11h10.2l-3.6-3.6L13 6l6 6-6 6-1.4-1.4 3.6-3.6H5v-2Z"/></svg>';
    mobileCta.appendChild(ctaIcon);
  }
  drawer.appendChild(mobileCta);
}
if((header.getAttribute('data-wpst-mobile-contact')||'0')==='1'){
  const contact=document.createElement('div');
  contact.className='wpst-mobile-contact';
  const title=header.getAttribute('data-wpst-mobile-contact-title')||'Hızlı İletişim';
  const phone=header.getAttribute('data-wpst-mobile-phone')||'';
  const email=header.getAttribute('data-wpst-mobile-email')||'';
  let links='';
  if(phone)links+='<a href="tel:'+phone.replace(/[^+0-9]/g,'')+'"><span>☎</span><b>'+phone+'</b></a>';
  if(email)links+='<a href="mailto:'+email+'"><span>✉</span><b>'+email+'</b></a>';
  if(links)contact.innerHTML='<strong>'+title+'</strong><div class="wpst-mobile-contact-links">'+links+'</div>';
  if(links)drawer.appendChild(contact);
}
if((header.getAttribute('data-wpst-mobile-social')||'0')==='1'){
  const socials=document.createElement('div');
  socials.className='wpst-mobile-socials';
  [['instagram',header.getAttribute('data-wpst-mobile-instagram'),'Instagram','IG'],['facebook',header.getAttribute('data-wpst-mobile-facebook'),'Facebook','f'],['youtube',header.getAttribute('data-wpst-mobile-youtube'),'YouTube','▶'],['linkedin',header.getAttribute('data-wpst-mobile-linkedin'),'LinkedIn','in']].forEach(item=>{
    if(!item[1])return; const a=document.createElement('a');a.href=item[1];a.target='_blank';a.rel='noopener noreferrer';a.className='wpst-mobile-social is-'+item[0];a.setAttribute('aria-label',item[2]);a.textContent=item[3];socials.appendChild(a);
  });
  if(socials.children.length)drawer.appendChild(socials);
}
header.appendChild(drawer);
if((header.getAttribute('data-wpst-overlay')||'0')==='1') document.body.appendChild(overlay);

const breakpoint=parseInt(header.getAttribute('data-wpst-breakpoint')||'768',10);
let lastFocusedBeforeMenu=null;

function drawerFocusable(){
  return Array.from(drawer.querySelectorAll('a[href],button:not([disabled]),[tabindex]:not([tabindex="-1"])'))
    .filter(el=>el.offsetParent!==null);
}

function closeMenu(){
  toggle.classList.remove('is-active');
  toggle.setAttribute('aria-expanded','false');
  drawer.classList.remove('is-open');
  drawer.setAttribute('aria-hidden','true');
  document.documentElement.classList.remove('wpst-menu-open');
  overlay.classList.remove('is-open');
  overlay.setAttribute('aria-hidden','true');
  setTimeout(function(){
    if(drawerOriginParent && drawer.parentNode===document.body){
      if(drawerOriginNext && drawerOriginNext.parentNode===drawerOriginParent){
        drawerOriginParent.insertBefore(drawer,drawerOriginNext);
      }else{
        drawerOriginParent.appendChild(drawer);
      }
    }
  },300);
}
function openMenu(){
  lastFocusedBeforeMenu=document.activeElement;
  if(drawer.parentNode!==document.body) document.body.appendChild(drawer);
  toggle.classList.add('is-active');
  toggle.setAttribute('aria-expanded','true');
  drawer.classList.add('is-open');
  drawer.setAttribute('aria-hidden','false');
  document.documentElement.classList.add('wpst-menu-open');
  overlay.classList.add('is-open');
  overlay.setAttribute('aria-hidden','false');
  setTimeout(()=>drawerClose.focus(),30);
}
function responsiveSync(){
  const width=window.innerWidth||document.documentElement.clientWidth||1200;
  const mobile=width<=breakpoint;

  header.classList.toggle('is-wpst-mobile',mobile);
  header.classList.remove('wpst-device-desktop','wpst-device-tablet','wpst-device-mobile');
  if(mobile) header.classList.add('wpst-device-mobile');
  else if(width<=1024) header.classList.add('wpst-device-tablet');
  else header.classList.add('wpst-device-desktop');

  if(!mobile)closeMenu();
}
responsiveSync();
let wpstHeaderResizeRaf=0;
window.addEventListener('resize',function(){
  cancelAnimationFrame(wpstHeaderResizeRaf);
  wpstHeaderResizeRaf=requestAnimationFrame(responsiveSync);
},{passive:true});

toggle.addEventListener('click',function(e){
  e.preventDefault();e.stopPropagation();
  drawer.classList.contains('is-open')?closeMenu():openMenu();
});
drawerClose.addEventListener('click',function(e){e.preventDefault();closeMenu();toggle.focus();});
overlay.addEventListener('click',closeMenu);
document.addEventListener('click',e=>{
  if(!header.contains(e.target) && !drawer.contains(e.target))closeMenu();
});
drawer.addEventListener('click',e=>{
  const link=e.target.closest('a');
  if(!link)return;
  // A parent with an opened submenu can still navigate if it has a real URL.
  closeMenu();
});
document.addEventListener('keydown',e=>{
  if(e.key==='Escape' && drawer.classList.contains('is-open')){
    e.preventDefault();
    closeMenu();
    (lastFocusedBeforeMenu||toggle).focus();
    return;
  }
  if(e.key==='Tab' && drawer.classList.contains('is-open')){
    const focusable=drawerFocusable();
    if(!focusable.length)return;
    const first=focusable[0],last=focusable[focusable.length-1];
    if(e.shiftKey && document.activeElement===first){
      e.preventDefault();last.focus();
    }else if(!e.shiftKey && document.activeElement===last){
      e.preventDefault();first.focus();
    }
  }
});
}

function bootWPSTSiteHeaders(){
  document.querySelectorAll('.wpsoft-site-header').forEach(initWPSTSiteHeader);
}
if(document.readyState==='loading'){
  document.addEventListener('DOMContentLoaded',bootWPSTSiteHeaders,{once:true});
}else{
  bootWPSTSiteHeaders();
}
window.addEventListener('load',bootWPSTSiteHeaders,{once:true});
})();

function initFooterReveal(){
 const footers=document.querySelectorAll('.wpsoft-site-footer[data-wpst-footer-reveal="1"]');
 if(!footers.length)return;
 if(!('IntersectionObserver' in window)){footers.forEach(f=>f.classList.add('is-wpst-footer-visible'));return;}
 const io=new IntersectionObserver(entries=>entries.forEach(entry=>{
   if(entry.isIntersecting){entry.target.classList.add('is-wpst-footer-visible');io.unobserve(entry.target);}
 }),{rootMargin:'0px 0px -5% 0px',threshold:.08});
 footers.forEach(f=>io.observe(f));
}
if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',initFooterReveal);else initFooterReveal();

/* v3.1.28 Header Builder v3 */
document.addEventListener('DOMContentLoaded',function(){
  document.querySelectorAll('.wpsoft-site-header').forEach(function(header){
    let lastY=window.scrollY||0;
    const hideOnScroll=header.getAttribute('data-wpst-hide-scroll')==='1';
    const delta=parseInt(header.getAttribute('data-wpst-hide-delta')||'12',10);
    if(hideOnScroll){
      window.addEventListener('scroll',function(){
        const y=window.scrollY||0;
        if(Math.abs(y-lastY)<delta)return;
        if(y>lastY && y>100 && !document.documentElement.classList.contains('wpst-menu-open')){
          header.classList.add('is-wpst-hidden-scroll');
        }else{
          header.classList.remove('is-wpst-hidden-scroll');
        }
        lastY=y;
      },{passive:true});
    }

    const popup=header.querySelector('.wpst-search-popup');
    if(popup){
      const openers=document.querySelectorAll('[data-wpst-search-toggle]');
      const close=popup.querySelector('.wpst-search-close');
      function openSearch(){
        popup.classList.add('is-open');popup.setAttribute('aria-hidden','false');
        const input=popup.querySelector('input[type=search]');if(input)setTimeout(()=>input.focus(),30);
      }
      function closeSearch(){popup.classList.remove('is-open');popup.setAttribute('aria-hidden','true');}
      openers.forEach(x=>x.addEventListener('click',function(e){e.preventDefault();openSearch()}));
      if(close)close.addEventListener('click',closeSearch);
      popup.addEventListener('click',e=>{if(e.target===popup)closeSearch()});
      document.addEventListener('keydown',e=>{if(e.key==='Escape')closeSearch()});
    }
  });
});

/* v3.3.18 Gallery Zoom 2.0 · Real Gallery Lightbox */
(function(){
  let box=null,img=null,caption=null,counter=null,prevBtn=null,nextBtn=null,closeBtn=null;
  let items=[],index=0,lastFocus=null,activeGallery=null;
  let touchStartX=0,touchStartY=0,touchActive=false;

  function ensureBox(){
    if(box&&document.body.contains(box))return box;
    box=document.querySelector('.wpst-gallery-lightbox');
    if(!box){
      box=document.createElement('div');
      box.className='wpst-gallery-lightbox';
      box.setAttribute('aria-hidden','true');
      box.setAttribute('role','dialog');
      box.setAttribute('aria-modal','true');
      box.setAttribute('aria-label','Galeri görüntüleyici');
      box.innerHTML=
        '<div class="wpst-gallery-lightbox-inner">'+
          '<div class="wpst-gallery-lightbox-toolbar">'+
            '<div class="wpst-gallery-lightbox-counter" aria-live="polite"></div>'+
            '<button class="wpst-gallery-lightbox-close" type="button" aria-label="Galeriyi kapat">×</button>'+
          '</div>'+
          '<button class="wpst-gallery-lightbox-prev" type="button" aria-label="Önceki fotoğraf">‹</button>'+
          '<div class="wpst-gallery-lightbox-stage">'+
            '<img alt="" draggable="false">'+
          '</div>'+
          '<button class="wpst-gallery-lightbox-next" type="button" aria-label="Sonraki fotoğraf">›</button>'+
          '<div class="wpst-gallery-lightbox-caption"></div>'+
          '<div class="wpst-gallery-lightbox-hint" aria-hidden="true"><span>←</span> Fotoğraflar arasında gezin <span>→</span></div>'+
        '</div>';
      document.body.appendChild(box);
    }

    img=box.querySelector('.wpst-gallery-lightbox-stage img');
    caption=box.querySelector('.wpst-gallery-lightbox-caption');
    counter=box.querySelector('.wpst-gallery-lightbox-counter');
    prevBtn=box.querySelector('.wpst-gallery-lightbox-prev');
    nextBtn=box.querySelector('.wpst-gallery-lightbox-next');
    closeBtn=box.querySelector('.wpst-gallery-lightbox-close');

    if(box.dataset.wpstGalleryBound!=='1'){
      box.dataset.wpstGalleryBound='1';

      closeBtn.addEventListener('click',close);
      prevBtn.addEventListener('click',function(e){e.stopPropagation();show(index-1,-1);});
      nextBtn.addEventListener('click',function(e){e.stopPropagation();show(index+1,1);});

      /* Clicking the image itself continues to the next image. */
      img.addEventListener('click',function(e){
        e.stopPropagation();
        if(items.length>1)show(index+1,1);
      });

      box.addEventListener('click',function(e){
        if(e.target===box)close();
      });

      const stage=box.querySelector('.wpst-gallery-lightbox-stage');
      stage.addEventListener('touchstart',function(e){
        if(!e.touches||e.touches.length!==1)return;
        touchStartX=e.touches[0].clientX;
        touchStartY=e.touches[0].clientY;
        touchActive=true;
      },{passive:true});

      stage.addEventListener('touchend',function(e){
        if(!touchActive||!e.changedTouches||!e.changedTouches.length)return;
        touchActive=false;
        const dx=e.changedTouches[0].clientX-touchStartX;
        const dy=e.changedTouches[0].clientY-touchStartY;
        if(Math.abs(dx)<45||Math.abs(dx)<Math.abs(dy)*1.15)return;
        if(dx<0)show(index+1,1);
        else show(index-1,-1);
      },{passive:true});
    }
    return box;
  }

  function preload(i){
    if(!items.length)return;
    const n=(i+items.length)%items.length;
    const url=items[n].getAttribute('data-full')||'';
    if(url){const p=new Image();p.src=url;}
  }

  function show(i,direction){
    if(!items.length)return;
    index=(i+items.length)%items.length;
    const item=items[index];
    const url=item.getAttribute('data-full')||'';
    const alt=item.getAttribute('data-alt')||'';
    const cap=item.getAttribute('data-caption')||'';

    if(direction){
      const stage=box.querySelector('.wpst-gallery-lightbox-stage');
      stage.classList.remove('is-next','is-prev');
      void stage.offsetWidth;
      stage.classList.add(direction>0?'is-next':'is-prev');
    }

    img.src=url;
    img.alt=alt;
    caption.textContent=cap;
    caption.style.display=cap?'block':'none';
    counter.textContent=(index+1)+' / '+items.length;

    const multi=items.length>1;
    prevBtn.hidden=!multi;
    nextBtn.hidden=!multi;
    box.classList.toggle('has-multiple',multi);

    preload(index+1);
    preload(index-1);
  }

  function open(gallery,item){
    ensureBox();
    activeGallery=gallery;
    items=[...gallery.querySelectorAll('[data-wpst-gallery-open]')];
    if(!items.length)return;

    index=Math.max(0,items.indexOf(item));
    lastFocus=document.activeElement;

    box.setAttribute('data-style',gallery.getAttribute('data-lightbox-style')||'dark');
    show(index,0);
    box.classList.add('is-open');
    box.setAttribute('aria-hidden','false');
    document.documentElement.classList.add('wpst-lightbox-open');
    document.body.classList.add('wpst-gallery-open');

    requestAnimationFrame(function(){closeBtn.focus();});
  }

  function close(){
    if(!box)return;
    box.classList.remove('is-open');
    box.setAttribute('aria-hidden','true');
    document.documentElement.classList.remove('wpst-lightbox-open');
    document.body.classList.remove('wpst-gallery-open');
    if(img)img.removeAttribute('src');
    items=[];
    activeGallery=null;
    if(lastFocus&&lastFocus.focus)lastFocus.focus();
    lastFocus=null;
  }

  /* Delegation also works for Elementor widgets injected after DOMContentLoaded. */
  document.addEventListener('click',function(e){
    const item=e.target.closest('[data-wpst-gallery-open]');
    if(!item)return;
    const gallery=item.closest('[data-wpst-gallery]');
    if(!gallery)return;
    e.preventDefault();
    open(gallery,item);
  });

  document.addEventListener('keydown',function(e){
    if(!box||!box.classList.contains('is-open'))return;
    if(e.key==='Escape'){e.preventDefault();close();return;}
    if(e.key==='ArrowLeft'){e.preventDefault();show(index-1,-1);return;}
    if(e.key==='ArrowRight'){e.preventDefault();show(index+1,1);return;}
    if(e.key==='Home'){e.preventDefault();show(0,-1);return;}
    if(e.key==='End'){e.preventDefault();show(items.length-1,1);return;}
    if(e.key==='Tab'){
      const focusable=[closeBtn,prevBtn,nextBtn].filter(function(el){return el&&!el.hidden&&!el.disabled;});
      if(!focusable.length)return;
      const first=focusable[0],last=focusable[focusable.length-1];
      if(e.shiftKey&&document.activeElement===first){e.preventDefault();last.focus();}
      else if(!e.shiftKey&&document.activeElement===last){e.preventDefault();first.focus();}
    }
  });
})();

/* Header device state is synchronized by the main mobile Header controller. */

// Footer Builder 2.1 mobile accordion
(function(){
 const footers=document.querySelectorAll('.wpsoft-site-footer[data-wpst-mobile-accordion="1"]');
 if(!footers.length)return;
 const mq=window.matchMedia('(max-width:767px)');
 const setup=(footer)=>{
  const labels={left:footer.dataset.wpstMobileTitleLeft||'Kurumsal',center:footer.dataset.wpstMobileTitleCenter||'Bağlantılar',right:footer.dataset.wpstMobileTitleRight||'İletişim'};
  footer.querySelectorAll('.wpst-footer-zone').forEach((zone,index)=>{
   if(!zone.querySelector('.wpst-footer-accordion-toggle')){
    const key=zone.classList.contains('wpst-footer-zone-center')?'center':zone.classList.contains('wpst-footer-zone-right')?'right':'left';
    const b=document.createElement('button'); b.type='button'; b.className='wpst-footer-accordion-toggle'; b.textContent=labels[key]; b.setAttribute('aria-expanded','false'); zone.prepend(b);
    b.addEventListener('click',()=>{const open=zone.classList.toggle('is-open');b.setAttribute('aria-expanded',open?'true':'false')});
   }
  });
 };
 footers.forEach(setup);
})();


/* v3.1.77 Blog reading progress */
(function(){
 const bars=[...document.querySelectorAll('[data-wpst-reading-progress]')];
 if(!bars.length)return;
 let raf=0;
 const update=()=>{
  raf=0;
  const doc=document.documentElement;
  const max=Math.max(1,doc.scrollHeight-window.innerHeight);
  const value=Math.max(0,Math.min(100,(window.scrollY/max)*100));
  bars.forEach(el=>{
   const bar=el.querySelector('.wpst-reading-progress-bar');
   const pct=el.querySelector('.wpst-reading-progress-percent');
   if(bar)bar.style.width=value.toFixed(2)+'%';
   if(pct)pct.textContent=Math.round(value)+'%';
   el.setAttribute('aria-valuenow',String(Math.round(value)));
  });
 };
 const request=()=>{if(!raf)raf=requestAnimationFrame(update)};
 window.addEventListener('scroll',request,{passive:true});window.addEventListener('resize',request,{passive:true});update();
})();


/* v3.2.44 Gallery & Media carousel controls */
document.addEventListener('DOMContentLoaded',function(){
  function enhanceMediaCarousel(g,prevSelector,nextSelector){
    if(g.dataset.wpstMediaCarouselReady==='1')return;
    g.dataset.wpstMediaCarouselReady='1';
    const step=()=>Math.max(260,g.clientWidth*.76);
    const prev=g.querySelector(prevSelector),next=g.querySelector(nextSelector);
    const move=dir=>g.scrollBy({left:dir*step(),behavior:window.matchMedia('(prefers-reduced-motion: reduce)').matches?'auto':'smooth'});
    if(prev)prev.addEventListener('click',()=>move(-1));
    if(next)next.addEventListener('click',()=>move(1));
    if(!g.hasAttribute('tabindex'))g.tabIndex=0;
    g.setAttribute('role','region');
    g.setAttribute('aria-roledescription','carousel');
    g.addEventListener('keydown',function(e){
      if(e.key==='ArrowLeft'){e.preventDefault();move(-1);}
      if(e.key==='ArrowRight'){e.preventDefault();move(1);}
      if(e.key==='Home'){e.preventDefault();g.scrollTo({left:0,behavior:'smooth'});}
      if(e.key==='End'){e.preventDefault();g.scrollTo({left:g.scrollWidth,behavior:'smooth'});}
    });
  }
  document.querySelectorAll('.wpst-gallery-zoom-pro.is-carousel').forEach(g=>enhanceMediaCarousel(g,'[data-gallery-prev]','[data-gallery-next]'));
  document.querySelectorAll('.wpst-video-gallery-pro.is-carousel').forEach(g=>enhanceMediaCarousel(g,'[data-video-prev]','[data-video-next]'));

  document.querySelectorAll('.wpst-video-gallery-pro img[data-wpst-video-fallback]').forEach(function(img){
    if(img.dataset.wpstVideoFallbackReady==='1')return;
    img.dataset.wpstVideoFallbackReady='1';
    img.addEventListener('error',function(){
      const fallback=this.getAttribute('data-wpst-video-fallback');
      if(fallback && this.src!==fallback){
        this.removeAttribute('data-wpst-video-fallback');
        this.src=fallback;
      }
    },{once:true});
  });

  const triggers=[...document.querySelectorAll('[data-wpst-video-open]')];
  if(!triggers.length)return;
  let box=document.querySelector('.wpst-video-lightbox');
  if(!box){
    box=document.createElement('div');
    box.className='wpst-video-lightbox';
    box.setAttribute('aria-hidden','true');
    box.setAttribute('role','dialog');
    box.setAttribute('aria-modal','true');
    box.setAttribute('aria-label','Video görüntüleyici');
    box.innerHTML='<div class="wpst-video-lightbox-inner"><button class="wpst-video-lightbox-close" type="button" aria-label="Kapat">×</button><iframe allow="autoplay; fullscreen; picture-in-picture" allowfullscreen title="Video"></iframe></div>';
    document.body.appendChild(box);
  }
  const frame=box.querySelector('iframe'),closeBtn=box.querySelector('.wpst-video-lightbox-close');
  let lastFocus=null;
  const close=()=>{box.classList.remove('is-open');box.setAttribute('aria-hidden','true');frame.src='about:blank';document.documentElement.classList.remove('wpst-lightbox-open');if(lastFocus&&lastFocus.focus)lastFocus.focus();};
  document.addEventListener('click',function(e){
    const btn=e.target.closest('[data-wpst-video-open]');
    if(!btn)return;
    const video=btn.getAttribute('data-video')||'';
    if(!video)return;
    e.preventDefault();lastFocus=btn;frame.src=video;
    box.classList.add('is-open');box.setAttribute('aria-hidden','false');document.documentElement.classList.add('wpst-lightbox-open');
    requestAnimationFrame(function(){closeBtn.focus();});
  });
  closeBtn.addEventListener('click',close);
  box.addEventListener('click',e=>{if(e.target===box)close()});
  document.addEventListener('keydown',e=>{
    if(!box.classList.contains('is-open'))return;
    if(e.key==='Escape'){e.preventDefault();close();return;}
    if(e.key==='Tab'){
      const focusable=[closeBtn,frame];
      const first=focusable[0],last=focusable[focusable.length-1];
      if(e.shiftKey&&document.activeElement===first){e.preventDefault();last.focus();}
      else if(!e.shiftKey&&document.activeElement===last){e.preventDefault();first.focus();}
    }
  });
});


/* v3.2.44 Unified Media Framework interactions */
document.addEventListener('DOMContentLoaded',function(){
  document.querySelectorAll('[data-wpst-before-after]').forEach(function(el){
    const range=el.querySelector('input[type="range"]');
    if(!range)return;
    const sync=()=>el.style.setProperty('--wpst-ba-pos',range.value+'%');
    range.addEventListener('input',sync);sync();
  });

  document.querySelectorAll('.wpst-ew-image-hotspots[data-hotspot-mode="click"]').forEach(function(wrap){
    wrap.addEventListener('click',function(e){
      const btn=e.target.closest('.wpst-hotspot');
      if(!btn)return;
      if(e.target.closest('a'))return;
      e.preventDefault();
      const open=btn.getAttribute('aria-expanded')==='true';
      wrap.querySelectorAll('.wpst-hotspot[aria-expanded="true"]').forEach(x=>x.setAttribute('aria-expanded','false'));
      btn.setAttribute('aria-expanded',open?'false':'true');
    });
  });
  document.addEventListener('click',function(e){
    if(e.target.closest('.wpst-ew-image-hotspots[data-hotspot-mode="click"]'))return;
    document.querySelectorAll('.wpst-ew-image-hotspots[data-hotspot-mode="click"] .wpst-hotspot[aria-expanded="true"]').forEach(x=>x.setAttribute('aria-expanded','false'));
  });

  if(window.matchMedia&&window.matchMedia('(max-width:767px)').matches){
    document.querySelectorAll('.wpst-ew-video-hero[data-mobile-video="0"]').forEach(function(hero){
      const video=hero.querySelector('video');
      if(!video)return;
      const poster=video.getAttribute('poster');
      if(poster)hero.style.backgroundImage='url("'+poster.replace(/"/g,'\\"')+'")';
      try{video.pause();}catch(e){}
    });
  }
});


/* v3.2.44 — WPSoft Motion Framework 3.0 */
(function(){
  const reduce=window.matchMedia&&window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const entrySelector=[
    '.wpst-entry-fade','.wpst-entry-fade-up','.wpst-entry-fade-down',
    '.wpst-entry-fade-left','.wpst-entry-fade-right','.wpst-entry-scale',
    '.wpst-entry-zoom','.wpst-entry-blur','.wpst-entry-rotate-soft','.wpst-entry-clip-up'
  ].join(',');

  function thresholdFor(el){
    const raw=getComputedStyle(el).getPropertyValue('--wpst-motion-threshold').trim();
    const val=parseFloat(raw);
    return Number.isFinite(val)?Math.min(.8,Math.max(0,val/100)):.15;
  }

  function initEntries(scope){
    const nodes=[...(scope||document).querySelectorAll(entrySelector)].filter(el=>!el.classList.contains('wpst-entry-none'));
    nodes.forEach(function(el){
      if(el.dataset.wpstMotionInit==='1')return;
      el.dataset.wpstMotionInit='1';
      if(reduce||el.classList.contains('wpst-motion-none')){
        el.classList.add('wpst-motion-visible');
        return;
      }
      el.classList.add('wpst-motion-ready');
      const observer=new IntersectionObserver(function(entries){
        entries.forEach(function(entry){
          if(entry.isIntersecting){
            entry.target.classList.add('wpst-motion-visible');
            if(!entry.target.classList.contains('wpst-motion-repeat-yes'))observer.unobserve(entry.target);
          }else if(entry.target.classList.contains('wpst-motion-repeat-yes')){
            entry.target.classList.remove('wpst-motion-visible');
          }
        });
      },{threshold:thresholdFor(el),rootMargin:'0px 0px -3% 0px'});
      observer.observe(el);
    });
  }

  let raf=0;
  function initParallax(scope){
    if(reduce)return;
    const items=[...(scope||document).querySelectorAll('.wpst-parallax-yes')].filter(x=>x.dataset.wpstParallaxInit!=='1');
    if(!items.length)return;
    items.forEach(x=>x.dataset.wpstParallaxInit='1');
    const update=()=>{
      raf=0;
      document.querySelectorAll('.wpst-parallax-yes').forEach(function(el){
        const r=el.getBoundingClientRect();
        if(r.bottom<0||r.top>window.innerHeight)return;
        const center=r.top+r.height/2;
        const offset=(center-window.innerHeight/2)/window.innerHeight;
        el.style.setProperty('--wpst-parallax-y',(offset*-24).toFixed(2)+'px');
      });
    };
    const request=()=>{if(!raf)raf=requestAnimationFrame(update)};
    if(!window.__wpstParallaxBound){
      window.__wpstParallaxBound=true;
      window.addEventListener('scroll',request,{passive:true});
      window.addEventListener('resize',request,{passive:true});
    }
    request();
  }

  function initTilt(scope){
    if(reduce)return;
    (scope||document).querySelectorAll('.wpst-mouse-follow-yes').forEach(function(el){
      if(el.dataset.wpstTiltInit==='1')return;
      el.dataset.wpstTiltInit='1';
      el.addEventListener('pointermove',function(e){
        if(e.pointerType&&e.pointerType!=='mouse')return;
        const r=el.getBoundingClientRect();
        const x=(e.clientX-r.left)/r.width-.5,y=(e.clientY-r.top)/r.height-.5;
        el.style.setProperty('--wpst-tilt-x',(-y*5).toFixed(2)+'deg');
        el.style.setProperty('--wpst-tilt-y',(x*6).toFixed(2)+'deg');
      });
      el.addEventListener('pointerleave',function(){
        el.style.setProperty('--wpst-tilt-x','0deg');
        el.style.setProperty('--wpst-tilt-y','0deg');
      });
    });
  }

  function boot(scope){initEntries(scope);initParallax(scope);initTilt(scope)}
  document.addEventListener('DOMContentLoaded',()=>boot(document));

  // Elementor editor/preview may inject widgets after DOMContentLoaded.
  if(window.elementorFrontend&&window.elementorFrontend.hooks){
    window.elementorFrontend.hooks.addAction('frontend/element_ready/global',function($scope){
      const el=$scope&&$scope[0]?$scope[0]:document;
      boot(el.parentElement||el);
    });
  }else{
    const mo=new MutationObserver(function(muts){
      for(const m of muts){if(m.addedNodes&&m.addedNodes.length){boot(document);break;}}
    });
    if(document.documentElement)mo.observe(document.documentElement,{childList:true,subtree:true});
  }
})();


/* v3.2.88 — Video Background Pro */
document.addEventListener('DOMContentLoaded',function(){
  const items=[...document.querySelectorAll('[data-wpst-video-background]')];
  if(!items.length)return;

  const reduce=window.matchMedia&&window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  items.forEach(function(wrap){
    const video=wrap.querySelector('video');
    if(!video)return;

    if(reduce){
      try{video.pause();}catch(e){}
      return;
    }

    if(wrap.getAttribute('data-pause-offscreen')!=='1')return;

    if('IntersectionObserver' in window){
      const observer=new IntersectionObserver(function(entries){
        entries.forEach(function(entry){
          try{
            if(entry.isIntersecting){
              if(video.autoplay || video.hasAttribute('autoplay'))video.play().catch(function(){});
            }else{
              video.pause();
            }
          }catch(e){}
        });
      },{threshold:.08});
      observer.observe(wrap);
    }
  });
});


/* Inspector · Scroll To Top */
(function(){
  'use strict';
  function initScrollTop(){
    var btn=document.querySelector('[data-wpst-scroll-top]');
    if(!btn || btn.dataset.wpstReady==='1')return;
    btn.dataset.wpstReady='1';
    var threshold=Math.max(100,parseInt(btn.getAttribute('data-threshold')||'320',10));
    var ticking=false;
    function update(){
      btn.classList.toggle('is-visible',(window.pageYOffset||document.documentElement.scrollTop||0)>=threshold);
      ticking=false;
    }
    function onScroll(){
      if(!ticking){
        ticking=true;
        window.requestAnimationFrame(update);
      }
    }
    btn.addEventListener('click',function(){
      var reduce=window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
      window.scrollTo({top:0,left:0,behavior:reduce?'auto':'smooth'});
    });
    window.addEventListener('scroll',onScroll,{passive:true});
    update();
  }
  if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',initScrollTop);
  else initScrollTop();
})();
