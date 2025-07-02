<?php
// File: taxonomies/register-main-prooblem.php

function wcr_register_main_problems() {
    $labels = [
        'name'              => _x('Main Problems', 'taxonomy general name', 'material-design-child'),
        'singular_name'     => _x('Main Problem', 'taxonomy singular name', 'material-design-child'),
        'search_items'      => __('Search Main Problem', 'material-design-child'),
        'all_items'         => __('All Main Problems', 'material-design-child'),
        'parent_item'       => __('Parent Main Problem', 'material-design-child'),
        'parent_item_colon' => __('Parent Main Problem:', 'material-design-child'),
        'edit_item'         => __('Edit Main Problem', 'material-design-child'),
        'update_item'       => __('Update Main Problem', 'material-design-child'),
        'add_new_item'      => __('Add New Main Problem', 'material-design-child'),
        'new_item_name'     => __('New Main Problem Name', 'material-design-child'),
        'menu_name'         => __('Main Problems', 'material-design-child'),
    ];

    $args = [
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'public'            => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'show_in_rest'      => true,
        'show_in_rest'      => true,
        'rewrite'           => ['slug' => 'main-problems'],
    ];

    register_taxonomy('main-problems', ['post'], $args);
}

add_action('init', 'wcr_register_main_problems');