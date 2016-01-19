<!-- Step 1 -->
<?php
    global $user_ID, $ae_post_factory;
    $ae_pack = $ae_post_factory->get('bid_plan');
    $packs = $ae_pack->fetch('bid_plan');

    // $package_data = AE_Package::get_package_data( $user_ID );

?>

<div class="step-wrapper step-plan" id="step-plan">
	<a href="#" class="step-heading active">
    	<span class="number-step">1</span>
        <span class="text-heading-step"><?php _e( 'Select your pricing plan' , ET_DOMAIN ); ?></span>
        <i class="fa fa-caret-down"></i>
    </a>
    <div class="step-content-wrapper content">
    	<ul class="list-price">
        <?php foreach ($packs as $key => $package) { 
            $number_of_post =   $package->et_number_posts;
            $sku = $package->sku;
            $text = sprintf(__("You can bid %d projects using this plan.", ET_DOMAIN) , $number_of_post);  
        ?>
        	<li data-sku="<?php echo $package->sku ?>" data-id="<?php echo $package->ID ?>" data-price="<?php echo $package->et_price; ?>" >
            	<span class="price">
                    <?php if( $package->et_price ) {
                        ae_price($package->et_price);
                    }else {
                        _e("Free", ET_DOMAIN);
                    } ?>
                </span>
                <span class="title-plan">
                    <?php echo $package->post_title; if($text) { echo ' - '. $text; } ?> 
                    <span><?php echo $package->post_content; ?></span>
                </span>
                <a href="#" class="btn btn-submit-price-plan select-plan"><?php _e( 'Select' , ET_DOMAIN ); ?></a>
            </li>
        <?php }
        echo '<script type="data/json" id="package_plans">'.json_encode($packs).'</script>';
        ?>
        </ul>
    </div>
</div>
<!-- Step 1 / End -->