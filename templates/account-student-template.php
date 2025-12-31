<div class="mld-account-main-wrapper">
	<div class="mld-account-inner-wrapper">
		<div class="mld-account-f-row">
			<?php
			echo MLD_ACCOUNT_MODULE::mld_get_user_profile( $user_id );
			?>
			<div class="mld-account-wrapper">
				<?php
				echo MLD_ACCOUNT_MODULE::mld_get_input_wrapper( $u_name, 'text', $user_name, '', '', '', 'readonly' );
				echo MLD_ACCOUNT_MODULE::mld_get_input_wrapper( 'Date of Birth', 'date', $user_dob, '', '', '', 'readonly' );
				echo MLD_ACCOUNT_MODULE::mld_get_input_wrapper( 'Email', 'text', $user_email, '', '', '', 'readonly' );
				?>
			</div>
			<div class="mld-account-wrapper">
				<?php
				echo MLD_ACCOUNT_MODULE::mld_get_input_wrapper( 'Home Address', 'text', $user_address, '', '', '', 'readonly' );
				echo MLD_ACCOUNT_MODULE::mld_get_input_wrapper( 'School Name', 'text', $user_school, '', '', '', 'readonly' );
				echo MLD_ACCOUNT_MODULE::mld_get_input_wrapper( 'Start Date', 'text', $dateWithoutTime, '', '', '', 'readonly' );
				?>
			</div>
		</div>
		<div class="mld-parent-account-wrapper">
			<div class="mld-account-parent-inner-wrap">
				<?php
				echo MLD_ACCOUNT_MODULE::mld_get_input_wrapper( 'Parent Details', 'button', 'Parent Details', 'mld-title-hidden' );
				echo MLD_ACCOUNT_MODULE::mld_get_input_wrapper( 'Name', 'text', $parent_name, '', '', '', 'readonly' );
				?>
			</div>
			<div class="mld-account-parent-inner-wrap">
				<?php
				echo MLD_ACCOUNT_MODULE::mld_get_input_wrapper( 'text', 'text', '', '', '', 'mld-wrapper-hidden' );
				echo MLD_ACCOUNT_MODULE::mld_get_input_wrapper( 'Email', 'text', $parent_email, '', '', '', 'readonly' );
				?>
			</div>
			<div class="mld-account-parent-inner-wrap">
				<?php
				echo MLD_ACCOUNT_MODULE::mld_get_input_wrapper( 'Contact # 1', 'text', $mld_phone, '', '', '', '', 'mld-contact-one' );
				echo MLD_ACCOUNT_MODULE::mld_get_input_wrapper( 'Contact # 2', 'text', $second_phone, '', '', '', '', 'mld-contact-two' );
				?>
			</div>
		</div>
		<?php
		$logged_in_user_capability = get_user_meta( get_current_user_id(), $wpdb->prefix.'capabilities', true );
		$logged_in_user_capability = array_keys( $logged_in_user_capability );

		if( in_array( 'administrator', $logged_in_user_capability ) ) {

			$client_table_name = $wpdb->prefix.'mld_client_communication';
			$client_query = $wpdb->prepare(
				"SELECT * FROM $client_table_name WHERE current_user_id = %d ORDER BY ID DESC",
				$user_id
			);						
			$communication_data = $wpdb->get_results($client_query);
			?>
			<div class="mld-parent-meeting-wrapper">
				<button><?php echo __( 'Client Communication', 'myrtle-learning-dashboard' ); ?></button>
				<div class="mld-meeting-textarea">
					
					<?php
					if( ! empty( $communication_data ) && is_array( $communication_data ) ) {
						?>
						<div class="mld-communication-wrapper">
							<?php
							foreach( $communication_data as $data ) {
								
								$cl_msg = isset( $data->message ) ? $data->message : '';
								$cl_msg = str_replace( "\\'", "'", $cl_msg );
								$timestam = isset( $data->dates ) ? $data->dates : '';
								$user_ip = self::mld_user_ip_address();
								$current_time_zone = self::mld_get_time_zone( $user_ip, 'timezone' );
								$date = new DateTime( "@$timestam" ); 
								$date->setTimezone( new DateTimeZone($current_time_zone ) );
								$timestam = $date->format( 'l jS \of F Y h:i:s A' );
								$author_id = isset( $data->logged_in_user_id ) ? $data->logged_in_user_id : 0;
								$mld_surname = mld_get_username( $author_id );
								?>
								<div class="mld-message-reference"><?php echo $timestam.' ( '.ucwords( $mld_surname ).' )'; ?></div>
								<div class="mld-message"><?php echo $cl_msg; ?></div>
								<?php
							}
							?>
						</div>
						<?php
					}
					?>
					<textarea rows="4" disabled></textarea>
				</div>
				<div class="mld-buttons-wrapper">
					<button class="mld-click-to-edit"><?php echo __( 'Click to Edit', 'myrtle-learning-dashboard' ); ?></button>
					<button class="mld-click-to-update" data-logged_in_user_id="<?php echo get_current_user_id(); ?>" data-current_user_id="<?php echo $user_id; ?>"><?php echo __( 'Click to Update', 'myrtle-learning-dashboard' ); ?></button>
				</div>
			</div>
			<?php
		}
		?>

		<div class="mld-password-reset-main-wrapper">
			<?php
			echo MLD_ACCOUNT_MODULE::mld_get_password_reset_html( $user_email, $user_pass );
			?>
			<div style="display: none;"class="mld-pop-outer">
				<div class="mld-pop-inner">
					<div class="mld-popup-header">
						<div class="mld-close mld-reset-close"><span class="dashicons dashicons-no"></span></div>
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
						<div class="mld-update-pass-btn">
							<?php echo __( 'Update Password', 'myrtle-learning-dashboard' ); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="mld-footer-wrapper">
			<div class="mld-save-changes">
				<button class="mld-save-changes-btn" user_id="<?php echo $user_id; ?>"><?php echo __( 'Save Changes', 'myrtle-learning-dashboard' ); ?></button>
			</div>
		</div>
		<div class="mld-user-pdf">
			<?php
			if( is_array( $files ) && ! empty( $files ) ) {

				$current_user_id = get_current_user_id();
				foreach( $files as $file ) {

					$pdf_name = explode( '/', $file );
					$pdf_name = end( $pdf_name );
					$pdf_name = explode( '.' , $pdf_name );
					$pdf_name = $pdf_name[0];
					$basePath = "/home/runcloud/webapps/myrtlelearning/";
					$cleanedPath = str_replace( $basePath, '', $file );
					$cleanedPath = site_url().'/'.$cleanedPath;
					$avatar_url = $cleanedPath;

					if( user_can( $current_user_id, 'manage_options' ) ) {
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
									<input type="button" class="mld-pdf-comment" value="<?php echo __( 'Comment', 'myrtle-learning-dashboard' ); ?>" data-pdf_key="<?php echo $pdf_name; ?>" data-user_id="<?php echo $user_id; ?>">
								</p>
							</div>
						</div>
						<?php
					} else {

						?>
						<div class="mld-pdf-main-wrapper">
							<div class="mld-pdf-wrapper">
								<div class="mld-pdf-name"><?php echo ucwords( substr($pdf_name, 0, 20 ) ); ?></div>
								<a href="<?php echo $avatar_url; ?>" download><img src="<?php echo MLD_ASSETS_URL.'images/myrtle-pdf.jpg'; ?>"></a>
							</div>
							<div class="mld-delete-pdf">
								<img src="<?php echo MLD_ASSETS_URL.'images/three-dot.png' ?>" class="mld-pdf-delete">
								<p>
									<input type="button" class="mld-pdf-comment" data-is_capable="no" value="<?php echo __( 'Comment', 'myrtle-learning-dashboard' ); ?>" data-pdf_key="<?php echo $pdf_name; ?>" data-user_id="<?php echo $user_id; ?>">
								</p>
							</div>
						</div>
						<?php
					}
				}
			}
			?>
		</div>
		<div class="mld-uploaded-files-content"></div>
		<div class="mld-files-wrapper mld-upload-files">
			<?php 
			$btn_disabled = '';
			$zoho_link = 'https://www.zoho.com/';
			$cursor = '';
			$target = '_blank';
			if( in_array( 'pending', $user_roles ) || in_array( 'pending_student', $user_roles ) || in_array( 'pending_teacher', $user_roles ) ) {
				$btn_disabled = 'disabled';
				$zoho_link = '#';
				$cursor = 'no-drop';
				$target = '';
			}
			?>
			<button class="mld-term-condition-btn mld-term-button"><?php echo __( 'Terms & Condition', 'myrtle-learning-dashboard' ); ?></button>
			<a href="<?php echo $zoho_link; ?>" target="<?php echo $target; ?>" style="cursor: <?php echo $cursor; ?>;"><button class="mld-invince-statement" style="cursor: <?php echo $cursor; ?>;"><?php echo __( 'INVOICE/STATEMENT', 'myrtle-learning-dashboard' ); ?></button></a>
			<button class="mld-invince-upload mld-uplaod-admin-files" <?php echo $btn_disabled; ?> style="cursor: <?php echo $cursor; ?>;"><?php echo __( 'UPLOAD FILES', 'myrtle-learning-dashboard' ); ?></button>
			<?php
			if( in_array( 'pending_teacher', $user_roles ) ) {
				?>
				<button class="mld-checklist"><?php echo __( 'Checklist', 'myrtle-learning-dashboard' ); ?></button>
				<?php
			}
			
			?>
			<img src="<?php echo MLD_ASSETS_URL.'images/spinner.gif' ?>" class="mld-comment-loader">
			<input type="file" name="mld-pdf-files" class="mld-pdf-files" user_id="<?php echo $user_id; ?>" style="display: none;">
		</div>
	</div>
</div>