
<?php

  $block_comments = NULL;
  $block_threads  = [];

  if ( $comments->hasListedChildren() ) {
    $block_comments = $comments->children->listed()->filterBy( 'block_id', $block->id() );

    // TODO: transform the flat "block_comments" array into
    // an array of "threads" each being an array of comments,
    // threads share: selection type, and selection coordinates

    foreach ( $block_comments as $comment ) {
      $selection_type   = $comment -> selection_type();
      $selection_coords = $comment -> { 'selection_' . $selection_type }();

      $thread = [
        'selection' => [
          'type'   => $selection_type,
          'coords' => $selection_coords
        ],
        'comments' => $block_comments
                      -> filterBy( 'selection_type', '==', $selection_type )
                      -> filterBy( 'selection_coords', '==', $selection_coords )
                      // andré help <\3
      ];
    }
  }
?>

<section
  tabindex="0"
  class="block <?= $block->type() ?>"
  id="<?= 'b_' . $block->id() ?>"
  data-type="block-<?= $block->type() ?>"
>


  <div class="contents">
    <span><?= $block->uid() ?></span>
    <?php
    if ( $block->layout()->isNotEmpty() ) {
      snippet( 'grid/index', [ 'rows' => $block->layout()->toLayouts() ] );
    } else {
      echo $block;
    } ?>
  </div>

  <?php snippet( 'comments/index', [
    'block'    => $block,
    'comments' => $block_comments,
    // 'threads'  => $block_threads
  ] ) ?>

</section>
