<?php
/**
 * work templates
 */

if( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class Myrtle_Work
 */
class Myrtle_Work {

	/**
	 * @var self
	 */
	private static $instance = null;

	/**
     * user_id
     */
	private $user_id;

	/**
	 * @since 1.0
	 * @return $this
	 */
	public static function instance() {

		if ( is_null( self::$instance ) && ! ( self::$instance instanceof Myrtle_Work ) ) {
			self::$instance = new self;
			self::$instance->hooks();
			self::$instance->user_id = get_current_user_id();
		}

		return self::$instance;
	}

	/**
	 * Call hooks
	 *
	 * @return void
	 */
	public function hooks() {
		add_action( 'wp_ajax_get_work_group_id', [ $this, 'mld_group_on_change' ] );
		add_action( 'wp_ajax_get_work_user_id', [ $this, 'mld_user_id_on_change' ] );
		add_action( 'wp_ajax_get_work_course_id', [ $this, 'mld_course_on_change' ] );
		add_action( 'wp_ajax_get_work_lesson_id', [ $this, 'mld_lesson_on_change' ] );
		add_action( 'wp_ajax_get_work_topic_id', [ $this, 'mld_topic_on_change' ] );
		add_action( 'wp_ajax_proceed_to_quiz_detail', [ $this, 'mld_proceed_to_quiz_detail' ] );
		add_action( 'wp_ajax_get_essays_comment', [ $this, 'mld_display_essays_comment' ] );
		add_action( 'wp_ajax_update_essays_comment', [ $this, 'mld_update_essays_comment' ] );
		add_action( 'wp_ajax_get_grading_html', [ $this, 'mld_get_grading_html' ] );
		add_action( 'wp_ajax_approve_essay', [ $this, 'mld_approve_essay' ] );
		add_action( 'wp_ajax_award_points', [ $this, 'mld_award_points' ] );
		add_action( 'wp_ajax_delete_essay_comments', [ $this, 'mld_delete_essay_comments' ] );
		add_action( 'wp_ajax_set_awarded_points', [ $this, 'mld_set_awarded_points' ] );
	}

	/**
	 * set awarded points
	 */
	public function mld_set_awarded_points() {

		global $wpdb;

		$point_count = isset( $_POST['point'] ) ? intval( $_POST['point'] ) : '';
		$essay_id = isset( $_POST['essay_id'] ) ? $_POST['essay_id'] : '';
		$question_id = get_post_meta( $essay_id, 'question_id', true );
		$quiz_id = get_post_meta( $essay_id, 'quiz_id', true );
		$essay = $this->mld_get_essay( $essay_id );
		$submitted_essay = learndash_get_submitted_essay_data( $quiz_id, $question_id, $essay );
		$submitted_essay['points_awarded'] = $point_count;
		learndash_update_submitted_essay_data( $quiz_id, $question_id, $essay, $submitted_essay );
		wp_die();
	}

	/**
	 * delete essay comments
	 */
	public function mld_delete_essay_comments() {

		global $wpdb;

		$comment_id = isset( $_POST['comment_id'] ) ? $_POST['comment_id'] : '';

		if( ! $comment_id ) {
			wp_die();
		}

		$table_name = $wpdb->prefix . 'comments';

		$wpdb->query( $wpdb->prepare( 
			"
			DELETE FROM $table_name
			WHERE comment_ID = %d
			",
			$comment_id
		) );

		wp_die();
	}

	/**
	 * create a function to get essay
	 */
	public function mld_get_essay( $essay_id ) {

		global $wpdb;

		$query = $wpdb->prepare("
			SELECT *
			FROM $wpdb->posts
			WHERE post_type = %s
			AND ID = %d
			", 'sfwd-essays', $essay_id	 );
		$essay = $wpdb->get_results( $query );
		$essay = isset( $essay[0] ) ? $essay[0] : '';
		return $essay;
	}

	/**
	 * award points
	 */
	public function mld_award_points() {

		global $wpdb;

		$point_count = isset( $_POST['point_count'] ) ? intval( $_POST['point_count'] ) : '';
		
		$essay_id = isset( $_POST['essay_id'] ) ? $_POST['essay_id'] : '';
		$question_id = get_post_meta( $essay_id, 'question_id', true );
		$quiz_id = get_post_meta( $essay_id, 'quiz_id', true );
		$essay = $this->mld_get_essay( $essay_id );
		$submitted_essay = learndash_get_submitted_essay_data( $quiz_id, $question_id, $essay );

		$status = isset( $submitted_essay['graded'] ) ? $submitted_essay['graded'] : '';
		
		$quiz_score_difference = 0;
		
		if( 'graded' == $status ) {
			$quiz_score_difference = 1;
		} else {
			$quiz_score_difference = -1;
		}

		$previous_point = isset( $submitted_essay['points_awarded'] ) ? intval( $submitted_essay['points_awarded'] ) : 0;

		if( $point_count ) {

			if( $point_count > $previous_point ) {
			 	$points_awarded_difference = $point_count - $previous_point;
			} else {
			 	$points_awarded_difference = ( $previous_point - $point_count ) * -1;
			}

			$updated_scoring = array(
				'updated_question_score'    => $point_count,
				'points_awarded_difference' => $points_awarded_difference,
				'score_difference'          => $quiz_score_difference,
			);

			learndash_update_quiz_data( $quiz_id, $question_id, $updated_scoring, $essay );
			$submitted_essay['points_awarded'] = $point_count;
			learndash_update_submitted_essay_data( $quiz_id, $question_id, $essay, $submitted_essay );
		}

		wp_die();
	}

	/**
	 * approve essay
	 */
	public function mld_approve_essay() {

		global $wpdb;

		$essay_id = isset( $_POST['essay_id'] ) ? $_POST['essay_id'] : '';
		$question_id = get_post_meta( $essay_id, 'question_id', true );
		$quiz_id = get_post_meta( $essay_id, 'quiz_id', true );
		$essay = $this->mld_get_essay( $essay_id );

		$submitted_essay = learndash_get_submitted_essay_data( $quiz_id, $question_id, $essay );
		$submitted_essay['status'] = 'graded';

		learndash_update_submitted_essay_data( $quiz_id, $question_id, $essay, $submitted_essay );
		$query = $wpdb->prepare("
			UPDATE {$wpdb->prefix}posts
			SET post_status = %s
			WHERE ID = %d
			AND post_type = %s
			", 'graded', $essay_id, 'sfwd-essays' );
		$wpdb->query($query);
		wp_die();
	}

	/**
	 * get grading html
	 */
	public function mld_get_grading_html() {
		
		$essay_id = isset( $_POST['essay_id'] ) ? intval( $_POST['essay_id'] ) : '';

		if( empty( $essay_id ) ) {

			$response['message'] = __( 'essay id not found', 'myrtle-learning-dashboard' );
			$response['status'] = 'false';
			echo json_encode( $response );
			wp_die();
		}

		$essay_detail = learndash_get_essay_details( $essay_id );

		$total_point = isset( $essay_detail['points']['total'] ) ? $essay_detail['points']['total'] : 0;
		$awarded_point = isset( $essay_detail['points']['awarded'] ) ? $essay_detail['points']['awarded'] : 0;
		$status = isset( $essay_detail['status'] ) ? $essay_detail['status'] : '';
		ob_start();
		
		?>
		<div class="mld-grading-status-wrapper">
			<div class="mld-grading-status-title"><?php echo __( 'Status', 'myrtle-learning-dashboard' ); ?></div>
			<div class="mld-grading-status">
				<?php 
				if( 'graded' != $status ) {
					?>
					<span><?php echo __( 'Not Approve', 'myrtle-learning-dashboard' ); ?></span>
					<button style="font-size: 17px;" class="mld-essay-apr-btn" data-eaasy_id="<?php echo $essay_id; ?>"><?php echo __( 'Approve', 'myrtle-learning-dashboard' ); ?></button>
					<?php
				} else {
					?>
					<button><?php echo __( 'Approved', 'myrtle-learning-dashboard' ); ?></button>
					<?php
				}
				?>
			</div>
		</div>
		<div class="mld-grading-points-wrapper">
			<div class="mld-grading-point-title"><?php echo __( 'Award Points', 'myrtle-learning-dashboard' ); ?></div>
			<div class="mld-grading-point">
				<input type="text" placeholder="<?php echo __( 'points', 'myrtle-learning-dashboard' ); ?>">
				<button class="mld-essay-point-btn" data-eaasy_id="<?php echo $essay_id; ?>"><?php echo __( 'Award', 'myrtle-learning-dashboard' ); ?></button>
			</div>
		</div>
		<div class="mld-point-text">
			<?php echo $awarded_point.'/'.$total_point ?> <?php echo __( 'Points Awarded', 'myrtle-learning-dashboard' ); ?>
		</div>
		<div class="mld-essay-total-point-wrapper">
			<div class="mld-essay-total-point">
				<?php echo __( 'Set total point(s)', 'myrtle-learning-dashboard' ); ?>
			</div>
			<div class="mld-total-point-set-wrapper">
				<input type="text" name="">
				<button class="mld-set-awarded-point" data-eaasy_id="<?php echo $essay_id; ?>"><?php echo __( 'Update', 'myrtle-learning-dashboard' ); ?></button>
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
	 * update comment
	 */
	public function mld_update_essays_comment() {

		global $wpdb;

		$comment = isset( $_POST['comment'] ) ? $_POST['comment'] : '';
		$comment_post_id = isset( $_POST['essay_id'] ) ? $_POST['essay_id'] : '';
		$user_id = isset( $_POST['user_id'] ) ? $_POST['user_id'] : '';

		wp_insert_comment(
			array(
				'comment_post_ID'   	=> $comment_post_id,
				'comment_author'		=> mld_get_username( $user_id ),
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
		wp_die();
	}

	/**
	 * create a function to get comments
	 */
	public function mld_get_essays_comment( $id ) {

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

	/**
	 * get essays comment 
	 */
	public function mld_display_essays_comment() {
		
		global $wpdb;

		$essay_id = isset( $_POST['essay_id'] ) ? intval( $_POST['essay_id'] ) : '';
		$logged_in_user = self::$instance->user_id;

		if( empty( $essay_id ) ) {

			$response['message'] = __( 'essay id not found', 'myrtle-learning-dashboard' );
			$response['status'] = 'false';
			echo json_encode( $response );
			wp_die();
		}

		$comments = $this->mld_get_essays_comment( $essay_id );

		ob_start();
		if( ! empty( $comments ) ) {
			foreach( $comments as $key => $comment ) {

				$comment_id = isset( $comment->comment_ID ) ? $comment->comment_ID : '';
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
								<?php

								echo stripslashes( $comment->comment_content ); 
								$user_capability = mld_user_capability( $logged_in_user );
								if( in_array( 'administrator', $user_capability ) ) {
									?>
									<div class="dashicons dashicons-trash mld-comment-trash" data-comment_id="<?php echo $comment_id; ?>"></div>
									<?php
								}
								?>
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
	 * proceed to quiz detail
	 */
	public function mld_proceed_to_quiz_detail() {

		global $wpdb;
		
		$user_id = isset( $_POST['user_id'] ) ? intval( $_POST['user_id'] ) : '';
		$quiz_id = isset( $_POST['quiz_id'] ) ? intval( $_POST['quiz_id'] ) : '';
		$user_capability = mld_user_capability( $this->user_id );
		
		$review_class = '';

		if( in_array( 'administrator', $user_capability ) || in_array( 'group_leader', $user_capability ) ) {
			$review_class = 'mld-essay-review';
		}

		$query = $wpdb->prepare("
			SELECT ID, post_date, post_title
			FROM $wpdb->posts
			WHERE post_type = %s
			AND post_author = %d
			", 'sfwd-essays', $user_id );


		$essays = $wpdb->get_results( $query );

		ob_start();
		?>
		<div class="mld-back-btn">
			<span class="dashicons dashicons-arrow-left-alt"></span>
			<span class="mld-go-back"><?php echo __( 'Go Back', 'myrtle-learning-dashboard' ); ?></span>
		</div>
		<?php
		
		if( empty( $essays ) ) {
			?>
			<div class="mld-work-error-message">
				<?php echo __( 'No data found', 'myrtle-learning-dashboard' ); ?>
			</div>
			<?php
		}

		if( ! empty( $essays ) && is_array( $essays ) ) {
			?>
			<div class="mld-work-second-wrapper">
				<table>
					<tr>
						<th><?php echo __( 'Title', 'myrtle-learning-dashboard' ); ?></th>
						<th><?php echo __( 'Date submitted', 'myrtle-learning-dashboard' ); ?></th>
						<th><?php echo __( 'Grading', 'myrtle-learning-dashboard' ); ?></th>
						<th><?php echo __( 'Comments', 'myrtle-learning-dashboard' ); ?></th>
						<th></th>
					</tr>
				<?php
				foreach( $essays as $essay ) {

					$post_id = isset( $essay->ID ) ? $essay->ID : '';

					$user_uploaded_quiz = learndash_get_user_quiz_entry_for_essay( $post_id, $user_id );
					$uploaded_quiz_id = isset( $user_uploaded_quiz['quiz'] ) ? $user_uploaded_quiz['quiz'] : '';

					if( $quiz_id != $uploaded_quiz_id ) {
						continue;
					}

					$file_url = get_post_meta( $post_id, 'upload', true );
					$post_date = isset( $essay->post_date ) ? $essay->post_date : '';
					$essay_detail = learndash_get_essay_details( $post_id );
					$status = isset( $essay_detail['status'] ) ? $essay_detail['status'] : '';
					
					$rev_text = 'Review';
					$bg_color = '';
					$text_color = '';
					
					if( 'graded' == $status ) {

						$rev_text = 'Reviewed';
						$bg_color = '#18440a';
						$text_color = 'white';
					}

					$timestamp = strtotime( $post_date );
					$formattedDate = date( 'jS F Y', $timestamp );
					$post_title = isset( $essay->post_title ) ? $essay->post_title : '';
					
					?>
					<tr>
						<td width="49%">
							<a href="<?php echo $file_url; ?>" download><?php echo $post_title; ?></a>
						</td>
						<td width="17%"><?php echo $formattedDate; ?></td>
						<td width="17%" class="<?php echo $review_class; ?>" data_id="<?php echo $post_id; ?>" style="background-color: <?php echo $bg_color; ?>; color: <?php echo $text_color; ; ?>;"><?php echo $rev_text; ?></td>
						<td width="17%" class="mld-work-comment"  data_id="<?php echo $post_id; ?>" style="background: #fcb408; color: white;"><?php echo __( 'Comment', 'myrtle-learning-dashboard' ); ?></td>
					</tr>
					<?php
				}
				?>
				</table>
			</div>

			<div class="mld-pop-outer" style="display: none;">
				<div class="mld-pop-inner">
					<div class="work-popup-closed">
						<div class="mld-essay-comment-title"><?php echo __( 'Comment', 'myrtle-learning-dashboard' ); ?></div>
						<div class="dashicons dashicons-dismiss"></div>
					</div>
					<div class="mld-word-comment-section">
					</div>
					<div class="mld-essay-comment-input">
						<div class="mld-work-input">
							<textarea id="mld-essay-comment-box" placeholder="<?php echo __( 'Enter comment...', 'myrtle-learning-dashboard' ); ?>" cols="10" rows="1.5"></textarea>
						</div>
						<div class="mld-work-submit">
							<button class="mld-essay-comment-update-btn"><?php echo __( 'Submit', 'myrtle-learning-dashboard' ); ?></button>
							<input type="hidden" data-user_id="<?php echo $this->user_id; ?>" data-essay_id="" class="mld-hidden-ids">
						</div>
						<div class="mld-clear-both"></div>
					</div>
					<img src="<?php echo MLD_ASSETS_URL.'images/spinner.gif' ?>" class="mld-my-work-loader">
				</div>
			</div>

			<?php
		}

		$content = ob_get_contents();
		ob_get_clean();

		$response['content'] = $content;
		$response['status'] = 'true';
		echo json_encode( $response );
		wp_die();

	}

	/**
	 * get quiz
	 */
	public function mld_topic_on_change() {
		
		$response = [];
		$topic_id = isset( $_POST['topic_id'] ) ? intval( $_POST['topic_id'] ) : '';
		$course_id = isset( $_POST['course_id'] ) ? intval( $_POST['course_id'] ) : '';
		$user_id = isset( $_POST['user_id'] ) ? intval( $_POST['user_id'] ) : '';

		if( empty( $topic_id ) ) {

			$response['message'] = __( 'topic id not found', 'myrtle-learning-dashboard' );
			$response['status'] = 'false';

			echo json_encode( $response );
			wp_die();
		}
		
		$quiz_list = mld_get_lesson_topic_quizzess( $course_id, $topic_id );

		if( empty( $quiz_list ) && ! is_array( $quiz_list ) ) {
			
			$response['message'] = __( 'quiz id not found', 'myrtle-learning-dashboard' );
			$response['status'] = 'false';

			echo json_encode( $response );
			wp_die();				
		}

		ob_start();

		?>
		<option value=""><?php echo __( 'Select a Quiz', 'myrtle-learning-dashboard' ); ?></option>
		<?php
		foreach( $quiz_list as $quiz_id ) {

			?>
			<option value="<?php echo $quiz_id;?>"><?php echo get_the_title( $quiz_id ); ?></option>
			<?php
		}

		$content = ob_get_contents();
		ob_get_clean();

		$response['content'] = $content;
		$response['status'] = 'true';
		echo json_encode( $response );
		wp_die();
	}

	/**
	 * get the list of topic
	 */
	public function mld_lesson_on_change() {

		$response = [];
		$lesson_id = isset( $_POST['lesson_id'] ) ? intval( $_POST['lesson_id'] ) : '';
		$course_id = isset( $_POST['course_id'] ) ? intval( $_POST['course_id'] ) : '';
		$user_id = isset( $_POST['user_id'] ) ? $_POST['user_id'] : '';

		if( empty( $lesson_id ) ) {

			$response['message'] = __( 'lesson id not found', 'myrtle-learning-dashboard' );
			$response['status'] = 'false';

			echo json_encode( $response );
			wp_die();
		}

		$lesson_quizzess = mld_get_lesson_topic_quizzess( $course_id, $lesson_id );
		
		if( ! empty( $lesson_quizzess ) && is_array( $lesson_quizzess ) ) {

			ob_start();

			?>
			<option value=""><?php echo __( 'Select a Quiz', 'myrtle-learning-dashboard' ); ?></option>
			<?php
			foreach( $lesson_quizzess as $quiz_id ) {
				?>
				<option value="<?php echo $quiz_id; ?>"><?php echo get_the_title( $quiz_id ); ?></option>
				<?php
			}

			$content = ob_get_contents();
			ob_get_clean();

			$response['quiz_content'] = $content;
			$response['status'] = 'true';
		}

		// $lesson_topic = mld_get_lesson_topics( $lesson_id, $course_id );
		$lesson_topic = learndash_get_topic_list( $lesson_id, $course_id );
		$lesson_topic = array_column( $lesson_topic, 'ID' );
		
		if( empty( $lesson_topic ) && ! is_array( $lesson_topic ) ) {

			ob_start();

			?>
			<option value=""><?php echo __( 'Select a topic', 'myrtle-learning-dashboard' ); ?></option>
			<?php

			$content = ob_get_contents();
			ob_get_clean();

			$response['message'] = __( 'topic id not found', 'myrtle-learning-dashboard' );
			$response['content'] = $content;
			$response['status'] = 'false';

			echo json_encode( $response );
			wp_die();
		}

		ob_start();

		if( $lesson_topic && is_array( $lesson_topic ) ) {
			?>
			<option value=""><?php echo __( 'Select a Topic', 'myrtle-learning-dashboard' ); ?></option>
			<?php
			foreach( $lesson_topic as $topic_id ) {

				?>
				<option value="<?php echo $topic_id;?>"><?php echo get_the_title( $topic_id ); ?></option>
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
	 * get lesson of a courses
	 */
	public function mld_course_on_change() {

		$response = [];

		$course_id = isset( $_POST['course_id'] ) ? intval( $_POST['course_id'] ) : '';
		$user_id = isset( $_POST['user_id'] ) ? $_POST['user_id'] : '';

		if( empty( $course_id ) ) {

			$response['message'] = __( 'course id not found', 'myrtle-learning-dashboard' );
			$response['status'] = 'false';

			echo json_encode( $response );
			wp_die();
		}

		$course_lesson = mld_get_course_lessons( $course_id );
		$course_quizzess = mld_get_course_quizzess( $course_id );
		
		if( ! empty( $course_quizzess ) && is_array( $course_quizzess ) ) {

			ob_start();
				?>
				<option value=""><?php echo __( 'Select a Quiz', 'myrtle-learning-dashboard' ); ?></option>
				<?php
				foreach( $course_quizzess as $quiz_id ) {
					?>
					<option value="<?php echo $quiz_id;?>"><?php echo get_the_title( $quiz_id ); ?></option>
					<?php
				}

			$content = ob_get_contents();
			ob_get_clean();

			$response['quiz_content'] = $content;
			$response['status'] = 'true';
		}

		if( empty( $course_lesson ) && ! is_array( $course_lesson ) ) {

			$response['message'] = __( 'lesson id not found', 'myrtle-learning-dashboard' );
			$response['status'] = 'false';

			echo json_encode( $response );
			wp_die();
		}

		ob_start();

			?>
			<option value=""><?php echo __( 'Select a Lesson', 'myrtle-learning-dashboard' ); ?></option>
			<?php
			foreach( $course_lesson as $lesson_id ) {
				?>
				<option value="<?php echo $lesson_id;?>"><?php echo get_the_title( $lesson_id ); ?></option>
				<?php
			}

		$content = ob_get_contents();
		ob_get_clean();

		$response['content'] = $content;
		$response['status'] = 'true';
		echo json_encode( $response );
		wp_die();
	}

	/**
	 * get user courses
	 */
	public function mld_user_id_on_change() {

		$response = [];

		global $wpdb;

		$user_id = isset( $_POST['user_id'] ) ? intval( $_POST['user_id'] ) : '';

		if( empty( $user_id ) ) {

			$response['message'] = __( 'user id not found', 'myrtle-learning-dashboard' );
			$response['status'] = 'false';

			echo json_encode( $response );
			wp_die();
		}

		$user_capability = mld_user_capability( $user_id );
		$user_courses = mld_get_user_courses( $user_id );
		
		if( empty( $user_courses ) && ! is_array( $user_courses ) ) {

			$response['message'] = __( 'course id not found', 'myrtle-learning-dashboard' );
			$response['status'] = 'false';

			echo json_encode( $response );
			wp_die();
		}

		ob_start();

		?>
		<option value=""><?php echo __( 'Select a Course', 'myrtle-learning-dashboard' ); ?></option>
		<?php
		foreach( $user_courses as $user_course ) {

			$user_course = isset( $user_course->id ) ? $user_course->id : 0;
			?>
			<option value="<?php echo $user_course;?>"><?php echo get_the_title( $user_course ); ?></option>
			<?php
		}

		$content = ob_get_contents();
		ob_get_clean();

		$response['content'] = $content;
		$response['status'] = 'true';
		echo json_encode( $response );
		wp_die();
	}

	/**
	 * get group users
	 */
	public function mld_group_on_change() {

		global $wpdb;

		$response = [];

		$group_id = isset( $_POST['group_id'] ) ? intval( $_POST['group_id'] ) : '';

        if( empty( $group_id ) ) {

            $response['message'] = __( 'group id not found', 'myrtle-learning-dashboard' );
			$response['status'] = 'false';

			echo json_encode( $response );
			wp_die();
        }

		$user_ids = mld_get_group_users( $group_id );

		if( empty( $user_ids ) && ! is_array( $user_ids ) ) {

			$response['message'] = __( 'user id not found', 'myrtle-learning-dashboard' );
			$response['status'] = 'false';

			echo json_encode( $response );
			wp_die();
		}

		ob_start();

		?>
		<option value=""><?php echo __( 'Select a User', 'myrtle-learning-dashboard' ); ?></option>
		<?php
		foreach( $user_ids as $user_id ) {

			$username = mld_get_username( $user_id );
			?>
			<option value="<?php echo $user_id;?>"><?php echo $username; ?></option>
			<?php
		}

		$content = ob_get_contents();
		ob_get_clean();

		$response['content'] = $content;
		$response['status'] = 'true';
		echo json_encode( $response );
		wp_die();
	}
}

Myrtle_Work::instance();