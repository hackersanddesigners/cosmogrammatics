<?php
  $link = $block->pageurl()->toLinkObject();
  if ($link && $pageEmbed = page($link->value())) {
    $page_id   = 'p_' . $pageEmbed->slug();
    $page_skin = [
      'colors' => $pageEmbed->colors()->toEntity(),
      'fonts'  => $pageEmbed->fonts()->toEntity(),
      'rules'  => $pageEmbed->css()->toStructure(),
      'prefix' => $page_id
    ];
    snippet( 'style/tag', $page_skin );
?>

  <div
    class="page-embed block"
    id="<?= $page_id ?>"
  >
    <a
      href="<?= $pageEmbed->url() ?>"
      title="<?= $pageEmbed->title() ?>"
    >
      <h1><?= $pageEmbed->title()->html() ?></h1>
    </a>
  </div>
<?php } ?>
