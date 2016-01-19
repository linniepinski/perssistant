<?php
	et_get_mobile_header();
?>
<section class="section-wrapper section-user-profile">
    <div class="tabs-acc-details tab-profile" id="tab_change_pw">
        <form class="form-mobile-wrapper form-user-profile chane_pass_form" id="resetpass_form">
			<input type="hidden" id="user_login" name="user_login" value="<?php if(isset($_GET['user_login'])) echo $_GET['user_login'] ?>" />
			<input type="hidden" id="user_key" name="user_key" value="<?php if(isset($_GET['key'])) echo $_GET['key'] ?>">        	
            <div class="form-group-mobile edit-profile-title">
                <label><?php _e("Your New Password", ET_DOMAIN) ?></label>
                <input type="password" id="new_password" name="new_password" placeholder="<?php _e("New password", ET_DOMAIN); ?>">
            </div>
            <div class="form-group-mobile">
                <label><?php _e("Retype New Password", ET_DOMAIN) ?></label>
                <input type="password" id="re_new_password" name="re_new_password" placeholder="<?php _e("Retype New password", ET_DOMAIN); ?>">
            </div>
            <p class="btn-warpper-bid">
                <input type="submit" class="btn-submit btn-sumary btn-bid" value="<?php _e("Reset", ET_DOMAIN) ?>" />
            </p>
        </form>
    </div>    
</section>
<?php
	et_get_mobile_footer();
?>