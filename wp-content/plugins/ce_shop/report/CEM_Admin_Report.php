<?php
/**
 * Project : classifiedengine
 * User: thuytien
 * Date: 11/27/2014
 * Time: 8:55 AM
 */
if(class_exists('WooCommerce')):
class CEM_Admin_Report {

    public static function get_reports($reports) {
        $ce_reports = array(
            'order_by_seller'     => array(
                'title'  => __( 'Seller', 'woocommerce' ),
                'reports' => array(
                    "sales_by_date"    => array(
                        'title'       => __( 'Sales by date', 'woocommerce' ),
                        'description' => '',
                        'hide_title'  => true,
                        'callback'    => array( __CLASS__, 'get_report' )
                    )
                )
            )
        );

        return array_merge($ce_reports, $reports);
    }

    public static function get_report( $name ) {
        $name  = sanitize_title( str_replace( '_', '-', $name ) );
        $class = 'CE_Report_' . str_replace( '-', '_', $name );
        $filePath = apply_filters( 'ce_admin_reports_path', dirname(__FILE__).'/class-ce-report-' . $name . '.php', $name, $class );

        if(!file_exists($filePath)){
            return;
        }

        include_once( $filePath );

        if ( ! class_exists( $class ) )
            return;

        $report = new $class();
        $report->output_report();
    }
}
endif;