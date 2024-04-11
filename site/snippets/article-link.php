<?php

$id   = 'a_' . $article->slug();
$skin = [
  'colors' => $article->colors()->toEntity(),
  'fonts'  => $article->fonts()->toEntity(),
  'rules'  => $article->css()->toStructure(),
  'prefix' => $id
];
snippet( 'style/tag', $skin );

// we need to get the first image from the builder
// instead of simply using $article->image();
// ugly but we're redeclaring a bunch of functions
// because i won't learn PHP anymore.

$block_image = $article->builder()->toBlocks()->filterBy('type', 'image')->first();
$alt     = $block_image->alt();
$caption = $block_image->caption();
$crop    = $block_image->crop()->isTrue();
$link    = $block_image->link();
$ratio   = $block_image->ratio()->or('auto');
$src = null;

if ($block_image->location() == 'web') {
  $src = $block_image->src()->resize(400)->esc();
} elseif ($block_image = $block_image->image()->toFile()) {
  $alt = $alt ?? $block_image->alt();
  $src = $block_image->resize(400)->url();
}

// <https://getkirby.com/docs/cookbook/performance/responsive-images>
$sizes = "(min-width: 1200px) 30vw,
  (min-width: 800px) 50vw,
  100vw";

$img_width = $block_image->resize(1800)->width();
// --

$fileEmbed = null;
if ($src) {
  $blockParentID = $block_image->parent()->id();
  $block_imageID = $block_image->id();

  $file_tokens = explode('/', $block_imageID);
  array_pop($file_tokens);
  $articleID = implode('/', $file_tokens);

  if ($blockParentID != $articleID) {
    $fileEmbed = $articleID;
  }
}

?>

<a
  class="article-link <?= $id ?>"
  href="<?= $article->url() ?>"
  title="<?= $article->title() ?>"
>

  <?php if ($src): ?>
    <figure
      <?= Html::attr(['data-ratio' => $ratio, 'data-crop' => $crop], null, ' ') ?>
      class="<?= $fileEmbed ? 'orphan_image' : '' ?>"
    >
      <picture>
        <source
          srcset="<?= $block_image->srcset('avif') ?>"
          sizes="<?= $sizes ?>"
          type="image/avif"
        >
        <source
          srcset="<?= $block_image->srcset('webp') ?>"
          sizes="<?= $sizes ?>"
          type="image/webp"
        >
	<?php if ($link->isNotEmpty()): ?>
	  <img
	    preload="metadata"
	    alt="<?= $alt->esc() ?>"
	    src="<?= $src ?>"
	    srcset="<?= $block_image->srcset() ?>"
	    sizes="<?= $sizes ?>"
	    width="<?= $img_width ?>"
	    height="<?= $img_height ?>"
	  >
	<?php else: ?>
	  <img
	    preload="metadata"
	    alt="<?= $alt->esc() ?>"
	    src="<?= $src ?>"
	    srcset="<?= $block_image->srcset() ?>"
	    sizes="<?= $sizes ?>"
	    width="<?= $img_width ?>"
	  >
      </picture>
	<?php endif ?>
    </figure>
  <?php endif ?>
  
  <h1><?= $article->title()->html() ?></h1>

  <?php
  if ( $authors = $article->authors() ) {
    snippet( 'grid/authors', [ 'authors' => $authors ] );
  }
  if ( $comments = $article->children()->children()->listed() ) {
    snippet( 'comments/count', [ 'comments' => $comments ] );
  }
  ?>

</a>
