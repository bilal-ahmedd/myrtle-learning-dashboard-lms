(function( $ ) { 'use strict';

    $( document ).ready( function() {

        var MLD_MENU = {

            init: function() {
                this.removeStartTime();
                this.addTargetAttribute();
                this.changeThisMonthText();
                this.loadMoreEvents();
            },

            /**
             * load more events
             */
            loadMoreEvents: function() {

                $( document ).on( 'click', '.mld-calendar-see-more', function() {
                    $( '.tribe-events-calendar-list .tribe-common-g-row.tribe-events-calendar-list__event-row' ).css( 'display', 'flex' );
                    $( '.mld-calendar-see-more' ).remove();
                } );
            },

            /**
             * change this month button text on calender page
             */
            changeThisMonthText: function() {

                setInterval( function () {
                    $( '.tribe-events-c-top-bar.tribe-events-header__top-bar a' ).html( 'My Calendar' );
                    $( '.tribe-events-c-top-bar.tribe-events-header__top-bar a' ).attr( 'href', '' );
                    $(".tribe-events-c-top-bar.tribe-events-header__top-bar a").off('click');
                    $( '.tribe-events-c-top-bar.tribe-events-header__top-bar a' ).addClass( 'mld-my-calendar-title' );
                }, 1000);
            },

            /**
             * Add target attribute
             */
            addTargetAttribute: function() {

                $( '.tribe-events-calendar-month__calendar-event-title a' ).attr( 'target', '_blank' );
                $( '.ecs-event-list a' ).attr( 'target', '_blank' );
                $( '.tribe-events-calendar-month__calendar-event-tooltip-title a' ).attr( 'target', '_blank' );
                $( '.tribe-events-calendar-month__multiday-event-hidden a' ).attr( 'target', '_blank' );
                $( '.tribe-events-calendar-month-mobile-events__mobile-event-title a' ).attr( 'target', '_blank' );
                $( '.tribe-events-calendar-list__event-title a' ).attr( 'target', '_blank' );
            },

            /**
             * remove start time
             */
            removeStartTime: function() {

                $( '.tribe-events-c-view-selector__list-item-link' ).attr( 'onclick', 'changeTime()' );
                $( '.tribe-events-calendar-month__more-events a' ).attr( 'onclick', 'changeTime()' );

                if( $('span').hasClass( 'tribe-event-date-start' ) ) {

                    $.each( $( '.tribe-event-date-start' ), function( index, elem ) {

                        let time = $(elem).text();
                        if( time.includes( '-' ) ) {
                            time = time.substring( 0, time.indexOf( '-' ) );
                        }
                        $(elem).text( time );
                    } );
                }

                if( $( 'abbr' ).hasClass( 'tribe-events-start-datetime' ) ) {
                    let starttime = $( '.tribe-events-start-datetime' ).text();
                    if( starttime.includes( '-' ) ) {
                        starttime = starttime.substring( 0, starttime.indexOf( '-' ) );
                    }
                    $( '.tribe-events-start-datetime' ).text( starttime );
                }
            },
        };
        MLD_MENU.init();
    });
})( jQuery );

function changeTime() {

    jQuery( document ).ready( function( $ ) {
        setTimeout( function() {

            if( $('span').hasClass( 'tribe-event-date-start' ) ) {

                $.each( $( '.tribe-event-date-start' ), function( index, elem ) {

                    let time = $(elem).text();
                    if( time.includes( '-' ) ) {
                        time = time.substring( 0, time.indexOf( '-' ) );
                    }
                    $(elem).text( time );
                } );
            }
        }, 8000 );

        setTimeout( function() {
            $( '.tribe-events-calendar-day__time-separator' ).hide();
            $( '.tribe-events-c-view-selector__list-item-link' ).attr( 'onclick', 'changeTime()' );
            $( '.tribe-events-calendar-month__more-events a' ).attr( 'onclick', 'changeTime()' );
            $( '.tribe-events-calendar-month__calendar-event-title a' ).attr( 'target', '_blank' );
            $( '.ecs-event-list a' ).attr( 'target', '_blank' );
            $( '.tribe-events-calendar-month__calendar-event-tooltip-title a' ).attr( 'target', '_blank' );
            $( '.tribe-events-calendar-month__multiday-event-hidden a' ).attr( 'target', '_blank' );
            $( '.tribe-events-calendar-month-mobile-events__mobile-event-title a' ).attr( 'target', '_blank' );
            $( '.tribe-events-calendar-day__event-title a' ).attr( 'target', '_blank' );
         }, 11000 );
    });
}