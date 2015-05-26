$(document).ready(function() {	

	var calcTotBudget = 0;
	var calcTotDebit = 0;
	var calcDiff = 0;

	$('#account').change(function() {
		window.location = "index.php?page=2&date=" + date + "&account=" + $('#account').val();
	});
	
	$('td').each(function() { 
				
		if ($(this).hasClass('budget'))
			calcTotBudget += parseNumber($(this).html());
			
		if ($(this).hasClass('debit'))
			calcTotDebit += parseNumber($(this).html());
						
		$('#totalBudget').html(formatStringToNumberAsString(calcTotBudget,2));
		$('#totalDebit').html(formatStringToNumberAsString(calcTotDebit,2));
				
		// Not the same code as content.js
		// 2012.01.16 : add parameter to take all row
		/*
		if ($(this).hasClass('calcDifGlobal')) {
			var totalDebit  = parseNumber($(this).prev().html());
			var totalBudget = parseNumber($(this).prev().prev().html());

			$(this).html(formatStringToNumberAsString(parseFloat(totalDebit) - parseFloat(totalBudget), 2));
		}
		*/
		
		// Same code as content.js
		// 2012.01.16 : add parameter to take all row
		/*
		if ($(this).hasClass('calcDif')) {
			var totalDebit  = parseNumber($(this).prev().html());
			var totalBudget = parseNumber($(this).prev().prev().html());
			
			$(this).html(formatStringToNumberAsString(parseFloat(totalBudget) - parseFloat(totalDebit), 2));
		}
		*/		
		
		if ($(this).hasClass('calcDif'))
			calcDiff += parseNumber($(this).html());
			
		$('#totalDiff').html(formatStringToNumberAsString(calcDiff,2));
		
		if ($(this).hasClass('amount'))	{
			$(this).html(formatStringToNumberAsString($(this).html(), 2));
			
			if (parseNumber($(this).html()) < 0)
				$(this).addClass('negativ');
			else 
				$(this).removeClass('negativ');
		}
	});
	
	// 2012.01.16 : add parameter to take all row
	var monthAvailable = parseNumber($('#currentlyAvailable').html());
	var yearAvailable = parseNumber($('#totalDiff').html());
	$('#currentlyAvailable').html(formatStringToNumberAsString(monthAvailable + yearAvailable));
	
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
	
	var estimatedSolde = parseNumber($('#estimatedSolde').html()) - parseNumber($('#totalBudget').html());
	$('#estimatedSolde').html(formatStringToNumberAsString(estimatedSolde, 2));
	
	loadGrafic(0);
	
	$('#operation').change(function () {
		loadGrafic($('#operation').val());
	});
});

function loadGrafic(operation) {

	$.post("_reportMonth.php?year=" + year + "&account=" + account + "&operation=" + operation,
	  function(data){
	  
			debit = data.debit;
			budget = data.budget;
			$("#chart1").empty();
			
			// Show legend only for operation
			var showlegend = false;
			
			// Show array only for operation
			if (debit.length != 0 && operation != 0) {
				
				showlegend = true;
				$('#recapTable').css('display', 'block');
				
				var round = 0;
				var cptLine = 0;
				var total = 0;
				
				$('tr#recapBudget td').each( function() {
							
					$(this).html(formatStringToNumberAsString(0, round));
							
					if (budget[cptLine] != undefined && 
						budget[cptLine][0] != undefined && 
						$(this).hasClass(budget[cptLine][0])) {
						
						$(this).html(formatStringToNumberAsString(budget[cptLine][1], round));
						total += budget[cptLine][1];
						cptLine++;
					}
					
					if ($(this).hasClass('average'))
						$(this).html(formatStringToNumberAsString(total / cptLine, round));
					
					if ($(this).hasClass('total'))
						$(this).html(formatStringToNumberAsString(total, round));
				});
				
				cptLine = 0;
				total = 0;
				
				$('tr#recapDebit td').each( function() {
				
					$(this).html(formatStringToNumberAsString(0, round));
				
					if (debit[cptLine] != undefined && 
						debit[cptLine][0] != undefined && 
						$(this).hasClass(debit[cptLine][0])) {
						
						$(this).html(formatStringToNumberAsString(debit[cptLine][1], round));
						total += debit[cptLine][1];
						cptLine++;
					}
					
					if ($(this).hasClass('average'))
						$(this).html(formatStringToNumberAsString(total / cptLine, round));
					
					if ($(this).hasClass('total'))
						$(this).html(formatStringToNumberAsString(total, round));
				});
				
			} else {
				$('#recapTable').css('display', 'none');
			}
			
			// Show graphic
			if (debit.length != 0) {
				graficLine(showlegend);
			}
			
	 }, "json");
}

function graficLine(showlegend){

	plot1 = $.jqplot('chart1', [debit, budget], {
		title:'',
		seriesColors: ["#168BD4", "#FFE88B"],
		// legend:{show:false, location:'e', placement:'outsideGrid'},
		series:[
			{label:'Débit'},
			{label:'Budget'},
		],
		axes:{
			xaxis:{
				renderer:$.jqplot.DateAxisRenderer,
				tickOptions:{
					formatString:'%b'
				},
				min: '01-01-' + year,
				max: '12-01-' + year,
				tickInterval: '1 month',
			},
				yaxis:{
				tickOptions:{
					formatString:"%'d.-"
				}
			}
		},
		highlighter: {
			show: true,
			sizeAdjust: 7.5
		},
		cursor: {
			show: false
		}
	});
	
	if (showlegend) {
		plot1.legend.show = true;
		plot1.legend.location = 'se';
		plot1.replot();
	}
} 