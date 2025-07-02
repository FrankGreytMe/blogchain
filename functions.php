<?php
/**
 * Material Design Child Theme - Safe Implementation
 * Loads parent functions AND child styles without conflicts
 */

// 1. PROPERLY ENQUEUE STYLES & SCRIPTS
function material_child_enqueue() {
    // Load parent style using parent's exact handle
    wp_enqueue_style(
        'material-design-google-style',
        get_template_directory_uri() . '/style.css',
        array(),
        wp_get_theme()->get('Version')
    );

    // Material Components CSS from CDN
    wp_enqueue_style(
        'material-components-web-css',
        'https://unpkg.com/material-components-web@latest/dist/material-components-web.min.css',
        array(),
        null
    );

    // Load base child style
    wp_enqueue_style(
        'material-child-style',
        get_stylesheet_uri(),
        array('material-design-google-style'),
        wp_get_theme()->get('Version')
    );

    // Automatically enqueue extra CSS files from assets/css/
    $extra_styles = array('cards', 'singlePost', 'comments', 'footer', 'header', 'languageModal','sidebar'); // Add more filenames here without .css
    foreach ($extra_styles as $style) {
        $path = "/assets/css/{$style}.css";
        $full_path = get_stylesheet_directory() . $path;

        if (file_exists($full_path)) {
            wp_enqueue_style(
                "material-child-style-{$style}",
                get_stylesheet_directory_uri() . $path,
                array('material-child-style'),
                filemtime($full_path)
            );
        }
    }

    // Material Components JS
    wp_enqueue_script(
        'material-components-web',
        'https://unpkg.com/material-components-web@latest/dist/material-components-web.min.js',
        array(),
        null,
        true
    );

    // Custom menu logic
    wp_enqueue_script(
        'material-child-theme-menu',
        get_stylesheet_directory_uri() . '/assets/js/material-menu.js',
        array('material-components-web'),
        filemtime(get_stylesheet_directory() . '/assets/js/material-menu.js'),
        true
    );
    // Animate cards
    wp_enqueue_script(
        'animate-cards-js',
        get_stylesheet_directory_uri() . '/assets/js/animate-cards.js',
        array('material-components-web'),
        filemtime(get_stylesheet_directory() . '/assets/js/animate-cards.js'),
        true
    );
    // Add this new section for burger-menu.js
    wp_enqueue_script(
        'header-menu-js',
        get_stylesheet_directory_uri() . '/assets/js/burger-menu.js',
        array('material-components-web', 'jquery'), // Dependencies
        filemtime(get_stylesheet_directory() . '/assets/js/burger-menu.js'),
        true
    );
    wp_enqueue_script(
        'active-header-js',
        get_stylesheet_directory_uri() . '/assets/js/active-header.js',
        array('material-components-web', 'jquery'), // Dependencies
        filemtime(get_stylesheet_directory() . '/assets/js/active-header.js'),
        true
    );

    wp_enqueue_script(
        'modal-language-menu-js',
        get_stylesheet_directory_uri() . '/assets/js/modal-language-menu.js',
        array('material-components-web', 'jquery'),
        filemtime(get_stylesheet_directory() . '/assets/js/modal-language-menu.js'),
        true
    );

    // Add auth handler script with proper dependencies
    wp_enqueue_script(
        'auth-handler',
        get_stylesheet_directory_uri() . '/assets/js/auth-handler.js',
        array('jquery', 'material-components-web'), // Adding required dependencies
        filemtime(get_stylesheet_directory() . '/assets/js/auth-handler.js'),
        true
    );

    // Localize script if you need to pass PHP variables to JS
    wp_localize_script('burger-menu-js', 'menuSettings', array(
        'mobileBreakpoint' => 768,
        'isMobile' => wp_is_mobile()
    ));
}
add_action('wp_enqueue_scripts', 'material_child_enqueue', 20);

// 2. SAFELY EXTEND PARENT FUNCTIONALITY
function material_child_after_setup() {
    // Add new image sizes
    add_image_size('child-theme-card', 400, 300, true);

    // Register custom menu location
    register_nav_menus(array(
        'child-theme-menu' => __('Child Theme Menu', 'material-design-child')
    ));
}
add_action('after_setup_theme', 'material_child_after_setup', 20);

// 3. ENQUEUE WAVE ANIMATION SCRIPT
function enqueue_wave_animation_script() {
    // Only enqueue on front-end
    if (is_admin()) return;

    // Enqueue Paper.js library
    wp_enqueue_script(
        'paperjs',
        'https://cdnjs.cloudflare.com/ajax/libs/paper.js/0.12.17/paper-full.min.js',
        [],
        '0.12.17',
        true
    );

    // Enqueue the custom wave animation script
    wp_enqueue_script(
        'wave-animation',
        get_stylesheet_directory_uri() . '/assets/js/wave-animation.js',
        ['paperjs'],
        filemtime(get_stylesheet_directory() . '/assets/js/wave-animation.js'),
        true
    );


}
add_action('wp_enqueue_scripts', 'enqueue_wave_animation_script');


// Force-enable MDI icons in menus
add_filter('elem_material_icons_enable_menu_icons', '__return_true');

// Load the AdminMenu class
require_once get_stylesheet_directory() . '/Menus/AdminMenu.php';

// Initialize after all plugins are loaded
add_action('wp_loaded', function() {
    if (class_exists('ACF')) {
        (new \WCR\Menus\AdminMenu())->initAdminMenu();
    } else {
        error_log('ACF plugin not active');
    }
}, 20);

// Autoloader for classes
spl_autoload_register(function ($class) {
    $prefix = 'WCR\\';
    $base_dir = get_stylesheet_directory() . '/';

    if (strpos($class, $prefix) === 0) {
        $relative_class = substr($class, strlen($prefix));
        $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

        if (file_exists($file)) {
            require_once $file;
        } else {
            error_log('Autoload file not found: ' . $file);
        }
    }
});

// Ensure Admins can always use REST API
add_filter('rest_authentication_errors', function($result) {
    if (current_user_can('administrator')) {
        return $result; // Bypass restrictions for admins
    }
    return $result;
});
//taxonomy files
$taxonomy_files = glob(get_stylesheet_directory() . '/taxonomies/*.php');
foreach ($taxonomy_files as $file) {
    require_once $file;
}

// Register strings for Polylang translations
function material_child_register_strings() {
    if (function_exists('pll_register_string')) {
        // Common UI elements
        pll_register_string('leave-comment', 'Leave a comment', 'material-design-child');
        pll_register_string('read-more', 'Read more', 'material-design-child');
        pll_register_string('posted-on', 'Posted on', 'material-design-child');
        pll_register_string('by-author', 'by', 'material-design-child');
        pll_register_string('categories', 'Categories', 'material-design-child');
        pll_register_string('tags', 'Tags', 'material-design-child');
        pll_register_string('search', 'Search', 'material-design-child');
        pll_register_string('menu', 'Menu', 'material-design-child');
        pll_register_string('close', 'Close', 'material-design-child');

        // Comment count strings
        pll_register_string('single-comment', '%s Comment', 'material-design-child');
        pll_register_string('multiple-comments', '%s Comments', 'material-design-child');
        pll_register_string('write-comment', 'Write a comment', 'material-design-child');
        pll_register_string('notify-changes', 'Notify on changes', 'material-design-child');
        pll_register_string('comment-label', 'Comment', 'material-design-child');

        // Menu and Language Modal strings
        pll_register_string('blog-chain', 'Blog-Chain', 'material-design-child');
        pll_register_string('switch-language', 'Switch to preferred Language', 'material-design-child');
        pll_register_string('select-language', 'Select Language', 'material-design-child');
        pll_register_string('language', 'Language', 'material-design-child');
        pll_register_string('cancel', 'Cancel', 'material-design-child');
        pll_register_string('save', 'Save', 'material-design-child');
    }
}
add_action('init', 'material_child_register_strings');


// Enable SVG upload support
function add_svg_support($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    $mimes['svgz'] = 'image/svg+xml';
    return $mimes;
}
add_filter('upload_mimes', 'add_svg_support');

// Enable SVG preview in media library
function fix_svg_display($response) {
    if ($response['mime'] === 'image/svg+xml') {
        $response['sizes'] = [
            'full' => [
                'url' => $response['url'],
                'width' => '',
                'height' => ''
            ]
        ];
    }
    return $response;
}
add_filter('wp_prepare_attachment_for_js', 'fix_svg_display');

// Load WCR REST API class
require_once get_stylesheet_directory() . '/inc/api/class-wcr-rest-api.php';

// Initialize REST API functionality
add_action('init', function() {
    $rest_api = new \WCR\API\WCR_REST_API();
    $rest_api->init();
});

add_action('http_api_curl', function($handle) {
    curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
}, 10);

function wcr_admin_enqueue_scripts( $hook ) {
	if ( 'toplevel_page_wcr-token' === $hook ) {
		wp_enqueue_style(
			'wcr-admin',
			get_stylesheet_directory_uri() . '/assets/css/admin.css',
			array(),
			filemtime( get_stylesheet_directory() . '/assets/css/admin.css' )
		);
	}
}
add_action( 'admin_enqueue_scripts', 'wcr_admin_enqueue_scripts' );

require_once get_stylesheet_directory() . '/inc/token/class-wcr-token.php';
