<?php

return function ($page) {

    $comments = $page->find('comments');

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
