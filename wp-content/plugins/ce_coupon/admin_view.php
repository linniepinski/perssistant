<?php
if(class_exists('ET_AdminMenuItem')) {

	Class CE_Coupon_Menu extends ET_AdminMenuItem{
		const CE_COUPON_SLUG ='ce-coupon';
		function __construct($args = array()){

			 parent::__construct(self::CE_COUPON_SLUG,  array(
	            'menu_title'    => __('CE Coupon', ET_DOMAIN),
	            'page_title'    => __('CE COUPON', ET_DOMAIN),
	            'callback'      => array($this, 'menu_view'),
	            'slug'          => self::CE_COUPON_SLUG,
	            'page_subtitle' => __('CE Coupon', ET_DOMAIN),
	            'pos'           => 59,
	            'icon_class'    => 'icon-menu-overview'
	        ));

		}

		/**
		 * view in backend this plugin.
		 */

		function menu_view($args){ ?>
			<div class="et-main-header">
	    		<div class="title font-quicksand"><?php _e("CE COUPON", ET_DOMAIN); ?></div>
	    		<div class="desc"><?php _e("Create and manage your own discount promotions.", ET_DOMAIN); ?></div>
	    	</div>
	    	<div class="et-main-content" id="coupon-manage">
	    		<div class="et-main-main one-column">
	    		<?php
	    		require_once 'coupon-list.php';
	    		require_once 'coupon-add-new.php';
	    		?>
	    		</div>
	    	</div>
		    <?php

		}
		function on_add_styles(){
			wp_enqueue_style('admin.css');
			wp_enqueue_style('roll-style',plugin_dir_url(__FILE__).'/css/ce-coupon.css');
		}
		/**
		 *  add script for backend this page.
		 */

		function on_add_scripts(){
			wp_enqueue_script('jquery');
			wp_enqueue_script('underscore');
			wp_enqueue_script('backbone');
			wp_enqueue_script( 'ce' );
			wp_enqueue_script( 'jquery-ui-autocomplete' );
			wp_enqueue_script( 'admin_scripts' );
			wp_enqueue_script( 'jquery.validator' );
			wp_enqueue_script('jquery-ui-datepicker');
			wp_enqueue_script( 'ce_coupon', plugin_dir_url(__FILE__).'/js/ce-coupon.js',array('jquery', 'underscore', 'backbone', 'ce')  );
		}
	}
	//new CE_Coupon_Menu();
}


?>