<?php //echo date('d M y g:i:s', time()) ?>
<div id="simplyhired-schedule" class="et-main-main clearfix" <?php if ( !isset($sub_section) || $sub_section != 'schedule') echo 'style="display:none"' ?> >
	<div class="module">
		<div class="title font-quicksand"><?php _e("recurrence", ET_DOMAIN); _e(" (The next timestamp for a cron event: ", ET_DOMAIN); echo date('d M y g:i:s', wp_next_scheduled( 'simplyhired_import_schedule_event')) ?> )</div>
		<div class="desc">
			<?php _e("How often should the event reoccur?", ET_DOMAIN); ?>
			<div class="form no-margin no-padding no-background">
				<div class="form-item">
					<input style="width : 50%;" class="bg-grey-input " type="text" value="<?php echo get_option('et_simplyhired_recurrence',5) ?>" id="recurrence" name="recurrence"> (days)
				</div>
			</div>
		</div>
		<!--
		-->
	</div>

	<div class="module">
		<div class="title font-quicksand"><?php _e("Simplyhired Schedule List", ET_DOMAIN); ?></div>
		<div class="desc">
			<div class="btn-add-importer">
				<button id="add-simplyhired-schedule"><?php _e("Add new", ET_DOMAIN); ?> <span class="icon" data-icon="+"></span></button>
			</div>
		</div>
		<?php 
		$schedule_list	=	$this->get_schedule_option();	
		?>
		<table class="list-import-job" id="schedule_list">
			<tr>
				<th width="30%"><?php _e("Job title", ET_DOMAIN); ?></th>
				<th width="20%"><?php _e("Location", ET_DOMAIN); ?></th>
				<th width="15%"><?php _e("Contract Type", ET_DOMAIN); ?></th>
				<th width="15%"><?php _e("Author", ET_DOMAIN); ?></th>
				<th width="20%"></th>
			</tr>
			<?php foreach ($schedule_list as $key => $schedule) { ?>
			<tr id="schedule-<?php echo $schedule['schedule_id'] ?>" class="<?php if(isset($schedule['ON']) && $schedule['ON'] == 0 ) echo 'off'; ?>">
				<td><a target="_blank" href="<?php echo $schedule['q'] ?>" title="<?php echo $schedule['q'] ?>"><?php echo $schedule['q'] ?></a></td>
				<td><?php echo ($schedule['lc'] != '') ? $schedule['lc'].' '.$schedule['ls'] : $schedule['lz'] ?></td>
				<td><?php echo ($schedule['fjt'] != '') ? $schedule['fjt'] : __("All contract types", ET_DOMAIN) ?></td>
				<td><?php echo $schedule['author']  ?></td>
				<td>
					<a href="#" title="<?php if(isset($schedule['ON']) && $schedule['ON'] == 0 ) echo 'Turn on this schedule'; else echo 'Turn off this schedule' ?>" class="icon power"  data-icon="Q" ></a>
					<a href="#" title="Edit schedule" class="icon edit" data-icon="p"></a>
					<a href="#" title="Delete schedule" class="icon delete" data-icon="#"></a>
					<input class="schedule_id" type="hidden" name="schedule_id" value="<?php echo $schedule['schedule_id'] ?>" />
				</td>
			</tr>
			<?php } ?>
		</table>
	</div>
	<script  id="schedule_data" type="text/data"><?php echo json_encode($schedule_list) ?></script>
	<script id="et_schedule_list" type="text/template">
		<?php 
		echo '<td>
				<a target="_blank" href="{{ q }}">
					{{ q }}
				</a>
			</td>
			<td> {{ lc }} {{ ls }} </td>
			<td><# if( fjt == "" ) { #> All contract types <# } else { #> {{fjt}} <#} #> </td>
			<td>{{author}}</td>
			<td>
				<a href="#" title="<# if( ON == 1 ) { #> Turn off this schedule <# } else { #> Turn on this schedule <# } #>" data-icon="Q" class="icon power"></a>
				<a href="#" title="Edit schedule" class="icon edit" data-icon="p"></a>
				<a href="#" title="Delete schedule" class="icon delete" data-icon="#"></a>
				<input class="schedule_id" type="hidden" name="schedule_id" value="{{schedule_id}}" />
			</td>';
		?>

	</script>
	<script id="schedule_template" type="text/template">
		<?php 
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
		?>
		<div class="module form-schedule" id="form-schedule">
			<form action="#" method="post" name="schedule_form" id="schedule_form">
				<?php wp_nonce_field('update_schedule','simplyhired_update_schedule'); ?>
				<div class="form-item">
					<label><?php _e("Simplyhired Contract type", ET_DOMAIN); ?></label>
					<div class="select-category select-style et-button-select">
						<select id="schedule_fjt" name="fjt" class="" title="<?php _e("ALL CONTRACT TYPES", ET_DOMAIN); ?>">
							<option class="empty" value=""><?php _e("ALL CONTRACT TYPES", ET_DOMAIN); ?> </option>
							<?php foreach ($contract_type as $key => $value) { ?>
								<option value="<?php echo $key ?>" <?php if($key == $fjt) echo 'selected="selected"' ; ?>>
									<?php echo $value ?>
								</option>
							<?php } ?>
						</select>
					</div>
				</div>	

				<div class="form-item">
					<input placeholder='<?php _e("Job title", ET_DOMAIN); ?>' type="text" name="q" value="<?php echo '{{q}}'; ?>" id="schedule_jobtitle" />
				</div>
				<div class="form-item">
					<input placeholder='<?php _e("Zipcode", ET_DOMAIN); ?>' type="text" name="lz" value="<?php echo '{{lz}}'; ?>" id="schedule_lz" />
				</div>
				<div class="form-item">
					<input placeholder='<?php _e("City", ET_DOMAIN); ?>' type="text" name="lc" value="<?php echo '{{lc}}'; ?>" id="schedule_lc" />
				</div>
				<div class="form-item">
					<input placeholder='<?php _e("State", ET_DOMAIN); ?>' type="text" name="ls" value="<?php echo '{{ ls }}'; ?>" id="schedule_ls" />
				</div>
				<div class="form-item">
					<input placeholder='<?php _e("Number of jobs to import (max 25)", ET_DOMAIN); ?>' type="text" name="ws" value="<?php echo '{{ ws }}'; ?>" id="schedule_ws" />
				</div>
				
				<div class="form-item">
					<label><?php _e("Assign a Category for the jobs", ET_DOMAIN); ?></label>
					<?php 
						$job_cats	=	get_terms('job_category', array('orderby' => 'id', 'order' => 'ASC','hide_empty' => false ,'pad_counts' => true));
					?>
					<div class="select-category et-button-select select-style">
						<select name="job_category" id="job_category">
							<?php foreach ($job_cats as $key => $job_cat) { ?>
								<option value="<?php echo $job_cat->slug ?>"><?php echo $job_cat->name ?></option>
							<?php } ?>
						</select>
					</div>
				</div>
				<div class="form-item">
					<label><?php _e("Assign a Job Type for the jobs", ET_DOMAIN); ?></label>
					<?php 
						$job_types	=	get_terms('job_type', array('orderby' => 'id', 'order' => 'ASC','hide_empty' => false ,'pad_counts' => true));
					?>
					<div class="select-category et-button-select select-style">
						<select name="job_type" id="job_type">
							<?php foreach ($job_types as $key => $job_type) { ?>
								<option value="<?php echo $job_type->slug ?>"><?php echo $job_type->name ?></option>
							<?php } ?>
						</select>
					</div>
				</div>
				<div class="form-item clearfix">
					<label><?php _e("Assign an Author for the jobs", ET_DOMAIN); ?></label>
					<input type="hidden" name="import_author" value="<?php echo '{{import_author}}'; ?>" id="import_author_schedule" />
					<input class="backend-input" type="text" placeholder="<?php _e("Enter an author's name", ET_DOMAIN); ?>" value="<?php echo '{{author}}' ?>" id="search_author_schedule" name="author" />
				</div>		        				
				<div class="form-button">
					<div class="btn-add-schedule">
						<input type="submit" value="<?php _e("Save Schedule", ET_DOMAIN); ?>" id="submit_schedule" class="btn-button" />
						<span class="icon" data-icon="+"></span>
					</div>
				</div>
				<input type="hidden" name="action" value="update-simplyhired-import-schedule" />
				<input type="hidden" class="schedule_id" name="schedule_id" value="<?php echo '{{ schedule_id }}'; ?>" />
				<input type="hidden"  name="ON" value="<?php echo '{{ON}}'; ?>" />
			</form>
		</div>
	</script>
	
</div>