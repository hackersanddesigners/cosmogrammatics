<section class="row comment-review">
  <div class="column" data-col-span="12" data-col-width="1/4">
    <form action="<?= $page->url() ?>" method="POST" class="comment-list">
      <fieldset>
        <div class="comment-header">
          <div class="comment-username-wrapper">
            <label for="comment-username">Username:</label>
            <input type="text" id="comment-username" name="comment-username" value="" readonly="readonly">
            <button type="button" class="button comment-username-edit-btn">edit</button>
          </div>
        </div>

        <h3 class="comment-status">No unpublished comments</h3>
        <div class="comment-data"></div>
        <p class="comment-status"><small><i>Once published, comments can not be edited.</i></small></p>
        <input name="post_comment" class="hidden button post_comment" type="submit" value="publish">
        <button type="button" class="hidden button comment-edit">edit</button>
        <button type="button" class="hidden button comment-remove">remove</button>
      </fieldset>

      <div class="honey">
        <label for="website">If you are a human, leave this field empty</label>
        <input type="website" name="website" id="website" value="<?= isset($data['website']) ? esc($data['website']) : null ?>"/>
      </div>
    </form>
  </div>
</section>
