(function(){
'use strict';

function initShell(shell){
 if(!shell || shell.dataset.wpstPortfolioReady==='1')return;
 shell.dataset.wpstPortfolioReady='1';

 var initial=Math.max(1,parseInt(shell.getAttribute('data-initial-count')||'6',10));
 var grid=shell.querySelector('.wpst-ew-portfolio');
 if(!grid)return;

 var items=Array.prototype.slice.call(grid.querySelectorAll('.wpst-portfolio-item'));
 var filters=Array.prototype.slice.call(shell.querySelectorAll('.wpst-portfolio-filter'));
 var more=shell.querySelector('.wpst-portfolio-more');
 var active='all';
 var expanded=false;

 function matches(item){
  if(active==='all')return true;
  var cats=(item.getAttribute('data-categories')||'').split(/\s+/);
  return cats.indexOf(active)!==-1;
 }

 function render(){
  var matched=items.filter(matches);
  matched.forEach(function(item,index){
   var show=expanded || index<initial;
   item.classList.toggle('is-wpst-portfolio-hidden',!show);
   item.setAttribute('aria-hidden',show?'false':'true');
  });
  items.filter(function(item){return !matches(item);}).forEach(function(item){
   item.classList.add('is-wpst-portfolio-hidden');
   item.setAttribute('aria-hidden','true');
  });

  if(more){
   more.parentElement.style.display=(!expanded && matched.length>initial)?'flex':'none';
  }
 }

 filters.forEach(function(btn){
  btn.addEventListener('click',function(){
   active=btn.getAttribute('data-filter')||'all';
   expanded=false;
   filters.forEach(function(other){
    var selected=other===btn;
    other.classList.toggle('is-active',selected);
    other.setAttribute('aria-pressed',selected?'true':'false');
   });
   render();
  });
 });

 if(more){
  more.addEventListener('click',function(){
   expanded=true;
   render();
  });
 }

 render();
}

function init(root){
 (root||document).querySelectorAll('.wpst-portfolio-shell').forEach(initShell);
}

if(document.readyState==='loading'){
 document.addEventListener('DOMContentLoaded',function(){init(document);});
}else{
 init(document);
}

window.addEventListener('elementor/frontend/init',function(){
 if(window.elementorFrontend && elementorFrontend.hooks){
  elementorFrontend.hooks.addAction('frontend/element_ready/wpsoft-portfolio.default',function($scope){
   var el=$scope && $scope[0]?$scope[0]:document;
   init(el);
  });
 }
});
})();