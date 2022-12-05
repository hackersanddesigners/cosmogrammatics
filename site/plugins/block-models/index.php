<?php

use Kirby\Cms\Block;


function replaceFootnotePlaceholder ($text_in, $footnotes) {
    // this code should run only once, during the first
    // transform operation from `<article-footnote>` to
    // `<a href="{ref}">{ref}</a>`

    $pattern = '/<article-footnote>(.*)<\/article-footnote>/mU';

    // <https://stackoverflow.com/a/11174818>
    $callback = function ($matches) use ($footnotes) {

        static $count = -1;
        $count++;

        $ref = $footnotes[$count]['ref'];

        $ft_ref = 'ft-' . $ref;
        $ft_note = '#note-ref-' . $ref;

        $replacement = $matches[1] . '<a id="' . $ft_ref . '" href="' . $ft_note . '" class="ref-ft"></a>';

        return $replacement;
    };

    $text_out = preg_replace_callback($pattern, $callback, $text_in);
    
    return $text_out;
}

class DefaultBlock extends Block {
    public function text() {

        $isPanel = Str::startsWith(parent::kirby()->route()->pattern(), 'api/(.*)');

        if ($isPanel == false) {
           
            // -- footnote refs
            // check if text->footnotes has any note
            // if yes, start matching any `<article-footnote` found in
            // $block->text()

            $footnotes = parent::footnotes()->toStructure();
            $footnotes_count = $footnotes->count();

            if ($footnotes_count > 0) {
                $text_in = parent::text();
                $footnotes_list = $footnotes->toArray();
                $text_out = replaceFootnotePlaceholder($text_in, $footnotes_list);

                return $text_out;

            } else {
                return parent::text();
            }

        } else {
            return parent::text();
        }

    }
}

Kirby::plugin('cosmo/set-footnote', [
    'blockModels' => [
        'Kirby\\Cms\\Block' => DefaultBlock::class,
    ]
]);
