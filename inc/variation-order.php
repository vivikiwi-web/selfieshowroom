<?php
/**
 * Order variation attribute options by variation menu order.
 *
 * @package Sober_Child
 */

defined( 'ABSPATH' ) || exit;

add_filter( 'woocommerce_dropdown_variation_attribute_options_args', 'sober_child_sort_variation_attribute_options', 20 );
add_filter( 'woocommerce_get_product_terms', 'sober_child_sort_variation_attribute_terms', 20, 4 );
add_filter( 'woocommerce_dropdown_variation_attribute_options_html', 'sober_child_end_variation_attribute_sort', 999, 2 );

/**
 * Reorder dropdown options to follow variation menu_order.
 *
 * Taxonomy attributes are reordered in woocommerce_get_product_terms.
 * WooCommerce and WCBoost reload those terms and ignore the options array order.
 *
 * @param array $args Dropdown arguments.
 * @return array
 */
function sober_child_sort_variation_attribute_options( $args ) {
	if ( empty( $args['product'] ) || empty( $args['attribute'] ) || ! $args['product'] instanceof WC_Product ) {
		return $args;
	}

	$product   = $args['product'];
	$attribute = (string) $args['attribute'];

	if ( ! $product->is_type( 'variable' ) ) {
		return $args;
	}

	if ( ! taxonomy_exists( $attribute ) ) {
		$options         = isset( $args['options'] ) && is_array( $args['options'] ) ? $args['options'] : array();
		$args['options'] = sober_child_order_attribute_values( $product, $attribute, $options );
	}

	sober_child_variation_sort_attribute( $attribute );

	return $args;
}

/**
 * Reorder taxonomy terms while a variation attribute dropdown is rendering.
 *
 * @param array  $terms      Product terms.
 * @param int    $product_id Product ID.
 * @param string $taxonomy   Taxonomy slug.
 * @param array  $args       Term query arguments.
 * @return array
 */
function sober_child_sort_variation_attribute_terms( $terms, $product_id, $taxonomy, $args ) {
	unset( $args );

	$current = sober_child_variation_sort_attribute();

	if ( ! $current || $taxonomy !== $current || ! is_array( $terms ) || empty( $terms ) || ! isset( $terms[0]->slug ) ) {
		return $terms;
	}

	$product = wc_get_product( $product_id );

	if ( ! $product instanceof WC_Product ) {
		return $terms;
	}

	$ordered_slugs = sober_child_order_attribute_values( $product, $taxonomy, wp_list_pluck( $terms, 'slug' ) );
	$by_slug       = array();

	foreach ( $terms as $term ) {
		$by_slug[ $term->slug ] = $term;
	}

	$sorted = array();

	foreach ( $ordered_slugs as $slug ) {
		if ( isset( $by_slug[ $slug ] ) ) {
			$sorted[] = $by_slug[ $slug ];
		}
	}

	return $sorted;
}

/**
 * Clear the attribute currently being sorted.
 *
 * @param string $html Dropdown HTML.
 * @param array  $args Dropdown arguments.
 * @return string
 */
function sober_child_end_variation_attribute_sort( $html, $args ) {
	$attribute = isset( $args['attribute'] ) ? (string) $args['attribute'] : '';

	if ( '' === $attribute || $attribute === sober_child_variation_sort_attribute() ) {
		sober_child_variation_sort_attribute( false );
	}

	return $html;
}

/**
 * Remember which attribute is being rendered.
 *
 * @param string|false|null $attribute Attribute name to set, false to clear, null to read.
 * @return string|null
 */
function sober_child_variation_sort_attribute( $attribute = null ) {
	static $current = null;

	if ( false === $attribute ) {
		$current = null;
		return null;
	}

	if ( is_string( $attribute ) ) {
		$current = $attribute;
	}

	return $current;
}

/**
 * Order attribute values by the first variation that uses each value.
 *
 * Variations come back in menu_order, then ID. An empty value ("any")
 * does not claim a position. Values missing from variations stay at the
 * end, in their original order.
 *
 * @param WC_Product $product   Variable product.
 * @param string     $attribute Attribute name.
 * @param array      $options   Current option values or slugs.
 * @return array
 */
function sober_child_order_attribute_values( $product, $attribute, $options ) {
	$options  = array_map( 'strval', array_values( $options ) );
	$meta_key = 'attribute_' . sanitize_title( $attribute );
	$ordered  = array();
	$seen     = array();

	foreach ( $product->get_children() as $child_id ) {
		$variation = wc_get_product( $child_id );

		if ( ! $variation instanceof WC_Product_Variation ) {
			continue;
		}

		$attributes = $variation->get_variation_attributes();
		$value      = isset( $attributes[ $meta_key ] ) ? (string) $attributes[ $meta_key ] : '';

		if ( '' === $value || isset( $seen[ $value ] ) ) {
			continue;
		}

		$seen[ $value ] = true;
		$ordered[]      = $value;
	}

	foreach ( $options as $option ) {
		if ( isset( $seen[ $option ] ) ) {
			continue;
		}

		$seen[ $option ] = true;
		$ordered[]       = $option;
	}

	if ( empty( $options ) ) {
		return $ordered;
	}

	$allowed = array_flip( $options );

	return array_values(
		array_filter(
			$ordered,
			static function ( $value ) use ( $allowed ) {
				return isset( $allowed[ $value ] );
			}
		)
	);
}
