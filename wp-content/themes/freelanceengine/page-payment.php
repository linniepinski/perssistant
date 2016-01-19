<?php
/**
 * Template Name: Payment Template
 *
 * Displays the Team Template of the theme.
 *
 * @package ThemeGrill
 * @subpackage Himalayas
 * @since Himalayas 1.0
 */


global $post;
get_header();
$current_user = wp_get_current_user();
the_post();
?>
    <div class="container-fluid page-container">
        <!-- block control  -->
        <div class="row block-posts">
            <?php

            ?>
            <div class="col-md-12 col-sm-12 col-xs-12 posts-container" id="left_content">

                <?php
                $amount = $_POST['amount']; // E.g. "250" for 2.50 EUR!
                $currency = 'EUR'; // ISO 4217
                $description = $_SERVER["HTTP_REFERER"];
                $privateApiKey = get_option('paymill_secret_key');

                if (isset($_POST['paymillToken'])) {
                    $token = $_POST['paymillToken'];

                    $client = request(
                        'clients/',
                        array(),
                        $privateApiKey
                    );

                    $payment = request(
                        'payments/',
                        array(
                            'token' => $token,
                            'client' => $client['id']
                        ),
                        $privateApiKey
                    );

                    $transaction = request(
                        'transactions/',
                        array(
                            'amount' => $amount,
                            'currency' => $currency,
                            'client' => $client['id'],
                            'payment' => $payment['id'],
                            'description' => $description
                        ),
                        $privateApiKey
                    );

                    $isStatusClosed = isset($transaction['status']) && $transaction['status'] == 'closed';

                    $isResponseCodeSuccess = isset($transaction['response_code']) && $transaction['response_code'] == 20000;

                    if ($isStatusClosed && $isResponseCodeSuccess) {
                        echo '<strong>Transaction successful!</strong>';

                        $user_balance = get_user_meta($current_user->ID,'account_cash_balance',true);

                        if (empty($user_balance)){
                            update_user_meta($current_user->ID,'account_cash_balance', $transaction['amount']);
                        }else{
                            $new_user_balance = (float)$user_balance + $transaction['amount'];
                            update_user_meta($current_user->ID,'account_cash_balance', $new_user_balance);
                        }
                        var_dump(get_user_meta($current_user->ID,'account_cash_balance',true));

                        wpdb_add_history_account_balance($current_user->ID,$current_user->ID,$user_balance,$transaction['amount'],$new_user_balance,null,$_SERVER["HTTP_REFERER"],serialize($transaction),'account recharge',null);

                        echo "<pre>";
                        var_dump($transaction);
                        echo "</pre>";


                    } else {
                        echo '<strong>Transaction not successful!</strong> <br />';


                        echo "<pre>";
                        var_dump($transaction);
                        echo "</pre>";
                    }
                }
                ?>


                <div class="clearfix"></div>
                <?php /*
		        <div class="latest-pages">
		        	<h4><?php _e("Similar Pages", ET_DOMAIN) ?></h4>
		        	<?php fre_latest_pages($post->ID) ?>
		        </div>
		        */ ?><!-- end latest page -->
                <!-- end page content -->
            </div>
        </div>
        <!--// block control  -->
    </div>

        <?php

        get_footer();
        ?>

