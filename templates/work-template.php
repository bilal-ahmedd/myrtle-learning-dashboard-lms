<div class="mld-work-main-wrapper">
	<div class="mld-work-first-row">
		<?php
		if( !$this->allowed_user ) {
			?>
			<input type="hidden" class="mld-hidden-work-user-id" value="<?php echo $this->user_id; ?>">
			<?php
		}

		$is_administrator = user_can( $this->user_id, 'manage_options' );
		$user_courses = [];

		if( $this->allowed_user ) {

			if( $is_administrator ) {
				$groups = mld_get_groups_for_admin();
			} else {
				$groups = mld_get_groups_for_leader( $this->user_id );
			}
			?>
			<div class="mld-work-inner-wrapper">
				<div class="mld-work-title"><?php echo __( 'Select Group*', 'myrtle-learning-dashboard' ); ?></div>
				<div class="mld-work-field">
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
			<div class="mld-work-inner-wrapper">
				<div class="mld-work-title"><?php echo __( 'Select User*', 'myrtle-learning-dashboard' ); ?></div>
				<div class="mld-work-field">
					<select class="mld-select-user">
						<option value=""><?php  echo __( 'Select User', 'myrtle-learning-dashboard' ); ?></option>
					</select>
				</div>
			</div>
			<?php

		} else {
			$user_courses = ld_get_mycourses( $user_id );
		}
		?>
	</div>
	<div class="mld-work-second-row">
		<div class="mld-work-inner-wrapper">
			<div class="mld-work-title"><?php echo __( 'Select Course*', 'myrtle-learning-dashboard' ); ?></div>
			<div class="mld-work-field">
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
		<div class="mld-work-inner-wrapper">
			<div class="mld-work-title"><?php echo __( 'Select Lesson*', 'myrtle-learning-dashboard' ); ?></div>
			<div class="mld-work-field">
				<select class="mld-select-lesson">
					<option value=""><?php  echo __( 'Select Lesson', 'myrtle-learning-dashboard' ); ?></option>
				</select>
			</div>
		</div>
	</div>
	<div class="mld-work-third-row">
		<div class="mld-work-inner-wrapper">
			<div class="mld-work-title"><?php echo __( 'Select Topic*', 'myrtle-learning-dashboard' ); ?></div>
			<div class="mld-work-field">
				<select class="mld-select-topic">
					<option value=""><?php  echo __( 'Select Topic', 'myrtle-learning-dashboard' ); ?></option>
				</select>
			</div>
		</div>
		<div class="mld-work-inner-wrapper">
			<div class="mld-work-title"><?php echo __( 'Select Test/Quiz*', 'myrtle-learning-dashboard' ); ?></div>
			<div class="mld-work-field">
				<select class="mld-select-test-quiz">
					<option value=""><?php  echo __( 'Select Test/Quiz', 'myrtle-learning-dashboard' ); ?></option>
				</select>
			</div>
		</div>
	</div>
	<div class="mld-work-submit-wrapper">
		<button>
			<?php echo __( 'PROCEED', 'myrtle-learning-dashboard' ); ?>
			<span class="mld-work-arroq dashicons dashicons-arrow-right-alt"></span>
			<img src="<?php echo MLD_ASSETS_URL.'images/spinner.gif' ?>" class="mld-comment-loader">
		</button>
		<div class="mld-clear-both"></div>
	</div>
</div>

<div class="mld-second-part-main-wrapper"></div>