<?php
/**
 * work templates
 */

if( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class Myrtle_Work_Template
 */
class Myrtle_Work_Template {

	/**
	 * @var self
	 */
	private static $instance = null;

	/**
     * user_id
     */
	private $user_id = 0;
	private $allowed_user = false;
	private $gruop_data = '';

	/**
	 * @since 1.0
	 * @return $this
	 */
	public static function instance() {

		if ( is_null( self::$instance ) && ! ( self::$instance instanceof Myrtle_Work_Template ) ) {
			self::$instance = new self;

			self::$instance->user_id = get_current_user_id();
			self::$instance->hooks();
			self::$instance->includes();
		}

		return self::$instance;
	}

	/**
	 * include files
	 */
	private function includes() {

		require_once MLD_INCLUDES_DIR . 'my-work/mld-work.php';
	}

	/**
	 * Call hooks
	 *
	 * @return void
	 */
	public function hooks() {
		add_shortcode( 'mld-work', [ $this, 'mld_work_callback' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'mld_enqueue_scripts' ] );
		add_filter( 'exms_dashboard_tabs', [ $this, 'exms_my_work_tab' ] );
	}
	
	public function exms_my_work_tab( $tabs ) {

		$tabs['exms_my_work'] = array(
			'label' => __( 'My Work', 'exms' ),
			'icon'  => 'dashicons-networking',
		);
		return $tabs;
	}

	/**
	 * enqueue scripts 
	 */
	public function mld_enqueue_scripts() {

		if( 'works' == FRONT_PAGE ) {

			wp_enqueue_editor();
			wp_enqueue_media();

			$rand = rand( 1000000, 1000000000 );
			wp_enqueue_script( 'work-frontend', MLD_ASSETS_URL . 'js/frontend-work.js', [ 'jquery' ], $rand, true );
			wp_enqueue_style( 'mld-work-frontend-css', MLD_ASSETS_URL . 'css/work.css', [], $rand, null );
			wp_localize_script( 'work-frontend', 'MLD', [
				'ajaxURL'       => admin_url( 'admin-ajax.php' ),
				'security'      => wp_create_nonce( 'mld_ajax_nonce' )
			] );
		}
	}

	/**
	 * my-work shortcode callback
	 */
	public function mld_work_callback() {

		global $wpdb;
		
		$user_id = self::$instance->user_id;

		if( ! $user_id ) {
			return __( 'You need to be logged in first.', 'myrtle-learning-dashboard' );
		}

		$user_capability = mld_user_capability( $user_id );
		
		if( in_array( 'administrator', $user_capability ) || in_array( 'group_leader', $user_capability )  ) {
			$this->allowed_user = true;
		}
		ob_start();
		require_once MLD_TEMPLATES_DIR . 'work-template.php';
		$content = ob_get_contents();
		ob_get_clean();
		return $content;
	}
}

Myrtle_Work_Template::instance();