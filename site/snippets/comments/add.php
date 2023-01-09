<form
  action="<?= $page->url() ?>"
  method="POST"
  name="comment_form"
  class="comment_form"
  data-block-id="<?= isset( $block ) ? $block->bid() : NULL ?>"
  data-article-slug="<?= $page->slug() ?>"
  data-block-selection-type="<?= $selection_type ?? NULL ?>"
  data-block-selection-text-id=""
>
  <input type="text" placeholder="comment..." id="body" name="body" required>
  <input type="text" placeholder="author" id="author" name="author" required>

  <input type="hidden" id="article_slug" name="article_slug" value="<?= $page->slug() ?>">
  <input type="hidden" id="block_id" name="block_id" value="<?= isset( $block ) ? $block->bid() : NULL ?>">
  <input type="hidden" id="selection_type" name="selection_type" value="<?= $selection_type ?? NULL ?>">
  <input type="hidden" id="selection_text" name="selection_text" value="">

  <input name="post_comment" type="submit" value="post">

  <label for="post_comment">
    <p class="small_italic">Press ENTER to post.</p>
  </label>

  <div class="honey">
    <label for="website">If you are a human, leave this field empty</label>
    <input type="website" name="website" id="website" value="<?= isset($data['website']) ? esc($data['website']) : null ?>"/>
  </div>
</form>
