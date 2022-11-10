<?php

use Hidehalo\Nanoid\Client;
use Hidehalo\Nanoid\GeneratorInterface;


function makeRef($client, $ref) {
    if ($ref === '0') {
        return $client->generateId($size = 21);
    } else {
        return $ref;
    }
}

function replaceFootnotePlaceholder ($text_in, $footnotes_new) {
    // this code should run only once, during the first
    // transform operation from `<article-footnote>` to
    // `<a href="{ref}">{ref}</a>`

    $pattern = '/<article-footnote>(.*)<\/article-footnote>/mU';

    // <https://stackoverflow.com/a/11174818>
    $callback = function ($matches) use ($footnotes_new) {

        static $count = -1;
        $count++;

        $ref = $footnotes_new[$count]['ref'];

        $ft_ref = 'ft-' . $ref;
        $ft_note = '#note-ref-' . $ref;

        $replacement = $matches[1] . '<a id="' . $ft_ref . '" href="' . $ft_note . '" class="ref-ft"><span>[' . $ref . ']</span></a>';

        return $replacement;
    };

    $text_out = preg_replace_callback($pattern, $callback, $text_in);
    
    return $text_out;
}

function setBID($client, $bid) {
    if ($bid === '0') {
        return $client->generateId($size = 21);;
    } else {
        return $bid;
    }
}

function parseBlocks($blocks, $client, $type) {

    $updatedBlocks = [];

    foreach($blocks as $block) {

        $blockType = $block->type();

        if ($blockType === 'columns') {

            // we have one layout per block, no need to loop over
            $layout = $block->layout()->toLayouts()->first();

            $columnsNew = [];
            foreach($layout->columns() as $column) {
                // we need to:
                // - parse the layout blocks
                // - reconstruct the layout with updated blocks
                // - convert it back to a layout object

                $subblocks = $column->blocks();
                $updatedSubblocks = parseBlocks($subblocks, $client, 'layout');
                $subblocksNew = new Kirby\Cms\Blocks($updatedSubblocks);

                $columnNew = new Kirby\Cms\LayoutColumn(
                    [
                        'blocks' => $subblocksNew->toArray(),
                        'width' => $column->width(),
                    ]
                );

                array_push($columnsNew, $columnNew);
            };

            $layoutColumnsNew = new Kirby\Cms\LayoutColumns($columnsNew);
            
            $layoutNew = Kirby\Cms\Layout::factory([
                'columns' => $layoutColumnsNew->toArray(),
            ]);

            $layoutsNew = new Kirby\Cms\Layouts([$layoutNew]);

            // -- update block
            $blockLayoutUpdated = [
                'content' => [
                    'layout' => $layoutsNew->toArray(),
                ],
                'type' => 'columns',
            ];

            $blockLayoutNew = new Kirby\Cms\Block($blockLayoutUpdated);
            array_push($updatedBlocks, $blockLayoutNew);

        } else if ($blockType === 'text') {

            $footnotes = $block->footnotes()->toStructure();
            $footnotes_count = $footnotes->count();
            $ref = '';

            // -- footnote notes
            $footnotes_new = [];
            foreach($footnotes as $footnote) {

                $ref_new = makeRef($client, $footnote->ref()->value());
                $new_footnote = array(
                    'note' => $footnote->note()->value(),
                    'ref' => $ref_new, 
                );

                array_push($footnotes_new, $new_footnote);
            }

            // -- footnote refs
            // check if text->footnotes has any note
            // if yes, start matching any `<article-footnote` found in
            // $block->text()->value()
            $text_new = $block->text()->value();

            if ($footnotes_count > 0) {
                $text_in = $block->text()->value();
                $text_new = replaceFootnotePlaceholder($text_in, $footnotes_new);
            } 

            // -- update block
            $blockUpdated = [
                'content' => [
                    'text' => $text_new,
                    'bid' => setBID($client, $block->bid()->value()),
                    'footnotes' => $footnotes_new,
                ],
                'type' => $block->type(),
            ];

            $blockUpdated = new Kirby\Cms\Block($blockUpdated);
            array_push($updatedBlocks, $blockUpdated);

        } else {
            array_push($updatedBlocks, $block);
        }

    }; // -- end blocks foreach


    return $updatedBlocks;

}


// footnote insertion: add UUID for each footnote
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
            // if each sub block is of type -> text and pass blocks
            // to same function (recursive-like, but 1 level only)
            // else if blocks are of either type, simply return them
            // as-is.
            $blocks = $newPage->builder()->toBlocks();

            $updatedBlocks = parseBlocks($blocks, $client, 'block');
            $blocksNew = new Kirby\Cms\Blocks($updatedBlocks);

            // -- write to file
            kirby()->impersonate('kirby');
            $newPage->update([
                'builder' => json_encode($blocksNew->toArray()),
            ]);

        }
    ]
]);
