<?php

  // page identifier used as class for styling

  $page_id = 'p_' . $page->slug();

  // if the block is an embed, make another <style> tag that
  // is scoped with the embedded pages' styles.

  if ( $block->type() == 'page_embed' ) {
      if ($link = $block->pageurl()->toLinkObject()) {
          if ($pageEmbed = page($link->value())) {
              $page_id = 'p_' . $pageEmbed->slug();
              snippet( 'style/tag', [
                'colors' => $pageEmbed->colors()->toEntity(),
                'fonts'  => $pageEmbed->fonts()->toEntity(),
                'rules'  => $pageEmbed->css()->toStructure(),
                'prefix' => $page_id
            ] );
          }
      }
  }
?>

<section
  tabindex="0"
  class="block <?= $block->type() ?>  <?= $page_id ?>"
  id="<?= $block->bid() ?>"
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

  <aside>
    <?php snippet( 'comments/index', [
      'block'     => $block,
      'threads'   => $threads
    ]) ?>
    <?php snippet( 'footnotes/index', [
      'footnotes' => $block->footnotes()->toStructure()
    ]) ?>
  </aside>

</section>
