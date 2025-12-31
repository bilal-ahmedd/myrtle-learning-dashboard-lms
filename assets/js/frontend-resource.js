(function( $ ) { 'use strict';

    $( document ).ready( function() {

        var MLD_RESOURCE_FRONTEND = {

            init: function() {
                this.programOnChange();
                this.subjectOnChange();
                this.examOnChange();
                this.paperOnChange();
                this.yearOnChange();
                this.ProceedSpecificResource();
                this.resourceOnChange();
                this.removeIframe();
                this.tierOnChange();
                this.typeOnChange();
            },

            /**
             * type on change
             */
            typeOnChange: function() {

                $( document ).on( 'change', '#mld-type-dropdown', function() {

                    let courseid = $( '#mld-subject-dropdown' ).val();
                    let quizid = $( '#mld-exam-dropdown' ).val();
                    let paper = $( '#mld-paper-dropdown' ).val();
                    let tier = $( '#mld-tier-dropdown' ).val();
                    let year = $( '#mld-year-dropdown' ).val();
                    let type = $( '#mld-type-dropdown' ).val();

                    MLD_RESOURCE_FRONTEND.getResourceAccordingToData( courseid, quizid, paper, tier, year, type );
                } );
            },

            /**
             * Tier on change
             */
            tierOnChange: function() {

                $( document ).on( 'change', '#mld-tier-dropdown', function() {

                    let courseid = $( '#mld-subject-dropdown' ).val();
                    let quizid = $( '#mld-exam-dropdown' ).val();
                    let paper = $( '#mld-paper-dropdown' ).val();
                    let tier = $( '#mld-tier-dropdown' ).val();
                    let year = $( '#mld-year-dropdown' ).val();
                    let type = $( '#mld-type-dropdown' ).val();

                    MLD_RESOURCE_FRONTEND.getResourceAccordingToData( courseid, quizid, paper, tier, year, type );
                } );
            },

            /**
             * remove iframe tag
             */
            removeIframe: function() {

                $( document ).on( 'click', '.mld-popup-header .mld-close', function() {
                    $( '.mld-video-iframe' ).remove();
                } );
            },

            /**
             * resource on change
             */
            resourceOnChange: function() {

                $( document ).on( 'change', '#resource-answer-dropdown', function() {

                    let self = $( this );
                    let val = self.val();

                    if( val ) {
                        $( '.mld-proceed-btn' ).css( 'cursor', 'pointer' );
                    }

                    let title = $('option:selected', $(this) ).data( 'title' );
                    let videoLink = $('option:selected', $(this) ).data( 'video_url' );
                    let pdfURL = $('option:selected', $(this) ).data( 'pdf_url' );
                    $( '.mld-proceed-btn' ).attr( 'data-title', title );
                    $( '.mld-proceed-btn' ).attr( 'data-video_url', videoLink );
                    $( '.mld-proceed-btn' ).attr( 'data-pdf_url', pdfURL );
                } );
            },

            /**
             * proceed specific resource
             */
            ProceedSpecificResource: function() {

                $( document ).on( 'click', '.mld-proceed-btn', function() {

                    let resourceTitle = $( '.mld-proceed-btn' ).attr( 'data-title' );
                    let youtubeUrl = $( '.mld-proceed-btn' ).attr( 'data-video_url' );
                    let pdfUrl = $( '.mld-proceed-btn' ).attr( 'data-pdf_url' );
                    let videoID = '';

                    if( youtubeUrl ) {

                        // Extract the query string from the URL
                        let queryString = youtubeUrl.split('?')[1];

                        // Split the query string into key-value pairs
                        let params = queryString.split('&');

                        // Find the parameter that starts with 'v='
                        var videoParam = $.grep(params, function(param) {
                          return param.indexOf('v=') === 0;
                        });

                        // Extract the value after 'v='
                        videoID = videoParam[0].substring(2);
                    }

                    if( ! resourceTitle ) {
                        return false;
                    }

                    $( '.mld-pop-outer' ).css( 'z-index', '1' );
                    $( '.mld-pop-outer' ).show();
                    $( '.mld-pop-inner' ).css( 'height', '80%' );
                    $( '.mld-pop-inner' ).css( 'width', '50%' );
                    $( '.resource-mld-header-title' ).html( '<h2>'+resourceTitle+'</h2>' );
                    if( videoID ) {
                        $( '.mld-resource-content' ).html( '<iframe class="mld-video-iframe" src="https://www.youtube.com/embed/'+videoID+'"></iframe>' );
                        $( '.mld-resource-popup-inner' ).css( 'height', '500px' );
                        $( '.mld-resource-popup-inner' ).css( 'width', '65%' );
                        $( '.mld-resource-popup-inner .mld-resource-content' ).css( 'height', '65%' );
                    } else {
                        $( '.mld-resource-popup-inner .mld-resource-content' ).css( 'height', '55%' );
                        $( '.mld-pop-inner.mld-resource-popup-inner' ).css( 'height', '300px' );
                    }
                    $( '.mld-download-link' ).attr( 'href', pdfUrl );
                } );
            },

            /**
             * run resource ajax on year change
             */
            yearOnChange: function() {

                $( document ).on( 'change', '#mld-year-dropdown', function() {

                    let courseid = $( '#mld-subject-dropdown' ).val();
                    let quizid = $( '#mld-exam-dropdown' ).val();
                    let paper = $( '#mld-paper-dropdown' ).val();
                    let tier = $( '#mld-tier-dropdown' ).val();
                    let year = $( '#mld-year-dropdown' ).val();
                    let type = $( '#mld-type-dropdown' ).val();

                    MLD_RESOURCE_FRONTEND.getResourceAccordingToData( courseid, quizid, paper, tier, year, type );
                } );
            },

            /**
             * tun resource ajax on paper onchange
             */
            paperOnChange: function() {

                $( document ).on( 'change', '#mld-paper-dropdown', function() {

                    let courseid = $( '#mld-subject-dropdown' ).val();
                    let quizid = $( '#mld-exam-dropdown' ).val();
                    let paper = $( '#mld-paper-dropdown' ).val();
                    let tier = $( '#mld-tier-dropdown' ).val();
                    let year = $( '#mld-year-dropdown' ).val();
                    let type = $( '#mld-type-dropdown' ).val();

                    MLD_RESOURCE_FRONTEND.getResourceAccordingToData( courseid, quizid, paper, tier, year, type );
                } );
            },

            /**
             * Run resource ajax on exam onchange
             */
            examOnChange: function() {

                $( document ).on( 'change', '#mld-exam-dropdown', function() {

                    let courseid = $( '#mld-subject-dropdown' ).val();
                    let quizid = $( '#mld-exam-dropdown' ).val();
                    let paper = $( '#mld-paper-dropdown' ).val();
                    let tier = $( '#mld-tier-dropdown' ).val();
                    let year = $( '#mld-year-dropdown' ).val();
                    let type = $( '#mld-type-dropdown' ).val();

                    MLD_RESOURCE_FRONTEND.getResourceAccordingToData( courseid, quizid, paper, tier, year, type );
                } );
            },

            /**
             * run ajax for getting resource
             */
            getResourceAccordingToData: function( courseID, quizID = 0, paper = '', tier = '', year = 0, type = '' ) {

                let data = {
                    'action'               : 'get_resource',
                    'mld_nounce'           : MLD.security,
                    'course_id'            : courseID,
                    'quiz_id'              : quizID,
                    'paper'                : paper,
                    'tier'                 : tier,
                    'year'                 : year,
                    'type'                 : type
                };
                jQuery.post( MLD.ajaxURL, data, function( response ) {

                    let jsonEncode = JSON.parse( response );

                    if( jsonEncode.status == 'true' ) {
                        $( '#resource-answer-dropdown' ).html( jsonEncode.content );
                    }
                } );
            },

            /**
             * display quies according to subject ( subject == learndash course )
             */
            subjectOnChange: function() {

                $( document ).on( 'change', '#mld-subject-dropdown', function() {

                    let courseID = $( '#mld-subject-dropdown' ).val();
                    let quizid = $( '#mld-exam-dropdown' ).val();
                    let paper = $( '#mld-paper-dropdown' ).val();
                    let tier = $( '#mld-tier-dropdown' ).val();
                    let year = $( '#mld-year-dropdown' ).val();
                    let type = $( '#mld-type-dropdown' ).val();

                    MLD_RESOURCE_FRONTEND.getResourceAccordingToData( courseID, quizid, paper, tier, year, type );

                    let data = {
                        'action'               : 'get_course_quizess',
                        'mld_nounce'           : MLD.security,
                        'course_id'            : courseID
                    };

                    jQuery.post( MLD.ajaxURL, data, function( response ) {

                        let jsonEncode = JSON.parse( response );

                        if( jsonEncode.status == 'true' ) {
                            $( '#mld-exam-dropdown' ).html( jsonEncode.content );
                        }
                    } );
                } );
            },

            /**
             * display course according to tags
             */
            programOnChange: function() {

                $( document ).on( 'change', '#mld-program-dropdown', function() {

                    let termID = $( '#mld-program-dropdown' ).val();

                    let data = {
                        'action'               : 'get_resource_courses',
                        'mld_nounce'           : MLD.security,
                        'term_id'              : termID
                    };
                    jQuery.post( MLD.ajaxURL, data, function( response ) {

                        let jsonEncode = JSON.parse( response );

                        if( jsonEncode.status == 'true' ) {
                            $( '#mld-subject-dropdown' ).html( jsonEncode.content );
                        }
                    } );
                } );
            },
        };

        MLD_RESOURCE_FRONTEND.init();
    });
})( jQuery );