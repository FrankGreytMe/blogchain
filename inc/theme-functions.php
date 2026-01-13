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
