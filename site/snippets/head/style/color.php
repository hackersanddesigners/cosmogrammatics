<?php
  $color_value = $page->colors()->toEntity()->{ $color }();
  if ( $color_value->isNotEmpty() ) {
?>
  --<?= $color ?> : <?= $page->colors()->toEntity()->{ $color }()->toColor( 'rgb' ) ?>;
<?php } ?>
