$(document).ready(function() {

	$('#account').change(function() {
		$.ajax({
			type: 'GET',
			url:  '_getOperationIphone.php',
			data: 'account=' + $('#account').val(),
			dataType: 'json',
			contentType: 'application/x-www-form-urlencoded;charset=utf-8;',
			success: function (operations) {
				$('#operation').find('option').remove();
				$('#operation').append('<option value="-1">-</option>');

				for (var i = 0; i < operations.length; i++) {
					$('#operation').append('<option value="' + operations[i].id + '">' + decodeURIComponent( escape( operations[i].name ) ) + '</option>');
				}
			},
			error: function (XMLHttpRequest, textStatus, errorThrown) {
				alert('load product search form error: '+textStatus);
			}
		});
	});
});