// payday_datepickers.js

( function ( $ ) {
	var move_in_date_field = '#input_7_51';
	var date_of_birth_field = '#input_7_9';
	var hire_date_field = '#input_7_52';
	var first_payday_field = '#input_7_18';
	var second_payday_field = '#input_7_19';
	
	var today = new Date();
	var holidays_year = today.getFullYear();
	var holidays = new Array();
	var holidays_url = '/payday_datepickers_proxy.php?action=getPublicHolidaysForYear&country=usa&year=';
	
	$.ajax({
		url: holidays_url + holidays_year
		,dataType: 'jsonp'
		,jsonp: 'jsonp'
	}).done( function ( data ) {
		for ( var d in data ) {
			holidays.push( data[d] );
		};
		
		holidays_year++;
		$.ajax({
			url: holidays_url + holidays_year
			,dataType: 'jsonp'
			,jsonp: 'jsonp'
		}).done( function ( data ) {
			for ( var d in data ) {
				holidays.push( data[d] );
			}
		});
	});
	
	var noWeekends_noHolidays = function ( d ) {
		for ( var h in holidays ) {
			var year = holidays[h].date.year;
			var month = holidays[h].date.month;
			var day = holidays[h].date.day;
			
			if (( d.getFullYear() == year ) && ( ( d.getMonth() + 1 ) == month ) && ( d.getDate() == day )) {
				return [false, '', holidays[h].localName];
			}
		}

		return $.datepicker.noWeekends( d );
	};
	
	$( move_in_date_field ).datepicker({
		changeMonth: true
		,changeYear: true
		,dateFormat: 'yy-mm-dd'
	});
	$( date_of_birth_field ).datepicker({
		changeMonth: true
		,changeYear: true
		,dateFormat: 'yy-mm-dd'
	});
	$( hire_date_field ).datepicker({
		changeMonth: true
		,changeYear: true
		,dateFormat: 'yy-mm-dd'
	});
	
	$( first_payday_field ).datepicker({
		dateFormat: 'yy-mm-dd'
		,maxDate: 45
		,minDate: 0
		,beforeShowDay: noWeekends_noHolidays
	});
	$( second_payday_field ).datepicker({
		dateFormat: 'yy-mm-dd'
		,maxDate: 45
		,minDate: 0
		,beforeShowDay: noWeekends_noHolidays
	});
})( jQuery );
