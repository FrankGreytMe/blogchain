<?php
// File: taxonomies/register-region.php

function wcr_register_region_taxonomy() {
    $labels = [
        'name'              => _x('Regions', 'taxonomy general name', 'material-design-child'),
        'singular_name'     => _x('Region', 'taxonomy singular name', 'material-design-child'),
        'search_items'      => __('Search Regions', 'material-design-child'),
        'all_items'         => __('All Regions', 'material-design-child'),
        'parent_item'       => __('Parent Region', 'material-design-child'),
        'parent_item_colon' => __('Parent Region:', 'material-design-child'),
        'edit_item'         => __('Edit Region', 'material-design-child'),
        'update_item'       => __('Update Region', 'material-design-child'),
        'add_new_item'      => __('Add New Region', 'material-design-child'),
        'new_item_name'     => __('New Region Name', 'material-design-child'),
        'menu_name'         => __('Regions', 'material-design-child'),
    ];

    $args = [
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'public'            => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'show_in_rest'      => true,
        'rewrite'           => ['slug' => 'region'],
    ];

    register_taxonomy('region', ['post'], $args);
}

add_action('init', 'wcr_register_region_taxonomy');