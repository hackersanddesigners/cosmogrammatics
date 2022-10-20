<?php snippet('head') ?>

<body class="<?= $page->template() ?>">

  <?php snippet('header') ?>

  <main id="main">
    <h1><?= $page->title()->html() ?></h1>
    <?php snippet( 'blocks/index' ) ?>
  </main>

  // leaving this here so you can integrate it however you like
  // see <https://getkirby.com/docs/reference/panel/fields/layout>
  //
  // <?php foreach ($page->builder()->toLayouts() as $layout): ?>
  // <section data-bg-image="" data-bg-color="">
  //   <?php foreach ($layout->columns() as $column): ?>
  //     <div class="blocks" data-col-span="<?= $column->span() ?>" data-col-width="<?= $column->width() ?>">
  //       <?php snippet('blocks', ['column' => $column]) ?>
  //     </div>
  //   <?php endforeach ?>
  // </section>
  // <?php endforeach ?>

  <?php snippet( 'footer' ) ?>

</body>
