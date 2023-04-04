<?php

return function ($site) {

  $query = get('q');
  $results = $site->index()->listed()->search($query);

  return [
    'query' => $query,
    'results' => $results,
  ];

};
