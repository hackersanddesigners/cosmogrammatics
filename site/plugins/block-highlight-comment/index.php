<?php

use Kirby\Cms\Page;
use Kirby\Cms\Collection;
use Kirby\Exception\PermissionException;



// -- utils
function adjust_offset ($n, $x) {
    $x_container = ((2 + $n) + (3 + $n));
    return $x_container + $x;
};

function wrapSelectedText ($text_in, $comment_offsets, $id) {
    // split text using given offset from text-selection
    // and wrap this text selection around an extra pair of tags
    // (for now <span>{}</span>)
    // => TODO check if wrap operation is done already

    // each new item in the offset array works on a new `<t>` tag

    $text = str_replace('</p>', '', $text_in);
    $p_list = Str::split($text, '<p>');
    // dump($p_list);

    foreach($comment_offsets as $key => $value) {
        // dump([$key, $value]);
        // $left_side = str_slice($text_in, 0, $value['x1']);
        // $center_side = str_slice($text_in, $value['x1'], $value['x2']);
        // $right_side = str_slice($text_in, $value['x2']);

        // $wrap_before = '<span class="comment-highlight" id="' . $id .'" >';
        // $wrap_after = '</span>';

        // // check if wrapping is done already
        // if (Str::startsWith($left_side, $wrap_before)
        //     OR Str::startsWith($right_side, $wrap_after)) {
        //         return $text_in;
        // };
        // $text_out = $text_in;

        // $text_out = $left_side . $wrap_before . $center_side . $wrap_after . $right_side;

        // dump([$left_side, $center_side, $right_side]);

    }

    return $text_in;

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


// andré : the selection_coords field is being used here to
// organize comments into threads. It won't work with the new
// selection_text field that highlight js introudces

// -- manipulate block content to add extra spans to highlight
//    specified content in the offset
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

                    $coord_label = [];
                    foreach($coords->toStructure() as $coord) {
                        $label = '';

                        if ($type == 'text') {
                            $label = $coord->x1()->value() . '-' . $coord->x2()->value();
                        } else if ($type == 'audio') {
                            $label = $coord->t1()->value() . '-' . $coord->t2()->value();
                        }

                        array_push($coord_label, $label);
                    };
                    $coord_id = join("_", $coord_label);
                    $id = $this->bid() . '_sel_' . $type . '-' . $coord_id;

                    // dump(['id', $id]);

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
            $text_in = parent::text();

            $comment_offsets = [];
            foreach ( $threads as $thread ) {

                // if a selection exists and the thread does not target
                // the block as a whole


                if (
                    $thread['selection_type']->value() &&
                    $thread['selection_coords']->value()
                ) {

                    // We get the coordinate values in a format that is
                    // compatible with andré's wrapSelectedText method.

                    $id = $thread['selection_id'];
                    // $coords = $thread['selection_coords']->toEntity();
                    $coords = $thread['selection_coords']->toStructure();

                    // when selecting text across two or more DOM nodes
                    // we'll have two selection-coords entries
                    // (eg the offset will be broken down in two parts
                    // that we need to combine back)

                    // let's compute "absolute" char offset by
                    // using counting the DOM node tag as well as
                    // by combining eventual two or more parts
                    // offset chunks

                    // IS THIS DATA STRUCTURE NECESSARY?
                    foreach($coords as $coord) {
                        // offset values are counted w/o taking into
                        // account the node tag characters, let's do that
                        // eg `<p>{...}<\p>`, we only have `p` in this case
                        // and not the brackets; somehow kirby doesn't print
                        // correctly the full HTML tag, though it can store it
                        // just fine.

                        $x1 = $coord->x1()->value();
                        $x2 = $coord->x2()->value();

                        array_push($comment_offsets, [
                            'x1' => $x1,
                            'x2' => $x2,
                        ]);
                    };
                }
            }

            // We create a new block, with updated text content
            // that has been highlighted with the selection from
            // this thread of comments.

            // $text_updated = wrapSelectedText( $text_in, $offsets, $id );

            // $updated = new Kirby\Cms\Block([
            //     'type' => $this->type(),
            //     'content' => [
            //         'text' => $text_in,
            //         'footnotes' => $this->footnotes()->toArray(),
            //     ]
            // ]);

            // and most important: we reset $text_in to be the
            // text value of the newly updated block, so we are
            // incrementing each loop with the new contents.
            // this bugs out the data structure
            // $text_in = $updated->text();


            $text_updated = wrapSelectedText( $text_in->toString(), $comment_offsets, '' );

            // dump($comment_offsets);

            return $updated;
        }

    ]
]);
