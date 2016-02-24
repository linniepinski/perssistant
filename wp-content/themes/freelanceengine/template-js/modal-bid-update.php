<?php wp_reset_query();
global $wp_query, $ae_post_factory, $user_ID, $post; ?><!-- MODAL BIG -->
<div class="modal fade" id="modal_bid_update">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><i class="fa fa-times"></i>
                </button>                <?php if (!(ae_get_option('invited_to_bid') && !fre_check_invited($user_ID, $post->ID))) { ?>
                    <h4 class="modal-title"><?php _e('Set your bid:', 'modal-update-bid'); ?></h4>                <?php } ?>
            </div>
            <div
                class="modal-body">                <?php if (ae_get_option('invited_to_bid') && !fre_check_invited($user_ID, $post->ID)) {
                    echo '<p class="lead  warning">';
                    _e("Oops, You must be invited to bid this project", 'modal-update-bid');
                    echo '</p>';
                } else { ?>
                    <div>
                        <form id="bid_form_update" class="bid-form-update">

                            <div class="form-group"><label for="bid_budget"><?php
                                    if(get_post_meta($post->ID,'type_budget',true) == 'hourly_rate'){
                                        _e('Hourly rate', 'modal-update-bid');
                                    }else{
                                        _e('Budget', 'modal-update-bid');
                                    }
                                    ?>
                                </label>




                                <?php
                                $post_the_id=get_the_ID();
                                $bid_the_id=fre_has_bid( get_the_ID() );


                                add_filter('posts_orderby', 'fre_order_by_bid_status');
                                $q_bid = new WP_Query(array('post_type' => BID,
                                                            'post_parent' => get_the_ID(),
                                                            'author' => $user_ID,
                                                            'post_status' => array('publish','complete', 'accept'))
                                );
                                remove_filter('posts_orderby', 'fre_order_by_bid_status');

                                $post_object = $ae_post_factory->get(BID);

                                if( $q_bid->have_posts() ) {
                                    while( $q_bid->have_posts() ){
                                        $q_bid->the_post();
                                        $convert    = $post_object->convert($post);
                                        $bid_update=$ae_post_factory->get( BID )->current_post->bid_budget;
                                        }
                                }
                                ?>



                                <input type="number" name="bid_budget" id="bid_budget_update"
                                       class="form-control required number" min="0" value="<?php echo $bid_update; ?>"/></div>
                            <div class="clearfix"></div>

                            <input type="hidden" name="post_parent" value="<?php echo  $post_the_id; ?>" /> <input type="hidden"
                                                                                                        name="method"
                                                                                                        value="update"/>

                            <input type="hidden" name="ID"
                                   value="<?php echo $bid_the_id;?>"/>

                            <input type="hidden" name="action"
                                   value="ae-sync-bid"/>                        <?php do_action('after_bid_form'); ?>
                            <button type="submit"
                                    class="btn-submit-update btn-sumary btn-sub-create">                            <?php _e('Submit', 'modal-update-bid') ?>                        </button>
                        </form>
                    </div>            <?php } ?>            </div>
        </div>
        <!-- /.modal-content -->    </div>
    <!-- /.modal-dialog --></div><!-- /.modal -->