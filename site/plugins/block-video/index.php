<?php

Kirby::plugin('cosmo/block-video', [
    'blueprints' => [
        'blocks/video' => __DIR__ . '/blueprints/blocks/video.yml',
        'files/video' => __DIR__ . '/blueprints/files/video.yml',
    ],
    'snippets' => [
        'blocks/video' => __DIR__ . '/snippets/blocks/video.php'
    ],
]);
