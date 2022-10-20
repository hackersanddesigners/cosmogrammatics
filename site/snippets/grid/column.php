<section
  class="column"
  data-col-span="<?= $column->span() ?>"
  data-col-width="<?= $column->width() ?>"
>
  <?php foreach ( $column->blocks() as $block ) {
    snippet( 'grid/block', [ 'block' => $block ] );
  } ?>
</section>
