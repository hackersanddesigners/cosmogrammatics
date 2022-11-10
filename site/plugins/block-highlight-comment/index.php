<?php

use Kirby\Cms\Page;
use Kirby\Cms\Collection;
use Kirby\Exception\PermissionException;


function wrapSelectedText ($text_in, $offset) {
    // regex match text inside HTML (w/o DOM tags?)
    // using given offset from text-selection
    // and wrap this text selection around an extra pair of tags
    // (for now <span>{}</span>)
    // => check if wrap operation is done already

    $left_side = str_slice($text_in, 0, $offset['x1']);
    $center_side = str_slice($text_in, $offset['x1'], $offset['y1']);
    $right_side = str_slice($text_in, $offset['y1']);

    // var_dump([$text_in, $left_side, $center_side, $right_side]);
 
    $wrap_before = '<span class="comment-highlight">';
    $wrap_after = '</span>';

    // var_dump(Str::startsWith($left_side, $wrap_before));
    // var_dump(Str::startsWith($right_side, $wrap_before));

    // // check if wrapping is done already
    // if (Str::startsWith($left_side, $wrap_before)
    //     OR Str::startsWith($right_side, $wrap_after)) {
    //         return $text_in;
    // };
    // $text_out = $text_in;

    $text_out = $left_side . $wrap_before . $center_side . $wrap_after . $right_side;

    return $text_out;
}

// <https://stackoverflow.com/a/53985015>
function str_slice() {
    $args = func_get_args();

    switch (count($args)) {
    case 1:
        return $args[0];
    case 2:
        $str        = $args[0];
        $str_length = strlen($str);
        $start      = $args[1];
        if ($start < 0) {
            if ($start >= - $str_length) {
                $start = $str_length - abs($start);
            } else {
                $start = 0;
            }
        }
        else if ($start >= $str_length) {
            $start = $str_length;
        }
        $length = $str_length - $start;
        return substr($str, $start, $length);
    case 3:
        $str        = $args[0];
        $str_length = strlen($str);
        $start      = $args[1];
        $end        = $args[2];
        if ($start >= $str_length) {
            return "";
        }
        if ($start < 0) {
            if ($start < - $str_length) {
                $start = 0;
            } else {
                $start = $str_length - abs($start);
            }
        }
        if ($end <= $start) {
            return "";
        }
        if ($end > $str_length) {
            $end = $str_length;
        }
        $length = $end - $start;
        return substr($str, $start, $length);
    }
    return null;
};


function parseBlockSelection($blocks, $block_id, $comments, $offset, $type) {

    $updatedBlocks = [];

    foreach($blocks as $block) {

        $blockType = $block->type();

        var_dump([$block->id(), $block_id]);

        if ($blockType === 'columns') {

            // we have one layout per block, no need to loop over
            $layout = $block->layout()->toLayouts()->first();

            $columnsNew = [];
            foreach($layout->columns() as $column) {
                // we need to:
                // - parse the layout blocks
                // - reconstruct the layout with updated blocks
                // - convert it back to a layout object

                $subblocks = $column->blocks();
                $updatedSubblocks = parseBlockSelection($subblocks, $block_id, $comments, $offset, 'layout');
                $subblocksNew = new Kirby\Cms\Blocks($updatedSubblocks);

                $columnNew = new Kirby\Cms\LayoutColumn(
                    [
                        'blocks' => $subblocksNew->toArray(),
                        'width' => $column->width(),
                    ]
                );

                array_push($columnsNew, $columnNew);
            };

            $layoutColumnsNew = new Kirby\Cms\LayoutColumns($columnsNew);
            
            $layoutNew = Kirby\Cms\Layout::factory([
                'columns' => $layoutColumnsNew->toArray(),
            ]);

            $layoutsNew = new Kirby\Cms\Layouts([$layoutNew]);

            // -- update block
            $blockLayoutUpdated = [
                'content' => [
                    'layout' => $layoutsNew->toArray(),
                ],
                'type' => 'columns',
            ];

            $blockLayoutNew = new Kirby\Cms\Block($blockLayoutUpdated);
            array_push($updatedBlocks, $blockLayoutNew);

        } else if ($blockType === 'text' && $block->id() === $block_id) {

            $text_in = $block->text()->value();
            $text_new = wrapSelectedText($text_in, $offset);

            // -- update block
            $blockUpdated = [
                'content' => [
                    'text' => $text_new,
                    'footnotes' => $block->footnotes()->toArray(),
                ],
                'type' => $block->type(),
            ];

            $blockUpdated = new Kirby\Cms\Block($blockUpdated);
            array_push($updatedBlocks, $blockUpdated);

        } else {
            array_push($updatedBlocks, $block);
        }

    }; // -- end blocks foreach


    return $updatedBlocks;

}


// parse block-text and wrap comments pointing to a specific text offset
// inside a span tag in order to manipulate it visually with CSS in the frontend
// similarly to the `cosmo/footnote-ref` plugin, we need to map through
// block-text as well as block->columns->block-text
Kirby::plugin('cosmo/block-highlight-comment', [
    'hooks' => [
        'route:after' => function ($route, $path, $method) {

            $kirby = kirby();
            $request = $kirby->request();

            // url => api/pages/articles+tottoaa+comments/children
            if (Str::startsWith($path, 'api/pages/articles+')
                && Str::endsWith($path, '+comments/children')
                && $method == "POST") {

                // <https://getkirby.com/docs/cookbook/forms/user-registration>

                // check if CSRF token is valid
                $csrf = $request->csrf();
                if (csrf($csrf) === true) {

                    $body = $request->body();

                    // set a new empty collection
                    // and then update the var with any comments
                    // part of the current article
                    $comments = new Collection();
                    $page = page($body->get('fullpath'));

                    // filter article comments by block_id if any
                    // else skip
                    if ($page->hasChildren()) {
                        $block_id = $body->get('content')['block_id'];

                        if ($block_id != '') {
                            $comments = $page->children()->drafts()->filterBy('block_id', $block_id);

                            $offset = $body->get('content')['selection_text'];

                            $blocks = $page->builder()->toBlocks();
                            $updatedBlocks = parseBlockSelection($blocks, $block_id, $comments, $offset, 'block');
                            $blocksNew = new Kirby\Cms\Blocks($updatedBlocks);

                            // // -- write to file
                            // kirby()->impersonate('kirby');
                            // $page->update([
                            //     'builder' => json_encode($blocksNew->toArray()),
                            // ]);

                        }
                    }

                } // -- end 
            }
        }
    ]
]);
