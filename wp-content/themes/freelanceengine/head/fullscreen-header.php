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
                          <?php $currentlang = get_bloginfo('language'); if($currentlang=="en-GB"): ?>
      
       

                    <div class="login-form-header-wrapper">
                            
                        <?php if(!is_user_logged_in()){ ?>

                        <div class="non-login">
                    
                            <div class="navbar-left links-wrap">
                            <a class="login login-btn" href="<?php echo site_url(); ?>/submit-project/"><?php _e("POST A PROJECT", ET_DOMAIN) ?></a>
                            
                            
                            <div class="dropdown-info-acc-wrapper">

                            <div class="dropdown">

                                <a href="#" class="dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown">

                                        <span class="avatar-and-name"><i class="fa fa-search"></i><span><?php _e("BROWSE", ET_DOMAIN); ?></span></span>


                                    <span class="caret"></span>

                                </a>

                                <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1" style="  left: -11px;
  top: -110%">

                                   
                                   <li role="presentation" class="hidden-xs hidden-sm">

                                        <span  class="avatar-and-name" style="color: #333;"><i class="fa fa-search"></i><span><?php _e("BROWSE", ET_DOMAIN); ?></span></span>


                                    <span class="caret" style="border-top: 4px solid #000;"></span>

                                    </li>
                                    
                                    <?php if(wp_get_current_user()->roles[0] == 'freelancer' || !is_user_logged_in()){ ?>
                                    <li role="presentation" class="divider hidden-xs hidden-sm"></li>

                                    <li role="presentation">

                                        <a  tabindex="-1" role="menuitem" href="<?php echo site_url(); ?>/projects/" class="se-proj">

                                                <?php _e("Search Projects", ET_DOMAIN) ?>

                                        </a>

                                    </li><?php } ?>
                                    <li role="presentation" class="divider"></li>

                                    

                                    <li role="presentation">

                                        <a role="menuitem" tabindex="-1" href="<?php echo site_url(); ?>/profiles/" class="se-free">

                                            </i><?php _e("Search Virtual Assistant", ET_DOMAIN) ?>

                                        </a>

                                    </li>

                                </ul>

                            </div>
                                                    
                        </div> 
                        <a class="login login-btn" href="<?php echo site_url(); ?>/how-it-works/"><?php _e("HOW IT WORKS", ET_DOMAIN) ?></a>
                        <div class="sitelang"><?php pll_the_languages(array('show_flags'=>1,'show_names'=>0)); ?></div>
                            </div>
                           

                       
                         <a href="<?php echo site_url(); ?>/login" class="login login-btn"><?php _e("LOGIN", ET_DOMAIN) ?></a>

                            <a href="<?php echo site_url(); ?>/sign-up" class="register register-btn"><?php _e("SIGN UP", ET_DOMAIN) ?></a>

                            <a class="register register-btn highlighted" href="<?php echo site_url(); ?>/perssistant-plus/"><?php _e("PERSSISTANT<span>+</span>", ET_DOMAIN) ?></a>
                        </div>

                    
                        <?php } else { ?>
                         <div class="navbar-left links-wrap" > 
                            <?php if( ae_user_role() == FREELANCER ) { ?>
                            <a href="<?php echo site_url(); ?>/projects/" class="login login-btn"><?php _e("FIND A PROJECT", ET_DOMAIN) ?></a>
                            <?php } else { ?>
                                <a href="<?php echo site_url(); ?>/submit-project/" class="login login-btn"><?php _e("POST A PROJECT", ET_DOMAIN) ?></a>
                            <?php } ?>
                        
                        <div class="dropdown-info-acc-wrapper">

                            <div class="dropdown">

                                <a href="#" class="dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown">

                                    <span class="avatar-and-name"><i class="fa fa-search"></i><span><?php _e("BROWSE", ET_DOMAIN); ?></span></span>


                                    <span class="caret"></span>

                                </a>

                                <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1" style="left: -11px;
  top: -110%">

                                   
                                   <li role="presentation" class="hidden-xs hidden-sm">

                                        <span  class="avatar-and-name" style="color: #333;"><i class="fa fa-search"></i><span><?php _e("BROWSE", ET_DOMAIN); ?></span></span>


                                    <span class="caret"></span>

                                    </li>
                                    
                                    <?php if(wp_get_current_user()->roles[0] == 'freelancer' || !is_user_logged_in()){ ?>
                                    <li role="presentation" class="divider hidden-xs hidden-sm"></li>

                                    <li role="presentation">

                                        <a role="menuitem" tabindex="-1" href="<?php echo site_url(); ?>/projects/" class="se-proj">

                                                <?php _e("Search Projects", ET_DOMAIN) ?>

                                        </a>

                                    </li> <?php } ?>

                                    <li role="presentation" class="divider"></li>

                                    

                                    <li role="presentation">

                                        <a role="menuitem" tabindex="-1" href="<?php echo site_url(); ?>/profiles/" class="se-free">

                                            </i><?php _e("Search Virtual Assistant", ET_DOMAIN) ?>

                                        </a>

                                    </li>

                                </ul>

                            </div>
                                                    
                        </div>
                         <a class="login login-btn" href="<?php echo site_url(); ?>/how-it-works/"><?php _e("HOW IT WORKS", ET_DOMAIN) ?></a>
                             <a class="login login-btn" href="<?php echo site_url(); ?>/chat-room/"><?php _e("MESSAGES", ET_DOMAIN) ?>&nbsp&nbsp<span class="badge count-chat"></span></a>  
<div class="sitelang"><?php pll_the_languages(array('show_flags'=>1,'show_names'=>0)); ?></div></div>

                        <div class="dropdown-info-acc-wrapper" style="margin-right:12px">

                            <div class="dropdown">

                                <a href="#" class="dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown">

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

                                            <i class="fa fa-user"></i><?php _e("Your Profile", ET_DOMAIN) ?>

                                        </a>

                                    </li>

                                    <li role="presentation" class="divider"></li>

                                    <li role="presentation">
                                        
                                        <a href="<?php echo site_url(); ?>/profile/" class="trigger-notification">

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
                            <a class="register register-btn highlighted" href="<?php echo site_url(); ?>/perssistant-hiring-services/"><?php _e("PERSSISTANT<span>+</span>", ET_DOMAIN) ?></a>

                      
                        <?php } ?>

                    </div>

                </div>
                 <?php else: ?>
                 <div class="login-form-header-wrapper">
                            
                        <?php if(!is_user_logged_in()){ ?>

                        <div class="non-login">
                    
                            <div class="pull-left mtop7" >
                            <a class="login login-btn" href="<?php echo site_url(); ?>/projekt-hinzuzufugen/"><?php _e("PROJEKT HINZUFÜGEN", ET_DOMAIN) ?></a>
                            
                            
                              <div class="dropdown-info-acc-wrapper">

                            <div class="dropdown">

                                <a href="#" class="dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown">

                                        <span class="avatar-and-name"><i class="fa fa-search"></i><span><?php _e("SUCHE", ET_DOMAIN); ?></span></span>


                                    <span class="caret"></span>

                                </a>

                                <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1" style="  left: -11px;
  top: -110%">

                                   
                                   <li role="presentation">

                                        <span  class="avatar-and-name" style="color: #333;"><i class="fa fa-search"></i><span><?php _e("BROWSE", ET_DOMAIN); ?></span></span>


                                    <span class="caret" style="border-top: 4px solid #000;"></span>

                                    </li>

                                    <li role="presentation" class="divider"></li>

                                    <li role="presentation">

                                        <a  tabindex="-1" role="menuitem" href="<?php echo site_url(); ?>/projects/" class="se-proj">

                                                <?php _e("Suche nach Projekten", ET_DOMAIN) ?>

                                        </a>

                                    </li>

                                    <li role="presentation" class="divider"></li>

                                    

                                    <li role="presentation">

                                        <a role="menuitem" tabindex="-1" href="<?php echo site_url(); ?>/profiles/" class="se-free">

                                            </i><?php _e("Suche nach Freiberuflern", ET_DOMAIN) ?>

                                        </a>

                                    </li>

                                </ul>

                            </div>
                                                    
                        </div> 
                        <a class="login login-btn" href="<?php echo site_url(); ?>/wie-es-funktioniert/"><?php _e("WIE ES FUNKTIONIERT", ET_DOMAIN) ?></a>
                        <a class="login login-btn" href="<?php echo site_url(); ?>/chat-room/"><?php _e("NACHRICHTEN", ET_DOMAIN) ?>&nbsp&nbsp<span class="badge count-chat"></span></a>
<div class="sitelang"><?php pll_the_languages(array('show_flags'=>1,'show_names'=>0)); ?></div>
                            </div>
                           

                       
                         <a href="<?php echo site_url(); ?>/login" class="login login-btn"><?php _e("EINLOGGEN", ET_DOMAIN) ?></a>

                            <a href="<?php echo site_url(); ?>/sign-up" class="register register-btn"><?php _e("ANMELDUNG", ET_DOMAIN) ?></a>

                            <a class="register register-btn highlighted" href="<?php echo site_url(); ?>/perssistant-plus/"><?php _e("PERSSISTANT<span>+</span>", ET_DOMAIN) ?></a>
                        </div>

                    
                        <?php } else { ?>
                         <div class="pull-left mtop7" > 
                            <?php if( ae_user_role() == FREELANCER ) { ?>
                            <a href="<?php echo site_url(); ?>/projects/" class="login login-btn"><?php _e("FIND A PROJECT", ET_DOMAIN) ?></a>
                            <?php } else { ?>
                                <a href="<?php echo site_url(); ?>/projekt-hinzuzufugen/" class="login login-btn"><?php _e("PROJEKT HINZUFÜGEN", ET_DOMAIN) ?></a>
                            <?php } ?>
                        
                        <div class="dropdown-info-acc-wrapper">

                            <div class="dropdown">

                                <a href="#" class="dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown">

                                        <span class="avatar-and-name"><i class="fa fa-search"></i><span><?php _e("SUCHE", ET_DOMAIN); ?></span></span>


                                    <span class="caret"></span>

                                </a>

                                <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1" style="left: -11px;
  top: -110%">

                                   
                                    <li role="presentation" >

                                        <span  class="avatar-and-name" style="color: #333;"><i class="fa fa-search"></i><span><?php _e("SUCHE", ET_DOMAIN); ?></span></span>


                                    <span class="caret"></span>

                                    </li>

                                    <li role="presentation" class="divider"></li>

                                    <li role="presentation">

                                        <a role="menuitem" tabindex="-1" href="<?php echo site_url(); ?>/projects/" class="se-proj">

                                                <?php _e("Suche nach Projekten", ET_DOMAIN) ?>

                                        </a>

                                    </li>

                                    <li role="presentation" class="divider"></li>

                                    

                                    <li role="presentation">

                                        <a role="menuitem" tabindex="-1" href="<?php echo site_url(); ?>/profiles/" class="se-free">

                                            </i><?php _e("Suche nach Freiberuflern", ET_DOMAIN) ?>

                                        </a>

                                    </li>

                                </ul>

                            </div>
                                                    
                        </div>
                         <a class="login login-btn" href="<?php echo site_url(); ?>/wie-es-funktioniert/"><?php _e("WIE ES FUNKTIONIERT", ET_DOMAIN) ?></a>
                             <a class="login login-btn" href="<?php echo site_url(); ?>/chat-room/"><?php _e("NACHRICHTEN", ET_DOMAIN) ?>&nbsp&nbsp<span class="badge count-chat"></span></a> 
<div class="sitelang"><?php pll_the_languages(array('show_flags'=>1,'show_names'=>0)); ?></div> </div>

                        <div class="dropdown-info-acc-wrapper" style="margin-right:12px">

                            <div class="dropdown">

                                <a href="#" class="dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown">

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

                                </a>

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

                                            <i class="fa fa-user"></i><?php _e("Dein Profil", ET_DOMAIN) ?>

                                        </a>

                                    </li>

                                    <li role="presentation" class="divider"></li>

                                    <li role="presentation">
                                        
                                        <a href="<?php echo site_url(); ?>/profile/" class="trigger-notification">

                                            <i class="fa fa-flag"></i>

                                            <?php 

                                                _e("Benachrichtigung", ET_DOMAIN); 

                                                if($notify_number) {

                                                    echo ' <span class="notify-number">(' . $notify_number . ')</span>';

                                                }

                                             ?>

                                        </a>

                                    </li>

                                    <li role="presentation" class="divider"></li>

                                    <li role="presentation">

                                        <a role="menuitem" tabindex="-1" href="<?php echo wp_logout_url(); ?>" class="logout">

                                            <i class="fa fa-sign-out"></i><?php _e("Abmelden", ET_DOMAIN) ?>

                                        </a>

                                    </li>

                                </ul>

                            </div>
                                                    
                        </div>
                            <a class="register register-btn highlighted" href="<?php echo site_url(); ?>/perssistant-hiring-services/"><?php _e("PERSSISTANT<span>+</span>", ET_DOMAIN) ?></a>

                      
                        <?php } ?>

                    </div>

       
        <?php endif; ?> 


            </div>
            <!-- /.navbar-collapse -->

            </div>
        <!-- /.container -->
        </nav>
    </div>

</header>
<?php /*
<div class="container box-shadow-style-theme search-form-top" id="search-proj">
       <div class="slider-wrap">
        <i class="fa fa-times fa-4"></i> 
            <div class="row" style="clear:both; border-top: 1px solid #F5F5F5; padding-top:5px; margin-top:5px">
            <div class="col-md-15">
                <div class="content-search-form-top-wrapper">
                    <h2 class="title-search-form-top">Category</h2>
                    <p>
                        <select data-chosen-width="90%" data-chosen-disable-search="" data-placeholder="Choose categories" name="project_category" id="project_category" class="cat-filter chosen-select" style="display: none;">
    <option value="" selected="selected">All categories</option>
    <option class=" ui-design  level-0" value="ui-design">UI Design</option>
    <option class=" web-design  level-0" value="web-design">Web Design</option>
</select><div class="chosen-container chosen-container-single" style="width: 90%;" title="" id="project_category_chosen"><a class="chosen-single" tabindex="-1"><span>All categories</span><div><b></b></div></a><div class="chosen-drop"><div class="chosen-search"><input type="text" autocomplete="off"></div><ul class="chosen-results"></ul></div></div>
 
                    </p>
                </div>
            </div>
    
            <div class="col-md-15">
                <div class="content-search-form-top-wrapper">
                    <div class="search-control">
                        <h2 class="title-search-form-top">Keyword</h2>
                        <input class="form-control keyword search" type="text" id="s" placeholder="Keyword" name="s" autocomplete="off" spellcheck="false">
                    </div>
                </div>
            </div>
    
            <div class="col-md-15">
                <div class="content-search-form-top-wrapper">
                    <h2 class="title-search-form-top">Project Type</h2>
                    <p>
                        <select data-chosen-width="90%" data-chosen-disable-search="1" data-placeholder="All types" name="project_type" id="project_type" class="type-filter chosen-select" style="display: none;">
    <option value="" selected="selected">All types</option>
    <option class=" full-time  level-0" value="full-time">Full time</option>
    <option class=" urgent  level-0" value="urgent">Urgent</option>
</select><div class="chosen-container chosen-container-single chosen-container-single-nosearch" style="width: 90%;" title="" id="project_type_chosen"><a class="chosen-single chosen-default" tabindex="-1"><span>All types</span><div><b></b></div></a><div class="chosen-drop"><div class="chosen-search"><input type="text" autocomplete="off" readonly=""></div><ul class="chosen-results"></ul></div></div>
 
                    </p>
                </div>
            </div>
            
                        <div class="col-md-15">
                <div class="content-search-form-top-wrapper">
                    <h2 class="title-search-form-top">Budget</h2>
                    <div class="slider slider-horizontal" style="width: 170px;"><div class="slider-track"><div class="slider-selection"></div><div class="slider-handle round"></div><div class="slider-handle round"></div></div><div class="tooltip top" style="top: -30px;"><div class="tooltip-arrow"></div><div class="tooltip-inner">[ : 0</div></div><input id="et_budget" type="text" name="et_budget" class="slider-ranger" value="" data-slider-min="0" data-slider-max="Array" data-slider-step="5" data-slider-value="[0,Array]"></div> 
                    <b class="currency">$1.00</b>
                    <input type="hidden" name="budget" id="budget" value="">
                </div>
            </div>
            <div class="col-md-15">
                <div class="content-search-form-top-wrapper">
                    <div class="skill-control">
                        <h2 class="title-search-form-top">Your Skills</h2>
                        <input class="form-control skill" type="text" id="skill" placeholder="Type and enter" name="" autocomplete="off" spellcheck="false">
                        <input type="hidden" class="skill_filter" name="filter_skill" value="1">
                        <ul class="skills-list" id="skills_list"></ul>
                    </div>
                </div>
            </div>
        </div>
       </div>
    </div>
<div class="container box-shadow-style-theme search-form-top" id="search-free">
        <div class="slider-wrap">
        <i class="fa fa-times fa-4"></i> 
          <div class="row" style="clear:both; border-top: 1px solid #F5F5F5; padding-top:5px; margin-top:5px">
            <div class="col-md-15">
                <div class="content-search-form-top-wrapper">
                    <h2 class="title-search-form-top">Freelancer</h2>
                    <p>
                        <select data-chosen-width="90%" data-chosen-disable-search="" data-placeholder="Choose categories" name="project_category" id="project_category" class="cat-filter chosen-select" style="display: none;">
    <option value="" selected="selected">All categories</option>
    <option class=" ui-design  level-0" value="ui-design">UI Design</option>
    <option class=" web-design  level-0" value="web-design">Web Design</option>
</select><div class="chosen-container chosen-container-single" style="width: 90%;" title="" id="project_category_chosen"><a class="chosen-single" tabindex="-1"><span>All categories</span><div><b></b></div></a><div class="chosen-drop"><div class="chosen-search"><input type="text" autocomplete="off"></div><ul class="chosen-results"></ul></div></div>
 
                    </p>
                </div>
            </div>
    
            <div class="col-md-15">
                <div class="content-search-form-top-wrapper">
                    <div class="search-control">
                        <h2 class="title-search-form-top">Keyword</h2>
                        <input class="form-control keyword search" type="text" id="s" placeholder="Keyword" name="s" autocomplete="off" spellcheck="false">
                    </div>
                </div>
            </div>
    
            <div class="col-md-15">
                <div class="content-search-form-top-wrapper">
                    <h2 class="title-search-form-top">Project Type</h2>
                    <p>
                        <select data-chosen-width="90%" data-chosen-disable-search="1" data-placeholder="All types" name="project_type" id="project_type" class="type-filter chosen-select" style="display: none;">
    <option value="" selected="selected">All types</option>
    <option class=" full-time  level-0" value="full-time">Full time</option>
    <option class=" urgent  level-0" value="urgent">Urgent</option>
</select><div class="chosen-container chosen-container-single chosen-container-single-nosearch" style="width: 90%;" title="" id="project_type_chosen"><a class="chosen-single chosen-default" tabindex="-1"><span>All types</span><div><b></b></div></a><div class="chosen-drop"><div class="chosen-search"><input type="text" autocomplete="off" readonly=""></div><ul class="chosen-results"></ul></div></div>
 
                    </p>
                </div>
            </div>
            
                        <div class="col-md-15">
                <div class="content-search-form-top-wrapper">
                    <h2 class="title-search-form-top">Budget</h2>
                    <div class="slider slider-horizontal" style="width: 170px;"><div class="slider-track"><div class="slider-selection"></div><div class="slider-handle round"></div><div class="slider-handle round"></div></div><div class="tooltip top" style="top: -30px;"><div class="tooltip-arrow"></div><div class="tooltip-inner">[ : 0</div></div><input id="et_budget" type="text" name="et_budget" class="slider-ranger" value="" data-slider-min="0" data-slider-max="Array" data-slider-step="5" data-slider-value="[0,Array]"></div> 
                    <b class="currency">$1.00</b>
                    <input type="hidden" name="budget" id="budget" value="">
                </div>
            </div>
            <div class="col-md-15">
                <div class="content-search-form-top-wrapper">
                    <div class="skill-control">
                        <h2 class="title-search-form-top">Your Skills</h2>
                        <input class="form-control skill" type="text" id="skill" placeholder="Type and enter" name="" autocomplete="off" spellcheck="false">
                        <input type="hidden" class="skill_filter" name="filter_skill" value="1">
                        <ul class="skills-list" id="skills_list"></ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
*/ ?>
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

     jQuery(window).on('scroll', function(){
        if(jQuery('#header-wrapper').hasClass('sticky')){
            jQuery('.search-form-top').addClass('stickyForm')
        }else{
             jQuery('.search-form-top').removeClass('stickyForm')
        }
         if(jQuery('body').scrollTop()===0){
         jQuery('.search-form-top').removeClass('stickyForm')
     }
    })

</script>