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

// Removed - B2BKing handles visibility natively

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

    // Get subcategories (B2BKing will handle visibility automatically)
    $subcategories = get_terms(array(
        'taxonomy' => 'product_cat',
        'hide_empty' => false,  // Show empty categories too
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
        // Parent category not visible to current user or doesn't exist
        // Show helpful error message for admins
        if (current_user_can('manage_options')) {
            return '<div class="cart-models-error" style="border: 2px solid #dc3232; padding: 20px; margin: 20px 0; background: #fff;">'
                . '<h3 style="color: #dc3232; margin-top: 0;">⚠️ Cart Models Configuration Error</h3>'
                . '<p><strong>The "Cart Models" parent category is not visible to your current user group.</strong></p>'
                . '<p>If the parent category is not visible, no child categories (e.g. Cart Models) will be visible.</p>'
                . '<p>To fix this:</p>'
                . '<ol>'
                . '<li>Go to <strong>Products → Categories</strong></li>'
                . '<li>Find the <strong>"Cart Models"</strong> category</li>'
                . '<li>Edit it and scroll to <strong>"B2BKing Visibility"</strong></li>'
                . '<li>Check the boxes for <strong>ALL customer groups</strong> to make it visible to everyone</li>'
                . '<li>Save changes</li>'
                . '</ol>'
                . '<p><em>Note: Only administrators can see this message. Regular users see nothing.</em></p>'
                . '</div>';
        }
        // Non-admins see nothing (fail silently)
        return '';
    }

    // Query arguments
    $args = array(
        'taxonomy' => 'product_cat',
        'hide_empty' => false,
        'parent' => $parent_term->term_id,
        'number' => $atts['limit'],
        'offset' => ($paged - 1) * $atts['limit'],
        'meta_key' => 'category_order',
        'orderby' => 'meta_value_num',
        'order' => 'ASC'
    );

    // Get subcategories (B2BKing handles visibility)
    $subcategories = get_terms($args);

    // Check if cart models category is empty
    if (empty($subcategories)) {
        // Show helpful error message for admins
        if (current_user_can('manage_options')) {
            return '<div class="cart-models-error" style="border: 2px solid #f0ad4e; padding: 20px; margin: 20px 0; background: #fff;">'
                . '<h3 style="color: #f0ad4e; margin-top: 0;">⚠️ No Cart Models Found</h3>'
                . '<p><strong>The "Cart Models" category has no subcategories or products visible to your current user group.</strong></p>'
                . '<p>This could mean:</p>'
                . '<ul>'
                . '<li>No cart model subcategories have been created yet</li>'
                . '<li>The subcategories exist but are hidden from your customer group in B2BKing</li>'
                . '<li>The subcategories exist but have no products assigned</li>'
                . '</ul>'
                . '<p>To fix this:</p>'
                . '<ol>'
                . '<li>Go to <strong>Products → Categories</strong></li>'
                . '<li>Create subcategories under <strong>"Cart Models"</strong> (e.g., "Canadian Tire Carts", "Costco Carts")</li>'
                . '<li>For each subcategory, set <strong>B2BKing Visibility</strong> to show it to the appropriate customer groups</li>'
                . '<li>Assign products to these subcategories</li>'
                . '<li>Save changes</li>'
                . '</ol>'
                . '<p><em>Note: Only administrators can see this message. Regular users see nothing.</em></p>'
                . '</div>';
        }
        // Non-admins see nothing (fail silently)
        return '';
    }

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
            padding: 20px;
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
