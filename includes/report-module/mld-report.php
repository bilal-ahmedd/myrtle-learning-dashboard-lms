<?php
/**
 * Myrtle Learning - Admin Hooks
 *
 */
if( ! defined( 'ABSPATH' ) ) exit;

class MLD_REPORT_MODULE {

	private static $instance;

	/**
	 * Create class instance
	 */
	public static function instance() {

		if( is_null( self::$instance ) && ! ( self::$instance instanceof MLD_REPORT_MODULE ) ) {

			self::$instance = new MLD_REPORT_MODULE;
			self::$instance->hooks();
			self::$instance->includes();
		}

		return self::$instance;
	}

	/**
	 * include files
	 */
	private function includes() {
		require_once MLD_INCLUDES_DIR . 'report-module/report-template.php';
	}

	/**
	 * Define hooks
	 */
	private function hooks() {
		add_shortcode( 'myrtle_report', [ $this, 'mld_myrtle_report' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'mld_report_scripts' ] );
		add_action( 'init', [ $this, 'mld_download_report' ] );
	}

	/**
	 * download report
	 */
	public function mld_download_report() {

		$course_id = isset( $_GET['mld_c_id'] ) ? $_GET['mld_c_id'] : 0;
		$group_id = isset( $_GET['mld_group_id'] ) ? $_GET['mld_group_id'] : 0;
		$start_date = isset( $_GET['mld_start_date'] ) ? $_GET['mld_start_date'] : 0;
		$end_date = isset( $_GET['mld_end_date'] ) ? $_GET['mld_end_date'] : 0;
		$user_id = isset( $_GET['user_id'] ) ? $_GET['user_id'] : 0;

		if( ! $group_id ) {
			return;
		}

		if( $group_id && $course_id ) {
			$pdf_content = Myrtle_Report_Template::mld_get_course_table( $group_id, $course_id, false, $start_date, $end_date );
		}

		if( $group_id && ! $course_id ) {
			$pdf_content = Myrtle_Report_Template::mld_get_group_table( $group_id, false, $start_date, $end_date, $user_id );
		}

        $page_height = strlen( $pdf_content ) / 10;
        $pdf_page_format = PDF_PAGE_FORMAT;
        // $pdf_page_format = 'A4';
        $pdf = new TCPDF($pdf_page_format, PDF_UNIT, $pdf_page_format, true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor( 'LRC' );
        $pdf->SetTitle( 'LRC Course Outline' );
        $pdf->SetSubject( 'LRC Outline' );
        // set header and footer fonts
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        $pdf->setHeaderData( '', 0, '', '', [ 0, 0, 0 ], [ 255, 255, 255 ] );
        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        // set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        // add a page
        $pdf->AddPage();
        // output the HTML content
        $pdf->writeHTML( $pdf_content, true, false, true, false, '' );
        // reset pointer to the last page
        $pdf->lastPage();
        ob_clean();
        //Close and output PDF document
        $pdf->Output( 'pdf_course_outline', 'I' );
        die();
	}

	/**
	 * enqueue report script
	 */
	public function mld_report_scripts() {

		wp_enqueue_editor();
		wp_enqueue_media();

		$rand = rand( 1000000, 1000000000 );

		wp_enqueue_style( 'popup-external-select-min-css', MLD_ASSETS_URL .'css/select2.min.css' );
		wp_enqueue_script( 'popup-external-select2-jquery-js', MLD_ASSETS_URL. 'js/select2.full.min.js', ['jquery'], $rand, true );

		wp_enqueue_style( 'report-2-css', MLD_ASSETS_URL .'css/report_2.css', '', $rand, false );
		wp_enqueue_style( 'popup-external-select-min-css', MLD_ASSETS_URL .'css/select2.min.css', '', $rand, false );
		wp_enqueue_script( 'popup-external-select2-jquery-js', MLD_ASSETS_URL. 'js/select2.full.min.js', ['jquery'], $rand, true );

		wp_enqueue_script( 'msp-backend', 'https://cdn.jsdelivr.net/npm/chart.js@4.2.1/dist/chart.umd.min.js', [ 'jquery' ], $rand, true );
		wp_register_script( 'mld-report-js', MLD_ASSETS_URL . 'js/report.js', [ 'jquery' ], $rand, true );
		wp_enqueue_script( 'msp-datalabels-js', 'https://cdnjs.cloudflare.com/ajax/libs/chartjs-plugin-datalabels/2.2.0/chartjs-plugin-datalabels.min.js', [ 'jquery' ], $rand, true );
		wp_enqueue_script( 'mld-report-js' );
		wp_enqueue_script( 'mld-chart-js' );

		wp_localize_script( 'mld-report-js', 'MLD', [
			'ajaxURL'       => admin_url( 'admin-ajax.php' ),
			'security'      => wp_create_nonce( 'mld_ajax_nonce' ),
			'siteURL'		=> site_url()
		] );

		/**
		 * Guage chart file
		 */
    	wp_enqueue_script( 'mld-core-js', 'https://cdn.amcharts.com/lib/4/core.js', [ 'jquery' ], $rand, true );
    	wp_enqueue_script( 'mld-charts-js', 'https://cdn.amcharts.com/lib/4/charts.js', [ 'jquery' ], $rand, true );
    	wp_enqueue_script( 'mld-theme-js', 'https://cdn.amcharts.com/lib/4/themes/animated.js', [ 'jquery' ], $rand, true );
	}

	/**
	 * create a function to get option according array
	 *
	 * @param $array
	 */
	public function get_options( $array ) {

		if( empty( $array ) || ! is_array( $array ) ) {
			return;
		}

		ob_start();
		foreach( $array as $arr ) {
			?>
			<option value="<?php echo $arr; ?>"><?php echo get_the_title( $arr ); ?></option>
			<?php
		}

		$content = ob_get_contents();
		ob_get_clean();
		return $content;
 	}

	/**
	 * create a shortcode to display the report
	 */
	public function mld_myrtle_report() {

		if( ! is_user_logged_in() ) {
			return;
		}

		$user_id = get_current_user_id();

		if ( learndash_is_group_leader_user( $user_id ) === true || current_user_can( 'manage_options' ) ) {
			$is_subscriber = 0;
			if( current_user_can( 'manage_options' ) ) {
				$groups = mld_get_groups_for_admin();
			} else {
				$groups = mld_get_groups_for_leader( $user_id );
			}
		
			$course_dropdown = 'inline-block';
		} else {
			$is_subscriber = $user_id;
			$groups = mld_get_user_groups( $user_id );
			$course_dropdown = 'none';
		}

		if( empty( $groups ) || ! is_array( $groups ) ) {
			return __( 'You are not enrolled to any group', 'myrtle-learning-dashboard' );
		}

		$c_u_name = ucwords( mld_get_username( $user_id ) );
		$c_avatar_url = get_avatar_url( $user_id );
		ob_start();
		?>
		<input type="hidden" class="mld-current-u-name" value="<?php echo $c_u_name; ?>">
		<input type="hidden" class="mld-current-u-avatar" value="<?php echo $c_avatar_url; ?>">
		<div class="mld-report-warpper">
			<input type="hidden" value="<?php echo $is_subscriber; ?>" class="mld-user-role">
			<div class="mld-main-report-wrapper">
				<div class="mld-report-group-wrapper">
					<div class="mld-group-title"><?php echo __( 'Select Group *', 'myrtle-learning-dashboard' ); ?></div>
					<div>
						<select name="mld-groups" class="mld-groups-dropdown">
							<option value=""><?php echo __( 'Please select group', 'myrtle-learning-dashboard' ); ?></option>
							<?php
							echo $this->get_options( $groups );
							?>
						</select>
					</div>
				</div>
				<div class="mld-report-courses-wrapper" style="display: <?php echo $course_dropdown; ?>;">
					<div class="mld-report-title">
						<?php echo __( 'Select a course *', 'myrtle-learning-dashboard' ); ?>
					</div>
					<div>
						<select name="mld-courses" class="mld-courses-dropdown" disabled="disabled">
							<option selected="selected"><?php echo __( 'Select a course', 'myrtle-learning-dashboard' ); ?></option>
						</select>
					</div>
				</div>
				<div class="mld-start-date-wrapper">
					<div class="mld-start-date-title">
						<?php echo __( 'Start Date', 'myrtle-learning-dashboard' ); ?>
					</div>
					<div>
						<input type="date" class="mld-start-date">
					</div>
				</div>
				<div class="mld-end-date-wrapper">
					<div class="mld-start-date-title">
						<?php echo __( 'End Date', 'myrtle-learning-dashboard' ); ?>
					</div>
					<div>
						<input type="date" class="mld-end-date">
					</div>
				</div>
			</div>
			<input type="button" class="mld-report-submit" disabled="disabled" value="<?php echo __( 'Apply', 'myrtle-learning-dashboard' ); ?>">
			<img src="<?php echo MLD_ASSETS_URL.'images/spinner.gif' ?>" class="mld-report-loader">
		</div>
		<div class="mld-report-data">
		</div>
		<?php
		$content = ob_get_contents();
		ob_get_clean();
		return $content;
	}
}

/**
 * Initialize MLD_REPORT_MODULE
 */
MLD_REPORT_MODULE::instance();

/**
 * create full pdf
 */
add_action( 'wp', 'wpe_generate_certificate' );
function wpe_generate_certificate() {

		$start_date = isset( $_GET['mld_start_date'] ) ? $_GET['mld_start_date'] : 0;
		$end_date = isset( $_GET['mld_end_date'] ) ? $_GET['mld_end_date'] : 0;
		$group_id = isset( $_GET['group_id'] ) ? $_GET['group_id'] : 0;
		$user_id = isset( $_GET['user_id'] ) ? $_GET['user_id'] : 0;
		$course_id = isset( $_GET['course_id'] ) ? $_GET['course_id'] : 0;
		$actual_academic_comment = [ 'Review topic before lesson', 'Complete classwork-High Standard', 'Complete Homework-High Standard', 'Complete Corrections-Uploaded' ];
		$actual_behaviour_comment = [ 'Excellent attendance', 'Ready and on time for each lesson', 'Fully engaged and focused in lessons', 'Complete all tasks' ];
		$academic_comments = get_user_meta( $user_id, 'mld_academic_comment_'.$group_id.'_'.$course_id, true );
		$group_administrators = mld_get_group_leaders( $group_id );
		$group_courses_name = mld_get_group_courses( $group_id );
		$selected_course = isset( $_GET['mld_included_courses'] ) ? $_GET['mld_included_courses'] : [];
		$is_academic_comment = isset( $_GET['mld_academic'] ) ? $_GET['mld_academic'] : '';
		$is_behaviour_comment = isset( $_GET['mld_behaviour'] ) ? $_GET['mld_behaviour'] : '';

		if( ! empty( $selected_course ) ) {
			$group_courses_name = explode( ',', $selected_course );	
		}

		$administrator_name = [];
		if( $group_administrators && is_array( $group_administrators ) ) {
			foreach( $group_administrators as $group_administrator ) {
				$administrator_name[] = mld_get_username( $group_administrator );
			}
		}

		$group_courses_list_name = [];

		if( $group_courses_name && is_array( $group_courses_name ) ) {
			foreach( $group_courses_name as $courses_name ) {
				$group_courses_list_name[] = get_the_title( $courses_name );
			}
		}

		if( ! $academic_comments ) {
			$academic_comments = [];
		}

		$behaviour_comments = get_user_meta( $user_id, 'mld_behavior_comment_'.$group_id.'_'.$course_id, true );

		if( ! $behaviour_comments ) {
			$behaviour_comments = [];
		}

		if( ! $group_id || ! $user_id ) {
			return;
		}
		require_once MLD_INCLUDES_DIR . '/lib/TCPDF-main/tcpdf.php';
		class MYPDF extends TCPDF {

			public function Header() {

				$this->Rect(0, 0, $this->getPageWidth(),$this->getPageHeight(), 'DF', "",  array(51,87, 33));
			}

			public function Footer() {

				// Position at 15 mm from bottom
				$this->SetY(-15);
				// Set font
				$this->SetFont('helvetica', 'I', 8);
				// Page number
				$this->Cell(0, 10, '------------------ www.myrtlelearning.com ------------------', 0, false, 'C', 0, '', 0, false, 'T', 'M');
			}
		}

		ob_start();
		?>
		<div>
			<img style="text-align: center;" src="https://myrtlelearning.com/wp-content/uploads/2022/12/white-logo-3.png">
			<h1 style="color: white; text-align: center;"><?php echo __( 'Progress Report', 'myrtle-learning-dashboard' ); ?></h1>
			<div>
				<table class="mld-full-report-header" style=" border: 5px solid white; background-color: white; color: #18440a;" cellspacing="0" cellpadding="10">
					<thead>
						<tr>
							<th>Student Name : <?php echo ucwords( mld_get_username( $user_id ) ); ?></th>
							<th>Teacher(s): <?php echo implode( ',', $administrator_name ); ?></th>
						</tr>
						<tr>
							<th>Course(s): <?php echo implode( ',', $group_courses_list_name ); ?></th>
							<th>School Year:</th>
						</tr>
					</thead>
				</table>
			</div>

			<table cellspacing="0" cellpadding="10" style="border: 5px solid white;">
				<thead>
					<tr style="background-color: #18440a; color: white;">
						<th style="text-align: center;"><?php echo __( 'Course', 'myrtle-learning-dashboard' ); ?></th>
						<th style="text-align: center;"><?php echo __( 'Average Score', 'myrtle-learning-dashboard' ); ?></th>
						<th style="text-align: center;"><?php echo __( 'Target Score', 'myrtle-learning-dashboard' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					$no = 0;
					$inclued_cour = isset( $_GET['mld_included_courses'] ) ? explode( ',', $_GET['mld_included_courses'] ) : [];
					$group_courses = mld_get_group_courses( $group_id );
						
					if( $inclued_cour ) {
						$group_courses = $inclued_cour;	
					}

					if( $group_courses ) {

						foreach( $group_courses as $group_course ) {
							$no++;
							$color_name = '#efefef';
							if( $no % 2 == 0 ) {
								$color_name = '#c1cbc8';
							}

							$check_user_access = sfwd_lms_has_access( $group_course, $user_id );

							if( false == $check_user_access ) {
								continue;
							}
							?>
							<tr style="background-color: <?php echo $color_name; ?>; color: #18440a;">
								<td style="text-align: center;"><?php echo get_the_title( $group_course ); ?></td>
								<td style="text-align: center;"><?php echo Myrtle_Report_Template::get_course_progress( $user_id, $group_course, $start_date, $end_date ).'%' ?></td>
								<td style="text-align: center;"><?php echo Myrtle_Report_Template::get_course_target_score( $group_course ); ?></td>
							</tr>
							<?php
						}
					}
					?>
				</tbody>
			</table>
			<table cellspacing="0" cellpadding="10" style="border: 5px solid white;">
				<thead>
					<tr>
						<?php
						if( 'yes' == $is_academic_comment && ( ! $is_behaviour_comment || 'no' == $is_behaviour_comment ) ) {
							?>
							<th width="100%" style="border:5px solid white; background-color: #18440a; color: white; text-align: center;"><?php echo __( 'Academic', 'myrtle-learning-dashboard' ); ?></th>	
							<?php
						} else if( 'yes' == $is_behaviour_comment && ( ! $is_academic_comment || 'no' == $is_academic_comment ) ) {
							?>
							<th width="100%" style="border:5px solid white; background-color: #18440a; color: white; text-align: center;"><?php echo __( 'Behaviour', 'myrtle-learning-dashboard' ); ?></th>
							<?php
						} else {
							?>
							<th width="49%" style="border:5px solid white; background-color: #18440a; color: white; text-align: center;"><?php echo __( 'Academic', 'myrtle-learning-dashboard' ); ?></th>
							<th width="2%" style="border:5px solid white; background-color: white;"></th>
							<th width="49%" style="border:5px solid white; background-color: #18440a; color: white; text-align: center;"><?php echo __( 'Behaviour', 'myrtle-learning-dashboard' ); ?></th>
							<?php
						}
						?>
					</tr>
				</thead>
				<tbody>
					<tr>
						<?php 
						if( 'yes' == $is_academic_comment && ( ! $is_behaviour_comment || 'no' == $is_behaviour_comment ) ) {
							?>
							<td width="100%" style="background-color: #18440a; text-align: center; color: white;"><?php echo __( 'What Went Well', 'myrtle-learning-dashboard' ); ?></td>
							<?php
						} else if( 'yes' == $is_behaviour_comment && ( ! $is_academic_comment || 'no' == $is_academic_comment ) ) {
							?>
							<td width="100%" style="background-color: #18440a; text-align: center; color: white;"><?php echo __( 'What Went Well', 'myrtle-learning-dashboard' ); ?></td>
							<?php
						} else {
							?>
							<td width="49%" style="background-color: #18440a; text-align: center; color: white;"><?php echo __( 'What Went Well', 'myrtle-learning-dashboard' ); ?></td>
							<td width="2%" style="background-color: white;"></td>
							<td width="49%" style="background-color: #18440a; text-align: center; color: white;"><?php echo __( 'What Went Well', 'myrtle-learning-dashboard' ); ?></td>
							<?php
						}
						?>
					</tr>
					<tr>
						<?php 

						if( 'yes' == $is_academic_comment && ( ! $is_behaviour_comment || 'no' == $is_behaviour_comment ) ) {
							
							?>
							<td style="background-color: #f8f9fd; color: #18440a; text-align: center;">
								<table>
									<thead>
										<?php
										if( ! empty( $group_courses_name ) && is_array( $group_courses_name ) ) {
											foreach( $group_courses_name as $group_course ) {
												?>
												<tr>
													<td><h3><?php echo get_the_title( $group_course ).'( Course )'; ?></h3></td>
												</tr>
												<?php
												$academic_comments = get_user_meta( $user_id, 'mld_academic_comment_'.$group_id.'_'.$group_course, true );
												if( ! empty( $academic_comments ) && is_array( $academic_comments ) ) {

													foreach( $academic_comments as $academic_comment ) {
														
														?>
														<tr style="background-color: #f8f9fd;">
															<td><?php echo $academic_comment; ?></td>
														</tr>
														<?php
													}
												} else {
													?>
													<tr>
														<td><?php echo __( 'No comment found', 'myrtle-learning-dashboard' ); ?></td>
													</tr>
													<?php
												}
											}
										}
										?>
									</thead>
								</table>
							</td>
							<?php
						} else if( 'yes' == $is_behaviour_comment && ( ! $is_academic_comment || 'no' == $is_academic_comment ) ) {
							
							?>
							<td style="background-color: #f8f9fd; color: #18440a; text-align: center;">
								<table>
									<thead>
										<?php
										if( ! empty( $group_courses_name ) && is_array( $group_courses_name ) ) {
											foreach( $group_courses_name as $group_courses ) {

												?>
												<tr>
													<td><h3><?php echo get_the_title( $group_course ).'( Course )'; ?></h3></td>
												</tr>
												<?php
												$behaviour_comments = get_user_meta( $user_id, 'mld_behavior_comment_'.$group_id.'_'.$group_courses, true );

												if( ! empty( $behaviour_comments ) && is_array( $behaviour_comments ) ) {

													foreach( $behaviour_comments as $behaviour_comment ) {
														?>
														<tr style="background-color: #f8f9fd;">
															<td><?php echo $behaviour_comment; ?></td>
														</tr>
														<?php
													}
												} else {
													?>
													<tr>
														<td><?php echo __( 'No comment found', 'myrtle-learning-dashboard' ); ?></td>
													</tr>
													<?php
												}
											}
										}
										?>
									</thead>
								</table>
							</td>
							<?php
						} else {
							?>
							<td style="background-color: #f8f9fd; color: #18440a; text-align: center;">
								<table>
									<thead>
										<?php
										if( ! empty( $group_courses_name ) && is_array( $group_courses_name ) ) {
											foreach( $group_courses_name as $group_course ) {
												?>
												<tr>
													<td><h3><?php echo get_the_title( $group_course ).'( Course )'; ?></h3></td>
												</tr>
												<?php
												$academic_comments = get_user_meta( $user_id, 'mld_academic_comment_'.$group_id.'_'.$group_course, true );
												
												if( ! empty( $academic_comments ) && is_array( $academic_comments ) ) {

													foreach( $academic_comments as $academic_comment ) {
														?>
														<tr style="background-color: #f8f9fd;">
															<td><?php echo $academic_comment; ?></td>
														</tr>
														<?php
													}
												} else {
													?>
													<tr>
														<td><?php echo __( 'No comment found', 'myrtle-learning-dashboard' ); ?></td>
													</tr>
													<?php
												}
											}
										}
										?>
									</thead>
								</table>
							</td>
							<td style="background-color: white;"></td>
							<td style="background-color: #f8f9fd; color: #18440a; text-align: center;">
								<table>
									<thead>
										<?php
										if( ! empty( $group_courses_name ) && is_array( $group_courses_name ) ) {
											foreach( $group_courses_name as $group_courses ) {

												?>
												<tr>
													<td><h3><?php echo get_the_title( $group_course ).'( Course )'; ?></h3></td>
												</tr>
												<?php
												$behaviour_comments = get_user_meta( $user_id, 'mld_behavior_comment_'.$group_id.'_'.$group_courses, true );

												if( ! empty( $behaviour_comments ) && is_array( $behaviour_comments ) ) {

													foreach( $behaviour_comments as $behaviour_comment ) {
														?>
														<tr style="background-color: #f8f9fd;">
															<td><?php echo $behaviour_comment; ?></td>
														</tr>
														<?php
													}
												} else {
													?>
													<tr>
														<td><?php echo __( 'No comment found', 'myrtle-learning-dashboard' ); ?></td>
													</tr>
													<?php
												}
											}
										}
										?>
									</thead>
								</table>
							</td>
							<?php
						}
						?>
					</tr>
					<tr style="border:10px solid white;">
						<?php 
						if( 'yes' == $is_academic_comment && ( ! $is_behaviour_comment || 'no' == $is_behaviour_comment ) ) {
							?>
							<td width="100%" style="background-color: #18440a; text-align: center; color: white;"><?php echo __( 'Even If Better', 'myrtle-learning-dashboard' ); ?></td>
							<?php
						} else if( 'yes' == $is_behaviour_comment && ( ! $is_academic_comment || 'no' == $is_academic_comment ) ) {
							?>
							<td width="100%" style="background-color: #18440a; text-align: center; color: white;"><?php echo __( 'Even If Better', 'myrtle-learning-dashboard' ); ?></td>
							<?php
						} else {
							?>
							<td width="49%" style="background-color: #18440a; text-align: center; color: white;"><?php echo __( 'Even If Better', 'myrtle-learning-dashboard' ); ?></td>
							<td width="2%" style="background-color: white;"></td>
							<td width="49%" style="background-color: #18440a; text-align: center; color: white;"><?php echo __( 'Even If Better', 'myrtle-learning-dashboard' ); ?></td>
							<?php
						}
						?>
					</tr>
					<tr>
						<?php 
						if( 'yes' == $is_academic_comment && ( ! $is_behaviour_comment && 'no' == $is_behaviour_comment ) ) {
							?>
							<td style="background-color: #f8f9fd; color: #18440a; text-align: center;">
								<table>
									<thead>
										<?php
										if( ! empty( $group_courses_name ) && is_array( $group_courses_name ) ) {

											foreach( $group_courses_name as $group_course ) {

												?>
												<tr>
													<td><h3><?php echo get_the_title( $group_course ).'( Course )'; ?></h3></td>
												</tr>
												<?php

												$diffrence_academic_comments = get_user_meta( $user_id, 'mld_approved_academic_comment_'.$group_id.'_'.$group_course, true );
												
												if( ! $diffrence_academic_comments ) {
													$diffrence_academic_comments = [];
												}

												if( ! empty( $diffrence_academic_comments ) && is_array( $diffrence_academic_comments ) ) {

													foreach( $diffrence_academic_comments as $diffrence_academic_comment ) {
														?>
														<tr style="background-color: #f8f9fd;">
															<td><?php echo $diffrence_academic_comment; ?></td>
														</tr>
														<?php
													}
												} else {
													?>
													<tr>
														<td><?php echo __( 'No comment found', 'myrtle-learning-dashboard' ); ?></td>
													</tr>
													<?php
												}
											}
										}
										?>
									</thead>
								</table>
							</td>
							<?php
						} else if( 'yes' == $is_behaviour_comment && ( ! $is_academic_comment || 'no' == $is_academic_comment ) ) {
							?>
							<td style="background-color: #f8f9fd; color: #18440a; text-align: center;">
								<table>
									<thead>
										<?php
										if( ! empty( $group_courses_name ) && is_array( $group_courses_name ) ) {
											foreach( $group_courses_name as $group_course ) {

												?>
												<tr>
													<td><h3><?php echo get_the_title( $group_course ).'( Course )'; ?></h3></td>
												</tr>
												<?php

												$diffrence_behaviour_comments = get_user_meta( $user_id, 'mld_approved_behavior_comment_'.$group_id.'_'.$group_course, true );

												if( ! $diffrence_behaviour_comments ) {
													$diffrence_behaviour_comments = [];
												}

												if( ! empty( $diffrence_behaviour_comments ) && is_array( $diffrence_behaviour_comments ) ) {

													foreach( $diffrence_behaviour_comments as $diffrence_behaviour_comment ) {
														?>
														<tr>
															<td><?php echo $diffrence_behaviour_comment; ?></td>
														</tr>
														<?php
													}
												} else {
													?>
													<tr>
														<td><?php echo __( 'No comment found', 'myrtle-learning-dashboard' ); ?></td>
													</tr>
													<?php
												}
											}
										}
										?>
									</thead>
								</table>
							</td>
							<?php
						} else {
							?>
							<td style="background-color: #f8f9fd; color: #18440a; text-align: center;">
								<table>
									<thead>
										<?php
										if( ! empty( $group_courses_name ) && is_array( $group_courses_name ) ) {

											foreach( $group_courses_name as $group_course ) {

												?>
												<tr>
													<td><h3><?php echo get_the_title( $group_course ).'( Course )'; ?></h3></td>
												</tr>
												<?php
												$diffrence_academic_comments = get_user_meta( $user_id, 'mld_approved_academic_comment_'.$group_id.'_'.$group_course, true );													
												if( ! $diffrence_academic_comments ) {
													$diffrence_academic_comments = [];
												}

												if( ! empty( $diffrence_academic_comments ) && is_array( $diffrence_academic_comments ) ) {

													foreach( $diffrence_academic_comments as $diffrence_academic_comment ) {
														?>
														<tr style="background-color: #f8f9fd;">
															<td><?php echo $diffrence_academic_comment; ?></td>
														</tr>
														<?php
													}
												} else {
													?>
													<tr>
														<td><?php echo __( 'No comment found', 'myrtle-learning-dashboard' ); ?></td>
													</tr>
													<?php
												}
											}
										}
										?>
									</thead>
								</table>
							</td>
							<td style="background-color: white;"></td>
							<td style="background-color: #f8f9fd; color: #18440a; text-align: center;">
								<table>
									<thead>
										<?php
										if( ! empty( $group_courses_name ) && is_array( $group_courses_name ) ) {
											foreach( $group_courses_name as $group_course ) {

												?>
												<tr>
													<td><h3><?php echo get_the_title( $group_course ).'( Course )'; ?></h3></td>
												</tr>
												<?php

												$diffrence_behaviour_comments = get_user_meta( $user_id, 'mld_approved_behavior_comment_'.$group_id.'_'.$group_course, true );
												if( ! $diffrence_behaviour_comments ) {
													$diffrence_behaviour_comments = [];
												}

												if( ! empty( $diffrence_behaviour_comments ) && is_array( $diffrence_behaviour_comments ) ) {

													foreach( $diffrence_behaviour_comments as $diffrence_behaviour_comment ) {
														?>
														<tr>
															<td><?php echo $diffrence_behaviour_comment; ?></td>
														</tr>
														<?php
													}
												} else {
													?>
													<tr>
														<td><?php echo __( 'No comment found', 'myrtle-learning-dashboard' ); ?></td>
													</tr>
													<?php
												}
											}
										}
										?>
									</thead>
								</table>
							</td>
							<?php
						}
						?>
					</tr>
				</tbody>
			</table>
		</div>
		<?php
		$content = ob_get_contents();
		ob_get_clean();

		$page_height = strlen( $content ) / 10;
        $pdf_page_format = PDF_PAGE_FORMAT;
        $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, $pdf_page_format, true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor( 'LRC' );
        $pdf->SetTitle( 'LRC Course Outline' );
        $pdf->SetSubject( 'LRC Outline' );
        // set header and footer fonts
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        $pdf->setHeaderData( '', 0, '', '', [ 0, 0, 0 ], [ 255, 255, 255 ] );
        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        // set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        // add a page
        $pdf->AddPage();
        // output the HTML content
        $pdf->writeHTML( $content, true, false, true, false, '' );
        // reset pointer to the last page
        $pdf->lastPage();
        ob_clean();
        //Close and output PDF document
        $pdf->Output( 'pdf_course_outline', 'I' );
        die();
}