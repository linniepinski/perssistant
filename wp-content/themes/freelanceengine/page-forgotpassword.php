	<?php

/**

 * Template Name: Desktop forgotpassword page

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

                  <form id="forgot_form" class="auth-form forgot_form">

					<div class="form-group">

						<label for="forgot_user_email"><?php _e('Enter your email here', ET_DOMAIN) ?></label>

						<input type="text" class="form-control" id="user_email" name="user_email" />

					</div>
                        <?php if( function_exists( 'cptch_display_captcha' ) ) { echo "<input type='hidden' name='cntctfrm_contact_action' value='true' />"; echo cptch_display_captcha(); }?>

                    <div class="clearfix"></div>

					<button type="submit" class="btn-submit btn-sumary btn-sub-create">

						<?php _e('Send', ET_DOMAIN) ?>

					</button>

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