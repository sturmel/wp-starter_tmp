<?php

/**
 * The template for displaying the front page
 * This template will be used for the homepage
 */

if (! class_exists('Timber')) {
    echo 'Timber not activated. Make sure you activate the plugin in <a href="/wp-admin/plugins.php#timber">/wp-admin/plugins.php</a>';
    return;
}

$context = Timber::context();

// Get the front page post data
$post = Timber::get_post();
$context['post'] = $post;

// Utiliser le template flexible pour la page d'accueil aussi
Timber::render('template-flexible.twig', $context);
