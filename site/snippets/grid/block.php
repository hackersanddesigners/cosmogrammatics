
<?php
  $block_comments = NULL;
  if ( $comments->hasListedChildren() ) {
    $block_comments = $comments->children->listed()->filterBy( 'block_id', $block->id() );
  }
?>

<section
  class="block <?= $block->type() ?>"
  id="<?= 'b_' . $block->id() ?>"
  data-type="block-<?= $block->type() ?>"
>

  <?= $block ?>
  <?php if ( $comments->hasListedChildren() ) {
    snippet( 'comments/index', [ 'comments' => $block_comments ] );
  } ?>

</section>
