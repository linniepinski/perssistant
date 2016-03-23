<?php
/**
 * Template Name: Page Post Project
*/
global $user_ID;

// user already login redirect to home page

if(!$user_ID) {

    wp_redirect(home_url().'/login');

    exit;
}

get_header();
?>

<!-- Breadcrumb Blog -->
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
<!-- Breadcrumb Blog / End -->

<!-- Page Post Place -->
<section id="blog-page">
	<div class="container">
    	<div class="row">
            
        	<!-- Column left -->
        	<div class="col-md-9 col-sm-12 col-xs-12">
            	<div class="post-place-warpper" id="post-place">
                	
                    <?php 
                    // check disable payment plan or not
                    $disable_plan = ae_get_option('disable_plan', false);
                    if(!$disable_plan) {
                        // template/post-place-step1.php
                        get_template_part( 'template/post-project', 'step1' );    
                    }                    
                    
                    if(!$user_ID) {
                        // template/post-place-step2.php
                        get_template_part( 'template/post-project', 'step2' );
                    }
                    
                    // template/post-place-step3.php
                    get_template_part( 'template/post-project', 'step3' );


                    if(!$disable_plan) {
                        // template/post-place-step4.php
                        get_template_part( 'template/post-project', 'step4' );
                    }    

                    ?>
                </div>
                
                <?php
                /**
                 * tos agreement
                */
                $tos = et_get_page_link('tos', array() ,false);
                if($tos) { ?>
                    <div class="term-of-use">                           
                    <?php 
                        printf(__('By posting your project, you agree to our <a href="%s">Term of Use and Privacy policy</a>', 'page-submit-project'), et_get_page_link('tos') );
                    ?>
                    </div>
                <?php } ?>
                
            <!-- Column left / End --> 
            </div>
            <!-- Column right -->
            <?php /*
        	<div class="col-md-3 col-sm-12 col-xs-12 page-sidebar" id="right_content">
                <!-- <div class="widget user_payment_status"> -->
                <?php 
                    ob_start(); 
                    ae_user_package_info($user_ID);
                    $package = ob_get_clean();
                    if($package != '') { 
                        echo '<div class="widget user_payment_status">';
                        echo $package;
                        echo '</div>';
                    }
                    get_sidebar('page'); ?>
            </div><!-- RIGHT CONTENT -->
            <?php
             */ ?>
            <!-- Column right / End -->
	   </div>
    </div>
</section>
<!-- Page Post Place / End -->        

<?php
get_footer();