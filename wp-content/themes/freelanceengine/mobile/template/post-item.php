<div class="blog-wrapper post-item">
    <div class="row">
        <div class="col-md-2 col-xs-2">
            <div class="author-wrapper">
                <span class="avatar-author">
                    <?php echo get_avatar($post->post_author, 48); ?>
                </span>
            </div>
        </div>
        <div class="col-md-10 col-xs-10">
            <div class="blog-content">
                <span class="tag">
                    <?php the_category( ' - ' ); ?>
                </span>
                <span class="cmt">
                    <i class="fa fa-comments"></i>
                    <?php comments_number( '0', '1', '%' ); ?>
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