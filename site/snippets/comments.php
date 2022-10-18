<?php if ( count($comments) !== 0 ) { ?>
  <aside>
    <?php foreach ($comments as $comment) {
      snippet( 'comment', [ 'comment' => $comment ] );
    } ?>
  </aside>
<?php } ?>
