(function(){
'use strict';
const labels={logo:'Logo',menu:'Menü',button:'Buton',search:'Arama',account:'Hesap',cart:'Sepet',text:'Metin',html:'HTML',social:'Sosyal Medya',spacer:'Esnek Boşluk',copyright:'Telif Yazısı'};
const defaults={logo:{type:'logo'},menu:{type:'menu',menu:0},button:{type:'button',text:'Teklif Al',url:'#iletisim'},search:{type:'search'},account:{type:'account',url:''},cart:{type:'cart',url:''},text:{type:'text',text:'Kısa açıklama'},html:{type:'html',html:'<strong>Özel HTML</strong>'},social:{type:'social',instagram:'',facebook:'',linkedin:'',x:''},spacer:{type:'spacer'},copyright:{type:'copyright'}};
const selectedSection={header:5,footer:5};
let dragged=null;
function esc(v){const d=document.createElement('div');d.textContent=v||'';return d.innerHTML}
function panel(type){return document.querySelector(`[data-panel="${type}"]`)}
function count(type){if(type==='header'||type==='footer')return 9;const i=panel(type).querySelector('[data-section-count-input]');return Math.max(1,Math.min(4,parseInt(i.value||'3',10)))}
function sectionPosition(type,n,c){
 if(type==='header'||type==='footer'){const rows=type==='header'?['Top Bar','Main Header','Bottom Bar']:['Üst Footer','Ana Footer','Alt Footer'],zones=['Sol Alan','Orta Alan','Sağ Alan'];const idx=Math.max(0,Math.min(8,n-1));return rows[Math.floor(idx/3)]+' · '+zones[idx%3];}
 if(c===1)return 'Tek Kolon';
 if(c===2)return n===1?'Sol Alan':'Sağ Alan';
 if(c===3)return n===1?'Sol Alan':(n===2?'Orta Alan':'Sağ Alan');
 if(c===4)return n===1?'Sol Alan':(n===2?'Orta Sol':(n===3?'Orta Sağ':'Sağ Alan'));
 return n+'. Alan';
}
function sectionName(type,n,c){return ((type==='header'||type==='footer')?'Alan ':'Bölüm ')+n+' · '+sectionPosition(type,n,c);}

function items(type){const i=document.querySelector(`[data-layout-input="${type}"]`);try{return JSON.parse(i.value||'[]')}catch(e){return[]}}
function save(type,data){document.querySelector(`[data-layout-input="${type}"]`).value=JSON.stringify(data);render(type)}
function normalize(type,data){const c=count(type);if(type==='header'||type==='footer'){const versionEl=panel(type).querySelector(type==='header'?'[data-header-builder-version]':'[data-footer-builder-version]');const saved=parseInt(versionEl?.dataset.savedVersion||'1',10);if(saved<2){const legacyEl=panel(type).querySelector('[data-section-count-input]'),legacy=Math.max(1,Math.min(4,parseInt(legacyEl?.value||'3',10)));const map=legacy===1?{1:5}:legacy===2?{1:4,2:6}:legacy===4?{1:4,2:5,3:5,4:6}:{1:4,2:5,3:6};data=data.map(x=>Object.assign({},x,{section:map[Math.max(1,Math.min(4,parseInt(x.section||1,10)))]||5}));if(versionEl)versionEl.dataset.savedVersion='2';}}return data.map((x,n)=>Object.assign({},x,{section:Math.max(1,Math.min(c,parseInt(x.section||((type==='header'||type==='footer')?5:Math.min(n+1,c)),10))) }))}
function autoArrange(type,data,c){if(!data.length)return data;const last=c;return data.map((item,index)=>{let section=1;if(c===1)section=1;else if(type==='header'){if(item.type==='logo')section=1;else if(item.type==='button')section=last;else if(item.type==='menu')section=c===2?2:Math.ceil(c/2);else if(item.type==='social')section=Math.max(1,last-1);else if(data.length===1)section=1;else section=Math.max(1,Math.min(c,Math.floor(index*(c-1)/(data.length-1))+1))}else{section=data.length===1?1:Math.max(1,Math.min(c,Math.floor(index*(c-1)/(data.length-1))+1))}return Object.assign({},item,{section})})}
function menuOptions(selected){let h='<option value="0">Varsayılan menü</option>';(WPST_DATA.menus||[]).forEach(m=>h+=`<option value="${m.id}" ${String(selected)===String(m.id)?'selected':''}>${esc(m.name)}</option>`);return h}
function preview(item,type){if(item.type==='logo'){const u=(WPST_DATA.logos&&WPST_DATA.logos[type])||'';return u?`<span class="wpst-preview-logo has-image"><img src="${esc(u)}"></span>`:`<span class="wpst-preview-logo">${esc(WPST_DATA.siteName||'wpsoft')}</span>`}if(item.type==='menu')return '<span class="wpst-preview-menu"><span>Anasayfa</span><span>Hizmetler</span><span>Hakkımızda</span><span>İletişim</span></span>';if(item.type==='button'){const st=`--preview-button-local:${esc(item.button_bg||'')};--preview-button-text-local:${esc(item.button_text_color||'')};--preview-button-hover-local:${esc(item.button_hover_bg||'')};--preview-button-hover-text-local:${esc(item.button_hover_text_color||'')}`;return `<span class="wpst-preview-button" style="${st}">${esc(item.text||'Buton')}</span>`;}if(item.type==='search')return '<span class="wpst-preview-action">⌕</span>';if(item.type==='account')return '<span class="wpst-preview-action">◎</span>';if(item.type==='cart')return '<span class="wpst-preview-action">▣</span>';if(item.type==='text')return `<span>${esc(item.text||'Metin')}</span>`;if(item.type==='html')return `<span class="wpst-preview-html">&lt;/&gt; ${esc((item.html||'HTML').replace(/<[^>]*>/g,' ').trim().slice(0,28)||'HTML')}</span>`;if(item.type==='social')return '<span class="wpst-preview-social">◎ f in X</span>';if(item.type==='spacer')return '<span class="wpst-preview-spacer"></span>';if(item.type==='copyright')return `<span>© ${new Date().getFullYear()} ${esc(WPST_DATA.siteName||'WPSoft')}</span>`;return labels[item.type]||item.type}
function elementFields(item,type){
 let h='',c=count(type);
 if(item.type==='menu')h+=`<label>Menü<select data-field="menu">${menuOptions(item.menu||0)}</select></label>`;
 if(item.type==='button')h+=`<label>Buton Yazısı<input data-field="text" value="${esc(item.text||'Buton')}"></label><label>Bağlantı<input data-field="url" value="${esc(item.url||'#')}"></label><div class="wpst-button-color-grid"><label>Arka Plan<input type="color" data-field="button_bg" value="${esc(item.button_bg||'#2563eb')}"></label><label>Yazı Rengi<input type="color" data-field="button_text_color" value="${esc(item.button_text_color||'#ffffff')}"></label><label>Hover Arka Plan<input type="color" data-field="button_hover_bg" value="${esc(item.button_hover_bg||'#1d4ed8')}"></label><label>Hover Yazı<input type="color" data-field="button_hover_text_color" value="${esc(item.button_hover_text_color||'#ffffff')}"></label></div><small class="wpst-button-color-help">Renkler canlı önizlemeye anında uygulanır ve yalnızca bu header butonunu etkiler.</small>`;if(item.type==='account'||item.type==='cart')h+=`<label>Özel Bağlantı<input data-field="url" value="${esc(item.url||'')}" placeholder="Boş bırakılırsa otomatik"></label>`;
 if(item.type==='text')h+=`<label>Metin<input data-field="text" value="${esc(item.text||'')}"></label>`;
 if(item.type==='html')h+=`<label>HTML Kodu<textarea data-field="html" rows="8" placeholder="<div>...</div>">${esc(item.html||'')}</textarea><small>Temel ve güvenli HTML etiketleri desteklenir.</small></label>`;
 if(item.type==='social')h+=['instagram','facebook','linkedin','x'].map(k=>`<label>${k.toUpperCase()}<input data-field="${k}" value="${esc(item[k]||'')}" placeholder="https://"></label>`).join('');
 h+=`<label>Yerleşeceği Bölüm<select data-field="section">${Array.from({length:c},(_,i)=>i+1).map(n=>`<option value="${n}" ${Number(item.section)===n?'selected':''}>${sectionName(type,n,c)}</option>`).join('')}</select><small>Bloğun hangi görsel alanda yer alacağını seçin.</small></label>`;
 h+=`<label class="wpst-inline-check"><input type="checkbox" data-field="hide_mobile" ${item.hide_mobile?'checked':''}> Mobilde gizle</label>`;
 return h;
}
function openInspector(type,index){
 const p=panel(type),data=items(type),item=data[index];if(!item)return;
 const c=count(type),sn=Number(item.section)||1,box=p.querySelector('.wpst-element-inspector');
 box.innerHTML=`<div class="wpst-inspector-title"><strong>${labels[item.type]||item.type}</strong><span>${sectionName(type,sn,c)}</span></div>${elementFields(item,type)}<div class="wpst-inspector-actions"><button type="button" class="button wpst-duplicate-inspector">Kopyala</button><button type="button" class="button-link-delete wpst-delete-inspector">Sil</button></div>`;
 activateInspector(p,'element');
 box.querySelectorAll('[data-field]').forEach(f=>f.addEventListener('input',()=>{const key=f.dataset.field;data[index][key]=f.type==='checkbox'?(f.checked?1:0):(key==='menu'||key==='section'?parseInt(f.value,10):f.value);save(type,data);setTimeout(()=>openInspector(type,index),0)}));
 box.querySelector('.wpst-delete-inspector').onclick=()=>{data.splice(index,1);save(type,data);box.innerHTML='Önizlemede bir elemana tıklayın. Ayarları burada açılır.'};
 box.querySelector('.wpst-duplicate-inspector').onclick=()=>{data.splice(index+1,0,JSON.parse(JSON.stringify(item)));save(type,data)};
}
function activateInspector(p,name){p.querySelectorAll('[data-inspector],[data-inspector-pane]').forEach(x=>x.classList.remove('is-active'));p.querySelector(`[data-inspector="${name}"]`).classList.add('is-active');p.querySelector(`[data-inspector-pane="${name}"]`).classList.add('is-active')}
function renderSectionList(type,data){
 const p=panel(type),list=p.querySelector(`[data-section-list="${type}"]`),c=count(type);
 if(type==='header'||type==='footer'){
   const rows=type==='header'?[['Top Bar',1],['Main Header',4],['Bottom Bar',7]]:[['Üst Footer',1],['Ana Footer',4],['Alt Footer',7]];
   list.innerHTML=`<div class="wpst-section-list-guide"><strong>${type==='header'?'Header':'Footer'} Satırları</strong><small>Satır ve konum seçin; yeni blok doğrudan o alana eklenir.</small></div>`+rows.map(([label,start])=>{const zones=[0,1,2].map(off=>{const n=start+off,its=data.filter(x=>Number(x.section||5)===n),content=its.length?its.map(x=>labels[x.type]||x.type).join(' · '):'Boş';return `<button type="button" class="wpst-section-card ${type==='header'?'wpst-header-zone-card':'wpst-footer-zone-card'} ${selectedSection[type]===n?'is-active':''}" data-select-section="${n}"><span class="wpst-section-number">${off===0?'L':off===1?'O':'R'}</span><span class="wpst-section-card-copy"><b>${['Sol','Orta','Sağ'][off]}</b><small>${content}</small></span><span class="wpst-section-count-badge">${its.length}</span></button>`}).join('');return `<div class="${type==='header'?'wpst-header-row-group':'wpst-footer-row-group'}"><div class="wpst-header-row-group-title"><strong>${label}</strong><small>${label==='Main Header'?'Ana navigasyon':(label==='Ana Footer'?'Ana footer içeriği':'İsteğe bağlı satır')}</small></div>${zones}</div>`}).join('');
 }else{
   list.innerHTML='<div class="wpst-section-list-guide"><strong>Yerleşim Alanları</strong><small>Bir alan seçin; yeni blok doğrudan o alana eklenir.</small></div>'+Array.from({length:c},(_,i)=>{const n=i+1,its=data.filter(x=>Number(x.section||1)===n),content=its.length?its.map(x=>labels[x.type]).join(' · '):'Henüz blok yok';return `<button type="button" class="wpst-section-card section-${n} ${selectedSection[type]===n?'is-active':''}" data-select-section="${n}"><span class="wpst-section-number">${n}</span><span class="wpst-section-card-copy"><b>${sectionPosition(type,n,c)}</b><small>${content}</small></span><span class="wpst-section-count-badge">${its.length} blok</span></button>`}).join('');
 }
 list.querySelectorAll('[data-select-section]').forEach(b=>b.onclick=()=>{selectedSection[type]=parseInt(b.dataset.selectSection,10);render(type);const info=p.querySelector('.wpst-selected-section-info');info.innerHTML=`<strong>${sectionName(type,selectedSection[type],c)}</strong><p>Yeni bloklar bu alana eklenecek. Önizlemede aynı alan vurgulanır.</p>`;activateInspector(p,'section')});
}
function ensureFrame(canvas,type){
 // The Header live preview canvas is intentionally moved inside
 // .wpst-header-preview-scroll. Therefore checking only parentElement would
 // create a new browser frame on every render/inspector interaction.
 // Reuse the closest existing frame instead and keep a single scroll surface.
 const existing=canvas.closest('.wpst-device-frame');
 if(existing){
   if(type==='header'){
     let surface=existing.querySelector(':scope > .wpst-header-preview-scroll');
     if(!surface){
       surface=document.createElement('div');
       surface.className='wpst-header-preview-scroll';
       surface.innerHTML='<div class="wpst-header-preview-spacer" aria-hidden="true"><div></div><div></div><div></div><div></div></div>';
       existing.appendChild(surface);
     }
     if(canvas.parentElement!==surface) surface.insertBefore(canvas,surface.firstChild);
   }
   return existing;
 }
 const f=document.createElement('div');
 f.className='wpst-device-frame is-desktop is-'+type+'-preview';
 f.innerHTML='<div class="wpst-browser-bar"><span class="wpst-browser-dots"><i></i><i></i><i></i></span><span class="wpst-browser-address">siteniz.com</span></div><div class="wpst-phone-notch"></div><button type="button" class="wpst-mobile-menu-toggle" aria-label="Mobil menü"><span></span><span></span><span></span></button><div class="wpst-mobile-drawer"><div class="wpst-mobile-drawer-head"><div class="wpst-mobile-drawer-brand">WPSOFT</div><strong>Menü</strong><button type="button" aria-label="Menüyü kapat">×</button></div><nav><a>Anasayfa</a><a>Hizmetler</a><a>Hakkımızda</a><a>İletişim</a></nav><div class="wpst-mobile-drawer-contact"><strong>Hızlı İletişim</strong><span class="is-phone">+90 555 000 00 00</span><span class="is-email">info@example.com</span></div><div class="wpst-mobile-drawer-social"><i>IG</i><i>FB</i><i>YT</i><i>IN</i></div><button type="button" class="wpst-drawer-cta">Teklif Al</button></div>';
 canvas.parentNode.insertBefore(f,canvas);
 if(type==='header'){
   const surface=document.createElement('div');
   surface.className='wpst-header-preview-scroll';
   surface.innerHTML='<div class="wpst-header-preview-spacer" aria-hidden="true"><div></div><div></div><div></div><div></div></div>';
   f.appendChild(surface);
   surface.insertBefore(canvas,surface.firstChild);
 }else{
   f.appendChild(canvas);
 }
 const toggle=f.querySelector('.wpst-mobile-menu-toggle');
 const close=f.querySelector('.wpst-mobile-drawer-head button');
 if(toggle)toggle.onclick=()=>f.classList.toggle('is-menu-open');
 if(close)close.onclick=()=>f.classList.remove('is-menu-open');
 return f
}
function applyStyles(type){
 const p=panel(type),canvas=p.querySelector(`[data-builder="${type}"]`),get=n=>p.querySelector(`[name="wpst_settings[${n}]"]`);
 if(!canvas)return;
 const frame=ensureFrame(canvas,type);
 canvas.style.setProperty('--preview-bg',get(type+'_background')?.value||'#fff');
 canvas.style.setProperty('--preview-color',get(type+'_text_color')?.value||'#111827');
 canvas.style.setProperty('--preview-pad',(get(type+'_padding')?.value||16)+'px');
 canvas.style.setProperty('--preview-button',get('button_background')?.value||'#2563eb');
 canvas.style.setProperty('--preview-radius',(get('button_radius')?.value||10)+'px');

 const w=parseInt(get(type+'_logo_width')?.value||'0',10),h=parseInt(get(type+'_logo_height')?.value||'0',10);
 canvas.querySelectorAll('.wpst-preview-logo img').forEach(img=>{img.style.width=w>0?w+'px':'';img.style.height=h>0?h+'px':'';img.style.maxWidth=w>0?'none':'';img.style.maxHeight=h>0?'none':'';img.style.objectFit='contain'});

 const checked=n=>!!get(n)?.checked;
 if(type==='header'){
   frame.classList.toggle('is-preview-transparent',checked('header_transparent'));
   frame.classList.toggle('is-preview-sticky',checked('header_sticky'));
   frame.classList.toggle('is-preview-shrink',checked('header_shrink'));
   frame.classList.toggle('is-preview-blur',checked('header_blur'));
   frame.dataset.stickyMode=get('header_sticky_mode')?.value||'always';
   frame.dataset.drawerSide=get('header_mobile_drawer_side')?.value||'right';
   frame.dataset.drawerStyle=get('header_mobile_drawer_style')?.value||'clean';
   const boxedLayout=(p.querySelector('[name="wpst_settings[header_layout_style]"]:checked')?.value||'normal')==='boxed';
   frame.classList.toggle('is-preview-boxed',boxedLayout);
   frame.style.setProperty('--preview-boxed-width',Math.min(98,Math.max(62,((parseInt(get('header_boxed_width')?.value||'1260',10)/1366)*100)))+'%');
   frame.style.setProperty('--preview-boxed-radius',(get('header_boxed_radius')?.value||14)+'px');
   frame.style.setProperty('--wpst-mobile-drawer-width',Math.min(92,Math.max(62,((parseInt(get('header_mobile_drawer_width')?.value||'340',10)/390)*100)))+'%');
   frame.classList.toggle('has-mobile-overlay',checked('header_mobile_overlay'));
   frame.classList.toggle('has-mobile-drawer-logo',checked('header_mobile_drawer_logo'));

   // Live Header Builder sticky runtime. The preview has its own scroll surface,
   // so window.scrollY can never drive the sticky/scrolled state here.
   const previewScroll=frame.querySelector('.wpst-header-preview-scroll');
   if(previewScroll){
     const threshold=Math.max(0,parseInt(get('header_scroll_threshold')?.value||'60',10));
     frame.style.setProperty('--wpst-preview-scroll-bg',get('header_scrolled_background')?.value||'#ffffff');
     frame.style.setProperty('--wpst-preview-scroll-color',get('header_scrolled_text_color')?.value||'#111827');
     frame.style.setProperty('--wpst-preview-scrolled-height',(get('header_scrolled_height')?.value||'64')+'px');
     const syncStickyPreview=()=>{
       const enabled=checked('header_sticky');
       const mode=get('header_sticky_mode')?.value||'always';
       const y=previewScroll.scrollTop||0;
       const scrolled=y>threshold;
       frame.classList.toggle('is-preview-scrolled',scrolled);
       frame.classList.toggle('is-preview-sticky-active',enabled && (mode==='always' || scrolled));
       frame.dataset.stickyMode=mode;
     };
     if(previewScroll.dataset.wpstStickyBound!=='1'){
       previewScroll.dataset.wpstStickyBound='1';
       previewScroll.addEventListener('scroll',syncStickyPreview,{passive:true});
     }
     syncStickyPreview();
   }

   const drawer=frame.querySelector('.wpst-mobile-drawer');
   if(drawer){
     const cta=drawer.querySelector('.wpst-drawer-cta');
     if(cta){cta.textContent=get('header_mobile_cta_text')?.value||'Teklif Al';cta.style.display=checked('header_mobile_cta_enabled')?'block':'none';}
     const close=drawer.querySelector('.wpst-mobile-drawer-head button');
     if(close)close.title=get('header_mobile_close_text')?.value||'Kapat';
     const brand=drawer.querySelector('.wpst-mobile-drawer-brand');
     if(brand)brand.style.display=checked('header_mobile_drawer_logo')?'block':'none';
     const contact=drawer.querySelector('.wpst-mobile-drawer-contact');
     if(contact){
       contact.style.display=checked('header_mobile_contact_enabled')?'grid':'none';
       const strong=contact.querySelector('strong');if(strong)strong.textContent=get('header_mobile_contact_title')?.value||'Hızlı İletişim';
       const phone=contact.querySelector('.is-phone');if(phone)phone.textContent=get('header_mobile_phone')?.value||'+90 555 000 00 00';
       const email=contact.querySelector('.is-email');if(email)email.textContent=get('header_mobile_email')?.value||'info@example.com';
     }
     const social=drawer.querySelector('.wpst-mobile-drawer-social');
     if(social)social.style.display=checked('header_mobile_social_enabled')?'flex':'none';
   }

   const announcement=frame.querySelector('.wpst-preview-announcement');
   const topbar=frame.querySelector('.wpst-preview-topbar');
   if(announcement){
     announcement.style.display=checked('header_announcement_enabled')?'flex':'none';
     announcement.querySelector('span').textContent=get('header_announcement_text')?.value||'Duyuru metni';
     announcement.querySelector('b').textContent=(get('header_announcement_link_text')?.value||'İncele')+' →';
   }
   if(topbar){
     topbar.style.display=checked('header_topbar_enabled')?'flex':'none';
     topbar.querySelector('span').textContent=get('header_topbar_text')?.value||'Topbar metni';
     topbar.querySelector('b').textContent=get('header_topbar_link_text')?.value||'İletişim';
   }
   const previewRows=[['top',0],['main',1],['bottom',2]];
   const activeDevice=frame.classList.contains('is-mobile')?'mobile':(frame.classList.contains('is-tablet')?'tablet':'desktop');
   previewRows.forEach(([key,index])=>{
     const row=canvas.querySelectorAll('.wpst-preview-header-row')[index];if(!row)return;
     const prefix='header_row_'+key+'_';
     const h=get(prefix+'height_'+activeDevice)?.value||(key==='main'?'78':'38');
     row.style.minHeight=h+'px';
     row.style.background=get(prefix+'background')?.value||(key==='main'?'#ffffff':'#f8fafc');
     row.style.color=get(prefix+'text_color')?.value||'#111827';
     row.style.borderBottom=(get(prefix+'border_width')?.value||0)+'px solid '+(get(prefix+'border_color')?.value||'#e5e7eb');
     row.style.setProperty('--wpst-preview-row-container',(get(prefix+'container')?.value||1200)+'px');
     row.classList.toggle('is-row-fullwidth',checked(prefix+'full_width'));
     row.classList.toggle('is-row-device-hidden',!checked(prefix+'show_'+activeDevice));
   });
 }
 if(type==='footer'){
   frame.classList.toggle('is-footer-reveal',checked('footer_reveal'));
   frame.dataset.footerColumns=get('footer_mobile_columns')?.value||'1';
   frame.dataset.footerAlign=get('footer_mobile_align')?.value||'left';
   const previewRows=[['top',0],['main',1],['bottom',2]];
   const activeDevice=frame.classList.contains('is-mobile')?'mobile':(frame.classList.contains('is-tablet')?'tablet':'desktop');
   previewRows.forEach(([key,index])=>{
     const row=canvas.querySelectorAll('.wpst-preview-footer-row')[index];if(!row)return;
     const prefix='footer_row_'+key+'_';
     row.style.minHeight=(get(prefix+'height_'+activeDevice)?.value||(key==='main'?'190':'72'))+'px';
     row.style.background=get(prefix+'background')?.value||'#111827';
     row.style.color=get(prefix+'text_color')?.value||'#ffffff';
     row.style.borderTop=(get(prefix+'border_width')?.value||0)+'px solid '+(get(prefix+'border_color')?.value||'#243047');
     row.style.setProperty('--wpst-preview-row-container',(get(prefix+'container')?.value||1200)+'px');
     row.classList.toggle('is-row-fullwidth',checked(prefix+'full_width'));
     row.classList.toggle('is-row-device-hidden',!checked(prefix+'show_'+activeDevice));
   });
 }

}
function render(type){
 const p=panel(type),canvas=p.querySelector(`[data-builder="${type}"]`),frame=ensureFrame(canvas,type);
 let data=normalize(type,items(type));document.querySelector(`[data-layout-input="${type}"]`).value=JSON.stringify(data);renderSectionList(type,data);
 const c=count(type);canvas.style.setProperty('--section-count',(type==='header'||type==='footer')?1:c);canvas.innerHTML='';
 const addSection=(s)=>{const subset=data.map((x,i)=>({x,i})).filter(o=>Number(o.x.section||((type==='header'||type==='footer')?5:1))===s);const sec=document.createElement('div');sec.className='wpst-preview-section section-'+s+(selectedSection[type]===s?' is-selected':'')+(subset.length?' has-items':' is-empty');sec.dataset.section=s;const short=(type==='header'||type==='footer')?['Sol','Orta','Sağ'][(s-1)%3]:sectionPosition(type,s,c);sec.innerHTML=`<div class="wpst-section-visual-head"><span class="wpst-section-index">${(type==='header'||type==='footer')?['L','O','R'][(s-1)%3]:s}</span><span class="wpst-section-position"><b>${short}</b><small>${subset.length} blok</small></span><span class="wpst-section-selected-mark">${selectedSection[type]===s?'SEÇİLİ':'SEÇ'}</span></div><div class="wpst-section-dropzone"></div>`;const dz=sec.querySelector('.wpst-section-dropzone');sec.onclick=e=>{if(e.target.closest('.wpst-canvas-item'))return;selectedSection[type]=s;render(type);p.querySelector('.wpst-selected-section-info').innerHTML=`<strong>${sectionName(type,s,c)}</strong><p>Yeni bloklar bu alana eklenir.</p>`;activateInspector(p,'section')};if(!subset.length)dz.innerHTML=`<div class="wpst-section-empty"><b>+ Blok ekle</b><span>${short}</span></div>`;subset.forEach(({x:item,i:index})=>{const el=document.createElement('div');el.className='wpst-canvas-item wpst-item-'+item.type+(item.hide_mobile?' is-mobile-hidden':'');el.draggable=true;el.dataset.index=index;el.innerHTML=`<div class="wpst-item-preview">${preview(item,type)}</div><div class="wpst-item-actions"><span class="dashicons dashicons-move"></span><button type="button" title="Ayarlar">⚙</button></div>`;el.onclick=e=>{e.stopPropagation();openInspector(type,index)};el.addEventListener('dragstart',()=>{dragged=index;el.classList.add('is-dragging')});el.addEventListener('dragend',()=>el.classList.remove('is-dragging'));dz.appendChild(el)});sec.ondragover=e=>{e.preventDefault();sec.classList.add('is-drop-target')};sec.ondragleave=e=>{if(!sec.contains(e.relatedTarget))sec.classList.remove('is-drop-target')};sec.ondrop=e=>{e.preventDefault();sec.classList.remove('is-drop-target');const block=e.dataTransfer.getData('wpst/block');if(block){data.push(Object.assign({},defaults[block],{section:s}));save(type,data);return}if(dragged!==null&&data[dragged]){data[dragged].section=s;save(type,data)}};return sec};
 if(type==='header'||type==='footer'){const rows=type==='header'?[['Top Bar',1],['Main Header',4],['Bottom Bar',7]]:[['Üst Footer',1],['Ana Footer',4],['Alt Footer',7]];rows.forEach(([label,start])=>{const row=document.createElement('div');row.className=(type==='header'?'wpst-preview-header-row':'wpst-preview-footer-row')+' '+(start===4?'is-main':'');row.innerHTML=`<div class="wpst-preview-row-label"><strong>${label}</strong><span>${start===4?(type==='header'?'Ana navigasyon':'Ana içerik'):'Opsiyonel'}</span></div><div class="wpst-preview-row-zones"></div>`;const zones=row.querySelector('.wpst-preview-row-zones');[start,start+1,start+2].forEach(n=>zones.appendChild(addSection(n)));canvas.appendChild(row)})}else{for(let s=1;s<=c;s++)canvas.appendChild(addSection(s))}
 frame.classList.toggle('has-menu',data.some(i=>i.type==='menu'&&!i.hide_mobile));const btn=data.find(i=>i.type==='button'&&!i.hide_mobile),cta=frame.querySelector('.wpst-drawer-cta');if(btn){cta.style.display='block';cta.textContent=btn.text||'Teklif Al'}else cta.style.display='none';applyStyles(type)
}
document.querySelectorAll('.wpst-block-add').forEach(b=>{b.addEventListener('click',()=>{const type=b.closest('.wpst-panel').dataset.panel,data=items(type);data.push(Object.assign({},defaults[b.dataset.block],{section:selectedSection[type]}));save(type,data)});b.addEventListener('dragstart',e=>e.dataTransfer.setData('wpst/block',b.dataset.block))});

document.querySelectorAll('[data-panel="header"] [data-inspector-pane="general"] input,[data-panel="header"] [data-inspector-pane="general"] select').forEach(el=>{
  el.addEventListener('input',()=>applyStyles('header'));
  el.addEventListener('change',()=>applyStyles('header'));
});
document.querySelectorAll('[data-panel="footer"] [data-inspector-pane="general"] input,[data-panel="footer"] [data-inspector-pane="general"] select').forEach(el=>{
  el.addEventListener('input',()=>applyStyles('footer'));
  el.addEventListener('change',()=>applyStyles('footer'));
});

document.querySelectorAll('.wpst-section-count').forEach(b=>b.onclick=()=>{const p=b.closest('.wpst-panel'),type=p.dataset.panel,c=parseInt(b.dataset.sections,10);p.querySelector('[data-section-count-input]').value=c;p.querySelectorAll('.wpst-section-count').forEach(x=>x.classList.toggle('is-active',x===b));if(selectedSection[type]>c)selectedSection[type]=c;let data=autoArrange(type,items(type),c);save(type,data)});
document.querySelectorAll('.wpst-tab').forEach(b=>b.onclick=()=>{document.querySelectorAll('.wpst-tab,.wpst-panel').forEach(x=>x.classList.remove('is-active'));b.classList.add('is-active');const target=panel(b.dataset.tab);if(target)target.classList.add('is-active')});
document.querySelectorAll('.wpst-device-switch button').forEach(b=>b.onclick=()=>{const g=b.parentNode,p=b.closest('.wpst-panel'),frame=p.querySelector('.wpst-device-frame');g.querySelectorAll('button').forEach(x=>x.classList.remove('is-active'));b.classList.add('is-active');const device=b.dataset.device||'desktop';frame.classList.toggle('is-mobile',device==='mobile');frame.classList.toggle('is-tablet',device==='tablet');frame.classList.toggle('is-desktop',device==='desktop');frame.classList.remove('is-menu-open');applyStyles(p.dataset.panel)});
document.querySelectorAll('.wpst-inspector-tabs button').forEach(b=>b.onclick=()=>activateInspector(b.closest('.wpst-panel'),b.dataset.inspector));
function updateModes(){document.querySelectorAll('.wpst-panel').forEach(p=>{const r=p.querySelector('.wpst-mode-switch input:checked');if(r)p.querySelectorAll('[data-mode-content]').forEach(c=>c.style.display=c.dataset.modeContent===r.value?'block':'none')})}
document.querySelectorAll('.wpst-mode-switch input').forEach(r=>r.addEventListener('change',updateModes));

/* Header device source synchronizer.
 * A dedicated Elementor template selection must not silently remain behind a
 * stale Live Builder source value. Conversely, choosing Live Builder must stay
 * authoritative and must not be overwritten by the template selector.
 */
(function syncHeaderDeviceSources(){
 const form=document.querySelector('form.wpst-settings-form, form[action="options.php"]');
 const root=form||document;
 const desktopSource=root.querySelector('[name="wpst_settings[header_desktop_source]"]');
 const mobileSource=root.querySelector('[name="wpst_settings[header_mobile_source]"]');
 const desktopTemplate=root.querySelector('[name="wpst_settings[header_template]"]');
 const mobileTemplate=root.querySelector('[name="wpst_settings[mobile_header_template]"]');
 if(!desktopSource||!mobileSource)return;

 const mark=function(source,template){
   if(!template)return;
   const isElementor=source.value==='elementor';
   template.disabled=!isElementor;
   template.closest('label')?.classList.toggle('is-source-disabled',!isElementor);
 };
 const refresh=function(){mark(desktopSource,desktopTemplate);mark(mobileSource,mobileTemplate)};

 desktopSource.addEventListener('change',refresh);
 mobileSource.addEventListener('change',refresh);

 if(desktopTemplate)desktopTemplate.addEventListener('change',function(){
   if(parseInt(this.value||'0',10)>0 && desktopSource.value!=='elementor'){
     desktopSource.value='elementor';
     desktopSource.dispatchEvent(new Event('change',{bubbles:true}));
   }
 });
 if(mobileTemplate)mobileTemplate.addEventListener('change',function(){
   if(parseInt(this.value||'0',10)>0 && mobileSource.value!=='elementor'){
     mobileSource.value='elementor';
     mobileSource.dispatchEvent(new Event('change',{bubbles:true}));
   }
 });
 refresh();
})();
document.querySelectorAll('[data-logo-control]').forEach(control=>{const type=control.dataset.logoControl,input=control.querySelector('[data-logo-id]'),img=control.querySelector('img'),empty=control.querySelector('.wpst-logo-preview span'),previewBox=control.querySelector('.wpst-logo-preview'),remove=control.querySelector('.wpst-logo-remove'),select=control.querySelector('.wpst-logo-select');let media=null;select.onclick=()=>{if(typeof wp==='undefined'||!wp.media)return;if(media){media.open();return}media=wp.media({title:'Logo Seç',button:{text:'Bu logoyu kullan'},library:{type:'image'},multiple:false});media.on('select',()=>{const a=media.state().get('selection').first().toJSON(),url=(a.sizes&&a.sizes.medium?a.sizes.medium.url:a.url);input.value=a.id;img.src=url;img.style.display='block';empty.textContent='';previewBox.classList.remove('is-empty');remove.style.display='';WPST_DATA.logos=WPST_DATA.logos||{};WPST_DATA.logos[type]=url;render(type.indexOf('header')===0?'header':type)});media.open()};remove.onclick=()=>{input.value=0;img.style.display='none';empty.textContent='Logo seçilmedi';previewBox.classList.add('is-empty');remove.style.display='none';WPST_DATA.logos[type]='';render(type.indexOf('header')===0?'header':type)}});
document.querySelectorAll('.wpst-design input,.wpst-design select,.wpst-design textarea').forEach(el=>el.addEventListener('input',()=>applyStyles(el.closest('.wpst-panel').dataset.panel)));

function globalPreview(){
 const root=document.querySelector('[data-panel="global"]');
 if(!root)return;
 const get=n=>root.querySelector(`[name="wpst_settings[${n}]"]`);
 const demo=root.querySelector('.wpst-global-demo');
 if(!demo)return;
 const primary=get('global_primary')?.value||'#2563eb';
 const heading=get('global_heading')?.value||'#0f172a';
 const muted=get('global_muted')?.value||'#64748b';
 const surface=get('global_surface')?.value||'#ffffff';
 const soft=get('global_soft')?.value||'#f8fafc';
 const border=get('global_border')?.value||'#e2e8f0';
 const radius=(get('global_card_radius')?.value||20)+'px';
 const bodyFont=get('global_body_font')?.value||'system';
 const headingFont=get('global_heading_font')?.value||'system';
 const fontMap={
   system:'-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif',
   inter:'Inter,sans-serif',
   manrope:'Manrope,sans-serif',
   dmsans:'"DM Sans",sans-serif',
   plusjakarta:'"Plus Jakarta Sans",sans-serif',
   outfit:'Outfit,sans-serif',
   sora:'Sora,sans-serif',
   spacegrotesk:'"Space Grotesk",sans-serif',
   urbanist:'Urbanist,sans-serif',
   figtree:'Figtree,sans-serif',
   worksans:'"Work Sans",sans-serif',
   nunitosans:'"Nunito Sans",sans-serif',
   sourcesans3:'"Source Sans 3",sans-serif',
   playfair:'"Playfair Display",serif',
   cormorant:'"Cormorant Garamond",serif',
   poppins:'Poppins,sans-serif',
   montserrat:'Montserrat,sans-serif',
   roboto:'Roboto,sans-serif',
   opensans:'"Open Sans",sans-serif',
   lato:'Lato,sans-serif'
 };
 const br=(get('global_button_radius')?.value||12)+'px';
 const bh=(get('global_button_height')?.value||48)+'px';
 demo.style.background=surface;demo.style.borderColor=border;demo.style.borderRadius=radius;
 demo.style.fontFamily=fontMap[bodyFont]||fontMap.system;
 demo.querySelector('small').style.color=primary;
 demo.querySelector('h3').style.color=heading;
 demo.querySelector('h3').style.fontFamily=fontMap[headingFont]||fontMap.system;
 demo.querySelector('h3').style.fontWeight=get('global_heading_weight')?.value||800;
 demo.querySelector('h3').style.lineHeight=get('global_heading_line_height')?.value||1.1;
 demo.querySelector('h3').style.letterSpacing=(get('global_heading_letter_spacing')?.value||-0.02)+'em';
 demo.querySelector('p').style.color=muted;
 demo.querySelectorAll('.wpst-global-demo-cards div').forEach(x=>{x.style.background=soft;x.style.borderColor=border;x.style.borderRadius=radius});
 demo.querySelectorAll('.wpst-global-demo-cards b').forEach(x=>x.style.color=primary);
 const a=demo.querySelector('a');a.style.background=primary;a.style.borderRadius=br;a.style.minHeight=bh;
 a.style.fontWeight=get('global_button_weight')?.value||800;
 a.style.letterSpacing=(get('global_button_letter_spacing')?.value||0)+'em';
 a.style.textTransform=get('global_button_text_transform')?.value||'none';
 root.querySelectorAll('.wpst-global-color input[type="color"]').forEach(i=>{const code=i.closest('.wpst-global-color')?.querySelector('code');if(code)code.textContent=i.value});
}
document.querySelectorAll('[data-panel="global"] input,[data-panel="global"] select').forEach(el=>el.addEventListener('input',globalPreview));

render('header');render('footer');updateModes();globalPreview();
window.WPSTApplyBuilderStyles=applyStyles;
})();

/* v3.1.23 admin UX helpers */
(function(){
function activateWpstTab(name){
  const tab=document.querySelector('.wpst-tab[data-tab="'+name+'"]');
  const panel=document.querySelector('.wpst-panel[data-panel="'+name+'"]');
  if(!tab||!panel)return;
  document.querySelectorAll('.wpst-tab').forEach(x=>x.classList.remove('is-active'));
  document.querySelectorAll('.wpst-panel').forEach(x=>x.classList.remove('is-active'));
  tab.classList.add('is-active');panel.classList.add('is-active');
  try{localStorage.setItem('wpst-active-tab',name)}catch(e){}
}
document.addEventListener('DOMContentLoaded',function(){
  const form=document.getElementById('wpst-settings-form');
  const saved=(()=>{try{return localStorage.getItem('wpst-active-tab')}catch(e){return null}})();
  if(saved && document.querySelector('.wpst-tab[data-tab="'+saved+'"]')) activateWpstTab(saved);

  document.querySelectorAll('[data-open-tab]').forEach(btn=>{
    btn.addEventListener('click',function(){
      const name=this.getAttribute('data-open-tab');
      activateWpstTab(name);
      const tabs=document.querySelector('.wpst-tabs');
      if(tabs)tabs.scrollIntoView({behavior:'smooth',block:'start'});
    });
  });

  document.querySelectorAll('.wpst-tab').forEach(tab=>{
    tab.addEventListener('click',()=>{try{localStorage.setItem('wpst-active-tab',tab.dataset.tab)}catch(e){}});
  });

  if(form){
    let initial=new FormData(form);
    let initialString=new URLSearchParams(initial).toString();
    const savebar=form.querySelector('.wpst-savebar');
    function checkDirty(){
      const current=new URLSearchParams(new FormData(form)).toString();
      if(savebar)savebar.classList.toggle('is-dirty',current!==initialString);
    }
    form.addEventListener('input',checkDirty);
    form.addEventListener('change',checkDirty);
    form.addEventListener('submit',()=>{if(savebar)savebar.classList.remove('is-dirty')});
  }
});
})();

/* v3.1.31 Builder workspace UX */
document.addEventListener('DOMContentLoaded',function(){
  document.querySelectorAll('[data-wpst-block-search]').forEach(input=>{
    input.addEventListener('input',function(){
      const q=(this.value||'').toLocaleLowerCase('tr-TR').trim();
      const palette=this.closest('.wpst-palette');
      if(!palette)return;
      palette.querySelectorAll('.wpst-block-add').forEach(btn=>{
        const label=(btn.textContent||'').toLocaleLowerCase('tr-TR');
        btn.style.display=!q||label.includes(q)?'flex':'none';
      });
    });
  });

  document.querySelectorAll('.wpst-builder-grid').forEach(grid=>{
    const design=grid.querySelector('.wpst-design');
    const workspace=grid.querySelector('.wpst-workspace');
    if(!design||!workspace)return;
    const observer=new MutationObserver(()=>{
      const active=design.querySelector('.wpst-inspector-tab.is-active');
      workspace.classList.toggle('has-element-edit',!!active);
    });
    observer.observe(design,{subtree:true,attributes:true,attributeFilter:['class']});
  });
});

/* v3.1.32 Builder drag/drop usability */
document.addEventListener('DOMContentLoaded',function(){
  document.querySelectorAll('.wpst-builder-grid').forEach(grid=>{
    const palette=grid.querySelector('.wpst-palette');
    const canvas=grid.querySelector('.wpst-canvas');
    if(!palette||!canvas)return;

    palette.addEventListener('dragstart',()=>canvas.classList.add('is-wpst-drop-ready'));
    palette.addEventListener('dragend',()=>canvas.classList.remove('is-wpst-drop-ready'));

    canvas.addEventListener('dragenter',()=>canvas.classList.add('is-wpst-drop-active'));
    canvas.addEventListener('dragleave',e=>{
      if(!canvas.contains(e.relatedTarget))canvas.classList.remove('is-wpst-drop-active');
    });
    canvas.addEventListener('drop',()=>{
      canvas.classList.remove('is-wpst-drop-active','is-wpst-drop-ready');
    });
  });
});

/* WPSoft Design System 2.0 presets + live preview */
(function(){
  document.addEventListener('DOMContentLoaded',function(){
    const root=document.querySelector('.wpst-global-v2');
    if(!root)return;

    const presets={
      modern:{
        global_primary:'#2563eb',global_secondary:'#7c3aed',global_accent:'#0ea5e9',global_heading:'#0f172a',global_text:'#334155',global_muted:'#64748b',global_surface:'#ffffff',global_page_bg:'#ffffff',global_surface_alt:'#f8fafc',global_surface_dark:'#0f172a',global_soft:'#f8fafc',global_border:'#e2e8f0',
        global_button_bg:'#2563eb',global_button_text:'#ffffff',global_button_hover_bg:'#1d4ed8',global_button_hover_text:'#ffffff',global_secondary_button_bg:'#ffffff',global_secondary_button_text:'#0f172a',global_secondary_button_border:'#cbd5e1',
        global_body_font:'dmsans',global_heading_font:'plusjakarta',global_heading_weight:800,global_heading_letter_spacing:-0.02,global_radius_sm:8,global_radius_md:14,global_radius_lg:20,global_radius_xl:30,global_card_radius:20,global_button_radius:12,global_shadow:'soft',global_motion:'normal',global_gap:24,global_section_space:80,global_section_space_tablet:60,global_section_space_mobile:40,global_space_xs:8,global_space_sm:12,global_space_md:20,global_space_lg:32,global_space_xl:48,global_space_xxl:72,global_button_height:48,global_button_padding_x:24
      },
      minimal:{
        global_primary:'#111827',global_secondary:'#64748b',global_accent:'#111827',global_heading:'#111827',global_text:'#374151',global_muted:'#6b7280',global_surface:'#ffffff',global_page_bg:'#ffffff',global_surface_alt:'#fafafa',global_surface_dark:'#111827',global_soft:'#fafafa',global_border:'#e5e7eb',
        global_button_bg:'#111827',global_button_text:'#ffffff',global_button_hover_bg:'#000000',global_button_hover_text:'#ffffff',global_secondary_button_bg:'#ffffff',global_secondary_button_text:'#111827',global_secondary_button_border:'#d1d5db',
        global_body_font:'figtree',global_heading_font:'figtree',global_heading_weight:700,global_heading_letter_spacing:-0.025,global_radius_sm:4,global_radius_md:8,global_radius_lg:12,global_radius_xl:18,global_card_radius:10,global_button_radius:7,global_shadow:'none',global_motion:'soft',global_gap:18,global_section_space:68,global_section_space_tablet:50,global_section_space_mobile:34,global_space_xs:6,global_space_sm:10,global_space_md:16,global_space_lg:26,global_space_xl:40,global_space_xxl:60,global_button_height:44,global_button_padding_x:20
      },
      corporate:{
        global_primary:'#1d4ed8',global_secondary:'#0f766e',global_accent:'#0284c7',global_heading:'#0f172a',global_text:'#334155',global_muted:'#64748b',global_surface:'#ffffff',global_page_bg:'#ffffff',global_surface_alt:'#f1f5f9',global_surface_dark:'#0f172a',global_soft:'#f1f5f9',global_border:'#cbd5e1',
        global_button_bg:'#1d4ed8',global_button_text:'#ffffff',global_button_hover_bg:'#1e40af',global_button_hover_text:'#ffffff',global_secondary_button_bg:'#ffffff',global_secondary_button_text:'#1e3a8a',global_secondary_button_border:'#94a3b8',
        global_body_font:'sourcesans3',global_heading_font:'manrope',global_heading_weight:800,global_heading_letter_spacing:-0.02,global_radius_sm:6,global_radius_md:10,global_radius_lg:16,global_radius_xl:22,global_card_radius:16,global_button_radius:8,global_shadow:'soft',global_motion:'normal',global_gap:22,global_section_space:76,global_section_space_tablet:56,global_section_space_mobile:38,global_space_xs:8,global_space_sm:12,global_space_md:18,global_space_lg:28,global_space_xl:44,global_space_xxl:68,global_button_height:48,global_button_padding_x:24
      },
      creative:{
        global_primary:'#7c3aed',global_secondary:'#db2777',global_accent:'#06b6d4',global_heading:'#18181b',global_text:'#3f3f46',global_muted:'#71717a',global_surface:'#ffffff',global_page_bg:'#ffffff',global_surface_alt:'#faf5ff',global_surface_dark:'#18181b',global_soft:'#faf5ff',global_border:'#e9d5ff',
        global_button_bg:'#7c3aed',global_button_text:'#ffffff',global_button_hover_bg:'#6d28d9',global_button_hover_text:'#ffffff',global_secondary_button_bg:'#fdf4ff',global_secondary_button_text:'#86198f',global_secondary_button_border:'#f0abfc',
        global_body_font:'dmsans',global_heading_font:'sora',global_heading_weight:800,global_heading_letter_spacing:-0.035,global_radius_sm:10,global_radius_md:18,global_radius_lg:28,global_radius_xl:40,global_card_radius:28,global_button_radius:16,global_shadow:'medium',global_motion:'dynamic',global_gap:28,global_section_space:92,global_section_space_tablet:68,global_section_space_mobile:46,global_space_xs:10,global_space_sm:14,global_space_md:22,global_space_lg:36,global_space_xl:56,global_space_xxl:84,global_button_height:52,global_button_padding_x:28
      },
      luxury:{
        global_primary:'#a16207',global_secondary:'#292524',global_accent:'#ca8a04',global_heading:'#1c1917',global_text:'#44403c',global_muted:'#78716c',global_surface:'#fffdf8',global_page_bg:'#fffdf8',global_surface_alt:'#fafaf9',global_surface_dark:'#292524',global_soft:'#fafaf9',global_border:'#e7e5e4',
        global_button_bg:'#292524',global_button_text:'#fffdf8',global_button_hover_bg:'#a16207',global_button_hover_text:'#ffffff',global_secondary_button_bg:'#fffdf8',global_secondary_button_text:'#292524',global_secondary_button_border:'#a8a29e',
        global_body_font:'manrope',global_heading_font:'playfair',global_heading_weight:700,global_heading_letter_spacing:-0.01,global_radius_sm:2,global_radius_md:6,global_radius_lg:12,global_radius_xl:18,global_card_radius:12,global_button_radius:2,global_shadow:'soft',global_motion:'soft',global_gap:26,global_section_space:94,global_section_space_tablet:70,global_section_space_mobile:46,global_space_xs:8,global_space_sm:12,global_space_md:20,global_space_lg:34,global_space_xl:54,global_space_xxl:86,global_button_height:48,global_button_padding_x:28
      },
      dark:{
        global_primary:'#38bdf8',global_secondary:'#8b5cf6',global_accent:'#22d3ee',global_heading:'#f8fafc',global_text:'#cbd5e1',global_muted:'#94a3b8',global_surface:'#0f172a',global_page_bg:'#020617',global_surface_alt:'#111827',global_surface_dark:'#020617',global_soft:'#111827',global_border:'#334155',
        global_button_bg:'#38bdf8',global_button_text:'#082f49',global_button_hover_bg:'#7dd3fc',global_button_hover_text:'#082f49',global_secondary_button_bg:'#111827',global_secondary_button_text:'#f8fafc',global_secondary_button_border:'#475569',
        global_body_font:'inter',global_heading_font:'spacegrotesk',global_heading_weight:800,global_heading_letter_spacing:-0.03,global_radius_sm:8,global_radius_md:14,global_radius_lg:22,global_radius_xl:32,global_card_radius:22,global_button_radius:12,global_shadow:'strong',global_motion:'normal',global_gap:24,global_section_space:84,global_section_space_tablet:62,global_section_space_mobile:42,global_space_xs:8,global_space_sm:12,global_space_md:20,global_space_lg:32,global_space_xl:50,global_space_xxl:76,global_button_height:48,global_button_padding_x:24
      }
    };

    function field(key){return root.querySelector('[data-design-field="'+key+'"]')}
    let applyingPreset=false;
    function setField(key,value){
      const el=field(key);if(!el)return;
      if(el.type==='checkbox')el.checked=!!value;
      else el.value=value;
      el.dispatchEvent(new Event('input',{bubbles:true}));
      el.dispatchEvent(new Event('change',{bubbles:true}));
    }
    function markCustom(){
      if(applyingPreset)return;
      const hidden=root.querySelector('[data-global-preset-input]');
      if(hidden)hidden.value='custom';
      root.querySelectorAll('[data-design-preset]').forEach(x=>x.classList.remove('is-active'));
    }
    root.querySelectorAll('[data-design-preset]').forEach(btn=>btn.addEventListener('click',function(){
      const name=this.dataset.designPreset,data=presets[name];if(!data)return;
      applyingPreset=true;
      Object.keys(data).forEach(k=>setField(k,data[k]));
      applyingPreset=false;
      const hidden=root.querySelector('[data-global-preset-input]');if(hidden)hidden.value=name;
      root.querySelectorAll('[data-design-preset]').forEach(x=>x.classList.toggle('is-active',x===this));
      updatePreview();
    }));

    root.querySelectorAll('[data-design-field]').forEach(el=>{
      el.addEventListener('input',function(){markCustom();updatePreview()});
      el.addEventListener('change',updatePreview);
    });

    root.querySelectorAll('input[type=color][data-design-field]').forEach(el=>el.addEventListener('input',function(){
      const code=this.closest('.wpst-global-color')?.querySelector('code');if(code)code.textContent=this.value;
    }));

    function val(key,fallback){const el=field(key);return el&&el.value!==''?el.value:fallback}
    function updatePreview(){
      const preview=root.querySelector('[data-design-preview]');if(!preview)return;
      preview.style.setProperty('--ds-primary',val('global_primary','#2563eb'));
      preview.style.setProperty('--ds-secondary',val('global_secondary','#7c3aed'));
      preview.style.setProperty('--ds-heading',val('global_heading','#0f172a'));
      preview.style.setProperty('--ds-text',val('global_text','#334155'));
      preview.style.setProperty('--ds-muted',val('global_muted','#64748b'));
      preview.style.setProperty('--ds-page-bg',val('global_page_bg','#fff'));
      preview.style.setProperty('--ds-surface',val('global_surface','#fff'));
      preview.style.setProperty('--ds-soft',val('global_soft','#f8fafc'));
      preview.style.background=val('global_page_bg','#fff');
      preview.style.setProperty('--ds-border',val('global_border','#e2e8f0'));
      preview.style.setProperty('--ds-button',val('global_button_bg','#2563eb'));
      preview.style.setProperty('--ds-button-text',val('global_button_text','#fff'));
      preview.style.setProperty('--ds-secondary-button',val('global_secondary_button_bg','#fff'));
      preview.style.setProperty('--ds-secondary-text',val('global_secondary_button_text','#0f172a'));
      preview.style.setProperty('--ds-secondary-border',val('global_secondary_button_border','#cbd5e1'));
      preview.style.setProperty('--ds-radius',val('global_card_radius','20')+'px');
      preview.style.setProperty('--ds-button-radius',val('global_button_radius','12')+'px');
      preview.style.setProperty('--ds-h3',Math.min(42,Number(val('global_h2_size','42')))+'px');

      root.querySelectorAll('[data-radius-preview]').forEach(box=>{
        const input=field(box.dataset.radiusPreview);if(input)box.style.borderRadius=input.value+'px';
      });
    }
    updatePreview();
  });
})();

/* v3.1.48 Header Builder 3.0 presets */
(function(){
 document.addEventListener('DOMContentLoaded',function(){
  const root=document.querySelector('.wpst-live-header-settings');if(!root)return;
  const presets={
   corporate:{header_desktop_height:76,header_scrolled_height:62,header_menu_gap:27,header_menu_hover:'underline',header_transparent_text_color:'#ffffff',header_scrolled_background:'#ffffff',header_scrolled_text_color:'#0f172a'},
   saas:{header_desktop_height:74,header_scrolled_height:62,header_menu_gap:22,header_menu_hover:'pill',header_transparent_text_color:'#ffffff',header_scrolled_background:'#ffffff',header_scrolled_text_color:'#111827'},
   luxury:{header_desktop_height:86,header_scrolled_height:68,header_menu_gap:34,header_menu_hover:'fade',header_transparent_text_color:'#ffffff',header_scrolled_background:'#fffdf8',header_scrolled_text_color:'#292524'},
   ecommerce:{header_desktop_height:72,header_scrolled_height:60,header_menu_gap:21,header_menu_hover:'pill',header_transparent_text_color:'#ffffff',header_scrolled_background:'#ffffff',header_scrolled_text_color:'#111827'},
   hotel:{header_desktop_height:84,header_scrolled_height:64,header_menu_gap:30,header_menu_hover:'underline',header_transparent_text_color:'#ffffff',header_scrolled_background:'#ffffff',header_scrolled_text_color:'#123c37'},
   creative:{header_desktop_height:82,header_scrolled_height:64,header_menu_gap:32,header_menu_hover:'slide',header_transparent_text_color:'#ffffff',header_scrolled_background:'#ffffff',header_scrolled_text_color:'#18181b'}
  };
  function field(k){return root.querySelector('[data-header-v3-field="'+k+'"]')}
  root.querySelectorAll('[data-header-preset]').forEach(btn=>btn.addEventListener('click',function(){
   const name=this.dataset.headerPreset,data=presets[name];if(!data)return;
   Object.keys(data).forEach(k=>{const el=field(k);if(el){el.value=data[k];el.dispatchEvent(new Event('input',{bubbles:true}));el.dispatchEvent(new Event('change',{bubbles:true}))}});
   const hidden=root.querySelector('[data-header-preset-input]');if(hidden)hidden.value=name;
   root.querySelectorAll('[data-header-preset]').forEach(x=>x.classList.toggle('is-active',x===this));
  }));
  root.querySelectorAll('[data-header-v3-field]').forEach(el=>el.addEventListener('input',function(){
   const hidden=root.querySelector('[data-header-preset-input]');if(hidden)hidden.value='custom';
   root.querySelectorAll('[data-header-preset]').forEach(x=>x.classList.remove('is-active'));
  }));
 });

/* v3.2.44 Header Builder 3.0 toolbar */
document.querySelectorAll('[data-panel="header"]').forEach(function(p){
  p.querySelectorAll('[data-hb-device]').forEach(function(btn){
    btn.addEventListener('click',function(){
      p.querySelectorAll('[data-hb-device]').forEach(function(x){x.classList.remove('is-active')});
      btn.classList.add('is-active');
      var dev=btn.dataset.hbDevice;
      p.classList.toggle('wpst-hb-preview-tablet',dev==='tablet');
      p.classList.toggle('wpst-hb-preview-mobile',dev==='mobile');
      var legacy=p.querySelector('[data-device="'+dev+'"]');
      if(legacy) legacy.click();
    });
  });
  p.querySelectorAll('[data-hb-state]').forEach(function(btn){
    btn.addEventListener('click',function(){
      p.querySelectorAll('[data-hb-state]').forEach(function(x){x.classList.remove('is-active')});
      btn.classList.add('is-active');
      var state=btn.dataset.hbState;
      p.classList.toggle('wpst-hb-state-transparent',state==='transparent');
      p.classList.toggle('wpst-hb-state-sticky',state==='sticky');
      /* Preview state buttons are visual-only.
         They must never change the saved Transparent/Sticky settings. */
      var frame=p.querySelector('.wpst-device-frame');
      if(frame){
        frame.classList.toggle('is-preview-transparent-state',state==='transparent');
        frame.classList.toggle('is-preview-sticky-state',state==='sticky');
      }
    });
  });
});


/* v3.2.44 Header Builder 3.1 */
document.querySelectorAll('[data-panel="header"]').forEach(function(p){
  var focus=p.querySelector('[data-hb-focus]');
  if(focus){
    focus.addEventListener('click',function(){
      p.classList.toggle('wpst-hb-focus');
      var on=p.classList.contains('wpst-hb-focus');
      focus.innerHTML='<span class="dashicons dashicons-'+(on?'editor-contract':'editor-expand')+'"></span>'+(on?'Panelleri Göster':'Önizlemeyi Büyüt');
    });
  }
  var quickPresets={
    minimal:{
      header_preset:'minimal',header_desktop_height:70,header_scrolled_height:58,header_menu_gap:22,
      header_menu_hover:'fade',header_menu_active:'none',header_layout_style:'normal',
      header_transparent:false,header_transparent_overlay:false,header_glass_style:'off',
      header_scrolled_background:'#ffffff',header_scrolled_text_color:'#0f172a'
    },
    corporate:{
      header_preset:'corporate',header_desktop_height:78,header_scrolled_height:64,header_menu_gap:28,
      header_menu_hover:'shadow',header_menu_active:'shadow',header_layout_style:'normal',
      header_transparent:false,header_transparent_overlay:false,header_glass_style:'off',
      header_scrolled_background:'#ffffff',header_scrolled_text_color:'#0f172a'
    },
    centered:{
      header_preset:'centered',header_desktop_height:80,header_scrolled_height:64,header_menu_gap:30,
      header_menu_hover:'fade',header_menu_active:'shadow',header_layout_style:'normal',
      header_transparent:false,header_transparent_overlay:false,header_glass_style:'off',
      header_scrolled_background:'#ffffff',header_scrolled_text_color:'#111827'
    },
    transparent:{
      header_preset:'transparent',header_desktop_height:82,header_scrolled_height:64,header_menu_gap:30,
      header_menu_hover:'fade',header_menu_active:'shadow',header_layout_style:'normal',
      header_transparent:true,header_transparent_overlay:true,header_glass_style:'off',
      header_transparent_text_color:'#ffffff',header_scrolled_background:'#ffffff',header_scrolled_text_color:'#0f172a'
    },
    'floating-boxed':{
      /* Do not set header_boxed_mobile here. The user's
         "Mobilde de Floating / Boxed kullan" preference must be preserved. */
      header_preset:'floating-boxed',header_desktop_height:76,header_scrolled_height:64,header_menu_gap:26,
      header_menu_hover:'shadow',header_menu_active:'shadow',header_layout_style:'boxed',
      header_transparent:false,header_transparent_overlay:false,header_glass_style:'off',header_shadow_style:'normal',
      header_scrolled_background:'#ffffff',header_scrolled_text_color:'#0f172a'
    }
  };

  function qField(name){return p.querySelector('[name="wpst_settings['+name+']"]')}
  function qSet(name,value){
    var fields=p.querySelectorAll('[name="wpst_settings['+name+']"]');
    if(!fields.length)return;
    fields.forEach(function(el){
      if(el.type==='checkbox'){
        el.checked=!!value;
        el.dispatchEvent(new Event('change',{bubbles:true}));
        return;
      }
      if(el.type==='radio'){
        var shouldCheck=el.value===String(value);
        el.checked=shouldCheck;
        if(shouldCheck)el.dispatchEvent(new Event('change',{bubbles:true}));
        return;
      }
      el.value=String(value);
      el.dispatchEvent(new Event('input',{bubbles:true}));
      el.dispatchEvent(new Event('change',{bubbles:true}));
    });
  }

  function qSyncLayoutUi(){
    var selected=p.querySelector('[name="wpst_settings[header_layout_style]"]:checked');
    var boxed=!!selected&&selected.value==='boxed';
    var boxedPanel=p.querySelector('[data-boxed-only]');
    if(boxedPanel){
      boxedPanel.hidden=!boxed;
      boxedPanel.classList.toggle('is-active',boxed);
    }
    p.querySelectorAll('.wpst-header-layout-choice .wpst-layout-choice').forEach(function(label){
      var input=label.querySelector('[data-header-layout-choice]');
      label.classList.toggle('is-active',!!input&&input.checked);
    });
    var boxedMobile=qField('header_boxed_mobile');
    p.classList.toggle('wpst-hb-boxed-mobile-enabled',!!boxedMobile&&boxedMobile.checked);
  }

  var quickSnapshot={};
  [
    'header_preset','header_desktop_height','header_scrolled_height','header_menu_gap',
    'header_menu_hover','header_menu_active','header_layout_style','header_transparent',
    'header_transparent_overlay','header_glass_style','header_transparent_text_color',
    'header_scrolled_background','header_scrolled_text_color','header_shadow_style'
  ].forEach(function(name){
    var el=qField(name);
    if(!el)return;
    if(el.type==='checkbox')quickSnapshot[name]=el.checked;
    else if(el.type==='radio'){
      var checked=p.querySelector('[name="wpst_settings['+name+']"]:checked');
      quickSnapshot[name]=checked?checked.value:'';
    }else quickSnapshot[name]=el.value;
  });

  function qApply(key){
    var data=key==='current'?quickSnapshot:quickPresets[key];
    if(!data)return;
    Object.keys(data).forEach(function(name){qSet(name,data[name])});

    var hidden=p.querySelector('[data-header-preset-input]');
    if(hidden)hidden.value=key==='current'?(quickSnapshot.header_preset||'custom'):(data.header_preset||key);

    p.querySelectorAll('[data-hb-preset]').forEach(function(x){x.classList.toggle('is-active',x.dataset.hbPreset===key)});
    ['minimal','corporate','centered','transparent','floating-boxed'].forEach(function(k){p.classList.toggle('wpst-hb-preset-'+k,key===k)});

    var stateKey=(key==='transparent'||!!data.header_transparent)?'transparent':'normal';
    p.querySelectorAll('[data-hb-state]').forEach(function(x){x.classList.toggle('is-active',x.dataset.hbState===stateKey)});
    p.classList.toggle('wpst-hb-state-transparent',stateKey==='transparent');

    /* Keep the Quick Start preset, Inspector controls and live preview in one state. */
    qSyncLayoutUi();
    if(window.WPSTApplyBuilderStyles)window.WPSTApplyBuilderStyles('header');

    var saveState=p.querySelector('[data-wpst-save-state]');
    if(saveState){
      saveState.classList.add('is-dirty');
      var b=saveState.querySelector('b');if(b)b.textContent='Kaydedilmedi';
    }
  }

  p.querySelectorAll('[data-hb-preset]').forEach(function(btn){
    btn.addEventListener('click',function(){qApply(btn.dataset.hbPreset)});
  });
});


/* v3.2.44 Mobile Header Builder */
document.querySelectorAll('[data-panel="header"]').forEach(function(p){
  var mobileBuilder=p.querySelector('.wpst-mobile-builder-v3');
  if(!mobileBuilder)return;

  var tabs=mobileBuilder.querySelectorAll('[data-mobile-builder-tab]');
  var panes=mobileBuilder.querySelectorAll('[data-mobile-builder-pane]');
  tabs.forEach(function(btn){
    btn.addEventListener('click',function(){
      var key=btn.dataset.mobileBuilderTab;
      tabs.forEach(function(x){x.classList.toggle('is-active',x===btn)});
      panes.forEach(function(x){x.classList.toggle('is-active',x.dataset.mobileBuilderPane===key)});
    });
  });

  var presetSelect=mobileBuilder.querySelector('[name="wpst_settings[header_mobile_preset]"]');
  mobileBuilder.querySelectorAll('[data-mobile-preset]').forEach(function(btn){
    btn.addEventListener('click',function(){
      mobileBuilder.querySelectorAll('[data-mobile-preset]').forEach(function(x){x.classList.toggle('is-active',x===btn)});
      if(presetSelect){
        presetSelect.value=btn.dataset.mobilePreset;
        presetSelect.dispatchEvent(new Event('change',{bubbles:true}));
      }
    });
  });

  function openMobilePreview(openDrawer){
    var mobile=p.querySelector('[data-hb-device="mobile"]');
    if(mobile)mobile.click();
    if(mobileBuilder && !mobileBuilder.open)mobileBuilder.open=true;
    setTimeout(function(){
      var frame=p.querySelector('.wpst-device-frame');
      if(frame && openDrawer)frame.classList.add('is-menu-open');
      if(window.WPSTApplyBuilderStyles)window.WPSTApplyBuilderStyles('header');
      var workspace=p.querySelector('.wpst-workspace');
      if(workspace && workspace.scrollIntoView)workspace.scrollIntoView({behavior:'smooth',block:'start'});
    },60);
  }
  var previewBtn=mobileBuilder.querySelector('[data-wpst-open-mobile-preview]');
  if(previewBtn)previewBtn.addEventListener('click',function(){openMobilePreview(false)});
  var testDrawer=mobileBuilder.querySelector('[data-wpst-test-drawer]');
  if(testDrawer)testDrawer.addEventListener('click',function(){openMobilePreview(true)});

  p.querySelectorAll('[data-hb-device]').forEach(function(btn){
    btn.addEventListener('click',function(){
      if(btn.dataset.hbDevice==='mobile'){
        p.classList.add('wpst-mobile-edit-mode');
      }else{
        p.classList.remove('wpst-mobile-edit-mode');
      }
    });
  });

  mobileBuilder.querySelectorAll('input,select').forEach(function(field){
    field.addEventListener('input',function(){if(window.WPSTApplyBuilderStyles)window.WPSTApplyBuilderStyles('header')});
    field.addEventListener('change',function(){if(window.WPSTApplyBuilderStyles)window.WPSTApplyBuilderStyles('header')});
  });
});


/* v3.2.44 Footer Builder 3.0 */
document.querySelectorAll('[data-panel="footer"]').forEach(function(p){
  p.querySelectorAll('[data-fb-device]').forEach(function(btn){
    btn.addEventListener('click',function(){
      p.querySelectorAll('[data-fb-device]').forEach(function(x){x.classList.remove('is-active')});
      btn.classList.add('is-active');
      var dev=btn.dataset.fbDevice;
      p.classList.toggle('wpst-fb-preview-tablet',dev==='tablet');
      p.classList.toggle('wpst-fb-preview-mobile',dev==='mobile');
      var legacy=p.querySelector('[data-device="'+dev+'"]');
      if(legacy)legacy.click();
    });
  });

  var focus=p.querySelector('[data-fb-focus]');
  if(focus){
    focus.addEventListener('click',function(){
      p.classList.toggle('wpst-fb-focus');
      var on=p.classList.contains('wpst-fb-focus');
      focus.innerHTML='<span class="dashicons dashicons-'+(on?'editor-contract':'editor-expand')+'"></span>'+(on?'Panelleri Göster':'Önizlemeyi Büyüt');
    });
  }

  p.querySelectorAll('[data-fb-preset]').forEach(function(btn){
    btn.addEventListener('click',function(){
      p.querySelectorAll('[data-fb-preset]').forEach(function(x){x.classList.remove('is-active')});
      btn.classList.add('is-active');
      ['corporate','minimal','dark','glass'].forEach(function(k){p.classList.remove('wpst-fb-preset-'+k)});
      var key=btn.dataset.fbPreset;
      if(key!=='current')p.classList.add('wpst-fb-preset-'+key);
    });
  });
});

})();


/* v3.2.44 — Live Builder UX 3.0 */
document.addEventListener('DOMContentLoaded',function(){
  document.querySelectorAll('.wpst-builder-grid').forEach(function(grid){
    const panel=grid.closest('.wpst-panel');
    const stage=grid.querySelector('.wpst-preview-stage');
    const frame=grid.querySelector('.wpst-device-frame');
    if(!panel||!stage||!frame)return;

    let zoom=1;
    const value=grid.querySelector('[data-wpst-zoom-value]');
    const updateZoom=()=>{
      frame.style.setProperty('--wpst-builder-zoom',String(zoom));
      stage.style.setProperty('--wpst-builder-zoom',String(zoom));
      if(value)value.textContent=Math.round(zoom*100)+'%';
    };
    const zin=grid.querySelector('[data-wpst-zoom-in]');
    const zout=grid.querySelector('[data-wpst-zoom-out]');
    if(zin)zin.addEventListener('click',function(){
      zoom=Math.min(1.25,Math.round((zoom+.1)*10)/10);updateZoom();
    });
    if(zout)zout.addEventListener('click',function(){
      zoom=Math.max(.6,Math.round((zoom-.1)*10)/10);updateZoom();
    });
    updateZoom();

    const focus=grid.querySelector('[data-wpst-focus-mode]');
    stage.addEventListener('dblclick',function(e){
      if(e.target.closest('button,a,input,select,textarea,.wpst-canvas-item'))return;
      if(focus)focus.click();
    });
    if(focus)focus.addEventListener('click',function(){
      const on=panel.classList.toggle('wpst-focus-mode');
      focus.classList.toggle('is-active',on);
      const b=focus.querySelector('b');if(b)b.textContent=on?'Daralt':'Genişlet';
      requestAnimationFrame(()=>stage.scrollTo({left:0,top:0,behavior:'smooth'}));
    });

    // ESC exits focus mode.
    document.addEventListener('keydown',function(e){
      if(e.key==='Escape'&&panel.classList.contains('wpst-focus-mode')){
        panel.classList.remove('wpst-focus-mode');
        if(focus){focus.classList.remove('is-active');const b=focus.querySelector('b');if(b)b.textContent='Genişlet';}
      }
    });

    // Automatically fit the preview a little better when switching device.
    grid.querySelectorAll('.wpst-device-switch button').forEach(function(btn){
      btn.addEventListener('click',function(){
        const device=btn.dataset.device||'desktop';
        if(device==='mobile')zoom=1;
        else if(device==='tablet')zoom=1;
        else zoom=1;
        updateZoom();
        requestAnimationFrame(function(){
          stage.scrollLeft=Math.max(0,(stage.scrollWidth-stage.clientWidth)/2);
        });
      });
    });
  });
});


/* v3.2.44 — Live Builder UX 3.1 */
document.addEventListener('DOMContentLoaded',function(){
  document.querySelectorAll('.wpst-builder-grid').forEach(function(grid){
    const panel=grid.closest('.wpst-panel');
    if(!panel)return;
    const type=panel.dataset.panel;
    const layoutInput=panel.querySelector('[data-layout-input="'+type+'"]');
    const canvas=grid.querySelector('.wpst-canvas');
    const stage=grid.querySelector('.wpst-preview-stage');
    const state=panel.querySelector('[data-wpst-save-state]');
    if(!layoutInput||!canvas)return;

    let history=[], historyIndex=-1, restoring=false;
    const snapshot=()=>layoutInput.value||'[]';
    const pushHistory=()=>{
      if(restoring)return;
      const value=snapshot();
      if(history[historyIndex]===value)return;
      history=history.slice(0,historyIndex+1);
      history.push(value);
      if(history.length>50)history.shift();
      historyIndex=history.length-1;
      updateHistoryButtons();
    };
    const updateHistoryButtons=()=>{
      const u=grid.querySelector('[data-wpst-undo]'),r=grid.querySelector('[data-wpst-redo]');
      if(u)u.disabled=historyIndex<=0;
      if(r)r.disabled=historyIndex<0||historyIndex>=history.length-1;
    };
    const restore=(index)=>{
      if(index<0||index>=history.length)return;
      restoring=true;historyIndex=index;layoutInput.value=history[index];
      layoutInput.dispatchEvent(new Event('input',{bubbles:true}));
      restoring=false;updateHistoryButtons();markDirty();
    };
    history=[snapshot()];historyIndex=0;updateHistoryButtons();

    const observer=new MutationObserver(function(){pushHistory()});
    observer.observe(layoutInput,{attributes:true,attributeFilter:['value']});
    layoutInput.addEventListener('input',function(){setTimeout(pushHistory,0)});

    const undo=grid.querySelector('[data-wpst-undo]'),redo=grid.querySelector('[data-wpst-redo]');
    if(undo)undo.addEventListener('click',()=>restore(historyIndex-1));
    if(redo)redo.addEventListener('click',()=>restore(historyIndex+1));

    const collapse=grid.querySelector('[data-wpst-panel-collapse]');
    if(collapse)collapse.addEventListener('click',function(){
      const on=grid.classList.toggle('is-controls-collapsed');
      collapse.title=on?'Kontrol Panelini Göster':'Kontrol Panelini Gizle';
    });

    const refresh=grid.querySelector('[data-wpst-preview-refresh]');
    if(refresh&&stage)refresh.addEventListener('click',function(){
      stage.classList.add('is-refreshing');
      setTimeout(function(){
        layoutInput.dispatchEvent(new Event('input',{bubbles:true}));
        stage.classList.remove('is-refreshing');
      },260);
    });

    const markDirty=()=>{
      if(!state)return;
      state.classList.add('is-dirty');
      state.classList.remove('is-saving');
      const b=state.querySelector('b');if(b)b.textContent='Kaydedilmedi';
    };

    panel.querySelectorAll('input,select,textarea').forEach(function(el){
      if(el.closest('.wpst-preview-actions'))return;
      el.addEventListener('input',markDirty);
      el.addEventListener('change',markDirty);
    });

    const form=panel.closest('form');
    if(form)form.addEventListener('submit',function(){
      if(!state)return;
      state.classList.remove('is-dirty');state.classList.add('is-saving');
      const b=state.querySelector('b');if(b)b.textContent='Kaydediliyor';
    });

    // Floating toolbar on selected canvas item.
    canvas.addEventListener('click',function(e){
      const item=e.target.closest('.wpst-canvas-item');
      if(!item)return;
      canvas.querySelectorAll('.wpst-canvas-item.is-selected').forEach(x=>x.classList.remove('is-selected'));
      canvas.querySelectorAll('.wpst-floating-toolbar').forEach(x=>x.remove());
      item.classList.add('is-selected');
      const tb=document.createElement('div');
      tb.className='wpst-floating-toolbar';
      tb.innerHTML=
        '<button type="button" data-float-edit title="Düzenle"><span class="dashicons dashicons-edit"></span></button>'+
        '<button type="button" data-float-duplicate title="Kopyala"><span class="dashicons dashicons-admin-page"></span></button>'+
        '<button type="button" data-float-move title="Taşı"><span class="dashicons dashicons-move"></span></button>'+
        '<button type="button" class="is-danger" data-float-delete title="Sil"><span class="dashicons dashicons-trash"></span></button>';
      item.appendChild(tb);

      const idx=parseInt(item.dataset.index||'-1',10);
      tb.querySelector('[data-float-edit]').addEventListener('click',function(ev){
        ev.stopPropagation();
        const settings=item.querySelector('.wpst-item-actions button');if(settings)settings.click();
      });
      tb.querySelector('[data-float-duplicate]').addEventListener('click',function(ev){
        ev.stopPropagation();
        try{
          const arr=JSON.parse(layoutInput.value||'[]');
          if(idx>=0&&arr[idx]){
            const copy=JSON.parse(JSON.stringify(arr[idx]));
            arr.splice(idx+1,0,copy);
            layoutInput.value=JSON.stringify(arr);
            layoutInput.dispatchEvent(new Event('input',{bubbles:true}));
            markDirty();
          }
        }catch(err){}
      });
      tb.querySelector('[data-float-move]').addEventListener('click',function(ev){
        ev.stopPropagation();item.scrollIntoView({behavior:'smooth',block:'center'});
      });
      tb.querySelector('[data-float-delete]').addEventListener('click',function(ev){
        ev.stopPropagation();
        try{
          const arr=JSON.parse(layoutInput.value||'[]');
          if(idx>=0&&arr[idx]){
            arr.splice(idx,1);
            layoutInput.value=JSON.stringify(arr);
            layoutInput.dispatchEvent(new Event('input',{bubbles:true}));
            markDirty();
          }
        }catch(err){}
      });
    });

    document.addEventListener('click',function(e){
      if(e.target.closest('.wpst-canvas-item'))return;
      canvas.querySelectorAll('.wpst-canvas-item.is-selected').forEach(x=>x.classList.remove('is-selected'));
      canvas.querySelectorAll('.wpst-floating-toolbar').forEach(x=>x.remove());
    });

    // Keyboard history shortcuts while builder is active.
    document.addEventListener('keydown',function(e){
      if(!panel.classList.contains('is-active'))return;
      const mod=e.ctrlKey||e.metaKey;
      if(mod&&e.key.toLowerCase()==='z'){
        e.preventDefault();
        if(e.shiftKey)restore(historyIndex+1);else restore(historyIndex-1);
      }else if(mod&&e.key.toLowerCase()==='y'){
        e.preventDefault();restore(historyIndex+1);
      }
    });
  });
});


/* v3.2.44 — Header/Footer Builder UX interactions */
document.addEventListener('DOMContentLoaded',function(){

  function activateInspector(design,target){
    if(!design)return;
    var btn=design.querySelector('[data-inspector="'+target+'"]');
    if(btn)btn.click();
  }

  document.querySelectorAll('.wpst-panel[data-panel="header"],.wpst-panel[data-panel="footer"]').forEach(function(panel){
    var palette=panel.querySelector('.wpst-palette');
    var design=panel.querySelector('.wpst-design');
    var canvas=panel.querySelector('.wpst-canvas');

    /* LEFT: Structure / Blocks tabs */
    if(palette){
      var paletteTabs=palette.querySelectorAll('[data-palette-tab]');
      var palettePanes=palette.querySelectorAll('[data-palette-pane]');
      paletteTabs.forEach(function(btn){
        btn.addEventListener('click',function(){
          var key=btn.dataset.paletteTab;
          paletteTabs.forEach(function(x){x.classList.toggle('is-active',x===btn);});
          palettePanes.forEach(function(x){x.classList.toggle('is-active',x.dataset.palettePane===key);});
        });
      });
    }

    /* Preview selection -> inspector */
    if(canvas){
      canvas.addEventListener('click',function(e){
        var item=e.target.closest('.wpst-canvas-item');
        var zone=e.target.closest('.wpst-drop-zone,.wpst-preview-zone,[data-zone]');
        var row=e.target.closest('.wpst-preview-header-row,.wpst-preview-footer-row,[data-row-key]');
        if(item){
          activateInspector(design,'element');
        }else if(zone || row){
          activateInspector(design,'section');
        }else{
          activateInspector(design,'general');
        }
      },true);
    }

    /* Extra inspector views: Behavior / Mobile.
       They do not move/remove settings; only jump to existing relevant groups. */
    if(design){
      design.querySelectorAll('[data-inspector-view]').forEach(function(btn){
        btn.addEventListener('click',function(){
          design.querySelectorAll('.wpst-inspector-tabs button').forEach(function(x){x.classList.remove('is-active');});
          btn.classList.add('is-active');

          var general=design.querySelector('[data-inspector-pane="general"]');
          design.querySelectorAll('[data-inspector-pane]').forEach(function(p){p.classList.remove('is-active');});
          if(general)general.classList.add('is-active');

          var selector=btn.dataset.inspectorView==='behavior'
            ? '.wpst-live-header-settings,.wpst-live-footer-settings'
            : '.wpst-live-settings-details,.wpst-logo-control,.wpst-mobile-social-settings,.wpst-scroll-logo-control';

          var target=general?general.querySelector(selector):null;
          if(target && target.scrollIntoView){
            target.scrollIntoView({behavior:'smooth',block:'start'});
            target.classList.add('wpst-inspector-flash');
            setTimeout(function(){target.classList.remove('wpst-inspector-flash');},1200);
          }
        });
      });
    }

    /* Drag/drop insertion indicator */
    panel.addEventListener('dragover',function(e){
      var zone=e.target.closest('.wpst-drop-zone,.wpst-preview-zone,[data-zone]');
      panel.querySelectorAll('.wpst-drop-active').forEach(function(x){if(x!==zone)x.classList.remove('wpst-drop-active');});
      if(zone)zone.classList.add('wpst-drop-active');
    });
    panel.addEventListener('dragleave',function(e){
      var zone=e.target.closest('.wpst-drop-zone,.wpst-preview-zone,[data-zone]');
      if(zone && !zone.contains(e.relatedTarget))zone.classList.remove('wpst-drop-active');
    });
    panel.addEventListener('drop',function(){
      panel.querySelectorAll('.wpst-drop-active').forEach(function(x){x.classList.remove('wpst-drop-active');});
    });
    panel.addEventListener('dragend',function(){
      panel.querySelectorAll('.wpst-drop-active').forEach(function(x){x.classList.remove('wpst-drop-active');});
    });

    /* Preview toolbar: move rare actions into More menu visually.
       Existing buttons keep their original handlers. */
    var actions=panel.querySelector('.wpst-preview-actions');
    if(actions && !actions.querySelector('.wpst-preview-more')){
      var more=document.createElement('div');
      more.className='wpst-preview-more';
      var toggle=document.createElement('button');
      toggle.type='button';
      toggle.className='wpst-preview-more-toggle';
      toggle.title='Diğer';
      toggle.innerHTML='<span class="dashicons dashicons-ellipsis"></span>';
      var menu=document.createElement('div');
      menu.className='wpst-preview-more-menu';

      ['[data-wpst-preview-refresh]','[data-wpst-panel-collapse]'].forEach(function(sel){
        var original=actions.querySelector(sel);
        if(original){
          var clone=original.cloneNode(true);
          clone.addEventListener('click',function(){original.click();more.classList.remove('is-open');});
          menu.appendChild(clone);
          original.classList.add('wpst-toolbar-secondary-action');
        }
      });

      more.appendChild(toggle);more.appendChild(menu);actions.appendChild(more);
      toggle.addEventListener('click',function(e){e.stopPropagation();more.classList.toggle('is-open');});
      document.addEventListener('click',function(){more.classList.remove('is-open');});
    }
  });
});



/* v3.2.44 — Builder stabilization */
document.addEventListener('DOMContentLoaded',function(){
  document.querySelectorAll('.wpst-panel[data-panel="header"],.wpst-panel[data-panel="footer"]').forEach(function(panel){
    panel.querySelectorAll('[data-palette-tab]').forEach(function(btn){
      btn.setAttribute('aria-selected',btn.classList.contains('is-active')?'true':'false');
      btn.addEventListener('click',function(){
        panel.querySelectorAll('[data-palette-tab]').forEach(function(x){
          x.setAttribute('aria-selected',x===btn?'true':'false');
        });
      });
    });

    panel.addEventListener('keydown',function(e){
      if(e.key==='Escape'){
        panel.querySelectorAll('.wpst-drop-active').forEach(function(x){x.classList.remove('wpst-drop-active');});
        panel.querySelectorAll('.wpst-floating-toolbar').forEach(function(x){x.remove();});
        panel.querySelectorAll('.wpst-canvas-item.is-selected').forEach(function(x){x.classList.remove('is-selected');});
      }
    });

    /* Keep mobile drawer closed when device changes away from mobile. */
    panel.querySelectorAll('[data-preview-device]').forEach(function(btn){
      btn.addEventListener('click',function(){
        if(btn.dataset.previewDevice!=='mobile'){
          panel.querySelectorAll('.wpst-device-frame.is-menu-open').forEach(function(frame){frame.classList.remove('is-menu-open');});
        }
      });
    });
  });
});


/* v3.3.18.21.6 · Floating / Boxed UI cleanup */
(function(){
  function initBoxedVisibility(scope){
    (scope||document).querySelectorAll('[data-panel="header"]').forEach(function(panel){
      var radios=panel.querySelectorAll('[data-header-layout-choice]');
      var boxed=panel.querySelector('[data-boxed-only]');
      if(!radios.length||!boxed)return;
      function sync(){
        var selected=panel.querySelector('[data-header-layout-choice]:checked');
        var on=!!selected&&selected.value==='boxed';
        boxed.hidden=!on;
        boxed.classList.toggle('is-active',on);
        panel.querySelectorAll('.wpst-header-layout-choice .wpst-layout-choice').forEach(function(label){
          var input=label.querySelector('[data-header-layout-choice]');
          label.classList.toggle('is-active',!!input&&input.checked);
        });
      }
      radios.forEach(function(r){r.addEventListener('change',sync);});
      sync();
    });
  }
  if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',function(){initBoxedVisibility(document);});
  else initBoxedVisibility(document);
})();


/* v3.3.18.21.7 · Header row enable/disable UI */
(function(){
  function initHeaderRows(root){
    (root||document).querySelectorAll('[data-panel="header"]').forEach(function(panel){
      panel.querySelectorAll('[data-header-row-enable]').forEach(function(toggle){
        var key=toggle.dataset.headerRowEnable;
        var card=panel.querySelector('[data-header-row-card="'+key+'"]');
        if(!card)return;
        var options=card.querySelector('[data-row-options]');
        var status=card.querySelector('[data-row-status]');
        var sticky=panel.querySelector('[name="wpst_settings[header_sticky_'+key+']"]');
        function sync(){
          var on=toggle.checked;
          if(options)options.hidden=!on;
          if(status){
            status.textContent=on?'Aktif':'Kapalı';
            status.classList.toggle('is-on',on);
            status.classList.toggle('is-off',!on);
          }
          card.classList.toggle('is-disabled',!on);
          if(sticky){
            sticky.disabled=!on;
            if(!on)sticky.checked=false;
          }
        }
        toggle.addEventListener('change',sync);
        sync();
      });
    });
  }
  if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',function(){initHeaderRows(document);});
  else initHeaderRows(document);
})();


/* v3.3.18.21.8 · remember Header Inspector accordion state per admin session */
(function(){
  var KEY='wpst_header_inspector_open_v3318218';
  function initInspectorState(){
    document.querySelectorAll('[data-panel="header"]').forEach(function(panel){
      var details=Array.from(panel.querySelectorAll(
        '.wpst-logo-responsive-settings, .wpst-header-row-settings .wpst-row-settings-card, .wpst-live-header-settings .wpst-live-settings-details'
      ));
      if(!details.length)return;

      var saved={};
      try{ saved=JSON.parse(sessionStorage.getItem(KEY)||'{}')||{}; }catch(e){ saved={}; }

      details.forEach(function(d,idx){
        var summary=d.querySelector(':scope > summary');
        if(!summary)return;
        var label=(summary.textContent||'').trim().replace(/\s+/g,' ');
        var id='d'+idx+'_'+label;
        if(Object.prototype.hasOwnProperty.call(saved,id)) d.open=!!saved[id];

        d.addEventListener('toggle',function(){
          saved[id]=d.open;
          try{ sessionStorage.setItem(KEY,JSON.stringify(saved)); }catch(e){}
        });
      });
    });
  }
  if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',initInspectorState);
  else initInspectorState();
})();


/* v3.3.1 · Inspector row appearance live binding
   Header/Footer row Görünüm controls must repaint preview immediately. */
(function(){
  function bindRowAppearance(panel,type){
    if(!panel)return;
    var selector = type==='header'
      ? '.wpst-header-row-settings input, .wpst-header-row-settings select'
      : '.wpst-footer-row-settings input, .wpst-footer-row-settings select';

    panel.querySelectorAll(selector).forEach(function(field){
      if(field.dataset.wpstRowAppearanceBound==='1')return;
      field.dataset.wpstRowAppearanceBound='1';

      function refresh(){
        if(window.WPSTApplyBuilderStyles){
          window.WPSTApplyBuilderStyles(type);
        }
      }
      field.addEventListener('input',refresh);
      field.addEventListener('change',refresh);
    });
  }

  function init(){
    bindRowAppearance(document.querySelector('[data-panel="header"]'),'header');
    bindRowAppearance(document.querySelector('[data-panel="footer"]'),'footer');
  }

  if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',init);
  else init();
})();
