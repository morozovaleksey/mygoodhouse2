function convert_to_innodb()
{
	var request = $.ajax({
		url: convert_to_innodb_url,
		dataType: 'json',
		type: "POST",
		beforeSend: function(data) {
			ajax_loading_open();
		},
		success: function(data) {
			ajax_loading_close();
			alert(data.message);
		},      
		error: function (xhr, ajaxOptions, thrownError) {     
			ajax_loading_close();
			alert(data.message);
		}
	});
}
function readURL(input) {
	$('a.button.button_upload_xls span').html('');
	$('a.button.button_upload_xls span').html(' <b>('+input.val()+')</b>');
}

function export_start() {
	var temp_url = url_export;
	temp_url += '&export_range_from='+$('input[name="import_xls_export_range_from"]').val();
	temp_url += '&export_range_to='+$('input[name="import_xls_export_range_to"]').val();
	temp_url += '&export_range_price_from='+$('input[name="import_xls_export_price_between_from"]').val();
	temp_url += '&export_range_price_to='+$('input[name="import_xls_export_price_between_to"]').val();
	temp_url += '&export_categories='+$('select[name="import_xls_export_categories"]').val();
	temp_url += '&export_manufacturers='+$('select[name="import_xls_export_manufacturers"]').val();
	temp_url += '&export_models='+$('input[name="import_xls_export_models"]').val();
	
	window.location = temp_url;
}

function save_configuration() {
	var request = $.ajax({
		url: save_configuration_url,
		data: $('form').serialize(),
		type: "POST",
		dataType: 'json',
		beforeSend: function(data) {
			ajax_loading_open();
		},
		success: function(data) {
			ajax_loading_close();
			alert(data.message);
		},      
		error: function (xhr, ajaxOptions, thrownError) {     
			ajax_loading_close();
			alert(data.message);
		}
	});
}