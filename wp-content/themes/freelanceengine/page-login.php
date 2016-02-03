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

    wp_redirect('/profile');

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

                  <form role="form" id="user_signin_form" class="auth-form signin_form">

					<div class="form-group">

						<label for="login_user_login"><?php _e('Your User Name or Email', ET_DOMAIN) ?></label>

						<input type="text" class="form-control" id="login_user_login" name="user_login" placeholder="<?php _e('Enter username', ET_DOMAIN) ?>">

					</div>

					<div class="form-group">

						<label for="login_user_pass"><?php _e('Your Password', ET_DOMAIN) ?></label>

						<input type="password" class="form-control" id="login_user_pass" name="user_pass" placeholder="<?php _e('Password', ET_DOMAIN) ?>">

					</div>		

                    <div class="clearfix"></div>

					<button type="submit" class="btn-submit btn-sumary btn-sub-create">

						<?php _e('Sign in', ET_DOMAIN) ?>

					</button>

                    <a class="show-forgot-form" href="<?php echo site_url() . '/forgotpassword'?>"><?php _e("Forgot Password?", ET_DOMAIN) ?></a> |<a class="show-forgot-form" href="<?php echo site_url() . '/sign-up'?>"><?php _e("Register", ET_DOMAIN) ?></a>
<?php

			                if( function_exists('ae_render_social_button')){

			                    $before_string = __("You can also sign in by:", ET_DOMAIN);

			                    ae_render_social_button( array(), array(), $before_string ); 

			                }

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