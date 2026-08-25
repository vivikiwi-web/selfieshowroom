<?php
/**
 * Sober Child functions and definitions.
 *
 * @link    https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Sober Child
 */

defined( 'ABSPATH' ) || exit;

require_once get_stylesheet_directory() . '/inc/vc-hero.php';
require_once get_stylesheet_directory() . '/inc/typography.php';

add_action( 'wp_enqueue_scripts', 'sober_child_enqueue_scripts', 20 );
add_filter( 'wp_resource_hints', 'sober_child_resource_hints', 10, 2 );

/**
 * Enqueues stylesheets and scripts of the child theme.
 *
 * @return void
 */
function sober_child_enqueue_scripts() {
	if ( is_rtl() ) {
		wp_enqueue_style( 'sober-rtl', get_template_directory_uri() . '/rtl.css' );
	}

	wp_enqueue_style( 'sober-child', get_stylesheet_uri() );

	wp_enqueue_style(
		'sober-child-cormorant',
		'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;1,400;1,500&display=swap',
		array(),
		null
	);

	$css_path = get_stylesheet_directory() . '/assets/css/main.css';
	$js_path  = get_stylesheet_directory() . '/assets/js/main.js';

	if ( file_exists( $css_path ) ) {
		wp_enqueue_style(
			'sober-child-main',
			get_stylesheet_directory_uri() . '/assets/css/main.css',
			array( 'sober-child', 'sober-child-cormorant' ),
			(string) filemtime( $css_path )
		);
	}

	if ( file_exists( $js_path ) ) {
		wp_enqueue_script(
			'sober-child-main',
			get_stylesheet_directory_uri() . '/assets/js/main.js',
			array(),
			(string) filemtime( $js_path ),
			true
		);
	}
}

/**
 * Prefetch Google Fonts origins for the hero serif.
 *
 * @param array  $urls          URLs to print for resource hints.
 * @param string $relation_type The relation type the URLs are printed for.
 * @return array
 */
function sober_child_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = array(
			'href' => 'https://fonts.googleapis.com',
		);
		$urls[] = array(
			'href'        => 'https://fonts.gstatic.com',
			'crossorigin' => 'anonymous',
		);
	}

	return $urls;
}
