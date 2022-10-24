<?php if ( count($comments) !== 0 ) { ?>
  <aside>

    <?php



      foreach ($comments as $comment) {
        snippet( 'comments/comment', [ 'comment' => $comment ] );
      }

      snippet( 'comments/form', [
        'block'     => $block,
        // 'selection' => $selection
      ] )

    ?>

  </aside>
<?php } ?>
