<?php
global $et_global, $post, $user_ID;
$imgUrl	=	$et_global['imgUrl'];
$jsUrl	=	$et_global['jsUrl'];
$job      = $post;
 
if(have_posts()) { the_post ();
	// get all job categories
	$job_cats  = et_get_the_job_category ($job->ID);
	// get all job types
	$job_types	=	et_get_the_job_type($job->ID);
	
	$company		= et_create_companies_response( $post->post_author );
	$company_logo	= $company['user_logo'];
	$job_location	= et_get_post_field($post->ID, 'location');

	//echo get_post_meta( $job->ID, 'et_rss_job_category', true );

get_header ();

?>
<style type="text/css">
	/*** update single rss ***/
.btn-apply-rss {
	display: block;
	max-width: 250px;
	float: left;
}

</style>
<div class="wrapper content-container">

		<div class="heading">
			<div class="main-center">
				<h1 data="<?php echo $job->ID;?>" class="title job-title" id="job_title"><?php the_title()?>
					 
					<span class="vcount">(<?php echo et_post_views($job->ID); ?>)</span>
					
				</h1>
			</div>
		</div>
	
		<div class="heading-info clearfix mapoff">
			<div class="main-center">
				<div class="info f-left f-left-all">
					<div class="company job-info">
						<?php
							if (!empty($company_logo)){
								?>
								<div class="thumb_logo">
									<a id="job_author_thumb" class="thumb job_author_link" href="<?php echo get_author_posts_url($company['ID'])?>" 
										title="<?php printf(__('View posted jobs by %s', ET_DOMAIN), $company['display_name']) ?>">
										<img src="<?php echo $company_logo['thumbnail'][0]; ?>" id="company_logo_thumb" data="<?php echo (isset($company_logo['attach_id'])) ? $company_logo['attach_id'] : '';?>" />
									</a>
								</div>
								<?php
							}
						?>
						<!-- Job author, type, location, posted date -->
						<div>
							<a  href="<?php echo get_author_posts_url($company['ID'])?>" data="<?php echo $company['ID'];?>"
								title="<?php printf(__('View posted jobs by %s', ET_DOMAIN), $company['display_name']) ?>" class="name job_author_link" id="job_author_name">
							  <?php echo $company['display_name']?>
							</a>
						</div>
						<div id="job_type" class="job-type">
							<?php if( !empty($job_types) ) {
								foreach($job_types as $job_type){
								?>
								<input class="job-type-slug" type="hidden" value="<?php echo $job_type->slug; ?>"/>
								<a class="<?php echo 'color-' . $job_type->color; ?>" href="<?php echo et_get_job_type_link ($job_type) ?>" title="<?php printf(__('View posted jobs in %s ', ET_DOMAIN), $job_type->name) ?>">
									<span class="flag"></span>
									<?php echo $job_type->name; ?>
								</a>
								<?php 
								}
							}?>
						</div>
						<?php if($job_location != '') { ?>
						<span class="icon location" data-icon="@"></span>
						<div title="" class="job-location" id="job_location">								
							<?php echo $job_location; ?>
						</div>
						<?php } ?>
						<span class="icon date" data-icon="\"></span>
						<div class="date">							
							<?php the_date ()?>
						</div><div class="date"><?php the_date ()?></div>
					</div>
				</div>
				<!-- social share -->
				<?php get_template_part( 'template/single' , 'social' ); ?>
				<!-- end social share -->
				<div class="clear"></div>
			</div>
		</div>

		<div class="main-center padding-top30">
	

			<div class="main-column">
				
				<div class="job-detail tinymce-style">
					<div class="description" id="job_description">
					<?php 
						$html 	=	new HtmlFixer();
						echo $html->getFixedHtml(get_the_content());
					?>
					</div>
				</div>
				
				<div class="form_container">
					<?php if( $job->post_status == 'publish' || $user_ID == $job->post_author ||
								current_user_can('edit_others_posts') ) { ?>
					<div class="bg-job-frame job-apply submit-apply" id="job_action">
						<a rel="nofollow" target="_blank" title="<?php _e("APPLY FOR THIS JOB",ET_DOMAIN); ?>" class="bg-btn-action border-radius btn-default btn-apply btn-apply-rss applyJob" href="<?php echo get_post_meta($job->ID,  'et_rss_url', true); ?>" >
							<?php _e("APPLY FOR THIS JOB",ET_DOMAIN); ?>
							<span class="icon" data-icon="R"></span>
						</a>  
					</div>
					<?php } ?>
					
				</div>
			</div>
			<?php get_sidebar( ); ?>
			<div class="clearfix"></div>			
		</div>
<?php } ?>
</div>

<?php 
get_footer();