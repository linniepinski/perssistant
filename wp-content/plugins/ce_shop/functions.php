<?php
function cem_get_order_item_statues()
{
    return wc_get_order_statuses();
}

function cem_get_order_item_status($key)
{
    $status = cem_get_order_item_statues();
    if (isset($status[$key])) {
        return $status[$key];
    } else {
        return null;
    }
}

/**
 *
 */
function et_render_transaction_report(){

    global $user_ID;
    $start_date = $end_date = null;
    if(isset($_POST['start_date']))
    {
        $start_date = $_POST['start_date'];
    }

    if(isset($_POST['end_date']))
    {
        $end_date = $_POST['end_date'];
    }

    $selled_orders = et_get_order_product_by_seller($user_ID, $start_date, $end_date);
    $report = array();
    foreach($selled_orders as $order_id => $order){

        $date = get_the_date("Y-m-d",$order_id);
        if( isset($report[$date]["salecount"])){
            $report[$date]["salecount"] += count($order);
        }
        else{
            $report[$date]["salecount"]  = count($order);
        }
        $report[$date]["subtotal"]  = array();
        foreach($order as $product_id => $product){
            if(isset($report[$date]["subtotal"][$product->status])){
                $report[$date]["subtotal"][$product->status] +=$product->subtotal;
            }
            else{
                $report[$date]["subtotal"][$product->status] =$product->subtotal;
            }
        }
    }
    ob_start();
    foreach($report as $date => $data){
        echo "<tr>";
        echo "<td>{$date}</td>";
        echo "<td>{$data['salecount']}</td>";
        echo "<td>";
        foreach($data['subtotal'] as $status => $total){
            $subtotal = wc_price($total);
            $status = wc_get_order_status_name($status);
            echo "{$status} : {$subtotal}<br>";
        }
        echo "</td>";
        echo "</tr>";
    }
    ob_end_flush();
}

/**
 * @param $user_ID
 * @return array
 */
function et_et_order_by_customer($user_ID)
{
    global $wpdb;
    if ($user_ID == null) {
        global $user_ID;
    }

    $order_status = array_keys(wc_get_order_statuses());
    $order_status = "'" . implode("','", $order_status) . "'";

    $order_item = $wpdb->prefix . "woocommerce_order_items";
    $order_itemmeta = $wpdb->prefix . "woocommerce_order_itemmeta";

    $query = "SELECT order_post.*,order_itemmeta_2.order_item_id as order_item_id, order_itemmeta_2.meta_value as subtotal, order_itemmeta_3.meta_value as qty, order_item.order_id as order_id, product_post.ID as product_id, product_post.post_title as product_title, order_meta_address1.meta_value as billing_address_1, billing_city.meta_value as city
                        FROM $wpdb->posts order_post
                        INNER JOIN $order_item order_item ON  order_post.ID = order_item.order_id
                        INNER JOIN $order_itemmeta order_itemmeta ON order_item.order_item_id = order_itemmeta.order_item_id
                        INNER JOIN $order_itemmeta order_itemmeta_2 ON order_item.order_item_id = order_itemmeta_2.order_item_id
                        INNER JOIN $order_itemmeta order_itemmeta_3 ON order_item.order_item_id = order_itemmeta_2.order_item_id
                        INNER JOIN $wpdb->postmeta order_meta_address1 ON order_post.ID = order_meta_address1.post_id
                        INNER JOIN $wpdb->postmeta billing_city ON order_post.ID = billing_city.post_id
                        AND order_meta_address1.meta_key = '_billing_address_1'
                        AND billing_city.meta_key = '_billing_city'
                        AND order_itemmeta.meta_key = '_product_id'
                        AND order_itemmeta_2.meta_key = '_line_subtotal'
                        AND order_itemmeta_3.meta_key = '_qty'
                        AND order_post.post_status IN ($order_status)
                        AND order_post.post_author = $user_ID
                        AND order_post.post_type = 'shop_order'";

    $orders = $wpdb->get_results($query, OBJECT);
    //TODO : Cần Cải  tiến
    $need_orders = array();
    foreach ($orders as $seller_order) {
        $need_orders[$seller_order->order_id][$seller_order->product_id] = $seller_order;
    }
    return $need_orders;
}

/**
 * @param null $user_ID
 * @param null $start_date
 * @param null $end_date
 * @return array
 */
function et_get_order_product_by_seller($user_ID = null, $start_date = null, $end_date = null)
{
    global $wpdb;
    if ($user_ID == null) {
        global $user_ID;
    }

    //Date range extra query
    $range_query = '';
    if( ( null != $start_date ) ){
        if(null == $end_date){
            $range_query = $wpdb->prepare("AND order_post.post_date = %s", et_mysql_date_format($start_date));
        }
        else{
            $range_query = $wpdb->prepare("AND order_post.post_date >= %s AND order_post.post_date <= %s", et_mysql_date_format($start_date), et_mysql_date_format($end_date));
        }
    }
    $order_status = array_keys(wc_get_order_statuses());
    $order_status = "'" . implode("','", $order_status) . "'";

    $order_item = $wpdb->prefix . "woocommerce_order_items";
    $order_itemmeta = $wpdb->prefix . "woocommerce_order_itemmeta";

    $query = "SELECT order_post.*,order_itemmeta_2.order_item_id as order_item_id, order_itemmeta_2.meta_value as subtotal, order_itemmeta_3.meta_value as qty, order_item.order_id as order_id, product_post.ID as product_id, product_post.post_title as product_title, order_meta_address1.meta_value as billing_address_1, billing_city.meta_value as city
                        FROM $wpdb->posts order_post
                        INNER JOIN $order_item order_item ON  order_post.ID = order_item.order_id
                        INNER JOIN $order_itemmeta order_itemmeta ON order_item.order_item_id = order_itemmeta.order_item_id
                        INNER JOIN $order_itemmeta order_itemmeta_2 ON order_item.order_item_id = order_itemmeta_2.order_item_id
                        INNER JOIN $order_itemmeta order_itemmeta_3 ON order_item.order_item_id = order_itemmeta_3.order_item_id
                        INNER JOIN $wpdb->posts product_post ON product_post.ID = order_itemmeta.meta_value
                        INNER JOIN $wpdb->postmeta order_meta_address1 ON order_post.ID = order_meta_address1.post_id
                        INNER JOIN $wpdb->postmeta billing_city ON order_post.ID = billing_city.post_id
                        AND order_meta_address1.meta_key = '_billing_address_1'
                        AND billing_city.meta_key = '_billing_city'
                        AND order_itemmeta.meta_key = '_product_id'
                        AND order_itemmeta_2.meta_key = '_line_subtotal'
                        AND order_itemmeta_3.meta_key = '_qty'
                        AND order_post.post_status IN ($order_status)
                        AND product_post.post_author = $user_ID
                        AND order_post.post_type = 'shop_order'
                        {$range_query}
                        ORDER BY order_post.ID DESC";

    $orders = $wpdb->get_results( $query, OBJECT);
    $need_orders = array();
    foreach ($orders as $seller_order) {
        $need_orders[$seller_order->order_id][$seller_order->product_id] = $seller_order;
    }
    return $need_orders;
}
function et_mysql_date_format($date){
    $date = new DateTime($date);
    return $date->format('Y-m-d H:i:s');
}
function cem_render_item_status_select($item_id)
{
    $statusList = cem_get_order_item_statues();
    $current_status = wc_get_order_item_meta($item_id, 'status', true);
    echo '<select name="status[' . $item_id . ']">';
    foreach ($statusList as $key => $status) {
        echo '<option value="' . $key . '" ' . ($key == $current_status ? 'selected' : '') . '>' . $status . '</option>';
    }
    echo '</select>';
}

function cem_get_lock_status()
{
    $status = array(
        'cancelled',
        'refunded',
        'failed',
        'completed'

    );
    return apply_filters('cem_get_lock_status', $status);
}