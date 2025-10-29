<?php
require_once(get_stylesheet_directory() . '/inc/security.php');
require_once(get_stylesheet_directory() . '/inc/performance.php');
require_once(get_stylesheet_directory() . '/inc/acf-layouts.php');
require_once(get_stylesheet_directory() . '/inc/custom-post-types/testimonials.php');
require_once(get_stylesheet_directory() . '/inc/custom-post-types/projects.php');
require_once(get_stylesheet_directory() . '/inc/custom-post-types/cf7-webhooks.php');
require_once(get_stylesheet_directory() . '/inc/acf-contact-forms.php');
// Duplication util (posts/pages/projects)
require_once(get_stylesheet_directory() . '/inc/duplicate.php');

// Core setup, menus, editor settings
require_once(get_stylesheet_directory() . '/inc/setup.php');

// Admin tweaks
require_once(get_stylesheet_directory() . '/inc/admin.php');

// Assets (styles/scripts/fonts)
require_once(get_stylesheet_directory() . '/inc/assets.php');

// ACF: post header fields (title + subtitle)
require_once(get_stylesheet_directory() . '/inc/acf-post-fields.php');

// ACF: taxonomy fields (e.g., category color for post tags)
require_once(get_stylesheet_directory() . '/inc/acf-taxonomies.php');

// Timber context additions
add_filter('timber/context', function ($context) {
    $context['main_menu'] = Timber\Timber::get_menu('main_menu');
    $context['footer_menu_legal'] = Timber\Timber::get_menu('footer_menu_legal');
    $context['footer_menu_social'] = Timber\Timber::get_menu('footer_menu_social');
    $upload_dir_info = wp_upload_dir();
    $context['uploads_base_url'] = $upload_dir_info['baseurl'];

    return $context;
});

// Register custom query vars used by the Project Archive layout
add_filter('query_vars', function ($vars) {
    $vars[] = 'q';        // terme de recherche
    $vars[] = 'pg';       // numéro de page
    $vars[] = 'expertise'; // filtre par slug d'expertise
    return $vars;
});

// SEO spécifique pour le layout "Projets – Archive"
require_once(get_stylesheet_directory() . '/inc/seo-project-archive.php');

// Override des archives de catégories (Articles) via page associée
require_once(get_stylesheet_directory() . '/inc/category-archive-override.php');

// Mail: utiliser MSMTP_FROM du .env
add_action('init', function () {
    $env_from = getenv('MSMTP_FROM');
    if ($env_from && filter_var(trim($env_from), FILTER_VALIDATE_EMAIL)) {
        $env_from = trim($env_from);
        add_filter('wp_mail_from', function ($from) use ($env_from) {
            if (empty($from) || $from === 'server@wordpress.org') {
                return $env_from;
            }
            return $from;
        });
    }
});
