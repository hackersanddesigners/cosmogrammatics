<?php if ($comments->isNotEmpty()): ?>
<section class="row comment-review">
  <button class="comment-toggle">Show Comments</button>

  <form class="comment-list hidden">
    <fieldset>
      <h3>Your unpublished comments</h3>
      <div class="comment-data"></div>
      <input type="submit" value="Publish">
    </fieldset>
  </form>
</section>
<?php endif ?>
