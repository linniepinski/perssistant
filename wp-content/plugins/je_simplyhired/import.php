<div id="simplyhired-import" class="et-main-main inner-content import-container clearfix" style="display:none" >
	<form id="simplyhired_job">
		<div class="title font-quicksand"><?php _e('Import simplyhired jobs manually') ?></div>
		<div class="desc">
			Search simplyhired.com for jobs you want to import into your website
			<div class="import-search-form">
			<?php 
				$search_string	=	$this->get_search_string();
				$contract_type	=	array(
						'full-time'		=> __("Full-time", ET_DOMAIN), 
						'part-time'		=> __("Part-time", ET_DOMAIN), 
						'telecommute'	=> __("Telecommute", ET_DOMAIN), 
						'contract'		=> __("Contract", ET_DOMAIN), 
						'internship'	=> __("Internship", ET_DOMAIN), 
						'temporary'		=> __("Temporary", ET_DOMAIN), 
						'seasonal'		=> __("Seasonal", ET_DOMAIN), 
						'permanent'		=> __("Permanent", ET_DOMAIN), 
						'volunteer'		=> __("Volunteer", ET_DOMAIN), 
					);
				extract($search_string);
			?>		
				<!-- CONTRACT TYPE -->
				<div class="form no-margin no-padding no-background">
					<div class="select-style et-button-select">
						<select name="fjt" class="" title="<?php _e("ALL CONTRACT TYPES", ET_DOMAIN); ?>">
							<option class="empty" value=""><?php _e("ALL CONTRACT TYPES", ET_DOMAIN); ?> </option>
							<?php foreach ($contract_type as $key => $value) { ?>
								<option value="<?php echo $key ?>" <?php if($key == $fjt) echo 'selected="selected"' ; ?>>
									<?php echo $value ?>
								</option>
							<?php } ?>
						</select>
					</div>
				</div>	

				<div class="form no-margin no-padding no-background">
					<div class="form-item">
						<input class="bg-grey-input" placeholder="<?php _e('Job title', ET_DOMAIN) ?>" type="text" value="<?php echo $q ?>" name="q" />
					</div>
				</div>

				
				<!-- Location -->
				<div class="form no-margin no-padding no-background">
					<div class="form-item">
						<input class="bg-grey-input" placeholder="<?php _e('Zipcode', ET_DOMAIN) ?>" type="text" value="<?php echo $lz ?>" name="lz" />
					</div>
				</div>			
				<div class="form no-margin no-padding no-background">
					<div class="form-item">
						<input class="bg-grey-input" placeholder="<?php _e('City', ET_DOMAIN) ?>" type="text" value="<?php echo urldecode($lc) ?>" name="lc" />
					</div>
				</div>
				<div class="form no-margin no-padding no-background">
					<div class="form-item">
						<input class="bg-grey-input" placeholder="<?php _e('State', ET_DOMAIN) ?>" type="text" value="<?php echo urldecode($ls) ?>" name="ls" />
					</div>
				</div>

				<div class="form no-margin no-padding no-background">
					<div class="form-item">
						<input class="bg-grey-input" placeholder="<?php _e('Window Size (max: 100)', ET_DOMAIN) ?>" type="text" value="<?php echo $ws ?>" name="ws" />
					</div>
				</div>
				
				<div class="form no-margin no-padding no-background">
					<div class="form-item">
						<input class="bg-grey-input" placeholder="<?php _e('Page Number', ET_DOMAIN) ?>" type="text" value="<?php echo $pn ?>" name="pn" />
					</div>
				</div>

				
			</div>
		</div>
	</form>
	
	<div class="f-left-all">
		<button class="engine-btn btn-button engine-btn-icon" id="simplyhired_search">
			<?php _e('Search', ET_DOMAIN)?>
		</button>
	</div>
	<div class="clear"></div>
	<div id="import_search_result" style="display: none">
		<form action="" id="import">
			<?php wp_nonce_field('save_import_job_manual','simplyhired_manual_import_job'); ?>
			<input type="hidden" name="action" value="simplyhired-save-jobs" />

			<div class="import-adjustment">
				<div class="padding-top10 import-results-heading font-quicksand">
					<?php _e("With Selected Jobs",ET_DOMAIN);?>:
				</div>
				<div class="import-results-adjust">
					<?php _e('Job Categories', ET_DOMAIN) ?> :
					<?php $job_types = et_get_job_types(); ?>
					<div class="select-style et-button-select">
						<select name="apply_cat" class="">
							<?php echo et_job_categories_option_list(); ?>
						</select>
					</div><br>
					<?php _e('Job Types', ET_DOMAIN) ?> :
					<div class="select-style et-button-select">
						<select name="apply_type" class="">
							<?php foreach ($job_types as $type) {
								echo '<option value="'. $type->slug .'">'.$type->name.'</option>';
							} ?>
						</select>
					</div>
				</div>
			</div>
			<div class="clear"></div>
			<div class="import-tb-container">
				<table>
					<tbody>
						<tr class="heading">
							<th><input class="setall" type="checkbox" name="" checked="checked"></th>
							<th width="58%">Job Title</th>
							<th width="20%">Category</th>
							<th width="20%">Type</th>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="import-paginator">

			</div>
			<div class="import-adjustment">
				<div class="padding-top10 import-results-heading font-quicksand">
					<?php _e("Apply for all jobs",ET_DOMAIN);?>:
				</div>
				<div class="import-results-adjust">
					<?php $users = get_users(array(
						'role' => 'administrator'
					)); 
					$user_data = array();
					foreach ($users as $user) {
						$user_data[] = array( 'id' => $user->ID, 'label' => $user->user_nicename );
					}
					?>
					<input type="hidden" name="import_author" id="import_author" value='1'>
					<input class="backend-input" placeholder="<?php _e('Author Username', ET_DOMAIN) ?>" type="text" value="" id="search_author" name="author" />
				</div>
			</div>
			<script type="text/data" id="user_source"><?php echo json_encode($user_data);?></script>
			<div class="clear"></div>
			<button type="submit" style="margin-top: 20px" class="et-button btn-button" id="save_import"><?php _e('Import all selected Jobs') ?></button>
			<div class="clear"></div>
		</form>
	</div>
</div>