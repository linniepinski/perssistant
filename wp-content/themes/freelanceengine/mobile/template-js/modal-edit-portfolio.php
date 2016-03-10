<?php
global $user_ID;
$profile_id = get_user_meta($user_ID, 'user_profile_id', true);
?>
<div class="modal fade" id="modal_edit_portfolio">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <i class="fa fa-times"></i>
                </button>
                <h4 class="modal-title"><?php _e("Edit item for your Portfolio", ET_DOMAIN) ?></h4>
            </div>
            <div class="modal-body">
                <form id="edit_portfolio" class="auth-form edit_portfolio">
                    <input type="hidden" id="ID" name="id" value="">
                    <input type="hidden" name="method" value="update"/>

                    <div id="portfolio_img_edit_container">
                        <input type="hidden" name="post_thumbnail" id="post_thumbnail" value="0"/>
                		<span class="image" id="portfolio_img_edit_thumbnail">
                			<!-- IMG UPLOAD GO HERE -->
                            <img id="portfolio_img_edit_thumb" src="">
                		</span>
                        <span class="et_ajaxnonce hidden"
                              id="<?php echo wp_create_nonce('portfolio_img_edit_et_uploader'); ?>"></span>

                        <p class="add-file"><?php _e('ADD FILES', ET_DOMAIN) ?></p>

                        <p class="drag-drop"><?php _e('Simply Drag & Drop', ET_DOMAIN) ?></p>

                        <p class="browser-image">
                            <input type="button" id="portfolio_img_edit_browse_button"
                                   class="btn btn-default btn-submit" value="Change"/>
                        </p>
                    </div>
                    <div class="clearfix"></div>
                    <div class="form-group">
                        <label><?php _e('Portfolio Title', ET_DOMAIN) ?></label>

                        <p><input type="text" name="post_title" id="post_title"/></p>
                    </div>
                    <div class="form-group">
                        <label><?php _e('Portfolio Description', ET_DOMAIN) ?></label>
                        <textarea name="post_content" id="post_content"></textarea>
                    </div>
                    <div class="clearfix"></div>
                    <div class="form-group portfolio-skills">
                        <label><?php _e('Select Skill', ET_DOMAIN) ?></label>

<!--                        <p>-->
<!--                            <select id="skills" name="skill">-->
<!--                                --><?php
//
//                                if ($profile_id) {
//                                    $skills = wp_get_object_terms($profile_id, 'skill');
//                                } else {
//                                    $skills = get_terms('skill', array('hide_empty' => false));
//                                }
//
//                                if (!empty($skills)) {
//
//                                    foreach ($skills as $skill) {
//                                        echo '<option data-id="'.$skill->term_id.'" value="' . $skill->slug . '">' . $skill->name . '</option>';
//                                    }
//                                }
//                                ?>
<!--                            </select>-->
<!--                            --><?php
//                            //var_dump($skills);
//                            ?>
<!--                        </p>-->

                            <?php
                            $switch_skill = ae_get_option('switch_skill');

                            if (!$switch_skill) {
                                ?>
                                <input class="form-control skill" type="text" id="skill"
                                       placeholder="<?php _e("Skills (max is 10)", ET_DOMAIN); ?>"
                                       name=""
                                       autocomplete="off" class="skill" spellcheck="false">
                                <ul class="skills-list" id="skills_list"></ul>
                                <?php
                            } else {
                                $c_skills = array();
                                if (!empty($current_skills)) {
                                    foreach ($current_skills as $key => $value) {
                                        $c_skills[] = $value->term_id;
                                    };
                                }
                                ae_tax_dropdown('skill', array('attr' => 'data-chosen-width="95%" multiple data-chosen-disable-search="" data-placeholder="' . __(" Skills (max is 10)", ET_DOMAIN) . '"',
                                        'class' => 'sw_skill modal-skills required',
                                        'hide_empty' => false,
                                        'hierarchical' => true,
                                        'id' => 'skill',
                                        'show_option_all' => false,
                                        'selected' => $c_skills
                                    )
                                );
                            }

                            ?>
                        </div>

                    <div class="clearfix"></div>
                    <button type="submit" class="btn-submit btn-sumary btn-sub-create">
                        <?php _e('Add item', ET_DOMAIN) ?>
                    </button>
                </form>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div><!-- /.modal -->