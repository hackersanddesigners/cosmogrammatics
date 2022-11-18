<?php snippet('head') ?>

<body class="<?= $page->template() ?>">

  <?php snippet('header') ?>

  <main id="main">
    <?php snippet( 'grid/title' ) ?>
    <?php snippet( 'grid/index', [ 'rows' => $page->builder()->toLayouts() ] ) ?>
    <?php snippet( 'footnotes' ) ?>
    <?php snippet( 'utils/selection-toolbar' ) ?>
  </main>

  <?php snippet( 'footer' ) ?>

</body>
