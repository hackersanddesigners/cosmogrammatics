<article>
  <header>
    <p><time datetime="<?= $comment->timestamp()->toDate('Y-m-d H:m:s') ?>">On <?= $comment->timestamp()->toDate('Y-m-d H:m:s') ?></time><p>
    <p>by <?= $comment->user()->html() ?></p>
    <!-- <a href="#<?= $comment->block_id() ?>">Show Block</a> -->
  </header>
  <section>
    <?= $comment->text()->html() ?>
  </section>
</article>
