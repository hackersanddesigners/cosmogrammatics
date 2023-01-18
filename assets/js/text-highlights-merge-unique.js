const LocalStore = require('./local.store')

function textHighlightMergeUnique(newData, article_slug) {
  // merge this array of text-highlights
  // w/ current localStorage['combo-highlight']
  const comment_store = new LocalStore(`comment-${article_slug}`);
  let comments = comment_store.getAll();

  // <https://stackoverflow.com/a/54134237>
  const ids = new Set(comments.map(d => d.id));
  const highlightsMerged = [...comments, ...newData].filter(d => !ids.has(d.ID));

  // the save function checks if the item
  // exists already, so we just pass
  // the entire dataset
  comment_store.save(highlightsMerged)

  console.log('after-save =>', highlightsMerged, comment_store.getAll())

}

module.exports = textHighlightMergeUnique
