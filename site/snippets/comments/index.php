<aside>
  <?php
    $non_threaded_comments = $comments->filterBy('selection_coords', '==', 'NULL');
    $non_threaded_thread = [
      'selection_type'   => NULL,
      'selection_coords' => NULL,
      'comments'         => $non_threaded_comments
    ];
    snippet( 'comments/thread', [
      'thread'         => $non_threaded_thread,
      'block'          => $block,
    ]);
    foreach( $threads as $thread ) {
      snippet( 'comments/thread', [
        'thread'         => $thread,
        'block'          => $block,
      ]);
    }
  ?>
</aside>
