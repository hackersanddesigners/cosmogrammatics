<?php foreach ( $page->builder()->toBlocks() as $block ): ?>
  <div
    id="<?= $block->id() ?>"
    data-type="block-<?= $block->type() ?>"
  >
    <?= $block ?>
    <?php if ( $comments->hasListedChildren() ) {
      snippet( 'comments', [
        'comments' => $comments
        ->children
        ->listed()
        ->filterBy( 'block_id', $block->id() )
      ] );
    } ?>
  </div>
<?php endforeach ?>
