<article tabindex="0">
  <section>
    <?= $comment->text() ?>
  </section>
  <footer class="small_italic">
    <p><time datetime="<?= $comment->timestamp()->toDate('Y-m-d H:i:s') ?>">On <?= $comment->timestamp()->toDate('d-m-Y H:i:s') ?></time><p>
    <p>by <?= $comment->user() ?></p>
    <!-- <a href="#<?= $comment->block_id() ?>">Show Block</a> -->
  </footer>
</article>
