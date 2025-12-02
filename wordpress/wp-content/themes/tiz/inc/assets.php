<?php
// Styles & scripts enqueues

add_action('wp_head', 'enqueue_google_fonts', 1);
function enqueue_google_fonts()
{
?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap">
    </noscript>
<?php
}

add_action('wp_enqueue_scripts', 'theme_enqueue_styles_scripts');
function theme_enqueue_styles_scripts()
{
    // Parent theme stylesheet with cache-busting based on filemtime
    $parent_style_path = get_template_directory() . '/style.css';
    $parent_style_ver = file_exists($parent_style_path) ? filemtime($parent_style_path) : null;
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css', [], $parent_style_ver);
    wp_enqueue_style(
        'child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array('parent-style'),
        wp_get_theme()->get('Version')
    );

    // Récupère l'environnement : constante WP_ENV (wp-config.php) ou variable d'env (Docker)
    // Par défaut : production si non défini
    $wordpress_env = defined('WP_ENV') ? constant('WP_ENV') : (getenv('WORDPRESS_ENV') ?: 'production');

    // Utilise dev_build en développement, dist en production
    if ($wordpress_env === 'development') {
        $css_file_name_relative = '/dev_build/styles.css';
        $js_file_name_relative = '/dev_build/scripts.js';
    } else {
        $css_file_name_relative = '/dist/styles.min.css';
        $js_file_name_relative = '/dist/scripts.min.js';
    }


    $theme_css_path = get_stylesheet_directory() . $css_file_name_relative;
    if (file_exists($theme_css_path)) {
        wp_enqueue_style(
            'tailwind-style',
            get_stylesheet_directory_uri() . $css_file_name_relative,
            array('child-style'),
            filemtime($theme_css_path)
        );
    }

    $theme_js_path = get_stylesheet_directory() . $js_file_name_relative;
    if (file_exists($theme_js_path)) {
        wp_enqueue_script(
            'child-scripts',
            get_stylesheet_directory_uri() . $js_file_name_relative,
            array(),
            filemtime($theme_js_path),
            array('strategy' => 'defer')
        );
    }
}
