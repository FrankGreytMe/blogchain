<?php

namespace WCR\ACF\Settings;

class SiteSettings {
    public static function setACFFields(): void
    {
        if (!function_exists('acf_add_local_field_group')) {
            return;
        }

        acf_add_local_field_group([
            'key' => 'group_site_settings',
            'title' => 'Site Settings',
            'fields' => [
                [
                    'key' => 'field_enable_sidebar',
                    'label' => 'Enable Sidebar',
                    'name' => 'enable_sidebar',
                    'type' => 'true_false',
                    'ui' => 1,
                    'default_value' => 0,
                ],
            ],
            'location' => [
                [
                    [
                        'param' => 'options_page',
                        'operator' => '==',
                        'value' => 'wcr-settings-site-settings',
                    ],
                ],
            ],
        ]);
    }
}
