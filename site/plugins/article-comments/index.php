<?php

Kirby::plugin('cosmo-api/article-comments', [
    'routes' => function ($kirby) {
        return [
            [
                'pattern' => 'cosmo-api/(:any)',
                'action' => function ($alphanum) use ($kirby) {

                    $article_path = 'articles/' . $alphanum . '/comments';
                    $comments = $kirby->page($article_path)->children()->listed();

                    $text_highlights = [];
                    foreach($comments as $comment) {
                        // double check we're not returnign any comment
                        // w/o block_id / bid, for backward support
                        if ($comment->block_id()->isNotEmpty()) {

                            $selection_text = $comment->selection_text()->toObject();
                            $endMeta = $selection_text->endMeta()->toObject();
                            $startMeta = $selection_text->startMeta()->toObject();

                            $highlight = [
                                'content' => [
                                    'user' => $comment->user()->value(),
                                    'timestamp' => $comment->timestamp()->value(),
                                    'article_slug' => $comment->article_slug()->value(),
                                    'block_id' => $comment->block_id()->value(),
                                    'text' => $comment->text()->value(),
                                    'selection_type' => $comment->selection_type()->value(),
                                    'selection_text' => [
                                        'startMeta' => [
                                            'parentIndex' => $startMeta->parentIndex()->value(),
                                            'parentTagName' => $startMeta->parentTagName()->value(),
                                            'textOffset' => $startMeta->textOffset()->value(),
                                        ],
                                        'endMeta' => [
                                            'parentIndex' => $endMeta->parentIndex()->value(),
                                            'parentTagName' => $endMeta->parentTagName()->value(),
                                            'textOffset' => $endMeta->textOffset()->value(),
                                        ],
                                        'text' => $selection_text->text()->value(),
                                        'id' => $selection_text->id()->value(),
                                        '__isHighlightSource' => $selection_text->__isHighlightSource()->value(),
                                    ],
                                ],
                                'id' => $comment->id(),
                                'slug' => $comment->slug(),
                                'title' => $comment->title()->value(),
                                'status' => 'published'
                            ];

                            array_push($text_highlights, $highlight);
                        };

                    } // bid-check

                    return json_encode($text_highlights);
                }
            ]
        ];
    }
]);
