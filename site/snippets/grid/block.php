<section
  tabindex="0"
  class="block <?= $block->type() ?>"
  id="<?= 'b_' . $block->bid() ?>"
  data-type="block-<?= $block->type() ?>"
>

  <?php $threads = $block->threads( $comments ) ?>

  <div class="contents">
    <?php if ( $block->layout()->isNotEmpty() ) {
      snippet( 'grid/index', [ 'rows' => $block->layout()->toLayouts() ] );
    } else {
      echo $block->highlightComments( $threads );
    } ?>
  </div>

  <?php snippet( 'comments/index', [
    'block'   => $block,
    'threads' => $threads
  ]) ?>

</section>
