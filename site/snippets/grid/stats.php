<div class="stats">
  <?php snippet( 'comments/count', [ 'comments' => $comments ]  ) ?>
  <div class="view-options">
    <fieldsets>
      <legend>view</legend>
      <div>
        <input type="radio" id="1" name="view" value="1">
        <label for="1">only article</label>
      </div>
      <div>
        <input type="radio" id="2" name="view" value="2" checked>
        <label for="2">article + comments</label>
      </div>
      <div>
        <input type="radio" id="3" name="view" value="3">
        <label for="3">comments + article</label>
      </div>
      <div>
        <input type="radio" id="4" name="view" value="4">
        <label for="4">only comments</label>
      </div>
    </fieldsets>
  </div>
</div>
