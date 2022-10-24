<?php

return function ($kirby, $page) {

  // if the form has been submitted…

  echo( "hii" );

  if ( $kirby->request()->is( 'POST' )) {
    $data = [
      'body'   => get('body'),
      'author' => get('author')
    ];
    var_dump( $data );
  }

  return [
    'data'  => $data ?? false
  ];
}

?>
