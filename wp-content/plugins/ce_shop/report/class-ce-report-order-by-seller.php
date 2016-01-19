<?php
/**
 * WC_Report_Sales_By_Date
 *
 * @author      WooThemes
 * @category    Admin
 * @package     WooCommerce/Admin/Reports
 * @version     2.1.0
 */
if(class_exists('WooCommerce')):
require_once get_template_directory() . '/includes/wc_integrate/report/WC_Admin_Report_Integrate.php';
class CE_Report_Report_By_Seller extends WC_Admin_Report_Integrate {

	public $chart_colours = array();

	/**
	 * Get the legend for the main chart sidebar
	 * @return array
	 */
	public function get_chart_legend() {
		$legend   = array();

		$total_sales = $this->get_order_report_data( array(
			'data' => array(
				'et_order_total' => array(
					'type'     => 'meta',
					'function' => 'SUM',
					'name'     => 'total_sales'
				)
			),
			'query_type'   => 'get_var',
			'order_types'  => array('order'),
			'order_status' => array('publish'),
			'filter_range' => true
		) );


		$total_pending = $this->get_order_report_data( array(
			'data' => array(
				'et_order_total' => array(
					'type'     => 'meta',
					'function' => 'SUM',
					'name'     => 'total_sales'
				),
				'ID' => array(
					'type' => 'post_data',
					'function' => 'COUNT',
					'name' => 'total_orders'
				),
			),
			'query_type' => 'get_row',
			'order_types'  => array('order'),
			'order_status' => array('pending'),
			'filter_range' => true
		) );

		$total_draft = $this->get_order_report_data( array(
			'data' => array(
				'et_order_total' => array(
					'type'     => 'meta',
					'function' => 'SUM',
					'name'     => 'total_sales'
				),
				'ID' => array(
					'type' => 'post_data',
					'function' => 'COUNT',
					'name' => 'total_orders'
				),
			),
			'query_type' => 'get_row',
			'order_types'  => array('order'),
			'order_status' => array('draft'),
			'filter_range' => true
		) );

		$total_paid = absint($this->get_order_report_data(array(
			'data' => array(
				'ID' => array(
					'type'     => 'post_data',
					'function' => 'COUNT',
					'name'     => 'total_orders'
				)
			),
			'query_type'   => 'get_var',
			'filter_range' => true,
			'order_types'  => array('order'),
			'order_status' => array('publish'),
		) ) );

		$this->average_sales = $total_sales / ( $this->chart_interval + 1 );

		switch ( $this->chart_groupby ) {

			case 'day' :
				$average_sales_title = sprintf( __( '%s average daily sales', 'woocommerce' ), '<strong>' . wc_price( $this->average_sales ) . '</strong>' );
			break;

			case 'month' :
				$average_sales_title = sprintf( __( '%s average monthly sales', 'woocommerce' ), '<strong>' . wc_price( $this->average_sales ) . '</strong>' );
			break;
		}

		$legend[] = array(
			'title' => sprintf( __( '%s sales in this period', 'woocommerce' ), '<strong>' . wc_price( $total_sales ) . '</strong>' ),
			'color' => $this->chart_colours['sales_amount'],
			'highlight_series' => 4
		);

		$legend[] = array(
			'title' => sprintf(__('%s Pending sales', 'woocommerce'), '<strong>' . wc_price($total_pending->total_sales) . '</strong>'),
			'color' => $this->chart_colours['sales_pending'],
			'highlight_series' => 6
		);

		$legend[] = array(
			'title' => sprintf(__('%s draft sales', 'woocommerce'), '<strong>' . wc_price($total_draft->total_sales) . '</strong>'),
			'color' => $this->chart_colours['sales_draft'],
			'highlight_series' => 5
		);

		$legend[] = array(
			'title' => $average_sales_title,
			'color' => $this->chart_colours['average'],
			'highlight_series' => 3
		);

		$legend[] = array(
			'title' => sprintf(__('%s paid orders', 'woocommerce'), '<strong>' . $total_paid . '</strong>'),
			'color' => $this->chart_colours['order_count'],
			'highlight_series' => 0
		);

		$legend[] = array(
			'title' => sprintf(__('%s pending orders', 'woocommerce'), '<strong>' . $total_pending->total_orders . '</strong>'),
			'color' => $this->chart_colours['order_pending'],
			'highlight_series' => 2
		);


		$legend[] = array(
			'title' => sprintf(__('%s draft orders', 'woocommerce'), '<strong>' . $total_draft->total_orders . '</strong>'),
			'color' => $this->chart_colours['order_draft'],
			'highlight_series' => 1
		);

		return $legend;
	}

	/**
	 * Output the report
	 */
	public function output_report() {

		$ranges = array(
			'year'         => __( 'Year', 'woocommerce' ),
			'last_month'   => __( 'Last Month', 'woocommerce' ),
			'month'        => __( 'This Month', 'woocommerce' ),
			'7day'         => __( 'Last 7 Days', 'woocommerce' )
		);

		$this->chart_colours = array(
			'sales_amount' => '#DE5500',
			'average'      => '#75b9e7',
			'order_count' => '#6C2D58',
			'item_count'   => '#d4d9dc',

			'sales_draft' => '#3498db',

			'order_draft' => '#F6B17F',
			'order_pending' => '#40B8AF',

			'sales_pending'=> '#00AC6B',
		);

		$current_range = ! empty( $_GET['range'] ) ? sanitize_text_field( $_GET['range'] ) : '7day';

		if ( ! in_array( $current_range, array( 'custom', 'year', 'last_month', 'month', '7day' ) ) ) {
			$current_range = '7day';
		}

		$this->calculate_current_range( $current_range );

		include( WC()->plugin_path() . '/includes/admin/views/html-report-by-date.php');
	}

	/**
	 * Output an export link
	 */
	public function get_export_button() {

		$current_range = ! empty( $_GET['range'] ) ? sanitize_text_field( $_GET['range'] ) : '7day';
		?>
		<a
			href="#"
			download="report-<?php echo esc_attr( $current_range ); ?>-<?php echo date_i18n( 'Y-m-d', current_time('timestamp') ); ?>.csv"
			class="export_csv"
			data-export="chart"
			data-xaxes="<?php _e( 'Date', 'woocommerce' ); ?>"
			data-groupby="<?php echo $this->chart_groupby; ?>"
		>
			<?php _e( 'Export CSV', 'woocommerce' ); ?>
		</a>
		<?php
	}

	/**
	 * Get the main chart
	 *
	 * @return string
	 */
	public function get_main_chart() {
		global $wp_locale;

		// Get orders and dates in range - we want the SUM of order totals, COUNT of order items, COUNT of orders, and the date
		$orders = $this->get_order_report_data( array(
			'data' => array(
				'et_order_total' => array(
					'type'     => 'meta',
					'function' => 'SUM',
					'name'     => 'total_sales'
				),
				'ID' => array(
					'type'     => 'post_data',
					'function' => 'COUNT',
					'name'     => 'total_orders',
					'distinct' => true,
				),
				'post_date' => array(
					'type'     => 'post_data',
					'function' => '',
					'name'     => 'post_date'
				),
			),
			'group_by'     => $this->group_by_query,
			'order_by'     => 'post_date ASC',
			'query_type'   => 'get_results',
			'filter_range' => true,
			'order_types'  => array('order'),
			'order_status' => array('publish'),
		) );

		$draft_orders = $this->get_order_report_data( array(
			'data' => array(
				'et_order_total' => array(
					'type'     => 'meta',
					'function' => 'SUM',
					'name'     => 'total_draft_sales'
				),
				'ID' => array(
					'type' => 'post_data',
					'function' => 'COUNT',
					'name' => 'total_orders',
					'distinct' => true,
				),
				'post_date' => array(
					'type'     => 'post_data',
					'function' => '',
					'name'     => 'post_date'
				),
			),
			'group_by'     => $this->group_by_query,
			'order_by'     => 'post_date ASC',
			'query_type'   => 'get_results',
			'filter_range' => true,
			'order_types'  => array('order'),
			'order_status' => array('draft'),
		) );

		$pending_orders = $this->get_order_report_data( array(
			'data' => array(
				'et_order_total' => array(
					'type'     => 'meta',
					'function' => 'SUM',
					'name'     => 'total_pending_sales'
				),
				'ID' => array(
					'type' => 'post_data',
					'function' => 'COUNT',
					'name' => 'total_orders',
					'distinct' => true,
				),
				'post_date' => array(
					'type'     => 'post_data',
					'function' => '',
					'name'     => 'post_date'
				),
			),
			'group_by'     => $this->group_by_query,
			'order_by'     => 'post_date ASC',
			'query_type'   => 'get_results',
			'filter_range' => true,
			'order_types'  => array('order'),
			'order_status' => array('pending'),
		) );

		// Prepare data for report
		$order_counts      = $this->prepare_chart_data( $orders, 'post_date', 'total_orders', $this->chart_interval, $this->start_date, $this->chart_groupby );
		$order_amounts     = $this->prepare_chart_data( $orders, 'post_date', 'total_sales', $this->chart_interval, $this->start_date, $this->chart_groupby );

		$draft_counts = $this->prepare_chart_data($draft_orders, 'post_date', 'total_orders', $this->chart_interval, $this->start_date, $this->chart_groupby);
		$draft_amounts    = $this->prepare_chart_data( $draft_orders, 'post_date', 'total_draft_sales', $this->chart_interval, $this->start_date, $this->chart_groupby );

		$pending_counts = $this->prepare_chart_data($pending_orders, 'post_date', 'total_orders', $this->chart_interval, $this->start_date, $this->chart_groupby);
		$pending_amounts    = $this->prepare_chart_data( $pending_orders, 'post_date', 'total_pending_sales', $this->chart_interval, $this->start_date, $this->chart_groupby );
		// Encode in json format
		$chart_data = json_encode( array(
			'order_counts'      => array_values( $order_counts ),
			'order_amounts'     => array_values( $order_amounts ),

			'draft_counts' => array_values($draft_counts),
			'draft_amounts'     => array_values( $draft_amounts ),

			'pending_amounts'     => array_values( $pending_amounts ),
			'pending_counts' => array_values($pending_counts),
		) );
		?>
		<div class="chart-container">
			<div class="chart-placeholder main"></div>
		</div>
		<script type="text/javascript">

			var main_chart;

			jQuery(function(){
				var order_data = jQuery.parseJSON( '<?php echo $chart_data; ?>' );
				var drawGraph = function( highlight ) {
					var series = [
						{//0
							label: "<?php echo esc_js( __( 'Paid orders', 'woocommerce' ) ) ?>",
							data: order_data.order_counts,
							color: '<?php echo $this->chart_colours['order_count']; ?>',
							bars: {
								fillColor: '<?php echo $this->chart_colours['order_count']; ?>',
								fill: true,
								show: true,
								lineWidth: 0,
								barWidth: <?php echo $this->barwidth; ?> * 0.5, align: 'center'
						},
						shadowSize :0,
						stack:true,
						enable_tooltip:true,
						prepend_label:true,
						hoverable:true,
					},
					{//1
						label: "<?php echo esc_js( __( 'Draft Order ', 'woocommerce' ) ) ?>",
						data: order_data.draft_counts,
						color:'<?php echo $this->chart_colours['order_draft']; ?>',
						shadowSize:0,
						enable_tooltip:true,
						prepend_label:true,
						hoverable:true,
						stack:true,
						bars:
							{
								fillColor: '<?php echo $this->chart_colours['order_draft']; ?>',
								fill:true,
								show:true,
								lineWidth:0,
								barWidth: <?php echo $this->barwidth; ?> *0.5,
								align:'center'
							}
					}, {//2
						label: "<?php echo esc_js( __( 'Pending Order ', 'woocommerce' ) ) ?>",
						data: order_data.pending_counts,
						color: '<?php echo $this->chart_colours['order_pending']; ?>',
						shadowSize: 0,
						enable_tooltip: true,
						prepend_label: true,
						hoverable: true,
						stack: true,
						bars: {
							fillColor: '<?php echo $this->chart_colours['order_pending']; ?>',
							fill: true,
							show: true,
							lineWidth: 0,
							barWidth: <?php echo $this->barwidth; ?> * 0.5, align: 'center'
						}
					},
					{//3
						label: "<?php echo esc_js( __( 'Average sales amount', 'woocommerce' ) ) ?>",
						data: [ [ <?php echo min( array_keys( $order_amounts ) ); ?>, <?php echo $this->average_sales; ?> ], [ <?php echo max( array_keys( $order_amounts ) ); ?>, <?php echo $this->average_sales; ?> ] ],
						yaxis: 2,
						color: '<?php echo $this->chart_colours['average']; ?>',
						points: { show: false },
						lines: { show: true, lineWidth: 2, fill: false },
						shadowSize: 0,
						hoverable: true
					},
					{//4
						label: "<?php echo esc_js( __( 'Sales amount', 'woocommerce' ) ) ?>",
						data: order_data.order_amounts,
						yaxis: 2,
						color: '<?php echo $this->chart_colours['sales_amount']; ?>',
						points: { show: true, radius: 5, lineWidth: 3, fillColor: '#fff', fill: true },
						lines: { show: true, lineWidth: 4, fill: false },
						shadowSize: 0,
						hoverable: true,
						<?php echo $this->get_currency_tooltip(); ?>
					},
					{//5
						label: "<?php echo esc_js( __( 'Draft sales amount', 'woocommerce' ) ) ?>",
						data: order_data.draft_amounts,
						yaxis: 2,
						color: '<?php echo $this->chart_colours['sales_draft']; ?>',
						points: { show: true, radius: 5, lineWidth: 3, fillColor: '#fff', fill: true },
						lines: { show: true, lineWidth: 4, fill: false },
						shadowSize: 0,
						hoverable: true,
						<?php echo $this->get_currency_tooltip(); ?>
					},
					{//6
						label: "<?php echo esc_js( __( 'Pending sales amount', 'woocommerce' ) ) ?>",
						data: order_data.pending_amounts,
						yaxis: 2,
						color: '<?php echo $this->chart_colours['sales_pending']; ?>',
						points: { show: true, radius: 5, lineWidth: 3, fillColor: '#fff', fill: true },
						lines: { show: true, lineWidth: 4, fill: false },
						shadowSize: 0,
						hoverable: true,
						<?php echo $this->get_currency_tooltip(); ?>
					},
					];

					if ( highlight !== 'undefined' && series[ highlight ] ) {
						highlight_series = series[ highlight ];

						highlight_series.color = '#A31E39';

						if ( highlight_series.bars )
							highlight_series.bars.fillColor = '#A31E39';

						if ( highlight_series.lines ) {
							highlight_series.lines.lineWidth = 5;
						}
					}

					main_chart = jQuery.plot(
						jQuery('.chart-placeholder.main'),
						series,
						{
							legend: {
								show: false
							},
							grid: {
								color: '#aaa',
								borderColor: 'transparent',
								borderWidth: 0,
								hoverable: true
							},
							xaxes: [ {
								color: '#aaa',
								position: "bottom",
								tickColor: 'transparent',
								mode: "time",
								timeformat: "<?php if ( $this->chart_groupby == 'day' ) echo '%d %b'; else echo '%b'; ?>",
								monthNames: <?php echo json_encode( array_values( $wp_locale->month_abbrev ) ) ?>,
								tickLength: 1,
								minTickSize: [1, "<?php echo $this->chart_groupby; ?>"],
								font: {
									color: "#aaa"
								}
							} ],
							yaxes: [
								{
									min: 0,
									minTickSize: 1,
									tickDecimals: 0,
									color: '#d4d9dc',
									font: { color: "#aaa" }
								},
								{
									position: "right",
									min: 0,
									tickDecimals: 2,
									alignTicksWithAxis: 1,
									color: 'transparent',
									font: { color: "#aaa" }
								}
							],
						}
					);

					jQuery('.chart-placeholder').resize();
				}

				drawGraph();

				jQuery('.highlight_series').hover(
					function() {
						drawGraph( jQuery(this).data('series') );
					},
					function() {
						drawGraph();
					}
				);
			});
		</script>
		<?php
	}
}
endif;