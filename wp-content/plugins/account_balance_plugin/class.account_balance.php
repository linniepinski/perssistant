<?php

/**
 * Created by PhpStorm.
 * User: ANDREY
 * Date: 27.10.15
 * Time: 13:02
 */
class account_balance
{
    public static function Init()
    {
        wp_enqueue_script('front-page-js', plugin_dir_url(__FILE__) . 'script.js', array('jquery'));

        wp_register_style('account_balance', plugin_dir_url(__FILE__) . 'style.css');
        wp_enqueue_style('account_balance');

        wp_localize_script('front-page-js', 'MyAjax', array('ajaxurl' => admin_url('admin-ajax.php')));

        add_action('wp_ajax_get_payment_form', array('account_balance', 'get_payment_form'));
        add_action('wp_ajax_nopriv_get_payment_form', array('account_balance', 'get_payment_form'));

        add_shortcode('paymill', array('account_balance', 'display_paymill_btn'));
        add_shortcode('modal_paymill', array('account_balance', 'get_modal_paymill'));
    }



    public static function get_payment_form()
    {
        echo do_shortcode('[paymill id=99 amount=' . $_POST['custom_amount'] . ' title="Custom amount" custom_description="' . htmlspecialchars($_POST['custom_description']) . '" ]');

        wp_die();
    }

    public static function get_modal_paymill($atts, $content = null)
    {
        ?>

        <div class="modal fade" id="stripe_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel">{PLAN NAME}</h4>
                    </div>
                    <div class="modal-body">
                        <form id="custom_payment">
                            <div class="row" style="margin-bottom: 15px">
                                <div class="col-xs-12">
<!--                                    <label for="custom_description">Comment</label>-->
                                    <textarea style="display: none" id="custom_description" class="form-control"
                                              name="custom_description">gsdfgsdgf</textarea>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-6">
                                    <!--        <label for="custom_amount">Amount</label>-->
                                    <input id="custom_amount" type="number" min="1" name="custom_amount" class="form-control" required=""
                                           value="" placeholder="Amount, &euro;">
                                </div>
                                <div class="col-xs-6">
                                    <button id="amount_send" type="submit"
                                            class="btn btn-primary btn-block">Confirm
                                    </button>
                                </div>

                        </form>
                    </div>
                    <div id="created_payment" style="margin-top: 10px">

                    </div>
                </div>

                <!--                    <div-->
                <!--                        class="wp-stripe-poweredby">-->
                <?php //printf(__('Payments powered by %s. No card information is stored on this server.', 'wp-stripe'), '<a href="http://wordpress.org/extend/plugins/wp-stripe" target="_blank">WP-Stripe</a>'); ?><!--</div>-->

            </div>
        </div>
        </div>
    <?php

    }

    public static function display_paymill_btn($atts, $content = null)
    {
//var_dump($atts['amount']);
        if ($atts['amount'] == null or $atts['amount'] == '') {
            $atts['amount'] = 1;
        }
        ?>
        <script type="text/javascript" src="https://bridge.paymill.com/"></script>
        <form action="/payment" class="payment_paymill" method="post">

            <script
                src="https://button.paymill.com/v1/"
                id="buttonPayment<?php echo $atts['id'] ?>"
                data-label="Buy"
                data-title="<?php echo $atts['title'] ?>"
                data-description=""
                data-submit-button="Pay <?php echo $atts['amount'] ?> EUR"
                data-amount="<?php echo $atts['amount'] * 100 ?>"
                data-currency="EUR"
                data-elv="false"
                data-public-key="<?php echo get_option('paymill_public_key'); ?>"
                data-inline="true"

                ></script>
            <input type="hidden" name="amount" value="<?php echo $atts['amount'] * 100 ?>">
            <?php
            if ($atts['id'] == '99') {
                ?>
                <input type="hidden" name="custom_description" value="<?php echo $atts['custom_description'] ?>">

            <?php
            }
            ?>

        </form>
    <?php
    }


}

function account_balance(){
    global $current_user;
    $user_balance = get_user_meta($current_user->data->ID, 'account_cash_balance', true);
    //var_dump(get_user_meta($current_user->data->ID,'account_cash_balance',true));
    $style = '';

        if ($user_balance > 0) {
           $style = 'balance';
            $string_balance = fre_price_format($user_balance / 100);

        } else {
            $style = 'negative-balance';
            $string_balance = fre_price_format($user_balance / 100);
        }
    return array('style' => $style, 'string_balance' => $string_balance);
}

function view_account_balance()
{
    global $current_user;
    $user_balance = get_user_meta($current_user->data->ID, 'account_cash_balance', true);
    //var_dump(get_user_meta($current_user->data->ID,'account_cash_balance',true));

    ?>
    <div class="account-balance">
        <label>Your account balance:</label>

        <?php
        if ($user_balance > 0) {
            ?>
            <span class="balance">
    <?php
    echo fre_price_format($user_balance / 100);
    ?>
        </span>
        <?php
        } else {
            ?>
            <span class="negative_balance">
    <?php
    echo fre_price_format($user_balance / 100);
    ?>
        </span>
        <?php
        }
        ?>
    </div>
<?php

}

function wpdb_add_history_account_balance($from, $to, $balance_before, $balance_change, $balance_after, $description, $source, $response, $purpose, $project_id)
{
    global $wpdb;
    $result = $wpdb->insert("wp_account_balance_history", array(
        "from" => $from,
        "to" => $to,
        "balance_before" => $balance_before,
        "balance_change" => $balance_change,
        "balance_after" => $balance_after,
        "description" => $description,
        "source" => $source,
        "response" => $response,
        "purpose" => $purpose,
        "project_id" => $project_id,

    ));
    //var_dump($result);
    return $result;
}

function requestApi($action = '', $params = array(), $privateApiKey)
{
    $curlOpts = array(
        CURLOPT_URL => "https://api.paymill.com/v2/" . $action,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_USERAGENT => 'Paymill-php/0.0.2',
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_CAINFO => realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'paymill.crt',
    );

    $curlOpts[CURLOPT_POSTFIELDS] = http_build_query($params, null, '&');
    $curlOpts[CURLOPT_USERPWD] = $privateApiKey . ':';

    $curl = curl_init();
    curl_setopt_array($curl, $curlOpts);
    $responseBody = curl_exec($curl);
    $responseInfo = curl_getinfo($curl);
    if ($responseBody === false) {
        $responseBody = array('error' => curl_error($curl));
    }
    curl_close($curl);

    if ('application/json' === $responseInfo['content_type']) {
        $responseBody = json_decode($responseBody, true);
    }

    return array(
        'header' => array(
            'status' => $responseInfo['http_code'],
            'reason' => null,
        ),
        'body' => $responseBody
    );
}

/**
 * Perform API and handle exceptions
 *
 * @param        $action
 * @param array $params
 * @param string $privateApiKey
 *
 * @return mixed
 */
function request($action, $params = array(), $privateApiKey)
{
    if (!is_array($params)) {
        $params = array();
    }

    $responseArray = requestApi($action, $params, $privateApiKey);
    $httpStatusCode = $responseArray['header']['status'];
    if ($httpStatusCode != 200) {
        $errorMessage = 'Client returned HTTP status code ' . $httpStatusCode;
        if (isset($responseArray['body']['error'])) {
            $errorMessage = $responseArray['body']['error'];
        }
        $responseCode = '';
        if (isset($responseArray['body']['data']['response_code'])) {
            $responseCode = $responseArray['body']['data']['response_code'];
        }

        return array("data" => array(
            "error" => $errorMessage,
            "response_code" => $responseCode,
            "http_status_code" => $httpStatusCode
        ));
    }

    return $responseArray['body']['data'];
}