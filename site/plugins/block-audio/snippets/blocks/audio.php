<?php if ($file = $block->source_file()->toFile()): ?>
<div class="audio-wrapper">
  <audio controls>
     <source src="<?= $file->url() ?>" type="<?= $file->mime() ?>">
    Your browser does not support the <code>audio</code> element.
  </audio> 
  <div class="audio-info">
    <h3><?= $block->title()->html() ?></h3>
    <div><?= $block->caption()->html() ?></div>
  </div>
</div>
<?php elseif ($url = $block->source_url()): ?>
<div class="audio-wrapper">
  <div class="iframe-wrapper"><?= $url ?></div>
  <div class="audio-info">
    <h3><?= $block->title()->html() ?></h3>
    <div><?= $block->caption()->html() ?></div>
  </div>
</div>
<?php endif ?>
