<?php

Kirby::plugin('cosmo/block-iframe', [
  'blueprints' => [
    'blocks/iframe' => __DIR__ . '/blueprints/blocks/iframe.yml',
  ],
  'snippets' => [
    'blocks/iframe' => __DIR__ . '/snippets/blocks/iframe.php',
  ],
]);
