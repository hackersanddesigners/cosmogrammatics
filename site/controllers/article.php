<?php

return function ($page) {

    // <article>/comments/<[comment-1, comment-2, ...]>
    $comments = $page->children()->children()->listed();

    // footnotes
    $footnotes_all = [];

    $blocks = $page->builder()->toBlocks();
    foreach($blocks as $block) {
        if ($block->type() === 'columns') {

            // collect eventual block text footnotes
            $layout = $block->layout()->toLayouts()->first();
            foreach($layout->columns() as $column) {
                $subblocks = $column->blocks();
                foreach($subblocks as $subblock) {

                    if ($subblock->type() === 'text') {
                        $subfootnotes = $subblock->footnotes()->toStructure();

                        foreach($subfootnotes as $subfootnote) {
                            array_push($footnotes_all, $subfootnote);
                        };
                    }
                };
            };

        } else if ($block->type() === 'text') {
            $footnotes = $block->footnotes()->toStructure();

            foreach($footnotes as $footnote) {
                array_push($footnotes_all, $footnote);
            };
        }
    }

    

    $skin = [
        'colors' => $page->colors()->toEntity(),
        'fonts'  => $page->fonts()->toEntity(),
        'css'    => $page->css()->toStructure()
    ];

    return [
        'comments' => $comments,
        'footnotes' => $footnotes,
        'skin'     => $skin
    ];

};
