<div class="mld-staff-edit-wrapper">
	<h1><?php echo __( 'Teacher Registration Fields', 'myrtle-learning-dashboard' ); ?></h1>
	<div class="mld-staff-edit-content-wrapper">
		<div class="mld-form-fields-wrapper">
			<div class="mld-form-field">
				<label><?php echo __( 'Number Of Experience', 'myrtle-learning-dashboard' ); ?></label>
				<p>
					<select class="mld-experience-field">
						<option value=""><?php echo __( 'Select number of experience', 'myrtle-learning-dashboard' ); ?></option>
						<?php
						for ( $x = 1; $x <= 20; $x++ ) {
							?>
							<option value="<?php echo $x; ?>" <?php echo selected( $x, $t_exp, true ); ?>><?php echo $x; ?></option>
							<?php
						}
						?>
					</select>
				</p>
			</div>
			<div class="mld-form-field">
				<label><?php echo __( 'Subjects', 'myrtle-learning-dashboard' ); ?></label>
				<p>
					<select class="mld-subject-field" multiple="multiple">
						<option value=""><?php echo __( 'Select Subjects', 'myrtle-learning-dashboard' ); ?></option>
						<?php
						if( is_array( $subjects_array ) && ! empty( $subjects_array ) ) {
							foreach( $subjects_array as $key => $subject ) {
								$selected_subs = '';

								if( ! empty( $t_sub ) && is_array( $t_sub ) ) {

									if( in_array( $key, $t_sub ) ) {
										$selected_subs = 'selected';
									}
								}
								?>
								<option value="<?php echo $key; ?>" <?php echo $selected_subs; ?>>
									<?php echo $subject; ?>
								</option>
								<?php
							}
						}
						?>
					</select>
				</p>
			</div>
			<div class="mld-edit-form-field">
				<label><?php echo __( 'Avaibility', 'myrtle-learning-dashboard' ); ?></label>
				<p>
					<?php
					echo self::mld_get_select_html( 'Select availability', 'mld-availability', $t_availib );
					?>
				</p>
			</div>
			<div class="mld-edit-form-field">
				<label><?php echo __( 'DBS', 'myrtle-learning-dashboard' ); ?></label>
				<p>
					<?php
					echo self::mld_get_select_html( 'DBS', 'mld-dbs', $t_dbs );
					?>
				</p>
			</div>
			<div class="mld-form-field">
				<label><?php echo __( 'Email', 'myrtle-learning-dashboard' ); ?></label>
				<p>
					<input type="text" value="<?php echo $t_email; ?>" class="t-email" placeholder="<?php echo __( 'Enter Email', 'myrtle-learning-dashboard' ); ?>">
				</p>
			</div>
			<div class="mld-form-field">
				<label><?php echo __( 'Address', 'myrtle-learning-dashboard' ); ?></label>
				<p>
					<input type="text" value="<?php echo $t_address; ?>" class="t-address" placeholder="<?php echo __( 'Enter Address', 'myrtle-learning-dashboard' ); ?>">
				</p>
			</div>
			<div class="mld-form-field">
				<label><?php echo __( 'Date Of Birth', 'myrtle-learning-dashboard' ); ?></label>
				<p>
					<input type="date" value="<?php echo $t_dob; ?>" class="t-dob" placeholder="<?php echo __( 'Enter Date Of Birth', 'myrtle-learning-dashboard' ); ?>">
				</p>
			</div>
			<div class="mld-form-field">
				<label><?php echo __( 'Town/County', 'myrtle-learning-dashboard' ); ?></label>
				<p>
					<input type="text" value="<?php echo $t_county; ?>" class="t-county" placeholder="<?php echo __( 'Enter County', 'myrtle-learning-dashboard' ); ?>">
				</p>
			</div>
			<div class="mld-form-field">
				<label><?php echo __( 'Home Tel', 'myrtle-learning-dashboard' ); ?></label>
				<p>
					<input type="text" value="<?php echo $t_hometel; ?>" class="t-hometel" placeholder="<?php echo __( 'Enter Home Tel', 'myrtle-learning-dashboard' ); ?>">
				</p>
			</div>
			<div class="mld-form-field">
				<label><?php echo __( 'Mobile Number', 'myrtle-learning-dashboard' ); ?></label>
				<p>
					<input type="number" value="<?php echo $t_mobile_number; ?>" class="t-mobile-number" placeholder="<?php echo __( 'Enter Mobile Number', 'myrtle-learning-dashboard' ); ?>">
				</p>
			</div>
			<div>
				<label class="mld-statement-label"><?php echo __( 'Personal Statement ( Max 100 Words )', 'myrtle-learning-dashboard' ); ?></label>
				<p>
					<textarea rows="5" class="mld-personal-statement" placeholder="<?php echo __( 'Please write a brief personal statement about yourself, not more than 100words focussed on the topics : Passion; Challenges faced/Solutions; Personal / Educational values/ethos', 'myrtle-learning-dashboard' ); ?>"><?php echo $t_statement; ?></textarea>
				</p>
			</div>
		</div>
		<div class="mld-form-fields-wrapper">
		</div>
		<div class="mld-registration-table">
			<button><?php echo __( 'Education Details', 'myrtle-learning-dashboard' ); ?></button>
			<div class="mld-new-row">
				<button><?php echo __( 'Add New Row', 'myrtle-learning-dashboard' ); ?></button>
			</div>
			<?php
			$check_tea_clg_info = isset( $tea_clg_info[0][0] ) && ! empty( $tea_clg_info[0][0] ) ? $tea_clg_info : '';

			if( empty( $tea_clg_info ) ) {
				// echo Myrtle_Staff_Template::mld_get_registration_table( 1, [ 'Date', 'College / A Level', 'Courses / Subjects', 'Status ( Pass/ Fail/ Pending )', 'Delete' ], 'mld-college-education' );
			} else {
				?>
				<table class="mld-college-education">
					<thead>
						<tr>
							<th><?php echo __( 'Date', 'myrtle-learning-dashboard' ); ?></th>
							<th><?php echo __( 'College / A Level', 'myrtle-learning-dashboard' ); ?></th>
							<th><?php echo __( 'Courses / Subsjecs', 'myrtle-learning-dashboard' ); ?></th>
							<th><?php echo __( 'Staus (Pass/Fail/Pending)', 'myrtle-learning-dashboard' ); ?></th>
							<th><?php echo __( 'Delete', 'myrtle-learning-dashboard' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						if( is_array( $tea_clg_info ) && ! empty( $tea_clg_info ) ) {
							$no = 0;
							foreach( $tea_clg_info as $college_data ) {

								$t_colg_data = isset( $tea_clg_info[$no] ) ? $tea_clg_info[$no] : [];
								?>
								<tr>
									<?php
									if( is_array( $t_colg_data ) && ! empty( $t_colg_data ) ) {
										foreach( $t_colg_data as $key => $col_data ) {
											?>
											<td contenteditable="true">
												<?php
												if( 0 == $key ) {
													?>
													<input type="date" value="<?php echo $col_data; ?>">	
													<?php
												} else {
													echo $col_data;
												}
												?>
											</td>
											<?php
										}
									}
									?>
									<td><i class="fa fa-trash mld-delete-table-row"></i></td>
								</tr>
								<?php
								$no++;
							}
						}
						?>
					</tbody>
				</table>
				<?php
			}

			?>
			<div class="mld-new-row">
				<button><?php echo __( 'Add New Row', 'myrtle-learning-dashboard' ); ?></button>
			</div>
			<?php

			$tea_uni_info = isset( $tea_uni_info[0][0] ) & ! empty( $tea_uni_info[0][0] ) ? $tea_uni_info : '';

			if( empty( $tea_uni_info ) ) {

				// echo Myrtle_Staff_Template::mld_get_registration_table( 1, [ 'Date', 'University', 'Subjects', 'Qualification ( Degree/ Masters/ Doctorate )', 'Delete' ], 'mld-university-education' );
			} else {
				?>
				<table class="mld-university-education">
					<thead>
						<tr>
							<th><?php echo __( 'Date', 'myrtle-learning-dashboard' ); ?></th>
							<th><?php echo __( 'University', 'myrtle-learning-dashboard' ); ?></th>
							<th><?php echo __( 'Subsjecs', 'myrtle-learning-dashboard' ); ?></th>
							<th><?php echo __( 'Qualification (Degree/Masters/Doctorate)', 'myrtle-learning-dashboard' ); ?></th>
							<th><?php echo __( 'Delete', 'myrtle-learning-dashboard' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php

						if( is_array( $tea_uni_info ) && ! empty( $tea_uni_info ) ) {
							$no = 0;
							foreach( $tea_uni_info as $t_uni_data ) {

								$t_university_data = isset( $tea_uni_info[$no] ) ? $tea_uni_info[$no] : [];
								?>
								<tr>
									<?php
									if( is_array( $t_university_data ) && ! empty( $t_university_data ) ) {
										foreach( $t_uni_data as $key => $uni_data ) {

											?>
											<td contenteditable="true">
												<?php
												if( 0 == $key ) {
													?><input type="date" value="<?php echo $uni_data; ?>"><?php	
												} else {
													echo $uni_data;
												}
												?>
											</td>
											<?php	
										}
									}
									?>
									<td><i class="fa fa-trash mld-delete-table-row"></i></td>
								</tr>
								<?php
								$no++;
							}
						}
						?>
					</tbody>
				</table>
				<?php
			}
			?>
			<button><?php echo __( 'Experience', 'myrtle-learning-dashboard' ); ?></button>
			<div class="mld-new-row">
				<button><?php echo __( 'Add New Row', 'myrtle-learning-dashboard' ); ?></button>
			</div>
			<?php 
			$tea_exp_info = isset( $tea_exp_info[0][0] ) && ! empty( $tea_exp_info[0][0] ) ? $tea_exp_info : '';

			if( empty( $tea_exp_info ) ) {
				?>
				<table class="mld-experience-years">
					<thead>
						<tr>
							<th><?php echo __( 'Subjects Taught', 'myrtle-learning-dashboard' ); ?></th>
							<th><?php echo __( 'Level', 'myrtle-learning-dashboard' ); ?></th>
							<th><?php echo __( 'Number of Students', 'myrtle-learning-dashboard' ); ?></th>
							<th><?php echo __( 'Percentage Pass', 'myrtle-learning-dashboard' ); ?></th>
							<th><?php echo __( 'Delete', 'myrtle-learning-dashboard' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td contenteditable="true">
								<select>
									<option value=""><?php echo __( 'Select taught subjects', 'myrtle-learning-dashboard' ); ?></option>
									<?php 
									$t_subjects = get_user_meta( $user_id, 'mld_teacher_selected_subjects', true ); 
									if( is_array( $t_subjects ) && ! empty( $t_subjects ) ) {
										foreach( $t_subjects as $subject ) {
											?>
											<option value="<?php echo $subject; ?>"><?php echo $subject; ?></option>
											<?php
										}
									} 
									?>
								</select>
							</td>
							<td>
								<select>
									<option value=""><?php echo __( 'Select level', 'myrtle-learning-dashboard' ); ?></option>
									<option value="Primary-School"><?php echo __( 'Primary School', 'myrtle-learning-dashboard' ); ?></option>
									<option value="Secondary-School"><?php echo __( 'Secondary School', 'myrtle-learning-dashboard' ); ?></option>
									<option value="Sixth-Form"><?php echo __( 'Sixth Form', 'myrtle-learning-dashboard' ); ?></option>
									<option value="Office-Staff"><?php echo __( 'Office Staff', 'myrtle-learning-dashboard' ); ?></option>
									<option value="Not-Applicable"><?php echo __( 'Not Applicable', 'myrtle-learning-dashboard' ); ?></option>
									<option value="Other"><?php echo __( 'Other', 'myrtle-learning-dashboard' ); ?></option>
								</select>
							</td>
							<td>
								<select>
									<option value=""><?php echo __( 'Number of students', 'myrtle-learning-dashboard' ); ?></option>
									<option value="100+">100+</option>
									<option value="200+">200+</option>
									<option value="300+">300+</option>
									<option value="400+">400+</option>
									<option value="500+">500+</option>
									<option value="600+">600+</option>
									<option value="700+">700+</option>
									<option value="800+">800+</option>
									<option value="900+">900+</option>
									<option value="1000+">1000+</option>
								</select>
							</td>
							<td>
								<select>
									<option value=""><?php echo __( 'Number of percentage', 'myrtle-learning-dashboard' ); ?></option>
									<option value="40%-59%">40-59%</option>
									<option value="60%-79%">60-79%</option>
									<option value="79%-100%">80-100%</option>
								</select>
							</td>
							<td>
								<i class="fa fa-trash mld-delete-table-row"></i>
							</td>
						</tr>
					</tbody>
				</table>
				<?php
			} else {
				?>
				<table class="mld-experience-years">
					<thead>
						<tr>
							<th><?php echo __( 'Subject Taught', 'myrtle-learning-dashboard' ); ?></th>
							<th><?php echo __( 'Level', 'myrtle-learning-dashboard' ); ?></th>
							<th><?php echo __( 'Number of Students', 'myrtle-learning-dashboard' ); ?></th>
							<th><?php echo __( 'Percentage Pass', 'myrtle-learning-dashboard' ); ?></th>
							<th><?php echo __( 'Delete', 'myrtle-learning-dashboard' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php 
						if( is_array( $tea_exp_info ) && ! empty( $tea_exp_info ) ) {

							$no = 0;
							foreach( $tea_exp_info as $experience_data ) {

								$t_experience_data = isset( $tea_exp_info[$no] ) ? $tea_exp_info[$no] : [];
								?>
								<tr>
									<?php
									if( is_array( $t_experience_data ) && ! empty( $t_experience_data ) ) {
										
										$last_key = end( array_keys( $t_experience_data ) );
										foreach( $t_experience_data as $key => $experience_data ) {

											if( $key == $last_key ) {
												?>
												<td><i class="fa fa-trash mld-delete-table-row"></i></td>
												<?php
											} else {
												
												if( 1 == $key ) {
													?>
													<td contenteditable="true">
														<select>
															<option value=""><?php echo __( 'Select a level', MLD_TEXT_DOMAIN ); ?></option>
															<option value="Primary-School" <?php echo selected( 'Primary-School', $experience_data, true ); ?>><?php echo __( 'Primary School', MLD_TEXT_DOMAIN ); ?></option>
															<option value="Secondary-School" <?php echo selected( 'Secondary-School', $experience_data, true ); ?>><?php echo __( 'Secondary School', MLD_TEXT_DOMAIN ); ?></option>
															<option value="Sixth-Form" <?php echo selected( 'Sixth-Form', $experience_data, true ); ?>><?php echo __( 'Sixth Form', MLD_TEXT_DOMAIN ); ?></option>
															<option value="Office-Staff" <?php echo selected( 'Office-Staff', $experience_data, true ); ?>><?php echo __( 'Office Staff', MLD_TEXT_DOMAIN ); ?></option>
															<option value="Not-Applicable" <?php echo selected( 'Not-Applicable', $experience_data, true ); ?>><?php echo __( 'Not Applicable', MLD_TEXT_DOMAIN ); ?></option>
															<option value="Other" <?php echo selected( 'Other', $experience_data, true ); ?>><?php echo __( 'Other', MLD_TEXT_DOMAIN ); ?></option>
														</select>
													</td>
													<?php
												}

												if( 2 == $key ) {
													?>
													<td>
														<select>
															<option value=""><?php echo __( 'Number of students', 'myrtle-learning-dashboard' ); ?></option>
															<option value="100+" <?php echo selected( $experience_data, '100+', true ); ?>>100+</option>
															<option value="200+" <?php echo selected( $experience_data, '200+', true ); ?>>200+</option>
															<option value="300+" <?php echo selected( $experience_data, '300+', true ); ?>>300+</option>
															<option value="400+" <?php echo selected( $experience_data, '400+', true ); ?>>400+</option>
															<option value="500+" <?php echo selected( $experience_data, '500+', true ); ?>>500+</option>
															<option value="600+" <?php echo selected( $experience_data, '600+', true ); ?>>600+</option>
															<option value="700+" <?php echo selected( $experience_data, '700+', true ); ?>>700+</option>
															<option value="800+" <?php echo selected( $experience_data, '800+', true ); ?>>800+</option>
															<option value="900+" <?php echo selected( $experience_data, '900+', true ); ?>>900+</option>
															<option value="1000+" <?php echo selected( $experience_data, '1000+', true ); ?>>1000+</option>
														</select>
													</td>
													<?php
												}

												if( 3 == $key ) {
													?>
													<td>
														<select>
															<option value=""><?php echo __( 'Number of percentage', 'myrtle-learning-dashboard' ); ?></option>
															<option value="40%-59%" <?php echo selected( $experience_data, '40%-59%', true ); ?>>40-59 %</option>
															<option value="60%-79%" <?php echo selected( $experience_data, '60%-79%', true ); ?>>60-79 %</option>
															<option value="80%-100%" <?php echo selected( $experience_data, '80%-100%', true ); ?>>80-100%</option>
														</select>
													</td>
													<?php
												}
												if( 0 == $key ) {
													?>
													<td>
														<select>
															<option value=""><?php echo __( 'Select taught subject', 'myrtle-learning-dashboard' ); ?></option>
															<?php 
															if( is_array( $t_sub ) && ! empty( $t_sub ) ) {
																foreach( $t_sub as $sub ) {
																	?>
																	<option value="<?php echo $sub; ?>" <?php echo selected( $experience_data, $sub, true ); ?>><?php echo str_replace( '-', ' ', $sub ); ?></option>
																	<?php
																}
															}		
															?>
														</select>
													</td>
													<?php
												}
											}
										}
									}
									?>
								</tr>
								<?php
								$no++;
							}
						}
						?>
					</tbody>
				</table>
				<?php
			}
			?>
		</div>
	</div>
	<div class="mld-approved-teacher-wrapper">
		<button data-user_id="<?php echo $user_id; ?>" class="mld-user-accept button button-primary"><?php echo __( 'Approved', 'myrtle-learning-dashboard' ); ?></button>
		<button data-user_id="<?php echo $user_id; ?>" class="mld-user-deny button button-primary"><?php echo __( 'Denied', 'myrtle-learning-dashboard' ); ?></button>
	</div>
	<div class="mld-edit-staff-update-btn">
		<button data-user_id="<?php echo $user_id; ?>"><?php echo __( 'Update Profile', 'myrtle-learning-dashboard' ); ?></button>
		<div class="mld-staff-submit-btn"></div>
	</div>
</div>
<?php