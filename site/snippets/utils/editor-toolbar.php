<form
  id="editor_toolbar"
  class="hidden"
  name="editor_toolbar"
  action="/<?= $page ?>"
  method="post"
  data-article-slug="<?= $page->slug() ?>"
  data-csrf="<?= csrf() ?>"
>
  <h3>Currently editing <span class="own_comment_count">0</span> comments</h3>
  <input
    disabled
    name="post_comments"
    type="submit"
    value="publish comments"
  />
</form>
