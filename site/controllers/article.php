<?php

return function ($page) {

    // <article>/comments/<[comment-1, comment-2, ...]>
    $comments = $page->children()->children()->listed();

    $skin = [
        'colors' => $page->colors()->toEntity(),
        'fonts'  => $page->fonts()->toEntity(),
        'css'    => $page->css()->toStructure()
    ];

    return [
        'comments' => $comments,
        'skin'     => $skin
    ];

};
