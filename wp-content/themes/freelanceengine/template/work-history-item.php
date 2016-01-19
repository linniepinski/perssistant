<?php 
/**
 * Template part for employer project details 
 # this template is loaded in template/list-work-history.php
 * @since 1.0   
 * @package FreelanceEngine
 */
global $user_ID;
$author_id = get_query_var('author');
if(!$author_id) {
    $author_id = $user_ID;
}


global $wp_query, $ae_post_factory, $post;

$post_object = $ae_post_factory->get( PROJECT );
$current     = $post_object->current_post;

if(!$current){
    return;
}

$status = array(    
    'reject'  => __("REJECTED", ET_DOMAIN) , 
    'pending' => __("PENDING", ET_DOMAIN) , 
    'publish' => __("ACTIVE", ET_DOMAIN), 
    'close' => __("HIRED", ET_DOMAIN),
    'complete' => __("COMPLETED", ET_DOMAIN),
    'draft'   => __("DRAFT", ET_DOMAIN), 
    'archive' => __("ARCHIVED", ET_DOMAIN), 
    'disputing' => __( "DISPUTE" , ET_DOMAIN )
);

?>
<li class="bid-item">
    <div class="name-history">
        <a href="<?php echo $current->author_url;?>">
            <span class="avatar-bid-item">
                <?php echo $current->et_avatar; ?>
            </span>
        <a>
        <div class="content-bid-item-history">
            <?php if($current->post_status == 'complete'){ ?>
                <h5>
                    <a href = "<?php echo $current->permalink; ?>"><?php echo $current->post_title; ?></a>
                    <div class="rate-it" data-score="<?php echo $current->rating_score; ?>"></div>
                </h5>
                <span class="comment-author-history"><?php echo $current->project_comment; ?></span>
                <span class="stt-in-process"><?php _e('Job is completed', ET_DOMAIN);?></span> 
            
            <?php } else if($current->post_status == 'close'){ ?>
                <h5>
                    <a href = "<?php echo $current->permalink; ?>"><?php echo $current->post_title; ?></a>
                </h5>
                <span class="stt-in-process">
                    <?php 
                        _e('Job is closed', ET_DOMAIN);
                     ?>
                </span> 
            <?php }else{ ?>

                <h5>
                    <a href = "<?php echo $current->permalink; ?>"><?php echo $current->post_title; ?></a>
                </h5>
                <span class="stt-in-process"><?php _e('Job is open', ET_DOMAIN);?></span> 
                
            <?php } ?>
        </div>
    </div>
    <ul class="info-history">
        <li>
            <?php echo $current->post_date; ?>
        </li>
        <li>
            <?php _e("Budget", ET_DOMAIN); ?> :
            <span class="number-price-project-info"><?php echo $current->budget; ?></span>
        </li>
        <li class="post-control"> 
        <?php
            if($author_id && $author_id == $user_ID) {
                echo $status[$current->post_status];
                echo '<span> &#9830; </span>';
                ae_edit_post_button($current);
            }
            if($user_ID == $current->post_author && $current->post_status == 'close') { 
            ?> 
                <a href = "<?php echo add_query_arg(array('workspace' => 1), $current->permalink); ?>" title=" <?php _e( 'Open Workspace' , ET_DOMAIN ); ?>">
                    <i class="fa fa-share-square-o"></i>
                </a>
            <?php }
        ?>

        </li>
    </ul>
    <div class="clearfix"></div>
</li>