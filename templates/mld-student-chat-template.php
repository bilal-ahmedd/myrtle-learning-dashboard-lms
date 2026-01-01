<?php

/**
 * Template to display [mld_student_dashboard] shortcode chat content
 */
if (! defined('ABSPATH')) exit;
?>
<div class="mld-std-chat-content">
    
    <div class="mld-std-chat-filter">
        <h4> <?php echo __('Groups') ?></h4>
        <div class="mld-std-chat-filter-fields" id="mldChatFilter">
          <select id="mld_group_select">
            <?php if( empty( $groups ) ) { ?>
              <option value=""><?php echo esc_html__( 'No groups found', 'mld' ); ?></option>
            <?php } else { ?>
              <option value="Select Group">
                <?php echo __( "Select Group", "myrtle-learning-dashboard" ); ?>
              </option>
              <?php foreach( $groups as $g ) { ?>
                <option value="<?php echo esc_attr( $g['id'] ); ?>" data-user-id="<?php echo esc_attr( $user_id ); ?>" data-group-id="<?php echo esc_attr( $g['id'] ); ?>" >
                  <?php echo esc_html( $g['title'] ); ?>
                </option>
              <?php } ?>
            <?php } ?>
          </select>
        </div>

    </div>
    <div class="mld-std-chat-wrap">
        <div class="mld-chat-shell">
  <div class="mld-chat-card">

    <!-- LEFT -->
    <aside class="mld-chat-left">
      <div class="mld-chat-left-head">
        <div class="mld-chat-h1">Messages</div>

        <div class="mld-chat-search">
          <span class="dashicons dashicons-search"></span>
          <input type="text" placeholder="search Message or Name" />
        </div>
      </div>

      <div class="mld-chat-users">
        <!-- user row -->
        <div class="mld-user-row is-active">
          <img class="mld-user-avatar" src="https://i.pravatar.cc/80?img=32" alt="">
          <div class="mld-user-meta">
            <div class="mld-user-name">Lavern Laboy</div>
            <div class="mld-user-last">Haha that's terrifying 😂</div>
          </div>
          <div class="mld-user-time">1h</div>
        </div>

        <!-- repeat rows -->
        <div class="mld-user-row">
          <img class="mld-user-avatar" src="https://i.pravatar.cc/80?img=32" alt="">
          <div class="mld-user-meta">
            <div class="mld-user-name">Lavern Laboy</div>
            <div class="mld-user-last">Haha that's terrifying 😂</div>
          </div>
          <div class="mld-user-time">1h</div>
        </div>
      </div>
    </aside>

    <!-- RIGHT -->
    <section class="mld-chat-right">

      <div class="mld-chat-panel">
        <!-- header -->
        <div class="mld-chat-top">
          <div class="mld-chat-top-left">
            <img class="mld-chat-top-avatar" src="https://i.pravatar.cc/80?img=32" alt="">
            <div>
              <div class="mld-chat-top-name">Lavern Laboy</div>
              <div class="mld-chat-top-sub">
                <span class="mld-online">Online</span>
                <span class="mld-dotsep">-</span>
                <span class="mld-lastseen">Last seen, 2.02pm</span>
              </div>
            </div>
          </div>

          <button class="mld-chat-more" type="button" aria-label="More">
            <span class="dashicons dashicons-ellipsis"></span>
          </button>
        </div>

        <!-- body -->
        <div class="mld-chat-body" id="mldChatBody">
          <div class="mld-msg is-left">
            <img class="mld-msg-av" src="https://i.pravatar.cc/80?img=32" alt="">
            <div class="mld-bubble">omg, this is amazing</div>
          </div>

          <div class="mld-msg is-left">
            <img class="mld-msg-av" src="https://i.pravatar.cc/80?img=32" alt="">
            <div class="mld-bubble">perfect! ✅</div>
          </div>

          <div class="mld-msg is-left">
            <img class="mld-msg-av" src="https://i.pravatar.cc/80?img=32" alt="">
            <div class="mld-bubble">Wow, this is really epic</div>
          </div>

          <div class="mld-msg is-right">
            <div class="mld-bubble is-me">How are you?</div>
            <img class="mld-msg-av" src="https://i.pravatar.cc/80?img=12" alt="">
          </div>

          <div class="mld-msg is-left">
            <img class="mld-msg-av" src="https://i.pravatar.cc/80?img=32" alt="">
            <div class="mld-bubble">I'm good bro</div>
          </div>

          <div class="mld-msg is-left">
            <img class="mld-msg-av" src="https://i.pravatar.cc/80?img=32" alt="">
            <div class="mld-bubble">perfect! ✅</div>
          </div>

          <div class="mld-msg is-left">
            <img class="mld-msg-av" src="https://i.pravatar.cc/80?img=32" alt="">
            <div class="mld-bubble">just ideas for next time</div>
          </div>

          <div class="mld-msg is-right">
            <div class="mld-bubble is-me">wooooooo</div>
            <img class="mld-msg-av" src="https://i.pravatar.cc/80?img=12" alt="">
          </div>

          <div class="mld-msg is-right">
            <div class="mld-bubble is-me">Haha that's terrifying 😂</div>
            <img class="mld-msg-av" src="https://i.pravatar.cc/80?img=12" alt="">
          </div>

          <div class="mld-msg is-right">
            <div class="mld-bubble is-me">What are you doing now a days</div>
            <img class="mld-msg-av" src="https://i.pravatar.cc/80?img=12" alt="">
          </div>
        </div>

        <!-- composer -->
        <div class="mld-chat-compose">
          <button class="mld-compose-ic" type="button" aria-label="Attachment">
            <span class="dashicons dashicons-paperclip"></span>
          </button>
          <button class="mld-compose-ic" type="button" aria-label="Emoji">
            <span class="dashicons dashicons-smiley"></span>
          </button>

          <div class="mld-compose-input">
            <input id="mldChatInput" type="text" placeholder="Type a message" />
            <button id="mldChatSend" class="mld-send" type="button" aria-label="Send">
              <span class="dashicons dashicons-arrow-right-alt"></span>
            </button>
          </div>
        </div>

      </div>
    </section>

  </div>
</div>

    </div>

</div>