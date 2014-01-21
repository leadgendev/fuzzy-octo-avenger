// bank_aba.js

( function ( $ ) {
	//alert('loading script');
    //$( document ).ready( function () {
        var bank_aba_field = '#input_7_35';
        var bank_phone_field = '#input_7_34';
        var bank_name_field = '#input_7_33';
        //alert('loading script');
        $( bank_aba_field ).change( function () {
           var routing_number = $( bank_aba_field ).val();
           
           $.ajax({
               url: '/bank_aba_proxy.php?rn=' + routing_number
               ,dataType: 'jsonp'
           }).done( function ( data ) {
				//alert( data );
               if ( data['message'] == 'OK' ) {
                   var bank_name = data['customer_name'];
                   var bank_phone = data['telephone'];
                   
                   $( bank_name_field ).val( bank_name );
                   $( bank_phone_field ).val( bank_phone );
               }
           });
        });
    //});
})( jQuery );
