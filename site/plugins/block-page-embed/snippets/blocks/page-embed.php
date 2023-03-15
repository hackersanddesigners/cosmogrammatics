// TODO replace this with the article list item element


<?php
  $link = $block->pageurl()->toLinkObject();
  if ($link && $pageEmbed = page($link->value())) {
?>
  <a
    href="<?= $pageEmbed->url() ?>"
    title="<?= $pageEmbed->title() ?>"
    class="article-link"
  >
    <?= $pageEmbed->image() ?>
    <h1><?= $pageEmbed->title()->html() ?></h1>
  </a>
<?php } ?>
