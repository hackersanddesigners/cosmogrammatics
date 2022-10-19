<?php foreach ($column->blocks() as $block): ?>
<div id="<?= $block->id() ?>" data-type="block-<?= $block->type() ?>">
  <?= $block ?>
</div>
<?php endforeach ?>
