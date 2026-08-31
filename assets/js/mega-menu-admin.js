(function($){
function sync(row){
 var mode=row.find('.wpst-mega-mode').val()||'columns';
 row.find('.wpst-mode-columns').toggle(mode==='columns');
 row.find('.wpst-mode-elementor').toggle(mode==='elementor');
}
$(function(){
 $('.wpst-mega-row').each(function(){sync($(this));});
 $(document).on('change','.wpst-mega-mode',function(){sync($(this).closest('.wpst-mega-row'));});
 $(document).on('change','.wpst-switch input',function(){var row=$(this).closest('.wpst-mega-row');row.find('.wpst-mega-status').toggleClass('is-on',this.checked).text(this.checked?'Mega Menü Açık':'Normal Menü');});
});
})(jQuery);
(function($){
function shadowValue(v){
 if(v==='none')return 'none';
 if(v==='medium')return '0 24px 65px rgba(15,23,42,.15)';
 if(v==='strong')return '0 30px 80px rgba(15,23,42,.22)';
 return '0 20px 55px rgba(15,23,42,.10)';
}
function updatePreview(row){
 var card=row.closest('.wpst-mega-menu-card');
 var preview=card.find('.wpst-mega-preview-panel').first();
 if(!preview.length)return;
 var cols=parseInt(row.find('[name$="[cols]"]').val()||3,10);
 var bg=row.find('.wpst-mega-panel-bg').val()||'#fff';
 var radius=parseInt(row.find('.wpst-mega-panel-radius').val()||22,10);
 var shadow=row.find('.wpst-mega-panel-shadow').val()||'soft';
 var style=row.find('[name$="[item_style]"]').val()||'cards';
 var density=row.find('[name$="[density]"]').val()||'comfortable';
 preview.css({'background':bg,'border-radius':radius+'px','box-shadow':shadowValue(shadow)});
 preview.attr('data-style',style).attr('data-density',density);
 preview.toggleClass('is-compact',density==='compact');
 preview.find('.wpst-mega-preview-cols').css('grid-template-columns','repeat('+cols+',minmax(0,1fr))');
}
$(document).on('input change','.wpst-mega-row input,.wpst-mega-row select',function(){
 updatePreview($(this).closest('.wpst-mega-row'));
});
$('.wpst-mega-row').each(function(){updatePreview($(this));});

$(document).on('click','.wpst-menu-image-select',function(e){
 e.preventDefault();
 var wrap=$(this).closest('.wpst-menu-image-field');
 var frame=wp.media({title:'Mega Menü Görseli',button:{text:'Görseli Kullan'},multiple:false});
 frame.on('select',function(){
   var att=frame.state().get('selection').first().toJSON();
   wrap.find('.wpst-menu-image-id').val(att.id);
   var url=(att.sizes&&att.sizes.thumbnail)?att.sizes.thumbnail.url:att.url;
   wrap.find('.wpst-menu-image-preview').html('<img src="'+url+'" alt="">');
 });
 frame.open();
});
$(document).on('click','.wpst-menu-image-remove',function(e){
 e.preventDefault();
 var wrap=$(this).closest('.wpst-menu-image-field');
 wrap.find('.wpst-menu-image-id').val('');
 wrap.find('.wpst-menu-image-preview').html('<span>Görsel yok</span>');
});
$(document).on('change','.wpst-menu-icon-select',function(){
 var select=$(this),value=select.val(),preview=select.closest('.wpst-menu-icon-field').find('.wpst-menu-icon-preview');
 var svg=window.wpstMenuIcons&&wpstMenuIcons.svgs?wpstMenuIcons.svgs[value]:'';
 preview.html(svg||'<span class="dashicons dashicons-minus"></span>');
});
$(document).on('click','.wpst-menu-icon-remove',function(e){
 e.preventDefault();var field=$(this).closest('.wpst-menu-icon-field');field.find('.wpst-menu-icon-select').val('').trigger('change');
});
function syncIconSource(field){
 var source=field.find('input[type="radio"][name$="[icon_source]"]:checked').val()||'internal';
 field.attr('data-icon-source',source);
 field.find('.wpst-menu-icon-pane.is-internal').toggle(source==='internal');
 field.find('.wpst-menu-icon-pane.is-custom').toggle(source==='custom');
}
$('.wpst-menu-icon-field').each(function(){syncIconSource($(this));});
$(document).on('change','.wpst-menu-icon-source input',function(){syncIconSource($(this).closest('.wpst-menu-icon-field'));});
$(document).on('click','.wpst-menu-custom-icon-select',function(e){
 e.preventDefault();
 var field=$(this).closest('.wpst-menu-icon-field');
 var frame=wp.media({title:'Özel Menü İkonu',button:{text:'İkonu Kullan'},library:{type:['image/png','image/svg+xml']},multiple:false});
 frame.on('select',function(){
  var att=frame.state().get('selection').first().toJSON();
  if(['image/png','image/svg+xml'].indexOf(att.mime)<0){window.alert('Yalnız SVG veya PNG seçebilirsiniz.');return;}
  field.find('.wpst-menu-custom-icon-id').val(att.id);
  var img=$('<img>',{src:att.url,alt:''});
  field.find('.wpst-menu-custom-icon-preview').empty().append(img);
 });
 frame.open();
});
$(document).on('click','.wpst-menu-custom-icon-remove',function(e){
 e.preventDefault();var field=$(this).closest('.wpst-menu-icon-field');field.find('.wpst-menu-custom-icon-id').val('');field.find('.wpst-menu-custom-icon-preview').html('<span>SVG/PNG seçilmedi</span>');
});
})(jQuery);
