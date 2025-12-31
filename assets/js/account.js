(function( $ ) { 'use strict';

    $( document ).ready( function() {

        var MLD_ACCOUNT = {

            init: function() {
                this.uploadPDF();
                this.getPolicies();
                this.uploadPolicy();
                this.goBackOnAccount();
                this.resetPassword();
                this.updateUserProfile();
                this.deletePDF();
                this.subjectOnChange();
                this.saveChanges();
                this.removeUpdateSubjects();
                this.removeTags();
                this.wrapSubjectDropdown();
                this.searchPDF();
                this.toggleDeleteOption();
                this.pdfPopup();
                this.addMoreCategory()
                this.displayCourseAccordingToGroup();
                this.accountCoursesOnchange();
                this.getUserAccountHtml();
                this.removeCategoryHtml();
                this.openTermAndConditionPopup();
                this.pdfComment();
                this.updatePdfComment();
                this.deletePolicyType();
                this.checklist();
                this.addNewUploder();
                this.uploadUserForms();
                this.addAnotherRefree();
                this.updateBankDetails();
                this.makeMeetingAreaEditable();
                this.updateMeetingText();
            },

            /**
             * update meeting area
             */
            updateMeetingText: function() {

                $( document ).on( 'click', '.mld-click-to-update', function() {

                    let self = $(this);
                    $( '.mld-meeting-textarea textarea' ).attr( 'disabled', true );
                    $( '.mld-click-to-update' ).text( 'CLICK TO UPDATE ...' );
                    
                    let message = $( '.mld-meeting-textarea textarea' ).val();
                    
                    let loggedInUser = self.attr( 'data-logged_in_user_id' );
                    let currentUser = self.attr( 'data-current_user_id' );
                    
                    let data = {
                        'action'          : 'update_parent_communication',
                        'message'         : message,
                        'logged_in_user'  : loggedInUser,
                        'current_user'    : currentUser
                    };

                    jQuery.post( MLD.ajaxURL, data, function( response ) {
                        $( '.mld-account-submit' ).click();
                        $( '.mld-click-to-update' ).text( 'CLICK TO UPDATE' );
                    } );
                } );
            },

            /**
             * make editable
             */
            makeMeetingAreaEditable: function() {

                $( document ).on( 'click', '.mld-click-to-edit', function() {
                    
                    $( '.mld-meeting-textarea textarea' ).removeAttr( 'disabled' );
                    $( '.mld-meeting-textarea textarea' ).css( 'background-color', 'white' );
                    $( '.mld-meeting-textarea textarea' ).css( 'border', '2px solid #18440a' );
                } );
            },

            /**
             * update bank details
             */
            updateBankDetails: function() {

                $( document ).on( 'click', '.mld-bank-detail-submit-btn', function() {

                    let siteURL = $( '.mld-header-main-wrapper' ).attr( 'sitr-url' );
                    let bank_detail = $( '.mld-already-filled-bank-detail' ).val();
                                            
                    if( 'yes' == bank_detail ) {
                        
                        $( '.mld-error-msg' ).show();
                        return false;
                    }

                    let title = $( '.mld-pd-title-data' ).text();
                    let forename = $( '.mld-pd-forename-data' ).text();
                    let surname = $( '.mld-pd-surname-data' ).text();
                    let niNumber = $( '.mld-pd-ni-number-data' ).text();
                    let DOB = $( '.mld-pd-dob-data' ).text();
                    let homeAddress = $( '.mld-pd-home-address-data' ).text();
                    let homeEmail = $( '.mld-pd-home-email-data' ).text();
                    let mobileNumber = $( '.mld-pd-mobile-number-data' ).text();
                    let subjects = $( '.mld-pd-subjects-data' ).text();
                    let bankName = $( '.mld-bd-name-data' ).text();
                    let accountHolderName = $( '.mld-bd-account-name-data' ).text();
                    let sortCode = $( '.mld-bd-sort-code-data' ).text();
                    let accountNumber = $( '.mld-bd-b-account-number-data' ).text();
                    let bankAddress = $( '.mld-bd-bank-address-data' ).text();
                    let certificateNumber = $( '.mld-certificate-number-data' ).text();
                    let usernameOnCertificate = $( '.mld-certificate-surname-data' ).text();
                    let currentYN = $( '.mld-current-yn-data' ).text();
                    let dobOnCertificate = $( '.mld-certificate-dob-data' ).text();
                    let internalUse = $( '.mld-internal-use-data' ).text();
                    let signatureName = $( '.mld-signature-table .mld-signatur-name' ).text();
                    let signatureDate = $( '.mld-signature-table .mld-signature-date input' ).val();
                    let resourceName = $( '.mld-human-resources-table .mld-resource-name' ).text();
                    let resourceDate = $( '.mld-human-resources-table .mld-resource-date input' ).val();
                    let self = $(this);                   
                    self.val( 'Submit...' );
                    var formData = new FormData();
                    formData.append( 'action', 'mld_update_checklist_form' );
                    
                    formData.append( 'title', title );
                    formData.append( 'forename', forename );
                    formData.append( 'surname', surname );
                    formData.append( 'ni_number', niNumber );
                    formData.append( 'dob', DOB );
                    formData.append( 'home_address', homeAddress );
                    formData.append( 'home_email', homeEmail );
                    formData.append( 'mobile_number', mobileNumber );
                    formData.append( 'subjects', subjects );
                    formData.append( 'bank_name', bankName );
                    formData.append( 'account_holder_name', accountHolderName );
                    formData.append( 'sort_code', sortCode );
                    formData.append( 'bank_address', bankAddress );
                    formData.append( 'account_number', accountNumber );
                    formData.append( 'certificate_number', certificateNumber );
                    formData.append( 'username_on_certificate', usernameOnCertificate );
                    formData.append( 'current_yn', currentYN );
                    formData.append( 'dob_on_certificate', dobOnCertificate );
                    formData.append( 'internal_use', internalUse );
                    formData.append( 'signature_name', signatureName );
                    formData.append( 'signature_date', signatureDate );
                    formData.append( 'resource_date', resourceDate );
                    formData.append( 'resource_name', resourceName );
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

                    formData.append( 'list_a_data', JSON.stringify( listAData ) );
                    formData.append( 'list_b_data', JSON.stringify( listBData ) );

                    // let resourceSign = $( '.mld-resource-signatur input' );
                    // let resourceSignature = resourceSign[0].files[0];

                    let emailSign = $( '.mld-signature input' );
                    let emailSignature = emailSign[0].files[0];

                    formData.append( 'email_sign', emailSignature );
                    // formData.append( 'resource_sign', resourceSignature );

                    var ajaxurl = MLD.ajaxURL;

                    $.ajax( {
                        url: ajaxurl,
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function( resp ) {
                            window.location.href = 'https://myrtlelearning.com/thank-you-2/';
                        }
                    } );
                } );
            },

            /**
             * Add another refree
             */
            addAnotherRefree: function() {

                $( document ).on( 'click', '.mld-add-another-referr', function() {

                    let refHtml = '';

                    refHtml += '<table class="mld-refrence-form-wrapper">';
                    refHtml += '<tr class="mld-refree-heading"><th colspan="2">Referee</th></tr>';
                    refHtml += '<tr colspan="2" class="mld-empty-row"></tr>';
                    refHtml += '<tr><td width="40%" class="mld-filled-data mld-first-label">Name of Applicant:</td><td width="60%" class="mld-answer-box mld-first-answer"><input></td></tr>';
                    refHtml += '<tr><td width="40%" class="mld-filled-data mld-general-data">Position Applied for:</td><td width="60%" class="mld-answer-box mld-general-answer"><input></td></tr>';
                    refHtml += '<tr><td width="40%" class="mld-filled-data mld-general-data">Name of Referee:</td><td width="60%" class="mld-answer-box mld-general-answer"><input></td></tr>';
                    refHtml += '<tr><td width="40%" class="mld-filled-data mld-general-data">Email Address of Referee:</td><td width="60%" class="mld-answer-box mld-general-answer"><input></td></tr>';
                    refHtml += '<tr><td width="40%" class="mld-filled-data mld-general-data">Phone Number of Referee:</td><td width="60%" class="mld-answer-box mld-general-answer"><input></td></tr>';
                    refHtml += '<tr><td width="40%" class="mld-filled-data mld-last-label">Name of Organisation:</td><td width="60%" class="mld-answer-box mld-last-answer"><input></td></tr>';
                    refHtml += '</table>';
                    $( '.mld-refrence-form-wrapper' ).after( refHtml );
                } );
            },

            /**
             * upload user form
             */
            uploadUserForms: function() {

                $( document ).on( 'click', '.mld-user-upload-btn', function() {

                    $( '.mld-checklist-error' ).hide();

                    let self = $(this);
                    let userID = self.attr( 'data-user_id' );
                    
                    let formArray = [];
                    var formData = new FormData();
                    $.each( $( '.mld-teacher-inner-wrapper' ), function( index, elem ) {

                        let formTitle = $( elem ).find( '.mld-form-title' ).val();
                        let teacherFiles = $( elem ).find( '.mld-teacher-uploads' );
                        let teacherForm = teacherFiles[0].files[0];
                        
                        if( teacherForm && formTitle ) {

                            formArray.push( formTitle );
                            var formTitleWithDash = formTitle.replace(/ /g, '_');
                            formData.append( formTitleWithDash, teacherForm );
                        }   
                    } );

                    /**
                     * refrence array
                     */
                    let refArray = [];
                    $.each( $( '.mld-ref-main-wrapper table' ), function( index, elem ) {
                        let innerArray = [];
                        $.each( $( elem ).find( 'tr' ), function( innerIndex, innerElement ) {

                            let label = $( innerElement ).find( '.mld-filled-data' ).text();
                            label = label.replace(/\s/g, "_").replace(/:/g, "");
                            label = label.toLowerCase();
                            
                            let val = $( innerElement ).find( '.mld-answer-box input' ).val();

                            if( label && val ) {
                                innerArray.push( {[label]: val} );
                            }
                        } );

                        if( innerArray.length !== 0 ) {
                            refArray.push( innerArray );
                        }
                    } );

                    let Refreecount = refArray.length;

                    if( Refreecount < 2 ) {
                        $( '.mld-checklist-error' ).show();
                        return false;
                    }

                    self.val( 'Updating...' );
                    formData.append( 'action', 'mld_upload_user_form' );
                    formData.append( 'teacher_form_title', JSON.stringify( formArray ) );
                    formData.append( 'mld-user-id', userID );
                    formData.append( 'refrence-form-data', JSON.stringify( refArray ) );
                    var ajaxurl = MLD.ajaxURL;

                    $.ajax( {
                        url: ajaxurl,
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function( resp ) {
                            self.val( 'Update' );
                        }
                    } );
                } );
            },

            /**
             * add new uploader
             */
            addNewUploder: function() {

                $( document ).on( 'click', '.mld-new-form-uploader', function() {
                    
                    let html = '';
                    html += '<div class="mld-teacher-inner-wrapper">';
                    html += '<p><input type="text" placeholder="Teacher Form Title" class="mld-form-title"></p>';
                    html += '<p><input type="file" class="mld-teacher-uploads" class="mld-teacher-form"></p>';
                    html += '</div>';
                    $( '.mld-new-form-uploader' ).after( html );
                } );
            },

            /**
             * checklist popup
             */
            checklist: function() {

                $( document ).on( 'click', '.mld-checklist', function() {

                    $( ".mld-pop-outer" ).fadeIn( "slow" );
                    $( '.mld-pop-inner' ).css( 'width', '800px' );
                    $( '.mld-pop-inner' ).css( 'height', '500px' );
                    $( '.mld-pop-inner' ).css( 'overflow-y', 'auto' );
                    $( '.mld-user-pdf' ).hide();
                    $( '.mld-terms-condition-wrapper' ).hide();
                    $( '.mld-password-flield-wrapper' ).hide();

                    let data = {
                        'action'          : 'update_checklist_data',
                        'mld_nounce'      : MLD.security
                    };

                    jQuery.post( MLD.ajaxURL, data, function( response ) {
                        
                        let jsonEncode = JSON.parse( response );

                        if( 'true' == jsonEncode.status ) {
                            $( '.mld-popup-header' ).after( jsonEncode.content );
                        }
                    } );
                } );
            },

            /**
             * Delete a policy type
             */
            deletePolicyType: function() {

                $( document ).on( 'click', '.mld-delete-policy-type', function() {

                    var result = confirm("Are you sure you want to delete this Policy Type ?");

                    if (result === true) {

                        let self = $(this);
                        let policyType = self.data( 'type' );
                        self.text( 'Deleting' );
                        let data = {
                            'action'          : 'delete_policy_type',
                            'mld_nounce'      : MLD.security,
                            'policy_type'     : policyType
                        };

                        jQuery.post( MLD.ajaxURL, data, function( response ) {
                            self.parents( '.mld-main-policy-wrapper' ).remove();
                        } );
                    }
                } );
            },

            /**
             * update pdf comment
             */
            updatePdfComment: function() {

                $( document ).on( 'click', '.mld-pdf-comment-btn', function() {

                    let self = $(this);
                    let userID = self.data( 'user_id' );
                    let key = self.data( 'key' );
                    let comment = self.parents( '.mld-comment-footer-section' ).find( '.mld-comment-text' ).val(); 
                    if( ! userID || ! key || ! comment ) {
                        return false;
                    }

                    let html = '';
                    html += '<div class="mld-teachers-comment">'+comment+'</div>';
                    self.parents( '.mld-pdf-comment-wrapper' ).find( '.mld-comment-section' ).append( html );
                    
                    let data = {
                        'action'          : 'pdf_update_comment',
                        'mld_nounce'      : MLD.security,
                        'user_id'         : userID,
                        'key'             : key,
                        'comment'         : comment
                    };

                    jQuery.post( MLD.ajaxURL, data, function( response ) {
                        $( '.mld-comment-text' ).val( '' );
                    } );
                } );
            },

            /**
             * open pdf comment popup
             */
            pdfComment: function() {

                $( document ).on( 'click', '.mld-pdf-comment', function() {

                    let self = $(this);
                    let pdfKey = self.data( 'pdf_key' );
                    let userID = self.data( 'user_id' );
                    let capable = self.data( 'is_capable' );
                    $( '.mld-delete-pdf input' ).hide();
                    $( ".mld-pop-outer" ).fadeIn( "slow" );
                    $( '.mld-user-pdf' ).hide();
                    
                    let data = {
                        'action'          : 'pdf_comment_html',
                        'mld_nounce'      : MLD.security,
                        'user_id'         : userID,
                        'key'             : pdfKey,
                        'capable'         : capable
                    };

                    jQuery.post( MLD.ajaxURL, data, function( response ) {

                        let jsonEncode = JSON.parse( response );

                        if( 'true' == jsonEncode.status ) {

                            $( '.mld-password-flield-wrapper' ).html( jsonEncode.content );
                        }
                    } );
                } );
            },

            /**
             * open term and condition popup
             */
            openTermAndConditionPopup: function() {
                $( document ).on( 'click', '.mld-term-condition-btn', function() {
                    
                    $( ".mld-pop-outer" ).fadeIn( "slow" );
                    $( '.mld-pop-inner' ).css( 'width', '800px' );
                    $( '.mld-pop-inner' ).css( 'height', '500px' );
                    $( '.mld-pop-inner' ).css( 'overflow-y', 'auto' );
                    $( '.mld-user-pdf' ).hide();
                } );
            },


            /**
             * remove category html
             */
            removeCategoryHtml: function() {

                $( document ).on( 'click', '.mld-close', function() {
                    
                    $( '.mld-user-pdf' ).show();
                    $( '.mld-password-flield-wrapper' ).show();
                    $( '.mld-terms-condition-wrapper' ).remove();
                    $( '.mld-forms-wrapper' ).remove();
                } );

                $( document ).on( 'click', '.mld-term-condition-btn', function() {

                    let data = {
                        'action'          : 'terms_and_conditions',
                        'mld_nounce'      : MLD.security
                    };

                    jQuery.post( MLD.ajaxURL, data, function( response ) {

                        let jsonEncode = JSON.parse( response );

                        if( 'true' == jsonEncode.status ) {

                            $( '.mld-password-flield-wrapper' ).after( jsonEncode.content );
                            $( '.mld-password-flield-wrapper' ).hide();
                        }
                    } );
                } );

                $( document ).on( 'click', '.mld-close-category', function() {

                    $( '.add-category-content' ).show();

                    let self = $(this);
                    self.parents( '.mld-close-category-wrapper' ).remove();
                } );
            },

            /**
             * User Account html
             */
            getUserAccountHtml: function() {

                $( '.mld-account-dropdowns-wrapper' ).parent( '.elementor-widget-container' ).css( 'height', 'auto' );

                let profileSrc = $( '.mld-main-avatar img' ).attr( 'src' );
                let defaultSrc = $( '.mld-main-avatar' ).attr( 'profile-default_src' );

                if( ! profileSrc ) {
                    $( '.mld-main-avatar img' ).attr( 'src', defaultSrc );
                }

                $( document ).on( 'click', '.mld-account-submit', function() {

                    $( '.mld-pdf-files' ).remove();
                    let group_id = $( '.mld-account-group-dropdown' ).val();
                    let userID = $( '.mld-account-users' ).val();

                    if( ! group_id || ! userID ) {
                        return false;
                    }

                    $(this).val( 'Applying...' );

                    let data = {
                        'action'          : 'get_user_account_html',
                        'mld_nounce'      : MLD.security,
                        'user_id'        : userID
                    };

                    jQuery.post( MLD.ajaxURL, data, function( response ) {

                        let jsonEncode = JSON.parse( response );

                        if( 'true' == jsonEncode.status ) {
                            $( '.mld-account-main-wrapper' ).remove();
                            $( '.mld-account-content-wrapper' ).html( jsonEncode.content );
                            $( '.mld-term-button' ).css( 'display', 'none' );
                            $( '.mld-account-submit' ).val(  'Apply' );

                            let userProfileSrc = $( '.mld-main-avatar img' ).attr( 'src' );
                            
                            if( ! userProfileSrc ) {
                                $( '.mld-main-avatar img' ).attr( 'src', defaultSrc );
                            }
                        }
                    } );
                } );
            },

            /**
             * Account courses on chnage
             */
            accountCoursesOnchange: function() {

                $( document ).on( 'change', '.mld-account-users', function() {

                    let self = $(this);
                    let courseID = self.val();

                    if( ! courseID ) {
                        return false;
                    } 

                    $( '.mld-account-submit' ).removeAttr( 'disabled' );
                } );
            },

            /**
             * Display course according to group
             */
            displayCourseAccordingToGroup: function() {

                $( document ).on( 'change', '.mld-account-group-dropdown', function() {

                    let self = $(this);
                    let groupID = self.val();

                    if( ! groupID ) {
                        return false;
                    }

                    let data = {
                        'action'          : 'get_account_group_users',
                        'mld_nounce'      : MLD.security,
                        'group_id'        : groupID
                    };

                    jQuery.post( MLD.ajaxURL, data, function( response ) {

                        let jsonEncode = JSON.parse( response );

                        if( 'true' == jsonEncode.status ) {
                            $( '.mld-account-users' ).html( jsonEncode.content );
                        }
                    } );
                } );
            },

            /**
             * Add more category
             */
            addMoreCategory: function () {

                $( document ).on( 'click', '.add-category-content', function() {

                    let self = $(this);
                    self.hide();
                    self.before( '<div class="mld-close-category-wrapper"><span class="dashicons dashicons-no mld-close-category"></span><input type="text" class="category-input-field" placeholder="Enter Category"><button class="add-category-button">Add Category</button></div>' );
                } );

                $( document ).on( 'click', '.add-category-button', function() {

                    let categoryType = $( '.category-input-field' ).val();
                    
                    if( ! categoryType ) {
                        return false;
                    }

                    let data = {
                        'action' : 'add_policy_category',
                        'type'   : categoryType
                    };

                    jQuery.post( MLD.ajaxURL, data, function( response ) {

                        let jsonEncode = JSON.parse( response );

                        if( 'true' == jsonEncode.status ) {

                             $( '.mld-add-policy-wrapper' ).before( jsonEncode.content );
                             $( '.add-category-content' ).show();
                             $( '.mld-close-category-wrapper' ).remove();
                        }
                    } );
                } );
            },

            /**
             * working on pdf popup
             */ 
            pdfPopup: function() {

                $( document ).on( 'click', '.mld-view-all-policy', function() {

                    let self = $( this );

                    $( ".mld-pop-outer" ).fadeIn( "slow" );
                    $( '.mld-pop-inner' ).css( 'width', '800px' );
                    $( '.mld-pop-inner' ).css( 'height', '500px' );
                    $( '.mld-pop-inner' ).css( 'overflow-y', 'auto' );

                    let pdfType = self.data( 'category' );
                    let policyType = pdfType.replace("mld-policy-type-", "");
                    policyType = policyType.replace(/-/g, ' ');
                    
                    // Convert to title case
                    var titleCase = policyType.replace(/\b\w/g, function(match) {
                        return match.toUpperCase();
                    });

                    let data = {
                        'action' : 'get_policies',
                        'type'   : pdfType
                    };

                    jQuery.post( MLD.ajaxURL, data, function( response ) {

                        let jsonEncode = JSON.parse( response );

                        if( 'true' == jsonEncode.pdfpopup ) {
                            $( '.mld-pdf-popup-content' ).html( jsonEncode.content );
                            $( '.mld-user-pdf' ).css( 'display', 'flex' );
                            $( '.mld-header-title' ).text( titleCase );
                            $( '.mld-delete-pdf' ).hide();
                            $( '.mld-header-title' ).css( 'width', '95%' );
                            $( '.mld-close' ).css( 'width', '5%' );
                        } else {
                            $( '.mld-account-main-wrapper' ).hide();
                            $( '.mld-policies-main-wrapper' ).show();
                            $( '.mld-comment-loader' ).hide();
                            $( '.mld-upload-policies-cont' ).html( jsonEncode.content );
                        }
                    } );
                } );
            },

            /** 
             * toggle delete option
             */
            toggleDeleteOption: function() {

                $( document ).on( 'click', '.mld-single-pdf-dele-option img', function() {

                    let self = $(this);
                    self.next().slideToggle();
                } );
            },

            /** 
             * search pdf 
             */
            searchPDF: function() {

                $( document ).on( 'keyup', '.mld-policy-search-field', function() {
                    MLD_ACCOUNT.SearchPdfFiles( 'mld-pdf-table' );
                } );
            },

            wrapSubjectDropdown: function() {

                let subjectHtml = $( '.mld-subject-wrapper' ).html();
                $( '.mld-subject-details-inner-wrapper .mld-input-content' ).append( subjectHtml );
                $( '.mld-subject-wrapper select' ).hide();
            },

            /**
             * Remove Tags
             */
            removeTags: function() {

                $( document ).on( 'click', '.mld-subject-details-wrapper .mld-tag-delete-text', function() {

                    let self = $(this);
                    self.parents( '.mld-tag-button' ).remove();
                    let subjectClass = self.attr( 'subject-class' );
                    $( '.'+subjectClass ).show();
                } );
            },

            /**
             * remove updated subject
             */
            removeUpdateSubjects: function() {

                $.each( $( '.mld-tag-button' ), function( index, elem ) {
                    let val = $( elem ).text();
                    let removeSpace = val.replace(/\s+/g, "-");
                    $( '.'+removeSpace ).remove();
                } );
            },

            /**
             * save changes
             */
            saveChanges: function() {

                $( document ).on( 'click', '.mld-save-changes-btn', function() {

                    let self = $( this );
                    self.text( 'Saving...' );
                    let contactOne = $( '.mld-contact-one' ).val();
                    let contactTwo = $( '.mld-contact-two' ).val();
                    let userID = self.attr( 'user_id' );
                    
                    var tags = [];
                    $.each( $( '.mld-tag-button' ), function( index, elem ) {

                        let text = $( elem ).text();
                        tags.push( text );
                    } );

                    let data = {
                        'action'        : 'update_contacts',
                        'contact_one'   : contactOne,
                        'contact_two'   : contactTwo,
                        'tags'          : tags,
                        'user_id'       : userID
                    };

                    jQuery.post( MLD.ajaxURL, data, function( response ) {
                        self.text( 'Save Changes' );
                    } );
                } );
            },

            /**
             * create subject tags
             */
            subjectOnChange: function() {

                $( document ).on( 'change', '.mld-subject-details-dropdown select', function() {

                    let self = $( this );
                    let value = self.val();
                    let id = value.replace(/\s+/g, '-');
                    let val = $( '#'+id ).prev().text();
                    
                    if( value != val ) {
                        let imgURL = $( '.mld-three-dot-url' ).val();
                        let html = '<div class="mld-tag-button">'+value+'';
                        html += '<div class="mld-delete-pdf">';
                        html += '<img src="'+imgURL+'" class="mld-pdf-delete">';
                        html += '<p><input type="button" class="mld-tag-delete-text" value="Delete" subject-class="'+id+'"></p>';
                        html += '</div>';
                        html += '</div>';

                        $( '.mld-tags-wrapper' ).append( html );
                        $(".mld-subject-details-dropdown select option[value='" + value + "']").hide();
                    } 
                } );
            },

            /**
             * Delete a uploaded pdf
             */
            deletePDF: function() {

                $(document).on('click', function() {
                    $( '.mld-tag-delete-text' ).hide();
                } );

                $( document ).on( 'click', '.mld-delete-pdf img', function( event ) {

                    event.stopPropagation();
                    let self = $( this );
                    self.parents( '.mld-delete-pdf' ).find( 'input' ).slideToggle();
                } );

                $( document ).on( 'click', '.mld-delete-pdf input, .mld-policy-delete', function() {

                    let self = $( this );
                    let url = self.data( 'url' );

                    let parent = self.parents( '.mld-pdf-table' );
                    let tableID = parent.attr( 'id' );
                    let rowLenght = $( '#'+tableID+' tr' ).length;
                   
                    if( ! url ) {
                        return;
                    }

                    let data = {
                        'action' : 'delete_policies',
                        'url'    : url
                    };

                    jQuery.post( MLD.ajaxURL, data, function( response ) {

                        self.parents( '.mld-pdf-main-wrapper' ).hide();
                        self.parents( 'tr' ).remove();
                        let length = rowLenght - 1;
                        if( length < 1 ) {
                            $( '#'+tableID ).show();
                        }
                    } );
                } );
            },

            /**
             * update user profile
             */
            updateUserProfile: function() {

                $( document ).on( 'click', '.mld-edit-profile img', function() {
                    $( '.mld-edit-profile-input' ).click();
                } );

                $( document ).on( 'change', '.mld-edit-profile-input', function() {

                    let files = $( '.mld-edit-profile-input' );
                    var file = $( files )[0].files[0];
                    var formData = new FormData();
                    formData.append( 'file', file );
                    formData.append( 'action', 'change_user_profile' );
                    var ajaxurl = MLD.ajaxURL;

                    $.ajax( {
                        url: ajaxurl,
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function( resp ) {

                            $( '.mld-header-avatar img' ).attr( 'src', resp );
                            $( '.mld-main-avatar img' ).attr( 'src', resp );
                        }
                    } );
                } );
            },

            /**
             * Reset User Password
             */
            resetPassword: function() {

                $( document ).on( 'click', '.mld-reset-btn', function() {
                    $( ".mld-pop-outer" ).fadeIn( "slow" );
                    $( '.mld-user-pdf' ).hide();
                    
                } );

                $( document ).on( 'click', '.mld-update-pass-btn', function() {
                    
                    let self = $(this);
                    let oldPassword = $( '.mld-old-password' ).val();
                    let newPassword = $( '.mld-new-password' ).val();
                    let userID = self.attr( 'user_id' );

                    let data = {
                        'action'       : 'update_password',
                        'old_password' : oldPassword,
                        'new_password' : newPassword,
                        'user_id'      : userID
                    };

                    jQuery.post( MLD.ajaxURL, data, function( response ) {

                        let jsonEncode = JSON.parse( response );
                        $( '.mld-pass-error-message' ).show();
                        $( '.mld-pass-error-message' ).html( jsonEncode.content );
                    } );
                } );
            },

            /**
             * back on the main account content
             */
            goBackOnAccount: function() {

                $( document ).on( 'click', '.mld-policies-go-back', function() {

                    $( '.mld-account-content-wrapper .mld-account-main-wrapper' ).remove();
                    $( '.mld-account-main-wrapper' ).show();
                    $( '.mld-policies-main-wrapper' ).hide();
                    $( '.mld-files-main-wrapper' ).show();
                    $( '.mld-account-dropdowns-wrapper' ).show();
                    $( '.mld-account-group-dropdown option:first-child' ).prop( 'selected', true );
                    $( '.mld-account-users option:first-child' ).prop( 'selected', true );
                } );
            },

            /**
             * Upload policy
             */
            uploadPolicy: function() {

                $( document ).on( 'click', '.mld-upload-btn', function() {

                    let self = $(this);
                    let policyType = self.attr( 'data-category' );
                    $( '.mld-policy-file-input' ).attr( 'data-category', policyType );
                    $( '.mld-policy-file-input' ).click();
                } );

                $( document ).on( 'change', '.mld-policy-file-input', function() {

                    let self = $(this);
                    let policyType = self.attr( 'data-category' );
                    let files = $( '.mld-policy-file-input' );
                    var file = $( files )[0].files[0];
                    var formData = new FormData();
                    formData.append( 'file', file );
                    formData.append( 'policyType', policyType );
                    formData.append( 'action', 'upload_policy_pdf' );
                    var ajaxurl = MLD.ajaxURL;

                    $.ajax( {
                        url: ajaxurl,
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function( resp ) {

                            let tableID = '#'+policyType+' tbody';
                            $( tableID ).html( resp );
                            self.prev().hide();
                            $( '.mld-upload-policies-cont' ).html( resp );
                            $( '#'+policyType ).hide();
                        }
                    } );
                } );
            },

            /**
             * get policies
             */
            getPolicies: function() {

                $( document ).on( 'click', '.mld-invoice-btn', function() {

                    let self = $( this );
                    self.next().show();

                    let data = {
                        'action' : 'get_policies'
                    };

                    jQuery.post( MLD.ajaxURL, data, function( response ) {

                        let jsonEncode = JSON.parse( response );
                        $( '.mld-account-main-wrapper' ).hide();
                        $( '.mld-files-main-wrapper' ).hide();
                        $( '.mld-policies-main-wrapper' ).show();
                        $( '.mld-comment-loader' ).hide();
                        $( '.mld-account-dropdowns-wrapper' ).hide();
                        $( '.mld-upload-policies-cont' ).html( jsonEncode.content );
                    } );
                } );
            },

            /**
             * upload user pdfs
             */
            uploadPDF: function() {

                $( document ).on( 'click', '.mld-uplaod-admin-files', function() {
                    let self = $(this);
                    let parent = self.parents( '.mld-upload-files' );
                    parent.find( '.mld-pdf-files' ).click();
                } );

                $( document ).on( 'change', '.mld-pdf-files', function() {

                    let self = $(this);
                    let userID = self.attr( 'user_id' );
                    self.prev().show();
                    let files = $( '.mld-pdf-files' );
                    var file = $( files )[0].files[0];
                    var formData = new FormData();
                    formData.append( 'file', file );
                    formData.append( 'user_id', userID );
                    formData.append( 'action', 'upload_admin_pdf' );
                    var ajaxurl = MLD.ajaxURL;
                    
                    $.ajax( {
                        url: ajaxurl,
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function( resp ) {
                            $( '.mld-comment-loader' ).hide();
                            $( '.mld-user-pdf' ).html( resp );
                        }
                    } );
                } );
            },

            /**
             * create a function to search a pdf
             */
            SearchPdfFiles: function( tableID ) {

                var input, filter, table, tr, td, i, txtValue;
                input = $( '.mld-policy-search-field' ).val();

                if( input ) {

                    filter = input.toUpperCase();
                    table = document.getElementsByClassName( tableID );

                    $.each( $( table ), function( index, elem ) {

                        tr = elem.getElementsByTagName( "tr" );

                        for (i = 0; i < tr.length; i++) {

                            td = tr[i].getElementsByTagName("td")[1];

                            if ( td ) {

                                txtValue = td.textContent || td.innerText;

                                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                                    tr[i].style.display = "revert";
                                } else {
                                    tr[i].style.display = "none";
                                }
                            }     
                        }
                    } );
                } else {
                    $( '.mld-policy-content tr' ).css( 'display', 'revert' );
                    $( '.mld-policies-types-wrapper .mld-policy-content' ).css( 'overflow', 'hidden' );
                }
            }
        };
        MLD_ACCOUNT.init();
    });
})( jQuery );