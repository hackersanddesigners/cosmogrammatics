<section
    tabindex="0"
    class="block <?= $block->type() ?>"
    id="<?= 'b_' . $block->bid() ?>"
    data-type="block-<?= $block->type() ?>"
>


  <div class="contents">
  <?php
      if ( $block->layout()->isNotEmpty() ) {
          snippet( 'grid/index', [ 'rows' => $block->layout()->toLayouts() ] );
      } else {
          echo $block;
      } ?>
  </div>


<?php

    // <2022-11-10T20:55> andré: don't know how to put this inside a
    // controller for blocks, not sure there's such a thing yet...
    // on the upside, not having to do nested loops is good!
    $block_comments = $comments->filterBy('block_id', $block->bid());

    $block_threads = [];
    if ($comments) {

        $block_comments = $comments->filterBy('block_id', $block->bid());

        // foreach ($block_comments as $comment) {
        //     $selection_type = $comment->selection_type();
        //     // magic trick to call $comment->selection{text, image, audio, video}()
        //     // => eg $comment->selection_text();
        //     $selection_coords_key = 'selection_' . $selection_type;
        //     $selection_coords = $comment->{ $selection_coords_key }();
        //     $selection_coords_data = $selection_coords->toStructure();
        // }

        // for each block comment selection, check how many
        // comments there are
        foreach($block_comments as $block_comment) {

            $selection_type = $block_comment->selection_type();
            $selection_coord = $block_comment->selection_coords()->first()->value();

            // testing with selection_text for now
            // while figuring out decent way to filter out
            $matches = $block_comments
                     ->filterBy('selection_type', 'text')
                     ->filterBy('selection_coords', '!=', 'NULL')
                     ->filterBy('selection_coords', '==', $selection_coord);

            if ($matches->count() > 0) {

                $thread = [
                    'selection' => [
                        'type'   => $selection_type,
                        'coords' => $block_comment->selection_coords()->toStructure(),
                    ],
                    'comments' => $matches
                ];

                array_push($block_threads, $thread);
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
