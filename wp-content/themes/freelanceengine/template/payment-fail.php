<?php
/**
 * this template for payment fail, you can overide this template by child theme
*/
global $ad;	
?>
<div class="redirect-content" >
	<div class="main-center">
		<h3 class="title"><?php _e("Payment fail, friend",'payment-fail');?></h3>
		<?php
		if($ad) :
			$permalink	=	et_get_page_link('submit-project', array( 'id' => $ad->ID ));
		?>
			<div class="content">
				<?php _e("You are now redirected to submit listing page ... ",'payment-fail');?> <br/>
				<?php printf(__('Time left: %s', 'payment-fail' ), '<span class="count_down">10</span>')  ?> 
			</div>
			<?php echo '<a href="'.$permalink.'" >'.__("Post Project", 'payment-fail').'</a>'; 
		else :
			$permalink	=	home_url();
		?>	
		<div class="content">
				<?php _e("You are now redirected to home page ... ",'payment-fail');?> <br/>
				<?php printf(__('Time left: %s', 'payment-fail' ), '<span class="count_down">10</span>')  ?> 
			</div>
			<?php echo '<a href="'.$permalink.'" >'.__("Home page", 'payment-fail').'</a>'; 
		endif;
		?>
	</div>
</div>
<?php
