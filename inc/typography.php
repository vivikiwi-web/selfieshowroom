<?php
/**
 * Swap Sober’s Sofia Pro webfont for Poppins (Lithuanian glyph coverage).
 *
 * @package Sober_Child
 */

defined( 'ABSPATH' ) || exit;

add_filter( 'sober_get_option', 'sober_child_typography_use_poppins' );
add_filter( 'kirki_get_value', 'sober_child_typography_use_poppins' );

/**
 * Replace Sofia Pro with Poppins in typography option arrays.
 *
 * @param mixed $value Option value.
 * @return mixed
 */
function sober_child_typography_use_poppins( $value ) {
	if ( ! is_array( $value ) || empty( $value['font-family'] ) ) {
		return $value;
	}

	if ( 'Sofia Pro' === $value['font-family'] ) {
		$value['font-family'] = 'Poppins';
	}

	return $value;
}
