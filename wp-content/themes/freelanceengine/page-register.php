<?php 
/**
 * Template Name: Register Page Template
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

if($_GET['c_user_email']){
	wp_mail( $_GET['c_user_email'], 'E-mail confirmation', 'http://'.$_SERVER["HTTP_HOST"].'/sign-up/?email='.$_GET['c_user_email'] );	
}

get_header();

?>



<!-- Breadcrumb Blog -->
<?php /*
<div class="section-detail-wrapper breadcrumb-blog-page">

    <ol class="breadcrumb">

        <li><a href="<?php echo home_url() ?>" title="<?php echo get_bloginfo( 'name' ); ?>" ><?php _e("Home", 'page-register'); ?></a></li>

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
				<input type="hidden" value="<?php _e("Work", 'page-register'); ?>" class="work-text" name="worktext" />

				<input type="hidden" value="<?php _e("Hire", 'page-register'); ?>" class="hide-text" name="hidetext" />

                <form id="user_signup_form" class="auth-form signup_form">

                	<p class="user-type">

                		<?php _e("What are you looking for?", 'page-register') ?> 



                            <input type="checkbox" class="sign-up-switch" name="modal-check" id="modal-check"/>

                            <span class="user-role text work">

                                <?php _e("Work", 'page-register'); ?>

                            </span>

                	</p>

                	<input type="hidden" name="role" id="role" value="freelancer" />

					<div class="form-group">

						<label for="user_login"><?php _e('Username', 'page-register') ?></label>

						<input type="text" class="form-control" id="user_login" name="user_login" placeholder="<?php _e("Enter username", 'page-register') ?>">

					</div>

					<div class="form-group">

						<label for="register_user_email"><?php _e('Email address', 'page-register') ?></label>

						<input type="email" class="form-control" id="register_user_email" name="user_email" placeholder="<?php _e("Enter email", 'page-register') ?>">

					</div>

					<div class="form-group">

						<label for="register_user_pass"><?php _e('Password', 'page-register') ?></label>

						<input type="password" class="form-control" id="register_user_pass" name="user_pass" placeholder="<?php _e("Password", 'page-register') ?>">

					</div>
                    <div id="pswd_info" style="display: none">
                        <h4>Security level: <strong class="strong-level">danger</strong></h4>
<!--                        <ul>-->
<!--                            <li class="level"></li>-->
<!--                        </ul> -->
                        <h4>Password must meet the following requirements:</h4>
                        <ul>
                            <li id="letter" class="invalid">At least <strong>one letter</strong></li>
                            <li id="capital" class="invalid">At least <strong>one capital letter</strong></li>
                            <li id="number" class="invalid">At least <strong>one number</strong></li>
                            <li id="length" class="invalid">Be at least <strong>8 characters</strong></li>
                        </ul>
                    </div>

					<div class="form-group">

						<label for="repeat_pass"><?php _e('Retype Password', 'page-register') ?></label>

						<input type="password" class="form-control" id="repeat_pass" name="repeat_pass" placeholder="<?php _e("Retype password", 'page-register') ?>">

					</div>




					<div class="clearfix"></div>

					<?php if(get_theme_mod( 'termofuse_checkbox', false )){ ?>

					<div class="form-group policy-agreement">

						<input name="agreement" id="agreement" type="checkbox" />

						<?php printf(__('I agree with the <a href="%s">Term of Use and Privacy policy</a>', 'page-register'), et_get_page_link('tos') ); ?>

					</div>	

                    <div class="clearfix"></div>	

                    <?php } ?>

					<button type="submit" class="btn-submit btn-sumary btn-sub-create">

						<?php _e('Sign up', 'page-register') ?>

					</button>

					<?php if(!get_theme_mod( 'termofuse_checkbox', false )){ ?>

					<p class="text-term">

						<?php

		                /**

		                 * tos agreement

		                */

		                $tos = et_get_page_link('tos', array() ,false);

		                if($tos) { ?>

		                    <?php printf(__('By creating an account, you agree to our <a href="%s">Term of Use and Privacy policy</a>', 'page-register'), et_get_page_link('tos') ); ?>

		                <?php } ?>

					</p>

					<?php } 

//		                if( function_exists('ae_render_social_button')){
//
//		                    $before_string = __("You can also sign in by:", 'page-register');
//
//		                    ae_render_social_button( array(), array(), $before_string );
//
//		                }
//
					?><div class="socials-head"><?php echo __("You can also sign in by:", 'page-register'); ?></div><?php

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