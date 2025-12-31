<div class="mld-account-main-wrapper">
	<div class="mld-account-inner-wrapper">
		<div class="mld-account-f-row">
			<?php
			echo MLD_ACCOUNT_MODULE::mld_get_user_profile( $user_id );
			?>
			<div class="mld-account-wrapper">
				<?php
				echo MLD_ACCOUNT_MODULE::mld_get_input_wrapper( 'Staff Name', 'text', $mld_surname, '', '', '', 'readonly' );
				echo MLD_ACCOUNT_MODULE::mld_get_input_wrapper( 'Date of Birth', 'date', $mld_dob, '', '', '', 'readonly' );
				echo MLD_ACCOUNT_MODULE::mld_get_input_wrapper( 'Personal Email', 'text', $mld_mail, '', '', '', 'readonly' );
				?>
			</div>
			<div class="mld-account-wrapper">
				<?php
				echo MLD_ACCOUNT_MODULE::mld_get_input_wrapper( 'Home Address', 'text', $mld_h_address, '', '', '', 'readonly', '', 'textarea' );
				echo MLD_ACCOUNT_MODULE::mld_get_input_wrapper( 'Start Date', 'text', $dateWithoutTime, '', '', '', 'readonly' );
				echo MLD_ACCOUNT_MODULE::mld_get_input_wrapper( 'Contact # 1', 'text', $mld_contact_1, '', '', '', '', 'mld-contact-one' );
				?>
			</div>
			<div class="mld-account-wrapper">
				<?php
				echo MLD_ACCOUNT_MODULE::mld_get_input_wrapper( 'text', 'text', '', '', '', 'mld-wrapper-hidden' );
				echo MLD_ACCOUNT_MODULE::mld_get_input_wrapper( 'text', 'text', '', '', '', 'mld-wrapper-hidden' );
				echo MLD_ACCOUNT_MODULE::mld_get_input_wrapper( 'Contact # 2', 'text', $mld_contact_2, '', '', '', '', 'mld-contact-two' );
				?>
			</div>
		</div>
		<div class="mld-subject-details-wrapper">
			<div class="mld-subject-details-inner-wrapper">
				<?php
				echo MLD_ACCOUNT_MODULE::mld_get_input_wrapper( 'Parent Details', 'button', 'Subject Details', 'mld-title-hidden' );
				$user_sub = get_user_meta( $user_id, 'mld_teacher_selected_subjects', true );
				
				if( $user_sub ) {
					$user_sub = array_map( 'trim', $user_sub );
				}
			
				?>
				<div class="mld-tags-wrapper">
					<?php
					if( ! empty( $user_sub ) && is_array( $user_sub ) ) {
						foreach( $user_sub as $sub ) {
							
							$sub_class = str_replace( ' ', '-', $sub );
							?>
							<div class="mld-tag-button">
								<?php echo $sub; ?>
								<div class="mld-delete-pdf">
									<img src="<?php echo MLD_ASSETS_URL.'images/three-dot.png' ?>" class="mld-pdf-delete">
									<p>
										<input type="button" class="mld-tag-delete-text" value="<?php echo __( 'Delete', 'myrtle-learning-dashboard' ); ?>" subject-class="<?php echo $sub_class ?>">
									</p>
								</div>
							</div>
							<?php
						}
					}
					?>
				</div>
				<div class="mld-clear-both"></div>
			</div>
			<div class="mld-subject-wrapper">
				<din class="mld-subject-details-dropdown">
					<input type="hidden" class="mld-three-dot-url" value="<?php echo MLD_ASSETS_URL.'images/three-dot.png' ?>">
					<?php
					$subjects_array = [
						'Physics' => 'Physics',
						'Chemistry' => 'Chemistry',
						'Biology' => 'Biology',
						'Mathematics' => 'Mathematics',
						'English-Language' => 'English Language',
						'English-Literature' => 'English Literature',
						'Computer-Science' => 'Computer Science',
						'Design-and-Technology' => 'Design and Technology',
						'Geography' => 'Geography',
						'Business-Studies' => 'Business Studies',
						'Economics' => 'Economics',
						'Psychology' => 'Psychology',
						'French' => 'French',
						'Spanish' => 'Spanish',
						'Law' => 'Law',
						'Sociology' => 'Sociology',
						'History	' => 'History	',
						'Latin' => 'Latin',
						'Drama' => 'Drama',
						'Food-Technology' => 'Food Technology',
						'11Plus-Mathematics' => '11Plus Mathematics',
						'11Plus-Verbal-Reasoning' => '11Plus Verbal Reasoning',
						'11Plus-Non-Verbal-Reasoning' => '11Plus Non Verbal Reasoning',
						'11Plus-English' => '11Plus English',	
					];

					$get_new_subjects = get_option( 'mld-new-subjects' );

					if( is_array( $get_new_subjects ) && ! empty( $get_new_subjects ) ) {

						$new_sub_arr = [];

						foreach( $get_new_subjects as $subjects ) {
							$s_key = str_replace( ' ', '-', $subjects );
							$new_sub_arr[$s_key] = $subjects;	
						}

						$subjects_array = array_merge( $new_sub_arr, $subjects_array );
					}

					$updated_subjects = get_user_meta( get_current_user_id(), 'mld_teacher_selected_subjects', true );
					if( ! empty( $updated_subjects ) && is_array( $updated_subjects ) ) {
						$updated_subjects = array_map('trim', $updated_subjects);
					}
					?>
					<select>
						<option value=""><?php echo __( 'Select a Subject', 'myrtle-learning-dashboard' ); ?></option>
						<?php 
						foreach( $subjects_array as $key => $subject ) {

							$display = '';

							if( ! empty( $updated_subjects ) && is_array( $updated_subjects ) ) {

								if( in_array( $subject, $updated_subjects ) ) {
									$display = 'none';
								}
							}
							?>
							<option value="<?php echo $subject; ?>" class="<?php echo $key; ?>" style="display: <?php echo $display; ?>;"><?php echo $subject; ?></option>
							<?php 
						}
						?>
					</select>
				</din>
				<div class="mld-clear-both"></div>
			</div>
		</div>
		<div class="mld-password-reset-main-wrapper">
			<?php
			echo MLD_ACCOUNT_MODULE::mld_get_password_reset_html( $mld_mail, $user_pass );
			?>
			<div style="display: none;"class="mld-pop-outer">
				<div class="mld-pop-inner">
					<div class="mld-popup-header">
						<div class="mld-close mld-reset-close">
							<span class="dashicons dashicons-no"></span>
						</div>
					</div>
					<div class="mld-password-flield-wrapper">
						<div class="mld-old-fields-wrapper">
							<div class="mld-old-field-title">
								<?php echo __( 'Old Password', 'myrtle-learning-dashboard' ); ?>
							</div>
							<div class="mld-old-field-content">
								<input type="text" class="mld-old-password">
							</div>
						</div>
						<div class="mld-new-fields-wrapper">
							<div class="mld-new-field-title">
								<?php echo __( 'New Password', 'myrtle-learning-dashboard' ); ?>
							</div>
							<div class="mld-new-field-content">
								<input type="text" class="mld-new-password">
							</div>
						</div>
						<div class="mld-pass-error-message" style="display: none;"></div>
						<div class="mld-update-pass-btn" user_id="<?php echo $user_id; ?>">
							<?php echo __( 'Update Password', 'myrtle-learning-dashboard' ); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="mld-footer-wrapper">
			<div class="mld-upload-files-wrapper">
				<div class="mld-upload-files">
					<button class="mld-uplaod-admin-files"><?php echo __( 'UPLOAD FILES', 'myrtle-learning-dashboard' ); ?></button>
					<img src="<?php echo MLD_ASSETS_URL.'images/spinner.gif' ?>" class="mld-comment-loader">
					<input type="file" name="mld-pdf-files" class="mld-pdf-files" user_id="<?php echo $user_id; ?>" style="display: none;">
				</div>
			</div>
			<div class="mld-save-changes">
				<button class="mld-save-changes-btn" user_id="<?php echo $user_id; ?>"><?php echo __( 'Save Changes', 'myrtle-learning-dashboard' ); ?></button>
			</div>
			<div class="mld-clear-both"></div>
			<div class="mld-user-pdf mld-delete-user-pdf">
				<?php
				if( is_array( $files ) && ! empty( $files ) ) {
					foreach( $files as $file ) {
						
						$pdf_name = explode( '/', $file );
						$pdf_name = end( $pdf_name );
						$pdf_name = explode( '.', $pdf_name );
						$pdf_name = $pdf_name[0];
						$basePath = "/home/runcloud/webapps/myrtlelearning/";
						$cleanedPath = str_replace( $basePath, '', $file );
						$cleanedPath = site_url().'/'.$cleanedPath;
						$avatar_url = $cleanedPath; 

						if( user_can( $user_id, 'manage_options' ) ) {
							?>
							<div class="mld-pdf-main-wrapper">
								<div class="mld-pdf-wrapper">
									<div class="mld-pdf-name"><?php echo ucwords( $pdf_name ); ?></div>
									<a href="<?php echo $avatar_url; ?>" download><img src="<?php echo MLD_ASSETS_URL.'images/myrtle-pdf.jpg'; ?>"></a>
								</div>
								<div class="mld-delete-pdf">
									<img src="<?php echo MLD_ASSETS_URL.'images/three-dot.png'; ?>" class="mld-pdf-delete">
									<p>
										<input type="button" value="<?php echo __( 'Delete', 'myrtle-learning-dashboard' ); ?>" data-url="<?php echo $file; ?>">
									</p>
								</div>
							</div>
							<?php
						} else {
							?>
							<div class="mld-main-pdf-wrapper">
								<div class="mld-pdf-name"><?php echo ucwords( $pdf_name ); ?></div>
								<a href="<?php echo $avatar_url; ?>" download><img src="<?php echo MLD_ASSETS_URL.'images/myrtle-pdf.jpg'; ?>"></a>
							</div>
							<?php
						}
					}
				}
				?>
			</div>
		</div>
		<div class="mld-uploaded-files-content"></div>
	</div>
</div>
<div class="mld-policies-main-wrapper">
	<?php
	$default_saved_category = get_option( 'mld_saved_category' );
	?>
	<input type="file" class="mld-policy-file-input" style="display: none;">
	<div class="mld-policies-title">
		<div class="mld-policies-btn">
			<button><?php echo __( 'POLICIES', 'myrtle-learning-dashboard' ); ?></button>
		</div>
		<div class="mld-policies-go-back">
			<span class="dashicons dashicons-arrow-left-alt mld-policies-go-back-span"></span>
			<span class="mld-policies-go-back-text"><?php echo __( 'GO BACK', 'myrtle-learning-dashboard' ); ?></span>
		</div>
		<div class="mld-clear-both"></div>
	</div>
	<div class="mld-policy-search-wrapper">
		<input type="text" placeholder="<?php echo __( 'Search Policy...', 'myrtle-learning-dashboard' ); ?>" class="mld-policy-search-field">
		<span class="dashicons dashicons-search"></span>
	</div>
	<div class="mld-policies-types-wrapper">
		<?php 
		if( $default_saved_category && is_array( $default_saved_category ) ) {
			$default_saved_category = array_unique( $default_saved_category );
			
			foreach( $default_saved_category as $default_saved_category ) {

				$type = 'mld-policy-type-'.$default_saved_category;
				
				$upload_dir = wp_upload_dir();

				if( ! empty( $upload_dir['basedir'] ) ) {

					$new_upload_dir = $upload_dir['basedir'].'/'.$type;

					if ( ! file_exists( $new_upload_dir ) ) {
						continue;
					}
				}

				$policy_type_category = mld_get_category_files( $type );
				$default_saved_category = str_replace( '-', ' ', $default_saved_category );
				?>
				<div class="mld-main-policy-wrapper mld-policy-wrapper">
					<?php
					$delete_display = 'none';
					if( empty( $policy_type_category ) ) {
						$delete_display = '';
					}
					?>
					<button data-type="<?php echo $type; ?>" class="mld-delete-policy-type" id="<?php echo $type; ?>" style="display: <?php echo $delete_display; ?>;"><?php echo __( 'Delete', 'myrtle-learning-dashboard' ); ?></button>
					<div class="mld-policy-heading">
						<?php echo ucwords( $default_saved_category ); ?>
					</div>
					<div class="mld-policy-content">
						<table id="<?php echo $type; ?>" class="mld-pdf-table">
							<?php
							if( ! empty( $policy_type_category ) && is_array( $policy_type_category ) ) {
								foreach( $policy_type_category as $key => $category ) {

									$basePath = "/home/runcloud/webapps/myrtlelearning/";
									$cleanedPath = str_replace( $basePath, '', $category );
									$cleanedPath = site_url().'/'.$cleanedPath;
									$avatar_url = $cleanedPath;

									$url = $category;
									$category = explode( '/', $category );
									$category = end( $category );

									$display = '';

									if( 2 < $key ) {
										$display = 'none';
									}
									?>	
									<tr style="display: <?php echo $display; ?>">
										<td class="mld-policy-img"><img src="<?php echo $avatar_url; ?>"></td>
										<td class="mld-policy-title"><a href="<?php echo $avatar_url; ?>" download><?php echo substr( $category, 0, 15 ); ?></a></td>
										<td class="mld-policy-data mld-single-pdf-dele-option">
											<img src="<?php echo MLD_ASSETS_URL.'images/three-dot.png' ?>" style="height: 20px;">
											<div class="mld-policy-delete" data-url="<?php echo $url; ?>">
												<?php echo __( 'Delete', 'myrtle-learning-dashboard' ); ?>
											</div>
										</td>				
									</tr>
									<?php
								}
							} else {
								?>
								<tr>
									<td class="mld-pdf-not-found-msg"><?php echo __( 'No PDF Found', 'myrtle-learning-dashboard' ); ?></td>
								</tr>
								<?php
							}
							?>
						</table>	
					</div>
					<div class="mld-policy-upload-btn">
						<div class="mld-view-all-policy" data-category="<?php echo $type; ?>">
							<span><?php echo __( 'See All', 'myrtle-learning-dashboard' ); ?></span>
						</div>
						<?php
						if( current_user_can( 'manage_options' ) ) {
							?>
							<div class="mld-uplo-btn">
								<button class="mld-upload-btn" data-category="<?php echo $type; ?>"><?php echo __( 'Upload', 'myrtle-learning-dashboard' ); ?><span class="dashicons dashicons-upload"></span></button>
							</div>
							<?php 
						}
						?>
					</div>
				</div>
				<?php
			}
		}
		if( current_user_can( 'manage_options' ) ) {
			?>
			<div class="mld-add-policy-wrapper mld-policy-wrapper">
				<div class="add-category-content">
					<div class="dashicons dashicons-plus-alt mld-add-icon"></div>
					<div class="mld-add-category-text"><?php echo __( 'Add Category', 'myrtle-learning-dashboard' ); ?></div>
				</div>
			</div>
			<?php 
		}
		?>
	</div>
	<div style="display: none;" class="mld-pop-outer">
		<div class="mld-pop-inner mld-account-popup">
			<div class="mld-popup-header">
				<div class="mld-header-title"><?php echo __( 'Assignment Comment(s)' ); ?></div>
				<div class="mld-close" data_assignment-id="<?php echo $id; ?>"><span class="dashicons dashicons-no"></span></div>
				<div class="mld-clear-both"></div>
			</div>
			<div class="mld-pdf-popup-content"></div>
		</div>
	</div>
</div>