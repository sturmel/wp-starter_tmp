<?php

/**
 * Single template for CPT: project
 */

if (! class_exists('Timber\\Timber')) {
    echo 'Timber not activated. Make sure you activate the plugin in <a href="/wp-admin/plugins.php#timber">/wp-admin/plugins.php</a>';
    return;
}

$context = \Timber\Timber::context();
$post = \Timber\Timber::get_post();
$context['post'] = $post;

// Map ACF fields into a project view model
$client = get_field('clients', $post->ID);
$link_url = get_field('link_url', $post->ID);
// Prefer the banner image for the hero if set, otherwise fallback to the vignette image
$image_banner = get_field('image_banner', $post->ID);
$image = $image_banner ?: get_field('image', $post->ID);

// Build tags from taxonomy 'expertise' with color from term ACF
$terms = get_the_terms($post->ID, 'expertise') ?: [];

// Read ordered hashtags from the new repeater 'expertises'
// Normalize to an array of term IDs (supports different shapes just in case)
$hashtag_rows = get_field('expertises', $post->ID);
$ordered_ids = [];
if (is_array($hashtag_rows)) {
    // Case 1: repeater rows like [ ['expertise' => 123], ... ]
    if (!empty($hashtag_rows) && isset($hashtag_rows[0]) && is_array($hashtag_rows[0]) && array_key_exists('expertise', $hashtag_rows[0])) {
        foreach ($hashtag_rows as $row) {
            if (!empty($row['expertise']) && is_numeric($row['expertise'])) {
                $ordered_ids[] = (int) $row['expertise'];
            }
        }
    } else {
        // Case 2: simple array of ids
        foreach ($hashtag_rows as $maybe_id) {
            if (is_numeric($maybe_id)) {
                $ordered_ids[] = (int) $maybe_id;
            } elseif (is_array($maybe_id) && isset($maybe_id['expertise']) && is_numeric($maybe_id['expertise'])) {
                $ordered_ids[] = (int) $maybe_id['expertise'];
            }
        }
    }
} elseif (is_numeric($hashtag_rows)) {
    // Case 3: single id
    $ordered_ids[] = (int) $hashtag_rows;
}

$ordered_ids = array_values(array_unique(array_filter($ordered_ids)));

if (!empty($ordered_ids)) {
    // Reorder $terms according to $ordered_ids; keep others afterward
    $map = [];
    foreach ($terms as $t) {
        $map[$t->term_id] = $t;
    }
    $reordered = [];
    foreach ($ordered_ids as $id) {
        if (isset($map[$id])) {
            $reordered[] = $map[$id];
            unset($map[$id]);
        }
    }
    // Append remaining (not specified) terms in original order
    foreach ($terms as $t) {
        if (isset($map[$t->term_id])) {
            $reordered[] = $t;
            unset($map[$t->term_id]);
        }
    }
    $terms = $reordered;
}

$tags = [];
foreach ($terms as $t) {
    $color = function_exists('get_field') ? get_field('color', "expertise_{$t->term_id}") : '';
    // Use taxonomy ACF term field 'page' (field_expertise_page) as the link; if empty, no link
    $page_link = function_exists('get_field') ? get_field('page', "expertise_{$t->term_id}") : '';
    $tags[] = [
        'label' => $t->name,
        'link'  => $page_link ?: null,
        'color' => $color ?: null,
    ];
}

$context['project'] = [
    'title'    => get_field('custom_title', $post->ID) ?: $post->title(),
    'client'   => $client,
    'tags'     => $tags,
    'link_url' => $link_url,
    'image'    => $image,
];

\Timber\Timber::render('single-project.twig', $context);
