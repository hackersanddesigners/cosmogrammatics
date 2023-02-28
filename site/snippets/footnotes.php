<section class="notes column" data-col-span="12" data-col-width="1/1">
<?php if($footnotes): ?>
   <ol>
   <?php
       foreach($footnotes as $footnote):
         // footnotes are normal ones added inline in the block
         // or the citation author from a block-quote
         if ($footnote->type() == 'quote') {
           $note = $footnote->citation();
           $ref = $footnote->id();
         } else {
           $note = $footnote->note();
           $ref = $footnote->ref();
         }
    ?>
      <li id="note-ref-<?= $ref ?>">
          <?= $note ?>
      </li>
    <?php endforeach ?>
   </ol>
<?php endif ?>
</section>
