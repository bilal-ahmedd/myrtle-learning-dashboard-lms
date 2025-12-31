<?php
/**
 * Notification templates
 */

if( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class Myrtle_Staff_Template
 */
class Myrtle_Staff_Template {

	/**
	 * @var self
	 */
	private static $instance = null;

	/**
	 * @since 1.0
	 * @return $this
	 */
	public static function instance() {

		if ( is_null( self::$instance ) && ! ( self::$instance instanceof Myrtle_Staff_Template ) ) {
			self::$instance = new self;
			self::instance()->hooks();
			self::$instance->includes();
		}

		return self::$instance;
	}

	/**
	 * include files
	 */
	private function includes() {

		/**
		 * includes staff files
		 */
		$staff_file = MLD_INCLUDES_DIR . 'staff-module/mld-staff.php';

		if( file_exists( $staff_file ) ) {
			require_once $staff_file;
		}
	}

	/**
	 * Call hooks
	 *
	 * @return void
	 */
	public function hooks() {
		add_shortcode( 'teacher_form', [ $this, 'mld_teacher_registration_form' ] );
		add_shortcode( 'user_terms_condition', [ $this, 'mld_forms_terms_condition_btn' ] );
		add_shortcode( 'terms_condition_popup', [ $this, 'mld_terms_condition_popup' ] );
		add_shortcode( 'mld_staff', [ $this, 'mld_staff_shortcode' ] );
		add_shortcode( 'refrence_form', [ $this, 'mld_refrence_form_html' ] );
		add_shortcode( 'refrence_applicant_form', [ $this, 'mld_refrence_second_form' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'mld_staff_scripts' ] );
		add_action( 'wp_ajax_update_teacher_information', [ $this, 'mld_update_teacher_information' ] );
		add_action( 'wp_ajax_nopriv_update_teacher_information', [ $this, 'mld_update_teacher_information' ] );
		add_action( 'wp_ajax_update_new_subjects', [ $this, 'mld_update_new_subjects' ] );
		add_action( 'wp_ajax_nopriv_update_new_subjects', [ $this, 'mld_update_new_subjects' ] );
		add_action( 'user_register', [ $this, 'mld_user_register' ], 10, 1 );
		add_filter( 'allow_password_reset', [ $this, 'mld_resrict_user_to_reset_pass' ], 10, 2 );
		add_action( 'register_new_user', [ $this, 'mld_register_new_user' ], 10, 1 );
		add_action( 'wp_ajax_mld_update_reference_form', [ $this, 'mld_mld_update_reference_form' ] );
		add_action( 'wp_ajax_nopriv_mld_update_reference_form', [ $this, 'mld_mld_update_reference_form' ] );
		add_action( 'wp_ajax_display_course_leaders', [ $this, 'mld_display_course_leaders' ] );
		add_action( 'wp_ajax_display_full_detail', [ $this, 'mld_display_full_detail' ] );
		add_action( 'wp_ajax_send_email_to_admin', [ $this, 'mld_send_email_to_admin' ] );
		add_action( 'wp_ajax_update_teacher_availibity', [ $this, 'mld_update_teacher_availibity' ] );
		add_action( 'wp_ajax_update_teacher_unavailibity', [ $this, 'mld_update_teacher_unavailibity' ] );
		add_action( 'wp_ajax_update_teacher_dbs', [ $this, 'mld_update_teacher_dbs' ] );
		add_action( 'init', [ $this, 'mld_create_new_user_role' ] );
	}

	/**
	 * create new user role
	 */
	public function mld_create_new_user_role() {

		add_role(
			'pending_teacher',
			'pending teacher',
			array(
				'read'         => true,
				'upload_files' => true,
			)
		);

		add_role(
			'pending_student',
			'pending student',
			array(
				'read'         => true,
				'upload_files' => true,
			)
		);

		$student = get_role( 'student' );
	    $subscriber = get_role( 'subscriber' );

	    if ( $student && $subscriber ) {
	        // Pehle saari capabilities reset kar do (optional but recommended)
	        foreach ( $student->capabilities as $cap => $value ) {
	            $student->remove_cap( $cap );
	        }

	        // Ab subscriber wali capabilities assign karo
	        foreach ( $subscriber->capabilities as $cap => $value ) {
	            $student->add_cap( $cap, $value );
	        }
	    }
	}

	/**
	 * update teacher dbs
	 */
	public function mld_update_teacher_dbs() {

		$user_id = isset( $_POST['user_id'] ) ? $_POST['user_id'] : 0;
		$dbs = isset( $_POST['dbs'] ) ? $_POST['dbs'] : 'no';

		if( ! $user_id ) {
			wp_die();
		}

		$basic_info = get_user_meta( $user_id, 'mld-teacher-basic-info', true );
		if( is_array( $basic_info ) && ! empty( $basic_info ) ) {
			$basic_info['dbs'] = $dbs; 
		} else {
			$basic_info = [];
			$basic_info['dbs'] = $dbs;
		}

		update_user_meta( $user_id, 'mld-teacher-basic-info', $basic_info );

		wp_die();
	}

	/**
	 * update user unavailibity
	 */
	public function mld_update_teacher_unavailibity() {

		$user_id = isset( $_POST['user_id'] ) ? $_POST['user_id'] : 0;

		if( ! $user_id ) {
			wp_die();
		}

		$basic_info = get_user_meta( $user_id, 'mld-teacher-basic-info', true );
		if( is_array( $basic_info ) && ! empty( $basic_info ) ) {
			$basic_info['availability'] = 'no'; 
		} else {
			$basic_info = [];
			$basic_info['availability'] = 'no';
		}

		update_user_meta( $user_id, 'mld-teacher-basic-info', $basic_info );
		wp_die();
	}

	/**
	 * update avaibility 
	 */
	public function mld_update_teacher_availibity() {

		$user_id = isset( $_POST['user_id'] ) ? $_POST['user_id'] : 0;

		if( ! $user_id ) {
			wp_die();
		}

		$basic_info = get_user_meta( $user_id, 'mld-teacher-basic-info', true );
		if( is_array( $basic_info ) && ! empty( $basic_info ) ) {
			$basic_info['availability'] = 'yes'; 
		} else {
			$basic_info = [];
			$basic_info['availability'] = 'yes';
		}

		update_user_meta( $user_id, 'mld-teacher-basic-info', $basic_info );
		wp_die();
	}

	/**
	 * send email to admin
	 */
	public function mld_send_email_to_admin() {

		global $wpdb;

		$subject = isset( $_POST['subject'] ) ? $_POST['subject'] : '';
		$content = isset( $_POST['content'] ) ? $_POST['content'] : '';
		$user_id = isset( $_POST['user_id'] ) ? $_POST['user_id'] : '';
		$sender = isset( $_POST['email_sender'] ) ? $_POST['email_sender'] : '';

		if( ! $subject || ! $content || ! $user_id || ! $sender ) {
			wp_die();
		}

		$headers = array('Content-Type: text/html; charset=UTF-8');

		$uploads_dir = wp_upload_dir();
		$user_dir = isset( $uploads_dir['basedir'] ) ? $uploads_dir['basedir'] : '';
		$user_statement_name = get_user_meta( $user_id, 'mld_teacher_statement', true );
		
		if( $user_statement_name ) {
			$statement_file_address = $user_dir.'/mld-teachers-data/teacher_'.$user_id.'/'.$user_statement_name;
		} else {
			$statement_file_address = $user_dir.'/mld-teachers-data/teacher_'.$user_id.'/mld_teacher_statement.pdf';
		}
	
		// $admin_email = get_option('admin_email');
		$admin_email = 'hello@myrtlelearning.com';
		$content = $content.'<p>Sent By: '.$sender.'</p>';
		wp_mail( $admin_email, $subject, $content, $headers, $statement_file_address );
		wp_die();
	}

	/**
	 * get group leader full detail
	 */
	public function mld_display_full_detail() {

		$response = [];

		$user_id = isset( $_POST['user_id'] ) ? intval( $_POST['user_id'] ) : 0;
		
		if( ! $user_id ) {

			$response['content'] = __( 'data not found', 'myrtle-learning-dashboard' );
			$response['status'] = 'false';

			echo json_encode( $response );
			wp_die();
		}

		$content = $this->mld_get_group_leaders_full_detail( $user_id );

		$response['content'] = $content;
		$response['status'] = 'true';

		echo json_encode( $response );
		wp_die();
	}

	/**
	 * create a function to get group leader full detail html
	 */	
	public function mld_get_group_leaders_full_detail( $group_leader_id ) {

		global $wpdb;

		// $admin_email = get_option('admin_email');
		$admin_email = 'hello@myrtlelearning.com';
		$get_teacher_basic_data = get_user_meta( $group_leader_id, 'mld-teacher-basic-info', true );
		$table_name = $wpdb->prefix.'users';
		$teacher_name = $wpdb->get_results( $wpdb->prepare( "
			SELECT display_name FROM $table_name WHERE
			ID = %d", $group_leader_id ) );
		$teacher_name = isset( $teacher_name[0]->display_name ) ? ucwords( $teacher_name[0]->display_name ) : '';

		$years_of_teaching = isset( $get_teacher_basic_data['experience'] ) ? $get_teacher_basic_data['experience'] : ''; 
		$subjects = get_user_meta( $group_leader_id, 'mld_teacher_selected_subjects', true ); 
		$teacher_availibility = isset( $get_teacher_basic_data['availability'] ) ? $get_teacher_basic_data['availability'] : '';
		$teacher_dbs = isset( $get_teacher_basic_data['dbs'] ) ? $get_teacher_basic_data['dbs'] : 'no';
		$teacher_personal_statement = isset( $get_teacher_basic_data['personal_statement'] ) ? $get_teacher_basic_data['personal_statement'] : '';

		$dbs_check = '';

		if( 'yes' == $teacher_dbs ) {
			$dbs_check = 'checked="checked"';
		}

		$availibility_check = '';
		$unavailibility_check = '';

		if( 'yes' != $teacher_availibility ) {
			$unavailibility_check = 'checked';
		} else {
			$availibility_check = 'checked';
		}

		$teacher_college_detail = get_user_meta( $group_leader_id, 'mld-teacher-college-info', true );
		$teacher_uni_detail = get_user_meta( $group_leader_id, 'mld-teacher-uni-info', true );
		$teacher_experience = get_user_meta( $group_leader_id, 'mld-teacher-experience-info', true );

		ob_start();
		?>
		<?php 
		if( current_user_can( 'manage_options' ) ) {
			?>
			<input type="hidden" value="yes" class="mld-is-admin">
			<?php
		}
		?>
		<div class="mld-staff-back-btn">
			<span class="dashicons dashicons-arrow-left-alt mld-staff-icon"></span>
			<span class="mld-go-back"><?php echo __( 'Go Back', 'myrtle-learning-dashboard' ); ?></span>
		</div>
		<div class="mld-full-detail-wrapper">
			<div class="mld-staff-header-wrapper">
				<div class="mld-full-detail-head">
					<div class="mld-head-left">
						<div class="mld-group-leader-avatar">
							<?php 
							$user_avatar_url = get_user_meta( $group_leader_id, 'mld_user_avatar', true );

							if( ! $user_avatar_url ) {

								$table_name = $wpdb->prefix.'e_submissions_values';
								$user_email = $wpdb->get_var($wpdb->prepare(
									"SELECT user_email FROM {$wpdb->users} WHERE ID = %d",
									$group_leader_id
								) );
								$submission = $wpdb->get_results( "SELECT submission_id FROM $table_name WHERE value = '".$user_email."' LIMIT 1 " );
								$submission_id = isset( $submission[0]->submission_id ) ? intval( $submission[0]->submission_id ) : 0;
								$key = 'field_8427b4d';
								$submission_data = $wpdb->get_results( "SELECT submission.value as val FROM $table_name as submission WHERE submission.key = '".$key."' AND submission.submission_id = $submission_id " );
								$user_avatar_url = $submission_data[0]->val;
							}

							if( $user_avatar_url ) {
								?>
								<img src="<?php echo $user_avatar_url; ?>">
								<?php
							} else {

								$user_avatar_data = get_avatar_data( $group_leader_id );
								$avatar_url = isset( $user_avatar_data['url'] ) ? $user_avatar_data['url'] : '';
								?>
								<img src="<?php echo $avatar_url; ?>">
								<?php
							}
							?>
						</div>
						<div class="mld-group-leader-detail">
							<?php
							echo self::mld_get_user_basic_html( 'Name', $teacher_name );
							echo self::mld_get_user_basic_html( 'Years of Teaching', $years_of_teaching );
							echo self::mld_get_user_basic_html( 'Subjects', $subjects );
							?>
						</div>
						<div class="mld-clear-both"></div>
					</div>
					<div class="mld-head-right">
						<div class="mld-availability">
							<span class="mld-input-border">
								<input type="radio" <?php echo $availibility_check; ?> data-user_id="<?php echo $group_leader_id; ?>">
							</span>
							<label><?php echo __( 'Available', 'myrtle-learning-dashboard' ); ?></label>
						</div>
						<div class="mld-unavailability">
							<span class="mld-input-border">
								<input type="radio" <?php echo $unavailibility_check; ?> data-user_id="<?php echo $group_leader_id; ?>">
							</span>
							<label><?php echo __( 'Unavailable', 'myrtle-learning-dashboard' ); ?></label>
						</div>
						<div class="mld-dbs">
							<div class="dbs-checkbox-label">
								<label><?php echo __( 'DBS', 'myrtle-learning-dashboard' ); ?></label>
							</div>
							<div class="mld-dbs-checkbox">
								<input class='dbs dbs-checkbox-cls' id='dbs-checkbox-id' type="checkbox" <?php echo $dbs_check; ?> data-user_id="<?php echo $group_leader_id; ?>">
								<label class='dbs-btn' for='dbs-checkbox-id'></label>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="mld-group-leader-statement">
				<button class="mld-personal-statement-btn">
					<?php echo __( 'Personal Statement', 'myrtle-learning-dashboard' ); ?>
				</button>
				<div class="mld-personal-statement-content">
					<?php
					echo $teacher_personal_statement;
					?>
				</div>
			</div>
			<div class="mld-group-leader-educational-details">
				<button class="mld-educational-details-btn">
					<?php echo __( 'Educational Details', 'myrtle-learning-dashboard' ); ?>
				</button>
				<table>
					<thead>
						<tr>
							<th><?php echo __( 'Date', 'myrtle-learning-dashboard' ); ?></th>
							<th><?php echo __( 'College / A Level', 'myrtle-learning-dashboard' ); ?></th>
							<th><?php echo __( 'Courses / Subsjecs', 'myrtle-learning-dashboard' ); ?></th>
							<th><?php echo __( 'Staus (Pass/Fail/Pending)', 'myrtle-learning-dashboard' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php 
						if( is_array( $teacher_college_detail ) && ! empty( $teacher_college_detail ) ) {

							$s_no = 0;
							foreach( $teacher_college_detail as $key => $college_detail ) {

								$t_c_data = isset( $teacher_college_detail[$s_no] ) ? $teacher_college_detail[$s_no] : [];
								?>
								<tr>
									<?php
									if( is_array( $t_c_data ) && ! empty( $t_c_data ) ) {

										foreach( $t_c_data as $data ) {
											?>
											<td><?php echo $data; ?></td>
											<?php
										}
									}
									?>
								</tr>
								<?php
								$s_no++;
							}
						}
						?>
					</tbody>
				</table>
				<table>
					<thead>
						<tr>
							<th><?php echo __( 'Date', 'myrtle-learning-dashboard' ); ?></th>
							<th><?php echo __( 'University', 'myrtle-learning-dashboard' ); ?></th>
							<th><?php echo __( 'Subjects', 'myrtle-learning-dashboard' ); ?></th>
							<th><?php echo __( 'Qualification (Degree/Masters/Doctorate)', 'myrtle-learning-dashboard' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						if( is_array( $teacher_uni_detail ) && ! empty( $teacher_uni_detail ) ) {

							$u_no = 0;
							foreach( $teacher_uni_detail as $key => $uni_detail ) {

								$teacher_uni_data = isset( $teacher_uni_detail[$u_no] ) ? $teacher_uni_detail[$u_no] : []; 
								?>
								<tr>
									<?php
									if( is_array( $teacher_uni_data ) && ! empty( $teacher_uni_data ) ) {

										foreach( $teacher_uni_data as $key => $uni_data ) {
											?>
											<td><?php echo $uni_data; ?></td>
											<?php
										}
									}
									?>
								</tr>
								<?php
								$u_no++;
							}
						}
						?>
					</tbody>
				</table>
			</div>
			<div class="mld-group-leader-experience-details">
				<button><?php echo __( 'Experience', 'myrtle-learning-dashboard' ); ?></button>
				<table>
					<thead>
						<tr>
							<td><?php echo __( 'Subject Taught', 'myrtle-learning-dashboard' ); ?></td>
							<td><?php echo __( 'Level', 'myrtle-learning-dashboard' ); ?></td>
							<td><?php echo __( 'Number of Students', 'myrtle-learning-dashboard' ); ?></td>
							<td><?php echo __( 'Percentage Pass', 'myrtle-learning-dashboard' ); ?></td>
						</tr>
					</thead>
					<tbody>
						<?php
						if( is_array( $teacher_experience ) && ! empty( $teacher_experience ) ) {

							$no = 0;
							foreach( $teacher_experience as $key => $teacher_exper ) {

								$t_data = isset( $teacher_experience[$no] ) ? $teacher_experience[$no] : [];
								?>
								<tr>
									<?php
									if( is_array( $t_data ) && ! empty( $t_data ) ) {

										foreach( $t_data as $data ) {

											if( ! $data ) {
												continue;
											}

											if ( ! strpos( $data, '%' ) !== false) {
												$data = str_replace( '-', ' ', $data );
											}
											?>
											<td><?php echo $data; ?></td>
											<?php
										}
									}
									?>
								</tr>
								<?php
								$no++;
							}
						}
						?>
					</tbody>
				</table>
			</div>
			<?php
			if( current_user_can( 'manage_options' ) ) {
				?>
				<div class="mld-download-staff-profile">
					<a href="<?php echo get_permalink().'?mld_group_leader_id='.$group_leader_id.''; ?>" target="_blank"><?php echo __( 'Download', 'myrtle-learning-dashboard' );  ?></a>
				</div>
				<?php
			}

			$user_capability = mld_user_capability( get_current_user_id() );

			if( in_array( 'student' , $user_capability ) || in_array( 'employer' , $user_capability ) ) {

				?>
				<button id="mld-teacher-contact-popup">
					<?php echo __( 'Proceed', 'myrtle-learning-dashboard' ); ?>
				</button>
				<div class="mld-staff-popup">
					<div class="mld-staff-popup-content">
						<div class="mld-staff-popup-close">
							<span class="dashicons dashicons-no"></span>
						</div>

						<div class="mld-email-content-wrapper">
							<h5><?php echo __( 'Recipient : '.$admin_email.'', 'myrtle-learning-dashboard' ); ?></h5>
							<label><?php echo __( 'Sender Email', 'myrtle-learning-dashboard' ); ?></label>
							<input type="text" class="mld-staff-popup-e-sender" placeholder="<?php echo __( 'Enter Sender Email', 'myrtle-learning-dashboard' ); ?>">
							<label><?php echo __( 'Email Subject', 'myrtle-learning-dashboard' ); ?></label>
							<input type="text" class="mld-staff-popup-e-sub" placeholder="<?php echo __( 'Enter Email Subject', 'myrtle-learning-dashboard' ); ?>">
							<label><?php echo __( 'Email Content', 'myrtle-learning-dashboard' ); ?></label>
							<textarea class="mld-staff-popup-e-content" rows="4" placeholder="<?php echo __( 'Enter Email Content', 'myrtle-learning-dashboard' ); ?>"></textarea>
							<div class="mld-sent-btn-wrapper">
								<input type="button" class="mlf-staff-email-btn" value="<?php echo __( 'Send to Admin', 'myrtle-learning-dashboard' ); ?>" data-user_id="<?php echo $group_leader_id; ?>">
							</div>
						</div>
					</div>
				</div>
				<?php
			}

			?>
		</div>
		<?php

		$content = ob_get_contents();
		ob_get_clean();
		return $content;
	}

	/**
	 * create a function to get user basic detail
	 */
	public static function mld_get_user_basic_html( $label, $val ) {

		ob_start();
		$unique_label_class = 'mld_'.str_replace( ' ', '_', $label );
		$unique_detail_class = 'mld_detail_'.str_replace( ' ', '_', $label );
		?>
		<div class="mld-name">
			<div class="mld-name-label <?php echo $unique_label_class; ?>"><?php echo $label.' : '; ?></div>
			<div class="mld-name-detail <?php echo $unique_detail_class; ?>">
				<?php
				if( is_array( $val ) && ! empty( $val ) ) {
					echo str_replace( '-', ' ', implode( ',', $val ) );
				} else {
					echo ucwords( $val );
				} 
				?>	
			</div>
		</div>
		<?php

		$content = ob_get_contents();
		ob_get_clean();
		return $content;
	}

	/**
	 * display course leaders
	 */
	public function mld_display_course_leaders() {

		global $wpdb;
		$responce = [];
		$course_id = isset( $_POST['course_id'] ) ? $_POST['course_id'] : 0;
		$is_course_id = isset( $_POST['is_course_id'] ) ? $_POST['is_course_id'] : 0;
		$g_leader_id = [];
		
		if( 'true' == $is_course_id ) {
			
			$course_groups = mld_get_course_groups( $course_id );
			
			if( is_array( $course_groups ) && ! empty( $course_groups ) ) {
				foreach( $course_groups as $group_id ) {

					$group_leaders = mld_get_group_leaders( $group_id );

					if( is_array( $group_leaders ) && ! empty( $group_leaders ) ) {
						
						foreach( $group_leaders as $group_leader_id ) {
							$g_leader_id[] = $group_leader_id;
						}
					}
				}
			}
		} else {
			
			$table_name = $wpdb->prefix.'usermeta';
			$user_data = $wpdb->get_results("
				SELECT user_id, meta_value FROM $table_name
				WHERE meta_key = 'mld_teacher_selected_subjects'
				");
			if( is_array( $user_data ) && ! empty( $user_data ) ) {
				foreach( $user_data as $data ) {
					$subjects_array = unserialize( $data->meta_value );
					if( in_array( $course_id, $subjects_array ) ) {
						$g_leader_id[] = isset( $data->user_id ) ? intval( $data->user_id ) : 0;
					}
				}
			}
		}

		ob_start();
		if( is_array( $g_leader_id ) && ! empty( $g_leader_id ) ) {
			foreach( $g_leader_id as $id ) {
				echo self::mld_get_group_leader_html( $id );
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
	 * create a function to get group leader html
	 */
	public static function mld_get_group_leader_html( $l_id ) {

		global $wpdb;

		$teacher_basic_info = get_user_meta( $l_id, 'mld-teacher-basic-info', true );
		$teacher_subject = get_user_meta( $l_id, 'mld_teacher_selected_subjects', true );
		$experience = isset( $teacher_basic_info['experience'] ) ? $teacher_basic_info['experience'] : '';
		$group_leader_courses = learndash_get_group_leader_groups_courses( $l_id );
		$query = $wpdb->prepare(
			"SELECT user_login FROM {$wpdb->prefix}users WHERE ID = %d",
			$l_id
		);
		$mld_username = ucwords( $wpdb->get_var($query) );
		$mld_inner_id = strtolower( str_replace(['-', '.', '_', '@', ' '], '', $mld_username ) );
		$teacher_pass_rate = get_user_meta( $l_id, 'mld-teacher-experience-info', true );

		ob_start();
		?>
		<div class="mld-g-leader-inner" id="<?php echo $mld_inner_id; ?>">
			
			<div class="mld-staff-inner-content">
				<div class="mld-staff-box-popup">
					<div class="mld-staff-popup-name">
						<h4 class="staff-popup-name"><?php echo $mld_username; ?></h4>
					</div>
					<div class="mld-staff-popup-courses">
						<?php
						if( is_array( $teacher_subject ) && ! empty( $teacher_subject ) ) {
							echo str_replace( '-', ' ', implode( '|', $teacher_subject ) );
						}
						?>
					</div>
					<div class="mld-staff-popup-experience">
						<h4 class="mld-experience-heading"><?php echo __( 'Experience', 'myrtle-learning-dashboard' ); ?></h4>
						<?php 
						if( $experience ) {
							?>
							<div class="mld-experience-text"><?php echo $experience.' Years'; ?></div>
							<?php
						}
						?>
					</div>
					<div class="mld-staff-popup-pass-rate">
						<h4 class="mld-pass-rate-heading"><?php echo __( 'Pass Rate', 'myrtle-learning-dashboard' ); ?></h4>
						<?php 
						if( $teacher_pass_rate ) {
							?>
							<div class="mld-passrate-data">
								<?php 
								if( is_array( $teacher_pass_rate ) && ! empty( $teacher_pass_rate ) ) {
									?>
									<table class="mld-popup-table">
										<tr>
											<th><?php echo __( 'Subjects', 'myrtle-learning-dashboard' ); ?></th>
											<th><?php echo __( 'Pass Rate', 'myrtle-learning-dashboard' ); ?></th>
										</tr>
									<?php
									foreach( $teacher_pass_rate as $pass_rate ) {
										
										$subjects = isset( $pass_rate[0] ) ? str_replace( '-', ' ', $pass_rate[0] ) : '';
										$passing_rate = isset( $pass_rate[3] ) ? $pass_rate[3] : '';
										?>
										<tr>
											<td><?php echo $subjects; ?></td>
											<td><?php echo $passing_rate; ?></td>
										</tr>
										<?php
									}
									?>
									</table>
									<?php
								}
								?>
							</div>
							<?php
						}
						?>
					</div>
				</div>
			</div>
			<div class="mld-view-more-info">
				<span class="dashicons dashicons-visibility mld-view-more-details"></span>
			</div>
			<div class="mld-g-leader-photo">
				<?php 
				$user_avatar_url = get_user_meta( $l_id, 'mld_user_avatar', true );

				if( ! $user_avatar_url ) {

					$table_name = $wpdb->prefix.'e_submissions_values';
					$user_email = $wpdb->get_var($wpdb->prepare(
						"SELECT user_email FROM {$wpdb->users} WHERE ID = %d",
						$l_id
					));
					$submission = $wpdb->get_results( "SELECT submission_id FROM $table_name WHERE value = '".$user_email."' LIMIT 1 " );
					$submission_id = isset( $submission[0]->submission_id ) ? intval( $submission[0]->submission_id ) : 0;
					$key = 'field_8427b4d';
					$submission_data = $wpdb->get_results( "SELECT submission.value as val FROM $table_name as submission WHERE submission.key = '".$key."' AND submission.submission_id = $submission_id " );
					$user_avatar_url = isset( $submission_data[0]->val ) ? $submission_data[0]->val : '';
				}

				if( $user_avatar_url ) {
					?>
					<img src="<?php echo $user_avatar_url; ?>" class="mld_avatar_url">	
					<?php
				} else {

					$user_avatar_data = get_avatar_data( $l_id );
					$avatar_url = isset( $user_avatar_data['url'] ) ? $user_avatar_data['url'] : '';
					?>
					<img src="<?php echo $avatar_url; ?>" class="mld_no_avatar_url">
					<?php
				}
				?>
			</div>
			<div class="mld-g-leader-name">
				<?php echo $mld_username; ?>
			</div>
			<div class="mld-g-leader-subjects">
				<?php 
				if( is_array( $teacher_subject ) && ! empty( $teacher_subject ) ) {

					?>
					<div class="mld-t-sub">
						<?php echo str_replace( '-', ' ', implode( '|', $teacher_subject ) ); ?>
					</div>
					<?php
				}
				?>
			</div>
			<div class="mld-g-leader-btn">
				<button class="mld-group-leader-detail" data-user-id="<?php echo $l_id; ?>">
					<?php echo __( 'See Full Details', 'myrtle-learning-dashboard' ); ?>
					<img src="<?php echo MLD_ASSETS_URL.'images/spinner.gif' ?>" class="mld-staff-loader">
				</button>
			</div>
		</div>
		<?php

		$content = ob_get_contents();
		ob_get_clean();
		return $content;
	}

	/**
	 * create a function to get group leaders
	 */
	public static function mld_get_group_leaders() {

		global $wpdb;

		$group_leaders = get_users( 
			[
				'role'   => 'group_leader',
				'fields' => 'ID'
			]
		);

		if( is_array( $group_leaders ) && ! empty( $group_leaders ) ) {
			?>
			<div class="mld-group-leaders-wrapper">
			<?php
			foreach( $group_leaders as $group_leader ) {
				echo self::mld_get_group_leader_html( $group_leader );
			}
			?>
			</div>
			<?php
		}
	}

	/**
	 * create a shortcode to display the staff data
	 */
	public function mld_staff_shortcode() {

		$args = array(
			'numberposts' 	=> -1,
			'post_type'   	=> 'sfwd-courses',
			'post_status'	=> 'publish',
			'fields'		=> 'ids'
		);

		$ld_courses = get_posts( $args );

		$subjects_array = [
			'Physics' => 'Physics',
			'Chemistry' => 'Chemistry',
			'Biology' => 'Biology',
			'Mathematics' => 'Mathematics',
			'English-Language' => 'English Language',
			'English-Literature' => 'English Literature',
			'Computer-Science' => 'Computer Science',
			'Design-and-Technology' => 'Design and Technology',
			'Geography' => 'Geography',
			'Business-Studies' => 'Business Studies',
			'Economics' => 'Economics',
			'Psychology' => 'Psychology',
			'French' => 'French',
			'Spanish' => 'Spanish',
			'Law' => 'Law',
			'Sociology' => 'Sociology',
			'History	' => 'History	',
			'Latin' => 'Latin',
			'Drama' => 'Drama',
			'Food-Technology' => 'Food Technology',
			'11Plus-Mathematics' => '11Plus Mathematics',
			'11Plus-Verbal-Reasoning' => '11Plus Verbal Reasoning',
			'11Plus-Non-Verbal-Reasoning' => '11Plus Non Verbal Reasoning',
			'11Plus-English' => '11Plus English',	
		];

		$ld_courses = array_merge( $ld_courses, $subjects_array );

		ob_start();
		?>
		<div class="mld-staff-wrapper">
			<div class="mls-staff-search-weapper">
				<input type="text" placeholder="<?php echo __( 'Search Here', 'myrtle-learning-dashboard' ); ?>" class="mld-search-teacher">
				<button type="submit" class="mld-staff-search"><i class="fa fa-search"></i></button>
			</div>
			<div class="mld-clear-both"></div>
			<div class="mld-peoceed-btn-wrapper">
				<div class="mld-staff-course-dropdown">
					<label><?php echo __( 'Course', 'myrtle-learning-dashboard' ); ?></label>
					<p>
						<select class="mld-staff-courses">
							<option value=""><?php echo __( 'Choose Course Here', 'myrtle-learning-dashboard' ); ?></option>
							<?php
							if( is_array( $ld_courses ) && ! empty( $ld_courses ) ) {
								foreach( $ld_courses as $key => $course ) {

									$is_course_id = is_int( $course );
									if( $is_course_id ) {

										?>
										<option value="<?php echo $course; ?>"><?php  echo get_the_title( $course ); ?></option>
										<?php
									} else {
										?>
										<option value="<?php echo $key; ?>"><?php echo ucwords( str_replace( '-', ' ', $course ) ); ?></option>
										<?php
									} 
								}
							}
							?>
						</select>
					</p>
				</div>
				<div class="mld-staff-proceed-btn">
					<button>
						<?php echo __( 'Proceed', 'myrtle-learning-dashboard' ); ?>
						<span class="dashicons dashicons-arrow-right-alt"></span>
						<img src="<?php echo MLD_ASSETS_URL.'images/spinner.gif' ?>" class="mld-staff-loader">
					</button>
				</div>
			</div>

			<div class="mld-staff-content">
				<?php 
				echo self::mld_get_group_leaders();
				?>
			</div>
		</div>
		<div class="mld-teacher-full-report"></div>
		<?php
		$content = ob_get_contents();
		ob_get_clean();
		
		return $content;
	}

	/**
	 * update refrence form 
	 */
	public function mld_mld_update_reference_form() {
		
		global $wpdb;

		$refrence_table = $wpdb->prefix . 'mld_refrences';
		$user_id = isset( $_POST['user_id'] ) ? intval( $_POST['user_id'] ) : '';
		$email = isset( $_POST['email'] ) ? $_POST['email'] : '';
		$applicant_name = isset( $_POST['applicant_name'] ) ? trim( $_POST['applicant_name'] ) : '';
		$post_applied = isset( $_POST['post_applied'] ) ? trim( $_POST['post_applied'] ) : '';
		$job_title = isset( $_POST['job_title'] ) ? trim( $_POST['job_title'] ) : '';
		$time_period = isset( $_POST['time_period'] ) ? trim( $_POST['time_period'] ) : '';
		$applicant_capacity = isset( $_POST['applicant_capacity'] ) ? trim( $_POST['applicant_capacity'] ) : '';
		$organization_title = isset( $_POST['organization_title'] ) ? trim( $_POST['organization_title'] ) : '';
		$job_duties = isset( $_POST['job_duties'] ) ? trim( $_POST['job_duties'] ) : '';
		$leaving_reason = isset( $_POST['leaving_reason'] ) ? trim( $_POST['leaving_reason'] ) : '';
		$applicant_specification = isset( $_POST['applicant_specification'] ) ? trim( $_POST['applicant_specification'] ) : '';
		$applicant_work_with_children_answer = isset( $_POST['applicant_work_with_children_answer'] ) ? trim( $_POST['applicant_work_with_children_answer'] ) : '';
		$applicant_further_comment = isset( $_POST['applicant_further_comment'] ) ? trim( $_POST['applicant_further_comment'] ) : '';
		$experience_data = isset( $_POST['experience_data'] ) ? trim( $_POST['experience_data'] ) : '';
		$start_date = isset( $_POST['start_date'] ) ? trim( $_POST['start_date'] ) : '';
		$end_date = isset( $_POST['end_date'] ) ? trim( $_POST['end_date'] ) : '';
		$salary = isset( $_POST['salary'] ) ? trim( $_POST['salary'] ) : '';
		$trust_worthy_yes = isset( $_POST['trust_worthy_yes'] ) ? trim( $_POST['trust_worthy_yes'] ) : '';
		$duty_care_yes = isset( $_POST['duty_care_yes'] ) ? trim( $_POST['duty_care_yes'] ) : '';
		$disciplinary_warnings_yes = isset( $_POST['disciplinary_warnings_yes'] ) ? trim( $_POST['disciplinary_warnings_yes'] ) : '';
		$re_employ_yes = isset( $_POST['re_employ_yes'] ) ? trim( $_POST['re_employ_yes'] ) : '';
		$job_describe_yes = isset( $_POST['job_describe_yes'] ) ? trim( $_POST['job_describe_yes'] ) : '';
		$work_with_children_yes = isset( $_POST['work_with_children_yes'] ) ? trim( $_POST['work_with_children_yes'] ) : '';
		$quality_of_work = isset( $_POST['quality_of_work'] ) ? json_decode( str_replace( '\\', '', $_POST['quality_of_work'] ) ) : '';
		$quantity_of_work = isset( $_POST['quantity_of_work'] ) ? json_decode( str_replace( '\\', '', $_POST['quantity_of_work'] ) ) : '';
		$job_dedication = isset( $_POST['job_dedication'] ) ? json_decode( str_replace( '\\', '', $_POST['job_dedication'] ) ) : '';
		$ability_of_work = isset( $_POST['ability_of_work'] ) ? json_decode( str_replace( '\\', '', $_POST['ability_of_work'] ) ) : '';
		$working_relationship = isset( $_POST['working_relationship'] ) ? json_decode( str_replace( '\\', '', $_POST['working_relationship'] ) ) : '';
		$time_keeping = isset( $_POST['time_keeping'] ) ? json_decode( str_replace( '\\', '', $_POST['time_keeping'] ) ) : '';
		$org_name = isset( $_POST['org_name'] ) ? trim( $_POST['org_name'] ) : '';
		$org_date = isset( $_POST['org_date'] ) ? trim( $_POST['org_date'] ) : '';
		$org_telephone = isset( $_POST['org_telephone'] ) ? trim( $_POST['org_telephone'] ) : '';
		$sign_name = isset( $_FILES['sign']['name'] ) ? $_FILES['sign']['name'] : '';
		$stump_name = isset( $_FILES['stump']['name'] ) ? $_FILES['stump']['name'] : '';
		$refrence_data = [
			'applicant_name' 						=> $applicant_name,
			'post_applied' 							=> $post_applied,
			'job_title' 							=> $job_title,
			'time_period' 							=> $time_period,
			'applicant_capacity' 					=> $applicant_capacity,
			'organization_title' 					=> $organization_title,
			'job_duties' 							=> $job_duties,
			'leaving_reason' 						=> $leaving_reason,
			'applicant_specification' 				=> $applicant_specification,
			'applicant_work_with_children_answer' 	=> $applicant_work_with_children_answer,
			'applicant_further_comment' 			=> $applicant_further_comment,
			'experience_data' 						=> $experience_data,
			'start_date' 							=> $start_date,
			'end_date' 								=> $end_date,
			'salary' 								=> $salary,
			'trust_worthy_yes' 						=> $trust_worthy_yes,
			'duty_care_yes' 						=> $duty_care_yes,
			'disciplinary_warnings_yes' 			=> $disciplinary_warnings_yes,
			're_employ_yes' 						=> $re_employ_yes,
			'job_describe_yes' 						=> $job_describe_yes,
			'work_with_children_yes' 				=> $work_with_children_yes,
			'quality_of_work' 						=> $quality_of_work,
			'quantity_of_work' 						=> $quantity_of_work,
			'job_dedication' 						=> $job_dedication,
			'ability_of_work' 						=> $ability_of_work,
			'working_relationship' 					=> $working_relationship,
			'time_keeping' 							=> $time_keeping,
			'org_name' 								=> $org_name,
			'org_date' 								=> $org_date,
			'org_telephone' 						=> $org_telephone
		];

		$data = [
			'ref_email' 	=> $email,
			'user_id'		=> $user_id,
			'refrence_data'	=> json_encode( $refrence_data )
		];

		$reference_table = $wpdb->base_prefix . 'mld_refrences';
		$insert_result = $wpdb->insert( $reference_table, $data );
		
		$query = $wpdb->prepare( "SELECT ID FROM $refrence_table WHERE ref_email=%s AND user_id=%d", $email, $user_id );
		$applicant_refrence_data = $wpdb->get_results( $query );
		$auto_id = isset( $applicant_refrence_data[0]->ID ) ? intval( $applicant_refrence_data[0]->ID ) : '';
		
		if( $auto_id ) {

			$upload_dir = wp_upload_dir();
			if( ! empty( $upload_dir['basedir'] ) ) {

				$sign_upload_dir = $upload_dir['basedir'].'/mld_references/user_'.$auto_id.'/sign';
				$stump_upload_dir = $upload_dir['basedir'].'/mld_references/user_'.$auto_id.'/stump';
				
				if ( ! file_exists( $sign_upload_dir ) ) {
					wp_mkdir_p( $sign_upload_dir );
				}

				if ( ! file_exists( $stump_upload_dir ) ) {
					wp_mkdir_p( $stump_upload_dir );
				}

				move_uploaded_file( $_FILES['stump']['tmp_name'], $stump_upload_dir . '/' . $stump_name );
				move_uploaded_file( $_FILES['sign']['tmp_name'], $sign_upload_dir . '/' . $sign_name );
			}	
		}
		exit;
	}

	/**
	 * refrence second form
	 */
	public function mld_refrence_second_form() {

		global $wpdb;
		
		$refrence_table = $wpdb->prefix . 'mld_refrences';
		$user_id = isset( $_GET['userId'] ) ? $_GET['userId'] : '';
		$ref_email = isset( $_GET['ref_email'] ) ? $_GET['ref_email'] : '';
		
		ob_start();
		?>
		<input type="hidden" class="mld-user-id" value="<?php echo $user_id; ?>">
		<input type="hidden" class="mld-ref-email" value="<?php echo $ref_email; ?>">
		<div class="mld-main-wrapper">
			<div class="mld-form-applicent">
				<div class="mld-applicent-label mld-min-height">
					<p><?php echo __( 'Name of Applicant', 'myrtle-learning-dashboard' ); ?></p>
				</div>
				<div class="mld-applicent-data mld-min-height" contenteditable="true"></div>
				<div class="mld-clear-both"></div>
			</div>
			<div class="mld-form-applicent">
				<div class="mld-applicent-label mld-min-height">
					<p><?php echo __( 'Post Applied for', 'myrtle-learning-dashboard' ); ?></p>
				</div>
				<div class="mld-applicent-data mld-min-height" contenteditable="true"></div>
				<div class="mld-clear-both"></div>
			</div>
			<div class="mld-applicent-experience">
				<div class="mld-experience-label mld-min-height">
					<p><?php echo __( 'Did the applicant work for your organisation?', 'myrtle-learning-dashboard' ); ?></p>
				</div>
				<div class="mld-experience-data mld-min-height">
					<p>
						<label><?php echo __( 'Yes', 'myrtle-learning-dashboard' ); ?></label>
						<input type="checkbox" class="mld-experience-yes">
						<label><?php echo __( 'No', 'myrtle-learning-dashboard' ); ?></label>
						<input type="checkbox" class="mld-experience-no">
					</p>
				</div>
				<div class="mld-clear-both"></div>
			</div>
			<div class="mld-applicent-information">
				<div class="mld-information-label mld-min-height">
					<p><?php echo __( "If yes, what were the applicant's start and leaving dates?", 'myrtle-learning-dashboard' ); ?></p>
				</div>
			</div>
			<div class="mld-applicent-information-data">
				<div class="mld-start-date mld-min-height">
					<label><?php echo __( 'Start date:', 'myrtle-learning-dashboard' ); ?></label>
					<input type="date">
				</div>
				<div class="mld-end-date mld-min-height">
					<label><?php echo __( 'Leaving date:', 'myrtle-learning-dashboard' ); ?></label>
					<input type="date">
				</div>
				<div class="mld-salary mld-min-height">
					<label><?php echo __( 'Salary / Grade:', 'myrtle-learning-dashboard' ); ?></label>
					<input type="text">
				</div>				
			</div>
			<div class="mld-job-title">
				<div class="mld-job-title-label mld-min-height">
					<p><?php echo __( "What is your job title?", 'myrtle-learning-dashboard' ); ?></p>
				</div>
				<div class="mld-job-title-data mld-min-height" contenteditable="true"></div>
			</div>
			<div class="mld-time-period">
				<div class="mld-time-period-label mld-min-height">
					<p><?php echo __( "How long did you work with the applicant?", 'myrtle-learning-dashboard' ); ?></p>
				</div>
				<div class="mld-time-period-data mld-min-height" contenteditable="true"></div>
			</div>
			<div class="mld-applicent-capacity">
				<div class="mld-applicent-capacity-label mld-min-height">
					<p><?php echo __( "In what capacity do you know the applicant? E.g. as a colleague/as an employee reporting to you/other (please specify)", 'myrtle-learning-dashboard' ); ?></p>
				</div>
				<div class="mld-applicent-capacity-data mld-min-height" contenteditable="true"></div>
			</div>
			<div class="mld-organization-title">
				<div class="mld-organization-title-label mld-min-height">
					<p><?php echo __( "What was the applicant's job title with your organisation?", 'myrtle-learning-dashboard' ); ?></p>
				</div>
				<div class="mld-organization-title-data mld-min-height" contenteditable="true"></div>
			</div>
			<div class="mld-job-duties">
				<div class="mld-job-duties-label mld-min-height">
					<p><?php echo __( "What were the applicant's main job duties?", 'myrtle-learning-dashboard' ); ?></p>
				</div>
				<div class="mld-job-duties-data mld-min-height" contenteditable="true"></div>
			</div>
			<div class="mld-applicent-work-behaviour">
				<div class="mld-applicent-work-behaviour-label mld-min-height">
					<p><?php echo __( "What is your assessment of the following elements in relation to the applicant?", 'myrtle-learning-dashboard' ); ?></p>
				</div>
				<div class="mld-applicent-work-bahaviour-data">
					<table>
						<thead>
							<tr>
								<th class="mld-label"></th>
								<th class="mld-label"><?php echo __( 'Excellent', 'myrtle-learning-dashboard' ); ?></th>
								<th class="mld-label"><?php echo __( 'Good', 'myrtle-learning-dashboard' ); ?></th>
								<th class="mld-label"><?php echo __( 'Fair', 'myrtle-learning-dashboard' ); ?></th>
								<th class="mld-label"><?php echo __( 'Poor', 'myrtle-learning-dashboard' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<tr class="mld-quality-of-work">
								<td class="mld-label"><?php echo __( 'Quality of work', 'myrtle-learning-dashboard' ); ?></td>
								<td contenteditable="true" class="quality-answer">
									<input type="checkbox" class="mld-general-check mld-general">
									<input type="hidden" value="excellent" class="quality-answer-checkbox">
								</td>
								<td contenteditable="true" class="quality-answer">
									<input type="checkbox" class="mld-general-check mld-general">
									<input type="hidden" value="good" class="quality-answer-checkbox">
								</td>
								<td contenteditable="true" class="quality-answer">
									<input type="checkbox" class="mld-general-check mld-general">
									<input type="hidden" value="fair" class="quality-answer-checkbox">
								</td>
								<td contenteditable="true" class="quality-answer">
									<input type="checkbox" class="mld-general-check mld-general">
									<input type="hidden" value="poor" class="quality-answer-checkbox">
								</td>
							</tr>
							<tr class="mld-quantity-of-work">
								<td class="mld-label"><?php echo __( 'Quantity of work', 'myrtle-learning-dashboard' ); ?></td>
								<td contenteditable="true" class="quantity-work">
									<input type="checkbox" class="mld-general-check mld-general">
									<input type="hidden" value="excellent" class="quantity-work-checkbox">
								</td>
								<td contenteditable="true" class="quantity-work">
									<input type="checkbox" class="mld-general-check mld-general">
									<input type="hidden" value="good" class="quantity-work-checkbox">
								</td>
								<td contenteditable="true" class="quantity-work">
									<input type="checkbox" class="mld-general-check mld-general">
									<input type="hidden" value="fair" class="quantity-work-checkbox">
								</td>
								<td contenteditable="true" class="quantity-work">
									<input type="checkbox" class="mld-general-check mld-general">
									<input type="hidden" value="poor" class="quantity-work-checkbox">
								</td>
							</tr>
							<tr class="mld-job-dedication">
								<td class="mld-label"><?php echo __( 'Dedication to the job', 'myrtle-learning-dashboard' ); ?></td>
								<td contenteditable="true" class="job-dedication">
									<input type="checkbox" class="mld-general-check mld-general">
									<input type="hidden" value="excellent" class="job-dedication-checkbox">
								</td>
								<td contenteditable="true" class="job-dedication">
									<input type="checkbox" class="mld-general-check mld-general">
									<input type="hidden" value="good" class="job-dedication-checkbox">
								</td>
								<td contenteditable="true" class="job-dedication">
									<input type="checkbox" class="mld-general-check mld-general">
									<input type="hidden" value="fair" class="job-dedication-checkbox">
								</td>
								<td contenteditable="true" class="job-dedication">
									<input type="checkbox" class="mld-general-check mld-general">
									<input type="hidden" value="poor" class="job-dedication-checkbox">
								</td>
							</tr>
							<tr class="mld-ability-of-work">
								<td class="mld-label"><?php echo __( 'Ability to work without supervision', 'myrtle-learning-dashboard' ); ?></td>
								<td contenteditable="true" class="ability-of-work">
									<input type="checkbox" class="mld-general-check mld-general">
									<input type="hidden" value="excellent" class="ability-of-work-checkbox">
								</td>
								<td contenteditable="true" class="ability-of-work">
									<input type="checkbox" class="mld-general-check mld-general">
									<input type="hidden" value="good" class="ability-of-work-checkbox">
								</td>
								<td contenteditable="true" class="ability-of-work">
									<input type="checkbox" class="mld-general-check mld-general">
									<input type="hidden" value="fair" class="ability-of-work-checkbox">
								</td>
								<td contenteditable="true" class="ability-of-work">
									<input type="checkbox" class="mld-general-check mld-general">
									<input type="hidden" value="poor" class="ability-of-work-checkbox">
								</td>
							</tr>
							<tr class="mld-working-relationship">
								<td class="mld-label"><?php echo __( 'Working relationships', 'myrtle-learning-dashboard' ); ?></td>
								<td contenteditable="true" class="working-relationship">
									<input type="checkbox" class="mld-general-check mld-general">
									<input type="hidden" value="excellent" class="working-relationship-checkbox">
								</td>
								<td contenteditable="true" class="working-relationship">
									<input type="checkbox" class="mld-general-check mld-general">
									<input type="hidden" value="good" class="working-relationship-checkbox">
								</td>
								<td contenteditable="true" class="working-relationship">
									<input type="checkbox" class="mld-general-check mld-general">
									<input type="hidden" value="fair" class="working-relationship-checkbox">
								</td>
								<td contenteditable="true" class="working-relationship">
									<input type="checkbox" class="mld-general-check mld-general">
									<input type="hidden" value="poor" class="working-relationship-checkbox">
								</td>
							</tr>
							<tr class="mld-time-keeping">
								<td class="mld-label"><?php echo __( 'Time keeping', 'myrtle-learning-dashboard' ); ?></td>
								<td contenteditable="true" class="time-keeping">
									<input type="checkbox" class="mld-general-check mld-general">
									<input type="hidden" value="excellent" class="time-keeping-checkbox">
								</td>
								<td contenteditable="true" class="time-keeping">
									<input type="checkbox" class="mld-general-check mld-general">
									<input type="hidden" value="good" class="time-keeping-checkbox">
								</td>
								<td contenteditable="true" class="time-keeping">
									<input type="checkbox" class="mld-general-check mld-general">
									<input type="hidden" value="fair" class="time-keeping-checkbox">
								</td>
								<td contenteditable="true" class="time-keeping">
									<input type="checkbox" class="mld-general-check mld-general">
									<input type="hidden" value="poor" class="time-keeping-checkbox">
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<div class="mld-applicent-trustworthy">
				<div class="mld-applicent-trustworthy-label mld-min-height">
					<p><?php echo __( 'Did you find the applicant to be honest and trustworthy?', 'myrtle-learning-dashboard' ); ?></p>
				</div>
				<div class="mld-applicent-trustworthy-data mld-min-height">
					<p>
						<label><?php echo __( 'Yes', 'myrtle-learning-dashboard' ); ?></label>
						<input type="checkbox" class="mld-trustworthy-yes">
						<label><?php echo __( 'No', 'myrtle-learning-dashboard' ); ?></label>
						<input type="checkbox" class="mld-trustworthy-no">
					</p>
				</div>
				<div class="mld-clear-both"></div>
			</div>
			<div class="mld-applicent-duty-care">
				<div class="mld-applicent-duty-care-label mld-min-height">
					<p><?php echo __( 'Did you find the applicant to be reliable in carrying out his/her duties?', 'myrtle-learning-dashboard' ); ?></p>
				</div>
				<div class="mld-applicent-duty-care-data mld-min-height">
					<p>
						<label><?php echo __( 'Yes', 'myrtle-learning-dashboard' ); ?></label>
						<input type="checkbox" class="mld-duty-care-yes">
						<label><?php echo __( 'No', 'myrtle-learning-dashboard' ); ?></label>
						<input type="checkbox" class="mld-duty-care-no">
					</p>
				</div>
				<div class="mld-clear-both"></div>
			</div>
			<div class="mld-applicent-disciplinary-warnings">
				<div class="mld-applicent-disciplinary-warnings-label mld-min-height">
					<p><?php echo __( 'Does or did the applicant have any live disciplinary warnings with your organisation? If yes, please comment on the nature of these warnings below:', 'myrtle-learning-dashboard' ); ?></p>
				</div>
				<div class="mld-applicent-disciplinary-warnings-data mld-min-height">
					<p>
						<label><?php echo __( 'Yes', 'myrtle-learning-dashboard' ); ?></label>
						<input type="checkbox" class="mld-disciplinary-warnings-yes">
						<label><?php echo __( 'No', 'myrtle-learning-dashboard' ); ?></label>
						<input type="checkbox" class="mld-disciplinary-warnings-no">
					</p>
				</div>
				<div class="mld-clear-both"></div>
			</div>
			<div class="mld-leaving-reasons">
				<div class="mld-leaving-reasons-label mld-min-height">
					<p><?php echo __( "What was the reason for the applicant leaving your organisation?", 'myrtle-learning-dashboard' ); ?></p>
				</div>
				<div class="mld-leaving-reasons-data mld-min-height" contenteditable="true"></div>
			</div>
			<div class="mld-re-employ-applicent">
				<div class="mld-re-employ-applicent-label mld-min-height">
					<p><?php echo __( 'Did you find the applicant to be honest and trustworthy?', 'myrtle-learning-dashboard' ); ?></p>
				</div>
				<div class="mld-re-employ-applicent-data mld-min-height">
					<p>
						<label><?php echo __( 'Yes', 'myrtle-learning-dashboard' ); ?></label>
						<input type="checkbox" class="mld-re-employ-yes">
						<label><?php echo __( 'No', 'myrtle-learning-dashboard' ); ?></label>
						<input type="checkbox" class="mld-re-employ-no">
					</p>
				</div>
				<div class="mld-clear-both"></div>
			</div>
			<div class="mld-applicent-job-describe">
				<div class="mld-applicent-job-describe-label mld-min-height">
					<p><?php echo __( 'Do you consider the applicant has the ability and is suitable to perform the job described above?', 'myrtle-learning-dashboard' ); ?></p>
				</div>
				<div class="mld-applicent-job-describe-data mld-min-height">
					<p>
						<label><?php echo __( 'Yes', 'myrtle-learning-dashboard' ); ?></label>
						<input type="checkbox" class="mld-job-describe-yes">
						<label><?php echo __( 'No', 'myrtle-learning-dashboard' ); ?></label>
						<input type="checkbox" class="mld-job-describe-no">
					</p>
				</div>
				<div class="mld-clear-both"></div>
			</div>
			<div class="mld-applicent-specification">
				<div class="mld-applicent-specification-label mld-min-height">
					<p><?php echo __( "Is the applicant able to demonstrate that s/he meets the requirements of the person specification?", 'myrtle-learning-dashboard' ); ?></p>
				</div>
				<div class="mld-applicent-specification-data mld-min-height" contenteditable="true"></div>
			</div>
			<div class="mld-applicent-work-with-children">
				<div class="mld-applicent-work-with-children-label mld-min-height">
					<p><?php echo __( 'Are you satisfied that the candidate is suitable to work with children?', 'myrtle-learning-dashboard' ); ?></p>
				</div>
				<div class="mld-applicent-work-with-children-data mld-min-height">
					<p>
						<label><?php echo __( 'Yes', 'myrtle-learning-dashboard' ); ?></label>
						<input type="checkbox" class="mld-work-with-children-yes">
						<label><?php echo __( 'No', 'myrtle-learning-dashboard' ); ?></label>
						<input type="checkbox" class="mld-work-with-children-no">
					</p>
				</div>
				<div class="mld-clear-both"></div>
			</div>
			<div class="mld-applicent-work-with-children-answer">
				<div class="mld-applicent-work-with-children-answer-label mld-min-height">
					<p><?php echo __( "If you have answered ‘no’ to the above question, please specify your concerns and why you believe the individual may not be suitable.", 'myrtle-learning-dashboard' ); ?></p>
				</div>
				<div class="mld-applicent-work-with-children-answer-data mld-min-height" contenteditable="true"></div>
			</div>
			<div class="mld-applicent-further-comment">
				<div class="mld-applicent-further-comment-label mld-min-height">
					<p><?php echo __( "Please provide any further comments on the applicant's suitability for employment into the post described above.", 'myrtle-learning-dashboard' ); ?></p>
				</div>
				<div class="mld-applicent-further-comment-data mld-min-height" contenteditable="true"></div>
			</div>
			<div class="mld-organization-table">
				<table style="width: 100%;">
					<tr>
						<td width="30%" class="mld-label"><?php echo __( 'Name', 'myrtle-learning-dashboard' ); ?></td>
						<td contenteditable="true" class="mld-name"></td>
					</tr>
					<tr>
						<td width="30%" class="mld-label"><?php echo __( 'Signed', 'myrtle-learning-dashboard' ); ?></td>
						<td contenteditable="true" class="mld-sign">
							<input type="file" name="mld-organization-sign" class="mld-organization-sign">
						</td>
					</tr>
					<tr>
						<td width="30%" class="mld-label"><?php echo __( 'Date', 'myrtle-learning-dashboard' ); ?></td>
						<td contenteditable="true" class="mld-date"></td>
					</tr>
					<tr>
						<td width="30%" class="mld-label"><?php echo __( 'Telephone#', 'myrtle-learning-dashboard' ); ?></td>
						<td contenteditable="true" class="mld-telephone"></td>
					</tr>
					<tr>
						<td width="30%" class="mld-label"><?php echo __( 'Organisation stamp', 'myrtle-learning-dashboard' ); ?></td>
						<td contenteditable="true" class="mld-stamp">
							<input type="file" name="mld-organization-stump" class="mld-organization-stump">
						</td>
					</tr>
				</table>
			</div>
			<div class="mld-conclusion-content">
				<?php echo __( 'Please note that relevant factual content of the reference may be discussed with the applicant', 'myrtle-learning-dashboard' ); ?>
			</div>
			<div class="mld-applicent-form-submit">
				<input type="button" class="mld-applicent-submit" value="<?php echo __( 'Submit', 'myrtle-learning-dashboard' ); ?>">
			</div>
			<div class="mld-error" style="display: none;"><?php echo __( 'You have already filled the form.', 'myrtle-learning-dashboard' ); ?></div>
		</div>
		<?php

		$content = ob_get_contents();
		ob_get_clean();
		return $content;
	}

	/**
	 * create a shortcode to display the refrence form
	 */
	public function mld_refrence_form_html() {

		?>
		<div class="mld-ref-main-wrapper">
			<table class="mld-refrence-form-wrapper">
				<tr class="mld-refree-heading">
					<th colspan="2"><?php echo __( 'Referee', 'myrtle-learning-dashboard' ); ?></th>
				</tr>
				<tr colspan="2" class="mld-empty-row"></tr>
				<tr>
					<td width="40%" class="mld-filled-data mld-first-label"><?php echo __( 'Name of Applicant:', 'myrtle-learning-dashboard' ); ?></td>
					<td width="60%" class="mld-answer-box mld-first-answer"><input></td>
				</tr>
				<tr>
					<td width="40%" class="mld-filled-data mld-general-data"><?php echo __( 'Position Applied for:', 'myrtle-learning-dashboard' ); ?></td>
					<td width="60%" class="mld-answer-box mld-general-answer"><input></td>
				</tr>
				<tr>
					<td width="40%" class="mld-filled-data mld-general-data"><?php echo __( 'Name of Referee:', 'myrtle-learning-dashboard' ); ?></td>
					<td width="60%" class="mld-answer-box mld-general-answer"><input></td>
				</tr>
				<tr>
					<td width="40%" class="mld-filled-data mld-general-data"><?php echo __( 'Email Address of Referee:', 'myrtle-learning-dashboard' ); ?></td>
					<td width="60%" class="mld-answer-box mld-general-answer"><input></td>
				</tr>
				<tr>
					<td width="40%" class="mld-filled-data mld-general-data"><?php echo __( 'Phone Number of Referee:', 'myrtle-learning-dashboard' ); ?></td>
					<td width="60%" class="mld-answer-box mld-general-answer"><input></td>
				</tr>
				<tr>
					<td width="40%" class="mld-filled-data mld-last-label"><?php echo __( 'Name of Organisation:', 'myrtle-learning-dashboard' ); ?></td>
					<td width="60%" class="mld-answer-box mld-last-answer"><input></td>
				</tr>
			</table>
		</div>
		<span class="mld-add-another-referr"><?php echo __( 'Add another referee', 'myrtle-learning-dashboard' ); ?></span>
		<div style="display: none;">
			<input type="button" class="mld-ref-submit" value="<?php echo __( 'Submit', 'myrtle-learning-dashboard' ); ?>">
			<div class="mld-clear-both"></div>
		</div>
		<?php
	}

	/**
	 * disabled sending emails
	 */
	public function mld_register_new_user( $user_id ) {
		remove_action( 'register_new_user', 'wp_send_new_user_notifications' );
	}	

	/**
	 * restrict user to reset password
	 */
	public function mld_resrict_user_to_reset_pass( $allow, $user_id ) {

		$user_data = get_userdata( $user_id );
		$user_roles = isset( $user_data->roles ) ? $user_data->roles : '';

		if( in_array( 'pending', $user_roles ) || ! $user_roles ) {
			return false;
		}		

		return true;
	}

	/**
	 * create a shortcode to display the popup content
	 */
	public function mld_terms_condition_popup() {
		?>
		<div style="display: none;"class="mld-pop-outer">
			<div class="mld-pop-inner">
				<div class="mld-popup-header">
					<div class="mld-close mld-reset-close">
						<span class="dashicons dashicons-no"></span>
					</div>
				</div>
				<div class="mld-terms-condition-wrapper">
					<div class="mld-terms-condition-header">
						<img src="<?php echo MLD_ASSETS_URL.'images/logo.png'; ?>">
					</div>
					<div class="mld-term-condition-title">
						<u>
							<h2><?php echo __( 'Terms and Conditions', 'myrtle-learning-dashboard' ); ?></h2>
						</u>
					</div>
					<div class="mld-term-condition-content">
						<ul>
							<li><?php echo __( 'Myrtle Learning registration must be fully completed as best as possible via the website', 'myrtle-learning-dashboard' ); ?> <a href="https://myrtlelearning.com/" style="color: blue;"><?php echo __( '( www.myrtlelearning.com ).', 'myrtle-learning-dashboard' ); ?></a> </li>
							<li><?php echo __( 'Students must enroll onto the relevant course before turning up for tuition. If your child has any medical condition we <b>MUST</b> be informed when the registration form is filled in.', 'myrtle-learning-dashboard' ); ?></li>
							<li><?php echo __( 'If a condition has not been brought to our attention, we will not accept responsibility for any treatment or non-treatment of any condition. ', 'myrtle-learning-dashboard' ); ?></li>
							<li><?php echo __( 'If a condition is brought to our attention that requires a parent/guardian to be present we must be made aware of this while your child is in tuition.', 'myrtle-learning-dashboard' ); ?></li>
							<li><?php echo __( 'Under no circumstances will any staff member of Myrtle Learning attempt or manage in any way or involve themselves with the condition during the tuition period.', 'myrtle-learning-dashboard' ); ?></li>
							<li><?php echo __( 'If in our opinion we feel that a student is likely to prejudice the interests and actions of any other student or any staff member or if any information was withheld when filling out the registration form, we reserve the right to cancel lessons without notice, and fees already paid will not be refunded.', 'myrtle-learning-dashboard' ); ?></li>
							<li><?php echo __( 'It is the responsibility of parents/students to rearrange lessons they have missed in collaboration with the staff at Myrtle Learning.', 'myrtle-learning-dashboard' ); ?></li>
							<li><?php echo __( 'Initial assessments are chargeable per hour. The total cost for the month is calculated for the minimum hours to be covered per month. Payments must be made monthly.', 'myrtle-learning-dashboard' ); ?></li>
							<li><?php echo __( 'All students are encouraged to use our strategically designed bespoke exercise books for their notes. These books can be bought directly from our website or in our office.', 'myrtle-learning-dashboard' ); ?></li>
							<li><?php echo __( 'Enrollment fees are non-refundable.', 'myrtle-learning-dashboard' ); ?></li>
							<li><?php echo __( 'For online lessons, parents/students must ensure they have all the necessary resources to access the lesson.', 'myrtle-learning-dashboard' ); ?></li>
							<li><?php echo __( 'Myrtle Learning reserves the right to revoke the enrollment of any students who fail to comply with it’s rules or fail to meet the required Myrtle standards.', 'myrtle-learning-dashboard' ); ?></li>
							<li><?php echo __( 'During online lessons, students are not allowed to leave the sessions without the permission of the facilitators.', 'myrtle-learning-dashboard' ); ?></li>
							<li><?php echo __( 'During online lessons, students must enable their microphones and share their screens at all times.', 'myrtle-learning-dashboard' ); ?></li>
							<li><?php echo __( 'Any criminal damage made from the students must be paid in full to cover the cost.', 'myrtle-learning-dashboard' ); ?></li>
							<li><?php echo __( 'Myrtle Learning will not accept any responsibility once a student has left the premises. If a student is delayed or expects to be late for a lesson, please contact us on +44 330 118 0087 or an email to <u style="color: blue;">hello@myrtlelearning.com</u>. ', 'myrtle-learning-dashboard' ); ?></li>
							<li><?php echo __( 'Parents are fully responsible for siblings of students and any family relatives that are not enrolled with Myrtle Learning but come to our premises.', 'myrtle-learning-dashboard' ); ?></li>
							<li><?php echo __( 'No chewing gum is to be brought in by any  student to our centres, but the student is allowed to bring any food or drink to the lesson.', 'myrtle-learning-dashboard' ); ?></li>
							<li><?php echo __( 'If you have any enquiries, contact us from 10am-8pm Monday to Saturday and 12pm – 5pm Sundays. We can also be contacted by using ‘contact us’ link on the website', 'myrtle-learning-dashboard' ); ?></li>
						</ul>
						<div class="mld-team-title">
							<?php echo __( 'Myrtle Learning Team', 'myrtle-learning-dashboard' ); ?>
						</div>
						<div class="mld-social-icons">
							<a href="https://web.facebook.com/people/Myrtle-Learning/100065179803041/">
								<img src="<?php echo MLD_ASSETS_URL.'images/facebook.PNG'; ?>">
							</a>
							<a href="https://twitter.com/MyrtleLearning">
								<img src="<?php echo MLD_ASSETS_URL.'images/twitter.PNG'; ?>">
							</a>
							<a href="https://www.instagram.com/myrtlelearning/">
								<img src="<?php echo MLD_ASSETS_URL.'images/instagram.PNG'; ?>">
							</a>
							<a href="https://www.youtube.com/watch?v=8hg7ZvGBS0U">
								<img src="<?php echo MLD_ASSETS_URL.'images/youtube.PNG'; ?>">
							</a>
							<a href="https://www.google.com/search?hl=en-GB&gl=uk&q=Myrtle+Learning,+25+Pattens+Gardens,+Rochester+ME1+2QP&ludocid=17137572700817711204&lsig=AB86z5VptGt4KvM2lR-rXxtHp_W4#lrd=0x47d8cd0053a709d5:0xedd4e39298abe064,3">
								<img src="<?php echo MLD_ASSETS_URL.'images/google.PNG'; ?>">
							</a>
						</div>
						<div class="mld-terms-condition-footer">
							<a href="https://myrtlelearning.com/">
								<?php echo __( 'www.myrtlelearning.com', 'myrtle-learning-dashboard' ); ?>
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * user term and condition
	 */
	public function mld_forms_terms_condition_btn() {

		?>
		<div class="mld-footer-buttom">
			<button class="mld-registration-terms-condition">
				<?php echo __( 'TERMS & CONDITION', 'myrtle-learning-dashboard' ); ?>
			</button>
		</div>
		<?php
	}

	/**
	 * set user role as no role
	 */
	public function mld_user_register( $user_id ) {
	}

	/**
	 * update subjects
	 */
	public function mld_update_new_subjects() {

		$subject = isset( $_POST['subject'] ) ? $_POST['subject'] : '';
		
		if( ! $subject ) {
			wp_die();
		}

		$get_new_subjects = get_option( 'mld-new-subjects' );
		
		if( empty( $get_new_subjects ) || ! $get_new_subjects ) {
			$get_new_subjects = [];
		}

		$get_new_subjects[] = $subject;

		update_option( 'mld-new-subjects', $get_new_subjects );
		wp_die();
	}

	/**
	 * update teacher information
	 */
	public function mld_update_teacher_information() {

		$response = [];
		$files = $_FILES;

		$teacher_college_information = isset( $_POST['teacher_college_info'] ) ? str_replace( '\\', '', $_POST['teacher_college_info'] ) : [];
		$teacher_uni_information = isset( $_POST['teacher_uni_info'] ) ? str_replace( "\\", '', $_POST['teacher_uni_info'] ) : [];
		$teacher_experience = isset( $_POST['teacher_experience'] ) ? str_replace( "\\", '', $_POST['teacher_experience'] ) : [];

        $title = isset( $_POST['title'] ) ? $_POST['title'] : '';
        $surname = isset( $_POST['surname'] ) ? $_POST['surname'] : '';
        $first_name = isset( $_POST['firstName'] ) ? $_POST['firstName'] : '';
        $email = isset( $_POST['email'] ) ? $_POST['email'] : '';
        $address = isset( $_POST['address'] ) ? $_POST['address'] : '';
        $dob = isset( $_POST['dob'] ) ? $_POST['dob'] : '';
        $county = isset( $_POST['county'] ) ? $_POST['county'] : '';
        $home_tel = isset( $_POST['homeTel'] ) ? $_POST['homeTel'] : '';
        $mobile_number = isset( $_POST['mobileNumber'] ) ? $_POST['mobileNumber'] : '';
        $experience = isset( $_POST['experience'] ) ? $_POST['experience'] : '';
        $subjects = isset( $_POST['subjects'] ) ? explode( ',', $_POST['subjects'] ) : [];
        $availability = isset( $_POST['availability'] ) ? $_POST['availability'] : '';
        $dbs = isset( $_POST['dbs'] ) ? $_POST['dbs'] : '';
        $personal_statement = isset( $_POST['personalStatement'] ) ? $_POST['personalStatement'] : '';
        $username = isset( $_POST['username'] ) ? $_POST['username'] : '';
        
        $teacher_basic_info = [
        	'title' 				=> ucwords( $title ),
        	'surname' 				=> ucwords( $surname ),
        	'first_name' 			=> $first_name,
        	'address' 				=> $address,
        	'dob' 					=> $dob,
        	'county' 				=> $county,
        	'hometel' 				=> $home_tel,
        	'mobile_number' 		=> $mobile_number,
        	'experience' 			=> $experience,
        	'availability' 			=> $availability,
        	'dbs' 					=> $dbs,
        	'email' 				=> $email,
        	'personal_statement' 	=> $personal_statement,
        ];

        if( $first_name && $email ) {
        	update_option( 'mld_is_teacher', 'yes' );
        }
        
        $password = wp_generate_password(12);
        $user_id = wp_create_user( $username, $password, $email );
        
        if ( !is_wp_error( $user_id ) ) {

        	$user = new WP_User( $user_id );
        	$user->set_role('pending');
        	$tec_college_info = json_decode( $teacher_college_information );
        	$tec_uni_info = json_decode( $teacher_uni_information );
        	$tec_basic_info = $teacher_basic_info;
        	$tec_experience_info = json_decode( $teacher_experience );
        	
        	update_user_meta( $user_id, 'mld-teacher-college-info', $tec_college_info );
        	update_user_meta( $user_id, 'mld-teacher-uni-info', $tec_uni_info );
        	update_user_meta( $user_id, 'mld-teacher-basic-info', $tec_basic_info );
        	update_user_meta( $user_id, 'mld-teacher-experience-info', $tec_experience_info );
        	update_user_meta( $user_id, 'mld_teacher_selected_subjects', $subjects );

        	$uploads_dir = wp_upload_dir();

        	if( ! empty( $uploads_dir['basedir'] ) ) {

        		$teachers_folder = 'mld-teachers-data';
        		$teacher_folder_path = $uploads_dir['basedir'] . '/' . $teachers_folder;
        		if( ! is_dir( $teacher_folder_path ) ) {
        			wp_mkdir_p( $teacher_folder_path );
        		}

        		$teacher_upload_dir = $uploads_dir['basedir'].'/mld-teachers-data/teacher_'.$user_id;

        		if ( ! is_dir( $teacher_upload_dir ) ) {
        			wp_mkdir_p( $teacher_upload_dir );
        		}

        		if( is_array( $files ) && ! empty( $files ) ) {

        			$file_keys = array_keys( $files ); 
        			$no = 0;
        			$statement_upload = isset( $files['statement']['tmp_name'] ) ? $files['statement']['tmp_name'] : '';

					$photo_upload = isset( $files['photo']['tmp_name'] ) ? $files['photo']['tmp_name'] : '';
					$file_name = isset( $files['statement']['name'] ) ? $files['statement']['name'] : '';

        			foreach( $files as $file ) {
        				
        				$key = isset( $file_keys[$no] ) ? $file_keys[$no] : '';
        				if( $key ) {

        					if( 'statement' == $key ) {

        						$filename = $file_name;
        						move_uploaded_file( $statement_upload, $teacher_upload_dir.'/'.$filename );
        						update_user_meta( $user_id, 'mld_teacher_statement', $filename );
        					}

        					if( 'photo' === $key ) {

        						$new_upload_dir = $uploads_dir['basedir'].'/mld_avatar/user_'.$user_id;

        						if ( ! file_exists( $new_upload_dir ) ) {
        							wp_mkdir_p( $new_upload_dir );
        						}

        						$filename = rand(1005126, 9999888444) . 'mld_avatar.jpg';
    	    					move_uploaded_file( $photo_upload, $new_upload_dir . '/' . $filename );
	        					$directory_url =  site_url().'/wp-content/uploads/mld_avatar/user_'.$user_id;
        						update_user_meta( $user_id, 'mld_user_avatar', $directory_url . '/' . $filename );
        					}
        				} 
        				$no++;
        			}
        		}
        	}

        	$user_dir = isset( $uploads_dir['basedir'] ) ? $uploads_dir['basedir'] : '';
        	
        	$photo_file_address = $user_dir.'/mld-teachers-data/teacher_'.$user_id.'/mld_teacher_photo';
        	$statement_file_address = $user_dir.'/mld-teachers-data/teacher_'.$user_id.'/'.$file_name;
        	$headers = array('Content-Type: text/html; charset=UTF-8');
        	$img_url = MLD_ASSETS_URL.'images/logo_white-1.png';
        	$table_html .= '<div class="mld-email-table-wrapper" style="background-color: #e4e4e473; padding-botton: 30px;">';
        	$table_html .= '<div class="mld-email-table-header" style="background-color:#18440a; color:white; text-align: center; padding: 40px 0;">
        					<img src="'.$img_url.'" style="height: 70px;">
        					<h1>Teachers Details</h1>
        					</div>';
        	$table_html .= '<div><table style="width: 100%; padding: 1% 5% 0 5%;">
        	<tr>
        	<th style="background-color: #18440a; color: white; font-weight: bold; text-align: left; padding: 10px;">Label</th>
        	<th style="background-color: #18440a; color: white; font-weight: bold; text-align: left; padding: 10px;">Details</th>
        	</tr>';
        	$p_no = 1;
        	foreach ( $teacher_basic_info as $key => $basic_info ) {
        		
        		$bg_color = '';

        		if( $p_no % 2 == 0 ) {
        			$bg_color = '#18440a38';
        		}
        		$p_no++;
        		$index_heading = ucwords( str_replace( '_', ' ', $key ) );
        		if( 'Dbs' == $index_heading ) {
        			$index_heading = 'DBS';
        		}

        		if( 'Hometel' == $index_heading ) {
        			$index_heading = 'Home Tel';
        		}

        		$table_html .= '<tr>';
        		$table_html .= '<td style="border: 1px solid #e4e4e4; background-color: #18440a; color: white; width: 200px; padding: 10px;">' . ucwords( $index_heading ) . '</td>';
        		$table_html .= '<td style="border: 1px solid #e4e4e4; color: #18440a; background-color: '.$bg_color.'; width: 500px; padding: 10px;">' . $basic_info . '</td>';
        		$table_html .= '</tr>';
        	}

        	$table_html .= '</table></div>';
        	$table_html .= '<p></p>';
        	$table_html .= '<div><table style="width: 100%; padding: 1% 5% 0 5%;">
        	<tr>
        	<th style="background-color: #18440a; color: white; text-align: left; font-weight: 600; padding: 10px;">Date</th>
        	<th style="background-color: #18440a; color: white; text-align: left; font-weight: 600; padding: 10px;">College / A Level</th>
        	<th style="background-color: #18440a; color: white; text-align: left; font-weight: 600; padding: 10px;">Courses / Subjects</th>
        	<th style="background-color: #18440a; color: white; text-align: left; font-weight: 600; padding: 10px;">Status (Pass/Fail/Pending)</th>
        	</tr>';

        	$c_no = 0;
        	foreach ( $tec_college_info as $key => $college_info ) {

        		$table_array = isset( $tec_college_info[$c_no] ) ? $tec_college_info[$c_no] : [];
        		$table_html .= '<tr>';
        		if( is_array( $table_array ) && ! empty( $table_array ) ) {
        			foreach( $table_array as $data ) {
        				$table_html .= '<td style="border: 1px solid #18440a; text-align: left; padding: 10px;">' . $data . '</td>';		
        			}
        		}
        		$table_html .= '</tr>';
        		$c_no++;
        	}

        	$table_html .= '</table><div>';
        	$table_html .= '<p></p>';
        	$table_html .= '<div><table style="width: 100%; padding: 1% 5% 0 5%;"> 
        	<tr>
        	<th style="background-color: #18440a; color: white; text-align: left; font-weight: 600; padding: 10px;">Date</th>
        	<th style="background-color: #18440a; color: white; text-align: left; font-weight: 600; padding: 10px;">University</th>
        	<th style="background-color: #18440a; color: white; text-align: left; font-weight: 600; padding: 10px;">Subjects</th>
        	<th style="background-color: #18440a; color: white; text-align: left; font-weight: 600; padding: 10px;">Qualification  (Degree/Masters/Doctorate)</th>
        	</tr>';

        	$u_no = 0;
        	foreach ( $tec_uni_info as $key => $uni_info ) {

        		$uni_table_array = isset( $tec_uni_info[$u_no] ) ? $tec_uni_info[$u_no] : [];
        		$table_html .= '<tr>';
        		if( is_array( $uni_table_array ) && ! empty( $uni_table_array ) ) {
        			foreach( $uni_table_array as $uni_data ) {
        				$table_html .= '<td style="border: 1px solid #18440a; text-align: left; padding: 10px;">' . $uni_data . '</td>';		
        			}
        		}
        		$table_html .= '</tr>';
        		$u_no++;
        	}

        	$table_html .= '</table></div>';
        	$table_html .= '<p></p>';
        	$table_html .= '<div><table style="width: 100%; padding: 1% 5% 0 5%;">
        	<tr>
        	<th style="background-color: #18440a; color: white; text-align: left; font-weight: 600; padding: 10px;">Subject Taught</th>
        	<th style="background-color: #18440a; color: white; text-align: left; font-weight: 600; padding: 10px;">Level</th>
        	<th style="background-color: #18440a; color: white; text-align: left; font-weight: 600; padding: 10px;">Number Of Students</th>
        	<th style="background-color: #18440a; color: white; text-align: left; font-weight: 600; padding: 10px;">Percentage Pass</th>
        	</tr>';

        	$e_no = 0;
        	foreach ( $tec_experience_info as $key => $experience_info ) {

        		$exp_table_array = isset( $tec_experience_info[$e_no] ) ? $tec_experience_info[$e_no] : [];
        		$table_html .= '<tr>';
        		if( is_array( $exp_table_array ) && ! empty( $exp_table_array ) ) {
        			$no = 0;
        			foreach( $exp_table_array as $exp_data ) {

        				$no++;
        				if( 5 == $no ) {
        					continue;
        				}

        				if( ! $exp_data ) {
        					continue;
        				}
        				$table_html .= '<td style="border: 1px solid #18440a; text-align: left; padding: 10px;">' . $exp_data . '</td>';		
        			}
        		}
        		$table_html .= '</tr>';
        		$e_no++;
        	}

        	$table_html .= '</table><div>';
        	$table_html .= '</div>';
        	$table_html .= '<div style="text-align: center; font-weight: 600; padding: 10px; background-color: #18440a; color: white; margin-top: 15px;"> <span class="dashicons dashicons-admin-generic">&#169;</span> Myrtle Learning</div>';
        	// $admin_email = get_option('admin_email');
        	$admin_email = 'hello@myrtlelearning.com';
        	wp_mail( $admin_email, 'Teacher Registration', $table_html, $headers, $statement_file_address );
        	$header_url = MLD_ASSETS_URL.'images/header.PNG';
        	$footer_url = MLD_ASSETS_URL.'images/footer.PNG';
        	$teacher_content .= '<img src="'.$header_url.'" style="width: 99%;">';
        	$teacher_content .= '<p></p>';
        	$teacher_content .= '<div style="font-size: 20px; color: #365249;">Staff Registration</div>';
        	$teacher_content .= '<div style="font-weight: 600; font-size: 20px; color: #365249;">Confirmation Email</div>';
        	$teacher_content .= '<div style="color: #365249; font-size: 15px; margin: 15px 0 0 0;">Dear '.$first_name.',</div>';
        	$teacher_content .= '<div style="color: #365249; font-size: 15px; margin: 15px 0 15px 0;">Thank you for completing the application form for a role with Myrtle
				Learning.</div>';
			$teacher_content .= '<div style="color: #365249; font-size: 15px; margin: 15px 0 15px 0;">Your application is currently under review and we will be in touch with you
				in due course to arrange the next steps.</div>';
			$teacher_content .= '<div style="color: #365249; font-size: 15px; margin: 15px 0 15px 0;">We would confirm via email, if we would be proceeding with your
				application or otherwise.</div>';
			$teacher_content .= '<div style="color: #365249; font-size: 15px; margin: 15px 0 15px 0;">Thank you</div>';
			$teacher_content .= '<div style="font-weight: 600; color: #365249; font-size: 15px; margin: 15px 0 15px 0;">The Myrtle Learning<br>
				Recruitment Team</div>';
			$teacher_content .= '<p></p>';
			$teacher_content .= '<img src="'.$footer_url.'" style="width: 99%;">';
			wp_mail( $email, 'Registration Confirmation Email', $teacher_content, $headers );
        	echo __( 'Form Submit Successfully', 'myrtle-learning-dashboard' );
        } else {

        	$error_msg = isset( $user_id->errors['existing_user_login'][0] ) ? $user_id->errors['existing_user_login'][0] : '';
        	
        	if( ! $error_msg ) {

        		$error_msg = isset( $user_id->errors['existing_user_email'][0] ) ? $user_id->errors['existing_user_email'][0] : '';
        	}

        	echo $error_msg;
        }

        wp_die();
	}

	/**
	 * enqueue staff scripts
	 */
	public function mld_staff_scripts() {
		
		$rand = rand( 1000000, 1000000000 );
		wp_enqueue_style( 'staff-css', MLD_ASSETS_URL .'css/staff.css', '', $rand, false );
		wp_enqueue_style( 'external-select-min-css', MLD_ASSETS_URL .'css/select2.min.css', '', $rand, false );
		wp_enqueue_script( 'external-select2-jquery-js', MLD_ASSETS_URL. 'js/select2.full.min.js', ['jquery'], $rand, true );
		wp_enqueue_script( 'recaptcha-js', 'https://www.google.com/recaptcha/api.js', [ 'jquery' ], $rand, true );
		wp_enqueue_script( 'staff-js', MLD_ASSETS_URL .'js/staff.js', [ 'jquery' ], $rand, true );
		wp_localize_script( 'staff-js', 'MLD', [
			'ajaxURL'       => admin_url( 'admin-ajax.php' ),
			'siteURL'		=> site_url()
		] );
	}

	/**
	 * create a shortcode to display the teacher registration form
	 */
	public function mld_teacher_registration_form() {

		$new_subject = get_option( 'mld-new-subjects' );
		
		if( ! $new_subject ) {
			$new_subject = [];
		}

		if( is_array( $new_subject ) && ! empty( $new_subject ) ) {
			$new_subject_arr = [];
			foreach( $new_subject as $subject ) {
				$subject_key = str_replace( ' ', '-', $subject );

				$new_subject_arr[$subject_key] = $subject;
			}

			if( is_array( $new_subject_arr ) && ! empty( $new_subject_arr ) ) {
				$new_subject = $new_subject_arr;
			}
		}

		$subjects_array = [
			'Physics' => 'Physics',
			'Chemistry' => 'Chemistry',
			'Biology' => 'Biology',
			'Mathematics' => 'Mathematics',
			'English-Language' => 'English Language',
			'English-Literature' => 'English Literature',
			'Computer-Science' => 'Computer Science',
			'Design-and-Technology' => 'Design and Technology',
			'Geography' => 'Geography',
			'Business-Studies' => 'Business Studies',
			'Economics' => 'Economics',
			'Psychology' => 'Psychology',
			'French' => 'French',
			'Spanish' => 'Spanish',
			'Law' => 'Law',
			'Sociology' => 'Sociology',
			'History	' => 'History	',
			'Latin' => 'Latin',
			'Drama' => 'Drama',
			'Food-Technology' => 'Food Technology',
			'11Plus-Mathematics' => '11Plus Mathematics',
			'11Plus-Verbal-Reasoning' => '11Plus Verbal Reasoning',
			'11Plus-Non-Verbal-Reasoning' => '11Plus Non Verbal Reasoning',
			'11Plus-English' => '11Plus English',	
		];
		$subjects_array = array_merge( $new_subject, $subjects_array );
		$img_url = MLD_ASSETS_URL.'images/logo.png';
		?>
		<div class="mld-staff-background-overlay"></div>
		<div class="mld-teacher-registration-wrapper">
			<div class="mld-main-logo">
				<img src="<?php echo $img_url; ?>">
			</div>
			<div class="mld-main-title">
				<h1><?php echo __( 'Teacher Registration Form', MLD_TEXT_DOMAIN ); ?></h1>
			</div>
			<div class="mld-teacher-form-step">
				<div class="mld-step-number" style="border-color: green;">1</div>
				<div class="mld-step-line"></div>
				<div class="mld-step-number">2</div>
			</div>

			<div style="display: none;"class="mld-pop-outer">
				<div class="mld-pop-inner">
					<div class="mld-popup-header">
						<div class="mld-close mld-reset-close">
							<span class="dashicons dashicons-no"></span>
						</div>
					</div>
					<div class="mld-terms-condition-wrapper">
						<div class="mld-terms-condition-header">
							<img src="<?php echo MLD_ASSETS_URL.'images/logo.png'; ?>">
						</div>
						<div class="mld-term-condition-title">
							<u>
								<h2><?php echo __( 'Terms and Conditions', MLD_TEXT_DOMAIN ); ?></h2>
							</u>
						</div>
						<div class="mld-term-condition-content">
							<ul>
								<li><?php echo __( 'Myrtle Learning registration must be fully completed as best as possible via the website', MLD_TEXT_DOMAIN ); ?> <a href="https://myrtlelearning.com/" style="color: blue;"><?php echo __( '( www.myrtlelearning.com ).', MLD_TEXT_DOMAIN ); ?></a> </li>
								<li><?php echo __( 'Students must enroll onto the relevant course before turning up for tuition. If your child has any medical condition we <b>MUST</b> be informed when the registration form is filled in.', MLD_TEXT_DOMAIN ); ?></li>
								<li><?php echo __( 'If a condition has not been brought to our attention, we will not accept responsibility for any treatment or non-treatment of any condition. ', MLD_TEXT_DOMAIN ); ?></li>
								<li><?php echo __( 'If a condition is brought to our attention that requires a parent/guardian to be present we must be made aware of this while your child is in tuition.', MLD_TEXT_DOMAIN ); ?></li>
								<li><?php echo __( 'Under no circumstances will any staff member of Myrtle Learning attempt or manage in any way or involve themselves with the condition during the tuition period.', MLD_TEXT_DOMAIN ); ?></li>
								<li><?php echo __( 'If in our opinion we feel that a student is likely to prejudice the interests and actions of any other student or any staff member or if any information was withheld when filling out the registration form, we reserve the right to cancel lessons without notice, and fees already paid will not be refunded.', MLD_TEXT_DOMAIN ); ?></li>
								<li><?php echo __( 'It is the responsibility of parents/students to rearrange lessons they have missed in collaboration with the staff at Myrtle Learning.', MLD_TEXT_DOMAIN ); ?></li>
								<li><?php echo __( 'Initial assessments are chargeable per hour. The total cost for the month is calculated for the minimum hours to be covered per month. Payments must be made monthly.', MLD_TEXT_DOMAIN ); ?></li>
								<li><?php echo __( 'All students are encouraged to use our strategically designed bespoke exercise books for their notes. These books can be bought directly from our website or in our office.', MLD_TEXT_DOMAIN ); ?></li>
								<li><?php echo __( 'Enrollment fees are non-refundable.', MLD_TEXT_DOMAIN ); ?></li>
								<li><?php echo __( 'For online lessons, parents/students must ensure they have all the necessary resources to access the lesson.', MLD_TEXT_DOMAIN ); ?></li>
								<li><?php echo __( 'Myrtle Learning reserves the right to revoke the enrollment of any students who fail to comply with it’s rules or fail to meet the required Myrtle standards.', MLD_TEXT_DOMAIN ); ?></li>
								<li><?php echo __( 'During online lessons, students are not allowed to leave the sessions without the permission of the facilitators.', MLD_TEXT_DOMAIN ); ?></li>
								<li><?php echo __( 'During online lessons, students must enable their microphones and share their screens at all times.', MLD_TEXT_DOMAIN ); ?></li>
								<li><?php echo __( 'Any criminal damage made from the students must be paid in full to cover the cost.', MLD_TEXT_DOMAIN ); ?></li>
								<li><?php echo __( 'Myrtle Learning will not accept any responsibility once a student has left the premises. If a student is delayed or expects to be late for a lesson, please contact us on +44 330 118 0087 or an email to <u style="color: blue;">fankahsa@myrtlelearning.com</u>. ', MLD_TEXT_DOMAIN ); ?></li>
								<li><?php echo __( 'Parents are fully responsible for siblings of students and any family relatives that are not enrolled with Myrtle Learning but come to our premises.', MLD_TEXT_DOMAIN ); ?></li>
								<li><?php echo __( 'No chewing gum is to be brought in by any  student to our centres, but the student is allowed to bring any food or drink to the lesson.', MLD_TEXT_DOMAIN ); ?></li>
								<li><?php echo __( 'If you have any enquiries, contact us from 10am-8pm Monday to Saturday and 12pm – 5pm Sundays. We can also be contacted by using ‘contact us’ link on the website', MLD_TEXT_DOMAIN ); ?></li>
							</ul>
							<div class="mld-team-title">
								<?php echo __( 'Myrtle Learning Team', MLD_TEXT_DOMAIN ); ?>
							</div>
							<div class="mld-social-icons">
								<a href="https://web.facebook.com/people/Myrtle-Learning/100065179803041/">
									<img src="<?php echo MLD_ASSETS_URL.'images/facebook.PNG'; ?>">
								</a>
								<a href="https://twitter.com/MyrtleLearning">
									<img src="<?php echo MLD_ASSETS_URL.'images/twitter.PNG'; ?>">
								</a>
								<a href="https://www.instagram.com/myrtlelearning/">
									<img src="<?php echo MLD_ASSETS_URL.'images/instagram.PNG'; ?>">
								</a>
								<a href="https://www.youtube.com/watch?v=8hg7ZvGBS0U">
									<img src="<?php echo MLD_ASSETS_URL.'images/youtube.PNG'; ?>">
								</a>
								<a href="https://www.google.com/search?hl=en-GB&gl=uk&q=Myrtle+Learning,+25+Pattens+Gardens,+Rochester+ME1+2QP&ludocid=17137572700817711204&lsig=AB86z5VptGt4KvM2lR-rXxtHp_W4#lrd=0x47d8cd0053a709d5:0xedd4e39298abe064,3">
									<img src="<?php echo MLD_ASSETS_URL.'images/google.PNG'; ?>">
								</a>
							</div>
							<div class="mld-terms-condition-footer">
								<a href="https://myrtlelearning.com/">
									<?php echo __( 'www.myrtlelearning.com', MLD_TEXT_DOMAIN ); ?>
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="mld-form-fields-main-wrapper">
				<div class="mld-form-first-page">
					
					<div class="mld-form-fields-wrapper">
						<div class="mld-form-field">
							<div class="mld-registration-title"><?php echo __( 'Title' ); ?></div>
							<div class="mld-surname-wrapper">
								<?php 
								echo self::mld_get_registration_title_html( 'dr' );
								echo self::mld_get_registration_title_html( 'mr' );
								echo self::mld_get_registration_title_html( 'mrs' );
								echo self::mld_get_registration_title_html( 'ms' );
								echo self::mld_get_registration_title_html( 'miss' );
								?>
							</div>
						</div>
						<?php
						echo self::mld_get_form_fields( 'Username', 'yes', 'text', 'mld-username' );
						?>
					</div> 
					<div class="mld-form-fields-wrapper">
						<?php
						echo self::mld_get_form_fields( 'Surname', 'yes', 'text', 'mld-surname' );
						echo self::mld_get_form_fields( 'First Name', 'yes', 'text', 'mld-first-name' );
						?>
					</div>
					<div class="mld-form-fields-wrapper">
						<?php
						echo self::mld_get_form_fields( 'Email', 'yes', 'email', 'mld-email' );
						echo self::mld_get_form_fields( 'Address', 'no', 'text', 'mld-address' );
						?>
					</div>
					<div>
						<label><?php echo __( 'Enter Your Personal Statement ( Max 100 Words )' ); ?></label>
						<textarea class="mld-personal-statement" rows="4" placeholder="<?php echo __( 'Please write a brief personal statement about yourself, not more than 100words focussed on the topics : Passion; Challenges faced/Solutions; Personal / Educational values/ethos', MLD_TEXT_DOMAIN ); ?>"></textarea>
					</div>
					<div class="mld-form-fields-wrapper">
						<?php
						echo self::mld_get_form_fields( 'Date Of Birth', 'yes', 'date', 'mld-date-of-birth' );
						echo self::mld_get_form_fields( 'Town/County', 'no', 'text', 'mld-county' );
						?>
					</div>
					<div class="mld-form-fields-wrapper">
						<?php
						echo self::mld_get_form_fields( 'Home Tel', 'no', 'number', 'mld-home-tel' );
						echo self::mld_get_form_fields( 'Mobile Number', 'yes', 'number', 'mld-mobile-number' );
						?>
					</div>
					<div class="mld-form-fields-wrapper">
						<?php
						echo self::mld_get_form_fields( 'Upload Statement/CV', 'no', 'file', 'mld-statement' );
						echo self::mld_get_form_fields( 'Upload Photo', 'no', 'file', 'mld-photo' );
						?>
					</div>
					<div class="mld-form-footer">
						<div class="mld-footer-buttom">
							<button class="mld-registration-next-btn"><?php echo __( 'Next', MLD_TEXT_DOMAIN ); ?></button>
							<button class="mld-registration-terms-condition" style="display: none;"><?php echo __( 'TERMS & CONDITION', MLD_TEXT_DOMAIN ); ?></button>
						</div>
						<div class="mld-clear-both"></div>
					</div>
					<div class="mld-fields-empty-message">
						<?php echo __( 'Please fill out all necessary fields', MLD_TEXT_DOMAIN ); ?>
					</div>
				</div>
				<div class="mld-form-second-page">
					<div class="mld-form-fields-wrapper">
						<div class="mld-form-field">
							<select class="mld-experience-field">
								<option value=""><?php echo __( 'Select number of experience', MLD_TEXT_DOMAIN ); ?></option>
								<?php
								for ( $x = 1; $x <= 20; $x++ ) {
									?>
									<option value="<?php echo $x; ?>"><?php echo $x; ?></option>
									<?php
								}
								?>
							</select>
						</div>
						<div class="mld-form-field">
							<select class="mld-subject-field" multiple="multiple">
								<option value=""><?php echo __( 'Select Subjects', MLD_TEXT_DOMAIN ); ?></option>
								<?php 
								if( is_array( $subjects_array ) && ! empty( $subjects_array ) ) {
									foreach( $subjects_array as $key => $subject ) {
										?>
										<option value="<?php echo $key; ?>"><?php echo $subject; ?></option>
										<?php
									}
								}
								?>	
							</select>
							<div class="mld-add-subject-text-wrapper">
								<div class="mld-subject-text"><?php echo __( 'Add New Subject', MLD_TEXT_DOMAIN ); ?></div>
								<div class="mld-add-sub-field">
									<input type="text" placeholder="<?php echo __( 'Enter Subject Name', MLD_TEXT_DOMAIN ); ?>" class="mld-new-subject">
									<button class="mld-subject-add-btn"><?php echo __( 'Add', MLD_TEXT_DOMAIN ); ?></button>
								</div>
							</div>
						</div>
					</div>
					<div class="mld-form-fields-wrapper">
						<?php
						echo self::mld_get_select_html( 'Select availability', 'mld-availability' );
						echo self::mld_get_select_html( 'DBS', 'mld-dbs' );
						?>
					</div>
					<div class="mld-registration-table">
						<div class="mld-new-row">
							<button><?php echo __( 'Education Details', MLD_TEXT_DOMAIN ); ?></button>
							<button class="mld-add-new-row"><?php echo __( 'Add New Row', MLD_TEXT_DOMAIN ); ?></button>
						</div>
						<?php
						echo self::mld_get_registration_table( 1, [ 'Date', 'College / A Level', 'Courses / Subjects', 'Status ( Pass/ Fail/ Pending )','Delete' ], 'mld-college-education' );
						?>
						<div class="mld-new-row">
							<button class="mld-add-new-row mld-add-new-without-btn"><?php echo __( 'Add New Row', MLD_TEXT_DOMAIN ); ?></button>
						</div>
						<?php
						echo self::mld_get_registration_table( 1, [ 'Date', 'University', 'Subjects', 'Qualification ( Degree/ Masters/ Doctorate )', 'Delete' ], 'mld-university-education' );
						?>
						<div class="mld-new-row">
							<button><?php echo __( 'Experience', MLD_TEXT_DOMAIN ); ?></button>
							<button class="mld-add-new-row"><?php echo __( 'Add New Row', MLD_TEXT_DOMAIN ); ?></button>
						</div>
						<table class="mld-experience-years">
							<thead>
								<tr>
									<th><?php echo __( 'Subjects Taught', MLD_TEXT_DOMAIN ); ?></th>
									<th><?php echo __( 'Level', MLD_TEXT_DOMAIN ); ?></th>
									<th><?php echo __( 'Number of Students', MLD_TEXT_DOMAIN ); ?></th>
									<th><?php echo __( 'Percentage Pass', MLD_TEXT_DOMAIN ); ?></th>
									<th><?php echo __( 'Delete', MLD_TEXT_DOMAIN ); ?></th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td contenteditable="true">
										<select class="mld-subject-taught-dropdown">
											<option value=""><?php echo __( 'Select Taught Subjects', MLD_TEXT_DOMAIN ); ?></option>
										</select>
									</td>
									<td>
										<select>
											<option value=""><?php echo __( 'Select Level', MLD_TEXT_DOMAIN );?></option>
											<option value="Primary-School"><?php echo __( 'Primary School', MLD_TEXT_DOMAIN ); ?></option>
											<option value="Secondary-School"><?php echo __( 'Secondary School', MLD_TEXT_DOMAIN ); ?></option>
											<option value="Sixth-Form"><?php echo __( 'Sixth Form', MLD_TEXT_DOMAIN ); ?></option>
											<option value="Office-Staff"><?php echo __( 'Office Staff', MLD_TEXT_DOMAIN ); ?></option>
											<option value="Not-Applicable"><?php echo __( 'Not Applicable', MLD_TEXT_DOMAIN ); ?></option>
											<option value="Other"><?php echo __( 'Other', MLD_TEXT_DOMAIN ); ?></option>
										</select>
									</td>
									<td>
										<select>
											<option value=""><?php echo __( 'Number of students', MLD_TEXT_DOMAIN ); ?></option>
											<option value="100+">100+</option>
											<option value="200+">200+</option>
											<option value="300+">300+</option>
											<option value="400+">400+</option>
											<option value="500+">500+</option>
											<option value="600+">600+</option>
											<option value="700+">700+</option>
											<option value="800+">800+</option>
											<option value="900+">900+</option>
											<option value="1000+">1000+</option>
										</select>
									</td>
									<td>
										<select>
											<option value=""><?php echo __( 'Number of percentage', MLD_TEXT_DOMAIN ); ?></option>
											<option value="40%-59%">40-59%</option>
											<option value="60%-79%">60-79%</option>
											<option value="80%-100%">80-100%</option>
										</select>
									</td>
									<td><i class="fa fa-trash mld-delete-table-row"></i></td>
								</tr>
							</tbody>
						</table>
						<div class="mld-recaptcha">
							<div class="g-recaptcha" 
							data-sitekey="6Ld590YpAAAAAEC6-_dkF8hSWacRkB9TcaVMNyU0" 
							></div>
						</div>
					</div>
					<div class="mld-registration-submit-btn">
						<button class="mld-reg-back-btn"><?php echo __( 'Back', MLD_TEXT_DOMAIN ); ?></button>
						<button class="mld-registration-sub-btn"><?php echo __( 'Submit', MLD_TEXT_DOMAIN ); ?> <img src="<?php echo MLD_ASSETS_URL.'images/spinner.gif' ?>" class="mld-staff-loader"> </button>
					</div>
					<div class="mld-teacher-registration-message"></div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * create a function to get title html
	 */
	public function mld_get_registration_title_html( $value ) {

		ob_start();

		?>

		<div class="mld-registration-surname">
			<input type="radio" value="<?php echo $value; ?>" class="mld-surname-radio">
			<label><?php echo ucwords( $value ); ?></label>
		</div> 

		<?php

		$content = ob_get_contents();
		ob_get_clean();
		return $content;
	}

	/**
	 * create a function to get yes/no select
	 */
	public static function mld_get_select_html( $default_option, $class, $answer = '' ) {

		ob_start();

		?>
		<div class="mld-form-field">
			<select class="<?php echo $class; ?>">
				<option value=""><?php echo $default_option; ?></option>
				<option value="no" <?php echo selected( 'no', $answer, true ); ?>><?php echo __( 'No', 'myrtle-learning-dashboard' ); ?></option>
				<option value="yes" <?php echo selected( 'yes', $answer, true ); ?>><?php echo __( 'Yes', 'myrtle-learning-dashboard' ); ?></option>
			</select>
		</div>
		<?php

		$content = ob_get_contents();
		ob_get_clean();
		return $content;
	}

	/**
	 * create a function to get form fields
	 */
	public static function mld_get_form_fields( $title, $important, $type, $class ) {

		ob_start();
		?>
		<div class="mld-form-field">
			<label><?php echo $title; ?></label>
			<?php
			if( 'yes' == $important ) {
				?>
				<span class="mld-imporant">*</span>
				<?php
			}
 			?>
			<p>
				<input type="<?php echo $type; ?>" placeholder="<?php echo $title; ?>" class="mld-<?php echo $type.' '.$class; ?>">
			</p>
		</div>
		<?php
		$content = ob_get_contents();
		ob_get_clean();
		return $content;
	}

	/**
	 * create a function to get registration form table
	 */
	public function mld_get_registration_table( $row, $t_header, $class ) {

		ob_start();
		
		?>
		<table class="<?php echo $class; ?>">
			<thead>
				<tr>
					<?php
					foreach( $t_header as $headig ) {
						?>
						<th><?php echo $headig; ?></th>
						<?php
					}
					?>
				</tr>
			</thead>
			<tbody>
					<?php
					for ($x = 1; $x <= $row; $x++) {
						?>
						<tr>
							<?php
							foreach( $t_header as $_data ) {
								?>
								<td contenteditable="true">
									<?php
									if( 'Delete' == $_data ) {
										?>
										<i class="fa fa-trash mld-delete-table-row"></i>
										<?php
									}

									if( 'Date' == $_data ) {
										?>
										<input type="date">
										<?php
									}
									?>
								</td>
								<?php
							}
							?>
						</tr>
						<?php
					}
					?>
			</tbody>
		</table>
		<?php 

		$content = ob_get_contents();
		ob_get_clean();
		return $content;
	}

	/**
	 * create a function to get add new html
	 */
	public static function mld_get_add_row_html() {
		ob_start();
		?>
		<div class="mld-new-row">
			<button>Add New Row</button>
		</div>
		<?php
		$content = ob_get_contents();
		ob_get_clean();
		return $content;
	}
}

Myrtle_Staff_Template::instance();

/**
 * create full pdf
 */
add_action( 'wp', 'wpe_generate_staff_profile' );
function wpe_generate_staff_profile() {

	global $wpdb;

	$group_leader_id = isset( $_GET['mld_group_leader_id'] ) ? $_GET['mld_group_leader_id'] : 0;

	if( ! $group_leader_id ) {
		return;	
	}

	$get_teacher_college_data = get_user_meta( $group_leader_id, 'mld-teacher-college-info', true );
	$get_teacher_uni_data = get_user_meta( $group_leader_id, 'mld-teacher-uni-info', true );
	$get_teacher_basic_data = get_user_meta( $group_leader_id, 'mld-teacher-basic-info', true );
	$get_teacher_experience_data = get_user_meta( $group_leader_id, 'mld-teacher-experience-info', true );
	$table_name = $wpdb->prefix.'users';
	$t_name = $wpdb->get_results( $wpdb->prepare( "
		SELECT display_name FROM $table_name WHERE
		ID = %d", $group_leader_id ) );
	$t_name = ucwords( $t_name[0]->display_name );
	$t_ex_years = isset( $get_teacher_basic_data['experience'] ) ? $get_teacher_basic_data['experience'] : '';
	$t_personal_statement = isset( $get_teacher_basic_data['personal_statement'] ) ? $get_teacher_basic_data['personal_statement'] : '';
	$t_availability = isset( $get_teacher_basic_data['availability'] ) ? $get_teacher_basic_data['availability'] : '';
	$t_dbs = isset( $get_teacher_basic_data['dbs'] ) ? $get_teacher_basic_data['dbs'] : 'No';
	$user_avatar_data = get_avatar_data( $group_leader_id );
	$avatar_url = isset( $user_avatar_data['url'] ) ? $user_avatar_data['url'] : '';
	$subjects = get_user_meta( $group_leader_id, 'mld_teacher_selected_subjects', true );
	$tcpdf_light_gray = array(100, 100, 100);
	// require_once MLD_INCLUDES_DIR . '/lib/PDF/tcpdf.php';
	class MYPDF extends TCPDF {

		public function Header() {

			// $this->Rect(0, 0, $this->getPageWidth(),$this->getPageHeight(), 'DF', "",  array(51,87, 33));
		}

		public function Footer() {

			// Position at 15 mm from bottom
			$this->SetY(-15);
			// Set font
			$this->SetFont('helvetica', 'I', 8);
			// Page number
			$this->Cell(0, 10, '------------------ www.myrtlelearning.com ------------------', 0, false, 'C', 0, '', 0, false, 'T', 'M');
		}
	}

	ob_start();
	?>
	<div class="mld-staff-pdf-header">
		<table>
			<tr>
				<td width="37%" style="background-color: #18440a; border: 1px solid #18440a;"></td>
				<td width="59%" style="background-color: #18440a; border: 1px solid #18440a;"></td>
				<td width="4%" style="background-color: #fcb408; border: 1px solid #fcb408;"></td>
			</tr>
			<tr>
				<td width="4%" style="background-color: #18440a; border: 1px solid #18440a;"></td>
				<td width="33%" style="background-color: #18440a; border: 1px solid #18440a;">
					<?php
					$logo_url = MLD_ASSETS_URL.'images/logo_white-1.png'
					?>
					<img src="<?php echo $logo_url; ?>" style="width: 150px;">
				</td>
				<td width="59%" style="background-color: #18440a; color: white; border: 1px solid #18440a;">
					<table>
						<tr>
							<td width="7%" style="border: 1px solid #18440a;"></td>
							<td width="93%" style="border: 1px solid #18440a;"></td>
						</tr>
						<tr>
							<td width="7%" style="border: 1px solid #18440a;"></td>
							<td width="93%" style="font-size: 20px; border: 1px solid #18440a;"><?php echo __( 'STAFF PROFILES', 'myrtle-learning-dashboard' ); ?></td>
						</tr>
					</table>
				</td>
				<td width="4%" style="background-color: #fcb408; border: 1px solid #fcb408;"></td>
			</tr>
			<tr>
				<td width="37%" style="background-color: #18440a; border: 1px solid #18440a;"></td>
				<td width="59%" style="background-color: #18440a; border: 1px solid #18440a;"></td>
				<td width="4%" style="background-color: #fcb408; border: 1px solid #fcb408;"></td>
			</tr>
		</table>
	</div>
	<div class="staff-pdf-header" style="background-color: #f0f3f6">
		<br>
		<table>
			<thead>
				<tr>
					<td width="4%"></td>
					<td width="29.66%">
						<?php 
						$user_avatar_url = get_user_meta( $group_leader_id, 'mld_user_avatar', true );

						if( ! $user_avatar_url ) {

							$table_name = $wpdb->prefix.'e_submissions_values';
							$user_email = $wpdb->get_var($wpdb->prepare(
								"SELECT user_email FROM {$wpdb->users} WHERE ID = %d",
								$group_leader_id
							));
							$submission = $wpdb->get_results( "SELECT submission_id FROM $table_name WHERE value = '".$user_email."' " );
							$submission = end( $submission );
							$submission_id = isset( $submission->submission_id ) ? intval( $submission->submission_id ) : 0;
							$key = 'field_8427b4d';
							$submission_data = $wpdb->get_results( "SELECT submission.value as val FROM $table_name as submission WHERE submission.key = '".$key."' AND submission.submission_id = $submission_id " );
							$user_avatar_url = $submission_data[0]->val;
						}

						if( $user_avatar_url ) {
							?>
							<img src="<?php echo esc_url( $user_avatar_url ); ?>" 
						     alt="User Avatar" 
						     style="display: block !important; 
						            margin: 0 auto !important; 
						            height: 150px !important; 
						            width: 150px !important; 
						            object-fit: cover !important; 
						            border-radius: 50% !important;">

							<?php
						} else {

							$user_avatar_data = get_avatar_data( $group_leader_id );
							$avatar_url = isset( $user_avatar_data['url'] ) ? $user_avatar_data['url'] : '';
							?>
							<img src="<?php echo esc_url( $avatar_url ); ?>" 
						     alt="User Avatar" 
						     style="display: block !important; 
						            margin: 0 auto !important; 
						            height: 150px !important; 
						            width: 150px !important; 
						            object-fit: cover !important; 
						            border-radius: 50% !important;">
							<?php
						}
						?>
					</td>
					<td width="2%"></td>
					<td width="29.66%">
						<div class="mld-name-label">
							<?php echo __( 'Name :', 'myrtle-learning-dashboard' ); ?>
							<?php echo $t_name; ?>
						</div>
						<div class="mld-name-label">
							<?php echo __( 'Years of Teachings :', 'myrtle-learning-dashboard' ); ?>
							<?php echo $t_ex_years; ?>
						</div>
						<div class="mld-name-label">
							<?php echo __( 'Subjects :', 'myrtle-learning-dashboard' ); ?>
							<?php 
							if( is_array( $subjects ) && ! empty( $subjects ) ) {
								echo str_replace( '-', ' ', implode( ',', $subjects ) );
							} 
							?>
						</div>
					</td>
					<td width="2%"></td>
					<td width="28.66%">
						<!-- <div class="mld-head-right"> -->
							<div class="mld-availability">
								<?php
								if( $t_availability && 'yes' == $t_availability ) {
									?>
									<img src="https://myrtlelearning.com/wp-content/uploads/2022/12/checked-radio.png" style="height: 12px;">
									<?php
								} else {
									?>	
									<img src="https://myrtlelearning.com/wp-content/uploads/2022/12/unchecked-radio.png" style="height: 12px;">
									<?php
								}
								?>
								<label><?php echo __( 'Available', 'myrtle-learning-dashboard' ); ?></label>
							</div>
							<div class="mld-unavailability">
								<?php
								if( $t_availability && 'yes' == $t_availability ) {
									?>
									<img src="https://myrtlelearning.com/wp-content/uploads/2022/12/unchecked-radio.png" style="height: 12px;">
									<?php
								} else {
									?>	
									<img src="https://myrtlelearning.com/wp-content/uploads/2022/12/checked-radio.png" style="height: 12px;">
									<?php
								}
								?>
								<label><?php echo __( 'Unavailable', 'myrtle-learning-dashboard' ); ?></label>
							</div>
							<div class="mld-dbs">
								<label><?php echo __( 'DBS :', 'myrtle-learning-dashboard' ); ?></label>
								<?php echo $t_dbs; ?>
							</div>
						<!-- </div> -->
					</td>
					<td width="4%"></td>
				</tr>
			</thead>
		</table>
	</div>
	<div class="mld-group-leader-statement" style="border:1px solid #f0f3f6;">
		<br>
		<table>
			<thead>
				<tr>
					<td width="4%"></td>
					<td style="background-color: #18440a; text-align: center; color: white;">
						<div style="font-size: 8px; color: #18440a">testing content</div>
						<?php echo __( 'Personal Statement', 'myrtle-learning-dashboard' ); ?>
						<div style="font-size: 8px; color: #18440a;">testing content</div>
					</td>
					<td></td>
					<td></td>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td width="4%"></td>
					<td width="96%">
						<?php
						echo $t_personal_statement;
						?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<?php
	$content = ob_get_contents();
	ob_get_clean();

	ob_start();
	?>
	<div class="mld-college-details" style="background-color: #f0f3f6;">
		<br>
		<table>
			<thead>
				<tr>
					<th width="4%"></th>
					<th style="background-color: #18440a; text-align: center; color: white;">
						<div style="font-size: 8px; color: #18440a">testing content</div>
						<?php echo __( 'Educational Details', 'myrtle-learning-dashboard' ); ?>
						<div style="font-size: 8px; color: #18440a;">testing content</div>
					</th>
					<th></th>
					<th></th>
				</tr>
			</thead>
		</table>
		<br>
		<br>
		<table>
			<thead>
				<tr style="background-color: #18440a; color: white;">
					<th width="4%" style="background-color: #f0f3f6;"></th>
					<th width="23%" style="text-align: center;"><?php echo __( 'Date', 'myrtle-learning-dashboard' ); ?></th>
					<th width="23%" style="text-align: center;"><?php echo __( 'College / A Level', 'myrtle-learning-dashboard' ); ?></th>
					<th width="23%" style="text-align: center;"><?php echo __( 'Courses / Subsjecs', 'myrtle-learning-dashboard' ); ?></th>
					<th width="23%" style="text-align: center;"><?php echo __( 'Staus (Pass/Fail/Pending)', 'myrtle-learning-dashboard' ); ?></th>
					<th width="4%" style="background-color: #f0f3f6;"></th>
				</tr>
			</thead>
			<tbody>
				<?php 
				if( is_array( $get_teacher_college_data ) && ! empty( $get_teacher_college_data ) ) {
					$no = 0;
					foreach( $get_teacher_college_data as $college_data ) {

						$t_colg_data = isset( $get_teacher_college_data[$no] ) ? $get_teacher_college_data[$no] : [];
						?>
						<tr>
							<?php
							if( is_array( $t_colg_data ) && ! empty( $t_colg_data ) ) {
								foreach( $t_colg_data as $key => $col_data ) {

									if( 0 == $key ) {
										?>
										<td width="4%" style="border-right: 1px solid black;"></td>
										<?php
									}	

									?>
									<td width="23%" style="text-align: center; border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black;"><?php echo $col_data; ?></td>
									<?php
									if( 4 == $key ) {
										?>
										<td width="4%"></td>
										<?php
									}	
								}
							}
							?>
						</tr>
						<?php
						$no++;
					}
				}
				?>
			</tbody>
		</table>
		<br>
		<br>
		<br>
		<div class="mld-uni-details">
			<table>
				<thead>
					<tr style="background-color: #18440a; color: white;">
						<th width="4%" style="background-color: #f0f3f6;"></th>
						<th width="23%" style="text-align: center;"><?php echo __( 'Date', 'myrtle-learning-dashboard' ); ?></th>
						<th width="23%" style="text-align: center;"><?php echo __( 'University', 'myrtle-learning-dashboard' ); ?></th>
						<th width="23%" style="text-align: center;"><?php echo __( 'Subsjecs', 'myrtle-learning-dashboard' ); ?></th>
						<th width="23%" style="text-align: center;"><?php echo __( 'Qualification (Degree/Masters/Doctorate)', 'myrtle-learning-dashboard' ); ?></th>
						<th width="4%" style="background-color: #f0f3f6;"></th>
					</tr>
				</thead>
				<tbody>
					<?php 
					if( is_array( $get_teacher_uni_data ) && ! empty( $get_teacher_uni_data ) ) {
						$no = 0;
						foreach( $get_teacher_uni_data as $t_uni_data ) {

							$t_colg_data = isset( $get_teacher_college_data[$no] ) ? $get_teacher_college_data[$no] : [];
							?>
							<tr>
								<?php
								if( is_array( $t_uni_data ) && ! empty( $t_uni_data ) ) {
									foreach( $t_uni_data as $key => $uni_data ) {

										if( 0 == $key ) {
											?>
											<td width="4%" style="border-right: 1px solid black;"></td>
											<?php
										}	

										?>
										<td width="23%" style="text-align: center; border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black;"><?php echo $uni_data; ?></td>
										<?php
										if( 4 == $key ) {
											?>
											<td width="4%"></td>
											<?php
										}	
									}
								}
								?>
							</tr>
							<?php
							$no++;
						}
					}
					?>
				</tbody>
			</table>
		</div>
	</div>

	<div class="mld-experience-details" style="background-color: #f0f3f6;">
		<br>
		<table>
			<thead>
				<tr>
					<th width="4%"></th>
					<th style="background-color: #18440a; text-align: center; color: white;">
						<div style="font-size: 8px; color: #18440a">testing content</div>
						<?php echo __( 'Experience', 'myrtle-learning-dashboard' ); ?>
						<div style="font-size: 8px; color: #18440a;">testing content</div>
					</th>
					<th></th>
					<th></th>
				</tr>
			</thead>
		</table>
		<br>
		<br>
		<table>
			<thead>
				<tr style="background-color: #18440a; color: white;">
					<th width="4%" style="background-color: #f0f3f6;"></th>
					<th width="23%" style="text-align: center;"><?php echo __( 'Subject Taught', 'myrtle-learning-dashboard' ); ?></th>
					<th width="23%" style="text-align: center;"><?php echo __( 'Level', 'myrtle-learning-dashboard' ); ?></th>
					<th width="23%" style="text-align: center;"><?php echo __( 'Number of Students', 'myrtle-learning-dashboard' ); ?></th>
					<th width="23%" style="text-align: center;"><?php echo __( 'Percentage Pass', 'myrtle-learning-dashboard' ); ?></th>
					<th width="4%" style="background-color: #f0f3f6;"></th>
				</tr>
			</thead>
			<tbody>
				<?php 
				if( is_array( $get_teacher_experience_data ) && ! empty( $get_teacher_experience_data ) ) {

					$no = 0;
					foreach( $get_teacher_experience_data as $experience_data ) {

						$t_experience_data = isset( $get_teacher_experience_data[$no] ) ? $get_teacher_experience_data[$no] : [];
						?>
						<tr>
							<?php
							if( is_array( $t_experience_data ) && ! empty( $t_experience_data ) ) {
								foreach( $t_experience_data as $key => $experience_data ) {

									if( $key > 3 ) {
										continue;
									}

									if( 0 == $key ) {
										?>
										<td width="4%" style="border-right: 1px solid black;"></td>
										<?php
									}	

									?>
									<td width="23%" style="text-align: center; border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black;"><?php echo $experience_data; ?></td>
									<?php
									if( 3 == $key ) {
										?>
										<td width="4%"></td>
										<?php
									}	
								}
							}
							?>
						</tr>
						<?php
						$no++;
					}
				}
				?>
			</tbody>
		</table>
	</div>
	<?php
	$content_two = ob_get_contents();
	ob_get_clean();

	$page_height = strlen( $content ) / 10;
    $pdf_page_format = PDF_PAGE_FORMAT;
    $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, $pdf_page_format, true, 'UTF-8', false);
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor( 'LRC' );
    $pdf->SetTitle( 'LRC Course Outline' );
    $pdf->SetSubject( 'LRC Outline' );
    // set header and footer fonts
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
    $pdf->setHeaderData( '', 0, '', '', [ 0, 0, 0 ], [ 255, 255, 255 ] );
    // set default monospaced font
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    // set margins
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    // set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    // set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    // add a page
    $pdf->AddPage();
    // output the HTML content
    $pdf->writeHTML( $content, true, false, true, false, '' );

    $pdf->AddPage();
    // output the HTML content
    $pdf->writeHTML( $content_two, true, false, true, false, '' );
    // reset pointer to the last page
    $pdf->lastPage();
    ob_clean();
    //Close and output PDF document
    $pdf->Output( 'pdf_course_outline', 'I' );
    die();
}
