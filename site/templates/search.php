<?php snippet('head') ?>

<body class="<?= $page->template() ?>">
  <?php snippet( 'header' ) ?>

  <main id="main">
    <ul>
      <?php foreach($results as $result): ?>
        <li>
          <a href="<?= $result->url() ?>">
            <?= $result->title() ?>
          </a>
        </li>
      <?php endforeach ?>
    </ul>
  </main>

  <?php snippet( 'footer' ) ?>
</body>
