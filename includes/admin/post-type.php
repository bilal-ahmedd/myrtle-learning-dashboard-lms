<?php
/**
 * myrtle menu module
 */

if( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class MYRTLE_POST_TYPES
 */
class MYRTLE_POST_TYPES {

	/**
	 * @var self
	 */
	private static $instance = null;

	/**
	 * @since 1.0
	 * @return $this
	 */
	public static function instance() {

		if ( is_null( self::$instance ) && ! ( self::$instance instanceof MYRTLE_POST_TYPES ) ) {
			self::$instance = new self;
			self::instance()->hooks();
		}

		return self::$instance;
	}

	/**
	 * Call hooks
	 *
	 * @return void
	 */
	public function hooks() {
		
		add_action( 'init', [ $this, 'mld_create_post_types' ] );
		// add_action( 'admin_enqueue_scripts', [ $this, 'mld_enqueue_files' ] );
	}

	/**
	 * enqueue admin files
	 */
	public function mld_enqueue_files() {
		
		wp_enqueue_style( 'mld-admin-files', MLD_ASSETS_URL .'css/admin.css' );
	}

	/**
	 * create post types
	 */
	public function mld_create_post_types() {

		/**
		 * create notification menu
		 */
		$labels = array(

			'name'                  => _x( 'Notifications', 'Post type general name', 'myrtle-learning-dashboard' ),
			'singular_name'         => _x( 'Notification Module Type', 'Post type singular name', 'myrtle-learning-dashboard' ),
			'menu_name'             => _x( 'Notification Module Types', 'Admin Menu text', 'myrtle-learning-dashboard' ),
			'name_admin_bar'        => _x( 'Notification Module Types', 'Add New on Toolbar', 'myrtle-learning-dashboard' ),
			'add_new'               => __( 'Add Notification', 'myrtle-learning-dashboard' ),
			'add_new_item'          => __( 'Add New Notification', 'myrtle-learning-dashboard' ),
			'new_item'              => __( 'New Notification', 'myrtle-learning-dashboard' ),
			'edit_item'             => __( 'Edit Notification', 'myrtle-learning-dashboard' ),
			'view_item'             => __( 'View Notification', 'myrtle-learning-dashboard' ),
			'all_items'             => __( 'All Notifications', 'myrtle-learning-dashboard' ),
			'search_items'          => __( 'Search Notifocations', 'myrtle-learning-dashboard' ),
			'parent_item_colon'     => __( 'Parent Notification types:', 'myrtle-learning-dashboard' ),
			'not_found'             => __( 'No Notification found.', 'myrtle-learning-dashboard' ),
			'not_found_in_trash'    => __( 'No Notifications found in Trash.', 'myrtle-learning-dashboard' ),
		);

		$args = array(

			'labels'             => $labels,
			'description'        => 'Post type for Notifications.',
			'public'             => false,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => 'myrtle_menu',
			'query_var'          => true,
			'rewrite'            => [ 'slug' => 'mld-menu' ],
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => 20,
			'supports'           => [ 'title', 'editor', 'thumbnail' ],
			'taxonomies'         => [],
			'show_in_rest'       => true
		);

		register_post_type( 'mld_notifications', $args );

		/**
		 * Add categories taxonomy
		 */
		register_taxonomy( 'mld-notifications_categories', [ 'mld_notifications' ], [
			'hierarchical'      => true,
			'label'             => __( 'Notification Categories', 'myrtle-learning-dashboard' ),
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => [ 'slug' => 'mld-categories' ],
		] );

		
		/**
		 * create resource post type
		 */
		$labels = array(

			'name'                  => __( 'Resources', 'myrtle-learning-dashboard' ),
			'singular_name'         => __( 'Resource Module Type', 'myrtle-learning-dashboard' ),
			'menu_name'             => __( 'Resource Module Types', 'myrtle-learning-dashboard' ),
			'name_admin_bar'        => __( 'Resource Module Types', 'myrtle-learning-dashboard' ),
			'add_new'               => __( 'Add Resource', 'myrtle-learning-dashboard' ),
			'add_new_item'          => __( 'Add New Resource', 'myrtle-learning-dashboard' ),
			'new_item'              => __( 'New Resource', 'myrtle-learning-dashboard' ),
			'edit_item'             => __( 'Edit Resource', 'myrtle-learning-dashboard' ),
			'view_item'             => __( 'View Resource', 'myrtle-learning-dashboard' ),
			'all_items'             => __( 'All Resources', 'myrtle-learning-dashboard' ),
			'search_items'          => __( 'Search Resources', 'myrtle-learning-dashboard' ),
			'parent_item_colon'     => __( 'Parent Resource types:', 'myrtle-learning-dashboard' ),
			'not_found'             => __( 'No Resource found.', 'myrtle-learning-dashboard' ),
			'not_found_in_trash'    => __( 'No Resources found in Trash.', 'myrtle-learning-dashboard' ),
		);

		$args = array(
			'labels'             => $labels,
			'description'        => __( 'Post type for Resources', 'myrtle-learning-dashboard'),
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_menu'       => 'myrtle_menu',
			'query_var'          => false,
			'rewrite'            => false,
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'menu_position'      => 20,
			'supports'           => [ 'title' ],
			'taxonomies'         => [],
			'show_in_rest'       => true
		);

		register_post_type( 'mld_resources', $args );
	}
}

MYRTLE_POST_TYPES::instance();