var id;
var date;
var page = 1;

function changeDialogTitle(dialogName, title) {
	$('#' + dialogName).dialog('option', 'title', title);
}

function addTransaction(id, date, account) {
	
	$.ajax({
		type: 'GET',
		url:  'add.php',
		data: 'id=' + id + '&date=' + date + '&account=' + account,
		dataType: 'text',
		success: function (msg) {
			$('#dialogAdd').html(msg);
			$("#dialogAdd").dialog("open");
		},
		error: function (XMLHttpRequest, textStatus, errorThrown) {
			alert('load product search form error: '+textStatus);
		}
	});
}

function searchTransactions(date, account) {

	$.ajax({
		type: 'GET',
		url:  'search.php',
		data: 'date=' + date + '&account=' + account,
		dataType: 'text',
		success: function (msg) {
			$('#dialogSearch').html(msg);
			$("#dialogSearch").dialog("open");
		},
		error: function (XMLHttpRequest, textStatus, errorThrown) {
			alert('load product search form error: '+textStatus);
		}
	});
}

function deleteTransaction (localId, localDate, localPage, localAccount) {
	id = localId;
	date = localDate;
	page = localPage;
	account = localAccount;
	$('#dialog').dialog('open');
}

$(function(){

	$('#dialogAdd').dialog({
		autoOpen	: false,
		modal		: true,
		autoOpen	: false,
		resizable	: false,
		draggable	: true,
		height:650,
		width:580
	});

	$('#dialogSearch').dialog({
		autoOpen	: false,
		modal		: true,
		autoOpen	: false,
		resizable	: false,
		draggable	: true,
		height:650,
		width:800
	});

	// Dialog			
	$('#dialog').dialog({
		autoOpen	: false,
		modal		: true,
		autoOpen	: false,
		resizable	: false,
		draggable	: true,
		width		: 500,
		buttons		: {
			"Non": function() { 
				$(this).dialog('close'); 
			},
			"Oui": function() { 
				$.ajax({
					type: 'GET',
					url:  '_delete.php',
					data: 'id=' + id,
					dataType: 'text',
					success: function (msg) {
						window.location = "index.php?page=" + page + "&date=" + date + "&action=2" + "&account=" + account
					},
					error: function (XMLHttpRequest, textStatus, errorThrown) {
						alert('load product search form error: '+textStatus);
					}
				});
			},
		}
	});
});

function hideShowRow(operationID) {
	if ($('#row' + operationID).hasClass('hidden')) {
		$('#row' + operationID).removeClass('hidden');
		$('#plusMinus' + operationID).html("<img alt='Cacher' src='images/minus.png'/>");
	}
	else {
		$('#row' + operationID).addClass('hidden');
		$('#plusMinus' + operationID).html("<img alt='Montrer' src='images/plus.png'/>");
	}
}

function parseNumber(value){
	if(value != ''){
		var regEx = /([ ']+)/;
		while (regEx.test(value)) {
			value = value.replace(regEx, '');
		}
	} else {
		value = '0';
	}
	_t = parseFloat(value);
	if(isNaN(_t)){
		return 0;
	} else {
		return _t;
	}
}

function formatStringToNumberAsString(value, decimals){
	if(decimals == undefined){
		var decimals = 2;
	}
	var result = 0;
	if(value == '' || value == undefined){
		result = 0;
	} else {
		result = parseNumber(value);
	}
	// round to 0.01
	if(result != 0){
		var _t = Math.pow(10, decimals);
		result = (Math.round(result * _t) / _t);
	}
	if(decimals > 0){
		// add decimals
		result = result.toFixed(decimals);
	}
	result = result.toString();
	// add thousand separator
	result = addThousandSeparator(result);
	
	return result;
}

function addThousandSeparator(value){
	var regEx = /(\d+)(\d{3})/;
	while (regEx.test(value)) {
		value = value.replace(regEx, '$1' + "'" + '$2');
	}
	return value;
}

function checkDescrip(text,length) 
{
	if(text.value.length >= length) 
	{
		alert("Taille maximale atteinte pour ce champs.");
		text.value = text.value.slice(0, length);
	}
}