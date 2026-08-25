<?php
/**
 * Selfies Showroom campaign hero — WPBakery element.
 *
 * @package Sober_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the hero shortcode with WPBakery.
 *
 * @return void
 */
function sober_child_vc_map_hero() {
	if ( ! function_exists( 'vc_map' ) ) {
		return;
	}

	vc_map(
		array(
			'name'        => esc_html__( 'Selfies Hero', 'sober-child' ),
			'description' => esc_html__( 'Premium editorial fashion campaign hero', 'sober-child' ),
			'base'        => 'sober_child_hero',
			'icon'        => 'icon-wpb-single-image',
			'category'    => esc_html__( 'Selfies Showroom', 'sober-child' ),
			'params'      => array(
				array(
					'type'        => 'attach_image',
					'heading'     => esc_html__( 'Background image', 'sober-child' ),
					'param_name'  => 'image',
					'description' => esc_html__( 'Wide fashion photo (model on the right, space on the left).', 'sober-child' ),
					'admin_label' => true,
				),
				array(
					'type'        => 'textfield',
					'heading'     => esc_html__( 'Brand line', 'sober-child' ),
					'param_name'  => 'brand_text',
					'value'       => 'Selfies Showroom —',
					'description' => esc_html__( 'Small line above the heading.', 'sober-child' ),
				),
				array(
					'type'        => 'textarea',
					'heading'     => esc_html__( 'Heading', 'sober-child' ),
					'param_name'  => 'heading',
					'value'       => "mažiau triukšmo,",
					'description' => esc_html__( 'Main editorial line(s). Line breaks are allowed.', 'sober-child' ),
					'admin_label' => true,
				),
				array(
					'type'        => 'textfield',
					'heading'     => esc_html__( 'Heading accent (italic)', 'sober-child' ),
					'param_name'  => 'heading_accent',
					'value'       => 'daugiau tavęs',
					'description' => esc_html__( 'Italic serif accent line under the heading.', 'sober-child' ),
				),
				array(
					'type'        => 'textfield',
					'heading'     => esc_html__( 'Button text', 'sober-child' ),
					'param_name'  => 'button_text',
					'value'       => 'ATRASK KOLEKCIJĄ',
				),
				array(
					'type'        => 'vc_link',
					'heading'     => esc_html__( 'Button link', 'sober-child' ),
					'param_name'  => 'button_link',
				),
				array(
					'type'        => 'textfield',
					'heading'     => esc_html__( 'Extra class name', 'sober-child' ),
					'param_name'  => 'el_class',
				),
				array(
					'type'       => 'css_editor',
					'heading'    => esc_html__( 'CSS box', 'sober-child' ),
					'param_name' => 'css',
					'group'      => esc_html__( 'Design Options', 'sober-child' ),
				),
			),
		)
	);
}
add_action( 'vc_before_init', 'sober_child_vc_map_hero' );

/**
 * Render the hero shortcode.
 *
 * @param array $atts Shortcode attributes.
 * @return string
 */
function sober_child_hero_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'image'          => '',
			'brand_text'     => 'Selfies Showroom —',
			'heading'        => 'mažiau triukšmo,',
			'heading_accent' => 'daugiau tavęs',
			'button_text'    => 'ATRASK KOLEKCIJĄ',
			'button_link'    => '',
			'el_class'       => '',
			'css'            => '',
		),
		$atts,
		'sober_child_hero'
	);

	$image_id  = absint( $atts['image'] );
	$image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'full' ) : '';

	$link = array(
		'url'    => '',
		'title'  => '',
		'target' => '',
		'rel'    => '',
	);

	if ( $atts['button_link'] && function_exists( 'vc_build_link' ) ) {
		$built = vc_build_link( $atts['button_link'] );
		if ( is_array( $built ) ) {
			$link = array_merge( $link, $built );
		}
	}

	$css_class = array( 'ss-hero' );

	if ( ! empty( $atts['el_class'] ) ) {
		$css_class[] = $atts['el_class'];
	}

	if ( $atts['css'] && function_exists( 'vc_shortcode_custom_css_class' ) ) {
		$css_class[] = vc_shortcode_custom_css_class( $atts['css'] );
	}

	ob_start();
	include get_stylesheet_directory() . '/vc_templates/sober_child_hero.php';
	return ob_get_clean();
}
add_shortcode( 'sober_child_hero', 'sober_child_hero_shortcode' );

/**
 * Preload the hero background when the shortcode is present (LCP).
 *
 * @return void
 */
function sober_child_hero_preload() {
	if ( is_admin() || ! is_singular() ) {
		return;
	}

	$post = get_post();
	if ( ! $post instanceof WP_Post || ! has_shortcode( $post->post_content, 'sober_child_hero' ) ) {
		return;
	}

	if ( ! preg_match( '/\[sober_child_hero([^\]]*)\]/', $post->post_content, $match ) ) {
		return;
	}

	$atts = shortcode_parse_atts( trim( $match[1] ) );
	if ( empty( $atts['image'] ) ) {
		return;
	}

	$url = wp_get_attachment_image_url( absint( $atts['image'] ), 'full' );
	if ( ! $url ) {
		return;
	}

	printf(
		'<link rel="preload" as="image" href="%s" fetchpriority="high" />' . "\n",
		esc_url( $url )
	);
}
add_action( 'wp_head', 'sober_child_hero_preload', 2 );
