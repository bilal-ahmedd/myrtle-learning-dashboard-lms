<?php

/**
 * Template to display [exms_student_dashboard] shortcode notifiation content
 */
if (! defined('ABSPATH')) exit;
?>
<h3 class="exms-std-dashboard-heading">
  <?php echo __('Notification', 'myrtle-learning-dashboard'); ?>
</h3>
<div class="mld-std-notification-content">
<div class="mld-dashboard-grid">

  <div class="mld-exam-card">

    <div class="mld-exam-head">
      <h4>
        <?php
        $items = [];

        if ( isset($notifications) && $notifications instanceof WP_Query && $notifications->have_posts() ) {
          while ( $notifications->have_posts() ) {
            $notifications->the_post();
            $items[] = get_post();
          }
          wp_reset_postdata();
        }

        $primary = $items[0] ?? null;

        if ( $primary ) {
          echo esc_html( get_the_title( $primary ) );
        } else {
          echo esc_html__( 'No Notifications', 'myrtle-learning-dashboard' );
        }
        ?>
      </h4>

      <p>
        <?php
        if ( $primary ) {
          $excerpt = has_excerpt( $primary ) ? get_the_excerpt( $primary ) : wp_trim_words( wp_strip_all_tags( $primary->post_content ), 22, '...' );
          echo esc_html( $excerpt );
        } else {
          echo esc_html__( 'There are currently no notifications.', 'myrtle-learning-dashboard' );
        }
        ?>
      </p>
    </div>

    <div class="mld-exam-body">

      <h5 class="mld-exam-sub">
        <?php
        if ( $primary ) {
          echo esc_html__( 'Details', 'myrtle-learning-dashboard' );
        } else {
          echo esc_html__( 'Start Times for Lessons', 'myrtle-learning-dashboard' ); 
        }
        ?>
      </h5>

      <?php if ( $primary ) { ?>
        <?php
          echo apply_filters( 'the_content', $primary->post_content );
        ?>
      <?php } else { ?>

        <p>
         <?php _e( "No Notifiation content.", "myrtle-learning-dashboard" ) ?>
        </p>

      <?php } ?>

    </div>
  </div>

  <div class="mld-notify-card">

    <div class="mld-notify-head">
      <span class="mld-notify-heading"><?php _e( "Other Notification", "myrtle-learning-dashboard" ); ?></span>
      <span class="dashicons dashicons-bell mld-notify-bell-icon"></span>
    </div>

    <div class="mld-notify-list">
      <?php
      $others = array_slice( $items, 1 );

      if ( ! empty( $others ) ) :
        foreach ( $others as $post_obj ) :
          $time_ago = human_time_diff( get_the_time( 'U', $post_obj ), current_time( 'timestamp' ) );

          $payload = [
            'id'      => (int) $post_obj->ID,
            'title'   => get_the_title( $post_obj ),
            'timeAgo' => sprintf( __( '%s ago', 'myrtle-learning-dashboard' ), $time_ago ),
            'excerpt' => has_excerpt( $post_obj )
                          ? get_the_excerpt( $post_obj )
                          : wp_trim_words( wp_strip_all_tags( $post_obj->post_content ), 22, '...' ),
            'content' => apply_filters( 'the_content', $post_obj->post_content ),
          ];
      ?>
          <div class="mld-notify-item">
            <span class="dashicons dashicons-megaphone"></span>
            <!-- <div>
              <strong><?php echo esc_html( get_the_title( $post_obj ) ); ?></strong>
              <small><?php echo esc_html( sprintf( __( '%s ago', 'myrtle-learning-dashboard' ), $time_ago ) ); ?></small>
            </div> -->
            <div class="mld-notification-read-more">
              <a href="#" data-parent='<?php echo esc_attr( wp_json_encode( $payload ) ); ?>'>
                <strong><?php echo esc_html( get_the_title( $post_obj ) ); ?></strong>
                <small><?php echo esc_html( sprintf( __( '%s ago', 'myrtle-learning-dashboard' ), $time_ago ) ); ?></small>
              </a>
            </div>
          </div>
      <?php
        endforeach;
      else :
      ?>
        <div class="mld-notify-item">
          <span class="dashicons dashicons-megaphone"></span>
          <div>
            <strong><?php echo esc_html__( 'No other notifications', 'myrtle-learning-dashboard' ); ?></strong>
            <small><?php echo esc_html__( 'Just Now', 'myrtle-learning-dashboard' ); ?></small>
          </div>
        </div>
      <?php endif; ?>
    </div>

    <div class="mld-notify-foot">
      <a href="JAVASCRIPT:;" id="mld-back" data-paged="" data-type="back">
        <span class="dashicons dashicons-arrow-left-alt2 mld-pagination-arrow"></span>
      </a>
      <a href="JAVASCRIPT:;" data-paged="2" data-type="next" id="mld-next">
        <span class="dashicons dashicons-arrow-right-alt2 mld-pagination-arrow"></span>
      </a>
    </div>

  </div>
</div>

</div>