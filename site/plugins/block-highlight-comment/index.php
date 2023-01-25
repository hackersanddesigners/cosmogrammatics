<?php

// -- setup custom thread method:
//    get all comments related to this block:
//    - filter by block_id
Kirby::plugin('cosmo/block-highlight-comment', [

    'blockMethods' => [
        'threads' => function ($comments = []) {

            // originally this function was meant to filter comments also
            // by selection_coords (an object containing x1-3, y1-3, t1-2
            // set of fields to store any type of block selection (text, image,
            // audio, video, ...).
            // as of <2023-01-25> we ended up using a specific JS plugin to handle
            // text-selection in the frontend and adding block-level commenting
            // besides this; so: no another selection coords are needed and we're
            // not filtering by this field for the time being.

            $threads = [];
            if ($comments) {
                $block_comments = $comments->filterBy('block_id', $this->bid() );

                // for each block_comment check if there are comments
                // that belong to the same "thread", meaning they share
                // a selection type and coords

                foreach( $block_comments as $block_comment ) {

                    $type = $block_comment->selection_type();

                    // first, check if thread has already been created.
                    // It can be uniquely identified by its selection type
                    // and coords.
                    $exists = FALSE;
                    foreach ($threads as $thr) {
                        if ($thr['selection_type']->value() == $type->value()) {
                            $exists = TRUE;
                            break;
                        }
                    }

                    // if the thread doesn't exist, we proceed with it's
                    // creation and addition to the $threads array.
                    if (!$exists) {
                        $threads[] = [
                            'selection_type'   => $type,
                            'comments'         => $block_comments
                            ->filterBy('selection_type', $type)
                        ];
                    }

                };
            }

            return $threads;

        }
    ]
]);
