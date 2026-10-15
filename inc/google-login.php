<?php
/**
 * Google sign-in on WooCommerce login, register, and checkout login forms.
 *
 * Nextend Social Login handles OAuth. Its free build does not print buttons
 * on WooCommerce forms, so the child theme places them on the existing hooks.
 *
 * @package Sober_Child
 */

defined( 'ABSPATH' ) || exit;

add_action( 'woocommerce_login_form_start', 'sober_child_google_login_button' );
add_action( 'woocommerce_register_form_start', 'sober_child_google_register_button' );

/**
 * Print the Google button above the login fields.
 *
 * @return void
 */
function sober_child_google_login_button() {
	sober_child_google_auth_button( 'login' );
}

/**
 * Print the Google button above the registration fields.
 *
 * @return void
 */
function sober_child_google_register_button() {
	sober_child_google_auth_button( 'register' );
}

/**
 * Print a full-width Google button and an email-form separator.
 *
 * @param string $label_type Nextend label type: login or register.
 * @return void
 */
function sober_child_google_auth_button( $label_type ) {
	if ( is_user_logged_in() || ! class_exists( 'NextendSocialLogin' ) ) {
		return;
	}

	if ( ! NextendSocialLogin::isProviderEnabled( 'google' ) ) {
		return;
	}

	$buttons = NextendSocialLogin::renderButtonsWithContainer( 'fullwidth', false, false, false, 'center', $label_type );

	if ( ! is_string( $buttons ) || '' === $buttons ) {
		return;
	}

	echo '<div class="ss-google-login">';
	// Nextend builds the button markup, including the provider SVG.
	echo $buttons; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo '<p class="ss-google-login__or"><span>' . esc_html( sober_child_google_login_or_label() ) . '</span></p>';
	echo '</div>';
}

/**
 * Separator between Google and the email form.
 *
 * @return string
 */
function sober_child_google_login_or_label() {
	$label = __( 'or', 'sober-child' );

	if ( 'or' === $label && str_starts_with( determine_locale(), 'lt' ) ) {
		return 'arba';
	}

	return $label;
}
