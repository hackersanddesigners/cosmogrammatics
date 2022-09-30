<ul>
  <?php foreach ($page->children() as $comment): ?>
  <li>
    <a href="<?= $comment->url() ?>">
      <p><?= $comment->text() ?> / <?= $comment->user() ?></p>
    </a>
  </li>
  <?php endforeach ?>
</ul>
