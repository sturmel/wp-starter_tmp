<?php
// Layout: Content + Image Section
if (!function_exists('acf_add_local_field_group')) {
    return [];
}

return [
    'key' => 'layout_content_image',
    'name' => 'content_image',
    'label' => 'Contenu + Image',
    'display' => 'block',
    'sub_fields' => [
        [
            'key' => 'field_content_image_bg_color',
            'label' => 'Couleur de fond',
            'name' => 'background_color',
            'type' => 'color_picker',
            'default_value' => '',
            'wrapper' => ['width' => '50'],
        ],
        [
            'key' => 'field_content_image_layout',
            'label' => 'Disposition',
            'name' => 'layout_type',
            'type' => 'select',
            'choices' => [
                'image_right' => 'Image à droite, texte à gauche',
                'image_left' => 'Image à gauche, texte à droite',
                'no_image' => 'Pas d\'image (texte seul)',
            ],
            'default_value' => 'image_right',
            'required' => 1,
            'wrapper' => ['width' => '50'],
        ],
        [
            'key' => 'field_content_image_heading',
            'label' => 'Titre (H2)',
            'name' => 'heading',
            'type' => 'text',
            'instructions' => 'Texte simple uniquement. Vous pouvez ajouter une mise en forme &lt;span&gt;texte mis en forme&lt;/span&gt; dans le contenu final si nécessaire.',
            'required' => 1,
            'wrapper' => ['width' => '50'],
        ],
        [
            'key' => 'field_content_image_description',
            'label' => 'Description principale',
            'name' => 'description',
            'type' => 'wysiwyg',
            'tabs' => 'all',
            'toolbar' => 'full',
            'media_upload' => 1,
            'delay' => 1,
            'required' => 1,
            'wrapper' => ['width' => '50'],
        ],
        [
            'key' => 'field_content_image_extended_content',
            'label' => 'Contenu étendu',
            'name' => 'extended_content',
            'type' => 'wysiwyg',
            'tabs' => 'all',
            'toolbar' => 'full',
            'media_upload' => 1,
            'delay' => 1,
            'instructions' => 'Contenu qui s\'affichera après avoir cliqué sur "En savoir plus"',
            'wrapper' => ['width' => '50'],
        ],
        [
            'key' => 'field_content_image_image',
            'label' => 'Image',
            'name' => 'image',
            'type' => 'image',
            'return_format' => 'array',
            'preview_size' => 'medium',
            'conditional_logic' => [
                [
                    [
                        'field' => 'field_content_image_layout',
                        'operator' => '!=',
                        'value' => 'no_image',
                    ],
                ],
            ],
            'wrapper' => ['width' => '50'],
        ],
        [
            'key' => 'field_content_image_read_more_text',
            'label' => 'Texte du bouton "En savoir plus"',
            'name' => 'read_more_text',
            'type' => 'text',
            'default_value' => 'En savoir plus',
            'conditional_logic' => [
                [
                    [
                        'field' => 'field_content_image_extended_content',
                        'operator' => '!=',
                        'value' => '',
                    ],
                ],
            ],
            'wrapper' => ['width' => '50'],
        ],
        [
            'key' => 'field_content_image_read_less_text',
            'label' => 'Texte du bouton "Masquer"',
            'name' => 'read_less_text',
            'type' => 'text',
            'default_value' => 'Masquer',
            'conditional_logic' => [
                [
                    [
                        'field' => 'field_content_image_extended_content',
                        'operator' => '!=',
                        'value' => '',
                    ],
                ],
            ],
            'wrapper' => ['width' => '50'],
        ],
        [
            'key' => 'field_content_image_primary_btn',
            'label' => 'Bouton principal',
            'name' => 'primary_button',
            'type' => 'group',
            'layout' => 'block',
            'wrapper' => ['width' => '50'],
            'sub_fields' => [
                [
                    'key' => 'field_content_image_primary_btn_label',
                    'label' => 'Libellé',
                    'name' => 'label',
                    'type' => 'text',
                    'wrapper' => ['width' => '50'],
                ],
                [
                    'key' => 'field_content_image_primary_btn_link',
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
            'key' => 'field_content_image_secondary_btn',
            'label' => 'Bouton secondaire',
            'name' => 'secondary_button',
            'type' => 'group',
            'layout' => 'block',
            'wrapper' => ['width' => '50'],
            'sub_fields' => [
                [
                    'key' => 'field_content_image_secondary_btn_label',
                    'label' => 'Libellé',
                    'name' => 'label',
                    'type' => 'text',
                    'wrapper' => ['width' => '50'],
                ],
                [
                    'key' => 'field_content_image_secondary_btn_link',
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
    ],
];
