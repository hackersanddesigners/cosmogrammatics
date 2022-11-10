<?php if ( $comments && count($comments) !== 0 ) { ?>
  <aside>

    <?php
      foreach ($comments as $comment) {
        snippet( 'comments/comment', [ 'comment' => $comment ] );
      }
      snippet( 'comments/add', [
        'block'          => $block,
        'selection_type' => $comments->first()->selection_type(),
        'selection_text' => $comments->first()->selection_text()
      ])
    ?>

  </aside>
<?php } ?>
