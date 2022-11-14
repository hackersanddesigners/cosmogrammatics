<?php

use Kirby\Cms\Page;
use Kirby\Cms\Collection;
use Kirby\Exception\PermissionException;


function wrapSelectedText ($text_in, $offset, $id) {
    // regex match text inside HTML (w/o DOM tags?)
    // using given offset from text-selection
    // and wrap this text selection around an extra pair of tags
    // (for now <span>{}</span>)
    // => check if wrap operation is done already

    $left_side = str_slice($text_in, 0, $offset['x1']);
    $center_side = str_slice($text_in, $offset['x1'], $offset['y1']);
    $right_side = str_slice($text_in, $offset['y1']);

    $wrap_before = '<span class="comment-highlight" id="' . $id .'" >';
    $wrap_after = '</span>';

    // // check if wrapping is done already
    // if (Str::startsWith($left_side, $wrap_before)
    //     OR Str::startsWith($right_side, $wrap_after)) {
    //         return $text_in;
    // };
    // $text_out = $text_in;

    $text_out = $left_side . $wrap_before . $center_side . $wrap_after . $right_side;

    return $text_out;
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
            $layout = $block->layout()->toLayouts()->first();

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


// // parse block-text and wrap comments pointing to a specific text offset
// // inside a span tag in order to manipulate it visually with CSS in the frontend
// // similarly to the `cosmo/footnote-ref` plugin, we need to map through
// // block-text as well as block->columns->block-text
// Kirby::plugin('cosmo/block-highlight-comment', [
//     'hooks' => [
//         'route:after' => function ($route, $path, $method) {

//             $kirby = kirby();
//             $request = $kirby->request();

//             // url => api/pages/articles+tottoaa+comments/children
//             if (Str::startsWith($path, 'api/pages/articles+')
//                 && Str::endsWith($path, '+comments/children')
//                 && $method == "POST") {

//                 // <https://getkirby.com/docs/cookbook/forms/user-registration>

//                 // check if CSRF token is valid
//                 $csrf = $request->csrf();
//                 if (csrf($csrf) === true) {

//                     $body = $request->body();

//                     // set a new empty collection
//                     // and then update the var with any comments
//                     // part of the current article
//                     $comments = new Collection();
//                     $page = page($body->get('fullpath'));

//                     // filter article comments by block_id if any
//                     // else skip
//                     if ($page->hasChildren()) {
//                         $block_bid = $body->get('content')['block_bid'];

//                         if ($block_bid != '') {
//                             $comments = $page->children()->drafts()->filterBy('block_bid', $block_bid);

//                             $offset = $body->get('content')['selection_text'];

//                             $blocks = $page->builder()->toBlocks();
//                             $updatedBlocks = parseBlockSelection($blocks, $block_bid, $comments, $offset, 'block');
//                             $blocksNew = new Kirby\Cms\Blocks($updatedBlocks);

//                             // // -- write to file
//                             // kirby()->impersonate('kirby');
//                             // $page->update([
//                             //     'builder' => json_encode($blocksNew->toArray()),
//                             // ]);

//                         }
//                     }

//                 } // -- end
//             }
//         }
//     ]
// ]);



Kirby::plugin('cosmo/block-methods', [
  'blockMethods' => [

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
          $id     = 'b_' . $this->bid() . '_sel_' . $type . $coords ;

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
      $text_in = $this->text()->value();

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
          $coords = $thread['selection_coords']->toEntity();
          $offset = [
            'x1' => $coords->x1()->value(),
            'y1' => $coords->y1()->value()
          ];

          // We create a new block, with updated text content
          // that has been highlighted with the selection from
          // this thread of comments.

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

          $text_in = $updated->text()->value();

        }
      }



      return $updated;
    }

  ]
]);
