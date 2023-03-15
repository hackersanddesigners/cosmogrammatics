<div class="row">
  <div class="column">
    <div class="block block-title" tabindex="0">
      <div class="contents">
        <h1><?= $page->title()->html() ?></h1>
        <?php if ( $authors = $page->authors() ) {
          snippet( 'grid/authors', [ 'authors' => $authors ] );
        } ?>
      </div>
    </div>
  </div>
</div>
