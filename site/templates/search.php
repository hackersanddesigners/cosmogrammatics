<?php snippet('head') ?>

<body class="<?= $page->template() ?>">
  <?php snippet( 'header', ['query' => $query] ) ?>

  <main id="main">
    <h2 class="query_display">Search results for query "<i><?= $query ?></i>"</h2>
    <?php snippet('article-list' ) ?>
  </main>

  <?php snippet( 'footer' ) ?>
</body>
