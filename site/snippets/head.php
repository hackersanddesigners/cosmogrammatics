<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <link href=data:, rel=icon>

  <title>
    <?= $site->title()->html() ?>
    <?= e($page->template() != 'home', ' / ' . $page->title()->html())?>
  </title>

  <meta name="description" content="">

  <?php
    if ($page->intendedTemplate() == 'article') {
      echo Bnomei\Fingerprint::css('assets/css/templates/article.css');
    };

    if ($page->intendedTemplate() == 'home') {
      echo Bnomei\Fingerprint::css('assets/css/templates/home.css');
    };

    if ($page->intendedTemplate() == 'default') {
      echo Bnomei\Fingerprint::css('assets/css/templates/default.css');
    };


    echo Bnomei\Fingerprint::css('assets/css/base.css');
  ?>

  <?php if ( isset( $skin ) ) {
    snippet( 'style/tag', [
      'colors' => $skin[ 'colors' ],
      'fonts'  => $skin[ 'fonts' ],
      'rules'  => $skin[ 'rules' ]
    ]);
  } ?>

</head>
