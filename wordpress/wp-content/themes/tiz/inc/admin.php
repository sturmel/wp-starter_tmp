<?php
// Admin-only tweaks

// CSS admin: limiter la hauteur d’aperçu uniquement sur le champ logo du repeater
add_action('admin_head', function () {
    echo '<style>
    /* Contrainte forte sur l\'aperçu du champ image des logos */
    .acf-field[data-key="field_logos_item_image"] .image-wrap { max-height: 100px !important; width: auto !important; }
    .acf-field[data-key="field_logos_item_image"] .image-wrap img { max-height: 100px !important; height: auto !important; width: auto !important; object-fit: contain !important; }
    /* Cas de liste en tableau dans repeater */
    .acf-field[data-key="field_logos_item_image"] .acf-image-uploader img { max-height: 100px !important; height: auto !important; width: auto !important; object-fit: contain !important; }
    </style>';
});

function hide_editor()
{
    if (!is_admin()) {
        return;
    }
    $screen = get_current_screen();
    if ($screen && $screen->id === 'page') {
        $postId = $_GET['post'] ?? $_POST['post_ID'] ?? null;
        if ($postId) {
            $templateFile = get_post_meta($postId, '_wp_page_template', true);
            $targetTemplates = array();
            if (in_array($templateFile, $targetTemplates)) {
                remove_post_type_support('page', 'editor');
            }
        }
    }
}
add_action('load-page.php', 'hide_editor');
