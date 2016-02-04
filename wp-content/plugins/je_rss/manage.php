<div id="rss-manage" class="et-main-main inner-content import-container clearfix" <?php if ( !isset($sub_section) || $sub_section != 'manage') echo 'style="display:none"'?>>
	
	<div class="title font-quicksand"><?php _e('Plugin options', ET_DOMAIN) ?></div>
	<div class="desc">
		<form id="rss_options">
			<div>	
				<?php _e('Automatically delete imported job older than ') ?>
				<input type="text" class="option delete-limit-days" style="width: 50px" name="delete_days" value="<?php echo get_option('je_rss_delete_days', 30) ?>" maxlength="3">
				<?php _e('Day(s)'); _e("&nbsp;or", ET_DOMAIN); ?>
				<input type="button" id="delete_old_jobs" class="button" value="<?php _e("Delete Now", ET_DOMAIN); ?>">
			</div>
		</form>
	</div>
	
	<div class="title font-quicksand"><?php _e('Imported Jobs', ET_DOMAIN) ?></div>
	<div class="desc">
		<div id="ijobs" class="import-tb-container">
			<?php 
			$jobs = new WP_Query(array(
				'post_type' 		=> 'job',
				'meta_key' 			=> 'et_template_id',
				'meta_value' 		=> 'rss',
				'posts_per_page' 	=> 10,
				'paged' 			=> 1
			));
			?>
			<input type="hidden" name="paged" value="1">
			<table>
				<tbody>
					<tr class="heading">
						<th><input class="setall" type="checkbox" name=""></th>
						<th width="40%"><?php _e('Title') ?></th>
						<th width="30%"><?php _e('RSS Link') ?></th>
						<th width="15%"><?php _e('Creator') ?></th>
						<th width="15%"><?php _e('Date') ?></th>
					</tr>
					<?php
					foreach ($jobs->posts as $index => $job) {
						?>
						<tr>
							<td><input class="allow" type="checkbox" name="" value="<?php echo $job->ID ?>"></th>
							<td><a target="_blank" href="<?php echo get_permalink($job->ID) ?>" >
									<?php echo $job->post_title ?>
								</a>
							</td>
							<td><?php echo get_post_meta($job->ID, 'et_rss_url', true) ?></td>
							<td><?php echo get_post_meta($job->ID, 'et_rss_creator', true) ?></td>
							<td><?php echo $job->post_date ?></td>
						</tr>
						<?php
					}
					?>
				</tbody>
			</table>
		</div>
		<div class="rss-controls">
			<?php if ($jobs->max_num_pages > 1) { ?>
			<div class="paginate">
				<span>1</span>
				<?php for ($i = 2; $i <= $jobs->max_num_pages; $i++){ ?>
					<a href="#" class="pi"><?php echo $i ?></a>
				<?php } ?>
			</div>
			<?php } ?>
			<a href="#" id="delete_rss" class="icon" data-icon="#"><?php _e('Delete selected jobs', ET_DOMAIN) ?></a>
		</div>
	</div>
</div>