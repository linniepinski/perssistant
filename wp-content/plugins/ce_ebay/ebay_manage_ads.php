
<div id="ebay-manage" class="ebay-box hide">
	<div class="title font-quicksand"><?php _e('Imported Ads',ET_DOMAIN);?></div>
	<div class="desc">
	 	<form class="ebay-manage">
			<div id="iads" class="import-tb-container">
				<table class="ebay-ads">
					<thead>
						<tr class="heading">
							<th><input class="select_all" name="" id="select_all" type="checkbox"></th>
							<th width="12%"><?php _e('Thumbnail',ET_DOMAIN);?></th>
							<th width="33%"><?php _e('Title',ET_DOMAIN);?></th>
							<th><?php _e('Price',ET_DOMAIN);?></th>
							<th><?php _e('Address',ET_DOMAIN);?></th>
							<th><?php _e('Date',ET_DOMAIN);?></th>
						</tr>
					</thead>
					<tbody>
					<?php
					// 'meta_query' => array(
				    //    array(
				    //        'key' 	=> 'ce_ebay_item_id',
				    //        'value' 	=> 1,
				    //        'compare'=> '>=',
				    //    ))
					$args = array(
						'post_type' => CE_AD_POSTTYPE,
						'meta_key' 	=> 'ce_ebay_item_id',
					    'meta_query'         => array(
				            array(
				                'key'      =>'ce_ebay_item_id' ,
				                'value'  => '',
				                'compare'       => 'NOT EXISTS'
				                )
				            )
					   	);
					$current = array(
						'EUR' => '&euro;',
						'USD' => '$',
						'CAD' => 'CA $',
						'GBP' => '&pound;',
						'SGD' => 'S$',
						'PHP' => '&#8369;',
						'AUD' => 'AU $',
						'PLN' => 'zł',
						'CHF' => 'CHF',
						'INR' => 'INR'
						);

					$ads 	= CE_Ads::query($args);
					if($ads->have_posts()) :
						while($ads->have_posts()) : $ads->the_post();
							global $post;
							$ad = CE_Ads::convert($post);
							$currentcy = get_post_meta($ad->ID,'et_currency',true);
							$price_key = CE_ET_PRICE;
							?>
							<tr class="item-static">
								<td><input type="checkbox" id="<?php the_ID();?>" value="<?php the_ID();?>" class="select" name="id[]" /></td>
								<td><?php the_post_thumbnail();?></td>
								<td><a href="<?php the_permalink();?>"><?php the_title();?> </a></td>
								<td><?php echo (isset($current[$currentcy]) ? $current[$currentcy] : $currentcy).$ad->$price_key;?> </td>
								<td><?php the_date();?></td>
							</tr>
							<?php 
						endwhile;
					endif;

					?>
					</tbody>
					<thead>
						<tr class="head-footer">
							<th><input class="select_all" name="" id="select_all" type="checkbox"></th>
							<th width="12%"><?php _e('Thumbnail',ET_DOMAIN);?></th>
							<th width="33%"><?php _e('Title',ET_DOMAIN);?></th>
							<th><?php _e('Price',ET_DOMAIN);?></th>
							<th><?php _e('Address',ET_DOMAIN);?></th>
							<th><?php _e('Date',ET_DOMAIN);?></th>
						</tr>
					</thead>
				</table>
				<input type="hidden" name="action" value="ebay-delete-ads" />
				<div class="row row-pagination paging-wp">
				</div>
			</div>
			<div class="ebay-controls">
				<a href="#" id="delete_ebay_ad" class="icon" data-icon="#"> <?php _e('Delete selected Ads',ET_DOMAIN);?></a>
			</div>
		</form>
	</div>

</div>