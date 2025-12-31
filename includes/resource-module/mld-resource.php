<?php
/**
 * Myrtle Learning - Admin Hooks
 *
 */
if( ! defined( 'ABSPATH' ) ) exit;

class MLD_RESOURCE_MODULE {

	private static $instance;

	/**
	 * Create class instance
	 */
	public static function instance() {

		if( is_null( self::$instance ) && ! ( self::$instance instanceof MLD_RESOURCE_MODULE ) ) {

			self::$instance = new MLD_RESOURCE_MODULE;
			self::$instance->hooks();
		}

		return self::$instance;
	}

	/**
	 * Define hooks
	 */
	private function hooks() {
        add_action( 'add_meta_boxes', [ $this, 'mld_add_metabox' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'mld_add_admin_scripts' ] );
        add_action( 'save_post', [ $this, 'mld_save_resource_settings' ], 10, 3 );
        add_action( 'wp_ajax_get_subject_categories', [ $this, 'mld_get_subject_categories' ] );
        add_action( 'wp_ajax_mld_add_clone', [ $this, 'mld_clone_resource' ] );
        add_filter( 'post_row_actions', [ $this, 'mld_remove_view_option' ], 10, 2 );
	}

    /**
     * Remove view option from the resource
     */
    public function mld_remove_view_option( $actions, $post ) {

        if ( $post->post_type === 'mld_resources' ) {
            unset( $actions['view'] );
        }
        return $actions;
    }

    /**
     * create a function to get all quizes of a course
     */
    public function mld_get_all_categories( $course_id ) {

        $subject_exams = get_terms( array(
            'taxonomy' => 'ld_course_category',
            'object_ids' => $course_id
        ) );

        $category = [];
        if( $subject_exams && is_array( $subject_exams ) ) {
            foreach( $subject_exams as $subject_exam ) {
                $category[] = $subject_exam->name;
            }
        }

        return $category;
    }

    /**
     * Clone resourse post type
     */
    public function mld_clone_resource() {

        $response = [];
        global $wpdb;

        if( ! wp_verify_nonce( $_POST['mld_nounce'], 'mld_ajax_nonce' ) ) {

            $response['message'] = __( 'data not found', 'myrtle-learning-dashboard' );
            $response['status'] = 'false';

            echo json_encode( $response );
            wp_die();
        }

        $resource_id = isset( $_POST['resource_id'] ) ? $_POST['resource_id'] : 0;

        if( ! $resource_id ) {

            $response['message'] = __( 'resource id not found', 'myrtle-learning-dashboard' );
            $response['status'] = 'false';

            echo json_encode( $response );
            wp_die();
        }

        $resource_title = get_the_title( $resource_id );
        $copy_text = __( 'Copy of', 'myrtle-learning-dashboard' );
        $new_title = $copy_text.' '.$resource_title;

        // Create post object
        $post_clone = array(
          'post_title'    => $new_title,
          'post_content'  => '',
          'post_type'     => 'mld_resources',
          'post_status'   => 'publish'
        );

        // Insert the post into the database
        $new_resource_id = wp_insert_post( $post_clone );

        $table_name = $wpdb->prefix.'mld_resource';
        $resource_settings = $wpdb->get_results( "SELECT * FROM $table_name
        WHERE post_id = $resource_id
        " );

        $resource_subject = isset( $resource_settings[0]->resource_subject ) ? $resource_settings[0]->resource_subject : '';
        $resource_type = isset( $resource_settings[0]->resource_type ) ? $resource_settings[0]->resource_type : '';
        $resource_video_link = isset( $resource_settings[0]->resource_video_link ) ? $resource_settings[0]->resource_video_link : '';
        $resource_pdf = isset( $resource_settings[0]->resource_pdf ) ? $resource_settings[0]->resource_pdf : '';
        $resource_exam = isset( $resource_settings[0]->resource_exam ) ? $resource_settings[0]->resource_exam : '';
        $resource_year = isset( $resource_settings[0]->resource_year ) ? intval( $resource_settings[0]->resource_year ) : 0;
        $resource_tier = isset( $resource_settings[0]->resource_tier ) ? $resource_settings[0]->resource_tier : '';


        $wpdb->insert( $table_name, array(
            'post_id'               => $new_resource_id,
            'resource_title'        => $new_title,
            'resource_subject'      => $resource_subject,
            'resource_exam'         => $resource_exam,
            'resource_type'         => $resource_type,
            'resource_video_link'   => $resource_video_link,
            'resource_pdf'          => $resource_pdf,
            'resource_year'         => $resource_year,
            'resource_tier'         => $resource_tier
        ) );

        $response['status'] = 'true';

        echo json_encode( $response );
        wp_die();
    }

    /**
     * get subject exam
     */
    public function mld_get_subject_categories() {

        $response = [];

        $subject_id = isset( $_POST['subject_id'] ) ? intval( $_POST['subject_id'] ) : 0;

        if( empty( $subject_id ) ) {

            $response['message'] = __( 'subject id not found', 'myrtle-learning-dashboard' );
			$response['status'] = 'false';

			echo json_encode( $response );
			wp_die();
        }

        $subject_exams = $this->mld_get_all_categories( $subject_id );

        ob_start();

        if( $subject_exams && is_array( $subject_exams ) ) {
            foreach( $subject_exams as $subject_exam ) {
                ?>
                <option value="<?php echo $subject_exam; ?>"><?php echo $subject_exam; ?></option>
                <?php
            }
        }

        $content = ob_get_contents();
        ob_get_clean();

        $response['content'] = $content;
        $response['status'] = 'true';
        echo json_encode( $response );
        wp_die();
    }
    /**
     * mld save resource settings data at postmeta
     */
    public function mld_save_resource_settings( $post_id, $post, $update ) {

        global $wpdb;

        $tabel = "{$wpdb->base_prefix}mld_resource";

        $post_type = get_post_type( $post_id );

        if( 'mld_resources' == $post_type ) {

            if( ! $update ) {
                return false;
            }

            $post_status = get_post_status( $post_id );

            if( 'draft' != $post_status && 'publish' != $post_status ) {
                return false;
            }

            $time = time();

            $resource_subject = isset( $_POST['resource-subject'] ) ? $_POST['resource-subject'] : '';
            $resource_exam = isset( $_POST['mld-resource-exam'] ) ? $_POST['mld-resource-exam'] : '';
            $resource_type = isset( $_POST['resource-type'] ) ? $_POST['resource-type'] : '';
            $resource_video_link = isset( $_POST['resource-video-link'] ) ? $_POST['resource-video-link'] : '';
            $resource_pdf = isset( $_POST['mld-resource-pdf-name'] ) ? $_POST['mld-resource-pdf-name'] : '';
            $resource_year = isset( $_POST['mld-year'] ) ? $_POST['mld-year'] : 0;
            $resource_tier = isset( $_POST['resource-tier'] ) ? $_POST['resource-tier'] : '';
            $resource_test = isset( $_POST['resource-test'] ) ? $_POST['resource-test'] : '';

            $resource_settings = $wpdb->get_results( "SELECT ID FROM $tabel
             WHERE post_id = $post_id" );

            if( ! $resource_settings ) {

                $wpdb->insert( $tabel, array(
                    'post_id'               => $post_id,
                    'resource_title'        => get_the_title( $post_id ),
                    'resource_subject'      => $resource_subject,
                    'resource_exam'         => $resource_exam,
                    'resource_type'         => $resource_type,
                    'resource_video_link'   => $resource_video_link,
                    'resource_pdf'          => $resource_pdf,
                    'resource_year'         => $resource_year,
                    'resource_tier'         => $resource_tier,
                    'resource_test'         => $resource_test
                ) );
            } else {
                $wpdb->query( "UPDATE $tabel SET resource_subject = '".$resource_subject."',
                    resource_exam = '".$resource_exam."',
                    resource_type = '".$resource_type."',
                    resource_video_link = '".$resource_video_link."',
                    resource_pdf = '".$resource_pdf."',
                    resource_year = '".$resource_year."',
                    resource_tier = '".$resource_tier."',
                    resource_test = '".$resource_test."'
                    WHERE post_id = '".$post_id."'" );
            }
        }
    }

    /**
     * add admin enqueue scripts for resource page
     */
    public function mld_add_admin_scripts() {

        $post_type = get_post_type();

        if( 'mld_resources' == $post_type ) {

            wp_enqueue_media();
            $rand = rand( 1000000, 1000000000 );
            wp_enqueue_script( 'mld-resource-backend-js', MLD_ASSETS_URL . 'js/resource.js', [ 'jquery' ], $rand, true );
            wp_enqueue_style( 'mld-resource-backend-css', MLD_ASSETS_URL . 'css/resource.css', [], $rand, null );

            wp_localize_script( 'mld-resource-backend-js', 'MLD', [
		        'ajaxURL'       => admin_url( 'admin-ajax.php' ),
		        'security'      => wp_create_nonce( 'mld_ajax_nonce' )
	        ] );
        }
    }

    /**
     * add meta box in rescorce post type
     */
    public function mld_add_metabox( $post_type ) {

        add_meta_box(
            'mld-resource',
            __( 'Resource Settings', 'myrtle-learning-dashboard' ),
            [ $this, 'mld_resource_metabox_callback' ],
            'mld_resources',
            'normal',
            'high' );
    }

    /**
     * resource metabox callback
     */
    public function mld_resource_metabox_callback() {

        global $wpdb;

        $course_args = array(
            'post_type'         => 'sfwd-courses',
            'numberposts'       => -1,
            'fields'            => 'ids'
        );

        $courses_array = get_posts( $course_args );

        $post_id = get_the_ID();
        $table_name = $wpdb->prefix.'mld_resource';
        $resource_settings = $wpdb->get_results( "SELECT * FROM $table_name
        WHERE post_id = $post_id
        " );

        $resource_subject = isset( $resource_settings[0]->resource_subject ) ? $resource_settings[0]->resource_subject : '';
        $resource_type = isset( $resource_settings[0]->resource_type ) ? $resource_settings[0]->resource_type : '';
        $resource_video_link = isset( $resource_settings[0]->resource_video_link ) ? $resource_settings[0]->resource_video_link : '';
        $resource_pdf = isset( $resource_settings[0]->resource_pdf ) ? $resource_settings[0]->resource_pdf : '';
        $resource_exam = isset( $resource_settings[0]->resource_exam ) ? $resource_settings[0]->resource_exam : '';
        $resource_year = isset( $resource_settings[0]->resource_year ) ? intval( $resource_settings[0]->resource_year ) : 0;
        $resource_tier = isset( $resource_settings[0]->resource_tier ) ? $resource_settings[0]->resource_tier : '';
        $resource_test = isset( $resource_settings[0]->resource_test ) ? $resource_settings[0]->resource_test : '';

        $pdf_text = __( 'Add PDF', 'myrtle-learning-dashboard' );
        $pdf_class = 'mld-resource-pdf-class';
        ?>
        <input type="hidden" class="mld_selected_year" value="<?php echo $resource_year; ?>">
        <div class="mld-resource-settings-wrapper">
            <div class="mld-inner-wrap">
                <div class="mld-inner-wrap-title">
                    <?php echo __( 'Select a Subject', 'myrtle-learning-dashboard' ); ?>
                </div>
                <div class="mld-inner-wrap-content">
                    <select name="resource-subject" id="mld-subject-dropdown">
                        <option value=""><?php echo __( 'Select a Subject', 'myrtle-learning-dashboard' ); ?></option>
                        <?php
                        if( $courses_array && is_array( $courses_array ) ) {
                            foreach( $courses_array as $courses ) {
                                ?>
                                <option value="<?php echo $courses; ?>" <?php echo selected( $resource_subject, $courses,true ); ?>><?php echo get_the_title( $courses ); ?></option>
                                <?php
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="mld-clear-both"></div>
            </div>
            <div class="mld-inner-wrap">
                <div class="mld-inner-wrap-title">
                    <?php echo __( 'Select Examboard', 'myrtle-learning-dashboard' ); ?>
                </div>
                <div class="mld-inner-wrap-content">
                    <?php
                    if( ! $resource_exam ) {
                        ?>
                        <select name="mld-resource-exam" id="mld-resource-exam">
                            <option value=""><?php echo __( 'Select a examboard', 'myrtle-learning-dashboard' ); ?></option>
                        </select>
                        <?php
                    } else {
                        ?>
                        <select name="mld-resource-exam" id="mld-resource-exam">
                            <option value=""><?php echo __( 'Select a Examboard', 'myrtle-learning-dashboard' ); ?></option>
                            <?php
                            $subject_exams = $this->mld_get_all_categories( $resource_subject );
                            if( $subject_exams && is_array( $subject_exams ) ) {
                                foreach( $subject_exams as $subject_exam ) {
                                    ?>
                                    <option value="<?php echo $subject_exam; ?>" <?php echo selected( $resource_exam,$subject_exam, true ); ?>><?php echo $subject_exam; ?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                        <?php
                    }
                    ?>
                </div>
                <div class="mld-clear-both"></div>
            </div>
            <div class="mld-inner-wrap">
                <div class="mld-inner-wrap-title">
                    <?php echo __( 'Select a Paper', 'myrtle-learning-dashboard' ); ?>
                </div>
                <div class="mld-inner-wrap-content">
                    <select name="resource-type" id="resource-type-dropdown">
                        <option value=""><?php echo __( 'Select a paper type', 'myrtle-learning-dashboard' ); ?></option>
                        <option value="mark-scheme" <?php echo selected( $resource_type, 'mark-scheme', true ); ?>><?php echo __( 'Mark Scheme', 'myrtle-learning-dashboard' ); ?></option>
                        <option value="question" <?php echo selected( $resource_type, 'question', true ); ?>><?php echo __( 'Question Papers', 'myrtle-learning-dashboard' ); ?></option>
                    </select>
                </div>
                <div class="mld-clear-both"></div>
            </div>
            <div class="mld-inner-wrap">
                <div class="mld-inner-wrap-title">
                    <?php echo __( 'Select Tier', 'myrtle-learning-dashboard' ); ?>
                </div>
                <div class="mld-inner-wrap-content">
                    <select name="resource-tier" id="resource-tier-dropdown">
                        <option value=""><?php echo __( 'Select a tier', 'myrtle-learning-dashboard' ); ?></option>
                        <option value="Foundation" <?php echo selected( $resource_tier, 'Foundation', true ); ?>><?php echo __( 'Foundation', 'myrtle-learning-dashboard' ); ?></option>
                        <option value="higher" <?php echo selected( $resource_tier, 'higher', true ); ?>><?php echo __( 'Higher', 'myrtle-learning-dashboard' ); ?></option>
                    </select>
                </div>
                <div class="mld-clear-both"></div>
            </div>
            <div class="mld-inner-wrap">
                <div class="mld-inner-wrap-title">
                    <?php echo __( 'Select a Year', 'myrtle-learning-dashboard' ); ?>
                </div>
                <div class="mld-inner-wrap-content">
                <select id="mld_years" name="mld-year">
                    <option value=""><?php echo __( 'Select a Year', 'myrtle-learning-dashboard' ); ?></option>
                </select>
                </div>
                <div class="mld-clear-both"></div>
            </div>
            <!-- -->
            <div class="mld-inner-wrap">
                <div class="mld-inner-wrap-title">
                    <?php echo __( 'Select Type', 'myrtle-learning-dashboard' ); ?>
                </div>
                <div class="mld-inner-wrap-content">
                    <select id="mld_type" name="resource-test">
                        <option value=""><?php echo __( 'Select a Type', 'myrtle-learning-dashboard' ); ?></option>
                        <?php 
                        $resource_type = Myrtle_Resource_Template::get_type_dropdown();
                        
                        foreach( $resource_type as $key => $type ) {
                            ?>
                            <option value="<?php echo $key; ?>" <?php echo selected( $resource_test, $key,true ); ?>><?php echo $type; ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </div>
                <div class="mld-clear-both"></div>
            </div>
            <!-- -->
            <div class="mld-inner-wrap">
                <div class="mld-inner-wrap-title">
                    <?php echo __( 'Enter Youtube link', 'myrtle-learning-dashboard' ); ?>
                </div>
                <div class="mld-inner-wrap-content">
                    <input type="text" name="resource-video-link" value="<?php echo $resource_video_link; ?>" placeholder="<?php echo __( 'Enter video link', 'myrtle-learning-dashboard' ); ?>">
                </div>
                <div class="mld-clear-both"></div>
            </div>
            <div class="mld-inner-wrap">
                <div class="mld-inner-wrap-title">
                    <?php echo __( 'Upload PDF', 'myrtle-learning-dashboard' ); ?>
                </div>
                <div class="mld-inner-wrap-content">
                    <div class="mld-pdf">
                        <?php
                        if( $resource_pdf ) {
                            $pdf_text = __( 'Remove PDF', 'myrtle-learning-dashboard' );
                            $pdf_class = 'mld-remove-pdf';
                            ?>
                            <embed src="<?php echo $resource_pdf; ?>" class="mld-pdf-img" type="application/pdf" width="252px" height="266px" />
                            <?php
                        }
                        ?>
                    </div>
                    <a href="#" data-post_id="<?php echo $post_id; ?>" class="<?php echo $pdf_class; ?>"><?php echo $pdf_text; ?></a>
                    <input type="hidden" class="mld-resource-hidden-pdf-class" name="mld-resource-pdf-name" value='<?php echo $resource_pdf; ?>'>
                </div>
                <div class="mld-clear-both"></div>
            </div>
        </div>
        <?php
    }
}

/**
 * Initialize MLD_RESOURCE_MODULE
 */
MLD_RESOURCE_MODULE::instance();