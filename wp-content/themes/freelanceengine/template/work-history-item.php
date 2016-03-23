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
    'reject'  => __("REJECTED", 'work-history-item') , 
    'pending' => __("PENDING", 'work-history-item') , 
    'publish' => __("ACTIVE", 'work-history-item'), 
    'close' => __("HIRED", 'work-history-item'),
    'complete' => __("COMPLETED", 'work-history-item'),
    'draft'   => __("DRAFT", 'work-history-item'), 
    'archive' => __("ARCHIVED", 'work-history-item'), 
    'disputing' => __( "DISPUTE" , 'work-history-item' )
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
                <span class="stt-in-process"><?php _e('Job is completed', 'work-history-item');?></span> 
            
            <?php } else if($current->post_status == 'close'){ ?>
                <h5>
                    <a href = "<?php echo $current->permalink; ?>"><?php echo $current->post_title; ?></a>
                </h5>
                <span class="stt-in-process">
                    <?php 
                        _e('Job is closed', 'work-history-item');
                     ?>
                </span>
            <?php } else if($current->post_status == 'disputing'){ ?>
                <h5>
                    <a href = "<?php echo $current->permalink; ?>"><?php echo $current->post_title; ?></a>
                </h5>
                <span class="stt-in-process">
                    <?php
                    _e('Job is paused', 'work-history-item');
                    ?>
                </span>
            <?php }else{ ?>

                <h5>
                    <a href = "<?php echo $current->permalink; ?>"><?php echo $current->post_title; ?></a>
                </h5>
                <span class="stt-in-process"><?php _e('Job is open', 'work-history-item');?></span> 
                
            <?php } ?>
        </div>
    </div>
    <ul class="info-history">
        <li>
            <?php echo $current->post_date; ?>
        </li>
        <li>
            <?php _e("Budget", 'work-history-item'); ?> :
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
                <a href = "<?php echo add_query_arg(array('workspace' => 1), $current->permalink); ?>" title=" <?php _e( 'Open Workspace' , 'work-history-item' ); ?>">
                    <i class="fa fa-share-square-o"></i>
                </a>
            <?php }
        ?>

        </li>
    </ul>
    <div class="clearfix"></div>
</li>