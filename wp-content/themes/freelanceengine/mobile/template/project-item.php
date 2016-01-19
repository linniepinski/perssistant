<?php 
global $wp_query, $ae_post_factory, $post;
$post_object    = $ae_post_factory->get( PROJECT );
$current        = $post_object->current_post;

$author_name = get_the_author_meta('display_name', $post->post_author);
?>
<li class="project-item">
    <div class="info-project-top">
        <div class="avatar-author-project">
            <a href="<?php echo $current->author_url; ?>" title="<?php echo $author_name; ?>">
                <?php echo get_avatar($post->post_author, 25); ?>
            </a>
        </div>
        <a href="<?php the_permalink(); ?>" class="title-project" title="<?php the_title(); ?>">
            <?php the_title(); ?>
        </a>
        <?php if($current->et_featured) { ?>
            <span class="ribbon"><i class="fa fa-star"></i></span>
        <?php } ?>
        <div class="clearfix"></div>
    </div>
    <div class="info-bottom">
        <span class="name-author"><?php printf(__("Posted by %s", ET_DOMAIN), $author_name); ?></span>
        <span class="price-project"><?php echo $current->budget; ?></span>
    </div>
</li>