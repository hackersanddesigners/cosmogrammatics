<?php foreach ($page->builder()->toBlocks() as $block): ?>
<div data-id="<?= $block->id() ?>" data-type="block-<?= $block->type() ?>">
  <?= $block ?>
</div>
<?php endforeach ?>
