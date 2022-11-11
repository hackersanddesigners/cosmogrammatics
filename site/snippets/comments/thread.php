<section class="thread">
  <?php
    foreach ($thread['comments'] as $comment) {
      snippet( 'comments/comment', [ 'comment' => $comment ] );
    }
    snippet( 'comments/add', [
      'block'            => $block,
      'selection_type'   => $thread['selection_type'],
      'selection_coords' => $thread['selection_coords']
    ])
  ?>
</section>
