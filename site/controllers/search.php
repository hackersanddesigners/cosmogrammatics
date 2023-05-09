<?php

return function ($site) {

  $query = get('q');
  $results = page('articles')->children()->listed()->search($query);
  // $site->index()
  return [
    'query' => $query,
    'articles' => $results,
  ];

};
