<?php

$font_names  = [ 'title', 'body', 'comments', 'footnotes' ];
$color_names = [ 'back', 'fore', 'accent', 'grid' ];

echo '<style>';

foreach( $font_names as $font ) {
  echo snippet( 'head/style/font', [ 'font' => $font ] );
}

echo ':root {';

foreach( $color_names as $color ) {
  echo snippet( 'head/style/color', [ 'color' => $color ] );
}

echo '}';
echo '</style>';
