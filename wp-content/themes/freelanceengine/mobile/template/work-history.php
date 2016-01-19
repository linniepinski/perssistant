<?php 
/**
 * Template part for employer posted project block
 # this template is loaded in page-profile.php , author.php
 * @since 1.0
 * @package FreelanceEngine
 */
?>
<div class="work-history project-history">

<?php
if(is_page_template('page-profile.php')) {
    $status = array(    
        'reject'   => __("REJECTED", ET_DOMAIN) , 
        'pending'  => __("PENDING", ET_DOMAIN) , 
        'publish'  => __("ACTIVE", ET_DOMAIN), 
        'close'    => __("HIRED", ET_DOMAIN),
        'complete' => __("COMPLETED", ET_DOMAIN),
        'draft'    => __("DRAFT", ET_DOMAIN), 
        'archive'  => __("ARCHIVED", ET_DOMAIN), 
    );
} else {
    $status = array(    
        'publish'  => __("ACTIVE", ET_DOMAIN), 
        'complete' => __("COMPLETED", ET_DOMAIN)
    );
}
global $user_ID;
$author_id = get_query_var('author');
if(is_page_template('page-profile.php')) {
    $author_id = $user_ID;
}
// filter order post by status
add_filter('posts_orderby', 'fre_order_by_project_status');
query_posts( array(
    'is_profile'  => true, 
    'post_status' => array('publish','close', 'complete'), 
    'post_type'   => PROJECT, 
    'author'      => $author_id 
)); 
// remove filter order post by status

$bid_posts   = $wp_query->found_posts;
?>
<div class="btn-tabs-wrapper">            
    <div class="work-history-heading">
        <a href="#" class="work-history-title" >
            <?php 
            if(fre_share_role()) {
                printf(__('Posted Projects (%s)', ET_DOMAIN), fre_count_user_posts($author_id, PROJECT) );
            }else {
                printf(__('Works History (%s)', ET_DOMAIN), fre_count_user_posts($author_id, PROJECT) );
            }
            ?>
        </a>
        <div class="project-status-filter" >
            <select class="status-filter " name="post_status" data-chosen-width="100%" data-chosen-disable-search="1" 
                data-placeholder="<?php _e("Select a status", ET_DOMAIN); ?>">
                <option value=""><?php _e("Select a status", ET_DOMAIN); ?></option>
                <?php foreach ($status as $key => $stat) {
                    echo '<option value="'.$key.'">'.$stat.'</option>' ;
                }  ?>
            </select>
        </div>
        <div class="clearfix"></div>
    </div>
</div>

<?php
    // list portfolio
    if(have_posts()):
        get_template_part( 'mobile/template/work', 'history-list' );     
    else :
    endif;
    //wp_reset_postdata();
    wp_reset_query();
    remove_filter('posts_orderby', 'fre_order_by_project_status');
?>
</div>