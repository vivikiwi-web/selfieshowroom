<?php
/**
 * Sober Child functions and definitions.
 *
 * @link    https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Sober Child
 */

add_action( 'wp_enqueue_scripts', 'sober_child_enqueue_scripts', 20 );

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

	$css_path = get_stylesheet_directory() . '/assets/css/main.css';
	$js_path  = get_stylesheet_directory() . '/assets/js/main.js';

	if ( file_exists( $css_path ) ) {
		wp_enqueue_style(
			'sober-child-main',
			get_stylesheet_directory_uri() . '/assets/css/main.css',
			array( 'sober-child' ),
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
