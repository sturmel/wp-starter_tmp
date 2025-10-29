<?php

/**
 * CF7 Webhooks: map Contact Form 7 forms to outbound webhooks.
 * - Admin CPT to link a CF7 form to a webhook URL
 * - When mapped form is submitted, skip email and POST JSON payload to the webhook
 */

// Register CPT
add_action('init', function () {
    register_post_type('cf7_webhook', [
        'labels' => [
            'name' => __('Webhooks CF7', 'tiz'),
            'singular_name' => __('Webhook CF7', 'tiz'),
            'add_new' => __('Ajouter', 'tiz'),
            'add_new_item' => __('Ajouter un Webhook CF7', 'tiz'),
            'edit_item' => __('Modifier le Webhook CF7', 'tiz'),
            'new_item' => __('Nouveau Webhook CF7', 'tiz'),
            'view_item' => __('Voir le Webhook CF7', 'tiz'),
            'search_items' => __('Rechercher', 'tiz'),
            'not_found' => __('Aucun élément', 'tiz'),
            'menu_name' => __('Webhooks CF7', 'tiz'),
        ],
        'public' => false,
        'publicly_queryable' => false,
        'exclude_from_search' => true,
        'has_archive' => false,
        'rewrite' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_nav_menus' => false,
        'show_in_rest' => false,
        'query_var' => false,
        'can_export' => false,
        'menu_icon' => 'dashicons-randomize',
        'supports' => ['title'],
        'capability_type' => 'post',
        'map_meta_cap' => true,
    ]);
});

// Block any accidental front access (404)
add_action('template_redirect', function () {
    if (!is_admin() && (is_singular('cf7_webhook') || is_post_type_archive('cf7_webhook'))) {
        status_header(404);
        nocache_headers();
        include get_query_template('404');
        exit;
    }
});

// Exclude from Yoast sitemap if plugin active
add_filter('wpseo_sitemap_exclude_post_type', function ($value, $post_type) {
    if ('cf7_webhook' === $post_type) {
        return true;
    }
    return $value;
}, 10, 2);

// Exclude from Rank Math sitemap if plugin active
add_filter('rank_math/sitemap/exclude_post_type', function ($excluded) {
    if (!is_array($excluded)) {
        $excluded = [];
    }
    $excluded[] = 'cf7_webhook';
    return array_unique($excluded);
});

// Exclude from WP core sitemaps
add_filter('wp_sitemaps_post_types', function ($post_types) {
    unset($post_types['cf7_webhook']);
    return $post_types;
});

// Meta box: settings
add_action('add_meta_boxes', function () {
    add_meta_box('tiz_cf7_webhook_settings', __('Paramètres du Webhook', 'tiz'), 'tiz_cf7_webhook_meta_box', 'cf7_webhook', 'normal', 'default');
});

function tiz_cf7_webhook_meta_box($post)
{
    wp_nonce_field('tiz_cf7_webhook_save', 'tiz_cf7_webhook_nonce');

    $selected_form_id = (int) get_post_meta($post->ID, 'cf7_form_id', true);
    $webhook_url      = (string) get_post_meta($post->ID, 'webhook_url', true);

    // Fetch CF7 forms
    $forms = get_posts([
        'post_type' => 'wpcf7_contact_form',
        'numberposts' => -1,
        'post_status' => 'publish',
        'orderby' => 'title',
        'order' => 'ASC',
    ]);

    echo '<p><label for="cf7_form_id"><strong>' . esc_html__('Formulaire CF7', 'tiz') . '</strong></label></p>';
    echo '<select id="cf7_form_id" name="cf7_form_id" style="min-width: 320px;">';
    echo '<option value="">' . esc_html__('— Sélectionner —', 'tiz') . '</option>';
    foreach ($forms as $f) {
        printf('<option value="%d" %s>%s</option>', (int) $f->ID, selected($selected_form_id, $f->ID, false), esc_html($f->post_title));
    }
    echo '</select>';

    echo '<p style="margin-top:1em;"><label for="webhook_url"><strong>' . esc_html__('URL du Webhook', 'tiz') . '</strong></label></p>';
    printf('<input type="url" id="webhook_url" name="webhook_url" value="%s" style="min-width: 520px;" placeholder="https://example.com/webhook" />', esc_attr($webhook_url));

    echo '<p class="description" style="margin-top:.5em;">' . esc_html__('Lorsque ce formulaire sera soumis, aucun email ne sera envoyé. Les données seront POSTées en JSON à cette URL.', 'tiz') . '</p>';
}

add_action('save_post_cf7_webhook', function ($post_id, $post) {
    if (!isset($_POST['tiz_cf7_webhook_nonce']) || !wp_verify_nonce($_POST['tiz_cf7_webhook_nonce'], 'tiz_cf7_webhook_save')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $form_id = isset($_POST['cf7_form_id']) ? (int) $_POST['cf7_form_id'] : 0;
    $url     = isset($_POST['webhook_url']) ? esc_url_raw(trim($_POST['webhook_url'])) : '';

    update_post_meta($post_id, 'cf7_form_id', $form_id);
    update_post_meta($post_id, 'webhook_url', $url);
}, 10, 2);

// Helper: get active mapping for a given CF7 form ID
function tiz_cf7_webhook_get_mapping($form_id)
{
    $mapping = null;
    if (!$form_id) {
        return $mapping;
    }

    $posts = get_posts([
        'post_type' => 'cf7_webhook',
        'post_status' => 'publish',
        'meta_query' => [
            [
                'key' => 'cf7_form_id',
                'value' => (int) $form_id,
                'compare' => '=',
                'type' => 'NUMERIC',
            ],
        ],
        'numberposts' => 1,
        'orderby' => 'ID',
        'order' => 'DESC',
        'fields' => 'ids',
    ]);

    if (!empty($posts)) {
        $pid = (int) $posts[0];
        $url = get_post_meta($pid, 'webhook_url', true);
        if (!empty($url)) {
            $mapping = [
                'post_id' => $pid,
                'webhook_url' => $url,
            ];
        }
    }

    return $mapping;
}

// Store webhook results globally
$GLOBALS['tiz_webhook_results'] = [];

// Send webhook once per submission and skip emails
add_filter('wpcf7_skip_mail', function ($skip, $contact_form) {
    if (!is_object($contact_form) || !method_exists($contact_form, 'id')) {
        return $skip;
    }

    $form_id = (int) $contact_form->id();
    $map     = tiz_cf7_webhook_get_mapping($form_id);

    if (!$map || empty($map['webhook_url'])) {
        return $skip; // No mapping: keep default behavior
    }

    // Send webhook and get result
    $result = tiz_cf7_webhook_send_payload($contact_form, $map['webhook_url']);

    // Store result for later use
    $GLOBALS['tiz_webhook_results'][$form_id] = $result;

    // Set submission status based on webhook result
    if (class_exists('WPCF7_Submission')) {
        $submission = \WPCF7_Submission::get_instance();
        if ($submission) {
            if ($result['success']) {
                $submission->set_status('mail_sent');
                $submission->set_response(__('Votre message a été envoyé avec succès.', 'textdomain'));
            } else {
                $submission->set_status('mail_failed');
                $submission->set_response(__('Erreur lors de l\'envoi du message. Veuillez réessayer.', 'textdomain'));
            }
        }
    }

    return true; // skip CF7 emails for mapped forms
}, 10, 2);

function tiz_cf7_webhook_send_payload($contact_form, $webhook_url)
{
    if (!class_exists('WPCF7_Submission')) {
        return ['success' => false, 'error' => 'WPCF7_Submission class not found'];
    }

    $submission = \WPCF7_Submission::get_instance();
    if (!$submission) {
        return ['success' => false, 'error' => 'No submission instance'];
    }

    $posted = $submission->get_posted_data();
    if (!is_array($posted)) {
        $posted = [];
    }

    // Strip internal keys (starting with underscore)
    $fields = [];
    foreach ($posted as $key => $value) {
        if (strpos($key, '_') === 0) {
            continue;
        }
        $fields[$key] = $value;
    }

    // Files metadata (filenames only)
    $files = $submission->uploaded_files();
    $files_meta = [];
    if (is_array($files)) {
        foreach ($files as $field => $paths) {
            if (is_array($paths)) {
                $files_meta[$field] = array_map(function ($p) {
                    return [
                        'filename' => basename($p),
                        'path' => $p,
                    ];
                }, $paths);
            } elseif (is_string($paths) && $paths !== '') {
                $files_meta[$field] = [[
                    'filename' => basename($paths),
                    'path' => $paths,
                ]];
            }
        }
    }

    $payload = [
        'form' => [
            'id' => (int) $contact_form->id(),
            'title' => method_exists($contact_form, 'title') ? (string) $contact_form->title() : '',
        ],
        'meta' => [
            'timestamp' => gmdate('c'),
            'remote_ip' => $submission->get_meta('remote_ip'),
            'user_agent' => $submission->get_meta('user_agent'),
            'url' => $submission->get_meta('url'),
            'referer' => $submission->get_meta('referer'),
        ],
        'fields' => $fields,
    ];

    if (!empty($files_meta)) {
        $payload['files'] = $files_meta;
    }

    $args = [
        'timeout' => 15,
        'blocking' => true, // Wait for response
        'headers' => [
            'Content-Type' => 'application/json',
        ],
        'body' => wp_json_encode($payload),
    ];

    $response = wp_remote_post(esc_url_raw($webhook_url), $args);

    if (is_wp_error($response)) {
        error_log('[CF7 Webhook] Error: ' . $response->get_error_message());
        return ['success' => false, 'error' => $response->get_error_message()];
    }

    $status_code = wp_remote_retrieve_response_code($response);
    $body = wp_remote_retrieve_body($response);

    // Check if response is successful (2xx status code)
    if ($status_code >= 200 && $status_code < 300) {
        // Try to decode JSON response
        $json_response = json_decode($body, true);

        // Check if JSON has 'ok' field set to true
        if (is_array($json_response) && isset($json_response['ok']) && $json_response['ok'] === true) {
            return ['success' => true, 'response' => $json_response];
        } else {
            $error_msg = 'Invalid response format or ok=false';
            if (is_array($json_response) && isset($json_response['error'])) {
                $error_msg = $json_response['error'];
            }
            error_log('[CF7 Webhook] Invalid response: ' . $body);
            return ['success' => false, 'error' => $error_msg];
        }
    } else {
        error_log('[CF7 Webhook] HTTP Error ' . $status_code . ': ' . $body);
        return ['success' => false, 'error' => 'HTTP ' . $status_code];
    }
}

// Override the feedback response based on webhook result
add_filter('wpcf7_feedback_response', function ($response, $result) {
    if (!is_array($response)) {
        return $response;
    }

    // Extract form ID from the response
    $form_id = 0;
    if (isset($response['into']) && preg_match('/#wpcf7-f(\d+)-/', $response['into'], $matches)) {
        $form_id = (int) $matches[1];
    }

    // Check if we have a webhook result for this form
    if ($form_id && isset($GLOBALS['tiz_webhook_results'][$form_id])) {
        $webhook_result = $GLOBALS['tiz_webhook_results'][$form_id];

        if ($webhook_result['success']) {
            $response['status'] = 'mail_sent';
            $response['message'] = __('Votre message a été envoyé avec succès.', 'textdomain');
        } else {
            $response['status'] = 'mail_failed';
            $response['message'] = __('Erreur lors de l\'envoi du message: ', 'textdomain') . $webhook_result['error'];
        }
    }

    return $response;
}, 10, 2);
