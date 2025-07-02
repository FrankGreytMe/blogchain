<?php
/**
 * Template part for displaying top app bar with ACF menu (using menu surface)
 *
 * @package MaterialDesign
 */

$menu_links = get_field('menu_links', 'option');
if ( ! isset( $class ) ) {
	$class = '';
}
?>

<div class="mdc-top-app-bar top-app-bar <?php echo esc_attr($class); ?>">
    <div class="mdc-top-app-bar__row top-app-bar__header">
        <div class="mdc-top-app-bar__section mdc-top-app-bar__section--align-start">
            <?php
            $custom_logo = get_field('header_logo', 'option');
            $site_title = get_bloginfo('name');

            if ($custom_logo) : ?>
                <a href="<?php echo esc_url('https://wcr.is/home'); ?>" class="custom-logo-link" rel="home">
                    <img src="<?php echo esc_url($custom_logo['url']); ?>"
                         alt="<?php echo esc_attr($custom_logo['alt'] ?: $site_title); ?>"
                         class="custom-logo">
                </a>
            <?php elseif (has_custom_logo()) : ?>
                <?php the_custom_logo(); ?>
            <?php else : ?>
                <span class="mdc-top-app-bar__title"><?php echo esc_html($site_title); ?></span>
            <?php endif; ?>
        </div>

        <div class="mdc-top-app-bar__section mdc-top-app-bar__section--align-end">
            <div id="jwt-test" style="display: none; font-size: 12px; margin-right: 10px;"></div>
            <?php if ($menu_links) : ?>
                <!-- Desktop Menu -->
                <?php include get_theme_file_path('inc/menus/menu-desktop.php'); ?>

                <!-- Mobile Menu Button -->
                <div class="mobile-menu-items mdc-menu-surface--anchor" id="mobile-menu-anchor">
                    <?php foreach ($menu_links as $link) :
                        $current_user = wp_get_current_user();
                        $allowed = empty($link['roles']) || array_intersect($link['roles'], $current_user->roles);

                        $label = trim($link['label']);
                        $label_normalized = trim(strip_tags($label));
                        $label_upper = mb_strtoupper($label_normalized); // Unicode safe

                        // Check for both English and Russian versions
                        $is_login = (
                            strpos($label_upper, 'LOGIN') !== false ||
                            mb_stripos($label_normalized, 'Вход') !== false ||
                            mb_stripos($label_normalized, 'Увійти') !== false
                        );

                        $is_sign_up = (
                            (strpos($label_upper, 'SIGN') !== false && strpos($label_upper, 'UP') !== false) ||
                            mb_stripos($label_normalized, 'ЗАРЕЄСТР') !== false ||
                            mb_stripos($label_normalized, 'УВАТИСЯ') !== false ||
                            mb_stripos($label_normalized, 'ЗАРЕГИСТР') !== false ||
                            mb_stripos($label_normalized, 'ИРОВАТЬСЯ') !== false ||
                            mb_stripos($label_normalized, 'Зареєструватися') !== false
                        );

                        if ($allowed && ($is_login || $is_sign_up)) : ?>
                            <a href="<?php echo esc_url($link['url']); ?>"
                               class="<?php echo $is_sign_up ? 'sign-up-button' : 'login-button'; ?> mdc-top-app-bar__action-item mdc-button mdc-button--icon-leading <?php echo $is_sign_up ? 'sign-up-class' : ''; ?>">

                                <?php if (!empty($link['icon_image_mobile'])) :
                                    $icon = $link['icon_image_mobile']; ?>
                                    <img src="<?php echo esc_url($icon['url']); ?>"
                                         alt="<?php echo esc_attr($icon['alt']); ?>"
                                         class="mdc-top-app-bar__icon"
                                         width="24" height="24">
                                <?php endif; ?>

                                <span class="<?php echo $is_sign_up ? 'sign-up-label' : 'mdc-button__label'; ?>">
                                    <?php
                                    if (!empty($link['label_mobile'])) {
                                        // Don't add line breaks for sign up button
                                        echo wp_kses_post($link['label_mobile']);
                                    } else {
                                        echo wp_kses_post($label);
                                    }
                                    ?>
                                </span>
                            </a>
                        <?php endif;
                    endforeach; ?>

                    <button class="mdc-icon-button" id="burger-menu-button" aria-haspopup="true">
                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/blog-network.svg" alt="Menu Icon" width="16" height="16">
                        <span><?php echo pll__('Blog-Chain'); ?></span>
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Mobile Dropdown Menu -->
     <?php include get_theme_file_path('inc/menus/menu-mobile.php'); ?>
</div>
    <!-- Language modal -->
    <?php include get_theme_file_path('inc/menus/language-modal.php'); ?>
