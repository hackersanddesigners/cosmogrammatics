<?php snippet('header') ?>

<main>
  <article>
    <head>
      <h1><?= $page->title()->html() ?></h1>
    </head>

    <?php snippet('blocks') ?>

    <!-- TODO could wrap all this logic inside comments -->
    <?php if($comments->hasListedChildren()): ?>
    <aside>
      <h3>Comments</h3>
      <?php snippet('comments') ?>
    </aside>
    <?php endif ?>
  </article>
</main>


<?php snippet('footer') ?>
