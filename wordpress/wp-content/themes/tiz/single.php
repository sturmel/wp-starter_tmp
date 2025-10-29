<?php
// Single Post template
use Timber\Timber;

$context = Timber::context();
$post = Timber::get_post();
$context['post'] = $post;

// Featured image
$context['featured_image'] = $post ? $post->thumbnail() : null;

// Header fields: custom title + subtitle
$context['post_header'] = [
    'custom_title' => function_exists('get_field') && $post ? (get_field('custom_title', $post->ID) ?: '') : '',
    'subtitle'     => function_exists('get_field') && $post ? (get_field('subtitle', $post->ID) ?: '') : '',
];

// Flexible content (if using ACF flexible field on posts): expects field name 'flexible_content'
$context['flex_sections'] = function_exists('get_field') && $post ? get_field('flexible_content', $post->ID) : [];

Timber::render('views/single-post.twig', $context);
