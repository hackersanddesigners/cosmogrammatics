<section
  tabindex="0"
  class="block <?= $block->type() ?>"
  id="<?= $block->bid() ?>"
  data-type="block-<?= $block->type() ?>"
>

  <div class="contents">
    <?php if ( $block->layout()->isNotEmpty() ) {
      snippet( 'grid/index', [ 'rows' => $block->layout()->toLayouts() ] );
    } else {
      echo $block;
    } ?>
  </div>

  <?php if ($page->intendedTemplate() == 'article'): ?>
    <aside>
      <?php
        snippet( 'utils/click2copy', [ 'url' => '#' . $block->bid() ] );
        snippet( 'comments/index', [
          'block'   => $block,
          'threads' => $block->threads( $comments )
        ]);
      ?>
    </aside>
  <?php endif ?>

</section>
