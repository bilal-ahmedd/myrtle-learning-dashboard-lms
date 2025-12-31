<?php
/**
 * Notification templates
 */

if( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class Myrtle_Staff_File
 */
class Myrtle_Staff {

	/**
	 * @var self
	 */
	private static $instance = null;

	/**
	 * @since 1.0
	 * @return $this
	 */
	public static function instance() {

		if ( is_null( self::$instance ) && ! ( self::$instance instanceof Myrtle_Staff ) ) {
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
		add_action( 'wp_ajax_delete_teacher_form', [ $this, 'mld_delete_teacher_form' ] );
		add_action( 'wp_ajax_mld_upload_teacher_form', [ $this, 'mld_upload_teacher_form' ] );
		add_action( 'show_user_profile', [ $this, 'mld_add_teacher_register_fields' ] );
		add_action( 'edit_user_profile', [ $this, 'mld_add_teacher_register_fields' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'mld_enqueue_scripts' ] );
		add_action( 'wp_ajax_update_teacher_edit_profile', [ $this, 'mld_update_teacher_edit_profile' ] );
		add_action( 'wp_ajax_update_user_role', [ $this, 'mld_update_user_role' ] );
		add_action( 'wp_ajax_deny_pending_user', [ $this, 'mld_deny_pending_user' ] );
		add_action( 'wp_ajax_accept_subscriber_user', [ $this, 'mld_accept_subscriber_user' ] );
		add_action( 'wp_ajax_deny_subscriber_user', [ $this, 'mld_deny_subscriber_user' ] );
		add_action( 'wp_ajax_accept_pending_teacher', [ $this, 'mld_accept_pending_teacher' ] );
		add_action( 'wp_ajax_deny_pending_teacher', [ $this, 'mld_deny_pending_teacher' ] );
		add_action( 'wp_ajax_accept_pending_student_user', [ $this, 'mld_accept_pending_student_user' ] );
		add_action( 'wp_ajax_deny_pending_student_user', [ $this, 'mld_deny_pending_student_user' ] );
		add_filter( 'exms_dashboard_tabs', [ $this, 'exms_my_staff_tab' ] );
	}

	public function exms_my_staff_tab( $tabs ) {

		$tabs['exms_my_staff'] = array(
			'label' => __( 'My Staff Profile', 'exms' ),
			'icon'  => 'dashicons-admin-users',
		);
		return $tabs;
	}

	/**
	 * deni pending student & send an email
	 */
	public function mld_deny_pending_student_user() {

		$user_id = isset( $_POST['user_id'] ) ? $_POST['user_id'] : 0;

		if( ! $user_id ) {
			wp_die();
		}	

		wp_delete_user( $user_id );
		wp_die();
	}

	/**
	 * accept pending user & send an email
	 */
	public function mld_accept_pending_student_user() {

		global $wpdb;

		$user_id = isset( $_POST['user_id'] ) ? $_POST['user_id'] : 0;

		if( ! $user_id ) {
			wp_die();
		}

		$user_object = new WP_User( $user_id );
		$user_object->set_role('student');

		$headers = array('Content-Type: text/html; charset=UTF-8');
		$email = mld_get_user_email( $user_id );
		$user = get_user_by( 'email', $email );
		$username = isset( $user->user_login ) ? $user->user_login : '';
		$key = get_password_reset_key( $user );
		$reset_link = site_url() . '/resetpass/?action=rp&key=' . $key . '&login=' . $username;
		$header_url = MLD_ASSETS_URL.'images/header.PNG';
		$footer_url = MLD_ASSETS_URL.'images/footer.PNG';
		$reset_img = MLD_ASSETS_URL.'images/password-reset.PNG';
		$calendar_img = MLD_ASSETS_URL.'images/calendar-booking.PNG';
		$student_congratulation_content .= '<img src="'.$header_url.'" style="width: 99%;">';
		$student_congratulation_content .= '<p></p>';

		$student_congratulation_content .= '<div style="font-size: 20px; color: #365249;">'.__( 'Student Registration', 'myrtle-learning-dashboard' ).'</div>';
		$student_congratulation_content .= '<div style="font-size: 20px; color: #365249; font-weight: 600;">'.__( 'Congratulations Email', 'myrtle-learning-dashboard' ).'</div>';

		$student_congratulation_content .= '<div style="font-size: 15px; color: #365249; margin: 15px 0 15px 0;">'.__( 'Dear '.ucwords( $username ).' ', 'myrtle-learning-dashboard' ).'</div>';
		$student_congratulation_content .= '<div style="font-size: 15px; color: #365249; margin: 15px 0 15px 0;">'.__( 'Following a review of your assessment/interview outcome with us, we would like to offer you a place at Myrtle Learning,', 'myrtle-learning-dashboard' ).'</div>';
		$student_congratulation_content .= '<div style="font-size: 20px; color: #365249; margin: 15px 0 15px 0; font-weight: 600;">'.__( 'CONGRATULATIONS!', 'myrtle-learning-dashboard' ).'</div>';

		$student_congratulation_content .= '<div style="font-size: 15px; color: #365249; margin: 15px 0 15px 0;">'.__( 'You can access our system using your username and password you set up for the initial assessment. You can always reset your password at any time if required.', 'myrtle-learning-dashboard' ).'</div>';
		$student_congratulation_content .= '<div style="font-size: 15px; color: #365249; margin: 15px 0 15px 0;">'.__( 'A member of our team will be getting in touch to confirm and discuss the processes leading to you starting your learning journey with Myrtle Learning.', 'myrtle-learning-dashboard' ).'</div>';
		$student_congratulation_content .= '<div style="font-size: 15px; color: #365249; margin: 15px 0 15px 0;">'.__( 'Please use the link below to book your most convenient for this meeting.', 'myrtle-learning-dashboard' ).'</div>';

		$student_congratulation_content .= '<a href="#"><img src="'.$calendar_img.'" style="width: 300px;"></a>';
		$student_congratulation_content .= '<div style="font-size: 15px; color: #365249; margin: 15px 0 15px 0;">'.__( 'Thank You', 'myrtle-learning-dashboard' ).'</div>';
		$student_congratulation_content .= '<div style="font-size: 20px; color: #365249; margin: 15px 0 15px 0; font-weight: 600;">'.__( 'The Myrtle Learning <br> Recruitment Team', 'myrtle-learning-dashboard' ).'</div>';

		$student_congratulation_content .= '<p></p>';
		$student_congratulation_content .= '<img src="'.$footer_url.'" style="width: 99%;">';
		wp_mail( $email, 'Staff Registration', $student_congratulation_content, $headers );

		wp_die();
	}

	/**
	 * deny pending teacher & send an email
	 */
	public function mld_deny_pending_teacher() {

		global $wpdb;

		$user_id = isset( $_POST['user_id'] ) ? $_POST['user_id'] : 0;

		if( ! $user_id ) {
			wp_die();
		}	

		$headers = array('Content-Type: text/html; charset=UTF-8');
		$email = mld_get_user_email( $user_id );
		$user = get_user_by( 'email', $email );
		$username = isset( $user->user_login ) ? $user->user_login : '';
		$header_url = MLD_ASSETS_URL.'images/header.PNG';
		$footer_url = MLD_ASSETS_URL.'images/footer.PNG';
		$regret_content .= '<img src="'.$header_url.'" style="width: 99%;">';
		$regret_content .= '<p></p>';
		$regret_content .= '<div style="font-size: 20px; color: #365249;">'.__( 'Staff Registration', 'myrtle-learning-dashboard' ).'</div>';
		$regret_content .= '<div style="font-size: 20px; color: #365249; font-weight: 600;">'.__( 'Regret Email', 'myrtle-learning-dashboard' ).'</div>';

		$regret_content .= '<div style="font-size: 15px; color: #365249; margin: 15px 0 15px 0;">'.__( 'Dear '.$username.',', 'myrtle-learning-dashboard' ).'</div>';
		$regret_content .= '<div style="font-size: 15px; color: #365249; margin: 15px 0 15px 0;">'.__( 'Following a review of your interview with Myrtle Learning, we regret to inform you that we cannot proceed with your application as a staff of Myrtle Learning.', 'myrtle-learning-dashboard' ).'</div>';
		$regret_content .= '<div style="font-size: 15px; color: #365249; margin: 15px 0 15px 0;">'.__( 'Even though you are great in terms of area of expertise, your skill set does not meet the current needs of Myrtle Learning.', 'myrtle-learning-dashboard' ).'</div>';
		$regret_content .= '<div style="font-size: 15px; color: #365249; margin: 15px 0 15px 0;">'.__( 'We would however like to keep your details on our system to enable us contact should any role become available that requires your skill set.', 'myrtle-learning-dashboard' ).'</div>';
		$regret_content .= '<div style="font-size: 15px; color: #365249; margin: 15px 0 15px 0;">'.__( 'Please do not hesitate to let us know if you like us to delete your details from our database.', 'myrtle-learning-dashboard' ).'</div>';
		$regret_content .= '<div style="font-size: 15px; color: #365249; margin: 15px 0 15px 0; font-weight: 600;">'.__( 'We wish you all the best in any future endeavors.', 'myrtle-learning-dashboard' ).'</div>';
		$regret_content .= '<div style="font-size: 15px; color: #365249; margin: 15px 0 15px 0;">'.__( 'Thank You', 'myrtle-learning-dashboard' ).'</div>';
		$regret_content .= '<div style="font-size: 20px; color: #365249; margin: 15px 0 15px 0; font-weight: 600;">'.__( 'The Myrtle Learning <br> Recruitment Team', 'myrtle-learning-dashboard' ).'</div>';
		$regret_content .= '<p></p>';
		$regret_content .= '<img src="'.$footer_url.'" style="width: 99%;">';

		wp_mail( $email, 'Staff Registration', $regret_content, $headers );
		wp_delete_user( $user_id );
		wp_die();
	}

	/**
	 * accept pending teacher & send an email
	 */
	public function mld_accept_pending_teacher() {

		global $wpdb;

		$user_id = isset( $_POST['user_id'] ) ? $_POST['user_id'] : 0;

		if( ! $user_id ) {
			wp_die();
		}

		$user_object = new WP_User( $user_id );
		$user_object->set_role('group_leader');
		$headers = array('Content-Type: text/html; charset=UTF-8');
		$email = mld_get_user_email( $user_id );
		$user = get_user_by( 'email', $email );
		$username = $user->user_login;
		$key = get_password_reset_key( $user );
		$reset_link = site_url() . '/resetpass/?action=rp&key=' . $key . '&login=' . $username;
		$header_url = MLD_ASSETS_URL.'images/header.PNG';
		$footer_url = MLD_ASSETS_URL.'images/footer.PNG';
		$reset_img = MLD_ASSETS_URL.'images/password-reset.PNG';
		$calendar_img = MLD_ASSETS_URL.'images/calendar-booking.PNG';
		$teacher_congratulation_content .= '<img src="'.$header_url.'" style="width: 99%;">';
		$teacher_congratulation_content .= '<p></p>';
		$teacher_congratulation_content .= '<div style="font-size: 20px; color: #365249;">'.__( 'Staff Registration', 'myrtle-learning-dashboard' ).'</div>';
		$teacher_congratulation_content .= '<div style="font-size: 20px; color: #365249; font-weight: 600;">'.__( 'Congratulations Email', 'myrtle-learning-dashboard' ).'</div>';

		$teacher_congratulation_content .= '<div style="font-size: 15px; color: #365249; margin: 15px 0 15px 0;">'.__( 'Dear '.ucwords( $username ).' ', 'myrtle-learning-dashboard' ).'</div>';
		$teacher_congratulation_content .= '<div style="font-size: 15px; color: #365249; margin: 15px 0 15px 0;">'.__( 'Following a review of your interview with us, we would like to offer you a role at Myrtle Learning,', 'myrtle-learning-dashboard' ).'</div>';
		$teacher_congratulation_content .= '<div style="font-size: 20px; color: #365249; margin: 15px 0 15px 0; font-weight: 600;">'.__( 'CONGRATULATIONS!', 'myrtle-learning-dashboard' ).'</div>';

		$teacher_congratulation_content .= '<div style="font-size: 15px; color: #365249; margin: 15px 0 15px 0;">'.__( 'Please use the link below to create a password.', 'myrtle-learning-dashboard' ).'</div>';
		$teacher_congratulation_content .= '<div style="font-size: 15px; color: #365249; margin: 15px 0 15px 0;">'.__( 'Username: '.$username.' ', 'myrtle-learning-dashboard' ).'</div>';
		$teacher_congratulation_content .= '<p></p>';
		$teacher_congratulation_content .= '<a href="'.$reset_link.'"><img src="'.$reset_img.'" style="width: 300px;"></a>';
		$teacher_congratulation_content .= '<div style="font-size: 15px; color: #365249; margin: 15px 0 15px 0;">'.__( 'This will give you access to our platform. Please upload all the required documents by selecting the <b>‘Checklist’</b> button at the bottom of the page.', 'myrtle-learning-dashboard' ).'</div>';
		$teacher_congratulation_content .= '<div style="font-size: 15px; color: #365249; margin: 15px 0 15px 0;">'.__( 'A member of our team will be getting in touch to confirm and discuss the processes leading to you starting your empowerment journey with Myrtle Learning as soon as all the required documents are uploaded.', 'myrtle-learning-dashboard' ).'</div>';
		$teacher_congratulation_content .= '<div style="font-size: 15px; color: #365249; margin: 15px 0 15px 0;">'.__( 'If you have any issues with this process, please use the link below to book a meeting with a member of our recruitment team for support.', 'myrtle-learning-dashboard' ).'</div>';

		$teacher_congratulation_content .= '<a href="#"><img src="'.$calendar_img.'" style="width: 300px;"></a>';
		$teacher_congratulation_content .= '<div style="font-size: 15px; color: #365249; margin: 15px 0 15px 0;">'.__( 'Thank You', 'myrtle-learning-dashboard' ).'</div>';
		$teacher_congratulation_content .= '<div style="font-size: 20px; color: #365249; margin: 15px 0 15px 0; font-weight: 600;">'.__( 'The Myrtle Learning <br> Recruitment Team', 'myrtle-learning-dashboard' ).'</div>';

		$teacher_congratulation_content .= '<p></p>';
		$teacher_congratulation_content .= '<img src="'.$footer_url.'" style="width: 99%;">';
		wp_mail( $email, 'Staff Registration', $teacher_congratulation_content, $headers );

		wp_die();	
	}

	/**
	 * deny user and send an email
	 */
	public function mld_deny_subscriber_user() {

		global $wpdb;

		$user_id = isset( $_POST['user_id'] ) ? $_POST['user_id'] : 0;

		if( ! $user_id ) {
			wp_die();
		}

		$email = mld_get_user_email( $user_id );
		$user = get_user_by( 'email', $email );
		$username = isset( $user->user_login ) ? $user->user_login : '';
		$header_url = MLD_ASSETS_URL.'images/header.PNG';
		$footer_url = MLD_ASSETS_URL.'images/footer.PNG';
		$deni_content .= '<img src="'.$header_url.'" style="width: 99%;">';
		$deni_content .= '<p></p>';
		$deni_content .= '<div style="font-size: 20px; color:#365249;">Student Registration</div>';
		$deni_content .= '<div style="font-weight: 600; font-size: 20px; color:#365249;">Denial Email</div>';
		$deni_content .= '<div style="color: #365249; font-size: 15px; margin: 15px 0 15px 0;">Dear '.$username.',</div>';
		$deni_content .= '<div style="color: #365249; font-size: 15px; margin: 15px 0 15px 0;">Following a review of your registration with Myrtle Learning, we regret to
			inform you that, we cannot proceed with your application as a student of
			Myrtle Learning.</div>';
		$deni_content .= '<div style="color: #365249; font-size: 15px; margin: 15px 0 15px 0;">As we eschew the standard of excellence in learning, we ensure that our
			services are tailored to meet the needs of our students but unfortunately,
			on this occasion, we do not think we are in the position to be able to meet
			those expectations.</div>';

		$deni_content .= '<div style="font-weight: 600; font-size: 20px; color: #365249; margin: 15px 0 15px 0;">We wish you all the best in any future endeavors.</div>';
		$deni_content .= '<div style="color: #365249; font-size: 15px; margin: 15px 0 15px 0;">Thank you</div>';
		$deni_content .= '<div style="font-weight: 600; font-size: 20px; color: #365249;">The Myrtle Learning Team</div>';
		$deni_content .= '<p></p>';
		$deni_content .= '<img src="'.$footer_url.'" style="width: 99%;">';
		wp_mail( $email, '[Myrtle Learning] Student Registration
			', stripslashes( $deni_content ), "Content-Type: text/html; charset=UTF-8" );	

		wp_delete_user( $user_id );
		wp_die();
	}

	/**
	 * accept student as a subscriber and send an email
	 */
	public function mld_accept_subscriber_user() {

		global $wpdb;

		$user_id = isset( $_POST['user_id'] ) ? $_POST['user_id'] : 0;

		if( ! $user_id ) {
			wp_die();
		}

		$user_object = new WP_User( $user_id );
		$user_object->set_role('pending_student');
		$email = mld_get_user_email( $user_id );
		$user = get_user_by( 'email', $email );
		$username = isset( $user->user_login ) ? $user->user_login : '';
		$key = get_password_reset_key( $user );
		$reset_link = site_url() . '/resetpass/?action=rp&key=' . $key . '&login=' . $username;

		$header_url = MLD_ASSETS_URL.'images/header.PNG';
		$footer_url = MLD_ASSETS_URL.'images/footer.PNG';
		$calendar_book = MLD_ASSETS_URL.'images/calendar-book.PNG';
		$password_reset = MLD_ASSETS_URL.'images/password-reset.PNG';

		$approval_content .= '<img src="'.$header_url.'" style="width: 99%;">.';
		$approval_content .= '<p></p>';
		$approval_content .= '<div style="color: #365249; font-size: 20px;">Student Registration</div>';
		$approval_content .= '<div style="font-weight: 600; color: #365249; font-size: 20px;">Approval Email</div>';
		$approval_content .= '<div style="color: #365249; font-size: 15px; margin: 15px 0 15px 0;">Dear '.$username.',</div>';
		$approval_content .= '<div style="color: #365249; font-size: 15px; margin: 15px 0 15px 0;">Following a review of your registration with Myrtle Learning, we would like to
			invite you for an initial assessment.</div>';
		$approval_content .= '<div style="color: #365249; font-size: 15px; margin: 15px 0 15px 0;">This can either be done online or in person at Myrtle Learning. Use the link
			below to choose your most convenient day/time for the assessment:</div>';
		$approval_content .= '<a href="#"><img src="'.$calendar_book.'" style="height: 60px;"></a>';
		$approval_content .= '<div style="color: #365249; font-size: 15px; margin: 15px 0 15px 0;">Please use the link below to set your password to be able to gain access
			to the initial assessment.</div>';
		$approval_content .= '<a href="'.$reset_link.'"><img src="'.$password_reset.'" style="height: 60px;"></a>';
		$approval_content .= '<div style="color: #365249; font-size: 15px; margin: 15px 0 15px 0;">A member of our team will be getting in touch to confirm and discuss the
			processes for the initial assessment and answer any questions you may
			have regarding it.</div>';
		$approval_content .= '<div style="color: #365249; font-size: 15px; margin: 15px 0 15px 0;">Thank you</div>';
		$approval_content .= '<div style="font-weight: 600; color: #365249; font-size: 20px;">The Myrtle Learning Team</div>';
		$approval_content .= '<p></p>';
		$approval_content .= '<img src="'.$footer_url.'" style="width: 99%;">';
		wp_mail( $email, '[Myrtle Learning] Student Registration
			', stripslashes( $approval_content ), "Content-Type: text/html; charset=UTF-8" );
		wp_die();
	}

	/**
	 * deny pending user and send deny email
	 */
	public function mld_deny_pending_user() {

		global $wpdb;

		$user_id = isset( $_POST['user_id'] ) ? $_POST['user_id'] : 0;

		if( ! $user_id ) {
			wp_die();
		}

		$headers = array('Content-Type: text/html; charset=UTF-8');
		$email = mld_get_user_email( $user_id );
		$user = get_user_by( 'email', $email );
		$username = isset( $user->user_login ) ? $user->user_login : '';
		$header_url = MLD_ASSETS_URL.'images/header.PNG';
		$footer_url = MLD_ASSETS_URL.'images/footer.PNG';

		$deni_content .= '<img src="'.$header_url.'" style="width: 99%;">';
		$deni_content .= '<p></p>';
		$deni_content .= '<div style="font-size: 20px; color: #365249;">Staff Registration</div>';
		$deni_content .= '<div style="font-size: 20px; color: #365249; font-weight: 600;">Denial Email</div>';
		$deni_content .= '<div style="font-size: 15px; color: #365249; margin: 15px 0 0 0;">Dear '.$username.',</div>';
		$deni_content .= '<div style="font-size: 15px; color: #365249; margin: 15px 0 15px 0;">Following a review of your application with Myrtle Learning, we regret to
			inform you that we cannot proceed with your application as a staff of
			Myrtle Learning.</div>';
		$deni_content .= '<div style="font-size: 15px; color: #365249; margin: 15px 0 15px 0;">Unfortunately, at this point in time, your skill set does not meet our needs
			for the role applied for.</div>';
		$deni_content .= '<div style="font-size: 15px; color: #365249; margin: 15px 0 15px 0;">Please note that this does not prevent you from applying for any future
			roles at Myrtle learning should they become available.</div>';
		$deni_content .= '<div style="font-weight: 600; color: #365249; font-size: 15px; margin: 15px 0 15px 0;">We wish you all the best in any future endeavors.</div>';
		$deni_content .= '<div style="font-size: 15px; margin: 15px 0 15px 0;">Thank you</div>';
		$deni_content .= '<div style="font-weight: 600; color: #365249; font-size: 15px;">The Myrtle Learning <br> Recruitment Team</div>';
		$deni_content .= '<p></p>';
		$deni_content .= '<img src="'.$footer_url.'" style="width: 99%;">';

		wp_mail( $email, 'Denial Email', $deni_content, $headers );
		wp_delete_user( $user_id );
		wp_die();
	}

	/**
	 * approved a pending user and also send approval email
	 */
	public function mld_update_user_role() {

		global $wpdb;

		$user_id = isset( $_POST['user_id'] ) ? $_POST['user_id'] : 0;

		if( ! $user_id ) {
			wp_die();
		}

		$user_object = new WP_User( $user_id );
		$user_object->set_role('pending_teacher');

		$email = $wpdb->get_var( $wpdb->prepare( "SELECT user_email FROM $wpdb->users WHERE ID = %d", $user_id ) );
		$user = get_user_by( 'email', $email );
		$username = $user->user_login;
		$key = get_password_reset_key( $user );
		// $reset_link = site_url() . '/resetpass/?action=rp&key=' . $key . '&login=' . $username;
		$reset_link = esc_url( wp_lostpassword_url() );
		$messages = '<p>Someone has requested a password reset for the following account:</p><p>Site Name: Myrtle Learning</p><p>Username: '.$username.'</p><p>If this was a mistake, ignore this email and nothing will happen.</p><p>To reset your password, visit the following address:</p><a href="'.$reset_link.'">'.$reset_link.'</a>';
		$reset_img = MLD_ASSETS_URL.'images/password-reset.PNG';
		$img_url = MLD_ASSETS_URL.'images/interview-btn.PNG';
		$header_url = MLD_ASSETS_URL.'images/header.PNG';
		$footer_url = MLD_ASSETS_URL.'images/footer.PNG';
		$teacher_approve_content .= '<img src="'.$header_url.'" style="width: 99%;">';
		$teacher_approve_content .= '<p></p>';
		$teacher_approve_content .= '<div style="font-size: 20px; color: #365249;">Staff Registration</div>';
		$teacher_approve_content .= '<div style="font-weight: 600; font-size: 20px; color: #365249;">Staff Approval Email</div>';
		$teacher_approve_content .= '<div style="font-size: 15px; color: #365249; margin: 15px 0 0 0;">Dear '.$username.',</div>';
		$teacher_approve_content .= '<div style="font-size: 15px; color: #365249; margin: 15px 0 15px 0;">Following a review of your application with Myrtle Learning, we would like to
			invite you for an interview.</div>';
		$teacher_approve_content .= '<a href="'.$reset_link.'"><img src="'.$reset_img.'" style="width: 300px;"></a>';
		$teacher_approve_content .= '<div style="font-size: 15px; color: #365249; margin: 15px 0 15px 0;">'.__( 'This will give you access to our platform. Please upload all the required documents by selecting the <b>‘Checklist’</b> button at the bottom of the page.', 'myrtle-learning-dashboard' ).'</div>';
		$teacher_approve_content .= '<div style="font-size: 15px; color: #365249; margin-bottom: 10px; margin: 15px 0 15px 0;">This can either be done online or in person at Myrtle Learning. Use the link below to choose your most convenient day/time for the interview:</div>';
		$teacher_approve_content .= "<a href='#' style='color: white; text-decoration: none; margin: 15px 0 15px 0;'><img src='".$img_url."' style='height: 60px;'></a>";
		$teacher_approve_content .= '<div style="font-size: 15px; color: #365249; margin: 15px 0 15px 0;">A member of our team will be getting in touch to confirm and discuss the
			processes for the interview and answer any questions you may have
			regarding it.</div>';
		$teacher_approve_content .= '<div style="font-size: 15px; color: #365249; margin: 15px 0 15px 0;">Thank you</div>';
		$teacher_approve_content .= '<div style="font-weight: 600; font-size: 15px; color: #365249;">The Myrtle Learning <br> Recruitment Team</div>';
		$teacher_approve_content .= '<p></p>';
		$teacher_approve_content .= '<img src="'.$footer_url.'" style="width: 99%;">';

		wp_mail( $email, '[Myrtle Learning] Staff Approval Email
			', stripslashes( $teacher_approve_content ), "Content-Type: text/html; charset=UTF-8" );

		wp_die();
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
	 * update teacher edit data
	 */
	public function mld_update_teacher_edit_profile() {

		$user_id = isset( $_POST['user_id'] ) ? $_POST['user_id'] : 0;
		$exper = isset( $_POST['exper'] ) ? $_POST['exper'] : 0;
		$subjects = isset( $_POST['subjects'] ) ? $_POST['subjects'] : [];
		$availability = isset( $_POST['availability'] ) ? $_POST['availability'] : 0;
		$personal_statement = isset( $_POST['statement'] ) ? $_POST['statement'] : '';
		$dbs = isset( $_POST['dbs'] ) ? $_POST['dbs'] : 0;
		$email = isset( $_POST['email'] ) ? $_POST['email'] : '';
		$address = isset( $_POST['address'] ) ? $_POST['address'] : '';
		$dob = isset( $_POST['dob'] ) ? $_POST['dob'] : '';
		$county = isset( $_POST['county'] ) ? $_POST['county'] : '';
		$hometel = isset( $_POST['hometel'] ) ? $_POST['hometel'] : '';
		$mobile_number = isset( $_POST['mobileNumber'] ) ? $_POST['mobileNumber'] : '';
		$teacher_college_information = isset( $_POST['college_edu'] ) ? str_replace( '\\', '', $_POST['college_edu'] ) : [];
		$teacher_uni_information = isset( $_POST['uni_edu'] ) ?str_replace( '\\', '', $_POST['uni_edu'] ) : [];
		$teacher_experience = isset( $_POST['experience_edu'] ) ?str_replace( '\\', '', $_POST['experience_edu'] ) : [];

		$user_edit_data = [ 
			'experience'			=> $exper,
			'availability'			=> $availability,
			'dbs'					=> $dbs,
			'address'				=> $address,
			'email'					=> $email,
			'address'				=> $dob,
			'county'				=> $county,
			'hometel'				=> $hometel,
			'mobile_number'			=> $mobile_number,
			'personal_statement'	=> $personal_statement,
		];

		$get_basic_info = get_user_meta( $user_id, 'mld-teacher-basic-info', true );  
		update_user_meta( $user_id, 'mld_teacher_selected_subjects', $subjects );
		
		if( $get_basic_info ) {
			
			$get_basic_info['experience'] = $exper;
			$get_basic_info['availability'] = $availability;
			$get_basic_info['dbs'] = $dbs;
			$get_basic_info['personal_statement'] = $personal_statement;
			$get_basic_info['email'] = $email;
			$get_basic_info['dob'] = $dob;
			$get_basic_info['hometel'] = $hometel;
			$get_basic_info['address'] = $address;
			$get_basic_info['county'] = $county;
			$get_basic_info['mobile_number'] = $mobile_number;

			update_user_meta( $user_id, 'mld-teacher-basic-info', $get_basic_info );
		} else {
			update_user_meta( $user_id, 'mld-teacher-basic-info', $user_edit_data );
		}

		if( $teacher_college_information ) {
			update_user_meta( $user_id, 'mld-teacher-college-info', $teacher_college_information );
		}

		if( $teacher_uni_information ) {
			update_user_meta( $user_id, 'mld-teacher-uni-info', $teacher_uni_information );
		}

		if( $teacher_experience ) {
			update_user_meta( $user_id, 'mld-teacher-experience-info', $teacher_experience );
		}
		wp_die();
	}

	/**
	 * add registratiuon fields 
	 */
	public function mld_add_teacher_register_fields( $user_id ) {

		global $wpdb;

		$refrence_table = $wpdb->prefix . 'mld_refrences';
		$bank_detail_table = $wpdb->prefix.'mld_bank_details';

		$user_id = isset( $user_id->data->ID ) ? intval( $user_id->data->ID ) : 0;
		$user_capability = mld_user_capability( $user_id );

		$bank_detail = $wpdb->get_results( "SELECT bank_detail FROM $bank_detail_table WHERE user_id = '".$user_id."' " );
		$bank_detail = isset( $bank_detail[0]->bank_detail ) ? $bank_detail[0]->bank_detail : '';
		$bank_detail = unserialize( $bank_detail );
		$title = isset( $bank_detail['title'] ) ? $bank_detail['title'] : '';
		$forename = isset( $bank_detail['forename'] ) ? $bank_detail['forename'] : '';
		$surname = isset( $bank_detail['surname'] ) ? $bank_detail['surname'] : '';
		$ni_number = isset( $bank_detail['ni_number'] ) ? $bank_detail['ni_number'] : '';
		$dob = isset( $bank_detail['dob'] ) ? $bank_detail['dob'] : '';
		$home_address = isset( $bank_detail['home_address'] ) ? $bank_detail['home_address'] : '';
		$home_email = isset( $bank_detail['home_email'] ) ? $bank_detail['home_email'] : '';
		$mobile_number = isset( $bank_detail['mobile_number'] ) ? $bank_detail['mobile_number'] : '';
		$subjects = isset( $bank_detail['subjects'] ) ? $bank_detail['subjects'] : '';
		$bank_name = isset( $bank_detail['bank_name'] ) ? $bank_detail['bank_name'] : '';
		$account_holder_name = isset( $bank_detail['account_holder_name'] ) ? $bank_detail['account_holder_name'] : '';
		$sort_code = isset( $bank_detail['sort_code'] ) ? $bank_detail['sort_code'] : '';
		$bank_address = isset( $bank_detail['bank_address'] ) ? $bank_detail['bank_address'] : '';
		$account_number = isset( $bank_detail['account_number'] ) ? $bank_detail['account_number'] : '';
		$certificate_number = isset( $bank_detail['certificate_number'] ) ? $bank_detail['certificate_number'] : '';
		$surname_on_certificate = isset( $bank_detail['username_on_certificate'] ) ? $bank_detail['username_on_certificate'] : '';
		$current_yn = isset( $bank_detail['current_yn'] ) ? $bank_detail['current_yn'] : '';
		$dob_on_certificate = isset( $bank_detail['dob_on_certificate'] ) ? $bank_detail['dob_on_certificate'] : '';
		$internal_use = isset( $bank_detail['internal_use'] ) ? $bank_detail['internal_use'] : '';
		$signature_name = isset( $bank_detail['signature_name'] ) ? $bank_detail['signature_name'] : '';
		$signature_date = isset( $bank_detail['signature_date'] ) ? $bank_detail['signature_date'] : '';
		$resource_name = isset( $bank_detail['resource_name'] ) ? $bank_detail['resource_name'] : '';
		$resource_date = isset( $bank_detail['resource_date'] ) ? $bank_detail['resource_date'] : '';
		$list_a_data = isset( $bank_detail['list_a_data'] ) ? unserialize( $bank_detail['list_a_data'] ) : '';
		$list_b_data = isset( $bank_detail['list_b_data'] ) ? unserialize( $bank_detail['list_b_data'] ) : '';

		$list_data_1 = isset( $list_a_data[0] ) && $list_a_data[0] === 'yes' ? 'checked' : '';
		$list_data_2 = isset( $list_a_data[1] ) && $list_a_data[1] === 'yes' ? 'checked' : '';
		$list_data_3 = isset( $list_a_data[2] ) && $list_a_data[2] === 'yes' ? 'checked' : '';
		$list_data_4 = isset( $list_a_data[3] ) && $list_a_data[3] === 'yes' ? 'checked' : '';
		$list_data_5 = isset( $list_a_data[4] ) && $list_a_data[4] === 'yes' ? 'checked' : '';
		$list_data_6 = isset( $list_a_data[5] ) && $list_a_data[5] === 'yes' ? 'checked' : '';
		$list_data_7 = isset( $list_a_data[6] ) && $list_a_data[6] === 'yes' ? 'checked' : '';
		$list_data_8 = isset( $list_a_data[7] ) && $list_a_data[7] === 'yes' ? 'checked' : '';
		$list_data_9 = isset( $list_a_data[8] ) && $list_a_data[8] === 'yes' ? 'checked' : '';
		$list_data_10 = isset( $list_a_data[9] ) && $list_a_data[9] === 'yes' ? 'checked' : '';
		$list_data_11 = isset( $list_a_data[10] ) && $list_a_data[10] === 'yes' ? 'checked' : '';

		$list_b_data_1 = isset( $list_b_data[0] ) && $list_b_data[0] === 'yes' ? 'checked' : '';
		$list_b_data_2 = isset( $list_b_data[1] ) && $list_b_data[1] === 'yes' ? 'checked' : '';
		$list_b_data_3 = isset( $list_b_data[2] ) && $list_b_data[2] === 'yes' ? 'checked' : '';
		$list_b_data_4 = isset( $list_b_data[3] ) && $list_b_data[3] === 'yes' ? 'checked' : '';
		$list_b_data_5 = isset( $list_b_data[4] ) && $list_b_data[4] === 'yes' ? 'checked' : '';
		$list_b_data_6 = isset( $list_b_data[5] ) && $list_b_data[5] === 'yes' ? 'checked' : '';
		$list_b_data_7 = isset( $list_b_data[6] ) && $list_b_data[6] === 'yes' ? 'checked' : '';
		$list_b_data_8 = isset( $list_b_data[7] ) && $list_b_data[7] === 'yes' ? 'checked' : '';

		if( empty( $user_capability ) ) {
			?>
			<div class="mld-approved-user-wrapper">
				<h3><?php echo __( 'User Approval', 'myrtle-learning-dashboard' ); ?></h3>
				<button data-user_id="<?php echo $user_id; ?>" class="mld-subscriber-accept button button-primary"><?php echo __( 'Approved', 'myrtle-learning-dashboard' ) ?></button>
				<button data-user_id="<?php echo $user_id; ?>" class="mld-subscriber-deny button button-primary"><?php echo __( 'Denied', 'myrtle-learning-dashboard' ); ?></button>
			</div>
			<?php
		}

		if( in_array( 'pending_student', $user_capability ) ) {
			?>
			<div class="mld-pending-student-wrapper">
				<h3><?php echo __( 'Pending Approval/Deni', 'myrtle-learning-dashboard' ); ?></h3>
				<button data-user_id="<?php echo $user_id; ?>" class="mld-student-pending-accept button button-primary"><?php echo __( 'Approved', 'myrtle-learning-dashboard' ) ?></button>
				<button data-user_id="<?php echo $user_id; ?>" class="mld-student-pending-deny button button-primary"><?php echo __( 'Denied', 'myrtle-learning-dashboard' ); ?></button>
			</div>
			<?php
		}

		if( in_array( 'group_leader', $user_capability ) || in_array( 'pending', $user_capability ) || in_array( 'pending_teacher', $user_capability ) ) {

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

			$tea_clg_info = get_user_meta( $user_id, 'mld-teacher-college-info', true );
			$tea_uni_info = get_user_meta( $user_id, 'mld-teacher-uni-info', true );
			$tea_basic_info = get_user_meta( $user_id, 'mld-teacher-basic-info', true );
			$tea_exp_info = get_user_meta( $user_id, 'mld-teacher-experience-info', true );
			$t_exp = isset( $tea_basic_info['experience'] ) ? $tea_basic_info['experience'] : 0;
			$t_sub = get_user_meta( $user_id, 'mld_teacher_selected_subjects', true );
			if( is_array( $t_sub ) && ! empty( $t_sub ) ) {
				$t_sub = array_map( 'trim', $t_sub );
				$t_sub = array_map( function( $value ) {
					return str_replace(' ', '-', $value );
				}, $t_sub );
			}

			$t_availib = isset( $tea_basic_info['availability'] ) ? $tea_basic_info['availability'] : 0;
			$t_dbs = isset( $tea_basic_info['dbs'] ) ? $tea_basic_info['dbs'] : 0;
			$t_statement = isset( $tea_basic_info['personal_statement'] ) ? $tea_basic_info['personal_statement'] : '';
			$t_address = isset( $tea_basic_info['address'] ) ? $tea_basic_info['address'] : '';
			$t_dob = isset( $tea_basic_info['dob'] ) ? $tea_basic_info['dob'] : ''; 
			$t_county = isset( $tea_basic_info['county'] ) ? $tea_basic_info['county'] : '';
			$t_hometel = isset( $tea_basic_info['hometel'] ) ? $tea_basic_info['hometel'] : '';
			$t_mobile_number = isset( $tea_basic_info['mobile_number'] ) ? $tea_basic_info['mobile_number'] : '';
			$t_email = mld_get_user_email( $user_id );

			if( current_user_can( 'administrator' ) ) {

				require_once MLD_TEMPLATES_DIR.'teacher-registration-fields-backend.php';

				?>
				<h3><?php echo __( 'Bank Details', 'myrtle-learning-dashboard' ); ?></h3>
				<?php
				require_once MLD_TEMPLATES_DIR.'bank-fields-backend-template.php';
			}		
		}

		if( in_array( 'pending_teacher', $user_capability ) && current_user_can( 'administrator' ) ) {

			?>
			<h2><?php echo __( 'Teacher Uploaded Documents', 'myrtle-learning-dashboard' ); ?></h2>			
			<?php

			$user_form_title = get_user_meta( $user_id, 'mld-user-forms', true );
			?>
			<div class="mld-user-form-wrapper">
			<?php
			$no = 0;
			foreach( $user_form_title as $form_title ) {
				
				$title_with_dash = str_replace( ' ', '_', $form_title );
				$uploaded_file = mld_get_category_files( 'mld-user-form-uploads/'.$user_id.'/'.$title_with_dash );
				if( ! empty( $uploaded_file ) && is_array( $uploaded_file ) ) {

					$file = end( $uploaded_file );

					$basePath = "/home/runcloud/webapps/myrtlelearning/";
					$cleanedPath = str_replace( $basePath, '', $file );
					$cleanedPath = site_url().'/'.$cleanedPath;
					$form_url = $cleanedPath;

					$file_name_array = explode( '/', $file );
					$file_name = end( $file_name_array );
				}

				?>
				<div class="mld-single-form-wrapper">
					<h3><?php echo $form_title; ?></h3>
					<a href="<?php echo $form_url; ?>" download><?php echo $file_name; ?></a>
				</div>
				<?php
				$no++;
			}
			?>

			<h2><?php echo __( 'Teacher reference form', 'myrtle-learning-dashboard' ); ?></h2>
			<?php

			$get_refrence_from = get_user_meta( $user_id, 'mld-user-refrence-data', true ); 
			
			/**
			 * display teacher submitted refree form
			 */
			if( is_array( $get_refrence_from ) && ! empty( $get_refrence_from ) ) {
				foreach( $get_refrence_from as $index => $refrence_from ) {
					
					$data = isset( $get_refrence_from[$index] ) ? $get_refrence_from[$index] : []; 
					?>
					<div class="mld-ref-main-wrapper">
						<table class="mld-refrence-form-wrapper">
							<tr class="mld-refree-heading">
								<th colspan="2"><?php echo __( 'Referee', 'myrtle-learning-dashboard' ); ?></th>
							</tr>
							<tr colspan="2" class="mld-empty-row"></tr>
							<tr>
								<td width="30%" class="mld-filled-data mld-first-label"><?php echo __( 'Name of Applicant:', 'myrtle-learning-dashboard' ); ?></td>
								<td width="70%" class="mld-answer-box mld-first-answer"><?php echo $data[0]->name_of_applicant; ?></td>
							</tr>
							<tr>
								<td width="30%" class="mld-filled-data mld-general-data"><?php echo __( 'Position Applied for:', 'myrtle-learning-dashboard' ); ?></td>
								<td width="70%" class="mld-answer-box mld-general-answer"><?php echo $data[1]->position_applied_for; ?></td>
							</tr>
							<tr>
								<td width="30%" class="mld-filled-data mld-general-data"><?php echo __( 'Name of Referee:', 'myrtle-learning-dashboard' ); ?></td>
								<td width="70%" class="mld-answer-box mld-general-answer"><?php echo $data[2]->name_of_referee; ?></td>
							</tr>
							<tr>
								<td width="30%" class="mld-filled-data mld-general-data"><?php echo __( 'Email Address of Referee:', 'myrtle-learning-dashboard' ); ?></td>
								<td width="70%" class="mld-answer-box mld-general-answer"><?php echo $data[3]->email_address_of_referee; ?></td>
							</tr>
							<tr>
								<td width="30%" class="mld-filled-data mld-general-data"><?php echo __( 'Phone Number of Referee:', 'myrtle-learning-dashboard' ); ?></td>
								<td width="70%" class="mld-answer-box mld-general-answer"><?php echo $data[4]->phone_number_of_referee; ?></td>
							</tr>
							<tr>
								<td width="30%" class="mld-filled-data mld-last-label"><?php echo __( 'Name of Organisation:', 'myrtle-learning-dashboard' ); ?></td>
								<td width="70%" class="mld-answer-box mld-last-answer"><?php echo $data[5]->name_of_organisation; ?></td>
							</tr>
						</table>
					</div>
					<?php
				}
				?>
				<div class="mld-refree-wrapper">
					<a href="<?php echo site_url() . '?ref_pdf=yes&user_id=' . $user_id; ?>" target="_blank" class="mld-refree-wrapper-inner"><?php echo __( 'Download PDF' ); ?></a>
				</div>
				<?php
			}

			if( is_array( $get_refrence_from ) && ! empty( $get_refrence_from ) ) {
				foreach( $get_refrence_from as $index => $refrence_data ) {
					
					$data = isset( $get_refrence_from[$index] ) ? $get_refrence_from[$index] : [];
					$ref_email = isset( $data[3]->email_address_of_referee ) ? $data[3]->email_address_of_referee : '';	
					$query = $wpdb->prepare( "SELECT refrence_data FROM $refrence_table WHERE ref_email=%s AND user_id=%d", $ref_email, $user_id );
					$applicant_refrence_data = $wpdb->get_results( $query );
					
					if( empty( $applicant_refrence_data ) ) {
						continue;
					}

					$applicant_refrence_data = isset( $applicant_refrence_data[0]->refrence_data ) ? json_decode( $applicant_refrence_data[0]->refrence_data ) : '';
					$applicant_name = isset( $applicant_refrence_data->applicant_name ) ? $applicant_refrence_data->applicant_name : '';
					$p_applied = isset( $applicant_refrence_data->post_applied ) ? $applicant_refrence_data->post_applied : '';
					$experience_opt = isset( $applicant_refrence_data->experience_data ) ? $applicant_refrence_data->experience_data : '';

					$experience_check = 'checked';
					$experience_uncheck = '';

					if( 'no' == $experience_check || ! $experience_check ) {

						$experience_check = '';
						$experience_uncheck = 'checked';
					}

					$s_date = isset( $applicant_refrence_data->start_date ) ? $applicant_refrence_data->start_date : '';
					$e_date = isset( $applicant_refrence_data->end_date ) ? $applicant_refrence_data->end_date : '';
					$salary = isset( $applicant_refrence_data->salary ) ? $applicant_refrence_data->salary : '';
					$j_title  = isset( $applicant_refrence_data->job_title ) ? $applicant_refrence_data->job_title : '';
					$t_period = isset( $applicant_refrence_data->time_period ) ? $applicant_refrence_data->time_period : '';
					$a_capacity = isset( $applicant_refrence_data->applicant_capacity ) ? $applicant_refrence_data->applicant_capacity : '';
					$org_title = isset( $applicant_refrence_data->organization_title ) ? $applicant_refrence_data->organization_title : '';
					$j_duties = isset( $applicant_refrence_data->job_duties ) ? $applicant_refrence_data->job_duties : '';

					$quality_of_work = isset( $applicant_refrence_data->quality_of_work ) ? $applicant_refrence_data->quality_of_work : '';

					$quality_first_col = isset( $quality_of_work[0] ) && $quality_of_work[0] === 'true' ? 'checked' : '';
					$quality_second_col = isset( $quality_of_work[1] ) && $quality_of_work[1] === 'true' ? 'checked' : '';
					$quality_third_col = isset( $quality_of_work[2] ) && $quality_of_work[2] === 'true' ? 'checked' : '';
					$quality_fourth_col = isset( $quality_of_work[3] ) && $quality_of_work[3] === 'true' ? 'checked' : ''; 

					$quantity_of_work = isset( $applicant_refrence_data->quantity_of_work ) ? $applicant_refrence_data->quantity_of_work : '';
					$quantity_first_col = isset( $quantity_of_work[0] ) && $quantity_of_work[0] === 'true' ? 'checked' : '';
					$quantity_second_col = isset( $quantity_of_work[1] ) && $quantity_of_work[1] === 'true' ? 'checked' : '';
					$quantity_third_col = isset( $quantity_of_work[2] ) && $quantity_of_work[2] === 'true' ? 'checked' : '';
					$quantity_fourth_col = isset( $quantity_of_work[3] ) && $quantity_of_work[3] === 'true' ? 'checked' : '';
					$job_dedication = isset( $applicant_refrence_data->job_dedication ) ? $applicant_refrence_data->job_dedication : '';

					$job_dedication_first_col = isset( $job_dedication[0] ) && $job_dedication[0] === 'true' ? 'checked' : '';
					$job_dedication_second_col = isset( $job_dedication[1] ) && $job_dedication[1] === 'true' ? 'checked' : '';
					$job_dedication_third_col = isset( $job_dedication[2] ) && $job_dedication[2] === 'true' ? 'checked' : '';
					$job_dedication_fourth_col = isset( $job_dedication[3] ) && $job_dedication[3] === 'true' ? 'checked' : '';

					$work_ability = isset( $applicant_refrence_data->ability_of_work ) ? $applicant_refrence_data->ability_of_work : '';

					$ability_first_col = isset( $work_ability[0] ) && $work_ability[0] === 'true' ? 'checked' : '';
					$ability_second_col = isset( $work_ability[1] ) && $work_ability[1] === 'true' ? 'checked' : '';
					$ability_third_col = isset( $work_ability[2] ) && $work_ability[2] === 'true' ? 'checked' : '';
					$ability_fourth_col = isset( $work_ability[3] ) && $work_ability[3] === 'true' ? 'checked' : '';

					$working_relationship = isset( $applicant_refrence_data->working_relationship ) ? $applicant_refrence_data->working_relationship : '';

					$working_rela_first_col = isset( $working_relationship[0] ) && $working_relationship[0] === 'true' ? 'checked' : '';
					$working_rela_second_col = isset( $working_relationship[1] ) && $working_relationship[1] === 'true' ? 'checked' : '';
					$working_rela_third_col = isset( $working_relationship[2] ) && $working_relationship[2] === 'true' ? 'checked' : '';
					$working_rela_fourth_col = isset( $working_relationship[3] ) && $working_relationship[3] === 'true' ? 'checked' : '';

					$time_keep = isset( $applicant_refrence_data->time_keeping ) ? $applicant_refrence_data->time_keeping : '';

					$time_keep_first_col = isset( $time_keep[0] ) && $time_keep[0] === 'true' ? 'checked' : '';
					$time_keep_second_col = isset( $time_keep[1] ) && $time_keep[1] === 'true' ? 'checked' : '';
					$time_keep_third_col = isset( $time_keep[2] ) && $time_keep[2] === 'true' ? 'checked' : '';
					$time_keep_fourth_col = isset( $time_keep[3] ) && $time_keep[3] === 'true' ? 'checked' : '';

					$trust = isset( $applicant_refrence_data->trust_worthy_yes ) ? $applicant_refrence_data->trust_worthy_yes : '';

					$trust_check = 'checked';
					$trust_uncheck = '';

					if( 'no' == $trust || ! $trust ) {

						$trust_check = '';
						$trust_uncheck = 'checked';
					}

					$care = isset( $applicant_refrence_data->duty_care_yes ) ? $applicant_refrence_data->duty_care_yes : '';
						
					$care_check = 'checked';
					$care_uncheck = '';

					if( 'no' == $care || ! $care ) {

						$care_check = '';
						$care_uncheck = 'checked';
					}

					$disciplinary = isset( $applicant_refrence_data->disciplinary_warnings_yes ) ? $applicant_refrence_data->disciplinary_warnings_yes : '';

					$disciplinary_check = 'checked';
					$disciplinary_uncheck = '';

					if( 'no' == $disciplinary || ! $disciplinary ) {

						$disciplinary_check = '';
						$disciplinary_uncheck = 'checked';
					}

					$l_reason = isset( $applicant_refrence_data->leaving_reason ) ? $applicant_refrence_data->leaving_reason : '';

					$re_employ = isset( $applicant_refrence_data->re_employ_yes ) ? $applicant_refrence_data->re_employ_yes : '';
					
					$re_employ_check = 'checked';
					$re_employ_uncheck = '';

					if( 'no' == $re_employ || ! $re_employ ) {

						$re_employ_check = '';
						$re_employ_uncheck = 'checked';
					}

					$j_describe = isset( $applicant_refrence_data->job_describe_yes ) ? $applicant_refrence_data->job_describe_yes : '';

					$j_describe_check = 'checked';
					$j_describe_uncheck = '';

					if( 'no' == $j_describe || ! $j_describe ) {

						$j_describe_check = '';
						$j_describe_uncheck = 'checked';
					}

					$a_specification = isset( $applicant_refrence_data->applicant_specification ) ? $applicant_refrence_data->applicant_specification : '';

					$w_w_children = isset( $applicant_refrence_data->work_with_children_yes ) ? $applicant_refrence_data->work_with_children_yes : '';
					$w_w_children_check = 'checked';
					$w_w_children_uncheck = '';

					if( 'no' == $w_w_children || ! $w_w_children ) {

						$w_w_children_check = '';
						$w_w_children_uncheck = 'checked';
					}
					
					$w_w_children_ans = isset( $applicant_refrence_data->applicant_work_with_children_answer ) ? $applicant_refrence_data->applicant_work_with_children_answer : '';
					$further_comment = isset( $applicant_refrence_data->applicant_further_comment ) ? $applicant_refrence_data->applicant_further_comment : '';
					$name = isset( $applicant_refrence_data->org_name ) ? $applicant_refrence_data->org_name : '';
					$date = isset( $applicant_refrence_data->org_date ) ? $applicant_refrence_data->org_date : '';
					$telephone = isset( $applicant_refrence_data->org_telephone ) ? $applicant_refrence_data->org_telephone : '';
					$sign = '';
					$stump = '';
					$query = $wpdb->prepare( "SELECT ID FROM $refrence_table WHERE ref_email=%s AND user_id=%d", $ref_email, $user_id );
					$applicant_refrence_data = $wpdb->get_results( $query );
					$auto_id = isset( $applicant_refrence_data[0]->ID ) ? intval( $applicant_refrence_data[0]->ID ) : '';
					$upload_dir = wp_get_upload_dir();
					$sign_upload_dir = $upload_dir['basedir'].'/mld_references/user_'.$auto_id.'/sign';
					$stump_upload_dir = $upload_dir['basedir'].'/mld_references/user_'.$auto_id.'/stump';
					$sign_file = glob( $sign_upload_dir . '/*' );
					$stump_file = glob( $stump_upload_dir . '/*' );
					$sign_file = isset( $sign_file[0] ) ? $sign_file[0] : '';
					$stump_file = isset( $stump_file[0] ) ? $stump_file[0] : '';
					$basePath = "/home/runcloud/webapps/myrtlelearning/";
					$sign_cleanedPath = str_replace( $basePath, '', $sign_file );
					$sign_url = site_url().'/'.$sign_cleanedPath;
					$stump_cleanedPath = str_replace( $basePath, '', $stump_file );
					$stump_url = site_url().'/'.$stump_cleanedPath;
					require_once MLD_TEMPLATES_DIR.'refrence-form-backend.php';
					?>
					<div class="mld-refree-wrapper">
						<a href="<?php echo site_url() . '?sub_by=referee&user_id=' . $user_id; ?>" target="_blank" class="mld-refree-wrapper-inner"><?php echo __( 'Download PDF', 'myrtle-learning-dashboard' ); ?></a>
					</div>
					<?php
				}
			}
			?>
			<input type="button" class="mld-confirm-as-teacher button button-primary" data-user_id="<?php echo $user_id; ?>" value="<?php echo __( 'Approve', 'myrtle-learning-dashboard' ); ?>">
			<input type="button" class="mld-deny-as-teacher button button-primary" data-user_id="<?php echo $user_id; ?>" value="<?php echo __( 'Deny', 'myrtle-learning-dashboard' ); ?>">
			</div>
			<?php
		}
	}

	/**
	 * enqueue admin scripts
	 */
	public function mld_enqueue_scripts() {

		$rand = rand( 1000000, 1000000000 );
		wp_enqueue_style( 'staff-admin-css', MLD_ASSETS_URL .'css/staff-admin.css', '', $rand, false );
		wp_enqueue_script( 'staff-admin-js', MLD_ASSETS_URL .'js/staff-admin.js', [ 'jquery' ], $rand, true );
		wp_localize_script( 'staff-admin-js', 'MLD', [
			'ajaxURL'       => admin_url( 'admin-ajax.php' ),
			'site_url'		=> site_url()
		] );

		wp_enqueue_style( 'external-select-min-css', MLD_ASSETS_URL .'css/select2.min.css', '', $rand, false );
		wp_enqueue_script( 'external-select2-jquery-js', MLD_ASSETS_URL. 'js/select2.full.min.js', ['jquery'], $rand, true );
	}

	/**
	 * delete teacher form
	 */
	public function mld_delete_teacher_form() {

		$delete_url = isset( $_POST['delete_url'] ) ? $_POST['delete_url'] : '';
		unlink( $delete_url );
		wp_die();
	}

	/**
	 * mld upload teacher form
	 */
	public function mld_upload_teacher_form() {

		$files = $_FILES;
		$teacher_form_titles = isset( $_POST['teacher_form_title'] ) ? str_replace( '\\', '', $_POST['teacher_form_title'] ) : [];
		$teacher_form_info = json_decode( $teacher_form_titles );

		update_option( 'mld_teacher_form_titles', $teacher_form_info );
		if( ! empty( $teacher_form_info ) && is_array( $teacher_form_info ) ) {

			$uploads_dir = wp_upload_dir();

			foreach( $teacher_form_info as $data ) {

				$title = isset( $data->title ) ? $data->title : '';

				$title_with_dash = str_replace( ' ', '-', $title );
				$teacher_form_dir = $uploads_dir['basedir'].'/mld-teachers-form-uploads/'.$title_with_dash;

				if ( ! is_dir( $teacher_form_dir ) ) {
					wp_mkdir_p( $teacher_form_dir );
				}

				$file_key = str_replace( ' ', '_', $title );
				$uploaded_file = isset( $files[$file_key]['tmp_name'] ) ? $files[$file_key]['tmp_name'] : '';
				$file_name = isset( $files[$file_key]['name'] ) ? $files[$file_key]['name'] : '';
				move_uploaded_file( $uploaded_file, $teacher_form_dir.'/'.$file_name );
			}
		}
		wp_die();
	}

	/**
	 * teacher form menu callback
	 */
	public function mld_teacher_form_menu_callback() {

		$form_titles = get_option( 'mld_teacher_form_titles' );
		if( is_array( $form_titles ) ) {

			$form_titles = array_filter( $form_titles );
		}

		$display = 'none';
		if( $form_titles ) {
			$display = '';
		}
		?>
		<div class="mld-teacher-uploads">
			<p><?php echo __( 'Select Upload Type', 'myrtle-learning-dashboard' ); ?></p>
			<select class="mld-upload-type">
				<option value=""><?php echo __( 'Select a type', 'myrtle-learning-dashboard' ) ?></option>
				<option value="documents"><?php echo __( 'Documents', 'myrtle-learning-dashboard' ); ?></option>
				<option value="form"><?php echo __( 'Form', 'myrtle-learning-dashboard' ); ?></option>
			</select>
			<input type="button" value="<?php echo __( 'Add New File', 'myrtle-learning-dashboard' ); ?>" class="mld-teacher-upload button button-primary">
		</div>
		<div class="mld-form-files">
			<?php
			if( ! empty( $form_titles ) && is_array( $form_titles ) ) {

				$no = 0;
				foreach( $form_titles as $data ) {

					$type = isset( $data->index ) ? $data->index : '';
					$title = isset( $data->title ) ? $data->title : ''; 
					$title_with_dash = str_replace( ' ', '-', $title );

					$file = mld_get_category_files( 'mld-teachers-form-uploads/'.$title_with_dash );
					$file = isset( $file[0] ) ? $file[0] : '';
					$form_title = $title;
					$basePath = "/home/runcloud/webapps/myrtlelearning/";

					$cleanedPath = str_replace( $basePath, '', $file );
					$cleanedPath = site_url().'/'.$cleanedPath;
					$form_url = $cleanedPath;
					$file_name_array = explode( '/', $file );
					$file_name = end( $file_name_array );

					?>
					<div class="mld-teacher-inner-wrapper">
						<span class="dashicons dashicons-edit mld-edit-icon"></span>
						<span class="dashicons dashicons-no mld-dashicon" data-delete_url="<?php echo $file; ?>"></span>
						<h3><?php echo $form_title; ?></h3>
						<a href="<?php echo $form_url; ?>" download><?php echo $file_name; ?></a>
						<input type="text" value="<?php echo $form_title; ?>" class="mld-form-title" style="display: none;" data-type="<?php echo $type; ?>">
						<input type="file" class="mld-teacher-uploads" style="display: none;" value="<?php echo $file_name; ?>">
					</div>
					<?php
					$no++;
				}
			}
			?>
		</div>
		<input type="button" value="<?php echo __( 'Update', 'myrtle-learning-dashboard' ); ?>" class="mld-update-teacher-form button button-primary" style="display: <?php echo $display; ?>;">
		<?php
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
							echo $this->mld_get_user_basic_html( 'Name', $teacher_name );
							echo $this->mld_get_user_basic_html( 'Years of Teaching', $years_of_teaching );
							echo $this->mld_get_user_basic_html( 'Subjects', $subjects );
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
	public function mld_get_user_basic_html( $label, $val ) {

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
}

Myrtle_Staff::instance();

/**
 * create reference full pdf
 */
add_action( 'wp', 'wpe_generate_reference_pdf' );
function wpe_generate_reference_pdf() {

	global $wpdb;
	$ref_pdf = isset( $_GET['ref_pdf'] ) ? $_GET['ref_pdf'] : '';
	$user_id = isset( $_GET['user_id'] ) ? $_GET['user_id'] : '';
	$submitted_by = isset( $_GET['sub_by'] ) ? $_GET['sub_by'] : '';
	$refrence_table = $wpdb->prefix . 'mld_refrences'; 	
	if( $ref_pdf && $user_id ) {
		
		// require_once MLD_INCLUDES_DIR . '/lib/PDF/tcpdf.php';
		class MYPDF extends TCPDF {

			public function Header() {

				$this->Rect(0, 0, $this->getPageWidth(),$this->getPageHeight(), 'DF', "",  array(51,87, 33));
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
		$get_refrence_from = get_user_meta( $user_id, 'mld-user-refrence-data', true );
		if( ! empty( $get_refrence_from ) && is_array( $get_refrence_from ) ) {
			foreach( $get_refrence_from as $index => $refrence ) {	
				$data = isset( $get_refrence_from[$index] ) ? $get_refrence_from[$index] : [];
				?>
				<table cellpadding="5">
					<tr style="background-color: #365249;"><td colspan="2" style="color: white;"><b><?php echo __( 'Referee', 'myrtle-learning-dashboard' ); ?></b></td></tr>
					<tr style="background-color: white;"><td style="color: #365249; border-right: 2px solid #e4e4e4;"><b><?php echo __( 'Name of Applicant:', 'myrtle-learning-dashboard' ); ?></b></td><td style="color: #365249;"><?php echo $data[0]->name_of_applicant; ?></td></tr>
					<tr style="background-color: #f2f2f2;"><td style="color: #365249; border-right: 2px solid #e4e4e4;"><b><?php echo __( 'Position Applied for:', 'myrtle-learning-dashboard' ); ?></b></td><td style="color: #365249;"><?php echo $data[1]->position_applied_for; ?></td></tr>
					<tr style="background-color: white;"><td style="color: #365249; border-right: 2px solid #e4e4e4;"><b><?php echo __( 'Name of Referee:', 'myrtle-learning-dashboard' ); ?></b></td><td style="color: #365249;"><?php echo $data[2]->name_of_referee; ?></td></tr>
					<tr style="background-color: #f2f2f2;"><td style="color: #365249; border-right: 2px solid #e4e4e4;"><b><?php echo __( 'Email Address of Referee:', 'myrtle-learning-dashboard' ); ?></b></td><td style="color: #365249;"><?php echo $data[3]->email_address_of_referee; ?></td></tr>
					<tr style="background-color: white;"><td style="color: #365249; border-right: 2px solid #e4e4e4;"><b><?php echo __( 'Phone Number of Referee:', 'myrtle-learning-dashboard' ); ?></b></td><td style="color: #365249;"><?php echo $data[4]->phone_number_of_referee; ?></td></tr>
					<tr style="background-color: #f2f2f2;"><td style="color: #365249; border-right: 2px solid #e4e4e4;"><b><?php echo __( 'Name of Organisation:', 'myrtle-learning-dashboard' ); ?></b></td><td style="color: #365249;"><?php echo $data[5]->name_of_organisation; ?></td></tr>
					<tr><td></td><td></td></tr>				
				</table>
				<br>
				<?php
			}
		}

		$content = ob_get_contents();
		ob_get_clean();

		$page_height = strlen( $content ) / 10;
		$pdf_page_format = PDF_PAGE_FORMAT;
		$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, $pdf_page_format, true, 'UTF-8', false);
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor( 'LRC' );
		$pdf->SetTitle( 'LRC Course Outline' );
		$pdf->SetSubject( 'LRC Outline' );

		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
		$pdf->setHeaderData( '', 0, '', '', [ 0, 0, 0 ], [ 255, 255, 255 ] );
    
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    
		$pdf->AddPage();
    
		$pdf->writeHTML( $content, true, false, true, false, '' );
    
		$pdf->lastPage();
		ob_clean();
    
		$pdf->Output( 'pdf_course_outline', 'I' );
		die();	
	}

	/**
	 * create pdf submitted by refree
	 */
	if( $user_id && $submitted_by ) {
		
		// require_once MLD_INCLUDES_DIR . '/lib/PDF/tcpdf.php';
		class MYPDF extends TCPDF {

			public function Header() {

				$this->Rect(0, 0, $this->getPageWidth(),$this->getPageHeight(), 'DF', "",  array(51,87, 33));
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

		$get_refrence_from = get_user_meta( $user_id, 'mld-user-refrence-data', true );
		
		ob_start();
		if( ! empty( $get_refrence_from ) && is_array( $get_refrence_from ) ) {
			foreach( $get_refrence_from as $index => $get_refrence ) {

				$data = isset( $get_refrence_from[$index] ) ? $get_refrence_from[$index] : [];
				$ref_email = isset( $data[3]->email_address_of_referee ) ? $data[3]->email_address_of_referee : '';	
				$query = $wpdb->prepare( "SELECT refrence_data FROM $refrence_table WHERE ref_email=%s AND user_id=%d", $ref_email, $user_id );
				$applicant_refrence_data = $wpdb->get_results( $query );
				if( empty( $applicant_refrence_data ) ) {
					continue;
				}

				$applicant_refrence_data = isset( $applicant_refrence_data[0]->refrence_data ) ? json_decode( $applicant_refrence_data[0]->refrence_data ) : '';
				$applicant_name = isset( $applicant_refrence_data->applicant_name ) ? $applicant_refrence_data->applicant_name : '';
				$p_applied = isset( $applicant_refrence_data->post_applied ) ? $applicant_refrence_data->post_applied : '';
				$experience_opt = isset( $applicant_refrence_data->experience_data ) ? $applicant_refrence_data->experience_data : '';					
				$s_date = isset( $applicant_refrence_data->start_date ) ? $applicant_refrence_data->start_date : '';
				$e_date = isset( $applicant_refrence_data->end_date ) ? $applicant_refrence_data->end_date : '';
				$salary = isset( $applicant_refrence_data->salary ) ? $applicant_refrence_data->salary : '';
				$j_title  = isset( $applicant_refrence_data->job_title ) ? $applicant_refrence_data->job_title : '';
				$t_period = isset( $applicant_refrence_data->time_period ) ? $applicant_refrence_data->time_period : '';
				$a_capacity = isset( $applicant_refrence_data->applicant_capacity ) ? $applicant_refrence_data->applicant_capacity : '';
				$org_title = isset( $applicant_refrence_data->organization_title ) ? $applicant_refrence_data->organization_title : '';
				$j_duties = isset( $applicant_refrence_data->job_duties ) ? $applicant_refrence_data->job_duties : '';

				$qlty_work = isset( $applicant_refrence_data->quality_of_work ) ? $applicant_refrence_data->quality_of_work : '';
				$qn_work = isset( $applicant_refrence_data->quantity_of_work ) ? $applicant_refrence_data->quantity_of_work : '';
				$j_dedication = isset( $applicant_refrence_data->job_dedication ) ? $applicant_refrence_data->job_dedication : '';
				$w_ability = isset( $applicant_refrence_data->ability_of_work ) ? $applicant_refrence_data->ability_of_work : '';
				$w_relation = isset( $applicant_refrence_data->working_relationship ) ? $applicant_refrence_data->working_relationship : '';
				$t_keeping = isset( $applicant_refrence_data->time_keeping ) ? $applicant_refrence_data->time_keeping : '';					
				$trust = isset( $applicant_refrence_data->trust_worthy_yes ) ? $applicant_refrence_data->trust_worthy_yes : '';
				$care = isset( $applicant_refrence_data->duty_care_yes ) ? $applicant_refrence_data->duty_care_yes : '';
				$disciplinary = isset( $applicant_refrence_data->disciplinary_warnings_yes ) ? $applicant_refrence_data->disciplinary_warnings_yes : '';
				$l_reason = isset( $applicant_refrence_data->leaving_reason ) ? $applicant_refrence_data->leaving_reason : '';
				$re_employ = isset( $applicant_refrence_data->re_employ_yes ) ? $applicant_refrence_data->re_employ_yes : '';
				$j_describe = isset( $applicant_refrence_data->job_describe_yes ) ? $applicant_refrence_data->job_describe_yes : '';
				$a_specification = isset( $applicant_refrence_data->applicant_specification ) ? $applicant_refrence_data->applicant_specification : '';
				$w_w_children = isset( $applicant_refrence_data->work_with_children_yes ) ? $applicant_refrence_data->work_with_children_yes : '';
				$w_w_children_ans = isset( $applicant_refrence_data->applicant_work_with_children_answer ) ? $applicant_refrence_data->applicant_work_with_children_answer : '';
				$further_comment = isset( $applicant_refrence_data->applicant_further_comment ) ? $applicant_refrence_data->applicant_further_comment : '';
				$name = isset( $applicant_refrence_data->org_name ) ? $applicant_refrence_data->org_name : '';
				$date = isset( $applicant_refrence_data->org_date ) ? $applicant_refrence_data->org_date : '';
				$telephone = isset( $applicant_refrence_data->org_telephone ) ? $applicant_refrence_data->org_telephone : '';
				$sign = '';
				$stump = '';
				$query = $wpdb->prepare( "SELECT ID FROM $refrence_table WHERE ref_email=%s AND user_id=%d", $ref_email, $user_id );
				$applicant_refrence_data = $wpdb->get_results( $query );
				$auto_id = isset( $applicant_refrence_data[0]->ID ) ? intval( $applicant_refrence_data[0]->ID ) : '';
				$upload_dir = wp_get_upload_dir();
				$sign_upload_dir = $upload_dir['basedir'].'/mld_references/user_'.$auto_id.'/sign';
				$stump_upload_dir = $upload_dir['basedir'].'/mld_references/user_'.$auto_id.'/stump';
				$sign_file = glob( $sign_upload_dir . '/*' );
				$stump_file = glob( $stump_upload_dir . '/*' );
				$sign_file = isset( $sign_file[0] ) ? $sign_file[0] : '';
				$stump_file = isset( $stump_file[0] ) ? $stump_file[0] : '';
				$basePath = "/home/runcloud/webapps/myrtlelearning/";
				$sign_cleanedPath = str_replace( $basePath, '', $sign_file );
				$sign_url = site_url().'/'.$sign_cleanedPath;
				$stump_cleanedPath = str_replace( $basePath, '', $stump_file );
				$stump_url = site_url().'/'.$stump_cleanedPath;
				
				?>
				<table cellpadding="10">
					<tr>
						<td width="30%" style="background-color: #365249; color: #ffffff; border-bottom: 4px solid white;"><b><?php echo __( 'Name of Applicant', 'myrtle-learning-dashboard' ); ?></b></td>
						<td width="70%" style="background-color: #ffffff; border-bottom: 4px solid white; background-color: #e4e4e4;"><?php echo $applicant_name; ?></td>
					</tr>
					<tr>
						<td width="30%" style="background-color: #365249; border-bottom: 4px solid white; color: #ffffff;"><b><?php echo __( 'Post Applied for', 'myrtle-learning-dashboard' ); ?></b></td>
						<td width="70%" style="background-color: #ffffff; background-color: #e4e4e4; border-bottom: 4px solid white;"><?php echo $p_applied; ?></td>
					</tr>
				</table>
				<table cellpadding="10">
					<tr>
						<td width="60%" style="background-color: #365249; border-bottom: 4px solid white; color: #ffffff;"><b><?php echo __( 'Did the applicant work for your organisation?', 'myrtle-learning-dashboard' ); ?></b></td>
						<td width="40%" style="background-color: #ffffff; background-color: #e4e4e4; border-bottom: 4px solid white;">							
							<?php
							if( 'yes' == $experience_opt ) {
								?>
								<label><?php echo __( 'Yes', 'myrtle-learning-dashboard' ); ?></label>
								<img src="<?php echo MLD_ASSETS_URL.'images/approved.png'; ?>" style="height: 15px;">
								<label><?php echo __( 'No', 'myrtle-learning-dashboard' ); ?></label>
								<img src="<?php echo MLD_ASSETS_URL.'images/unchecked.png'; ?>" style="height: 15px;">
								<?php
							} else {
								?>
								<label><?php echo __( 'Yes', 'myrtle-learning-dashboard' ); ?></label>
								<img src="<?php echo MLD_ASSETS_URL.'images/unchecked.png'; ?>" style="height: 15px;">
								<label><?php echo __( 'No', 'myrtle-learning-dashboard' ); ?></label>
								<img src="<?php echo MLD_ASSETS_URL.'images/approved.png'; ?>" style="height: 15px;">								
								<?php
							}
							?>
						</td>
					</tr>
				</table>
				<table cellpadding="10">
					<tr>
						<td colspan="3" style="background-color: #365249; color: #ffffff;"><b><?php echo __( "If yes, what were the applicant's start and leaving dates?", 'myrtle-learning-dashboard' ); ?></b></td>
					</tr>
					<tr>
						<td style="background-color: #e4e4e4; border-right: 2px solid white; border-bottom: 4px solid white;"><?php echo __( 'Start date: ', 'myrtle-learning-dashboard' ); echo $s_date;?></td>
						<td style="background-color: #e4e4e4; border-right: 2px solid white; border-bottom: 4px solid white;"><?php echo __( 'Leaving date: ', 'myrtle-learning-dashboard' ); echo $e_date;?></td>
						<td style="background-color: #e4e4e4; border-bottom: 4px solid white;"><?php echo __( 'Salary: ', 'myrtle-learning-dashboard' ); echo $salary;?></td>
					</tr>
				</table>
				<table cellpadding="10">
					<tr>
						<td style="background-color: #365249; color: #ffffff;"><b><?php echo __( 'What is your job title?', 'myrtle-learning-dashboard' ); ?></b></td>	
					</tr>
					<tr>
						<td style="border-bottom: 4px solid white; background-color: #e4e4e4;"><?php echo $j_title; ?></td>
					</tr>
					<tr>
						<td style="background-color: #365249; color: #ffffff;"><b><?php echo __( 'How long did you work with the applicant?', 'myrtle-learning-dashboard' ); ?></b></td>						
					</tr>
					<tr>
						<td style="border-bottom: 4px solid white; background-color: #e4e4e4;"><?php echo $t_period;?></td>
					</tr>
					<tr>
						<td style="background-color: #365249; color: #ffffff;"><b><?php echo __( 'In what capacity do you know the applicant? E.g. as a colleague/as an employee reporting to you/other (please specify)', 'myrtle-learning-dashboard' ); ?></b></td>						
					</tr>
					<tr>
						<td style="border-bottom: 4px solid white; background-color: #e4e4e4;"><?php echo $a_capacity; ?></td>
					</tr>
					<tr>
						<td style="background-color: #365249; color: #ffffff;"><b><?php echo __( "What was the applicant's job title with your organisation?", 'myrtle-learning-dashboard' ); ?></b></td>						
					</tr>
					<tr>
						<td style="border-bottom: 4px solid white; background-color: #e4e4e4;"><?php echo $org_title; ?></td>
					</tr>
					<tr>
						<td style="background-color: #365249; color: #ffffff;"><b><?php echo __( "What were the applicant's main job duties?", 'myrtle-learning-dashboard' ); ?></b></td>
					</tr>
					<tr>
						<td style="border-bottom: 4px solid white; background-color: #e4e4e4;"><?php echo $j_duties; ?></td>
					</tr>
					<tr>
						<td style="background-color: #365249; color: #ffffff;"><b><?php echo __( 'What is your assessment of the following elements in relation to the applicant?', 'myrtle-learning-dashboard' ); ?></b></td>
					</tr>
				</table>
				<table cellpadding="10">
					<tr>
						<td width="40%" style="background-color: #365249; border: 1px solid white;"></td>
						<td width="15%" style="background-color: #365249; color: #ffffff; border: 2px solid white;"><?php echo __( 'Excellent', 'myrtle-learning-dashboard' ); ?></td>
						<td width="15%" style="background-color: #365249; color: #ffffff; border: 2px solid white;"><?php echo __( 'Good', 'myrtle-learning-dashboard' ); ?></td>
						<td width="15%" style="background-color: #365249; color: #ffffff; border: 2px solid white;"><?php echo __( 'Fair', 'myrtle-learning-dashboard' ); ?></td>
						<td width="15%" style="background-color: #365249; color: #ffffff; border: 2px solid white;"><?php echo __( 'Poor', 'myrtle-learning-dashboard' ); ?></td>
					</tr>
					<tr>
						<td width="40%" style="background-color: #365249; border: 1px solid white; color: #ffffff;"><b><?php echo __( 'Quality of work', 'myrtle-learning-dashboard' ); ?></b></td>
						<?php 
						if( ! empty( $qlty_work ) && is_array( $qlty_work ) ) {
							foreach( $qlty_work as $work ) {
								?>
								<td width="15%" style="border: 2px solid white; background-color: #e4e4e4;">
									<?php 
									if( 'true' == $work ) {
										?>
										<img src="<?php echo MLD_ASSETS_URL.'images/approved.png'; ?>" style="height: 15px;">
										<?php
									} else {
										?>
										<img src="<?php echo MLD_ASSETS_URL.'images/unchecked.png'; ?>" style="height: 15px;">
										<?php
									}
									?>			
								</td>
								<?php
							}
						}
						?>
					</tr>
					<tr>
						<td width="40%" style="background-color: #365249; border: 1px solid white; color: #ffffff;"><b><?php echo __( 'Quantity of work', 'myrtle-learning-dashboard' ); ?></b></td>
						<?php 
						if( ! empty( $qn_work ) && is_array( $qn_work ) ) {
							foreach( $qn_work as $q_work ) {
								?>
								<td width="15%" style="border: 2px solid white; background-color: #e4e4e4;">
									<?php 
									if( 'true' == $q_work ) {
										?>
										<img src="<?php echo MLD_ASSETS_URL.'images/approved.png'; ?>" style="height: 15px;">
										<?php
									} else {
										?>
										<img src="<?php echo MLD_ASSETS_URL.'images/unchecked.png'; ?>" style="height: 15px;">
										<?php
									}
									?>			
								</td>
								<?php
							}
						}
						?>
					</tr>
					<tr>
						<td width="40%" style="background-color: #365249; border: 1px solid white; color: #ffffff;"><b><?php echo __( 'Dedication to the job', 'myrtle-learning-dashboard' ); ?></b></td>
						<?php 
						if( ! empty( $j_dedication ) && is_array( $j_dedication ) ) {
							foreach( $j_dedication as $dedication ) {
								?>
								<td width="15%" style="border: 2px solid white; background-color: #e4e4e4;">
									<?php 
									if( 'true' == $dedication ) {
										?>
										<img src="<?php echo MLD_ASSETS_URL.'images/approved.png'; ?>" style="height: 15px;">
										<?php
									} else {
										?>
										<img src="<?php echo MLD_ASSETS_URL.'images/unchecked.png'; ?>" style="height: 15px;">
										<?php
									}
									?>			
								</td>
								<?php
							}
						}
						?>
					</tr>
					<tr>
						<td width="40%" style="background-color: #365249; border: 1px solid white; color: #ffffff;"><b><?php echo __( 'Ability to work without supervision', 'myrtle-learning-dashboard' ); ?></b></td>
						<?php 
						if( ! empty( $w_ability ) && is_array( $w_ability ) ) {
							foreach( $w_ability as $ability ) {
								?>
								<td width="15%" style="border: 2px solid white; background-color: #e4e4e4;">
									<?php 
									if( 'true' == $ability ) {
										?>
										<img src="<?php echo MLD_ASSETS_URL.'images/approved.png'; ?>" style="height: 15px;">
										<?php
									} else {
										?>
										<img src="<?php echo MLD_ASSETS_URL.'images/unchecked.png'; ?>" style="height: 15px;">
										<?php
									}
									?>			
								</td>
								<?php
							}
						}
						?>
					</tr>
					<tr>
						<td width="40%" style="background-color: #365249; border: 1px solid white; color: #ffffff;"><b><?php echo __( 'Working relationships', 'myrtle-learning-dashboard' ); ?></b></td>
						<?php 
						if( ! empty( $w_relation ) && is_array( $w_relation ) ) {
							foreach( $w_relation as $relation ) {
								?>
								<td width="15%" style="border: 2px solid white; background-color: #e4e4e4;">
									<?php 
									if( 'true' == $relation ) {
										?>
										<img src="<?php echo MLD_ASSETS_URL.'images/approved.png'; ?>" style="height: 15px;">
										<?php
									} else {
										?>
										<img src="<?php echo MLD_ASSETS_URL.'images/unchecked.png'; ?>" style="height: 15px;">
										<?php
									}
									?>			
								</td>
								<?php
							}
						}
						?>
					</tr>
					<tr>
						<td width="40%" style="background-color: #365249; border: 1px solid white; color: #ffffff;"><b><?php echo __( 'Time keeping', 'myrtle-learning-dashboard' ); ?></b></td>
						<?php 
						if( ! empty( $t_keeping ) && is_array( $t_keeping ) ) {
							foreach( $t_keeping as $keeping ) {
								?>
								<td width="15%" style="border: 2px solid white; background-color: #e4e4e4;">
									<?php 
									if( 'true' == $keeping ) {
										?>
										<img src="<?php echo MLD_ASSETS_URL.'images/approved.png'; ?>" style="height: 15px;">
										<?php
									} else {
										?>
										<img src="<?php echo MLD_ASSETS_URL.'images/unchecked.png'; ?>" style="height: 15px;">
										<?php
									}
									?>			
								</td>
								<?php
							}
						}
						?>
					</tr>
				</table>
				<table cellpadding="10">
					<tr>
						<td width="60%" style="background-color: #365249; border-bottom: 4px solid white; color: #ffffff;"><b><?php echo __( 'Did you find the applicant to be honest and trustworthy?', 'myrtle-learning-dashboard' ); ?></b></td>
						<td width="40%" style="background-color: #ffffff; background-color: #e4e4e4; border-bottom: 4px solid white;">							
							<?php
							if( 'yes' == $trust ) {
								?>
								<label><?php echo __( 'Yes', 'myrtle-learning-dashboard' ); ?></label>
								<img src="<?php echo MLD_ASSETS_URL.'images/approved.png'; ?>" style="height: 15px;">
								<label><?php echo __( 'No', 'myrtle-learning-dashboard' ); ?></label>
								<img src="<?php echo MLD_ASSETS_URL.'images/unchecked.png'; ?>" style="height: 15px;">
								<?php
							} else {
								?>
								<label><?php echo __( 'Yes', 'myrtle-learning-dashboard' ); ?></label>
								<img src="<?php echo MLD_ASSETS_URL.'images/unchecked.png'; ?>" style="height: 15px;">
								<label><?php echo __( 'No', 'myrtle-learning-dashboard' ); ?></label>
								<img src="<?php echo MLD_ASSETS_URL.'images/approved.png'; ?>" style="height: 15px;">								
								<?php
							}
							?>
						</td>
					</tr>
					<tr>
						<td width="60%" style="background-color: #365249; border-bottom: 4px solid white; color: #ffffff;"><b><?php echo __( 'Did you find the applicant to be reliable in carrying out his/her duties?', 'myrtle-learning-dashboard' ); ?></b></td>
						<td width="40%" style="background-color: #ffffff; background-color: #e4e4e4; border-bottom: 4px solid white;">							
							<?php
							if( 'yes' == $care ) {
								?>
								<label><?php echo __( 'Yes', 'myrtle-learning-dashboard' ); ?></label>
								<img src="<?php echo MLD_ASSETS_URL.'images/approved.png'; ?>" style="height: 15px;">
								<label><?php echo __( 'No', 'myrtle-learning-dashboard' ); ?></label>
								<img src="<?php echo MLD_ASSETS_URL.'images/unchecked.png'; ?>" style="height: 15px;">
								<?php
							} else {
								?>
								<label><?php echo __( 'Yes', 'myrtle-learning-dashboard' ); ?></label>
								<img src="<?php echo MLD_ASSETS_URL.'images/unchecked.png'; ?>" style="height: 15px;">
								<label><?php echo __( 'No', 'myrtle-learning-dashboard' ); ?></label>
								<img src="<?php echo MLD_ASSETS_URL.'images/approved.png'; ?>" style="height: 15px;">								
								<?php
							}
							?>
						</td>
					</tr>
					<tr>
						<td width="60%" style="background-color: #365249; border-bottom: 4px solid white; color: #ffffff;"><b><?php echo __( 'Does or did the applicant have any live disciplinary warnings with your organisation? If yes, please comment on the nature of these warnings below:', 'myrtle-learning-dashboard' ); ?></b></td>
						<td width="40%" style="background-color: #ffffff; background-color: #e4e4e4; border-bottom: 4px solid white;">							
							<?php
							if( 'yes' == $disciplinary ) {
								?>
								<label><?php echo __( 'Yes', 'myrtle-learning-dashboard' ); ?></label>
								<img src="<?php echo MLD_ASSETS_URL.'images/approved.png'; ?>" style="height: 15px;">
								<label><?php echo __( 'No', 'myrtle-learning-dashboard' ); ?></label>
								<img src="<?php echo MLD_ASSETS_URL.'images/unchecked.png'; ?>" style="height: 15px;">
								<?php
							} else {
								?>
								<label><?php echo __( 'Yes', 'myrtle-learning-dashboard' ); ?></label>
								<img src="<?php echo MLD_ASSETS_URL.'images/unchecked.png'; ?>" style="height: 15px;">
								<label><?php echo __( 'No', 'myrtle-learning-dashboard' ); ?></label>
								<img src="<?php echo MLD_ASSETS_URL.'images/approved.png'; ?>" style="height: 15px;">								
								<?php
							}
							?>
						</td>
					</tr>
				</table>
				<table cellpadding="10">
					<tr>
						<td style="background-color: #365249; color: #ffffff;"><b><?php echo __( 'What was the reason for the applicant leaving your organisation?', 'myrtle-learning-dashboard' ); ?></b></td>
					</tr>
					<tr>
						<td style="background-color: #e4e4e4; border-bottom: 4px solid white;"><?php echo $l_reason; ?></td>
					</tr>
				</table>
				<table cellpadding="10">
					<tr>
						<td width="60%" style="background-color: #365249; border-bottom: 4px solid white; color: #ffffff;"><b><?php echo __( 'Did you find the applicant to be honest and trustworthy?', 'myrtle-learning-dashboard' ); ?></b></td>
						<td width="40%" style="background-color: #ffffff; background-color: #e4e4e4; border-bottom: 4px solid white;">							
							<?php
							if( 'yes' == $re_employ ) {
								?>
								<label><?php echo __( 'Yes', 'myrtle-learning-dashboard' ); ?></label>
								<img src="<?php echo MLD_ASSETS_URL.'images/approved.png'; ?>" style="height: 15px;">
								<label><?php echo __( 'No', 'myrtle-learning-dashboard' ); ?></label>
								<img src="<?php echo MLD_ASSETS_URL.'images/unchecked.png'; ?>" style="height: 15px;">
								<?php
							} else {
								?>
								<label><?php echo __( 'Yes', 'myrtle-learning-dashboard' ); ?></label>
								<img src="<?php echo MLD_ASSETS_URL.'images/unchecked.png'; ?>" style="height: 15px;">
								<label><?php echo __( 'No', 'myrtle-learning-dashboard' ); ?></label>
								<img src="<?php echo MLD_ASSETS_URL.'images/approved.png'; ?>" style="height: 15px;">								
								<?php
							}
							?>
						</td>
					</tr>
					<tr>
						<td width="60%" style="background-color: #365249; border-bottom: 4px solid white; color: #ffffff;"><b><?php echo __( 'Did you find the applicant to be reliable in carrying out his/her duties?', 'myrtle-learning-dashboard' ); ?></b></td>
						<td width="40%" style="background-color: #ffffff; background-color: #e4e4e4; border-bottom: 4px solid white;">							
							<?php
							if( 'yes' == $j_describe ) {
								?>
								<label><?php echo __( 'Yes', 'myrtle-learning-dashboard' ); ?></label>
								<img src="<?php echo MLD_ASSETS_URL.'images/approved.png'; ?>" style="height: 15px;">
								<label><?php echo __( 'No', 'myrtle-learning-dashboard' ); ?></label>
								<img src="<?php echo MLD_ASSETS_URL.'images/unchecked.png'; ?>" style="height: 15px;">
								<?php
							} else {
								?>
								<label><?php echo __( 'Yes', 'myrtle-learning-dashboard' ); ?></label>
								<img src="<?php echo MLD_ASSETS_URL.'images/unchecked.png'; ?>" style="height: 15px;">
								<label><?php echo __( 'No', 'myrtle-learning-dashboard' ); ?></label>
								<img src="<?php echo MLD_ASSETS_URL.'images/approved.png'; ?>" style="height: 15px;">								
								<?php
							}
							?>
						</td>
					</tr>
				</table>
				<table cellpadding="10">
					<tr>
						<td style="background-color: #365249; color: #ffffff;"><b><?php echo __( 'Is the applicant able to demonstrate that s/he meets the requirements of the person specification?', 'myrtle-learning-dashboard' ); ?></b></td>
					</tr>
					<tr>
						<td style="background-color: #e4e4e4; border-bottom: 4px solid white;"><?php echo $a_specification; ?></td>
					</tr>
				</table>
				<table cellpadding="10">
					<tr>
						<td width="60%" style="background-color: #365249; border-bottom: 4px solid white; color: #ffffff;"><b><?php echo __( 'Are you satisfied that the candidate is suitable to work with children?', 'myrtle-learning-dashboard' ); ?></b></td>
						<td width="40%" style="background-color: #ffffff; background-color: #e4e4e4; border-bottom: 4px solid white;">							
							<?php
							if( 'yes' == $w_w_children ) {
								?>
								<label><?php echo __( 'Yes', 'myrtle-learning-dashboard' ); ?></label>
								<img src="<?php echo MLD_ASSETS_URL.'images/approved.png'; ?>" style="height: 15px;">
								<label><?php echo __( 'No', 'myrtle-learning-dashboard' ); ?></label>
								<img src="<?php echo MLD_ASSETS_URL.'images/unchecked.png'; ?>" style="height: 15px;">
								<?php
							} else {
								?>
								<label><?php echo __( 'Yes', 'myrtle-learning-dashboard' ); ?></label>
								<img src="<?php echo MLD_ASSETS_URL.'images/unchecked.png'; ?>" style="height: 15px;">
								<label><?php echo __( 'No', 'myrtle-learning-dashboard' ); ?></label>
								<img src="<?php echo MLD_ASSETS_URL.'images/approved.png'; ?>" style="height: 15px;">								
								<?php
							}
							?>
						</td>
					</tr>
				</table>
				<table cellpadding="10">
					<tr>
						<td style="background-color: #365249; color: #ffffff;"><b><?php echo __( 'If you have answered ‘no’ to the above question, please specify your concerns and why you believe the individual may not be suitable.', 'myrtle-learning-dashboard' ); ?></b></td>
					</tr>
					<tr>
						<td style="background-color: #e4e4e4; border-bottom: 4px solid white;"><?php echo $w_w_children_ans; ?></td>
					</tr>
					<tr>
						<td style="background-color: #365249; color: #ffffff;"><b><?php echo __( "Please provide any further comments on the applicant's suitability for employment into the post described above.", 'myrtle-learning-dashboard' ); ?></b></td>
					</tr>
					<tr>
						<td style="background-color: #e4e4e4; border-bottom: 4px solid white;"><?php echo $further_comment; ?></td>
					</tr>
				</table>
				<table cellpadding="10">
					<tr>
						<td width="30%" style="background-color: #365249; border: 1px solid white; color: #ffffff;"><b><?php echo __( 'Name', 'myrtle-learning-dashboard' ); ?></b></td>
						<td width="70%" style="background-color: #e4e4e4; border: 1px solid white;"><?php echo $name; ?></td>
					</tr>
					<tr>
						<td width="30%" style="background-color: #365249;  border: 1px solid white; color: #ffffff;"><b><?php echo __( 'Signed', 'myrtle-learning-dashboard' ); ?></b></td>
						<td width="70%" style="background-color: #e4e4e4; border: 1px solid white; "><img src="<?php echo $sign_url; ?>" style="height: 20px;"></td>
					</tr>
					<tr>
						<td width="30%" style="background-color: #365249;  border: 1px solid white; color: #ffffff;"><b><?php echo __( 'Date', 'myrtle-learning-dashboard' ); ?></b></td>
						<td width="70%" style="background-color: #e4e4e4; border: 1px solid white; "><?php echo $date; ?></td>
					</tr>
					<tr>
						<td width="30%" style="background-color: #365249;  border: 1px solid white; color: #ffffff;"><b><?php echo __( 'Telephone#', 'myrtle-learning-dashboard' ); ?></b></td>
						<td width="70%" style="background-color: #e4e4e4; border: 1px solid white; "><?php echo $telephone; ?></td>
					</tr>
					<tr>
						<td width="30%" style="background-color: #365249;  border: 1px solid white; color: #ffffff;"><b><?php echo __( 'Organisation stamp	', 'myrtle-learning-dashboard' ); ?></b></td>
						<td width="70%" style="background-color: #e4e4e4; border: 1px solid white; "><img src="<?php echo $stump_url; ?>" style="height: 20px;"></td>
					</tr>
				</table>
				<?php
			}
		}

		$content = ob_get_contents();
		ob_get_clean();

		$page_height = strlen( $content ) / 10;
		$pdf_page_format = PDF_PAGE_FORMAT;
		$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, $pdf_page_format, true, 'UTF-8', false);
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor( 'LRC' );
		$pdf->SetTitle( 'LRC Course Outline' );
		$pdf->SetSubject( 'LRC Outline' );

		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
		$pdf->setHeaderData( '', 0, '', '', [ 0, 0, 0 ], [ 255, 255, 255 ] );

		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		$pdf->AddPage();

		$pdf->writeHTML( $content, true, false, true, false, '' );

		$pdf->lastPage();
		ob_clean();

		$pdf->Output( 'pdf_course_outline', 'I' );
		die();	
	}
}