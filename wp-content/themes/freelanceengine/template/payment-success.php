<?php 
/**
 * this template for payment success, you can overide this template by child theme
*/
global $ad, $payment_return;
extract( $payment_return );
$payment_type			= get_query_var( 'paymentType' );	
?>
<div class="redirect-content" style="overflow:hidden" >
	<div class="main-center main-content">
	<h3 class="title"><?php _e("Success, friend",'payment-success');?></h3>
	<?php 
	if($ad):
		$permalink	=	get_permalink( $ad->ID );
	?>
		<div class="content">
		<?php 
			if($payment_type == 'cash'){
				printf(__("<p>Your listing has been submitted to our website.</p> %s ", 'payment-success') , $response['L_MESSAAGE']);
			}

			if($payment_status == 'Pending') 
				printf(__("Your payment has been sent successfully but is currently set as 'pending' by %s. <br/>You will be notified when your listing is approved.", 'payment-success'), $payment_type); 
			?>
			<br/>
			<?php _e("You are now redirected to your listing page ... ",'payment-success');?> 
			<br/>
			<?php printf(__('Time left: %s', 'payment-success' ), '<span class="count_down">10</span>');  ?> 
		</div>
		<?php echo '<a href="'.$permalink.'" >'.get_the_title( $ad->ID ).'</a>'; ?>
	<?php
	else:
		$order = $payment_return['order'];
		$order_pay = $order->get_order_data();
		
		if($payment_type == 'cash'){
			printf(__("<p>You have purchased successfull package: %s.</p> %s ", 'payment-success'), $order_pay['payment_package'] , $response['L_MESSAAGE']);
		}
		if($payment_status == 'Pending') {
			printf(__("Your payment has been sent successfully but is currently set as 'pending' by %s. <br/>You will be notified when your listing is approved.", 'payment-success'), $payment_type);
		}
		?>
		<br/>
		<?php _e("You are now redirected to home page ... ",'payment-success');?> 
		<br/>
		<?php printf(__('Time left: %s', 'payment-success' ), '<span class="count_down">10</span>');
	endif;
	?>
	</div>
</div>	
