<?php
function wcr_get_current_wcr_user() {
	$wcr_user = apply_filters( 'wcr_get_current_wcr_user', false );
	// var_dump($wcr_user);
	return $wcr_user;
}
function wcr_is_wcr_user_logged_in() {
	$is_user_logged_in = false;
	$wcr_user = wcr_get_current_wcr_user();
	if ( $wcr_user ) {
		$is_user_logged_in = true;
	}
	return $is_user_logged_in;
}

function wcr_get_wcr_userrole() {
	$userrole = '';
	$is_user_logged_in = wcr_is_wcr_user_logged_in();
	// var_dump($is_user_logged_in);
	if ( $is_user_logged_in ) {
		$userrole = apply_filters( 'wcr_get_wcr_userrole', $userrole );
	}
	return $userrole;
}
