<article tabindex="0">
  <section>
    <?= $comment->text()->html() ?>
  </section>
  <footer class="small_italic">
    <p><time datetime="<?= $comment->timestamp()->toDate('Y-m-d H:i:s') ?>">On <?= $comment->timestamp()->toDate('Y-m-d H:i:s') ?></time><p>
    <p>by <?= $comment->user()->html() ?></p>
    <!-- <a href="#<?= $comment->block_id() ?>">Show Block</a> -->
  </footer>
</article>
