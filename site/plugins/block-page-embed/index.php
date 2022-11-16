<?php

Kirby::plugin('cosmo/block-page-embed', [
    'blueprints' => [
        'blocks/page_embed' => __DIR__ . '/blueprints/blocks/page-embed.yml',
    ],
    'snippets' => [
        'blocks/page_embed' => __DIR__ . '/snippets/blocks/page-embed.php',
    ],
]);
