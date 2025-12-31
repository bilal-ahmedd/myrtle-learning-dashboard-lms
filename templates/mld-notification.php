<?php
/**
 * Myrtle Learning - Admin Hooks
 *
 */
if( ! defined( 'ABSPATH' ) ) exit;

class MLD_NOTIFICATION_MODULE {

	private static $instance;

	/**
	 * Create class instance
	 */
	public static function instance() {

		if( is_null( self::$instance ) && ! ( self::$instance instanceof MLD_NOTIFICATION_MODULE ) ) {

			self::$instance = new MLD_NOTIFICATION_MODULE;
			self::$instance->hooks();
			self::$instance->includes();
		}

		return self::$instance;
	}

	/**
	 * include files
	 */
	private function includes() {
		require_once MLD_INCLUDES_DIR . 'notification-module/notification-template.php';
	}

	/**
	 * Define hooks
	 */
	private function hooks() {

		add_shortcode( 'notification_module', [ $this, 'mld_notification_module_func' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'mld_enqueue_scripts' ] );
		// add_filter( 'post_row_actions', [ $this, 'mld_remove_row_actions' ], 10, 1 );
		add_filter( 'exms_dashboard_tabs', [ $this, 'exms_notification_tab' ] );
		add_action( 'exms_dashboard_tab_content_exms_my_notification', [ $this, 'render_notification_tab_content' ] );
	}

	public function exms_notification_tab( $tabs ) {

		$tabs['exms_my_notification'] = array(
			'label' => __( 'Notification', 'exms' ),
			'icon'  => 'dashicons-bell',
		);
		return $tabs;
	}

	public function render_notification_tab_content() {

		var_dump( MLD_TEMPLATES_DIR . 'exms-student-notification-template.php' );
		if( file_exists( MLD_TEMPLATES_DIR . 'exms-student-notification-template.php' ) ) {
			echo " Helloo";
			require MLD_TEMPLATES_DIR . 'exms-student-notification-template.php';
		}
	}

	/**
	 * Remove view button from post type
	 */
	public function mld_remove_row_actions( $actions ) {

		if( get_post_type() == 'mld_notifications' ) {

			unset( $actions['view'] );
		}

		return $actions;
	}

	/**
	 * enqueue scripts
	 */
	public function mld_enqueue_scripts() {

		$localized = [
			'ajaxURL'   => admin_url( 'admin-ajax.php' ),
			'security'  => wp_create_nonce( 'mld_ajax_nonce' ),
			'siteURL'		=> site_url()
		];

		$rand = rand( 1000000, 1000000000 );

        // if( has_shortcode( get_the_content( get_the_ID() ), 'notification_module' ) ) {
	        wp_enqueue_style( 'notification-css', MLD_ASSETS_URL . 'css/notification.css', [], $rand, null );
	        wp_register_script( 'mld-notification-js', MLD_ASSETS_URL . 'js/notification.js', [ 'jquery' ], $rand, true );
	        wp_localize_script( 'mld-notification-js', 'MLD', $localized );
	        wp_enqueue_script( 'mld-notification-js' );
        // }
	}

	/**
	 * create a shortcode to get notifications
	 */
	public function mld_notification_module_func( $attr ) {

		if( ! is_user_logged_in() ) {
			return;
		}

		$user_id = get_current_user_id();
		$get_userdata = get_userdata( $user_id );
		$type = isset( $attr['type'] ) ? $attr['type'] : 'section';

		ob_start();
		echo Myrtle_Notification_Template::get_notification_template( $get_userdata, $type );
		$content = ob_get_contents();
		ob_get_clean();
		return $content;
	}
}

/**
 * Initialize MYLI_NOTIFICATION_MODULE
 */
MLD_NOTIFICATION_MODULE::instance();