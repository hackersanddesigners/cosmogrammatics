<?php

  // article identifier used as class for styling
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

  <?php if ($authors = $article->authors()): ?>
    <div class="article-authors">
    <?php foreach ($authors->split(',') as $author): ?>
      <span class="article-author"><?= $author ?></span>
    <?php endforeach ?>
    </div>
  <?php endif ?>

  <?php if ($comments = $article->children()->children()->listed() ): ?>
    <div class="stats">
      <p><span id="comment_count">
        <?= $comments->count() ?></span> comments
     </p>
    </div>
  <?php endif ?>

</a>
