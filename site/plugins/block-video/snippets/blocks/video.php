<?php if ($file = $block->source_file()->toFile()): ?>
<div class="video-wrapper">
  <video controls>
     <source src="<?= $file->url() ?>" type="<?= $file->mime() ?>">
    Your browser does not support the <code>video</code> element.
  </video> 
  <div class="video-info">
    <h3><?= $block->title()->html() ?></h3>
    <div><?= $block->caption()->html() ?></div>
  </div>
</div>
<?php endif ?>
