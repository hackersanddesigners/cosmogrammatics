<?= $rule->selector() ?> {
  <?php foreach ( $rule->declarations()->toStructure() as $declaration ) { ?>
    <?= $declaration->property() ?> : <?= $declaration->value() ?> ;
  <?php } ?>
}
