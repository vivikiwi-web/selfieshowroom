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
					'type'        => 'attach_image',
					'heading'     => esc_html__( 'Mobile background image', 'sober-child' ),
					'param_name'  => 'image_mobile',
					'description' => esc_html__( 'Optional image for screens up to 767px. The desktop image is used when this is empty.', 'sober-child' ),
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
			'image_mobile'   => '',
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

	$images        = sober_child_hero_resolve_images( $atts['image'], $atts['image_mobile'] );
	$desktop_image = $images['desktop'];
	$mobile_image  = $images['mobile'];

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

	if ( ! empty( $mobile_image['url'] ) ) {
		$css_class[] = 'ss-hero--mobile-crop';
	}

	if ( ! empty( $atts['el_class'] ) ) {
		$css_class[] = $atts['el_class'];
	}

	if ( $atts['css'] && function_exists( 'vc_shortcode_custom_css_class' ) ) {
		$css_class[] = vc_shortcode_custom_css_class( $atts['css'] );
	}

	// Claim the single high-priority image slot before later shortcodes render.
	if ( ! empty( $desktop_image['url'] ) && function_exists( 'wp_high_priority_element_flag' ) ) {
		wp_high_priority_element_flag( false );
	}

	ob_start();
	include get_stylesheet_directory() . '/vc_templates/sober_child_hero.php';
	return ob_get_clean();
}
add_shortcode( 'sober_child_hero', 'sober_child_hero_shortcode' );

/**
 * Attachment data used by the hero image and its preload.
 *
 * @param int|string $attachment_id Attachment ID.
 * @return array{url:string,width:int,height:int,type:string}
 */
function sober_child_hero_attachment( $attachment_id ) {
	$attachment_id = absint( $attachment_id );
	$empty         = array(
		'url'    => '',
		'width'  => 0,
		'height' => 0,
		'type'   => '',
	);

	if ( ! $attachment_id ) {
		return $empty;
	}

	$src = wp_get_attachment_image_src( $attachment_id, 'full' );
	if ( empty( $src[0] ) ) {
		return $empty;
	}

	$filetype = wp_check_filetype( $src[0] );

	return array(
		'url'    => $src[0],
		'width'  => isset( $src[1] ) ? (int) $src[1] : 0,
		'height' => isset( $src[2] ) ? (int) $src[2] : 0,
		'type'   => ! empty( $filetype['type'] ) ? $filetype['type'] : '',
	);
}

/**
 * Desktop image, plus a mobile image only when it is a different file.
 *
 * A mobile-only selection is promoted to the desktop slot so the hero still renders.
 *
 * @param int|string $desktop_id Desktop attachment ID.
 * @param int|string $mobile_id  Mobile attachment ID.
 * @return array{desktop:array,mobile:array}
 */
function sober_child_hero_resolve_images( $desktop_id, $mobile_id ) {
	$desktop = sober_child_hero_attachment( $desktop_id );
	$mobile  = sober_child_hero_attachment( $mobile_id );

	if ( ! empty( $mobile['url'] ) && ! empty( $desktop['url'] ) && $mobile['url'] === $desktop['url'] ) {
		$mobile = array(
			'url'    => '',
			'width'  => 0,
			'height' => 0,
			'type'   => '',
		);
	}

	if ( empty( $desktop['url'] ) && ! empty( $mobile['url'] ) ) {
		$desktop = $mobile;
		$mobile  = array(
			'url'    => '',
			'width'  => 0,
			'height' => 0,
			'type'   => '',
		);
	}

	return array(
		'desktop' => $desktop,
		'mobile'  => $mobile,
	);
}

/**
 * Media query for the optional mobile hero image.
 *
 * Keep this aligned with the mobile breakpoint in src/scss/components/_hero.scss.
 *
 * @return string
 */
function sober_child_hero_mobile_media() {
	return '(max-width: 767px)';
}

/**
 * Preload only the hero image the current viewport will paint.
 *
 * @param string $url   Image URL.
 * @param string $media Optional media query. Empty preloads for every viewport.
 * @return void
 */
function sober_child_hero_print_preload( $url, $media = '' ) {
	if ( ! $url ) {
		return;
	}

	echo '<link rel="preload" as="image" href="' . esc_url( $url ) . '" fetchpriority="high"';

	if ( $media ) {
		echo ' media="' . esc_attr( $media ) . '"';
	}

	echo " />\n";
}

/**
 * Preload the hero image when the shortcode is the first-screen image.
 *
 * @return void
 */
function sober_child_hero_preload() {
	static $printed = false;

	if ( $printed || is_admin() || ! is_singular() ) {
		return;
	}

	$post = get_post();
	if ( ! $post instanceof WP_Post || ! has_shortcode( $post->post_content, 'sober_child_hero' ) ) {
		return;
	}

	if ( ! preg_match( '/\[sober_child_hero([^\]]*)\]/', $post->post_content, $match ) ) {
		return;
	}

	$atts   = shortcode_parse_atts( trim( $match[1] ) );
	$atts   = is_array( $atts ) ? $atts : array();
	$images = sober_child_hero_resolve_images(
		isset( $atts['image'] ) ? $atts['image'] : '',
		isset( $atts['image_mobile'] ) ? $atts['image_mobile'] : ''
	);

	if ( empty( $images['desktop']['url'] ) ) {
		return;
	}

	$printed = true;

	if ( ! empty( $images['mobile']['url'] ) ) {
		sober_child_hero_print_preload( $images['mobile']['url'], sober_child_hero_mobile_media() );
		sober_child_hero_print_preload( $images['desktop']['url'], '(min-width: 768px)' );
		return;
	}

	sober_child_hero_print_preload( $images['desktop']['url'] );
}
add_action( 'wp_head', 'sober_child_hero_preload', 1 );

/**
 * Keep LiteSpeed from lazy-loading the hero image.
 *
 * @param array $excludes Exclusion needles matched against the img tag.
 * @return array
 */
function sober_child_hero_litespeed_lazy_excludes( $excludes ) {
	if ( ! is_array( $excludes ) ) {
		$excludes = array();
	}

	$excludes[] = 'ss-hero__image';

	return $excludes;
}
add_filter( 'litespeed_media_lazy_img_excludes', 'sober_child_hero_litespeed_lazy_excludes' );
