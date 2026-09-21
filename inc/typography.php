<?php
/**
 * Site type: Commissioner headings, Source Sans 3 body.
 * Hero heading stays Cormorant Garamond. Do not load Sofia Pro, Quicksand, Raleway, or Poppins.
 *
 * @package Sober_Child
 */

defined( 'ABSPATH' ) || exit;

add_filter( 'sober_get_option', 'sober_child_typography_map_fonts', 10, 2 );
add_filter( 'kirki_get_value', 'sober_child_typography_map_fonts', 10, 2 );
add_filter( 'kirki_enqueue_google_fonts', 'sober_child_remove_unused_google_fonts' );
add_action( 'wp_enqueue_scripts', 'sober_child_enqueue_fonts', 15 );
add_action( 'enqueue_block_editor_assets', 'sober_child_enqueue_fonts', 20 );

/**
 * Customizer settings that should use the heading sans.
 *
 * @return string[]
 */
function sober_child_heading_font_settings() {
	return array(
		'typo_h1',
		'typo_h2',
		'typo_h3',
		'typo_h4',
		'typo_h5',
		'typo_h6',
		'typo_page_header_title',
		'typo_page_header_minimal_title',
		'type_widget_title',
		'type_product_title',
		'typo_woocommerce_headers',
	);
}

/**
 * Map heading settings to Commissioner and everything else to Source Sans 3.
 *
 * Customizer size, weight, color, and transform are left unchanged.
 *
 * @param mixed  $value Option value.
 * @param string $name  Option / field name.
 * @return mixed
 */
function sober_child_typography_map_fonts( $value, $name = '' ) {
	if ( ! is_array( $value ) || empty( $value['font-family'] ) ) {
		return $value;
	}

	if ( in_array( (string) $name, sober_child_heading_font_settings(), true ) ) {
		$value['font-family'] = 'Commissioner';
		return $value;
	}

	$value['font-family'] = 'Source Sans 3';

	return $value;
}

/**
 * Kirki should not enqueue extra Google Fonts; the child loads the families.
 *
 * @param array $fonts Font families and weights.
 * @return array
 */
function sober_child_remove_unused_google_fonts( $fonts ) {
	return is_array( $fonts ) ? array() : array();
}

/**
 * Load Commissioner, Source Sans 3, and slim Cormorant Garamond (hero only).
 *
 * @return void
 */
function sober_child_enqueue_fonts() {
	wp_dequeue_style( 'sober-fonts' );
	wp_deregister_style( 'sober-fonts' );

	wp_enqueue_style(
		'sober-child-fonts',
		'https://fonts.googleapis.com/css2?family=Commissioner:ital,wght@0,400;0,500;0,600;0,700&family=Cormorant+Garamond:ital,wght@0,400;1,400&family=Source+Sans+3:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap&subset=latin,latin-ext',
		array(),
		null
	);
}
