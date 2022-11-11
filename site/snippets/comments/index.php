<?php if ($comments && $comments->count() > 0): ?>
  <aside>
    <?php
      $non_threaded_thread = $comments->filterBy('selection_type', '==', 'NULL');
      if ( $non_threaded_thread && $non_threaded_thread->count() > 0 ) {
        snippet( 'comments/thread', [
          'thread'         => [ 'comments' => $non_threaded_thread ],
          'block'          => $block,
          'selection_type' => $non_threaded_thread->first()->selection_type(),
          'selection_text' => $non_threaded_thread->first()->selection_text()
        ]);
      }
    ?>
    <?php foreach( $threads as $thread ) {
        snippet( 'comments/thread', [
          'thread'         => $thread,
          'block'          => $block,
          'selection_type' => $comments->first()->selection_type(),
          'selection_text' => $comments->first()->selection_text()
        ]);
      } ?>
  </aside>
<?php endif ?>
