<?php
if(!isset($_REQUEST['jobroll_request'])) {
	get_header ();
?>
	<div class="wrapper content-container" id="jobroll_info">
		<div class="heading">
			<div class="main-center">
				<h1 class="title"><?php the_title();?></h1>
			</div>
		</div>

		<div class="wrapper main-jobroll">

				<div class="main-center clearfix">
					<?php the_content(); ?>
					<form id="jobroll_form">
					<div class="job-left f-left">

						<div class="main-title"><?php _e("CONTENT",ET_DOMAIN);?></div>
						<div class="item-jobroll clearfix">
							<div class="item-job">
								<label><?php _e("Job Category", ET_DOMAIN); ?></label>
								<div class="input-job select-style btn-background border-radius">
									<select class="front-change" title="<?php _e("All categories", ET_DOMAIN);?>" name="categories" id="categories" style="z-index: 10; opacity: 0;">
										<option value="" ><?php _e("All categories", ET_DOMAIN);?></option>
										<?php 
										et_job_categories_option_list ( $parent =	0 );
										?>
									</select>
								</div>
							</div>
							<div class="item-job">
								<label><?php _e("Job Type", ET_DOMAIN); ?></label>
								<div class="input-job select-style btn-background border-radius">
									<select name="job_types" class="front-change" id="job_types"  style="z-index: 10; opacity: 0;" title="<?php _e("All job types", ET_DOMAIN); ?>">
										<option value=""><?php _e("All job types", ET_DOMAIN); ?></option>
										<?php $types = et_get_job_types();
										if ( !empty($types) ){
											foreach ($types as $type) {
												echo '<option value="' . $type->slug . '">' . $type->name . '</option>';
											}
										}
										?>
									</select>
								</div>
							</div>
						</div>

						<!-- display -->
						<div class="main-title"><?php _e("DISPLAY",ET_DOMAIN);?></div>
						<div class="item-jobroll clearfix">
							<div class="item-job">
								<label><?php _e("Number of jobs", ET_DOMAIN); ?></label>
								<div class="input-job">
									<input type="text" class="bg-default-input"  id="number" name="number" value="5" />
								</div>
							</div>
							<div class="item-job">
								<label><?php _e("Width", ET_DOMAIN); ?></label>
								<div class="input-job">
									<input type="text" class="bg-default-input" class="width" id="width" name="width" value="300"/>
								</div>
							</div>
						</div>
						<div class="item-jobroll clearfix">
							<div class="item-job">
								<label><?php _e("Background Color", ET_DOMAIN); ?></label>
								<div class="input-job">
									<input type="text" class="bg-default-input"  id="color" name="color" value="d9d9d9" />
								</div>
							</div>
							<div class="item-job">
								<label><?php _e("Title", ET_DOMAIN); ?></label>
								<div class="input-job">
									<input type="text" class="bg-default-input"  id="title" name="title" value="<?php printf(__("Jobs from %s",ET_DOMAIN), get_option('blogname') ); ?>"/>
								</div>
							</div>
						</div>
						<?php
						$id 	= JE_Jobroll::get_page_jobroll();
						$url 	= get_permalink($id);
						$url 	= add_query_arg(array('jobroll_request' => 1,'number' =>5,'width'=>250,'color'=>'d9d9d9'),$url );

						?>

						<!-- view code -->
						<div class="main-title"><?php _e("CODE",ET_DOMAIN);?></div>
						<div class="code-view preview" id="preview">
							<label><?php _e("Copy this code and paste into your website", ET_DOMAIN); ?></label>
							<textarea class="code-content bg-grey-widget"><?php echo htmlentities('<iframe id="je_jobroll" style="border:0; overflow:hidden;" src="'.$url.'" frameborder="0" allowtransparency="true" marginheight="0" marginwidth="0"></iframe>') ?></textarea>
						</div>

					</div>
					</form>
					<!-- right content -->

					<div class="quickview-right f-right">
						<div class="main-title"><?php _e("QUICK VIEW",ET_DOMAIN);?></div>
						<iframe id="frame_preview" style="border:0; overflow:hidden;" src="<?php echo $url;?>" frameborder="0" height="400px" allowtransparency="true" marginheight="0" marginwidth="0"></iframe>
					</div>
			</div>
		</div>
	</div>
	<?php
	get_footer();

} else {
	extract($_REQUEST);
	$opt 		=	new ET_GeneralOptions ();
	$customize	=	$opt->get_customization ();

	$args	=	array(
			'post_type'			=> 'job',
			'post_status'		=> 'publish',
			'posts_per_page'	=> ($number) ? $number : 5,
			//'meta_key' 			=> 'et_location_lat'
		);

	if(isset($job_types) && $job_types) {
		$args['tax_query'][]	=array(
									'taxonomy' => 'job_type',
									'field'    => 'slug',
									'terms'    => $job_types,
								);
	}

	if(isset($categories) && $categories) {
		$args['tax_query'][] =  array(
									'taxonomy' => 'job_category',
									'field'    => 'id',
									'terms'    => $categories,
								);
	}
	$args['tax_query']['relation'] = 'AND';
	echo '<pre>';
	//var_dump($args);
	echo '</pre>';
	$query	=	new WP_Query( $args );
	if($query->have_posts()) {
	?>
	<html>
	<head>
	<style type="text/css">
		body {
			background-color: transparent;
			overflow: hidden;
		}

		 .demo-view {
		 	font-size: 12px;
			width: <?php echo $width ?>px;
			background-color: #<?php echo $color ?>;
			box-shadow: inset 0 1px 6px -2px #d9d9d9;
		}

		.demo-view .main-title {
			padding: 20px 20px 10px;
			margin-bottom: 5px;
			border-bottom: 1px solid #e6e6e6;
			font-family: <?php echo $customize['font-heading'] ?>;
			font-size: <?php echo $customize['font-heading-size'] ?>;
			white-space: nowrap;
			overflow: hidden;
			text-overflow: ellipsis;
		}

		.demo-view .demo-item {
			padding: 10px 20px;
			font-family: <?php echo $customize['font-text'] ?>;
			font-size: <?php echo $customize['font-text-size'] ?>;

		}

		.demo-view .demo-item .name {
			font-weight: bold;
			font-size: <?php echo $customize['font-action-size'] ?>;
			font-family: <?php echo $customize['font-action'] ?>;
			white-space: nowrap;

			overflow: hidden;
			text-overflow: ellipsis;
		}
		.demo-view .demo-item .name a{
			color: #333;
			font-size: 13px;
			text-decoration: none;
		}

		.demo-view .demo-item span {
			white-space: nowrap;
			overflow: hidden;
			text-overflow: ellipsis;
			display: block;
		}
	</style>
	</head>
	<body>
		<div class="demo-view bg-grey-widget">
			<div class="main-title"><?php echo (isset($title) && $title != '') ? urldecode($title) : sprintf(__("Jobs from %s",ET_DOMAIN), get_option('blogname') ); ?></div>
			<?php
			while ($query->have_posts())  { $query->the_post();
				global $post;

				$location	=	get_post_meta( $post->ID, 'et_location', true );
				$user	= get_userdata( (int)$post->post_author );
			?>
			<div class="demo-item">
				<div class="name color-action"><a href="<?php the_permalink() ?>" target="_blank" ><?php the_title() ?> </a></div>
				<span><?php echo $user->display_name; ?> <?php if( $location  != '') echo ' - '.$location ; ?></span>
			</div>
			<?php } ?>
		</div>
	<?php
	} else {
	?>
	<html>
	<head>
	<style type="text/css">
		body {
			background-color: transparent;
			overflow: hidden;
		}
	</style>
	</head>
	<body>
		<div class="demo-view bg-grey-widget">
			<div class="main-title"><?php printf(__("Jobs from %s",ET_DOMAIN), get_option('blogname') ); ?></div>
			<?php _e("No job currently matches your selected option.", ET_DOMAIN); ?>
		</div>
	</body>
	</html>
	<?php
	}
	wp_reset_query();
	?>
	</body>
	</html>
	<?php
}

