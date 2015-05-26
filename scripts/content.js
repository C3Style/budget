$(document).ready(function() {

	var calcTotBudget = 0;
	var calcTotDebit = 0;
	var calcTotCredit = 0;
	var calcTotCreditNoPaid = 0;
	var calcDiff = 0;

	$('#account').change(function() {
		window.location = "index.php?page=1&date=" + date + "&account=" + $('#account').val();
	});
	
	$('td').each(function() { 
		
		if ($(this).hasClass('amount'))
			$(this).html(formatStringToNumberAsString($(this).html(),2));
			
		if ($(this).hasClass('budget'))
			calcTotBudget += parseNumber($(this).html());
			
		if ($(this).hasClass('debit'))
			calcTotDebit += parseNumber($(this).html());
			
		if ($(this).hasClass('credit'))
			calcTotCredit += parseNumber($(this).html());
			
		if ($(this).hasClass('creditNoPaid'))
			calcTotCreditNoPaid += parseNumber($(this).html());
			
		$('#totalBudget').html(formatStringToNumberAsString(calcTotBudget,2));
		$('#totalDebit').html(formatStringToNumberAsString(calcTotDebit,2));
		$('#totalCredit').html(formatStringToNumberAsString(calcTotCredit,2));
			
		/*
		// 2012.01.16 : add parameter to take all row
		if ($(this).hasClass('calcDif')) {
			var totalDebit  = parseNumber($(this).prev().html());
			var totalBudget = parseNumber($(this).prev().prev().html());
			
			$(this).html(formatStringToNumberAsString(parseFloat(totalBudget) - parseFloat(totalDebit), 2));
		}
		*/
		
		if ($(this).hasClass('calcDif'))
			calcDiff += parseNumber($(this).html());
			
		$('#totalDiff').html(formatStringToNumberAsString(calcDiff,2));
	});
	
	$('#totalGlobal').html(
		formatStringToNumberAsString(parseNumber($('#solde').html()) - 
									 parseNumber($('#totalDebit').html()) +
									 parseNumber($('#totalCredit').html()),2)
	);
	
	// Must be done after global and estimated total 	
	$('td').each(function() { 
		
		if ($(this).hasClass('amount'))	{
			$(this).html(formatStringToNumberAsString($(this).html(),2));
			
			if (parseNumber($(this).html()) < 0)
				$(this).addClass('negativ');
			else 
				$(this).removeClass('negativ');
		}
	});
	
	// For total values
	$('th').each(function() { 
		
		if ($(this).hasClass('amount'))	{
			$(this).html(formatStringToNumberAsString($(this).html(),2));
			
			if (parseNumber($(this).html()) < 0) {
				$(this).addClass('negativ');
				$(this).addClass('negativBG');
			} else {
				$(this).removeClass('negativ');
				$(this).removeClass('negativBG');
			}
		}
	});
	
	$('.date-picker').datepicker( {
        changeMonth: true,
        changeYear: true,
        showButtonPanel: false,
        dateFormat: 'MM yy',
        onClose: function(dateText, inst) { 
            var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
            var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
			var newMonth = parseInt(month) + 1
			if (newMonth < 10) {
				newMonth = '0' + newMonth
			}
			window.location = 'index.php?page=1&date=' + year + newMonth + '01' + '&account=' + account;
            $(this).datepicker('setDate', new Date(year, month, 1));
        }
    });
	
	$.datepicker.regional['fr'] = {clearText: 'Effacer', clearStatus: '',
		closeText: 'Choisir', closeStatus: '',
		prevText: '', prevStatus: '',
		nextText: '', nextStatus: '',
		currentText: 'Aujourd\'hui', currentStatus: '',
		monthNames: ['janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre'],
		monthNamesShort: ['janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre'],
		monthStatus: 'Voir un autre mois', yearStatus: '',
		weekHeader: 'Sm', weekStatus: '',
		dayNames: ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'],
		dayNamesShort: ['Dim','Lun','Mar','Mer','Jeu','Ven','Sam'],
		dayNamesMin: ['Di','Lu','Ma','Me','Je','Ve','Sa'],
		dayStatus: 'Utiliser DD comme premier jour de la semaine', dateStatus: 'Choisir le DD, MM d',
		dateFormat: 'dd/mm/yy', firstDay: 0, 
		initStatus: 'Choisir la date', isRTL: false};
	$.datepicker.setDefaults($.datepicker.regional['fr']);
})

function paid (id, date, account) {
	
	$.ajax({
		type: 'GET',
		url:  '_paid.php',
		data: 'id=' + id + '&date=' + date,
		dataType: 'text',
		success: function (msg) {
			window.location = "index.php?page=1&date=" + date + "&account=" + account
		},
		error: function (XMLHttpRequest, textStatus, errorThrown) {
			alert('load product search form error: '+textStatus);
		}
	});
}

var showHide = 1; // 1 = minus | -1 = plus

function hideShowRowAll() {
	
	$("tr[name~=row]").each(function() { 
		var id = $(this).attr('id');
		var numID = id.substring(3, id.length);
		
		if ($('#row' + numID).hasClass('hidden') && showHide == 1) {
			$('#row' + numID).removeClass('hidden');
			$('#plusMinus' + numID).html("<img alt='Cacher' src='images/minus.png'/>");
			$('#plusMinusAll').html("<img alt='Montrer' src='images/minus.png'/>");
			
		} else if (showHide == -1) {
			$('#row' + numID).addClass('hidden');
			$('#plusMinus' + numID).html("<img alt='Montrer' src='images/plus.png'/>");
			$('#plusMinusAll').html("<img alt='Montrer' src='images/plus.png'/>");
		}		
	});
	
	showHide = -showHide;
}

function previewUnpaid (date, account) {

	// Required for the calendar in add.php
	window.location.href="#goTopOfPage";
	
	$('#preview').html('');
	$.ajax({
		type: 'GET',
		url:  'unPaid.php',
		data: 'date=' + date + '&account=' + account,
		dataType: 'text',
		success: function (msg) {
			$('#preview').html(msg);
			$('#preview').modal();
		},
		error: function (XMLHttpRequest, textStatus, errorThrown) {
			alert('load product search form error: '+textStatus);
		}
	});
}