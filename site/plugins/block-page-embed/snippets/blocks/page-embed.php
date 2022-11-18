<?php
  $link = $block->pageurl()->toLinkObject();
  if ($link && $pageEmbed = page($link->value())) {
?>
  <a
    href="<?= $pageEmbed->url() ?>"
    title="<?= $pageEmbed->title() ?>"
  >
    <?= $pageEmbed->image() ?>
    <h1><?= $pageEmbed->title()->html() ?></h1>
  </a>
<?php } ?>
