(function( $ ) { 'use strict';

    $( document ).ready( function() {

        var MLD_CHAT = {

            init: function() {

                /**
                 * start chat js
                 */
                this.searchUser();
                this.sendMessageToUser();
                this.displayTeacherOrUsers();
                this.changeCurrentUserChat();
                this.liveUpdateChat();
                this.removeUserChat();
                this.makeChatboxResponsive();
                this.setLocalStorage();
                this.addMessageDeleteOption();
                this.loadMoreChat();
            },

            /**
             * load more chat
             */
            loadMoreChat: function() {

                $( document ).on( 'click', '.mld-load-more-btn', function() {

                    let self = $(this);
                    self.text( 'Loading...' );
                    let groupID = self.attr( 'data-group_id' );
                    let userID = self.attr( 'data-user_id' );
                    let chatType = self.attr( 'data-chat_type' );
                    let chatUser = self.attr( 'data-chat_user' );
                    let page = self.attr( 'data-paged' );
                    let updatedPage = parseInt(page) + 1;
                    self.attr( 'data-paged', updatedPage );

                    let data = {
                        'action'          : 'load_more_messages',
                        'group_id'        : groupID,
                        'user_id'         : userID,
                        'chat_type'       : chatType,
                        'chat_user'       : chatUser,
                        'page'            : updatedPage
                    };

                    jQuery.post( MLD.ajaxURL, data, function( response ) {
                        
                        let jsonEncode = JSON.parse( response );
                        if( 'true' == jsonEncode.status ) {
                             $( '.mld-load-more-btn' ).text('Load more');
                             $( '.mld-load-more-btn' ).after( jsonEncode.content );
        
                             if( jsonEncode.count < 20 ) {
                                $( '.mld-load-more-btn' ).remove();
                             }
                        }
                    } );

                } );
            },

            /**
             * Add message delete option  
             */
            addMessageDeleteOption: function() {

                $( document ).on( 'click', '.mld-delete-message', function() {
                    
                    let self = $(this);
                    let ID = self.attr( 'data-id' );                   
                    
                    let data = {
                        'action'          : 'delete_message',
                        'id'              : ID
                    };

                    jQuery.post( MLD.ajaxURL, data, function( response ) {

                        self.prev().remove();
                        self.remove();
                    } );

                } );
            },

            /**
             * set local storage for chatting
             */
            setLocalStorage: function() {

                $( document ).on( 'click', '.mld-user-local-storage', function() {

                    let self = $(this);
                    let doerID = self.data( 'doer' );
                    let userID = self.data( 'user_id' );
                    let groupID = self.data( 'group_id' );
                    let Type = self.data( 'type' );
                    let pageUrl = self.data( 'site' );
                    localStorage.setItem( 'GroupId', groupID );
                    localStorage.setItem( 'DoerId', doerID );
                    localStorage.setItem( 'UserId', userID );
                    localStorage.setItem( 'ChatType', Type );
                    window.location.replace( pageUrl );
                } );

                let storedDoerUser = localStorage.getItem( 'DoerId' );
                let storedUserId = localStorage.getItem( 'UserId' );
                let storedGroup = localStorage.getItem( 'GroupId' );
                let storedType = localStorage.getItem( 'ChatType' );

                if( storedDoerUser && storedGroup && storedType && storedUserId ) {

                    $( '.mld-group-field' ).val( storedGroup );

                    let data = {
                        'action'          : 'get_group_users',
                        'mld_nounce'      : MLD.security,
                        'group_id'        : storedGroup
                    };

                    jQuery.post( MLD.ajaxURL, data, function( response ) {

                        let jsonEncode = JSON.parse( response );
                        if( 'true' == jsonEncode.status ) {
                            $( '.mld-chat-users #mld-user-table' ).html( jsonEncode.content ).change();

                            if( 'general' == storedType ) {
                                $( '.mld-notification-'+storedUserId+'-'+storedGroup ).click();
                            } else {
                                $( '.mld-notification-'+storedGroup ).click();
                            }
                        }
                    } );
                }
            },

            /**
             *
             */
            makeChatboxResponsive: function () {

                $( window ).on( 'resize', function() {

                    if( $( window ).width() >= 950 ) {

                        $( '.mld-chatbox-container .mld-chatbox' ).show();
                        $( '.mld-chatbox-container .mld-chat-users' )
                    }
                } );
            },

            /**
             * remove user chat
             */
            removeUserChat: function() {

                $( 'body' ).on( 'click', '.mld-user-close span', function() {

                    let screenWidth = window.innerWidth;

                    if( 950 == screenWidth || screenWidth < 950 ) {

                        $( '.mld-chatbox-container .mld-chat-users' ).show();
                        $( '.mld-chatbox-container .mld-chatbox' ).hide();
                    } else {
                        $( '.mld-chatbox .mld-chat-box' ).hide();
                    }
                } );
            },


/**
 * Change current user chat
 */
changeCurrentUserChat: function() {

  $(document).on('click', '.mld-user-row', function() {

    let self         = $(this);
    let groupID      = self.data('group-id');
    let loggedInUser = self.data('logged_user');
    let chatUser     = self.data('chat-user');
    let chatType     = self.data('chat_type');
    let screenWidth  = window.innerWidth;

    // Blur right-side chat body (template uses .mld-chat-body / #mldChatBody)
    $('#mldChatBody').css('filter', 'blur(2.5px)');

    // OPTIONAL: If your left user row has name/avatar inside it, you can update header here
    // (won't break if elements aren't present)
    let userName   = self.find('.mld-chat-top-name').first().text();
    let profileUrl = self.find('img').first().attr('src');

    if (userName) $('.mld-chat-top-name').text(userName);
    if (profileUrl) $('.mld-chat-top-avatar').attr('src', profileUrl);

    // Fetch updated chat
    MLD_CHAT.getUpdatedChat(groupID, loggedInUser, chatUser, chatType);

    if (950 == screenWidth || screenWidth < 950) {
      $('.mld-chat-users').hide();
      $('.mld-chatbox').show();
    }

    $('.mld-chatbox').css('background', '#ffffff');
    $('.mld-chat-starter').hide();
    $('.mld-chatbox .mld-chat-box').show();
    $('.mld-notification-icon').hide();

    setTimeout(function() {
      let messageCount = $('.mld-msg').length; // template uses .mld-msg per message
      if (messageCount >= 19) {
        $('.mld-load-more-btn').show();
      }
    }, 5000);

  });
},

/**
 * Return updated chat data
 *
 * @returns {boolean}
 */
getUpdatedChat: function(groupID = '', loggedInUser = '', chatUser = '', chatType = '') {

  // No inputs in template, so fall back to stored state
  if ('' == groupID)      groupID      = (MLD_CHAT.current && MLD_CHAT.current.groupID)      ? MLD_CHAT.current.groupID      : '';
  if ('' == loggedInUser) loggedInUser = (MLD_CHAT.current && MLD_CHAT.current.loggedInUser) ? MLD_CHAT.current.loggedInUser : '';
  if ('' == chatUser)     chatUser     = (MLD_CHAT.current && MLD_CHAT.current.chatUser)     ? MLD_CHAT.current.chatUser     : '';
  if ('' == chatType)     chatType     = (MLD_CHAT.current && MLD_CHAT.current.chatType)     ? MLD_CHAT.current.chatType     : '';

  // If nothing selected yet, do nothing
  if (!groupID || !loggedInUser) {
    $('#mldChatBody').css('filter', 'blur(0)');
    return;
  }

  let chatDetails = {};

  $.each($('.mld-single-user-wrap'), function(index, elem) {
    chatDetails[index] = {
      'chat_user_id'    : $(elem).attr('data-chat_user'),
      'chat_type'       : $(elem).attr('data-chat_type'),
      'last_message_id' : $(elem).attr('data-last_message_id')
    };
  });

  let data = {
    'action'       : 'update_live_chat',
    'mld_nounce'   : MLD.security,
    'group_id'     : groupID,
    'loggin_user'  : loggedInUser,
    'chat_user'    : chatUser,
    'chat_type'    : chatType,
    'message_data' : chatDetails
  };

  jQuery.post(MLD.ajaxURL, data, function(response) {

    let jsonEncode = null;

    try {
      jsonEncode = (typeof response === 'string') ? JSON.parse(response) : response;
    } catch (e) {
      $('#mldChatBody').html('');
      $('#mldChatBody').css('filter', 'blur(0)');
      return;
    }

    if ('true' == jsonEncode.status) {

      // ✅ RIGHT-side template: messages go inside #mldChatBody
      // Your PHP should return message markup using:
      // <div class="mld-msg is-left">...</div> / <div class="mld-msg is-right">...</div>
      $('#mldChatBody').html(jsonEncode.content);

      // OPTIONAL header updates if backend sends these
      if (jsonEncode.header_name) $('.mld-chat-top-name').text(jsonEncode.header_name);
      if (jsonEncode.header_avatar) $('.mld-chat-top-avatar').attr('src', jsonEncode.header_avatar);
      if (jsonEncode.last_seen) $('.mld-lastseen').text(jsonEncode.last_seen);
      if (jsonEncode.is_online !== undefined) {
        $('.mld-online').text(jsonEncode.is_online ? 'Online' : 'Offline');
      }

      let serverChatType = jsonEncode.chatType;
      let user_id        = jsonEncode.userId;
      let content        = jsonEncode.message;
      let lastID         = jsonEncode.id;

      // Keep your existing notification logic intact
      if ('group' == serverChatType) {

        $('.mld-notification-' + groupID).find('.mld-notification-icon').show();
        $('.mld-notification-' + groupID).find('.mld-group-last-message').html(content);
        $('.mld-notification-' + groupID).attr('data-last_message_id', lastID);

      } else {

        // No inputs now; compare with current state
        let currentChatUserId = (MLD_CHAT.current && MLD_CHAT.current.chatUser) ? MLD_CHAT.current.chatUser : '';

        if (currentChatUserId != user_id) {
          $('.mld-notification-' + user_id + '-' + groupID).find('.mld-notification-icon').show();
        }

        $('.mld-notification-' + user_id + '-' + groupID).find('.mld-group-last-message').html(content);
        $('.mld-notification-' + user_id + '-' + groupID).attr('data-last_message_id', lastID);
      }

    } else {
      $('#mldChatBody').html('');
    }

    // Unblur
    $('#mldChatBody').css('filter', 'blur(0)');

    // Scroll to bottom (template uses #mldChatBody as the scroll container)
    let elem = document.getElementById('mldChatBody');
    if (elem) {
      elem.scrollTop = elem.scrollHeight;
    }

  });
},

/**
 * update chat on live
 */
liveUpdateChat: function() {

  if (undefined == MLD.chat_setings) {
    return;
  }

  let time = MLD.chat_setings.time;
  let unit = MLD.chat_setings.unit;

  let intervalTime;
  if ('second' == unit) {
    intervalTime = time * 1000;
  } else {
    intervalTime = time * 60000;
  }

  setInterval(function() {
    MLD_CHAT.getUpdatedChat(); // uses stored state now
  }, intervalTime);
},


            /**
             * Update users behalf of group changes
             */
            displayTeacherOrUsers: function() {

                $( document ).on( 'change', '#mld_group_select', function() {

                    let self = $( this );

                    if( self.val() == '' ) {
                        return false;
                    }

                    let chat_type = "";
                    if ( self.attr('id') == 'mld_group_select' ) {
                        chat_type = "group";
                    }

                    let data = {
                        'action'          : 'get_group_users',
                        'mld_nounce'      : MLD.security,
                        'group_id'        : self.val(),
                        'chat_type'       : chat_type,
                    };

                    jQuery.post( MLD.ajaxURL, data, function( response ) {

                        let jsonEncode = JSON.parse( response );
                        if( 'true' == jsonEncode.status ) {
                            $( '.mld-chat-users' ).html( jsonEncode.content ).change();
                            
                            $.each( $( '.mld-group-users' ), function( ind, ele ) {
                                
                                if( ! $(ele).find( 'img' ).attr( 'src' ) ) {
                                    $(ele).find( 'img' ).attr( 'src', $( ele ).attr( 'profile-default_src' ) );
                                }
                            } );
                        }
                    } );
                } );
            },

            /**
             * send t message to user
             */
            sendMessageToUser: function() {

                $( document ).on( 'keyup', '.mld-message-box textarea', function( e ) {

                    if( e.keyCode == 13 ) {

                        let self = $(this);
                        let message = self.val();
                        self.val( '' );

                        let parent = self.parents( '.mld-chat-box' );
                        let groupID = parent.find( 'input[name=groupID]' ).val();
                        let loggedInUser = parent.find( 'input[name=loggedInUser]' ).val();
                        let chatUser = parent.find( 'input[name=chatUser]' ).val();
                        let chatType = parent.find( 'input[name=chatType]' ).val();

                        if( 'group' == chatType ) {
                            chatUser = groupID;
                        }

                        if( '' == message || '' == chatUser || '' == loggedInUser || '' == chatType || '' == groupID ) {
                            return false;
                        }

                        let html = '<div class="mld-chat-msg-wrap">' +
                            '<div class="mld-sender-wrapper mld-sender-msg">' +
                            '<div class="mld-user-chat mld-sender">'+message+'</div>' +
                            '</div>' +
                            '<div class="mld-clear-both"></div>' +
                            '</div>';
                        $( '.mld-chat-msgs' ).last().append( html );

                        let elem = document.getElementById( 'mld-chat-scrolldown' );
                        elem.scrollTop = elem.scrollHeight;

                        let data = {
                            'action'            : 'update_chat',
                            'mld_nounce'        : MLD.security,
                            'message'           : message,
                            'group_id'          : groupID,
                            'chat_user'         : chatUser,
                            'chat_type'         : chatType,
                            'logged_in_user'    : loggedInUser
                        };

                        jQuery.post( MLD.ajaxURL, data, function( response ) {} );
                    }
                } );
            },

            /**
             * Search user on list
             */
            searchUser: function() {

                $( 'body' ).on( 'keyup', '#mld-search-input', function() {

                    let userInput = document.getElementById( "mld-search-input" );
                    let filterInput = userInput.value.toUpperCase();
                    let userTable = document.getElementById( "mld-user-table" );
                    let tableRow = userTable.getElementsByTagName("tr");
                    let i;
                    for ( i = 0; i < tableRow.length; i++ ) {

                        let tableData = tableRow[i].getElementsByTagName("td")[0];
                        if ( tableData ) {
                            let value = tableData.textContent || tableData.innerText;
                            if ( value.toUpperCase().indexOf( filterInput ) > -1 ) {
                                tableRow[i].style.display = "";
                            } else {
                                tableRow[i].style.display = "none";
                            }
                        }
                    }
                } );
            },
        };

        MLD_CHAT.init();
    });
})( jQuery );