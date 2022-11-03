<?php

use Hidehalo\Nanoid\Client;
use Hidehalo\Nanoid\GeneratorInterface;


function parseBlocks($blocks, $client) {

    $updatedBlocks = [];

    foreach($blocks as $block) {

        $blockType = $block->type();

        if ($blockType === 'text') {

            $footnotes = $block->footnotes()->toStructure();
            $footnotes_count = $footnotes->count();
            $ref = '';

            // -- footnote notes
            $footnotes_new = [];
            foreach($footnotes as $footnote) {

                $new_footnote = array(
                    'note' => $footnote->note()->value(),
                    'ref' => $client->generateId($size = 21),
                );

                array_push($footnotes_new, $new_footnote);
            }


            // -- footnote refs
            // regex against `<article-footnote>` tag and replace it with:
            // - `{content <article-footnote>} <a href="<short-hash-id>">{counter ref}</a>`

            // check if text->footnotes has any note
            // if yes, start matching any `<article-footnote` found in
            // $block->text()

            if ($footnotes_count > 0) {

                $text_in = $block->text();
                $pattern = '/<article-footnote>(.*)<\/article-footnote>/mU';

                // <https://stackoverflow.com/a/11174818>
                $callback = function ($matches) use ($footnotes_new) {

                    static $count = 0;
                    $count++;

                    $ref = $footnotes_new[$count]['ref'];

                    $ft_ref = 'ft-' . '-' . $ref;
                    $ft_note = '#note-ref-' . '-' . $ref;

                    $replacement = $matches[1] . '<a id="' . $ft_ref . '" href="' . $ft_note . '" class="ref-ft"><span>[' . $ref . ']</span></a>';

                    return $replacement;
                };

                $text_out = preg_replace_callback($pattern, $callback, $text_in);

            } 


            // -- update block
            $blockUpdated = new Kirby\Cms\Block([
                'content' => [
                    'text' => $text_out,
                    'footnotes' => $footnotes_new,
                ],
                'type' => 'text',
            ]);

            array_push($updatedBlocks, $blockUpdated);

        } else {
            array_push($updatedBlocks, $block);
        }

    }; // -- end blocks foreach


    return $updatedBlocks;

}


// footnote insertion: add incremental footnote number
// <https://getkirby.com/docs/cookbook/templating/update-blocks-programmatically>
Kirby::plugin('cosmo/footnote-ref', [
    'hooks' => [
        'page.update:after' => function ($newPage, $oldPage) {

            // -- parse through block-text and replace <article-footnote>
            //    with actual <a href"{ref}">{ref}</a>
            //    as well as post-update footnote list ref element
            //    with same {ref}; {ref} is a UUID generated for each footnote


            // init UUID plugin
            $client = new Client();

            // check if builder blocks have either type -> text
            // or type -> columns, if the latter map down to find
            // if each sub block is of type -> type
            $blocks = $newPage->builder()->toBlocks();
            $updatedBlocks = parseBlocks($blocks, $client);

            // foreach($blocks as $block) {

            //     $blockType = $block->type(); 

            //     if ($blockType === 'columns') {

            //         foreach($layout->columns() as $column) {
            //             $subblocks = $column->blocks();

            //             // run functions for blocks as below
            //         };

            //     };

            // }; // -- end block foreach


            $newBlocks = new Kirby\Cms\Blocks($updatedBlocks);

            // -- write to file
            kirby()->impersonate('kirby');
            $newPage->update([
                'Builder' => json_encode($newBlocks->toArray()),
            ]);

        }
    ]
]);



