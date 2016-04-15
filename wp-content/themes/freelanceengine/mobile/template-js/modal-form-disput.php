<?php wp_reset_query();
global $user_ID, $post; ?><!-- MODAL BIG -->
<div class="modal fade" id="modal_contact_form_disput">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><i class="fa fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_disput" class="form_disput">
                    <?php
                    //                if (ICL_LANGUAGE_CODE == 'en') {
                    //                    echo do_shortcode('[contact-form-7 id="2279" title="Disput EN"]');
                    //                } else {
                    //                    echo do_shortcode('[contact-form-7 id="2280" title="Disput DE"]');
                    //                }
                    $project_info_output = array();
                    $project_info_output['user_id'] = $user_ID;
                    $project_info_output['current_display_name'] = get_userdata($user_ID)->display_name;
                    $project_info_output['e_mail'] = get_userdata($user_ID)->user_email;
                    //                $project_info_output['ID'] = $post->ID;
                    //                $project_info_output['post_status'] = $post->post_status;
                    //                $project_info_output['post_author'] = $post->post_author;
                    //                $project_info_output['display_name'] = get_userdata($post->post_author)->display_name;
                    //                $project_info_output['post_date_gmt'] = $post->post_date_gmt;
                    //
                    //                $project_info_output['post_title'] = $post->post_title;
                    //                $project_info_output['guid'] = $post->guid;
                    //                $project_meta = get_post_meta($post->ID);
                    //                $project_info_output['et_budget'] = $project_meta['et_budget'][0];
                    //                $project_info_output['et_featured'] = $project_meta['et_featured'][0];
                    //                $project_info_output['hours_limit'] = $project_meta['hours_limit'][0];
                    //                $project_info_output['type_budget'] = $project_meta['type_budget'][0];
                    //                $project_info_output['accepted'] = $project_meta['accepted'][0];
                    //
                    //                $bid_post_id = get_post_meta($post->ID)['accepted'][0];
                    //                if ($bid_post_id) {
                    //                    $bid_post = get_post($bid_post_id);
                    //                    $project_info_output['bid_post_date_gmt'] = $bid_post->post_date_gmt;
                    //                    $project_info_output['bid_post_author'] = $bid_post->post_author;
                    //                    $project_info_output['bid_display_name'] = get_userdata($bid_post->post_author)->display_name;
                    //                    $project_bid_meta = get_post_meta($bid_post_id);
                    //                    $project_info_output['bid_budget'] = $project_bid_meta['bid_budget'][0];
                    //                    $project_info_output['bid_time'] = $project_bid_meta['bid_time'][0];
                    //                    $project_info_output['bid_type_time'] = $project_bid_meta['type_time'][0];
                    //                }

                    echo '<script type="application/javascript">var $project_info_output = ' . json_encode($project_info_output) . '</script>';
                    ?>
                    <input type="hidden" name="project_id" value="<?php echo $post->ID; ?>">
                    <input type="hidden" name="user_id" value="<?php echo $user_ID; ?>">
                    <input type="hidden" name="action" value="ae-open-dispute">
                    <div class="form-group">
                        <label for="current_display_name"><?php _e('Your Name', 'modal-disput') ?></label>
                        <input type="text" class="form-control" id="current_display_name" name="current_display_name"
                               readonly placeholder="<?php _e('Enter username', 'modal-disput') ?>">
                    </div>
                    <div class="form-group">
                        <label for="login_user_login"><?php _e('Your E-mail (required)', 'modal-disput') ?></label>
                        <input type="email" class="form-control" name="e_mail"
                               placeholder="<?php _e('Enter username', 'modal-disput') ?>">
                    </div>
                    <div class="form-group">
                        <label for="login_user_login"><?php _e('Subject (required)', 'modal-disput') ?></label>
                        <input type="text" class="form-control" name="subject"
                               placeholder="<?php _e('Enter subject', 'modal-disput') ?>">
                    </div>
                    <div class="form-group">
                        <label for="login_user_login"><?php _e('Amount', 'modal-disput') ?></label>
                        <input type="text" class="form-control" name="amount"
                               placeholder="<?php _e('Enter amount', 'modal-disput') ?>">
                    </div>
                    <div class="form-group">
                        <label for="message">
                            <?php _e('Your Message (required)', 'modal-disput'); ?>
                        </label>
                        <textarea rows="4" class="form-control" name="message"></textarea>
                    </div>
                    <input type="hidden" value="">
                    <div class="form-group">
                        <button type="submit" class="btn btn-ok">
                            <?php _e('Send', 'modal-disput') ?>
                        </button>
                    </div>
                    <script type="application/javascript">
                        var disput_modal = jQuery('#modal_contact_form_disput');
                        disput_modal.on('show.bs.modal', function () {
                            jQuery.each($project_info_output, function (i, item) {
                                console.log(i + item);
                                disput_modal.find('input[name="' + i + '"]').val(item);
                            });
                        })
                    </script>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->