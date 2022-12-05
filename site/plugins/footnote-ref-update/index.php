<?php

use Kirby\Uuid\Uuid;


function setBID($bid) {
    if ($bid == NULL || $bid->exists() == false || $bid->isEmpty()) {
        return 'b_' . Uuid::generate();
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
            if ($layout = $block->layout()->toLayouts()->first()) {

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
                        'bid' => setBID($block->bid()),
                    ],
                    'type' => 'columns',
                ];

                $blockLayoutNew = new Kirby\Cms\Block($blockLayoutUpdated);
                array_push($updatedBlocks, $blockLayoutNew);
            }

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

            $blockContent = $block->content()->toArray();

            // if we first make a block-column and then add
            // a block inside the column, we `bid` field is not
            // added yet to the block. let's check if it
            // exists and if not, let's create it first

            if (array_key_exists('bid', $blockContent)) {
                array_walk($blockContent, function (&$value, $key) use ($block) {
                    if($key == 'bid'){ 
                        $value = setBID($block->bid()); 
                    }
                });

            } else {
                // add new `bid` key-value pair to array
                $blockContent['bid'] = '';
                $blockContent['bid'] = setBID('');
            }

            $blockUpdated = [
                'content' => $blockContent,
                'type' => $block->type(),
            ];

            $blockNew = new Kirby\Cms\Block($blockUpdated);
            array_push($updatedBlocks, $blockNew);

        }

    }; // -- end blocks foreach


    return $updatedBlocks;

}


// set block->bid and footnote ref across blocks
// both values are generated as UUID
Kirby::plugin('cosmo/footnote-ref', [
    'hooks' => [
        'page.update:after' => function ($newPage, $oldPage) {

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
