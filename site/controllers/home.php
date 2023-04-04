<?php

return function ($page) {

  $articles = page('articles')->children()->listed();

  // search query
  $query = get('q');

  return [
    'articles' => $articles,
    'query' => $query,
  ];

};
