<section
  class="row"
  data-bg-image=""
  data-bg-color=""
>
  <?php foreach ($row->columns() as $column) {
    snippet( 'grid/column', [ 'column' => $column ] ) ;
  } ?>
</section>
