<?php
/**
 * Notification templates
 */

if( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class Myrtle_Report_Template
 */
class Myrtle_Report_Template {

	/**
	 * @var self
	 */
	private static $instance = null;
	private $userid;

	/**
	 * @since 1.0
	 * @return $this
	 */
	public static function instance() {

		if ( is_null( self::$instance ) && ! ( self::$instance instanceof Myrtle_Report_Template ) ) {
			self::$instance = new self;

			self::$instance->userid = get_current_user_id();
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
		add_action( 'wp_ajax_get_group_courses', [ $this, 'mld_get_group_courses' ] );
		add_action( 'wp_ajax_get_user_table', [ $this, 'mld_get_user_table' ] );
		add_action( 'wp_ajax_get_user_detail', [ $this, 'mld_get_user_detail' ] );
		add_action( 'wp_ajax_update_user_comment', [ $this, 'mld_update_user_comment' ] );
		add_shortcode( 'report_section', [ $this, 'mld_report_section' ] );
		add_action( 'wp_ajax_get_user_group_chart', [ $this, 'mld_get_user_group_chart' ] );
		add_action( 'wp_ajax_get_user_course_detail', [ $this, 'mld_get_user_course_detail' ] );
		add_action( 'wp_ajax_update_ld_assignments', [ $this, 'mld_update_ld_assignments' ] );
		add_action( 'wp_ajax_get_comments', [ $this, 'mld_get_comments' ] );
		add_action( 'wp_ajax_update_comments', [ $this, 'mld_update_comments' ] );
		add_action( 'wp_ajax_approved_ld_assignments', [ $this, 'mld_approved_ld_assignments' ] );
		add_action( 'wp_ajax_update_assignment_points', [ $this, 'mld_update_assignment_points' ] );
		add_filter( 'mld_filter_user', [ $this, 'mld_filter_user_func' ] );
	}

	/**
	 * update user
	 */
	public function mld_filter_user_func( $user_id ) {

		$get_uni_user = get_user_meta( $user_id, 'mld_user_id', true );
		if( $get_uni_user && ! empty( $get_uni_user ) ) {
			return intval( $get_uni_user );
		}
		return $user_id;
	}

	/**
	 * update assignment points
	 */
	public function mld_update_assignment_points() {

		$response = [];

		if( ! wp_verify_nonce( $_POST['mld_nounce'], 'mld_ajax_nonce' ) ) {

			$response['message'] = __( 'data not found', 'myrtle-learning-dashboard' );
			$response['status'] = 'false';

			echo json_encode( $response );
			wp_die();
		}

		$assignment_id = isset( $_POST['post_id'] ) ? $_POST['post_id'] : 0;
		$points = isset( $_POST['point'] ) ? $_POST['point'] : 0;
		$prev_points = isset( $_POST['prev_points'] ) ? $_POST['prev_points'] : 0;

		$lesson_id = get_post_meta( $assignment_id, 'lesson_id', true );
		$post_type = get_post_type( $lesson_id );

		if( 'sfwd-lessons' == $post_type ) {
			$key = '_sfwd-lessons';
			$p_key = 'sfwd-lessons_';
		} else {
			$key = '_sfwd-topic';
			$p_key = 'sfwd-topic_';
		}
		$point_key = $p_key.'lesson_assignment_points_enabled';
		$max_points = get_post_meta( $lesson_id, $key, true );
		$is_point_enabled = isset( $max_points[$point_key] ) ? $max_points[$point_key] : '';

		if( 'on' != $is_point_enabled ) {

			$response['message'] = __( 'Points not enabled', 'myrtle-learning-dashboard' );
			$response['status'] = 'false';

			echo json_encode( $response );
			wp_die();
		}

		$point_amount_key = $p_key.'lesson_assignment_points_amount';
		$point_amount = isset( $max_points[$point_amount_key] ) ? $max_points[$point_amount_key] : '';

		$awarded_points = get_post_meta( $assignment_id, 'points', true );
		$total_points = $awarded_points + $points;

		if( $total_points > $point_amount ) {

			$actual_point = $point_amount - $awarded_points;
			$response['message'] = __( 'Please enter number less than '.$actual_point.'', 'myrtle-learning-dashboard' );
			$response['status'] = 'false';

			echo json_encode( $response );
			wp_die();
		}

		update_post_meta( $assignment_id, 'points', $total_points );
		$response['status'] = 'true';
		echo json_encode( $response );
		wp_die();
	}

	/**
	 * Approved assignments
	 */
	public function mld_approved_ld_assignments() {

		$response = [];

		if( ! wp_verify_nonce( $_POST['mld_nounce'], 'mld_ajax_nonce' ) ) {

			$response['message'] = __( 'data not found', 'myrtle-learning-dashboard' );
			$response['status'] = 'false';

			echo json_encode( $response );
			wp_die();
		}

		$assignment_id = isset( $_POST['id'] ) ? $_POST['id'] : 0;

		if( empty( $assignment_id ) ) {

			$response['message'] = __( 'data not found', 'myrtle-learning-dashboard' );
			$response['status'] = 'false';

			echo json_encode( $response );
			wp_die();
		}

		update_post_meta( $assignment_id, 'approval_status', 1 );

		$response['status'] = 'true';
		echo json_encode( $response );
		wp_die();
	}

	/**
	 * update comments
	 */
	public function mld_update_comments() {

		$response = [];

		global $wpdb;

		if( ! wp_verify_nonce( $_POST['mld_nounce'], 'mld_ajax_nonce' ) ) {

			$response['message'] = __( 'data not found', 'myrtle-learning-dashboard' );
			$response['status'] = 'false';

			echo json_encode( $response );
			wp_die();
		}

		$assignment_id = isset( $_POST['post_id'] ) ? $_POST['post_id'] : 0;
		$comment = isset( $_POST['comment'] ) ? $_POST['comment'] : '';
		$user_id = self::$instance->userid;
		$user_name = mld_get_username( $user_id );

		if( empty( $assignment_id ) || empty( $comment ) ) {

			$response['message'] = __( 'data not found', 'myrtle-learning-dashboard' );
			$response['status'] = 'false';

			echo json_encode( $response );
			wp_die();
		}

		wp_insert_comment(
			array(
				'comment_post_ID'   	=> $assignment_id,
				'comment_author'		=> $user_name,
				'user_id'				=> $user_id,
				'comment_content'		=> $comment,
				'comment_author_IP'		=> $_SERVER['REMOTE_ADDR'],
				'comment_author_url'	=> site_url(),
				'comment_type'			=> 'comment',
				'comment_date'			=> date("Y-m-d h:i:s"),
				'comment_date_gmt'		=> date("Y-m-d h:i:s"),
				'comment_author_email'	=> mld_get_user_email( $user_id )
			)
		);

		$response['status'] = 'true';
		echo json_encode( $response );
		wp_die();
	}

	/**
	 * get assignment comments
	 */
	public function mld_get_comments() {

		$response = [];

		if( ! wp_verify_nonce( $_POST['mld_nounce'], 'mld_ajax_nonce' ) ) {

			$response['message'] = __( 'data not found', 'myrtle-learning-dashboard' );
			$response['status'] = 'false';

			echo json_encode( $response );
			wp_die();
		}

		$assignment_id = isset( $_POST['post_id'] ) ? $_POST['post_id'] : 0;

		if( empty( $assignment_id ) ) {

			$response['message'] = __( 'Assignment id not found', 'myrtle-learning-dashboard' );
			$response['status'] = 'false';

			$response['status'] = 'false';
			echo json_encode( $response );
			wp_die();
		}

		$comments = $this->mld_get_assignment_comments( $assignment_id );
		$logged_in_user = self::$instance->userid;

		ob_start();
		if( ! empty( $comments ) ) {
			foreach( $comments as $key => $comment ) {

				$prev_index = $key - 1;
				$prev_doer = isset( $comments[$prev_index]->user_id ) ? $comments[$prev_index]->user_id : '';
				$comment_author = $comment->user_id;
				$parent_class = ( $logged_in_user == $comment_author ) ? 'mld-sender-wrapper mld-sender-msg' : 'mld-reciever-wrapper mld-reciever-msg';
				$child_class = ( $logged_in_user == $comment_author ) ? 'mld-sender' : 'mld-reciever';
				?>
				<div class="mld-chat-msg-wrap">
					<?php
					if( $comment_author != $prev_doer && $logged_in_user != $comment_author ) {
						?>
						<div class="mld-message-user"><?php echo mld_get_username( $comment_author ); ?></div>
						<?php
					}
					?>
					<div class="<?php echo $parent_class; ?>">
						<div class="mld-chat-msg <?php echo $child_class;?>">
							<div class="mld-user-chat">
								<?php echo stripslashes( $comment->comment_content ); ?>
							</div>
						</div>
					</div>
					<div class="mld-clear-both"></div>
				</div>
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
	 * update ld assignments
	 */
	public function mld_update_ld_assignments() {

		$response = [];

		if( ! wp_verify_nonce( $_POST['mld_nounce'], 'mld_ajax_nonce' ) ) {

			$response['message'] = __( 'data not found', 'myrtle-learning-dashboard' );
			$response['status'] = 'false';

			echo json_encode( $response );
			wp_die();
		}

		$id = isset( $_POST['id'] ) ? $_POST['id'] : 0;

		if( empty( $id ) ) {

			$response['message'] = __( 'ID not found', 'myrtle-learning-dashboard' );
			$response['status'] = 'false';

			echo json_encode( $response );
			wp_die();
		}

		$lesson_id = get_post_meta( $id, 'lesson_id', true );
		$post_type = get_post_type( $lesson_id );

		if( 'sfwd-lessons' == $post_type ) {
			$key = '_sfwd-lessons';
			$p_key = 'sfwd-lessons_';
		} else {
			$key = '_sfwd-topic';
			$p_key = 'sfwd-topic_';
		}
		$point_key = $p_key.'lesson_assignment_points_enabled';
		$max_points = get_post_meta( $lesson_id, $key, true );
		$is_point_enabled = isset( $max_points[$point_key] ) ? $max_points[$point_key] : '';

		$point_amount_key = $p_key.'lesson_assignment_points_amount';
		$point_amount = isset( $max_points[$point_amount_key] ) ? $max_points[$point_amount_key] : '';

		$assignment_points = get_post_meta( $id, 'points', true );

		if( ! $assignment_points ) {
			$assignment_points = 0;
		}
		$point_awarded_text = '';
		if( 'on' == $is_point_enabled ) {
			$point_awarded_text = $assignment_points.' / '.$point_amount.' points awarded.';
		}
		$is_assignment_approved = get_post_meta( $id, 'approval_status', true );

		if( $is_assignment_approved ) {
			$aprove_btn = '<div class="mld-aproved">'.__( 'Approved', 'myrtle-learning-dashboard' ).'</div>';
		} else {
			$aprove_btn = '<div class="mld-not-approve-text">'.__( 'Not Approve', 'myrtle-learning-dashboard' ).'</div><div class="mld-aproved-comment-action" data-assignment_id="'.$id.'">'. __( 'Approve', 'myrtle-learning-dashboard' ).'</div>';
		}

		ob_start();
		?>
		<div class="mld-assignment-options-wrapper">
			<div class="mld-assignment-approv-wrap">
				<div class="mld-approve-title"><?php echo __( 'Status', 'myrtle-learning-dashboard' ); ?></div>
				<div class="mld-approve-content">
					<?php
					echo $aprove_btn;
					?>
				</div>
				<div class="mld-clear-both"></div>
			</div>
			<div class="mld-assignment-point-wrap">
				<div class="mld-point-title"><?php echo __( 'Award Points', 'myrtle-learning-dashboard' ); ?></div>
				<div class="mld-point-content">
					<input class="mld-assignment-point" type="number" placeholder="<?php echo __( 'Points', 'myrtle-learning-dashboard' ); ?>">
					<button class="update_assignment_point" data-assignment_id="<?php echo $id; ?>" data-total_assign_points="<?php echo $assignment_points; ?>"><?php echo __( 'Award', 'myrtle-learning-dashboard' ); ?></button>
					<div class="mld-awarded-points-class mld-awarded-points-class-<?php echo $id; ?>"><?php echo $point_awarded_text; ?></div>
					<div class="mld-point-error-message">error message</div>
				</div>
				<div class="mld-clear-both"></div>
				<input type="hidden" value="<?php echo $is_assignment_approved; ?>" class="mld-is-assignment-approved">
			</div>
		</div>
		<?php
		$content = ob_get_contents();
		ob_get_clean();
		$response['content'] = $content;
		$response['status'] = 'true';
		echo json_encode( $response );
		wp_die();
	}

	/**
	 * get user course detail
	 */
	public function mld_get_user_course_detail() {

		$response = [];

		if( ! wp_verify_nonce( $_POST['mld_nounce'], 'mld_ajax_nonce' ) ) {

			$response['message'] = __( 'data not found', 'myrtle-learning-dashboard' );
			$response['status'] = 'false';

			echo json_encode( $response );
			wp_die();
		}

		$user_id = self::$instance->userid;
		$group_id = isset( $_POST['group_id'] ) ? $_POST['group_id'] : 0;
		$course_id = isset( $_POST['course_id'] ) ? $_POST['course_id'] : 0;
		$start_date = isset( $_POST['start_date'] ) ? strtotime( $_POST['start_date'] ) : 0;
		$end_date = isset( $_POST['end_date'] ) ? strtotime( $_POST['end_date'] ) : 0;
		$mld_academic_comments = [ 'Review topic before lesson', 'Complete classwork-High Standard', 'Complete Homework-High Standard', 'Complete Corrections-Uploaded' ];
		$mld_behaviour_comments = [ 'Excellent attendance', 'Ready and on time for each lesson', 'Fully engaged and focused in lessons', 'Complete all tasks' ];
		$academic_comments = get_user_meta( $user_id, 'mld_academic_comment_'.$group_id.'_'.$course_id, true );
		$behavior_comments = get_user_meta( $user_id, 'mld_behavior_comment_'.$group_id.'_'.$course_id, true );
		$approved_behaviour_comment = get_user_meta( $user_id, 'mld_approved_behavior_comment_'.$group_id.'_'.$course_id, true );
		$approved_academic_comment = get_user_meta( $user_id, 'mld_approved_academic_comment_'.$group_id.'_'.$course_id, true );

		if( empty( $academic_comments ) ) {
			$academic_comments = [];
		}

		if( empty( $behavior_comments ) ) {
			$behavior_comments = [];
		}

		if( empty( $group_id ) ) {

			$response['message'] = __( 'group id not found', 'myrtle-learning-dashboard' );
			$response['status'] = 'false';

			echo json_encode( $response );
			wp_die();
		}

		$courses = mld_get_group_courses( $group_id );
		$user_course_progress = [];
		$course_target_score = [];
		$course_cohort_score = [];
		$course_title = [];
		$user_score_average = round( $this->get_user_group_courses_progress( $user_id, $group_id, $start_date, $end_date ) );
		$group_coh_score = round( $this->get_cohot_average( $group_id, $start_date, $end_date ) );
		$group_tar_score = round( $this->get_group_courses_quiz_percentage( $group_id ) );
		if( $courses && is_array( $courses ) ) {
			foreach( $courses as $course ) {

				$course_title[] = substr( get_the_title( $course ), 0, 10);
				$user_course_progress[] = self::get_course_progress( $user_id, $course, $start_date, $end_date );
				$course_target_score[] = str_replace( '%', '', self::get_course_target_score( $course ) );
				$course_cohort_score[] = $this->get_course_user_cohort_average( $group_id, $course, $start_date, $end_date );
			}
		}

		ob_start();
		?>
		<div class="mld-header-inner-wrapper">
			<div class="mld-back-user-report">
				<span class="dashicons dashicons-arrow-left-alt"></span>
				<span class="mld-go-back"><?php echo __( 'Go Back', 'myrtle-learning-dashboard' ); ?></span>
			</div>
			<div class="mld-detail-grapg-inner">
				<div class="mld-page-title mld-column">
					<b><?php echo __( 'My Report', 'myrtle-learning-dashboard' ); ?></b>
				</div>
				<br></br>
				<div class="mld-assignment-button-wrapper">
					<div class="mld-courses-count mld-column">
						<div class="dashicons dashicons-book-alt mld-course-count">
						</div>
						<div><?php echo count( $courses ); ?></div>
					</div>
					<div class="mld-group-name mld-column">
						<?php echo get_the_title( $group_id ); ?>
					</div>
					<div class="mld-report mld-column">
						<a href="<?php echo get_permalink().'?group_id='.$group_id.'&user_id='.$user_id.'&course_id='.$course_id.'&mld_start_date='.$start_date.'&mld_end_date='.$end_date.''; ?>" target="_blank"><?php echo __( 'Download Full Report' );  ?></a>
					</div>
				</div>
			</div>
		</div>
		<div class="mld-user-course-header">
			<div class="mld-user-detail-wrapper">
				<div class="mld-user-avatar">
					<?php echo get_avatar( $user_id, 150 ); ?>
				</div>
				<div class="mld-user-name">
					<?php
					echo ucwords( mld_get_username( $user_id ) );
					?>
				</div>
				<div class="mld-side-graph-wrapper">
					<div class="course-graph-wrapper">
						<div class="course-score-title"><?php echo __( 'AVG SCORE ', 'myrtle-learning-dashboard' ).round( $user_score_average ).'%'; ?></div>
						<div class="mld-average-wrap">
							<div class="course-ave-color">
								<div class="mld-course-progress-bar" style="width: <?php echo round( $user_score_average ).'%'; ?>; background-color: #39803e;"></div>
							</div>
						</div>
					</div>
					<div class="course-graph-wrapper">
						<div class="course-score-title"><?php echo __( 'TARGET SCORE ', 'myrtle-learning-dashboard' ).$group_tar_score.'%'; ?></div>
						<div class="mld-average-wrap">
							<div class="course-ave-color">
								<div class="mld-course-progress-bar" style="width: <?php echo $group_tar_score.'%'; ?>; background-color: #32584a;"></div>
							</div>
						</div>
					</div>
					<div class="course-graph-wrapper">
						<div class="course-score-title"><?php echo __( 'COHORT SCORE ', 'myrtle-learning-dashboard' ).$group_coh_score.'%'; ?></div>
						<div class="mld-average-wrap">
							<div class="course-ave-color">
								<div class="mld-course-progress-bar" style="width: <?php echo $group_coh_score.'%'; ?>; background-color: #fbb11b;"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="mld-user-comments">
				<div>
					<h3 class="mld-comment-title"><?php echo __( 'Comments', 'myrtle-learning-dashboard' ); ?></h3>
					<div class="mld-both-comments-wrapper">
						<h5 class="mld-comment-title mld-comment-title-alignment"><?php echo __( 'Academic Comments', 'myrtle-learning-dashboard' ); ?></h5>
						<div>
							<span><?php echo __( 'What went well', 'myrtle-learning-dashboard' ); ?></span>
							<?php
							if( $academic_comments && is_array( $academic_comments ) ) {
								foreach( $academic_comments as $academic_comment ) {
									?>
									<div><?php echo '*' .$academic_comment; ?></div>
									<?php
								}
							}
							?>
						</div>
						<div>
							<span><?php echo __( 'Even Better If', 'myrtle-learning-dashboard' ); ?></span>
							<?php

							$unique_academic_comments = array_diff( $mld_academic_comments, $academic_comments );
							if( $approved_academic_comment && is_array( $approved_academic_comment ) ) {
								$unique_academic_comments = $approved_academic_comment;
							}
							if( $unique_academic_comments && is_array( $unique_academic_comments ) ) {
								foreach( $unique_academic_comments as $key => $unique_academic_comment ) {
									?>
									<div class="mld-remaining-comments"><?php echo '*' .$unique_academic_comment; ?></div>
									<?php
								}
							}
							?>
						</div>
						<h5 class="mld-comment-title mld-comment-title-alignment"><?php echo __( 'Behaviour Comments', 'myrtle-learning-dashboard' ); ?></h5>
						<div>
							<span><?php echo __( 'What went well', 'myrtle-learning-dashboard' ); ?></span>
							<?php
							if( $behavior_comments && is_array( $behavior_comments ) ) {
								foreach( $behavior_comments as $behavior_comment ) {
									?>
									<div><?php echo '*' .$behavior_comment; ?></div>
									<?php
								}
							}
							?>
						</div>
						<div>
							<span><?php echo __( 'Even Better If', 'myrtle-learning-dashboard' ); ?></span>
							<?php
							$unique_behavior_comments = array_diff( $mld_behaviour_comments, $behavior_comments );
							if( $approved_behaviour_comment && is_array( $approved_behaviour_comment ) ) {
								$unique_behavior_comments = $approved_behaviour_comment;
							}
							if( $unique_behavior_comments && is_array( $unique_behavior_comments ) ) {
								foreach( $unique_behavior_comments as $unique_behavior_comment ) {
									?>
									<div class="mld-remaining-comments"><?php echo '*' .$unique_behavior_comment; ?></div>
									<?php
								}
							}
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="mld-user-course-detail-graph">
			<div class="mld-user-graph-container-wrapper">
				<div id="mld-user-graph-container">
					<div class="mld-user-results-title">
						<?php echo mld_get_username( $user_id ).' '.__( 'Results Analysis', 'myrtle-learning-dashboard' ); ?>
					</div>
					<canvas id="canvas"></canvas>
				</div>
			</div>
			<div class="mld-average-wrapper">
				<div class="mld-user-student-average-box">
					<div class="mld-user-average-title">
						<?php echo mld_get_username( $user_id ).' '.__( 'Average Score %', 'myrtle-learning-dashboard' ); ?>
					</div>
					<div class="mld-student-average-box">
						<div class="mld-student-average-wrapper"></div>
					</div>
				</div>

				<div class="mld-user-student-cohort-box">
					<div class="mld-user-cohort-average-title">
							<?php echo __( 'Cohort Average Score %', 'myrtle-learning-dashboard' ); ?>
					</div>
					<div class="mld-user-cohort-box">
						<div class="mld-cohort-average-wrapper"></div>
					</div>
				</div>
				<div class="mld-clear-both"></div>
			</div>
			<div class="mld-user-courses">
				<?php
				echo do_shortcode( '[ld_profile]' );
				?>
			</div>
		</div>
		<div class="mld-clear-both"></div>
		<?php
		$content = ob_get_contents();
		ob_get_clean();
		$response['content'] = $content;
		$response['user_course_progress'] = $user_course_progress;
		$response['course_target_progress'] = $course_target_score;
		$response['course_cohort_progress'] = $course_cohort_score;
		$response['titles'] = $course_title;
		$response['user_group_score_average'] = $user_score_average;
		$response['user_cohort_average'] = $this->get_cohot_average( $group_id, $start_date, $end_date );
		$response['group_target_average'] = $this->get_group_courses_quiz_percentage( $group_id );
		$response['status'] = 'true';
		$response['status'] = 'true';
		echo json_encode( $response );
		wp_die();
	}

	/**
	 * display user group guage chart
	 */
	public function mld_get_user_group_chart() {

		$response = [];

		if( ! wp_verify_nonce( $_POST['mld_nounce'], 'mld_ajax_nonce' ) ) {

			$response['message'] = __( 'data not found', 'myrtle-learning-dashboard' );
			$response['status'] = 'false';

			echo json_encode( $response );
			wp_die();
		}

		$group_id = isset( $_POST['group_id'] ) ? $_POST['group_id'] : 0;
		$user_id = self::$instance->userid;

		if( ! $group_id || ! $user_id ) {

			$response['message'] = __( 'data not found', 'myrtle-learning-dashboard' );
			$response['status'] = 'false';

			echo json_encode( $response );
			wp_die();
		}

		$group_percentage = $this->get_user_group_courses_progress( $user_id, $group_id, 0, 0 );
		$group_target_percentage = $this->get_group_courses_quiz_percentage( $group_id );

		if( ! $group_percentage || NULL == $group_percentage ) {
			$group_percentage = 0;
		}

		$response['average'] = $group_percentage;
		$response['target']  = $group_target_percentage;
		$response['status']  = 'true';

		echo json_encode( $response );
		wp_die();
	}
	/**
	 * report section
	 */
	public function mld_report_section() {

		if( ! is_user_logged_in() ) {
			return;
		}

		$user_id = self::$instance->userid;

		ob_start();

		if( learndash_is_group_leader_user( $user_id ) == true || current_user_can( 'manage_options' ) ) {

			if( learndash_is_group_leader_user( $user_id ) == true ) {
				$group_ids = mld_get_groups_for_leader( $user_id );
			} elseif( current_user_can( 'manage_options' ) ) {
				$group_ids = mld_get_groups_for_admin();
			}

			$group_ids = array_slice( $group_ids, 0, 4 );

			if( $group_ids && is_array( $group_ids ) ) {
				?>
				<div class="mld-groups-wrapper">
					<?php
					foreach( $group_ids as $group_id ) {
						?>
						<div class="mld-groups" data-group_id="<?php echo $group_id; ?>" data-page_url="<?php echo site_url().'/dashboard/report'; ?>">
							<?php echo get_the_title( $group_id ); ?>
						</div>
						<?php
					}
					?>
					<div class="mld-report-view-more">
						<a href="<?php echo site_url().'/dashboard/reports';?>" target="_blank"><?php echo __( 'View more', 'myrtle-learning-dashboard' ); ?></a>
					</div>
					<div class="mld-clear-both"></div>
				</div>
				<?php
			}
		} else {
			$user_groups = mld_get_user_groups( $user_id );
			
			?>
			<div class="mld-groups-wrapper">
				<?php
				if( empty( $user_groups ) ) {
					?>
					<div><?php echo __( 'No group found', 'myrtle-learning-dashboard' ); ?></div>
					<?php
				} else {
					$user_group_id = isset( $user_groups[0] ) ? $user_groups[0] : 0;
					$user_group_percentage = $this->get_user_group_courses_progress( $user_id, $user_group_id, 0, 0 );
					$course_target_score = $this->get_group_courses_quiz_percentage( $user_group_id );
					if( ! $user_group_percentage || $user_group_percentage == NULL ) {
						$user_group_percentage = 0;
					}

					?>
					<div class="mld-group-select-box">
						<input type="hidden" value="<?php echo $user_group_percentage; ?>" class="mld-user-group-percentage">
						<input type="hidden" value="<?php echo round( $course_target_score ); ?>" class="mld-course-group-target">
						<select class="mld-user-guage-graph">
							<?php
							foreach( $user_groups as $user_group ) {
								?>
								<option value="<?php echo $user_group; ?>"><?php echo get_the_title( $user_group ); ?></option>
								<?php
							}
							 ?>
						</select>
					</div>
					<div class="mld-user-chart-title">
						<?php echo mld_get_username( $user_id ).' '.__( 'Average Score %', 'myrtle-learning-dashboard' ); ?>
					</div>
					<div id="mld-user-chart"></div>
					<div class="mld-user-all-report">
						<a href="<?php echo site_url().'/dashboard/report';?>" target="_blank"><?php echo __( 'Click to see', 'myrtle-learning-dashboard' ); ?></a>
					</div>
					<?php
				}
				?>
			</div>
			<?php
		}

		$content = ob_get_contents();
		ob_get_clean();
		return $content;
	}

	/**
	 * update commnets
	 */
	public function mld_update_user_comment() {

		$response = [];

		if( ! wp_verify_nonce( $_POST['mld_nounce'], 'mld_ajax_nonce' ) ) {

			$response['message'] = __( 'data not found', 'myrtle-learning-dashboard' );
			$response['status'] = 'false';

			echo json_encode( $response );
			wp_die();
		}

		$user_id = isset( $_POST['user_id'] ) ? $_POST['user_id'] : 0;
		$group_id = isset( $_POST['group_id'] ) ? $_POST['group_id'] : 0;
		$academic_comments = isset( $_POST['academic_comments'] ) ? $_POST['academic_comments'] : [];
		$behavior_comments = isset( $_POST['behavior_comment'] ) ? $_POST['behavior_comment'] : [];

		$course_id = isset( $_POST['course_id'] ) ? $_POST['course_id'] : 0;
		$approved_behaviour_comment = isset( $_POST['approved_behaviour'] ) ? $_POST['approved_behaviour'] : [];
		$approved_acedamic_comment = isset( $_POST['approved_academic'] ) ? $_POST['approved_academic'] : [];

		if( ! $user_id || ! $group_id || ! $course_id ) {

			$response['message'] = __( 'data not found', 'myrtle-learning-dashboard' );
			$response['status'] = 'false';

			echo json_encode( $response );
			wp_die();
		}

		update_user_meta( $user_id, 'mld_academic_comment_'.$group_id.'_'.$course_id, $academic_comments );
		update_user_meta( $user_id, 'mld_behavior_comment_'.$group_id.'_'.$course_id, $behavior_comments );
		update_user_meta( $user_id, 'mld_approved_behavior_comment_'.$group_id.'_'.$course_id, $approved_behaviour_comment );
		update_user_meta( $user_id, 'mld_approved_academic_comment_'.$group_id.'_'.$course_id, $approved_acedamic_comment );
		wp_die();
	}

	/**
	 * get user detail
	 */
	public function mld_get_user_detail() {

		$response = [];

		if( ! wp_verify_nonce( $_POST['mld_nounce'], 'mld_ajax_nonce' ) ) {

			$response['message'] = __( 'data not found', 'myrtle-learning-dashboard' );
			$response['status'] = 'false';

			echo json_encode( $response );
			wp_die();
		}

		$current_user_id = self::$instance->userid;
		
		$start_date = isset( $_POST['start_date'] ) ? strtotime( $_POST['start_date'] ) : 0;
		$end_date = isset( $_POST['end_date'] ) ? strtotime( $_POST['end_date'] ) : 0;
		$user_id = isset( $_POST['user_id'] ) ? intval( $_POST['user_id'] ) : 0;
		$group_id = isset( $_POST['group_id'] ) ? intval( $_POST['group_id'] ) : 0;
		$course_id = isset( $_POST['course_id'] ) ? intval( $_POST['course_id'] ) : 0;
		$target_score = $this->get_group_courses_quiz_percentage( $group_id );
		$cohort_score = $this->get_cohot_average( $group_id, $start_date, $end_date );
		$group_courses = mld_get_group_courses( $group_id );
		$ave_score = $this->get_user_group_courses_progress( $user_id, $group_id, $start_date, $end_date );
		$group_course_count = count( $group_courses );
		$user_assignments = $this->get_user_assignment( $group_id, $user_id, $start_date, $end_date );
		$academic_comments = get_user_meta( $user_id, 'mld_academic_comment_'.$group_id.'_'.$course_id, true );
		$behavior_comments = get_user_meta( $user_id, 'mld_behavior_comment_'.$group_id.'_'.$course_id, true );
		$mld_academic_comments = [ 'Review topic before lesson', 'Complete classwork-High Standard', 'Complete Homework-High Standard', 'Complete Corrections-Uploaded' ];
		$mld_behaviour_comments = [ 'Excellent attendance', 'Ready and on time for each lesson', 'Fully engaged and focused in lessons', 'Complete all tasks' ];
		$approved_behaviour = get_user_meta( $user_id, 'mld_approved_behavior_comment_'.$group_id.'_'.$course_id, true );
		$approved_academy = get_user_meta( $user_id, 'mld_approved_academic_comment_'.$group_id.'_'.$course_id, true );

		if( empty( $group_courses ) ) {

			$response['message'] = __( 'data not found', 'myrtle-learning-dashboard' );
			$response['status'] = 'false';

			echo json_encode( $response );
			wp_die();
		}

		if( empty( $academic_comments ) ) {
			$academic_comments = [];
		}

		if( empty( $behavior_comments ) ) {
			$behavior_comments = [];
		}

		update_user_meta( $current_user_id, 'mld_user_id', $user_id );
		ob_start();
		$course_titles = [];
		$course_user_cohort_average = [];
		$course_target_score = [];
		$group_courses_name = mld_get_group_courses( $group_id );
		$behaviour_text = '';
		$academic_text = '';
		$included_courses = '';
		?>
		<div class="mld-back-user-report mld-back-btn">
			<span class="dashicons dashicons-arrow-left-alt"></span>
			<span class="mld-go-back"> <?php echo __( 'Go Back', 'myrtle-learning-dashboard' ); ?> </span>
		</div>
		<input type="hidden" class="mld-assignment-count" value="<?php echo count( $user_assignments ); ?>">
		<div class="mld-report-container">
			<div class="mld-user-progress-wrap">
				<div class="mld-full-report">
					<button><?php echo __( 'Full report', 'myrtle-learning-dashboard' ); ?></button>
				</div>
				<div class="mld-full-report-pdf">
					<div class="mld-group-courses-wraper">
						<div class="mld-group-title">
							<span><?php echo ucwords( get_the_title( $group_id ) ); ?></span>
						</div>
						<div class="mld-group-courses">
							<span><?php echo __( 'Course(s)', 'myrtle-learning-dashboard' ); ?></span>
							<span><?php echo $group_course_count; ?></span>
						</div>
						<div class="clear-both"></div>
					</div>
					<div class="mld-group-course-progress">
			 			<?php
			 			foreach( $group_courses as $group_course ) {

			 				$check_user_access = sfwd_lms_has_access( $group_course, $user_id );

			 				if( false == $check_user_access ) {
			 					continue;
			 				}

			 				$mld_percentage = self::get_course_progress( $user_id, $group_course, $start_date, $end_date );
			 				$course_target = self::get_course_target_score( $group_course );
							$course_target = str_replace( '%','', $course_target );
							$course_target_score[] = $course_target;
			 				$course_user_cohort_average[] = $this->get_course_user_cohort_average( $group_id, $group_course, $start_date, $end_date );
							$course_titles[] = substr( get_the_title( $group_course ), 0, 10);
			 				?>
			 				<div class="mld-user-progress">
			 					<div class="mld-course-name"><?php echo get_the_title( $group_course ); ?></div>
			 					<div class="mld-course-percentage"><?php echo round( $mld_percentage ).'%'; ?></div>
			 					<div class="mld-clear-both"></div>
			 				</div>
			 				<?php
			 			}
			 			?>
			 		</div>
			 		<div class="mld-side-graph-wrapper">
			 			<div class="course-graph-wrapper">
			 				<div class="course-score-title"><?php echo __( 'AVG SCORE', 'myrtle-learning-dashboard' ); ?></div>
			 				<div class="mld-average-wrap">
			 					<div class="course-ave-color">
			 						<div class="mld-course-progress-bar" style="width: <?php echo round( $ave_score ).'%'; ?>; background-color: #39803e;"></div>
			 					</div>
			 					<div class="course-ave-score">
			 						<?php echo round( $ave_score ).'%'; ?>
			 					</div>
			 				</div>
			 			</div>
			 			<div class="course-graph-wrapper">
			 				<div class="course-score-title"><?php echo __( 'TARGET SCORE', 'myrtle-learning-dashboard' ); ?></div>
			 				<div class="mld-average-wrap">
			 					<div class="course-ave-color">
			 						<div class="mld-course-progress-bar" style="width: <?php echo round( $target_score ).'%'; ?>; background-color: #32584a;"></div>
			 					</div>
			 					<div class="course-ave-score"><?php echo round( $target_score ).'%' ?></div>
			 				</div>
			 			</div>
			 			<div class="course-graph-wrapper">
			 				<div class="course-score-title"><?php echo __( 'COHORT AVG SCORE', 'myrtle-learning-dashboard' ); ?></div>
			 				<div class="mld-average-wrap">
			 					<div class="course-ave-color">
			 						<div class="mld-course-progress-bar" style="width: <?php echo round( $cohort_score ).'%'; ?>; background-color: #fbb11b;"></div>
			 					</div>
			 					<div class="course-ave-score"><?php echo round( $cohort_score ).'%'; ?></div>
			 				</div>
			 			</div>
			 		</div>
				</div>
				<div class="mld-full-report-download">
					<button class="mld-full-report-btn"><?php echo __( 'Download Full Report', 'myrtle-learning-dashboard' ); ?></button>
					<!-- -->

					<div class="mld-pop-outer" style="display: none;">
						<div class="mld-pop-inner">
							<div class="report-popup-closed">
								<span class="dashicons dashicons-dismiss"></span>
							</div>
							<div class="mld-password-flield-wrapper">
								<div class="mld-group-courses-label">
									<label><?php echo __( 'Select a Course', 'myrtle-learning-dashboard' ); ?></label>
									<select class="mld-popup-course-dropdown" name="report-course-pop[]" multiple>
										<option value=""><?php echo __( 'Select a Course', 'myrtle-learning-dashboard' ); ?></option>
										<?php 
										if( ! empty( $group_courses_name ) && is_array( $group_courses_name ) ) {
											foreach( $group_courses_name as $group_courses ) {
												?>
												<option value="<?php echo $group_courses; ?>"><?php echo get_the_title( $group_courses ); ?></option>
												<?php
											}
										}
										?>
									</select>
								</div>
								<div class="mld-academic-check">
									<label>
										<?php echo __( 'Academic Comments', 'myrtle-learning-dashboard' ); ?>
									</label>
									<input type="checkbox" name="">
								</div>
								<div class="mld-behaviour-check">
									<label>
										<?php echo __( 'Behaviour Comments', 'myrtle-learning-dashboard' ); ?>
									</label>
									<input type="checkbox" name="">
								</div>
								<div class="mld-report-continue">
									<a href="<?php echo get_permalink().'?ave_score='.round( $ave_score ).'&target_score='.round( $target_score ).'&cohort_score='.round( $cohort_score ).'&group_id='.$group_id.'&user_id='.$user_id.'&course_id='.$course_id.'&mld_start_date='.$start_date.'&mld_end_date='.$end_date.'&mld_academic='.$academic_text.'&mld_behaviour='.$behaviour_text.'&mld_included_courses='.$included_courses.''; ?>" target="_blank"><?php echo __( 'Continue' ); ?></a>
								</div>
							</div>
						</div>
					</div>
					<!-- -->
				</div>
			</div>
			<div class="mld-progress-content-wrapper">
				<div class="mld-report-header">
					<div class="mld-avg-score">
						<div class="mld-average-indicator" style="background-color: #39803e;"></div>
						<div class="course-score-title">
							<?php echo mld_get_username( $user_id ).' '.__( 'SCORE', 'myrtle-learning-dashboard' ); ?></div>
					</div>
					<div class="mld-target-score">
						<div class="mld-average-indicator" style="background-color: #32584a;"></div>
						<div class="course-score-title"><?php echo __( 'TARGET SCORE', 'myrtle-learning-dashboard' ); ?></div>
					</div>
					<div class="mld-cohot-score">
						<div class="mld-average-indicator" style="background-color: #fbb11b;"></div>
						<div class="course-score-title"><?php echo __( 'COHORT SCORE', 'myrtle-learning-dashboard' ); ?></div>
					</div>
				</div>
				<div class="mld-graph-container-wrapper">
					<div id="mld-graph-container">
						<div class="mld-user-results-title">
							<?php echo mld_get_username( $user_id ).' '.__( 'Results Analysis', 'myrtle-learning-dashboard' ); ?>
						</div>
						<canvas id="canvas"></canvas>
					</div>
				</div>
			</div>
			<div class="mld-clear-both"></div>
		</div>

		<div class="mld-commnet-container">
			<div class="mld-comment-side">
				<div class="mld-report-comments">
					<button><?php echo __( 'Comments', 'myrtle-learning-dashboard' ); ?></button>
				</div>
				<div class="mld-comments-wrapper">
					<h5><?php echo __( 'Academic Comments', 'myrtle-learning-dashboard' ); ?></h5>
					<div class="mld-approved-academic-comment-section">
						<div class="custom-comment-wrapper">
							<div class="mld-went-wrap"><?php echo __( 'What went well', 'myrtle-learning-dashboard' ); ?></div>
							<?php
							if ( learndash_is_group_leader_user( $current_user_id ) === true || current_user_can( 'manage_options' ) ) {
								?>
								<div class="mld-add-academic-custom-comment"><?php echo __( 'Add Comment', 'myrtle-learning-dashboard' ); ?></div>
								<?php
							}
							?>
							<div class="mld-clear-both"></div>
						</div>
						<?php
						if( $academic_comments && is_array( $academic_comments ) ) {
							foreach( $academic_comments as $academic_comment ) {
								?>
								<div class="mld-comments mld-academic-comments">
									<i class="fa fa-trash mld-delete-comment" aria-hidden="true"></i>
									<div class="mld-comment-wrapper">
										<?php echo $academic_comment; ?>
									</div>
									<span class="dashicons dashicons-edit mld-comment-editable"></span>
									<div class="dashicons dashicons-no-alt mld-user-academic-comment mld-comment-wrapper-icon"></div>
									<div class="mld-clear-both"></div>
								</div>
								<?php
							}
						}
						?>
					</div>
					<div class="mld-academic-comment mld-comments-wrap">
						<span><?php echo __( 'Even Better If', 'myrtle-learning-dashboard' ); ?></span>
						<?php
						$unique_academic_comments = array_diff( $mld_academic_comments, $academic_comments );
						$no = 0;
						$hide_academic_class = '';

						if( $approved_academy && is_array( $approved_academy ) ) {
							$unique_academic_comments = $approved_academy;
						}

						if( $unique_academic_comments && is_array( $unique_academic_comments ) ) {

							$unique_academic_comments_count = count( $unique_academic_comments );

							if( $unique_academic_comments_count > 1 ) {
								$hide_academic_class = 'mld-hide-comments';
							}
							foreach( $unique_academic_comments as $key => $unique_academic_comment ) {
								$no++;
								if( 1 == $no && $unique_academic_comments_count > 1 ) {
									?>
									<div class="mld-comments mld-acade-class">
										<i class="fa fa-trash mld-delete-comment" aria-hidden="true"></i>
										<div class="mld-comment-wrapper"><?php echo $unique_academic_comment; ?></div>
										<span class="dashicons dashicons-edit mld-comment-editable"></span>
										<div class="dashicons dashicons-plus-alt2 mld-comment-wrapper-icon" data-comment="mld-plus-academic"></div>
										<div class="mld-clear-both"></div>
									</div>
									<?php
								} else {
									?>
									<div class="mld-comments mld-acade-class <?php echo $hide_academic_class; ?>">
										<i class="fa fa-trash mld-delete-comment" aria-hidden="true"></i>
										<div class="mld-comment-wrapper"><?php echo $unique_academic_comment; ?></div>
										<span class="dashicons dashicons-edit mld-comment-editable"></span>
										<div class="dashicons dashicons-yes mld-approved-academic-comment mld-comment-wrapper-icon"></div>
										<div class="mld-clear-both"></div>
									</div>
									<?php
								}
							}
						}
						?>
					</div>

					<h5><?php echo __( 'Behaviour Comments', 'myrtle-learning-dashboard' ); ?></h5>
					<div class="mld-approved-behavior-comments-section">
						<div class="custom-comment-wrapper">
							<div class="mld-went-wrap"><?php echo __( 'What went well', 'myrtle-learning-dashboard' ); ?></div>
							<?php
							if ( learndash_is_group_leader_user( $current_user_id ) === true || current_user_can( 'manage_options' ) ) {
								?>
								<div class="mld-add-behaviour-custom-comment"><?php echo __( 'Add Comment', 'myrtle-learning-dashboard' ); ?></div>
								<?php
							}
							?>
							<div class="mld-clear-both"></div>
						</div>
						<?php
						if( $behavior_comments && is_array( $behavior_comments ) ) {
							foreach( $behavior_comments as $behavior_comment ) {
								?>
								<div class="mld-comments mld-behavior-comments">
									<i class="fa fa-trash mld-delete-comment" aria-hidden="true"></i>
									<div class="mld-comment-wrapper"><?php echo $behavior_comment; ?></div>
									<span class="dashicons dashicons-edit mld-comment-editable"></span>
									<div class="dashicons dashicons-no-alt mld-user-behavior-comment mld-comment-wrapper-icon"></div>
									<div class="mld-clear-both"></div>
								</div>
								<?php
							}
						}
						?>
					</div>
					<div class="mld-behavior-comment mld-comments-wrap">
						<span><?php echo __( 'Even Better If', 'myrtle-learning-dashboard' ); ?></span>
						<?php
						$unique_behavior_comments = array_diff( $mld_behaviour_comments, $behavior_comments );
						$no = 0;
						$hide_class = '';

						if( $approved_behaviour && is_array( $approved_behaviour ) ) {
							$unique_behavior_comments = $approved_behaviour;
						}
						if( $unique_behavior_comments && is_array( $unique_behavior_comments ) ) {
							$unique_behavior_comments_count = count( $unique_behavior_comments );

							if( $unique_behavior_comments_count > 1 ) {
								$hide_class = 'mld-hide-comments';
							}
							foreach( $unique_behavior_comments as $unique_behavior_comment ) {
								$no++;

								if( 1 == $no && $unique_behavior_comments_count > 1 ) {
									?>
									<div class="mld-comments mld-behave-class">
										<i class="fa fa-trash mld-delete-comment" aria-hidden="true"></i>
										<div class="mld-comment-wrapper"><?php echo $unique_behavior_comment; ?></div>
										<span class="dashicons dashicons-edit mld-comment-editable"></span>
										<div class="dashicons dashicons-plus-alt2 mld-comment-wrapper-icon" data-comment="mld-plus-behaviour"></div>
										<div class="mld-clear-both"></div>
									</div>
									<?php
								} else {
									?>
									<div class="mld-comments mld-behave-class <?php echo $hide_class; ?>">
										<i class="fa fa-trash mld-delete-comment" aria-hidden="true"></i>
										<div class="mld-comment-wrapper"><?php echo $unique_behavior_comment; ?></div>
										<span class="dashicons dashicons-edit mld-comment-editable"></span>
										<div class="dashicons dashicons-yes mld-approved-behavior-comment mld-comment-wrapper-icon"></div>
										<div class="mld-clear-both"></div>
									</div>
									<?php
								}
							}
						}
						?>
					</div>
					<div class="mld-update-comments" data-user_id ="<?php echo $user_id; ?>" data-group_id="<?php echo $group_id; ?>">
						<?php echo __( 'Update', 'myrtle-learning-dashboard' ); ?>
						<img src="<?php echo MLD_ASSETS_URL.'images/spinner.gif' ?>" class="mld-comment-loader">
					</div>
				</div>
			</div>
			<div class="mld-assignment-wrap">
				<div class="mld-assignment-inner-content">
					<div class="uploaded-work-btn"><?php echo __( 'Uploaded Work', 'myrtle-learning-dashboard' ); ?></div>
					<div class="mld-assignment-table">
						<div class="mld-assignment-body">
						<div class="mld-assignment-wrapper"><?php echo __( 'Uploaded Work','myrtle-learning-dashboard' ); ?><span class="dashicons dashicons-insert mld-assignment-wrapper-icon"></span></div>
							<div class="mld-file-data-wrapper">
								<?php
								$no = 0;
								if( $user_assignments && is_array( $user_assignments ) ) {
									foreach( $user_assignments as $id => $assignment ) {

										$no++;
										$date = $assignment['date'];
										$title = $assignment['title'];
										$title_parts = explode( '_', $title );
										$title_text = end( $title_parts );

										if( 1 == $no ) {
										?>
										<div class="mld-assignment-main-wrapper">
											<div class="mld-assignment-content">
												<div class="mld-assignment-file">
													<div class="mld-content"><?php echo __( 'NAME', 'myrtle-learning-dashboard' ); ?></div>
												</div>
												<div class="mld-assignment-date">
													<div class="mld-content"><?php echo __( 'MODIFIED', 'myrtle-learning-dashboard' ); ?></div>
												</div>
												<div class="mld-assignment-grading">
													<div class="mld-content"><?php echo __( 'GRADING', 'myrtle-learning-dashboard' ); ?></div>
												</div>
												<div class="mld-assignment-grading-comment">
													<div class="mld-content"><?php echo __( 'COMMENTS', 'myrtle-learning-dashboard' ); ?></div>
												</div>
												<div class="mld-clear-both"></div>
											</div>
										</div>
										<?php
										}
										?>
										<div class="mld-assignment-main-wrapper">
											<div class="mld-assignment-content">
												<div class="mld-assignment-file">
													<div class="mld-content mld-file-name">
														<span class="dashicons dashicons-media-text"></span>
														<label><a href="<?php echo site_url().'/wp-content/uploads/assignments/'.$title ?>" download><?php echo substr( $title_text, 0, 12 ); ?></a></label>
													</div>
												</div>
												<div class="mld-assignment-date">
													<div class="mld-content mld-file-date">
														<span><?php echo date( 'jS F, Y', strtotime( $date ) ); ?></span>
													</div>
												</div>
												<div class="mld-assignment-grading">
													<div class="mld-content">
														<?php
														$is_asignment_approved = get_post_meta( $id, 'approval_status', true );

														if( $is_asignment_approved ) {
															$approve_text = __( 'Reviewed', 'myrtle-learning-dashboard' );
															$text_class = 'mld-approved-text-class';
														} else {
															$approve_text = __( 'Action', 'myrtle-learning-dashboard' );
															$text_class = 'mld-approve-text-class';
														}
														$assignment_points = get_post_meta( $id, 'points', true );
														?>
														<button class="mld-aproved-comment <?php echo $text_class;?> mld-aproved-comment-<?php echo $id; ?>" data-assignment_id="<?php echo $id; ?>" data-total_point="<?php echo $assignment_points; ?>"><?php echo $approve_text; ?></button>
													</div>
												</div>
												<div class="mld-assignment-grading-comment">
													<div class="mld-content">
														<button class="mld-comment-btn" data-assignment_id="<?php echo $id; ?>"><?php echo __( 'Comments', 'myrtle-learning-dashboard' ); ?></button>
													</div>
												</div>
												<div class="mld-clear-both"></div>
											</div>
										</div>
										<?php
									}
								}
								?>
							</div>
						</div>
					</div>
					<div class="mld-courses" id="mld-course-refresh">
						<?php
						if( 31 == $user_id ) {
							$per_page = 20;
						} else {
							$per_page = 4;
						}
						echo do_shortcode( '[ld_profile user_id="'.$user_id.'" per_page="'.$per_page.'" show_header="no"]' );
						?>
					</div>
				</div>
			</div>
			<div class="mld-clear-both"></div>
		</div>

		<div style="display: none;"class="mld-pop-outer">
			<div class="mld-pop-inner">
				<div class="mld-popup-header">
					<div class="mld-header-title"><?php echo __( 'Assignment Comment(s)' ); ?></div>
					<div class="mld-close" data_assignment-id="<?php echo $id; ?>"><span class="dashicons dashicons-no"></span></div>
					<div class="mld-clear-both"></div>
				</div>
				<div class="mld-assignment-comment-wrap"></div>
				<div class="mld-assignment-input-wrap">
					<div class="mld-assignment-input">
						<textarea id="mld-comment-box" placeholder="<?php echo __( 'Enter comment...' ); ?>" cols="10" rows="1.5"></textarea>
					</div>
					<div class="mld-assignment-submit">
						<button><?php echo __( 'Submit', 'myrtle-learning-dashboard' ); ?></button>
					</div>
					<div class="mld-clear-both"></div>
				</div>
			</div>
		</div>

		<?php
		$content = ob_get_contents();
		ob_get_clean();

		$response['content'] = $content;
		$response['course_titles'] = $course_titles;
		$response['course_cohort'] = $course_user_cohort_average;
		$response['course_target'] = $course_target_score;
		$response['status'] = 'true';

		echo json_encode( $response );
		wp_die();
	}

	/**
	 * create user report table
	 */
	public function mld_get_user_table() {

		$response = [];

		if( ! wp_verify_nonce( $_POST['mld_nounce'], 'mld_ajax_nonce' ) ) {

			$response['message'] = __( 'data not found', 'myrtle-learning-dashboard' );
			$response['status'] = 'false';

			echo json_encode( $response );
			wp_die();
		}

		$course_id = isset( $_POST['course_id'] ) ? $_POST['course_id'] : 0;
		$group_id = isset( $_POST['group_id'] ) ? $_POST['group_id'] : 0;
		$user_id = isset( $_POST['user_id'] ) ? $_POST['user_id'] : 0;
		$start_date = isset( $_POST['strat_date'] ) ? strtotime( $_POST['strat_date'] ) : 0;
		$end_date = isset( $_POST['end_start'] ) ? strtotime ( $_POST['end_start'] ) : 0;

		if( $course_id && $group_id ) {
			$content = self::mld_get_course_table( $group_id, $course_id, true, $start_date, $end_date );
		}

		if( $group_id && ! $course_id ) {
			$content = self::mld_get_group_table( $group_id, true, $start_date, $end_date, $user_id );
		}

		$response['content'] = $content;
		$response['status'] = 'true';
		echo json_encode( $response );
		wp_die();
	}

	/**
	 * get courses of selected group
	 */
	public function mld_get_group_courses() {

		$response = [];

		if( ! wp_verify_nonce( $_POST['mld_nounce'], 'mld_ajax_nonce' ) ) {

			$response['message'] = __( 'data not found', 'myrtle-learning-dashboard' );
			$response['status'] = 'false';

			echo json_encode( $response );
			wp_die();
		}

		$group_id = isset( $_POST['group_id'] ) ? $_POST['group_id'] : 0;

		if( ! $group_id ) {

			$response['message'] = __( 'Group id not found', 'myrtle-learning-dashboard' );
			$response['status'] = 'false';

			echo json_encode( $response );
			wp_die();
		}

		$group_courses = mld_get_group_courses( $group_id );

		ob_start();
		if( $group_courses && is_array( $group_courses ) ) {

			?>
			<option value=""><?php echo __( 'Select a course', 'myrtle-learning-dashboard' ); ?></option>
			<?php

			foreach( $group_courses as $group_course ) {
				?>
				<option value="<?php echo $group_course; ?>"><?php echo get_the_title( $group_course ); ?></option>
				<?php
			}
		}

		$content = ob_get_contents();
		ob_get_clean();

		$response['status'] = 'true';
		$response['content'] = $content;
		echo json_encode( $response );
		wp_die();
	}

 	/**
 	 * create a function to get course progress
 	 */
 	public static function get_course_progress( $user_id, $course_id, $start_time = 0, $end_time = 0 ) {

		$get_user_quizzess = get_user_meta( $user_id,'_sfwd-quizzes', true );
		$mld_quiz_avg_percentage = 0;

		$no = 0;
		if( ! empty( $get_user_quizzess ) && is_array( $get_user_quizzess ) ) {

			foreach( $get_user_quizzess as $get_user_quizz ) {
				$course = isset( $get_user_quizz['course'] ) ? $get_user_quizz['course'] : 0;
				if( $course_id == $course ) {
					$user_completed_time = $get_user_quizz['time'];

					/**
					 * perform start and end date conditions
					 */
					if( $start_time && ! $end_time ) {
						if( $user_completed_time > $start_time ) {
							$mld_quiz_avg_percentage += $get_user_quizz['percentage'];
							$no++;
						}
					} elseif( ! $start_time && $end_time ) {
						if( $user_completed_time < $end_time ) {
							$mld_quiz_avg_percentage += $get_user_quizz['percentage'];
							$no++;
						}
					} elseif( $start_time && $end_time ) {
						if( $user_completed_time > $start_time && $user_completed_time < $end_time ) {
							$mld_quiz_avg_percentage += $get_user_quizz['percentage'];
							$no++;
						}
					} else {
						$mld_quiz_avg_percentage += $get_user_quizz['percentage'];
						$no++;
					}
				}
			}	
		}

		if( empty( $no ) ) {
			return 0;
		}

		return round ( $mld_quiz_avg_percentage / $no );
 	}

 	/**
 	 * create a function to get quiz passing percentage
 	 */
 	public static function mld_get_quiz_passing_percentage( $quiz_ids ) {

 		if( empty( $quiz_ids ) || ! is_array( $quiz_ids ) ) {
 			return;
 		}

 		$passing_percentage = 0;

 		if( ! empty( $quiz_ids ) && is_array( $quiz_ids ) ) {

 			foreach( $quiz_ids as $quiz_id ) {

 				$percentage = get_post_meta( $quiz_id, '_sfwd-quiz', true );
 				$percentage_2 = isset( $percentage['sfwd-quiz_passingpercentage'] ) ? intval( $percentage['sfwd-quiz_passingpercentage'] ) : 0;
				$passing_percentage += 10;
 			}
 		}

 		$percentage_3 = ( $passing_percentage / count( $quiz_ids ) );
 		return $percentage_3;
 	}

 	/**
 	 * create a function to get group courses percentage
 	 */
 	public function get_group_courses_quiz_percentage( $group_id ) {

 		if( ! $group_id ) {
 			return;
 		}

 		$no = 0;
 		$course_quiz_percentage = 0;

 		$group_courses = mld_get_group_courses( $group_id );

 		if( ! $group_courses ) {
 			return;
 		}

 		if( ! empty( $group_courses ) && is_array( $group_courses ) ) {

 			foreach( $group_courses as $group_course ) {

 				$quizes = learndash_course_get_steps_by_type( $group_course, 'sfwd-quiz' );
 				$percentage = self::mld_get_quiz_passing_percentage( $quizes );
 				if( $percentage && NULL != $percentage ) {
 					$course_quiz_percentage += $percentage;
 					$no++;
 				}
 			}
 		}

		if( ! $no ) {
			return 0;
		}

 		return ( $course_quiz_percentage / $no );
 	}

 	/**
 	 * create a function to get cohot average score
 	 */
 	public function get_cohot_average( $group_id, $start_date, $end_date ) {

 		$group_users = mld_get_group_users( $group_id );

 		if( ! $group_users ) {
 			return;
 		}

 		$user_courses_progress = 0;

 		foreach( $group_users as $user_id ) {
 			$user_courses_progress += $this->get_user_group_courses_progress( $user_id, $group_id, $start_date, $end_date );
 		}

 		return round( ( $user_courses_progress / count( $group_users ) ) );
 	}

 	/**
 	 * create a function to get user group courses progress
 	 */
 	public function get_user_group_courses_progress( $user_id, $group_id, $start_date, $end_date ) {

 		$group_courses = mld_get_group_courses( $group_id );

 		if( ! $group_courses ) {
 			return;
 		}

 		$course_progress = 0;
 		$no = 0;

 		foreach( $group_courses as $group_course ) {

 			$mld_percentage = self::get_course_progress( $user_id, $group_course, $start_date, $end_date );
 			
 			if( $mld_percentage > 0 ) {

 				$course_progress += $mld_percentage;
 				$no++;
 			}
 		}

		if( ! empty( $no ) ) {

			return round( ( $course_progress / $no ) );
		} else {
			return 0;
		} 
 	}

 	/**
 	 * create a function to get course target score
 	 */
 	public static function get_course_target_score( $course_id ) {

		$quizes = learndash_course_get_steps_by_type( $course_id, 'sfwd-quiz' );
		$percentage = intval( self::mld_get_quiz_passing_percentage( $quizes ) );
		return $percentage;
		// return round( $percentage ).'%';
 	}

 	/**
 	 * create a function to get course users average
 	 */
 	public function get_course_user_cohort_average( $group_id, $course_id, $start_date, $end_date ) {

 		$group_users = mld_get_group_users( $group_id );

 		if( ! $group_users ) {
 			return;
 		}

 		$course_users_progress = 0;

 		foreach( $group_users as $user_id ) {
 			$course_users_progress += self::get_course_progress( $user_id, $course_id, $start_date, $end_date );
 		}

 		return round( ( $course_users_progress / count( $group_users ) ) );
 	}

 	/**
 	 * create a function to full report html
 	 */
 	public static function mld_full_report( $avg_score, $target_score, $cohort_score, $group_id, $user_id ) {

 		$group_courses = mld_get_group_courses( $group_id );

 		ob_start();
 		?>

 		<h3><?php echo __( 'Detail Report', 'myrtle-learning-dashboard' ); ?></h3>
 		<div class="mld-group-course-progress">
 			<?php
 			foreach( $group_courses as $group_course ) {

 				$check_user_access = sfwd_lms_has_access( $group_course, $user_id );

 				if( false == $check_user_access ) {
 					continue;
 				}

 				$mld_percentage = self::get_course_progress( $user_id, $group_course );
 				$course_target = self::get_course_target_score( $group_course );
 				$course_target = str_replace( '%','', $course_target );
 				$course_target_score[] = $course_target;
 				$course_user_cohort_average[] = $this->get_course_user_cohort_average( $group_id, $group_course );
 				$course_titles[] = get_the_title( $group_course );
 				?>
 				<div class="mld-user-progress">
 					<section class="mld-course-name"><?php echo get_the_title( $group_course ); ?></section>
 					<strong class="mld-course-percentage"><?php echo round( $mld_percentage ).'%'; ?></strong>
 				</div>
 				<?php
 			}
 			?>
 		</div>
 		<div class="mld-side-graph-wrapper">
 			<div class="course-graph-wrapper">
 				<section class="course-score-title"><?php echo __( 'AVG SCORE', 'myrtle-learning-dashboard' ); ?></section>
				<strong class="course-ave-score"><?php echo round( $avg_score ).'%'; ?></strong>
				<div class="course-ave-color" style="background: black;">
				<div class="mld-course-progress-bar" style="width: <?php echo round( $avg_score ).'%'; ?>; background-color: #39803e;"></div>
				</div>
 			</div>
 			<div class="course-graph-wrapper">
 				<section class="course-score-title"><?php echo __( 'TARGET SCORE', 'myrtle-learning-dashboard' ); ?></section>
				<strong class="course-ave-score"><?php echo round( $target_score ).'%' ?></strong>
				<div class="course-ave-color">
					<div class="mld-course-progress-bar" style="width: <?php echo round( $target_score ).'%'; ?>; background-color: #32584a;"></div>
				</div>
 			</div>
 			<div class="course-graph-wrapper">
 				<section class="course-score-title"><?php echo __( 'COHORT AVG SCORE', 'myrtle-learning-dashboard' ); ?></section>
				<strong class="course-ave-score"><?php echo round( $cohort_score ).'%'; ?></strong>
				<div class="course-ave-color">
					<div class="mld-course-progress-bar" style="width: <?php echo round( $cohort_score ).'%'; ?>; background-color: #fbb11b;"></div>
				</div>
 			</div>

 		</div>
 		<?php
 		$content = ob_get_contents();
 		ob_get_clean();
 		return $content;
 	}

 	/**
 	 * create a function to get user assignment
 	 */
 	public function get_user_assignment( $group_id, $user_id, $start_time, $end_time ) {

 		global $wpdb;

 		$table_name = $wpdb->prefix.'posts';

 		$assignments = $wpdb->get_results( $wpdb->prepare( "
 			SELECT ID, post_title, post_modified FROM $table_name WHERE
 			post_type = %s AND post_author = %d", 'sfwd-assignment', $user_id ) );

 		$group_courses = mld_get_group_courses( $group_id );

 		$assignment_data = [];

 		if( $assignments && is_array( $assignments ) ) {

 			foreach( $assignments as $assignment ) {

				$assignment_date = isset( $assignment->post_modified ) ? strtotime( $assignment->post_modified ) : 0;
 				$assigment_id = $assignment->ID;
 				$course_id = learndash_get_course_id( $assigment_id );

 				if( in_array( $course_id, $group_courses ) ) {

					if( $start_time && ! $end_time ) {
						if( $assignment_date > $start_time ) {

							$assignment_data[$assigment_id] = [
								'date' => $assignment->post_modified,
								'title'=> $assignment->post_title
							];
						}
					} elseif( ! $start_time && $end_time ) {
						if( $assignment_date < $end_time ) {

							$assignment_data[$assigment_id] = [
								'date' => $assignment->post_modified,
								'title'=> $assignment->post_title
							];
						}
					} elseif( $start_time && $end_time ) {
						if( $assignment_date > $start_time && $assignment_date < $end_time ) {

							$assignment_data[$assigment_id] = [
								'date' => $assignment->post_modified,
								'title'=> $assignment->post_title
							];
						}
					} else {

						$assignment_data[$assigment_id] = [
							'date' => $assignment->post_modified,
							'title'=> $assignment->post_title
						];
					}
 				}
 			}
 		}
 		return $assignment_data;
 	}

	/**
	 * create a function to get course table
	 *
	 * @param $group_id
	 * @param $course_id
	 */
	public static function mld_get_course_table( $group_id, $course_id, $download_btn, $start_time, $end_time ) {

		$group_users = mld_get_group_users( $group_id );

		if( empty( $group_users ) || ! is_array( $group_users ) ) {

			$response['message'] = __( 'data not found', 'myrtle-learning-dashboard' );
			$response['status'] = 'false';

			echo json_encode( $response );
			wp_die();
		}

		ob_start();
		if( true == $download_btn ) {
			?>
			<a class="mld-download-report" href="<?php echo get_permalink().'?mld_group_id='.$group_id.'&mld_c_id='.$course_id.'&mld_start_date='.$start_time.'&mld_end_date='.$end_time.''; ?>" target="_blank"><?php echo __( 'Download Report', 'myrtle-learning-dashboard' ); ?></a>
			<?php
		} else {
			?>
			<h2><?php echo __( "Students Record", 'myrtle-learning-dashboard' ); ?></h2>
			<?php
		}
		?>
		<div class="mld-report-table-wrapper">
			<table class="mld-user-report-table" cellspacing="0" cellpadding="10" border="0.5">
				<thead>
					<tr style="background-color:#18440a; text-align:center;">
						<th><?php echo __( 'No', 'myrtle-learning-dashboard' ); ?></th>
						<th><?php echo __( 'Course', 'myrtle-learning-dashboard' ); ?></th>
						<th><?php echo __( 'Student Name', 'myrtle-learning-dashboard' ); ?></th>
						<th style="background:#fcb408;"><?php echo __( 'Score', 'myrtle-learning-dashboard' ); ?></th>
						<th><?php echo __( 'Target', 'myrtle-learning-dashboard' ); ?></th>
						<th><?php echo __( 'Academic', 'myrtle-learning-dashboard' ); ?></th>
						<th><?php echo __( 'Behaviour', 'myrtle-learning-dashboard' ); ?></th>
					</tr>
				</thead>
				<tbody>
			<?php

			foreach( $group_users as $group_user ) {

				$check_user_access = sfwd_lms_has_access( $course_id, $group_user );

				if( false == $check_user_access ) {
					continue;
				}

				$user_in_group = learndash_is_user_in_group( $group_user, $group_id );

				if( ! $user_in_group ) {
					continue;
				}

				$course_progress = self::get_course_progress( $group_user, $course_id, $start_time, $end_time );
				$academic_comments = get_user_meta( $group_user, 'mld_academic_comment_'.$group_id.'_'.$course_id, true );
				$academic_comments = empty( $academic_comments ) ? [] : $academic_comments;

				$behavier_comments = get_user_meta( $group_user, 'mld_behavior_comment_'.$group_id.'_'.$course_id, true );
				$behavier_comments = empty( $behavier_comments ) ? [] : $behavier_comments;

				?>
				<tr class="mld-table-row" style="text-align:center;">
					<td><?php echo $course_id; ?></td>
					<td><?php echo ucwords( get_the_title( $course_id ) ); ?></td>
					<td class="mld-user-report" data-user_id="<?php echo $group_user; ?>" data-course_id="<?php echo $course_id; ?>" data-avatar_url="<?php echo get_avatar_url( $group_user ); ?>"><?php echo mld_get_username( $group_user ); ?></td>
					<td style="background: #fcb408; color: #18440a;"><?php echo round( $course_progress ).'%'; ?></td>
					<td><?php echo round( intval( self::get_course_target_score( $course_id ) ) ).'%'; ?></td>
					<td class="mld-academic-star" data-academic_star="<?php echo count( $academic_comments ); ?>">
						<?php
						$academic_c = count( $academic_comments );
						if( $academic_c > 0 ) {
							for ( $x = 1; $x <= $academic_c; $x++) {
								echo '*';
							}
						} else {
							echo '0';
						}
						?>
					</td>
					<td class="mld-behaviour-star" data-behaviour_star="<?php echo count( $behavier_comments ); ?>">
						<?php
						$bahavior_c = count( $behavier_comments );
						if( $bahavior_c > 0 ) {
							for ( $x = 1; $x <= $bahavior_c; $x++) {
								echo '*';
							}
						} else {
							echo '0';
						}
						?>
					</td>
				</tr>
				<?php
			}

			?>
				</tbody>
			</table>
		</div>
		<div class="mls-user-table-detail-report">
		</div>
		<?php

		$content = ob_get_contents();
		ob_get_clean();
		return $content;
	}

	/**
	 * create a function to get group table
	 */
	public static function mld_get_group_table( $group_id, $download_btn, $start_date, $end_date, $user_id ) {

		$group_courses = mld_get_group_courses( $group_id );

		ob_start();
		if( $group_courses && is_array( $group_courses ) ) {

			if( true == $download_btn ) {
				?>
				<a class="mld-download-report" href="<?php echo get_permalink().'?mld_group_id='.$group_id.'&user_id='.$user_id.'&mld_start_date='.$start_date.'&mld_end_date='.$end_date.''; ?>" target="_blank"><?php echo __( 'Download Report', 'myrtle-learning-dashboard' ); ?></a>
				<?php
			} else {
				?>
				<h2><?php echo __( "Group Courses Record", 'myrtle-learning-dashboard' ); ?></h2>
				<?php
			}

			$user_course_detail = 'mld_user_detail_report';
			if( ! $user_id ) {
				$user_course_detail = '';
			}
			?>
			<div class="mld-report-table-wrapper">
				<table class="mld-user-report-table" cellspacing="0" cellpadding="10" border="0.5">
					<thead>
						<tr style="background-color:#18440a; text-align:center;">
							<th><?php echo __( 'No', 'myrtle-learning-dashboard' ); ?></th>
							<th><?php echo __( 'Course', 'myrtle-learning-dashboard' ); ?></th>
							<th style="background:#fcb408;"><?php echo __( 'Score', 'myrtle-learning-dashboard' ); ?></th>
							<th><?php echo __( 'Target', 'myrtle-learning-dashboard' ); ?></th>
							<th><?php echo __( 'Academic', 'myrtle-learning-dashboard' ); ?></th>
							<th><?php echo __( 'Behaviour', 'myrtle-learning-dashboard' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach( $group_courses as $group_course ) {
							$academic = self::mld_get_user_points_average( $group_id, $group_course, $user_id );
							?>
							<tr style="text-align: center;">
								<td><?php echo $group_course; ?></td>
								<td class="<?php echo $user_course_detail; ?>" data-course_id="<?php echo $group_course; ?>"><?php echo get_the_title( $group_course ); ?></td>
								<td style="background:#fcb408;"><?php echo round( self::mld_get_course_users_progress_average( $group_course, $start_date, $end_date, $user_id ) ).'%'; ?></td>
								<td><?php echo round( self::get_course_target_score( $group_course ) ).'%'; ?></td>
								<td>
									<?php
									$acedemic_count = isset( $academic['academic'] ) ? round( $academic['academic'] ) : 0;
									$academic_count = intval( $acedemic_count );
									if( $academic_count > 0 ) {
										for ( $x = 1; $x <= $academic_count; $x++) {
											echo '*';
										}
									} else {
										echo '0';
									}
									?>
								</td>
								<td>
									<?php
									$behavior_count = isset( $academic['behavior'] ) ? round( $academic['behavior'] ) : 0;
									$behavior_count = intval( $behavior_count );
									if( $behavior_count > 0 ) {
										for ( $x = 1; $x <= $behavior_count; $x++) {
											echo '*';
										}
									} else {
										echo '0';
									}
									?>
								</td>
							</tr>
							<?php
						}
						?>
					</tbody>
				</table>
			</div>
			<div class="mld-user-course-detail-wrapper"></div>
			<?php
		}

		$content = ob_get_contents();
		ob_get_clean();
		return $content;
	}

	/**
	 * create a function to get course users average
	 */
	public static function mld_get_course_users_progress_average( $course_id, $start_date, $end_date, $user_id ) {

		if( ! $user_id ) {

			$course_users = learndash_get_course_users_access_from_meta( $course_id );
			$u_c_progress = 0;
			$users = 0;
			if( $course_users && is_array( $course_users ) ) {
				$users = count( $course_users );
				foreach( $course_users as $course_user ) {
					$u_c_progress += self::get_course_progress( $course_user, $course_id, $start_date, $end_date );
				}
			}

			if( 0 == $users ) {
				return 0;
			}

			return ( $u_c_progress / $users );
		} else {
			$u_c_progress = self::get_course_progress( $user_id, $course_id, $start_date, $end_date );
			return $u_c_progress;
		}
	}

	/**
	 * create a function to get user point average
	 */
	public static function mld_get_user_points_average( $group_id, $course_id, $user_id ) {

		if( ! $user_id ) {

			$users = learndash_get_course_users_access_from_meta( $course_id );
			$points_average = [];
			$users_count = 0;
			if( $users && is_array( $users ) ) {

				foreach( $users as $user ) {
					$academic_points = get_user_meta( $user, 'mld_academic_comment_'.$group_id.'_'.$course_id, true );
					$behavior_points = get_user_meta( $user, 'mld_behavior_comment_'.$group_id.'_'.$course_id, true );
					if( ! empty( $academic_points ) ) {
						$users_count++;
						$points_average['academic_points'][] = count( $academic_points );
						$points_average['behavior_points'][] = count( $behavior_points );
					}

				}
			}

			if( empty( $users_count ) ) {
				return 0;
			}
			$academic = isset( $points_average['academic_points'] ) ? $points_average['academic_points'] : [];
			$academic_average = isset( $points_average['academic_points'] ) ? array_sum( $points_average['academic_points'] ) : 0;
			$behavior_average = isset( $points_average['behavior_points'] ) ? array_sum( $points_average['behavior_points'] ) : 0;

			$points = [];
			$points[ 'academic' ] = round( ( $academic_average / $users_count ) );
			$points[ 'behavior' ] = round( ( $behavior_average / $users_count ) );
			return $points;
		} else {

			$academic_points = intval( get_user_meta( $user_id, 'mld_academic_comment_'.$group_id.'_'.$course_id, true ) );
			$behavior_points = intval( get_user_meta( $user_id, 'mld_behavior_comment_'.$group_id.'_'.$course_id, true ) );

			$points = [];
			$points[ 'academic' ] = round( $academic_points );
			$points[ 'behavior' ] = round( $behavior_points );
			return $points;
		}
	}

	/**
	 * create a function to get user comments
	 */
	public function mld_get_assignment_comments( $id ) {

		global $wpdb;

		$table_name = $wpdb->prefix.'comments';
		$comments = $wpdb->get_results( $wpdb->prepare( "
			SELECT * FROM $table_name WHERE
			comment_post_ID = %d AND comment_approved = %s ", $id, '1' ) );

		if( ! $comments || empty( $comments ) ) {
			return [];
		}

		return $comments;
	}
}

Myrtle_Report_Template::instance();