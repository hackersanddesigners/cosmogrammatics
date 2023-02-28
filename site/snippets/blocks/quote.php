<?php /** @var \Kirby\Cms\Block $block */ ?>
<blockquote>
  <?= $block->text() ?>
  <?php
     if ($block->citation()->isNotEmpty()): ?>
     <footer>
       <a id="ft-<?= $block->id() ?>" href="#note-ref-<?= $block->id() ?>" class="ref-ft"><?= $block->citation() ?></a>
     </footer>
     <?php endif ?>
</blockquote>
