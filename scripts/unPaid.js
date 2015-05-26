$(document).ready(function() {
	
	$('td').each(function() { 
		if ($(this).hasClass('amount'))
			$(this).html(formatStringToNumberAsString($(this).html(),2));
	});
});

function payOlderTransaction (id, date, account, isPaid) {
	
	$.ajax({
		type: 'GET',
		url:  '_paid.php',
		data: 'id=' + id + '&date=' + date,
		dataType: 'text',
		success: function (msg) {
			if ($('#' + id + date).hasClass('unpaid')) {
				 $('#' + id + date).css('background-color', '#FFFFFF');
				 $('#' + id + date).removeClass('unpaid');
				 $('#' + id + date + ' td.btnPaid').addClass('btnUnpaid');
				 $('#' + id + date + ' td.btnPaid').removeClass('btnPaid');
			} else {
				$('#' + id + date).css('background-color', '#FFE88B');
				$('#' + id + date).addClass('unpaid');
				$('#' + id + date + ' td.btnUnpaid').addClass('btnPaid');
				$('#' + id + date + ' td.btnUnpaid').removeClass('btnUnpaid');
			}
		},
		error: function (XMLHttpRequest, textStatus, errorThrown) {
			alert('load product search form error: '+textStatus);
		}
	});
}