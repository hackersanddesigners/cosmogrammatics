<?php if ($embed = $block->source_url()->toEmbed()): ?>
<div class="embed-wrapper">
  <div class="block-embed-container">
    <div class="block-embed-preview"><?= $embed->code() ?></div>
    <div class="block-embed-preview-background"></div>
  </div>
  <div class="embed-info">
    <h3><?= $block->title()->html() ?></h3>
    <div><?= $block->caption()->html() ?></div>
  </div>
</div>
<?php endif ?>
