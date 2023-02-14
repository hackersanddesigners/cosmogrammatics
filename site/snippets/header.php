<header>
  <a
    class="skip-to-main collapsible"
    href="#main"
  >
    skip to main content
  </a>
  <nav tabindex="0" class="collapsible">
    <ul>
      <?php foreach($site->pages()->listed() as $nav_item): ?>
      <li>
          <a
            href="<?= $nav_item->url() ?>"
            title="<?= $nav_item->url() ?>"
          >
            <?= $nav_item->title() ?>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  </nav>
  <div class="title">
    <a href="<?= $site->url() ?>">
      <?= $site->title()->html() ?>
    </a>
  </div>
  <?php snippet( 'search' ) ?>
</header>
