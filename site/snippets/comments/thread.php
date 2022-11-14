<section class="thread">
  <?php

    $thread = $thread ?? [
      'selection_type'   => NULL,
      'selection_coords' => NULL,
      'comments'         => []
    ];

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
