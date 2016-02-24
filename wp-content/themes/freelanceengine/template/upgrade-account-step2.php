<?php 
    $step = 2;
    $disable_plan = ae_get_option('disable_plan', false);
    if($disable_plan) $step--;
?>
<div class="step-wrapper step-auth" id="step-auth">
    <a href="#" class="step-heading active">
    	<span class="number-step"><?php echo $step; ?></span>
        <span class="text-heading-step">
            <?php _e("Login or Register", 'upgrade-account-step2'); ?>
        </span>
        <i class="fa fa-caret-right"></i>
    </a>
    <div class="step-content-wrapper content  " style="<?php if($step != 1) echo "display:none;" ?>"    >
        <!-- Nav tabs 
        <ul class="nav nav-tabs" role="tablist">
            <li class="active"><a href="#signin" role="tab" data-toggle="tab"><?php _e('Login', 'upgrade-account-step2'); ?></a></li>
            <li><a href="#signup" role="tab" data-toggle="tab"><?php _e('Register', 'upgrade-account-step2') ?></a></li>
        </ul>-->
        <div class="tab-content">
            <div class="tab-pane fade " id="signup">
            	<div class="text-intro-acc">
            		<?php _e('Already have an account?', 'upgrade-account-step2') ?>&nbsp;&nbsp;<a href="#signin" role="tab" data-toggle="tab"><?php _e('Login', 'upgrade-account-step2'); ?></a>
                </div>
                <form id="signup_form" class="signup_form">
                    <input type="hidden" name="role" id="role" value="employer" />
                    <div class="form-group">
                    	<div class="row">
                        	<div class="col-md-4">
                            	<label class="control-label title-plan" for="user_login"><?php _e('Username', 'upgrade-account-step2') ?><span><?php _e('Enter username', 'upgrade-account-step2') ?></span></label>
                            </div>
                            <div class="col-sm-8">
                                <input type="text" class="form-control text-field" id="user_login" name="user_login" placeholder="<?php _e("Enter username", 'upgrade-account-step2'); ?>">
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="form-group">
                    	<div class="row">
                        	<div class="col-md-4">
                            	<label class="control-label title-plan" for="user_email"><?php _e('Email address', 'upgrade-account-step2') ?><span><?php _e('Enter a email', 'upgrade-account-step2') ?></span></label>
                            </div>
                            <div class="col-sm-8">
                                <input type="email" class="form-control text-field" id="user_email" name="user_email" placeholder="<?php _e("Your email address", 'upgrade-account-step2'); ?>">
                            </div>
                        </div>
                    </div>
                    <!-- <div class="clearfix"></div>
                    <div class="form-group">
                    	<div class="row">
                        	<div class="col-md-4">
                            	<label class="control-label title-plan" for="user_email"><?php _e('I\'m looking to:', 'upgrade-account-step2') ?><span><?php _e('Choose type account', 'upgrade-account-step2') ?></span></label>
                            </div>
                            <div class="col-sm-8">
                                <input type="checkbox" class="sign-up-switch" name="modal-check" data-switchery="true" style="display: none;">
                            </div>
                        </div>
                    </div> -->
                    <div class="clearfix"></div>
                    <div class="form-group">
                    	<div class="row">
                        	<div class="col-md-4">
                            	<label class="control-label title-plan" for="user_pass"><?php _e('Password', 'upgrade-account-step2') ?><span><?php _e('Enter password', 'upgrade-account-step2') ?></span></label>
                            </div>
                            <div class="col-sm-8">
                                <input type="password" class="form-control text-field" id="user_pass" name="user_pass" placeholder="<?php _e('Password', 'upgrade-account-step2');?>">
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="form-group">
                    	<div class="row">
                        	<div class="col-md-4">
                            	<label class="control-label title-plan" for="repeat_pass"><?php _e('Retype Password', 'upgrade-account-step2') ?><span><?php _e('Retype password', 'upgrade-account-step2') ?></span></label>
                            </div>
                            <div class="col-sm-8">
                                <input type="password" class="form-control text-field" id="repeat_pass" name="repeat_pass" placeholder="<?php _e('Password', 'upgrade-account-step2');?>">
                            </div>
                        </div>
                    </div>    
                    <div class="clearfix"></div>
                    <?php if(get_theme_mod( 'termofuse_checkbox', false )){ ?>
                    <div class="form-group policy-agreement">
                        <div class="row">
                            <div class="col-md-offset-4 col-md-8" >
                                <input name="agreement" id="agreement" type="checkbox" />
                                <?php printf(__('I agree with the <a href="%s">Term of Use and Privacy policy</a>', 'upgrade-account-step2'), et_get_page_link('tos') ); ?>
                            </div>
                        </div>
                    </div>  
                    <div class="clearfix"></div>
                    <?php } ?>
                    <div class="form-group">
                    	<div class="row">
                            <div class="col-md-4">
                            </div>
                            <div class="col-sm-8">
                                <button type="submit" class="btn btn-submit btn-submit-login-form">
                                    <?php _e('Create account', 'upgrade-account-step2') ?>
                                </button>
                            </div>
                        </div>
                        <?php if(!get_theme_mod( 'termofuse_checkbox', false )){ ?>
                        <div class="row">
                            <div class="col-md-offset-4 col-md-8" style="margin-top:10px;">
                            <?php
                                /**
                                 * tos agreement
                                */
                                $tos = et_get_page_link('tos', array() ,false);
                                if($tos) {
                            ?>
                                <p class="text-policy">
                                    <?php printf(__('By creating an account, you agree to our <a href="%s">Term of Use and Privacy policy</a>', 'upgrade-account-step2'), et_get_page_link('tos') ); ?>
                                </p>
                            <?php 
                                } 
                            ?>
                            </div>
                        </div>
                        <?php } ?>
                    </div>       
                </form> 
            </div>
            <div class="tab-pane fade in active" id="signin">
                <div class="text-intro-acc">
            		<?php _e('You do not have an account?', 'upgrade-account-step2') ?>&nbsp;&nbsp;<a href="#signup" role="tab" data-toggle="tab"><?php _e('Register', 'upgrade-account-step2') ?></a>
                </div>
                <form id="signin_form" class="signin_form">
                    <div class="form-group">
                    	<div class="row">
                        	<div class="col-md-4">
                            	<label class="control-label title-plan" for="user_login"><?php _e('Username', 'upgrade-account-step2') ?><span><?php _e('Enter Username', 'upgrade-account-step2') ?></span></label>
                            </div>
                            <div class="col-sm-8">
                                <input type="text" class="form-control text-field" id="user_login" name="user_login" placeholder="<?php _e('Enter username', 'upgrade-account-step2');?>">
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="form-group">
                    	<div class="row">
                        	<div class="col-md-4">
                            	<label class="control-label title-plan" for="user_pass"><?php _e('Password', 'upgrade-account-step2') ?><span><?php _e('Enter Password', 'upgrade-account-step2') ?></span></label>
                            </div>
                            <div class="col-sm-8">
                                <input type="password" class="form-control text-field" id="user_pass" name="user_pass" placeholder="<?php _e('Password', 'upgrade-account-step2');?>">
                            </div>
                        </div>
                    </div>  
                    <div class="clearfix"></div>
                    <div class="form-group">
                    	<div class="row">
                        	<div class="col-md-4">
                            </div>
                            <div class="col-sm-8">
                                <button type="submit" class="btn btn-submit btn-submit-login-form">
                                    <?php _e('Submit', 'upgrade-account-step2') ?>
                                </button>
                            </div>
                        </div>
                    </div>    
                </form> 
            </div>
        </div>
    </div>
</div>
<!-- Step 2 / End -->