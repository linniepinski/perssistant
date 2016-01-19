<?php //update_option ('et_rss_schedule', array()); ?>
<div id="indeed-schedule" class="et-main-main clearfix" <?php if ( !isset($sub_section) || $sub_section != 'schedule') echo 'style="display:none"' ?> >
	<div class="module">
		<div class="title font-quicksand"><?php _e("Recurrence", ET_DOMAIN); ?></div>
		<div class="desc">
			How often should the event reoccur?
			<div class="form no-margin no-padding no-background">
				<div class="form-item">
					<input style="width : 50%;" class="bg-grey-input " type="text" value="<?php echo get_option('et_indeed_recurrence',7) ?>" id="recurrence" name="recurrence"> (days)
				</div>
			</div>
			<!--<a class="find-out font-quicksand" href="#">Find out more <span class="icon" data-icon="i"></span></a> -->
		</div>
		<!--
		-->
	</div>

	<div class="module">
		<div class="title font-quicksand"><?php _e("Schedules", ET_DOMAIN); ?></div>
		<div class="desc">
			<div class="module form-schedule" id="form-schedule" style="display: none">
				<form action="#" method="post" name="import_indeed_schedule_form" id="indeed_schedule_form">
					<div class="form-item">
						<label><?php _e('Query data', ET_DOMAIN) ?></label>
						<?php 
							$support_co	=	$this->indeed_support_country();
							$co = '';
						?>
						<div class="select-category et-button-select select-style">
							<select name="co" class="" title="<?php _e("Choose your country", ET_DOMAIN); ?>">
								<option class="empty" value=""><?php _e("Choose your country", ET_DOMAIN); ?> </option>
								<?php foreach ($support_co as $key => $value) { ?>
									<option value="<?php echo $key ?>" ><?php echo $value ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="form-item">
						<input type="text" name="title" placeholder="<?php _e('Job title', ET_DOMAIN) ?>" value="" id="indeed_link_input" />
					</div>
					<div class="form-item" >
						<input type="text" name="loc" placeholder="<?php _e('Location', ET_DOMAIN) ?>" value="" id="indeed_loc_input" />
					</div>
					<div class="form-item">
						<input type="text" name="lim" placeholder="<?php _e('Number of jobs to import (max 25)', ET_DOMAIN) ?>" value="" id="indeed_lim_input" />
					</div>
					<div class="form-item">
						<label><?php _e('Job category', ET_DOMAIN) ?></label>
						<?php 
							$job_cats	=	get_terms('job_category', array('orderby' => 'id', 'order' => 'ASC','hide_empty' => false ,'pad_counts' => true));
						?>
						<div class="select-category et-button-select select-style">
							<select name="cat" id="job_category">
								<?php foreach ($job_cats as $key => $job_cat) { ?>
									<option value="<?php echo $job_cat->slug ?>"><?php echo $job_cat->name ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="form-item">
						<label><?php _e('Job type', ET_DOMAIN) ?></label>
						<?php 
							$job_types	=	get_terms('job_type', array('orderby' => 'id', 'order' => 'ASC','hide_empty' => false ,'pad_counts' => true));
						?>
						<div class="select-category et-button-select select-style">
							<select name="type" id="job_type">
								<?php foreach ($job_types as $key => $job_type) { ?>
									<option value="<?php echo $job_type->slug ?>"><?php echo $job_type->name ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="form-item clearfix">
						<label>Author</label>
						<input type="hidden" name="auth_id" value="" id="import_author_schedule" />
						<input class="backend-input" type="text" placeholder="<?php _e("Provide an author's name for the jobs", ET_DOMAIN); ?>" value="" id="schedule_author" name="author" />
					</div>		        				
					<div class="form-button">
						<div class="btn-add-schedule">
							<input type="submit" value="Save Schedule" id="submit_schedule" />
							<span class="icon" data-icon="+"></span>
						</div>
					</div>
					<input type="hidden" class="schedule_id" name="schedule_id" value="" />
				</form>
			</div>
			<div class="btn-add-importer">
				<button id="add-indeed-schedule"><?php _e("Add", ET_DOMAIN); ?> <span class="icon" data-icon="+"></span></button>
			</div>
		</div>
		<?php 
		$schedules	= get_posts(array(
				'post_type' 	=> 'indeed_schedule',
				'post_status' 	=> array('draft','publish','pending'),
				'order' 		=> 'asc',
				'posts_per_page' => -1
			));
		?>
		<table class="list-import-job" id="schedule_list">
			<tr>
				<th width=""><?php _e("Job Title", ET_DOMAIN); ?></th>
				<th width=""><?php _e("Location", ET_DOMAIN); ?></th>
				<th width=""><?php _e("Country", ET_DOMAIN); ?></th>
				<th width=""><?php _e("Limit", ET_DOMAIN); ?></th>
				<th width=""><?php _e("Actions", ET_DOMAIN); ?></th>
			</tr>
			<?php 
			foreach ($schedules as $i => $row) {
				$sche = $this->build_schedule_item($row);
				?>
					<tr class="<?php if(!$sche['active']) echo 'off' ?>" id="schedule-<?php echo $sche['id'] ?>">
						<td><?php echo $sche['title'] ?></td>
						<td><?php echo $sche['location'] ?></td>
						<td><?php echo $sche['country'] ?></td>
						<td><?php echo $sche['limit'] ?></td>
						<td>
							<a href="#" class="icon power" data-id="<?php echo $sche['id'] ?>" data-icon="Q" title="<?php if(!$sche['active']) _e('Turn on this schedule', ET_DOMAIN); else _e('Turn off this schedule', ET_DOMAIN);  ?>"><span>Off</span></a>
							<?php /* <a href="#" class="icon edit" data-id="<?php echo $sche['id'] ?>" data-icon="p"><span>Edit</span></a> */ ?>
							<a href="#" class="icon delete" data-id="<?php echo $sche['id'] ?>" data-icon="#" title="<?php _e('Delete schedule', ET_DOMAIN) ?>"><span>Delete</span></a>
							<input class="schedule_id" type="hidden" name="schedule_id" value="<?php echo $sche['id'] ?>" />
						</td>
					</tr>
				<?php
			}
			?>
			
		</table>
	</div>
	<script  id="schedule_data" type="text/data"><?php echo json_encode($schedule_list) ?></script>
	<script id="schedule_row" type="text/template">
		<tr class="<# if (!active) { #> <?php echo '{{off}}' ?> <# } #>" id="schedule-<?php echo '{{id}}' ?>">
			<td><?php echo '{{title}}' ?></td>
			<td><?php echo '{{location}}' ?></td>
			<td><?php echo '{{country}}' ?></td>
			<td><?php echo '{{limit}}' ?></td>
			<td>
				<a href="#" class="icon power" data-id="<?php echo ' {{ id }}' ?>" data-icon="Q" title="Turn on this schedule"><span>Off</span></a>
				<a href="#" class="icon delete" data-id="<?php echo '{{ id }}' ?>" data-icon="#" title="Delete schedule"><span>Delete</span></a>
				<input class="schedule_id" type="hidden" name="schedule_id" value="<?php echo '{{id}}' ?>" />
			</td>
		</tr>
	</script>	
</div>