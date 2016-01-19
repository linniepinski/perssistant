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
            'title' => __("Social API", ET_DOMAIN) ,
            'id' => 'social-settings',
            'icon' => 'B',
            'class' => ''
        ) ,
        'groups' => array(
            array(
                'args' => array(
                    'title' => __("Twitter API", ET_DOMAIN) ,
                    'id' => 'twitter-api',
                    'class' => '',
                    'desc' => __("Enabling this will allow users to login via Twitter.", ET_DOMAIN)
                ) ,
                
                'fields' => array(
                    array(
                        'id' => 'twitter_login',
                        'type' => 'switch',
                        'title' => __("Twitter API ", ET_DOMAIN) ,
                        'name' => 'twitter_login',
                        'class' => ''
                    ) ,
                    array(
                        'id' => 'et_twitter_key',
                        'type' => 'text',
                        'title' => __("Twitter key ", ET_DOMAIN) ,
                        'name' => 'et_twitter_key',
                        'placeholder' => __("Twitter Consumer Key", ET_DOMAIN) ,
                        'class' => '',
                    ) ,
                    array(
                        'id' => 'et_twitter_secret',
                        'type' => 'text',
                        'title' => __("Twitter secret ", ET_DOMAIN) ,
                        'name' => 'et_twitter_secret',
                        'placeholder' => __("Twitter Consumer Secret", ET_DOMAIN) ,
                        'class' => '',
                    )
                ) ,
            ) ,
            array(
                'args' => array(
                    'title' => __("Facebook API", ET_DOMAIN) ,
                    'id' => 'facebook-api',
                    'class' => '',
                    'desc' => __("Enabling this will allow users to login via Facebook.", ET_DOMAIN)
                ) ,
                
                'fields' => array(
                    array(
                        'id' => 'facebook_login',
                        'type' => 'switch',
                        'title' => __("Facebook API ", ET_DOMAIN) ,
                        'name' => 'facebook_login',
                        'class' => ''
                    ) ,
                    array(
                        'id' => 'et_facebook_key',
                        'type' => 'text',
                        'title' => __("Facebook key ", ET_DOMAIN) ,
                        'name' => 'et_facebook_key',
                        'placeholder' => __("Facebook Application ID", ET_DOMAIN) ,
                        'class' => ''
                    ),
                    array(
                        'id' => 'et_facebook_secret_key',
                        'type' => 'text',
                        'title' => __("Facebook secret key ", ET_DOMAIN) ,
                        'name' => 'et_facebook_secret_key',
                        'placeholder' => __("Facebook Secret Key", ET_DOMAIN) ,
                        'class' => ''
                    ) 
                ) 
            ) ,
            array(
                'args' => array(
                    'title' => __("Google API", ET_DOMAIN) ,
                    'id' => 'google-api',
                    'class' => '',
                    'desc' => __("Enabling this will allow users to login via Google.", ET_DOMAIN)
                ) ,
                
                'fields' => array(
                    array(
                        'id' => 'gplus_login',
                        'type' => 'switch',
                        'title' => __("Google API ", ET_DOMAIN) ,
                        'name' => 'gplus_login',
                        'class' => ''
                    ) ,
                    array(
                        'id' => 'gplus_client_id',
                        'type' => 'text',
                        'title' => __("Google key ", ET_DOMAIN) ,
                        'name' => 'gplus_client_id',
                        'placeholder' => __("Client ID", ET_DOMAIN) ,
                        'class' => ''
                    ),
                     array(
                        'id' => 'gplus_secret_id',
                        'type' => 'text',
                        'title' => __("Google Secret key ", ET_DOMAIN) ,
                        'name' => 'gplus_secret_id',
                        'placeholder' => __("Google secret key", ET_DOMAIN) ,
                        'class' => ''
                    )
                )
            ),
            array(
                'args' => array(
                    'title' => __("LinkedIn API", ET_DOMAIN) ,
                    'id' => 'linkedin-api',
                    'class' => '',
                    'desc' => __("Enabling this will allow users to login via LinkedIn.", ET_DOMAIN)
                ) ,
                
                'fields' => array(
                    array(
                        'id' => 'linkedin_login',
                        'type' => 'switch',
                        'title' => __("LinkedIn API ", ET_DOMAIN) ,
                        'name' => 'linkedin_login',
                        'class' => ''
                    ) ,
                    array(
                        'id' => 'linkedin_api_key',
                        'type' => 'text',
                        'title' => __("Consumer Key / API Key ", ET_DOMAIN) ,
                        'name' => 'linkedin_api_key',
                        'placeholder' => __("LinkedIn API Key", ET_DOMAIN) ,
                        'class' => ''
                    ),
                     array(
                        'id' => 'linkedin_secret_key',
                        'type' => 'text',
                        'title' => __("Consumer Secret / Secret Key ", ET_DOMAIN) ,
                        'name' => 'linkedin_secret_key',
                        'placeholder' => __("LinkedIn secret key", ET_DOMAIN) ,
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
            'title' => __("General setting", ET_DOMAIN) ,
            'id' => 'social-page-settings',
            'icon' => 'y',
            'class' => ''
        ) ,
        'groups' => array(
            array(
                'args' => array(
                    'title' => __("Social connect page", ET_DOMAIN) ,
                    'id' => 'social-connect',
                    'class' => '',
                    'desc' => __("You can create a new page and paste shortcode [social_connect_page] here", ET_DOMAIN)
                ) ,
                
                'fields' => array(
                    array(
                        'id' => 'social_connect',
                        'type' => 'text',
                        'title' => __("Social connect page url ", ET_DOMAIN) ,
                        'name' => 'social_connect',
                        'placeholder' => __("eg:http://enginethemes.com/directoryengine/social-connect", ET_DOMAIN) ,
                        'class' => '',
                        'default' => $authentication_page
                    )
                )
            ),
            array(
                    // Units of measurement
                    'args' => array(
                        'title' => __("Select User roles", ET_DOMAIN) ,
                        'id' => 'user_roles',
                        'class' => '',
                        'desc' => __("Select the user roles.", ET_DOMAIN)
                    ) ,
                    
                    'fields' => array(
                        array(
                            'id' => 'user-roles',
                            'type' => 'multi_select',
                            'data' => $user_roles,
                            'title' => __("Select user roles", ET_DOMAIN) ,
                            'name' => 'social_user_role',
                            'class' => 'option-item bg-grey-input ',
                            'placeholder' => __("Select user roles", ET_DOMAIN),
                            'label' => __("Select user roles", ET_DOMAIN),
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
            'page_title' => __('Social login', ET_DOMAIN) ,
            'menu_title' => __('SOCIAL LOGIN', ET_DOMAIN) ,
            'cap' => 'administrator',
            'slug' => 'ae-social-login',
            'icon' => 'B',
            'desc' => __("setup a way for users to login via their socile network accounts", ET_DOMAIN)
        ) ,
        'container' => $orderlist
    );
    
    return $pages;
}
