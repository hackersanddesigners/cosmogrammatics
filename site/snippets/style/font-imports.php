<?php

foreach( $font_names as $key ) {
  snippet( 'style/font-import', [
    'value'  => $fonts->{ $key }()->toFile(),
  ] );
}
