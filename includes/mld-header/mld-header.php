<?php
/**
 * Myrtle Learning - header
 *
 */
if( ! defined( 'ABSPATH' ) ) exit;

class MLD_HEADER {

	private static $instance;

	private $userlogged;
	private $userid;
	private $site_url;

	/**
	 * Create class instance
	 */
	public static function instance() {

		if( is_null( self::$instance ) && ! ( self::$instance instanceof MLD_HEADER ) ) {

			self::$instance = new MLD_HEADER;

			self::$instance->userlogged = is_user_logged_in();

			if( self::$instance->userlogged ) {
				self::$instance->userid = get_current_user_id();
			}

			self::$instance->site_url = site_url();
			self::$instance->hooks();
		}

		return self::$instance;
	}

	/**
	 * Define hooks
	 */
	private function hooks() {

		add_shortcode( 'user_profile', [ $this, 'mld_create_user_profile' ] );
		add_shortcode( 'mld_login', [ $this, 'mld_dashboard_login' ] );
		add_shortcode( 'myrtle_header', [ $this, 'mld_myrtle_header' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'mld_enqueue_header_scripts' ] );
		add_action( 'template_redirect', [ $this, 'mld_redirect_user_on_logout' ] );
	}

	/**
	 * logout user when user is logout
	 */
	public function mld_redirect_user_on_logout() {

		if( ! self::$instance->userlogged ) {

			if( 'reports' == FRONT_PAGE || 'my-account' == FRONT_PAGE || 'calendar' == FRONT_PAGE || 'mld-courses' == FRONT_PAGE || 'resources' == FRONT_PAGE || 'staff-profile' == FRONT_PAGE || 'works' == FRONT_PAGE || 'chat' == FRONT_PAGE || 'notification' == FRONT_PAGE || '?user_switched=true' == FRONT_PAGE || 'dashboard' == FRONT_PAGE ) {
				wp_redirect( site_url() );
				exit;
			}
		}

		if( self::$instance->userlogged ) {

			// var_dump( get_permalink() );exit;
			if( 'report' == FRONT_PAGE ) {
				wp_redirect( site_url().'/dashboard/reports' );
				exit;
			}

			if( 'myrtle-dashboard' == FRONT_PAGE || '?user_switched=true' == FRONT_PAGE ) {
				wp_redirect( site_url().'/dashboard' );
				exit;
			}

			$user_capability = mld_user_capability( self::$instance->userid );

			if( is_array( $user_capability ) && ! empty( $user_capability ) ) {

				if( ! in_array( 'administrator', $user_capability ) ) {
					?>
					<style type="text/css">
						body {
							margin-top: -32px !important;
						}
						#wpadminbar {
							display: none !important;
						}
					</style>
					<?php
				}
			}
			// var_dump( 2 );exit;
		}
	}

	/**
	 * enqueue header scripts
	 */
	public function mld_enqueue_header_scripts() {

		$rand = rand( 1000000, 1000000000 );
		wp_enqueue_style('dashicons');
		wp_enqueue_style( 'mld-header-css', MLD_ASSETS_URL . 'css/header.css', [], $rand, null );
		wp_enqueue_script( 'mld-header-js', MLD_ASSETS_URL . 'js/header.js', [ 'jquery' ], $rand, true );
	}

	/**
	 * myrtle header
	 */
	public function mld_myrtle_header( $atts ) {

		if( ! self::$instance->userlogged ) {
			return __( 'You need to be logged in to access this page', 'myrtle-learning-dashboard' );
		}

		$user_id = self::$instance->userid;
		$user_name = mld_get_username( $user_id );
		$header_content = isset( $atts['content'] ) ? $atts['content'] : '';
		$header_title = isset( $atts['title'] ) ? $atts['title'] : '';

		ob_start();
		?>
		<div class="mld-header-main-wrapper" sitr-url="<?php echo site_url(); ?>">
			<div class="dashicons dashicons-arrow-right-alt mld-menu-line"></div>
			<div class="mld-header-left">
				<div class="mld-header-title"><?php echo $header_title; ?></div>
				<div class="mld-header-content"><?php echo $header_content; ?></div>
			</div>
			<div class="mld-header-right">
				<div class="mld-main-header-user-avatar">
					<div class="mld-header-avatar"><?php echo get_avatar( $user_id, '70px' ); ?></div>
					<div class="mld-header-avatar-name">
						<span class="mld-header-user-name"><?php echo $user_name; ?></span>
						<span class="dashicons dashicons-arrow-down-alt2 mld-logout-arrow"></span>
					</div>
					<div class="mld-current-user-star" style="display: none;"></div>
				</div>
				<div class="mld-main-header-logout-menu">
					<div class="mld-logout-dropdown">
						<a href="<?php echo wp_logout_url(); ?>"><?php echo __( 'Logout', 'myrtle-learning-dashboard' ); ?></a>
					</div>
				</div>
				<div class="mld_clear_both"></div>
			</div>
			<div class="mld-clear-both"></div>
		</div>
		<?php
		$content = ob_get_contents();
		ob_get_clean();

		return $content;
	}

	/**
	 * create a shortcode to display login/logout option
	 */
	public function mld_dashboard_login() {

		ob_start();

		$user_id = self::$instance->userid;
		$is_user_logged_in = self::$instance->userlogged; 
		?>
        <div class="mld-login-dropdown <?php echo ( $is_user_logged_in ) ? 'mcli': ''; ?> ">
			<?php if( $is_user_logged_in ) { ?>
			<?php echo get_avatar( $user_id, '50px' ); ?>
            <span class="dashicons dashicons-arrow-down-alt2 mld-img-arrow"></span>
			<?php } ?>
            <div class="mld-login-logout-menu <?php echo ( $is_user_logged_in ) ? 'mld-login-btn': ''; ?>">
            	<a href="<?php echo site_url().'/dashboard'; ?> " class="mld-main-menu <?php echo ( $is_user_logged_in ) ? 'mcli': ''; ?> ">
					<?php echo __( 'Dashboard', 'myrtle-learning-dashboard' ); ?>
                </a>
				<?php echo do_shortcode( '[learndash_login]' ); ?>
            </div>
        </div>
		<?php
		$content = ob_get_contents();
		ob_get_clean();

		return $content;
	}

	/**
	 * create user profile
	 */
	public function mld_create_user_profile() {

		if( ! self::$instance->userlogged ) {
			return;
		}

		$user_id = self::$instance->userid;
		// $user_email = mld_get_user_email( $user_id );
		$user_name = mld_get_username( $user_id );
		$site_url = self::$instance->site_url;

		ob_start();

		?>
		<div class="mld-header-wrapper">
			<div class="mld-header-title">
				<span class="dashicons dashicons-arrow-right-alt mld-menu-line"></span>
				<div class="mld-user-name"><?php echo 'Hi '.$user_name.','; ?></div>
				<div class="mld-welcome-text"><?php echo __( 'Welcome to', 'myrtle-learning-dashboard' ); ?></div>
				<div class="mld-dashboard"><?php echo __( 'Myrtle Learning Dashboard', 'myrtle-learning-dashboard' ); ?></div>
			</div>
			<div class="mld-profile-container">
				<div class="mld-chat" style="display: none;">
					<a href="<?php echo $site_url;?>/myrtle-dashboard/mld-chat/"><span class="dashicons dashicons-format-chat"></span></a>
				</div>
				<div class="mld-bell-icon" style="display: none;">
					<a href="<?php echo $site_url;?>/myrtle-dashboard/mld-notification/"><span class="dashicons dashicons-bell"></span></a>
				</div>
				<div class="mld-profile">
					<span>
						<img src="<?php echo MLD_ASSETS_URL.'images/header-img.png' ?>" alt="not found">
					</span>
					<span style="display: none;">
						<?php echo get_avatar( $user_id, '70px' ); ?>
					</span>
					<span class="dashicons dashicons-arrow-down-alt2 mld-logout-arrow"></span>
					<div class="mld-logout-dropdown">
						<a href="<?php echo wp_logout_url(); ?>"><?php echo __( 'Logout', 'myrtle-learning-dashboard' ); ?></a>
					</div>
				</div>
			</div>
			<div class="mld-clear-both"></div>
		</div>
		<?php

		$content = ob_get_contents();
		ob_get_clean();

		return $content;
	}
}

/**
 * Initialize MYLI_NOTIFICATION_MODULE
 */
MLD_HEADER::instance();