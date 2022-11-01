<?php

use Kirby\Cms\Block;
use Kirby\Cms\Content;

class DefaultBlock extends Block
{
    public function id(): string
    {

        // check if block is of type `text`, else skip
        if ($this->type() === 'text') {

            // -- footnote refs
            // regex against `<article-footnote>` tag and replace it with:
            // - `{content <article-footnote>} <a href="<short-hash-id>">{counter ref}</a>`

            // check if text->footnotes has any note
            // if yes, start matching any `<article-footnote` found in
            // $this->content->text

            $footnotes_count = $this->content()->footnotes()->toStructure()->count();

            if ($footnotes_count > 0) {

                $in = $this->content()->text();
                $pattern = '/<article-footnote>(.*)<\/article-footnote>/mU';

                // <https://stackoverflow.com/a/11174818>
                $out = preg_replace_callback(
                    $pattern,
                    function ($matches){
                        static $ref = 0;
                        $ref++;

                        $ft_ref = 'ft-' . $ref;
                        $ft_note = '#note-ref-' . $ref;

                        $replacement = $matches[1] . '<a id="' . $ft_ref . '" href="' . $ft_note . '" class="ref-ft"><span>[' . $ref . ']</span></a>';

                        return $replacement;
                    },
                    $in);


                // var_dump($out);

                // TODO: this replaces the actual text in the file
                // does it make sense to instead replace the produced HTML
                // used in the template? (maybe with caching?)
                
                // $content = $this->content()->toArray();
                // $content['text'] = $out;
                // $content_update = $this->content()->update($content, true);

            }

            return parent::id();

            // return $this->content();

        } else {
            return parent::id();

            // return $this->content();
        }
    }
}

Kirby::plugin('my/blockModels', [
    'blockModels' => [
        'Kirby\\Cms\\Block' => DefaultBlock::class,
    ]
]);
