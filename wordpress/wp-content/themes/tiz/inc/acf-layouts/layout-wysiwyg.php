<?php
// Layout: WYSIWYG Section
if (!function_exists('acf_add_local_field_group')) {
    return [];
}

return [
    'key' => 'layout_wysiwyg',
    'name' => 'wysiwyg',
    'label' => 'Bloc WYSIWYG',
    'display' => 'block',
    'sub_fields' => [
        [
            'key' => 'field_wysiwyg_content',
            'label' => 'Contenu',
            'name' => 'content',
            'type' => 'wysiwyg',
            'tabs' => 'all',
            'toolbar' => 'full',
            'media_upload' => 1,
            'delay' => 0,
        ],
    ],
];
