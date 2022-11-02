<?php $footnotes_all = []; ?>

<?php foreach ($column->blocks() as $block): ?>
<div id="<?= $block->id() ?>" data-type="block-<?= $block->type() ?>">
  <?php if ($block->type() === 'text'): ?>
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
