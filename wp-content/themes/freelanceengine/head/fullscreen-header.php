<?php 

    global $current_user;

    $class_trans = '';

    if(is_page_template('page-home.php')) {

        $class_trans = 'class="trans-color"';

    }else{

        $class_trans = 'class="not-page-home"';

    }

?>

<style>
    #header-wrapper.sticky, #header-wrapper.not-page-home, #header-wrapper.not-page-home .top-header{
        background-color:rgba(84,179,219,1) !important;
    }
    </style>
<header id="header-wrapper" data-size="big" <?php echo $class_trans ;?> >

    <div class="top-header">
    <nav class="navbar navbar-default">

        <div class="container">
            <div class="navbar-header">
                  <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                  </button>
                    <?php $sufx = (is_user_logged_in())?'?r=1':'';?>
                    <?php if (is_page_template('page-home.php')) { ?>

					<a href="<?php echo home_url() . $sufx; ?>" class="logo site_logo_white"><?php fre_logo('site_logo_white') ?></a>

                    <a href="<?php echo home_url() . $sufx; ?>" class="logo site_logo_black"><?php fre_logo('site_logo_black') ?></a>

                <?php } else { ?>

                    <a href="<?php echo home_url() . $sufx; ?>" class="logo"><?php fre_logo('site_logo_black') ?></a>

                <?php } ?>               
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="navbar-collapse">

                
                <?php /* Commented by Prawez on 07-06-2015
                <div class="col-md-4 col-xs-4">

                    <ul class="btn-menu-call">

                        <?php if(has_nav_menu('et_header')) { // dont render button menu if dont have menu ?>

                        <li><a href="javascript:void(0);" class="trigger-overlay trigger-menu"><i class="fa fa-bars"></i><span><?php _e("MENU", ET_DOMAIN); ?></span></a></li>

                        <?php } ?>

                        <li><a href="javascript:void(0);" class="trigger-overlay trigger-search"><i class="fa fa-search"></i><span><?php _e("BROWSE", ET_DOMAIN); ?></span></a></li>

                    </ul>

                </div>
                */?>
<!--                          --><?php //$currentlang = get_bloginfo('language'); if($currentlang=="en-GB"): ?>
      
       

                    <div class="login-form-header-wrapper">
                            
                        <?php if(!is_user_logged_in()){ ?>

                        <div class="non-login">
                    
                            <div class="navbar-left links-wrap">
                            <a class="login login-btn" href="<?php echo site_url(); ?>/submit-project/"><?php _e("POST A PROJECT", 'header') ?></a>
                            
                            
                            <div class="dropdown-info-acc-wrapper">

                            <div class="dropdown">

                                <a href="#" class="dropdown-toggle" id="dropdownMenu1" data-toggle="dropdown">

                                        <span class="avatar-and-name"><span><?php _e("BROWSE", 'header'); ?></span></span>


                                    <span class="caret"></span>

                                </a>

                                <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1" style="  left: -11px;
  top: -110%">

                                   
                                   <li role="presentation" class="hidden-xs hidden-sm">

                                        <span  class="avatar-and-name" style="color: #333;"><span><?php _e("BROWSE", 'header'); ?></span></span>


                                    <span class="caret" style="border-top: 4px solid #000;"></span>

                                    </li>
                                    
                                    <?php if(ae_user_role() == FREELANCER || !is_user_logged_in()){ ?>
                                    <li role="presentation" class="divider hidden-xs hidden-sm"></li>

                                    <li role="presentation">

                                        <a  tabindex="-1" role="menuitem" href="<?php echo site_url(); ?>/projects/" class="se-proj">

                                                <?php _e("Search Projects", 'header') ?>

                                        </a>

                                    </li><?php } ?>
                                    <li role="presentation" class="divider"></li>

                                    

                                    <li role="presentation">

                                        <a role="menuitem" tabindex="-1" href="<?php echo site_url(); ?>/profiles/" class="se-free">

                                            <?php _e("Search Virtual Assistant", 'header') ?>

                                        </a>

                                    </li>

                                </ul>

                            </div>
                                                    
                        </div> 
                        <a class="login login-btn" href="<?php echo site_url(); ?>/how-it-works/"><?php _e("HOW IT WORKS", 'header') ?></a>
                            </div>
                            <div class="language-selector-wpml-custom">
                                <?php do_action('wpml_add_language_selector');?>
                            </div>

                            <a href="<?php echo site_url(); ?>/login" class="login login-btn"><?php _e("LOGIN", 'header') ?></a>

                            <a href="<?php echo site_url(); ?>/sign-up" class="register register-btn"><?php _e("SIGN UP", 'header') ?></a>

                            <a class="register register-btn highlighted" href="<?php echo site_url(); ?>/perssistant-plus/"><?php _e("PERSSISTANT<span>+</span>", 'header') ?></a>
                        </div>

                    
                        <?php } else { ?>
                         <div class="navbar-left links-wrap" > 
                            <?php if( ae_user_role() == FREELANCER ) { ?>
                            <a href="<?php echo site_url(); ?>/projects/" class="login login-btn"><?php _e("FIND A PROJECT", 'header') ?></a>
                            <?php } else { ?>
                                <a href="<?php echo site_url(); ?>/submit-project/" class="login login-btn"><?php _e("POST A PROJECT", 'header') ?></a>
                            <?php } ?>
                        
                        <div class="dropdown-info-acc-wrapper">

                            <div class="dropdown">

                                <a href="#" class="dropdown-toggle" id="dropdownMenu1" data-toggle="dropdown">

                                    <span class="avatar-and-name"><span><?php _e("BROWSE", 'header'); ?></span></span>


                                    <span class="caret"></span>

                                </a>

                                <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1" style="left: -11px;
  top: -110%">

                                   
                                   <li role="presentation" class="hidden-xs hidden-sm">

                                        <span  class="avatar-and-name" style="color: #333;"><span><?php _e("BROWSE", 'header'); ?></span></span>


                                    <span class="caret"></span>

                                    </li>
                                    
                                    <?php if(ae_user_role() == FREELANCER || !is_user_logged_in()){ ?>
                                    <li role="presentation" class="divider hidden-xs hidden-sm"></li>

                                    <li role="presentation">

                                        <a role="menuitem" tabindex="-1" href="<?php echo site_url(); ?>/projects/" class="se-proj">

                                                <?php _e("Search Projects", 'header') ?>

                                        </a>

                                    </li> <?php } ?>

                                    <li role="presentation" class="divider"></li>

                                    

                                    <li role="presentation">

                                        <a role="menuitem" tabindex="-1" href="<?php echo site_url(); ?>/profiles/" class="se-free">

                                            </i><?php _e("Search Virtual Assistant", 'header') ?>

                                        </a>

                                    </li>

                                </ul>

                            </div>
                                                    
                        </div>
                         <a class="login login-btn" href="<?php echo site_url(); ?>/how-it-works/"><?php _e("HOW IT WORKS", 'header') ?></a>
                             <a class="login login-btn" href="<?php echo site_url(); ?>/chat-room/"><?php _e("MESSAGES", 'header') ?>&nbsp&nbsp<span class="badge count-chat"></span></a>
                             <div class="language-selector-wpml-custom">
                                 <?php do_action('wpml_add_language_selector');?>
                             </div>
                        <div class="dropdown-info-acc-wrapper" style="margin-right:12px">

                            <div class="dropdown">

                                <a href="#" class="dropdown-toggle" id="dropdownMenu1" data-toggle="dropdown">

                                    <span class="avatar-and-name current_user_avatar">

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

                                </a>

                                <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">

                                    <li class="hidden-xs hidden-sm"><span class="avatar-and-name avatar-name-ontop">

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

                                    <li role="presentation" class="divider hidden-xs hidden-sm"></li>

                                    <li role="presentation">

                                        <a role="menuitem" tabindex="-1" href="<?php echo et_get_page_link("profile") ?>" class="display-name">

                                            <i class="fa fa-user"></i><?php _e("Your Profile", 'header') ?>

                                        </a>

                                    </li>

                                    <li role="presentation" class="divider"></li>

                                    <li role="presentation">
                                        
                                        <a href="<?php echo site_url(); ?>/profile/" class="trigger-notification">

                                            <i class="fa fa-flag"></i>

                                            <?php 

                                                _e("Notification", 'header');

                                                if($notify_number) {

                                                    echo ' <span class="notify-number">(' . $notify_number . ')</span>';

                                                }

                                             ?>

                                        </a>

                                    </li>

                                    <li role="presentation" class="divider"></li>

                                    <li role="presentation">

                                        <a role="menuitem" tabindex="-1" href="<?php echo wp_logout_url(); ?>" class="logout">

                                            <i class="fa fa-sign-out"></i><?php _e("Logout", 'header') ?>

                                        </a>

                                    </li>

                                </ul>

                            </div>
                                                    
                        </div>
                            <a class="register register-btn highlighted" href="<?php echo site_url(); ?>/perssistant-hiring-services/"><?php _e("PERSSISTANT<span>+</span>", 'header') ?></a>

                      
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
    jQuery('.slider-wrap .fa-times').on('click', function(){
        jQuery(this).parents('.container').slideUp();        
        
    })

    jQuery('.se-proj').on('click', function(){
        jQuery('#search-free').slideUp();        
        jQuery('#search-proj').slideToggle();
    })
    jQuery('.se-free').on('click', function(){
         jQuery('#search-proj').slideUp();
        jQuery('#search-free').slideToggle();
    })

</script>