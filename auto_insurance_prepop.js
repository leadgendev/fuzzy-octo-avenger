// auto_insurance_prepop.js

( function ( $ ) {
	$( document ).ready( function () {
		var first_name_field = '#input_3_1_3';
		var last_name_field = '#input_3_1_6';
		var zip_code_field = '#input_3_2_5';
		var email_field = '#input_3_43';
		var home_phone_field = '#input_3_5';
		var cell_phone_field = '#input_3_7';
		
		var url = window.location.href;
		var query_str = url.split( '?' )[1];
		
		if ( query_str == undefined ) {
			return;
		}
		
		var url_parts = query_str.split( '&' );
		var params = Array();
		for ( var i in url_parts ) {
			var param = url_parts[i];
			param = param.split( '=' );
			params[param[0]] = decodeURIComponent( param[1] );
		}
		
		if ( params['first_name'] != undefined ) {
			$( first_name_field ).val( params['first_name'] );
		}
		
		if ( params['last_name'] != undefined ) {
			$( last_name_field ).val( params['last_name'] );
		}
		
		if ( params['zip_code'] != undefined ) {
			$( zip_code_field ).val( params['zip_code'] );
		}
		
		if ( params['email'] != undefined ) {
			$( email_field ).val( params['email'] );
		}
		
		if ( params['home_phone'] != undefined ) {
			$( home_phone_field ).val( params['home_phone'] );
		}
		
		if ( params['cell_phone'] != undefined ) {
			$( cell_phone_field ).val( params['cell_phone'] );
		}
	});
})( jQuery );