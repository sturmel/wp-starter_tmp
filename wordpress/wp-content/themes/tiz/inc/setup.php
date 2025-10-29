<?php
// Theme setup and core settings

// Image sizes
add_action('after_setup_theme', function () {
    add_image_size('admin-logo-100h', 9999, 100, false);
});

// Disable classic editor on pages (use ACF sections only)
add_action('init', function () {
    // Désactive l'éditeur de contenu sur les pages pour n'afficher que les champs ACF
    remove_post_type_support('page', 'editor');
}, 100);

// Menus
function custom_navigation()
{
    register_nav_menu('main_menu', __('Menu principal'));
    register_nav_menu('footer_menu_legal', __('Menu footer Legal'));
    register_nav_menu('footer_menu_social', __('Menu footer Social'));
}
add_action('init', 'custom_navigation');

// Disable Gutenberg where applicable
add_filter('use_block_editor_for_post', '__return_false');
add_filter('use_widgets_block_editor', '__return_false');
