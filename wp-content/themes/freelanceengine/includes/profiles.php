<?php 

/**

* Registers a new post type profile

* @uses $wp_post_types Inserts new post type object into the list

*

* @param string  Post type key, must not exceed 20 characters

* @param array|string  See optional args description above.

* @return object|WP_Error the registered post type object, or an error object

*/

function fre_register_profile() {



	$labels = array(

		'name'                => __( 'Profiles', 'profiles-backend' ),

		'singular_name'       => __( 'Profile', 'profiles-backend' ),

		'add_new'             => _x( 'Add New profile', 'profiles-backend', 'profiles-backend' ),

		'add_new_item'        => __( 'Add New profile', 'profiles-backend' ),

		'edit_item'           => __( 'Edit profile', 'profiles-backend' ),

		'new_item'            => __( 'New profile', 'profiles-backend' ),

		'view_item'           => __( 'View profile', 'profiles-backend' ),

		'search_items'        => __( 'Search Profiles', 'profiles-backend' ),

		'not_found'           => __( 'No Profiles found', 'profiles-backend' ),

		'not_found_in_trash'  => __( 'No Profiles found in Trash', 'profiles-backend' ),

		'parent_item_colon'   => __( 'Parent profile:', 'profiles-backend' ),

		'menu_name'           => __( 'Profiles', 'profiles-backend' ),

	);



	$args = array(

		'labels'              => $labels,

		'hierarchical'        => true,

		'public'              => true,

		'show_ui'             => true,

		'show_in_menu'        => true,

		'show_in_admin_bar'   => true,

		'menu_position'       => 6,

		

		'show_in_nav_menus'   => true,

		'publicly_queryable'  => true,

		'exclude_from_search' => false,

		'has_archive'         => ae_get_option('fre_profile_archive', 'profiles'),

		'query_var'           => true,

		'can_export'          => true,

		'rewrite'             => true, //array('slug' => ae_get_option('fre_profile_slug', '')),

		'capability_type'     => 'post',

		'supports'            => array(

								'title', 'editor', 'author', 'thumbnail',

								'excerpt','custom-fields', 'trackbacks', 'comments',

								'revisions', 'page-attributes', 'post-formats'

								)

	); 



	register_post_type( PROFILE, $args );



    $labels = array(

        'name'                  => _x('Countries', 'Taxonomy plural name', 'profiles-backend') ,

        'singular_name'         => _x('Country', 'Taxonomy singular name', 'profiles-backend') ,

        'search_items'          => __('Search countries', 'profiles-backend') ,

        'popular_items'         => __('Popular countries', 'profiles-backend') ,

        'all_items'             => __('All countries', 'profiles-backend') ,

        'parent_item'           => __('Parent country', 'profiles-backend') ,

        'parent_item_colon'     => __('Parent country', 'profiles-backend') ,

        'edit_item'             => __('Edit country', 'profiles-backend') ,

        'update_item'           => __('Update country ', 'profiles-backend') ,

        'add_new_item'          => __('Add New country ', 'profiles-backend') ,

        'new_item_name'         => __('New country Name', 'profiles-backend') ,

        'add_or_remove_items'   => __('Add or remove country', 'profiles-backend') ,

        'choose_from_most_used' => __('Choose from most used enginetheme', 'profiles-backend') ,

        'menu_name'             => __('Countries', 'profiles-backend') ,

    );

    

    $args = array(

        'labels'            => $labels,

        'public'            => true,

        'show_in_nav_menus' => true,

        'show_admin_column' => true,

        'hierarchical'      => false,

        'show_tagcloud'     => true,

        'show_ui'           => true,

        'query_var'         => true,

        'rewrite'           => array(

            'slug'              =>  ae_get_option('country_slug', 'country')

        ),

        'query_var'         => true,

        'capabilities'      => array(

            'manage_terms',

            'edit_terms',

            'delete_terms',

            'assign_terms'

        )

    );

    register_taxonomy('country', array(PROFILE) , $args);



    global $ae_post_factory;

    $ae_post_factory->set(PROFILE, new AE_Posts(PROFILE, array('project_category', 'skill') , array(

        'et_professional_title', 

        'rating_score', 

        'hour_rate', 

        'et_experience',

        'currency',
        'country'

    )));



}

add_action( 'init', 'fre_register_profile' );

    /**

* Registers a new post type portfolio

* @uses $wp_post_types Inserts new post type object into the list

*

* @param string  Post type key, must not exceed 20 characters

* @param array|string  See optional args description above.

* @return object|WP_Error the registered post type object, or an error object

*/

function fre_register_portfolio() {



    $labels = array(

        'name'                => __( 'Portfolios', 'profiles-backend' ),

        'singular_name'       => __( 'Portfolio', 'profiles-backend' ),

        'add_new'             => _x( 'Add New portfolio', 'profiles-backend', 'profiles-backend' ),

        'add_new_item'        => __( 'Add New portfolio', 'profiles-backend' ),

        'edit_item'           => __( 'Edit portfolio', 'profiles-backend' ),

        'new_item'            => __( 'New portfolio', 'profiles-backend' ),

        'view_item'           => __( 'View portfolio', 'profiles-backend' ),

        'search_items'        => __( 'Search portfolio', 'profiles-backend' ),

        'not_found'           => __( 'No portfolio found', 'profiles-backend' ),

        'not_found_in_trash'  => __( 'No portfolios found in Trash', 'profiles-backend' ),

        'parent_item_colon'   => __( 'Parent portfolio:', 'profiles-backend' ),

        'menu_name'           => __( 'Portfolios', 'profiles-backend' ),

    );



    $args = array(

        'labels'              => $labels,

        'hierarchical'        => true,

        'public'              => true,

        'show_ui'             => true,

        'show_in_menu'        => true,

        'show_in_admin_bar'   => true,

        'menu_position'       => 6,

        

        'show_in_nav_menus'   => true,

        'publicly_queryable'  => true,

        'exclude_from_search' => false,

        'has_archive'         => ae_get_option('fre_portfolio_archive', 'portfolios'),

        'query_var'           => true,

        'can_export'          => true,

        'rewrite'             => array('slug' => ae_get_option('fre_portfolio', 'portfolio')),

        'capability_type'     => 'post',

        'supports'            => array(

                                'title', 'editor', 'author', 'thumbnail',

                                'excerpt','custom-fields', 'trackbacks', 'comments',

                                'revisions', 'page-attributes', 'post-formats'

                                )

    );

    global $ae_post_factory;

//    $meta_array_portfolio = array(
//        'portfolio_link',
//
//    );

    $ae_post_factory->set(PORTFOLIO, new AE_Posts( PORTFOLIO, array('skill'), array('portfolio_link') ) );

    register_post_type( PORTFOLIO, $args );

}

add_action('init', 'fre_register_portfolio' );



    /**

     * Create a taxonomy

     *

     * @uses  Inserts new taxonomy object into the list

     * @uses  Adds query vars

     *

     * @param string  Name of taxonomy object

     * @param array|string  Name of the object type for the taxonomy object.

     * @param array|string  Taxonomy arguments

     * @return null|WP_Error WP_Error if errors, otherwise null.

     */

function fre_register_tax_skill(){

    $status = false;

    $switch_skill = ae_get_option('switch_skill');

    if($switch_skill){

        $status = true;

    }

    $labels = array(

        'name'                  => _x('Skills', 'Taxonomy plural name', 'profiles-backend') ,

        'singular_name'         => _x('Skill', 'Taxonomy singular name', 'profiles-backend') ,

        'search_items'          => __('Search Skills', 'profiles-backend') ,

        'popular_items'         => __('Popular Skills', 'profiles-backend') ,

        'all_items'             => __('All Skills', 'profiles-backend') ,

        'parent_item'           => __('Parent Skill', 'profiles-backend') ,

        'parent_item_colon'     => __('Parent Skill', 'profiles-backend') ,

        'edit_item'             => __('Edit Skill', 'profiles-backend') ,

        'update_item'           => __('Update Skill ', 'profiles-backend') ,

        'add_new_item'          => __('Add New Skill ', 'profiles-backend') ,

        'new_item_name'         => __('New Skill Name', 'profiles-backend') ,

        'add_or_remove_items'   => __('Add or remove skill', 'profiles-backend') ,

        'choose_from_most_used' => __('Choose from most used enginetheme', 'profiles-backend') ,

        'menu_name'             => __('Skills', 'profiles-backend') ,

    );

    

    $args = array(

        'labels'            => $labels,

        'public'            => true,

        'show_in_nav_menus' => true,

        'show_admin_column' => true,

        'hierarchical'      => $status,

        'show_tagcloud'     => true,

        'show_ui'           => true,

        'query_var'         => true,

        'rewrite'           => array(

            'slug'              =>  ae_get_option('skill_slug', 'skill')

        ),

        'query_var'         => true,

        'capabilities'      => array(

            'manage_terms',

            'edit_terms',

            'delete_terms',

            'assign_terms'

        )

    );

    register_taxonomy('skill', array(PROFILE, PORTFOLIO, PROJECT) , $args);

}

add_action('init', 'fre_register_tax_skill' );



class Fre_ProfileAction extends AE_PostAction {

	function __construct($post_type = 'fre_profile') {

		$this->post_type = PROFILE;

        // add action fetch profile

		$this->add_ajax('ae-fetch-profiles', 'fetch_post');

        /**

         * sync profile 

         # update , insert ... 

         * @param Array $request

         * @since v1.0

         */ 

        $this->add_ajax('ae-profile-sync', 'sync_post');



        /**

         * hook convert a profile to add custom meta data 

         * @param Object $result profile object

         * @since v1.0

         */

        $this->add_filter('ae_convert_fre_profile', 'ae_convert_profile');

        // hook to groupy by, group profile by author

        $this->add_filter( 'posts_groupby', 'posts_groupby', 10, 2 );



        // filter post where to check user professional title

        $this->add_filter('posts_search', 'fre_posts_search', 10, 2);

        // add filter posts join to join post meta and get et professional title

        $this->add_filter('posts_join', 'fre_join_post', 10, 2);



	}

    /**

     * convert  profile 

     * @package FreelanceEngine

     */

    function ae_convert_profile($result){

        $result->et_avatar = get_avatar($result->post_author,70 );

        $result->author_link = get_author_posts_url( $result->post_author );

        if($result->et_experience) {

            if($result->et_experience > 1 ) {

                $result->experience = sprintf(__("%d years", 'profiles-backend'), $result->et_experience);    

            }else{

                $result->experience = sprintf(__("%d year", 'profiles-backend'), $result->et_experience);    

            }    

        }else {

            $result->experience = get_post_meta($result->ID, 'experience', true);

        }

        

        



        // override profile ling

        $result->permalink = $result->author_link; 

        $result->author_name = get_the_author_meta( 'display_name', $result->post_author );

        $result->hourly_rate_price = sprintf(__("%s/h", 'profiles-backend'), fre_price_format($result->hour_rate));



//        $rating = Fre_Review::freelancer_rating_score($result->post_author);
//var_dump($result->rating_score);
//        $result->rating_score = $rating['rating_score'];
//        var_dump($result->rating_score);



        ob_start();

        $i = 1;



        if($result->tax_input['skill']){

            $total_skill = count($result->tax_input['skill']);

            $string_length = 0;

            foreach ($result->tax_input['skill'] as $tax){

                $i++;

                $string_length += strlen($tax->name);

        ?>

                <li><span class="skill-name-profile"><?php echo $tax->name;?></span></li>

        <?php 

                if($string_length > 20 ) break;

                if($i >= 5) break;

            }

            if($i < $total_skill) {

                echo '<li><span class="skill-name-profile">+'.($total_skill-$i).'</span></li>'  ;

            }

        }

        $skill_list = ob_get_clean();



        // skill dont need id array

        unset($result->skill);

        // generate skill list

        $result->skill_list = $skill_list;

        return $result;

    }



    /**

     * group profile by user id if user can not edit other profils 

     * @param string $groupby

     * @param object $groupby Wp_Query object

     * @since 1.0                   

     * @author Dakachi

     */

    function posts_groupby($groupby, $query) {

        global $wpdb;



        if( is_admin() && current_user_can('edit_others_posts') ) {

            return $groupby;

        }



        if(isset($query->query_vars['post_type']) && $query->query_vars['post_type'] == $this->post_type) {

            $groupby = "{$wpdb->posts}.post_author";    

        }        

        return $groupby;

    }

    /**

     * add post where when user search, check professional title 

     * @param String $where SQL where string

     * @since 1.4

     * @author Dakachi

     */

    function fre_posts_search($post_search , $query) {

        global $wpdb;

        if(isset($_REQUEST['query']['s']) && $_REQUEST['query']['s']!='' && $query->query_vars['post_type'] == PROFILE) {



            $post_search = substr($post_search, 0, -2);

            

            $search = $_REQUEST['query']['s'];

            $q = array();

            $q['s'] = $search;

            // there are no line breaks in <input /> fields

            $search = str_replace( array( "\r", "\n" ), '', esc_sql($search) );

            $q['search_terms_count'] = 1;

            if ( ! empty( $q['sentence'] ) ) {

                $q['search_terms'] = array( $q['s'] );

            } else {

                if ( preg_match_all( '/".*?("|$)|((?<=[\t ",+])|^)[^\t ",+]+/', $q['s'], $matches ) ) {

                    $q['search_terms_count'] = count( $matches[0] );

                    $q['search_terms'] = $matches[0];

                    // if the search string has only short terms or stopwords, or is 10+ terms long, match it as sentence

                    if ( empty( $q['search_terms'] ) || count( $q['search_terms'] ) > 9 )

                    $q['search_terms'] = array( $q['s'] );

                } else {

                    $q['search_terms'] = array( $q['s'] );

                }

            }

            

            foreach ( $q['search_terms'] as $term ) {

                $post_search .= " OR prof_title.meta_value LIKE '%".$term."%'";

            }



            $post_search .= ") ";



            // $where .= " OR prof_title.meta_value LIKE '%".$_REQUEST['query']['s']."%'";

        }

        // wp_send_json( $post_search );

        return $post_search;

    }



    /**

     * join postmeta table to get et_professional_title 

     * @param String $join SQL join string

     * @since 1.4

     * @author Dakachi

     */


    function fre_join_post($join, $query){

        global $wpdb;

        if(isset($_REQUEST['query']['s']) && $_REQUEST['query']['s']!='' && $query->query_vars['post_type'] == PROFILE) {

            $join .= " INNER JOIN $wpdb->postmeta as prof_title ON ID = prof_title.post_id AND prof_title.meta_key='et_professional_title' ";    

        }

        return $join;

    }

    function fre_join_select_confirmed_posts($join, $query)
    {
        global $wpdb;

        $join .= " INNER JOIN $wpdb->usermeta AS s1 ON wp_posts.post_author = s1.user_id AND s1.meta_key = 'interview_status' AND (s1.meta_value ='confirmed' OR s1.meta_value = '' ) ";
        $join .= " INNER JOIN $wpdb->usermeta AS s2 ON wp_posts.post_author = s2.user_id AND s2.meta_key = 'user_available' AND s2.meta_value ='on' ";
        $join .= " INNER JOIN $wpdb->usermeta AS s3 ON wp_posts.post_author = s3.user_id AND s3.meta_value ='a:1:{s:10:\"freelancer\";b:1;}' ";

        return $join;

    }

    /**

     * filter query args before query

     * @package FreelanceEngine

     */

    public function filter_query_args($query_args){

        

        if(isset($_REQUEST['query'])) {
            $this->add_filter('posts_join', 'fre_join_select_confirmed_posts', 10, 2);

            $query = $_REQUEST['query'];
            $query_args['post_status']= array('draft','publish');
            $query_args= wp_parse_args($query_args, $query);

            // query profile base on skill

            if(isset($query['skill']) && $query['skill'] != '') {

                //$query_args['skill_slug__and'] = $query['skill'];

                $query_args['tax_query'] = array(

                        'skill' => array(

                                'taxonomy' => 'skill',

                                'terms'    => $query['skill'],

                                'field'    => 'slug'

                          ));

                unset($query_args['skill']);

            }

            // list featured profile

            if(isset($query['meta_key'])) {

                $query_args['meta_key'] =  $query['meta_key'];

                if(isset($query['meta_value'])) {

                    $query_args['meta_value'] =  $query['meta_value'];

                }

            }

            // add hour rate filter to query

            if( isset($query['hour_rate']) && !empty( $query['hour_rate'] ) ){

                $hour_rate = $query['hour_rate'];

                $hour_rate = explode(",",$hour_rate);

                $query_args['meta_query'][] =  array(   'key' =>'hour_rate',

                                                        'value' => array((int)$hour_rate[0],

                                                        (int)$hour_rate[1]),

                                                        'type'    => 'numeric',

                                                        'compare' => 'BETWEEN'

                                                );

            }


            if(isset($query['country']) && $query['country'] != '') {
                $query_args['meta_query'][] =  array(
                    'key' =>'country',
                    'value' => $query['country'],
                );
            }
            unset($query_args['country']);



            if(isset($query['project_category']) && $query['project_category'] != '') {

                $query_args['project_category'] = $query['project_category'];

                // $query_args['tax_query']['project_category'] =  array( 'taxonomy' => 'project_category', 'terms' => $query['project_category'], 'field' =>;

                // unset($query_args['project_category']);

            }

        }


        return apply_filters( 'fre_profile_query_args', $query_args, $query );

    }

    /**

     * hanlde profile action

     * @package FreelanceEngine

     */

    function sync_post() {

        global $ae_post_factory, $user_ID, $current_user;        

        $request   = $_REQUEST;

        $ae_users  = new AE_Users();

        $user_data = $ae_users->convert($current_user);

        $profile   = $ae_post_factory->get($this->post_type);



        if(!AE_Users::is_activate($user_ID)) {

            wp_send_json( array(

                'success' => false,

                'msg'     => __("Your account is pending. You have to activate your account to create profile.", 'profiles-backend')

                )

            );

        };



        // set status for profile

        if( !isset($request['post_status']) ){

            $request['post_status'] = 'publish';

        }

        // set profile title

        $request['post_title'] = $user_data->display_name;



        if($request['method'] == 'create') {

            $profile_id = get_user_meta( $user_ID, 'user_profile_id', true );

            if($profile_id) {

                $profile_post = get_post($profile_id);

                if($profile_post && $profile_post->post_status != 'draft') {

                    wp_send_json( array(

                        'success' => false,

                        'msg'     => __("You only can have on profile.", 'profiles-backend')

                        )

                    );    

                }                

            }

        }

        // sync profile

        $result = $profile->sync($request);

        

        if (!is_wp_error($result)) { 

            $result->redirect_url = $result->permalink;

            $rating_score = get_post_meta( $result->ID, 'rating_score', true );

            if(!$rating_score) {

                update_post_meta( $result->ID, 'rating_score', 0);

            }

            // action create profile

            if ($request['method'] == 'create') {

                // store profile id to user meta

                $response = array(

                    'success' => true,

                    'data' => $result,

                    'msg' => __("Your profile has been created successfull.", 'profiles-backend')

                );

                wp_send_json($response);

                //action update profile

            } else if($request['method'] == 'update'){



                $response = array(

                    'success' => true,

                    'data' => $result,

                    'msg' => __("Your profile has been updated successfully.", 'profiles-backend')
                    
                );

                wp_send_json($response);

            }



        } else {



            wp_send_json(array(

                'success' => false,

                'data'    => $result,

                'msg'     => $result->get_error_message()

            ));

        }

    }

}







class Fre_PortfolioAction extends AE_PostAction {

    function __construct($post_type = 'portfolio') {

        $this->post_type = PORTFOLIO;

        $this->add_ajax('ae-fetch-portfolios', 'fetch_post');

        $this->add_ajax('ae-portfolio-sync', 'sync_post');

        $this->add_filter('ae_convert_portfolio', 'ae_convert_portfolio');

    }

    /**

     * filter query args before query

     * @package FreelanceEngine

     */

    public function filter_query_args($query_args){

        if(isset($_REQUEST['query'])) {

            $query = $_REQUEST['query'];

            if(isset($query['skill']) && $query['skill'] != '') {

                $query_args['skill'] = $query['skill'];

            }

        }



        return $query_args;

    }

    function ae_convert_portfolio($result){



        $thumbnail_full_src              = wp_get_attachment_image_src( get_post_thumbnail_id( $result->ID ), 'full' );

        $thumbnail_src                   = wp_get_attachment_image_src( get_post_thumbnail_id( $result->ID ), 'portfolio' );

        $result->the_post_thumbnail_full = $thumbnail_full_src[0];

        $result->the_post_thumbnail      = $thumbnail_src[0];



        return $result;

    }

    /**

     * hanlde portfolio action

     * @package FreelanceEngine

     */

    function sync_post() {

        global $ae_post_factory, $user_ID, $current_user,$post;

        // echo 1; exit;

        $request   = $_REQUEST;

        $ae_users  = new AE_Users();

        $user_data = $ae_users->convert($current_user);

        $portfolio = $ae_post_factory->get($this->post_type);

   // var_dump($request);
    //var_dump($portfolio);

        if (!isset($request['id'])){
            unset($request['id']);
        }else{
            $request['ID'] = $request['id'];
            unset($request['id']);
        }
       // $request['post_content'] = strip_tags($request['post_content']);
        // set status for profile

        if( !isset($request['post_status']) ){

            $request['post_status'] = 'publish';

        }

        // set default post content

        //$request['post_content'] = '';



        // sync place
//var_dump($request);
        $result = $portfolio->sync($request);

//var_dump($result);

        if (!is_wp_error($result)) { 

            //update post thumbnail

            if( isset($request['post_thumbnail']) ){

                $thumb_id = $request['post_thumbnail'];

                set_post_thumbnail( $result, $thumb_id );

                $result = $portfolio->get( $result->ID );
            }



            // action create profile

            if ($request['method'] == 'create') {

                $convert = $portfolio->convert($result);

                $response = array(

                    'success'      => true,

                    'data'         => $convert,

                    'msg'          => __("Portfolio has been created successfully.", 'profiles-backend')

                );

                wp_send_json($response);

            } else if($request['method'] == 'delete' || $request['method'] == 'remove' ){

                $response = array(

                    'success'      => true,

                    'msg'          => __("Portfolio has been deleted successfully.", 'profiles-backend')

                );

                wp_send_json($response);

                //action update profile

            } else if($request['method'] == 'update'){


                $response = array(

                    'success'      => true,

                    'data'         => array(

                        'redirect_url' => $result->permalink,
                        'post_title' => $result->post_title,
                        'post_content' => $result->post_content,
                        'the_post_thumnail' => $result->the_post_thumnail,
                        'the_post_thumbnail' => $result->the_post_thumbnail,
                        'the_post_thumbnail_full' => $result->the_post_thumbnail_full,
                        'featured_image' => $result->featured_image,


                    ) ,

                    'msg'          => __("Portfolio has been updated successfully.", 'profiles-backend')

                );

                wp_send_json($response);

            }



        } else {

            wp_send_json(array(

                'success' => false,

                'data'    => $result,

                'msg'     => $result->get_error_message()

            ));

        }

    }

}

