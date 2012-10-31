jQuery(document).ready( function(){
    
    jQuery( '#recaptcha_public_key' ).blur( function() { if( this.value == '' ) this.value='Public Key'; } );
    jQuery( '#recaptcha_public_key' ).focus( function() { if( this.value == 'Public Key' ) this.value=''; } );
    jQuery( '#recaptcha_private_key' ).blur( function() { if( this.value == '' ) this.value='Private Key'; } );
    jQuery( '#recaptcha_private_key' ).focus( function() { if( this.value == 'Private Key' ) this.value=''; } );
    
});