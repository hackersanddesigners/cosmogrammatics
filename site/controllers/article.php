<?php

return function ($page) {

    // <article>/comments/<[comment-1, comment-2, ...]>
    $comments = $page->children()->children()->listed();

    // footnotes
    $footnotes_list = [];

    $blocks = $page->builder()->toBlocks();
    foreach($blocks as $block) {
        if ($block->type() === 'columns') {

            // collect eventual block text footnotes
            if ($layout = $block->layout()->toLayouts()->first()) {
                if ($columns = $layout->columns()) {
                    foreach($layout->columns() as $column) {
                        $subblocks = $column->blocks();
                        foreach($subblocks as $subblock) {

                            if ($subblock->type() === 'text') {
                                $subfootnotes = $subblock->footnotes()->toStructure();

                                foreach($subfootnotes as $subfootnote) {
                                    array_push($footnotes_list, $subfootnote);
                                };
                            }
                        };
                    };
                };
            }

        } else if ($block->type() === 'text') {
            $footnotes = $block->footnotes()->toStructure();

            foreach($footnotes as $footnote) {
                array_push($footnotes_list, $footnote);
            };
        }
    }

    $skin = [
      'colors' => $page->colors()->toEntity(),
      'fonts'  => $page->fonts()->toEntity(),
      'rules'  => $page->css()->toStructure()
    ];

    return [
      'comments'  => $comments,
      'footnotes' => $footnotes_list,
      'skin'      => $skin
    ];

};
