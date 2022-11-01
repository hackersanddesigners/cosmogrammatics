<?php

use Kirby\Cms\Block;
use Hidehalo\Nanoid\Client;
use Hidehalo\Nanoid\GeneratorInterface;

class DefaultBlock extends Block
{
    public function id(): string
    {

        $client = new Client();

        if ($this->type() === 'text') {
            $text = $this->content()->text();

            // regex against `<article-footnote>` tag and replace it with:
            // - <li><a href="<short-hash-id>"></a></li>

            $uuid = $client->generateId($size = 21);

            $pattern = '/\<article-footnote>(.*)<\/article-footnote>/m';
            $replacement = '<a href="#ref-' . $uuid . '">$1</a>';

            $new_text = preg_replace($pattern, $replacement, $text);

            $content = $this->content()->toArray();
            $content['text'] = $new_text;
            $content_update = $this->content()->update($content, true);

            return 'block-' . parent::id();

        } else {
            return 'block-' . parent::id();
        }
    }
}

Kirby::plugin('my/blockModels', [
    'blockModels' => [
        'Kirby\\Cms\\Block' => DefaultBlock::class,
    ]
]);
