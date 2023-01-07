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
                        $selection_text = $comment->selection_text()->toObject();
                        $endMeta = $selection_text->endMeta()->toObject();
                        $startMeta = $selection_text->startMeta()->toObject();

                        $highlight = [
                            '__isHighlightSource' => $selection_text->__isHighlightSource()->value(),
                            'endMeta' => [
                                'parentIndex' => $endMeta->parentIndex()->value(),
                                'parentTagName' => $endMeta->parentTagName()->value(),
                                'textOffset' => $endMeta->textOffset()->value(),
                            ],
                            'id' => $selection_text->id()->value(),
                            'startMeta' => [
                                'parentIndex' => $startMeta->parentIndex()->value(),
                                'parentTagName' => $startMeta->parentTagName()->value(),
                                'textOffset' => $startMeta->textOffset()->value(),
                            ],
                            'text' => $selection_text->text()->value()
                        ];

                        array_push($text_highlights, $highlight);
                    };

                    return json_encode($text_highlights);
                }
            ]
        ];
    }
]);
