<article>
  <form
    action="/<?= $page ?>"
    method="post"
    name="comment_form"
    class="comment_form"
    data-block-id="<?= isset( $block ) ? $block->id() : NULL ?>"
    data-article-slug="<?= $page->slug() ?>"
    data-csrf="<?= csrf() ?>"
    data-selection-type="<?= $selection_type ?? NULL ?>"
    data-selection-text="<?= $selection_text ?? NULL ?>"
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
    <p>
      <small><em>Press ENTER to post.</em></small>
    </p>
    <input
      type="submit"
      value="post"
    />
  </form>
</article>

<!-- action="/api/pages/articles+<?= $page->slug() ?>+comments/children" -->
<!-- action="<?= "/" . $page . "/comments" ?>" -->
<!-- action="/api/pages/comments/children" -->
