<?php
/**
 * Myrtle Learning - Admin Hooks
 *
 */
if( ! defined( 'ABSPATH' ) ) exit;

class MLD_ACCOUNT_MODULE {

	private static $instance;

	private $userlogged;
	private $userid;

	/**
	 * Create class instance
	 */
	public static function instance() {

		if( is_null( self::$instance ) && ! ( self::$instance instanceof MLD_ACCOUNT_MODULE ) ) {

			self::$instance = new MLD_ACCOUNT_MODULE;

			self::$instance->userlogged = is_user_logged_in();
			self::$instance->userid = get_current_user_id();

			self::$instance->hooks();
		}

		return self::$instance;
	}

	/**
	 * Define hooks
	 */
	private function hooks() {
		add_action( 'wp_ajax_upload_admin_pdf', [ $this, 'mld_upload_admin_pdf' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'mld_account_scripts' ] );
		add_action( 'wp_ajax_get_policies', [ $this, 'mld_get_policies' ] );
		add_action( 'wp_ajax_upload_policy_pdf', [ $this, 'mld_upload_policy_pdf' ] );
		add_action( 'wp_ajax_update_password', [ $this, 'mld_update_password' ] );
		add_action( 'wp_ajax_change_user_profile', [ $this, 'mld_change_user_profile' ] );
		add_filter( 'pre_get_avatar', [ $this, 'mld_update_user_profile' ], 10, 3 );
		add_action( 'wp_ajax_delete_policies', [ $this, 'mld_delete_policies' ] );
		add_action( 'wp_ajax_update_contacts', [ $this, 'mld_update_contacts' ] );
        add_action( 'wp_ajax_add_policy_category', [ $this, 'mld_add_category_type' ] );
        add_shortcode( 'personal_and_bank_detail', [ $this, 'mld_shortcode_to_display_personal_and_bank_detail_fields' ] );
		add_shortcode( 'myrtle_account', [ $this, 'mld_myrtle_account' ] );
		add_action( 'wp_ajax_get_account_group_users', [ $this, 'mld_get_account_group_users' ] );
		add_action( 'wp_ajax_get_user_account_html', [ $this, 'mld_get_user_account_content' ] );
		add_action( 'wp_ajax_terms_and_conditions', [ $this, 'mld_terms_and_conditions' ] );
		add_action( 'wp_ajax_pdf_comment_html', [ $this, 'mld_pdf_comment_html' ] );
		add_action( 'wp_ajax_delete_policy_type', [ $this, 'mld_delete_policy_type' ] );
		add_action( 'wp_ajax_update_checklist_data', [ $this, 'mld_update_checklist_data' ] );
		add_action( 'wp_ajax_mld_upload_user_form', [ $this, 'mld_upload_user_form' ] );
		add_action( 'wp_ajax_mld_update_checklist_form', [ $this, 'mld_update_checklist_form' ] );
		add_action( 'wp_ajax_update_parent_communication', [ $this, 'mld_update_parent_communication' ] );
	}

	/**
	 * function to get ip address
	 */
	public static function mld_user_ip_address() {
	
	    $possible_headers = array(
	        'HTTP_CF_CONNECTING_IP',
	        'HTTP_X_FORWARDED_FOR',
	        'HTTP_X_FORWARDED',
	        'HTTP_FORWARDED_FOR',
	        'HTTP_FORWARDED',
	        'HTTP_CLIENT_IP',
	        'REMOTE_ADDR'
	    );

	    foreach ($possible_headers as $header) {
	        if (isset($_SERVER[$header]) && filter_var($_SERVER[$header], FILTER_VALIDATE_IP)) {
	            return $_SERVER[$header];
	        }
	    }

	    return ''; // No valid IP found
	}

	/**
	 * function to timezone
	 */
	public function mld_get_time_zone( $ip, $get ) {

	    $specific_target = '';
	    $url = 'http://ip-api.com/json/' . $ip;
	    $response = wp_remote_get( $url );

	    if( is_array( $response ) ) {

	        $response_body = $response['body'];
	        $response_body = json_decode( $response_body, true );
	        if (isset($response_body[$get])) {
		        $specific_target = $response_body[$get];
		    }
	    }

	    return $specific_target;
	}

	/**
	 * update parent communication
	 */
	public function mld_update_parent_communication() {

		global $wpdb;

		$communication_text = isset( $_POST['message'] ) ? $_POST['message'] : '';
		$logged_in_user = isset( $_POST['logged_in_user'] ) ? intval( $_POST['logged_in_user'] ) : 0;
		$current_user = isset( $_POST['current_user'] ) ? intval( $_POST['current_user'] ) : 0;

		if( ! $logged_in_user || ! $current_user ) {
			wp_die();
		}

		$communication_table = $wpdb->base_prefix . 'mld_client_communication';
				
		$data_to_insert = array(

			'logged_in_user_id' 	=> $logged_in_user,
			'current_user_id' 		=> $current_user,
			'message' 				=> $communication_text,
			'dates'					=> strtotime( "now" )
		);

		$wpdb->insert( $communication_table, $data_to_insert );
		wp_die();
	}

	/**
	 * update checklist form
	 */
	public function mld_update_checklist_form() {

		global $wpdb;		
		$files = $_FILES;

		$user_id = self::$instance->userid;

		$signature_name = isset( $_FILES['email_sign']['name'] ) ? $_FILES['email_sign']['name'] : [];

		$upload_dir = wp_upload_dir();
		if( ! empty( $upload_dir['basedir'] ) ) {

			$sign_upload_dir = $upload_dir['basedir'].'/mld_bank_detail_upload/user_'.$user_id.'/signature-initials-emailed-copies';
			$resource_upload_dir = $upload_dir['basedir'].'/mld_bank_detail_upload/user_'.$user_id.'/signature';

			if ( ! file_exists( $sign_upload_dir ) ) {
				wp_mkdir_p( $sign_upload_dir );
			}

			move_uploaded_file( $_FILES['email_sign']['tmp_name'], $sign_upload_dir . '/' . $signature_name );
		}

		$title = isset( $_POST['title'] ) ? $_POST['title'] : '';
		$forename = isset( $_POST['forename'] ) ? $_POST['forename'] : '';
		$surname = isset( $_POST['surname'] ) ? $_POST['surname'] : '';
		$ni_number = isset( $_POST['ni_number'] ) ? $_POST['ni_number'] : '';
		$dob = isset( $_POST['dob'] ) ? $_POST['dob'] : '';
		$home_address = isset( $_POST['home_address'] ) ? $_POST['home_address'] : '';
		$home_email = isset( $_POST['home_email'] ) ? $_POST['home_email'] : '';
		$mobile_number = isset( $_POST['mobile_number'] ) ? $_POST['mobile_number'] : '';
		$subjects = isset( $_POST['subjects'] ) ? $_POST['subjects'] : '';
		$bank_name = isset( $_POST['bank_name'] ) ? $_POST['bank_name'] : '';
		$account_holder_name = isset( $_POST['account_holder_name'] ) ? $_POST['account_holder_name'] : '';
		$sort_code = isset( $_POST['sort_code'] ) ? $_POST['sort_code'] : '';
		$bank_address = isset( $_POST['bank_address'] ) ? $_POST['bank_address'] : '';
		$account_number = isset( $_POST['account_number'] ) ? $_POST['account_number'] : '';
		$certificate_number = isset( $_POST['certificate_number'] ) ? $_POST['certificate_number'] : '';
		$username_on_certificate = isset( $_POST['username_on_certificate'] ) ? $_POST['username_on_certificate'] : '';
		$current_yn = isset( $_POST['current_yn'] ) ? $_POST['current_yn'] : '';
		$dob_on_certificate = isset( $_POST['dob_on_certificate'] ) ? $_POST['dob_on_certificate'] : '';
		$internal_use = isset( $_POST['internal_use'] ) ? $_POST['internal_use'] : '';
		$signature_name = isset( $_POST['signature_name'] ) ? $_POST['signature_name'] : '';
		$signature_date = isset( $_POST['signature_date'] ) ? $_POST['signature_date'] : '';
		$resource_date = isset( $_POST['resource_date'] ) ? $_POST['resource_date'] : '';
		$resource_name = isset( $_POST['resource_name'] ) ? $_POST['resource_name'] : '';
		$list_a_data = isset( $_POST['list_a_data'] ) ? json_decode( str_replace( '\\', '', $_POST['list_a_data'] ) ) : '';

		$list_b_data = isset( $_POST['list_b_data'] ) ? json_decode( str_replace( '\\', '', $_POST['list_b_data'] ) ) : '';

		$bank_detail_array = [
			'title'						=> $title,
			'forename'					=> $forename,
			'surname'					=> $surname,
			'ni_number'					=> $ni_number,
			'dob'						=> $dob,
			'home_address'				=> $home_address,
			'home_email'				=> $home_email,
			'mobile_number'				=> $mobile_number,
			'subjects'					=> $subjects,
			'bank_name'					=> $bank_name,	
			'account_holder_name'		=> $account_holder_name,
			'sort_code'					=> $sort_code,
			'bank_address'				=> $bank_address,
			'account_number'			=> $account_number,
			'certificate_number'		=> $certificate_number,
			'username_on_certificate'	=> $username_on_certificate,
			'current_yn'				=> $current_yn,
			'dob_on_certificate'		=> $dob_on_certificate,
			'internal_use'				=> $internal_use,
			'signature_name'			=> $signature_name,
			'signature_date'			=> $signature_date,
			'resource_name'				=> $resource_name,
			'resource_date'				=> $resource_date,
			'list_a_data'				=> serialize( $list_a_data ),
			'list_b_data'				=> serialize( $list_b_data )
		];

		$bank_detail_table = $wpdb->base_prefix . 'mld_bank_details';
		$data_to_insert = array(
			'user_id' 		=> self::$instance->userid,
			'bank_detail' 	=> serialize( $bank_detail_array ),
			'dates'			=> date("Y/m/d")
		);

		$wpdb->insert( $bank_detail_table, $data_to_insert );

		$headers = array('Content-Type: text/html; charset=UTF-8');
		$header_url = MLD_ASSETS_URL.'images/header.PNG';
		$footer_url = MLD_ASSETS_URL.'images/footer.PNG';
		$bank_detail_content .= '<img src="'.$header_url.'" style="width: 99%;">';
		$bank_detail_content .= '<p></p>';

		$bank_detail_content .= '<h3>Personal Details</h3>';
		$bank_detail_content .= '<table border="2" style="width: 100%;">';
		$bank_detail_content .= '<tr>';
		$bank_detail_content .= '<td>Title</td>';
		$bank_detail_content .= '<td>'.$title.'</td>';
		$bank_detail_content .= '<td>Forename</td>';
		$bank_detail_content .= '<td>'.$forename.'</td>';
		$bank_detail_content .= '<td>Surname</td>';
		$bank_detail_content .= '<td>'.$surname.'</td>';
		$bank_detail_content .= '</tr>';
		$bank_detail_content .= '<tr>';
		$bank_detail_content .= '<td>NI Number</td>';
		$bank_detail_content .= '<td colspan="2">'.$ni_number.'</td>';
		$bank_detail_content .= '<td>Date of Birth</td>';
		$bank_detail_content .= '<td colspan="2">'.$dob.'</td>';
		$bank_detail_content .= '</tr>';
		$bank_detail_content .= '<td>Home Address</td>';
		$bank_detail_content .= '<td colspan="5">'.$home_address.'</td>';
		$bank_detail_content .= '<tr>';
		$bank_detail_content .= '<td>Home Email</td>';
		$bank_detail_content .= '<td colspan="2">'.$home_email.'</td>';
		$bank_detail_content .= '<td>Mobile NUmber</td>';
		$bank_detail_content .= '<td colspan="2">'.$mobile_number.'</td>';
		$bank_detail_content .= '</tr>';
		$bank_detail_content .= '<tr>';
		$bank_detail_content .= '<td>Dept./Subject(s)</td>';
		$bank_detail_content .= '<td colspan="5">'.$subjects.'</td>';
		$bank_detail_content .= '</tr>';
		$bank_detail_content .= '</table>';
		$bank_detail_content .= '<h3>Bank Details</h3>';
		$bank_detail_content .= '<div>Please provide details of the bank account you wish your salary to be paid into.</div>';
		$bank_detail_content .= '<table border="2" style="width: 100%;">';
		$bank_detail_content .= '<tr>';
		$bank_detail_content .= '<td>Bank Name</td>';
		$bank_detail_content .= '<td>'.$bank_name.'</td>';
		$bank_detail_content .= '<td>Account Holders Name</td>';
		$bank_detail_content .= '<td>'.$account_holder_name.'</td>';
		$bank_detail_content .= '</tr>';
		$bank_detail_content .= '<tr>';
		$bank_detail_content .= '<td>Sort Code</td>';
		$bank_detail_content .= '<td>'.$sort_code.'</td>';
		$bank_detail_content .= '<td>Account Number</td>';
		$bank_detail_content .= '<td>'.$account_number.'</td>';
		$bank_detail_content .= '</tr>';
		$bank_detail_content .= '<tr>';
		$bank_detail_content .= '<td>Bank Address</td>';
		$bank_detail_content .= '<td colspan="3">'.$bank_address.'</td>';
		$bank_detail_content .= '</tr>';
		$bank_detail_content .= '</table>';
		$bank_detail_content .= '<h3>Disclosure & Barring Service</h3>';
		$bank_detail_content .= '<div>Please provide the following information if you consent to Myrtle Learning performing a status check</div>';
		
		$bank_detail_content .= '<table border="2" style="width: 100%;">';
		$bank_detail_content .= '<tr>';
		$bank_detail_content .= '<td>Certificate Number</td>';
		$bank_detail_content .= '<td>'.$certificate_number.'</td>';
		$bank_detail_content .= '<td>Applicant Surname on Certificate</td>';
		$bank_detail_content .= '<td>'.$username_on_certificate.'</td>';
		$bank_detail_content .= '</tr>';
		$bank_detail_content .= '<tr>';
		$bank_detail_content .= '<td>Current (Y/N)</td>';
		$bank_detail_content .= '<td>'.$current_yn.'</td>';
		$bank_detail_content .= '<td>Date of Birth on Certificate</td>';
		$bank_detail_content .= '<td>'.$dob_on_certificate.'</td>';
		$bank_detail_content .= '</tr>';
		$bank_detail_content .= '<tr>';
		$bank_detail_content .= '<td>Internal Use</td>';
		$bank_detail_content .= '<td colspan="3">'.$internal_use.'</td>';
		$bank_detail_content .= '</tr>';
		$bank_detail_content .= '</table>';

		$bank_detail_content .= '<h3>Right to Work</h3>';
		$bank_detail_content .=	'<ul>';
		$bank_detail_content .=	'<li><b>You must be provide one of the documents or combinations of documents in List Aor List B below as proof that someone is allowed to work in the UK.</b></li>';
		$bank_detail_content .=	'<li><b>You must only accept originals documents.</b></li>';
		$bank_detail_content .=	'</ul>';

		$bank_detail_content .=	'<h3>List A</h3>';

		if( ! empty( $list_a_data ) && is_array( $list_a_data ) ) {
			
			$bank_detail_content .= '<ul>';
			foreach( $list_a_data as $index => $data ) {

				if( 0 == $index ) {
					$checkbox_content = 'A passport showing the holder, or a person named in the passport as the child of the holder, is a British citizen or a citizen of the UK and colonies having the right of abode in the UK';
				}

				if( 1 == $index ) {
					$checkbox_content = 'A passport or national identity card showing that the holder, or a person named in the passport as the child of the holder, is a national of a European Economic Area country or Switzerland';
				}

				if( 2 == $index ) {
					$checkbox_content = 'A residence permit,registration certificate or document certifying or indicating permanent residence issued by the Home Office, the Border and Immigration Agency, or the UK Border Agency to a national of a European Economic Area country or Switzerland';
				}

				if( 3 == $index ) {
					$checkbox_content = 'A permanent residence card or document issued by the Home Office, the Border and Immigration Agency, or the UK Border Agency to the family member of a national of a European Economic Area country or Switzerland';
				}

				if( 4 == $index ) {
					$checkbox_content = 'A Biometric Residence Permit issued by the UK Border Agency to the holder which indicates that the person named in it is allowed to stay indefinitely in the UK, or has no time limit on their stay in the UK';
				}

				if( 5 == $index ) {
					$checkbox_content = 'A passport or other travel document endorsed to show that the holder is exempt from immigration control, is allowed to stay indefinitely in the UK, has the right of abode in the UK, or has no time limit on their stay in the UK';
				}

				if( 6 == $index ) {
					$checkbox_content = 'An Immigration Status Document issued by the Home Office, the Border and Immigration Agency, or the UK Border Agency to the holder with an endorsement indicating that the person named in it is allowed to stay indefinitely in the UK or has no time limit on their stay in the UK together with an official document issued by a previous employer or Government agency with the person’s name and National Insurance number (a P45, P46, National Insurance card, or letter from a Government agency)';
				}

				if( 7 == $index ) {
					$checkbox_content = 'A full birth or adoption certificate issued in the UK which includes the name(s) of at least one of the holder’s parents together with an official document issued by a previous employer or Government agency with the person’s name and National Insurance number (a P45, P46, National Insurance card, or letter from a Government agency)';
				}

				if( 8 == $index ) {
					$checkbox_content = 'A birth or adoption certificate issued in the Channel Islands, the Isle of Man or Ireland together with an official document issued by a previous employer or Government agency with the person’s name and National Insurance number (a P45, P46, National Insurance card, or letter from a Government agency)';
				}

				if( 9 == $index ) {
					$checkbox_content = 'A certificate of registration or naturalization as a British citizen together with an official document issued by a previous employer or Government agency with the person’s name and National Insurance number (a P45, P46, National Insurance card, or letter from a Government agency)';
				}

				if( 10 == $index ) {
					$checkbox_content = 'A letter issued by the Home Office, the Border and Immigration Agency, or the UK Border Agency to the holder which indicates that the person named in it is allowed to stay indefinitely in the UK together with an official document issued by a previous employer or Government agency with the person’s name and National Insurance number (a P45, P46, National Insurance card, or letter from a Government agency)';
				}

				$check = '';
				if( 'yes' == $data ) {
					$check = 'checked';
				}

				$bank_detail_content .= '<li>';
				$bank_detail_content .= '<input type="checkbox" '.$check.'>';
				$bank_detail_content .= '<span>'.$checkbox_content.'</span>';
				$bank_detail_content .= '</li>';	
			}
			$bank_detail_content .= '</ul>';
		}

		$bank_detail_content .=	'<h3>List B</h3>';

		if( ! empty( $list_b_data ) && is_array( $list_b_data ) ) {
			
			$bank_detail_content .= '<ul>';
			foreach( $list_a_data as $index => $data ) {

				if( 0 == $index ) {
					$checkbox_content = 'A passport or other travel document endorsed to show that the holder is allowed to stay in the UK and is allowed to do the type of work you are offering';
				}

				if( 1 == $index ) {
					$checkbox_content = 'A Biometric Residence Permit issued by the UK Border Agency to the holder which indicates that the person named in it can stay in the UK and is allowed to do the type of work you are offering';
				}

				if( 2 == $index ) {
					$checkbox_content = 'A residence cardor document issued by the Home Office, the Border and Immigration Agency, or the UK Border Agency to a family member of a national of a European Economic Area country or Switzerland';
				}

				if( 3 == $index ) {
					$checkbox_content = 'A work permit or othe rapproval or other approval to take employment issued by the Home Office, the Border and Immigration Agency or the UK Border Agency together with either a passport or travel document endorsed to show the holder is allowed to stay in the UK and is allowed to do the work you are offering or a letter issued by the Home Office, the Border and Immigration Agency or the UK Border Agency to the holder or to you confirming the same';
				}

				if( 4 == $index ) {
					$checkbox_content = 'A Certificate of Application which is less than 6 months old issued by the Home Office, the Border and Immigration Agency or the UK Border Agency to or for the family member of a national of a European Economic Area country or Switzerland stating the holder is allowed to take employment together with a positive verification letter from the UK Border Agency’s Employer Checking Service';
				}

				if( 5 == $index ) {
					$checkbox_content = 'An Application Registration Card (ARC) issued by the Home Office, the Border and Immigration Agency stating that the holder is ‘ALLOWED TO WORK’ or ‘EMPLOYMENT PERMITTED’ together with a positive verification letter from the UK Border Agency’s Employer Checking Service';
				}

				if( 6 == $index ) {
					$checkbox_content = 'An Immigration Status Document issued by the Home Office, the Border and Immigration Agency or the UK Border Agency to the holder with an endorsement indicating that the person named on it can stay in the UK and is allowed to do the type of work you are offering together with an official document issued by a previous employer or Government agency with the person’s name and National Insurance number (a P45, P46, National Insurance card, or letter from a Government agency)';
				}

				if( 7 == $index ) {
					$checkbox_content = 'A letter is sued by the Home Office, the Border and Immigration Agency or the UK Border Agency to the holder or to you as the potential employer or employer, which indicates that the person named in it can stay in the UK and is allowed to do the type of work you are offering together with an official document issued by a previous employer or Government agency with the person’s name and National Insurance number (a P45, P46, National Insurance card, or letter from a Government agency)';
				}

				$check = '';
				if( 'yes' == $data ) {
					$check = 'checked';
				}

				$bank_detail_content .= '<li>';
				$bank_detail_content .= '<input type="checkbox" '.$check.'>';
				$bank_detail_content .= '<span>'.$checkbox_content.'</span>';
				$bank_detail_content .= '</li>';	
			}
			$bank_detail_content .= '</ul>';
		}

		$sign_upload_dir = $upload_dir['basedir'].'/mld_bank_detail_upload/user_'.$user_id.'/signature-initials-emailed-copies';
		$resource_upload_dir = $upload_dir['basedir'].'/mld_bank_detail_upload/user_'.$user_id.'/signature';

		$sign_file = glob( $sign_upload_dir . '/*' );
		$resource_file = glob( $resource_upload_dir . '/*' );

		$sign_file = isset( $sign_file[0] ) ? $sign_file[0] : '';
		$resource_file = isset( $resource_file[0] ) ? $resource_file[0] : '';

		$basePath = "/home/runcloud/webapps/myrtlelearning/";
		$sign_cleanedPath = str_replace( $basePath, '', $sign_file );
		$sign_url = site_url().'/'.$sign_cleanedPath;
		$resource_cleanedPath = str_replace( $basePath, '', $resource_file );
		$resource_url = site_url().'/'.$resource_cleanedPath;

		$bank_detail_content .= '<h3>Signature</h3>';
		$bank_detail_content .= '<table border="2" style="width: 100%;">';
		$bank_detail_content .= '<tr>';
		$bank_detail_content .= '<th>Name</th>';
		$bank_detail_content .= '<th>Signature or Initials for emailed copies</th>';
		$bank_detail_content .= '<th>Date</th>'; 	
		$bank_detail_content .= '</tr>';
		$bank_detail_content .= '<tr>';
		$bank_detail_content .= '<td>'.$signature_name.'</td>';
		$bank_detail_content .= '<td><img src="'.$sign_url.'" style="height: 60px;"></td>';
		$bank_detail_content .= '<td>'.$signature_date.'</td>';
		$bank_detail_content .= '</tr>';
		$bank_detail_content .= '</table>';

		$bank_detail_content .= '<h3>Human Resources Verification</h3>';
		$bank_detail_content .= '<table border="2" style="width: 100%;">';
		$bank_detail_content .= '<tr>';
		$bank_detail_content .= '<th>Name</th>';
		$bank_detail_content .= '<th>Signature</th>';
		$bank_detail_content .= '<th>Date</th>'; 	
		$bank_detail_content .= '</tr>';
		$bank_detail_content .= '<tr>';
		$bank_detail_content .= '<td>'.$resource_name.'</td>';
		$bank_detail_content .= '<td><img src="'.$resource_url.'" style="width: 60px;"></td>';
		$bank_detail_content .= '<td>'.$resource_date.'</td>';
		$bank_detail_content .= '</tr>';
		$bank_detail_content .= '</table>';

		$bank_detail_content .= '<p></p>';
		$bank_detail_content .= '<img src="'.$footer_url.'" style="width: 99%;">';
		$admin_email = get_option('admin_email');
		wp_mail( 'hello@myrtlelearning.com', 'Bank Detail', $bank_detail_content, $headers );

		wp_die();
	}

	/**
	 * upload teacher form
	 */
	public function mld_upload_user_form() {

		global $wpdb;
		
		$files = $_FILES;
		$user_id = isset( $_POST['mld-user-id'] ) ? $_POST['mld-user-id'] : '';
		$teacher_form_titles = isset( $_POST['teacher_form_title'] ) ? str_replace( '\\', '', $_POST['teacher_form_title'] ) : [];
		$teacher_form_info = json_decode( $teacher_form_titles );
		$refrence_data = isset( $_POST['refrence-form-data'] ) ? str_replace( '\\', '', $_POST['refrence-form-data'] ) : [];
		$refrence_data = json_decode( $refrence_data ); 
		update_user_meta( $user_id, 'mld-user-refrence-data', $refrence_data );
		if( ! empty( $refrence_data ) && is_array( $refrence_data ) ) {
			
			$headers = array('Content-Type: text/html; charset=UTF-8');
			$header_url = MLD_ASSETS_URL.'images/header.PNG';
			$footer_url = MLD_ASSETS_URL.'images/footer.PNG';
			$form_url = site_url().'/refrence-form/';
			$urlWithUserId = $form_url . "?userId=" . $user_id;
			
			foreach( $refrence_data as $ref_data ) {
				
				$refree_email = isset( $ref_data[3]->email_address_of_referee ) ? $ref_data[3]->email_address_of_referee : '';
				$urlWithTwoParams = $urlWithUserId . "&ref_email=" . $refree_email;
				$candidate_name = isset( $ref_data[0]->name_of_applicant ) ? $ref_data[0]->name_of_applicant: '';
				if( $refree_email ) {

					$refree_html = '';
					
					$refree_html .= '<img src="'.$header_url.'" style="width: 99%;">';
					$refree_html .= '<p></p>';
						
					$refree_html .= '<div style="font-size: 15px; color: #365249; margin: 15px 0 15px 0;">'.__( 'Good morning,', 'myrtle-learning-dashboard' ).'</div>';
					$refree_html .= '<div style="font-size: 15px; color: #365249; margin: 15px 0 15px 0; font-weight: 600;">'.__( 'Candidate Name: '.$candidate_name, 'myrtle-learning-dashboard' ).'</div>';
					
					$refree_html .= '<div style="font-size: 15px; color: #365249; margin: 15px 0 15px 0;">'.__( 'The above named person has applied for a job as a teacher and has given your name for reference purposes.', 'myrtle-learning-dashboard' ).'</div>';
					$refree_html .= '<div style="font-size: 15px; color: #365249; margin: 15px 0 15px 0;">'.__( 'As part of the process of verifying the suitability of candidates to work with children, the school are required to obtain a reference to confirm if the applicant is suitable for the post. I would be really grateful if you would complete the attached form to indicate if they can fulfill the requirements of this post.', 'myrtle-learning-dashboard' ).'</div>';
					$refree_html .= '<div style="font-size: 15px; color: #365249; margin: 15px 0 15px 0;">'.__( 'Please complete as many sections as possible, some of the information requested may not be relevant to your organisation however it enables us to make a thorough assessment.', 'myrtle-learning-dashboard' ).'</div>';
					$refree_html .= '<div style="font-size: 15px; color: #365249; margin: 15px 0 15px 0;">'.__( ' Any reference provided will be placed on their personal file and may be viewed by the individual. The reference should be accurate and should not contain any information which is misstatement or omission.', 'myrtle-learning-dashboard' ).'</div>';
					$refree_html .= '<div style="font-size: 15px; color: #365249; margin: 15px 0 15px 0;">'.__( 'Please click on the button below to access the reference form and complete it as required.', 'myrtle-learning-dashboard' ).'</div>';
					$refree_html.= '<a href="'.$urlWithTwoParams.'" style="background-color: #365249; padding: 10px; color: white; cursor: pointer;">Reference Form</a>';
					$refree_html .= '<div style="font-size: 15px; color: #365249; margin: 15px 0 15px 0;">'.__( 'If you have any queries please do not hesitate to contact us.', 'myrtle-learning-dashboard' ).'</div>';
					$refree_html .= '<div style="font-size: 15px; color: #365249; margin: 15px 0 15px 0;">'.__( 'We would appreciate it if you are able to return a completed form within a week.', 'myrtle-learning-dashboard' ).'</div>';
					$refree_html .= '<div style="font-size: 15px; color: #365249; margin: 15px 0 15px 0;">'.__( 'May I thank you in anticipation for your assistance in this matter.', 'myrtle-learning-dashboard' ).'</div>';
					$refree_html .= '<div style="font-size: 15px; color: #365249; margin: 15px 0 15px 0;">'.__( 'Kind Regards', 'myrtle-learning-dashboard' ).'</div>';
					$refree_html .= '<div style="font-size: 15px; color: #365249; margin: 15px 0 15px 0; font-weight: 600;">'.__( 'The Myrtle Learning Recruitment Team', 'myrtle-learning-dashboard' ).'</div>';
					$refree_html .= '<p></p>';
					$refree_html .= '<img src="'.$footer_url.'" style="width: 99%;">';
					wp_mail( $refree_email, 'Reference request email - Staff Registration', $refree_html, $headers );
				}
			}	
		}
		update_user_meta( $user_id, 'mld-user-forms', $teacher_form_info );
		if( ! empty( $teacher_form_info ) && is_array( $teacher_form_info ) ) {

			$uploads_dir = wp_upload_dir();

			foreach( $teacher_form_info as $title ) {

				$file_key = str_replace( ' ', '_', $title );
				$teacher_form_dir = $uploads_dir['basedir'].'/mld-user-form-uploads/'.$user_id.'/'.$file_key;

				if ( ! is_dir( $teacher_form_dir ) ) {
					wp_mkdir_p( $teacher_form_dir );
				}

				$uploaded_file = isset( $files[$file_key]['tmp_name'] ) ? $files[$file_key]['tmp_name'] : '';
				$file_name = isset( $files[$file_key]['name'] ) ? $files[$file_key]['name'] : '';

				move_uploaded_file( $uploaded_file, $teacher_form_dir.'/'.$file_name );
			}

			$headers = array('Content-Type: text/html; charset=UTF-8');

			$email = $wpdb->get_var( $wpdb->prepare( "SELECT user_email FROM $wpdb->users WHERE ID = %d", $user_id ) );
			$user = get_user_by( 'email', $email );
			$username = ucwords( $user->user_login );

			$header_url = MLD_ASSETS_URL.'images/header.PNG';
			$footer_url = MLD_ASSETS_URL.'images/footer.PNG';
			$upload_html .= '<img src="'.$header_url.'" style="width: 99%;">';
			$upload_html .= '<p></p>';
			$upload_html .= '<div style="font-size: 20px; color: #18440a;">'.__( 'Staff Registration', 'myrtle-learning-dashboard' ).'</div>';
			$upload_html .= '<div style="font-size: 20px; color: #18440a; font-weight: 600;">'.__( 'Document Upload Email', 'myrtle-learning-dashboard' ).'</div>';
			$upload_html .= '<div style="font-size: 20px; color: #18440a; margin: 15px 0 15px 0;">'.__( 'Update!!!', 'myrtle-learning-dashboard' ).'</div>';
			$upload_html .= '<div style="font-size: 15px; color: #18440a; margin: 15px 0 15px 0;">'.__( 'All documents for the candidate below have been uploaded.', 'myrtle-learning-dashboard' ).'</div>';
			$upload_html .= '<div style="font-size: 15px; color: #18440a; margin: 15px 0 15px 0;">'.__( 'Candidate Name: '.$username.' ', 'myrtle-learning-dashboard' ).'</div>';
			$upload_html .= '<div style="font-size: 15px; color: #18440a; margin: 15px 0 15px 0; text-decoration: underline;">'.__( 'Documents uploaded:', 'myrtle-learning-dashboard' ).'</div>';

			$upload_html .= '<table style="width: 100%;">';
			if( ! empty( $teacher_form_info ) && is_array( $teacher_form_info ) ) {

				foreach( $teacher_form_info as $teacher_form_title ) {

					$title_with_dash = str_replace( ' ', '_', $teacher_form_title );
					$uploaded_file = mld_get_category_files( 'mld-user-form-uploads/'.$user_id.'/'.$file_key );

					if( ! empty( $uploaded_file ) && is_array( $uploaded_file ) ) {

						$upload_html .= '<tr><td width="40%" style="background-color:#18440a; color: white; padding: 10px; text-align: center;">'.$teacher_form_title.'</td><td width="30%" style="background: #e4e4e4; font-weight: 600; color: #18440a; padding: 10px; text-align: center;">'.__( '1 Upload', 'myrtle-learning-dashboard' ).'</td><td width="30%" style="background-color:#18440a; color: white; padding: 10px; text-align: center;">'.__( 'Complete', 'myrtle-learning-dashboard' ).'</td></tr>';
					}	
				}
			}

			$upload_html .= '</table>';

			$get_updated_form = get_user_meta( $user_id, 'mld-user-forms', true );
			$form_titles = get_option( 'mld_teacher_form_titles' );
			$form_titles = array_column( $form_titles, 'title' );
			$title_difference = array_diff( $form_titles, $get_updated_form );

			if( ! empty( $title_difference ) && is_array( $title_difference ) ) {
				$upload_html .= '<div style="font-size: 15px; color: #18440a; margin: 15px 0 15px 0; text-decoration: underline;">'.__( 'Documents required:', 'myrtle-learning-dashboard' ).'</div>';
				$upload_html .= '<table style="width: 100%;">';
				foreach( $title_difference as $diff_title ) {
					$upload_html .='<tr><td width="40%" style="background-color:#18440a; color: white; padding: 10px; text-align: center;">'.$diff_title.'</td><td width="30%" style="background-color:#e4e4e4; color: #18440a; padding: 10px; text-align: center; font-weight: 600;">0 upload</td><td width="30%" style="background-color:#FFB206; color: white; padding: 10px; text-align: center;">'.__( 'Pending', 'myrtle-learning-dashboard' ).'</td></tr>';	
				}
				$upload_html .= '</table>';
			}

			$upload_html .= '<p></p>';
			$upload_html .= '<a href="#" style="font-weight: 600; color: #18440a; margin: 15px 0 15px 0;">'.__( 'Check link', 'myrtle-learning-dashboard' ).'</a>';
			
			$user_refrences = get_user_meta( $user_id, 'mld-user-refrence-data' , true );

			if( ! empty( $user_refrences ) && is_array( $user_refrences ) ) {
				foreach( $user_refrences as $refrence ) {	
					
					$n_of_applicant = isset( $refrence[0]->name_of_applicant ) ? $refrence[0]->name_of_applicant : '';
					$applied_for = isset( $refrence[1]->position_applied_for ) ? $refrence[1]->position_applied_for : '';
					$n_of_refree = isset( $refrence[2]->name_of_referee ) ? $refrence[2]->name_of_referee : '';
					$e_of_refree = isset( $refrence[3]->email_address_of_referee ) ? $refrence[3]->email_address_of_referee : '';
					$p_of_applicant = isset( $refrence[4]->phone_number_of_referee ) ? $refrence[4]->phone_number_of_referee : '';
					$org = isset( $refrence[5]->name_of_organisation ) ? $refrence[5]->name_of_organisation : '';			
					$upload_html .= '<div style="font-size: 15px; color: #18440a; margin: 15px 0 15px 0;">'.__( 'Reference Form(s)', 'myrtle-learning-dashboard' ).'</div>';
					$upload_html .= '<div style="font-size: 15px; color: #18440a; padding: 15px; background: #18440a; color: white;">'.__( 'Referee', 'myrtle-learning-dashboard' ).'</div>';
					$upload_html .= '<table border="1px" style="width: 100%;">';
					$upload_html .= '<tr><td width="40%" style="padding: 10px; background-color: #18440a; color: white;">'.__( 'Name of Applicant:', 'myrtle-learning-dashboard' ).'</td><td width="60%" style="padding: 10px;">'.$n_of_applicant.'</td></tr>';
					$upload_html .= '<tr><td width="40%" style="padding: 10px; background-color: #18440a; color: white;">'.__( 'Position Applied for:', 'myrtle-learning-dashboard' ).'</td><td width="60%" style="padding: 10px;">'.$applied_for.'</td></tr>';
					$upload_html .= '<tr><td width="40%" style="padding: 10px; background-color: #18440a; color: white;">'.__( 'Name of Referee:', 'myrtle-learning-dashboard' ).'</td><td width="60%" style="padding: 10px;">'.$n_of_refree.'</td></tr>';
					$upload_html .= '<tr><td width="40%" style="padding: 10px; background-color: #18440a; color: white;">'.__( 'Email Address of Referee:', 'myrtle-learning-dashboard' ).'</td><td width="60%" style="padding: 10px;">'.$e_of_refree.'</td></tr>';
					$upload_html .= '<tr><td width="40%" style="padding: 10px; background-color: #18440a; color: white;">'.__( 'Phone Number of Referee:', 'myrtle-learning-dashboard' ).'</td><td width="60%" style="padding: 10px;">'.$p_of_applicant.'</td></tr>';
					$upload_html .= '<tr><td width="40%" style="padding: 10px; background-color: #18440a; color: white;">'.__( 'Name of Organisation:', 'myrtle-learning-dashboard' ).'</td><td width="60%" style="padding: 10px;">'.$org.'</td></tr>';
					$upload_html .= '</table>';
				}
			}
			$upload_html .= '<div style="font-size: 15px; color: #18440a; margin: 15px 0 15px 0;">'.__( 'Thank You', 'myrtle-learning-dashboard' ).'</div>';
			$upload_html .= '<div style="font-size: 20px; color: #18440a; margin: 15px 0 15px 0; font-weight: 600;">'.__( 'The Myrtle Learning <br> Recruitment Team', 'myrtle-learning-dashboard' ).'</div>';
			$upload_html .= '<p></p>';
			$upload_html .= '<img src="'.$footer_url.'" style="width: 99%;">';
			
			$admin_email = get_option('admin_email');
			wp_mail( 'hello@myrtlelearning.com', 'Staff Registration', $upload_html, $headers );
		}
		$user = new WP_User( $user_id );
		$user->set_role('pending_teacher');
		wp_die();
	}

	/**
	 * update checklist data
	 */
	public function mld_update_checklist_data() {

		$response = [];
		$response['status'] = 'false';
		$content = '';
		$form_titles = get_option( 'mld_teacher_form_titles' );
		
		$form_titles = array_filter( $form_titles );

		if( ! empty( $form_titles ) && is_array( $form_titles ) ) {

			$no = 0;
			ob_start();
			?>
			<div class="mld-forms-wrapper">
			<?php
			$no = 0;
			$form_no = 0;
			foreach( $form_titles as $data ) {

				$title = isset( $data->title ) ? $data->title : '';
				$index = isset( $data->index ) ? $data->index : '';
				$form_title = $title;
				$title_with_dash = str_replace( ' ', '-', $form_title );
				$file = mld_get_category_files( 'mld-teachers-form-uploads/'.$title_with_dash );
				$file = isset( $file[0] ) ? $file[0] : '';
				$basePath = "/home/runcloud/webapps/myrtlelearning/";
				$cleanedPath = str_replace( $basePath, '', $file );
				$cleanedPath = site_url().'/'.$cleanedPath;

				if( 0 == $no ) {
					?>
					<div class="mld-statutory-title">
						<?php echo __( 'Statutory Document Upload', 'myrtle-learning-dashboard' ); ?>
					</div>
					<?php
				}

				if( 'documents' == $index ) {
					?>
						
					<div class="mld-document-inner mld-teacher-inner-wrapper">
						<input type="hidden" value="<?php echo $title; ?>" class="mld-form-title">
						<div class="mld-title"><?php echo $title; ?></div>
						<div class="mld-file">
							<input type="file" class="mld-form-file mld-teacher-uploads">
						</div>
						<div class="mld-clear-both"></div>
					</div>
					<?php
				} 

				if( 'form' == $index ) {

					$site_url = site_url();

					if( 0 == $form_no ) {
						?>
						<div class="mld-form-title">
							<?php echo __( 'Forms to be signed', 'myrtle-learning-dashboard' ); ?>
						</div>
						<?php
					}

					?>
					<div class="mld-form-inner mld-teacher-inner-wrapper">
						<input type="hidden" value="<?php echo $title; ?>" class="mld-form-title">
						<div class="mld-title"><a href="<?php echo $site_url; ?>/employee-and-bank-detail/" target="_blank"><?php echo $title; ?></a></div>
						<div class="mld-file">
							<input type="file" class="mld-form-file mld-teacher-uploads">
						</div>
						<div class="mld-clear-both"></div>
					</div>
					<?php
					$form_no++;
				}
				$no++;
			}
			echo do_shortcode( '[refrence_form]' );
			?>
				<div class="mld-checklist-error"><?php echo __( 'The referee should be at least 2', 'myrtle-learning-dashboard' ); ?></div>
				<div class="mld-update-checklist6">
					<input type="button" class="mld-user-upload-btn" value="<?php echo __( 'UPDATE', 'myrtle-learning-dashboard' ); ?>" data-user_id="<?php echo self::$instance->userid; ?>">
				</div>
			</div>
			<?php
			$content = ob_get_clean();
			$response['status'] = 'true';
		}	

		$response['content'] = $content;
		echo json_encode( $response );

		wp_die();
	}

	/*
	 * Delete a polivy type
	 */
	public function mld_delete_policy_type() {

		$policy_type = isset( $_POST['policy_type'] ) ? $_POST['policy_type'] : '';
		$response = [];

		if( ! $policy_type ) {

			$response['message'] = __( 'Policy type not found', 'myrtle-learning-dashboard' );
			$response['status'] = 'false';

			echo json_encode( $response );
			wp_die();
		}

		$upload_dir = wp_upload_dir();
		$folder_directory = $upload_dir['basedir'].'/'.$policy_type;
		rmdir( $folder_directory );
		wp_die();
	}

	/**
	 * genarate pdf comment html
	 */
	public function mld_pdf_comment_html() {

		$response = [];

		$user_id = isset( $_POST['user_id'] ) ? $_POST['user_id'] : 0;
		$key = isset( $_POST['key'] ) ? $_POST['key'] : '';
		$capable = isset( $_POST['capable'] ) ? $_POST['capable'] : 'yes';
		$display = '';
		if( 'no' == $capable ) {
			$display = 'none';
		}
		$user_comments = get_user_meta( $user_id, $user_id.'_'.$key, true );
		
		if( ! $user_id ) {

			$response['message'] = __( 'User id not found', 'myrtle-learning-dashboard' );
			$response['status'] = 'false';

			echo json_encode( $response );
			wp_die();
		}

		if( ! $key ) {

			$response['message'] = __( 'Key not found', 'myrtle-learning-dashboard' );
			$response['status'] = 'false';

			echo json_encode( $response );
			wp_die();
		}

		ob_start();
		?>
		<div class="mld-pdf-comment-wrapper">
			<div class="mld-comment-section">
				<?php
				if( ! empty( $user_comments ) && is_array( $user_comments ) ) {
					foreach( $user_comments as $comment ) {
						?>
						<div class="mld-teachers-comment">
							<?php echo $comment; ?>
						</div>
						<?php
					}
				}
				?>		
			</div>
			<div class="mld-comment-footer-section" style="display: <?php echo $display; ?>;">
				<div class="mld-comment-input-field">
					<input type="text" class="mld-comment-text" placeholder="<?php echo __( 'Please Enter PDF Comment', 'myrtle-learning-dashboard' ); ?>">
				</div>
				<div class="mld-pdf-update-btn">
					<input type="button" value="<?php echo __( 'Update', 'myrtle-learning-dashboard' ); ?>" class="mld-pdf-comment-btn" data-user_id="<?php echo $user_id; ?>" data-key="<?php echo $key; ?>">
				</div>
			</div>
		</div>
		<?php

		$content = ob_get_contents();
		ob_get_clean();

		$response['status'] = 'true';
		$response['content'] = $content;
		echo json_encode( $response );
		wp_die();
	}

	/**
	 * Terms and Conditions
	 */
	public function mld_terms_and_conditions() {

		$response = [];

		ob_start();
		?>
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
		<?php

		$content = ob_get_contents();
		ob_get_clean();

		$response['status'] = 'true';
		$response['content'] = $content;
		echo json_encode( $response );
		wp_die();

	}

	/**
	 * get user content 
	 */
	public function mld_get_user_account_content() {

		$response = [];

		$user_id = isset( $_POST['user_id'] ) ? intval( $_POST['user_id'] ) : 0;

		if( ! $user_id ) {

			$response['message'] = __( 'User id not found', 'myrtle-learning-dashboard' );
			$response['status'] = 'false';

			echo json_encode( $response );
			wp_die();
		}

		$content = $this->mld_get_user_account_html( $user_id );

		$response['status'] = 'true';
		$response['content'] = $content;
		echo json_encode( $response );
		wp_die();
	}

	/**
	 * create a function to get user acoount html
	 *
	 *@param $user_id
	 */
	public function mld_get_user_account_html( $user_id ) {

		if( ! self::$instance->userlogged ) {
			return;
		}

		global $wpdb;

		$user_data = get_userdata( $user_id );
		$user_roles = isset( $user_data->roles ) ? $user_data->roles : '';
		$user_start_date = isset( $user_data->data->user_registered ) ? $user_data->data->user_registered : '';
		$timestamp = strtotime( $user_start_date );
		$dateWithoutTime = date( "Y-m-d", $timestamp );
		$upload_dir = wp_get_upload_dir();
		$directory_path = $upload_dir['basedir'] . '/mld_uploaded_files/user_files_'.$user_id;
		$files = glob($directory_path . '/*');
		$attachment_dir_path = $upload_dir['basedir'] . '/mld-teachers-data/teacher_'.$user_id;
		$attachment_files = glob( $attachment_dir_path . '/*' );
		$files = array_merge( $files, $attachment_files );
		ob_start();

		if( learndash_is_group_leader_user( $user_id ) == true || user_can( $user_id, 'manage_options' ) ) {

			$get_basic_info = get_user_meta( $user_id, 'mld-teacher-basic-info', true );
			$mld_dob = isset( $get_basic_info['dob'] ) ? $get_basic_info['dob'] : '';

			if( ! $mld_dob ) {
				$mld_dob = mld_get_user_data( 'field_5804a34', $user_id );
			}
			
			$mld_h_address = isset( $get_basic_info['address'] ) ? $get_basic_info['address'] : '';

			if( ! $mld_h_address ) {
				$mld_h_address = stripslashes( mld_get_user_data( 'address', $user_id ) );	
			} 

			$mld_contact_1 = isset( $get_basic_info['hometel'] ) ? $get_basic_info['hometel'] : '';

			if( ! $mld_contact_1 ) {
				$mld_contact_1 = mld_get_user_data( 'tel', $user_id );
			}

			$mld_contact_2 = isset( $get_basic_info['mobile_number'] ) ? $get_basic_info['mobile_number'] : '';

			if( ! $mld_contact_2 ) {
				$mld_contact_2 = mld_get_user_data( 'mobile_number', $user_id );
			}
		
			$mld_mail = isset( $user_data->data->user_email ) ? $user_data->data->user_email : '';
			$mld_surname = mld_get_username( $user_id );
			$user_pass = '********';
			
			require_once MLD_TEMPLATES_DIR.'account-admin-template.php';
		
		} else {

			$mld_phone = mld_get_user_data( 'field_c1f0933', $user_id );
			$second_phone = mld_get_user_data( 'field_935a7ff', $user_id );
			$user_dob = mld_get_user_data( 'field_1b47487', $user_id );
			$user_address = mld_get_user_data( 'field_54e7a14', $user_id );
			$user_school = mld_get_user_data( 'field_b1e8a7a', $user_id );
			$parent_name = mld_get_user_data( 'field_ef03a29', $user_id );
			$parent_email = mld_get_user_data( 'field_a5b87e3', $user_id );
			if( ! $parent_email ) {
				$parent_email = get_user_meta( $user_id, 'mld_user_parent_email', true );
			}

			if( ! $parent_name ) {
				$parent_name = get_user_meta( $user_id, 'mld_user_parent_name', true ); 
			}
			$user_email = isset( $user_data->data->user_email ) ? $user_data->data->user_email : '';
			$first_name = get_user_meta( $user_id, 'first_name', true );
			$last_name = get_user_meta( $user_id, 'last_name', true );
			$user_name = ucwords( $first_name ).''.ucwords( $last_name );

			if( ! $first_name && ! $last_name ) {
				$user_name = mld_get_username( $user_id );
			}

			$user_pass = '********';
			$user_capability = get_user_meta( $user_id, $wpdb->prefix.'capabilities', true );
			$user_capability = array_keys( $user_capability );
			$u_name = 'Staff Name';

			if( in_array( 'subscriber', $user_capability ) || in_array( 'student', $user_capability ) ) {
				$u_name = 'Student Name';
			}

			require_once MLD_TEMPLATES_DIR.'account-student-template.php';
		}

		$content = ob_get_contents();
		ob_get_clean();
		return $content;
	}

	/**
	 * Get group courses
 	 */
	public function mld_get_account_group_users() {

		$response = [];

		$group_id = isset( $_POST['group_id'] ) ? $_POST['group_id'] : 0;

		if( ! $group_id ) {

			$response['message'] = __( 'Group id not found', 'myrtle-learning-dashboard' );
			$response['status'] = 'false';

			echo json_encode( $response );
			wp_die();
		}

		$group_users = mld_get_group_users( $group_id );

		ob_start();
		
		if( $group_users && is_array( $group_users ) ) {

			?>
			<option value=""><?php echo __( 'Select a user', 'myrtle-learning-dashboard' ); ?></option>
			<?php

			foreach( $group_users as $group_user ) {
				?>
				<option value="<?php echo $group_user; ?>"><?php echo mld_get_username( $group_user ); ?></option>
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
	 * create a shortcode to display the report
	 */
	public function mld_myrtle_account() {

		$user_id = self::$instance->userid;
		
		ob_start();
		
		if ( current_user_can( 'manage_options' ) || learndash_is_group_leader_user( $user_id ) ) {

			if( current_user_can( 'manage_options' ) ) {
				$groups = mld_get_groups_for_admin();
			} else {
				$groups = mld_get_groups_for_leader( $user_id );
			}

			if( ! empty( $groups ) && is_array( $groups ) && current_user_can( 'manage_options' ) ) {

				?>
				<div class="mld-account-dropdowns-wrapper">
					<select class="mld-account-group-dropdown">
						<option value=""><?php echo __( 'Select a group', 'myrtle-learning-dashboard' ); ?></option>
						<?php
						foreach( $groups as $group_id ) {
							?>
							<option value="<?php echo $group_id; ?>"><?php echo get_the_title( $group_id ); ?></option>
							<?php 
						}
						?>
					</select>
					<select class="mld-account-users">
						<option value=""><?php echo __( 'Select a user', 'myrtle-learning-dashboard' ); ?></option>
					</select>
					<input type="button" class="mld-account-submit" disabled="disabled" value="<?php echo __( 'Apply', 'myrtle-learning-dashboard' ); ?>">
				</div>
				<?php
			}
		}

		echo $this->mld_get_user_account_html( $user_id );

		if ( current_user_can( 'manage_options' ) || learndash_is_group_leader_user( $user_id ) ) {

			?>
			<div class="mld-account-content-wrapper"></div>

			<div class="mld-files-wrapper mld-files-main-wrapper">
				<button class="mld-term-condition-btn"><?php echo __( 'Terms & Condition', 'myrtle-learning-dashboard' ); ?></button>
				<a href="https://www.zoho.com/" target="_blank"><button class="mld-invince-statement"><?php echo __( 'TIMESHEET', 'myrtle-learning-dashboard' ); ?></button></a>
				<button class="mld-invince-statement mld-invoice-btn mld-policy-btn"><?php echo __( 'POLICIES', 'myrtle-learning-dashboard' ); ?></button>
				<img src="<?php echo MLD_ASSETS_URL.'images/spinner.gif' ?>" class="mld-comment-loader">
			</div>
			<?php
		}

		$content = ob_get_contents();
		ob_get_clean();
		return $content;
	}

	/**
	 * create a function to get bank detail html
	 */
	public function mld_get_bank_detail_fields() {
		
		$user_id = self::$instance->userid;

		if( is_admin() ) {

			global $wpdb;

			$table_name = $wpdb->prefix . 'mld_bank_details';
			$query = $wpdb->prepare("SELECT * FROM $table_name WHERE user_id = %d", $user_id );
			$results = $wpdb->get_results($query);
			
			if( ! $results ) {
				return;
			}
		}

		$upload_dir = wp_upload_dir();
		$sign_upload_dir = $upload_dir['basedir'].'/mld_bank_detail_upload/user_'.$user_id.'/signature-initials-emailed-copies';
		$resource_upload_dir = $upload_dir['basedir'].'/mld_bank_detail_upload/user_'.$user_id.'/signature';

		$sign_file = glob( $sign_upload_dir . '/*' );
		$resource_file = glob( $resource_upload_dir . '/*' );

		$sign_file = isset( $sign_file[0] ) ? $sign_file[0] : '';
		$resource_file = isset( $resource_file[0] ) ? $resource_file[0] : '';

		$basePath = "/home/runcloud/webapps/myrtlelearning/";
		$sign_cleanedPath = str_replace( $basePath, '', $sign_file );
		$sign_url = site_url().'/'.$sign_cleanedPath;
		$resource_cleanedPath = str_replace( $basePath, '', $resource_file );
		$resource_url = site_url().'/'.$resource_cleanedPath;
		
		ob_start();

		require_once MLD_TEMPLATES_DIR.'bank-fields-template.php';

		$content = ob_get_contents();
		ob_get_clean();

		return $content;		
	}

	/**
	 * create a shortcode to display the fields
	 */
	public function mld_shortcode_to_display_personal_and_bank_detail_fields() {

		global $wpdb;

		if( ! self::$instance->userlogged ) {
			return __( 'Need to be logged in to view this page', 'myrtle-learning-dashboard' );
		}

		return $this->mld_get_bank_detail_fields();
	}

    /**
     * Add category type
     */
    public function mld_add_category_type() {

    	$type = isset( $_POST['type'] ) ? $_POST['type'] : '';

    	$new_category_title = $type;
    	$type = str_replace( " ", '-', $type );

    	if( ! empty( $type ) ) {

    		$category_type = 'mld-policy-type-'.$type;
    		$default_saved_category = get_option( 'mld_saved_category' );

    		if( $default_saved_category ) {
    			$default_category = array_merge( $default_saved_category, [$type] );
    		} else {
    			$default_category = [ 'data', 'administration', 'attendance', 'staff-operation', 'safeguarding', 'teacing-learning', 'examinations', 'teaching-agency', $type ];
    		}

    		update_option( 'mld_saved_category', $default_category );

    		$upload_dir = wp_upload_dir();

    		if( ! empty( $upload_dir['basedir'] ) ) {

    			$new_upload_dir = $upload_dir['basedir'].'/'.$category_type;

    			if ( ! file_exists( $new_upload_dir ) ) {
    				wp_mkdir_p( $new_upload_dir );
    			}
    		}

    		$category_folder = strtolower( $new_category_title );
    		$category_folder = str_replace( ' ', '-', $category_folder );
    		ob_start();

    		?>
    		<div class="mld-main-policy-wrapper mld-policy-wrapper">
    			<button data-type="mld-policy-type-<?php echo $category_folder; ?>" class="mld-delete-policy-type" id="mld-policy-type-<?php echo $category_folder; ?>"><?php echo __( 'Delete', 'myrtle-learning-dashboard' ); ?></button>
    			<div class="mld-policy-heading">
    				<?php echo ucwords( $new_category_title ); ?>
    			</div>
    			<div class="mld-policy-content">
    				<table id="<?php echo $category_type; ?>" class="mld-pdf-table">
    					<tbody></tbody>
    				</table>	
    			</div>
    			<div class="mld-policy-upload-btn">
    				<div class="mld-view-all-policy" data-category="<?php echo $category_type; ?>">
    					<span><?php echo __( 'See All', 'myrtle-learning-dashboard' ); ?></span>
    				</div>
    				<?php
    				if( current_user_can( 'manage_options' ) ) {
    					?>
    					<div class="mld-uplo-btn">
    						<button class="mld-upload-btn" data-category="<?php echo $category_type; ?>"><?php echo __( 'Upload', 'myrtle-learning-dashboard' ); ?><span class="dashicons dashicons-upload"></span></button>
    					</div>
    					<?php 
    				}
    				?>
    			</div>
    		</div>
    		<?php

    		$content = ob_get_contents();
    		ob_get_clean();

    		$response['content']  = $content;
    		$response['status']  = 'true';
    		echo json_encode( $response );
    		wp_die();
    	}

    	$response['status']  = 'false';
    	wp_die();
    }

	/**
	 * update user contacts
	 */
	public function mld_update_contacts() {

		$user_id = isset( $_POST['user_id'] ) ? intval( $_POST['user_id'] ) : 0;
		
		$contact_one = isset( $_POST['contact_one'] ) ? $_POST['contact_one']: 0;
		$contact_two = isset( $_POST['contact_two'] ) ? $_POST['contact_two'] : 0;
		
		$get_basic_info = get_user_meta( $user_id, 'mld-teacher-basic-info', true );
		$tags = isset( $_POST['tags'] ) ? $_POST['tags'] : '';

		if( learndash_is_group_leader_user( $user_id ) == true || user_can( $user_id, 'manage_options') ) { 
			
			if( $get_basic_info ) {

				$get_basic_info['hometel'] = $contact_one;
				$get_basic_info['mobile_number'] = $contact_two;
				update_user_meta( $user_id, 'mld-teacher-basic-info', $get_basic_info );
			} else {

				if( $contact_one ) {

					$mld_tel_no = mld_get_user_data( 'tel', $user_id );

					if( $mld_tel_no ) {

						if( NULL == $mld_tel_no || empty ( $mld_tel_no ) ) {
							mld_insert_data( 'tel', $contact_one, $user_id );
						} else {
							mld_update_user_data( 'tel', $contact_one, $user_id );
						}
					} else {
						if( NULL == $mld_tel_no || empty ( $mld_tel_no ) ) {
							mld_insert_data( 'field_935a7ff', $contact_one, $user_id );
						} else {
							mld_update_user_data( 'field_935a7ff', $contact_one, $user_id );
						}
					}
				}

				if( $contact_two ) {

					$mld_mobile_no = mld_get_user_data( 'mobile_number', $user_id );
					
					if( $mld_mobile_no ) {
						if( NULL == $mld_mobile_no || empty ( $mld_mobile_no ) ) {
							mld_insert_data( 'mobile_number', $contact_two, $user_id );
						} else {
							mld_update_user_data( 'mobile_number', $contact_two, $user_id );
						}
					} else {
						if( NULL == $mld_mobile_no || empty ( $mld_mobile_no ) ) {
							mld_insert_data( 'field_c1f0933', $contact_two, $user_id );
						} else {
							mld_update_user_data( 'field_c1f0933', $contact_two, $user_id );
						}
					}				
				}
			}

		} else {
			// update_user_meta( $user_id, 'mld_phone_number', $contact_one );
			// update_user_meta( $user_id, 'mld_home_tel', $contact_two );
			// var_dump( 'working' );
			$mld_user_ph_no = mld_get_user_data( 'field_c1f0933', $user_id );
					
			if( $contact_one ) {

				if( NULL == $mld_user_ph_no || empty ( $mld_user_ph_no ) ) {
					mld_insert_data( 'field_c1f0933', $contact_one, $user_id );
				} else {
					mld_update_user_data( 'field_c1f0933', $contact_one, $user_id );
				}
			}

			$mld_user_home_no = mld_get_user_data( 'field_935a7ff', $user_id );
			
			if( $contact_two ) {

				if( NULL == $mld_user_home_no || empty ( $mld_user_home_no ) ) {
					mld_insert_data( 'field_935a7ff', $contact_two, $user_id );
				} else {
					mld_update_user_data( 'field_935a7ff', $contact_two, $user_id );
				}
			}
		}

		update_user_meta( self::$instance->userid, 'mld_teacher_selected_subjects', $tags );
		wp_die();
	}

	/**
	 * Delete a policy
	 */
	public function mld_delete_policies() {

		$delete_url = isset( $_POST['url'] ) ? $_POST['url'] : '';

		if( empty( $delete_url ) ) {

			$response['content'] = __( 'url not found', 'myrtle-learning-dashboard' );
			$response['status']  = 'false';

			echo json_encode( $response );
			wp_die();
		}

		unlink( $delete_url );
		$response['status']  = 'true';

		echo json_encode( $response );
		wp_die();
	}

	/**
	 * update user profile
	 */
	public function mld_update_user_profile( $avatar, $id_or_email, $args  ) {

		global $wpdb;

		$avatar = get_user_meta( $id_or_email, 'mld_user_avatar', true );

		if( ! $avatar ) {

			$table_name = $wpdb->prefix.'e_submissions_values';
			$user_email = mld_get_user_email( $id_or_email );
        	$submission = $wpdb->get_results( "SELECT submission_id FROM $table_name WHERE value = '".$user_email."' LIMIT 1 " );
        	$submission_id = isset( $submission[0]->submission_id ) ? intval( $submission[0]->submission_id ) : 0;
        	$key = 'field_8427b4d';

        	$submission_data = $wpdb->get_results( "SELECT submission.value as val FROM $table_name as submission WHERE submission.key = '".$key."' AND submission.submission_id = $submission_id " );
        	$avatar = isset( $submission_data[0] ) ? $submission_data[0]->val : 0;
		}

		ob_start();

		?>
		<img src="<?php echo $avatar; ?>" class="mld-update-avatar">
		<?php

		$avatar = ob_get_contents();
		ob_get_clean();
		return $avatar;
	}

	/**
	 * update user profile
	 */
	public function mld_change_user_profile() {

		$user_id = self::$instance->userid;

		$user = new WP_User( $user_id );
		$user->set_role('');

		require_once ABSPATH . 'wp-admin/includes/file.php';
		$uploaded_file = wp_handle_upload( $_FILES['profile'], ['test_form' => false] ); 

		if ( isset( $uploaded_file['url'] ) ) {
			$avatar_url = $uploaded_file['url']; 
			update_user_meta($user_id, 'mld_user_avatar', $avatar_url);
		}
	}

	/**
	 * update user password
	 */
	public function mld_update_password() {

		$user_id = isset( $_POST['user_id'] ) ? $_POST['user_id'] : self::$instance->userid;
		$user_data = get_userdata( $user_id );
		$user_email = mld_get_user_email( $user_id );
		$old_pass = isset( $_POST['old_password'] ) ? $_POST['old_password'] : '';
		$new_pass = isset( $_POST['new_password'] ) ? $_POST['new_password'] : '';

		if( ! $old_pass || ! $new_pass ) {

			$response['content'] = __( 'Please fill the both fields', 'myrtle-learning-dashboard' );

			echo json_encode( $response );
			wp_die();
		}

		$is_valid_credential = wp_authenticate( $user_email, $old_pass );
		$pass_error = isset( $is_valid_credential->errors ) ? $is_valid_credential->errors : 0;

		if( $pass_error ) {

			$response['content'] = __( 'Please fill the correct old password', 'myrtle-learning-dashboard' );

			echo json_encode( $response );
			wp_die();
		}

		reset_password( $user_data->data, $new_pass );
		$response['content'] = __( 'Password Reset Successfully...', 'myrtle-learning-dashboard' );

		echo json_encode( $response );
		wp_die();
	}

	/**
	 * upload policy pdf
	 */
	public function mld_upload_policy_pdf() {

		$category = isset( $_POST['policyType'] ) ? $_POST['policyType'] : '';
		$filename = isset( $_FILES['file']['name'] ) ? $_FILES['file']['name'] : '';
		
		if( ! $category || ! $filename ) {
			wp_die();
		}

		$upload_dir = wp_upload_dir();

		if( ! empty( $upload_dir['basedir'] ) ) {

			$new_upload_dir = $upload_dir['basedir'].'/'.$category;

			if ( ! file_exists( $new_upload_dir ) ) {
				wp_mkdir_p( $new_upload_dir );
			}

			move_uploaded_file( $_FILES['file']['tmp_name'], $new_upload_dir . '/' . $filename);

			$pdf_files = mld_get_category_files( $category );

			if( $pdf_files && is_array( $pdf_files ) ) {

				ob_start();

				foreach( $pdf_files as $key => $pdf_file ) {

					$display = '';

					if( 2 < $key ) {
						$display = 'none';
					}

					$basePath = "/home/runcloud/webapps/myrtlelearning/";
					$cleanedPath = str_replace( $basePath, '', $pdf_file );
					$cleanedPath = site_url().'/'.$cleanedPath;
					$avatar_url = $cleanedPath;

					$url = $pdf_file;
					$category = explode( '/', $pdf_file );
					$category = end( $category );

					?>	
					<tr style="display: <?php echo $display; ?>">
						<td class="mld-policy-img"><img src="<?php echo $avatar_url; ?>"></td>
						<td class="mld-policy-title"><?php echo substr( $category, 0, 15 ); ?></td>
						<td class="mld-policy-data mld-single-pdf-dele-option">
							<img src="<?php echo MLD_ASSETS_URL.'images/three-dot.png' ?>" style="height: 20px;">
							<div class="mld-policy-delete" data-url="<?php echo $url; ?>">
								<?php echo __( 'Delete', 'myrtle-learning-dashboard' ); ?>
							</div>
						</td>				
					</tr>
					<?php
				}

				$contents = ob_get_contents();
				ob_get_clean();
				echo $contents;
			}
		}
		wp_die();
	}

	/**
	 * get policies
	 */
	public function mld_get_policies() {

		$pdf_type = isset( $_POST['type'] ) ? $_POST['type'] : '';

		if( $pdf_type ) {

			$pdf_files = mld_get_category_files( $pdf_type );

			if( ! empty( $pdf_files ) && is_array( $pdf_files ) ) {

				ob_start();
				?>
				<div class="mld-user-pdf">
				<?php
				foreach( $pdf_files as $pdf_file ) {

					$pdf_name = explode( '/', $pdf_file );
					$pdf_name = end( $pdf_name );
					$pdf_name = explode( '.', $pdf_name );
					$pdf_name = $pdf_name[0];
					$basePath = "/home/runcloud/webapps/myrtlelearning/";
					$cleanedPath = str_replace( $basePath, '', $pdf_file );
					$cleanedPath = site_url().'/'.$cleanedPath;
					$avatar_url = $cleanedPath;
					?>
					<div class="mld-pdf-main-wrapper">
						<div class="mld-pdf-name"><?php echo ucwords( $pdf_name ); ?></div>
						<div class="mld-pdf-wrapper">
							<a href="<?php echo $avatar_url; ?>" download>
								<img src="<?php echo MLD_ASSETS_URL.'images/myrtle-pdf.jpg'; ?>">
							</a>
						</div>
						<div class="mld-delete-pdf">
							<img src="<?php echo MLD_ASSETS_URL.'images/three-dot.png' ?>" class="mld-pdf-delete">
							<p>
								<input type="button" value="<?php echo __( 'Delete', 'myrtle-learning-dashboard' ); ?>" data-url="<?php echo $o_file; ?>">
							</p>
						</div>
					</div>
					<?php
				}
				?>
				</div>
				<?php
			}

			$content = ob_get_contents();
			ob_get_clean();

			$response['pdfpopup'] = 'true';
			$response['content'] = $content;
			$response['status']  = 'true';

			echo json_encode( $response );
			wp_die();
		} else {

			$upload_dir = wp_get_upload_dir();
			$directory_path = $upload_dir['basedir'] . '/mld_policy_files/';
			$files = glob($directory_path . '/*');

			if( is_array( $files ) && ! empty( $files ) ) {
				ob_start();
				?>
				<div class="mld-user-pdf">
					<?php
					foreach ( $files as $file ) {
						$o_file = $file;
						$replacement = "/nas/content/live/myrtlelearning";
						$avatar_filtrer = str_replace( $replacement, '', $file );
						$site_url = site_url();
						$avatar = $site_url.'/'.$avatar_filtrer;
						$avatar_url = filter_var( $avatar, FILTER_VALIDATE_URL );
						?>
						<div class="mld-pdf-main-wrapper">
							<div class="mld-pdf-wrapper">
								<a href="<?php echo $avatar_url; ?>" download><img src="<?php echo MLD_ASSETS_URL.'images/myrtle-pdf.jpg'; ?>"></a>
							</div>
							<div class="mld-delete-pdf">
								<img src="<?php echo MLD_ASSETS_URL.'images/three-dot.png' ?>" class="mld-pdf-delete">
								<p>
									<input type="button" value="<?php echo __( 'Delete', 'myrtle-learning-dashboard' ); ?>" data-url="<?php echo $o_file; ?>">
								</p>
							</div>
						</div>
						<?php
					}
					?>
				</div>
				<?php
				$content = ob_get_contents();
				ob_get_clean();

				$response['pdfpopup'] = 'false';
				$response['content'] = $content;
				$response['status']  = 'true';

				echo json_encode( $response );
				wp_die();
			}
		}

		$response['content'] = __( 'No Policies Uploaded', 'myrtle-learning-dashboard' );
		$response['status']  = 'false';

		echo json_encode( $response );
		wp_die();
	}

	/**
	 * upload user pdfs
	 */
	public function mld_upload_admin_pdf() {

		$user_id = isset( $_POST['user_id'] ) ? intval( $_POST['user_id'] ) : 0;
		$user_capability = mld_user_capability( self::$instance->userid );	
		$user_name = mld_get_username( $user_id );

		$name_title = __( 'Student Name : ', 'myrtle-learning-dashboard' );
		
		if( in_array( 'administrator', $user_capability ) || in_array( 'group_leader', $user_capability ) ) {
			$name_title = __( 'Staff Name : ', 'myrtle-learning-dashboard' );
		}

		$upload_dir = wp_upload_dir();

		if( ! empty( $upload_dir['basedir'] ) ) {

			$new_upload_dir = $upload_dir['basedir'].'/mld_uploaded_files/user_files_'.$user_id;

			if ( ! file_exists( $new_upload_dir ) ) {
				wp_mkdir_p( $new_upload_dir );
			}

			$filename = str_replace( ' ', '-', $_FILES['file']['name'] );
			move_uploaded_file( $_FILES['file']['tmp_name'], $new_upload_dir . '/' . $filename);

			$upload_dir = wp_get_upload_dir();
			$directory_path = $upload_dir['basedir'] . '/mld_uploaded_files/user_files_'.$user_id;
			$files = glob( $directory_path . '/*' );

			$attachment_dir_path = $upload_dir['basedir'] . '/mld-teachers-data/teacher_'.$user_id;
			$attachment_files = glob( $attachment_dir_path . '/*' );
			$files = array_merge( $files, $attachment_files );
			
			ob_start();

			if( is_array( $files ) && ! empty( $files ) ) {
				?>
				<div class="mld-user-pdf mld-delete-user-pdf">
				<?php
				foreach ( $files as $file ) {

					$replacement = "/nas/content/live/myrtlelearning";
					$avatar_filtrer = str_replace( $replacement, '', $file );
					$site_url = site_url();
					$avatar = $site_url.'/'.$avatar_filtrer;
					$avatar_url = filter_var( $avatar, FILTER_VALIDATE_URL );
					$pdf_file_name = explode( '/', $file );
					$pdf_file_name = end( $pdf_file_name );
					$pdf_file_name = explode( '.', $pdf_file_name );
					$pdf_file_name = $pdf_file_name[0];
					
					if( user_can( $user_id, 'manage_options' ) ) {
						?>
						<div class="mld-pdf-main-wrapper">
							<div class="mld-pdf-wrapper">
								<div class="mld-pdf-name"><?php echo ucwords( $pdf_file_name ); ?></div>
								<a href="<?php echo $avatar_url; ?>" download><img src="<?php echo MLD_ASSETS_URL.'images/myrtle-pdf.jpg'; ?>"></a>
							</div>
							<div class="mld-delete-pdf">
								<img src="<?php echo MLD_ASSETS_URL.'images/three-dot.png' ?>" class="mld-pdf-delete">
								<p>
									<input type="button" value="<?php echo __( 'Delete', 'myrtle-learning-dashboard' ); ?>" data-url="<?php echo $file; ?>">
								</p>
							</div>
						</div>
						<?php
					} else {

						if ( user_can( self::$instance->userid, 'manage_options' ) ) {
							?>
							<div class="mld-pdf-main-wrapper">
								<div class="mld-pdf-wrapper">
									<div class="mld-pdf-name"><?php echo ucwords( $pdf_file_name ); ?></div>
									<a href="<?php echo $avatar_url; ?>" download><img src="<?php echo MLD_ASSETS_URL.'images/myrtle-pdf.jpg'; ?>"></a>
								</div>
								<div class="mld-delete-pdf">
									<img src="<?php echo MLD_ASSETS_URL.'images/three-dot.png' ?>" class="mld-pdf-delete">
									<p>
										<input type="button" value="<?php echo __( 'Delete', 'myrtle-learning-dashboard' ); ?>" data-url="<?php echo $file; ?>">
										<input type="button" class="mld-pdf-comment" value="<?php echo __( 'Comment', 'myrtle-learning-dashboard' ); ?>" data-pdf_key="<?php echo $pdf_file_name; ?>" data-user_id="<?php echo $user_id; ?>">
									</p>
								</div>
							</div>
							<?php
						} else {
							?>
							<div class="mld-pdf-main-wrapper">
								<div class="mld-pdf-wrapper">
									<div class="mld-pdf-name"><?php echo ucwords( $pdf_file_name ); ?></div>
									<a href="<?php echo $avatar_url; ?>" download><img src="<?php echo MLD_ASSETS_URL.'images/myrtle-pdf.jpg'; ?>"></a>
								</div>
								<div class="mld-delete-pdf">
									<img src="<?php echo MLD_ASSETS_URL.'images/three-dot.png' ?>" class="mld-pdf-delete">
									<p>
										<input type="button" class="mld-pdf-comment" data-is_capable="no" value="<?php echo __( 'Comment', 'myrtle-learning-dashboard' ); ?>" data-pdf_key="<?php echo $pdf_file_name; ?>" data-user_id="<?php echo $user_id; ?>">
									</p>
								</div>
							</div>
							<?php
						}
					}
				}
				?>
				</div>
				<?php
			}

			$content = ob_get_contents();
			ob_get_clean();
			echo $content;

			/** send email to admin **/

			$headers = array('Content-Type: text/html; charset=UTF-8');
			$header_url = MLD_ASSETS_URL.'images/header.PNG';
			$footer_url = MLD_ASSETS_URL.'images/footer.PNG';
			$file_upload_content .= '<img src="'.$header_url.'" style="width: 99%;">';
			$file_upload_content .= '<p></p>';

			$file_upload_content .= '<div style="font-size: 20px; color: #365249;">'.$name_title.' '.$user_name.'</div>';
			$file_upload_content .= '<div>'.__( 'Document Title' ).'</div>';
			$file_upload_content .= '<div>'.$filename.'</div>';

			$file_upload_content .= '<p></p>';
			$file_upload_content .= '<img src="'.$footer_url.'" style="width: 99%;">';
			$admin_email = get_option('admin_email');
			wp_mail( 'hello@myrtlelearning.com', 'File Upload', $file_upload_content, $headers );
		}
		wp_die();
	}
	/**
	 * enqeue account scripts
	 */
	public function mld_account_scripts() {

		if( ! self::$instance->userlogged ) {
			return;
		}

		$user_id = self::$instance->userid;
		$user_role = mld_user_capability( $user_id );
		$rand = rand( 1000000, 1000000000 );

		if( 'my-account' == FRONT_PAGE || 'employee-and-bank-detail' == FRONT_PAGE || 'student-admission-form' == FRONT_PAGE || in_array( 'pending_teacher', $user_role ) || in_array( 'pending_student', $user_role ) ) {

			wp_enqueue_style( 'account-css', MLD_ASSETS_URL .'css/account.css', '', $rand, false );
			wp_enqueue_script( 'account-frontend', MLD_ASSETS_URL . 'js/account.js', [ 'jquery' ], $rand, true );
			wp_localize_script( 'account-frontend', 'MLD', [
				'ajaxURL'       => admin_url( 'admin-ajax.php' ),
				'siteURL'		=> site_url()
			] );
		}
	}

	/**
	 * create a function to get content footer
	 */
	public static function mld_get_content_footer() {
		?>
		<div class="mld-save-changes">
			<button><?php echo __( 'Save Changes', 'myrtle-learning-dashboard' ); ?></button>
		</div>
		<div class="mld-files-wrapper">

			<a href="https://clockify.me/" target="_blank"><button><?php echo __( 'INVOICE/STATEMENT', 'myrtle-learning-dashboard' ); ?></button></a>
			<button><?php echo __( 'UPLOAD FILES', 'myrtle-learning-dashboard' ); ?></button>
		</div>
		<?php
	}
	/**
	 * create a function to get input field
	 */
	public static function mld_get_input_wrapper( $title, $type, $value = '', $title_class = '', $content_class = '', $parent_class = '', $read_only = '', $input_class = '', $textarea = '' ) {
		?>
		<div class="mld-account-input-wrapper <?php echo $parent_class; ?>">
			<div class="mld-input-title <?php echo $title_class; ?>">
				<?php echo __( $title, 'myrtle-learning-dashboard' ); ?>
			</div>
			<div class="mld-input-content <?php echo $content_class; ?>">
				<?php 
				if( $textarea ) {

					?>
					<textarea rows="2" placeholder="<?php echo $title; ?>" <?php echo $read_only; ?>><?php echo $value; ?></textarea>
					<?php
				} else {
					?>
					<input type="<?php echo $type; ?>" <?php echo $read_only; ?> value="<?php echo $value; ?>" class="<?php echo $input_class; ?>">
					<?php
				}
				?>
			</div>
		</div>
		<?php
	}

	/**
	 * create a function to get password reset html
	 */
	public static function mld_get_password_reset_html( $user_email, $user_pass ) {
		?>
		<div class="mld-password-reset-wrapper">
			<div class="mld-security-setting-button">

				<?php
				echo self::mld_get_input_wrapper( 'security-setting', 'button', 'Security Settings', 'mld-title-hidden' );
				?>
			</div>
			<div class="mld-password-reset-fields">
				<?php
					echo self::mld_get_input_wrapper( 'Email', 'text', $user_email, '', '', '', 'readonly' );
					echo self::mld_get_input_wrapper( 'Password', 'text', $user_pass, '', '', '', 'readonly' );
					echo self::mld_get_input_wrapper( 'Reset Password', 'button', 'Reset Password', 'mld-title-hidden', 'mld-reset-btn' );
				?>
			</div>
		</div>
		<?php
	}

	/**
	 * create a function to get user avatar and personal details button
	 */
	public static function mld_get_user_profile( $user_id = '' ) {
		
		if( ! $user_id ) {
			$user_id = self::$instance->userid;
		}
	
		?>
		<div class="mld-user-profile-wrapper">
			<div class="mld-persobal-btn">
				<button><?php echo __( 'Personal Details', 'myrtle-learning-dashboard' ); ?></button>
			</div>
			<input type="file" class="mld-edit-profile-input" style="display: none;">
			<div class="mld-user-profile">
				<div class="mld-main-avatar" profile-default_src="<?php echo get_avatar_url($user->ID, ['size' => '40'] );?>">
				<?php
				echo get_avatar( $user_id );
				?>
				</div>
				<div class="mld-edit-profile">
					<img src="<?php echo MLD_ASSETS_URL.'images/edit.png' ?>" class="mld-edit-icon">
				</div>
			</div>
			<div class="mld-clear-both"></div>
		</div>
		<?php
	}
}

/**
 * Initialize MLD_ACCOUNT_MODULE
 */
MLD_ACCOUNT_MODULE::instance();