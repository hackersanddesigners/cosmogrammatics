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

        <p class=""><small><i>Dear visitor, you are invited to comment on this article. You can comment by selecting text or you can select entire blocks by clicking on them. Before you comment please check our community guidelines.</i></small></p>
        <h3 class="comment-status"></h3>
        <div class="comment-data"></div>
        <p class="comment-status"><small><i>Comments can not be edited once they are published.</i></small></p>
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
