<?php
/**
 * WooCommerce checkout and address field adjustments.
 *
 * @package Sober_Child
 */

defined( 'ABSPATH' ) || exit;

add_filter( 'woocommerce_get_country_locale', 'sober_child_hide_lithuania_state_field' );

/**
 * Hide the State / County (Apskritis) field for Lithuania.
 *
 * WooCommerce has no LT locale override, so the default required state
 * field appears as a free-text "Apskritis" input. Other countries keep
 * their own state/province fields.
 *
 * @param array $locale Country locale field overrides.
 * @return array
 */
function sober_child_hide_lithuania_state_field( $locale ) {
	if ( ! is_array( $locale ) ) {
		return $locale;
	}

	$locale['LT']['state'] = array(
		'required' => false,
		'hidden'   => true,
	);

	return $locale;
}
