(function( $ ) { 'use strict';

    $( document ).ready( function() {

        var MLD_WORK_FRONTEND = {

            init: function() {
                this.groupOnChange();
                this.userOnChange();
                this.courseOnChange();
                this.lessonOnChange();
                this.topicOnChange();
                this.proceedToQuizDetail();
                this.changeData();
                this.openCommentPopup();
                this.closeCommentPopup();
                this.updateComment();
                this.openReviewPopup();
                this.approveEssay();
                this.awardPoints();
                this.applyWpEditor();
                this.deleteComments();
                this.setAwardingPoints();
                this.displayAwardedPointWrapper();
            },

            /**
             * display awarded point wrapper
             */
            displayAwardedPointWrapper: function() {

                $( document ).on( 'click', '.mld-essay-total-point', function() {

                    $( '.mld-total-point-set-wrapper' ).show();
                } );
            },


            /**
             * set awarding points
             */
            setAwardingPoints: function() {

                $( document ).on( 'click', '.mld-set-awarded-point', function() {

                    $( '.mld-set-awarded-point' ).text( 'Update...' );
                    let point = $( '.mld-total-point-set-wrapper input' ).val();
                    let essayID = $( '.mld-set-awarded-point' ).attr( 'data-eaasy_id' );
                    
                    let data = {
                        'action'          : 'set_awarded_points',
                        'point'           : point,
                        'essay_id'        : essayID
                    };

                    jQuery.post( MLD.ajaxURL, data, function( response ) {

                        let pointText = $( '.mld-point-text' ).text();

                        let parts = pointText.split(" Points Awarded");
                        let result = parts[0];
                        var array = result.split('/');

                        let awardedPoints = parseInt( array[0] );
                        let pointLimit = array[1];
                        let addition = $( '.mld-total-point-set-wrapper input' ).val();
                        $( '.mld-point-text' ).text( addition+'/'+pointLimit+' Points Awarded' ); 
                        $( '.mld-total-point-set-wrapper' ).hide();
                        $( '.mld-total-point-set-wrapper input' ).val( '' );
                    } );
                } );
            },

            /**
             * delete essay comment
             */
            deleteComments: function() {

                $( document ).on( 'click', '.mld-comment-trash', function() {

                    let self = $(this);
                    let commentID = self.attr( 'data-comment_id' ); 
                    
                    if( ! commentID ) {
                        return false;
                    }

                    let data = {
                        'action'          : 'delete_essay_comments',
                        'comment_id'      :  commentID
                    };

                    jQuery.post( MLD.ajaxURL, data, function( response ) {
                        
                        self.parents( '.mld-user-chat' ).remove();
                    } );         
                } );
            },

            /**
             * apply wp editor
             */
            applyWpEditor: function() {

                $( document ).on( 'click', '.mld-work-comment', function() {

                    setTimeout(function() { 
                        $( '.add_media' ).html( '<span class="dashicons dashicons-cloud-upload mld-upload-pdf"></span>' );
                        $.each( $( '.mld-upload-pdf' ), function( index, elem ) {
                            if( index > 0 ) {
                                $( elem ).hide();
                            }
                        } );
                    }, 200);

                } );

                $( document ).on( 'click', '.mld-work-comment', function() {

                    var editorSettings = {

                        tinymce: {
                            toolbar: "",
                            textarea_rows: 1,
                        },
                        quicktags: {},
                        mediaButtons: true, 
                    };

                    wp.editor.initialize( 'mld-essay-comment-box', editorSettings );
                } );
            },

            /**
             * Award point
             */
            awardPoints: function() {

                $( document ).on( 'click', '.mld-essay-point-btn', function() {

                    let pointTotal = $( '.mld-grading-point input' ).val();
                    let self = $(this);
                    self.text( 'Awarding...' );
                    let essayID = self.attr( 'data-eaasy_id' );
                             
                    if( pointTotal ) {
                        
                        let data = {
                            'action'          : 'award_points',
                            'point_count'     : pointTotal,
                            'essay_id'        : essayID
                        };

                        jQuery.post( MLD.ajaxURL, data, function( response ) {
                            

                            let pointQuantity = parseInt( $( '.mld-grading-point input' ).val() );
                            let pointText = $( '.mld-point-text' ).text();

                            let parts = pointText.split(" Points Awarded");
                            let result = parts[0];
                            var array = result.split('/');

                            let awardedPoints = parseInt( array[0] );
                            let pointLimit = array[1];
                            let addition = pointQuantity + awardedPoints;
                            self.text( 'Awarded' );
                            $( '.mld-point-text' ).text( pointQuantity+'/'+pointLimit+' Points Awarded' );
                            $( '.mld-grading-point input' ).val( ' ' );
                        } );                        
                    }
                } );
            },

            /**
             * approve essay
             */
            approveEssay: function() {

                $( document ).on( 'click', '.mld-essay-apr-btn', function() {

                    let self = $(this);
                    self.text( 'Approve...' );
                    let essayID = self.attr( 'data-eaasy_id' ); 
                    
                    if( essayID ) {                        

                        let data = {
                            'action'          : 'approve_essay',
                            'essay_id'        : essayID
                        };

                        jQuery.post( MLD.ajaxURL, data, function( response ) {
                            $( '.mld-essay-apr-btn' ).text( 'Approved' );
                        } );
                    }
                } );
            },  

            /**
             * open review popup
             */
            openReviewPopup: function() {

                $( document ).on( 'click', '.mld-essay-review', function() {

                    let self = $(this);

                    $( '.mld-pop-outer' ).show();
                    $( '.mld-my-work-loader' ).show();
                    $( '.mld-word-comment-section' ).hide();
                    $( '.mld-body-wrapper' ).css( 'z-index', '0' );
                    let essayID = self.attr( 'data_id' );
                    $( '.mld-essay-comment-title' ).text( 'Assignment Options' );
                    $( '.mld-essay-comment-input' ).hide();
                    
                    let data = {
                        'action'          : 'get_grading_html',
                        'essay_id'        : essayID
                    };

                    jQuery.post( MLD.ajaxURL, data, function( response ) {

                        let jsonEncode = JSON.parse( response );
                        if( jsonEncode.status == 'true' ) {
                            $( '.mld-my-work-loader' ).hide();
                            $( '.mld-word-comment-section' ).show();
                            $( '.mld-word-comment-section' ).html( jsonEncode.content );
                        }
                    } );
                } );
            },

            /**
             * update comment
             */
            updateComment: function() {

                $( document ).on( 'click', '.mld-essay-comment-update-btn', function() {

                    let comment = tinymce.get( 'mld-essay-comment-box' ).getContent();
                    let userID = $( '.mld-hidden-ids' ).attr( 'data-user_id' );
                    let essayID = $( '.mld-hidden-ids' ).attr( 'data-essay_id' );
                    
                    if( ! comment || ! userID || ! essayID ) {
                        return false;
                    }

                    let html = ''; 
                    html += '<div class="mld-chat-msg-wrap">';
                    html += '<div class="mld-reciever-wrapper mld-reciever-msg">';
                    html += '<div class="mld-chat-msg mld-reciever">';
                    html += '<div class="mld-user-chat">'+comment+'</div>';
                    html += '</div>';
                    html += '</div>';
                    html += '<div class="mld-clear-both"></div>';
                    html += '</div>';

                    if ( $('.mld-word-comment-section').hasClass( '.mld-chat-msg-wrap' ) ) {
                        
                        $('.mld-word-comment-section .mld-chat-msg-wrap').last().after( html );
                    } else {
                        $('.mld-word-comment-section').append( html );
                    }
                    
                    tinymce.get('mld-essay-comment-box').setContent( '' );

                    let data = {
                        'action'          : 'update_essays_comment',
                        'comment'         : comment,
                        'user_id'         : userID,
                        'essay_id'        : essayID
                    };

                    jQuery.post( MLD.ajaxURL, data, function( response ) {

                        let data = {
                            'action'          : 'get_essays_comment',
                            'essay_id'        :  essayID
                        };

                        jQuery.post( MLD.ajaxURL, data, function( response ) {

                            let jsonEncode = JSON.parse( response );
                            if( jsonEncode.status == 'true' ) {
                                $( '.mld-word-comment-section' ).html( jsonEncode.content );
                                $( '.mld-essay-comment-input' ).show();                            
                            }                        
                        } );
                    } );
                } );
            },

            /**
             * close comment popup
             */
            closeCommentPopup: function() {

                $( document ).on( 'click', '.work-popup-closed .dashicons.dashicons-dismiss', function() {

                    $( '.mld-pop-outer' ).hide();
                     $( '.mld-body-wrapper' ).css( 'z-index', '1' );
                } );
            },

            /**
             * open work comment popup
             */
            openCommentPopup: function() {

                $( document ).on( 'click', '.mld-work-comment', function() {

                    let self = $(this);

                    $( '.mld-pop-outer' ).show();
                    $( '.mld-my-work-loader' ).show();
                    $( '.mld-body-wrapper' ).css( 'z-index', '0' );
                    $( '.mld-word-comment-section' ).hide();
                    $( '.mld-essay-comment-input' ).hide();  
                    let essayID = self.attr( 'data_id' );
                    $( '.mld-hidden-ids' ).attr( 'data-essay_id', essayID );
                    $( '.mld-essay-comment-title' ).text( 'Comments' );

                    let data = {
                        'action'          : 'get_essays_comment',
                        'essay_id'        :  essayID
                    };

                    jQuery.post( MLD.ajaxURL, data, function( response ) {

                        let jsonEncode = JSON.parse( response );
                        if( jsonEncode.status == 'true' ) {
                            $( '.mld-my-work-loader' ).hide();
                            $( '.mld-word-comment-section' ).show();
                            $( '.mld-word-comment-section' ).html( jsonEncode.content );
                            $( '.mld-essay-comment-input' ).show();                            
                        }                        
                    } );
                } );
            },

            /**
             * change data when user click on back icon
             */ 
            changeData: function() {

                $( document ).on( 'click', '.mld-back-btn', function() {

                    $( '.mld-work-second-wrapper' ).remove();
                    $( '.mld-back-btn' ).remove();
                    $( '.mld-work-main-wrapper' ).show();
                } );
            },

            /**
             * proceed to quiz detail
             */
            proceedToQuizDetail: function() {

                $( document ).on( 'click', '.mld-work-submit-wrapper button', function() {

                    let quizID = $( '.mld-select-test-quiz' ).val();
                    let userID = $( '.mld-select-user' ).val();
                    
                    if( ! userID ) {
                        userID = $( '.mld-hidden-work-user-id' ).val();
                    }
                    
                    if( ! quizID ) {
                        return false;
                    }

                    $( '.mld-comment-loader' ).show();

                    let data = {
                        'action'                    : 'proceed_to_quiz_detail',
                        'mld_nounce'                : MLD.security,
                        'quiz_id'                   : quizID,
                        'user_id'                   : userID
                    };

                    jQuery.post( MLD.ajaxURL, data, function( response ) {

                        let jsonEncode = JSON.parse( response );

                        if( jsonEncode.status == 'true' ) {
                            $( '.mld-comment-loader' ).hide();
                            $( '.mld-work-main-wrapper' ).hide();
                            $( '.mld-second-part-main-wrapper' ).html( jsonEncode.content );
                        }
                    } );
                } );
            },

            /**
             * topic on change
             */
            topicOnChange: function() {

                $( document ).on( 'change', '.mld-select-topic', function() {

                    let self = $(this);
                    let topicID = self.val();
                    let courseID = $( '.mld-select-course' ).val();
                    let userID = $( '.mld-select-user' ).val();

                    let data = {
                        'action'                    : 'get_work_topic_id',
                        'mld_nounce'                : MLD.security,
                        'topic_id'                  : topicID,
                        'course_id'                 : courseID,
                        'user_id'                   : userID
                    };

                    jQuery.post( MLD.ajaxURL, data, function( response ) {

                        let jsonEncode = JSON.parse( response );

                        if( jsonEncode.status == 'true' ) {
                            $( '.mld-select-test-quiz' ).html( jsonEncode.content );
                        }
                    } );
                } );
            },

            /**
             * lesson on change
             */
            lessonOnChange: function() {

                $( document ).on( 'change', '.mld-select-lesson', function() {

                    let self = $(this);
                    let lessonID = self.val();
                    let courseID = $( '.mld-select-course' ).val();
                    let userID = $( '.mld-select-user' ).val();

                    let data = {
                        'action'                    : 'get_work_lesson_id',
                        'mld_nounce'                : MLD.security,
                        'lesson_id'                 : lessonID,
                        'course_id'                 : courseID,
                        'user_id'                   : userID
                    };

                    jQuery.post( MLD.ajaxURL, data, function( response ) {

                        let jsonEncode = JSON.parse( response );

                        if( jsonEncode.status == 'true' ) {
                            $( '.mld-select-topic' ).html( jsonEncode.content );
                            $( '.mld-select-test-quiz' ).html( jsonEncode.quiz_content );
                        }
                    } );
                } );
            },

            /**
             * course on change
             */
            courseOnChange: function() {

                $( document ).on( 'change', '.mld-select-course', function() {

                    let self = $(this);
                    let courseID = self.val();
                    let userID = $( '.mld-select-user' ).val();

                    let data = {
                        'action'                    : 'get_work_course_id',
                        'mld_nounce'                : MLD.security,
                        'course_id'                 : courseID,
                        'user_id'                   : userID
                    };

                    jQuery.post( MLD.ajaxURL, data, function( response ) {

                        let jsonEncode = JSON.parse( response );

                        if( jsonEncode.status == 'true' ) {
                            $( '.mld-select-lesson' ).html( jsonEncode.content );
                            $( '.mld-select-test-quiz' ).html( jsonEncode.quiz_content );
                        }
                    } );
                } );
            },

            /**
             * user on change
             */ 
            userOnChange: function() {

                $( document ).on( 'change', '.mld-select-user', function() {

                    let self = $(this);
                    let userID = self.val();
                    let groupID = $( '.mld-select-group' ).val();

                    let data = {
                        'action'                : 'get_work_user_id',
                        'mld_nounce'            : MLD.security,
                        'user_id'               : userID,
                        'group_id'              : groupID    
                    };

                    jQuery.post( MLD.ajaxURL, data, function( response ) {

                        let jsonEncode = JSON.parse( response );

                        if( jsonEncode.status == 'true' ) {
                            $( '.mld-select-course' ).html( jsonEncode.content );
                        }
                    } );
                } );
            },

            /**
             * group onchange 
             */
            groupOnChange: function() {

                $( document ).on( 'change', '.mld-select-group', function() {

                    let self = $(this);
                    let groupID = self.val();

                    let data = {
                        'action'                : 'get_work_group_id',
                        'mld_nounce'            : MLD.security,
                        'group_id'              : groupID
                    };
                    
                    jQuery.post( MLD.ajaxURL, data, function( response ) {
                        
                        let jsonEncode = JSON.parse( response );

                        if( jsonEncode.status == 'true' ) {
                            $( '.mld-select-user' ).html( jsonEncode.content );
                        }
                    } );
                } );
            },
        };

        MLD_WORK_FRONTEND.init();
    });
})( jQuery );