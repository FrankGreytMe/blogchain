<?php
/**
 * Custom WordPress Nav Menu Walker for Material Design Mobile Menu
 * Integrates with ACF for menu icons and descriptions
 */

class WCR_Mobile_Menu_Walker extends Walker_Nav_Menu {

	/**
	 * Start the element output.
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param WP_Post $item Menu item data object.
	 * @param int $depth Depth of menu item. Used for padding.
	 * @param stdClass $args An object of wp_nav_menu() arguments.
	 * @param int $id Current item ID.
	 */
	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		// Check menu item visibility.
		if ( ! $this->is_menu_item_visible( $item ) ) {
			return;
		}

		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;
		$classes[] = 'mdc-list-item';

		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

		$output .= $indent . '<li' . $class_names . ' role="menuitem">';

		// Get ACF menu icon.
		$menu_icon = get_field( 'menu_icon', $item->ID );
		$icon_html = '';

		if ( $menu_icon ) {
			$icon_html = sprintf(
				'<img src="%s" alt="%s" class="mdc-list-item__graphic" width="16" height="16">',
				esc_url( $menu_icon['url'] ),
				esc_attr( $menu_icon['alt'] ?? '' )
			);
		}

		// Get menu description
		$menu_description = get_field( 'menu_description', $item->ID );
		$description_html = '';

		if ( ! empty( $menu_description ) ) {
			$description_html = sprintf(
				'<span class="field_link_description">%s</span>',
				esc_html( $menu_description )
			);
		}

		// Build the menu item link
		$atts = array();
		$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
		$atts['target'] = ! empty( $item->target )     ? $item->target     : '';
		$atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
		$atts['href']   = ! empty( $item->url )        ? $item->url        : '';
		$atts['class']  = 'mdc-list-item__text';

		// Special handling for language switcher (no href)
		if ( $this->is_language_switcher_item( $item ) ) {
			$atts['id'] = 'open-language-modal';
			unset( $atts['href'] );
		}

		$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( ! empty( $value ) ) {
				$value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}

		$item_output = $args->before;
		$item_output .= '<a' . $attributes . '>';

		$item_output .= $icon_html;
		$item_output .= $this->format_mobile_menu_label( $item->title );

		$item_output .= '</a>';
		$item_output .= $description_html;
		$item_output .= $args->after;

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}

	/**
	 * Ends the element output, if needed.
	 */
	public function end_el( &$output, $item, $depth = 0, $args = null ) {
		// Check menu item visibility
		if ( ! $this->is_menu_item_visible( $item ) ) {
			return;
		}

		$output .= "</li>\n";
	}

	/**
	 * Check if menu item should be visible based on ACF visibility settings
	 */
	private function is_menu_item_visible( $item ) {
		$menu_visibility = get_field( 'menu_visibility', $item->ID );

		// Default to 'all' if no visibility setting is found
		if ( empty( $menu_visibility ) ) {
			$menu_visibility = 'all';
		}

		// Check login status
		$is_logged_in = function_exists( 'wcr_is_wcr_user_logged_in' ) ? wcr_is_wcr_user_logged_in() : false;

		switch ( $menu_visibility ) {
			case 'all':
				// Visible to everyone
				return true;

			case 'logged_in':
				// Only visible if user is logged in
				if ( ! $is_logged_in ) {
					return false;
				}

				$allowed_roles = get_field( 'menu_visibility_wcr_userrole', $item->ID );

				// If user roles are specified, check if current user role is allowed
				if ( ! is_array( $allowed_roles ) || empty( $allowed_roles ) ) {
					return false;
				}

				if ( in_array( 'all', $allowed_roles, true ) ) {
					return true;
				}

				$wcr_user = wcr_get_current_wcr_user();

				if ( ! isset( $wcr_user->role ) || empty( $wcr_user->role ) || ! in_array( $wcr_user->role, $allowed_roles ) ) {
					return false;
				}

				return true;

			case 'logged_out':
				// Only visible if user is logged out
				$visibility = ! $is_logged_in;
				return $visibility;

			default:
				return false;
		}
	}

	/**
	 * Check if this is a language switcher item
	 */
	private function is_language_switcher_item( $item ) {
		$classes = empty( $item->classes ) ? array() : (array) $item->classes;

		// Check if it's a Polylang language switcher item
		if ( in_array( 'pll-parent-menu-item', $classes ) || in_array( 'menu-item-has-children', $classes ) ) {
			return true;
		}

		// Additional check based on title or URL pattern
		$title = strtolower( $item->title );
		$url = $item->url;

		// If title is a language name and URL is empty or "#", consider it language switcher
		$language_names = array( 'english', 'deutsch', 'русский', 'українська', 'english', 'german', 'russian', 'ukrainian' );

		if ( in_array( $title, $language_names ) && ( empty( $url ) || $url === '#' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Format mobile menu label with paragraph tag
	 */
	private function format_mobile_menu_label( $title ) {
		$formatted_title = esc_html( $title );

		return '<p>' . $formatted_title . '</p>';
	}
}
