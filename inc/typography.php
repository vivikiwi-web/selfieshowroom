<?php
/**
 * Load Quicksand instead of Sofia Pro / Poppins (Lithuanian glyph coverage).
 *
 * @package Sober_Child
 */

defined( 'ABSPATH' ) || exit;

add_filter( 'sober_get_option', 'sober_child_typography_use_quicksand' );
add_filter( 'kirki_get_value', 'sober_child_typography_use_quicksand' );
add_filter( 'gettext_with_context', 'sober_child_disable_poppins_google_font', 10, 4 );
add_filter( 'kirki_enqueue_google_fonts', 'sober_child_remove_kirki_poppins_sofia' );
add_action( 'wp_enqueue_scripts', 'sober_child_enqueue_fonts', 15 );
add_action( 'enqueue_block_editor_assets', 'sober_child_enqueue_fonts', 20 );

/**
 * Replace Sofia Pro and Poppins with Quicksand in typography option arrays.
 *
 * @param mixed $value Option value.
 * @return mixed
 */
function sober_child_typography_use_quicksand( $value ) {
	if ( ! is_array( $value ) || empty( $value['font-family'] ) ) {
		return $value;
	}

	$family = $value['font-family'];

	if ( 'Sofia Pro' === $family || 'Poppins' === $family ) {
		$value['font-family'] = 'Quicksand';
	}

	return $value;
}

/**
 * Stop the parent theme from requesting Poppins via Google Fonts.
 *
 * @param string $translation Translated text.
 * @param string $text        Original text.
 * @param string $context     Text context.
 * @param string $domain      Text domain.
 * @return string
 */
function sober_child_disable_poppins_google_font( $translation, $text, $context, $domain ) {
	if ( 'sober' === $domain && 'Poppins font: on or off' === $context ) {
		return 'off';
	}

	return $translation;
}

/**
 * Drop Poppins / Sofia Pro from Kirki’s Google Fonts queue.
 *
 * @param array $fonts Font families and weights.
 * @return array
 */
function sober_child_remove_kirki_poppins_sofia( $fonts ) {
	if ( ! is_array( $fonts ) ) {
		return $fonts;
	}

	foreach ( array_keys( $fonts ) as $family ) {
		$normalized = strtolower( (string) $family );

		if ( false !== strpos( $normalized, 'poppins' ) || false !== strpos( $normalized, 'sofia' ) ) {
			unset( $fonts[ $family ] );
		}
	}

	return $fonts;
}

/**
 * Dequeue parent Poppins URL and load Quicksand + the hero serif.
 *
 * @return void
 */
function sober_child_enqueue_fonts() {
	wp_dequeue_style( 'sober-fonts' );
	wp_deregister_style( 'sober-fonts' );

	wp_enqueue_style(
		'sober-child-fonts',
		'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;1,400;1,500&family=Quicksand:wght@300;400;500;600;700&display=swap',
		array(),
		null
	);
}
