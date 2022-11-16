<section class="notes column" data-col-span="12" data-col-width="1/1">
<?php if($footnotes): ?>
   <ol>
   <?php
       foreach($footnotes as $footnote):
       $ref = $footnote->ref();
    ?>
        <li id="note-ref-<?= $ref ?>">
            <a href="#ft-<?= $ref ?>" class="note-ref-backlink">↩</a>
            <?= $footnote->note() ?>
        </li>
    <?php endforeach ?>
   </ol>
<?php endif ?>
</section>
