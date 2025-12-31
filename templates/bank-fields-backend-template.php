<div class="mld-personal-bank-detail-wrapper">
	<b><?php echo __( '1.Personal Details', 'myrtle-learning-dashboard' ); ?></b>
	<div class="mld-personal-detail-main-wrapper">
		<div class="mld-personal-detail-wrapper">
			<div class="mld-pd-row1">
				<div class="mld-pd-title-wrapper">
					<div class="mld-pd-title mld-padding"><?php echo __( 'Title', 'myrtle-learning-dashboard' ); ?></div>
					<div class="mld-pd-title-data mld-padding"><?php echo $title; ?></div>
				</div>
				<div class="mld-pd-forename-wrapper">
					<div class="mld-pd-forename mld-padding"><?php echo __( 'Forename', 'myrtle-learning-dashboard' ); ?></div>
					<div class="mld-pd-forename-data mld-padding"><?php echo $forename; ?></div>
				</div>
				<div class="mld-pd-surname-wrapper">
					<div class="mld-pd-surname mld-padding"><?php echo __( 'Surname', 'myrtle-learning-dashboard' ); ?></div>
					<div class="mld-pd-surname-data mld-padding"><?php echo $surname; ?></div>
				</div>
			</div>
			<div class="mld-pd-row2">
				<div class="mld-pd-ni-number-wrapper">
					<div class="mld-pd-ni-number mld-padding"><?php echo __( 'NI Number', 'myrtle-learning-dashboard' ); ?></div>
					<div class="mld-pd-ni-number-data mld-padding"><?php echo $ni_number; ?></div>
				</div>
				<div class="mld-pd-dob-wrapper">
					<div class="mld-pd-dob mld-padding"><?php echo __( 'Date of Birth', 'myrtle-learning-dashboard' ); ?></div>
					<div class="mld-pd-dob-data mld-padding"><?php echo $dob; ?></div>
				</div>
			</div>
			<div class="mld-pd-row3">
				<div class="mld-pd-home-address-wrapper">
					<div class="mld-pd-home-address mld-padding"><?php echo __( 'Home Address', 'myrtle-learning-dashboard' ); ?></div>
					<div class="mld-pd-home-address-data mld-padding"><?php echo $home_address; ?></div>
				</div>
			</div>
			<div class="mld-pd-row4">
				<div class="mld-pd-home-email-wrapper">
					<div class="mld-pd-home-email mld-padding"><?php echo __( 'Home Email', 'myrtle-learning-dashboard' ); ?></div>
					<div class="mld-pd-home-email-data mld-padding"><?php echo $home_email; ?></div>
				</div>
				<div class="mld-pd-mobile-number-wrapper">
					<div class="mld-pd-mobile-number mld-padding"><?php echo __( 'Mobile Number', 'myrtle-learning-dashboard' ); ?></div>
					<div class="mld-pd-mobile-number-data mld-padding"><?php echo $mobile_number; ?></div>
				</div>
			</div>
			<div class="mld-pd-row5">
				<div class="mld-pd-subjects-wrapper">
					<div class="mld-pd-subjects mld-padding"><?php echo __( 'Dept./Subject(s)', 'myrtle-learning-dashboard' ); ?></div>
					<div class="mld-pd-subjects-data mld-padding"><?php echo $subjects; ?></div>
				</div>
			</div>
		</div>
	</div>
	<b><?php echo __( '2.Bank Details', 'myrtle-learning-dashboard' ); ?></b>
	<div class="mld-bank-detail-main-wrapper">
		<div class="mld-bank-detail-wrapper">
			<div class="mld-bank-detail-info">
				<?php echo __( ' Please provide details of the bank account you wish your salary to be paid into.' ); ?>
			</div>
			<div class="mld-bank-detail-row1">
				<div class="mld-bd-b-name-wrapper">
					<div class="mld-bd-name mld-padding"><?php echo __( 'Bank Name', 'myrtle-learning-dashboard' ); ?></div>
					<div class="mld-bd-name-data mld-padding"><?php echo $bank_name; ?></div>
				</div>
				<div class="mld-bd-b-account-name-wrapper">
					<div class="mld-bd-account-name mld-padding"><?php echo __( 'Account Holders Name', 'myrtle-learning-dashboard' ); ?></div>
					<div class="mld-bd-account-name-data mld-padding"><?php echo $account_holder_name; ?></div>
				</div>
			</div>
			<div class="mld-bank-detail-row2">
				<div class="mld-bd-sort-code-wrapper">
					<div class="mld-bd-sort-code mld-padding"><?php echo __( 'Sort Code', 'myrtle-learning-dashboard' ); ?></div>
					<div class="mld-bd-sort-code-data mld-padding"><?php echo $sort_code; ?></div>
				</div>
				<div class="mld-bd-b-account-number-wrapper">
					<div class="mld-bd-b-account-number mld-padding"><?php echo __( 'Account Number', 'myrtle-learning-dashboard' ); ?></div>
					<div class="mld-bd-b-account-number-data mld-padding"><?php echo $account_number; ?></div>
				</div>
			</div>
			<div class="mld-bank-detail-row3">
				<div class="mld-bd-bank-address-wrapper">
					<div class="mld-bd-bank-address mld-padding"><?php echo __( 'Bank Address', 'myrtle-learning-dashboard' ); ?></div>
					<div class="mld-bd-bank-address-data mld-padding"><?php echo $bank_address; ?></div>
				</div>
			</div>
		</div>
	</div>
	<b><?php echo __( '3.Disclosure & Barring Service', 'myrtle-learning-dashboard' ); ?></b>
	<div class="mld-disclosure-service-main-wrapper">
		<div class="mld-disclosure-service-wrapper">
			<div class="mld-disclosure-service-info">
				<?php echo __( 'Please provide the following information if you consent to Myrtle Learning performing a status check' ); ?>
			</div>
			<div class="mld-disclosure-service-row1">
				<div class="mld-certificate-number-wrapper">
					<div class="mld-certificate-number mld-padding"><?php echo __( 'Certificate Number', 'myrtle-learning-dashboard' ); ?></div>
					<div class="mld-certificate-number-data mld-padding"><?php echo $certificate_number; ?></div>
				</div>
				<div class="mld-certificate-surname-wrapper">
					<div class="mld-certificate-surname mld-padding"><?php echo __( "Applicant's Surname on Certificate", 'myrtle-learning-dashboard' ); ?></div>
					<div class="mld-certificate-surname-data mld-padding"><?php echo $surname_on_certificate; ?></div>
				</div>
			</div>
			<div class="mld-disclosure-service-row2">
				<div class="mld-current-yn-wrapper">
					<div class="mld-current-yn mld-padding"><?php echo __( 'Current (Y/N)', 'myrtle-learning-dashboard' ); ?></div>
					<div class="mld-current-yn-data mld-padding"><?php echo $current_yn; ?></div>
				</div>
				<div class="mld-certificate-dob-wrapper">
					<div class="mld-certificate-dob mld-padding"><?php echo __( 'Date of Birth on Certificate', 'myrtle-learning-dashboard' ); ?></div>
					<div class="mld-certificate-dob-data mld-padding"><?php echo $dob_on_certificate; ?></div>
				</div>
			</div>
			<div class="mld-disclosure-service-row3">
				<div class="mld-internal-use-wrapper">
					<div class="mld-internal-use mld-padding"><?php echo __( 'Internal Use', 'myrtle-learning-dashboard' ); ?></div>
					<div class="mld-internal-use-data mld-padding"><?php echo $internal_use; ?></div>
				</div>
			</div>
		</div>
	</div>
	<b><?php echo __( '4. Right to Work', 'myrtle-learning-dashboard' ); ?></b>
	<ul class="mld-righr-to-work-list">
		<li><?php echo __( 'You must be provide one of the documents or combinations of documents in List
		Aor List B below as proof that someone is allowed to work in the UK.', 'myrtle-learning-dashboard' ); ?></li>
		<li><?php echo __( 'You must only accept originals documents.', 'myrtle-learning-dashboard' ); ?></li>
	</ul>
	<div class="mld-list-a">
		<?php echo __( 'List A' ); ?>
	</div>
	<div class="mld-list-a-wrapper">
		<ul>
			<li>
				<input type="checkbox" <?php echo $list_data_1; ?>>
				<span>
					<?php echo __( 'A passport showing the holder, or a person named in the passport as the child
						of the holder, is a British citizen or a citizen of the UK and colonies having the
						right of abode in the UK', 'myrtle-learning-dashboard' ); ?>
				</span>
			</li>
			<li>
				<input type="checkbox" <?php echo $list_data_2; ?>>
				<span>
					<?php echo __( 'A passport or national identity card showing that the holder, or a person
							named in the passport as the child of the holder, is a national of a European
							Economic Area country or Switzerland', 'myrtle-learning-dashboard' ); ?>
				</span>
			</li>
			<li>
				<input type="checkbox" <?php echo $list_data_3; ?>>
				<span>
					<?php echo __( 'A residence permit,registration certificate or document certifying or indicating
								permanent residence issued by the Home Office, the Border and Immigration
								Agency, or the UK Border Agency to a national of a European Economic Area
								country or Switzerland', 'myrtle-learning-dashboard' ); ?>
				</span>
			</li>
			<li>
				<input type="checkbox" <?php echo $list_data_4; ?>>
				<span>
					<?php echo __( 'A permanent residence card or document issued by the Home Office, the
									Border and Immigration Agency, or the UK Border Agency to the family
									member of a national of a European Economic Area country or Switzerland', 'myrtle-learning-dashboard' ); ?>
				</span>
			</li>
			<li>
				<input type="checkbox" <?php echo $list_data_5; ?>>
				<span>
					<?php echo __( 'A Biometric Residence Permit issued by the UK Border Agency to the holder
										which indicates that the person named in it is allowed to stay indefinitely in the
										UK, or has no time limit on their stay in the UK', 'myrtle-learning-dashboard' ); ?>
				</span>
			</li>
			<li>
				<input type="checkbox" <?php echo $list_data_6; ?>>
				<span>
					<?php echo __( 'A passport or other travel document endorsed to show that the holder is
											exempt from immigration control, is allowed to stay indefinitely in the UK, has
											the right of abode in the UK, or has no time limit on their stay in the UK', 'myrtle-learning-dashboard' ); ?>
				</span>
			</li>
			<li>
				<input type="checkbox" <?php echo $list_data_7; ?>>
				<span>
					<?php echo __( 'An Immigration Status Document issued by the Home Office, the Border
												and Immigration Agency, or the UK Border Agency to the holder with an
												endorsement indicating that the person named in it is allowed to stay
												indefinitely in the UK or has no time limit on their stay in the UK together
												with an official document issued by a previous employer or Government agency with the person’s name and National Insurance number (a P45,
												P46, National Insurance card, or letter from a Government agency)', 'myrtle-learning-dashboard' ); ?>
				</span>
			</li>
			<li>
				<input type="checkbox" <?php echo $list_data_8; ?>>
				<span>
					<?php echo __( 'A full birth or adoption certificate issued in the UK which includes the
													name(s) of at least one of the holder’s parents together with an official
													document issued by a previous employer or Government agency with the
													person’s name and National Insurance number (a P45, P46, National
													Insurance card, or letter from a Government agency)', 'myrtle-learning-dashboard' ); ?>
				</span>
			</li>
			<li>
				<input type="checkbox" <?php echo $list_data_9; ?>>
				<span>
					<?php echo __( 'A birth or adoption certificate issued in the Channel Islands, the Isle of Man
														or Ireland together with an official document issued by a previous employer
														or Government agency with the person’s name and National Insurance
														number (a P45, P46, National Insurance card, or letter from a Government
														agency)', 'myrtle-learning-dashboard' ); ?>
				</span>
			</li>
			<li>
				<input type="checkbox" <?php echo $list_data_10; ?>>
				<span>
					<?php echo __( 'A certificate of registration or naturalization as a British citizen together with
															an official document issued by a previous employer or Government agency
															with the person’s name and National Insurance number (a P45, P46, National
															Insurance card, or letter from a Government agency)', 'myrtle-learning-dashboard' ); ?>
				</span>
			</li>
			<li>
				<input type="checkbox" <?php echo $list_data_11; ?>>
				<span>
					<?php echo __( 'A letter issued by the Home Office, the Border and Immigration Agency, or the
							UK Border Agency to the holder which indicates that the person named in it is
							allowed to stay indefinitely in the UK together with an official document
							issued by a previous employer or Government agency with the person’s name
							and National Insurance number (a P45, P46, National Insurance card, or letter
							from a Government agency)', 'myrtle-learning-dashboard' ); ?>
				</span>
			</li>
		</ul>
	</div>
	<div class="mld-list-b">
		<?php echo __( 'List B' ); ?>
	</div>
	<div class="mld-list-b-wrapper">
		<ul>
			<li>
				<input type="checkbox" <?php echo $list_b_data_1; ?>>
				<span>
					<?php echo __( 'A passport or other travel document endorsed to show that the holder is
						allowed to stay in the UK and is allowed to do the type of work you are
						offering', 'myrtle-learning-dashboard' ); ?>
				</span>
			</li>
			<li>
				<input type="checkbox" <?php echo $list_b_data_2; ?>>
				<span>
						<?php echo __( 'A Biometric Residence Permit issued by the UK Border Agency to the holder
							which indicates that the person named in it can stay in the UK and is allowed
							to do the type of work you are offering', 'myrtle-learning-dashboard' ); ?>
				</span>
			</li>
			<li>
				<input type="checkbox" <?php echo $list_b_data_3; ?>>
				<span>
						<?php echo __( 'A residence cardor document issued by the Home Office, the Border and
							Immigration Agency, or the UK Border Agency to a family member of a
							national of a European Economic Area country or Switzerland', 'myrtle-learning-dashboard' ); ?>
				</span>
			</li>
			<li>
				<input type="checkbox" <?php echo $list_b_data_4; ?>>
				<span>
					<?php echo __( 'A work permit or othe rapproval or other approval to take employment issued
							by the Home Office, the Border and Immigration Agency or the UK Border
							Agency together with either a passport or travel document endorsed to show
							the holder is allowed to stay in the UK and is allowed to do the work you are
							offering or a letter issued by the Home Office, the Border and Immigration
							Agency or the UK Border Agency to the holder or to you confirming the same', 'myrtle-learning-dashboard' ); ?>
				</span>
			</li>
			<li>
				<input type="checkbox" <?php echo $list_b_data_5; ?>>
				<span>
					<?php echo __( 'A Certificate of Application which is less than 6 months old issued by the
						Home Office, the Border and Immigration Agency or the UK Border Agency to
						or for the family member of a national of a European Economic Area country
						or Switzerland stating the holder is allowed to take employment together with
						a positive verification letter from the UK Border Agency’s Employer Checking
						Service', 'myrtle-learning-dashboard' ); ?>
				</span>
			</li>
			<li>
				<input type="checkbox" <?php echo $list_b_data_6; ?>>
				<span>
					<?php echo __( 'An Application Registration Card (ARC) issued by the Home Office, the
						Border and Immigration Agency stating that the holder is ‘ALLOWED TO
						WORK’ or ‘EMPLOYMENT PERMITTED’ together with a positive verification
						letter from the UK Border Agency’s Employer Checking Service', 'myrtle-learning-dashboard' ); ?>
				</span>
			</li>
			<li>
				<input type="checkbox" <?php echo $list_b_data_7; ?>>
				<span>
					<?php echo __( 'An Immigration Status Document issued by the Home Office, the Border and
						Immigration Agency or the UK Border Agency to the holder with an
						endorsement indicating that the person named on it can stay in the UK and is
						allowed to do the type of work you are offering together with an official
						document issued by a previous employer or Government agency with the
						person’s name and National Insurance number (a P45, P46, National
						Insurance card, or letter from a Government agency)', 'myrtle-learning-dashboard' ); ?>
				</span>
			</li>
			<li>
				<input type="checkbox" <?php echo $list_b_data_8; ?>>
				<span>
					<?php echo __( 'A letter is sued by the Home Office, the Border and Immigration Agency or the
						UK Border Agency to the holder or to you as the potential employer or
						employer, which indicates that the person named in it can stay in the UK and
						is allowed to do the type of work you are offering together with an official
						document issued by a previous employer or Government agency with the
						person’s name and National Insurance number (a P45, P46, National
						Insurance card, or letter from a Government agency)', 'myrtle-learning-dashboard' ); ?>
				</span>
			</li>				
		</ul>
	</div>
	<div class="mld-signature-wrapper">
		<div class="mld-signature">
			<?php echo __( '5.Signature' ); ?>
		</div>
		<table border="2px" class="mld-signature-table">
			<tr>
				<th><?php echo __( 'Name', 'myrtle-learning-dashboard' ); ?></th>
				<th><?php echo __( 'Signature or Initials for emailed copies', 'myrtle-learning-dashboard' ); ?></th>
				<th><?php echo __( 'Date', 'myrtle-learning-dashboard' ); ?></th>
			</tr>
			<tr>
				<td class="mld-signatur-name"><?php echo $signature_name; ?></td>
				<td contenteditable="true" class="mld-signature">
					<?php
					if( ! is_admin() ) {
						?>
						<input type="file">
						<?php
					}
					?>
				</td>
				<td class="mld-signature-date">
					<?php echo $signature_date;?>
				</td>
			</tr>
		</table>
	</div>
</div>