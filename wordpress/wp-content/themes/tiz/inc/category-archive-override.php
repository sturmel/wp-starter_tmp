<?php
// Redirect Category archives to an assigned override Page if configured via ACF
// Field key: override_page (post_object -> Page ID) on taxonomy "category"

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

add_action('template_redirect', function () {
    if (is_admin() || is_feed()) {
        return;
    }

    if (!function_exists('get_field')) {
        return; // ACF not loaded
    }

    if (is_category()) {
        $term = get_queried_object();
        if ($term && isset($term->term_id)) {
            $page_id = get_field('override_page', 'category_' . $term->term_id);
            if ($page_id && get_post_status($page_id) === 'publish') {
                $target = get_permalink($page_id);
                if ($target) {
                    wp_redirect($target, 301);
                    exit;
                }
            }
        }
    }
});
