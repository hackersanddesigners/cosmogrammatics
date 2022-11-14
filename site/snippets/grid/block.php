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

  <?php snippet( 'comments/index', [
    'block'   => $block,
    'threads' => get_block_threads( $block, $comments )
  ]) ?>

</section>
