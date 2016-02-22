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

$currency           = ae_get_option('content_currency',array('align' => 'left', 'code' => 'USD', 'icon' => '$'));

?>




<?php
$role = ae_user_role();
if ($project_status == 'publish') {
    if( ( fre_share_role() || $role == FREELANCER ) && $user_ID != $project->post_author ){
        $has_bid = fre_has_bid( get_the_ID() );
        if( $has_bid ) {
?>
            <div class="single-projects info-project-item-details content-require-project h4"><?php  _e('You submitted this proposal',ET_DOMAIN);?></div>
<?php
        }
    }
}
?>






<div class="col-md-12">
	<div class="tab-content-project">
    	<!-- Title -->
    	<div class="row title-tab-project">
            <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                <span><?php _e("PROJECT TITLE", ET_DOMAIN); ?></span>
            </div>
            <div class="hidden-xs col-sm-2 col-md-2 col-lg-2">
                <span><?php _e("BY", ET_DOMAIN); ?></span>
            </div>
            <div class="hidden-xs hidden-sm hidden-md col-lg-2">
                <span><?php _e("POSTED DATE", ET_DOMAIN); ?></span>
            </div>
            <div class="hidden-xs col-sm-6 col-md-6 col-lg-2">
                <span><?php _e("BUDGET", ET_DOMAIN); ?></span>
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
                            <span class="budget-project-item"> <?php echo $convert->budget . ' / hour'; ?></span>
                        <?php
                        }else{
                            ?>
                            <span class="budget-project-item"> <?php echo $convert->budget; ?></span>
                        <?php
                        }
                        ?>
                    </div>
                    <!--STATUS-->
                    <div class="col-xs-5 col-sm-3 col-md-3 col-lg-2 text-right  " style="padding:0; margin:0;">
                    <?php
                    if(current_user_can( 'manage_options' ) && false) {
                        get_template_part( 'template/admin', 'project-control' );
                    }elseif( !$user_ID && $project_status == 'publish'){ ?>
                        <a href="#"  class="btn btn-apply-project-item btn-login-trigger" ><?php  _e('Bid',ET_DOMAIN);?></a>  
                        <a href="#" class="popup-login" style="display:none;">Open Login Popup</a>
                    <?php } else {
                        $role = ae_user_role();
                        switch ($project_status) {
                            case 'publish':
                                if( ( fre_share_role() || $role == FREELANCER ) && $user_ID != $project->post_author ){
                                    $has_bid = fre_has_bid( get_the_ID() );
                                    if( $has_bid ) {                                                                   
                                        ?>
                                        <a rel="<?php echo $project->ID;?>" href="#" id="<?php echo $has_bid;?>" title= "<?php _e('Cancel this bidding',ET_DOMAIN); ?>"  class="btn btn-apply-project-item btn-del-project-modal modal_bid_update" >
                                            <?php  _e('Cancel',ET_DOMAIN);?>
                                        </a>

                                        <a  href="#" class="btn btn-apply-project-item btn-project-status modal_bid_update" data-toggle="modal" data-target="#modal_bid_update">
                                            <?php  _e(' Edit Bid ',ET_DOMAIN);?>
                                        </a>

                                    <?php
                                    } else{ ?>
                                        <a href="#"  class="btn btn-apply-project-item btn-project-status" data-toggle="modal" data-target="#modal_bid">
                                            <?php  _e('Bid ',ET_DOMAIN);?>
                                        </a>
                                    <?php }
                                }else { ?>
<!--                                    <a href="#" id="--><?php //the_ID();?><!--"  class="btn btn-apply-project-item" >--><?php // _e('Open',ET_DOMAIN);?><!--</a>-->
                                    <div class="alert alert-success alert-status-project" role="alert"><?php _e('Status:',ET_DOMAIN);?> <strong><?php _e('Open',ET_DOMAIN);?></strong></div>

                                <?php
                                }
                                break;
                            case 'close':
                                if( (int)$project->post_author == $user_ID){ ?>
                                
                                    <a title="<?php  _e('Finish',ET_DOMAIN);?>" href="#" id="<?php the_ID();?>"   class="btn btn-apply-project-item btn-project-status btn-complete-project" >
                                        <?php  _e('Finish',ET_DOMAIN);?>
                                    </a>
                                    <a title="<?php _e('Close',ET_DOMAIN);?>" href="#" id="<?php the_ID();?>"   class="btn btn-apply-project-item btn-project-status btn-close-project" >
                                        <?php _e('Close',ET_DOMAIN);?>
                                    </a>
                                    <?php 
                                }else{ 
                                    $bid_accepted_author = get_post_field( 'post_author', $bid_accepted);
                                    if($bid_accepted_author == $user_ID) {
                                ?>
                                    <a title="<?php  _e('Quit',ET_DOMAIN);?>" href="#" id="<?php the_ID();?>"   class="btn btn-apply-project-item btn-project-status btn-quit-project" >
                                        <?php  _e('Quit',ET_DOMAIN);?>
                                    </a>
                                <?php } 
                                }
                                break;
                            case 'complete' :
                                $freelan_id  = (int)get_post_field('post_author',$project->accepted);
                        
                                $comment = get_comments( array('status'=> 'approve', 'type' => 'fre_review', 'post_id'=> get_the_ID() ) );

                                if( $user_ID == $freelan_id && empty( $comment ) ){ ?>
                                    <a href="#" id="<?php the_ID();?>"   class="btn btn-apply-project-item btn-project-status btn-complete-project" ><?php  _e('Review',ET_DOMAIN);?></a>
                                    <?php 
                                } else { ?>

<!--                                <a href="#" id="--><?php //the_ID();?><!--"   class="btn btn-apply-project-item project-complete" >--><?php // _e('Completed',ET_DOMAIN);?><!--</a>-->
                                    <div class="alert alert-danger alert-status-project" role="alert"><?php _e('Status:',ET_DOMAIN);?> <strong><?php _e('Closed',ET_DOMAIN);?></strong></div>
                                <?php
                                }
                                break;
                            default:
                                $text_status =   array( 'pending'   => __('Pending',ET_DOMAIN),
                                                        'draft'     => __('Draft',ET_DOMAIN),
                                                        'archive'   => __('Draft',ET_DOMAIN),
                                                        'reject'    => __('Reject', ET_DOMAIN),
                                                        'trash'     => __('Trash', ET_DOMAIN), 
                                                        'close'     => __('Working', ET_DOMAIN), 
                                                        'complete'  => __('Completed', ET_DOMAIN), 
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


<!--Modal Window For Deleting Bid!-->
<div class="modal modal-vcenter fade in" id="modal-deleting-bid">
    <div class="modal-dialog top-margin">
        <div class="modal-content" style="margin-top:150px; height:250px;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" aria-hidden="true"><span aria-hidden="true" style="font-size: 18px;">X</span></button>
                <h4 class="modal-title text-center text-color-popup" style="font-size:30px;">Deleting Bid</h4>
                <hr>
            </div>
            <div class="modal-body">
                <h4 class="text-center" style="font-size:25px; padding-top:10px;">Are you sure, you want to delete the Bid?</h4>
                <div style="padding-top:25px;">
                    <a style="margin:10px;" href="#" class="btn btn-apply-project-item" data-dismiss="modal">
                        <?php  _e('No',ET_DOMAIN);?>
                    </a>
                    <a style="margin:10px;" rel="<?php echo $project->ID;?>" href="#" id="<?php echo $has_bid;?>" class="btn btn-apply-project-item btn-del-project" >
                        <?php  _e('Delete',ET_DOMAIN);?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
