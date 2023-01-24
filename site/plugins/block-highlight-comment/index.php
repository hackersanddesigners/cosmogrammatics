<?php

// use Kirby\Cms\Page;
// use Kirby\Cms\Collection;
// use Kirby\Exception\PermissionException;


// -- setup custom thread method
Kirby::plugin('cosmo/block-highlight-comment', [

    'blockMethods' => [
        // TODO add more commentary
        'threads' => function ($comments = []) {

            $threads = [];

            // get all comments related to this block

            if ($comments) {
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


            // dump($comment_offsets);
            return $updated;
        }

    ]
]);
