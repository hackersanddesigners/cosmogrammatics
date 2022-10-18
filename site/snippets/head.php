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

  <?= css('assets/css/styles.css') ?>
</head>