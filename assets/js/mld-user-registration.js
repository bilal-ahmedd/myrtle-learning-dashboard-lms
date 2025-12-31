(function( $ ) { 'use strict';

    $( document ).ready( function() {

        var MLD_USER_REGISTRATION = {

            init: function() {

                this.nextPage();
                this.backPage();
                this.submitUserRegistrationData();
                this.radioBoxCheckedUnchecked();
                this.checkUserName();
                this.checkUserEmail();
            },

            /**
             * check email is exists or not 
             */
            checkUserEmail: function() {

                $( document ).on( 'click', function() {

                    if ( $( event.target ).closest( ".mld-u-reg-email" ).length === 0 ) {

                        let email = $( '.mld-u-reg-email' ).val();

                        let data = {
                            'action'               : 'email_is_exists',
                            'email'                : email,
                            'mld_nounce'           : MLD.security
                        };

                        jQuery.post( MLD.ajaxURL, data, function( response ) {

                            let emailJsonEncode = JSON.parse( response );
                            
                            if( 'true' == emailJsonEncode.status ) {
                                $( '.mld-email-exists-msg' ).remove();
                                $( '.mld-u-reg-email' ).css( 'border-color', 'red' );
                                $( '.mld-u-reg-email' ).after( '<div class="mld-email-exists-msg">The user already exists. Please try again with a different email.</div>' );
                            }
                        } );
                    }
                } );
            },

            /**
             * check username is exists or not
             */
            checkUserName: function() {

                $( document ).on( 'click', function() {

                    if ( $( event.target ).closest( ".mld-u-reg-username" ).length === 0 ) {

                        let username = $( '.mld-u-reg-username' ).val();

                        let data = {
                            'action'               : 'user_is_exists',
                            'username'             : username,
                            'mld_nounce'           : MLD.security
                        };

                        jQuery.post( MLD.ajaxURL, data, function( response ) {

                            let jsonEncode = JSON.parse( response );
                            if( 'true' == jsonEncode.status ) {

                                $( '.mld-user-exists-msg' ).remove();
                                $( '.mld-name-suggestion' ).remove();
                                $( '.mld-u-reg-username' ).css( 'border-color', 'red' );
                                $( '.mld-u-reg-username' ).after( '<div class="mld-user-exists-msg">The user already exists. Please try again with a different username.</div>' );
                                $( '.mld-user-exists-msg' ).after( '<div class="mld-name-suggestion">for instance: '+jsonEncode.suggestion+'</div>' );
                            }
                        } );
                    }
                } );
            },

            /**
             * make sure check and uncheck radio option
             */
            radioBoxCheckedUnchecked: function() {

                /**
                 * medical condition radio box
                 */
                $( document ).on( 'change', '.mld-medi-condition .mld-condition-option input[type="radio"]', function() {
                    
                    let mediYes = $('.medical-condition-yes').is(':checked');
                    let mediNo = $('.medical-condition-no').is(':checked');
                    
                    if( true == mediYes && true == mediNo ) {

                        let currentRadio = $(this).attr('class');
                        let removeRadio = '';
                        if( 'medical-condition-yes' == currentRadio ) {
                            removeRadio = 'medical-condition-no';
                        } else {
                            removeRadio = 'medical-condition-yes';
                        }
                        $('.mld-medi-condition .mld-condition-option .'+removeRadio).prop('checked', false);
                    }
                } );

                /**
                 * extra support check and unceck
                 */
                $( document ).on( 'change', '.mld-extra-support .mld-condition-option input[type="radio"]', function() {

                    let extraYes = $('.extra-support-yes').is(':checked');
                    let extraNo = $('.extra-support-no').is(':checked');
                    
                    if( true == extraYes && true == extraNo ) {

                        let currentRadio = $(this).attr('class');
                        let removeRadio = '';
                        if( 'extra-support-yes' == currentRadio ) {
                            removeRadio = 'extra-support-no';
                        } else {
                            removeRadio = 'extra-support-yes';
                        }
                        $('.mld-extra-support .mld-condition-option .'+removeRadio).prop('checked', false);
                    }
                } );

                /**
                 * allergies check and unceck
                 */
                $( document ).on( 'change', '.mld-allergies .mld-condition-option input[type="radio"]', function() {

                    let allergiesYes = $('.allergies-yes').is(':checked');
                    let allergiesNo = $('.allergies-no').is(':checked');
                    
                    if( true == allergiesYes && true == allergiesNo ) {

                        let currentRadio = $(this).attr('class');
                        let removeRadio = '';
                        if( 'allergies-yes' == currentRadio ) {
                            removeRadio = 'allergies-no';
                        } else {
                            removeRadio = 'allergies-yes';
                        }
                        $('.mld-allergies .mld-condition-option .'+removeRadio).prop('checked', false);
                    }
                } );
            },

            /**
             * submit user registration data
             */
            submitUserRegistrationData: function() {

                $( document ).on( 'click', '.mld-submit-user-registration', function() { 

                    $(this).text( 'Submit...' );
                    let fName = $( '.mld-user-f-name' ).val();
                    let lName = $( '.mld-user-l-name' ).val();
                    let email = $( '.mld-u-reg-email' ).val();
                    let username = $( '.mld-u-reg-username' ).val();
                    let dateOfBirth = $( '.mld-user-db' ).val();
                    let yearGroup = $( '.mld-user-year-group' ).val();
                    let school = $( '.mld-u-reg-schl' ).val();
                    let parentName = $( '.mld-u-reg-parent-name' ).val();
                    let phone = $( '.mld-u-reg-phone' ).val();
                    let telephone = $( '.mld-u-reg-h-tel' ).val();
                    let address = $( '.mld-u-reg-address' ).val();
                    let parentEmail = $( '.mld-u-reg-parent-email' ).val();
                    let medicalSelectedValue = $('.mld-medi-condition .mld-condition-option input[type="radio"]:checked').next('label').text();
                    let extraSelectedValue = $('.mld-extra-support .mld-condition-option input[type="radio"]:checked').next('label').text();
                    let allergiesSelectedValue = $('.mld-allergies .mld-condition-option input[type="radio"]:checked').next('label').text();
                    
                    let medicalComment = '';
                    if( 'Yes' == medicalSelectedValue ) {
                        medicalComment = $( '.mld-medi-condition .mld-condition-detail textarea' ).val();
                    }

                    let extraComment = '';
                    if( 'Yes' == extraSelectedValue ) {
                        extraComment = $( '.mld-extra-support .mld-condition-detail textarea' ).val();
                    }

                    let allergiesComment = '';
                    if( 'Yes' == allergiesSelectedValue ) {
                        allergiesComment = $( '.mld-allergies .mld-condition-detail textarea' ).val();
                    }

                    let tuitionCheckedValues = [];

                    $('.mld-tuition-wrapper input[type="checkbox"]:checked').each(function() {
                        tuitionCheckedValues.push( $(this).next('label').text() ); 
                    });

                    let coursesCheckedValues = [];

                    $('.mld-course-check-boxes input[type="checkbox"]:checked').each(function() {
                        coursesCheckedValues.push( $(this).next('label').text() ); 
                    });

                    let examinationBoardCheckedValues = [];

                    $('.mld-examination-board-wrapper input[type="checkbox"]:checked').each(function() {
                        examinationBoardCheckedValues.push( $(this).next('label').text() ); 
                    });

                    let otherCourses = '';
                    if (coursesCheckedValues.includes('Other')) {
                        otherCourses = $( '.mld-courses-detail-wrapper textarea' ).val();
                    }

                    let files = $( '.mld-u-reg-photo' );
                    var file = $( files )[0].files[0];

                    var formData = new FormData();
                    formData.append( 'action', 'save_user_registration_data' );
                    formData.append( 'profile', file );
                    formData.append( 'first_name', fName );
                    formData.append( 'last_name', lName );
                    formData.append( 'email', email );
                    formData.append( 'username', username );
                    formData.append( 'date_of_birth', dateOfBirth );
                    formData.append( 'year_group', yearGroup );
                    formData.append( 'school', school );
                    formData.append( 'parent_name', parentName );
                    formData.append( 'phone_number', phone );
                    formData.append( 'home_tel', telephone );
                    formData.append( 'address', address );
                    formData.append( 'parent_email', parentEmail );
                    formData.append( 'medical_option', medicalSelectedValue );
                    formData.append( 'extra_support_option', extraSelectedValue );
                    formData.append( 'allergies_option', allergiesSelectedValue );
                    formData.append( 'medical_detailed', medicalComment );
                    formData.append( 'extra_support_detailed', extraComment );
                    formData.append( 'allergies_detailed', allergiesComment );
                    formData.append( 'tuition', tuitionCheckedValues );
                    formData.append( 'courses', coursesCheckedValues );
                    formData.append( 'other_courses', otherCourses );
                    formData.append( 'examination_board', examinationBoardCheckedValues );

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
             * decrease form page
             */
            backPage: function() {

                $( document ).on( 'click', '.mld-user-prev-btn', function() {

                    $( '.mld-user-next-btn' ).text( 'Next' );
                    $( '.mld-user-next-btn' ).removeClass( 'mld-submit-user-registration' );

                    let page = parseFloat( $( '.mld-user-next-btn' ).attr( 'data-page' ) );
                    $( '.mld-user-next-btn' ).attr( 'data-page', page - 1 );

                    if( 2 == page ) {

                        $( '.mld-user-registration-first-page' ).show();
                        $( '.mld-user-registration-second-page' ).hide();                    
                        $( '.mld-user-prev-btn' ).hide();
                        $( '.mld-user-next-btn' ).css( 'width', '100%' );
                    }

                    if( 3 == page ) {
                        $( '.mld-user-registration-second-page' ).show();
                        $( '.mld-user-registration-third-page' ).hide();
                    }

                    if( 4 == page ) {
                        $( '.mld-user-registration-fourth-page' ).hide();
                        $( '.mld-user-registration-third-page' ).show();
                    }
                } );
            },

            /**
             * change form page
             */
            nextPage: function() {

                $( document ).on( 'click', '.mld-user-next-btn', function() {

                    let self = $(this);
                    let page = parseFloat( self.attr( 'data-page' ) );

                    if( 1 == page ) {

                        let form = true;
                        $.each( $( '.mld-user-registration-first-page .mld-important' ), function( index, elem ) {
                            if( $(elem).val() == '' ) {
                                $( '.mld-error-msg' ).show();
                                form = false;
                            }
                        } );

                        if( form === false ) {
                            return false;
                        }

                        self.attr( 'data-page', page + 1 );
                        $( '.mld-user-registration-first-page' ).hide();
                        $( '.mld-user-registration-second-page' ).show();                    
                        $( '.mld-user-prev-btn' ).show();
                        $( '.mld-user-prev-btn' ).css( 'width', '49%' );
                        $( '.mld-user-next-btn' ).css( 'width', '49%' );
                        $( '.step-two .circle' ).addClass( 'active' );
                    }

                    if( 2 == page ) {

                        let form = true;
                        $.each( $( '.mld-user-registration-second-page .mld-important' ), function( index, elem ) {
                            if( $(elem).val() == '' ) {
                                $( '.mld-error-msg' ).show();
                                form = false;
                            }
                        } );

                        if( form === false ) {
                            return false;
                        }

                        self.attr( 'data-page', page + 1 );
                        $( '.mld-user-registration-second-page' ).hide();
                        $( '.mld-user-registration-third-page' ).show();
                        $( '.step-three .circle' ).addClass( 'active' );
                    }

                    if( 3 == page ) {

                        self.attr( 'data-page', page + 1 );
                        $( '.mld-user-registration-fourth-page' ).show();
                        $( '.mld-user-registration-third-page' ).hide();
                        $( '.mld-user-next-btn' ).text( 'Submit' );
                        $( '.mld-user-next-btn' ).addClass( 'mld-submit-user-registration' );
                        $( '.step-four .circle' ).addClass( 'active' );
                    }

                    $( '.mld-error-msg' ).hide();
                } );
            },  
        };
        MLD_USER_REGISTRATION.init();
    });
})( jQuery );
