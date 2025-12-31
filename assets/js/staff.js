(function( $ ) { 'use strict';

    $( document ).ready( function() {

        var MLD_STAFF = {

            init: function() {
                this.displayFormSecondPage();
                this.submitTeacherRegistrationForm();
                this.searchCourseLeaders();
                this.searchGroupLeadersByName();
                this.displayGroupLeaderFullDetail();
                this.backMainStaffPage();
                this.displayGroupLeaderPopup();
                this.applyRestrictionOnPersonalStatement();
                this.addNewRow();
                this.deleteTableRow();
                this.convertSelectIntoSelect2();
                this.addNewSubjects();
                this.teacherEmailPopup();
                this.sendEmailToAdmin();
                this.updateTeacherAvailibity();
                this.updateTeacherDBS();
                this.updateSubjectTaughtOption();
                this.displayTermsAndConditionPopup();
                this.submitApplicentForm();
                this.uncheckAllOtherCheckbox();
                // this.clickOutsideTheUsername();
                this.removeAlreadyExistsMessage();
            },

            /**
             * remove already exists message
             */
            removeAlreadyExistsMessage: function() {

                $( document ).on( 'keydown', '.mld-username', function() {

                    $( '.mld-username' ).css( 'border-color', 'unset' );
                    $( '.mld-user-exists-msg' ).remove();
                    $( '.mld-name-suggestion' ).remove();
                } );
            },

            /**
             * click outside the username
             */
            clickOutsideTheUsername: function() {

                $( document ).on( 'click', function() {

                    if ( $( event.target ).closest( ".mld-username" ).length === 0 ) {

                        let username = $( '.mld-username' ).val();

                        let data = {
                            'action'               : 'check_username',
                            'username'             : username,
                            'mld_nounce'           : MLD.security
                        };

                        jQuery.post( MLD.ajaxURL, data, function( response ) {

                            let jsonEncode = JSON.parse( response );
                            if( 'true' == jsonEncode.status ) {

                                $( '.mld-user-exists-msg' ).remove();
                                $( '.mld-name-suggestion' ).remove();
                                $( '.mld-username' ).css( 'border-color', 'red' );
                                $( '.mld-username' ).after( '<div class="mld-user-exists-msg">The user already exists. Please try again with a different username.</div>' );
                                $( '.mld-user-exists-msg' ).after( '<div class="mld-name-suggestion">for instance: '+jsonEncode.suggestion+'</div>' );
                            }
                        } );
                    }
                } );
            },

            /**
             * uncheck all other checkboxes
             */
            uncheckAllOtherCheckbox: function() {

                $( document ).on( 'change', '.mld-quality-of-work .mld-general, .mld-quantity-of-work .mld-general, .mld-job-dedication .mld-general, .mld-ability-of-work .mld-general, .mld-working-relationship .mld-general, .mld-time-keeping .mld-general', function() {

                    let self = $(this);
                    let parent = self.parents( 'tr' ).attr( 'class' );

                    if ( self.is(':checked') ) {
                        $( '.'+parent+' .mld-general' ).addClass( 'mld-general-check' );
                        self.removeClass( 'mld-general-check' );
                        $( '.'+parent+' .mld-general-check' ).prop( 'checked', false );
                    }
                } );
            },

            /**
             * submit applicent form
             */
            submitApplicentForm: function() {

                $( document ).on( 'click', '.mld-applicent-submit', function() {

                    let siteURL = $( '.mld-header-main-wrapper' ).attr( 'sitr-url' );
                    let email = $( '.mld-ref-email' ).val();
                    let userID = $( '.mld-user-id' ).val();

                    if( ! email || ! userID ) {
                        return false;
                    }

                    let submitStatus = $( '.mld-already-filled' ).val();
                    if( submitStatus ) {
                        $( '.mld-error' ).show();
                        return false;
                    }
                    $( '.mld-applicent-submit' ).val( 'SUBMIT...' );                    let applicentName = $( '.mld-form-applicent .mld-applicent-data' ).text();
                    let postApplied = $( '.mld-form-applicent .mld-applicent-data' ).text();
                    let jobTitle = $( '.mld-job-title .mld-job-title-data' ).text();
                    let mldTimePeriod = $( '.mld-time-period .mld-time-period-data' ).text();
                    let applicentCapacity = $( '.mld-applicent-capacity .mld-applicent-capacity-data' ).text();
                    let OrganizationTitle = $( '.mld-organization-title .mld-organization-title-data' ).text();
                    let jobDuties = $( '.mld-job-duties .mld-job-duties-data' ).text();
                    let LeavingReason = $( '.mld-leaving-reasons .mld-leaving-reasons-data' ).text();
                    let applicentSpecification = $( '.mld-applicent-specification .mld-applicent-specification-data' ).text();
                    let applicentWorkWithChildrenAnswer = $( '.mld-applicent-work-with-children-answer .mld-applicent-work-with-children-answer-data' ).text();
                    let applicentFurtherComment = $( '.mld-applicent-further-comment .mld-applicent-further-comment-data' ).text();
                    let experienceData = '';
                    if( $( '.mld-experience-yes' ).prop( 'checked' ) ) {
                        experienceData = 'yes';
                    } else {
                        experienceData = 'no';
                    }

                    let startDate = $( '.mld-start-date input' ).val();
                    let endDate = $( '.mld-end-date input' ).val();
                    let Salary = $( '.mld-salary input' ).val();
                    let trustworthyYes = '';
                    if( $( '.mld-trustworthy-yes' ).prop( 'checked' ) ) {
                        trustworthyYes = 'yes';
                    } else {
                        trustworthyYes = 'no';
                    }

                    let dutyCareYes = '';
                    if( $( '.mld-duty-care-yes' ).prop( 'checked' ) ) {
                        dutyCareYes = 'yes';
                    } else {
                        dutyCareYes = 'no';
                    }

                    let disciplinaryWarningsYes = '';
                    if( $( '.mld-disciplinary-warnings-yes' ).prop( 'checked' ) ) {
                        disciplinaryWarningsYes = 'yes';
                    } else {
                        disciplinaryWarningsYes = 'no';
                    }

                    let reEmployYes = '';
                    if( $( '.mld-re-employ-yes' ).prop( 'checked' ) ) {
                        reEmployYes = 'yes';
                    } else {
                        reEmployYes = 'no';
                    }

                    let jobDescribeYes = '';
                    if( $( '.mld-job-describe-yes' ).prop( 'checked' ) ) {
                        jobDescribeYes = 'yes';
                    } else {
                        jobDescribeYes = 'no';
                    }

                    let workWithChildrenYes = '';
                    if( $( '.mld-work-with-children-yes' ).prop( 'checked' ) ) {
                        workWithChildrenYes = 'yes';
                    } else {
                        workWithChildrenYes = 'no';
                    }

                    let qualityOfWork = [];
                    $.each( $( '.mld-quality-of-work .mld-general' ), function( index, elem ) {

                        if( $( elem ).is(':checked') ) {
                            qualityOfWork.push( 'true' );
                        } else {
                            qualityOfWork.push( 'false' );
                        }
                    } );

                    let quantityOfWork = [];
                    $.each( $( '.mld-quantity-of-work .mld-general' ), function( index, elem ) {

                        if( $( elem ).is( ':checked' ) ) {
                            quantityOfWork.push( 'true' );
                        }  else {
                            quantityOfWork.push( 'false' );                            
                        }
                    } );

                    let jobDedication = [];
                    $.each( $( '.mld-job-dedication .mld-general' ), function( index, elem ) {

                        if( $( elem ).is( ':checked' ) ) {
                            jobDedication.push( 'true' );
                        } else {
                            jobDedication.push( 'false' );
                        }
                    } );

                    let abilityOfWork = [];
                    $.each( $( '.mld-ability-of-work .mld-general' ), function( index, elem ) {

                        if( $( elem ).is( ':checked' ) ) {
                            abilityOfWork.push( 'true' );
                        } else {
                            abilityOfWork.push( 'false' );
                        }
                    } );

                    let workingRelationship = [];
                    $.each( $( '.mld-working-relationship .mld-general' ), function( index, elem ) {

                        if( $(elem).is( ':checked' ) ) {
                            workingRelationship.push( 'true' );
                        } else {
                            workingRelationship.push( 'false' );
                        }
                    } );

                    let timeKeeping = [];
                    $.each( $( '.mld-time-keeping .mld-general' ), function( index, elem ) {

                        if( $(elem).is( ':checked' ) ) {
                            timeKeeping.push( 'true' );
                        } else {
                            timeKeeping.push( 'false' );
                        }
                    } );

                    let orgName = $( '.mld-organization-table .mld-name' ).text();
                    let orgdate = $( '.mld-organization-table .mld-date' ).text();
                    let orgtelephone = $( '.mld-organization-table .mld-telephone' ).text();
                    
                    let sign = $( '.mld-organization-sign' );
                    var fileSign = $(sign)[0].files[0];

                    let stump = $( '.mld-organization-stump' );
                    var fileStump = $(stump)[0].files[0];
                    
                    var formData = new FormData();
                    formData.append( 'action', 'mld_update_reference_form' );
                    formData.append( 'sign', fileSign );
                    formData.append( 'stump', fileStump );
                    formData.append( 'applicant_name', applicentName );
                    formData.append( 'post_applied', postApplied );
                    formData.append( 'job_title', jobTitle );
                    formData.append( 'time_period', mldTimePeriod );
                    formData.append( 'applicant_capacity', applicentCapacity );
                    formData.append( 'organization_title', OrganizationTitle );
                    formData.append( 'job_duties', jobDuties );
                    formData.append( 'leaving_reason', LeavingReason );
                    formData.append( 'applicant_specification', applicentSpecification );
                    formData.append( 'applicant_work_with_children_answer', applicentWorkWithChildrenAnswer );
                    formData.append( 'applicant_further_comment', applicentFurtherComment );
                    formData.append( 'experience_data', experienceData );
                    formData.append( 'start_date', startDate );
                    formData.append( 'end_date', endDate );
                    formData.append( 'salary', Salary );
                    formData.append( 'trust_worthy_yes', trustworthyYes );
                    formData.append( 'duty_care_yes', dutyCareYes );
                    formData.append( 'disciplinary_warnings_yes', disciplinaryWarningsYes );
                    formData.append( 're_employ_yes', reEmployYes );
                    formData.append( 'job_describe_yes', jobDescribeYes );
                    formData.append( 'work_with_children_yes', workWithChildrenYes );
                    formData.append( 'quality_of_work', JSON.stringify( qualityOfWork ) );
                    formData.append( 'quantity_of_work', JSON.stringify( quantityOfWork ) );
                    formData.append( 'job_dedication', JSON.stringify( jobDedication ) );
                    formData.append( 'ability_of_work', JSON.stringify( abilityOfWork ) );
                    formData.append( 'working_relationship', JSON.stringify( workingRelationship ) );
                    formData.append( 'time_keeping', JSON.stringify( timeKeeping ) );
                    formData.append( 'org_name', orgName );
                    formData.append( 'org_date', orgdate );
                    formData.append( 'org_telephone', orgtelephone );
                    formData.append( 'user_id', userID );
                    formData.append( 'email', email );
                    var ajaxurl = MLD.ajaxURL;

                    $.ajax( {
                        url: ajaxurl,
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function( resp ) {
                            window.location.href = MLD.siteURL+'/thank-you-2/';
                        }
                    } );

                } );
            },

            /**
             * add terms and condition popup
             */
            displayTermsAndConditionPopup: function() {

                $( document ).on( 'click', '.mld-registration-terms-condition', function() {
                    $( ".mld-pop-outer" ).show();
                    $( '.mld-pop-inner' ).css( 'width', '90%' );
                    $( '.mld-pop-inner' ).css( 'height', '500px' );
                    $( '.mld-pop-inner' ).css( 'margin', 'auto' );
                    $( '.mld-pop-inner' ).css( 'overflow-y', 'auto' );
                    $( '.mld-pop-inner' ).css( 'border-radius', '12px' );
                    $("html, body").animate({ scrollTop: 500 }, "slow");
                } );
                $( document ).on( 'click', '.mld-popup-header .mld-close', function() {
                    $( ".mld-pop-outer" ).hide();
                } );
            },

            /**
            * update subject taught option
            */
            updateSubjectTaughtOption: function() {

                $( document ).on( 'change', '.mld-subject-field', function() {
                    
                    let option = $('.mld-subject-field').val();
                    $('.mld-subject-taught-dropdown').empty();
                    $( '.mld-subject-taught-dropdown' ).append( '<option value="">Select Taught Subjects</option>' );
                    $.each( option, function( index, value ) {
                        let optionValueWithSpace = value.replace( /-/g, ' ' );
                        $( '.mld-subject-taught-dropdown' ).append( '<option value="'+value+'">'+optionValueWithSpace+'</option>' );
                    } );
                } );
            },

            /**
             * update teacher dbs
             */
            updateTeacherDBS: function() {

                $( document ).on( 'click', '.mld-dbs-checkbox .dbs-btn', function() {
                    
                    let userIsAdmin = $( '.mld-is-admin' ).val();
                    if( ! userIsAdmin ) {
                        return false;
                    } 
                    
                    var confirmed = confirm("Are you sure you want to update DBS?"); 
                    
                    if( confirmed ) {

                        let DBS = '';
                        if( $( '.mld-dbs-checkbox input' ).prop( 'checked' ) == true ) {
                            DBS = 'no';
                        } else {
                            DBS = 'yes';
                        }

                        let userID = $( '.mld-dbs-checkbox input' ).attr( 'data-user_id' );
                        
                        let data = {
                            'action'            : 'update_teacher_dbs',
                            'user_id'           : userID,
                            'dbs'               : DBS
                        };

                        jQuery.post( MLD.ajaxURL, data, function( response ) {
                        } );
                    } else {
                        return false;
                    }                    
                } );
            },

            /**
             * update teacher avaibility
             */
            updateTeacherAvailibity: function() {

                $( document ).on( 'click', '.mld-availability input', function() {
                    
                    let userIsAdmin = $( '.mld-is-admin' ).val();
                    if( ! userIsAdmin ) {
                        return false;
                    } 
                    var confirmed = confirm("Are you sure you want to update avaibility?"); 

                    if( confirmed ) {

                        if( $(this).prop("checked") == true ) {

                            let self = $(this);
                            let userID = self.attr( 'data-user_id' );

                            let data = {
                                'action'            : 'update_teacher_availibity',
                                'user_id'           : userID
                            };

                            jQuery.post( MLD.ajaxURL, data, function( response ) {
                            } );

                            $( '.mld-unavailability input' ).prop( "checked", false );
                        } 
                    } else {
                        if( $(this).prop("checked") == false ) {
                            $( '.mld-availability input' ).prop( "checked", true );    
                        } else {
                            $( '.mld-availability input' ).prop( "checked", false );
                        }
                    }
                } );

                $( document ).on( 'click', '.mld-unavailability input', function() {
                    
                    let userIsAdmin = $( '.mld-is-admin' ).val();
                    if( ! userIsAdmin ) {
                        return false;
                    } 
                    var confirmed = confirm("Are you sure you want to update unavaibility?"); 

                    if( confirmed ) {

                        if( $(this).prop("checked") == true ) {

                            let self = $(this);
                            let userID = self.attr( 'data-user_id' );

                            let data = {
                                'action'            : 'update_teacher_unavailibity',
                                'user_id'           : userID
                            };

                            jQuery.post( MLD.ajaxURL, data, function( response ) {
                            } );

                            $( '.mld-availability input' ).prop( "checked", false );
                        } 
                    } else {

                        if( $(this).prop("checked") == false ) {
                            $( '.mld-unavailability input' ).prop( "checked", true );    
                        } else {
                            $( '.mld-unavailability input' ).prop( "checked", false );
                        }
                    } 
                } );
            },

            /**
             * send email to admin
             */
            sendEmailToAdmin: function() {

                $( document ).on( 'click', '.mlf-staff-email-btn', function(e) {

                    e.preventDefault();

                    let self = $(this);
                    $( '.mlf-staff-email-btn' ).val( 'Sending...' );
                    let emailSender = $( '.mld-staff-popup-e-sender' ).val();
                    let emailSubject = $( '.mld-staff-popup-e-sub' ).val();
                    let emailContent = $( '.mld-staff-popup-e-content' ).val();
                    let userID = self.attr( 'data-user_id' );
                    
                    let data = {
                        'action'        : 'send_email_to_admin',
                        'subject'       : emailSubject,
                        'content'       : emailContent,
                        'email_sender'  : emailSender,
                        'user_id'       : userID
                    };

                    jQuery.post( MLD.ajaxURL, data, function( response ) {

                        $( '.mlf-staff-email-btn' ).val( 'EMAIL SENT' );
                        $( '.mlf-staff-email-btn' ).css( 'background-color', '#18440a' );
                    } );
                } );
            },

            teacherEmailPopup: function() {

                $( document ).on( 'click', '#mld-teacher-contact-popup', function() {
                    $( '.mld-staff-popup' ).show();
                    $( '.mlf-staff-email-btn' ).css( 'background-color', '#ffb207' );
                } );

                $( document ).on( 'click', '.mld-staff-popup-close', function() {
                    $( '.mld-staff-popup' ).hide();
                } );
            },

            addNewSubjects: function() {

                $( document ).on( 'click', '.mld-subject-text', function() {
                    $( '.mld-add-sub-field' ).slideToggle();
                } );

                $( document ).on( 'click', '.mld-subject-add-btn', function() {

                    let subjectName = $( '.mld-new-subject' ).val();
                    
                    if( ! subjectName ) {
                        return false;
                    }

                    if( $(".mld-subject-field option[value='" + subjectName + "']").length === 1 ) {

                        $( '.mld-add-sub-field' ).append( '<div class="mld-already-error">Subject already available in the dropdown</div>' );
                        
                        setTimeout( function() {
                            $( '.mld-already-error' ).remove();
                            $( '.mld-add-sub-field' ).slideToggle();
                            $( '.mld-new-subject' ).val( '' );
                        }, 5000 );
                        return false;
                    }

                    $( '.mld-subject-add-btn' ).text( 'Adding...' );
                    let optionValue = subjectName.replace(/\s+/g, '-');
                    let subjectOption = '<option value="'+optionValue+'">'+subjectName+'</option>';
                    $( '.mld-subject-field' ).prepend( subjectOption );
                    
                    let data = {
                        'action'   : 'update_new_subjects',
                        'subject'  : subjectName
                    };

                    jQuery.post( MLD.ajaxURL, data, function( response ) {

                        setTimeout( function() {
                            $( '.mld-new-subject' ).val( '' );
                            $( '.mld-subject-add-btn' ).text( 'Add' );                            
                            $( '.mld-add-sub-field' ).hide();
                        }, 2000 );
                    } );
                } );
            },

            convertSelectIntoSelect2: function() {
                $('.mld-subject-field').select2({
                    placeholder: 'Select a subject',
                } );
            },

            /**
             * create a function to get first hundred word of a sentence
             */
            getFirstHundredWords: function( sentence ) {

                var words = sentence.split(/\s+/);
                var firstHundredWordsString = '';
                for (var i = 0; i < 100 && i < words.length; i++) {
                    firstHundredWordsString += words[i] + ' ';
                }
                return firstHundredWordsString;
            },

            /**
             * Delete table row 
             */ 
            deleteTableRow: function() {

                $( document ).on( 'click', '.mld-delete-table-row', function() {

                    let self = $(this);
                    let trCount = self.parents( 'table' ).find( 'tbody tr' ).length;
                    if( trCount > 1 ) {
                        self.parents( 'tr' ).remove()
                    } 
                } );
            },

            /**
             * Add new role
             */
            addNewRow: function() {

                $( document ).on( 'click', '.mld-new-row button', function() {

                    let self = $(this);
                    let tableClass = self.parents( '.mld-new-row' ).next().attr( 'class' );
                    let firstRow = '.'+tableClass+ ' tbody tr:first';
                    let firstRowData = $( firstRow ).html();
                    let appendClass = '.'+tableClass+' tbody';
                    $( appendClass ).append( '<tr>'+firstRowData+'</tr>' );
                    
                    let lastRow = '.'+tableClass+ ' tbody tr:last';
                    let lastRowData = $( lastRow ).find( 'td' );
                    $.each( $( lastRowData ), function( index, elem ) {
                        if( undefined === $( elem ).find( 'input' ).val() ) {

                            if( $( elem ).find( '.mld-delete-table-row' ).length == 0 ) {
                                if( $( elem ).find( 'select' ).length == 0 ) {
                                    $( elem ).text( '' );
                                } else {
                                    $.each( $( elem ).find( 'select option' ), function( i_index, i_elem ) {
                                        $( i_elem ).removeAttr( 'selected' );
                                    } );
                                }
                            } 
                        } else {

                            $( elem ).find( 'input' ).val( ' ' );
                        }
                    } );
                } );
            },

            /**
             * Apply restriction
             */
            applyRestrictionOnPersonalStatement: function() {

                $( document ).on( 'keydown', '.mld-personal-statement', function() {

                    let self = $(this);
                    let sentence = self.val()
                    let trimSentence = sentence.trim();
                    let words = trimSentence.split(" ");
                    let wordCount = words.length;

                    if( wordCount > 100 ) {

                        let firstHundredWords = MLD_STAFF.getFirstHundredWords( sentence );
                        $( '.mld-personal-statement' ).val( firstHundredWords );
                    }
                } );
            },

            /**
             * Display group leader popup with slide animation
             */
            displayGroupLeaderPopup: function() {

                $(document).on('click', '.mld-view-more-details', function(event) {
                    event.stopPropagation();
                    
                    let parent = $(this).closest('.mld-g-leader-inner');
                    let popup = parent.find('.mld-staff-inner-content');
                    
                    $('.mld-staff-inner-content').not(popup).stop(true, true).fadeOut(300).removeClass('fadeInUp').addClass('fadeInDown');
                    
                    if (popup.is(':visible')) {
                        popup.stop(true, true).fadeOut(300).removeClass('fadeInUp').addClass('fadeInDown');
                    } else {
                        popup.stop(true, true).fadeIn(300).removeClass('fadeInDown').addClass('fadeInUp');
                    }

                    let popupOffset = popup.offset();
                    let popupWidth = popup.outerWidth();
                    let windowWidth = $(window).width();

                    if (popupOffset.left + popupWidth > windowWidth) {
                        popup.css({
                            left: 'auto',
                            right: '0'
                        });
                    } else {
                        popup.css({
                            left: '',
                            right: ''
                        });
                    }
                });

                // Close popup when clicking outside
                $(document).on('click', function() {
                    $('.mld-staff-inner-content').fadeOut(300).removeClass('fadeInUp').addClass('fadeInDown');
                });

                $('.mld-staff-box-popup').css({
                    'max-height': '400px',
                    'overflow-y': 'auto'
                });

                $(document).on('mouseleave', '.mld-g-leader-inner', function() {
                    var $popup = $(this).find('.mld-staff-inner-content');
                    
                    $popup.stop(true, true).fadeOut(200).removeClass('fadeInUp').addClass('fadeInDown').css({
                        'max-height': '', 
                        'overflow-y': ''  
                    });
                });
            },

            /**
             * Back main staff page
             */
            backMainStaffPage: function() {

                $( document ).on( 'click', '.mld-staff-back-btn', function() {

                    $( '.mld-staff-wrapper' ).slideDown(300); 

                    $( '.mld-teacher-full-report' ).slideUp(300, function () {
                        $( this ).html( '' );
                    });
                } );
            },

            /**
             * Display group leader full detail
             */
            displayGroupLeaderFullDetail: function() {

                $( document ).on( 'click', '.mld-group-leader-detail', function() {
                    
                    let self = $(this);
                    self.find( '.mld-staff-loader' ).show();
                    let userID = self.attr( 'data-user-id' );

                    let data = {
                        'action'   : 'display_full_detail',
                        'user_id'  : userID
                    };

                    jQuery.post( MLD.ajaxURL, data, function( response ) {

                        let jsonEncode = JSON.parse( response );
                        if( 'true' == jsonEncode.status ) {
                            $( '.mld-teacher-full-report' ).slideUp(300, function () {
                                $( this ).html( jsonEncode.content ).slideDown( 300 );
                            });
                            $( '.mld-staff-wrapper' ).hide();
                            $( '.mld-staff-loader' ).hide();
                        }
                    } );
                } );
            },

            /**
             * dearch group leader using leader name
             */
            searchGroupLeadersByName: function() {

                $( document ).on( 'keydown', '.mld-search-teacher', function() {

                    let leaderName = $( '.mld-search-teacher' ).val();
                    let nameWithDash = leaderName.replace(/[-._@ ]/g, '').toLowerCase();

                    $.each( $( '.mld-g-leader-inner' ), function( index, elem ) {

                        let id = $( elem ).attr( 'id' );
                        let innerID = '';

                        if( id.includes( nameWithDash ) ) {
                            innerID = '#'+id;
                            $( innerID ).show();
                        } else {
                            innerID = '#'+id;
                            $( innerID ).hide(); 
                        }
                    } );
                } );
            },

            searchCourseLeaders: function() {

                $( document ).on( 'click', '.mld-staff-proceed-btn button', function() {
                    
                    $( '.mld-staff-proceed-btn .mld-staff-loader' ).show();
                    let courseID = $( '.mld-staff-courses' ).val();
                    
                    let isNumeric = $.isNumeric( courseID );
                    
                    let data = {
                        'action'               : 'display_course_leaders',
                        'course_id'            : courseID,
                        'is_course_id'         : isNumeric
                    };

                    jQuery.post( MLD.ajaxURL, data, function( response ) {

                        let jsonEncode = JSON.parse( response );
                        if( 'true' == jsonEncode.status ) {
                            if( jsonEncode.content ) {
                                $( '.mld-group-leaders-wrapper' ).html( jsonEncode.content );
                            } else {
                                $( '.mld-group-leaders-wrapper' ).html( '<div class="mld-no-teacher-found">No Teachers Found</div>' );
                            }
                            $( '.mld-staff-loader' ).hide();
                        }
                    } );
                } );
            },

            submitTeacherRegistrationForm: function() {

                $( document ).on( 'click', '.mld-registration-sub-btn', function() {

                    let siteURL = $( '.mld-header-main-wrapper' ).attr( 'sitr-url' );
                    var response = grecaptcha.getResponse();
                    if( response.length == 0 ) {
                        alert( 'please verify you are humann!' );
                        return false;
                    } 

                    $( '.mld-staff-loader' ).show();
                    let title = '';
                    $.each( $( '.mld-surname-radio' ), function( index, elem ) {
                        if ( $( elem ).is(":checked") ) {
                            title = $( elem ).val();
                        }
                    } );

                    let username = $( '.mld-username' ).val();
                    let surname = $( '.mld-surname' ).val();
                    let firstName = $( '.mld-first-name' ).val();
                    let email = $( '.mld-email' ).val();
                    let address = $( '.mld-address' ).val();
                    let dob = $( '.mld-date-of-birth' ).val();
                    let county = $( '.mld-county' ).val();
                    let homeTel = $( '.mld-home-tel' ).val();
                    let mobileNumber = $( '.mld-mobile-number' ).val();
                    let experience = $( '.mld-experience-field' ).val();
                    let subjects = $( '.mld-subject-field' ).val();
                    let availability = $( '.mld-availability' ).val();
                    let dbs = $( '.mld-dbs' ).val(); 
                    let personalStatement = $( '.mld-personal-statement' ).val();
                    
                    /**
                     * college Detail
                     */
                    let collegeDetail = [];
                    $.each( $( '.mld-college-education tbody tr' ), function( index, elem ) {
                        let innerArray = [];
                        $.each( $( $( elem ).find( 'td' ) ), function( inner_index, inner_elem ) {
                        
                            if( 0 != $( inner_elem ).find( 'input' ).length ) {
                                let inputDate = $( inner_elem ).find( 'input' ).val();
                                if( ! inputDate ) {
                                    inputDate = '-';
                                }
                                innerArray.push( inputDate );
                            } else {
                                if( 0 == $( inner_elem ).find( '.fa-trash' ).length && 0 == $( inner_elem ).find( 'input' ).length ) {
                                    let textValue = $( inner_elem ).text().replace( "\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t", '' );
                                    
                                    if( ! textValue ) {
                                        textValue = '-';
                                    }
                                    innerArray.push( textValue );
                                }
                            }
                        } );
                        collegeDetail.push( innerArray );
                    } );

                    /**
                     * University Detail
                     */
                    let uniDetail = [];
                    $.each( $( '.mld-university-education tbody tr' ), function( index, elem ) {
                        let innerUniArray = [];
                        $.each( $( $( elem ).find( 'td' ) ), function( uniIndex, Unielem ) {
                            if( 0 != $( Unielem ).find( 'input' ).length ) {
                                let inputVal = $( Unielem ).find( 'input' ).val();
                                if( ! inputVal ) {
                                    inputVal = '-';
                                }
                                innerUniArray.push( inputVal );
                            } else {
                                if( 0 == $( Unielem ).find( '.fa-trash' ).length && 0 == $( Unielem ).find( 'input' ).length ) {
                                    let textVal = $( Unielem ).text().replace( "\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t", '' );
                                    if( ! textVal ) {
                                        textVal = '-';
                                    }
                                    innerUniArray.push( textVal );
                                }
                            }
                        } );
                        uniDetail.push( innerUniArray );
                    } );

                    /**
                     * Experience detail
                     */
                    let experienceArray = [];
                    $.each( $( '.mld-experience-years tbody tr' ), function( index, elem ) {
                        let innerExperience = [];
                        $.each( $( $( elem ).find( 'td' ) ), function( expIndex, expelem ) {

                            if( ! $( expelem ).find( 'select' ).val() ) {
                                innerExperience.push( '-' );
                            } else {
                                if( 0 != $( expelem ).find( 'select' ).length ) {
                                    let drpValue = $( expelem ).find( 'select' ).val();
                                    innerExperience.push( drpValue );
                                }
                            }
                        } );
                        experienceArray.push( innerExperience );
                    } );

                    /**
                     * statement/photo
                     */
                    let Sfile = $( '.mld-statement' );
                    var statement = $( Sfile )[0].files[0];
                    let photoFiles = $( '.mld-photo' );
                    var photo = $( photoFiles )[0].files[0];
                    var formData = new FormData();
                    formData.append( 'statement', statement );
                    formData.append( 'photo', photo );
                    formData.append( 'teacher_college_info', JSON.stringify( collegeDetail ) );
                    formData.append( 'teacher_uni_info', JSON.stringify( uniDetail ) );
                    formData.append( 'teacher_experience', JSON.stringify( experienceArray ) );
                    /** **/
                    formData.append( 'title', title );
                    formData.append( 'username', username );
                    formData.append( 'surname', surname );
                    formData.append( 'firstName', firstName );
                    formData.append( 'email', email );
                    formData.append( 'address', address );
                    formData.append( 'dob', dob );
                    formData.append( 'county', county );
                    formData.append( 'homeTel', homeTel );
                    formData.append( 'mobileNumber', mobileNumber );
                    formData.append( 'experience', experience );
                    formData.append( 'subjects', subjects );
                    formData.append( 'availability', availability );
                    formData.append( 'dbs', dbs );
                    formData.append( 'personalStatement', personalStatement );
                    /** **/
                    formData.append( 'action', 'update_teacher_information' );
                    var ajaxurl = MLD.ajaxURL;
                    
                    $.ajax( {
                        url: ajaxurl,
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function( resp ) {
                            $( '.mld-staff-loader' ).hide();
                            $( '.mld-teacher-registration-message' ).html( resp );
                            if( 'Form Submit Successfully' == resp ) {
                                window.location.href = MLD.siteURL+'/thank-you-2/';
                            }
                        }
                    } );
                } );
            },

            displayFormSecondPage: function() {

                $( document ).on( 'click', '.mld-registration-next-btn', function() {
                    
                    setTimeout(function() { 

                        let username = $( '.mld-username' ).val();
                        let surname = $( '.mld-surname' ).val();
                        let firstName = $( '.mld-first-name' ).val();
                        let email = $( '.mld-email' ).val();
                        let dateOfBirth = $( '.mld-date-of-birth' ).val();
                        let MobileNumber = $( '.mld-mobile-number' ).val();
                        let alreadyErrorMessage = $( '.mld-user-exists-msg' ).text();

                        if( ! surname || ! firstName || ! email || ! dateOfBirth || ! MobileNumber ) {
                            $( '.mld-fields-empty-message' ).show();
                            return false;
                        }   

                        if( alreadyErrorMessage ) {

                            $("html, body").animate({ scrollTop: 300 }, "slow");
                            return false;
                        }

                        let currentScrollTop = $(window).scrollTop();

                        $("html, body").animate({ scrollTop: 300 }, "slow");
                        $( '.mld-form-first-page' ).hide();
                        $( '.mld-form-title-wrapper' ).hide();
                        $( '.mld-form-second-page' ).show();
                        $( '.mld-fields-empty-message' ).hide();
                        $( '.mld-teacher-registration-message' ).text( '' );
                        $( '.mld-step-number' ).css( 'border-color', 'green' );
                    }, 2000);
                } );

                $( document ).on( 'click', '.mld-reg-back-btn', function() {

                    $( '.mld-form-first-page' ).show();
                    $( '.mld-form-title-wrapper' ).show();
                    $( '.mld-form-second-page' ).hide();
                } );

                $( document ).on( 'click', '.mld-surname-radio', function() {

                    let self = $(this);
                    $( '.mld-surname-radio' ).prop( 'checked', false );
                    self.prop( 'checked', true );
                } );
            },
        };
        MLD_STAFF.init();
    });
})( jQuery );
