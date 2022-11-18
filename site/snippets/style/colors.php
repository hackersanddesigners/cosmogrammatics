<?php

foreach( $color_names as $key ) {
  snippet( 'style/color', [
    'key'    => $key,
    'value'  => $colors->{ $key }()
  ] );
}
