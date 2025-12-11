<?php
/**
 * TCM 2025 Child Theme - Main Functions File
 *
 * @package TCM_2025_Child
 * @since 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define constants
define('TCM_CHILD_VERSION', '1.0.0');
define('TCM_CHILD_DIR', get_stylesheet_directory());
define('TCM_CHILD_URL', get_stylesheet_directory_uri());

/**
 * Core theme setup - always loaded
 */
require_once TCM_CHILD_DIR . '/inc/core/theme-setup.php';
require_once TCM_CHILD_DIR . '/inc/core/loader.php';

/**
 * Initialize modules
 */
tcm_child_load_modules();
