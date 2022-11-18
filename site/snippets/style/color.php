<?php if ( $value->isNotEmpty() ) { ?>
  --<?= $key ?> : <?= $value->toColor( 'rgb' ) ?>;
<?php } ?>
