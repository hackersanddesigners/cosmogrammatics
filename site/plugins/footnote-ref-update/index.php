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

                    $footnotes = [];
                    foreach($block->footnotes()->toStructure() as $footnote) {
                        // get footnote index to use for the `ref` field
                        $ref = $footnote->indexOf($block->footnotes()->toStructure());

                        $new_footnote = array(
                            'note' => $footnote->note()->value(),
                            'ref' => $ref +1,
                        );

                        array_push($footnotes, $new_footnote);
                    }

                    $blockUpdated = new Kirby\Cms\Block([
                        'content' => [
                            'text' => $block->text(),
                            'footnotes' => $footnotes,
                        ],
                        'type' => 'text',
                    ]);

                    array_push($updatedBlocks, $blockUpdated);

                } else {
                    array_push($updatedBlocks, $block);
                }
            };

            // $newBlocks = $blocks->add(new Kirby\Cms\Blocks($updatedBlocks));
            $newBlocks = new Kirby\Cms\Blocks($updatedBlocks);

            // -- write to file
            kirby()->impersonate('kirby');
            $newPage->update([
                'Build' => json_encode($newBlocks->toArray()),
            ]);

        }
    ]
]);
