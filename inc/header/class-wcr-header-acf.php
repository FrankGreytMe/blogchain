<?php
class WCR_Header_ACF {
	public function __construct() {
		add_filter( 'acf/load_field/name=menu_visibility_wcr_userrole', array( $this, 'wcr_userrole_options' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
	}

	public function wcr_userrole_options( $field ) {
		// Get the repeater field data from the options page
		$user_roles = get_field( 'wcr_user_roles', 'option' );

		// Initialize an empty array for the choices
		$choices = array(
			'all' => esc_html__( 'All', 'material-design-child' ),
		);

		// Check if we have any rows in the repeater field
		if ( $user_roles ) {
			// Loop through each row of the repeater field
			foreach ( $user_roles as $role ) {
				// Get the user_role and user_role_name values
				$user_role      = $role['user_role'];
				$user_role_name = $role['user_role_name'];

				// Add the user_role as the value and user_role_name as the label
				$choices[ $user_role ] = $user_role_name;
			}
		}

		// Assign the generated choices to the select field
		$field['choices'] = $choices;

		return $field;
	}

	public function admin_enqueue_scripts( $hook ) {
		if ( 'nav-menus.php' === $hook ) {
			wp_enqueue_style(
				'menus_acf',
				get_stylesheet_directory_uri() . '/assets/css/admin/menus-acf.css',
				array(),
				filemtime( get_stylesheet_directory() . '/assets/css/admin/menus-acf.css' )
			);
		}
	}
}
new WCR_Header_ACF();
