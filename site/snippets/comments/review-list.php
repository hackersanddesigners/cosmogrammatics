<section class="row comment-review">
  <div class="column" data-col-span="12" data-col-width="1/4">
    <form action="<?= $page->url() ?>" method="POST" class="comment-list">
      <fieldset>
        <h3>Your unpublished comments:</h3>
        <div class="comment-header">
          <p class="comment-status">You have x unpublished comments.</p>
          <div class="comment-username-wrapper">
            <label for="comment-username">Username:</label>
            <input type="text" id="comment-username" name="comment-username" value="" readonly="readonly">
            <button type="button" class="comment-username-edit-btn">edit</button>
          </div>
        </div>

        <div class="comment-data"></div>
        <input name="post_comment" class="button post_comment" type="submit" value="publish">
        <button type="button" class="button comment-edit">edit</button>
        <button type="button" class="button comment-remove">remove</button>
      </fieldset>

      <div class="honey">
        <label for="website">If you are a human, leave this field empty</label>
        <input type="website" name="website" id="website" value="<?= isset($data['website']) ? esc($data['website']) : null ?>"/>
      </div>
    </form>
  </div>
</section>
