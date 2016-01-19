<?php 
/**
 * The template for displaying admin control button to edit project
 * @since 1.0
 * @author Dakachi
 */
global $post;
$featured = get_post_meta( $post->ID, 'et_featured', true);
if( current_user_can( 'manage_options' )) { ?>
    <?php
        if( $post->post_status == 'complete' && ae_get_option('use_escrow') ) { 
            // success update order data
            $bid_id_accepted = get_post_meta( $post->ID, 'accepted', true );
            $order = get_post_meta($bid_id_accepted, 'fre_bid_order', true);
            $order_status = get_post_field( 'post_status', $order );
            if($order_status != 'finish') : 
        ?>
            <a class="btn btn-project-status btn-excecute-project manual-transfer"  title="<?php _e("Transfer Money To Freelancer", ET_DOMAIN); ?>" href="#">
                <?php _e("Transfer Money", ET_DOMAIN); ?>
            </a>
        <?php else : ?>
            <a class="btn btn-project-status btn-excecute-project"  href="#">
                <?php _e("Money Transfered", ET_DOMAIN); ?>
            </a>
        <?php endif;
        }else { ?>    
        <ul class="button-event event-listing">
            <li class="tooltips update edit"><a class="action" data-action="edit" data-toggle="tooltip" title="<?php _e("Edit", ET_DOMAIN); ?>" data-original-title="<?php _e("Edit", ET_DOMAIN); ?>" href="#"><i class="fa fa-pencil" data-icon="p"></i></a></li>
            <?php
            if($post->post_status == 'publish' ) {
                if(!$featured) { ?>
                    <li class="tooltips flag toggleFeature"><a class="action" data-action="toggleFeature" href="#" data-toggle="tooltip" title="<?php _e("Set as featured", ET_DOMAIN); ?>" data-original-title="<?php _e("Set as featured", ET_DOMAIN); ?>" ><span class="icon fa fa-flag" data-icon="^"></span></a></li>
                <?php } else { ?>
                    <li class="tooltips flag toggleFeature featured"><a class="action" data-action="toggleFeature" href="#" data-toggle="tooltip" title="<?php _e("Remove featured", ET_DOMAIN); ?>" data-original-title="<?php _e("Remove featured", ET_DOMAIN); ?>" ><span class="icon color-yellow fa fa-flag" data-icon="^"></span></a></li>
                <?php }
            } ?>
            <?php if($post->post_status == 'disputing' ) { ?>
                <li class="tooltips remove resolve"><a class="action" data-action="resolve-dispute" data-toggle="tooltip" title="<?php _e("Resolve Dispute", ET_DOMAIN); ?>" data-original-title="<?php _e("Resolve", ET_DOMAIN); ?>" href="#"><span class="fa fa-check" data-icon="3"></span></a></li>
            <?php } ?>

            <?php if( $post->post_status == 'pending') { ?>
                <li class="tooltips remove approve"><a class="action" data-action="approve" data-toggle="tooltip" title="<?php _e("Approve", ET_DOMAIN); ?>" data-original-title="<?php _e("Approve", ET_DOMAIN); ?>" href="#"><span class="fa fa-check" data-icon="3"></span></a></li>
                <li class="tooltips remove reject"><a class="action" data-action="reject" data-toggle="tooltip" title="<?php _e("Reject", ET_DOMAIN); ?>" data-original-title="<?php _e("Reject", ET_DOMAIN); ?>" href="#"><span class="icon color-purple fa fa-times" data-icon="*"></span></a></li>
            <?php }else { ?>
                <li class="tooltips remove archive"><a class="action" data-action="archive" data-toggle="tooltip" title="<?php _e("Archive", ET_DOMAIN); ?>" data-original-title="<?php _e("Archive", ET_DOMAIN); ?>" href="#"><span class="icon fa fa-trash-o" data-icon="#"></span></a></li>
            <?php } 
        } ?>
    </ul>
<?php } ?>