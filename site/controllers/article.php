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

        // we POST a list of fields all named the same
        // `comment_data[]`, the `[]` put the fields inside
        // a common array.
        // let's loop over the array and process each field,
        // which is JSON stringify value

        $body = get('comment_data');
        file_put_contents('log.txt', json_encode($body));
        
        foreach($body as $field) {

            // get the data and check if it's all good

            $field = json_decode($field, true);
            $selection_text = yaml::encode($field['content']['selection_text'], true);

            $content = $field['content'];
            $data = [
                'user' => $content['user'],
                'timestamp' => $content['timestamp'],
                'article_slug' => $content['article_slug'],
                'block_id' => $content['block_id'],
                'text' => $content['text'],
                'selection_type' => $content['selection_type'],
                'selection_text' => $selection_text
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
                        'slug'     => $content['timestamp'],
                        'template' => $field['template'],
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

        } // -- ends data loop

    }


    // -- return data

    return [
      'comments'  => $comments,
      'footnotes' => $footnotes_list,
      'skin'      => $skin
    ];

};
