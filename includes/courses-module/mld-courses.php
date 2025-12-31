<?php
/**
 * Myrtle Learning - Admin Hooks
 *
 */
if( ! defined( 'ABSPATH' ) ) exit;

class MLD_COURSE_MODULE {

	private static $instance;
	private $userid;

	/**
	 * Create class instance
	 */
	public static function instance() {

		if( is_null( self::$instance ) && ! ( self::$instance instanceof MLD_COURSE_MODULE ) ) {

			self::$instance = new MLD_COURSE_MODULE;

			self::$instance->userid = get_current_user_id();
			self::$instance->hooks();
		}

		return self::$instance;
	}

	/**
	 * Define hooks
	 */
	private function hooks() {
		add_shortcode( 'mld_courses', [ $this, 'mld_course_list' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'mld_courses_scripts' ] );
		add_action( 'wp_ajax_added_featured_image', [ $this, 'mld_added_featured_image' ] );
		add_action( 'wp_ajax_append_group_users', [ $this, 'mld_append_group_users' ] );
		add_action( 'wp_ajax_display_course_shortcode', [ $this, 'mld_display_course_shortcode' ] );
	}

	/**
	 * display user courses shortcode
	 */
	public function mld_display_course_shortcode() {

		$response = [];
		$current_user_id = self::$instance->userid;
		$user_id = isset( $_POST['user_id'] ) ? $_POST['user_id'] : 0;

		if( ! $user_id ) {

			$response['message'] = __( 'user id not found', 'myrtle-learning-dashboard' );
			$response['status'] = 'false';

			echo json_encode( $response );
			wp_die();
		}

		update_user_meta( $current_user_id, 'mld_user_id', $user_id );
		$users_shortcode = do_shortcode( '[ld_profile user_id="'.$user_id.'" per_page="5" show_header="no"]' );

		$response['content'] = $users_shortcode;
		$response['status'] = 'true';

		echo json_encode( $response );
		wp_die();
	}

	/**
	 * get group users
	 */
	public function mld_append_group_users() {

		$response = [];

		$group_id = isset( $_POST['group_id'] ) ? $_POST['group_id'] : 0;

		if( ! $group_id ) {

			$response['message'] = __( 'group id not found', 'myrtle-learning-dashboard' );
			$response['status'] = 'false';

			echo json_encode( $response );
			wp_die();
		}

		$user_ids = mld_get_group_users( $group_id );

		ob_start();

		if( $user_ids && is_array( $user_ids ) ) {
			?>
			<option value=""><?php echo __( 'Select a user', 'myrtle-learning-dashboard' ); ?></option>
			<?php
			foreach( $user_ids as $user_id ) {
				?>
				<option value="<?php echo $user_id; ?>"><?php echo mld_get_username( $user_id ); ?></option>
				<?php
			}
		}

		$content = ob_get_contents();
		ob_get_clean();

		$response['content'] = $content;
		$response['status'] = 'true';

		echo json_encode( $response );
		wp_die();
	}

	/**
	 * Added featured iumage
	 */
	public function mld_added_featured_image() {

		$course_id = isset( $_POST['course_id'] ) ? $_POST['course_id'] : 0;

		$response = [];

		if( ! $course_id ) {

			$response['message'] = __( 'course id not found', 'myrtle-learning-dashboard' );
			$response['status'] = 'false';

			echo json_encode( $response );
			wp_die();
		}

		$thumbnail_url = get_the_post_thumbnail_url( $course_id );

		ob_start();
		?>
		<img src="<?php echo $thumbnail_url ?>" class="mld-course-featured-image">
		<?php
		$content = ob_get_contents();
		ob_get_clean();

		$response['content'] = $content;
		$response['status'] = 'true';

		echo json_encode( $response );
		wp_die();
	}

	/**
	 * Added css/jquery for course page
	 */
	public function mld_courses_scripts() {

		$rand = rand( 1000000, 1000000000 );
		
		if( 'mld-courses' == FRONT_PAGE || 'dashboard' == FRONT_PAGE ) {
			wp_enqueue_style( 'course-css', MLD_ASSETS_URL .'css/course.css', '', $rand, false );
		}

		wp_enqueue_script( 'course-js', MLD_ASSETS_URL . 'js/course.js', [ 'jquery' ], $rand, true );
		wp_localize_script( 'course-js', 'MLD', [
			'ajaxURL'       => admin_url( 'admin-ajax.php' ),
			'security'      => wp_create_nonce( 'mld_ajax_nonce' )
		] );
	}

	/**
	 * create a shortcode to display the courses list of a student
	 */
	public function mld_course_list() {

		if( ! is_user_logged_in() ) {
			return;
		}

		$user_id = self::$instance->userid;

		if( learndash_is_group_leader_user( $user_id ) == true || current_user_can( 'manage_options' ) ) {

			if( current_user_can( 'manage_options' ) ) {
				$group_ids = mld_get_groups_for_admin();
			} else {
				$group_ids = mld_get_groups_for_leader( $user_id );
			}

			if( $group_ids && is_array( $group_ids ) ) {

				ob_start();
				?>
				<div class="mld-courses-list-wrap">
					<div class="mld-courses-dropdown-wrapper">
						<div class="mld-courses-group-dropdown">
							<div class="mld-courses-group-title">
								<?php echo __( 'Select Group *', 'myrtle-learning-dashboard' ); ?>
							</div>
							<div>
								<select class="mld-selected-group-id">
									<option value=''><?php echo __( 'Select a group', 'myrtle-learning-dashboard' ); ?></option>
									<?php
									foreach( $group_ids as $group_id ) {
										?>
										<option value="<?php echo $group_id; ?>"><?php echo get_the_title( $group_id ); ?></option>
										<?php
									}
									?>
								</select>
							</div>
						</div>
						<div class="mld-courses-user-dropdown">
							<div class="mld-courses-user-title">
								<?php echo __( 'Select a user *', 'myrtle-learning-dashboard' ); ?>
							</div>
							<div>
								<select class="mld-selected-user-id">
									<option value=""><?php echo __( 'Select a user', 'myrtle-learning-dashboard' ); ?></option>
								</select>
							</div>
						</div>
						<div class="mld_clear_both"></div>
					</div>
					<img src="<?php echo MLD_ASSETS_URL.'images/spinner.gif' ?>" class="mld-courses-loader">
					<div class="mld-user-courses-shortcode mld-courses-list-wrapper"></div>
				</div>
				<?php
				$content = ob_get_contents();
				ob_get_clean();
				return $content;
			}
		} else {
			ob_start();
			?>
			<div class="mld-courses-list-wrapper">
				<?php
				echo do_shortcode( '[ld_profile user_id="'.$user_id.'" per_page="5" show_header="no"]' );
				?>
			</div>
			<?php
			$content = ob_get_contents();
			ob_get_clean();
			return $content;
		}
	}
}

/**
 * Initialize MLD_COURSE_MODULE
 */
MLD_COURSE_MODULE::instance();