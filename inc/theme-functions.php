<?php
function wcr_get_current_wcr_user() {
	$wcr_user = apply_filters( 'wcr_get_current_wcr_user', false );
	return $wcr_user;
}
function wcr_is_wcr_user_logged_in() {
	$wcr_user_manager  = wcr_user_manager();

	$is_user_logged_in = $wcr_user_manager->is_user_logged_in();

	return $is_user_logged_in;
}

function wcr_get_wcr_user_roles() {
	$wcr_user_manager = wcr_user_manager();

	$user_roles = $wcr_user_manager->get_user_roles();
	$user_roles = apply_filters( 'wcr_get_wcr_user_roles', $user_roles );

	return $user_roles;
}

function wcr_get_permission_wp() {
	$wcr_user_manager = wcr_user_manager();

	$permission_wp = $wcr_user_manager->get_permission_wp();
	$permission_wp = apply_filters( 'wcr_get_permission_wp', $permission_wp );

	return $permission_wp;
}

function safe_get_field( $field_name, $post_id = false, $default = '' ) {
    // Only call ACF after init hook has fired.
    if ( ! did_action( 'init' ) ) {
        return $default;
    }

    // Check if ACF function exists.
    if ( ! function_exists( 'get_field' ) ) {
        return $default;
    }

    $value = get_field( $field_name, $post_id );
    return $value !== false && $value !== null ? $value : $default;
}
