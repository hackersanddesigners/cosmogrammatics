<?php

use Kirby\Cms\Page;
use Kirby\Cms\Collection;
use Kirby\Exception\PermissionException;


function wrapSelectedText ($text_in, $offset, $id) {
    // split text using given offset from text-selection
    // and wrap this text selection around an extra pair of tags
    // (for now <span>{}</span>)
    // => TODO check if wrap operation is done already

    // $left_side = str_slice($text_in, 0, $offset['x1']);
    // $center_side = str_slice($text_in, $offset['x1'], $offset['y1']);
    // $right_side = str_slice($text_in, $offset['y1']);

    // $wrap_before = '<span class="comment-highlight" id="' . $id .'" >';
    // $wrap_after = '</span>';

    // check if wrapping is done already
    // if (Str::startsWith($left_side, $wrap_before)
    //     OR Str::startsWith($right_side, $wrap_after)) {
    //         return $text_in;
    // };
    // $text_out = $text_in;

    // $text_out = $left_side . $wrap_before . $center_side . $wrap_after . $right_side;

    // return $text_out;
}

// <https://stackoverflow.com/a/53985015>
function str_slice() {
    $args = func_get_args();

    switch (count($args)) {
    case 1:
        return $args[0];
    case 2:
        $str        = $args[0];
        $str_length = strlen($str);
        $start      = $args[1];
        if ($start < 0) {
            if ($start >= - $str_length) {
                $start = $str_length - abs($start);
            } else {
                $start = 0;
            }
        }
        else if ($start >= $str_length) {
            $start = $str_length;
        }
        $length = $str_length - $start;
        return substr($str, $start, $length);
    case 3:
        $str        = $args[0];
        $str_length = strlen($str);
        $start      = $args[1];
        $end        = $args[2];
        if ($start >= $str_length) {
            return "";
        }
        if ($start < 0) {
            if ($start < - $str_length) {
                $start = 0;
            } else {
                $start = $str_length - abs($start);
            }
        }
        if ($end <= $start) {
            return "";
        }
        if ($end > $str_length) {
            $end = $str_length;
        }
        $length = $end - $start;
        return substr($str, $start, $length);
    }
    return null;
};


function parseBlockSelection($blocks, $block_bid, $comments, $offset, $type) {

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
                    $updatedSubblocks = parseBlockSelection($subblocks, $block_bid, $comments, $offset, 'layout');
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
            }

        } else if ($blockType === 'text' && $block->bid() === $block_bid) {

            $text_in = $block->text()->value();
            $text_new = wrapSelectedText($text_in, $offset);

            // -- update block
            $blockUpdated = [
                'content' => [
                    'text' => $text_new,
                    'footnotes' => $block->footnotes()->toArray(),
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



Kirby::plugin('cosmo/block-highlight-comment', [
    'blockMethods' => [
        // setup custom thread method
        // TODO add more commentary
        'threads' => function ( $comments = [] ) {

            $threads = [];

            // get all comments related to this block

            if ( $comments ) {
                $block_comments = $comments->filterBy('block_id', $this->bid() );

                // for each block_comment check if there are comments
                // that belong to the same "thread", meaning they share
                // a selection type and coords

                foreach( $block_comments as $block_comment ) {

                    $type   = $block_comment->selection_type();
                    $coords = $block_comment->selection_coords();
                    $id     = $this->bid() . '_sel_' . $type . $coords ;

                    // first, check if thread has already been created.
                    // It can be uniquely identified by its selection type
                    // and coords.

                    $exists = FALSE;
                    foreach ( $threads as $thr ) {
                        if (
                            $thr['selection_type']->value() == $type->value() &&
                            $thr['selection_coords']->value() == $coords->value()
                        ) {
                            $exists = TRUE;
                            break;
                        }
                    }

                    // if the thread doesn't exist, we proceed with it's
                    // creation and addition to the $threads array.

                    if ( !$exists ) {
                        $threads[] = [
                            'selection_type'   => $type,
                            'selection_coords' => $coords,
                            'selection_id'     => $id,
                            'comments'         => $block_comments
                            ->filterBy('selection_type', $type )
                            ->filterBy('selection_coords', $coords )
                        ];
                    }

                };
            }

            return $threads;

        },


        'highlightComments' => function ( $threads ) {

            // original text content of the block

            $updated = $this;
            $text_in = $this->text();

            foreach ( $threads as $thread ) {

                // if a selection exists and the thread does not target
                // the block as a whole

                if (
                    $thread['selection_type']->value() &&
                    $thread['selection_coords']->value()
                ) {

                    // We get the coordinate values in a format that is
                    // compatible with andré's wrapSelectedText method.

                    $id     = $thread['selection_id'];
                    // $coords = $thread['selection_coords']->toEntity();
                    $coords = $thread['selection_coords']->toStructure();

                    // if selecting text across two or more DOM nodes
                    // we'll have two selection-coords entries
                    // (eg the offset will be broken down in two parts
                    // that we need to combine back)

                    // let's compute "absolute" char offset by
                    // using counting the DOM node tag as well as
                    // by combining eventual two or more parts
                    // offset chunks

                    $offset = [];
                    $MULTIPLE_OFFSET = $coords->count() > 1;

                    // dump([$coords->first()->n1()->html()->value(),
                    //       $coords->first()->n1()->html()->length()]);

                    foreach($coords as $coord) {
                        // offset values are counted w/o taking into
                        // account the node tag characters, let's do that

                        $x1_container = (
                            (2 + $coord->n1()->length()) + (3 + $coord->n2()->length())
                        );

                        $x1_offset = $coord->x1()->value();

                        dump([$x1_container,
                              $x1_offset]);

                        $offset['x1'] = $x1_container + $x1_offset;

                        // $offset['x1'] = $coord->x1()->value();
                        // $offset['x2'] = $coord->x2()->value();
                    };

                    dump($offset);
                    
                    // $offset = [
                    //     'x1' => $coords->x1()->value(),
                    //     'y1' => $coords->y1()->value()
                    // ];

                    // We create a new block, with updated text content
                    // that has been highlighted with the selection from
                    // this thread of comments.

                    // var_dump([$coords, $offset]);
                    
                    $updated = new Kirby\Cms\Block([
                        'type' => $this->type(),
                        'content' => [
                            'text'      => wrapSelectedText( $text_in, $offset, $id ),
                            'footnotes' => $this->footnotes()->toArray(),
                        ]
                    ]);

                    // and most important: we reset $text_in to be the
                    // text value of the newly updated block, so we are
                    // incrementing each loop with the new contents.

                    $text_in = $updated->text();

                }
            }



            return $updated;
        }

    ]
]);
