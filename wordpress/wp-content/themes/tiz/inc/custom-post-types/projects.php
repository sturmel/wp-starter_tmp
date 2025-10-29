<?php
// CPT Projets + Taxonomie Expertises + champs ACF

// Enregistrement du CPT et de la taxonomie
add_action('init', function () {
    // CPT: project
    $labels = array(
        'name' => __('Projets', 'tiz'),
        'singular_name' => __('Projet', 'tiz'),
        'menu_name' => __('Projets', 'tiz'),
        'name_admin_bar' => __('Projet', 'tiz'),
        'add_new' => __('Ajouter', 'tiz'),
        'add_new_item' => __('Ajouter un projet', 'tiz'),
        'new_item' => __('Nouveau projet', 'tiz'),
        'edit_item' => __('Modifier le projet', 'tiz'),
        'view_item' => __('Voir le projet', 'tiz'),
        'all_items' => __('Tous les projets', 'tiz'),
        'search_items' => __('Rechercher des projets', 'tiz'),
        'not_found' => __('Aucun projet trouvé', 'tiz'),
        'not_found_in_trash' => __('Aucun projet dans la corbeille', 'tiz'),
    );

    register_post_type('project', array(
        'labels' => $labels,
        'public' => true,
        'has_archive' => false,
        'menu_position' => 21,
        'menu_icon' => 'dashicons-portfolio',
        'supports' => array('title', 'thumbnail', 'excerpt'),
        'show_in_rest' => true,
        'rewrite' => array('slug' => 'portfolio', 'with_front' => false),
    ));

    // Taxonomy: expertise (non hiérarchique, comme des tags)
    $tax_labels = array(
        'name' => __('Expertises', 'tiz'),
        'singular_name' => __('Expertise', 'tiz'),
        'search_items' => __('Rechercher des expertises', 'tiz'),
        'all_items' => __('Toutes les expertises', 'tiz'),
        'edit_item' => __('Modifier l’\u00a0expertise', 'tiz'),
        'update_item' => __('Mettre à jour l’\u00a0expertise', 'tiz'),
        'add_new_item' => __('Ajouter une expertise', 'tiz'),
        'new_item_name' => __('Nom de la nouvelle expertise', 'tiz'),
        'menu_name' => __('Expertises', 'tiz'),
    );

    register_taxonomy('expertise', array('project'), array(
        'labels' => $tax_labels,
        'public' => true,
        'hierarchical' => false,
        'show_admin_column' => true,
        'show_in_rest' => true,
        'rewrite' => array('slug' => 'expertises'),
    ));
});

// Flush des permaliens à l'activation du thème pour ce CPT/Taxo
add_action('after_switch_theme', function () {
    flush_rewrite_rules();
});

// Champs ACF: pour le CPT et pour la taxonomie
add_action('acf/init', function () {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    // Groupe de champs pour les Projets
    acf_add_local_field_group(array(
        'key' => 'group_project_fields',
        'title' => __('Projet - Champs', 'tiz'),
        'fields' => array(
            array(
                'key' => 'field_project_clients',
                'label' => __('Clients', 'tiz'),
                'name' => 'clients',
                'type' => 'text',
                'instructions' => __('Nom du ou des clients', 'tiz'),
                'required' => 0,
            ),
            array(
                'key' => 'field_project_custom_title',
                'label' => __('Titre', 'tiz'),
                'name' => 'custom_title',
                'type' => 'text',
                'instructions' => __('Titre spécifique au projet (affichage)', 'tiz'),
                'required' => 0,
            ),
            // Nouveau: champ de description (longue, non affichée automatiquement)
            array(
                'key' => 'field_project_description',
                'label' => __('Description', 'tiz'),
                'name' => 'description',
                'type' => 'textarea',
                'instructions' => __('Description longue (non affichée automatiquement sur la page projet).', 'tiz'),
                'required' => 0,
                'rows' => 5,
                'new_lines' => 'wpautop',
            ),
            // Nouveau: sélection et ordre des hashtags (expertises)
            array(
                'key' => 'field_project_expertises_order',
                'label' => __('Hashtags (Expertises)', 'tiz'),
                'name' => 'expertises',
                'type' => 'repeater',
                'instructions' => __('Sélectionnez et ordonnez les hashtags. Chaque expertise ne peut apparaître qu\'une seule fois.', 'tiz'),
                'min' => 0,
                'layout' => 'table',
                'button_label' => __('Ajouter un hashtag', 'tiz'),
                'sub_fields' => array(
                    array(
                        'key' => 'field_project_expertises_order_item',
                        'label' => __('Expertise', 'tiz'),
                        'name' => 'expertise',
                        'type' => 'taxonomy',
                        'taxonomy' => 'expertise',
                        'field_type' => 'select',
                        'add_term' => 0,
                        'save_terms' => 0,
                        'load_terms' => 0,
                        'return_format' => 'id',
                        'multiple' => 0,
                    ),
                ),
            ),
            array(
                'key' => 'field_project_image',
                'label' => __('Image vignette', 'tiz'),
                'name' => 'image',
                'type' => 'image',
                'instructions' => __('Image spécifique au projet (obligatoire)', 'tiz'),
                'required' => 1,
                'return_format' => 'array',
                'preview_size' => 'medium',
                'library' => 'all',
            ),
            // Nouveau: image de bandeau pour la page projet (utilisée dans le hero)
            array(
                'key' => 'field_project_banner_image',
                'label' => __('Image bandeau', 'tiz'),
                'name' => 'image_banner',
                'type' => 'image',
                'instructions' => __('Image bandeau affichée en haut de la page projet (obligatoire).', 'tiz'),
                'required' => 1,
                'return_format' => 'array',
                'preview_size' => 'medium',
                'library' => 'all',
            ),
            array(
                'key' => 'field_project_link_url',
                'label' => __('Lien (URL)', 'tiz'),
                'name' => 'link_url',
                'type' => 'url',
                'instructions' => __('Optionnel', 'tiz'),
                'required' => 0,
                'allow_null' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'project',
                ),
            ),
        ),
        'position' => 'acf_after_title',
        'style' => 'default',
        'active' => true,
    ));

    // Groupe de champs pour la taxonomie Expertise (métadonnées des tags)
    acf_add_local_field_group(array(
        'key' => 'group_expertise_term_meta',
        'title' => __('Expertise - Métadonnées', 'tiz'),
        'fields' => array(
            array(
                'key' => 'field_expertise_color',
                'label' => __('Couleur', 'tiz'),
                'name' => 'color',
                'type' => 'color_picker',
            ),
            array(
                'key' => 'field_expertise_page',
                'label' => __('Page associée', 'tiz'),
                'name' => 'page',
                'type' => 'page_link',
                'allow_null' => 1,
                'post_type' => array('page'),
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'taxonomy',
                    'operator' => '==',
                    'value' => 'expertise',
                ),
            ),
        ),
        'position' => 'normal',
        'style' => 'default',
        'active' => true,
    ));

    // Groupe Flexible Content pour Projets (sans Hero et sans Archive)
    $layouts_project = [];
    $dir = get_stylesheet_directory() . '/inc/acf-layouts';
    if (is_dir($dir)) {
        foreach (glob($dir . '/layout-*.php') as $file) {
            $layout_def = include $file;
            if (is_array($layout_def) && isset($layout_def['key']) && isset($layout_def['name'])) {
                // Exclure Hero et l'archive projets du FC des singles
                if ($layout_def['name'] !== 'hero' && $layout_def['name'] !== 'project-archive') {
                    $layouts_project[] = $layout_def;
                }
            }
        }
    }

    acf_add_local_field_group([
        'key' => 'group_flexible_content_project',
        'title' => 'Flexible Content (Projet)',
        'fields' => [
            [
                'key' => 'field_flexible_content_project_root',
                'label' => 'Sections',
                'name' => 'flexible_content',
                'type' => 'flexible_content',
                'layouts' => $layouts_project,
                'button_label' => 'Ajouter une section',
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'project',
                ],
            ],
        ],
        'position' => 'acf_after_title',
        'style' => 'default',
        'active' => true,
    ]);
});

// Validation ACF: empêcher les doublons dans le champ hashtags
add_filter('acf/validate_value/key=field_project_expertises_order_item', function ($valid, $value, $field, $input) {
    if ($valid !== true) {
        return $valid;
    }
    if (!isset($_POST['acf']['field_project_expertises_order']) || !is_array($_POST['acf']['field_project_expertises_order'])) {
        return $valid;
    }
    $rows = $_POST['acf']['field_project_expertises_order'];
    $ids = [];
    foreach ($rows as $row) {
        if (isset($row['field_project_expertises_order_item']) && $row['field_project_expertises_order_item']) {
            $ids[] = (int) $row['field_project_expertises_order_item'];
        }
    }
    if (empty($ids)) {
        return $valid;
    }
    $counts = array_count_values($ids);
    foreach ($counts as $count) {
        if ($count > 1) {
            return __('Chaque hashtag ne peut être sélectionné qu\'une seule fois.', 'tiz');
        }
    }
    return $valid;
}, 10, 4);

// Sync des hashtags (repeater) vers la taxonomie 'expertise'
add_action('acf/save_post', function ($post_id) {
    if (get_post_type($post_id) !== 'project') {
        return;
    }
    // Ne synchronise que si le champ a été soumis (pas d'écrasement involontaire)
    if (!isset($_POST['acf']['field_project_expertises_order'])) {
        return;
    }
    $rows = isset($_POST['acf']['field_project_expertises_order']) ? (array) $_POST['acf']['field_project_expertises_order'] : [];
    $ids = [];
    foreach ($rows as $row) {
        if (!empty($row['field_project_expertises_order_item'])) {
            $ids[] = (int) $row['field_project_expertises_order_item'];
        }
    }
    $ids = array_values(array_unique(array_filter($ids)));
    wp_set_object_terms($post_id, $ids, 'expertise', false);
}, 20);

// SEO: rendre les archives des termes 'expertise' non indexables
add_filter('wp_robots', function (array $robots) {
    if (is_tax('expertise')) {
        // Noindex, mais on laisse le crawl des liens (follow)
        $robots['noindex'] = true;
        $robots['follow'] = true;
        // Par sécurité, on supprime une éventuelle directive index
        unset($robots['index']);
    }
    return $robots;
});
