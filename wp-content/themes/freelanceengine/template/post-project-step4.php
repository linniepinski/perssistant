<!-- Step 4 -->
<?php 
    global $user_ID;
    $step = 4;

    $disable_plan = ae_get_option('disable_plan', false);
    if($disable_plan) $step--;
    if($user_ID) $step--;

?>
<div class="step-wrapper step-payment" id="step-payment">
	<a href="#" class="step-heading active">
    	<span class="number-step"><?php echo $step; ?></span>
        <span class="text-heading-step"><?php _e("Select payment method", 'post-project-step4'); ?></span>
        <i class="fa fa-caret-right"></i>
    </a>
    <div class="step-content-wrapper content" style="display:none;">
        <?php do_action( 'before_payment_list_wrapper' ); ?>

        <form method="post" id="checkout_form">
            <div class="payment_info"> </div>
            <div style="position:absolute; left : -7777px; " >
                <input type="submit" id="payment_submit" />
            </div>
        </form>
                                
    	<ul class="list-price">
        <?php 
            $paypal = ae_get_option('paypal');
            if($paypal['enable']) { 
        ?>
        	<li>
                <span class="title-plan" data-type="paypal">
                    <?php _e("Paypal", 'post-project-step4'); ?>
                    <span><?php _e("Send your payment via Paypal.", 'post-project-step4'); ?></span>
                </span>
                <a href="#" class="btn btn-submit-price-plan select-payment" data-type="paypal"><?php _e("Select", 'post-project-step4'); ?></a>
            </li>
        <?php }
            $co = ae_get_option('2checkout');
            if($co['enable']) { 
         ?>
            <li>
                <span class="title-plan" data-type="2checkout">
					<?php _e("2Checkout", 'post-project-step4'); ?>
                    <span><?php _e("Send your payment via 2Checkout.", 'post-project-step4'); ?></span>
                </span>
                <a href="#" class="btn btn-submit-price-plan select-payment" data-type="2checkout"><?php _e("Select", 'post-project-step4'); ?></a>
            </li>
        <?php 
        }   
            $cash = ae_get_option('cash');
            if($cash['enable']) { 
        ?>
            <li>
                <span class="title-plan" data-type="cash">
                    <?php _e("Cash", 'post-project-step4'); ?>
                    <span><?php _e("Send your cash payment to our bank account", 'post-project-step4'); ?></span>
                </span>
                <a href="#" class="btn btn-submit-price-plan select-payment" data-type="cash"><?php _e("Select", 'post-project-step4'); ?></a>
            </li>
        <?php } 
            do_action( 'after_payment_list' ); 
        ?>
        </ul>
        <?php do_action( 'after_payment_list_wrapper' ); ?>
    </div>
</div>

<!-- Step 4 / End -->