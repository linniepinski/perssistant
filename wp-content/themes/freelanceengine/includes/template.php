<?php
/**
 * count user posts
 * @since 1.1			
 * @author Thái NT
 */
function fre_count_user_posts( $userid, $post_type = 'post' ) {
	global $wpdb;

	$where = get_posts_by_author_sql( $post_type, true, $userid );

	$count = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->posts $where" );

  	return apply_filters( 'get_usernumposts', $count, $userid );
}
/**
 * render latest page in single page
 * @since 1.0			
 * @author Thái NT
 */
function fre_latest_pages($id){
	$query = new WP_Query(array(
		'post_type'    => 'page',
		'showposts'    => 3,
		'post__not_in' => array($id)
		));
	if($query->have_posts()){
		echo '<ul>';
		while ($query->have_posts()) {
			$query->the_post();
			?>
			<li>
				<a href="<?php the_permalink() ?>">
					<?php the_title();?>
				</a>
			</li>
			<?php
		}
		echo '</ul>';
	}
	wp_reset_query();
}
if(!function_exists('fre_profile_button')) {
	/**
	 * render profile button associate with user login 
	 * @since 1.0			
	 * @author Dakachi
	 */
	function fre_profile_button(){
		global $user_ID;
		/* user have not logged in */
		if(!$user_ID) { ?>
			<a href="<?php echo et_get_page_link('auth'); ?>" class="btn btn-sumary btn-post-profile">
		        <i class="fa fa-plus-circle"></i><?php _e("Create a Profile", 'template-backend'); ?>
		    </a>
		<?php
			return ;
		}
		// current user is a freelancer
		if( fre_share_role() || ae_user_role() == FREELANCER ) { 
		?>
			<a href="<?php echo et_get_page_link( array('page_type' => 'profile', 'post_title' => __("Profile", 'template-backend' )) ); ?>" class="btn btn-sumary btn-post-profile">
	            <i class="fa fa-plus-circle"></i><?php _e("Review your Profile", 'template-backend'); ?>
	        </a>
		<?php
			return '';
		}

		// current user is an employer
		?>
			<a href="<?php echo get_post_type_archive_link(PROFILE) ?>" class="btn btn-sumary btn-post-profile">
	            <i class="fa fa-plus-circle"></i><?php _e("Find a Freelancer", 'template-backend'); ?>
	        </a>
		<?php

	}
}


if(!function_exists('fre_project_button')) {
	/**
	 * render project button associate with user login 
	 * @since 1.0			
	 * @author Dakachi
	 */
	function fre_project_button($echo = false){
		global $user_ID;
		/* user have not logged in */
		if(!$user_ID) { ?>
			<a href="<?php echo et_get_page_link('submit-project'); ?>" class="btn btn-sumary btn-post-project">
		        <i class="fa fa-plus-circle"></i><?php _e("Post a Project", 'template-backend'); ?>
		    </a>
		<?php
			return ;
		}
		// current user is a freelancer
		if( ae_user_role() != FREELANCER ) { 
		?>
			<a href="<?php echo et_get_page_link( array('page_type' => 'submit-project', 'post_title' => __("Post a Project", 'template-backend' )) ); ?>" class="btn btn-sumary btn-post-project">
				<i class="fa fa-plus-circle"></i><?php _e("Post a Project", 'template-backend'); ?>
			</a>
		<?php
			return '';
		}

		// current user is an employer
		?>
			<a href="<?php echo get_post_type_archive_link(PROJECT) ?>" class="btn btn-sumary btn-post-project"><i class="fa fa-plus-circle"></i>
	            <?php _e("Find a Project", 'template-backend'); ?>
	        </a>
		<?php
	}
		
}

function ae_edit_post_button($post)
{
	if ($post->post_status == 'pending') { ?>
		<a title="<?php _e("Edit", 'template-backend'); ?>" data-action="edit" data-target="#" class="action edit"
		   href="#edit_place"><i class="fa fa-pencil"></i></a>
		<a title="<?php printf(__("%d views", 'template-backend'), $post->post_views) ?>" class="post-views" href="#"><i
				class="fa fa-eye"></i> <label class="eye-count"><?php echo $post->post_views; ?></label></a>
	<?php } ?>

	<?php if ($post->post_status == 'publish') { ?>
	<a title="<?php _e("Edit", 'template-backend'); ?>" data-action="edit" data-target="#" class="action edit"
	   href="#edit_place"><i class="fa fa-pencil"></i></a>
	<a title="<?php printf(__("%d views", 'template-backend'), $post->post_views) ?>" class="post-views" href="#"><i
			class="fa fa-eye"></i> <label class="eye-count"><?php echo $post->post_views; ?></label></a>
	<a data-action="archive" class="action archive" href="#"><i class="fa fa-trash-o"></i></a>
<?php } ?>
	<?php if ($post->post_status == 'archive') { ?>
	<a title="<?php _e("Edit", 'template-backend'); ?>" data-target="#" class=""
	   href="<?php echo et_get_page_link('submit-project', array('id' => $post->ID)) ?>">
		<i class="fa fa-pencil"></i>
	</a>
	<a title="<?php _e("Delete", 'template-backend'); ?>" data-action="delete" class="action delete" href="#"> <i
			class="fa fa-times"></i>
	</a>
<?php } ?>
	<?php if ($post->post_status == 'complete') { ?>
	<a data-action="archive" class="action archive" href="#"><i class="fa fa-trash-o"></i></a>
<?php }
	do_action('ae_edit_post_button', $post);
}

function ae_js_edit_post_button () {
?>
	<# if(post_status == 'pending'){ #>
        <a data-action="edit" data-target="#" class="action edit" href="#edit_place"><i class="fa fa-pencil"></i></a>
        <a title="{{= post_views }} <?php _e("views", 'template-backend'); ?>" class="post-views" href="#"><i class="fa fa-eye"></i> <label class="eye-count">{{= post_views }}</label></a>
    <# } #>

    <# if(post_status == 'publish'){ #>
        <a data-action="edit" data-target="#" class="action edit" href="#edit_place"><i class="fa fa-pencil"></i></a>
        <a title="{{= post_views }} <?php _e("views", 'template-backend'); ?>" class="post-views" href="#"><i class="fa fa-eye"></i> <label class="eye-count">{{= post_views }}</label></a>
        <a data-action="archive" class="action archive" href="#"><i class="fa fa-trash-o"></i></a>
    <# } #>
    <# if(post_status == 'draft'){ #>
        <a title="<?php _e("Edit", 'template-backend'); ?>" data-target="#" class="" 
        	href="<?php echo et_get_page_link('submit-project') ?>?id={{= ID }}">
            <i class="fa fa-pencil"></i>
        </a>
        <a data-action="delete" class="action archive" href="#">
            <i class="fa fa-times"></i>
        </a>
    <# } #>
    <# if(post_status == 'archive'){ #>
        <a title="<?php _e("Renew", 'template-backend'); ?>" data-target="#" class="" 
        	href="<?php echo et_get_page_link('submit-project') ?>?id={{= ID }}">
            <i class="fa fa-refresh"></i>
        </a>
        <a title="<?php _e("Delete", 'template-backend'); ?>" data-action="delete" class="action delete" href="#">
            <i class="fa fa-times"></i>
        </a>
    <# } #>
    <?php
}
function render_security_check_pass_info(){
	?>
	<div id="pswd_info" style="display: none">
      <h4><?php _e("Security level:", 'template-backend'); ?><strong class="strong-level"><?php _e("danger", 'template-backend'); ?></strong></h4>
                        <h4><?php _e("Password must meet the following requirements:", 'template-backend'); ?></h4>
                        <ul>
                            <li id="letter" class="invalid"><?php _e("At least", 'template-backend'); ?> <strong><?php _e("one letter", 'template-backend'); ?></strong></li>
                            <li id="capital" class="invalid"><?php _e("At least", 'template-backend'); ?> <strong><?php _e("one capital letter", 'template-backend'); ?></strong></li>
                            <li id="number" class="invalid"><?php _e("At least", 'template-backend'); ?> <strong><?php _e("one number", 'template-backend'); ?></strong></li>
                            <li id="length" class="invalid"><?php _e("Be at least", 'template-backend'); ?> <strong><?php _e("8 characters", 'template-backend'); ?></strong></li>
                        </ul>
    </div>
	<?php
}
