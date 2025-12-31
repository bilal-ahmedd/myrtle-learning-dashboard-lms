(function( $ ) { 'use strict';

    $( document ).ready( function() {

        var MLD_NOTIFICATION = {

            init: function() {

                this.nextBack();
                this.changePageContent();
            },

            /**
             * change page content
             */
            changePageContent: function() {

                $(document).on('click', '.mld-notification-read-more a', function(e){
                    e.preventDefault();

                    let self = $(this);

                    let data = self.attr('data-parent');
                    try { data = JSON.parse(data); } catch(err) { data = {}; }
                    if (data.title) {
                        $('.mld-exam-head h4').text(data.title);
                    }

                    if(data.excerpt) {
                        $('.mld-exam-head p').text(data.excerpt);
                    }

                    if (data.content) {
                        $('.mld-exam-body h5').nextAll().remove();
                        $('.mld-exam-body h5').after(data.content);
                    }

                    // if (imgURL) {
                    //     $('.mld-page-content-wrapper .mld-page-header img').attr('src', imgURL);
                    // }

                    $('.loop-wrapper').removeClass('is-active');
                    //wrap.addClass('is-active');
                    $('html, body').animate({ scrollTop: 0 }, 600);
                });
            },

            /**
             * Expand texteditor on edit page
             */
            nextBack: function() {

                $( document ).on( 'click', '#mld-next, #mld-back', function() {

                    let self = $( this );
                    if( '' != self.data( 'paged' ) ) {

                        $( '.mld-comment-loader' ).show();
                        let paged = self.data( 'paged' );
                        let siblingPaged = self.siblings( 'a' ).data( 'paged' );
                        if( '' == siblingPaged ) {
                            siblingPaged = 0;
                        }
                        
                        let data = {
                            'action': 'paged_notification',
                            'mld_nounce': MLD.security,
                            'paged': paged
                        };

                        jQuery.post( MLD.ajaxURL, data, function( response ) {

                            let jsonEncode = JSON.parse( response );

                            if( true == jsonEncode.status ) {

                                $( document ).find( '.mld-notify-card' ).replaceWith( jsonEncode.content ).change();
                                if( 'next' == self.data( 'type' ) ) {
                                    $( document ).find( '#mld-back' ).attr( 'data-paged', siblingPaged + 1 ).change();
                                    $( document ).find( '#mld-next' ).attr( 'data-paged', paged + 1 ).change();
                                } else {
                                    $( document ).find( '#mld-back' ).attr( 'data-paged', paged - 1 ).change();
                                    $( document ).find( '#mld-next' ).attr( 'data-paged', siblingPaged - 1 ).change();
                                }
                                $( '.mld-comment-loader' ).hide();
                            }
                        });
                    }
                } );
            },
        };

        MLD_NOTIFICATION.init();
    });
})( jQuery );