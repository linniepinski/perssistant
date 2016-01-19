<?php 
global $post, $job;

$is_indeed_job  = et_get_post_field($job['id'], 'indeed_url') != '' ? true : false;
$job_cat 	= isset($job['categories'][0]) ? $job['categories'][0] : '';
$job_type 	= isset($job['job_types'][0]) ? $job['job_types'][0] : '';
$company		= et_create_companies_response( $job['author_id'] );
$company_logo	= $company['user_logo'];

// add this company data to the array to pass to js
if(!isset($arrAuthors[$company['id']])){
	$arrAuthors[$company['id']]	= array(
		'display_name'	=> $company['display_name'],
		'user_url'		=> $company['user_url'],
		'user_logo'		=> $company_logo
	);
}
?>
<li class="job-item">
	<div class="thumb">
		<a href="<?php echo $job['indeed_ref_url'] ?>">
			<img src="<?php echo $job['indeed_logo'] ?>">
		</a>
	</div>

	<div class="content">
		<a class="title-link" target="_blank" href="<?php echo $job['indeed_ref_url'] ?>" title="<?php printf(__('View %s job details', ET_DOMAIN), get_the_title())?>">
			<h6 class="title"><?php the_title() ?></h6>
		</a>
		<div class="desc f-left-all">
			<div class="cat company_name">
				<?php echo $job['indeed_company'] ?>
			</div>
			<?php if ($job_type != '') { ?>
			<div class="job-type <?php echo 'color-' . $job_type['color'] ?>">
				<span class="flag"></span>
				<?php if ( !$is_indeed_job ){ ?>
					<a href="<?php echo $job_type['url']; ?>" title="<?php printf(__('View all jobs posted in %s', ET_DOMAIN), $job_type['name']);?>">
						<?php echo $job_type['name'] ?>
					</a>
				<?php } else { ?>
					<?php echo $job_type['name'] ?>
				<?php } ?>
			</div>
			<?php } ?>
			<?php if ($job['location'] != '') { ?>
				<div><span class="icon" data-icon="@"></span><span class="job-location"><?php echo $job['location'] ?></span></div>
			<?php } ?>
		</div>

		<div class="tech f-right actions">
			<?php
				$fea 		 =	'';
				$set_feature =  __('Set Featured', ET_DOMAIN) ;
				if( $job['featured']) {
						$fea = 'fea';
						$set_feature = __('Remove Featured',ET_DOMAIN);
				?>
				<span class="feature font-heading"><?php _e('featured', ET_DOMAIN) ?></span>
			<?php } ?>
			<?php if (get_option('et_indeed_display_label')){ ?>
			<span id=indeed_at><a href="http://www.indeed.com/">jobs</a> by <a
				href="http://www.indeed.com/" title="Job Search"><img
				src="http://www.indeed.com/p/jobsearch.gif" style="border: 0;
				vertical-align: middle;" alt="Indeed job search"></a>
			</span>
			<?php } ?>
		</div>

	</div>
</li>