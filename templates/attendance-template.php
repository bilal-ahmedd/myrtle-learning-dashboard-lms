<div class="mld-attendance-main-wrapper">
	<input type="hidden" class="mld-attendance-type" value="user-attendance">
	<?php 

	$is_administrator = user_can( $user_id, 'manage_options' );
	if( $is_administrator ) {
		?>
		<div class="attendance-type-wrapper">
			<div class="mld-user-attendance attendance-active">
				<?php echo __( 'User Attendance', 'myrtle-learning-dashboard' ); ?>
			</div>
			<div class="mld-teacher-attendance">
				<?php echo __( 'Teacher Attendance', 'myrtle-learning-dashboard' ); ?>
			</div>
		</div>
		<?php
	}
	?>
	<div class="mld-attendance-first-row">
		<?php
		$is_display = false;
		$user_courses = [];

		$user_capability = mld_user_capability( $user_id );
		$teacher_id = 0;
		if( in_array( 'administrator', $user_capability ) || in_array( 'group_leader', $user_capability )  ) {
			
			$is_display = true;

			$teacher_id = self::$instance->userid;

			if( $is_administrator ) {
				$groups = mld_get_groups_for_admin();
			} else {
				$groups = mld_get_groups_for_leader( $user_id );
			}
		} else {
			$groups = mld_get_user_groups( $user_id );
		}

		?>
		<input type="hidden" class="mld-teacher-id" value="<?php echo $teacher_id; ?>">
		<div class="mld-attendance-inner-wrapper">
			<div class="mld-group-label"><?php echo __( 'Select Group*', 'myrtle-learning-dashboard' ); ?></div>
			<div class="mld-attendance-field">
				<select class="mld-select-group">
					<option value=""><?php echo __( 'Select a group', 'myrtle-learning-dashboard' ); ?></option>
					<?php 
					if( ! empty( $groups ) && is_array( $groups ) ) {
						foreach( $groups as $group ) {
							?>
							<option value="<?php echo $group; ?>"><?php echo get_the_title( $group ); ?></option>
							<?php
						}
					}
					?>
				</select>
			</div>
		</div>
		<div class="mld-attendance-inner-wrapper">
			<div class="mld-course-label"><?php echo __( 'Select Course', 'myrtle-learning-dashboard' ); ?></div>
			<div class="mld-attendance-field">
				<select class="mld-select-course">
					<option value=""><?php  echo __( 'Select a Course', 'myrtle-learning-dashboard' ); ?></option>
					<?php 
					if( ! empty( $user_courses ) && is_array( $user_courses ) ) {
						foreach( $user_courses as $user_course ) {
							?>
							<option value="<?php echo $user_course; ?>"><?php echo get_the_title( $user_course ); ?></option>
							<?php
						}
					}
					?>
				</select>
			</div>
		</div>
	</div>
	<?php 
	if( true == $is_display ) {
		?>
		<div class="mld-attendance-second-row">
			<div class="mld-attendance-inner-wrapper">
				<div class="mld-user-label"><?php echo __( 'Exclude User', 'myrtle-learning-dashboard' ); ?></div>
				<div class="mld-attendance-field mld-exclude-user">
					<select class="mld-select-exclude-user" name="mld_selected_exculde_users[]" multiple="multiple">
						<option value=""><?php  echo __( 'Select exclude User', 'myrtle-learning-dashboard' ); ?></option>
					</select>
				</div>
			</div>
			<div class="mld-attendance-inner-wrapper">
				<div class="mld-user-label"><?php echo __( 'Include User', 'myrtle-learning-dashboard' ); ?></div>
				<div class="mld-attendance-field mld-include-user">
					<select class="mld-select-include-user" name="mld_selected_include_users[]" multiple="multiple">
						<option value=""><?php  echo __( 'Select include User', 'myrtle-learning-dashboard' ); ?></option>
					</select>
				</div>
			</div>
		</div>
		<?php
	}
	?>
	<div class="mld-attendance-submit-wrapper">
		<button>
			<?php echo __( 'PROCEED', 'myrtle-learning-dashboard' ); ?>
			<span class="mld-attendance-arrow dashicons dashicons-arrow-right-alt"></span>
			<img src="<?php echo MLD_ASSETS_URL.'images/spinner.gif' ?>" class="mld-comment-loader">
		</button>
		<div class="mld-clear-both"></div>
	</div>
</div>

<div class="mld-back-user-report mld-attendance-back-btn">
	<span class="dashicons dashicons-arrow-left-alt"></span>
	<span class="mld-go-back"><?php echo __( 'Go Back', 'myrtle-learning-dashboard' ); ?></span>
</div>

<div class="mld-attendance-filter-wrapper">
	<select class="mld-attendance-filter">
		<option value=""><?php echo __( 'Select an option', 'myrtle-learning-dashboard' ); ?></option>
		<option value="this-week"><?php echo __( 'This Week', 'myrtle-learning-dashboard' ); ?></option>
		<option value="last-week"><?php echo __( 'Last Week', 'myrtle-learning-dashboard' ); ?></option>
		<option value="this-month"><?php echo __( 'This Month', 'myrtle-learning-dashboard' ); ?></option>
		<option value="last-month"><?php echo __( 'Last Month', 'myrtle-learning-dashboard' ); ?></option>
		<option value="custom-date"><?php echo __( 'Custom Date', 'myrtle-learning-dashboard' ); ?></option>
	</select>
	<input type="date" class="mld-attendance-start-date" placeholder="<?php echo __( 'Start date', 'myrtle-learning-dashboard' ); ?>">
	<input type="date" class="mld-attendance-end-date" placeholder="<?php echo __( 'End date', 'myrtle-learning-dashboard' ); ?>">
	<button class="mld-filter-btn"><?php echo __( 'Apply', 'myrtle-learning-dashboard' ); ?></button>
	<img src="<?php echo MLD_ASSETS_URL.'images/spinner.gif' ?>" class="mld-comment-loader mld-filter-loader">
	<?php 
	if( $is_display ) {
		?>
		<input type="hidden" class="mld-attendance-enable" value="yes">
		<button class="mld-submit-btn"><?php echo __( 'Submit', 'myrtle-learning-dashboard' ); ?></button>
		<?php
	}
	?>
</div>

<div class="mld-second-part-main-wrapper"></div>
<div class="mld-attendance-pagination">
	<div class="mld-backward-attendance dashicons dashicons-arrow-left-alt2" data-page="<?php echo $page; ?>"></div>
	<div class="mld-pagination-number">
		<div class="mld-start-page">0</div>
		<div class="mld-pagination-text"><?php echo __( 'Out Of', 'myrtle-learning-dashboard' ); ?></div>
		<div class="mld-end-page"></div> 
	</div>
	<div class="mld-forward-attendance dashicons dashicons-arrow-right-alt2" data-page="<?php echo $page; ?>"></div>
</div>
<img src="<?php echo MLD_ASSETS_URL.'images/spinner.gif' ?>" class="mld-comment-loader mld-pagination-loader">