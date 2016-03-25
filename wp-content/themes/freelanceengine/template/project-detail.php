<?php
/**
 * The template for displaying project detail heading, author info and action button
 */
global $wp_query, $ae_post_factory, $post, $user_ID;

$post_object    = $ae_post_factory->get(PROJECT);
$convert = $project = $post_object->current_post;

$et_expired_date    = $convert->et_expired_date;    
$bid_accepted       = $convert->accepted;
$project_status     = $convert->post_status;
$profile_id         = get_user_meta($post->post_author,'user_profile_id', true);
$IsInvitedToProject = (get_post_meta($convert->ID,"invited_{$user_ID}",true) == '1') ? true : false;

$currency           = ae_get_option('content_currency',array('align' => 'left', 'code' => 'USD', 'icon' => '$'));

?>




<?php
$role = ae_user_role();
if ($project_status == 'publish') {
    if( ( fre_share_role() || $role == FREELANCER ) && $user_ID != $project->post_author ){
        $has_bid = fre_has_bid( get_the_ID() );
        if( $has_bid ) {
?>
            <div class="single-projects info-project-item-details content-require-project h4"><?php  _e('You submitted this proposal','projects-page');?></div>
<?php
        }
    }
}
?>






<div class="col-md-12">
	<div class="tab-content-project">
        <?php if(interview_is_profile_activated())
        {?>
            <div class="col-md-12" style="margin-top: 25px">
                <div class="alert alert-warning" role="alert">
                    <?php  _e('Your profile is not activated. To activate you profile you need to pass the interview - ','projects-page');?>
                    <a href="/interview"><?php  _e('Interview details','projects-page');?></a>
                    <?php
                    if (get_option('interview_system') == 'false') {
                        ?>
                        <button id="activate_without_interview"
                                class="btn btn-info"><?php  _e('Activate without interview','projects-page');?>
                        </button>
                        <?php
                    }
                    ?>
                </div>
            </div>
        <?php
        }
        ?>
    	<!-- Title -->
    	<div class="row title-tab-project">
            <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                <span><?php _e("PROJECT TITLE", 'projects-page'); ?></span>
            </div>
            <div class="hidden-xs col-sm-2 col-md-2 col-lg-2">
                <span><?php _e("BY", 'projects-page'); ?></span>
            </div>
            <div class="hidden-xs hidden-sm hidden-md col-lg-2">
                <span><?php _e("POSTED DATE", 'projects-page'); ?></span>
            </div>
            <div class="hidden-xs col-sm-6 col-md-6 col-lg-2">
                <span><?php _e("BUDGET", 'projects-page'); ?></span>
            </div>
        </div>
        <!-- Title / End -->
        <!-- Content project -->
        <div class="single-projects">
            <div class="project type-project project-item">
                <div class="row">
                    <!--PROJECT TITLE-->
                    <div class="col-xs-7 col-sm-4 col-md-4 col-lg-4">
                        <a href="<?php echo get_author_posts_url( $post->post_author ); ?>" class="avatar-author-project-item">
                            <?php echo get_avatar( $post->post_author, 35,true, get_the_title($profile_id) ); ?>
                        </a>
                        <h1 class="content-title-project-item"><?php the_title();?></h1>
                    </div>
                    <!--BY-->
                    <div class="hidden-xs col-sm-2 col-md-2 col-lg-2">
                      	<span class="author-link-project-item"><?php the_author_posts_link();?></span>
                    </div>
                    <!--POSTED DATE-->
                    <div class="hidden-xs hidden-sm hidden-md col-lg-2">
                         <span  class="time-post-project-item"><?php the_date(); ?></span>
                    </div>
                    <!--BUDGET-->
                    <div class="hidden-xs col-sm-3 col-md-3 col-lg-2">
                        <?php
                        if ($convert->type_budget == 'hourly_rate'){
                           ?>
                            <span class="budget-project-item"> <?php echo $convert->budget . __(" / hour", 'projects-page'); ?></span>
                        <?php
                        }else{
                            ?>
                            <span class="budget-project-item"> <?php echo $convert->budget; ?></span>
                        <?php
                        }
                        ?>
                    </div>
                    <?php
                    $data_project['data'] = array(
                        'title' => get_the_title(),
                        'type_budget' => $convert->type_budget,
                        'description' => get_the_content(),
                        'skill' => taxonomy_project_to_array(get_the_ID(), 'skill'),
                        'project_category' => taxonomy_project_to_array(get_the_ID(), 'project_category'),
                        'hours_limit' => $convert->hours_limit
                    );
                    ?>
                    <script id="data_current_project" type="text/javascript">
                     var current_project_data = <?php echo json_encode($data_project); ?>
                    </script>
                    <!--STATUS-->
                    <div class="col-xs-5 col-sm-3 col-md-3 col-lg-2 text-right  " style="padding:0; margin:0;">
                    <?php
                    if(current_user_can( 'manage_options' )) {
                        get_template_part( 'template/admin', 'project-control' );
                    }elseif( !$user_ID && $project_status == 'publish'){ ?>
                        <a href="#"  class="btn btn-apply-project-item btn-login-trigger" ><?php  _e('Bid','projects-page');?></a>  
                        <a href="#" class="popup-login" style="display:none;">Open Login Popup</a>
                    <?php } else {
                        $role = ae_user_role();
                        switch ($project_status) {
                            case 'publish':
                                if( ( fre_share_role() || $role == FREELANCER ) && $user_ID != $project->post_author ){
                                    $has_bid = fre_has_bid( get_the_ID() );
                                    if ($has_bid) {
                                        ?>
                                        <a rel="<?php echo $project->ID; ?>" href="#" id="<?php echo $has_bid; ?>"
                                           title="<?php _e('Cancel this bidding', 'projects-page'); ?>"
                                           class="btn btn-apply-project-item btn-del-project-modal modal_bid_update">
                                            <?php _e('Cancel', 'projects-page'); ?>
                                        </a>

                                        <a href="#"
                                           class="btn btn-apply-project-item btn-project-status modal_bid_update"
                                           data-toggle="modal" data-target="#modal_bid_update">
                                            <?php _e(' Edit Bid ', 'projects-page'); ?>
                                        </a>

                                        <?php
                                    } elseif ($IsInvitedToProject) { ?>
                                        <a href="#" class="btn btn-apply-project-item btn-project-status"
                                           data-toggle="modal" data-target="#modal_bid">
                                            <?php _e('Accept', 'projects-page'); ?>
                                        </a>
                                        <a href="#"
                                           class="btn btn-decline-invite btn-apply-project-item btn-project-status">
                                            <?php _e('Decline', 'projects-page'); ?>
                                        </a>
                                    <?php } else { ?>
                                        <a href="#" class="btn btn-apply-project-item btn-project-status"
                                           data-toggle="modal" data-target="#modal_bid" <?php if(interview_is_profile_activated()) echo'disabled'?>>
                                            <?php _e('Bid ', 'projects-page'); ?>
                                        </a>
                                    <?php }
                                } else { ?>
<!--                                    <a href="#" id="--><?php //the_ID();?><!--"  class="btn btn-apply-project-item" >--><?php // _e('Open','projects-page');?><!--</a>-->
                                    <div class="alert alert-success alert-status-project" role="alert"><?php _e('Status:','projects-page');?> <strong><?php _e('Open','projects-page');?></strong></div>

                                <?php
                                }
                                break;
                            case 'close':
                                if( (int)$project->post_author == $user_ID){ ?>
                                
                                    <a title="<?php  _e('Finish','projects-page');?>" href="#" id="<?php the_ID();?>"   class="btn btn-apply-project-item btn-project-status btn-complete-project" >
                                        <?php  _e('Finish','projects-page');?>
                                    </a>
                                    <a title="<?php _e('Close','projects-page');?>" href="#" id="<?php the_ID();?>"   class="btn btn-apply-project-item btn-project-status btn-close-project" >
                                        <?php _e('Close','projects-page');?>
                                    </a>
                                    <?php 
                                }else{ 
                                    $bid_accepted_author = get_post_field( 'post_author', $bid_accepted);
                                    if($bid_accepted_author == $user_ID) {
                                ?>
                                    <a title="<?php  _e('Quit','projects-page');?>" href="#" id="<?php the_ID();?>"   class="btn btn-apply-project-item btn-project-status btn-quit-project" >
                                        <?php  _e('Quit','projects-page');?>
                                    </a>
                                <?php } 
                                }
                                break;
                            case 'complete' :
                                $freelan_id  = (int)get_post_field('post_author',$project->accepted);
                        
                                $comment = get_comments( array('status'=> 'approve', 'type' => 'fre_review', 'post_id'=> get_the_ID() ) );

                                if( $user_ID == $freelan_id && empty( $comment ) ){ ?>
                                    <a href="#" id="<?php the_ID();?>"   class="btn btn-apply-project-item btn-project-status btn-complete-project" ><?php  _e('Review','projects-page');?></a>
                                    <?php 
                                } else { ?>

<!--                                <a href="#" id="--><?php //the_ID();?><!--"   class="btn btn-apply-project-item project-complete" >--><?php // _e('Completed','projects-page');?><!--</a>-->
                                    <div class="alert alert-danger alert-status-project" role="alert"><?php _e('Status:','projects-page');?> <strong><?php _e('Closed','projects-page');?></strong></div>
                                <?php
                                }
                                break;
                            default:
                                $text_status =   array( 'pending'   => __('Pending','projects-page'),
                                                        'draft'     => __('Draft','projects-page'),
                                                        'archive'   => __('Draft','projects-page'),
                                                        'reject'    => __('Reject', 'projects-page'),
                                                        'trash'     => __('Trash', 'projects-page'), 
                                                        'close'     => __('Working', 'projects-page'), 
                                                        'complete'  => __('Completed', 'projects-page'), 
                                                        );
                                if(isset($text_status[$project_status])){ ?>
                                    <a href="#"  class="btn btn-apply-project-item" ><?php  echo isset($text_status[$convert->post_status]) ? $text_status[$convert->post_status] : ''; ;?></a>
                                    <?php
                                }
                                break;
                        }
                    }
                    ?>
                    </div>
                </div>
            </div>
                <?php if( Fre_ReportForm::AccessReport() 
                        && ($post->post_status == 'disputing' || $post->post_status == 'disputed') 
                        && !isset($_REQUEST['workspace']) 
                    ) { ?>
                    <div class="workplace-container">
                        <?php get_template_part('template/project', 'report') ?>
                    </div>
                <?php }else if( isset($_REQUEST['workspace']) && $_REQUEST['workspace'] ) { ?>
                    <div class="workplace-container">
                        <?php get_template_part('template/project', 'workspaces') ?>
                    </div>
                <?php }else {
                    get_template_part('template/project-detail' , 'info');
                    get_template_part('template/project-detail' , 'content');
                } ?>
                            
                </div>


            </div>
        <!-- Content project / End -->
        <div class="clearfix"></div>
    </div><!-- tab-content-project !-->
</div>  <!--col-md-12 !-->

<?php
// TODO: delete this
?>
<!--Modal Window For Deleting Bid!-->
<!--<div class="modal modal-vcenter fade in" id="modal-deleting-bid">-->
<!--    <div class="modal-dialog top-margin">-->
<!--        <div class="modal-content" style="margin-top:150px; height:250px;">-->
<!--            <div class="modal-header">-->
<!--                <button type="button" class="close" data-dismiss="modal" aria-label="Close" aria-hidden="true"><span aria-hidden="true" style="font-size: 18px;">X</span></button>-->
<!--                <h4 class="modal-title text-center text-color-popup" style="font-size:30px;">--><?php // _e('NoDeleting Bid','projects-page');?><!--</h4>-->
<!--                <hr>-->
<!--            </div>-->
<!--            <div class="modal-body">-->
<!--                <h4 class="text-center" style="font-size:25px; padding-top:10px;">--><?php // _e('NoAre you sure, you want to delete the Bid?','projects-page');?><!--</h4>-->
<!--                <div style="padding-top:25px;">-->
<!--                    <a style="margin:10px;" href="#" class="btn btn-apply-project-item" data-dismiss="modal">-->
<!--                        --><?php // _e('No','projects-page');?>
<!--                    </a>-->
<!--                    <a style="margin:10px;" rel="--><?php //echo $project->ID;?><!--" href="#" id="--><?php //echo $has_bid;?><!--" class="btn btn-apply-project-item btn-del-project" >-->
<!--                        --><?php // _e('Delete','projects-page');?>
<!--                    </a>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->
