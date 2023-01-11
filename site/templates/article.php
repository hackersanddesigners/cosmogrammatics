<?php snippet('head') ?>

<body class="<?= $page->template() ?>">

  <?php snippet('header') ?>

  <main id="main">
    <?php snippet( 'grid/title' ) ?>
    <?php snippet( 'grid/index', [ 'rows' => $page->builder()->toLayouts() ] ) ?>
    <?php snippet( 'footnotes' ) ?>
    <?php snippet( 'utils/selection-toolbar' ) ?>
    <?php snippet( 'comments/alert', [ 'alert' => $alert ] ) ?>
    <?php snippet( 'comments/review-list', [ 'comments' => $comments ] ) ?>
  </main>

  <?php snippet( 'footer' ) ?>

</body>
