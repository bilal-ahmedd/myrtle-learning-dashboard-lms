(function( $ ) { 'use strict';

    $( document ).ready( function() {

        var MLD_GENERAL = {

            init: function() {
                this.fixedPasswordIconAlignment();
                this.clickOnLoginButtonAfterPasswordReset();
            },

            /**
             * click on login  
             */
            clickOnLoginButtonAfterPasswordReset: function() {

                $( document ).on( 'click', '.reset-pass-submit input', function() {

                    setTimeout( function() {

                        let html = $( '.alert-green' ).html();

                        if( html ) {
                            $( '.learndash-wrapper .ld-login-button' ).click();
                        }
                    }, 5000 );
                } );
            },

            /**
             * Fixed password icon
             */
            fixedPasswordIconAlignment: function() {

                $( '.wp-pwd span' ).css( 'display', 'contents' );
                $( '.reset-pass-submit input' ).val( 'Update Password' );
            },
        };
        MLD_GENERAL.init();
    });
})( jQuery );