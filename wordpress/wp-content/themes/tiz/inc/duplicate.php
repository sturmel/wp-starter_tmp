<?php

/**
 * Duplicate posts, pages, and custom post type 'project'.
 * Adds a row action link + a bulk action + admin bar item when editing a single.
 */

// Add row action link in post list tables
add_filter('post_row_actions', 'tiz_add_duplicate_link_row', 10, 2);
add_filter('page_row_actions', 'tiz_add_duplicate_link_row', 10, 2);
function tiz_add_duplicate_link_row($actions, $post)
{
    if (!current_user_can('edit_posts')) {
        return $actions;
    }
    $allowed = ['post', 'page', 'project'];
    if (!in_array($post->post_type, $allowed, true)) {
        return $actions;
    }
    $url = wp_nonce_url(
        add_query_arg([
            'action' => 'tiz_duplicate_post',
            'post' => $post->ID,
        ], admin_url('admin.php')),
        'tiz_duplicate_' . $post->ID,
        '_tizdup'
    );
    $actions['tiz_duplicate'] = '<a href="' . esc_url($url) . '" title="' . esc_attr__('Duppliquer cet élément', 'tiz') . '">' . esc_html__('Dupliquer', 'tiz') . '</a>';
    return $actions;
}

// Add bulk action
add_filter('bulk_actions-edit-post', 'tiz_register_bulk_duplicate');
add_filter('bulk_actions-edit-page', 'tiz_register_bulk_duplicate');
add_filter('bulk_actions-edit-project', 'tiz_register_bulk_duplicate');
function tiz_register_bulk_duplicate($bulk_actions)
{
    $bulk_actions['tiz_bulk_duplicate'] = __('Dupliquer', 'tiz');
    return $bulk_actions;
}

add_filter('handle_bulk_actions-edit-post', 'tiz_handle_bulk_duplicate', 10, 3);
add_filter('handle_bulk_actions-edit-page', 'tiz_handle_bulk_duplicate', 10, 3);
add_filter('handle_bulk_actions-edit-project', 'tiz_handle_bulk_duplicate', 10, 3);
function tiz_handle_bulk_duplicate($redirect_url, $action, $post_ids)
{
    if ($action !== 'tiz_bulk_duplicate') {
        return $redirect_url;
    }
    $count = 0;
    foreach ($post_ids as $post_id) {
        if (tiz_duplicate_post($post_id)) {
            $count++;
        }
    }
    return add_query_arg('tiz_duplicated', $count, $redirect_url);
}

// Admin notice for bulk
add_action('admin_notices', function () {
    $tiz_duplicated = filter_input(INPUT_GET, 'tiz_duplicated', FILTER_SANITIZE_NUMBER_INT);
    if (!empty($tiz_duplicated)) {
        $n = (int) $tiz_duplicated;
        if ($n > 0) {
            printf('<div class="notice notice-success is-dismissible"><p>' . esc_html(_n('%d élément dupliqué.', '%d éléments dupliqués.', $n, 'tiz')) . '</p></div>', $n);
        }
    }
});

// Handle single duplication action
add_action('admin_action_tiz_duplicate_post', function () {
    $get_post_id = filter_input(INPUT_GET, 'post', FILTER_SANITIZE_NUMBER_INT);
    if (empty($get_post_id)) {
        wp_die(__('Paramètre manquant.', 'tiz'));
    }
    $post_id = (int) $get_post_id;
    check_admin_referer('tiz_duplicate_' . $post_id, '_tizdup');
    if (!current_user_can('edit_post', $post_id)) {
        wp_die(__('Permissions insuffisantes.', 'tiz'));
    }
    $new_id = tiz_duplicate_post($post_id, true);
    if ($new_id) {
        wp_safe_redirect(get_edit_post_link($new_id, '')); // go to new edit screen
        exit;
    }
    wp_safe_redirect(wp_get_referer() ?: admin_url('edit.php'));
    exit;
});

/**
 * Core duplication logic.
 * @param int $post_id
 * @param bool $mark_title Add (copie) to title
 * @return int|false New post ID or false on failure
 */
function tiz_duplicate_post($post_id, $mark_title = false)
{
    $post = get_post($post_id);
    if (!$post) {
        return false;
    }
    $allowed = ['post', 'page', 'project'];
    if (!in_array($post->post_type, $allowed, true)) {
        return false;
    }
    $new_post_args = [
        'post_author' => get_current_user_id(),
        'post_content' => $post->post_content,
        'post_title' => $post->post_title . ($mark_title ? ' (' . __('Copie', 'tiz') . ')' : ''),
        'post_excerpt' => $post->post_excerpt,
        'post_status' => 'draft',
        'post_type' => $post->post_type,
        'post_password' => $post->post_password,
        'menu_order' => $post->menu_order,
        'post_parent' => $post->post_parent,
        'ping_status' => $post->ping_status,
        'comment_status' => $post->comment_status,
    ];
    $new_id = wp_insert_post($new_post_args);
    if (is_wp_error($new_id) || !$new_id) {
        return false;
    }

    // Taxonomies
    $taxes = get_object_taxonomies($post->post_type);
    foreach ($taxes as $tax) {
        $terms = wp_get_object_terms($post_id, $tax, ['fields' => 'ids']);
        if (!empty($terms) && !is_wp_error($terms)) {
            wp_set_object_terms($new_id, $terms, $tax, false);
        }
    }

    // Post meta (exclude WP core protected & revision meta automatically)
    $meta = get_post_meta($post_id);
    foreach ($meta as $key => $values) {
        // skip editors internal lock / etc.
        if (in_array($key, ['_edit_lock', '_edit_last'], true)) {
            continue;
        }
        foreach ($values as $v) {
            add_post_meta($new_id, $key, maybe_unserialize($v));
        }
    }

    // Featured image
    $thumb_id = get_post_thumbnail_id($post_id);
    if ($thumb_id) {
        set_post_thumbnail($new_id, $thumb_id);
    }

    return $new_id;
}

// Add admin bar shortcut when editing a post
add_action('admin_bar_menu', function ($wp_admin_bar) {
    if (!is_admin()) return;
    $screen = function_exists('get_current_screen') ? get_current_screen() : null;
    if (!$screen || $screen->base !== 'post') return;
    $post_id = (int) filter_input(INPUT_GET, 'post', FILTER_SANITIZE_NUMBER_INT);
    if (!$post_id) return;
    $post = get_post($post_id);
    if (!$post) return;
    if (!current_user_can('edit_post', $post_id)) return;
    if (!in_array($post->post_type, ['post', 'page', 'project'], true)) return;
    $url = wp_nonce_url(
        add_query_arg([
            'action' => 'tiz_duplicate_post',
            'post' => $post_id,
        ], admin_url('admin.php')),
        'tiz_duplicate_' . $post_id,
        '_tizdup'
    );
    $wp_admin_bar->add_node([
        'id' => 'tiz-duplicate',
        'title' => __('Dupliquer', 'tiz'),
        'href' => $url,
        'meta' => ['title' => __('Dupliquer ce contenu', 'tiz')]
    ]);
}, 999);
