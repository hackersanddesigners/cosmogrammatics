<?php $ref = $footnote->ref() ?>
  <li id="note-ref-<?= $ref ?>">
    <a
      href="#ft-<?= $ref ?>"
      class="note-ref-backlink"
    >↩</a>
    <?= $footnote->note() ?>
  </li>
