<?php foreach ( $page->builder()->toBlocks() as $block ) {
  snippet( 'blocks/block', [ 'block' => $block ] ) ;
} ?>
