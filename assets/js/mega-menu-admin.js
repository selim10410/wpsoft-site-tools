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
})(jQuery);
