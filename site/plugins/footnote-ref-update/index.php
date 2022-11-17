<?php

use Kirby\Uuid\Uuid;


function setBID($bid) {
    if ($bid->isEmpty()) {
        return Uuid::generate();
    } else {
        return $bid;
    }
}

function makeRef($ref) {
    if ($ref === '0') {
        return UUid::generate();
    } else {
        return $ref;
    }
}

function parseBlocks($blocks, $type) {

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
                $updatedSubblocks = parseBlocks($subblocks, 'layout');
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

            // -- footnote notes
            $footnotes_new = [];
            foreach($footnotes as $footnote) {

                $ref_new = makeRef($footnote->ref()->value());
                $new_footnote = array(
                    'note' => $footnote->note()->value(),
                    'ref' => $ref_new, 
                );

                array_push($footnotes_new, $new_footnote);
            }

            // -- update block
            $blockUpdated = [
                'content' => [
                    'text' => $block->text()->value(),
                    'bid' => setBID($block->bid()),
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
// TODO change plugin name as we update also block->bid in here?
Kirby::plugin('cosmo/footnote-ref', [
    'hooks' => [
        'page.update:after' => function ($newPage, $oldPage) {

            // -- parse through block-text and replace <article-footnote>
            //    with actual <a href"{ref}">{ref}</a>
            //    as well as post-update footnote list ref element
            //    with same {ref}; {ref} is a UUID generated for each footnote

            // check if builder blocks have either type -> text
            // or type -> columns, if the latter map down to find
            // if each sub block is of type -> text and pass blocks
            // to same function (recursive-like, but 1 level only)
            // else if blocks are of either type, simply return them
            // as-is.
            $blocks = $newPage->builder()->toBlocks();

            $updatedBlocks = parseBlocks($blocks, 'block');
            $blocksNew = new Kirby\Cms\Blocks($updatedBlocks);

            // -- write to file
            kirby()->impersonate('kirby');
            $newPage->update([
                'builder' => json_encode($blocksNew->toArray()),
            ]);

        }
    ]
]);
