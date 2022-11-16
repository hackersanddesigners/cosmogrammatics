<?php

Kirby::plugin('cosmo/block-page-embed', [
    'blueprints' => [
        'blocks/page-embed' => __DIR__ . '/blueprints/blocks/page-embed.yml',
    ],
    'snippets' => [
        'blocks/page-embed' => __DIR__ . '/snippets/blocks/page-embed.php',
    ],
]);
