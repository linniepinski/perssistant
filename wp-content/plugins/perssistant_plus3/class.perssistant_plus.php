<?php

/**
 * Created by PhpStorm.
 * User: ANDREY
 * Date: 27.10.15
 * Time: 13:02
 */
class perssistant_plus
{
    public static function Init()
    {
        wp_enqueue_script('front-page-js', plugin_dir_url(__FILE__) . 'script.js', array('jquery'));

        wp_register_style('perssistant_plus', plugin_dir_url(__FILE__) . 'style.css');
        wp_enqueue_style('perssistant_plus');

        wp_localize_script('front-page-js', 'MyAjax', array('ajaxurl' => admin_url('admin-ajax.php')));


        add_action('wp_ajax_get_payment_form', array('perssistant_plus', 'get_payment_form'));
        add_action('wp_ajax_nopriv_get_payment_form', array('perssistant_plus', 'get_payment_form'));

        //add_action('perssistant_plus', array('perssistant_plus', 'pers_plus_post'));

        //add_action('wp_footer', array('perssistant_plus', 'stripe_form'));
        //add_action('perssistant_plus', array('perssistant_plus', 'display_perssistant_plus'));
        add_shortcode('paymill', array('perssistant_plus', 'display_paymill_btn'));


    }

    public static function get_payment_form()
    {
        echo do_shortcode('[paymill id=99 amount=' . $_POST['custom_amount'] . ' title="Custom amount" custom_description="' . htmlspecialchars($_POST['custom_description']) . '" ]');

        ?>

        <?php
        wp_die();
    }


    public static function display_paymill_btn($atts, $content = null)
    {
//var_dump($atts['amount']);
if ($atts['amount']== null or $atts['amount']==''){
    $atts['amount']= 1;
}
        ?>

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