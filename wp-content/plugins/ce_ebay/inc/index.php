<?php
//error_reporting(E_ALL);  // turn on all errors, warnings and notices for easier debugging
require_once('ce_base.php');
require_once('ce_ebay_api.php');
require_once('ce_ebay_ajax.php');  // functions to aid with display of information


add_filter('post_type_link','ce_the_permalink_ad',10,2);
function ce_the_permalink_ad($link,$post){
	if($post->post_type == @CE_AD_POSTTYPE){
	$url = get_post_meta($post->ID,'ce_ebay_url',true);
	if(!empty($url))
		return $url;
	}
	return $link;
}
function ce_upload_attachment($filename,$post_id){
	$wp_filetype = wp_check_filetype(basename($filename), null );
 	$wp_upload_dir = wp_upload_dir();
  	$attachment = array(
     'guid' => $filename, 
     'post_mime_type' => $wp_filetype['type'],
     'post_title' => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
     'post_content' => '',
     'post_status' => 'inherit'
  	);
  	$attach_id = wp_insert_attachment( $attachment, $filename, $post_id );
  	wp_update_post(array('ID'=>$attach_id,'post_title'=>'123','guid'=>$filename));
  	return $attach_id;

}
//return apply_filters( 'post_thumbnail_html', $html, $post_id, $post_thumbnail_id, $size, $attr );
add_filter('post_thumbnail_html','ce_ebay_thumbnail',11,5);
function ce_ebay_thumbnail($html, $post_id, $post_thumbnail_id, $size, $attr){
	$url_out_thumb = get_post_meta($post_id,'ce_out_thumb',true);
	if( !empty($url_out_thumb) ) {
		return '<img title="'.get_the_title($post_id).'" alt="'.get_the_title($post_id).'" class="attachment-ad-thumbnail wp-post-image ce-out-thumb" src="'.$url_out_thumb.'" />';
	}
	return $html;
}
//// assign template for front-end display

add_filter('ce_template_publish_ad','ebay_template_publish_ad',10,2);
function ebay_template_publish_ad($html,$ad){
	global $post;
	$record 	= get_post_meta($post->ID,'ce_ebay_item_id',true);
	$currency 	= get_post_meta($post->ID,'et_currency',true);
	$current 	= array(
		'EUR' 	=> '&euro;',
		'USD' 	=> '$',
		'CAD' 	=> 'CA $',
		'GBP' 	=> '&pound;',
		'SGD' 	=> 'S$',
		'PHP' 	=> '&#8369;',
		'AUD' 	=> 'AU $',
		'PLN' 	=> 'zł',
		'CHF' 	=> 'CHF',
		'INR' 	=> 'INR',
		'MYR' 	=> 'RM'
		);
	$icon = isset($current[$currency]) ? $current[$currency] : $currency;

	if($record){
		$html .='<div class="item-product col-md-4">';

		$html .=' <p class="img thumbnail-ebay"><a target = "_blank" href="'.get_permalink().'">'. $ad->the_post_thumbnail.'</a></p>';
		$html .='<p class="intro-product"><a target = "_blank" href="'.get_permalink().'" title="'.sprintf(__("Views %s", ET_DOMAIN), get_the_title()).'">';
      	$html .='<span class="title">'.get_the_title().'</span><br/>';
     	if(isset($ad->location[0])) {
        	$html .='<span class="name">'.$ad->location[0]->name.'</span>';
      	}
      	$price_key = CE_ET_PRICE;
      	$html .='<span class="price">'.ce_ebay_get_price_format($ad->$price_key, '' ,$icon).'</span>';

    	$html .='</a></p>';

		$html .= '</p></div>';

	}
	return $html;
}

/*
Add template id for item import. It use to check templase when scroll insert ad.
*/
add_filter('ce_convert_ad','ce_convert_ad');
function ce_convert_ad($result){

	$ebay_item  = get_post_meta($result->ID,'ce_ebay_item_id',true);
	if($ebay_item){
		$result->template_id ='ebay';
	}

	return $result;
}
add_action('wp_footer','ebay_footer', 12);
function ebay_footer(){	?>
	<script type="text/template" id="ad-item-template_ebay">
        <p class="img thumbnail-ebay">
           	<a target="_blank" href="<?php echo "{{ guid }}"; ?>">
           		<?php echo " <# if( parseInt(et_featured) == 1 ) { #> "; ?>
           			<span class="icon-featured"><?php __("Featured", ET_DOMAIN) ?></span>
           		<?php echo " <# } #>  "; ?>
			    <span class="shadown-img"><img src="<?php echo TEMPLATEURL; ?>/img/shadown-black.png"></span>
           		{{ the_post_thumbnail }}
	           </a>
	    </p>
	    <div class="intro-product">
	        <a href="{{ guid }}">
	            <span class="title">{{ post_title }}</span>
	            <p>
	                <span class="name"> <?php echo "<# if( typeof location[0] !== 'undefined' ) { #> {{ location[0].name }} <# } #>" ; ?></span>
	                <span class="price">{{ price }}</span>
	            </p>
	        </a>
	        <div class="description" >
	            {{ post_excerpt }}
	        </div>
	    </div>
        <!--/span-->
	</script>
	<?php

	/**
	 * js template for relate list in single ad
	*/
	if(is_singular( CE_AD_POSTTYPE ) ) {
	?>
		<script type="text/template" id="ce-single-related_ebay">
			<div class="item-product ad-carousel related-classified">
			    <?php echo " <# if( parseInt(et_featured) ) { #>"; ?>
			        <span class="icon-featured"><?php __("Featured", ET_DOMAIN) ?></span>
			    <?php echo "<# } #>"; ?>
			    <p class="img">
			        <a href="{{ permalink }}" target="_blank">
			        	<span class="shadown-img"><img src="<?php echo TEMPLATEURL; ?>/img/shadown-black.png"></span>
			            {{
			                the_post_thumbnail
			            }}
			        </a>
			    </p>
			    <!-- ad details -->
			    <p class="intro-product">
	            	<a href="{{ permalink }}"  target="_blank" >
		              	<span class="title">{{ post_title }}</span>
		              	<span class="name"> <?php echo "<# if( typeof location[0] !== 'undefined' ) { #> {{ location[0].name }} <# } #>" ; ?></span>
		              	<span class="price">{{ price }}</span>
	              	</a>
	            </p>
			</div><!--/span-->
		</script>
	<?php
	}


}
function ce_ebay_get_price_format( $amount , $style = '',$currency){

	$option 		= new CE_Options();
	$align			= $option->get_option('et_currency_align');
	$price_format 	= $option->get_option('et_currency_format');
	$format = '%1$s';
	switch ($style) {
		case 'sup':
			$format = '<sup>%s</sup>';
			break;

		case 'sub':
			$format = '<sub>%s</sub>';
			break;
		default:
			$format = '%s';
			break;
	}
	$decimal		=	get_theme_mod( 'et_decimal' , 2 );
	$decimal_point 	= 	($price_format != 2) ? "." : ",";
	$thousand_sep 	= 	($price_format != 2) ? "," : ".";

	if ( $align != "right") {
		$format = $format . '%s';
		return sprintf( $format, $currency, number_format( (double)$amount, $decimal , $decimal_point , $thousand_sep) );
	}
	else {
		$format = '%s' . $format;
		return sprintf( $format, number_format((double)$amount, $decimal , $decimal_point , $thousand_sep ),  $currency );
	}


}


?>