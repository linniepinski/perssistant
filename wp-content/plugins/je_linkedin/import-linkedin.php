
<div id="linkedin-import" class="et-main-main" <?php if ( !isset($sub_section) || $sub_section != 'import') echo 'style="display:none"' ?>  >
<?php 
	$industry=$this->et_linkedin_industry();
	$search_str	=	$this->get_search_string();
	if(isset($search_str['job-type']) == '')  $search_str['job-type']='';
	if(isset($search_str['industry']) == '')  $search_str['industry']='';
	if(isset($search_str['company-name']) == '')  $search_str['company-name']='';
	$industry_l =str_replace('industry,', '', $search_str['industry']);
	?>
	<form id="linkedin_job">
		
		<div class="title font-quicksand"><?php _e('Import LinkedIn jobs manually') ?></div>
		<div class="desc">
			Search LinkedIn.com for jobs you want to import into your website
			<div class="import-search-form">
			
			<div class="form no-margin no-padding no-background">
				<div class="select-style et-button-select" style="width: 270px;" >
					<select style="width: 270px;"  name="facet" class="" title="<?php _e("ALL CATEGORIES", ET_DOMAIN); ?>">
						<option class="empty" value=""><?php _e("ALL CATEGORIES", ET_DOMAIN); ?> </option>
						<?php foreach ($industry as $key => $value) { ?>
							<option value="industry,<?php echo $key ?>" <?php if($key == $industry_l) echo 'selected="selected"' ; ?>>
								<?php echo $value ?>
							</option>
						<?php } ?>
					</select>
				</div>
			</div>		
				<div class="form no-margin no-padding no-background">
					<?php 
						$support_co	=	$this->linkedin_support_country();
					
						
					?>
					<div class="select-style et-button-select" style="width: 270px;">
						<select style="width: 270px;"  name="country-code" class="" title="<?php _e("Choose your country", ET_DOMAIN); ?>">
							<option class="empty" value=""><?php _e("Choose your country", ET_DOMAIN); ?> </option>
							<?php foreach ($support_co as $key => $value) { ?>
								<option value="<?php echo $key ?>" <?php if($key == $search_str['country-code']) echo 'selected="selected"' ; ?>><?php echo $value ?></option>
							<?php } ?>
						</select>
						
					</div>
				</div>
				<div class="form no-margin no-padding no-background">
				   <div class="form-item">
						<input class="bg-grey-input" placeholder="<?php _e('Postal Code', ET_DOMAIN) ?>" type="text" value="<?php echo $search_str['postal-code']; ?>" name="postal-code" />
					  
					</div>
					
					</div>
				<div class="form no-margin no-padding no-background">
				   <div class="form-item">
						<input class="bg-grey-input" placeholder="<?php _e('Keywords', ET_DOMAIN) ?>" type="text" value="<?php echo $search_str['keywords']; ?>" name="keywords" />
					</div>
					</div>
			<div class="form no-margin no-padding no-background">
					<div class="form-item">
						<input class="bg-grey-input" placeholder="<?php _e('Job title', ET_DOMAIN) ?>" type="text" value="<?php echo $search_str['job-title']; ?>" name="job-title" />
					</div>
				</div>
				<div class="form no-margin no-padding no-background">
					<div class="form-item">
						<input class="bg-grey-input" placeholder="<?php _e('Company name', ET_DOMAIN) ?>" type="text" value="<?php echo $search_str['company-name']; ?>" name="company-name" />
					</div>
				</div>
				
			</div>
		</div>
	</form>
	<div class="clear"></div>
	<div class="f-left-all">
		<button class="engine-btn btn-button engine-btn-icon" id="linkedin_search">
			<?php _e('Search', ET_DOMAIN)?>
		</button>
	</div>
	<div class="clear"></div>
	<div id="import_search_result" style="display: none">
		<form action="" id="import">
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
							<th width="58%">Job Type</th>
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
						'role' => ''
					)); 
					$user_data = array();
					foreach ($users as $user) {
						$user_data[] = array( 'id' => $user->ID, 'label' => $user->display_name );
					}
					?>
					<input type="hidden" name="import_author" id="import_author">
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