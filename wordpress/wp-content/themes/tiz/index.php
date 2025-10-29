<?php

/**
 * The main template file
 */

if (! class_exists('Timber')) {
    echo 'Timber not activated. Make sure you activate the plugin in <a href="/wp-admin/plugins.php#timber">/wp-admin/plugins.php</a>';
    return;
}

$context = \Timber\Timber::context();

// Check if this is the front page
if (is_front_page()) {
    $context['post'] = \Timber\Timber::get_post();
    \Timber\Timber::render('template-flexible.twig', $context);
} elseif (is_page()) {
    // Pour les pages, utiliser le template flexible content
    $context['post'] = \Timber\Timber::get_post();
    \Timber\Timber::render('template-flexible.twig', $context);
} else {
    $posts = \Timber\Timber::get_posts();
    $context['posts'] = $posts;
    \Timber\Timber::render('index.twig', $context);
}
