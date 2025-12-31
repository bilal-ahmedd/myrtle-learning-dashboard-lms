<?php
/**
 * Account template
 */

if( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class Myrtle_account_template
 */
class Myrtle_account_template {

	/**
	 * @var self
	 */
	private static $instance = null;

	/**
	 * @since 1.0
	 * @return $this
	 */
	public static function instance() {

		if ( is_null( self::$instance ) && ! ( self::$instance instanceof Myrtle_account_template ) ) {
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
		add_action( 'edit_user_profile', [ $this, 'mld_add_account_fields' ] );
		add_action( 'show_user_profile', [ $this, 'mld_add_account_fields' ] );
		add_action( 'edit_user_profile_update', [ $this, 'mld_update_user_fields' ] );
		add_action( 'personal_options_update', [ $this, 'mld_update_user_fields' ] );
		add_action( 'wp_ajax_trash_communication', [ $this, 'mld_trash_communication' ] );
		add_action( 'wp_ajax_update_communication_comments', [ $this, 'mld_update_communication_comments' ] );
	}

	/**
	 * update communication message
	 */
	public function mld_update_communication_comments() {

		global $wpdb;

		$table_name = $wpdb->prefix . 'mld_client_communication';
		$id = isset( $_POST['id'] ) ? $_POST['id'] : 0;
		$message = isset( $_POST['message'] ) ? $_POST['message'] : '';

		$update_query = $wpdb->prepare(
			"UPDATE $table_name SET message = %s WHERE ID = %d",
			$message, $id
		);

		$result = $wpdb->query($update_query);
		wp_die();
	}

	/**
	 * trash communication message
	 */
	public function mld_trash_communication() {

		global $wpdb;

		$id = isset( $_POST['id'] ) ? $_POST['id'] : 0;

		$table_name = $wpdb->prefix . 'mld_client_communication';

		$delete_query = $wpdb->prepare(
			"DELETE FROM $table_name WHERE ID = %d",
			$id
		);

		$result = $wpdb->query($delete_query);
		wp_die();
	}

	/**
	 * Add account fields
	 */
	public function mld_add_account_fields( $user ) {

		global $wpdb;

		$user_id = isset( $user->data->ID ) ? intval( $user->data->ID ) : 0;

		$key_one = 'field_5804a34';
		$key_two = 'address';
		if( learndash_is_group_leader_user( $user_id ) === false && ! user_can( $user_id, 'administrator' ) ) {

			$key_one = 'field_1b47487';
			$key_two = 'field_54e7a14';
		}

		$teacher_basic_info = get_user_meta( $user_id, 'mld-teacher-basic-info', true ); 
		$mld_date_birth = isset( $teacher_basic_info['dob'] ) ? $teacher_basic_info['dob'] : '';
		if( ! $mld_date_birth ) {
			$mld_date_birth = mld_get_user_data( $key_one, $user_id );
		}

		$mld_user_address = isset( $teacher_basic_info['address'] ) ? $teacher_basic_info['address'] : '';
		if( ! $mld_user_address ) {
			$mld_user_address = mld_get_user_data( $key_two, $user_id );	
		} 		

		$mld_user_sch = mld_get_user_data( 'field_b1e8a7a', $user_id );
		$mld_father_email = mld_get_user_data( 'field_a5b87e3', $user_id );
		if( ! $mld_father_email ) {
			$mld_father_email = get_user_meta( $user_id, 'mld_user_parent_email', true );
		}
		$mld_get_father_name = mld_get_user_data( 'field_ef03a29', $user_id );
		if( ! $mld_get_father_name ) {
			$mld_get_father_name = get_user_meta( $user_id, 'mld_user_parent_name', true );
		}
		?>
		<div class="mld-admin-account-fields-wrapper">
			<b>
				<h1><?php echo __( 'User Details', 'myrtle-learning-dashboard' ); ?></h1>
			</b>
			<div class="mld-account-fields">
				<h3><?php echo __( 'Date of Birth', 'myrtle-learning-dashboard' ); ?></h3>
				<p><input type="date" name="mld-user-dob" value="<?php echo $mld_date_birth; ?>" style="height: 44px; width: 400px;"></p>
				<h3><?php echo __( 'Home Address', 'myrtle-learning-dashboard' ); ?></h3>
				<p><textarea rows="3" name="mld-user-address" placeholder="<?php echo __( 'Home Address', 'myrtle-learning-dashboard' ); ?>" style="width: 400px;"><?php echo stripslashes( $mld_user_address ); ?></textarea></p>
				<?php

				if( learndash_is_group_leader_user( $user_id ) === false && ! user_can( $user_id, 'administrator' ) ) {
					?>
					<h3><?php echo __( 'School Name', 'myrtle-learning-dashboard' ); ?></h3>
					<input type="text" name="mld-school-name" style="height: 44px; width: 400px;" value="<?php echo $mld_user_sch; ?>" placeholder="<?php echo __( 'Enter School Name', 'myrtle-learning-dashboard' ); ?>">
					<div>
						<h1><?php echo __( 'Parent Details', 'myrtle-learning-dashboard' ); ?></h1>
					</div>
					<h3><?php echo __( "Enter Father's Name", 'myrtle-learning-dashboard' ); ?></h3>
					<p>
						<input type="text" name="mld-father-name" style="height: 44px; width: 400px;" value="<?php echo $mld_get_father_name; ?>" placeholder="<?php echo __( "Father's Name", 'myrtle-learning-dashboard' );?>">
					</p>

					<h3><?php echo __( "Enter Father's Email", 'myrtle-learning-dashboard' ); ?></h3>
					<p>
						<input type="email" name="mld-father-email" style="height: 44px; width: 400px;" value="<?php echo $mld_father_email; ?>" placeholder="<?php echo __( "Father's Email", 'myrtle-learning-dashboard' );?>">
					</p>
					<?php
				}
				?>
			</div>
		</div>
		<h3><?php echo __( 'Client Communication', 'myrtle-learning-dashboard' ); ?></h3>
		<div class="mld-communication-wrapper-backend">
			<?php 
			$client_table_name = $wpdb->prefix.'mld_client_communication';
			$name_table = $wpdb->prefix.'users';

			$client_query = $wpdb->prepare(
				"SELECT * FROM $client_table_name WHERE current_user_id = %d ORDER BY ID DESC",
				$user_id
			);

			$communication_data = $wpdb->get_results($client_query);

			if( ! empty( $communication_data ) && is_array( $communication_data ) ) {
				?>
				<style type="text/css">
					.mld-communication-wrapper-backend {
						height: 300px;
					}
				</style>
				<?php
				foreach( $communication_data as $data ) {

					$cl_msg = isset( $data->message ) ? $data->message : '';
					$timestam = isset( $data->dates ) ? $data->dates : '';
					$author_name = isset( $data->logged_in_user_id ) ? $data->logged_in_user_id : 0;
					$auto_id = isset( $data->ID ) ? $data->ID : 0;

					$query = $wpdb->prepare(
						"SELECT display_name 
						FROM {$wpdb->users} 
						WHERE ID = %d",
						$author_name
					);
					$mld_surname = ucwords( $wpdb->get_var($query) );
					?>
					<div class="mld-communication-inner-wrapper">
						<div class="mld-message-reference"><?php echo date( 'l jS \of F Y h:i:s A', $timestam ).' ( '.ucwords( $mld_surname ).' )'; ?></div>
						<div class="mld-message"><?php echo $cl_msg; ?></div>
						<span class="dashicons dashicons-edit mld-communication-edit" data-ID="<?php echo $auto_id; ?>"></span>
						<span class="dashicons dashicons-trash mld-communication-trash" data-ID="<?php echo $auto_id; ?>"></span>
						<div class="mld-communication-update-btn">
							<button data-ID="<?php echo $auto_id; ?>"><?php echo __( 'Update Communication', 'myrtle-learning-dashboard' ); ?></button>
						</div>
					</div>
					<?php
				}
			}
			?>
		</div>
		<?php
	}

	/**
	 * update user fields
	 */
	public function mld_update_user_fields( $user_id ) {

		$key_one = 'field_5804a34';
		$key_two = 'address';

		if( learndash_is_group_leader_user( $user_id ) == false && ! user_can($user_id, 'administrator') ) {
			
			$key_one = 'field_1b47487';
			$key_two = 'field_54e7a14';

			$mld_sch_name = isset( $_POST['mld-school-name'] ) ? $_POST['mld-school-name'] : '';
			$mld_user_sch = mld_get_user_data( 'field_b1e8a7a', $user_id );
			
			if( $mld_sch_name ) {

				if( NULL == $mld_user_sch || empty ( $mld_user_sch ) ) {
					mld_insert_data( 'field_b1e8a7a', $mld_sch_name, $user_id );
				} else {
					mld_update_user_data( 'field_b1e8a7a', $mld_sch_name, $user_id );
				}
			}

			/**
			 * Father name html
			 */ 
			$mld_fath_name = isset( $_POST['mld-father-name'] ) ? $_POST['mld-father-name'] : '';
			
			// $mld_get_father_name = mld_get_user_data( 'field_ef03a29', $user_id );

			// if( $mld_fath_name ) {
				update_user_meta( $user_id, 'mld_user_parent_name', $mld_fath_name );
				// if( NULL == $mld_get_father_name || empty ( $mld_get_father_name ) ) {
				// 	mld_insert_data( 'field_ef03a29', $mld_fath_name, $user_id );
				// } else {
				// 	mld_update_user_data( 'field_ef03a29', $mld_fath_name, $user_id );
				// }
			// }

			/**
			 * Father Email html
			 */
			$mld_fath_email = isset( $_POST['mld-father-email'] ) ? $_POST['mld-father-email'] : '';
			update_user_meta( $user_id, 'mld_user_parent_email', $mld_fath_email );
		}

		$dob = isset( $_POST['mld-user-dob'] ) ? $_POST['mld-user-dob'] : 0;
		$mld_address = isset( $_POST['mld-user-address'] ) ? $_POST['mld-user-address'] : '';

		$get_basic_info = get_user_meta( $user_id, 'mld-teacher-basic-info', true );
		
		if( ! $get_basic_info ) {

			$mld_date_birth = mld_get_user_data( $key_one, $user_id );
			if( $dob ) {
				if( NULL == $mld_date_birth || empty ( $mld_date_birth ) ) {
					mld_insert_data( $key_one, $dob, $user_id );
				} else {
					mld_update_user_data( $key_one, $dob, $user_id );
				}
			}

			$mld_user_address = mld_get_user_data( $key_two, $user_id );

			if( $mld_address ) {

				if( NULL == $mld_user_address || empty ( $mld_user_address ) ) {
					mld_insert_data( $key_two, $mld_address, $user_id );
				} else {
					mld_update_user_data( $key_two, $mld_address, $user_id );
				}
			}
		} 

		if( $get_basic_info ) {

			$get_basic_info['dob'] = $dob;
			$get_basic_info['address'] = $mld_address;
			update_user_meta( $user_id, 'mld-teacher-basic-info', $get_basic_info );
		}
	}
}

Myrtle_account_template::instance();