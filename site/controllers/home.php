<?php

return function ($page) {

    $articles = page('articles')->children()->listed();

    return [
        'articles' => $articles,
    ];

};
