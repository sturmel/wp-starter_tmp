<?php
// Layout: Scroll inifini (infinite scroll)
if (!function_exists('acf_add_local_field_group')) {
    return [];
}

return [
    'key' => 'layout_infinite_scroll',
    'name' => 'logos_infinite_scroll',
    'label' => 'Défilement infini',
    'display' => 'block',
    'sub_fields' => [
        [
            'key' => 'field_logos_heading',
            'label' => 'Titre (H2)',
            'name' => 'heading',
            'type' => 'text',
            'instructions' => 'Texte simple uniquement.  Vous pouvez ajouter une mise en forme &lt;span&gt;texte mis en forme&lt;/span&gt; dans le contenu final si nécessaire.',
        ],
        [
            'key' => 'field_logos_primary_btn',
            'label' => 'Bouton principal',
            'name' => 'primary_button',
            'type' => 'group',
            'layout' => 'block',
            'wrapper' => ['width' => '50'],
            'sub_fields' => [
                [
                    'key' => 'field_logos_primary_btn_label',
                    'label' => 'Libellé',
                    'name' => 'label',
                    'type' => 'text',
                    'wrapper' => ['width' => '50'],
                ],
                [
                    'key' => 'field_logos_primary_btn_link',
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
            'key' => 'field_logos_secondary_btn',
            'label' => 'Bouton secondaire',
            'name' => 'secondary_button',
            'type' => 'group',
            'layout' => 'block',
            'wrapper' => ['width' => '50'],
            'sub_fields' => [
                [
                    'key' => 'field_logos_secondary_btn_label',
                    'label' => 'Libellé',
                    'name' => 'label',
                    'type' => 'text',
                    'wrapper' => ['width' => '50'],
                ],
                [
                    'key' => 'field_logos_secondary_btn_link',
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
            'key' => 'field_logos_bg_color',
            'label' => 'Couleur de fond',
            'name' => 'background_color',
            'type' => 'color_picker',
            'default_value' => '#f9fafb',
            'wrapper' => ['width' => '50'],
        ],
        [
            'key' => 'field_logos_speed',
            'label' => 'Vitesse (secondes par boucle)',
            'name' => 'speed',
            'type' => 'number',
            'min' => 5,
            'max' => 60,
            'step' => 1,
            'default_value' => 20,
            'instructions' => 'Duration for one full logo loop (in seconds).',
            'wrapper' => ['width' => '50'],
        ],
        [
            'key' => 'field_logos_items',
            'label' => 'Logos',
            'name' => 'logos',
            'type' => 'repeater',
            'layout' => 'table',
            'button_label' => 'Ajouter un logo',
            'min' => 2,
            'sub_fields' => [
                [
                    'key' => 'field_logos_item_image',
                    'label' => 'Logo',
                    'name' => 'image',
                    'type' => 'image',
                    'return_format' => 'array',
                    'preview_size' => 'admin-logo-100h',
                    'required' => 1,
                    'instructions' => 'Preview limited to 100px height in the admin (original size is preserved).',
                ],
                [
                    'key' => 'field_logos_item_url',
                    'label' => 'URL',
                    'name' => 'url',
                    'type' => 'url',
                    'instructions' => 'Associated link (added with rel="nofollow").',
                ],
                [
                    'key' => 'field_logos_item_newtab',
                    'label' => 'Ouvrir dans un nouvel onglet',
                    'name' => 'new_tab',
                    'type' => 'true_false',
                    'ui' => 1,
                    'default_value' => 1,
                ],
            ],
        ]
    ],
];
