<?php

/**
 * @package Social login  config and settings
 */

add_filter('ae_admin_menu_pages', 'ae_social_login_settings');
function ae_social_login_settings($pages) {
    
    $options = AE_Options::get_instance();
    $sections = array();
    
    /**
     * social settings section
     */
    $user_roles = ae_social_auth_support_role();
    $authentication_page = ae_get_social_connect_page_link();
    $default_role = ae_get_social_login_user_roles_default();
    $sections[] = array(
        'args' => array(
            'title' => __("Social API", 'aecore-other-backend') ,
            'id' => 'social-settings',
            'icon' => 'B',
            'class' => ''
        ) ,
        'groups' => array(
            array(
                'args' => array(
                    'title' => __("Twitter API", 'aecore-other-backend') ,
                    'id' => 'twitter-api',
                    'class' => '',
                    'desc' => __("Enabling this will allow users to login via Twitter.", 'aecore-other-backend')
                ) ,
                
                'fields' => array(
                    array(
                        'id' => 'twitter_login',
                        'type' => 'switch',
                        'title' => __("Twitter API ", 'aecore-other-backend') ,
                        'name' => 'twitter_login',
                        'class' => ''
                    ) ,
                    array(
                        'id' => 'et_twitter_key',
                        'type' => 'text',
                        'title' => __("Twitter key ", 'aecore-other-backend') ,
                        'name' => 'et_twitter_key',
                        'placeholder' => __("Twitter Consumer Key", 'aecore-other-backend') ,
                        'class' => '',
                    ) ,
                    array(
                        'id' => 'et_twitter_secret',
                        'type' => 'text',
                        'title' => __("Twitter secret ", 'aecore-other-backend') ,
                        'name' => 'et_twitter_secret',
                        'placeholder' => __("Twitter Consumer Secret", 'aecore-other-backend') ,
                        'class' => '',
                    )
                ) ,
            ) ,
            array(
                'args' => array(
                    'title' => __("Facebook API", 'aecore-other-backend') ,
                    'id' => 'facebook-api',
                    'class' => '',
                    'desc' => __("Enabling this will allow users to login via Facebook.", 'aecore-other-backend')
                ) ,
                
                'fields' => array(
                    array(
                        'id' => 'facebook_login',
                        'type' => 'switch',
                        'title' => __("Facebook API ", 'aecore-other-backend') ,
                        'name' => 'facebook_login',
                        'class' => ''
                    ) ,
                    array(
                        'id' => 'et_facebook_key',
                        'type' => 'text',
                        'title' => __("Facebook key ", 'aecore-other-backend') ,
                        'name' => 'et_facebook_key',
                        'placeholder' => __("Facebook Application ID", 'aecore-other-backend') ,
                        'class' => ''
                    ),
                    array(
                        'id' => 'et_facebook_secret_key',
                        'type' => 'text',
                        'title' => __("Facebook secret key ", 'aecore-other-backend') ,
                        'name' => 'et_facebook_secret_key',
                        'placeholder' => __("Facebook Secret Key", 'aecore-other-backend') ,
                        'class' => ''
                    ) 
                ) 
            ) ,
            array(
                'args' => array(
                    'title' => __("Google API", 'aecore-other-backend') ,
                    'id' => 'google-api',
                    'class' => '',
                    'desc' => __("Enabling this will allow users to login via Google.", 'aecore-other-backend')
                ) ,
                
                'fields' => array(
                    array(
                        'id' => 'gplus_login',
                        'type' => 'switch',
                        'title' => __("Google API ", 'aecore-other-backend') ,
                        'name' => 'gplus_login',
                        'class' => ''
                    ) ,
                    array(
                        'id' => 'gplus_client_id',
                        'type' => 'text',
                        'title' => __("Google key ", 'aecore-other-backend') ,
                        'name' => 'gplus_client_id',
                        'placeholder' => __("Client ID", 'aecore-other-backend') ,
                        'class' => ''
                    ),
                     array(
                        'id' => 'gplus_secret_id',
                        'type' => 'text',
                        'title' => __("Google Secret key ", 'aecore-other-backend') ,
                        'name' => 'gplus_secret_id',
                        'placeholder' => __("Google secret key", 'aecore-other-backend') ,
                        'class' => ''
                    )
                )
            ),
            array(
                'args' => array(
                    'title' => __("LinkedIn API", 'aecore-other-backend') ,
                    'id' => 'linkedin-api',
                    'class' => '',
                    'desc' => __("Enabling this will allow users to login via LinkedIn.", 'aecore-other-backend')
                ) ,
                
                'fields' => array(
                    array(
                        'id' => 'linkedin_login',
                        'type' => 'switch',
                        'title' => __("LinkedIn API ", 'aecore-other-backend') ,
                        'name' => 'linkedin_login',
                        'class' => ''
                    ) ,
                    array(
                        'id' => 'linkedin_api_key',
                        'type' => 'text',
                        'title' => __("Consumer Key / API Key ", 'aecore-other-backend') ,
                        'name' => 'linkedin_api_key',
                        'placeholder' => __("LinkedIn API Key", 'aecore-other-backend') ,
                        'class' => ''
                    ),
                     array(
                        'id' => 'linkedin_secret_key',
                        'type' => 'text',
                        'title' => __("Consumer Secret / Secret Key ", 'aecore-other-backend') ,
                        'name' => 'linkedin_secret_key',
                        'placeholder' => __("LinkedIn secret key", 'aecore-other-backend') ,
                        'class' => ''
                    )
                )
            )
        )
    );
     /**
     * social settings section
     */
    $sections[] = array(
        'args' => array(
            'title' => __("General setting", 'aecore-other-backend') ,
            'id' => 'social-page-settings',
            'icon' => 'y',
            'class' => ''
        ) ,
        'groups' => array(
            array(
                'args' => array(
                    'title' => __("Social connect page", 'aecore-other-backend') ,
                    'id' => 'social-connect',
                    'class' => '',
                    'desc' => __("You can create a new page and paste shortcode [social_connect_page] here", 'aecore-other-backend')
                ) ,
                
                'fields' => array(
                    array(
                        'id' => 'social_connect',
                        'type' => 'text',
                        'title' => __("Social connect page url ", 'aecore-other-backend') ,
                        'name' => 'social_connect',
                        'placeholder' => __("eg:http://enginethemes.com/directoryengine/social-connect", 'aecore-other-backend') ,
                        'class' => '',
                        'default' => $authentication_page
                    )
                )
            ),
            array(
                    // Units of measurement
                    'args' => array(
                        'title' => __("Select User roles", 'aecore-other-backend') ,
                        'id' => 'user_roles',
                        'class' => '',
                        'desc' => __("Select the user roles.", 'aecore-other-backend')
                    ) ,
                    
                    'fields' => array(
                        array(
                            'id' => 'user-roles',
                            'type' => 'multi_select',
                            'data' => $user_roles,
                            'title' => __("Select user roles", 'aecore-other-backend') ,
                            'name' => 'social_user_role',
                            'class' => 'option-item bg-grey-input ',
                            'placeholder' => __("Select user roles", 'aecore-other-backend'),
                            'label' => __("Select user roles", 'aecore-other-backend'),
                            'default' => $default_role
        
                        )
                    )
                ),
            
        )
    );
    $temp = array();
    foreach ($sections as $key => $section) {
        $temp[] = new AE_section($section['args'], $section['groups'], $options);
    }
    
    $orderlist = new AE_container(array(
        'class' => 'social-login-settings',
        'id' => 'settings',
    ) , $temp, $options);
    $pages[] = array(
        'args' => array(
            'parent_slug' => 'et-overview',
            'page_title' => __('Social login', 'aecore-other-backend') ,
            'menu_title' => __('SOCIAL LOGIN', 'aecore-other-backend') ,
            'cap' => 'administrator',
            'slug' => 'ae-social-login',
            'icon' => 'B',
            'desc' => __("setup a way for users to login via their socile network accounts", 'aecore-other-backend')
        ) ,
        'container' => $orderlist
    );
    
    return $pages;
}
