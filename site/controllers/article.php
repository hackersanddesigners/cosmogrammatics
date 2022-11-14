<?php


// ANDRÉ I am defining this function here, but only able to use it
// in the block.php snipppet itself.

function get_block_threads( $block, $comments ) {

  $threads = [];

  // get all comments related to this block

  if ( $comments ) {
    $block_comments = $comments->filterBy('block_id', $block->bid());

    // for each block_comment check if there are comments
    // that belong to the same "thread", meaning they share
    // a selection type and coords

    foreach( $block_comments as $block_comment ) {

      $selection_type   = $block_comment->selection_type();
      $selection_coords = $block_comment->selection_coords();

      // first, check if thread has already been created.
      // It can be uniquely identified by its selection type
      // and coords.

      $thread_exists = FALSE;
      foreach ( $threads as $thr ) {
        if (
          $thr['selection_type']->value() == $selection_type->value() &&
          $thr['selection_coords']->value() == $selection_coords->value()
        ) {
          $thread_exists = TRUE;
          break;
        }
      }

      // if the thread doesn't exist, we proceed with it's
      // creation and addition to the $threads array.

      if ( !$thread_exists ) {
        $threads[] = [
          'selection_type'   => $selection_type,
          'selection_coords' => $selection_coords,
          'comments'         => $block_comments
            ->filterBy('selection_type', $selection_type )
            ->filterBy('selection_coords', $selection_coords )
        ];
      }

    };
  }

  return $threads;

}


return function ($page) {

    // <article>/comments/<[comment-1, comment-2, ...]>
    $comments = $page->children()->children()->listed();

    // footnotes
    $footnotes_all = [];

    $blocks = $page->builder()->toBlocks();
    foreach($blocks as $block) {
        if ($block->type() === 'columns') {

            // collect eventual block text footnotes
            $layout = $block->layout()->toLayouts()->first();
            foreach($layout->columns() as $column) {
                $subblocks = $column->blocks();
                foreach($subblocks as $subblock) {

                    if ($subblock->type() === 'text') {
                        $subfootnotes = $subblock->footnotes()->toStructure();

                        foreach($subfootnotes as $subfootnote) {
                            array_push($footnotes_all, $subfootnote);
                        };
                    }
                };
            };

        } else if ($block->type() === 'text') {
            $footnotes = $block->footnotes()->toStructure();

            foreach($footnotes as $footnote) {
                array_push($footnotes_all, $footnote);
            };
        }
    }

    // doesnt work here for some reason
    // $block->thread = get_block_threads( $block, $comments )

    $skin = [
        'colors' => $page->colors()->toEntity(),
        'fonts'  => $page->fonts()->toEntity(),
        'css'    => $page->css()->toStructure()
    ];

    return [
        'comments' => $comments,
        'footnotes' => $footnotes,
        'skin'     => $skin
    ];

};
