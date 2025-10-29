<?php
// Layout: Project Archive (page-level queryable archive)
if (!function_exists('acf_add_local_field_group')) {
    return [];
}

return [
    'key' => 'layout_project_archive',
    'name' => 'project-archive',
    'label' => 'Projets – Archive',
    'display' => 'block',
    'sub_fields' => [
        [
            'key' => 'field_projarch_admin_note',
            'label' => 'Conseils d’utilisation',
            'name' => 'admin_note',
            'type' => 'message',
            'message' => '<p>Ce bloc affiche une liste de projets avec recherche, filtre (liste déroulante) et pagination par liens numérotés, tout en conservant le héros et le bas de page éditables.</p>
<p>SEO : lorsque ce bloc est présent sur la page, le thème ajoute automatiquement dans &lt;head&gt; :<br>
– une balise canonical (sans le paramètre de recherche),<br>
– meta robots : <em>noindex,follow</em> sur les résultats de recherche (?q=), sinon <em>index,follow</em>,<br>
– les liens rel="prev"/"next" pour la pagination.</p>
<p>Paramètres d’URL supportés : <code>?q=</code> (recherche), <code>?expertise=slug</code> (filtre), <code>?pg=2</code> (pagination).</p>',
            'new_lines' => 'wpautop',
            'esc_html' => 0,
        ],
        [
            'key' => 'field_projarch_heading',
            'label' => 'Titre (H2)',
            'name' => 'heading',
            'type' => 'text',
            'instructions' => 'Texte simple uniquement.  Vous pouvez ajouter une mise en forme &lt;span&gt;texte mis en forme&lt;/span&gt; dans le contenu final si nécessaire.',
            'required' => 0,
        ],
        [
            'key' => 'field_projarch_description',
            'label' => 'Description',
            'name' => 'description',
            'type' => 'textarea',
            'rows' => 3,
            'new_lines' => '',
            'wrapper' => ['width' => '50'],
        ],
        [
            'key' => 'field_projarch_posts_per_page',
            'label' => 'Nombre de projets par page',
            'name' => 'posts_per_page',
            'type' => 'number',
            'min' => 1,
            'max' => 48,
            'default_value' => 9,
            'wrapper' => ['width' => '50'],
        ],
        // Champs retirés: "Trier par" (toujours date), "Ordre" (toujours DESC), recherche/filtre (toujours actifs), style des filtres, pagination (toujours liens)
    ],
];
