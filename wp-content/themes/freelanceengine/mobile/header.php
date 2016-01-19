<?php
    global $current_user, $ae_post_factory, $user_ID;
    if($user_ID) {
        $ae_user     = AE_Users::get_instance();
        $user_data   = $ae_user->convert($current_user);
        $post_object = $ae_post_factory->get(PROFILE);
        // get user profile id
        $profile_id  = get_user_meta( $user_ID, 'user_profile_id', true);
        // get post profile
        $profile     = get_post($profile_id);

        if( $profile && !is_wp_error($profile) ){    
            $profile = $post_object->convert( $profile );
        }    
    }
?>
<!DOCTYPE html>
<!--[if IE 7]>
	<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
	<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta name="viewport" content="width=device-width, initial-scale=1 ,user-scalable=no">
    <title><?php wp_title( '|', true, 'right' ); ?></title>
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <?php
        //loads comment reply JS on single posts and pages
        // if ( is_single()) wp_enqueue_script( 'comment-reply' );  
        ae_favicon();
        wp_head(); 
        if(function_exists('et_mobile_render_less_style')) {
            et_mobile_render_less_style();    
        }
    ?>
</head>
<body <?php body_class( 'cbp-spmenu-push' ); ?>>
    <?php
        if(has_nav_menu('et_mobile')) {
            /**
            * Displays a navigation menu
            * @param array $args Arguments
            */
            $args = array(
                'theme_location' => 'et_mobile',
                'menu' => '',
                'container' => 'nav',
                'container_class' => 'cbp-spmenu cbp-spmenu-vertical cbp-spmenu-left',
                'container_id' => 'cbp-spmenu-s1',
                'menu_class' => 'menu-main',
                'menu_id' => '',
                'echo' => true,
                'before' => '',
                'after' => '',
                'link_before' => '',
                'link_after' => ''
            );
      
          wp_nav_menu( $args );
        }


        $notify_number = 0;
        if(function_exists('fre_user_have_notify') ) {
            $notify_number = fre_user_have_notify();
        }

    ?>

    <?php if($user_ID) { ?>
    <nav class="cbp-spmenu cbp-spmenu-vertical cbp-spmenu-right" id="cbp-spmenu-s2">
        <ul class="list-info-user-nav">
            <?php if(is_user_logged_in()){ ?>
        	<li>
            	<div class="avatar-user-menu">
                    <?php echo get_avatar( $user_data->ID, 48 ); ?>
                </div>
                <div class="user-name-menu">
                	<span class="name"><?php echo $user_data->display_name; ?></span>
                    <span class="position"><?php echo isset($profile->et_professional_title) ? $profile->et_professional_title : ''; ?></span>
                </div>
            </li>
            <li>
            	<a href="<?php echo et_get_page_link('profile'); ?>#tab_account" class="link-menu-nav">
                    <?php _e('Account Details', ET_DOMAIN) ?>
                </a>
            </li>
            <?php if( ae_user_role($current_user->ID) == FREELANCER ){ ?>
            <li>
            	<a href="<?php echo et_get_page_link('profile'); ?>#tab_profile" class="link-menu-nav">
                    <?php _e('Profile Details', ET_DOMAIN) ?>
                </a>
            </li>
            <?php } ?>
            <li>
            	<a href="<?php echo et_get_page_link('profile'); ?>#tab_project" class="link-menu-nav">
                    <?php _e('Project Details', ET_DOMAIN) ?>
                </a>
            </li>
            <li>
                <a href="<?php echo et_get_page_link('profile'); ?>#tab_notification" class="link-menu-nav trigger-notification" >
                    <?php 
                        _e("Notifications", ET_DOMAIN); 
                        if($notify_number) echo ' <span class="notify-number">('.$notify_number.')</span>';
                    ?>
                </a>
            </li>
            <li>
                <a href="<?php echo et_get_page_link('profile'); ?>#tab_change_pw" class="mb-change-password">
                    <i class="fa fa-key"></i>
                    <?php _e('Change Password', ET_DOMAIN) ?>
                </a>
            </li>
            <li>
                <a href="<?php echo wp_logout_url( home_url() ); ?>" class="logout-link">
                    <i class="fa fa-sign-out"></i><?php _e('Log out', ET_DOMAIN) ?>
                </a>
            </li>
            <?php } else { ?>
            <li>
                <a href="<?php echo et_get_page_link('auth') ?>" class="creat-team-link">
                    <i class="fa fa-sign-in"></i><?php _e('Sign in', ET_DOMAIN) ?>
                </a>
            </li>
            <?php } ?>
        </ul>
    </nav>
    <?php } ?>
    <header id="header">
        <div class="container-fluid">
        	<div class="row">
                <div class="col-xs-3">
                <?php if(has_nav_menu('et_mobile')) { ?>
                    <div id="left_menu">
                      <span></span>
                      <span></span>
                      <span></span>
                    </div>
                <?php } ?>
                </div>
                <div class="col-xs-5">
                    <div class="header-title">
                        <a href="<?php echo home_url(); ?>" class="logo-mobile">
                            <?php ae_mobile_logo(); ?>
                        </a>
                    </div>
                </div>
                <div class="col-xs-4">
                    <div class="user-avatar avatar login-form-header-wrapper">
                    <?php if(is_user_logged_in()) { ?> 
                        <a href="<?php echo et_get_page_link('profile'); ?>#tab_notification" class="trigger-notification" >
                            <span class="flag-icon">
                                <?php 
                                    if($notify_number) {
                                        echo '<span class="circle-new"></span>';
                                    }
                                ?>
                                <i class="fa fa-flag-o"></i>
                            </span>
                        </a>
                        <a href="#" id="right_menu" class="logged-in">
                            <?php echo get_avatar( $user_ID, 30); ?>
                        </a>
                    <?php }else { ?>
                        <a class="non-login right_menu" href="<?php echo et_get_page_link('auth') ?>"> 
                            <span class="icon-form-header icon-user-header"></span>
                        </a>
                    <?php 
                    // get template mobile header_login_template
                    get_template_part( 'mobile/template-js/header', 'login' );
                    } ?>
                    </div>
                </div>
            </div>
        </div>
    </header><!-- END HEADER -->
    
<?php
    if($user_ID) {
        echo '<script type="data/json"  id="user_id">'. json_encode(array('id' => $user_ID, 'ID'=> $user_ID) ) .'</script>';  
    } 
?>
