<?php if ( count($comments) !== 0 ) { ?>
  <aside>

    <?php foreach ($comments as $comment) {
      snippet( 'comments/comment', [ 'comment' => $comment ] );
    } ?>

    <?php snippet( 'comments/form' ) ?>

  </aside>
<?php } ?>
