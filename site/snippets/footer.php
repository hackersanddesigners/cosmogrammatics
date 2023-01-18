<footer>
  <?php if ($page->intendedTemplate() == 'article') {
    echo Bnomei\Fingerprint::js('assets/js/templates/article.min.js', ['type' => 'module', 'async' => true]);
  } ?>

  <p class="copyright"><?= $site->copyright()->html() ?></p>
</footer>
