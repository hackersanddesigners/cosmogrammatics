<?php snippet('head') ?>

<body class="<?= $page->template() ?>">
  <?php snippet('header') ?>
  <main id="main">
    <div class="content-wrapper">
      <?php snippet( 'grid/title' ) ?>
      <?php snippet( 'grid/index', [ 'rows' => $page->builder()->toLayouts() ] ) ?>
      <?php snippet( 'footnotes' ) ?>
    </div>
  </main>
  <?php snippet( 'footer' ) ?>
</body>
