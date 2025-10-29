<?php
// Security related functions and hooks

/**
 * Disable XML-RPC interface.
 */
add_filter('xmlrpc_enabled', '__return_false');

/**
 * Remove WordPress version number from various places.
 */
function remove_wordpress_version()
{
    return '';
}
add_filter('the_generator', 'remove_wordpress_version'); // General generator tag

// Also remove from RSS feeds (duplicate of the above, but good to be explicit)
add_filter('the_generator', function () {
    return '';
});

/**
 * Remove version numbers from script and style URLs.
 */
function remove_version_from_scripts_styles($src, $handle)
{
    // Keep version for theme assets to allow cache-busting (filemtime in assets.php)
    $theme_uri = trailingslashit(get_stylesheet_directory_uri());
    if (strpos($src, $theme_uri) === 0) {
        return $src; // do not strip ?ver for theme CSS/JS
    }

    if (strpos($src, 'ver=') !== false) {
        $src = remove_query_arg('ver', $src);
    }
    return $src;
}
add_filter('style_loader_src', 'remove_version_from_scripts_styles', 9999, 2);
add_filter('script_loader_src', 'remove_version_from_scripts_styles', 9999, 2);

/**
 * Disable File Editor in WordPress admin.
 * It's best to also set this in wp-config.php if possible.
 */
if (!defined('DISALLOW_FILE_EDIT')) {
    define('DISALLOW_FILE_EDIT', true);
}

/**
 * Attempt to remove X-Powered-By header.
 * Effectiveness depends on server configuration.
 */
if (function_exists('header_remove')) {
    @header_remove('X-Powered-By'); // Suppress errors if it fails
}

/**
 * Remove unnecessary header links for security.
 */
remove_action('wp_head', 'wp_generator'); // WordPress generator tag (covered by remove_wordpress_version but good to have)
remove_action('wp_head', 'wlwmanifest_link'); // Windows Live Writer Manifest Link
remove_action('wp_head', 'rsd_link'); // Really Simple Discovery Link
// remove_action('wp_head', 'wp_shortlink_wp_head'); // Shortlink (consider if you use it)
// remove_action('wp_head', 'wp_oembed_add_discovery_links', 10); // oEmbed discovery links (if not using oEmbeds)
// remove_action('template_redirect', 'rest_output_link_header', 11, 0); // REST API link from HTTP headers (if REST API is not public)

/**
 * Disable comments and pings site-wide
 */
function tiz_disable_comments_support()
{
    // Disable support in all post types
    foreach (get_post_types() as $post_type) {
        if (post_type_supports($post_type, 'comments')) {
            remove_post_type_support($post_type, 'comments');
        }
        if (post_type_supports($post_type, 'trackbacks')) {
            remove_post_type_support($post_type, 'trackbacks');
        }
    }
}
add_action('admin_init', 'tiz_disable_comments_support');
add_action('init', 'tiz_disable_comments_support');

// Close comments/pings on the front-end and empty existing arrays
add_filter('comments_open', '__return_false', 20, 2);
add_filter('pings_open', '__return_false', 20, 2);
add_filter('comments_array', '__return_empty_array', 10, 2);

// Remove comments from admin menu and discussion settings when disabled
function tiz_hide_comments_admin_menu()
{
    remove_menu_page('edit-comments.php');
    remove_submenu_page('options-general.php', 'options-discussion.php');
}
add_action('admin_menu', 'tiz_hide_comments_admin_menu');

// Redirect legacy comments pages if accessed directly
function tiz_block_comments_admin_pages()
{
    global $pagenow;
    if (in_array($pagenow, ['comment.php', 'edit-comments.php'], true)) {
        wp_safe_redirect(admin_url());
        exit;
    }
}
add_action('admin_init', 'tiz_block_comments_admin_pages');

// Remove comments from admin bar
add_action('admin_bar_menu', function ($wp_admin_bar) {
    $wp_admin_bar->remove_node('comments');
}, 999);

/**
 * Extra hardening
 */
// Remove X-Pingback header and disable pingback XML-RPC method
add_filter('wp_headers', function ($headers) {
    unset($headers['X-Pingback']);
    return $headers;
});
add_filter('xmlrpc_methods', function ($methods) {
    unset($methods['pingback.ping']);
    return $methods;
});

// Remove emojis (perf + minor privacy)
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('admin_print_scripts', 'print_emoji_detection_script');
remove_action('wp_print_styles', 'print_emoji_styles');
remove_action('admin_print_styles', 'print_emoji_styles');
add_filter('emoji_svg_url', '__return_false');

// Block author enumeration (?author=1)
add_action('template_redirect', function () {
    if (!is_admin() && isset($_GET['author'])) {
        wp_safe_redirect(home_url('/'), 301);
        exit;
    }
});

// Hide author archives (return 404 on is_author)
add_action('template_redirect', function () {
    if (is_author()) {
        global $wp_query;
        $wp_query->set_404();
        status_header(404);
        nocache_headers();
        // Load 404 template if available
        $template = get_query_template('404');
        if ($template) {
            include $template;
        } else {
            // Fallback: simple 404 text
            echo '404 Not Found';
        }
        exit;
    }
});

// Restrict REST API users endpoint for non-logged-in requests
add_filter('rest_endpoints', function ($endpoints) {
    if (!is_user_logged_in()) {
        unset($endpoints['/wp/v2/users']);
        unset($endpoints['/wp/v2/users/(?P<id>[\\d]+)']);
    }
    return $endpoints;
});

// Generic login error message
add_filter('login_errors', function () {
    return __('Identifiants invalides.', 'tiz');
});

// Basic security headers (prefer server config; added here as a fallback)
add_action('send_headers', function () {
    header('X-Frame-Options: SAMEORIGIN');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    if (is_ssl()) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
    }
});
