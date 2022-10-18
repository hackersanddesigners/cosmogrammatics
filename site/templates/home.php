<?php snippet('head') ?>

<body class="<?= $page->template() ?>">

  <?php snippet('header') ?>

  <main id="main">
    <?php foreach ($articles as $article): ?>
    <p>Published articles:</p>
    <ul>
      <li>
        <a href="<?= $article->url() ?>">
          <?= $article->title()->html() ?>
        </a>
      </li>
    </ul>
    <?php endforeach ?>
  </main>

  <?php snippet('footer') ?>

</body>
