function load_fields(select_element)
{
	//var table_to_select = $(this).parent('div.multiform_block').find('select.table_to_select');
	var selected_table = $(select_element).val();
	var config = $('#config').val();
	var url_to_ajax = "\?r=hmvc/fields/"+config+"/"+selected_table;
	$.ajax({
	    url: url_to_ajax,
	    dataType: 'json',
	    type: 'post',
	    contentType: 'application/json',
	  //  data: JSON.stringify( { "first-name": $('#first-name').val(), "last-name": $('#last-name').val() } ),
	    processData: false,
	    success: function( data, textStatus, jQxhr ){
	       // $('#response pre').html( JSON.stringify( data ) );
	    	//var fields_to_element = $(select_element).parent('div.multiform_block').find('select.fld_to_select');
	    	var fields_to_element = $(select_element).parent().find('select.fld_to_select');
	    	fields_to_element.html('');
	    	for (var key in data) 
	    	{
	    		fields_to_element.append('<option value="' + key + '">' + key + '</option>')
	    	}
	    },
	    error: function( jqXhr, textStatus, errorThrown ){
	        console.log( errorThrown );
	        $('#console').html(errorThrown.message);
	    }
	});
	
}

function check_required(el)
{
	field = $(el).val();
	cb_req = $(el).parent().find('.cb_required');
	if($('#field_'+field+'_required').is(':checked'))//.attr('checked')=='checked')
	{
		
		cb_req.prop('checked',"checked");
		cb_req.attr('disabled',true);
		
		prev_element=cb_req.prev('input[type=hidden]');
		doubler_exists = false;
		if(prev_element.length>0)
			{
			if(prev_element.attr('name')==cb_req.attr('name'))
				{
				doubler_exists = true;
				}
			}
		if(!doubler_exists)
			{
			hidden_req = $('<input type="hidden" />');
			hidden_req.attr('name',cb_req.attr('name'));
			hidden_req.attr('value','on');
			cb_req.before(hidden_req);
			}
	}
	else
	{
		cb_req.attr('disabled',false);
		
		prev_element=cb_req.prev('input[type=hidden]');
		doubler_exists = false;
		if(prev_element.length>0)
			{
			if(prev_element.attr('name')==cb_req.attr('name'))
				{
				doubler_exists = true;
				}
			}
		
		if(doubler_exists)
		{
			$(prev_element).remove();
		}
	}
}

function find_fields()
{
	
}

function toggle_ep_divs(_ep)
{
	$('#authcon_settings_'+_ep).toggle();
	$('#con_for_auth_div_'+_ep).toggle();
}

$( document ).ready(function() {
	
  //  $('.drg_view').draggable({cursor: 'crosshair'});
    
   // $('#bindings').jqDynaForm();
});


