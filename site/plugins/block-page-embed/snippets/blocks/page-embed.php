<?php
  $link = $block->pageurl()->toLinkObject();
  if ($link && $pageEmbed = page($link->value())) {
    snippet( 'article-list-item', [ 'article' => $pageEmbed ] ) ;
  }
?>
