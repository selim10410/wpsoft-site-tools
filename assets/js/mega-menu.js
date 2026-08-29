(function(){
'use strict';
function all(s,r){return Array.prototype.slice.call((r||document).querySelectorAll(s))}
function visibleRect(el){
 if(!el)return null;
 var r=el.getBoundingClientRect();
 if(r.width<=0||r.height<=0)return null;
 var cs=window.getComputedStyle(el);
 if(cs.display==='none'||cs.visibility==='hidden')return null;
 return r;
}
function megaContext(li){
 var header=li.closest('.wpsoft-site-header');
 var liveNav=li.closest('.wpst-q-nav');
 var elementorNav=li.closest('.wpst-navigation');
 var type=elementorNav?'elementor':(liveNav?'live':'generic');
 var surface=null;

 if(type==='live' && header){
   surface=header.querySelector('.wpst-header-row-main .wpst-header-row-inner') ||
           header.querySelector('.wpst-quick-inner') ||
           header.querySelector('.wpst-header-row-main');
 }

 if(type==='elementor'){
   var nav=elementorNav;
   var row=nav.closest('.e-con');
   if(!row){
     var widget=nav.closest('.elementor-element');
     row=widget?widget.parentElement:null;
   }
   surface=row||nav;
 }

 return {header:header,type:type,surface:surface};
}
function applyPrecisePanelPosition(li,panel,desiredTop,desiredCenter){
 /*
  * A position:fixed descendant becomes relative to a transformed ancestor.
  * Sticky/floating Live Builder and some Elementor containers use transforms.
  * After the first CSS position, measure the real viewport rectangle and
  * compensate the delta. This removes the horizontal drift and top gap in
  * both rendering systems without moving the panel outside its menu item.
  */
 var rect=panel.getBoundingClientRect();
 if(!rect.width)return;

 var desiredLeft=desiredCenter-(rect.width/2);
 var dx=desiredLeft-rect.left;
 var dy=desiredTop-rect.top;

 if(Math.abs(dx)>.5){
   var currentLeft=parseFloat(li.style.getPropertyValue('--wpst-mega-left'))||desiredCenter;
   li.style.setProperty('--wpst-mega-left',(currentLeft+dx)+'px');
 }
 if(Math.abs(dy)>.5){
   var currentTop=parseFloat(li.style.getPropertyValue('--wpst-mega-top'))||desiredTop;
   li.style.setProperty('--wpst-mega-top',Math.max(0,currentTop+dy)+'px');
 }
}
function position(li,settle){
 if(window.innerWidth<=900)return;
 var link=li.querySelector(':scope > a[data-wpst-mega="1"]');
 var panel=li.querySelector(':scope > .sub-menu, :scope > .wpst-elementor-mega-panel, :scope > .wpst-mega-dynamic-panel');
 if(!link||!panel)return;

 var r=link.getBoundingClientRect();
 var ctx=megaContext(li);
 var header=ctx.header;
 var headerRect=visibleRect(header);
 var surfaceRect=visibleRect(ctx.surface);
 var sticky=!!(header && (
   header.classList.contains('is-wpst-sticky-active') ||
   header.classList.contains('is-wpst-scrolled') ||
   window.getComputedStyle(header).position==='fixed' ||
   window.getComputedStyle(header).position==='sticky'
 ));

 /* Use the visible row bottom, not the whole header wrapper. Hidden hybrid
    sources/top padding must never create space above the mega panel. */
 var anchorRect=sticky?(surfaceRect||headerRect||r):(surfaceRect||r);
 var top=Math.max(0,Math.round(anchorRect.bottom));

 var panelWidth=panel.getBoundingClientRect().width||Math.min(1120,window.innerWidth-40);
 panelWidth=Math.min(panelWidth,window.innerWidth-24);
 var half=panelWidth/2;
 var align=link.getAttribute('data-wpst-panel-align')||'center';

 var widthMode='menu';
 if(li.classList.contains('wpst-mega-width-wide'))widthMode='wide';
 if(li.classList.contains('wpst-mega-width-full'))widthMode='full';

 var center=r.left+(r.width/2);

 if((widthMode==='wide'||widthMode==='full') && surfaceRect){
   center=surfaceRect.left+(surfaceRect.width/2);
 }else{
   if(align==='left')center=r.left+half;
   else if(align==='right')center=r.right-half;
 }

 center=Math.max(half+12,Math.min(window.innerWidth-half-12,center));

 li.style.setProperty('--wpst-mega-top',top+'px');
 li.style.setProperty('--wpst-mega-left',Math.round(center)+'px');
 li.classList.toggle('is-wpst-mega-sticky-context',sticky);
 li.classList.toggle('is-wpst-mega-live-context',ctx.type==='live');
 li.classList.toggle('is-wpst-mega-elementor-context',ctx.type==='elementor');
 panel.style.setProperty('--wpst-mega-available-height',Math.max(180,window.innerHeight-top-8)+'px');

 if(settle || li.classList.contains('is-wpst-mega-open')){
   applyPrecisePanelPosition(li,panel,top,center);
 }
}
function closeOthers(current){
 all('.wpst-mega-enabled.is-wpst-mega-open').forEach(function(x){
  if(x===current)return;
  x.classList.remove('is-wpst-mega-open');
  var a=x.querySelector(':scope > a[data-wpst-mega="1"]');
  if(a)a.setAttribute('aria-expanded','false');
 });
}
function init(){
 all('.wpst-mega-enabled').forEach(function(li){
  if(li.dataset.wpstMegaReady==='1')return;li.dataset.wpstMegaReady='1';
  var link=li.querySelector(':scope > a[data-wpst-mega="1"]');
  var sub=li.querySelector(':scope > .sub-menu');
  var panel=li.querySelector(':scope > .wpst-elementor-mega-panel, :scope > .wpst-mega-dynamic-panel');
  var drop=panel||sub;
  if(!link||!drop)return;

  var panelId='wpst-mega-panel-'+Math.random().toString(36).slice(2,9);
  drop.id=drop.id||panelId;
  link.setAttribute('aria-controls',drop.id);
  link.setAttribute('aria-expanded','false');

  var mobileToggle=null;

  var title=link.getAttribute('data-wpst-promo-title')||'';
  var text=link.getAttribute('data-wpst-promo-text')||'';
  var promoButton=link.getAttribute('data-wpst-promo-button')||'';
  var promoUrl=link.getAttribute('data-wpst-promo-url')||'#';
  var panelBg=link.getAttribute('data-wpst-panel-bg')||'#ffffff';
  var panelRadius=parseInt(link.getAttribute('data-wpst-panel-radius')||'22',10);
  [sub,panel].forEach(function(el){
    if(!el)return;
    el.style.background=panelBg;
    el.style.borderRadius=panelRadius+'px';
  });
  if(title&&sub&&!sub.querySelector('.wpst-mega-promo')){
   var promo=document.createElement('li');promo.className='wpst-mega-promo';
   promo.innerHTML='<div class="wpst-mega-promo-copy"><strong></strong><span></span></div>';
   promo.querySelector('strong').textContent=title;promo.querySelector('span').textContent=text;
   if(promoButton){var a=document.createElement('a');a.href=promoUrl||'#';a.textContent=promoButton+' →';promo.appendChild(a);}
   sub.appendChild(promo);
  }

  function open(){
    if(window.innerWidth<=900)return;
    closeOthers(li);
    position(li,false);
    li.classList.add('is-wpst-mega-open');
    link.setAttribute('aria-expanded','true');
    requestAnimationFrame(function(){
      position(li,true);
      requestAnimationFrame(function(){if(li.classList.contains('is-wpst-mega-open'))position(li,true);});
    });
  }
  function close(){
    li.classList.remove('is-wpst-mega-open');
    li.classList.remove('is-wpst-mega-sticky-context');
    li.classList.remove('is-wpst-mega-live-context');
    li.classList.remove('is-wpst-mega-elementor-context');
    link.setAttribute('aria-expanded','false');
  }

  li.addEventListener('mouseenter',function(){if(window.innerWidth>900)open()});
  li.addEventListener('mouseleave',function(){if(window.innerWidth>900)setTimeout(function(){if(!li.matches(':hover'))close()},80)});

  link.addEventListener('click',function(e){
   if(window.innerWidth<=900)return;
   if(link.getAttribute('href')==='#'||link.getAttribute('href')===''){e.preventDefault();li.classList.contains('is-wpst-mega-open')?close():open()}
  });

  link.addEventListener('focus',function(){if(window.innerWidth>900)open()});
  li.addEventListener('focusout',function(){setTimeout(function(){if(!li.contains(document.activeElement))close()},0)});
  li.addEventListener('keydown',function(e){if(e.key==='Escape'){close();link.focus();}});


  var megaPositionRaf=0;
  window.addEventListener('scroll',function(){
    if(!li.classList.contains('is-wpst-mega-open'))return;
    if(megaPositionRaf)cancelAnimationFrame(megaPositionRaf);
    megaPositionRaf=requestAnimationFrame(function(){megaPositionRaf=0;position(li,true);});
  },{passive:true});
  window.addEventListener('resize',function(){
    if(window.innerWidth<=900){close();return;}
    if(li.classList.contains('is-wpst-mega-open'))position(li,true);
  });
 });

 document.addEventListener('click',function(e){
  all('.wpst-mega-enabled.is-wpst-mega-open').forEach(function(li){if(!li.contains(e.target))li.classList.remove('is-wpst-mega-open')});
 });
}
document.addEventListener('DOMContentLoaded',init);
})();