<?php
// ACF fields for taxonomies (e.g., Category color for Post tags)

if (function_exists('add_action')) {
    add_action('acf/init', function () {
        if (!function_exists('acf_add_local_field_group')) {
            return;
        }

        acf_add_local_field_group([
            'key' => 'group_tax_category_color',
            'title' => 'Catégorie – Couleur (tags)',
            'fields' => [
                [
                    'key' => 'field_tax_category_color',
                    'label' => 'Couleur du tag',
                    'name' => 'color',
                    'type' => 'color_picker',
                    'instructions' => 'Couleur utilisée pour les tags des cartes d’articles.',
                    'required' => 0,
                    'wrapper' => ['width' => '', 'class' => '', 'id' => ''],
                ],
                [
                    'key' => 'field_tax_category_override_page',
                    'label' => 'Page associée (override)',
                    'name' => 'override_page',
                    'type' => 'post_object',
                    'post_type' => ['page'],
                    'return_format' => 'id',
                    'ui' => 1,
                    'required' => 0,
                    'instructions' => 'Si défini, cette page remplace l’archive par défaut de la catégorie.',
                    'wrapper' => ['width' => '', 'class' => '', 'id' => ''],
                ],
            ],
            'location' => [
                [
                    [
                        'param' => 'taxonomy',
                        'operator' => '==',
                        'value' => 'category',
                    ],
                ],
            ],
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => '',
            'active' => true,
            'description' => '',
        ]);
    });
}
