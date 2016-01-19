<?php 
global $post, $job;

$job_cat 		= isset($job['categories'][0]) ? $job['categories'][0] : '';
$job_type 		= isset($job['job_types'][0]) ? $job['job_types'][0] : '';
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
		<a href="<?php echo $job['rss_url'] ?>">
			<img src="<?php echo $job['rss_logo'] ?>">
		</a>
	</div>

	<div class="content">
		<a class="title-link" href="<?php echo $job['permalink'] ?>" title="<?php printf(__('View %s job details', ET_DOMAIN), get_the_title())?>">
			<h6 class="title"><?php the_title() ?></h6>
		</a>
		<div class="desc f-left-all">
			<div class="cat company_name">
				<a data="<?php echo $company['ID'];?>" href="<?php echo $company['post_url'];?>" title="<?php printf(__('View posted jobs by %s', ET_DOMAIN), $company['display_name']) ?>">
					<?php echo $company['display_name'] ?>
				</a>
			</div>
			<?php if ($job_type != '') { ?>
			<div class="job-type <?php echo 'color-' . $job_type['color'] ?>">
				<span class="flag"></span>
				<a href="<?php echo $job_type['url'] ?>"><?php echo $job_type['name'] ?></a>
			</div>
			<?php } ?>
			<?php if ($job['location'] != '') { ?>
				<div><span class="icon" data-icon="@"></span><span class="job-location"><?php echo $job['location'] ?></span></div>
			<?php } ?>
		</div>
		<?php /*
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
			<span id="rss_at" class="font-links-style"><a target="_blank" href="<?php echo $job['permalink'] ?>">Jobs</a> by <a
				href="<?php echo $job['permalink'] ?>" target="_blank" title="Rss Job">Rss</a>
			</span>
		</div>
		 */ ?>
	</div>
</li>