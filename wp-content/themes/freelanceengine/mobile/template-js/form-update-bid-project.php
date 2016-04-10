<?php
global $wp_query, $ae_post_factory, $user_ID, $post;
?>
<form id="bid_form_update" class="bid-form bid-form-mobile bid-form-update" <?php if(!isset($_GET['bid'])) echo 'style="display:none"';?> >

    <div class="form-group"><label for="bid_budget"><?php
            if(get_post_meta($post->ID,'type_budget',true) == 'hourly_rate'){
                _e('Hourly rate', 'form-update-bid-project-mobile');
            }else{
                _e('Budget', 'form-update-bid-project-mobile');
            }
            ?>
        </label>
        <?php
        $post_the_id=get_the_ID();
        $bid_the_id=fre_has_bid( get_the_ID() );


        add_filter('posts_orderby', 'fre_order_by_bid_status');
        $q_bid = new WP_Query(array('post_type' => BID,
                'post_parent' => get_the_ID(),
                'author' => $user_ID,
                'post_status' => array('publish','complete', 'accept'))
        );
        remove_filter('posts_orderby', 'fre_order_by_bid_status');

        $post_object = $ae_post_factory->get(BID);

        if( $q_bid->have_posts() ) {
            while( $q_bid->have_posts() ){
                $q_bid->the_post();
                $convert    = $post_object->convert($post);
                $bid_update=$ae_post_factory->get( BID )->current_post->bid_budget;
            }
        }
        ?>

		    <?php
		    $settings_stripe_secret_key = get_option('settings_stripe_secret_key');
		    $settings_stripe_public_key = get_option('settings_stripe_public_key');
		    $settings_company_fee_for_stripe = get_option('settings_company_fee_for_stripe');
		    if(!empty($settings_stripe_secret_key) && !empty($settings_stripe_public_key) && !empty($settings_company_fee_for_stripe)){
			    $calc_price = $bid_update - (ceil($bid_update * $settings_company_fee_for_stripe) / 100 + 0.33);
			    ?>
			    <input type="number" name="bid_budget" id="bid_budget_update" style="margin-bottom: 10px;" data-fee-percentage="<?php echo $settings_company_fee_for_stripe; ?>" class="form-control required number calc_price_with_fees" min="1" value="<?php echo $bid_update; ?>" />
			    <span style="float: right;" class="calc_price_without_fees">For you, without taxes and fees  <?php echo $calc_price; ?></span>
		    <?php } else { ?>
			    <input type="number" name="bid_budget" id="bid_budget_update" class="form-control required number" min="1" value="<?php echo $bid_update; ?>"/>
		    <?php } ?>
    </div>
    <div class="clearfix"></div>

    <input type="hidden" name="post_parent" value="<?php echo  $post_the_id; ?>" /> <input type="hidden"
                                                                                           name="method"
                                                                                           value="update"/>

    <input type="hidden" name="ID"
           value="<?php echo $bid_the_id;?>"/>

    <input type="hidden" name="action"
           value="ae-sync-bid"/>                        <?php do_action('after_bid_form'); ?>
    <button type="submit"
            class="btn-submit-update btn-sumary btn-sub-create">                            <?php _e('Submit', 'form-update-bid-project-mobile') ?>                        </button>
</form>
