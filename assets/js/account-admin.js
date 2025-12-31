(function( $ ) { 'use strict';

    $( document ).ready( function() {

        var MLD_ADMIN_ACCOUNT = {

            init: function() {
                this.updateResourceVerification();
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
        };
        MLD_ADMIN_ACCOUNT.init();
    });
})( jQuery );
