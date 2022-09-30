<?php

class CommentsPage extends Kirby\Cms\Page {

    // convert $page->children() to read from SQLite db
    public function children() {

        $comments = [];

        foreach (Db::select('comment') as $comment) {

            $comments[] = [
                'slug' => $comment->slug(),
                'num' => $comment->status() === 'listed' ? 0 : null,
                'template' => 'comment',
                'model' => 'comment',
                'content' => [
                    'title' => $comment->title() ?? 'New Comment',
                    'text' => $comment->text(),
                    'user' => $comment->user(),
                    'status' => is_null($comment->status()) ? 'draft' : $comment->status()
                ]

            ];
        }

        return Pages::factory($comments, $this);
    }
    
}
