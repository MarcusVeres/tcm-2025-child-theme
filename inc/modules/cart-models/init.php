<?php
/**
 * Cart Models Module
 * Custom display and shortcode for cart models category
 *
 * @package TCM_2025_Child
 * @since 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Override cart models category page
 */
function tcm_child_override_cart_models_page() {
    if (!is_product_category('cart-models')) {
        return;
    }

    // Remove standard product listing elements
    remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
    remove_action('woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 10);
    remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
    remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);
    remove_action('woocommerce_shop_loop', 'wc_setup_loop');
    remove_action('woocommerce_after_shop_loop', 'woocommerce_pagination', 10);

    // Add custom content
    add_action('woocommerce_before_main_content', 'tcm_child_display_custom_cart_models', 30);
}
add_action('template_redirect', 'tcm_child_override_cart_models_page');

/**
 * Display custom cart models grid
 */
function tcm_child_display_custom_cart_models() {
    // Get the current category
    $term = get_term_by('slug', 'cart-models', 'product_cat');

    if (!$term) {
        return;
    }

    // Get subcategories
    $subcategories = get_terms(array(
        'taxonomy' => 'product_cat',
        'hide_empty' => true,
        'parent' => $term->term_id,
        'meta_key' => 'category_order',
        'orderby' => 'meta_value_num',
        'order' => 'ASC'
    ));

    echo '<div class="cart-models-grid">';
    foreach ($subcategories as $subcategory) {
        $thumbnail_id = get_term_meta($subcategory->term_id, 'thumbnail_id', true);
        $image = $thumbnail_id ? wp_get_attachment_url($thumbnail_id) : wc_placeholder_img_src();

        echo '<div class="cart-model-card">';
        echo '<a href="' . esc_url(get_term_link($subcategory)) . '">';
        echo '<img src="' . esc_url($image) . '" alt="' . esc_attr($subcategory->name) . '">';
        echo '<h2>' . esc_html($subcategory->name) . '</h2>';
        echo '</a>';
        echo '</div>';
    }
    echo '</div>';

    // Add inline CSS for the grid
    tcm_child_cart_models_inline_css();
}

/**
 * Shortcode to display cart models
 *
 * Usage: [display_cart_models columns="3" limit="12"]
 *
 * @param array $atts Shortcode attributes
 * @return string HTML output
 */
function tcm_child_cart_models_shortcode($atts) {
    // Parse attributes
    $atts = shortcode_atts(array(
        'columns' => 3,
        'limit' => -1,
    ), $atts);

    // Get current page from query var
    $paged = isset($_GET['cart_page']) ? absint($_GET['cart_page']) : 1;

    // Get cart-models category
    $parent_term = get_term_by('slug', 'cart-models', 'product_cat');
    if (!$parent_term) {
        return '';
    }

    // Query arguments
    $args = array(
        'taxonomy' => 'product_cat',
        'hide_empty' => true,
        'parent' => $parent_term->term_id,
        'number' => $atts['limit'],
        'offset' => ($paged - 1) * $atts['limit'],
        'meta_key' => 'category_order',
        'orderby' => 'meta_value_num',
        'order' => 'ASC'
    );

    // Get subcategories
    $subcategories = get_terms($args);

    // Count total for pagination
    $total_terms = wp_count_terms('product_cat', array('parent' => $parent_term->term_id));
    $total_pages = ceil($total_terms / $atts['limit']);

    // Start output buffer
    ob_start();

    // Render the grid
    echo '<div class="cart-models-grid columns-' . esc_attr($atts['columns']) . '">';
    foreach ($subcategories as $subcategory) {
        $thumbnail_id = get_term_meta($subcategory->term_id, 'thumbnail_id', true);
        $image = $thumbnail_id ? wp_get_attachment_url($thumbnail_id) : wc_placeholder_img_src();

        echo '<div class="cart-model-card">';
        echo '<a href="' . esc_url(get_term_link($subcategory)) . '">';
        echo '<div class="cart-img-wrapper">';
        echo '<img src="' . esc_url($image) . '" alt="' . esc_attr($subcategory->name) . '">';
        echo '</div>';
        echo '<h2>' . esc_html($subcategory->name) . '</h2>';
        echo '</a>';
        echo '</div>';
    }
    echo '</div>';

    // Custom pagination
    if ($total_pages > 1) {
        echo '<div class="cart-models-pagination">';

        // Create the base URL (current page URL without the cart_page parameter)
        $base_url = remove_query_arg('cart_page');

        // Previous page
        if ($paged > 1) {
            echo '<a href="' . esc_url(add_query_arg('cart_page', $paged - 1, $base_url)) . '" class="prev page-numbers">&laquo; Previous</a>';
        }

        // Page numbers
        for ($i = 1; $i <= $total_pages; $i++) {
            if ($i == $paged) {
                echo '<span class="page-numbers current">' . $i . '</span>';
            } else {
                echo '<a href="' . esc_url(add_query_arg('cart_page', $i, $base_url)) . '" class="page-numbers">' . $i . '</a>';
            }
        }

        // Next page
        if ($paged < $total_pages) {
            echo '<a href="' . esc_url(add_query_arg('cart_page', $paged + 1, $base_url)) . '" class="next page-numbers">Next &raquo;</a>';
        }

        echo '</div>';
    }

    // Add CSS for grid
    tcm_child_cart_models_inline_css();

    return ob_get_clean();
}
add_shortcode('display_cart_models', 'tcm_child_cart_models_shortcode');

/**
 * Output inline CSS for cart models grid
 */
function tcm_child_cart_models_inline_css() {
    static $css_output = false;

    // Only output CSS once
    if ($css_output) {
        return;
    }

    $css_output = true;

    ?>
    <style>
        /* Scope WooCommerce element hiding to ONLY cart-models pages */
        .tax-product_cat.term-cart-models .wc-block-product-template__responsive,
        .tax-product_cat.term-cart-models .wc-block-product-template,
        .tax-product_cat.term-cart-models .wp-block-woocommerce-product-template,
        .tax-product_cat.term-cart-models .wc-block-product-results-count,
        .tax-product_cat.term-cart-models .wp-block-woocommerce-product-results-count,
        .tax-product_cat.term-cart-models .wc-block-catalog-sorting,
        .tax-product_cat.term-cart-models .wp-block-woocommerce-catalog-sorting {
            display: none !important;
        }

        .cart-models-grid {
            display: grid;
            grid-template-columns: repeat(var(--columns, 3), 1fr);
            gap: 20px;
            margin: 30px 0;
            width: auto !important;
            margin-left: 0 !important;
            margin-right: 0 !important;
            max-width: none !important;
        }

        .cart-models-grid.columns-2 { --columns: 2; }
        .cart-models-grid.columns-3 { --columns: 3; }
        .cart-models-grid.columns-4 { --columns: 4; }

        .cart-model-card {
            border: 1px solid #eee;
            border-radius: 5px;
            padding: 15px;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .cart-model-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .cart-model-card > a {
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .cart-model-card .cart-img-wrapper {
            display: flex;
            height: 100%;
            justify-content: center;
            flex-direction: column;
        }

        .cart-model-card img {
            max-width: 100%;
            height: auto;
            margin-bottom: 10px;
        }

        .cart-model-card h2 {
            margin: 0;
            font-size: 18px;
        }

        @media (max-width: 768px) {
            .cart-models-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 480px) {
            .cart-models-grid {
                grid-template-columns: 1fr;
            }
        }

        /* PAGINATION */
        .cart-models-pagination {
            text-align: center;
            margin: 20px 0;
        }

        .cart-models-pagination .page-numbers {
            padding: 5px 10px;
            margin: 0 5px;
            border: 1px solid #ddd;
            border-radius: 3px;
            display: inline-block;
        }

        .cart-models-pagination .current {
            background: #ebe9eb;
            color: #8a7e88;
        }
    </style>
    <?php
}
