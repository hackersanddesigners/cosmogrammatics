<?php
/** @var \Kirby\Cms\Block $block */
$caption = $block->caption();
$crop    = $block->crop()->isTrue();
$ratio   = $block->ratio()->or('auto');
?>
<figure<?= Html::attr([ 'data-ratio' => $ratio, 'data-crop' => $crop ], null, ' ' ) ?> >
  <ol>
    <?php foreach ($block->images()->toFiles() as $image): ?>
    <li>
      <a href="<?= $image->url() ?>">
        <?= $image ?>
      </a>
    </li>
    <?php endforeach ?>
    </ol>
  <?php if ($caption->isNotEmpty()): ?>
  <figcaption>
    <?= $caption ?>
  </figcaption>
  <?php endif ?>
  <div class="controls">
    <span class="left"><</span>
    <span class="right">></span>
  </div>
</figure>
