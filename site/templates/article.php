<?php snippet('head') ?>

<body class="<?= $page->template() ?>">

  <?php snippet('header') ?>

  <main id="main">
    <h1><?= $page->title()->html() ?></h1>
    <?php snippet( 'blocks/index' ) ?>
  </main>

  <?php snippet( 'footer' ) ?>

</body>
