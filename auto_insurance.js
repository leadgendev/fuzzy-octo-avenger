// auto_insurance.js

( function ( $ ) {
	$( document ).ready( function () {
		var year_field = '#input_3_24';
		var make_field = '#input_3_25';
		var model_field = '#input_3_26';
		var sub_model_field = '#input_3_27';
		
		$( year_field + ' option' ).remove();
		$( '<option value="Choose Year">Choose Year</option>' ).appendTo( year_field );
		
		$( make_field + ' option' ).remove();
		$( '<option value="Choose Make">Choose Make</option>' ).appendTo( make_field );
		$( make_field ).attr( 'disabled', 'disabled' );
		
		$( model_field + ' option' ).remove();
		$( '<option value="Choose Model">Choose Model</option>' ).appendTo( model_field );
		$( model_field ).attr( 'disabled', 'disabled' );
		
		$( sub_model_field + ' option' ).remove();
		$( '<option value="Choose Sub-Model">Choose Sub-Model</option>' ).appendTo( sub_model_field );
		$( sub_model_field ).attr( 'disabled', 'disabled' );
		
		for ( var year = 2013; year >= 1981; year-- ) {
			var html = '<option value="' + year + '">' + year + '</option>';
			$( html ).appendTo( year_field );
		}
		
		$( year_field ).change( function () {
			var year = $( year_field ).val();
			
			$( make_field + ' option' ).remove();
			$( '<option value="Choose Make">Choose Make</option>' ).appendTo( make_field );
			$( make_field ).attr( 'disabled', 'disabled' );
			
			$( model_field + ' option' ).remove();
			$( '<option value="Choose Model">Choose Model</option>' ).appendTo( model_field );
			$( model_field ).attr( 'disabled', 'disabled' );
			
			$( sub_model_field + ' option' ).remove();
			$( '<option value="Choose Sub-Model">Choose Sub-Model</option>' ).appendTo( sub_model_field );
			$( sub_model_field ).attr( 'disabled', 'disabled' );
			
			if ( year == 'Choose Year' ) {
				alert( 'Please select a vehicle year.' );
				return;
			}
			
			$.ajax({
				url: '/vehicleMake.php'
				,type: 'POST'
				,data: {
					vehicleYear: year
				}
				,dataType: 'xml'
			}).done( function ( response ) {
				$( response ).find( 'Make' ).each( function () {
					var make = $( this ).text();
					var html = '<option value="' + make + '">' + make + '</option>';
					$( html ).appendTo( make_field );
				});
				$( make_field ).removeAttr( 'disabled' );
			});
		});
		
		$( make_field ).change( function () {
			var year = $( year_field ).val();
			var make = $( make_field ).val();
			
			$( model_field + ' option' ).remove();
			$( '<option value="Choose Model">Choose Model</option>' ).appendTo( model_field );
			$( model_field ).attr( 'disabled', 'disabled' );
			
			$( sub_model_field + ' option' ).remove();
			$( '<option value="Choose Sub-Model">Choose Sub-Model</option>' ).appendTo( sub_model_field );
			$( sub_model_field ).attr( 'disabled', 'disabled' );
			
			if ( make == 'Choose Make' ) {
				alert( 'Please select a vehicle make.' );
				return;
			}
			
			$.ajax({
				url: '/vehicleModel.php'
				,type: 'POST'
				,data: {
					vehicleYear: year
					,vehicleMake: make
				}
				,dataType: 'xml'
			}).done( function ( response ) {
				$( response ).find( 'Model' ).each( function () {
					var model = $( this ).text();
					var html = '<option value="' + model + '">' + model + '</option>';
					$( html ).appendTo( model_field );
				});
				$( model_field ).removeAttr( 'disabled' );
			});
		});
		
		$( model_field ).change( function () {
			var year = $( year_field ).val();
			var make = $( make_field ).val();
			var model = $( model_field ).val();
			
			$( sub_model_field + ' option' ).remove();
			$( '<option value="Choose Sub-Model">Choose Sub-Model</option>' ).appendTo( sub_model_field );
			$( sub_model_field ).attr( 'disabled', 'disabled' );
			
			if ( model == 'Choose Model' ) {
				alert( 'Please select a vehicle model.' );
				return;
			}
			
			$.ajax({
				url: '/vehicleSubModel.php'
				,type: 'POST'
				,data: {
					vehicleYear: year
					,vehicleMake: make
					,vehicleModel: model
				}
				,dataType: 'xml'
			}).done( function ( response ) {
				$( response ).find( 'Sub-Model' ).each( function () {
					var sub_model = $( this ).text();
					var html = '<option value="' + sub_model + '">' + sub_model + '</option>';
					$( html ).appendTo( sub_model_field );
				});
				$( sub_model_field ).removeAttr( 'disabled' );
			});
		});
	});
})( jQuery );