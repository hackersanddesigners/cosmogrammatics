<?php if ($comments->isNotEmpty()): ?>
<section class="row comment-review">
  <button class="comment-toggle">Show Comments</button>

  <form action="<?= $page->url() ?>" method="POST" class="comment-list hidden">
    <fieldset>
      <h3>Your unpublished comments</h3>
      <p class="comment-status">You have x unpublished comments.</p>
      <div class="comment-data"></div>
      <input name="post_comment" type="submit" value="Publish">
    </fieldset>

    <div class="honey">
      <label for="website">If you are a human, leave this field empty</label>
      <input type="website" name="website" id="website" value="<?= isset($data['website']) ? esc($data['website']) : null ?>"/>
    </div>
  </form>
</section>
<?php endif ?>
