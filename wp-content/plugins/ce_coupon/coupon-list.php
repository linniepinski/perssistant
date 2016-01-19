<div id="coupon_list">
	<?php 
	$currency	=	ET_Payment::get_currency();
	$args	=	array(
		'post_type'		=> 'ce_coupon',
		'post_status'	=> 'publish'
	);
	$coupon_list	=	new WP_Query ($args);
	?>

	<div class="module ce-coupon-list">
		<table>
			<tr>
				<th><?php _e("Coupon code", ET_DOMAIN); ?></th>
				<th><?php _e("Start Date", ET_DOMAIN); ?></th>
				<th><?php _e("Expiry Date", ET_DOMAIN);?></th>
				<th><?php _e("Discount", ET_DOMAIN); ?></th>
				<th><?php _e("Applied Product", ET_DOMAIN); ?></th>
				<th><?php _e("Used Count", ET_DOMAIN); ?></th>
				<th><?php _e("Actions", ET_DOMAIN); ?></th>
			</tr>
			<?php 
			$plans	=	et_get_payment_plans();
			while ($coupon_list->have_posts()) { $coupon_list->the_post();
				global $post;
				$ce_coupon = new CE_Coupon();
				$coupon_data	=	$ce_coupon->generate_coupon_response(get_the_title());
				$date_limit		=	$coupon_data['date_limit'];
				$added_product	=	$coupon_data['added_product'];

			?>
			<tr id="coupon-<?php echo $post->ID; ?>" data-coupon="<?php echo $post->ID; ?>" >
				<!-- coupon data -->
				<script id="coupon_<?php echo $post->ID ?>" type="text/data">
					<?php echo json_encode($coupon_data); ?>
				</script>

				<td><?php echo $coupon_data['coupon_code'] ?></td>
				<td align="center"><?php if($date_limit == 'on') echo $coupon_data['start_date']; else _e("Lifetime", ET_DOMAIN); ?></td>
				<td align="center"><?php if($date_limit == 'on') echo $coupon_data['expired_date']; else _e("Lifetime", ET_DOMAIN); ?></td>
				<td>
				<?php
					echo $coupon_data['discount_rate'];
					if($coupon_data['discount_type'] == 'percent')
						echo ' (%)';
					else echo $currency['code'];
				?>
				</td>
				<td>
					<?php
					if(empty($added_product)) {
						_e("All ad packages", ET_DOMAIN);
					} else {
						$num	=	count($added_product);
						$i		=	0;
						foreach ($added_product as $key => $value) {
							$i++;
							echo $value;
							if($i < $num) echo ', ';
						}
					}
					?>
				</td>
				<td><?php echo intval($coupon_data['have_been_used']) ?><span class="count">/<?php echo $coupon_data['usage_count'] ?></span></td>
				<td align="center">
					<a href="#" class="delete" title="<?php _e("Delete", ET_DOMAIN); ?>"><span class="icon" data-icon="-"></span></a>
					<a href="#" class="edit" title="<?php _e("Edit", ET_DOMAIN); ?>"><span class="icon" data-icon="p"></span></a>
				</td>
			</tr>

			<?php } ?>
		</table>
	</div>

	<div class="coupon-page-navigation">
		<ul>
			<?php if ($coupon_list->max_num_pages > 1) { ?>
					<li data-page="1" ><span class="current">1</span></li>
				<?php 
					for ($i = 2; $i <= $coupon_list->max_num_pages; $i++){ ?>
					<li data-page="<?php echo $i ?>"><a  href="#" class="pi"><?php echo $i ?></a></li>
				<?php } ?>
			<?php } ?>
		</ul>
	</div>
</div>