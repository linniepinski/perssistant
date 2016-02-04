<div id="rss-import" class="et-main-main import-container clearfix">
	<form id="rss_job">
		<div class="title font-quicksand"><?php _e('Import jobs manually') ?></div>
		<div class="desc">
			Enter the RSS link and choose the jobs you want to import into your job board
			<div class="import-search-form">
				<div class="form no-margin no-padding no-background">
					<div class="form-item">
						<input class="bg-grey-input" placeholder="<?php _e('RSS link', ET_DOMAIN) ?>" type="text" value="http://www.wphired.com/feed/?post_type=job_listing" id="rss_link" name="rss_link" />
					</div>
				</div>
			</div>
		</div>
	</form>
	
	<div class="f-left-all">
		<div class="engine-btn-area">
			<button class="engine-btn btn-button engine-btn-icon" id="get_rss" data-icon="">
				<?php _e('Get Job', ET_DOMAIN)?>
			</button>
			<span class="icon" data-icon="r"></span>
		</div>
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
								echo '<option value="'. $type->term_id .'">'.$type->name.'</option>';
							} ?>
						</select>
					</div> <br/>
					<?php _e('Job Location', ET_DOMAIN) ?> :
					<div class="location">
						<input name="apply_location" type="text" placeholder="Enter location for selected jobs" />
					</div>
				</div>
			</div>
			<div class="clear"></div>
			<div class="import-tb-container">
				<table>
					<tbody>
						<tr class="heading">
							<th><input class="setall" type="checkbox" name="" checked="checked"></th>
							<th width="35%">Job Title</th>
							<th width="22%">Category</th>
							<th width="22%">Type</th>
							<th width="20%">Location</th>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="import-adjustment">
				<div class="padding-top10 import-results-heading font-quicksand">
					<?php _e("Apply for all jobs",ET_DOMAIN);?>:
				</div>
				<div class="import-results-adjust">
					<?php $users = get_users(array(
						'role' => 'administrator',
						// 'role' => 'company'
					)); 

					$company_user	=	get_users( array(
						// 'role' => 'administrator',
						'role' => 'company'
					) );
					$users	=	array_merge( $users , $company_user );
					$user_data = array();
					foreach ($users as $user) {
						$user_data[] = array( 'id' => $user->ID, 'label' => $user->user_nicename );
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