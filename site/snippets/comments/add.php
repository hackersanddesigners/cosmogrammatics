<form
  action="/<?= $page ?>"
  method="post"
  name="comment_form"
  class="comment_form"
  data-block-id="<?= isset( $block ) ? $block->bid() : NULL ?>"
  data-article-slug="<?= $page->slug() ?>"
  data-csrf="<?= csrf() ?>"
  data-selection-type="<?= $selection_type ?? NULL ?>"
  data-selection-coords="<?= $selection_coords ?? NULL ?>"
>

  <input
    type="text"
    placeholder="comment..."
    name="body"
    required
  />

  <input
    type="text"
    placeholder="author"
    name="author"
    required
  />

  <input
    name="post_comment"
    type="submit"
    value="post"
  />

  <label for="post_comment">
    <p class="small_italic">Press ENTER to post.</p>
  </label>

</form>
