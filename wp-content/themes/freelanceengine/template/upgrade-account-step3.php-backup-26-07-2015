<?php 
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
?>
<div class="step-wrapper step-post" id="step-post">
	<a href="#" class="step-heading active">
    	<span class="number-step"><?php if($step > 1 ) echo $step; else echo '<i class="fa fa-rocket"></i>'; ?></span>
        <span class="text-heading-step"><?php _e("Enter your project details", ET_DOMAIN); ?></span>
        <i class="fa fa-caret-right"></i>
    </a>
    <div class="step-content-wrapper content" style="<?php if($step != 1) echo "display:none;" ?>" >
    	<form class="post" role="form">
            <!-- bid budget -->
            <div class="form-group">
            	<div class="row">
                	<div class="col-md-4">
                		<label for="post_title" class="control-label title-plan">
                            <?php printf(__("Budget (%s)", ET_DOMAIN), fre_currency_sign(false) ); ?>
                            <br/>
                            <!-- <span><?php _e("Enter a short title for your project", ET_DOMAIN); ?></span> -->
                        </label>
                    </div>
                    <div class="col-sm-8">
                        <input type="number" name="bid_budget" id="bid_budget" class="input-item form-control text-field required number" >
                    </div>
                </div>
            </div>
            <!--//  bid budget  -->
			
            <!-- deadline -->
            <div class="form-group">
            	<div class="row">
                	<div class="col-md-4">
                    	<label for="et_budget" class="control-label title-plan">
                            <?php _e("Deadline", ET_DOMAIN); ?>
                        </label>
                    </div>
                    <div class="col-sm-8">
                        <div class="row">
                            <div class="col-sm-6 col-xs-6">
                                <input required  type="number" name="bid_time" id="bid_time" class="input-item form-control text-field is_number">
                            </div>
                            <div class="col-sm-5 col-xs-5">
                                <select name="type_time" class="form-control" style="height:45px;">                           
                                    <option value="day"><?php _e('days',ET_DOMAIN);?></option>
                                    <option value="week"><?php _e('week',ET_DOMAIN);?></option>
                                </select>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
            <!--// deadline-->

            <!-- project description -->
            <div class="form-group">
            	<div class="row">
                    <div class="col-md-4">
                        <label for="post_content" class="control-label title-plan">
                            <?php _e("Notes", ET_DOMAIN); ?>
                            <br />
                            <!-- <span><?php _e("Describe your project in a few paragraphs", ET_DOMAIN); ?></span> -->
                        </label>
                    </div>
                    
                    <div class="col-sm-8">
                        <?php wp_editor( '', 'bid_content', ae_editor_settings()  );  ?>
                    </div>
                </div>
            </div>
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