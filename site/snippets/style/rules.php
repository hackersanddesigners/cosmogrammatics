<?php

foreach ( $rules as $rule ) {
  snippet( 'style/rule', [
    'rule'     => $rule,
    'selector' => $selector
  ]);
}
