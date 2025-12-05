<?php
/**
 * Custom WordPress Nav Menu Walker for Material Design Header
 * Integrates with ACF for menu icons and visibility controls
 */

class WCR_Header_Menu_Walker extends Walker_Nav_Menu {

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

		// Only process top-level items
		if ( $depth > 0 ) {
			parent::start_el( $output, $item, $depth, $args, $id );
			return;
		}

		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;

		// Get ACF menu icon
		$menu_icon = get_field( 'menu_icon', $item->ID );
		$icon_html = '';

		if ( $menu_icon ) {
			$icon_html = sprintf(
				'<img src="%s" alt="%s" class="mdc-top-app-bar__icon" width="24" height="24">',
				esc_url( $menu_icon['url'] ),
				esc_attr( $menu_icon['alt'] ?? '' )
			);
		}

		// Build custom class based on menu item title or ID
		$custom_class = $this->get_menu_item_class( $item );

		// Check if this is a language switcher item (Polylang)
		$is_language_switcher = in_array( 'pll-parent-menu-item', $classes ) || in_array( 'menu-item-has-children', $classes );

		if ( $is_language_switcher ) {
			$output .= $indent . '<div class="language-switcher-container mdc-top-app-bar__action-item">';

			$output .= sprintf(
				'<a href="%s" id="language-toggle" class="english-button mdc-top-app-bar__action-item mdc-button mdc-button--icon-leading">%s<span class="mdc-button__label" id="language-label">%s</span></a>',
				'#', // esc_url( $item->url ),
				$icon_html,
				esc_html( $item->title )
			);
		} else {
			$output .= $indent . sprintf(
				'<a href="%s" class="%s mdc-top-app-bar__action-item mdc-button mdc-button--icon-leading %s">%s<span class="mdc-button__label">%s</span></a>',
				esc_url( $item->url ),
				esc_attr( $custom_class ),
				$this->get_additional_button_classes( $item ),
				$icon_html,
				$this->format_menu_label( $item->title, $custom_class )
			);
		}
	}

	/**
	 * Ends the element output, if needed.
	 */
	public function end_el( &$output, $item, $depth = 0, $args = null ) {
		// Check menu item visibility
		if ( ! $this->is_menu_item_visible( $item ) ) {
			return;
		}

		// Only process top-level items
		if ( $depth > 0 ) {
			parent::end_el( $output, $item, $depth, $args );
			return;
		}

		$is_language_switcher = in_array( 'pll-parent-menu-item', $item->classes ) || in_array( 'menu-item-has-children', $item->classes );

		if ( $is_language_switcher ) {
			$output .= "</div>\n";
		} else {
			$output .= "\n";
		}
	}

	/**
	 * Starts the list before the elements are added.
	 */
	public function start_lvl( &$output, $depth = 0, $args = null ) {
		if ( $depth === 0 ) {
			$output .= '<div class="cdk-overlay-pane"><div tabindex="-1" role="menu" class="mat-mdc-menu-panel mat-mdc-elevation-specific mat-menu-below mat-elevation-z8" style="transform-origin: left top;"><div class="mat-mdc-menu-content">';
		}
	}

	/**
	 * Ends the list of after the elements are added.
	 */
	public function end_lvl( &$output, $depth = 0, $args = null ) {
		if ( $depth === 0 ) {
			$output .= '</div></div></div>';
		}
	}

	/**
	 * Start submenu items (language switcher options)
	 */
	public function start_el_lvl( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		if ( $depth === 1 ) {
			// Check visibility for submenu items too
			if ( ! $this->is_menu_item_visible( $item ) ) {
				return;
			}

			$output .= sprintf(
				'<button class="mat-mdc-menu-item mat-mdc-focus-indicator" role="menuitem" tabindex="0" aria-disabled="false" onclick="location.href=\'%s\'"><span class="mat-mdc-menu-item-text">%s</span><div class="mat-ripple mat-mdc-menu-ripple"></div></button>',
				esc_url( $item->url ),
				esc_html( $item->title )
			);
		}
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
	 * Get custom class name based on menu item
	 */
	private function get_menu_item_class( $item ) {
		$title = strtolower( $item->title );
		$class_map = array(
			'home'       => 'home-button',
			'ngo'        => 'ngoblog-chain-button',
			'blog-chain' => 'ngoblog-chain-button',
			'get'        => 'gethelp-button',
			'help'       => 'gethelp-button',
			'sign'       => 'sign-up-button',
			'login'      => 'login-button'
		);

		foreach ( $class_map as $keyword => $class ) {
			if ( strpos( $title, $keyword ) !== false ) {
				return $class;
			}
		}

		// Fallback: generate class from title
		return sanitize_title( $item->title ) . '-button';
	}

	/**
	 * Get additional button classes
	 */
	private function get_additional_button_classes( $item ) {
		$classes = '';

		// Add sign-up-class for sign-up button
		if ( strpos( strtolower( $item->title ), 'sign' ) !== false ) {
			$classes .= ' sign-up-class';
		}

		return trim( $classes );
	}

	/**
	 * Format menu label with paragraph tags for multi-line items
	 */
	private function format_menu_label( $title, $class ) {
		$formatted_title = wp_kses(
			$title,
			array(
				'br' => array(),
			)
		);

		// Replace <br> tags with closing and opening paragraph tags
		if ( strpos( $formatted_title, '<br' ) !== false ) {
			$formatted_title = preg_replace( '/<br\s*\/?>/i', '</p><p>', $formatted_title );
			$formatted_title = '<p>' . $formatted_title . '</p>';
		} else {
			$formatted_title = '<p>' . $formatted_title . '</p>';
		}

		// Special handling for sign-up button
		if ( $class === 'sign-up-button' ) {
			$formatted_title = '<span class="sign-up-label">' . $formatted_title . '</span>';
		}

		return $formatted_title;
	}
}
