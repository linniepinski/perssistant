<?php

global $current_user;

$class_trans = '';

$switcher_options = array(
    'EscapeActive' => false,
    'MissingTranslate' => true
);
if (is_page_template('page-home.php')) {

    $class_trans = 'class="trans-color"';

} else {

    $class_trans = 'class="not-page-home"';

}
$current_lang = '';
if (ICL_LANGUAGE_CODE == 'en') $current_lang = '';
else $current_lang = '/' . ICL_LANGUAGE_CODE;
?>
<header id="header-wrapper" data-size="big" <?php echo $class_trans; ?> >

    <div class="top-header">
        <nav class="navbar navbar-default">

            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                            data-target="#navbar-collapse" aria-expanded="false">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <?php if (is_page_template('page-home.php')) { ?>

                        <a href="<?php echo home_url()?>"
                           class="logo site_logo_white"><?php fre_logo('site_logo_white') ?></a>

                        <a href="<?php echo home_url()?>"
                           class="logo site_logo_black"><?php fre_logo('site_logo_black') ?></a>

                    <?php } else { ?>

                        <a href="<?php echo home_url()?>"
                           class="logo"><?php fre_logo('site_logo_black') ?></a>

                    <?php } ?>
                </div>

                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="navbar-collapse">

                    <div class="login-form-header-wrapper">

                        <?php if (!is_user_logged_in()) { ?>

                            <div class="non-login">

                                <div class="navbar-left links-wrap">
                                    <a class="login login-btn"
                                       href="<?php echo site_url().$current_lang; ?>/submit-project/"><?php _e("POST A PROJECT", 'header') ?></a>


                                    <div class="dropdown-info-acc-wrapper">

                                        <div class="dropdown">

                                            <a href="#" class="dropdown-toggle" id="dropdownMenu1"
                                               data-toggle="dropdown">

                                                <span
                                                    class="avatar-and-name"><span><?php _e("BROWSE", 'header'); ?></span></span>


                                                <span class="caret"></span>

                                            </a>

                                            <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1"
                                                style="  left: -11px;  top: -110%">


                                                <li role="presentation" class="hidden-xs hidden-sm">

                                                    <span class="avatar-and-name"
                                                          style="color: #333;"><span><?php _e("BROWSE", 'header'); ?></span></span>


                                                    <span class="caret" style="border-top: 4px solid #000;"></span>

                                                </li>

                                                <?php if (wp_get_current_user()->roles[0] == 'freelancer' || !is_user_logged_in()) { ?>
                                                    <li role="presentation" class="divider hidden-xs hidden-sm"></li>

                                                    <li role="presentation">

                                                    <a tabindex="-1" role="menuitem"
                                                       href="<?php echo site_url(); ?>/projects/" class="se-proj">

                                                        <?php _e("Search Projects", 'header') ?>

                                                    </a>

                                                    </li><?php } ?>
                                                <li role="presentation" class="divider"></li>


                                                <li role="presentation">

                                                    <a role="menuitem" tabindex="-1"
                                                       href="<?php echo site_url().$current_lang; ?>/profiles/" class="se-free">

                                                        <?php _e("Search Virtual Assistant", 'header') ?>

                                                    </a>

                                                </li>

                                            </ul>

                                        </div>

                                    </div>
                                    <a class="login login-btn"
                                       href="<?php echo site_url().$current_lang; ?>/how-it-works/"><?php _e("HOW IT WORKS", 'header') ?></a>
                                    <ul class="sitelang">                                <?php do_action('wpml_custom_language_switcher', $switcher_options); ?>
                                    </ul>
                                </div>


                                <a href="<?php echo site_url().$current_lang; ?>/login"
                                   class="login login-btn"><?php _e("LOGIN", 'header') ?></a>

                                <a href="<?php echo site_url().$current_lang; ?>/sign-up"
                                   class="register register-btn"><?php _e("SIGN UP", 'header') ?></a>

                                <!--<a class="perssistant register register-btn highlighted" href="<?php echo site_url(); ?>/perssistant-plus/"><?php _e("PERSSISTANT<span>+</span>", 'header') ?></a>-->
                            </div>


                        <?php } else { ?>
                            <div class="navbar-left links-wrap">
                                <?php if (ae_user_role() == FREELANCER) { ?>
                                    <a href="<?php echo site_url().$current_lang; ?>/projects/"
                                       class="login login-btn"><?php _e("FIND A PROJECT", 'header') ?></a>
                                <?php } else { ?>
                                    <a href="<?php echo site_url().$current_lang; ?>/submit-project/"
                                       class="login login-btn"><?php _e("POST A PROJECT", 'header') ?></a>
                                <?php } ?>

                                <div class="dropdown-info-acc-wrapper">

                                    <div class="dropdown">

                                        <a href="#" class="dropdown-toggle" id="dropdownMenu1" data-toggle="dropdown">

                                            <span class="avatar-and-name"><span><?php _e("BROWSE", 'header'); ?></span></span>


                                            <span class="caret"></span>

                                        </a>

                                        <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1"
                                            style="left: -11px;  top: -110%">


                                            <li role="presentation" class="hidden-xs hidden-sm">

                                                <span class="avatar-and-name"
                                                      style="color: #333;"><span><?php _e("BROWSE", 'header'); ?></span></span>


                                                <span class="caret"></span>

                                            </li>

                                            <?php if (wp_get_current_user()->roles[0] == 'freelancer' || !is_user_logged_in()) { ?>
                                                <li role="presentation" class="divider hidden-xs hidden-sm"></li>

                                                <li role="presentation">

                                                    <a role="menuitem" tabindex="-1"
                                                       href="<?php echo site_url().$current_lang; ?>/projects/" class="se-proj">

                                                        <?php _e("Search Projects", 'header') ?>

                                                    </a>

                                                </li> <?php } ?>

                                            <li role="presentation" class="divider"></li>


                                            <li role="presentation">

                                                <a role="menuitem" tabindex="-1"
                                                   href="<?php echo site_url().$current_lang; ?>/profiles/" class="se-free">

                                                    </i><?php _e("Search Virtual Assistant", 'header') ?>

                                                </a>

                                            </li>

                                        </ul>

                                    </div>

                                </div>
                                <a class="login login-btn"
                                   href="<?php echo site_url().$current_lang; ?>/how-it-works/"><?php _e("HOW IT WORKS", 'header') ?></a>
                                <a class="login login-btn"
                                   href="<?php echo site_url().$current_lang; ?>/chat-room/"><?php _e("MESSAGES", 'header') ?>
                                    &nbsp&nbsp<span class="badge count-chat"></span></a>
                                <ul class="sitelang">                                <?php do_action('wpml_custom_language_switcher', $switcher_options); ?>
                                </ul>
                            </div>

                            <div class="dropdown-info-acc-wrapper" style="margin-right:12px">

                                <div class="dropdown">

                                    <a href="#" class="dropdown-toggle" id="dropdownMenu1" data-toggle="dropdown">

                                    <span class="avatar-and-name current_user_avatar">

                                        <span class="avatar">

                                            <?php

                                            $notify_number = 0;

                                            if (function_exists('fre_user_have_notify')) {

                                                $notify_number = fre_user_have_notify();

                                                if ($notify_number) {

                                                    echo '<span class="trigger-overlay trigger-notification-2 circle-new">' . $notify_number . '</span>';

                                                }

                                            }

                                            echo get_avatar($user_ID);

                                            ?>

                                        </span>

                                        <?php echo $current_user->display_name; ?>

                                    </span>

                                        <span class="caret"></span>

                                    </a>

                                    <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">

                                        <li class="hidden-xs hidden-sm"><span class="avatar-and-name avatar-name-ontop">

                                        <span class="avatar">

                                            <?php

                                            $notify_number = 0;

                                            if (function_exists('fre_user_have_notify')) {

                                                $notify_number = fre_user_have_notify();

                                                if ($notify_number) {

                                                    echo '<span class="trigger-overlay trigger-notification-2 circle-new">' . $notify_number . '</span>';

                                                }

                                            }

                                            echo get_avatar($user_ID);

                                            ?>

                                        </span>

                                                <?php echo $current_user->display_name; ?>

                                    </span>

                                            <span class="caret"></span></li>

                                        <li role="presentation" class="divider hidden-xs hidden-sm"></li>

                                        <li role="presentation">

                                            <a role="menuitem" tabindex="-1"
                                               href="<?php echo site_url().$current_lang; ?>/profile/" class="display-name">

                                                <i class="fa fa-user"></i><?php _e("Your Profile", 'header') ?>

                                            </a>

                                        </li>

                                        <li role="presentation" class="divider"></li>

                                        <li role="presentation">

                                            <a href="<?php echo site_url().$current_lang; ?>/profile/" class="trigger-notification">

                                                <i class="fa fa-flag"></i>

                                                <?php

                                                _e("Notification", 'header');

                                                if ($notify_number) {

                                                    echo ' <span class="notify-number">(' . $notify_number . ')</span>';

                                                }

                                                ?>

                                            </a>

                                        </li>

                                        <li role="presentation" class="divider"></li>

                                        <li role="presentation">

                                            <a role="menuitem" tabindex="-1" href="<?php echo wp_logout_url(); ?>"
                                               class="logout">

                                                <i class="fa fa-sign-out"></i><?php _e("Logout", 'header') ?>

                                            </a>

                                        </li>

                                    </ul>

                                </div>

                            </div>
<!--                            <a class="perssistant register register-btn highlighted"-->
<!--                               href="--><?php //echo site_url(); ?><!--/perssistant-hiring-services/">--><?php //_e("PERSSISTANT<span>+</span>", 'header') ?><!--</a>-->


                        <?php } ?>

                    </div>

                </div>


            </div>
            <!-- /.navbar-collapse -->

    </div>
    <!-- /.container -->
    </nav>
    </div>

</header>

<script>
    jQuery('.slider-wrap .fa-times').on('click', function () {
        jQuery(this).parents('.container').slideUp();

    })

    jQuery('.se-proj').on('click', function () {
        jQuery('#search-free').slideUp();
        jQuery('#search-proj').slideToggle();
    })
    jQuery('.se-free').on('click', function () {
        jQuery('#search-proj').slideUp();
        jQuery('#search-free').slideToggle();
    })


</script>