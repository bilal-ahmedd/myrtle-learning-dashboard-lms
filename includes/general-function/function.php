<?php 

/**
 * create a function to get lesson topic
 */
function mld_get_lesson_topics() {

	global $wpdb;

	$query = "
	SELECT p.ID 
	FROM {$wpdb->postmeta} AS pm1 
	JOIN {$wpdb->postmeta} AS pm2 ON pm1.post_id = pm2.post_id 
	JOIN {$wpdb->posts} AS p ON pm1.post_id = p.ID 
	WHERE pm1.meta_key = %s AND pm1.meta_value = %d 
	AND pm2.meta_key = %s AND pm2.meta_value = %d 
	AND p.post_type = %s ";

	$lesson_key = 'lesson_id';
	$course_key = 'ld_course';
	$post_type = 'sfwd-topic';

	$prepared_query = $wpdb->prepare( $query, $lesson_key, $lesson_id, $course_key, $course_id, $post_type );
	return $wpdb->get_col($prepared_query);
}

/**
 * create a function to get lesson/topic quizzess
 */
function mld_get_lesson_topic_quizzess( $course_id, $lesson_id ) {

	global $wpdb;

	$course_meta_key = 'ld_course_'.$course_id;
	$course_meta_value = $course_id;
	$lesson_meta_key = 'lesson_id';
	$lesson_meta_value = $lesson_id;

	$query = $wpdb->prepare(
		"SELECT p.ID AS post_id
		FROM {$wpdb->posts} AS p
		JOIN {$wpdb->postmeta} AS pm1 ON p.ID = pm1.post_id AND pm1.meta_key = %s AND pm1.meta_value = %s
		JOIN {$wpdb->postmeta} pm2 ON p.ID = pm2.post_id AND pm2.meta_key = %s AND pm2.meta_value = %s
		WHERE p.post_type = 'sfwd-quiz'",
		$course_meta_key,
		$course_meta_value,
		$lesson_meta_key,
		$lesson_meta_value
	);

	return $wpdb->get_col($query);
}

/**
 * create a function to get course quizzess
 */
function mld_get_course_quizzess( $course_id ) {

	global $wpdb;

	$query = "SELECT DISTINCT pm.post_id
	FROM {$wpdb->postmeta} AS pm
	JOIN {$wpdb->posts} AS p ON pm.post_id = p.ID
	WHERE pm.meta_key = 'course_id' 
	AND pm.meta_value = %d 
	AND p.post_type = 'sfwd-quiz'";
	$prepared_query = $wpdb->prepare( $query, $course_id );
	return $wpdb->get_col( $prepared_query );
}
/**
 * create a function to get course lesson
 */
function mld_get_course_lessons( $course_id ) {

	global $wpdb;

	$query = "SELECT pm.post_id
	FROM {$wpdb->postmeta} AS pm
	JOIN {$wpdb->posts} AS p ON pm.post_id = p.ID
	WHERE pm.meta_key = 'course_id' 
	AND pm.meta_value = %d 
	AND p.post_type = 'sfwd-lessons'";

	$prepared_query = $wpdb->prepare( $query, $course_id );
	return $wpdb->get_col( $prepared_query );
}
/**
 * create a function to get user courses
 */
function mld_get_user_courses( $user_id ) {

	global $wpdb;

	$query = "SELECT DISTINCT pm2.post_id AS id
	FROM {$wpdb->prefix}usermeta AS pm1
	JOIN {$wpdb->prefix}postmeta AS pm2
	ON pm2.meta_key = CONCAT('learndash_group_enrolled_', pm1.meta_value)
	WHERE pm1.user_id = $user_id
	AND pm1.meta_key LIKE 'learndash_group_users_%'

	UNION

	SELECT DISTINCT course_id AS id
	FROM {$wpdb->prefix}learndash_user_activity
	WHERE user_id = $user_id
	AND activity_type = 'access'";
	return $wpdb->get_results( $query );
}

/**
 * create a function to get group users
 */
// function mld_get_group_users( $group_id ) {

// 	global $wpdb;

// 	$meta_key = 'learndash_group_users_'.$group_id;

// 	$query = $wpdb->prepare(
// 		"SELECT user_id 
// 		FROM {$wpdb->usermeta} 
// 		WHERE meta_key = %s",
// 		$meta_key
// 	);
	
//  	return $wpdb->get_col($query);
//  }

/**
 * create a function to get user capability array
 */
function mld_user_capability( $user_id ) {

	global $wpdb;

	$user_capability = get_user_meta( $user_id, $wpdb->prefix.'capabilities', true );
	
	return array_keys( $user_capability );
}

/**
 * create a function to get user email
 */
function mld_get_user_email( $user_id ) {

	global $wpdb;

	$user_email = $wpdb->get_var($wpdb->prepare(
		"SELECT user_email FROM {$wpdb->users} WHERE ID = %d",
		$user_id
	) );

	return $user_email;
}

/**
 * Gets the list of groups associated with the course.
 */
function mld_get_course_groups( $course_id ) {

	global $wpdb;

	$meta_key = 'learndash_group_enrolled_%';

	$query = $wpdb->prepare(
	    "SELECT meta_key 
	    FROM {$wpdb->postmeta} 
	    WHERE post_id = %d
	    AND meta_key LIKE %s",
	    $course_id, $meta_key
	);
	
	$group_ids_array = $wpdb->get_col($query);

	$group_ids = array_map( function( $value ) {
		return str_replace( 'learndash_group_enrolled_', '', $value );
	}, $group_ids_array );

	return $group_ids;
}

/**
 * Gets the list of group leaders for the given group ID.
 */
function mld_get_group_leaders( $group_id ) {

	global $wpdb;

	$query = "SELECT user_id 
	FROM {$wpdb->usermeta} 
	WHERE meta_key = 'learndash_group_leaders_".$group_id."'";
	$user_ids = $wpdb->get_col( $query );
	$group_leaders = [];

	if ( ! empty( $user_ids ) ) {
		foreach ( $user_ids as $user_id ) {

			$meta_key = $wpdb->prefix . 'capabilities';
			$capabilities = get_user_meta( $user_id, $meta_key, true );
			if( ! empty( $capabilities ) ) {
				
				$capabilities_keys = array_keys( $capabilities );
				// var_dump( $capabilities_keys );
				if( $capabilities_keys ) {
					$capabilities_keys = array_values( $capabilities_keys );
					// var_dump( $capabilities_keys );
					if( in_array( 'group_leader', $capabilities_keys ) ) {
						$group_leaders[] = intval( $user_id );	
					}
				}
			}
		}
	}

	return $group_leaders;
}

/**
 * Gets the list of group IDs administered by the user.
 */
function mld_get_user_administrated_groups( $user_id ) {

	global $wpdb;

	$query = "SELECT meta_value 
	FROM {$wpdb->usermeta} 
	WHERE user_id = $user_id
	AND meta_key LIKE 'learndash_group_leaders_%'";
	return $wpdb->get_col( $query );
}

/**
 * get a users group ids
 */
function mld_get_user_groups( $user_id ) {
	
	global $wpdb;

	$query = "SELECT meta_value 
	FROM {$wpdb->usermeta} 
	WHERE user_id = $user_id
	AND meta_key LIKE 'learndash_group_users_%'";
	return $wpdb->get_col( $query );		
}
/**
 * Gets the list of enrolled courses for a group.
 */
function mld_get_group_courses( $group_id ) {

	global $wpdb;

	$query = "SELECT post_id 
	FROM {$wpdb->postmeta} 
	WHERE meta_key = 'learndash_group_enrolled_".$group_id."'";
	return $wpdb->get_col( $query );			
}

/**
 * create a function to get username
 */
function mld_get_username( $user_id ) {

	global $wpdb;

	$first_name = get_user_meta( $user_id, 'first_name', true );
	$last_name = get_user_meta( $user_id, 'last_name', true );

	if( $first_name && $last_name ) {
		return $first_name.' '.$last_name;
	} else {
		$query = "SELECT display_name 
		FROM {$wpdb->users} 
		WHERE ID = $user_id";
		$username_array = $wpdb->get_col( $query );
		return $username_array[0];
	}
}

/**
 * create a function to get groups for admin
 */
function mld_get_groups_for_admin() {

	global $wpdb;

	$query = $wpdb->prepare(
		"SELECT ID 
		FROM {$wpdb->posts} 
		WHERE post_type = %s 
		AND post_status = %s", 
		'groups', 
		'publish'
	);

	$post_id = $wpdb->get_col($query);
	return array_reverse( $post_id );
}

/**
 * create a function to get groups for group leader
 */
function mld_get_groups_for_leader( $user_id ) {

	global $wpdb;

	$query = $wpdb->prepare(
		"SELECT meta_value 
		FROM {$wpdb->usermeta} 
		WHERE user_id = %d 
		AND meta_key LIKE %s",
		$user_id,
		'learndash_group_leaders_%'
	);

	$group_ids = $wpdb->get_col($query);
	return array_reverse( $group_ids );
}
/**
 * create a function to category pdf files
 */
function mld_get_category_files( $category ) {

	$upload_dir = wp_get_upload_dir();
	$directory_path = $upload_dir['basedir'] . '/'.$category;
	$files = glob($directory_path . '/*');
	return $files;
}

/**
 * create a function to get user enrolled group
 */
function mld_get_user_enrolled_group( $user_id, $limit = 0 ) {

    global $wpdb;

    if( ! $user_id ) {
        return;
    }

    $group_ids = [];
    $post_type = 'exms-groups';
    
    if( current_user_can( 'administrator' ) ) {

        $group_ids = get_posts( array(
            'post_type'      => $post_type,
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'fields'         => 'ids',
            'posts_per_page' => ( $limit > 0 ) ? absint( $limit ) : -1,
        ) );
    } else {

        $table_name = $wpdb->prefix . 'exms_user_enrollments';
        
        $query = $wpdb->prepare(
            "SELECT DISTINCT post_id
            FROM {$table_name}
            WHERE user_id = %d
            AND post_type = %s",
            $user_id,
            $post_type
        );

        // ✅ limit apply only if provided
        if ( $limit > 0 ) {
            $query .= $wpdb->prepare( " LIMIT %d", absint( $limit ) );
        }

        $group_ids = $wpdb->get_col( $query );
    }

    return $group_ids;
}

function mld_get_group_users( $group_id ) {

    global $wpdb;

    $table_name = $wpdb->prefix . 'exms_user_enrollments';

    $user_ids = $wpdb->get_col(
        $wpdb->prepare(
            "SELECT user_id 
            FROM $table_name 
            WHERE type = %s 
            AND post_type = %s 
            AND post_id = %d",
            'student',
            'exms-groups',
            $group_id
        )
    );

    return $user_ids;
}
?>