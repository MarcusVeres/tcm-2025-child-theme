<?php
/**
 * Breadcrumbs Module
 * Prioritize specific product categories in breadcrumb navigation
 *
 * @package TCM_2025_Child
 * @since 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Prioritize product type categories in breadcrumbs
 *
 * @param array $crumbs Current breadcrumbs
 * @param object $breadcrumb Breadcrumb object
 * @return array Modified breadcrumbs
 */
function tcm_child_prioritize_product_breadcrumbs($crumbs, $breadcrumb) {
    global $product;

    if (!is_product() || !$product) {
        return $crumbs;
    }

    $product_cats = wc_get_product_term_ids($product->get_id(), 'product_cat');

    // Define priority categories (product type categories)
    $priority_cats = array(
        'accessories',
        'ads-and-labels',
        'baskets-and-holders',
        'bearings',
        'bumpers',
        'casters',
        'chains',
        'ears',
        'gates',
        'handles',
        'ladders',
        'locks',
        'seatbelts',
        'seats',
        'security',
        'wheels',
            'heavy-duty-wheels',
            'light-duty-wheels',
            'security-wheels',
            'specialty-wheels',
            'super-duty-wheels',
    );

    // Check if product has any priority category
    foreach ($product_cats as $cat_id) {
        $term = get_term($cat_id, 'product_cat');

        if (!$term || is_wp_error($term)) {
            continue;
        }

        if (in_array($term->slug, $priority_cats)) {
            // Rebuild breadcrumbs using this category
            $cat_ancestors = get_ancestors($cat_id, 'product_cat');
            $new_crumbs = array($crumbs[0]); // Home

            // Add ancestors
            foreach (array_reverse($cat_ancestors) as $ancestor) {
                $ancestor_term = get_term($ancestor, 'product_cat');
                if ($ancestor_term && !is_wp_error($ancestor_term)) {
                    $new_crumbs[] = array($ancestor_term->name, get_term_link($ancestor_term));
                }
            }

            // Add the category itself
            $new_crumbs[] = array($term->name, get_term_link($term));

            // Add product
            $new_crumbs[] = array($product->get_name(), get_permalink($product->get_id()));

            return $new_crumbs;
        }
    }

    return $crumbs;
}
add_filter('woocommerce_get_breadcrumb', 'tcm_child_prioritize_product_breadcrumbs', 20, 2);
