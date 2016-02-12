<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function get_profiles_price_range() {
    global $wpdb;
    $price_range_args = $wpdb->get_results(
            "SELECT "
            . "MIN( CAST( meta_value AS UNSIGNED ) ) AS min_price, "
            . "MAX( CAST( meta_value AS UNSIGNED ) ) AS max_price "
            . "FROM wp_postmeta "
            . "WHERE meta_key = 'hour_rate'");
    return $price_range_args[0];
}

function get_project_price_range() {
    global $wpdb;
    $price_range_args = $wpdb->get_results(
            "SELECT "
            . "MIN(CAST( wp_postmeta.meta_value AS UNSIGNED )) AS min_price, "
            . "MAX(CAST( wp_postmeta.meta_value AS UNSIGNED )) AS max_price "
            . "FROM wp_posts "
            . "INNER JOIN wp_postmeta "
            . "ON wp_posts.ID = wp_postmeta.post_id "
            . "WHERE wp_posts.post_status = 'publish' "
            . "AND wp_postmeta.meta_key = 'et_budget'");
    return $price_range_args[0];
}
