<?php
/*
Plugin Name: Hotel FAQ Plugin
Plugin URI: http://example.com/
Description: A plugin to add a custom post type "Hotel FAQs" and custom taxonomy "Hotel FAQ Categories".
Version: 1.0
Author: Dikshit-sethi
*/

// Add custom taxonomy
add_action('init', 'hotel_faq_categories_register');

function hotel_faq_categories_register() {
    $labels = array(
        'name'                          => 'Hotel FAQ Categories',
        'singular_name'                 => 'Hotel FAQ Category',
        'search_items'                  => 'Search Hotel FAQ Categories',
        'popular_items'                 => 'Popular Hotel FAQ Categories',
        'all_items'                     => 'All Hotel FAQ Categories',
        'parent_item'                   => 'Parent Hotel FAQ Category',
        'edit_item'                     => 'Edit Hotel FAQ Category',
        'update_item'                   => 'Update Hotel FAQ Category',
        'add_new_item'                  => 'Add New Hotel FAQ Category',
        'new_item_name'                 => 'New Hotel FAQ Category',
        'separate_items_with_commas'    => 'Separate hotel FAQ categories with commas',
        'add_or_remove_items'           => 'Add or remove hotel FAQ categories',
        'choose_from_most_used'         => 'Choose from most used hotel FAQ categories'
    );

    $args = array(
        'label'                         => 'Hotel FAQ Categories',
        'labels'                        => $labels,
        'public'                        => true,
        'hierarchical'                  => true,
        'show_ui'                       => true,
        'show_in_nav_menus'             => true,
        'args'                          => array( 'orderby' => 'term_order' ),
        'rewrite'                       => array( 'slug' => 'hotel-faq-categories', 'with_front' => true, 'hierarchical' => true ),
        'query_var'                     => true
    );

    register_taxonomy( 'hotel_faq_categories', 'hotel_faq', $args );
}

// Add custom post type for Hotel FAQs
add_action('init', 'hotel_faq_register');

function hotel_faq_register() {

    $labels = array(
        'name' => 'Hotel FAQs',
        'singular_name' => 'Hotel FAQ',
        'add_new' => 'Add New',
        'add_new_item' => 'Add New Hotel FAQ',
        'edit_item' => 'Edit Hotel FAQ',
        'new_item' => 'New Hotel FAQ',
        'view_item' => 'View Hotel FAQ',
        'search_items' => 'Search Hotel FAQs',
        'not_found' =>  'Nothing found',
        'not_found_in_trash' => 'Nothing found in Trash',
        'parent_item_colon' => ''
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'query_var' => true,
        'has_archive' => true,
        'rewrite' => array( 'slug' => 'hotel-faq', 'with_front' => true ),
        'capability_type' => 'post',
        'menu_position' => 6,
        'supports' => array('title','thumbnail')
    );

    register_post_type( 'hotel_faq' , $args );
}
// for custom post type

function wo_second_editor($post) {

  wp_nonce_field('wo_blue_box_nonce_action', 'wo_blue_box_nonce');

  echo "<h3>Hotel Faq content:</h3>";
  $content = get_post_meta($post->ID, 'wo_blue_box', true);
  wp_editor(htmlspecialchars_decode($content), 'wo_blue_box', array("media_buttons" => false));
}

add_action('edit_form_advanced', 'wo_second_editor');

function wo_save_postdata($post_id, $post, $update) {


  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
    return $post_id;
  }

  if (!current_user_can('edit_post', $post_id)) {
    return $post_id;
  }

  // Verify that the nonce is valid.
  if (!isset($_POST['wo_blue_box_nonce']) || !wp_verify_nonce($_POST['wo_blue_box_nonce'], 'wo_blue_box_nonce_action')) {
    return $post_id;
  }

  // Save the data
  if (isset($_POST['wo_blue_box'])) {
    $data = sanitize_textarea_field($_POST['wo_blue_box']);
    update_post_meta($post_id, 'wo_blue_box', $data);
  }
}

add_action('save_post', 'wo_save_postdata', 10, 3);




