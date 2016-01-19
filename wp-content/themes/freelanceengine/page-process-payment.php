<?php 
/**
 *	Template Name: Process Payment
 */

$payment_type			= get_query_var( 'paymentType' );

if ($_REQUEST['invoice']) {
    $ad	=	get_post( $_REQUEST['invoice'] );
    if ($ad->post_parent) {
        #make project is featured
        update_post_meta($ad->post_parent, 'et_featured', '1');
    }
    get_header();
} else {
    $session	=	et_read_session ();

    //processs payment
    $payment_return = ae_process_payment($payment_type , $session );

    $ad_id		=	$session['ad_id'];

    get_header();

    global $ad , $payment_return;    
    $payment_return	=	wp_parse_args( $payment_return, array('ACK' => false, 'payment_status' => '' ));
    extract( $payment_return );
    if($session['ad_id'])
            $ad	=	get_post( $session['ad_id'] );    
    else 
            $ad	=	false;
}
?>

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

<!-- Page Blog -->
<section id="blog-page">
    <div class="container page-container">
		<!-- block control  -->
		<div class="row block-posts block-page">
			<div class="col-md-9 col-sm-12 col-xs-12 posts-container" id="left_content">
	            <div class="blog-content">


					<?php 
						if( ( isset($ACK) && $ACK ) || (isset($test_mode) && $test_mode) ) {
							if($ad) :
								$permalink	=	get_permalink( $ad->ID );
							else:
								$permalink = home_url();
							endif;
							/**
							 * template payment success
							*/
							get_template_part( 'template/payment' , 'success' );

						 } else if ($_REQUEST['invoice']) {
                                                     if($ad->post_parent) :
								$permalink	=	get_permalink( $ad->post_parent );
							else:
								$permalink = home_url();
							endif;
							/**
							 * template payment success
							*/
							get_template_part( 'template/payment' , 'success' );
                                                 } else {

							if($ad):
								$permalink	=	et_get_page_link('submit-project', array( 'id' => $ad->ID ));
							else :
								$permalink	=	home_url();
							endif;
							/**
							 * template payment fail
							*/
							get_template_part( 'template/payment' , 'fail' );

						}
						// clear session
						et_destroy_session();

						?>

						
				</div>
	        </div>
        

		    <!-- Column left / End --> 
		    
		    <div class="col-md-3 col-sm-12 col-xs-12 page-sidebar" id="right_content">
				<?php get_sidebar('page'); ?>
			</div><!-- RIGHT CONTENT -->
		</div>
    </div>
</section>
<!-- Page Blog / End -->   
<script type="text/javascript">
  	jQuery(document).ready (function () {
  		var $count_down	=	jQuery('.count_down');
		setTimeout (function () {
			window.location = '<?php echo $permalink ?>';
		}, 10000 );
		setInterval (function () { 
			if($count_down.length >  0) {
				var i	=	 $count_down.html();
				$count_down.html(parseInt(i) -1 );
			}					
		}, 1000 );
  	});
</script>

<?php
get_footer();