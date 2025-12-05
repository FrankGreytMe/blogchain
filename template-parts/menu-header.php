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
			<!-- Desktop Menu -->
			<?php
			wp_nav_menu( array(
				'theme_location' => 'primary',
				'menu_class'     => 'primary-menu mdc-top-app-bar__action-items desktop-menu-items',
				'walker'         => new WCR_Header_Menu_Walker(),
				// 'container'      => false,
				// 'items_wrap'     => '<div class="%2$s">%3$s</div>',
				'fallback_cb'    => false,
				'depth'          => 2,
			));
			?>
			<div class="mobile-menu-items mdc-menu-surface--anchor" id="mobile-menu-anchor">
				<button class="mdc-icon-button" id="burger-menu-button" aria-haspopup="true">
					<img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/blog-network.svg" alt="Menu Icon" width="16" height="16">
					<span><?php echo pll__('Blog-Chain'); ?></span>
				</button>
			</div>
		</div>
	</div>

	<!-- Mobile Dropdown Menu -->
	<?php
	wp_nav_menu( array(
		'theme_location'  => 'primary_mobile',
		'menu_class'      => 'primary-menu-mobile mdc-list',
		'container_id'    => 'mobile-drawer',
		'container_class' => 'mdc-menu mdc-menu-surface',
		'walker'          => new WCR_Mobile_Menu_Walker(),
		// 'container'    => false,
		// 'items_wrap'   => '<div class="%2$s">%3$s</div>',
		'fallback_cb'     => false,
		'depth'           => 1,
	));
	?>
</div>
<!-- Language modal -->
<?php include get_theme_file_path('inc/menus/language-modal.php'); ?>
