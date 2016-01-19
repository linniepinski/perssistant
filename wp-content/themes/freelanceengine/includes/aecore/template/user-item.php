<?php 
	global $user, $wp_roles;

	$user_role	=	$user->roles;
	$user_role	=	array_pop($user_role);
	$role_names	=	$wp_roles->role_names;
?>
<li class="et-member" data-id="<?php echo $user->ID; ?>">
	<div class="et-mem-container">
		<div class="et-mem-avatar">
			<?php echo get_avatar( $user->ID ); ?>
		</div>
		<!-- action change user role -->
		<div class="et-act">
			
			<select name="role" class="role-change regular-input" >
				<?php foreach ( $role_names as $role_name => $role_label ) {
					if($role_name == $user_role ) 
						echo '<option value="'. $role_name .'" selected="selected">'. $role_label .'</option>';
					else 
						echo '<option value="'. $role_name .'" >'. $role_label .'</option>';
				} ?>						
			</select>
			<!-- Manual Confirm User -->
			<?php if( $user->register_status == "unconfirm" && current_user_can( 'administrator' ) ){ ?>
			<a class="action et-act-confirm" data-act="confirm" href="javascript:void(0)" title="Confirm this user">
				<span class="icon" data-icon="3"></span>
			</a>
			<?php } ?>
			<!-- Manual Confirm User -->
			<?php do_action( 'ae_admin_user_action', $user ); ?>
		</div>

		<div class="et-mem-detail">
			<?php do_action( 'ae_admin_before_user_details', $user ); ?>
			<div class="et-mem-top">
				<span class="name"><?php echo $user->display_name; ?></span>
				<span class="thread icon" data-icon="w" title="<?php _e("Posts", ET_DOMAIN); ?>"><?php echo $user->post_count; ?></span>
				<span class="comment icon" data-icon="q" title="<?php _e("Comments", ET_DOMAIN); ?>" ><?php echo $user->comment_count; ?></span>
			</div>

			<div class="et-mem-bottom">
				<span class="date"><?php printf(__("Join on %s", ET_DOMAIN), (string)date(get_option('date_format'), strtotime($user->user_registered) )) ; ?></span>
				<?php 
				if($user->location) { 
					echo '<span class="loc icon" data-icon="@">'. $user->location. '</span>';
				}else {
					echo '<span class="loc icon" data-icon="G"></span>';
				} ?>
			</div>
			<?php do_action( 'ae_admin_after_user_details', $user ); ?>
		</div>
	</div>
</li>