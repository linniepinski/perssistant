<?php


define('CUSTOM_THEME_SETTINGS_STRIPE_SECRET_KEY', 'settings_stripe_secret_key');
define('CUSTOM_THEME_SETTINGS_STRIPE_PUBILC_KEY', 'settings_stripe_public_key');
define('CUSTOM_THEME_SETTINGS_STRIPE_CLIENT_ID', 'settings_stripe_client_id');
define('CUSTOM_THEME_SETTINGS_COMPANY_FEE', 'settings_company_fee_for_stripe');

if (is_admin()) {

    function validate_custom_theme_stripe_payment_settings_submit($input) {

        $input[CUSTOM_THEME_SETTINGS_STRIPE_SECRET_KEY] =
            empty($input[CUSTOM_THEME_SETTINGS_STRIPE_SECRET_KEY]) ?
                '' : $input[CUSTOM_THEME_SETTINGS_STRIPE_SECRET_KEY];
        $input[CUSTOM_THEME_SETTINGS_STRIPE_PUBILC_KEY] =
            empty($input[CUSTOM_THEME_SETTINGS_STRIPE_PUBILC_KEY]) ?
                '' : $input[CUSTOM_THEME_SETTINGS_STRIPE_PUBILC_KEY];
        $input[CUSTOM_THEME_SETTINGS_STRIPE_CLIENT_ID] =
            empty($input[CUSTOM_THEME_SETTINGS_STRIPE_CLIENT_ID]) ?
                '' : $input[CUSTOM_THEME_SETTINGS_STRIPE_CLIENT_ID];
        $input[CUSTOM_THEME_SETTINGS_COMPANY_FEE] =
            empty($input[CUSTOM_THEME_SETTINGS_COMPANY_FEE]) ?
                '' : $input[CUSTOM_THEME_SETTINGS_COMPANY_FEE];

        return $input;
    }

    function register_and_build_fields() {
        register_setting('custom_theme_stripe_payment_settings', 'custom_theme_stripe_payment_settings_options', 'validate_custom_theme_stripe_payment_settings_submit');
    }

    add_action('admin_init', 'register_and_build_fields');

    function create_custom_theme_stripe_payment_settings_page() {
        require_once('admin-custom_theme_stripe_payment-settings-page-html.inc.php');
    }

    function custom_theme_stripe_payment_settings_page() {
        add_options_page('Stripe Payment settings', 'Stripe Payment settings', 'administrator', 'custom_theme_stripe_payment-settings', 'create_custom_theme_stripe_payment_settings_page');
    }

    add_action('admin_menu', 'custom_theme_stripe_payment_settings_page');

}
