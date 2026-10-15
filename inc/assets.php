<?php
/**
 * Frontend asset loading for the child theme.
 *
 * Parent Sober, WPBakery, WooCommerce, and Sober Addons remain the source of
 * registered handles. This file only dequeues what this storefront does not use.
 *
 * @package Sober_Child
 */

defined( 'ABSPATH' ) || exit;

add_action( 'wp', 'sober_child_disable_unused_global_styles' );
add_action( 'wp_enqueue_scripts', 'sober_child_dequeue_unused_styles', 100 );
add_action( 'wp_print_styles', 'sober_child_dequeue_unused_styles', 1 );
add_action( 'wp_enqueue_scripts', 'sober_child_dequeue_unused_scripts', 100 );
add_filter( 'style_loader_tag', 'sober_child_filter_font_awesome_5_tag', 10, 2 );
add_filter( 'should_load_separate_core_block_assets', 'sober_child_load_block_assets_on_demand' );
add_filter( 'should_load_block_assets_on_demand', 'sober_child_load_block_assets_on_demand' );

/**
 * Gutenberg core CSS is unused on WPBakery/WooCommerce templates.
 *
 * The only published block document in this install is a wp_navigation entry,
 * not a storefront page. Combined block CSS still printed inline on every view.
 *
 * @return bool
 */
function sober_child_load_block_assets_on_demand() {
	return true;
}

/**
 * Combined Gutenberg preset CSS is unused on WPBakery pages.
 *
 * @return void
 */
function sober_child_disable_unused_global_styles() {
	if ( is_admin() || sober_child_view_uses_blocks() ) {
		return;
	}

	remove_action( 'wp_enqueue_scripts', 'wp_enqueue_global_styles' );
	remove_action( 'wp_footer', 'wp_enqueue_global_styles', 1 );
	remove_action( 'wp_body_open', 'wp_global_styles_render_svg_filters' );
}

/**
 * Remove stylesheets that do not paint the current view.
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

	if ( ! sober_child_view_uses_blocks() ) {
		wp_dequeue_style( 'wp-block-library' );
		wp_dequeue_style( 'wp-block-library-theme' );
		wp_dequeue_style( 'classic-theme-styles' );
		wp_dequeue_style( 'global-styles' );
		wp_dequeue_style( 'wc-blocks-style' );
		wp_dequeue_style( 'wc-blocks-vendors-style' );
		wp_dequeue_style( 'wc-blocks-checkout-style' );
	}

	if ( ! function_exists( 'is_shop' ) || ( ! is_shop() && ! is_product_taxonomy() && ! is_cart() && ! is_checkout() && ! is_account_page() ) ) {
		wp_dequeue_style( 'select2' );
	}
}

/**
 * Remove scripts registered globally by Sober / Sober Addons when the view
 * cannot use them. jQuery, owl-carousel, isotope, FitVids, simple-scrollbar,
 * MediaElement, Headroom, notify, and Woo variation scripts stay queued:
 * `sober.js` calls them or the mini-cart / quick view depends on them.
 *
 * @return void
 */
function sober_child_dequeue_unused_scripts() {
	if ( is_admin() ) {
		return;
	}

	if ( ! function_exists( 'is_product' ) || ! is_product() ) {
		wp_dequeue_script( 'sticky-kit' );
	}

	if ( ! function_exists( 'is_shop' ) || ( ! is_shop() && ! is_product_taxonomy() ) ) {
		wp_dequeue_script( 'sober-products-filter-widget' );
		wp_dequeue_script( 'jquery-serialize-object' );
	}

	if ( ! function_exists( 'is_shop' ) || ( ! is_shop() && ! is_product_taxonomy() && ! is_cart() && ! is_checkout() && ! is_account_page() ) ) {
		wp_dequeue_script( 'select2' );
	}

	if ( ! sober_child_needs_sober_shortcodes() ) {
		wp_dequeue_script( 'sober-shortcodes' );
		wp_dequeue_script( 'jquery-countdown' );
		wp_dequeue_script( 'jquery-circle-progress' );
	}

	if ( function_exists( 'is_cart' ) && ( is_cart() || is_checkout() || is_account_page() ) ) {
		wp_dequeue_script( 'vc_woocommerce-add-to-cart-js' );
	}
}

/**
 * Icon boxes on the homepage use images but still enqueue WPBakery FA5.
 * Keep the stylesheet only when a shortcode actually requests an icon font.
 *
 * @param string $tag    HTML link tag.
 * @param string $handle Style handle.
 * @return string
 */
function sober_child_filter_font_awesome_5_tag( $tag, $handle ) {
	if ( is_admin() ) {
		return $tag;
	}

	if ( 'vc_font_awesome_5_shims' !== $handle && 'vc_font_awesome_5' !== $handle ) {
		return $tag;
	}

	return sober_child_needs_font_awesome_5() ? $tag : '';
}

/**
 * Whether the main query is a Gutenberg document.
 *
 * @return bool
 */
function sober_child_view_uses_blocks() {
	if ( ! is_singular() ) {
		return false;
	}

	$post = get_post();

	return $post instanceof WP_Post && has_blocks( $post );
}

/**
 * Sober Addons shortcodes.js (countdown, charts, product tabs AJAX).
 *
 * @return bool
 */
function sober_child_needs_sober_shortcodes() {
	if ( is_front_page() ) {
		return true;
	}

	if ( ! is_singular() ) {
		return false;
	}

	$post = get_post();

	if ( ! $post instanceof WP_Post || '' === $post->post_content ) {
		return false;
	}

	return (bool) preg_match( '/\[sober_/', $post->post_content );
}

/**
 * Font Awesome 5 is only required for icon-font shortcodes, not image icon boxes.
 *
 * @return bool
 */
function sober_child_needs_font_awesome_5() {
	if ( ! is_singular() ) {
		return false;
	}

	$post = get_post();

	if ( ! $post instanceof WP_Post ) {
		return false;
	}

	$content = (string) $post->post_content;

	if ( has_shortcode( $content, 'sober_info_list' ) || has_shortcode( $content, 'vc_icon' ) ) {
		return true;
	}

	if ( preg_match( '/icon_type="(?!image|external_link)[^"]*"/', $content ) ) {
		return true;
	}

	return false !== strpos( $content, 'icon_fontawesome' );
}
