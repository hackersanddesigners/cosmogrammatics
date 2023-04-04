<?php snippet( 'head' ) ?>

<body class="<?= $page->template() ?>">

 <?php snippet( 'header', ['query' => $query] ) ?>

  <main id="main">
    <?php snippet('article-list' ) ?>
  </main>

  <?php snippet( 'footer' ) ?>

</body>
