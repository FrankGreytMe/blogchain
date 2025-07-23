<?php

namespace WCR\ACF\Settings;

class MenuSetup {
    public static function setACFFields(): void
    {
        if (!function_exists('acf_add_local_field_group')) {
            return;
        }

        acf_add_local_field_group([
            'key' => 'group_menu_setup',
            'title' => 'Menu Setup',
            'fields' => [
                [
                    'key' => 'field_header_tab',
                    'label' => 'Header Settings',
                    'name' => '',
                    'type' => 'tab',
                    'placement' => 'top',
                ],
                [
                    'key' => 'field_header_logo',
                    'label' => 'Header Logo',
                    'name' => 'header_logo',
                    'type' => 'image',
                    'return_format' => 'array',
                    'preview_size' => 'medium',
                    'library' => 'all',
                    'mime_types' => 'jpg,jpeg,png,svg',
                    'wrapper' => [
                        'width' => '50'
                    ],
                ],
                [
                    'key' => 'field_menu_tab',
                    'label' => 'Menu Items',
                    'name' => '',
                    'type' => 'tab',
                    'placement' => 'top',
                ],
                [
                    'key' => 'field_menu_links',
                    'label' => 'Menu Links',
                    'name' => 'menu_links',
                    'type' => 'repeater',
                    'layout' => 'block',
                    'button_label' => 'Add Menu Item',
                    'sub_fields' => [
                        [
                            'key' => 'field_desktop_section',
                            'label' => 'Desktop Version',
                            'name' => '',
                            'type' => 'accordion',
                            'open' => 1,
                            'multi_expand' => 1,
                        ],
                        [
                            'key' => 'field_link_label',
                            'label' => 'Label',
                            'name' => 'label',
                            'type' => 'wysiwyg',
                            'tabs' => 'visual',
                            'toolbar' => 'basic',
                            'media_upload' => 0,
                            'wrapper' => [
                                'width' => '50'
                            ],
                        ],
                        [
                            'key' => 'field_link_url',
                            'label' => 'URL',
                            'name' => 'url',
                            'type' => 'url',
                            'wrapper' => [
                                'width' => '50'
                            ],
                        ],
                        [
                            'key' => 'field_link_icon',
                            'label' => 'Icon Image',
                            'name' => 'icon_image',
                            'type' => 'image',
                            'return_format' => 'array',
                            'preview_size' => 'thumbnail',
                            'library' => 'all',
                            'mime_types' => 'jpg,jpeg,png,gif,svg',
                            'wrapper' => [
                                'width' => '50'
                            ],
                        ],
                        [
                            'key' => 'field_link_roles',
                            'label' => 'Visible to Roles',
                            'name' => 'roles',
                            'type' => 'checkbox',
                            'choices' => [
								/*
                                'administrator' => 'Administrator',
                                'editor' => 'Editor',
                                'author' => 'Author',
                                'subscriber' => 'Subscriber',
								*/
								'all'        => 'All',
								'logged_out' => 'Logged Out User Only',
								'org_admin'  => 'Administrator',
								'consult'    => 'Consult',
								'blog'       => 'Blog',
                            ],
                            'wrapper' => [
                                'width' => '50'
                            ],
                        ],
                        [
                            'key' => 'field_mobile_section',
                            'label' => 'Mobile Version',
                            'name' => '',
                            'type' => 'accordion',
                            'open' => 0,
                            'multi_expand' => 1,
                        ],
                        [
                            'key' => 'field_link_label_mobile',
                            'label' => 'Mobile Label',
                            'name' => 'label_mobile',
                            'type' => 'wysiwyg',
                            'tabs' => 'visual',
                            'media_upload' => 0,
                            'wrapper' => [
                                'width' => '50'
                            ],
                        ],
                        [
                            'key' => 'field_link_url_mobile',
                            'label' => 'Mobile URL',
                            'name' => 'url_mobile',
                            'type' => 'url',
                            'wrapper' => [
                                'width' => '50'
                            ],
                        ],
                        [
                            'key' => 'field_link_icon_mobile',
                            'label' => 'Mobile Icon',
                            'name' => 'icon_image_mobile',
                            'type' => 'image',
                            'return_format' => 'array',
                            'preview_size' => 'thumbnail',
                            'library' => 'all',
                            'mime_types' => 'jpg,jpeg,png,gif,svg',
                            'wrapper' => [
                                'width' => '50'
                            ],
                        ],
                        [
                            'key' => 'field_link_description_section',
                            'label' => 'Mobile Description',
                            'name' => 'description_mobile_item',
                            'type' => 'text',
                            'wrapper' => [
                                'width' => '50'
                            ],
                        ],
                    ],
                ],
            ],
            'location' => [
                [
                    [
                        'param' => 'options_page',
                        'operator' => '==',
                        'value' => 'wcr-settings-menu-setup',
                    ],
                ],
            ],
            'style' => 'seamless',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'active' => true,
        ]);
    }
}
