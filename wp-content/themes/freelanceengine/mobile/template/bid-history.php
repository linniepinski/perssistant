<?php 
/**
 * Template part for user bid history block 
 # This template is loaded in page-profile.php , author.php
 * @since v1.0  
 * @package EngineTheme
 */
global $user_ID, $wp_query;
$author_id = get_query_var('author');
if(is_page_template('page-profile.php')) {
    $author_id = $user_ID;
}
 $status = array(    
        'publish'  => __("ACTIVE", 'bid-history-mobile'), 
        'complete' => __("COMPLETED", 'bid-history-mobile')
    );
    
query_posts( array(  'post_status' => array('publish', 'complete'), 'post_type' => BID, 'author' => $author_id, 'accepted' => 1  ));
?>
<div class="bid-history">
    <div class="btn-tabs-wrapper">
        <ul role="tablist">
            <li class="active">
                <a href="#history-tabs" role="tab" data-toggle="tab">
                    <?php printf(__('History (%s)', 'bid-history-mobile'), $wp_query->found_posts ) ?>
                </a>
            </li>
            <li>
                <a href="#porfolio-tabs" role="tab" data-toggle="tab">
                    <?php printf(__('(%s) Porfolios', 'bid-history-mobile'), fre_count_user_posts($author_id, PORTFOLIO) ) ?>
                </a>
            </li>
        </ul>
    </div>
    <!-- / .btn-tabs-wrapper -->
    <div class="tab-content">
        <div class="tab-pane fade in active" id="history-tabs">
            <div class="btn-tabs-wrapper">            
                <div class="work-history-heading">
                    <a href="#" class="work-history-title" >
                        <?php printf(__('Works History (%s)', 'bid-history-mobile'), fre_count_user_posts($author_id, PROJECT) ) ?>
                    </a>
                    <div class="project-status-filter" >
                        <select class="status-filter " name="post_status" data-chosen-width="100%" data-chosen-disable-search="1" 
                            data-placeholder="<?php _e("Select a status", 'bid-history-mobile'); ?>">
                            <?php foreach ($status as $key => $stat) {
                                echo '<option value="'.$key.'">'.$stat.'</option>' ;
                            }  ?>
                        </select>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <?php 
                get_template_part('mobile/template/bid', 'history-list');
                wp_reset_query();
            ?>
            <div class="clearfix"></div>
        </div><!-- / .tab-history -->
        <div class="tab-pane fade portfolio-container" id="porfolio-tabs">
            <?php
                query_posts( array( 
                    'post_status' => 'publish', 
                    'post_type'   => PORTFOLIO, 
                    'author'      => $author_id
                ));
                get_template_part( 'mobile/list', 'portfolios' );
            ?>                
        </div><!-- / .tab-porfolio -->
    </div><!-- / .tab-content -->
    <?php
	// $author_id = get_query_var('author');
 //    if(is_page_template('page-profile.php')) {
 //        global $user_ID;
 //        $author_id = $user_ID;
 //    }
 //    query_posts( array(  'post_status' => 'publish', 'post_type' => BID, 'author' => $author_id, 'accepted' => 1  )); 
 //    $bid_posts   = $wp_query->found_posts;

 //    // list portfolio
 //    if(have_posts()):
 //        get_template_part( 'mobile/template/bid', 'history-list' );     
 //    else :
 //    endif;
 //    wp_reset_postdata();
 //    wp_reset_query();
?>
</div>