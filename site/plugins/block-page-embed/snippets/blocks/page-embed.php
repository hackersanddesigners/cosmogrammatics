<?php
    $link = $block->pageurl()->toLinkObject(); 
    if ($pageEmbed = page($link->value())): ?>
<div class="page-embed">
    <a href="<?= $pageEmbed->url() ?>"><?= $pageEmbed->title()->html() ?></a>
</div>
<?php endif ?>
