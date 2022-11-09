<?php
  $file = $page->fonts()->toEntity()->{ $font }()->toFile();
  if ( $file ) {
?>
  @font-face {
    font-family: <?= $font ?>;
    src: url( <?= $file->url() ?> );
  }
<?php } ?>
