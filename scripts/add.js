$(document).ready(function() {

	$('#amount').val(formatStringToNumberAsString($('#amount').val(),2));

    $('#rec0').click(function() { 
            
        var cases = $("#rowRec").find(':checkbox');
        if(this.checked)
		{
            cases.attr('checked', true);
			$('#recText').html('Tout décocher');
        }
		else
		{            
			cases.attr('checked', false);
            $('#recText').html('Tout cocher');
        }           
    });
	
	$('#amount').change(function() { 
		$(this).val(formatStringToNumberAsString($(this).val(),2));
	});
	
	$('#amount').click(function() { 
		$(this).select();
	});
	
	$.validator.addMethod("checkOperation", function(value, element) {
		return !($('#hiddenType').val() != 2 && $('#operation').val() == -1);
	}, "Il manque l'opération.");
	
	$.validator.addMethod("checkDate", function(value, element) {
		return !($('#hiddenType').val() != 3 && $('#date').val() == "");
	}, "Il manque la date.");
	
	$.validator.addMethod("checkAtLeastOneRec", function(value, element) {
	
		var countChecked = 0;
		$(':checkbox').each(function(){
			if ($(this).is(':checked')) {
				countChecked += 1;
			}
		});
			
		return !($('#hiddenType').val() != 3 && countChecked == 0);
			
	}, "Il manque la récurrence.");
	
	$.validator.addMethod("checkRecValidDate", function(value, element) {
	
		var badDate = false;
		
		if ($('#hiddenType').val() != 3) {
			$(':checkbox').each(function(){
				if ($(this).is(':checked') && $(this).attr('name') != 'rec0') {
				
					var chosenDate = $('#date').val();
					try {
						$.datepicker.parseDate('yy-m-dd', chosenDate.substr(6, 4) + '-' + $(this).val() + '-' + chosenDate.substr(0, 2));
					} 
					catch(error) {
						badDate = true;
					}
				}
			});
		}
		
		return !(badDate);
		
	}, "Le jour n'existe pas pour tous les mois choisis.");
	
	$.validator.addMethod("checkAmount", function(value, element) {
		return !(parseNumber($('#amount').val()) <= 0 || parseNumber($('#amount').val()) >= 1000000);
	}, "Le montant doit se situer entre 0 et 1'000'000.");
	
	// validate signup form on keyup and submit
	var validator = $("#add").validate({
		success: "valid",
		rules: {
			operation: 			{ checkOperation: true },
			date: 				{ checkDate: true },
			onlyCheckRec:	 	{ checkAtLeastOneRec: true, checkRecValidDate: true },
			amount:				{ checkAmount: true }
		}
	});
});

function save() {
	$('form').submit();
}

function changeType (obj) {

	switch(obj.value) {
		case "1"  : // Débit
			$('#rowOperation').css("display", "");
			$('#rowYear').css("display", "none");
			$('#rowRemarkRec').css("display", "none");
			$('#rowDate').css("display", "");
			break;
		case "2" : // Crédit
			$('#rowOperation').css("display", "none");
			$('#rowYear').css("display", "none");
			$('#rowRemarkRec').css("display", "none");
			$('#rowDate').css("display", "");
			break;
		case "3" : // Budget 
			$('#rowOperation').css("display", "");
			$('#rowYear').css("display", "");
			$('#rowRemarkRec').css("display", "");
			$('#rowDate').css("display", "none");
			break;
	}
	
	$('#hiddenType').val(obj.value);
}

function setRecurrence(date) {
	date = "" + date;
	$(':checkbox').each(function(){
		$(this).attr('checked', false);
	});
	var month = parseInt(date.substr(4, 2),10);
	$('#rec' + month).attr('checked', true);
}