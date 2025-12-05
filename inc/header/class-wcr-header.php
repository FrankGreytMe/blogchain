<?php
class WCR_Header {

	public function __construct() {
		add_action( 'init', array( $this, 'register_nav_menus' ), 5 );
	}

	public function register_nav_menus() {
		// Register menu location.
		register_nav_menus( array(
			'primary'        => esc_html__( 'Primary (Desktop)', 'material-design-child' ),
			'primary_mobile' => esc_html__( 'Primary (Mobile)', 'material-design-child' ),
		));
	}
}
new WCR_Header();
