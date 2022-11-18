<?php if ( $value ) { ?>
  @font-face {
    font-family: <?= $value->name() ?>;
    src: url( <?= $value->url() ?> );
  }
<?php } ?>
