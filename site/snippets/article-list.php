<ul class="article-list">
  <?php foreach ( $articles as $article ): ?>
    <li>
      <?php snippet( 'article-link', [ 'article' => $article ] ) ?>
    </li>
  <?php endforeach ?>
</ul>