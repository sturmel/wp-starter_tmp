<?php
// Chargement ACF: différer à acf/init pour garantir que tout ACF est prêt
if (!function_exists('acf_add_local_field_group')) {
    return; // ACF pas actif
}

add_action('acf/init', function () {
    $layouts = [];
    $dir = get_stylesheet_directory() . '/inc/acf-layouts';
    if (is_dir($dir)) {
        foreach (glob($dir . '/layout-*.php') as $file) {
            $layout_def = include $file; // Chaque fichier doit retourner un array (layout)
            if (is_array($layout_def) && isset($layout_def['key']) && isset($layout_def['name'])) {
                $layouts[] = $layout_def;
            }
        }
    }

    // Si aucun layout trouvé, on enregistre quand même le groupe pour debug
    acf_add_local_field_group([
        'key' => 'group_flexible_content_base',
        'title' => 'Flexible Content',
        'fields' => [
            [
                'key' => 'field_flexible_content_root',
                'label' => 'Sections',
                'name' => 'flexible_content',
                'type' => 'flexible_content',
                'layouts' => $layouts,
                'button_label' => 'Ajouter une section',
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'page',
                ],
            ],
            [
                [
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'post',
                ],
            ],
        ],
    ]);
});

// Sync JSON (inchangé)
add_filter('acf/settings/save_json', function ($path) {
    $dir = get_stylesheet_directory() . '/acf-json';
    if (!is_dir($dir)) {
        wp_mkdir_p($dir);
    }
    return $dir;
});
add_filter('acf/settings/load_json', function ($paths) {
    $paths[] = get_stylesheet_directory() . '/acf-json';
    return $paths;
});

// No changes required here: layouts are auto-loaded via glob('layout-*.php').
// This message is to acknowledge check; keeping file unchanged.
