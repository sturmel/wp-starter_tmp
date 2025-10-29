<?php
// Layout: Posts – Archive (page-level)
if (!function_exists('acf_add_local_field_group')) {
    return [];
}

return [
    'key' => 'layout_post_archive',
    'name' => 'post-archive',
    'label' => 'Articles – Archive',
    'display' => 'block',
    'sub_fields' => [
        [
            'key' => 'field_postarch_heading',
            'label' => 'Titre (H2)',
            'name' => 'heading',
            'type' => 'text',
            'instructions' => 'Texte simple uniquement.  Vous pouvez ajouter une mise en forme &lt;span&gt;texte mis en forme&lt;/span&gt; dans le contenu final si nécessaire.',
            'required' => 0,
            'wrapper' => ['width' => '50'],
        ],
        [
            'key' => 'field_postarch_description',
            'label' => 'Description',
            'name' => 'description',
            'type' => 'textarea',
            'rows' => 3,
            'new_lines' => '',
            'wrapper' => ['width' => '50'],
        ],
        [
            'key' => 'field_postarch_posts_per_page',
            'label' => 'Nombre d’articles par page',
            'name' => 'posts_per_page',
            'type' => 'number',
            'min' => 1,
            'max' => 48,
            'default_value' => 9,
            'wrapper' => ['width' => '50'],
        ],
        [
            'key' => 'field_postarch_categories',
            'label' => 'Catégories (filtre admin)',
            'name' => 'categories',
            'type' => 'taxonomy',
            'taxonomy' => 'category',
            'field_type' => 'multi_select',
            'return_format' => 'id',
            'add_term' => 0,
            'allow_null' => 1,
            'instructions' => 'Filtre les articles affichés (aucun filtre en front).',
            'wrapper' => ['width' => '50'],
        ],
        // Tri et ordre forcés: date DESC. Pas de recherche ni filtres front.
    ],
];
