<?php wp_reset_query();
global $user_ID, $post; ?><!-- MODAL BIG -->
<div class="modal fade" id="modal_bid">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><i class="fa fa-times"></i>
                </button>
                <?php if (!(ae_get_option('invited_to_bid') && !fre_check_invited($user_ID, $post->ID))) { ?>
                    <h4 class="modal-title"><?php _e('Set your bid:', 'modal-add-bid'); ?></h4>
                <?php } ?>
            </div>
            <div class="modal-body">
                <?php if (ae_get_option('invited_to_bid') && !fre_check_invited($user_ID, $post->ID)) {
                    echo '<p class="lead  warning">';
                    _e("Oops, You must be invited to bid this project", 'modal-add-bid');
                    echo '</p>';
                } else { ?>

                    <form id="bid_form" class="bid-form">
                        <div class="form-group">
                            <label for="bid_budget">
                                <?php
                                if (get_post_meta($post->ID, 'type_budget', true) == 'hourly_rate') {
                                    _e('Hourly rate', 'modal-add-bid');
                                } else {
                                    _e('Budget', 'modal-add-bid');
                                }
                                ?>
                            </label>

                            <div class="checkbox hidden" style="display: inline-block;margin-left: 20px;">
                                <label><input type="checkbox" name="decide_later"
                                              class="checkbox1"><?php _e('Decide later', 'modal-add-bid'); ?></label>
                            </div>
                            <script>
                                jQuery(document).ready(function () {
                                    jQuery('.checkbox1').change(function () {
                                        if (jQuery(this).is(':checked')) {
                                            jQuery('#bid_budget').removeClass('required').removeAttr('value').parent().hide();
                                        }
                                        else {
                                            jQuery('#bid_budget').addClass('required').removeAttr('value').parent().show();
                                        }

                                    });

                                });
                            </script>
                            <div class="form-group">
                                <?php
                                $settings_stripe_secret_key = get_option('settings_stripe_secret_key');
                                $settings_stripe_public_key = get_option('settings_stripe_public_key');
                                $settings_company_fee_for_stripe = get_option('settings_company_fee_for_stripe');
                                if (!empty($settings_stripe_secret_key) && !empty($settings_stripe_public_key) && !empty($settings_company_fee_for_stripe)) {
                                    ?>
                                    <input type="number" name="bid_budget" id="bid_budget" style="margin-bottom: 10px;"
                                           data-fee-percentage="<?php echo $settings_company_fee_for_stripe; ?>"
                                           class="form-control required number calc_price_with_fees" min="1"/>
                                    <span style="float: right;" class="calc_price_without_fees"></span>
                                <?php } else { ?>
                                    <input type="number" name="bid_budget" id="bid_budget"
                                           class="form-control required number" min="1"/>
                                <?php } ?>
                            </div>
                        </div>

                        <div class="clearfix"></div>
                        <div class="form-group">
                            <label for="bid_time"><?php _e('Deadline', 'modal-add-bid'); ?></label>
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="row ">
                                        <div class="bid-time-group-addon">
                                            <div class="col-md-6">
                                            </div>
                                            <div class="col-md-12">
                                                <div class="row">
                                                    <div class="col-xs-6">
                                                        <label class="sr-only" for="bid_time">Bid time</label>
                                                        <input type="number" name="bid_time" id="bid_time" min="1"
                                                               class="form-control required number"
                                                               placeholder="number"/>
                                                    </div>
                                                    <div class="col-xs-6">
                                                        <div class="input-group">
                                                        <span
                                                            class="input-group-addon"><?php _e('in', 'modal-add-bid'); ?></span>
                                                            <label class="sr-only"
                                                                   for="type_time"><?php _e('Type time', 'modal-add-bid'); ?></label>
                                                            <select name="type_time" class="form-control required">
                                                                <option
                                                                    value="day"><?php _e('days', 'modal-add-bid'); ?></option>
                                                                <option
                                                                    value="week"><?php _e('weeks', 'modal-add-bid'); ?></option>
                                                                <option
                                                                    value="month"><?php _e('months', 'modal-add-bid'); ?></option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="form-group">
                                <label for="post_content"><?php _e('Proposal', 'modal-add-bid'); ?></label>
                                <textarea id="bid_content" name="bid_content"></textarea>
                                <?php //wp_editor('', 'bid_content', ae_editor_settings() );  ?>
                            </div>
                            <div class="clearfix"></div>
                            <input type="hidden" name="post_parent" value="<?php the_ID(); ?>"/>
                            <input type="hidden" name="method" value="create"/>
                            <input type="hidden" name="action" value="ae-sync-bid"/>
                            <?php do_action('after_bid_form'); ?>
                            <button type="submit" class="btn-submit btn-sumary btn-sub-create">
                                <?php _e('Submit', 'modal-add-bid') ?>
                            </button>
                        </div>

                    </form>

                <?php } ?>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

