(function( $ ) { 'use strict';

    $( document ).ready( function() {

        var MLD_COURSE = {

            init: function() {
                this.addedCourseFeaturedImage();
                this.appendGroupCourses();
                this.displayCoursesShortcodeAccordingToUser();
                this.updateQuizStatistics();
                // this.expandCourseDetail();
            },

            /**
             * expand course detail
             */
            expandCourseDetail: function() {

                $( document ).on( 'click', '.ld-expand-button', function() {

                    let self = $(this);
                    if ( $(this).hasClass('.mld-arrow-class') ) {                        self.removeClass( '.mld-arrow-class' );
                        self.parents('.ld-item-list-item-preview').next('div').css('max-height', 0 );
                    } else {
                        self.addClass( '.mld-arrow-class' );
                        self.parents('.ld-item-list-item-preview').next('div').css('max-height', 'unset');
                    }
                } );
            },

            /**
             * update quiz statistics
             */
            updateQuizStatistics: function() {

                $( document ).on( 'click', '.statistic_data', function() {
                    let self = $(this);
                    let parent = self.parents( 'th' ).parent( 'tr' );
                    let nextTr = parent.next();
                    let findCoreectAnswer = nextTr.find( 'li' );

                    $.each( $( findCoreectAnswer ), function( index, elem ) {
                        let checkedAttr = $( elem ).find( 'input' ).attr( 'checked' );
                        if( 'checked' == checkedAttr ) {
                            $( elem ).addClass( 'wpProQuiz_answerCorrect' );
                        }
                    } );
                    nextTr.slideToggle();
                } );

                $( document ).on( 'click', '#wpProQuiz_overlay_close', function() {
                    $( '#wpProQuiz_user_overlay' ).hide();
                    $( '.mld-header-main-wrapper' ).show();
                } );

                $( document ).on( 'click', '.user_statistic', function() {

                    if( $( document ).find( 'div' ).hasClass( 'mld-user-courses-shortcode' ) ) {

                        let self = $( this );
                        $( '.mld-header-main-wrapper' ).hide();
                        $( '#wpProQuiz_user_overlay' ).show();
                        $( '#wpProQuiz_loadUserData' ).show();
                        let quizId = self.data( 'quiz-id' );
                        let userId = self.data( 'user-id' );
                        let refId = self.data( 'ref-id' );
                        let nonce = self.data( 'statistic-nonce' );
                        let data = {
                            'quizId'          : quizId,
                            'userId'          : userId,
                            'refId'           : refId,
                            'statistic_nonce' : nonce
                        };
                        let data_2 = {
                            'action'            : 'wp_pro_quiz_admin_ajax_statistic_load_user',
                            'func'              : 'statisticLoadUser',
                            'data'              : data
                        };

                        jQuery.post( MLD.ajaxURL, data_2, function( response ) {

                            let resp = JSON.parse( response );
                            $( '#wpProQuiz_loadUserData' ).hide();
                            $( '#wpProQuiz_user_content' ).html( resp.html  );
                        } );
                    }
                } );
            },

            /**
             * display user's courses
             */
            displayCoursesShortcodeAccordingToUser: function() {

                $( document ).on( 'change', '.mld-selected-user-id', function() {

                    $( '.mld-courses-list-wrap .mld-courses-loader' ).show();

                    let self = $(this);
                    let userID = self.val();

                    let data = {
                        'action'               : 'display_course_shortcode',
                        'user_id'              : userID
                    };

                    jQuery.post( MLD.ajaxURL, data, function( response ) {

                        let jsonEncode = JSON.parse( response );

                        if( 'true' == jsonEncode.status ) {
                            $( '.mld-courses-list-wrap .mld-courses-loader' ).hide();
                            $( '.mld-user-courses-shortcode' ).html( jsonEncode.content );
                            $( '.mld-user-courses-shortcode.mld-courses-list-wrapper ' ).css( 'padding', '20px' );
                            MLD_COURSE.updateFeaturedImage();
                        }
                    } );
                } );
            },

            /**
             * append group courses according to group
             */
            appendGroupCourses: function() {

                $( document ).on( 'change', '.mld-selected-group-id', function() {

                    $( '.mld-courses-list-wrap .mld-courses-loader' ).show();

                    let groupID = $(this).val();

                    let data = {
                        'action'               : 'append_group_users',
                        'group_id'             : groupID
                    };

                    jQuery.post( MLD.ajaxURL, data, function( response ) {

                        let jsonEncode = JSON.parse( response );

                        if( 'true' == jsonEncode.status ) {
                            $( '.mld-courses-list-wrap .mld-courses-loader' ).hide();
                            $( '.mld-selected-user-id' ).html( jsonEncode.content );
                        }
                    } );

                } );
            },

            /**
             * create a function to update a featured image
             */
            updateFeaturedImage: function( avgScore, targetScore, cohortScore, titles ) {

                $( '.mld-course-featured-image' ).remove();

                $.each( $( '.mld-courses-list-wrapper .ld-item-list-item-course ' ), function( index, elem ) {

                    let self = $( this );
                    let courseClass = self.attr( 'id' );
                    let courseID = courseClass.replace( 'ld-course-list-item-', '' );

                    let data = {
                        'action'               : 'added_featured_image',
                        'course_id'            : courseID
                    };

                    jQuery.post( MLD.ajaxURL, data, function( response ) {

                        let jsonEncode = JSON.parse( response );

                        if( 'true' == jsonEncode.status ) {
                            $( '#'+courseClass+' .ld-status-icon.ld-status-incomplete' ).after( jsonEncode.content );
                            $( '#'+courseClass+' .ld-status-icon.ld-status-complete' ).after( jsonEncode.content );
                            $( '#'+courseClass+' .ld-status-icon.ld-status-in-progress' ).after( jsonEncode.content );
                        }
                    } );
                } );
            },
            /**
             * Added course featured image
             */
            addedCourseFeaturedImage: function(){

                MLD_COURSE.updateFeaturedImage();

                $( document ).on( 'click', '.mld-courses-list-wrapper .next.ld-primary-color-hover ', function() {

                    setTimeout( function() {
                        MLD_COURSE.updateFeaturedImage();
                    }, 5000);
                } );

                $( document ).on( 'click', '.mld-courses-list-wrapper .prev.ld-primary-color-hover ', function() {

                    setTimeout( function() {
                        MLD_COURSE.updateFeaturedImage();
                    }, 5000);
                } );

                $( document ).on( 'click', '.mld-courses-list-wrapper .ld-item-search-submit', function() {

                    setTimeout( function() {
                        MLD_COURSE.updateFeaturedImage();
                    }, 5000);
                } );

                $( document ).on( 'click', '.mld-courses-list-wrapper .ld-reset-button', function() {

                    setTimeout( function() {
                        MLD_COURSE.updateFeaturedImage();
                    }, 5000);
                } );
            },
        };
        MLD_COURSE.init();
    });
})( jQuery );
