(function( $ ) { 'use strict';

    $( document ).ready( function() {

        var MLD_ADMIN_STAFF = {

            init: function() {
                this.submitTeacherRegistrationForm();
                this.addNewrow();
                this.deleteTableRow();
                this.applyRestrictionOnPersonalStatement();
                this.approvedTeacher();
                this.denyUser();
                this.approvedSubscriber();
                this.denySubscriber();
                this.addNewForms();
                this.uploadTeacherForms();
                this.approvePendingTeacher();
                this.denyPendingTeacher();
                this.removeTeacherForm();
                this.approveStudentPending();
                this.deniPendingStudent();
                this.editTeacherForm();
                this.updateResourceVerification();
                this.removeResourceSign();
                this.trashCommunicationMessage();
                this.activeUpdateFields();
                this.updateCommunicationMessage();
            },

            /**
             * update communication message
             */ 
            updateCommunicationMessage: function() {

                $( document ).on( 'click', '.mld-communication-update-btn button', function(e) {

                    e.preventDefault();

                    let self = $(this);
                    let id = self.attr( 'data-id' );
                    let message = self.parents( '.mld-communication-inner-wrapper' ).find( '.mld-message' ).text(); 
                    
                    self.parents( '.mld-communication-inner-wrapper' ).find( '.mld-message' ).css( 'background-color', 'unset' );
                    self.hide();
                    
                    let data = {
                        'action'       : 'update_communication_comments',
                        'id'           : id,
                        'message'      : message
                    };

                    jQuery.post( MLD.ajaxURL, data, function( response ) {} );
                } );
            },

            /**
             * active update fields
             */
            activeUpdateFields: function() {

                $( document ).on( 'click', '.mld-communication-edit', function() {

                    let self = $(this);
                    let id = self.attr( 'data-id' );
                    self.parents( '.mld-communication-inner-wrapper' ).find( '.mld-message' ).attr( 'contenteditable', 'true' );
                    self.parents( '.mld-communication-inner-wrapper' ).find( '.mld-communication-update-btn' ).show();
                    self.parents( '.mld-communication-inner-wrapper' ).find( '.mld-message' ).css( 'background-color', 'white' );
                } );
            },

            /**
             * trash communication message
             */
            trashCommunicationMessage: function() {

                $( document ).on( 'click', '.mld-communication-trash', function() {

                    var confirmed = confirm("Are you sure you want to Delete the comment?");

                    if( confirmed ) {

                        let self = $(this);
                        let ID = self.attr( 'data-id' );

                        let data = {
                            'action'       : 'trash_communication',
                            'id'           : ID
                        };

                        self.parents( '.mld-communication-inner-wrapper' ).remove();
                        jQuery.post( MLD.ajaxURL, data, function( response ) {} );
                    }
                } );
            },

            /**
             * remove resource sign
             */
            removeResourceSign: function() {

                $( document ).on( 'click', '.mld-delete-resource_sign', function() {

                    let self = $(this);
                    self.hide();
                    $( '.mld-resource-signatur img' ).hide();
                    $( '.mld-resource-signatur input' ).show();
                } );
            },

            /**
             * update resource verification
             */
            updateResourceVerification: function() {

                $( document ).on( 'click', '.resour-update-btn button', function(e) {

                    e.preventDefault();

                    let self = $(this);
                    self.text( 'Update Verification...' );
                    let resourceName = $( '.mld-resource-name' ).text();
                    let resourceDate = $( '.mld-resource-date input' ).val();

                    /**
                     * personal data
                     */

                    let title = $( '.mld-pd-title-data' ).text();
                    let foreName = $( '.mld-pd-forename-data' ).text();
                    let surName = $( '.mld-pd-surname-data' ).text();
                    let niNumber = $( '.mld-pd-ni-number-data' ).text();
                    let DOB = $( '.mld-pd-dob-data' ).text();
                    let homeAddress = $( '.mld-pd-home-address-data' ).text();
                    let homeEmail = $( '.mld-pd-home-email-data' ).text();
                    let mobileNumber = $( '.mld-pd-mobile-number-data' ).text();
                    let deptSubjects = $( '.mld-pd-subjects-data' ).text();

                    /**
                     * band detail
                     */
                    let bankName = $( '.mld-bd-name-data' ).text();
                    let accountHolderName = $( '.mld-bd-account-name-data' ).text();
                    let sortCode = $( '.mld-bd-sort-code-data' ).text();
                    let accountNumber = $( '.mld-bd-b-account-number-data' ).text();
                    let bankAddress = $( '.mld-bd-bank-address-data' ).text();

                    /**
                     * Disclosure & Barring Service
                     */
                    let certificateNumber = $( '.mld-certificate-number-data' ).text();
                    let applicantName = $( '.mld-certificate-surname-data' ).text();
                    let currentYN = $( '.mld-current-yn-data' ).text();
                    let DOBCertificate = $( '.mld-certificate-dob-data' ).text();
                    let internalUse = $( '.mld-internal-use-data' ).text();

                    /**
                     * signature
                     */
                    let signatureName = $( '.mld-signatur-name' ).text();
                    let signatureDate = $( '.mld-signature-date' ).text();

                    /**
                     * list a and b data
                     */
                    let listAData = [];
                    $.each( $( '.mld-list-a-wrapper li input' ), function( index, elem ) {

                        if( $(elem).prop("checked") == true ) {
                            listAData.push( 'yes' );
                        } else if( $(elem).prop("checked") == false ) {
                            listAData.push( 'no' );
                        }
                    } );

                    let listBData = [];
                    $.each( $( '.mld-list-b-wrapper li input' ), function( index, elem ) {

                        if( $(elem).prop("checked") == true ) {
                            listBData.push( 'yes' );
                        } else if( $(elem).prop("checked") == false ) {
                            listBData.push( 'no' );
                        }
                    } );

                    var formData = new FormData();
                    formData.append( 'list_a_data', JSON.stringify( listAData ) );
                    formData.append( 'list_b_data', JSON.stringify( listBData ) );
                    
                    let userID = self.attr( 'date-user_id' );
                    let files = $( '.mld-resource-signatur input' );
                    var file = $( files )[0].files[0];
                    
                    formData.append( 'file', file );
                    formData.append( 'user_id', userID );
                    formData.append( 'resource_name', resourceName );
                    formData.append( 'resource_date', resourceDate );
                    
                    formData.append( 'title', title );
                    formData.append( 'forename', foreName );
                    formData.append( 'surname', surName );
                    formData.append( 'ni_number', niNumber );
                    formData.append( 'dob', DOB );
                    formData.append( 'home_address', homeAddress );
                    formData.append( 'home_email', homeEmail );
                    formData.append( 'mobile_number', mobileNumber );
                    formData.append( 'subjects', deptSubjects );
                    formData.append( 'bank_name', bankName );
                    formData.append( 'account_holder_name', accountHolderName );
                    formData.append( 'sort_code', sortCode );
                    formData.append( 'bank_address', bankAddress );
                    formData.append( 'account_number', accountNumber );
                    formData.append( 'certificate_number', certificateNumber );
                    formData.append( 'username_on_certificate', applicantName );
                    formData.append( 'current_yn', currentYN );
                    formData.append( 'dob_on_certificate', DOBCertificate );
                    formData.append( 'internal_use', internalUse );
                    formData.append( 'signature_name', signatureName );
                    formData.append( 'signature_date', signatureDate );

                    formData.append( 'action', 'update_resource_verification' );
                    var ajaxurl = MLD.ajaxURL;
                    
                    $.ajax( {
                        url: ajaxurl,
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function( resp ) {
                            $( '.resour-update-btn button' ).text( 'Update Verification' );
                        }
                    } );
                } );
            },

            /**
             * update teacher form
             */
            editTeacherForm: function() {

                $( document ).on( 'click', '.mld-edit-icon', function() {

                    let self = $(this);
                    let parents = self.parents( '.mld-teacher-inner-wrapper' );
                    parents.find( 'h3' ).hide();
                    parents.find( '.mld-form-title' ).show();
                    parents.find( '.mld-form-title' ).attr( 'contenteditable', 'true' );
                    parents.find( '.mld-form-title' ).css( 'background', 'white' );
                } );
            },

            /**
             * deny pending student 
             */
            deniPendingStudent: function() {

                $( document ).on( 'click', '.mld-student-pending-deny', function(e) {

                    e.preventDefault();

                    var confirmed = confirm("Are you sure you want to Deny? If you deny then user and it's data will be deleted.");

                    if( confirmed ) {

                        let self = $(this);
                        let userID = self.attr( 'data-user_id' );

                        let data = {
                            'action'            : 'deny_pending_student_user',
                            'user_id'           : userID
                        };

                        jQuery.post( MLD.ajaxURL, data, function( response ) {

                            let pageUrl = MLD.site_url+'/wp-admin/users.php';
                            window.location.href = pageUrl;
                        } );
                    }
                } );
            },

            /**
             * student pending user
             */
            approveStudentPending: function() {

                $( document ).on( 'click', '.mld-student-pending-accept', function(e) {

                    e.preventDefault();

                    var confirmed = confirm("Are you sure you want to approved?");

                    if( confirmed ) {

                        let self = $(this);
                        let userID = self.attr( 'data-user_id' );

                        let data = {
                            'action'            : 'accept_pending_student_user',
                            'user_id'           : userID
                        };

                        jQuery.post( MLD.ajaxURL, data, function( response ) {

                            let pageUrl = MLD.site_url+'/wp-admin/users.php';
                            window.location.href = pageUrl;
                        } );
                    }
                } );
            },

            /**
             * Remove Teacher Form
             */
            removeTeacherForm: function() {
 
                $( document ).on( 'click', '.mld-dashicon', function() {

                    var confirmed = confirm("Are you sure you want to Delete?");
                    if( confirmed ) {

                        let self = $(this);
                        
                        let deleteURL = self.attr( 'data-delete_url' );

                        let data = {
                            'action'            : 'delete_teacher_form',
                            'delete_url'        : deleteURL
                        };

                        jQuery.post( MLD.ajaxURL, data, function( response ) {
                            self.parents( '.mld-teacher-inner-wrapper' ).remove();
                        } );
                    }
                } );
            },

            /**
             * denyt pensing teacher
             */ 
            denyPendingTeacher: function() {

                $( document ).on( 'click', '.mld-deny-as-teacher', function(e) {

                    e.preventDefault();

                    var confirmed = confirm("Are you sure you want to Deny? If you deny then user and it's data will be deleted.");

                    if( confirmed ) {

                        let self = $(this);
                        let userID = self.attr( 'data-user_id' );

                        let data = {
                            'action'            : 'deny_pending_teacher',
                            'user_id'           : userID
                        };

                        jQuery.post( MLD.ajaxURL, data, function( response ) {

                            let pageUrl = MLD.site_url+'/wp-admin/users.php';
                            window.location.href = pageUrl;
                        } );
                    }
                } );
            },

            /**
             * approv pending teacher
             */
            approvePendingTeacher: function() {

                $( document ).on( 'click', '.mld-confirm-as-teacher', function(e) {

                    e.preventDefault();

                    var confirmed = confirm("Are you sure you want to approved?");

                    if( confirmed ) {

                        let self = $(this);
                        let userID = self.attr( 'data-user_id' );

                        let data = {
                            'action'            : 'accept_pending_teacher',
                            'user_id'           : userID
                        };

                        jQuery.post( MLD.ajaxURL, data, function( response ) {

                            let pageUrl = MLD.site_url+'/wp-admin/users.php';
                            window.location.href = pageUrl;
                        } );
                    }
                } );
            },

            /**
             * upload teacher form
             */
            uploadTeacherForms: function() {

                $( document ).on( 'click', '.mld-update-teacher-form', function() {

                    $(this).val( 'Updating...' );
                    let formArray = [];
                    var formData = new FormData();
                    $.each( $( '.mld-teacher-inner-wrapper' ), function( index, elem ) {
                        
                        let formTitle = $( elem ).find( '.mld-form-title' ).val();
                        let teacherFiles = $( elem ).find( '.mld-teacher-uploads' );
                        let teachetFormType = $( elem ).find( '.mld-form-title' ).attr( 'data-type' );
                        let teacherForm = teacherFiles[0].files[0];
                        
                        if( formTitle ) {

                            formArray.push( {

                                index: teachetFormType,
                                title: formTitle
                            } );
                        }      

                        if( teacherForm && formTitle ) {

                            var formTitleWithDash = formTitle.replace(/ /g, '_');
                            formData.append( formTitleWithDash, teacherForm );
                        }   
                    } );

                    formData.append( 'action', 'mld_upload_teacher_form' );
                    formData.append( 'teacher_form_title', JSON.stringify( formArray ) );
                                            
                    var ajaxurl = MLD.ajaxURL;

                    $.ajax( {
                        url: ajaxurl,
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function( resp ) {
                            location.reload();
                        }
                    } );
                } );
            },

            /**
             * Add new forms
             */
            addNewForms: function() {

                $( document ).on( 'click', '.mld-teacher-upload', function() {

                    let formType = $( '.mld-upload-type' ).val();
                            
                    if( ! formType ) {
                        return false;
                    }        

                    $( '.mld-update-teacher-form' ).show();
                    let html = '';

                    if( 'form' == formType ) {

                        html += '<div class="mld-teacher-inner-wrapper">';
                        html += '<p><input type="text" placeholder="Teacher Form Title" class="mld-form-title" data-type="'+formType+'"></p>';
                        html += '<p><input type="file" class="mld-teacher-uploads" class="mld-teacher-form"></p>';
                        html += '</div>';
                    }

                    if( 'documents' == formType ) {

                        html += '<div class="mld-teacher-inner-wrapper">';
                        html += '<p><input type="text" placeholder="Document Title" class="mld-form-title" data-type="'+formType+'"></p>';
                        html += '<p><input type="file" class="mld-teacher-uploads" class="mld-teacher-form" style="display: none;"></p>';
                        html += '</div>';
                    }

                    $( '.mld-teacher-uploads' ).append( html );
                } );
            },

            /**
             * deny subscriber
             */
            denySubscriber: function() {

                $( document ).on( 'click', '.mld-subscriber-deny', function(e) {

                    e.preventDefault();

                    var confirmed = confirm("Are you sure you want to Deny? If you deny then user and it's data will be deleted.");

                    if( confirmed ) {

                        let self = $(this);
                        let userID = self.attr( 'data-user_id' );

                        let data = {
                            'action'            : 'deny_subscriber_user',
                            'user_id'           : userID
                        };

                        jQuery.post( MLD.ajaxURL, data, function( response ) {

                            let pageUrl = MLD.site_url+'/wp-admin/users.php';
                            window.location.href = pageUrl;
                        } );
                    }
                } );
            },

            /**
             * approve as a subscriber
             */
            approvedSubscriber: function() {

                $( document ).on( 'click', '.mld-subscriber-accept', function(e) {

                    e.preventDefault();

                    var confirmed = confirm("Are you sure you want to approved?");

                    if( confirmed ) {

                        let self = $(this);
                        let userID = self.attr( 'data-user_id' );

                        let data = {
                            'action'            : 'accept_subscriber_user',
                            'user_id'           : userID
                        };

                        jQuery.post( MLD.ajaxURL, data, function( response ) {

                            let pageUrl = MLD.site_url+'/wp-admin/users.php';
                            window.location.href = pageUrl;
                        } );
                    }
                } );
            },

            /**
             * Deny user
             */
            denyUser: function() {

                $( document ).on( 'click', '.mld-user-deny', function(e) {

                    e.preventDefault();

                    var confirmed = confirm("Are you sure you want to Deny? If you deny then user and it's data will be deleted.");

                    if( confirmed ) {

                        let self = $(this);
                        let userID = self.attr( 'data-user_id' );

                        let data = {
                            'action'            : 'deny_pending_user',
                            'user_id'           : userID
                        };

                        jQuery.post( MLD.ajaxURL, data, function( response ) {
                            
                            let pageUrl = MLD.site_url+'/wp-admin/users.php';
                            window.location.href = pageUrl;
                        } );
                    }
                } );
            },

            /**
             * approved pending user
             */
            approvedTeacher: function() {

                $( document ).on( 'click', '.mld-user-accept', function(e) {

                    e.preventDefault();

                    var confirmed = confirm("Are you sure you want to approved?");

                    if( confirmed ) {

                        let self = $(this);
                        let userID = self.attr( 'data-user_id' );

                        let data = {
                            'action'            : 'update_user_role',
                            'user_id'           : userID
                        };

                        jQuery.post( MLD.ajaxURL, data, function( response ) {
                            location.reload();
                        } );
                    }
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
             * apply restiction to write only hundred words
             */
            applyRestrictionOnPersonalStatement: function() {

            	$( document ).on( 'keydown', '.mld-personal-statement', function() {

            		let self = $(this);
            		let sentence = self.val()
            		let trimSentence = sentence.trim();
            		let words = trimSentence.split(" ");
            		let wordCount = words.length;

            		if( wordCount > 100 ) {

            			let firstHundredWords = MLD_ADMIN_STAFF.getFirstHundredWords( sentence );
            			$( '.mld-personal-statement' ).val( firstHundredWords );
            		}
            	} );
            },

            /**
             * Delete table row 
             */ 
            deleteTableRow: function() {

                $( document ).on( 'click', '.mld-delete-table-row', function(e) {

                    e.preventDefault();
                    let self = $(this);
                    let trCount = self.parents( 'table' ).find( 'tbody tr' ).length;
                    if( trCount > 1 ) {
                        self.parents( 'tr' ).remove()
                    } 
                } );
            },

            /**
             * add new row
             */
            addNewrow: function() {

                $( document ).on( 'click', '.mld-new-row button', function(e) {

                    e.preventDefault();

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

            submitTeacherRegistrationForm: function() {

                $( '.mld-subject-field' ).select2();

                $( document ).on( 'click', '.mld-edit-staff-update-btn button', function( e ) {

                    e.preventDefault();

                    let self = $(this);
                    self.text( 'Updating...' );
                    let userID = self.attr( 'data-user_id' );

                    if( ! userID ) {
                        return false;
                    }

                    let experience = $( '.mld-experience-field' ).val();
                    let subjects = $( '.mld-subject-field' ).val();
                    let availability = $( '.mld-availability' ).val();
                    let dbs = $( '.mld-dbs' ).val(); 
                    let personalStatement = $( '.mld-personal-statement' ).val();
                    let email = $( '.t-email' ).val();
                    let address = $( '.t-address' ).val();
                    let dob = $( '.t-dob' ).val();
                    let county = $( '.t-county' ).val();
                    let hometel = $( '.t-hometel' ).val();
                    let mobileNumber = $( '.t-mobile-number' ).val();
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

                    let data = {
                        'action'            : 'update_teacher_edit_profile',
                        'user_id'           : userID,
                        'college_edu'       : collegeDetail,
                        'uni_edu'           : uniDetail,
                        'experience_edu'    : experienceArray,
                        'exper'             : experience,
                        'subjects'          : subjects,
                        'availability'      : availability,
                        'dbs'               : dbs,
                        'statement'         : personalStatement,
                        'email'             : email,
                        'address'           : address,
                        'dob'               : dob,
                        'county'            : county,
                        'hometel'           : hometel,
                        'mobileNumber'      : mobileNumber
                    };

                    jQuery.post( MLD.ajaxURL, data, function( response ) {
                        self.text( 'Updated' );
                    } );
                } );
            },
        };
        MLD_ADMIN_STAFF.init();
    });
})( jQuery );
