
<?php
  $block_comments = NULL;
  if ( $comments->hasListedChildren() ) {
    $block_comments = $comments->children->listed()->filterBy( 'block_id', $block->id() );
  }
?>

<section
  id="<?= $block->id() ?>"
  data-type="block-<?= $block->type() ?>"
>

  <!-- <code>
    <pre>
      <?= var_dump( $block->toHTML() ) ?>
    </pre>
  </code> -->

  <?= $block ?>
  <?php if ( $comments->hasListedChildren() ) {
    snippet( 'comments/index', [ 'comments' => $block_comments ] );
  } ?>

</section>
