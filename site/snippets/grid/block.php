<?php

  // page identifier used as class for styling
  $page_id = 'p_' . $page->slug();

  // if the block is an embed, make another <style> tag that
  // is scoped with the embedded pages' styles.
  if ( $block->type() == 'page_embed' ) {
      if ($link = $block->pageurl()->toLinkObject()) {
          if ($pageEmbed = page($link->value())) {
              $page_id   = 'p_' . $pageEmbed->slug();
              $page_skin = [
                  'colors' => $pageEmbed->colors()->toEntity(),
                  'fonts'  => $pageEmbed->fonts()->toEntity(),
                  'rules'  => $pageEmbed->css()->toStructure(),
                  'prefix' => $page_id
              ];
              snippet( 'style/tag', $page_skin );
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

  <div class="contents">
    <?php if ( $block->layout()->isNotEmpty() ) {
      snippet( 'grid/index', [ 'rows' => $block->layout()->toLayouts() ] );
    } else {
      echo $block;
    } ?>
  </div>

  <?php if ($page->intendedTemplate() == 'article'): ?>
  <aside>
      $threads = $block->threads( $comments );

      snippet( 'comments/index', [
        'block'   => $block,
        'threads' => $threads
      ])
    ?>
  </aside>
  <?php endif ?>

</section>
