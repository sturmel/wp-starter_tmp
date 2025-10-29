<?php
// Layout: Values Section
if (!function_exists('acf_add_local_field_group')) {
    return [];
}

return [
    'key' => 'layout_values',
    'name' => 'values',
    'label' => 'Valeurs',
    'display' => 'block',
    'sub_fields' => [
        [
            'key' => 'field_values_bg_color',
            'label' => 'Couleur de fond',
            'name' => 'background_color',
            'type' => 'color_picker',
            'default_value' => '',
            'wrapper' => ['width' => '50'],
        ],
        [
            'key' => 'field_values_heading',
            'label' => 'Titre (H2)',
            'name' => 'heading',
            'type' => 'text',
            'instructions' => 'Texte simple uniquement. Vous pouvez ajouter une mise en forme &lt;span&gt;texte mis en forme&lt;/span&gt; dans le contenu final si nécessaire.',
            'required' => 1,
            'wrapper' => ['width' => '50'],
        ],
        [
            'key' => 'field_values_description',
            'label' => 'Description',
            'name' => 'description',
            'type' => 'textarea',
            'rows' => 3,
            'new_lines' => '',
            'wrapper' => ['width' => '50'],
        ],
        [
            'key' => 'field_values_cards_bg_color',
            'label' => 'Couleur de fond des cartes',
            'name' => 'cards_bg_color',
            'type' => 'color_picker',
            'instructions' => 'Couleur appliquée au fond de toutes les cartes (par défaut gris clair).',
            'wrapper' => ['width' => '50'],
        ],
        [
            'key' => 'field_values_cards',
            'label' => 'Cartes de valeurs',
            'name' => 'cards',
            'type' => 'repeater',
            'layout' => 'row',
            'button_label' => 'Ajouter une valeur',
            'min' => 1,
            'sub_fields' => [
                [
                    'key' => 'field_values_card_image',
                    'label' => 'Icône',
                    'name' => 'image',
                    'type' => 'image',
                    'return_format' => 'array',
                    'preview_size' => 'thumbnail',
                    'required' => 1,
                ],
                [
                    'key' => 'field_values_card_title',
                    'label' => 'Titre',
                    'name' => 'title',
                    'type' => 'text',
                    'required' => 1,
                ],
                [
                    'key' => 'field_values_card_text',
                    'label' => 'Texte descriptif',
                    'name' => 'text',
                    'type' => 'textarea',
                    'rows' => 4,
                    'required' => 1,
                ],
                [
                    'key' => 'field_values_card_color',
                    'label' => "Couleur du titre et de l'accent",
                    'name' => 'custom_color',
                    'type' => 'color_picker',
                    'default_value' => '#0aa2ff',
                    'instructions' => "Couleur utilisée pour le titre et les barres d'accent au hover",
                ],
            ],
        ],
    ],
];
