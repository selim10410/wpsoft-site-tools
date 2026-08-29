document.addEventListener('DOMContentLoaded',function(){
  if(typeof WPST_LICENSE_STATE==='undefined'||WPST_LICENSE_STATE.active)return;

  const selectors=[
    'form#wpst-settings-form',
    '.wpst-mt-actions',
    '.wpst-mt-new-section',
    '.wpst-condition-editor',
    '.wpst-mega-wrap form',
    '.wpst-builder-grid'
  ];

  selectors.forEach(sel=>{
    document.querySelectorAll(sel).forEach(node=>{
      node.classList.add('wpst-readonly-locked');
      node.querySelectorAll('input,select,textarea,button').forEach(el=>{
        if(el.closest('.wpst-license-page'))return;
        el.disabled=true;
      });
      node.querySelectorAll('a').forEach(a=>{
        const href=a.getAttribute('href')||'';
        if(href.includes('wpst_export')||href.includes('wpsoft-system-status'))return;
        a.addEventListener('click',function(e){
          e.preventDefault();
          window.location.href=WPST_LICENSE_STATE.activationUrl;
        });
      });
    });
  });

  const host=document.querySelector('.wpst-wrap,.wpst-my-templates,.wpst-mega-wrap');
  if(host&&!host.querySelector('.wpst-readonly-lock-note')){
    const n=document.createElement('div');
    n.className='wpst-readonly-lock-note';
    n.textContent='WPSoft Site Tools salt okunur modda. Düzenleme, ekleme ve silme işlemleri için aktivasyon gereklidir.';
    host.insertBefore(n,host.firstChild);
  }
});