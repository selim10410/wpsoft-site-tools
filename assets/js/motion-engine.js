(function(){
'use strict';

var reduced=window.matchMedia&&window.matchMedia('(prefers-reduced-motion: reduce)').matches;
var entranceSelector=[
  '.wpst-entry-fade','.wpst-entry-fade-up','.wpst-entry-fade-down',
  '.wpst-entry-fade-left','.wpst-entry-fade-right','.wpst-entry-scale',
  '.wpst-entry-blur','.wpst-entry-zoom','.wpst-entry-rotate-soft',
  '.wpst-entry-clip-up','.wpst-entry-reveal-left','.wpst-entry-reveal-right',
  '.wpst-entry-flip-soft'
].join(',');

function deviceMotionDisabled(el){
  var w=window.innerWidth||document.documentElement.clientWidth||1200;
  if(w<=767 && el.classList.contains('wpst-motion-disable-mobile-yes'))return true;
  if(w>=768 && w<=1024 && el.classList.contains('wpst-motion-disable-tablet-yes'))return true;
  if(w>=1025 && el.classList.contains('wpst-motion-disable-desktop-yes'))return true;
  return false;
}

function cssNumber(el,name,fallback){
  var raw=getComputedStyle(el).getPropertyValue(name).trim();
  var n=parseFloat(raw);
  return isFinite(n)?n:fallback;
}

function staggerTargets(el){
  if(!el.classList.contains('wpst-stagger-yes'))return [];
  var container=el.querySelector(':scope > .elementor-widget-container');
  if(!container)return [];
  var root=container.children.length===1?container.firstElementChild:container;
  if(!root)return [];

  var direct=[].slice.call(root.children||[]).filter(function(n){
    return !/^(SCRIPT|STYLE|TEMPLATE)$/i.test(n.tagName||'');
  });

  if(direct.length<=1){
    var collection=root.querySelector(
      '.wpst-ew-service-cards-pro,.wpst-ew-service-carousel-track,.wpst-ew-icon-list,'+
      '.wpst-ew-icon-steps,.wpst-ew-number-cards,.wpst-ew-contact-cards,.wpst-ew-team,'+
      '.wpst-ew-testimonials,.wpst-ew-portfolio,.wpst-ew-product-showcase,.wpst-story-cards,'+
      '.wpst-ew-reveal-cards,.wpst-fm-cards,.wpst-ew-process-steps-pro,ul'
    );
    if(collection){
      direct=[].slice.call(collection.children||[]);
    }
  }
  return direct.slice(0,30);
}

function prepareStagger(el){
  var delay=cssNumber(el,'--wpst-stagger-delay',90);
  staggerTargets(el).forEach(function(item,i){
    item.classList.add('wpst-motion-stagger-item');
    item.style.setProperty('--wpst-stagger-item-delay',(delay*i)+'ms');
  });
}

function reveal(el){
  el.classList.add('is-wpst-motion-visible');
  staggerTargets(el).forEach(function(item){item.classList.add('is-wpst-stagger-visible');});
}

function reset(el){
  el.classList.remove('is-wpst-motion-visible');
  staggerTargets(el).forEach(function(item){item.classList.remove('is-wpst-stagger-visible');});
}

var observed=new WeakSet();
function initEntrance(el){
  if(observed.has(el))return;
  observed.add(el);
  prepareStagger(el);

  if(reduced||deviceMotionDisabled(el)){
    reveal(el);
    return;
  }

  if(!('IntersectionObserver' in window)){
    reveal(el);
    return;
  }

  var threshold=Math.max(0,Math.min(.8,cssNumber(el,'--wpst-motion-threshold',15)/100));
  var repeat=el.classList.contains('wpst-motion-repeat-yes');
  var observer=new IntersectionObserver(function(entries){
    entries.forEach(function(entry){
      if(entry.isIntersecting){
        reveal(entry.target);
        if(!repeat)observer.unobserve(entry.target);
      }else if(repeat){
        reset(entry.target);
      }
    });
  },{threshold:threshold,rootMargin:'0px 0px -4% 0px'});
  observer.observe(el);
}

function scan(root){
  var scope=root&&root.querySelectorAll?root:document;
  if(root&&root.matches&&root.matches(entranceSelector))initEntrance(root);
  scope.querySelectorAll(entranceSelector).forEach(initEntrance);
}

var parallaxReady=new WeakSet();
function initParallax(root){
  if(reduced)return;
  var scope=root&&root.querySelectorAll?root:document;
  var nodes=[];
  if(root&&root.matches&&root.matches('.wpst-parallax-yes'))nodes.push(root);
  scope.querySelectorAll('.wpst-parallax-yes').forEach(function(el){nodes.push(el);});
  nodes.forEach(function(el){parallaxReady.add(el);});
}

var ticking=false;
function updateParallax(){
  if(reduced)return;
  var h=window.innerHeight||1;
  document.querySelectorAll('.wpst-parallax-yes').forEach(function(el){
    if(deviceMotionDisabled(el)){el.style.setProperty('--wpst-parallax-y','0px');return;}
    var r=el.getBoundingClientRect();
    if(r.bottom<0||r.top>h)return;
    var p=((r.top+r.height/2)-(h/2))/h;
    el.style.setProperty('--wpst-parallax-y',(p*16).toFixed(2)+'px');
  });
  ticking=false;
}

var mouseReady=new WeakSet();
function initMouse(root){
  if(reduced||!(window.matchMedia&&window.matchMedia('(pointer:fine)').matches))return;
  var scope=root&&root.querySelectorAll?root:document;
  var nodes=[];
  if(root&&root.matches&&root.matches('.wpst-mouse-follow-yes'))nodes.push(root);
  scope.querySelectorAll('.wpst-mouse-follow-yes').forEach(function(el){nodes.push(el);});
  nodes.forEach(function(el){
    if(mouseReady.has(el))return;
    mouseReady.add(el);
    el.addEventListener('pointermove',function(e){
      if(deviceMotionDisabled(el))return;
      var r=el.getBoundingClientRect();
      if(!r.width||!r.height)return;
      el.style.setProperty('--wpst-mx',(((e.clientX-r.left)/r.width-.5)*2).toFixed(3));
      el.style.setProperty('--wpst-my',(((e.clientY-r.top)/r.height-.5)*2).toFixed(3));
    });
    el.addEventListener('pointerleave',function(){
      el.style.setProperty('--wpst-mx','0');
      el.style.setProperty('--wpst-my','0');
    });
  });
}

function boot(root){
  scan(root||document);
  initParallax(root||document);
  initMouse(root||document);
  updateParallax();
}

function ready(fn){
  if(document.readyState!=='loading')fn();
  else document.addEventListener('DOMContentLoaded',fn,{once:true});
}

ready(function(){
  boot(document);

  window.addEventListener('scroll',function(){
    if(!ticking){ticking=true;requestAnimationFrame(updateParallax);}
  },{passive:true});
  window.addEventListener('resize',function(){
    document.querySelectorAll(entranceSelector).forEach(function(el){
      if(deviceMotionDisabled(el))reveal(el);
    });
    if(!ticking){ticking=true;requestAnimationFrame(updateParallax);}
  },{passive:true});

  if('MutationObserver' in window){
    var mo=new MutationObserver(function(records){
      records.forEach(function(record){
        record.addedNodes.forEach(function(node){
          if(node.nodeType===1)boot(node);
        });
      });
    });
    var observerRoot=document.body||document.documentElement;
    if(observerRoot&&observerRoot.nodeType===1){
      mo.observe(observerRoot,{childList:true,subtree:true});
    }
  }

  if(window.elementorFrontend&&window.elementorFrontend.hooks){
    window.elementorFrontend.hooks.addAction('frontend/element_ready/global',function($scope){
      var node=$scope&&$scope[0]?$scope[0]:$scope;
      if(node)boot(node);
    });
  }
});
})();
