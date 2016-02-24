<?php

global $wp_query, $ae_post_factory, $post;
$post_object    = $ae_post_factory->get( PROJECT );
$current        = $post_object->current_post;
?>
<li <?php post_class( 'project-item' ); ?>>
	<div class="row">
    	<div class="col-md-4 col-sm-5 col-xs-7 text-ellipsis">
        	<a href="<?php echo get_author_posts_url( $current->post_author ); ?>"  class="title-project">
                <?php echo get_avatar( $post->post_author, 35 ); ?>
            </a>
            <a href="<?php echo get_permalink();?>" title="<?php the_title(); ?>" class="project-item-title">
                <?php the_title(); ?>
            </a>

        </div>
        <div class="col-md-2 col-sm-3 hidden-xs">
            <?php 
            if($current->et_featured) { ?>
                <span class="ribbon"><i class="fa fa-star"></i></span>
            <?php } ?>
            <span>
                <?php  the_author_posts_link(); ?>
            </span>
        </div>
        <div class="col-md-2 col-sm-2 hidden-sm hidden-xs">
             <span>
                <?php echo get_the_date() ?>
            </span>
        </div>

        <div class="col-md-2 col-sm-2 col-xs-4 hidden-xs">
            <span class="budget-project">
                <?php
                if ($current->type_budget == 'hourly_rate'){
                    echo fre_price_format($current->et_budget).'/h';
                }else{
echo fre_price_format($current->et_budget);
                }
                ?>
            </span>
        </div>
        <div class="col-md-2 col-sm-2 col-xs-5">
            <p class="wrapper-btn">
                <a href="<?php echo get_permalink();?>" class="btn-sumary btn-apply-project">
                    <?php _e('Apply','project-item');?>
                </a>
            </p>
        </div>
    </div>
</li>