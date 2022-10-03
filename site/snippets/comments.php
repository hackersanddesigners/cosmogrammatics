<?php foreach ($comments->children->listed() as $comment): ?>
<div>
  <time datetime="<?= $comment->timestamp()->toDate('Y-m-d H:m:s') ?>"><?= $comment->timestamp()->toDate('Y-m-d H:m:s') ?></time> 
  <div><?= $comment->user()->html() ?></div>
  <div><?= $comment->text()->html() ?></div>
</div>
<?php endforeach ?>
