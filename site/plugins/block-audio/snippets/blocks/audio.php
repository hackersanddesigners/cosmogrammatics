<?php if ($file = $block->source_file()->toFile()): ?>
  <audio
    controls
    preload="metadata"
  >
    <source
      src="<?= $file->url() ?>"
      type="<?= $file->mime() ?>"
    />
    Your browser does not support the <code>audio</code> element.
  </audio>
  <h3><?= $block->title() ?></h3>
  <div class="caption"><?= $block->caption() ?></div>
<?php endif ?>
