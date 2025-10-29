<?php
// SEO pour la page qui contient le layout "Projets – Archive"
add_action('wp_head', function () {
    if (!is_page()) {
        return;
    }
    $post_id = get_queried_object_id();
    if (!$post_id) {
        return;
    }

    // Vérifie si le layout Project Archive est présent sur cette page
    $flex = get_field('flexible_content', $post_id);
    if (!$flex || !is_array($flex)) {
        return;
    }

    $layout_settings = [
        'posts_per_page' => 9,
        'orderby' => 'date',
        'order' => 'DESC',
    ];
    $has_archive_layout = false;
    foreach ($flex as $row) {
        if (isset($row['acf_fc_layout']) && $row['acf_fc_layout'] === 'project-archive') {
            $has_archive_layout = true;
            // Récupère les réglages du layout (si définis)
            if (isset($row['posts_per_page']) && (int)$row['posts_per_page'] > 0) {
                $layout_settings['posts_per_page'] = (int)$row['posts_per_page'];
            }
            if (!empty($row['orderby'])) {
                $layout_settings['orderby'] = $row['orderby'];
            }
            if (!empty($row['order'])) {
                $layout_settings['order'] = $row['order'];
            }
            break; // on ne gère qu'une occurrence
        }
    }

    if (!$has_archive_layout) {
        return;
    }

    // Paramètres d'URL
    $q = (string) get_query_var('q');
    $pg = (int) get_query_var('pg');
    if ($pg < 1) {
        $pg = 1;
    }
    $expertise = get_query_var('expertise');
    $expertise_slugs = [];
    if (!empty($expertise)) {
        $expertise_slugs = is_array($expertise) ? array_filter($expertise) : [$expertise];
    }

    $base = get_permalink($post_id);

    // Canonical: pas de paramètre q, mais conserve expertise et pg
    $canon_args = [];
    if ($pg > 1) {
        $canon_args['pg'] = $pg;
    }
    if (!empty($expertise_slugs)) {
        $canon_args['expertise'] = $expertise_slugs;
    }
    $canonical = empty($canon_args) ? $base : add_query_arg($canon_args, $base);

    // Éviter les doublons si un plugin SEO majeur gère déjà ces balises
    if (defined('RANK_MATH_VERSION') || defined('WPSEO_VERSION')) {
        // Laisser le plugin gérer canonical, robots, prev/next. On peut sortir ici.
        return;
    }

    echo '<link rel="canonical" href="' . esc_url($canonical) . '" />' . "\n";

    // Robots: noindex sur la recherche
    $robots = (trim($q) !== '') ? 'noindex,follow' : 'index,follow';
    echo '<meta name="robots" content="' . esc_attr($robots) . '" />' . "\n";

    // Calcul pagination (prev/next)
    $ppp = (int) $layout_settings['posts_per_page'];
    $count_args = [
        'post_type'      => 'project',
        'post_status'    => 'publish',
        's'              => $q,
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'suppress_filters' => true,
    ];
    if (!empty($expertise_slugs)) {
        $count_args['tax_query'] = [[
            'taxonomy' => 'expertise',
            'field'    => 'slug',
            'terms'    => $expertise_slugs,
        ]];
    }
    $all_ids = get_posts($count_args);
    $total_posts = is_array($all_ids) ? count($all_ids) : 0;
    $total_pages = max(1, (int) ceil($total_posts / max(1, $ppp)));

    // Prev
    if ($pg > 1) {
        $prev_pg = $pg - 1;
        $prev_args = [];
        if (!empty($expertise_slugs)) {
            $prev_args['expertise'] = $expertise_slugs;
        }
        if ($prev_pg > 1) {
            $prev_args['pg'] = $prev_pg;
        }
        $prev_url = empty($prev_args) ? $base : add_query_arg($prev_args, $base);
        echo '<link rel="prev" href="' . esc_url($prev_url) . '" />' . "\n";
    }
    // Next
    if ($pg < $total_pages) {
        $next_pg = $pg + 1;
        $next_args = [];
        if (!empty($expertise_slugs)) {
            $next_args['expertise'] = $expertise_slugs;
        }
        $next_args['pg'] = $next_pg;
        $next_url = add_query_arg($next_args, $base);
        echo '<link rel="next" href="' . esc_url($next_url) . '" />' . "\n";
    }
}, 12);
