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
    $src = $block_image->src()->esc();
} elseif ($image = $block_image->image()->toFile()) {
    $alt = $alt ?? $image->alt();
    $src = $image->url();
}

$fileEmbed = null;
if ($src) {
  $blockParentID = $block_image->parent()->id();
  $imageID = $image->id();

  $file_tokens = explode('/', $imageID);
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
    <?php if ($link->isNotEmpty()): ?>
      <img preload="metadata" src="<?= $src ?>" alt="<?= $alt->esc() ?>">
    <?php else: ?>
      <img preload="metadata" src="<?= $src ?>" alt="<?= $alt->esc() ?>">
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
