<?php

// footnote insertion: add incremental footnote number
// <https://getkirby.com/docs/cookbook/templating/update-blocks-programmatically>
Kirby::plugin('cosmo/footnote-ref', [
    'hooks' => [
        'page.update:after' => function ($newPage, $oldPage) {
            // do something after the page is updated

            $blocks = $newPage->builder()->toBlocks();

            $updatedBlocks = [];
            foreach($blocks as $block) {
                if ($block->type() === 'text') {

                    // -- footnote refs
                    // regex against `<article-footnote>` tag and replace it with:
                    // - `{content <article-footnote>} <a href="<short-hash-id>">{counter ref}</a>`
                    // - in order to make the code work also on successive
                    //   call, for instance when some new footnotes are being added
                    //   later on — either on newest parts of the text or on previous
                    //   ones, we do a second match_replace to update the numbering

                    // check if text->footnotes has any note
                    // if yes, start matching any `<article-footnote` found in
                    // $block->text()

                    $footnotes = $block->footnotes()->toStructure();
                    $footnotes_count = $footnotes->count();

                    if ($footnotes_count > 0) {

                        $pattern_one_in = $block->text();
                        $pattern_one = '/<article-footnote>(.*)<\/article-footnote>/mU';

                        // <https://stackoverflow.com/a/11174818>
                        $callback = function ($matches) use ($block) {
                            // this ref works only the first time
                            // if new footnotes are added afterwards
                            // the regex (correctly) don't match on them
                            // but only on the new footnotes, so
                            // the count starts from 1 again

                            static $ref = 0;
                            $ref++;

                            $ft_ref = 'ft-' . $block->id() . '-' . $ref;
                            $ft_note = '#note-ref-' . $block->id() . '-' . $ref;

                            $replacement = $matches[1] . '<a id="' . $ft_ref . '" href="' . $ft_note . '" class="ref-ft"><span>[' . $ref . ']</span></a>';

                            return $replacement;
                        };

                        $pattern_one_text_new = preg_replace_callback($pattern_one, $callback, $pattern_one_in);

                    }

                    // -- second pass, bleah ):

                    $pattern_two = '/\<a id="*" href="#note-ref(.*)" (.*)\>(.*)\<\/a>/mU';

                    $callback = function ($matches) use ($block) {
                        static $ref = 0;
                        $ref++;

                        $ft_ref = 'ft-' . $block->id() . '-' . $ref;

                        $ft_note = '#note-ref-' . $block->id() . '-' . $ref;

                        $replacement = '<a id="' . $ft_ref . '" href="' . $ft_note . '" class="ref-ft"><span>[' . $ref . ']</span></a>';

                        return $replacement;
                    };

                    $text_new = preg_replace_callback($pattern_two, $callback, $pattern_one_text_new);

                    // $xml = simplexml_load_string($pattern_one_text_new);
                    // var_dump($xml);
                    // $list = $xml->xpath("//@href");

                    // $preparedUrls = array();
                    // foreach($list as $item) {
                    //     var_dump($item);
                    //     $item = parse_url($item);
                    //     $preparedUrls[] = $item['scheme'] . '://' .  $item['host'] . '/';
                    // }


                    // -- footnote notes
                    $footnotes_new = [];
                    foreach($footnotes as $footnote) {
                        // get footnote index to use for the `ref` field
                        $ref = $footnote->indexOf($footnotes);

                        $new_footnote = array(
                            'note' => $footnote->note()->value(),
                            'ref' => $ref +1,
                        );

                        array_push($footnotes_new, $new_footnote);
                    }


                    // -- update block
                    $blockUpdated = new Kirby\Cms\Block([
                        'content' => [
                            // 'text' => $block->text()->value(),
                            'text' => $text_new,
                            'footnotes' => $footnotes_new,
                        ],
                        'type' => 'text',
                    ]);

                    array_push($updatedBlocks, $blockUpdated);

                } else {
                    array_push($updatedBlocks, $block);
                }
            };

            $newBlocks = new Kirby\Cms\Blocks($updatedBlocks);

            // -- write to file
            kirby()->impersonate('kirby');
            $newPage->update([
                'Builder' => json_encode($newBlocks->toArray()),
            ]);

        }
    ]
]);
