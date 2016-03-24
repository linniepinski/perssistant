	<?php

/**

 * Template Name: Desktop login page

*/

global $user_ID;

// user already login redirect to home page

if($user_ID) {

    // isset redirect url

    if(isset($_REQUEST['redirect'])) {

        wp_redirect($_REQUEST['redirect']);

        exit;

    }

    wp_redirect(home_url());

    exit;

}



get_header();



?>



<!-- Breadcrumb Blog -->
<?php /*
<div class="section-detail-wrapper breadcrumb-blog-page">

    <ol class="breadcrumb">

        <li><a href="<?php echo home_url() ?>" title="<?php echo get_bloginfo( 'name' ); ?>" ><?php _e("Home", ET_DOMAIN); ?></a></li>

        <li><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></li>

    </ol>

</div>
*/?>
<!-- Breadcrumb Blog / End -->



<section class="blog-header-container">

	<div class="container">

		<!-- blog header -->

		<div class="row">

		    <div class="col-md-12 blog-classic-top">

		        <h2><?php the_title(); ?></h2>

		    </div>

		</div>      

		<!--// blog header  -->	

	</div>

</section>



<div class="container page-container">

	<!-- block control  -->

	<div class="row block-posts block-page">

		<div class="col-md-12 col-sm-12 col-xs-12 posts-container" id="left_content">

            <div class="blog-content">

                  <form id="user_signin_form" class="auth-form signin_form">

					<div class="form-group">

						<label for="login_user_login"><?php _e('Your User Name or Email', 'login-page') ?></label>

						<input type="text" class="form-control" id="login_user_login" name="user_login" placeholder="<?php _e('Enter username', 'login-page') ?>">

					</div>

					<div class="form-group">

						<label for="login_user_pass"><?php _e('Your Password', 'login-page') ?></label>

						<input type="password" class="form-control" id="login_user_pass" name="user_pass" placeholder="<?php _e('Password', 'login-page') ?>">

					</div>		

                    <div class="clearfix"></div>
					  <?php if( function_exists( 'cptch_display_captcha' ) ) { echo "<input type='hidden' name='cntctfrm_contact_action' value='true' />"; echo cptch_display_captcha(); }?>
					  <div class="clearfix"></div>

					<button type="submit" class="btn-submit btn-sumary btn-sub-create">

						<?php _e('Sign in', 'login-page') ?>

					</button>

                    <a class="show-forgot-form" href="<?php echo site_url() . ae_current_lang() . '/forgotpassword'?>"><?php _e("Forgot Password?", 'login-page') ?></a> |<a class="show-forgot-form" href="<?php echo site_url() . ae_current_lang() . '/sign-up'?>"><?php _e("Register", 'login-page') ?></a>
<?php

//			                if( function_exists('ae_render_social_button')){
//
//			                    $before_string = __("You can also sign in by:", ET_DOMAIN);
//
//			                    ae_render_social_button( array(), array(), $before_string );
//
//			                }

?><div class="socials-head"><?php echo __("You can also sign in by:", 'login-page'); ?></div><?php

do_action( 'wordpress_social_login' );
			            ?>
				</form>	           

				<div class="clearfix"></div>

            </div><!-- end page content -->

		</div><!-- LEFT CONTENT -->
		<?php /*
		<div class="col-md-3 col-sm-12 col-xs-12 page-sidebar" id="right_content">

			<?php get_sidebar('page'); ?>
			

		</div><!-- RIGHT CONTENT -->
		<?php */?>
	</div>

	<!--// block control  -->

</div>

<?php

	get_footer();

?>