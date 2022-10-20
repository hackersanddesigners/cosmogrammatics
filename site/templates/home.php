<?php snippet('head') ?>

<body class="<?= $page->template() ?>">

  <?php snippet('header') ?>

  <main id="main">
    <h2>Published articles:</h2>
    <ul>
    <?php foreach ($articles as $article): ?>
      <li>
        <a href="<?= $article->url() ?>">
          <?= $article->title()->html() ?>
        </a>
      </li>
      <?php endforeach ?>
    </ul>
  </main>

  <?php snippet('footer') ?>

</body>
