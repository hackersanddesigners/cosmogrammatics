<header>
  <a
    class="skip-to-main collapsible"
    href="#main"
  >
    skip to main content
  </a>
  <nav tabindex="0" class="collapsible">
    <ul>
      <?php foreach ( [
           (object)[
          'name' => 'About',
          'href' => '/about',
        ], (object)[
          'name' => 'Get Involved',
          'href' => '/contribute',
        ], (object)[
          'name' => 'Contact',
          'href' => '/contact',
        ], (object)[
          'name' => 'Imprint',
          'href' => '/imprint',
        ],
      ] as $nav_item ): ?>
        <li>
          <a
            href="<?= $nav_item->href ?>"
            title="<?= $nav_item->href ?>"
          >
            <?= $nav_item->name ?>
          </a>
        </li>
      <?php endforeach ?>
    </ul>
  </nav>
  <?php snippet( 'search' ) ?>
  <span class="title">
    <a href="<?= $site->url() ?>">
      <?= $site->title()->html() ?>
    </a>
  </span>
</header>
