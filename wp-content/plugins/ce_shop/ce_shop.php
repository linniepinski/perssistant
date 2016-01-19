<?php
/*
Plugin Name: CE Shop
Plugin URI: http://www.enginethemes.com/
Description: If you want to build a marketplace where users can buy/sell their product. This plugin is all you need. However please remember to active the WooCommerce plugin first.
Author: EngineTheme
Author URI: http://enginethemes.com/
Contributors: EngineThemes Team
Version: 1.1
Developer : nguyenvanduocit
*/

require_once dirname(__FILE__) . '/functions.php';
require_once dirname(__FILE__) . '/update.php';

class CE_MARKET
{


    function __construct()
    {
        add_action( 'plugins_loaded', array($this,'check_woocommerce') );
        require_once dirname( __FILE__ ) . '/includes/class-tgm-plugin-activation.php';
        add_action( 'tgmpa_register', array($this, 'register_required_plugins' ));
    }

    function check_woocommerce()
    {
        if (!class_exists('WooCommerce')) {
            add_action('admin_notices', array($this, 'admin_notice'));
        }
        else{
            add_action('ce_single_after_description', 'woocommerce_template_single_rating');
            add_action('ce_single_before_detail', array($this, 'single_before_detail'));
            add_action('ce_single_after_description', array($this, 'add_cart_button_template'));
            add_action('ce_place_item_after_price', array($this, 'after_shop_loop_item'));
            add_action('ce_header_before_profile_icon', array($this, 'header_before_profile_icon'));
            add_action('page_account_nav_tab', array($this, 'profile_page_nav_tab'));
            add_action('ce_handle_seller_order_update', array($this, 'handle_sell_order_update'), 10, 2);
            add_action('woocommerce_before_order_itemmeta', array($this, 'before_order_itemmeta'), 10, 3);
            add_action('ce_insert_ad', array($this, 'insert_product_meta'), 10, 2);
            add_action('ce_update_ad', array($this, 'update_ad_meta'), 10, 2);
            add_action('wp_head', array($this, 'enqueue_asset'));

            add_action('woocommerce_return_to_shop_redirect', array($this, 'woocommerce_return_to_shop_redirect'));
            add_action('woocommerce_coupons_enabled', array($this, 'woocommerce_coupons_enabled'));

            add_action('woocommerce_loop_add_to_cart_link', array($this, 'woocommerce_loop_add_to_cart_link'), 10, 2);

        }
    }

    function admin_notice()
    {
        ?>
        <div class="error">
            <p><?php _e('To enable shop functions, you have to install/enable plugin "Woocommerce"', ET_DOMAIN); ?></p>
        </div>
    <?php
    }
    function register_required_plugins(){
        $plugins = array(
            // This is an example of how to include a plugin pre-packaged with a theme.
            array(
                'name'               => 'WooCommerce - excelling eCommerce', // The plugin name.
                'slug'               => 'woocommerce', // The plugin slug (typically the folder name).
                'required'           => true, // If false, the plugin is only 'recommended' instead of required.
                'force_activation'   => true, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
                'force_deactivation' => true, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
            ),
        );
        /**
         * Array of configuration settings. Amend each line as needed.
         * If you want the default strings to be available under your own theme domain,
         * leave the strings uncommented.
         * Some of the strings are added into a sprintf, so see the comments at the
         * end of each line for what each argument will be.
         */
        $config = array(
            'default_path' => '',                      // Default absolute path to pre-packaged plugins.
            'menu'         => 'ce-shop-install-plugin', // Menu slug.
            'has_notices'  => true,                    // Show admin notices or not.
            'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
            'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
            'is_automatic' => true,                   // Automatically activate plugins after installation or not.
            'message'      => 'asdfasdf',                      // Message to output right before the plugins table.
            'strings'      => array(
                'page_title'                      => __( 'Install Required Plugins', 'tgmpa' ),
                'menu_title'                      => __( 'Install Plugins', 'tgmpa' ),
                'installing'                      => __( 'Installing Plugin: %s', 'tgmpa' ), // %s = plugin name.
                'oops'                            => __( 'Something went wrong with the plugin API.', 'tgmpa' ),
                'notice_can_install_required'     => _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.' ), // %1$s = plugin name(s).
                'notice_can_install_recommended'  => _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.' ), // %1$s = plugin name(s).
                'notice_cannot_install'           => _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.' ), // %1$s = plugin name(s).
                'notice_can_activate_required'    => _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s).
                'notice_can_activate_recommended' => _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s).
                'notice_cannot_activate'          => _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.' ), // %1$s = plugin name(s).
                'notice_ask_to_update'            => _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.' ), // %1$s = plugin name(s).
                'notice_cannot_update'            => _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.' ), // %1$s = plugin name(s).
                'install_link'                    => _n_noop( 'Begin installing plugin', 'Begin installing plugins' ),
                'activate_link'                   => _n_noop( 'Begin activating plugin', 'Begin activating plugins' ),
                'return'                          => __( 'Return to Required Plugins Installer', 'tgmpa' ),
                'plugin_activated'                => __( 'Plugin activated successfully.', 'tgmpa' ),
                'complete'                        => __( 'All plugins installed and activated successfully. %s', 'tgmpa' ), // %s = dashboard link.
                'nag_type'                        => 'updated' // Determines admin notice type - can only be 'updated', 'update-nag' or 'error'.
            )
        );
        tgmpa( $plugins, $config );
    }
    function enqueue_asset()
    {
        if (is_page_template('page-account-transaction.php')) {

            wp_enqueue_script('datepicker', TEMPLATEURL . "/js/lib/bootstrap-datepicker.js");
            wp_enqueue_script('page-account-transaction', TEMPLATEURL . "/js/page-account-transaction.js");
            wp_enqueue_style('datepicker', TEMPLATEURL . "/css/datepicker.css");
        }
        wp_enqueue_style('ce_marketplace', plugins_url("/css/ce_marketplace.css", __FILE__));
    }

    function woocommerce_return_to_shop_redirect($home) {
        return home_url();
    }
    
    function woocommerce_coupons_enabled($enable){
        return false;
    }

    function before_order_itemmeta($item_id, $item, $_product)
    {
        $sellerData = get_userdata($_product->post->post_author);
        echo sprintf(__(" - <a href=\"%s\">%s</a>", ET_DOMAIN), $sellerData->user_url, $sellerData->display_name);
    }

    function handle_sell_order_update($order, $postData)
    {
        global $user_ID;
        if (count($postData) > 0) {
            if ($order->has_status('completed')) {
                _e("This order is completed, you can not edit it.", ET_DOMAIN);
            } elseif (!isset($_POST['ce_update_order_nonce']) || !wp_verify_nonce($_POST['ce_update_order_nonce'], 'ce_update_order_nonce')) {
                _e('Sorry, your nonce did not verify.', ET_DOMAIN);
            } else {
                foreach ($postData['status'] as $item_id => $status) {
                    $result = wc_update_order_item_meta($item_id, 'status', $status);
                }
            }
        }
    }

    function profile_page_nav_tab($section = '')
    {
        ?>
        <li <?php if (is_page_template('page-account-sell-order.php')) echo 'class="active"' ?>><a
                title="<?php _e("Views all your sell order", ET_DOMAIN); ?>"
                href="<?php echo et_get_page_link(array('page_type' => 'account-sell-order', 'post_title' => __("Sale order", ET_DOMAIN))) ?>"><?php _e("Sale Order", ET_DOMAIN); ?></a>
        </li>
        <li <?php if (is_page_template('page-account-purchase-invoice.php')) echo 'class="active"' ?>><a
                title="<?php _e("Views all your purchase invoice", ET_DOMAIN); ?>"
                href="<?php echo et_get_page_link(array('page_type' => 'account-purchase-invoice', 'post_title' => __("Purchase order", ET_DOMAIN))) ?>"><?php _e("Purchase order", ET_DOMAIN); ?></a>
        </li>
        <li <?php if (is_page_template('page-account-transaction.php')) echo 'class="active"' ?>><a
                title="<?php _e("Views all your transactions", ET_DOMAIN); ?>"
                href="<?php echo et_get_page_link(array('page_type' => 'account-transaction', 'post_title' => __("Transactions", ET_DOMAIN))) ?>"><?php _e("Transactions", ET_DOMAIN); ?></a>
        </li>
    <?php
    }

    /**
     *
     * Insert post to database
     *
     * @param $post_id
     * @param $data
     */
    function insert_product_meta($post_id, $data)
    {
        add_post_meta($post_id, 'total_sales', '0', true);
        wp_set_object_terms($post_id, 'simple', 'product_type', false);

        wc_update_product_stock_status($post_id, 'instock');

        update_post_meta($post_id, '_sale_price_dates_from', '');
        update_post_meta($post_id, '_sale_price_dates_to', '');

        update_post_meta($post_id, '_downloadable', 'no');
        update_post_meta($post_id, '_virtual', 'no');
        update_post_meta($post_id, '_sale_price', '');

        if (isset($data['_regular_price']) && ($data['_regular_price'] > 0)) {
            update_post_meta($post_id, '_price', $data['_regular_price']);
        }

        update_post_meta($post_id, '_tax_status', '');
        update_post_meta($post_id, '_tax_class', '');
        update_post_meta($post_id, '_purchase_note', '');
        wp_set_object_terms($post_id, '', 'product_shipping_class');
        update_post_meta($post_id, '_sku', '');
        update_post_meta($post_id, '_sold_individually', '');

        do_action('woocommerce_process_product_meta_' . 'simple', $post_id);
        wc_delete_product_transients($post_id);
    }

    /**
     *
     * Update post to database
     *
     * @param $post_id
     * @param $data
     */
    function update_ad_meta($post_id, $data)
    {
        wp_set_object_terms($post_id, 'simple', 'product_type');
        if (isset($data['_regular_price']) && ($data['_regular_price'] > 0)) {
            update_post_meta($post_id, '_price', $data['_regular_price']);
        }
    }

    function single_before_detail()
    {
        echo '<div class="row">';
        do_action('woocommerce_before_single_product');
        echo '</div>';
    }

    function header_before_profile_icon()
    {
        if (WC()->cart->cart_contents_count > 0): ?>
            <span class="cart-icon">
                <a href="<?php echo WC()->cart->get_cart_url() ?>" class="bg-btn-header btn-header"
                   title="Your cart">
                    <i class="fa fa-shopping-cart"></i>
                    <?php echo WC()->cart->cart_contents_count; ?>
                </a>
            </span>
        <?php
        endif;
    }

    function after_shop_loop_item()
    {
        do_action('woocommerce_after_shop_loop_item');
    }

    function woocommerce_loop_add_to_cart_link($link, $product){
        global $product;

        return sprintf( '<a href="%s" rel="nofollow" data-product_id="%s" data-product_sku="%s" data-quantity="%s" class="btn-primary button %s product_type_%s">%s <i class="fa fa-shopping-cart"></i></a>',
                esc_url( $product->add_to_cart_url() ),
                esc_attr( $product->id ),
                esc_attr( $product->get_sku() ),
                esc_attr( isset( $quantity ) ? $quantity : 1 ),
                $product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
                esc_attr( $product->product_type ),
                esc_html( $product->add_to_cart_text() )
            );
    }

    function add_cart_button_template()
    {
        woocommerce_template_single_add_to_cart();
    }
}

new CE_MARKET();

