<?php

/**
 * Extra controls for Contact Form 7 forms.
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

add_action('acf/init', function () {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group([
        'key' => 'tiz_cf7_additional_settings',
        'title' => __('Options du formulaire', 'tiz'),
        'fields' => [
            [
                'key' => 'tiz_cf7_form_html_id',
                'label' => __('ID HTML du formulaire', 'tiz'),
                'name' => 'form_html_id',
                'type' => 'text',
                'instructions' => __('Optionnel. Caractères autorisés : lettres, chiffres, tirets (-), underscores (_) et deux-points (:). Exemple : contact-principal', 'tiz'),
                'wrapper' => ['width' => '100'],
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'wpcf7_contact_form',
                ],
            ],
        ],
        'position' => 'side',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'active' => true,
    ]);
});

add_action('admin_enqueue_scripts', function ($hook) {
    if (!function_exists('acf_enqueue_scripts')) {
        return;
    }

    if ($hook === 'toplevel_page_wpcf7') {
        acf_enqueue_scripts();
    }
});

add_filter('wpcf7_editor_panels', function ($panels) {
    if (!isset($panels['form-panel'])) {
        return $panels;
    }

    $panels['tiz-acf'] = [
        'title' => __('Options supplémentaires', 'tiz'),
        'callback' => 'tiz_cf7_render_acf_panel',
    ];

    return $panels;
});

if (!function_exists('tiz_cf7_render_acf_panel')) {
    function tiz_cf7_render_acf_panel($contact_form)
    {
        if (!function_exists('acf_get_fields') || !function_exists('acf_form_data') || !function_exists('acf_render_fields')) {
            echo '<p>' . esc_html__('ACF est requis pour cette section.', 'tiz') . '</p>';
            return;
        }

        $post_id = 0;
        if (is_object($contact_form) && method_exists($contact_form, 'id')) {
            $post_id = (int) $contact_form->id();
        } elseif (isset($_GET['post'])) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            $post_id = (int) $_GET['post'];
        }

        if (!$post_id) {
            echo '<p>' . esc_html__('Impossible de déterminer l’identifiant du formulaire.', 'tiz') . '</p>';
            return;
        }

        $fields = acf_get_fields('tiz_cf7_additional_settings');
        if (!$fields) {
            echo '<p>' . esc_html__('Aucun champ configuré pour ce panneau.', 'tiz') . '</p>';
            return;
        }

        acf_form_data([
            'post_id' => $post_id,
            'screen' => 'post',
        ]);

        echo '<div class="acf-fields -sidebar">';
        acf_render_fields($fields, $post_id, 'div', 'field');
        echo '</div>';
    }
}

if (!function_exists('tiz_cf7_get_form_html_id')) {
    /**
     * Retrieve the requested HTML id for a CF7 form (if any) and normalise it.
     */
    function tiz_cf7_get_form_html_id($contact_form): string
    {
        if (!function_exists('get_field') || !is_object($contact_form) || !method_exists($contact_form, 'id')) {
            return '';
        }

        $post_id = (int) $contact_form->id();
        if (!$post_id) {
            return '';
        }

        $raw_id = (string) get_field('form_html_id', $post_id);
        if ($raw_id === '') {
            return '';
        }

        $sanitised = preg_replace('/[^A-Za-z0-9_\-:]/', '', $raw_id);
        return $sanitised ?: '';
    }
}

add_filter('wpcf7_form_id_attr', function ($id_attr) {
    if ($id_attr !== '') {
        return $id_attr;
    }

    if (!class_exists('WPCF7_ContactForm')) {
        return $id_attr;
    }

    $contact_form = WPCF7_ContactForm::get_current();
    if (!$contact_form) {
        return $id_attr;
    }

    $html_id = tiz_cf7_get_form_html_id($contact_form);
    if ($html_id === '') {
        return $id_attr;
    }

    return $html_id;
});
