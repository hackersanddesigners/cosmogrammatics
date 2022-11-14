<section
    tabindex="0"
    class="block <?= $block->type() ?>"
    id="<?= 'b_' . $block->bid() ?>"
    data-type="block-<?= $block->type() ?>"
>


  <div class="contents">
    <?php if ( $block->layout()->isNotEmpty() ) {
      snippet( 'grid/index', [ 'rows' => $block->layout()->toLayouts() ] );
    } else {
      echo $block;
    } ?>
  </div>


  <?php

    // <2022-11-10T20:55> andré: don't know how to put this inside a
    // controller for blocks, not sure there's such a thing yet...
    // on the upside, not having to do nested loops is good!

    $block_comments = [];
    $block_threads  = [];

    if ( $comments ) {

      // get all comments related to this block

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
        foreach ( $block_threads as $thr ) {
          if (
            $thr['selection_type']->value() == $selection_type->value() &&
            $thr['selection_coords']->value() == $selection_coords->value()
          ) {
            $thread_exists = TRUE;
            break;
          }
        }

        // if the thread doesn't exist, we proceed with it's
        // creation and addition to the $block_threads array.

        if ( !$thread_exists ) {
          $block_threads[] = [
            'selection_type'   => $selection_type,
            'selection_coords' => $selection_coords,
            'comments'         => $block_comments
              ->filterBy('selection_type', $selection_type )
              ->filterBy('selection_coords', $selection_coords )
          ];
        }

      };
    }

    snippet( 'comments/index', [
        'block'    => $block,
        'comments' => $block_comments,
        'threads'  => $block_threads,
    ] )


  ?>

</section>
