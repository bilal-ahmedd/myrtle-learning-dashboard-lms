<?php
/**
 * myrtle menu module
 */

if( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class MYRTLE_MENU
 */
class MYRTLE_MENU {

	/**
	 * @var self
	 */
	private static $instance = null;

	/**
	 * @since 1.0
	 * @return $this
	 */
	public static function instance() {

		if ( is_null( self::$instance ) && ! ( self::$instance instanceof MYRTLE_MENU ) ) {
			
			self::$instance = new self;
			self::$instance->hooks();
		}

		return self::$instance;
	}

	/**
	 * Call hooks
	 *
	 * @return void
	 */
	public function hooks() {
		add_action( 'admin_menu', [ $this, 'mld_add_menus' ] );
	}

	/**
	 * Main menu has nothing to display
	 */
	public function mld_mainmenu() {
		return false;
	}

	/**
	 * add all menus
	 */
	public function mld_add_menus() {

		if ( ! current_user_can( 'manage_options' ) ) {

			// Allow only 'Profile' menu
        	global $menu, $submenu;

			// List of allowed menus
	        $allowed = array( 'profile.php' );

	        foreach ( $menu as $key => $value ) {
	            if ( !in_array( $value[2], $allowed ) ) {
	                unset( $menu[$key] );
	            }
	        }

	        // Remove all submenus except profile
	        foreach ( $submenu as $key => $value ) {
	            if ( $key !== 'profile.php' ) {
	                unset( $submenu[$key] );
	            }
	        }
		}
		
		add_menu_page(
			__( 'Myrtle Dashboard', 'myrtle-learning-dashboard' ),
			__( 'Myrtle Dashboard', 'myrtle-learning-dashboard' ),
			'manage_options',
			'myrtle_menu',
			[ $this, 'mld_mainmenu' ],
			'',
			6
		);

		add_submenu_page(
			'myrtle_menu',
			__( 'Chat Settings', 'myrtle-learning-dashboard' ),
			__( 'Chat Settings', 'myrtle-learning-dashboard' ),
			'manage_options',
			'mld_chat',
			[ $this, 'mld_menu_callback' ],
		);

		add_submenu_page(
			'myrtle_menu',
			__( 'Teacher Forms', 'myrtle-learning-dashboard' ),
			__( 'Teacher Forms', 'myrtle-learning-dashboard' ),
			'manage_options',
			'mld_teacher_form',
			[ $this, 'mld_teacher_form_menu_callback' ],
		);
	}

	/**
	 * Added menu for myrtle setting page
	 */
	public function mld_menu_callback() {
		
		$urls = get_option( 'mld_chat_settings' );
		$time = isset( $urls['time'] ) ? $urls['time'] : 5;
		$unit = isset( $urls['unit'] ) ? $urls['unit'] : 'minute';
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

	/**
	 * teacher form menu callback
	 */
	public function mld_teacher_form_menu_callback() {

		$form_titles = get_option( 'mld_teacher_form_titles' );
		if( is_array( $form_titles ) ) {

			$form_titles = array_filter( $form_titles );
		}

		$display = 'none';
		if( $form_titles ) {
			$display = '';
		}
		?>
		<div class="mld-teacher-uploads">
			<p><?php echo __( 'Select Upload Type', 'myrtle-learning-dashboard' ); ?></p>
			<select class="mld-upload-type">
				<option value=""><?php echo __( 'Select a type', 'myrtle-learning-dashboard' ) ?></option>
				<option value="documents"><?php echo __( 'Documents', 'myrtle-learning-dashboard' ); ?></option>
				<option value="form"><?php echo __( 'Form', 'myrtle-learning-dashboard' ); ?></option>
			</select>
			<input type="button" value="<?php echo __( 'Add New File', 'myrtle-learning-dashboard' ); ?>" class="mld-teacher-upload button button-primary">
		</div>
		<div class="mld-form-files">
			<?php
			if( ! empty( $form_titles ) && is_array( $form_titles ) ) {

				$no = 0;
				foreach( $form_titles as $data ) {

					$type = isset( $data->index ) ? $data->index : '';
					$title = isset( $data->title ) ? $data->title : ''; 
					$title_with_dash = str_replace( ' ', '-', $title );

					$file = mld_get_category_files( 'mld-teachers-form-uploads/'.$title_with_dash );
					$file = isset( $file[0] ) ? $file[0] : '';
					$form_title = $title;
					$basePath = "/home/runcloud/webapps/myrtlelearning/";

					$cleanedPath = str_replace( $basePath, '', $file );
					$cleanedPath = site_url().'/'.$cleanedPath;
					$form_url = $cleanedPath;
					$file_name_array = explode( '/', $file );
					$file_name = end( $file_name_array );

					?>
					<div class="mld-teacher-inner-wrapper">
						<span class="dashicons dashicons-edit mld-edit-icon"></span>
						<span class="dashicons dashicons-no mld-dashicon" data-delete_url="<?php echo $file; ?>"></span>
						<h3><?php echo $form_title; ?></h3>
						<a href="<?php echo $form_url; ?>" download><?php echo $file_name; ?></a>
						<input type="text" value="<?php echo $form_title; ?>" class="mld-form-title" style="display: none;" data-type="<?php echo $type; ?>">
						<input type="file" class="mld-teacher-uploads" style="display: none;" value="<?php echo $file_name; ?>">
					</div>
					<?php
					$no++;
				}
			}
			?>
		</div>
		<input type="button" value="<?php echo __( 'Update', 'myrtle-learning-dashboard' ); ?>" class="mld-update-teacher-form button button-primary" style="display: <?php echo $display; ?>;">
		<?php
	}
}

MYRTLE_MENU::instance();