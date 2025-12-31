<?php
	
/**
 * create a function to get user data
 */ 
function mld_get_user_data( $key, $user_id ) {

	global $wpdb;
	$user_email = mld_get_user_email( $user_id );
	$table_name = $wpdb->prefix.'e_submissions_values';
	$submissions_table = $wpdb->prefix.'e_submissions';

	/**
	 * create a query to get submission id
	 */

	$submission = $wpdb->get_results( "SELECT submission_id FROM $table_name WHERE value = '".$user_email."' " );
	$submission = end( $submission );
	$submission_id = isset( $submission->submission_id ) ? intval( $submission->submission_id ) : 0;

	if( ! $submission_id ) {
		return '';
	}

	$submission_data = $wpdb->get_results( "SELECT submission.value as val FROM $table_name as submission WHERE submission.key = '".$key."' AND submission.submission_id = $submission_id " );
	$submission_data = isset( $submission_data[0]->val ) ? $submission_data[0]->val : 0;
	return $submission_data;
}

/**
 * create a function to update user contact
 */
function mld_update_user_data( $key, $value, $user_id ) {

	global $wpdb;

	$user_email = mld_get_user_email( $user_id );
	$table_name = $wpdb->prefix.'e_submissions_values';

   /**
	* create a query to get submission id
	*/
	$submission = $wpdb->get_results( "SELECT submission_id FROM $table_name WHERE value = '".$user_email."' LIMIT 1 " );
	$submission_id = isset( $submission[0]->submission_id ) ? intval( $submission[0]->submission_id ) : 0;
	$query = "UPDATE $table_name as submission SET submission.value = '$value' WHERE submission.submission_id = $submission_id AND submission.key = '$key'";
	$true = $wpdb->query($query);

	if( false === $true || empty( $true ) || NULL === $true ) {
		return false;
	}
}

/**
 * create a function to inerst the data
 */
function mld_insert_data( $key, $value, $user_id ) {

	global $wpdb;

	$user_email = mld_get_user_email( $user_id );
	$table_name = $wpdb->prefix.'e_submissions_values';

	/**
	 * create a query to get submission id
	 */
	$submission = $wpdb->get_results( "SELECT submission_id FROM $table_name WHERE value = '".$user_email."' LIMIT 1 " );
	$submission_id = isset( $submission[0]->submission_id ) ? intval( $submission[0]->submission_id ) : 0;

	$data = array(
		'submission_id' => $submission_id,
		'key' 			=> $key,
		'value' 		=> $value
	);

	$wpdb->insert( $table_name, $data );
}
?>