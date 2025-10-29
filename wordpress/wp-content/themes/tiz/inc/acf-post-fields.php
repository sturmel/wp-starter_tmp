<?php
// ACF field group: Post Header (Title + Subtitle)
if (!function_exists('acf_add_local_field_group')) {
    return; // ACF not active
}

add_action('acf/init', function () {
    acf_add_local_field_group([
        'key' => 'group_post_header_fields',
        'title' => "En-têtes de l’article",
        'fields' => [
            [
                'key' => 'field_post_custom_title',
                'label' => 'Titre (H1) personnalisé',
                'name' => 'custom_title',
                'type' => 'text',
                'instructions' => 'Optionnel. S’il est vide, le titre WordPress sera utilisé.',
                'required' => 0,
                'wrapper' => ['width' => '', 'class' => '', 'id' => ''],
            ],
            [
                'key' => 'field_post_subtitle',
                'label' => 'Sous-titre',
                'name' => 'subtitle',
                'type' => 'text',
                'instructions' => 'Optionnel. S’affiche sous le titre.',
                'required' => 0,
                'wrapper' => ['width' => '', 'class' => '', 'id' => ''],
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'post',
                ],
            ],
        ],
        'position' => 'acf_after_title', // Above the content editor
        'style' => 'seamless',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'menu_order' => 0,
        'active' => true,
        'show_in_rest' => 0,
    ]);
});
