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
require_once get_stylesheet_directory() . '/inc/footer.php';
require_once get_stylesheet_directory() . '/inc/variation-order.php';
require_once get_stylesheet_directory() . '/inc/google-login.php';
require_once get_stylesheet_directory() . '/inc/woocommerce.php';

add_action( 'wp_enqueue_scripts', 'sober_child_enqueue_scripts', 20 );
add_action( 'wp_enqueue_scripts', 'sober_child_dequeue_unused_styles', 100 );
add_action( 'wp_print_styles', 'sober_child_dequeue_unused_styles', 1 );
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

	// style.css is the theme header only. Rules live in assets/css/main.css.
	$css_path = get_stylesheet_directory() . '/assets/css/main.css';
	$js_path  = get_stylesheet_directory() . '/assets/js/main.js';

	if ( file_exists( $css_path ) ) {
		wp_enqueue_style(
			'sober-child-main',
			get_stylesheet_directory_uri() . '/assets/css/main.css',
			array( 'sober-child-fonts' ),
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
 * Remove stylesheets that do not paint the storefront.
 *
 * Omnisend prints its wp-admin menu CSS on the front end, and the checkout
 * newsletter block stylesheet on every page. jQuery stays in the head because
 * WooCommerce, WPBakery, and Sober still expect it before their deferred scripts.
 *
 * @return void
 */
function sober_child_dequeue_unused_styles() {
	if ( is_admin() ) {
		return;
	}

	wp_dequeue_style( 'site-styles.css' );

	if ( ! function_exists( 'is_checkout' ) || ! is_checkout() ) {
		wp_dequeue_style( 'omnisend-woocommerce-checkout-block-checkout-newsletter-subscription-block' );
	}
}

/**
 * Prefetch Google Fonts origins.
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
