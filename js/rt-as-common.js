(function(a){a.fn.extend({tabify:function(e){function c(b){hash=a(b).find("a").attr("href");return hash=hash.substring(0,hash.length-4)}function f(b){a(b).addClass("active");a(c(b)).show();a(b).siblings("li").each(function(){a(this).removeClass("active");a(c(this)).hide()})}return this.each(function(){function b(){location.hash&&a(d).find("a[href="+location.hash+"]").length>0&&f(a(d).find("a[href="+location.hash+"]").parent())}var d=this,g={ul:a(d)};a(this).find("li a").each(function(){a(this).attr("href", a(this).attr("href")+"-tab")});location.hash&&b();setInterval(b,100);a(this).find("li").each(function(){a(this).hasClass("active")?a(c(this)).show():a(c(this)).hide()});e&&e(g)})}})})(jQuery);

jQuery(document).ready( function(){

    jQuery('#rtAS_registration_username').keyup( function(){
        jQuery(this).next().hide();
    });

    jQuery('#rtAS_registration_email').keyup( function(){
        jQuery(this).next().hide();
    });

    jQuery('.widget_rtas_login_reg_widget .hide-if-js').hide();
    jQuery('.widget_rtas_login_reg_widget .hide-if-no-js').show();

    jQuery('#tabsmenu').tabify();

    jQuery( '#rtAS_login_username, #rtAS_registration_username' ).blur( function() { if( this.value == '' ) this.value='Username'; } );
    jQuery( '#rtAS_login_username, #rtAS_registration_username' ).focus( function() { if( this.value == 'Username' ) this.value=''; } );
    jQuery( '#rtAS_login_password' ).blur( function() { if( this.value == '' ) this.value='Password'; } );
    jQuery( '#rtAS_login_password' ).focus( function() { if( this.value == 'Password' ) this.value=''; } );
    jQuery( '#rtAS_registration_email' ).blur( function() { if( this.value == '' ) this.value='Email Address'; } );
    jQuery( '#rtAS_registration_email' ).focus( function() { if( this.value == 'Email Address' ) this.value=''; } );

    jQuery('#rtAS-Login-Form').on( 'submit', function(e){
        e.preventDefault();
        jQuery( '#rtAS-Login-Block .rtas-loader' ).show();
        var ajaxurl = url.admin_ajax_url;
        var data = {
            action: 'validate_login',
            log : jQuery('#rtAS_login_username').val(),
            pwd: jQuery('#rtAS_login_password').val(),
            rememberme: jQuery('#rtAS_login_rememberme').val()
        }

        jQuery.ajax({
          url: ajaxurl,
          type: "POST",
          data: data,
          success: function(response) {
                if( response == true ){
                    location.reload();
                } else {
                    jQuery( '#rtAS-Login-Block .rtas-loader' ).hide();
                    jQuery('#rtAS_login_username').parent().prev().addClass('not-available');
                    jQuery('#rtAS_login_username').parent().prev().html('Invalid Username or Password.');
                }
            },
          xhrFields: {
		  		withCredentials: true
		  	}/*
,
		  crossDomain: true
*/
        });
    });

    jQuery('#rtAS_registration_username').blur(function(){
        var ajaxurl = url.admin_ajax_url;
        var data = {
            action: 'validate_registration',
            check_username: true,
            username: jQuery(this).val()
        };

        jQuery.ajax( {
            url: ajaxurl,
            type: 'POST',
            data: data,
            success: function(response) {
                check_username(response);
            }
        });
    });

    jQuery('#rtAS_registration_email').blur(function(){
        var ajaxurl = url.admin_ajax_url;
        var data = {
            action: 'validate_registration',
            check_email: true,
            email: jQuery(this).val()
        };

        jQuery.ajax( {
            url: ajaxurl,
            type: 'POST',
            data: data,
            success: function(response) {
                check_email(response);
            }
        });
    });

    jQuery('#rtAS_registration_submit').on( 'click', function(e){
        e.preventDefault();
        jQuery( '#rtas-registration-block .rtas-loader' ).show();
        var ajaxurl = url.admin_ajax_url;
        var data = {
            action: 'validate_registration',
            username: jQuery('#rtAS_registration_username').val(),
            email: jQuery('#rtAS_registration_email').val(),
            recaptcha_challenge_field : jQuery('#recaptcha_challenge_field').val(),
            recaptcha_response_field : jQuery('#recaptcha_response_field').val()
        };

        jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            dataType: 'json',
            data: data,
            success: function(response) {
                jQuery( '#rtas-registration-block .rtas-loader' ).hide();
                Recaptcha.reload();
                if( 'Please check your email inbox for verification.' == response.recaptcha ) {
                    jQuery('#rtas-registration-block').html('<p><span class="available">'+response.recaptcha+'</span></p>');
//                    jQuery('#rtAS_registration_username').next().removeClass('available');
//                    jQuery('#rtAS_registration_email').next().removeClass('not-available');
//                    jQuery('#rtAS_registration_email').next().removeClass('available');
//                    jQuery('#rtAS_registration_username').val('');
//                    jQuery('#rtAS_registration_email').val('');
//                    jQuery('#rtAS_registration_username').next().html('').hide();
//                    jQuery('#rtAS_registration_email').next().html('').hide();
//                    jQuery('#rtAS_registration_submit').parent().prev().removeClass('not-available').hide();
//                    jQuery('#rtAS_registration_username').parent().prev().addClass('available').css('display', 'block');
//                    jQuery('#rtAS_registration_username').parent().prev().html(response.recaptcha);
//                    jQuery('#rtAS_registration_username').parent().prev().delay(15000).fadeOut(1000);
                } else {
                    check_username(response.username);
                    check_email(response.email);
                    jQuery('#rtAS_registration_username').parent().prev().removeClass('available').hide();
                    jQuery('#rtAS_registration_submit').parent().prev().addClass('not-available').css('display','block');
                    jQuery('#rtAS_registration_submit').parent().prev().html(response.recaptcha);
               }
            }
        });
    });

});

function check_username(response) {
    if( 'Available.' == response ){
        jQuery('#rtAS_registration_username').next().removeClass('not-available');
        jQuery('#rtAS_registration_username').next().addClass('available');
        jQuery('#rtAS_registration_username').next().css('display','block');
    } else {
        jQuery('#rtAS_registration_username').next().removeClass('available');
        jQuery('#rtAS_registration_username').next().addClass('not-available');
        jQuery('#rtAS_registration_username').next().css('display','block');
    }
    jQuery('#rtAS_registration_username').next().html(response);
}

function check_email(response){
    if( 'Valid Email.' == response ){
        jQuery('#rtAS_registration_email').next().removeClass('not-available');
        jQuery('#rtAS_registration_email').next().addClass('available');
        jQuery('#rtAS_registration_email').next().css('display','block');
    } else {
        jQuery('#rtAS_registration_email').next().removeClass('available');
        jQuery('#rtAS_registration_email').next().addClass('not-available');
        jQuery('#rtAS_registration_email').next().css('display','block');
    }
    jQuery('#rtAS_registration_email').next().html(response);
}
