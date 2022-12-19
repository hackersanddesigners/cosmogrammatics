<section class="comments">
  <?php

  // If the block does not have any comments yet, we create
  // one empy thread to hold the comment/add form and any
  // comments that will be added in during browser session.

  if ( empty( $threads ) ) {
    snippet( 'comments/thread', [
      'block'  => $block,
      'thread' => NULL,
    ]);
  }

  foreach( $threads as $thread ) {
    snippet( 'comments/thread', [
      'block'  => $block,
      'thread' => $thread,
    ]);
  }

  ?>
</section>
