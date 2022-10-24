<article tabindex="0">
  <section
    @click="notify( `<?= 'block id' . $comment->block_id() ?>` )"
  >
    <?= $comment->text()->html() ?>
  </section>
  <footer>
    <p><time datetime="<?= $comment->timestamp()->toDate('Y-m-d H:m:s') ?>">On <?= $comment->timestamp()->toDate('Y-m-d H:m:s') ?></time><p>
    <p>by <?= $comment->user()->html() ?></p>
    <!-- <a href="#<?= $comment->block_id() ?>">Show Block</a> -->
  </footer>
</article>
