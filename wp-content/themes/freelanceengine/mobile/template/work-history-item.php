<?php
/**
 * Template part for employer project details 
 # this template is loaded in template/list-work-history.php
 * @since 1.1 
 * @package FreelanceEngine
 */
	$author_id = get_query_var('author');
	if(is_page_template('page-profile.php')) {
	    global $user_ID;
	    $author_id = $user_ID;
	}

	global $wp_query, $ae_post_factory, $post;

	$post_object = $ae_post_factory->get( PROJECT );
	$current     = $post_object->current_post;

	if(!$current){
	    return;
	}
?>

<li class="bid-item">
    <div class="info-project-top">
        <div class="avatar-author-project">
            <?php echo $current->et_avatar;?>
        </div>
        <h1 class="title-project">
            <a href="<?php echo $current->permalink; ?>" title="<?php echo $current->post_title; ?>" >
                <?php echo $current->post_title; ?>
            </a>
        </h1>
        <div class="clearfix"></div>
    </div>
    <div class="info-bottom">
        <?php if($current->post_status == 'complete'){ ?>
            <?php if(isset($current->project_comment) && $current->project_comment != ''){ ?>
            <span class="comment-stt-project"><blockquote><?php echo $current->project_comment; ?></blockquote></span>
            <?php } ?>
            <span class="star-project">
                <div class="rate-it" data-score="<?php echo $current->rating_score; ?>"></div>
            </span>            
        <?php } else if($current->post_status == 'close'){ ?>
            <span class="status"><?php _e('Job is closed', ET_DOMAIN);?></span>
        <?php } else { ?>
            <span class="status"><?php _e('Job in process', ET_DOMAIN);?></span>
        <?php } ?>
    </div>
</li>