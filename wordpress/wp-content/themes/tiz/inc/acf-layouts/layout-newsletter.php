<?php
// Layout: Newsletter (gradient background + heading/subheading + CF7 form)
if (!function_exists('acf_add_local_field_group')) {
    return [];
}

return [
    'key' => 'layout_newsletter',
    'name' => 'newsletter',
    'label' => 'Newsletter',
    'display' => 'block',
    'sub_fields' => [
        [
            'key' => 'field_news_color_start',
            'label' => 'Dégradé - Couleur de début (gauche)',
            'name' => 'color_start',
            'type' => 'color_picker',
            'default_value' => '#0a84eb',
            'wrapper' => ['width' => '50'],
        ],
        [
            'key' => 'field_news_color_end',
            'label' => 'Dégradé - Couleur de fin (droite)',
            'name' => 'color_end',
            'type' => 'color_picker',
            'default_value' => '#0599ff',
            'wrapper' => ['width' => '50'],
        ],
        [
            'key' => 'field_news_bg_split_top',
            'label' => 'Fond pleine largeur – Couleur haute (0% - 50%)',
            'name' => 'background_split_top',
            'type' => 'color_picker',
            'instructions' => 'Optionnel. Utilisée avec la couleur basse pour un fond pleine largeur bi-couleur (vertical).',
            'wrapper' => ['width' => '50'],
        ],
        [
            'key' => 'field_news_bg_split_bottom',
            'label' => 'Fond pleine largeur – Couleur basse (50% - 100%)',
            'name' => 'background_split_bottom',
            'type' => 'color_picker',
            'instructions' => 'Optionnel. Affiché seulement si la couleur haute est définie.',
            'wrapper' => ['width' => '50'],
        ],
        [
            'key' => 'field_news_heading',
            'label' => 'Titre',
            'name' => 'heading',
            'instructions' => 'Texte simple uniquement.  Vous pouvez ajouter une mise en forme &lt;span&gt;texte mis en forme&lt;/span&gt; dans le contenu final si nécessaire.',
            'type' => 'text',
            'required' => 1,
            'wrapper' => ['width' => '50'],
        ],
        [
            'key' => 'field_news_subheading',
            'label' => 'Sous-titre',
            'name' => 'subheading',
            'type' => 'textarea',
            'rows' => 3,
            'new_lines' => '',
            'wrapper' => ['width' => '50'],
        ],
        [
            'key' => 'field_news_cf7',
            'label' => 'Formulaire Contact Form 7',
            'name' => 'contact_form',
            'type' => 'post_object',
            'post_type' => ['wpcf7_contact_form'],
            'return_format' => 'id',
            'ui' => 1,
            'required' => 1,
            'wrapper' => ['width' => '50'],
        ],
    ],
];
