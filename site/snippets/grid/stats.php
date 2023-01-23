<div class="stats">
  <p><span id="comment_count"><?= $comments->count() ?></span> comments</p>
  <div class="view-options">
    <legend>view</legend>
    <div>
      <input type="radio" id="1" name="view" checked>
      <label for="1">only article</label>
    </div>
    <div>
      <input type="radio" id="2" name="view">
      <label for="2">article + comments</label>
    </div>
    <div>
      <input type="radio" id="3" name="view">
      <label for="3">comments + article</label>
    </div>
    <div>
      <input type="radio" id="4" name="view">
      <label for="4">only comments</label>
    </div>
  </div>
</div>
