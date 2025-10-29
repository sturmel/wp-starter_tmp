<?php
// Layout: Project Section
if (!function_exists('acf_add_local_field_group')) {
    return [];
}

return [
    'key' => 'layout_project',
    'name' => 'project',
    'label' => 'Projets',
    'display' => 'block',
    'sub_fields' => [
        [
            'key' => 'field_project_bg_color',
            'label' => 'Couleur de fond',
            'name' => 'background_color',
            'type' => 'color_picker',
            'default_value' => '',
            'wrapper' => ['width' => '50'],
        ],
        // Header fields
        [
            'key' => 'field_project_heading',
            'label' => 'Titre (H2)',
            'name' => 'heading',
            'type' => 'text',
            'instructions' => 'Texte simple uniquement.  Vous pouvez ajouter une mise en forme &lt;span&gt;texte mis en forme&lt;/span&gt; dans le contenu final si nécessaire.',
            'required' => 1,
            'wrapper' => ['width' => '50'],
        ],
        [
            'key' => 'field_project_description',
            'label' => 'Description',
            'name' => 'description',
            'type' => 'textarea',
            'rows' => 3,
            'new_lines' => '',
            'wrapper' => ['width' => '50'],
        ],
        [
            'key' => 'field_project_cards_bg_color',
            'label' => 'Couleur de fond des cartes',
            'name' => 'cards_bg_color',
            'type' => 'color_picker',
            'instructions' => 'Couleur appliquée à l’arrière-plan de chaque carte projet (optionnel).',
            'default_value' => '',
            'wrapper' => ['width' => '50'],
        ],
        // CTA primary
        [
            'key' => 'field_project_primary_btn',
            'label' => 'Bouton principal',
            'name' => 'primary_button',
            'type' => 'group',
            'layout' => 'block',
            'wrapper' => ['width' => '50'],
            'sub_fields' => [
                [
                    'key' => 'field_project_primary_btn_label',
                    'label' => 'Libellé',
                    'name' => 'label',
                    'type' => 'text',
                    'wrapper' => ['width' => '50'],
                ],
                [
                    'key' => 'field_project_primary_btn_link',
                    'label' => 'Page interne',
                    'name' => 'link',
                    'type' => 'page_link',
                    'post_type' => [],
                    'allow_null' => 1,
                    'allow_archives' => 0,
                    'multiple' => 0,
                    'wrapper' => ['width' => '50'],
                ],
            ],
        ],
        // CTA secondary
        [
            'key' => 'field_project_secondary_btn',
            'label' => 'Bouton secondaire',
            'name' => 'secondary_button',
            'type' => 'group',
            'layout' => 'block',
            'wrapper' => ['width' => '50'],
            'sub_fields' => [
                [
                    'key' => 'field_project_secondary_btn_label',
                    'label' => 'Libellé',
                    'name' => 'label',
                    'type' => 'text',
                    'wrapper' => ['width' => '50'],
                ],
                [
                    'key' => 'field_project_secondary_btn_link',
                    'label' => 'Page interne',
                    'name' => 'link',
                    'type' => 'page_link',
                    'post_type' => [],
                    'allow_null' => 1,
                    'allow_archives' => 0,
                    'multiple' => 0,
                    'wrapper' => ['width' => '50'],
                ],
            ],
        ],
        // Listing options
        [
            'key' => 'field_project_display_latest',
            'label' => 'Afficher les 3 derniers projets',
            'name' => 'display_latest',
            'type' => 'true_false',
            'ui' => 1,
            'default_value' => 1,
            'wrapper' => ['width' => '50'],
        ],
        // Relationship stays full width
        [
            'key' => 'field_project_selected_projects',
            'label' => 'Projets sélectionnés',
            'name' => 'selected_projects',
            'type' => 'relationship',
            'post_type' => ['project'],
            'filters' => ['search'],
            'elements' => ['featured_image'],
            'return_format' => 'id',
            'min' => 0,
            'max' => 0,
            'instructions' => "Si l'option ci-dessus n'est pas cochée, sélectionnez ici les projets à afficher et ordonnez-les.",
            'conditional_logic' => [
                [
                    [
                        'field' => 'field_project_display_latest',
                        'operator' => '!=',
                        'value' => '1',
                    ],
                ],
            ],
        ],
    ],
];
