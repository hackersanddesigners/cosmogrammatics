<?php

/** @var \Kirby\Cms\Block $block */
$alt     = $block->alt();
$caption = $block->caption();
$crop    = $block->crop()->isTrue();
$link    = $block->link();
$ratio   = $block->ratio()->or('auto');
$src     = null;

if ($block->location() == 'web') {
  $src = $block->src()->resize(950)->esc();
} elseif ($image = $block->image()->toFile()) {
  $alt = $alt ?? $image->alt();
  $src = $image->resize(950)->url();
}

// <https://getkirby.com/docs/cookbook/performance/responsive-images>
$sizes = "(min-width: 1200px) 80vw,
  100vw";

$img_width = $block->resize(1800)->width();
// --

// -- is-file-embed
// check if current file
// is coming from another article
// if yes, link to the article

// unclear why i cannot wrap this in a func
// just here in this file in this place,
// it throws "cannot redeclare function"
// alles gut, oke.


$fileEmbed = null;

if ($src) {

  $blockParentID = $block->parent()->id();
  $imageID = $block->id();

  $file_tokens = explode('/', $imageID);
  array_pop($file_tokens);
  $articleID = implode('/', $file_tokens);

  if ($blockParentID != $articleID) {
    $fileEmbed = $articleID;
  }
}

?>

<?php if ($src): ?>
  <figure
    <?= Html::attr(['data-ratio' => $ratio, 'data-crop' => $crop], null, ' ') ?>
    class="<?= $fileEmbed ? 'orphan_image' : '' ?>"
  >
    <?php if ($link->isNotEmpty()): ?>
      <a href="<?= Str::esc($link->toUrl()) ?>">
	<img
	  preload="metadata"
	  alt="<?= $alt->esc() ?>"
	  src="<?= $src ?>"
	  srcset="<?= $block->srcset() ?>"
	  sizes="<?= $sizes ?>"
	  width="<?= $img_width ?>"
	  height="auto"
	>
      </a>
    <?php else: ?>
      <a href="<?= $src ?>">
	<img
	  preload="metadata"
	  alt="<?= $alt->esc() ?>"
	  src="<?= $src ?>"
	  srcset="<?= $block->srcset() ?>"
	  sizes="<?= $sizes ?>"
	  width="<?= $img_width ?>"
	  height="auto"
	>
      </a>
    <?php endif ?>

    <figcaption>
      <?php if ($caption->isNotEmpty()): ?>
	<p><?= $caption ?></p>
      <?php endif ?>
      <?php if ($fileEmbed): ?>
	This file originates in the cosmogram <a href="<?= url($fileEmbed) ?>"><i><?= page($fileEmbed)->title() ?></i>.</a>
      <?php endif ?>
    </figcaption>
  </figure>
<?php endif ?>
