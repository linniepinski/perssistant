<?php
/**
 * this template for payment fail, you can overide this template by child theme
*/
global $ad;	
?>
<div class="redirect-content" >
	<div class="main-center">
		<h3 class="title"><?php _e("Payment fail, friend",ET_DOMAIN);?></h3>
		<?php
		if($ad) :
			$permalink	=	et_get_page_link('submit-project', array( 'id' => $ad->ID ));
		?>
			<div class="content">
				<?php _e("You are now redirected to submit listing page ... ",ET_DOMAIN);?> <br/>
				<?php printf(__('Time left: %s', ET_DOMAIN ), '<span class="count_down">10</span>')  ?> 
			</div>
			<?php echo '<a href="'.$permalink.'" >'.__("Post Project", ET_DOMAIN).'</a>'; 
		else :
			$permalink	=	home_url();
		?>	
		<div class="content">
				<?php _e("You are now redirected to home page ... ",ET_DOMAIN);?> <br/>
				<?php printf(__('Time left: %s', ET_DOMAIN ), '<span class="count_down">10</span>')  ?> 
			</div>
			<?php echo '<a href="'.$permalink.'" >'.__("Home page", ET_DOMAIN).'</a>'; 
		endif;
		?>
	</div>
</div>
<?php
