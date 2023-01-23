<?php snippet('head') ?>

<body class="<?= $page->template() ?>">
  <?php snippet('header') ?>

  <main id="main">
    <div class="view-comment-options-wrapper">
      <?php snippet( 'grid/stats' ) ?>
      <?php snippet( 'comments/review-list' ) ?>
    </div>
    <div class="content-wrapper">
      <?php snippet( 'grid/title' ) ?>
      <?php snippet( 'grid/index', [ 'rows' => $page->builder()->toLayouts() ] ) ?>
      <?php snippet( 'footnotes' ) ?>
      <?php snippet( 'utils/selection-toolbar' ) ?>
      <?php snippet( 'comments/alert', [ 'alert' => $alert ] ) ?>
    </div>
  </main>

  <?php snippet( 'footer' ) ?>
  <div class="focus-overlay"></div>

</body>
