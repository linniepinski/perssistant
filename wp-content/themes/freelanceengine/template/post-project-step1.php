<!-- Step 1 -->
<?php
    global $user_ID, $ae_post_factory;
    $ae_pack = $ae_post_factory->get('pack');
    $packs = $ae_pack->fetch();

    $package_data = AE_Package::get_package_data( $user_ID );

    $orders = AE_Payment::get_current_order($user_ID);
?>

<div class="step-wrapper step-plan" id="step-plan">
	<a href="#" class="step-heading active">
    	<span class="number-step">1</span>
        <span class="text-heading-step"><?php _e( 'Select your pricing plan' , 'post-project-step1' ); ?></span>
        <i class="fa fa-caret-down"></i>
    </a>
    <div class="step-content-wrapper content">
    	<ul class="list-price">
        <?php foreach ($packs as $key => $package) { 
            $number_of_post =   $package->et_number_posts;
            $sku = $package->sku;
            $text = '';
            $order = false;
            if($number_of_post > 1 ) {
                // get package current order
                if(isset($orders[$sku])) {
                    $order = get_post($orders[$sku]);
                }
                
                if( isset($package_data[$sku] ) && $package_data[$sku]['qty'] > 0 ) {
                    /**
                     * print text when company has job left in package
                    */
                    $number_of_post =   $package_data[$sku]['qty'];
                    if($number_of_post > 1 ) {
                        $text = sprintf(__("You can submit %d posts using this plan.", 'post-project-step1') , $number_of_post);
                    }
                    else  {
                        $text = sprintf(__("You can submit %d post using this plan.", 'post-project-step1') , $number_of_post);
                    }
                }else {
                    /**
                     * print normal text if company dont have job left in this package
                    */
                    $text = sprintf(__("You can submit %d posts using this plan.", 'post-project-step1') , $number_of_post);       
                }
                
            }
            
            $class_select = '';
            if($package->et_price > 0 && isset($package_data[$sku]['qty']) && $package_data[$sku]['qty'] > 0 ) {
                $order = get_post($orders[$sku]);
                if( $order && !is_wp_error( $order ) ){
                    $class_select = 'class="auto-select '.$order->post_status.'"' ;
                }
            }
        ?>
        	<li <?php echo $class_select; ?> data-sku="<?php echo $package->sku ?>" data-id="<?php echo $package->ID ?>" data-price="<?php echo $package->et_price; ?>"
                <?php if( $package->et_price ) { ?>
                    data-label="<?php printf(__("You have selected: %s", 'post-project-step1') , $package->post_title ); ?>"
                <?php } else { ?>
                    data-label="<?php _e("You are currently using the 'Free' plan", 'post-project-step1'); ?>"
                <?php } ?>
             >
            	<span class="price">
                    <?php if( $package->et_price ) {
                        ae_price($package->et_price);
                    }else {
                        _e("Free", 'post-project-step1');
                    } ?>
                </span>
                <span class="title-plan">
                    <?php echo $package->post_title; if($text) { echo ' - '. $text; } ?> 
                    <span><?php echo $package->post_content; ?></span>
                </span>
                <a href="#" class="btn btn-submit-price-plan select-plan"><?php _e( 'Select' , 'post-project-step1' ); ?></a>
                <div class="clearfix"></div>
            </li>
        <?php }
        echo '<script type="data/json" id="package_plans">'.json_encode($packs).'</script>';
         ?>
        </ul>
    </div>
</div>
<!-- Step 1 / End -->