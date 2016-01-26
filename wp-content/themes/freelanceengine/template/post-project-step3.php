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
        <span class="text-heading-step"><?php _e("Enter your project details", ET_DOMAIN); ?></span>
        <i class="fa fa-caret-right"></i>
    </p>
    <div class="step-content-wrapper content" style="<?php if($step != 1) echo "display:none;" ?>" >
    	<form class="post" role="form">
            <!-- project title -->
            <div class="form-group">
            	<div class="row">
                	<div class="col-md-4">
                		<label for="post_title" class="control-label title-plan">
                            <?php _e("Project Title", ET_DOMAIN); ?>
                            <br/>
                            <span><?php _e("Enter a short title for your project", ET_DOMAIN); ?></span>
                        </label>
                    </div>
                    <div class="col-sm-8">
                        <input type="text" class="input-item form-control text-field" id="post_title" placeholder="<?php _e("Project Title", ET_DOMAIN); ?>" name="post_title">
                    </div>
                </div>
            </div>
            <!--// project title -->

            <!-- project type payment fixed/per_hour -->
            <div class="form-group">
                <div class="row">
                    <div class="col-md-4">
                        <label for="post_title" class="control-label title-plan">
                            <?php _e("Project work type", ET_DOMAIN); ?>
                            <br/>
                            <span><?php _e("Select ", ET_DOMAIN); ?></span>
                        </label>
                    </div>
                    <div class="col-sm-8">
<!--                        <input type="text" class="input-item form-control text-field" id="post_title" placeholder="--><?php //_e("Project Title", ET_DOMAIN); ?><!--" name="post_title">-->
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
                            <?php printf(__("Limit hours per week (%s)", ET_DOMAIN), fre_currency_sign(false) ); ?>
                        </label>
                    </div>
                    <div class="col-sm-8">
                        <input step="2" disabled required type="number" class="input-item form-control text-field is_number" id="hours_limit" placeholder="<?php _e("Hours_limit", ET_DOMAIN); ?>" name="hours_limit">
                    </div>
                </div>
            </div>
            <!--// project hours_limit -->

            <!-- project budget -->
            <div class="form-group">
            	<div class="row">
                	<div class="col-md-4">
                    	<label for="et_budget" class="control-label title-plan">
                            <?php printf(__("Budget (%s)", ET_DOMAIN), fre_currency_sign(false) ); ?>
                        </label>
                    </div>
                    <div class="col-sm-8">
                        <input step="1" min="1" required type="number" class="input-item form-control text-field is_number" id="et_budget" placeholder="<?php _e("Budget", ET_DOMAIN); ?>" name="et_budget">
                    </div>
                </div>
            </div>
            <!--// project budget -->
<!-- project category -->
            <div class="form-group">
            	<div class="row">
                	<div class="col-md-4">
                    	<label for="project_category" class="control-label title-plan"><?php _e("Category", ET_DOMAIN); ?><br/><span><?php _e(" Select the best one(s) ", ET_DOMAIN); ?></span></label>
                    </div>
                
                    <div class="col-sm-8">
                       <?php ae_tax_dropdown( 'project_category' , 
							  array(  'attr' => 'data-chosen-width="95%" data-chosen-disable-search="" multiple data-placeholder="'.__("Choose categories", ET_DOMAIN).'"', 
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
                		<label for="skill" class="control-label title-plan"><?php _e("Skills", ET_DOMAIN); ?>
                            <br/>
                            <span><?php _e("Press Enter to keep adding skills", ET_DOMAIN); ?></span>
                        </label>
                	</div>
                    <div class="col-sm-8">
                        
                        <?php 
                        $switch_skill = ae_get_option('switch_skill');
                        if(!$switch_skill){
                            ?>
                            <input type="text" class="form-control text-field skill" id="skill" placeholder="<?php _e("Skills", ET_DOMAIN); ?>" name=""  autocomplete="off" spellcheck="false" >
                            <ul class="skills-list" id="skills_list"></ul>
                            <?php
                        }else{
                            ae_tax_dropdown( 'skill' , array(  'attr' => 'data-chosen-width="95%" data-chosen-disable-search="" multiple data-placeholder="'.__(" Skills (max is 5)", ET_DOMAIN).'"', 
                                                'class' => 'sw_skill chosen multi-tax-item tax-item required', 
                                                'hide_empty' => false, 
                                                'hierarchical' => true , 
                                                'id' => 'skill' , 
                                                'show_option_all' => false
                                        ) 
                            );
                        }
                        
                        ?>
                    </div>
                </div>
            </div>
            <!--// project skills -->


            

            <!-- file attachment -->
            <div class="form-group">
                <div class="row" id="gallery_place">
                    <div class="col-md-4">
                        <label for="carousel_browse_button" class="control-label title-plan">
                            <?php _e("Attachment", ET_DOMAIN); ?><br/>
                            <span>
                            <?php _e("File extension: Png, Jpg, Pdf, Zip", ET_DOMAIN); ?>
                            </span>
                        </label>
                    </div>
                
                    <div class="edit-gallery-image col-sm-8 col-md-8" id="gallery_container">
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
            </div>
            <!--//file attachment -->

            <!-- project description -->
            <div class="form-group">
            	<div class="row">
                    <div class="col-md-4">
                        <label for="post_content" class="control-label title-plan">
                            <?php _e("Description", ET_DOMAIN); ?>
                            <br />
                            <span><?php _e("Describe your project in a few paragraphs", ET_DOMAIN); ?></span>
                        </label>
                    </div>
                    
                    <div class="col-sm-8">
                        <?php wp_editor( '', 'post_content', ae_editor_settings()  );  ?>
                    </div>
                </div>
            </div>

            <!--// project description -->						<!-- project is featured -->
            <div class="form-group">            	<div class="row">                    <div class="col-md-4">                        <label for="post_content" class="control-label title-plan">                            <?php _e("Is Featured", ET_DOMAIN); ?>                            <br />                            <span><?php _e("Make this post featured in listing.", ET_DOMAIN); ?></span><span><?php _e("cost: 25eur", ET_DOMAIN); ?></span>                        </label>                    </div>                                        <div class="col-sm-8">						<input type="hidden" value="0" name="et_featured">



            <label id="checkbox_label">
            <input step="5" required type="checkbox" class="form-control text-field" id="et_featured" name="et_featured" style="display:none;">
                <span id="checkbox_img"></span>
            </label>
                    </div>                </div>            </div>            <!--// project is featured -->

			
	
			
			
		<div class="modal modal-vcenter fade in" id="modal">
			<div class="modal-dialog top-margin">
				<div class="modal-content" style="margin-top:150px; height:350px;">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close" aria-hidden="true"><span aria-hidden="true" style="font-size: 18px;">X</span></button>
						<h4 class="modal-title text-center text-color-popup" style="font-size:30px;">Featured Project</h4>
						<hr>
					</div>
					<div class="modal-body">
						<h4 class="text-center" style="font-size:25px; padding-top:50px;">The cost of featured projects is 25 Euro.</h4>
					</div>
				</div>
			</div>
		</div>
			

		<script type="text/javascript">	
			jQuery('document').ready(function(){
				jQuery("#et_featured").click(function(){
					if (jQuery("#et_featured").is(':checked')) { jQuery('#modal').modal('show'); }
				});
			});
		</script>
			

			
			
			
            <?php do_action( 'ae_submit_post_form', PROJECT, $post ); ?>
            
            <div class="form-group">
            	<div class="row">
                    <div class="col-md-4"></div>
                    <div class="col-sm-8">
                        <button type="submit" class="btn btn-submit-login-form"><?php _e("Submit", ET_DOMAIN); ?></button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- Step 3 / End -->