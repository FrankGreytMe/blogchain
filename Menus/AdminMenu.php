<?php

namespace WCR\Menus;

use FilesystemIterator;

class AdminMenu {
    public function initAdminMenu(): void
    {

        if (!function_exists('acf_add_options_page')) {
            error_log('ACF functions not available in initAdminMenu');
            return;
        }

        if (function_exists('acf_add_options_page')) {
            acf_add_options_page([
                'page_title'    => 'WCR Settings',
                'menu_title'    => 'WCR Settings',
                'menu_slug'     => 'wcr-settings',
                'capability'    => 'publish_pages',
                'redirect'      => true,
                'icon_url'      => 'dashicons-admin-generic',
            ]);

            $sub_pages = [
                'Site Settings',
                'Menu Setup',
            ];

            foreach ($sub_pages as $title) {
                $slug = 'wcr-settings-' . sanitize_title($title);
                acf_add_options_sub_page([
                    'page_title'    => $title,
                    'menu_title'    => $title,
                    'parent_slug'   => 'wcr-settings',
                    'menu_slug'     => $slug,
                    'update_button' => 'Update Settings',
                    'capability'    => 'publish_pages',
                ]);
                
            }
        }

        self::initAdminSettingsPage();
    }

    private static function initAdminSettingsPage(): void
    {
        // Updated path without /app/
        $settings_dir = get_stylesheet_directory() . '/ACF/Settings/';
    
        if (!is_dir($settings_dir)) {
            error_log('Settings directory not found: ' . $settings_dir);
            return;
        }
    
        foreach (new FilesystemIterator($settings_dir) as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $className = 'WCR\\ACF\\Settings\\' . $file->getBasename('.php');
                
                if (class_exists($className)) {
                    $className::setACFFields();
                } else {
                    error_log('Class not found: ' . $className);
                }
            }
        }
    }
}
