<?php if ($alert): ?>
<section class="row">
  <section class="column alert">
    <ul>
      <?php foreach ($alert as $message): ?>
      <li><?= kirbytext($message) ?></li>
      <?php endforeach ?>
    </ul>
  </section>
</section>
<?php endif ?>
