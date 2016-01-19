/**
 * This file adds some LIVE to the Theme Customizer live preview. To leverage
 * this, set your custom settings to 'postMessage' and then add your handling
 * here. Your javascript should grab settings from customizer controls, and
 * then make any necessary changes to the page using jQuery.
 */
(function($) {
$(document).ready(function() {
	// console.log('customizer init');
	//Update site link color in real time...
	wp.customize('header_bg_color', function(value) {
		value.bind(function(newval) {
			console.log(newval);
			if(newval == false){
				newval = "#fff";
			}
			$('#menu-top').css('background-color', newval);
			customizer['header'] = newval;
			less.refresh();
		});
	});
	/**
	 * update background
	*/
	wp.customize('body_bg_color', function(value) {
		value.bind(function(newval) {
			if(newval == false){
				newval = "#ecf0f1";
			}
			$('body').css('background-color', newval);
			customizer['background'] = newval;
			less.refresh();
		});
	});
	/**
	 * update footer background
	*/
	wp.customize('footer_bg_color', function(value) {
		value.bind(function(newval) {
			if(newval == false){
				newval = "#34495e";
			}
			$('footer').css('background-color', newval);
			customizer['footer'] = newval;
			less.refresh();
		});
	});
	/**
	 * copy right area
	*/
	wp.customize('btm_footer_color', function(value) {
		value.bind(function(newval) {
			if(newval == false){
				newval = "#2c3e50";
			}
			$('.copyright-wrapper').css('background-color', newval);
			customizer['footer_bottom'] = newval;
			less.refresh();
		});
	});
	/**
	 * main color
	*/
	wp.customize('main_color', function(value) {
		value.bind(function(newval) {
			if(newval == false){
				newval = "#1b83d3";
			}
			customizer['action_1'] = newval;
			$('.list-option-filter .icon-list-view .icon-view.active i, .list-option-filter .icon-list-view .icon-view:hover i, .list-option-filter .sort-rates-lastest a.sort-icon.active, .list-option-filter .sort-rates-lastest a.sort-icon:hover, a:hover, a:focus').css('color', newval);
			$('.step-content-wrapper .list-price li .btn.btn-submit-price-plan, .btn.btn-submit-login-form, .btn-post-place, ul.top-menu-right > li.top-user:hover, ul.top-menu-right > li.top-user.open, ul.top-menu-right li.top-search:hover, ul.top-menu-right li.top-search.active, ul.top-menu li .menu-btn:hover, .menu-btn.gn-icon-menu.gn-selected, .mask-color, .slider-selection, .option-search.right input[type="submit"]').css('background-color', newval);
			$('.option-search.right input[type="submit"]').css('color', '#fff');
			less.refresh();
		});
	});
});
})(jQuery);