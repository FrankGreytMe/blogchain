<?php
// File: taxonomies/register-target-group.php

function wcr_register_target_group_taxonomy() {
    $labels = [
        'name'              => _x('Target Groups', 'taxonomy general name', 'material-design-child'),
        'singular_name'     => _x('Target Group', 'taxonomy singular name', 'material-design-child'),
        'search_items'      => __('Search Target Group', 'material-design-child'),
        'all_items'         => __('All Target Group', 'material-design-child'),
        'parent_item'       => __('Parent Target Group', 'material-design-child'),
        'parent_item_colon' => __('Parent Target Group:', 'material-design-child'),
        'edit_item'         => __('Edit Target Group', 'material-design-child'),
        'update_item'       => __('Update Target Group', 'material-design-child'),
        'add_new_item'      => __('Add New Target Group', 'material-design-child'),
        'new_item_name'     => __('New Target Group Name', 'material-design-child'),
        'menu_name'         => __('Target Groups', 'material-design-child'),
    ];

    $args = [
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'public'            => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'show_in_rest'      => true,
        'rewrite'           => ['slug' => 'target-group'],
    ];

    register_taxonomy('target-group', ['post'], $args);
}

add_action('init', 'wcr_register_target_group_taxonomy');