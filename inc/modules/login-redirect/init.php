<?php
/**
 * Login Redirect Module
 * Redirects customers to home page after login (administrators go to wp-admin)
 *
 * @package TCM_2025_Child
 * @since 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Redirect customers to home page after login
 * Administrators will still go to wp-admin
 *
 * @param string $redirect Default redirect URL
 * @param WP_User $user User object
 * @return string Modified redirect URL
 */
function tcm_child_redirect_after_login($redirect, $user) {
    // Get the user's role
    $user_role = $user->roles[0];

    // If user is an administrator, use default redirect (wp-admin)
    if ($user_role === 'administrator') {
        return $redirect;
    }

    // For customers and other roles, redirect to home page
    return home_url();
}
add_filter('woocommerce_login_redirect', 'tcm_child_redirect_after_login', 10, 2);
