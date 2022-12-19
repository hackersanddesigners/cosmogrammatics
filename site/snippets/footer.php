<footer>
  <?php
  // <https://getkirby.com/docs/guide/api/authentication#session-based-authentication>
  // <https://getkirby.com/docs/reference/objects/cms/user/login>
  // grab user with role: API,
  // log them in to Kirby and start an new User session
  // do any js fetch call with CSRF tokens

  $user = $kirby->users()->filterBy('role', '==', 'api')->first();

  if($user and $user->login(Config::get('api_pass'))) {
    // user is logged-in and a new user session started
    // the script below will work correctly by sending an CSRF token
  }
  ?>

  <?php if ($page->intendedTemplate() == 'article') {
    echo js([ 'assets/js/templates/article.min.js' ], [ 'type'  => 'module', 'defer' => true ]);
  } ?>

  <p class="copyright"><?= $site->copyright()->html() ?></p>
</footer>
