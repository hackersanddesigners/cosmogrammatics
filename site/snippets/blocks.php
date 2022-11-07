<?php $footnotes_all = []; ?>

<?php foreach ($column->blocks() as $block): ?>
<div id="<?= $block->id() ?>" data-type="block-<?= $block->type() ?>">
  <?php
      // check if block-type is text or columns
      // if columns, loop over one more layer to
      // the blocks and check if there's text blocks
      // collect all footnotes to $footnotes_all.

       if ($block->type() === 'columns') {

           // render block
           echo $block;

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

       } else if ($block->type() === 'text'): ?>
       <?= $block->text() ?>
       <?php
         
         $footnotes = $block->footnotes()->toStructure();
         foreach($footnotes as $footnote) {
             array_push($footnotes_all, $footnote);
         };
       ?>
   <?php else: ?>
       <?= $block ?>
   <?php endif ?>
</div>
<?php endforeach ?>

<div class="notes">
<?php if($footnotes_all): ?>
   <ol>
   <?php
       foreach($footnotes_all as $footnote):
       $ref = $block->id() . '-' . $footnote->ref();
       ?>
       <li id="note-ref-<?= $ref ?>"><?= $footnote->note() ?><a href="#ft-<?= $ref ?>">↩</a></li>
   <?php endforeach ?>
   </ol>
<?php endif ?>
