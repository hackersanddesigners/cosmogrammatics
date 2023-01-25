const LocalStore = require('./local.store')

async function fetchArticleHighlightsFromAPI(article_slug) {
  const url = `/cosmo-api/${ article_slug }`

  return fetch(url)
    .then(response => response.json())
    .then(response => {
      const newData = response
      textHighlightMergeUnique(newData, article_slug)

    })
  .catch(error => {
    return []
  });

}

function textHighlightMergeUnique(newData, article_slug) {
  // merge this array of text-highlights (newData)
  // w/ current localStorage['comment-{article-slug}']

  // when removing comment from backend only, we need force
  // remove it from frontend as well: how to?

  const comment_store = new LocalStore(`comment-${article_slug}`);
  let comments = comment_store.getAll();

  const highlightsMerged = [...comments, ...newData]
  const highlights = highlightsMerged.filter((value, index, self) => {
    return index === self.findIndex(t => {
      return t.id === value.id
    })
  })

  // the save function checks if the item
  // exists already, so we just pass
  // the entire dataset
  comment_store.save(highlights)
}

module.exports = fetchArticleHighlightsFromAPI
