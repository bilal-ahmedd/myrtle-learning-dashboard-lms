<?php
/**
 * Notification templates
 */

if( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class Myrtle_Notification_Template
 */
class Myrtle_Notification_Template {

	/**
	 * @var self
	 */
	private static $instance = null;

	/**
	 * @var user_data
	 */
	private $user_data = [];

	/**
	 * @var type
	 */
	private $type = 'section';

	/**
	 * @var category
	 */
	private $category = 'student';

	/**
	 * @var limit
	 */
	private $limit = '1';

	/**
	 * @var offset
	 */
	private $offset = 0;

	/**
	 * @var return
	 */
	private $pagination = false;

	/**
	 * @var return
	 */
	private $return = '';

	/**
	 * @since 1.0
	 * @return $this
	 */
	public static function instance() {

		if ( is_null( self::$instance ) && ! ( self::$instance instanceof Myrtle_Notification_Template ) ) {
			self::$instance = new self;
			self::instance()->hooks();
		}

		return self::$instance;
	}

	/**
	 * Call hooks
	 *
	 * @return void
	 */
	public function hooks() {

		add_action( 'wp_ajax_paged_notification', [ $this, 'mld_paged_notification' ] );
		add_action( 'exms_add_content_on_dashboard', [ $this, 'mld_add_custom_sections_on_dashboard' ] );
		add_filter( 'exms_dashboard_tabs', [ $this, 'mld_notification_tab' ] );
		add_action( 'exms_dashboard_tab_content_mld_my_notification', [ $this, 'render_notification_tab_content' ] );
	}

	public function mld_notification_tab( $tabs ) {

		$tabs['mld_my_notification'] = array(
			'label' => __( 'Notification', 'myrtle-learning-dashboard' ),
			'icon'  => 'dashicons-bell',
		);
		return $tabs;
	}

	public function render_notification_tab_content() {

		$user_id   = get_current_user_id();
		$userdata  = get_userdata( $user_id );

		$category = 'student';
		if ( $userdata && in_array( 'exms_group_leader', (array) $userdata->roles, true ) ) {
			$category = 'teacher';
		}
		if ( $userdata && in_array( 'administrator', (array) $userdata->roles, true ) ) {
			$category = 'admin';
		}

		$notifications = $this->get_notfications( 4, $category );
		if ( file_exists( MLD_TEMPLATES_DIR . 'mld-student-notification-template.php' ) ) {
			require MLD_TEMPLATES_DIR . 'mld-student-notification-template.php';
		}
	}


	/**
	 * add custom section on the dashboard
	 */
	public function mld_add_custom_sections_on_dashboard() {
		echo do_shortcode( '[notification_module type="section"]' );
	}	

	/**
	 * Return paginated data
	 *
	 * @return void
	 */
	public function mld_paged_notification() {

		$response = [];
		
		if( ! wp_verify_nonce( $_POST['mld_nounce'], 'mld_ajax_nonce' ) ) {

			$response['content'] = __( 'nonce error', 'myrtle-learning-dashboard' );
			$response['status'] = 'false';

			echo json_encode( $response );
			wp_die();
		}

		$paged = ( isset( $_POST['paged'] ) && !empty( $_POST['paged'] ) ) ? $_POST['paged'] : '';
		
		if( empty( $paged ) ) {

			$response['content'] = __( 'paged not found', 'myrtle-learning-dashboard' );
			$response['status'] = 'false';

			echo json_encode( $response );
			wp_die();
		}

		if( ! is_user_logged_in() ) {

			$response['content'] = __( 'user not logged in', 'myrtle-learning-dashboard' );
			$response['status'] = 'false';

			echo json_encode( $response );
			wp_die();
		}

		$this->pagination = true;
		$this->user_data = get_userdata( get_current_user_id() );
		$this->offset = $paged;
		$this->get_page();

		if( 'post not found' == $this->return ) {

			$response['content'] = $this->return;
			$response['status'] = false;
			echo json_encode( $response );
			wp_die();
		}

		$response['content'] = $this->return;
		$response['status'] = true;
		echo json_encode( $response );
		wp_die();
	}

	/**
	 * Return notifications
	 *
	 * @param $userdata
	 * @param $type
	 *
	 * @return return|string
	 */
	public static function get_notification_template( $userdata, $type ) {

		self::instance()->user_data = $userdata;
		self::instance()->type = $type;

		if( in_array( 'exms_group_leader', self::instance()->user_data->roles ) ) {
			self::instance()->category = 'teacher';
		}

        if( in_array( 'administrator', self::instance()->user_data->roles ) ) {
			self::instance()->category = 'admin';
		}

		call_user_func( [ self::$instance, 'get_' . $type ] );
		return self::instance()->return;
	}

	/**
	 * Return section notifications
	 *
	 * @return false|void
	 */
	private function get_section() {

		$notifications = $this->get_notfications( 3, $this->category );

		ob_start();
		?>
		<div class="mld-std-notification-section mld-std-main-section">
			<div class="mld-std-child-section-header mld-std-notification-section-header">
				<div class="mld-std-notification-child-section">
					<h4> <?php echo __('Myrtle Learning Notifications', 'myrtle-learning-dashboard'); ?> </h4>
					<a href="#"> <?php echo __('View all', 'myrtle-learning-dashboard'); ?></a>
				</div>
				<div class="mld-std-notification-child-section">
					<span class="dashicons dashicons-bell"></span>
				</div>
			</div>
			<?php
			if( $notifications->have_posts() ) {
				while ( $notifications->have_posts() ) {

					$notifications->the_post();
					?>
					<div class="mld-std-child-section-content">
						<div class="mld-std-notification-child-section-content mld-std-notification-child-section-content-icon">
							<span class="dashicons dashicons-rss"></span>
						</div>
						<div class="mld-std-notification-child-section-content">
							<p class="mld-std-notifications">
								<?php echo get_the_title() ?> 
							</p>
							<p>
								<?php echo __('Just Now', 'myrtle-learning-dashboard'); ?>
							</p>
						</div>
					</div>
					<?php
				}
			} else {
				?>
				<div class="mld-no-notification-found-wrapper">
					<img class="mld-no-notification-found" src="<?php echo MLD_ASSETS_URL.'images/no-notification-found.png' ?>">
				</div>
				<?php
			} 
			?>
		</div>
		<?php
        wp_reset_postdata();
		$content = ob_get_contents();
		ob_get_clean();

		self::instance()->return = $content;
	}

	/**
     * Return Active Notification
     *
	 * @return false|void
	 */
    private function get_active_notification() {

	    self::instance()->limit = 1;
	    $notifications = $this->get_notfications( $this->limit, $this->category, $this->offset );
	    if( $this->pagination && ! $notifications->have_posts() ) {
		    self::instance()->return = 'post not found';
		    return false;
	    }

	    if( $notifications->have_posts() ) {
		    ?>
            <div class="mld-selected-notification" id="mld-current-notification">
		    <?php
		    while( $notifications->have_posts() ) {
			    $notifications->the_post();
			    ?>
                <div class="mld-page-content-wrapper">
                    <div class="mld-page-header">
                        <img src="<?php echo get_the_post_thumbnail_url( get_the_ID() ); ?>">
                    </div>
                    <div class="mld-page-title">
					    <?php echo strtoupper( get_the_title() ); ?>
                    </div>
                    <div class="mld-page-content">
					    <?php the_content(); ?>
                    </div>
                </div>
			    <?php
			    break;
		    }
		    ?>
            </div><?php
	    }
	    wp_reset_postdata();
    }

	/**
	 * Return page notifications
	 *
	 * @return false|void
	 */
	private function get_page() {

		if( ! in_array( 'exms_student', self::instance()->user_data->roles ) &&
			! in_array( 'subscriber', self::instance()->user_data->roles ) &&
			! in_array( 'exms_group_leader', self::instance()->user_data->roles ) &&
			! in_array( 'administrator', self::instance()->user_data->roles ) ) {
			return false;
		}

		if( ! $this->pagination ) {
			self::instance()->get_active_notification();
		}

		self::instance()->limit = 3;
		$notifications_paged = $this->get_notfications( $this->limit, $this->category, $this->offset );

		if( $this->pagination && ! $notifications_paged->have_posts() ) {
			self::instance()->return = 'post not found';
			return false;
		}

		ob_start();

		if( $notifications_paged->have_posts() ) {

			$items  = $notifications_paged->posts;
			$others = array_slice( $items, 1 );

			?>
			<div class="mld-notify-card">

				<div class="mld-notify-head">
				<span class="mld-notify-heading"><?php _e( 'Other Notifications', 'myrtle-learning-dashboard') ?></span>
				<span class="dashicons dashicons-bell mld-notify-bell-icon"></span>
				</div>

				<div class="mld-notify-list">
					<?php
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
			<div class="mld-clear-both"></div>
			<?php
		}

		wp_reset_postdata();
		$content = ob_get_contents();
		ob_get_clean();

		self::instance()->return = $content;
	}


	/**
	 * Return notifications
	 *
	 * @param $limit
	 * @param $category
	 *
	 * @return WP_Query
	 */
	private function get_notfications( $limit = 5, $category = 'student', $offset = 0, $count = ''  ) {

		$args = [
			'post_type'     => 'mld_notifications',
			'post_status'   => 'publish',
			'order'         => 'DESC',
			'order_by'      => 'ID'
		];

        if( 'admin' != $category ) {
	        $args['tax_query'] = [
		        'relation' => 'OR',
		        [ 'taxonomy' => 'mld-notifications_categories', 'field' => 'slug', 'terms' => [ 'both', $category ] ]
	        ];
        }

		if( 'count' == $count || $limit < 1 ) {
			$args['posts_per_page'] = -1;
		}

		if( $limit > 1 || empty( $count ) ) {
			$args['posts_per_page'] = $limit;
			$args['paged'] = $offset;
		}

		$notifications =  new WP_Query( $args );

		if( 'count' == $count ) {
			return $notifications->found_posts;
		}
		return $notifications;
	}

	/**
	 * Return notification count
	 *
	 * @param $limit
	 * @param $category
	 *
	 * @return WP_Query
	 */
	private function get_notification_count( $limit, $category ) {

		return $this->get_notfications( $limit, $category, 0,'count' );
	}
}

Myrtle_Notification_Template::instance();