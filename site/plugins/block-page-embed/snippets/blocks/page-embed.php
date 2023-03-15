<?php
  $link = $block->pageurl()->toLinkObject();
  if ($link && $pageEmbed = page($link->value())) {
    snippet( 'article-link', [ 'article' => $pageEmbed ] ) ;
  }
?>
