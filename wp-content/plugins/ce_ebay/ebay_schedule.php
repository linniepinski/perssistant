
<div id="ebay-schedule" class="ebay-box hide">
	<div class="module">
		<div class="title font-quicksand"><?php _e("recurrence", ET_DOMAIN); _e(" (The next timestamp for a cron event: ", ET_DOMAIN); echo date('d M y g:i:s', wp_next_scheduled( 'simplyhired_import_schedule_event')) ?> )</div>
		<div class="desc">
			<?php _e("How often should the event reoccur?", ET_DOMAIN); ?>
			<div class="form no-margin no-padding no-background">
				<div class="form-item" style="width:61%">

					<?php $number_days 	=  get_option('ce_ebay_days_run',5) ; ?>
					<input style="width : 88%;" class="bg-grey-input number_day" type="text" value="<?php echo $number_days; ?>"  id="number_day" name="number_day"> (days)

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
				<button id="add-ebay-schedule" class="btn-ebay"><?php _e("Add new", ET_DOMAIN); ?> <span class="icon" data-icon="+"></span></button>
			</div>
		</div>

		<?php 		$schedule_list	=	$this->get_schedule_option();	?>

		<table class="list-import-ad" id="schedule_list">
			<tr>
				<th width="15%"><?php _e("Keywords", ET_DOMAIN); ?></th>
				<th width="25%"><?php _e("Site ", ET_DOMAIN); ?></th>
				<th width="15%"><?php _e("Category", ET_DOMAIN); ?></th>
				<th width="15%"><?php _e("User ID", ET_DOMAIN); ?></th>
				<th width="20%"></th>
			</tr>
			<?php 
				foreach ($schedule_list as $key => $schedule) {
				?>
			<tr id="schedule-<?php echo $schedule['schedule_id'] ?>" class="<?php if(isset($schedule['ON']) && $schedule['ON'] == 0 ) echo 'off'; ?> schedule-option">
				<td><a target="_blank" href="<?php echo $schedule['keywords'] ?>" title="<?php echo $schedule['keywords'] ?>"><?php echo $schedule['keywords'] ?></a></td>

				<td><?php echo ($schedule['site_name'] != '') ? $schedule['site_name'] : $schedule['site_name'] ?></td>
				<td><?php if(isset( $schedule['cat_name']) ) echo  $schedule['cat_name'];  else _e("All Category", ET_DOMAIN) ?></td>
				<td><?php if(isset($schedule['user_id'])) echo $schedule['user_id']  ?></td>

				<td>
					<a href="#" data-id = "<?php echo $schedule['schedule_id'] ?>" title="<?php if(isset($schedule['ON']) && $schedule['ON'] == 0 ) echo 'Turn on this schedule'; else echo 'Turn off this schedule' ?>" class="icon power"  data-icon="Q" ></a>
					<a href="#" data-id = "<?php echo $schedule['schedule_id'] ?>" title="Edit schedule" class="icon edit" data-icon="p"></a>
					<a href="#" data-id = "<?php echo $schedule['schedule_id'] ?>" title="Delete schedule" class="icon delete" data-icon="#"></a>
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
				<a target="_blank" href="{{ keywords  }}">
					{{ keywords }}
				</a>
			</td>
			<td> {{ site_name }} </td>
			<td><# if( category == "-1" ) { #> All categories <# } else { #> {{ cat_name }} <#} #> </td>
			<td>{{author}}</td>
			<td>
				<a href="#" data-id ="{{ schedule_id }}" title="<# if( ON == 1 ) { #> Turn off this schedule <# } else { #> Turn on this schedule <# } #>" data-icon="Q" class="icon power"></a>
				<a href="#" data-id ="{{ schedule_id }}" title="Edit schedule" class="icon edit" data-icon="p"></a>
				<a href="#" data-id ="{{ schedule_id }}" title="Delete schedule" class="icon delete" data-icon="#"></a>
				<input class="schedule_id" type="hidden" name="schedule_id" value="{{ schedule_id }}" />
			</td>';
		?>

	</script>
	<script id="schedule_template" type="text/template">

		<div class="module form-schedule" id="form-schedule">
			<form action="#" method="post" name="schedule_form" id="schedule_form">
				<?php wp_nonce_field('update_schedule','simplyhired_update_schedule'); ?>
				<div class="form-item">
					<?php

					$args = array(
						'echo' 		=> true,
						'class' 	=> 'select-schedule'
					);
					ce_ebay_dropdow_site($args);
					?>
				</div>

				<div class="form-item">
					<?php  ce_ebay_dropdow_categories($args); ?>
				</div>

				<div class="form-item">
					<input placeholder='<?php _e("Ad title", ET_DOMAIN); ?>' type="text" name="keywords" value="<?php echo '{{ keywords  }}'; ?>" id="schedule_jobtitle" />
				</div>

				<div class="form-item">
					<input placeholder='<?php _e("User ID", ET_DOMAIN); ?>' type="text" name="user_id" value="<?php echo '{{ user_id }}'; ?>" id="schedule_lc" />
				</div>

				<div class="form-item">
					<input placeholder='<?php _e("Number of ads to import (max 25)", ET_DOMAIN); ?>' type="text" name="number" value="<?php echo '{{ number }}'; ?>" id="schedule_ws" />
				</div>

				<div class="form-item">
					<label><?php _e("Assign a Category for the ads", ET_DOMAIN); ?></label>
					<?php
						$ad_cats	=	get_terms(CE_AD_CAT, array('orderby' => 'id', 'order' => 'ASC','hide_empty' => false ,'pad_counts' => true));
					?>
					<div class="select-schedule et-button-select select-style select_cat">
						<select name="<?php echo CE_AD_CAT; ?>" id="<?php echo CE_AD_CAT; ?>">
							<?php foreach ($ad_cats as $key => $ad_cat) { ?>
								<option value="<?php echo $ad_cat->slug ?>"><?php echo $ad_cat->name ?></option>
							<?php } ?>
						</select>
					</div>
				</div>

				<div class="form-item clearfix">
					<label><?php _e("Assign an Author for the aobs", ET_DOMAIN); ?></label>
					<input type="hidden" name="import_author" value="<?php echo '{{import_author}}'; ?>" id="import_author_schedule" />
					<input class="backend-input" type="text" placeholder="<?php _e("Enter an author's name", ET_DOMAIN); ?>" value="<?php echo '{{author}}' ?>" id="search_author_schedule" name="author" />
				</div>
				<div class="form-button">
					<div class="btn-add-schedule">
						<input type="submit" value="<?php _e("Save Schedule", ET_DOMAIN); ?>" id="submit_schedule" class="btn-button" />
						<span class="icon" data-icon="+"></span>
					</div>
				</div>
				<input type="hidden" name="action" value="update-ebay-schedule" />
				<input type="hidden" class="schedule_id" name="schedule_id" value="<?php echo '{{schedule_id}}'; ?>" />
				<input type="hidden"  name="ON" value="<?php echo '{{ON }}'; ?>" />
			</form>
		</div>
	</script>

</div>