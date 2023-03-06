const LocalStore = require('./local.store')

async function highlightsSync(article_slug) {
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
  // filter out comments w/o block_id for backward support

  // the problem is that upon publishing an article
  // we're removing it from the store, but it's not really
  // being removed (?);
  // so when merging the two stores (backend and frontend)
  // we're still adding the published articles that we should
  // have removed. two ways:
  // - fix the remove from store bug
  // - filter out local store to keep only draft comments

  const comment_store = new LocalStore(`comment-${article_slug}`);
  let comments = comment_store.getAll();
  const drafts = comments.filter(comment => comment.status === 'draft')

  const highlightsMerged = [...drafts, ...newData]
  const highlights = highlightsMerged.filter((value, index, self) => {
    return index === self.findIndex(t => {
      return t.id === value.id && t.content.block_id !== null
    })
  })

  // to make sure we just set only the
  // localStore draft comments together with
  // the published comments from the backend:
  // we remove and save the comment_store
  // each time.
  // this mostly solves the problem
  // of removing a published comment from the
  // backend without using other ways to
  // pass that reference to the frontend and
  // remove it from the localStore.
  // brutal, but overall fine !:
  comment_store.removeAll()
  comment_store.save(highlights)
}

module.exports = highlightsSync
