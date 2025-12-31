(function( $ ) { 'use strict';

    $( document ).ready( function() {

        var MLD_HEADER = {

            init: function() {
                // this.sideMenuResponsives();
                this.sideMenuResponsive();
                this.addActivClass();
                this.activeLogoutMenu();
            },

            /**
             * display logout menu
             */
            activeLogoutMenu: function() {

                $( '.mld-logout-arrow' ).click( function() {
                    $( '.mld-logout-dropdown' ).slideToggle();
                } );
            },

            /**
             * Add active class on side menu item
             */
            addActivClass: function() {

                let currentUrl = $(location).attr('href');
                let tabUrl = $( '.jet-profile-menu__item-link' ).attr( 'href' );

                $.each( $( '.jet-profile-menu__item-link' ), function( index, elem ) {

                    if( currentUrl != $( elem ).attr( 'href' ) ) {
                        return true;
                    }
                    $( elem ).addClass( 'mld-active-tab' );
                } );
            },

            /**
             * Sidebar responsive logic
             */
            sideMenuResponsive: function() {

                let sidebarVisible = false;
                let lastWidth = $(window).width();

                function checkSidebarVisibility() {
                    let currentWidth = $(window).width();

                    // Prevent triggering on mobile scroll
                    if (lastWidth === currentWidth) {
                        return;
                    }
                    lastWidth = currentWidth;

                    if (currentWidth <= 1024) {
                        $( '.cross-dashboard-bar' ).hide();
                        $( '.mld-menu-line' ).css({ visibility: 'visible' });
                        $( '.ml-side-bar' ).hide().css({ left: '-300px', position: 'absolute' });
                        sidebarVisible = false;
                    } else {
                        $( '.mld-menu-line' ).css({ visibility: 'hidden' });
                        $( '.ml-side-bar' ).show().css({ left: '0px', position: 'unset' });
                        sidebarVisible = true;
                    }
                }

                function showSidebar() {
                    if (!sidebarVisible) {
                        $( '.mld-menu-line' ).css({ visibility: 'hidden' });
                        $( '.cross-dashboard-bar' ).show();
                        $( '.ml-side-bar' ).show().css({ left: '-300px', zIndex: '9999', width: '350px', position: 'absolute' });

                        $( '.ml-side-bar' ).animate({ left: '0px' }, 300, function() {
                            sidebarVisible = true;
                        });
                    }
                }

                function hideSidebar() {
                    if (sidebarVisible) {
                        $( '.ml-side-bar' ).animate({ left: '-300px' }, 300, function() {
                            $( '.ml-side-bar' ).hide();
                            $( '.mld-menu-line' ).css({ visibility: 'visible' });
                            $( '.cross-dashboard-bar' ).hide();
                            sidebarVisible = false;
                        });
                    }
                }

                // Event listeners for menu toggle
                $( document ).on( 'click', '.mld-menu-line', showSidebar );
                $( document ).on( 'click', '.cross-dashboard-bar', hideSidebar );

                // Initial setup and optimized resize listener
                checkSidebarVisibility();
                $( window ).on( 'resize', checkSidebarVisibility );
            }


        };

        MLD_HEADER.init();
    });
})( jQuery );