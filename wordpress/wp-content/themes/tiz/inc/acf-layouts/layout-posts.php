<?php
// Layout: Posts Section (latest or manual selection)
if (!function_exists('acf_add_local_field_group')) {
    return [];
}

return [
    'key' => 'layout_posts',
    'name' => 'posts',
    'label' => 'Articles',
    'display' => 'block',
    'sub_fields' => [
        [
            'key' => 'field_posts_bg_color',
            'label' => 'Couleur de fond',
            'name' => 'background_color',
            'type' => 'color_picker',
            'default_value' => '',
            'wrapper' => ['width' => '50'],
        ],
        [
            'key' => 'field_posts_heading',
            'label' => 'Titre (H2)',
            'name' => 'heading',
            'type' => 'text',
            'instructions' => 'Texte simple uniquement.  Vous pouvez ajouter une mise en forme &lt;span&gt;texte mis en forme&lt;/span&gt; dans le contenu final si nécessaire.',
            'required' => 1,
        ],
        [
            'key' => 'field_posts_description',
            'label' => 'Description',
            'name' => 'description',
            'type' => 'textarea',
            'rows' => 3,
            'new_lines' => '',
        ],
        [
            'key' => 'field_posts_primary_btn',
            'label' => 'Bouton principal',
            'name' => 'primary_button',
            'type' => 'group',
            'layout' => 'block',
            'wrapper' => ['width' => '50'],
            'sub_fields' => [
                [
                    'key' => 'field_posts_primary_btn_label',
                    'label' => 'Libellé',
                    'name' => 'label',
                    'type' => 'text',
                    'wrapper' => ['width' => '50'],
                ],
                [
                    'key' => 'field_posts_primary_btn_link',
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
        [
            'key' => 'field_posts_secondary_btn',
            'label' => 'Bouton secondaire',
            'name' => 'secondary_button',
            'type' => 'group',
            'layout' => 'block',
            'wrapper' => ['width' => '50'],
            'sub_fields' => [
                [
                    'key' => 'field_posts_secondary_btn_label',
                    'label' => 'Libellé',
                    'name' => 'label',
                    'type' => 'text',
                    'wrapper' => ['width' => '50'],
                ],
                [
                    'key' => 'field_posts_secondary_btn_link',
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
        [
            'key' => 'field_posts_display_latest',
            'label' => 'Afficher les 3 derniers articles',
            'name' => 'display_latest',
            'type' => 'true_false',
            'ui' => 1,
            'default_value' => 1,
            'instructions' => "Sinon, sélectionnez manuellement les articles ci-dessous.",
            'wrapper' => ['width' => '50'],
        ],
        [
            'key' => 'field_posts_latest_category',
            'label' => 'Catégorie (optionnel)',
            'name' => 'latest_category',
            'type' => 'taxonomy',
            'taxonomy' => 'category',
            'field_type' => 'select',
            'return_format' => 'id',
            'add_term' => 0,
            'allow_null' => 1,
            'instructions' => 'Filtre les 3 derniers articles par cette catégorie.',
            'conditional_logic' => [
                [
                    [
                        'field' => 'field_posts_display_latest',
                        'operator' => '==',
                        'value' => '1',
                    ],
                ],
            ],
            'wrapper' => ['width' => '50'],
        ],
        // Relationship stays full width
        [
            'key' => 'field_posts_selected',
            'label' => 'Articles sélectionnés',
            'name' => 'selected_posts',
            'type' => 'relationship',
            'post_type' => ['post'],
            'filters' => ['search', 'taxonomy'],
            'elements' => ['featured_image'],
            'return_format' => 'id',
            'min' => 0,
            'max' => 0,
            'instructions' => 'Sélectionnez et ordonnez les articles à afficher.',
            'conditional_logic' => [
                [
                    [
                        'field' => 'field_posts_display_latest',
                        'operator' => '!=',
                        'value' => '1',
                    ],
                ],
            ],
        ],
    ],
];
