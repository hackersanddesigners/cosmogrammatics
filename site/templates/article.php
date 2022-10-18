<?php snippet('head') ?>

<body class="<?= $page->template() ?>">

  <?php snippet('header') ?>

  <script>

  </script>

  <main id="main">
    <h1><?= $page->title()->html() ?></h1>
    <?php snippet('blocks') ?>
  </main>

  <?php snippet('footer') ?>

</body>
