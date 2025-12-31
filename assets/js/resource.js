(function( $ ) { 'use strict';

    $( document ).ready( function() {

        var MLD_RESOURCE_BACKEND = {

            init: function() {
                this.openMediaUploader();
                this.removePDF();
                this.subjectOnChange();
                this.apeendOptionInYearSelect();
                this.addCloneOption();
            },

            /**
             * add clone option in resourece post type
             */
            addCloneOption: function() {

                $( document ).on( 'click', '.mld-resource-clone', function() {

                    let self = $( this );
                    let resourceID = self.attr( 'data-post_id' );
                    
                    let data = {
                        'action'               : 'mld_add_clone',
                        'mld_nounce'           : MLD.security,
                        'resource_id'          : resourceID
                    };

                    jQuery.post( MLD.ajaxURL, data, function( response ) {

                        let jsonEncode = JSON.parse( response );

                        if( jsonEncode.status == 'true' ) {
                            location.reload();
                        }
                    } );
                } );
            },

            /**
             * append option in year select dropdown
             */
            apeendOptionInYearSelect: function() {

                let selectedYear = $( '.mld_selected_year' ).val();
                var ddlYears = $("#mld_years");

                for ( var i = 2011; i <= 2050; i++ ) {

                    var option = $("<option />");
                    option.html(i);
                    option.val(i);
                    if( selectedYear == i ) {
                        option.attr( 'selected', 'selected' );
                    }
                    ddlYears.append(option);
                }
            },

            /**
             * convert input type text into year picker
             */
            convertTextintoYearPicker: function() {

                $("#mld_datepicker").datepicker( {
                    format: "yyyy",
                    viewMode: "years",
                    minViewMode: "years",
                    autoclose:true
                 } );
            },
            /**
             * display exam according to subject
             */
            subjectOnChange: function() {

                $( document ).on( 'change', '#mld-subject-dropdown', function() {

                    let subjectID = $( '#mld-subject-dropdown' ).val();

                    let data = {
                        'action'               : 'get_subject_categories',
                        'mld_nounce'           : MLD.security,
                        'subject_id'           : subjectID
                    };

                    jQuery.post( MLD.ajaxURL, data, function( response ) {

                        let jsonEncode = JSON.parse( response );

                        if( jsonEncode.status == 'true' ) {
                            $( '#mld-resource-exam' ).html( jsonEncode.content );
                        }
                    } );
                } );
            },

            /**
             * remove pdf
             */
            removePDF: function() {

                $( document ).on( 'click', '.mld-remove-pdf', function() {
                    $( '.mld-remove-pdf' ).addClass( 'mld-resource-pdf-class' );
                    $( '.mld-resource-pdf-class' ).removeClass( 'mld-remove-pdf' );
                    $( '.mld-pdf-img' ).remove();
                    $( '.mld-resource-hidden-pdf-class' ).val( '' );
                    $( '.mld-resource-pdf-class' ).text( 'Add PDF' );
                } );
            },

            /**
             * open media uploader
             */
            openMediaUploader: function() {

                let mediaUploaders;

                $( document ).on( 'click', '.mld-resource-pdf-class', function(e) {
                    
                    e.preventDefault();

                    let self = $(this);
                    let postId = self.attr( 'data-post_id' );

                    if( mediaUploaders ) {
                        mediaUploaders.open();
                        return;
                    }

                    mediaUploaders = wp.media.frames.file_frame = wp.media({
                    title: 'Upload a PDF',
                        button: {
                        text: 'Upload PDF'
                    },
                        multiple: false
                    });

                    let attachment = '';

                    mediaUploaders.on('select', function() {

                    	attachment = mediaUploaders.state().get('selection').first().toJSON();

                        $( '.mld-pdf-img' ).attr( 'src', attachment.url );
                        $( '.mld-resource-hidden-pdf-class' ).attr( 'value', attachment.url );
                    	$( '.mld-inner-wrap-content .mld-pdf' ).html( '<embed src="'+attachment.url+'" class="mld-pdf-img" type="application/pdf" width="252px" height="266px" />' );
                        $( '.mld-resource-pdf-class' ).text( 'Remove PDF' );
                        $( '.mld-resource-pdf-class' ).addClass( 'mld-remove-pdf' );
                        $( '.mld-resource-pdf-class' ).removeClass( 'mld-resource-pdf-class' );
                    } );
                    mediaUploaders.open();
                } );
            },
        };

        MLD_RESOURCE_BACKEND.init();
    });
})( jQuery );