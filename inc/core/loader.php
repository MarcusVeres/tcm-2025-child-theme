<?php
/**
 * Module Loader
 * Handles loading of all theme modules
 *
 * @package TCM_2025_Child
 * @since 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Load all theme modules
 */
function tcm_child_load_modules() {
    $modules_dir = TCM_CHILD_DIR . '/inc/modules';

    // Define available modules
    // Each module should have an init.php file
    $modules = array(
        'login-redirect',
        'woocommerce',
        'breadcrumbs',
        'cart-models',
    );

    // Load each module
    foreach ($modules as $module) {
        $module_file = $modules_dir . '/' . $module . '/init.php';

        if (file_exists($module_file)) {
            require_once $module_file;
        }
    }
}

/**
 * Check if a plugin is active
 * Useful for conditional module loading
 *
 * @param string $plugin Plugin file path relative to plugins directory
 * @return bool
 */
function tcm_child_is_plugin_active($plugin) {
    if (!function_exists('is_plugin_active')) {
        include_once(ABSPATH . 'wp-admin/includes/plugin.php');
    }
    return is_plugin_active($plugin);
}
