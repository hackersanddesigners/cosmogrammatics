<?php

use Kirby\Cms\Block;
use Hidehalo\Nanoid\Client;
use Hidehalo\Nanoid\GeneratorInterface;

class DefaultBlock extends Block
{
    public function id(): string
    {

        $client = new Client();
        $uuid = $client->generateId($size = 21);

        if ($this->type() === 'text') {

            // -- footnote refs
            // regex against `<article-footnote>` tag and replace it with:
            // - `{content <article-footnote>} <a href="<short-hash-id>">{counter ref}</a>`

            $text = $this->content()->text();

            $pattern = '/\<article-footnote>(.*)<\/article-footnote>/m';
            $replacement = '$1 <a href="#ref-' . $uuid . '" class="ref"><span>[*]</span></a>';

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
