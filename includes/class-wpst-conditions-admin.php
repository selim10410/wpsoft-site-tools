<?php
if ( ! defined( 'ABSPATH' ) ) exit;

final class WPST_Conditions_Admin {
    public static function init(){
        add_action('add_meta_boxes',array(__CLASS__,'add_box'));
        add_action('save_post_elementor_library',array(__CLASS__,'save'));
        add_action('wp_ajax_wpst_condition_search',array(__CLASS__,'ajax_search'));
    }

    public static function add_box(){
        add_meta_box('wpst-display-conditions','WPSoft · Display Conditions 2.2',array(__CLASS__,'render'),'elementor_library','side','default');
    }

    private static function searchable_types(){
        return array('page','post','portfolio','category','tag','author','portfolio_category','product','product_category','product_tag');
    }

    private static function split_ids($value){
        $ids=array_filter(array_map('absint',preg_split('/\s*,\s*/',(string)$value)));
        return array_values(array_unique($ids));
    }

    private static function entity_label($type,$id){
        $id=absint($id); if(!$id) return '';
        if(in_array($type,array('page','post','portfolio','product'),true)){
            $p=get_post($id); return $p ? get_the_title($p).' (#'.$id.')' : '#'.$id;
        }
        if(in_array($type,array('category','tag','portfolio_category','product_category','product_tag'),true)){
            $taxonomy=array('category'=>'category','tag'=>'post_tag','portfolio_category'=>(class_exists('WPST_Portfolio_Manager')?WPST_Portfolio_Manager::TAXONOMY:'wpst_portfolio_category'),'product_category'=>'product_cat','product_tag'=>'product_tag');
            $term=get_term($id,$taxonomy[$type]);
            return ($term && !is_wp_error($term)) ? $term->name.' (#'.$id.')' : '#'.$id;
        }
        if('author'===$type){
            $u=get_userdata($id); return $u ? $u->display_name.' (#'.$id.')' : '#'.$id;
        }
        return (string)$id;
    }

    private static function display_value($type,$value){
        if(!$value) return '';
        if('post_type'===$type){
            $obj=get_post_type_object(sanitize_key($value));
            return $obj && isset($obj->labels->singular_name) ? $obj->labels->singular_name : $value;
        }
        if(in_array($type,self::searchable_types(),true)){
            $labels=array(); foreach(self::split_ids($value) as $id) $labels[]=self::entity_label($type,$id);
            return implode(', ',$labels);
        }
        return $value;
    }


    private static function summarize_rules($rules,$groups_relation='or'){
        if(empty($rules)) return 'Koşul yok: şablon tüm uygun konumlarda kullanılabilir.';
        $opts=class_exists('WPST_Display_Conditions')?WPST_Display_Conditions::labels():array();
        $groups=array();
        foreach($rules as $r){
            $gid=isset($r['group'])?max(1,absint($r['group'])):1;
            if(!isset($groups[$gid])) $groups[$gid]=array();
            $label=isset($opts[$r['type']])?$opts[$r['type']]:$r['type'];
            $value=self::display_value($r['type'],isset($r['value'])?$r['value']:'');
            $text=(isset($r['mode'])&&'exclude'===$r['mode']?'Hariç: ':'').$label.($value?' → '.$value:'');
            $groups[$gid][]=$text;
        }
        $parts=array();
        foreach($groups as $gid=>$items) $parts[]='Grup '.$gid.': '.implode(' · ',$items);
        return implode(('and'===$groups_relation?' + ':' / '),$parts);
    }

    private static function template_scope($post_id){
        $hf=get_post_meta($post_id,'_wpst_hf_type',true);
        if($hf) return 'hf:'.$hf;
        $blog=get_post_meta($post_id,'_wpst_blog_template_type',true);
        if($blog) return 'blog:'.$blog;
        $et=get_post_meta($post_id,'_elementor_template_type',true);
        return 'elementor:'.($et?:'page');
    }

    private static function potential_conflicts($post_id,$rules){
        if(empty($rules)) return array();
        $scope=self::template_scope($post_id);
        $current=array();
        foreach($rules as $r){
            if(isset($r['mode'])&&'exclude'===$r['mode']) continue;
            $current[]=sanitize_key($r['type']).':'.sanitize_text_field(isset($r['value'])?$r['value']:'');
        }
        if(empty($current)) return array();
        $q=new WP_Query(array(
            'post_type'=>'elementor_library','post_status'=>array('publish','draft','private'),
            'posts_per_page'=>100,'post__not_in'=>array(absint($post_id)),
            'meta_query'=>array(array('key'=>'_wpst_display_conditions','compare'=>'EXISTS')),
            'fields'=>'ids','no_found_rows'=>true
        ));
        $out=array();
        foreach($q->posts as $other_id){
            if(self::template_scope($other_id)!==$scope) continue;
            $other=get_post_meta($other_id,'_wpst_display_conditions',true);
            if(!is_array($other)) continue;
            foreach($other as $r){
                if(isset($r['mode'])&&'exclude'===$r['mode']) continue;
                $sig=sanitize_key(isset($r['type'])?$r['type']:'').':'.sanitize_text_field(isset($r['value'])?$r['value']:'');
                if(in_array($sig,$current,true)){
                    $out[]=array('id'=>$other_id,'title'=>get_the_title($other_id),'priority'=>class_exists('WPST_Display_Conditions')?WPST_Display_Conditions::priority($other_id):10);
                    break;
                }
            }
        }
        return $out;
    }

    public static function render($post){
        wp_nonce_field('wpst_conditions_save','wpst_conditions_nonce');
        $search_nonce=wp_create_nonce('wpst_condition_search');
        $rules=get_post_meta($post->ID,'_wpst_display_conditions',true);
        if(!is_array($rules)) $rules=array();
        $rules=class_exists('WPST_Display_Conditions')?WPST_Display_Conditions::sanitize($rules):$rules;
        $opts=class_exists('WPST_Display_Conditions')?WPST_Display_Conditions::labels():array();
        $priority=(int)get_post_meta($post->ID,'_wpst_condition_priority',true);if(!$priority)$priority=10;
        $groups_relation=get_post_meta($post->ID,'_wpst_condition_groups_relation',true);$groups_relation=('and'===$groups_relation)?'and':'or';
        $post_types=get_post_types(array('public'=>true),'objects');
        $pages=get_pages(array(
            'post_status'=>array('publish','draft','private'),
            'sort_column'=>'menu_order,post_title',
            'sort_order'=>'ASC'
        ));

        echo '<div class="wpst-cond-box" data-search-nonce="'.esc_attr($search_nonce).'">';
        echo '<p><strong>Şablon nerede kullanılsın?</strong></p>';
        echo '<p class="description">Include konumu dahil eder, Exclude eşleşirse şablonu kapatır. Kuralları gruplara ayırıp AND / OR mantığı kurabilirsin.</p>';
        echo '<div style="display:grid;grid-template-columns:1fr 1.35fr;gap:6px">';
        echo '<select id="wpst-cond-mode"><option value="include">Include</option><option value="exclude">Exclude</option></select>';
        echo '<select id="wpst-cond-type">';
        foreach($opts as $k=>$v) echo '<option value="'.esc_attr($k).'">'.esc_html($v).'</option>';
        echo '</select></div>';
        echo '<div style="display:grid;grid-template-columns:.8fr 1.2fr;gap:6px;margin-top:6px">';
        echo '<label style="font-size:11px">Grup<select id="wpst-cond-group" style="width:100%">';
        for($g=1;$g<=6;$g++) echo '<option value="'.$g.'">Grup '.$g.'</option>';
        echo '</select></label>';
        echo '<label style="font-size:11px">Önceki kuralla<select id="wpst-cond-operator" style="width:100%"><option value="or">OR · veya</option><option value="and">AND · ve</option></select></label>';
        echo '</div>';
        echo '<label style="display:block;font-size:11px;margin-top:6px">Gruplar arası ilişki<select name="wpst_condition_groups_relation" id="wpst-cond-groups-relation" style="width:100%"><option value="or" '.selected($groups_relation,'or',false).'>OR · gruplardan biri</option><option value="and" '.selected($groups_relation,'and',false).'>AND · tüm gruplar</option></select></label>';

        echo '<div id="wpst-cond-value-wrap" style="margin-top:7px">';
        echo '<input type="text" id="wpst-cond-value" placeholder="Değer (gerekirse)" style="width:100%">';
        echo '<div id="wpst-cond-search-ui" style="display:none">';
        echo '<input type="search" id="wpst-cond-search" placeholder="Ara ve seç…" autocomplete="off" style="width:100%">';
        echo '<div id="wpst-cond-search-results" style="display:none;max-height:180px;overflow:auto;border:1px solid #dcdcde;border-top:0;background:#fff"></div>';
        echo '<div id="wpst-cond-selected" style="display:flex;flex-wrap:wrap;gap:5px;margin-top:6px"></div>';
        echo '</div>';
        echo '<div id="wpst-cond-pages-wrap" style="display:none">';
        echo '<label style="display:block;font-size:11px;font-weight:600;margin-bottom:4px">Sayfaları Seç</label>';
        echo '<div class="wpst-page-select" id="wpst-page-select">';
        echo '<button type="button" class="wpst-page-select-trigger" id="wpst-page-select-trigger"><span>Sayfa seç…</span><b>⌄</b></button>';
        echo '<div class="wpst-page-select-menu" id="wpst-page-select-menu">';
        foreach($pages as $page){
            $status=('publish'===$page->post_status)?'':' · '.ucfirst($page->post_status);
            $title=get_the_title($page)?:'(Başlıksız)';
            echo '<label class="wpst-page-select-option">';
            echo '<input type="checkbox" value="'.absint($page->ID).'" data-label="'.esc_attr($title).'">';
            echo '<span>'.esc_html($title).'<small>#'.absint($page->ID).esc_html($status).'</small></span>';
            echo '</label>';
        }
        echo '</div>';
        echo '</div>';
        echo '<div id="wpst-page-selected-summary" class="wpst-page-selected-summary"></div>';
        echo '</div>';
        echo '<select id="wpst-cond-post-type" style="display:none;width:100%">';
        foreach($post_types as $pt){
            if('attachment'===$pt->name) continue;
            echo '<option value="'.esc_attr($pt->name).'">'.esc_html($pt->labels->singular_name).'</option>';
        }
        echo '</select>';
        echo '</div>';

        echo '<button type="button" class="button button-primary" id="wpst-cond-add" style="margin-top:7px;width:100%">+ Koşul Ekle</button>';
        echo '<div id="wpst-cond-list">';
        foreach($rules as $i=>$r){
            $mode=isset($r['mode'])&&'exclude'===$r['mode']?'exclude':'include';
            $display=self::display_value($r['type'],isset($r['value'])?$r['value']:'');
            echo '<div class="wpst-cond-row" style="margin-top:8px;padding:9px;border:1px solid #e2e8f0;border-radius:9px;background:#fff">';
            echo '<button type="button" class="button-link-delete wpst-cond-remove" style="float:right;font-size:18px;line-height:1" aria-label="Koşulu kaldır">×</button>';
            echo '<div><b style="display:inline-block;color:'.('exclude'===$mode?'#dc2626':'#16a34a').';margin-bottom:2px">'.esc_html(strtoupper($mode)).'</b></div>';
            $group=isset($r['group'])?max(1,absint($r['group'])):1;
            $operator=(isset($r['operator'])&&'and'===$r['operator'])?'and':'or';
            echo '<div style="font-weight:600">'.esc_html(isset($opts[$r['type']])?$opts[$r['type']]:$r['type']).'</div>';
            echo '<div style="font-size:10px;color:#7c3aed;margin-top:2px">Grup '.absint($group).' · '.esc_html(strtoupper($operator)).'</div>';
            if($display) echo '<div style="font-size:11px;color:#64748b;margin-top:2px;word-break:break-word">'.esc_html($display).'</div>';
            echo '<input type="hidden" name="wpst_conditions['.$i.'][mode]" value="'.esc_attr($mode).'">';
            echo '<input type="hidden" name="wpst_conditions['.$i.'][type]" value="'.esc_attr($r['type']).'">';
            echo '<input type="hidden" name="wpst_conditions['.$i.'][value]" value="'.esc_attr(isset($r['value'])?$r['value']:'').'">';
            echo '<input type="hidden" name="wpst_conditions['.$i.'][group]" value="'.absint($group).'">';
            echo '<input type="hidden" name="wpst_conditions['.$i.'][operator]" value="'.esc_attr($operator).'">';
            echo '<div style="clear:both"></div></div>';
        }
        echo '</div>';
        echo '<p><label><strong>Öncelik</strong><br><input type="number" name="wpst_condition_priority" min="1" max="999" value="'.absint($priority).'" style="width:100%"></label><span class="description">Düşük sayı daha yüksek önceliktir.</span></p>';
        echo '<div class="wpst-cond-summary"><strong>Aktif kullanım özeti</strong><div>'.esc_html(self::summarize_rules($rules,$groups_relation)).'</div></div>';
        $conflicts=self::potential_conflicts($post->ID,$rules);
        if($conflicts){
            echo '<div class="wpst-cond-conflict"><strong>⚠ Olası şablon çakışması</strong>';
            foreach($conflicts as $c) echo '<div>'.esc_html($c['title']).' · öncelik '.absint($c['priority']).'</div>';
            echo '<small>Daha düşük öncelik numarası önce değerlendirilir. Koşulları daraltarak çakışmayı kaldırabilirsin.</small></div>';
        }
        echo '<p class="description"><b>Mantık:</b> Aynı gruptaki kurallar seçtiğin AND/OR operatörüyle, gruplar ise üstteki ilişkiyle değerlendirilir. Eşleşen herhangi bir Exclude her zaman şablonu kapatır.</p></div>';
        ?>
        <style>
        #wpst-cond-search-results button{display:block;width:100%;text-align:left;border:0;background:#fff;padding:7px 9px;cursor:pointer}
        #wpst-cond-search-results button:hover{background:#f1f5f9}
        .wpst-cond-chip{display:inline-flex;align-items:center;gap:4px;background:#eef2ff;color:#3730a3;border-radius:999px;padding:4px 7px;font-size:11px}
        .wpst-cond-chip button{border:0;background:transparent;cursor:pointer;color:inherit;padding:0;line-height:1}
        .wpst-cond-summary{margin-top:10px;padding:10px;border-radius:10px;background:#f8fafc;border:1px solid #e2e8f0;font-size:11px;color:#475569}
        .wpst-cond-summary strong{display:block;color:#0f172a;margin-bottom:4px}
        .wpst-cond-conflict{margin-top:8px;padding:10px;border-radius:10px;background:#fff7ed;border:1px solid #fed7aa;color:#9a3412;font-size:11px}
        .wpst-cond-conflict strong{display:block;margin-bottom:4px}
        .wpst-page-select{position:relative}
        .wpst-page-select-trigger{width:100%;min-height:40px;display:flex;align-items:center;justify-content:space-between;padding:8px 11px;border:1px solid #c3c4c7;border-radius:7px;background:#fff;color:#1d2327;cursor:pointer;text-align:left}
        .wpst-page-select-trigger:hover,.wpst-page-select.is-open .wpst-page-select-trigger{border-color:#2271b1;box-shadow:0 0 0 1px #2271b1}
        .wpst-page-select-trigger b{font-size:15px;transition:transform .18s ease}
        .wpst-page-select.is-open .wpst-page-select-trigger b{transform:rotate(180deg)}
        .wpst-page-select-menu{display:none;position:absolute;z-index:9999;left:0;right:0;top:calc(100% + 5px);max-height:260px;overflow:auto;padding:5px;border:1px solid #c3c4c7;border-radius:8px;background:#fff;box-shadow:0 14px 34px rgba(15,23,42,.15)}
        .wpst-page-select.is-open .wpst-page-select-menu{display:block}
        .wpst-page-select-option{display:flex;align-items:flex-start;gap:9px;padding:8px 7px;border-radius:6px;cursor:pointer}
        .wpst-page-select-option:hover{background:#f6f7f7}
        .wpst-page-select-option input{margin-top:2px}
        .wpst-page-select-option span{display:flex;flex-direction:column;font-size:12px;font-weight:600;color:#1d2327}
        .wpst-page-select-option small{font-size:10px;font-weight:400;color:#646970;margin-top:1px}
        .wpst-page-selected-summary{display:flex;flex-wrap:wrap;gap:5px;margin-top:7px}
        .wpst-page-chip{display:inline-flex;align-items:center;gap:5px;padding:4px 7px;border-radius:999px;background:#eef2ff;color:#3730a3;font-size:11px}
        .wpst-page-chip button{border:0;background:transparent;color:inherit;padding:0;cursor:pointer;line-height:1}
        </style>
        <script>
        document.addEventListener('DOMContentLoaded',function(){
          const box=document.querySelector('.wpst-cond-box'), add=document.getElementById('wpst-cond-add'), list=document.getElementById('wpst-cond-list');
          if(!box||!add||!list)return;
          const typeEl=document.getElementById('wpst-cond-type'), val=document.getElementById('wpst-cond-value'), searchUI=document.getElementById('wpst-cond-search-ui'), search=document.getElementById('wpst-cond-search'), results=document.getElementById('wpst-cond-search-results'), selected=document.getElementById('wpst-cond-selected'), pagesWrap=document.getElementById('wpst-cond-pages-wrap'), pageSelect=document.getElementById('wpst-page-select'), pageTrigger=document.getElementById('wpst-page-select-trigger'), pageMenu=document.getElementById('wpst-page-select-menu'), pageSummary=document.getElementById('wpst-page-selected-summary'), postType=document.getElementById('wpst-cond-post-type');
          const searchable=<?php echo wp_json_encode(self::searchable_types()); ?>;
          const nonce=box.dataset.searchNonce||'';
          let chosen=[]; let timer=null;
          const esc=s=>String(s).replace(/[&<>"']/g,m=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m]));
          const resetChosen=()=>{chosen=[];selected.innerHTML='';search.value='';results.style.display='none';};
          const renderChosen=()=>{selected.innerHTML=chosen.map(x=>'<span class="wpst-cond-chip" data-id="'+x.id+'">'+esc(x.text)+' <button type="button" aria-label="Kaldır">×</button></span>').join('');};
          function updateValueUI(){
            resetChosen(); val.value=''; val.style.display='none'; searchUI.style.display='none'; pagesWrap.style.display='none'; postType.style.display='none';
            const t=typeEl.value;
            if(t==='page'){
              pagesWrap.style.display='block';
            } else if(searchable.includes(t)) searchUI.style.display='block';
            else if(t==='post_type') postType.style.display='block';
            else if(['entire_site','home','blog','single_post','blog_archive','date_archive','search','404','shop','cart','checkout','my_account','logged_in','logged_out','mobile','desktop'].includes(t)){}
            else val.style.display='block';
          }
          typeEl.addEventListener('change',updateValueUI); updateValueUI();
          search.addEventListener('input',function(){
            clearTimeout(timer); const q=this.value.trim(); if(q.length<2){results.style.display='none';return;}
            timer=setTimeout(async()=>{
              const body=new URLSearchParams({action:'wpst_condition_search',nonce:nonce,type:typeEl.value,q:q});
              try{const r=await fetch(ajaxurl,{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded; charset=UTF-8'},body:body.toString()});const j=await r.json();
                if(!j.success||!Array.isArray(j.data)){results.style.display='none';return;}
                results.innerHTML=j.data.map(x=>'<button type="button" data-id="'+x.id+'" data-text="'+esc(x.text)+'">'+esc(x.text)+'</button>').join('')||'<div style="padding:8px;color:#64748b">Sonuç bulunamadı.</div>';
                results.style.display='block';
              }catch(e){results.style.display='none';}
            },250);
          });
          results.addEventListener('click',function(e){const b=e.target.closest('button[data-id]');if(!b)return;const id=String(b.dataset.id);if(!chosen.some(x=>String(x.id)===id))chosen.push({id:id,text:b.dataset.text});renderChosen();search.value='';results.style.display='none';search.focus();});
          selected.addEventListener('click',function(e){const chip=e.target.closest('.wpst-cond-chip');if(!chip||e.target.tagName!=='BUTTON')return;chosen=chosen.filter(x=>String(x.id)!==String(chip.dataset.id));renderChosen();});

          function updatePageSummary(){
            if(!pageMenu||!pageTrigger||!pageSummary)return;
            const checked=[...pageMenu.querySelectorAll('input[type="checkbox"]:checked')];
            pageTrigger.querySelector('span').textContent=checked.length?checked.length+' sayfa seçildi':'Sayfa seç…';
            pageSummary.innerHTML=checked.map(x=>'<span class="wpst-page-chip">'+esc(x.dataset.label)+'<button type="button" data-id="'+x.value+'" aria-label="Kaldır">×</button></span>').join('');
          }
          if(pageTrigger&&pageSelect){
            pageTrigger.addEventListener('click',function(e){
              e.stopPropagation();
              pageSelect.classList.toggle('is-open');
            });
          }
          if(pageMenu){
            pageMenu.addEventListener('change',function(e){
              if(e.target.matches('input[type="checkbox"]'))updatePageSummary();
            });
          }
          if(pageSummary){
            pageSummary.addEventListener('click',function(e){
              const b=e.target.closest('button[data-id]');if(!b)return;
              const cb=pageMenu.querySelector('input[value="'+b.dataset.id+'"]');
              if(cb){cb.checked=false;updatePageSummary();}
            });
          }
          document.addEventListener('click',function(e){
            if(pageSelect&&!pageSelect.contains(e.target))pageSelect.classList.remove('is-open');
          });

          add.addEventListener('click',function(){
            const mode=document.getElementById('wpst-cond-mode').value, type=typeEl.value, typeLabel=typeEl.selectedOptions[0].text, group=document.getElementById('wpst-cond-group').value, operator=document.getElementById('wpst-cond-operator').value;
            let value='', display='';
            if(type==='page'){
              const opts=[...pageMenu.querySelectorAll('input[type="checkbox"]:checked')];
              value=opts.map(x=>x.value).join(',');
              display=opts.map(x=>x.dataset.label).join(', ');
            }
            else if(searchable.includes(type)){value=chosen.map(x=>x.id).join(',');display=chosen.map(x=>x.text).join(', ');}
            else if(type==='post_type'){value=postType.value;display=postType.selectedOptions[0]?postType.selectedOptions[0].text:value;}
            else {value=val.value||'';display=value;}
            if((type==='page'||searchable.includes(type))&&!value){alert(type==='page'?'En az bir sayfa seçmelisin.':'En az bir içerik seçmelisin.');return;}
            const duplicate=[...list.querySelectorAll('.wpst-cond-row')].some(r=>{
              const m=r.querySelector('input[name$="[mode]"]'),t=r.querySelector('input[name$="[type]"]'),v=r.querySelector('input[name$="[value]"]'),g=r.querySelector('input[name$="[group]"]');
              return m&&t&&v&&g&&m.value===mode&&t.value===type&&v.value===value&&g.value===group;
            });
            if(duplicate){alert('Bu koşul aynı grupta zaten mevcut.');return;}
            const i=list.querySelectorAll('.wpst-cond-row').length, row=document.createElement('div');
            row.className='wpst-cond-row';row.style.cssText='margin-top:8px;padding:9px;border:1px solid #e2e8f0;border-radius:9px;background:#fff';
            row.innerHTML='<button type="button" class="button-link-delete wpst-cond-remove" style="float:right;font-size:18px;line-height:1" aria-label="Koşulu kaldır">×</button>'+
              '<div><b style="display:inline-block;color:'+(mode==='exclude'?'#dc2626':'#16a34a')+';margin-bottom:2px">'+mode.toUpperCase()+'</b></div><div style="font-weight:600">'+esc(typeLabel)+'</div><div style="font-size:10px;color:#7c3aed;margin-top:2px">Grup '+esc(group)+' · '+esc(operator.toUpperCase())+'</div>'+(display?'<div style="font-size:11px;color:#64748b;margin-top:2px;word-break:break-word">'+esc(display)+'</div>':'')+
              '<input type="hidden" name="wpst_conditions['+i+'][mode]" value="'+mode+'"><input type="hidden" name="wpst_conditions['+i+'][type]" value="'+type+'"><input type="hidden" name="wpst_conditions['+i+'][value]" value="'+esc(value)+'"><input type="hidden" name="wpst_conditions['+i+'][group]" value="'+esc(group)+'"><input type="hidden" name="wpst_conditions['+i+'][operator]" value="'+esc(operator)+'"><div style="clear:both"></div>';
            list.appendChild(row); resetChosen(); val.value='';
            if(type==='page'&&pageMenu){
              pageMenu.querySelectorAll('input[type="checkbox"]').forEach(x=>x.checked=false);
              updatePageSummary();
              if(pageSelect)pageSelect.classList.remove('is-open');
            }
          });
          list.addEventListener('click',function(e){if(e.target.classList.contains('wpst-cond-remove'))e.target.closest('.wpst-cond-row').remove();});
        });
        </script>
        <?php
    }

    public static function ajax_search(){
        check_ajax_referer('wpst_condition_search','nonce');
        if(!current_user_can('edit_posts')) wp_send_json_error(array('message'=>'Yetkisiz'),403);
        $type=isset($_POST['type'])?sanitize_key(wp_unslash($_POST['type'])):'';
        $q=isset($_POST['q'])?sanitize_text_field(wp_unslash($_POST['q'])):'';
        if(!in_array($type,self::searchable_types(),true) || strlen($q)<2) wp_send_json_success(array());
        $out=array();
        if(in_array($type,array('page','post','portfolio','product'),true)){
            $pt=array('page'=>'page','post'=>'post','portfolio'=>(class_exists('WPST_Portfolio_Manager')?WPST_Portfolio_Manager::POST_TYPE:'wpst_portfolio'),'product'=>'product')[$type];
            $items=get_posts(array('post_type'=>$pt,'post_status'=>array('publish','draft','private'),'s'=>$q,'posts_per_page'=>20,'orderby'=>'relevance','order'=>'DESC'));
            foreach($items as $p) $out[]=array('id'=>$p->ID,'text'=>get_the_title($p).' (#'.$p->ID.')');
        }elseif(in_array($type,array('category','tag','portfolio_category','product_category','product_tag'),true)){
            $tax=array('category'=>'category','tag'=>'post_tag','portfolio_category'=>(class_exists('WPST_Portfolio_Manager')?WPST_Portfolio_Manager::TAXONOMY:'wpst_portfolio_category'),'product_category'=>'product_cat','product_tag'=>'product_tag')[$type];
            if(taxonomy_exists($tax)){
                $terms=get_terms(array('taxonomy'=>$tax,'hide_empty'=>false,'search'=>$q,'number'=>20));
                if(!is_wp_error($terms)) foreach($terms as $t) $out[]=array('id'=>$t->term_id,'text'=>$t->name.' (#'.$t->term_id.')');
            }
        }elseif('author'===$type){
            $users=get_users(array('search'=>'*'.$q.'*','search_columns'=>array('user_login','user_nicename','display_name','user_email'),'number'=>20,'orderby'=>'display_name'));
            foreach($users as $u) $out[]=array('id'=>$u->ID,'text'=>$u->display_name.' (#'.$u->ID.')');
        }
        wp_send_json_success($out);
    }

    public static function save($post_id){
        if(!isset($_POST['wpst_conditions_nonce']) || !wp_verify_nonce($_POST['wpst_conditions_nonce'],'wpst_conditions_save')) return;
        if(!current_user_can('edit_post',$post_id)) return;
        $rules=isset($_POST['wpst_conditions'])?wp_unslash($_POST['wpst_conditions']):array();
        $rules=class_exists('WPST_Display_Conditions')?WPST_Display_Conditions::sanitize($rules):array();
        update_post_meta($post_id,'_wpst_display_conditions',$rules);
        $priority=isset($_POST['wpst_condition_priority'])?max(1,min(999,absint($_POST['wpst_condition_priority']))):10;
        update_post_meta($post_id,'_wpst_condition_priority',$priority);
        $groups_relation=(isset($_POST['wpst_condition_groups_relation']) && 'and'===sanitize_key(wp_unslash($_POST['wpst_condition_groups_relation'])))?'and':'or';
        update_post_meta($post_id,'_wpst_condition_groups_relation',$groups_relation);
    }
}
WPST_Conditions_Admin::init();
