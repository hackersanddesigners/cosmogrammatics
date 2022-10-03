<?php foreach ($comments->children->listed() as $comment): ?>
<div>
  <div>
    <time datetime="<?= $comment->timestamp()->toDate('Y-m-d H:m:s') ?>">On <?= $comment->timestamp()->toDate('Y-m-d H:m:s') ?></time> 
    <span>by <?= $comment->user()->html() ?></span>
    <a href="#<?= $comment->block_id() ?>">Show Block</a>
  </div>
  <div><?= $comment->text()->html() ?></div>
</div>
<?php endforeach ?>
