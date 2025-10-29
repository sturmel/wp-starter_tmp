<?php

/**
 * Template pour les pages individuelles
 */

if (! class_exists('Timber\\Timber')) {
    echo 'Timber not activated. Make sure you activate the plugin in <a href="/wp-admin/plugins.php#timber">/wp-admin/plugins.php</a>';
    return;
}

$context = \Timber\Timber::context();
$context['post'] = \Timber\Timber::get_post();

// Utiliser le template flexible pour toutes les pages sauf la page d'accueil
if (is_front_page()) {
    $posts = \Timber\Timber::get_posts();
    $context['posts'] = $posts;
    \Timber\Timber::render('index.twig', $context);
} else {
    \Timber\Timber::render('template-flexible.twig', $context);
}
