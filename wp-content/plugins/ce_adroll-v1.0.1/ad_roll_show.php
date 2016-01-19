<?php 
$default	= array('number' => 5,'bgcolor'=>'d9d9d9','title' => sprintf(__('Ads from %s',ET_DOMAIN),get_bloginfo("name")) );
$arg 		= wp_parse_args($_REQUEST,$default);

extract($arg);

$height = $number*78 + 79;
$option = new CE_Options();
$customize		=	$option->get_customization ();
global $et_global;
if(!isset($customize['action_2']) || empty($customize['action_2']) ){
	$customize['action_2'] = '#3783C4';
}
?>
<html>
<head>


<link rel="stylesheet" href="<?php echo CE_ROLL_URL;?>/css/demo.css" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php echo CE_ROLL_URL;?>/css/flexslider.css" type="text/css" media="screen" />

<style type="text/css">
	body{
		background: #<?php echo $bgcolor;?>;
		max-width: 100%;
		font-family: Open Sans,Arial,Helvetica,sans-serif;
		overflow: hidden;;
	}
	.textwidget{
		overflow: hidden;
		max-width: 100%;

	}
	.wrap-roll{
		padding: 10px 8px;		
		display: block;
		background: #<?php echo $bgcolor;?>;		
		min-height:<?php //echo $height;?>px; 
		
	}
	.caroufredsel_wrapper	
	{
		min-height: <?php echo $height+20;?>px;		
		
	}
	.item-slide,
	.list-adroll{
		min-height: <?php echo $height;?>px;		
	}
	.list-adroll {
		padding-left: 0;		
		text-decoration: none;
		padding-top: 10px;
		margin-top: 0;
		overflow: hidden;
		
		position: relative;
	}

	.list-adroll .item-slide{
		list-style: none;
		padding:5px 1px  5px 0;
		
		width: 232px;
		
	}
	.list-adroll .item-slide p{
		margin: 0;
		line-height: 16px;
	}
	.list-adroll .item-slide .item-roll{
		display: block;
		clear: both;
		padding: 5px 0;
		overflow: hidden;
		height: 64px;
	}
	.list-adroll .item-slide a{
		color: <?php echo $customize['action_2'];?>;
		font-family: Helvetica,san-serif;
		font-size: 12px;
		text-decoration: none;
		font-weight: bold;
		text-overflow: ellipsis;
    	white-space: nowrap; 
	}
	.list-adroll .item-slide  a:hover{
		text-decoration: underline;
	}
	.title-roll{
		text-overflow: ellipsis;
    	white-space: nowrap; 
    	padding: 5px 20px 5px 0;    	
    	overflow: hidden;
    	border-bottom: 1px solid #E6E6E6;    	
    	font-family: Arial,san-serif;
	    font-size: 13px;
	    font-weight: bold;
	    letter-spacing: -0.01ex;
	    margin: 0;
	    padding-bottom: 12px;
	    text-shadow: 0 0 1px rgba(255, 255, 255, 0.1);
	    text-transform: uppercase;
}

	}
	.wrap-roll .item-slide span{
		
    	overflow: hidden;
    	text-overflow: ellipsis;
    	
    	font-size: 12px;
    	color: #444;
	}
	.wrap-roll .item-slide span.ad-price{
		display: inline;
		color: #D84F38;
		font-size: 12px;
		white-space: nowrap;
	}
	.wrap-roll .item-slide span.roll-adress{
		font-size: 12px;
		color: #6A6C6B;
	}
	span.empty_roll{
		font-size: 12px;
		color: #444;
		padding-top: 20px;
		display: block;
	}
	.item-slide{
		position: relative;
	}
	.right-item{
		width:115px;
		padding-left:5px; 
		float:left;
	}
	.left-item-roll{
		max-height: 68px;
		overflow: hidden;
		float: left;
	}
	
	.flex-direction-nav a{
		position: relative;
		z-index: 10000;
		background: url('<?php echo CE_ROLL_URL;?>/css/arrows.png') no-repeat left bottom; 
		text-indent: -10000;
		float: left;
		width: 7px;
		margin: 0 3px;
		height: 10px;

	}	
	a.flex-prev{
		background-position: left bottom;
	}
	a.btn-prev:hover{
		background-position: left top;
	}
	a.flex-next{
		background-position: right bottom;
	}
	a.flex-next:hover{
		background-position: right top;
	}
</style>
<script type="text/javascript">

</script>
</head>
<body>

<?php

$ad_cat 	= isset($_REQUEST['ad_cat']) ? intval($_REQUEST['ad_cat']) : '';
$ad_local  	= isset($_REQUEST['ad_location']) ? intval($_REQUEST['ad_location']) : '';

$count_page = 3;

$args 	= array(
			'post_type' 		=> 'ad',
			'post_status' 		=>'publish',
			'posts_per_page' 	=> $number*$count_page
			);
if(!empty($ad_cat)){
	$args['tax_query']['relation'] = 'AND';
	$args['tax_query'][] 	= array(
								'taxonomy' => ET_AdCatergory::AD_CAT,
								'field' => 'id',
								'terms' => array(intval($ad_cat))								
							);
}
if(!empty($ad_local)){
	$args['tax_query']['relation'] = 'AND';
	$args['tax_query'][] 	= array(
								'taxonomy' => ET_AdLocation::AD_LOCATION,
								'field' => 'id',
								'terms' => array(intval($ad_local))							
							);
}

$ads 		= new WP_Query($args);
$count = count($ads->posts);


echo '<div class="wrap-roll">';
echo '<h2 class="title title-roll widget-title">'.$title.'</h2>';
if($ads->have_posts()){	
	echo '<div class="flexslider">';
	echo '<ul id="ce_roll" class="list-adroll slides">';
	$i = 1;
	
	while($ads->have_posts()): $ads->the_post(); 
		
		global $post;
		
		$location	= get_post_meta( get_the_ID(), 'et_full_location', true );
		$user		= get_userdata( (int)$post->post_author );
		$ad = CE_Ads::convert ($post);
		
		$full_location = get_post_meta(get_the_ID(),$et_global['db_prefix'].'full_location',true);
		
		if(  $i % $number == 1 ){
			echo '<li class="item-slide">';
		}
		?>
			<div class="item-roll">
				<div class="left-item-roll">				
					<a target="_blank" title = "<?php the_title();?>" href="<?php the_permalink();?>">
						<?php
							if(has_post_thumbnail()){
								$post_thumbnail_id = get_post_thumbnail_id( get_the_ID() );
								echo wp_get_attachment_image($post_thumbnail_id,'thumbnail',false,array('width' => '50','height' => '50'));
							} else {
								echo '<img src="'.TEMPLATEURL.'/img/no_image.gif" />';
							}
						?>
					</a>
				</div>
				<div class="right-item" >
					<p><a target="_blank" href="<?php the_permalink()?>"> <?php the_title(); ?></a> </p>
					<p><span class="ad-price"> <?php echo $ad->price;?> </span> </p>
	            	<p><span class="roll-adress"><?php echo $full_location; ?></span></p>
	           </div>
           </div>
            
	
		<?php

		if( ($i%$number != 0 && $i == $count) || $i == $count || $i % $number == 0  ){
		
			echo '</li>';
		}

		$i++;
	endwhile;

	echo '</ul> </div>';
	
} else{
	echo '<span class="empty_roll">';
	_e('No ad currently matches your selected option.',ET_DOMAIN);
	echo '</div>';
} 
echo '</div>';

?>
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.0/jquery.min.js"></script>
<script defer src="<?php echo CE_ROLL_URL;?>/js/jquery.flexslider.js"></script>

  <script type="text/javascript">
  	(function ($) {
	    $(window).load(function(){
	      $('.flexslider').flexslider({
	        animation: "slide",
	        controlNav : false,
	        directionNav : true,
	        prevText  : '',
	        nextText  : '',
	        start: function(slider){
	          $('body').removeClass('loading');
	        }
	      });
	    });
	})(jQuery);
  </script>

</body>
</html>