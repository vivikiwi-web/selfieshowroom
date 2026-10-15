<?php
/**
 * Banner Image shortcode.
 *
 * Child override: do not enqueue the WPBakery default Google font when the
 * banner is set to use theme fonts. The map default is Abril Fatface, and the
 * parent template requested it even though the family is not applied.
 *
 * @package Sober_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Shortcode attributes
 *
 * @var $atts
 * @var $image
 * @var $image_size
 * @var $desc
 * @var $text_position
 * @var $button_text
 * @var $link
 * @var $google_fonts
 * @var $font_container
 * @var $el_class
 * @var $css
 * @var $font_container_data - returned from $this->getAttributes
 * @var $google_fonts_data   - returned from $this->getAttributes
 * Shortcode class
 * @var $this                WPBakeryShortCode_Sober_Banner
 */
$image = $image_size = $button_type = $button_text = $link = $google_fonts = $font_container = $el_class = $css = $font_container_data = $google_fonts_data = '';
// This is needed to extract $font_container_data and $google_fonts_data.
extract( $this->getAttributes( $atts ) ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract

$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract

extract( $this->getStyles( $el_class . $this->getCSSAnimation( $css_animation ), $css, $google_fonts_data, $font_container_data, $atts ) ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract

$settings = get_option( 'wpb_js_google_fonts_subsets' );
if ( is_array( $settings ) && ! empty( $settings ) ) {
	$subsets = '&subset=' . implode( ',', $settings );
} else {
	$subsets = '';
}

$uses_theme_fonts = isset( $atts['use_theme_fonts'] ) && 'yes' === $atts['use_theme_fonts'];

if ( ! $uses_theme_fonts && isset( $google_fonts_data['values']['font_family'] ) ) {
	wp_enqueue_style(
		'vc_google_fonts_' . vc_build_safe_css_class( $google_fonts_data['values']['font_family'] ),
		'https://fonts.googleapis.com/css?family=' . $google_fonts_data['values']['font_family'] . $subsets,
		array(),
		defined( 'WPB_VC_VERSION' ) ? WPB_VC_VERSION : null
	);
}

if ( ! empty( $styles ) ) {
	$style = 'style="' . esc_attr( implode( ';', $styles ) ) . '"';
} else {
	$style = '';
}

$link = vc_build_link( $link );

if ( is_numeric( $image ) ) {
	$size = apply_filters( 'sober_banner_size', $atts['image_size'], $atts, 'sober_banner' );

	if ( function_exists( 'wpb_getImageBySize' ) ) {
		$image = wpb_getImageBySize(
			array(
				'attach_id'  => $atts['image'],
				'thumb_size' => $size,
			)
		);

		$image = $image['thumbnail'];
	} else {
		$size_array = explode( 'x', $size );
		$size       = 1 === count( $size_array ) ? $size : $size_array;

		$image = wp_get_attachment_image_src( $atts['image'], $size );

		if ( $image ) {
			$image = sprintf(
				'<img alt="%s" src="%s">',
				esc_attr( $atts['image'] ),
				esc_url( $image[0] )
			);
		}
	}
} elseif ( $image ) {
	$image = sprintf( '<img alt="%s" src="%s">', esc_attr( wp_strip_all_tags( $content ) ), esc_url( $image ) );
}

switch ( $button_type ) {
	case 'arrow_icon':
		$button = '<span class="sober-button button-icon-only">' . sober_svg_icon( 'icon=right-arrow&echo=0' ) . '</span>';
		break;

	case 'normal':
		$button = '<span class="sober-button button-normal button active">' . esc_html( $button_text ) . '</span>';
		break;

	default:
		$button = '<span class="sober-button button-light line-hover active">' . esc_html( $button_text ) . '</span>';
		break;
}

$output  = '';
$output .= '<div class="' . esc_attr( $css_class ) . '">';
$output .= empty( $link['url'] ) ? '<div class="banner-wrapper">' : '<a href="' . esc_url( $link['url'] ) . '" target="' . esc_attr( $link['target'] ) . '" rel="' . esc_attr( $link['rel'] ) . '">';
$output .= $image;
$output .= '<span class="banner-content">';
if ( $desc ) {
	$output .= '<span class="banner-desc">' . $desc . '</span>';
}
$output .= '<span class="banner-text" ' . $style . '>' . $content . '</span>';
$output .= $button;
$output .= '</span>';
$output .= empty( $link['url'] ) ? '</div>' : '</a>';
$output .= '</div>';

echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
