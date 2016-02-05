<?php wp_reset_query();
global $user_ID, $post; ?><!-- MODAL BIG -->
<div class="modal fade" id="modal_bid">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><i class="fa fa-times"></i>
                </button> <?php if (!(ae_get_option('invited_to_bid') && !fre_check_invited($user_ID, $post->ID))) { ?>
                    <h4 class="modal-title"><?php _e('Set your bid:', ET_DOMAIN); ?></h4>                <?php } ?>
            </div>
            <div
                class="modal-body">                <?php if (ae_get_option('invited_to_bid') && !fre_check_invited($user_ID, $post->ID)) {
                    echo '<p class="lead  warning">';
                    _e("Oops, You must be invited to bid this project", ET_DOMAIN);
                    echo '</p>';
                } else { ?>
                    <div>
                        <form id="bid_form" class="bid-form">
                            <div class="form-group"><label for="bid_budget"><?php
                                    if (get_post_meta($post->ID, 'type_budget', true) == 'hourly_rate') {
                                        _e('Hourly rate', ET_DOMAIN);
                                    } else {
                                        _e('Budget', ET_DOMAIN);
                                    }
                                    ?>
                                </label>

                                <div class="checkbox" style="display: inline-block;margin-left: 20px;
">
                                    <label><input type="checkbox" name="decide_later" class="checkbox1">Decide
                                        later</label>
                                </div>
                                <script>
                                    jQuery(document).ready(function () {
                                        jQuery('.checkbox1').change(function () {

                                            if (jQuery(this).is(':checked')) {
                                                jQuery('#bid_budget').removeClass('required').removeAttr('value').parent().hide();
//                                                    jQuery('#bid_budget').val('0');
//                                                    jQuery('#bid_budget').text('0');
//                                                    jQuery('#bid_budget').attr('type','hidden');
                                            }
                                            else {
                                                jQuery('#bid_budget').addClass('required').removeAttr('value').parent().show();

//                                                    jQuery('#bid_budget').attr('type','number');
//                                                    jQuery('#bid_budget').val('');
//                                                    jQuery('#bid_budget').text('');
                                            }

                                        });

                                    });

                                </script>
                                <div class="form-group">
                                    <input type="number" name="bid_budget" id="bid_budget"
                                           class="form-control required number" min="1"/></div>
                                </div>

                            <div class="clearfix"></div>
                            <div class="form-group"><label for="bid_time"><?php _e('Deadline', ET_DOMAIN); ?></label>

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


                                                            <span class="input-group-addon">in</span>

                                                            <label class="sr-only" for="type_time">Type time</label>
                                                            <select name="type_time" class="form-control required">
                                                                <option value="day">days</option>
                                                                <option value="week">weeks</option>
                                                                <option value="month">months</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div
                                        class="col-xs-6">                                                                    <?php /*									<select name="type_time">																	<option value="day"><?php _e('days',ET_DOMAIN);?></option>										<option value="week"><?php _e('week',ET_DOMAIN);?></option>									</select>                                                                    <?php */ ?>                                </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="form-group"><label
                                    for="post_content"><?php _e('Proposal', ET_DOMAIN); ?></label> <textarea
                                    id="bid_content"
                                    name="bid_content"></textarea> <?php //wp_editor('', 'bid_content', ae_editor_settings() );  ?>
                            </div>
                            <div class="clearfix"></div>
                            <input type="hidden" name="post_parent" value="<?php the_ID(); ?>"/> <input type="hidden"
                                                                                                        name="method"
                                                                                                        value="create"/>
                            <!--                            <input type="hidden" name="wtf" value="asfasdf">-->
                            <input type="hidden" name="action"
                                   value="ae-sync-bid"/> <?php do_action('after_bid_form'); ?>
                            <button type="submit"
                                    class="btn-submit btn-sumary btn-sub-create">                            <?php _e('Submit', ET_DOMAIN) ?>                        </button>
                        </form>
                    </div>            <?php } ?>            </div>
        </div>
        <!-- /.modal-content -->    </div>
    <!-- /.modal-dialog --></div><!-- /.modal -->