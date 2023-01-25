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

        }
    ]
]);
