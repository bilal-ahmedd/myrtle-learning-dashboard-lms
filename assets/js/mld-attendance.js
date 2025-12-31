(function( $ ) { 'use strict';

    $( document ).ready( function() {

        var MLD_ATTENDANCE = {

            init: function() {
                this.groupOnChange();
                // this.courseOnChange();
                this.loadAttendanceTable();
                this.attendanceStatus();
                this.applySelect2();
                this.excludeOnChange();
                this.includeOnChange();
                this.openAttendanceCommentPopup();
                this.closeAttendancePopup();
                this.updateAttendance();
                this.filterOnChange();
                this.backFilterData();
                this.attendanceActiveTab();
                this.addAttendanceAttr();
            },

            /**
             * add attendance attr
             */
            addAttendanceAttr: function() {

                $( document ).on( 'click', '.mld-attendance', function() {

                    let isAttendanceEnabled = $( '.mld-attendance-enable' ).val();
                    if( 'yes' != isAttendanceEnabled ) {
                        return false;
                    }
                    let self = $(this);
                    let attendanceData = self.attr( 'data-attendance' );
                    self.parents( '.mld-attendace-data-row' ).attr( 'attendance-status', attendanceData );
                    self.parents( '.mld-attendace-data-row' ).attr( 'attendance-user_id', self.attr( 'data-user_id' ) );
                    self.parents( '.mld-attendace-data-row' ).attr( 'attendance-date', self.attr( 'data-attendance_date' ) );
                    self.parents( '.mld-attendace-data-row' ).find( 'button' ).removeAttr( 'style' );
                    self.css( 'background-color', '#18440a' );
                } );    
            },

            /**
             * attendance tab
             */
            attendanceActiveTab: function() {

                $(document).on('click', '.mld-user-attendance, .mld-teacher-attendance', function() {
                    $('.mld-user-attendance, .mld-teacher-attendance').removeClass('attendance-active');
                    $(this).addClass('attendance-active');
                    let attendanceClass = $(this).attr( 'class' );

                    if( $(this).hasClass( 'mld-teacher-attendance' ) ) {
                        $( '.mld-attendance-type' ).val( 'teacher-attendance' );
                    } else {
                        $( '.mld-attendance-type' ).val( 'user-attendance' );
                    }
                } );
            },

            /**
             * back filter data
             */
            backFilterData: function() {

                $( document ).on( 'click', '.mld-attendance-back-btn', function() {
                    $( '.mld-attendance-back-btn' ).slideUp();
                    $( '.mld-attendance-filter-wrapper' ).slideUp();
                    $( '.mld-second-part-main-wrapper' ).slideUp();
                    $( '.mld-attendance-pagination' ).slideUp();
                    $( '.mld-attendance-main-wrapper' ).slideDown();
                    $( '.mld-submit-btn' ).hide();
                    $( '.mld-start-page' ).text( 0 );
                    $( '.mld-end-page' ).text( 0 );
                    $( '.mld-attendance-filter' ).val( '' );
                } );
            },

            /**
             * filter on change
             */
            filterOnChange: function() {

                $( document ).on( 'change', '.mld-attendance-filter', function() {

                    let Val = $(this).val();

                    if( ! Val ) {
                        return false;
                    }

                    $( '.mld-filter-btn' ).show();
                    $( '.mld-no-attendance-found' ).remove();
                    if( 'custom-date' == Val ) {
                        $( '.mld-attendance-start-date' ).show();
                        $( '.mld-attendance-end-date' ).show();
                    } else {
                        $( '.mld-attendance-start-date' ).hide();
                        $( '.mld-attendance-end-date' ).hide();
                    }
                } );
            },

            /**
             * update attendance
             */
            updateAttendance: function() {

                $( document ).on( 'click', '.mld-submit-btn', function() {

                    $( '.mld-submit-btn' ).text( 'Submit...' );

                    let attendanceArray = [];
                    $.each( $( '.mld-attendace-data-row' ), function( index, elem ) {
                        
                        let attendance = $(elem).attr( 'attendance-status' );
                        let userID = $(elem).attr( 'attendance-user_id' )
                        let date = $(elem).attr( 'attendance-date' );
                        let comment = $(elem).find( '.mld-attendance-textarea' ).val();
                        let numberOfHours = $(elem).find('.mld-attendance-hours').val();

                        // Create an object and push to the array

                        if( attendance && userID && 0 != userID ) {
                            
                            attendanceArray.push({
                                attendance: attendance,
                                userID: userID,
                                date: date,
                                comment: comment,
                                hours: numberOfHours
                            });
                        }
                    } );

                    let teacherID = $( '.mld-teacher-id' ).val();
                    let groupID = $( '.mld-attendance-field .mld-select-group' ).val();
                    let courseID = $( '.mld-attendance-field .mld-select-course' ).val();
                    let attendanceType = $( '.mld-attendance-type' ).val();

                    let data = {
                        'action'                    : 'update_attendance',
                        'mld_nounce'                : MLD.security,
                        'group_id'                  : groupID,
                        'course_id'                 : courseID,
                        'teacher_id'                : teacherID,
                        'data'                      : attendanceArray,
                        'attendance_type'           : attendanceType         
                    };

                    jQuery.post( MLD.ajaxURL, data, function( response ) {
                        $( '.mld-submit-btn' ).text( 'Submit' );
                        alert( 'The attendance has been successfully updated' );
                    } );
               } );
            },

            /**
             * close attendance popup
             */
            closeAttendancePopup: function() {

                $( document ).on( 'click', '.mld-attendance-close-icon,.mld-attendance-comment-btn button', function() {
                    $( '.mld-pop-outer' ).slideUp( '500' );
                } );
            },

            /**
             * open attendance comment box
             */
            openAttendanceCommentPopup: function() {

                $( document ).on( 'click', '.mld-attendance-comment', function() {

                    let self = $(this);
                    let parent = self.parents( '.mld-attendace-data-row' ).find( '.mld-pop-outer' ).slideDown( '500' );
                    $( '.mld-pop-inner' ).css( 'height', '300px' );
                    
                    let groupID = self.attr( 'data-group_id' );
                    let courseID = self.attr( 'data-course_id' );
                    let studentID = self.attr( 'data-student_id' );
                    let dateTime = self.attr( 'data-date_time' );
                    
                    $( '.mld-attendance-comment-btn button' ).attr( 'data-group_id', groupID );
                    $( '.mld-attendance-comment-btn button' ).attr( 'data-course_id', courseID );
                    $( '.mld-attendance-comment-btn button' ).attr( 'data-student_id', studentID );
                    $( '.mld-attendance-comment-btn button' ).attr( 'data-date_time', dateTime );
                
                    let data = {
                        'action'                    : 'get_attendance_comment',
                        'mld_nounce'                : MLD.security,
                        'student_id'                : studentID,
                        'date_time'                 : dateTime
                    };

                    jQuery.post( MLD.ajaxURL, data, function( response ) {

                        let jsonEncode = JSON.parse( response );

                        if( jsonEncode.status == 'true' ) {
                            if( jsonEncode.content ) {
                                $( '.mld-attendance-textarea' ).val( jsonEncode.content );
                            }
                        }
                    } );

                } );
            },

            /**
             * include on change
             */
            includeOnChange: function() {

                $( document ).on( 'change', '.mld-select-exclude-user', function() {
                    $( '.mld-include-user .select2-selection__choice' ).remove();
                } );
            },

            /**
             * exclude on change
             */
            excludeOnChange: function() {

                $( document ).on( 'change', '.mld-select-include-user', function() {
                    $( '.mld-exclude-user .select2-selection__choice' ).remove();
                } );
            },

            /**
             * apply select 2
             */
            applySelect2: function() {

                $( '.mld-select-exclude-user,.mld-select-include-user' ).select2();
            },

            /**
             * update attendance
             */
            attendanceStatus: function() {

                $( document ).on( 'click', '.attendance-btn', function() {

                    let self = $(this);
                    self.parents( 'td' ).find( '.attendance-btn' ).removeAttr( 'style' );
                    $( self ).css( 'background-color', '#18440a' );
                    $( self ).css( 'color', '#ffffff' );
                    $( self ).parents( 'tr' ).attr( 'data-attendance_status', self.attr( 'data-attendance' ) );
                } );
            },

            /**
             * load attendance table
             */
            loadAttendanceTable: function() {

                $( document ).on( 'click', '.mld-attendance-submit-wrapper button,.mld-forward-attendance,.mld-backward-attendance,.mld-filter-btn', function() {

                    let self = $(this);
                    $( '.mld-attendance-submit-wrapper img' ).show();
                    
                    let excludeUser = $( '.mld-select-exclude-user' ).val();
                    let includeUser = $( '.mld-select-include-user' ).val();
                    let groupID = $( '.mld-attendance-field .mld-select-group' ).val();
                    let courseID = $( '.mld-attendance-field .mld-select-course' ).val();
                    let attendanceType = $( '.mld-attendance-type' ).val();
                    let attendanceFilterValue = $( '.mld-attendance-filter' ).val();
                    let startDate = $( '.mld-attendance-start-date' ).val();
                    let endDate = $( '.mld-attendance-end-date' ).val();

                    let currentPage = $( '.mld-backward-attendance' ).attr( 'data-page' );
                    let nextPage = 0;
                    if( ! currentPage ) {
                        currentPage = 0;
                    }

                    if (self.hasClass('mld-forward-attendance') ) {
                        $( '.mld-pagination-loader' ).show();
                        nextPage = parseInt( currentPage ) + 1;
                    } else if( self.hasClass('mld-backward-attendance') ) {
                        $( '.mld-pagination-loader' ).show();
                        nextPage = parseInt( currentPage ) - 1;
                    } else {

                        if( ! currentPage ) {
                            nextPage = 1;
                        } else {
                            nextPage = currentPage;
                        }

                        if( self.hasClass( 'mld-filter-btn' ) || self.parents( '.mld-attendance-submit-wrapper' ) ) {
                            nextPage = 1;
                        }
                    }

                    $( '.mld-start-page' ).text( nextPage );
                    $( '.mld-backward-attendance' ).attr( 'data-page', nextPage );

                    if( self.hasClass( 'mld-filter-btn' ) ) {
                        $( '.mld-filter-loader' ).show();
                    }

                    let data = {
                        'action'                    : 'load_attendance_data',
                        'mld_nounce'                : MLD.security,
                        'group_id'                  : groupID,
                        'course_id'                 : courseID,
                        'exclude_user'              : excludeUser,
                        'include_user'              : includeUser,
                        'attendance_type'           : attendanceType,
                        'page'                      : nextPage,
                        'attendance_filter'         : attendanceFilterValue,
                        'start_date'                : startDate,
                        'end_date'                  : endDate
                    };

                    jQuery.post( MLD.ajaxURL, data, function( response ) {

                        let jsonEncode = JSON.parse( response );

                        if( jsonEncode.status == 'true' ) {

                            $( '.mld-attendance-pagination' ).hide();
                            $( '.mld-pagination-loader' ).hide();
                            $( '.mld-filter-loader' ).hide();
                            $( '.mld-submit-btn' ).show();
                            $( '.mld-attendance-back-btn' ).slideDown();
                            $( '.mld-attendance-filter-wrapper' ).slideDown();
                            $( '.mld-second-part-main-wrapper' ).slideDown();
                            $( '.mld-attendance-main-wrapper' ).slideUp();
                            $( '.mld-attendance-filter-wrapper select' ).slideDown();
                            let src = $( '.mld-attendance-submit-wrapper img' ).attr( 'src' );
                            $( '.mld-second-part-main-wrapper' ).html( jsonEncode.content );
                            $( '.mld-attendance-submit-wrapper img' ).hide();
                            let count = $( '.data-count' ).val();

                            if( count > 1 ) {
                                $( '.mld-attendance-pagination' ).css( 'display', 'flex' );
                                $( '.mld-end-page' ).text( count );
                            }
                        }
                    } );
                } );
            },

            /**
             * course on change
             */
            courseOnChange: function() {

                $( document ).on( 'change', '.mld-attendance-field .mld-select-course', function() {

                    let self = $(this);
                    let courseID = self.val();
                    let userID = $( '.mld-attendance-field .mld-select-user' ).val();

                    let data = {
                        'action'                    : 'course_on_change',
                        'mld_nounce'                : MLD.security,
                        'course_id'                 : courseID,
                        'user_id'                   : userID
                    };

                    jQuery.post( MLD.ajaxURL, data, function( response ) {

                        let jsonEncode = JSON.parse( response );

                        if( jsonEncode.status == 'true' ) {
                            $( '.mld-attendance-field .mld-select-lesson' ).html( jsonEncode.content );
                            $( '.mld-attendance-field .mld-select-test-quiz' ).html( jsonEncode.quiz_content );
                        }
                    } );
                } );
            },

            /*
             * group on change
             */
            groupOnChange: function() {

                setTimeout( function() {
                    $( '.mld-exclude-user .select2-container--default' ).removeAttr( 'style' );
                    $( '.mld-include-user .select2-container--default' ).removeAttr( 'style' );
                }, 50);

                $( document ).on( 'change', '.mld-attendance-field .mld-select-group', function() {

                    $( '.mld-attendance-second-row' ).slideDown( '500' );
                    let self = $(this);
                    let groupID = self.val();
                    let teacherID = $( '.mld-teacher-id' ).val();
                    let attendanceType = $( '.mld-attendance-type' ).val();
                    
                    if( 'teacher-attendance' == attendanceType ) {
                        $( '.mld-exclude-user' ).prev().text( 'Exclude Teacher' );
                        $( '.mld-include-user' ).prev().text( 'Include Teacher' );
                    } else {

                        $( '.mld-exclude-user' ).prev().text( 'Exclude User' );
                        $( '.mld-include-user' ).prev().text( 'Include User' );
                    }

                    let data = {
                        'action'                : 'group_on_change',
                        'mld_nounce'            : MLD.security,
                        'group_id'              : groupID,
                        'teacher_id'            : teacherID,
                        'attendance_type'       : attendanceType
                    };
                    
                    jQuery.post( MLD.ajaxURL, data, function( response ) {

                        let jsonEncode = JSON.parse( response );

                        if( jsonEncode.status == 'true' ) {
                            $( '.mld-attendance-field .mld-select-course' ).html( jsonEncode.course_content );
                            $( '.mld-attendance-field .mld-select-exclude-user' ).html( jsonEncode.content );
                            $( '.mld-attendance-field .mld-select-include-user' ).html( jsonEncode.content );
                        }
                        // let jsonEncode = JSON.parse( response );

                        // if( jsonEncode.status == 'true' ) {
                        //     $( '.mld-attendance-field .mld-select-course' ).html( jsonEncode.course_content );
                        //     $( '.mld-attendance-field .mld-select-exclude-user' ).html( jsonEncode.content );
                        //     $( '.mld-attendance-field .mld-select-include-user' ).html( jsonEncode.content );
                        // }
                    } );
                } ); 
            }
        };
        MLD_ATTENDANCE.init();
    });
})( jQuery );
