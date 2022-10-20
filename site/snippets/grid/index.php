<?php foreach ( $page->builder()->toLayouts() as $row ) {
  snippet( 'grid/row', [ 'row' => $row ] );
} ?>
