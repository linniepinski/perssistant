<?php

/**
 * Registers a new post type Project
 * @uses $wp_post_types Inserts new post type object into the list
 *
 * @param string  Post type key, must not exceed 20 characters
 * @param array|string See optional args description above.
 * @return object|WP_Error the registered post type object, or an error object
 */
function fre_register_project()
{

    $labels = array(
        'name' => __('Projects', ET_DOMAIN),
        'singular_name' => __('Project', ET_DOMAIN),
        'add_new' => _x('Add New project', ET_DOMAIN, ET_DOMAIN),
        'add_new_item' => __('Add New project', ET_DOMAIN),
        'edit_item' => __('Edit project', ET_DOMAIN),
        'new_item' => __('New project', ET_DOMAIN),
        'view_item' => __('View project', ET_DOMAIN),
        'search_items' => __('Search Projects', ET_DOMAIN),
        'not_found' => __('No Projects found', ET_DOMAIN),
        'not_found_in_trash' => __('No Projects found in Trash', ET_DOMAIN),
        'parent_item_colon' => __('Parent project:', ET_DOMAIN),
        'menu_name' => __('Projects', ET_DOMAIN),
    );

    $args = array(
        'labels' => $labels,
        'hierarchical' => true,

        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_admin_bar' => true,
        'menu_position' => 5,
        'show_in_nav_menus' => true,
        'publicly_queryable' => true,
        'exclude_from_search' => false,
        'has_archive' => ae_get_option('fre_project_archive', 'projects'),
        'query_var' => true,
        'can_export' => true,
        'rewrite' => array(
            'slug' => ae_get_option('fre_project_slug', 'project')
        ),
        'capability_type' => 'post',
        'supports' => array(
            'title',
            'editor',
            'author',
            'thumbnail',
            'excerpt',
            'custom-fields',
            'trackbacks',
            //'comments',
            'revisions',
            'page-attributes',
            'post-formats'
        )
    );
    register_post_type(PROJECT, $args);

    /**
     * Create a taxonomy project category
     *
     * @uses  Inserts new taxonomy project category  object into the list
     */
    $labels = array(
        'name' => _x('Project Categories', 'Taxonomy plural name', ET_DOMAIN),
        'singular_name' => _x('Project Category', 'Taxonomy singular name', ET_DOMAIN),
        'search_items' => __('Search Project Categories', ET_DOMAIN),
        'popular_items' => __('Popular Project Categories', ET_DOMAIN),
        'all_items' => __('All Project Categories', ET_DOMAIN),
        'parent_item' => __('Parent Project Category', ET_DOMAIN),
        'parent_item_colon' => __('Parent Project Category', ET_DOMAIN),
        'edit_item' => __('Edit Project Category', ET_DOMAIN),
        'update_item' => __('Update Project Category', ET_DOMAIN),
        'add_new_item' => __('Add New Project Category', ET_DOMAIN),
        'new_item_name' => __('New Project Category Name', ET_DOMAIN),
        'add_or_remove_items' => __('Add or remove Project Categories', ET_DOMAIN),
        'choose_from_most_used' => __('Choose from most used enginetheme', ET_DOMAIN),
        'menu_name' => __('Project Category', ET_DOMAIN),
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'show_in_nav_menus' => true,
        'show_admin_column' => true,
        'hierarchical' => true,
        'show_tagcloud' => true,
        'show_ui' => true,
        'query_var' => true,
        'rewrite' => array(
            'slug' => ae_get_option('project_category_slug', 'project_category'),
            'hierarchical' => ae_get_option('project_category_hierarchical', false)
        ),
        'capabilities' => array(
            'manage_terms',
            'edit_terms',
            'delete_terms',
            'assign_terms'
        )
    );

    register_taxonomy('project_category', array(
        PROJECT,
        PROFILE
    ), $args);

    /**
     * Create a taxonomy project category
     *
     * @uses  Inserts new taxonomy project category  object into the list
     */

    $labels = array(
        'name' => _x('Project Types', 'Taxonomy plural name', ET_DOMAIN),
        'singular_name' => _x('Project Type', 'Taxonomy singular name', ET_DOMAIN),
        'search_items' => __('Search Project Types', ET_DOMAIN),
        'popular_items' => __('Popular Project Types', ET_DOMAIN),
        'all_items' => __('All Project Types', ET_DOMAIN),
        'parent_item' => __('Parent Project Type', ET_DOMAIN),
        'parent_item_colon' => __('Parent Project Type', ET_DOMAIN),
        'edit_item' => __('Edit Project Type', ET_DOMAIN),
        'update_item' => __('Update Project Type', ET_DOMAIN),
        'add_new_item' => __('Add New Project Type', ET_DOMAIN),
        'new_item_name' => __('New Project Type Name', ET_DOMAIN),
        'add_or_remove_items' => __('Add or remove Project Types', ET_DOMAIN),
        'choose_from_most_used' => __('Choose from most used enginetheme', ET_DOMAIN),
        'menu_name' => __('Project Type', ET_DOMAIN),
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'show_in_nav_menus' => true,
        'show_admin_column' => true,
        'hierarchical' => true,
        'show_tagcloud' => true,
        'show_ui' => true,
        'query_var' => true,
        'rewrite' => array(
            'slug' => ae_get_option('project_type_slug', 'project_type'),
            'hierarchical' => false
        ),
        'query_var' => true,
        'capabilities' => array(
            'manage_terms',
            'edit_terms',
            'delete_terms',
            'assign_terms'
        )
    );

    register_taxonomy('project_type', array(
        PROJECT
    ), $args);

    global $ae_post_factory;
    $project_tax = array(
        'skill',
        'project_category',
        'project_type'
    );

    $project_meta = array(
        'et_budget',
        'et_expired_date',

        // 'bid_type',
        // 'hour_rate',
        'hours_limit',
        'type_budget', //(hourly_rate ,  fixed)


        'deadline',
        'total_bids',

        // tb cong tong so bid
        'bid_average',

        // accepted bid id
        'accepted',

        // payment data
        'et_payment_package',

        // count post view, this field should not be updated by author
        'post_views'
    );

    $ae_post_factory->set(PROJECT, new AE_Posts(PROJECT, $project_tax, $project_meta));
}

add_action('init', 'fre_register_project', 1);

/**
 * register post type bid.
 */
class Fre_ProjectAction extends AE_PostAction
{
    function __construct($post_type = 'project')
    {
        $this->mail = new Fre_Mailing();
        $this->post_type = PROJECT;
        $this->disable_plan = ae_get_option('disable_plan', false);
        $this->add_ajax('ae-fetch-projects', 'fetch_post');
        $this->add_ajax('ae-project-sync', 'post_sync');
        $this->add_ajax('fre_get_skills', 'fre_get_skills');
        $this->add_filter('ae_convert_project', 'ae_convert_project');

        /**
         * catch wp head check cookie and set post views
         * # update post views
         */
        $this->add_action('template_redirect', 'update_post_views');

        // delete bid on project after deltete project
        //$this->add_action( 'admin_init', 'fre_admin_init' );

        $this->add_action('ae_pre_update_project', 'add_project_type');
        $this->add_action('ae_pre_insert_project', 'add_project_type');

        $this->add_action('delete_post', 'fre_after_delete_project', 12, 1);

        /**
         * catch ad change status event, update expired date
         */
        $this->add_action('transition_post_status', 'change_post_status', 10, 3);

        /**
         * add action publish ad, update ad order and related ad in a package
         */
        $this->add_action('ae_publish_post', 'publish_post_action');
    }

    /**
     * update post views
     */
    public function update_post_views()
    {
        if (is_singular($this->post_type)) {
            global $post;
            $views = get_post_meta($post->ID, 'post_views', true);
            if ($post->post_status == 'publish') {
                $cookie = 'cookie_' . $post->ID . '_visited';
                if (!isset($_COOKIE[$cookie])) {
                    update_post_meta($post->ID, 'post_views', $views + 1);
                    setcookie($cookie, 'is_visited', time() + 3 * 3600);
                }
            }
        }
    }

    /**
     * Override filter_query_args for action fetch_post.
     *
     */
    public function filter_query_args($query_args)
    {
        global $user_ID;
        $query = $_REQUEST['query'];

        if (isset($_REQUEST['query']['skill'])) {
            if (isset($query['skill']) && $query['skill'] != '') {

                //$query_args['skill_slug__and'] = $query['skill'];
                $query_args['tax_query'] = array(
                    'skill' => array(
                        'taxonomy' => 'skill',
                        'terms' => $query['skill'],
                        'field' => 'slug'
                    )
                );
            }
        }

        // list featured profile
        if (isset($query['meta_key'])) {
            $query_args['meta_key'] = $query['meta_key'];
            if (isset($query['meta_value'])) {
                $query_args['meta_value'] = $query['meta_value'];
            }
        }

        // // filter project by project category
        if (isset($query['project_category']) && $query['project_category'] != '') {
            $query_args['project_category'] = $query['project_category'];
        }

        // query project by project type
        if (isset($query['project_type']) && $query['project_type'] != '') {
            $query_args['project_type'] = $query['project_type'];
        }

        // filter project by budget
        if (isset($query['et_budget']) && !empty($query['et_budget'])) {
            $budget = $query['et_budget'];
            $budget = explode(",", $budget);
            $query_args['meta_query'][] = array(
                'key' => 'et_budget',
                'value' => array(
                    (int)$budget[0],
                    (int)$budget[1]
                ),
                'type' => 'numeric',
                'compare' => 'BETWEEN'
            );
        }

        // project posted from query date
        if (isset($query['date'])) {
            $date = $query['date'];
            $day = date('d', strtotime($date));
            $mon = date('m', strtotime($date));
            $year = date('Y', strtotime($date));
            $query_args['date_query'][] = array(
                'year' => $year,
                'month' => $mon,
                'day' => $day,
                'inclusive' => true
            );
        }

        /**
         * add query when archive project type
         */

        if (current_user_can('manage_options') && isset($query['is_archive_project']) && $query['is_archive_project'] == TRUE) {
            $query_args['post_status'] = array(
                'pending',
                'publish'
            );
        }

        // query arg for filter page default
        if (isset($query['orderby'])) {
            $orderby = $query['orderby'];
            switch ($orderby) {
                case 'et_featured':
                    $query_args['meta_key'] = $orderby;
                    $query_args['orderby'] = 'meta_value_num date';
                    $query_args['meta_query'] = array(
                        'relation' => 'OR',
                        array(
                            //check to see if et_featured has been filled out
                            'key' => $orderby,
                            'compare' => 'IN',
                            'value' => array(
                                0,
                                1
                            )
                        ),
                        array(
                            //if no et_featured has been added show these posts too
                            'key' => $orderby,
                            'value' => 0,
                            'compare' => 'NOT EXISTS'
                        )
                    );
                    break;

                case 'et_budget':
                    $query_args['meta_key'] = 'et_budget';
                    $query_args['orderby'] = 'meta_value_num date';
                    break;

                case 'date':
                default:
                    add_filter('posts_orderby', array(
                        'ET_FreelanceEngine',
                        'order_by_post_pending'
                    ), 2, 12);
                    break;
            }
        }

        /*
         * set post status when query in page profile or author.php
        */
        if (isset($query['is_author']) && $query['is_author']) {
            if (!isset($query['post_status'])) {
                $query_args['post_status'] = array(
                    'close',
                    'complete',
                    'publish'
                );
            }
            $query_args['post_status'] = $query['post_status'];
        }

        if (isset($query['post_status']) && isset($query['author']) && $query['post_status'] && $user_ID == $query['author']) {
            $query_args['post_status'] = $query['post_status'];
        }

        return apply_filters('fre_project_query_args', $query_args, $query);
    }

    /**
     * ajax callback sync post details
     * - update
     * - insert
     * - delete
     */
    function post_sync()
    {
        $request = $_REQUEST;
        global $ae_post_factory, $user_ID;

        if (!AE_Users::is_activate($user_ID)) {
            wp_send_json(array(
                'success' => false,
                'msg' => __("Your account is pending. You have to activate your account to continue this step.", ET_DOMAIN)
            ));
        };
        if (check_existing_post_name($request['post_title'])) {
            wp_send_json(array(
                'success' => false,
                'msg' => __("Current title name already exists", ET_DOMAIN)
            ));
        }
        // prevent freelancer submit project
        if (!fre_share_role() && ae_user_role() == FREELANCER) {
            wp_send_json(array(
                'success' => false,
                'msg' => __("You need an employer account to post a project.", ET_DOMAIN)
            ));
        }

        // unset package data when edit place if user can edit others post
        if (isset($request['ID']) && !isset($request['renew'])) {
            unset($request['et_payment_package']);
        }

//        if (isset($request['archive'])) {
//            $request['post_status'] = 'archive';
//        }
//        if (isset($request['publish'])) {
//            $request['post_status'] = 'publish';
//        }
//        if (isset($request['delete'])) {
//            $request['post_status'] = 'trash';
//        }
//
//        if (isset($request['disputed'])) {
//            $request['post_status'] = 'disputed';
//        }

        if (isset($request['project_type'])) unset($request['project_type']);

        $place = $ae_post_factory->get($this->post_type);

        // sync place
        $result = $place->sync($request);

        if (!is_wp_error($result)) {

            // update place carousels
            if (isset($request['et_carousels'])) {

                // loop request carousel id
                foreach ($request['et_carousels'] as $key => $value) {
                    $att = get_post($value);

                    // just admin and the owner can add carousel
                    if (current_user_can('manage_options') || $att->post_author == $user_ID) {
                        wp_update_post(array(
                            'ID' => $value,
                            'post_parent' => $result->ID
                        ));
                    }
                }
            }

            /**
             * check payment package and check free or use package to send redirect link
             */
            if (isset($request['et_payment_package'])) {

                // check seller use package or not
                $check = AE_Package::package_or_free($request['et_payment_package'], $result);

                // check use package or free to return url
                if ($check['success']) {
                    $result->redirect_url = $check['url'];
                }

                $result->response = $check;

                // check seller have reached limit free plan
                $check = AE_Package::limit_free_plan($request['et_payment_package']);

                if ($check['success']) {

                    // false user have reached maximum free plan
                    $response['success'] = false;
                    $response['msg'] = $check['msg'];

                    // send response to client
                    wp_send_json($response);
                }
            }

            // check payment package


            /**
             * check disable plan and submit place to view details
             */
            if ($this->disable_plan && $request['method'] == 'create') {

                // disable plan, free to post place
                $response = array(
                    'success' => true,
                    'data' => array(
                        'ID' => $result->ID,
                        // set redirect url
                        'redirect_url' => $result->permalink
                    ),
                    'msg' => __("Submit place successfull.", ET_DOMAIN)
                );

                // send response
                wp_send_json($response);
            }

            // send json data to client
            wp_send_json(array(
                'success' => true,
                'data' => $result,
                'msg' => __("Update project successful!", ET_DOMAIN)
            ));
        } else {

            // update false
            wp_send_json(array(
                'success' => false,
                'data' => $result,
                'msg' => $result->get_error_message()
            ));
        }
    }

    /**
     * Get skill
     */

    public function fre_get_skills()
    {
        $terms = get_terms('skill', array(
            'hide_empty' => 0,
            'fields' => 'names'
        ));
        wp_send_json($terms);
    }

    /**
     *Convert project
     *
     *
     */
    function ae_convert_project($result)
    {
        global $user_ID;
        $result->et_avatar = get_avatar($result->post_author, 35);
        $result->author_url = get_author_posts_url($result->post_author);
        $result->author_name = get_the_author_meta('display_name', $result->post_author);
        $result->budget = fre_price_format($result->et_budget);
        $result->bid_budget_text = fre_price_format(get_post_meta($result->accepted, 'bid_budget', true));

        $result->rating_score = (float)get_post_meta($result->ID, 'rating_score', true);

        $comment = get_comments(array(
            'post_id' => $result->ID,
            'type' => 'fre_review'
        ));
        if ($comment) {
            $result->project_comment = $comment['0']->comment_content;
        } else {
            $result->project_comment = '';
        }

        // project is disputing
        if ($result->post_status == 'disputing') $result->status_text = __("DISPUTE", ET_DOMAIN);

        // project completed text status
        if ($result->post_status == 'complete') $result->status_text = __("COMPLETED", ET_DOMAIN);

        // project close for working when accepted a bids
        if ($result->post_status == 'close') {
            $result->status_text = __("HIRED", ET_DOMAIN);
            if ($user_ID == $result->post_author) {
                $result->workspace_link = add_query_arg(array(
                    'workspace' => 1
                ), $result->permalink);
            }
        }

        /**
         * return carousels
         */
        if (current_user_can('manage_options') || $result->post_author == $user_ID) {
            $children = get_children(array(
                'numberposts' => 15,
                'order' => 'ASC',
                'post_parent' => $result->ID,
                'post_type' => 'attachment'
            ));

            $result->et_carousels = array();

            foreach ($children as $key => $value) {
                $result->et_carousels[] = $key;
            }

            /**
             * set post thumbnail in one of carousel if the post thumbnail doesnot exists
             */
            if (has_post_thumbnail($result->ID)) {
                $thumbnail_id = get_post_thumbnail_id($result->ID);
                if (!in_array($thumbnail_id, $result->et_carousels)) $result->et_carousels[] = $thumbnail_id;
            }
        }

        $result->posted_by = sprintf(__("Posted by %s", ET_DOMAIN), $result->author_name);
        return $result;
    }

    /**
     * Run sql to delete all bids on this project.
     * int $project_id
     */
    function fre_after_delete_project($project_id)
    {
        global $wpdb;
        $post_type = get_post_field('post_type', $project_id);
        if ($post_type == PROJECT && current_user_can('delete_posts')) $wpdb->query($wpdb->prepare("DELETE FROM $wpdb->posts WHERE post_parent = %d AND post_type = %s ", $project_id, BID));
    }

    /**
     * hook to ae_update_project/ae_insert_project to add project type
     * @param object $result Project object
     * @since 1.0
     * @author Dakachi
     */
    function add_project_type($args)
    {

        /**
         * checking old data
         */
        if ($args['method'] == 'update') {
            $prev_post = get_post($args['ID']);

            // get current status and compare to display msg.

            if ($prev_post->post_status == 'reject') {

                // change post status to pending when edit rejected ad
                $args['post_status'] = 'pending';
            }

            global $ae_post_factory;
            $pack = $ae_post_factory->get('pack');
            $package_id = get_post_meta($args['ID'], 'et_payment_package', true);
            $package = $pack->get($package_id);

            if (isset($package->project_type) && $package->project_type) {
                $project_type = get_term_by('id', $package->project_type, 'project_type');
                if ($project_type && !is_wp_error($project_type)) {
                    $args['project_type'] = $project_type->term_id;
                }
            }
        }

        // add project type to param
        if (isset($args['et_payment_package'])) {
            global $ae_post_factory;
            $pack = $ae_post_factory->get('pack');
            $package = $pack->get($args['et_payment_package']);

            if (isset($package->project_type) && $package->project_type) {
                $project_type = get_term_by('id', $package->project_type, 'project_type');
                if ($project_type && !is_wp_error($project_type)) {
                    $args['project_type'] = $project_type->term_id;
                }
            }
        }

        return $this->validate_data($args);
    }

    /**
     * validate data
     */
    public function validate_data($data)
    {
        global $user_ID;

        if (is_wp_error($data)) return $data;

        $require_fields = apply_filters('fre_project_required_fields', array(
            'et_budget',
            'project_category'
        ));

        if (!current_user_can('manage_options')) {
            if (isset($data['renew']) && !isset($data['et_payment_package']) && $this->disable_plan

                // if not disable package plan
            ) {
                return new WP_Error('empty_package', __("Cannot create a place with an empty package.", ET_DOMAIN));
            }

            if (!isset($data['post_content']) || $data['post_content'] == '') {
                return new WP_Error('ad_empty_content', __("You should enter short description for your place.", ET_DOMAIN));
            }

            if (!isset($data['post_title']) || $data['post_title'] == '') {
                return new WP_Error('ad_empty_content', __("Your place should have a title.", ET_DOMAIN));
            }

            if (!isset($data['project_category']) && in_array('project_category', $require_fields) && !is_admin()) {
                return new WP_Error('invalid_category', __("Your project should has a category!", ET_DOMAIN));
            }

            if (!isset($data['et_budget']) && in_array('et_budget', $require_fields)) {
                return new WP_Error('invalid_budget', __("Your have to enter a budget for your requirement!", ET_DOMAIN));
            }
        }

        if (in_array('et_budget', $require_fields) && $data['et_budget'] <= 0) {
            return new WP_Error('budget_less_than_zero', __("Your budget have to greater than zero!", ET_DOMAIN));
        }

        /**
         * unsert featured et_featured param if user cannot  edit others posts
         */
        if (!ae_user_can('edit_others_posts')) {
            unset($data['et_featured']);

            // unset($data['post_status']);
            unset($data['et_expired_date']);
            unset($data['post_views']);
        }

        /**
         * check payment package is valid or not
         * set up featured if this package is featured
         */
        if (isset($data['et_payment_package'])) {

            /**
             * check package plan exist or not
             */
            global $ae_post_factory;
            $package = $ae_post_factory->get('pack');

            $plan = $package->get($data['et_payment_package']);
            if (!$plan) return new WP_Error('invalid_plan', __("You have selected an invalid plan.", ET_DOMAIN));

            /**
             * if user can not edit others posts the et_featured will no be unset and check,
             * this situation should happen when user edit/add post in backend.
             * Force to set featured post
             */
            if (!isset($data['et_featured']) || !$data['et_featured']) {
                $data['et_featured'] = 0;
                if (isset($plan->et_featured) && $plan->et_featured) {
                    $data['et_featured'] = 1;
                }
            }
        }

        /**
         * check max category options, filter ad category
         */
        $max_cat = ae_get_option('max_cat', 3);
        if ($max_cat && !current_user_can('edit_others_posts')) {

            /**
             * check max category user can set for a place
             */
            $num_of_cat = count($data['project_category']);
            if ($max_cat < $num_of_cat) {
                for ($i = $max_cat; $i < $num_of_cat; $i++) {
                    unset($data['place_category'][$i]);
                }
            }
        }

        return apply_filters('fre_project_validate_data', $data);
    }

    /**
     * catch event change ad status, update expired date
     */
    public function change_post_status($new_status, $old_status, $post)
    {

        // not is post type controled
        if ($post->post_type != $this->post_type) return;

        /**
         * check post package data
         */
        global $ae_post_factory;
        $pack = $ae_post_factory->get('pack');

        $sku = get_post_meta($post->ID, 'et_payment_package', true);
        $package = $pack->get($sku);

        $old_expiration = get_post_meta($post->ID, 'et_expired_date', true);

        /**
         * if an ad didnt have a package, force publish
         */
        if (!$package || is_wp_error($package)) {
            if ($new_status == 'publish') {
                do_action('ae_publish_post', $post->ID);
            }
            $this->mail->change_status($new_status, $old_status, $post);
            return false;
        };

        // if isset duration
        if (isset($package->et_duration)) {
            $duration = (int)$package->et_duration;
            if ($new_status == 'pending') {

                // clear ad expired date and post view when change from archive to pending
                if ($old_status == "archive" || $old_status == "draft") {

                    // force update expired date if job is change from draft or archive to publish
                    $expired_date = date('Y-m-d H:i:s', strtotime("+{$duration} days"));

                    /**
                     * reset post expired date
                     */
                    update_post_meta($post->ID, 'et_expired_date', '');

                    /**
                     * reset post view
                     */
                    update_post_meta($post->ID, 'post_view', 0);

                    /**
                     * change post date
                     */
                    wp_update_post(array(
                        'ID' => $post->ID,
                        'post_date' => ''
                    ));
                }
            } elseif ($new_status == 'publish') {

                // update post expired date when publish
                if ($old_status == "archive" || $old_status == "draft") {

                    // force update expired date if job is change from draft or archive to publish

                    $expired_date = date('Y-m-d H:i:s', strtotime("+{$duration} days"));
                    update_post_meta($post->ID, 'et_expired_date', $expired_date);
                } else {

                    // update expired date when the expired date less then current time

                    if (empty($old_expiration) || current_time('timestamp') > strtotime($old_expiration)) {
                        $expired_date = date('Y-m-d H:i:s', strtotime("+{$duration} days"));
                        update_post_meta($post->ID, 'et_expired_date', $expired_date);

                        // echo get_post_meta( $post->ID, 'et_expired_date' , true );


                    }
                }
            }
        }

        if ($new_status == 'publish') {
            do_action('ae_publish_post', $post->ID);
        }

        /**
         * send mail when change ad status
         */
        $this->mail->change_status($new_status, $old_status, $post);
    }
}

/**
 * get number bid of a project
 * @param  int $project_id : project id
 * @param  string $post_type : post type of project id;
 * @return int number bidding on this project.
 */
function get_number_bids($project_id, $post_type = 'bid')
{
    global $wpdb;
    $count_bid = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) 
                                                    FROM  $wpdb->posts 
                                                    WHERE post_type =%s 
                                                        and (post_status = 'publish' 
                                                            or post_status = 'complete' 
                                                            or post_status = 'accept' )
                                                        and post_parent = %d", $post_type, $project_id));
    return (int)$count_bid;
}

/**
 * return sum of bid bud get on a project.
 * @param  int $project_id : project id
 * @param  string $meta_key : the metakey to sum it.
 * @return sum of bid budget on this project.
 */
function get_total_cost_bids($project_id, $meta_key = 'bid_budget')
{
    global $wpdb;
    $sql = "SELECT sum(meta_value) 
                FROM $wpdb->postmeta pm, $wpdb->posts p  
                WHERE   p.ID = pm.post_id  
                        AND p.post_type ='bid' 
                        AND pm.meta_key = %s   
                        AND p.post_parent =%d ";
    $total = $wpdb->get_var($wpdb->prepare($sql, $meta_key, $project_id));
    return $total;
}

/**
 * count number post of a user.
 * @param  int $userid : author id of post.
 * @param  string $post_type
 * @return count number project of author id.
 */
function fre_count_user_posts_by_type($user_id, $post_type = 'project', $status = "publish", $multi = false)
{
    global $wpdb;

    //$where = get_posts_by_author_sql( $post_type, true, $userid );
    $where = '';
    if (!$multi) $where = "WHERE post_type = '" . $post_type . "' AND post_status = '" . $status . "'";
    else if ($multi) $where = "WHERE post_type = '" . $post_type . "' AND post_status IN (" . $status . ") ";

    $count = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts $where AND post_author = $user_id");

    return apply_filters('get_usernumprojects', $count, $user_id);
}

/** sum a meta value of user.
 * itn $user_id
 * string $post_type
 * string $meta_key,
 * get sum all value of meta_key.
 */
function fre_count_meta_value_by_user($user_id, $post_type, $meta_key)
{
    global $wpdb;
    $sql = "SELECT sum(pm.meta_value) from $wpdb->posts p  LEFT JOIN $wpdb->postmeta pm  ON  p.ID = pm.post_id WHERE p.post_type = '" . $post_type . "' AND p.post_status='complete' AND p.post_author = " . $user_id . "   AND pm.meta_key = '" . $meta_key . "' ";
    $count = $wpdb->get_var($sql);
    return (float)$count;
}

function fre_count_total_user_spent($user_id)
{
    global $wpdb;
    $sql = "SELECT SUM(pm.meta_value) from $wpdb->posts project 
        JOIN  $wpdb->posts bid 
            ON project.ID = bid.post_parent
        JOIN $wpdb->postmeta pm
            ON bid.id = pm.post_id
            WHERE project.post_status = 'complete'
                AND bid.post_status = 'complete' 
                AND pm.meta_key ='bid_budget'
                AND project.post_author = $user_id ";
    return (float)$wpdb->get_var($sql);
}

function fre_count_total_user_earned($user_id)
{
    global $wpdb;
    $sql = "SELECT SUM(pm.meta_value) from $wpdb->posts bid 
        JOIN $wpdb->postmeta pm
            ON bid.id = pm.post_id
            WHERE bid.post_status = 'complete'
                AND pm.meta_key ='bid_budget'
                AND bid.post_author = $user_id   
                AND bid.post_type = 'bid' ";
    return (float)$wpdb->get_var($sql);
}

/**
 * display html of list skill or category of project
 * @param  int $id project id
 * @param  string $title - title apperance in h3
 * @param  string $slug taxonomy slug
 * @return display list taxonomy of project.
 */
function list_tax_of_project($id, $title = '', $taxonomy = 'project_category', $class = '')
{

    $class = 'list-categories';
    if ($class = 'skill') $class = 'list-skill';

    $terms = get_the_terms($id, $taxonomy);
    if ($terms && !is_wp_error($terms)): ?>
        <h3 class="title-content"><?php
            printf($title); ?></h3>
        <div class="list-require-skill-project list-taxonomires list-<?php
        echo $taxonomy; ?>">
            <?php
            the_taxonomy_list($taxonomy, '<span class="skill-name-profile">', '</span>', $class); ?>
        </div>
        <?php
    endif;
}

if (!function_exists('fre_display_user_info')) {

    /**
     * display user info of a freelancer or employser
     * @param  int $profile_id
     * @return display info in single-project.php or author.php
     */
    function fre_display_user_info($user_id)
    {

        global $wp_query, $user_ID;
        $user = get_userdata($user_id);
        $ae_users = AE_Users::get_instance();
        $user_data = $ae_users->convert($user);
        $role = ae_user_role($user_id);
        ?>
        <div class="info-company-avatar">
            <a href="<?php
            echo get_author_posts_url($user_id); ?>">
                <span class="info-avatar"><?php
                    echo get_avatar($user_id, 35);
                    echo get_the_title($user_id); ?></span>
            </a>
            <div class="info-company">
                <h3 class="info-company-name"><?php
                    echo $user_data->display_name; ?></h3>
                <span class="time-since">
                    <?php
                    printf(__('Member Since %s', ET_DOMAIN), date(get_option('date_format'), strtotime($user_data->user_registered))); ?>
                </span>
            </div>
        </div>
        <ul class="list-info-company-details">
            <?php
            if ($role == 'freelancer') {
                ?>
                <li>
                    <div class="address"><i class="fa fa-map-marker"></i>
                        <?php
                        _e('Location:', ET_DOMAIN); ?>
                        <span class="info" title="<?php
                        echo $user_data->location; ?>">
                            <?php
                            echo $user_data->location; ?>
                        </span>
                    </div>
                </li>
                <li>
                    <div class="spent"><i class="fa fa-money"></i>
                        <?php
                        _e('Earning:', ET_DOMAIN); ?>
                        <span class="info">
                            <?php
                            echo fre_price_format(fre_count_meta_value_by_user($user_id, 'bid', 'bid_budget')); ?>
                        </span>
                    </div>
                </li>
                <li>
                    <div class="briefcase"><i class="fa fa-briefcase"></i>
                        <?php
                        _e('Project complete:', ET_DOMAIN); ?>
                        <span class="info">
                            <?php
                            echo fre_count_user_posts_by_type($user_id, BID, 'complete'); ?>
                        </span>
                    </div>
                </li>
                <?php
            } else { ?>
                <li>
                    <div class="address"><i class="fa fa-map-marker"></i>
                        <?php
                        _e('Location:', ET_DOMAIN); ?>
                        <span class="info" title="<?php
                        echo $user_data->location; ?>">
                            <?php
                            echo $user_data->location; ?>
                        </span>
                    </div>
                </li>
                <li>
                    <div class="spent"><i class="fa fa-money"></i>
                        <?php
                        _e('Total spent:', ET_DOMAIN); ?>
                        <span class="info"><?php
                            echo fre_price_format(fre_count_total_user_spent($user_id)); ?></span>
                    </div>
                </li>
                <li>
                    <div class="briefcase"><i class="fa fa-briefcase"></i>
                        <?php
                        _e('Project posted:', ET_DOMAIN); ?>
                        <span class="info"><?php
                            echo fre_count_user_posts_by_type($user_id, 'project', '"publish","complete","close" ', true); ?></span>

                    </div>
                </li>
                <li>
                    <div class="hired"><i class="fa fa-send"></i>
                        <?php
                        _e('Hires:', ET_DOMAIN); ?>
                        <span class="info"><?php
                            echo fre_count_user_posts_by_type($user_id, 'project', 'complete'); ?></span>
                    </div>
                </li>

                <?php
            }
            ?>

        </ul>
        <?php
        do_action('fre_after_block_user_info', $user_id);
    }
}
?>