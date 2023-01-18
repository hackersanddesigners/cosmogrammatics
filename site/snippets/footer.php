<footer>
  <?php if ($page->intendedTemplate() == 'article') {
    echo js([ 'assets/js/templates/article.min.js?v=7ede2fb' ], [ 'type'  => 'module', 'async' => true ]);
  } ?>

  <p class="copyright"><?= $site->copyright()->html() ?></p>
</footer>
