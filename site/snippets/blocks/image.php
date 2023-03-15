<?php

/** @var \Kirby\Cms\Block $block */
$alt     = $block->alt();
$caption = $block->caption();
$crop    = $block->crop()->isTrue();
$link    = $block->link();
$ratio   = $block->ratio()->or('auto');
$src     = null;

if ($block->location() == 'web') {
    $src = $block->src()->esc();
} elseif ($image = $block->image()->toFile()) {
    $alt = $alt ?? $image->alt();
    $src = $image->url();
}


// -- is-file-embed
// check if current file
// is coming from another article
// if yes, link to the article

// unclear why i cannot wrap this in a func
// just here in this file in this place,
// it throws "cannot redeclare function"
// alles gut, oke.

$fileEmbed = null;
$blockParentID = $block->parent()->id();
$imageID = $image->id();

$file_tokens = explode('/', $imageID);
array_pop($file_tokens);
$articleID = implode('/', $file_tokens);

if ($blockParentID != $articleID) {
    $fileEmbed = $articleID;
}

?>
<?php if ($src): ?>
<figure
  <?= Html::attr(['data-ratio' => $ratio, 'data-crop' => $crop], null, ' ') ?>
  class="<?= $fileEmbed ? 'orphan_image' : '' ?>"
>
  <?php if ($link->isNotEmpty()): ?>
  <a href="<?= Str::esc($link->toUrl()) ?>">
    <img preload="metadata" src="<?= $src ?>" alt="<?= $alt->esc() ?>">
  </a>
  <?php else: ?>
  <img preload="metadata" src="<?= $src ?>" alt="<?= $alt->esc() ?>">
  <?php endif ?>

  <figcaption>
    <?php if ($caption->isNotEmpty()): ?>
      <p><?= $caption ?></p>
    <?php endif ?>
    <?php if ($fileEmbed): ?>
      <a href="<?= url($fileEmbed) ?>">This files originates in the cosmogram <i><b><?= page($fileEmbed)->title() ?></b></i>.</a>
    <?php endif ?>
  </figcaption>
</figure>
<?php endif ?>
