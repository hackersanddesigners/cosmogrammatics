<?php snippet('head') ?>

<body class="<?= $page->template() ?>">

  <?php snippet('header') ?>

  <main id="main">
    <div class="row">
      <div class="column">
        <div class="block">
          <div class="contents">
            <p class="stats"><?= size( $comments->children() ) ?> comments</h5>
            <h1><?= $page->title()->html() ?></h1>
          </div>
        </div>
      </div>
    </div>
    <?php snippet( 'grid/index' ) ?>
  </main>

  <?php snippet( 'footer' ) ?>

</body>
