(function( $ ) { 'use strict';

    $( document ).ready( function() {

        var MLD_REPORT = {

            init: function() {

                this.displayCourseAccordingToGroup();
                this.enabledApplyButton();
                this.createTable();
                this.userDetailReport();
                this.arrangeAcademicComments();
                this.arrangeBehaviorComments();
                this.updateComments();
                this.updateQuizStatistics();
                this.backFullReport();
                this.showComments();
                this.setLocalStorage();
                this.showAssignments();
                this.approvedComment();
                this.updateAssignmentComments();
                this.awardAssignmentpoints();
                this.appendBehaviourCustomCommentBox();
                this.addBehaviourCustomComment();
                this.appendAcademicCustomComment();
                this.addAcademicCustomComment();
                this.closeCustomCommentOption();
                this.uploadPDFonComment();
                this.deleteComments();
                this.editAbleComment();
                /**
                 * user guage chart
                 */
                this.userGuageChart();
                this.changeUserGroupChart();
                this.userCourseDetail();
                this.backUserDetailReport();
                this.displayReportPopup();
                this.updateUrlAccordingTocomments();
                this.courseOnChange();
            },

            /**
             * change the pdg url according to courses
             */
            courseOnChange: function() {

                $( document ).on( 'change', '.mld-popup-course-dropdown', function() {

                    let val = $(this).val().toString();
                    let href = $( '.mld-report-continue a' ).attr( 'href' );
                    var urlParams = new URLSearchParams( href );
                    urlParams.set( 'mld_included_courses', val );
                    var updatedUrlString = '?' + urlParams.toString();
                    $( '.mld-report-continue a' ).attr( 'href', updatedUrlString );
                } );
            },

            /**
             * update the pdf url
             */
            updateUrlAccordingTocomments: function() {

                $( document ).on( 'click', '.mld-academic-check input', function() {

                    let val = '';
                    let self = $(this);
                    let href = $( '.mld-report-continue a' ).attr( 'href' );
                    
                    if( self.prop("checked") == true ) {
                        val = 'yes';
                    } else if( self.prop("checked") == false ) {
                        val = 'no';
                    }

                    var urlParams = new URLSearchParams( href );
                    urlParams.set( 'mld_academic', val );
                    var updatedUrlString = '?' + urlParams.toString();
                    $( '.mld-report-continue a' ).attr( 'href', updatedUrlString );
                } );

                $( document ).on( 'click', '.mld-behaviour-check input', function() {

                    let val = '';
                    let self = $(this);
                    let href = $( '.mld-report-continue a' ).attr( 'href' );
                    
                    if( self.prop("checked") == true ) {
                        val = 'yes';
                    } else if( self.prop("checked") == false ) {
                        val = 'no';
                    }

                    var urlParams = new URLSearchParams( href );
                    urlParams.set( 'mld_behaviour', val );
                    var updatedUrlString = '?' + urlParams.toString();
                    $( '.mld-report-continue a' ).attr( 'href', updatedUrlString );
                } );
            },

            /**
             * display report popup
             */
            displayReportPopup: function() {

                $( document ).on( 'click', '.mld-full-report-btn', function() {
                    $( '.mld-full-report-download .mld-pop-outer' ).show();
                    
                    $( '.mld-popup-course-dropdown' ).select2( {
                       placeholder: 'Select an option'
                    } );

                    $( '.mld-update-comments' ).click();
                } );

                $( document ).on( 'click', '.report-popup-closed span', function() {
                    $( '.mld-full-report-download .mld-pop-outer' ).hide();                    
                } );
            },

            /**
             * editable comments
             */
            editAbleComment: function() {

                $( document ).on( 'click', '.mld-comment-editable', function() {

                    let self = $(this);
                    let parent = self.parents( '.mld-comments' ).find( '.mld-comment-wrapper' ).attr( 'contenteditable', 'true' );
                    self.parents( '.mld-comments' ).find( '.mld-comment-wrapper' ).css( 'background-color', 'white' );
                } );
            },

            /**
             * Delete comments
             */
            deleteComments: function() {

                $( document ).on( 'click', '.mld-delete-comment', function() {

                    let deleteConfirmation = confirm("Are you sure you want to delete?");
                    
                    if( deleteConfirmation ) {

                        let self = $(this);
                        self.parents( '.mld-comments' ).remove();
                    }
                } );
            },

            /**
             * upload pdf on assignment comment
             */
            uploadPDFonComment: function() {

                $( document ).on( 'click', '.mld-comment-btn', function() {

                    setTimeout(function() { 
                        $( '.add_media' ).html( '<span class="dashicons dashicons-cloud-upload mld-upload-pdf"></span>' );
                        $.each( $( '.mld-upload-pdf' ), function( index, elem ) {
                            if( index > 0 ) {
                                $( elem ).hide();
                            }
                        } );
                    }, 200);

                } );

                $( document ).on( 'click', '.mld-comment-btn', function() {

                    var editorSettings = {

                        tinymce: {
                            toolbar: "",
                            textarea_rows: 1,
                        },
                        quicktags: {},
                        mediaButtons: true, 
                    };
                    wp.editor.initialize( 'mld-comment-box', editorSettings ); 
                });
            },

            /**
             * close custom comment option
             */
            closeCustomCommentOption: function() {

                $( document ).on( 'click', '.mld-custom-comment-close', function() {
                    let self = $( this );
                    self.parents( '.mld-new-comment-section' ).remove();
                } );
            },

            /**
             * add academic custom comment
             */
            addAcademicCustomComment: function() {

                $( document ).on( 'click', '.mld-add-new-academic-comment', function() {

                    let self = $(this);
                    let parent = self.parents( '.mld-new-comment-section' );
                    let customComment = parent.find( '.mld-custom-comment' ).val();
                    let html = '';

                    html +='<div class="mld-comments mld-academic-comments">';
                    html +='<i class="fa fa-trash mld-delete-comment" aria-hidden="true"></i>';
                    html +='<div class="mld-comment-wrapper">'+customComment+'</div>';
                    html +='<span class="dashicons dashicons-edit mld-comment-editable"></span>';
                    html +='<div class="dashicons dashicons-no-alt mld-user-academic-comment mld-comment-wrapper-icon"></div>';
                    html +='<div class="mld-clear-both"></div>';
                    html +='</div>';

                    $( '.mld-approved-academic-comment-section' ).append( html );
                    $( parent ).remove();
                } );
            },

            /**
             * append academic textarea
             */
            appendAcademicCustomComment: function() {
                $( document ).on( 'click', '.mld-add-academic-custom-comment', function() {

                    let self = $(this);
                    let parent = self.parents( '.mld-approved-academic-comment-section' );
                    if( ! parent.find( '.mld-new-comment-section' ).attr( 'class' ) ) {
                        $( '.mld-approved-academic-comment-section' ).append( '<div class="mld-new-comment-section"><div class="mld-custom-comment-wrapper"><div class="mld-custom-comment-close dashicons dashicons-no"></div><div class="mld-custom-comment-textarea"><textarea class="mld-custom-comment" rows="2" cols="15" placeholder="Enter Custom Comment..."></textarea></div><div class="mld-clear-both"></div></div><button class="mld-add-new-academic-comment">Add</button><div class="mld-clear-both"></div></div>' );
                    }
                } );
            },

            /**
             * Add Behaviour Custom Comment
             */
            addBehaviourCustomComment: function() {

                $( document ).on( 'click', '.mld-add-new-comment', function() {

                    let self = $(this);
                    let parent = self.parents( '.mld-new-comment-section' );
                    let customComment = parent.find( '.mld-custom-comment' ).val();
                    let html = '';
                    html +='<div class="mld-comments mld-behavior-comments">';
                    html +='<i class="fa fa-trash mld-delete-comment" aria-hidden="true"></i>';
                    html +='<div class="mld-comment-wrapper">'+customComment+'</div>';
                    html +='<span class="dashicons dashicons-edit mld-comment-editable"></span>';
                    html +='<div class="dashicons dashicons-no-alt mld-user-behavior-comment mld-comment-wrapper-icon"></div>';
                    html +='<div class="mld-clear-both"></div>';
                    html +='</div>';
                    $( '.mld-approved-behavior-comments-section' ).append( html );
                    $( parent ).remove();
                } );
            },

            /**
             * Add custom Behaviour comment
             */
            appendBehaviourCustomCommentBox: function() {

                $( document ).on( 'click', '.mld-add-behaviour-custom-comment', function() {

                    let self = $(this);
                    let parent = self.parents( '.mld-approved-behavior-comments-section' );

                    if( ! parent.find( '.mld-new-comment-section' ).attr( 'class' ) ) {
                        $( '.mld-approved-behavior-comments-section' ).append( '<div class="mld-new-comment-section"><div class="mld-custom-comment-wrapper"><div class="mld-custom-comment-close dashicons dashicons-no"></div><div class="mld-custom-comment-textarea"><textarea class="mld-custom-comment" rows="2" cols="15" placeholder="Enter Custom Comment..."></textarea></div><div class="mld-clear-both"></div></div><button class="mld-add-new-comment">Add</button><div class="mld-clear-both"></div></div>' );
                    }
                } );
            },

            /**
             * Award assignments points
             */
            awardAssignmentpoints: function() {

                $( document ).on( 'click', '.update_assignment_point', function() {

                    let self = $(this);
                    let assignmentID = self.data( 'assignment_id' );
                    let point = $( document ).find( '.mld-assignment-point' ).val();
                    if( ! assignmentID || ! point ) {
                        return false;
                    }

                    let data = {
                        'action'               : 'update_assignment_points',
                        'mld_nounce'           : MLD.security,
                        'post_id'              : assignmentID,
                        'point'                : point
                    };

                    jQuery.post( MLD.ajaxURL, data, function( response ) {

                        let jsonEncode = JSON.parse( response );

                        if( jsonEncode.status == 'false' ) {

                            let message = jsonEncode.message;
                            $( '.mld-point-error-message' ).text( message );
                            $( '.mld-point-error-message' ).show();
                        }

                        if( jsonEncode.status == 'true' ) {

                            $( '.mld-point-error-message' ).text( 'Point award successfully...' );
                            $( '.mld-point-error-message' ).css( 'color', 'green' );
                            $( '.mld-point-error-message' ).show();

                            setTimeout( function() {
                                let approvedValue = $( '.mld-is-assignment-approved' ).val();
                                $( '.mld-assignment-options-wrapper' ).remove();
                                $(".mld-pop-outer").fadeOut("slow");

                                if( approvedValue ) {
                                    $( '.mld-aproved-comment-'+assignmentID ).removeClass( 'mld-approve-text-class' );
                                    $( '.mld-aproved-comment-'+assignmentID ).addClass( 'mld-approved-text-class' );
                                    $( '.mld-aproved-comment-'+assignmentID ).text( 'REVIEWED' );
                                }
                            }, 2000);
                        }
                    } );
                } );
            },

            /**
             * update Assignment comment
             */
            updateAssignmentComments: function() {

                /**
                 * get comments
                 */
                $( document ).on( 'click', '.mld-assignment-grading-comment button', function() {

                    let self = $(this);
                    $( ".mld-pop-outer" ).fadeIn( "slow" );
                    $( '.mld-popup-header .mld-header-title' ).text( 'Assignment Comment(s)' );
                    $( '.mld-assignment-input-wrap' ).show();
                    $( '.mld-assignment-comment-wrap' ).show();
                    let assignmetID = self.data( 'assignment_id' );
                    $( '.mld-assignment-submit button' ).attr( 'assignment_id', assignmetID );
                    if( ! assignmetID ) {
                        return;
                    }

                    $( '.mld-pop-inner' ).css( 'width', '600px' );
                    $( '.mld-pop-inner' ).css( 'height', '400px' );

                    let data = {
                        'action'               : 'get_comments',
                        'mld_nounce'           : MLD.security,
                        'post_id'              : assignmetID
                    };
                    jQuery.post( MLD.ajaxURL, data, function( response ) {

                        let jsonEncode = JSON.parse( response );

                        if( jsonEncode.status == 'true' ) {
                            $( '.mld-pop-inner .mld-assignment-comment-wrap' ).html( jsonEncode.content );
                        }
                    } );
                } );

                /**
                 * close comment popup
                 */
                $( document ).on( 'click', '.mld-close', function() {

                    let approvedValue = $( '.mld-is-assignment-approved' ).val();
                    $(".mld-pop-outer").fadeOut("slow");
                    $( '.mld-assignment-options-wrapper' ).remove();
                    let self = $(this);
                    let assignmentID = self.attr( 'data_assignment-id' );

                    if( approvedValue ) {
                        $( '.mld-aproved-comment-'+assignmentID ).removeClass( 'mld-approve-text-class' );
                        $( '.mld-aproved-comment-'+assignmentID ).addClass( 'mld-approved-text-class' );
                        $( '.mld-aproved-comment-'+assignmentID ).text( 'REVIEWED' );
                    }
                } );

                /**
                 * update comment
                 */
                $( document ).on( 'click', '.mld-assignment-submit button', function() {

                    let self = $(this);
                    let assignmentID = self.attr( 'assignment_id' );
                    let comment = tinymce.get( 'mld-comment-box' ).getContent();

                    if( ! comment || ! assignmentID ) {
                        return;
                    }

                    let html = '<div class="mld-chat-msg-wrap">' +
                    '<div class="mld-sender-wrapper mld-sender-msg">' +
                    '<div class="mld-user-chat mld-sender">'+comment+'</div>' +
                    '</div>' +
                    '<div class="mld-clear-both"></div>' +
                    '</div>';

                    tinymce.get('mld-comment-box').setContent( '' );

                    $( '.mld-assignment-comment-wrap' ).last().append( html );

                    let data = {
                        'action'               : 'update_comments',
                        'mld_nounce'           : MLD.security,
                        'post_id'              : assignmentID,
                        'comment'              : comment
                    };

                    jQuery.post( MLD.ajaxURL, data, function( response ) {} );
                } );
            },

            /**
             * approved comment
             */
            approvedComment: function() {

                $( document ).on( 'click', '.mld-aproved-comment', function() {

                    $( ".mld-pop-outer" ).fadeIn( "slow" );
                    let self = $(this);
                    let assignmetID = self.data( 'assignment_id' );
                    if( ! assignmetID ) {
                        return;
                    }

                    $( '.mld-popup-header .mld-header-title' ).text( 'Assignment Options' );
                    $( '.mld-assignment-input-wrap' ).hide();
                    $( '.mld-assignment-comment-wrap' ).hide();
                    $( '.mld-pop-inner' ).css( 'height', '300px' );
                    $( '.mld-pop-inner' ).css( 'width', '500px' );

                        let id = self.data( 'assignment_id' );
                        let data = {
                            'action'          : 'update_ld_assignments',
                            'mld_nounce'      : MLD.security,
                            'id'              : id
                        };
                        jQuery.post( MLD.ajaxURL, data, function( response ) {
                            let jsonEncode = JSON.parse( response );

                            if( jsonEncode.status = 'true' ) {
                                $( '.mld-popup-header' ).after( jsonEncode.content );
                            }
                        } );
                } );

                $( document ).on( 'click', '.mld-aproved-comment-action', function() {

                    let self = $(this);
                    let assignment_id = self.data( 'assignment_id' );

                    if( ! assignment_id ) {
                        return false;
                    }

                    let data = {
                        'action'          : 'approved_ld_assignments',
                        'mld_nounce'      : MLD.security,
                        'id'              : assignment_id
                    };

                    jQuery.post( MLD.ajaxURL, data, function( response ) {

                        let jsonEncode = JSON.parse( response );

                        if( jsonEncode.status == 'true' ) {
                            $( '.mld-assignment-approv-wrap .mld-approve-content' ).html( '<div class="mld-aproved">Approved</div>' );
                            $( '.mld-is-assignment-approved' ).val( '1' );
                        }
                    } );
                } );
            },

            /**
             * back user detail report
             */
            backUserDetailReport: function() {

                $( document ).on( 'click', '.mld-back-user-report', function() {

                    $( '.mld-download-report' ).show();
                    $( '.mld-my-report' ).show();
                    $( '.mld-report-table-wrapper' ).show();
                    $( '.mld-report-warpper' ).show();
                    $( '.mld-user-course-detail-wrapper' ).hide();
                } );
            },

            /**
             * get user course detail
             */
            userCourseDetail: function() {

                $( document ).on( 'click', '.mld_user_detail_report', function() {

                    let self = $( this );
                    let groupid = $( '.mld-groups-dropdown' ).val();
                    let startDate = $( '.mld-start-date' ).val();
                    let endDate = $( '.mld-end-date' ).val();
                    let courseId = self.data( 'course_id' );
                    $( '.mld-report-loader' ).show();

                    let data = {
                        'action'          : 'get_user_course_detail',
                        'mld_nounce'      : MLD.security,
                        'group_id'        : groupid,
                        'start_date'      : startDate,
                        'end_date'        : endDate,
                        'course_id'       : courseId
                    };

                    jQuery.post( MLD.ajaxURL, data, function( response ) {

                        let jsonEncode = JSON.parse( response );

                        if( 'true' == jsonEncode.status ) {

                            $( '.mld-report-loader' ).hide();
                            $( '.mld-download-report' ).hide();
                            $( '.mld-my-report' ).hide();
                            $( '.mld-report-table-wrapper' ).hide();
                            $( '.mld-report-warpper' ).hide();
                            $( '.mld-user-course-detail-wrapper' ).show();

                            let uCoursePro = jsonEncode.user_course_progress;
                            let CcohortPro = jsonEncode.course_cohort_progress;
                            let CtargetPro = jsonEncode.course_target_progress;
                            let Ctitles = jsonEncode.titles;
                            let userAverageScore = parseInt( jsonEncode.user_group_score_average );
                            let userCohortAverageScore = parseInt( jsonEncode.user_cohort_average );
                            let groupTargetScoure = parseInt( jsonEncode.group_target_average );

                            $( '.mld-user-course-detail-wrapper' ).html( jsonEncode.content );
                            MLD_REPORT.createChart( uCoursePro, CtargetPro, CcohortPro, Ctitles );

                            if( 0 == uCoursePro || null == uCoursePro ) {
                                uCoursePro = 10;
                            }

                            if( 0 == userCohortAverageScore || null == userCohortAverageScore ) {
                                userCohortAverageScore = 10;
                            }

                            if( 0 == userAverageScore || null == userAverageScore ) {
                                userAverageScore = 10;
                            }

                            MLD_REPORT.createUserChart( userAverageScore, 'mld-student-average-wrapper', -60, groupTargetScoure );
                            MLD_REPORT.createUserChart( userCohortAverageScore, 'mld-cohort-average-wrapper', -60, groupTargetScoure );
                        }
                    } );
                } );
            },

            /**
             * create user guage chart function
             */
            createUserChart: function( average, ID, radius, targetScore ) {
                am4core.useTheme(am4themes_animated);
                // create chart
                var chart = am4core.create( ID, am4charts.GaugeChart);
                chart.innerRadius = radius;
                chart.startAngle = -220;
                chart.endAngle = 40;

                var axis = chart.xAxes.push(new am4charts.ValueAxis());
                axis.min = 10;
                axis.max = 90;
                axis.strictMinMax = true;

                var range0 = axis.axisRanges.create();
                range0.value = 0;
                range0.endValue = 20;
                range0.axisFill.fillOpacity = 1;
                range0.axisFill.fill = '#ff1b01';

                var range1 = axis.axisRanges.create();
                range1.value = 20;
                range1.endValue = targetScore;
                range1.axisFill.fillOpacity = 1;
                range1.axisFill.fill = '#ffb307';

                var range2 = axis.axisRanges.create();
                range2.value = targetScore;
                range2.endValue = 100;
                range2.axisFill.fillOpacity = 1;
                range2.axisFill.fill = '#325648';

                var hand = chart.hands.push(new am4charts.ClockHand());

                setInterval(() => {
                    hand.showValue( average, 1000, am4core.ease.cubicOut);
                }, 500);

                /**
                 * Hide chart promote link
                 */
                $( 'title' ).parent( 'g' ).remove();
            },

            /**
             * change user chart according to group
             */
            changeUserGroupChart: function() {

                $( document ).on( 'change', '.mld-user-guage-graph', function() {

                    let self = $( this );
                    let groupID = self.val();

                    let data = {
                        'action'          : 'get_user_group_chart',
                        'mld_nounce'      : MLD.security,
                        'group_id'        : groupID
                    };

                    jQuery.post( MLD.ajaxURL, data, function( response ) {

                        let jsonEncode = JSON.parse( response );

                        if( 'true' == jsonEncode.status ) {

                            let userPercentage = parseInt( jsonEncode.average );
                            let groupTarget = parseInt( jsonEncode.target );
                            $( '.mld-user-group-percentage' ).val( userPercentage );
                            $( '.mld-course-group-target' ).val( groupTarget );

                            if( 0 == userPercentage ) {
                                userPercentage = 10;
                            }
                            MLD_REPORT.createUserChart( userPercentage, 'mld-user-chart', -40, groupTarget );
                        }
                    } );
                } );
            },

            /**
             * create user guage chart
             */
            userGuageChart: function() {

                if( $( 'select' ).hasClass( 'mld-user-guage-graph' ) ) {
                    let average = parseInt( $( '.mld-user-group-percentage' ).val() );
                    let targetAverage = parseInt( $( '.mld-course-group-target' ).val() );

                    if( 0 == average || null == average ) {
                        average = 10;
                    }
                    MLD_REPORT.createUserChart( average, 'mld-user-chart', -40, targetAverage );
                }
            },

            showAssignments: function() {

                $( document ).on( 'click', '.mld-assignment-wrapper-icon', function() {

                    let self = $( this );
                    if( self.hasClass( 'dashicons-insert' ) ) {
                        self.removeClass( 'dashicons-insert' );
                        self.addClass( 'dashicons-remove' );
                        let assignmentCount = $( '.mld-assignment-count' ).val();
                        if( assignmentCount > 4 ) {
                            $( '.mld-file-data-wrapper' ).addClass( 'mld-assignment-data' );
                        }
                    } else {
                        self.removeClass( 'dashicons-remove' );
                        self.addClass( 'dashicons-insert' );
                        $( '.mld-file-data-wrapper' ).removeClass( 'mld-assignment-data' );
                    }

                    $( '.mld-assignment-content' ).slideToggle();
                    $( '.mld-assignment-date' ).slideToggle();
                    $( '.mld-assignment-grading' ).slideToggle();
                    $( '.mld-assignment-grading-comment' ).slideToggle();
                } );
            },

            /**
             * set local storage
             */
            setLocalStorage: function() {

                $( document ).on( 'click', '.mld-groups', function() {

                    let self = $( this );
                    let pageUrl = self.data( 'page_url' );
                    var groupId = self.data( 'group_id' );
                    localStorage.setItem( 'GroupId', groupId );
                    window.location.replace( pageUrl );
                } );

                let storageData = localStorage.getItem( 'GroupId' );

                if( storageData ) {
                    $( '.mld-groups-dropdown' ).val( storageData );

                    let data = {
                        'action'          : 'get_group_courses',
                        'mld_nounce'      : MLD.security,
                        'group_id'        : storageData
                    };

                    jQuery.post( MLD.ajaxURL, data, function( response ) {

                        let jsonEncode = JSON.parse( response );

                        if( 'true' == jsonEncode.status ) {

                            $( '.mld-courses-dropdown' ).html( jsonEncode.content );
                            $( '.mld-report-submit' ).removeAttr( 'disabled' );
                            $( '.mld-courses-dropdown' ).removeAttr( 'disabled' );
                            $( '.mld-report-submit' ).css( 'cursor', 'pointer' );
                            $( '.mld-courses-dropdown' ).css( 'cursor', 'pointer' );
                            $( '.mld-report-submit' ).click();
                            localStorage.removeItem( 'GroupId' );
                        }
                    } );
                }
            },

            /**
             * Display comments on clicking plus icon
             */
            showComments: function() {

                $( document ).on( 'click', '.dashicons-plus-alt2', function() {

                    let self = $( this )
                    self.removeClass( 'dashicons dashicons-plus-alt2' );
                    let commentType = self.data( 'comment' );

                    let parentClass = 'mld-approved-behavior-comment';
                    if( 'mld-plus-academic' == commentType ) {
                        parentClass = 'mld-approved-academic-comment';
                    }
                    self.addClass( 'dashicons dashicons-yes '+parentClass );
                    let parent = self.parents( '.mld-comments-wrap' );
                    parent.find( '.mld-hide-comments' ).slideToggle();
                } );
            },

            backFullReport: function() {

                $( document ).on( 'click', '.mld-back-btn', function() {

                    $( '.mls-user-table-detail-report' ).hide();
                    $( '.mld-back-btn' ).hide();
                    $( '.mld-user-avatar' ).html( 'My Report' );
                    $( '.mld-report-table-wrapper' ).show();
                    $( '.mld-download-report' ).show();

                    $( '.mld-report-warpper' ).css( 'display', 'block' );

                    $( '.mld-user-avatar' ).css({
                        'font-size': '26px',
                        'font-weight' : 'bold',
                        'color' : '#18440a'
                    } );

                    let userName = $( '.mld-current-u-name' ).val();
                    let userAvatar = $( '.mld-current-u-avatar' ).val();
                    $( '.mld-header-avatar img' ).attr( 'src', userAvatar );
                    $( '.mld-header-avatar-name .mld-header-user-name' ).text( userName );
                } );
            },

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
                } );

                $( document ).on( 'click', '.user_statistic', function() {

                    if( $( document ).find( 'div' ).hasClass( 'mld-courses' ) ) {

                        let self = $( this );
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
             * update comments
             */
            updateComments: function() {

                $( document ).on( 'click', '.mld-update-comments', function() {

                    let self = $( this );
                    $( '.mld-comment-loader' ).show();
                    let userId = self.data( 'user_id' );
                    let groupId = self.data( 'group_id' );
                    let courseId = $( '.mld-courses-dropdown' ).val();

                    if( ! userId || ! groupId ) {
                        return;
                    }

                    let academicComment = [];

                    $.each( $( '.mld-academic-comments' ), function( index, elem ) {
                        academicComment[index] = $( elem ).find( '.mld-comment-wrapper' ).html();
                    } );

                    let behaviorComment = [];

                    $.each( $( '.mld-behavior-comments' ), function( index, elem ) {
                        behaviorComment[index] = $( elem ).find( '.mld-comment-wrapper' ).html();
                    } );

                    let approvedBehaviourComment = [];

                    $.each( $( '.mld-behave-class' ), function( index, elem ) {
                        approvedBehaviourComment[index] = $( elem ).find( '.mld-comment-wrapper' ).html();
                    } );

                    let approvedAcademicComment = [];

                    $.each( $( '.mld-acade-class' ), function( index, elem ) {
                        approvedAcademicComment[index] = $( elem ).find( '.mld-comment-wrapper' ).html();
                    } );

                    let data = {
                        'action'            : 'update_user_comment',
                        'mld_nounce'        : MLD.security,
                        'user_id'           : userId,
                        'group_id'          : groupId,
                        'academic_comments' : academicComment,
                        'behavior_comment'  : behaviorComment,
                        'approved_behaviour': approvedBehaviourComment,
                        'approved_academic' : approvedAcademicComment,
                        'course_id'         : courseId
                    };

                    jQuery.post( MLD.ajaxURL, data, function( response ) {

                        $( '.mld-comment-wrapper' ).css( 'background-color', '#f2f2f2' );
                        $( '.mld-comment-wrapper' ).removeAttr( 'contenteditable' );
                        $( '.mld-comment-loader' ).hide();
                    } );

                } );
            },

            /**
             * set behavior comment arrangment
             */
            arrangeBehaviorComments: function() {

                $( document ).on( 'click', '.mld-user-behavior-comment, .mld-approved-behavior-comment', function() {

                    let self = $( this );

                    let uniqueClass = 'mld-behavior-comments';
                    let behaviourClass = '';
                    let span = '<div class="dashicons dashicons-no-alt mld-user-behavior-comment mld-comment-wrapper-icon"></div>';
                    
                    if( $( self ).hasClass( 'dashicons-no-alt' ) ) {
                        uniqueClass = '';
                        behaviourClass = 'mld-behave-class';
                        span = '<div class="dashicons dashicons-yes mld-approved-behavior-comment mld-comment-wrapper-icon"></div>';
                    }

                    let val = self.parents( '.mld-comments' ).find( '.mld-comment-wrapper' ).html();
                    self.parents( '.mld-comments' ).remove();
                    let wantsComment = '';

                    wantsComment += '<div class="mld-comments '+behaviourClass+''+' '+''+uniqueClass+'">';
                    wantsComment += '<i class="fa fa-trash mld-delete-comment" aria-hidden="true"></i>';
                    wantsComment += '<div class="mld-comment-wrapper">'+val+'</div>';
                    wantsComment += '<span class="dashicons dashicons-edit mld-comment-editable"></span>';
                    wantsComment += span;
                    wantsComment += '<div class="mld-clear-both"></div>';
                    wantsComment += '</div>';

                    if( self.hasClass( 'mld-approved-behavior-comment' ) ) {
                        $( '.mld-approved-behavior-comments-section' ).append( wantsComment );
                    } else {
                        $( '.mld-behavior-comment' ).append( wantsComment );
                    }
                } )
            },

            /**
             *  set academic comment arrangement
             */
            arrangeAcademicComments: function() {

                $( document ).on( 'click', '.mld-approved-academic-comment, .mld-user-academic-comment', function() {

                    let self = $( this );

                    let uniqueClass = 'mld-academic-comments';
                    let academic_class = '';
                    let span = '<div class="dashicons dashicons-no-alt mld-user-academic-comment mld-comment-wrapper-icon"></div>';
                    if( $( self ).hasClass( 'dashicons-no-alt' ) ) {

                        uniqueClass = '';
                        academic_class = 'mld-acade-class';
                        span = '<div class="dashicons dashicons-yes mld-approved-academic-comment mld-comment-wrapper-icon"></div>';
                    }

                    let val = self.parents( '.mld-comments' ).find( '.mld-comment-wrapper' ).html();
                    self.parents( '.mld-comments' ).remove();
                    let wantsComment = '';

                    wantsComment += '<div class="mld-comments '+uniqueClass+''+' '+' '+academic_class+'">';
                    wantsComment += '<i class="fa fa-trash mld-delete-comment" aria-hidden="true"></i>';
                    wantsComment += '<div class="mld-comment-wrapper">'+val+'</div>';
                    wantsComment += '<span class="dashicons dashicons-edit mld-comment-editable"></span>';
                    wantsComment += span;
                    wantsComment += '<div class="mld-clear-both"></div>';
                    wantsComment += '</div>';

                    if( self.hasClass( 'mld-approved-academic-comment' ) ) {
                        $( '.mld-approved-academic-comment-section' ).append( wantsComment );
                    } else {
                        $( '.mld-academic-comment' ).append( wantsComment );
                    }
                } );
            },

            /**
             * create a function bar graph
             */
            createChart: function( avgScore, targetScore, cohortScore, titles ) {

                let cohortProgress = [];
                $.each( cohortScore, function( index, elem ) {
                    cohortProgress.push( -1 * elem );
                } );

                const data = {
                    labels: titles,
                    datasets: [{
                      label: 'Target Score',
                      type: 'line',
                      data: targetScore,
                      backgroundColor: '#32584a',
                      borderColor: '#32584a',
                      borderWidth: 1,
                      borderSkipped: false
                    },{
                      label: 'Student Score',
                      data: avgScore,
                      backgroundColor: '#39803e',
                      borderColor: '#39803e',
                      borderWidth: 1,
                      borderSkipped: false
                    },
                    {
                      label: 'Cohort Score',
                      data: cohortProgress,
                      backgroundColor: '#fbb11b',
                      borderColor: '#fbb11b',
                      borderWidth: 1,
                      borderSkipped: false
                    }]
                  };
                // rotated labels
                const rotatedLabels = {
                    id: 'rotatedLabels',
                    beforeDatasetsDraw( chart, args, pluginOptions ) {
                        const{ ctx,chartArea: { top,bottom,left,right,width,height }, scales: { x, y } } = chart;
                        ctx.save();
                        const angle = Math.PI / 180;
                        scalelabels( 'Student Score', 0, 60, '#39803e' );
                        scalelabels( 'Cohort Score', -60, 0, '#fbb11b' );
                        function scalelabels( text, bottomScale, topScale, color ) {
                            ctx.translate(0, 0);
                            ctx.rotate(270 * angle);
                            ctx.font = 'bold 10px sans-serif';
                            ctx.textAlign = 'center';
                            ctx.fillStyle = color;
                            ctx.fillText( text, (y.getPixelForValue(bottomScale) + y.getPixelForValue(topScale))/ -2, 5);
                            ctx.rotate(-270 * angle);
                        }
                        ctx.restore();
                    }
                }

                // config
                const config = {
                type: 'bar',
                data,
                options: {
                    layout: {
                      padding: {
                        left: 30
                      }
                    },
                    plugins: {
                    // legend: {
                    //     display: false
                    // },
                    datalabels: {
                        color: '#000',
                        formatter: (value, context) => {
                            return Math.abs( context.dataset.data[context.dataIndex] )+'%';
                        }
                    },
                    tooltip: {
                        callbacks: {
                        label: (context) => {
                            const nett = Math.abs(context.raw)
                            return `${context.dataset.label}: ${nett}`
                        }
                        }
                    }
                    },
                    scales: {
                        x: {
                            stacked: true,
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value, index, values) {
                                    return Math.abs(value)+'%';
                                }
                            }
                        }
                    }
                },
                plugins: [ChartDataLabels, rotatedLabels]
                };

                // render init block
                window.myBar = new Chart(
                    document.getElementById('canvas'),
                    config
                );
            },

            /**
             * create user detail report
             */
            userDetailReport: function() {

                $( document ).on( 'click', '.mld-user-report', function() {

                    let self = $( this );
                    let userId = self.data( 'user_id' );
                    let groupID = $( '.mld-groups-dropdown' ).val();
                    let courseId = self.data( 'course_id' );
                    let startDate = $( '.mld-start-date' ).val();
                    let endDate = $( '.mld-end-date' ).val();

                    if( ! userId || ! groupID ) {
                        return false;
                    }

                    $( '.mld-report-loader' ).show();

                    let data = {
                        'action'          : 'get_user_detail',
                        'mld_nounce'      : MLD.security,
                        'group_id'        : groupID,
                        'user_id'         : userId,
                        'course_id'       : courseId,
                        'start_date'      : startDate,
                        'end_date'        : endDate
                    };

                    jQuery.post( MLD.ajaxURL, data, function( response ) {

                        let jsonEncode = JSON.parse( response );

                        if( 'true' == jsonEncode.status ) {

                            $( '.mld-download-report' ).hide();
                            $( '.mld-report-warpper' ).css( 'display', 'none' );
                            $( '.mld-report-table-wrapper' ).hide();
                            let userAvatar = self.data( 'avatar_url' );
                            let userName = self.text();
                            $( '.mld-my-report' ).html( '<div class="mld-user-avatar-wrapper"><div class="mld-back-btn"><span class="dashicons dashicons-arrow-left-alt"></span><span class="mld-go-back">Go Back</span></div><div class="mld-user-avatar"><span><img src="'+userAvatar+'" class="mld-avatar"></span><span class="mld-user-star-wrap"><div class="mld-user-star"></div><div class="mld-user-name">'+userName+'</div></span></div><div class="mld-clear-both"></div></div>' );
                            $( '.mld-report-loader' ).hide();
                            $( '.mls-user-table-detail-report' ).html( jsonEncode.content );
                            $( '.mls-user-table-detail-report' ).show();
                            $( '.mld-header-avatar img' ).attr( 'src', userAvatar );
                            $( '.mld-header-avatar-name .mld-header-user-name' ).text( userName );
                            let parent = self.parents( '.mld-table-row' );
                            let academicStar = parent.find( '.mld-academic-star' ).data( 'academic_star' );
                            let behaviourStar = parent.find( '.mld-behaviour-star' ).data( 'behaviour_star' );
                            let starAddition = ( academicStar + behaviourStar );
                            let averageStar = 0;
                            if( starAddition > 0 ) {
                                averageStar = Math.round( ( academicStar + behaviourStar ) / 2 ) ;
                            }

                            let star = "";
                            for (let i = 0; i < averageStar; i++) {
                                star += "*";
                            }

                            $( '.mld-main-header-user-avatar .mld-current-user-star' ).html( star );
                            $( '.mld-user-star-wrap .mld-user-star' ).html( star );

                            let percentages = [];

                            $.each( $( '.mld-course-percentage' ), function( index, elem ) {

                                let percent = $(elem).text();
                                let removePercent = percent.replace('%','');
                                percentages.push( removePercent );
                            } );

                            let targetScore = jsonEncode.course_target;
                            let cohortScore = jsonEncode.course_cohort;
                            let titles = jsonEncode.course_titles;
                            MLD_REPORT.createChart( percentages, targetScore, cohortScore, titles );
                        }
                    } );
                } );
            },

            /**
             * Create user report table
             */
            createTable: function() {

                $( document ).on( 'click', '.mld-report-submit', function() {

                    let groupId = $( '.mld-groups-dropdown' ).val();
                    let courseId = $( '.mld-courses-dropdown' ).val();
                    let startDtae = $( '.mld-start-date' ).val();
                    let endDate = $( '.mld-end-date' ).val();
                    let isSubscriber = $( '.mld-user-role' ).val();

                    if( ! groupId ) {
                        return false;
                    }

                    $( '.mld-report-loader' ).show();

                    let data = {
                        'action'          : 'get_user_table',
                        'mld_nounce'      : MLD.security,
                        'group_id'        : groupId,
                        'course_id'       : courseId,
                        'strat_date'      : startDtae,
                        'end_start'       : endDate,
                        'user_id'         : isSubscriber
                    };

                    jQuery.post( MLD.ajaxURL, data, function( response ) {

                        let jsonEncode = JSON.parse( response );

                        if( 'true' == jsonEncode.status ) {
                            $( '.mld-report-loader' ).hide();
                            $( '.mld-report-data' ).html( jsonEncode.content );
                        }
                    } );

                } );
            },

            /**
             * Enabled Apply button
             */
            enabledApplyButton: function() {

                $( document ).on( 'change', '.mld-courses-dropdown', function() {

                    let self = $( this );
                    let Course = self.val();

                    if( ! Course ) {
                        return false;
                    }

                    $( '.mld-report-submit' ).removeAttr( 'disabled' );
                    $( '.mld-report-submit' ).css( 'cursor', 'pointer' );
                } );
            },

            /**
             * Display course according to ajax
             */
            displayCourseAccordingToGroup: function() {

                $( document ).on( 'change', '.mld-groups-dropdown', function() {

                    let self = $( this );

                    let groupId = self.val();

                    if( ! groupId ) {
                        return false;
                    }

                    $( '.mld-report-loader' ).show();

                    let data = {
                        'action'          : 'get_group_courses',
                        'mld_nounce'      : MLD.security,
                        'group_id'        : groupId
                    };

                    jQuery.post( MLD.ajaxURL, data, function( response ) {

                        let jsonEncode = JSON.parse( response );

                        if( 'true' == jsonEncode.status ) {

                            $( '.mld-report-loader' ).hide();
                            $( '.mld-courses-dropdown' ).html( jsonEncode.content );
                            $( '.mld-courses-dropdown' ).css( 'cursor', 'pointer' );
                            $( '.mld-courses-dropdown' ).removeAttr( 'disabled' );
                            $( '.mld-report-submit' ).removeAttr( 'disabled' );
                            $( '.mld-report-submit' ).css( 'cursor', 'pointer' );
                        }
                    } );
                } );
            },
        };
        MLD_REPORT.init();
    });
})( jQuery );
