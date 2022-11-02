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
  <span
    tabindex="0"
    class="title"
  >
    <a href="<?= $site->url() ?>">
      <?= $site->title()->html() ?>
    </a>
  </span>
</header>

<!--
<script type="module">

import { $$ } from '/assets/js/utils/index.js'
const header_children = Array.from( $$( 'header .collapsible' ) )
const EXPANDED_CLASS = 'expanded'

header_children.map( header_child => {

  header_child.onclick = select_child

  function select_child( e ) {
    console.log( e.target )
    header_children.map( c => collapse( c ) )
    expand( e.target )
  }

  function expand( el ) {
    el.classList.add( EXPANDED_CLASS )
  }
  function collapse( el ) {
    el.classList.remove( EXPANDED_CLASS )
  }

})

</script> -->
