<?php snippet('head/index') ?>

<body class="<?= $page->template() ?>">

  <?php snippet('header') ?>

  <main id="main">
    <?php snippet( 'grid/title' ) ?>
    <?php snippet( 'grid/index', [ 'rows' => $page->builder()->toLayouts() ] ) ?>
  </main>

  <?php snippet( 'footer' ) ?>

</body>
