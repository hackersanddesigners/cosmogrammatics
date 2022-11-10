<?php if ($comments && $comments->count() > 0) { ?>
  <aside>

    <?php
      foreach ($comments as $comment) {
        snippet( 'comments/comment', [ 'comment' => $comment ] );
      }

      snippet( 'comments/add', [
        'block'     => $block,
        // 'selection' => $selection
      ] )
    ?>

  </aside>
<?php } ?>
