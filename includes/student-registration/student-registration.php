<?php
/**
 * Student Registration
 */

if( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class Student_Registration
 */
class Student_Registration {

	/**
	 * @var self
	 */
	private static $instance = null;

	/**
	 * @since 1.0
	 * @return $this
	 */
	public static function instance() {

		if ( is_null( self::$instance ) && ! ( self::$instance instanceof Student_Registration ) ) {
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
		add_shortcode( 'user-registration', [ $this, 'mld_user_registration_form' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'mld_user_registration_scripts' ] );
		add_action( 'wp_ajax_save_user_registration_data', [ $this, 'mld_save_user_registration_data' ] );
		add_action( 'wp_ajax_nopriv_save_user_registration_data', [ $this, 'mld_save_user_registration_data' ] );
		add_action( 'wp_ajax_user_is_exists', [ $this, 'mld_user_is_exists' ] );
		add_action( 'wp_ajax_nopriv_user_is_exists', [ $this, 'mld_user_is_exists' ] );
		add_action( 'wp_ajax_email_is_exists', [ $this, 'mld_email_is_exists' ] );
		add_action( 'wp_ajax_nopriv_email_is_exists', [ $this, 'mld_email_is_exists' ] );
		add_filter( 'learndash_quiz_question_result', [ $this, 'mld_refector_fill_in_blanks' ] );
		// add_action( 'wp_mail_failed', function( $wp_error ) {
		// 	var_dump( $wp_error->get_error_messages() );
		// } );
	}

	/**
	 * refector fill in the blanks
	 */
	public function mld_refector_fill_in_blanks( $array ) {

		$type = isset( $array['e']['type'] ) ? $array['e']['type'] : '';
		if( 'cloze_answer' != $type ) {
			return $array;
		}
		$user_answer = isset( $array['e']['r'][0] ) ? preg_replace( '/\s+/', '', $array['e']['r'][0] ) : '';
		$question_answer = strip_tags($array['e']['c'][0][0]);
		$no_space_question_answer = preg_replace('/\s+/', '', $question_answer);

		if( strtolower( $user_answer ) == strtolower( $no_space_question_answer ) ) {

			$array['p'] = $array['e']['possiblePoints'];
			$array['c'] = true;
			$array['s']->{"0"} = true;	
		}
		return $array;
	}

	/**
	 * check email is exists or not
	 */
	public function mld_email_is_exists() {
		
		$email_response = [];

		// if( ! wp_verify_nonce( $_POST['mld_nounce'], 'mld_ajax_nonce' ) ) {

		// 	$response['message'] = __( 'data not found', 'myrtle-learning-dashboard' );
		// 	$response['status'] = 'false';

		// 	echo json_encode( $response );
		// 	wp_die();
		// }

		$email = isset( $_POST['email'] ) ? $_POST['email'] : '';
		
		if( ! $email ) {

			$email_response['message'] = __( 'email not found', 'myrtle-learning-dashboard' );
			$email_response['status'] = 'false';

			echo json_encode( $email_response );
			wp_die();
		}

		if( email_exists( $email ) ) {

			$email_response['message'] = __( 'Email exists', 'myrtle-learning-dashboard' );
			$email_response['status'] = 'true';
			echo json_encode( $email_response );
			wp_die();
		}		
	}

	/**
	 * check user is exists or not
	 */
	public function mld_user_is_exists() {
		
		$response = [];

		// if( ! wp_verify_nonce( $_POST['mld_nounce'], 'mld_ajax_nonce' ) ) {

		// 	$response['message'] = __( 'asad data not found', 'myrtle-learning-dashboard' );
		// 	$response['status'] = 'false';

		// 	echo json_encode( $response );
		// 	wp_die();
		// }

		$username = isset( $_POST['username'] ) ? $_POST['username'] : '';

		if( ! $username ) {

			$response['message'] = __( 'username not found', 'myrtle-learning-dashboard' );
			$response['status'] = 'false';

			echo json_encode( $response );
			wp_die();
		}

		if( username_exists( $username ) ) {

			$response['message'] = __( 'username exists', 'myrtle-learning-dashboard' );
			$response['status'] = 'true';
			$random_number = rand( 10, 10000000 );
			$response['suggestion'] = $username.'_'.$random_number;
			echo json_encode( $response );
			wp_die();
		}
	}

	/**
	 * save user registration fields
	 */
	public function mld_save_user_registration_data() {

		$first_name = isset( $_POST['first_name'] ) ? $_POST['first_name'] : '';
		$last_name = isset( $_POST['last_name'] ) ? $_POST['last_name'] : '';
		$useremail = isset( $_POST['email'] ) ? $_POST['email'] : '';
		$username = isset( $_POST['username'] ) ? $_POST['username'] : '';
		$date_of_birth = isset( $_POST['date_of_birth'] ) ? $_POST['date_of_birth'] : '';
		$year_group = isset( $_POST['year_group'] ) ? $_POST['year_group'] : '';
		$school = isset( $_POST['school'] ) ? $_POST['school'] : '';
		$parent_name = isset( $_POST['parent_name'] ) ? $_POST['parent_name'] : '';
		$phone_number = isset( $_POST['phone_number'] ) ? $_POST['phone_number'] : '';
		$home_tel = isset( $_POST['home_tel'] ) ? $_POST['home_tel'] : '';
		$address = isset( $_POST['address'] ) ? $_POST['address'] : '';
		$parent_email = isset( $_POST['parent_email'] ) ? $_POST['parent_email'] : '';
		$medical_option = isset( $_POST['medical_option'] ) ? $_POST['medical_option'] : '';
		$extra_support_option = isset( $_POST['extra_support_option'] ) ? $_POST['extra_support_option'] : '';
		$allergies_option = isset( $_POST['allergies_option'] ) ? $_POST['allergies_option'] : '';
		$medical_detailed = isset( $_POST['medical_detailed'] ) ? $_POST['medical_detailed'] : '';
		$extra_support_detailed = isset( $_POST['extra_support_detailed'] ) ? $_POST['extra_support_detailed'] : '';
		$allergies_detailed = isset( $_POST['allergies_detailed'] ) ? $_POST['allergies_detailed'] : '';
		$other_courses = isset( $_POST['other_courses'] ) ? $_POST['other_courses'] : '';
		$tuition = isset( $_POST['tuition'] ) ? $_POST['tuition'] : '';
		$courses = isset( $_POST['courses'] ) ? $_POST['courses'] : '';
		$examination_board = isset( $_POST['examination_board'] ) ? $_POST['examination_board'] : '';

		$password = wp_generate_password(12);
		$user_id = wp_create_user( $username, $password, $useremail );

		if ( ! is_wp_error( $user_id ) ) {

			$user = new WP_User( $user_id );
			$user->set_role('');

			require_once ABSPATH . 'wp-admin/includes/file.php';
			$uploaded_file = wp_handle_upload( $_FILES['profile'], ['test_form' => false] ); 

			if ( isset( $uploaded_file['url'] ) ) {
				$avatar_url = $uploaded_file['url']; 
				update_user_meta($user_id, 'mld_user_avatar', $avatar_url);
			}

			$userdata = [
				'mld_user_first_name' 			=> $first_name,
				'mld_user_last_name' 			=> $last_name,
				'mld_user_email' 				=> $useremail,
				'mld_user_username' 			=> $username,
				'mld_user_dob' 					=> $date_of_birth,
				'mld_user_year_group' 			=> $year_group,
				'mld_user_school' 				=> $school,
				'mld_user_parent_name' 			=> $parent_name,
				'mld_phone_number'				=> $phone_number,
				'mld_home_tel'					=> $home_tel,
				'mld_address'					=> $address,
				'mld_user_parent_email' 		=> $parent_email,
				'mld_user_medical_option' 		=> $medical_option,
				'mld_user_support_option' 		=> $extra_support_option,
				'mld_user_allergies_option' 	=> $allergies_option,
				'mld_user_medical_detailed' 	=> $medical_detailed,
				'mld_user_support_detailed' 	=> $extra_support_detailed,
				'mld_user_allergies_detailed' 	=> $allergies_detailed,
				'mld_user_other_courses' 		=> $other_courses,
				'mld_user_tuition' 				=> $tuition,
				'mld_user_courses' 				=> $courses,
				'mld_user_examination_board'	=> $examination_board
			];

			foreach( $userdata as $key => $data ) {
				if( $data ) {
					update_user_meta( $user_id, $key, $data );
				}
			}

			$headers = array('Content-Type: text/html; charset=UTF-8');
			$header_url = MLD_ASSETS_URL.'images/header.PNG';
			$footer_url = MLD_ASSETS_URL.'images/footer.PNG';

			$content .= '<p></p>';
			$content .= '<img src="'.$header_url.'" style="width: 99%;">';
			$content .= '<div style="font-size: 20px; color: #365249;">Student Registration</div>';
			$content .= '<div style="font-weight: 600; font-size: 20px; color: #365249;">Confirmation Email</div>';
			$content .= '<div style="color: #365249; margin: 15px 0 15px 0; font-size="15px">Thank you for <b>completing the registration</b> with Myrtle Learning.</div>';
			$content .= '<div style="color: #365249; font-size: 15px; margin: 15px 0 15px 0;">Your registration is currently under review and we will be in touch with you
			in due course to arrange the next steps.</div>';
			$content .= '<div style="color: #365249; font-size: 15px; margin: 15px 0 15px 0;">We would confirm via email, the approval of your registration or otherwise.</div>';
			$content .= '<div style="color: #365249; font-size: 15px; margin: 15px 0 15px 0;">Thank you</div>';
			$content .= '<div style="color: #365249; font-weight: 600;">The Myrtle Learning Team</div>';
			$content .= '<p></p>';
			$content .= '<img src="'.$footer_url.'" style="width: 99%;">';

			wp_mail( $useremail, 'Student Registration', $content, $headers );

			/**
			 * send email to admin
			 */
			$upload_photo = get_user_meta( $user_id, 'mld_user_avatar', true );
			$admin_email_content .= '<img src="'.$header_url.'" style="width: 99%;">';
			$admin_email_content .= '<p></p>';
			$admin_email_content .= '<div style="font-weight: 600;">Admin/Student Registration Email</div>';
			$admin_email_content .= '<div style="font-weight: 400;">'.__( 'Student', 'myrtle-learning-dashboard' ).'</div>';
			$admin_email_content .= '<table style="width: 100%;">';
			$admin_email_content .= '<tr><td width="40%" style="background-color:#18440a; color: white; padding: 10px;">First Name</td><td width="60%" style="background-color: #e4e4e4; padding: 10px; font-weight: 600;">'.$first_name.'</td></tr>';
			$admin_email_content .= '<tr><td width="40%" style="background-color:#18440a; color: white; padding: 10px;">Last Name</td><td width="60%" style="background-color: #e4e4e4; padding: 10px; font-weight: 600;">'.$last_name.'</td></tr>';
			$admin_email_content .= '<tr><td width="40%" style="background-color:#18440a; color: white; padding: 10px;">Email</td><td width="60%" style="background-color: #e4e4e4; padding: 10px;">'.$useremail.'</td></tr>';
			$admin_email_content .= '<tr><td width="40%" style="background-color:#18440a; color: white; padding: 10px;">Username</td><td width="60%" style="background-color: #e4e4e4; padding: 10px; font-weight: 600;">'.$username.'</td></tr>';
			$admin_email_content .= '<tr><td width="40%" style="background-color:#18440a; color: white; padding: 10px;">Upload Photo</td><td width="60%" style="background-color: #e4e4e4; padding: 10px;"><img src="'.$upload_photo.'" style="height: 45px;"></td></tr>';
			$admin_email_content .= '<tr><td width="40%" style="background-color:#18440a; color: white; padding: 10px;">Date of Birth</td><td width="60%" style="background-color: #e4e4e4; padding: 10px; font-weight: 600;">'.$date_of_birth.'</td></tr>';
			$admin_email_content .= '<tr><td width="40%" style="background-color:#18440a; color: white; padding: 10px;">Year Group</td><td width="60%" style="background-color: #e4e4e4; padding: 10px; font-weight: 600;">'.$year_group.'</td></tr>';
			$admin_email_content .= '<tr><td width="40%" style="background-color:#18440a; color: white; padding: 10px;">School</td><td width="60%" style="background-color: #e4e4e4; padding: 10px; font-weight: 600;">'.$school.'</td></tr>';
			$admin_email_content .= '<tr><td width="40%" style="background-color:#18440a; color: white; padding: 10px;">Parent Name</td><td width="60%" style="background-color: #e4e4e4; padding: 10px;">'.$parent_name.'</td></tr>';
			$admin_email_content .= '<tr><td width="40%" style="background-color:#18440a; color: white; padding: 10px;">Phone Numbers</td><td width="60%" style="background-color: #e4e4e4; padding: 10px; font-weight: 600;">'.$phone_number.'</td></tr>';
			$admin_email_content .= '<tr><td width="40%" style="background-color:#18440a; color: white; padding: 10px;">Home Tel</td><td width="60%" style="background-color: #e4e4e4; padding: 10px; font-weight: 600;">'.$home_tel.'</td></tr>';
			$admin_email_content .= '<tr><td width="40%" style="background-color:#18440a; color: white; padding: 10px;">Address</td><td width="60%" style="background-color: #e4e4e4; padding: 10px; font-weight: 600;">'.$address.'</td></tr>';
			$admin_email_content .= '<tr><td width="40%" style="background-color:#18440a; color: white; padding: 10px;">Parent Email</td><td width="60%" style="background-color: #e4e4e4; padding: 10px;">'.$parent_email.'</td></tr>';
			$admin_email_content .= '<tr><td width="40%" style="background-color:#18440a; color: white; padding: 10px;">Has your child got any medical condition?</td><td width="60%" style="background-color: #e4e4e4; padding: 10px;">'.$medical_option.'</td></tr>';
			
			if( 'Yes' == $medical_option ) {
				$admin_email_content .= '<tr style="background-color: #e4e4e463;"><td colspan="2" style="padding: 10px; border: unset; background-color: #e4e4e463;"><div style="width: 100%;">'.$medical_detailed.'</div></td></tr>';
			}
			$admin_email_content .= '<tr><td width="40%" style="background-color:#18440a; color: white; padding: 10px;">Does your child receive any extra support in school?</td><td width="60%" style="background-color: #e4e4e4; padding: 10px;">'.$extra_support_option.'</td></tr>';
			if( 'Yes' == $extra_support_option ) {
				$admin_email_content .= '<tr><td colspan="2" style="padding: 10px; border: unset; background-color: #e4e4e463;"><div style="width: 100%;">'.$extra_support_detailed.'</div></td></tr>';
			}
			$admin_email_content .= '<tr><td width="40%" style="background-color:#18440a; color: white; padding: 10px;">Has your child got any allergies?</td><td width="60%" style="background-color: #e4e4e4; padding: 10px;">'.$allergies_option.'</td></tr>';
			if( 'Yes' == $allergies_option ) {
				$admin_email_content .= '<tr><td colspan="2" style="padding: 10px; border: unset; background-color: #e4e4e463;"><div style="width: 100%;">'.$allergies_detailed.'</div></td></tr>';
			}
			$admin_email_content .= '<tr><td width="40%" style="background-color:#18440a; color: white; padding: 10px;">Level Required</td><td width="60%" style="background-color: #e4e4e4; padding: 10px;">'.$tuition.'</td></tr>';
			$admin_email_content .= '<tr><td width="40%" style="background-color:#18440a; color: white; padding: 10px;">Level Required</td><td width="60%"style="background-color: #e4e4e4; padding: 10px;">'.$courses.'</td></tr>';
			$admin_email_content .= '</table>';
			$admin_email_content .= '<p></p>';
			$admin_email_content .= '<img src="'.$footer_url.'" style="width: 99%;">';

			// $admin_email = get_option('admin_email');
			$admin_email = 'hello@myrtlelearning.com';
			wp_mail( $admin_email, 'Student Registration', $admin_email_content, $headers );
		}
	}

	/**
	 * enqueue scripts file
	 */ 
	public function mld_user_registration_scripts() {

		if( has_shortcode( get_the_content( get_the_ID() ), 'user-registration' ) ) {

			$rand = rand( 1000000, 1000000000 );
			wp_enqueue_style( 'mld-user-registration-css', MLD_ASSETS_URL .'css/user-registration.css', '', $rand, false );
			wp_enqueue_script( 'mld-user-registration-js', MLD_ASSETS_URL . 'js/mld-user-registration.js', [ 'jquery' ], $rand, true );
			wp_localize_script( 'mld-user-registration-js', 'MLD', [
				'ajaxURL'       => admin_url( 'admin-ajax.php' ),
				'siteURL'		=> site_url(),
				'security'      => wp_create_nonce( 'mld_ajax_nonce' )
			] );
			// wp_enqueue_script( 'mld-recaptcha-js', 'https://www.google.com/recaptcha/api.js', [ 'jquery' ], $rand, true );
		}
	}

	/**
	 * create a shortcode for user registration form
	 */	
	public function mld_user_registration_form() {

		ob_start();
		?>
		<div class="mld-form-head-wrap">
			<img src="<?php echo MLD_ASSETS_URL.'images/logo.png'; ?>">
			<div class="mld-student-form-heading">
				<?php echo __( 'Students Application Form', 'myrtle-learning-dashboard' ); ?>
			</div>
		</div>
		<div class="mld-user-registration-wrapper">
			
			<div class="stepper">
				<div class="step step-one">
					<div class="circle active">1</div>
					<div class="line"></div>
				</div>
				<div class="step step-two">
					<div class="circle">2</div>
					<div class="line"></div>
				</div>
				<div class="step step-three">
					<div class="circle">3</div>
					<div class="line"></div>
				</div>
				<div class="step step-four">
					<div class="circle">4</div>
				</div>
			</div>
			
			<div class="mld-user-registration-first-page">
				<div class="mld-sub-heading">
					<?php echo __( 'Student', 'myrtle-learning-dashboard' ); ?>
				</div>
				<div class="mld-user-reg-fields-wrapper">
					<div class="mld-user-first-name">
						<label><?php echo __( 'First Name', 'myrtle-learning-dashboard' ); ?></label>
						<span class="mld-imporant">*</span>
						<input type="text" placeholder="<?php echo __( 'Enter your first name', 'myrtle-learning-dashboard' ); ?>" class="mld-user-f-name mld-important">
					</div>
					<div class="mld-user-last-name">
						<label><?php echo __( 'Last Name', 'myrtle-learning-dashboard' ); ?></label>
						<span class="mld-imporant">*</span>
						<input type="text" placeholder="<?php echo __( 'Enter your last name', 'myrtle-learning-dashboard' ); ?>" class="mld-user-l-name mld-important">
					</div>
					<div class="mld-clear-both"></div>
				</div>
				<div class="mld-user-reg-fields-wrapper">
					<label><?php echo __( 'Email', 'myrtle-learning-dashboard' ); ?></label>
					<span class="mld-imporant">*</span>
					<input type="email" placeholder="<?php echo __( 'Enter your email address', 'myrtle-learning-dashboard' ); ?>" class="mld-u-reg-email mld-important">
				</div>
				<div class="mld-user-reg-fields-wrapper">
					<label><?php echo __( 'Username', 'myrtle-learning-dashboard' ); ?></label>
					<span class="mld-imporant">*</span>
					<input type="text" placeholder="<?php echo __( 'Enter your username', 'myrtle-learning-dashboard' ); ?>" class="mld-u-reg-username mld-important">
				</div>
				<div class="mld-user-reg-fields-wrapper">
					<label><?php echo __( 'Upload Photo', 'myrtle-learning-dashboard' ); ?></label>
					<input type="file" class="mld-u-reg-photo">
				</div>
			</div>

			<div class="mld-user-registration-second-page">
				<div class="mld-user-reg-fields-wrapper">
					<div class="mld-user-date-birth">
						<label><?php echo __( 'Date of Birth', 'myrtle-learning-dashboard' ); ?></label>
						<span class="mld-imporant">*</span>
						<input type="date" placeholder="<?php echo __( 'Select date of birth', 'myrtle-learning-dashboard' ); ?>" class="mld-user-db mld-important">
					</div>
					<div class="mld-user-y-group">
						<label><?php echo __( 'Year Group', 'myrtle-learning-dashboard' ); ?></label>
						<span class="mld-imporant">*</span>
						<input type="text" placeholder="<?php echo __( 'Enter your year group', 'myrtle-learning-dashboard' ); ?>" class="mld-user-year-group mld-important">
					</div>
					<div class="mld-clear-both"></div>
				</div>
				<div class="mld-user-reg-fields-wrapper">
					<label><?php echo __( 'School', 'myrtle-learning-dashboard' ); ?></label>
					<span class="mld-imporant">*</span>
					<input type="text" placeholder="<?php echo __( 'Enter your school', 'myrtle-learning-dashboard' ); ?>" class="mld-u-reg-schl mld-important">
				</div>
				<div class="mld-sub-heading">
					<?php echo __( 'Parent Information', 'myrtle-learning-dashboard' ); ?>
				</div>
				<div class="mld-user-reg-fields-wrapper">
					<label><?php echo __( 'Parent Name', 'myrtle-learning-dashboard' ); ?></label>
					<span class="mld-imporant">*</span>
					<input type="text" placeholder="<?php echo __( 'Enter your Parent name', 'myrtle-learning-dashboard' ); ?>" class="mld-u-reg-parent-name mld-important">
				</div>
				<div class="mld-user-reg-fields-wrapper">
					<label><?php echo __( 'Phone Numbers', 'myrtle-learning-dashboard' ); ?></label>
					<span class="mld-imporant">*</span>
					<input type="text" placeholder="<?php echo __( 'Enter your phone number', 'myrtle-learning-dashboard' ); ?>" class="mld-u-reg-phone mld-important">
				</div>
				<div class="mld-user-reg-fields-wrapper">
					<label><?php echo __( 'Home Tel', 'myrtle-learning-dashboard' ); ?></label>
					<span class="mld-imporant">*</span>
					<input type="text" placeholder="<?php echo __( 'Enter your home tel', 'myrtle-learning-dashboard' ); ?>" class="mld-u-reg-h-tel mld-important">
				</div>
				<div class="mld-user-reg-fields-wrapper">
					<label><?php echo __( 'Address', 'myrtle-learning-dashboard' ); ?></label>
					<span class="mld-imporant">*</span>
					<input type="text" placeholder="<?php echo __( 'Enter your addrerss', 'myrtle-learning-dashboard' ); ?>" class="mld-u-reg-address mld-important">
				</div>
				<div class="mld-user-reg-fields-wrapper">
					<label><?php echo __( 'Parent Email', 'myrtle-learning-dashboard' ); ?></label>
					<span class="mld-imporant">*</span>
					<input type="text" placeholder="<?php echo __( 'Enter your parent email', 'myrtle-learning-dashboard' ); ?>" class="mld-u-reg-parent-email mld-important">
				</div>
			</div>

			<div class="mld-user-registration-third-page">
				<div class="mld-sub-heading">
					<?php echo __( 'Medical Condition', 'myrtle-learning-dashboard' ); ?>
				</div>
				<div class="mld-medi-condition">
					<div class="mld-condition-text">
						<?php echo __( 'Has your child got any medical condition?', 'myrtle-learning-dashboard' ); ?>
					</div>
					<div class="mld-condition-option">
						<input type="radio" class="medical-condition-yes">
						<label><?php echo __( 'Yes', 'myrtle-learning-dashboard' ); ?></label>
						<input type="radio" class="medical-condition-no">
						<label><?php echo __( 'No', 'myrtle-learning-dashboard' ); ?></label>
					</div>
					<div class="mld-condition-detail">
						<?php echo __( 'If YES, please give brief details below:', 'myrtle-learning-dashboard' ); ?>
						<textarea placeholder="<?php echo __( 'Detailed message', 'myrtle-learning-dashboard' ); ?>"></textarea>
					</div>
				</div>
				<div class="mld-extra-support">
					<div class="mld-condition-text">
						<?php echo __( 'Does your child receive any extra support in school? YES / NO', 'myrtle-learning-dashboard' ); ?>
					</div>
					<div class="mld-condition-option">
						<input type="radio" class="extra-support-yes">
						<label><?php echo __( 'Yes', 'myrtle-learning-dashboard' ); ?></label>
						<input type="radio" class="extra-support-no">
						<label><?php echo __( 'No', 'myrtle-learning-dashboard' ); ?></label>
					</div>
					<div class="mld-condition-detail">
						<?php echo __( 'If YES, please give brief details below:', 'myrtle-learning-dashboard' ); ?>
						<textarea placeholder="<?php echo __( 'Detailed message', 'myrtle-learning-dashboard' ); ?>"></textarea>
					</div>
				</div>
				<div class="mld-allergies">
					<div class="mld-condition-text">
						<?php echo __( 'Has your child got any allergies? YES / NO', 'myrtle-learning-dashboard' ); ?>
					</div>
					<div class="mld-condition-option">
						<input type="radio" class="allergies-yes">
						<label><?php echo __( 'Yes', 'myrtle-learning-dashboard' ); ?></label>
						<input type="radio" class="allergies-no">
						<label><?php echo __( 'No', 'myrtle-learning-dashboard' ); ?></label>
					</div>
					<div class="mld-condition-detail">
						<?php echo __( 'If YES, please give brief details below:', 'myrtle-learning-dashboard' ); ?>
						<textarea placeholder="<?php echo __( 'Detailed message', 'myrtle-learning-dashboard' ); ?>"></textarea>
					</div>
				</div>
			</div>

			<div class="mld-user-registration-fourth-page">
				<div class="mld-sub-heading">
					<?php echo __( 'Tuition Required', 'myrtle-learning-dashboard' ); ?>
				</div>
				<div class="mld-tuition-wrapper">
					<div class="mld-tuition-required-text">
						<?php echo __( 'Level Required (Please tick as appropriate)', 'myrtle-learning-dashboard' ); ?>
					</div>
					<input type="checkbox">
					<label><?php echo __( 'KS1', 'myrtle-learning-dashboard' ); ?></label>
					<input type="checkbox">
					<label><?php echo __( 'KS2', 'myrtle-learning-dashboard' ); ?></label>
					<input type="checkbox">
					<label><?php echo __( 'KS3', 'myrtle-learning-dashboard' ); ?></label>
					<input type="checkbox">
					<label><?php echo __( 'KS4', 'myrtle-learning-dashboard' ); ?></label>
					<input type="checkbox">
					<label><?php echo __( 'KS5', 'myrtle-learning-dashboard' ); ?></label>
				</div>
				<div class="mld-sub-heading">
					<?php echo __( 'Courses', 'myrtle-learning-dashboard' ); ?>
				</div>
				<div class="mld-courses-wrapper">
					<div class="mld-tuition-required-text">
						<?php echo __( 'Level Required (Please tick as appropriate)', 'myrtle-learning-dashboard' ); ?>
					</div>
					<div class="mld-course-check-boxes">
						<input type="checkbox">
						<label><?php echo __( '11 Plus', 'myrtle-learning-dashboard' ); ?></label>
						<input type="checkbox">
						<label><?php echo __( 'KS1 English', 'myrtle-learning-dashboard' ); ?></label>
						<input type="checkbox">
						<label><?php echo __( 'KS1 Mathematics', 'myrtle-learning-dashboard' ); ?></label>
						<input type="checkbox">
						<label><?php echo __( 'KS2 English', 'myrtle-learning-dashboard' ); ?></label>
						<input type="checkbox">
						<label><?php echo __( 'KS2 Mathematics', 'myrtle-learning-dashboard' ); ?></label>
						<input type="checkbox">
						<label><?php echo __( 'KS3 English', 'myrtle-learning-dashboard' ); ?></label>
						<input type="checkbox">
						<label><?php echo __( 'KS3 Mathematics', 'myrtle-learning-dashboard' ); ?></label>
						<input type="checkbox">
						<label><?php echo __( 'KS3 Science', 'myrtle-learning-dashboard' ); ?></label>
						<input type="checkbox">
						<label><?php echo __( 'IGCSE', 'myrtle-learning-dashboard' ); ?></label>
						<input type="checkbox">
						<label><?php echo __( 'GCSE English', 'myrtle-learning-dashboard' ); ?></label>
						<input type="checkbox">
						<label><?php echo __( 'GCSE Mathematics', 'myrtle-learning-dashboard' ); ?></label>
						<input type="checkbox">
						<label><?php echo __( 'GCSE Science', 'myrtle-learning-dashboard' ); ?></label>
						<input type="checkbox">
						<label><?php echo __( 'GCSE Geography', 'myrtle-learning-dashboard' ); ?></label>
						<input type="checkbox">
						<label><?php echo __( 'Reading Club', 'myrtle-learning-dashboard' ); ?></label>
						<input type="checkbox">
						<label><?php echo __( 'Other', 'myrtle-learning-dashboard' ); ?></label>
					</div>
					<div class="mld-courses-detail-wrapper">
						<?php echo __( 'If Other selected, please list the courses below:', 'myrtle-learning-dashboard' ); ?>
						<textarea placeholder="<?php echo __( 'other courses', 'myrtle-learning-dashboard' ); ?>"></textarea>
					</div>	
				</div>
				<div class="mld-sub-heading">
					<?php echo __( 'Examination Boards', 'myrtle-learning-dashboard' ); ?>
				</div>
				<div class="mld-examination-board-wrapper">
					<div class="mld-examination-board-text">
						<?php echo __( '(Please tick as appropriate)', 'myrtle-learning-dashboard' ); ?>
					</div>
					<input type="checkbox">
					<label><?php echo __( 'AQA', 'myrtle-learning-dashboard' ); ?></label>
					<input type="checkbox">
					<label><?php echo __( 'EDEXCEL', 'myrtle-learning-dashboard' ); ?></label>
					<input type="checkbox">
					<label><?php echo __( 'OCR', 'myrtle-learning-dashboard' ); ?></label>
					<input type="checkbox">
					<label><?php echo __( 'CEM', 'myrtle-learning-dashboard' ); ?></label>
					<input type="checkbox">
					<label><?php echo __( 'GL Assessment', 'myrtle-learning-dashboard' ); ?></label>
					<input type="checkbox">
					<label><?php echo __( 'Other', 'myrtle-learning-dashboard' ); ?></label>
				</div>
			</div>

			<div class="mld-user-registration-next">
				<div class="mld-error-msg">
					<?php echo __( 'Please fill all the fields', 'myrtle-learning-dashboard' ); ?>
				</div>
				<button class="mld-user-prev-btn"><?php echo __( 'Previous', 'myrtle-learning-dashboard' ); ?></button>
				<button class="mld-user-next-btn" data-page="1"><?php echo __( 'Next', 'myrtle-learning-dashboard' ); ?></button>
			</div>

			<div class="mld-footer-buttom">
				<button class="mld-registration-terms-condition">
					<?php echo __( 'TERMS & CONDITION', 'myrtle-learning-dashboard' ); ?>
				</button>
			</div>
			<?php 
			echo do_shortcode( '[terms_condition_popup]' );
			?>
		</div>
		<?php
		$content = ob_get_contents();
		ob_get_clean();
		return $content;
	}
}

Student_Registration::instance();
