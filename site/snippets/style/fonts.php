<?php

foreach( $font_names as $key ) {
  snippet( 'style/font', [
    'key'    => $key,
    'value'  => $fonts->{ $key }()->toFile(),
  ] );
}
