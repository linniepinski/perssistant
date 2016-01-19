<?php //echo date('d M y g:i:s', time()) ?>
<div id="rss-schedule" class="et-main-main clearfix" <?php if ( !isset($sub_section) || $sub_section != 'schedule') echo 'style="display:none"' ?> >
	<div class="module">
		<div class="title font-quicksand"><?php _e("Recurrence", ET_DOMAIN); _e(" (The next timestamp for a cron event: ", ET_DOMAIN); echo date('d M y g:i:s', wp_next_scheduled( 'rss_import_schedule_event')) ?> )</div>
		<div class="desc">
			How often should the event reoccur?
			<div class="form no-margin no-padding no-background">
				<div class="form-item">
					<input style="width : 50%;" class="bg-grey-input " type="text" value="<?php echo get_option('et_rss_recurrence',5) ?>" id="recurrence" name="recurrence"> (days)
				</div>
			</div>
			<!--<a class="find-out font-quicksand" href="#">Find out more <span class="icon" data-icon="i"></span></a> -->
		</div>
		<!--
		-->
	</div>

	<div class="module">
		<div class="title font-quicksand"><?php _e("RSS Link Schedule", ET_DOMAIN); ?></div>
		<div class="desc">
			<div class="btn-add-importer">
				<button id="add-rss-schedule"><?php _e("Add new", ET_DOMAIN); ?> <span class="icon" data-icon="+"></span></button>
			</div>
		</div>
		<?php 
			$schedule_list	=	$this->get_schedule_option();
		?>
		<table class="list-import-job" id="schedule_list">
			<tr>
				<th width="60%"><?php _e("RSS Link", ET_DOMAIN); ?></th>
				<th width="20%"><?php _e("Author", ET_DOMAIN); ?></th>
				<th width="20%"></th>
			</tr>
			<?php foreach ($schedule_list as $key => $schedule) { ?>
			<tr id="schedule-<?php echo $schedule['schedule_id'] ?>" class="<?php if(isset($schedule['ON']) && $schedule['ON'] == 0 ) echo 'off'; ?>">
				<td><a target="_blank" href="<?php echo $schedule['rss_link'] ?>" title="<?php echo $schedule['rss_link'] ?>"><?php echo $schedule['rss_link'] ?></a></td>
				<td><?php echo $schedule['author'] ?></td>
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
	<script id="et_rss_list" type="text/template">
		<?php 
		echo '<td>
				<a target="_blank" href="{{ rss_link }}">
					{{ rss_link }}
				</a>
			</td>
			<td>{{author}}</td>
			<td>
				<a href="#" title="<# if( ON == 1 ) { #> Turn off this schedule <# } else { #> Turn on this schedule <# } #>" data-icon="Q" class="icon power"></a>
				<a href="#" title="Edit schedule" class="icon edit" data-icon="p"></a>
				<a href="#" title="Delete schedule" class="icon delete" data-icon="#"></a>
				<input class="schedule_id" type="hidden" name="schedule_id" value="{{schedule_id}}" />
			</td>';
		?>

	</script>
	<script id="et_rss_schedule" type="text/template">
		<div class="module form-schedule" id="form-schedule">
			<form action="#" method="post" name="import_rss_schedule_form" id="rss_schedule_form">
				<div class="form-item">
					<label>RSS link</label>
					<input type="text" name="rss_link" value="<?php echo '{{ rss_link }}'; ?>" id="rss_link_input" />
				</div>
				<div class="form-item">
					<label>Category</label>
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
					<label>Job Type</label>
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
				<div class="form-item">
					<label>Job Location</label>
					<div class="location">
						<input type="text" name="job_location" value="<?php echo '<# if( job_location !== undefined) { #>{{ job_location }} <# } #>'; ?>" />
					</div>
				</div>
				<div class="form-item clearfix">
					<label>Author</label>
					<input type="hidden" name="import_author" value="<?php echo '{{import_author}}'; ?>" id="import_author_schedule" />
					<input class="backend-input" type="text" placeholder="<?php _e("Entering job author name", ET_DOMAIN); ?>" value="<?php echo '{{author}}' ?>" id="search_author_schedule" name="author" />
				</div>		        				
				<div class="form-button">
					<div class="btn-add-schedule">
						<input type="submit" value="Save Schedule" id="submit_schedule" class="btn-button" />
						<span class="icon" data-icon="+"></span>
					</div>
				</div>
				<input type="hidden" name="action" value="update-rss-import-schedule" />
				<input type="hidden" class="schedule_id" name="schedule_id" value="<?php echo '{{ schedule_id }}'; ?>" />
				<input type="hidden"  name="ON" value="<?php echo '{{ON}}'; ?>" />
			</form>
		</div>
	</script>
	<script id="et_schedule_preview" type="text/template">
		<?php 
		echo '<tr>
			<td><a target="_blank" href="{{ link }}">{{title}}</a></td>
			<td><a href="{{link}}">{{link}}</a></td>
			<td>{{creator}}</td>
			<td>{{pubDate}}</td>
		</tr>'; 
		?>
	</script>

	<div class="module job-import-review" style="display : none;">
		<div class="title">Preview</div>
		<table class="list-import-job-preview">
			<tr>
				<th>Title</th>
				<th>Link</th>
				<th>Creator</th>
				<th>Date</th>
			</tr>
		</table>
	</div>
	
</div>