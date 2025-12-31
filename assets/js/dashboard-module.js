(function( $ ) { 'use strict';

    $( document ).ready( function() {

        var DashBoardModule = {

            init: function() {
                this.SideBarMenuClickReflect();
            },

            /**
            * When user click on Sider bar wrapper so it direct click on ancor link
            */
            SideBarMenuClickReflect: function() {

                $( document ).on( 'click', '.mld-menu-handle-wrapper', function() {

                    $( this ).find( 'a' )[ 0 ].click();
                    
                } );
            }

        };

        DashBoardModule.init();
    });
})( jQuery );