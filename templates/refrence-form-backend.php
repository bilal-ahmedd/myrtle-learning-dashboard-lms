<div class="mld-main-wrapper">
	<div class="mld-form-applicent">
		<div class="mld-applicent-label mld-min-height">
			<p><?php echo __( 'Name of Applicant', 'myrtle-learning-dashboard' ); ?></p>
		</div>
		<div class="mld-applicent-data mld-min-height" contenteditable="true"><?php echo $applicant_name; ?></div>
		<div class="mld-clear-both"></div>
	</div>
	<div class="mld-form-applicent">
		<div class="mld-applicent-label mld-min-height">
			<p><?php echo __( 'Post Applied for', 'myrtle-learning-dashboard' ); ?></p>
		</div>
		<div class="mld-applicent-data mld-min-height" contenteditable="true"><?php echo $p_applied; ?></div>
		<div class="mld-clear-both"></div>
	</div>
	<div class="mld-applicent-experience">
		<div class="mld-experience-label mld-min-height">
			<p><?php echo __( 'Did the applicant work for your organisation?', 'myrtle-learning-dashboard' ); ?></p>
		</div>
		<div class="mld-experience-data mld-min-height">
			<p>
				<label><?php echo __( 'Yes', 'myrtle-learning-dashboard' ); ?></label>
				<input type="checkbox" class="mld-experience-yes" <?php  echo $experience_check;?>>
				<label><?php echo __( 'No', 'myrtle-learning-dashboard' ); ?></label>
				<input type="checkbox" class="mld-experience-no" <?php  echo $experience_uncheck;?>>
			</p>
		</div>
		<div class="mld-clear-both"></div>
	</div>
	<div class="mld-applicent-information">
		<div class="mld-information-label mld-min-height">
			<p><?php echo __( "If yes, what were the applicant's start and leaving dates?", 'myrtle-learning-dashboard' ); ?></p>
		</div>
	</div>
	<div class="mld-applicent-information-data">
		<div class="mld-start-date mld-min-height">
			<label><?php echo __( 'Start date:', 'myrtle-learning-dashboard' ); ?></label>
			<input type="date" value="<?php echo $s_date; ?>">
		</div>
		<div class="mld-end-date mld-min-height">
			<label><?php echo __( 'Leaving date:', 'myrtle-learning-dashboard' ); ?></label>
			<input type="date" value="<?php echo $e_date; ?>">
		</div>
		<div class="mld-salary mld-min-height">
			<label><?php echo __( 'Salary / Grade:', 'myrtle-learning-dashboard' ); ?></label>
			<input type="text" value="<?php echo $salary; ?>">
		</div>				
	</div>
	<div class="mld-job-title">
		<div class="mld-job-title-label mld-min-height">
			<p><?php echo __( "What is your job title?", 'myrtle-learning-dashboard' ); ?></p>
		</div>
		<div class="mld-job-title-data mld-min-height" contenteditable="true"><?php echo $j_title; ?></div>
	</div>
	<div class="mld-time-period">
		<div class="mld-time-period-label mld-min-height">
			<p><?php echo __( "How long did you work with the applicant?", 'myrtle-learning-dashboard' ); ?></p>
		</div>
		<div class="mld-time-period-data mld-min-height" contenteditable="true"><?php echo $t_period; ?></div>
	</div>
	<div class="mld-applicent-capacity">
		<div class="mld-applicent-capacity-label mld-min-height">
			<p><?php echo __( "In what capacity do you know the applicant? E.g. as a colleague/as an employee reporting to you/other (please specify)", 'myrtle-learning-dashboard' ); ?></p>
		</div>
		<div class="mld-applicent-capacity-data mld-min-height" contenteditable="true"><?php echo $a_capacity; ?></div>
	</div>
	<div class="mld-organization-title">
		<div class="mld-organization-title-label mld-min-height">
			<p><?php echo __( "What was the applicant's job title with your organisation?", 'myrtle-learning-dashboard' ); ?></p>
		</div>
		<div class="mld-organization-title-data mld-min-height" contenteditable="true"><?php echo $org_title; ?></div>
	</div>
	<div class="mld-job-duties">
		<div class="mld-job-duties-label mld-min-height">
			<p><?php echo __( "What were the applicant's main job duties?", 'myrtle-learning-dashboard' ); ?></p>
		</div>
		<div class="mld-job-duties-data mld-min-height" contenteditable="true"><?php echo $j_duties; ?></div>
	</div>
	<div class="mld-applicent-work-behaviour">
		<div class="mld-applicent-work-behaviour-label mld-min-height">
			<p><?php echo __( "What is your assessment of the following elements in relation to the applicant?", 'myrtle-learning-dashboard' ); ?></p>
		</div>
		<div class="mld-applicent-work-bahaviour-data">
			<table>
				<thead>
					<tr>
						<th class="mld-label"></th>
						<th class="mld-label"><?php echo __( 'Excellent', 'myrtle-learning-dashboard' ); ?></th>
						<th class="mld-label"><?php echo __( 'Good', 'myrtle-learning-dashboard' ); ?></th>
						<th class="mld-label"><?php echo __( 'Fair', 'myrtle-learning-dashboard' ); ?></th>
						<th class="mld-label"><?php echo __( 'Poor', 'myrtle-learning-dashboard' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<tr class="mld-quality-of-work">
						<td class="mld-label"><?php echo __( 'Quality of work', 'myrtle-learning-dashboard' ); ?></td>
						<td contenteditable="true" class="quality-answer">
							<input type="checkbox" class="mld-general-check mld-general" <?php echo $quality_first_col; ?>>
							<input type="hidden" value="excellent" class="quality-answer-checkbox">
						</td>
						<td contenteditable="true" class="quality-answer">
							<input type="checkbox" class="mld-general-check mld-general" <?php echo $quality_second_col; ?>>
							<input type="hidden" value="good" class="quality-answer-checkbox">
						</td>
						<td contenteditable="true" class="quality-answer">
							<input type="checkbox" class="mld-general-check mld-general" <?php echo $quality_third_col; ?>>
							<input type="hidden" value="fair" class="quality-answer-checkbox">
						</td>
						<td contenteditable="true" class="quality-answer">
							<input type="checkbox" class="mld-general-check mld-general" <?php echo $quality_fourth_col; ?>>
							<input type="hidden" value="poor" class="quality-answer-checkbox">
						</td>
					</tr>
					<tr class="mld-quantity-of-work">
						<td class="mld-label"><?php echo __( 'Quantity of work', 'myrtle-learning-dashboard' ); ?></td>
						<td contenteditable="true" class="quantity-work">
							<input type="checkbox" class="mld-general-check mld-general" <?php echo $quantity_first_col; ?>>
							<input type="hidden" value="excellent" class="quantity-work-checkbox">
						</td>
						<td contenteditable="true" class="quantity-work">
							<input type="checkbox" class="mld-general-check mld-general" <?php echo $quantity_second_col; ?>>
							<input type="hidden" value="good" class="quantity-work-checkbox">
						</td>
						<td contenteditable="true" class="quantity-work">
							<input type="checkbox" class="mld-general-check mld-general" <?php echo $quantity_third_col; ?>>
							<input type="hidden" value="fair" class="quantity-work-checkbox">
						</td>
						<td contenteditable="true" class="quantity-work">
							<input type="checkbox" class="mld-general-check mld-general" <?php echo $quantity_fourth_col; ?>>
							<input type="hidden" value="poor" class="quantity-work-checkbox">
						</td>
					</tr>
					<tr class="mld-job-dedication">
						<td class="mld-label"><?php echo __( 'Dedication to the job', 'myrtle-learning-dashboard' ); ?></td>
						<td contenteditable="true" class="job-dedication">
							<input type="checkbox" class="mld-general-check mld-general" <?php echo $job_dedication_first_col; ?>>
							<input type="hidden" value="excellent" class="job-dedication-checkbox">
						</td>
						<td contenteditable="true" class="job-dedication">
							<input type="checkbox" class="mld-general-check mld-general" <?php echo $job_dedication_second_col; ?>>
							<input type="hidden" value="good" class="job-dedication-checkbox">
						</td>
						<td contenteditable="true" class="job-dedication">
							<input type="checkbox" class="mld-general-check mld-general" <?php echo $job_dedication_third_col; ?>>
							<input type="hidden" value="fair" class="job-dedication-checkbox">
						</td>
						<td contenteditable="true" class="job-dedication">
							<input type="checkbox" class="mld-general-check mld-general" <?php echo $job_dedication_fourth_col; ?>>
							<input type="hidden" value="poor" class="job-dedication-checkbox">
						</td>
					</tr>
					<tr class="mld-ability-of-work">
						<td class="mld-label"><?php echo __( 'Ability to work without supervision', 'myrtle-learning-dashboard' ); ?></td>
						<td contenteditable="true" class="ability-of-work">
							<input type="checkbox" class="mld-general-check mld-general" <?php echo $ability_first_col; ?>>
							<input type="hidden" value="excellent" class="ability-of-work-checkbox">
						</td>
						<td contenteditable="true" class="ability-of-work">
							<input type="checkbox" class="mld-general-check mld-general" <?php echo $ability_second_col; ?>>
							<input type="hidden" value="good" class="ability-of-work-checkbox">
						</td>
						<td contenteditable="true" class="ability-of-work">
							<input type="checkbox" class="mld-general-check mld-general" <?php echo $ability_third_col; ?>>
							<input type="hidden" value="fair" class="ability-of-work-checkbox">
						</td>
						<td contenteditable="true" class="ability-of-work">
							<input type="checkbox" class="mld-general-check mld-general" <?php echo $ability_fourth_col; ?>>
							<input type="hidden" value="poor" class="ability-of-work-checkbox">
						</td>
					</tr>
					<tr class="mld-working-relationship">
						<td class="mld-label"><?php echo __( 'Working relationships', 'myrtle-learning-dashboard' ); ?></td>
						<td contenteditable="true" class="working-relationship">
							<input type="checkbox" class="mld-general-check mld-general" <?php echo $working_rela_first_col; ?>>
							<input type="hidden" value="excellent" class="working-relationship-checkbox">
						</td>
						<td contenteditable="true" class="working-relationship">
							<input type="checkbox" class="mld-general-check mld-general" <?php echo $working_rela_second_col; ?>>
							<input type="hidden" value="good" class="working-relationship-checkbox">
						</td>
						<td contenteditable="true" class="working-relationship">
							<input type="checkbox" class="mld-general-check mld-general" <?php echo $working_rela_third_col; ?>>
							<input type="hidden" value="fair" class="working-relationship-checkbox">
						</td>
						<td contenteditable="true" class="working-relationship">
							<input type="checkbox" class="mld-general-check mld-general" <?php echo $working_rela_fourth_col; ?>>
							<input type="hidden" value="poor" class="working-relationship-checkbox">
						</td>
					</tr>
					<tr class="mld-time-keeping">
						<td class="mld-label"><?php echo __( 'Time keeping', 'myrtle-learning-dashboard' ); ?></td>
						<td contenteditable="true" class="time-keeping">
							<input type="checkbox" class="mld-general-check mld-general" <?php echo $time_keep_first_col; ?>>
							<input type="hidden" value="excellent" class="time-keeping-checkbox">
						</td>
						<td contenteditable="true" class="time-keeping">
							<input type="checkbox" class="mld-general-check mld-general" <?php echo $time_keep_second_col; ?>>
							<input type="hidden" value="good" class="time-keeping-checkbox">
						</td>
						<td contenteditable="true" class="time-keeping">
							<input type="checkbox" class="mld-general-check mld-general" <?php echo $time_keep_third_col; ?>>
							<input type="hidden" value="fair" class="time-keeping-checkbox">
						</td>
						<td contenteditable="true" class="time-keeping">
							<input type="checkbox" class="mld-general-check mld-general" <?php echo $time_keep_fourth_col; ?>>
							<input type="hidden" value="poor" class="time-keeping-checkbox">
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<div class="mld-applicent-trustworthy">
		<div class="mld-applicent-trustworthy-label mld-min-height">
			<p><?php echo __( 'Did you find the applicant to be honest and trustworthy?', 'myrtle-learning-dashboard' ); ?></p>
		</div>
		<div class="mld-applicent-trustworthy-data mld-min-height">
			<p>
				<label><?php echo __( 'Yes', 'myrtle-learning-dashboard' ); ?></label>
				<input type="checkbox" class="mld-trustworthy-yes" <?php echo $trust_check; ?>>
				<label><?php echo __( 'No', 'myrtle-learning-dashboard' ); ?></label>
				<input type="checkbox" class="mld-trustworthy-no" <?php echo $trust_uncheck; ?>>
			</p>
		</div>
		<div class="mld-clear-both"></div>
	</div>
	<div class="mld-applicent-duty-care">
		<div class="mld-applicent-duty-care-label mld-min-height">
			<p><?php echo __( 'Did you find the applicant to be reliable in carrying out his/her duties?', 'myrtle-learning-dashboard' ); ?></p>
		</div>
		<div class="mld-applicent-duty-care-data mld-min-height">
			<p>
				<label><?php echo __( 'Yes', 'myrtle-learning-dashboard' ); ?></label>
				<input type="checkbox" class="mld-duty-care-yes" <?php echo $care_check;?>>
				<label><?php echo __( 'No', 'myrtle-learning-dashboard' ); ?></label>
				<input type="checkbox" class="mld-duty-care-no" <?php echo $care_uncheck;?>>
			</p>
		</div>
		<div class="mld-clear-both"></div>
	</div>
	<div class="mld-applicent-disciplinary-warnings">
		<div class="mld-applicent-disciplinary-warnings-label mld-min-height">
			<p><?php echo __( 'Does or did the applicant have any live disciplinary warnings with your organisation? If yes, please comment on the nature of these warnings below:', 'myrtle-learning-dashboard' ); ?></p>
		</div>
		<div class="mld-applicent-disciplinary-warnings-data mld-min-height">
			<p>
				<label><?php echo __( 'Yes', 'myrtle-learning-dashboard' ); ?></label>
				<input type="checkbox" class="mld-disciplinary-warnings-yes" <?php echo $disciplinary_check; ?>>
				<label><?php echo __( 'No', 'myrtle-learning-dashboard' ); ?></label>
				<input type="checkbox" class="mld-disciplinary-warnings-no" <?php echo $disciplinary_uncheck; ?>>
			</p>
		</div>
		<div class="mld-clear-both"></div>
	</div>
	<div class="mld-leaving-reasons">
		<div class="mld-leaving-reasons-label mld-min-height">
			<p><?php echo __( "What was the reason for the applicant leaving your organisation?", 'myrtle-learning-dashboard' ); ?></p>
		</div>
		<div class="mld-leaving-reasons-data mld-min-height" contenteditable="true"><?php echo $l_reason; ?></div>
	</div>
	<div class="mld-re-employ-applicent">
		<div class="mld-re-employ-applicent-label mld-min-height">
			<p><?php echo __( 'Did you find the applicant to be honest and trustworthy?', 'myrtle-learning-dashboard' ); ?></p>
		</div>
		<div class="mld-re-employ-applicent-data mld-min-height">
			<p>
				<label><?php echo __( 'Yes', 'myrtle-learning-dashboard' ); ?></label>
				<input type="checkbox" class="mld-re-employ-yes" <?php echo $re_employ_check; ?>>
				<label><?php echo __( 'No', 'myrtle-learning-dashboard' ); ?></label>
				<input type="checkbox" class="mld-re-employ-no" <?php echo $re_employ_uncheck; ?>>
			</p>
		</div>
		<div class="mld-clear-both"></div>
	</div>
	<div class="mld-applicent-job-describe">
		<div class="mld-applicent-job-describe-label mld-min-height">
			<p><?php echo __( 'Do you consider the applicant has the ability and is suitable to perform the job described above?', 'myrtle-learning-dashboard' ); ?></p>
		</div>
		<div class="mld-applicent-job-describe-data mld-min-height">
			<p>
				<label><?php echo __( 'Yes', 'myrtle-learning-dashboard' ); ?></label>
				<input type="checkbox" class="mld-job-describe-yes" <?php echo $j_describe_check; ?>>
				<label><?php echo __( 'No', 'myrtle-learning-dashboard' ); ?></label>
				<input type="checkbox" class="mld-job-describe-no" <?php echo $j_describe_uncheck; ?>>
			</p>
		</div>
		<div class="mld-clear-both"></div>
	</div>
	<div class="mld-applicent-specification">
		<div class="mld-applicent-specification-label mld-min-height">
			<p><?php echo __( "Is the applicant able to demonstrate that s/he meets the requirements of the person specification?", 'myrtle-learning-dashboard' ); ?></p>
		</div>
		<div class="mld-applicent-specification-data mld-min-height" contenteditable="true"><?php echo $a_specification; ?></div>
	</div>
	<div class="mld-applicent-work-with-children">
		<div class="mld-applicent-work-with-children-label mld-min-height">
			<p><?php echo __( 'Are you satisfied that the candidate is suitable to work with children?', 'myrtle-learning-dashboard' ); ?></p>
		</div>
		<div class="mld-applicent-work-with-children-data mld-min-height">
			<p>
				<label><?php echo __( 'Yes', 'myrtle-learning-dashboard' ); ?></label>
				<input type="checkbox" class="mld-work-with-children-yes" <?php echo $w_w_children_check; ?>>
				<label><?php echo __( 'No', 'myrtle-learning-dashboard' ); ?></label>
				<input type="checkbox" class="mld-work-with-children-no" <?php echo $w_w_children_uncheck; ?>>
			</p>
		</div>
		<div class="mld-clear-both"></div>
	</div>
	<div class="mld-applicent-work-with-children-answer">
		<div class="mld-applicent-work-with-children-answer-label mld-min-height">
			<p><?php echo __( "If you have answered ‘no’ to the above question, please specify your concerns and why you believe the individual may not be suitable.", 'myrtle-learning-dashboard' ); ?></p>
		</div>
		<div class="mld-applicent-work-with-children-answer-data mld-min-height" contenteditable="true"><?php echo $w_w_children_ans; ?></div>
	</div>
	<div class="mld-applicent-further-comment">
		<div class="mld-applicent-further-comment-label mld-min-height">
			<p><?php echo __( "Please provide any further comments on the applicant's suitability for employment into the post described above.", 'myrtle-learning-dashboard' ); ?></p>
		</div>
		<div class="mld-applicent-further-comment-data mld-min-height" contenteditable="true"><?php echo $further_comment; ?></div>
	</div>
	<div class="mld-organization-table">
		<table style="width: 100%;">
			<tr>
				<td width="30%" class="mld-label"><?php echo __( 'Name', 'myrtle-learning-dashboard' ); ?></td>
				<td contenteditable="true" class="mld-name"><?php echo $name; ?></td>
			</tr>
			<tr>
				<td width="30%" class="mld-label"><?php echo __( 'Signed', 'myrtle-learning-dashboard' ); ?></td>
				<td contenteditable="true" class="mld-sign">
					<img src="<?php echo $sign_url; ?>">
				</td>
			</tr>
			<tr>
				<td width="30%" class="mld-label"><?php echo __( 'Date', 'myrtle-learning-dashboard' ); ?></td>
				<td contenteditable="true" class="mld-date"><?php echo $date; ?></td>
			</tr>
			<tr>
				<td width="30%" class="mld-label"><?php echo __( 'Telephone#', 'myrtle-learning-dashboard' ); ?></td>
				<td contenteditable="true" class="mld-telephone"><?php echo $telephone; ?></td>
			</tr>
			<tr>
				<td width="30%" class="mld-label"><?php echo __( 'Organisation stamp', 'myrtle-learning-dashboard' ); ?></td>
				<td contenteditable="true" class="mld-stamp">
					<img src="<?php echo $stump_url; ?>">
				</td>
			</tr>
		</table>
	</div>
</div>