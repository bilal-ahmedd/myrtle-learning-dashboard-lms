<?php
/**
 * Myrtle Learning - Admin Hooks
 *
 */
if( ! defined( 'ABSPATH' ) ) exit;

class MLD_CALENDAR_MODULE {

	private static $instance;
	private $userlogged;

	/**
	 * Create class instance
	 */
	public static function instance() {

		if( is_null( self::$instance ) && ! ( self::$instance instanceof MLD_CALENDAR_MODULE ) ) {

			self::$instance = new MLD_CALENDAR_MODULE;

			self::$instance->userlogged = is_user_logged_in();
			self::$instance->hooks();
		}

		return self::$instance;
	}

	/**
	 * Define hooks
	 */
	private function hooks() {
		add_shortcode( 'mld_calendar_event', [ $this, 'mld_calendar_event_func' ] );
		add_shortcode( 'mld_list_event', [ $this, 'mld_list_event_func' ] );
		add_shortcode( 'mld_google_meet', [ $this, 'mld_google_meet_func' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'mld_calendar_scripts' ] );
		// add_shortcode( 'calendar_list_view', [ $this, 'mld_calendar_list_view_callback' ] );
		add_filter( 'exms_dashboard_tabs', [ $this, 'exms_my_calendar_tab' ] );
	}
	public function exms_my_calendar_tab( $tabs ) {

		$tabs['exms_my_calendar'] = array(
			'label' => __( 'My Calendar', 'exms' ),
			'icon'  => 'dashicons-calendar-alt',
		);
		return $tabs;
	}

	/**
	 * callback function of calendar list view
	 */
	public function mld_calendar_list_view_callback() {

		global $wpdb;

		if( ! self::$instance->userlogged ) {
			return false;
		}

		$group_name = $this->mld_get_group_name();
		$limit = isset( $atts['limit'] ) ? intval( $atts['limit'] ) : 3;

		$args = array(
			'taxonomy' => 'tribe_events_cat'
		);

		$group_name_array = explode( ',', $group_name );
		$category = get_categories($args);

		if( is_array( $category ) && ! empty( $category ) ) {
			$category = array_column( $category,'name' );
		}

		$new_array = [];
		if( is_array( $category ) && is_array( $group_name_array ) ) {
			$new_array = array_intersect( $group_name_array, $category );
		}

		if( empty( $new_array ) ) {

			$categories = array_map( function( $cat ) {
				return trim( str_replace( '-', '', $cat ) );
			}, $category );

			$placeholders = implode(', ', array_fill(0, count($categories), '%s'));
			$cat = "AND t.name NOT IN ($placeholders)";
		} else {

			$categories = array_map( function( $cat ) {
				return trim( str_replace( '-', '', $cat ) );
			}, $group_name_array );

			$placeholders = implode( ', ', array_fill( 0, count( $categories ), '%s' ) );
			$cat = "AND t.name IN ($placeholders)";
		}

		$query = "
		    SELECT p.ID, p.post_title 
		    FROM {$wpdb->prefix}posts p
		    INNER JOIN {$wpdb->prefix}term_relationships tr ON (p.ID = tr.object_id)
		    INNER JOIN {$wpdb->prefix}term_taxonomy tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id)
		    INNER JOIN {$wpdb->prefix}terms t ON (tt.term_id = t.term_id)
		    WHERE p.post_type = 'tribe_events' 
		    AND p.post_status = 'publish'
		    AND tt.taxonomy = 'tribe_events_cat' 
		    $cat
		    GROUP BY p.ID
		    ORDER BY p.post_date DESC
		    LIMIT %d
		";

		$query_params = array_merge($categories, [3]);
		$results = $wpdb->get_results($wpdb->prepare($query, $query_params));

		ob_start();

		if (!empty($results)) {
			?>
			<div class="mld-event-main-wrapper">
			<?php
		    foreach ($results as $post) {

		    	$thumbnail_url = get_the_post_thumbnail_url( $post->ID );
		    	$event_start_date = get_post_meta( $post->ID, '_EventStartDate', true );
		    	$event_end_date = get_post_meta( $post->ID, '_EventEndDate', true );
		    	$formatted_start = date( "h:i A", strtotime( $event_start_date ) );
				$formatted_end = date( "h:i A", strtotime( $event_end_date ) );
				$result = "$formatted_start - $formatted_end";
		    	?>
		    	<div class="mld-events-wrapper">
		    		<div class="mld-event-thumbnail">
		    			<img src="<?php echo $thumbnail_url; ?>">  
		    		</div>
		    		<div class="mld-event-detail">
		    			<div class="mld-event-link">
		    				<a href="<?php echo get_permalink( $post->ID ); ?>"><?php echo esc_html($post->post_title); ?></a>
		    			</div>  
		    			<div class="mld-event-time"><?php echo $result; ?></div>
		    		</div>
		    		<div class="mld-clear-both"></div>
		    	</div>
		    	<?php
		    }
		    ?>
		    <div class="mld-view-all-events">
		    	<a href="<?php echo site_url(). '/dashboard/calendar' ?>"><?php echo __( 'View All Events', 'myrtle-learning-dashboard' ); ?></a>
		    </div>
		    </div>
		    <?php
		} else {
		    echo 'No events found in this category.';
		}

		$content = ob_get_contents();
		ob_get_clean();
		return $content;
	}
	/**
	 * enqueue calendar scripts
	 */
	public function mld_calendar_scripts() {

		$rand = rand( 1000000, 1000000000 );

		$is_shortcode_available = has_shortcode( get_the_content( get_the_ID() ), 'mld_list_event' );

		if( 'dashboard' == FRONT_PAGE || 'plan' == FRONT_PAGE || true === $is_shortcode_available || 'tribe_events' === get_post_type() ) {
			wp_enqueue_style( 'calendar-css', MLD_ASSETS_URL .'css/calendar.css', '', $rand, false );
			wp_enqueue_script( 'calendar-js', MLD_ASSETS_URL .'js/calendar.js', [ 'jquery' ], $rand, true );
		}

		if( 'dashboard' == FRONT_PAGE || true === $is_shortcode_available ) {

			wp_enqueue_style( 'calendar-dashboard-css', MLD_ASSETS_URL .'css/calendar_dashboard.css', '', $rand, false );
		}

		if( 'plan' == FRONT_PAGE ) {
			wp_enqueue_style( 'calendar-plan-css', MLD_ASSETS_URL .'css/calendar_plan.css', '', $rand, false );
		}
	}

	/**
	 * create a shortcode to display google meet link
	 */
	public function mld_google_meet_func( $atts ) {

		if( ! self::$instance->userlogged ) {
			return false;
		}

		$post_id = isset( $atts['post_id'] ) ? $atts['post_id'] : get_the_ID();
		$text = isset( $atts['text'] ) ? $atts['text'] : 'Google meet';
		$google_meet_custom_field = tribe_get_custom_fields( $post_id );
		$google_meet_url = isset( $google_meet_custom_field['Google meet'] ) ? $google_meet_custom_field['Google meet'] : '';

		if( empty( $google_meet_url )  ) {
			return;
		}

		$google_meet_url = esc_url( $google_meet_url );
		ob_start();
		?>
		<div class="mld-meet-wrapper">
			<a href="<?php echo $google_meet_url; ?>" class="mld-google-meet" target="_blank"><?php echo $text; ?></a>
		</div>
		<?php
		$content = ob_get_contents();
		ob_get_clean();
		return $content;
	}

	/**
	 * create a shortcode to display event in list view
	 */
	public function mld_list_event_func( $atts ) {

		if( ! self::$instance->userlogged ) {
			return false;
		}

		$group_name = $this->mld_get_group_name();
		$limit = isset( $atts['limit'] ) ? intval( $atts['limit'] ) : 3;

		$args = array(
			'taxonomy' => 'tribe_events_cat'
		);

		$group_name_array = explode( ',', $group_name );
		$category = get_categories($args);

		if( is_array( $category ) && ! empty( $category ) ) {
			$category = array_column( $category,'name' );
		}

		$new_array = [];
		if( is_array( $category ) && is_array( $group_name_array ) ) {
			$new_array = array_intersect( $group_name_array, $category );
		}

		if( empty( $new_array ) ) {
			$category = implode( ',', $category );
			$cat = 'exclude-category="'.$category.'"';
		} else {
			$cat = 'cat="'.$group_name.'"';
		}

		if( $group_name ) {
			ob_start();

			echo do_shortcode( '[tribe_events view="list" events_per_page="'.$limit.'" tribe-bar="false" '.$cat.']' );
			
			if( 'dashboard' == FRONT_PAGE ) {
				?>
				<div class="mld-view-all-events">
					<a href="<?php echo site_url(). '/dashboard/calendar' ?>"><?php echo __( 'View All Events', 'myrtle-learning-dashboard' ); ?></a>
				</div>
				<?php
			}

			$content = ob_get_contents();
			ob_get_clean();
			return $content;
		} else {
			return '<div class="mld-no-event-wrapper">
                <img class="mld-no-message-found" src="' . esc_url( MLD_ASSETS_URL . 'images/no-event-found.png' ) . '" alt="No message found">
            </div>';
		}
		return 'working';
	}

	/**
	 * create a shortcode to display event in calendar view
	 */
	public function mld_calendar_event_func() {

		if( ! self::$instance->userlogged ) {
			return false;
		}

		$group_name = $this->mld_get_group_name();
		$args = array(
			'taxonomy' => 'tribe_events_cat'
		);

		$group_name_array = explode( ',', $group_name );
		$category = get_categories($args);

		if( is_array( $category ) && ! empty( $category ) ) {
			$category = array_column( $category,'name' );
		}

		$new_array = array_intersect( $group_name_array, $category );
		if( empty( $new_array ) ) {
			$category = implode( ',', $category );
			$cat = 'exclude-category="'.$category.'"';
		} else {
			$cat = 'cat="'.$group_name.'"';
		}

		if( $group_name ) {
			return do_shortcode( '[tribe_events view="month" '.$cat.']' );
		} else {
			return __( 'No event Found', 'myrtle-learning-dashboard' );
		}
	}

	/**
	 * create a function to get group name
	 */
	public function mld_get_group_name() {

		$user_id = get_current_user_id();
		if ( learndash_is_group_leader_user( $user_id ) === true || current_user_can( 'manage_options' ) ) {
			$groups = mld_get_user_administrated_groups( $user_id );
		} else {
			$groups = mld_get_user_groups( $user_id );
		}

		$group_title = [];
		if( $groups && is_array( $groups ) ) {
			foreach( $groups as $group ) {
				if( 'publish' != get_post_status( $group ) ) {
					continue;
				}
				$group_title[] = get_the_title( $group );
			}
		}

		$group_title = array_unique( $group_title );
		$group_title = implode( ',', $group_title );
		return $group_title;
	}
}

/**
 * Initialize MYLI_NOTIFICATION_MODULE
 */
MLD_CALENDAR_MODULE::instance();