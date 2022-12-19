const LocalStore = require('./local.store')

function textHighlightMergeUnique(newData) {
  // merge this array of text-highlights
  // w/ current localStorage['combo-highlight']
  const store = new LocalStore();
  let localStore = store.getAll();

  // <https://stackoverflow.com/a/54134237>
  const ids = new Set(localStore.map(d => d.id));
  const highlightsMerged = [...localStore, ...newData.filter(d => !ids.has(d.ID))];

  // the save function checksif the item
  // exists already, so we just pass
  // the entire dataset
  store.save(highlightsMerged)

}

module.exports = textHighlightMergeUnique
