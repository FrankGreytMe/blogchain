<?php
/**
 * Helper functions.
 */

/**
 * Display third-party user data
 */
function wcr_display_user_data() {
	global $wcr_user_manager;
	$is_user_logged_in = $wcr_user_manager->is_user_logged_in();
	if ( $is_user_logged_in ) {
		$user_data = $wcr_user_manager->get_user_data();
		$user_role = $wcr_user_manager->get_user_role();
		$name = join( ' ', array_filter( array( $user_data->first_name, $user_data->last_name ) ) );
		?>
		<div class="wcr-user-data">
			<h3><?php echo 'Welcome, ' . esc_html( $name ); ?></h3>
			<p><?php echo '<p>Email: ' . esc_html( $user_data->email ); ?></p>
			<!--<p><?php echo '<p>User Role: ' . esc_html( $user_role ); ?></p>-->
		</div>
		<?php
	}
}
