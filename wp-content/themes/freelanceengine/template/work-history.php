<?php 

/**

 * Template part for employer posted project block

 # this template is loaded in page-profile.php , author.php

 * @since 1.0

 * @package FreelanceEngine

 */

if(is_page_template('page-profile.php')) {

    $status = array(    

        'reject'  => __("REJECTED", 'work-history') , 

        'pending' => __("PENDING", 'work-history') , 

        'publish' => __("ACTIVE", 'work-history'), 

        'close' => __("HIRED", 'work-history'),

        'complete' => __("COMPLETED", 'work-history'),

        'draft'   => __("DRAFT", 'work-history'), 

        'archive' => __("ARCHIVED", 'work-history'), 

        'disputing' => __( "DISPUTE" , 'work-history' )

    );

}else {

    $status = array(    

        'publish' => __("ACTIVE", 'work-history'), 

        'complete' => __("COMPLETED", 'work-history')

    );

}





?>

<div class="profile-history project-history">

<?php 

$author_id = get_query_var('author');

$stat = array('publish','close', 'complete', 'disputing');

if(is_page_template('page-profile.php')) {

    global $user_ID;

    $author_id = $user_ID;

    $stat = array('pending','publish','close', 'complete', 'disputing');

}

// filter order post by status

add_filter('posts_orderby', 'fre_order_by_project_status');

query_posts( array( 'is_profile' => true, 

                    'post_status' => $stat, 

                    'post_type' => PROJECT, 

                    'author' => $author_id )

                ); 

// remove filter order post by status

$bid_posts   = $wp_query->found_posts;

?>

    <div class="work-history-heading">

        <h4 class="title-big-info-work-history-items">

            <?php 

            if(fre_share_role() ) {

                printf(__("Posted projects (%d)", 'work-history'), $wp_query->found_posts);

            }else{

                printf(__("Work history and Reviews (%d)", 'work-history'), $wp_query->found_posts);

            }

            ?>    

        </h4>

        <div class="project-status-filter" >

            <select class="status-filter chosen-select" name="post_status" data-chosen-width="100%" data-chosen-disable-search="1" 

                data-placeholder="<?php _e("Select a status", 'work-history'); ?>">

                <option value=""><?php _e("Select a status", 'work-history'); ?></option>

                <?php foreach ($status as $key => $stat) {

                    echo '<option value="'.$key.'">'.$stat.'</option>' ;

                }  ?>

            </select>

        </div>

        <div class="clearfix"></div>

    </div>

    <?php

        // list portfolio

        if(have_posts()):

            get_template_part( 'template/work', 'history-list' );     

        else :
            if (fre_share_role() || ae_user_role($author_id) != FREELANCER) {
                echo '<ul style="list-style:none;padding:0;"><li><span class="no-results">'.__('No work history.', 'work-history').'</span></li></ul>';
            } else {
                echo '<ul style="list-style:none;padding:0;"><li><span class="no-results">'.__('You have not worked on any project yet.', 'work-history').'</span></li></ul>';
            }

        endif;

        //wp_reset_postdata();

     ?>



</div>

<?php

wp_reset_query();

remove_filter('posts_orderby', 'fre_order_by_project_status');