<?php
/**
 * WooCommerce Module
 * Customizations for WooCommerce including account menu and category ordering
 *
 * @package TCM_2025_Child
 * @since 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Customize WooCommerce account menu items
 *
 * @param array $items Menu items
 * @return array Modified menu items
 */
function tcm_child_customize_account_menu($items) {
    // Remove unwanted tabs
    $remove_items = array('conversations', 'offers', 'subaccounts', 'downloads');
    foreach ($remove_items as $item) {
        if (isset($items[$item])) {
            unset($items[$item]);
        }
    }

    // Rename tabs
    if (isset($items['purchase-lists'])) {
        $items['purchase-lists'] = 'My Shopping Lists';
    }

    if (isset($items['bulkorder'])) {
        $items['bulkorder'] = 'Create Shopping List';
    }

    return $items;
}
// Use priority 999 to ensure this runs after all other plugins
add_filter('woocommerce_account_menu_items', 'tcm_child_customize_account_menu', 999, 1);

/**
 * Add custom ordering field to product category admin
 */
function tcm_child_add_category_order_field() {
    ?>
    <div class="form-field">
        <label for="category_order"><?php _e('Category Order', 'tcm-2025-child'); ?></label>
        <input type="number" name="category_order" id="category_order" value="0">
        <p class="description"><?php _e('Enter a number to set the custom order (lower numbers display first)', 'tcm-2025-child'); ?></p>
    </div>
    <?php
}
add_action('product_cat_add_form_fields', 'tcm_child_add_category_order_field');

/**
 * Edit category order field for existing categories
 *
 * @param WP_Term $term Term object
 */
function tcm_child_edit_category_order_field($term) {
    $category_order = get_term_meta($term->term_id, 'category_order', true);
    if (!$category_order) {
        $category_order = 0;
    }
    ?>
    <tr class="form-field">
        <th scope="row"><label for="category_order"><?php _e('Category Order', 'tcm-2025-child'); ?></label></th>
        <td>
            <input type="number" name="category_order" id="category_order" value="<?php echo esc_attr($category_order); ?>">
            <p class="description"><?php _e('Enter a number to set the custom order (lower numbers display first)', 'tcm-2025-child'); ?></p>
        </td>
    </tr>
    <?php
}
add_action('product_cat_edit_form_fields', 'tcm_child_edit_category_order_field');

/**
 * Save the custom order field
 *
 * @param int $term_id Term ID
 */
function tcm_child_save_category_order_field($term_id) {
    if (isset($_POST['category_order'])) {
        update_term_meta($term_id, 'category_order', absint($_POST['category_order']));
    }
}
add_action('created_product_cat', 'tcm_child_save_category_order_field');
add_action('edited_product_cat', 'tcm_child_save_category_order_field');
