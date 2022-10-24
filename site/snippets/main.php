<?php

  $data = [];
  $grid = [];

  foreach( $page->builder()->toLayouts() as $k_row ) {
    $row = [];

    foreach( $k_row->columns() as $k_column ) {
      $column = [];

      foreach ( $k_column->blocks() as $k_block ) {
        $block_comments   = [];
        $k_block_comments = NULL;

        if ( $comments->hasListedChildren() ) {
          $k_block_comments = $comments->children->listed()->filterBy( 'block_id', $k_block->id() );

          foreach ($k_block_comments as $comment) {
            $block_comments[] = [
              'time' => $comment->timestamp()->toDate('Y-m-d H:m:s'),
              'text' => $comment->text()->html(),
              'user' => $comment->user(),
              'b_id' => $comment->block_id()
            ];
          }

        }

        $column[] = [
          'id'      => $k_block->id(),
          'type'    => $k_block->type(),
          'text'    => $k_block->text(),
          'comments'=> $block_comments,
        ];

      }

      $row[] = [
        'width'  => $k_column->width(),
        'span'   => $k_column->span(),
        'blocks' => $column
      ];
    }

    $grid[] = $row;

  }

  $data[] = [
    'grid' => $grid
  ];


?>


<script>
  const data = <?= json_encode( $data, JSON_HEX_TAG); ?>
</script>

<!-- ¡ VUE STARTS HERE ! -->
<main id="main">
  <h1><?= $page->title()->html() ?></h1>
  <?php snippet( 'grid/index' ) ?>
  <Rows :rows="data[0].grid" />
</main>
<!-- ¡ VUE ENDS HERE ! -->
