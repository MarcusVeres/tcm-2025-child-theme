<?php
/**
 * Core Theme Setup
 * Handles basic theme initialization and asset loading
 *
 * @package TCM_2025_Child
 * @since 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enqueue parent and child theme styles
 */
function tcm_child_enqueue_styles() {
    // Enqueue parent theme stylesheet
    wp_enqueue_style(
        'parent-style',
        get_template_directory_uri() . '/style.css',
        array(),
        wp_get_theme()->parent()->get('Version')
    );

    // Enqueue child theme stylesheet
    wp_enqueue_style(
        'child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array('parent-style'),
        TCM_CHILD_VERSION
    );
}
add_action('wp_enqueue_scripts', 'tcm_child_enqueue_styles');

/**
 * Add theme support for WooCommerce and other features
 */
function tcm_child_theme_support() {
    // WooCommerce support
    add_theme_support('woocommerce');

    // Add other theme supports as needed
    // add_theme_support('post-thumbnails');
    // add_theme_support('title-tag');
}
add_action('after_setup_theme', 'tcm_child_theme_support');
