<div class="modal fade" id="modal_edit_project">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <i class="fa fa-times"></i>
                </button>
                <h4 class="modal-title"><?php _e('Edit Project','modal-edit-project');?></h4>
            </div>
            <div class="modal-body">
                <div class="content">
                    <form class="post edit-project" role="form" id="frm_edit_project">
                        <!-- project title -->
                        <div class="form-group">
                            <label for="post_title" class="control-label title-plan"><?php _e("Project Title", 'modal-edit-project'); ?></label>
                            <input type="text" class="input-item form-control text-field" id="post_title" placeholder="<?php _e("Project Title", 'modal-edit-project'); ?>" name="post_title">
                        </div>
                        <!--// project title -->
						<div class="clearfix"></div>
                        <!-- project budget -->
                        <div class="form-group">
                            <label for="et_budget" class="control-label title-plan"><?php printf(__("Budget (%s)", 'modal-edit-project'), fre_currency_sign(false) ); ?></label>
                            <input required type="number" min="1" class="input-item form-control text-field" id="et_budget" placeholder="<?php _e("Budget", 'modal-edit-project'); ?>" name="et_budget">
                        </div>
                        <!--// project budget -->
						<div class="clearfix"></div>
                        <!-- project skills -->
                        <div class="form-group skill-control">
                            <label for="skill" class="control-label title-plan">
                                <?php _e("Skills", 'modal-edit-project'); ?>
                            </label>
                            <?php 
                            $switch_skill = ae_get_option('switch_skill');
                            if(!$switch_skill){
                                ?>
                                <input class="form-control skill" type="text" id="skill" placeholder="<?php _e("Skills (max is 10)", 'modal-edit-project'); ?>" name=""  autocomplete="off" class="skill" spellcheck="false" >
                                <ul class="skills-list" id="skills_list"></ul>
                                <?php
                            }else{
                               
                                ae_tax_dropdown(    'skill' , array(  'attr' => 'data-chosen-width="95%" data-chosen-disable-search="" multiple data-placeholder="'.__(" Skills (max is 10)", 'modal-edit-project').'"',
                                                    'class' => 'sw_skill required', 
                                                    'hide_empty' => false, 
                                                    'hierarchical' => true , 
                                                    'id' => 'skill' , 
                                                    'show_option_all' => false
                                                ) 
                                );
                            }
                            ?>
                        </div>
                        <!--// project skills -->
                        <div class="clearfix"></div>

                        <!-- project category -->
                        <div class="form-group project_category">
                            <label for="project_category" class="control-label title-plan">
                                <?php _e("Category", 'modal-edit-project'); ?>
                            </label>
                            <?php ae_tax_dropdown( 'project_category' , 
                                  array(  'attr' => 'data-chosen-width="500px" data-chosen-disable-search="" multiple data-placeholder="'.__("Choose categories", 'modal-edit-project').'"',
                                          'class' => 'chosen multi-tax-item tax-item required', 
                                          'hide_empty' => false, 
                                          'hierarchical' => true , 
                                          'id' => 'project_category' , 
                                          'show_option_all' => false
                                      ) 
                            ) ;?> 

                        </div>
                        <!--// project category -->
                        <!-- file attachment -->
                        <div class="form-group" id="gallery_place">
                            <label for="carousel_browse_button" class="control-label title-plan">
                                <?php _e("Attachment", 'modal-edit-project'); ?><br/>
                            </label>
                            <div class="edit-gallery-image" id="gallery_container">
                               <ul class="gallery-image carousel-list" id="image-list">
                                    <li>
                                        <div class="plupload_buttons" id="carousel_container">
                                            <span class="img-gallery" id="carousel_browse_button">
                                                <a href="#" class="add-img"><i class="fa fa-paperclip"></i> <?php _e("Attach file", 'modal-edit-project'); ?></a>
                                            </span>
                                        </div>
                                    </li>
                                </ul>
                                <span class="et_ajaxnonce" id="<?php echo wp_create_nonce( 'ad_carousels_et_uploader' ); ?>"></span>
                            </div>
                        </div>
            <!--//file attachment -->
                        <div class="clearfix"></div>

                        <!-- project description -->
                        <div class="form-group">

                            <label for="edit_projects" class="control-label title-plan"><?php _e("Description", 'modal-edit-project'); ?></label>

                            <?php wp_editor( '', 'edit_projects', ae_editor_settings()  );  ?>

                        </div>           
                        <div class="clearfix"></div>   

                        <?php //$post = ''; do_action( 'ae_edit_post_form', PROJECT, $post ); ?>

                        <div class="form-group">
                             <button type="submit" class="btn btn-submit-login-form"><?php _e("Submit", 'modal-edit-project'); ?></button>
                        </div>
                    </form>
                </div>
                
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
