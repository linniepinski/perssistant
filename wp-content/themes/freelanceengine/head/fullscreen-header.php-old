<?php 
    global $current_user;
    $class_trans = '';
    if(is_page_template('page-home.php')) {
        $class_trans = 'class="trans-color"';
    }else{
        $class_trans = 'class="not-page-home"';
    }
?>
<header id="header-wrapper" data-size="big" <?php echo $class_trans ;?>>
	<div class="top-header">
        <div class="container">
            <div class="row">
                <div class="col-md-4 col-xs-4">
                    <ul class="btn-menu-call">
                        <?php if(has_nav_menu('et_header')) { // dont render button menu if dont have menu ?>
                        <li><a href="javascript:void(0);" class="trigger-overlay trigger-menu"><i class="fa fa-bars"></i><span><?php _e("MENU", ET_DOMAIN); ?></span></a></li>
                        <?php } ?>
                        <li><a href="javascript:void(0);" class="trigger-overlay trigger-search"><i class="fa fa-search"></i><span><?php _e("SEARCH", ET_DOMAIN); ?></span></a></li>
                    </ul>
                </div>
                <div class="col-md-4 col-xs-4">
                <?php if(is_page_template('page-home.php')) { ?>
                    <a href="<?php echo home_url(); ?>" class="logo site_logo_white"><?php fre_logo('site_logo_white') ?></a>
                    <a href="<?php echo home_url(); ?>" class="logo site_logo_black"><?php fre_logo('site_logo_black') ?></a>
                <?php }else { ?>
                    <a href="<?php echo home_url(); ?>" class="logo"><?php fre_logo('site_logo_black') ?></a>
                <?php } ?>
                </div>
                <div class="col-md-4 col-xs-4">
                	<div class="login-form-header-wrapper">
						<?php if(!is_user_logged_in()){ ?>
                        <div class="non-login">
                            <a href="#" class="login login-btn"><?php _e("LOGIN", ET_DOMAIN) ?></a>
                            <a href="#" class="register register-btn"><?php _e("SIGN UP", ET_DOMAIN) ?></a>
                        </div>
                        <?php } else { ?>
						<div class="dropdown-info-acc-wrapper">
                            <div class="dropdown">
                                <div class="dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown">
                                    <span class="avatar-and-name">
                                        <span class="avatar">
                                            <?php 
                                                $notify_number = 0;
                                                if(function_exists('fre_user_have_notify') ) {
                                                    $notify_number = fre_user_have_notify();
                                                    if($notify_number) {
                                                        echo '<span class="trigger-overlay trigger-notification-2 circle-new">'.$notify_number.'</span>';
                                                    }                                                    
                                                } 
                                                echo get_avatar($user_ID);
                                            ?>
                                        </span>
                                        <?php echo $current_user->display_name; ?>
                                    </span>
                                    <span class="caret"></span>
                                </div>
                                <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
                                    <li><span class="avatar-and-name avatar-name-ontop">
                                        <span class="avatar">
                                            <?php 
                                                $notify_number = 0;
                                                if(function_exists('fre_user_have_notify') ) {
                                                    $notify_number = fre_user_have_notify();
                                                    if($notify_number) {
                                                        echo '<span class="trigger-overlay trigger-notification-2 circle-new">'.$notify_number.'</span>';
                                                    }                                                    
                                                } 
                                                echo get_avatar($user_ID);
                                            ?>
                                        </span>
                                        <?php echo $current_user->display_name; ?>
                                    </span>
                                    <span class="caret"></span></li>
                                    <li role="presentation" class="divider"></li>
                                    <li role="presentation">
                                        <a role="menuitem" tabindex="-1" href="<?php echo et_get_page_link("profile") ?>" class="display-name">
                                            <i class="fa fa-user"></i><?php _e("Your Profile", ET_DOMAIN) ?>
                                        </a>
                                    </li>
                                    <li role="presentation" class="divider"></li>
                                    <li role="presentation">
                                        <a href="javascript:void(0);" class="trigger-overlay trigger-notification">
                                            <i class="fa fa-flag"></i>
                                            <?php 
                                                _e("Notification", ET_DOMAIN); 
                                                if($notify_number) {
                                                    echo ' <span class="notify-number">(' . $notify_number . ')</span>';
                                                }
                                             ?>
                                        </a>
                                    </li>
                                    <li role="presentation" class="divider"></li>
                                    <li role="presentation">
                                        <a role="menuitem" tabindex="-1" href="<?php echo wp_logout_url(); ?>" class="logout">
                                            <i class="fa fa-sign-out"></i><?php _e("Logout", ET_DOMAIN) ?>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>