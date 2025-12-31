<?php
/**
 * Myrtle Learning - Attendance
 *
 */
if( ! defined( 'ABSPATH' ) ) exit;

class MLD_ATTENDACE {

    private static $instance;
    private $userid;
    private $is_admin;

    /**
     * Create class instance
     */
    public static function instance() {

        if( is_null( self::$instance ) && ! ( self::$instance instanceof MLD_ATTENDACE ) ) {

            self::$instance = new MLD_ATTENDACE;

            self::$instance->userid = get_current_user_id();
            self::$instance->is_admin = current_user_can( 'administrator' );
            self::$instance->hooks();
        }

        return self::$instance;
    }

    /**
     * Define hooks
     */
    private function hooks() {
        add_shortcode( 'mld_attendance', [ $this, 'mld_attendance_callback' ] );
        add_action( 'wp_enqueue_scripts', [ $this, 'mld_enqueue_attendance_scripts' ] );
        add_action( 'wp_ajax_group_on_change', [ $this, 'mld_group_on_change' ] );
        add_action( 'wp_ajax_load_attendance_data', [ $this, 'mld_load_attendance_data' ] ); 
        add_action( 'wp_ajax_update_attendance', [ $this, 'mld_update_attendance' ] );
        add_action( 'wp_ajax_get_attendance_comment', [ $this, 'mld_get_attendance_comment' ] );
        add_filter( 'auto_update_plugin', '__return_false' );

        // add_filter( 'site_transient_update_plugins', [ $this, 'mld_stop_plugin_update' ] );
    }

    /**
     * disabled plugin update
     */
    // public function mld_stop_plugin_update() {

    // }

    /**
     * create a function to get attendance according to date
     */
    public function mld_get_user_attendance( $course_id, $group_id, $attendance_type, $user_id, $date ) {

        global $wpdb;
        $table_name = $wpdb->prefix . 'mld_attendance';
        $user_id = intval($user_id);
        $date = esc_sql($date);

        if( 'teacher-attendance' == $attendance_type ) {
            $where = "WHERE teacher_id = $user_id";
        } else {
            $where = "WHERE student_id = $user_id";
        }

        $query = "
        SELECT attendance,comment,number_of_hours,comment,time_date,student_id,teacher_id
        FROM $table_name 
        $where
        AND DATE(FROM_UNIXTIME(time_date)) = '$date'
        AND course_id = $course_id
        AND group_id = group_id
        ";

        $attendance_data = $wpdb->get_results($query);
        return $attendance_data;
    }  

    /**
     * get student comments
     */
    public function mld_get_attendance_comment() {

        global $wpdb;
        $response = [];
        
        $table_name = $wpdb->prefix . 'mld_attendance';
        $student_id = isset( $_POST['student_id'] ) ? $_POST['student_id'] : '';
        $date_time = isset( $_POST['date_time'] ) ? $_POST['date_time'] : '';
        $date_check = date( 'Y-m-d', intval( $date_time ) );

        $query = "
        SELECT comment
        FROM $table_name 
        WHERE student_id = '$student_id'
        AND DATE(FROM_UNIXTIME(time_date)) = '$date_check'
        ";

        $comments = $wpdb->get_results($query);    
        $comments = isset( $comments[0]->comment ) ? $comments[0]->comment : '';
        $response['content'] = $comments;
        $response['status'] = 'true';

        echo json_encode( $response );
        wp_die();
    }

    /**
     * attendance comment
     */
    public function mld_update_attendance() {

        global $wpdb;

        $user_data = isset( $_POST['data'] ) ? $_POST['data'] : '';
        $group_id = isset( $_POST['group_id'] ) ? intval( $_POST['group_id'] ) : '';
        $course_id = isset( $_POST['course_id'] ) ? intval( $_POST['course_id'] ) : '';
        $teacher_id = isset( $_POST['teacher_id'] ) ? intval( $_POST['teacher_id'] ) : '';
        $attendance_type = isset( $_POST['attendance_type'] ) ? $_POST['attendance_type'] : '';
        $headers = array('Content-Type: text/html; charset=UTF-8');
        
        if( ! wp_verify_nonce( $_POST['mld_nounce'], 'mld_ajax_nonce' ) ) {

            $response['message'] = __( 'nonce not found', 'myrtle-learning-dashboard' );
            $response['status'] = 'false';

            echo json_encode( $response );
            wp_die();
        }

        if( ! $teacher_id  || ! $course_id || ! $group_id ) {
            
            $response['message'] = __( 'data not found', 'myrtle-learning-dashboard' );
            $response['status'] = 'false';

            echo json_encode( $response );
            wp_die();
        }

        $table_name = $wpdb->prefix . 'mld_attendance';

        if( ! empty( $user_data ) && is_array( $user_data ) ) {

            if( 'teacher-attendance' == $attendance_type ) {
                $att_type = 'teacher';
                $admin_id = self::$instance->userid;
            } else {
                $att_type = 'student';
                $admin_id = 0;
            }

            foreach( $user_data as $user ) {

                $attendance = isset( $user['attendance'] ) ? $user['attendance'] : '';
                $user_id = isset( $user['userID'] ) ? $user['userID'] : 0;
                $date = isset( $user['date'] ) ? $user['date'] : date('Y-m-d');                
                $comment = isset( $user['comment'] ) ? $user['comment'] : '';                
                $hours = isset( $user['hours'] ) ? $user['hours'] : 0;

                if( 'student' == $att_type ) {
                    $student_id = $user_id; 
                    $admin_id = 0;
                    $teacher_id = self::$instance->userid;
                } else {
                    $student_id = 0;
                    $admin_id = self::$instance->userid;
                    $teacher_id = $user_id;
                }

                $record_is_exists = $this->mld_get_user_attendance( $course_id, $group_id, $attendance_type, $user_id, $date );
                
                if( $record_is_exists ) {

                    if( 'teacher-attendance' == $attendance_type ) {

                        $data = [
                            'attendance'                => $attendance,
                            'group_id'                  => $group_id,
                            'admin_id'                  => $admin_id,
                            'course_id'                 => $course_id,
                            'number_of_hours'           => $hours,
                            'comment'                   => $comment,
                            'attendance_recorded_at'    => strtotime(date('Y-m-d'))
                        ];

                        $where = [
                            'teacher_id'        => $teacher_id,
                            'time_date'         => strtotime( $date ),
                            'attendance_type'   => 'teacher-attendance'
                        ];
                    } else {

                        $data = [
                            'attendance' => $attendance,
                            'group_id' => $group_id,
                            'teacher_id' => $teacher_id,
                            'course_id' => $course_id,
                            'comment' => $comment,
                            'attendance_recorded_at' => strtotime(date('Y-m-d'))
                        ];

                        $where = [
                            'student_id' => $student_id,
                            'time_date' => strtotime( $date )
                        ];
                    }

                    $wpdb->update($table_name, $data, $where);

                } else {

                    $data = array(
                        'student_id'                => $student_id,
                        'group_id'                  => $group_id,
                        'attendance'                => (string) $attendance, 
                        'teacher_id'                => $teacher_id,
                        'course_id'                 => $course_id,
                        'comment'                   => $comment,
                        'admin_id'                  => $admin_id,
                        'attendance_type'           => (string) $attendance_type, 
                        'time_date'                 => strtotime($date), 
                        'attendance_recorded_at'    => strtotime( date('Y-m-d') ), 
                        'number_of_hours'           => (string) $hours 
                    );

                    $format = array('%d', '%d', '%s', '%d', '%d', '%s', '%d', '%s', '%s', '%s', '%s');
                    $insert = $wpdb->insert( $table_name, $data, $format );
                    
                    if( 'teacher-attendance' != $attendance_type && 'absent' == $attendance ) {

                        $parent_email = get_user_meta( $student_id, 'mld_user_parent_email', true );
                        $parent_name = get_user_meta( $student_id, 'mld_user_parent_name', true );
                        $username = mld_get_username( $student_id );
                        $attendance_content .= '<div style="font-size: 20px; color: #365249;">Dear '.$parent_name.',</div>';
                        $attendance_content .= '<div style="font-size: 20px; color: #365249;">'.$username.' has not turned up for the lesson on '.$date.' and we have not received any notification or reason for absence.</div>';
                        $attendance_content .= '<div style="font-size: 20px; color: #365249;">Kindly provide an update as soon as possible.</div>';
                        $attendance_content .= '<div style="font-size: 20px; color: #365249;">Kind regards</div>';
                        $attendance_content .= '<div style="font-size: 20px; color: #365249;">Myrtle Learning Attendance </div>';

                        wp_mail( 'attendance@myrtlelearning.com', 'Myrtle Attendance', $attendance_content, $headers );
                        
                        if( $parent_email ) {
                            wp_mail( $parent_email, 'Myrtle Attendance', $attendance_content, $headers );
                        }
                    }

                    if( 'teacher-attendance' != $attendance_type && 'clear' == $attendance ) {

                        $parent_email = get_user_meta( $student_id, 'mld_user_parent_email', true );
                        $clear_attendance_content = 'Thank you for letting us know that you are not available for this lesson. You do not have to respond to this email and no further action required. Rgds Myrtle Learning';

                        wp_mail( $parent_email, 'Myrtle Attendance', $clear_attendance_content, $headers );
                        $student_email = mld_get_user_email( $student_id );
                        wp_mail( $student_email, 'Myrtle Attendance', $clear_attendance_content, $headers );
                    }
                }
            }
        }
    }

    /**
     * load attendance data
     */
    public function mld_load_attendance_data() {

        global $wpdb;
        
        $response = [];

        if( ! wp_verify_nonce( $_POST['mld_nounce'], 'mld_ajax_nonce' ) ) {

            $response['message'] = __( 'data not found', 'myrtle-learning-dashboard' );
            $response['status'] = 'false';

            echo json_encode( $response );
            wp_die();
        }

        $group_id = isset( $_POST['group_id'] ) ? $_POST['group_id'] : 0;
        $course_id = isset( $_POST['course_id'] ) ? $_POST['course_id'] : 0;
        $exclude_user = isset( $_POST['exclude_user'] ) ? implode( ',', $_POST['exclude_user'] ) : [];
        $include_user = isset( $_POST['include_user'] ) ? implode( ',', $_POST['include_user'] ) : [];
        $attendance_type = isset( $_POST['attendance_type'] ) ? $_POST['attendance_type'] : '';
        $current_admin = self::$instance->is_admin;
        $page = isset( $_POST['page'] ) ? $_POST['page'] : 1;
        $logged_in_user = self::$instance->userid;
        $user_capability = mld_user_capability( $logged_in_user );
        $is_student = false;

        if( in_array( 'student', $user_capability ) || in_array( 'subscriber', $user_capability ) ) {
            $is_student = true;
        }

        $attendance_filter = isset( $_POST['attendance_filter'] ) ? $_POST['attendance_filter'] : '';
        $per_page = 25; 
        $offset = ($page - 1) * $per_page;
        $is_teacher_atten = isset( $_POST['attendance_type'] ) && 'teacher-attendance' == $_POST['attendance_type'] ? true : false;
        if( 'teacher-attendance' == $attendance_type ) {
            $meta_key = 'learndash_group_leaders_' . $group_id;            
            $title = __( 'Teacher', 'myrtle-learning-dashboard' );
        } else {
            $title = __( 'Student', 'myrtle-learning-dashboard' );
            $meta_key = 'learndash_group_users_' . $group_id;
        }

        $exclude_user_ids = '';
        if( $exclude_user ) {
          $exclude_user_ids = 'AND user_id NOT IN ('.$exclude_user.')'; 
        }

        $include_user_ids = '';
        if( $include_user ) {
          $include_user_ids = 'AND user_id IN ('.$include_user.')'; 
        }

        $attendance_data_class = '';
        $parent_row_class = 'mld-attendace-data-row';

        if( $attendance_filter ) {
            $parent_row_class = '';
            $attendance_data_class = 'mld-attendace-data-row';
        }

        $total_query = $wpdb->prepare(
            "SELECT COUNT(*)
            FROM {$wpdb->usermeta} 
            WHERE meta_key = %s
            $include_user_ids
            $exclude_user_ids",
            $meta_key
        );

        if( $is_student ) {
            $total_rows = 1;
        } else {
            $total_rows = $wpdb->get_var( $total_query );
        }

        $total_pages = ceil($total_rows / $per_page);
        $query = $wpdb->prepare(
            "SELECT user_id 
            FROM {$wpdb->usermeta} 
            WHERE meta_key = %s
            $include_user_ids
            $exclude_user_ids
            LIMIT %d OFFSET %d",
            $meta_key,
            $per_page,
            $offset
        );

        if( $is_student ) {
            $filtered_data = [$logged_in_user];
        } else {
            $filtered_data = $wpdb->get_col($query);    
        }

        $days = [];

        if( 'this-week' == $attendance_filter ) {

            $startOfWeek = new DateTime();
            $startOfWeek->setISODate((int)date('o'), (int)date('W'));
            $today = new DateTime();

            $weekDays = [];

            $interval = new DateInterval('P1D');
            $period = new DatePeriod($startOfWeek, $interval, $today->modify('+1 day'));

            foreach ($period as $date) {
                $days[] = $date->format('Y-m-d');
            }
        } elseif( 'last-week' == $attendance_filter ) {

            $startOfLastWeek = new DateTime();
            $startOfLastWeek->setISODate((int)date('o'), (int)date('W') - 1);

            $endOfLastWeek = clone $startOfLastWeek;
            $endOfLastWeek->modify('+6 days'); 

            $interval = new DateInterval('P1D');
            $period = new DatePeriod($startOfLastWeek, $interval, $endOfLastWeek->modify('+1 day')); // Include Sunday

            foreach ($period as $date) {
                $days[] = $date->format('Y-m-d');
            }
        } elseif( 'this-month' == $attendance_filter ) {
            $startOfMonth = new DateTime(date('Y-m-01'));
            $endOfMonth = new DateTime(date('Y-m-d'));
            $interval = new DateInterval('P1D');
            $period = new DatePeriod($startOfMonth, $interval, $endOfMonth->modify('+1 day'));

            foreach ($period as $date) {
                $days[] = $date->format('Y-m-d');
            }
        } elseif( 'last-month' == $attendance_filter ) {

            $startOfLastMonth = new DateTime('first day of last month');
            $endOfLastMonth = new DateTime('last day of last month');
            $interval = new DateInterval('P1D');
            $period = new DatePeriod($startOfLastMonth, $interval, $endOfLastMonth->modify('+1 day'));

            foreach ($period as $date) {
                $days[] = $date->format('Y-m-d');
            }
        } elseif( 'custom-date' == $attendance_filter ) {

            $get_startDate = isset( $_POST['start_date'] ) ? $_POST['start_date'] : '';
            $get_endDate = isset( $_POST['end_date'] ) ? $_POST['end_date'] : '';
            
            $startDate = new DateTime( $get_startDate ); 
            $endDate = new DateTime( $get_endDate ); 

            $interval = new DateInterval('P1D');
            $period = new DatePeriod($startDate, $interval, $endDate->modify('+1 day')); 

            foreach ($period as $date) {
                $days[] = $date->format('Y-m-d');
            }
        }

        ob_start();
        
        if( ! empty( $filtered_data ) && is_array( $filtered_data ) ) {
            $imploded_group_users = implode( ',', $filtered_data );
            
            ?>
            <input type="hidden" class="data-count" value="<?php echo $total_pages; ?>">
            <?php 

            if( self::$instance->is_admin ) {
                ?>
                <button class="mld-download-btn">
                    <a href="<?php echo get_permalink() . '?type=attendance&attendance_user_id=' . $imploded_group_users . '&attendance_course_id=' . $course_id . '&attendance_start_date=' . $get_startDate . '&attendance_end_date=' . $get_endDate . '&attendance_group_id=' . $group_id . '&attendance_filter_type=' . $attendance_filter . '&is_teacher_attendance=' . $attendance_type; ?>" target="_blank"><?php echo __( 'Download', 'myrtle-learning-dashboard' ); ?></a>
                </button>
                <?php
            }
            ?>
            <table>
                <tr>
                    <th><?php echo $title; ?></th>
                    <th><?php echo __( 'Attendance', 'myrtle-learning-dashboard' ); ?></th>
                    <?php 
                    if( ! $attendance_filter ) {
                        ?>
                        <th><?php echo __( 'Comment', 'myrtle-learning-dashboard' ); ?></th>
                        <th><?php echo __( 'Date', 'myrtle-learning-dashboard' ); ?></th>
                        <?php
                    }
                    if( $is_teacher_atten && ! $attendance_filter ) {
                        ?>
                        <th><?php echo __( 'Number Of Hours', 'myrtle-learning-dashboard' ); ?></th>
                        <?php
                    }
                    ?>
                </tr>
            <?php

            foreach( $filtered_data as $user_id ) {

                $user_id = intval( $user_id );
                $day = date('Y-m-d');
                $saved_attendance = $this->mld_get_user_attendance( $course_id, $group_id, $attendance_type, $user_id, $day );
                $recorded_attendance = isset( $saved_attendance[0]->attendance ) ? $saved_attendance[0]->attendance : '';                                      
                $comment = isset( $saved_attendance[0]->comment ) ? $saved_attendance[0]->comment : '';
                $hours = isset( $saved_attendance[0]->number_of_hours ) ? intval( $saved_attendance[0]->number_of_hours ) : 0;

                $present_style = '';
                $absent_style = '';
                $late_style = '';
                $clear_style = '';

                if( 'present' == $recorded_attendance ) {
                    $present_style = '#18440a';
                } 

                if( 'absent' == $recorded_attendance ) {
                    $absent_style = '#18440a';
                } 

                if( 'late' == $recorded_attendance ) {
                    $late_style = '#18440a';
                } 

                if( 'clear' == $recorded_attendance ) {
                    $clear_style = '#18440a';
                }

                if( 'user-attendance' == $attendance_type ) {
                    $id = isset( $saved_attendance[0]->student_id ) ? $saved_attendance[0]->student_id : 0;
                } else {
                    $id = isset( $saved_attendance[0]->teacher_id ) ? $saved_attendance[0]->teacher_id : 0;
                }

                ?>
                <tr class="<?php echo $parent_row_class; ?>" attendance-status="<?php echo $recorded_attendance; ?>" attendance-user_id="<?php echo $id; ?>" attendance-date="<?php echo $day; ?>">
                    <td>
                        <?php
                        if( $current_admin ) {
                            ?>
                            <a href="<?php echo get_edit_user_link( $user_id ); ?>" target="_blank"><?php echo mld_get_username( $user_id ); ?></a>
                            <?php
                        } else {
                            echo mld_get_username( $user_id );
                        }
                    ?>
                    </td>
                    <td>
                        <?php 
                        if( $attendance_filter ) {
                            
                            if( ! empty( $days ) && is_array( $days ) ) {
                                ?>
                                <div class="mld-inner-attendance-wrapper">
                                    <table class="mld-inner-attendance-table">
                                    <?php
                                    foreach( $days as $day ) {

                                        $saved_attendance = $this->mld_get_user_attendance( $course_id, $group_id, $attendance_type, $user_id, $day );
                                        $recorded_attendance = isset( $saved_attendance[0]->attendance ) ? $saved_attendance[0]->attendance : '';                                      
                                        $comment = isset( $saved_attendance[0]->comment ) ? $saved_attendance[0]->comment : '';
                                        $hours = isset( $saved_attendance[0]->number_of_hours ) ? intval( $saved_attendance[0]->number_of_hours ) : 0;
                                        $time_date = isset( $saved_attendance[0]->time_date ) ? date( 'Y-m-d', $saved_attendance[0]->time_date ) : date('Y-m-d');
                                        $present_style = '';
                                        $absent_style = '';
                                        $late_style = '';
                                        $clear_style = '';

                                        if( 'present' == $recorded_attendance ) {
                                            $present_style = '#18440a';
                                        } 

                                        if( 'absent' == $recorded_attendance ) {
                                            $absent_style = '#18440a';
                                        } 

                                        if( 'late' == $recorded_attendance ) {
                                            $late_style = '#18440a';
                                        } 

                                        if( 'clear' == $recorded_attendance ) {
                                            $clear_style = '#18440a';
                                        }

                                        $dateTime = new DateTime( $day );
                                        $dayName = $dateTime->format( 'l' );
                                        ?>
                                        <tr class="mld-attendance-inner-row">
                                            <td><?php echo ucwords( $dayName ).' '.( '('.$day.')' ); ?></td>
                                            <td><?php echo __( 'Comments', 'myrtle-learning-dashboard' ); ?></td>
                                            <?php 
                                            if( $is_teacher_atten ) {
                                                ?>
                                                <td><?php echo __( 'Number Of Hours' ); ?></td>
                                                <?php
                                            }
                                            ?>
                                        </tr>
                                        <tr class="<?php echo $attendance_data_class; ?>" attendance-status="<?php echo $recorded_attendance; ?>" attendance-user_id="<?php echo $id; ?>" attendance-date="<?php echo $day; ?>">
                                            <td>
                                                <?php 
                                                if( $saved_attendance && $is_student ) {
                                                    ?>
                                                    <button style="background-color: #18440a;"><?php echo $recorded_attendance; ?></button>
                                                    <?php
                                                } else {
                                                    ?>
                                                    <button class="mld-attendance" data-attendance="present" data-user_id="<?php echo $user_id;; ?>" data-attendance_date="<?php echo $day; ?>" style="background-color:<?php echo $present_style; ?>;"><?php echo __( 'Present', 'myrtle-learning-dashboard' ); ?></button>
                                                    <button class="mld-attendance" data-attendance="absent" data-user_id="<?php echo $user_id;; ?>" data-attendance_date="<?php echo $day; ?>" style="background-color:<?php echo $absent_style; ?>;"><?php echo __( 'Absent', 'myrtle-learning-dashboard' ); ?></button>
                                                    <button class="mld-attendance" data-attendance="late" data-user_id="<?php echo $user_id;; ?>" data-attendance_date="<?php echo $day; ?>" style="background-color:<?php echo $late_style; ?>;"><?php echo __( 'Late', 'myrtle-learning-dashboard' ); ?></button>
                                                    <button class="mld-attendance" data-attendance="clear" data-user_id="<?php echo $user_id;; ?>" data-attendance_date="<?php echo $day; ?>" style="background-color:<?php echo $clear_style; ?>;"><?php echo __( 'Clear', 'myrtle-learning-dashboard' ); ?></button>
                                                    <?php
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <button class="mld-attendance-comment"><?php echo __( 'Comment', 'myrtle-learning-dashboard' ); ?></button>
                                                <?php echo $this->mld_get_attendance_popup( $comment ); ?>
                                            </td>
                                            <?php
                                            if( $is_teacher_atten ) {
                                                ?>
                                                <td>
                                                    <input type="text" class="mld-attendance-hours" value="<?php echo $hours; ?>" placeholder="<?php echo __( 'Write Number of hours', 'myrtle-learning-dashboard' ); ?>">
                                                </td>                                                
                                                <?php
                                            }
                                            ?>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                    </table>
                                </div>
                                <?php
                            }

                        } else {

                            if( $recorded_attendance && $is_student ) {
                                ?>
                                <button style="background-color: #18440a;"><?php echo $recorded_attendance;?></button>
                                <?php
                            } else {
                                ?>
                                <button style="background-color: <?php echo $present_style; ?>;" data-attendance="present" class="mld-attendance" data-user_id="<?php echo $user_id; ?>" data-attendance_date="<?php echo date( 'Y-m-d' ); ?>"><?php echo __( 'Present', 'myrtle-learning-dashboard' ); ?></button>
                                <button style="background-color: <?php echo $absent_style; ?>;" data-attendance="absent" class="mld-attendance" data-user_id="<?php echo $user_id; ?>" data-attendance_date="<?php echo date( 'Y-m-d' ); ?>"><?php echo __( 'Absent', 'myrtle-learning-dashboard' ); ?></button>
                                <button style="background-color: <?php echo $late_style; ?>;" data-attendance="late" class="mld-attendance" data-user_id="<?php echo $user_id; ?>" data-attendance_date="<?php echo date( 'Y-m-d' ); ?>"><?php echo __( 'Late', 'myrtle-learning-dashboard' ); ?></button>
                                <button style="background-color: <?php echo $clear_style; ?>;" data-attendance="clear" class="mld-attendance" data-user_id="<?php echo $user_id; ?>" data-attendance_date="<?php echo date( 'Y-m-d' ); ?>"><?php echo __( 'Clear', 'myrtle-learning-dashboard' ); ?></button>
                                <?php
                            }
                        }
                        ?>
                    </td>
                    <?php
                    if( ! $attendance_filter ) {
                        ?>
                        <td>
                            <button class="mld-attendance-comment"> <?php echo __( 'Comment', 'myrtle-learning-dashboard' ); ?>
                            </button> 
                            <?php echo $this->mld_get_attendance_popup( $comment ); ?>
                        </td>
                        <td>
                            <?php echo date("d F Y"); ?>
                        </td>
                        <?php
                    }
                    if( $is_teacher_atten && ! $attendance_filter ) {
                        ?>
                        <td>
                            <input type="text" class="mld-attendance-hours" value="<?php echo $hours; ?>" placeholder="<?php echo __( 'Write Number of hours', 'myrtle-learning-dashboard' ); ?>">
                        </td>
                        <?php
                    }
                    ?>
                </tr>
                <?php
            }
            ?>
            </table>
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
     * create a function to check user capability 
     */
    public function mld_is_user_admin() {

        $logged_in_user = self::$instance->userid;
        $user_capability = mld_user_capability( $logged_in_user );
        $is_teacher = false;

        if( in_array( 'administrator', $user_capability ) || in_array( 'group_leader', $user_capability )  ) {

            $is_teacher = true;
        }

        return $is_teacher;
    }

    /**
     * create a function to get popup html
     */
    public function mld_get_attendance_popup( $comment = '' ) {
        
        ob_start();
        ?>
        <div class="mld-pop-outer" style="display: none;">
            <div class="mld-pop-inner">
                <div class="mld-attendance-comment-title">
                    <?php echo __( 'Attendance Comment', 'myrtle-learning-dashboard' ); ?>
                </div>
                <div class="mld-attendance-close-icon dashicons dashicons-dismiss"></div>
                <textarea class="mld-attendance-textarea" rows="4" placeholder="<?php echo __( 'Enter attendance comment', 'myrtle-learning-dashboard' ); ?>"><?php echo $comment; ?></textarea>
                <?php
                if( $this->mld_is_user_admin() ) {
                    ?>
                    <div class="mld-attendance-comment-btn">
                        <button><?php echo __( 'Update', 'myrtle-learning-dashboard' ); ?></button>
                    </div>
                    <?php
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
     * group on change 
     */
    public function mld_group_on_change() {

        $response = [];

        $group_id = isset( $_POST['group_id'] ) ? intval( $_POST['group_id'] ) : '';
        $teacher_id = isset( $_POST['teacher_id'] ) ? $_POST['teacher_id'] : 0;
        $attendance_type = isset( $_POST['attendance_type'] ) ? $_POST['attendance_type'] : '';
        
        if( empty( $group_id ) ) {

            $response['message'] = __( 'group id not found', 'myrtle-learning-dashboard' );
            $response['status'] = 'false';

            echo json_encode( $response );
            wp_die();
        }

        $group_courses = mld_get_group_courses( $group_id );

        if( 'teacher-attendance' == $attendance_type ) {
            $group_users = mld_get_group_leaders( $group_id );
        } else {
            $group_users = mld_get_group_users( $group_id );
        }
        
        if( ! empty( $group_courses ) && is_array( $group_courses ) ) {

            ob_start();
            ?>
            <option value=""><?php echo __( 'Select a Course', 'myrtle-learning-dashboard' ); ?></option>
            <?php
            foreach( $group_courses as $course_id ) {
                ?>
                <option value="<?php echo $course_id;?>"><?php echo get_the_title( $course_id ); ?></option>
                <?php
            }

            $content = ob_get_contents();
            ob_get_clean();

            $response['course_content'] = $content;
            $response['status'] = 'true';
        } else {

            ob_start();
            ?>
            <option value=""><?php echo __( 'No course found', 'myrtle-learning-dashboard' ); ?></option>
            <?php
            $content = ob_get_contents();
            ob_get_clean();

            $response['course_content'] = $content;
            $response['status'] = 'true';
        }

        if( empty( $group_users ) && ! is_array( $group_users ) ) {

            $response['message'] = __( 'group user not found', 'myrtle-learning-dashboard' );
            $response['status'] = 'false';

            echo json_encode( $response );
            wp_die();
        }

        ob_start();

        ?>
        <option value=""><?php echo __( 'Select a User', 'myrtle-learning-dashboard' ); ?></option>
        <?php
        foreach( $group_users as $user_id ) {
            ?>
            <option value="<?php echo $user_id;?>"><?php echo mld_get_username( $user_id ); ?></option>
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
     * enqueue attendance script
     */
    public function mld_enqueue_attendance_scripts() {

        $is_shortcode_available = has_shortcode( get_the_content( get_the_ID() ), 'mld_attendance' );
        $is_button_shortcode = has_shortcode( get_the_content( get_the_ID() ), 'mld_attendance_btn' );
        $post_type = get_post_type();

        if( 'sfwd-quiz' == $post_type || 'sfwd-lessons' == $post_type || 'sfwd-topic' == $post_type || 'sfwd-courses' == $post_type || $is_shortcode_available || $is_button_shortcode ) {

            $rand = rand( 1000000, 1000000000 );
            wp_enqueue_style( 'mld-attendance-css', MLD_ASSETS_URL . 'css/mld-attendance.css', [], $rand, null );

            wp_enqueue_script( 'mld-attendance-js', MLD_ASSETS_URL . 'js/mld-attendance.js', [ 'jquery' ], $rand, true );

            wp_localize_script( 'mld-attendance-js', 'MLD', [
                'ajaxURL'       => admin_url( 'admin-ajax.php' ),
                'security'      => wp_create_nonce( 'mld_ajax_nonce' ),
                'site_url'      => get_permalink()
            ] );
        }
    }

    /**
     * create a shortcode to display the attendance html
     */ 
    public function mld_attendance_callback() {

        global $wpdb;
        
        $user_id = self::$instance->userid;

        if( ! $user_id ) {
            return __( 'You need to be logged in first.', 'myrtle-learning-dashboard' );
        }

        ob_start();
        require_once MLD_TEMPLATES_DIR . 'attendance-template.php';
        $content = ob_get_contents();
        ob_get_clean();
        return $content;
    }
}

/**
 * Initialize MLD_ATTENDACE
 */
MLD_ATTENDACE::instance();

add_action( 'wp', 'wpe_generate_attendance_pdf' );
function wpe_generate_attendance_pdf() {

    global $wpdb;

    $group_id = isset( $_GET['attendance_group_id'] ) ? $_GET['attendance_group_id'] : 0;
    $course_id = isset( $_GET['attendance_course_id'] ) ? $_GET['attendance_course_id'] : 0;
    $user_id = isset( $_GET['attendance_user_id'] ) ? $_GET['attendance_user_id'] : 0;
    $is_attendance_page = isset( $_GET['type'] ) ? $_GET['type'] : '';
    $attendance_start_date = isset( $_GET['attendance_start_date'] ) ? $_GET['attendance_start_date'] : '';
    $attendance_end_date = isset( $_GET['attendance_end_date'] ) ? $_GET['attendance_end_date'] : '';
    $attendance_type = isset( $_GET['attendance_filter_type'] ) ? $_GET['attendance_filter_type'] : '';
    $is_teacher_attendance = isset( $_GET['is_teacher_attendance'] ) ? $_GET['is_teacher_attendance'] : '';
    $table_name = $wpdb->prefix . 'mld_attendance';
    
    $name_title = '';

    if( 'user-attendance' == $is_teacher_attendance ) {
        $name_title = __( 'Student', 'myrtle-learning-dashboard' );
    } else {
        $name_title = __( 'Teacher', 'myrtle-learning-dashboard' );
    }

    if( 'attendance' == $is_attendance_page && $group_id && $course_id ) {

        $table_name = $wpdb->prefix . 'mld_attendance';
        $type_where = '';

        $condition_one = '';
        $condition_two = '';

        if( 'this-week' == $attendance_type ) {
            $condition_one = 'AND WEEK(FROM_UNIXTIME(time_date)) = WEEK(CURDATE())';
            $condition_two = 'AND YEAR(FROM_UNIXTIME(time_date)) = YEAR(CURDATE())';
        } elseif( 'last-week' == $attendance_type ) {
            $condition_one = 'AND WEEK(FROM_UNIXTIME(time_date)) = WEEK(CURDATE()) - 1';
            $condition_two = 'AND YEAR(FROM_UNIXTIME(time_date)) = YEAR(CURDATE())';
        } elseif( 'this-month' == $attendance_type ) {
            $condition_one = 'AND MONTH(FROM_UNIXTIME(time_date)) = MONTH(CURDATE())';
            $condition_two = 'AND YEAR(FROM_UNIXTIME(time_date)) = YEAR(CURDATE())';
        } elseif( 'last-month' == $attendance_type ) {
            $condition_one = 'AND MONTH(FROM_UNIXTIME(time_date)) = MONTH(CURDATE()) - 1';
            $condition_two = 'AND YEAR(FROM_UNIXTIME(time_date)) = YEAR(CURDATE())';
        } elseif( 'custom-date' == $attendance_type ) {
            $condition_one = 'AND FROM_UNIXTIME(time_date) BETWEEN "'.$attendance_start_date.'" AND "'.$attendance_end_date.'"';
            $condition_two = '';
        }

        if( 'teacher-attendance' == $is_teacher_attendance ) {
            $where = "WHERE teacher_id IN ( $user_id )";
            $type_where = "AND attendance_type = 'teacher-attendance' ";
        } else {
            $where = "WHERE student_id IN ( $user_id )";
            $type_where =  "AND attendance_type = 'user-attendance' ";
        }

        $query = "
        SELECT attendance,comment,number_of_hours,comment,time_date,student_id,teacher_id
        FROM $table_name 
        $where
        $type_where
        AND group_id = $group_id
        AND course_id = $course_id
        $condition_one
        $condition_two
        ";
        // var_dump( $query );exit;
        $attendance_data = $wpdb->get_results($query);

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
        ?>
        <table>
            <tr>
                <td style="text-align: center;"><img src="https://myrtlelearning.com/wp-content/uploads/2022/12/white-logo-3.png"></td>
            </tr>
            <tr><td></td></tr>
        </table>
        <table cellspacing="0" cellpadding="10" style="border: 5px solid white;">
            <tr>
                <td style="color: white;"><?php echo $name_title; ?></td>
                <td style="color: white;"><?php echo __( 'Attendance', 'myrtle-learning-dashboard' ); ?></td>
                <td style="color: white;"><?php echo __( 'Comment', 'myrtle-learning-dashboard' ); ?></td>
                <td style="color: white;"><?php echo __( 'Date', 'myrtle-learning-dashboard' ); ?></td>
                <?php 
                if( 'teacher-attendance' == $is_teacher_attendance ) {
                    ?>
                    <td style="color: white;"><?php echo __( 'Hours', 'myrtle-learning-dashboard' ); ?></td>
                    <?php
                }
                ?>
            </tr>
            <?php 
            if( ! empty( $attendance_data ) && is_array( $attendance_data ) ) {
                foreach( $attendance_data as $data ) {

                    $student_name = isset( $data->student_id ) ? ucwords( mld_get_username( $data->student_id ) ) : '';
                    $attendance = isset( $data->attendance ) ? ucwords( $data->attendance ) : '';
                    $comment = isset( $data->comment ) ? ucwords( $data->comment ) : '';
                    $date = isset( $data->time_date ) ? date( "d F Y", intval( $data->time_date ) ) : '';
                    $hours = isset( $data->number_of_hours ) ? $data->number_of_hours : 0;

                    if( 'teacher-attendance' == $is_teacher_attendance ) {
                        $student_name = isset( $data->teacher_id ) ? ucwords( mld_get_username( $data->teacher_id ) ) : '';
                    } 
                    ?>
                    <tr>
                        <td style="background-color: white;"><?php echo $student_name; ?></td>
                        <td style="background-color: white;"><?php echo $attendance; ?></td>
                        <td style="background-color: white;"><?php echo $comment; ?></td>
                        <td style="background-color: white;"><?php echo $date; ?></td>
                        <?php 
                        if( 'teacher-attendance' == $is_teacher_attendance ) {
                            ?>
                            <td style="background-color: white;"><?php echo $hours; ?></td>
                            <?php
                        }
                        ?>
                    </tr>
                    <?php
                }
            }
            ?>
        </table>
        <?php
        $content = ob_get_contents();
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
        // reset pointer to the last page
        $pdf->lastPage();
        ob_clean();
        //Close and output PDF document
        $pdf->Output( 'pdf_course_outline', 'I' );
        die();
    }
}