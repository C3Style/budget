var currentTab = ''
$(document).ready(function() {	

	currentTab = '#tab' + $('#tabGetValue').val()

	$('#balance').val(formatStringToNumberAsString($('#balance').val(),2));

	$('#account').change(function() {
		window.location = "index.php?page=3&account=" + $('#account').val() + "&tab=" + currentTab.substr(4, 1);
	});
	
	$('#balance').change(function() { 
		$(this).val(formatStringToNumberAsString($(this).val(),2));
	});
	
	$('#yearBudgetSrc').change(function() { 
		refreshDataCopyBudget();
	});
	
	$('td').each(function() { 
				
		if ($(this).hasClass('amount'))	
			$(this).html(formatStringToNumberAsString($(this).html(), 2));
			
		if (parseNumber($(this).html()) < 0)
			$(this).addClass('negativ');
		else 
			$(this).removeClass('negativ');
	});
	
	$(".tab_content").hide(); //Hide all content
	$("#liTab" + $('#tabGetValue').val()).addClass("active").show(); //Activate first tab
	$("#tab" + $('#tabGetValue').val()).show(); //Show first tab content

	//On Click Event
	$("ul.tabs li").click(function() {

		currentTab = $(this).find("a").attr("href");
	
		$("ul.tabs li").removeClass("active"); //Remove any "active" class
		$(this).addClass("active"); //Add "active" class to selected tab
		$(".tab_content").hide(); //Hide all tab content

		var activeTab = $(this).find("a").attr("href"); //Find the href attribute value to identify the active tab + content
		$(activeTab).fadeIn(); //Fade in the active ID content
		return false;
	});
});

function addOperation () {
	
	var opName = $('#operation').val();
	opName = opName.replace(/\"/g,"\\\"");
	opName = opName.replace(/\'/g,"\\\'"); 

	if ($('#operation').val() == '') {
		$('#errorOperation').html('<p>L\'opération ne doit pas être vide.<p>');
		$('#errorOperation').dialog('open');
	}
	else {
		add('_addOperation.php', 
			'name=' + opName + '&account=' + $('#account').val(), 
			'Cette opération existe déjà.',
			$('#account').val());
	}
}

function addBalance () {
	
	if ($('#balance').val() == '0.00') {
		$('#errorOperation').html('<p>La solde ne doit pas être vide.<p>');
		$('#errorOperation').dialog('open');
	}
	else {
		add('_addBalance.php', 
			'balance=' + parseNumber($('#balance').val()) + '&year=' + $('#year').val() + '&account=' + $('#account').val(), 
			'La solde existe déjà pour cette année.',
			$('#account').val());
	}
}

function add (urlValue, dataValue, errorMessage, account) {
		
	$.ajax({
		type: 'GET',
		url:  urlValue,
		data: dataValue,
		dataType: 'json',
		success: function (msg) {
			if (msg.error === true) {
				$('#errorOperation').html('<p>' + errorMessage + '<p>');
				$('#errorOperation').dialog('open');
			}
			else
				window.location = "index.php?page=3&account=" + account + "&tab=" + currentTab.substr(4, 1);
		},
		error: function (XMLHttpRequest, textStatus, errorThrown) {
			alert('load product search form error: '+textStatus);
		}
	});
}

var id;
var url;
function deleteOperation (idLocal, accountLocal) {
	account = accountLocal;
	id = idLocal;
	url = '_deleteOperation.php';
	$('#dialogOperation').html('<p>Etes-vous sûr de vouloir supprimer l\'opération?<p>');
	$('#dialogOperation').dialog('open');
}

function deleteBalance (idLocal, accountLocal) {
	account = accountLocal;
	id = idLocal;
	url = '_deleteBalance.php';
	$('#dialogOperation').html('<p>Etes-vous sûr de vouloir supprimer le solde?<p>');
	$('#dialogOperation').dialog('open');
}

$(function(){

	// Dialog			
	$('#errorOperation').dialog({
		autoOpen	: false,
		modal		: true,
		autoOpen	: false,
		resizable	: false,
		draggable	: false,
		width		: 500,
		buttons		: {
			"OK": function() { 
				$(this).dialog('close'); 
			},
		}
	});
	
	// Dialog			
	$('#dialogOperation').dialog({
		autoOpen	: false,
		modal		: true,
		autoOpen	: false,
		resizable	: false,
		draggable	: false,
		width		: 500,
		buttons		: {
			"Non": function() { 
				$(this).dialog('close'); 
			},
			"Oui": function() { 
				$.ajax({
					type: 'GET',
					url:  url,
					data: 'id=' + id + '&account=' + account,
					dataType: 'text',
					success: function (msg) {
						window.location = "index.php?page=3&account=" + account + "&tab=" + currentTab.substr(4, 1)
					},
					error: function (XMLHttpRequest, textStatus, errorThrown) {
						alert('load product search form error: '+textStatus);
					}
				});
			},
		}
	});
	
	// Dialog			
	$('#copyBudgetOperation').dialog({
		autoOpen	: false,
		modal		: true,
		autoOpen	: false,
		resizable	: false,
		draggable	: false,
		width		: 500,
		buttons		: {
			"Non": function() { 
				$(this).dialog('close'); 
			},
			"Oui": function() { 
				$('form').submit();
			},
		}
	});
});

function saveCopy() {
	
	if ($('#yearBudgetSrc').val() == 0 || $('#yearBudgetDest').val() == 0) {
		$('#errorOperation').html('<p>Veuillez choisir une année source et une année de destination.<p>');
		$('#errorOperation').dialog('open');
		return;
	}
	
	if ($('#yearBudgetSrc').val() >= $('#yearBudgetDest').val()) {
		$('#errorOperation').html('<p>Veuillez choisir une année source inférieure à l\'année de destination.<p>');
		$('#errorOperation').dialog('open');
		return;
	}

	if ($('#treeInput').val() == '') {
		$('#errorOperation').html('<p>Veuillez choisir au moins une transaction à copier.<p>');
		$('#errorOperation').dialog('open');
		return;
	}

	$('#copyBudgetOperation').html('<p>Etes-vous sûr d\'effectuer la copie des transactions sélectionnées?<p>');
	$('#copyBudgetOperation').html('<p>Etes-vous sûr d\'effectuer la copie des transactions sélectionnées?<p>');
	$('#copyBudgetOperation').dialog('open');
}

function updatePassword() {

	if ($('#newPassword').val() != $('#confirmPassword').val()) {
		$('#errorOperation').html('<p>Le nouveau mot de passe ne correspond pas à la confirmation.<p>');
		$('#errorOperation').dialog('open');
	}
	else {
		$('form').submit();
	}
}

function refreshDataCopyBudget() {
	
	$('#loading').css('display', 'block');
	$('#tree').html('');	
	var tree = $("#tree").dynatree("getTree");

	$.ajax({
		type: 'GET',
		url:  '_copyBudget.php',
		data: 'year=' + $('#yearBudgetSrc').val() + '&account=' + account,
		dataType: 'text',
		success: function (msg) {
			$('#tree').html(msg);
			tree.reload();
			$('#tree').css('display', 'block');	
			$('#loading').css('display', 'none');
		},
		error: function (XMLHttpRequest, textStatus, errorThrown) {
			alert('load product search form error: '+textStatus);
			$('#loading').css('display', 'none');
		}
	});
}