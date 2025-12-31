<?php
/**
 * Resource templates
 */

if( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class Myrtle_Resource_Template
 */
class Myrtle_Resource_Template {

	/**
	 * @var self
	 */
	private static $instance = null;

	/**
	 * @since 1.0
	 * @return $this
	 */
	public static function instance() {

		if ( is_null( self::$instance ) && ! ( self::$instance instanceof Myrtle_Resource_Template ) ) {
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

        add_action( 'wp_enqueue_scripts', [ $this, 'mld_enqueue_scripts' ] );
        add_shortcode( 'my_resources', [ $this, 'mld_my_resources_template' ] );
        add_action( 'wp_ajax_get_resource_courses', [ $this, 'mld_get_resource_courses' ] );
        add_action( 'wp_ajax_get_course_quizess', [ $this, 'mld_get_course_quizess' ] );
        add_action( 'wp_ajax_get_resource', [ $this, 'mld_get_resource' ] );
        add_filter( 'post_row_actions', [ $this, 'mld_add_clone_option' ], 10, 2 );
        add_shortcode( 'download_button', [ $this, 'mld_document_downloadable_button' ] );
	}

    /**
     * create a shortcode to display the downloadable button of document
     */
    public function mld_document_downloadable_button( $atts ) {

        ob_start();

        $document_link = isset( $atts['link'] ) ? $atts['link'] : '';
        $download_text = isset( $atts['title'] ) ? $atts['title'] : __( 'Download Writing Sheet', 'myrtle-learning-dashboard' );

        if( $document_link ) {
            $download = 'download';
        } else {
            $download = '';
        }
        ?>
        <a class="mld-download-able-btn" href="<?php echo $document_link; ?>" <?php echo $download; ?>><?php echo $download_text; ?></a>
        <?php
        $content = ob_get_contents();
        ob_get_clean();
        return $content;
    }

    /**
     * Added clone option
     */
    public function mld_add_clone_option( $actions, $post ) {

        if ($post->post_type == 'mld_resources') {

            $actions['clone'] = '<button type="button" class="mld-resource-clone" data-post_id="'.$post->ID.'">'.__( 'Clone', 'myrtle-learning-dashboard' ).'</button>';
        }

        return $actions;
    }

    /**
     * get resource according to value
     */
    public function mld_get_resource() {

        global $wpdb;

        $response = [];

		if( ! wp_verify_nonce( $_POST['mld_nounce'], 'mld_ajax_nonce' ) ) {

			$response['message'] = __( 'data not found', 'myrtle-learning-dashboard' );
			$response['status'] = 'false';

			echo json_encode( $response );
			wp_die();
		}

        $course_id = isset( $_POST['course_id'] ) ? intval( $_POST['course_id'] ) : 0;
        $quiz_id = isset( $_POST['quiz_id'] ) ? intval( $_POST['quiz_id'] ) : 0;
        $quiz_type = isset( $_POST['paper'] ) ? $_POST['paper'] : '';
        $resource_year = isset( $_POST['year'] ) ? $_POST['year'] : '';
        $resource_tier = isset( $_POST['tier'] ) ? $_POST['tier'] : '';
        $resource_type = isset( $_POST['type'] ) ? $_POST['type'] : '';

        $quiz = '';
        
        if( $quiz_id ) {
            $quiz = "AND resource_exam = $quiz_id";
        }

        $paper = '';
        if( $quiz_type ) {
            $paper = "AND resource_type = '$quiz_type'";
        }

        $year = '';
        if( $resource_year ) {
            $year = "AND resource_year = '$resource_year'";
        }

        $tier = '';
        if( $resource_tier ) {
            $tier = "AND resource_tier = '$resource_tier'";
        }

        $type = '';
        if( $resource_type ) {
            $type = "AND resource_test = '$resource_type'";
        }

        $table_name = $wpdb->prefix.'mld_resource';

        $resources = $wpdb->get_results( "SELECT * FROM $table_name
        WHERE resource_subject = $course_id $quiz $paper $tier $year $type" );

        $content = '';

        if( ! empty( $resources ) && is_array( $resources )  ) {
            ob_start();

            ?>
            <option value=""><?php echo __( 'Select a Resource', 'myrtle-learning-dashboard' ); ?></option>
            <?php
            foreach( $resources as $resource ) {

                $resource_post_status = get_post_status( $resource->post_id );
                if( 'publish' != $resource_post_status ) {
                    continue;
                }
                ?>
                <option value="<?php echo $resource->post_id; ?>" data-title="<?php echo ucwords( get_the_title( $resource->post_id ) ) ;?>" data-video_url="<?php echo $resource->resource_video_link; ?>" data-pdf_url="<?php echo $resource->resource_pdf; ?>"><?php echo get_the_title( $resource->post_id ); ?></option>
                <?php
            }

            $content = ob_get_contents();
            ob_get_clean();
        }

        if( $course_id ) {
            $response['content'] = $content;
        } else {
            $response['content'] = '<option value="">No resource found</option>';
        }

        $response['status'] = 'true';

        echo json_encode( $response );
        wp_die();
    }

    /**
     * get course quizzess
     */
    public function mld_get_course_quizess() {

        $response = [];

		if( ! wp_verify_nonce( $_POST['mld_nounce'], 'mld_ajax_nonce' ) ) {

			$response['message'] = __( 'data not found', 'myrtle-learning-dashboard' );
			$response['status'] = 'false';

			echo json_encode( $response );
			wp_die();
		}

        $course_id = isset( $_POST['course_id'] ) ? intval( $_POST['course_id'] ) : 0;

        if( empty( $course_id ) ) {

            $response['message'] = __( 'course id not found', 'myrtle-learning-dashboard' );
			$response['status'] = 'false';

			echo json_encode( $response );
			wp_die();
        }

        $subject_exams = get_terms( array(
            'taxonomy' => 'ld_course_category',
            'object_ids' => $course_id
        ) );

        $course_categories = [];

        if( $subject_exams && is_array( $subject_exams ) ) {
            foreach( $subject_exams as $subject_exam ) {
                $course_categories[] = $subject_exam->name;
            }
        }

        ob_start();

        if( $course_categories && is_array( $course_categories ) ) {
            ?>
            <option value=""><?php echo __( 'Select a category', 'myrtle-learning-dashboard' ); ?></option>
            <?php
            foreach( $course_categories as $course_category ) {
                ?>
                <option value="<?php echo $course_category; ?>"><?php echo $course_category; ?></option>
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
     * get all courses related to tag
     */
    public function mld_get_resource_courses() {

        $response = [];

		if( ! wp_verify_nonce( $_POST['mld_nounce'], 'mld_ajax_nonce' ) ) {

			$response['message'] = __( 'data not found', 'myrtle-learning-dashboard' );
			$response['status'] = 'false';

			echo json_encode( $response );
			wp_die();
		}

        $term_id = isset( $_POST['term_id'] ) ? intval( $_POST['term_id'] ) : 0;

        if( empty( $term_id ) ) {

            $response['message'] = __( 'term id not found', 'myrtle-learning-dashboard' );
			$response['status'] = 'false';

			echo json_encode( $response );
			wp_die();
        }

        $args = array(
            'post_type' => 'sfwd-courses',
            'posts_per_page' => -1,
            'tax_query' => array(
                array(
                'taxonomy' => 'ld_course_tag',
                'field' => 'id',
                'terms' => $term_id
                 )
            )
        );

        $posts = get_posts( $args );

        ob_start();

        if( $posts && is_array( $posts ) ) {
            ?>
            <option value=""><?php echo __( 'Select a Subject', 'myrtle-learning-dashboard' ); ?></option>
            <?php
            foreach( $posts as $post ) {
                $post_id = $post->ID;
                ?>
                <option value="<?php echo $post_id;?>"><?php echo get_the_title($post_id); ?></option>
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
     * create a function for resource table
     */
    public static function mld_get_resource_data( $resource_data ) {
        ob_start();
        ?>
        <table class="mld-resaurce-table">
            <thead>
                <tr>
                    <td><?php echo __( 'NO', 'myrtle-learning-dashboard' ); ?></td>
                    <td><?php echo __( 'Resource Title', 'myrtle-learning-dashboard' ); ?></td>
                    <td><?php echo __( 'Resource Subject', 'myrtle-learning-dashboard' ); ?></td>
                    <td><?php echo __( 'Resource Type', 'myrtle-learning-dashboard' ); ?></td>
                    <td><?php echo __( 'Download', 'myrtle-learning-dashboard' ); ?></td>
                </tr>
            </thead>
            <?php
            $no = 0;
            foreach( $resource_data as $resource ) {
                $no++;
                ?>
                <tr>
                    <td><?php echo $no; ?></td>
                    <td><?php echo $resource->resource_title; ?></td>
                    <td><?php echo $resource->resource_subject; ?></td>
                    <td><?php echo $resource->resource_object; ?></td>
                    <td class="mld-resource-youtube-video" data_video-url="<?php echo $resource->resource_video_link; ?>" data_title="<?php echo ucwords( $resource->resource_title ); ?>" mld-post_permalink="<?php echo get_the_permalink( $resource->post_id ); ?>"><?php echo __( 'Download', 'myrtle-learning-dashboard' ); ?></td>
                    <td><a href="<?php echo $resource->resource_pdf; ?>" download><?php echo __( 'PDF', 'myrtle-learning-dashboard' ); ?></a></td>
                </tr>
                <?php
            }
                ?>
        </table>
        <?php

        $content = ob_get_contents();
        ob_get_clean();
        return $content;
    }

    /**
     * creare  function to get resource shortcode html
     */
    public static function mld_resource_shortcode_function() {

        global $wpdb;
        $table_name = $wpdb->base_prefix . 'mld_resource';
        $resource_year = $wpdb->get_results( "SELECT DISTINCT resource_year FROM $table_name" );

        $tags = get_terms( array(
            'taxonomy' => 'ld_course_tag'
        ) );

        require_once MLD_TEMPLATES_DIR . 'resource-template.php';
    }

    /**
     * create a My resources template
     */
    public function mld_my_resources_template() {
        
        global $wpdb;
        
        $table_name = $wpdb->base_prefix . 'mld_resource';
        $resource_year = $wpdb->get_results( "SELECT DISTINCT resource_year FROM $table_name" );

        $tags = get_terms( array(
            'taxonomy' => 'ld_course_tag'
        ) );
        ob_start();

        require_once MLD_TEMPLATES_DIR . 'resource-template-shortcode.php';

        $content = ob_get_contents();
        ob_get_clean();
        return $content;

    }

    /**
     * enqueue scripts
     */
    public function mld_enqueue_scripts() {

        $rand = rand( 1000000, 1000000000 );
        wp_enqueue_script( 'resource-frontend', MLD_ASSETS_URL . 'js/frontend-resource.js', [ 'jquery' ], $rand, true );
        wp_enqueue_style( 'mld-resource-frontend-css', MLD_ASSETS_URL . 'css/frontend-resource.css', [], $rand, null );
    }

    /**
     * create a function to get type dropdown
     */
    public static function get_type_dropdown() {

        $type_array = [
            'specimen-papers'           => 'Specimen Papers',
            'practice-papers'           => 'Practice Papers',
            'shading-answer-sheet'      => 'Shading Answer Sheets',
            'test-1'                    => 'Test 1',
            'test-2'                    => 'Test 2',
            'test-3'                    => 'Test 3',
            'test-4'                    => 'Test 4',
            'test-5'                    => 'Test 5',
            'test-6'                    => 'Test 6',
            'test-7'                    => 'Test 7',
            'test-8'                    => 'Test 8',
            'test-9'                    => 'Test 9',
            'test-10'                   => 'Test 10',
            'test-11'                   => 'Test 11',
            'test-12'                   => 'Test 12',
            'test-13'                   => 'Test 13',
            'test-14'                   => 'Test 14',
            'test-15'                   => 'Test 15',
            'test-16'                   => 'Test 16',
            'test-17'                   => 'Test 17',
            'test-18'                   => 'Test 18',
            'test-19'                   => 'Test 19',
            'test-20'                   => 'Test 20',
            'test-21'                   => 'Test 21',
            'test-22'                   => 'Test 22',
            'test-23'                   => 'Test 23',
            'test-24'                   => 'Test 24',
            'test-25'                   => 'Test 25',
            'test-26'                   => 'Test 26',
            'test-27'                   => 'Test 27',
            'test-28'                   => 'Test 28',
            'test-29'                   => 'Test 29',
            'test-30'                   => 'Test 30',
            'test-31'                   => 'Test 31',
            'test-32'                   => 'Test 32',
            'test-33'                   => 'Test 33',
            'test-34'                   => 'Test 34',
            'test-35'                   => 'Test 35',
            'test-36'                   => 'Test 36',
            'test-37'                   => 'Test 37',
            'test-38'                   => 'Test 38',
            'test-39'                   => 'Test 39',
            'test-40'                   => 'Test 40',
        ];
        return $type_array;
    }
}

Myrtle_Resource_Template::instance();