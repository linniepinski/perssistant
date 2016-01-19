<?php
global $post;
$order_object =	new AE_Order($post->ID);
$order_data = $order_object->get_order_data();	
$products = $order_data['products'];

$package = array_pop($products);

$post_parent = '';
if($post->post_parent) {
	$post_parent = get_post($post->post_parent);
}

$support_gateway = apply_filters('ae_support_gateway', array(
    			'cash' => __("Cash", ET_DOMAIN),
    			'paypal' => __("Paypal", ET_DOMAIN),
    			'2checkout' => __("2Checkout", ET_DOMAIN),
    		));
?>
<li>
	<div class="method">
		<?php echo isset($support_gateway[$order_data['payment']]) ? $support_gateway[$order_data['payment']] : $order_data['payment']; 
			if($post->post_status == 'pending') : ?> 
				<a title="<?php _e("Approve", ET_DOMAIN); ?>" class="color-green action publish" data-id="<?php echo $post->ID; ?>" href="#">
					<span class="icon" data-icon="3"></span>
				</a>
				<a title="<?php _e("Decline", ET_DOMAIN); ?>" class="color-red action decline" data-id="<?php echo $post->ID; ?>" href="#">
					<span class="icon" data-icon="*"></span>
				</a>
		<?php 
			endif; 
		?>
	</div>
	<div class="content">
		<?php 
		if( $post ) {  
			switch ($post->post_status) {
			case 'pending':
				echo '<a title="' . __("Pending", ET_DOMAIN) . '" class="color-red error" href="#"><span class="icon" data-icon="!"></span></a>';
				break;
			case 'publish':
				echo '<a title="'. __("Confirmed", ET_DOMAIN) . '" class="color-green" href="#"><span class="icon" data-icon="2"></span></a>';
				break;			
			default:
				echo '<a title="' .__("Failed", ET_DOMAIN) .'" class="color" style="color :grey;" href="#"><span class="icon" data-icon="*"></span></a>';
				break;
			}
		?>	
			<span class="price font-quicksand">
				<?php echo ae_currency_sign(false) . $order_data['total']; ?>
			</span>
		<?php
			if($post_parent) { ?>
				<a target="_blank" href="<?php echo get_permalink( $post_parent->ID ) ?>" class="ad ad-name">
					<?php 
						echo get_the_title( $post_parent->ID );
						echo ' (' .$package['NAME']. ')' ; 
					?>
				</a>
			<?php }else { ?>
				<a href="#" class="ad ad-name">
					<?php echo '(' .$package['NAME']. ')' ; ?>
				</a>
			<?php
			}
			 _e(' by ', ET_DOMAIN);						        						
			?> 
			<a target="_blank" href="<?php echo get_author_posts_url($post->post_author, $author_nicename = '') ?>" class="company">
				<?php echo get_the_author_meta('display_name',$post->post_author) ?>
			</a>
		<?php 
		} else { 
			$author	=	'<a target="_blank" href="'.get_author_posts_url($post->post_author).'" class="company">' . 
							get_the_author_meta('display_name',$post->post_author) .
						'</a>'; 
		?>
			<span>
				<?php printf (__("This post has been deleted by %s", ET_DOMAIN) , $author ); ?>
			</span>
		<?php 
			} 
		?>
			
	</div>
</li>