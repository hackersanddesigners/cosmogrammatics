<?php snippet('header') ?>

<main>
  <article>
    <head>
      <h1><?= $page->title()->html() ?></h1>
    </head>

    <?php foreach ($page->builder()->toLayouts() as $layout): ?>
    <section data-bg-image="" data-bg-color="">
      <?php foreach ($layout->columns() as $column): ?>
        <div class="blocks" data-col-span="<?= $column->span() ?>" data-col-width="<?= $column->width() ?>">
          <?php snippet('blocks', ['column' => $column]) ?>
        </div>
      <?php endforeach ?>
    </section>
    <?php endforeach ?>

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
