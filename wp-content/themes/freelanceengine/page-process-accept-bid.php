<?php 
/**
 *	Template Name: Process Accept Bid
 */

$payment_type			= get_query_var( 'paymentType' );

$session	=	et_read_session ();
//processs payment
$payment_return = fre_process_escrow($payment_type , $session );

$ad_id		=	$session['ad_id'];

get_header();

global $ad , $payment_return;

$payment_return	=	wp_parse_args( $payment_return, array('ACK' => false, 'payment_status' => '' ));
extract( $payment_return );
if($session['ad_id'])
	$ad	=	get_post( $session['ad_id'] );
else 
	$ad	=	false;

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
					$permalink	=	get_permalink( $ad->ID );
					if( isset($ACK) && $ACK  ) {
						$permalink = add_query_arg(array('workspace' => 1), $permalink );
						$workspace = '<a href="'.$permalink.'">'.get_the_title($ad->ID).'</a>';
						printf(__( 'You have successfully sent the money. Now you can start working on project %s .' , ET_DOMAIN ), $workspace);
						/**
						 * template payment success
						*/
						// redirect to workspace

					 } else {
					 	// redirect to project place
						_e( 'Accept bid fail' , ET_DOMAIN );
						echo $payment_return['msg'];
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