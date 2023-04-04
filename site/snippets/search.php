<form
  name="search"
  method="get"
  action="/search?q="
  class="collapsible"
>
  <input
    type="search"
    aria-label="Search"
    id="search"
    name="q"
    placeholder="Search cosmograms..."
    value="<?= html($query) ?>"
  />
  <input
    type="submit"
    value="search"
  >
</form>
