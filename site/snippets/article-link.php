<?php

  $id   = 'a_' . $article->slug();
  $skin = [
    'colors' => $article->colors()->toEntity(),
    'fonts'  => $article->fonts()->toEntity(),
    'rules'  => $article->css()->toStructure(),
    'prefix' => $id
  ];
  snippet( 'style/tag', $skin );

?>

<a
  class="article-link <?= $id ?>"
  href="<?= $article->url() ?>"
  title="<?= $article->title() ?>"
>

  <?= $article->image() ?>

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
