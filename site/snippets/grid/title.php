<div class="row">
  <div class="column">
    <div class="block" tabindex="0">
      <div class="contents">
          <div class="stats">
            <div class="options">
              <label for="view_mode">
                view mode:
              </label>
              <select
                name="view_mode"
                id="view_mode"
              >
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3" selected>3</option>
                <option value="4">4</option>
                <option value="5">5</option>
              </select>
            </div>
            <p><span id="comment_count"><?= $comments->count() ?></span> comments</p>
          </div>
        <h1><?= $page->title()->html() ?></h1>
      </div>
    </div>
  </div>
</div>
