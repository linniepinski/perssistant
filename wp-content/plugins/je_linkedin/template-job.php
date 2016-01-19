<?php 
global $post, $job;

$is_linkedin_job  = et_get_post_field($job['id'], 'linkedin_url') != '' ? true : false;
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
		<a href="<?php echo  $company['post_url']; ?>">
			<img src="<?php echo $job['linkedin_logo'] ?>">
		</a>
	</div>

	<div class="content">
		<a class="title-link" href="<?php the_permalink() ?>" title="<?php printf(__('View %s job details', ET_DOMAIN), get_the_title())?>">
			<h6 class="title"><?php the_title() ?></h6>
		</a>
		<div class="desc f-left-all">
			<div class="cat company_name">
				<?php echo $job['linkedin_company'] ?>
			</div>
			<?php if ($job_type != '') { ?>
			<div class="job-type <?php echo 'color-' . $job_type['color'] ?>">
				<span class="flag"></span>
				<?php if ( !$is_linkedin_job ){ ?>
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
			<?php if (get_option('et_linkedin_display_label')){ ?>
			<span id="linkedin_at" class="font-links-style"><a target="_blank" href="http://www.linkedin.com/"><?php _e("Job", ET_DOMAIN); ?></a> <?php _e("by", ET_DOMAIN); ?> <a
				href="http://www.linkedin.com/" target="_blank" title="LinkedIn Job"><img
				src="<?php echo plugin_dir_url(__FILE__) ?>/linkedin.png" style="border: 0;
				vertical-align: middle;" alt="LinkedIn job"></a>
			</span>
			<?php } ?>
		</div>
		
	</div>
</li>