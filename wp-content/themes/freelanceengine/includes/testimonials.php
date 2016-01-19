<?php

/**
 * this file contain all function related to testimonials
 */
add_action('init', 'ae_init_testimonial');
function ae_init_testimonial() {
    
    /**
     * register post type testimonial to store testimonial details
     */
    register_post_type('testimonial', array(
        'labels' => array(
            'name'               => __('Testimonials', ET_DOMAIN) ,
            'singular_name'      => __('Testimonial', ET_DOMAIN) ,
            'add_new'            => __('Add New', ET_DOMAIN) ,
            'add_new_item'       => __('Add New Testimonial', ET_DOMAIN) ,
            'edit_item'          => __('Edit Testimonial', ET_DOMAIN) ,
            'new_item'           => __('New Testimonial', ET_DOMAIN) ,
            'all_items'          => __('All Testimonials', ET_DOMAIN) ,
            'view_item'          => __('View Testimonial', ET_DOMAIN) ,
            'search_items'       => __('Search Testimonials', ET_DOMAIN) ,
            'not_found'          => __('No testimon found', ET_DOMAIN) ,
            'not_found_in_trash' => __('No Testimonials found in Trash', ET_DOMAIN) ,
            'parent_item_colon'  => '',
            'menu_name'          => __('Testimonials', ET_DOMAIN)
        ) ,
        'public'             => true,
        'publicly_queryable' => false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => true,
        'capability_type'    => 'post',
        'has_archive'        => 'testimonials',
        'hierarchical'       => true,
        'menu_position'      => null,
        'supports' => array(
            'title',
            'editor',
            'thumbnail'
        )
    ));
}

/**
 * Adds a box to the main column on the Post and Page edit screens.
 */
function test_add_meta_box() {

    $screens = array( 'testimonial' );

    foreach ( $screens as $screen ) {

        add_meta_box(
            'testimonial_sectionid',
            __( 'Testimonials Infomation', ET_DOMAIN ),
            'test_meta_box_callback',
            $screen
        );
    }
}
add_action( 'add_meta_boxes', 'test_add_meta_box' );

/**
 * Prints the box content.
 * 
 * @param WP_Post $post The object for the current post/page.
 */
function test_meta_box_callback( $post ) {

    // Add an nonce field so we can check for it later.
    wp_nonce_field( 'test_meta_box', 'test_meta_box_nonce' );

    /*
     * Use get_post_meta() to retrieve an existing value
     * from the database and use the value for the form.
     */
    $value = get_post_meta( $post->ID, '_test_category', true );

    echo '<label for="test_new_field">';
    _e( 'Category:', ET_DOMAIN );
    echo '</label> ';
    echo '<input type="text" id="test_new_field" name="_test_category" value="' . esc_attr( $value ) . '" size="45" />';
}

/**
 * When the post is saved, saves our custom data.
 *
 * @param int $post_id The ID of the post being saved.
 */
function test_save_meta_box_data( $post_id ) {

    /*
     * We need to verify this came from our screen and with proper authorization,
     * because the save_post action can be triggered at other times.
     */

    // Check if our nonce is set.
    if ( ! isset( $_POST['test_meta_box_nonce'] ) ) {
        return;
    }

    // Verify that the nonce is valid.
    if ( ! wp_verify_nonce( $_POST['test_meta_box_nonce'], 'test_meta_box' ) ) {
        return;
    }

    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    /* OK, it's safe for us to save the data now. */
    
    // Make sure that it is set.
    if ( ! isset( $_POST['_test_category'] ) ) {
        return;
    }

    // Sanitize user input.
    $my_data = sanitize_text_field( $_POST['_test_category'] );

    // Update the meta field in the database.
    update_post_meta( $post_id, '_test_category', $my_data );
}
add_action( 'save_post', 'test_save_meta_box_data' );