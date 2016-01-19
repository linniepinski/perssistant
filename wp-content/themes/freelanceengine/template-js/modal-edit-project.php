<div class="modal fade" id="modal_edit_project">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <i class="fa fa-times"></i>
                </button>
                <h4 class="modal-title"><?php _e('Edit Project',ET_DOMAIN);?></h4>
            </div>
            <div class="modal-body">
                <div class="content">
                    <form class="post edit-project" role="form" id="frm_edit_project">
                        <!-- project title -->
                        <div class="form-group">
                            <label for="post_title" class="control-label title-plan"><?php _e("Project Title", ET_DOMAIN); ?></label>
                            <input type="text" class="input-item form-control text-field" id="post_title" placeholder="<?php _e("Project Title", ET_DOMAIN); ?>" name="post_title">
                        </div>
                        <!--// project title -->
						<div class="clearfix"></div>
                        <!-- project budget -->
                        <div class="form-group">
                            <label for="et_budget" class="control-label title-plan"><?php printf(__("Budget (%s)", ET_DOMAIN), fre_currency_sign(false) ); ?></label>
                            <input required type="text" class="input-item form-control text-field" id="et_budget" placeholder="<?php _e("Budget", ET_DOMAIN); ?>" name="et_budget">
                        </div>
                        <!--// project budget -->
						<div class="clearfix"></div>
                        <!-- project skills -->
                        <div class="form-group skill-control">
                            <label for="skill" class="control-label title-plan">
                                <?php _e("Skills", ET_DOMAIN); ?>
                            </label>
                            <?php 
                            $switch_skill = ae_get_option('switch_skill');
                            if(!$switch_skill){
                                ?>
                                <input class="form-control skill" type="text" id="skill" placeholder="<?php _e("Skills (max is 5)", ET_DOMAIN); ?>" name=""  autocomplete="off" class="skill" spellcheck="false" >
                                <ul class="skills-list" id="skills_list"></ul>
                                <?php
                            }else{
                               
                                ae_tax_dropdown(    'skill' , array(  'attr' => 'data-chosen-width="95%" data-chosen-disable-search="" multiple data-placeholder="'.__(" Skills (max is 5)", ET_DOMAIN).'"', 
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
                                <?php _e("Category", ET_DOMAIN); ?>
                            </label>
                            <?php ae_tax_dropdown( 'project_category' , 
                                  array(  'attr' => 'data-chosen-width="500px" data-chosen-disable-search="" multiple data-placeholder="'.__("Choose categories", ET_DOMAIN).'"', 
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
                                <?php _e("Attachment", ET_DOMAIN); ?><br/>
                            </label>
                            <div class="edit-gallery-image" id="gallery_container">
                               <ul class="gallery-image carousel-list" id="image-list">
                                    <li>
                                        <div class="plupload_buttons" id="carousel_container">
                                            <span class="img-gallery" id="carousel_browse_button">
                                                <a href="#" class="add-img"><?php _e("Attach file", ET_DOMAIN); ?> <i class="fa fa-plus"></i></a>
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

                            <label for="post_content" class="control-label title-plan"><?php _e("Description", ET_DOMAIN); ?></label>

                            <?php wp_editor( '', 'post_content', ae_editor_settings()  );  ?>

                        </div>           
                        <div class="clearfix"></div>   

                        <?php $post = ''; do_action( 'ae_edit_post_form', PROJECT, $post ); ?>

                        <div class="form-group">
                             <button type="submit" class="btn btn-submit-login-form"><?php _e("Submit", ET_DOMAIN); ?></button>
                        </div>
                    </form>
                </div>
                
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->