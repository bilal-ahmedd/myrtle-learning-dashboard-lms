<?php
/**
 * Plugin Name: Myrtle Learning Dashboard
 * Version: 1.0
 * Description: This plugin is made to design the custom dashboard for LearnDash teachers & students.
 * Author URI: ldninjas.com
 * Plugin URI: ldninjas.com
 * Text Domain: myrtle-learning-dashboard
 * License: GNU General Public License v2.0
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

if( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class MSP
 */
class Myrtle_Learning_Dashboard {

    const VERSION = '1.0';

    /**
     * @var self
     */
    private static $instance = null;

    /**
     * @since 1.0
     * @return $this
     */
    public static function instance() {

        if ( is_null( self::$instance ) && ! ( self::$instance instanceof Myrtle_Learning_Dashboard ) ) {
            self::$instance = new self;

            self::$instance->setup_constants();
            self::$instance->get_myrtle_page();
            self::$instance->includes();
        }

        return self::$instance;
    }

    /**
     * defining constants for plugin
     */
    public function setup_constants() {

        /**
         * Directory
         */
        define( 'MLD_DIR', plugin_dir_path ( __FILE__ ) );
        define( 'MLD_DIR_FILE', MLD_DIR . basename ( __FILE__ ) );
        define( 'MLD_INCLUDES_DIR', trailingslashit ( MLD_DIR . 'includes' ) );
        define( 'MLD_TEMPLATES_DIR', trailingslashit ( MLD_DIR . 'templates' ) );
        define( 'MLD_BASE_DIR', plugin_basename(__FILE__));

        /**
         * URLs
         */
        define( 'MLD_URL', trailingslashit ( plugins_url ( '', __FILE__ ) ) );
        define( 'MLD_ASSETS_URL', trailingslashit ( MLD_URL . 'assets/' ) );

        define( 'MLD_VERSION', self::VERSION );

        /**
         * Text Domain
         */  
        define( 'MLD_TEXT_DOMAIN', 'myrtle-learning-dashboard' );
    }

    /**
     * Save constant for the current frontend page.
     */
    public function get_myrtle_page() {

        $url = array_filter( explode( "/", $_SERVER['REQUEST_URI'] ) );
        define( 'FRONT_PAGE', end( $url ) );
    }

    /**
     * Plugin requiered files
     */
    public function includes() {    

        // require_once MLD_INCLUDES_DIR . '/lib/TCPDF-main/tcpdf.php';

        /**
         * require files
         */
        require_once MLD_INCLUDES_DIR.'general-function/function.php';
        require_once MLD_INCLUDES_DIR.'account-module/account-template.php';
        require_once MLD_INCLUDES_DIR.'account-module/mld-account-function.php';
        require_once MLD_INCLUDES_DIR.'mld-header/mld-header.php';
        require_once MLD_INCLUDES_DIR . 'staff-module/staff-template.php';
        require_once MLD_INCLUDES_DIR . 'resource-module/resource-template.php';
        require_once MLD_INCLUDES_DIR.'notification-module/mld-notification.php';
        require_once MLD_INCLUDES_DIR.'courses-module/mld-courses.php';
        require_once MLD_INCLUDES_DIR.'calendar-module/mld-calendar.php';
        require_once MLD_INCLUDES_DIR.'chat-module/chat-template.php';
        require_once MLD_INCLUDES_DIR . 'my-work/my-work.php';
        require_once MLD_INCLUDES_DIR.'report-module/mld-report.php';
        require_once MLD_INCLUDES_DIR.'dashboard/dashboard-module.php';
        require_once MLD_INCLUDES_DIR.'student-registration/student-registration.php';
        require_once MLD_INCLUDES_DIR.'attendance-module/attendance-template.php';
        require_once MLD_INCLUDES_DIR . 'chat-module/mld-chat.php';
        require_once MLD_INCLUDES_DIR.'admin/post-type.php';
        require_once MLD_INCLUDES_DIR.'admin/menu.php';

        // if( is_admin() ) {

        //     require_once MLD_INCLUDES_DIR.'account-module/mld-account.php';
        //     require_once MLD_INCLUDES_DIR.'staff-module/mld-staff.php';
        //     require_once MLD_INCLUDES_DIR . 'resource-module/mld-resource.php';
        // }
    }
}

/**
 * Display admin notifications if dependency not found.
 */
function MLD_ready() {

    if( !is_admin() ) {
        return;
    }

    if( ! class_exists( 'EXMS' ) ) {
        deactivate_plugins ( plugin_basename ( __FILE__ ), true );
        $class = 'notice is-dismissible error';
        $message = __( 'Myrtle Learning Dashboard add-on requires Exam-LMS plugin is to be activated', 'myrtle-learning-dashboard' );
        printf ( '<div id="message" class="%s"> <p>%s</p></div>', $class, $message );
    }
}

/**
 * @return bool
 */
function MLD() {
    if ( ! class_exists( 'EXMS' ) ) {
        add_action( 'admin_notices', 'MLD_ready' );
        return false;
    }

    return Myrtle_Learning_Dashboard::instance();
}
add_action( 'plugins_loaded', 'MLD' );

/**
 * create table on plugin activation
 */
register_activation_hook( __FILE__, 'mld_create_chat_table' );

function mld_create_chat_table() {

    global $wpdb;

    $table_name = $wpdb->base_prefix . 'mld_chats';
    $resource_table = $wpdb->base_prefix . 'mld_resource';
    $registration_table = $wpdb->base_prefix . 'mld_registration';
    $reference_table = $wpdb->base_prefix . 'mld_refrences';
    $bank_detail_table = $wpdb->base_prefix . 'mld_bank_details';
    $client_communication_table = $wpdb->base_prefix . 'mld_client_communication';
    $user_attendance_table = $wpdb->base_prefix . 'mld_attendance';
    $attendance_query = $wpdb->prepare('SHOW TABLES LIKE %s', $wpdb->esc_like( $user_attendance_table ) );

    $query = $wpdb->prepare('SHOW TABLES LIKE %s', $wpdb->esc_like( $table_name ) );
    $resource_query = $wpdb->prepare('SHOW TABLES LIKE %s', $wpdb->esc_like( $resource_table ) );
    $registration_query = $wpdb->prepare('SHOW TABLES LIKE %s', $wpdb->esc_like( $registration_table ) );

    $refrence_query = $wpdb->prepare('SHOW TABLES LIKE %s', $wpdb->esc_like( $reference_table ) );

    $bank_detail_query = $wpdb->prepare('SHOW TABLES LIKE %s', $wpdb->esc_like( $bank_detail_table ) );
    $client_communication_query = $wpdb->prepare('SHOW TABLES LIKE %s', $wpdb->esc_like( $client_communication_table ) );

    /**
     * create bank detail table
     */
    if ( !$wpdb->get_var( $bank_detail_query ) == $bank_detail_table ) {

        $charset_collate = $wpdb->get_charset_collate();
        $bank_detail_sql = "CREATE TABLE {$bank_detail_table} (

            ID INT PRIMARY KEY AUTO_INCREMENT,
            user_id VARCHAR(255),
            bank_detail VARCHAR(255),
            dates VARCHAR(65535)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta( $bank_detail_sql );
    }

    /**
     * create refrence form table
     */
    if ( !$wpdb->get_var( $refrence_query ) == $reference_table ) {

        $charset_collate = $wpdb->get_charset_collate();
        $reference_sql = "CREATE TABLE {$reference_table} (

            ID INT PRIMARY KEY AUTO_INCREMENT,
            ref_email VARCHAR(255),
            user_id VARCHAR(255),
            refrence_data VARCHAR(255),
            dates VARCHAR(65535)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta( $reference_sql );
    }


    if ( !$wpdb->get_var( $registration_query ) == $registration_table ) {

        $charset_collate = $wpdb->get_charset_collate();
        $registration_sql = "CREATE TABLE {$registration_table} (

            ID INT PRIMARY KEY AUTO_INCREMENT,
            email VARCHAR(255),
            user_role VARCHAR(255),
            user_data VARCHAR(255),
            dates VARCHAR(65535)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($registration_sql);
    }

    if ( !$wpdb->get_var( $query ) == $table_name ) {

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE {$table_name} (

            ID INT PRIMARY KEY AUTO_INCREMENT,
            group_id BIGINT(255),
            group_leader_id VARCHAR(255),
            user_id BIGINT(255),
            chat_type VARCHAR(65535),
            doer VARCHAR(65535),
            message VARCHAR(65535),
            dates VARCHAR(65535)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    /**
     * create resource table
     */
    if ( !$wpdb->get_var( $resource_query ) == $resource_table ) {

        $charset_collate = $wpdb->get_charset_collate();

        $resource_sql = "CREATE TABLE {$resource_table} (

            ID INT PRIMARY KEY AUTO_INCREMENT,
            post_id BIGINT(255),
            resource_title VARCHAR(255),
            resource_subject VARCHAR(65535),
            resource_exam VARCHAR(65535),
            resource_type VARCHAR(65535),
            resource_video_link VARCHAR(65535),
            resource_pdf VARCHAR(65535),
            dates VARCHAR(65535)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($resource_sql);
    }

    /**
     * create client communication table
     */
    if ( !$wpdb->get_var( $client_communication_query ) == $client_communication_table ) {

        $charset_collate = $wpdb->get_charset_collate();

        $client_communication_sql = "CREATE TABLE {$client_communication_table} (

            ID INT PRIMARY KEY AUTO_INCREMENT,
            logged_in_user_id BIGINT(255),
            current_user_id BIGINT(255),
            message VARCHAR(65535),
            dates VARCHAR(65535)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($client_communication_sql);
    }

    /**
     * create a table to save user attendance
     */
    if( !$wpdb->get_var( $attendance_query ) == $user_attendance_table ) {

        $charset_collate = $wpdb->get_charset_collate();

        $attendance_sql = "CREATE TABLE {$user_attendance_table} (

            ID INT PRIMARY KEY AUTO_INCREMENT,
            student_id BIGINT(255),
            teacher_id BIGINT(255),
            course_id BIGINT(255),
            lesson_id BIGINT(255),
            topic_id BIGINT(255),
            quiz_id BIGINT(255),
            group_id BIGINT(255),
            attendance VARCHAR(65535),
            time_date VARCHAR(65535)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($attendance_sql);
    }
}
