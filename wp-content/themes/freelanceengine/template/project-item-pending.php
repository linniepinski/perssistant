<?php

global $wp_query, $ae_post_factory, $post;
$post_object    = $ae_post_factory->get( PROJECT );
$current        = $post_object->current_post;
?>
<li <?php post_class( 'project-item pending' ); ?>>
	<div class="row">
    	<div class="col-md-5 col-sm-5 col-xs-7 text-ellipsis ">
        	<a href="<?php echo get_author_posts_url( $current->post_author ); ?>"  class="title-project">
                <?php echo get_avatar( $post->post_author, 35 ); ?>
            </a>
            <a href="<?php the_permalink();?>" >
                <?php the_title(); ?>
            </a>
        </div>
         <div class="col-md-2 col-sm-3 hidden-xs">
            <span><?php  the_author_posts_link(); ?></span>
        </div>
        <div class="col-md-2 hidden-sm hidden-xs">
             <span><?php echo get_the_date() ?></span>
        </div>

        <div class="col-md-1 col-sm-2 hidden-xs">
            <span class="budget-project"><?php echo fre_price_format($current->et_budget);?></span>
        </div>
        <div class="col-md-2 text-right col-sm-2 col-xs-5">
           <!--  <a data-action="" class=" paid-status" href="#"> UNPAID </a> -->
            <a data-action="approve" class="action approve" href="#"><i class="fa fa-check"></i></a> &nbsp;
            <a data-action="reject" class="action reject" href="#"><i class="fa fa-times"></i></a>  &nbsp; 
            <a data-action="edit" data-target="#" class="action edit" href="#edit_place"><i class="fa fa-pencil"></i></a> &nbsp;            
        </div>
    </div>
</li>