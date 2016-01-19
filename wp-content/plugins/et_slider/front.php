<?php

class ET_Slider_Front{
	function __construct(){
		//$this->add_script();
	}
	public static function add_script(){
		wp_enqueue_script('et_slider_plugins',plugin_dir_url( __FILE__).'js/plugins.js' );
		wp_enqueue_style('et_style_custom' ,plugin_dir_url( __FILE__).'css/et-slider.css');
	}

	public static function et_slider_display($slider_id,  $container , $speed, $hide_text, $height){
		self::add_script();
		$cslide = !empty($container) ? $container : 'myCarousel';
		$pex = get_post($slider_id);
		if(!$pex)
			$slider_id = -1;
	  	$args = array(
	  		'post_type' 	=> 'et_slider',
	  		'numberposts' 	=> -1,
		  	'orderby'		=> 'menu_order date',
		    'order'			=> 'DESC',
		    'post_status'	=> 'publish',
		    'post_parent' 	=> $slider_id
		 );


		$et_content = '';
		$posts = new WP_Query($args);
		if( $posts->have_posts() ){

			$et_content .='<div class="slide-homepage" style="display : none;">
				<div id="'. $cslide.'" class="'. $cslide.' carousel slide">
				    <ol class="carousel-indicators">';

					    for($i = 0; $i < $posts->post_count ; $i++){
					    	$class = ($i==0) ? 'active' : '';
						    $et_content.= '<li data-target="#'.$cslide.'" data-slide-to="'.$i.'" class="'.$class.'"></li>';
						}

				$et_content .='</ol>
					<div class="caption-block"></div>
				 	<div class="carousel-inner">';

				    $j=0;
					while($posts->have_posts()){
						global $post;
						$posts->the_post();
						$et_link	= get_post_meta(get_the_ID(),'et_link',true);
						$att_id 	= get_post_meta(get_the_ID(),'_thumbnail_id',true);
						$read_more 	= get_post_meta(get_the_ID(),'read_more',true);
						if(empty($read_more))
							$read_more = __('Read more',ET_DOMAIN);

						$att_url 	= wp_get_attachment_image_src($att_id,'full');
						$url_thumb 	= is_array($att_url) ? $att_url[0] : '';

						$class = ($j==0) ? 'active' : '';

					 	$et_content .= '<div class="'.$class.' item">';
					 	$et_title  	= break_text($post->post_title,27);
					 	$et_des 	= get_the_content();

					    if($hide_text == false) {
							$et_content .= '<div class="carousel-caption">
								<h4 class="title-slider"><a href ="'.esc_url($et_link).'">'.$et_title.' </a> </h4>';
								$et_content .='<div class= "et-slider-des">'.$et_des.'</div>
								<div class="btn-read-more"><a href="'.esc_url($et_link).'">'.$read_more.'<span class="icon" data-icon="]"></span></a></div>
							</div>';
						 }

						$et_content.=' <div class="carousel-thumb">';
							if(!empty($url_thumb))
								$et_content.= '<img alt= "'.$post->post_title.'" title="'.$post->post_title.'" align ="middle" src="'.$url_thumb.'" >';
						$et_content.='</div>
					    </div>';
					    $j++;
					}
					wp_reset_query();
					//$thumb_h = $height-
					$line_height = $height - 4;
					$text_height = $height - 152;
					$et_content .='</div>
				</div>
			</div>';
			$et_content .="
			<script type='text/javascript'>
				(function($){
					$(document).ready(function($){
						$('.slide-homepage').show();
						$('#".$cslide."').carousel({
						  interval: ".$speed."
						});
					});

				})(jQuery);

			</script>
			<style type='text/css'>
			.".$cslide."{
				min-height:".$height."px;
				overflow: hidden;
			}
			.second-column #".$cslide."{
				min-height:0px;

			}
			.".$cslide." .carousel-inner .item{

				overflow: hidden;
			}
			.".$cslide." .carousel-thumb{
				height:".$height."px;
				overflow: hidden;
				line-height:".$line_height."px;
			}
			.".$cslide." .carousel-inner {

			}

			.".$cslide." .et-slider-des{
				max-height: 143px;
				overflow:hidden;
				font-weight:bold;

			}
			body.single #".$cslide." .et-slider-des{
				max-height:1000px;
				overflow:hidden;
			}
			body.single #".$cslide." .carousel-inner .btn-read-more{ display:none;}
			.".$cslide." .carousel-caption{
				min-height:".$height."px;
			}
			.second-column #".$cslide." {
				padding-top:0;
			}

			</style>";

		}else{
			$et_content .= 'There are no images in your slide.';
		}
		return $et_content;


	}
	public static function et_slider_shortcode($args){

		extract( shortcode_atts( array(
			'id' 		=> '',
			'speed' 	=> 5000,
			'container' => 'myCarousel'
			), $args ) );

		$default  		=  array(
			'height' 	=> '200',
			'speed'  	=> 5000,
			'container' => 'myCarousel_'.$args['id'].'_'.mt_rand(1,9),
			'id'    	=> '');
		$args = wp_parse_args( $args, $default );

		//$div = 'myCarousel_'.$args['id'];
		$slider =  self::et_slider_display($args['id'], $args['container'], $args['speed'], false, $args['height']);
		return $slider;
	}

}

add_filter('widget_text', 'do_shortcode', 11);
add_shortcode('et_slider', array( 'ET_Slider_Front', 'et_slider_shortcode' ) );
function etdo_shortcode(){
	return do_shortcode('[et_slider]');
}

function et_slider_do_shortcode(){
	//echo do_shortcode('[et_slider]');
}

function new_excerpt_more( $excerpt ) {
	global $post;
	return str_replace( '[...]', '', $excerpt );
}
add_filter( 'wp_trim_excerpt', 'new_excerpt_more' );
function custom_excerpt_length( $length ) {
	return 34;
}
add_filter( 'excerpt_length', 'custom_excerpt_length', 999 );

//add_filter("the_content", "break_text");
function break_text($text,$length){

    $visible = wp_trim_words($text,$length);
    return balanceTags($visible) . "";
}
add_filter('the_content', 'trim_content');

function trim_content($content){

    if(is_singular('et_slider')){
        //use your own trick to get the first 50 words. I'm getting the first 100 characters just to show an example.
        $content = (strlen($content) <= 255)? $content : wp_html_excerpt($content, 255);
    }
    return $content;
}