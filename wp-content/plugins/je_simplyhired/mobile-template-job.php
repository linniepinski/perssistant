<?php 
global $job;
$colours  		= et_get_job_type_colors();

?>
<li class="list-item">
	<a href="<?php echo $job['simplyhired_url'] ?>" target="_blank" data-transition="slide">
		<h2 class="list-title">
			<?php the_title(); ?>
		</h2>
		<p class="list-subtitle">
			<span class="list-info job-loc"><?php echo $job['simplyhired_company']; ?></span>
			<?php if (count($job['job_types']) > 0 ) { ?>
				<span class="list-info job-title color-<?php echo $colours[$job['job_types'][0]['term_id']]; ?>"><span class="icon-label flag"></span><?php echo $job['job_types'][0]['name']; ?></span>
			<?php } ?>	
			<?php if ($job['location'] != '') { ?>
				<span class="list-info job-loc icon" data-icon="@"><?php echo $job['location']; ?></span>
			<?php } ?>
		</p>
	</a>
	<div class="mblDomButtonGrayArrow arrow">
		<div></div>
	</div>
</li>