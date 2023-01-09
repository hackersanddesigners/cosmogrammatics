<?php

return function ($kirby, $page) {

    // -- preparing data for template

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


    // -- handling POST request from comment action


    // check if request is POST and input type submit has been clicked
    if ($kirby->request()->is('POST') && get('post_comment')) {

        // check if the honeypot trap has been triggered
        if (empty(get('website')) === false) {
            go($page->url());
            exit;
        }

        // get the data and check if it's all good
        $ts = date("c");
        $selection_text = yaml::encode(json_decode(get('selection_text'), true));

        $data = [
            'user' => get('author'),
            'timestamp' => $ts,
            'article_slug' => get('article_slug'),
            'block_id' => get('block_id'),
            'text' => get('body'),
            'selection_type' => get('selection_type'),
            'selection_text' => $selection_text,
        ];

        $rules = [
            'user' => ['required'],
            'text' => ['required'],
        ];

        $messages = [
            'user' => 'Please set a name',
            'text' => 'Please write a comment',
        ];

        // if data has some problems stop everything
        // and refresh page
        if ($invalid = invalid($data, $rules, $messages)) {
            go($page->url());
            exit;

        } else {
            // else proceed with requested operation:
            // - act as the API user
            // - create subpage for comment

            $user = $kirby->users()->filterBy('role', '==', 'api')->first();
            $kirby->impersonate($user->email());

            try {

                // make subpage under <current-article>/comments>
                $comments_subpage = $page->find('comments');
                
                $comment = $comments_subpage->createChild([
                    'slug'     => 'test-' . $ts,
                    'template' => 'comment',
                    'content'  => $data
                ]);

                // done
                // maybe show an <article>/comment-added subpage
                // explaining what happened?
                go($page->url());

            } catch (Exception $e) {
                $alert = ['Adding your comments failed: ' . $e->getMessage()];
            }
        }

    }


    // -- return data

    return [
      'comments'  => $comments,
      'footnotes' => $footnotes_list,
      'skin'      => $skin
    ];

};
