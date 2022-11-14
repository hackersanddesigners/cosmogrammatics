<?php

use Kirby\Cms\Block;
use Kirby\Uuid\Uuid;


class DefaultBlock extends Block
{
    public function bid(): string
    {
        return UUid::generate();
    }
}

Kirby::plugin('my/blockModels', [
    'blockModels' => [
        'Kirby\\Cms\\Block' => DefaultBlock::class,
    ]
]);
