<?php
	et_get_mobile_header();

   
?>
<section class="section section-single-project">
<?php 
    if(have_posts()) { the_post();
        global $wp_query, $ae_post_factory, $post, $project, $user_ID;
        $post_object    = $ae_post_factory->get(PROJECT);

        $convert            = $post_object->convert($post);
        $et_expired_date    = $convert->et_expired_date;    
        $bid_accepted       = $convert->accepted;
        $project_status     = $convert->post_status;
        $profile_id         = get_user_meta($post->post_author,'user_profile_id', true);
        $currency           = ae_get_option('content_currency',array('align' => 'left', 'code' => 'USD', 'icon' => '$'));
        $project            = $convert;
        $exp                = $convert->et_expired_date;
        $IsInvitedToProject = (get_post_meta($convert->ID,"invited_{$user_ID}",true) == '1') ? true : false;
        ?>

	<div class="info-single-project-wrapper">
    	<div class="container">
            <div class="info-project-top">
                <div class="avatar-author-project">
                    <a href="<?php echo get_author_posts_url( $post->post_author ); ?> ">
                        <?php echo get_avatar( $post->post_author, 35,true, get_the_title($profile_id) ); ?> 
                    </a>
                </div>
                <h1 class="title-project"><?php the_title();?></h1>
                <div class="clearfix"></div>
            </div>
            <div class="info-bottom">
                <span class="name-author">
                    <?php printf(__('Posted by %s','single-project-mobile'), get_the_author_meta( 'display_name', $convert->post_author ));?>
                </span>
                <span class="price-project"> 
                    <?php echo $convert->budget; ?>
                </span>
            </div>
        </div>
    </div>
    <div class="info-bid-wrapper">
        <ul class="bid-top">
            <li>
                <?php 
                    $total_count = get_comments(array( 'post_id' => $post->ID, 'type' => 'comment', 'count' => true, 'status' => 'approve' ));
                    if($total_count < 1){
                        printf(__('<span class="number">%d</span>Comment', 'single-project-mobile'), intval($total_count));
                    }else {
                        printf(__('<span class="number">%d</span>Comments', 'single-project-mobile'), $total_count);
                    } 
                ?>
            </li>
            <li>
                <?php 
                    if($convert->total_bids && $convert->total_bids > 1) {
                        printf(__('<span class="number">%d</span> bids', 'single-project-mobile'), $convert->total_bids);
                    }else {
                        printf(__('<span class="number">%d</span> bid', 'single-project-mobile'), $convert->total_bids);
                    }
                ?>
            </li>
            <li><span class="number"><?php echo fre_price_format($convert->bid_average); ?></span> <?php printf(__("Avg Bids(%s)",'single-project-mobile'), $currency['code']);?></li>
            <li class="clearfix"></li>
            <li class="stt-bid">
            	<div class="time">
            		<span class="number"><?php _e("Open", 'single-project-mobile'); ?></span><?php if( empty($exp)) printf(__('%s ago','single-project-mobile'), human_time_diff( get_the_time('U'), time() ) ); else printf(__('%s left','single-project-mobile'), human_time_diff( time(), strtotime($exp)) );  ?>
                </div>
                <p class="btn-warpper-bid">
                <?php if( !$user_ID && $project_status == 'publish'){ ?>
                        <a href="#"  class="btn-apply-project-item btn-login-trigger btn-bid btn-bid-mobile" ><?php  _e('Bid','single-project-mobile');?></a>                                        
                        <?php 
                    } else {
                        $role = ae_user_role();
                        if($project_status == 'publish'){
                            if($role == FREELANCER){
                                $has_bid = fre_has_bid( get_the_ID() );
                                if ($has_bid) {
                                    ?>
                                    <a rel="<?php echo $project->ID; ?>" href="#" id="<?php echo $has_bid; ?>"
                                       title="<?php _e('Delete this bidding', 'single-project-mobile'); ?>"
                                       class="btn-bid btn-bid-update-mobile"><?php _e('Edit bid', 'single-project-mobile'); ?></a>

                                    <a rel="<?php echo $project->ID; ?>" href="#" id="<?php echo $has_bid; ?>"
                                       title="<?php _e('Delete this bidding', 'single-project-mobile'); ?>"
                                       class="btn-bid btn-del-project"><?php _e('Cancel', 'single-project-mobile'); ?></a>

                                <?php } elseif ($IsInvitedToProject) { ?>
                                    <a href="#" class="btn-apply-project-item  btn-bid btn-bid-mobile">
                                        <?php _e('Accept', 'single-project-mobile'); ?>
                                    </a>
                                    <a href="#"
                                       class="btn btn-decline-invite btn-apply-project-item btn-project-status">
                                        <?php _e('Decline', 'single-project-mobile'); ?>
                                    </a>
                                <?php } else { ?>
                                    <a href="#" class="btn-apply-project-item  btn-bid btn-bid-mobile">
                                        <?php _e('Bid ', 'single-project-mobile'); ?>
                                    </a>
                                <?php }
                            } else { ?>
                                <a href="#" id="<?php the_ID(); ?>"
                                   class="btn-apply-project-item btn-bid"><?php _e('Open', 'single-project-mobile'); ?></a>
                                <?php
                            }
                            
                        }
                        if($project_status == 'close'){
                            if( (int)$project->post_author == $user_ID){ ?>
                                <a class="btn btn-apply-project-item btn-project-status btn-disput-this">
                                    <?php _e('Open disput', 'single-project-mobile'); ?>
                                </a>
                                <a href="#" class="btn btn-primary btn-close-project"><?php _e("Close", 'single-project-mobile'); ?></a>
                                <a href="#" id="<?php the_ID();?>"   class="btn btn-primary btn-project-status btn-complete-project btn-complete-mobile" >
                                    <?php  _e('Complete','single-project-mobile');?>
                                </a>
                                <?php 
                            } else {
                                $freelan_id  = (int)get_post_field('post_author', $bid_accepted);
                                if($freelan_id == $user_ID ) { ?>
                                    <a href="#"  class="btn btn-primary btn-quit-project" title="<?php  _e('Quit','single-project-mobile');?>" ><?php  _e('Quit','single-project-mobile');?></a>
                                <?php }else{ ?> 
                                    <a href="#"  class="btn btn-primary" title="<?php  _e('Working','single-project-mobile');?>" ><?php  _e('Working','single-project-mobile');?></a>
                                <?php }
                            }
                        } else if($project_status == 'complete'){ 

                            $freelan_id  = (int)get_post_field('post_author',$convert->accepted);
                        
                            $comment        = get_comments( array('status'=> 'approve', 'type' => 'fre_review', 'post_id'=> get_the_ID() ) );

                            if( $user_ID == $freelan_id && empty( $comment ) ){ ?>
                                <a href="#" id="<?php the_ID();?>" title="<?php  _e('Review job','single-project-mobile');?>" class="btn-bid btn-project-status btn-complete-project btn-complete-mobile" ><?php  _e('Review job','single-project-mobile');?></a>
                                <?php 
                            } else { ?>
                                <a href="#"  class="btn-bid" title="<?php  _e('Completed','single-project-mobile');?>" ><?php  _e('Completed','single-project-mobile');?></a>
                                <?php 
                            }
                        } else if($project_status == 'opened_disput'){
                            ?>
                            <a class="btn btn-apply-project-item btn-project-status btn-disput-this">
                                <?php _e('Disput', 'single-project-mobile'); ?>
                            </a>
                            <?php
                        } else{
                            $text_status =   array( 'pending'   => __('Pending','single-project-mobile'),
                                                        'draft'     => __('Draft','single-project-mobile'),
                                                        'archive'   => __('Draft','single-project-mobile'),
                                                        'reject'    => __('Reject', 'single-project-mobile'),
                                                        'trash'     => __('Trash', 'single-project-mobile'), 
                                                        'close'     => __('Working', 'single-project-mobile'), 
                                                        'complete'  => __('Completed', 'single-project-mobile'), 
                                                    );
                            if(isset($text_status[$project_status])){ ?>
                                <a href="#"  class="btn-apply-project-item" ><?php  echo isset($text_status[$convert->post_status]) ? $text_status[$convert->post_status] : ''; ;?></a>
                                <?php
                            }
                        }
                    }
                    ?>             
            </li>
        </ul>
        <div class="clearfix"></div>
    </div>
    <?php 
    // render open workspace button
    if(fre_access_workspace($post)) {
        $project_link = get_permalink( $post->ID );
        if(isset($_REQUEST['workspace']) && $_REQUEST['workspace']) {
            echo '<a class="workspace-link" style="font-weight:600;" href="'.get_permalink( $post->ID ).'">
                <i class="fa fa-arrow-left"></i> '.__("Back To Project Page", 'single-project-mobile').
            '</a>';
        }else {
            echo '<a class="workspace-link" style="font-weight:600;" href='.add_query_arg(array('workspace' => 1), $project_link ).'>'.__("Open Workspace", 'single-project-mobile').' <i class="fa fa-arrow-right"></i></a>';
        }
    }
    ?>

    <?php 
    // dispute form
    if( Fre_ReportForm::AccessReport() 
            && ($post->post_status == 'disputing' || $post->post_status == 'disputed') 
            && !isset($_REQUEST['workspace']) 
        ) { 
            get_template_part('template/project', 'report') ?>
    <?php } ?>

    <!-- user message -->
    <?php if(isset($_REQUEST['workspace']) && $_REQUEST['workspace'] && fre_access_workspace($post)) { ?>
    <div class="workplace-container">
        <div class="info-single-project-wrapper">
            <h1 class="title-workspace"><?php _e("Workspace", 'single-project-mobile'); ?></h1>
        </div>
        <?php get_template_part('template/project', 'workspaces') ?>
    </div>
    <?php }else{ ?>
    <!--// user message -->
    <div class="content-project-wrapper">
        <!-- form bid !-->
        <div class="form-bid">           
            <?php get_template_part('mobile/template-js/form','bid-project'); ?>
            <?php get_template_part('mobile/template-js/form','update-bid-project'); ?>
            <?php get_template_part('mobile/template-js/form','review-project'); ?>
        </div>
        <!-- end form bid !-->
    	<h2 class="title-content"><?php _e('Project description:','single-project-mobile');?></h2>
        <?php 
            the_content(); 
            if(function_exists('et_render_custom_field')) {
                et_render_custom_field($post);
            }
        ?>
        <?php list_tax_of_project( get_the_ID(), __('Skills required:','single-project-mobile'), $tax_name = 'skill' ); ?>
        <?php list_tax_of_project( get_the_ID(), __('Category:','single-project-mobile'), $tax_name = 'project_category' ); ?>
        <?php

            // list project attachment
            $attachment = get_children( array(
                    'numberposts' => -1,
                    'order' => 'ASC',
                    'post_parent' => $post->ID,
                    'post_type' => 'attachment'
                  ), OBJECT );
            if(!empty($attachment)) {
                echo '<div class="project-attachment">';
                echo '<h3 class="title-content">'. __("Attachment:", 'single-project-mobile') .'</h3>';
                echo '<ul class="list-file-attack-report">';
                foreach ($attachment as $key => $att) {
                    $file_type = wp_check_filetype($att->post_title, array('jpg' => 'image/jpeg',
                                                                            'jpeg' => 'image/jpeg',
                                                                            'gif' => 'image/gif',
                                                                            'png' => 'image/png',
                                                                            'bmp' => 'image/bmp'
                                                                        )
                                                );
                    $class="";
                    
                    if(isset($file_type['ext']) && $file_type['ext']) $class="image-gallery";
                    echo '<li>
                            <a class="'.$class.'" target="_blank" href="'.$att->guid.'"><i class="fa fa-paperclip"></i>'.$att->post_title.'</a>
                        </li>';
                }
                echo '</ul>';
                echo '</div>';
            }

        ?>
    </div>
    <?php } ?>
    <div class="history-cmt-wrapper">
    	<div class="btn-tabs-wrapper">
        	<ul class="" role="tablist">
            	<li class="active">
                    <a href="#history-tabs" role="tab" data-toggle="tab">
                        <?php printf(__('%s Bidders','single-project-mobile'),$convert->total_bids); ?>
                    </a>
                </li>
                <li>
                    <a href="#comment-tabs" role="tab" data-toggle="tab">
                    <?php 
                        $total_comment = get_comments(array( 'post_id' => $post->ID, 'type' => 'comment', 'count' => true, 'status' => 'approve' ));
                        // comments_number (__('0 Comment', 'single-project-mobile'), __('1 Comment', 'single-project-mobile'), __('% Comments', 'single-project-mobile')); 
                        if($total_comment > 1) {
                            printf(__("%d Comments", 'single-project-mobile'), $total_comment);
                        }else{
                            printf(__("%d Comment", 'single-project-mobile'), $total_comment);
                        }
                    ?>
                    </a>
                </li>
            </ul>
        </div>
        <div class="tab-content">
        	<div class="tab-pane fade in active" id="history-tabs">
            	<!-- List bid of this project !-->
                <?php 

                global $project, $post;               
                add_filter('posts_orderby', 'fre_order_by_bid_status');
                $q_bid      = new WP_Query( array(  'post_type' => BID, 
                                                    'post_parent' => get_the_ID(), 
                                                    'post_status' => array('publish','complete', 'accept')
                                                ) 
                                            );
                $bid_data = array();
                remove_filter('posts_orderby', 'fre_order_by_bid_status');
                if($q_bid->have_posts()):
                    echo '<div class="info-bidding-wrapper project-'.$project->post_status.'">';
                        echo '<ul class="list-history-bidders list-bidding">';
                        while($q_bid->have_posts()):  $q_bid->the_post();
                            get_template_part( 'mobile/template/bid', 'item' );
                            $bid_data[] = $post;
                        endwhile;
                        echo '</ul>';

                        // paging list bid on this project
                        if($q_bid->max_num_pages > 1){
                            echo '<div class="paginations-wrapper">';
                                $q_bid->query = array_merge(  $q_bid->query ,array('is_single' => 1 ) ) ;   
                                ae_pagination($q_bid, get_query_var('paged'), $type = 'load_more');                            
                            echo '</div>';
                        }
                    echo '</div>';


                    // end paging
                else :
                    get_template_part( 'mobile/template/bid', 'not-item' );
                endif;
                wp_reset_query();
                ?>
                <?php 
                if(!empty($bid_data)) {
                    echo '<script type="data/json" class="biddata" >'.json_encode($bid_data).'</script>';
                } ?>
                <!-- End list bid !-->
                <div class="clearfix"></div>
            </div>
            
            <div class="tab-pane fade" id="comment-tabs">
            	<div class="comment-list-wrapper">
                   <!-- <div class="comments" id="project_comment">
        <?php /*comments_template('/comments.php', true)*/?>
    </div>-->
                </div>
            </div>
        </div>
    </div>
    
    <?php get_template_part( 'mobile/form-bid', 'project' ); ?>
    <?php 
    }
?>
<input type="hidden" id="project_id" name="<?php echo $project->ID;?>" value="<?php echo $project->ID;?>" />   
</section>
<?php
    echo '<script type="data/json" id="project_data">'.json_encode($project).'</script>';
	et_get_mobile_footer();
?>