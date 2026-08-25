<?php
/**
 * Template for [sober_child_hero] shortcode.
 *
 * Expected variables: $atts, $image_url, $link, $css_class.
 *
 * @package Sober_Child
 */

defined( 'ABSPATH' ) || exit;

$brand_text     = isset( $atts['brand_text'] ) ? $atts['brand_text'] : '';
$heading        = isset( $atts['heading'] ) ? $atts['heading'] : '';
$heading_accent = isset( $atts['heading_accent'] ) ? $atts['heading_accent'] : '';
$button_text    = isset( $atts['button_text'] ) ? $atts['button_text'] : '';

$has_image = (bool) $image_url;

$target = ! empty( $link['target'] ) ? $link['target'] : '_self';
$rel    = ! empty( $link['rel'] ) ? $link['rel'] : '';
if ( '_blank' === $target && false === strpos( $rel, 'noopener' ) ) {
	$rel = trim( $rel . ' noopener' );
}
?>
<section
	class="<?php echo esc_attr( implode( ' ', array_filter( $css_class ) ) ); ?>"
	<?php if ( $has_image ) : ?>
		style="--ss-hero-image: url(<?php echo esc_url( $image_url ); ?>);"
	<?php endif; ?>
	data-ss-hero
	aria-label="<?php echo esc_attr__( 'Selfies Showroom hero', 'sober-child' ); ?>"
>
	<div class="ss-hero__media" aria-hidden="true"></div>
	<div class="ss-hero__overlay" aria-hidden="true"></div>

	<div class="ss-hero__inner">
		<div class="ss-hero__content">
			<?php if ( '' !== trim( (string) $brand_text ) ) : ?>
				<p class="ss-hero__brand"><?php echo esc_html( $brand_text ); ?></p>
			<?php endif; ?>

			<?php if ( '' !== trim( (string) $heading ) || '' !== trim( (string) $heading_accent ) ) : ?>
				<h1 class="ss-hero__heading">
					<?php if ( '' !== trim( (string) $heading ) ) : ?>
						<span class="ss-hero__heading-main"><?php echo nl2br( esc_html( $heading ) ); ?></span>
					<?php endif; ?>
					<?php if ( '' !== trim( (string) $heading_accent ) ) : ?>
						<em class="ss-hero__heading-accent"><?php echo esc_html( $heading_accent ); ?></em>
					<?php endif; ?>
				</h1>
			<?php endif; ?>

			<?php if ( '' !== trim( (string) $button_text ) && ! empty( $link['url'] ) ) : ?>
				<a
					class="ss-hero__cta"
					href="<?php echo esc_url( $link['url'] ); ?>"
					target="<?php echo esc_attr( $target ); ?>"
					<?php echo $rel ? 'rel="' . esc_attr( $rel ) . '"' : ''; ?>
				>
					<span><?php echo esc_html( $button_text ); ?></span>
				</a>
			<?php elseif ( '' !== trim( (string) $button_text ) ) : ?>
				<span class="ss-hero__cta ss-hero__cta--static"><span><?php echo esc_html( $button_text ); ?></span></span>
			<?php endif; ?>
		</div>
	</div>
</section>
