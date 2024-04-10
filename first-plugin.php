<?php
/*
Plugin Name: Webindia News
Description: This is a Webindia News plugin for WordPress.
Version: 1.0
Author: Janaki
*/

// Function to create the DB table
function ss_options_install() {
    global $wpdb;

    $table_name = $wpdb->prefix . "webindia_news";
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE $table_name (
            id INT NOT NULL AUTO_INCREMENT,
            title VARCHAR(255) NOT NULL,
            content TEXT,
            PRIMARY KEY (id)
          ) $charset_collate; ";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta($sql);
}

// Run the install scripts upon plugin activation
register_activation_hook(__FILE__, 'ss_options_install');

// Function to insert post data into the custom table
function insert_post_data_into_custom_table($post_id) {
    global $wpdb;

    // Check if the post is being created or updated
    if (wp_is_post_revision($post_id) || wp_is_post_autosave($post_id)) {
        return;
    }

    // Get post data
    $post = get_post($post_id);
    $title = $post->post_title;
    $content = $post->post_content;

    // Insert post data into custom table
    $table_name = $wpdb->prefix . "webindia_news";
    $wpdb->insert(
        $table_name,
        array(
            'title' => $title,
            'content' => $content
        ),
        array(
            '%s', // Title is a string
            '%s'  // Content is a string
        )
    );
}

// Hook into the save_post action to insert data into custom table when a post is created or updated
add_action('save_post', 'insert_post_data_into_custom_table');

// Function to create custom post type
function wpmu_create_post_type() {
    $labels = array( 
        'name' => __( 'Webindia News', 'wpmu' ),
        'singular_name' => __( 'Webindia News', 'wpmu' ),
        'add_new' => __( 'Add News', 'wpmu' ),
        'add_new_item' => __( 'Add Webindia News', 'wpmu' ),
        'edit_item' => __( 'Edit Webindia News', 'wpmu' ),
        'new_item' => __( 'New News', 'wpmu' ),
        'view_item' => __( 'View News', 'wpmu' ),
        'search_items' => __( 'Search Projects', 'wpmu' ),
        'not_found' =>  __( 'No News Found', 'wpmu' ),
        'not_found_in_trash' => __( 'No News found in Trash', 'wpmu' ),
    );
    $args = array(
        'labels' => $labels,
        'has_archive' => true,
        'public' => true,
        'hierarchical' => false,
        'rewrite' => array( 'slug' => 'projects' ),
        'supports' => array(
            'title', 
            'editor', 
            'excerpt', 
            'custom-fields', 
            'thumbnail',
            'page-attributes'
        ),
        'taxonomies' => array( 'post_tag', 'category'), 
    );
    register_post_type( 'project', $args );
} 
add_action( 'init', 'wpmu_create_post_type' );

// For getting 404 page not found
function wpmu_flush_rewrite_rules() {
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'wpmu_flush_rewrite_rules' );
?>
