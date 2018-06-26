$( document ).ready(function() 
{
	$('.ref_delete').click(function(obj) 
    		{
    			if(confirm($(obj).attr('conf_message')))
    			{
    				return true;
    			}
    			return false;
    		});
});

function on_sel_file(el)
{
	if($(el).prop('checked'))
		$(el).parents('.fielditem').one().find('.filetype_div').show()
	else
		$(el).parents('.fielditem').one().find('.filetype_div').hide()
}

function on_type_change(type_el)
{
	var cfg = $('[name="entity[cfg]"').val();
	var fldtype = $(type_el).val(); 
	var fld_name = $(type_el).attr('name');
	var entity_name = $('[name="entity[ename]"]').val();
	var el_fldinfo = $(type_el).parents('.fielditem').one().find('.fldinfo');
	
	load_ajax_block(el_fldinfo,"/master/emaker/typeinfo/"+cfg+"/"+fldtype+"/"+fld_name+"/"+entity_name);//+"/"+nameroot);
	
}

function on_entity_change(type_el)
{
	var cfg = $('[name="entity[cfg]"').val();
	var fldtype = $(type_el).val(); 
	var fld_name = $(type_el).attr('name');
	var entity_to = $('._entity_to').val();
	var el_fld_to = $(type_el).parents('._eref').one().find('._fld_to');
	
	el_fld_to.html("");
	
	var the_url = "/master/emaker/efields/"+cfg+"/"+entity_to;
	$.getJSON( the_url, function( data ) {
		  el_fld_to.html("");
		  $.each( data.items, function( key, val ) {
			  el_fld_to.append($('<option value="'+val+'">'+val+"</option>"));
		  });
	});
}