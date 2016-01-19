<script type="text/template" id="header_login_template">

<div class="dropdown-info-acc-wrapper">
    <div class="dropdown">
        <div class="dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown">
            <span class="avatar-and-name">
                <span class="avatar">
                    <img alt="" src="{{= et_avatar_url }}" class="avatar avatar-96 photo avatar-default" height="96" width="96">
                </span>
                {{= display_name }} 
            </span>
            <span class="caret"></span>
        </div>
        <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
             <li><span class="avatar-and-name avatar-name-ontop">
                <span class="avatar">
                    <img alt="" src="{{= et_avatar_url }}" class="avatar avatar-96 photo avatar-default" height="96" width="96">
                </span>
                {{= display_name }} 
                </span>
                <span class="caret"></span>
            </li>

            <li role="presentation" class="divider"></li>

            <li role="presentation">
                <a role="menuitem" tabindex="-1" href="<?php echo et_get_page_link('profile'); ?>" class="display-name">
                    <i class="fa fa-user"></i><?php _e("Your Profile", ET_DOMAIN) ?>
                </a>
            </li>
            <li role="presentation" class="divider"></li>
            <?php /*
            <li role="presentation">
                <a href="javascript:void(0);" class="trigger-overlay trigger-notification">
                    <i class="fa fa-flag"></i>
                    <?php 
                        _e("Notification", ET_DOMAIN); 
                        // if($notify_number) {
                        //     echo ' <span class="notify-number">(' . $notify_number . ')</span>';
                        // }
                     ?>
                </a>
            </li>
            
            <li role="presentation" class="divider"></li>
            */ ?>
            <li role="presentation">
                <a role="menuitem" tabindex="-1" href="<?php echo wp_logout_url(); ?>" class="logout">
                    <i class="fa fa-sign-out"></i><?php _e("Logout", ET_DOMAIN) ?>
                </a>
            </li>
        </ul>
    </div>
</div>
	
</script>