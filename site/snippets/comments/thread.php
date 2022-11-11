<section class="thread">
  <?php
    var_dump( $thread['selection']['coords'] );
    foreach ($thread['comments'] as $comment) {
      snippet( 'comments/comment', [ 'comment' => $comment ] );
    }
    snippet( 'comments/add', [
      'block'          => $block,
      'selection_type' => $comments->first()->selection_type(),
      'selection_text' => $comments->first()->selection_text()
    ])
  ?>
</section>
