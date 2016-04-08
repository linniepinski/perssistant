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
                <?php
                echo do_shortcode('[contact-form-7 id="2336" title="disput"]');
                ?>
                <pre>
                <?php
                $project_info_output = array();

                $project_info_output['ID'] = $post->ID;
                $project_info_output['post_status'] = $post->post_status;
                $project_info_output['post_author'] = $post->post_author;
                $project_info_output['display_name'] = get_userdata($post->post_author)->display_name;
                $project_info_output['post_date_gmt'] = $post->post_date_gmt;

                $project_info_output['post_title'] = $post->post_title;
                $project_info_output['guid'] = $post->guid;
                $project_meta = get_post_meta($post->ID);
                $project_info_output['et_budget'] = $project_meta['et_budget'][0];
                $project_info_output['et_featured'] = $project_meta['et_featured'][0];
                $project_info_output['hours_limit'] = $project_meta['hours_limit'][0];
                $project_info_output['type_budget'] = $project_meta['type_budget'][0];
                $project_info_output['accepted'] = $project_meta['accepted'][0];

//                $project_info_output = get_post_meta($post->ID));
                $bid_post_id = get_post_meta($post->ID)['accepted'][0];
                if ($bid_post_id){
                    $bid_post = get_post($bid_post_id);
                    $project_info_output['bid_post_date_gmt'] = $bid_post->post_date_gmt;
                    $project_info_output['bid_post_author'] = $bid_post->post_author;
                    $project_info_output['bid_display_name'] = get_userdata($bid_post->post_author)->display_name;
                    $project_bid_meta = get_post_meta($bid_post_id);
                    $project_info_output['bid_budget'] = $project_bid_meta['bid_budget'][0];
                    $project_info_output['bid_time'] = $project_bid_meta['bid_time'][0];
                    $project_info_output['bid_type_time'] = $project_bid_meta['type_time'][0];
                }
//                $project_info_output = $bid_post);

echo '<script type="application/javascript">var $project_info_output = '.json_encode($project_info_output).'</script>';
                var_dump($project_info_output);
                ?>
                    </pre>
                <script type="application/javascript">
                    var disput_modal = jQuery('#modal_contact_form_disput');
                    disput_modal.on('show.bs.modal', function () {
                        jQuery.each($project_info_output, function (i, item) {
                            console.log(i + item);
                            disput_modal.find('input[name="'+i+'"]').val(item);
                        });
                    })
                </script>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->