<?php

return function ($page) {

    $comments = $page->find('comments');

    return [
        'comments' => $comments,
    ];

};
