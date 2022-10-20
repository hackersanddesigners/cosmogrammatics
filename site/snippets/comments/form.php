<form
  action="<?= "/" . $page . "/comments" ?>"
  method="post"
  name="comment_form"
  class="post_comment"
  onsubmit="post_comment( event )"
>

  <input
    type="text"
    placeholder="reply..."
    name="body"
  />

  <input
    type="text"
    placeholder="author"
    name="author"
  />

  <label for="comment_input">
  </label>
  <p>
    Press ENTER to post.
  </p>
  <input
    type="submit"
    value="post"
  />
</form>
