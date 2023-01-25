<div class="row">
  <div class="column">
    <div class="block block-title" tabindex="0">
      <div class="contents">
        <h1><?= $page->title()->html() ?></h1>
        <?php if ($authors = $page->authors()): ?>
          <div class="article-authors">
          <?php foreach ($authors->split(',') as $author): ?>
            <span class="article-author"><?= $author ?></span>
          <?php endforeach ?>
          </div>
        <?php endif ?>
      </div>
    </div>
  </div>
</div>
