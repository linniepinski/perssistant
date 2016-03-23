<?php 

    if($_POST){
    unset($_POST);
    }

    global $user_ID;
    $step = 3;

    $disable_plan = ae_get_option('disable_plan', false);
    if($disable_plan) $step--;
    if($user_ID) $step--;
    $post = '';
    if(isset($_REQUEST['id'])) {
        $post = get_post($_REQUEST['id']);
        if($post) {
            global $ae_post_factory;
            $post_object = $ae_post_factory->get($post->post_type);
            echo '<script type="data/json"  id="edit_postdata">'. json_encode($post_object->convert($post)) .'</script>';
        }
       
    }
    //$current_skills = get_the_terms( $profile, 'skill' );
?>
<form method="post" id="checkout_form">	<div class="payment_info"> </div>	<div style="position:absolute; left : -7777px; " >		<input type="submit" id="payment_submit" />	</div></form>
<div class="step-wrapper step-post" id="step-post">
	<p class="step-heading active">
    	<span class="number-step"><?php if($step > 1 ) echo $step; else echo '<i class="fa fa-rocket"></i>'; ?></span>
        <span class="text-heading-step"><?php _e("Enter your project details", 'post-project-step3'); ?></span>
    </p>
    <div class="step-content-wrapper content" style="<?php if($step != 1) echo "display:none;" ?>" >
    	<form class="post" role="form">
            <!-- project title -->
            <div class="form-group">
            	<div class="row">
                	<div class="col-md-4">
                		<label for="post_title" class="control-label title-plan">
                            <?php _e("Project Title", 'post-project-step3'); ?>
                            <br/>
                            <span><?php _e("Enter a short title for your project", 'post-project-step3'); ?></span>
                        </label>
                    </div>
                    <div class="col-sm-8">
                        <input type="text" class="input-item form-control text-field" id="post_title" placeholder="<?php _e("Project Title", 'post-project-step3'); ?>" name="post_title">
                    </div>
                </div>
            </div>
            <!--// project title -->

            <!-- project type payment fixed/per_hour -->
            <div class="form-group">
                <div class="row">
                    <div class="col-md-4">
                        <label for="post_title" class="control-label title-plan">
                            <?php _e("Project work type", 'post-project-step3'); ?>
                            <br/>
                            <span><?php _e("Select ", 'post-project-step3'); ?></span>
                        </label>
                    </div>
                    <div class="col-sm-8">
<!--                        <input type="text" class="input-item form-control text-field" id="post_title" placeholder="--><?php //_e("Project Title", 'post-project-step3'); ?><!--" name="post_title">-->
                    <select class="input-item form-control text-field" name="type_budget">
                        <option value="fixed">Fixed budget</option>
                        <option value="hourly_rate">Hourly rate budget</option>
                    </select>
                    </div>
                </div>
            </div>
            <!--// project type_budget fixed/per_hour -->

            <!-- project hours_limit -->
            <div class="form-group for_type_budget" style="display: none">
                <div class="row">
                    <div class="col-md-4">
                        <label for="hours_limit" class="control-label title-plan">
                            <?php printf(__("Limit hours per week", 'post-project-step3') ); ?>
                        </label>
                    </div>
                    <div class="col-sm-8">
                        <input step="1" min="1" disabled required type="number" class="input-item form-control text-field is_number" id="hours_limit" placeholder="<?php _e("Hours limit", 'post-project-step3'); ?>" name="hours_limit">
                    </div>
                </div>
            </div>
            <!--// project hours_limit -->

            <!-- project budget -->
            <div class="form-group">
            	<div class="row">
                	<div class="col-md-4">
                    	<label for="et_budget" class="control-label title-plan">
                            <?php printf(__("Budget (%s)", 'post-project-step3'), fre_currency_sign(false) ); ?>
                        </label>
                    </div>
                    <div class="col-sm-8">
                        <input min="1" required type="number" class="input-item form-control text-field is_number" id="et_budget" placeholder="<?php _e("Budget", 'post-project-step3'); ?>" name="et_budget">
                    </div>
                </div>
            </div>
            <!--// project budget -->
<!-- project category -->
            <div class="form-group">
            	<div class="row">
                	<div class="col-md-4">
                    	<label for="project_category" class="control-label title-plan"><?php _e("Category", 'post-project-step3'); ?><br/><span><?php _e(" Select the best one(s) ", 'post-project-step3'); ?></span></label>
                    </div>
                
                    <div class="col-sm-8">
                       <?php ae_tax_dropdown( 'project_category' , 
							  array(  'attr' => 'data-chosen-width="95%" data-chosen-disable-search="" multiple data-placeholder="'.__("Choose categories", 'post-project-step3').'"', 
									  'class' => 'chosen multi-tax-item tax-item required', 
									  'hide_empty' => false, 
									  'hierarchical' => true , 
									  'id' => 'project_category' , 
									  'show_option_all' => false
								  ) 
						) ;?> 
                    </div>
                </div>
            </div>
            <!--// project category -->

            <!-- project skills -->
            <div class="form-group skill-control">
            	<div class="row">
                	<div class="col-md-4">
                		<label for="skill" class="control-label title-plan"><?php _e("Skills", 'post-project-step3'); ?>
                            <br/>
                            <span><?php _e("Press Enter to keep adding skills", 'post-project-step3'); ?></span>
                        </label>
                	</div>
                    <div class="col-sm-8">
                        
                        <?php 
                        $switch_skill = ae_get_option('switch_skill');
                        if(!$switch_skill){
                            ?>
                            <input type="text" class="form-control text-field skill" id="skill" placeholder="<?php _e("Skills", 'post-project-step3'); ?>" name=""  autocomplete="off" spellcheck="false" >
                            <ul class="skills-list" id="skills_list"></ul>
                            <?php
                        }else{
                            ae_tax_dropdown( 'skill' , array(  'attr' => 'data-chosen-width="95%" data-chosen-disable-search="" multiple data-placeholder="'.__(" Skills (max is 10)", 'post-project-step3').'"',
                                                'class' => 'sw_skill chosen multi-tax-item tax-item required', 
                                                'hide_empty' => false, 
                                                'hierarchical' => true , 
                                                'id' => 'skill' , 
                                                'show_option_all' => false
                                        ) 
                            );
                            ?>

                        <?php
                        }
                        
                        ?>
                    </div>
                    <div class="row">
                        <div class="col-sm-offset-4 col-sm-8 skill-error error">
                        </div>
                    </div>
                </div>
            </div>
            <!--// project skills -->


            

            <!-- file attachment -->
            <div class="form-group">
                <div class="row" id="gallery_place">
                    <div class="col-md-4">
                        <label for="carousel_browse_button" class="control-label title-plan">
                            <?php _e("Attachment", 'post-project-step3'); ?><br/>
                            <span>
                            <?php _e("File extension: Png, Jpg, Pdf, Zip, Ppt,Doc", 'post-project-step3'); ?>
                            </span>
                        </label>
                    </div>
                
                    <div class="edit-gallery-image col-sm-8 col-md-8" id="gallery_container">
                       <ul class="gallery-image carousel-list" id="image-list">
                            <li>
                                <div class="plupload_buttons" id="carousel_container">
                                    <span class="img-gallery" id="carousel_browse_button">
                                        <a href="#" class="add-img"><i class="fa fa-paperclip"></i> <?php _e("Attach file", 'post-project-step3'); ?></a>
                                    </span>
                                </div>
                            </li>
                        </ul>
                        <span class="et_ajaxnonce" id="<?php echo wp_create_nonce( 'ad_carousels_et_uploader' ); ?>"></span>
                    </div>
                </div>
            </div>
            <!--//file attachment -->

            <!-- project description -->
            <div class="form-group">
            	<div class="row">
                    <div class="col-md-4">
                        <label for="post_content" class="control-label title-plan">
                            <?php _e("Description", 'post-project-step3'); ?>
                            <br />
                            <span><?php _e("Describe your project in a few paragraphs", 'post-project-step3'); ?></span>
                        </label>
                    </div>
                    
                    <div class="col-sm-8">
                        <?php wp_editor( '', 'post_content', ae_editor_settings()  );  ?>
                    </div>

                </div>
                <div class="row">
                    <div class="col-sm-offset-4 col-sm-8 post-content-error error">
                    </div>
                </div>
            </div>

            <!--// project description -->						<!-- project is featured -->
            <div class="form-group">
                <div class="row">
                    <div class="col-md-4">
                        <label for="post_content" class="control-label title-plan"><?php _e("Is Featured", 'post-project-step3'); ?><br />
                            <span><?php _e("Make this post featured in listing.", 'post-project-step3'); ?></span>
                            <span><?php _e("cost: ", 'post-project-step3'); ?> <strong><?php _e("25 EUR", 'post-project-step3'); ?></strong> </span>
                        </label>
                    </div>
                    <div class="col-sm-8">
                        <input type="hidden" value="0" name="et_featured">
                        <label id="checkbox_label">
                        <input step="5" required type="checkbox" class="form-control text-field" id="et_featured" name="et_featured" style="display:none;">

                            <span class="switchery">
                                <small></small>
                                <span id="et_featured_checkbox"><?php _e("Yes", 'post-project-step3'); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php _e("No", 'post-project-step3'); ?></span>
                            </span>

                        </label>
                    </div>
                </div>
            </div><!--// project is featured -->

			
	
			
			
		<div class="modal modal-vcenter fade in" id="modal_featured">
			<div class="modal-dialog modal_featured_margin">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close" aria-hidden="true"><span aria-hidden="true">X</span></button>
						<h4 class="modal-title text-center text-color-popup">Featured Project</h4>
						<hr class="hidden-xs">
					</div>
					<div class="modal-body">
						<h4 class="text-center">The cost for a premium job is 25 eur, to be paid after confirmation of your job.</h4>
					</div>
                    <div class="modal-footer featured-modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Ok</button>
                        <button type="button" class="btn btn-default featured-cancel" data-dismiss="modal">Cancel</button>
                    </div>
				</div>
			</div>
		</div>
			

		<script type="text/javascript">	
			jQuery('document').ready(function(){
				jQuery("#et_featured").click(function(){
					if (jQuery("#et_featured").is(':checked')) { jQuery('#modal_featured').modal('show'); }
				});
                jQuery(".featured-cancel").on('click', function(){
                    jQuery("#et_featured").removeAttr('checked');
                });
			});
		</script>
			

			
			
			
            <?php do_action( 'ae_submit_post_form', PROJECT, $post ); ?>
            
            <div class="form-group">
            	<div class="row">
                    <div class="col-md-4"></div>
                    <div class="col-sm-8">
                        <button type="submit" class="btn btn-submit-login-form"><?php _e("Submit", 'post-project-step3'); ?></button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- Step 3 / End -->