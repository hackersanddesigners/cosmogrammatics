<?php if ($comments && $comments->count() > 0): ?>
  <aside>
    <?php foreach( $threads as $thread ): ?>
      <?= var_dump($thread) ?>
      <section class="thread">
        <?php
          foreach ($thread->$comments as $comment) {
            snippet( 'comments/comment', [ 'comment' => $comment ] );
          }
         snippet( 'comments/add', [
            'block'          => $block,
            'selection_type' => $comments->first()->selection_type(),
            'selection_text' => $comments->first()->selection_text()
          ])
        ?>
      </section>
    <?php endforeach ?>
  </aside>
<?php endif ?>
