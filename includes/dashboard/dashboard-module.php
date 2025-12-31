<?php
/**
 * Myrtle Learning - sidebar menu
 *
 */
if( ! defined( 'ABSPATH' ) ) exit;

class MLD_DASHBOARD_MODULE {

	private static $instance;

	/**
	 * Create class instance
	 */
	public static function instance() {

		if( is_null( self::$instance ) && ! ( self::$instance instanceof MLD_DASHBOARD_MODULE ) ) {

			self::$instance = new MLD_DASHBOARD_MODULE;
			self::$instance->hooks();
		}

		return self::$instance;
	}

	/**
	 * Define hooks
	 */
	private function hooks() {
		add_action( 'wp_enqueue_scripts', [ $this, 'mld_enqueue_dashboard_module_files' ] );
		add_action( 'wp_head', [ $this, 'mld_show_tabs_according_to_dynamic_roles' ]  );
	}

	/**
	 * enqueue sidebar css/jquery files
	 */
	public function mld_enqueue_dashboard_module_files() {
		$rand = rand( 1000000, 1000000000 );
		if ( strpos( $_SERVER['REQUEST_URI'], '/dashboard/' ) !== false ) {
			wp_enqueue_style( 'dashboard-module-css', MLD_ASSETS_URL .'css/dashboard-module.css', '', $rand, false );
			wp_enqueue_script( 'dashboard-module-js', MLD_ASSETS_URL. 'js/dashboard-module.js', [ 'jquery' ], $rand, true );
		}
	}

	/**
	* Show tabs according to conditions based on selected user roles.
	*/
	public function mld_show_tabs_according_to_dynamic_roles() {

	    // Define all tabs with their respective allowed roles (using exact values)
	    $tabs_roles = [
	        'mld-is-dashboard-tab'      => [ 'administrator', 'subscriber', 'student', 'group_leader' ],
	        'mld-is-my-account-tab'     => [ 'administrator', 'pending', 'subscriber', 'student', 'pending_teacher', 'pending_student', 'group_leader' ],
	        'mld-is-my-report-tab'      => [ 'administrator', 'subscriber', 'student', 'group_leader' ],
	        'mld-is-my-calendar-tab'    => [ 'administrator', 'subscriber', 'student', 'pending_student', 'group_leader' ],
	        'mld-is-my-courses-tab'     => [ 'administrator', 'subscriber', 'student', 'pending_student', 'group_leader' ],
	        'mld-is-resources-tab'      => [ 'administrator', 'subscriber', 'student', 'pending_student' ],
	        'mld-my-staff-profile-tab'  => [ 'administrator', 'subscriber', 'student', 'group_leader' ],
	        'mld-is-my-work-tab'        => [ 'administrator', 'subscriber', 'student', 'group_leader' ],
	    ];

	    // Get current user roles
	    $current_user = wp_get_current_user();
	    $user_roles = $current_user->roles;

	    if ( empty( $user_roles ) || ! is_array( $user_roles ) ) {
	        return;
	    }
        foreach ( $tabs_roles as $tab_class => $allowed_roles ) {

            $has_allowed_role = array_intersect( $user_roles, $allowed_roles );

            if ( ! empty( $has_allowed_role ) ) {
            	?>
            	<style type="text/css">
            		.<?php echo $tab_class; ?> {
            			display: block !important; 
            		}
            	</style>
            	<?php
            } else {
            	?>
            	<style type="text/css">
            		.<?php echo $tab_class; ?> {
            			display: none !important; 
            		}
            	</style>
            	<?php
            }
        }
	}
}

/**
 * Initialize MLD_DASHBOARD_MODULE
 */
MLD_DASHBOARD_MODULE::instance();