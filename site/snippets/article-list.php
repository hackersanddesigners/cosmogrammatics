<ul class="article-list">
  <?php foreach ( $articles as $article ): ?>
    <li>
      <?php snippet( 'article-list-item', [ 'article' => $article ] ) ?>
    </li>
  <?php endforeach ?>
</ul>