<?php

/**
 * Custom Post Type: Testimonials (Témoignages)
 * - Registers CPT `testimonial`
 * - Registers ACF field group with rating (1..5), testimonial text, full name, company
 */

// Register CPT
add_action('init', function () {
    $labels = [
        'name' => __('Témoignages', 'tiz'),
        'singular_name' => __('Témoignage', 'tiz'),
        'menu_name' => __('Témoignages', 'tiz'),
        'name_admin_bar' => __('Témoignage', 'tiz'),
        'add_new' => __('Ajouter', 'tiz'),
        'add_new_item' => __('Ajouter un témoignage', 'tiz'),
        'new_item' => __('Nouveau témoignage', 'tiz'),
        'edit_item' => __('Modifier le témoignage', 'tiz'),
        'view_item' => __('Voir le témoignage', 'tiz'),
        'all_items' => __('Tous les témoignages', 'tiz'),
        'search_items' => __('Rechercher des témoignages', 'tiz'),
        'not_found' => __('Aucun témoignage trouvé', 'tiz'),
        'not_found_in_trash' => __('Aucun témoignage dans la corbeille', 'tiz'),
    ];

    $args = [
        'labels' => $labels,
        'public' => true,
        'show_in_menu' => true,
        'menu_icon' => 'dashicons-testimonial',
        // Remove 'editor' so only title and ACF fields are used
        'supports' => ['title', 'thumbnail', 'revisions'],
        'has_archive' => true,
        'show_in_rest' => false,
        'rewrite' => [
            'slug' => 'temoignages',
            'with_front' => false,
        ],
    ];

    register_post_type('testimonial', $args);
});

// ACF Fields (variables English, labels French)
add_action('acf/init', function () {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group([
        'key' => 'group_testimonial_fields',
        'title' => 'Témoignage — Champs',
        'fields' => [
            [
                'key' => 'field_testimonial_rating',
                'label' => 'Note (1 à 5)',
                'name' => 'rating',
                'type' => 'number',
                'min' => 1,
                'max' => 5,
                'step' => 1,
                'required' => 1,
                'default_value' => 5,
            ],
            [
                'key' => 'field_testimonial_text',
                'label' => 'Témoignage',
                'name' => 'testimonial',
                'type' => 'textarea',
                'rows' => 4,
                'new_lines' => '',
                'required' => 1,
            ],
            [
                'key' => 'field_testimonial_full_name',
                'label' => 'Nom et prénom',
                'name' => 'full_name',
                'type' => 'text',
                'required' => 1,
            ],
            [
                'key' => 'field_testimonial_company',
                'label' => 'Entreprise',
                'name' => 'company',
                'type' => 'text',
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'testimonial',
                ],
            ],
        ],
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'active' => true,
    ]);
});
