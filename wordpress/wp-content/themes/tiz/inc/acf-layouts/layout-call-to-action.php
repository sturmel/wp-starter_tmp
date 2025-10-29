<?php
// Layout: Call To Action Section
if (!function_exists('acf_add_local_field_group')) {
    return [];
}

return [
    'key' => 'layout_call_to_action',
    'name' => 'call_to_action',
    'label' => 'Call to action',
    'display' => 'block',
    'sub_fields' => [
        [
            'key' => 'field_cta_color_start',
            'label' => 'Dégradé - Couleur de début (gauche)',
            'name' => 'color_start',
            'type' => 'color_picker',
            'wrapper' => ['width' => '50'],
        ],
        [
            'key' => 'field_cta_color_end',
            'label' => 'Dégradé - Couleur de fin (droite)',
            'name' => 'color_end',
            'type' => 'color_picker',
            'wrapper' => ['width' => '50'],
        ],
        [
            'key' => 'field_cta_bg_split_top',
            'label' => 'Fond pleine largeur – Couleur haute (0% - 50%)',
            'name' => 'background_split_top',
            'type' => 'color_picker',
            'instructions' => 'Optionnel. Utilisée avec la couleur basse pour un fond vertical bi-couleur derrière la carte.',
            'wrapper' => ['width' => '50'],
        ],
        [
            'key' => 'field_cta_bg_split_bottom',
            'label' => 'Fond pleine largeur – Couleur basse (50% - 100%)',
            'name' => 'background_split_bottom',
            'type' => 'color_picker',
            'instructions' => 'Optionnel. Affichée seulement si la couleur haute est définie.',
            'wrapper' => ['width' => '50'],
        ],
        [
            'key' => 'field_cta_heading',
            'label' => 'Titre (H2)',
            'name' => 'heading',
            'type' => 'text',
            'required' => 1,
            'instructions' => 'Texte du call to action. Vous pouvez ajouter une mise en forme &lt;span&gt;texte mis en forme&lt;/span&gt; dans le contenu final si nécessaire.'
        ],
        [
            'key' => 'field_cta_primary_btn',
            'label' => 'Bouton principal',
            'name' => 'primary_button',
            'type' => 'group',
            'layout' => 'block',
            'wrapper' => ['width' => '50'],
            'sub_fields' => [
                [
                    'key' => 'field_cta_primary_btn_link_type',
                    'label' => 'Type de lien',
                    'name' => 'link_type',
                    'type' => 'button_group',
                    'choices' => [
                        'internal' => 'Page du site',
                        'phone' => 'Numéro de téléphone',
                    ],
                    'default_value' => 'internal',
                    'layout' => 'horizontal',
                    'wrapper' => ['width' => '50'],
                ],
                [
                    'key' => 'field_cta_primary_btn_label',
                    'label' => 'Libellé',
                    'name' => 'label',
                    'type' => 'text',
                    'required' => 1,
                    'wrapper' => ['width' => '50'],
                ],
                [
                    'key' => 'field_cta_primary_btn_link',
                    'label' => 'Page interne',
                    'name' => 'link',
                    'type' => 'page_link',
                    'post_type' => [],
                    'allow_null' => 1,
                    'allow_archives' => 0,
                    'multiple' => 0,
                    'wrapper' => ['width' => '50'],
                    'conditional_logic' => [
                        [
                            ['field' => 'field_cta_primary_btn_link_type', 'operator' => '==', 'value' => 'internal'],
                        ],
                    ],
                ],
                [
                    'key' => 'field_cta_primary_btn_phone',
                    'label' => 'Numéro de téléphone',
                    'name' => 'phone_number',
                    'type' => 'text',
                    'wrapper' => ['width' => '50'],
                    'instructions' => 'Format libre. Les espaces, points ou tirets seront automatiquement retirés pour le lien tel:.',
                    'conditional_logic' => [
                        [
                            ['field' => 'field_cta_primary_btn_link_type', 'operator' => '==', 'value' => 'phone'],
                        ],
                    ],
                ],
            ],
        ],
        [
            'key' => 'field_cta_secondary_btn',
            'label' => 'Bouton secondaire',
            'name' => 'secondary_button',
            'type' => 'group',
            'layout' => 'block',
            'wrapper' => ['width' => '50'],
            'sub_fields' => [
                [
                    'key' => 'field_cta_secondary_btn_label',
                    'label' => 'Libellé',
                    'name' => 'label',
                    'type' => 'text',
                    'wrapper' => ['width' => '50'],
                ],
                [
                    'key' => 'field_cta_secondary_btn_link',
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
