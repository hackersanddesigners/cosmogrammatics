<article>
  <form
    action=""
    method="post"
    name="comment_form"
    class="comment_form"
    data-auth-user="<?= $kirby->option('env')['api_user'] ?>"
    data-auth-pass="<?= $kirby->option('env')['api_pass'] ?>"
    data-block-id="<?= $block->id() ?>"
    data-article-slug="<?= $page ?>"
  >

    <input
      type="text"
      placeholder="reply..."
      name="body"
    />

    <input
      type="text"
      placeholder="author"
      name="author"
    />

    <label for="comment_input">
    </label>
    <!-- <p>
      <small><em>Press ENTER to post.</em></small>
    </p> -->
    <input
      type="submit"
      value="post"
    />
  </form>
</article>

<!-- action="<?= "/" . $page . "/comments" ?>" -->
<!-- action="/api/pages/comments/children" -->
