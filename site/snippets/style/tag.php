<?php
  $font_names  = [ 'title', 'body', 'comments', 'footnotes' ];
  $color_names = [ 'back', 'fore', 'accent', 'grid' ];
  $prefix      = isset( $prefix ) ? $prefix : NULL;
  $selector    = $prefix ? '.' . $prefix : ':root';
?>

<style>

  <?php snippet( 'style/font-imports', [
    'font_names' => $font_names,
    'fonts'      => $fonts,
  ]) ?>

  <?= $selector ?> {

    <?php
      snippet( 'style/fonts', [
        'font_names' => $font_names,
        'fonts'      => $fonts,
      ]);
      snippet( 'style/colors', [
        'color_names' => $color_names,
        'colors'      => $colors,
      ]);
    ?>

  }

  <?php snippet( 'style/rules', [
    'rules'    => $rules,
    'selector' => $selector,
  ]) ?>

</style>
