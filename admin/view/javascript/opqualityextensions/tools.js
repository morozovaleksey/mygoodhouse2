function ajax_loading_open() {
  jQuery('body').prepend('<div class="ajax_loading"><i class="fa fa-refresh fa-spin"></i></div>');
}

function ajax_loading_close() {
  jQuery('body div.ajax_loading').fadeOut('fast', function(){
    jQuery(this).remove();
  });
}

function add_row_table_input(element)
{
	var table = element.closest('table');
	var model_row = table.find('tr.model_row').html();
	var num_rows = table.data('rows');

	var new_row = model_row.replace(/replace_by_number/gi, num_rows);
	table.find('tbody').find('tr.model_row').before('<tr>'+new_row+'</tr>');
	table.data('rows', (num_rows+1));

	var last_row_inserted = table.find('tbody').find('tr.model_row').prev('tr');
	last_row_inserted.find('.bootstrap-select').replaceWith(function() { return $(this).find('select'); });
	last_row_inserted.find('.selectpicker').selectpicker();
}

function save_configuration_ajax(form_extension, force_function)
{
	if(typeof force_function !== 'undefined')
		$('input[name="force_function"]').val(force_function);

	$.ajax({
		url: form_extension.attr('action'),
		data: form_extension.serialize(),
		type: "POST",
		dataType: 'json',
		beforeSend:function()
		{
			ajax_loading_open();
		},
		success: function(data) {
			ajax_loading_close();
			if(!data.error)
				open_manual_notification(data.message, 'success', 'check');
			else
				open_manual_notification(data.message, 'warning', 'exclamation');
		},
		error: function(data) {
			ajax_loading_close();
			alert('Error saving configuration.');
		},
	});
}

$('.selectpicker').selectpicker();