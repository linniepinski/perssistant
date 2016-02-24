<?php 
global $wp_query, $ae_post_factory, $post;
$post_object = $ae_post_factory->get('portfolio');

?>
<div class="row">
	<ul class="list-item-portfolio">
		<?php
        $postdata = array();
        while (have_posts()) { the_post();
            $convert = $post_object->convert($post,'thumbnail');
            $postdata[] = $convert;
            //echo '<pre>';

            //var_dump($postdata);
            //var_dump($convert);
            //echo '</pre>';
           // var_dump($convert);
            get_template_part( 'template/portfolio', 'item' );
        }
        ?>
	</ul>

    
    <div class="col-md-4 col-sm-4 col-xs-4 list-item-portfolio-last add-porfolio-button">
        <a href="#" class="add-portfolio">
            <i class="fa fa-plus"></i>
            <?php _e('Add you work', 'list-portfolios'); ?>
        </a>
    </div>
</div>
	<?php
      
/**
 * render post data for js
*/
echo '<script type="data/json" class="postdata portfolios-data" >'.json_encode($postdata).'</script>';
?>
<!-- pagination -->
<?php
	echo '<div class="paginations-wrapper">';
	ae_pagination($wp_query, get_query_var('paged'), 'load');
	echo '</div>';             
?>

