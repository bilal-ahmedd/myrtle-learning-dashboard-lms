<?php
/**
 * Myrtle Learning - Admin Hooks
 *
 */
if( ! defined( 'ABSPATH' ) ) exit;

class MLD_CHAT_MODULE {

	private static $instance;

	/**
	 * Chat Settings
	 */
	private $settings;

	/**
	 * Create class instance
	 */
	public static function instance() {

		if( is_null( self::$instance ) && ! ( self::$instance instanceof MLD_CHAT_MODULE ) ) {

			self::$instance = new MLD_CHAT_MODULE;
			self::$instance->hooks();

			$settings = get_option( 'mld_chat_settings' );
            self::instance()->settings = $settings;
		}

		return self::$instance;
	}

	/**
	 * Define hooks
	 */
	private function hooks() {

		add_action( 'admin_post_mld_setting_action', [ $this, 'mld_update_chat_settings' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'mld_include_admin_files' ] );
		add_action( 'wp_ajax_mld_search_users', [ $this, 'mld_search_users_callback' ] );
		add_action( 'admin_notices', [ $this, 'mld_admin_notice' ] );
		add_filter( 'exms_dashboard_tabs', [ $this, 'exms_chat_tab' ] );
		add_action( 'exms_dashboard_tab_content_mld_my_chat', [ $this, 'render_chat_tab_content' ] );
	}
	
	public function exms_chat_tab( $tabs ) {

		$tabs['mld_my_chat'] = array(
			'label' => __( 'Chat', 'exms' ),
			'icon'  => 'dashicons-format-chat',
		);
		return $tabs;
	}

	public function render_chat_tab_content() {

		$user_id   = get_current_user_id();
		$group_ids  = mld_get_user_enrolled_group( $user_id ); // ex: array('27')

		if( empty( $group_ids ) || ! is_array( $group_ids ) ) {
			$group_ids = [];
		}

		$groups = [];
		foreach( $group_ids as $gid ) {
			$gid = absint( $gid );
			if ( ! $gid ) { continue; }

			$title = get_the_title( $gid );
			if( empty( $title ) ) {
				$title = sprintf( __( 'Group #%d', 'myrtle-learning-dashboard' ), $gid );
			}

			$groups[] = [
				'id'    => $gid,
				'title' => $title,
			];
		}


		if( file_exists( MLD_TEMPLATES_DIR . 'mld-student-chat-template.php' ) ) {
			require MLD_TEMPLATES_DIR . 'mld-student-chat-template.php';
		}
	}

	/**
	 * Added admin notice
	 */
	public function mld_admin_notice() {

		if( isset( $_GET['message'] ) && $_GET['message'] == 'mld_updated' ) {

			$class = 'notice is-dismissible notice-success';
			$message = __( 'settings Updated', 'msp' );
			printf ( '<div id="message" class="%s"> <p>%s</p></div>', $class, $message );
		}
	}

	/**
	 * live user search
	 */
	public function mld_search_users_callback() {

		global $wpdb;

        $return = [];
        $search_query = isset( $_GET['q'] ) ? $_GET['q'] : '';
        $capabilities = $wpdb->prefix.'capabilities';
        $get_users = $wpdb->get_results( "SELECT users.ID, users.display_name
        FROM {$wpdb->users} as users INNER JOIN {$wpdb->usermeta} as usermeta
        ON users.ID = usermeta.user_id
        WHERE usermeta.meta_key = '$capabilities'
        AND usermeta.meta_value NOT LIKE '%administrator%'
        AND users.user_login LIKE '%{$search_query}%' LIMIT 5", ARRAY_N );

        if( $get_users ) {
        	$return = $get_users;
        }
		echo json_encode( $return );
        wp_die();
	}

	/**
	 * Include admin css
	 */
	public function mld_include_admin_files( $hook ) {

        if( 'myrtle-dashboard_page_mld_chat' == $hook ) {

	        $rand = rand( 1000000, 1000000000 );
	        wp_enqueue_style( 'external-select-min-css', MLD_ASSETS_URL .'css/select2.min.css' );
	        wp_enqueue_style( 'mld-backend-css', MLD_ASSETS_URL . 'css/admin.css', [], $rand, null );
	        wp_enqueue_script( 'external-select2-jquery-js', MLD_ASSETS_URL. 'js/select2.full.min.js', ['jquery'], $rand, true );
	        wp_enqueue_script( 'mld-backend-js', MLD_ASSETS_URL. 'js/backend.js', ['jquery'], $rand, true );
	        wp_localize_script( 'mld-backend-js', 'MLD', [
		        'ajaxURL'   => admin_url( 'admin-ajax.php' )
	        ] );
        }
	}

	/**
	 * update chat settings
	 */
	public function mld_update_chat_settings() {

		if( isset( $_POST['mld-chat-submit'] ) && check_admin_referer( 'mld_setting_nonce', 'mld_setting_nonce_field' ) ) {

			$update_time = isset( $_POST['mld-chat-update-time'] ) ? $_POST['mld-chat-update-time'] : 0;
			$update_unit = isset( $_POST['mld-time-unit'] ) ? $_POST['mld-time-unit'] : '';
			$blocked_users = isset( $_POST['mld_selected_users'] ) ? $_POST['mld_selected_users'] : '';
			$mld_settings = [];

			$mld_settings['time'] = $update_time;
			$mld_settings['unit'] = $update_unit;
			$mld_settings['block_users'] = $blocked_users;

			update_option( 'mld_chat_settings', $mld_settings );

            /**
             * Redirect to the HTTP Referer
             */
            wp_redirect( add_query_arg( 'message', 'mld_updated', $_POST['_wp_http_referer'] ) );
		}
	}

	/**
	 * Added menu for myrtle setting page
	 */
	public function mld_menu_callback() {

		$urls = self::instance()->settings;
		$time = isset( $urls['time'] ) ? $urls['time'] : 0;
		$unit = isset( $urls['unit'] ) ? $urls['unit'] : '';
		$blocked_users = isset( $urls['block_users'] ) ? $urls['block_users'] : [];

        ?>
        <div class="mld-myrtle-settings">
            <h1><?php echo __( 'Chat settings', 'myrtle-learning-dashboard' ); ?></h1>
        </div>
        <form class="chat-settings-grand-wrapper" method="post" action="<?php echo admin_url( 'admin-post.php' ); ?>">

            <div class="chat-settings-wrapper">
                <label><?php echo __( 'Set Ajax Time:', 'myrtle-learning-dashboard' ); ?></label>
                <input type="number" name="mld-chat-update-time" class="mld-chat-update-time" value="<?php echo $time; ?>">
                <select class="mld-time-unit" name="mld-time-unit">
                    <option value="minute" <?php selected( 'minute', $unit, true ); ?>><?php echo __( 'Minute(s)', 'myrtle-learning-dashboard' ); ?></option>
                    <option value="second" <?php selected( 'second', $unit, true ); ?>><?php echo __( 'Second(s)', 'myrtle-learning-dashboard' ); ?></option>
                </select>
            </div>

            <div class="mld-wrapper mld-select-users-fields">
        		<label for="users"><?php _e( 'Restrict User(s):', 'myrtle-learning-dashboard' ); ?></label>
        		<select id="select_users" name="mld_selected_users[]" class="mld-select-user-list" multiple="multiple">
        			<?php
        			$user_name = new Myrtle_Chat_Template();
        			if( ! empty( $blocked_users ) && is_array( $blocked_users ) ) {
        				foreach( $blocked_users as $blocked_user ) {
        					?>
        					<option selected="selected" value="<?php echo $blocked_user; ?>"><?php echo $user_name->get_user_name( $blocked_user ); ?></option>
        					<?php
        				}
        			}
        			?>
        		</select>
            </div>

            <?php
            wp_nonce_field( 'mld_setting_nonce', 'mld_setting_nonce_field' );
            ?>
            <input type="hidden" name="action" value="mld_setting_action">
            <input type="submit" value="<?php echo __( 'Update', 'myrtle-learning-dashboard' ); ?>" class="button button-primary mld-chat-submit" name="mld-chat-submit">
        </form>
        <?php

	}
}

/**
 * Initialize MLD_CHAT_MODULE
 */
MLD_CHAT_MODULE::instance();