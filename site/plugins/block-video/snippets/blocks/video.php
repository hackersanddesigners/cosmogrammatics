<?php if ($file = $block->source_file()->toFile()): ?>
  <video
    controls
    preload="metadata"
  >
     <source
      src="<?= $file->url() ?>"
      type="<?= $file->mime() ?>"
    >
    Your browser does not support the <code>video</code> element.
  </video>
  <h3><?= $block->title() ?></h3>
  <div class="caption"><?= $block->caption() ?></div>
<?php endif ?>
