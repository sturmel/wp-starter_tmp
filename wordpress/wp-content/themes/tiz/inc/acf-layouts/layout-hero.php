<?php
// Layout Hero simplifié (suppression mode de titre avancé)
if (!function_exists('acf_add_local_field_group')) {
    return [];
}

return [
    'key' => 'layout_hero',
    'name' => 'hero',
    'label' => 'Hero',
    'display' => 'block',
    'sub_fields' => [
        [
            'key' => 'field_hero_bg_color',
            'label' => 'Couleur de fond',
            'name' => 'background_color',
            'type' => 'color_picker',
            'default_value' => '',
            'wrapper' => ['width' => '50'],
        ],
        [
            'key' => 'field_hero_variant',
            'label' => 'Disposition',
            'name' => 'variant',
            'type' => 'button_group',
            'choices' => [
                'full_background' => 'Fond plein',
                'image_right' => 'Image droite',
                'no_image' => 'Sans image',
            ],
            'default_value' => 'full_background',
            'layout' => 'horizontal',
            'return_format' => 'value',
            'wrapper' => ['width' => '50'],
        ],
        [
            'key' => 'field_hero_bg_image',
            'label' => 'Image (fond ou côté)',
            'name' => 'image',
            'type' => 'image',
            'return_format' => 'array',
            'preview_size' => 'medium',
            'conditional_logic' => [
                [['field' => 'field_hero_variant', 'operator' => '!=', 'value' => 'no_image']]
            ],
            'wrapper' => ['width' => '50'],
        ],
        [
            'key' => 'field_hero_bg_image_mobile',
            'label' => 'Image mobile (<1024px)',
            'name' => 'image_mobile',
            'type' => 'image',
            'return_format' => 'array',
            'preview_size' => 'medium',
            'instructions' => 'Optionnel. Utilisée uniquement pour la variante "Fond plein" sur les écrans < 1024px. Si vide, l\'image principale est réutilisée.',
            'conditional_logic' => [
                [
                    ['field' => 'field_hero_variant', 'operator' => '==', 'value' => 'full_background']
                ]
            ],
            'wrapper' => ['width' => '50'],
        ],
        [
            'key' => 'field_hero_overlay',
            'label' => 'Overlay (0-90%)',
            'name' => 'overlay',
            'type' => 'number',
            'min' => 0,
            'max' => 90,
            'step' => 10,
            'default_value' => 0,
            'conditional_logic' => [
                [['field' => 'field_hero_variant', 'operator' => '==', 'value' => 'full_background']]
            ],
            'wrapper' => ['width' => '50'],
        ],
        [
            'key' => 'field_hero_title',
            'label' => 'Titre (H1)',
            'name' => 'title',
            'type' => 'text',
            'required' => 1,
            'instructions' => 'Texte simple uniquement.  Vous pouvez ajouter une mise en forme &lt;span&gt;texte mis en forme&lt;/span&gt; dans le contenu final si nécessaire.',
        ],
        [
            'key' => 'field_hero_subtitle',
            'label' => 'Sous-titre (H2)',
            'name' => 'subtitle',
            'type' => 'textarea',
            'rows' => 3,
            'wrapper' => ['width' => '50'],
        ],
        [
            'key' => 'field_hero_description',
            'label' => 'Description',
            'name' => 'description',
            'type' => 'textarea',
            'rows' => 4,
            'new_lines' => 'br',
            'wrapper' => ['width' => '50'],
        ],
        [
            'key' => 'field_hero_alignment',
            'label' => 'Alignement texte',
            'name' => 'alignment',
            'type' => 'button_group',
            'choices' => [
                'left' => 'Gauche',
                'center' => 'Centre',
            ],
            'default_value' => 'left',
            'layout' => 'horizontal',
            'wrapper' => ['width' => '50'],
        ],
        [
            'key' => 'field_hero_height',
            'label' => 'Hauteur',
            'name' => 'height',
            'type' => 'select',
            'choices' => [
                'screen' => 'Pleine hauteur (100vh)',
                'large' => 'Grande (~70vh)',
                'medium' => 'Moyenne (~50vh)',
            ],
            'default_value' => 'screen',
            'wrapper' => ['width' => '50'],
        ],
        [
            'key' => 'field_hero_primary_button',
            'label' => 'Bouton principal (interne)',
            'name' => 'primary_button',
            'type' => 'group',
            'layout' => 'block',
            'wrapper' => ['width' => '50'],
            'sub_fields' => [
                [
                    'key' => 'field_hero_primary_btn_link_type',
                    'label' => 'Type de lien',
                    'name' => 'link_type',
                    'type' => 'button_group',
                    'choices' => [
                        'internal' => 'Page du site',
                        'external' => 'Lien externe',
                    ],
                    'default_value' => 'internal',
                    'layout' => 'horizontal',
                    'wrapper' => ['width' => '50'],
                ],
                [
                    'key' => 'field_hero_primary_btn_label',
                    'label' => 'Label',
                    'name' => 'label',
                    'type' => 'text',
                    'wrapper' => ['width' => '50'],
                ],
                [
                    'key' => 'field_hero_primary_btn_link',
                    'label' => 'Lien interne',
                    'name' => 'link',
                    'type' => 'page_link',
                    'post_type' => [],
                    'allow_null' => 1,
                    'wrapper' => ['width' => '50'],
                    'conditional_logic' => [
                        [
                            ['field' => 'field_hero_primary_btn_link_type', 'operator' => '==', 'value' => 'internal'],
                        ],
                    ],
                ],
                [
                    'key' => 'field_hero_primary_btn_external_url',
                    'label' => 'URL externe',
                    'name' => 'external_url',
                    'type' => 'url',
                    'wrapper' => ['width' => '50'],
                    'conditional_logic' => [
                        [
                            ['field' => 'field_hero_primary_btn_link_type', 'operator' => '==', 'value' => 'external'],
                        ],
                    ],
                    'instructions' => 'Inclure https://. Le lien sera ouvert dans un nouvel onglet avec nofollow.',
                ],
            ],
        ],
        [
            'key' => 'field_hero_secondary_button',
            'label' => 'Bouton secondaire (interne)',
            'name' => 'secondary_button',
            'type' => 'group',
            'layout' => 'block',
            'wrapper' => ['width' => '50'],
            'sub_fields' => [
                [
                    'key' => 'field_hero_secondary_btn_label',
                    'label' => 'Label',
                    'name' => 'label',
                    'type' => 'text',
                    'wrapper' => ['width' => '50'],
                ],
                [
                    'key' => 'field_hero_secondary_btn_link',
                    'label' => 'Lien interne',
                    'name' => 'link',
                    'type' => 'page_link',
                    'post_type' => [],
                    'allow_null' => 1,
                    'wrapper' => ['width' => '50'],
                ],
            ],
        ],
    ],
];
