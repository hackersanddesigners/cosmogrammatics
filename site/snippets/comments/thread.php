<section class="thread">
  <?php

    $thread = $thread ?? [
      'selection_type'   => NULL,
      'comments'         => []
    ];

    foreach ($thread['comments'] as $comment) {
      snippet( 'comments/comment', [ 'comment' => $comment ]);
    }

    snippet( 'comments/add', [
      'block'            => $block,
      'selection_type'   => $block->type()
    ])
  ?>
</section>
