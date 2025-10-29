<?php
// Layout: Testimonial List Section
if (!function_exists('acf_add_local_field_group')) {
    return [];
}

return [
    'key' => 'layout_testimonial_list',
    'name' => 'testimonial_list',
    'label' => 'Témoignages (liste)',
    'display' => 'block',
    'sub_fields' => [
        [
            'key' => 'field_testimonial_list_bg_color',
            'label' => 'Couleur de fond',
            'name' => 'background_color',
            'type' => 'color_picker',
            // no default to keep theme background unless chosen
            'wrapper' => ['width' => '50'],
        ],
        [
            'key' => 'field_testimonial_card_bg_color',
            'label' => 'Couleur des cartes',
            'name' => 'card_background_color',
            'type' => 'color_picker',
            'instructions' => 'Unique pour toutes les cartes de témoignages dans cette section.',
            'wrapper' => ['width' => '50'],
        ],
        [
            'key' => 'field_testimonial_list_heading',
            'label' => 'Titre de section',
            'name' => 'heading',
            'type' => 'text',
            'instructions' => 'Texte simple uniquement.  Vous pouvez ajouter une mise en forme &lt;span&gt;texte mis en forme&lt;/span&gt; dans le contenu final si nécessaire.',
            'required' => 1,
            'wrapper' => ['width' => '50'],
        ],
        [
            'key' => 'field_testimonial_list_show_google_logo',
            'label' => 'Afficher le logo Google',
            'name' => 'show_google_logo',
            'type' => 'true_false',
            'ui' => 1,
            'default_value' => 1,
            'instructions' => 'Activé: affiche le logo Google et 5 étoiles. Désactivé: affiche uniquement 5 étoiles.',
            'wrapper' => ['width' => '50'],
        ],
        [
            'key' => 'field_testimonial_list_posts',
            'label' => 'Sélection de témoignages',
            'name' => 'testimonials',
            'type' => 'repeater',
            'layout' => 'row',
            'button_label' => 'Ajouter un témoignage',
            'min' => 1,
            'max' => 12,
            'instructions' => 'Choisir les témoignages à afficher et, si besoin, associer une page interne.',
            'sub_fields' => [
                [
                    'key' => 'field_testimonial_list_post',
                    'label' => 'Témoignage',
                    'name' => 'testimonial',
                    'type' => 'post_object',
                    'post_type' => ['testimonial'],
                    'return_format' => 'object',
                    'required' => 1,
                    'ui' => 1,
                    'wrapper' => ['width' => '50'],
                ],
                [
                    'key' => 'field_testimonial_list_link',
                    'label' => 'Page interne (optionnel)',
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
