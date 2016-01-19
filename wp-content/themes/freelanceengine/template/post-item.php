<?php
/**
 * The template for displaying post details in a loop
 * @since 1.0
 * @package FreelanceEngine
 * @category Template
 */
?>
<div class="blog-wrapper post-item">
    <div class="row">
        <div class="col-md-3 col-xs-3">
            <div class="author-wrapper">
                <span class="avatar-author">
                    <?php echo get_avatar($post->post_author, 65); ?>
                </span>
                <span class="date">
                    <?php the_author();?><br>
                    <?php the_time('M j');  ?> <sup><?php the_time('S');?></sup>, <?php the_time('Y');?>
                </span>
            </div>
        </div>
        <div class="col-md-9 col-xs-9">
            <div class="blog-content">
                <span class="tag">
                    <?php the_category( ' - ' ); ?>
                </span>
                <span class="cmt">
                    <i class="fa fa-comments"></i>
                    <?php comments_number(); ?>
                </span>
                <h2 class="title-blog"><a href="<?php the_permalink(); ?>"><?php the_title() ?></a></h2>
                <?php
                    if(is_single()){
                        the_content();
                        wp_link_pages( array(
                            'before'      => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', ET_DOMAIN ) . '</span>',
                            'after'       => '</div>',
                            'link_before' => '<span>',
                            'link_after'  => '</span>',
                        ) );                        
                    } else {
                        the_excerpt();
                ?>
                <a href="<?php the_permalink(); ?>" class="read-more">
                    <?php _e("READ MORE",ET_DOMAIN) ?><i class="fa fa-arrow-circle-o-right"></i>
                </a>
                <?php } ?>
            </div>
        </div>
    </div>
</div>